<?php
// last change: 2018-08-24
class clsRIFT3 {
	var $dbgout;
	var $config;
	var $status;
	var $trigger;
	var $cronjobs;
	var $rules;
	var $status_has_changed;
	var $config_has_changed;
	var $client;

	function __construct($read_config = true) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		$this->dbgout = false;

		$this->config = array();
		$this->status = array();
		$this->trigger = array();
		$this->status_has_changed = false;
		$this->config_has_changed = false;
		$this->client = CLIENT;

		if ($read_config) {
			$this->config_read();
			$this->status_read_from_file();
		}
	}

	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		if ($this->status_has_changed)
			$this->status_write_to_file();
		if ($this->config_has_changed)
			$this->config_write();
	}

	function debug() {
		echo highlight_string(print_r($this->conf, true));
	}

	function log($section, $message, $value = '', $info = '') {
		if (array_key_exists($message, $this->config['names']))
			$message = $this->config['names'][$message];
		if (array_key_exists($message, $this->config['devices']))
			$message = $this->config['devices'][$message]['name'];
		file_put_contents(ABSPATH.'/config/rift3.log', date('c')."\t".$this->client."\t".$section."\t".$message."\t".$value."\t".$info."\r\n", FILE_APPEND);
	}
	
	function log_read() {
		$file = ABSPATH.'/config/rift3.log';

		if (!is_file($file)) {
			file_put_contents($file, date('c')."\t".$this->client."\tconfig\tLogfile\tcreated\t\r\n");
			chmod($file, CHMODMASK);
			return array();
		}

		$lines = file($file);
		krsort($lines);

		$arr = array();
		$cnt = 0;
		foreach ($lines as $line) {
			if (!empty($line)) {
				$fields = explode("\t", trim($line));

				$arr[$cnt]['datetime']	= $fields[0];
				$arr[$cnt]['client']	= $fields[1];
				$arr[$cnt]['section']	= $fields[2];
				$arr[$cnt]['message']	= $fields[3];
				$arr[$cnt]['value']		= $fields[4];
				$arr[$cnt]['info']		= $fields[5];
				$cnt++;
			}
		}

		return $arr;
	}

	function log_resize($num_of_lines = 100) {
		$file = ABSPATH.'/config/rift3.log';
		$lines = file($file); // reads the file into an array by line
		$keep = array_slice($lines, ($num_of_lines * -1), $num_of_lines); // keep the last n elements of the array
		file_put_contents($file, implode("", $keep)); // combine array and write it back to file
		$this->log('cronjob', 'Resize Logfile', 'executed');
	}

	function config_read() {
// 		$this->conf['devices'] = array();
// 		$this->conf['sensors'] = array();
// 		$this->trigger = array();
// 		$this->conf['receipes'] = array();
// 		$this->conf['config'] = array();
// 
// 		$config_file = ABSPATH.'/config/devices.conf';
// 		if (file_exists($config_file)) {
// 			$lines = file($config_file);
// 			foreach ($lines as $line) {
// 				$fields = explode('->', trim($line));
// 				$device_id = $fields[0];
// 				$this->conf['devices'][$device_id]['name'] = $fields[2];
// 				$this->conf['devices'][$device_id]['control-type'] = $fields[1];
// 				$this->conf['devices'][$device_id]['display-type'] = $fields[3];
// 				$this->conf['devices'][$device_id]['on-param'] = $fields[4];
// 				$this->conf['devices'][$device_id]['off-param'] = $fields[5];
// 				$this->conf['devices'][$device_id]['room'] = $fields[6];
// 				$this->conf['devices'][$device_id]['action-url'] = 'http://%SOCKETCONTROL%/%DEVICEID%/%ONOFFPARAM%';
// 
// 				$this->status[$device_id]['status'] = 'unknown';
// 				$this->status[$device_id]['change'] = '0';
// 			}
// 		}

		// read trigger from file
		$this->trigger_read();

		// read base-config from file
		$config_file = ABSPATH.'/config/config.ser';
		if (file_exists($config_file)) {
			$serialized_data = file_get_contents($config_file);
			$this->config = unserialize($serialized_data);
		}

		if (!is_array($this->config['names']))
			$this->config['names']['time'] = TXTTIME;

		// read rules from file
		$this->rules_read();
		
// 		// read external switches from file
// 		$config_file = ABSPATH.'/config/switch.conf';
// 		if (file_exists($config_file)) {
// 			$lines = file($config_file);
// 			foreach ($lines as $line) {
// 				$fields = explode('->', trim($line));
// 				debugarr($fields);
// // 				if (count($fields) > 2) {
// // 					$this->trigger[$fields[0]][] = $fields[1].'/'.$fields[2];
// // 				}
// 			}
// 		}
// exit;




// $this->config['widgets'][0] = 'ESP-Light-Sensor';
// $this->config['widgets'][1] = 'ifttt-weather';
// $this->config['widgets'][2] = 'ESP-Temp-Sensor';
// $this->config['widgets'][3] = 'time';
// $this->config['widgets'][4] = '';
// $this->config['widgets'][5] = '';
// $this->config['widgets'][6] = '';
// $this->config['widgets'][7] = '';

// $this->config['types']['ESP-Light-Sensor'] = 'daynight';
// $this->config['types']['ESP-Temp-Sensor'] = 'temperature';
// $this->config['types']['ifttt-weather'] = 'weather';
// $this->config['types']['time'] = 'time';
// 
// $this->config_has_changed = true;

		// TODO
// 		$this->conf['config']['ESP-Socket-Control'] = file_get_contents(ABSPATH.'/config/ESP-Socket-Control.device');
	}

	function config_write() {
		asort($this->config['names']);

		$file = ABSPATH.'/config/config.ser';
		$serialized_data = serialize($this->config);
		file_put_contents($file, $serialized_data);
		chmod($file, CHMODMASK);
	}

	function config_set($device_id, $value, $info = '') {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		$config_file = ABSPATH.'/config/'.$device_id.'.device';
		file_put_contents($config_file, $value);
		chmod($config_file, CHMODMASK);

		$this->config_has_changed = true;

		$this->log('config', $device_id, $value, $info);
	}

	function config_replace_expressions($in) {
		$what = array('%SOCKETCONTROL%');
		$with = array($this->conf['config']['ESP-Socket-Control']);
		return str_replace($what, $with, $in);
	}

	function name_update($id, $new_name) {
		$this->config['names'][$id] = $new_name;
		$this->config_has_changed = true;
	}

	function device_register($device_id, $reg_info, $protocol) {
		$items = explode('|', $reg_info);
		foreach ($items as $item) {
			$fields = explode(':', $item);
			switch ($fields[0]) {
				case 'REG':
					$fields[0] = 'name';
					if (empty($fields[1]))
						$fields[1] = $device_id;
					break;
				case 'wn':		$fields[0] = 'WiFi Name'; break;
				case 'ws':		$fields[0] = 'WiFi Signal'; break;
				case 'vc':		$fields[0] = 'Voltage'; break;
				case 'ov':		$fields[0] = 'ohoco-version'; break;
				case 'sv':		$fields[0] = 'sketch-version'; break;
				break;
			}
			if ($fields[1] != '')
				$this->config['devices'][$device_id][$fields[0]] = $fields[1];
		}
		$this->config['devices'][$device_id]['registered'] = date('Y-m-d H:i:s');
		$this->config['devices'][$device_id]['connected'] = date('Y-m-d H:i:s');
		$this->config['devices'][$device_id]['last-ping'] = date('Y-m-d H:i:s');
		$this->config['devices'][$device_id]['protocol'] = $protocol;
		$this->config_has_changed = true;
		$this->log('config', $device_id, 'registered');
	}

	function device_unregister($device_id) {
		foreach ($this->config['switch'] as $switch_id => $switch) {
			if ($switch['device'] == $device_id) {
				if (array_key_exists($switch_id, $this->status[$switch_id])) {
					unset($this->status[$switch_id]);
					$this->status_has_changed = true;
				}
				$this->log('config', $device_id, $switch_id.' removed');
				unset($this->config['switch'][$switch_id]);
			}
		}
		$this->log('config', $device_id, 'unregistered');
		unset($this->config['devices'][$device_id]);
		unset($this->config['names'][$device_id]);
		$this->config_has_changed = true;
	}

	function device_alive($device_id, $alive_info) {
		$items = explode('|', $alive_info);
		foreach ($items as $item) {
			$fields = explode(':', $item);
			switch ($fields[0]) {
				case 'ws':		$fields[0] = 'WiFi Signal'; break;
				case 'vc':		$fields[0] = 'Voltage'; break;
			}
			if ($fields[1] != '')
				$this->config['devices'][$device_id][$fields[0]] = $fields[1];
		}
		$this->config['devices'][$device_id]['last-ping'] = date('Y-m-d H:i:s');
		$this->config_has_changed = true;
		$this->log('device', $device_id, 'alive');
	}

	function device_reconnected($device_id) {
		$this->config['devices'][$device_id]['connected'] = date('Y-m-d H:i:s');
		$this->config_has_changed = true;
		$this->log('device', $device_id, 'reconnect');
	}

	function device_send_control_command($device_id, $command) {
		switch ($this->config['devices'][$device_id]['protocol']) {
			case 'HTTP':
				$remote_ip = $this->config['devices'][$device_id]['ip'];
				$remote_port = 18266;

				if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
					socket_sendto($socket, $command, strlen($command), 0, $remote_ip, $remote_port);
					$this->log('config', $device_id, $command, $remote_ip.":".$remote_port);
				}
				else
					$this->log('config', $device_id, 'Cant create UDP socket');
				break; 

			case 'MQTT':
				include_once('class.mqtt.php');
				$topic = 'ohoco/callback/'.$device_id;
				$MQTT = new phpMQTT(MQTT_BROKER_ADDR, MQTT_BROKER_PORT, 'MqttPubRelay');
				if ($MQTT->connect(true, NULL, MQTT_USERNAME, MQTT_PASSWORD)) {
					$MQTT->publish($topic, $command, 1, false);
					$MQTT->close();
					$this->log('config', $device_id, $command, $topic);
				}
				else {
				    $this->log('config', $device_id, 'MQTT timeout');
				}
				break;

			default:
				die('ERR: UNKNOWN PROTOCOL');
		}
	}

	function device_config_save($device_id, $cfg_info) {
		$file = ABSPATH.'/config/'.$device_id.'.cfg';
		file_put_contents($file, $cfg_info);
		chmod($file, CHMODMASK);

		$this->log('config', $device_id, 'CFG received');
	}

	function sensor_register($sensor_id, $sensor_type) {
		$this->config['types'][$sensor_id] = $sensor_type;
		if (!array_key_exists($sensor_id, $this->config['names']))
			$this->config['names'][$sensor_id] = $sensor_id;
		$this->config_has_changed = true;
	}

	function sensor_unregister($sensor_id) {
		unset($this->config['types'][$sensor_id]);
		unset($this->config['names'][$sensor_id]);
		$this->config_has_changed = true;
		unset($this->status[$sensor_id]);
		$this->status_has_changed = true;
	}

	function sensor_set_name($sensor_id, $sensor_name) {
		if (empty($sensor_name))
			$sensor_name = $sensor_id;
		$this->config['names'][$sensor_id] = $sensor_name;
		$this->config_has_changed = true;
	}

	function switch_register($switch_id, $switch_data, $switch_protocol, $on_url = '', $off_url = '') {
		$switch_items = explode(';', $switch_data);
		$switch_device = $switch_items[0];
		$switch_type = $switch_items[1];
		$this->config['types'][$switch_id] = $switch_type;
		$this->config['switch'][$switch_id]['device'] = $switch_device;
		$this->config['switch'][$switch_id]['protocol'] = $switch_protocol;
		$this->config['switch'][$switch_id]['on_url'] = $on_url;
		$this->config['switch'][$switch_id]['off_url'] = $off_url;
		if (!array_key_exists($switch_id, $this->config['names']))
			$this->config['names'][$switch_id] = $switch_id;
		$this->config_has_changed = true;

		$this->status[$switch_id]['status'] = 'off';
		$this->status[$switch_id]['change'] = time();
		$this->status_has_changed = true;
	}

	function switch_send_udp_command($switch_id, $command) {
		$device_id = $this->config['switch'][$switch_id]['device'];
		$remote_ip = $this->config['devices'][$device_id]['ip'];
		$remote_port = 18266;

		if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
			socket_sendto($socket, $command, strlen($command), 0, $remote_ip, $remote_port);
			usleep(1000000);
			$this->status_read_from_file();
			return $this->status[$switch_id]['status'];
		}
		else
			$this->log('device', $this->config['names'][$switch_id], 'Cant create UDP socket');
		return 'error';
	}

