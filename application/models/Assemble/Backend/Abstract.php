<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Backend
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 后端数据装载器
 *
 * @abstract
 * @name ZtChart_Model_Assemble_Backend_Abstract
 */
abstract class ZtChart_Model_Assemble_Backend_Abstract {
    
    /**
     * 
     */
    const ROW_LABEL = 'label';
    const ROW_DATA = 'data';
    
    /**
     * 
     * @staticvar array
     */
    static protected $_allowedGameTypes = null;
    
    /**
     * 
     * @var array
     */
    protected $_gameTypes = null;
    
    /**
     * 是否按游戏类型分组查询
     * 
     * @var boolean
     */
    protected $_group = false;
    
    /**
     * 
     * @var ZtChart_Model_Db_Table_Abstract
     */
    protected $_tableDAO = null;
    
    /**
     * 
     * @var array
     */
    protected $_merge = array();
    
    /**
     * 
     * @param integer|array $gameTypes
     * @return void
     */
    static public function setAllowedGameTypes($gameTypes) {
        self::$_allowedGameTypes = (null == $gameTypes) ? $gameTypes : (array) $gameTypes;
    }
    
    /**
     * 
     * @return array
     */
    static public function getAllowedGameTypes() {
        return self::$_allowedGameTypes;
    }
    
    /**
     * 
     * @param array $config
     */
    public function __construct(array $config) {
        $this->setConfig($config);
    }
    
    /**
     * 设置参数
     * 
     * @param array $config
     * @return void
     */
    public function setConfig($config) {
        foreach (array_change_key_case($config) as $param => $value) {
            $setMethod = 'set' . ucfirst($param);
            if (method_exists($this, $setMethod)) {
                call_user_func(array($this, $setMethod), $value);
            }
        }
    }
    
    /**
     * 清空游戏类型
     * 
     * @return void
     */
    public function clearGameTypes() {
        $this->_gameTypes = null;
    }
    
    /**
     * 设置游戏类型
     * 
     * @param integer|array $gameTypes
     * @return void
     */
    public function setGameTypes($gameTypes) {
        $this->_gameTypes = array_filter((array) $gameTypes);
    }
    
    /**
     * 取得游戏类型
     * 
     * @return null|array
     */
    public function getGameTypes() {
        return $this->_gameTypes;
    }
    
    /**
     * 取得数据集合
     * 
     * @param string $start 起始时间
     * @param string $end 结束时间
     * @param string $unit 时间单位
     * @return array
     */
    public function collect($start, $end, $unit) {
        if (!$this->_checkDatetimeUnit($unit)) {
            throw new ZtChart_Model_Assemble_Backend_Exception('The datetime unit is illegal: ' . $unit);
        }
        $this->_selectDatetimeTable($start, $end, $unit);
        
        $pos = ZtChart_Model_Assemble_Datetime::getDatetimePos($unit);
        $select = $this->select(ZtChart_Model_Assemble_Datetime::normalizeDatetime($start), 
                                ZtChart_Model_Assemble_Datetime::normalizeDatetime($end), $pos);
        
        $rowset = array();
        foreach ($this->_selectGameTypes($select, $this->_group)->query()->fetchAll() as $row) {
            $label = substr($row[self::ROW_LABEL], 0, $pos);
            if (!array_key_exists($label, $rowset)) {
                $rowset[$label] = array();
            }
            unset($row[self::ROW_LABEL]);
            
            $tmp = &$rowset[$label];
            if (!empty($this->_merge)) {
                foreach ($this->_merge as $merge) {
                    $v = $row[$merge];
                    if (!array_key_exists($v, $tmp)) {
                        $tmp[$v] = array();
                        unset($row[$merge]);
                    }
                    $tmp = &$tmp[$v];
                }
            } 
            foreach ($row as $key => $value) {
                if (!array_key_exists($key, $tmp)) {
                    $tmp[$key] = 0;
                }
                $tmp[$key] += $value;
            }
        } 
        
        return $rowset;
    }
    
    /**
     * 取得当前对象所有参数组合的哈希值
     * 
     * @return string
     */
    public function hashObject() {
        $vars = array(get_class($this));
        foreach (get_class_methods($this) as $method) {
            if ('get' == substr($method, 0, 3)) {
                $vars[substr($method, 3)] = $this->$method();
            }
        }
        
        return Zend_Crypt::hash('md5', (serialize($vars)));
    }
    
    /**
     * 设置是否按游戏类型分组查询
     * 
     * @param boolean $group
     */
    public function setGroup($group = true) {
        $this->_group = $group;
    }
    
    /**
     * 返回是否按游戏类型分组查询
     * 
     * @return boolean
     */
    public function getGroup() {
        return $this->_group; 
    }
    
    /**
     * 检查时间单位是否合法
     * 
     * @param string $unit
     * @return boolean
     */
    protected function _checkDatetimeUnit($unit) {
        return ZtChart_Model_Assemble_Datetime::checkDatetimeUnit($unit);
    }
    
    /**
     * 选择合适的数据表
     * 
     * @param string $start 起始时间
     * @param string $end 结束时间
     * @param string $unit 时间单位
     * @return void
     */
    protected function _selectDatetimeTable($start, $end, $unit) {
        $tableName = $this->_tableDAO->info('name');
        if ('_' == substr($tableName, -2, 1)) {
            $tableName = substr($tableName, 0, -2);
        }
        switch ($unit) {
            case Zend_Date::YEAR:
            case Zend_Date::MONTH:
            case Zend_Date::DAY:
                $tableName .= '_d';
                break;
            case Zend_Date::HOUR:
                $tableName .= '_h';
                break;
            case Zend_Date::MINUTE:
                $tableName .= '_i';
                break;
            case Zend_Date::SECOND:
                break;
        }
        
        $this->_tableDAO->setOptions(array('name' => $tableName));
    }
    
    /**
     * 选择合适的游戏类型
     * 
     * @param Zend_Db_Select $select
     * @param boolean $group
     * @return Zend_Db_Select
     */
    protected function _selectGameTypes(Zend_Db_Select $select, $group = false) {
        $tableName = $this->_tableDAO->info('name');
        $column = (strstr($tableName, '_', true) ?: $tableName) . '_gametype';
        if (is_array(self::$_allowedGameTypes) && is_array($this->_gameTypes)) {
            $gameTypes = array_intersect($this->_gameTypes, self::$_allowedGameTypes);
        } else if (is_array(self::$_allowedGameTypes)) {
            $gameTypes = self::$_allowedGameTypes;
        } else if (is_array($this->_gameTypes)) {
            $gameTypes = $this->_gameTypes;
        }
        if (isset($gameTypes) && !empty($gameTypes)) {
            count($gameTypes) === 1 ? $select->where("{$column} = ?", $gameTypes)
                                    : $select->where("{$column} IN (?)", $gameTypes);
        }
        
        if (true === $group) {
            if (!in_array(ZtChart_Model_Assemble::GAMETYPE, $this->_merge)) {
                array_unshift($this->_merge, ZtChart_Model_Assemble::GAMETYPE);
            }
            $select->columns("{$column} AS " . ZtChart_Model_Assemble::GAMETYPE)
                   ->group(ZtChart_Model_Assemble::GAMETYPE);
        }
        
        return $select;
    }
    
    /**
     * 取得获取数据的Select对象
     * 
     * @abstract
     * @param string $start 起始时间
     * @param string $end 结束时间
     * @param integer $pos 时间位置
     * @return Zend_Db_Select
     */
    abstract public function select($start, $end, $pos);
}
