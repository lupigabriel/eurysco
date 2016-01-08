<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_eventviewer.php')) { exit; }

if (isset($_GET['node'])) {
	$node = $_GET['node'];
} else {
	exit;
}

if (isset($_GET['domain'])) {
	$domain = $_GET['domain'];
} else {
	exit;
}

if (isset($_GET['computerip'])) {
	$computerip = $_GET['computerip'];
} else {
	exit;
}

if (isset($_GET['executorport'])) {
	$executorport = $_GET['executorport'];
} else {
	exit;
}

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
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodeseventviewer'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

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
	if (is_numeric($_GET['page'])) {
		$pgkey = $_GET['page'];
	} else {
		$pgkey = 0;
	}
} else {
	$pgkey = 0;
}

if (!isset($_SESSION['csv_nodes_eventviewer'])) {
	$_SESSION['csv_nodes_eventviewer'] = '';
}

$lastupdate = '';

if ($orderby == 'EventType') { $obyEventType = ' color:#8063C8;'; } else { $obyEventType = ''; }
if ($orderby == 'EventCode') { $obyEventCode = ' color:#8063C8;'; } else { $obyEventCode = ''; }
if ($orderby == 'Logfile') { $obyLogfile = ' color:#8063C8;'; } else { $obyLogfile = ''; }
if ($orderby == 'SourceName') { $obySourceName = ' color:#8063C8;'; } else { $obySourceName = ''; }
if ($orderby == 'TimeGenerated') { $obyTimeGenerated = ' color:#8063C8;'; } else { $obyTimeGenerated = ''; }

$eventstable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="1%" align="center"></td><td width="1%" align="center"><a href="?orderby=EventType&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyEventType . '" title="Descending Order by Type">Type</a></td><td width="5%" align="center"><a href="?orderby=EventCode&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyEventCode . '" title="Descending Order by Event ID">Event ID</a></td><td width="8%" align="center"><a href="?orderby=Logfile&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyLogfile . '" title="Descending Order by Log">Log</a></td><td width="55%"><a href="?orderby=SourceName&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obySourceName . '" title="Descending Order by Source Name">Source Name</a></td><td align="center"><a href="?orderby=TimeGenerated&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyTimeGenerated . '" title="Descending Order by Time Generated">Time Generated</a></td></tr>';

$db = new SQLite3($euryscoinstallpath . '\\sqlite\\euryscoServer');
$db->busyTimeout(5000);
$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');

$eventsarray = array();
$eventscounter = 0;

$nodepath = $euryscoinstallpath . '\\nodes\\' . $node . '\\';

