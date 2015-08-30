<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @subpackage ZtChart_Model_Monitor_Daemon
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Archive.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 日志数据归档类
 *
 * @final
 * @see ZtChart_Model_Monitor_Daemon_Abstract
 * @name ZtChart_Model_Monitor_Daemon_Archive
 */
final class ZtChart_Model_Monitor_Daemon_Archive extends ZtChart_Model_Monitor_Daemon_Abstract {
    
    /**
     * 延迟时间
     */
    const CYCLE_TIMESTAMP = 3600;
    
    /**
     * 
     * @var string
     */
    protected $_pattern = 'ymd-H';
    
    /**
     * 
     * @var integer
     */
    protected $_timestamp = 0;
    
    /**
     * 
     * @var array
     */
    protected $_aggregates = array();
    
    /**
     * 取得文件归档的最后时间
     * 
     * @static
     * @param string $format
     * @return string
     */
    static public function getDeadlineTime($format = "Y-m-d H:00:00") {
        $storage = new ZtChart_Model_Storage('monitor', 'archive');
        
        return date($format, $storage->read());
    }
    
    /**
     * 运行守护进程，归档日志文件。
     * 
     * @see ZtChart_Model_Monitor_Abstract::run()
     */
    public function run() {
        sleep($this->_console->getDelayTimestamp());
        
        while (true) {
            $this->_timestamp = $timestamp = time();
            $deadlineTimestamp = $timestamp - self::CYCLE_TIMESTAMP;
            $this->import($this->_console->getLogPaths(), $deadlineTimestamp);
            
            $storage = new ZtChart_Model_Storage('monitor', 'archive');
            $storage->write($deadlineTimestamp);
            
            time_sleep_until($timestamp + self::CYCLE_TIMESTAMP);
        }
    }
    
    /**
     * 导入以前的日志文件
     * 
     * @param array $logPaths
     * @param integer $timestamp
     * @return void
     */
    public function import($logPaths, $timestamp) {
        $pattern = date($this->_pattern, $timestamp);
        foreach ($this->getCategory($logPaths, $pattern) as $identifier => $logInfo) {
            $multiline = '';
            foreach ($logInfo as $key => $value) {
                $multiline .= implode(ZtChart_Model_Monitor_Log_Abstract::DELIMITER, array($key, $value, PHP_EOL));
            }
            $tmpfile = $this->_tmpfile($identifier);
            if (file_put_contents($tmpfile, $multiline)) {
                try {
                    $affected = $this->_exec("LOAD DATA INFILE '{$tmpfile}' INTO TABLE {$identifier} FIELDS TERMINATED BY ','");
                } catch (Zend_Db_Adapter_Exception $e) {
                    return;
                }
                $this->_logger->info("{$affected} 条数据被导入 {$identifier}");
            }
            @unlink($tmpfile);
            
            if (false !== ($aggregate = $this->getAggregate($identifier))) {
                foreach ($aggregate->getOutfileSQLs($timestamp) as $suffix => $outfileSQL) {
                    $tmpfile = $this->_tmpfile($identifier);
                    try {
                        $this->_exec(sprintf($outfileSQL, $tmpfile), false);
                        $affected = $this->_exec("LOAD DATA INFILE '{$tmpfile}' INTO TABLE {$identifier}{$suffix} FIELDS TERMINATED BY ','");
                    } catch (Zend_Db_Adapter_Exception $e) {
                        return;
                    }
                    @unlink($tmpfile);
                    $this->_logger->info("{$affected}条数据被导入 {$identifier}{$suffix}");
                }
            }
        }
    }
    
    /**
     * 取得所有的日志分类统计数据
     * 
     * @param array $logPaths
     * @param string $pattern
     * @return array
     */
    public function getCategory($logPaths, $pattern) {
        $stats = array();
        foreach($this->getObjectFiles($logPaths, $pattern) as $objectFile) {
            fprintf(STDOUT, "读取文件：%s (%d Byte)...", $objectFile, filesize($objectFile));
            $sedFile = $this->_sed($objectFile);
            fprintf(STDOUT, "过滤后文件：%s (%d Byte)...", $sedFile, filesize($sedFile));
            foreach (file($sedFile) as $no => $rawline) {
                try {
                    $mlog = new ZtChart_Model_Monitor_Log($rawline);
                    if (false !== ($lines = $mlog->category())) {
                        foreach ($lines as $identifier => $entry) {
                            if (!array_key_exists($identifier, $stats)) {
                                $stats[$identifier] = array();
                            }
                            foreach ($entry as $key => $value) {
                                if (!array_key_exists($key, $stats[$identifier])) {
                                    $stats[$identifier][$key] = 0;
                                }
                                $stats[$identifier][$key] += $value;
                            }
                        }
                    }
                } catch (ZtChart_Model_Monitor_Log_Exception $e) {
                    $this->_logger->warn("日志处理错误({$no})：" . iconv('GBK', 'UTF-8', $rawline));
                }
            }
            @unlink($sedFile);
            fprintf(STDOUT, " 完毕\n");
        }
        
        return $stats;
    }
    
    /**
     * 取得日志数据聚合分析的对象
     * 
     * @param string $identifier
     * @return false|ZtChart_Model_Monitor_Aggregate_Abstract
     */
    public function getAggregate($identifier) {
        if (!array_key_exists($identifier, $this->_aggregates)) {
            try {
                $this->_aggregates[$identifier] = ZtChart_Model_Monitor_Aggregate::factory($identifier);
            } catch (ZtChart_Model_Monitor_Aggregate_Exception $e) {
                return false;
            }
        }
        
        return $this->_aggregates[$identifier];
    }
    
    /**
     * 取得要解析的文件列表
     * 
     * @param array $logPaths
     * @param string $pattern
     * @return array
     */
    public function getObjectFiles($logPaths, $pattern) {
        $objectFiles = array();
        foreach ($logPaths as $file) {
            if (is_dir($file)) {
                foreach (new DirectoryIterator("glob://{$file}/*.{$pattern}") as $logFile) {
                    if ($this->accept($logFile->getFilename())) {
                        $objectFiles[] = $logFile->getPathname();
                    }
                }
            } else if (is_file($file) && $this->accept(basename($file))) {
                $objectFiles[] = $file;
            }
        }
        
        return $objectFiles;
    }
    
    /**
     * 设置文件匹配模式
     * 
     * @param string $pattern
     * @return void
     */
    public function setPattern($pattern) {
        $this->_pattern = $pattern;
    }
}