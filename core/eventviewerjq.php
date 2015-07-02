<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/eventviewer.php')) { exit; }

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
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['eventviewer'] > 0) {  } else { exit; }
session_write_close();

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'TimeGenerated';
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

if ($orderby == 'EventType') { $obyEventType = ' color:#8063C8;'; } else { $obyEventType = ''; }
if ($orderby == 'EventCode') { $obyEventCode = ' color:#8063C8;'; } else { $obyEventCode = ''; }
if ($orderby == 'Logfile') { $obyLogfile = ' color:#8063C8;'; } else { $obyLogfile = ''; }
if ($orderby == 'SourceName') { $obySourceName = ' color:#8063C8;'; } else { $obySourceName = ''; }
if ($orderby == 'TimeGenerated') { $obyTimeGenerated = ' color:#8063C8;'; } else { $obyTimeGenerated = ''; }

$eventstable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="1%" align="center"></td><td width="1%" align="center"><a href="?orderby=EventType&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyEventType . '" title="Descending Order by Type">Type</a></td><td width="5%" align="center"><a href="?orderby=EventCode&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyEventCode . '" title="Descending Order by Event ID">Event ID</a></td><td width="8%" align="center"><a href="?orderby=Logfile&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyLogfile . '" title="Descending Order by Log">Log</a></td><td width="55%"><a href="?orderby=SourceName&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obySourceName . '" title="Descending Order by Source Name">Source Name</a></td><td align="center"><a href="?orderby=TimeGenerated&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyTimeGenerated . '" title="Descending Order by Time Generated">Time Generated</a></td></tr>';



$eventsarray = array();
$eventscounter = 0;
$eventviewercount = 0;

