<?php
	function switch433mhz($on_param, $off_param, $onoff) {
		switch ($onoff) {
			case ON:
				//$cmd = 'send 00001 1 1';
				$cmd = 'send '.$on_param;
				$output = array();
				for ($i=1; $i<=2; $i++) {
					@exec($cmd, $output, $retval);
				}
				break;
			
			case OFF:
				//$cmd = 'send 00001 1 0';
				$cmd = 'send '.$off_param;
				$output = array();
				for ($i=1; $i<=2; $i++) {
					@exec($cmd, $output, $retval);
				}
				break;
		}
	}
	
	$this->action_register('433MHzSwitch', 'switch433mhz', '');
?>