<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_User
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: User.php 37158 2012-09-26 09:25:22Z zhangweiwen $
 */

/**
 * 用户管理
 *
 * @name ZtChart_Model_User
 */
class ZtChart_Model_User {

    /**
     * 匿名用户
     */
    const ANONYMOUS_ID = 0;
    const ANONYMOUS_NAME = 'anonymous';
    
    /**
     *
     * @staticvar integer
     */
    static protected $_anonymousRole = ZtChart_Model_Role::GUEST;
    
    /**
     * 
     * @var ZtChart_Model_Db_Table_Row
     */
    protected $_userRow = null;
    
    /**
     * 设置匿名用户的身份
     * 
     * @param integer $role
     * @return void
     */
    static public function setAnonymousRole($role) {
        self::$_anonymousRole = $role;
    }
    
    /**
     * 得到一个匿名用户
     * 
     * @param integer $role
     * @return ZtChart_Model_User
     */
    static public function getAnonymous() {
        return new self(self::ANONYMOUS_NAME);
    }
    
    /**
     * 构造函数，初始化用户数据。
     * 
     * @param string|integer $userNameOrId
     * @param ZtChart_Model_DbTable_User $userDAO
     * @throws ZtChart_Model_User_Exception
     */
    public function __construct($userNameOrId = null, $userDAO = null) {
        if (null !== $userNameOrId) {
            if (null === ($userRow = $this->fetchRow($userNameOrId, $userDAO))) {
                throw new ZtChart_Model_User_Exception("User: {$userNameOrId} is not exist.");
            }
            $this->_userRow = $userRow;
        }
    }
    
    /**
     * 取得用户ID
     *
     * @return integer
     */
    public function getUserId() {
        return (int) $this->_userRow->user_id;
    }
    
    /**
     * 取得用户名
     * 
     * @return string
     */
    public function getUsername() {
        return $this->_userRow->user_name;
    }
    
    /**
     * 取得用户令牌
     * 
     * @param boolean $ignore 如果不存在是否生成一个新的令牌
     * @return string
     */
    public function getTokenring($ignore = false) {
        if ($ignore && empty($this->_userRow->user_tokenring)) {
            $this->_userRow->user_tokenring = md5(uniqid($this->_userRow->user_name));
            $this->_userRow->save();
        }
        
        return $this->_userRow->user_tokenring;
    }
    
    /**
     * 取得用户的角色
     * 
     * @return ZtChart_Model_Role
     */
    public function getRole() {
        return new ZtChart_Model_Role($this->getRoleId());
    }
    
    /**
     * 取得用户的角色ID
     * 
     * @return integer
     */
    public function getRoleId() {
        $roleKey = 'User_' . $this->getUserId() . '_roleId';
        if (!Zend_Registry::isRegistered($roleKey)) {
            if (null === $this->_userRow->findParentRow('ZtChart_Model_DbTable_Role')
                    && ZtChart_Model_Role::ADMIN != $this->_userRow['user_roleid']
                    && ZtChart_Model_Role::GUEST != $this->_userRow['user_roleid']) {
                throw new ZtChart_Model_User_Exception('The user: ' . $this->getUsername()
                        . ' does not belong to any role.');
            }
            Zend_Registry::set($roleKey, $this->_userRow['user_roleid']);
        }
        
        return (int) Zend_Registry::get($roleKey);
    }
    
    /**
     * 用户是否有效
     * 
     * @return boolean
     */
    public function isActive() {
        return 0 != (int) $this->_userRow->user_active;
    }
    
    /**
     * 取得用户数据对象
     * 
     * @param string|integer $userNameOrId
     * @param ZtChart_Model_DbTable_User $userDAO
     * @return ZtChart_Model_Db_Table_Row
     */
    public function fetchRow($userNameOrId, $userDAO = null) {
        if (null === $userDAO) {
            $userDAO = new ZtChart_Model_DbTable_User();
        }
        
        if (self::ANONYMOUS_ID === $userNameOrId || self::ANONYMOUS_NAME == $userNameOrId) {
            $userRow = $userDAO->createRow(array(
                            'user_id' => self::ANONYMOUS_ID, 
                            'user_name' => self::ANONYMOUS_NAME, 
                            'user_roleid' => self::$_anonymousRole, 
                            'user_active' => 1
                        ));
            $userRow->setReadOnly(true);
        } else {
            $userRow = $userDAO->fetchRow(
                            array(is_numeric($userNameOrId) ? 'user_id = ?' 
                                                            : 'user_name = ?' 
                                    => $userNameOrId));;
        }
        
        return $userRow;
    }
    
    /**
     * 是否匿名用户
     * 
     * @return boolean
     */
    public function isAnonymous() {
        return $this->_anonymous;
    }
    
    /**
     * 是否拥有该角色
     * 
     * @param integer $roleId
     * @return boolean
     */
    public function hasRole($roleId) {
        return in_array($roleId, $this->getRole()->getSelfAndChildRolesId());
    }
    
    /**
     * 用户是否属于所拥有的角色
     * 
     * @param integer $userId
     * @return boolean
     */
    public function hasRoleUser($userId) {
        $user = new self($userId);
        
        return ZtChart_Model_Role::GUEST == $user->getRoleId() || $this->hasRole($user->getRoleId());
    }
}