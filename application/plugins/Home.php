<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Plugin_Auth
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Home.php 36809 2012-09-07 08:00:39Z zhangweiwen $
 */

/**
 * 全局控制器动作插件——用户主页跳转
 *
 * @final
 * @name ZtChart_Plugin_Home
 * @see Zend_Controller_Plugin_Abstract
 */
final class ZtChart_Plugin_Home extends Zend_Controller_Plugin_Abstract {
    
    /**
     * 根据用户被分配的访问权限，选择允许访问的第一个地址。
     * 
     * @see Zend_Controller_Plugin_Abstract::dispatchLoopShutdown()
     */
    public function dispatchLoopShutdown() {
        $url = '/';
        if (false !== ($user = $this->_checkIdentity(Zend_Auth::getInstance()->getIdentity()->user_name))) {
            if (null != ($module = $user->getRole()->getModuleResource())) {
                $url = $module;
            } 
        } 
        Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrl($url);
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
}