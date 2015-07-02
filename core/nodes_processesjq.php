<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_processes.php')) { exit; }

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
	set_time_limit($_GET['phptimeout']);
} else {
	set_time_limit(120);
}

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$start = $time;

include('/include/init.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesprocesscontrol'] > 0) {  } else { exit; }

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'PercentProcessorTime';
}

if (isset($_GET['filter'])) {
	$filter = $_GET['filter'];
} else {
	$filter = '';
}

if (isset($_GET['cid'])) {
	$cid = $_GET['cid'];
} else {
	$cid = '';
}

if (isset($_GET['message'])) {
	$message = $_GET['message'];
} else {
	$message = '';
}

if (isset($_GET['page'])) {
	$pgkey = $_GET['page'];
} else {
	$pgkey = 0;
}

if (!isset($_SESSION['csv_nodes_processes'])) {
	$_SESSION['csv_nodes_processes'] = '';
}

$lastupdate = '';

$db = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoServer');
$db->busyTimeout(5000);
$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');

if ($cid != '') {
	$dbaudit = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoAudit');
	$dbaudit->busyTimeout(30000);
	$dbaudit->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');
	$audittot = $dbaudit->querySingle('SELECT COUNT(id) FROM auditLog');
	if ($audittot > 500) { $audittot = $audittot - 500; } else { $audittot = 0; }
	$allaudit = $dbaudit->query('SELECT description, exitcode FROM auditLog WHERE cid = "' . $cid . '" LIMIT 500 OFFSET ' . $audittot);
	while ($auditrow = $allaudit->fetchArray()) {
		if ($auditrow['exitcode'] == 0) {
			$message = '<blockquote style="font-size:12px; background-color:#603CBA; color:#FFFFFF; border-left-color:#482E8C;">' . urldecode($auditrow['description']) . '</blockquote><br />';
		} else {
			$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">' . urldecode($auditrow['description']) . '</blockquote><br />';
		}
	}
	$dbaudit->close();
}

$processarray = array();
$procecounter = 0;
$idlecpu = 100;
$WorkingSetPrivatePropName = 'WorkingSetPrivate';

$nodepath = str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $node . '\\';

$lastupdate = 'N/A';
if (!is_null($db->querySingle('SELECT node FROM xmlProcesses WHERE node = "' . $node . '"')) || file_exists($nodepath . 'processes.xml.gz')) {
	if (file_exists($nodepath . 'processes.xml.gz')) { $lastupdate = date('d/m/Y H:i:s', filemtime(str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $node . '\\processes.xml.gz')); }
	$xml = simplexml_load_string($db->querySingle('SELECT xml FROM xmlProcesses WHERE node = "' . $node . '"'));
	if (!is_object($xml)) {
		$fp = gzopen($nodepath . 'processes.xml.gz', 'rb');
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

		$Name = urldecode($xml->$prop->Name);
		$PercentProcessorTime = $xml->$prop->PercentProcessorTime;
		$WorkingSetPrivate = $xml->$prop->WorkingSetPrivate;
		$IDProcess = $xml->$prop->IDProcess;
		$CreatingProcessID = $xml->$prop->CreatingProcessID;
		$CommandLine = urldecode($xml->$prop->CommandLine);
		$FileName = urldecode($xml->$prop->FileName);
		$ExecutablePath = urldecode($xml->$prop->ExecutablePath);
		$UserName = $xml->$prop->UserName;

		$datarow = strtolower('<Name>' . $Name . '</Name><IDProcess>' . $IDProcess . '</IDProcess><PercentProcessorTime>' . $PercentProcessorTime . '</PercentProcessorTime><WorkingSetPrivate>' . $WorkingSetPrivate . '</WorkingSetPrivate><CreatingProcessID>' . $CreatingProcessID . '</CreatingProcessID><CommandLine>' . $CommandLine . '</CommandLine><FileName>' . $FileName . '</FileName><ExecutablePath>' . $ExecutablePath . '</ExecutablePath><UserName>' . $UserName . '</UserName>');
		$prefilter = $_SESSION['usersett']['nodesprocesscontrolf'];
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
			$processarray[$procecounter][0] = strtolower(str_replace('.', '', str_replace('%', '', str_replace(' KB', '', $xml->$prop->$orderby))));
			$processarray[$procecounter][1] = $IDProcess;
			$processarray[$procecounter][2] = $Name;
			$processarray[$procecounter][3] = $PercentProcessorTime;
			$processarray[$procecounter][4] = $WorkingSetPrivate;
			$processarray[$procecounter][5] = $CreatingProcessID;
			$processarray[$procecounter][6] = 'Raw Data View:' . "\n\n" . '<Name>' . $Name . '</Name>' . "\n" . '<IDProcess>' . $IDProcess . '</IDProcess>' . "\n" . '<PercentProcessorTime>' . $PercentProcessorTime . '</PercentProcessorTime>' . "\n" . '<WorkingSetPrivate>' . $WorkingSetPrivate . '</WorkingSetPrivate>' . "\n" . '<CreatingProcessID>' . $CreatingProcessID . '</CreatingProcessID>' . "\n" . '<CommandLine>' . $CommandLine . '</CommandLine>' . "\n" . '<FileName>' . $FileName . '</FileName>' . "\n" . '<ExecutablePath>' . $ExecutablePath . '</ExecutablePath>' . "\n" . '<UserName>' . $UserName . '</UserName>';
			$processarray[$procecounter][7] = $CommandLine;
			$processarray[$procecounter][8] = $FileName;
			$processarray[$procecounter][9] = $ExecutablePath;
			$processarray[$procecounter][10] = $UserName;
			$procecounter = $procecounter + 1;
		}

	}
}

