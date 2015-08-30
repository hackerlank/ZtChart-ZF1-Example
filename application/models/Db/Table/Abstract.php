<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model_Db
 * @subpackage ZtChart_Model_Db_Table
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 37415 2012-11-15 08:14:57Z zhangweiwen $
 */

/**
 * 全局数据表对象抽象类
 *
 * @abstract
 * @name ZtChart_Model_Db_Table_Abstract
 * @see Zend_Db_Table_Abstract
 */
abstract class ZtChart_Model_Db_Table_Abstract extends Zend_Db_Table_Abstract {
    
    /**
     * 
     * @staticvar array
     */
    static protected $_defaultTableDb = array();
    
    /**
     * 
     * @staticvar array
     */
    static protected $_instanceTableDb = array();
    
    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'ZtChart_Model_Db_Table_Row';

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'ZtChart_Model_Db_Table_Rowset';
    
    /**
     * 设置所有注册的表的默认适配器
     *
     * @static
     * @param  array $adapters
     * @return void
     */
    static public function setDefaultTableAdapters($adapters) {
        self::$_defaultTableDb = $adapters;
    }
    
    /**
     * 取得所有注册的表的默认适配器
     *
     * @static
     * @return Zend_Db_Adapter_Abstract or null
     */
    static public function getDefaultTableAdapters() {
        return self::$_defaultTableDb;
    }
    
    /**
     * 添加一个表的默认适配器
     * 
     * @param mixed $db Either an Adapter object, or a string naming a Registry key
     * @param string $class
     * @return void
     */
    static public function addDefaultTableAdapter($db, $class) {
        self::$_defaultTableDb[$class] = self::_setupAdapter($db);
    }
    
    /**
     * 取得所有实例化的适配器
     * 
     * @return array
     */
    static public function getInstanceTableAdapters() {
        return self::$_instanceTableDb;
    }
    
    /**
     * 保存一个实例化的适配器
     * 
     * @param Zend_Db_Adapter_Abstract $db
     * @return void
     */
    static public function pushInstanceTableAdapter($db) {
        if (!in_array($db, self::$_instanceTableDb)) {
            self::$_instanceTableDb[] = $db;
        }
    }
    
    /**
     * 
     * 
     * @static
     * @see Zend_Db_Table_Abstract::_setupAdapter()
     * @param  mixed $db Either an Adapter object, or a string naming a Registry key
     * @return Zend_Db_Adapter_Abstract
     * @throws Zend_Db_Table_Exception
     */
    protected static function _setupAdapter($db) {
        if (null != $db = parent::_setupAdapter($db)) {
            self::pushInstanceTableAdapter($db);
        }
        
        return $db;
    }
    
    /**
     * 取得当前表的注册的默认适配器
     *
     * @return Zend_Db_Adapter_Abstract or null
     */
    public function getDefaultTableAdapter() {
        return array_key_exists(get_class($this), self::$_defaultTableDb) 
                                ? self::$_defaultTableDb[get_class($this)] : null;
    }
    
    /**
     * Initialize database adapter.
     *
     * @see Zend_Db_Table_Abstract::_setupDatabaseAdapter()
     * @return void
     */
    protected function _setupDatabaseAdapter() {
        if (!empty(self::$_defaultTableDb)) {
            if (null == ($db = $this->getDefaultTableAdapter())) {
                foreach (self::getDefaultTableAdapters() as $tableClass => $adapter) {
                    if (is_subclass_of(get_class($this), $tableClass)) {
                        $this->_setAdapter($adapter);
                        break;
                    }
                }
            } else {
                $this->_setAdapter($db);
            }
        }
        
        parent::_setupDatabaseAdapter();
    }
    
    /**
     * 取出记录总数
     * 
     * @param array $where
     * @param string|array $group
     * @return integer
     */
    public function count(array $where = null, $group = null) {
        $select = $this->select()->from($this->_name, 'COUNT(*)');
        if (!empty($where)) {
            foreach ($where as $cond => $value) {
                is_integer($cond) ? $select->where($value) : $select->where($cond, $value);
            }
        }
        if (!empty($group)) {
            $select->group($group);
        }
        
        return $select->query()->fetchColumn();
    }
    
    /**
     * 扩充了父类的同名方法，如果传入的是整形值或整数字符串，则删除主键值为该整形值的所有记录。
     * 
     * @see Zend_Db_Table_Abstract::delete()
     * @param array|string|integer $where SQL WHERE clause(s).
     * @return int The number of rows deleted.
     */
    public function delete($where) {
        return parent::delete($this->_whereInt($where)); 
    }
    
