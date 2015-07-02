<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes_services.php')) { exit; }

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
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesservicecontrol'] > 0) {  } else { exit; }

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'DisplayName';
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

if (!isset($_SESSION['csv_nodes_services'])) {
	$_SESSION['csv_nodes_services'] = '';
}

$lastupdate = '';

if ($orderby == 'ProcessId') { $obyProcessId = ' color:#8063C8;'; } else { $obyProcessId = ''; }
if ($orderby == 'DisplayName') { $obyDisplayName = ' color:#8063C8;'; } else { $obyDisplayName = ''; }
if ($orderby == 'State') { $obyState = ' color:#8063C8;'; } else { $obyState = ''; }
if ($orderby == 'StartMode') { $obyStartMode = ' color:#8063C8;'; } else { $obyStartMode = ''; }
if ($orderby == 'ExitCode') { $obyExitCode = ' color:#8063C8;'; } else { $obyExitCode = ''; }

$servicetable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="1%" style="font-size:12px; font-weight:bold;" align="center"></td><td width="6%" align="center"><a href="?orderby=ProcessId&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyProcessId . '" title="Ascending Order by PID">PID</a></td><td width="60%"><a href="?orderby=DisplayName&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyDisplayName . '" title="Ascending Order by Display Name">Display Name</a></td><td width="12%" align="center"><a href="?orderby=State&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyState . '" title="Ascending Order by State">State</a></td><td width="12%" align="center"><a href="?orderby=StartMode&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyStartMode . '" title="Ascending Order by Start Mode">Start Mode</a></td><td align="center"><a href="?orderby=ExitCode&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyExitCode . '" title="Ascending Order by Exit Code">Exit Code</a></td></tr>';

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
			if ($auditrow['exitcode'] == 1056) {
				$message = '<blockquote style="font-size:12px; background-color:#C97D15; color:#FFFFFF; border-left-color:#A25600;">' . urldecode($auditrow['description']) . '</blockquote><br />';
			} else {
				$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">' . urldecode($auditrow['description']) . '</blockquote><br />';
			}
		}
	}
	$dbaudit->close();
}

$servicearray = array();
$servicecounter = 0;

$nodepath = str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $node . '\\';

$lastupdate = 'N/A';
if (!is_null($db->querySingle('SELECT node FROM xmlServices WHERE node = "' . $node . '"')) || file_exists($nodepath . 'services.xml.gz')) {
	if (file_exists($nodepath . 'services.xml.gz')) { $lastupdate = date('d/m/Y H:i:s', filemtime(str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $node . '\\services.xml.gz')); }
	$xml = simplexml_load_string($db->querySingle('SELECT xml FROM xmlServices WHERE node = "' . $node . '"'));
	if (!is_object($xml)) {
		$fp = gzopen($nodepath . 'services.xml.gz', 'rb');
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

		$ProcessId = $xml->$prop->ProcessId;
		$Name = trim(urldecode($xml->$prop->Name));
		$DisplayName = trim(urldecode($xml->$prop->DisplayName));
		$State = $xml->$prop->State;
		$StartMode = $xml->$prop->StartMode;
		$ExitCode = $xml->$prop->ExitCode;
		$StartName = urldecode($xml->$prop->StartName);

		$datarow = strtolower('<DisplayName>' . $DisplayName . '</DisplayName><Name>' . $Name . '</Name><ProcessId>' . $ProcessId . '</ProcessId><State>' . $State . '</State><StartMode>' . $StartMode . '</StartMode><ExitCode>' . $ExitCode . '</ExitCode><StartName>' . $StartName . '</StartName>');
		$prefilter = $_SESSION['usersett']['nodesservicecontrolf'];
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
			$servicearray[$servicecounter][0] = strtolower($xml->$prop->$orderby);
			$servicearray[$servicecounter][1] = $DisplayName;
			$servicearray[$servicecounter][2] = $ProcessId;
			$servicearray[$servicecounter][3] = $Name;
			$servicearray[$servicecounter][4] = $State;
			$servicearray[$servicecounter][5] = $StartMode;
			$servicearray[$servicecounter][6] = $ExitCode;
			$servicearray[$servicecounter][7] = strtolower($StartName);
			$servicearray[$servicecounter][8] = 'Raw Data View:' . "\n\n" . '<DisplayName>' . $DisplayName . '</DisplayName>' . "\n" . '<Name>' . $Name . '</Name>' . "\n" . '<ProcessId>' . $ProcessId . '</ProcessId>' . "\n" . '<State>' . $State . '</State>' . "\n" . '<StartMode>' . $StartMode . '</StartMode>' . "\n" . '<ExitCode>' . $ExitCode . '</ExitCode>' . "\n" . '<StartName>' . $StartName . '</StartName>';
			$servicecounter = $servicecounter + 1;
		}

	}
}

