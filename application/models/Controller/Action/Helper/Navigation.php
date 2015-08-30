<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Controller
 * @subpackage ZtChart_Model_Controller_Action_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Navigation.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 动作助手——导航显示
 *
 * @name ZtChart_Model_Controller_Action_Helper_Navigation
 * @see Zend_Controller_Action_Helper_Abstract
 */
class ZtChart_Model_Controller_Action_Helper_Navigation extends Zend_Controller_Action_Helper_Abstract {

    /**
     * 
     * @param Zend_Controller_Action $controller
     */
    public function __construct(Zend_Controller_Action $actionController = null) {
        if (null !== $actionController) {
            $this->setActionController($actionController);
        }
    }
    
    /**
     * 设置导航项中需要显示的动作器
     * 
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param boolean $keep
     * @return void
     */
    public function activeAction($action, $controller = null, $module = null, $keep = false) {
        if (null === $controller) {
            $controller = $this->getRequest()->getControllerName();
        }
        if (null === $module) {
            $module = $this->getRequest()->getModuleName();
        }
        if (null !== ($navigation = $this->_getNavigation())) {
            if (null !== ($page = $navigation->findOneBy('module', $module))) {
                if (null !== ($page = $page->findOneBy('controller', $controller))) {
                    if (null !== ($page = $page->findOneBy('action', $action))) {
                        $page->setActive();
                    }
                }
            }
        }
    }
    
    /**
     * 设置导航项中需要显示的控制器
     *
     * @param string $controller
     * @param string $module
     * @return void
     */
    public function activeController($controller, $module = null) {
        if (null === $module) {
            $module = $this->getRequest()->getModuleName();
        }
        if (null !== ($navigation = $this->_getNavigation())) {
            if (null !== ($page = $navigation->findOneBy('module', $module))) {
                if (null !== ($page = $page->findOneBy('controller', $controller))) {
                    $page->setActive();
                }
            }
        }
    }
    
    /**
     * 设置导航项中需要显示的模块
     *
     * @param string $module
     * @return void
     */
    public function activeModule($module) {
        if (null !== ($navigation = $this->_getNavigation())) {
            if (null !== ($page = $navigation->findOneBy('module', $module))) {
                $page->setActive();
            }
        }
    }
    
    /**
     * 
     * @return Zend_Navigation
     */
    protected function _getNavigation() {
        if (Zend_Registry::isRegistered('Zend_Navigation')) {
            $navigation = Zend_Registry::get('Zend_Navigation');
            if ($navigation instanceof Zend_Navigation) {
                return $navigation;
            }
        }
        
        return null;
    }
}