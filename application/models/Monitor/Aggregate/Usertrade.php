<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @subpackage ZtChart_Model_Monitor_Aggregate
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Usertrade.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * Usertrade日志数据聚合类
 *
 * @see ZtChart_Model_Monitor_Aggregate_Abstract
 * @name ZtChart_Model_Monitor_Aggregate_Usertrade
 */
class ZtChart_Model_Monitor_Aggregate_Usertrade extends ZtChart_Model_Monitor_Aggregate_Abstract {
    
    /**
     * 
     * @see ZtChart_Model_Monitor_Aggregate_Abstract::groupSQL()
     * @throws ZtChart_Model_Monitor_Aggregate_Exception 
     */
    public function groupSQL($start, $end, $pos) {
        throw new ZtChart_Model_Monitor_Aggregate_Exception('The table usertrade do not support grouped data.');
    }
}