<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Infoserver.php 37708 2012-12-17 08:26:09Z zhangweiwen $
 */

/**
 * 在线人数日志数据表类
 *
 * @name ZtChart_Model_DbTable_Infoserver
 */
class ZtChart_Model_DbTable_Infoserver extends ZtChart_Model_Db_Table_Abstract {
    
    /**
     * 表前缀
     * 
     * @var string
     */
    const TABLE_ONLINENUM = 'ONLINENUM';
    const TABLE_COUNTRYONLINE = 'COUNTRYONLINE';
    
    /**
     * 字段名
     * 
     * @var string
     */
    const COLUMN_ONLINENUM = 'OnlineNum';
    const COLUMN_NUM = 'Num';
    
    /**
     * 
     * @staticvar array
     */
    static protected $_infoServerConfigs = array();
    
    /**
     * 
     * @staticvar array
     */
    static protected $_infoServerAdapters = array();
    
    /**
     *
     * @var string
     */
    protected $_primary = 'NO';
    
    /**
     * 
     * @var string|Zend_Db_Expr
     */
    protected $_exprSumOnlineNum;
    
    /**
     * 
     * @param Zend_Db_Adapter_Abstract $db
     */
    static public function setDefaultTableAdapter($db) {
        self::addDefaultTableAdapter($db, __CLASS__);
    }
    
    /**
     * 设置所有游戏的Infoserver数据库参数
     * 
     * @static
     * @param array $params
     */
    static public function setInfoserverConfigs($configs) {
        self::$_infoServerConfigs = $configs;
    }
    
    /**
     * 添加一个指定游戏类型的Infoserver数据库参数
     * 
     * @static
     * @param array $config
     * @param integer $gameType
     */
    static public function addInfoserverConfig($config, $gameType) {
        self::$_infoServerConfigs[$gameType][] = $config;
    }
    
    /**
     * 设置所有游戏的Infoserver数据库适配器
     * 
     * @static
     * @param array $adapters
     */
    static public function setInfoserverAdapters($adapters) {
        self::$_infoServerAdapters = $adapters;
    }
    
    /**
     * 添加一个指定游戏类型的Infoserver数据库适配器
     * 
     * @static
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param integer $gameType
     */
    static public function addInfoserverAdapter($adapter, $gameType) {
        self::$_infoServerAdapters[$gameType][] = self::_setupAdapter($adapter);
    }
    
    /**
     * 指定的游戏是否单一适配器
     *
     * @static
     * @param integer $gameType
     * @return boolean
     */
    static public function isSingleAdapter($gameType) {
        return count(self::getInfoserverAdapters($gameType)) == 1;
    }
    
    /**
     * 取得所有Infoserver的游戏类型
     * 
     * @static
     * @return array
     */
    static public function getInfoserverGameTypes() {
        return !empty(self::$_infoServerConfigs) ? array_keys(self::$_infoServerConfigs) : array_keys(self::$_infoServerAdapters);
    }
    
    /**
     * 返回指定游戏的所有适配器
     * 
     * @param integer $gameType
     * @return array
     * @throws ZtChart_Model_Db_Table_Exception
     */
    static public function getInfoserverAdapters($gameType) {
        if (!array_key_exists($gameType, self::$_infoServerAdapters)) {
            if (!array_key_exists($gameType, self::$_infoServerConfigs)) {
                throw new ZtChart_Model_Db_Table_Exception('The gametype of infoserver is invalid:' . $gameType);
            } else {
                foreach (self::$_infoServerConfigs[$gameType] as $config) {
                    self::addInfoserverAdapter(Zend_Db::factory($config['adapter'], $config), $gameType);
                }
            }
        }
        
        return self::$_infoServerAdapters[$gameType];
    }
    
    /**
     * 工厂方法，返回指定游戏的在线人数日志数据表对象
     * 
     * @param integer $gameType
     * @param string $datetime
     * @param mixed $config
     * @return ZtChart_Model_DbTable_Infoserver
     */
    static public function factory($gameType, $datetime = null, $config = array()) {
        $loader = new Zend_Loader_PluginLoader(
            array(
                __CLASS__ => realpath(__DIR__ . '/Infoserver')
            )
        );
        if (false !== ($class = $loader->load(ZtChart_Model_GameType::getShortName($gameType), false))) {
            $infoserver = new $class($gameType, $datetime, $config);
        } else {
            $infoserver = new self($gameType, $datetime, $config);
        }
        
        return $infoserver;
    }
    
    /**
     * 
     * @param integer $gameType
     * @param string $datetime
     * @param mixed $config
     * @throws ZtChart_Model_Db_Table_Exception
     */
    public function __construct($gameType, $datetime = null, $config = array()) {
        parent::__construct($config);
        
        if (self::isSingleAdapter($gameType)) {
            $this->_setAdapter(self::$_infoServerAdapters[$gameType][0]);
        }
        $this->setTablename($datetime);
    }
    