$wmisclass = $wmi->ExecQuery("SELECT ComputerName, EventCode, EventIdentifier, EventType, Logfile, Message, RecordNumber, SourceName, TimeGenerated, Type, User FROM Win32_NTLogEvent WHERE Logfile = 'System'");
foreach($wmisclass as $obj) {
	
	$ComputerName = '';
	$EventCode = '';
	$EventIdentifier = '';
	$EventType = '<div class="icon-info" style="color:#2e92d3; margin-top:2px;" title="System Info"></div>';
	$TypeOrder = 1;
	$InsertionStrings = '';
	$Logfile = '';
	$Message = '';
	$RecordNumber = '';
	$SourceName = '';
	$TimeGenerated = '';
	$TimeWritten = '';
	$Type = '';
	$User = '';
	$ComputerName = $obj->ComputerName;
	$EventCode = $obj->EventCode;
	$EventIdentifier = $obj->EventIdentifier;
	if (strtolower($obj->EventType) == '2') { $EventType = '<div class="icon-warning" style="color:#df9c19; margin-top:2px;" title="System Warning"></div>'; $TypeOrder = 2; }
	if (strtolower($obj->EventType) == '1') { $EventType = '<div class="icon-minus" style="color:#933000; margin-top:2px;" title="System Error"></div>'; $TypeOrder = 3; }
	$Logfile = $obj->Logfile;
	$Message = $obj->Message;
	$RecordNumber = $obj->RecordNumber;
	$SourceName = $obj->SourceName;
	$TimeGenerated = $obj->TimeGenerated;
	$utc_date = DateTime::createFromFormat('Y-m-d H:i:s', substr($TimeGenerated, 0, 4) . '-' . substr($TimeGenerated, 4, 2) . '-' . substr($TimeGenerated, 6, 2) . ' ' . substr($TimeGenerated, 8, 2) . ':' . substr($TimeGenerated, 10, 2) . ':' . substr($TimeGenerated, 12, 2), new DateTimeZone('UTC'));
	$nyc_date = $utc_date;
	if (substr($obj->TimeGenerated, 22, 3) == 000) { $nyc_date->setTimeZone(new DateTimeZone($timezonesetting)); }
	$TimeGenForm = $nyc_date->format('d/m/Y H:i:s');
	$Type = $obj->Type;
	$User = $obj->User;
	if ($orderby != 'EventType') { $Ordering = $obj->$orderby; } else { $Ordering = $TypeOrder; }

	$datarow = strtolower('<TimeGenerated>' . $TimeGenForm . '</TimeGenerated><EventCode>' . $EventCode . '</EventCode><EventIdentifier>' . $EventIdentifier . '</EventIdentifier><Type>' . $Type . '</Type><EventType>' . $obj->EventType . '</EventType><Logfile>' . $Logfile . '</Logfile><Message>' . $Message . '</Message><RecordNumber>' . $RecordNumber . '</RecordNumber><SourceName>' . $SourceName . '</SourceName><ComputerName>' . $ComputerName . '</ComputerName><User>' . $User . '</User>');
	$prefilter = $_SESSION['usersett']['eventviewerf'];
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
		$eventsarray[$eventscounter][0] = strtolower($Ordering);
		$eventsarray[$eventscounter][1] = $TimeGenerated;
		$eventsarray[$eventscounter][2] = $EventCode;
		$eventsarray[$eventscounter][3] = $EventIdentifier;
		$eventsarray[$eventscounter][4] = $EventType;
		$eventsarray[$eventscounter][5] = $InsertionStrings;
		$eventsarray[$eventscounter][6] = $Logfile;
		$eventsarray[$eventscounter][7] = $Message;
		$eventsarray[$eventscounter][8] = $RecordNumber;
		$eventsarray[$eventscounter][9] = $SourceName;
		$eventsarray[$eventscounter][10] = $ComputerName;
		$eventsarray[$eventscounter][11] = $TimeWritten;
		$eventsarray[$eventscounter][12] = $Type;
		$eventsarray[$eventscounter][13] = $User;
		$eventsarray[$eventscounter][14] = 'Raw Data View:' . "\n\n" . '<TimeGenerated>' . $TimeGenForm . '</TimeGenerated>' . "\n" . '<EventCode>' . $EventCode . '</EventCode>' . "\n" . '<EventIdentifier>' . $EventIdentifier . '</EventIdentifier>' . "\n" . '<Type>' . $Type . '</Type>' . "\n" . '<EventType>' . $obj->EventType . '</EventType>' . "\n" . '<Logfile>' . $Logfile . '</Logfile>' . "\n" . '<Message>' . substr($Message, 0, 10) . '...' . '</Message>' . "\n" . '<RecordNumber>' . $RecordNumber . '</RecordNumber>' . "\n" . '<SourceName>' . $SourceName . '</SourceName>' . "\n" . '<ComputerName>' . $ComputerName . '</ComputerName>' . "\n" . '<User>' . $User . '</User>';
		if ($Message != '') { if (strlen($Message) <= 55) { $eventsarray[$eventscounter][15] = $Message; } else { $eventsarray[$eventscounter][15] = substr($Message, 0, 55) . '...'; } } else { $eventsarray[$eventscounter][15] = $SourceName; } 
		if (strlen($SourceName) <= 30) { $eventsarray[$eventscounter][16] = $SourceName; } else { $eventsarray[$eventscounter][16] = substr($SourceName, 0, 30) . '...'; }
		$eventsarray[$eventscounter][17] = $TimeGenForm;
		$eventsarray[$eventscounter][18] = $obj->EventType;
		$eventscounter = $eventscounter + 1;
		$eventviewercount = $eventviewercount + 1;
	} else {
		$eventviewercount = $eventviewercount + 0.2;
	}
	
	if ($eventviewercount > 249) { break; }
}

