<?php

/*
	configuration file for RefStats
	Production - updated 11/19/2014
*/

// location array used to populate location dropdowns around app
$location_array = array(
	"NOPE" => "Please Select Your Location",
	"PK1" => "Purdy Kresge Library - Desk 1",
	"PK2" => "Purdy Kresge Library - Desk 2",
	"UGL" => "Undergraduate Library",
	"LAW" => "Neef Law Library",
	"MED" => "Shiffman Medical Library"
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
	"MED" => array(
		"NOPE" => "Please Select Your User",
		"GOOBER" => "Of the Goober Variety",
		"TRONIC" => "Tronic in nature",
		"HORSE" => "Ubiquitous Horse",
	)	
);

?>