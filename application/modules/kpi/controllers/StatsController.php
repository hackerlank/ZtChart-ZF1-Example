<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: StatsController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 游戏KPI模块的统计概况控制器
 *
 * @name Kpi_StatsController
 * @see Zend_Controller_Action
 */
class Kpi_StatsController extends Zend_Controller_Action {

    /**
     * 
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
                                ->getActionResource('kpi', 'stats') ?: 'summary';
        $this->_forward($action);
    }
    
    /**
     * 统计概况
     */
    public function summaryAction()
    {
        
    }
    
    /**
     * 登陆概况
     */
    public function accessAction()
    {
        if ($this->_hasParam('chart')) {
            $assembleIp = new ZtChart_Model_Assemble('Flserver_Ip', 'RGraph');
            $assembleAccount = new ZtChart_Model_Assemble('Flserver_Account', 'RGraph');
            if ($this->_hasParam('gametype')) {
                $assembleIp->setGameTypes($this->_getParam('gametype'));
                $assembleAccount->setGameTypes($this->_getParam('gametype'));
            }
            $chartIp = $assembleIp->findPredefinedAssembleDataWithClientip(
                                    $this->_request->getCookie('stats_access',
                                                ZtChart_Model_Assemble_Datetime::RECENT_24HOUR));
            $chartAccount = $assembleAccount->findPredefinedAssembleDataWithAccount(
                                    $this->_request->getCookie('stats_access',
                                                ZtChart_Model_Assemble_Datetime::RECENT_24HOUR));
            $this->view->assign('chartIp', $chartIp['chart']);
            $this->view->assign('chartAccount', $chartAccount['chart']);
        } else {
            $assembleIp = new ZtChart_Model_Assemble('Flserver_Ip');
            $assembleAccount = new ZtChart_Model_Assemble('Flserver_Account');
            if ($this->_hasParam('gametype')) {
                $assembleIp->setGameTypes($this->_getParam('gametype'));
                $assembleAccount->setGameTypes($this->_getParam('gametype'));
            }
            $chartIpEntire = $assembleIp->findPredefinedAssembleDataWithClientip(
                                                ZtChart_Model_Assemble_Datetime::ENTIRE_DAY);
            $chartIpToday = $assembleIp->findPredefinedAssembleDataWithClientip(
                                                ZtChart_Model_Assemble_Datetime::TODAY);
            $chartAccountEntire = $assembleAccount->findPredefinedAssembleDataWithAccount(
                                                ZtChart_Model_Assemble_Datetime::ENTIRE_DAY);
            $chartAccountToday = $assembleAccount->findPredefinedAssembleDataWithAccount(
                                                ZtChart_Model_Assemble_Datetime::TODAY);
            $this->view->assign('chartIpEntire', $chartIpEntire['chart']);
            $this->view->assign('chartIpToday', $chartIpToday['chart']);
            $this->view->assign('chartAccountEntire', $chartAccountEntire['chart']);
            $this->view->assign('chartAccountToday', $chartAccountToday['chart']);
        }
    }
    
    /**
     * 在线概况
     */
    public function onlineAction()
    {
        
    }
    
