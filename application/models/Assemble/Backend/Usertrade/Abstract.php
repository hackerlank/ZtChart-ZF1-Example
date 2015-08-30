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
 * 充值消耗日志数据装载器抽象类
 *
 * @abstract
 * @name ZtChart_Model_Assemble_Backend_Usertrade_Abstract
 */
abstract class ZtChart_Model_Assemble_Backend_Usertrade_Abstract extends ZtChart_Model_Assemble_Backend_Abstract {
    
    /**
     * 
     */
    const PAYMENT = 0; // 充值
    const CONSUME = 1; // 消耗
    const SERVER = 2; // 专区卡
    const PROP = 5; // 道具卡
    const GIVED = 20; // 赠点
    
    /**
     * 操作类型
     * 
     * @var integer
     */
    protected $_opType = null;
    
    /**
     * 网银充值
     * 
     * @var integer
     */
    protected $_netbank = null;
    
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
        $select->where('usertrade_datetime >= ?', ZtChart_Model_Assemble_Datetime::padDatetime($start, $pos))
               ->where('usertrade_datetime < ?', ZtChart_Model_Assemble_Datetime::padDatetime($end, $pos));
        if (null !== $this->_opType) {
            $select->where('usertrade_optype = ?', $this->_opType);
        }
        if (null !== $this->_netbank) {
            // $this->clearGameTypes();
            $select->where('usertrade_netbank = ?', $this->_netbank);
        }
        
        return $select;
    }
    
    /**
     * 
     * @param integer $opType
     * @return void
     */
    public function setOpType($opType) {
        $this->_opType = $opType;
    }
    
    /**
     * 
     * @return integer
     */
    public function getOpType() {
        return $this->_opType;
    }
    
    /**
     * 设置充值
     * 
     * @return void
     */
    public function setPayment() {
        $this->setOpType(self::PAYMENT);
    }
    
    /**
     * 设置消耗
     * 
     * @return void
     */
    public function setConsume() {
        $this->setOpType(self::CONSUME);
    }
    
    /**
     * 设置是否网银
     * 
     * @param integer $netbank
     * @return void
     */
    public function setNetbank($netbank = 1) {
        $this->_netbank = $netbank;
    }
    
    /**
     * 返回是否网银
     * 
     * @return integer
     */
    public function getNetbank() {
        return $this->_netbank;
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