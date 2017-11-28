<?php
	include_once('_config.php');
	include_once('_common.php');
	include_once('lib/class.ui.php');
	
	$ui = new clsUserInterface();
	
	$ui->meta();
	$ui->header(TXTSENSORS);
	$ui->navigation();
	$ui->sensors->display();
	$ui->footer();
?>