if ($orderby == 'Name' || $orderby == 'User' || $orderby == 'IDProcess' || $orderby == 'CreatingProcessID') {
	sort($processarray);
} else {
	rsort($processarray);
}

$processpagearray = array();
if ($_SESSION['csv_nodes_processes'] == 'csv_nodes_processes') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Processes";' . "\n" . '"eurysco CSV Source Node: ' . $node . '";' . "\n" . '"eurysco CSV Filter: ' . $filter . '";' . "\n" . '"eurysco CSV Total Raws: ' . $procecounter . '";' . "\n\n");
	array_push($tmpcsvexport, '"Name";' . '"PID";' . '"CPU Usage";' . '"Memory Usage";' . '"Parent PID";' . '"Command Line";' . '"File Name";' . '"Executable Path";' . '"Username";' . "\n\n");
}
foreach ($processarray as $processrow) {
	if ($processrow[2] == 'euryscosrv' || $processrow[2] == 'euryscosrv#1' || $processrow[2] == 'euryscosrv#2' || $processrow[2] == 'euryscosrv#3' || $processrow[2] == 'euryscosrv#4' || $processrow[2] == 'euryscosrv#5' || $processrow[2] == 'euryscosrv#6' || $processrow[2] == 'php_eurysco_agent' || $processrow[2] == 'php_eurysco_server' || $processrow[2] == 'httpd_eurysco_server' || $processrow[2] == 'httpd_eurysco_server#1' || $processrow[2] == 'eurysco.agent.status.check' || $processrow[2] == 'eurysco.agent.exec.timeout') {
		array_push($processpagearray, '<tr class="rowselectsrv" title="' . htmlentities($processrow[6]) . '"><td style="font-size:12px;" align="center">&nbsp;</td><td style="font-size:12px;" align="center">' . $processrow[1] . '</td><td style="font-size:12px;">' . $processrow[2] . '</td><td style="font-size:12px;" align="center"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">' . $processrow[10] . '</div></td><td style="font-size:12px;" align="center">' . $processrow[3] . '</td><td style="font-size:12px;" align="right">' . $processrow[4] . '</td><td style="font-size:12px;" align="center">' . $processrow[5] . '</td></tr>');
	} else {
		if (strlen($processrow[2]) > 12) { $LimitName = substr($processrow[2], 0, 12) . '&nbsp;[...]'; } else { $LimitName = $processrow[2]; }
		array_push($processpagearray, '<tr class="rowselectsrv" title="' . htmlentities($processrow[6]) . '"><td style="font-size:12px;" align="center"><a href=\'javascript:endprocess("' . $processrow[1] . '","' . str_replace(' ', '&nbsp;', str_replace('\'', '%27', $processrow[2])) . '","' . $processrow[3] . '","' . $processrow[4] . '","' . $processrow[5] . '","' . str_replace(' ', '&nbsp;', str_replace('\'', '%27', $LimitName)) . '","' . urlencode(urlencode($processrow[2])) . '","' . $processrow[10] . '");\'><img src="/images/collapse24-black.png" width="16" height="16" border="0" title="Process: ' . $processrow[2] . '"></a></td><td style="font-size:12px;" align="center">' . $processrow[1] . '</td><td style="font-size:12px;">' . str_replace(' ', '&nbsp;', $processrow[2]) . '</td><td style="font-size:12px;" align="center"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">' . $processrow[10] . '</div></td><td style="font-size:12px;" align="center">' . $processrow[3] . '</td><td style="font-size:12px;" align="right">' . $processrow[4] . '</td><td style="font-size:12px;" align="center">' . $processrow[5] . '</td></tr>');
	}
	if ($_SESSION['csv_nodes_processes'] == 'csv_nodes_processes') { array_push($tmpcsvexport, '"' . $processrow[2] . '";' . '"' . $processrow[1] . '";' . '"' . $processrow[3] . '";' . '"' . $processrow[4] . '";' . '"' . $processrow[5] . '";' . '"' . $processrow[7] . '";' . '"' . $processrow[8] . '";' . '"' . $processrow[9] . '";' . '"' . $processrow[10] . '";' . "\n"); }
}

