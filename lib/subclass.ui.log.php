<?php
// last change: 2018-07-16
class clsLogInterface {
	var $rift3;

	function __construct(&$rift3) {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		$this->rift3 = $rift3;
	}

	function __destruct() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
	}

	function search() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		echo "<div class='log-search'>";
		echo "<input type='text' id='js-search' value=''  onkeyup='return rift3log.search()' />";
		echo "</div>"; // .log-search
	}

	function display() {
		//echo __CLASS__.'::'.__FUNCTION__.'<br>';
		echo "<div class='js-log log-container'>";

		$log_entries = $this->rift3->log_read();
		
// debugarr($log_entries);

		if (count($log_entries) > 0) {
			foreach ($log_entries as $log_entry) {
				echo "<div class='log-box log-box-",$log_entry['section'],"'>";
				echo "<div class='log-client'><img src='res/img/clients/",$log_entry['client'],".png' width='16' height='16' alt='",$log_entry['client'],"'></div>";
				echo "<div class='log-time'>",dtstr($log_entry['datetime']),"</div>";
// 				if (strlen($log_entry['t']) > 1)
// 					echo "<div class='log-trigger'>",$log_entry['t'],"</div>";
				if (!empty($log_entry['info']))
					echo "<div class='log-message'>",$log_entry['message']," <span class='log-info'>",$log_entry['info'],"</span></div>";
				else
					echo "<div class='log-message'>",$log_entry['message'],"</div>";
				if (!empty($log_entry['value'])) {
					if ($log_entry['value'] == 'on')
						echo "<div class='log-value is-on'>",$log_entry['value'],"</div>";
					else if ($log_entry['value'] == 'off')
						echo "<div class='log-value is-off'>",$log_entry['value'],"</div>";
					else if ($log_entry['value'] == 'nochg')
						echo "<div class='log-value is-notchanged'>",$log_entry['value'],"</div>";
// 					elseif (($log_entry['value'] == ON2OFF) || ($log_entry['value'] == OFF2ON))
// 						echo "<div class='log-value is-changed'>",$log_entry['value'],"</div>";
					else {
						if ($log_entry['info'] == 'connect')
							echo "<div class='log-value is-val'><a href='http://",$log_entry['value'],"/status'>",$log_entry['value'],"</a></div>";
						else
							echo "<div class='log-value is-val'>",$log_entry['value'],"</div>";
					}
				}
				echo "</div>";
			}
		}

		echo "</div>"; // .log-container
	}
}
?>