<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Resource.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 资源数据表对象
 *
 * @name ZtChart_Model_DbTable_Resource
 * @see ZtChart_Model_Db_Table_Abstract
 */
class ZtChart_Model_DbTable_Resource extends ZtChart_Model_Db_Table_Abstract {

    /**
     * 
     * @var string
     */
    protected $_name = 'resource';
    
    /**
     * 
     * @var string
     */
    protected $_primary = 'resource_id';
    
    /**
     * 
     * @var array
     */
    protected $_dependentTables = array('ZtChart_Model_DbTable_Acl');
}