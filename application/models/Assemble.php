<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Assemble.php 35652 2012-06-14 12:44:10Z zhangweiwen $
 */

/**
 * 数据装配器
 *
 * @name ZtChart_Model_Assemble
 */
class ZtChart_Model_Assemble {
    
    /**
     * 
     */
    const GAMETYPE = 'gametype';
    
    /**
     * 
     * @var ZtChart_Model_Assemble_Backend_Abstract
     */
    protected $_backend;
    
    /**
     * 
     * @var ZtChart_Model_Assemble_Frontend_Abstract
     */
    protected $_frontend;
    
    /**
     * 
     * @staticvar Zend_Cache_Core
     */
    static protected $_cache;
    
    /**
     * 设置缓存对象
     * 
     * @static
     * @param Zend_Cache_Core $cache
     */
    static public function setCache($cache) {
        self::$_cache = $cache;
    }
    
    /**
     * 取得缓存对象
     * 
     * @static
     * @return Zend_Cache_Core
     */
    static public function getCache() {
        return self::$_cache;
    }
    
    /**
     * 构造函数
     * 
     * @param string $backend
     * @param string $frontend
     * @throws ZtChart_Model_Assemble_Exception
     */
    public function __construct($backend, $frontend = 'PHPArray') {
        $loader = new Zend_Loader_PluginLoader(
            array(
                'ZtChart_Model_Assemble_Backend_' => realpath(__DIR__ . '/Assemble/Backend'), 
                'ZtChart_Model_Assemble_Frontend_' => realpath(__DIR__ . '/Assemble/Frontend')
            )
        );
        
        $backendName = is_array($backend) ? key($backend) : $backend;
        if (false === ($backendClass = $loader->load($backendName, false))) {
            throw new ZtChart_Model_Assemble_Exception(
                            "Specified backend class '{$backendName}' could not be found");
        }
        $this->_backend = new $backendClass($backend);
        
        $frontendName = is_array($frontend) ? key($frontend) : $frontend;
        if (false === ($frontendClass = $loader->load($frontendName, false))) {
            throw new ZtChart_Model_Assemble_Exception(
                            "Specified frontend class '{$frontendName}' could not be found");
        }
        $this->_frontend = new $frontendClass($frontend);
    }
    
    /**
     * 取得一段时间的数据，按时间划分，并补零。
     * 
     * @param string $start 起始时间
     * @param string $end 结束时间
     * @param string $unit 时间单位
     * @return array
     */
    public function getRangeAssembleData($start, $end, $unit = Zend_Date::MINUTE) {
        $dataset = array();
        foreach ($this->_getDatetimeRange($start, $end, $unit) as $label) {
            if (false !== ($data = $this->getCacheData($label)) 
                            && (0 != $data || Zend_Date::SECOND == $unit)) {
                $dataset[$label] = $data;
            } else {
                $dataset += $this->cacheRangeRawData(
                        ZtChart_Model_Assemble_Datetime::normalizeTimestamp($label), $end, $unit);
                break;
            } 
        }

        return $this->_frontend->format($dataset);
    }
    
    /**
     * 取得某个预定义时间的数据，按时间划分，并补零。
     * 
     * @param integer $interval
     * @param string $unit
     * @return array
     */
    public function getPredefinedAssembleData($interval, $unit = null) {
        $preDefined = ZtChart_Model_Assemble_Datetime::getPredefinedRange($interval, $unit);
        
        return $this->getRangeAssembleData($preDefined['start'], $preDefined['end'], $preDefined['unit']);
    }
    
    /**
     * 取得一段时间的原始数据集
     *
     * @param string $start 起始时间
     * @param string $end 结束时间
     * @param string $unit 时间单位
     * @return arrray
     */
    public function fetchRangeData($start, $end, $unit = Zend_Date::MINUTE) {
        return $this->_backend->collect($start, $end, $unit);
    }
    
    /**
     * 取得某个预定义时间的数据集
     * 
     * @param integer $interval
     * @param string $unit
     * @return array
     */
    public function fetchPredefinedData($interval, $unit = null) {
        $preDefined = ZtChart_Model_Assemble_Datetime::getPredefinedRange($interval);
        
        return $this->fetchRangeData($preDefined['start'], $preDefined['end'], $preDefined['unit']);
    }
    
    /**
     * 取得一段时间的原始数据并保存在缓存中
     *
     * @param string $start 起始时间
     * @param string $end 结束时间
     * @param string $unit 时间单位
     * @param integer $lifetime 有效时间
     * @return array
     * @throws ZtChart_Model_Assemble_Exception
     */
    public function cacheRangeData($start, $end, $unit = Zend_Date::MINUTE, $lifetime = 0) {
        foreach (($dataset = $this->fetchRangeData($start, $end, $unit)) as $label => $data) {
            if (!$this->setCacheData($data, $label, $lifetime)) {
                throw new ZtChart_Model_Assemble_Exception('Failed for save data into cache.');
            }
        }
        
        return $dataset;
    }
    
    /**
     * 取得一段时间的原始数据集，按时间划分，并补零。
     * 
     * @param string $start 起始时间
     * @param string $end 结束时间
     * @param string $unit 时间单位
     * @return arrray
     */
    public function getRangeRawData($start, $end, $unit = Zend_Date::MINUTE) {
        return array_merge(
                    array_fill_keys($this->_getDatetimeRange($start, $end, $unit), null), 
                                                    $this->fetchRangeData($start, $end, $unit));
    }
    
