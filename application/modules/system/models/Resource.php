<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_System_Model_Resource
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Resource.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 系统模块的资源管理
 *
 * @name ZtChart_System_Model_Resource
 */
class ZtChart_System_Model_Resource {

    /**
     * 导入模式
     */
    const RESET = 1;
    const APPEND = 2;
    const REPLACE = 3;
    
    /**
     * 
     * @var ZtChart_Model_DbTable_Resource
     */
    protected $_resourceDAO = null;
    
    /**
     * 
     * @var integer
     */
    protected $_mode;
    
    /**
     * 
     * @param ZtChart_Model_DbTable_Resource $resourceDAO
     */
    public function __construct($resourceDAO = null) {
        if (null === $resourceDAO) {
            $resourceDAO = new ZtChart_Model_DbTable_Resource();
        }
        $this->_resourceDAO = $resourceDAO;
    }
    
    /**
     * 清空资源数据
     * 
     * @return integer
     */
    public function truncate() {
        return $this->_resourceDAO->truncate();
    }
    
    /**
     * 根据模式导入Zend_Navigation数据到资源数据表中
     * 
     * @param Zend_Navigation_Container $container
     * @return void
     */
    public function import(Zend_Navigation_Container $container, $mode = null) {
        if (!empty($mode)) {
            $this->_mode = $mode;
        }
        if (self::RESET == $this->_mode) {
            $this->truncate();
        }
        $this->load($container);
    }
    
    /**
     * 导入Zend_Navigation数据到资源数据表中
     * 
     * @param Zend_Navigation_Container $container
     * @param integer $parent
     * @return integer $order
     * @return integer
     * @throws ZtChart_System_Model_Resource_Exception
     */
    public function load(Zend_Navigation_Container $container, $parent = 0, $order = 0) {
        foreach ($container as $page) {
            if (!$page instanceof Zend_Navigation_Page_Mvc) {
                throw new ZtChart_System_Model_Resource_Exception('The Zend_Navigation page must be MVC.');
            }
            if (!$page->getResource()) {
                continue;
            }
            $resource = array(
                'resource_name' => $page->getLabel(), 
                'resource_desc' => $page->desc ?: $page->getLabel(), 
                'resource_mvc' => $page->getResource(), 
                'resource_parent' => $parent, 
                'resource_order' => ++$order, 
                'resource_hash' => new Zend_Db_Expr('MD5(resource_mvc)')
            );
            switch ($this->_mode) {
                case self::RESET:
                case self::APPEND:
                    $resourceId = $this->_resourceDAO->insert($resource);
                    break;
                case self::REPLACE:
                    if (null === ($resourcRow = $this->_resourceDAO->fetchRow(
                                        array('resource_hash = ?' => md5($page->getResource()))))) {
                        $resourceId = $this->_resourceDAO->insert($resource);
                    } else {
                        $resourceId = $resourcRow['resource_id'];
                        $this->_resourceDAO->update($resource, $resourceId);
                    }
                    break;
                default:
                    throw new ZtChart_System_Model_Resource_Exception('The imort mode error.');
            }
            if ($page->hasChildren()) {
                $order = $this->load($page, $resourceId, $order);
            }
        }
        
        return $order;
    }
    
    /**
     * 取得全部资源
     * 
     * @param integer $count
     * @param integer $offset
     * @return array:
     */
    public function fetch($count = null, $offset = 0) {
        return $this->fetchRowset($count, $offset)->toArray();
    }
    
    /**
     * 取得全部资源
     * 
     * @param integer $count
     * @param integer $offset
     * @return ZtChart_Model_Db_Table_Rowset
     */
    public function fetchRowset($count = null, $offset = 0) {
        return $this->_resourceDAO->fetchAll(null, 'resource_order', $count, $offset);
    }
    
    /**
     * 取得所有后代节点ID
     *
     * @param integer $roleId
     * @return array
     */
    public function findDescendants($resourceId) {
        $relation = array();
        foreach ($this->_resourceDAO->fetchAll(array('resource_id > ?' => $resourceId)) as $row) {
            $relation[$row['resource_parent']][] = $row['resource_id'];
        }
        if (!array_key_exists($resourceId, $relation)) {
            $descendants = array();
        } else {
            $descendants = $relation[$resourceId];
            foreach ($relation as $parent => $resources) {
                if (in_array($parent, $descendants)) {
                    $descendants = array_merge($descendants, $resources);
                }
            }
        }
    
        return $descendants;
    }
    
    /**
     * 取得自身及所有后代节点ID
     *
     * @param integer $resourceId
     * @return array
     */
    public function findSelfAndDescendants($resourceId) {
        return array($resourceId) +  $this->findDescendants($resourceId);
    }
    
    /**
     * 取得指定角色的所有后代资源节点ID
     * 
     * @param integer $roleId
     * @return array
     */
    public function findRoleDescendants($roleId) {
        $descendants = array();
        
        $role = new ZtChart_Model_Role($roleId);
        foreach ($role->getResourcesId() as $resourceId) {
            $descendants = array_merge($descendants, $this->findDescendants($resourceId));
        }
        
        return $descendants;
    }
    
    /**
     * 取得指定角色的所有资源节点ID
     * 
     * @param integer $roleId
     * @return array:
     */
    public function findRoleSelfAndDescendants($roleId) {
        $resources = array();
        
        $role = new ZtChart_Model_Role($roleId);
        foreach ($role->getResourcesId() as $resourceId) {
            $resources = array_merge($resources, $this->findDescendants($resourceId), (array) $resourceId);
        }
        
        return $resources;
    }
    
    /**
     * 添加资源
     * 
     * @param array $data
     * @return integer
     */
    public function insert($data) {
        foreach ($data as $key => $value) {
            if (!is_scalar($value)) {
                $data[$key] = Zend_Json::encode($value);
            }
        }
        
        return $this->_resourceDAO->insert($data);
    }
    
    /**
     * 取得角色数据的分页器
     * 
     * @param integer $page
     * @return Zend_Paginator
     */
    public function getPaginator($page = 1) {
        $paginator = Zend_Paginator::factory($this->_resourceDAO->select());
        $paginator->setCurrentPageNumber($page ?: 1);
        
        return $paginator;
    }
}