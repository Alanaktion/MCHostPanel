<?php
require_once 'inc/lib.php';

session_start();
if (!$_SESSION['user'] && !$user = user_info($_SESSION['user'])) {
	// Not logged in, redirect to login page
	header('Location: .');
	exit('Not Authorized');
} elseif (!$_SESSION['is_admin'] && $user['role'] != 'admin') {
	// Not an admin, redirect to login page
	header('Location: .');
	exit('Not Authorized');
}

// Switch users
if ($_POST['action'] == 'user-switch' && $_POST['user']) {
	$_SESSION['is_admin'] = true;
	$_SESSION['user'] = $_POST['user'];
	header('Location: .');
	exit('Switching Users');
}

// Add new user
if ($_POST['action'] == 'user-add')
	user_add($_POST['user'], $_POST['pass'], $_POST['role'], $_POST['dir'], $_POST['ram']);

// Start a server
if ($_POST['action'] == 'server-start') {
	$stu = user_info($_POST['user']);
	if (!server_running($stu['user']))
		server_start($stu['user']);
}

// Kill a server
if ($_POST['action'] == 'server-stop')
	if ($_POST['user'] == 'ALL')
		server_kill_all();
	else
		server_kill($_POST['user']);

?><!doctype html>
<html>
<head>
	<title>Administration | MCHostPanel</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<link rel="stylesheet" href="css/style.css">
	<meta name="author" content="Alan Hardman (http://alanaktion.com)">
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			window.setTimeout(function () {
				$('.alert-success,.alert-error').fadeOut();
			}, 3000);
			$('#frm-killall').submit(function () {
				return confirm('Are you sure you want to KILL EVERY SERVER?\nServers will not save any new data, and all connected players will be disconnected!');
			});
		});
	</script>
</head>
<body>
<?php require 'inc/top.php'; ?>
<div class="container-fluid">
	<h1 class="pull-left">Administration</h1>
	<?php if ($_POST['action'] == 'user-add') { ?>
		<p class="alert alert-success pull-right"><i class="icon-ok"></i> User added successfully.</p>
	<?php } elseif ($_POST['action'] == 'server-start') { ?>
		<p class="alert alert-success pull-right"><i class="icon-ok"></i> Server started.</p>
	<?php } elseif ($_POST['action'] == 'server-stop') { ?>
		<p class="alert alert-success pull-right"><i class="icon-ok"></i> Server killed.</p>
	<?php } ?>
	<div class="clearfix"></div>
	<div class="row-fluid">
		<div class="span8">
			<h3>Running Servers</h3>
			<pre>Running as user: <?php echo `whoami` . "\n" . `screen -ls`; ?></pre>
			<form action="admin.php" method="post">
				<input type="hidden" name="action" value="server-start">
				<select name="user" style="vertical-align: top;">
					<optgroup label="Users">
						<?php
						$ul = user_list();
						foreach ($ul as $u)
							echo '<option value="' . $u . '">' . $u . '</option>';
						?>
					</optgroup>
				</select>
				<button type="submit" class="btn btn-success">Start Server</button>
			</form>
			<form action="admin.php" method="post">
				<input type="hidden" name="action" value="server-stop">
				<select name="user" style="vertical-align: top;">
					<option value="ALL">All Servers</option>
					<optgroup label="Users">
						<?php
						$ul = user_list();
						foreach ($ul as $u)
							echo '<option value="' . $u . '">' . $u . '</option>';
						?>
					</optgroup>
				</select>
				<button type="submit" class="btn btn-danger">Kill Server</button>
			</form>
			<form action="admin.php" method="post">
				<legend>Switch to a User</legend>
				<input type="hidden" name="action" value="user-switch">
				<select name="user" style="vertical-align: top;">
					<?php
					$ul = user_list();
					foreach ($ul as $u)
						echo '<option value="' . $u . '">' . $u . '</option>';
					?>
				</select>
				<button type="submit" class="btn btn-danger">Log In</button>
			</form>
		</div>
		<div class="span4">
			<form action="admin.php" method="post" autocomplete="off">
				<input type="hidden" name="action" value="user-add">
				<legend>Add New User</legend>
				<div class="control-group">
					<label class="control-label" for="user">Username</label>

					<div class="controls">
						<div class="input-prepend">
							<span class="add-on"><i class="icon-user"></i></span>
							<input class="span4" type="text" name="user" id="user">
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="pass">Password</label>

					<div class="controls">
						<div class="input-prepend">
							<span class="add-on"><i class="icon-lock"></i></span>
							<input class="span4" type="password" name="pass" id="pass">
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="dir">Home Directory</label>

					<div class="controls">
						<div class="input-prepend">
							<span class="add-on"><i class="icon-folder-open"></i></span>
							<input class="span10" type="text" name="dir" id="dir" value="<?php echo strtr(dirname(__FILE__), '\\', '/'); ?>">
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="ram">Server Memory</label>

					<div class="controls">
						<div class="input-append">
							<input class="span3" type="number" name="ram" id="ram" value="512">
							<span class="add-on">MB</span>
						</div>
						<span class="text-info">0 MB = No Server</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="port">Server Port</label>

					<div class="controls">
						<input class="span3" type="number" name="port" id="port" value="25565">
						<span class="text-info">0 = No Server</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="role">User Role</label>

					<div class="controls">
						<select name="role" id="role" class="span4">
							<option value="user" selected>User</option>
							<option value="admin">Administrator</option>
						</select>
					</div>
				</div>
				<button type="submit" class="btn btn-primary">Add User</button>
			</form>
		</div>
	</div>
</div>
</body>
</html>