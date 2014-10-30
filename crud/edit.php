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
			</div>
		</div>



<? 
if (isset($_GET['id']) ) { 
	$id = (int) $_GET['id']; 
	if ($_SERVER['REQUEST_METHOD'] == "POST") { 
		foreach($_POST AS $key => $value) { $_POST[$key] = mysqli_real_escape_string($link, $value); } 
		$sql = "UPDATE `ref_stats` SET  `ref_type` =  '{$_POST['ref_type']}' ,  `location` =  '{$_POST['location']}' ,  `ip` =  '{$_POST['ip']}' ,  `timestamp` =  '{$_POST['timestamp']}'   WHERE `id` = '$id' "; 
		mysqli_query($link, $sql) or die(mysqli_error());
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
						<label>Reference Type (1,2,3)</label>
						<input class="form-control" type='text' name='ref_type' value='<?= stripslashes($row['ref_type']) ?>'/>
					</div>
					<div class="form-group">					
						<label>Location</label>
						<select class="form-control" id="location" name="location">
							<option <?php if ( stripslashes($row['location'])=="PK") echo 'selected="selected"'; ?> value="PK">Purdy Kresge Library</option>
							<option <?php if ( stripslashes($row['location'])=="UGL") echo 'selected="selected"'; ?> value="UGL">Undergraduate Library</option>
							<option <?php if ( stripslashes($row['location'])=="LAW") echo 'selected="selected"'; ?> value="LAW">Neef Law Library</option>
							<option <?php if ( stripslashes($row['location'])=="MED") echo 'selected="selected"'; ?> value="MED">Shiffman Medical Library</option>
						</select>
					</div>
					<div class="form-group">						
						<label>IP Address (automatically populated, override if necessary)</label>
						<input type='text' name='ip' class="form-control" value='<?= stripslashes($row['ip']) ?>'/>
					</div>
					<div class="form-group">						
						<label>Original Timestamp (automatically populated, override if necessary)</label>
						<input type='text' name='timestamp' class="form-control" value='<?= stripslashes($row['timestamp']) ?>'/>
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












