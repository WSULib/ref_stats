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
			</div>
		</div>

<?php
if (isset($_POST['submitted'])) { 
	foreach($_POST AS $key => $value) { $_POST[$key] = mysqli_real_escape_string($link, $value); } 
	$sql = "INSERT INTO `ref_stats` ( `ref_type` ,  `location` ,  `ip`  ) VALUES(  '{$_POST['ref_type']}' ,  '{$_POST['location']}' ,  '{$_POST['ip']}'  ) ";
	mysqli_query($link, $sql) or die(mysqli_error()); 
	?>

		<div class="row">
			<div class="col-md-10">
				<p style="color:green;">Success!  Transaction added.</p>
				<a href='list.php'>Back To Listing</a> 
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
						<label>Reference Type (1,2,3)</label>
						<input class="form-control" type='text' name='ref_type'/>
					</div>
					<div class="form-group">					
						<label>Location</label>
						<select class="form-control" id="location" name="location">
							<option value="PK">Purdy Kresge Library</option>
							<option value="UGL">Undergraduate Library</option>
							<option value="LAW">Neef Law Library</option>
							<option value="MED">Shiffman Medical Library</option>
						</select>
					</div>
					<div class="form-group">						
						<label>IP Address (automatically populated, override if necessary)</label>
						<input type='text' name='ip' class="form-control" value="<?php echo IPgrabber(); ?>"/>
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

