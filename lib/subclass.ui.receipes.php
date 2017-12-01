<?php
// last change: 2017-12-01
class clsReceipeInterface {
	var $rift3;
	
	function __construct(&$rift3) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		$this->rift3 = $rift3;
	}

	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}
	
	function display_overview() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		echo "<div class='js-receipes receipes-container'>";

		$this->rift3->receipe_initialize();

		if (count($this->rift3->receipes) > 0) {
			foreach ($this->rift3->receipes as $key => $receipe) {
				echo "<div class='receipe-box'>";
				echo "<div class='receipe-title' onclick='document.location.href=\"receipes.php?do=edit-trigger&id=",$key,"\";'>",$receipe['name'],"</div>";

				echo "<div class='receipe-meta'>";
				if ($receipe['trigger']['7280fa2cf1815eb354fca963addfc2b0']['chk'] == ON)
					echo "<div class='receipe-status'><span class='receipe-status-circle is-on'></span> ",TXTACTIVE,"</div>";
				elseif ($receipe['trigger']['a8c2f020b12bab55d01eab919f8138fe']['chk'] == OFF)
					echo "<div class='receipe-status'><span class='receipe-status-circle is-off'></span> ",TXTINACTIVE,"</div>";
				else
					echo "<div class='receipe-status'><span class='receipe-status-circle is-val'></span> ",TXTUNKNOWN,"</div>";
				echo "<div class='receipe-time'>",date('d.m.y H:i', $receipe['last_run']),"</div>";
				if (count($receipe['trigger']) > 0) {
					$receipe_hash = md5($key);
					echo "<div class='receipe-info'><img src='res/img/info.png' width='16' height='16' onclick='return toggle_receipe_trigger(\"",$receipe_hash,"\");'></div>";
				}
				echo "</div>"; // .receipe-meta
				
				if (count($receipe['trigger']) > 0) {
					echo "<div class='receipe-trigger is-hidden' id='js-",$receipe_hash,"'>";
					$this->rift3->receipe_display_trigger($receipe['trigger'], $receipe_hash);
					echo "</div>"; // .receipe-trigger
				}

				echo "</div>"; // .receipe-box
			}
		}

