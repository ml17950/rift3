<?php
// class clsTimeHour {
// 	var $sensor_name;
// 	
// 	function __construct($sensor_name) {
// 		$this->sensor_class = 'time24';
// 		$this->sensor_name = $sensor_name;
// 	}
// 	
// 	function __destruct() {
// 	}
// 	
// 	function read($param = '') {
// 		return date('H');
// 	}
// }
// 
// $sensor = new clsTimeHour('Hour');

	$new_value = date('H');
	// call update-function of clsRIFT3 ($this)
	$this->sensor_update('Hour', $new_value, 'time', 'time24');
?>