<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package System_View
 * @subpackage System_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: RoleList.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 视图助手——角色数据显示
 *
 * @name System_View_Helper_RoleList
 * @see Zend_View_Helper_Abstract
 */
class System_View_Helper_RoleList extends Zend_View_Helper_Abstract {

    /**
     * 默认表单属性
     * 
     * @var array
     */
    protected $_attributes = array(
        'select' => array('name' => 'formSelect'), 
        'radio' => array('name' => 'formRadio')
    );
    
    /**
     * 以指定的格式输出角色列表
     * 
     * @param integer|null $roleId 父角色ID
     * @param array $attributes 需要显示的HTML标签的属性
     * @param string $style 输出的HTML类型，为空则返回PHP数组
     * @param integer|null $count 总共取出多少数据
     * @param integer $offset 从哪一行开始取数据
     * @return string|array
     */
    public function roleList($roleId, $attributes = array(), $style = 'select', $count = null, $offset = 0) {
        $role = new ZtChart_Model_Role($roleId);
        $rowset = $role->getSelfAndChildRoles($count, $offset);
        if (empty($style)) {
            return $rowset->toArray();
        }
        
        return $this->_html($rowset, $style, $attributes);
    }
    
    
    /**
     * 把结果集以指定的HTML方式输出
     * 
     * @param ZtChart_Model_DbTable_Role_Rowset $rowset 数据结果集
     * @param string $style 输出的HTML类型
     * @param array $attributes 需要显示的HTML标签的属性
     * @return string
     */
    protected function _html(ZtChart_Model_DbTable_Role_Rowset $rowset, $style, $attributes = null) {
        $htmlMethod = '_html' . ucfirst($style);
        if (!method_exists($this, $htmlMethod)) {
            throw new Zend_View_Exception('The method is not exist.');
        }
        return call_user_func(array($this, $htmlMethod), $rowset, $attributes);
    }
    
    /**
     * 以<select>标签的形式输出
     * 
     * @param ZtChart_Model_DbTable_Role_Rowset $rowset 数据结果集
     * @param array $attributes 需要显示的HTML标签的属性
     * @return string
     */
    protected function _htmlSelect(ZtChart_Model_DbTable_Role_Rowset $rowset, $attributes = array()) {
        if (isset($attributes['options'])) {
            $options = $attributes['options'];
            unset($attributes['options']);
        } else {
            $options = array();
        }
        
        $htmlSelect = new Zend_Form_Element_Select(array_merge($this->_attributes['select'], $attributes));
        $htmlSelect->clearDecorators()->addDecorator('ViewHelper');
        $htmlSelect->addMultiOptions($options);
        foreach ($rowset as $row) {
            $htmlSelect->addMultiOption($row->role_id, $row->role_name);
        }
        
        return $htmlSelect;
    }
    
    /**
     * 以<radio>标签的形式输出
     * 
     * @param ZtChart_Model_DbTable_Role_Rowset $rowset
     * @param array $attributes
     */
    protected function _htmlRadio(ZtChart_Model_DbTable_Role_Rowset $rowset, $attributes = array()) {
        if (isset($attributes['options'])) {
            $options = $attributes['options'];
            unset($attributes['options']);
        } else {
            $options = array();
        }
        
        $htmlRadio = new Zend_Form_Element_Radio(array_merge($this->_attributes['radio'], $attributes));
        $htmlRadio->clearDecorators()->addDecorator('ViewHelper');
        $htmlRadio->addMultiOptions($options);
        foreach ($rowset as $row) {
            $htmlRadio->addMultiOption($row->role_id, $row->role_name);
        }
        
        return $htmlRadio;
    }
}