<?php

include_once(__DIR__."/../config.php"); //imports relative to "inc/functions.php"
include($_SERVER['DOCUMENT_ROOT'].'inc/dbs/'.$config_file);

function reporter($color, $msg, $visibility) {
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
	setcookie('user_group', 'NOPE');
}


# function to create location dropdown selections
function makeLocationDropdown($please_select=True, $preset) {	
	
	# get location array from config.php
	global $location_array;
	$array = $location_array;

	if ($please_select == False){
		unset($array['NOPE']);
	}

	// $preset overrides even Cookie location
	foreach ($array as $key => $value) {
		if ($key == $preset) {			
			echo '<option value="'.$key.'" selected>'.$value.'</option>';
		}			
		else {
			echo '<option value="'.$key.'">'.$value.'</option>';
		}
	}
}

# function to create location checkbox grid
function makeCheckboxGrid($please_select=True, $preset_array) {	
	
	# get location array from config.php
	global $location_array;
	$array = $location_array;

	if ($please_select == False){
		unset($array['NOPE']);
	}

	// $preset overrides even Cookie location
	foreach ($array as $key => $value) {
		$location_exceptions = array("PK1","PK2");
		if ( !in_array($key, $location_exceptions) ){
			if ( in_array($key, $preset_array) ){			
				echo '<li><div class="checkbox"><label><input class="locationcheckbox" type="checkbox" onclick="$(\'#ALL_checkbox\').not(this).prop(\'checked\', false);" name="locations[]" value="'.$key.'" checked> '.$value.'</label></div></li>';
			}			
			else {
				echo '<li><div class="checkbox"><label><input class="locationcheckbox" type="checkbox" onclick="$(\'#ALL_checkbox\').not(this).prop(\'checked\', false);" name="locations[]" value="'.$key.'"> '.$value.'</label></div></li>';
			}	
		}
	}
}

# function to create days of week checkbox grid
function makeDOWCheckboxGrid($preset_array) {	

	# get location array from config.php	
	$array = array(
		"2" => "Monday",
		"3" => "Tuesday",
		"4" => "Wednesday",
		"5" => "Thursday",
		"6" => "Friday",
		"7" => "Saturday",
		"1" => "Sunday"
	);	

	// $preset overrides even Cookie location
	if ($preset_array == NULL){
		foreach ($array as $key => $value) {		
			echo '<li><div class="checkbox"><label><input type="checkbox" name="dow[]" value="'.$key.'" checked> '.$value.'</label></div></li>';
		}
	}
	else {
		foreach ($array as $key => $value) {		
			if ( in_array($key, $preset_array) ){			
				echo '<li><div class="checkbox"><label><input type="checkbox" name="dow[]" value="'.$key.'" checked> '.$value.'</label></div></li>';
			}			
			else {
				echo '<li><div class="checkbox"><label><input type="checkbox" name="dow[]" value="'.$key.'"> '.$value.'</label></div></li>';
			}
		}	
	}
	
}


# function to create user checkbox grid
function makeUserCheckboxGrid($preset_array) {	

	# get location array from config.php	
	$array = array(
		"WLF" => "WSU Law Faculty",
		"OTF" => "Other Faculty",
		"WLS" => "WSU Law Students",
		"OTS" => "Other Students",
		"WSA" => "WSU Administration/Staff",
		"LGP" => "Legal Professionals",
		"COP" => "Community Patrons",
		"DMC" => "Detroit Medical Center (DMC)",
		"COM" => "Community",
		"WSU" => "Wayne State Affiliated"
	);	

	// $preset overrides even Cookie location
	if ($preset_array == NULL){
		foreach ($array as $key => $value) {		
			echo '<li><div class="checkbox"><label><input onclick="$(\'#ALL_user_checkbox\').prop(\'checked\', false);"class="usercheckbox" type="checkbox" name="user[]" value="'.$key.'"> '.$value.'</label></div></li>';
		}
	}
	else {
		foreach ($array as $key => $value) {		
			if ( in_array($key, $preset_array) ){			
				echo '<li><div class="checkbox"><label><input onclick="$(\'#ALL_user_checkbox\').prop(\'checked\', false);"class="usercheckbox" type="checkbox" name="user[]" value="'.$key.'" checked> '.$value.'</label></div></li>';
			}			
			else {
				echo '<li><div class="checkbox"><label><input onclick="$(\'#ALL_user_checkbox\').prop(\'checked\', false);"class="usercheckbox" type="checkbox" name="user[]" value="'.$key.'"> '.$value.'</label></div></li>';
			}
		}	
	}
	
}



