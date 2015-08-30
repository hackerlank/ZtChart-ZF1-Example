<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Acl
 * @subpackage ZtChart_Model_Acl_Loader
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Loader.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 权限分配数据装载器
 *
 * @name ZtChart_Model_Acl_Loader
 */
class ZtChart_Model_Acl_Loader {
    
    /**
     * 
     * @staticvar ZtChart_Model_Acl_Loader
     */
    static private $_instance = null;
    
    /**
     * 
     * @staticvar string
     */
    static protected $_aclDAOClass = 'ZtChart_Model_DbTable_Acl';
    
    /**
     * 
     * @staticvar string
     */
    static protected $_roleDAOClass = 'ZtChart_Model_DbTable_Role';
    
    /**
     * 
     * @staticvar string
     */
    static protected $_resourceDAOClass = 'ZtChart_Model_DbTable_Resource';
    
    /**
     * 
     * @var Zend_Cache_Core
     */
    static protected $_cache = null;
    
    /**
     * 
     * @var Zend_Acl
     */
    protected $_acl;
    
    /**
     * 设置默认的Acl数据表类名
     * 
     * @static
     * @param string $classname
     * @return void
     */
    static public function setAclDAOClass($classname) {
        self::$_aclDAOClass = $classname;
    }
    
    /**
     * 设置默认的角色数据表类名
     *
     * @static
     * @param string $classname
     * @return void
     */
    static public function setRoleDAOClass($classname) {
        self::$_roleDAOClass = $classname;
    }
    
    /**
     * 设置默认的资源数据表类名
     *
     * @static
     * @param string $classname
     * @return void
     */
    static public function setResourceDAOClass($classname) {
        self::$_resourceDAOClass = $classname;
    }
    
    /**
     * 设置缓存对象
     * 
     * @static
     * @param Zend_Cache_Core $cache
     * @return void
     */
    static public function setCache(Zend_Cache_Core $cache) {
        self::$_cache = $cache;
    }
    
    /**
     * 重置缓存对象
     * 
     * @static
     * @return boolean 
     */
    static public function resetCache() {
        if (null !== self::$_cache) {
            return self::$_cache->clean();
        }
        
        return false;
    }
    
    /**
     * 哈希输入的字符串
     *
     * @param string $value
     * @return string
     */
    static public function hash($value) {
        if (null !== $value) {
            $value = Zend_Crypt::hash('md5', $value);
        }
    
        return $value;
    }
    
    /**
     * 返回一个实例
     * 
     * @static
     * @return ZtChart_Model_Acl_Loader
     */
    static public function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * 返回含有权限分配数据的Zend_Acl对象
     * 
     * @param Zend_Acl $acl
     * @param boolean $inCache
     * @return Zend_Acl
     */
    public function load(Zend_Acl $acl = null, $inCache = true) {
        if (null === $acl) {
            $acl = new Zend_Acl();
        }
        
        // 添加角色
        foreach ($this->getRoles($inCache) as $roleRow) {
            /* $acl->addRole($this->_hashRole($roleRow['role_id']), 
                                    $this->_hashRole($roleRow['role_parent'] ?: null)); */
            $acl->addRole($this->_hashRole($roleRow['role_id']));
        }
        $acl->addRole($this->_hashRole(ZtChart_Model_Role::GUEST));
        $acl->addRole($this->_hashRole(ZtChart_Model_Role::ADMIN));
        
        // 添加资源
        foreach ($this->getResources($inCache) as $resourceRow) {
            $acl->addResource($resourceRow['resource_mvc'], $resourceRow['resource_parent_mvc']);
        }
        
        // 添加权限关系
        $assert = new ZtChart_Model_Acl_Assert_GameType();
        foreach ($this->getAcls($inCache) as $aclRow) {
            $acl->allow($this->_hashRole($aclRow['acl_roleid']), 
                                    $aclRow['acl_resourcemvc'], $aclRow['acl_privileges'], $assert);
        }
        $acl->deny($this->_hashRole(ZtChart_Model_Role::GUEST));
        $acl->allow($this->_hashRole(ZtChart_Model_Role::ADMIN));
        
        return $acl;
    }
    
    /**
     * 取得角色数据
     * 
     * @param integer $roleId
     * @param boolean $inCache
     * @return array|null
     */
    public function getRole($roleId, $inCache = true) {
        foreach ($this->getRoles($inCache) as $role) {
            if ($roleId == $this->_hashRole($role['role_id'])) {
                return $role;
            }
        }
        
        return null;
    }
    
    /**
     * 取得资源数据
     * 
     * @param integer $resourceId
     * @param boolean $inCache
     * @return array|null
     */
    public function getResource($resourceId, $inCache = true) {
        foreach ($this->getResources($inCache) as $resource) {
            if ($resourceId == $resource['resource_id']) {
                return $resource;
            }
        }
        
        return null;
    }
    
    /**
     * 取得所有角色数据
     * 
     * @param boolen $inCache
     * @return array
     */
    public function getRoles($inCache = true) {
        return $inCache ? $this->_getRolesInCache() : $this->_getRolesInDb();
    }
    
