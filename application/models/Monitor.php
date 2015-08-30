<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Monitor.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 日志数据监控类
 *
 * @name ZtChart_Model_Monitor
 */
class ZtChart_Model_Monitor {

    /**
     * 
     * @var ZtChart_Model_Monitor_Abstract
     */
    protected $_daemon = null;
    
    /**
     * 构造函数
     * 
     * @param string $daemon
     * @param array|Zend_Config $config
     * @param ZtChart_Model_Monitor_Console $console
     */
    public function __construct($daemon, $config = array(), ZtChart_Model_Monitor_Console $console = null) {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }
        if (null === $console) {
            $console = ZtChart_Model_Monitor_Console::getInstance();
        }
        $loader = new Zend_Loader_PluginLoader(
            array(
                'ZtChart_Model_Monitor_Daemon' => realpath(__DIR__ . '/Monitor/Daemon')
            )
        );
        if (false === ($daemonClass = $loader->load($daemon, false))) {
            throw new ZtChart_Model_Monitor_Exception("Specified daemon class '{$daemon}' could not be found.");
        } else if (!is_subclass_of($daemonClass, 'ZtChart_Model_Monitor_Daemon_Abstract')) {
            throw new ZtChart_Model_Monitor_Exception("Specified daemon class '{$daemon}' is illegal.");
        } else {
            $this->_daemon = new $daemonClass($console, $config);
        }
    }
    
    /**
     * 守护进程
     * 
     * @return void
     */
    public function daemon() {
        ini_set('memory_limit', -1);
        $this->_daemon->run();
        ini_restore('memory_limit');
    }
    
    /**
     * 设置守护进程的参数
     * 
     * @param string $name
     * @param mixed $value
     */
    public function setDaemonConfig($name, $value) {
        $method = 'set' . ucfirst($name);
        if (method_exists($this->_daemon, $method)) {
            call_user_func(array($this->_daemon, $method), $value);
        }
    }
}