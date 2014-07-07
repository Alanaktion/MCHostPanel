<?php
require_once 'inc/lib.php';
require_once 'inc/Spyc.php';

session_start();
if (!$_SESSION['user'] || !$user = user_info($_SESSION['user'])) {
	// Not logged in, redirect to login page
	header('Location: .');
	exit('Not Authorized');
}

?><!doctype html>
<html>
<head>
<title>Map | MCHostPanel</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
<link rel="stylesheet" href="css/style.css">
<meta name="author" content="Alan Hardman [phpizza.com]">
</head>
<body>
<?php require 'inc/top.php'; ?>
<div class="tab-content">
	<div class="tab-pane active">
		<div class="container-fluid">
			<div class="row-fluid">
			<?php
				if(is_file($user["home"] . "/plugins/dynmap/configuration.txt")) {
					$config = spyc_load_file($user["home"] . "/plugins/dynmap/configuration.txt");
					$port = $config["webserver-port"];
			?>
				<iframe style="width: 100%; height: 600px;" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:<?php echo $port; ?>/"></iframe>
			<?php } else { ?>
				<p class="alert alert-danger">No dynmap configuration found.</p>
			<?php } ?>
			</div>
		</div>
	</div>
</div>
</body>
</html>
