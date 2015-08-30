<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package Controller
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: OnlineController.php 37708 2012-12-17 08:26:09Z zhangweiwen $
 */

/**
 * 服务模块的在线人数控制器
 *
 * @name Ws_OnlineController
 * @see Zend_Controller_Action
 */
 
class Ws_OnlineController extends Zend_Controller_Action {
    
    /**
     * 
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_setParam('allowedGameTypes', array());
        
        try {
            $user = new ZtChart_Model_User($this->_getParam('uname', ''));
            if ($user->isActive() && $user->getTokenring() == $this->_getParam('token')) {
                $this->_setParam('allowedGameTypes', array_intersect(explode(',', $this->_getParam('gametype')), $user->getRole()->getGameTypes(true)));
            } 
        } catch (ZtChart_Model_User_Exception $e) {
            
        }
    }
    
    /**
     * 当前在线人数
     */
    public function nowAction()
    {
        $data = array();
        
        // 当前时间戳
        $nowTimestamp = Zend_Date::now()->subMinute(1)->setSecond(0)->getTimestamp(); 
        // 同比时间戳
        $sameTimestamp = ZtChart_Model_Assemble_Datetime::getPredefinedStartTimestamp(
                            $this->_getParam('pdate', ZtChart_Model_Assemble_Datetime::RECENT_24HOUR), $nowTimestamp); 
        foreach ($this->_getParam('allowedGameTypes') as $gameType) {
            $now = $same = null;
            
            if (ZtChart_Model_GameType::isQule($gameType)) {
                
                // 取趣乐游戏的数据
                $qule = new ZtChart_Model_Qule();
                
                // 取当前的数据
                $nowData = $qule->getGamedata(array(
                                                'gametype' => $gameType, 
                                                'zoneid' => $this->_getParam('zoneid'),
                                                'querydate' => $nowTimestamp, 
                                                'loadnumber' => 1));
                if (!empty($nowData)) {
                    $now = $nowData['data'][0][1];
                    
                    // 取同比数据
                    $sameData = $qule->getGamedata(array(
                                                    'gametype' => $gameType,
                                                    'zoneid' => $this->_getParam('zoneid'),
                                                    'querydate' => $sameTimestamp,
                                                    'loadnumber' => 1));
                    if (!empty($sameData)) {
                        $same = $sameData['data'][0][1];
                    }
                } 
            } else {
                
                // 取InfoServer中的数据
                try {
                    if (ZtChart_Model_DbTable_Infoserver::isSingleAdapter($gameType)) {
                        
                        // 游戏只有一个数据库的情况
                        $infoserver = ZtChart_Model_DbTable_Infoserver::factory($gameType);
                        $now = $infoserver->fetchSum($nowTimestamp);
                        $infoserver->setTablename($sameTimestamp);
                        $same = $infoserver->fetchSum($sameTimestamp);
                    } else {
                        // @todo 游戏含有多个数据库的情况
                    }
                } catch (Zend_Db_Exception $e) {
                    ZtChart_Model_Logger::err($e->getMessage());
                    continue;
                }
            }
            $data[$gameType] = array(
                    'gamename' => ZtChart_Model_GameType::getLongName($gameType),
                    'datetime' => $nowTimestamp,
                    'number' => $now == null ? null : $now,
                    'delta' => ($now == null || $same == null) ? null : $now - $same,
                    'percentage' => ($now == null || $same == null) ? null : @round($data[$gameType]['delta'] / $now * 100, 2));
        }
        
        $this->_helper->json($data);
    }
    
    /**
     * 在线人数概况
     */
    public function profileAction()
    {
        $data = array();
        
        // 当前时间戳
        $nowTimestamp = Zend_Date::now()->setSecond(0)->getTimestamp();
        // 同比时间戳
        $sameTimestamp = ZtChart_Model_Assemble_Datetime::getPredefinedStartTimestamp(
                $this->_getParam('pdate', ZtChart_Model_Assemble_Datetime::RECENT_24HOUR), $nowTimestamp);
        
        foreach ($this->_getParam('allowedGameTypes') as $gameType) {
            if (ZtChart_Model_GameType::isQule($gameType)) {
                
                // 取Qule游戏的数据
                $qule = new ZtChart_Model_Qule();
                
                // 取今天的数据
                $nowData = $qule->getGamedata(array(
                                                'gametype' => $gameType, 
                                                'zoneid' => $this->_getParam('zoneid'), 
                                                'loadnumber' => 1));
                if (!empty($nowData)) {
                    $data['today'] = array('pcu' => $nowData['max'], 'acu' => round($nowData['avg']), 'bcu' => $nowData['min']);
                }
                
                // 取同比的数据
                $sameData = $qule->getGamedata(array(
                                                'gametype' => $gameType, 
                                                'zoneid' => $this->_getParam('zoneid'), 
                                                'loadnumber' => 1, 
                                                'querydate' => $sameTimestamp));
                if (!empty($sameData)) {
                    $data['yestoday'] = array('pcu' => $sameData['max'], 'acu' => round($sameData['avg']), 'bcu' => $sameData['min']);
                }
            } else {
                
                // 取InfoServer中的数据
                try {
                    if (ZtChart_Model_DbTable_Infoserver::isSingleAdapter($gameType)) {
                        
                        // 游戏只有一个数据库的情况
                        $infoserver = ZtChart_Model_DbTable_Infoserver::factory($gameType);
                            
                        // 取今天的数据
                        $data['today'] = array('pcu' => $infoserver->fetchMax(), 'acu' => round($infoserver->fetchAvg()), 'bcu' => $infoserver->fetchMin());
                            
                        // 取同比的数据
                        $infoserver->setTablename($sameTimestamp);
                        $data['yestoday'] = array('pcu' => $infoserver->fetchMax(), 'acu' => round($infoserver->fetchAvg()), 'bcu' => $infoserver->fetchMin());
                    } else {
                        // @todo 游戏含有多个数据库的情况
                    }
                } catch (Zend_Db_Exception $e) {
                    ZtChart_Model_Logger::err($e->getMessage());
                    continue;
                }
            }
        }
        
        $this->_helper->json($data);
    }
    
