<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Kpi_View
 * @subpackage Kpi_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: RadioDate.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 视图助手——时间选择
 *
 * @name Kpi_View_Helper_RadioDate
 * @see Zend_View_Helper_FormRadio
 */
class Kpi_View_Helper_RadioDate extends Zend_View_Helper_FormRadio {
    
    /**
     * 产生时间选择表单
     * 
     * @param string $name
     * @param string $name1
     * @param string $name2
     * @param array $options
     * @return string
     */
    public function radioDate($name, $name1, $name2, $options = array(
                        ZtChart_Model_Assemble_Datetime::RECENT_24HOUR, 
                        ZtChart_Model_Assemble_Datetime::RECENT_48HOUR, 
                        ZtChart_Model_Assemble_Datetime::RECENT_1WEEK, 
                        ZtChart_Model_Assemble_Datetime::RECENT_1MONTH, 
                        ZtChart_Model_Assemble_Datetime::CUSTOM)) {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        $value = $request->getQuery($name, $options[0]);
        $options = array_map(function($str) { return ' ' . $str; }, 
                        ZtChart_Model_Assemble_Datetime::getPredefinedDatetimes($options));
        $html = $this->formRadio($name, $value, null, $options, PHP_EOL);
        $params = array('maxDate' => date('Y-m-d'));
        $attribs = array('class' => 'dateInput', 'readonly' => 'readonly');
        if (!empty($value)) {
            $attribs['disabled'] = 'disabled';
        }
        if (array_key_exists(ZtChart_Model_Assemble_Datetime::CUSTOM, $options)) {
            $html .= '<span class="spanInput">' . PHP_EOL
                . $this->view->datePicker($name1, $request->getQuery($name1), $params, $attribs) . PHP_EOL
                . '-'
                . $this->view->datePicker($name2, $request->getQuery($name2), $params, $attribs) . PHP_EOL
                . '</span>' . PHP_EOL;
            $html .= '<button class="btn6" type="submit">确定</button>';
        }
        
        return $html;
    }
}