<?php
if(!is_file('.installed')) {
	header('Location: install.php');
	exit;
}

require_once 'config.php';
session_start();
if(empty($_SESSION['user'])) {
	header('Location: login.php');
}

?>
<!DOCTYPE html>
<html>
<head>
	<?php include 'app/view/head.php'; ?>
</head>
<body>
	<?php include 'app/view/nav.php'; ?>
	<div class="container">

	</div>
</body>
</html>
