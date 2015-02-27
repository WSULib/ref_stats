<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- jQuery -->
	<script src="../inc/jquery-1.11.1.min.js"></script>

	<!-- jQuery UI -->
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="../inc/bootstrap-3.2.0-dist/css/bootstrap.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="../inc/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

	<!-- Local JS -->
	<script src="../inc/functions.js"></script>	

	<!-- CRUD css -->
	<link rel="stylesheet" href="crud.css">	
	<link rel="stylesheet" href="../inc/shared.css">	
	
</head>

<body>
	<div class="container">

	<div id="breadcrumb" class="row">
		<div class="col-md-12 text-center">
			<h2>RefStats Management</h2>			
			<p>Current location: <span style="font-size:125%;">
				<?php 
					if ($_COOKIE['location'] != "NOPE"){
						echo $_COOKIE['location']; 
					}
					else {
						echo "<span style='color:red;'>Unselected</span>";
					}						
				?>
			</span></p>			
			<p><a class="btn btn-WSUgreen" href="../"><!-- <span class="glyphicon glyphicon-hand-down" aria-hidden="true"></span> --> RefStats Main Page</a> <a class="btn btn-WSUgreen" href="./list.php"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Manage Stats</a> <a class="btn btn-WSUgreen" href="./reports.php"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Reports</a></p>
		</div>
	</div>


