<?php
class clsRIFT3 {
	
	var $sensors;
	var $receipes;
	var $actions;
	var $notifier;
	var $last_status_change;
	
	function __construct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		
		
		$this->sensors = array();
		$this->receipes = array();
		$this->actions = array();
		$this->notifier = array();
		
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
	
	function log($msg) {
		file_put_contents(ABSPATH.'/data/rift3.log', date('d.m. H:i:s')."\t".CLIENT."\t".$msg."\r\n", FILE_APPEND);
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
			$fields = explode("\t", trim($line));
			
			$arr[$cnt]['d'] = $fields[0];
			$arr[$cnt]['c'] = $fields[1];
			$arr[$cnt]['t'] = $fields[2];
			$arr[$cnt]['a'] = $fields[3];
			$arr[$cnt]['v'] = $fields[4];
			$cnt++;
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
				if (file_exists($name_file))
					$this->sensors[$key]['name'] = file_get_contents($name_file);
				else
					$this->sensors[$key]['name'] = $key;
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
		
echo $id,": ",$last_status_data," / ",$current_status_data," [",$sensor_type,"] [",$options_type,"]<br>";
		
		if ($current_status_data != $last_status_data) {
			file_put_contents($status_file, $current_status_data);
			$this->sensors[$key]['value'] = $current_status_data;
			$this->sensors[$key]['changed'] = 'x'.time();
			$this->last_status_change = time();
		}
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
}
?>