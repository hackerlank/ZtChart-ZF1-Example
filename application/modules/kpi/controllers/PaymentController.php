<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: PaymentController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 游戏KPI模块的充值分析控制器
 *
 * @name Kpi_PaymentController
 * @see ZtChart_Model_Controller_Action
 */
class Kpi_PaymentController extends ZtChart_Model_Controller_Action {

    /**
     * 加载图表布局动作助手
     *
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_helper->addHelper(new ZtChart_Model_Controller_Action_Helper_Chart($this));
    }
    
    /**
     * 首页
     */
    public function indexAction()
    {
        $action = Zend_Registry::get('user')->getRole()
                                ->getActionResource('kpi', 'payment') ?: 'detail';
        $this->_goto($action);
    }
    
    /**
     * 充值详情
     */
    public function detailAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account', 'RGraph');
        } else {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account');
        }
        if ($this->_hasParam('gametype')) {
            $assemble->setGameTypes($this->_getParam('gametype'));
        }
        if (0 != ($selectDatetime = $this->_getParam('select_datetime', 
                                            ZtChart_Model_Assemble_Datetime::THIS_MONTH))) {
            $chart = $assemble->findPredefinedAssembleDataWithPayment($selectDatetime);
            $chartBank = $assemble->findPredefinedAssembleDataWithPaymentAndNetbank($selectDatetime);
        } else {
            $chart = $assemble->findRangeAssembleDataWithPayment(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
            $chartBank = $assemble->findRangeAssembleDataWithPaymentAndNetbank(
                            $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
        }

        $this->view->assign($chart);
        $this->view->assign('chartBank', $chartBank['chart']);
    }
    
    /**
     * 充值月趋势
     */
    public function tendencyAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account', 'RGraph');
        } else {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account');
        }
        if ($this->_hasParam('gametype')) {
            $assemble->setGameTypes($this->_getParam('gametype'));
        }
        if (0 != ($selectDatetime = $this->_getParam('select_datetime', 
                                            ZtChart_Model_Assemble_Datetime::THIS_YEAR))) {
            $chart = $assemble->findPredefinedAssembleDataWithPayment(
                    $selectDatetime, Zend_Date::MONTH);
            $chartBank = $assemble->findPredefinedAssembleDataWithPaymentAndNetbank(
                    $selectDatetime, Zend_Date::MONTH);
        } else {
            $chart = $assemble->findRangeAssembleDataWithPayment(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
            $chartBank = $assemble->findRangeAssembleDataWithPaymentAndNetbank(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
        }

        $this->view->assign($chart);
        $this->view->assign('chartBank', $chartBank['chart']);
    }
}