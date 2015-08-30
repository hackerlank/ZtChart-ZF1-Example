<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Ws_Model_Tokenring
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Tokenring.php 37416 2012-11-16 14:17:21Z zhangweiwen $
 */

/**
 * 服务模块的令牌管理
 *
 * @name ZtChart_Ws_Model_Tokenring
 */
class ZtChart_Ws_Model_Tokenring {
    
    /**
     * Session中的命名空间
     */
    const TOKENRING_NS = '';
    
    /**
     * 
     * @var Zend_Session_Namespace
     */
    protected $_session;
    
    /**
     * 
     * @var string
     */
    protected $_account;
    
    /**
     * 
     * @param string $account
     */
    public function __construct($account) {
        $session = new Zend_Session_Namespace(self::TOKENRING_NS);
        if (!isset($session->{$account}) || empty($session->{$account})) {
            $session->{$account} = md5(microtime() . $account);
        }
        $this->_session = $session;
        
        $this->_account = $account;
    }
    
    /**
     * 令牌是否有效
     * 
     * @param string $tokenring
     * @return boolean
     */
    public function isValid($tokenring) {
        return $this->_session->{$this->_account} == $tokenring;
    }
    
    /**
     * 取得令牌
     * 
     * @return string
     */
    public function getTokenring() {
        return $this->_session->{$this->_account};
    }
}