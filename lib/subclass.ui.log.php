<?php
// last change: 2017-11-20
class clsLogInterface {
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
		echo "<div class='js-log log-container'>";

		$log_entries = $this->rift3->log_read();

		if (count($log_entries) > 0) {
			foreach ($log_entries as $log_entry) {
				echo "<div class='log-box'>";
				echo "<div class='log-client'><img src='res/img/clients/",$log_entry['c'],".png' width='16' height='16' alt='",$log_entry['c'],"'></div>";
				echo "<div class='log-time'>",$log_entry['d'],"</div>";
				if (strlen($log_entry['t']) > 0)
					echo "<div class='log-trigger'>",$log_entry['t'],"</div>";
				echo "<div class='log-action'>",$log_entry['a'],"</div>";
				if ($log_entry['v'] == 'ON')
					echo "<div class='log-value is-on'>",$log_entry['v'],"</div>";
				else if ($log_entry['v'] == 'OFF')
					echo "<div class='log-value is-off'>",$log_entry['v'],"</div>";
				else
					echo "<div class='log-value is-val'>",$log_entry['v'],"</div>";
				echo "</div>";
			}
		}

		echo "</div>"; // .log-container
	}
}
?>