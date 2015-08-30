<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 日志数据监控抽象类
 *
 * @abstract
 * @name ZtChart_Model_Monitor_Daemon_Abstract
 */
abstract class ZtChart_Model_Monitor_Daemon_Abstract {

    /**
     * 
     * @staticvar Zend_Db_Adapter_Pdo_Abstract
     */
    static protected $_defaultDb = null;
    
    /**
     * 
     * @var string
     */
    static protected $_nfsDirectory = '';
    
    /**
     * 
     * @var array
     */
    protected $_filePattern = array('flserver*.log.*', 'hlserver*.log.*', '*user[Tt]rade.log.*');
    
    /**
     * 
     * @var Zend_Db_Adapter_Pdo_Abstract
     */
    protected $_db = null;
    
    /**
     * 
     * @var Zend_Log
     */
    protected $_logger = null;
    
    /**
     * 
     * @var ZtChart_Model_Monitor_Console
     */
    protected $_console = null;
    
    /**
     * 
     * @static
     * @param Zend_Db_Adapter_Pdo_Abstract $db
     */
    static public function setDefaultDb(Zend_Db_Adapter_Pdo_Abstract $db) {
        self::$_defaultDb = $db;
    }
    
    /**
     * 
     * @static
     * @param string $nfsDirectory
     */
    static public function setNfsDirectory($nfsDirectory) {
        self::$_nfsDirectory = $nfsDirectory;
    }
    
    /**
     * 
     * @static
     * @return string
     */
    static public function getNfsDirectory() {
        return self::$_nfsDirectory;
    }
    
    /**
     * 
     * @param ZtChart_Model_Monitor_Console $console
     * @param array $config
     */
    public function __construct(ZtChart_Model_Monitor_Console $console, $config = array()) {
        $this->_console = $console;
        
        foreach ($config as $name => $value) {
            $method = 'set' . ucfirst($name);
            if (method_exists($this, $method)) {
                $method($value);
            }
        }
        if (null === $this->_db) {
            $this->_db = self::$_defaultDb;
        }
        if (null === $this->_logger) {
            $this->_logger = Zend_Log::factory(array(
                array(
                    'writerName' => 'Stream', 
                    'writerParams' => array(
                        'stream' => 'php://stderr'
                    ), 
                    'filterName' => 'Priority', 
                    'filterParams' => array(
                        'priority' => Zend_Log::ERR, 
                        'operator' => '<='
                    )
                ), 
                array(
                    'writerName' => 'Stream',
                    'writerParams' => array(
                        'stream' => 'php://stdout'
                    ),
                    'filterName' => 'Priority',
                    'filterParams' => array(
                        'priority' => Zend_Log::INFO
                    )
                )
            ));
        }
    }
    
    /**
     * 判断日志文件是否符合条件
     * 
     * @param string $filename
     * @return boolean
     */
    public function accept($filename) {
        if (null !== $this->_filePattern) {
            foreach ($this->_filePattern as $pattern) {
                if (fnmatch($pattern, $filename)) {
                    return true;
                }
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * 检查文件大小
     * 
     * @param string $filename
     * @param integer $size
     * @return boolean
     */
    public function checkSize($filename, $size = 104857600) {
        clearstatcache();
        
        return filesize($filename) > $size;
    }
    
    /**
     * 设置需要处理的日志文件命名模式
     * 
     * @param array $pattern
     * @return void
     */
    public function setFilePattern($pattern) {
        $this->_filePattern = $pattern;
    }
    
    /**
     * 设置数据库适配器
     * 
     * @param Zend_Db_Adapter_Pdo_Abstract $db
     * @return void
     */
    public function setDb(Zend_Db_Adapter_Pdo_Abstract $db) {
        $this->_db = $db;
    }
    
    /**
     * 设置日志记录器
     * 
     * @param Zend_Log $logger
     * @return void
     */
    public function setLogger(Zend_Log $logger) {
        $this->_logger = $logger;
    }
    
    /**
     * 执行SQL语句
     * 
     * @param string|Zend_Db_Select $sql
     * @param boolean $commit
     * @return integer
     */
    protected function _exec($sql, $commit = true) {
        $affected = $this->_db->exec($sql);
        if ($commit) {
            $this->_db->exec('COMMIT');
        }
        
        return $affected;
    }
    
    /**
     * 取得通过NFS方式挂载的文件路径
     * 
     * @param string $filename
     * @return string
     */
    protected function _nfsfile($filename) {
        return self::getNfsDirectory() . DIRECTORY_SEPARATOR . basename($filename);
    }
    
    /**
     * 创建唯一的临时文件
     * 
     * @param string $identifier
     * @param integer $mode
     * @return string
     */
    protected function _tmpfile($identifier, $mode = 0777) {
        $tmpdir = self::getNfsDirectory() ?: sys_get_temp_dir();
        $tmpfile = tempnam($tmpdir, $identifier);
        chmod($tmpfile, $mode);
        
        return $tmpfile;
    }
    
    /**
     * 过滤文件中无效的行，返回一个临时的文件。
     * 
     * @param string $filename
     * @return string
     */
    protected function _sed($filename) {
        $sedfile = $this->_tmpfile('');
        exec("sed -e '/ERROR/d' {$filename} > {$sedfile}");
        
        return $sedfile;
    }
    
    /**
     * 运行守护进程
     * 
     * @return void
     */
    abstract public function run();
}