<?php
	include_once('../../../_config.php');
	include_once('../../../_common.php');
	include_once('../../../class.rift3.php');
	
	$id		= param('id');
	$value	= param('v');
	$client	= param('c', 'unknown');
	
	if ($client == 'unknown') {
		$useragent = substr($_SERVER['HTTP_USER_AGENT'], 0, 4);
		if ($useragent == 'Wget')
			$client = 'cron';
		elseif ($useragent == 'ESP8')
			$client = 'esp';
		else
			$client = 'web';
	}
	
	define('CLIENT', $client);
	
	$rift3 = new clsRIFT3();
	
	$rift3->device_readall();
	
	if (array_key_exists($id, $rift3->devices)) {
		$rift3->status_readall();
		$rift3->action_initialize();
		$rift3->receipe_run_action($id, $value, ' ');
		echo $rift3->sensor_status[$id];
	}
	else
		echo "ERROR";
?>