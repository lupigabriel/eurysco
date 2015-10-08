<?php include('/include/init.php'); ?>

<?php

$corepath = str_replace('\\server', '\\core', $_SERVER['DOCUMENT_ROOT']);
$nodespath = str_replace('\\server', '\\nodes', $_SERVER['DOCUMENT_ROOT']);

if (isset($_POST['configstatus']) && isset($_POST['agentversion']) && isset($_POST['refreshrate']) && isset($_POST['computername']) && isset($_POST['coreport']) && isset($_POST['executorport']) && isset($_POST['cpuusage']) && isset($_POST['cpumanufacturer']) && isset($_POST['cpumodel']) && isset($_POST['cpucurrentclock']) && isset($_POST['cpumaxclock']) && isset($_POST['cpuarchitecture']) && isset($_POST['cpucores']) && isset($_POST['cputhreads']) && isset($_POST['cpusockettype']) && isset($_POST['osname']) && isset($_POST['osversion']) && isset($_POST['osservicepack']) && isset($_POST['osserialnumber']) && isset($_POST['manufacturer']) && isset($_POST['model']) && isset($_POST['domain']) && isset($_POST['totalprocesses']) && isset($_POST['localdatetime']) && isset($_POST['lastbootuptime']) && isset($_POST['uptime']) && isset($_POST['memoryusage']) && isset($_POST['totalmemory']) && isset($_POST['usedmemory']) && isset($_POST['freememory']) && isset($_POST['sysdiskuspc']) && isset($_POST['sysdiskfree']) && isset($_POST['sysdisksize']) && isset($_POST['sysdiskused']) && isset($_POST['sysdisktype']) && isset($_POST['services_total']) && isset($_POST['services_running']) && isset($_POST['services_error']) && isset($_POST['scheduler_total']) && isset($_POST['scheduler_error']) && isset($_POST['events_warning']) && isset($_POST['events_error']) && isset($_POST['nagios_status']) && isset($_POST['nagiostotalcount']) && isset($_POST['nagiosnormacount']) && isset($_POST['nagioswarnicount']) && isset($_POST['nagioscriticount']) && isset($_POST['nagiosunknocount']) && isset($_POST['netstatestcount']) && isset($_POST['netstatliscount']) && isset($_POST['netstattimcount']) && isset($_POST['netstatclocount']) && isset($_POST['netstat_status']) && isset($_POST['inventory_status']) && isset($_POST['programs_status'])) {

	echo '<refreshrate>' . $nodesrrsetting . '</refreshrate>' . "\n";

	$envcomputername = 'localhost';
	if (isset($_ENV["COMPUTERNAME"])) {
		$envcomputername = strtolower($_ENV["COMPUTERNAME"]);
	}

	if (file_exists($nodespath . '\\' . $_POST['computername'] . '\\exec.on')) { echo '<exec>on</exec>' . "\n"; } else { echo '<exec>off</exec>' . "\n"; }

	$db = new SQLite3(str_replace('\\server', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoServer');
	$db->busyTimeout(30000);
	$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');

	$hashnodesxml = '<inventory>0null</inventory>' . "\n" . '<programs>0null</programs>' . "\n" . '<processes>0null</processes>' . "\n" . '<services>0null</services>' . "\n" . '<netstat>0null</netstat>' . "\n" . '<scheduler>0null</scheduler>' . "\n" . '<events>0null</events>' . "\n" . '<nagios>0null</nagios>' . "\n";
	$hashnodes = $db->query('SELECT * FROM nodesHash WHERE node = "' . strtolower($_POST['computername']) . '"');
	while ($hashnode = $hashnodes->fetchArray()) {
		$hashnodesxml = '<inventory>' . $hashnode['inventory'] . '</inventory>' . "\n" . '<programs>' . $hashnode['programs'] . '</programs>' . "\n" . '<processes>' . $hashnode['processes'] . '</processes>' . "\n" . '<services>' . $hashnode['services'] . '</services>' . "\n" . '<netstat>' . $hashnode['netstat'] . '</netstat>' . "\n" . '<scheduler>' . $hashnode['scheduler'] . '</scheduler>' . "\n" . '<events>' . $hashnode['events'] . '</events>' . "\n" . '<nagios>' . $hashnode['nagios'] . '</nagios>' . "\n";
	}
	echo $hashnodesxml;

	echo '<settings>';
	if (!file_exists($nodespath . '\\' . $_POST['computername'] . '\\config_settings.xml')) { echo '0null'; } else { echo hash_file('md2', $nodespath . '\\' . $_POST['computername'] . '\\config_settings.xml'); }
	echo '</settings>' . "\n";

	if (!file_exists($nodespath . '\\' . strtolower($_POST['computername']) . '\\')) { mkdir($nodespath . '\\' . strtolower($_POST['computername']) . '\\', 0777); @copy($corepath . '\\conf\\config_settings.xml', $nodespath . '\\' . strtolower($_POST['computername']) . '\\config_settings.xml'); }
	if ($_POST['configstatus'] == 0 && file_exists($config_settings)) { @copy($corepath . '\\conf\\config_settings.xml', $nodespath . '\\' . strtolower($_POST['computername']) . '\\config_settings.xml'); }
	$lastcom = date('Y-m-d H:i:s');
	if (strtolower($_POST['computername']) == strtolower($envcomputername)) { $nodeip = '127.0.0.1'; } else { $nodeip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
	if (is_null($db->querySingle('SELECT node FROM nodesStatus WHERE node = "' . strtolower($_POST['computername']) . '"'))) {
		$db->query('INSERT INTO nodesStatus (node, agentversion, refreshrate, computername, coreport, executorport, lastcom, computerip, cpuusage, cpumanufacturer, cpumodel, cpucurrentclock, cpumaxclock, cpuarchitecture, cpucores, cputhreads, cpusockettype, osname, osversion, osservicepack, osserialnumber, manufacturer, model, domain, totalprocesses, localdatetime, lastbootuptime, uptime, memoryusage, totalmemory, usedmemory, freememory, sysdiskuspc, sysdiskfree, sysdisksize, sysdiskused, sysdisktype, services_total, services_running, services_error, scheduler_total, scheduler_error, events_warning, events_error, nagios_status, nagiostotalcount, nagiosnormacount, nagioswarnicount, nagioscriticount, nagiosunknocount, netstatestcount, netstatliscount, netstattimcount, netstatclocount, netstat_status, inventory_status, programs_status) VALUES ("' . strtolower($_POST['computername']) . '", "' . $_POST['agentversion'] . '", "' . $_POST['refreshrate'] . '", "' . strtolower($_POST['computername']) . '", "' . strtolower($_POST['coreport']) . '", "' . strtolower($_POST['executorport']) . '", "' . $lastcom . '", "' . $nodeip . '", "' . $_POST['cpuusage'] . '", "' . $_POST['cpumanufacturer'] . '", "' . $_POST['cpumodel'] . '", "' . $_POST['cpucurrentclock'] . '", "' . $_POST['cpumaxclock'] . '", "' . $_POST['cpuarchitecture'] . '", "' . $_POST['cpucores'] . '", "' . $_POST['cputhreads'] . '", "' . $_POST['cpusockettype'] . '", "' . $_POST['osname'] . '", "' . $_POST['osversion'] . '", "' . $_POST['osservicepack'] . '", "' . $_POST['osserialnumber'] . '", "' . $_POST['manufacturer'] . '", "' . $_POST['model'] . '", "' . strtolower($_POST['domain']) . '", "' . $_POST['totalprocesses'] . '", "' . $_POST['localdatetime'] . '", "' . $_POST['lastbootuptime'] . '", "' . $_POST['uptime'] . '", "' . $_POST['memoryusage'] . '", "' . $_POST['totalmemory'] . '", "' . $_POST['usedmemory'] . '", "' . $_POST['freememory'] . '", "' . $_POST['sysdiskuspc'] . '", "' . $_POST['sysdiskfree'] . '", "' . $_POST['sysdisksize'] . '", "' . $_POST['sysdiskused'] . '", "' . $_POST['sysdisktype'] . '", "' . $_POST['services_total'] . '", "' . $_POST['services_running'] . '", "' . $_POST['services_error'] . '", "' . $_POST['scheduler_total'] . '", "' . $_POST['scheduler_error'] . '", "' . $_POST['events_warning'] . '", "' . $_POST['events_error'] . '", "' . $_POST['nagios_status'] . '", "' . $_POST['nagiostotalcount'] . '", "' . $_POST['nagiosnormacount'] . '", "' . $_POST['nagioswarnicount'] . '", "' . $_POST['nagioscriticount'] . '", "' . $_POST['nagiosunknocount'] . '", "' . $_POST['netstatestcount'] . '", "' . $_POST['netstatliscount'] . '", "' . $_POST['netstattimcount'] . '", "' . $_POST['netstatclocount'] . '", "' . $_POST['netstat_status'] . '", "' . $_POST['inventory_status'] . '", "' . $_POST['programs_status'] . '")');
	} else {
		$db->query('UPDATE nodesStatus SET agentversion = "' . $_POST['agentversion'] . '", refreshrate = "' . $_POST['refreshrate'] . '", computername = "' . strtolower($_POST['computername']) . '", coreport = "' . strtolower($_POST['coreport']) . '", executorport = "' . strtolower($_POST['executorport']) . '", lastcom = "' . $lastcom . '", computerip = "' . $nodeip . '", cpuusage = "' . $_POST['cpuusage'] . '", cpumanufacturer = "' . $_POST['cpumanufacturer'] . '", cpumodel = "' . $_POST['cpumodel'] . '", cpucurrentclock = "' . $_POST['cpucurrentclock'] . '", cpumaxclock = "' . $_POST['cpumaxclock'] . '", cpuarchitecture = "' . $_POST['cpuarchitecture'] . '", cpucores = "' . $_POST['cpucores'] . '", cputhreads = "' . $_POST['cputhreads'] . '", cpusockettype = "' . $_POST['cpusockettype'] . '", osname = "' . $_POST['osname'] . '", osversion = "' . $_POST['osversion'] . '", osservicepack = "' . $_POST['osservicepack'] . '", osserialnumber = "' . $_POST['osserialnumber'] . '", manufacturer = "' . $_POST['manufacturer'] . '", model = "' . $_POST['model'] . '", domain = "' . strtolower($_POST['domain']) . '", totalprocesses = "' . $_POST['totalprocesses'] . '", localdatetime = "' . $_POST['localdatetime'] . '", lastbootuptime = "' . $_POST['lastbootuptime'] . '", uptime = "' . $_POST['uptime'] . '", memoryusage = "' . $_POST['memoryusage'] . '", totalmemory = "' . $_POST['totalmemory'] . '", usedmemory = "' . $_POST['usedmemory'] . '", freememory = "' . $_POST['freememory'] . '", sysdiskuspc = "' . $_POST['sysdiskuspc'] . '", sysdiskfree = "' . $_POST['sysdiskfree'] . '", sysdisksize = "' . $_POST['sysdisksize'] . '", sysdiskused = "' . $_POST['sysdiskused'] . '", sysdisktype = "' . $_POST['sysdisktype'] . '", services_total = "' . $_POST['services_total'] . '", services_running = "' . $_POST['services_running'] . '", services_error = "' . $_POST['services_error'] . '", scheduler_total = "' . $_POST['scheduler_total'] . '", scheduler_error = "' . $_POST['scheduler_error'] . '", events_warning = "' . $_POST['events_warning'] . '", events_error = "' . $_POST['events_error'] . '", nagios_status = "' . $_POST['nagios_status'] . '", nagiostotalcount = "' . $_POST['nagiostotalcount'] . '", nagiosnormacount = "' . $_POST['nagiosnormacount'] . '", nagioswarnicount = "' . $_POST['nagioswarnicount'] . '", nagioscriticount = "' . $_POST['nagioscriticount'] . '", nagiosunknocount = "' . $_POST['nagiosunknocount'] . '", netstatestcount = "' . $_POST['netstatestcount'] . '", netstatliscount = "' . $_POST['netstatliscount'] . '", netstattimcount = "' . $_POST['netstattimcount'] . '", netstatclocount = "' . $_POST['netstatclocount'] . '", netstat_status = "' . $_POST['netstat_status'] . '", inventory_status = "' . $_POST['inventory_status'] . '", programs_status = "' . $_POST['programs_status'] . '" WHERE node = "' . strtolower($_POST['computername']) . '"');
	}
	$xml = '<system>' . "\n" . '	<status>' . "\n" . '		<agentversion>' . $_POST['agentversion'] . '</agentversion>' . '<refreshrate>' . $_POST['refreshrate'] . '</refreshrate>' . '<computername>' . strtolower($_POST['computername']) . '</computername>' . '<coreport>' . strtolower($_POST['coreport']) . '</coreport>' . '<executorport>' . strtolower($_POST['executorport']) . '</executorport>' . '<lastcom>' . $lastcom . '</lastcom>' . '<computerip>' . $nodeip . '</computerip>' . '<cpuusage>' . $_POST['cpuusage'] . '</cpuusage>' . '<cpumanufacturer>' . $_POST['cpumanufacturer'] . '</cpumanufacturer>' . '<cpumodel>' . $_POST['cpumodel'] . '</cpumodel>' . '<cpucurrentclock>' . $_POST['cpucurrentclock'] . '</cpucurrentclock>' . '<cpumaxclock>' . $_POST['cpumaxclock'] . '</cpumaxclock>' . '<cpuarchitecture>' . $_POST['cpuarchitecture'] . '</cpuarchitecture>' . '<cpucores>' . $_POST['cpucores'] . '</cpucores>' . '<cputhreads>' . $_POST['cputhreads'] . '</cputhreads>' . '<cpusockettype>' . $_POST['cpusockettype'] . '</cpusockettype>' . '<osname>' . $_POST['osname'] . '</osname>' . '<osversion>' . $_POST['osversion'] . '</osversion>' . '<osservicepack>' . $_POST['osservicepack'] . '</osservicepack>' . '<osserialnumber>' . $_POST['osserialnumber'] . '</osserialnumber>' . '<manufacturer>' . $_POST['manufacturer'] . '</manufacturer>' . '<model>' . $_POST['model'] . '</model>' . '<domain>' . strtolower($_POST['domain']) . '</domain>' . '<totalprocesses>' . $_POST['totalprocesses'] . '</totalprocesses>' . '<localdatetime>' . $_POST['localdatetime'] . '</localdatetime>' . '<lastbootuptime>' . $_POST['lastbootuptime'] . '</lastbootuptime>' . '<uptime>' . $_POST['uptime'] . '</uptime>' . '<memoryusage>' . $_POST['memoryusage'] . '</memoryusage>' . '<totalmemory>' . $_POST['totalmemory'] . '</totalmemory>' . '<usedmemory>' . $_POST['usedmemory'] . '</usedmemory>' . '<freememory>' . $_POST['freememory'] . '</freememory>' . '<sysdiskuspc>' . $_POST['sysdiskuspc'] . '</sysdiskuspc>' . '<sysdiskfree>' . $_POST['sysdiskfree'] . '</sysdiskfree>' . '<sysdisksize>' . $_POST['sysdisksize'] . '</sysdisksize>' . '<sysdiskused>' . $_POST['sysdiskused'] . '</sysdiskused>' . '<sysdisktype>' . $_POST['sysdisktype'] . '</sysdisktype>' . '<services_total>' . $_POST['services_total'] . '</services_total>' . '<services_running>' . $_POST['services_running'] . '</services_running>' . '<services_error>' . $_POST['services_error'] . '</services_error>' . '<scheduler_total>' . $_POST['scheduler_total'] . '</scheduler_total>' . '<scheduler_error>' . $_POST['scheduler_error'] . '</scheduler_error>' . '<events_warning>' . $_POST['events_warning'] . '</events_warning>' . '<events_error>' . $_POST['events_error'] . '</events_error>' . '<nagios_status>' . $_POST['nagios_status'] . '</nagios_status>' . '<nagiostotalcount>' . $_POST['nagiostotalcount'] . '</nagiostotalcount>' . '<nagiosnormacount>' . $_POST['nagiosnormacount'] . '</nagiosnormacount>' . '<nagioswarnicount>' . $_POST['nagioswarnicount'] . '</nagioswarnicount>' . '<nagioscriticount>' . $_POST['nagioscriticount'] . '</nagioscriticount>' . '<nagiosunknocount>' . $_POST['nagiosunknocount'] . '</nagiosunknocount>' . '<netstatestcount>' . $_POST['netstatestcount'] . '</netstatestcount>' . '<netstatliscount>' . $_POST['netstatliscount'] . '</netstatliscount>' . '<netstattimcount>' . $_POST['netstattimcount'] . '</netstattimcount>' . '<netstatclocount>' . $_POST['netstatclocount'] . '</netstatclocount>' . '<netstat_status>' . $_POST['netstat_status'] . '</netstat_status>' . '<inventory_status>' . $_POST['inventory_status'] . '</inventory_status>' . '<programs_status>' . $_POST['programs_status'] . '</programs_status>' . "\n" . '	</status>' . "\n" . '</system>';
	if (is_null($db->querySingle('SELECT node FROM xmlStatus WHERE node = "' . strtolower($_POST['computername']) . '"'))) {
		$db->query('INSERT INTO xmlStatus (node, xml) VALUES ("' . strtolower($_POST['computername']) . '", "' . $xml . '")');
	} else {
		$db->query('UPDATE xmlStatus SET xml = "' . $xml . '" WHERE node = "' . strtolower($_POST['computername']) . '"');
	}
	
	echo '<groups>' . "\n";
	$grouplist = scandir($corepath . '\\groups\\');
	$grouplistcheck = 'Administrators;Auditors;Operators;Users;';
	foreach($grouplist as $group) {
		if(pathinfo($group)['extension'] == 'xml') {
			if (filesize($corepath . '\\groups\\' . $group) == 0) {
				@unlink($corepath . '\\groups\\' . $group);
			} else {
				$groupxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($corepath . '\\groups\\' . $group, true)))));
				if (hash('sha512', $groupxml->settings->groupname . 'Distributed') == $groupxml->settings->groupauth) {
					$mcrykey = pack('H*', hash('sha256', hash('sha512', str_replace('.xml', '', $group))));
					$groupsxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($corepath . '\\groups\\' . $group, true)))));
					$usersett = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($groupsxml->settings->groupsettings), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($groupsxml->settings->groupsettings), 0, $iv_size)));
					$checkprefilter = 0;
					if ($usersett['nodesstatusf'] != '') {
						$wl_xmlstatus = strtolower('<computername>' . $_POST['computername'] . '</computername><osname>' . $_POST['osname'] . '</osname><osversion>' . $_POST['osversion'] . '</osversion><osservicepack>' . $_POST['osservicepack'] . '</osservicepack><osserialnumber>' . $_POST['osserialnumber'] . '</osserialnumber><manufacturer>' . $_POST['manufacturer'] . '</manufacturer><model>' . $_POST['model'] . '</model><domain>' . $_POST['domain'] . '</domain>');
						$prefilter = $usersett['nodesstatusf'];
						$checkprefilter = 1;
						if (substr($prefilter, 0, 1) != '-') {
							if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($prefilter))) . '/', $wl_xmlstatus) || strpos($wl_xmlstatus, strtolower($prefilter)) > -1) {
								$checkprefilter = 0;
							} else {
								$checkprefilter = 1;
							}
						} else {
							$notprefilter = substr($prefilter, 1);
							if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notprefilter))) . '/', $wl_xmlstatus) && !strpos($wl_xmlstatus, strtolower($notprefilter))) {
								$checkprefilter = 0;
							} else {
								$checkprefilter = 1;
							}
						}
					}
					if ($checkprefilter == 0) {
						echo '<' . str_replace('.xml', '', str_replace(' ', '', $group)) . '>';
						echo '<file>' . $group . '</file>';
						echo '<hash>' . hash_file('md2', $corepath . '\\groups\\' . $group) . '</hash>';
						echo '</' . str_replace('.xml', '', str_replace(' ', '', $group)) . '>' . "\n";
						$grouplistcheck = $grouplistcheck . str_replace('.xml', '', $group) . ';';
					}
				}
			}
		}
	}
	$grouplistcheck = trim($grouplistcheck, ';');
	echo '</groups>' . "\n";

	echo '<users>' . "\n";
	$userlist = scandir($corepath . '\\users\\');
	foreach($userlist as $user) {
		if(pathinfo($user)['extension'] == 'xml') {
			if (filesize($corepath . '\\users\\' . $user) == 0) {
				@unlink($corepath . '\\users\\' . $user);
			} else {
				$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($corepath . '\\users\\' . $user, true)))));
				if (hash('sha512', $userxml->settings->username . 'Distributed') == $userxml->settings->userauth) {
					$grouplist = explode(';', $grouplistcheck);
					foreach ($grouplist as $group) {
						if (hash('sha512', $userxml->settings->username . $group) == $userxml->settings->usertype) {
							echo '<' . str_replace('.xml', '', $user) . '>';
							echo '<file>' . $user . '</file>';
							echo '<hash>' . hash_file('md2', $corepath . '\\users\\' . $user) . '</hash>';
							echo '</' . str_replace('.xml', '', $user) . '>' . "\n";
						}
					}
				}
			}
		}
	}
	echo '</users>' . "\n";

	echo '</euryscoServer>';

}

$db->close();


foreach (get_defined_vars() as $key=>$val) {
	if ($key != '_GET' && $key != '_POST' && $key != '_COOKIE' && $key != '_FILES' && $key != '_SERVER' && $key != '_SESSION' && $key != '_ENV') {
		$$key = null;
		unset($$key);
	}
}

if (extension_loaded('zlib')) { ob_end_flush(); }

?>