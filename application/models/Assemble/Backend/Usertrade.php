<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Backend
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Usertrade.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 充值消耗日志数据装载器抽象类
 *
 * @name ZtChart_Model_Assemble_Backend_Usertrade
 */
class ZtChart_Model_Assemble_Backend_Usertrade extends ZtChart_Model_Assemble_Backend_Usertrade_Abstract {
    
    /**
     * 构造函数
     */
    public function __construct() {
        $this->_tableDAO = new ZtChart_Model_DbTable_Usertrade();
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
        $select->columns("usertrade_datetime AS {$asLabel}")->columns("SUM(usertrade_point) AS {$asData}");
        $select->group($asLabel);
    
        return $select;
    }
}