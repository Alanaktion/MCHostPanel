<?php
require_once 'inc/lib.php';

session_start();

if ($_SESSION['user']) {

	if (!$user = user_info($_SESSION['user'])) {
		// User does not exist, redirect to login page
		header('Location: .');
		exit('Not Authorized');
	}

} elseif ($_POST['user'] && $_POST['pass']) {

	// Get user data
	$user = user_info($_POST['user']);

	$_SESSION['is_admin'] = $user['role'] == 'admin';

	// Check user exists and password is good
	if (!$user || !bcrypt_verify($_POST['pass'], $user['pass'])) {
		// Login failure, redirect to login page
		header('Location: ./?error=badlogin');
		exit('Not Authorized');
	}

	// Current user is valid
	$_SESSION['user'] = $user['user'];

} else {

	// Not logged in, redirect to login page
	header('Location: .');
	exit('Not Authorized');

}
?><!doctype html>
<html>
<head>
	<title>Dashboard | MCHostPanel</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<link rel="stylesheet" href="css/style.css">
	<meta name="author" content="Alan Hardman [phpizza.com]">
	<style type="text/css">
		#cmd {
			height: 30px;
		}
		form {
			margin: 0;
		}
	</style>
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript">
		function updateStatus(once) {
			$.post('ajax.php', {
				req: 'server_running'
			}, function (data) {
				if (data) {
					$('#lbl-status').text('Running').addClass('label-success').removeClass('label-important');
					$('#btn-srv-start').prop('disabled', true);
					$('#btn-srv-stop,#btn-srv-restart').prop('disabled', false);
					$('#cmd').prop('disabled', false);
				} else {
					$('#lbl-status').text('Stopped').addClass('label-important').removeClass('label-success');
					$('#btn-srv-start').prop('disabled', false);
					$('#btn-srv-stop,#btn-srv-restart').prop('disabled', true);
					$('#cmd').prop('disabled', true);
				}
			}, 'json');
			if (!once)
				window.setTimeout(updateStatus, 5000);
		}
		function updatePlayers() {
			$.post('ajax.php', {
				req: 'players'
			}, function (data) {
				if (data.error) {
					$('#lbl-players').text('Unknown').attr('title', 'Enable Query in server.properties to see player information').tooltip();
				} else {
					try{
						console.log(data);
					} catch(ex) {}

					if(data.players === false) {
						$('#lbl-players').text(0 + '/' + data.info.MaxPlayers);
					} else {
						$('#lbl-players').text(data.players.length + '/' + data.info.MaxPlayers);
						$('#lbl-players').append('<br/><br/>');
						$('#lbl-players').append('<legend>Player List</legend>');
					}
					$.each(data.players, function (i, val) {
						console.log(val);
						$('#lbl-players').append('<img src=' +  '<?php echo('/inc/getface.php?username=') ?>' + val + '&amp;size=24' + '/>');
						$('#lbl-players').append(" " + val);
						$('#lbl-players').append('<br/><br/>');
					});
				}
			}, 'json').error(function(){
				$('#lbl-players').text('Error');
			});
		}
		function server_start() {
			$.post('ajax.php', {
				req: 'server_start'
			}, function () {
				updateStatus(true);
			});
		}
		function server_stop(callback) {
			$.post('ajax.php', {
				req: 'server_stop'
			}, function () {
				updateStatus(true);
				if (callback)
					callback();
			});
		}
		function refreshLog() {
			updateStatus();
			$.post('ajax.php', {
				req: 'server_log'
			}, function (data) {
				if ($('#log').scrollTop() == $('#log')[0].scrollHeight) {
					$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
				} else {
					$('#log').html(data);
				}
				window.setTimeout(refreshLog, 3000);
			});
		}
		function refreshLogOnce() {
			$.post('ajax.php', {
				req: 'server_log'
			}, function (data) {
				$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
			});
		}
		$(document).ready(function () {
			updateStatus();
			updatePlayers();
			$('button.ht').tooltip();
			$('#btn-srv-start').click(function () {
				server_start();
				$(this).prop('disabled', true).tooltip('hide');
			});
			$('#btn-srv-stop').click(function () {
				server_stop();
				$(this).prop('disabled', true).tooltip('hide');
			});
			$('#btn-srv-restart').click(function () {
				server_stop(server_start);
				$('').prop('disabled', true).tooltip('hide');
			});

			// Send commands with form onSubmit
			$('#frm-cmd').submit(function () {
				$.post('ajax.php', {
					req: 'server_cmd',
					cmd: $('#cmd').val()
				}, function () {
					$('#cmd').val('').prop('disabled', false);
					refreshLogOnce();
				});
				$('#cmd').prop('disabled', true);
				return false;
			});

			// Fix sizing
			$('#log').css('height', $(window).height() - 200 + 'px');

			// Initialize log
			$.post('ajax.php', {
				req: 'server_log'
			}, function (data) {
				$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
				window.setTimeout(refreshLog, 3000);
			});

			// Keep sizing correct
			$(document).resize(function () {
				$('#log').css('height', $(window).height() - 190 + 'px');
			});
		});
	</script>
