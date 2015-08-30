<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @subpackage ZtChart_Model_DbTable_Acl
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Row.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 权限关系数据行对象类
 *
 * @name ZtChart_Model_DbTable_Acl_Row
 * @see ZtChart_Model_Db_Table_Row_Abstract
 */
class ZtChart_Model_DbTable_Acl_Row extends ZtChart_Model_Db_Table_Row_Abstract {
    
    /**
     * Returns the column/value data as an array.
     *
     * @see Zend_Db_Table_Row_Abstract::toArray()
     * @param boolean $decodePrivileges
     * @return array
     */
    public function toArray($decodePrivileges = false) {
        $data = (array) $this->_data;
        if ($decodePrivileges) {
            $data['acl_privileges'] = Zend_Json::decode($data['acl_privileges']);
        }
        
        return $data;
    }
}