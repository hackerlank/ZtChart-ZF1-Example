<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Ip.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * IP地址归属地关系数据表对象
 *
 * @name ZtChart_Model_DbTable_Ip
 * @see ZtChart_Model_Db_Table_Abstract
 */
class ZtChart_Model_DbTable_Ip extends ZtChart_Model_Db_Table_Abstract {

    /**
     * 
     * @var string
     */
    protected $_name = 'ip';
    
    /**
     * 
     * @var string
     */
    protected $_primary = 'ip_id';
    
    /**
     *
     * @var array
     */
    protected $_referenceMap = array(
        'Area' => array(
            'columns' => 'ip_areaid',
            'refTableClass' => 'ZtChart_Model_DbTable_Area',
            'refColumns' => 'area_id')
    );
}