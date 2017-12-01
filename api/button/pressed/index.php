<?php
// last change: 2017-12-01
	include_once('../../../_config.php');
	include_once('../../../_common.php');
	include_once('../../../lib/class.rift3.php');

	$id		= param('id');
	$client	= param('c', 'esp');

	define('CLIENT', $client);

	$rift3 = new clsRIFT3();

	$rift3->log($id, "OK");
?>