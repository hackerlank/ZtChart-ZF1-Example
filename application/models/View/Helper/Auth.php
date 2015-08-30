<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_View
 * @subpackage ZtChart_Model_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Auth.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局视图助手——登陆信息显示
 *
 * @name ZtChart_Model_View_Helper_Auth
 * @see Zend_View_Helper_Abstract
 */
class ZtChart_Model_View_Helper_Auth extends Zend_View_Helper_Abstract {

    /**
     * 取得相关的登陆信息
     * 
     * @param string $key
     * @return string
     */
    public function auth($key = 'display_name') {
        $auth = Zend_Auth::getInstance();
        if ($auth->getIdentity()) {
            return $auth->getIdentity()->$key;
        }
    }
}