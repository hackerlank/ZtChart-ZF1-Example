<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Acl
 * @subpackage ZtChart_Model_Acl_Assert
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: GameType.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 权限分配规则——可访问的游戏类型
 *
 * @name ZtChart_Model_Acl_Assert_GameType
 */
class ZtChart_Model_Acl_Assert_GameType implements Zend_Acl_Assert_Interface {
    
    /**
     * 
     * @var Zend_Controller_Request_Http
     */
    protected $_request;
    
    /**
     * 构造函数，初始化请求参数。
     * 
     * @param ZtChart_Model_Acl_Loader $aclLoader
     * @param Zend_Controller_Request_Http $request
     */
    public function __construct(Zend_Controller_Request_Http $request = null) {
        if (null === $request) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
        }
        $this->_request = $request;
    }
    
    /**
     * 判断是否有访问某个游戏的条件
     * 
     * @see Zend_Acl_Assert_Interface::assert()
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null, 
                                Zend_Acl_Resource_Interface $resource = null, $privilege = null) {
        if ($this->_request->has('gametype')) {
            $roleData = ZtChart_Model_Acl_Loader::getInstance()->getRole($role->getRoleId());
            
            return in_array($this->_request->getParam('gametype'), $roleData['role_gametype']);
        }
        
        return true;
    }
}