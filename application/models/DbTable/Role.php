<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Role.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 角色数据表对象
 *
 * @name ZtChart_Model_DbTable_Role
 * @see ZtChart_Model_Db_Table_Abstract
 */
class ZtChart_Model_DbTable_Role extends ZtChart_Model_Db_Table_Abstract {

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'ZtChart_Model_DbTable_Role_Row';
    
    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'ZtChart_Model_DbTable_Role_Rowset';
    
    /**
     * 
     * @var string
     */
    protected $_name = 'role';
    
    /**
     * 
     * @var string
     */
    protected $_primary = 'role_id';
    
    /**
     * 
     * @var array
     */
    protected $_dependentTables = array('ZtChart_Model_DbTable_User', 'ZtChart_Model_DbTable_Acl');
}