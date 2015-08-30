<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Kpi_View
 * @subpackage Kpi_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: InflectorUrl.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 视图助手——URL地址变形器
 *
 * @name Kpi_View_Helper_InflectorUrl
 * @see Zend_View_Helper_Abstract
 */
class Kpi_View_Helper_InflectorUrl extends Zend_View_Helper_Abstract {

    /**
     * 根据类别变形URL地址
     * 
     * @param string $url
     * @param string $type
     * @return string
     */
    public function inflectorUrl($url, $type) {
        if (!empty($url) && method_exists($this, $method = '_' . $type)) {
            if (preg_match('/href="(.+)"/i', $url, $match)) {
                $url = preg_replace('/href=".+"/i', 'href="' . $this->$method($match[1]) . '"', $url);
            }
        }
        
        return $url;
    }
    
    /**
     * 把实时监控的路由地址变形为带有问号参数的地址
     * 
     * @param string $url
     * @return string
     */
    protected function _monitor($url) {
        $url = str_replace($this->view->baseUrl(), '', $url);
        $pieces = explode(DIRECTORY_SEPARATOR, trim($url, DIRECTORY_SEPARATOR), 3);
        $pieces[2] = '?select=' . $pieces[2];
        
        return $this->view->baseUrl(implode(DIRECTORY_SEPARATOR, $pieces));
    }
}