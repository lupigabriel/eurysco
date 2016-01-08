<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_services.php')) { exit; }

if (isset($_GET['node'])) {
	$node = $_GET['node'];
} else {
	exit;
}

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesservicecontrol'] > 0) {  } else { exit; }
if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }
session_write_close();

if (isset($_GET['idprocess'])) {
	if ($_GET['idprocess'] != '-') {
		$idprocess = $_GET['idprocess'];
	} else {
		$idprocess = '0';
	}
} else {
	$idprocess = '0';
}

$Name = '';
$NamePath = '';
$PercentProcessorTime = '-';
$WorkingSetPrivate = '-';
$WorkingSetPrivatePropName = 'WorkingSetPrivate';

$processes = $euryscoinstallpath . '\\nodes\\' . $node . '\\processes.xml.gz';
if (file_exists($processes)) {
$db = new SQLite3($euryscoinstallpath . '\\sqlite\\euryscoServer');
$db->busyTimeout(30000);
$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');
$xml = simplexml_load_string($db->querySingle('SELECT xml FROM xmlProcesses WHERE node = "' . $node . '"'));
if (!is_object($xml)) {
	$fp = gzopen($processes, 'rb');
	$bl = '';
	while (!feof($fp)) {
		$gz = gzread($fp, 2048);
		$bl = $bl . $gz;
	}
	fclose($fp);
	$xml = simplexml_load_string($bl);
}
$db->close();
	if ($idprocess != '0') {
		foreach ($xml->children() as $prop=>$n) {
			if ($xml->$prop->IDProcess == $idprocess) {
				$Name = urldecode($xml->$prop->Name);
				$PercentProcessorTime = $xml->$prop->PercentProcessorTime;
				$WorkingSetPrivate = $xml->$prop->WorkingSetPrivate;
			}
		}
	}
}

$ExecutablePath = '-';
if (strlen($Name) > 15) { $ShortName = substr($Name, 0, 15) . '&nbsp;[...]'; } else { $ShortName = $Name; }
if ($ExecutablePath != '') { $NamePath = $ShortName; }

echo json_encode(array('ExecutablePath'=>strtolower(utf8_encode($ExecutablePath)),'Name'=>utf8_encode($Name),'NameURL'=>utf8_encode(urlencode($Name)),'NamePath'=>utf8_encode($NamePath),'PercentProcessorTime'=>utf8_encode($PercentProcessorTime),'WorkingSetPrivate'=>utf8_encode($WorkingSetPrivate)));

flush();


if (extension_loaded('zlib')) { ob_end_flush(); }

?>