<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
	configuration file for RefStats
	TEMPLATE - updated 03/2016

	## 03/2016 ##
	Expanding "ref_stats" to include information from other locations.
	As such, creating creating new arrays in config that will power things.
	
	Arrays:

	'groups':
		- locations - array of locations included in that group
		- buttons and values - array of buttons and values

	'ip_whitelist':
		- ip address as key, groups as array
	
	'location_array'
		- created on the fly from user ip, resulting in locations that user can access

	Maintaing other arrays such as 'simple_location_array' and 'user_arrays'.

	Moved 'ref_type_hash' from functions.php to here, renamed 'transaction_type_hash'.
*/

// location is in 'inc/dbs/'
$config_file = "ref_stats_config.php";


// Group array
// Contains array of Groups.  Within each group, are associated locations and buttons.
$groups_array = array(
	"UGL_GROUP" => array(
		"locations" => array(
			"UGL" => "Undergraduate Library"
		),
		"buttons" => array(1,2,3,5,6,7,8,9,10,11,12),
		"open" => False
	),
	"PKINFO_GROUP" => array(
		"locations" => array(
			"PK1" => "Purdy Kresge Library - Desk 1",
			"PK2" => "Purdy Kresge Library - Desk 2",
		),
		"buttons" => array(1,2,3,8,9,10),
		"open" => False
	),
	"PKCIRC_GROUP" => array(
		"locations" => array(
			"PKCIRC" => "Purdy Kresge Library - Circulation"
		),
		"buttons" => array(5,6,7,8,9,10),
		"open" => False
	),
	"MED_GROUP" => array(
		"locations" => array(
			"MED_LIB" => "Shiffman Medical Library",
			"MED_PHARM" => "Applebaum Learning Resource Center"
		),
		"buttons" => array(1,2,3,4),
		"open" => True
	),
	"LAW_GROUP" => array(
		"locations" => array(
			"LAW" => "Neef Law Library"
		),
		"buttons" => array(1,2,3),
		"open" => True
	)
);

// IP whitelist
// Contains IP as key for array of groups this IP is allowed for
$ip_whitelist = array(

	// UGL
	"x.y.z.a" => array('UGL_GROUP'),
	"x.y.z.a" => array('UGL_GROUP'),
	"x.y.z.a" => array('UGL_GROUP'),
	"x.y.z.a" => array('UGL_GROUP'),
	"x.y.z.a" => array('UGL_GROUP'),
	"x.y.z.a" => array('UGL_GROUP'),

	// PKINFO_GROUP
	"x.y.z.a" => array('PKINFO_GROUP'),
	"x.y.z.a" => array('PKINFO_GROUP'),

	// PKCIRC_GROUP
	"x.y.z.a" => array('PKCIRC_GROUP'),
	"x.y.z.a" => array('PKCIRC_GROUP'),
	"x.y.z.a" => array('PKCIRC_GROUP'),

	//ADMIN
	"x.y.z.a" => array('ALL'),
	"x.y.z.a" => array('ALL'),
	"x.y.z.a" => array('ALL')

);


// location array
// Generated on-the-fly from user IP.  If IP not in list, ascribe to open groups (e.g. Med and Law)
function generateLocationArray($groups_array, $ip_whitelist) {
	
	// vars	
	$user_ip = $_SERVER['REMOTE_ADDR'];
	$user_locations = array();

	// prime with `NOPE` location
	$user_locations['NOPE'] = "Please Select Your Location";

	# if IP in ip_whitelist
	if (array_key_exists($user_ip, $ip_whitelist)) {
		$user_groups = $ip_whitelist[$user_ip];
		// echo "IP found";
		// loop through groups
		foreach ($groups_array as $key => $value) {
			// check group affiliation
			if (in_array($key, $user_groups) || in_array("ALL", $user_groups) ){
				// loop through locations
				foreach ($groups_array[$key]['locations'] as $key => $value) {
					// push to $user_locations
					$user_locations[$key] = $value;
				}	
			}	
		}
	}

	# else, ascribe to "open" groups
	// loop through groups
	else {
		// echo "IP *not* found";
		foreach ($groups_array as $key => $value) {			
			if ($groups_array[$key]['open'] == True){				
				// loop through locations
				foreach ($groups_array[$key]['locations'] as $key => $value) {
					// push to $user_locations					
					$user_locations[$key] = $value;
				}	
			}
		}	
	}
	
	return $user_locations;

}

// finally, set $location_array
$location_array = generateLocationArray($groups_array, $ip_whitelist);


// location array used to populate location dropdowns around app
$simple_location_array = array();
foreach (array_keys($location_array) as $location) {
	if ($location != "NOPE") {
		array_push($simple_location_array, $location);
	}
}
	

// user array used to populate location dropdowns around app
$user_arrays = array(
	"LAW" => array(
		"NOPE" => "Please Select Your User",
		"WLF" => "WSU Law Faculty",
		"OTF" => "Other Faculty",
		"WLS" => "WSU Law Students",
		"OTS" => "Other Students",
		"WSA" => "WSU Administration/Staff",
		"LGP" => "Legal Professionals",
		"COP" => "Community Patrons",
	),
	"MED_LIB" => array(
		"NOPE" => "Please Select Your User",
		"DMC" => "Detroit Medical Center (DMC)",
		"COM" => "Community",
		"WSU" => "Wayne State Affiliated",
	),
	"MED_PHARM" => array(
		"NOPE" => "Please Select Your User",
		"DMC" => "Detroit Medical Center (DMC)",
		"COM" => "Community",
		"WSU" => "Wayne State Affiliated",
	)	
);


// Transaction Type Hash
// Translates transaction numbers from DB to human readable form
$transaction_type_hash = array(

	// Reference
	1 => "Directional",
	2 => "Brief",
	3 => "Extended",
	4 => "Consultation",

	// Circ
	5 => "General Circ",
	6 => "Reserves Circ",
	7 => "ILL / MEL Circ",

	// Tech
	8 => "Print / Copy / Scan",
	9 => "Desktop Support",
	10 => "BYOD Support",
	11 => "Staff Support",
	12 => "Classroom Support",
);


// DEBUG
// echo "<p>user IP</p>";
// echo $_SERVER['REMOTE_ADDR'];
// echo "<p>location_array</p>";
// print_r($location_array);
// echo "<p>simple_location_array</p>";
// print_r($simple_location_array);



/* MySQL View Table for Reports (Feels backwards, but this works for pushing "PK1" or "PK2" to "PK" for view table):
CREATE VIEW ref_stats_reports (id, ref_type, detailed_location, location, user_group, ip, timestamp) AS SELECT id, ref_type, location AS detailed_location, CASE location WHEN location NOT IN ('PK1','PK2') THEN 'PK' ELSE location END, user_group, ip, timestamp FROM ref_stats;
*/


?>
