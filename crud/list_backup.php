<? 
include('header.php');
include('../inc/functions.php');

// DEBUG
echo "Current tool location: {$_COOKIE['location']}<br>";
echo "Current editing location: {$_REQUEST['edit_location']}\n";

?>
<body>

	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h2>Reference Statistics Management</h2>
				<p>					
					<a class="btn btn-info" href="../index.php">Back to refStats</a></p>
				</p>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<h3>Create Transaction</h3>				
				<a class="btn btn-info" href="./new.php">New Row</a></p>				
				<h3>Edit Transactions</h3>			
				
				<div class="col-md-3">					
					<form action="./list.php" method="GET">	
						<div class="form-group">																
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
					</p>
				</div>

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

					// get current page					
					if (isset($_REQUEST['page']) && $_REQUEST['page'] > 0) {
						$page = $_REQUEST['page'];
					}
					else { 
						$page = 1;						
					}					

					// get limiter					
					if (isset($_REQUEST['rows']) && $_REQUEST['rows'] > 0) {
						$rows = $_REQUEST['rows'];
					}
					else { $rows = 15; }

					// establish DB cursor					
					$cursor = ($page-1) * $rows;	

					// establish editing location, or select all
					if (isset($_REQUEST['edit_location']) && $_REQUEST['edit_location'] == "ALL"){
						$where_clause = "WHERE location = ANY(select location from ref_stats)";
					}
					elseif (isset($_REQUEST['edit_location'])) {
						$where_clause = "WHERE location = '{$_REQUEST['edit_location']}'";
					}
					else {
						// default to current location
						$where_clause = "WHERE location = '{$_COOKIE['location']}'";
					}

					// get total count
					$result=mysqli_query($link, "SELECT * FROM ref_stats $where_clause") or trigger_error(mysqli_error());	
					$total_stats = mysqli_num_rows($result);
					
					$result = mysqli_query($link, "SELECT * FROM `ref_stats` $where_clause ORDER BY timestamp DESC LIMIT $cursor, $rows") or trigger_error(mysqli_error()); 					
					while($row = mysqli_fetch_array($result)){ 
						foreach($row AS $key => $value) { $row[$key] = stripslashes($value); }
						echo "<tr>";  
						echo "<td>" . nl2br( $row['id']) . "</td>";  
						echo "<td>" . nl2br( $row['ref_type']) . "</td>";  
						echo "<td>" . nl2br( $row['location']) . "</td>";  
						echo "<td>" . nl2br( $row['ip']) . "</td>";  
						echo "<td>" . nl2br( $row['timestamp']) . "</td>";  
						echo "<td><a href=edit.php?id={$row['id']}>Edit</a> / <a href=delete.php?id={$row['id']}>Delete</a></td> "; 
						echo "</tr>"; 
					}
					?>
				</table>

				<ul class="pager">											
					<li class="<?php if ($page < 2) {echo 'disabled';} ?>"><a href="list.php?page=<?php echo ($page-1).'&rows='.($rows).'&edit_location='.$current_edit_location;; ?>">Previous</a></li>
					<li class="<?php if ( ($rows * ($page)) > $total_stats) {echo 'disabled';}?>"><a href="list.php?page=<?php echo ($page+1).'&rows='.($rows).'&edit_location='.$current_edit_location; ?>">Next</a></li>
				</ul>				
				<?php
				
				?>				
			</div>
		</div>
	</div>
</body>
</html>
