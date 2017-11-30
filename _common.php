<?php
// last change: 2017-11-19
	function param($key, $dflt = '') {
		if (isset($_GET[$key]) && ($_GET[$key] != ''))
			return trim($_GET[$key]);
		else {
			if (isset($_POST[$key]) && ($_POST[$key] != '')) {
				if (is_array($_POST[$key]))
					return $_POST[$key];
				else
					return trim($_POST[$key]);
			}
			else
				return $dflt;
		}
	}
	
	function param_int($key, $dflt = 0) {
		if (isset($_POST[$key]) && ($_POST[$key] != '')) {
			if (is_array($_POST[$key]))
				return $_POST[$key];
			else
				return intval(trim($_POST[$key]));
		}
		elseif (isset($_GET[$key]) && ($_GET[$key] != ''))
			return intval(trim($_GET[$key]));
		else
			return $dflt;
	}
	
	function debugarr(&$arr) {
		echo highlight_string(print_r($arr, true)),"<hr>";
	}
	
	function getLastNDays($days, $format = 'd/m'){
	    $m = date("m"); $de= date("d"); $y= date("Y");
	    $dateArray = array();
	    for($i=0; $i<=$days-1; $i++){
	    	$key = date('Y-m-d', mktime(0,0,0,$m,($de-$i),$y));
	        $dateArray[$key]['date'] = date($format, mktime(0,0,0,$m,($de-$i),$y));
	        $dateArray[$key]['value'] = 0;
	    }
	    return array_reverse($dateArray);
	}
	
	function virtual_device($on_param, $off_param, $onoff) {
	}
?>