<?php
class clsItsWeekend {
	var $sensor_name;
	
	function __construct($sensor_name) {
		$this->sensor_class = 'onoff';
		$this->sensor_name = $sensor_name;
	}
	
	function __destruct() {
	}
	
	function read($param = '') {
		switch (date('w')) {
			case 0: // Sunday
			case 6: // Saturday
				return YES;
				break;
		}
		return NO;
	}
}

$sensor = new clsItsWeekend('Weekend');
?>