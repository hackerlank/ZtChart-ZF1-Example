<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @subpackage ZtChart_Model_Monitor_Log
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Usertrade.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * Usertrade日志分析类
 *
 * @see ZtChart_Model_Monitor_Log_Abstract
 * @name ZtChart_Model_Monitor_Log_Usertrade
 */
class ZtChart_Model_Monitor_Log_Usertrade extends ZtChart_Model_Monitor_Log_Abstract {

    /**
     * 共享内存标识符
     */
    const SHM_IDENTIFIER = 3;
    
    /**
     * 日志内容标识符
     */
    const LOG_IDENTIFIER = 'usertrade';
    
    /**
     * 分析Usertrade的日志
     * 
     * @see ZtChart_Model_Monitor_Log_Abstract::_analyze()
     */
    protected function _analyze($data) {
        $line = null;
        if (strpos($data, 'INFO:') === 0) {
            $info = explode(',', substr($data, strrpos($data, ':') + 1));
            if (count($info) >= 8) {
                if (0 == $info[2]) {
                    switch (trim($info[0])) {
                        case '网页充值卡':
                        case '游戏充值卡':
                            $line = array(
                                array(
                                    'account' => trim($info[3]),
                                    'clientip' => ip2long(trim($info[10])),
                                    'optype' => trim($info[4]),
                                    'gametype' => trim($info[5]),
                                    'netbank' => 0,
                                    'point' => trim($info[8])
                                )
                            );
                            break;
                        case '网页消耗':
                        case '游戏消耗':
                        case '游戏赠点':
                            $line = array(
                                array(
                                    'account' => trim($info[3]),
                                    'clientip' => ip2long(trim($info[13])),
                                    'optype' => trim($info[4]),
                                    'gametype' => trim($info[5]),
                                    'netbank' => 0,
                                    'point' => trim($info[10])
                                ), 
                                array(
                                    'account' => trim($info[3]),
                                    'clientip' => ip2long(trim($info[13])),
                                    'optype' => trim($info[4]),
                                    'gametype' => trim($info[5]),
                                    'netbank' => 1,
                                    'point' => trim($info[9])
                                )
                            );
                            break;
                        case '网页充值':
                        case '游戏直充':
                            $line = array(
                                array(
                                    'account' => trim($info[3]),
                                    'clientip' => ip2long(trim($info[12])),
                                    'optype' => trim($info[4]),
                                    'gametype' => trim($info[5]),
                                    'netbank' => trim($info[11]),
                                    'point' => trim($info[8])
                                )
                            );
                            break;
                    }
                }
            } else {
                throw new ZtChart_Model_Monitor_Log_Exception('Log info format error.');
            }
        }
        
        return $line;
    }
    
    /**
     * 统计Usertrade的日志
     *
     * @see ZtChart_Model_Monitor_Log_Abstract::_stats()
     */
    protected function _stats($data) {
        try {
            $lineInfo = $this->_analyze($data);
        } catch (ZtChart_Model_Monitor_Log_Exception $e) {
            return false;
        }
        if (empty($lineInfo)) {
            return false;
        }
        $stats = array();
        foreach (array('_area' => '_statsArea', '_account' => '_statsAccount') as $suffix => $method) {
            $stats[self::LOG_IDENTIFIER . $suffix] = array();
            foreach ($lineInfo as $line) {
                $stats[self::LOG_IDENTIFIER . $suffix] += call_user_func(array($this, $method), $line);
            }
        }
        
        return $stats;
    }
    
    /**
     * 按地区统计
     * 
     * @param array $lineInfo
     * @return array
     */
    protected function _statsArea($lineInfo) {
        $info = array($lineInfo['optype'], $lineInfo['gametype'], $lineInfo['netbank'], $this->_ip2area($lineInfo['clientip']));
        
        return array($this->_join($info) => $lineInfo['point']);
    }
    
    /**
     * 按账号统计
     * 
     * @param array $lineInfo
     * @return array
     */
    protected function _statsAccount($lineInfo) {
        $info = array($lineInfo['account'], $lineInfo['optype'], $lineInfo['gametype'], $lineInfo['netbank']);
        
        return array($this->_join($info) => $lineInfo['point']);
    }
}