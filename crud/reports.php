<?php
include('header.php');
include('../inc/functions.php');

// get current page					
if (isset($_REQUEST['page'])) {
	$page = $_REQUEST['page'];
}
else { 
	$page = 0;						
}	

// establish editing location, or select all
if (isset($_REQUEST['edit_location']) && $_REQUEST['edit_location'] == "ALL"){
	$location_where = "location = ANY(select location from ref_stats)";
}
elseif (isset($_REQUEST['edit_location'])) {
	$location_where = "location = '{$_REQUEST['edit_location']}'";
}
elseif (isset($_COOKIE['location']) && $_COOKIE['location'] != "NOPE"){	
	// default to current location
	$location_where = "location = '{$_COOKIE['location']}'";
}
else {
	$_REQUEST['edit_location'] = "ALL";
	$location_where = "location = ANY(select location from ref_stats)";	
}					

// perform query
$query = "SELECT id, ref_type, location, user_group, ip, DATE_FORMAT(timestamp, '%r') AS print_timestamp, timestamp AS ordering_timestamp FROM ref_stats WHERE DATE(timestamp) = DATE_ADD(CURDATE(), INTERVAL $page DAY) AND $location_where ORDER BY ordering_timestamp DESC";
$result = mysqli_query($link, $query) or trigger_error(mysqli_error());
$total_day_stats = mysqli_num_rows($result);
$results_date = date('l\, m\-j\-y', strtotime( ($page)." days" ));
$graph_date = date('m d Y', strtotime( ($page)." days" ));




?>

		<!-- data view type -->
		<div class="row">
			<div class="col-md-6">
				<h3>Export Data</h3>		
				<p>Click the button to export data in tab-delimited format.  This format is suitable and openable by Microsoft Excel.</p>
				<a class="btn btn-WSUgreen" href="export.php">Export</a></p>	
			</div>
			<div class="col-md-6">
				<h3>QuickStats</h3>		
				<p>QuickStats is designed to provide a quick overview of reference transactions for a given location and date range.</p>
				<a class="btn btn-WSUgreen" href="quickstats.php">QuickStats</a></p>	
			</div>				
		</div>	


	<body>
</html>
