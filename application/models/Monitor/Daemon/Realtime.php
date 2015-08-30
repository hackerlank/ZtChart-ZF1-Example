<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Monitor
 * @subpackage ZtChart_Model_Monitor_Daemon
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: Realtime.php 35718 2012-06-27 04:03:53Z zhangweiwen $
 */

/**
 * 日志数据实时监控类
 *
 * @final
 * @see ZtChart_Model_Monitor_Daemon_Abstract
 * @name ZtChart_Model_Monitor_Daemon_Realtime
 */
final class ZtChart_Model_Monitor_Daemon_Realtime extends ZtChart_Model_Monitor_Daemon_Abstract {

    /**
     * 共享内存大小
     */
    const SHM_SIZE = 536870912;
    
    /**
     *
     * @var resource
     */
    protected $_shmId = null;
    
    /**
     *
     * @var resource
     */
    protected $_semId = null;
    
    /**
     * 初始化共享内存和信号量
     * 
     * @throws ZtChart_Model_Monitor_Exception
     */
    public function __construct($config) {
        if (!extension_loaded('pcntl')) {
            throw new ZtChart_Model_Monitor_Daemon_Exception('Pcntl extension is not loaded !');
        }
    
        if (!extension_loaded('inotify')) {
            throw new ZtChart_Model_Monitor_Daemon_Exception('Inotify extension is not loaded !');
        }
    
        if (!extension_loaded('sysvshm')) {
            throw new ZtChart_Model_Monitor_Daemon_Exception('System V shared memory extension is not loaded !');
        }
        $this->_shmId = shm_attach(ftok(__FILE__, '1'), self::SHM_SIZE);
    
        if (!extension_loaded('sysvsem')) {
            throw new ZtChart_Model_Monitor_Daemon_Exception('System V semaphore extension is not loaded !');
        }
        $this->_semId = sem_get(ftok(__FILE__, 2));
        
        parent::__construct($config);
    }
    
    /**
     * 运行守护进程，监控有变化的日志文件。
     * 
     * @see ZtChart_Model_Monitor_Abstract::daemon()
     */
    public function run() {
        if (0 == ($pid = pcntl_fork())) {
        
            // 子进程负责处理当前日志
            try {
                $this->tail($this->_console->getLogPaths());
            } catch (ZtChart_Model_Monitor_Exception $e) {
                $this->_logger->err($e->getMessage());
                exit(1);
            }
        } else if (0 < $pid) {
            pcntl_setpriority(-1);
        
            // 父进程负责把子进产生的数据写入数据库
            if (pcntl_sigprocmask(SIG_BLOCK, array(SIGCHLD, SIGALRM, SIGINT, SIGTERM))) {
                $interval = 10; // 10秒写一次
                pcntl_alarm($interval);
                while ($signo = pcntl_sigwaitinfo(array(SIGCHLD, SIGALRM, SIGINT, SIGTERM))) {
                    if (SIGALRM == $signo) {
                        pcntl_alarm($interval);
                    }
                    
                    try {
                        $this->extract($interval);
                    } catch (ZtChart_Model_Monitor_Exception $e) {
                        posix_kill($pid, 9);
                        exit(1);
                    }
                    
                    if (SIGCHLD == $signo || SIGINT == $signo || SIGTERM == $signo) {
                        break;
                    }
                }
            }
        }
        exit(0);
    }
    
