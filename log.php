<?php
// last change: 2017-11-20
	include_once('_config.php');
	include_once('_common.php');
	include_once('lib/class.ui.php');
	
	$ui = new clsUserInterface();
	
	$ui->meta();
	$ui->header(TXTLOG);
	$ui->navigation();
	$ui->log->display();
	$ui->footer();
?>