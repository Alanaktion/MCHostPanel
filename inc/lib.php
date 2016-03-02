<?php
/**
 * Copyright (c) Alan Hardman 2013-2016
 * @author Alan Hardman <alan@phpizza.com>
 *
 * Note: I used ASCII art text with the Colossal font
 * to take advantage of Sublime Text's amazing minimap.
 * This groups sections in a way that allows me to see
 * where any given code section is at a glance.
 *
 * ...or I could just use Ctrl+R. Sublime Text rocks.
 */

require_once 'data/config.php';
require_once 'inc/mclogparse.inc.php';

/*
8888888888 d8b 888                                     888
888        Y8P 888                                     888
888            888                                     888
8888888    888 888  .d88b.  .d8888b  888  888 .d8888b  888888 .d88b.  88888b.d88b.
888        888 888 d8P  Y8b 88K      888  888 88K      888   d8P  Y8b 888 "888 "88b
888        888 888 88888888 "Y8888b. 888  888 "Y8888b. 888   88888888 888  888  888
888        888 888 Y8b.          X88 Y88b 888      X88 Y88b. Y8b.     888  888  888
888        888 888  "Y8888   88888P'  "Y88888  88888P'  "Y888 "Y8888  888  888  888
                                          888
                                     Y8b d88P
                                      "Y88P"
*/

/**
 * Rename a user's file
 * @param  string $path
 * @param  string $newname
 * @param  string $home
 * @return bool
 */
function file_rename($path,$newname,$home) {
	return rename($home.$path,$home.rtrim($path,basename($path)).$newname);
}

/**
 * Download a user's file
 * @param  string  $path
 * @param  string  $home
 * @param  boolean $force
 * @return void
 */
function download($path,$home,$force = true) {
	if(is_file($home.$path) && $force) {
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($path).'";');
		header('Content-Transfer-Encoding: binary');
		readfile($home.$path);
	} elseif(is_file($home.$path)) {
		header('Content-type: '.mimetype($home.$path));
		readfile($home.$path);
	} else {
		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
		header('Status: 404 Not Found');
		$_SERVER['REDIRECT_STATUS'] = 404;
		if(!$force)
			echo 'The requested file is not available.';
	}
}

/**
 * Get a file's mime type by file name
 * @param  string $filename
 * @return string
 */
function mimetype($filename) {
	$mime_types = array(
		'txt' => 'text/plain',
		'htm' => 'text/html',
		'html' => 'text/html',
		'php' => 'text/html',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'xml' => 'application/xml',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',
		// images
		'png' => 'image/png',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'ico' => 'image/vnd.microsoft.icon',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		// archives
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		'exe' => 'application/x-msdownload',
		'msi' => 'application/x-msdownload',
		'cab' => 'application/vnd.ms-cab-compressed',
		// audio/video
		'mp3' => 'audio/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',
		// adobe
		'pdf' => 'application/pdf',
		'psd' => 'image/vnd.adobe.photoshop',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',
		// ms office
		'doc' => 'application/msword',
		'rtf' => 'application/rtf',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		// open office
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	);

	$ext = strtolower(array_pop(explode('.',$filename)));
	if(array_key_exists($ext, $mime_types)) {
		return $mime_types[$ext];
	} elseif(function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME);
		$mimetype = finfo_file($finfo, $filename);
		finfo_close($finfo);
		return $mimetype;
	} else
		return 'application/octet-stream';
}

/**
 * Get a file size using native methods when possible
 * This allows file sizes greater than 4GB to work properly on a 32-bit environment
 * @param  string $file
 * @return int
 */
function getsize($file) {
	$size = filesize($file);
	if($size < 0)
		if(!(strtoupper(substr(PHP_OS,0,3))=='WIN'))
			$size = trim(`stat -c%s $file`);
		else {
			$fsobj = new COM('Scripting.FileSystemObject');
			$f = $fsobj->GetFile($file);
			$size = $file->Size;
		}
	return $size;
}

/**
 * Helper for file_backread function
 * @param  string $haystack
 * @param  string $needle
 * @param  int    $x
 * @return string|bool
 */