// 	function switch_send_http_command($switch_id, $command) {
// 		$device_id = $this->config['switch'][$switch_id]['device'];
// 		$remote_ip = $this->config['devices'][$device_id]['ip'];
// 		if (!empty($remote_ip))
// 			$url = 'http://'.$device_ip.'/switch?id='.$switch_id.'&do='.$new_status;
// 		else
// 			$url = $this->config['switch'][$switch_id][$new_status.'_url'];
// 
// 		$response = '';
// 		$urlarr = parse_url($url);
// 		if (!isset($urlarr['port']))
// 			$urlarr['port'] = 80;
// 		$url = $urlarr['scheme'].'://'.$urlarr['host'].$urlarr['path'];
// 		if (!empty($urlarr['query']))
// 			$url .= '?'.$urlarr['query'];
// 		if (!empty($urlarr['user'])) {
// 			$context = stream_context_create(array(
// 			    'http' => array('header'  => "Authorization: Basic ".base64_encode($urlarr['user'].':'.$urlarr['pass']))
// 			));
// 			$response = file_get_contents($url, false, $context);
// 		}
// 		else
// 			$response = file_get_contents($url, false);
// 
// 		if ($response === false) {
// 			$this->log('error', $this->config['names'][$switch_id].' : switch_change_http failed', 'error');
// 			echo 'error';
// 		}
// 		else {
// 			$this->status_set_value($switch_id, $new_status);
// 			echo $new_status;
// 		}
// 	}

	function switch_raw_command($switch_id, $command) {
		$switch_protocol = $this->config['switch'][$switch_id]['protocol'];

		if ($switch_protocol == 'UDP') {
			echo $this->switch_send_udp_command($switch_id, $command);
		}
// 		elseif ($switch_protocol == 'HTTP') {
// 			echo $this->switch_send_http_command($switch_id, $command);
// 		}
		else
			$this->log('error', 'Unknown switch protocol', $switch_protocol);
	}

	function switch_turn_on($switch_id) {
		$switch_protocol = $this->config['switch'][$switch_id]['protocol'];

		if ($switch_protocol == 'UDP') {
			echo $this->switch_send_udp_command($switch_id, $switch_id.':on');
// 			$this->log('status', $switch_id, 'on'); //, 'udp');
		}
		elseif ($switch_protocol == 'HTTP') {
			echo $this->switch_change_http($switch_id, 'on');
// 			$this->log('status', $switch_id, 'on'); //, 'http');
		}
		else
			$this->log('error', 'Unknown switch protocol', $switch_protocol);
	}

	function switch_turn_off($switch_id) {
		$switch_protocol = $this->config['switch'][$switch_id]['protocol'];

		if ($switch_protocol == 'UDP') {
			echo $this->switch_send_udp_command($switch_id, $switch_id.':off');
// 			$this->log('status', $switch_id, 'off'); //, 'udp');
		}
		elseif ($switch_protocol == 'HTTP') {
			echo $this->switch_change_http($switch_id, 'off');
// 			$this->log('status', $switch_id, 'off'); //, 'http');
		}
		else
			$this->log('error', 'Unknown switch protocol', $switch_protocol);
	}