    /**
     * @see Zend_Db_Table_Abstract::init()
     */
    public function init() {
        $this->setExprSumOnlineNum();
    }
    
    /**
     * 根据时间设置数据表
     * 
     * @param string $datetime
     * @return void
     */
    public function setTablename($datetime) {
        $date = new Zend_Date($datetime);
        $this->_name = self::TABLE_COUNTRYONLINE . $date->toString('yMMdd');
    }
    
    /**
     * 设置在线人数总数的表达式
     * 
     * @param string $column
     * @return void
     */
    public function setExprSumOnlineNum($column = self::COLUMN_NUM) {
        $this->_exprSumOnlineNum = new Zend_Db_Expr("SUM({$column})");
    }
    
    /**
     * 取得InfoServer数据表中某个时间段的总数
     * 
     * @param integer $startTimestamp
     * @param integer $range
     * @return string|null
     */
    public function fetchSum($startTimestamp = null, $range = 1) {
        $select = $this->select(true)->reset(self::COLUMNS)->columns(array('OnlineNum' => $this->_exprSumOnlineNum));
        if (!empty($startTimestamp)) {
            $select->where('rTimestamp >= ?', $startTimestamp, Zend_DB::INT_TYPE);
            if (!empty($range)) {
                $startDate = new Zend_Date($startTimestamp);
                $select->where('rTimestamp <= ?', $startDate->addMinute($range)->getTimestamp(), Zend_DB::INT_TYPE);
            }
        }
        
        return $select->query()->fetchColumn();
    }
    
    /**
     * 取得InfoServer数据表中某个时间段的分组数据总数
     * 
     * @param integer $startTimestamp
     * @param integer $range
     * @param array|string $spec
     * @return array|null
     */
    public function fetchSumGroup($startTimestamp = null, $range = null, $spec = 'rTimestamp') {
        $startDate = new Zend_Date($startTimestamp);
        
        if (empty($range)) {
            $endDate = Zend_Date::now();
        } else {
            $endDate = clone $startDate;
            $endDate->addMinute($range);
        }

        $unionParts = array();
        for (; 0 >= ($compare = $startDate->compareDate($endDate)); $startDate->addDay(1)) {
            $infoserver = clone $this;
            $infoserver->setTablename($startDate);
            $unionParts[] = $infoserver->getSumGroupSelect($startTimestamp, 0 == $compare ? $range : null, $spec);
        }
        $select = $this->getAdapter()->select()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);
        
        return $select->query()->fetchAll(Zend_Db::FETCH_NUM);
    }
    
    /**
     * 取得InfoServer数据表中某个时间段的分组数据SQL语句
     * 
     * @param integer $startTimestamp
     * @param integer $range
     * @param array|string $spec
     * @return Zend_Db_Select
     */
    public function getSumGroupSelect($startTimestamp = null, $range = null, $spec = 'rTimestamp') {
        $select = $this->select(true)->reset(self::COLUMNS)->columns(array('rTimestamp', 'OnlineNum' => $this->_exprSumOnlineNum));
        if (!empty($startTimestamp)) {
            $startDate = new Zend_Date($startTimestamp);
            $select->where('rTimestamp >= ?', $startDate->setSecond(0)->getTimestamp(), Zend_DB::INT_TYPE);
            if (!empty($range)) {
                $select->where('rTimestamp <= ?', $startDate->addMinute($range - 1)->setSecond(59)->getTimestamp(), Zend_DB::INT_TYPE);
            }
        } 
        $select->group($spec);
        
        return $select;
    }
    
    /**
     * 取得InfoServer数据表中相同时间戳和的最大值 
     * 
     * @return string|null
     */
    public function fetchMax() {
        return $this->select(true)
                    ->reset(self::COLUMNS)
                    ->columns(array('OnlineNum' => $this->_exprSumOnlineNum))
                    ->group('rTimestamp')
                    ->order('OnlineNum DESC')
                    ->limit(1)
                    ->query()->fetchColumn();
    }
    
    /**
     * 取得InfoServer数据表中相同时间戳和的最小值
     *
     * @return string|null
     */
    public function fetchMin() {
        return $this->select(true)
                    ->reset(self::COLUMNS)
                    ->columns(array('OnlineNum' => $this->_exprSumOnlineNum))
                    ->group('rTimestamp')
                    ->order('OnlineNum')
                    ->limit(1)
                    ->query()->fetchColumn();
    }
    
    /**
     * 取得InfoServer数据表中的平均值，按时间戳个数计算。
     * 
     * @return float
     */
    public function fetchAvg() {
        return $this->fetchSum() / $this->count(null, 'rTimestamp');
    }
}