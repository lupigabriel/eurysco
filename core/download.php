<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/explorer.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/registry.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/tail.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/7zip.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/audit.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_eventviewer.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_inventory.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_nagios.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_netstat.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_processes.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_programs.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_scheduler.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_services.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes.php')) { exit; }

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

if (isset($_SESSION['agentstatus'])) {
	$agentstatus = $_SESSION['agentstatus'];
} else {
	$agentstatus = '';
}

set_time_limit(600);

if (isset($_GET['path']) && isset($_GET['download'])) {
	$filelocation = $_GET['path'] . '\\' . $_GET['download'];
	if (file_exists($filelocation)) {
		if (false != ($handler = fopen($filelocation, 'r'))) {
			ob_end_clean();
			ob_start();
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $_GET['download']);
			header('Content-Transfer-Encoding: chunked');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Cache-Control: no-cache');
			header('Pragma: no-cache');
			$filesize = filesize($filelocation);
			if ($filesize < 0) {
				$filesize = exec('explorerfs.cmd "' . $filelocation . '"', $errorarray, $errorlevel);
				if ($errorlevel != 0) {
					$filesize = -1;
				}
			}
			if ($filesize > -1) { header('Content-Length: ' . $filesize); }
			while (false != ($chunk = fread($handler, 1048576))) {
				if ($chunk != '') {
					echo $chunk;
					ob_flush();
					flush();
				} else {
					ob_flush();
					flush();
					break;
				}
			}
			fclose($handler);
			$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     download request for file "' . str_replace('\\\\', '\\', $filelocation) . '" completed';
			include('/auditlog.php');
		}
		exit;
	}
}

?>