<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: ResourceController.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统管理模块的资源控制器
 *
 * @name System_ResourceController
 * @see ZtChart_Model_Controller_Action
 */
class System_ResourceController extends ZtChart_Model_Controller_Action {

    /**
     * 首页
     */
    public function indexAction()
    {
        $action = Zend_Registry::get('user')->getRole()
                                ->getActionResource('system', 'resource') ?: 'list';
        $this->_forward($action);
    }
    
    /**
     * 资源列表
     */
    public function listAction()
    {
        $resource = new ZtChart_System_Model_Resource();
        $this->view->resource = $resource->fetch();
    }
    
    /**
     * 重置
     */
    public function resetAction()
    {
        if ($this->_request->isPost()) {
            if (Zend_Registry::isRegistered('Zend_Navigation')) {
                $container = Zend_Registry::get('Zend_Navigation');
                if ($container instanceof Zend_Navigation_Container) {
                    $resource = new ZtChart_System_Model_Resource();
                    $resource->import($container, 1);
                    ZtChart_Model_Acl_Loader::resetCache();
                    return;
                }
            }
            $this->_helper->dialog('数据重置错误', Zend_Log::ERR);
        }
    }
    
    /**
     * 覆盖
     */
    public function replaceAction()
    {
        if ($this->_request->isPost()) {
            if (Zend_Registry::isRegistered('Zend_Navigation')) {
                $container = Zend_Registry::get('Zend_Navigation');
                if ($container instanceof Zend_Navigation_Container) {
                    $resource = new ZtChart_System_Model_Resource();
                    $resource->import($container, 3);
                    ZtChart_Model_Acl_Loader::resetCache();
                    return;
                }
            }
            $this->_helper->dialog('数据追加错误', Zend_Log::ERR);
        }
    }
}