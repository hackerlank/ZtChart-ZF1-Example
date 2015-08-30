<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Bootstrap
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Bootstrap.php 35722 2012-06-27 04:04:25Z zhangweiwen $
 */

/**
 * 定义了游戏总揽模块的环境配置
 * 
 * @name Profile_Bootstrap
 * @see Zend_Application_Module_Bootstrap
 */
class Profile_Bootstrap extends Zend_Application_Module_Bootstrap {

    /**
     * 初始化游戏总揽的类的命名空间
     * 
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload() {
        return new Zend_Application_Module_Autoloader(array(
            'namespace' => 'ZtChart_Profile', 
            'basePath' => realpath(__DIR__)
        ));
    }
}