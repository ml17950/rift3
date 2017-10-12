<?php
	//error_reporting(E_ALL);
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', 1);
	
	define('ABSPATH', dirname(__FILE__));
	define('CHMODMASK', 0775);
	
	define('YES', 'ON');
	define('NO', 'OFF');
	define('YES2NO', 'ON2OFF');
	define('NO2YES', 'OFF2ON');
	
	define('ON', 'ON');
	define('OFF', 'OFF');
	define('ON2OFF', 'ON2OFF');
	define('OFF2ON', 'OFF2ON');
	
	define('UNKNOWN', 'UNKNOWN');
	
	// ###########################################
	// ### change only defines below this line ###
	// ###########################################
	
	date_default_timezone_set('Europe/Berlin');
	

?>