<? 
include('header.php');
include('../inc/functions.php');
?>
<body>

	<div class="container">

		<div class="row">
			<div class="col-md-12">
				<h2>Reference Statistics Management</h2>
				<p><a href="../index.php"><strong>Reference Stats Tool (Live)</strong></a></p>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<p>Missed clicking the button?  Want to set the location or time manually?  Click here to log an action: <a class="btn btn-default" href=new.php>New Row</a></p>
				<h3>Transactions</h3>
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

					// get total count
					$result=mysqli_query($link, "SELECT * FROM ref_stats") or trigger_error(mysqli_error());	
					$total_stats = mysqli_num_rows($result);

					// $result = mysqli_query($link, "SELECT * FROM `ref_stats` ORDER BY timestamp DESC") or trigger_error(mysqli_error());
					$result = mysqli_query($link, "SELECT * FROM `ref_stats` ORDER BY timestamp DESC LIMIT $cursor, $rows") or trigger_error(mysqli_error()); 					
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
					<li class="<?php if ($page < 2) {echo 'disabled';} ?>"><a href="list.php?page=<?php echo ($page-1).'&rows='.($rows); ?>">Previous</a></li>
					<li class="<?php if ( ($rows * ($page)) > $total_stats) {echo 'disabled';}?>"><a href="list.php?page=<?php echo ($page+1).'&rows='.($rows); ?>">Next</a></li>
				</ul>				
				<?php
				
				?>				
			</div>
		</div>
	</div>
</body>
</html>
