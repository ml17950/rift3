<?php
// last change: 2017-11-23
	include_once('_config.php');
	include_once('_common.php');
	include_once('lib/class.ui.php');
	
	$ui = new clsUserInterface();
	
	$do = param('do');
	
	$ui->meta();
	
	switch ($do) {
		case 'edit-trigger':
			$id = param('id');
			$ui->header(TXTRECEIPE_EDIT);
			$ui->navigation();
			$ui->receipes->display_edit_form($id);
			break;

		case 'save-receipe':
			$ui->rift3->receipe_save($_POST);
			$ui->success_message(TXTRECEIPE_SAVED, 'receipes.php');
			break;

		default:
			$ui->header(TXTRECEIPES);
			$ui->navigation();
			$ui->receipes->display_overview();
	}
	
	$ui->footer();
?>