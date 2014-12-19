<?php

include($_SERVER['DOCUMENT_ROOT'].'inc/dbs/ref_stats_config.php');
include(__DIR__."/../config.php"); //imports relative to "inc/functions.php"

function reporter($color, $msg, $visibility) {
		// $color = ($type == 'success' ? "green" : "red");
		echo '<div id="feedback" class="row-fluid" style="color: '.$color.'; visibility:'.$visibility.';">';
		echo '<div id="msg" class="col-md-12">';
		echo '<h4>'.$msg.'</h4>';
		echo '</div>';
		echo '</div> <!-- row -->';

}

function ipGrabber() {
	$ip = getenv('HTTP_CLIENT_IP')?:
	getenv('HTTP_X_FORWARDED_FOR')?:
	getenv('HTTP_X_FORWARDED')?:
	getenv('HTTP_FORWARDED_FOR')?:
	getenv('HTTP_FORWARDED')?:
	getenv('REMOTE_ADDR');
	return $ip;
}


function locationSetter() {
	if (!isset($_COOKIE['location'])) {
		setcookie('location', 'NOPE');
	}
}

function userSetter() {
	setcookie('userType', 'NOPE');
}

function makeDropdown($type, $please_select=True) {	
	// $location_array loaded from config.php
	if ($_COOKIE['location'] == "LAW" && $type == "law") {
		global $user_array;
		$array = $user_array;
	}
	else {
		global $location_array;
		$array = $location_array;
	}

	if ($please_select == False){
		unset($array['NOPE']);
	}

	foreach ($array as $key => $value) {
		if($key == $_COOKIE['location']) {
			echo '<option value="'.$key.'" selected>'.$value.'</option>';
		}
		else {
			echo '<option value="'.$key.'">'.$value.'</option>';
		}
	}

}


// function to report 8am - 11pm table rows showing stats
function statsGraph($link, $context, $current_edit_location, $graph_date){			

	// get location
	$location = $_COOKIE['location'];	


	# main index, current time
	if ($context == "index") {
		$query = "SELECT HOUR(timestamp) AS hour, ref_type FROM `ref_stats` WHERE DATE(timestamp)=DATE(NOW()) AND location = '$location' ORDER BY ref_type";
		$result = mysqli_query($link, $query) or trigger_error(mysqli_error()); 	
	}

	# crud, based on $graph_date
	if ($context == "crud") {
		if ($current_edit_location != "ALL") {
			$location_filter = "AND location = '$current_edit_location'";			
		}		
		$query = "SELECT HOUR(timestamp) AS hour, ref_type FROM `ref_stats` WHERE DATE_FORMAT(timestamp, '%m %d %Y') = '$graph_date' $location_filter ORDER BY ref_type";				
		$result = mysqli_query($link, $query) or trigger_error(mysqli_error()); 	
	}	

	// prepare results array
	$shown_hours = array(
		8 => array("8am",""),
		9 => array("9am",""),
		10 => array("10am",""),
		11 => array("11am",""),
		12 => array("12pm",""),
		13 => array("1pm",""),
		14 => array("2pm",""),
		15 => array("3pm",""),
		16 => array("4pm",""),
		17 => array("5pm",""),
		18 => array("6pm",""),
		19 => array("7pm",""),
		20 => array("8pm",""),
		21 => array("9pm",""),
		22 => array("10pm",""),
		23 => array("11pm","")		
		);			

	// update graph marks for each hour returned
	while($row = mysqli_fetch_array($result)) {
		$shown_hours[(int)$row['hour']][1].= "<span class='ref_type_{$row['ref_type']}'>&#9608;</span>"; 		
	}

	// push to page
	foreach($shown_hours as $hour){
		echo "<tr>";
		echo "<td class='time_col'>{$hour[0]}</td>";
		echo "<td><strong>{$hour[1]}</strong></td>";
		echo "</tr>";
	}
}

// reference type
$ref_type_hash = array(
	1 => "Directional",
	2 => "Brief",
	3 => "Extended"
)




























?>