<?php
//指向public的上一级目录
define('APP_PATH', realpath(dirname(__FILE__) . '/../'));

$application = new Yaf_Application( APP_PATH . "/conf/application.ini");

$application->bootstrap()->run();
?>
