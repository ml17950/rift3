<?php
// last change: 2018-06-01
	include_once('_sysconfig.php');
	include_once('lib/common.php');
	include_once('lib/class.ui.php');

	$ui = new clsUserInterface();

	$do = param('do');

	if ($do == 'activate') {
		$id = param('id');
		$ui->rift3->trigger_activate($id);
		header('Location: trigger.php');
		exit;
	}
	elseif ($do == 'delete') {
		$id = param('id');
		$ui->rift3->trigger_unregister($id);
		header('Location: trigger.php');
		exit;
	}

	$ui->meta();
	$ui->header(TXTTRIGGER);
	$ui->navigation();
	$ui->trigger->display();
// 	$ui->trigger->display_old();
	$ui->footer();
?>