<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Plugin_Layout
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Layout.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局控制器动作插件——布局控制
 *
 * @see ZtChart_Model_Layout_Controller_Plugin_Layout
 * @name ZtChart_Plugin_Layout
 */
class ZtChart_Plugin_Layout extends ZtChart_Model_Layout_Controller_Plugin_Layout {

    /**
     * 
     * @staticvar array
     */
    static protected $_disabledModules = array();
    
    /**
     * 设置需要禁止布局的模块
     * 
     * @static
     * @param array $modules
     * @return void
     */
    static public function setDisabledModules(array $modules) {
        self::$_disabledModules = $modules;
    }
    
    /**
     * 添加需要禁止布局的模块名
     * 
     * @static
     * @param string $moduleName
     * @return void
     */
    static public function addDisabledModules($moduleName) {
        self::$_disabledModules[] = $moduleName;
    }
    
    /**
     * 判断当前模块是否需要禁止布局
     * 
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if (in_array($request->getModuleName(), self::$_disabledModules)) {
            $this->_layout->disableLayout();
        }
    }
}