if ($_SESSION['csv_nodes_processes'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/nodes_processes.php?csv_nodes_processes&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_nodes_processes'] == 'csv_nodes_processes') {
	$_SESSION['csv_nodes_processes'] = $tmpcsvexport;
}
if ($_SESSION['csv_nodes_processes'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_nodes_processes&source=' . $node . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

if ($orderby == 'IDProcess') { $obyIDProcess = ' color:#8063C8;'; } else { $obyIDProcess = ''; }
if ($orderby == 'Name') { $obyName = ' color:#8063C8;'; } else { $obyName = ''; }
if ($orderby == 'UserName') { $obyUser = ' color:#8063C8;'; } else { $obyUser = ''; }
if ($orderby == 'PercentProcessorTime') { $obyPercentProcessorTime = ' color:#8063C8;'; } else { $obyPercentProcessorTime = ''; }
if ($orderby == $WorkingSetPrivatePropName) { $obyWorkingSetPrivate = ' color:#8063C8;'; } else { $obyWorkingSetPrivate = ''; }
if ($orderby == 'CreatingProcessID') { $obyCreatingProcessID = ' color:#8063C8;'; } else { $obyCreatingProcessID = ''; }

$processtable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="1%" style="font-size:12px; font-weight:bold;" align="center"></td><td width="6%" align="center"><a href="?orderby=IDProcess&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyIDProcess . '" title="Ascending Order by PID">PID</a></td><td width="50%"><a href="?orderby=Name&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyName . '" title="Ascending Order by Name">Name</a></td><td width="5%" align="center"><a href="?orderby=UserName&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyUser . '" title="Ascending Order by User">User</a></td><td width="12%" align="center"><a href="?orderby=PercentProcessorTime&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyPercentProcessorTime . '" title="Descending Order by CPU Usage">CPU Usage</a></td><td width="10%" align="center"><a href="?orderby=' . $WorkingSetPrivatePropName . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyWorkingSetPrivate . '" title="Descending Order by Memory Usage">Memory Usage</a></td><td align="center"><a href="?orderby=CreatingProcessID&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyCreatingProcessID . '" title="Ascending Order by Parent PID">Parent PID</a></td></tr>';

$processpages = array_chunk($processpagearray, 100);

if ($pgkey > count($processpages) - 1) { $pgkey = count($processpages) - 1; }

if (count($processpages) > 0) {
	foreach($processpages[$pgkey] as $processrw) {
		$processtable = $processtable . $processrw;
	}
}

if ($procecounter == 0) { $processtable = $processtable . '<tr class="rowselectsrv"><td style="font-size:12px;" align="center" colspan="7">No Results...</td></tr>'; }

$processtable = $processtable . '</table>';

$processpaging = '';
if (count($processpages) > 1) {
	if ($pgkey > 5) {
		$processpaging = $processpaging . '<a href="?page=1&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($processpages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$processpaging = $processpaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($processpages) > $pgkey + 6) {
		$processpaging = $processpaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($processpages) . '&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($processpages) . '</span></a>';
	}
	$processtable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $processpaging . '</blockquote><br />' . $processtable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $processpaging . '</blockquote>';
}

$totalelement = count($processarray);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('processtable'=>utf8_encode($processtable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'lastupdate'=>$lastupdate,'csvexport'=>$csvexport,'message'=>$message));

flush();

$db->close();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>