$eventviewercount = 0;
$wmisclass = $wmi->ExecQuery("SELECT ComputerName, EventCode, EventIdentifier, EventType, Logfile, Message, RecordNumber, SourceName, TimeGenerated, Type, User FROM Win32_NTLogEvent WHERE Logfile = 'Application'");
foreach($wmisclass as $obj) {

	$ComputerName = '';
	$EventCode = '';
	$EventIdentifier = '';
	$EventType = '<div class="icon-info" style="color:#2e92d3; margin-top:2px;" title="Application Info"></div>';
	$TypeOrder = 1;
	$InsertionStrings = '';
	$Logfile = '';
	$Message = '';
	$RecordNumber = '';
	$SourceName = '';
	$TimeGenerated = '';
	$TimeWritten = '';
	$Type = '';
	$User = '';
	$ComputerName = $obj->ComputerName;
	$EventCode = $obj->EventCode;
	$EventIdentifier = $obj->EventIdentifier;
	if (strtolower($obj->EventType) == '2') { $EventType = '<div class="icon-warning" style="color:#df9c19; margin-top:2px;" title="Application Warning"></div>'; $TypeOrder = 2; }
	if (strtolower($obj->EventType) == '1') { $EventType = '<div class="icon-minus" style="color:#933000; margin-top:2px;" title="Application Error"></div>'; $TypeOrder = 3; }
	$Logfile = $obj->Logfile;
	$Message = $obj->Message;
	$RecordNumber = $obj->RecordNumber;
	$SourceName = $obj->SourceName;
	$TimeGenerated = $obj->TimeGenerated;
	$utc_date = DateTime::createFromFormat('Y-m-d H:i:s', substr($TimeGenerated, 0, 4) . '-' . substr($TimeGenerated, 4, 2) . '-' . substr($TimeGenerated, 6, 2) . ' ' . substr($TimeGenerated, 8, 2) . ':' . substr($TimeGenerated, 10, 2) . ':' . substr($TimeGenerated, 12, 2), new DateTimeZone('UTC'));
	$nyc_date = $utc_date;
	if (substr($obj->TimeGenerated, 22, 3) == 000) { $nyc_date->setTimeZone(new DateTimeZone($timezonesetting)); }
	$TimeGenForm = $nyc_date->format('d/m/Y H:i:s');
	$Type = $obj->Type;
	$User = $obj->User;
	if ($orderby != 'EventType') { $Ordering = $obj->$orderby; } else { $Ordering = $TypeOrder; }

	$datarow = strtolower('<TimeGenerated>' . $TimeGenForm . '</TimeGenerated><EventCode>' . $EventCode . '</EventCode><EventIdentifier>' . $EventIdentifier . '</EventIdentifier><Type>' . $Type . '</Type><EventType>' . $obj->EventType . '</EventType><Logfile>' . $Logfile . '</Logfile><Message>' . $Message . '</Message><RecordNumber>' . $RecordNumber . '</RecordNumber><SourceName>' . $SourceName . '</SourceName><ComputerName>' . $ComputerName . '</ComputerName><User>' . $User . '</User>');
	$prefilter = $_SESSION['usersett']['eventviewerf'];
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
		$eventsarray[$eventscounter][0] = strtolower($Ordering);
		$eventsarray[$eventscounter][1] = $TimeGenerated;
		$eventsarray[$eventscounter][2] = $EventCode;
		$eventsarray[$eventscounter][3] = $EventIdentifier;
		$eventsarray[$eventscounter][4] = $EventType;
		$eventsarray[$eventscounter][5] = $InsertionStrings;
		$eventsarray[$eventscounter][6] = $Logfile;
		$eventsarray[$eventscounter][7] = $Message;
		$eventsarray[$eventscounter][8] = $RecordNumber;
		$eventsarray[$eventscounter][9] = $SourceName;
		$eventsarray[$eventscounter][10] = $ComputerName;
		$eventsarray[$eventscounter][11] = $TimeWritten;
		$eventsarray[$eventscounter][12] = $Type;
		$eventsarray[$eventscounter][13] = $User;
		$eventsarray[$eventscounter][14] = 'Raw Data View:' . "\n\n" . '<TimeGenerated>' . $TimeGenForm . '</TimeGenerated>' . "\n" . '<EventCode>' . $EventCode . '</EventCode>' . "\n" . '<EventIdentifier>' . $EventIdentifier . '</EventIdentifier>' . "\n" . '<Type>' . $Type . '</Type>' . "\n" . '<EventType>' . $obj->EventType . '</EventType>' . "\n" . '<Logfile>' . $Logfile . '</Logfile>' . "\n" . '<Message>' . substr($Message, 0, 10) . '...' . '</Message>' . "\n" . '<RecordNumber>' . $RecordNumber . '</RecordNumber>' . "\n" . '<SourceName>' . $SourceName . '</SourceName>' . "\n" . '<ComputerName>' . $ComputerName . '</ComputerName>' . "\n" . '<User>' . $User . '</User>';
		if ($Message != '') { if (strlen($Message) <= 55) { $eventsarray[$eventscounter][15] = $Message; } else { $eventsarray[$eventscounter][15] = substr($Message, 0, 55) . '...'; } } else { $eventsarray[$eventscounter][15] = $SourceName; } 
		if (strlen($SourceName) <= 30) { $eventsarray[$eventscounter][16] = $SourceName; } else { $eventsarray[$eventscounter][16] = substr($SourceName, 0, 30) . '...'; }
		$eventsarray[$eventscounter][17] = $TimeGenForm;
		$eventsarray[$eventscounter][18] = $obj->EventType;
		$eventscounter = $eventscounter + 1;
		$eventviewercount = $eventviewercount + 1;
	} else {
		$eventviewercount = $eventviewercount + 0.2;
	}
	
	if ($eventviewercount > 249) { break; }
}

