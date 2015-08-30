<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: LogoutController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 用户退出控制器
 *
 * @name LogoutController
 * @see Zend_Controller_Action
 */
 
class LogoutController extends Zend_Controller_Action
{
    
    /**
     * 初始化，设置不用布局模式。
     */
    public function init()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
    }
    
    /**
     * 用户退出
     */
    public function indexAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/'); 
    }
}