    /**
     * 取得解析后的日志数据，写入数据库。
     * 
     * @param integer $interval
     * @return void
     * @throws ZtChart_Model_Monitor_Exception
     */
    public function extract($interval) {
        foreach (ZtChart_Model_Monitor_Log::getIdentifierClasses() as $identifierClass) {
            $logIdentifier = constant("{$identifierClass}::LOG_IDENTIFIER");
            $shmIdentifier = constant("{$identifierClass}::SHM_IDENTIFIER");
            $tmpfile = $this->_tmpfile($logIdentifier);
            if (file_put_contents($tmpfile, $this->_shmRead($shmIdentifier, true))) {
                $cachetime = time() - 2; // 当前服务器时间与LogServer时间可能有误差，因此设2秒冗余时间。
        
                try {
                    $affected = $this->_exec("LOAD DATA INFILE '{$tmpfile}' INTO TABLE {$logIdentifier} FIELDS TERMINATED BY ','");
                } catch (Zend_Db_Adapter_Exception $e) {
                    throw new ZtChart_Model_Monitor_Exception($e);
                }
        
                try {
                    $assemble = new ZtChart_Model_Assemble($logIdentifier);
                    $assemble->cacheRangeRawData($cachetime - $interval, $cachetime, Zend_Date::SECOND, 300);
                } catch (ZtChart_Model_Assemble_Exception $e) {
                    $this->_logger->warn($e->getMessage());
                }
                $this->_logger->info("{$identifierClass}: {$affected} 行数据被导入 {$logIdentifier} 表");
            }
            @unlink($tmpfile);
        }
    }
    
    /**
     * 监控日志文件
     * 
     * @param array $logPaths
     * @return void
     * @throws ZtChart_Model_Monitor_Exception
     */
    public function tail($logPaths) {
        if (($inotify = inotify_init()) === false) {
            throw new ZtChart_Model_Monitor_Daemon_Exception('Failed to obtain an inotify instance.');
        }
        $watchFiles = $watchDirs = array();
        foreach ($logPaths as $file) {
            if (($watch = inotify_add_watch($inotify, $file, IN_CREATE |IN_MODIFY)) === false) {
                throw new ZtChart_Model_Monitor_Daemon_Exception("Failed to watch file '{$file}'.");
            }
            if (is_file($file)) {
                if (false === ($fd = fopen($file, "r"))) {
                    throw new ZtChart_Model_Monitor_Daemon_Exception("File '{$file}' is not readable.");
                }
                $this->_ffseek($fd);
                $watchFiles[$watch] = $fd;
            } else if (is_dir($file)) {
                $watchFiles[$watch] = array();
                $watchDirs[$watch] = $file;
            }
        }
        
        while (($events = inotify_read($inotify)) !== false) {
            foreach ($events as $event) {
                if ($event['mask'] & IN_Q_OVERFLOW) {
                    throw new ZtChart_Model_Monitor_Daemon_Exception("The number of inotify queued events reaches upper limit.");
                }
                if (!$this->accept($event['name'])) {
                    continue;
                }
                if ($event['mask'] & (IN_CREATE | IN_MODIFY)) {
                    if (!($event['mask'] & IN_ISDIR)) {
                        if (!array_key_exists($event['name'], $watchFiles[$event['wd']])) {
                            $fn = $fd = null;
                            foreach ($watchFiles[$event['wd']] as $wfn => $wfd) {
                                if (strncasecmp($wfn, $event['name'], strpos($wfn, '.')) == 0) {
                                    $fn = $wfn;
                                    $fd = $wfd;
                                    break;
                                }
                            }
        
                            // 判断当前创建或修改的日志文件是否最新
                            if (strcasecmp($fn, $event['name']) < 0) {
                                if (is_resource($fd) && fclose($fd)) {
                                    unset($watchFiles[$event['wd']][$fn]);
                                }
                                $filename = $watchDirs[$event['wd']] . DIRECTORY_SEPARATOR . $event['name'];
                                if (false === ($fd = fopen($filename, "r"))) {
                                    $this->_logger->err("File '{$filename}' is not readable\n");
                                    continue;
                                }
                                if ($event['mask'] & IN_MODIFY) {
                                    $this->_ffseek($fd);
                                }
                                $watchFiles[$event['wd']][$event['name']] = $fd;
                            } else {
                                
                                // 如果不是最新的日志文件（重写的上一个小时的日志文件），则不处理。
                                continue;
                            }
                        } else {
                            $fd = $watchFiles[$event['wd']][$event['name']];
                        }
                    } else {
                        continue;
                        // $fd = $watchFiles[$event['wd']];
                    }
        
                    // 读取日志并分析
                    $raw = '';
                    $lines = 0;
                    while (true) {
                        $raw .= $block = fread($fd, 4096);
                        if (false !== ($pos = strpos($raw, "\n"))) {
                            $rawline = substr($raw, 0, $pos + 1);
                            try {
                                $mlog = new ZtChart_Model_Monitor_Log($rawline);
                                $line = $mlog->transform();
                                if (!empty($line)) {
                                    $this->_shmWrite($mlog->getShmIdentifier(), $line);
                                    $lines++;
                                }
                            } catch (ZtChart_Model_Monitor_Log_Exception $e) {
                                $this->_logger->warn("日志处理错误：" . iconv('GBK', 'UTF-8', $rawline));
                            }
                            $raw = substr($raw, $pos + 1);
                        } else {
                            if (($offset = strlen($block)) > 0) {
                                fseek($fd, -$offset, SEEK_CUR);
                            }
                            break;
                        }
                    }
                    if (0 != $lines) {
                        $this->_logger->info(sprintf("%4s行有效数据已处理：%s", $lines, $watchDirs[$event['wd']] . '/' . $event['name']));
                    }
                }
            }
        }
        
        foreach ($watchFiles as $watch => $fd) {
            if (is_resource($fd)) {
                $fd = (array) $fd;
            }
            array_walk($fd, 'fclose');
            inotify_rm_watch($inotify, $watch);
        }
        fclose($inotify);
    }
    
