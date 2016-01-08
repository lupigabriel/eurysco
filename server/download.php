<?php

include(str_replace('\\server', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_server.php');

set_time_limit(60);

if (isset($_POST['type']) && isset($_POST['download'])) {
	$filelocation = '';
	if ($_POST['type'] == 'groups') { $filelocation = $euryscoinstallpath . '\\groups\\' . $_POST['download']; }
	if ($_POST['type'] == 'users') { $filelocation = $euryscoinstallpath . '\\users\\' . $_POST['download']; }
	if ($_POST['type'] == 'settings' && isset($_POST['computername'])) { $filelocation = $euryscoinstallpath . '\\nodes\\' . $_POST['computername'] . '\\' . $_POST['download']; }
	if ($filelocation == '') { exit; }
	if (file_exists($filelocation)) {
		if (false != ($handler = fopen($filelocation, 'r'))) {
			ob_end_clean();
			ob_start();
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $_POST['download']);
			header('Content-Transfer-Encoding: chunked');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Cache-Control: no-cache');
			header('Pragma: no-cache');
			$filesize = filesize($filelocation);
			header('Content-Length: ' . $filesize);
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
		}
		if ($_POST['type'] == 'settings') { @unlink($filelocation); }
		exit;
	}
}



foreach (get_defined_vars() as $key=>$val) {
	if ($key != '_GET' && $key != '_POST' && $key != '_COOKIE' && $key != '_FILES' && $key != '_SERVER' && $key != '_SESSION' && $key != '_ENV') {
		$$key = null;
		unset($$key);
	}
}

if (extension_loaded('zlib')) { ob_end_flush(); }

?>