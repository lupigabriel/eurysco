<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/processes.php')) { exit; }

include('/include/init.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['processcontrol'] > 0) {  } else { exit; }
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

if ($idprocess != '0') {
	$wmiprocesses = $wmi->ExecQuery("SELECT Name FROM Win32_Service WHERE ProcessId = '" . $idprocess . "'");
	foreach($wmiprocesses as $process) {
		$Name = $process->Name;
	}
}


$ParentName = '-';

if ($pid != '0') {
	$wmiprocesses = $wmi->ExecQuery("SELECT Name FROM Win32_PerfFormattedData_PerfProc_Process WHERE IDProcess = '" . $pid . "'");
	foreach($wmiprocesses as $process) {
		$ParentName = $process->Name;
	}
}


$get_sid  = new Variant('', VT_BSTR);
$strUser = new Variant('', VT_BSTR);

$ExecutablePath = '';
$FilePath = '';
$colProcesses = $wmi->ExecQuery("SELECT ExecutablePath, Name FROM Win32_Process WHERE ProcessId = '" . $idprocess . "'");
foreach ($colProcesses as $objProcess) {
	$FileName = $objProcess->Name;
	$ExecutablePath = $objProcess->ExecutablePath;
	try { $re_turn = $objProcess->GetOwner($strUser); } catch(Exception $e) { }
	if ($re_turn != 0) {
		$UserName = '-';
	} else {
		$UserName = strtolower($strUser);
	}
}
if (strlen($FileName) > 15) { $FileName = substr($FileName, 0, 15); }
$ParentNameURL = $ParentName;
if (strlen($ParentName) > 15) { $ParentName = substr($ParentName, 0, 15) . '&nbsp;[...]'; }
if (strlen($Name) > 15) { $LimitName = substr($Name, 0, 15) . '&nbsp;[...]'; } else { $LimitName = $Name; }
if ($ExecutablePath != '') {
	$FilePath = '';
	if (is_dir(preg_replace('/' . strtolower($FileName) . '.*/', '', strtolower($ExecutablePath)))) {
		$FilePath = preg_replace('/' . strtolower($FileName) . '.*/', '', strtolower($ExecutablePath));
	}
	if (is_dir(str_replace('\\' . strtolower($FileName), '', strtolower($ExecutablePath)))) {
		$FilePath = str_replace('\\' . strtolower($FileName), '', strtolower($ExecutablePath)) . '\\';
	}
	$FileName = str_replace(' ', '&nbsp;', $FileName);
} else {
	$FilePath = '-';
	$FileName = str_replace(' ', '&nbsp;', $FileName);
}

echo json_encode(array('LimitName'=>utf8_encode($LimitName),'Name'=>utf8_encode($Name),'NameURL'=>utf8_encode(urlencode($Name)),'ParentName'=>utf8_encode($ParentName),'ParentNameURL'=>utf8_encode(urlencode($ParentNameURL)),'ExecutablePath'=>strtolower(utf8_encode($ExecutablePath)),'FileName'=>utf8_encode($FileName),'FilePath'=>utf8_encode(rawurlencode($FilePath)),'UserName'=>utf8_encode(strtolower(str_replace('\\', '\\\\', $UserName)))));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>