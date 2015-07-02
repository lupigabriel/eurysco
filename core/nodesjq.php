<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/nodes.php')) { exit; }

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
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesstatus'] > 0) {  } else { exit; }

$deploysettingspath = str_replace('\\core', '\\server\\settings\\' . md5($_SESSION['username']) . '.xml', $_SERVER['DOCUMENT_ROOT']);
if (file_exists($deploysettingspath)) {
	$deploysettings = '<blockquote style="font-size:12px; background-color:#603CBA; color:#FFFFFF; border-left-color:#482E8C;"><strong>New Settings</strong> changed <strong>' . date ('d/m/Y H:i:s', filemtime($deploysettingspath)) . '</strong> are ready to deploy on the following nodes&nbsp;&nbsp;<a href=\'javascript:confirmdeploy("' . date ('d/m/Y H:i:s', filemtime($deploysettingspath)) . '");\' style="font-size:12px; color:#FFFFFF;" title="Confirm Deploy"><div class="icon-reply-2"></div></a>&nbsp;<a href=\'javascript:removedeploy("' . date ('d/m/Y H:i:s', filemtime($deploysettingspath)) . '");\' style="font-size:12px; color:#FFFFFF;" title="Cancel Deploy"><div class="icon-cancel"></div></a></blockquote><br />';
} else {
	$deploysettings = '';
}

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = '';
}

if (isset($_GET['find'])) {
	$find = $_GET['find'];
} else {
	$find = '';
}

if ($find != '') {
	if ($orderby == '') {
		$orderby = 'computername';
	}
} else {
	if ($orderby == '') {
		$orderby = 'cpuusage';
	}
}

if (!isset($_SESSION['csv_nodes'])) {
	$_SESSION['csv_nodes'] = '';
}

$db = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoServer');
$db->busyTimeout(30000);
$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');

$dbaudit = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoAudit');
$dbaudit->busyTimeout(30000);
$dbaudit->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');

if (isset($_GET['findtype'])) {
	$findtype = $_GET['findtype'];
} else {
	$findtype = '';
}

$sessionfindincr = 25;
if ($findtype == 'status') { $sessionfindincr = 250; }
if ($findtype == 'events') { $sessionfindincr = 25; }
if ($findtype == 'inventory') { $sessionfindincr = 150; }
if ($findtype == 'nagios') { $sessionfindincr = 150; }
if ($findtype == 'netstat') { $sessionfindincr = 100; }
if ($findtype == 'processes') { $sessionfindincr = 75; }
if ($findtype == 'programs') { $sessionfindincr = 75; }
if ($findtype == 'scheduler') { $sessionfindincr = 75; }
if ($findtype == 'services') { $sessionfindincr = 75; }

if (!isset($_SESSION['sessionfindres'])) {
	$_SESSION['sessionfindres'] = '';
}
if (!isset($_SESSION['sessionfindresnodes'])) {
	$_SESSION['sessionfindresnodes'] = '';
}

if (isset($_GET['sessionfind'])) {
	$sessionfind = $_GET['sessionfind'];
} else {
	$sessionfind = '';
}

if (!isset($_SESSION['resultsarr'])) {
	$_SESSION['resultsarr'] = array();
}

if (!isset($_SESSION['nodelist'])) {
	$_SESSION['nodelist'] = '';
}

if (isset($_SESSION['sessionfindlast'])) {
	if ($_SESSION['sessionfindlast'] != $sessionfind) {
		$_SESSION['sessionfindlast'] = $sessionfind;
		$_SESSION['sessionfindoffs'] = 0;
		$_SESSION['sessionfindtotn'] = 0;
		$_SESSION['sessionfindincr'] = $sessionfindincr;
		$_SESSION['sessionfindresu'] = '';
		$_SESSION['nodearray'] = array();
		$_SESSION['nodecounter'] = 0;
		$_SESSION['sessionfindres'] = '';
		$_SESSION['sessionfindresnodes'] = '';
		$_SESSION['resultsarr'] = array();
		$_SESSION['nodelist'] = '';
	}
} else {
	$_SESSION['sessionfindlast'] = $sessionfind;
	$_SESSION['sessionfindoffs'] = 0;
	$_SESSION['sessionfindtotn'] = 0;
	$_SESSION['sessionfindincr'] = $sessionfindincr;
	$_SESSION['sessionfindresu'] = '';
	$_SESSION['nodearray'] = array();
	$_SESSION['nodecounter'] = 0;
	$_SESSION['nodelist'] = '';
}

if ($find != '' && !strpos($findtype, '_metering') && $_SESSION['sessionfindres'] == '') {
	$findsql = ' JOIN xml' . ucfirst($findtype) . ' USING (node) LIMIT ' . $_SESSION['sessionfindincr'] . ' OFFSET ' . $_SESSION['sessionfindoffs'];
	$_SESSION['sessionfindtotn'] = $db->querySingle('SELECT COUNT(node) FROM nodesStatus');
	if ($_SESSION['sessionfindtotn'] > 0) { $filterprogressn = round((($_SESSION['sessionfindoffs'] + $_SESSION['sessionfindincr']) * 100 / $_SESSION['sessionfindtotn'])); if ($filterprogressn > 100) { $filterprogressn = 100; } } else { $filterprogressn = 0; }
	if ($filterprogressn > 0) { $filterprogress = '<div class="progress-bar"><div class="bar bg-color-blue" style="width: ' . $filterprogressn . '%" title="Filter Progress ' . $filterprogressn . '%"></div></div>'; } else { $filterprogress = ''; }	
} else {
	$findsql = '';
	$filterprogress = '';
	$_SESSION['nodearray'] = array();
	$_SESSION['nodecounter'] = 0;
	$_SESSION['nodelist'] = '';
}

if (isset($_GET['page'])) {
	$pgkey = $_GET['page'];
} else {
	$pgkey = 0;
}

if (isset($_GET['results'])) {
	$results = $_GET['results'];
} else {
	$results = 0;
}

if (isset($_GET['confirmdeploy'])) {
	if (file_exists($deploysettingspath)) {
		$confirmdeploy = $_GET['confirmdeploy'];
	} else {
		$confirmdeploy = '';
	}
} else {
	$confirmdeploy = '';
}

if (!isset($_SESSION['cmdnodes'])) {
	$_SESSION['cmdnodes'] = '';
}

if (!isset($_SESSION['cmdnodescurr'])) {
	$_SESSION['cmdnodescurr'] = '';
}
					
if (!isset($_SESSION['cmdnodeslast'])) {
	$_SESSION['cmdnodeslast'] = '';
}

if (!isset($_SESSION['cmdfilter'])) {
	$_SESSION['cmdfilter'] = '';
}
					
$cmdnodeslist = '';
$message = '';

$lastcomtimeout = 150;

if ($_SESSION['cmdnodescurr'] != $_SESSION['cmdnodeslast']) {
	$exectimeout = 600000;
	$lastcomtimeout = $exectimeout + 90000;
	$_SESSION['cmdnodescid'] = md5(date('r') . $_SESSION['username'] . $_SESSION['cmdnodes']);
	$cmdexecoutput = $_SESSION['cmdnodes'];
}

