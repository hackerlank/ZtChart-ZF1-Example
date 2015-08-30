<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Db
 * @subpackage ZtChart_Model_Db_Adapter_Pdo
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Mysql.php 37415 2012-11-15 08:14:57Z zhangweiwen $
 */

/**
 * MySQL数据库适配器类
 *
 * @name ZtChart_Model_Db_Adapter_Pdo_Mysql
 * @see Zend_Db_Adapter_Pdo_Mysql
 */
class ZtChart_Model_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql {
    
    /**
     * Creates a PDO object and connects to the database.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }
    
        if (!empty($this->_config['timeout'])) {
            $this->_config['driver_options'][PDO::ATTR_TIMEOUT] = $this->_config['timeout'];
        }
    
        parent::_connect();
    }
    
    /**
     * Retrieve server info in PHP style
     *
     * @return string
     */
    public function getServerInfo() {
        $this->_connect();
        try {
            $info = $this->_connection->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (PDOException $e) {
            // In case of the driver doesn't support getting info
            return null;
        }
        
        return $info;
    }
    
    /**
     * 取得数据表的引擎类型
     * 
     * @param string $tableName
     * @param string $schemaName
     * @return string
     * @throws Zend_Db_Exception
     */
    public function getEngine($tableName, $schemaName = null) {
        if (!preg_match('/ENGINE=(\w+) /iU', $this->showCreateTable($tableName, $schemaName), $match)) {
            throw new Zend_Db_Exception('Cannot get the MySQL table engine.');
        }
        
        return $match[1];
    }
    
    /**
     * 取得数据表的创建语句
     * 
     * @param string $tableName
     * @param string $schemaName
     * @return string
     */
    public function showCreateTable($tableName, $schemaName = null) {
        if ($schemaName) {
            $sql = 'SHOW CREATE TABLE ' . $this->quoteIdentifier("$schemaName.$tableName", true);
        } else {
            $sql = 'SHOW CREATE TABLE ' . $this->quoteIdentifier($tableName, true);
        }
        $stmt = $this->query($sql);
        
        return $stmt->fetchAll(Zend_Db::FETCH_COLUMN | Zend_Db::FETCH_UNIQUE);
    }
    
    /**
     * 清空数据表
     * 
     * @param mxied $table
     * @return integer
     */
    public function truncate($table) {
        try {
            $sql = "TRUNCATE TABLE " . $this->quoteIdentifier($table, true);
            $result = $this->exec($sql);
        } catch (Zend_Db_Exception $e) {
            $result = $this->delete($table);
            $this->query("ALTER TABLE {$table} AUTO_INCREMENT = 1");
        }
        
        return $result;
    }
}