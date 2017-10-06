<?php
	function param($key, $dflt = '') {
		if (isset($_GET[$key]) && ($_GET[$key] != ''))
			return trim($_GET[$key]);
		else {
			if (isset($_POST[$key]) && ($_POST[$key] != '')) {
				if (is_array($_POST[$key]))
					return $_POST[$key];
				else
					return trim($_POST[$key]);
			}
			else
				return $dflt;
		}
	}
	
	function param_int($key, $dflt = 0) {
		if (isset($_POST[$key]) && ($_POST[$key] != '')) {
			if (is_array($_POST[$key]))
				return $_POST[$key];
			else
				return intval(trim($_POST[$key]));
		}
		elseif (isset($_GET[$key]) && ($_GET[$key] != ''))
			return intval(trim($_GET[$key]));
		else
			return $dflt;
	}
	
	function debugarr(&$arr) {
		echo highlight_string(print_r($arr, true)),"<hr>";
	}
	
	function getLastNDays($days, $format = 'd/m'){
	    $m = date("m"); $de= date("d"); $y= date("Y");
	    $dateArray = array();
	    for($i=0; $i<=$days-1; $i++){
	    	$key = date('Y-m-d', mktime(0,0,0,$m,($de-$i),$y));
	        $dateArray[$key]['date'] = date($format, mktime(0,0,0,$m,($de-$i),$y));
	        $dateArray[$key]['value'] = 0;
	    }
	    return array_reverse($dateArray);
	}
	
// 	
// 	
// 	
// 	$arr_groups['favs'] = 'Favoriten';
// 	$arr_groups['eat'] = 'Essen/Trinken';
// 	$arr_groups['car'] = 'Auto';
// 	$arr_groups['stuff'] = 'Lifestyle';
// 	$arr_groups['income'] = 'Einnahmen';
// 	
// 	$arr_types['favs']['00-other'] = 'Sonstiges';
// 	$arr_types['favs']['10-breakfast'] = 'Frühstück';
// 	$arr_types['favs']['10-lunch'] = 'Mittag';
// 	$arr_types['favs']['10-sweets'] = 'Naschen';
// 	$arr_types['eat']['20-beer'] = 'Bier';
// 	$arr_types['eat']['20-fastfood'] = 'Fastfood';
// 	$arr_types['eat']['20-ice'] = 'Eis';
// 	$arr_types['stuff']['37-medicament'] = 'Medikamente';
// 	$arr_types['stuff']['38-smoking'] = 'Zigaretten';
// 	$arr_types['car']['61-fuel'] = 'Tanken';
// 	$arr_types['car']['62-parking'] = 'Parkgebühren';
// 	$arr_types['income']['95-money'] = 'Gehalt';
// 	$arr_types['income']['97-sale'] = 'Verkauf';
// 	
// 	$arr_type_names['00-other'] = 'Sonstiges';
// 	$arr_type_names['10-breakfast'] = 'Frühstück';
// 	$arr_type_names['10-lunch'] = 'Mittag';
// 	$arr_type_names['10-sweets'] = 'Naschen';
// 	$arr_type_names['20-beer'] = 'Bier';
// 	$arr_type_names['20-fastfood'] = 'Fastfood';
// 	$arr_type_names['20-ice'] = 'Eis';
// 	$arr_type_names['37-medicament'] = 'Medikamente';
// 	$arr_type_names['38-smoking'] = 'Zigaretten';
// 	$arr_type_names['61-fuel'] = 'Tanken';
// 	$arr_type_names['62-parking'] = 'Parkgebühren';
// 	$arr_type_names['95-money'] = 'Gehalt';
// 	$arr_type_names['97-sale'] = 'Verkauf';
// 	
// 	$arr_accounts = array('HHK');
// 	$arr_names = array('Martin','Verena');
?>