<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package Resources
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Qule.php 37175 2012-09-27 08:01:55Z zhangweiwen $
 */

/**
 * 资源初始化类——趣乐平台
 *
 * @final
 * @name Resources_Qule
 * @see Zend_Application_Resource_ResourceAbstract
 */
final class Resources_Qule extends Zend_Application_Resource_ResourceAbstract {

    /**
     * 
     * @var string
     */
    protected $_defaultUrl = null;
    
    /**
     *
     * @var array
     */
    protected $_params = array();
    
    /**
     * 初试化趣乐平台
     * 
     * @return void
     */
    public function init() {
        ZtChart_Model_Qule::setDefaultUri($this->_defaultUrl);
        ZtChart_Model_Qule::setMethods($this->_params);
        
        return new ZtChart_Model_Qule();
    }
    
    /**
     * 
     * @param string $url
     * @return Resources_Qule
     */
    public function setDefaultUrl($url) {
        $this->_defaultUrl = $url;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getDefaultUrl() {
        return $this->_defaultUrl;
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
}