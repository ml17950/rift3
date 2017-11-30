<?php
// last change: 2017-11-30
class clsHomeInterface {
	var $rift3;

	function __construct(&$rift3) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		$this->rift3 = $rift3;
		
		$this->rift3->sensor_initialize();
		$this->rift3->device_initialize();
		$this->rift3->widgets_initialize();
	}

	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}

	function widget($name, $type, $value) {
		echo "<div class='widget-box'>";

		switch ($type) {
			case 'weather':
				echo "<div class='widget-icon'><img src='res/img/switches/",$type,"-",$value,".png' width='56' height='56' alt='",$type,"-",$value,"'></div>";
				echo "<div class='widget-name'>",$value,"</div>";
				break;
			
			case 'time':
				echo "<div class='widget-icon'><img src='res/img/switches/",$type,"-ALL.png' width='56' height='56' alt='",$type,"-ALL'></div>";
				echo "<div class='widget-name'>",$value,"</div>";
				break;
			
			case 'temp':
				echo "<div class='widget-icon'><img src='res/img/switches/",$type,"-ALL.png' width='56' height='56' alt='",$type,"-ALL'></div>";
				echo "<div class='widget-name'>",$value," °C</div>";
				break;

			default:
				echo "<div class='widget-icon'><img src='res/img/switches/",$type,"-",$value,".png' width='56' height='56' alt='",$type,"-",$value,"'></div>";
				echo "<div class='widget-name'>",$name,"</div>";
		}

		echo "</div>";
	}

	function display_widgets() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		if (count($this->rift3->widgets) > 0) {
			echo "<div class='js-widgets widget-container'>";
			foreach ($this->rift3->widgets as $id => $key) {
				$this->widget($this->rift3->sensors[$key]['name'], $this->rift3->sensors[$key]['type'], $this->rift3->sensors[$key]['value']);
			}
			echo "</div>"; // .widget-container
		}
	}

	function display_switches() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';

		echo "<div class='js-switches switch-container'>";

		if (count($this->rift3->devices) > 0) {
			foreach ($this->rift3->devices as $key => $device) {
				echo "<div class='switch-box hand' id='switch-",$key,"' onclick='return rift_switch.toggle(\"",$key,"\", \"",$this->rift3->sensors[$key]['type'],"\");'>";
				echo "<div class='switch-icon'><img id='switch-",$key,"-icon' data-state='",$this->rift3->sensors[$key]['value'],"' src='res/img/switches/",$this->rift3->sensors[$key]['type'],"-",$this->rift3->sensors[$key]['value'],".png' width='48' height='48' alt='",$this->rift3->sensors[$key]['type'],"'></div>";
				echo "<div class='switch-name'>",$this->rift3->sensors[$key]['name'],"</div>";
				echo "<div class='switch-time'>",date('d.m H:i', $this->rift3->sensors[$key]['changed']),"</div>";
				echo "</div>";
			}
		}

		echo "</div>"; // .switch-container

// 		echo "<div class='debug'>";
// // 		debugarr($this->rift3->sensors);
// 		debugarr($this->rift3->devices);
// 		echo "</div>";
	}
}
?>