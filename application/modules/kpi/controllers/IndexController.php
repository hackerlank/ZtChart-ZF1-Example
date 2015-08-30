<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: IndexController.php 35723 2012-06-27 04:04:49Z zhangweiwen $
 */

/**
 * 游戏KPI模块的默认控制器
 *
 * @name Kpi_IndexController
 * @see ZtChart_Model_Controller_Action
 */
class Kpi_IndexController extends ZtChart_Model_Controller_Action {

    /**
     * 游戏KPI首页
     */
    public function indexAction()
    {
        $controller = Zend_Registry::get('user')->getRole()
                                    ->getControllerResource('kpi') ?: 'stats';
        
        $this->_goto('index', $controller);
    }
}