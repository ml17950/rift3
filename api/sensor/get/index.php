<?php
	include_once('../../../_config.php');
	include_once('../../../_common.php');
	include_once('../../../class.rift3.php');
	
	$id		= param('id');
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
	
	echo $rift3->status_read($id);
?>