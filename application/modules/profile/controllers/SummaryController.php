<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: SummaryController.php 35722 2012-06-27 04:04:25Z zhangweiwen $
 */

/**
 * 游戏总揽模块的总揽数据控制器
 *
 * @name Profile_SummaryController
 * @see ZtChart_Model_Controller_Action
 */
class Profile_SummaryController extends ZtChart_Model_Controller_Action {

    /**
     * 游戏总揽首页
     */
    public function indexAction()
    {
        $this->_forward('list');
    }
    
    /**
     *
     */
    public function listAction()
    {
    
    }
    
    /**
     * 游戏总览
     */
    public function mainAction()
    {
        $this->view->request = $this->_request;
        
        $assemble = new ZtChart_Model_Assemble('Usertrade_Account');
        $assemble->setFrontendFormat('sum');
        $this->view->todayPayment = $assemble->findPredefinedAssembleDataWithPayment(ZtChart_Model_Assemble_Datetime::TODAY);
        $this->view->todayPaymentBank = $assemble->findPredefinedAssembleDataWithPaymentAndNetbank(ZtChart_Model_Assemble_Datetime::TODAY);
        $this->view->todayConsume = $assemble->findPredefinedAssembleDataWithConsumeAndApa(ZtChart_Model_Assemble_Datetime::TODAY);
        $this->view->yestodayPayment = $assemble->findPredefinedAssembleDataWithPayment(ZtChart_Model_Assemble_Datetime::YESTODAY);
        $this->view->yestodayPaymentBank = $assemble->findPredefinedAssembleDataWithPaymentAndNetbank(ZtChart_Model_Assemble_Datetime::YESTODAY);
        $this->view->yestodayConsume = $assemble->findPredefinedAssembleDataWithConsumeAndApa(ZtChart_Model_Assemble_Datetime::YESTODAY);
        
        $assemble = new ZtChart_Model_Assemble('Flserver_Account');
        $assemble->setFrontendFormat('sum');
        $this->view->todayAccount = $assemble->findPredefinedAssembleDataWithAccount(ZtChart_Model_Assemble_Datetime::TODAY);
        $this->view->yestodayAccount = $assemble->findPredefinedAssembleDataWithAccount(ZtChart_Model_Assemble_Datetime::YESTODAY);
    }
    
    /**
     * 游戏列表
     */
    public function groupAction()
    {
        $this->view->request = $this->_request;
        
        $assemble = new ZtChart_Model_Assemble('Usertrade_Account');
        $assemble->setFrontendFormat('group');
        $this->view->todayConsume = $assemble->findPredefinedAssembleDataWithConsumeAndApaAndGroup(ZtChart_Model_Assemble_Datetime::TODAY);
        $this->view->yestodayConsume = $assemble->findPredefinedAssembleDataWithConsumeAndApaAndGroup(ZtChart_Model_Assemble_Datetime::YESTODAY);
        $this->view->todayConsumeBank = $assemble->findPredefinedAssembleDataWithConsumeAndNetbankAndGroup(ZtChart_Model_Assemble_Datetime::TODAY);
        $this->view->yestodayConsumeBank = $assemble->findPredefinedAssembleDataWithConsumeAndNetbankAndGroup(ZtChart_Model_Assemble_Datetime::YESTODAY);
        
        $assemble = new ZtChart_Model_Assemble('Flserver_Account');
        $assemble->setFrontendFormat('group');
        $this->view->todayAccount = $assemble->findPredefinedAssembleDataWithAccountAndGroup(ZtChart_Model_Assemble_Datetime::TODAY);
        $this->view->yestodayAccount = $assemble->findPredefinedAssembleDataWithAccountAndGroup(ZtChart_Model_Assemble_Datetime::YESTODAY);
    }
}