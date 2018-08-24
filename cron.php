<?php
// last change: 2018-07-30
	include_once('_sysconfig.php');
	include_once('lib/common.php');
	include_once('lib/lang_'.UI_LANGUAGE.'.php');
	include_once('lib/class.rift3.php');

	$rift3 = new clsRIFT3();

	$rift3->cronjobs_execute();
	$rift3->run_external_sensors();

	$rift3->rules_check_conditions();

	if (date('H:i') == '02:16')
		$rift3->log_resize(500);
?>