<?php
$time_start = microtime(true);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Athens');

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../../_zend/1-11-2/library'),get_include_path())));

/** Zend_Application */
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV,APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap()->run();

// PTX Debug.
if(isset($_GET['ptxdebug']) && $_GET['ptxdebug'] == 'true') {
    $time_end = microtime(true);
    $time = $time_end - $time_start;
    
    echo '<p class="msg info">Time: <b>'.$time.'</b> s</p>';
}