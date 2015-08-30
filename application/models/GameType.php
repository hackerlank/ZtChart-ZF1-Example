<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_GameType
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: GameType.php 37337 2012-11-06 19:42:02Z zhangweiwen $
 */

/**
 * 游戏类型
 *
 * @name ZtChart_Model_GameType
 */
class ZtChart_Model_GameType {
    
    /**
     * 趣乐游戏开始代码
     */
    const QULE = 1025;
    
    /**
     * 
     * @staticvar array
     */
    static protected $_games = array();
    
    /**
     * 
     * @staticvar array
     */
    static protected $_shortNames = array();
    
    /**
     * 取得所有游戏列表
     * 
     * @see ZtChart_Model_GameType::getLongNames()
     * @static
     * @param integer $offset
     * @return array
     */
    static public function getGames($offset = 0) {
        return self::getLongNames($offset);
    }
    
    /**
     * 取得所有游戏列表
     * 
     * @static
     * @param integer $offset
     * @return array
     */
    static public function getLongNames($offset = 0) {
        return array_slice(self::$_games, $offset, null, true);
    }
    
    /**
     * 取得指定游戏的名字
     *
     * @static
     * @param integer $index
     * @return string
     */
    static public function getLongName($index) {
        return self::$_games[$index];
    }
    
    /**
     * 取得所有游戏缩写列表
     * 
     * @static
     * @param integer $offset
     * @return array()
     */
    static public function getShortNames($offset = 0) {
        return array_slice(self::$_shortNames, $offset, null, true);
    }
    
    /**
     * 取得指定游戏的缩写
     * 
     * @static
     * @param integer $index
     * @return string
     */
    static public function getShortName($index) {
        return self::$_shortNames[$index];
    }
    
    /**
     * 是否属于趣乐游戏
     * 
     * @static
     * @param integer $gameType
     * @return boolean
     */
    static public function isQule($gameType) {
        return $gameType >= self::QULE; 
    }
}
