<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model_Db
 * @subpackage ZtChart_Model_Db_Table_Rowset
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局数据行集合对象抽象类
 *
 * @abstract
 * @name ZtChart_Model_Db_Table_Rowset_Abstract
 * @see Zend_Db_Table_Rowset_Abstract
 */
abstract class ZtChart_Model_Db_Table_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstract {

    /**
     * ZtChart_Model_Db_Table_Row class name.
     *
     * @var string
     */
    protected $_rowClass = 'ZtChart_Model_Db_Table_Row';
    
    /**
     * 返回某一列的所有值
     * 
     * @param string $column
     * @return array
     */
    public function getColumn($column) {
        $columnData = array();
        foreach ($this as $row) {
            $columnData[] = $row[$column];
        }
        
        return $columnData;
    }
}