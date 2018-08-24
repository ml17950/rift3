<?php
// last change: 2018-02-23
	include_once('_sysconfig.php');
	include_once('lib/common.php');
	include_once('lib/class.ui.php');

	$ui = new clsUserInterface();

	$do = param('do');

	if ($do == 'save-cronjobs') {
		unset($_POST['do']);
		$ui->rift3->cronjobs = array();
		foreach ($_POST as $jobid => $jobparam) {
			if ($jobparam['run-param'] != '-:-:-') {
				$fields = explode(':', $jobparam['run-param']);
				$ui->rift3->cronjobs_update_item($jobid, $jobparam['chk-sensor'], $jobparam['chk-value'], $fields[0], $fields[1], $fields[2]);
			}
		}
		$ui->rift3->cronjobs_write();
		header('Location: cronjobs.php');
		exit;
	}

	$ui->meta();
	$ui->header(TXTCRONJOBS);
	$ui->navigation();
	$ui->cronjobs->display();
	$ui->footer();
?>