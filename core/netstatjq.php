<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/netstat.php')) { exit; }

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
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['networkstats'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'State';
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

$netstatprerarray = array();
$netstatprecounter = 0;

session_write_close();
$output = shell_exec('netstat.exe -abno');
session_start();

$netstats = preg_split("/\r\n|\n|\r/", $output);

foreach($netstats as $netstat) {

	$netstatprerarray[$netstatprecounter][0] = '';
	$netstatprerarray[$netstatprecounter][1] = '';
	$netstatprerarray[$netstatprecounter][2] = '';
	$netstatprerarray[$netstatprecounter][3] = '';
	$netstatprerarray[$netstatprecounter][4] = '';
	$netstatprerarray[$netstatprecounter][5] = '';
	$netstatprerarray[$netstatprecounter][6] = '';
	
	if (preg_match('/:/', $netstat) && !preg_match('/\\\/', $netstat)) {
		$netstatfl = explode('  ', $netstat);
		$flcount = 1;
		foreach($netstatfl as $netstatflc) {
			if (trim($netstatflc) != '') {
				if ($flcount == 1) { $netstatprerarray[$netstatprecounter][0] = trim($netstatflc); }
				if ($flcount == 2) { $netstatprerarray[$netstatprecounter][1] = trim($netstatflc); }
				if ($flcount == 3) { $netstatprerarray[$netstatprecounter][2] = trim($netstatflc); }
				if ($flcount == 4) { if (!is_numeric(trim($netstatflc))) { $netstatprerarray[$netstatprecounter][3] = trim($netstatflc); } else { $netstatprerarray[$netstatprecounter][4] = trim($netstatflc); } }
				if ($flcount == 5) { $netstatprerarray[$netstatprecounter][4] = trim($netstatflc); }
				$flcount = $flcount + 1;
			}			
		}
		$netstatprecounter = $netstatprecounter + 1;
	}
	
	if (!preg_match('/:/', trim($netstat)) && !preg_match('/\\\/', trim($netstat)) && !preg_match('/\./', trim($netstat)) && !preg_match('/ /', trim($netstat)) && trim($netstat) != '') {
		$netstatprerarray[$netstatprecounter - 1][5] = trim($netstat);
	}
	
	if (!preg_match('/:/', trim($netstat)) && !preg_match('/\\\/', trim($netstat)) && preg_match('/\[.*\]/', trim($netstat)) && trim($netstat) != '') {
		$netstatprerarray[$netstatprecounter - 1][6] = substr(trim($netstat), 1, -1);
	}
	
}


$netstatrarray = array();
$netstatcounter = 0;
	
foreach ($netstatprerarray as $netstatprerow) {

	$Protocol = $netstatprerow[0];
	$LocalAddress = $netstatprerow[1];
	$ForeignAddress = $netstatprerow[2];
	$State = $netstatprerow[3];
	$PID = $netstatprerow[4];
	$ProcessName = $netstatprerow[6];
	$ServiceName = $netstatprerow[5];
		
	$datarow = strtolower('<Protocol>' . $Protocol . '</Protocol><LocalAddress>' . $LocalAddress . '</LocalAddress><ForeignAddress>' . $ForeignAddress . '</ForeignAddress><State>' . $State . '</State><PID>' . $PID . '</PID><ProcessName>' . $ProcessName . '</ProcessName><ServiceName>' . $ServiceName . '</ServiceName>');
	$prefilter = $_SESSION['usersett']['networkstatsf'];
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
	if ($checkfilter == 0 && $Protocol != '') {
		if ($orderby == 'Protocol') { $netstatrarray[$netstatcounter][0] = $Protocol; }
		if ($orderby == 'LocalAddress') { $netstatrarray[$netstatcounter][0] = $LocalAddress; }
		if ($orderby == 'ForeignAddress') { $netstatrarray[$netstatcounter][0] = $ForeignAddress; }
		if ($orderby == 'State') { if ($State != '') { $netstatrarray[$netstatcounter][0] = $State; } else { $netstatrarray[$netstatcounter][0] = '~'; } }
		if ($orderby == 'PID') { if ($PID != '') { $netstatrarray[$netstatcounter][0] = $PID; } else { $netstatrarray[$netstatcounter][0] = '~'; } }
		if ($orderby == 'ProcessName') { if ($ProcessName != '') { $netstatrarray[$netstatcounter][0] = $ProcessName; } else { $netstatrarray[$netstatcounter][0] = '~'; } }
		if ($orderby == 'ServiceName') { if ($ServiceName != '') { $netstatrarray[$netstatcounter][0] = $ServiceName; } else { $netstatrarray[$netstatcounter][0] = '~'; } }
		$netstatrarray[$netstatcounter][1] = $Protocol;
		$netstatrarray[$netstatcounter][2] = $LocalAddress;
		$netstatrarray[$netstatcounter][3] = $ForeignAddress;
		$netstatrarray[$netstatcounter][4] = $State;
		$netstatrarray[$netstatcounter][5] = $PID;
		$netstatrarray[$netstatcounter][6] = $ProcessName;
		$netstatrarray[$netstatcounter][7] = $ServiceName;
		$netstatrarray[$netstatcounter][8] = 'Raw Data View:' . "\n\n" . '<Protocol>' . $Protocol . '</Protocol>' . "\n" . '<LocalAddress>' . $LocalAddress . '</LocalAddress>' . "\n" . '<ForeignAddress>' . $ForeignAddress . '</ForeignAddress>' . "\n" . '<State>' . $State . '</State>' . "\n" . '<PID>' . $PID . '</PID>' . "\n" . '<ProcessName>' . $ProcessName . '</ProcessName>' . "\n" . '<ServiceName>' . $ServiceName . '</ServiceName>';
		$netstatcounter = $netstatcounter + 1;
	}
		
}

if ($orderby == 'Protocol') { $obyProtocol = ' color:#8063C8;'; } else { $obyProtocol = ''; }
if ($orderby == 'LocalAddress') { $obyLocalAddress = ' color:#8063C8;'; } else { $obyLocalAddress = ''; }
if ($orderby == 'ForeignAddress') { $obyForeignAddress = ' color:#8063C8;'; } else { $obyForeignAddress = ''; }
if ($orderby == 'State') { $obyState = ' color:#8063C8;'; } else { $obyState = ''; }
if ($orderby == 'PID') { $obyPID = ' color:#8063C8;'; } else { $obyPID = ''; }
if ($orderby == 'ProcessName') { $obyProcessName = ' color:#8063C8;'; } else { $obyProcessName = ''; }
if ($orderby == 'ServiceName') { $obyServiceName = ' color:#8063C8;'; } else { $obyServiceName = ''; }

$netstattable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="5%" align="center"><a href="?orderby=Protocol&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyProtocol . '" title="Ascending Order by Type">Type</a></td><td width="20%" align="center"><a href="?orderby=LocalAddress&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyLocalAddress . '" title="Ascending Order by LocalAddress">Local Address</a></td><td width="20%" align="center"><a href="?orderby=ForeignAddress&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyForeignAddress . '" title="Ascending Order by ForeignAddress">Foreign Address</a></td><td width="10%" align="center"><a href="?orderby=State&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyState . '" title="Ascending Order by State">State</a></td><td width="5%" align="center"><a href="?orderby=PID&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyPID . '" title="Ascending Order by PID">PID</a></td><td width="20%" align="center"><a href="?orderby=ProcessName&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyProcessName . '" title="Ascending Order by Process Name">Process</a></td><td width="20%" align="center"><a href="?orderby=ServiceName&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyServiceName . '" title="Ascending Order by Service Name">Service</a></td></tr>';

if ($orderby == 'Protocol' || $orderby == 'LocalAddress' || $orderby == 'ForeignAddress' || $orderby == 'State' || $orderby == 'PID' || $orderby == 'ProcessName' || $orderby == 'ServiceName') {
	sort($netstatrarray);
} else {
	rsort($netstatrarray);
}

$netstatpagearray = array();
if ($_SESSION['csv_netstat'] == 'csv_netstat') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Netstat";' . "\n" . '"eurysco CSV Source Node: ' . $envcomputername . '";' . "\n" . '"eurysco CSV Filter: ' . $filter . '";' . "\n" . '"eurysco CSV Total Raws: ' . $netstatcounter . '";' . "\n\n");
	array_push($tmpcsvexport, '"Type";' . '"Local Address";' . '"Foreign Address";' . '"State";' . '"PID";' . '"Process Name";' . '"Service Name";' . "\n\n");
}
foreach ($netstatrarray as $netstatrow) {
	if (substr_count($netstatrow[2], ':') == 1) { $LocalAddressNrm = str_replace(':', ' : ', $netstatrow[2]); } else { $LocalAddressNrm = str_replace(']:', '] : ', $netstatrow[2]); } 
	if (substr_count($netstatrow[3], ':') == 1) { $ForeignAddressNrm = str_replace(':', ' : ', $netstatrow[3]); } else { $ForeignAddressNrm = str_replace(']:', '] : ', $netstatrow[3]); }
	if (strlen($netstatrow[2]) > 25) { $LocalAddressChr = '10px'; } else { $LocalAddressChr = '12px'; }
	if (strlen($netstatrow[3]) > 25) { $ForeignAddressChr = '10px'; } else { $ForeignAddressChr = '12px'; }
	if (strlen($netstatrow[6]) > 15) { $LimitProcess = substr($netstatrow[6], 0, 15) . '...'; } else { $LimitProcess = $netstatrow[6]; }
	if (strlen($netstatrow[7]) > 12) { $LimitService = substr($netstatrow[7], 0, 12) . '...'; } else { $LimitService = $netstatrow[7]; }
	if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['processcontrol'] > 0) { $NetStatToProcPID = '<a href="/processes.php?filter=IDProcess.' . $netstatrow[5] . '..IDProcess" style="font-size:12px;" title="Filter Processes by PID">' . $netstatrow[5] . '</a>'; $NetStatToProcesses = '<a href="/processes.php?filter=FileName.' . $netstatrow[6] . '..FileName" style="font-size:12px;" title="Filter Processes by File Name">' . $LimitProcess . '</a>'; } else { $NetStatToProcPID = $netstatrow[5]; $NetStatToProcesses = $LimitProcess; }
	if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['servicecontrol'] > 0) { $NetStatToServices = '<a href="/services.php?filter=Name.' . $netstatrow[7] . '..Name" style="font-size:12px;" title="Filter Services by Name">' . $LimitService . '</a>'; } else { $NetStatToServices = $LimitService; }
	array_push($netstatpagearray, '<tr class="rowselect" title="' . htmlentities($netstatrow[8]) . '"><td style="font-size:12px;" align="center">' . $netstatrow[1] . '</td><td style="font-size:' . $LocalAddressChr . '; white-space:nowrap; table-layout:fixed; overflow:hidden;" align="center">' . $LocalAddressNrm . '</td><td style="font-size:' . $ForeignAddressChr . '; white-space:nowrap; table-layout:fixed; overflow:hidden;" align="center">' . $ForeignAddressNrm . '</td><td style="font-size:10px;" align="center">' . $netstatrow[4] . '</td><td style="font-size:12px;" align="center">' . $NetStatToProcPID . '</td><td style="font-size:12px;" align="center" title="' . $netstatrow[6] . '">' . $NetStatToProcesses . '</td><td style="font-size:12px;" align="center" title="' . $netstatrow[7] . '">' . $NetStatToServices . '</td></tr>');
	if ($_SESSION['csv_netstat'] == 'csv_netstat') { array_push($tmpcsvexport, '"' . $netstatrow[1] . '";' . '"' . $netstatrow[2] . '";' . '"' . $netstatrow[3] . '";' . '"' . $netstatrow[4] . '";' . '"' . $netstatrow[5] . '";' . '"' . $netstatrow[6] . '";' . '"' . $netstatrow[7] . '";' . "\n"); }
}

