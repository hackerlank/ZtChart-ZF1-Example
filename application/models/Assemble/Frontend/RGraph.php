<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Frontend
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: RGraph.php 35718 2012-06-27 04:03:53Z zhangweiwen $
 */

/**
 * 前端数据生成器——RGraph
 *
 * @name ZtChart_Model_Assemble_Frontend_RGraph
 */
class ZtChart_Model_Assemble_Frontend_RGraph extends ZtChart_Model_Assemble_Frontend_Abstract {
    
    /**
     * 标准的处理器
     * 
     * @param array $dataset
     * @return mixed
     */
    public function standard($dataset) {
        $tmp = array();
        $pad = 0;
        foreach ($dataset as $data) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (!array_key_exists($key, $tmp)) {
                        $tmp[$key] = array();
                    }
                    $tmp[$key] = array_pad($tmp[$key], count($tmp[$key]) + $pad, null);
                    $tmp[$key][] = $value;
                }
                $pad = 0;
            } else {
                $pad++;
            }
        }
        if ($pad > 0) {
            $tmp = array_map(function($v) use ($pad) { 
                                return array_pad($v, count($v) + $pad, null); 
                            }, $tmp);
        }
        $label = array_keys($dataset);
        $count = count($label);
        
        return compact('label', 'count') + $tmp;
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
        
        $label = array_keys($areaData);
        $data = array_values($areaData);
        
        return compact('label', 'data');
    }
}