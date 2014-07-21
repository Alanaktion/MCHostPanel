<?php
require_once 'inc/lib.php';

if ($_POST['action'] == 'user-update')
{
	user_modify($_POST['user'], $_POST['pass'], $_POST['role'], $_POST['dir'], $_POST['ram'], $_POST['port']);
	echo('hello world');
}
?>
<!doctype html>
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
					<form action="userProfile.php" method="post" autocomplete="off">
					<input type="hidden" name="action" value="user-update">
						<div id="nameArea">	
							<h3>Username: 
							<input type="hidden" name="action" value=<?php echo $user['user'];?>>
							<?php
							echo $user['user'];
			 				?>

			 				</h3>
			 			</div>
			 			
			 			</br>
			 	
			 			<div id="roleArea">	
							<h3>Role:
								<input type="hidden" name="action" value=<?php echo $user['role'];?>>
								<?php
							echo $user['role'];
			 				?>
			 				</h3>
			 				
			 			</div>
			 			
			 			</br>
			 			
			 			<div id="homeArea">	
							<h3>Home Directory:</h3> 
								<div class="controls">
									<input class="span10" type="text" name="dir" id="dir" value=<?php echo $user['home'];?>>
								</div>
			 				
			 			</div>
			 			
			 			</br>
			 			
			 			<div id="ramArea">	
							<h3>RAM Allocated:</h3>
								<div class="controls">
									<input class="span10" type="text" name="ram" id="ram" value=<?php echo $user['ram'];?>>
								</div>
			 			</div>
			 			
			 			</br>
			 			
			 			<div id="portArea">	
							<h2>Port:</h2>
			 					<div class="controls">
									<input class="span3" type="number" name="port" id="port" value=<?php echo $user['port'];?>>
								</div>
			 			</div>
			 			
			 		</br></br></br>
			 		
			 		<button type="submit" class="btn btn-primary">Save Changes</button>
			 		</form>
			 	</div>	
			</div>
		</div>
	</div>
</div>
</body>
</html>