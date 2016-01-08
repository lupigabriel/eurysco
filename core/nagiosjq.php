<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nagios.php')) { exit; }

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
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nagiosstatus'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'NagiosCommandExit';
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

if (isset($_GET['nrpepathname'])) {
	$nrpepathname = $_GET['nrpepathname'];
} else {
	$nrpepathname = '';
}

if (isset($_GET['nscppathname'])) {
	$nscppathname = $_GET['nscppathname'];
} else {
	$nscppathname = '';
}


$lastupdate = '';

if ($orderby == 'NagiosCommandName') { $obyNagiosCommandName = ' color:#8063C8;'; } else { $obyNagiosCommandName = ''; }
if ($orderby == 'NagiosCommandMsgs') { $obyNagiosCommandMsgs = ' color:#8063C8;'; } else { $obyNagiosCommandMsgs = ''; }
if ($orderby == 'NagiosCommandExit') { $obyNagiosCommandExit = ' color:#8063C8;'; } else { $obyNagiosCommandExit = ''; }

$nagioscommandname = '';
$nagioscommandstrn = '';
$nagioscommandmsgs = '';
$nagioscommandexit = 0;
$nagiostotalcount = 0;
$nagiosnormacount = 0;
$nagioswarnicount = 0;
$nagioscriticount = 0;
$nagiosunknocount = 0;
$nagios_status = -1;


$nagiostable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="1%" align="center"><a href="?orderby=NagiosCommandExit" style="font-size:12px; font-weight:bold;' . $obyNagiosCommandExit . '" title="Descending Order by Status">Status</a></td><td width="5%" align="center"><a href="?orderby=NagiosCommandName" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyNagiosCommandName . '" title="Ascending Order by Command Name">Name</a></td><td><a href="?orderby=NagiosCommandMsgs" style="font-size:12px; font-weight:bold;' . $obyNagiosCommandMsgs . '" title="Ascending Order by Output Message">Message</a></td></tr>';

$nagiosarray = array();
$nagioscounter = 0;


$NrpePath = preg_replace('/nrpe_nt.exe.*/i', '', $nrpepathname);
if ($nrpepathname != '' && file_exists($NrpePath . 'nrpe_nt.exe') && file_exists($NrpePath . 'nrpe.cfg') && is_readable($NrpePath . 'nrpe.cfg')) {
	
	$filearr = file($NrpePath . 'nrpe.cfg');
	$lastlines = array_slice($filearr, -1000);
	
	foreach ($lastlines as $lastline) {
		if (preg_match('/command\[/i', $lastline) && !preg_match('/#.*command\[/i', $lastline)) {
			$nagioscommandstrn = preg_replace('/[\n\r]/', '', preg_replace('/.*=/', '', $lastline));
			if (strpos($nagioscommandstrn, ':\\') > 0) {
				if (!file_exists(preg_replace('/\.exe.*/', '.exe', $nagioscommandstrn))) {
					$nagioscommandstrn = '';
				}
			} else {
				if (file_exists(preg_replace('/\.exe.*/', '.exe', $NrpePath . $nagioscommandstrn))) {
					$nagioscommandstrn = $NrpePath . $nagioscommandstrn;
				} else {
					$nagioscommandstrn = '';
				}
			}
			if ($nagioscommandstrn != '') {
				
				$nagioscommandname = strtolower(preg_replace('/.*\[/', '', preg_replace('/\].*/', '', $lastline)));
				session_write_close();
				$nagioscommandmsgs = exec($nagioscommandstrn, $errorarray, $nagioscommandexit);
				session_start();
				
				$nagios_status = 0;
				
				$datarow = strtolower('<NagiosCommandName>' . $nagioscommandname . '</NagiosCommandName><NagiosCommandStrn>' . $nagioscommandstrn . '</NagiosCommandStrn><NagiosCommandMsgs>' . $nagioscommandmsgs . '</NagiosCommandMsgs><NagiosCommandExit>' . $nagioscommandexit . '</NagiosCommandExit>');
				$prefilter = $_SESSION['usersett']['nagiosstatusf'];
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
					$nagiosexitorder = 0;
					if ($nagioscommandexit == 0) { $nagiosexitorder = 0; }
					if ($nagioscommandexit == 3) { $nagiosexitorder = 1; }
					if ($nagioscommandexit == 1 || $nagioscommandexit > 3 || $nagioscommandexit < 0) { $nagiosexitorder = 2; }
					if ($nagioscommandexit == 2) { $nagiosexitorder = 3; }
					
					$nagiosstatusicon = 'Normal';
					if ($nagioscommandexit == 0) { $nagiosstatusicon = 'Normal'; }
					if ($nagioscommandexit == 3) { $nagiosstatusicon = 'Unknown'; }
					if ($nagioscommandexit == 1 || $nagioscommandexit > 3 || $nagioscommandexit < 0) { $nagiosstatusicon = 'Warning'; }
					if ($nagioscommandexit == 2) { $nagiosstatusicon = 'Critical'; }
					
					if ($orderby == 'NagiosCommandName') { $nagiosarray[$nagioscounter][0] = strtolower($nagioscommandname); }
					if ($orderby == 'NagiosCommandMsgs') { $nagiosarray[$nagioscounter][0] = strtolower($nagioscommandmsgs); }
					if ($orderby == 'NagiosCommandExit') { $nagiosarray[$nagioscounter][0] = $nagiosexitorder; }
					$nagiosarray[$nagioscounter][1] = $nagioscommandname;
					$nagiosarray[$nagioscounter][2] = $nagioscommandstrn;
					$nagiosarray[$nagioscounter][3] = $nagioscommandmsgs;
					$nagiosarray[$nagioscounter][4] = $nagioscommandexit;
					$nagiosarray[$nagioscounter][5] = '<img src="/img/nagios_' . $nagiosstatusicon . '.png" width="10" height="13" style="vertical-align: middle;" title="' . $nagiosstatusicon . "\nReturn Code: " . $nagioscommandexit . '" />';
					$nagiosarray[$nagioscounter][6] = $nagiosstatusicon . "\nReturn Code: " . $nagioscommandexit;
					$nagiosarray[$nagioscounter][7] = 'Raw Data View:' . "\n\n" . '<NagiosCommandName>' . $nagioscommandname . '</NagiosCommandName>' . "\n" . '<NagiosCommandStrn>' . $nagioscommandstrn . '</NagiosCommandStrn>' . "\n" . '<NagiosCommandMsgs>' . $nagioscommandmsgs . '</NagiosCommandMsgs>' . "\n" . '<NagiosCommandExit>' . $nagioscommandexit . '</NagiosCommandExit>';
					
					$nagioscounter = $nagioscounter + 1;
				}
				
			}
		}
	}

}


