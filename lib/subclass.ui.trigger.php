<?php
// last change: 2018-08-24
class clsTriggerInterface {
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
		echo "<div class='js-trigger trigger-container'>";

		if (is_array($this->rift3->config['trigger'])) {
			arsort($this->rift3->config['trigger']);

			if (is_array($this->rift3->config['trigger']) && (count($this->rift3->config['trigger']) > 0)) {
				foreach ($this->rift3->config['trigger'] as $trigger_id => $activated) {
					$trigger_hash = md5($trigger_id);

					if (array_key_exists($trigger_id, $this->rift3->trigger)) {
						$has_actions = true;
					}
					else {
						$has_actions = false;
					}

					echo "<div class='trigger-box'>";

					echo "<div class='trigger-icon'><img src='res/img/sensors/unknown.png' width='32' height='32' alt='",$sensor_type,"' title='",$sensor_image,"'></div>";
					echo "<div class='trigger-name'>",$trigger_id,"</div>";

					echo "<div class='trigger-change'>",dtstr($activated),"</div>";
					echo "<div class='trigger-options'><a href='trigger.php?id=",$trigger_id,"&do=activate'><img src='res/img/ui/activate.png' width='24' height='24' alt='activate' title='activate trigger now'></a></div>";
// 	 				echo "<div class='trigger-options'><a href='trigger.php?id=",$trigger_id,"&do=rename'><img src='res/img/ui/edit.png' width='24' height='24' alt='edit' title='edit'></a></div>";
					echo "<div class='trigger-options'><a href='trigger.php?id=",$trigger_id,"&do=delete'><img src='res/img/ui/delete.png' width='24' height='24' alt='delete' title='delete'></a></div>";

					if ($has_actions) {
						foreach ($this->rift3->trigger[$trigger_id] as $i => $trigger_action) {
							$fields = explode('/', $trigger_action);

							echo "<div class='trigger-actions'>";
							switch ($fields[0]) {
								case 'switch':	echo ACTSWITCH," "; break;
								case 'notify':	echo ACTNOTIFY," "; break;
								case 'cmd':		echo ACTCOMMAND," "; break;
								default: echo $fields[0]," ";
							}
							if (array_key_exists($fields[1], $this->rift3->config['names']))
								echo $this->rift3->config['names'][$fields[1]];
							else
								echo $fields[1];
							echo " &rarr; ";
							switch ($fields[2]) {
								case 'on':	echo TXTON; break;
								case 'off':	echo TXTOFF; break;
								default: echo $fields[2];
							}
							echo "</div>";
						}
					}

					echo "</div>"; // .trigger-box
				}
			}
		}

// echo "<div class='debug'>";
// debugarr($this->rift3->config['trigger']);
// debugarr($this->rift3->trigger);
// debugarr($this->rift3->config);
// echo "</div>";

		echo "</div>"; // .trigger-container
	}

	function display_old() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		echo "<div class='js-trigger trigger-container'>";

		ksort($this->rift3->config['trigger']);

		if (is_array($this->rift3->config['trigger']) && (count($this->rift3->config['trigger']) > 0)) {
			foreach ($this->rift3->config['trigger'] as $trigger_id => $created) {
				$trigger_hash = md5($trigger_id);

				echo "<div class='trigger-box'>";

				echo "<div class='trigger-icon'><img src='res/img/sensors/unknown.png' width='32' height='32' alt='",$sensor_type,"' title='",$sensor_image,"'></div>";

				echo "<div class='trigger-title'>",$trigger_id,"</div>";
				//echo "<div class='trigger-title' onclick='document.location.href=\"receipes.php?do=edit-trigger&id=",$key,"\";'>",$receipe['name'],"</div>";

				if (array_key_exists($trigger_id, $this->rift3->trigger)) {
					echo "<div class='trigger-meta'>";
					echo "<div class='trigger-status'><span class='trigger-status-circle is-on'></span> ",TXTACTIVE,"</div>";
					echo "<div class='trigger-time'>",TXTACTIVATED," ",date('d.m.y H:i', $created),"</div>";
					echo "<div class='trigger-info'><img src='res/img/ui/info.png' width='16' height='16' onclick='return toggle_receipe_trigger(\"",$trigger_hash,"\");'></div>";
					echo "</div>"; // .trigger-meta

					echo "<div class='trigger-trigger is-hidden' id='js-",$trigger_hash,"'>";
					foreach ($this->rift3->trigger[$trigger_id] as $idx => $trigger_action) {
						echo str_replace('/',' &rarr; ', $trigger_action),"<br>";
					}
					echo "</div>"; // .trigger-trigger
				}
				else {
					echo "<div class='trigger-meta'>";
					echo "<div class='trigger-status'><span class='trigger-status-circle is-off'></span> ",TXTINACTIVE,"</div>";
					echo "<div class='trigger-time'>",TXTACTIVATED," ",date('d.m.y H:i', $created),"</div>";
					echo "<div class='trigger-info'>&nbsp;</div>";
					echo "</div>"; // .trigger-meta
				}

				echo "</div>"; // .trigger-box
			}
		}
// echo "<div class='debug'>";
// debugarr($this->rift3->config['trigger']);
// debugarr($this->rift3->trigger);
// echo "</div>";

		echo "</div>"; // .trigger-container
	}
}
?>