$lastupdate = 'N/A';
if (!is_null($db->querySingle('SELECT node FROM xmlEvents WHERE node = "' . $node . '"')) || file_exists($nodepath . 'events.xml.gz')) {
	if (file_exists($nodepath . 'events.xml.gz')) { $lastupdate = date('d/m/Y H:i:s', filemtime($euryscoinstallpath . '\\nodes\\' . $node . '\\events.xml.gz')); }
	$xml = simplexml_load_string($db->querySingle('SELECT xml FROM xmlEvents WHERE node = "' . $node . '"'));
	if (!is_object($xml)) {
		$fp = gzopen($nodepath . 'events.xml.gz', 'rb');
		$bl = '';
		while (!feof($fp)) {
			$gz = gzread($fp, 2048);
			$bl = $bl . $gz;
		}
		fclose($fp);
		$xml = simplexml_load_string($bl);
	}
	$db->close();
	foreach ($xml->children() as $prop=>$n) {

		if ($xml->$prop->Logfile == 'System') {

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
			$ComputerName = $xml->$prop->ComputerName;
			$EventCode = $xml->$prop->EventCode;
			$EventIdentifier = $xml->$prop->EventIdentifier;
			if (strtolower($xml->$prop->EventType) == '2') { $EventType = '<div class="icon-warning" style="color:#df9c19; margin-top:2px;" title="System Warning"></div>'; $TypeOrder = 2; }
			if (strtolower($xml->$prop->EventType) == '1') { $EventType = '<div class="icon-minus" style="color:#933000; margin-top:2px;" title="System Error"></div>'; $TypeOrder = 3; }
			$Logfile = $xml->$prop->Logfile;
			$Message = urldecode($xml->$prop->Message);
			$RecordNumber = $xml->$prop->RecordNumber;
			$SourceName = urldecode($xml->$prop->SourceName);
			$TimeGenerated = $xml->$prop->TimeGenerated;
			$Type = $xml->$prop->Type;
			$User = urldecode($xml->$prop->User);
			if ($orderby != 'EventType') { $Ordering = $xml->$prop->$orderby; } else { $Ordering = $TypeOrder; }
			if ($orderby == 'TimeGenerated') { $Ordering = date('YmdHis', strtotime(str_replace('/', '-', $xml->$prop->TimeGenerated))); }

			$datarow = strtolower('<TimeGenerated>' . $TimeGenerated . '</TimeGenerated><EventCode>' . $EventCode . '</EventCode><EventIdentifier>' . $EventIdentifier . '</EventIdentifier><Type>' . $Type . '</Type><EventType>' . $xml->$prop->EventType . '</EventType><Logfile>' . $Logfile . '</Logfile><Message>' . $Message . '</Message><RecordNumber>' . $RecordNumber . '</RecordNumber><SourceName>' . $SourceName . '</SourceName><ComputerName>' . $ComputerName . '</ComputerName><User>' . $User . '</User>');
			$prefilter = $_SESSION['usersett']['nodeseventviewerf'];
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
				$eventsarray[$eventscounter][1] = date('YmdHis', strtotime(str_replace('/', '-', $xml->$prop->TimeGenerated)));
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
				$eventsarray[$eventscounter][14] = 'Raw Data View:' . "\n\n" . '<TimeGenerated>' . $TimeGenerated . '</TimeGenerated>' . "\n" . '<EventCode>' . $EventCode . '</EventCode>' . "\n" . '<EventIdentifier>' . $EventIdentifier . '</EventIdentifier>' . "\n" . '<Type>' . $Type . '</Type>' . "\n" . '<EventType>' . $xml->$prop->EventType . '</EventType>' . "\n" . '<Logfile>' . $Logfile . '</Logfile>' . "\n" . '<Message>' . substr($Message, 0, 10) . '...' . '</Message>' . "\n" . '<RecordNumber>' . $RecordNumber . '</RecordNumber>' . "\n" . '<SourceName>' . $SourceName . '</SourceName>' . "\n" . '<ComputerName>' . $ComputerName . '</ComputerName>' . "\n" . '<User>' . $User . '</User>';
				if ($Message != '') { if (strlen($Message) <= 55) { $eventsarray[$eventscounter][15] = $Message; } else { $eventsarray[$eventscounter][15] = substr($Message, 0, 55) . '...'; } } else { $eventsarray[$eventscounter][15] = $SourceName; } 
				if (strlen($SourceName) <= 30) { $eventsarray[$eventscounter][16] = $SourceName; } else { $eventsarray[$eventscounter][16] = substr($SourceName, 0, 30) . '...'; }
				$eventsarray[$eventscounter][17] = $TimeGenerated;
				$eventsarray[$eventscounter][18] = $xml->$prop->EventType;
				$eventsarray[$eventscounter][19] = $xml->$prop->TimeGenerated;
				$eventscounter = $eventscounter + 1;
			}
	
		}

		if ($xml->$prop->Logfile == 'Application') {

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
			$ComputerName = $xml->$prop->ComputerName;
			$EventCode = $xml->$prop->EventCode;
			$EventIdentifier = $xml->$prop->EventIdentifier;
			if (strtolower($xml->$prop->EventType) == '2') { $EventType = '<div class="icon-warning" style="color:#df9c19; margin-top:2px;" title="Application Warning"></div>'; $TypeOrder = 2; }
			if (strtolower($xml->$prop->EventType) == '1') { $EventType = '<div class="icon-minus" style="color:#933000; margin-top:2px;" title="Application Error"></div>'; $TypeOrder = 3; }
			$Logfile = $xml->$prop->Logfile;
			$Message = urldecode($xml->$prop->Message);
			$RecordNumber = $xml->$prop->RecordNumber;
			$SourceName = urldecode($xml->$prop->SourceName);
			$TimeGenerated = $xml->$prop->TimeGenerated;
			$Type = $xml->$prop->Type;
			$User = urldecode($xml->$prop->User);
			if ($orderby != 'EventType') { $Ordering = $xml->$prop->$orderby; } else { $Ordering = $TypeOrder; }
			if ($orderby == 'TimeGenerated') { $Ordering = date('YmdHis', strtotime(str_replace('/', '-', $xml->$prop->TimeGenerated))); }

			$datarow = strtolower('<TimeGenerated>' . $TimeGenerated . '</TimeGenerated><EventCode>' . $EventCode . '</EventCode><EventIdentifier>' . $EventIdentifier . '</EventIdentifier><Type>' . $Type . '</Type><EventType>' . $xml->$prop->EventType . '</EventType><Logfile>' . $Logfile . '</Logfile><Message>' . $Message . '</Message><RecordNumber>' . $RecordNumber . '</RecordNumber><SourceName>' . $SourceName . '</SourceName><ComputerName>' . $ComputerName . '</ComputerName><User>' . $User . '</User>');
			$prefilter = $_SESSION['usersett']['nodeseventviewerf'];
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
				$eventsarray[$eventscounter][1] = date('YmdHis', strtotime(str_replace('/', '-', $xml->$prop->TimeGenerated)));
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
				$eventsarray[$eventscounter][14] = 'Raw Data View:' . "\n\n" . '<TimeGenerated>' . $TimeGenerated . '</TimeGenerated>' . "\n" . '<EventCode>' . $EventCode . '</EventCode>' . "\n" . '<EventIdentifier>' . $EventIdentifier . '</EventIdentifier>' . "\n" . '<Type>' . $Type . '</Type>' . "\n" . '<EventType>' . $xml->$prop->EventType . '</EventType>' . "\n" . '<Logfile>' . $Logfile . '</Logfile>' . "\n" . '<Message>' . substr($Message, 0, 10) . '...' . '</Message>' . "\n" . '<RecordNumber>' . $RecordNumber . '</RecordNumber>' . "\n" . '<SourceName>' . $SourceName . '</SourceName>' . "\n" . '<ComputerName>' . $ComputerName . '</ComputerName>' . "\n" . '<User>' . $User . '</User>';
				if ($Message != '') { if (strlen($Message) <= 55) { $eventsarray[$eventscounter][15] = $Message; } else { $eventsarray[$eventscounter][15] = substr($Message, 0, 55) . '...'; } } else { $eventsarray[$eventscounter][15] = $SourceName; } 
				if (strlen($SourceName) <= 30) { $eventsarray[$eventscounter][16] = $SourceName; } else { $eventsarray[$eventscounter][16] = substr($SourceName, 0, 30) . '...'; }
				$eventsarray[$eventscounter][17] = $TimeGenerated;
				$eventsarray[$eventscounter][18] = $xml->$prop->EventType;
				$eventsarray[$eventscounter][19] = $xml->$prop->TimeGenerated;
				$eventscounter = $eventscounter + 1;
			}
	
		}

		if ($xml->$prop->Logfile == 'Security') {

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
			$ComputerName = $xml->$prop->ComputerName;
			$EventCode = $xml->$prop->EventCode;
			$EventIdentifier = $xml->$prop->EventIdentifier;
			if (strtolower($xml->$prop->EventType) == '5') { $EventType = '<div class="icon-locked-2" style="color:#933000; margin-top:2px;" title="Audit Failure"></div>'; $TypeOrder = 3; }
			$Logfile = $xml->$prop->Logfile;
			$Message = urldecode($xml->$prop->Message);
			$RecordNumber = $xml->$prop->RecordNumber;
			$SourceName = urldecode($xml->$prop->SourceName);
			$TimeGenerated = $xml->$prop->TimeGenerated;
			$Type = $xml->$prop->Type;
			$User = urldecode($xml->$prop->User);
			if ($orderby != 'EventType') { $Ordering = $xml->$prop->$orderby; } else { $Ordering = $TypeOrder; }
			if ($orderby == 'TimeGenerated') { $Ordering = date('YmdHis', strtotime(str_replace('/', '-', $xml->$prop->TimeGenerated))); }

			$datarow = strtolower('<TimeGenerated>' . $TimeGenerated . '</TimeGenerated><EventCode>' . $EventCode . '</EventCode><EventIdentifier>' . $EventIdentifier . '</EventIdentifier><Type>' . $Type . '</Type><EventType>' . $xml->$prop->EventType . '</EventType><Logfile>' . $Logfile . '</Logfile><Message>' . $Message . '</Message><RecordNumber>' . $RecordNumber . '</RecordNumber><SourceName>' . $SourceName . '</SourceName><ComputerName>' . $ComputerName . '</ComputerName><User>' . $User . '</User>');
			$prefilter = $_SESSION['usersett']['nodeseventviewerf'];
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
				$eventsarray[$eventscounter][1] = date('YmdHis', strtotime(str_replace('/', '-', $xml->$prop->TimeGenerated)));
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
				$eventsarray[$eventscounter][14] = 'Raw Data View:' . "\n\n" . '<TimeGenerated>' . $TimeGenerated . '</TimeGenerated>' . "\n" . '<EventCode>' . $EventCode . '</EventCode>' . "\n" . '<EventIdentifier>' . $EventIdentifier . '</EventIdentifier>' . "\n" . '<Type>' . $Type . '</Type>' . "\n" . '<EventType>' . $xml->$prop->EventType . '</EventType>' . "\n" . '<Logfile>' . $Logfile . '</Logfile>' . "\n" . '<Message>' . substr($Message, 0, 10) . '...' . '</Message>' . "\n" . '<RecordNumber>' . $RecordNumber . '</RecordNumber>' . "\n" . '<SourceName>' . $SourceName . '</SourceName>' . "\n" . '<ComputerName>' . $ComputerName . '</ComputerName>' . "\n" . '<User>' . $User . '</User>';
				if ($Message != '') { if (strlen($Message) <= 55) { $eventsarray[$eventscounter][15] = $Message; } else { $eventsarray[$eventscounter][15] = substr($Message, 0, 55) . '...'; } } else { $eventsarray[$eventscounter][15] = $SourceName; } 
				if (strlen($SourceName) <= 30) { $eventsarray[$eventscounter][16] = $SourceName; } else { $eventsarray[$eventscounter][16] = substr($SourceName, 0, 30) . '...'; }
				$eventsarray[$eventscounter][17] = $TimeGenerated;
				$eventsarray[$eventscounter][18] = $xml->$prop->EventType;
				$eventsarray[$eventscounter][19] = $xml->$prop->TimeGenerated;
				$eventscounter = $eventscounter + 1;
			}
	
		}

	}
}



