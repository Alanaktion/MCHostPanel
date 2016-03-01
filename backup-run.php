<?php 
require_once 'inc/lib.php';

if(!isset($_GET['user'])) {
	error_log("MCHostPanel Backup: No user supplied!");
	exit("No user supplied!");
}

if(!isset($_GET['delete'])) {
	error_log("MCHostPanel Backup: No backup auto-delete supplied!");
	exit("No auto-delete supplied!");
}

if (!$user = user_info($_GET['user'])) {
	// Clean out the supplied user just incase
	$user = preg_replace('/[^A-Za-z0-9\- ]/', '', $_GET['user']);
	
	// User does not exist, redirect to login page
	error_log("MCHostPanel Backup: '" . $user . "' user does not exist!");
	exit('Not Authorized');
}

if(!server_running($user['user'])) {
	exit('Server not running');
}

server_cmd($user['user'], "/save-all");
//Give the server a chance to save
sleep(15);
//Prevent auto-saves while we run the backup
server_cmd($user['user'], "/save-off");

if(!is_dir($user['home'] . "/" . "backups")){
	mkdir($user['home'] . "/" . "backups");
}

$timeout = intval($_GET['delete']) * 60 * 60; //Convert to seconds

if($timeout !== 0) {
	$backups = array_diff(scandir($user['home'] . "/" . "backups/"), array('.', '..'));
	
	foreach($backups as $backup) {
		$timeCreated = filectime($user['home'] . "/" . "backups/" . $backup);
		//Times up!
		if($timeout + $timeCreated <= time()) {
			unlink($user['home'] . "/" . "backups/" . $backup);
		}
	}
}
try {
	$archiveFile = date('Y-m-d') . " - " . time() . " - Settings.tar";
	//$worldArchiveFile = date('Y-m-d') . " - " . time() . " - World.tar";

	$phar = new PharData($user['home'] . "/" . "backups/" . $archiveFile);
	
	$phar->buildFromDirectory($user['home'], '/^((?!backups).)*$/');
	$phar->compress(Phar::GZ);
	
	//Delete the .tar file since now we have a .tar.gz
	unlink($user['home'] . "/" . "backups/" . $archiveFile);
	
} catch (Exception $e) {
	error_log("MCHostPanel Backup: '" . $user . "' Backup Failure!\r\nException : " . $e);
	exit("Exception : " . $e);
}

echo "MCHostPanel Backup Success";
?>