    /**
     * 充值概况
     */
    public function paymentAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account', 'RGraph');
            if ($this->_hasParam('gametype')) {
                $assemble->setGameTypes($this->_getParam('gametype'));
            }
            $chart = $assemble->findPredefinedAssembleDataWithPayment(
                                $this->_request->getCookie('stats_payment', 
                                                    ZtChart_Model_Assemble_Datetime::THIS_MONTH));
            $chartBank = $assemble->findPredefinedAssembleDataWithPaymentAndNetbank(
                                $this->_request->getCookie('stats_payment', 
                                                    ZtChart_Model_Assemble_Datetime::THIS_MONTH));
            $this->view->assign('chart', $chart['chart']);
            $this->view->assign('chartBank', $chartBank['chart']);
        } else {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account');
            if ($this->_hasParam('gametype')) {
                $assemble->setGameTypes($this->_getParam('gametype'));
            }
            $chartEntire = $assemble->findPredefinedAssembleDataWithPayment(
                                                    ZtChart_Model_Assemble_Datetime::ENTIRE_DAY);
            $chartEntireBank = $assemble->findPredefinedAssembleDataWithPaymentAndNetbank(
                                                    ZtChart_Model_Assemble_Datetime::ENTIRE_DAY);
            $chartToday = $assemble->findPredefinedAssembleDataWithPayment(
                                                    ZtChart_Model_Assemble_Datetime::TODAY);
            $chartTodayBank = $assemble->findPredefinedAssembleDataWithPaymentAndNetbank(
                                                    ZtChart_Model_Assemble_Datetime::TODAY);
            $this->view->assign('chartEntire', $chartEntire['chart']);
            $this->view->assign('chartEntireBank', $chartEntireBank['chart']);
            $this->view->assign('chartToday', $chartToday['chart']);
            $this->view->assign('chartTodayBank', $chartTodayBank['chart']);
        }
    }
    
    /**
     * 消耗概况
     */
    public function consumeAction()
    {
        if ($this->_hasParam('chart')) {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account', 'RGraph');
            if ($this->_hasParam('gametype')) {
                $assemble->setGameTypes($this->_getParam('gametype'));
            }
            $chart = $assemble->findPredefinedAssembleDataWithConsumeAndApa(
                                    $this->_request->getCookie('stats_consume', 
                                                ZtChart_Model_Assemble_Datetime::RECENT_24HOUR));
            $chartBank = $assemble->findPredefinedAssembleDataWithConsumeAndNetbank(
                                    $this->_request->getCookie('stats_consume', 
                                                ZtChart_Model_Assemble_Datetime::RECENT_24HOUR));
            $this->view->assign('chart', $chart['chart']);
            $this->view->assign('chartBank', $chartBank['chart']);
        } else {
            $assemble = new ZtChart_Model_Assemble('Usertrade_Account');
            if ($this->_hasParam('gametype')) {
                $assemble->setGameTypes($this->_getParam('gametype'));
            }
            $chartEntire = $assemble->findPredefinedAssembleDataWithConsumeAndApa(
                                                ZtChart_Model_Assemble_Datetime::ENTIRE_DAY);
            $chartToday = $assemble->findPredefinedAssembleDataWithConsumeAndApa(
                                                ZtChart_Model_Assemble_Datetime::TODAY);
            $chartEntireBank = $assemble->findPredefinedAssembleDataWithConsumeAndNetbank(
                                                ZtChart_Model_Assemble_Datetime::ENTIRE_DAY);
            $chartTodayBank = $assemble->findPredefinedAssembleDataWithConsumeAndNetbank(
                                                ZtChart_Model_Assemble_Datetime::TODAY);
            $this->view->assign('chartEntire', $chartEntire['chart']);
            $this->view->assign('chartToday', $chartToday['chart']);
            $this->view->assign('chartEntireBank', $chartEntireBank['chart']);
            $this->view->assign('chartTodayBank', $chartTodayBank['chart']);
        }
    }
    
    /**
     * 今日地区分布
     */
    public function areaAction()
    {
        if ($this->_hasParam('chart')) {
            if ('pieAccess' == $this->_getParam('chart')) {
                $assemble = new ZtChart_Model_Assemble('Flserver_Area', 'RGraph');
                if ($this->_hasParam('gametype')) {
                    $assemble->setGameTypes($this->_getParam('gametype'));
                }
                $assemble->setFrontendFormat('area');
                $this->view->assign($assemble->getPredefinedAssembleData(
                                                ZtChart_Model_Assemble_Datetime::TODAY));
            }
            
            if ('pieConsume' == $this->_getParam('chart')) {
                $assemble = new ZtChart_Model_Assemble('Usertrade_Area', 'RGraph');
                if ($this->_hasParam('gametype')) {
                    $assemble->setGameTypes($this->_getParam('gametype'));
                }
                $assemble->setFrontendFormat('area');
                $this->view->assign($assemble->findPredefinedAssembleDataWithConsume(
                                                ZtChart_Model_Assemble_Datetime::TODAY));
            }
        }
    }
}
