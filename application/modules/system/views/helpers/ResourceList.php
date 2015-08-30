<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package System_View
 * @subpackage System_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: ResourceList.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 视图助手——资源数据显示
 *
 * @name System_View_Helper_ResourceList
 * @see Zend_View_Helper_Abstract
 */
class System_View_Helper_ResourceList extends Zend_View_Helper_Abstract {

    /**
     * 
     * @var array
     */
    protected $_resourceRowset = null;
    
    /**
     * 以指定的格式输出资源列表
     * 
     * @param boolean $style 直接输出数组还是按样式输出
     * @param integer $count 总数
     * @param integer $offset 起始位置
     * @return array|ZtChart_System_View_Helper_ResourceList
     */
    public function resourceList($style = true, $count = null, $offset = 0) {
        $resource = new ZtChart_System_Model_Resource();
        $rowset = $resource->fetchRowset($count, $offset);
        if (!$style) {
            return $rowset->toArray();
        }
        $this->_resourceRowset = $rowset;
        
        return $this;
    }
    
    /**
     * 以<select>标签的形式输出
     * 
     * @param array $attributes 需要显示的HTML标签的属性
     * @return string
     */
    public function renderSelect($attributes = array()) {
        if (isset($attributes['options'])) {
            $options = $attributes['options'];
            unset($attributes['options']);
        } else {
            $options = array();
        }
        
        $htmlSelect = new Zend_Form_Element_Select(array_merge(array('name' => 'formSelect'), $attributes));
        $htmlSelect->clearDecorators()->addDecorator('ViewHelper');
        $htmlSelect->addMultiOptions($options);
        
        foreach ($this->_resourceRowset as $row) {
            $prefix = str_repeat('　', 2 * (substr_count($row['resource_mvc'], 
                                                    ZtChart_Model_Acl_Resource::SEPARATOR) + 1));
            $htmlSelect->addMultiOption($row['resource_id'], $prefix . $row['resource_name']);
        }
        
        return $htmlSelect;
    }
    
    /**
     * 以<ul>标签的形式输出
     * 
     * @param array|integer $checkedResourceId 需要设置为checked的元素
     * @param array $allowedResourceId 允许显示的元素
     * @param array $attributes 需要显示的HTML标签的属性
     * @return string
     */
    public function renderList($checkedResourceId = array(), $allowedResourceId = null, $attributes = array()) {
        $prevDepth = -1;
        $html = '';
        foreach ($this->_resourceRowset as $row) {
            $depth = substr_count($row['resource_mvc'], ZtChart_Model_Acl_Resource::SEPARATOR) + 1;
            $indent = str_repeat('        ', $depth);
            if ($depth > $prevDepth) {
                // start new ul tag
                if (isset($attributes['ulClass'])) {
                    $ulClass = ' class="' . $attributes['ulClass'] . '"';
                } else {
                    $ulClass = '';
                }
                $html .= $indent . '<ul' . $ulClass . '>' . PHP_EOL;
            } else if ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . PHP_EOL;
                    $html .= $ind . '</ul>' . PHP_EOL;
                }
                // close previous li tag
                $html .= $indent . '    </li>' . PHP_EOL;
            } else {
                // close previous li tag
                $html .= $indent . '    </li>' . PHP_EOL;
            }
            
            $checked = '';
            $disabled = '';
            if (!empty($checkedResourceId) && (in_array($row['resource_id'], $checkedResourceId) 
                    || in_array($row['resource_parent'], $checkedResourceId))) {
                if (!empty($row['resource_parent']) && !in_array($row['resource_id'], $checkedResourceId)) {
                    $checkedResourceId[] = $row['resource_id'];
                }
                $checked = ' checked="checked"';
                if (in_array($row['resource_parent'], $checkedResourceId)) {
                    $disabled = ' disabled="disabled"';
                } 
            } 
            if (is_array($allowedResourceId) && !in_array($row['resource_id'], $allowedResourceId)) {
                $disabled = ' disabled="disabled"';
            }
            
            // render li tag and page
            if (isset($attributes['liClass'])) {
                $liClass = ' class="' . $attributes['liClass'] . '_' . $depth . '"';
            } else {
                $liClass = '';
            }
            
            $input = '<input type="checkbox" name="resource_id[]"' 
                    . ' class="resource_id"' . $checked . $disabled 
                    . ' value="' . $row['resource_id'] . '" /> ';
            $html .= $indent . '    <li' . $liClass . '>' . PHP_EOL
                    . $indent . '        ' . $input
                    . $row['resource_name'] . PHP_EOL;
            
            // store as previous depth for next iteration
            $prevDepth = $depth;
        }
        
        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 1; $i > 0; $i--) {
                $indent = str_repeat('        ', $i - 1);
                $html .= $indent . '    </li>' . PHP_EOL
                        . $indent . '</ul>' . PHP_EOL;
            }
            $html = rtrim($html, PHP_EOL);
        }
        
        return $html;
    }
}