    /**
     * 扩充了父类的同名方法，如果传入的是整形值或整数字符串，则更新主键值为该整形值的所有记录。
     * 
     * @see Zend_Db_Table_Abstract::update()
     * @param array|string|integer $where SQL WHERE clause(s).
     * @return int The number of rows updated.
     */
    public function update(array $data, $where) {
        return parent::update($data, $this->_whereInt($where)); 
    }
    
    /**
     * 清空表的数据。
     * 
     * @return integer
     */
    public function truncate() {
        $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
        return $this->_db->truncate($tableSpec);
    }
    
    /**
     * 扩充了父类的同名方法，如果传入的是整形值或整数字符串，则取得主键值为该整形值的所有记录。
     * 
     * @param  mixed $key The value(s) of the primary keys.
     * @return ZtChart_Model_Db_Table_Rowset Row(s) matching the criteria.
     */
    public function find() {
        if (1 == func_num_args()) {
            $primary = func_get_arg(0);
            if ($this->_isInt($primary)) {
                return parent::find(intval($primary));
            }
        }
        return call_user_func_array(array('parent', 'find'), func_get_args());
    }
    
    /**
     * 扩充了父类的同名方法，如果传入的是整形值或整数字符串，则取得主键值为该整形值的一条记录。
     *
     * @param integer|string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array $order  OPTIONAL An SQL ORDER clause.
     * @param int $offset OPTIONAL An SQL OFFSET value.
     * @return ZtChart_Model_Db_Table_Row|null The row results per the Zend_Db_Adapter fetch mode, or null if no row found.
     */
    public function fetchRow($where = null, $order = null, $offset = null) {
        return parent::fetchRow($this->_whereInt($where), $order, $offset);
    }
    
    /**
     * 取得最后一条记录
     * 
     * @return ZtChart_Model_Db_Table_Row
     */
    public function lastRow() {
        return parent::fetchRow(null, array_map(function($primary) 
                                        { return $primary . ' DESC'; }, $this->info(parent::PRIMARY)));
    }
    
    /**
     * 返回对应的Dependent表对象
     * 
     * @param string $classname
     * @return null|Model_DbTable_Abstract
     */
    public function getDependentObject($tableClassname) {
        if (!in_array($tableClassname, $this->_dependentTables)) {
            throw new Zend_Db_Table_Exception("No dependent table $tableClassname.");
        }
        
        return new $tableClassname();
    }
    
    /**
     * 返回与对应的Dependent表建立join连接时的on语句条件
     * 
     * @param string $tableClassname
     * @return string
     */
    public function getDependentJoinOn($tableClassname) {
        $dependent = $this->getDependentObject($tableClassname);
        $refMap = $dependent->getReference(get_class($this));
        if (!isset($refMap[parent::REF_COLUMNS])) {
            $refMap[parent::REF_COLUMNS] = (array) $this->_primary;
        }
        $joinOn = array();
        for ($i = 0; $i < count($refMap[parent::COLUMNS]); $i++) {
            $joinOn[] = sprintf(" %s.%s = %s.%s ", $this->info(parent::NAME), $refMap[parent::REF_COLUMNS][$i], 
                                        $dependent->info(parent::NAME), $refMap[parent::COLUMNS][$i]);
        }
        
        return implode(Zend_Db_Select::SQL_AND, $joinOn);
    }
    
    /**
     * 返回对应的Reference表对象
     * 
     * @param string $tableClassname
     * @param string $ruleKey
     * @return Model_DbTable_Abstract
     */
    public function getReferenceObject($tableClassname, $ruleKey = null) {
        $refMap = $this->getReference($tableClassname, $ruleKey);
        return new $refMap[parent::REF_TABLE_CLASS]();
    }
    
    /**
     * 返回与对应的Reference表建立join连接时的on语句条件
     * 
     * @param string $tableClassname
     * @param string $ruleKey
     * @return string
     */
    public function getReferenceJoinOn($tableClassname, $ruleKey = null) {
        $refMap = $this->getReference($tableClassname, $ruleKey);
        $refTable = $this->getReferenceObject($tableClassname, $ruleKey = null);
        if (!isset($refMap[parent::REF_COLUMNS])) {
            $refMap[parent::REF_COLUMNS] = $refTable->info(parent::PRIMARY);
        }
        $joinOn = array();
        for ($i = 0; $i < count($refMap[parent::COLUMNS]); $i++) {
            $joinOn[] = sprintf(" %s.%s = %s.%s ", $this->info(parent::NAME), $refMap[parent::COLUMNS][$i], 
                                $refTable->info(parent::NAME), $refMap[parent::REF_COLUMNS][$i]);
        }
        
        return implode(Zend_Db_Select::SQL_AND, $joinOn);
    }
    
