<?php
include($_SERVER['DOCUMENT_ROOT'].'inc/dbs/ref_stats_config.php'); 
include('header.php');
include('../inc/functions.php');
?>

<body>
	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h2>Edit Transaction</h2>
				<p>Current location: <span style="font-size:150%;"><?php echo $_COOKIE['location']; ?></span></p>
			</div>
		</div>



<? 
if (isset($_GET['id']) ) { 
	$id = (int) $_GET['id']; 
	if ($_SERVER['REQUEST_METHOD'] == "POST") { 
		$insert_date = date("Y-m-d")." {$_REQUEST['hour']}";
		foreach($_POST AS $key => $value) { $_POST[$key] = mysqli_real_escape_string($link, $value); } 
		$sql = "UPDATE `ref_stats` SET  `ref_type` =  '{$_POST['ref_type']}' ,  `location` =  '{$_POST['location']}' ,  `ip` =  '{$_POST['ip']}' ,  `timestamp` =  '$insert_date'   WHERE `id` = '$id' "; 
		mysqli_query($link, $sql) or die(mysqli_error());

		// if coming from index.php, return
		if (isset($_REQUEST['origin']) && $_REQUEST['origin'] == 'index' ){
			header('Location: ../', true, 302);
		}
?>

		<div class="row">
			<div class="col-md-10">
				<p style="color:green;">Success!  Transaction edited.</p>
				<a href='list.php'>Back To Listing</a> 
			</div>
		</div>

<?php
} 
else {	
	$row = mysqli_fetch_array ( mysqli_query($link, "SELECT * FROM `ref_stats` WHERE `id` = '$id' ")); 

?>

		<div class="row">
			<div class="col-md-6">
				<form action='' method='POST' class="form" role="form">					

					<div class="form-group">
						<label>Reference Type:</label>		
						<div class='radio'>
							<label>
								<input type='radio' name='ref_type' value='1' <?php if ( $row['ref_type'] == 1) { echo "checked='checked'"; } ?>><span class="btn btn-primary ref_type_button">Directional</span>
							</label>
						</div>
						<div class='radio'>
							<label>
								<input type='radio' name='ref_type' value='2' <?php if ( $row['ref_type'] == 2) { echo "checked='checked'"; } ?>><span class="btn btn-primary ref_type_button">Brief Reference</span>
							</label>
						</div>
						<div class='radio'>
							<label>
								<input type='radio' name='ref_type' value='3' <?php if ( $row['ref_type'] == 3) { echo "checked='checked'"; } ?>><span class="btn btn-primary ref_type_button">Extended Reference</span>
							</label>
						</div>
					</div>

					<div class="form-group">					
						<label>Location</label>
						<select class="form-control" id="location" name="location">
							<?php makeDropdown(False); ?>
						</select>
					</div>
					<div class="form-group">						
						<label>IP Address (automatically populated, override only if necessary)</label>
						<input type='text' name='ip' class="form-control" value='<?= stripslashes($row['ip']) ?>'/>
					</div>	

					<div class="form-group">
						<label>Time (Hour)</label>						
						<select class="form-control" id="hour" name="hour">
							<?php

							// derive previous hour							
							$timestamp_linux = strtotime($row['timestamp']);
							$timestamp_hour = date("H",$timestamp_linux);
							$hour = 8;							

							while ($hour < 24) {								
								$startHour = date("g a", strtotime("$hour:00"));
								$endHour = date("g a", strtotime(($hour+1).":00"));

								if ($timestamp_hour != $hour){
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


					<button type="submit" class="btn btn-default">Submit</button> 
				</form>
			</div>
		</div>

	</div>

<? 
	} 
}
?> 












