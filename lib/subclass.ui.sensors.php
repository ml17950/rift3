<?php
// last change: 2017-11-30
class clsSensorInterface {
	var $rift3;

	function __construct(&$rift3) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		$this->rift3 = $rift3;
	}

	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}

	function display() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		$this->rift3->sensor_initialize();
		$just_updated = time() - 60;
		$today_updated = mktime(0,0,0);

		echo "<div class='js-sensors sensor-container'>";

		if (count($this->rift3->sensors) > 0) {
			foreach ($this->rift3->sensors as $key => $sensor) {
				echo "<div class='sensor-box' id='sensor-",$key,"'>"; // onclick='return sensor.show(\"",k,"\", \"",jsonObj['sensors'][k]['n'],"\", \"",jsonObj['sensors'][k]['t'],"\");'>";
				echo "<div class='sensor-icon'><img src='res/img/types/",strtolower($sensor['type']),".png' width='32' height='32' alt='",$sensor['type'],"'></div>";
				echo "<div class='sensor-name'>",$sensor['name'],"</div>";
				if ($sensor['changed'] >= $just_updated)
					echo "<div class='sensor-time is-just-updated'>",date('d.m H:i', $sensor['changed']),"</div>";
				elseif ($sensor['changed'] >= $today_updated)
					echo "<div class='sensor-time is-today-updated'>",date('d.m H:i', $sensor['changed']),"</div>";
				else
					echo "<div class='sensor-time'>",date('d.m H:i', $sensor['changed']),"</div>";
				if ($sensor['value'] == 'ON')
					echo "<div class='sensor-value is-on'>",$sensor['value'],"</div>";
				else if ($sensor['value'] == 'OFF')
					echo "<div class='sensor-value is-off'>",$sensor['value'],"</div>";
				else
					echo "<div class='sensor-value is-val'>",$sensor['value'],"</div>";
				echo "</div>";
			}
		}

		echo "</div>"; // .sensor-container

// 		echo "<div class='debug'>";
// 		debugarr($this->rift3->sensors);
// 		echo "</div>";
	}
}
?>