rsort($eventsarray);

$eventspagearray = array();
if ($_SESSION['csv_nodes_eventviewer'] == 'csv_nodes_eventviewer') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Event Viewer";' . "\n" . '"eurysco CSV Source Node: ' . $node . '";' . "\n" . '"eurysco CSV Filter: ' . $filter . '";' . "\n" . '"eurysco CSV Total Raws: ' . $eventscounter . '";' . "\n\n");
	array_push($tmpcsvexport, '"Record Number";' . '"Computer Name";' . '"Time Generated";' . '"Type";' . '"User";' . '"Logfile";' . '"Event Code";' . '"Event Identifier";' . '"Event Type";' . '"Source Name";' . '"Message";' . "\n\n");
}
foreach ($eventsarray as $eventrow) {
	array_push($eventspagearray, '<tr class="rowselectsrv" title="' . htmlentities($eventrow[14]) . '"><td align="center"><a href=\'javascript:eventinfo("' . $eventrow[17] . '","' . $eventrow[2] . '","' . $eventrow[3] . '","' . preg_replace('/\r\n|\r|\n/','\n', str_replace('\\', '\\\\', htmlentities(preg_replace('/[^a-zA-Z0-9 \.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\r\n]*/', '', $eventrow[7]), ENT_QUOTES, 'UTF-8'))) . '","' . $eventrow[8] . '","' . str_replace('-', ' ', str_replace('\\', '\\\\', $eventrow[16])) . '","' . $eventrow[10] . '","' . $eventrow[12] . '","' . $eventrow[13] . '","' . $eventrow[6] . '","' . str_replace('\\', '\\\\', $eventrow[9]) . '");\'><img src="/images/info24-black.png" width="16" height="16" border="0" title="Event ID:&nbsp;' . $eventrow[2] . '"></a></td><td align="center" style="font-size:12px;">' . $eventrow[4] . '</td><td align="center" style="font-size:12px;">' . $eventrow[2] . '</td><td align="center" style="font-size:12px;">' . $eventrow[6] . '</td><td><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="' . htmlentities($eventrow[7], ENT_QUOTES, 'UTF-8') . '">' . $eventrow[15] . '</div></td><td align="center"><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px;">' . $eventrow[17] . '</div></td></tr>');
	if ($_SESSION['csv_nodes_eventviewer'] == 'csv_nodes_eventviewer') { array_push($tmpcsvexport, '"' . $eventrow[8] . '";' . '"' . $eventrow[10] . '";' . '"' . $eventrow[19] . '";' . '"' . $eventrow[12] . '";' . '"' . $eventrow[13] . '";' . '"' . $eventrow[6] . '";' . '"' . $eventrow[2] . '";' . '"' . $eventrow[3] . '";' . '"' . $eventrow[18] . '";' . '"' . $eventrow[9] . '";' . '"' . $eventrow[7] . '";' . "\n"); }
}

