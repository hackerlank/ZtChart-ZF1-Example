<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: ConsumeController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 游戏KPI模块的消耗分析控制器
 *
 * @name Kpi_ConsumeController
 * @see ZtChart_Model_Controller_Action
 */
class Kpi_ConsumeController extends ZtChart_Model_Controller_Action {

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
                                ->getActionResource('kpi', 'consume') ?: 'detail';
        $this->_goto($action);
    }
    
    /**
     * 消耗详情
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
                                        ZtChart_Model_Assemble_Datetime::RECENT_24HOUR))) {
            $chart = $assemble->findPredefinedAssembleDataWithConsumeAndApa($selectDatetime);
            $chartBank = $assemble->findPredefinedAssembleDataWithConsumeAndNetbank($selectDatetime);
        } else {
            $chart = $assemble->findRangeAssembleDataWithConsumeAndApa(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
            $chartBank = $assemble->findRangeAssembleDataWithConsumeAndNetbank(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
        }
        
        $this->view->assign($chart);
        $this->view->assign('chartBank', $chartBank['chart']);
    }
    
    /**
     * 时段分布
     */
    public function periodAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account', 'RGraph');
        } else {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account');
        }
        if ($this->_hasParam('gametype')) {
            $assemble->setGameTypes($this->_getParam('gametype'));
        }
        if (0 != ($selectDatetime = $this->_getParam('select_datetime', ZtChart_Model_Assemble_Datetime::TODAY))) {
            $chart = $assemble->findPredefinedAssembleDataWithConsumeAndApa($selectDatetime);
            $chartBank = $assemble->findPredefinedAssembleDataWithConsumeAndNetbank($selectDatetime);
        } else {
            $chart = $assemble->findRangeAssembleDataWithConsumeAndApa(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
            $chartBank = $assemble->findRangeAssembleDataWithConsumeAndNetbank(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
        }
        $this->view->assign($chart);
        $this->view->assign('chartBank', $chartBank['chart']);
    }
    
    /**
     * 地区分布
     */
    public function areaAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Area', 'RGraph');
        } else {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Area');
        }
        if ($this->_hasParam('gametype')) {
            $assemble->setGameTypes($this->_getParam('gametype'));
        }
        $assemble->setFrontendFormat('area');
        if (0 != ($selectDatetime = $this->_getParam('select_datetime', 
                                        ZtChart_Model_Assemble_Datetime::TODAY))) {
            $chart = $assemble->findPredefinedAssembleDataWithConsume($selectDatetime);
            $chartBank = $assemble->findPredefinedAssembleDataWithConsumeAndNetbank($selectDatetime);
        } else {
            $chart = $assemble->findRangeAssembleDataWithConsume(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
            $chartBank = $assemble->findRangeAssembleDataWithConsumeAndNetbank(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
        }
        $this->view->assign($chart);
        $this->view->assign('chartBank', $chartBank['chart']);
    }
}