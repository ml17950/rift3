<?php
class clsRIFT3 {
	public $switches;
	
	var $sensor_status;
	var $sensor_types;
	var $sensor_names;
	var $sensor_changed;
	var $devices;
	var $status;
	var $receipes;
	var $widgets;
	
	function __construct() {
// 		echo __CLASS__.'::'.__FUNCTION__.'<br>';
		
		// initialize
		
		define('STATUSDATA', ABSPATH.'/data/status/');
		
		if (!is_file(ABSPATH.'/data/last.status'))
			$this->init_directories();
		
		$this->sensor_status = array();
		$this->sensor_types = array();
		$this->sensor_names = array();
		$this->sensor_changed = array();
		$this->devices = array();
		$this->sensor_status = array();
		$this->receipes = array();
		$this->widgets = array();
		
		$this->widgets = array('ESP-LIGHT','IFTTT-Weather','ESP-TEMP','datetime','Tower','ESP-ROBBY','Dose6');
	}
	
// 	function __destruct() {
// 		echo __CLASS__.'::'.__FUNCTION__.'<br>';
// 	}
	
	function initialize() {
	}
	
	function debug() {
		echo "<hr><hr>";
// 		echo ABSPATH,"<br>";
// 		echo STATUSDATA,"<br>";
// 		echo "<hr>";
		
		echo "sensors<br>";
		debugarr($this->sensors);
		echo "<hr>";
		
		echo "devices<br>";
		debugarr($this->devices);
		echo "<hr>";
		
		echo "sensor_status<br>";
		debugarr($this->sensor_status);
		echo "<hr>";
		
		echo "sensor_types<br>";
		debugarr($this->sensor_types);
		echo "<hr>";
		
		echo "sensor_changed<br>";
		debugarr($this->sensor_changed);
		echo "<hr>";		
		
// 		echo "receipes<br>";
// 		debugarr($this->receipes);
// 		echo "<hr>";		
	}
	
	function init_directories() {
		if (!is_dir('data')) {
			mkdir('data', 0775);
			chmod('data', 0775);
		}
		if (!is_dir('data/receipes')) {
			mkdir('data/receipes', 0775);
			chmod('data/receipes', 0775);
		}
		if (!is_dir('data/status')) {
			mkdir('data/status', 0775);
			chmod('data/status', 0775);
		}
		if (!is_dir('data/devices')) {
			mkdir('data/devices', 0775);
			chmod('data/devices', 0775);
		}
		if (!is_dir('data/types')) {
			mkdir('data/types', 0775);
			chmod('data/types', 0775);
		}
		if (!is_dir('data/names')) {
			mkdir('data/names', 0775);
			chmod('data/names', 0775);
		}
		if (!is_dir('data/logs')) {
			mkdir('data/logs', 0775);
			chmod('data/logs', 0775);
		}
		
		file_put_contents(ABSPATH.'/data/rift3.log', date('d.m. H:i:s')."\t".CLIENT."\tcreated\r\n");
		chmod(ABSPATH.'/data/rift3.log', 0775);
		
		file_put_contents(ABSPATH.'/data/last.status', time());
		chmod(ABSPATH.'/data/last.status', 0775);
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
	
	function log($msg) {
		file_put_contents(ABSPATH.'/data/rift3.log', date('d.m. H:i:s')."\t".CLIENT."\t".$msg."\r\n", FILE_APPEND);
	}
	
	function log_resize($num_of_lines = 100) {
// TODO
		$file = ABSPATH.'/data/rift3.log';
		
		$lines = file($file); // reads the file into an array by line
		$flipped = array_reverse($lines); // reverse the order of the array
		$keep = array_slice($flipped, 0, $num_of_lines); // keep the first 50 elements of the array
		
		file_put_contents($file, implode("", $keep));
	}
	
	function sensor_updateall() {
		$sensors = array();
		$files = @glob(ABSPATH.'/sensors/*.php');
		
		if (is_array($files) && (count($files) > 0)) {
			$last_change = filemtime(ABSPATH.'/data/last.status');
			
			foreach ($files as $file) {
				include_once($file);
				$key = $sensor->sensor_name;
				$sensors[$key]['name'] = $key;
				$sensors[$key]['status'] = $sensor->read();
				$sensors[$key]['type'] = $sensor->sensor_class;
				
				$status_file = STATUSDATA.$sensor->sensor_name.'.status';
				if (is_file($status_file))
					$last_status_data = file_get_contents($status_file);
				else
					$last_status_data = '-';
				$current_status_data = $sensor->read();
				if ($current_status_data != $last_status_data) {
					file_put_contents($status_file, $current_status_data);
					$last_change = time();
				}
			}
			
			file_put_contents(ABSPATH.'/data/last.status', $last_change);
		}
		
// 		debugarr($sensors);
	}
	
	function sensor_save($id, $name, $type) {
// 		$new_id = $this->text2id($id);
		
		$file = ABSPATH.'/data/names/'.$id.'.name';
		file_put_contents($file, $name);
		
		$file = ABSPATH.'/data/types/'.$id.'.type';
		file_put_contents($file, $type);
		
		$file = ABSPATH.'/data/status/'.$id.'.status';
		if (!file_exists($file))
			file_put_contents($file, UNKNOWN);
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
		
		return true;
	}
	
	function device_readall() {
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
				
// 				$this->devices[$rkey]['stat'] = $this->load_action_status($rkey);
// 				$this->sensors['-'.$rkey]['name'] = $this->devices[$rkey]['name'];
// 				$this->sensors['-'.$rkey]['status'] = strtoupper($this->load_action_status($rkey));
// 				$this->sensors['-'.$rkey]['type'] = 'device';
// 				$this->sensor_types['-'.$rkey] = 'onoff';
			}
		}
		
