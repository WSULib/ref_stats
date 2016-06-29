<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

/*
	configuration file for RefStats
	Production - updated 03/2016

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
			"UGL" => "UGL Integrated Desk"
		),
		"buttons" => array(1,2,3,5,6,7,8,9,10,11,12),
		"open" => True
	),
	"PKINFO_GROUP" => array(
		"locations" => array(
			"PK1" => "Purdy Reference Desk 1 (South)",
			"PK2" => "Purdy Reference Desk 2 (North)",
		),
		"buttons" => array(1,2,3,8,9,10),
		"open" => True
	),
	"PKCIRC_GROUP" => array(
		"locations" => array(
			"PKCIRC" => "Purdy Kresge Library - Circulation"
		),
		"buttons" => array(5,6,7,8,9,10),
		"open" => True
	),
	"MED_GROUP" => array(
		"locations" => array(
			"MED_LIB" => "Shiffman Medical Library",
			"MED_PHARM" => "Applebaum Learning Resource Center"
		),
		"buttons" => array(1,2,3,4),
		"open" => True
	),
	"LAWINFO_GROUP" => array(
		"locations" => array(
			"LAWINFO" => "Neef Law Library - Reference"
		),
		"buttons" => array(1,2,3,6,8,9,10),
		"open" => True
	),
	"LAWCIRC_GROUP" => array(
		"locations" => array(
			"LAWCIRC" => "Neef Law Library - Circulation"
		),
		"buttons" => array(5,6,7,8,9,10),
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
	"141.217.54.95" => array('ALL'), // GH
	"x.y.z.a" => array('ALL'), // GH
	"x.y.z.a" => array('ALL'), // CH	
	"x.y.z.a" => array('PKCIRC_GROUP', 'UGL_GROUP') // CH

);


// location array
// Generated on-the-fly from user IP.  If IP not in list, ascribe to open groups (e.g. Med and Law)
function generateLocationArray($groups_array, $ip_whitelist) {
	global $user_groups;
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
	"LAWINFO" => array(
		"NOPE" => "Please Select Your User",
		"WLF" => "WSU Law Faculty",
		"OTF" => "Other Faculty",
		"WLS" => "WSU Law Students",
		"OTS" => "Other Students",
		"WSA" => "WSU Administration/Staff",
		"LGP" => "Legal Professionals",
		"COP" => "Community Patrons",
	),
	"LAWCIRC" => array(
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
        1 => array("Directional","ref","<div><p><strong>Defined:</strong>  Involves the logistical use of the library, campus or the website. Includes questions on policies and procedures.</p><p><strong>Examples:</strong><ul><li>Where is a department located?</li><li>How late are you open today?</li><li>What is the URL for the library homepage?</li><li>Do you have vending machines in this building?</li><li>How does course reserves work?</li></ul></p></div>"),
        2 => array("Brief Reference","ref","<div><p><strong>Defined:</strong> Involves the knowledge, use or instruction of one or more information sources
(catalog, library homepage, print books, databases, etc.) and lasts less than 3 minutes.</p><p><strong>Examples:</strong><ul><li>Do you own this book?</li><li>How do I cite a web page using APA style?</li><li>Where should I go to find resources relating to social work?</li></ul></p></div>"),
        3 => array("Extended Reference","ref","<div><p><strong>Defined:</strong>  Involves the knowledge, use or instruction of one or more information sources
(catalog, library homepage, print books, databases, etc.) and lasts more than 3 minutes.</p><p><strong>Examples:</strong><ul><li>I need 5 literary criticism articles.</li><li>Can you help me locate this specific inventory for PTSD?</li><li>I need help finding historical images of a particular building.</li></ul></p></div>"),
        4 => array("Consultation","ref","<div><p><strong>Defined:</strong> Definition not available.</p><p><strong>Examples:</strong><ul><!-- Examples here --></ul></p></div>"),
        
        // Circ
        5 => array("General Circ","circ","<div><p><strong>Defined:</strong> Pertaining to general circulation functions.</p><p><strong>Examples:</strong><ul><li>General inquiries about Circulation policies and procedures.</li><li>Check in/out materials from the general stacks.</li><li>Collect Library fines.</li><li>Creating/updating patron records.</li></ul></p></div>"),
        6 => array("Reserves Circ","circ","<div><p><strong>Defined:</strong> Pertaining to Reserve items.</p><p><strong>Examples:</strong><ul><li>General inquiries about Reserve materials.</li><li>Check in/out items from Reserves collection.</li><li>Process request for items to be on Reserve</li></ul></p></div>"),
        7 => array("ILL / MEL Circ","circ","<div><p><strong>Defined:</strong> Pertaining to ILL / MEL items.</p><p><strong>Examples:</strong><ul><li>General inquiries about ILL / MEL materials.</li><li>Check in/out ILL / MEL materials.</li><li>Assist with requesting ILL / MEL materials.</li></ul></p></div>"),
        
        // Tech
        8 => array("Print / Copy / Scan","tech","<div><p><strong>Defined:</strong> Pertaining to public printing, copying, scanning services.</p><p><strong>Examples:</strong><ul><li>General inquiries about printing/copying/scanning services.</li><li>Assist with printing/copying/scanning process.</li><li>Submit a maintenance request</li></ul></p></div>"),
        9 => array("Desktop Support","tech","<div><p><strong>Defined:</strong> Pertaining to public computing services.</p><p><strong>Examples:</strong><ul><li>General inquiries about public computing.</li><li>Assist with WSU related applications (i.e. Academica, Blackboard).</li><li>Assist with Microsoft Office applications or other specialty software applications.</li></ul></p></div>"),
        10 => array("BYOD Support","tech","<div><p><strong>Defined:</strong> Pertaining to personal mobile devices.</p><p><strong>Examples:</strong><ul><li>General inquiries about personal mobile devices.</li><li>Assist with accessing/configuring mobile device with WSU wireless.</li></ul></p></div>"),
        11 => array("Staff Support","tech","<div><p><strong>Defined:</strong> Pertaining to assisting Library System staff.</p><p><strong>Examples:</strong><ul><li>Assist with inquiries regarding desktop support for Library System staff.</li><li>Submit a maintenance request.</li></ul></p></div>"),
        12 => array("Classroom Support","tech","<div><p><strong>Defined:</strong> Pertaining to assisting Library System staff.</p><p><strong>Examples:</strong><ul><li>Assist with inquiries regarding classroom technology.</li><li>Assist with inquiries regarding lecture capture technology.</li><li>Submit a maintenance request.</li></ul></p></div>")
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
