<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- jQuery -->
<script src="inc/jquery-1.11.1.min.js"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="inc/bootstrap-3.2.0-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="inc/main.css">
<link rel="icon" href="../../inc/img/favicon.ico" type="image/x-icon" />

<!-- Latest compiled and minified JavaScript -->
<script src="inc/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>
<script>
window.open ("./index.php","ref_stats","menubar=0,resizable=0,width=350,height=880");
</script>
	
</head>
<body>

	<div class="container">
		<div class="row-fluid">
			<div id="header" class="col-md-12">
				<img id="logo" src="inc/w.png" />
				<h2>Reference Stats Tool Startup</h2>
			</div>
		</div>

		<div class="row-fluid">
			<div class="col-md-12 actions">
				<p>Don't see the Reference Stats Tool?  It is possible you have pop-ups disabled.</p>
				<p><a href="./index.php">Click here to open Ref Stats</a></p>
			</div>
		</div> <!-- row -->

		<div class="row-fluid">
			<div class="col-md-12 actions">
				<p>Prefer a bookmark for your browser? Drag this button to your bookmarks bar:</p>
				<p><a class="btn btn-default" href="javascript: (function(){window.open('http://<?php echo $_SERVER['SERVER_NAME'] ?>/forms/ref_stats/index.php','ref_stats','menubar=0,resizable=0,width=350,height=880')})();">RefStats</a></p>
			</div>
		</div> <!-- row -->

	</div> <!-- container -->

</body>
</html>
