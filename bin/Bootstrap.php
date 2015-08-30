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
 * 定义了全局的环境配置
 *
 * @name Bootstrap
 * @see Zend_Application_Bootstrap_Bootstrap
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * 初始化类的命名空间
     *
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload() {
        return new Zend_Application_Module_Autoloader(array(
            'namespace' => 'ZtChart',
            'basePath' => APPLICATION_PATH
        ));
    }
    
    /**
     * 初始化使用Infobright数据库的类
     */
    protected function _initInfobright() {
        $infobright = $this->bootstrap('multidb')->getResource('multidb')->getDb('infobright');
        
        ZtChart_Model_Monitor_Daemon_Abstract::setDefaultDb($infobright);
        ZtChart_Model_Monitor_Daemon_Abstract::setNfsDirectory('/var/tmp');
    }
    
    /**
     * 初始化数据文件存储目录
     */
    protected function _initStorage() {
        ZtChart_Model_Storage::setRootDirectory(realpath(__DIR__ . '/../storage'));
    }
    
    /**
     * 初始化IP数据的目录
     */
    protected function _initIpDataPath() {
        ZtChart_Model_Assemble_Ip::setDataPath(realpath(__DIR__ . '/../data'));
    }
}