function __file_backread_helper(&$haystack,$needle,$x) {
    $pos=0;$cnt=0;
    while($cnt < $x && ($pos=strpos($haystack,$needle,$pos))!==false){$pos++;$cnt++;}
    return $pos==false ? false:substr($haystack,$pos,strlen($haystack));
}

/**
 * Read n lines from the end of a file
 * @param  string $file
 * @param  int    $lines
 * @param  int    $fsize
 * @return string
 */
function file_backread($file,$lines,&$fsize=0){
    $f=fopen($file,'r');
    if(!$f)return Array();


    $splits=$lines*50;
    if($splits>10000)$splits=10000;

    $fsize=filesize($file);
    $pos=$fsize;

    $buff1=Array();
    $cnt=0;

    while($pos)
    {
        $pos=$pos-$splits;

        if($pos<0){ $splits+=$pos; $pos=0;}

        fseek($f,$pos);
        $buff=fread($f,$splits);
        if(!$buff)break;

        $lines -= substr_count($buff, "\n");

        if($lines <= 0) {
            $buff1[] = __file_backread_helper($buff,"\n",abs($lines)+1);
            break;
        }
        $buff1[] = $buff;
    }

    return str_replace("\r",'',implode('',array_reverse($buff1)));
}

/**
 * Force download of a file to the browser
 * @param  string $url
 * @param  string $path
 * @return string|bool
 */
function file_download($url,$path) {
	$file = fopen($url,'rb');
	if($file) {
		$newf = fopen($path,'wb');
		if($newf)
			while(!feof($file))
				fwrite($newf,fread($file,1024*8),1024*8);
		else
			return false;
	}

	if($file)
		fclose($file);
	else
		return false;

	if($newf)
		fclose($newf);

	return $path;
}

/**
 * Delete a folder and it's contents
 * (stack algorithm, faster than a recursive function)
 * @param  string $dirname
 * @return bool
 */
function rmdirr($dirname) {
	// Sanity check
	if(!file_exists($dirname))
		return false;

	// Simple delete for a file
	if(is_file($dirname) || is_link($dirname))
		return unlink($dirname);

	// Create and iterate stack
	$stack = array($dirname);
	while($entry = array_pop($stack)) {
		// Watch for symlinks
		if(is_link($entry)) {
			unlink($entry);
			continue;
		}

		// Attempt to remove the directory
		if(@rmdir($entry))
		continue;

		// Otherwise add it to the stack
		$stack[] = $entry;
		$dh = opendir($entry);
		while(false !== $child = readdir($dh)) {
			// Ignore pointers
			if($child === '.' || $child === '..')
				continue;

			// Unlink files and add directories to stack
			$child = $entry . DIRECTORY_SEPARATOR . $child;
			if(is_dir($child) && !is_link($child))
				$stack[] = $child;
			else
				unlink($child);
		}
		closedir($dh);
		print_r($stack);
	}

	return true;
}


/*
 .d8888b.
d88P  Y88b
888    888
888        888d888 .d88b.  88888b.
888        888P"  d88""88b 888 "88b
888    888 888    888  888 888  888
Y88b  d88P 888    Y88..88P 888  888
 "Y8888P"  888     "Y88P"  888  888
*/

/**
 * Creates a MCHostPanel cron job
 * @param string $job A fully formatted cron job
 */
function create_cron($job) {
	$output = shell_exec('crontab -l');
	file_put_contents("/tmp/crontab.txt", $output . $job . PHP_EOL);
	echo exec("crontab /tmp/crontab.txt");
}

/**
 * Deletes a MCHostPanel cron job
 * @param string $name
 */
function delete_cron($name) {
	$output = shell_exec('crontab -l');
	$output = preg_replace("/^.*backup-run\.php " . preg_quote(escapeshellarg($name)) . "(.*)[\r\n]/mi", "", $output);

	file_put_contents("/tmp/crontab.txt", $output);
	echo exec("crontab /tmp/crontab.txt");
}

