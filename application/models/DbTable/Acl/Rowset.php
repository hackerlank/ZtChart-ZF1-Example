<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @subpackage ZtChart_Model_DbTable_Acl
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Rowset.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 权限关系数据行集合对象类
 *
 * @name ZtChart_Model_DbTable_Acl_Rowset
 * @see ZtChart_Model_Db_Table_Rowset_Abstract
 */
class ZtChart_Model_DbTable_Acl_Rowset extends ZtChart_Model_Db_Table_Rowset_Abstract {
    
    /**
     * ZtChart_Model_DbTable_Acl_Row class name.
     *
     * @var string
     */
    protected $_rowClass = 'ZtChart_Model_DbTable_Acl_Row';
    
    /**
     * Returns all data as an array.
     *
     * Updates the $_data property with current row object values.
     *
     * @see Zend_Db_Table_Rowset_Abstract::toArray()
     * @param boolean $decodePrivileges
     * @return array
     */
    public function toArray($decodePrivileges = false) {
        foreach ($this->_rows as $i => $row) {
            $this->_data[$i] = $row->toArray($decodePrivileges);
        }
        return $this->_data;
    }
}