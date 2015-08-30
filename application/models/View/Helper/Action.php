<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_View
 * @subpackage ZtChart_Model_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Action.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局视图助手——动作控制器内容显示
 *
 * @name ZtChart_Model_View_Helper_Action
 * @see Zend_View_Helper_Action
 */
class ZtChart_Model_View_Helper_Action extends Zend_View_Helper_Action {
    
    /**
     * 显示动作控制器的内容
     * 
     * @see Zend_View_Helper_Action::action()
     * @param  string $action
     * @param  string $controller
     * @param  string $module Defaults to default module
     * @param  array|null $params
     * @param  boolean $accept
     * @return string
     */
    public function action($action, $controller, $module = null, array $params = array(), $accept = false) {
        if (false !== $accept) {
            if (!$this->view->navigation()->mvcMenu()
                                ->acceptMVC($action, $controller, $module, $params, true)) {
                return '';
            }
        }
        if ($params == array(null)) {
            $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        }
        
        return parent::action($action, $controller, $module, $params);
    }
}