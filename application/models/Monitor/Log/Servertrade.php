<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @subpackage ZtChart_Model_Monitor_Log
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Servertrade.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * Usertrade日志分析类
 *
 * @see ZtChart_Model_Monitor_Log_Usertrade
 * @name ZtChart_Model_Monitor_Log_Servertrade
 */

// 由于Usertrade日志中的标识符是ServerTrade，所以在此创建Usertrade日志分析类的别名。
if (!class_alias('ZtChart_Model_Monitor_Log_Usertrade', 'ZtChart_Model_Monitor_Log_Servertrade')) {
    throw new ZtChart_Model_Monitor_Log_Exception('Cannot create class ZtChart_Model_Monitor_Log_Servertrade.');
}