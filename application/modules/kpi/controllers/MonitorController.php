<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: MonitorController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 游戏KPI模块的实时监控控制器
 *
 * @name Kpi_MonitorController
 * @see Zend_Controller_Action
 */
class Kpi_MonitorController extends Zend_Controller_Action {

    /**
     * 
     * 
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        // 必须在先于上下文助手执行
        $this->_helper->addHelper(new ZtChart_Model_Controller_Action_Helper_Chart($this));
        
        $context = $this->_helper->getHelper('AjaxContext');
        $context = $this->_helper->getHelper('ContextSwitch');
        $context->addActionContext('access', 'json')
                ->addActionContext('online', 'json')
                ->addActionContext('payment', 'json')
                ->addActionContext('consume', 'json')
                ->initContext();
    }
    
    /**
     * 预处理时间
     * 
     * @see Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        $timestamp = time() - 10;
        
        if (!$this->_hasParam('start')) {
            $this->_setParam('start', $timestamp - 60);
        }
        if (!$this->_hasParam('end')) {
            $this->_setParam('end', $timestamp);
        }
        
        if ($this->_request->isPost()) {
            
        }
    }
    
    /**
     * 首页
     */
    public function indexAction()
    {
        $action = Zend_Registry::get('user')->getRole()
                                ->getActionResource('kpi', 'monitor') ?: 'summary';
        $this->_forward($action);
    }
    
    /**
     * 实时概况
     */
    public function summaryAction()
    {
        
    }
    
    /**
     * 实时登陆
     */
    public function accessAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Flserver', 'RGraph');
            if ($this->_hasParam('gametype')) {
                $assemble->setGameTypes($this->_getParam('gametype'));
            }
            $this->view->assign($assemble->findRangeAssembleDataWithClientip($this->_getParam('start'),
                                                                $this->_getParam('end'), Zend_Date::SECOND));
        } else if ($this->_hasParam('control')) {
            $this->render('accessControl');
        }
    }
    
    /**
     * 实时在线
     */
    public function onlineAction()
    {
        if ($this->_hasParam('chart')) {
            
        } else if ($this->_hasParam('control')) {
            $this->render('onlineControl');
        }
    }
    
    /**
     * 实时充值
     */
    public function paymentAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Usertrade', 'RGraph');
            if ($this->_hasParam('gametype')) {
                $assemble->setGameTypes($this->_getParam('gametype'));
            }
            $payment = $assemble->findRangeAssembleDataWithPayment($this->_getParam('start'),
                                                                $this->_getParam('end'), Zend_Date::SECOND);
            $netbank = $assemble->findRangeAssembleDataWithPaymentAndNetbank($this->_getParam('start'),
                                                                $this->_getParam('end'), Zend_Date::SECOND);
            $this->view->assign(compact('payment', 'netbank'));
        } else if ($this->_hasParam('control')) {
            $this->render('paymentControl');
        }
    }
    
    /**
     * 实时消耗
     */
    public function consumeAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Usertrade', 'RGraph');
            if ($this->_hasParam('gametype')) {
                $assemble->setGameTypes($this->_getParam('gametype'));
            }
            $consume = $assemble->findRangeAssembleDataWithConsume($this->_getParam('start'),
                                                                $this->_getParam('end'), Zend_Date::SECOND);
            $netbank = $assemble->findRangeAssembleDataWithConsumeAndNetbank($this->_getParam('start'),
                                                                $this->_getParam('end'), Zend_Date::SECOND);
            $this->view->assign(compact('consume', 'netbank'));
        } else if ($this->_hasParam('control')) {
            $this->render('consumeControl');
        }
    }
}