/**
 * Checks if a cron job already exists for this user
 * @param string $name
 * @return bool on match
 */
function check_cron_exists($name) {
	$output = shell_exec('crontab -l');
	return (preg_match("/backup-run\.php " . preg_quote(escapeshellarg($name)) . "/i", $output));
}

/**
 * Checks if a cron job already exists and return data about it
 * @param string $name
 * @return array on match
 */
function get_cron($name) {
	if(check_cron_exists($name)) {
		$output = shell_exec('crontab -l');

		preg_match("/^.*backup-run\.php " . preg_quote(escapeshellarg($name)) . "(.*)/mi", $output, $matches);

		$parts = explode(" ", $matches[0]);

		//Spooky stuff
		$freq = explode("/", $parts[1]); //Grab the cron job date stuff
		$freq = (isset($freq[1]) ? $freq[1] : 1); //freq 1 will have numbers greater than 2 for intervals

		$delete = $parts[9];

		$ret = array();
		$ret["hrFreq"] = $freq;
		$ret['hrDeleteAfter'] = $delete;

		return $ret;
	} else {
		return array();
	}
}


/*
 .d8888b.
d88P  Y88b
Y88b.
 "Y888b.    .d88b.  888d888 888  888  .d88b.  888d888 .d8888b
    "Y88b. d8P  Y8b 888P"   888  888 d8P  Y8b 888P"   88K
      "888 88888888 888     Y88  88P 88888888 888     "Y8888b.
Y88b  d88P Y8b.     888      Y8bd8P  Y8b.     888          X88
 "Y8888P"   "Y8888  888       Y88P    "Y8888  888      88888P'
*/

/**
 * Start a server with a given username
 * @param string $name
 */
function server_start($name) {

	// Get user details
	$user = user_info($name);

	// Make sure server isn't already running
	if(server_running($user['user']))
		return false;

	// Check that server has a .jar, selecting the first .jar in the directory if one has not been set
	if(empty($user['jar'])) {
		$files = scandir($user['home']);
		foreach($files as $file) {
			if(substr($file, -4) == '.jar') {
				$jar = $file;
				break;
			}
		}
	} else {
		$jar = $user['jar'];
	}

	if(is_file($user['home'].'/'.$jar)) {

		// Verify server.properties (Prevent user from modifying port)
		if(is_file($user['home'].'/server.properties')) {
			$prop = file($user['home'].'/server.properties',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);

			// Remove any port setting
			foreach($prop as $i=>$p) {
				if(strpos($p,'server-port')!==false) {
					unset($prop[$i]);
					continue;
				}
			}

			// Add user's port
			$prop[] = 'server-port='.intval($user['port']);

			// Save properties file
			file_put_contents($user['home'].'/server.properties',implode("\n",$prop));

		} else {
			// File doesn't exist, use template from ./serverbase
			file_put_contents(
				$user['home'].'/server.properties',
				str_replace(
					'%PORT%',
					intval($user['port']),
					file_get_contents('serverbase/server.properties')
				)
			);
		}

		// Launch server process in a detached GNU Screen
		shell_exec(
			'cd '.escapeshellarg($user['home']).'; '. // Change to server directory
			sprintf(
				str_replace('craftbukkit.jar', $jar, KT_SCREEN_CMD_START), // Base command
				escapeshellarg(KT_SCREEN_NAME_PREFIX.$user['user']), // Screen Name
				intval($user['ram']/2), // Startup RAM
				$user['ram']  // Maximum RAM
			)
		);

	}
}

/**
 * Pass a command to a running server
 * @param string $name
 * @param string $cmd
 */
function server_cmd($name,$cmd) {
	shell_exec(
		sprintf(
			KT_SCREEN_CMD_EXEC, // Base command
			KT_SCREEN_NAME_PREFIX.$name, // Screen Name
			str_replace(array('\\','"'),array('\\\\','\\"'),(get_magic_quotes_gpc() ? stripslashes($cmd) : $cmd)) // Server command
		)
	);
}

/**
 * Safely shut down a server
 * @param string $name
 */
