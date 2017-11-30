<?php
	if (intval(date('H')) <= 12)
		$new_value = YES;
	else
		$new_value = NO;
	// call update-function of clsRIFT3 ($this)
	$this->sensor_update('Time', $new_value, 'time', 'time');
?>