$cmdnodesex = 0;
$cmdnodesok = 0;
$cmdnodesko = 0;
$cmdnodeslex = '';
$cmdnodeslok = '';
$cmdnodeslko = '';
if ($_SESSION['cmdnodes'] != '' && $_SESSION['cmdnodescid'] != '') {
	$cmdnodesarray = array();
	$auditlogs = $dbaudit->query('SELECT node, exitcode FROM auditLog WHERE cid = "' . $_SESSION['cmdnodescid'] . '"');
	while ($auditlog = $auditlogs->fetchArray()) {
		$cmdnodesex = $cmdnodesex + 1;
		$cmdnodeslex = $cmdnodeslex . '#' . $auditlog['node'] . '#';
		$cmdnodesarray[$auditlog['node']] = $auditlog['exitcode'];
		if ($auditlog['exitcode'] == 0) {
			$cmdnodesok = $cmdnodesok + 1;
			$cmdnodeslok = $cmdnodeslok . '#' . $auditlog['node'] . '#';
		}
		if ($auditlog['exitcode'] > 0 || $auditlog['exitcode'] < 0) {
			$cmdnodesko = $cmdnodesko + 1;
			$cmdnodeslko = $cmdnodeslko . '#' . $auditlog['node'] . '#';
		}
	}
}
$cmdnodesex = ':&nbsp;' . $cmdnodesex;
$cmdnodesok = ':&nbsp;' . $cmdnodesok;
$cmdnodesko = ':&nbsp;' . $cmdnodesko;
$cmdfilter = $cmdnodeslex;
if ($_SESSION['cmdfilter'] == 'all') { $cmdfilter_all = ' selected="selected"'; $cmdfilter = $cmdnodeslex; } else { $cmdfilter_all = ''; }
if ($_SESSION['cmdfilter'] == 'success') { $cmdfilter_success = ' selected="selected"'; $cmdfilter = $cmdnodeslok; } else { $cmdfilter_success = ''; }
if ($_SESSION['cmdfilter'] == 'error') { $cmdfilter_error = ' selected="selected"'; $cmdfilter = $cmdnodeslko; } else { $cmdfilter_error = ''; }
$cmdresult = '<form id="cmdfilterform" name="cmdfilterform" method="get"><select id="cmdfilter" name="cmdfilter" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border:0px; background-color:transparent;" onchange="this.form.submit();"><option value="all"' . $cmdfilter_all . '>&nbsp;All&nbsp;Nodes' . $cmdnodesex . '&nbsp;&nbsp;</option><option value="success"' . $cmdfilter_success . '>&nbsp;Success' . $cmdnodesok . '&nbsp;&nbsp;</option><option value="error"' . $cmdfilter_error . '>&nbsp;Error' . $cmdnodesko . '&nbsp;&nbsp;</option></select>&nbsp;&nbsp;&nbsp;<i>' . $_SESSION['cmdnodes'] . '</i>&nbsp;&nbsp;<a href="?orderby=' . $orderby . '&cmdnodesclear" style="font-size:12px;" title="Clear Command"><div class="icon-cancel"></div></a><input type="hidden" id="orderby" name="orderby" value="' . $orderby . '" /><input type="hidden" id="find" name="find" value="' . $find . '" /><input type="hidden" id="findtype" name="findtype" value="' . $findtype . '" /></form>';

$checkclearnode = 0;
$checkfindincr = 0;

