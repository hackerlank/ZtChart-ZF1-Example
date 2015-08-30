<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Backend
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Account.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 充值消耗日志按帐号统计数据装载器
 *
 * @name ZtChart_Model_Assemble_Backend_Usertrade_Account
 */
class ZtChart_Model_Assemble_Backend_Usertrade_Account extends ZtChart_Model_Assemble_Backend_Usertrade_Abstract {
    
    /**
     * 是否计算APA数据
     *
     * @var boolean
     */
    protected $_apa = false;
    
    /**
     * 构造函数
     */
    public function __construct() {
        $this->_tableDAO = new ZtChart_Model_DbTable_Usertrade_Account();
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
        if (true === $this->_apa) {
            $select->columns('COUNT(DISTINCT usertrade_account) AS apa');
        }
        $select->group($asLabel);
    
        return $select;
    }
    
    /**
     * 设置是否计算APA数据
     * 
     * @param boolean $apa
     * @return void
     */
    public function setApa($apa = true) {
        $this->_apa = $apa;
    }
    
    /**
     * 返回是否计算APA数据
     * 
     * @return boolean
     */
    public function getApa() {
        return $this->_apa;
    }
}