if ($_SESSION['csv_nodes_eventviewer'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/nodes_eventviewer.php?csv_nodes_eventviewer&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_nodes_eventviewer'] == 'csv_nodes_eventviewer') {
	$_SESSION['csv_nodes_eventviewer'] = $tmpcsvexport;
}
if ($_SESSION['csv_nodes_eventviewer'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_nodes_eventviewer&source=' . $node . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

$eventspages = array_chunk($eventspagearray, 75);

if ($pgkey > count($eventspages) - 1) { $pgkey = count($eventspages) - 1; }

if (count($eventspages) > 0) {
	foreach($eventspages[$pgkey] as $event) {
		$eventstable = $eventstable . $event;
	}
}

if ($eventscounter == 0) { $eventstable = $eventstable . '<tr class="rowselectsrv"><td style="font-size:12px;" align="center" colspan="6">No Results...</td></tr>'; }

$eventstable = $eventstable . '</table>';

$eventspaging = '';
if (count($eventspages) > 1) {
	if ($pgkey > 5) {
		$eventspaging = $eventspaging . '<a href="?page=1&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($eventspages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$eventspaging = $eventspaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($eventspages) > $pgkey + 6) {
		$eventspaging = $eventspaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($eventspages) . '&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($eventspages) . '</span></a>';
	}
	$eventstable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $eventspaging . '</blockquote><br />' . $eventstable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $eventspaging . '</blockquote>';
}

$totalelement = count($eventsarray);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('eventstable'=>utf8_encode($eventstable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'lastupdate'=>$lastupdate,'csvexport'=>$csvexport));

flush();

$db->close();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>