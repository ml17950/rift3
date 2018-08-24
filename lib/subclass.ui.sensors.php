<?php
// last change: 2018-07-30
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
		echo "<div class='js-sensors sensor-container'>";

		ksort($this->rift3->status);

		foreach ($this->rift3->status as $sensor_id => $sensor_array) {
			echo "<div class='sensor-box'>";

			if (array_key_exists($sensor_id, $this->rift3->config['names']))
				$sensor_name = $this->rift3->config['names'][$sensor_id];
			else
				$sensor_name = $sensor_id;

			if (array_key_exists($sensor_id, $this->rift3->config['types']))
				$sensor_type = $this->rift3->config['types'][$sensor_id];
			else
				$sensor_type = 'unknown';

			switch ($sensor_type) {
				case 'unknown':		$sensor_image = 'unknown.png'; break;
				case 'time':		$sensor_image = 'time.png'; break;
				case 'date':		$sensor_image = 'date.png'; break;
				case 'temperature':	$sensor_image = 'temperature.png'; break;
				case 'humidity':
					$raw_val = str_replace(' %', '', $sensor_array['status']);
					if ($raw_val < 40)
						$sensor_image = 'humidity-low.png';
					elseif ($raw_val > 60)
						$sensor_image = 'humidity-high.png';
					else
						$sensor_image = 'humidity-ok.png';
					break;
// 				case 'daynight':	$sensor_image = 'daynight-'.$sensor_array['status'].'.png'; break;
// 				case 'weather':		$sensor_image = 'weather-'.$sensor_array['status'].'.png'; break;
// 				case 'gate':		$sensor_image = 'gate-'.$sensor_array['status'].'.png'; break;
// 				case 'light':		$sensor_image = 'light-'.$sensor_array['status'].'.png'; break;
				default:			$sensor_image = ''.$sensor_type.'-'.$sensor_array['status'].'.png'; break; //$sensor_image = 'unknown.png'; break;
			}

			echo "<div class='sensor-icon'><img src='res/img/sensors/",$sensor_image,"' width='32' height='32' alt='",$sensor_type,"' title='",$sensor_image,"'></div>";
			echo "<div class='sensor-name'>",$sensor_name,"</div>";
			switch ($sensor_array['status']) {
				case 'on':
				case 'open':
				case 'home':
					echo "<div class='sensor-value is-on'>",$sensor_array['status'],"</div>";
					break;
				case 'off':
				case 'closed':
				case 'away':
					echo "<div class='sensor-value is-off'>",$sensor_array['status'],"</div>";
					break;
				default:
					echo "<div class='sensor-value is-val'>",$sensor_array['status'],"</div>";
			}
			echo "<div class='sensor-change'>",dtstr($sensor_array['change']),"</div>";
			echo "<div class='sensor-options'><a href='sensors.php?id=",$sensor_id,"&do=rename'><img src='res/img/ui/edit.png' width='24' height='24' alt='edit' title='edit'></a></div>";
			echo "<div class='sensor-options'><a href='sensors.php?id=",$sensor_id,"&do=delete'><img src='res/img/ui/delete.png' width='24' height='24' alt='delete' title='delete'></a></div>";

			echo "</div>";
		}

		echo "</div>"; // .sensor-container
	}

	function rename($sensor_id) {
		echo "<div class='form-container'>";

		echo "<form method='POST' action='sensors.php' accept-charset='utf-8'>";
		echo "<input type='hidden' name='do' value='save-name'>";
		echo "<input type='hidden' name='id' value='",$sensor_id,"'>";

		echo "<label for='currid'>Sensor-ID</label>";
		echo "<span id='currid'>",$sensor_id,"</span><br>";

		echo "<label for='newname'>Sensor-Name</label>";
		echo "<input type='text' name='newname' value='",$this->rift3->config['names'][$sensor_id],"'><br>";

		echo "<label for='newvalue'>Sensor-Wert</label>";
		echo "<input type='text' name='newvalue' value='",$this->rift3->status[$sensor_id]['status'],"'><br>";

		echo "<input type='submit' value='Speichern'>";
		echo "</form>";

		echo "<em>",TXTAPILINKS,"<br><br>";
		if (array_key_exists($sensor_id, $this->rift3->config['switch'])) {
			echo BASEURL.'/api/http/switch/'.$sensor_id.'/on';
			echo "<br><br>";
			echo BASEURL.'/api/http/switch/'.$sensor_id.'/off';
			echo "<br><br>";
			echo BASEURL.'/api/http/switch/'.$sensor_id.'/toggle';
		}
		else
			echo BASEURL.'/api/http/sensor/set/'.$sensor_id.'/%val%';
		echo "</em>";

		echo "</div>";
	}
}
?>