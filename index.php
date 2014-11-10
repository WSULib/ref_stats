<?php
include('inc/functions.php');
include('header.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="../../inc/img/favicon.ico" type="image/x-icon" />
	<!-- jQuery -->
	<script src="inc/jquery-1.11.1.min.js"></script>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="inc/bootstrap-3.2.0-dist/css/bootstrap.min.css">

	<!-- Local CSS -->
	<link rel="stylesheet" href="inc/main.css">
	<link rel="stylesheet" href="inc/shared.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="inc/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

	<!-- Local JS -->
	<script src="inc/functions.js"></script>	
</head>

<body onBlur="window.focus();">	
	<div class="container">

		<div class="row-fluid">
			<div id="header" class="col-md-12">
				<img id="logo" src="inc/w.png"/>
				<h2>Reference Stats Tool</h2>
			</div>
		</div>
		
		<!-- Message reporting and action logging -->
		<?php
		session_start();		
		locationSetter();			

		// 
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			if (isset($_SESSION['result']) && $_SESSION['result'] == "success") {								
				reporter("green", "<a style='color:green;'href='crud/edit.php?id={$_SESSION['last_trans_id']}&origin=index'>Submitted '{$_SESSION['ref_type_string']}' at ".$_SESSION['date']."</a>");
			}
			elseif (isset($_SESSION['result']) && $_SESSION['result'] == "fail") {
				reporter("red", "Error: Submission Failed", " ");
			}
			elseif (isset($_SESSION['result']) && $_SESSION['result'] == "location") {
				reporter("orange", "You Changed Your Location", " ");
			}
			else {
				reporter("white", "Nothing to report.", "visible");
			}
			session_destroy();
		}


		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			
			// set location
			if (isset($_POST['location'])) {
				$_COOKIE['location'] = $_POST['location'];
				setcookie('location', $_POST['location']);				
				$_SESSION['result'] = "location";				
				header('Location: ./', true, 302);
			}
			elseif ($_COOKIE['location'] == 'NOPE') {
				reporter("red", "Please Set Your Location", " ");		
			}

			// register reference transaction
			else {
			  	$type = $_POST['type'];
			  	$ip = ipGrabber();
				$location = $_COOKIE['location'];

				if (mysqli_connect_errno()) {
					reporter("red", "Error: Submission Failed" . mysqli_connect_error());
				}

				$query = "INSERT into ref_stats(ref_type, location, ip) VALUES ('$type', '$location', '$ip')";

				if($stmt = mysqli_prepare($link, $query)) {

				    $insert_result = mysqli_stmt_execute($stmt);				    

					if ($insert_result === TRUE) {
						$_SESSION['result'] = "success";
						$_SESSION['date'] = date("h:i:sa");
						$_SESSION['ref_type_string'] = $ref_type_hash[$_POST['type']];
						$_SESSION['last_trans_id'] = mysqli_insert_id($link);
					}
					else {						
						$_SESSION['result'] = "success";
					}
				    mysqli_stmt_close($stmt);
				    header('Location: ./', true, 302);

			   }
			   // if it fails
			   else {
					reporter("error", "Error: Submission Failed ", " ");
			   }				
			} // cookie
		} // post

		
		?>

		<div id="ref_actions">

			<div class="row-fluid">
				<div class="col-md-12">
				<form action="" method="POST">
					<input name="type" type="number" value="1"></input>
					<button type="submit" class="btn ref_type_button btn-primary btn-block btn-lg">Directional</button>
				</form>
				</div>
			</div> <!-- row -->

			<div class="row-fluid">
				<div class="col-md-12">
				<form action="" method="POST">
					<input name="type" type="number" value="2">
					<button type="submit" class="btn ref_type_button btn-primary btn-block btn-lg">Brief Reference</button>
				</form>
				</div>
			</div> <!-- row -->

			<div class="row-fluid">
				<div class="col-md-12">
				<form action="" method="POST">
					<input name="type" type="number" value="3">
					<button type="submit" class="btn ref_type_button btn-primary btn-block btn-lg">Extended Reference</button>
				</form>
				</div>
			</div> <!-- row -->

			<div class="row-fluid">
				<div class="col-md-12">
					<form action="" method="POST">
					<select class="form-control" id="location" name="location" onchange=this.form.submit()>
						<?php 
						makeDropdown();						
						?>
					</select>
					</form>
				</div>
			</div> <!-- row -->
			<div class="row-fluid">
				<div class="col-md-12">
					<p>
						<a href="crud/list.php"><button type="button" class="btn btn-sm btn-WSUgreen">Edit Transactions</button></a>
						<a href="#" onclick="window.open('./index.php','ref_stats','menubar=0,resizable=0,width=350,height=880');"><button type="button" class="btn btn-sm btn-WSUgreen">Launch Pop-Up</button></a>
					</p>
				</div>
			</div>

		</div>
		<hr>

		<div id="ref_graph">
			<div class="row-fluid">	
				<div class="col-md-12" id="refreport">				
					<h4 onclick="toggleIndexStats();">Today's Stats <span style="font-size:50%;">(click to toggle)</span></h4>	
					<div id="table_wrapper">
						<table class="table table-striped table-condensed">						
							<?php						
							statsGraph($link,"index",'','');							
							?>
						</table>
					</div>
				</div>
			</div> 
		</div> 

	</div> <!-- container -->
</body>
</html>