<?php
require_once 'inc/lib.php';
session_start();
if (empty($_SESSION['user']) || !$user = user_info($_SESSION['user'])) {
	header('Location: dashboard.php');
}
?>
<!doctype html>
<html>
	<head>
		<title>User Profile | MCHostPanel</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
							<form action="userProfile.php" method="post">
								<input type="hidden" name="action" value="user-update">
								<div id="nameArea">
									<h3>Username:
										<input type="hidden" name="user" value=<?php echo $user['user']; ?>>
										<?php echo $user['user']; ?>
									</h3>
								</div>
								</br>
								<div id="roleArea">
									<h3>Role:
										<input type="hidden" name="role" value=<?php echo $user['role']; ?>>
										<?php echo $user['role']; ?>
									</h3>
								</div>
								</br>
								<div id="homeArea">
									<h3>Home Directory:
										<input type="hidden" name="dir" id="dir" value=<?php echo $user['home']; ?>>
										<?php echo $user['home']; ?>
									</h3>
								</div>
								</br>
								<div id="ramArea">
									<h3>RAM Allocated:
										<input class="span10" type="hidden" name="ram" id="ram" value=<?php echo $user['ram']; ?>>
										<?php echo $user['ram']; ?>MB
									</h3>
								</div>
								</br>
								<div id="portArea">
									<h3>Port:
										<input class="span3" type="hidden" name="port" id="port" value=<?php echo $user['port']; ?>>
										<?php echo $user['port']; ?>
									</h3>
								</div>
								</br></br></br>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
