<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Frontend
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: PHPArray.php 35718 2012-06-27 04:03:53Z zhangweiwen $
 */

/**
 * 前端数据生成器——PHP数组
 *
 * @name ZtChart_Model_Assemble_Frontend_PHPArray
 */
class ZtChart_Model_Assemble_Frontend_PHPArray extends ZtChart_Model_Assemble_Frontend_Abstract {
    
    /**
     * 默认的数据处理
     * 
     * @see ZtChart_Model_Assemble_Frontend_Abstract::standard()
     * @param array $dataset
     * @return mixed
     */
    public function standard($dataset) {
        return $dataset;
    }
    
    /**
     * 求和
     * 
     * @param array $dataset
     * @return array
     */
    public function sum($dataset) {
        $tmp = array();
        foreach ($dataset as $data) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (!array_key_exists($key, $tmp)) {
                        $tmp[$key] = 0;
                    }
                    $tmp[$key] += $value;
                }
            }
        }
        
        return $tmp;
    }
    
    /**
     * 按游戏类型分组
     * 
     * @param array $dataset
     * @return array
     */
    public function group($dataset) {
        $tmp = array();
        foreach ($dataset as $label => $data) {
            if (is_array($data)) {
                foreach ($data as $gameType => $entry) {
                    if (!isset($tmp[$gameType])) {
                        $tmp[$gameType] = array();
                    }
                    foreach ($entry as $key => $value) {
                        if (!array_key_exists($key, $tmp[$gameType])) {
                            $tmp[$gameType][$key] = array();
                        }
                        $tmp[$gameType][$key][$label] = $value;
                    }
                }
            }
        }
        
        return $tmp;
    }
    
    /**
     * 按地区合并
     *
     * @param array $dataset
     * @return mixed
     */
    public function area($dataset) {
        $areaData = array_fill_keys(array_keys(ZtChart_Model_Assemble_Area::getAreas()), 0);
        
        foreach ($dataset as $data) {
            if (!empty($data)) {
                array_walk($data, function($value, $area) use (&$areaData) {
                    if (array_key_exists($area, $areaData)) {
                        $areaData[$area] += current($value);
                    }
                });
            }
        }
        // 把地区号是0、1、2、3、4、5、6、7、8、9的数据合并成为一个值，该值的地区号为0。
        foreach ($areaData as $area => $value) {
            if ($area < 10 && $area > 0) {
                $areaData[0] += $value;
                unset($areaData[$area]);
            }
        }
        arsort($areaData);
    
        return $areaData;
    }
}
