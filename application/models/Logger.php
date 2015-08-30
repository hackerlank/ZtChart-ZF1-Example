<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Bootstrap
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author$
 * @version $Id$
 */

/**
 * 日志类
 *
 * @final
 * @name ZtChart_Model_Logger
 */
final class ZtChart_Model_Logger {
    
    /**
     * 
     * @staticvar Zend_Log
     */
    static protected $_logger;
    
    /**
     * 
     * @static
     * @param Zend_Log $logger
     * @return void
     */
    static public function setLogger(Zend_Log $logger) {
        self::$_logger = $logger;
    }
    
    /**
     * 调用Zend_Log的方法
     * 
     * @static
     * @param string $name
     * @param array $arguments
     * @return void
     */
    static public function __callstatic($name, $arguments) {
        if (!empty(self::$_logger) && self::$_logger instanceof Zend_Log) {
            try {
                call_user_func_array(array(self::$_logger, $name), $arguments);
            } catch (Zend_Log_Exception $e) {}
        }
    }
}