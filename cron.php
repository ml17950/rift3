<?php
	include_once('_config.php');
	include_once('_common.php');
	include_once('class.rift3.php');
	
	define('CLIENT', 'crond');
	
	$rift3 = new clsRIFT3();
	
	$rift3->sensor_updateall();
	
	$rift3->notifier_initialize();
	$rift3->action_initialize();
	$rift3->receipe_initialize();
	$rift3->sensor_initialize();
	$rift3->device_readall();
	$rift3->status_readall();
	
	$rift3->receipe_check_trigger();
	
	if (intval(date('i')) == 0)
		$rift3->log_resize(500);
	
// 	$rift3->log_resize(5);
?>