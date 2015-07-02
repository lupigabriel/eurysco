<?php

include('/include/init.php');

set_time_limit(60);

if (isset($_POST['type']) && isset($_FILES['file'])) {
	if ($_FILES['file']['error'] > 0) {
	} else {
		if ($_FILES['file']['size'] > 1048576) {
		} else {
			if ($_POST['type'] == 'nodes') {
				$moveerrorlevel = @move_uploaded_file($_FILES['file']['tmp_name'], str_replace('\\server', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $_POST['node'] . '\\' . $_FILES['file']['name']);
				if (isset($_POST['comp'])) {
					$fp = '';
					$fp = gzopen(str_replace('\\server', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $_POST['node'] . '\\' . $_FILES['file']['name'], 'rb');
					$bl = '';
					while (!feof($fp)) {
						$gz = gzread($fp, 2048);
						$bl = $bl . $gz;
					}
					fclose($fp);
					$db = new SQLite3(str_replace('\\server', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoServer');
					$db->busyTimeout(30000);
					$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');
					$hashingfile = hash_file('md2', str_replace('\\server', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $_POST['node'] . '\\' . $_FILES['file']['name']);
					if (is_null($db->querySingle('SELECT node FROM nodesHash WHERE node = "' . strtolower($_POST['node']) . '"'))) {
						$db->query('INSERT INTO nodesHash (node, ' . strtolower(str_replace('.xml.gz', '', $_FILES['file']['name'])) . ') VALUES ("' . strtolower($_POST['node']) . '", "' . $hashingfile . '")');
					} else {
						$db->query('UPDATE nodesHash SET ' . strtolower(str_replace('.xml.gz', '', $_FILES['file']['name'])) . ' = "' . $hashingfile . '" WHERE node = "' . strtolower($_POST['node']) . '"');
					}
					if (is_null($db->querySingle('SELECT node FROM xml' . ucfirst(strtolower(str_replace('.xml.gz', '', $_FILES['file']['name']))) . ' WHERE node = "' . strtolower($_POST['node']) . '"'))) {
						$db->query('INSERT INTO xml' . ucfirst(strtolower(str_replace('.xml.gz', '', $_FILES['file']['name']))) . ' (node, xml) VALUES ("' . strtolower($_POST['node']) . '", "' . $bl . '")');
					} else {
						$db->query('UPDATE xml' . ucfirst(strtolower(str_replace('.xml.gz', '', $_FILES['file']['name']))) . ' SET xml = "' . $bl . '" WHERE node = "' . strtolower($_POST['node']) . '"');
					}
					$db->close();
					foreach (glob($_SERVER['DOCUMENT_ROOT'] . '\\metering\\*.' . str_replace('.xml.gz', '', $_FILES['file']['name']) . '_metering', GLOB_NOSORT) as $meteringname) {
						$checkfind = 0;
						$meteringfname = str_replace($_SERVER['DOCUMENT_ROOT'] . '\\metering\\', '', $meteringname);
						if (!file_exists(str_replace('\\server', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $_POST['node'] . '\\' . $meteringfname)) {
							$bl = urldecode($bl);
							$xml = simplexml_load_file($meteringname);
							$meteringfilter = base64_decode($xml->filter);
							if (substr($meteringfilter, 0, 1) != '-') {
								if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($meteringfilter))) . '/', strtolower($bl)) || strpos(strtolower($bl), strtolower($meteringfilter)) > -1) {
									$checkfind = 0;
								} else {
									$checkfind = 1;
								}
							} else {
								$notfind = substr($meteringfilter, 1);
								if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfind))) . '/', strtolower($bl)) && !strpos(strtolower($bl), strtolower($notfind))) {
									$checkfind = 0;
								} else {
									$checkfind = 1;
								}
							}
							if($checkfind == 0) {
								$fp = fopen(str_replace('\\server', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $_POST['node'] . '\\' . $meteringfname, 'w');
								fwrite($fp, '');
								fclose($fp);
							}
						}
					}
				}
			}
		}
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