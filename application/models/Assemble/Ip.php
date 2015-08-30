<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Area
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Ip.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 地区数据
 *
 * @name ZtChart_Model_Assemble_Ip
 */
class ZtChart_Model_Assemble_Ip {
    
    /**
     * 
     */
    const IP_FILE = 'ip.dat';
    
    /**
     * 
     */
    const IP_LENGTH = 9;
    
    /**
     * 
     * @staticvar Zend_Cache_Core
     */
    static protected $_cache;
    
    /**
     * 
     * @staticvar string
     */
    static protected $_datapath;
    
    /**
     * 
     * @var ZtChart_Model_DbTable_Ip
     */
    protected $_ipDAO = null;
    
    /**
     * 设置缓存对象
     *
     * @static
     * @param Zend_Cache_Core $cache
     */
    static public function setCache($cache) {
        self::$_cache = $cache;
    }
    
    /**
     * 取得缓存对象
     *
     * @static
     * @return Zend_Cache_Core
     */
    static public function getCache() {
        return self::$_cache;
    }
    
    /**
     * 设置IP数据文件目录
     * 
     * @param string $datapath
     */
    static public function setDataPath($datapath) {
        self::$_datapath = $datapath;
    }
    
    /**
     * 取得IP数据文件目录
     * 
     * @return string
     */
    static public function getDataPath() {
        return self::$_datapath;
    }
    
    /**
     * 初始化IP文件
     * 
     * @param boolean $init
     */
    public function __construct($init = false) {
        $ipDAO = new ZtChart_Model_DbTable_Ip();
        if ($init && !file_exists($this->getIpFile())) {
            $fp = fopen($this->getIpFile(), 'ab');
            foreach ($ipDAO->fetchAll()->toArray() as $ip) {
                $entry = pack('L', $ip['ip_begin_long']) . pack('L', $ip['ip_end_long']) . pack('C', $ip['ip_areaid']);
                fwrite($fp, $entry);
            }
            fclose($fp);
        }
        $this->_ipDAO = $ipDAO;
    }
    
    /**
     * 根据IP取得地区
     * 
     * @param string|integer $ip
     * @return integer
     */
    public function getArea($ip) {
        if (is_string($ip) && !is_numeric($ip)) {
            $ip = ip2long($ip);
        }
        if (false === ($area = self::$_cache->load($ip))) {
            self::$_cache->save($area = $this->_seekIp($ip), $ip);
        }
        
        return $area;
    }
    
    
    /**
     * 取得IP数据文件路径
     * 
     * @return string
     */
    public function getIpFile() {
        return self::getDataPath() . DIRECTORY_SEPARATOR . self::IP_FILE;
    }
    
    /**
     * 搜索IP，使用二分查找。
     * 
     * @param integer $ip
     * @return integer
     */
    protected function _seekIp($ip) {
        $begin = 0;
        $end = filesize($this->getIpFile());
        $fp = fopen($this->getIpFile(), 'r');
        while (true) {
            if ($end - $begin <= self::IP_LENGTH) {
                fseek($fp, $begin + self::IP_LENGTH - 1);
                
                return current(unpack('C', fread($fp, 1)));
            }
            $middle = ceil((($end - $begin) / self::IP_LENGTH) / 2) * self::IP_LENGTH + $begin;
            fseek($fp, $middle);
            if ($ip >= current(unpack('L', fread($fp, 4)))) {
                $begin = $middle;
            } else {
                $end = $middle;
            }
        }
        
        return 0;
    }
}