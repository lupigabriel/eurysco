<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/processes.php')) { exit; }

if (isset($_GET['phptimeout'])) {
	if (is_numeric($_GET['phptimeout'])) {
		set_time_limit($_GET['phptimeout']);
	} else {
		set_time_limit(120);
	}
} else {
	set_time_limit(120);
}

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$start = $time;

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['processcontrol'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'PercentProcessorTime';
}

if (isset($_GET['cpucount'])) {
	if (is_numeric($_GET['cpucount'])) {
		$cpucount = $_GET['cpucount'];
	} else {
		$cpucount = 1;
	}
} else {
	$cpucount = 1;
}

if (isset($_GET['filter'])) {
	$filter = $_GET['filter'];
} else {
	$filter = '';
}

if (isset($_GET['page'])) {
	if (is_numeric($_GET['page'])) {
		$pgkey = $_GET['page'];
	} else {
		$pgkey = 0;
	}
} else {
	$pgkey = 0;
}

$strUser = new Variant('', VT_BSTR);
$processjarray = array();
$CommandLine = '';
$FileName = '';
$ExecutablePath = '';
$UserName = '';
$processjarray[0][1] = $CommandLine;
$processjarray[0][2] = $FileName;
$processjarray[0][3] = $ExecutablePath;
$processjarray[0][4] = $UserName;
$ProcessId = '-';
$colProcesses = $wmi->ExecQuery("SELECT * FROM Win32_Process WHERE ProcessId > 0");
foreach ($colProcesses as $objProcess) {
	$CommandLine = $objProcess->CommandLine;
	$FileName = $objProcess->Name;
	$ExecutablePath = $objProcess->ExecutablePath;
	$UserName = '-';
	try { $re_turn = $objProcess->GetOwner($strUser); } catch(Exception $e) { }
	if ($re_turn == 0) { $UserName = strtolower($strUser); }
	$ProcessId = $objProcess->ProcessId;
	$processjarray[$ProcessId][1] = $CommandLine;
	$processjarray[$ProcessId][2] = $FileName;
	$processjarray[$ProcessId][3] = $ExecutablePath;
	$processjarray[$ProcessId][4] = $UserName;
}
	
