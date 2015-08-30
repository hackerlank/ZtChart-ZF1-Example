<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_System_Model_Opinion
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Opinion.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统模块的意见表管理
 *
 * @name ZtChart_System_Model_Opinion
 */
class ZtChart_System_Model_Opinion {
    
    /**
     * 
     * @var ZtChart_Model_DbTable_Opinion
     */
    protected $_opinionDAO = null;
    
    /**
     * 
     * @param ZtChart_Model_DbTable_Opinion $opinionDAO
     */
    public function __construct($opinionDAO = null) {
        if (null === $opinionDAO) {
            $opinionDAO = new ZtChart_Model_DbTable_Opinion();
        }
        $this->_opinionDAO = $opinionDAO;
    }
    
    /**
     * 取得全部意见
     * 
     * @param integer $count
     * @param integer $offset
     * @return array
     */
    public function fetch($count = null, $offset = 0) {
        return $this->fetchRowset($count, $offset)->toArray();
    }
    
    /**
     * 取得全部意见
     * 
     * @param integer $count
     * @param integer $offset
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function fetchRowset($count = null, $offset = 0) {
        return $this->_opinionDAO->fetchAll(null, 'opinion_id DESC', $count, $offset);
    }
    
    /**
     * 取得一个意见
     *
     * @param integer $opinionId
     * @return ZtChart_Model_Db_Table_Row
     */
    public function fetchRow($opinionId) {
        return $this->_opinionDAO->fetchRow($opinionId);
    }
    
    /**
     * 添加意见
     * 
     * @param array $data
     * @return integer
     */
    public function insert($data) {
        try {
            return $this->_opinionDAO->insert($data);
        } catch (Zend_Db_Exception $e) {
            return 0;
        } 
    }
    
    /**
     * 修改意见
     * 
     * @param array $data
     * @param integer $opinionId
     * @return integer
     */
    public function update($data, $opinionId) {
        try {
            return $this->_opinionDAO->update($data, $opinionId);
        } catch (Zend_Db_Exception $e) {
            return 0;
        }
    }
    
    /**
     * 删除意见
     * 
     * @param integer $opinionId
     * @return integer
     */
    public function delete($opinionId) {
        return $this->_opinionDAO->delete($opinionId);
    }
    
    /**
     * 开启或关闭意见
     * 
     * @param integer $opinionId
     * @return integer
     */
    public function toogleActive($opinionId) {
        return $this->update(array('opinion_active' => new Zend_Db_Expr('opinion_active XOR 1')), $opinionId);
    }
    
    /**
     * 取得公告数据的分页器
     * 
     * @param integer $page
     * @param array $where
     * @return Zend_Paginator
     */
    public function getPaginator($page = 1, $where = array()) {
        $select = $this->_opinionDAO->select();
        if (!empty($where)) {
            foreach ($where as $cond => $value) {
                switch ($cond) {
                    case 'opinion_submit_datetime':
                        if (!empty($value[0])) {
                            $select->where("{$cond} >= ?", $value[0]);
                        }
                        if (!empty($value[1])) {
                            $select->where("{$cond} <= ?", $value[1]);
                        }
                        break;
                    case 'opinion_status':
                        $select->where("{$cond} = ?", $value);
                        break;
                    default:
                        if (!empty($value)) {
                            $select->where("{$cond} = ?", $value);
                        }
                        break;
                }
            }
        }
        $paginator = Zend_Paginator::factory($select->order('opinion_id DESC'));
        $paginator->setCurrentPageNumber($page ?: 1);
        
        return $paginator;
    }
}