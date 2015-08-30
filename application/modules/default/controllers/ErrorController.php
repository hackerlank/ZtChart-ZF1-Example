<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: ErrorController.php 37415 2012-11-15 08:14:57Z zhangweiwen $
 */

/**
 * 错误处理控制器
 *
 * @name ErrorController
 * @see Zend_Controller_Action
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * 禁止布局
     */
    public function init()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
    }
    
    /**
     * 清空其他动作控制器输出的内容
     * 
     * @see Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        $this->_response->clearBody();
    }

    /**
     * 错误显示
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if (null != ($log = $this->getLog())) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }
        
        // Db exception, if db profiler is set
        if ($errors->exception instanceof Zend_Db_Exception) {
            foreach (ZtChart_Model_Db_Table_Abstract::getInstanceTableAdapters() as $adapter) {
                $profiler = $adapter->getProfiler();
                for ($queryId = 0; $queryId < $profiler->getTotalNumQueries(); $queryId++) {
                    if (!$profiler->getQueryProfile($queryId)->hasEnded()) {
                        $this->view->profile = $profiler->getQueryProfile($queryId);
                        break;
                    }
                }
            }
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

