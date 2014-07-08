<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<a class="brand" href="dashboard.php">MC<strong>Host</strong>Panel</a>
		<ul class="nav pull-right">
<?php if($_SESSION['is_admin'] || $user['role']=='admin') { ?>
			<li><a href="admin.php">Administration</a></li>
<?php } ?>
			<li><a>Logged in as <strong><?php echo $user['user']; ?></strong></a></li>
			<li><img src="http://alanaktion.net/mcface.php?user=<?php echo urlencode($user['user']); ?>" alt="<?php echo $user['user']; ?>"></li>
			<li><a href="./?logout"><i class="icon-off icon-white"></i> Log Out</a></li>
		</ul>
	</div>
</div>
<ul class="nav nav-tabs" id="myTab">
	<li <?php echo basename($_SERVER["SCRIPT_NAME"]) == "dashboard.php" ? 'class="active"' : ""; ?>><a href="dashboard.php">Dashboard</a></li>
	<li <?php echo basename($_SERVER["SCRIPT_NAME"]) == "files.php" ? 'class="active"' : ""; ?>><a href="files.php">File Manager</a></li>
	<li <?php echo basename($_SERVER["SCRIPT_NAME"]) == "console.php" ? 'class="active"' : ""; ?>><a href="console.php">Console</a></li>
	<?php if(is_file($user["home"] . "/plugins/dynmap/configuration.txt")) { ?>
		<li <?php echo basename($_SERVER["SCRIPT_NAME"]) == "map.php" ? 'class="active"' : ""; ?>><a href="map.php">Map&ensp;<span class="badge">BETA</span></a></li>
	<?php } ?>
</ul>