    /**
     * 读取共享内存数据
     *
     * @param integer $type
     * @param boolean $flag
     * @return string
     */
    protected function _shmRead($type, $flag = false)
    {
        if (false === sem_acquire($this->_semId)) {
            $errors = error_get_last();
            throw new ZtChart_Model_Monitor_Daemon_Exception($errors['message']);
        }
        $shmvar = shm_has_var($this->_shmId, $type) ? shm_get_var($this->_shmId, $type) : '';
        if (true === $flag && shm_has_var($this->_shmId, $type)) {
            shm_remove_var($this->_shmId, $type);
        }
        if (false === sem_release($this->_semId)) {
            $errors = error_get_last();
            throw new ZtChart_Model_Monitor_Daemon_Exception($errors['message']);
        }
    
        return $shmvar;
    }
    
    /**
     * 写入共享内存数据
     *
     * @param integer $type
     * @param string $value
     * @return boolean
     */
    protected function _shmWrite($type, $value)
    {
        if (false === sem_acquire($this->_semId)) {
            $errors = error_get_last();
            throw new ZtChart_Model_Monitor_Daemon_Exception($errors['message']);
        }
        $shmvar = (shm_has_var($this->_shmId, $type) ? shm_get_var($this->_shmId, $type) : '') . $value;
        $result = shm_put_var($this->_shmId, $type, $shmvar);
        if (false === sem_release($this->_semId)) {
            $errors = error_get_last();
            throw new ZtChart_Model_Monitor_Daemon_Exception($errors['message']);
        }
    
        return $result;
    }
    
    /**
     * 定位文件指针到文件最后一个换行符之后
     * 
     * @param resource $fd
     * @return boolean
     */
    private function _ffseek($fd) {
        $offset = 512;
        if (-1 == fseek($fd, -$offset, SEEK_END)) {
            return false;
        }
        if (false !== ($block = fread($fd, $offset))) {
            if (false !== ($pos = strrpos($block, "\n"))) {
                $offset = strlen($block) - $pos - 1;
            }
        }
        
        return 0 == fseek($fd, -$offset, SEEK_CUR);
    }
    
    /**
     * 清除共享内存和信号量
     */
    public function __destruct() {
        if (is_resource($this->_shmId)) {
            shm_remove($this->_shmId);
        }
        if (is_resource($this->_semId)) {
            sem_remove($this->_semId);
        }
    }
}