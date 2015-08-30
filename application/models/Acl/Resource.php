<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Acl
 * @subpackage ZtChart_Model_Acl_Resource
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Resource.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 资源数据管理
 *
 * @name ZtChart_Model_Acl_Resource
 */
class ZtChart_Model_Acl_Resource implements Zend_Acl_Resource_Interface {

    /**
     * 
     */
    const SEPARATOR = ':';
    
    /**
     * 
     * @var ZtChart_Model_Db_Table_Row
     */
    protected $_resourceRow = null;
    
/**
     * 根据Uri字符串生成资源字符串
     * 
     * @static
     * @param string $uri
     * @param Zend_Controller_Router_Rewrite $router
     * @return string
     */
    static public function parseUri($uri, $router = null) {
        if (null === $router) {
            $router = Zend_Controller_Front::getInstance()->getRouter();
        }
        
        return self::parseHttpRequest($router->route(new Zend_Controller_Request_Http($uri)));
    }
    
    /**
     * 解析Uri字符串，取出其中的资源。
     * 
     * @static
     * @param string $uri
     * @param Zend_Controller_Router_Rewrite $router
     * @return ZtChart_Model_Acl_Resource
     */
    static public function fromUri($uri, $router = null) {
        return new self(self::parseUri($uri));
    }
    
    /**
     * 根据动作控制器参数生成资源字符串
     * 
     * @param string $action
     * @param string $controller
     * @param string $module
     * @return string
     */
    static public function parsePageMvc($action, $controller, $module) {
        $mvc = array($module, $controller, $action);
        
        return implode(self::SEPARATOR, array_filter($mvc));
    }
    
    /**
     * 根据Zend_Controller_Request_Http对象生成资源字符串
     * 
     * @param Zend_Controller_Request_Http $request
     * @return string
     */
    static public function parseHttpRequest(Zend_Controller_Request_Http $request) {
        return self::parsePageMvc($request->getActionName(), 
                                    $request->getControllerName(), $request->getModuleName());
    }
    
    /**
     * 解析Zend_Controller_Request_Http对象，取出其中的资源。
     * 
     * @static
     * @param Zend_Controller_Request_Http $request
     * @return ZtChart_Model_Acl_Resource
     */
    static public function fromHttpRequest(Zend_Controller_Request_Http $request) {
        return new self(self::parseHttpRequest($request));
    }
    
    /**
     * 根据Zend_Navigation_Page对象生成资源字符串
     * 
     * @static
     * @param Zend_Navigation_Page $page
     * @return string
     */
    static public function parseNavigationPage(Zend_Navigation_Page $page) {
        if ($page instanceof Zend_Navigation_Page_Mvc) {
            $mvc = array($page->getModule(), $page->getController(), $page->getAction());
                
            return implode(self::SEPARATOR, array_filter($mvc));
        } else if ($page instanceof Zend_Navigation_Page_Uri) {
            return self::parseUri($page->getUri());
        }
        
        return $page->getResource();
    }
    
    /**
     * 解析Zend_Navigation_Page对象，取出其中的资源。
     * 
     * @static
     * @param Zend_Navigation_Page $page
     * @return ZtChart_Model_Acl_Resource
     */
    static public function fromNavigationPage(Zend_Navigation_Page $page) {
        return new self(self::parseNavigationPage($page));
    }
    
    /**
     * 
     * @param integer|string|ZtChart_Model_Db_Table_Row $resource
     */
    public function __construct($resource = null) {
        if (null !== $resource) {
            if (!$resource instanceof ZtChart_Model_Db_Table_Row) {
                $resourceDAO = new ZtChart_Model_DbTable_Resource();
                if (is_numeric($resource)) {
                    $resource = $resourceDAO->fetchRow($resource);
                } else if (is_string($resource)) {
                    $resource = $resourceDAO->fetchRow(array('resource_mvc = ?' => $resource));
                } 
            }
            $this->_resourceRow = $resource;
        }
    }
    
    /**
     * 
     * @see Zend_Acl_Resource_interface::getResourceId()
     */
    public function getResourceId() {
        return (string) $this->_resourceRow ? $this->_resourceRow->resource_id : 0;
    }
    
    /**
     * 取得资源字符串
     * 
     * @return string
     */
    public function getResourceMvc() {
        return $this->_resourceRow ? $this->_resourceRow->resource_mvc : '';
    }
    
    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->getResourceId();
    }
}