$eventviewercount = 0;
$wmisclass = $wmi->ExecQuery("SELECT ComputerName, EventCode, EventIdentifier, EventType, Logfile, Message, RecordNumber, SourceName, TimeGenerated, Type, User FROM Win32_NTLogEvent WHERE Logfile = 'Security'");
foreach($wmisclass as $obj) {
	
	$ComputerName = '';
	$EventCode = '';
	$EventIdentifier = '';
	$EventType = '<div class="icon-key" style="color:#cdab16; margin-top:2px;" title="Audit Success"></div>';
	$TypeOrder = 1;
	$InsertionStrings = '';
	$Logfile = '';
	$Message = '';
	$RecordNumber = '';
	$SourceName = '';
	$TimeGenerated = '';
	$TimeWritten = '';
	$Type = '';
	$User = '';
	$ComputerName = $obj->ComputerName;
	$EventCode = $obj->EventCode;
	$EventIdentifier = $obj->EventIdentifier;
	if (strtolower($obj->EventType) == '5') { $EventType = '<div class="icon-locked-2" style="color:#933000; margin-top:2px;" title="Audit Failure"></div>'; $TypeOrder = 3; }
	$Logfile = $obj->Logfile;
	$Message = $obj->Message;
	$RecordNumber = $obj->RecordNumber;
	$SourceName = $obj->SourceName;
	$TimeGenerated = $obj->TimeGenerated;
	$utc_date = DateTime::createFromFormat('Y-m-d H:i:s', substr($TimeGenerated, 0, 4) . '-' . substr($TimeGenerated, 4, 2) . '-' . substr($TimeGenerated, 6, 2) . ' ' . substr($TimeGenerated, 8, 2) . ':' . substr($TimeGenerated, 10, 2) . ':' . substr($TimeGenerated, 12, 2), new DateTimeZone('UTC'));
	$nyc_date = $utc_date;
	if (substr($obj->TimeGenerated, 22, 3) == 000) { $nyc_date->setTimeZone(new DateTimeZone($timezonesetting)); }
	$TimeGenForm = $nyc_date->format('d/m/Y H:i:s');
	$Type = $obj->Type;
	$User = $obj->User;
	if ($orderby != 'EventType') { $Ordering = $obj->$orderby; } else { $Ordering = $TypeOrder; }

	$datarow = strtolower('<TimeGenerated>' . $TimeGenForm . '</TimeGenerated><EventCode>' . $EventCode . '</EventCode><EventIdentifier>' . $EventIdentifier . '</EventIdentifier><Type>' . $Type . '</Type><EventType>' . $obj->EventType . '</EventType><Logfile>' . $Logfile . '</Logfile><Message>' . $Message . '</Message><RecordNumber>' . $RecordNumber . '</RecordNumber><SourceName>' . $SourceName . '</SourceName><ComputerName>' . $ComputerName . '</ComputerName><User>' . $User . '</User>');
	$prefilter = $_SESSION['usersett']['eventviewerf'];
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
		$eventsarray[$eventscounter][0] = strtolower($Ordering);
		$eventsarray[$eventscounter][1] = $TimeGenerated;
		$eventsarray[$eventscounter][2] = $EventCode;
		$eventsarray[$eventscounter][3] = $EventIdentifier;
		$eventsarray[$eventscounter][4] = $EventType;
		$eventsarray[$eventscounter][5] = $InsertionStrings;
		$eventsarray[$eventscounter][6] = $Logfile;
		$eventsarray[$eventscounter][7] = $Message;
		$eventsarray[$eventscounter][8] = $RecordNumber;
		$eventsarray[$eventscounter][9] = $SourceName;
		$eventsarray[$eventscounter][10] = $ComputerName;
		$eventsarray[$eventscounter][11] = $TimeWritten;
		$eventsarray[$eventscounter][12] = $Type;
		$eventsarray[$eventscounter][13] = $User;
		$eventsarray[$eventscounter][14] = 'Raw Data View:' . "\n\n" . '<TimeGenerated>' . $TimeGenForm . '</TimeGenerated>' . "\n" . '<EventCode>' . $EventCode . '</EventCode>' . "\n" . '<EventIdentifier>' . $EventIdentifier . '</EventIdentifier>' . "\n" . '<Type>' . $Type . '</Type>' . "\n" . '<EventType>' . $obj->EventType . '</EventType>' . "\n" . '<Logfile>' . $Logfile . '</Logfile>' . "\n" . '<Message>' . substr($Message, 0, 10) . '...' . '</Message>' . "\n" . '<RecordNumber>' . $RecordNumber . '</RecordNumber>' . "\n" . '<SourceName>' . $SourceName . '</SourceName>' . "\n" . '<ComputerName>' . $ComputerName . '</ComputerName>' . "\n" . '<User>' . $User . '</User>';
		if ($Message != '') { if (strlen($Message) <= 55) { $eventsarray[$eventscounter][15] = $Message; } else { $eventsarray[$eventscounter][15] = substr($Message, 0, 55) . '...'; } } else { $eventsarray[$eventscounter][15] = $SourceName; } 
		if (strlen($SourceName) <= 30) { $eventsarray[$eventscounter][16] = $SourceName; } else { $eventsarray[$eventscounter][16] = substr($SourceName, 0, 30) . '...'; }
		$eventsarray[$eventscounter][17] = $TimeGenForm;
		$eventsarray[$eventscounter][18] = $obj->EventType;
		$eventscounter = $eventscounter + 1;
		$eventviewercount = $eventviewercount + 1;
	} else {
		$eventviewercount = $eventviewercount + 0.2;
	}
	
	if ($eventviewercount > 249) { break; }
}