$wmiprocesses = $wmi->ExecQuery("SELECT * FROM Win32_PerfFormattedData_PerfProc_Process");
$processarray = array();
$procecounter = 0;
$idlecpu = 100;
$WorkingSetPrivatePropName = 'WorkingSetPrivate';
$checkprops = 1;
$checkidle = 0;
foreach($wmiprocesses as $process) {
	
	
	$IDProcess = '';
	$Name = '';
	$PercentProcessorTime = '';
	$WorkingSetPrivate = '';
	$CreatingProcessID = '';
	
	if ($checkprops != 0) {
		foreach($process->Properties_ as $wmiprop) {
			if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|WorkingSet|')) > -1) {
				$WorkingSetPrivatePropName = 'WorkingSet';
			}
			if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|WorkingSetPrivate|')) > -1) {
				$WorkingSetPrivatePropName = 'WorkingSetPrivate';
			}
		}
		$checkprops = 0;
	}
	$Name = $process->Name;
	$PercentProcessorTime = round(($process->PercentProcessorTime) / $cpucount, 0);
	$WorkingSetPrivate = number_format(($process->$WorkingSetPrivatePropName) / 1024, 0, ',', '.');
	$IDProcess = $process->IDProcess;
	$CreatingProcessID = $process->CreatingProcessID;
	$CommandLine = '';
	$FileName = '';
	$ExecutablePath = '';
	$UserName = '';
	if (array_key_exists($IDProcess, $processjarray)) {
		$CommandLine = str_replace('"', '\'', $processjarray[$IDProcess][1]);
		$FileName = $processjarray[$IDProcess][2];
		$ExecutablePath = $processjarray[$IDProcess][3];
		$UserName = $processjarray[$IDProcess][4];
	}
 	
	$datarow = strtolower('<Name>' . $Name . '</Name><IDProcess>' . $IDProcess . '</IDProcess><PercentProcessorTime>' . $PercentProcessorTime . '</PercentProcessorTime><WorkingSetPrivate>' . $WorkingSetPrivate . '</WorkingSetPrivate><CreatingProcessID>' . $CreatingProcessID . '</CreatingProcessID><CommandLine>' . $CommandLine . '</CommandLine><FileName>' . $FileName . '</FileName><ExecutablePath>' . $ExecutablePath . '</ExecutablePath><UserName>' . $UserName . '</UserName>');
	$prefilter = $_SESSION['usersett']['processcontrolf'];
	$checkprefilter = 1;
	if (substr($prefilter, 0, 1) != '-') {
		if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($prefilter))) . '/', $datarow) || strpos($datarow, strtolower($prefilter)) > -1) {
			$checkprefilter = 0;
		} else {
			$checkprefilter = 1;
		}
	} else {
		$notprefilter = substr($prefilter, 1);
		if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notprefilter))) . '/', $datarow) && !strpos($datarow, strtolower($notprefilter))) {
			$checkprefilter = 0;
		} else {
			$checkprefilter = 1;
		}
	}
	$checkfilter = 1;
	if (substr($filter, 0, 1) != '-') {
		if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', $datarow) || strpos($datarow, strtolower($filter)) > -1) {
			if ($checkprefilter == 0) {
				$checkfilter = 0;
			}
		} else {
			$checkfilter = 1;
		}
	} else {
		$notfilter = substr($filter, 1);
		if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', $datarow) && !strpos($datarow, strtolower($notfilter))) {
			if ($checkprefilter == 0) {
				$checkfilter = 0;
			}
		} else {
			$checkfilter = 1;
		}
	}
	if ($checkfilter == 0) {
		if (strtolower($Name) == 'idle') {
			$checkidle = 1;
			if ($orderby == 'User') {
				$processarray[0][0] = '';
			} else {
				$processarray[0][0] = strtolower($process->$orderby);
			}
			$processarray[0][1] = $IDProcess;
			$processarray[0][2] = $Name;
			$processarray[0][4] = $WorkingSetPrivate . ' KB';
			$processarray[0][5] = $CreatingProcessID;
			$processarray[0][7] = '';
			$processarray[0][8] = '';
			$processarray[0][9] = '';
			$processarray[0][10] = '';
			$procecounter = $procecounter + 1;
		}
		
		if (strtolower($Name) != '_total' && strtolower($Name) != 'idle') {
			
			$idlecpu = $idlecpu - $PercentProcessorTime;
			if ($orderby == 'User') {
				$processarray[$procecounter][0] = $UserName;
			} else {
				if ($orderby == 'PercentProcessorTime') {
					$processarray[$procecounter][0] = $PercentProcessorTime;
				} else {
					$processarray[$procecounter][0] = strtolower($process->$orderby);
				}
			}
			$processarray[$procecounter][1] = $IDProcess;
			$processarray[$procecounter][2] = $Name;
			$processarray[$procecounter][3] = $PercentProcessorTime . '%';
			$processarray[$procecounter][4] = $WorkingSetPrivate . ' KB';
			$processarray[$procecounter][5] = $CreatingProcessID;
			$processarray[$procecounter][6] = 'Raw Data View:' . "\n\n" . '<Name>' . $Name . '</Name>' . "\n" . '<IDProcess>' . $IDProcess . '</IDProcess>' . "\n" . '<PercentProcessorTime>' . $PercentProcessorTime . '%</PercentProcessorTime>' . "\n" . '<WorkingSetPrivate>' . $WorkingSetPrivate . ' KB</WorkingSetPrivate>' . "\n" . '<CreatingProcessID>' . $CreatingProcessID . '</CreatingProcessID>' . "\n" . '<CommandLine>' . $CommandLine . '</CommandLine>' . "\n" . '<FileName>' . $FileName . '</FileName>' . "\n" . '<ExecutablePath>' . $ExecutablePath . '</ExecutablePath>' . "\n" . '<UserName>' . $UserName . '</UserName>';
			$processarray[$procecounter][7] = $CommandLine;
			$processarray[$procecounter][8] = $FileName;
			$processarray[$procecounter][9] = $ExecutablePath;
			$processarray[$procecounter][10] = $UserName;
			$procecounter = $procecounter + 1;
		}
	}
	
	
}
if ($checkidle == 1) {
	if ($idlecpu <= 0) {
		if ($orderby == 'PercentProcessorTime') { $processarray[0][0] = 0; }
		$processarray[0][3] = '0%';
		$processarray[0][6] = 'Raw Data View:' . "\n\n" . '<Name>' . $processarray[0][2] . '</Name>' . "\n" . '<IDProcess>' . $processarray[0][1] . '</IDProcess>' . "\n" . '<PercentProcessorTime>0%</PercentProcessorTime>' . "\n" . '<WorkingSetPrivate>' . $processarray[0][4] . '</WorkingSetPrivate>' . "\n" . '<CreatingProcessID>' . $processarray[0][5] . '</CreatingProcessID>' . "\n" . '<CommandLine></CommandLine>' . "\n" . '<FileName></FileName>' . "\n" . '<ExecutablePath></ExecutablePath>' . "\n" . '<UserName></UserName>';
	} else {
		if ($orderby == 'PercentProcessorTime') { $processarray[0][0] = $idlecpu; }
		$processarray[0][3] = $idlecpu . '%';
		$processarray[0][6] = 'Raw Data View:' . "\n\n" . '<Name>' . $processarray[0][2] . '</Name>' . "\n" . '<IDProcess>' . $processarray[0][1] . '</IDProcess>' . "\n" . '<PercentProcessorTime>' . $idlecpu . '%</PercentProcessorTime>' . "\n" . '<WorkingSetPrivate>' . $processarray[0][4] . '</WorkingSetPrivate>' . "\n" . '<CreatingProcessID>' . $processarray[0][5] . '</CreatingProcessID>' . "\n" . '<CommandLine></CommandLine>' . "\n" . '<FileName></FileName>' . "\n" . '<ExecutablePath></ExecutablePath>' . "\n" . '<UserName></UserName>';
	}
}