    /**
     * 在线人数曲线
     */
    public function chartAction()
    {
        $data = array();
        
        // 当前时间戳
        $nowOffsetTimestamp = Zend_Date::now()->subMinute(abs($this->_getParam('offset')))->getTimestamp();
        
        foreach ($this->_getParam('allowedGameTypes') as $gameType) {
            if (ZtChart_Model_GameType::isQule($gameType)) {
                
                // 取Qule游戏的数据
                $qule = new ZtChart_Model_Qule();
                
                // 取当前偏移量的数据
                $nowData = $qule->getGamedata(array(
                                                'gametype' => $gameType, 
                                                'zoneid' => $this->_getParam('zoneid'), 
                                                'loadnumber' => $this->_getParam('range'), 
                                                'querydate' => $nowOffsetTimestamp));
                if (array_key_exists('data', $nowData) && !empty($nowData['data'])) {
                    foreach ($nowData['data'] as $item) {
                        $data['datetime'][] = $item[0];
                        $data['data'][0][] = $item[1];
                    } 
                }
                
                // 取同比偏移量的数据
                if ($this->_hasParam('pdate')) {
                    foreach (explode(',', $this->_getParam('pdate')) as $pdate) {
                        $sameOffsetTimestamp = ZtChart_Model_Assemble_Datetime::getPredefinedStartTimestamp($pdate, $nowOffsetTimestamp);
                        $sameData = $qule->getGamedata(array(
                                                        'gametype' => $gameType, 
                                                        'zoneid' => $this->_getParam('zoneid'), 
                                                        'loadnumber' => $this->_getParam('range'), 
                                                        'querydate' => $sameOffsetTimestamp));
                        if (array_key_exists('data', $sameData) && !empty($sameData['data'])) {
                            foreach ($sameData['data'] as $item) {
                                $data['data'][$pdate][] = $item[1];
                            }
                        }
                    }
                }
            } else {
                
                // 取InfoServer中的数据
                try {
                    if (ZtChart_Model_DbTable_Infoserver::isSingleAdapter($gameType)) {
                        
                        // 游戏只有一个数据库的情况
                        $infoserver = ZtChart_Model_DbTable_Infoserver::factory($gameType);
                            
                        // 取当前偏移量的数据
                        $nowOffsetDate = new Zend_Date($nowOffsetTimestamp);
                        $startNowOffsetTimestamp = $nowOffsetDate->subMinute($this->_getParam('range'))->getTimestamp();
                        for ($i = 0; $i < $this->_getParam('range'); $i++) {
                            $data['datetime'][$i] = $startNowOffsetTimestamp + $i * 60;
                            $data['tmp'][$i] = date('H:i', $data['datetime'][$i]);
                        }
                        foreach ($infoserver->fetchSumGroup($startNowOffsetTimestamp) as $item) {
                            if (in_array(date('H:i', $item[0]), $data['tmp'])) {
                                $data['data'][0][] = $item[1];
                            } else {
                                $data['data'][0][] = -1;
                            }
                        }
                        $data['data'][0] = array_pad($data['data'][0], $this->_getParam('range'), -1);
                            
                        // 取同比偏移量的数据
                        if ($this->_hasParam('pdate')) {
                            foreach (explode(',', $this->_getParam('pdate')) as $pdate) {
                                $startSameOffsetTimestamp = ZtChart_Model_Assemble_Datetime::getPredefinedStartTimestamp($pdate, $startNowOffsetTimestamp);
                                $infoserver->setTablename($startSameOffsetTimestamp);
                                foreach ($infoserver->fetchSumGroup($startSameOffsetTimestamp, $this->_getParam('range')) as $item) {
                                    if (in_array(date('H:i', $item[0]), $data['tmp'])) {
                                        $data['data'][$pdate][] = $item[1];
                                    } else {
                                        $data['data'][$pdate][] = -1;
                                    }
                                }
                                $data['data'][$pdate] = array_pad($data['data'][$pdate], $this->_getParam('range'), -1);
                            }
                        }
                        unset($data['tmp']);
                    } else {
                        // @todo 游戏含有多个数据库的情况
                    }
                } catch (Zend_Db_Exception $e) {
                    ZtChart_Model_Logger::err($e->getMessage());
                    continue;
                }
            }
        }
            
        $this->_helper->json($data);
    }
}