rsort($eventsarray);

$eventspagearray = array();
if ($_SESSION['csv_eventviewer'] == 'csv_eventviewer') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Event Viewer";' . "\n" . '"eurysco CSV Source Node: ' . $envcomputername . '";' . "\n" . '"eurysco CSV Filter: ' . $filter . '";' . "\n" . '"eurysco CSV Total Raws: ' . $eventscounter . '";' . "\n\n");
	array_push($tmpcsvexport, '"Record Number";' . '"Computer Name";' . '"Time Generated";' . '"Type";' . '"User";' . '"Logfile";' . '"Event Code";' . '"Event Identifier";' . '"Event Type";' . '"Source Name";' . '"Message";' . "\n\n");
}
foreach ($eventsarray as $eventrow) {
	array_push($eventspagearray, '<tr class="rowselect" title="' . htmlentities($eventrow[14]) . '"><td align="center"><a href=\'javascript:eventinfo("' . $eventrow[17] . '","' . $eventrow[2] . '","' . $eventrow[3] . '","' . preg_replace('/\r\n|\r|\n/','\n', str_replace('\\', '\\\\', htmlentities(preg_replace('/[^a-zA-Z0-9 \.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\r\n]*/', '', $eventrow[7]), ENT_QUOTES, 'UTF-8'))) . '","' . $eventrow[8] . '","' . str_replace('-', ' ', str_replace('\\', '\\\\', $eventrow[16])) . '","' . $eventrow[10] . '","' . $eventrow[12] . '","' . $eventrow[13] . '","' . $eventrow[6] . '","' . str_replace('\\', '\\\\', $eventrow[9]) . '");\'><img src="/images/info24-black.png" width="16" height="16" border="0" title="Event ID:&nbsp;' . $eventrow[2] . '"></a></td><td align="center" style="font-size:12px;">' . $eventrow[4] . '</td><td align="center" style="font-size:12px;">' . $eventrow[2] . '</td><td align="center" style="font-size:12px;">' . $eventrow[6] . '</td><td><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="' . htmlentities($eventrow[7], ENT_QUOTES, 'UTF-8') . '">' . $eventrow[15] . '</div></td><td align="center"><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px;">' . $eventrow[17] . '</div></td></tr>');
	if ($_SESSION['csv_eventviewer'] == 'csv_eventviewer') { array_push($tmpcsvexport, '"' . $eventrow[8] . '";' . '"' . $eventrow[10] . '";' . '"' . $eventrow[17] . '";' . '"' . $eventrow[12] . '";' . '"' . $eventrow[13] . '";' . '"' . $eventrow[6] . '";' . '"' . $eventrow[2] . '";' . '"' . $eventrow[3] . '";' . '"' . $eventrow[18] . '";' . '"' . $eventrow[9] . '";' . '"' . $eventrow[7] . '";' . "\n"); }
}