</head>
<body>
<?php require 'inc/top.php'; ?>
<div class="tab-content">
	<div class="tab-pane active">
		<?php if ($user['ram']) { ?>
			<div class="row-fluid">
				<div class="span5">
					<div class="well">
						<legend>Server Controls</legend>
						<div class="btn-toolbar">
							<div class="btn-group">
								<button class="btn btn-large btn-primary ht" id="btn-srv-start" title="Start" disabled><i class="icon-play"></i></button>
								<button class="btn btn-large btn-danger ht" id="btn-srv-stop" title="Stop" disabled><i class="icon-stop"></i></button>
							</div>
							<div class="btn-group">
								<button class="btn btn-large btn-warning ht" id="btn-srv-restart" title="Restart" disabled><i class="icon-refresh"></i></button>
							</div>
							<!-- Lappy's a dick, so no one gets updates anymore.
							<div class="btn-group">
								<a class="btn btn-large btn-success dropdown-toggle" data-toggle="dropdown" href="#">Update <span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="update.php?s=craftbukkit">CraftBukkit</a></li>
									<li><a href="update.php?s=minecraft_server">Minecraft</a></li>
								</ul>
							</div>-->
						</div>
						<!--
						<div>Update Server:
							<div class="btn-group" style="display: inline-block;">
								<button class="btn btn-small" id="btn-upd-cb">CraftBukkit</button>
								<button class="btn btn-small" id="btn-upd-mc">Minecraft Server</button>
							</div>
						</div>
						<br>
						<p class="alert alert-info">Updating will stop your server and install the latest CraftBukkit Recommended Build or offical Minecraft server release.</p>
						-->
					</div>
					<div class="well">
						<legend>Server Information</legend>
						<p><b>Server Status:</b> <span class="label" id="lbl-status">Checking&hellip;</span><br>
							<b>IP:</b> <?php echo KT_LOCAL_IP . ':' . $user['port']; ?><br>
							<b>RAM:</b> <?php echo $user['ram'] . 'MB'; ?><br>
							<b>Players:</b> <span id="lbl-players">Checking&hellip;</span>
						</p>
						<div class="player-list"></div>
					</div>
					<footer class="muted">&copy; <?php echo date('Y'); ?> Alan Hardman</footer>
				</div>
				<div class="span7">
					<pre id="log" class="well well-small"></pre>
					<form id="frm-cmd">
						<input type="text" id="cmd" name="cmd" maxlength="250" placeholder="Enter a command" autofocus>
					</form>
				</div>
			</div>
		<?php
		} else
			echo '
			<p class="alert alert-info">Your account does not have a server.</p>
			<footer class="muted">&copy; ' . date('Y') . ' Alan Hardman</footer>
';
		?>
	</div>
</div>
</body>
</html>
