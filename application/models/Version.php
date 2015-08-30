<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Version
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Version.php 37708 2012-12-17 08:26:09Z zhangweiwen $
 */

/**
 * 系统版本
 *
 * @final
 * @name ZtChart_Model_Version
 */
final class ZtChart_Model_Version {

    /**
     * 版本号
     */
    const VERSION = '1.0.7';
    
    /**
     * 版本比较
     * 
     * @param string $version
     * @return integer
     */
    public static function compareVersion($version) {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        
        return version_compare($version, strtolower(self::VERSION));
    }
}