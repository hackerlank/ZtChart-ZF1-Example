<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_View
 * @subpackage ZtChart_Model_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: DatePicker.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局视图助手——日期时间选择
 *
 * @name ZtChart_Model_View_Helper_JQuery_DatePicker
 * @see ZendX_JQuery_View_Helper_DatePicker
 */
class ZtChart_Model_View_Helper_JQuery_DatePicker extends ZendX_JQuery_View_Helper_DatePicker {

    /**
     * Create a jQuery UI Widget Date Picker
     *
     * @link   http://docs.jquery.com/UI/Datepicker
     * @param  string $id
     * @param  string $value
     * @param  array  $params jQuery Widget Parameters
     * @param  array  $attribs HTML Element Attributes
     * @return string
     */
    public function datePicker($id, $value = null, array $params = array(), array $attribs = array())
    {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);
    
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            if (!isset($params['dateFormat'])) {
                $params['dateFormat'] = self::resolveZendLocaleToDatePickerFormat();
            }
             
            $days = Zend_Locale::getTranslationList('Days');
            if (!isset($params['dayNames'])) {
                $params['dayNames'] = array_values($days['format']['wide']);
            }
            if (!isset($params['dayNamesShort'])) {
                $params['dayNamesShort'] = array_values($days['format']['abbreviated']);
            }
            if (!isset($params['dayNamesMin'])) {
                $params['dayNamesMin'] = array_values($days['stand-alone']['narrow']);
            }
             
            $months = Zend_Locale::getTranslationList('Months');
            if (!isset($params['monthNames'])) {
                $params['monthNames'] = array_values($months['stand-alone']['wide']);
            }
            if (!isset($params['monthNamesShort'])) {
                $params['monthNamesShort'] = array_values($months['stand-alone']['narrow']);
            }
        }
    
        // TODO: Allow translation of DatePicker Text Values to get this action from client to server
        $params = ZendX_JQuery::encodeJson($params);
    
        $js = sprintf('%s("#%s").datepicker(%s);',
                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
                $attribs['id'],
                $params
        );
    
        $this->jquery->addOnLoad($js);
    
        return $this->view->formText($id, $value, $attribs);
    }
    
    /**
     * Helps with building the correct Attributes Array structure.
     *
     * @param String $id
     * @param String $value
     * @param Array $attribs
     * @return Array $attribs
     */
    protected function _prepareAttributes($id, $value, $attribs)
    {
        if(!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }
        $attribs['name']  = isset($attribs['name']) ? $attribs['name'] : $id;
        $attribs['value'] = (string) $value;
    
        return $attribs;
    }
}