if (($_SESSION['sessionfindoffs'] == 0 && $_SESSION['sessionfindtotn'] == 0) || $_SESSION['sessionfindoffs'] < $_SESSION['sessionfindtotn'] || $_SESSION['sessionfindres'] == 'finish') {

	$checkfindincr = 1;
	
	$allnodes = $db->query('SELECT * FROM nodesStatus' . $findsql);

	while ($noderow = $allnodes->fetchArray()) {
		$pathname = str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $noderow['node'];
		$datarow = strtolower('<agentversion>' . $noderow['agentversion'] . '</agentversion><refreshrate>' . $noderow['refreshrate'] . '</refreshrate><computername>' . $noderow['computername'] . '</computername><coreport>' . $noderow['coreport'] . '</coreport><executorport>' . $noderow['executorport'] . '</executorport><lastcom>' . $noderow['lastcom'] . '</lastcom><computerip>' . $noderow['computerip'] . '</computerip><cpuusage>' . $noderow['cpuusage'] . '</cpuusage><cpumanufacturer>' . $noderow['cpumanufacturer'] . '</cpumanufacturer><cpumodel>' . $noderow['cpumodel'] . '</cpumodel><cpucurrentclock>' . $noderow['cpucurrentclock'] . '</cpucurrentclock><cpumaxclock>' . $noderow['cpumaxclock'] . '</cpumaxclock><cpuarchitecture>' . $noderow['cpuarchitecture'] . '</cpuarchitecture><cpucores>' . $noderow['cpucores'] . '</cpucores><cputhreads>' . $noderow['cputhreads'] . '</cputhreads><cpusockettype>' . $noderow['cpusockettype'] . '</cpusockettype><osname>' . $noderow['osname'] . '</osname><osversion>' . $noderow['osversion'] . '</osversion><osservicepack>' . $noderow['osservicepack'] . '</osservicepack><osserialnumber>' . $noderow['osserialnumber'] . '</osserialnumber><manufacturer>' . $noderow['manufacturer'] . '</manufacturer><model>' . $noderow['model'] . '</model><domain>' . $noderow['domain'] . '</domain><totalprocesses>' . $noderow['totalprocesses'] . '</totalprocesses><localdatetime>' . $noderow['localdatetime'] . '</localdatetime><lastbootuptime>' . $noderow['lastbootuptime'] . '</lastbootuptime><uptime>' . $noderow['uptime'] . '</uptime><memoryusage>' . $noderow['memoryusage'] . '</memoryusage><totalmemory>' . $noderow['totalmemory'] . '</totalmemory><usedmemory>' . $noderow['usedmemory'] . '</usedmemory><freememory>' . $noderow['freememory'] . '</freememory><sysdiskuspc>' . $noderow['sysdiskuspc'] . '</sysdiskuspc><sysdiskfree>' . $noderow['sysdiskfree'] . '</sysdiskfree><sysdisksize>' . $noderow['sysdisksize'] . '</sysdisksize><sysdiskused>' . $noderow['sysdiskused'] . '</sysdiskused><sysdisktype>' . $noderow['sysdisktype'] . '</sysdisktype><services_total>' . $noderow['services_total'] . '</services_total><services_running>' . $noderow['services_running'] . '</services_running><services_error>' . $noderow['services_error'] . '</services_error><scheduler_total>' . $noderow['scheduler_total'] . '</scheduler_total><scheduler_error>' . $noderow['scheduler_error'] . '</scheduler_error><events_warning>' . $noderow['events_warning'] . '</events_warning><events_error>' . $noderow['events_error'] . '</events_error><nagios_status>' . $noderow['nagios_status'] . '</nagios_status><nagiostotalcount>' . $noderow['nagiostotalcount'] . '</nagiostotalcount><nagiosnormacount>' . $noderow['nagiosnormacount'] . '</nagiosnormacount><nagioswarnicount>' . $noderow['nagioswarnicount'] . '</nagioswarnicount><nagioscriticount>' . $noderow['nagioscriticount'] . '</nagioscriticount><nagiosunknocount>' . $noderow['nagiosunknocount'] . '</nagiosunknocount><netstatestcount>' . $noderow['netstatestcount'] . '</netstatestcount><netstatliscount>' . $noderow['netstatliscount'] . '</netstatliscount><netstattimcount>' . $noderow['netstattimcount'] . '</netstattimcount><netstatclocount>' . $noderow['netstatclocount'] . '</netstatclocount><netstat_status>' . $noderow['netstat_status'] . '</netstat_status><inventory_status>' . $noderow['inventory_status'] . '</inventory_status><programs_status>' . $noderow['programs_status'] . '</programs_status>');
		$prefilter = $_SESSION['usersett']['nodesstatusf'];
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
		if ($checkprefilter == 0) {
			$_SESSION['nodelist'] = $_SESSION['nodelist'] . '#' . $noderow['computername'] . '#';
			$checkfind = 0;
			$resultsoutarr = '';
			if ($findtype != '' && !strpos($findtype, '_metering') && $_SESSION['sessionfindres'] == '') {
				$readft = strtolower(urldecode($noderow['xml']));
				if (substr($find, 0, 1) != '-') {
					if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($find))) . '/', strtolower($readft), $resultsoutarr, PREG_PATTERN_ORDER) || strpos(strtolower($readft), strtolower($find)) > -1) {
						$checkfind = 0;
						$resultsout = 'n/a';
						$resultsrcn = 0;
						foreach($resultsoutarr as $outarrrow => $resultsoutarrinner){
							$resultsrcn = $resultsrcn + count($resultsoutarrinner);
							foreach($resultsoutarrinner as $outarrrowinner => $resultsoutarrrow) {
								if ($resultsout == 'n/a') { $resultsout = ''; }
								$resultsout = $resultsout . preg_replace('/ \| \\n/', "\n", preg_replace('/\<.*/', '', preg_replace('/.*\>/', '', strip_tags(str_replace('</', ' | </', $resultsoutarrrow)))) . "\n");
							}
						}
						$_SESSION['resultsarr'][$noderow['node']][1] = $resultsrcn;
						$resultsout = trim(str_replace('	', '', $resultsout));
						if (strlen($resultsout) > 5000) { $_SESSION['resultsarr'][$noderow['node']][2] = substr($resultsout, 0, 5000) . '...' . "\n\n" . '...Results Limit'; } else { $_SESSION['resultsarr'][$noderow['node']][2] = $resultsout; }
					} else {
						$checkfind = 1;
					}
				} else {
					$notfind = substr($find, 1);
					if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfind))) . '/', strtolower($readft)) && !strpos(strtolower($readft), strtolower($notfind))) {
						$checkfind = 0;
					} else {
						$checkfind = 1;
					}
				}
			}
			if (strpos($findtype, '_metering') > 0 && !file_exists($pathname . '\\' . $findtype)) {
				$checkfind = 1;
			}
			if ($_SESSION['sessionfindres'] == 'finish') {
				if (!strpos('#' . $_SESSION['sessionfindresnodes'] . '#', '#' . $noderow['computername'] . '#')) {
					$checkfind = 1;
				}
			}
			if ($_SESSION['cmdnodescurr'] == $_SESSION['cmdnodeslast'] && $_SESSION['cmdnodes'] != '' && $_SESSION['cmdnodescid'] != '' && !strpos('#' . $cmdfilter . '#', '#' . $noderow['computername'] . '#')) {
				$checkfind = 1;
			}
		} else {
			$checkfind = 1;
		}
		if ($checkfind == 0) {
			if ($confirmdeploy == 'confirmdeploy') {
				if (file_exists($deploysettingspath)) {
					@copy($deploysettingspath, $pathname . '\\config_settings.xml');
				}
			}
			if ($find != '' && $findtype != '' && !strpos($findtype, '_metering') && $_SESSION['sessionfindres'] == '') { $_SESSION['sessionfindresnodes'] = $_SESSION['sessionfindresnodes'] . '#' . $noderow['computername'] . '#'; }
			if ($_SESSION['cmdnodescurr'] != $_SESSION['cmdnodeslast'] && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['nodesstatus'] > 2)) {
				$mcrykeycmd = pack('H*', hash('sha256', md5(strtolower($noderow['node']))));
				$xml = '<exec>' . "\n";
				$xml = $xml . '	<auditok>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $noderow['computername'] . '     nodes control     command "' . $cmdexecoutput . '" executed (command sent from server "' . $envcomputername . '")')))))) . '</auditok>' . "\n";
				$xml = $xml . '	<auditko>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $noderow['computername'] . '     nodes control     command "' . $cmdexecoutput . '" not executed (command sent from server "' . $envcomputername . '")')))))) . '</auditko>' . "\n";
				$xml = $xml . '	<auditnl>null</auditnl>' . "\n";
				$xml = $xml . '	<cid>' . $_SESSION['cmdnodescid'] . '</cid>' . "\n";
				$xml = $xml . '	<timeout>' . $exectimeout . '</timeout>' . "\n";
				$xml = $xml . '	<command>' . base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykeycmd, $cmdexecoutput, MCRYPT_MODE_CBC, $iv)) . '</command>' . "\n";
				$xml = $xml . '</exec>';
				$fp = fopen($pathname . '\\' . date('YmdHis') . md5($cmdexecoutput . 'nodes') . '.exec', 'w');
				fwrite($fp, $xml);
				fclose($fp);
				$fp = fopen($pathname . '\\' . 'exec.on', 'w');
				fclose($fp);
				$cmdnodeslist = $cmdnodeslist . $noderow['node'] . ' ';
			}
			if (isset($_SESSION['agentrefresh']) && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesstatus'] > 1)) {
				$mcrykeycmd = pack('H*', hash('sha256', md5(strtolower($noderow['node']))));
				$cid = '';
				$exectimeout = 10000;
				$agentrefreshexecoutput = 'sc.exe stop "euryscoAgent" & sc.exe start "euryscoAgent"';
				$xml = '<exec>' . "\n";
				$xml = $xml . '	<auditok>null</auditok>' . "\n";
				$xml = $xml . '	<auditko>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . strtolower($_SESSION['agentrefresh']) . '     nodes control     eurysco Agent Restart command not executed (command sent from server "' . $envcomputername . '")')))))) . '</auditko>' . "\n";
				$xml = $xml . '	<auditnl>null</auditnl>' . "\n";
				$xml = $xml . '	<cid>' . $cid . '</cid>' . "\n";
				$xml = $xml . '	<timeout>' . $exectimeout . '</timeout>' . "\n";
				$xml = $xml . '	<command>' . base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykeycmd, $agentrefreshexecoutput, MCRYPT_MODE_CBC, $iv)) . '</command>' . "\n";
				$xml = $xml . '</exec>';
				$fp = fopen($pathname . '\\' . date('YmdHis') . md5(strtolower($_SESSION['agentrefresh']) . 'agent') . '.exec', 'w');
				fwrite($fp, $xml);
				fclose($fp);
				$fp = fopen($pathname . '\\' . 'exec.on', 'w');
				fclose($fp);
			}
			if ((strtotime(date('Y-m-d H:i:s')) - strtotime($noderow['lastcom'])) < $lastcomtimeout) {
				if ($orderby == 'matches' || $orderby == 'results') {
					if ($orderby == 'matches') { $_SESSION['nodearray'][$_SESSION['nodecounter']][0] = $_SESSION['resultsarr'][$noderow['node']][1]; }
					if ($orderby == 'results') { $_SESSION['nodearray'][$_SESSION['nodecounter']][0] = $_SESSION['resultsarr'][$noderow['node']][2]; }
				} else {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][0] = str_replace('%', '', str_replace(':', '', str_replace('127.0.0.1', '0', $noderow[$orderby]))) . 0;
				}
				if ($orderby == 'services_running') { $_SESSION['nodearray'][$_SESSION['nodecounter']][0] = $_SESSION['nodearray'][$_SESSION['nodecounter']][0] + ($noderow['services_error'] * 10000); }
				if ($orderby == 'scheduler_total') { $_SESSION['nodearray'][$_SESSION['nodecounter']][0] = $_SESSION['nodearray'][$_SESSION['nodecounter']][0] + ($noderow['scheduler_error'] * 10000); }
				if ($orderby == 'events_error') { $_SESSION['nodearray'][$_SESSION['nodecounter']][0] = ($_SESSION['nodearray'][$_SESSION['nodecounter']][0] * 10000) + $noderow['events_warning']; }
				$_SESSION['nodearray'][$_SESSION['nodecounter']][1] = $noderow['computername'];
				if ($noderow['refreshrate'] < 120) {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][2] = '<div class="icon-link" style="font-size:12px; color:#0094FF;" title="Connected: Refresh every ' . $noderow['refreshrate'] . ' sec' . "\n" . 'eurysco Agent Version: ' . $noderow['agentversion'] . '"></div>';
				} else { 
					$_SESSION['nodearray'][$_SESSION['nodecounter']][2] = '<div class="icon-link" style="font-size:12px; color:#E56616;" title="Connected: Hold' . "\n" . 'eurysco Agent Version: ' . $noderow['agentversion'] . '"></div>';
				}
				if (preg_replace('/:.*/', '', $noderow['uptime']) > 23) {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][3] = (int)(preg_replace('/:.*/', '', $noderow['uptime']) / 24) . ' days';
				} else {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][3] = $noderow['uptime'];
				}
				$_SESSION['nodearray'][$_SESSION['nodecounter']][4] = $noderow['cpuusage'] . '%';
				$_SESSION['nodearray'][$_SESSION['nodecounter']][5] = $noderow['memoryusage'] . '%';
				$_SESSION['nodearray'][$_SESSION['nodecounter']][6] = '<span style="font-size:12px;" title="Total Processes: ' . $noderow['totalprocesses'] . '">' . $noderow['totalprocesses'] . '</span>';
				if ($noderow['totalprocesses'] > 0 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesprocesscontrol'] > 0)) {
					if ($findtype == 'processes' && $find != '') { $filterprocesses = '&filter=' . $find; } else { $filterprocesses = ''; }
					$_SESSION['nodearray'][$_SESSION['nodecounter']][6] = '<a href="/nodes_processes.php?node=' . $noderow['computername'] . '&domain=' . $noderow['domain'] . '&computerip=' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '&executorport=' . $noderow['executorport'] . $filterprocesses . '" style="font-size:12px;"><span style="font-size:12px;" title="Total Processes: ' . $noderow['totalprocesses'] . '">' . $noderow['totalprocesses'] . '</span></a>';
				} else {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][6] = '<div class="icon-none"></div>';
				}
				$_SESSION['nodearray'][$_SESSION['nodecounter']][7] = str_replace('127.0.0.1', 'localhost', $noderow['computerip']);
				$_SESSION['nodearray'][$_SESSION['nodecounter']][8] = $noderow['sysdiskuspc'] . '%';
				$_SESSION['nodearray'][$_SESSION['nodecounter']][9] = $noderow['domain'];
				$_SESSION['nodearray'][$_SESSION['nodecounter']][10] = substr($noderow['lastcom'], 8, 2) . '/' . substr($noderow['lastcom'], 5, 2) . '/' . substr($noderow['lastcom'], 0, 4) . ' ' . substr($noderow['lastcom'], 11, 2) . ':' . substr($noderow['lastcom'], 14, 2) . ':' . substr($noderow['lastcom'], 17, 2);
				$_SESSION['nodearray'][$_SESSION['nodecounter']][11] = $noderow['manufacturer'];
				$_SESSION['nodearray'][$_SESSION['nodecounter']][12] = $noderow['model'];
				$_SESSION['nodearray'][$_SESSION['nodecounter']][13] = $noderow['osname'];
				$_SESSION['nodearray'][$_SESSION['nodecounter']][14] = $noderow['osservicepack'];
				$_SESSION['nodearray'][$_SESSION['nodecounter']][15] = $noderow['cpumodel'];
				$_SESSION['nodearray'][$_SESSION['nodecounter']][16] = ' title="' . $noderow['cpumodel'] . "\n\n" . 'CPU Manufacturer: ' . $noderow['cpumanufacturer'] . "\n" . 'CPU Architecture: ' . $noderow['cpuarchitecture'] . ' Bit' . "\n" . 'CPU Cores: ' . $noderow['cpucores'] . "\n" . 'CPU Threads: ' . $noderow['cputhreads'] . "\n\n" . 'Max Clock: ' . $noderow['cpumaxclock'] . ' Mhz' . "\n" . 'Current Clock: ' . $noderow['cpucurrentclock'] . ' Mhz' . '"';
				$_SESSION['nodearray'][$_SESSION['nodecounter']][17] = ' title="' . 'Total Memory: ' . $noderow['totalmemory'] . ' MB' . "\n" . 'Used Memory: ' . $noderow['usedmemory'] . ' MB' . "\n" . 'Free Memory: ' . $noderow['freememory'] . ' MB' . '"';
				$_SESSION['nodearray'][$_SESSION['nodecounter']][18] = ' title="' . 'System Disk Size: ' . $noderow['sysdisksize'] . ' GB' . "\n" . 'System Disk Used: ' . $noderow['sysdiskused'] . ' GB' . "\n" . 'System Disk Free: ' . $noderow['sysdiskfree'] . ' GB' . '"';
				$_SESSION['nodearray'][$_SESSION['nodecounter']][19] = ' title="' . 'Domain: ' . $noderow['domain'] . "\n\n" . 'Manufacturer: ' . $noderow['manufacturer'] . "\n" . 'Model: ' . $noderow['model'] . "\n\n" . str_replace('  ', ' ', $noderow['osname'] . ' ' . str_replace('service pack ', '- SP', strtolower($noderow['osservicepack']))) . "\n" . $noderow['osserialnumber'] . "\n" . 'OS Version: ' . $noderow['osversion'] . '"';
				$_SESSION['nodearray'][$_SESSION['nodecounter']][20] = ' title="' . 'Last BootUP Time: ' . $noderow['lastbootuptime'] . "\n" . 'Local DateTime: ' . $noderow['localdatetime'] . '"';
				if ($noderow['services_total'] > 0 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesservicecontrol'] > 0)) {
					if ($findtype == 'services' && $find != '') { $filterservices = '&filter=' . $find; } else { $filterservices = ''; }
					if ($noderow['services_error'] == 0) {
						$_SESSION['nodearray'][$_SESSION['nodecounter']][21] = '<a href="/nodes_services.php?node=' . $noderow['computername'] . '&domain=' . $noderow['domain'] . '&computerip=' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '&executorport=' . $noderow['executorport'] . $filterservices . '" style="font-size:12px;"><span style="font-size:12px;" title="Total Services: ' . $noderow['services_total'] . "\n" . 'Running Services: ' . $noderow['services_running'] . "\n" . 'Error Services: ' . $noderow['services_error'] . '">' . $noderow['services_running'] . '</span></a>';
					} else {
						$_SESSION['nodearray'][$_SESSION['nodecounter']][21] = '<a href="/nodes_services.php?node=' . $noderow['computername'] . '&domain=' . $noderow['domain'] . '&computerip=' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '&executorport=' . $noderow['executorport'] . $filterservices . '" style="font-size:12px; color:#933000;"><span style="font-size:12px; color:#933000;" title="Total Services: ' . $noderow['services_total'] . "\n" . 'Total Services in Running: ' . $noderow['services_running'] . "\n" . 'Total Services in Error: ' . $noderow['services_error'] . '">' . $noderow['services_running'] . '</span></a>';
					}
				} else {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][21] = '<div class="icon-none"></div>';
				}
				if ($noderow['scheduler_total'] > 0 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesscheduledtasks'] > 0)) {
					if ($findtype == 'scheduler' && $find != '') { $filterscheduled = '&filter=' . $find; } else { $filterscheduled = ''; }
					if ($noderow['scheduler_error'] == 0) {
						$_SESSION['nodearray'][$_SESSION['nodecounter']][22] = '<a href="/nodes_scheduler.php?node=' . $noderow['computername'] . '&domain=' . $noderow['domain'] . '&computerip=' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '&executorport=' . $noderow['executorport'] . $filterscheduled . '" style="font-size:12px;"><span title="Total Scheduled Tasks: ' . $noderow['scheduler_total'] . "\n" . 'Error Scheduled Tasks: ' . $noderow['scheduler_error'] . '">' . $noderow['scheduler_total'] . '</span></a>';
					} else {
						$_SESSION['nodearray'][$_SESSION['nodecounter']][22] = '<a href="/nodes_scheduler.php?node=' . $noderow['computername'] . '&domain=' . $noderow['domain'] . '&computerip=' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '&executorport=' . $noderow['executorport'] . $filterscheduled . '" style="font-size:12px; color:#933000;"><span title="Total Scheduled Tasks: ' . $noderow['scheduler_total'] . "\n" . 'Error Scheduled Tasks: ' . $noderow['scheduler_error'] . '">' . $noderow['scheduler_total'] . '</span></a>';
					}
				} else {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][22] = '<div class="icon-none"></div>';
				}
				if (($noderow['events_error'] > 0 || $noderow['events_warning'] > 0) && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodeseventviewer'] > 0)) {
					if ($findtype == 'events' && $find != '') { $filterevents = '&filter=' . $find; } else { $filterevents = ''; }
					if ($noderow['events_error'] == 0) {
						$_SESSION['nodearray'][$_SESSION['nodecounter']][23] = '<a href="/nodes_eventviewer.php?node=' . $noderow['computername'] . '&domain=' . $noderow['domain'] . '&computerip=' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '&executorport=' . $noderow['executorport'] . $filterevents . '" style="font-size:12px;"><span style="font-size:12px;" title="Error Events: ' . $noderow['events_error'] . "\n" . 'Warning Events: ' . $noderow['events_warning'] . '">' . $noderow['events_warning'] . '</span></a>';
					} else {
						$_SESSION['nodearray'][$_SESSION['nodecounter']][23] = '<a href="/nodes_eventviewer.php?node=' . $noderow['computername'] . '&domain=' . $noderow['domain'] . '&computerip=' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '&executorport=' . $noderow['executorport'] . $filterevents . '" style="font-size:12px; color:#933000;"><span style="font-size:12px; color:#933000;" title="Error Events: ' . $noderow['events_error'] . "\n" . 'Warning Events: ' . $noderow['events_warning'] . '">' . $noderow['events_error'] . '</span></a>';
					}
				} else {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][23] = '<div class="icon-none"></div>';
				}
				if ($noderow['inventory_status'] > 0) { $_SESSION['nodearray'][$_SESSION['nodecounter']][24] = 'Inventory'; } else { $_SESSION['nodearray'][$_SESSION['nodecounter']][24] = ''; }
				if ($noderow['programs_status'] > 0) { $_SESSION['nodearray'][$_SESSION['nodecounter']][25] = 'Programs'; } else { $_SESSION['nodearray'][$_SESSION['nodecounter']][25] = ''; }
				$_SESSION['nodearray'][$_SESSION['nodecounter']][26] = $noderow['coreport'];
				$_SESSION['nodearray'][$_SESSION['nodecounter']][27] = $noderow['executorport'];
				if ($findtype == 'programs' && $find != '') { $_SESSION['nodearray'][$_SESSION['nodecounter']][28] = '&filter=' . $find; } else { $_SESSION['nodearray'][$_SESSION['nodecounter']][28] = ''; }
				$nagiosstatustitle = 'Nagios Checks: ' . $noderow['nagiostotalcount'] . "\n\n" . 'Nagios Normal: ' . $noderow['nagiosnormacount'] . "\n" . 'Nagios Warning: ' . $noderow['nagioswarnicount'] . "\n" . 'Nagios Critical: ' . $noderow['nagioscriticount'] . "\n" . 'Nagios Unknown: ' . $noderow['nagiosunknocount'];
				$nagiosstatusicon = 'normal';
				if ($noderow['nagiosnormacount'] > 0) { $nagiosstatusicon = 'normal'; }
				if ($noderow['nagiosunknocount'] > 0) { $nagiosstatusicon = 'unknown'; }
				if ($noderow['nagioswarnicount'] > 0) { $nagiosstatusicon = 'warning'; }
				if ($noderow['nagioscriticount'] > 0) { $nagiosstatusicon = 'critical'; }
				if ($findtype == 'nagios' && $find != '') { $filternagios = '&filter=' . $find; } else { $filternagios = ''; }
				if ($noderow['nagios_status'] > -1 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnagiosstatus'] > 0)) { $_SESSION['nodearray'][$_SESSION['nodecounter']][29] = '<a href="/nodes_nagios.php?node=' . $noderow['computername'] . '&domain=' . $noderow['domain'] . '&computerip=' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '&executorport=' . $noderow['executorport'] . $filternagios . '" style="font-size:12px;"><img src="/img/nagios_' . $nagiosstatusicon . '.png" width="10" height="13" style="vertical-align: middle; margin-bottom: 2px;" title="' . $nagiosstatustitle . '" /></a>&nbsp;&nbsp;&nbsp;'; } else { $_SESSION['nodearray'][$_SESSION['nodecounter']][29] = ''; }
				$netstatstatustitle = 'Netstat Established: ' . $noderow['netstatestcount'] . "\n" . 'Netstat Listening: ' . $noderow['netstatliscount'] . "\n" . 'Netstat Time Wait: ' . $noderow['netstattimcount'] . "\n" . 'Netstat Close Wait: ' . $noderow['netstatclocount'];
				if ($findtype == 'netstat' && $find != '') { $filternetstat = '&filter=' . $find; } else { $filternetstat = ''; }
				if ($noderow['netstat_status'] > 0 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnetworkstats'] > 0)) { $_SESSION['nodearray'][$_SESSION['nodecounter']][30] = '<a href="/nodes_netstat.php?node=' . $noderow['computername'] . '&domain=' . $noderow['domain'] . '&computerip=' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '&executorport=' . $noderow['executorport'] . $filternetstat . '" style="font-size:12px;" title="' . $netstatstatustitle . '">' . str_replace('127.0.0.1', 'localhost', $noderow['computerip']) . '</a>'; } else { $_SESSION['nodearray'][$_SESSION['nodecounter']][30] = str_replace('127.0.0.1', 'localhost', $noderow['computerip']); }
				$_SESSION['nodearray'][$_SESSION['nodecounter']][31] = $noderow['agentversion'];
				$_SESSION['nodearray'][$_SESSION['nodecounter']][32] = 'Connected';
				$_SESSION['nodearray'][$_SESSION['nodecounter']][33] = '';
				if (strpos('#' . $cmdnodeslok . '#', '#' . $noderow['computername'] . '#')) {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][33] = '<div class="icon-console" style="font-size:14px; vertical-align: middle; color:#2E92CF;" title="Command Exit Code: ' . $cmdnodesarray[$noderow['node']] . '"></div>&nbsp;&nbsp;';
				}
				if (strpos('#' . $cmdnodeslko . '#', '#' . $noderow['computername'] . '#')) {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][33] = '<div class="icon-console" style="font-size:14px; vertical-align: middle; color:#993300;" title="Command Exit Code: ' . str_replace('-1', 'Timeout', $cmdnodesarray[$noderow['node']]) . '"></div>&nbsp;&nbsp;';
				}
				if ($find != '' && $findtype != '' && !strpos($findtype, '_metering') && $filterprogress == 0) {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][34] = $_SESSION['resultsarr'][$noderow['node']][1];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][35] = $_SESSION['resultsarr'][$noderow['node']][2];
				} else {
					$_SESSION['nodearray'][$_SESSION['nodecounter']][34] = 0;
					$_SESSION['nodearray'][$_SESSION['nodecounter']][35] = 'n/a';
				}
				$_SESSION['nodecounter'] = $_SESSION['nodecounter'] + 1;
			} else {
				$checkclearnode = 0;
				if ($nodesclearsetting != 'Never') {
					if ((strtotime(date('Y-m-d H:i:s')) - strtotime($noderow['lastcom'])) > $nodesclearsetting) {
						$checkclearnode = 1;
					}
				}
				if ($checkclearnode == 1) {
					$clearnodedir = scandir($pathname);
					foreach ($clearnodedir as $clearnode) {
						if ($clearnode != '.' && $clearnode != '..') {
							if (file_exists($pathname . '\\' . $clearnode)) {
								@unlink($pathname . '\\' . $clearnode);
							}
						}
					}
					@rmdir($pathname);
					$db->querySingle('DELETE FROM nodesHash WHERE node = "' . $noderow['node'] . '"; DELETE FROM nodesStatus WHERE node = "' . $noderow['node'] . '"; DELETE FROM xmlEvents WHERE node = "' . $noderow['node'] . '"; DELETE FROM xmlInventory WHERE node = "' . $noderow['node'] . '"; DELETE FROM xmlNagios WHERE node = "' . $noderow['node'] . '"; DELETE FROM xmlNetstat WHERE node = "' . $noderow['node'] . '"; DELETE FROM xmlProcesses WHERE node = "' . $noderow['node'] . '"; DELETE FROM xmlPrograms WHERE node = "' . $noderow['node'] . '"; DELETE FROM xmlScheduler WHERE node = "' . $noderow['node'] . '"; DELETE FROM xmlStatus WHERE node = "' . $noderow['node'] . '"');
				} else {
					if ($orderby == 'computerip' || $orderby == 'computername' || $orderby == 'matches' || $orderby == 'results') {
						if ($orderby == 'matches' || $orderby == 'results') {
							if ($orderby == 'matches') { $_SESSION['nodearray'][$_SESSION['nodecounter']][0] = $_SESSION['resultsarr'][$noderow['node']][1]; }
							if ($orderby == 'results') { $_SESSION['nodearray'][$_SESSION['nodecounter']][0] = $_SESSION['resultsarr'][$noderow['node']][2]; }
						} else {
							$_SESSION['nodearray'][$_SESSION['nodecounter']][0] = str_replace('%', '', str_replace(':', '', str_replace('127.0.0.1', '0', $noderow[$orderby]))) . 0;
						}
					} else {
						$_SESSION['nodearray'][$_SESSION['nodecounter']][0] = -1;
					}
					$_SESSION['nodearray'][$_SESSION['nodecounter']][1] = $noderow['computername'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][2] = '<div class="icon-link-2" style="font-size:12px; color:#933000;" title="Last Connection: ' . substr($noderow['lastcom'], 8, 2) . '/' . substr($noderow['lastcom'], 5, 2) . '/' . substr($noderow['lastcom'], 0, 4) . ' ' . substr($noderow['lastcom'], 11, 2) . ':' . substr($noderow['lastcom'], 14, 2) . ':' . substr($noderow['lastcom'], 17, 2) . "\n" . 'eurysco Agent Version: ' . $noderow['agentversion'] . '"></div>';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][3] = '<div class="icon-none"></div>';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][4] = '<div class="icon-none"></div>';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][5] = '<div class="icon-none"></div>';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][6] = '<div class="icon-none"></div>';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][7] = str_replace('127.0.0.1', 'localhost', $noderow['computerip']);
					$_SESSION['nodearray'][$_SESSION['nodecounter']][8] = '<div class="icon-none"></div>';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][9] = $noderow['domain'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][10] = substr($noderow['lastcom'], 8, 2) . '/' . substr($noderow['lastcom'], 5, 2) . '/' . substr($noderow['lastcom'], 0, 4) . ' ' . substr($noderow['lastcom'], 11, 2) . ':' . substr($noderow['lastcom'], 14, 2) . ':' . substr($noderow['lastcom'], 17, 2);
					$_SESSION['nodearray'][$_SESSION['nodecounter']][11] = $noderow['manufacturer'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][12] = $noderow['model'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][13] = $noderow['osname'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][14] = $noderow['osservicepack'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][15] = $noderow['cpumodel'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][16] = ' title="' . $noderow['cpumodel'] . "\n\n" . 'CPU Manufacturer: ' . $noderow['cpumanufacturer'] . "\n" . 'CPU Architecture: ' . $noderow['cpuarchitecture'] . ' Bit' . "\n" . 'CPU Cores: ' . $noderow['cpucores'] . "\n" . 'CPU Threads: ' . $noderow['cputhreads'] . '"';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][17] = ' title="' . 'Total Memory: ' . $noderow['totalmemory'] . ' MB' . '"';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][18] = ' title="' . 'System Disk Size: ' . $noderow['sysdisksize'] . ' GB' . '"';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][19] = ' title="' . 'Domain: ' . $noderow['domain'] . "\n\n" . 'Manufacturer: ' . $noderow['manufacturer'] . "\n" . 'Model: ' . $noderow['model'] . "\n\n" . str_replace('  ', ' ', $noderow['osname'] . ' ' . str_replace('service pack ', '- SP', strtolower($noderow['osservicepack']))) . "\n" . $noderow['osserialnumber'] . "\n" . 'OS Version: ' . $noderow['osversion'] . '"';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][20] = '';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][21] = '<div class="icon-none"></div>';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][22] = '<div class="icon-none"></div>';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][23] = '<div class="icon-none"></div>';
					if ($noderow['inventory_status'] > 0) { $_SESSION['nodearray'][$_SESSION['nodecounter']][24] = 'Inventory'; } else { $_SESSION['nodearray'][$_SESSION['nodecounter']][24] = ''; }
					if ($noderow['programs_status'] > 0) { $_SESSION['nodearray'][$_SESSION['nodecounter']][25] = 'Programs'; } else { $_SESSION['nodearray'][$_SESSION['nodecounter']][25] = ''; }
					$_SESSION['nodearray'][$_SESSION['nodecounter']][26] = $noderow['coreport'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][27] = $noderow['executorport'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][28] = '';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][29] = '';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][30] = str_replace('127.0.0.1', 'localhost', $noderow['computerip']);
					$_SESSION['nodearray'][$_SESSION['nodecounter']][31] = $noderow['agentversion'];
					$_SESSION['nodearray'][$_SESSION['nodecounter']][32] = 'Disconnected';
					$_SESSION['nodearray'][$_SESSION['nodecounter']][33] = '';
					if ($find != '' && $findtype != '' && !strpos($findtype, '_metering') && $filterprogress == 0) {
						$_SESSION['nodearray'][$_SESSION['nodecounter']][34] = $_SESSION['resultsarr'][$noderow['node']][1];
						$_SESSION['nodearray'][$_SESSION['nodecounter']][35] = $_SESSION['resultsarr'][$noderow['node']][2];
					} else {
						$_SESSION['nodearray'][$_SESSION['nodecounter']][34] = 0;
						$_SESSION['nodearray'][$_SESSION['nodecounter']][35] = 'n/a';
					}
					$_SESSION['nodecounter'] = $_SESSION['nodecounter'] + 1;
				}
			}
		}
		if ($find != '') {
			if ($_SESSION['sessionfindoffs'] < $_SESSION['sessionfindtotn']) {
				$_SESSION['sessionfindoffs'] = $_SESSION['sessionfindoffs'] + 1;
			}
		}
	}

}

