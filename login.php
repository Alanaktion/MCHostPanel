<?php
if(!is_file('.installed')) {
	header('Location: install.php');
	exit;
}

require_once 'config.php';
session_start();
if(!empty($_GET['logout'])) {
	unset($_SESSION['user']);
}
if(!empty($_SESSION['user'])) {
	header('Location: dashboard.php');
}

// Handle login submission
if(!empty($_POST['user'])) {
	$username = preg_replace('/[^a-z0-9_-]/iu', '', $_POST['user']);
	if(is_file('data/' . $username . '.json')) {
		$file = file_get_contents('data/' . $username . '.json');
		$user = json_decode($file);
		if(sha1($user->salt . $_POST['pass']) == $user->pass) {
			$_SESSION['user'] = $user->user;
			header('Location: dashboard.php');
			exit;
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include 'app/view/head.php'; ?>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3">
				<form action="login.php" method="post">
					<div class="hidden-xs" style="height: 60px;"></div>
					<h1 class="h3 text-center"><?php echo $config['site_name']; ?></h1>
					<?php if(!empty($_POST['user'])) { ?>
						<p class="alert alert-danger">Invalid username or password.</p>
					<?php } ?>
					<fieldset class="form-group">
						<label class="sr-only" for="username">Username</label>
						<input type="text" class="form-control" id="username" name="user" placeholder="Username">
					</fieldset>
					<fieldset class="form-group">
						<label class="sr-only" for="password">Password</label>
						<input type="password" class="form-control" id="password" name="pass" placeholder="Password">
					</fieldset>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">Log In</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
