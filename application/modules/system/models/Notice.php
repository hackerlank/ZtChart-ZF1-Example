<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_System_Model_Notice
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Notice.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统模块的公告表管理
 *
 * @name ZtChart_System_Model_Notice
 */
class ZtChart_System_Model_Notice {
    
    /**
     * 
     * @var ZtChart_Model_DbTable_Notice
     */
    protected $_noticeDAO = null;
    
    /**
     * 
     * @param ZtChart_Model_DbTable_Notice $noticeDAO
     */
    public function __construct($noticeDAO = null) {
        if (null === $noticeDAO) {
            $noticeDAO = new ZtChart_Model_DbTable_Notice();
        }
        $this->_noticeDAO = $noticeDAO;
    }
    
    /**
     * 取得全部公告
     * 
     * @param integer $count
     * @param integer $offset
     * @return array
     */
    public function fetch($count = null, $offset = 0) {
        return $this->fetchRowset($count, $offset)->toArray();
    }
    
    /**
     * 取得全部公告
     * 
     * @param integer $count
     * @param integer $offset
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function fetchRowset($count = null, $offset = 0) {
        return $this->_noticeDAO->fetchAll(null, 'notice_id DESC', $count, $offset);
    }
    
    /**
     * 取得一个公告
     *
     * @param integer $noticeId
     * @return ZtChart_Model_Db_Table_Row
     */
    public function fetchRow($noticeId) {
        return $this->_noticeDAO->fetchRow($noticeId);
    }
    
    /**
     * 添加公告
     * 
     * @param array $data
     * @return integer
     */
    public function insert($data) {
        try {
            return $this->_noticeDAO->insert($data);
        } catch (Zend_Db_Exception $e) {
            return 0;
        } 
    }
    
    /**
     * 修改公告
     * 
     * @param array $data
     * @param integer $noticeId
     * @return integer
     */
    public function update($data, $noticeId) {
        try {
            return $this->_noticeDAO->update($data, $noticeId);
        } catch (Zend_Db_Exception $e) {
            return 0;
        }
    }
    
    /**
     * 删除公告
     * 
     * @param integer $noticeId
     * @return integer
     */
    public function delete($noticeId) {
        return $this->_noticeDAO->delete($noticeId);
    }
    
    /**
     * 开启或关闭公告
     * 
     * @param integer $noticeId
     * @return integer
     */
    public function toogleActive($noticeId) {
        return $this->update(array('notice_active' => new Zend_Db_Expr('notice_active XOR 1')), $noticeId);
    }
    
    /**
     * 取得公告数据的分页器
     * 
     * @param integer $page
     * @return Zend_Paginator
     */
    public function getPaginator($page = 1) {
        $paginator = Zend_Paginator::factory($this->_noticeDAO->select()->order('notice_id DESC'));
        $paginator->setCurrentPageNumber($page ?: 1);
        
        return $paginator;
    }
}