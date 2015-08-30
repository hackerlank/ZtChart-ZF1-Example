<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_View
 * @subpackage ZtChart_Model_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Dialog.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局视图助手——弹出对话框信息
 *
 * @name ZtChart_Model_View_Helper_JQuery_Dialog
 * @see ZendX_JQuery_View_Helper_DialogContainer
 */
class ZtChart_Model_View_Helper_JQuery_Dialog extends ZendX_JQuery_View_Helper_DialogContainer {

    /**
     * 
     * @var array
     */
    protected $_params = array(
        'modal' => true, 
        'resizable' => false, 
        'draggable' => false
    );
    
    /**
     * 
     * @var array
     */
    protected $_attribs = array(
        'style' => 'display: none'
    );
    
    /**
     * 
     * @var array
     */
    protected $_context = array(
                'beforeSubmit' => "$('form').submit(function(e) { %s\n e.preventDefault(); })"
            );
    
    /**
     *
     */
    public function __construct() {
        $this->_params['buttons'] = array(
            array('text' => '确定', 
                  'click' => new Zend_Json_Expr('function(event, ui) { $(this).dialog("close"); }')
            )
        );
    }
    
    /**
     * 弹出警告信息
     * 
     * @param string $text 显示的文本
     * @param string $title 显示的标题
     * @param string|array $closeUrl 关闭对话框后跳转的地址
     * @param string $context 上下文
     * @return string
     */
    public function dialog($text = '', $title = '', $closeUrl = null, $context = null) {
        if (empty($text)) {
            return;
        }
        if (!empty($closeUrl)) {
            if (is_array($closeUrl) && 'parent' == key($closeUrl)) {
                $closeUrl = current($closeUrl);
                $this->_params['close'] = new Zend_Json_Expr(
                        "function(event, ui) { parent.window.location.href = '{$closeUrl}'; }");
            } else {
                $this->_params['close'] = new Zend_Json_Expr(
                        "function(event, ui) { location.href = '{$closeUrl}'; }");
            }
        }
        $html =  $this->dialogContainer('dialog', $text, $this->_params, array('title' => $title) + $this->_attribs);
        
        if (!empty($context)) {
            if (array_key_exists($context, $this->_context)) {
                $context = $this->_context[$context];
            }
            $onLoadActions = $this->jquery->getOnLoadActions();
            $this->jquery->clearOnLoadActions()->addOnLoad(sprintf($context, $onLoadActions[0]));
        }
        
        return $html;
    }
}