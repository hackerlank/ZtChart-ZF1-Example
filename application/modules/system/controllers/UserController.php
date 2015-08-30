<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: UserController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统管理模块的用户控制器
 *
 * @name System_UserController
 * @see ZtChart_Model_Controller_Action
 */
class System_UserController extends ZtChart_Model_Controller_Action {
    
    /**
     *
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $inboxContext = $this->getHelper('inboxContext');
        $inboxContext->addActionContext('search', 'inbox')
                     ->initContext();
            
    }
    
    /**
     * 首页
     */
    public function indexAction()
    {
        $action = Zend_Registry::get('user')->getRole()
                                ->getActionResource('system', 'user') ?: 'list';
        $this->_forward($action);
    }
    
    /**
     * 用户列表
     */
    public function listAction()
    {
        $user = new ZtChart_System_Model_User();
        $this->view->paginator = $user->getPaginator($this->_request->getQuery('page'));
    }
    
    /*
     * 搜索用户
     */
    public function searchAction()
    {
        if ($this->_hasParam('q')) {
            try {
                $search = Zend_Json::decode(rawurldecode(base64_decode($this->_getParam('q'))));
            } catch (Zend_Json_Exception $e) {
                $this->_helper->dialog('参数错误', Zend_Log::ERR);
                return;
            }
            $user = new ZtChart_System_Model_User();
            $this->view->paginator = $user->getPaginator($this->_request->getQuery('page'), $search);
        }
        if ($this->_helper->inboxContext->getCurrentContext() != 'inbox') {
            $this->render('list');
        }
    }
    
    /**
     * 新增用户
     */
    public function newAction()
    {
        if ($this->_request->isPost()) {
            if (!Zend_Controller_Front::getInstance()->hasPlugin('ZtChart_Plugin_Auth')) {
                
                // 如果身份验证没有启用，则添加的用户都为管理员角色。
                $this->_request->setPost('user_roleid', ZtChart_Model_Role::ADMIN);
            }
            $authAdapter = new ZtChart_Model_Auth_Adapter_Soap();
            if (false === $authAdapter->searchAccount($this->_request->getPost('user_name'))) {
                $this->_helper->dialog('不是公司员工', Zend_Log::NOTICE);
                return;
            }
            
            $userAccess = new ZtChart_System_Model_User();
            if (!$userAccess->insert($this->_request->getPost())) {
                $this->_helper->dialog('添加用户失败', Zend_Log::WARN);
            } else {
                $this->_helper->dialog('添加用户成功', Zend_Log::INFO, $this->_helper->url('index'));
            }
        }
        if (Zend_Registry::isRegistered('user')) {
            $this->view->parentRoleId = Zend_Registry::get('user')->getRoleId();
        }
    }
    
    /**
     * 删除用户
     */
    /*
    public function removeAction()
    {
        if (! $this->_hasParam('user_id')) {
            return $this->render('deny');
        }
        $userId = $this->_getParam('user_id');
        $userAccess = new ZtChart_System_Model_User();
        if ($this->_request->isPost()) {
            if ($userAccess->delete($userId)) {
                $this->_helper->dialog('删除用户成功', Zend_Log::INFO, $this->_helper->url('list'));
            }
        } else {
            $this->view->user = $userAccess->fetchRow($userId);
        }
    }
    */
    
    /**
     * 启用用户
     */
    public function activeAction()
    {
        if (! $this->_hasParam('user_id')) {
            $this->_helper->json('非法访问');
        }
        
        $userId = $this->_getParam('user_id');
        if (Zend_Registry::get('user')->hasRoleUser($userId)) {
            if ($this->_request->isXmlHttpRequest() && $this->_request->isPost()) {
                $userAccess = new ZtChart_System_Model_User();
                if ($userAccess->toogleActive($userId)) {
                    $this->_helper->json(true);
                }
            }
        }
        $this->_helper->json('没有权限');
    }
    
    /**
     * 分配角色
     */
    public function assignAction()
    {
        if (! $this->_hasParam('user_id')) {
            return $this->render('deny');
        }
        $userId = $this->_getParam('user_id');
        if (Zend_Registry::get('user')->hasRoleUser($userId)) {
            if ($this->_request->isPost()) {
                if (Zend_Registry::get('user')->hasRole($this->_request->getPost('user_roleid'))) {
                    $userAccess = new ZtChart_System_Model_User();
                    if ($userAccess->update($this->_request->getPost(), $userId)) {
                        $this->_helper->dialog('分配角色成功', Zend_Log::INFO, $this->_helper->url('index'));
                    }
                }
            }
        } else {
            $this->view->noRoleUser = true;
        }
        $this->view->parentRoleId = Zend_Registry::get('user')->getRoleId();
        
        $objectUser = new ZtChart_Model_User($userId);
        $this->view->roleId = $objectUser->getRoleId();
    }
    
    /**
     * 搜索LDAP内的用户
     */
    public function ldapAction()
    {
        switch ($this->_getParam('type')) {
            case 'db':
                $user = new ZtChart_System_Model_User();
                $accountData =     $user->fetch();
                break;
            case 'soap':
            default:
                $authAdapter = new ZtChart_Model_Auth_Adapter_Soap();
                if ($this->_hasParam('user_name')) {
                    $accountData = $authAdapter->searchAccount($this->_getParam('user_name'));
                } else if ($this->_hasParam('display_name')) {
                    $accountData = $authAdapter->searchDisplayName($this->_getParam('display_name'));
                }
                $columns = array_fill_keys($this->_getParam('column'), null);
                $accountData = array_intersect_key((array) $accountData, $columns);
                break;
        }
        $this->_helper->json($accountData);
    }
}