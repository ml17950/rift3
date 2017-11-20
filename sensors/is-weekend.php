<?php
	function check_weekend() {
		switch (date('w')) {
			case 0: // Sunday
			case 6: // Saturday
				return YES;
				break;
		}
		return NO;
	}

	$new_value = check_weekend();
	// call update-function of clsRIFT3 ($this)
	$this->sensor_update('Weekend', $new_value, 'date', 'onoff');
?>