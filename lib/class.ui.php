<?php
class clsUserInterface {
	var $rift3;
	var $log;
	var $receipes;
	var $sensors;
	
	function __construct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		
		include_once('lib/lang_'.UI_LANGUAGE.'.php');
		
		include_once('lib/class.rift3.php');
		$this->rift3 = new clsRIFT3();
		
		include_once('lib/subclass.ui.log.php');
		$this->log = new clsLogInterface($this->rift3);
		
		include_once('lib/subclass.ui.receipes.php');
		$this->receipes = new clsReceipeInterface($this->rift3);
		
		include_once('lib/subclass.ui.sensors.php');
		$this->sensors = new clsSensorInterface($this->rift3);
	}
	
	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}
	
	function meta() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		
		echo "<html>";
		echo "<head>";
		echo "	<meta charset='UTF-8'>";
		echo "	<meta http-equiv='Content-Type' content='text/html; charset=utf8'>";
		echo "	<meta name='generator' content='PHP/PsPad'>";
		echo "	<meta name='robots' content='noarchive'>";
		echo "	<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'>";
		echo "	<meta name='apple-mobile-web-app-capable' content='yes'>";
		echo "	<meta name='mobile-web-app-capable' content='yes'>";
		echo "	<link href='touch-icon.png' type='image/png' rel='shortcut icon'>";
		echo "	<link href='touch-icon.png' rel='apple-touch-icon'>";
		echo "	<title>R.I.F.T.3</title>";
		echo "	<link rel='stylesheet' type='text/css' href='res/css/layout.css'>";
		echo "	<script type='text/javascript' src='res/js/jquery-1.11.3.min.js'></script>";
// 		echo "	<script type='text/javascript' src='main.js?20171012-2'></script>";
		echo "</head>";
	}
	
	function header($title = '') {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		
		echo "<body>";
		echo "<div id='content' class='js-content'>";
		
		echo "<h1>",$title,"</h1>";
		
	}
	
	function navigation() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		
		echo "<div id='navigation'>";
// 		echo "	<!--<a href='#' onclick='return getConfig();'><img src='res/img/config.png' border='0' width='48' height='48' hspace='3'></a>-->";
		echo "	<a href='log.php'><img src='res/img/log.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "	<a href='receipes.php'><img src='res/img/receipes.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "	<a href='sensors.php' onclick='return displaySensors();'><img src='res/img/sensors.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "	<a href='#' onclick='return displaySwitches();'><img src='res/img/switches.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "</div>";
	}
	
	function footer() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		
		echo "</div>"; // #content
		
		echo "</body>";
		echo "</html>";
	}
}
?>