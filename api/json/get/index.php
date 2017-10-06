<?php
	include_once('../../../_config.php');
	include_once('../../../_common.php');
	include_once('../../../class.rift3.php');
	
	$what	= param('w', 'unknown');
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
	
	switch ($what) {
		case 'debug':
			$rift3->device_readall();
			$rift3->status_readall();
			$rift3->types_readall();
// 			$rift3->debug();
			echo $rift3->ajax_get_last_data_as_json();
			break;
		
		case 'lastchg':
			echo $rift3->ajax_get_last_change();
			break;
		
		case 'lastdata':
			$rift3->device_readall();
			$rift3->status_readall();
			$rift3->types_readall();
			echo $rift3->ajax_get_last_data_as_json();
			break;
		
		case 'logdata':
			echo $rift3->ajax_get_log_as_json();
			break;
		
		default:
			echo "ERR:",$what;
	}
?>