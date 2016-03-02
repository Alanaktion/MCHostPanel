<?php
require_once 'inc/lib.php';

session_start();
if (empty($_SESSION['user']) || !$user = user_info($_SESSION['user'])) {
	// Not logged in, redirect to login page
	header('Location: .');
	exit('Not Authorized');
}

// Upload files
if (isset($_FILES['files']) && isset($_POST['dir'])) {
	$uploads = $_FILES['files'];
	$numfiles = count($uploads['name']);
	for ($i = 0; $i < $numfiles; $i++)
		move_uploaded_file($uploads['tmp_name'][$i], $user['home'] . $_POST['dir'] . '/' . $uploads['name'][$i]);
}

?><!doctype html>
<html>
<head>
	<title>Upload Files | MCHostPanel</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-ui">
	<meta name="author" content="Alan Hardman (http://phpizza.com)">
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<style type="text/css">
		body {
			background: none;
			background-color: transparent;
			overflow: hidden;
		}
	</style>
</head>
<body>
<?php if ($numfiles) { ?>
	<p class="alert alert-success"><?php echo $numfiles; ?> file<?php echo $numfiles > 1 ? 's were' : ' was'; ?> uploaded successfully.</p>
	<div style="position:absolute;bottom:0;right:0;">
		<button type="button" class="btn" onclick="top.$('#modal-upload').modal('hide');top.loaddir('<?php echo $_POST['dir']; ?>')">Close</button>
	</div>
<?php } else { ?>
	<form action="uploader.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="dir" value="<?php echo $_REQUEST['dir']; ?>">

		<p>Select files and click Upload to begin the upload process.</p>
		<input type="file" name="files[]" id="files" style="width:90%;" multiple>

		<div style="position:absolute;bottom:0;right:0;">
			<input type="reset" class="btn" value="Cancel" onclick="top.$('#modal-upload').modal('hide');">
			<button type="submit" class="btn btn-primary">Upload</button>
		</div>
	</form>
<?php } ?>
</body>
</html>
