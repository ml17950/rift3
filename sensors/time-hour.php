<?php
class clsTimeHour {
	var $sensor_name;
	
	function __construct($sensor_name) {
		$this->sensor_class = 'time24';
		$this->sensor_name = $sensor_name;
	}
	
	function __destruct() {
	}
	
	function read($param = '') {
		return date('H');
	}
}

$sensor = new clsTimeHour('Hour');
?>