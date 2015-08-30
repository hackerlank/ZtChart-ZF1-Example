<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Controller
 * @subpackage ZtChart_Model_Controller_Action_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Chart.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 动作助手——图表页面自动加载
 *
 * @name ZtChart_Model_Controller_Action_Helper_Chart
 * @see Zend_Controller_Action_Helper_Abstract
 */
class ZtChart_Model_Controller_Action_Helper_Chart extends Zend_Controller_Action_Helper_Abstract {

    /**
     * 
     * @param Zend_Controller_Action $actionController
     */
    public function __construct(Zend_Controller_Action $actionController = null) {
        if (null !== $actionController) {
            $this->setActionController($actionController);
        }
    }
    
    /**
     * 设置图表的布局页面和视图页面
     * 
     * @see Zend_Controller_Action_Helper_Abstract::preDispatch()
     */
    public function postDispatch() {
        $request = $this->getRequest();
        if ($request->has('chart') && !$this->_inErrorHandler()) {
            $action = $request->getActionName();
            try {
                $this->_actionController->render($action . 'Chart' . ucfirst($request->getParam('chart')));
                Zend_Layout::getMvcInstance()->setLayout('chart');
                ZtChart_Plugin_Layout::resetModuleLayout();
            } catch (Zend_View_Exception $e) {
                $this->_actionController->render($action);
            }
            
            // 是否需要以JSONP格式返回数据
            if ($request->has('callback')) {
                $response = $this->getResponse();
                $jsonp = $request->getParam('callback') . '(' . $response->getBody() . ')';
                $response->setBody($jsonp);
            }
        }
    }
    
    /**
     * 是否进入了错误处理模块
     * 
     * @return boolean
     */
    protected function _inErrorHandler() {
        $errorHandler = $this->getFrontController()->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        
        return $this->getRequest()->getModuleName() == $errorHandler->getErrorHandlerModule()
                && $this->getRequest()->getControllerName() == $errorHandler->getErrorHandlerController()
                && $this->getRequest()->getActionName() == $errorHandler->getErrorHandlerAction();
    }
}