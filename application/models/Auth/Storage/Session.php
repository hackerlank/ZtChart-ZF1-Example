<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Auth
 * @subpackage ZtChart_Model_Auth_Storage
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Session.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 身份认证信息存储类
 *
 * @name ZtChart_Model_Auth_Storage_Session
 */
class ZtChart_Model_Auth_Storage_Session extends Zend_Auth_Storage_Session {

    /**
     * 
     * @param string $member
     */
    public function __construct($member = parent::MEMBER_DEFAULT) {
        parent::__construct(parent::NAMESPACE_DEFAULT, $member);
    }
}