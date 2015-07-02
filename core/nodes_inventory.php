<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodessysteminventory'] > 0) {  } else { header('location: /'); exit; } ?>

<?php

if (isset($_GET['node'])) {
	$node = $_GET['node'];
} else {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (!strpos('#' . $_SESSION['nodelist'] . '#', '#' . $node . '#') || !isset($_SESSION['nodelist'])) {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (isset($_GET['domain'])) {
	$domain = $_GET['domain'];
} else {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (isset($_GET['computerip'])) {
	$computerip = $_GET['computerip'];
} else {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (isset($_GET['executorport'])) {
	$executorport = $_GET['executorport'];
} else {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

$nodepath = str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $node . '\\';

?>

<?php $_SESSION['nodes_inventory'] = '<a href="' . $_SERVER['REQUEST_URI'] . '" title="System Inventory"><div class="icon-box"></div>' . $node . '</a>'; ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php

$db = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoServer');
$db->busyTimeout(30000);
$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');

$lastupdate = 'N/A';
if (!is_null($db->querySingle('SELECT node FROM xmlInventory WHERE node = "' . $node . '"')) || file_exists($nodepath . 'inventory.xml.gz')) {
	$lastupdate = date('d/m/Y H:i:s', filemtime(str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $node . '\\inventory.xml.gz'));
	$xml = simplexml_load_string($db->querySingle('SELECT xml FROM xmlInventory WHERE node = "' . $node . '"'));
	if ($xml == '') {
		$fp = gzopen($nodepath . 'inventory.xml.gz', 'rb');
		$bl = '';
		while (!feof($fp)) {
			$gz = gzread($fp, 2048);
			$bl = $bl . $gz;
		}
		fclose($fp);
		$xml = simplexml_load_string($bl);
	}
} else {
	$lastupdate = '';
	if (!isset($_SESSION['nodes'])) { header('location: /nodes.php'); } else { header('location: ' . $_SESSION['nodes']); }
}

$db->close();

?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>System<small>inventory</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" class="eurysco-inventory-button big page-back"></a>
		</div>
	</div>
</div>

<br />

<div class="page" id="page-index">
	<div class="page-region">
		<div class="page-region-content">
			<div class="grid">
				<div class="row">
		            <div class="span1"></div>
					<div class="span10">
                    <h2 class="place-left"><?php echo $node; ?>:</h2>
					<div class="place-right" style="font-size:12px;"><a href="/xml.php?export=inventory&source=<?php echo $node; ?>" style="font-size:12px;" title="Export Source XML"><div class="icon-file-xml"></div></a>Last Update: <?php echo $lastupdate; ?></div>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="<?php echo $corelink; if (!isset($_SESSION['nodes'])) { echo '/nodes.php'; } else { echo $_SESSION['nodes']; } ?>" style="font-size:12px;" title="Return to Nodes Status"><div class="icon-feed"></div></a>&nbsp;Current Node:</div></td><td style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><?php if ($computerip != 'localhost') { ?><a href="https://<?php echo $computerip; ?>:<?php echo $executorport; ?>/inventory.php" style="font-size:13px;" target="_blank" title="Connect to System Inventory"><div class="icon-enter"></div></a>&nbsp;<?php } ?><?php echo $node . '.' . $domain; ?></td><?php if (file_exists($nodepath . 'nagios.xml.gz')) { ?><?php if (filesize($nodepath . 'nagios.xml.gz') > 40 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnagiosstatus'] > 0)) { ?><td width="5%" style="font-size:12px;"><a href="/nodes_nagios.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" style="color:#8063C8; font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Nagios Status">&nbsp;<img src="/img/nagios_normal.png" width="10" height="13" style="vertical-align: middle; margin-left: 2px; margin-right: 6px; margin-bottom: 2px;" title="Nagios Status" /></a></td><?php } ?><?php } ?><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'inventory.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodessysteminventory'] > 0)) { ?>href="/nodes_inventory.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="color:#8063C8; font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="System Inventory">&nbsp;<div class="icon-box"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'programs.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesinstalledprograms'] > 0)) { ?>href="/nodes_programs.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Installed Programs">&nbsp;<div class="icon-checkmark"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'processes.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesprocesscontrol'] > 0)) { ?>href="/nodes_processes.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Process Control">&nbsp;<div class="icon-bars"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'services.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesservicecontrol'] > 0)) { ?>href="/nodes_services.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Service Control">&nbsp;<div class="icon-cog"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'netstat.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnetworkstats'] > 0)) { ?>href="/nodes_netstat.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Network Stats">&nbsp;<div class="icon-tab"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'scheduler.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesscheduledtasks'] > 0)) { ?>href="/nodes_scheduler.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Scheduled Tasks">&nbsp;<div class="icon-calendar"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'events.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodeseventviewer'] > 0)) { ?>href="/nodes_eventviewer.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Events Viewer">&nbsp;<div class="icon-book"></div></a></td></tr>
					</table>
					</div>
					<div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li class="active">
							<a href="#" style="font-size:16px; color:#000;">Computer System:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									if ($xml->Computer_System->Computer_System_Caption != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $xml->Computer_System->Computer_System_Caption . '</strong></td></tr>'; }
									if ($xml->Computer_System->Computer_System_Domain != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Domain</td><td width="70%" style="font-size:12px;">' . $xml->Computer_System->Computer_System_Domain . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_DNSHostName != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">DNS HostName</td><td width="70%" style="font-size:12px;">' . $xml->Computer_System->Computer_System_DNSHostName . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_Manufacturer != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Manufacturer</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Computer_System->Computer_System_Manufacturer) . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_Model != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Model</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Computer_System->Computer_System_Model) . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_Description != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Description</td><td width="70%" style="font-size:12px;">' . $xml->Computer_System->Computer_System_Description . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_SystemType != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">System Type</td><td width="70%" style="font-size:12px;">' . $xml->Computer_System->Computer_System_SystemType . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_WakeUpType != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">WakeUp Type</td><td width="70%" style="font-size:12px;">' . $xml->Computer_System->Computer_System_WakeUpType . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_PrimaryOwnerName != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Primary OwnerName</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Computer_System->Computer_System_PrimaryOwnerName) . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_NumberOfProcessors != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Number of Processors</td><td width="70%" style="font-size:12px;">' . $xml->Computer_System->Computer_System_NumberOfProcessors . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_NumberOfLogicalProcessors != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Number of Logical Processors</td><td width="70%" style="font-size:12px;">' . $xml->Computer_System->Computer_System_NumberOfLogicalProcessors . '</td></tr>'; }
									if ($xml->Computer_System->Computer_System_TotalPhysicalMemory != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Total Physical Memory</td><td width="70%" style="font-size:12px;">' . $xml->Computer_System->Computer_System_TotalPhysicalMemory . '</td></tr>'; }
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li class="active">
							<a href="#" style="font-size:16px; color:#000;">Operating System:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									if ($xml->Operating_System->Operating_System_Caption != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . urldecode($xml->Operating_System->Operating_System_Caption) . '</strong></td></tr>'; }
									if ($xml->Operating_System->Operating_System_CSDVersion != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Service Pack</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_CSDVersion . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_Version != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Version</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_Version . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_Manufacturer != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Manufacturer</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_Manufacturer . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_OSArchitecture != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">OS Architecture</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_OSArchitecture . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_OSLanguage != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">OS Language</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_OSLanguage . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_SerialNumber != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Serial Number</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_SerialNumber . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_CSName != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Computer Name</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_CSName . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_WindowsDirectory != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">System Directory</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Operating_System->Operating_System_WindowsDirectory) . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_Organization != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Organization</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Operating_System->Operating_System_Organization) . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_RegisteredUser != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Registered User</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Operating_System->Operating_System_RegisteredUser) . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_CurrentTimeZone != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Current Time Zone</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_CurrentTimeZone . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_InstallDate != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Install Date</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_InstallDate . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_LastBootUpTime != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Last BootUp Time</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_LastBootUpTime . '</td></tr>'; }
									if ($xml->Operating_System->Operating_System_LocalDateTime != '') { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Local Date Time</td><td width="70%" style="font-size:12px;">' . $xml->Operating_System->Operating_System_LocalDateTime . '</td></tr>'; }
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li class="active">
							<a href="#" style="font-size:16px; color:#000;">Bios:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$bios_count = -1;
									foreach ($xml->Bios->children() as $prop=>$n) {
										if (strpos($prop . '|', ($bios_count + 1) . '|') > 0) { $bios_count = $bios_count + 1; }
										if ($prop == 'Bios_Name' . $bios_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $xml->Bios->$prop . '</strong></td></tr>'; }
										if ($prop == 'Bios_Manufacturer' . $bios_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Manufacturer</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Bios->$prop) . '</td></tr>'; }
										if ($prop == 'Bios_SMBIOSBIOSVersion' . $bios_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Version</td><td width="70%" style="font-size:12px;">' . $xml->Bios->$prop . '</td></tr>'; }
										if ($prop == 'Bios_ReleaseDate' . $bios_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Release Date</td><td width="70%" style="font-size:12px;">' . $xml->Bios->$prop . '</td></tr>'; }
										if ($prop == 'Bios_SerialNumber' . $bios_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Serial Number</td><td width="70%" style="font-size:12px;">' . $xml->Bios->$prop . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Processor:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$processor_count = -1;
									foreach ($xml->Processor->children() as $prop=>$n) {
										if (strpos($prop . '|', ($processor_count + 1) . '|') > 0) { $processor_count = $processor_count + 1; }
										if ($prop == 'Processor_Name' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . urldecode($xml->Processor->$prop) . '</strong></td></tr>'; }
										if ($prop == 'Processor_DeviceID' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Device ID</td><td width="70%" style="font-size:12px;">' . $xml->Processor->$prop . '</td></tr>'; }
										if ($prop == 'Processor_Manufacturer' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Manufacturer</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Processor->$prop) . '</td></tr>'; }
										if ($prop == 'Processor_Description' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Description</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Processor->$prop) . '</td></tr>'; }
										if ($prop == 'Processor_SocketDesignation' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Socket Type</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Processor->$prop) . '</td></tr>'; }
										if ($prop == 'Processor_AddressWidth' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Architecture</td><td width="70%" style="font-size:12px;">' . $xml->Processor->$prop . ' bit</td></tr>'; }
										if ($prop == 'Processor_MaxClockSpeed' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Max Clock Speed</td><td width="70%" style="font-size:12px;">' . $xml->Processor->$prop . '</td></tr>'; }
										if ($prop == 'Processor_ProcessorId' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Processor ID</td><td width="70%" style="font-size:12px;">' . $xml->Processor->$prop . '</td></tr>'; }
										if ($prop == 'Processor_NumberOfCores' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Number of Cores</td><td width="70%" style="font-size:12px;">' . $xml->Processor->$prop . '</td></tr>'; }
										if ($prop == 'Processor_NumberOfLogicalProcessors' . $processor_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Number of Logical Processors</td><td width="70%" style="font-size:12px;">' . $xml->Processor->$prop . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Video Controller:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$video_controller_count = -1;
									foreach ($xml->Video_Controller->children() as $prop=>$n) {
										if (strpos($prop . '|', ($video_controller_count + 1) . '|') > 0) { $video_controller_count = $video_controller_count + 1; }
										if ($prop == 'Video_Controller_Name' . $video_controller_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . urldecode($xml->Video_Controller->$prop) . '</strong></td></tr>'; }
										if ($prop == 'Video_Controller_DeviceID' . $video_controller_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Device ID</td><td width="70%" style="font-size:12px;">' . $xml->Video_Controller->$prop . '</td></tr>'; }
										if ($prop == 'Video_Controller_VideoProcessor' . $video_controller_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Video Processor</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Video_Controller->$prop) . '</td></tr>'; }
										if ($prop == 'Video_Controller_DriverVersion' . $video_controller_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Driver Version</td><td width="70%" style="font-size:12px;">' . $xml->Video_Controller->$prop . '</td></tr>'; }
										if ($prop == 'Video_Controller_InfFilename' . $video_controller_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Inf Filename</td><td width="70%" style="font-size:12px;">' . $xml->Video_Controller->$prop . '</td></tr>'; }
										if ($prop == 'Video_Controller_VideoModeDescription' . $video_controller_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Video Mode Description</td><td width="70%" style="font-size:12px;">' . $xml->Video_Controller->$prop . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Motherboard:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$baseboard_count = -1;
									foreach ($xml->Motherboard->children() as $prop=>$n) {
										if (strpos($prop . '|', ($baseboard_count + 1) . '|') > 0) { $baseboard_count = $baseboard_count + 1; }
										if ($prop == 'Motherboard_Manufacturer' . $baseboard_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . urldecode($xml->Motherboard->$prop) . '</strong></td></tr>'; }
										if ($prop == 'Motherboard_Product' . $baseboard_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Model</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Motherboard->$prop) . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Physical Memory:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$physical_memory_count = -1;
									foreach ($xml->Physical_Memory->children() as $prop=>$n) {
										if (strpos($prop . '|', ($physical_memory_count + 1) . '|') > 0) { $physical_memory_count = $physical_memory_count + 1; }
										if ($prop == 'Physical_Memory_DeviceLocator' . $physical_memory_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $xml->Physical_Memory->$prop . '</strong></td></tr>'; }
										if ($prop == 'Physical_Memory_BankLabel' . $physical_memory_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Bank Label</td><td width="70%" style="font-size:12px;">' . $xml->Physical_Memory->$prop . '</td></tr>'; }
										if ($prop == 'Physical_Memory_Capacity' . $physical_memory_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Capacity</td><td width="70%" style="font-size:12px;">' . $xml->Physical_Memory->$prop . '&nbsp;MB</td></tr>'; }
										if ($prop == 'Physical_Memory_Speed' . $physical_memory_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Speed</td><td width="70%" style="font-size:12px;">' . $xml->Physical_Memory->$prop . ' Mhz</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Logical Disk:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$logical_disk_count = -1;
									foreach ($xml->Logical_Disk->children() as $prop=>$n) {
										if (strpos($prop . '|', ($logical_disk_count + 1) . '|') > 0) { $logical_disk_count = $logical_disk_count + 1; }
										if ($prop == 'Logical_Disk_Name' . $logical_disk_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $xml->Logical_Disk->$prop . '</strong></td></tr>'; }
										if ($prop == 'Logical_Disk_Description' . $logical_disk_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Type</td><td width="70%" style="font-size:12px;">' . $xml->Logical_Disk->$prop . '</td></tr>'; }
										if ($prop == 'Logical_Disk_FileSystem' . $logical_disk_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">File System</td><td width="70%" style="font-size:12px;">' . $xml->Logical_Disk->$prop . '</td></tr>'; }
										if ($prop == 'Logical_Disk_Size' . $logical_disk_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Size</td><td width="70%" style="font-size:12px;">' . $xml->Logical_Disk->$prop . '</td></tr>'; }
										if ($prop == 'Logical_Disk_FreeSpace' . $logical_disk_count) {	echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Free Space</td><td width="70%" style="font-size:12px;">' . $xml->Logical_Disk->$prop . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Network Configuration:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$network_configuration_count = -1;
									foreach ($xml->Network_Configuration->children() as $prop=>$n) {
										if (strpos($prop . '|', ($network_configuration_count + 1) . '|') > 0) { $network_configuration_count = $network_configuration_count + 1; }
										if ($prop == 'Network_Configuration_Description' . $network_configuration_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . urldecode($xml->Network_Configuration->$prop) . '</strong></td></tr>'; }
										if ($prop == 'Network_Configuration_MACAddress' . $network_configuration_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">MAC Address</td><td width="70%" style="font-size:12px;">' . $xml->Network_Configuration->$prop . '</td></tr>'; }
										if ($prop == 'Network_Configuration_IPAddress' . $network_configuration_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">IP Address</td><td width="70%" style="font-size:12px;">' . str_replace(' ', '&nbsp;', $xml->Network_Configuration->$prop) . '</td></tr>'; }
										if ($prop == 'Network_Configuration_IPSubnet' . $network_configuration_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">IP Subnet</td><td width="70%" style="font-size:12px;">' . $xml->Network_Configuration->$prop . '</td></tr>'; }
										if ($prop == 'Network_Configuration_DefaultIPGateway' . $network_configuration_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">IP Gateway</td><td width="70%" style="font-size:12px;">' . $xml->Network_Configuration->$prop . '</td></tr>'; }
										if ($prop == 'Network_Configuration_DNSServerSearchOrder' . $network_configuration_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">DNS Server</td><td width="70%" style="font-size:12px;">' . $xml->Network_Configuration->$prop . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Drivers:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$driver_count = -1;
									foreach ($xml->Drivers->children() as $prop=>$n) {
										if (strpos($prop . '|', ($driver_count + 1) . '|') > 0) { $driver_count = $driver_count + 1; }
										if ($prop == 'Driver_DeviceName' . $driver_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Device Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . urldecode($xml->Drivers->$prop) . '</strong></td></tr>'; }
										if ($prop == 'Driver_DriverVersion' . $driver_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Driver Version</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Drivers->$prop) . '</td></tr>'; }
										if ($prop == 'Driver_FriendlyName' . $driver_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Friendly Name</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Drivers->$prop) . '</td></tr>'; }
										if ($prop == 'Driver_DriverProviderName' . $driver_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Provider Name</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Drivers->$prop) . '</td></tr>'; }
										if ($prop == 'Driver_DeviceClass' . $driver_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Device Class</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Drivers->$prop) . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Printers:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$printer_count = -1;
									foreach ($xml->Printer->children() as $prop=>$n) {
										if (strpos($prop . '|', ($printer_count + 1) . '|') > 0) { $printer_count = $printer_count + 1; }
										if ($prop == 'Printer_Name' . $printer_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . urldecode($xml->Printer->$prop) . '</strong></td></tr>'; }
										if ($prop == 'Printer_DriverName' . $printer_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Driver Name</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Printer->$prop) . '</td></tr>'; }
										if ($prop == 'Printer_PortName' . $printer_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Port Name</td><td width="70%" style="font-size:12px;">' . $xml->Printer->$prop . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Users:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$users_count = -1;
									foreach ($xml->Users->children() as $prop=>$n) {
										if (strpos($prop . '|', ($users_count + 1) . '|') > 0) { $users_count = $users_count + 1; }
										if ($prop == 'User_Name' . $users_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $xml->Users->$prop . '</strong></td></tr>'; }
										if ($prop == 'User_Group' . $users_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Group</td><td width="70%" style="font-size:12px;">' . $xml->Users->$prop . '</td></tr>'; }
										if ($prop == 'User_Domain' . $users_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Domain</td><td width="70%" style="font-size:12px;">' . $xml->Users->$prop . '</td></tr>'; }
										if ($prop == 'User_Type' . $users_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Type</td><td width="70%" style="font-size:12px;">' . $xml->Users->$prop . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Profiles:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$profiles_count = -1;
									foreach ($xml->Profiles->children() as $prop=>$n) {
										if (strpos($prop . '|', ($profiles_count + 1) . '|') > 0) { $profiles_count = $profiles_count + 1; }
										if ($prop == 'Profile_Name' . $profiles_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . urldecode($xml->Profiles->$prop) . '</strong></td></tr>'; }
										if ($prop == 'Profile_LastUseTime' . $profiles_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Last Use</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Profiles->$prop) . '</td></tr>'; }
										if ($prop == 'Profile_SID' . $profiles_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Status</td><td width="70%" style="font-size:12px;">' . $xml->Profiles->$prop . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Shares:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$users_count = -1;
									foreach ($xml->Shares->children() as $prop=>$n) {
										if (strpos($prop . '|', ($users_count + 1) . '|') > 0) { $users_count = $users_count + 1; }
										if ($prop == 'Share_Name' . $users_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . urldecode($xml->Shares->$prop) . '</strong></td></tr>'; }
										if ($prop == 'Share_Description' . $users_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Description</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Shares->$prop) . '</td></tr>'; }
										if ($prop == 'Share_Status' . $users_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Status</td><td width="70%" style="font-size:12px;">' . $xml->Shares->$prop . '</td></tr>'; }
										if ($prop == 'Share_Path' . $users_count) { echo '<tr class="rowselectsrv"><td width="30%" style="font-size:12px;">Path</td><td width="70%" style="font-size:12px;">' . urldecode($xml->Shares->$prop) . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>