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
 * 默认控制器
 *
 * @name IndexController
 * @see Zend_Controller_Action
 */
class IndexController extends Zend_Controller_Action
{

    /**
     * 首页
     */
    public function indexAction()
    {
        if ($this->_request->isPost() && $this->_hasParam('login')) {
            $this->_helper->actionStack('index', 'login');
        }
    }
    
    /**
     * 
     */
    public function denyAction()
    {
        
    }
    
    /**
     * 
     */
    public function forbiddenAction()
    {
        
    }
}

