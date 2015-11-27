<?php

sleep(5);
exec('taskkill.exe /f /im "eurysco.agent.status.check.exe" /t >nul 2>nul');
sleep(5);
pclose(popen('start "eurysco agent status check" /b "' . $_SESSION['agentpath'] . '\\eurysco.agent.status.check.exe" "' . $_SESSION['agentpath'] . '\\temp\\agent.status" >nul 2>nul', 'r'));
sleep(5);

$agentstatus = '';

$agentversion = include(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\version.phtml');

@include($_SESSION['agentpath'] . '\\' . 'agent.init.php');

$cycle_count = 1;
$cycle_count_processes = 1;
$cycle_count_services = 1;
$cycle_count_scheduler = 1;
$cycle_count_events = 1;
$cycle_count_programs = 1;
$cycle_count_inventory = 1;
$cycle_count_nagios = 1;
$cycle_count_netstat = 1;

$checkdownload = 0;
$settings = '0null';

$config_settings = $_SESSION['agentpath'] . '\\conf\\config_settings.xml';
$config_core = $_SESSION['agentpath'] . '\\conf\\config_core.xml';
$config_executor = $_SESSION['agentpath'] . '\\conf\\config_executor.xml';

$templist = scandir($_SESSION['agentpath'] . '\\temp\\');
foreach($templist as $temp) {
	if (strpos(strtolower('|' . $temp), strtolower('|sess')) > -1) {
		@unlink($_SESSION['agentpath'] . '\\temp\\' . $temp);
	}
}

$wmi = new COM('winmgmts://');
require('/include/class.WindowsRegistry.php');
$winReg = new WindowsRegistry();

$cs_computername = '-';
$cs_domain = '-';
$cs_manufacturer = '-';
$cs_model = '-';
$wmisclass = $wmi->ExecQuery("SELECT Domain, Manufacturer, Model, Name FROM Win32_ComputerSystem");
foreach($wmisclass as $obj) {
	$cs_computername = $obj->Name;
	$cs_domain = $obj->Domain;
	$cs_manufacturer = preg_replace('/[^a-zA-Z0-9 \.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\r\n]*/', '', $obj->Manufacturer);
	$cs_model = preg_replace('/[^a-zA-Z0-9 \.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\r\n]*/', '', $obj->Model);
}

$cpucount = 1;
$wmicpu = $wmi->ExecQuery("SELECT * FROM Win32_ComputerSystem");
foreach($wmicpu as $cpu) {
	$cpucount = $cpu->NumberOfProcessors;
	foreach($cpu->Properties_ as $wmiprop) {
		if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfLogicalProcessors|')) > -1) {
			$cpucount = $cpu->NumberOfLogicalProcessors;
		}
	}
}

$osversion = '';
$wmios = $wmi->ExecQuery("SELECT * FROM Win32_OperatingSystem");
foreach($wmios as $os) {
	$osversion = preg_replace('/\..*/', '', $os->Version);
}

$config_agentsrv = $_SESSION['agentpath'] . '\\conf\\config_agent.xml';
$eurysco_serverconaddress = '';
$eurysco_serverconport = '';
$eurysco_serverconpassword = '';
$eurysco_sslverifyhost = 'true';
$eurysco_sslverifypeer = 'true';
if (file_exists($config_agentsrv)) {
	$xmlagent = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_agentsrv, true)))));
	$eurysco_serverconaddress = $xmlagent->settings->serverconnectionaddress;
	$eurysco_serverconport = $xmlagent->settings->serverconnectionport;
	$eurysco_serverconpassword = $xmlagent->settings->serverconnectionpassword;
	$eurysco_sslverifyhost = $xmlagent->settings->sslverifyhost;
	$eurysco_sslverifypeer = $xmlagent->settings->sslverifypeer;
}

$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
$mcrykey = pack('H*', hash('sha256', hash('sha512', 'vNqgi_R1QX%C;z-724p4lFHm*?7c!e2%vG9tp+-*@#%=?!_;./' . hash('tiger128,4', $eurysco_serverconport) . '-*@#%=?!_;./-f;bTh2XXqW%Zs%88+/-7pVb;X')));
$mcrykeycmd = pack('H*', hash('sha256', md5(strtolower($cs_computername))));