if ($orderby != 'ProcessId') {
	sort($servicearray);
} else {
	rsort($servicearray);
}

$agentservicedisplayname = '### NULL AGENT NAME ###';
if (file_exists($config_agentsrv)) {
	$xmlagent = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_agentsrv, true)))));
	$agentservicedisplayname = $xmlagent->settings->agentservicedisplayname;
}

$coreservicedisplayname = '### NULL CORE NAME ###';
if (file_exists($config_coresrv)) {
	$xmlcore = simplexml_load_file($config_coresrv);
	$coreservicedisplayname = $xmlcore->settings->coreservicedisplayname;
}

$executorservicedisplayname = '### NULL EXECUTOR NAME ###';
if (file_exists($config_executorsrv)) {
	$xmlexecutor = simplexml_load_file($config_executorsrv);
	$executorservicedisplayname = $xmlexecutor->settings->executorservicedisplayname;
}

$serverservicedisplayname = '### NULL SERVER NAME ###';
if (file_exists($config_server)) {
	$xmlserver = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_server, true)))));
	$serverservicedisplayname = $xmlserver->settings->serverservicedisplayname;
}

$servicespagearray = array();
if ($_SESSION['csv_nodes_services'] == 'csv_nodes_services') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Services";' . "\n" . '"eurysco CSV Source Node: ' . $node . '";' . "\n" . '"eurysco CSV Filter: ' . $filter . '";' . "\n" . '"eurysco CSV Total Raws: ' . $servicecounter . '";' . "\n\n");
	array_push($tmpcsvexport, '"Display Name";' . '"Name";' . '"Process ID";' . '"State";' . '"Start Mode";' . '"Exit Code";' . '"Username";' . "\n\n");
}
foreach ($servicearray as $servicerow) {
	if (strtolower($servicerow[4]) == 'running') {
		$serviceicon = 'running.png';
		$servicestat = 'Running';
	} else {
		if ($servicerow[6] != 0 && $servicerow[6] != 1077) {
			$serviceicon = 'error.png';
			$servicestat = 'Error';
		} else {
			$serviceicon = 'notrunning.png';
			$servicestat = 'Not Running';
		}
	}
	if ($servicerow[3] == 'euryscoAgent' || $servicerow[3] == 'euryscoServer' || $servicerow[3] == 'euryscoServerSSL') {
		array_push($servicespagearray, '<tr class="rowselectsrv"><td style="font-size:12px;" align="center">&nbsp;</td><td style="font-size:12px;" align="center">' . $servicerow[2] . '</td><td><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">' . $servicerow[1] . '</div></td><td style="font-size:12px;" align="center">' . $servicerow[4] . '</td><td style="font-size:12px;" align="center">' . $servicerow[5] . '</td><td style="font-size:12px;" align="center">' . $servicerow[6] . '</td></tr>');
	} else {
		if (strlen($servicerow[1]) > 30) { $LimitName = substr($servicerow[1], 0, 30) . '&nbsp;[...]'; } else { $LimitName = $servicerow[1]; }
		if (strlen($servicerow[3]) > 15) { $ShortName = substr($servicerow[3], 0, 15) . '&nbsp;[...]'; } else { $ShortName = $servicerow[3]; }
		if (strlen(strtolower(str_replace('\\', '\\\\', $servicerow[7]))) > 26) { $UserName = substr(strtolower(str_replace('\\', '\\\\', $servicerow[7])), 0, 26) . '&nbsp;[...]'; } else { $UserName = strtolower(str_replace('\\', '\\\\', $servicerow[7])); }
		if ($servicerow[2] == 0) { $servicerow[2] = '-'; }
		array_push($servicespagearray, '<tr class="rowselectsrv" title="' . htmlentities($servicerow[8]) . '"><td style="font-size:12px;" align="center"><a href=\'javascript:serviceexec("' . $servicerow[2] . '","' . str_replace('\'', '%27', $servicerow[1]) . '","' . str_replace('\'', '%27', $servicerow[3]) . '","' . $servicerow[4] . '","' . $servicerow[5] . '","' . $servicerow[6] . '","' . str_replace('\'', '%27', $UserName) . '","' . str_replace('\'', '%27', $LimitName) . '","' . str_replace('\'', '%27', $ShortName) . '","' . urlencode(urlencode($servicerow[3])) . '");\'><img src="/img/' . $serviceicon . '" width="16" height="16" border="0" title="Service ' . $servicestat . ': ' . $servicerow[3] . '"></a></td><td style="font-size:12px;" align="center">' . $servicerow[2] . '</td><td><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">' . $servicerow[1] . '</div></td><td style="font-size:12px;" align="center">' . $servicerow[4] . '</td><td style="font-size:12px;" align="center">' . $servicerow[5] . '</td><td style="font-size:12px;" align="center">' . $servicerow[6] . '</td></tr>');
	}
	if ($_SESSION['csv_nodes_services'] == 'csv_nodes_services') { array_push($tmpcsvexport, '"' . $servicerow[1] . '";' . '"' . $servicerow[3] . '";' . '"' . $servicerow[2] . '";' . '"' . $servicerow[4] . '";' . '"' . $servicerow[5] . '";' . '"' . $servicerow[6] . '";' . '"' . $servicerow[7] . '";' . "\n"); }
}

