<?php
// last change: 2017-11-23
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
				echo "<div class='receipe-box' onclick='document.location.href=\"receipes.php?do=edit-trigger&id=",$key,"\";'>";
// 				echo "<div class='receipe-logo'><img src='res/img/clients/",$log_entry['c'],".png' width='16' height='16' alt='",$log_entry['c'],"'></div>";
				echo "<div class='receipe-title'>",$receipe['name'],"</div>";

				echo "<div class='receipe-meta'>";
				if ($receipe['trigger']['7280fa2cf1815eb354fca963addfc2b0']['chk'] == ON)
					echo "<div class='receipe-status'><span class='receipe-status-circle is-on'></span> ",TXTACTIVE,"</div>";
				elseif ($receipe['trigger']['a8c2f020b12bab55d01eab919f8138fe']['chk'] == OFF)
					echo "<div class='receipe-status'><span class='receipe-status-circle is-off'></span> ",TXTINACTIVE,"</div>";
				else
					echo "<div class='receipe-status'><span class='receipe-status-circle is-val'></span> ",TXTUNKNOWN,"</div>";
				echo "<div class='receipe-time'>",date('d.m.y H:i', $receipe['last_run']),"</div>";
				echo "</div>"; // .receipe-meta

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
		
		if (array_key_exists('trigger', $this->rift3->receipes[$receipe_id]))
			$rtrigger = $this->rift3->receipes[$receipe_id]['trigger'];
		else
			$rtrigger = array();
		if (array_key_exists('actions', $this->rift3->receipes[$receipe_id]))
			$ractions = $this->rift3->receipes[$receipe_id]['actions'];
		else
			$ractions = array();
		
		echo "<h1>Rezept bearbeiten</h1>";
		
		echo "<form name='recfrm' id='recfrm' action='receipes.php'>";
		echo "<input type='text' name='oname' value='",$receipe_id,"'>";
		
		echo "<h2>Wenn folgende Bedingungen zutreffen ...</h2>";
		
		echo "<div class='flex-row'>";
		echo "<div class='flex-name'>&nbsp;</div>";
		echo "<div class='flex-option'>Keine Aktion</div>";
		echo "<div class='flex-option'>An</div>";
		echo "<div class='flex-option'>Aus</div>";
		echo "<div class='flex-option'>Jetzt</div>";
		echo "</div>";
		
		foreach ($this->rift3->sensors as $skey => $sarr) {
			if ($sarr['optt'] == 'onoff') {
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
			elseif ($sarr['optt'] == 'time24') {
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
			}
			elseif ($sarr['optt'] == 'time60') {
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
			}
			elseif ($sarr['optt'] == 'time') {
			}
		}
		
		echo "<br><h2>Dann folgende Aktionen ausführen ...</h2>";
		
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