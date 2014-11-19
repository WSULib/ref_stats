<? 
include($_SERVER['DOCUMENT_ROOT'].'inc/dbs/ref_stats_config.php'); 
include('header.php');
include('../inc/functions.php');
?>

<body>
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h2>Add Transaction</h2>				
				<p>Current location: <span style="font-size:150%;"><?php echo $_COOKIE['location']; ?></span></p>				
			</div>
		</div>

<?php
if (isset($_POST['submitted'])) {

	foreach($_POST AS $key => $value) { $_POST[$key] = mysqli_real_escape_string($link, $value); } 
	$IP = IPgrabber();
	$insert_date = date("Y-m-d")." {$_REQUEST['hour']}";
	$sql = "INSERT INTO `ref_stats` ( `ref_type` ,  `location` ,  `ip`, `timestamp` ) VALUES(  '{$_REQUEST['ref_type']}' ,  '{$_REQUEST['location']}' ,  '$IP', '$insert_date'  ) ";
	$result = mysqli_query($link, $sql) or die(mysqli_error());
	?>

		<div class="row">
			<div class="col-md-10">
				<p style="color:green;">Success!  Transaction added.</p>
				<a class="btn btn-default" href='list.php'>Back to Transactions</a> 
			</div>
		</div>

<?php
} 
else {	

?>
		
		<div class="row">
			<div class="col-md-6">
				<form action='new.php' method='POST' class="form" role="form">
					<div class="form-group">	
						<label>Select location for this transaction:</label>													
						<select class="form-control" id="location" name="location">		
							<?php makeDropdown(False); ?>							
							<!-- <option <?php if ( !isset($_COOKIE['location']) || $_COOKIE['location'] == "NOPE" ) echo 'selected="selected"'; ?> value="PK">Please Select your Location</option>
							<option <?php if ( $_COOKIE['location']=="PK1") echo 'selected="selected"'; ?> value="PK1">Purdy Kresge Library - Desk 1</option>
							<option <?php if ( $_COOKIE['location']=="PK2") echo 'selected="selected"'; ?> value="PK2">Purdy Kresge Library - Desk 2</option>
							<option <?php if ( $_COOKIE['location']=="UGL") echo 'selected="selected"'; ?> value="UGL">Undergraduate Library</option>
							<option <?php if ( $_COOKIE['location']=="LAW") echo 'selected="selected"'; ?> value="LAW">Neef Law Library</option>
							<option <?php if ( $_COOKIE['location']=="MED") echo 'selected="selected"'; ?> value="MED">Shiffman Medical Library</option>								 -->
						</select>
					</div>

					<div class="form-group">
						<label>Reference Type:</label>		
						<div class='radio'>
							<label>
								<input type='radio' name='ref_type' value='1'><span class="btn btn-primary ref_type_button">Directional</span>
							</label>
						</div>
						<div class='radio'>
							<label>
								<input type='radio' name='ref_type' value='2'><span class="btn btn-primary ref_type_button">Brief Reference</span>
							</label>
						</div>
						<div class='radio'>
							<label>
								<input type='radio' name='ref_type' value='3'><span class="btn btn-primary ref_type_button">Extended Reference</span>
							</label>
						</div>
					</div>

					<div class="form-group">
						<label>Time (Hour)</label>
						<?php 
							// $current_hour = date("H");							
							// $hour = 8;							

							// while ($hour < 24) {								
							// 	$startHour = date("g a", strtotime("$hour:00"));
							// 	$endHour = date("g a", strtotime(($hour+1).":00"));

							// 	if ($current_hour != $hour){
							// 		echo "<div class='radio'><label><input type='radio' name='hour' id='hour_$hour' value='$hour'>$startHour - $endHour</label></div>";	
							// 	}
							// 	else {
							// 		// mark selected hour
							// 		echo "<div class='radio'><label><input type='radio' name='hour' id='hour_$hour' value='$hour' checked='checked'>$startHour - $endHour</label></div>";
							// 	}
							// 	$hour ++;
							// }
						?>
						<select class="form-control" id="hour" name="hour">
							<?php
							$current_hour = date("H");							
							$hour = 8;							

							while ($hour < 24) {								
								$startHour = date("g a", strtotime("$hour:00"));
								$endHour = date("g a", strtotime(($hour+1).":00"));

								if ($current_hour != $hour){
									echo "<option id='hour_$hour' value='$hour'>$startHour - $endHour</option>";	
								}
								else {
									// mark selected hour
									echo "<option id='hour_$hour' value='$hour' selected>$startHour - $endHour</option>";									
								}
								$hour ++;
							}
							?>
						</select>
					</div>

					<input type="hidden" name="submitted" value="true"/>
					<button type="submit" class="btn btn-default">Submit</button> 
				</form>
				
			</div>
		</div>

	</div>


<?php
}
?>

</body>
</html>

