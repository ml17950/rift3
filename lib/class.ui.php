<?php
// last change: 2018-04-23
class clsUserInterface {
	var $rift3;
	var $home;
	var $sensors;
	var $rules;
	var $trigger;
	var $log;
	var $config;
	var $cronjobs;

	function __construct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		include_once('lib/lang_'.UI_LANGUAGE.'.php');

		include_once('lib/class.rift3.php');
		$this->rift3 = new clsRIFT3();

		include_once('lib/subclass.ui.home.php');
		$this->home = new clsHomeInterface($this->rift3);

		include_once('lib/subclass.ui.sensors.php');
		$this->sensors = new clsSensorInterface($this->rift3);

		include_once('lib/subclass.ui.rules.php');
		$this->rules = new clsRuleInterface($this->rift3);

		include_once('lib/subclass.ui.trigger.php');
		$this->trigger = new clsTriggerInterface($this->rift3);

		include_once('lib/subclass.ui.log.php');
		$this->log = new clsLogInterface($this->rift3);

		include_once('lib/subclass.ui.config.php');
		$this->config = new clsConfigInterface($this->rift3);

		include_once('lib/subclass.ui.cronjobs.php');
		$this->cronjobs = new clsCronjobInterface($this->rift3);
	}

	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}

	function meta($with_autoreload = true) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		ob_start('ob_gzhandler');

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
		echo "	<title>RIFT3</title>";
		echo "	<link rel='stylesheet' type='text/css' href='res/css/layout.css'>";
		echo "	<script type='text/javascript' src='res/js/jquery-1.11.3.min.js'></script>";
		echo "	<script type='text/javascript' src='res/js/rift3.js?",VERSION,"'></script>";
		if ($with_autoreload) {
			echo "	<script type='text/javascript'>\n";
			echo "	document.addEventListener('visibilitychange', function () {\n";
			echo "	    // fires when user switches tabs, apps, goes to homescreen, etc.\n";
			echo "	    if (document.visibilityState === 'hidden') { lastView = Date.now() / 1000 | 0; }\n";
			echo "	    // fires when app transitions from prerender, user returns to the app / tab.\n";
			echo "	    if (document.visibilityState === 'visible') {\n";
			echo "	    	var now = Date.now() / 1000 | 0;\n";
			echo "	    	if (lastView > 0) { if ((now - lastView) > 120) { location.reload(); } }\n";
			echo "	      lastView = now;\n";
			echo "	    }\n";
			echo "	});\n";
			echo "	</script>\n";
		}
		echo "</head>";
	}

	function header($title = '') {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		echo "<body>";
		echo "<div id='content' class='js-content'>";

		echo "<h1 id='js-title'>",$title," | ",date('d.m.y H:i:s'),"</h1>";
	}

	function navigation() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		echo "<div id='navigation'>";
// 		echo "	<!--<a href='#' onclick='return getConfig();'><img src='res/img/config.png' border='0' width='48' height='48' hspace='3'></a>-->";
		echo "	<a href='config.php'><img src='res/img/ui/config.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "	<a href='log.php'><img src='res/img/ui/log.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "	<a href='cronjobs.php'><img src='res/img/ui/cronjobs.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "	<a href='rules.php'><img src='res/img/ui/rules.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "	<a href='trigger.php'><img src='res/img/ui/trigger.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "	<a href='sensors.php'><img src='res/img/ui/sensors.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "	<a href='home.php'><img src='res/img/ui/home.png' border='0' width='46' height='46' hspace='3'></a>";
// 		echo "	<a href='index.php'><img src='res/img/ui/home.png' border='0' width='46' height='46' hspace='3'></a>";
// 		echo "	<a href='index.html' onclick='return displaySwitches();'><img src='res/img/switches.png' border='0' width='46' height='46' hspace='3'></a>";
		echo "</div>";
	}

	function footer() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		echo "</div>"; // #content

		echo "</body>";
		echo "</html>";
	}

	function success_message($msg, $goto = '') {
		echo "<div class='success-msg'>",$msg,"</div>";
		if (!empty($goto))
			echo "<meta http-equiv='refresh' content='3; URL=",$goto,"'>";
		else
			echo "<meta http-equiv='refresh' content='3; URL=home.php'>";
	}

	function error_message($msg, $goto = '') {
		echo "<div class='error-msg'>",$msg,"</div>";
		if (!empty($goto))
			echo "<meta http-equiv='refresh' content='3; URL=",$goto,"'>";
		else
			echo "<meta http-equiv='refresh' content='3; URL=home.php'>";
	}
}
?>