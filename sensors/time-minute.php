<?php
// class clsTimeMinute {
// 	var $sensor_name;
// 	
// 	function __construct($sensor_name) {
// 		$this->sensor_class = 'time60';
// 		$this->sensor_name = $sensor_name;
// 	}
// 	
// 	function __destruct() {
// 	}
// 	
// 	function read($param = '') {
// 		return date('i');
// 	}
// }
// 
// $sensor = new clsTimeMinute('Minute');

	$new_value = date('i');
	// call update-function of clsRIFT3 ($this)
	$this->sensor_update('Minute', $new_value, 'time', 'time60');
?>