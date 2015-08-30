<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Bootstrap
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Bootstrap.php 37708 2012-12-17 08:26:09Z zhangweiwen $
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
     * 初始化Zend_Navigation
     */
    protected function _initNaviation() {
        $navgiation = new ZtChart_Model_Navigation(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        
        // 注册全局导航
        Zend_Registry::set('Zend_Navigation', $navgiation);
        
        // 初始化导航视图助手
        $view = $this->bootstrap('view')->getResource('view');
        $view->getHelper('navigation')->navigation($navgiation);
        
        return $navgiation;
    }
    
    /**
     * 初始化Zend_Paginator
     */
    protected function _initPaginator() {
        Zend_Paginator::setDefaultItemCountPerPage(15);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
    
        $view = $this->bootstrap('view')->getResource('view');
        $view->addScriptPath(realpath(__DIR__) . '/layouts');
    }
    
    /**
     * 初始化ZtChart_Model_Logger
     */
    protected function _initLogger() {
        $log = $this->bootstrap('Log')->getResource('Log');
        ZtChart_Model_Logger::setLogger($log);
    }
    
    /*
     * 初始化动作助手目录
    */
    protected function _initActionHelperPaths() {
        Zend_Controller_Action_HelperBroker::addPath(realpath(__DIR__) . '/models/Controller/Action/Helper', 
                                                        'ZtChart_Model_Controller_Action_Helper');
    }
    
    /**
     * 初始化全局的视图助手目录
     */
    protected function _initViewHelperPath() {
        $view = $this->bootstrap('view')->getResource('view');
        $view->addHelperPath(realpath(__DIR__) . '/models/View/Helper', 'ZtChart_Model_View_Helper');
        $view->addHelperPath(realpath(__DIR__) . '/models/View/Helper/Navigation', 'ZtChart_Model_View_Helper_Navigation');
        if ($this->hasPluginResource('jQuery')) {
            $this->bootstrap('jQuery');
            $view->addHelperPath(realpath(__DIR__) . '/models/View/Helper/JQuery', 'ZtChart_Model_View_Helper_JQuery');
        }
    }
    
    /**
     * 初始化使用Infobright数据库的数据表类
     */
    protected function _initInfobright() {
        $infobright = $this->bootstrap('multidb')->getResource('multidb')->getDb('infobright');
        
        ZtChart_Model_DbTable_Flserver::setDefaultTableAdapter($infobright);
        ZtChart_Model_DbTable_Usertrade::setDefaultTableAdapter($infobright);
    }
    
    /**
     * 初始化使用Infoserver系列数据库的数据表类
     */
    protected function _initInfoserver() {
        $options = $this->bootstrap('multidb')->getResource('multidb')->getOptions();
        $ic = new ZtChart_Model_Identification_Config($options['infoserver']['identification']);
        foreach ($ic->getAllInfoServerDb(true) as $gameType => $entries) {
            foreach ($entries as $entry) {
                ZtChart_Model_DbTable_Infoserver::addInfoserverConfig(array_merge($options['infoserver'], $entry), $gameType);
            }
        }
    }
    
    /**
     * 初始化匿名用户
     */
    protected function _initUser() {
        $frontController = $this->bootstrap('frontController')->getResource('frontController');
        if (!$frontController->hasPlugin('ZtChart_Plugin_Auth')) {
            ZtChart_Model_User::setAnonymousRole(ZtChart_Model_Role::ADMIN);
        }
        Zend_Registry::set('user', ZtChart_Model_User::getAnonymous());
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

