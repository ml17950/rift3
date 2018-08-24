<?php
// last change: 2018-07-30
	include_once('_sysconfig.php');
	include_once('lib/common.php');
	include_once('lib/class.ui.php');

	$ui = new clsUserInterface();

	$do = param('do');

	if ($do == 'delete') {
		$id = param('id');
		$ui->rift3->sensor_unregister($id);
		header('Location: sensors.php');
		exit;
	}
	elseif ($do == 'save-name') {
		$id = param('id');
		$nn = param('newname');
		if (!empty($nn))
			$ui->rift3->sensor_set_name($id, $nn);
		$nv = param('newvalue');
		if (!empty($nv))
			$ui->rift3->status_set_value($id, $nv, false);
		header('Location: sensors.php');
		exit;
	}

	$ui->meta();
	$ui->header(TXTSENSORS);
	$ui->navigation();
	if ($do == 'rename') {
		$id = param('id');
		$ui->sensors->rename($id);
	}
	else
		$ui->sensors->display();
	$ui->footer();
?>