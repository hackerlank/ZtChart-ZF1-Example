<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_System_Model_Role
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Role.php 35736 2012-06-27 09:55:59Z zhangweiwen $
 */

/**
 * 系统模块的角色表管理
 *
 * @name ZtChart_System_Model_Role
 */
class ZtChart_System_Model_Role {

    /**
     * 
     * @var ZtChart_Model_DbTable_Role
     */
    protected $_roleDAO = null;
    
    /**
     * 
     * @param ZtChart_Model_DbTable_Role $roleDAO
     */
    public function __construct($roleDAO = null) {
        if (null === $roleDAO) {
            $roleDAO = new ZtChart_Model_DbTable_Role();
        }
        $this->_roleDAO = $roleDAO;
    }
    
    /**
     * 取得全部角色
     * 
     * @param integer|null $parentRoleId
     * @param integer $count
     * @param integer $offset
     * @return array
     */
    public function fetch($parentRoleId = null, $count = null, $offset = 0) {
        return $this->fetchRowset($parentRoleId, $count, $offset)->toArray();
    }
    
    /**
     * 取得全部角色
     *
     * @param integer|null $parentRoleId
     * @param integer $count
     * @param integer $offset
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function fetchRowset($parentRoleId = null, $count = null, $offset = 0) {
        if (null !== $parentRoleId) {
            $parentRoleId = array('role_parent = ?' => $parentRoleId);
        }
        
        return $this->_roleDAO->fetchAll($parentRoleId, null, $count, $offset);
    }
    
    /**
     * 取得一个角色
     * 
     * @param integer $roleId
     * @return ZtChart_Model_Db_Table_Row
     */
    public function fetchRow($roleId) {
        return $this->_roleDAO->fetchRow($roleId);
    }
    
    /**
     * 取得所有后代节点ID
     * 
     * @param integer $roleId
     * @return array
     */
    public function findDescendants($roleId) {
        $relation = array();
        foreach ($this->_roleDAO->fetchAll(array('role_id > ?' => $roleId)) as $row) {
            $relation[$row['role_parent']][] = $row['role_id'];
        }
        if (!array_key_exists($roleId, $relation)) {
            $descendants = array();
        } else {
            $descendants = $relation[$roleId];
            foreach ($relation as $parent => $roles) {
                if (in_array($parent, $descendants)) {
                    $descendants = array_merge($descendants, $roles);
                }
            }
        }
        
        return $descendants;
    }
    
    /**
     * 取得自身及所有后代节点ID
     * 
     * @param integer $roleId
     * @return array
     */
    public function findSelfAndDescendants($roleId) {
        return array($roleId) +  $this->findDescendants($roleId);
    }
    
    /**
     * 添加角色
     * 
     * @param array $data
     * @return integer
     */
    public function insert($data) {
        if (array_key_exists('role_gametype', $data)) {
            if (!is_scalar($data['role_gametype'])) {
                $data['role_gametype'] = Zend_Json::encode($data['role_gametype']);
            }
        }
        
        return $this->_roleDAO->insert($data);
    }
    
    /**
     * 修改角色
     * 
     * @param array $data
     * @param integer $roleId
     * @param boolean $ommit
     * @return integer
     */
    public function update($data, $roleId, $ommit = true) {
        if ($ommit) {
            $data = $this->_roleDAO->filterColumns($data, true);
        }
        if (array_key_exists('role_gametype', $data)) {
            if (!is_scalar($data['role_gametype'])) {
                $data['role_gametype'] = Zend_Json::encode($data['role_gametype']);
            }
        }
        
        return $this->_roleDAO->update($data, $roleId);
    }
    
    /**
     * 删除角色
     * 
     * @param integer|array $roleId
     * @return integer
     */
    public function delete($roleId) {
        if (!$this->hasUser($roleId)) {
            return $this->_roleDAO->delete($roleId);
        }
        
        return 0;
    }
    
    /**
     * 取得角色允许访问的资源
     * 
     * @param integer $roleId
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function findResource($roleId) {
        if (null !== ($role = $this->fetchRow($roleId))) {
            return $role->findManyToManyRowset('ZtChart_Model_DbTable_Resource', 'ZtChart_Model_DbTable_Acl');
        }
        
        return null;
    }
    
    /**
     * 取得角色允许访问的资源的ID
     * 
     * @param integer $roleId
     * @return array
     */
    public function fetchResourceId($roleId) {
        if (null !== ($resource = $this->findResource($roleId))) {
            return $resource->getColumn('resource_id');
        }
        
        return array();
    }
    
    /**
     * 取得拥有该角色的所有用户
     * 
     * @param integer $roleId
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function findUser($roleId) {
        if (null !== ($role = $this->fetchRow($roleId))) {
            return $role->findDependentRowset('ZtChart_Model_DbTable_User');
        }
        
        return null;
    }
    
    /**
     * 该角色是否拥有用户
     * 
     * @param integer $roleId
     * @return boolean
     */
    public function hasUser($roleId) {
        return null !== ($user = $this->findUser($roleId)) && 0 != $user->count();
    }
    
    /**
     * 保存角色的权限
     * 
     * @param array|integer $resourceId
     * @param array $allowedResourceId
     * @param integer $roleId
     * @return boolean
     */
    public function saveAcl($resourceId, $allowedResourceId, $roleId) {
        if (!is_array($resourceId)) {
            $resourceId = (array) $resourceId;
        }
        $resourceId = array_intersect($resourceId, $allowedResourceId);
        $acl = new ZtChart_System_Model_Acl();
        
        return count($resourceId) == count($acl->resetRole(array_fill_keys($resourceId, null), $roleId));
    }
    
    /**
     * 取得角色数据的分页器
     * 
     * @param ZtChart_Model_Role $roleId
     * @param integer $page
     * @return Zend_Paginator
     */
    public function getPaginator($role, $page = 1) {
        $paginator = Zend_Paginator::factory($role->getChildRoles());
        $paginator->setCurrentPageNumber($page ?: 1);
        
        return $paginator;
    }
}