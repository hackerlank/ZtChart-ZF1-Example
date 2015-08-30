<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_DbTable
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Im.php 37708 2012-12-17 08:26:09Z zhangweiwen $
 */

/**
 * 在线人数日志数据表类——嘟嘟
 *
 * @name ZtChart_Model_DbTable_Infoserver_Im
 */
class ZtChart_Model_DbTable_Infoserver_Im extends ZtChart_Model_DbTable_Infoserver {
    
    /**
     * @see ZtChart_Model_DbTable_Infoserver::init()
     */
    public function init() {
        $this->setExprSumOnlineNum(self::COLUMN_ONLINENUM);
    }
    
    /**
     * 根据时间设置数据表
     * 
     * @param string $datetime
     * @return void
     */
    public function setTablename($datetime) {
        $date = new Zend_Date($datetime);
        $this->_name = self::TABLE_ONLINENUM . $date->toString('yMMdd');
    }
}