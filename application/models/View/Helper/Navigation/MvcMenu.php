<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_View
 * @subpackage ZtChart_Model_View_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: MvcMenu.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 全局视图助手——MVC菜单显示
 *
 * @name ZtChart_Model_View_Helper_Navigation_MvcMenu
 * @see Zend_View_Helper_Navigation_Menu
 */
class ZtChart_Model_View_Helper_Navigation_MvcMenu extends Zend_View_Helper_Navigation_Menu {

    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zend_Navigation_Container $container              [optional] container to
     *                                                           operate on
     * @return ZtChart_Model_View_Helper_Navigation_MvcMenu     fluent interface,
     *                                                           returns self
     */
    public function mvcMenu(Zend_Navigation_Container $container = null) {
        return $this->menu($container);
    }
    
    /**
     * 显示指定MVC菜单
     * 
     * @param string $action 动作器名称
     * @param string $controller 控制器名称
     * @param string $module 模块名称
     * @param array $params 路由参数
     * @param array $options 表单选项
     * @return string
     */
    public function renderMVCMenu($action, $controller = null, $module = null, 
                                                $params = array(), $options = array()) {
        $html = '';
        if (false !== ($page = $this->acceptMVC($action, $controller, $module, $params))) {
            $page->setOptions($options);
            $html = $this->htmlify($page);
        }
        
        return $html;
    }
    
    /**
     * 显示指定MVC菜单，忽略显示控制。
     * 
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @param array $options
     * @return string
     */
    public function renderMVCMenuAlwaysVisible($action, $controller = null, $module = null, 
                                                $params = array(), $options = array()) {
        $html = '';
        if (false !== ($page = $this->acceptMVC($action, $controller, $module, $params, true))) {
            $page->setOptions($options);
            $html = $this->htmlify($page);
        }
        
        return $html;
    }
    
    /**
     * 指定MVC菜单是否允许显示
     * 
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     * @param boolean $ignoreVisible
     * @return false|Zend_Navigation_Page
     */
    public function acceptMVC($action, $controller = null, $module = null, 
                                        $params = array(), $ignoreVisible = false) {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if (null === $controller) {
            $controller = $request->getControllerName();
        }
        if (null === $module) {
            $module = $request->getModuleName();
        }
        
        $container = $this->getContainer();
        if (null !== ($page = $container->findOneBy('module', $module))) {
            if (null !== ($page = $page->findOneBy('controller', $controller))) {
                if (!empty($action)) {
                    $page = $page->findOneBy('action', $action);
                }
                if ($page instanceof Zend_Navigation_Page) {
                    $page = clone $page;
                    if (!empty($params)) {
                        $page->setParams($params);
                    }
                    if ($ignoreVisible && !$page->isVisible()) {
                        $page->setVisible(true);
                    }
                    if ($this->accept($page)) {
                        return $page;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * 显示当前的菜单项
     * 
     * @param boolean $link
     * @param boolean $acl
     * @return string
     */
    public function renderCurrentMenu(Zend_Controller_Request_Http $request = null, 
                                                                $link = false, $acl = false) {
        $html = '';
        if (null === $request) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
        }
        
        $container = $this->getContainer();
        if (null !== ($page = $container->findOneBy('module', $request->getModuleName()))) {
            if (null !== ($page = $page->findOneBy('controller', $request->getControllerName()))) {
                if (null !== ($page = $page->findOneBy('action', $request->getActionName()))) {
                    if (!$acl || $this->accept($page)) {
                        $html = $link ? $this->htmlify($page) : $page->getLabel();
                    }
                }
            }
        }
        
        return $html;
    }
    
    /**
     * 显示模块级别下的所有菜单
     *
     * @see Zend_View_Helper_Navigation_Menu::renderMenu()
     * $param  integer                   $depth      the max depth
     * @param  string                    $ulClass    [optional] CSS class to
     *                                               use for UL element. Default
     *                                               is to use the value from
     *                                               {@link getUlClass()}.
     * @param  string|int                $indent     [optional] indentation as
     *                                               a string or number of
     *                                               spaces. Default is to use
     *                                               the value retrieved from
     *                                               {@link getIndent()}.
     * @param  boolean                   $recursive  whether page should be considered
     *                                               active if any child pages are active.
     * @return string                                rendered content
     */
    public function renderModuleMenu($depth = 0, $ulClass = null, $indent = null, $recursive = false) {
        return $this->_renderMVCLevelMenu(null, 0, $depth, $ulClass, $indent, $recursive);
    }
    
    /**
     * 显示控制器级别下的所有菜单
     * 
     * @see Zend_View_Helper_Navigation_Menu::renderMenu()
     * @param  string                      $moduleName 
     * @param  integer                   $depth      the max depth
     * @param  string                    $ulClass    [optional] CSS class to
     *                                               use for UL element. Default
     *                                               is to use the value from
     *                                               {@link getUlClass()}.
     * @param  string|int                $indent     [optional] indentation as
     *                                               a string or number of
     *                                               spaces. Default is to use
     *                                               the value retrieved from
     *                                               {@link getIndent()}.
     * @param  boolean                   $recursive  whether page should be considered
     *                                               active if any child pages are active.
     * @return string                                rendered content
     */
    public function renderControllerMenu($moduleName, $depth = 0, $ulClass = null, 
                                                        $indent = null, $recursive = false) {
        $container = $this->getContainer()->findOneBy('module', $moduleName);
        
        return $this->_renderMVCLevelMenu($container, 0, $depth, $ulClass, $indent, $recursive);
    }
    
    /**
     * 显示动作级别下的所有菜单
     *
     * @see Zend_View_Helper_Navigation_Menu::renderMenu()
     * @param  string                      $moduleName  
     * @param  string                    $controllerName
     * @param  integer                   $depth      the max depth
     * @param  string                    $ulClass    [optional] CSS class to
     *                                               use for UL element. Default
     *                                               is to use the value from
     *                                               {@link getUlClass()}.
     * @param  string|int                $indent     [optional] indentation as
     *                                               a string or number of
     *                                               spaces. Default is to use
     *                                               the value retrieved from
     *                                               {@link getIndent()}.
     * @param  boolean                   $recursive  whether page should be considered
     *                                               active if any child pages are active.
     * @return string                                rendered content
     */
    public function renderActionMenu($moduleName, $controllerName, $depth = 0, 
                                                            $ulClass = null, $indent = null, $recursive = false) {
        if (null !== ($container = $this->getContainer()->findOneBy('module', $moduleName))) {
            $container = $container->findOneBy('controller', $controllerName);
        }
        
        return $this->_renderMVCLevelMenu($container, 0, $depth, $ulClass, $indent, $recursive);
    }
    
    /**
     * 显示MVC相应级别的所有菜单
     *
     * @see Zend_View_Helper_Navigation_Menu::renderMenu()
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               render. Default is to render
     *                                               the container registered in
     *                                               the helper.
     * @param  integer                   $minDepth   the min depth
     * @param  integer                   $maxDepth   the max depth
     * @param  string                    $ulClass    [optional] CSS class to
     *                                               use for UL element. Default
     *                                               is to use the value from
     *                                               {@link getUlClass()}.
     * @param  string|int                $indent     [optional] indentation as
     *                                               a string or number of
     *                                               spaces. Default is to use
     *                                               the value retrieved from
     *                                               {@link getIndent()}.
     * @param  boolean                     $recursive  whether page should be considered
     *                                               active if any child pages are active.
     * @return string                                rendered content
     */
    protected function _renderMVCLevelMenu(Zend_Navigation_Container $container = null, 
                                            $minDepth = null, $maxDepth = null, $ulClass = null, 
                                            $indent = null, $recursive = false) {
        $html = '';
    
        // create iterator
        $iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }
    
        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive($recursive);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibilty
                continue;
            } 
    
            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);
    
            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $depth ==  0) {
                    $ulClass = ' class="' . $ulClass . '"';
                } else {
                    $ulClass = '';
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . self::EOL;
            } else if ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . self::EOL;
                    $html .= $ind . '</ul>' . self::EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            }
            
