<?php 

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Qule
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Qule.php 37175 2012-09-27 08:01:55Z zhangweiwen $
 */

/**
 * 系统版本
 *
 * @name ZtChart_Model_Qule
 */
class ZtChart_Model_Qule {
    
    /**
     * 
     */
    const GAMETYPE = 'gametype';
    const GAMEZONE = 'gamezone';
    const GAMEDATA = 'gamedata';
    
    /**
     * 
     * @staticvar Zend_Uri_Http
     */
    static protected $_defaultUri;
    
    /**
     *
     * @staticvar array
     */
    static protected $_methods = array();
    
    /**
     * 
     * @var Zend_Uri_Http
     */
    protected $_uri;
    
    /**
     * 
     * @static
     * @param string $url
     * @return void
     */
    static public function setDefaultUri($url) {
        self::$_defaultUri = Zend_Uri_Http::fromString($url);
    }
    
    /**
     *
     * @static
     * @param array $methods
     * @return void
     */
    static public function setMethods($methods) {
        self::$_methods = $methods;
    }
    
    /**
     * 初始化趣乐平台地址
     * 
     * @param string $url
     */
    public function __construct($url = null) {
        if (empty($url)) {
            $this->_uri = self::$_defaultUri;
        } else {
            $this->_uri = Zend_Uri_Http::fromString($url);
        }
    }
    
    /**
     * 取得所有游戏类型
     * 
     * @return mixed
     */
    public function getGametype() {
        $uri = clone $this->_uri;
        $uri->setQuery(self::$_methods[self::GAMETYPE]);
        
        return Zend_Json::decode(file_get_contents($uri));
    }
    
    /**
     * 取得游戏区
     * 
     * @param integer $gameType
     * @return mixed
     */
    public function getGamezone($gameType) {
        $uri = clone $this->_uri;
        $uri->setQuery(self::$_methods[self::GAMEZONE]);
        $uri->addReplaceQueryParameters(array('gametype' => $gameType));
        
        return Zend_Json::decode(file_get_contents($uri));
    }
    
    /**
     * 取得游戏数据
     * 
     * @param array $params
     * @return mixed
     */
    public function getGamedata($params) {
        $uri = clone $this->_uri;
        $uri->setQuery(self::$_methods[self::GAMEDATA]);
        $uri->addReplaceQueryParameters($params);
        
        return Zend_Json::decode(file_get_contents($uri));
    }
}