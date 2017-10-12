<?php
class clsTimeNow {
	var $sensor_name;
	
	function __construct($sensor_name) {
		$this->sensor_class = 'time';
		$this->sensor_name = $sensor_name;
	}
	
	function __destruct() {
	}
	
	function read($param = '') {
		return date('H:i');
	}
}

$sensor = new clsTimeNow('Time');
?>