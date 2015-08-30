<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Kpi_View
 * @subpackage Kpi_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Url.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 视图助手——产生包括查询字符串的URL地址
 *
 * @name Kpi_View_Helper_Url
 * @see Zend_View_Helper_Url
 */
class Kpi_View_Helper_Url extends Zend_View_Helper_Url {

    /**
     * Generates an url given the name of a route.
     *
     * @access public
     *
     * @param  array $urlOptions Options passed to the assemble method of the Route object.
     * @param  mixed $name The name of a Route to use. If null it will use the current Route
     * @param  bool $reset Whether or not to reset the route defaults with those provided
     * @return string Url for the link href attribute.
     */
    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true) {
        if ('' != $queryString = Zend_Controller_Front::getInstance()
                                        ->getRequest()->getServer('QUERY_STRING')) {
            $queryString = '?' . $queryString;
        }
        
        return parent::url($urlOptions, $name, $reset, $encode) . $queryString;
    }
}