if (($_SESSION['sessionfindoffs'] > 0 || $_SESSION['sessionfindtotn'] > 0) && $_SESSION['sessionfindoffs'] >= $_SESSION['sessionfindtotn'] && $_SESSION['sessionfindres'] == '') {
	$_SESSION['sessionfindres'] = 'finish';
}

if (isset($_SESSION['agentrefresh'])) {
	unset($_SESSION['agentrefresh']);
}

$dbaudit->close();
$db->close();

if ($confirmdeploy == 'confirmdeploy') {
	if (file_exists($deploysettingspath)) {
		@unlink($deploysettingspath);
		$deploysettings = '';
		if ($find != '' && $findtype != '') {
			$auditfiltered = ' to "filtered nodes" with string "' . $find . '" and type "' . $findtype . '"';
		} else {
			$auditfiltered = ' to "all nodes"';
		}
		$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     change settings     settings deployed successfully' . $auditfiltered;
	}
}

if ($orderby == 'computername' || $orderby == 'computerip' || $orderby == 'ostype') {
	sort($_SESSION['nodearray']);
} else {
	rsort($_SESSION['nodearray']);
}

$nodepagearray = array();
if ($_SESSION['csv_nodes'] == 'csv_nodes') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Nodes Status";' . "\n" . '"eurysco CSV Source Node: ' . $envcomputername . '";' . "\n" . '"eurysco CSV Filter ' . ucfirst(str_replace('metering', 'Metering', str_replace('_', ' ', preg_replace('/.*\./', '', $findtype)))) . ': ' . $find . '";' . "\n" . '"eurysco CSV Command: ' . $_SESSION['cmdnodes'] . '";' . "\n" . '"eurysco CSV Total Raws: ' . $_SESSION['nodecounter'] . '";' . "\n\n");
	array_push($tmpcsvexport, '"Name";' . '"Last Connection";' . '"Domain";' . '"Manufacturer";' . '"Model";' . '"CPU Model";' . '"OS Name";' . '"OS ServicePack";' . '"CPU Usage";' . '"Memory Usage";' . '"Sys Disk";' . '"Uptime";' . '"Computer IP";' . '"Filter Matches";' . '"Filter Results";' . "\n\n");
}
foreach ($_SESSION['nodearray'] as $noderow) {
	if (strlen($noderow[1]) > 18) { $computername = substr($noderow[1], 0, 18) . '...'; } else { $computername = $noderow[1]; }
	$comdomnamefull = $noderow[1] . '.' . $noderow[9];
	if (strlen($comdomnamefull) > 30) { $comdomname = substr($comdomnamefull, 0, 30) . '...'; } else { $comdomname = $comdomnamefull; }
	if (strlen($noderow[9]) > 18) { $domain = substr($noderow[9], 0, 18) . '...'; } else { $domain = $noderow[9]; }
	if (strlen($noderow[11]) > 15) { $manufacturer = substr($noderow[11], 0, 15) . '...'; } else { $manufacturer = $noderow[11]; }
	if (strlen($noderow[12]) > 15) { $model = substr($noderow[12], 0, 15) . '...'; } else { $model = $noderow[12]; }
	if (strlen($noderow[13]) > 42) { $osname = substr($noderow[13], 0, 42) . '...'; } else { $osname = $noderow[13]; }
	if (strlen($noderow[15]) > 42) { $cpumodel = substr($noderow[15], 0, 42) . '...'; } else { $cpumodel = $noderow[15]; }
	if ($results == 0) {
		array_push($nodepagearray, '<tr class="rowselectsrv"><td style="font-size:12px;" align="center"><a href=\'javascript:nodeinfo("' . $computername . '","' . $domain . '","' . $noderow[7] . '","' . $noderow[10] . '","' . $manufacturer . '","' . $model . '","' . $osname . '","' . $cpumodel . '","' . $noderow[11] . '","' . $noderow[12] . '","' . $noderow[13] . '","' . $noderow[15] . '","' . $noderow[9] . '","' . $comdomname . '","' . $comdomnamefull . '","' . $noderow[24] . '","' . $noderow[25] . '","' . $noderow[26] . '","' . $noderow[27] . '","' . urlencode(urlencode($find)) . '","' . $findtype . '","' . $noderow[28] . '","' . $noderow[31] . '","' . $noderow[32] . '");\' style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;">&nbsp;' . $noderow[2] . '</a></td><td style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"' . $noderow[19] . '>' . $noderow[33] . $noderow[29] . $noderow[1] . '</td><td style="font-size:12px;" align="center"' . $noderow[16] . '>' . $noderow[4] . '</td><td style="font-size:12px;" align="center"' . $noderow[17] . '>' . $noderow[5] . '</td><td style="font-size:12px;" align="center"' . $noderow[18] . '>' . $noderow[8] . '</td><td style="font-size:12px;" align="center">' . $noderow[6] . '</td><td style="font-size:12px;" align="center">' . $noderow[21] . '</td><td style="font-size:12px;" align="center">' . $noderow[22] . '</td><td style="font-size:12px;" align="center">' . $noderow[23] . '</td><td style="font-size:12px;" align="center"' . $noderow[20] . '>' . $noderow[3] . '</td><td style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;" align="center">' . $noderow[30] . '</td></tr>');
	} else {
		if ($noderow[35] == 'n/a') {
			array_push($nodepagearray, '<tr class="rowselectsrv"><td style="font-size:12px;" align="center"><a href=\'javascript:nodeinfo("' . $computername . '","' . $domain . '","' . $noderow[7] . '","' . $noderow[10] . '","' . $manufacturer . '","' . $model . '","' . $osname . '","' . $cpumodel . '","' . $noderow[11] . '","' . $noderow[12] . '","' . $noderow[13] . '","' . $noderow[15] . '","' . $noderow[9] . '","' . $comdomname . '","' . $comdomnamefull . '","' . $noderow[24] . '","' . $noderow[25] . '","' . $noderow[26] . '","' . $noderow[27] . '","' . urlencode(urlencode($find)) . '","' . $findtype . '","' . $noderow[28] . '","' . $noderow[31] . '","' . $noderow[32] . '");\' style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;">&nbsp;' . $noderow[2] . '</a></td><td style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"' . $noderow[19] . '>' . $noderow[33] . $noderow[29] . $noderow[1] . '</td><td style="font-size:12px;" align="center">' . $noderow[34] . '</td><td style="font-size:12px;" align="center"><div class="icon-none"></div></td></tr>');
		} else {
			array_push($nodepagearray, '<tr class="rowselectsrv"><td style="font-size:12px;" align="center"><a href=\'javascript:nodeinfo("' . $computername . '","' . $domain . '","' . $noderow[7] . '","' . $noderow[10] . '","' . $manufacturer . '","' . $model . '","' . $osname . '","' . $cpumodel . '","' . $noderow[11] . '","' . $noderow[12] . '","' . $noderow[13] . '","' . $noderow[15] . '","' . $noderow[9] . '","' . $comdomname . '","' . $comdomnamefull . '","' . $noderow[24] . '","' . $noderow[25] . '","' . $noderow[26] . '","' . $noderow[27] . '","' . urlencode(urlencode($find)) . '","' . $findtype . '","' . $noderow[28] . '","' . $noderow[31] . '","' . $noderow[32] . '");\' style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;">&nbsp;' . $noderow[2] . '</a></td><td style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"' . $noderow[19] . '>' . $noderow[33] . $noderow[29] . $noderow[1] . '</td><td style="font-size:12px;" align="center">' . $noderow[34] . '</td><td style="font-size:12px;" align="center"><textarea wrap="off" style="width:100%; font-family:\'Lucida Console\', Monaco, monospace; font-size:10px; height:50px; font-weight:normal; border:0px; padding-top:3px; padding-left:7px; background-color:transparent;" title="' . $noderow[35] . '">' . $noderow[35] . '</textarea></td></tr>');
		}
	}
	if ($_SESSION['csv_nodes'] == 'csv_nodes') { array_push($tmpcsvexport, '"' . $noderow[1] . '";' . '"' . $noderow[10] . '";' . '"' . $noderow[9] . '";' . '"' . $noderow[11] . '";' . '"' . $noderow[12] . '";' . '"' . $noderow[15] . '";' . '"' . $noderow[13] . '";' . '"' . $noderow[14] . '";' . '"' . str_replace('<div class="icon-none"></div>', 'n/a', $noderow[4]) . '";' . '"' . str_replace('<div class="icon-none"></div>', 'n/a', $noderow[5]) . '";' . '"' . str_replace('<div class="icon-none"></div>', 'n/a', $noderow[8]) . '";' . '"' . str_replace('<div class="icon-none"></div>', 'n/a', $noderow[3]) . '";' . '"' . $noderow[7] . '";' . '"' . $noderow[34] . '";' . '"' . $noderow[35] . '";' . "\n"); }
}

