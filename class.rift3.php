<?php
class clsRIFT3 {
	public $switches;
	
	var $sensors;
	var $sensor_types;
	var $sensor_changed;
	var $devices;
	var $status;
	var $receipes;
	
	function __construct() {
// 		echo __CLASS__.'::'.__FUNCTION__.'<br>';
		
		// initialize
		
		define('STATUSDATA', ABSPATH.'/data/status/');
		
		$this->sensors = array();
		$this->sensor_types = array();
		$this->sensor_changed = array();
		$this->devices = array();
		$this->status = array();
		$this->receipes = array();
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
		
// 		echo "sensors<br>";
// 		debugarr($this->sensors);
// 		echo "<hr>";
		
// 		echo "devices<br>";
// 		debugarr($this->devices);
// 		echo "<hr>";
// 		
		echo "devices<br>";
		debugarr($this->status);
		echo "<hr>";
		
		echo "types<br>";
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
		
		file_put_contents($file, implode("\n", $keep));
	}
	
	function sensor_updateall() {
		$sensors = array();
		$files = @glob(ABSPATH.'/sensors/*.php');
		
		if (is_array($files) && (count($files) > 0)) {
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
				if ($current_status_data != $last_status_data)
					file_put_contents($status_file, $current_status_data);
			}
		}
		
// 		debugarr($sensors);
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
				
				$this->status[$key] = $current_status;
				$this->sensor_changed[$key] = filemtime($file);
				
				if (array_key_exists($key, $this->devices))
					$this->devices[$key]['now'] = $current_status;
			}
		}
	}
	
	function status_save($id, $new_status, $type = '') {
		file_put_contents(STATUSDATA.$id.'.status', $new_status);
		if (!empty($type))
			file_put_contents(ABSPATH.'/data/types/'.$id.'.type', strtolower($type));
		file_put_contents(ABSPATH.'/data/last.status', time());
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
				
// 				echo $key," :: [",$current_type,"]<br>";
				
				$this->sensor_types[$key] = $current_type;
				
				if (array_key_exists($key, $this->devices))
					$this->devices[$key]['type'] = $current_type;
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
	
	function receipe_readall() {
		$files = @glob(ABSPATH.'/data/receipes/*.ini');
		
		if (is_array($files) && (count($files) > 0)) {
			foreach ($files as $file) {
				$lines = file($file);
				$rkey = str_replace('.ini', '', basename($file));
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
								$this->receipes[$rkey][$section][$fields[0]] = $fields[1];
							}
					}
				}
			}
		}
	}
	
	function receipe_execute($debug = false) {
		$execute_counter = 0;
		
		foreach ($this->receipes as $rkey => $receipe) {
			$num_trigger = count($receipe['trigger']);
// echo $num_trigger,":num_trigger<br>";
			if ($num_trigger > 0) {
				$run_trigger = 0;
				
// echo "<strong>",$rkey,"</strong><br>";
				foreach ($receipe['trigger'] as $sensorname => $checkvalue) {
// echo "- ",$sensorname,":",$checkvalue,"=",$this->status[$sensorname],"<br>";
					if ($this->status[$sensorname] == $checkvalue)
						$run_trigger++;
				}
				
				if ($run_trigger == $num_trigger) {
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
	
	function action_run($device_id, $action, $receipe_name = '') {
		$device_type = $this->devices[$device_id]['type'];
		$device_name = $this->devices[$device_id]['name'];
		$device_on_param = $this->devices[$device_id]['on'];
		$device_off_param = $this->devices[$device_id]['off'];
		
// 		echo "device_id: ",$device_id,"<br>";
// 		echo "receipe_name: ",$receipe_name,"<br>";
// 		echo "devicename: ",$device_name,"<br>";
// 		echo "device_type: ",$device_type,"<br>";
// 		echo "on_param: ",$device_on_param,"<br>";
// 		echo "off_param: ",$device_off_param,"<br>";
// 		echo "onoff: ",$action,"<br>";
// 		echo "<br>";
		
		$this->log($receipe_name."\t".$device_name."\t".$action);
		
		if (function_exists($device_type)) {
			call_user_func($device_type, $device_on_param, $device_off_param, $action);
			$this->status_save($device_id, $action);
		}
		else
			echo "function ",$this->devices[$device_id]['type']," not found <br>\r\n";
	}
	
	function ajax_get_last_change() {
		return time();
// 		return '1504012591';
	}
	
	function ajax_get_last_data_as_json() {
		
	}
}
?>