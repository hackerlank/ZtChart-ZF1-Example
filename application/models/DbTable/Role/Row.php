<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @subpackage ZtChart_Model_DbTable_Role
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Row.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 角色数据行对象类
 *
 * @name ZtChart_Model_DbTable_Role_Row
 * @see ZtChart_Model_Db_Table_Row_Abstract
 */
class ZtChart_Model_DbTable_Role_Row extends ZtChart_Model_Db_Table_Row_Abstract {
    
    /**
     * Returns the column/value data as an array.
     *
     * @see Zend_Db_Table_Row_Abstract::toArray()
     * @param boolean $decode
     * @return array
     */
    public function toArray($decode = false) {
        $data = (array) $this->_data;
        if (false !== $decode) {
            $data['role_gametype'] = Zend_Json::decode($data['role_gametype']);
        }
        
        return $data;
    }
}