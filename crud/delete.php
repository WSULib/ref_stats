<?php
include($_SERVER['DOCUMENT_ROOT'].'inc/dbs/ref_stats_config.php'); 
include('header.php');
include('../inc/functions.php');
?>

<body>
	<div class="container">

<?php
$id = (int) $_GET['id']; 
mysqli_query($link, "DELETE FROM `ref_stats` WHERE `id` = '$id' ") ;
$result = (mysqli_affected_rows($link))? "Row deleted." : "Nothing deleted.";
?> 

		<div class="row">
			<div class="col-md-10">
				<h2>Transaction Removal</h2>
				<p style="color:green;"><?php echo $result; ?></p>
				<a href='list.php'>Back To Listing</a>
			</div>
		</div>

	</div>

</body>
</html>
