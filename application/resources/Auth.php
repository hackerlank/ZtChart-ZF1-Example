<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package Resources
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Auth.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 资源初始化类——授权管理
 *
 * @final
 * @name Resources_Auth
 * @see Zend_Application_Resource_ResourceAbstract
 */
final class Resources_Auth extends Zend_Application_Resource_ResourceAbstract {

    /**
     * 
     * @var string
     */
    protected $_adapter = null;
    
    /**
     *
     * @var array
     */
    protected $_params = array();
    
    /**
     * 
     * @var array
     */
    protected $_anonymous = array();
    
    /**
     * 初试化Zend_Auth所需适配器的参数
     * 
     * @return Zend_Auth_Adapter_Interface
     */
    public function init() {
        $authAdapter = $this->_getAdapter();
        if (method_exists($authAdapter, 'setParams')) {
            $authAdapter->setParams($this->_params);
        } else {
            $this->_initParams($authAdapter, $this->_params);
        }
        $this->_initAnonymous($this->_anonymous);
        
        return $authAdapter;
    }
    
    /**
     * 初试化属性
     * 
     * @param array $params
     * @param Zend_Auth_Adapter_Interface $adapter
     * @return void
     */
    protected function _initParams(Zend_Auth_Adapter_Interface $adapter, array $params = array()) {
        if (empty($params)) {
            $params = $this->_params;
        }
        $class = new ReflectionClass(get_class($adapter));
        foreach ($params as $name => $value) {
            $method = 'set' . ucfirst($name);
            if ($class->hasMethod($method)) {
                $adapter->$method($value);
            }
        }
    }
    
    /**
     * 初始化匿名访问者
     * 
     * @param array $anonymous
     * @return void
     */
    protected function _initAnonymous($anonymous) {
        foreach ((array) $anonymous as $row) {
            ZtChart_Plugin_Auth::addAnonymous($row);
        }
    }
    
    /**
     * 取得Auth适配器
     * 
     * @param string $adapter
     * @return Zend_Auth_Adapter_Interface
     */
    protected function _getAdapter($adapter = null) {
        if (empty($adapter)) {
            $adapter = $this->_adapter;
        }
        
        $pluginLoader = new Zend_Loader_PluginLoader(
            array(
                'ZtChart_Model_Auth_Adapter_' => realpath(__DIR__ . '/../models/Auth/Adapter'), 
                'Zend_Auth_Adapter_' => 'Zend/Auth/Adapter'
            )
        );
        if (false === ($adapterClass = $pluginLoader->load($adapter, false))) {
            throw new Zend_Application_Resource_Exception("Specified Auth Adapter '{$adapter}' could not be found");
        }
        
        return new $adapterClass();
    }
    
    /**
     * 
     * @param string $adapter
     * @return Resources_Auth
     */
    public function setAdapter($adapter) {
        $this->_adapter = $adapter;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getAdapter() {
        return $this->_adapter;
    }
    
    /**
     * 
     * @param  array $params
     * @return Resources_Auth
     */
    public function setParams(array $params) {
        $this->_params = $params;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getParams() {
        return $this->_params;
    }
    
    /**
     * 
     * @param array $anonymous
     */
    public function setAnonymous(array $anonymous) {
        $this->_anonymous = $anonymous;
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function getAnonymous() {
        return $this->_anonymous;
    }
}