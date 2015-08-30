<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Exception.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局自定义异常类
 *
 * @name ZtChart_Model_Exception
 */
class ZtChart_Model_Exception extends Zend_Exception {
    
    /**
     * 
     */
    const SESSION_NAMESPACE = 'ZtChart_Model_Exception';
    
    /**
     * 持久化异常信息
     * 
     * @return void
     */
    public function persistent() {
        $session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        $session->message = $this->getMessage();
    }
    
    /**
     * 是否有持久化的异常信息
     * 
     * @static
     * @return boolean
     */
    static public function hasPersistentMessage() {
        $session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        
        return $session->__isset('message');
    }
    
    /**
     * 取出持久化的异常信息
     * 
     * @static
     * @return string
     */
    static public function getPersistentMessage() {
        $session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        
        return $session->message;
    }
    
    /**
     * 清除所有持久化的异常信息
     * 
     * @static
     * @return boolean
     */
    static public function clearPersistentMessage() {
        $session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        
        return $session->unsetAll();
    }
}