if ($orderby == 'Name' || $orderby == 'User' || $orderby == 'IDProcess' || $orderby == 'CreatingProcessID') {
	sort($processarray);
} else {
	rsort($processarray);
}

$processpagearray = array();
if ($_SESSION['csv_processes'] == 'csv_processes') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Processes";' . "\n" . '"eurysco CSV Source Node: ' . $envcomputername . '";' . "\n" . '"eurysco CSV Filter: ' . $filter . '";' . "\n" . '"eurysco CSV Total Raws: ' . $procecounter . '";' . "\n\n");
	array_push($tmpcsvexport, '"Name";' . '"PID";' . '"CPU Usage";' . '"Memory Usage";' . '"Parent PID";' . '"Command Line";' . '"File Name";' . '"Executable Path";' . '"Username";' . "\n\n");
}
foreach ($processarray as $processrow) {
	if ($processrow[2] == 'euryscosrv' || $processrow[2] == 'euryscosrv#1' || $processrow[2] == 'euryscosrv#2' || $processrow[2] == 'euryscosrv#3' || $processrow[2] == 'euryscosrv#4' || $processrow[2] == 'euryscosrv#5' || $processrow[2] == 'euryscosrv#6' || $processrow[2] == 'php_eurysco_agent' || $processrow[2] == 'php_eurysco_core' || $processrow[2] == 'php_eurysco_executor' || $processrow[2] == 'php_eurysco_server' || $processrow[2] == 'httpd_eurysco_core' || $processrow[2] == 'httpd_eurysco_core#1' || $processrow[2] == 'httpd_eurysco_executor' || $processrow[2] == 'httpd_eurysco_executor#1' || $processrow[2] == 'httpd_eurysco_server' || $processrow[2] == 'httpd_eurysco_server#1' || $processrow[2] == 'eurysco.agent.status.check' || $processrow[2] == 'eurysco.agent.exec.timeout') {
		array_push($processpagearray, '<tr class="rowselect" title="' . htmlentities($processrow[6]) . '"><td style="font-size:12px;" align="center">&nbsp;</td><td style="font-size:12px;" align="center">' . $processrow[1] . '</td><td style="font-size:12px;">' . $processrow[2] . '</td><td style="font-size:12px;" align="center"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">' . $processrow[10] . '</div></td><td style="font-size:12px;" align="center">' . $processrow[3] . '</td><td style="font-size:12px;" align="right">' . $processrow[4] . '</td><td style="font-size:12px;" align="center">' . $processrow[5] . '</td></tr>');
	} else {
		if (strlen($processrow[2]) > 15) { $LimitName = substr($processrow[2], 0, 15) . '&nbsp;[...]'; } else { $LimitName = $processrow[2]; }
		array_push($processpagearray, '<tr class="rowselect" title="' . htmlentities($processrow[6]) . '"><td style="font-size:12px;" align="center"><a href=\'javascript:endprocess("' . $processrow[1] . '","' . str_replace(' ', '&nbsp;', str_replace('\'', '%27', $processrow[2])) . '","' . $processrow[3] . '","' . $processrow[4] . '","' . $processrow[5] . '","' . str_replace(' ', '&nbsp;', str_replace('\'', '%27', $LimitName)) . '");\'><img src="/images/collapse24-black.png" width="16" height="16" border="0" title="Process: ' . $processrow[2] . '"></a></td><td style="font-size:12px;" align="center">' . $processrow[1] . '</td><td style="font-size:12px;">' . str_replace(' ', '&nbsp;', $processrow[2]) . '</td><td style="font-size:12px;" align="center"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">' . $processrow[10] . '</div></td><td style="font-size:12px;" align="center">' . $processrow[3] . '</td><td style="font-size:12px;" align="right">' . $processrow[4] . '</td><td style="font-size:12px;" align="center">' . $processrow[5] . '</td></tr>');
	}
	if ($_SESSION['csv_processes'] == 'csv_processes') { array_push($tmpcsvexport, '"' . $processrow[2] . '";' . '"' . $processrow[1] . '";' . '"' . $processrow[3] . '";' . '"' . $processrow[4] . '";' . '"' . $processrow[5] . '";' . '"' . $processrow[7] . '";' . '"' . $processrow[8] . '";' . '"' . $processrow[9] . '";' . '"' . $processrow[10] . '";' . "\n"); }
}

