<?php
// last change: 2017-12-06
	include_once('_config.php');
	include_once('_common.php');
	include_once('lib/class.rift3.php');

	define('CLIENT', 'crond');

	$rift3 = new clsRIFT3();

	$rift3->sensor_updateall();

	$rift3->notifier_initialize();
	$rift3->action_initialize();
	$rift3->receipe_initialize();
	$rift3->sensor_initialize();
	$rift3->device_initialize();

	$rift3->receipe_check_trigger();

	if (intval(date('i')) == 0)
		$rift3->log_resize(500);
?>