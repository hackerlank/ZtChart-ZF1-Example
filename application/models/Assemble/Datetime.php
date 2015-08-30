<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Assemble
 * @subpackage ZtChart_Model_Assemble_Datetime
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Datetime.php 37158 2012-09-26 09:25:22Z zhangweiwen $
 */

/**
 * 时间日期
 *
 * @name ZtChart_Model_Assemble_Datetime
 */
class ZtChart_Model_Assemble_Datetime {

    /**
     * 本系统日期起始时间
     */
    const ERA_DATE = '2012-06-01';
    const ERA_TIME = '00:00:00';
    const ERA_DATETIME = '2012-06-01 00:00:00';
    
    /**
     * 
     */
    const CUSTOM = 0;
    const TODAY = 1;
    const YESTODAY = 2;
    const TOMORROW = 3;
    const THIS_MONTH = 4;
    const THIS_YEAR = 5;
    const THIS_SEASON = 6;
    const RECENT_24HOUR = 101;
    const RECENT_48HOUR = 102;
    const RECENT_1WEEK = 103;
    const RECENT_1MONTH = 104;
    const RECENT_24MONTH = 105;
    const LAST_1MONTH = 201;
    const LAST_1YEAR = 202;
    const ENTIRE_DAY = 999;
    
    /**
     * 
     */
    const ZF_DATETIME_FORMAT = 'y-MM-dd HH:mm:ss';
    const ZF_DATE_FORMAT = 'y-MM-dd';
    const ZF_TIME_FORMAT = 'HH:mm:ss';
    const PHP_DATETIME_FORMAT = 'Y-m-d H:i:s';
    const PHP_DATE_FORMAT = 'Y-m-d';
    const PHP_TIME_FORMAT = 'H:i:s';
    
    /**
     * 
     * @staticvar array
     */
    static protected $_preDefined = array(
        self::TODAY => '今天', 
        self::YESTODAY => '昨天', 
        self::TOMORROW => '明天', 
        self::THIS_MONTH => '本月',
        self::THIS_YEAR => '今年',
        self::THIS_SEASON => '本季度', 
        self::RECENT_24HOUR => '近24小时',
        self::RECENT_48HOUR => '近48小时',
        self::RECENT_1WEEK => '近1周',
        self::RECENT_1MONTH => '近1个月',
        self::RECENT_24MONTH => '近24个月',
        self::LAST_1MONTH => '上月',
        self::LAST_1YEAR => '去年',
        self::ENTIRE_DAY => '全部日期', 
        self::CUSTOM => '选择日期'
    );
    
    /**
     * 时间单位允许字符，用于判断'YYYY-mm-dd HH:ii:ss'格式的时间相应的时间单位位置
     *
     * @staticvar array
     */
    static protected $_datetimeUnit = array(
        Zend_Date::YEAR => 4,  // 年
        Zend_Date::MONTH => 7,  // 月
        Zend_Date::DAY => 10, // 日
        Zend_Date::HOUR => 13, // 小时
        Zend_Date::MINUTE => 16, // 分钟
        Zend_Date::SECOND => 19  // 秒
    );
    
    /**
     * 时间间隔对照字符，请参阅DateInterval类
     * 
     * @staticvar array
     */
    static protected $_datetimeInterval = array(
        Zend_Date::YEAR => 'P%dY',  // 年
        Zend_Date::MONTH => 'P%dM',  // 月
        Zend_Date::DAY => 'P%dD', // 日
        Zend_Date::WEEK => 'P%dW', // 周
        Zend_Date::HOUR => 'PT%dH', // 小时
        Zend_Date::MINUTE => 'PT%dM', // 分钟
        Zend_Date::SECOND => 'PT%dS'  // 秒
    );
    
    /**
     * 检查时间单位是否合法
     *
     * @static
     * @param string $unit
     * @return boolean
     */
    static public function checkDatetimeUnit($unit) {
        return in_array($unit, array_keys(self::$_datetimeUnit));
    }
    
    /**
     * 取得相应的时间单位位置
     * 
     * @static
     * @param string $unit
     * @return integer
     */
    static public function getDatetimePos($unit) {
        return self::checkDatetimeUnit($unit) ? self::$_datetimeUnit[$unit] : 0;
    }
    
    /**
     * 取得低一个级别的时间单位
     * 
     * @static
     * @param string $unit
     * @return string
     */
    static public function getLowerDatetimeUnit($unit) {
        $unitSet = array_values(array_flip(self::$_datetimeUnit));
        
        return false !== ($pos = array_search($unit, $unitSet)) ? $unitSet[min(count($unitSet), $pos + 1)] : '';
    }
    
