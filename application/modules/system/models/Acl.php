<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_System_Model_Acl
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Acl.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统模块的权限表管理
 *
 * @name ZtChart_System_Model_Acl
 */
class ZtChart_System_Model_Acl {
    
    /**
     * 
     * @var ZtChart_Model_DbTable_Acl
     */
    protected $_aclDAO = null;
    
    /**
     * 
     */
    public function __construct() {
        $this->_aclDAO = new ZtChart_Model_DbTable_Acl();
    }
    
    /**
     * 添加权限
     * 
     * @param array $data
     * @return integer
     */
    public function insert($data) {
        if (array_key_exists('acl_privileges', $data)) {
            if (!empty($data['acl_privileges']) && !is_scalar($data['acl_privileges'])) {
                $data['acl_privileges'] = Zend_Json::encode($data['acl_privileges']);
            }
        }
        return $this->_aclDAO->insert($data);
    }
    
    /**
     * 批量添加权限
     * 
     * @param array $resources
     * @param integer $roleId
     * @return array
     */
    public function batchInsert($resources, $roleId) {
        $insertId = array();
        if (!empty($roleId)) {
            foreach ((array) $resources as $resourceId => $privileges) {
                $data = array(
                    'acl_roleid' => $roleId, 
                    'acl_resourceid' => $resourceId, 
                    'acl_privileges' => $privileges
                );
                $insertId[] = $this->insert($data);
            }
        }
        
        return $insertId;
    }
    
    /**
     * 重新设置权限
     * 
     * @param array $resources
     * @param integer $roleId
     * @return array
     */
    public function resetRole($resources, $roleId) {
        $this->removeRole($roleId);
        
        return $this->batchInsert($resources, $roleId);
    }
    
    /**
     * 根据角色信息删除记录
     * 
     * @param integer $roleId
     * @return integer
     */
    public function removeRole($roleId) {
        return $this->_aclDAO->delete(array('acl_roleid = ?' => $roleId));
    }
}