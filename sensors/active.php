<?php
class clsActive {
	var $sensor_name;
	
	function __construct($sensor_name) {
		$this->sensor_class = 'onoff';
		$this->sensor_name = $sensor_name;
	}
	
	function __destruct() {
	}
	
	function read($param = '') {
		return YES;
	}
}

$sensor = new clsActive('Active');
?>