<?php
// last change: 2018-07-09
	include_once('_sysconfig.php');
	include_once('lib/common.php');
	include_once('lib/class.ui.php');

	$ui = new clsUserInterface();

	$do = param('do');
	$view = param('view', 'overview');

	if ($do == 'save-systemcfg') {
// 		debugarr($_POST);
// 		debugarr($ui->rift3->config['widgets']);
		for ($i=0; $i<8; $i++) {
			$ui->rift3->config['widgets'][$i] = $_POST['widget'][$i];
		}
		$ui->rift3->config_has_changed = true;
		header('Location: config.php');
		exit;
	}
	elseif ($do == 'sendcmd') {
		$id = param('id');
		$cmd = param('cmd');
		$ui->rift3->device_send_control_command($id, $cmd);
		if ($view != 'overview') {
			usleep(500000); // wait for 0,5 seconds
			header('Location: config.php?view='.$view.'&id='.$id);
		}
		else
			header('Location: config.php');
		exit;
	}
	elseif ($do == 'delete') {
		$id = param('id');
		$ui->rift3->device_unregister($id);
		header('Location: config.php');
		exit;
	}

	$ui->meta(false);
	$ui->header(TXTCONFIG);
	$ui->navigation();
	switch ($view) {
		case 'devicecfg':
			$id = param('id');
			$ui->config->formHeader('save-devicecfg');
			$ui->config->display_device_configuration($id);
			$ui->config->formFooter();
			break;

		case 'overview':
			$ui->config->formHeader('save-systemcfg');
			$ui->config->display_devices();
			$ui->config->display_system();
			$ui->config->display_widgets();
			$ui->config->display_mqtt();
			$ui->config->display_types();
			$ui->config->formFooter();
			break;
	}
	$ui->footer();

// 	echo "<div class='debug'>";
// 	debugarr($ui->rift3->config);
// 	echo "</div>";
?>