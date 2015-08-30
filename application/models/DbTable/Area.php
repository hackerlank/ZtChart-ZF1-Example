<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Area.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * IP地址归属地数据表对象
 *
 * @name ZtChart_Model_DbTable_Area
 * @see ZtChart_Model_Db_Table_Abstract
 */
class ZtChart_Model_DbTable_Area extends ZtChart_Model_Db_Table_Abstract {

    /**
     *
     * @var string
     */
    protected $_name = 'area';
    
    /**
     *
     * @var string
     */
    protected $_primary = 'area_id';
}