if ($_SESSION['csv_processes'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/processes.php?csv_processes&orderby=' . $orderby . '&filter=' . urlencode($filter) . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_processes'] == 'csv_processes') {
	$_SESSION['csv_processes'] = $tmpcsvexport;
}
if ($_SESSION['csv_processes'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_processes&source=' . $envcomputername . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

if ($orderby == 'IDProcess') { $obyIDProcess = ' color:#8063C8;'; } else { $obyIDProcess = ''; }
if ($orderby == 'Name') { $obyName = ' color:#8063C8;'; } else { $obyName = ''; }
if ($orderby == 'User') { $obyUser = ' color:#8063C8;'; } else { $obyUser = ''; }
if ($orderby == 'PercentProcessorTime') { $obyPercentProcessorTime = ' color:#8063C8;'; } else { $obyPercentProcessorTime = ''; }
if ($orderby == $WorkingSetPrivatePropName) { $obyWorkingSetPrivate = ' color:#8063C8;'; } else { $obyWorkingSetPrivate = ''; }
if ($orderby == 'CreatingProcessID') { $obyCreatingProcessID = ' color:#8063C8;'; } else { $obyCreatingProcessID = ''; }

$processtable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="1%" style="font-size:12px; font-weight:bold;" align="center"></td><td width="6%" align="center"><a href="?orderby=IDProcess&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyIDProcess . '" title="Ascending Order by PID">PID</a></td><td width="50%"><a href="?orderby=Name&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyName . '" title="Ascending Order by Name">Name</a></td><td width="5%" align="center"><a href="?orderby=User&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyUser . '" title="Ascending Order by User">User</a></td><td width="12%" align="center"><a href="?orderby=PercentProcessorTime&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyPercentProcessorTime . '" title="Descending Order by CPU Usage">CPU Usage</a></td><td width="10%" align="center"><a href="?orderby=' . $WorkingSetPrivatePropName . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyWorkingSetPrivate . '" title="Descending Order by Memory Usage">Memory Usage</a></td><td align="center"><a href="?orderby=CreatingProcessID&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyCreatingProcessID . '" title="Ascending Order by Parent PID">Parent PID</a></td></tr>';

$processpages = array_chunk($processpagearray, 100);

if ($pgkey > count($processpages) - 1) { $pgkey = count($processpages) - 1; }

if (count($processpages) > 0) {
	foreach($processpages[$pgkey] as $processrw) {
		$processtable = $processtable . $processrw;
	}
}

if ($procecounter == 0) { $processtable = $processtable . '<tr><td style="font-size:12px;" align="center" colspan="7">No Results...</td></tr>'; }

$processtable = $processtable . '</table>';

$processpaging = '';
if (count($processpages) > 1) {
	if ($pgkey > 5) {
		$processpaging = $processpaging . '<a href="?page=1&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($processpages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$processpaging = $processpaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($processpages) > $pgkey + 6) {
		$processpaging = $processpaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($processpages) . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($processpages) . '</span></a>';
	}
	$processtable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $processpaging . '</blockquote><br />' . $processtable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $processpaging . '</blockquote>';
}

$totalelement = count($processarray);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('processtable'=>utf8_encode($processtable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'csvexport'=>$csvexport));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>