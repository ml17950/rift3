<?php
// last change: 2018-08-27
class clsRuleInterface {
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
		echo "<div class='js-rules rule-container'>";

		if (is_array($this->rift3->rules)) {
			foreach ($this->rift3->rules as $ruleid => $rule) {
				echo "<div class='rule-box'>";
				if ($rule['active'] == 1)
					echo "<div class='rule-status'><a href='rules.php?do=deactivate-rule&id=",$ruleid,"'><img src='res/img/ui/is-on.png' width='40' height='40'></a></div>";
				else
					echo "<div class='rule-status'><a href='rules.php?do=activate-rule&id=",$ruleid,"'><img src='res/img/ui/is-off.png' width='40' height='40'></a></div>";
				echo "<div class='rule-name'>",$rule['name'],"</div>";
				echo "<div class='rule-options'><a href='rules.php?do=edit&id=",$ruleid,"'><img src='res/img/ui/edit.png' width='24' height='24' alt='edit' title='edit'></a></div>";

// 				echo "<div class='rule-conditions'>";
// 				foreach ($rule['conditions'] as $type => $condition) {
// 					if (!empty($condition['status']) && ($condition['status'] != '-'))
// 						echo "&bull; ",$condition['status']," &rarr; ",$condition['type']," // ",$condition['value'],"<br>";
// 				}
// 				echo "</div>"; // .rule-conditions

				if ($rule['active'] == 1) {
					echo "<div class='rule-actions'>";
					if (!empty($rule['action']['status']['id']) && ($rule['action']['status']['id'] != '-'))
						echo "&rarr; ",TXTSET," ",$rule['action']['status']['id']," ",TXTSETTO," ",$rule['action']['status']['value'],"<br>";
					if (!empty($rule['action']['trigger']['id']) && ($rule['action']['trigger']['id'] != '-'))
						echo "&rarr; ",TXTACTIVATE," ",TXTTRIGGER,": ",$rule['action']['trigger']['id'],"<br>";
					echo "</div>"; // .rule-actions
				}

				echo "</div>"; // .rule-box
			}

			// -----------------------------------------------------------------

			echo "<div class='form-container'>";
			echo "<form method='POST' action='rules.php' accept-charset='utf-8'>";
			echo "<input type='hidden' name='do' value='create-rule'>";
			echo "<input type='submit' value='",TXTCREATENEWRULE,"'>";
			echo "</form>";
			echo "</div>"; // .form-container
		}

