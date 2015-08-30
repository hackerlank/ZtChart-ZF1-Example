<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Navigation
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Navigation.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统导航类
 *
 * @name ZtChart_Model_Navigation
 * @see Zend_Navigation
 */
class ZtChart_Model_Navigation extends Zend_Navigation {

    /**
     * Creates a new navigation container
     *
     * @see Zend_Navigation::__construct()
     * @param string|array|Zend_Config $pages    [optional] pages to add
     * @param string $environment                [optional] environment in pages
     * @throws Zend_Navigation_Exception         if $pages is invalid
     */
    public function __construct($pages = null, $environment = 'navigation') {
        if (is_string($pages) && is_file($pages)) {
            $file = $pages;
            switch (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
                case 'ini':
                    $pages = new Zend_Config_Ini($file, $environment);
                    break;
                case 'xml':
                    $pages = new Zend_Config_Xml($file, $environment);
                    break;
                case 'json':
                    $pages = new Zend_Config_Json($file, $environment);
                    break;
                case 'yaml':
                case 'yml':
                    $pages = new Zend_Config_Yaml($file, $environment);
                    break;
                default:
                    throw new ZtChart_Model_Navigation_Exception('Invalid configuration file provided; unknown config type');
            }
        }
        
        parent::__construct($pages);
    }
}