if ($_SESSION['csv_nodes_services'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/nodes_services.php?csv_nodes_services&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_nodes_services'] == 'csv_nodes_services') {
	$_SESSION['csv_nodes_services'] = $tmpcsvexport;
}
if ($_SESSION['csv_nodes_services'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_nodes_services&source=' . $node . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

$servicespages = array_chunk($servicespagearray, 100);

if ($pgkey > count($servicespages) - 1) { $pgkey = count($servicespages) - 1; }

if (count($servicespages) > 0) {
	foreach($servicespages[$pgkey] as $servicerw) {
		$servicetable = $servicetable . $servicerw;
	}
}

if ($servicecounter == 0) { $servicetable = $servicetable . '<tr class="rowselectsrv"><td style="font-size:12px;" align="center" colspan="6">No Results...</td></tr>'; }

$servicetable = $servicetable . '</table>';

$servicespaging = '';
if (count($servicespages) > 1) {
	if ($pgkey > 5) {
		$servicespaging = $servicespaging . '<a href="?page=1&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($servicespages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$servicespaging = $servicespaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($servicespages) > $pgkey + 6) {
		$servicespaging = $servicespaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($servicespages) . '&orderby=' . $orderby . '&node=' . $node . '&domain=' . $domain . '&computerip=' . $computerip . '&executorport=' . $executorport . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($servicespages) . '</span></a>';
	}
	$servicetable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $servicespaging . '</blockquote><br />' . $servicetable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $servicespaging . '</blockquote>';
}

$totalelement = count($servicearray);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('servicetable'=>utf8_encode($servicetable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'lastupdate'=>$lastupdate,'csvexport'=>$csvexport,'message'=>$message));

flush();

$db->close();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>