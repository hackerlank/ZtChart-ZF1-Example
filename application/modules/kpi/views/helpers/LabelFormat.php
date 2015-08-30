<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Kpi_View
 * @subpackage Kpi_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: LabelFormat.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 视图助手——图标的坐标格式化
 *
 * @name Kpi_View_Helper_LabelFormat
 * @see Zend_View_Helper_Abstract
 */
class Kpi_View_Helper_LabelFormat extends Zend_View_Helper_Abstract {

    /**
     * 
     */
    const LABEL_NUMBER = 12;
    
    /**
     * 
     * @staticvar boolean
     */
    static protected $_script = false;
    
    /**
     * 格式化坐标
     * 
     * @param string $labels
     * @return string
     */
    public function labelFormat($labels) {
        $script = "
            var lformat = function(datetime) {
                switch (datetime.length) {
                    case 19:
                        datetime = datetime.substr(14);
                        break;
                    case 16:
                        datetime = datetime.substr(11, 5);
                        break;
                    case 13:
                        datetime = datetime.substr(11, 2) + ':00';
                        break;
                    case 10:
                        datetime = datetime.substr(5, 5);
                        break;
                    case 7:
                        datetime = datetime.substr(0, 7);
                        break;
                    case 4:
                        datetime = datetime.substr(0, 4);
                        break;
                    default:
                        datetime = '';
                }
        
                return datetime;
            };
        ";
        if (false === self::$_script) {
            $this->view->headScript()->appendScript($script);
            self::$_script = true;
        }
        
        $count = ceil(count($labels) / self::LABEL_NUMBER);
        $json = Zend_Json::encode($labels);
        
        return "$.map({$json}, function(value, index) { return index % {$count} == 0 ? lformat(value) : ''; })";
    }
    
    
}