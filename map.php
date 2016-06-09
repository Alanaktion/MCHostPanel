<?php
require_once 'inc/lib.php';
require_once 'inc/Spyc.php';

session_start();
if (empty($_SESSION['user']) || !$user = user_info($_SESSION['user'])) {
	// Not logged in, redirect to login page
	header('Location: .');
	exit('Not Authorized');
}

?><!doctype html>
<html>
<head>
	<title>Map | MCHostPanel</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<link rel="stylesheet" href="css/style.css">
	<meta name="author" content="Alan Hardman <alan@phpizza.com>">
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
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
				<iframe id="map" style="width: 100%; height: 600px; border: none;" frameborder="0" src="http://<?php echo $_SERVER['HTTP_HOST']; ?>:<?php echo $port; ?>/"></iframe>
			<?php } else { ?>
				<p class="alert alert-danger">No dynmap configuration found.</p>
			<?php } ?>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function () {

		// Fix sizing
		$('#map').css('height', $(window).height() - 200 + 'px');

		// Keep sizing correct
		$(document).resize(function () {
			$('#map').css('height', $(window).height() - 200 + 'px');
		});

	});
</script>
</body>
</html>
