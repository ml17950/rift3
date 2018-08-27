<?php
// last change: 2018-08-27

	//error_reporting(E_ALL);
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', 1);

	define('VERSION', '2018-08-27');

	define('ABSPATH', dirname(__FILE__));
	define('CHMODMASK', 0775);

	if (strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4)) == 'wget')
		define('CLIENT', 'crond');
	elseif (strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 3)) == 'esp')
		define('CLIENT', 'esp');
	elseif (strpos($_SERVER['REQUEST_URI'], 'ifttt') !== false)
		define('CLIENT', 'ifttt');
	else
		define('CLIENT', 'web');

	if (empty($_SERVER['REQUEST_SCHEME']))
		define('BASEURL', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']));
	else
		define('BASEURL', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']));

	// ###########################################
	// ### change only defines below this line ###
	// ###########################################

	date_default_timezone_set('Europe/Berlin');

	define('UI_LANGUAGE', 'de');

	define('TODAY', mktime(0,0,0));
	define('YESTERDAY', TODAY - 86400);

	define('MQTT_BROKER_ADDR',	'localhost');
	define('MQTT_BROKER_PORT',	1883);
	define('MQTT_USERNAME',		'');
	define('MQTT_PASSWORD',		'');
	define('MQTT_UNDEF_PAYLOAD','0');

	include_once('_telegram.php');
?>