    /**
     * 取得高一个级别的时间单位
     * 
     * @static
     * @param string $unit
     * @return string
     */
    static public function getUpperDatetimeUnit($unit) {
        $unitSet = array_values(array_flip(self::$_datetimeUnit));
        
        return false !== ($pos = array_search($unit, $unitSet)) ? $unitSet[max(0, $pos - 1)] : '';
    }
    
    /**
     * 根据时间单位取得时间范围
     * 
     * @static
     * @param integer|string|Zend_Date $start 起始时间
     * @param integer|string|Zend_Date $end 结束时间
     * @param string $unit 时间单位
     * @return array
     */
    static public function getDatetimeRange($start, $end, $unit) {
        $range = array();
        if (self::checkDatetimeUnit($unit)) {
            $interval = new DateInterval(sprintf(self::$_datetimeInterval[$unit], 1));
            $period = new DatePeriod(new DateTime(self::normalizeDatetime($start)), 
                                $interval, new DateTime(self::normalizeDatetime($end)));
            foreach ($period as $datetime) {
                $range[] = substr($datetime->format(self::PHP_DATETIME_FORMAT), 0, self::getDatetimePos($unit));
            }
        }
        
        return $range;
    }
    
    /**
     * 格式化时间，转换成本系统指定的日期格式
     * 
     * @static
     * @param integer|string|Zend_Date $datetime
     * @param integer $length
     * @param string $unit
     * @return string
     */
    static public function normalizeDatetime($datetime, $length = 0, $unit = null) {
        $date = new Zend_Date($datetime);
        if ($length > 0) {
            $date->add($length, $unit);
        } else if ($length < 0) {
            $date->sub(abs($length), $unit);
        }
        
        return $date->toString(self::ZF_DATETIME_FORMAT);
    }
    
    /**
     * 先截取后用零补齐时间
     * 
     * @static
     * @param string $datetime
     * @param string|integer $unit
     * @return string
     */
    static public function padDatetime($datetime, $unit) {
        if (is_string($unit) && !is_numeric($unit)) {
            $unit = self::getDatetimePos($unit);
        }
        
        return self::normalizeDatetime(substr($datetime, 0, $unit));
    }
    
    /**
     * 把时间转换成时间戳
     * 
     * @static
     * @param string $datetime
     * @return integer
     */
    static public function normalizeTimestamp($datetime) {
        if (!$datetime instanceof Zend_Date) {
            $datetime = new Zend_Date($datetime);
        }
        
        return $datetime->toValue();
    }
    
    /**
     * 把时间字符串截取到指定时间单位
     * 
     * @static
     * @param integer|string|Zend_Date $datetime $datetime
     * @param string $unit
     * @return string
     */
    static public function truncateDatetime($datetime, $unit) {
        $datetime = new Zend_Date($datetime);
        
        return substr($datetime->toString(self::ZF_DATETIME_FORMAT), 0, self::getDatetimePos($unit));
    }
    
    /**
     * 取得所需要的预定义时间
     * 
     * @static
     * @param array $keys
     * @return array
     */
    static public function getPredefinedDatetimes($keys = null) {
        $datetimes = array();
        if (empty($keys)) {
            $datetimes = self::$_preDefined;
        } else {
            foreach ($keys as $key) {
                if (array_key_exists($key, self::$_preDefined)) {
                    $datetimes[$key] = self::$_preDefined[$key];
                }
            }
        }
        
        return $datetimes;
    }
    
    /**
     * 取得后一天的日期
     * 
     * @static
     * @param integer|string $datetime
     * @return string
     */
    static public function getNextDate($datetime = null) {
        $datetime = empty($datetime) ? Zend_Date::now() : new Zend_Date($datetime);
        $datetime->addDay(1);
        
        return $datetime->toString(self::ZF_DATE_FORMAT);
    }
    
    /**
     * 取得前一天的日期
     *
     * @static
     * @param integer|string $datetime
     * @return string
     */
    static public function getPrevDate($datetime = null) {
        $datetime = empty($datetime) ? Zend_Date::now() : new Zend_Date($datetime);
        $datetime->subDay(1);
    
        return $datetime->toString(self::ZF_DATE_FORMAT);
    }
    
