<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Identification
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Config.php 37175 2012-09-27 08:01:55Z zhangweiwen $
 */

/**
 * 游戏信息数据库权限配置解析类
 *
 * @name ZtChart_Model_Identification_Config
 */
class ZtChart_Model_Identification_Config {
    
    /**
     * 不需要的游戏ID（不是游戏类型），即配置文件中的ID属性。
     * 例如：<Game GameName="征途免费版" ID="0">
     *
     * @staticvar array
     */
    static protected $_filter = array(8, 13, 16, 17, 19);
    
    /**
     * 
     * @var Zend_Config_Xml
     */
    protected $_config;
    
    /**
     * 
     * @var string
     */
    protected $_encoding = 'GBK';
    
    /**
     * 
     * @param string $config
     */
    public function __construct($url) {
        if (false === ($dom = file_get_contents($url))) {
            throw new ZtChart_Model_Identification_Exception('The identification config file read error.');
        }
        $xml = '<?xml version="1.0" ?>' . PHP_EOL;
        $xml .= '<config>' . PHP_EOL;
        $xml .= mb_convert_encoding($dom, 'UTF-8', $this->_encoding);
        $xml .= '</config>';
        $config = new Zend_Config_Xml($xml, 'GameCenter');
        $this->_config = $config->get('Game');
    }
    
    /**
     * 取得所有游戏的配置信息
     * 
     * @return array
     */
    public function getAllInfo() {
        return $this->_config->toArray();
    }
    
    /**
     * 取得所有游戏的InfoServer配置信息
     * 
     * @param boolean $zendStyle
     * @return array
     */
    public function getAllInfoServerDb($zendStyle = false) {
        $info = array();
        while ($this->_config->valid()) {
            $data = $this->_config->current();
            if (!in_array($data->ID, self::$_filter)) {
                $config = $zendStyle ? $this->_zendDbConfig($data->InfoServerDB->toArray()) 
                                     : $data->InfoServerDB->toArray();
                if ($data->TypePara->count() > 1) {
                    foreach ($data->TypePara as $typePara) {
                        $info[$typePara->Value][] = $config;
                    }
                } else {
                    $info[$data->TypePara->Value][] = $config;
                }
            }
            $this->_config->next();
        }
        
        return $info;
    }
    
    /**
     * 取得所有游戏的ZoneInfo配置信息
     * 
     * @param boolean $zendStyle
     * @return array
     */
    public function getAllZoneInfoDb($zendStyle = false) {
        $info = array();
        while ($this->_config->valid()) {
            $data = $this->_config->current();
            $config = $zendStyle ? $this->_zendDbConfig($data->ZoneInfoDB->toArray())
                                 : $data->InfoServerDB->toArray();
            if ($data->TypePara->count() > 1) {
                foreach ($data->TypePara as $typePara) {
                    $info[$typePara->Value] = $config;
                }
            } else {
                $info[$data->TypePara->Value] = $config;
            }
                
            $this->_config->next();
        }
        
        return $info;
    }
    
    /**
     * 取得所有Infoserver配置中的游戏类型
     * 
     * @return array
     */
    public function getAllGameTypes() {
        $info = array();
        while ($this->_config->valid()) {
            $data = $this->_config->current();
            if ($data->TypePara->count() > 1) {
                foreach ($data->TypePara as $typePara) {
                    $info[] = $typePara->Value;
                }
            } else {
                $info[] = $data->TypePara->Value;
            }
        
            $this->_config->next();
        }
        
        return $info;
    }
    
    /**
     * 取得指定游戏的配置信息
     * 
     * @param integer $gameType
     * @param string $section
     * @return array
     * @throws ZtChart_Model_Identification_Exception
     */
    public function getInfo($gameType, $section = null) {
        while ($this->_config->valid()) {
            foreach($this->_config->current()->TypePara as $typePara) {
                if ($gameType == $typePara->Value) {
                    $info = $this->_config->current()->toArray();
                    if (!empty($section)) {
                        if (!array_key_exists($section, $info)) {
                            throw new ZtChart_Model_Identification_Exception("The section {$section} is not exist.");
                        }
                        $info = $info[$section];
                    }
                    
                    return $info;
                }
            }
            $this->_config->next();
        }
        
        throw new ZtChart_Model_Identification_Exception("The gametype is not exist.");
    }
    
    /**
     * 取得指定游戏的InfoServer配置信息
     * 
     * @param integer $gameType
     * @param boolean $zendStyle
     * @return array
     */
    public function getInfoServerDb($gameType, $zendStyle = false) {
        $info = $this->getInfo($gameType, 'InfoServerDB');
        if ($zendStyle) {
            $info = $this->_zendDbConfig($info);
        }
        
        return $info;
    }
    
    /**
     * 取得指定游戏的ZoneInfo配置信息
     * 
     * @param integer $gameType
     * @param boolean $zendStyle
     * @return array
     */
    public function getZoneInfoDb($gameType, $zendStyle) {
        $info = $this->getInfo($gameType, 'ZoneInfoDB');
        if ($zendStyle) {
            $info = $this->_zendDbConfig($info);
        }
        
        return $info;
    }
    
    /**
     * 把Identification配置转为Zend_Db风格的配置
     * 
     * @param array $config
     * @return array
     */
    protected function _zendDbConfig($config) {
        return array(
            'host' => $config['Server'], 'username' => $config['User'], 
            'password' => $config['Password'], 'port' => $config['Port'], 
            'dbname' => $config['DB']
        );
    }
}