    /**
     * 装配完整依赖关系的Select对象
     *
     * @param array|string $tableClass
     * @param string $joinType Type of join; inner, left, and null are currently supported
     * @param array|string|Zend_Db_Expr $cols The columns to select from this table.
     * @param string $schema The schema name to specify, if any.
     * @return Zend_Db_Select
     */
    public function selectDependent($tableClass = null, $joinType = 'left',
                                        $cols = Zend_Db_Select::SQL_WILDCARD, $schema = null) {
        $select = $this->select(true)->setIntegrityCheck(false);
        foreach ((array) $tableClass as $dependentTable) {
            if (!in_array($dependentTable, $this->_dependentTables)) {
                continue;
            }
            $dependentTable = new $dependentTable();
            try {
                $relation = $dependentTable->getReference(get_class($this));
            } catch (Zend_Db_Table_Exception $e) {
                continue;
            }
                
            $name = $dependentTable->info(self::NAME);
            $cond = "{$name}.{$relation[self::COLUMNS][0]} = {$this->_name}.{$relation[self::REF_COLUMNS][0]}";
            call_user_func(array($select, 'join' . ucfirst($joinType)), $name, $cond, $cols, $schema);
        }
    
        return $select;
    }
    
    /**
     * 装配完整父级关系的Select对象
     *
     * @param array|string $tableClassname The table name.
     * @param string $joinType Type of join; inner, left, and null are currently supported
     * @param array|string|Zend_Db_Expr $cols The columns to select from this table.
     * @param string $schema The schema name to specify, if any.
     * @return Zend_Db_Select
     */
    public function selectParent($tableClassname = null, $joinType = 'left',
                                    $cols = Zend_Db_Select::SQL_WILDCARD, $schema = null) {
        $select = $this->select(true)->setIntegrityCheck(false);
        if (!is_array($tableClassname)) {
            $tableClassname = (array) $tableClassname;
        }
        foreach ($tableClassname as $table) {
            try {
                $relation = $this->getReference($table);
            } catch (Zend_Db_Table_Exception $e) {
                continue;
            }
            $refTable = new $relation['refTableClass'];
                
            $name = $refTable->info(self::NAME);
            $cond = "{$name}.{$relation[self::REF_COLUMNS][0]} = {$this->_name}.{$relation[self::COLUMNS][0]}";
            call_user_func(array($select, 'join' . ucfirst($joinType)), $name, $cond, $cols, $schema);
        }
    
        return $select;
    }
    
    /**
     * 过滤数据表字段
     *
     * @param array $data
     * @param boolean $key
     * @return array
     */
    public function filterColumns($data, $key = false) {
        $columns = $this->info(self::COLS);
    
        return $key ? array_intersect_key($data, array_fill_keys($columns, null))
                    : array_intersect($data, $columns);
    }
    
    /**
     * 产生主键相关的WHERE语句
     * 
     * @param mixed $where
     * @return array
     */
    protected function _whereInt($where) {
        if (1 < count($primary = $this->info(parent::PRIMARY))) {
            throw new ZtChart_Model_DbTable_Exception('Do not support primary key more than one');
        }
        $primary = current($primary);
        
        if (empty($where) || $this->_isInt($where)) {
            $where = $this->_db->quoteInto("{$primary} = ?", $where, Zend_Db::INT_TYPE);
        } else if ($this->_isIntArray($where)) {
            $where = $this->_db->quoteInto("{$primary} IN (?)", $where);
        } 
        
        return $where;
    }
    
    /**
     * 判断是否整数数据
     * 
     * @param string|integer $value
     * @return boolean
     */
    protected function _isInt($value) {
        if (is_int($value)) {
            return true;
        } else if (is_numeric($value)) {
            if (preg_match('/^[0-9]+$/', $value)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 判断是否无字符串下标的整形数组
     * 
     * @param array $value
     * @return boolean
     */
    protected function _isIntArray($data) {
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                if (is_string($key) || !$this->_isInt($value)) {
                    return false;
                }
            }
            return true;
        }
        
        return false;
    }
}