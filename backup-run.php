<?php 
require_once dirname(__FILE__) . '/inc/lib.php';

//CLI overwrites
if (PHP_SAPI !== 'cli') {
	error_log("MCHostPanel Backup: Attempted to run backup-run.php over HTTP!");
	exit("Invalid access type\r\n");
}

if(!isset($argv[2])) {
	error_log("MCHostPanel Backup: No secret supplied!");
	exit("No user supplied!\r\n");
}

if(!isset($argv[1])) {
	error_log("MCHostPanel Backup: No user supplied!");
	exit("No user supplied!\r\n");
}

if(!isset($argv[3])) {
	error_log("MCHostPanel Backup: No backup auto-delete supplied!");
	exit("No auto-delete supplied!\r\n");
}

$name = $argv[1];
$secret = $argv[2];
$delete = $argv[3];

if (!$user = user_info($name)) {
	// Clean out the supplied user just incase
	$user = preg_replace('/[^A-Za-z0-9\- ]/', '', $name);
	
	// User does not exist, redirect to login page
	error_log("MCHostPanel Backup: '" . $user . "' user does not exist!");
	exit('Not Authorized\r\n');
}

//Make sure this page is run via cron and not from URL guessing
if($secret != hash("sha256", $user['pass'])) {
	error_log("MCHostPanel Backup: Invalid secret!");
	exit('Not Authorized\r\n');
}

if(!server_running($user['user'])) {
	exit('Server not running\r\n');
}

server_cmd($user['user'], "/save-all");
//Give the server a chance to save
sleep(30);
//Prevent auto-saves while we run the backup
server_cmd($user['user'], "/save-off");

if(!is_dir($user['home'] . "/" . "backups")){
	mkdir($user['home'] . "/" . "backups");
}

$timeout = intval($delete) * 60 * 60; //Convert to seconds

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
	exit("Exception : " . $e . "\r\n");
}

//Turn auto-saves back on
server_cmd($user['user'], "/save-on");

echo "MCHostPanel Backup Success\r\n";
?>
