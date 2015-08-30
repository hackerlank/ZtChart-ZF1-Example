<?php

/**
 * 人事招聘系统
 * 
 * @category Recruit
 * @package System_View
 * @subpackage System_View_Helper
 * @copyright Copyright (c) 2004 - 2011 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: RoleName.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 视图助手——显示角色名称
 *
 * @name System_View_Helper_RoleName
 * @see Zend_View_Helper_Abstract
 */
class System_View_Helper_RoleName extends Zend_View_Helper_Abstract {
    
    /**
     * 返回指定的角色名称
     * 
     * @param integer $roleId 角色数据表中的自增ID 
     * @return string
     */
    public function roleName($roleId) {
        $role = new ZtChart_Model_Role($roleId);
        
        return $role->getRoleName();
    }
}

