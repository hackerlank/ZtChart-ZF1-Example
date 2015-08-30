<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Profile_View
 * @subpackage Profile_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Money.php 35722 2012-06-27 04:04:25Z zhangweiwen $
 */

/**
 * 视图助手——点数换算为金钱显示
 *
 * @name Profile_View_Helper_Money
 * @see Zend_View_Helper_Abstract
 */
class Profile_View_Helper_Money extends Zend_View_Helper_Abstract {

    /**
     * 
     */
    const FACTOR = 0.0089;
    
    /**
     * 计算金额
     * 
     * @param integer $point
     * @return integer
     */
    public function money($point) {
        $currency = new Zend_Currency();
        
        return $currency->toCurrency(round(self::FACTOR * $point, 2));
    }
}