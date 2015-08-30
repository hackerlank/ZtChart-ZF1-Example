<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: IndexController.php 35736 2012-06-27 09:55:59Z zhangweiwen $
 */

/**
 * 游戏总揽模块的默认控制器
 *
 * @name Profile_IndexController
 * @see ZtChart_Model_Controller_Action
 */
class Profile_IndexController extends ZtChart_Model_Controller_Action {

    /**
     * 游戏总揽首页
     */
    public function indexAction()
    {
        $controller = Zend_Registry::get('user')->getRole()
                                    ->getControllerResource('profile') ?: 'summary';
        
        $this->_goto('index', $controller);
    }
}