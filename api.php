<?php
// last change: 2018-08-10
	define('DEBUG' , false);
// 	define('DEBUG' , true);

	include_once('_sysconfig.php');
	include_once('lib/common.php');
	include_once('lib/lang_'.UI_LANGUAGE.'.php');
	include_once('lib/class.rift3.php');

	$rift3 = new clsRIFT3();

// $rift3->dbgout = true;

	$path = trim($_GET['path']);
	$data = trim($_GET['data']);
	$payload = trim($_POST['payload']);
	$path_array = explode('/', $path);
	$req_format		= $path_array[0]; // http, json, mqtt, etc
	$req_section	= $path_array[1]; // config, sensor, trigger, status, etc
	

	if (DEBUG) {
		
// 		file_put_contents(ABSPATH.'/config/rift3.log', date('d.m.y H:i:s')."\t".$_SERVER['REDIRECT_URI']."\t".print_r($_SERVER, true)."\r\n", FILE_APPEND);
// 		chmod(ABSPATH.'/config/rift3.log', CHMODMASK);
		
// 		echo nl2br(print_r($_SERVER, true));
		
		echo $_SERVER['REDIRECT_URI'];
		echo "<hr>";
		echo "Path: [",$path,"]<br>";
		echo "Data: [",$data,"]<br>";
		echo "<hr>";
		echo nl2br(print_r($path_array, true));
		echo "<hr>";
		echo "req_format : [",$req_format,"]<br>";
		echo "req_section: [",$req_section,"]<br>";
	}
	
	switch ($req_format) {
		case 'ohoco':
			$req_device_id = $path_array[2];

// 			echo " {",$req_section,"}";
// 			echo " (",$req_device_id,")";
// 			echo " [",$payload,"] :: ";

			switch ($req_section) {
				case 'device':
					$rift3->device_register($req_device_id, $payload, 'HTTP');
					echo 'OK';
					break;

				case 'config':
					$rift3->device_config_save($req_device_id, $payload);
					echo 'OK';
					break;

				case 'ping':
					$rift3->device_alive($req_device_id, $payload);
					echo 'OK';
					break;

				case 'log':
					$rift3->log('config', $req_device_id, $payload);
					echo 'OK';
					break;

				case 'sensor':
					$rift3->sensor_register($req_device_id, $payload);
					echo 'OK';
					break;

				case 'switch':
					$rift3->switch_register($req_device_id, $payload, 'UDP');
					echo 'OK';
					break;

				case 'status':
					$rift3->status_set_value($req_device_id, $payload, true);
// 					$rift3->rules_check_conditions();
					echo 'OK';
					break;

				case 'trigger':
					$rift3->trigger_activate($req_device_id);
					echo 'OK';
					break;

				default:
					$rift3->log('error', $req_section."/".$req_device_id." :: ".$payload, 'ERROR');
					die("unknown req_section (".$req_section."/".$req_device_id." :: ".$payload.")");
			}
			break;

		case 'ui':
			$rift3->client = 'ui';

			$req_device_id = $path_array[2];
			$req_action = $path_array[3];
			
// 			CFG:checkInterval=5000
// 			echo " {",$req_section,"}";
// 			echo " (",$req_device_id,")";
// 			echo " [",$req_action,"] :: ";
			
			switch ($req_section) {
				case 'config':
					$cmd = trim($_POST['cmd']);
					if ($req_action == 'set') {
						$rift3->device_send_control_command($req_device_id, $cmd);
						$rift3->device_send_control_command($req_device_id, 'SENDCFG');
					}
					elseif ($req_action == 'save') {
						$rift3->device_send_control_command($req_device_id, $cmd);
						usleep(500000); // wait for 0,5 seconds
					}
					echo "[[",$cmd,"]]";
					break;

				case 'switch':
					$req_device_id		= $path_array[2];
					$req_device_action	= $path_array[3];
// 					if (DEBUG) {
// 						echo "req_device_id: [",$req_device_id,"]<br>";
// 						echo "req_device_action : [",$req_device_action,"]<br>";
// 					}
					if ($req_device_action == 'on')
						$rift3->switch_turn_on($req_device_id);
					elseif ($req_device_action == 'off')
						$rift3->switch_turn_off($req_device_id);
					elseif ($req_device_action == 'toggle') {
						if ($rift3->status[$req_device_id]['status'] == 'off')
							$rift3->switch_turn_on($req_device_id);
						else
							$rift3->switch_turn_off($req_device_id);
					}
					else
						die("unknown req_device_action (".$req_device_action.")");
					break;
			}
// 		exit;
			break;

		case 'http':
			switch ($req_section) {
				case 'switch':
					$req_device_id		= $path_array[2];
					$req_device_action	= $path_array[3];
					if (DEBUG) {
						echo "req_device_id: [",$req_device_id,"]<br>";
						echo "req_device_action : [",$req_device_action,"]<br>";
					}
					if ($req_device_action == 'on')
						$rift3->switch_turn_on($req_device_id);
					elseif ($req_device_action == 'off')
						$rift3->switch_turn_off($req_device_id);
					elseif ($req_device_action == 'toggle') {
						if ($rift3->status[$req_device_id]['status'] == 'off')
							$rift3->switch_turn_on($req_device_id);
						else
							$rift3->switch_turn_off($req_device_id);
					}
					else
						die("unknown req_device_action (".$req_device_action.")");
					break;

				case 'config':
					$req_action		= $path_array[2]; // setval, getval, setip, etc

					switch ($req_action) {
						case 'setip':
							$req_device_name	= $path_array[3];
							$req_device_ip		= $path_array[4];
							$req_device_reason	= $path_array[5];
							if (DEBUG) {
								echo "req_action : [",$req_action,"]<br>";
								echo "req_device_name: [",$req_device_name,"]<br>";
								echo "req_device_ip : [",$req_device_ip,"]<br>";
								echo "req_device_reason : [",$req_device_reason,"]<br>";
							}
							if (!empty($req_device_name)) {
								$rift3->config_set($req_device_name, $req_device_ip, $req_device_reason);
							}
							break;

						case 'setvcc':
							$req_device_name	= $path_array[3];
							$req_device_vcc		= $path_array[4];
							if (!empty($req_device_name)) {
								$rift3->log('config', $req_device_name, $req_device_vcc.' V', 'battery');
							}
							break;

						default:
							die("unknown req_action (".$req_action.")");
					}
					break;

				case 'device':
					$req_device_id		= $path_array[2];
					$req_device_action	= $path_array[3];
					if (DEBUG) {
						echo "req_device_id: [",$req_device_id,"]<br>";
						echo "req_device_action : [",$req_device_action,"]<br>";
					}
					if ($req_device_action == 'on')
						$rift3->device_turn_on($req_device_id);
					elseif ($req_device_action == 'off')
						$rift3->device_turn_off($req_device_id);
					else
						die("unknown req_device_action (".$req_device_action.")");
					break;

				case 'sensor':
					$req_action		= $path_array[2]; // set, get
					$req_device_id	= $path_array[3];

					if ($req_action == 'set') {
						$req_device_val	= $path_array[4];
						$rift3->status_set_value($req_device_id, $req_device_val, true);
// 						$rift3->rules_check_conditions();
						echo "OK";
					}
					elseif ($req_action == 'get') {
						echo $rift3->status_get_value($req_device_id);
					}
					else
						die("unknown req_action (".$req_action.")");
					break;

				case 'trigger':
					$req_device_id		= $path_array[2];
					$rift3->trigger_activate($req_device_id);
					break;

				default:
					die("unknown req_section (".$req_section.")");
			}
			break;

// 		case 'json':
// 			break;

// 		case 'mqtt':
// 			break;

		default:
			die("unknown req_format (".$req_format.")");
	}

	if (DEBUG) {
		echo "<hr><hr>";
		$rift3->debug();
	}
?>