# function to create user dropdown selections
function makeUserDropdown($please_select=True, $preset) {	
	
	# get user array from config.php
	global $user_arrays;
	$array = $user_arrays[$_COOKIE['location']];	

	if ($please_select == False){
		unset($array['NOPE']);
	}

	foreach ($array as $key => $value) {
		if($key == $preset) {
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
		23 => array("11pm",""),
		0 => array("12am",""),
		1 => array("1am",""),
		2 => array("2am",""),
		3 => array("3am",""),
		4 => array("4am",""),
		5 => array("5am",""),
		6 => array("6am",""),
		7 => array("7am","")
	);

	// strip after hours if not UGL
	if ($location != "UGL") {
		foreach (range(1, 7) as $number) {
		    unset($shown_hours[$number]);
		}
	}

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

function startsWith($haystack, $needle) {	
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
    return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}


function autoSelectLocation() {
	// Sets the location automatically if there's only one location to choose from
	// Does this by adding the location ID to $_POST['location'] and $_COOKIE['location']
	# get location array from config.php
	global $location_array;

	// make a temp variable from which to check $location array
	$temp_array = $location_array;
	unset($temp_array['NOPE']);

	if (count($temp_array) == 1) {
		// make sure this check hasn't happened already
		if ($_COOKIE['location'] == key($temp_array)) {
			return;
		}
		else {
			$_COOKIE['location'] = key($temp_array);
			$_SESSION['result'] == "location";
			return;
		}
	}
}

function authenticator() {
	global $groups_array;
	global $user_groups;
	$location = '';

	// Locate the location in the uber-variable that has all our location, group, and button info
	foreach($groups_array as $gkey => $gvalue) {
		// now go get the correct buttons for the group
		if(array_key_exists($_COOKIE['location'], $gvalue['locations'])) {
			$location = $gkey;
		}
	}

	// if they're set to ADMIN group 'ALL'
	if(in_array('ALL', $user_groups)) {
		return True;
	}

	// if they have the user group enabled for the location they want
	elseif(in_array($location, $user_groups)) {
		return True;
	}
	// if that location is set to be open
	elseif($groups_array[$location]['open']) {
		return True;
	}
	
	// nope
	else {
		return False;
	}

}


function buttonMaker2($transaction_type_hash) {
	// Makes the buttons needed according to the locations it has available in $location_array
	global $complete_location_array;
	global $user_groups;
	global $location_array;
	$user_buttons = array();
	// Locate the location in the uber-variable that has all our location, group, and button info
	foreach($complete_location_array as $key => $value) {
		// return $key;
		// now go get the correct buttons for the group
		if(array_key_exists($_COOKIE['location'], $complete_location_array[$key])) {
			// make sure you're allowed to see these buttons
			if (authenticator()) {
				$user_buttons = $value['buttons'];	
			}
			else {
				// Not allowed. No buttons. Do not pass go; Do not collection $200
				return;
			}
		}
	}

	// Makes buttons according to the array of buttons provided by $user_buttons
	foreach ($user_buttons as $button) {
		$value = $transaction_type_hash[$button][0];
		$ref_group = $transaction_type_hash[$button][1];
		$help_html = $transaction_type_hash[$button][2];
		$buttons[] = <<<"EOF"
			<div class="row">
				<div class="col-md-12">
					<div class="ref_button_wrapper">
						<form action="" method="POST">
							<input name="type" type="number" value="$button"></input>
							<button type="submit" class="btn ref_type_button btn-primary btn-block btn-lg $ref_group">$value</button>
						</form>
						<div id="button_help_$button" class="button_help_html">$help_html</div>
					</div>
					<div class="button_help_icon" onclick="$('#button_help_$button').slideToggle(); return false;">
						<span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
					</div>
				</div>
			</div>
EOF;
			}

	return $buttons;
}



function buttonMaker($transaction_type_hash) {
	// Makes the buttons needed according to the locations it has available in $location_array
	global $groups_array;
	global $user_groups;
	global $location_array;
	$user_buttons = array();

	// Locate the location in the uber-variable that has all our location, group, and button info
	foreach($groups_array as $gkey => $gvalue) {
		// now go get the correct buttons for the group
		if(array_key_exists($_COOKIE['location'], $gvalue['locations'])) {
			// make sure you're allowed to see these buttons
			if (authenticator()) {
				$user_buttons = $gvalue['buttons'];	
			}
			else {
				// Not allowed. No buttons. Do not pass go; Do not collection $200
				return;
			}
		}
	}

	// Makes buttons according to the array of buttons provided by $user_buttons
	foreach ($user_buttons as $button) {
		$value = $transaction_type_hash[$button][0];
		$ref_group = $transaction_type_hash[$button][1];
		$help_html = $transaction_type_hash[$button][2];
		$buttons[] = <<<"EOF"
			<div class="row">
				<div class="col-md-12">
					<div class="ref_button_wrapper">
						<form action="" method="POST">
							<input name="type" type="number" value="$button"></input>
							<button type="submit" class="btn ref_type_button btn-primary btn-block btn-lg $ref_group">$value</button>
						</form>
						<div id="button_help_$button" class="button_help_html">$help_html</div>
					</div>
					<div class="button_help_icon" onclick="$('#button_help_$button').slideToggle(); return false;">
						<span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
					</div>
				</div>
			</div>
EOF;
			}

	return $buttons;
}


function buttonMakerForm2($transaction_type_hash,$row) {
	// Makes the buttons needed according to the locations it has available in $location_array
	global $complete_location_array;
	global $user_groups;
	global $location_array;
	$user_buttons = array();

	// Locate the location in the uber-variable that has all our location, group, and button info
	foreach($complete_location_array as $key => $value) {
		// now go get the correct buttons for the group
		if(array_key_exists($_COOKIE['location'], $complete_location_array[$key])) {
			// make sure you're allowed to see these buttons
			if (authenticator()) {
				$user_buttons = $value['buttons'];
			}
			else {
				// Not allowed. No buttons. Do not pass go; Do not collection $200
				return;
			}
		}
	}

	// Makes buttons according to the array of buttons provided by $user_buttons
	foreach ($user_buttons as $button) {
		$value = $transaction_type_hash[$button][0];
		$ref_group = $transaction_type_hash[$button][1];
		if ($row['ref_type'] == $button){
			$checked = 'checked="checked"';	
		}
		else {
			$checked = "";	
		}		
		$buttons[] = <<<"EOF"
		<li>
			<div class='radio'>
				<label>
					<input type="radio" name="ref_type" value="$button" $checked>
						<span class="btn ref_type_button btn-primary btn-block btn-lg $ref_group">$value</span>
					</input>
				</label>
			<div>
		</li>
EOF;
			}

	return $buttons;
}




function buttonMakerForm($transaction_type_hash,$row) {
	// Makes the buttons needed according to the locations it has available in $location_array
	global $groups_array;
	global $user_groups;
	global $location_array;
	$user_buttons = array();

	// Locate the location in the uber-variable that has all our location, group, and button info
	foreach($groups_array as $gkey => $gvalue) {
		// now go get the correct buttons for the group
		if(array_key_exists($_COOKIE['location'], $gvalue['locations'])) {
			// make sure you're allowed to see these buttons
			if (authenticator()) {
				$user_buttons = $gvalue['buttons'];	
			}
			else {
				// Not allowed. No buttons. Do not pass go; Do not collection $200
				return;
			}
		}
	}

	// Makes buttons according to the array of buttons provided by $user_buttons
	foreach ($user_buttons as $button) {
		$value = $transaction_type_hash[$button][0];
		$ref_group = $transaction_type_hash[$button][1];
		if ($row['ref_type'] == $button){
			$checked = 'checked="checked"';	
		}
		else {
			$checked = "";	
		}		
		$buttons[] = <<<"EOF"
		<li>
			<div class='radio'>
				<label>
					<input type="radio" name="ref_type" value="$button" $checked>
						<span class="btn ref_type_button btn-primary btn-block btn-lg $ref_group">$value</span>
					</input>
				</label>
			<div>
		</li>
EOF;
			}

	return $buttons;
}


?>
