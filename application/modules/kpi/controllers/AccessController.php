<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: AccessController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 游戏KPI模块的登陆分析控制器
 *
 * @name Kpi_AccessController
 * @see ZtChart_Model_Controller_Action
 */
class Kpi_AccessController extends ZtChart_Model_Controller_Action {

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
                                ->getActionResource('kpi', 'access') ?: 'detail';
        $this->_goto($action);
    }
    
    /**
     * 登陆详情
     */
    public function detailAction()
    {
        if ($this->_hasParam('chart')) {
            $assembleIp = new ZtChart_Model_Assemble('Flserver_Ip', 'RGraph');
            $assembleAccount = new ZtChart_Model_Assemble('Flserver_Account', 'RGraph');
        } else {
            $assembleIp = new ZtChart_Model_Assemble('Flserver_Ip');
            $assembleAccount = new ZtChart_Model_Assemble('Flserver_Account');
        }
        if ($this->_hasParam('gametype')) {
            $assembleIp->setGameTypes($this->_getParam('gametype'));
            $assembleAccount->setGameTypes($this->_getParam('gametype'));
        }
        if (0 != ($selectDatetime = $this->_getParam('select_datetime', 
                                        ZtChart_Model_Assemble_Datetime::RECENT_24HOUR))) {
            $chartIp = $assembleIp->findPredefinedAssembleDataWithClientip($selectDatetime);
            $chartAccount = $assembleAccount->findPredefinedAssembleDataWithAccount($selectDatetime);
        } else {
            $chartIp = $assembleIp->findRangeAssembleDataWithClientip(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
            $chartAccount = $assembleAccount->findRangeAssembleDataWithAccount(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
        }
        $this->view->assign($chartIp);
        $this->view->assign('chartAccount', $chartAccount['chart']);
    }
    
    /**
     * 时段分布
     */
    public function periodAction()
    {
        if ($this->_hasParam('chart')) {
            $assembleIp = new ZtChart_Model_Assemble('Flserver_Ip', 'RGraph');
            $assembleAccount = new ZtChart_Model_Assemble('Flserver_Account', 'RGraph');
        } else {
            $assembleIp = new ZtChart_Model_Assemble('Flserver_Ip');
            $assembleAccount = new ZtChart_Model_Assemble('Flserver_Account');
        }
        if ($this->_hasParam('gametype')) {
            $assembleIp->setGameTypes($this->_getParam('gametype'));
            $assembleAccount->setGameTypes($this->_getParam('gametype'));
        }
        if (0 != ($selectDatetime = $this->_getParam('select_datetime', ZtChart_Model_Assemble_Datetime::TODAY))) {
            $chartIp = $assembleIp->findPredefinedAssembleDataWithClientip($selectDatetime);
            $chartAccount = $assembleAccount->findPredefinedAssembleDataWithAccount($selectDatetime);
        } else {
            $chartIp = $assembleIp->findRangeAssembleDataWithClientip(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
            $chartAccount = $assembleAccount->findRangeAssembleDataWithAccount(
                    $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
        }
        $this->view->assign($chartIp);
        $this->view->assign('chartAccount', $chartAccount['chart']);
    }
    
    /**
     * 地区分布
     */
    public function areaAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Flserver_Area', 'RGraph');
        } else {
            $assemble = new ZtChart_Model_Assemble('Flserver_Area');
        }
        if ($this->_hasParam('gametype')) {
            $assemble->setGameTypes($this->_getParam('gametype'));
        }
        $assemble->setFrontendFormat('area');
        if (0 != ($selectDatetime = $this->_getParam('select_datetime', ZtChart_Model_Assemble_Datetime::TODAY))) {
            $chart = $assemble->getPredefinedAssembleData($selectDatetime);
        } else {
            $chart = $assemble->getRangeAssembleData(
                        $this->_getParam('start'), $this->_getParam('end'), Zend_Date::DAY);
        }
        
        $this->view->assign($chart);
    }
}