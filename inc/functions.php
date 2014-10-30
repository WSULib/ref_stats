<?php

include($_SERVER['DOCUMENT_ROOT'].'inc/dbs/ref_stats_config.php');

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


function makeDropdown() {
	$array = array(
		"NOPE" => "Please Select Your Location",
		"PK" => "Purdy Kresge Library",
		"UGL" => "Undergraduate Library",
		"LAW" => "Neef Law Library",
		"MED" => "Shiffman Medical Library"
		);
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
function statsGraph($link){			

	// get location
	$location = $_COOKIE['location'];	

	$query = "SELECT HOUR(timestamp) AS hour, COUNT(ref_type) AS ref_count FROM `ref_stats` WHERE DATE(timestamp)=DATE(NOW()) AND location = '$location' GROUP BY HOUR(timestamp)";	

	$result = mysqli_query($link, $query) or trigger_error(mysqli_error()); 	

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
	while($row = mysqli_fetch_array($result)){
		$total_count = (int) $row['ref_count'];		
		$print_marks = "";
		$count = 0;
		do {
		    $print_marks = $print_marks."-";
		    $count++;
		} while ( $count < $total_count && $count < 20);		
		if ($total_count > 20){
			$print_marks = $print_marks."(+)";
		}
		$shown_hours[ (int) $row['hour'] ][1] = $print_marks;
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
	2 => "Brief Reference",
	3 => "Extended Reference"
)




























?>