while(true) {

	if ($eurysco_serverconaddress != '' && $eurysco_serverconport != '' && $eurysco_serverconpassword != '') {

		if (file_exists($config_core)) {
			$xmlsettings = simplexml_load_file($config_core);
			$core_port = $xmlsettings->settings->corelisteningport;
		} else {
			$core_port = 59980;
		}

		if (file_exists($config_executor)) {
			$xmlsettings = simplexml_load_file($config_executor);
			$executor_port = $xmlsettings->settings->executorlisteningport;
		} else {
			$executor_port = 59981;
		}

		if (file_exists($config_settings)) {
			$config_settings_status = 1;
			$xmlsettings = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_settings, true)))));
			$timezonesetting = $xmlsettings->settings->timezonesetting;
			date_default_timezone_set($timezonesetting);
			$roottaskssetting = $xmlsettings->settings->roottaskssetting;
			$cycle_count_status_limit = $xmlsettings->settings->statuscsetting;
			$cycle_count_processes_limit = $xmlsettings->settings->processescsetting;
			$cycle_count_services_limit = $xmlsettings->settings->servicescsetting;
			$cycle_count_scheduler_limit = $xmlsettings->settings->taskscsetting;
			$cycle_count_events_limit = $xmlsettings->settings->eventscsetting;
			$cycle_count_programs_limit = $xmlsettings->settings->programscsetting;
			$cycle_count_inventory_limit = $xmlsettings->settings->inventorycsetting;
			$cycle_count_nagios_limit = $xmlsettings->settings->nagioscsetting;
			$cycle_count_netstat_limit = $xmlsettings->settings->netstatcsetting;
		} else {
			$config_settings_status = 0;
			$timezonesetting = 'UTC';
			date_default_timezone_set($timezonesetting);
			$roottaskssetting = 'Enable';
			$cycle_count_status_limit = 1;
			$cycle_count_processes_limit = 2;
			$cycle_count_services_limit = 2;
			$cycle_count_scheduler_limit = 4;
			$cycle_count_events_limit = 60;
			$cycle_count_programs_limit = 120;
			$cycle_count_inventory_limit = 240;
			$cycle_count_nagios_limit = 2;
			$cycle_count_netstat_limit = 8;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $eurysco_sslverifypeer);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $eurysco_sslverifyhost);
		curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/connect.php');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 30000);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
		curl_setopt($ch, CURLOPT_USERPWD, trim(hash('sha256', $eurysco_serverconport . 'euryscoServer' . $eurysco_serverconport)) . ':' . trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($eurysco_serverconpassword), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($eurysco_serverconpassword), 0, $iv_size))));
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
		curl_exec($ch);

		// ### SYSTEM STATUS ###
		if ($refreshrate < 120) { @include($_SESSION['agentpath'] . '\\' . 'agent.status.php'); }

		if (strpos($agentstatus, 'Connection Success') > 0 && $refreshrate < 120) {
		
			// ### SYSTEM PROCESSES ###
			if ($cycle_count_processes_limit != 'Hold' || $cycle_count == 1 || $cycle_count_processes == 1000) {
				if ($cycle_count_processes >= $cycle_count_processes_limit || $cycle_count == 1) {
					$processes_total = 0;
					@include($_SESSION['agentpath'] . '\\' . 'agent.processes.php');
					if (@filesize($_SESSION['agentpath'] . '\\temp\\processes.xml.gz') == 0) {
						@unlink($_SESSION['agentpath'] . '\\temp\\processes.xml.gz');
					} else {
						if ($processes != hash_file('md2', $_SESSION['agentpath'] . '\\temp\\processes.xml.gz')) {
							curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/upload.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($ch, CURLOPT_POST, true);
							$data = array(
								'type' => 'nodes',
								'node' => $cs_computername,
								'file' => '@' . $_SESSION['agentpath'] . '\\temp\\processes.xml.gz',
								'comp' => 'comp',
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_exec($ch);
						}
					}
					$cycle_count_processes = 1;
				}
			}

			// ### SYSTEM SERVICES ###
			if ($cycle_count_services_limit != 'Hold' || $cycle_count == 1 || $cycle_count_services == 1000) {
				if ($cycle_count_services >= $cycle_count_services_limit || $cycle_count == 1) {
					$services_total = 0;
					$services_running = 0;
					$services_error = 0;
					@include($_SESSION['agentpath'] . '\\' . 'agent.services.php');
					if (@filesize($_SESSION['agentpath'] . '\\temp\\services.xml.gz') == 0) {
						@unlink($_SESSION['agentpath'] . '\\temp\\services.xml.gz');
					} else {
						if ($services != hash_file('md2', $_SESSION['agentpath'] . '\\temp\\services.xml.gz')) {
							curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/upload.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($ch, CURLOPT_POST, true);
							$data = array(
								'type' => 'nodes',
								'node' => $cs_computername,
								'file' => '@' . $_SESSION['agentpath'] . '\\temp\\services.xml.gz',
								'comp' => 'comp',
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_exec($ch);
						}
					}
					$cycle_count_services = 1;
				}
			}

			// ### SYSTEM SCHEDULER ###
			if ($cycle_count_scheduler_limit != 'Hold' || $cycle_count == 1 || $cycle_count_scheduler == 1000) {
				if ($cycle_count_scheduler >= $cycle_count_scheduler_limit || $cycle_count == 1) {
					$scheduler_total = 0;
					$scheduler_error = 0;
					@include($_SESSION['agentpath'] . '\\' . 'agent.scheduler.php');
					if (@filesize($_SESSION['agentpath'] . '\\temp\\scheduler.xml.gz') == 0) {
						@unlink($_SESSION['agentpath'] . '\\temp\\scheduler.xml.gz');
					} else {
						if ($scheduler != hash_file('md2', $_SESSION['agentpath'] . '\\temp\\scheduler.xml.gz')) {
							curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/upload.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($ch, CURLOPT_POST, true);
							$data = array(
								'type' => 'nodes',
								'node' => $cs_computername,
								'file' => '@' . $_SESSION['agentpath'] . '\\temp\\scheduler.xml.gz',
								'comp' => 'comp',
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_exec($ch);
						}
					}
					$cycle_count_scheduler = 1;
				}
			}

			// ### SYSTEM EVENTS ###
			if ($cycle_count_events_limit != 'Hold' || $cycle_count == 1) {
				if ($cycle_count_events >= $cycle_count_events_limit || $cycle_count == 1) {
					$events_warning = 0;
					$events_error = 0;
					@include($_SESSION['agentpath'] . '\\' . 'agent.events.php');
					if (@filesize($_SESSION['agentpath'] . '\\temp\\events.xml.gz') == 0) {
						@unlink($_SESSION['agentpath'] . '\\temp\\events.xml.gz');
					} else {
						if ($events != hash_file('md2', $_SESSION['agentpath'] . '\\temp\\events.xml.gz')) {
							curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/upload.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($ch, CURLOPT_POST, true);
							$data = array(
								'type' => 'nodes',
								'node' => $cs_computername,
								'file' => '@' . $_SESSION['agentpath'] . '\\temp\\events.xml.gz',
								'comp' => 'comp',
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_exec($ch);
						}
					}
					$cycle_count_events = 1;
				}
			}

			// ### SYSTEM PROGRAMS ###
			if ($cycle_count_programs_limit != 'Hold' || $cycle_count == 1) {
				if ($cycle_count_programs >= $cycle_count_programs_limit || $cycle_count == 1) {
					@include($_SESSION['agentpath'] . '\\' . 'agent.programs.php');
					if (@filesize($_SESSION['agentpath'] . '\\temp\\programs.xml.gz') == 0) {
						@unlink($_SESSION['agentpath'] . '\\temp\\programs.xml.gz');
					} else {
						if ($programs != hash_file('md2', $_SESSION['agentpath'] . '\\temp\\programs.xml.gz')) {
							curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/upload.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($ch, CURLOPT_POST, true);
							$data = array(
								'type' => 'nodes',
								'node' => $cs_computername,
								'file' => '@' . $_SESSION['agentpath'] . '\\temp\\programs.xml.gz',
								'comp' => 'comp',
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_exec($ch);
						}
					}
					$cycle_count_programs = 1;
				}
			}

			// ### SYSTEM INVENTORY ###
			if ($cycle_count_inventory_limit != 'Hold' || $cycle_count == 1) {
				if ($cycle_count_inventory >= $cycle_count_inventory_limit || $cycle_count == 1) {
					@include($_SESSION['agentpath'] . '\\' . 'agent.inventory.php');
					if (@filesize($_SESSION['agentpath'] . '\\temp\\inventory.xml.gz') == 0) {
						@unlink($_SESSION['agentpath'] . '\\temp\\inventory.xml.gz');
					} else {
						if ($inventory != hash_file('md2', $_SESSION['agentpath'] . '\\temp\\inventory.xml.gz')) {
							curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/upload.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($ch, CURLOPT_POST, true);
							$data = array(
								'type' => 'nodes',
								'node' => $cs_computername,
								'file' => '@' . $_SESSION['agentpath'] . '\\temp\\inventory.xml.gz',
								'comp' => 'comp',
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_exec($ch);
						}
					}
					$cycle_count_inventory = 1;
				}
			}

			// ### NAGIOS STATUS ###
			if ($cycle_count_nagios_limit != 'Hold' || $cycle_count == 1) {
				if ($cycle_count_nagios >= $cycle_count_nagios_limit || $cycle_count == 1) {
					@include($_SESSION['agentpath'] . '\\' . 'agent.nagios.php');
					if (@filesize($_SESSION['agentpath'] . '\\temp\\nagios.xml.gz') == 0) {
						@unlink($_SESSION['agentpath'] . '\\temp\\nagios.xml.gz');
					} else {
						if ($nagios != hash_file('md2', $_SESSION['agentpath'] . '\\temp\\nagios.xml.gz')) {
							curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/upload.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($ch, CURLOPT_POST, true);
							$data = array(
								'type' => 'nodes',
								'node' => $cs_computername,
								'file' => '@' . $_SESSION['agentpath'] . '\\temp\\nagios.xml.gz',
								'comp' => 'comp',
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_exec($ch);
						}
					}
					$cycle_count_nagios = 1;
				}
			}

			// ### NETSTAT STATUS ###
			if ($cycle_count_netstat_limit != 'Hold' || $cycle_count == 1) {
				if ($cycle_count_netstat >= $cycle_count_netstat_limit || $cycle_count == 1) {
					@include($_SESSION['agentpath'] . '\\' . 'agent.netstat.php');
					if (@filesize($_SESSION['agentpath'] . '\\temp\\netstat.xml.gz') == 0) {
						@unlink($_SESSION['agentpath'] . '\\temp\\netstat.xml.gz');
					} else {
						if ($netstat != hash_file('md2', $_SESSION['agentpath'] . '\\temp\\netstat.xml.gz')) {
							curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/upload.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($ch, CURLOPT_POST, true);
							$data = array(
								'type' => 'nodes',
								'node' => $cs_computername,
								'file' => '@' . $_SESSION['agentpath'] . '\\temp\\netstat.xml.gz',
								'comp' => 'comp',
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_exec($ch);
						}
					}
					$cycle_count_netstat = 1;
				}
			}

			$cycle_count = $cycle_count + 1;
			$cycle_count_processes = $cycle_count_processes + 1;
			$cycle_count_services = $cycle_count_services + 1;
			$cycle_count_scheduler = $cycle_count_scheduler + 1;
			$cycle_count_events = $cycle_count_events + 1;
			$cycle_count_programs = $cycle_count_programs + 1;
			$cycle_count_inventory = $cycle_count_inventory + 1;
			$cycle_count_nagios = $cycle_count_nagios + 1;
			$cycle_count_netstat = $cycle_count_netstat + 1;

		}
		
		// ### CHECK CONNECTION AND UPDATE STATUS ###
		$xmlresponse = 'Error Connection';
		$agentstatus = 'Disconnected';
		if (!isset($refreshrate)) { $refreshrate = 15; }
		curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		$data = array(
			'configstatus' => $config_settings_status,
			'agentversion' => $agentversion,
			'refreshrate' => $refreshrate,
			'computername' => $cs_computername,
			'coreport' => $core_port,
			'executorport' => $executor_port,
			'cpuusage' => $cpuload,
			'cpumanufacturer' => preg_replace('/[^a-zA-Z0-9 \.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\r\n]*/', '', $cpumanu),
			'cpumodel' => str_replace('  ', ' ', str_replace('   ', ' ', preg_replace('/[^a-zA-Z0-9 \.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\r\n]*/', '', $cpuname))),
			'cpucurrentclock' => number_format($cpucclo, 0, ',', '.'),
			'cpumaxclock' => number_format($cpumclo, 0, ',', '.'),
			'cpuarchitecture' => $cpuaddr,
			'cpucores' => $cpucore,
			'cputhreads' => $cpulogp,
			'cpusockettype' => $cpusock,
			'osname' => preg_replace('/[^a-zA-Z0-9 \.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\r\n]*/', '', $oscname),
			'osversion' => $osversi,
			'osservicepack' => preg_replace('/[^a-zA-Z0-9 \.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\r\n]*/', '', $osservp),
			'osserialnumber' => $ossernm,
			'manufacturer' => $cs_manufacturer,
			'model' => $cs_model,
			'domain' => $cs_domain,
			'totalprocesses' => $processes_total,
			'localdatetime' => $oscurtm,
			'lastbootuptime' => $oslastb,
			'uptime' => $osuptim,
			'memoryusage' => $ramuspc,
			'totalmemory' => number_format($totaram, 0, ',', '.'),
			'usedmemory' => number_format($ramused, 0, ',', '.'),
			'freememory' => number_format($freeram, 0, ',', '.'),
			'sysdiskuspc' => $dskuspc,
			'sysdiskfree' => $freedsk,
			'sysdisksize' => $totadsk,
			'sysdiskused' => $dskused,
			'sysdisktype' => $dskfisy,
			'services_total' => $services_total,
			'services_running' => $services_running,
			'services_error' => $services_error,
			'scheduler_total' => $scheduler_total,
			'scheduler_error' => $scheduler_error,
			'events_warning' => $events_warning,
			'events_error' => $events_error,
			'nagios_status' => $nagios_status,
			'nagiostotalcount' => $nagiostotalcount,
			'nagiosnormacount' => $nagiosnormacount,
			'nagioswarnicount' => $nagioswarnicount,
			'nagioscriticount' => $nagioscriticount,
			'nagiosunknocount' => $nagiosunknocount,
			'netstatestcount' => $netstatestcount,
			'netstatliscount' => $netstatliscount,
			'netstattimcount' => $netstattimcount,
			'netstatclocount' => $netstatclocount,
			'netstat_status' => $netstat_status,
			'inventory_status' => $inventory_status,
			'programs_status' => $programs_status,
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$xmlresponse = curl_exec($ch);
		if ($xmlresponse === false) {
			$xmlresponse = 'Error Connection';
			$agentstatus = curl_error($ch);
			$refreshrate = 15;
		} else {
			try {
				$xmlsrv = new SimpleXMLElement($xmlresponse);
				$agentstatus = $xmlsrv->connectionstatus;
				if ($xmlsrv->refreshrate != 'Hold') { $refreshrate = $xmlsrv->refreshrate / 1000; } else { $refreshrate = 120; }
				if ($cycle_count_status_limit != 1) { $refreshrate = 120; }
				$processes = $xmlsrv->processes;
				$services = $xmlsrv->services;
				$scheduler = $xmlsrv->scheduler;
				$events = $xmlsrv->events;
				$nagios = $xmlsrv->nagios;
				$programs = $xmlsrv->programs;
				$inventory = $xmlsrv->inventory;
				$execcount = $xmlsrv->execcount;
				$settings = $xmlsrv->settings;
			} catch(Exception $e) {
				$xmlresponse = 'Error Connection';
				$agentstatus = 'Disconnected';
				$refreshrate = 15;
			}
		}
		
		$fp = fopen($_SESSION['agentpath'] . '\\temp\\' . 'agent.status', 'w');
		fwrite($fp, $agentstatus);
		fclose($fp);
		
		if (strpos($agentstatus, 'Connection Success') > 0 && $refreshrate < 120) {
		
			// ### EXECUTE SERVER COMMAND ###
			if ($xmlsrv->exec == 'on') {
				if ($cycle_count == 1) { $clearcommands = 'on'; } else { $clearcommands = 'off'; }
				curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/exec.php');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				$data = array(
					'computername' => $cs_computername,
					'clearcommands' => $clearcommands,
				);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				$xmlexecresponse = curl_exec($ch);
				try { $xmlexec = new SimpleXMLElement($xmlexecresponse); } catch(Exception $e) { $xmlexec = 'Error Connection'; }
				foreach ($xmlexec->children() as $prop=>$node) {
					if ($xmlexec->$prop->auditok != 'null') { $auditok = base64_decode(base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($xmlexec->$prop->auditok)))))); } else { $auditok = ''; }
					if ($xmlexec->$prop->auditko != 'null') { $auditko = base64_decode(base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($xmlexec->$prop->auditko)))))); } else { $auditko = ''; }
					if ($xmlexec->$prop->auditnl != 'null') { $auditnl = base64_decode(base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($xmlexec->$prop->auditnl)))))); } else { $auditnl = ''; }
					$cid = $xmlexec->$prop->cid;
					$command = 	trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykeycmd, substr(base64_decode($xmlexec->$prop->command), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($xmlexec->$prop->command), 0, $iv_size)));
					$exectimeout = $xmlexec->$prop->timeout;
					pclose(popen('start "eurysco agent exec timeout" /b "' . $_SESSION['agentpath'] . '\\eurysco.agent.exec.timeout.exe" ' . $exectimeout . ' >nul 2>nul', 'r'));
					$execcmd = exec('"cmd.exe" /c "' . $command . '"', $errorarray, $errorlevel);
					sleep(1);
					$execcmdto = exec('taskkill.exe /f /im "eurysco.agent.exec.timeout.exe" /t >nul 2>nul', $errorarrayto, $errorlevelto);
					if ($errorlevelto == 0) { $execcmdmessage = '(exitcode ' . $errorlevel . ')'; } else { $errorlevel = -1; $execcmdmessage = '(command timeout)'; }
					if ($errorlevel == 0) {
	                    $audit = date('r') . $auditok;
					} else {
						if ($errorlevel == 1056) {
		                    $audit = date('r') . $auditnl;
						} else {
		                    $audit = date('r') . $auditko . ' ' . $execcmdmessage;
						}
					}
					$execdataref = '';
					if (strpos($auditok . $auditko . $auditnl, 'process control') > 0) { $execdataref = 'processes'; $cycle_count_processes = 1; }
					if (strpos($auditok . $auditko . $auditnl, 'service control') > 0) { $execdataref = 'services'; $cycle_count_services = 1; }
					if (strpos($auditok . $auditko . $auditnl, 'scheduled tasks') > 0) { $execdataref = 'scheduler'; $cycle_count_scheduler = 1; }
					if (strpos($auditok . $auditko . $auditnl, 'installed programs') > 0) { $execdataref = 'programs'; $cycle_count_programs = 1; }
					if ($execdataref != '') {
						@include($_SESSION['agentpath'] . '\\' . 'agent.' . $execdataref . '.php');
						if (@filesize($_SESSION['agentpath'] . '\\temp\\' . $execdataref . '.xml.gz') == 0) {
							@unlink($_SESSION['agentpath'] . '\\temp\\' . $execdataref . '.xml.gz');
						} else {
							curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/upload.php');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
							curl_setopt($ch, CURLOPT_POST, true);
							$data = array(
								'type' => 'nodes',
								'node' => $cs_computername,
								'file' => '@' . $_SESSION['agentpath'] . '\\temp\\' . $execdataref . '.xml.gz',
								'comp' => 'comp',
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_exec($ch);
						}
					}
					if ($audit != '') {
						curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/auditlog.php');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_POST, true);
						$data = array(
							'auditlog' => $audit,
							'cid' => $cid,
							'exitcode' => $errorlevel
						);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
						if (curl_exec($ch) === false) {
							$fp = fopen(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\audit\\audit-' . date('Ym') . '_' . date('M-Y') . '.log', 'a');
							fwrite($fp, $audit . "\r\n");
							fclose($fp);
							$auditsec = explode('     ', $audit);
							exec('eventcreate.exe /l "Application" /t INFORMATION /so "eurysco Audit : ' . $auditsec[1] . ' : ' . $auditsec[2] . '" /id 1 /d "' . str_replace('"', '\'', $audit) . '"', $errorarray, $errorlevel);
						}
					}
					sleep(3);
				}
			}
			
			if (strtolower($eurysco_serverconaddress) != 'https://' . strtolower($cs_computername)) {
			
				// ### CLEAN GROUPS INFO ###
				$activegroupcheck = 0;
				foreach ($xmlsrv->children() as $prop=>$node) {
					if ($prop == 'groups') {
						foreach ($xmlsrv->groups->children() as $group=>$node) {
							$activegroupcheck = $activegroupcheck . '|' . $xmlsrv->groups->$group->file . '|';
						}
					}
				}
				$grouplist = scandir(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\groups\\');
				foreach($grouplist as $group) {
					if(pathinfo($group)['extension'] == 'xml') {
						$groupxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\groups\\' . $group, true)))));
						if (hash('sha512', $groupxml->settings->groupname . 'Distributed') == $groupxml->settings->groupauth && !strpos(strtolower($activegroupcheck), strtolower($group))) {
							@unlink(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\groups\\' . $group);
						}
					}
				}

				// ### DOWNLOAD GROUPS INFO ###
				$checkdownload = 0;
				foreach ($xmlsrv->children() as $prop=>$node) {
					if ($prop == 'groups') {
						foreach ($xmlsrv->groups->children() as $group=>$node) {
							$checkdownload = 0;
							if (!file_exists(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\groups\\' . $xmlsrv->groups->$group->file)) {
								$checkdownload = 1;
							} else {
								if (@filesize(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\groups\\' . $xmlsrv->groups->$group->file) == 0) {
									@unlink(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\groups\\' . $xmlsrv->groups->$group->file);
									$checkdownload = 1;
								} else {
									if (hash_file('md2', str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\groups\\' . $xmlsrv->groups->$group->file) != $xmlsrv->groups->$group->hash) {
										$checkdownload = 1;
									}
								}
							}
							if ($checkdownload == 1) {
								$fp = fopen(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\groups\\' . $xmlsrv->groups->$group->file, 'w');
								curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/download.php');
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_POST, true);
								$data = array(
									'type' => 'groups',
									'download' => $xmlsrv->groups->$group->file
								);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
								curl_setopt($ch, CURLOPT_FILE, $fp);
								curl_exec($ch);
								fclose($fp);
							}
						}
					}
				}
			
				// ### CLEAN USERS INFO ###
				$activeusercheck = 0;
				foreach ($xmlsrv->children() as $prop=>$node) {
					if ($prop == 'users') {
						foreach ($xmlsrv->users->children() as $user=>$node) {
							$activeusercheck = $activeusercheck . '|' . $xmlsrv->users->$user->file . '|';
						}
					}
				}
				$userlist = scandir(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\');
				foreach($userlist as $user) {
					if(pathinfo($user)['extension'] == 'xml') {
						$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\' . $user, true)))));
						if (hash('sha512', $userxml->settings->username . 'Distributed') == $userxml->settings->userauth && !strpos(strtolower($activeusercheck), strtolower($user))) {
							@unlink(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\' . $user);
						}
					}
				}

				// ### DOWNLOAD USERS INFO ###
				$checkdownload = 0;
				foreach ($xmlsrv->children() as $prop=>$node) {
					if ($prop == 'users') {
						foreach ($xmlsrv->users->children() as $user=>$node) {
							$checkdownload = 0;
							if (!file_exists(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\' . $xmlsrv->users->$user->file)) {
								$checkdownload = 1;
							} else {
								if (@filesize(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\' . $xmlsrv->users->$user->file) == 0) {
									@unlink(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\' . $xmlsrv->users->$user->file);
									$checkdownload = 1;
								} else {
									if (hash_file('md2', str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\' . $xmlsrv->users->$user->file) != $xmlsrv->users->$user->hash) {
										$checkdownload = 1;
									}
								}
							}
							if ($checkdownload == 1) {
								$fp = fopen(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\' . $xmlsrv->users->$user->file, 'w');
								curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/download.php');
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_POST, true);
								$data = array(
									'type' => 'users',
									'download' => $xmlsrv->users->$user->file
								);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
								curl_setopt($ch, CURLOPT_FILE, $fp);
								curl_exec($ch);
								fclose($fp);
							}
						}
					}
				}
			}
		}
		
		if ($checkdownload == 1) {
			if (file_exists(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\Administrator.xml')) {
				if (hash_file('md2', str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\Administrator.xml') == '30a4ab68eb05a1e7f3e5c20df64a4260') {
					@unlink(str_replace('\\agent', '\\core', $_SESSION['agentpath']) . '\\users\\Administrator.xml');
				}
			}
		}
		
		// ### DOWNLOAD SETTINGS ###
		if (strtolower($eurysco_serverconaddress) != 'https://' . strtolower($cs_computername)) {
			if ($settings != '0null') {
				$fp = fopen($_SESSION['agentpath'] . '\\temp\\config_settings.xml', 'w');
				curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/download.php');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				$data = array(
					'type' => 'settings',
					'download' => 'config_settings.xml',
					'computername' => $cs_computername
				);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_exec($ch);
				fclose($fp);
				if ($settings == hash_file('md2', $_SESSION['agentpath'] . '\\temp\\config_settings.xml')) {
					@copy($_SESSION['agentpath'] . '\\temp\\config_settings.xml', str_replace('\\agent', '\\core\\conf\\config_settings.xml', $_SESSION['agentpath']));
					@copy($_SESSION['agentpath'] . '\\temp\\config_settings.xml', str_replace('\\agent', '\\server\\conf\\config_settings.xml', $_SESSION['agentpath']));
					@copy($_SESSION['agentpath'] . '\\temp\\config_settings.xml', $_SESSION['agentpath'] . '\\conf\\config_settings.xml');
				}
				@unlink($_SESSION['agentpath'] . '\\temp\\config_settings.xml');
			}
		}
		
		curl_close($ch);

	}
	
	foreach (get_defined_vars() as $key=>$val) {
		if ($key != 'pool' && $key != '_GET' && $key != '_POST' && $key != '_COOKIE' && $key != '_FILES' && $key != '_SERVER' && $key != '_SESSION' && $key != '_ENV' && $key != 'eurysco_serverconaddress' && $key != 'eurysco_serverconport' && $key != 'eurysco_serverconpassword' && $key != 'eurysco_sslverifyhost' && $key != 'eurysco_sslverifypeer' && $key != 'osversion' && $key != 'cpucount' && $key != 'cs_computername' && $key != 'cs_domain' && $key != 'cs_manufacturer' && $key != 'cs_model' && $key != 'wmi' && $key != 'winReg' && $key != 'agentversion' && $key != 'refreshrate' && $key != 'settings' && $key != 'config_settings' && $key != 'config_core' && $key != 'config_executor' && $key != 'iv_size' && $key != 'iv' && $key != 'mcrykey' && $key != 'mcrykeycmd' && $key != 'agentversion' && $key != 'refreshrate' && $key != 'cs_computername' && $key != 'core_port' && $key != 'executor_port' && $key != 'cpuload' && $key != 'cpumanu' && $key != 'cpuname' && $key != 'cpucclo' && $key != 'cpumclo' && $key != 'cpuaddr' && $key != 'cpucore' && $key != 'cpulogp' && $key != 'cpusock' && $key != 'oscname' && $key != 'osversi' && $key != 'osservp' && $key != 'ossernm' && $key != 'cs_manufacturer' && $key != 'cs_model' && $key != 'cs_domain' && $key != 'processes_total' && $key != 'oscurtm' && $key != 'oslastb' && $key != 'osuptim' && $key != 'ramuspc' && $key != 'totaram' && $key != 'ramused' && $key != 'freeram' && $key != 'dskuspc' && $key != 'freedsk' && $key != 'totadsk' && $key != 'dskused' && $key != 'dskfisy' && $key != 'services_total' && $key != 'services_running' && $key != 'services_error' && $key != 'scheduler_total' && $key != 'scheduler_error' && $key != 'events_warning' && $key != 'events_error' && $key != 'nagios_status' && $key != 'nagiostotalcount' && $key != 'nagiosnormacount' && $key != 'nagioswarnicount' && $key != 'nagioscriticount' && $key != 'nagiosunknocount' && $key != 'netstatestcount' && $key != 'netstatliscount' && $key != 'netstattimcount' && $key != 'netstatclocount' && $key != 'cycle_count' && $key != 'cycle_count_processes' && $key != 'cycle_count_services' && $key != 'cycle_count_scheduler' && $key != 'cycle_count_events' && $key != 'cycle_count_programs' && $key != 'cycle_count_inventory' && $key != 'cycle_count_nagios' && $key != 'cycle_count_netstat' && $key != 'agentstatus' && $key != 'processes' && $key != 'services' && $key != 'scheduler' && $key != 'nagios' && $key != 'events' && $key != 'inventory' && $key != 'netstat_status' && $key != 'inventory_status' && $key != 'programs_status' && $key != 'checkdownload') {
			$$key = null;
			unset($$key);
		}
	}

	flush();
	sleep($refreshrate);
	continue;

}

?>