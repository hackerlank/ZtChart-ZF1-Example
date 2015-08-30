<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: NoticeController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统管理模块的公告控制器
 *
 * @name System_NoticeController
 * @see ZtChart_Model_Controller_Action
 */
class System_NoticeController extends ZtChart_Model_Controller_Action {
    
    /**
     * 
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $inboxContext = $this->getHelper('inboxContext');
        $inboxContext->addActionContext('new', 'inbox')
                     ->addActionContext('edit', 'inbox')
                     ->addActionContext('remove', 'inbox')
                     ->initContext('inbox');
                                    
    }
    
    /**
     * 首页
     */
    public function indexAction()
    {
        $action = Zend_Registry::get('user')->getRole()
                                ->getActionResource('system', 'notice') ?: 'list';
        $this->_forward($action);
    }
    
    /**
     * 公告列表
     */
    public function listAction()
    {
        $notice = new ZtChart_System_Model_Notice();
        $this->view->paginator = $notice->getPaginator($this->_request->getQuery('page'));
    }
    
    /**
     * 新增公告
     */
    public function newAction()
    {
        if ($this->_request->isPost()) {
            $notice = new ZtChart_System_Model_Notice();
            if (!$notice->insert($this->_request->getPost())) {
                $this->_helper->dialog('添加公告失败', Zend_Log::WARN);
            } else {
                $this->_helper->dialog('添加公告成功', Zend_Log::INFO, 
                                    array('parent' => $this->_helper->url('index')));
            }
        }
    }
    
    /**
     * 编辑公告
     */
    public function editAction()
    {
        $notice = new ZtChart_System_Model_Notice();
        if ($this->_request->isPost()) {
            if (!$notice->update($this->_request->getPost(), $this->_getParam('notice_id'))) {
                $this->_helper->dialog('修改公告失败', Zend_Log::WARN);
            } else {
                $this->_helper->dialog('修改公告成功', Zend_Log::INFO, 
                                    array('parent' => $this->_helper->url('index')));
            }
        }
        $this->view->notice = $notice->fetchRow($this->_getParam('notice_id'));
    }
    
    /**
     * 删除公告
     */
    public function removeAction()
    {
        if ($this->_request->isPost()) {
            $notice = new ZtChart_System_Model_Notice();
            if (!$notice->delete($this->_getParam('notice_id'))) {
                $this->_helper->dialog('删除公告失败', Zend_Log::WARN);
            } else {
                $this->_helper->dialog('删除公告成功', Zend_Log::INFO, 
                                    array('parent' => $this->_helper->url('index')));
            }
        }
    }
}