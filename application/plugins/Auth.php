<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Plugin_Auth
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Auth.php 36810 2012-09-07 08:00:44Z zhangweiwen $
 */

/**
 * 全局控制器动作插件——用户登录验证
 *
 * @final
 * @name ZtChart_Plugin_Auth
 * @see Zend_Controller_Plugin_Abstract
 */
final class ZtChart_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
    
    /**
     * 允许匿名访问的Url地址
     * 
     * @staticvar array
     */
    static private $_anonymous = array();
    
    /**
     * Zend_Auth的实例
     * 
     * @var Zend_Auth
     */
    private $_auth = null;
    
    /**
     * 
     * @param array $anonymous
     */
    static public function addAnonymous($anonymous) {
        self::$_anonymous[] = $anonymous;
    }
    
    /**
     * 构造函数
     *
     */
    public function __construct() {
        $this->_auth = Zend_Auth::getInstance();
    }
    
    /**
     * 返回Zend_Auth实例
     * 
     * @return Zend_Auth
     */
    public function getAuth() {
        return $this->_auth;
    }
    
    /**
     * 判断是否登陆
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        if ($this->_auth->hasIdentity()) {
            if (false !== ($user = $this->_checkIdentity($this->_auth->getIdentity()->user_name))) {
                Zend_Registry::set('user', $user);
            } else {
                if (!$this->_isAllowedAnonymous($request)) {
                    $request->setModuleName('default')->setControllerName('index')->setActionName('forbidden');
                }
                $this->_auth->clearIdentity();
            }
        } else { 
            if (!$this->_isAllowedAnonymous($request)) {
                
                // 如果当前请求的Url地址不允许匿名访问，则跳转到登陆页面。
                $request->setModuleName('default')->setControllerName('login')->setActionName('index');
            }
        }
    }
    
    /**
     * 某些动作插件可能需要在登陆操作成功后，还未跳转之前进行一些数据处理方面的操作，
     * 因此在这里保存一下用户ID，供这些动作插件使用。
     * 
     * @see Zend_Controller_Plugin_Abstract::postDispatch()
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        if (Zend_Auth::getInstance()->hasIdentity() 
            && $this->_checkLoginUrl(null, $request->getControllerName(), $request->getModuleName())
            && false !== ($user = $this->_checkIdentity(Zend_Auth::getInstance()->getIdentity()->user_name))) {
            
            Zend_Registry::set('user', $user);
        } 
    }
    
    /**
     * 判断登陆的用户是否属于本系统
     * 
     * @param string $username
     * @return false|ZtChart_Model_User
     */
    protected function _checkIdentity($username) {
        try {
            $user = new ZtChart_Model_User($username);
            if (!$user->isActive()) {
                return false;
            }
        } catch (ZtChart_Model_User_Exception $e) {
            return false;
        }
        
        return $user;
    }
    
    /**
     * 是否允许匿名访问请求
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return boolean
     */
    protected function _isAllowedAnonymous(Zend_Controller_Request_Abstract $request) {
        return $this->_checkAnonymousUrl($request->getActionName(), 
                                $request->getControllerName(), $request->getModuleName());
    }
    
    /**
     * 判断当前请求的Url地址是否允许匿名访问
     * 
     * @param string $action
     * @param string $controller
     * @param string $module
     * @return boolean
     */
    protected function _checkAnonymousUrl($action, $controller, $module) {
        if ($this->_checkLoginUrl($action, $controller, $module)) {
            return true;
        }
        
        $requestUrl = array(
            $this->_request->getActionKey() => $action, 
            $this->_request->getControllerKey() => $controller, 
            $this->_request->getModuleKey() => $module
        );
        foreach (self::$_anonymous as $anonymousUrl) {
            if (!empty($anonymousUrl) && 0 == count(array_diff_assoc($anonymousUrl, $requestUrl))) {
                return true;
            }
        }
        
        return false;
    } 
    
    /**
     * 判断当前请求的Url地址是否为登陆控制器地址，如果传入的参数为null，则不检查该参数。
     * 
     * @param string $action
     * @param string $controller
     * @param string $module
     * @return boolean
     */
    protected function _checkLoginUrl($action = null, $controller = null, $module = null) {
        return (null === $action || 'soap' == $action) 
                && (null === $controller || 'login' == $controller)
                && (null === $module || 'default' == $module);
    }
}