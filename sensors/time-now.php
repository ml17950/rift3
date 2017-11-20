<?php
// class clsTimeNow {
// 	var $sensor_name;
// 	
// 	function __construct($sensor_name) {
// 		$this->sensor_class = 'time';
// 		$this->sensor_name = $sensor_name;
// 	}
// 	
// 	function __destruct() {
// 	}
// 	
// 	function read($param = '') {
// 		return date('H:i');
// 	}
// }
// 
// $sensor = new clsTimeNow('Time');

	$new_value = date('H:i');
	// call update-function of clsRIFT3 ($this)
	$this->sensor_update('Time', $new_value, 'time', 'time');
?>