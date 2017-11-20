<?php
	$new_value = date('i');
	// call update-function of clsRIFT3 ($this)
	$this->sensor_update('Minute', $new_value, 'time', 'time60');
?>