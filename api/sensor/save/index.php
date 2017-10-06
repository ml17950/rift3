<?php
	include_once('../../../_config.php');
	include_once('../../../_common.php');
	include_once('../../../class.rift3.php');
	
	$old_id		= param('oid');
	$new_id		= param('sid');
	$name		= param('sname');
	$type		= param('stype');
	$client		= param('client', 'unknown');
	
// 	print_r($_POST);
	
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

	if (empty($old_id)) {
		$new_id = $rift3->text2id($new_id);
		//$new_id = $rift3->generate_guid(); // $old_id =
	}
	elseif ($old_id != $new_id) {
		if (!$rift3->sensor_rename($old_id, $new_id)) {
			echo "ERR:sensor_rename";
			exit;
		}
	}
	
	$rift3->sensor_save($new_id, $name, $type);
?>