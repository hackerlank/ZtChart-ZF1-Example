<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Action.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 动作控制器
 *
 * @abstract
 * @name ZtChart_Model_Controller_Action
 * @see Zend_Controller_Action
 */
abstract class ZtChart_Model_Controller_Action extends Zend_Controller_Action {
    
    /**
     * 跳转到其他动作控制器，并修改请求中的MVC相关参数。
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @return void
     */
    public function _goto($action, $controller = null, $module = null, array $params = null) {
        $this->_setParam($this->_request->getActionKey(), $action);
        
        if (null !== $controller) {
            $this->_setParam($this->_request->getControllerKey(), $controller);
        }
        if (null !== $module) {
            $this->_setParam($this->_request->getModuleKey(), $module);
        }
        
        parent::_forward($action, $controller,  $module, $params);
    }
}