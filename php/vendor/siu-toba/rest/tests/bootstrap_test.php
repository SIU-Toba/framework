<?php

//set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

$autoloader = require_once __DIR__.'/../vendor/autoload.php';

//SIUToba\rest\bootstrap::registerAutoloader();
//
//function customAutoLoader($class)
//{
//	$file = rtrim(dirname(__FILE__), '/') . '/' . $class . '.php';
//	if (file_exists($file)) {
//		require $file;
//	} else {
//		return;
//	}
//}
//
//spl_autoload_register('customAutoLoader');