// debugarr($this->rift3->receipes);

		echo "</div>"; // .receipes-container
	}
	
	function display_edit_form($receipe_id) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		echo "<div class='js-receipes receipes-container'>";
		
		$this->rift3->device_initialize();
		$this->rift3->notifier_initialize();
		$this->rift3->sensor_initialize();
		$this->rift3->sensor_updateall();
		$this->rift3->receipe_initialize();
		
		$receipe = $this->rift3->receipes[$receipe_id];
		
		if (array_key_exists('trigger', $this->rift3->receipes[$receipe_id])) {
			foreach ($this->rift3->receipes[$receipe_id]['trigger'] as $tmp => $trigger) {
				$rtrigger[$trigger['id']] = $trigger['chk'];
			}
		}
		else
			$rtrigger = array();
		if (array_key_exists('actions', $this->rift3->receipes[$receipe_id]))
			$ractions = $this->rift3->receipes[$receipe_id]['actions'];
		else
			$ractions = array();
		
		echo "<form name='recfrm' id='recfrm' method='post' action='receipes.php'>";
		echo "<input type='text' name='do' value='save-receipe'>";
		echo "<input type='text' name='id' value='",$receipe_id,"'>";
		echo "<input type='text' name='new_name' value='",$receipe_id,"'>";
		
		echo "<h2>",TXTRECEIPE_IF,"</h2>";
		
		echo "<div class='flex-row'>";
		echo "<div class='flex-name'>&nbsp;</div>";
		echo "<div class='flex-option'>Keine Aktion</div>";
		echo "<div class='flex-option'>An / Ja</div>";
		echo "<div class='flex-option'>Aus / Nein</div>";
		echo "<div class='flex-option'>Aktuell</div>";
		echo "</div>";
		
		foreach ($this->rift3->sensors as $skey => $sarr) {
			switch ($sarr['optt']) {
				case 'time24':
					if (array_key_exists($skey, $rtrigger))
						$checkvalue = $rtrigger[$skey];
					else
						$checkvalue = '';
					
					echo "<div class='flex-row'>";
					echo "<div class='flex-name'>",$this->rift3->sensors[$skey]['name'],"</div>";
					echo "<div class='flex-select'><select size='1' name='trigger[",$skey,"]'>";
					echo "<option value=''>---</option>";
					for ($h=0; $h<24; $h++) {
						$v = str_pad($h, 2, '0', STR_PAD_LEFT);
						if ($v == $checkvalue)
							echo "<option value='",$v,"' selected>",$v,"</option>";
						else
							echo "<option value='",$v,"'>",$v,"</option>";
					}
					echo "</select></div>";
					echo "<div class='flex-option'>",$this->rift3->sensors[$skey]['value'],"</div>";
					echo "</div>";
					break;

				case 'time60':
					if (array_key_exists($skey, $rtrigger))
						$checkvalue = $rtrigger[$skey];
					else
						$checkvalue = '';
					
					echo "<div class='flex-row'>";
					echo "<div class='flex-name'>",$this->rift3->sensors[$skey]['name'],"</div>";
					echo "<div class='flex-select'><select size='1' name='trigger[",$skey,"]'>";
					echo "<option value=''>---</option>";
					for ($m=0; $m<60; $m++) {
						$v = str_pad($m, 2, '0', STR_PAD_LEFT);
						if ($v == $checkvalue)
							echo "<option value='",$v,"' selected>",$v,"</option>";
						else
							echo "<option value='",$v,"'>",$v,"</option>";
					}
					echo "</select></div>";
					echo "<div class='flex-option'>",$this->rift3->sensors[$skey]['value'],"</div>";
					echo "</div>";
					break;

				case 'time':
					echo "TODO (",$sarr['optt'],")<br>";
					break;

				case 'number':
					echo "TODO (",$sarr['optt'],")<br>";
					break;

				case 'onoff':
				default:
					if (array_key_exists($skey, $rtrigger)) {
						$checkedA = '';
						if ($rtrigger[$skey] == ON) {
							$checkedB = 'checked';
							$checkedC = '';
						}
						else {
							$checkedB = '';
							$checkedC = 'checked';
						}
					}
					else {
						$checkedA = 'checked';
						$checkedB = '';
						$checkedC = '';
					}
					
					echo "<div class='flex-row'>";
					echo "<div class='flex-name'>",$this->rift3->sensors[$skey]['name'],"</div>";
					echo "<div class='flex-option'><input type='radio' class='radio' name='trigger[",$skey,"]' value='' ",$checkedA,"></div>"; //<label for='",$dname,"'>Nix</label></div>";
					echo "<div class='flex-option'><input type='radio' class='radio' name='trigger[",$skey,"]' value='",ON,"' ",$checkedB,"></div>"; //<label for='",$dname,"'>An</label></div>";
					echo "<div class='flex-option'><input type='radio' class='radio' name='trigger[",$skey,"]' value='",OFF,"' ",$checkedC,"></div>"; //<label for='",$dname,"'>Aus</label></div>";
					echo "<div class='flex-option'>",$this->rift3->sensors[$skey]['value'],"</div>";
					echo "</div>";
			}
		}
		
		echo "<br><h2>",TXTRECEIPE_THEN,"</h2>";
		
		echo "<div class='flex-row'>";
		echo "<div class='flex-name'>&nbsp;</div>";
		echo "<div class='flex-option'>Kein Aktion</div>";
		echo "<div class='flex-option'>Einschalten</div>";
		echo "<div class='flex-option'>Ausschalten</div>";
		echo "</div>";
		
		foreach ($this->rift3->devices as $dkey => $darr) {
			if (array_key_exists($dkey, $ractions)) {
				$checkedA = '';
				if ($ractions[$dkey] == ON) {
					$checkedB = 'checked';
					$checkedC = '';
				}
				else {
					$checkedB = '';
					$checkedC = 'checked';
				}
			}
			else {
				$checkedA = 'checked';
				$checkedB = '';
				$checkedC = '';
			}
			
			echo "<div class='flex-row'>";
			echo "<div class='flex-name'>",$darr['name'],"</div>";
			echo "<div class='flex-option'><input type='radio' class='radio' name='actions[",$dkey,"]' value='' ",$checkedA,"></div>"; //<label for='",$dname,"'>Nix</label></div>";
			echo "<div class='flex-option'><input type='radio' class='radio' name='actions[",$dkey,"]' value='",ON,"' ",$checkedB,"></div>"; //<label for='",$dname,"'>An</label></div>";
			echo "<div class='flex-option'><input type='radio' class='radio' name='actions[",$dkey,"]' value='",OFF,"' ",$checkedC,"></div>"; //<label for='",$dname,"'>Aus</label></div>";
			echo "</div>";
		}
		
		foreach ($this->rift3->notifier as $nkey => $narr) {
			if (array_key_exists($nkey, $ractions))
				$nval = $ractions[$nkey];
			else
				$nval = '';

			echo "<div class='flex-row'>";
			echo "<div class='flex-name'>",$narr['name'],"</div>";
			echo "<div class='flex-text'><input type='text' name='actions[",$nkey,"]' value='",$nval,"'></div>";
			echo "</div>";
		}
		
		echo "<br><input type='submit' value='Speichern'>";
		
		echo "</form>";
		
// debugarr($this->rift3->receipes);
// debugarr($receipe);
// debugarr($this->rift3->devices);
// debugarr($this->rift3->notifier);
debugarr($this->rift3->sensors);

		
		echo "</div>"; // .receipes-container
	}
	
	function desplay_edit_actions($receipe_id) {
		echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}

}
?>