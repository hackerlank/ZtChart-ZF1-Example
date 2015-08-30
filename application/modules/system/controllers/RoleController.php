<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: RoleController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统管理模块的角色控制器
 *
 * @name System_RoleController
 * @see ZtChart_Model_Controller_Action
 */
class System_RoleController extends ZtChart_Model_Controller_Action {

    /**
     * 检查参数合法性
     */
    public function preDispatch()
    {
        if ($this->_hasParam('role_id') && !in_array($this->_getParam('role_id', 0), 
                        Zend_Registry::get('user')->getRole()->getChildRolesId())) {
            return $this->render('deny');
        }
    }
    
    /**
     * 首页
     */
    public function indexAction()
    {
        $action = Zend_Registry::get('user')->getRole()
                                ->getActionResource('system', 'role') ?: 'list';
        $this->_forward($action);
    }
    
    /**
     * 角色列表
     */
    public function listAction()
    {
        $roleAccess = new ZtChart_System_Model_Role();
        $this->view->paginator = $roleAccess->getPaginator(
                                    Zend_Registry::get('user')->getRole(), $this->_request->getQuery('page'));
    }
    
    /**
     * 新增角色
     */
    public function newAction()
    {
        $gameTypes = Zend_Registry::get('user')->getRole()->getGameTypes();
        if ($this->_request->isPost()) {
            $roleData = $this->_request->getPost();
            $roleData['role_parent'] = Zend_Registry::get('user')->getRoleId();
            if (!isset($roleData['role_gametype'])) {
                $roleData['role_gametype'] = array();
            }
            $roleData['role_gametype'] = array_intersect($roleData['role_gametype'], array_keys($gameTypes));
                
            $roleAccess = new ZtChart_System_Model_Role();
            if (!$roleAccess->insert($roleData)) {
                $this->_helper->dialog('添加角色失败', Zend_Log::WARN);
            } else {
                ZtChart_Model_Acl_Loader::resetCache();
                $this->_helper->dialog('添加角色成功', Zend_Log::INFO, $this->_helper->url('index'));
            }
        }
        $this->view->gameTypes = $gameTypes;
    }
    
    /**
     * 修改角色
     */
    public function editAction()
    {
        if (! $this->_hasParam('role_id')) {
            return $this->render('deny');
        }
        
        $roleId = $this->_getParam('role_id');
        $gameTypes = Zend_Registry::get('user')->getRole()->getGameTypes();
        $roleAccess = new ZtChart_System_Model_Role();
        if ($this->_request->isPost()) {
            $roleData = $this->_request->getPost();
            $roleData['role_gametype'] = array_intersect($roleData['role_gametype'], array_keys($gameTypes));
            if (!$roleAccess->update($roleData, $roleId)) {
                $this->_helper->dialog('修改角色失败', Zend_Log::WARN);
            } else {
                ZtChart_Model_Acl_Loader::resetCache();
                $this->_helper->dialog('修改角色成功', Zend_Log::INFO, $this->_helper->url('index'));
            }
        }
        $this->view->gameTypes = $gameTypes;
        $this->view->role = $roleAccess->fetchRow($roleId);
    }
    
    /**
     * 删除角色
     */
    public function removeAction()
    {
        if (! $this->_hasParam('role_id')) {
            return $this->render('deny');
        }
        
        $roleId = $this->_getParam('role_id');
        $roleAccess = new ZtChart_System_Model_Role();
        if ($this->_request->isPost()) {
            if ($roleAccess->hasUser($roleId)) {
                $this->_helper->dialog('有用户属于该角色，请先解除用户与角色关系后再删除。', Zend_Log::WARN);
            } else {
                $roleId = $roleAccess->findSelfAndDescendants($roleId);
                if ($roleAccess->delete($roleId)) {
                    ZtChart_Model_Acl_Loader::resetCache();
                    $this->_helper->dialog('删除角色成功', Zend_Log::INFO, $this->_helper->url('index'));
                }
            }
        } else {
            $this->view->role = $roleAccess->fetchRow($roleId);
        }
    }
    
    /**
     * 设置权限
     */
    public function assignAction()
    {
        if (! $this->_hasParam('role_id')) {
            return $this->render('deny');
        }
        
        $roleId = $this->_getParam('role_id');
        $resourceAccess = new ZtChart_System_Model_Resource();
        $allowedResourceId = $resourceAccess->findRoleSelfAndDescendants(Zend_Registry::get('user')->getRoleId());
        
        $roleAccess = new ZtChart_System_Model_Role();
        if ($this->_request->isPost()) {
            if ($roleAccess->saveAcl($this->_request->getPost('resource_id'), 
                                        $allowedResourceId, $roleId)) {
                ZtChart_Model_Acl_Loader::resetCache();
                $this->_helper->dialog('设置权限成功', Zend_Log::INFO);
            } else {
                $this->_helper->dialog('设置权限失败', Zend_Log::WARN);
            }
        }
        $this->view->resourceId = $roleAccess->fetchResourceId($roleId);
        $this->view->allowedResourceId = $allowedResourceId;
    }
}