$NscpPath = str_replace('"', '', preg_replace('/nscp.exe.*/i', '', $nscppathname));

if ($nscppathname != '' && file_exists($NscpPath . 'nscp.exe') && file_exists($NscpPath . 'nsclient.ini') && is_readable($NscpPath . 'nsclient.ini')) {

	$filearr = file($NscpPath . 'nsclient.ini');
	$lastlines = array_slice($filearr, -1000);
	
	$checkinitalias = 0;
	foreach ($lastlines as $lastline) {
		if (preg_match('/\[.*alias.*\]/i', $lastline)) { $checkinitalias = 1; }
		if ($checkinitalias == 1 && preg_match('/=/i', $lastline) && !preg_match('/;/i', $lastline)) {
			$nagioscommandstrn = trim(preg_replace('/.*@\|@/', '', preg_replace('/=/', '@|@', $lastline, 1)));
			$nagioscommandname = strtolower(trim(preg_replace('/@\|@.*/', '', preg_replace('/=/', '@|@', $lastline, 1))));
			if ($nagioscommandname != '' && $nagioscommandstrn != '') {
				session_write_close();
				$nagioscommandmsgs = exec('"' . $NscpPath . 'nscp.exe" client --query ' . $nagioscommandname, $errorarray, $nagioscommandexit);
				session_start();
				$nagios_status = 0;
				
				$datarow = strtolower('<NagiosCommandName>' . $nagioscommandname . '</NagiosCommandName><NagiosCommandStrn>' . $nagioscommandstrn . '</NagiosCommandStrn><NagiosCommandMsgs>' . $nagioscommandmsgs . '</NagiosCommandMsgs><NagiosCommandExit>' . $nagioscommandexit . '</NagiosCommandExit>');
				$prefilter = $_SESSION['usersett']['nagiosstatusf'];
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
					$nagiosexitorder = 0;
					if ($nagioscommandexit == 0) { $nagiosexitorder = 0; }
					if ($nagioscommandexit == 3) { $nagiosexitorder = 1; }
					if ($nagioscommandexit == 1 || $nagioscommandexit > 3 || $nagioscommandexit < 0) { $nagiosexitorder = 2; }
					if ($nagioscommandexit == 2) { $nagiosexitorder = 3; }
					
					$nagiosstatusicon = 'Normal';
					if ($nagioscommandexit == 0) { $nagiosstatusicon = 'Normal'; }
					if ($nagioscommandexit == 3) { $nagiosstatusicon = 'Unknown'; }
					if ($nagioscommandexit == 1 || $nagioscommandexit > 3 || $nagioscommandexit < 0) { $nagiosstatusicon = 'Warning'; }
					if ($nagioscommandexit == 2) { $nagiosstatusicon = 'Critical'; }
					
					if ($orderby == 'NagiosCommandName') { $nagiosarray[$nagioscounter][0] = $nagioscommandname; }
					if ($orderby == 'NagiosCommandMsgs') { $nagiosarray[$nagioscounter][0] = $nagioscommandmsgs; }
					if ($orderby == 'NagiosCommandExit') { $nagiosarray[$nagioscounter][0] = $nagiosexitorder; }
					$nagiosarray[$nagioscounter][1] = $nagioscommandname;
					$nagiosarray[$nagioscounter][2] = $nagioscommandstrn;
					$nagiosarray[$nagioscounter][3] = $nagioscommandmsgs;
					$nagiosarray[$nagioscounter][4] = $nagioscommandexit;
					$nagiosarray[$nagioscounter][5] = '<img src="/img/nagios_' . $nagiosstatusicon . '.png" width="10" height="13" style="vertical-align: middle;" title="' . $nagiosstatusicon . "\nReturn Code: " . $nagioscommandexit . '" />';
					$nagiosarray[$nagioscounter][6] = $nagiosstatusicon . "\nReturn Code: " . $nagioscommandexit;
					$nagiosarray[$nagioscounter][7] = 'Raw Data View:' . "\n\n" . '<NagiosCommandName>' . $nagioscommandname . '</NagiosCommandName>' . "\n" . '<NagiosCommandStrn>' . $nagioscommandstrn . '</NagiosCommandStrn>' . "\n" . '<NagiosCommandMsgs>' . $nagioscommandmsgs . '</NagiosCommandMsgs>' . "\n" . '<NagiosCommandExit>' . $nagioscommandexit . '</NagiosCommandExit>';
					
					$nagioscounter = $nagioscounter + 1;
				}
				
			}
		}
	}

}