		ksort($this->devices);
	}
	
	function device_save($id, $type, $name, $on_param, $off_param, $init_status = UNKNOWN) {
		$status_file = STATUSDATA.$id.'.status';
		$device_file = ABSPATH.'/data/devices/'.$id.'.ini';
		
		$data  = "; Device saved ".date('d.m.y H:i:s')."\r\n";
		$data .= "[device]\r\n";
		$data .= "type=".$type."\r\n";
		$data .= "name=".$name."\r\n";
		$data .= "on=".$on_param."\r\n";
		$data .= "off=".$off_param."\r\n";
		
		file_put_contents($device_file, $data);
		file_put_contents($status_file, $init_status);
	}
	
	function device_delete($id) {
		$status_file = STATUSDATA.$id.'.status';
		$device_file = ABSPATH.'/data/devices/'.$id.'.ini';
		
		if (is_file($status_file))
			unlink($status_file);
		if (is_file($device_file))
			unlink($device_file);
	}
	
	function status_readall() {
		$files = @glob(STATUSDATA.'*.status');
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				$current_status = file_get_contents($file);
				$key = substr(basename($file), 0, -7);
				
// 				echo $key," :: [",$current_status,"]<br>";
				
				$this->sensor_status[$key] = $current_status;
				$this->sensor_changed[$key] = filemtime($file);
				
				if (array_key_exists($key, $this->devices)) {
					$this->devices[$key]['now'] = $current_status;
					switch ($this->devices[$key]['type']) {
						case 'mailsender':
							$this->sensor_status[$key] = ON;
							break;
					}
				}
			}
			
			ksort($this->sensor_status);
		}
	}
	
	function status_save($id, $new_status, $type = '') {
		file_put_contents(STATUSDATA.$id.'.status', $new_status);
		if (!empty($type))
			file_put_contents(ABSPATH.'/data/types/'.$id.'.type', strtolower($type));
		file_put_contents(ABSPATH.'/data/last.status', time());
		
		$this->sensor_status[$id] = $new_status;
		$this->sensor_changed[$id] = time();
	}
	
	function status_save_and_log($id, $new_status, $type = '') {
		file_put_contents(STATUSDATA.$id.'.status', $new_status);
		if (!empty($type))
			file_put_contents(ABSPATH.'/data/types/'.$id.'.type', strtolower($type));
		file_put_contents(ABSPATH.'/data/logs/'.$id.'.log', date('d.m.y H:i:s')."\t".$new_status."\r\n", FILE_APPEND);
		file_put_contents(ABSPATH.'/data/last.status', time());
		
		$this->sensor_status[$id] = $new_status;
		$this->sensor_changed[$id] = time();
	}
	
	function status_read($id) {
		$status_file = STATUSDATA.$id.'.status';
		if (is_file($status_file))
			return file_get_contents($status_file);
		else
			return UNKNOWN;
	}
	
	function types_readall() {
		$files = @glob(ABSPATH.'/data/types/*.type');
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				$current_type = file_get_contents($file);
				$key = substr(basename($file), 0, -5);
				
				//echo $key," :: [",$current_type,"]<br>";
				
				$this->sensor_types[$key] = $current_type;
				
				if (array_key_exists($key, $this->devices)) {
					$this->devices[$key]['type'] = $current_type;
					$this->sensor_names[$key] = $this->devices[$key]['name'];
				}
				else {
					$name_file = ABSPATH.'/data/names/'.$key.'.name';
					if (file_exists($name_file))
						$this->sensor_names[$key] = file_get_contents($name_file);
				}
			}
		}
	}
	
	function action_readall() {
		$files = @glob(ABSPATH.'/actions/*.php');
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				include_once($file);
			}
		}
	}
	
	function action_run($device_id, $action, $receipe_name = '') {
		if (isset($this->devices[$device_id]['type']))
			$device_type = $this->devices[$device_id]['type'];
		else
			$device_type = 'virtual_device';
		if (isset($this->devices[$device_id]['name']))
			$device_name = $this->devices[$device_id]['name'];
		else
			$device_name = $device_id;
		if (isset($this->devices[$device_id]['on']))
			$device_on_param = $this->devices[$device_id]['on'];
		else
			$device_on_param = ON;
		if (isset($this->devices[$device_id]['off']))
			$device_off_param = $this->devices[$device_id]['off'];
		else
			$device_off_param = OFF;
		
// 		echo "receipe_name: ",$receipe_name,"<br>";
// 		echo "device_id: ",$device_id,"<br>";
// 		echo "action: ",$action,"<br>";
// 		echo "devicename: ",$device_name,"<br>";
// 		echo "device_type: ",$device_type,"<br>";
// 		echo "on_param: ",$device_on_param,"<br>";
// 		echo "off_param: ",$device_off_param,"<br>";
// 		echo "<hr>";
		
		switch ($device_type) {
			case 'mailsender':
				$this->log($receipe_name."\t".$device_name."\tOK");
				break;
			
			default:
				$this->log($receipe_name."\t".$device_name."\t".$action);
		}
		
		if (function_exists($device_type)) {
			call_user_func($device_type, $device_on_param, $device_off_param, $action);
			$this->status_save($device_id, $action);
		}
		else
			echo "function [",$device_type,"] not found <br>\r\n";
	}
	
	function receipe_readall() {
		$files = @glob(ABSPATH.'/data/receipes/*.ini');
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				$lines = file($file);
				$rkey = str_replace('.ini', '', basename($file));
				
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
		}
	}
	
	function receipe_execute($debug = false) {
		$execute_counter = 0;
		
// debugarr($this->receipes);
		
		foreach ($this->receipes as $rkey => $receipe) {
// 			debugarr($receipe);
			$num_trigger = count($receipe['trigger']);
			
			if ($num_trigger > 0) {
				$run_trigger = 0;
				
// echo "<hr>Rezept: ",$rkey,"<br>";
				foreach ($receipe['trigger'] as $hash => $trigger) {
					$sensorname = $trigger['id'];
					$checkvalue = $trigger['chk'];
					
// echo "- ",$sensorname,":",$checkvalue,"=",$this->sensor_status[$sensorname],"<br>";
					
					if (strpos($checkvalue, ':') !== false) {
						$chkfunc = explode(':', $checkvalue);
// 						debugarr($chkfunc);
						switch ($chkfunc[0]) {
							case 'NEWER':
								$last_status_change = filemtime(STATUSDATA.$sensorname.'.status');
								//echo "NEWER: ",time()," - ",$last_status_change," = ",(time() - $last_status_change)," :: ",$chkfunc[1],"<br>";
								if ((time() - $last_status_change) <= $chkfunc[1])
									$run_trigger++;
								break;
							
							case 'OLDER':
								$last_status_change = filemtime(STATUSDATA.$sensorname.'.status');
								//echo "OLDER: ",time()," - ",$last_status_change," = ",(time() - $last_status_change)," :: ",$chkfunc[1],"<br>";
								if ((time() - $last_status_change) >= $chkfunc[1])
									$run_trigger++;
								break;
						}
					}
					else {
						if ($this->sensor_status[$sensorname] == $checkvalue)
							$run_trigger++;
					}
				}
// echo $run_trigger," von ",$num_trigger,"<br>";
				
				if ($run_trigger == $num_trigger) {
// echo "execute ",$rkey,"<hr>";
					foreach ($receipe['actions'] as $actionname => $actionparam) {
						if ($debug == true)
							echo "run action ",$actionname," :: ",$actionparam," (",$rkey,")<br>";
						else
							$this->action_run($actionname, $actionparam, $rkey);
					}
					$execute_counter++;
				}
				else {
					if ($debug == true)
						echo "not all trigger matching (",$run_trigger,"/",$num_trigger,")<br>";
				}
			}
		}
		
		echo $execute_counter," receipes executed";
	}
	
	function ajax_get_last_change() {
// 		return time();
		return filemtime(ABSPATH.'/data/last.status');
	}
	
	function ajax_get_last_data_as_json() {
		$jsonArray['widgets'] = array();
		$jsonArray['sensors'] = array();
		
		if (count($this->widgets) > 0) {
			foreach ($this->widgets as $i => $widget_key) {
				if (array_key_exists($widget_key, $this->sensor_status)) {
					$jsonArray['widgets'][$widget_key]['t'] = 'UNKNOWN';
					$jsonArray['widgets'][$widget_key]['v'] = $this->sensor_status[$widget_key];
					$jsonArray['widgets'][$widget_key]['n'] = $widget_key;
				}
				
				if (array_key_exists($widget_key, $this->sensor_types))
					$jsonArray['widgets'][$widget_key]['t'] = $this->sensor_types[$widget_key];
				
				if (array_key_exists($widget_key, $this->sensor_names))
					$jsonArray['widgets'][$widget_key]['n'] = $this->sensor_names[$widget_key];
				
				switch ($jsonArray['widgets'][$widget_key]['t']) {
					case 'weather':
					//case 'daynight':
						$jsonArray['widgets'][$widget_key]['n'] = $jsonArray['widgets'][$widget_key]['v'];
						break;
					
					case 'time':
						$jsonArray['widgets'][$widget_key]['n'] = $jsonArray['widgets'][$widget_key]['v'];
						$jsonArray['widgets'][$widget_key]['v'] = 'ALL';
						break;
					
					case 'temp':
						$jsonArray['widgets'][$widget_key]['n'] = $jsonArray['widgets'][$widget_key]['v'].' °C';
						$jsonArray['widgets'][$widget_key]['v'] = 'ALL';
						break;
				}
			}
		}
		
		foreach ($this->sensor_status as $key => $sensor_data) {
			if (array_key_exists($key, $this->sensor_types))
				$jsonArray['sensors'][$key]['t'] = $this->sensor_types[$key];
			else
				$jsonArray['sensors'][$key]['t'] = 'unknown';
			if (array_key_exists($key, $this->devices))
				$jsonArray['sensors'][$key]['n'] = $this->devices[$key]['name'];
			else
				$jsonArray['sensors'][$key]['n'] = $key;
			$jsonArray['sensors'][$key]['v'] = $sensor_data;
			//$jsonArray['sensors'][$key]['c'] = date('d.m.', $this->sensor_changed[$key])."<br>".date('H:i', $this->sensor_changed[$key]);
			$jsonArray['sensors'][$key]['c'] = date('d.m.', $this->sensor_changed[$key])." ".date('H:i', $this->sensor_changed[$key]);
		}
		
		asort($jsonArray['sensors']);
		
// 		debugarr($jsonArray);
		
		return json_encode($jsonArray);
	}
	
	function ajax_get_log_as_json() {
		$jsonArray['log'] = array();
		
		$lines = file(ABSPATH.'/data/rift3.log');
		krsort($lines);
// 		debugarr($lines);
		
		$cnt = 0;
		foreach ($lines as $line) {
			$fields = explode("\t", trim($line));
			
			$jsonArray['log'][$cnt]['d'] = $fields[0];
			$jsonArray['log'][$cnt]['c'] = $fields[1];
			$jsonArray['log'][$cnt]['t'] = $fields[2];
			$jsonArray['log'][$cnt]['a'] = $fields[3];
			$jsonArray['log'][$cnt]['v'] = $fields[4];
			$cnt++;
		}
		
// 		debugarr($jsonArray);
		
		return json_encode($jsonArray);
	}
	
	function ajax_get_sensorlog_as_json() {
		$jsonArray['logs'] = array();
		$files = @glob(ABSPATH.'/data/logs/*.log');
		$max_entries = 288;
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
// 				echo $file,"<hr>";
				$key = substr(basename($file), 0, -4);
// 				echo $key,"<hr>";
				$lines = file($file);
				rsort($lines);
				
				$tmparr = array();
				$idx = 0;
				$first = '';
				$last = '';
				$min = 999999;
				$max = -999999;
				
				foreach ($lines as $line) {
					$fields = explode("\t", trim($line));
					
					if ($last == '')
						$last = $fields[0];
					$first = $fields[0];
					
					if ($fields[1] < $min)
						$min = $fields[1];
					if ($fields[1] > $max)
						$max = $fields[1];
					
					$tmparr[$idx] = $fields[1];
					$idx++;
					
					if ($idx >= $max_entries)
						break;
				}
				
				if (count($tmparr) < $max_entries) {
					$start = count($tmparr);
					for ($idx=$start; $idx<$max_entries; $idx++) {
						$tmparr[$idx] = 0;
					}
					$min = 0;
				}
				
				krsort($tmparr);
				
// 				echo "first: ",$first,"<br>";
// 				echo "last: ",$last,"<br>";
// 				echo "min: ",$min,"<br>";
// 				echo "max: ",$max,"<br>";
// 				echo "<hr>";
				
// 				debugarr($lines);
// 				debugarr($tmparr);
				
				$jsonArray['logs'][$key]['first'] = $first;
				$jsonArray['logs'][$key]['last'] = $last;
				$jsonArray['logs'][$key]['min'] = $min;
				$jsonArray['logs'][$key]['max'] = $max;
				$jsonArray['logs'][$key]['values'] = $tmparr;
			}
		}
		
// 		debugarr($jsonArray);
		
		return json_encode($jsonArray);
	}
}
?>