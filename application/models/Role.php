<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_User
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Role.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 角色管理
 *
 * @name ZtChart_Model_User
 */
class ZtChart_Model_Role {
    
    /**
     * 访客角色ID
     */
    const GUEST = -1;
    const GUEST_NAME = '未分配';
    
    /**
     * 系统管理员角色ID
     */
    const ADMIN = 0;
    const ADMIN_NAME = '超级管理员';
    
    /**
     * 
     * @var ZtChart_Model_Db_Table_Row
     */
    protected $_roleRow = null;
    
    /**
     * 
     * @var integer
     */
    protected $_roleId = -1;
    
    /**
     * 构造函数，初始化角色数据。
     *
     * @param integer $roleId
     * @param ZtChart_Model_DbTable_Role $roleDAO
     * @throws ZtChart_Model_Role_Exception
     */
    public function __construct($roleId = null, $roleDAO = null) {
        if (null !== $roleId) {
            if (null === ($roleRow = $this->fetchRow($roleId, $roleDAO)) 
                    && $roleId != self::ADMIN && $roleId != self::GUEST) {
                throw new ZtChart_Model_Role_Exception("Role: {$roleId} is not exist.");
            }    
            $this->_roleRow = $roleRow;
        }
        $this->_roleId = $roleId;
    }
    
    /**
     * 取得角色数据对象
     *
     * @param integer $roleId
     * @param ZtChart_Model_DbTable_Role $roleDAO
     * @return ZtChart_Model_Db_Table_Row
     */
    public function fetchRow($roleId, $roleDAO = null) {
        if (null === $roleDAO) {
            $roleDAO = new ZtChart_Model_DbTable_Role();
        }
    
        return $roleDAO->fetchRow(array('role_id = ?' => $roleId));
    }
    
    /**
     * 取得所有子角色
     * 
     * @param integer|null $count 总共取出多少数据
     * @param integer $offset 从哪一行开始取数据
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function getChildRoles($count = null, $offset = 0) {
        $roleDAO = new ZtChart_Model_DbTable_Role();
        
        return $roleDAO->fetchAll($this->isAdmin() ? null : array('role_parent = ?' => $this->_roleId));
    }
    
    /**
     * 取得所有子角色的ID
     * 
     * @param integer|null $count 总共取出多少数据
     * @param integer $offset 从哪一行开始取数据
     * @return array
     */
    public function getChildRolesId($count = null, $offset = 0) {
        return $this->getChildRoles($count, $offset)->getColumn('role_id');
    }
    
    /**
     * 取得自身及所有子角色
     * 
     * @param integer|null $count 总共取出多少数据
     * @param integer $offset 从哪一行开始取数据
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function getSelfAndChildRoles($count = null, $offset = 0) {
        $roleDAO = new ZtChart_Model_DbTable_Role();
        
        return $roleDAO->fetchAll(
                    $this->isAdmin() ? null : $roleDAO->select()->where('role_parent = ?', $this->_roleId)
                                                                ->orWhere('role_id = ?', $this->_roleId));
    }
    
    /**
     * 取得自身及所有子角色的ID
     *
     * @param integer|null $count 总共取出多少数据
     * @param integer $offset 从哪一行开始取数据
     * @return array
     */
    public function getSelfAndChildRolesId($count = null, $offset = 0) {
        return $this->getSelfAndChildRoles($count, $offset)->getColumn('role_id');
    }
    
    /**
     * 取得角色所拥有的资源
     * 
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function getResources() {
        $resourceDAO = new ZtChart_Model_DbTable_Resource();
        $select = $resourceDAO->select()->order('resource_order');
        
        return $this->isAdmin() 
                    ? $resourceDAO->fetchAll($select->where('resource_parent = 0')) 
                    : (!empty($this->_roleRow) 
                            ? $this->_roleRow->findManyToManyRowset(
                                    'ZtChart_Model_DbTable_Resource', 'ZtChart_Model_DbTable_Acl', 
                                    null, null, $select) 
                            : array());
    }
    
    /**
     * 取得角色所拥有的资源ID
     *
     * @return array
     */
    public function getResourcesId() {
        return $this->getResources()->getColumn('resource_id');
    }
    
    /**
     * 取得某个索引序列的模块名称
     *
     * @param string $module
     * @param integer $index
     * @return string
     */
    public function getModuleResource($index = 0) {
        foreach ($this->getResources() as $resourceRow) {
            $pieces = explode(ZtChart_Model_Acl_Resource::SEPARATOR, $resourceRow->resource_mvc);
            if (0 == $index--) {
                return $pieces[0];
            }
        }
    
        return null;
    }
    
    /**
     * 取得指定模块下某个索引序列的控制器名称
     * 
     * @param string $module
     * @param integer $index
     * @return string
     */
    public function getControllerResource($module, $index = 0) {
        foreach ($this->getResources() as $resourceRow) {
            $pieces = explode(ZtChart_Model_Acl_Resource::SEPARATOR, $resourceRow->resource_mvc);
            if ($pieces[0] == $module && count($pieces) > 1) {
                if (0 == $index--) {
                    return $pieces[1];
                }
            }
        }
        
        return null;
    }
    
    /**
     * 取得指定模块控制器下某个索引序列的动作器名称
     *
     * @param string $module
     * @param string $controller
     * @param integer $index
     * @return string
     */
    public function getActionResource($module, $controller, $index = 0) {
        foreach ($this->getResources() as $resourceRow) {
            $pieces = explode(ZtChart_Model_Acl_Resource::SEPARATOR, $resourceRow->resource_mvc);
            if (count($pieces) > 2 && $pieces[0] == $module && $pieces[1] == $controller) {
                if (0 == $index--) {
                    return $pieces[2];
                }
            }
        }
    
        return null;
    }
    
    /**
     * 取得可访问的游戏类型
     * 
     * @param boolean $onlycode
     * @return array
     */
    public function getGameTypes($onlycode = false) {
        $allowedGameTypes = ZtChart_Model_GameType::getGames();
        if (!$this->isAdmin()) {
            $gameTypes = empty($this->_roleRow) ? array() : Zend_Json::decode($this->_roleRow->role_gametype);
            $allowedGameTypes = array_intersect_key($allowedGameTypes, array_fill_keys($gameTypes, null));
        }

        if ($onlycode && is_array($allowedGameTypes)) {
            $allowedGameTypes = array_keys($allowedGameTypes);
        }
        
        return $allowedGameTypes;
    }
    
    /**
     * 是否访客
     * 
     * @return boolean
     */
    public function isGuest() {
        return self::GUEST == $this->_roleId;
    }
    
    /**
     * 是否管理员
     * 
     * @return boolean
     */
    public function isAdmin() {
        return self::ADMIN == $this->_roleId;
    }
    
    /**
     * 取得角色名称
     * 
     * @return string
     */
    public function getRoleName() {
        switch ($this->_roleId) {
            case self::ADMIN:
                return self::ADMIN_NAME;
            case self::GUEST:
                return self::GUEST_NAME;
            default:
                return $this->_roleRow->role_name;
        }
    }
}