function server_stop($name) {
	shell_exec(

		// "stop" command
		sprintf(
			KT_SCREEN_CMD_EXEC, // Base command
			KT_SCREEN_NAME_PREFIX.$name, // Screen Name
			'stop' // Server command
		).';'.

		// wait 5 seconds to ensure server has saved
		'sleep 5;'.

		// kill process
		sprintf(
			KT_SCREEN_CMD_KILL, // Base command
			escapeshellarg(KT_SCREEN_NAME_PREFIX.$name) // Screen Name
		)
	);
}

/**
 * Immediately kill a server with a given username (does not save anything!)
 * @param string $name
 */
function server_kill($name) {
	$user = user_info($name);
	shell_exec(
		sprintf(
			KT_SCREEN_CMD_KILL, // Base command
			escapeshellarg(KT_SCREEN_NAME_PREFIX.$user['user']) // Screen Name
		)
	);
}

/**
 * Kill ALL RUNNING GNU-SCREENS (under the web server user)
 */
function server_kill_all() {
	shell_exec(KT_SCREEN_CMD_KILLALL);
}

/**
 * Check if a server is running
 * @param  string $name
 * @return bool
 */
function server_running($name) {
	return !!strpos(`screen -ls`, KT_SCREEN_NAME_PREFIX . $name);
}

/**
 * Creates and deletes CRON jobs that manage the server backups
 * @param string $name The users / servers name
 * @param string $action "create" or "delete" based on the form input
 * @param integer $freq 1-24 based on the hour internal to run the job. (4 = every 4 hours)
 * @param integer $deleteAfter 0+ Number of hours to keep a backup. 0 is never delete
 * @return bool
 */
function server_manage_backup($name, $action, $freq, $deleteAfter) {
	if(!$user = user_info($name)) {
		exit("Invalid user");
	}

	switch($action) {
		case "create":
			if(!check_cron_exists($name)) {

				$freq = ($freq == 1 ? "*" : "*/" . $freq);

				// A secret passed to the cron job to prevent people from guessing jobs on improper setups
				$secret = hash("sha256", $user['pass']);

				$jobFile = "php " .$_SERVER['DOCUMENT_ROOT'] . "/backup-run.php " . escapeshellarg($user['user']) . " " . escapeshellarg($secret) . " " . escapeshellarg($deleteAfter);
				$job = "0 " . $freq . " * * * " . $jobFile;

				create_cron($job);
			}
			break;
		case "delete":
			delete_cron($name);
			break;
	}
}


/*
888     888
888     888
888     888
888     888 .d8888b   .d88b.  888d888 .d8888b
888     888 88K      d8P  Y8b 888P"   88K
888     888 "Y8888b. 88888888 888     "Y8888b.
Y88b. .d88P      X88 Y8b.     888          X88
 "Y88888P"   88888P'  "Y8888  888      88888P'
*/

// Add a new user
function user_add($user,$pass,$role,$home,$ram=512,$port=25565) {

	// Prevent overwriting an existing user
	if(is_file('data/users/' . strtolower(clean_alphanum($user)) . '.json')) {
		return false;
	}

	// Create user array
	$user = array(
		'user' => clean_alphanum($user),
		'pass' => bcrypt($pass),
		'role' => $role,
		'home' => rtrim(strtr($home, "\\", '/'), '/'),
		'ram'  => intval($ram),
		'port' => intval($port)
	);

	// Write to file
	file_put_contents('data/users/' . strtolower(clean_alphanum($user['user'])) . '.json', json_encode($user));

	//check users home directory exists. if it doesn't we create it.
	if (!file_exists($_POST['dir'])) {
    mkdir($_POST['dir'], 0777, true);
	}
}

// Delete a user
function user_delete($user) {
	// Delete user file if it exists
	if(is_file('data/users/' . strtolower(clean_alphanum($user)) . '.json')) {
		unlink('data/users/' . strtolower(clean_alphanum($user)) . '.json');
		return true;
	} else {
		return false;
	}
}

