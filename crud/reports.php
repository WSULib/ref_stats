<?php
include('header.php');
include('../inc/functions.php');

if (isset($_REQUEST['submitted'])){

	// establish reporting location, or select all

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
	$full_query = "SELECT ref_type, location, user_group, DAYNAME(timestamp), DATE(timestamp) AS simple_date, timestamp AS ordering_timestamp FROM ref_stats WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' AND $location_where ORDER BY ordering_timestamp DESC";
	// echo $full_query;
	$full_result = mysqli_query($link, $full_query) or trigger_error(mysqli_error());
	$total_date_range_results = mysqli_num_rows($full_result);

	// shunt to chart-ready arrays
	$location_totals = array();
	while ($row = mysqli_fetch_assoc($full_result)) {	

		if (!array_key_exists($row['location'],$location_totals)){
			$total_counts[$row['location']] = array();
		}
		$location_totals[$row['location']][] = $row['ordering_timestamp'];
	}

	// Transaction counts
	$type_query = "SELECT ref_type, COUNT(ref_type) AS ref_type_count FROM ref_stats WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' AND $location_where GROUP BY ref_type";
	// echo $type_query;
	$type_result = mysqli_query($link, $type_query) or trigger_error(mysqli_error());
	$type_counts = array();
	while($row = mysqli_fetch_assoc($type_result)) {		
		$type_counts[$ref_type_hash[$row['ref_type']]] = $row['ref_type_count'];
	}

	// Busiest Calculations
	// Day-of-the-week (dow)
	$dow_query = "SELECT DAYNAME(timestamp) AS dow_name, DAYOFWEEK(timestamp) AS dow_index, count(DAYOFWEEK(timestamp)) AS dow_count FROM ref_stats WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' AND $location_where GROUP BY dow_index ORDER BY dow_index;";
	$dow_result = mysqli_query($link, $dow_query) or trigger_error(mysqli_error());
	$dow_counts = array();
	while($row = mysqli_fetch_assoc($dow_result)) {		
		$dow_counts[$row['dow_name']] = $row['dow_count'];
	}

	// Day-of-the-week (dow)
	$hour_query = "SELECT HOUR(timestamp) AS hour, COUNT(CASE WHEN ref_type = 1 THEN ref_type END) AS Directional, COUNT(CASE WHEN ref_type = 2 THEN ref_type END) AS Brief, COUNT(CASE WHEN ref_type = 3 THEN ref_type END) AS Extended, COUNT(CASE WHEN ref_type = 4 THEN ref_type END) AS Consultation FROM ref_stats WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' AND $location_where GROUP BY hour;";
	$hour_result = mysqli_query($link, $hour_query) or trigger_error(mysqli_error());
	$hour_counts = array();
	while($row = mysqli_fetch_assoc($hour_result)) {		
		$hour_counts[$row['hour']] = array(
			"Directional" => $row['Directional'],
			"Brief" => $row['Brief'],
			"Extended" => $row['Extended'],
			"Consultation" => $row['Consultation'],
		);
	}

	// Busiest Single Days
	$single_query = "SELECT DAYNAME(timestamp) as dow_name, DATE(timestamp) AS date, count(ref_type) AS ref_count FROM ref_stats WHERE DATE(timestamp) >= '$date_start' AND DATE(timestamp) <= '$date_end' AND $location_where GROUP BY date ORDER BY ref_count DESC limit 5;";
	$single_result = mysqli_query($link, $single_query) or trigger_error(mysqli_error());

	/* Data Explanations:
	Recommended to use json_encode() for each array to use with charts.js

	$full_result = MySQL result set for all queries
	$total_date_range_results = Total transactions from all locations.

	$location_totals = array of locations, each an array full of timestamp.  Can be used to create graph.

	$trans_result = MySQL result set for counts of transaction types
	$type_counts = An associative array from ALL locations - key is type as string, value is count from database

	$dow_counts = Associative array with Day-of-the-Week (dow) names and total transactions counts for that day

	$hour_counts = Nested Associative array with hours of the day, and numbers for "Directional", "Brief", "Extended", and "Consultation"

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
			<div class="col-md-12">
				<h3>Select Location(s) and Date Range</h3>		
				<form action="reports.php" method="GET" class="form" role="form">
					<div class="row">											

						<div class="form-group col-md-12">
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
											<input id="ALL_checkbox" type="checkbox" name="locations[]" onclick="$('input').not(this).prop('checked', false);" value="ALL" <?php if ($_REQUEST['locations'] == array("ALL")) { echo "checked";} ?> > All 
										</label>
									</div>
								</li>	
								<li>
									<div class="checkbox">
										<label>
											<input id="ALL_checkbox" type="checkbox" name="locations[]" value="MAIN_CAMPUS" <?php if ( in_array("MAIN_CAMPUS", $_REQUEST['locations'])) { echo "checked";} ?> > Main Campus 
										</label>
									</div>
								</li>
														

								<?php  makeCheckboxGrid(False, $current_report_location_array); ?>
							</ul>
						</div>

					</div>
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
					<div class="row">
						<div class="form-group col-md-1">
							<input type="hidden" name="submitted" value="true"/>
							<button type="submit" class="btn btn-default">Update</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- QuickStats -->
		<div id="quickstats" class="row" style="display:<?php echo $quickstats_display; ?>">
			<div class="col-md-12">
				<h3>QuickStats</h3>

				<div class="row">
					<div class="col-md-6">
						<h4>Totals</h4>
						<p>
							<span>[LINE GRAPH HERE - ALL LOCATIONS AS INDIVIDUAL LINES]</span>
							<img class="img-responsive img-rounded" src="https://thisismydinner.files.wordpress.com/2012/03/img_95611.jpg" style:"max-width:100%;"/>
							<p class="raw_json" style="display:<?php echo $_REQUEST['raw_json']; ?>"><?php echo json_encode($location_totals); ?></p>
						</p>
						<p><span class="stat_name">Total Transactions</span>: <?php echo $total_date_range_results; ?></p>
						
					</div>

					<div class="col-md-6">
						<h4>Transactions Breakdown</h4>
						<p>
							<span>[PIE CHART HERE - VARIOUS REF TYPES FOR ALL LOCATIONS]</span>
							<img class="img-responsive img-circle" src="https://thisismydinner.files.wordpress.com/2012/03/img_95611.jpg" style:"max-width:100%;"/>
							<p class="raw_json" style="display:<?php echo $_REQUEST['raw_json']; ?>"><?php echo json_encode($type_counts); ?></p>
						</p>
						<ul>
							<?php
								foreach($type_counts as $key => $value) {
									echo "<li><span class='stat_name'>$key</span>: $value</li>";
								}
							?>
						</ul>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<h4>Busiest Days per Week</h4>
						<p>
							<span>[BAR CHART HERE - TOTAL TRANSACTIONS PER DAY-OF-THE-WEEK]</span>
							<img class="img-responsive img-rounded" src="https://thisismydinner.files.wordpress.com/2012/03/img_95611.jpg" style:"max-width:100%;"/>
							<span class="raw_json" style="display:<?php echo $_REQUEST['raw_json']; ?>"><?php echo json_encode($dow_counts); ?></span>
						</p>					
					</div>

					<div class="col-md-6">
						<h4>Busiest Hours per Day</h4>
						<p>
							<span>[BAR CHART HERE - SIMILAR TO BAR CHART ON FRONT PAGE]</span>
							<img class="img-responsive img-circle" src="https://thisismydinner.files.wordpress.com/2012/03/img_95611.jpg" style:"max-width:100%;"/>
							<span class="raw_json" style="display:<?php echo $_REQUEST['raw_json']; ?>"><?php echo json_encode($hour_counts); ?></span>
						</p>
					</div>
				</div>

			</div>
		</div>	

		<!-- Export -->
		<div id="export" class="row" style="display:<?php echo $export_display; ?>">
			<div class="col-md-12">
				<h3>Export Data</h3>				
				<!-- fires JS to generate .csv file -->
				<button type="submit" class="btn btn-success" onclick="alert('All the data will be yours.');">Download</button>
				</form>						
			</div>
		</div>


	<body>
</html>
