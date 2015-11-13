<nav class="navbar navbar-inverse navbar-static-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#topnav" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="dashboard.php"><?php echo $config['site_name']; ?></a>
		</div>
		<div class="collapse navbar-collapse" id="topnav">
			<ul class="nav navbar-nav">
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="files.php">Files</a></li>
				<li><a href="console.php">Console</a></li>
				<?php /*if($server->hasMap()) { ?>
					<li><a href="map.php">Map</a></li>
				<?php }*/ ?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php if($user->admin) { ?>
					<li><a href="admin.php">Administration</a></li>
				<?php } ?>
				<li><a href="login.php?logout=1">Log Out</a></li>
			</ul>
		</div>
	</div>
</nav>
