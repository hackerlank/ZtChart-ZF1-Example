<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @subpackage ZtChart_Model_Monitor_Aggregate
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 日志数据聚合抽象类
 *
 * @abstract
 * @name ZtChart_Model_Monitor_Aggregate_Abstract
 */
abstract class ZtChart_Model_Monitor_Aggregate_Abstract {
    
    /**
     * 
     */
    const GROUP_DAY = '_d';
    const GROUP_HOUR = '_h';
    const GROUP_MINUTE = '_i';
    
    /**
     * 取得所有SQL导出语句
     * 
     * @param $timestamp
     * @return array
     */
    public function getOutfileSQLs($timestamp = 0) {
        $groups = array(self::GROUP_MINUTE, self::GROUP_HOUR);
        if ('00' == date('H', $timestamp ?: time())) {
            $groups[] = self::GROUP_DAY;
        }
        
        $sqls = array();
        foreach ($groups as $flag) {
            if (false !== ($options = $this->_getGroupOptions($timestamp, $flag))) {
                $sqls[$flag] = $this->groupSQL($options['start'], $options['end'], $options['pos']);
            }
        }
        
        return $sqls;
    }
    
    /**
     * 取得时间分组参数
     * 
     * @param integer $timestamp
     * @param string $flag
     */
    protected function _getGroupOptions($timestamp, $flag) {
        switch ($flag) {
            case self::GROUP_DAY:
                $start = date('Y-m-d 00:00:00', $timestamp - 86400);
                $end = date('Y-m-d 23:59:59', $timestamp - 86400);
                $pos = 10;
                break;
            case self::GROUP_HOUR:
                $start = date('Y-m-d H:00:00', $timestamp);
                $end = date('Y-m-d H:59:59', $timestamp);
                $pos = 13;
                break;
            case self::GROUP_MINUTE:
                $start = date('Y-m-d H:00:00', $timestamp);
                $end = date('Y-m-d H:59:59', $timestamp);
                $pos = 16 + 1; // 为了导入的日期格式能被识别，所以截取到冒号。
                break;
            default:
                return false;
        }
        
        return compact('start', 'end', 'pos');
    }
    
    /**
     * 产生分组导出数据的SQL语句
     * 
     * @abstract
     * @param string $start
     * @param string $end
     * @param integer $pos
     * @return array
     */
    abstract public function groupSQL($start, $end, $pos);
}