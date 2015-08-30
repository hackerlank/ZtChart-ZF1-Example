<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: LoginController.php 36811 2012-09-07 08:00:51Z zhangweiwen $
 */

/**
 * 用户登陆控制器
 *
 * @name LoginController
 * @see Zend_Controller_Action
 */
class LoginController extends Zend_Controller_Action
{
    /**
     * 已经登录则跳转
     */
    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/');
        }
    }
    
    /**
     * 用户登录
     */
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $authAdapter = $this->getInvokeArg('bootstrap')->getResource('auth');
            if ($authAdapter instanceof ZtChart_Model_Auth_Adapter_Sso) {
                $this->_redirect(ZtChart_Model_Auth_Adapter_Sso::getLogin());
            } else if ($authAdapter instanceof ZtChart_Model_Auth_Adapter_Soap) {
                $this->_forward('soap');
            }
        } 
    }
    
    /**
     * 在SSO登录后，回调此页面。
     */
    public function ssoAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $authAdapter = new ZtChart_Model_Auth_Adapter_Sso();
            $authAdapter->setRemoteIp($this->_request->getClientIp());
            $authAdapter->setRealRemoteIp($this->_request->getClientIp(false));
            $authAdapter->setRemotePort($this->_request->getServer('REMOTE_PORT'));
            $authAdapter->setToken($this->_request->getCookie('token'));
            $authAdapter->setUsername($this->_request->getCookie('user_name'));
            
            if ($auth->authenticate($authAdapter)->isValid()) {
                $session = new Zend_Session_Namespace($auth->getStorage()->getNamespace());
                $this->_redirect($session->referer, array('prependBase' => false));
            } else {
                $this->_redirect('/');
            }
        } 
    } 
    
    /**
     * 使用SOAP方式登录
     */
    public function soapAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            if ($this->_request->isPost()) {
                $authAdapter = $this->getInvokeArg('bootstrap')->getResource('Auth');
                $authAdapter->setAccount($this->_request->getPost('account'));
                $authAdapter->setPassword($this->_request->getPost('pwd'));
                $authAdapter->setRemoteIp($this->_request->getClientIp());
                $authAdapter->setRealRemoteIp($this->_request->getClientIp(false));
                $authAdapter->setRemotePort($this->_request->getServer('REMOTE_PORT'));
                if ($auth->authenticate($authAdapter)->isValid()) {
                    $this->getFrontController()->registerPlugin(new ZtChart_Plugin_Home());
                } else {
                    $this->_helper->dialog('用户名密码错误', Zend_Log::NOTICE);
                }
            } else {
                $this->_redirect('/');
            }
        } 
    }
}