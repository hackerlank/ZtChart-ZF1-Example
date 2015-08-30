<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_System_Model_User
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: User.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统模块的用户表管理
 *
 * @name ZtChart_System_Model_User
 */
class ZtChart_System_Model_User {
    
    /**
     * 
     * @var ZtChart_Model_DbTable_User
     */
    protected $_userDAO = null;
    
    /**
     * 
     * @param ZtChart_Model_DbTable_User $userDAO
     */
    public function __construct($userDAO = null) {
        if (null === $userDAO) {
            $userDAO = new ZtChart_Model_DbTable_User();
        }
        $this->_userDAO = $userDAO;
    }
    
    /**
     * 取得全部用户
     * 
     * @param integer $count
     * @param integer $offset
     * @return array
     */
    public function fetch($count = null, $offset = 0) {
        return $this->fetchRowset($count, $offset)->toArray();
    }
    
    /**
     * 取得全部用户
     * 
     * @param integer $count
     * @param integer $offset
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function fetchRowset($count = null, $offset = 0) {
        return $this->_userDAO->fetchAll(null, null, $count, $offset);
    }
    
    /**
     * 取得一个用户
     *
     * @param integer $userId
     * @return ZtChart_Model_Db_Table_Row
     */
    public function fetchRow($userId) {
        return $this->_userDAO->fetchRow($userId);
    }
    
    /**
     * 添加用户
     * 
     * @param array $data
     * @return integer
     */
    public function insert($data) {
        try {
            return $this->_userDAO->insert($data);
        } catch (Zend_Db_Exception $e) {
            return 0;
        } 
    }
    
    /**
     * 修改用户
     * 
     * @param array $data
     * @param integer $userId
     * @return integer
     */
    public function update($data, $userId) {
        try {
            return $this->_userDAO->update($data, $userId);
        } catch (Zend_Db_Exception $e) {
            return 0;
        }
    }
    
    /**
     * 删除用户
     * 
     * @param integer $userId
     * @return integer
     */
    public function delete($userId) {
        return $this->_userDAO->delete($userId);
    }
    
    /**
     * 开启或关闭用户
     * 
     * @param integer $userId
     * @return integer
     */
    public function toogleActive($userId) {
        return $this->update(array('user_active' => new Zend_Db_Expr('user_active XOR 1')), $userId);
    }
    
    /**
     * 取得用户数据的分页器
     * 
     * @param integer $page
     * @param array $where
     * @return Zend_Paginator
     */
    public function getPaginator($page = 1, $where = array()) {
        $select = $this->_userDAO->select();
        if (!empty($where)) {
            foreach (array_filter($where) as $cond => $value) {
                $select->where("{$cond} = ?", $value);
            }
        }
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page ?: 1);
        
        return $paginator;
    }
}