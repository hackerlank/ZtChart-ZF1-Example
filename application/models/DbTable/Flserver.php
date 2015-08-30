<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Flserver.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 登陆日志数据表类
 *
 * @name ZtChart_Model_DbTable_Flserver
 */
class ZtChart_Model_DbTable_Flserver extends ZtChart_Model_Db_Table_Abstract {

    /**
     * 
     * @var string
     */
    protected $_name = 'flserver';
    
    /**
     *
     * @var string
     */
    protected $_primary = 'flserver_hash';
    
    /**
     *
     * @param Zend_Db_Adapter_Abstract $db
     */
    static public function setDefaultTableAdapter($db) {
        self::addDefaultTableAdapter($db, __CLASS__);
    }
}