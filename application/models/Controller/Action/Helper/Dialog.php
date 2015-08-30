<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Controller
 * @subpackage ZtChart_Model_Controller_Action_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Dialog.php 36808 2012-09-07 07:56:04Z zhangweiwen $
 */

/**
 * 动作助手——记录弹出对话框显示的内容
 *
 * @name ZtChart_Model_Controller_Action_Helper_Dialog
 * @see Zend_Controller_Action_Helper_Abstract
 */
class ZtChart_Model_Controller_Action_Helper_Dialog extends Zend_Controller_Action_Helper_Abstract {
    
    /**
     * 
     * @staticvar array
     */
    static public $priorities = array(
        Zend_Log::EMERG   => '紧急',
        Zend_Log::ALERT   => '警报',
        Zend_Log::CRIT    => '致命',
        Zend_Log::ERR     => '错误',
        Zend_Log::WARN    => '提醒',
        Zend_Log::NOTICE  => '注意',
        Zend_Log::INFO    => '信息',
        Zend_Log::DEBUG   => '调试');
    
    /**
     * @var Zend_Layout
     */
    protected $_layout;
    
    /**
     * Get layout object
     *
     * @return Zend_Layout
     */
    public function getLayoutInstance()
    {
        if (null === $this->_layout) {
            /**
             * @see Zend_Layout
             */
            require_once 'Zend/Layout.php';
            if (null === ($this->_layout = Zend_Layout::getMvcInstance())) {
                $this->_layout = new Zend_Layout();
            }
        }
    
        return $this->_layout;
    }
    
    /**
     * 显示对话框
     *
     * @param string $text
     * @param string|integer $title
     * @param string|array $closeUrl
     * @param string $context
     * @return void
     */
    public function popupDialog($text, $title = null, $closeUrl = null, $context = null) {
        if (is_integer($title) && array_key_exists($title, self::$priorities)) {
            $title = self::$priorities[$title];
        }
        $layout = $this->getLayoutInstance();
        $layout->assign('dialog', $layout->getView()->dialog($text, $title, $closeUrl, $context));
    }
    
    /**
     * 直接执行命令
     *
     * @param string $text
     * @param string|integer $title
     * @param string|array $closeUrl
     * @param string $context
     * @return void
     */
    public function direct($text, $title = null, $closeUrl = null, $context = null) {
        $this->popupDialog($text, $title, $closeUrl, $context);
    }
}