<?php
include('header.php');
include('../inc/functions.php');

if (isset($_REQUEST['submitted'])){	

	// establish reporting location, or select all (note: contains 'AND' from actual query to avoid altogether)
	/* ----------------------------------------------------------------------------------------------------- */

	$selected_locations = array();
	// default to ALL
	if ( isset($_REQUEST['locations']) && $_REQUEST['locations'] == array("ALL")){

		# grab from MySQL
		$all_locations_query = "SELECT DISTINCT(location) AS location FROM ref_stats_reports";
		$all_locations_result = mysqli_query($link, $all_locations_query) or trigger_error(mysqli_error());
		$selected_locations = array();
		while ($row = mysqli_fetch_assoc($all_locations_result)) {
			if ($row['location'] != ""){
		    	array_push($selected_locations, $row['location']);
			}
		}		

	}

	elseif ( isset($_REQUEST['locations']) ) {
		
		// prepare SQL clause
		$location_where = "AND location IN ('".implode("', '",$_REQUEST['locations'])."')";

		// // prepare selected_locations
		foreach($_REQUEST['locations'] as $location){
			if ($location != "NOPE" && $location != "ALL" && $location != "MAIN_CAMPUS"){
				array_push($selected_locations, $location);
			}
		}

	}

	elseif (isset($_COOKIE['location'])) {
		$location_where = "AND location = {$_COOKIE['location']}";
		$selected_locations = array($_COOKIE['location']);
	}	

	else {
		$location_where = "";
	}

	// finish cleaning $selected_locations
	$selected_locations = array_unique($selected_locations);

	// get date limitiers
	$date_start = date("Y-m-d", strtotime($_REQUEST['date_start']));
	$date_end = date("Y-m-d", strtotime($_REQUEST['date_end']));

	// set days of the week	
	if ( isset($_REQUEST['dow']) ) {		
		// prepare SQL clause
		$dow = "AND DAYOFWEEK(timestamp) IN ('".implode("', '",$_REQUEST['dow'])."')";
	}
	else {
		$dow = "";
	}

	// set user
	if ( isset($_REQUEST['user']) && $_REQUEST['user'] != array("ALL") ) {		
		// prepare SQL clause
		$user = "AND user_group IN ('".implode("', '",$_REQUEST['user'])."')";
	}
	else {
		$user = "";
	}


	// All transactions in date range (appropriate for csv export)
	/* ----------------------------------------------------------------------------------------------------- */
	$full_query = "SELECT ref_type, location, user_group, DAYNAME(timestamp) as day_of_week, DATE(timestamp) AS simple_date, timestamp AS ordering_timestamp FROM ref_stats_reports WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' $location_where $dow $user ORDER BY ordering_timestamp DESC";
	// echo $full_query;
	$full_result = mysqli_query($link, $full_query) or trigger_error(mysqli_error());
	$total_date_range_results = mysqli_num_rows($full_result);
	
	
	// Query for Location Totals
	/* ----------------------------------------------------------------------------------------------------- */
	// create locations array for case calls
	$location_cases = '';
	foreach($selected_locations as $location) {
		$location_cases .= ", COUNT(CASE WHEN location = '$location' THEN DATE(timestamp) END) AS $location";
	}
	$locations_total_query = "SELECT DATE(timestamp) AS date_string $location_cases FROM ref_stats_reports WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' $location_where $dow $user GROUP BY DATE(timestamp) ORDER BY date_string DESC";
	// echo $locations_total_query;
	$locations_total_result = mysqli_query($link, $locations_total_query) or trigger_error(mysqli_error());

	// creat arrays for each location
	$locations_total_sorted = array();
	foreach($selected_locations as $location) {
		if (!array_key_exists($location, $locations_total_sorted)) {
			$locations_total_sorted[$location] = array();
		}
	}

	// loop through rows
	while ($row = mysqli_fetch_assoc($locations_total_result)) {	
		foreach($selected_locations as $location) {
			array_push($locations_total_sorted[$location], array( $row['date_string'], $row[$location] ) );
		}
		
	}

	// Transaction counts
	/* ----------------------------------------------------------------------------------------------------- */
	$type_query = "SELECT ref_type, COUNT(ref_type) AS ref_type_count FROM ref_stats_reports WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' $location_where $dow $user GROUP BY ref_type";
	$type_result = mysqli_query($link, $type_query) or trigger_error(mysqli_error());
	$type_counts = array ( 
		'Directional' => 0,
		'Brief Reference' => 0,
		'Extended Reference' => 0,
		'Consultation' => 0,
		'General Circ' => 0,
		'Reserves Circ' => 0,
		'ILL / MEL Circ' => 0,
		'Room Reservation' => 0,
		'Print / Copy / Scan' => 0,
		'Desktop Support' => 0,
		'BYOD Support' => 0,
		'Staff Support' => 0,
		'Classroom Support' => 0
	);
	while($row = mysqli_fetch_assoc($type_result)) {		
		$type_counts[$transaction_type_hash[$row['ref_type']][0]] = $row['ref_type_count'];
	}


	// User Group counts
	/* ----------------------------------------------------------------------------------------------------- */
	$user_group_query = "SELECT user_group, COUNT(user_group) AS user_group_count FROM ref_stats_reports WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' $location_where $dow $user GROUP BY user_group";
	$user_group_result = mysqli_query($link, $user_group_query) or trigger_error(mysqli_error());
	$user_group_counts = array();
	while($row = mysqli_fetch_assoc($user_group_result)) {		
		$user_group_counts[$user_hash[$row['user_group']]] = $row['user_group_count'];
	}
	if (array_key_exists('Please Select Your User', $user_group_counts)) {
		$user_group_counts['Undefined'] = $user_group_counts['Please Select Your User'];
		unset($user_group_counts['Please Select Your User']);	
	}
	

	// Busiest Day-of-the-week (dow)
	/* ----------------------------------------------------------------------------------------------------- */
	$dow_query = "SELECT DAYNAME(timestamp) AS dow_name, DAYOFWEEK(timestamp) AS dow_index, count(DAYOFWEEK(timestamp)) AS dow_count FROM ref_stats_reports WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' $location_where $dow $user GROUP BY dow_index ORDER BY dow_index;";
	$dow_result = mysqli_query($link, $dow_query) or trigger_error(mysqli_error());
	$dow_counts = array();
	while($row = mysqli_fetch_assoc($dow_result)) {		
		$dow_counts[$row['dow_name']] = $row['dow_count'];
	}


	// Busiest Hours
	/* ----------------------------------------------------------------------------------------------------- */
	$hour_query = "SELECT HOUR(timestamp) AS hour, 
		COUNT(CASE WHEN ref_type = 1 THEN ref_type END) AS Directional,
		COUNT(CASE WHEN ref_type = 2 THEN ref_type END) AS Brief,
		COUNT(CASE WHEN ref_type = 3 THEN ref_type END) AS Extended,
		COUNT(CASE WHEN ref_type = 4 THEN ref_type END) AS Consultation,
		COUNT(CASE WHEN ref_type = 5 THEN ref_type END) AS General_Circ,
		COUNT(CASE WHEN ref_type = 6 THEN ref_type END) AS Reserves_Circ,
		COUNT(CASE WHEN ref_type = 7 THEN ref_type END) AS ILL_MEL_Circ,
		COUNT(CASE WHEN ref_type = 8 THEN ref_type END) AS Room_Reservation,
		COUNT(CASE WHEN ref_type = 9 THEN ref_type END) AS Print_Copy_Scan,
		COUNT(CASE WHEN ref_type = 10 THEN ref_type END) AS Desktop_Support,
		COUNT(CASE WHEN ref_type = 11 THEN ref_type END) AS BYOD_Support,
		COUNT(CASE WHEN ref_type = 12 THEN ref_type END) AS Staff_Support,
		COUNT(CASE WHEN ref_type = 13 THEN ref_type END) AS Classroom_Support
		FROM ref_stats_reports WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' $location_where $dow $user GROUP BY hour;";
	$hour_result = mysqli_query($link, $hour_query) or trigger_error(mysqli_error());
	$hour_counts = array();
	while($row = mysqli_fetch_assoc($hour_result)) {

		// NEW
		$hour_counts[$row['hour']] = array();
		foreach ($row as $key => $value) {
			if ($value != 0) {
				$hour_counts[$row['hour']][$key] = $value;
			}
		}
	}


	// Busiest Single Days
	/* ----------------------------------------------------------------------------------------------------- */
	$single_query = "SELECT DAYNAME(timestamp) as dow_name, DATE(timestamp) AS date, count(ref_type) AS ref_count FROM ref_stats_reports WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' $location_where $dow GROUP BY date ORDER BY ref_count DESC limit 5;";
	$single_result = mysqli_query($link, $single_query) or trigger_error(mysqli_error());

	/* Data Explanations:
	Recommended to use json_encode() for each array to use with charts.js

	$full_result = MySQL result set for all queries
	$total_date_range_results = Total transactions from all locations.

	$locations_total_result_sorted = array of arrays, with daily counts from locations

	$location_totals = array of locations, each an array full of timestamp.  Can be used to create graph.

	$trans_result = MySQL result set for counts of transaction types
	$type_counts = An associative array from ALL locations - key is type as string, value is count from database

	$dow_counts = Associative array with Day-of-the-Week (dow) names and total transactions counts for that day

	$hour_counts = Nested Associative array with hours of the day, and numbers for transaction types

	$busiest_day_counts 
	*/

	// set display flags
	$export_display = "block";
	$quickstats_display = "block";

}


