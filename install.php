<?php
	if(is_file('.installed')) {
		header('Location: index.php');
		exit('Already installed.');
	}
	require 'config.php';
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
				<form action="install.php" method="post">

				</form>
			</div>
		</div>
	</div>
</body>
</html>
