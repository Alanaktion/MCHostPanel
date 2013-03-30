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
