<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: IndexController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统管理模块的默认控制器
 *
 * @name System_IndexController
 * @see ZtChart_Model_Controller_Action
 */
class System_IndexController extends ZtChart_Model_Controller_Action {

    /**
     * 系统管理首页
     */
    public function indexAction()
    {
        $controller = Zend_Registry::get('user')->getRole()
                                    ->getControllerResource('system') ?: 'user';
        
        $this->_forward('index', $controller);
        $this->_helper->navigation->activeController($controller);
    }
}