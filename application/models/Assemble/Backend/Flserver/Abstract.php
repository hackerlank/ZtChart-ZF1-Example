<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Backend
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 登陆日志数据装载器抽象类
 *
 * @abstract
 * @name ZtChart_Model_Assemble_Backend_Flserver_Abstract
 */
abstract class ZtChart_Model_Assemble_Backend_Flserver_Abstract extends ZtChart_Model_Assemble_Backend_Abstract {
    
    /**
     * 取得获取数据的Select对象
     *
     * @see ZtChart_Model_Assemble_Backend_Abstract::select()
     * @param string $start 起始时间
     * @param string $end 结束时间
     * @param integer $pos 时间位置
     * @return Zend_Db_Select
     */
    public function select($start, $end, $pos) {
        $select = $this->columns(self::ROW_LABEL, self::ROW_DATA);
        $select->where("flserver_datetime >= ?", ZtChart_Model_Assemble_Datetime::padDatetime($start, $pos))
               ->where("flserver_datetime < ?", ZtChart_Model_Assemble_Datetime::padDatetime($end, $pos));
        
        return $select;
    }
    
    /**
     * 取得要统计的数据列
     * 
     * @abstract
     * @param string $asLabel 坐标列别名(X轴)
     * @param string $asData 数据列别名(Y轴)
     * @return Zend_Db_Select
     */
    abstract public function columns($asLabel, $asData);
}