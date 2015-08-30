<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @subpackage ZtChart_Model_Monitor_Log
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Flserver.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * Flserver日志分析类
 *
 * @see ZtChart_Model_Monitor_Log_Abstract
 * @name ZtChart_Model_Monitor_Log_Flserver
 */
class ZtChart_Model_Monitor_Log_Flserver extends ZtChart_Model_Monitor_Log_Abstract {

    /**
     * 共享内存标识符
     */
    const SHM_IDENTIFIER = 1;
    
    /**
     * 日志内容标识符
     */
    const LOG_IDENTIFIER = 'flserver';
    
    /**
     * 日志行项目数
     */
    const LOG_ITMEMS = 12;
    
    /**
     * 分析Flserver的日志
     * 
     * @see ZtChart_Model_Monitor_Log_Abstract::_analyze()
     */
    protected function _analyze($data) {
        $line = null;
        if (strpos($data, 'INFO: 登陆成功') === 0) {
            $info = explode(',', substr($data, strrpos($data, ':') + 1));
            if (static::LOG_ITMEMS == count($info)) {
                $line = array(
                    array(
                        'uid' => trim($info[0]),
                        'clientip' => ip2long(trim($info[2])),
                        'gametype' => trim($info[7])
                    )
                );
            } else {
                throw new ZtChart_Model_Monitor_Log_Exception('Log info format error.');
            }
        }
            
        return $line;
    }
    
    /**
     * 统计Flserver的日志
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
        foreach (array('_ip' => '_statsIp', 
                       '_area' => '_statsArea', 
                       '_account' => '_statsAccount') as $suffix => $method) {
            $stats[self::LOG_IDENTIFIER . $suffix] = call_user_func(array($this, $method), $lineInfo[0]);
        }
        
        return $stats;
    }
    
    /**
     * 按地区统计
     * 
     * @param array $lineInfo
     * @return array
     */
    private function _statsArea($lineInfo) {
        $info = array($lineInfo['gametype'], $this->_ip2area($lineInfo['clientip']));
        
        return array($this->_join($info) => 1);
    }
    
    /**
     * 按IP统计
     * 
     * @param array $lineInfo
     * @return array
     */
    private function _statsIp($lineInfo) {
        $info = array($lineInfo['gametype'], $lineInfo['clientip']);
        
        return array($this->_join($info) => 1);
    }
    
    /**
     * 按账号统计
     * 
     * @param array $lineInfo
     * @return array
     */
    private function _statsAccount($lineInfo) {
        $info = array($lineInfo['gametype'], $lineInfo['uid']);
        
        return array($this->_join($info) => 1);
    }
}