if ($_SESSION['csv_eventviewer'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/eventviewer.php?csv_eventviewer&orderby=' . $orderby . '&filter=' . urlencode($filter) . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_eventviewer'] == 'csv_eventviewer') {
	session_start();
	$_SESSION['csv_eventviewer'] = $tmpcsvexport;
	session_write_close();
}
if ($_SESSION['csv_eventviewer'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_eventviewer&source=' . $envcomputername . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

$eventspages = array_chunk($eventspagearray, 75);

if ($pgkey > count($eventspages) - 1) { $pgkey = count($eventspages) - 1; }

if (count($eventspages) > 0) {
	foreach($eventspages[$pgkey] as $event) {
		$eventstable = $eventstable . $event;
	}
}

if ($eventscounter == 0) { $eventstable = $eventstable . '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="6">No Results...</td></tr>'; }

$eventstable = $eventstable . '</table>';

$eventspaging = '';
if (count($eventspages) > 1) {
	if ($pgkey > 5) {
		$eventspaging = $eventspaging . '<a href="?page=1&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($eventspages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$eventspaging = $eventspaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($eventspages) > $pgkey + 6) {
		$eventspaging = $eventspaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($eventspages) . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($eventspages) . '</span></a>';
	}
	$eventstable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $eventspaging . '</blockquote><br />' . $eventstable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $eventspaging . '</blockquote>';
}

$totalelement = count($eventsarray);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('eventstable'=>utf8_encode($eventstable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'csvexport'=>$csvexport));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>