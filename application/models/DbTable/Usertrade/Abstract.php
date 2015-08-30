<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 充值消耗日志数据表抽象类
 *
 * @abstract
 * @name ZtChart_Model_DbTable_Usertrade_Abstract
 */
abstract class ZtChart_Model_DbTable_Usertrade_Abstract extends ZtChart_Model_Db_Table_Abstract {

    /**
     * 
     * @var string
     */
    protected $_primary = 'usertrade_hash';
}