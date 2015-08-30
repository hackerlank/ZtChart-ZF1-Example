<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Acl
 * @subpackage ZtChart_Model_Acl_Resource
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Role.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 角色数据管理
 *
 * @name ZtChart_Model_Acl_Resource
 */
class ZtChart_Model_Acl_Role implements Zend_Acl_Role_Interface {
    
    /**
     * 
     * @var ZtChart_Model_Db_Table_Row
     */
    protected $_roleRow;
    
    /**
     * 
     * @param integer|string|ZtChart_Model_Db_Table_Row $role
     */
    public function __construct($role) {
        if (null !== $role) {
            if (!$role instanceof ZtChart_Model_Db_Table_Row) {
                $roleDAO = new ZtChart_Model_DbTable_Role();
                if (is_numeric($role)) {
                    $role = $roleDAO->fetchRow($role);
                }
            }
            $this->_roleRow = $role;
        }
    }
    
    /**
     * 
     * @see Zend_Acl_Role_Interface::getRoleId()
     */
    public function getRoleId() {
        return (string) $this->_roleRow->role_id;
    }
    
    /**
     * 
     * @return string
     */
    public function __toString() {
        return $this->getRoleId();
    }
}