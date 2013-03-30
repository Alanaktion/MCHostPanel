<?php
require_once 'inc/lib.php';

session_start();
if(!$_SESSION['user'] || !$user = user_info($_SESSION['user'])) {
	// Not logged in, redirect to login page
	header('Location: .');
	exit('Not Authorized');
}

?><!doctype html>
<html>
<head>
	<title>Console | MCHostPanel</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
	<link rel="stylesheet" href="css/style.css">
	<meta name="author" content="Alan Hardman (http://alanaktion.com)">
	<style type="text/css">
		/* .tab-content,.nav-tabs > li.active > a,.nav-tabs > li.active > a:hover, */
		#cmd,#log{background-color:#000;color:#fff;}
		#cmd,#log{box-sizing:border-box;-moz-box-sizing:border-box;width:100%;}
		#log{overflow-y:scroll;}
		#cmd{height:30px;}
		form{margin:0;}
	</style>
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript">
		function refreshLog() {
			updateStatus();
			$.post('ajax.php',{
				req: 'server_log'
			},function(data){
				if($('#log').scrollTop()==$('#log')[0].scrollHeight) {
					$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
				} else {
					$('#log').html(data);
				}
				window.setTimeout('refreshLog();',1000);
			});
		}
		
		function refreshLogOnce() {
			$.post('ajax.php',{
				req: 'server_log'
			},function(data){
				$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
			});
		}
		
		function updateStatus() {
			$.post('ajax.php',{
				req: 'server_running'
			},function(data){
				if(data) {
					$('#cmd').prop('disabled',false);
				} else {
					$('#cmd').prop('disabled',true);
				}
			},'json');
		}
		
		$(document).ready(function(){
			
			// Send commands with form onSubmit
			$('#frm-cmd').submit(function(){
				$.post('ajax.php',{
					req: 'server_cmd',
					cmd: $('#cmd').val()
				},function(){
					$('#cmd').val('').prop('disabled',false);
					refreshLogOnce();
				});
				$('#cmd').prop('disabled',true);
				return false;
			});
			
			// Fix sizing
			$('#log').css('height',$(window).height()-200+'px');
			
			// Check if server is running
			updateStatus();
			
			// Initialize log
			$.post('ajax.php',{
				req: 'server_log'
			},function(data){
				$('#log').html(data).scrollTop($('#log')[0].scrollHeight);
				window.setTimeout('refreshLog();',1000);
			});
			
			// Keep sizing correct
			$(document).resize(function(){
				$('#log').css('height',$(window).height()-190+'px');
			});
			
		});
	</script>
</head>
<body>
<?php require 'inc/top.php'; ?>
	<ul class="nav nav-tabs" id="myTab">
		<li><a href="dashboard.php">Dashboard</a></li>
		<li><a href="files.php">File Manager</a></li>
		<li class="active"><a href="console.php">Console</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active">
<?php if($user['ram']) { ?>
			<pre id="log" class="well well-small"></pre>
			<form id="frm-cmd">
				<input type="text" id="cmd" name="cmd" maxlength="250" autofocus>
			</form>
<?php
	} else
		echo '<p class="alert alert-info">Your account does not have a server.</p>';
?>
		</div>
	</div>
</body>
</html>