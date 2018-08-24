<?php
// last change: 2018-07-05
	include_once('_sysconfig.php');
	include_once('lib/common.php');
	include_once('lib/class.ui.php');

	$ui = new clsUserInterface();

	$ui->meta();
	$ui->header(TXTLOG);
	$ui->navigation();
	$ui->log->search();
	$ui->log->display();
	$ui->footer();
?>