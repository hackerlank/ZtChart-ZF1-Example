<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: OnlineController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 游戏KPI模块的在线分析控制器
 *
 * @name Kpi_OnlineController
 * @see ZtChart_Model_Controller_Action
 */
class Kpi_OnlineController extends ZtChart_Model_Controller_Action {
    
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
                                ->getActionResource('kpi', 'online') ?: 'detail';
        $this->_goto($action);
    }

    /**
     * 在线详情
     */
    public function detailAction()
    {
        
    }
    
    /**
     * 时段分布
     */
    public function periodAction()
    {
        
    }
}
