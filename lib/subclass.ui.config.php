<?php
// last change: 2018-08-10
class clsConfigInterface {
	var $rift3;

	function __construct(&$rift3) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		$this->rift3 = $rift3;
	}

	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}

	function formHeader($action) {
		echo "<form method='POST' action='config.php' accept-charset='utf-8'>";
		echo "<input type='hidden' name='do' value='",$action,"'>";
	}

	function formFooter() {
		echo "</form>";
	}

	function display_devices() {
		echo "<div class='config-container'>";
		echo "<h2>",CFGDEVICES," - UI Version: ",VERSION,"</h2>";

		if (is_array($this->rift3->config['devices']) && (count($this->rift3->config['devices']) > 0)) {
			ksort($this->rift3->config['devices']);

			foreach ($this->rift3->config['devices'] as $device_id => $device_array) {
				echo "<div class='config-device-box'>";

				echo "<div class='config-device-row'>";
				echo "<div class='config-device-key'>DEVICE-ID</div>";
				echo "<div class='config-device-val'>",$device_id,"</div>";
				echo "</div>"; // .config-device-row

				$register_dt = 0;
				$last_ping_dt = 0;
				$device_ohoco_version = '';

				foreach ($device_array as $key => $val) {
					echo "<div class='config-device-row'>";
					echo "<div class='config-device-key'>",strtoupper($key),"</div>";
					if ($key == 'vc') {
						if (floatval(substr($val, 0, -2)) < 2.6)
							echo "<div class='config-device-val is-red'>",$val," - WARNING !</div>";
						else
							echo "<div class='config-device-val'>",$val,"</div>";
					}
					else
						echo "<div class='config-device-val'>",$val,"</div>";
					echo "</div>"; // .config-device-row

					if ($key == 'registered')
						$register_dt = strtotime($val);
					elseif ($key == 'last-ping')
						$last_ping_dt = strtotime($val);
					elseif ($key == 'ohoco-version')
						$device_ohoco_version = $val;
				}

				$runtime_seconds = time() - $register_dt;
				//$runtime_seconds = time() - $last_ping_dt;
				echo "<div class='config-device-row'>";
				echo "<div class='config-device-key'>RUNTIME</div>";
				if ((time() - $last_ping_dt) > 3630)
					echo "<div class='config-device-val is-red'>OFFLINE</div>";
				else
					echo "<div class='config-device-val'>",time_diff($runtime_seconds, false),"</div>";
				echo "</div>"; // .config-device-row

				echo "<div class='config-device-row'>";
				echo "<div class='config-device-val'>";
				echo "<a href='config.php?id=",$device_id,"&cmd=REREG&do=sendcmd' class='config-button green-button'>REREG</a>";
				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=REBOOT&do=sendcmd' class='config-button orange-button'>REBOOT</a>";
				echo " &bull; <a href='config.php?id=",$device_id,"&do=delete' class='config-button red-button'>DELETE</a>"; //<img src='res/img/ui/delete.png' width='24' height='24' alt='delete' title='delete'></a>";
				if ($device_ohoco_version >= '2018-08-10')
					echo " &bull; <a href='config.php?view=devicecfg&id=",$device_id,"' class='config-button cyan-button'>CONFIG</a>";
				
// 				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=CFG:checkInterval=900000&do=sendcmd'>15 Min</a>";
// 				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=CFG:checkInterval=300000&do=sendcmd'>5 Min</a>";
// 				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=CFG:checkInterval=120000&do=sendcmd'>2 Min</a>";
// 				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=CFG:checkInterval=60000&do=sendcmd'>1 Min</a>";
// 				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=CFG:checkInterval=15000&do=sendcmd'>15 Sec</a>";
// 				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=CFG:checkInterval=5000&do=sendcmd'>5 Sec</a>";
// 				
// 				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=WRITECFG&do=sendcmd'>WRITECFG</a>";
// 				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=SENDCFG&do=sendcmd'>SENDCFG</a>";
				echo " &bull; <a href='config.php?id=",$device_id,"&cmd=DEBUG&do=sendcmd' class='config-button grey-button'>DEBUG</a>";
				
				echo "</div>";
				echo "</div>"; // .config-device-row

				echo "</div>"; // .config-device-box
			}
		}
		
		echo "</div>"; // .config-container
	}

	function display_device_configuration($device_id) {
		$cfg_file = ABSPATH.'/config/'.$device_id.'.cfg';

		if (file_exists($cfg_file)) {
			$cfg_time = filemtime($cfg_file);

			if ((time() - $cfg_time) > 1200) {
				echo "<br><br>",CFGDEVCFG01,"<br><br>";
				echo "<meta http-equiv='refresh' content='1; URL=config.php?view=devicecfg&id=",$device_id,"&cmd=SENDCFG&do=sendcmd'>";
			}
			else {
				$cfg_data = file_get_contents($cfg_file);
				$cfg_rows = explode('|', trim($cfg_data));
				$cfg_key_replacements = array(	'chk' => 'checkInterval',
												'min' => 'minValue',
												'max' => 'maxValue',
												'itr' => 'inTrigger',
												'otr' => 'outTrigger',
												'wip' => 'WiFi-SSID',
												'wpw' => 'WiFi-PASS',
												'cip' => 'Controller-IP',
												'cpo' => 'Controller-Port');

				echo "<div class='form-container'>";

				foreach ($cfg_rows as $row) {
					$fields = explode(':', $row);
					$cfgname = str_replace(array_keys($cfg_key_replacements), array_values($cfg_key_replacements), $fields[0]);
					echo "<label for='newname'>",$cfgname,"</label>";
					echo "<input type='text' name='newname' value='",$fields[1],"' onchange='return rift3config.updval(\"",$device_id,"\",\"",$cfgname,"\",this.value);'><br>";
				}

				echo "<input type='submit' value='",CFGDEVCFG02,"' onclick='return rift3config.writecfg(\"",$device_id,"\");'>";
				echo "<br><br>",CFGDEVCFG03;

				echo "</div>";
			}
		}
		else {
			echo "<br><br>",CFGDEVCFG01,"<br><br>";
			echo "<meta http-equiv='refresh' content='1; URL=config.php?view=devicecfg&id=",$device_id,"&cmd=SENDCFG&do=sendcmd'>";
		}
	}

	function display_system() {
		echo "<div class='config-container'>";
		echo "<h2>",CFGSYSTEM,"</h2>";

		echo "<span>UI Version: ",VERSION,"</span><br><br>";
		
		echo "<input type='checkbox' name='ready' value='yes'> ",CFGSYS01,"<br<";
		
		echo "</div>"; // .widget-container
	}

	function display_widgets() {
		echo "<div class='config-container'>";
		echo "<h2>",CFGWIDGETS,"</h2>";

		for ($i=0; $i<=7; $i++) {
			echo "<label for='widget[",$i,"]'>Widget ",($i + 1),"</label>";
			echo "<select name='widget[",$i,"]' size='1'>";
			echo "<option value=''>-</option>";
			foreach ($this->rift3->status as $sid => $sarr) {
				if ($sid == $this->rift3->config['widgets'][$i])
					echo "<option value='",$sid,"' selected>",$this->rift3->config['names'][$sid],"</option>";
				else
					echo "<option value='",$sid,"'>",$this->rift3->config['names'][$sid],"</option>";
			}
			echo "</select><br>";
		}

		echo "<input type='submit' value='Speichern'>";

		echo "</div>"; // .config-container
	}

	function display_mqtt() {
		echo "<div class='config-container'>";
		echo __CLASS__.'::'.__FUNCTION__.'<br>';
		echo "</div>"; // .config-container
	}

	function display_types() {
		echo "<div class='config-container'>";
		echo __CLASS__.'::'.__FUNCTION__.'<br>';
		echo "</div>"; // .config-container
	}
}
?>