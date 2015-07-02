<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_processes.php')) { exit; }

if (isset($_GET['node'])) {
	$node = $_GET['node'];
} else {
	exit;
}

include('/include/init.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesprocesscontrol'] > 0) {  } else { exit; }
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

if (isset($_GET['pid'])) {
	$pid = $_GET['pid'];
} else {
	$pid = '0';
}

$Name = '-';

$db = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoServer');
$db->busyTimeout(30000);
$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');

$services = str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $node . '\\services.xml.gz';
if (file_exists($services)) {
	$xml = simplexml_load_string($db->querySingle('SELECT xml FROM xmlServices WHERE node = "' . $node . '"'));
	if (!is_object($xml)) {
		$fp = gzopen($services, 'rb');
		$bl = '';
		while (!feof($fp)) {
			$gz = gzread($fp, 2048);
			$bl = $bl . $gz;
		}
		fclose($fp);
		$xml = simplexml_load_string($bl);
	}
	if ($idprocess != '0') {
		foreach ($xml->children() as $prop=>$n) {
			if ($xml->$prop->ProcessId == $idprocess) {
				$Name = urldecode($xml->$prop->Name);
			}
		}
	}
}


$ParentName = '-';

$processes = str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $node . '\\processes.xml.gz';
if (file_exists($processes)) {
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
	foreach ($xml->children() as $prop=>$n) {
		if ($xml->$prop->IDProcess == $pid) {
			$ParentName = urldecode($xml->$prop->Name);
		}
	}
}

$db->close();

$ExecutablePath = '';
$FilePath = '';
$UserName = '-';
$FileName = '-';
if (strlen($Name) > 12) { $LimitName = substr($Name, 0, 12) . '&nbsp;[...]'; } else { $LimitName = $Name; }
$ParentNameURL = $ParentName;
if (strlen($ParentName) > 12) { $ParentName = substr($ParentName, 0, 12) . '&nbsp;[...]'; }

echo json_encode(array('LimitName'=>utf8_encode($LimitName),'Name'=>utf8_encode($Name),'NameURL'=>utf8_encode(urlencode($Name)),'ParentName'=>utf8_encode($ParentName),'ParentNameURL'=>utf8_encode(urlencode($ParentNameURL)),'ExecutablePath'=>strtolower(utf8_encode($ExecutablePath)),'FileName'=>utf8_encode($FileName),'FilePath'=>utf8_encode(rawurlencode($FilePath)),'UserName'=>utf8_encode(strtolower(str_replace('\\', '\\\\', $UserName)))));

flush();

$db->close();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>