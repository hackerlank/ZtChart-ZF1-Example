<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Acl.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 权限关系数据表对象
 *
 * @name ZtChart_Model_DbTable_Acl
 * @see ZtChart_Model_Db_Table_Abstract
 */
class ZtChart_Model_DbTable_Acl extends ZtChart_Model_Db_Table_Abstract {

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'ZtChart_Model_DbTable_Acl_Row';
    
    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'ZtChart_Model_DbTable_Acl_Rowset';
    
    /**
     * 
     * @var string
     */
    protected $_name = 'acl';
    
    /**
     * 
     * @var string
     */
    protected $_primary = 'acl_id';
    
    /**
     * 
     * @var array
     */
    protected $_referenceMap = array(
        'Role' => array(
            'columns' => 'acl_roleid', 
            'refTableClass' => 'ZtChart_Model_DbTable_Role', 
            'refColumns' => 'role_id'
        ), 
        'Resource' => array(
            'columns' => 'acl_resourceid', 
            'refTableClass' => 'ZtChart_Model_DbTable_Resource', 
            'refColumns' => 'resource_id'
        )
    );
}