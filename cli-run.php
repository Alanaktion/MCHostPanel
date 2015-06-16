<?php
// Command-line single-action interface, designed for cron jobs
// Pass parameteres to start/stop servers, more features coming later.

// WARNING: This interface gives direct access to all servers, DO NOT allow any user to access this interface.
// If you are the only one with shell access to your server, this is already protected, otherwise ensure only you can read/execute this file.

/* Example usage:
Start a server:   php cli-run.php action=start server=alanaktion
Stop a server:    php cli-run.php action=stop server=alanaktion
Restart a server: php cli-run.php action=restart server=alanaktion
Kill a server:    php cli-run.php action=kill server=alanaktion
*/

// Verify running from command line
if(php_sapi_name() !== 'cli') {
	die();
}

// Parse parameters into $_GET superglobal
parse_str(implode('&', array_slice($argv, 1)), $_GET);

// Verify an action was recieved
if(empty($_GET['action'])) {
	die("No action specified. Example usage: php -f action=start server=alanaktion");
}

// Initialize core
chdir(dirname(__FILE__));
require_once 'inc/lib.php';

// Handle actions
switch($_GET['action']) {
	case "start":
		server_start($_GET['server']);
		break;
	case "stop":
		server_stop($_GET['server']);
		break;
	case "restart":
		server_stop($_GET['server']);
		server_start($_GET['server']);
		break;
	case "kill":
		server_kill($_GET['server']);
		break;
	default:
		die("Unknown action: {$_GET['action']}");
}