if ($_SESSION['csv_nodes'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/nodes.php?csv_nodes&orderby=' . $orderby . '&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_nodes'] == 'csv_nodes') {
	$_SESSION['csv_nodes'] = $tmpcsvexport;
}
if ($_SESSION['csv_nodes'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_nodes&source=' . $envcomputername . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

if ($orderby == 'computername') { $obycomputername = ' color:#8063C8;'; } else { $obycomputername = ''; }
if ($orderby == 'cpuusage') { $obycpuusage = ' color:#8063C8;'; } else { $obycpuusage = ''; }
if ($orderby == 'memoryusage') { $obymemoryusage = ' color:#8063C8;'; } else { $obymemoryusage = ''; }
if ($orderby == 'sysdiskuspc') { $obysysdiskuspc = ' color:#8063C8;'; } else { $obysysdiskuspc = ''; }
if ($orderby == 'totalprocesses') { $obytotalprocesses = ' color:#8063C8;'; } else { $obytotalprocesses = ''; }
if ($orderby == 'services_running') { $obyservices_running = ' color:#8063C8;'; } else { $obyservices_running = ''; }
if ($orderby == 'scheduler_total') { $obyscheduler_total = ' color:#8063C8;'; } else { $obyscheduler_total = ''; }
if ($orderby == 'events_error') { $obyevents_error = ' color:#8063C8;'; } else { $obyevents_error = ''; }
if ($orderby == 'uptime') { $obyuptime = ' color:#8063C8;'; } else { $obyuptime = ''; }
if ($orderby == 'computerip') { $obycomputerip = ' color:#8063C8;'; } else { $obycomputerip = ''; }
if ($orderby == 'matches') { $obymatches = ' color:#8063C8;'; } else { $obymatches = ''; }
if ($orderby == 'results') { $obyresults = ' color:#8063C8;'; } else { $obyresults = ''; }

if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesstatus'] > 1) { $resetallnodes = '<form id="agentrefreshform" name="agentrefreshform" method="post"><a href="javascript:resetallagent();" title="Restart Current Agents" style="font-size:13px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden; color:#8063C8;">&nbsp;<div class="icon-loop"></div></a><input type="hidden" id="agentrefresh" name="agentrefresh" value="" /></form>'; } else { $resetallnodes = ''; }
if ($results == 0) {
	$nodetable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="5%" style="font-size:12px;" align="center">' . $resetallnodes . '</td><td><a href="?orderby=computername&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obycomputername . '" title="Ascending Order by Name">Name</a></td><td width="5%" align="center"><a href="?orderby=cpuusage&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obycpuusage . '" title="Descending Order by CPU Usage">CPU</a></td><td width="5%" align="center"><a href="?orderby=memoryusage&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obymemoryusage . '" title="Descending Order by RAM Usage">RAM</a></td><td width="5%" align="right"><a href="?orderby=sysdiskuspc&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obysysdiskuspc . '" title="Descending Order by System Disk">&nbsp;<div class="icon-pie"></div></a></td><td width="5%" align="center"><a href="?orderby=totalprocesses&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obytotalprocesses . '" title="Descending Order by Processes">&nbsp;<div class="icon-bars"></div></a></td><td width="5%" align="center"><a href="?orderby=services_running&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyservices_running . '" title="Descending Order by Services">&nbsp;<div class="icon-cog"></div></a></td><td width="5%" align="center"><a href="?orderby=scheduler_total&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyscheduler_total . '" title="Descending Order by Scheduler">&nbsp;<div class="icon-calendar"></div></a></td><td width="5%" align="center"><a href="?orderby=events_error&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyevents_error . '" title="Descending Order by Events">&nbsp;<div class="icon-book"></div></a></td><td width="10%" align="center"><a href="?orderby=uptime&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyuptime . '" title="Descending Order by Uptime">Uptime</a></td><td align="center" width="10%"><a href="?orderby=computerip&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obycomputerip . '" title="Ascending Order by IP Adress">IP Adress</a></td></tr>';
	$noresultc = 11;
} else {
	$nodetable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="5%" style="font-size:12px;" align="center">' . $resetallnodes . '</td><td><a href="?orderby=computername&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obycomputername . '" title="Ascending Order by Name">Name</a></td><td width="5%" align="center"><a href="?orderby=matches&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obymatches . '" title="Descending Order by Matches">Matches</a></td><td width="50%" align="center"><a href="?orderby=results&find=' . urlencode($find) . '&findtype=' . $findtype . '&results=' . $results . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyresults . '" title="Descending Order by Results">Results</a></td></tr>';
	$noresultc = 4;
}
	
$nodepages = array_chunk($nodepagearray, 50);

if ($pgkey > count($nodepages) - 1) { $pgkey = count($nodepages) - 1; }

if (count($nodepages) > 0 && $_SESSION['cmdnodescurr'] == $_SESSION['cmdnodeslast']) {
	foreach($nodepages[$pgkey] as $noderw) {
		$nodetable = $nodetable . $noderw;
	}
}

if ($_SESSION['nodecounter'] == 0 || $_SESSION['cmdnodescurr'] != $_SESSION['cmdnodeslast']) { $nodetable = $nodetable . '<tr class="rowselectsrv"><td style="font-size:12px;" align="center" colspan="' . $noresultc . '">No Results...</td></tr>'; }

$nodetable = $nodetable . '</table>';

$nodepaging = '';
if (count($nodepages) > 1 && $_SESSION['cmdnodescurr'] == $_SESSION['cmdnodeslast']) {
	if ($pgkey > 5) {
		$nodepaging = $nodepaging . '<a href="?page=1&orderby=' . $orderby . '&find=' . urlencode($find) . '&findtype=' . $findtype . '&cmdfilter=' . $_SESSION['cmdfilter'] . '&results=' . $results . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($nodepages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$nodepaging = $nodepaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&find=' . urlencode($find) . '&findtype=' . $findtype . '&cmdfilter=' . $_SESSION['cmdfilter'] . '&results=' . $results . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($nodepages) > $pgkey + 6) {
		$nodepaging = $nodepaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($nodepages) . '&orderby=' . $orderby . '&find=' . urlencode($find) . '&findtype=' . $findtype . '&cmdfilter=' . $_SESSION['cmdfilter'] . '&results=' . $results . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($nodepages) . '</span></a>';
	}
	$nodetable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $nodepaging . '</blockquote><br />' . $nodetable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $nodepaging . '</blockquote>';
}

if ($_SESSION['cmdnodescurr'] != $_SESSION['cmdnodeslast']) {
	$_SESSION['cmdnodeslast'] = $_SESSION['cmdnodescurr'];
	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     nodes control     command "' . $cmdexecoutput . '" sent to nodes "' . trim($cmdnodeslist) . '"';
}

$totalelement = count($_SESSION['nodearray']);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';
if ($checkfindincr == 1 && number_format(($finish - $start), 0, ',', '.') < number_format(($nodesstatusrrsetting / 4), 0, ',', '.')) { $_SESSION['sessionfindincr'] = $_SESSION['sessionfindincr'] * 2; }

echo json_encode(array('nodetable'=>utf8_encode($nodetable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'deploysettings'=>$deploysettings,'csvexport'=>$csvexport,'filterprogress'=>$filterprogress,'cmdresult'=>$cmdresult));

include('/auditlog.php');

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>