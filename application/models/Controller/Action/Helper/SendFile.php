<?php

/**
 * 平台数据实时监控系统
 *
 * @category ZtChart
 * @package ZtChart_Model_Controller
 * @subpackage ZtChart_Model_Controller_Action_Helper
 * @copyright Copyright (c) 2004 - 2012 平台中心技术部
 * @author $Author: zhangweiwen $
 * @version $Id: SendFile.php 37708 2012-12-17 08:26:09Z zhangweiwen $
 */

/**
 * 动作助手——发送文件到浏览器
 *
 * @name ZtChart_Model_Controller_Action_Helper_SendFile
 * @see Zend_Controller_Action_Helper_Abstract
 */
class ZtChart_Model_Controller_Action_Helper_SendFile extends Zend_Controller_Action_Helper_Abstract {
    
    /**
     * 发送文件
     * 
     * @param string $filename
     * @param string $filepath
     * @return void
     */
    public function direct($filepath, $filename = null) {
        $request = $this->_actionController->getRequest();
        $response = $this->_actionController->getResponse();
        
        $this->_actionController->getHelper('Layout')->disableLayout();
        $this->_actionController->getHelper('ViewRenderer')->setNoRender();
        if (empty($filename)) {
            $filename = basename($filepath);
        }
        if (false !== strpos($request->getServer('HTTP_USER_AGENT'), 'MSIE')) {
            $filename = rawurlencode($filename);
        }
        $response->setHeader('Content-Encoding', 'no-gzip')
                 ->setHeader('Content-Transfer-Encoding', 'binary')
                 ->setHeader('Content-Type', 'application/octet-stream')
                 ->setHeader('Content-Disposition', 'attachment; filename=' . $filename)
                 ->clearBody();
        readfile($filepath);
    }
}