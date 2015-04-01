<?php

/*
	configuration file for RefStats
	Production - updated 11/19/2014
*/

// location is in 'inc/dbs/'
$config_file = "ref_stats_beta_config.php";

// location array used to populate location dropdowns around app
$location_array = array(
	"NOPE" => "Please Select Your Location",
	"PK1" => "Purdy Kresge Library - Desk 1",
	"PK2" => "Purdy Kresge Library - Desk 2",
	"UGL" => "Undergraduate Library",
	"LAW" => "Neef Law Library",
	"MED_LIB" => "Shiffman Medical Library",
	"MED_PHARM" => "Applebaum Learning Resource Center"
);

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

// wide-open, reference locations
$ip_white_list = array(
	"141.217.54.36", // GH 
	// "141.217.54.38", // GH 
	"141.217.172.161",
	"141.217.175.115",
	"141.217.175.55",
	"141.217.175.58",
	"141.217.208.25",
	"141.217.54.89", // CH
	"141.217.84.120",
	"141.217.84.130",
	"141.217.84.146",
	"141.217.84.164",
	"141.217.84.165",
	"141.217.84.182",
	"141.217.84.183",
	"141.217.84.187",
	"141.217.84.239",
	"141.217.84.44",
	"141.217.98.26",
	"146.9.153.130",
	"146.9.153.138",
	"146.9.153.150",
	"146.9.153.151",
	"146.9.153.172",
	"146.9.153.191",
	"146.9.153.192",
	"35.16.92.182",
	"50.249.166.130"
);

?>