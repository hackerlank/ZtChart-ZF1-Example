<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Cache
 * @subpackage ZtChart_Model_Cache_Backend
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Memcached.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * Memcached
 *
 * @see Zend_Cache_Backend_Memcached
 * @name ZtChart_Model_Cache_Backend_Memcached
 */
class ZtChart_Model_Cache_Backend_Memcached extends Zend_Cache_Backend_Memcached {

    /**
     * 将指定元素的值增加value
     * 
     * @param string $id
     * @param integer $value
     * @return boolean
     */
    public function increment($id, $value = 1) {
        return $this->_memcache->increment($id, $value);
    }
    
    /**
     * 将指定元素的值减少value
     * 
     * @param string $id
     * @param integer $value
     * @return boolean
     */
    public function decrement($id, $value = 1) {
        return $this->_memcache->decrement($id, $value);
    }
}