#!/usr/bin/env php

<?php

// Check PHP version
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    exit('The system must be run at least PHP version 5.3.0, current version is: ' . PHP_VERSION);
}

// Define path to console bin directory
defined('APPLICATION_BIN')
    || define('APPLICATION_BIN', realpath(__DIR__));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/console.ini'
);
$application->bootstrap();

try {
    $monitor = new ZtChart_Model_Monitor('Archive');
    $monitor->daemon();
} catch (ZtChart_Model_Monitor_Console_Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}