    /**
     * 取得一段时间的原始数据并保存在缓存中，按时间划分，并补零。
     * 
     * @param string $start 起始时间
     * @param string $end 结束时间
     * @param string $unit 时间单位
     * @param integer $lifetime 有效时间
     * @return array
     * @throws ZtChart_Model_Assemble_Exception
     */
    public function cacheRangeRawData($start, $end, $unit = Zend_Date::MINUTE, $lifetime = 0) {
        foreach (($dataset = $this->getRangeRawData($start, $end, $unit)) as $label => $data) {
            if (!$this->setCacheData($data, $label, $lifetime)) {
                throw new ZtChart_Model_Assemble_Exception('Failed for save data into cache.');
            }
        }
        
        return $dataset;
    }
    
    /**
     * 取得某一时刻的原始数据
     * 
     * @param string $datetime
     * @param string $unit
     * @return integer|null
     */
    public function getMomentRawData($datetime, $unit = Zend_Date::MINUTE) {
        $rowset = $this->getRawData($datetime, $datetime, $unit);
        
        return !empty($rowset) ? current($rowset) : null;
    }
    
    /**
     * 取得某一时刻的原始数据并保存在缓存中
     * 
     * @param string $datetime
     * @param string $unit
     * @param integer $lifetime
     * @return integer
     * @throws ZtChart_Model_Assemble_Exception
     */
    public function cacheMomentRawData($datetime, $unit = Zend_Date::MINUTE, $lifetime = 0) {
        if (null !== ($value = $this->getMomentRawData($datetime, $unit))) {
            throw new ZtChart_Model_Assemble_Exception('The moment raw data is null: ' . $datetime);
        }
        if (!$this->setCacheData($value, $datetime, $lifetime)) {
            throw new ZtChart_Model_Assemble_Exception('Failed for save data into cache.');
        }
        
        return $value;
    }
    
    /**
     * 增加缓存中数据的值
     * 
     * @param string $label
     * @param integer $step
     * @return boolean
     */
    public function increaseCacheData($label, $step = 1) {
        if (false === ($data = $this->getCacheData($label))) {
            $data = 0;
        }
        
        return $this->setCacheData($data + $step, $label);
    }
    
    /**
     * 把数据保存到缓存中
     * 
     * @param mixed $data
     * @param string $label
     * @param integer $lifetime
     * @return boolean
     */
    public function setCacheData($data, $label, $lifetime = 0) {
        return self::$_cache->save($data, $this->_cacheId($label), array(), $lifetime);
    }
    
    /**
     * 从缓存中取得数据
     * 
     * @param string $label
     * @return mixed
     */
    public function getCacheData($label) {
        return self::$_cache->load($this->_cacheId($label));
    }
    
    /**
     * 设置前端数据生成器的参数
     * 
     * @param array $config
     */
    public function setFrontendConfig($config) {
        $this->_frontend->setConfig($config);
    }
    
    /**
     * 设置前端数据生成器格式
     * 
     * @param string $format
     */
    public function setFrontendFormat($format) {
        $this->setFrontendConfig(array('format' => $format));
    }
    
    /**
     * 设置后端数据装载器的参数
     * 
     * @param array $config
     */
    public function setBackendConfig($config) {
        $this->_backend->setConfig($config);
    }
    
    /**
     * 设置游戏类型
     * 
     * @param integer|array $gameTypes
     */
    public function setGameTypes($gameTypes) {
        $this->_backend->setGameTypes($gameTypes);
    }
    
    /**
     * 取得时间范围
     * 
     * @param string $start
     * @param string $end
     * @param string $unit
     * @return array
     */
    protected function _getDatetimeRange(&$start, &$end, $unit) {
        if (empty($start) && empty($end)) {
            $start = $end = ZtChart_Model_Assemble_Datetime::padDatetime(time(), $unit);
        } else if (empty($start)) {
            $start = ZtChart_Model_Assemble_Datetime::ERA_DATETIME;
        } else if (empty($end)) {
            $end = ZtChart_Model_Assemble_Datetime::padDatetime(time(), $unit);
        }
        
        return ZtChart_Model_Assemble_Datetime::getDatetimeRange($start, $end, $unit);
    }
    
    /**
     * 魔术方法
     * 
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        if (preg_match_all('/^(?:((?:count|find)([a-zA-Z]+))With)(\w+)/', $name, $matches)) {
            if (!method_exists($this, $matches[1][0]) 
                    && !method_exists($this, $getMethod = 'get' . $matches[2][0])) {
                throw new ZtChart_Model_Assemble_Exception('Does not support this method');
            }
            $backend = clone $this->_backend;
            foreach (explode('And', $matches[3][0]) as $value) {
                $setBackendMethod = 'set' . $value;
                if (!method_exists($this->_backend, $setBackendMethod)) {
                    throw new ZtChart_Model_Assemble_Exception(
                            'The backend does not support this method: ' . $setBackendMethod);
                }
                call_user_func_array(array($this->_backend, $setBackendMethod), array());
            }
            $dataset = call_user_func_array(array($this, $getMethod), $arguments);
            $this->_backend = $backend;
            
            return $dataset;
        }
    }
    
    /**
     * 生成缓存ID
     * 
     * @param string $label
     * @return string
     */
    protected function _cacheId($label) {
        $label = str_replace(array(' ', ':', '-'), null, $label);
        
        return $this->_backend->hashObject() . '_' . $label;
    }
}