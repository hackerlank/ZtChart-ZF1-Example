<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Plugin_Layout
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Layout.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 动作插件——布局控制
 *
 * @see Zend_Layout_Controller_Plugin_Layout
 * @name ZtChart_Plugin_Layout
 */
class ZtChart_Model_Layout_Controller_Plugin_Layout extends Zend_Layout_Controller_Plugin_Layout {

    /**
     * 
     * @staticvar array
     */
    static protected $_moduleLayout = array();
    
    /**
     * 重置模块布局
     * 
     * @static
     * @returo void
     */
    static public function resetModuleLayout() {
        self::$_moduleLayout = array();
    }
    
    /**
     * 设置模块布局
     * 
     * @static
     * @param array $moduleLayout
     * @return void
     */
    static public function setModuleLayout($moduleLayout) {
        self::$_moduleLayout = $moduleLayout;
    }
    
    /**
     * 添加模块布局
     * 
     * @static
     * @param string $module
     * @param string $layout
     * @return void
     */
    static public function addModuleLayout($module, $layout = null) {
        self::$_moduleLayout[$module] = $layout ?: $module;
    }
    
    /**
     * 移除模块布局
     * 
     * @param string $module
     * @return void
     */
    static public function removeModuleLayout($module) {
        if (self::hasModuleLayout($module)) {
            unset(self::$_moduleLayout[$module]);
        }
    }
    
    /**
     * 模块是否存在布局
     * 
     * @param string $module
     * @return boolean
     */
    public function hasModuleLayout($module) {
        return array_key_exists($module, self::$_moduleLayout) && !empty(self::$_moduleLayout[$module]);
    }
    
    /**
     * 加载模块布局
     * 
     * @see Zend_Layout_Controller_Plugin_Layout::postDispatch()
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        if (($this->_layout->getMvcSuccessfulActionOnly() && (!empty($this->_layoutActionHelper) 
                    && !$this->_layoutActionHelper->isActionControllerSuccessful()))
            || !$this->_layout->isEnabled() || !$request->isDispatched() || $this->_response->isRedirect()) {
            
            return;
        }
        if ($this->hasModuleLayout($module = $request->getModuleName())) {
            $content = $this->_response->getBody(true);
            if (isset($content['default'])) {
                $content[$this->_layout->getContentKey()] = $content['default'];
            }
            if ('default' != $this->_layout->getContentKey()) {
                unset($content['default']);
            }
            $this->_layout->assign($content); 
            
            try {
                $this->_response->setBody($this->_layout->render(self::$_moduleLayout[$module]));
            } catch (Exception $e) {
                $this->_response->setBody(null);
                throw $e;
            }
        }
        
        parent::postDispatch($request);
    }
}