            // render li tag and page
            $liClass = implode(' ', array($page->get('liclass'), $isActive ? 'active' : ''));
            $liClass = !empty($liClass) ? ' class="' . $liClass . '"': '';
            $html .= $myIndent . '    <li' . $liClass . '>' . self::EOL
            . $myIndent . '        ' . $this->htmlify($page) . self::EOL;
    
            // store as previous depth for next iteration
            $prevDepth = $depth;
        }
    
        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i-1);
                $html .= $myIndent . '    </li>' . self::EOL
                . $myIndent . '</ul>' . self::EOL;
            }
            $html = rtrim($html, self::EOL);
        }
    
        return $html;
    }

    /**
     * 判断菜单权限，如果某页面存在至少一个子页面，则显示。
     * 
     * @see Zend_View_Helper_Navigation_HelperAbstract::accept()
     * @param Zend_Navigation_Page $page
     * @param boolean $recursive
     * @return boolean
     */
    public function accept(Zend_Navigation_Page $page, $recursive = false) {
        if (parent::accept($page, $recursive)) {
            return true;
        } else if ($page->isVisible() && $page->hasChildren()) {
            foreach (new RecursiveIteratorIterator($page, 1) as $childPage) {
                if ($this->getUseAcl() && $this->_acceptAcl($childPage)) {
                    return true;
                } 
            }
        }
        
        return false;
    }
    
    /**
     * Determines whether a page should be accepted by ACL when iterating
     *
     * Rules:
     * - If helper has no ACL, page is accepted
     * - If page has a resource or privilege defined, page is accepted
     *   if the ACL allows access to it using the helper's role
     * - If page has no resource or privilege, page is accepted
     *
     * @param  Zend_Navigation_Page $page  page to check
     * @return bool                        whether page is accepted by ACL
     */
    protected function _acceptAcl(Zend_Navigation_Page $page) {
        if (true == $page->get('invalid')) {
            return false;
        }
        
        return parent::_acceptAcl($page);
    }
}