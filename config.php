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
$reference_ip_list = array(
	"141.217.54.36", // GH PC
);




?>