?>

		<!-- hidden div for messages -->
		<div id="report_messages" class="row">
			<div class="col-md-12" id="report_message"></div>
		</div>

		<!-- Limiters -->
		<div id="limiters" class="row">
			<div class="col-md-12 well">
				<h3>Select Filters</h3>		
				<form action="reports.php" method="GET" class="form" role="form">
					<div class="row">											

						<div class="form-group col-md-12">
							<label>Select Location:</label>
							<ul class="checkbox_grid">
								<?php
									// select transactions from DROPDOWN, or default to current tool location
									$current_report_location_array = array();									
									if (isset($_REQUEST['locations'])) {																		
										$current_report_location_array = $_REQUEST['locations'];										
									}
									else {									
										array_push($current_report_location_array, $_COOKIE['location']);
									}
								?>
								<li>
									<div class="checkbox">
										<label>
											<input id="ALL_checkbox" type="checkbox" name="locations[]" onclick="$('input.locationcheckbox').not(this).prop('checked', false);" value="ALL" <?php if ($_REQUEST['locations'] == array("ALL")) { echo "checked";} ?> > All 
										</label>
									</div>
								</li>
								<li>
									<div class="checkbox">
										<label>
											<input class="locationcheckbox" type="checkbox" name="locations[]" value="PK" <?php if ( in_array("PK", $_REQUEST['locations'])) { echo "checked";} ?> > Purdy/Kresge Reference
										</label>
									</div>
								</li>								

								<?php  makeCheckboxGrid(False, $current_report_location_array); ?>
							</ul>
						</div>

					</div>

					<!-- date select -->
					<div class="row">
						<div class="form-group col-md-3">
							<label>Start Date:</label>
							<input type="text" class="form-control" id="date_start" name="date_start" placeholder="please click to set" value="<?php if (isset($_REQUEST['date_start'])) {echo $_REQUEST['date_start'];}  ?>" >
							<script>
								$(function() {
									$( "#date_start" ).datepicker(({altField: "#date_start"}));
								});
							</script>
						</div>
						<div class="form-group col-md-3">
							<label>End Date:</label>
							<input type="text" class="form-control" id="date_end" name="date_end" placeholder="please click to set" value="<?php if (isset($_REQUEST['date_end'])) {echo $_REQUEST['date_end'];}  ?>" >
							<script>
								$(function() {
									$( "#date_end" ).datepicker(({altField: "#date_end"}));
								});
							</script>
						</div>
					</div>

					<!-- DOW select -->
					<div class="row">
						<div class="form-group col-md-5">
							<label>Select Days of the Week:</label>
							<?php
								// select transactions from DROPDOWN, or default to current tool location
								$current_dow = array();									
								if (isset($_REQUEST['dow'])) {																		
									$current_dow = $_REQUEST['dow'];
								}
								else {									
									$current_dow = NULL;
								}
							?>
							<ul class="checkbox_grid">
								<?php  makeDOWCheckboxGrid($current_dow); ?>								
							</ul>
						</div>
						<div class="form-group col-md-7">
							<label>Limit by User:</label>
							<?php
								// select transactions from DROPDOWN, or default to current tool location
								$current_user = array();									
								if (isset($_REQUEST['user'])) {																		
									$current_user = $_REQUEST['user'];
								}
								else {									
									$current_user = NULL;
								}
							?>
							<ul class="checkbox_grid">
								<li>
									<div class="checkbox">
										<label>
											<input id="ALL_user_checkbox" type="checkbox" name="user[]" onclick="$('input.usercheckbox').not(this).prop('checked', false);" value="ALL" <?php if ($_REQUEST['user'] == array("ALL") || !array_key_exists('user', $_REQUEST)) { echo "checked";} ?> > All 
										</label>
									</div>
								</li>
								<?php  makeUserCheckboxGrid($current_user); ?>								
							</ul>
						</div>						
					</div>

					<!-- submit -->					
					<div class="row">
						<div class="form-group col-md-1">
							<input type="hidden" name="submitted" value="true"/>
							<button type="submit" class="btn btn-success">Update</button>
						</div>
					</div>
				</form>
			</div>
		</div>


		<!-- QuickStats -->
		<div id="quickstats" class="row" style="display:<?php echo $quickstats_display; ?>">
			<div id="stats_results" class="col-md-12">

				<!-- top row -->
				<div class="row">
					<div class="col-md-6">
						<h3 style="text-align:center;">QuickStats</h3>
						<p><strong>Total Transaction</strong>: <?php echo $total_date_range_results; ?></p>

						<div class="col-md-6">
							<p><strong>Transactions Types:</strong>
								<ul>
									<?php
										foreach ($type_counts as $type => $count) {
											echo "<li>$type: $count</li>";
										}
									?>
								</ul>
							</p>
						</div>
						<div class="col-md-6">
							<p><strong>User Groups:</strong>
								<ul>
									<?php
										foreach ($user_group_counts as $user => $count) {
											echo "<li>$user: $count</li>";
										}
									?>
								</ul>
							</p>
						</div>

					</div>
					<div class="col-md-6" style="text-align:center;">
						<h3>Export Data</h3>				
						<form action="export_csv.php" method="POST">
							<input type="hidden" name="params" value='<?php echo json_encode($_REQUEST);?>'/>
							<button id="csv_button" type="submit" class="btn btn-WSUgreen" onclick="loadingCSV('Working...','Download as CSV');">Download as Spreadsheet (.csv)</button>
						</form>
					</div>
				</div>

				<!-- Transaction Breakdown -->
				<hr class="quickstats_dividers">
				<div class="row">
					<div class="col-md-12">
						<div id="transBreakdown"></div>
						<script type="text/javascript">
							transBreakdown(<?php echo json_encode($type_counts); ?>);
						</script>
					</div>
				</div>

				<!-- User Breakdown -->
				<?php 
					if (count($user_group_counts) > 1) {
				?>
				<hr class="quickstats_dividers">
				<div class="row">
					<div class="col-md-12">
						<div id="userBreakdown"></div>
						<script type="text/javascript">
							userBreakdown(<?php echo json_encode($user_group_counts); ?>);
						</script>
					</div>
				</div>
				<?php 
					} // close user if block
				?>

				<!-- Busiest Day of Week -->
				<hr class="quickstats_dividers">
				<div class="row">
					<div class="col-md-12">
						<div id="busiestDOWChart"></div>
						<script type="text/javascript">
							busiestDOW(<?php echo json_encode($dow_counts); ?>);
						</script>					
					</div>
				</div>				

				<!-- Busiest Transaction -->
				<hr class="quickstats_dividers">
				<div class="row">
					<div class="col-md-12">
						<div id="transPerLocation"></div>
						<script type="text/javascript">
							transPerLocation(<?php echo json_encode($locations_total_sorted); ?>,'<?php echo $date_start; ?>');
						</script>	
					</div>		
				</div>	

				<!-- Busiest Hours -->
				<hr class="quickstats_dividers">
				<div class="row">
					<div class="col-md-12">
						<div id="busiestHoursChart"></div>
						<script type="text/javascript">
							busiestHours(<?php echo json_encode($hour_counts); ?>);
						</script>						
					</div>
				</div>

			</div>	

		<div class="row">
			<div class="col-md-12 spacer40"></div>
		</div>

		<!-- footer -->
		<?php include('footer.php') ?>

	<body>
</html>
