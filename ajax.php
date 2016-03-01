<?php
require_once 'inc/lib.php';

session_start();
if (!$user = user_info($_SESSION['user']))
	exit();

switch ($_POST['req']) {
	case 'dir':
		// Initial vars
		$dirs = array();
		$files = array();

		// Get directory contents
		$h = opendir($user['home'] . $_POST['dir']);
		while (false !== ($f = readdir($h)))
			if ($f != '.' && $f != '..')
				if (is_dir($user['home'] . $_POST['dir'] . '/' . $f))
					$dirs[] = $f;
				elseif (is_file($user['home'] . $_POST['dir'] . '/' . $f))
					$files[] = $f;
		closedir($h);
		unset($f);

		// Sort data
		sort($dirs);
		sort($files);

		// Get file sizes
		$sizes = array();
		foreach ($files as $f)
			$sizes[] = filesize($user['home'] . $_POST['dir'] . '/' . $f);

		// Output data
		echo json_encode(array(
			'dirs' => $dirs,
			'files' => $files,
			'sizes' => $sizes
		));

		break;
	case 'file_get':
		if (is_file($user['home'] . $_POST['file']))
			echo file_get_contents($user['home'] . $_POST['file']);
		break;
	case 'file_put':
		if (is_file($user['home'] . $_POST['file']))
			file_put_contents($user['home'] . $_POST['file'], $_POST['data']);
		break;
	case 'delete':
		foreach ($_POST['files'] as $f)
			if (is_file($user['home'] . $f))
				unlink($user['home'] . $f);
		break;
	case 'rename':
		file_rename($_POST['path'], $_POST['newname'], $user['home']);
		break;
	case 'cron_exists':
		header('Content-type: application/json');
		echo json_encode(check_cron_exists($_POST['user']));
		break;
	case 'get_cron':
		header('Content-type: application/json');
		echo json_encode(get_cron($_POST['user']));
		break;
	case 'server_start':
		echo server_start($user['user']);
		break;
	case 'server_cmd':
		server_cmd($user['user'], $_POST['cmd']);
		break;
	case 'server_stop':
		server_stop($user['user']);
		break;
	case 'server_kill':
		server_kill($user['user']);
		break;
	case 'server_running':
		echo json_encode(server_running($user['user']));
		break;
	case 'server_log':
		/*if($files = glob($user['home'] . "screenlog.?*")) {
			// Prefer GNU screen log
			echo mclogparse2(file_backread($user['home']));
		} else*/
		if(is_file($user['home'] . "/logs/latest.log")) {
			// 1.7 logs
			echo mclogparse2(file_backread($user['home'] . '/logs/latest.log', 64));
		} elseif(is_file($user['home'] . "/server.log")) {
			// 1.6 and earlier
			echo mclogparse2(file_backread($user['home'] . '/server.log', 64));
		} else {
			echo "No log file found.";
		}
		break;
	case 'server_log_bytes':
		header('Content-type: application/json');

		// Find log file
		if(is_file($user['home'] . '/logs/latest.log')) {
			$file = $user['home'] . '/logs/latest.log';
		} elseif(is_file($user['home'] . '/server.log')) {
			$file = $user['home'] . '/server.log';
		} else {
			exit(json_encode(array('error' => 1, 'msg' => 'No log file found.')));
		}

		// Get requested byte range
		$start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
		$end = isset($_REQUEST['end']) ? intval($_REQUEST['end']) : null;

		$data = @file_get_contents($file, false, null, $start, $end);

		$return = array(
			'start' => $start,
			'end' => $start + strlen($data),
			'data' => $data,
		);

		if($data === false) {
			$data = file_get_contents($file, false, null, 0, 30*1024);
			$return = array(
				'error' => 2,
				'msg' => 'Failed to requested bytes from the log file. Returned first 30 KB.',
				'start' => 0,
				'end' => strlen($data),
				'data' => $data,
			);
		}

		echo json_encode($return);

	case 'players':
		require_once 'inc/MinecraftQuery.class.php';
		$mq = new MinecraftQuery();
		try {
			$mq->Connect(KT_LOCAL_IP, $user['port'], 2); // 2 second timeout
		} catch (MinecraftQueryException $ex) {
			echo json_encode(array('error' => 1, 'msg' => $ex->getMessage()));
			die();
		}

		$data = array(
			'info' => $mq->GetInfo(),
			'players' => $mq->GetPlayers()
		);

		echo json_encode($data);
		break;
	case 'set_jar':
		$result = user_modify($user['user'], $user['pass'], $user['role'], $user['home'], $user['ram'], $user['port'], $_POST['jar']);
		echo json_encode($result);
		break;
}

?>
