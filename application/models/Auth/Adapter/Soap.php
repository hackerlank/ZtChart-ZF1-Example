<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Auth
 * @subpackage ZtChart_Model_Auth_Adapter
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Soap.php 35718 2012-06-27 04:03:53Z zhangweiwen $
 */

/**
 * 登陆验证适配器——通过SOAP验证
 *
 * @name ZtChart_Model_Auth_Adapter_Soap
 */
class ZtChart_Model_Auth_Adapter_Soap implements Zend_Auth_Adapter_Interface {

    /**
     * 
     * @staticvar string
     */
    static protected $_login = null;
    
    /**
     * 
     * @staticvar string
     */
    static protected $_defaultWsdl = null; 
    
    /**
     * 
     * @var integer
     */
    protected $_soapVersion = SOAP_1_1;
    
    /**
     * 
     * @var string
     */
    protected $_wsdl = '';
    
    /**
     * 
     * @var string
     */
    protected $_account;
    
    /**
     * 
     * @var string
     */
    protected $_password;
    
    /**
     * IP地址
     *
     * @var string
     */
    protected $_remoteIp;
    
    /**
     * 真实IP地址，不是代理服务器地址。
     *
     * @var string
     */
    protected $_realRemoteIp;
    
    /**
     * 端口号
     *
     * @var string
     */
    protected $_remotePort;
    
    /**
     * 单点登陆系统中对应本系统的标示符
     *
     * @var string
     */
    protected $_code = 'kpi';
    
    /**
     * 
     * @static
     * @param string $login
     */
    static public function setLogin($login) {
        self::$_login = $login;
    }
    
    /**
     * 
     * @static
     * @return string
     */
    static public function getLogin() {
        return self::$_login;
    }
    
    /**
     * 
     * @static
     * @param string $wsdl
     */
    static public function setDefaultWsdl($wsdl) {
        self::$_defaultWsdl = $wsdl;
    }
    
    /**
     * 
     * @static
     * @return string
     */
    static public function getDefaultWsdl() {
        return self::$_defaultWsdl;
    }
    
    /**
     * 
     * @param string $wsdl
     * @param string $account
     * @param string $password
     * @param string $remoteIp
     * @param string $realRemoteIp
     * @param string $remotePort
     * @param string $code
     */
    public function __construct($wsdl = null, $account = null, $password = null, $remoteIp = null, 
                                $realRemoteIp = null, $remotePort = null, $code = null) {
        if (null != $wsdl) {
            $this->_wsdl = $wsdl;
        }
        
        if (null != $account) {
            $this->setAccount($account);
        }
        if (null != $password) {
            $this->setPassword($password);
        }
        
        if (null != $remoteIp) {
            $this->setRemoteIp($remoteIp);
        }
        
        if (null != $realRemoteIp) {
            $this->setRealRemoteIp($realRemoteIp);
        }
        
        if (null != $remotePort) {
            $this->setRemotePort($remotePort);
        }
        
        if (null != $code) {
            $this->setCode($code);
        }
    }
    
    /**
     * 通过SOAP方式验证帐号
     * 
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        try {
            $soap = $this->_soapClient();
            $result = $soap->ValidateAdByPasswd($this->_account, $this->_password, $this->_remoteIp, 
                    $this->_realRemoteIp, $this->_remotePort, $this->_code);
            if (!$result->return_flag) {
                $authMessage[] = $result->return_remark;
                $authResult = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null, $authMessage);
            } else {
                $identity = (object) $soap->QueryAdUserInfoByName($this->_account);
                $authResult = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
            }
        } catch (Zend_Soap_Client_Exception $e) {
            throw new Zend_Auth_Adapter_Exception('Soap adapter error: ' . $e->getMessage());
        } catch (SoapFault $e) {
            throw new Zend_Auth_Adapter_Exception('Soap adapter error: ' . $e->getMessage());
        }
        return $authResult;
    }
    
    /**
     * 根据域帐号名查找域账号信息
     *
     * @param string $account 域帐号名
     * @param string $key 需要返回的域帐号信息字段
     * @return mixed
     */
    public function searchAccount($account, $key = null) {
        if (!empty($account)) {
            try {
                $soap = $this->_soapClient();
                $adUser = $soap->QueryAdUserInfoByName($account);
                if (is_object($adUser) && property_exists($adUser, 'dn')) {
                    return null === $key ? $adUser : $adUser->$key;
                }
            } catch (Zend_Soap_Client_Exception $e) {
                throw $e;
            }
        }
        
        return false;
    }
    
    /**
     * 根据用户姓名查找域账号信息
     *
     * @param string $displayName 用户姓名
     * @param string $key 需要返回的域帐号信息字段
     * @return array
     */
    public function searchDisplayName($displayName, $key = null) {
        $result = array();
        try {
            $soap = $this->_soapClient();
            $adUser = $soap->QueryAdUserPartInfoByMuti('', $displayName);
            if (is_object($adUser)) {
                $userMainInfo = $adUser->user_main_info;
                if (is_object($userMainInfo)) {
                    $userMainInfo = array($userMainInfo);
                }
                foreach ($userMainInfo as $userInfo) {
                    $result[$userInfo->user_name] = null === $key ? $userInfo : $userInfo->$key;
                }
            }
        } catch (Zend_Soap_Client_Exception $e) {
            throw $e;
        }
    
        return $result;
    }
    
    /**
     * 
     * @return Zend_Soap_Client
     */
    protected function _soapClient() {
        $wsdl = empty($this->_wsdl) ? self::$_defaultWsdl : $this->_wsdl;
        $context = stream_context_create(array('http' => array(
                'protocol_version'=> '1.0', 'header'=> 'Content-Type: text/xml;')));
        $soapClient = new Zend_Soap_Client($wsdl, array(
                'soap_version' => $this->_soapVersion, 'stream_context' => $context));
        return $soapClient;
    }
    
    /**
     * @return string
     */
    public function getAccount() {
        return $this->_account;
    }

    /**
     * @param string $account
     */
    public function setAccount($account) {
        $this->_account = $account;
    }
    
    /**
     * @return string
     */
    public function getPassword() {
        return $this->_password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {
        $this->_password = $password;
    }
    
    /**
     *
     * @return string
     */
    public function getRemoteIp() {
        return $this->_remoteIp;
    }
    
    /**
     *
     * @param string $remoteIp
     */
    public function setRemoteIp($remoteIp) {
        $this->_remoteIp = $remoteIp;
    }
    
    /**
     *
     * @return string
     */
    public function getRealRemoteIp() {
        return $this->_realRemoteIp;
    }
    
    /**
     *
     * @param string $realRemoteIp
     */
    public function setRealRemoteIp($realRemoteIp) {
        $this->_realRemoteIp = $realRemoteIp;
    }
    
    /**
     *
     * @return string
     */
    public function getRemotePort() {
        return $this->_remotePort;
    }
    
    /**
     *
     * @param string $remotePort
     */
    public function setRemotePort($remotePort) {
        $this->_remotePort = $remotePort;
    }
    
    /**
     *
     * @return string
     */
    public function getCode() {
        return $this->_code;
    }
    
    /**
     *
     * @param string $code
     */
    public function setCode($code) {
        $this->_code = $code;
    }
}