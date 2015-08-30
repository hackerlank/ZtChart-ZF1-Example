<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model_Db
 * @subpackage ZtChart_Model_Db_Table_Row
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局数据行对象抽象类
 *
 * @abstract
 * @name ZtChart_Model_Db_Table_Row_Abstract
 * @see Zend_Db_Table_Row_Abstract
 */
abstract class ZtChart_Model_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract {
    
    /**
     * 取得主键记录
     * 
     * @return mixed
     */
    public function getPrimaryKey() {
        $this->_refresh();

        /**
         * Return the primary key value(s) as an array
         * if the key is compound or a scalar if the key
         * is a scalar.
         */
        $primaryKey = $this->_getPrimaryKey(true);
        if (count($primaryKey) == 1) {
            return current($primaryKey);
        }

        return $primaryKey;
    }
    
    /**
     * 创建关联的Dependent表的记录
     * 
     * @param string $tableClassname
     * @param boolean $refColumns
     * @param string $ruleKey
     * @return ZtChart_Model_Db_Table_Row
     */
    public function createDependentRow($tableClassname, $refColumns = true, $ruleKey = null) {
        $dependentRow = $this->getTable()->getDependentObject($tableClassname)->createRow();
        if (true === $refColumns) {
            $refMap = $dependentRow->getTable()->getReference($this->getTableClass(), $ruleKey);
            for ($key = 0; $key < count($refMap[Zend_Db_Table_Abstract::COLUMNS]); $key++) {
                $dependentRow->offsetSet($refMap[Zend_Db_Table_Abstract::COLUMNS][$key], 
                                $this->offsetGet($refMap[Zend_Db_Table_Abstract::REF_COLUMNS][$key])); 
            }
        }
        
        return $dependentRow;
    }

    /**
     * 清空数据行中列的内容
     * 
     * @return ZtChart_Model_Db_Table_Row_Abstract Provides a fluent interface
     */
    public function clearRow() {
        foreach ($this->_data as $columnName => $value) {
            if (!in_array($columnName, $this->_primary)) {
                $this->__set($columnName, '');
            }
        }
        
        return $this;
    }
    
    /**
     * 删除数据行中的所有列
     * 
     * @param string|array $reserve 需要保留的列名
     * @return ZtChart_Model_Db_Table_Row_Abstract Provides a fluent interface
     */
    public function unsetRow($reserve = null) {
        foreach ($this->_data as $columnName => $value) {
            if (null !== $reserve && in_array($columnName, (array) $reserve)) {
                continue;
            }
            $this->__unset($columnName);
        }
        
        return $this;
    }
}