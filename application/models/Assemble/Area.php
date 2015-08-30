<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Area
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Area.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 地区数据
 *
 * @name ZtChart_Model_Assemble_Area
 */
class ZtChart_Model_Assemble_Area {

    const OTHER = -1; //其他
    const UNKNOWN = 0; //未知
    const LOCALHOST = 1; //本机
    const LANHOST = 2; //局域网
    const NORTHAMERICA = 3; //北美洲
    const SOUTHAMERICA = 4; //南美洲
    const EUROPE = 5; //欧洲
    const ASIA = 6; //亚洲
    const AFRICA = 7; //非洲
    const OCEANIA = 8; //大洋洲
    const ANTARCTICA = 9; //南极洲
    const EDU = 10; //教育网
    const BEIJING = 11; //北京市
    const TIANJIN = 12; //天津市
    const HEBEI = 13; //河北省
    const SHANXI = 14; //山西省
    const NEIMENGGU = 15; //内蒙古自治区
    const LIAONING = 21; //辽宁省
    const JILIN = 22; //吉林省
    const HEILONGJIANG = 23; //黑龙江省
    const SHANGHAI = 31; //上海市
    const JIANGSU = 32; //江苏省
    const ZHEJIANG = 33; //浙江省
    const ANHUI = 34; //安徽省
    const FUJIAN = 35; //福建省
    const JIANGXI = 36; //江西省
    const SHANDONG = 37; //山东省
    const HENAN = 41; //河南省
    const HUBEI = 42; //湖北省
    const HUNAN = 43; //湖南省
    const GUANGDONG = 44; //广东省
    const GUANGXI = 45; //广西壮族自治区
    const HAINAN = 46; //海南省
    const CHONGQING = 50; //重庆市
    const SICHUAN = 51; //四川省
    const GUIZHOU = 52; //贵州省
    const YUNNAN = 53; //云南省
    const XIZANG = 54; //西藏自治区
    const SHAANXI = 61; //陕西省
    const GANSU = 62; //甘肃省
    const QINGHAI = 63; //青海省
    const NINGXIA = 64; //宁夏回族自治区
    const XINJIANG = 65; //新疆维吾尔自治区
    const TAIWAN = 71; //台湾省
    const XIANGGANG = 81; //香港特别行政区
    const AOMEN = 82; //澳门特别行政区
    
    /**
     * 
     * @staticvar array
     */
    static protected $_area = array(
                self::OTHER => '其他', 
                self::UNKNOWN => '未知',
                self::LOCALHOST => '本机',
                self::LANHOST => '局域网',
                self::NORTHAMERICA => '北美洲',
                self::SOUTHAMERICA => '南美洲',
                self::EUROPE => '欧洲',
                self::ASIA => '亚洲',
                self::AFRICA => '非洲',
                self::OCEANIA => '大洋洲',
                self::ANTARCTICA => '南极洲',
                self::EDU => '教育网',
                self::BEIJING => '北京市',
                self::TIANJIN => '天津市',
                self::HEBEI => '河北省',
                self::SHANXI => '山西省',
                self::NEIMENGGU => '内蒙古自治区',
                self::LIAONING => '辽宁省',
                self::JILIN => '吉林省',
                self::HEILONGJIANG => '黑龙江省',
                self::SHANGHAI => '上海市',
                self::JIANGSU => '江苏省',
                self::ZHEJIANG => '浙江省',
                self::ANHUI => '安徽省',
                self::FUJIAN => '福建省',
                self::JIANGXI => '江西省',
                self::SHANDONG => '山东省',
                self::HENAN => '河南省',
                self::HUBEI => '湖北省',
                self::HUNAN => '湖南省',
                self::GUANGDONG => '广东省',
                self::GUANGXI => '广西壮族自治区',
                self::HAINAN => '海南省',
                self::CHONGQING => '重庆市',
                self::SICHUAN => '四川省',
                self::GUIZHOU => '贵州省',
                self::YUNNAN => '云南省',
                self::XIZANG => '西藏自治区',
                self::SHAANXI => '陕西省',
                self::GANSU => '甘肃省',
                self::QINGHAI => '青海省',
                self::NINGXIA => '宁夏回族自治区',
                self::XINJIANG => '新疆维吾尔自治区',
                self::TAIWAN => '台湾省',
                self::XIANGGANG => '香港特别行政区',
                self::AOMEN => '澳门特别行政区');
    
    /**
     * 取得所有地区
     * 
     * @return array
     */
    static public function getAreas() {
        return self::$_area;
    }
                
    /**
     * 取得地区名字
     * 
     * @param integer $code
     * @return false|string
     */
    static public function getAreaName($code) {
        $r = new ReflectionClass(__CLASS__);
        
        return array_search($code, $r->getConstants());
    }
    
    /**
     * 取得地区中文名字
     * 
     * @param integer $code
     * @return false|string
     */
    static public function getAreaChineseName($code) {
        $code = intval($code);
        
        return array_key_exists($code, self::$_area) ? self::$_area[$code] : false;
    }
}