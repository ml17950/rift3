<?php
// last change: 2017-11-30
class clsHomeInterface {
	var $rift3;

	function __construct(&$rift3) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		$this->rift3 = $rift3;
	}

	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}

	function display_widgets() {
		echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}

	function display_switches() {
		echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}
?>