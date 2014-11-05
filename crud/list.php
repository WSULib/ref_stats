<? 
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
$query = "SELECT id, ref_type, location, ip, DATE_FORMAT(timestamp, '%r') AS print_timestamp, UNIX_TIMESTAMP(timestamp) as sort_timestamp FROM ref_stats WHERE DATE(timestamp) = DATE_ADD(CURDATE(), INTERVAL $page DAY) AND $location_where ORDER BY sort_timestamp DESC";
$result = mysqli_query($link, $query) or trigger_error(mysqli_error());
$total_day_stats = mysqli_num_rows($result);
$results_date = date('l\, m\-j\-y', strtotime( ($page)." days" ));
$graph_date = date('m d Y', strtotime( ($page)." days" ));

?>
<body>

	<div class="container">

		<div class="row">
			<div class="col-md-12 text-center">
				<h2>Reference Statistics Management</h2>
				<p>					
					<a class="btn btn-info" href="../index.php">Back to refStats</a></p>
				</p>				
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12 text-center">
				<h3>Add Transaction</h3>				
				<a class="btn btn-info" href="./new.php">New Row</a></p>	
			</div>
		</div>			

		<div class="row">

			<div class="col-md-12">
				<h3>Edit Transactions</h3>				
				
				<div class="col-md-3">					
					<form action="./list.php" method="GET">	
						<div class="form-group">		
							<label>Where would you like to edit?</label>														
							<select class="form-control" id="edit_location" name="edit_location" onchange='this.form.submit()'>
								<?php
								// select transactions from dropdown, or default to current tool location
								if (isset($_REQUEST['edit_location'])) {									
									$current_edit_location = $_REQUEST['edit_location'];
								}
								else {									
									$current_edit_location = $_COOKIE['location'];
								}	
								?>							
								<option <?php if ( $current_edit_location=="PK") echo 'selected="selected"'; ?> value="PK">Purdy Kresge Library</option>
								<option <?php if ( $current_edit_location=="UGL") echo 'selected="selected"'; ?> value="UGL">Undergraduate Library</option>
								<option <?php if ( $current_edit_location=="LAW") echo 'selected="selected"'; ?> value="LAW">Neef Law Library</option>
								<option <?php if ( $current_edit_location=="MED") echo 'selected="selected"'; ?> value="MED">Shiffman Medical Library</option>
								<option <?php if ( $current_edit_location=="ALL") echo 'selected="selected"'; ?> value="PK">All Locations</option>
							</select>					
						</div>
					</form>					
				</div>

				<div class="col-md-5">
					<ul class="pager">											
						<li class="<?php if ($page >= 0) {echo 'disabled'; }?>"><a href="list.php?page=<?php echo ($page+1).'&edit_location='.$current_edit_location;; ?>"><?php echo date('l\, m\-j\-y', strtotime( ($page+1)." days" )); ?></a></li>
						<li class=""><a href="list.php?page=0&edit_location=<?php echo $current_edit_location; ?>"><strong>Today</strong></a></li>
						<li class=""><a href="list.php?page=<?php echo ($page-1).'&edit_location='.$current_edit_location; ?>"><?php echo date('l\, m\-j-y', strtotime( ($page-1)." days" )); ?></a></li>
					</ul>				
				</div>

				<div id="transactions_total" class="col-md-3">
					<h4 class="text-center">
						<?php echo "Location: $current_edit_location<br>$results_date, $total_day_stats transactions"; ?>
					</h4>
				</div>		
			
			</div>
		</div>

		<!-- Table Row -->
		<div class="row">
			<div class="col-md-12" id="transactions_table">
				<table class="table table-striped">
					<tr>
						<td><b>Id</b></td> 
						<td><b>Ref Type</b></td> 
						<td><b>Location</b></td> 
						<td><b>Ip</b></td> 
						<td><b>Timestamp</b></td>
						<td><b>Actions</b></td> 
					</tr>
					<?php						
					while($row = mysqli_fetch_array($result)){ 
						foreach($row AS $key => $value) { $row[$key] = stripslashes($value); }
						echo "<tr>";  
						echo "<td>" . nl2br( $row['id']) . "</td>";  
						echo "<td>" . nl2br( $ref_type_hash[$row['ref_type']]) . "</td>";  
						echo "<td>" . nl2br( $row['location']) . "</td>";  
						echo "<td>" . nl2br( $row['ip']) . "</td>";  
						echo "<td>" . nl2br( $row['print_timestamp']) . "</td>";  
						echo "<td><a href=edit.php?id={$row['id']}>Edit</a> / <a href=delete.php?id={$row['id']}>Delete</a></td> "; 
						echo "</tr>"; 
					}
					?>
				</table>
			</div>
		</div>

		<!-- graph -->
		<div id="stats_graph" class="row">	
			<div class="col-md-12" id="refreport">				
				<h4 onclick="toggleIndexStats();">Stats Graph</h4>	
				<div id="table_wrapper">
					<table class="table table-striped table-condensed">						
						<?php						
						statsGraph($link, "crud", $current_edit_location, $graph_date);							
						?>
					</table>
				</div>
			</div>
		</div> 

	
</body>
</html>
