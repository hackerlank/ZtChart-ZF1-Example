<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model_Storage
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Storage.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 文件存储管理，用于一些中间数据的保存。
 *
 * @name ZtChart_Model_Storage
 */
class ZtChart_Model_Storage {

    /**
     * 根目录
     * 
     * @static
     * @var string
     */
    static protected $_rootDir;
    
    /**
     * 
     * @var string
     */
    protected $_path;
    
    /**
     * 设置根目录
     * 
     * @static
     * @param string $rootDir
     * @return void
     */
    static public function setRootDirectory($rootDir) {
        if (!is_readable($rootDir) || !is_writable($rootDir)) {
            throw new ZtChart_Model_Storage_Exception('The root directory cannot be access.');
        }
        self::$_rootDir = $rootDir;
    }
    
    /**
     * 
     * @param string $directory
     * @param string $file
     */
    public function __construct($directory, $filename = 'data') {
        $this->_path = self::$_rootDir . DIRECTORY_SEPARATOR . $directory;
        if (!is_dir($this->_path)) {
            
            mkdir($this->_path, 0755, true);
        } 
        $this->_path .= DIRECTORY_SEPARATOR . $filename;
    }
    
    /**
     * 读取内容
     * 
     * @return mixed
     */
    public function read() {
        if (!file_exists($this->_path)) {
            touch($this->_path);
        }
        $content = file_get_contents($this->_path);
        try {
            $data = Zend_Serializer::unserialize($content);
        } catch (Zend_Serializer_Exception $e) {
            $data = $content;
        }
        
        return $data;
    }
    
    /**
     * 保存内容
     * 
     * @param mixed $data
     * @return boolean
     */
    public function write($data) {
        if (!is_scalar($data)) {
            $data = Zend_Serializer::serialize($data);
        }
        
        return file_put_contents($this->_path, $data);
    }
    
    /**
     * 追加内容
     * 
     * @param mixed $data
     * @param string $separator
     * @return boolean
     */
    public function append($data, $separator = PHP_EOL) {
        if (!is_scalar($data)) {
            $data = serialize($data);
        }
        
        return file_put_contents($this->_path, $data . $separator, FILE_APPEND);
    }
}