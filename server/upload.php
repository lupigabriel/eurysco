<?php

include(str_replace('\\server', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_server.php');

set_time_limit(60);

if (isset($_POST['type']) && isset($_FILES['file'])) {
	if ($_FILES['file']['error'] > 0) {
	} else {
		if ($_FILES['file']['size'] > 1048576) {
		} else {
			if ($_POST['type'] == 'nodes') {
				$nodespath = $euryscoinstallpath . '\\nodes';
				$moveerrorlevel = @move_uploaded_file($_FILES['file']['tmp_name'], $nodespath . '\\' . $_POST['node'] . '\\' . $_FILES['file']['name']);
				if (isset($_POST['comp']) && file_exists($nodespath . '\\' . $_POST['node'] . '\\' . $_FILES['file']['name']) . '.xml.gz') {
					$fp = '';
					$fp = gzopen($nodespath . '\\' . $_POST['node'] . '\\' . $_FILES['file']['name'], 'rb');
					$bl = '';
					while (!feof($fp)) {
						$gz = gzread($fp, 2048);
						$bl = $bl . $gz;
					}
					fclose($fp);
					$db = new SQLite3($euryscoinstallpath . '\\sqlite\\euryscoServer');
					$db->busyTimeout(30000);
					$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');
					$hashingfile = hash_file('md5', $nodespath . '\\' . $_POST['node'] . '\\' . $_FILES['file']['name']);
					$filename = str_replace('.xml.gz', '', $_FILES['file']['name']);
					if (is_null($db->querySingle('SELECT node FROM nodesHash WHERE node = "' . strtolower($_POST['node']) . '"'))) {
						$db->query('INSERT INTO nodesHash (node, ' . strtolower($filename) . ') VALUES ("' . strtolower($_POST['node']) . '", "' . $hashingfile . '")');
					} else {
						$db->query('UPDATE nodesHash SET ' . strtolower($filename) . ' = "' . $hashingfile . '" WHERE node = "' . strtolower($_POST['node']) . '"');
					}
					if (is_null($db->querySingle('SELECT node FROM xml' . ucfirst(strtolower($filename)) . ' WHERE node = "' . strtolower($_POST['node']) . '"'))) {
						$db->query('INSERT INTO xml' . ucfirst(strtolower($filename)) . ' (node, xml) VALUES ("' . strtolower($_POST['node']) . '", "' . $bl . '")');
					} else {
						$db->query('UPDATE xml' . ucfirst(strtolower($filename)) . ' SET xml = "' . $bl . '" WHERE node = "' . strtolower($_POST['node']) . '"');
					}
					$db->close();
					foreach (glob($euryscoinstallpath . '\\metering\\*.' . $filename . '_metering', GLOB_NOSORT) as $meteringname) {
						$checkfind = 0;
						$meteringfname = str_replace($euryscoinstallpath . '\\metering\\', '', $meteringname);
						if (!file_exists($nodespath . '\\' . $_POST['node'] . '\\' . $meteringfname)) {
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
								$fp = fopen($nodespath . '\\' . $_POST['node'] . '\\' . $meteringfname, 'w');
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