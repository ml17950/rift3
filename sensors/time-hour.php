<?php
	$new_value = date('H');
	// call update-function of clsRIFT3 ($this)
	$this->sensor_update('Hour', $new_value, 'time', 'time24');
?>