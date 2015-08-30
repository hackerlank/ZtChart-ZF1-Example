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
 * 演示案例模块的默认控制器
 *
 * @name Demo_IndexController
 * @see Zend_Controller_Action
 */
class Demo_IndexController extends Zend_Controller_Action {

    /**
     * 默认页面
     */
    public function indexAction()
    {
        $this->_forward('index', 'line');
    }
}