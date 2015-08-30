<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Bootstrap
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Bootstrap.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 定义了演示案例模块的环境配置
 * 
 * @name Demo_Bootstrap
 * @see Zend_Application_Module_Bootstrap
 */
class Demo_Bootstrap extends Zend_Application_Module_Bootstrap {

    /**
     * 初始化Demo模块的类的命名空间
     * 
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload() {
        return new Zend_Application_Module_Autoloader(array(
            'namespace' => 'ZtChart_Demo', 
            'basePath' => realpath(__DIR__)
        ));
    }
    
    /**
     * 本模块禁止使用布局
     * 
     * @return void
     */
    protected function _initLayout() {
        ZtChart_Plugin_Layout::addDisabledModules('demo');
    }
}