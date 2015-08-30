<?php

/**
 * 平台数据实时监控系统
 * 
 * @category ZtChart
 * @package Resources
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Cachemanager.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 资源初始化类——缓存管理
 *
 * @final
 * @name Resources_Cachemanager
 * @see Zend_Application_Resource_Cachemanager
 */
final class Resources_Cachemanager extends Zend_Application_Resource_Cachemanager {

    /**
     * 扩展父类功能，设置默认缓存。
     */
    public function getCacheManager() {
        if (null != ($cacheManager = parent::getCacheManager())) {
            
            // 设置图表数据的缓存
            if ($cacheManager->hasCache('chart')) {
                ZtChart_Model_Assemble::setCache($cacheManager->getCache('chart'));
            }
            
            // 设置权限控制数据的缓存
            if ($cacheManager->hasCache('acl')) {
                ZtChart_Model_Acl_Loader::setCache($cacheManager->getCache('acl'));
            }
            
            // 设置IP数据的缓存
            if ($cacheManager->hasCache('ip')) {
                ZtChart_Model_Assemble_Ip::setCache($cacheManager->getCache('ip'));
            }
        }
        
        return $cacheManager;
    }
}