    /**
     * 取得预定义时间的开始时间戳
     * 
     * @param integer $interval 预定义时间常量
     * @param integer $timestamp
     * @return integer
     */
    static public function getPredefinedStartTimestamp($interval, $timestamp = null) {
        $range = self::getPredefinedRange($interval, Zend_Date::SECOND, $timestamp);
        if (!array_key_exists('start', $range)) {
            throw new ZtChart_Model_Assemble_Datetime_Exception('The predefined date is not exists.');
        }
        
        return strtotime($range['start']);
    }
    
    /**
     * 取得预定义时间的结束时间戳
     * 
     * @param integer $interval 预定义时间常量
     * @param integer $timestamp
     * @return integer
     */
    static public function getPredefinedEndTimestamp($interval, $timestamp = null) {
        $range = self::getPredefinedRange($interval, Zend_Date::SECOND, $timestamp);
        if (!array_key_exists('end', $range)) {
            throw new ZtChart_Model_Assemble_Datetime_Exception('The predefined date is not exists.');
        }
        
        return strtotime($range['end']);
    }
    
    /**
     * 取得某个预定义时间段
     * 
     * @static
     * @param integer $interval
     * @param string $forceUnit
     * @param integer $timestamp
     * @return array
     */
    static public function getPredefinedRange($interval, $forceUnit = null, $timestamp = null) {
        if (empty($timestamp)) {
            $timestamp = time();
        }
        $start = new Zend_Date($timestamp);
        $end = new Zend_Date($timestamp);
        switch ($interval) {
            case self::TODAY:
                $start->setHour(0)->setMinute(0)->setSecond(0);
                $unit = Zend_Date::HOUR;
                break;
            case self::YESTODAY:
                $start->subDay(1)->setHour(0)->setMinute(0)->setSecond(0);
                $end = clone $start;
                $end->addDay(1);
                $unit = Zend_Date::HOUR;
                break;
            case self::TOMORROW:
                $start->addDay(1)->setHour(0)->setMinute(0)->setSecond(0);
                $end = clone $start;
                $end->addDay(1);
                $unit = Zend_Date::HOUR;
                break;
            case self::THIS_MONTH:
                $start->setDay(1)->setHour(0)
                        ->setMinute(0)->setSecond(0);
                $unit = Zend_Date::DAY;
                break;
            case self::THIS_YEAR:
                $start->setMonth(1)->setDay(1)
                        ->setHour(0)->setMinute(0)->setSecond(0);
                $end->addMonth(1)->setDay(1)
                        ->setHour(0)->setMinute(0)->setSecond(0);
                $unit = Zend_Date::DAY;
                break;
            case self::THIS_SEASON:
                $start->setMonth(3 * floor(($start->toValue('M') - 1) / 3) + 1)->setDay(1)
                        ->setHour(0)->setMinute(0)->setSecond(0);
                $unit = Zend_Date::DAY;
            case self::RECENT_24HOUR:
                $start->subHour(24);
                $unit = Zend_Date::HOUR;
                break;
            case self::RECENT_48HOUR:
                $start->subHour(48);
                $unit = Zend_Date::HOUR;
                break;
            case self::RECENT_1WEEK:
                $start->subWeek(1);
                $unit = Zend_Date::DAY;
                break;
            case self::RECENT_1MONTH:
                $start->subMonth(1);
                $unit = Zend_Date::DAY;
                break;
            case self::RECENT_24MONTH:
                $start->subMonth(24);
                $unit = Zend_Date::DAY;
                break;
            case self::LAST_1MONTH:
                $start->subMonth(1)->setDay(1)->setHour(0)
                        ->setMinute(0)->setSecond(0);
                $end = clone $start;
                $end->addMonth(1);
                $unit = Zend_Date::DAY;
                break;
            case self::LAST_1YEAR:
                $start->subYear(1)->setMonth(1)->setDay(1)
                        ->setHour(0)->setMinute(0)->setSecond(0);
                $end = clone $start;
                $end->addYear(1);
                $unit = Zend_Date::DAY;
                break;
            case self::ENTIRE_DAY:
                $start->setDate(self::ERA_DATE, self::ZF_DATE_FORMAT)
                        ->setTime(self::ERA_TIME, self::ZF_TIME_FORMAT);
                $end->addDay(1);
                $unit = Zend_Date::DAY;
                break;
            default:
                $unit = Zend_Date::SECOND;
        }
        if (!empty($forceUnit)) {
            $unit = $forceUnit;
        }
        $start = max(self::truncateDatetime($start, $unit), self::truncateDatetime(self::ERA_DATETIME, $unit));
        $end = max(self::truncateDatetime($end, $unit), self::truncateDatetime(self::ERA_DATETIME, $unit));
        
        return compact('start', 'end', 'unit');
    }
}