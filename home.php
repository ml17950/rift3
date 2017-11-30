<?php
// last change: 2017-11-30
	include_once('_config.php');
	include_once('_common.php');
	include_once('lib/class.ui.php');
	
	$ui = new clsUserInterface();
	
	$ui->meta();
	$ui->header();
	$ui->navigation();
	$ui->home->display_widgets();
	$ui->home->display_switches();
	$ui->footer();
?>