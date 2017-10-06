<?php
	include_once('_config.php');
	include_once('_common.php');
	include_once('class.rift3.php');
	
	define('CLIENT', 'crond');
	
	$rift3 = new clsRIFT3();
	
	$rift3->sensor_updateall();
	
	$rift3->device_readall();
	$rift3->status_readall();
	$rift3->action_readall();
	$rift3->receipe_readall();
	
	$rift3->receipe_execute();
	
// 	$rift3->log_resize(5);
?>