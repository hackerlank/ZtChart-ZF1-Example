<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Controller
 * @subpackage ZtChart_Model_Controller_Action_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: InboxContext.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 动作助手——对话框表单上下文
 *
 * @name ZtChart_Model_Controller_Action_Helper_InboxContext
 * @see Zend_Controller_Action_Helper_ContextSwitch
 */
class ZtChart_Model_Controller_Action_Helper_InboxContext extends Zend_Controller_Action_Helper_ContextSwitch
{
    /**
     * Controller property to utilize for context switching
     * @var string
     */
    protected $_contextKey = 'inboxable';

    /**
     * Constructor
     *
     * Add HTML context
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->addContext('inbox', array('suffix' => 'inbox'));
    }
    
/**
     * 设置对话框布局
     *
     * @param  string $format
     * @return void
     */
    public function initContext($format = null) {
        $this->_currentContext = null;
        $this->setAutoDisableLayout(false);
        $this->setCallback('inbox', self::TRIGGER_POST, '_callbackPost');

        return parent::initContext($format);
    }
    
    /**
     * 
     */
    public function _callbackPost() {
        $layout = Zend_Layout::getMvcInstance();
        if ($layout->isEnabled()) {
            $layout->setLayout('inbox');
        }
        ZtChart_Model_Layout_Controller_Plugin_Layout::resetModuleLayout();
    }
}
