<?php
// last change: 2018-07-30
	include_once('_sysconfig.php');
	include_once('lib/common.php');
	include_once('lib/class.ui.php');

	$ui = new clsUserInterface();

	$do = param('do');

	if ($do == 'activate-rule') {
		$id = param_int('id');
		$ui->rift3->rules[$id]['active'] = 1;
		$ui->rift3->rules_write();
		header('Location: rules.php');
		exit;
	}
	elseif ($do == 'deactivate-rule') {
		$id = param_int('id');
		$ui->rift3->rules[$id]['active'] = 0;
		$ui->rift3->rules_write();
		header('Location: rules.php');
		exit;
	}
	elseif ($do == 'create-rule') {
		$id = time();
		$ui->rift3->rules[$id]['name'] = 'Regeln '.date('d.my H:i:s');
		$ui->rift3->rules[$id]['active'] = 0;
		$ui->rift3->rules[$id]['conditions'] = array();
		$ui->rift3->rules[$id]['action'] = array();

		$ui->rift3->rules_write();
		header('Location: rules.php?do=edit&id='.$id);
		exit;
	}
	elseif ($do == 'save-rule') {
// debugarr($_POST);
		$id = param_int('ruleid');
// debugarr($ui->rift3->rules[$id]);
		
		$ui->rift3->rules[$id]['name'] = param('rulename');

		for ($cond_index=1; $cond_index<4; $cond_index++) {
			if (($_POST['cond_status_'.$cond_index] != '-') && ($_POST['cond_type_'.$cond_index] != '-')) {
				$ui->rift3->rules[$id]['conditions'][$cond_index]['status'] = param('cond_status_'.$cond_index);
				$ui->rift3->rules[$id]['conditions'][$cond_index]['type'] = param('cond_type_'.$cond_index);
				$ui->rift3->rules[$id]['conditions'][$cond_index]['value'] = param('cond_value_'.$cond_index);
			}
			else
				unset($ui->rift3->rules[$id]['conditions'][$cond_index]);
		}

		if (!empty($_POST['statusidnew'])) {
			$ui->rift3->rules[$id]['action']['status']['id'] = param('statusidnew');
			$ui->rift3->status[$ui->rift3->rules[$id]['action']['status']['id']]['status'] = '-';
			$ui->rift3->status[$ui->rift3->rules[$id]['action']['status']['id']]['change'] = 0;
			$ui->rift3->status_has_changed = true;
		}
		else
			$ui->rift3->rules[$id]['action']['status']['id'] = param('statusid');
		$ui->rift3->rules[$id]['action']['status']['value'] = param('statusval');

		if (!empty($_POST['triggeridnew'])) {
			$ui->rift3->rules[$id]['action']['trigger']['id'] = param('triggeridnew');
			$ui->rift3->trigger[$ui->rift3->rules[$id]['action']['trigger']['id']][0] = 'empty/created';
			$ui->rift3->trigger_write();
		}
		else
			$ui->rift3->rules[$id]['action']['trigger']['id'] = param('triggeridold');

		
// debugarr($ui->rift3->rules[$id]);
		
		$ui->rift3->rules_write();
		header('Location: rules.php?do=edit&id='.$id);
		exit;
	}

	$ui->meta();
	$ui->header(TXTRULES);
	$ui->navigation();
	if ($do == 'edit') {
		$id = param('id');
		$ui->rules->edit($id);
	}
	else
		$ui->rules->display();
	$ui->footer();
?>