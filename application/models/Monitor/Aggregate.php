<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Aggregate.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 日志数据聚合类
 *
 * @name ZtChart_Model_Monitor_Aggregate
 */
class ZtChart_Model_Monitor_Aggregate {
    
    /**
     * 
     * @staticvar Zend_Loader_PluginLoader
     */
    static protected $_loader = null;
    
    /**
     * 工厂方法，产生日志数据聚合类。
     * 
     * @param string $identifier
     * @param boolean $throwException
     * @throws ZtChart_Model_Monitor_Aggregate_Exception
     * @return ZtChart_Model_Monitor_Aggregate_Abstract
     */
    static public function factory($identifier, $throwException = true) {
        if (null === self::$_loader) {
            self::$_loader = new Zend_Loader_PluginLoader(
                array(
                    'ZtChart_Model_Monitor_Aggregate_' => realpath(__DIR__ . '/Aggregate')
                )
            );
        }
        $identifier = implode('_', array_map('ucfirst', explode('_', $identifier)));
        if (false === ($aggregateClass = self::$_loader->load($identifier, false))) {
            if (!$throwException) {
                return false;
            }
            throw new ZtChart_Model_Monitor_Aggregate_Exception(
                    "Specified aggregate class '{$identifier}' could not be found");
        }
        
        return new $aggregateClass();
    }
}