<?php
// last change: 2018-04-24
	include_once('_sysconfig.php');
	include_once('lib/common.php');
	include_once('lib/class.ui.php');

	$ui = new clsUserInterface();

	$ui->meta();
	$ui->header(TXTHOME);
	$ui->navigation();
	$ui->home->display_widgets();
	$ui->home->display_switches();

// 	echo "<div class='debug'>";
// 	debugarr($ui->rift3->config);
// 	echo "</div>";

	$ui->footer();
?>