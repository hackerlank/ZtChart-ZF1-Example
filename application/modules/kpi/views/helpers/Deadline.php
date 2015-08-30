<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Kpi_View
 * @subpackage Kpi_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Deadline.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 视图助手——截止日期显示
 *
 * @name Kpi_View_Helper_Deadline
 * @see Zend_View_Helper_Abstract
 */
class Kpi_View_Helper_Deadline extends Zend_View_Helper_Abstract {

    /**
     * 
     * @var string
     */
    protected $_deadline = null;
    
    /**
     * 
     */
    public function __construct() {
        $this->_deadline = ZtChart_Model_Monitor_Daemon_Archive::getDeadlineTime();
    }
    
    /**
     * 比较截止日期与当前日期
     * 
     * @param string $datetime
     * @return string
     */
    public function deadline($datetime) {
        if (!empty($datetime)) {
            $padDatetime = ZtChart_Model_Assemble_Datetime::padDatetime($datetime, Zend_Date::SECOND);
            switch (strcmp($padDatetime, $this->_deadline)) {
                case -1:
                case 0:
                    return $datetime;
                case 1:
                    return substr($this->_deadline, 0, strlen($datetime));
            }
        }
    }
}