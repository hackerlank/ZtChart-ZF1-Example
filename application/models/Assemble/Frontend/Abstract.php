<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Frontend
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Abstract.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 前端数据生成器
 *
 * @abstract
 * @name ZtChart_Model_Assemble_Frontend_Abstract
 */
abstract class ZtChart_Model_Assemble_Frontend_Abstract {
    
    /**
     * 
     * @var string
     */
    protected $_format = 'standard';
    
    /**
     * 构造函数
     * 
     * @param array $config
     */
    public function __construct($config = null) {
        if (is_array($config)) {
            $this->setConfig($config);
        }
    }
    
    /**
     * 设置参数
     * 
     * @param array $config
     * @return void
     */
    public function setConfig($config) {
        foreach (array_change_key_case($config) as $param => $value) {
            $setMethod = 'set' . ucfirst($param);
            if (method_exists($this, $setMethod)) {
                call_user_func(array($this, $setMethod), $value);
            }
        }
    }
    
    /**
     * 设置输出的格式
     * 
     * @param string $format
     * @return void
     */
    public function setFormat($format) {
        $this->_format = $format;
    }
    
    /**
     * 默认的数据处理
     *
     * @param array $dataset
     * @return mixed
     */
    public function format($dataset) {
        end($dataset);
        $end = key($dataset);
        reset($dataset);
        $start = key($dataset);
        $chart = call_user_func(array($this, $this->_format), $dataset);
        
        return compact('start', 'end', 'chart');
    }
    
    /**
     * 标准的处理器
     * 
     * @param array $dataset
     * @return mixed
     */
    abstract public function standard($dataset); 
}