    /**
     * 取得所有资源数据
     * 
     * @param boolean $inCache
     * @return array
     */
    public function getResources($inCache = true) {
        return $inCache ? $this->_getResourcesInCache() : $this->_getResourcesInDb();
    }
    
    /**
     * 取得所有权限关系数据
     * 
     * @param boolean $inCache
     * @return array
     */
    public function getAcls($inCache = true) {
        return $inCache ? $this->_getAclsInCache() : $this->_getAclsInDb();
    }
    
    /**
     * 清除角色缓存
     * 
     * @return boolean
     */
    public function removeRolesCache() {
        if (null !== self::$_cache) {
            return self::$_cache->remove('role');
        }
        
        return true;
    }
    
    /**
     * 清除资源缓存
     *
     * @return boolean
     */
    public function removeResourcesCache() {
        if (null !== self::$_cache) {
            return self::$_cache->remove('resource');
        }
    
        return true;
    }
    
    /**
     * 清除权限关系缓存
     *
     * @return boolean
     */
    public function removeAclsCache() {
        if (null !== self::$_cache) {
            return self::$_cache->remove('acl');
        }
    
        return true;
    }
    
    /**
     * 取得缓存中的角色数据
     *
     * @return array
     */
    protected function _getRolesInCache() {
        if (null === self::$_cache) {
            throw new ZtChart_Model_Acl_Loader_Exception('The cache for acl is not exist.');
        }
        if (false === ($role = self::$_cache->load('role'))) {
            if (!self::$_cache->save($role = $this->_getRolesInDb(), 'role')) {
                throw new ZtChart_Model_Acl_Loader_Exception('The cache for acl cannot save data.');
            }
        }
        
        return $role;
    }
    
    /**
     * 取得数据库中的角色数据
     * 
     * @return array
     */
    protected function _getRolesInDb() {
        $roleDAO = new self::$_roleDAOClass();
        
        return $roleDAO->fetchAll()->toArray(true);
    }
    
    /**
     * 取得缓存中的资源数据
     *
     * @return array
     */
    protected function _getResourcesInCache() {
        if (null === self::$_cache) {
            throw new ZtChart_Model_Acl_Loader_Exception('The cache for acl is not exist.');
        }
        if (false === ($resource = self::$_cache->load('resource'))) {
            if (!self::$_cache->save($resource = $this->_getResourcesInDb(), 'resource')) {
                throw new ZtChart_Model_Acl_Loader_Exception('The cache for acl cannot save data.');
            }
        }
        
        return $resource;
    }
    
    /**
     * 取得数据库中的资源数据
     * 
     * @return array
     */
    protected function _getResourcesInDb() {
        $resourceDAO = new self::$_resourceDAOClass();
        
        $resourceRowset = array();
        foreach($resourceDAO->fetchAll() as $resourceRow) {
            $resourceRowset[$resourceRow->resource_id] = $resourceRow->toArray();
        }
        foreach ($resourceRowset as &$resourceRow) {
            if (!empty($resourceRow['resource_parent'])) {
                $resourceRow['resource_parent_mvc'] =
                                    $resourceRowset[$resourceRow['resource_parent']]['resource_mvc'];
            } else {
                $resourceRow['resource_parent_mvc'] = null;
            }
        }
        
        return $resourceRowset;
    }
    
    /**
     * 取得缓存中的权限关系数据
     *
     * @return array
     */
    protected function _getAclsInCache() {
        if (null === self::$_cache) {
            throw new ZtChart_Model_Acl_Loader_Exception('The cache for acl is not exist.');
        }
        if (false === ($acl = self::$_cache->load('acl'))) {
            if (!self::$_cache->save($acl = $this->_getAclsInDb(), 'acl')) {
                throw new ZtChart_Model_Acl_Loader_Exception('The cache for acl cannot save data.');
            }
        }
    
        return $acl;
    }
    
    /**
     * 取得数据库中的权限关系数据
     * 
     * @return array 
     */
    protected function _getAclsInDb() {
        $resourceRowset = $this->_getResourcesInDb();
        
        $aclDAO = new self::$_aclDAOClass();
        $aclRowset = $aclDAO->fetchAll()->toArray(true);
        foreach ($aclRowset as &$aclRow) {
            $aclRow['acl_resourcemvc'] = $resourceRowset[$aclRow['acl_resourceid']]['resource_mvc'];
        }
        
        return $aclRowset;
    }
    
    /**
     * 返回哈希处理后的角色ID
     * 
     * @param integer $roleId
     * @rturn string
     */
    protected function _hashRole($roleId) {
        return self::hash($roleId);
    }
    
    /**
     * 返回哈希处理后的资源ID
     * 
     * @param integer $resourceId
     * @return string
     */
    protected function _hashResource($resourceId) {
        return self::hash($resourceId);
    }
    
    /**
     * 实现单例模式，不允许直接实例化一个对象。
     */
    private function __construct() {}
    
    /**
     * 实现单例模式，不允许克隆对象。
     */
    private function __clone() {}
}