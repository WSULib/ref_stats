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
				reporter("green", "Successful Submission at ".$_SESSION['date']);
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
			if (isset($_POST['location'])) {
				$_COOKIE['location'] = $_POST['location'];
				setcookie('location', $_POST['location']);				
				$_SESSION['result'] = "location";				
				header('Location: ./', true, 303);
			}
			elseif ($_COOKIE['location'] == 'NOPE') {
				reporter("red", "Please Set Your Location", " ");		
			}
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
					}
					else {						
						$_SESSION['result'] = "success";
					}
				    mysqli_stmt_close($stmt);
				    header('Location: ./', true, 303);

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
					<button type="submit" class="btn btn-primary btn-block btn-lg">Directional</button>
				</form>
				</div>
			</div> <!-- row -->

			<div class="row-fluid">
				<div class="col-md-12">
				<form action="" method="POST">
					<input name="type" type="number" value="2">
					<button type="submit" class="btn btn-primary btn-block btn-lg">Brief Reference</button>
				</form>
				</div>
			</div> <!-- row -->

			<div class="row-fluid">
				<div class="col-md-12">
				<form action="" method="POST">
					<input name="type" type="number" value="3">
					<button type="submit" class="btn btn-primary btn-block btn-lg">Extended Reference</button>
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
						<a href="crud/list.php"><button type="button" class="btn btn-sm btn-info">Edit Transactions</button></a>
						<a href="#" onclick="window.open('./index.php','ref_stats','menubar=0,resizable=0,width=350,height=880');"><button type="button" class="btn btn-sm btn-info">Launch Pop-Up</button></a>
					</p>
				</div>
			</div>

		</div>
		<hr>
		<div id="ref_graph">

			<div class="row-fluid">	
				<div class="col-md-12" id="refreport">				
					<h4 style="color:green;">Today's Stats</h4>	
					<table class="table table-striped table-condensed">						
						<?php						
						statsGraph($link);							
						?>
					</table>
				</div>
			</div> <!-- row -->

		</div> <!-- ref_actions --> 
	</div> <!-- container -->
</body>
</html>