		echo "</div>"; // .rules-container

// 		echo "<div class='debug'>";
// 		debugarr($this->rift3->rules);
// 		echo "</div>";
	}

	function edit($rule_id) {
		$rule = $this->rift3->rules[$rule_id];

		echo "<div class='form-container'>";

		echo "<form method='POST' action='rules.php' accept-charset='utf-8'>";
		echo "<input type='hidden' name='do' value='save-rule'>";
		echo "<input type='hidden' name='ruleid' value='",$rule_id,"'>";

		echo "<label for='currid'>",TXTRULEID,"</label>";
		echo "<span id='currid'>",$rule_id,"</span><br>";

		echo "<label for='rulename'>",TXTRULENAME,"</label>";
		echo "<input type='text' name='rulename' value='",$rule['name'],"'><br>";

		// ---------------------------------------------------------------------

		for ($cond_index=1; $cond_index<4; $cond_index++) {
			if ($cond_index == 1)
				echo "<br>";
			else
				echo "<br>",TXTAND,"<br><br>";

			echo "<label for='cond_status_",$cond_index,"'>",TXTIF,"</label>";

			echo "<select id='cond_status_",$cond_index,"' name='cond_status_",$cond_index,"' size='1'>";
			echo "<option value='-'>---</option>";
			foreach ($this->rift3->status as $status_id => $status) {
				if (array_key_exists($status_id, $this->rift3->config['names']))
					$status_name = $this->rift3->config['names'][$status_id];
				else
					$status_name = $status_id;
	
				if ($status_id == $rule['conditions'][$cond_index]['status'])
					echo "<option value='",$status_id,"' selected>",$status_name,"</option>";
				else
					echo "<option value='",$status_id,"'>",$status_name,"</option>";
			}
			echo "</select><br>";

			$condchecks['EQU'] = TXTRULECONDEQU;
			$condchecks['NEQ'] = TXTRULECONDNEQ;
			$condchecks['LSS'] = TXTRULECONDLSS;
			$condchecks['LEQ'] = TXTRULECONDLEQ;
			$condchecks['GTR'] = TXTRULECONDGTR;
			$condchecks['GEQ'] = TXTRULECONDGEQ;
			$condchecks['OLD'] = TXTRULECONDOLD;

			echo "<label for='cond_type_",$cond_index,"'>",TXTIS,"</label>";

			echo "<select id='cond_type_",$cond_index,"' name='cond_type_",$cond_index,"' size='1'>";
			echo "<option value='-'>---</option>";
			foreach ($condchecks as $cond_id => $cond_name) {
				if ($cond_id == $rule['conditions'][$cond_index]['type'])
					echo "<option value='",$cond_id,"' selected>",$cond_name,"</option>";
				else
					echo "<option value='",$cond_id,"'>",$cond_name,"</option>";
			}
			echo "</select><br>";

			echo "<label for='cond_value_",$cond_index,"'>",TXTASAS,"</label>";
			echo "<input type='text' name='cond_value_",$cond_index,"' value='",$rule['conditions'][$cond_index]['value'],"'><br>";
			
			echo "<label for='cond_current_",$cond_index,"'>",TXTCURRENT,"</label>";
			echo $this->rift3->status[$rule['conditions'][$cond_index]['status']]['status'];
			echo " ",TXTBEFORE," ",(time() - $this->rift3->status[$rule['conditions'][$cond_index]['status']]['change'])," ",TXTSECONDS,"<br>";
		}
		
		// ---------------------------------------------------------------------
		
		echo "<br>",TXTACTIVATETRIGGER,"<br><br>";

		echo "<label for='triggeridold'>",TXTAVAILABLE,"</label>";
		echo "<select id='triggeridold' name='triggeridold' size='1'>";
		echo "<option value='-'>---</option>";
		foreach ($this->rift3->config['trigger'] as $trigger_id => $trigger_change) {
			if ($trigger_id == $rule['action']['trigger']['id'])
				echo "<option value='",$trigger_id,"' selected>",$trigger_id,"</option>";
			else
				echo "<option value='",$trigger_id,"'>",$trigger_id,"</option>";
		}
		echo "</select><br>";

		echo "<label for='triggeridnew'>",TXTORNEW,"</label>";
		echo "<input type='text' name='triggeridnew' value=''><br>";

		// ---------------------------------------------------------------------
		
		echo "<br>",TXTORSETSTATUS,"<br><br>";

		echo "<label for='statusid'>",TXTAVAILABLE,"</label>";
		echo "<select id='statusid' name='statusid' size='1'>";
		echo "<option value='-'>---</option>";
		foreach ($this->rift3->status as $status_id => $status) {
			if (array_key_exists($status_id, $this->rift3->config['names']))
				$status_name = $this->rift3->config['names'][$status_id];
			else
				$status_name = $status_id;
			if ($status_id == $rule['action']['status']['id'])
				echo "<option value='",$status_id,"' selected>",$status_name,"</option>";
			else
				echo "<option value='",$status_id,"'>",$status_name,"</option>";
		}
		echo "</select><br>";

		echo "<label for='statusidnew'>",TXTORNEW,"</label>";
		echo "<input type='text' name='statusidnew' value=''><br>";

		echo "<label for='statusval'>",TXTSETTO,"</label>";
		echo "<input type='text' name='statusval' value='",$rule['action']['status']['value'],"'><br>";
		
		// ---------------------------------------------------------------------

		echo "<input type='submit' value='",TXTSAVE,"'>";
		echo "</form>";

		echo "</div>"; // .form-container

// 		echo "<div class='debug'>";
// 		debugarr($this->rift3->rules[$rule_id]);
// 		debugarr($this->rift3->config);
// 		debugarr($this->rift3->status);
// 		debugarr($this->rift3->trigger);
// 		echo "</div>";
	}
}
?>