<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Backend
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Area.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 登陆日志按区域统计数据装载器
 *
 * @name ZtChart_Model_Assemble_Backend_Flserver_Area
 */
class ZtChart_Model_Assemble_Backend_Flserver_Area extends ZtChart_Model_Assemble_Backend_Flserver_Abstract {
    
    /**
     *
     * @var array
     */
    protected $_merge = array('area');
    
    /**
     * 构造函数
     */
    public function __construct() {
        $this->_tableDAO = new ZtChart_Model_DbTable_Flserver_Area();
    }
    
    /**
     * 取得要统计的数据列
     *
     * @see ZtChart_Model_Assemble_Backend_Abstract::columns()
     * @param string $asLabel 坐标列别名(X轴)
     * @param string $asData 数据列别名(Y轴)
     * @return Zend_Db_Select
     */
    public function columns($asLabel, $asData) {
        $select = $this->_tableDAO->select(true)->reset('columns');
        
        $select->columns("flserver_datetime AS {$asLabel}")
               ->columns("flserver_area AS area")
               ->columns("SUM(flserver_count) AS {$asData}");
        $select->group(array($asLabel, 'area'));
    
        return $select;
    }
}