// Get user data
function user_info($user) {
	if(is_file('data/users/' . strtolower(clean_alphanum($user)) . '.json')) {
		return json_decode(file_get_contents('data/users/' . strtolower(clean_alphanum($user) . '.json')), true);
	} else {
		return false;
	}
}

// Update user data
function user_modify($user,$pass,$role,$home,$ram,$port,$jar='craftbukkit.jar') {

	// check user existence
	if(is_file('data/users/' . strtolower(clean_alphanum($user)) . '.json')) {

		// Create user array
		$user = array(
			'user' => clean_alphanum($user),
			'pass' => bcrypt($pass),
			'role' => $role,
			'home' => $home,
			'ram'  => intval($ram),
			'port' => intval($port),
			'jar'  => $jar,
		);

		// Write to file
		file_put_contents('data/users/' . strtolower(clean_alphanum($user['user'])) . '.json', json_encode($user));
		return true;
	} else {
		return false;
	}

}

// List users
function user_list() {
	$h = opendir('data/users/');
	$users = array();
	while(($f = readdir($h)) !== false)
		if($f != '.' && $f != '..' && preg_match("/\.json$/", $f))
			$users[] = preg_replace("/\.json$/", "", $f);
	closedir($h);
	return $users;
}

/*
8888888888 d8b 888 888                    d8b
888        Y8P 888 888                    Y8P
888            888 888
8888888    888 888 888888 .d88b.  888d888 888 88888b.   .d88b.
888        888 888 888   d8P  Y8b 888P"   888 888 "88b d88P"88b
888        888 888 888   88888888 888     888 888  888 888  888
888        888 888 Y88b. Y8b.     888     888 888  888 Y88b 888
888        888 888  "Y888 "Y8888  888     888 888  888  "Y88888
                                                            888
                                                       Y8b d88P
                                                        "Y88P"
*/

// Remove non-alphanumeric characters from a string
function clean_alphanum($s) {
	return preg_replace('/([^A-Za-z0-9])/','',$s);
}

// Remove non-alphabetic characters from a string
function clean_alpha($s) {
	return preg_replace('/([^A-Za-z0-9])/','',$s);
}

// Remove non-numeric characters from a string
function clean_digit($s) {
	return preg_replace('/([^0-9])/','',$s);
}

// Verify email address syntax
function check_email($email) {
	return filter_var($email,FILTER_VALIDATE_EMAIL);
}


/*
 .d8888b.                            888                                              888
d88P  Y88b                           888                                              888
888    888                           888                                              888
888        888d888 888  888 88888b.  888888 .d88b.   .d88b.  888d888 8888b.  88888b.  88888b.  888  888
888        888P"   888  888 888 "88b 888   d88""88b d88P"88b 888P"      "88b 888 "88b 888 "88b 888  888
888    888 888     888  888 888  888 888   888  888 888  888 888    .d888888 888  888 888  888 888  888
Y88b  d88P 888     Y88b 888 888 d88P Y88b. Y88..88P Y88b 888 888    888  888 888 d88P 888  888 Y88b 888
 "Y8888P"  888      "Y88888 88888P"   "Y888 "Y88P"   "Y88888 888    "Y888888 88888P"  888  888  "Y88888
                        888 888                          888                 888                    888
                   Y8b d88P 888                     Y8b d88P                 888               Y8b d88P
                    "Y88P"  888                      "Y88P"                  888                "Y88P"
*/

// Generate a Base-64 salt string
function base64_salt($len = 22) {
	$characterList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/';
	$salt = '';
	for($i=0;$i<$len;$i++)
		$salt.= $characterList{mt_rand(0,(strlen($characterList)-1))};
	return $salt;
}

// Securely encrypt a password
function bcrypt($str) {
	$salt = strtr(base64_salt(22),'+','.');
	$work = 13;
	$salt = sprintf('$2y$%s$%s',$work,$salt);
	$hash = crypt($str,$salt);
	if(strlen($hash)>13)
		return $hash;
	else
		return false;
}

// Verify a bcrypt-encyrpted string
function bcrypt_verify($str,$hash) {
	return (crypt($str,$hash) === $hash);
}

?>
