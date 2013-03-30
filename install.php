<?php
require_once 'inc/lib.php';

if($_POST['user']) {
	session_start();
	user_add($_POST['user'],$_POST['pass'],'admin',$_POST['dir']);
	$_SESSION['user'] = clean_alphanum($_POST['user']);
}

?><!doctype html>
<html>
<head>
	<title>Install MCHostPanel</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<meta name="author" content="Alan Hardman (http://alanaktion.com)">
</head>
<body>
<?php if($_POST['user']) { ?>
	<div class="modal">
		<div class="modal-header">
			<h3>Install MCHostPanel</h3>
		</div>
		<div class="modal-body">
			<p>MCHostPanel has been installed, and you are now logged in.</p>
			<p class="alert alert-error">Delete <code>install.php</code>, otherwise anyone can create an administrator user!</p>
		</div>
		<div class="modal-footer">
			<a class="btn btn-success" href="panel.php">Continue to Panel</a>
		</div>
	</div>
<?php } else { ?>
	<form class="modal form-horizontal" action="install.php" method="post">
		<div class="modal-header">
			<h3>Install MCHostPanel</h3>
		</div>
		<div class="modal-body">
			<legend>Administrator User</legend>
			<div class="control-group">
				<label class="control-label" for="user">Username</label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-user"></i></span>
						<input class="span2" type="text" name="user" id="user">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="pass">Password</label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-lock"></i></span>
						<input class="span2" type="password" name="pass" id="pass">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dir">Home Directory</label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-folder-open"></i></span>
						<input class="span2" type="text" name="dir" id="dir" value="<?php echo strtr(dirname(__FILE__),'\\','/'); ?>">
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
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" type="submit">Install and Log In</button>
		</div>
	</form>
<?php } ?>
</body>