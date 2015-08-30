<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: ProxyController.php 37416 2012-11-16 14:17:21Z zhangweiwen $
 */

/**
 * 服务模块的代理控制器
 *
 * @name Ws_ProxyController
 * @see Zend_Controller_Action
 */
class Ws_ProxyController extends Zend_Controller_Action {

    /**
     * 
     * @see Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        
    }
    
    /**
     * 账号验证
     */
    public function loginAction()
    {
        if (!$this->_hasParam('token')) {
            $authAdapter = $this->getInvokeArg('bootstrap')->getResource('Auth');
            $authAdapter->setAccount($this->_getParam('uname'));
            $authAdapter->setPassword($this->_getParam('pwd'));
            $authAdapter->setRemoteIp($this->_request->getClientIp());
            $authAdapter->setRealRemoteIp($this->_request->getClientIp(false));
            $authAdapter->setRemotePort($this->_request->getServer('REMOTE_PORT'));
            $authResult = $authAdapter->authenticate();
            if ($authResult->isValid()) {
                try {
                    $user = new ZtChart_Model_User($this->_getParam('uname'));
                    if ($user->isActive()) {
                        $this->_helper->json(array('res' => true, 'token' => $user->getTokenring(true)));
                    } else {
                        $this->_helper->json(array('res' => false, 'error' => '不是本系统用户'));
                    }
                } catch (ZtChart_Model_User_Exception $e) {
                    
                }
            } else {
                $this->_helper->json(array('res' => false, 'error' => '用户名密码错误', 'exception' => $authResult->getMessages()));
            }
        } else {
            $user = new ZtChart_Model_User($this->_getParam('uname'));
            if ($user->isActive() && $user->getTokenring() == $this->_getParam('token')) {
                $this->_helper->json(array('res' => true, 'token' => $user->getTokenring()));
            } else {
                $this->_helper->json(array('res' => false, 'error' => '令牌错误'));
            }
        }
    }
    
    /**
     * 取得所有游戏类型
     */
    public function gameAction()
    {
        $gameTypes = array();
        
        $user = new ZtChart_Model_User($this->_getParam('uname'));
        if ($user->isActive() && $user->getTokenring() == $this->_getParam('token')) {
            $allowedGameTypes = $user->getRole()->getGameTypes(true);
            
            // 取趣乐的游戏类型
            $qule = new ZtChart_Model_Qule();
            foreach ($qule->getGametype() as $gameType) {
                if (in_array($gameType['game_code'], $allowedGameTypes)) {
                    if (!is_integer($gameType['game_code'])) {
                        $gameType['game_code'] = (int) $gameType['game_code'];
                    }
                    $gameTypes[] = $gameType;
                }
            }
            
            // 取InfoServer的游戏类型
            $infoServerGameTypes = ZtChart_Model_DbTable_Infoserver::getInfoserverGameTypes();
            foreach (ZtChart_Model_GameType::getLongNames() as $gameType => $gameName) {
                if (in_array($gameType, $allowedGameTypes) 
                        && in_array($gameType, $infoServerGameTypes) 
                        && !ZtChart_Model_GameType::isQule($gameType)) {
                    $gameTypes[] = array('game_code' => $gameType, 'game_name' => $gameName);
                }
            }
        } 
        
        $this->_helper->json($gameTypes);
    }
    
    /**
     * 取得游戏的所有区信息
     */
    public function gamezoneAction()
    {
        $zones = array();
        
        $user = new ZtChart_Model_User($this->_getParam('uname'));
        if ($user->isActive() && $user->getTokenring() == $this->_getParam('token')) {
            $allowedGameTypes = $user->getRole()->getGameTypes(true);
            if (in_array($this->_getParam('gametype'), $allowedGameTypes)) {
                if (ZtChart_Model_GameType::isQule($this->_getParam('gametype'))) {
                    
                    // 取趣乐的游戏区
                    $qule = new ZtChart_Model_Qule();
                    $zones = $qule->getGamezone($this->_getParam('gametype'));
                } else {
                    
                    // TODO 取InfoServer的游戏区
                }
            }
        } 
        
        $this->_helper->json($zones);
    }
    
    /**
     * 
     * @deprecated
     */
    public function gamedataAction()
    {
        $data = array();
        
        $user = new ZtChart_Model_User($this->_getParam('uname'));
        if ($user->isActive() && $user->getTokenring() == $this->_getParam('token')) {
            $allowedGameTypes = $user->getRole()->getGameTypes(true);
            if (in_array($this->_getParam('gametype'), $allowedGameTypes)) {
                $data = Zend_Json::decode(file_get_contents('http://192.168.102.203:6989/?getnumber&gametype=' 
                                    . $this->_getParam('gametype') . '&zoneid=' . $this->_getParam('zoneid') . '&first=' . $this->_getParam('first')));
            }
        } 
        
        $this->_helper->json($data);
    }
}