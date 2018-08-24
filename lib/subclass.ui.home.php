<?php
// last change: 2018-08-24
class clsHomeInterface {
	var $rift3;

	function __construct(&$rift3) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		$this->rift3 = $rift3;
		
// 		$this->rift3->sensor_initialize();
// 		$this->rift3->device_initialize();
// 		$this->rift3->widgets_initialize();
	}

	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}

	function display_widgets() {
		if (is_array($this->rift3->config['widgets']) && count($this->rift3->config['widgets']) > 0) {
			echo "<div class='js-widgets widget-container'>";
			foreach ($this->rift3->config['widgets'] as $num => $id) {
				if (!empty($id)) {
					$sensor_type = $this->rift3->config['types'][$id];

					switch ($sensor_type) {
						case 'time':
							$wimg = 'res/img/sensors/time.png';
							$wtxt = $this->rift3->status[$id]['status'];
							break;

						case 'date':
							$wimg = 'res/img/sensors/date.png';
							$wtxt = $this->rift3->status[$id]['status'];
							break;

						case 'temperature':
							$wimg = 'res/img/sensors/temperature.png';
							$wtxt = $this->rift3->status[$id]['status'];
							break;

						case 'humidity':
							$raw_val = str_replace(' %', '', $this->rift3->status[$id]['status']);
							if ($raw_val < 40)
								$wimg = 'res/img/sensors/humidity-low.png';
							elseif ($raw_val > 60)
								$wimg = 'res/img/sensors/humidity-high.png';
							else
								$wimg = 'res/img/sensors/humidity-ok.png';
							$wtxt = $this->rift3->status[$id]['status'];
							break;

						case 'daynight':
							$wimg = 'res/img/sensors/daynight-'.$this->rift3->status[$id]['status'].'.png';
							$wtxt = date('H:i', $this->rift3->status[$id]['change']);
// 							if ($this->rift3->status[$id]['status'] == 'on')
// 								$wtxt = 'Tag';
// 							else
// 								$wtxt = 'Nacht';
							break;

						case 'weather':
							$wimg = 'res/img/sensors/weather-'.$this->rift3->status[$id]['status'].'.png';
							$wtxt = $this->rift3->status[$id]['status'];
							break;

						default:
							if (empty($sensor_type))
								$sensor_type = 'is';
							$wimg = 'res/img/sensors/'.$sensor_type.'-'.$this->rift3->status[$id]['status'].'.png';
							$wtxt = $this->rift3->config['names'][$id];
// 							$wimg = 'res/img/ui/unknown.png';
// 							$wtxt = $id."/".$sensor_type."/".$this->rift3->status[$id]['status'];
					}

					echo "<div class='widget-box'>";
					echo "<div class='widget-icon'><img src='",$wimg,"' width='56' height='56' alt='",$id,"'></div>";
					echo "<div class='widget-name'>",$wtxt,"</div>";
					echo "</div>";
				}
			}
			echo "</div>"; // .widget-container
		}

// 		echo "<div class='debug'>";
// 		debugarr($this->rift3->config);
// 		debugarr($this->rift3->status);
// 		echo "</div>";
	}

	function display_switches() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		echo "<div class='js-devices switch-container'>";

		if (count($this->rift3->config['switch']) > 0) {
			foreach ($this->rift3->config['switch'] as $switch_id => $device_id) {
				echo "<div class='switch-box hand' id='device-",$switch_id,"' onclick='return rift3switch.toggle(\"",$switch_id,"\", \"",$this->rift3->config['types'][$switch_id],"\");'>";
				echo "<div class='switch-icon'><img id='device-",$switch_id,"-icon' data-state='",$this->rift3->status[$switch_id]['status'],"' src='res/img/sensors/",$this->rift3->config['types'][$switch_id],"-",$this->rift3->status[$switch_id]['status'],".png' width='48' height='48' alt='",$this->rift3->config['types'][$switch_id],"'></div>";
				echo "<div class='switch-name'>",$this->rift3->config['names'][$switch_id],"</div>";
				echo "<div class='switch-time'>",dtstr($this->rift3->status[$switch_id]['change'], 'd.m H:i:s'),"</div>";
				echo "</div>";
			}
		}

		echo "</div>"; // .switch-container

// 		echo "<div class='debug'>";
// 		debugarr($this->rift3->conf['devices']);
// 		debugarr($this->rift3->status);
// 		echo "</div>";
	}
}
?>