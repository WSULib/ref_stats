<?php
include('../inc/functions.php');

// grab all params from quick reports
$_REQUEST = json_decode($_REQUEST['params'],true);


// default to ALL
if ( isset($_REQUEST['locations']) && $_REQUEST['locations'] == array("ALL")){
	$location_where = "location = ANY(select location from ref_stats)";
}
elseif ( isset($_REQUEST['locations']) ) {
	$location_where = "location IN ('".implode("', '",$_REQUEST['locations'])."')";
	// adjust for combined locations
	if ( in_array("PK_COMB", $_REQUEST['locations'])){
		$location_where = str_replace("'PK_COMB'", "'PK1','PK2'", $location_where);
	}
	if ( in_array("MAIN_CAMPUS", $_REQUEST['locations'])){
		$location_where = str_replace("'MAIN_CAMPUS'", "'PK1','PK2','UGL'", $location_where);
	}
}
else {
	$location_where = "location = {$_COOKIE['location']}";
}	


// get date limitiers
$date_start = date("Y-m-d", strtotime($_REQUEST['date_start']));
$date_end = date("Y-m-d", strtotime($_REQUEST['date_end']));


// All transactions in date range (appropriate for csv export)
$full_query = "SELECT ref_type, location, user_group, DAYNAME(timestamp) as day_of_week, timestamp AS ordering_timestamp FROM ref_stats WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' AND $location_where ORDER BY ordering_timestamp DESC";
$result = mysqli_query($link, $full_query) or trigger_error(mysqli_error());

$fp = fopen('php://output', 'w');
if ($fp && $result) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="export.csv"');
    
    // write column names
    $fields = mysqli_num_fields ( $result );
	for ( $i = 0; $i < $fields; $i++ )
	{
	    $header .= mysqli_fetch_field_direct($result, $i)->name . ",";
	}
	echo $header."\n";
    while ($row = $result->fetch_assoc()) {
		$row['ref_type'] = $ref_type_hash[$row['ref_type']];
		fputcsv($fp, array_values($row));
	}
    die;
}

?>