<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Console.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 控制台命令参数处理类
 *
 * @name ZtChart_Model_Monitor_Console
 */
class ZtChart_Model_Monitor_Console {
    
    /**
     * 
     * @staticvar ZtChart_Model_Monitor_Console
     */
    static protected $_instance = null;
    
    /**
     * 
     * @var Zend_Console_Getopt
     */
    protected $_console = null;
    
    /**
     * 
     * @var array
     */
    protected $_logDirectories = array();
    
    /**
     * 
     * @var array
     */
    protected $_logFiles = array();
    
    /**
     * 
     * @var integer
     */
    protected $_delayTimestamp = 0;
    
    /**
     * 
     * @var integer
     */
    protected $_startTimestamp = 0;
    
    /**
     *
     * @var integer
     */
    protected $_endTimestamp = 0;
    
    /**
     * 
     * @static
     * @return ZtChart_Model_Monitor_Console
     */
    static public function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * 构造函数，初始化日志文件目录。
     */
    protected function __construct() {
        try {
            $console = new Zend_Console_Getopt(array(
                'logroot|l=s' => 'the Log Server root directory', 
                'logdir|d=s' => 'this log file directory', 
                'logfile|f=s' => 'the log files', 
                'delay|a=i' => 'delay timestamp', 
                'start|s=s' => 'start datetime', 
                'end|e=s' => 'end datetime'
            ));
            if (!$console->getOptions()) {
                throw new ZtChart_Model_Monitor_Console_Exception($console->getUsageMessage());
            }
            if (null !== ($logServerRootDirectory = $console->getOption('l'))) {
                foreach (array_diff(scandir($logServerRootDirectory), array('.', '..')) as $directory) {
                    $this->_logDirectories[] = realpath($logServerRootDirectory) . DIRECTORY_SEPARATOR . $directory;
                }
            }
            if (null !== ($logDirectories = $console->getOption('d'))) {
                $this->_logDirectories += $logDirectories;
            }
            if (null !== ($logFiles = $console->getOption('f'))) {
                $this->_logFiles = explode(' ', $logFiles);
            }
            if (null !== ($delayTimestamp = $console->getOption('a'))) {
                $this->_delayTimestamp = $delayTimestamp;
            }
            if (null !== ($startTimestamp = $console->getOption('s'))) {
                $this->_startTimestamp = $startTimestamp;
            }
            if (null !== ($endTimestamp = $console->getOption('e'))) {
                $this->_endTimestamp = $endTimestamp;
            }
        } catch (Zend_Console_Getopt_Exception $e) {
            throw new ZtChart_Model_Monitor_Console_Exception($e->getMessage());
        }
        
        $this->_console = $console;
    }
    
    /**
     * 取得所有日志文件的路径
     * 
     * @return array
     */
    public function getLogPaths() {
        $logPaths = array();
        foreach ($this->_logDirectories as $directory) {
            if (is_dir($directory)) {
                if (false === ($dh = opendir($directory))) {
                    throw new ZtChart_Model_Monitor_Console_Exception("Directory '{$directory}' can not be opened.");
                }
                closedir($dh);
                $logPaths[] = $directory;
            }
        }
        foreach ($this->_logFiles as $file) {
            if (is_link($file)) {
                $file = readlink($file);
            }
            if (!file_exists($file) || !is_readable($file)) {
                throw new ZtChart_Model_Monitor_Console_Exception("File '{$file}' does not exists or is not readable.");
            }
            $logPaths[] = $file;
        }
        
        return $logPaths;
    } 
    
    /**
     * 取得延迟执行时间
     * 
     * @return integer
     */
    public function getDelayTimestamp() {
        return $this->_delayTimestamp;
    }
    
    /**
     * 取得开始执行时间
     * 
     * @return integer
     */
    public function getStartTimestamp() {
        return $this->_startTimestamp;
    }
    
    /**
     * 取得结束执行时间
     *
     * @return integer
     */
    public function getEndTimestamp() {
        return $this->_endTimestamp;
    }
}