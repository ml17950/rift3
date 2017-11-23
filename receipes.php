<?php
	include_once('_config.php');
	include_once('_common.php');
	include_once('lib/class.ui.php');
	
	$ui = new clsUserInterface();
	
	$do = param('do');
	
	$ui->meta();
	$ui->header(TXTRECEIPES);
	$ui->navigation();
	
	switch ($do) {
		case 'edit-trigger':
			$id = param('id');
			$ui->receipes->display_edit_form($id);
			break;
		
		default:
			$ui->receipes->display_overview();
	}
	
	$ui->footer();
?>