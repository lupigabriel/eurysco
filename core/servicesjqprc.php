<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/services.php')) { exit; }

include('/include/init.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['servicecontrol'] > 0) {  } else { exit; }
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

if (isset($_GET['cpucount'])) {
	$cpucount = $_GET['cpucount'];
} else {
	$cpucount = 1;
}



$Name = '';
$NamePath = '';
$PercentProcessorTime = '-';
$WorkingSetPrivate = '-';
$WorkingSetPrivatePropName = 'WorkingSetPrivate';

if ($idprocess != '0') {
	$wmiprocesses = $wmi->ExecQuery("SELECT * FROM Win32_PerfFormattedData_PerfProc_Process WHERE IDProcess = '" . $idprocess . "'");
	foreach($wmiprocesses as $process) {
		foreach($process->Properties_ as $wmiprop) {
			if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|WorkingSet|')) > -1) {
				$WorkingSetPrivatePropName = 'WorkingSet';
			}
			if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|WorkingSetPrivate|')) > -1) {
				$WorkingSetPrivatePropName = 'WorkingSetPrivate';
			}
		}
		$Name = $process->Name;
		$PercentProcessorTime = round(($process->PercentProcessorTime) / $cpucount, 0) . '%';
		$WorkingSetPrivate = number_format(($process->$WorkingSetPrivatePropName) / 1024, 0, ',', '.') . '&nbsp;KB';
	}
}

$ExecutablePath = '';
$colProcesses = $wmi->ExecQuery("SELECT ExecutablePath FROM Win32_Process WHERE ProcessId = '" . $idprocess . "'");
foreach ($colProcesses as $objProcess) {
	$ExecutablePath = $objProcess->ExecutablePath;
}
if (strlen($Name) > 15) { $ShortName = substr($Name, 0, 15) . '&nbsp;[...]'; } else { $ShortName = $Name; }
if ($ExecutablePath != '') { $NamePath = $ShortName; }

echo json_encode(array('ExecutablePath'=>strtolower(utf8_encode($ExecutablePath)),'Name'=>utf8_encode($Name),'NameURL'=>utf8_encode(urlencode($Name)),'NamePath'=>utf8_encode($NamePath),'PercentProcessorTime'=>utf8_encode($PercentProcessorTime),'WorkingSetPrivate'=>utf8_encode($WorkingSetPrivate)));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>