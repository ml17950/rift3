<?php
// last change: 2017-12-01
	include_once('../../../_config.php');
	include_once('../../../_common.php');
	include_once('../../../lib/class.rift3.php');

	$id		= param('id');
	$value	= param('v');
	$type	= param('t');
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

	$rift3->status_save_and_log($id, $value, $type);
	$sensor_name = $rift3->sensor_getname($id);
	$rift3->log($sensor_name, $value);
	echo $rift3->status_read($id);
?>