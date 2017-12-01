<?php
// last change: 2017-12-01
class clsRIFT3 {
	
	var $sensors;
	var $receipes;
	var $actions;
	var $notifier;
	var $widgets;
	var $last_status_change;
	
	function __construct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		
		
		$this->sensors = array();
		$this->receipes = array();
		$this->actions = array();
		$this->notifier = array();
		$this->widgets = array();
		
		$this->last_status_change = 0;
	}
	
	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}
	
	function dummy() {
		echo "<hr>";
		echo __CLASS__.'::'.__FUNCTION__.'<br>';
		echo "<hr>";
	}

	function generate_guid() {
		$crc = crc32(time());
		if ($crc & 0x80000000) {
			$crc ^= 0xffffffff;
			$crc += 1;
			$crc = -$crc;
		}
		return str_pad(str_replace('ffffffff', '', dechex($crc)), 10, '0', STR_PAD_LEFT);
	}

	function text2id($text) {
		$what = array('ä','ö','ü','ß',' ');
		$with = array('ae','oe','ue','ss','-');
		$text = str_replace($what, $with, strtolower($text));
		return $text;
	}

	function log_old($msg) {
		file_put_contents(ABSPATH.'/data/rift3.log', date('d.m.y H:i:s')."\t".CLIENT."\t".$msg."\r\n", FILE_APPEND);
	}

	function log($device, $value, $trigger = '') {
		file_put_contents(ABSPATH.'/data/rift3.log', date('d.m.y H:i:s')."\t".CLIENT."\t".$trigger."\t".$device."\t".$value."\r\n", FILE_APPEND);
	}

	function log_resize($num_of_lines = 100) {
		$file = ABSPATH.'/data/rift3.log';
		$lines = file($file); // reads the file into an array by line
		$keep = array_slice($lines, ($num_of_lines * -1), $num_of_lines); // keep the last n elements of the array
		file_put_contents($file, implode("", $keep)); // combine array and write it back to file
	}
	
	function log_read() {
		$lines = file(ABSPATH.'/data/rift3.log');
		krsort($lines);
// 		debugarr($lines);
		
		$arr = array();
		$cnt = 0;
		foreach ($lines as $line) {
			if (!empty($line)) {
				$fields = explode("\t", trim($line));
				
				$arr[$cnt]['d'] = $fields[0];
				$arr[$cnt]['c'] = $fields[1];
				$arr[$cnt]['t'] = $fields[2];
				$arr[$cnt]['a'] = $fields[3];
				$arr[$cnt]['v'] = $fields[4];
				$cnt++;
			}
		}
		
		return $arr;
	}
	
	function action_initialize() {
		$files = @glob(ABSPATH.'/actions/*.php');
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				include_once($file);
			}
		}
	}
	
	function action_register($name, $function_name, $param_type) {
		$this->actions[$name]['name'] = $name;
		$this->actions[$name]['func'] = $function_name;
		$this->actions[$name]['type'] = $param_type;
	}
	
	function notifier_initialize() {
		$files = @glob(ABSPATH.'/notifier/*.php');
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				include_once($file);
			}
		}
	}
	
	function notifier_register($name, $function_name, $param_type) {
		$this->notifier[$name]['name'] = $name;
		$this->notifier[$name]['func'] = $function_name;
		$this->notifier[$name]['type'] = $param_type;
	}
	
	function sensor_initialize() {
		$files = @glob(ABSPATH.'/data/status/*.status');
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				$key = substr(basename($file), 0, -7);
				$name_file = ABSPATH.'/data/names/'.$key.'.name';
				$type_file = ABSPATH.'/data/types/'.$key.'.type';
				
				if (file_exists($name_file))
					$this->sensors[$key]['name'] = file_get_contents($name_file);
				else
					$this->sensors[$key]['name'] = $key;
				if (file_exists($type_file))
					$this->sensors[$key]['type'] = file_get_contents($type_file);
				else
					$this->sensors[$key]['type'] = UNKNOWN;
				$this->sensors[$key]['optt'] = UNKNOWN;
				$this->sensors[$key]['value'] = file_get_contents($file);
				$this->sensors[$key]['changed'] = filemtime($file);
			}
			
			ksort($this->sensors);
		}
	}
	
	function sensor_updateall() {
		$sensors = array();
		$files = @glob(ABSPATH.'/sensors/*.php');
		$this->last_status_change = 0;
		
		if (is_array($files) && (count($files) > 0)) {
			$last_change = filemtime(ABSPATH.'/data/last.status');
			
			foreach ($files as $file) {
				include_once($file);
			}
			
			if ($this->last_status_change > 0)
				file_put_contents(ABSPATH.'/data/last.status', $this->last_status_change);
		}
	}
	
	function sensor_update($id, $current_status_data, $sensor_type, $options_type) {
		$status_file = ABSPATH.'/data/status/'.$id.'.status';
		$typeof_file = ABSPATH.'/data/types/'.$id.'.type';
		
		if (is_file($status_file))
			$last_status_data = file_get_contents($status_file);
		else
			$last_status_data = UNKNOWN;
		
		if (!is_file($typeof_file))
			file_put_contents($typeof_file, strtolower($sensor_type));
		
		$this->sensors[$id]['type'] = $sensor_type;
		$this->sensors[$id]['optt'] = $options_type;
		
// echo "sensor_update :: ",$id,": ",$last_status_data," / ",$current_status_data," [",$sensor_type,"] [",$options_type,"]<br>";
		
		if ($current_status_data != $last_status_data) {
			file_put_contents($status_file, $current_status_data);
			$this->sensors[$key]['value'] = $current_status_data;
			$this->sensors[$key]['changed'] = 'x'.time();
			$this->last_status_change = time();
		}
	}
	
	function sensor_rename($old_id, $new_id) {
		$old_file = ABSPATH.'/data/status/'.$old_id.'.status';
		$new_file = ABSPATH.'/data/status/'.$new_id.'.status';
		if (file_exists($new_file))
			return false;
		if (file_exists($old_file))
			rename($old_file, $new_file);

		$old_file = ABSPATH.'/data/types/'.$old_id.'.type';
		$new_file = ABSPATH.'/data/types/'.$new_id.'.type';
		if (file_exists($old_file))
			rename($old_file, $new_file);

		$old_file = ABSPATH.'/data/names/'.$old_id.'.name';
		$new_file = ABSPATH.'/data/names/'.$new_id.'.name';
		if (file_exists($old_file))
			rename($old_file, $new_file);

		$old_file = ABSPATH.'/data/logs/'.$old_id.'.log';
		$new_file = ABSPATH.'/data/logs/'.$new_id.'.log';
		if (file_exists($old_file))
			rename($old_file, $new_file);

		file_put_contents(ABSPATH.'/data/last.status', time());

		return true;
	}

	function sensor_getname($id) {
		$name_file = ABSPATH.'/data/names/'.$id.'.name';
		if (is_file($name_file))
			return file_get_contents($name_file);
		else
			return $id;
	}

	function status_read($id) {
		$status_file = ABSPATH.'/data/status/'.$id.'.status';
		if (is_file($status_file))
			return file_get_contents($status_file);
		else
			return UNKNOWN;
	}

	function status_save($id, $new_status, $type = '') {
		file_put_contents(ABSPATH.'/data/status/'.$id.'.status', $new_status);
		if (!empty($type)) {
			if (!file_exists(ABSPATH.'/data/types/'.$id.'.type'))
				file_put_contents(ABSPATH.'/data/types/'.$id.'.type', strtolower($type));
		}

		$this->sensors[$id]['value'] = $new_status;
		$this->sensors[$id]['changed'] = time();

		$this->last_status_change = time();
		file_put_contents(ABSPATH.'/data/last.status', time());
	}

	function status_save_and_log($id, $new_status, $type = '') {
		file_put_contents(ABSPATH.'/data/status/'.$id.'.status', $new_status);
		if (!empty($type)) {
			if (!file_exists(ABSPATH.'/data/types/'.$id.'.type'))
				file_put_contents(ABSPATH.'/data/types/'.$id.'.type', strtolower($type));
		}
		file_put_contents(ABSPATH.'/data/logs/'.$id.'.log', date('d.m.y H:i:s')."\t".$new_status."\r\n", FILE_APPEND);

		$this->sensors[$id]['value'] = $new_status;
		$this->sensors[$id]['changed'] = time();

		$this->last_status_change = time();
		file_put_contents(ABSPATH.'/data/last.status', time());
	}

	function device_initialize() {
		$files = @glob(ABSPATH.'/data/devices/*.ini');
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				$lines = file($file);
				$rkey = str_replace('.ini', '', basename($file));
				$section = '';
				
				foreach ($lines as $line) {
					switch (trim($line)) {
						case '[device]':
							$section = 'device';
							break;
						
						case '';
							break;
						
						default:
							if (substr($line, 0, 1) != ';') {
								$fields = explode('=', trim($line));
								$this->devices[$rkey][$fields[0]] = $fields[1];
							}
					}
				}
				
				$name_file = ABSPATH.'/data/names/'.$rkey.'.name';
				if (file_exists($name_file))
					$this->devices[$rkey]['name'] = file_get_contents($name_file);
			}
		}
		
		ksort($this->devices);
	}
	
	
	
	
	function receipe_initialize() {
		$files = @glob(ABSPATH.'/data/receipes/*.ini');
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				$lines = file($file);
				$rkey = str_replace('.ini', '', basename($file));
				
				$this->receipes[$rkey]['name'] = $rkey;
				$this->receipes[$rkey]['last_run'] = filemtime($file);
				$this->receipes[$rkey]['trigger_count'] = -1;
				$this->receipes[$rkey]['actions_count'] = -1;
				
				$section = '';
				
				foreach ($lines as $line) {
					switch (trim($line)) {
						case '[trigger]':
							$section = 'trigger';
							break;
						
						case '[actions]':
							$section = 'actions';
							break;
						
						case '';
							break;
						
						default:
							if (substr($line, 0, 1) != ';') {
								$fields = explode('=', trim($line));
								if ($section == 'trigger') {
									$tkey = md5($line);
									$this->receipes[$rkey][$section][$tkey]['id'] = $fields[0];
									$this->receipes[$rkey][$section][$tkey]['chk'] = $fields[1];
								}
								else {
									$this->receipes[$rkey][$section][$fields[0]] = $fields[1];
								}
							}
					}
				}
				
				$this->receipes[$rkey]['trigger_count'] = count($this->receipes[$rkey]['trigger']);
				$this->receipes[$rkey]['actions_count'] = count($this->receipes[$rkey]['actions']);
			}
			
			ksort($this->receipes);
		}
	}
	
	function receipe_save(&$post) {
		$file = ABSPATH.'/data/receipes/'.$post['id'].'.ini';

		if ($post['new_name'] != $post['id']) {
			unlink($file);
			$file = ABSPATH.'/data/receipes/'.$post['new_name'].'.ini';
		}

		$data  = "[trigger]\r\n";
		foreach ($post['trigger'] as $name => $value) {
			if (!empty($value))
				$data .= $name."=".$value."\r\n";
		}
		
		$data .= "\r\n";
		$data .= "[actions]\r\n";
		foreach ($post['actions'] as $name => $value) {
			if (!empty($value))
				$data .= $name."=".$value."\r\n";
		}
		
// 		debugarr($post);
// 		echo nl2br($data);
// 		echo "<hr>",$file,"<hr>";
		
		file_put_contents($file, $data);
	}

	function receipe_display_trigger(&$trigger_array) {
		foreach ($trigger_array as $hash => $trigger) {
			$sensor_id = $trigger['id'];
			$check_val = $trigger['chk'];

			if (strpos($checkvalue, ':') !== false) {
				echo "<div class='receipe-trigger-fits-not'>",$this->sensors[$sensor_id]['name']," = ",$check_val," TODO</div>";
			}
			else {
				if ($this->sensors[$sensor_id]['value'] == $check_val)
					echo "<div class='receipe-trigger-fits'>",$this->sensors[$sensor_id]['name']," = ",$check_val,"</div>";
				else
					echo "<div class='receipe-trigger-fits-not'>",$this->sensors[$sensor_id]['name']," = ",$check_val,"</div>";
			}
		}
	}

	function receipe_run_action($action_key, $action_param, $receipe_name = '') {
		$debug = false;

		if (isset($this->notifier[$action_key]))
			$action_type = 'NOTIFIER';
		elseif (isset($this->devices[$action_key]))
			$action_type = 'DEVICE';
		else
			$action_type = 'SENSOR';

		switch ($action_type) {
			case 'NOTIFIER':
				$call_func_name = $this->notifier[$action_key]['func'];

				if ($debug == true) {
					echo "receipe_name: ",$receipe_name,"<br>";
					echo "action_type: ",$action_type,"<br>";
					echo "action_key: ",$action_key,"<br>";
					echo "action_param: ",$action_param,"<br>";
					echo "call_func_name: ",$call_func_name,"<br>";
					echo "<hr>";
				}

				if (function_exists($call_func_name)) {
					call_user_func($call_func_name, $action_param);
					$this->log($action_key, "sent", $receipe_name);
				}
				else {
					if ($debug == true)
						echo "function [",$call_func_name,"] not found <br>\r\n";
				}
				break;

			case 'DEVICE':
				$call_func_name = $this->devices[$action_key]['type'];

				if (isset($this->devices[$action_key]['name']))
					$device_name = $this->devices[$action_key]['name'];
				else
					$device_name = $action_key;

				if (isset($this->devices[$action_key]['on']))
					$device_on_param = $this->devices[$action_key]['on'];
				else
					$device_on_param = ON;
				if (isset($this->devices[$action_key]['off']))
					$device_off_param = $this->devices[$action_key]['off'];
				else
					$device_off_param = OFF;

				if ($debug == true) {
					echo "receipe_name: ",$receipe_name,"<br>";
					echo "action_type: ",$action_type,"<br>";
					echo "action_key: ",$action_key,"<br>";
					echo "action_param: ",$action_param,"<br>";
					echo "device_name: ",$device_name,"<br>";
					echo "call_func_name: ",$call_func_name,"<br>";
					echo "on_param: ",$device_on_param,"<br>";
					echo "off_param: ",$device_off_param,"<br>";
					echo "<hr>";
				}

				if (function_exists($call_func_name)) {
					call_user_func($call_func_name, $device_on_param, $device_off_param, $action_param);
					$this->status_save($action_key, $action_param);
					$this->log($device_name, $action_param, $receipe_name);
				}
				else {
					if ($debug == true)
						echo "function [",$call_func_name,"] not nound <br>\r\n";
				}
				break;

			case 'SENSOR':
				if (isset($this->sensors[$action_key]))
					$device_name = $this->sensors[$action_key]['name'];
				else
					$device_name = $action_key;

				if ($debug == true) {
					echo "receipe_name ",$receipe_name,"<br>";
					echo "action_type: ",$action_type,"<br>";
					echo "action_key: ",$action_key,"<br>";
					echo "action_param: ",$action_param,"<br>";
					echo "devicename: ",$device_name,"<br>";
 					echo "device_type: ",$device_type,"<br>";
					echo "<hr>";
				}

				$this->status_save($action_key, $action_param);
				$this->log($device_name, $action_param, $receipe_name);
				break;
		}
	}

	function widgets_initialize() {
		$widgets_file = ABSPATH.'/data/widgets.ser';

		if (file_exists($widgets_file)) {
			$serialized_data = file_get_contents($widgets_file);
			$this->widgets = unserialize($serialized_data);
		}

		//$this->widgets_save();
	}
	
	function widgets_save() {
		$widgets_file = ABSPATH.'/data/widgets.ser';
		
		$arr = array('ESP-LIGHT','IFTTT-Weather','ESP-TEMP','Time','TV','PC','Bastelkiste','ESP-ROBBY');
		
		$serialized_data = serialize($arr);
		file_put_contents($widgets_file, $serialized_data);
	}
}
?>