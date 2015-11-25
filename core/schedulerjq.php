<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/scheduler.php')) { exit; }

if (isset($_GET['phptimeout'])) {
	set_time_limit($_GET['phptimeout']);
} else {
	set_time_limit(120);
}

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$start = $time;

include('/include/init.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['scheduledtasks'] > 0) {  } else { exit; }

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'TaskName';
}

if (isset($_GET['filter'])) {
	$filter = $_GET['filter'];
} else {
	$filter = '';
}

if (isset($_GET['page'])) {
	$pgkey = $_GET['page'];
} else {
	$pgkey = 0;
}

$schedulerarray = array();
$schedulercounter = 0;

$NextRunTimeOrdHr = 0;
$LastRunTimeOrdHr = 0;

$random = md5(session_id());

$schtasks = $_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.csv';
if ($roottaskssetting == 'Enable') { $schtasksfilter = ' | findstr /v /r /c:%computername%...\\\\[^^\\"]*\\\\'; } else { $schtasksfilter = ''; }
session_write_close();
$output = shell_exec('schtasks.exe /query /fo csv /v | find /i "%computername%"' . $schtasksfilter);
session_start();
$fp = fopen($schtasks, 'w');
fwrite($fp, $output);
fclose($fp);
file_put_contents($schtasks, implode(PHP_EOL, file($schtasks, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
$output = fopen($schtasks, 'r');

while($column = fgetcsv($output, 4096, ',', '"')) {
	if ($_GET['osmver'] > 5) {
		$HostName = $column[0];
		$TaskName = $column[1];
		$TaskNameNR = preg_replace('/.*\\\/','',$TaskName);
		if (strlen($TaskNameNR) > 40) { $TaskNameShort = substr($TaskNameNR, 0, 40) . ' [...]'; } else { $TaskNameShort = $TaskNameNR; }
		if (strlen($TaskNameNR) > 15) { $TitleName = substr($TaskNameNR, 0, 15) . ' [...]'; } else { $TitleName = $TaskNameNR; }
		$NextRunTime = $column[2];
		if (!preg_match('/[0-9]/', $column[2])) {
			$NextRunTimeOrder = 0;
			$NextRunTimeOrdHr = 'href="?orderby=Next Run Time&filter=' . urlencode($filter) . '"';
		} else {
			$NextRunTimeOrder = date('YmdHis', strtotime(str_replace('/', '-', $column[2])));
			if ($NextRunTimeOrder != 19700101010000) {
				$NextRunTimeOrdHr = 'href="?orderby=Next Run Time&filter=' . urlencode($filter) . '"';
			} else {
				$NextRunTimeOrdHr = '';
			}
		}
		$RunAsUser = $column[14];
		if (strlen($RunAsUser) > 20) { $RunAsUserShort = substr($RunAsUser, 0, 20) . ' [...]'; } else { $RunAsUserShort = $RunAsUser; }
		$Status = $column[3];
		$LastResult = $column[6];
		$StatusIcon = 'notrunning.png';
		$StatusText = 'Not Running';
		if ($LastResult == 0) { $StatusIcon = 'notrunning.png'; $StatusText = 'Not Running'; }
		if ($LastResult != 0) { $StatusIcon = 'error.png'; $StatusText = 'Error'; }
		if ($LastResult == 267009) { $StatusIcon = 'running.png'; $StatusText = 'Running'; }
		$ScheduledTaskState = $column[11];
		$LastRunTime = $column[5];
		if (!preg_match('/[0-9]/', $column[5])) {
			$LastRunTimeOrder = 0;
			$LastRunTimeOrdHr = 'href="?orderby=Last Run Time&filter=' . urlencode($filter) . '"';
		} else {
			$LastRunTimeOrder = date('YmdHis', strtotime(str_replace('/', '-', $column[5])));
			if ($LastRunTimeOrder != 19700101010000) {
				$LastRunTimeOrdHr = 'href="?orderby=Last Run Time&filter=' . urlencode($filter) . '"';
			} else {
				$LastRunTimeOrdHr = '';
			}
		}
		$Creator = $column[7];
		if (strlen($Creator) > 20) { $CreatorShort = substr($Creator, 0, 20) . ' [...]'; } else { $CreatorShort = $Creator; }
		$ScheduledType = $column[18];
		$NextRunTimeAlt = $NextRunTime;
		if ($NextRunTime == 'N/A') { $NextRunTimeAlt = $ScheduledType; $NextRunTimeOrder = '0' . $ScheduledType; }
		$TaskToRun = $column[8];
	} else {
		if (count($column) > 27) {
			$HostName = $column[0];
			$TaskName = $column[1];
			$TaskNameNR = preg_replace('/.*\\\/','',$TaskName);
			if (strlen($TaskNameNR) > 40) { $TaskNameShort = substr($TaskNameNR, 0, 40) . ' [...]'; } else { $TaskNameShort = $TaskNameNR; }
			if (strlen($TaskNameNR) > 15) { $TitleName = substr($TaskNameNR, 0, 15) . ' [...]'; } else { $TitleName = $TaskNameNR; }
			$NextRunTime = $column[2];
			if (!preg_match('/[0-9]/', $column[2])) {
				$NextRunTimeOrder = 0;
				$NextRunTimeOrdHr = 'href="?orderby=Next Run Time&filter=' . urlencode($filter) . '"';
			} else {
				$NextRunTimeOrder = date('YmdHis', strtotime(str_replace('/', '-', $column[2])));
				if ($NextRunTimeOrder != 19700101010000) {
					$NextRunTimeOrdHr = 'href="?orderby=Next Run Time&filter=' . urlencode($filter) . '"';
				} else {
					$NextRunTimeOrdHr = '';
				}
			}
			$RunAsUser = $column[19];
			if (strlen($RunAsUser) > 20) { $RunAsUserShort = substr($RunAsUser, 0, 20) . ' [...]'; } else { $RunAsUserShort = $RunAsUser; }
			$Status = $column[3];
			$LastResult = $column[6];
			$StatusIcon = 'notrunning.png';
			$StatusText = 'Not Running';
			if ($LastResult == 0) { $StatusIcon = 'notrunning.png'; $StatusText = 'Not Running'; }
			if ($LastResult != 0) { $StatusIcon = 'error.png'; $StatusText = 'Error'; }
			if ($Status != '') { $StatusIcon = 'running.png'; $StatusText = 'Running'; }
			$ScheduledTaskState = $column[12];
			$LastRunTime = $column[5];
			if (!preg_match('/[0-9]/', $column[5])) {
				$LastRunTimeOrder = 0;
				$LastRunTimeOrdHr = 'href="?orderby=Last Run Time&filter=' . urlencode($filter) . '"';
			} else {
				$LastRunTimeOrder = date('YmdHis', strtotime(str_replace('/', '-', $column[5])));
				if ($LastRunTimeOrder != 19700101010000) {
					$LastRunTimeOrdHr = 'href="?orderby=Last Run Time&filter=' . urlencode($filter) . '"';
				} else {
					$LastRunTimeOrdHr = '';
				}
			}
			$Creator = $column[7];
			if (strlen($Creator) > 20) { $CreatorShort = substr($Creator, 0, 20) . ' [...]'; } else { $CreatorShort = $Creator; }
			$ScheduledType = $column[13];
			$NextRunTimeAlt = $NextRunTime;
			if ($NextRunTime == 'N/A') { $NextRunTimeAlt = $ScheduledType; $NextRunTimeOrder = '0' . $ScheduledType; }
			$TaskToRun = $column[9];
		} else {
			$HostName = $column[0];
			$TaskName = $column[1];
			$TaskNameNR = preg_replace('/.*\\\/','',$TaskName);
			if (strlen($TaskNameNR) > 40) { $TaskNameShort = substr($TaskNameNR, 0, 40) . ' [...]'; } else { $TaskNameShort = $TaskNameNR; }
			if (strlen($TaskNameNR) > 15) { $TitleName = substr($TaskNameNR, 0, 15) . ' [...]'; } else { $TitleName = $TaskNameNR; }
			$NextRunTime = $column[2];
			if (!preg_match('/[0-9]/', $column[2])) {
				$NextRunTimeOrder = 0;
				$NextRunTimeOrdHr = 'href="?orderby=Next Run Time&filter=' . urlencode($filter) . '"';
			} else {
				$NextRunTimeOrder = date('YmdHis', strtotime(str_replace('/', '-', $column[2])));
				if ($NextRunTimeOrder != 19700101010000) {
					$NextRunTimeOrdHr = 'href="?orderby=Next Run Time&filter=' . urlencode($filter) . '"';
				} else {
					$NextRunTimeOrdHr = '';
				}
			}
			$RunAsUser = $column[18];
			if (strlen($RunAsUser) > 20) { $RunAsUserShort = substr($RunAsUser, 0, 20) . ' [...]'; } else { $RunAsUserShort = $RunAsUser; }
			$Status = $column[3];
			$LastResult = $column[5];
			$StatusIcon = 'notrunning.png';
			$StatusText = 'Not Running';
			if ($LastResult == 0) { $StatusIcon = 'notrunning.png'; $StatusText = 'Not Running'; }
			if ($LastResult != 0) { $StatusIcon = 'error.png'; $StatusText = 'Error'; }
			if ($Status != '') { $StatusIcon = 'running.png'; $StatusText = 'Running'; }
			$ScheduledTaskState = $column[11];
			$LastRunTime = $column[4];
			if (!preg_match('/[0-9]/', $column[4])) {
				$LastRunTimeOrder = 0;
				$LastRunTimeOrdHr = 'href="?orderby=Last Run Time&filter=' . urlencode($filter) . '"';
			} else {
				$LastRunTimeOrder = date('YmdHis', strtotime(str_replace('/', '-', $column[4])));
				if ($LastRunTimeOrder != 19700101010000) {
					$LastRunTimeOrdHr = 'href="?orderby=Last Run Time&filter=' . urlencode($filter) . '"';
				} else {
					$LastRunTimeOrdHr = '';
				}
			}
			$Creator = $column[6];
			if (strlen($Creator) > 20) { $CreatorShort = substr($Creator, 0, 20) . ' [...]'; } else { $CreatorShort = $Creator; }
			$ScheduledType = $column[12];
			$NextRunTimeAlt = $NextRunTime;
			if ($NextRunTime == 'N/A') { $NextRunTimeAlt = $ScheduledType; $NextRunTimeOrder = '0' . $ScheduledType; }
			$TaskToRun = $column[8];
		}
	}
	
	$datarow = strtolower('<HostName>' . $HostName . '</HostName><TaskName>' . $TaskName . '</TaskName><NextRunTime>' . $NextRunTime . '</NextRunTime><RunAsUser>' . $RunAsUser . '</RunAsUser><Status>' . $Status . '</Status><LastResult>' . $LastResult . '</LastResult><ScheduledTaskState>' . $ScheduledTaskState . '</ScheduledTaskState><LastRunTime>' . $LastRunTime . '</LastRunTime><Creator>' . $Creator . '</Creator><ScheduledType>' . $ScheduledType . '</ScheduledType><TaskToRun>' . $TaskToRun . '</TaskToRun>');
	$prefilter = $_SESSION['usersett']['scheduledtasksf'];
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
		if ($orderby == 'RootTaskName') { $schedulerarray[$schedulercounter][0] = preg_replace('/.*\\\/','',$TaskName); }
		elseif ($orderby == 'Last Run Time') { $schedulerarray[$schedulercounter][0] = $LastRunTimeOrder; }
		elseif ($orderby == 'Next Run Time') { $schedulerarray[$schedulercounter][0] = $NextRunTimeOrder; }
		elseif ($orderby == 'ScheduledTaskState') { $schedulerarray[$schedulercounter][0] = $ScheduledTaskState; }
		else { $schedulerarray[$schedulercounter][0] = $column[1]; }
		$schedulerarray[$schedulercounter][1] = $TaskNameShort;
		$schedulerarray[$schedulercounter][2] = $ScheduledTaskState;
		$schedulerarray[$schedulercounter][3] = $NextRunTimeAlt;
		$schedulerarray[$schedulercounter][4] = $LastRunTime;
		$schedulerarray[$schedulercounter][5] = $TaskName;
		$schedulerarray[$schedulercounter][6] = $StatusIcon;
		$schedulerarray[$schedulercounter][7] = $StatusText;
		$schedulerarray[$schedulercounter][8] = $TitleName;
		$schedulerarray[$schedulercounter][9] = $LastResult;
		$schedulerarray[$schedulercounter][10] = $RunAsUser;
		$schedulerarray[$schedulercounter][11] = $Creator;
		$schedulerarray[$schedulercounter][12] = $ScheduledType;
		$schedulerarray[$schedulercounter][13] = $Status;
		$schedulerarray[$schedulercounter][14] = $TaskToRun;
		$schedulerarray[$schedulercounter][15] = $RunAsUserShort;
		$schedulerarray[$schedulercounter][16] = $CreatorShort;
		$schedulerarray[$schedulercounter][17] = $TaskNameNR;
		$schedulerarray[$schedulercounter][18] = 'Raw Data View:' . "\n\n" . '<HostName>' . $HostName . '</HostName>' . "\n" . '<TaskName>' . $TaskName . '</TaskName>' . "\n" . '<NextRunTime>' . $NextRunTime . '</NextRunTime>' . "\n" . '<RunAsUser>' . $RunAsUser . '</RunAsUser>' . "\n" . '<Status>' . $Status . '</Status>' . "\n" . '<LastResult>' . $LastResult . '</LastResult>' . "\n" . '<ScheduledTaskState>' . $ScheduledTaskState . '</ScheduledTaskState>' . "\n" . '<LastRunTime>' . $LastRunTime . '</LastRunTime>' . "\n" . '<Creator>' . $Creator . '</Creator>' . "\n" . '<ScheduledType>' . $ScheduledType . '</ScheduledType>' . "\n" . '<TaskToRun>' . $TaskToRun . '</TaskToRun>';
		$schedulercounter = $schedulercounter + 1;
	}
		
}

fclose($output);

if ($orderby == 'TaskName' || $orderby == 'RootTaskName') { $obyRootTaskName = ' color:#8063C8;'; } else { $obyRootTaskName = ''; }
if ($orderby == 'ScheduledTaskState') { $obyScheduledTaskState = ' color:#8063C8;'; } else { $obyScheduledTaskState = ''; }
if ($orderby == 'Next Run Time') { $obyNextRunTime = ' color:#8063C8;'; } else { $obyNextRunTime = ''; }
if ($orderby == 'Last Run Time') { $obyLastRunTime = ' color:#8063C8;'; } else { $obyLastRunTime = ''; }

$scheduletable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="1%" style="font-size:12px; font-weight:bold;" align="center"></td><td width="50%"><a href="?orderby=RootTaskName&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyRootTaskName . '" title="Ascending Order by Task Name">Task Name</a></td><td align="center"><a href="?orderby=ScheduledTaskState&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyScheduledTaskState . '" title="Descending Order by State">State</a></td><td width="20%" align="center"><a ' . $NextRunTimeOrdHr . ' style="font-size:12px; font-weight:bold;' . $obyNextRunTime . '" title="Descending Order by Next Rune">Next Run</a></td><td width="20%" align="center"><a ' . $LastRunTimeOrdHr . ' style="font-size:12px; font-weight:bold;' . $obyLastRunTime . '" title="Descending Order by Last Run">Last Run</a></td></tr>';

if ($orderby == 'TaskName' || $orderby == 'RootTaskName') {
	sort($schedulerarray);
} else {
	rsort($schedulerarray);
}

$schedulepagearray = array();
if ($_SESSION['csv_scheduler'] == 'csv_scheduler') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Scheduler";' . "\n" . '"eurysco CSV Source Node: ' . $envcomputername . '";' . "\n" . '"eurysco CSV Filter: ' . $filter . '";' . "\n" . '"eurysco CSV Total Raws: ' . $schedulercounter . '";' . "\n\n");
	array_push($tmpcsvexport, '"Name";' . '"State";' . '"Status";' . '"Next Runtime";' . '"Last Runtime";' . '"Last Result";' . '"Run as User";' . '"Task to Run";' . "\n\n");
}
foreach ($schedulerarray as $schedulerow) {
	if (strlen($schedulerow[1]) > 30) { $LimitName = substr($schedulerow[1], 0, 30) . '&nbsp;[...]'; } else { $LimitName = $schedulerow[1]; }
	array_push($schedulepagearray, '<tr class="rowselect" title="' . htmlentities($schedulerow[18]) . '"><td style="font-size:12px;" align="center"><a href=\'javascript:commandtask("' . str_replace('\'', '%27', $schedulerow[17]) . '","' . $schedulerow[2] . '","' . $schedulerow[3] . '","' . $schedulerow[4] . '","' . str_replace('\'', '%27', str_replace(' ', '&nbsp;', $schedulerow[8])) . '","' . $schedulerow[9] . '","' . strtolower(str_replace('\\', '\\\\', $schedulerow[10])) . '","' . str_replace('\'', '%27', str_replace('\\', '\\\\', $schedulerow[5])) . '","' . strtolower(str_replace('\\', '\\\\', $schedulerow[11])) . '","' . $schedulerow[12] . '","' . $schedulerow[13] . '","' . str_replace('"', '', str_replace('\'', '%27', strtolower(str_replace('\\', '\\\\', $schedulerow[14])))) . '","' . str_replace('\'', '%27', str_replace(' ', '&nbsp;', $LimitName)) . '","' . strtolower(str_replace('\\', '\\\\', $schedulerow[15])) . '","' . strtolower(str_replace('\\', '\\\\', $schedulerow[16])) . '");\'><img src="/img/' . $schedulerow[6] . '" width="16" height="16" border="0" title="' . $schedulerow[7] . '"></a></td><td style="font-size:12px;" title="' . $schedulerow[5] . '">' . str_replace(' ', '&nbsp;', $schedulerow[1]) . '</td><td style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;" align="center">' . $schedulerow[2] . '</td><td style="font-size:12px;" align="center"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">' . $schedulerow[3] . '</div></td><td style="font-size:12px;" align="center"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">' . $schedulerow[4] . '</div></td></tr>');
	if ($_SESSION['csv_scheduler'] == 'csv_scheduler') { array_push($tmpcsvexport, '"' . $schedulerow[5] . '";' . '"' . $schedulerow[2] . '";' . '"' . $schedulerow[13] . '";' . '"' . $schedulerow[3] . '";' . '"' . $schedulerow[4] . '";' . '"' . $schedulerow[9] . '";' . '"' . $schedulerow[10] . '";' . '"' . $schedulerow[14] . '";' . "\n"); }
}

if ($_SESSION['csv_scheduler'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/scheduler.php?csv_scheduler&orderby=' . $orderby . '&filter=' . urlencode($filter) . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_scheduler'] == 'csv_scheduler') {
	$_SESSION['csv_scheduler'] = $tmpcsvexport;
}
if ($_SESSION['csv_scheduler'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_scheduler&source=' . $envcomputername . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

$schedulepages = array_chunk($schedulepagearray, 50);

if ($pgkey > count($schedulepages) - 1) { $pgkey = count($schedulepages) - 1; }

if (count($schedulepages) > 0) {
	foreach($schedulepages[$pgkey] as $schedulerw) {
		$scheduletable = $scheduletable . $schedulerw;
	}
}

if ($schedulercounter == 0) { $scheduletable = $scheduletable . '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="5">No Results...</td></tr>'; }

$scheduletable = $scheduletable . '</table>';

$schedulepaging = '';
if (count($schedulepages) > 1) {
	if ($pgkey > 5) {
		$schedulepaging = $schedulepaging . '<a href="?page=1&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($schedulepages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$schedulepaging = $schedulepaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($schedulepages) > $pgkey + 6) {
		$schedulepaging = $schedulepaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($schedulepages) . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($schedulepages) . '</span></a>';
	}
	$scheduletable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $schedulepaging . '</blockquote><br />' . $scheduletable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $schedulepaging . '</blockquote>';
}

$totalelement = count($schedulerarray);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

unlink($schtasks);

echo json_encode(array('scheduletable'=>utf8_encode($scheduletable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'csvexport'=>$csvexport));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>