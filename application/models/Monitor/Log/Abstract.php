<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @subpackage ZtChart_Model_Monitor_Log
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 日志分析抽象类
 *
 * @abstract
 * @name ZtChart_Model_Monitor_Log_Abstract
 */
abstract class ZtChart_Model_Monitor_Log_Abstract {

    /**
     * 
     */
    const CHARSET = 'UTF-8';
    
    /**
     * 
     */
    const DELIMITER = ',';
    
    /**
     * 
     * @var string
     */
    protected $_datetime = null;
    
    /**
     * 
     * @var string
     */
    protected $_data = null;
    
    /**
     * 
     * @var ZtChart_Model_Assemble_Ip
     */
    protected $_ip = null;
    
    /**
     * 构造函数，转换数据的编码。
     * 
     * @param string $datetime
     * @param string $data
     * @param string $charset
     * @throws ZtChart_Model_Monitor_Log_Exception
     */
    public function __construct($datetime, $data, $charset = null) {
        $this->_datetime = $datetime;
        if (!empty($charset)) {
            if (!extension_loaded('mbstring')) {
                throw new ZtChart_Model_Monitor_Log_Exception('');
            }
            $data = mb_convert_encoding($data, self::CHARSET, $charset);
        }
        $this->_data = $data;
    }
    
    /**
     * 取得子类的共享内存标识符
     * 
     * @return integer
     */
    public function getShmIdentifier() {
        $r = new ReflectionClass($this);
        
        return $r->getConstant('SHM_IDENTIFIER');
    }
    
    /**
     * 分析日志中的数据部分并转换编码
     * 
     * @param string $charset 输出的编码格式
     * @return array
     */
    public function analyze($charset = null) {
        if (null !== ($data = $this->_analyze($this->_data))) {
            foreach ($data as &$line) {
                $line = array_merge(array('datetime' => $this->_datetime), $line);
                if (!empty($charset)) {
                    $line = array_map(function($value) use ($charset) {
                        return is_string($value) ? mb_convert_encoding($value, $charset, self::CHARSET) : $value;
                    }, $line);
                }
            }
        } else {
            $data = array();
        }
        
        return $this->_compose($data);
    }
    
    /**
     * 统计日志中的数据部分并转换编码
     * 
     * @param string $charset 输出的编码格式
     * @return false|array
     */
    public function stats($charset = null) {
        if (false !== $stats = ($this->_stats($this->_data))) {
            foreach ($stats as &$entry) {
                $newEntry = array();
                foreach ($entry as $key => $value) {
                    $newKey = $this->_datetime . self::DELIMITER
                            . (!empty($charset) ? mb_convert_encoding($key, $charset, self::CHARSET) : $key);
                    $newEntry[$newKey] = $value;
                }
                $entry = $newEntry;
            }
        }
        
        return $stats;
    }
    
    /**
     * 组合成可导入数据库的字符串数组
     * 
     * @param array $lines
     * @return array
     */
    protected function _compose($lines) {
        foreach ($lines as &$data) {
            array_push($data, '');
            $data = $this->_join($data);
        }
        
        return $lines;
    }
    
    /**
     * 拼接可导入数据库的字符串
     * 
     * @param array $data
     * @return string
     */
    protected function _join($data) {
        return implode(self::DELIMITER, $data);
    }
    
    /**
     * IP转地区号
     * 
     * @param integer|string $ip
     * @param boolean $ip2long
     * @return integer
     */
    protected function _ip2area($ip, $ip2long = false) {
        if ($ip2long && is_string($ip)) {
            $ip = ip2long($ip);
        }
        if (null === $this->_ip) {
            $this->_ip = new ZtChart_Model_Assemble_Ip();
        }
        
        return $this->_ip->getArea($ip);
    }
    
    /**
     * 分析日志中的数据部分
     * 
     * @abstract
     * @param string $data
     * @return array
     */
    abstract protected function _analyze($data);
    
    /**
     * 统计日志中的数据部分
     * 
     * @param string $data
     * @param string $datetime
     * @return false|array
     */
    abstract protected function _stats($data);
}