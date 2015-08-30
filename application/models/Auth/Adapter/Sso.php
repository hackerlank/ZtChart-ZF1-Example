<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Auth
 * @subpackage ZtChart_Model_Auth_Adapter
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Sso.php 35718 2012-06-27 04:03:53Z zhangweiwen $
 */

/**
 * 登陆验证适配器——通过SSO验证
 *
 * @name ZtChart_Model_Auth_Adapter_Sso
 */
class ZtChart_Model_Auth_Adapter_Sso implements Zend_Auth_Adapter_Interface {

    /**
     * 单点登陆地址
     * 
     * @staticvar string
     */
    static protected $_login = null;
    
    /**
     * 默认的获取域帐号信息的WSDL文件地址
     * 
     * @staticvar string
     */
    static protected $_defaultWsdl = null; 
    
    /**
     * 使用的SOAP版本
     * 
     * @var integer
     */
    protected $_soapVersion = SOAP_1_1;
    
    /**
     * 获取域帐号信息的WSDL文件地址
     * 
     * @var string
     */
    protected $_wsdl = '';
    
    /**
     * 用户名
     * 
     * @var string
     */
    protected $_username;
    
    /**
     * 令牌
     * 
     * @var string
     */
    protected $_token;
    
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
    protected $_code = 'recruit';
    
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
     * 构造函数，初始化单点登陆验证所需要的一些参数。
     * 
     * @param string $wsdl
     * @param string $username
     * @param string $token
     * @param string $remoteIp
     * @param string $realRemoteIp
     * @param string $remotePort
     * @param string $code
     */
    public function __construct($wsdl = null, $username = null, $token = null, $remoteIp = null, 
                                $realRemoteIp = null, $remotePort = null, $code = null) {
        $this->_wsdl = empty($wsdl) ? self::$_defaultWsdl : $wsdl;
        
        if (null != $username) {
            $this->setUsername($username);
        }
        
        if (null != $token) {
            $this->setToken($token);
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
     * 通过SSO方式验证帐号
     * 
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        try {
            $soap = $this->_soapClient();
            $result = $soap->ValidateAdByToken($this->_username, $this->_token, $this->_remoteIp, 
                                                        $this->_realRemoteIp, $this->_remotePort, $this->_code);
            if (!$result->return_flag) {
                $authMessage[] = $result->return_remark;
                $authResult = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null, $authMessage);
            } else {
                $identity = (object) $soap->QueryAdUserInfoByName($this->_username);
                $authResult = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
                setcookie('user_name', $this->_username, $result->cookie_expire, $result->cookie_path,
                            $result->cookie_domain, $result->cookie_secure);
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
     * 初始化SOAP协议的Client对象
     * 
     * @return Zend_Soap_Client
     */
    protected function _soapClient() {
        $context = stream_context_create(array('http' => array(
                'protocol_version'=> '1.0', 'header'=> 'Content-Type: text/xml;')));
        $soapClient = new Zend_Soap_Client($this->_wsdl, array(
                'soap_version' => $this->_soapVersion, 'stream_context' => $context));
        return $soapClient;
    }
    
    /**
     * 
     * @return string
     */
    public function getUsername() {
        return $this->_username;
    }

    /**
     * 
     * @param string $username
     */
    public function setUsername($username) {
        $this->_username = $username;
    }

    /**
     * 
     * @return string
     */
    public function getToken() {
        return $this->_token;
    }

    /**
     * 
     * @param string $token
     */
    public function setToken($token) {
        $this->_token = $token;
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