if ($_SESSION['csv_netstat'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/netstat.php?csv_netstat&orderby=' . $orderby . '&filter=' . urlencode($filter) . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_netstat'] == 'csv_netstat') {
	$_SESSION['csv_netstat'] = $tmpcsvexport;
}
if ($_SESSION['csv_netstat'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_netstat&source=' . $envcomputername . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

$netstatpages = array_chunk($netstatpagearray, 100);

if ($pgkey > count($netstatpages) - 1) { $pgkey = count($netstatpages) - 1; }

if (count($netstatpages) > 0) {
	foreach($netstatpages[$pgkey] as $netstatrw) {
		$netstattable = $netstattable . $netstatrw;
	}
}

if ($netstatcounter == 0) { $netstattable = $netstattable . '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="7">No Results...</td></tr>'; }

$netstattable = $netstattable . '</table>';

$netstatpaging = '';
if (count($netstatpages) > 1) {
	if ($pgkey > 5) {
		$netstatpaging = $netstatpaging . '<a href="?page=1&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($netstatpages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$netstatpaging = $netstatpaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($netstatpages) > $pgkey + 6) {
		$netstatpaging = $netstatpaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($netstatpages) . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($netstatpages) . '</span></a>';
	}
	$netstattable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $netstatpaging . '</blockquote><br />' . $netstattable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $netstatpaging . '</blockquote>';
}

$totalelement = count($netstatrarray);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('netstattable'=>utf8_encode($netstattable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'csvexport'=>$csvexport));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>