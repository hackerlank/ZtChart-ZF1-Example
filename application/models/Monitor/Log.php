<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Log.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 日志数据解析类
 *
 * @name ZtChart_Model_Monitor_Log
 */
class ZtChart_Model_Monitor_Log {
    
    /**
     *
     * @var string
     */
    protected $_datetime = null;
    
    /**
     *
     * @var string
     */
    protected $_identifier = null;
    
    /**
     * 
     * @var string
     */
    protected $_data = null;
    
    /**
     * 
     * @var string
     */
    protected $_charset = 'GBK';
    
    /**
     * 
     * @var ZtChart_Model_Monitor_Log_Abstract
     */
    protected $_identifierObject = null;
    
    /**
     * 
     * @staticvar Zend_Loader_PluginLoader
     */
    static protected $_loader = null;
    
    /**
     * 
     * @staticvar array
     */
    static protected $_existIdentifierClasses = array();
    
    /**
     * 
     * @staticvar array
     */
    static protected $_identifierClasses = array();
    
    /**
     * 取得日志分析类装载期
     * 
     * @return Zend_Loader_PluginLoader
     */
    static public function getLogLoader() {
        if (null === self::$_loader) {
            self::$_loader = new Zend_Loader_PluginLoader(
                array(
                    'ZtChart_Model_Monitor_Log_' => realpath(__DIR__ . '/Log')
                )
            );
        }
        
        return self::$_loader;
    }
    
    /**
     * 工厂方法，产生日志解析类。
     * 
     * @param string $datetime
     * @param string $identifier
     * @param string $data
     * @param string $charset
     * @param boolean $throwException
     * @throws ZtChart_Model_Monitor_Log_Exception
     * @return ZtChart_Model_Monitor_Log_Abstract
     */
    static public function factory($datetime, $identifier, $data, $charset = null, $throwException = true) {
        if (!array_key_exists($identifier, self::$_identifierClasses)) {
            if (false === ($identifierClass = self::getLogLoader()->load($identifier, false))) {
                if (!$throwException) {
                    return false;
                }
                throw new ZtChart_Model_Monitor_Log_Exception(
                        "Specified identifier class '{$identifier}' could not be found");
            } else {
                self::$_identifierClasses[$identifier] = $identifierClass;
            }
        } else {
            $identifierClass = self::$_identifierClasses[$identifier];
        }
        
        return new $identifierClass($datetime, $data, $charset);
    }
    
    /**
     * 取得所有已经存在的日志类
     * 
     * @static
     * @return array
     */
    static public function getExistIdentifierClasses() {
        return self::$_existIdentifierClasses;
    }
    
    /**
     * 取得所有日志类
     * 
     * @static
     * @return array
     */
    static public function getIdentifierClasses() {
        if (empty(self::$_identifierClasses)) {
            $paths = self::getLogLoader()->getPaths('ZtChart_Model_Monitor_Log_');
            foreach (new DirectoryIterator("glob://{$paths[0]}/*.php") as $entry) {
                if ($entry->isFile()) {
                    if (false !== ($identifierClass = self::getLogLoader()->load($entry->getBasename('.php'), false))
                        && is_subclass_of($identifierClass, 'ZtChart_Model_Monitor_Log_Abstract')) {
                        
                        self::$_identifierClasses[] = $identifierClass;
                    }
                }
            }
        }
        
        return self::$_identifierClasses;
    }
    
    /**
     * 
     * @param string $line
     * @param string $charset
     */
    public function __construct($line, $charset = null) {
        if (!empty($line)) {
            list($datetime, $identifier, $data) = array_pad(explode(' ', $line, 3), 3, '');
            
            $this->_datetime = $this->normalizeDatetime($datetime);
            $this->_identifier = strtolower(trim($identifier));
            $this->_data = trim($data);
        }
        if (!empty($charset)) {
            $this->_charset = $charset;
        }
    }
    
    /**
     * 
     */
    public function __destruct() {
        $this->_identifierObject = null;
    }
    
    /**
     * 把原始行转换成可导入数据库的行
     *
     * @param string $charset
     * @return string
     */
    public function transform($charset = null) {
        $line = '';
        if (!empty($this->_data) && !empty($this->_identifier)) {
            if (false !== ($io = self::factory($this->_datetime, $this->_identifier, $this->_data, $this->_charset, false))) {
                if (false != (boolean) ($lines = $io->analyze($charset))) {
                    $line = implode(PHP_EOL, $lines) . PHP_EOL;
                }
                $this->_identifierObject = $io;
            }
        }
    
        return $line;
    }
    
    /**
     * 分析原始行，返回可分类统计的行数组。
     * 
     * @param string $charset
     * @return false|array
     */
    public function category($charset = null) {
        if (!empty($this->_data) && !empty($this->_identifier)) {
            if (false !== ($io = self::factory($this->_datetime, $this->_identifier, $this->_data, $this->_charset, false))) {
                $this->_identifierObject = $io;
                if (false != (boolean) ($stats = $io->stats($charset))) {
                    return $stats;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 取得当前日志类型的共享内存标识符
     * 
     * @return integer
     */
    public function getShmIdentifier() {
        return $this->_identifierObject->getShmIdentifier();
    }
    
    /**
     * 转换时间日期为正规格式
     *
     * @param string $datetime
     * @return string
     */
    public function normalizeDatetime($datetime) {
        $datetimeInfo = explode('-', $datetime);
        if (2 == count($datetimeInfo)) {
            list($date, $time) = $datetimeInfo;
            $datetime = implode('-', str_split($date, 2)) . ' ' . $time;
            $datetime = date('Y-m-d H:i:s', strtotime($datetime));
        } else {
            throw new ZtChart_Model_Monitor_Log_Exception('Log datetime format error.');
        }
    
        return $datetime;
    }
    
    /**
     * 取得日志的日期
     * 
     * @return string
     */
    public function getDatetime() {
        return $this->_datetime;
    }
    
    /**
     * 取得日志的类型
     * 
     * @return string
     */
    public function getIdentifier() {
        return $this->_identifier;
    }
}