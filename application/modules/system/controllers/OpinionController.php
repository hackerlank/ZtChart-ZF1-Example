<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: OpinionController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统管理模块的意见控制器
 *
 * @name System_OpinionController
 * @see ZtChart_Model_Controller_Action
 */
class System_OpinionController extends ZtChart_Model_Controller_Action {
    
    /**
     *
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $inboxContext = $this->getHelper('inboxContext');
        $inboxContext->addActionContext('search', 'inbox')
                     ->addActionContext('reply', 'inbox')
                     ->initContext();
            
    }
    
    /**
     * 首页
     */
    public function indexAction()
    {
        $action = Zend_Registry::get('user')->getRole()
                                ->getActionResource('system', 'opinion') ?: 'list';
        $this->_forward($action);
    }
    
    /**
     * 意见列表
     */
    public function listAction()
    {
        $opinion = new ZtChart_System_Model_Opinion();
        $this->view->paginator = $opinion->getPaginator($this->_request->getQuery('page'));
    }
    
    /**
     * 答复意见
     */
    public function replyAction()
    {
        if ($this->_hasParam('opinion_id')) {
            $opinion = new ZtChart_System_Model_Opinion();
            if (null !== ($opinionRow = $opinion->fetchRow($this->_getParam('opinion_id')))) {
                if ($this->_hasParam('download')) {
                    if (file_exists($attachmentFile = Zend_Registry::get('attachmentPath') 
                                            . DIRECTORY_SEPARATOR . $opinionRow['opinion_attachment'])
                                    && is_readable($attachmentFile)) {
                        $this->_helper->sendFile($attachmentFile, substr(strstr($attachmentFile, '_'), 1));
                        
                        return;
                    }
                } else {
                    if ($this->_request->isPost()) {
                        if (!$opinion->update($this->_request->getPost(), $this->_getParam('opinion_id'))) {
                            $this->_helper->dialog('答复意见失败', Zend_Log::WARN);
                        } else {
                            $this->_helper->dialog('答复意见成功', Zend_Log::INFO,
                                    array('parent' => $this->_helper->url('index')));
                        }
                    }
                }
                $this->view->opinion = $opinionRow;
            }
        }
    }
    
    /**
     * 搜索意见
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
            $opinion = new ZtChart_System_Model_Opinion();
            $this->view->paginator = $opinion->getPaginator($this->_request->getQuery('page'), $search);
        }
        if ($this->_helper->inboxContext->getCurrentContext() != 'inbox') {
            $this->render('list');
        }
    }
}