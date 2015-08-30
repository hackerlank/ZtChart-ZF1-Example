<?php

/**
 * 平台数据实时监控系统
 *
 * @category Recruit
 * @package ZtChart_Plugin_Acl
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Acl.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局控制器动作插件——权限控制
 *
 * @final
 * @name ZtChart_Plugin_Acl
 * @see Zend_Controller_Plugin_Abstract
 */
final class ZtChart_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    /**
     * 
     * @var Zend_Acl
     */
    protected $_acl;
    
    /**
     * 
     */
    public function __construct() {
        $this->_acl = new Zend_Acl();
    }
    
    /**
     * 在路由结束之后，载入权限分配表。
     * 
     * @see Zend_Controller_Request_Abstract::routeShutdown()
     * @param Zend_Controller_Plugin_Abstract $request
     * @return void
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request) {
        $user = Zend_Registry::get('user');
        
        $role = ZtChart_Model_Acl_Loader::hash($user->getRoleId());
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($role);
        
        ZtChart_Model_Acl_Loader::getInstance()->load($this->_acl);
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($this->_acl);
        
        // 如果拥有全部游戏，则设置为NULL。
        if (($gameTypes = $user->getRole()->getGameTypes(true)) 
                                    == array_keys(ZtChart_Model_GameType::getGames())) {
            $gameTypes = null;
        }
        ZtChart_Model_Assemble_Backend_Abstract::setAllowedGameTypes($gameTypes);
    }
    
    /**
     * 在消息派发之前，验证权限。
     * 
     * @see Zend_Controller_Request_Abstract::preDispatch()
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if ($this->_request->getActionName() != Zend_Controller_Front::getInstance()->getDefaultAction()
            && $this->_acl->has($this->_resource())
            && !$this->_acl->isAllowed($this->_role(), $this->_resource(), $this->_privileges())) {
            
            // 如果没有权限则跳转到相关的提示页面
            $request->setModuleName('index')->setControllerName('index')->setActionName('deny');
        }
    }
    
    /**
     * 是否有权限
     * 
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return boolean
     */
    public function isAllowed($action, $controller, $module, $params = array()) {
        $resource = ZtChart_Model_Acl_Resource::parsePageMvc($action, $controller, $module);
        if (!$this->_acl->has($resource)) {
            return true;
        } else {
            return $this->_acl->isAllowed($this->_role(), $resource, $this->_privileges());
        }
    }
    
    /**
     * 获取当前角色
     * 
     * @return string
     */
    protected function _role() {
        try {
            $roleId = Zend_Registry::get('user')->getRoleId();
        } catch (ZtChart_Model_User_Exception $e) {
            $roleId = ZtChart_Model_Role::GUEST;
        }
        
        return ZtChart_Model_Acl_Loader::hash($roleId);
    }
    
    /**
     * 获取当前资源
     * 
     * @return string
     */
    protected function _resource() {
        return ZtChart_Model_Acl_Resource::parseHttpRequest($this->_request);
    }
    
    /**
     * 获取当前特权(privileges)
     * 
     * @return string
     */
    protected function _privileges() {
        return null;
    }
}