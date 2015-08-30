<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @subpackage ZtChart_Model_Monitor_Aggregate
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Ip.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * Flserver日志数据按IP聚合类
 *
 * @see ZtChart_Model_Monitor_Aggregate_Flserver
 * @name ZtChart_Model_Monitor_Aggregate_Flserver_Ip
 */
class ZtChart_Model_Monitor_Aggregate_Flserver_Ip extends ZtChart_Model_Monitor_Aggregate_Flserver {
    
    /**
     * 产生flserver表按IP导出分组数据的SQL语句
     * 
     * @see ZtChart_Model_Monitor_Aggregate_Flserver::groupSQL()
     */
    public function groupSQL($start, $end, $pos) {
        return "SELECT LEFT(flserver_datetime, {$pos}) AS dt, flserver_gametype, 
                            flserver_clientip, SUM(flserver_count), ''
                FROM flserver_ip
                WHERE flserver_datetime >= '{$start}' AND flserver_datetime <= '{$end}'
                GROUP BY dt, flserver_gametype, flserver_clientip
                INTO OUTFILE '%s'
                FIELDS TERMINATED BY ','";
    }
}