<?php
require_once 'inc/lib.php';

?><!doctype html>
<html>
<head>
	<title>User Profile | MCHostPanel</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<link rel="stylesheet" href="css/style.css">
	<meta name="author" content="James Pollock [jamesplanet.net]">
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>
<body>
<?php require 'inc/top.php'; ?>
<div class="tab-content">
	<div class="tab-pane active">
		<div class="container-fluid">
			<div class="row-fluid">
				<legend>User Profile</legend>
					<div id="profileArea">
						<div id="nameArea">	
							<h2>Username: <?php
							echo $user['user'];
			 				?>
			 				</h2>
			 			</div>
			 			
			 			</br>
			 	
			 			<div id="rollArea">	
							<h2>Role: <?php
							echo $user['role'];
			 				?>
			 				</h2>
			 			</div>
			 			
			 			</br>
			 			
			 			<div id="homeArea">	
							<h2>Home Directory: <?php
							echo $user['home'];
			 				?>
			 				</h2>
			 			</div>
			 			
			 			</br>
			 			
			 			<div id="ramArea">	
							<h2>RAM Allocated: <?php
							echo $user['ram'];
			 				?>
			 				</h2>
			 			</div>
			 			
			 			</br>
			 			
			 			<div id="portArea">	
							<h2>Port: <?php
							echo $user['port'];
			 				?>
			 				</h2>
			 		</div>
			 		
			 	</div>	
			</div>
		</div>
	</div>
</div>
</body>
</html>