if ($orderby == 'NagiosCommandName' || $orderby == 'NagiosCommandMsgs') {
	sort($nagiosarray);
} else {
	rsort($nagiosarray);
}

$nagiospagearray = array();
if ($_SESSION['csv_nagios'] == 'csv_nagios') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Nagios";' . "\n" . '"eurysco CSV Source Node: ' . $envcomputername . '";' . "\n" . '"eurysco CSV Filter: ' . $filter . '";' . "\n" . '"eurysco CSV Total Raws: ' . $nagioscounter . '";' . "\n\n");
	array_push($tmpcsvexport, '"Name";' . '"Command";' . '"Message";' . '"Return Code";' . "\n\n");
}
foreach ($nagiosarray as $nagiosrow) {
	array_push($nagiospagearray, '<tr class="rowselect" title="' . htmlentities($nagiosrow[7]) . '"><td style="font-size:12px;" align="center">' . $nagiosrow[5] . '</td><td style="font-size:12px;" align="center" title="' . htmlentities($nagiosrow[2]) . '">' . htmlentities($nagiosrow[1]) . '</td><td style="font-size:12px;">' . htmlentities($nagiosrow[3]) . '</td></tr>');
	if ($_SESSION['csv_nagios'] == 'csv_nagios') { array_push($tmpcsvexport, '"' . trim($nagiosrow[1]) . '";' . '"' . $nagiosrow[2] . '";' . '"' . $nagiosrow[3] . '";' . '"' . $nagiosrow[4] . '";' . "\n"); }
}

if ($_SESSION['csv_nagios'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/nagios.php?csv_nagios&orderby=' . $orderby . '&filter=' . urlencode($filter) . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_nagios'] == 'csv_nagios') {
	$_SESSION['csv_nagios'] = $tmpcsvexport;
}
if ($_SESSION['csv_nagios'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_nagios&source=' . $envcomputername . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

$nagiospages = array_chunk($nagiospagearray, 100);

if ($pgkey > count($nagiospages) - 1) { $pgkey = count($nagiospages) - 1; }

if (count($nagiospages) > 0) {
	foreach($nagiospages[$pgkey] as $nagios) {
		$nagiostable = $nagiostable . $nagios;
	}
}

if ($nagioscounter == 0) {
	$nagiostable = $nagiostable . '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="3">No Results...</td></tr>';
}

$nagiostable = $nagiostable . '</table>';

$nagiospaging = '';
if (count($nagiospages) > 1) {
	if ($pgkey > 5) {
		$nagiospaging = $nagiospaging . '<a href="?page=1&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($nagiospages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$nagiospaging = $nagiospaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($nagiospages) > $pgkey + 6) {
		$nagiospaging = $nagiospaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($nagiospages) . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($nagiospages) . '</span></a>';
	}
	$nagiostable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $nagiospaging . '</blockquote><br />' . $nagiostable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $nagiospaging . '</blockquote>';
}

$totalelement = count($nagiosarray);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('nagiostable'=>utf8_encode($nagiostable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'lastupdate'=>$lastupdate,'csvexport'=>$csvexport));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>