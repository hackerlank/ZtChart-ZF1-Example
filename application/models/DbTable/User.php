<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: User.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 用户数据表类
 *
 * @name ZtChart_Model_DbTable_User
 * @see ZtChart_Model_Db_Table_Abstract
 */
class ZtChart_Model_DbTable_User extends ZtChart_Model_Db_Table_Abstract {

    /**
     * 
     * @var string
     */
    protected $_name = 'user';
    
    /**
     * 
     * @var string
     */
    protected $_primary = 'user_id';
    
    /**
     * 
     * @var array
     */
    protected $_referenceMap = array(
        'Role' => array(
            'columns' => 'user_roleid',
            'refTableClass' => 'ZtChart_Model_DbTable_Role',
            'refColumns' => 'role_id')
    );
}