// 	function switch_change_udp($switch_id, $new_status) {
// 		$device_id = $this->config['switch'][$switch_id]['device'];
// 		$remote_ip = $this->config['devices'][$device_id]['ip'];
// 		$remote_port = 18266;
// 		$command = $switch_id.':'.$new_status;
// 
// 		if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
// 			socket_sendto($socket, $command, strlen($command), 0, $remote_ip, $remote_port);
// 			usleep(1000000);
// 			$this->status_read_from_file();
// 			return $this->status[$switch_id]['status'];
// 		}
// 		else
// 			$this->log('device', $this->config['names'][$switch_id], 'Cant create UDP socket');
// 		return 'error';
// 	}

	function switch_change_http($switch_id, $new_status) {
		$device_id = $this->config['switch'][$switch_id]['device'];
		$remote_ip = $this->config['devices'][$device_id]['ip'];
		if (!empty($remote_ip))
			$url = 'http://'.$device_ip.'/switch?id='.$switch_id.'&do='.$new_status;
		else
			$url = $this->config['switch'][$switch_id][$new_status.'_url'];

		$response = '';
		$urlarr = parse_url($url);
		if (!isset($urlarr['port']))
			$urlarr['port'] = 80;
		$url = $urlarr['scheme'].'://'.$urlarr['host'].$urlarr['path'];
		if (!empty($urlarr['query']))
			$url .= '?'.$urlarr['query'];
		if (!empty($urlarr['user'])) {
			$context = stream_context_create(array(
			    'http' => array('header'  => "Authorization: Basic ".base64_encode($urlarr['user'].':'.$urlarr['pass']))
			));
			$response = file_get_contents($url, false, $context);
		}
		else
			$response = file_get_contents($url, false);

// 		$headers  = "POST ".$urlarr['path']." HTTP/1.0\r\n";
// 		if (!empty($urlarr['user']))
// 			$headers .= "Authorization: Basic ".base64_encode($urlarr['user'].':'.$urlarr['pass'])."\r\n";
// 		$headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
// 		$headers .= "Content-Length: ".strlen($urlarr['query'])."\r\n\r\n";
// 
// 		$fp = fsockopen($urlarr['host'], $urlarr['port'], $errno, $errstr, 10);
// 		if (!$fp) {
// 		    $response = "[".$errno."] ".$errstr;
// 		} else {
// 			fputs($fp, $headers); 
// 			fputs($fp, $urlarr['query']); 
// 			$time_start = microtime(true);
// 		    while (!feof($fp)) {
// 		        $response = fgets($fp, 1024);
// 		        if ((microtime(true) - $time_start) > 500)
// 		        	break;
// 		    }
// 			fclose($fp);
// 		}

// 		$fp = fsockopen($urlarr['host'], 80, $errno, $errstr, 3);
// 		if (!$fp) {
// 		    $response = "[".$errno."] ".$errstr;
// 		} else {
// 		    //$out  = "GET ".$urlarr['path']."?".$urlarr['query']." HTTP/1.1\r\n";
// 		    $out  = "POST ".$urlarr['path']." HTTP/1.1\r\n";
// 		    $out .= "Host: ".$urlarr['host']."\r\n";
// 		    if (!empty($urlarr['user']))
// 		    	$out .= "Authorization: Basic ".base64_encode($urlarr['user'].':'.$urlarr['pass'])."\r\n";
// 		    $out .= "Connection: Close\r\n\r\n";
// 		    fwrite($fp, $out);
// 		    $time_start = microtime(true);
// 		    while (!feof($fp)) {
// 		        $response = fgets($fp, 128);
// 		        if ((microtime(true) - $time_start) > 500)
// 		        	break;
// 		    }
// 		    fclose($fp);
// 		}

// 		debugarr($urlarr);
// 		echo "RESPONSE: [",$response,"]"; exit;

		if ($response === false) {
			$this->log('error', $this->config['names'][$switch_id].' : switch_change_http failed', 'error');
			echo 'error';
		}
		else {
			$this->status_set_value($switch_id, $new_status);
			echo $new_status;
		}
	}

	function status_read_from_file() {
		$file = ABSPATH.'/config/status.ser';
		if (file_exists($file)) {
			$serialized_data = file_get_contents($file);
			$this->status = unserialize($serialized_data);
		}
		// preload fixed sensors
		$this->config['types']['time'] = 'time';
		$this->status['time']['status'] = date('H:i');

		$this->config['types']['day'] = 'date';
		$this->status['day']['status'] = date('D');

		$this->config['types']['weekend'] = 'switch';
		if ((date('N') > 5) && (date('N') < 8))
			$this->status['weekend']['status'] = 'on';
		else
			$this->status['weekend']['status'] = 'off';
	}

	function status_write_to_file() {
		$file = ABSPATH.'/config/status.ser';
		$serialized_data = serialize($this->status);
		file_put_contents($file, $serialized_data);
		chmod($file, CHMODMASK);
	}

	function status_set_value($device_id, $value, $log = false) {
// 		if ($value != $this->status[$device_id]['status']) {
			$this->status[$device_id]['status'] = $value;
			$this->status[$device_id]['change'] = time();
			$this->status_has_changed = true;
			if ($log)
				$this->log('status', $device_id, $value);
// 		}
// 		else {
// 			if ($log)
// 				$this->log('status', $device_id, 'nochg');
// 		}
	}

	function status_get_value($device_id) {
		return $this->status[$device_id]['status'];
	}

	function notify_via_telegram($message) {
		include_once('class.telegram.php');
$xxx=0;
		$this->log('notify', 'Telegram Message', 'sent');
	}

	function trigger_read() {
		$config_file = ABSPATH.'/config/trigger.conf';
		if (file_exists($config_file)) {
			$lines = file($config_file);
			foreach ($lines as $line) {
				$fields = explode('->', trim($line));
				if (count($fields) > 2) {
					$this->trigger[$fields[0]][] = $fields[1].'/'.$fields[2];
				}
			}
		}
	}

	function trigger_write() {
		$config_file = ABSPATH.'/config/trigger.conf';
		$file_data = '';
		foreach ($this->trigger as $trigger_name => $trigger_items) {
			foreach ($trigger_items as $trigger_line) {
				$file_data .= $trigger_name."->".$trigger_line."\r\n";
			}
		}
		echo nl2br($file_data);
		file_put_contents($config_file, $file_data);
		chmod($config_file, CHMODMASK);
	}

	function trigger_activate($trigger_id) {
		$this->log('trigger', 'Trigger '.$trigger_id, 'activated');
		if (array_key_exists($trigger_id, $this->trigger)) {
			foreach ($this->trigger[$trigger_id] as $p => $action) {
				$fields = explode('/', $action);
				switch ($fields[0]) {
					case 'switch':
						if ($fields[2] == 'on')
							$this->switch_turn_on($fields[1]);
						else
							$this->switch_turn_off($fields[1]);
						break;
					case 'notify':
						$message = trim($fields[2]);
						preg_match_all("/%(.*?)%/", $message, $matches);
						if (is_array($matches[0]) && (count($matches[0]) > 0)) {
							foreach ($matches[0] as $m => $match) {
								if (array_key_exists($matches[1][$m], $this->status))
									$replacement = time_diff(time() - $this->status[$matches[1][$m]]['change'], false);
								else
									$replacement = '';
								$message = str_replace($match, $replacement, $message);
							}
						}
						preg_match_all("/@(.*?)@/", $message, $matches);
						if (is_array($matches[0]) && (count($matches[0]) > 0)) {
							foreach ($matches[0] as $m => $match) {
								if (array_key_exists($matches[1][$m], $this->status))
									$replacement = $this->status[$matches[1][$m]]['status'];
								else
									$replacement = '';
								$message = str_replace($match, $replacement, $message);
							}
						}

						switch ($fields[1]) {
							case 'telegram':
								$this->notify_via_telegram($message);
								break;
						}
						break;
					case 'cmd':
						$switch_id = $fields[1];
						$command = trim($fields[2]);
						$this->switch_raw_command($switch_id, $command);
						$this->log('device', $switch_id, $command);
						break;
				}
			}
		}

		// trigger -> update last activation
		$this->config['trigger'][$trigger_id] = time();
		$this->config_has_changed = true;
	}

	function trigger_unregister($trigger_id) {
		unset($this->config['trigger'][$trigger_id]);
		unset($this->trigger[$trigger_id]);
		$this->config_has_changed = true;
	}

	function run_external_sensors() {
		$files = @glob(ABSPATH.'/sensors/*.php');
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				include_once($file);
			}
		}
	}

	function cronjobs_read() {
		$this->cronjobs = array();
		$cronjobs_file = ABSPATH.'/config/cronjobs.conf';
		if (file_exists($cronjobs_file)) {
			$lines = file($cronjobs_file);
			if (is_array($lines) && (count($lines) > 0)) {
				foreach ($lines as $line) {
					$fields = explode('->', trim($line));

					$jobid = md5($fields[0].$fields[1].$fields[3]);

					$this->cronjobs[$jobid]['chk-sensor'] = $fields[0];
					$this->cronjobs[$jobid]['chk-value'] = $fields[1];
					$this->cronjobs[$jobid]['run-type'] = $fields[2];
					$this->cronjobs[$jobid]['run-node'] = $fields[3];
					$this->cronjobs[$jobid]['run-action'] = $fields[4];
				}
			}
		}
	}

	function cronjobs_update_item($jobid, $chksensor, $chkvalue, $runtype, $runnode, $runaction = '') {
		if (!array_key_exists($jobid, $this->cronjobs))
			$jobid = md5($chksensor.$chkvalue.$runnode);

		$this->cronjobs[$jobid]['chk-sensor'] = $chksensor;
		$this->cronjobs[$jobid]['chk-type'] = '==';
		$this->cronjobs[$jobid]['chk-value'] = $chkvalue;
		$this->cronjobs[$jobid]['run-type'] = $runtype;
		$this->cronjobs[$jobid]['run-node'] = $runnode;
		$this->cronjobs[$jobid]['run-action'] = $runaction;
	}

	function cronjobs_write() {
		$file_data = '';
		foreach ($this->cronjobs as $jobid => $job) {
			$file_data .= $job['chk-sensor']."->".$job['chk-value']."->".$job['run-type']."->".$job['run-node']."->".$job['run-action']."\r\n";
		}
		$cronjobs_file = ABSPATH.'/config/cronjobs.conf';
		file_put_contents($cronjobs_file, $file_data);
		chmod($cronjobs_file, CHMODMASK);
	}

	function cronjobs_execute() {
		$cronjobs_file = ABSPATH.'/config/cronjobs.conf';
		if (file_exists($cronjobs_file)) {
			$lines = file($cronjobs_file);
			if (is_array($lines) && (count($lines) > 0)) {
				foreach ($lines as $line) {
					$fields = explode('->', trim($line));
					if (count($fields) > 1) {
						switch ($fields[0]) {
							case 'time': // on time - every day
								if ($fields[1] == date('H:i')) {
									$this->log('cronjob', 'Cronjob (t) '.$fields[0].'='.$fields[1], 'executed');
									switch ($fields[2]) {
										case 'trigger':
											$this->trigger_activate(trim($fields[3]));
											break;
										case 'switch':
											if ($fields[4] == 'on')
												$this->switch_turn_on($fields[3]);
											else
												$this->switch_turn_off($fields[3]);
											break;
										default:
											$this->log('error', 'Cronjob '.$fields[0].'='.$fields[1], $fields[2]);
									}
									
								}
								break;

							case 'wtime': // on time - only mo-fr
								if (($fields[1] == date('H:i')) && (date('N') < 6)) {
									$this->log('cronjob', 'Cronjob (w) '.$fields[0].'='.$fields[1], 'executed');
									switch ($fields[2]) {
										case 'trigger':
											$this->trigger_activate(trim($fields[3]));
											break;
										case 'switch':
											if ($fields[4] == 'on')
												$this->switch_turn_on($fields[3]);
											else
												$this->switch_turn_off($fields[3]);
											break;
										default:
											$this->log('error', 'Cronjob '.$fields[0].'='.$fields[1], $fields[2]);
									}
									
								}
								break;

							case 'ftime': // on time - only sa-so
								if (($fields[1] == date('H:i')) && (date('N') > 5)) {
									$this->log('cronjob', 'Cronjob (f) '.$fields[0].'='.$fields[1], 'executed');
									switch ($fields[2]) {
										case 'trigger':
											$this->trigger_activate(trim($fields[3]));
											break;
										case 'switch':
											if ($fields[4] == 'on')
												$this->switch_turn_on($fields[3]);
											else
												$this->switch_turn_off($fields[3]);
											break;
										default:
											$this->log('error', 'Cronjob '.$fields[0].'='.$fields[1], $fields[2]);
									}
									
								}
								break;
						}
					}
				}
			}
// 			else
// 				echo __CLASS__."::".__FUNCTION__." - no cronjobs not found<br>\r\n";
		}
// 		else
// 			echo __CLASS__."::".__FUNCTION__." - ".$cronjobs_file." not found<br>\r\n";
	}

	function rules_read() {
		$config_file = ABSPATH.'/config/rules.ser';
		if (file_exists($config_file)) {
			$serialized_data = file_get_contents($config_file);
			$this->rules = unserialize($serialized_data);
		}
	}

	function rules_write() {
		asort($this->rules);
		$file = ABSPATH.'/config/rules.ser';
		$serialized_data = serialize($this->rules);
		file_put_contents($file, $serialized_data);
		chmod($file, CHMODMASK);
	}

	function rules_check_conditions() {
// file_put_contents('/var/www/html/rift3/rules.log', date('d.m.y H:i:s')." rules_check_conditions\r\n", FILE_APPEND);
		if (count($this->rules) > 0) {
			$this->client = 'rule';
			foreach ($this->rules as $rid => $rule) {
				if ($rule['active'] == 1) {
					$cond2check = count($rule['conditions']);
					$cond2run = 0;

					if ($cond2check > 0) {
						foreach ($rule['conditions'] as $cid => $condition) {
							$status_value = $this->status[$condition['status']]['status'];
							$check_value = $condition['value'];
							$status_value_float = floatval(preg_replace('/[^0-9.,]+/', '', $status_value));
							$check_value_float  = floatval(preg_replace('/[^0-9.,]+/', '', $check_value));
							echo "[[ ",$condition['status']," :: ",$status_value," ",$condition['type']," ",$check_value," :: ",$cond2run,"&rarr;";
// 							echo "<br>((",$status_value_float,"/",$check_value_float,"))";
							switch ($condition['type']) {
								case 'EQU': if ($status_value == $check_value) { $cond2run++; } break;
								case 'NEQ': if ($status_value != $check_value) { $cond2run++; } break;
								case 'LSS': if ($status_value_float < $check_value_float) { $cond2run++; } break;
								case 'LEQ': if ($status_value_float <= $check_value_float) { $cond2run++; } break;
								case 'GTR': if ($status_value_float > $check_value_float) { $cond2run++; } break;
								case 'GEQ': if ($status_value_float >= $check_value_float) { $cond2run++; } break;
								default: echo "ERROR in RULE/COND/TYPE";
							}
							echo $cond2run," ]]<br>";
						}
					}
					echo "\$cond2check: ",$cond2check," / \$cond2run: ",$cond2run,"<br>";
					echo "<hr>";
					
					if ($cond2run >= $cond2check) {
						$this->log('cronjob', 'Rule '.$rule['name'], 'executed');
						
						if (!empty($rule['action']['trigger']['id']) && ($rule['action']['trigger']['id'] != '-')) {
							$this->trigger_activate($rule['action']['trigger']['id']);
							echo "{{FIRE TRIGGER :: ",$rule['action']['trigger']['id'], " (",$rule['action']['trigger']['value'],")}}<br>";
						}
						if (!empty($rule['action']['status']['id']) && ($rule['action']['status']['id'] != '-')) {
							$this->status_set_value($rule['action']['status']['id'], $rule['action']['status']['value'], true);
							echo "{{SET ",$rule['action']['status']['id']," &rarr; ",$rule['action']['status']['value'],"}}<br>";
						}
					}
					echo "<hr>";
				}
			}
		}
	}
}
?>
