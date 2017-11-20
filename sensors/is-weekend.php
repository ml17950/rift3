<?php
// class clsItsWeekend {
// 	var $sensor_name;
// 	
// 	function __construct($sensor_name) {
// 		$this->sensor_class = 'onoff';
// 		$this->sensor_name = $sensor_name;
// 	}
// 	
// 	function __destruct() {
// 	}
// 	
	function check_weekend() {
		switch (date('w')) {
			case 0: // Sunday
			case 6: // Saturday
				return YES;
				break;
		}
		return NO;
	}
// }
// 
// $sensor = new clsItsWeekend('Weekend');

	$new_value = check_weekend();
	// call update-function of clsRIFT3 ($this)
	$this->sensor_update('Weekend', $new_value, 'date', 'onoff');
?>