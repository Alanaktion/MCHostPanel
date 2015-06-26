<?php
if(!is_file('.installed')) {
	header('Location: install.php');
	exit;
}

session_start();
if(!empty($_SESSION['user'])) {
	header('Location: dashboard.php');
} else {
	header('Location: login.php');
}
