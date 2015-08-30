<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: LineController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 演示案例模块的线条图控制器
 *
 * @name Demo_LineController
 * @see Zend_Controller_Action
 */
class Demo_LineController extends Zend_Controller_Action {
    
    /**
     * 默认页面
     */
    public function indexAction()
    {
        $assemble = new ZtChart_Model_Assemble('Flserver');
        $this->view->chart = $assemble->getAssembleData('2012-03-13 10:30:15', '2012-03-13 10:35:23', Zend_Date::MINUTE);
    }
    
    /**
     * 通过Ajax获取数据
     */
    public function ajaxAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $assemble = new ZtChart_Model_Assemble('Flserver');
            $this->_helper->json($assemble->getAssembleData($this->_getParam('start'), 
                                                $this->_getParam('end'), Zend_Date::SECOND));
        }
    }
}