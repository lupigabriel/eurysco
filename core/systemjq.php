<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/index.php') && !strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) { exit; }

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
session_write_close();


$systemi = '';

if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['systeminfo'] > 0) { 

$systemi = $systemi . '<br /><h2><img src="img/cpu.png" width="32" height="32" />&nbsp;Processor:</h2>';
$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_Processor");
foreach($wmisclass as $obj) {
	$cpuiden = '-';
	$cpuload = 0;
	$cpumanu = '-';
	$cpuname = '-';
	$cpucclo = 0;
	$cpumclo = 0;
	$cpuaddr = 0;
	$cpucore = 0;
	$cpulogp = 0;
	$cpusock = '-';
	$cpuiden = $obj->DeviceID;
	if ($obj->LoadPercentage == '' || is_null($obj->LoadPercentage)) { $cpuload = 0; } else { $cpuload = $obj->LoadPercentage; }
	$cpumanu = $obj->Manufacturer;
	$cpuname = $obj->Name;
	$cpucclo = $obj->CurrentClockSpeed;
	$cpumclo = $obj->MaxClockSpeed;
	$cpuaddr = $obj->AddressWidth;
	$cpucore = 1;
	$cpulogp = 1;
	foreach($obj->Properties_ as $wmiprop) {
		if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfCores|')) > -1) {
			$cpucore = $obj->NumberOfCores;
		}
		if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfLogicalProcessors|')) > -1) {
			$cpulogp = $obj->NumberOfLogicalProcessors;
		}
	}
	$cpusock = $obj->SocketDesignation;
	$systemi = $systemi . '<div class="progress-bar"><div class="bar bg-color-blue" style="width: ' . $cpuload . '%"></div></div>';
	$systemi = $systemi . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>CPU ' . (str_replace('CPU' , '', $cpuiden) + 1) . ' Usage:</strong></td><td width="70%" style="font-size:12px;"><strong>' . $cpuload . '%</strong></td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Manufacturer:</td><td width="70%" style="font-size:12px;">' . $cpumanu . '</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Model:</td><td width="70%" style="font-size:12px;">' . $cpuname . '</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Current Clock:</td><td width="70%" style="font-size:12px;">' . number_format($cpucclo, 0, ',', '.') . '&nbsp;Mhz</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Max Clock:</td><td width="70%" style="font-size:12px;">' . number_format($cpumclo, 0, ',', '.') . '&nbsp;Mhz</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Architecture:</td><td width="70%" style="font-size:12px;">' . $cpuaddr . '&nbsp;bit</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Cores:</td><td width="70%" style="font-size:12px;">' . $cpucore . '</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Threads:</td><td width="70%" style="font-size:12px;">' . $cpulogp . '</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Socket Type:</td><td width="70%" style="font-size:12px;">' . $cpusock . '</td></tr>';
	$systemi = $systemi . '</table>';
}

}

$wmisclass = $wmi->ExecQuery("SELECT Caption, CSDVersion, CSName, FreePhysicalMemory, LastBootUpTime, LocalDateTime, NumberOfProcesses, SerialNumber, TotalVisibleMemorySize, Version FROM Win32_OperatingSystem");
foreach($wmisclass as $obj) {
	$compnam = '-';
	$oscname = '-';
	$osservp = '-';
	$ossernm = '-';
	$ostotpr = '-';
	$oslastb = '-';
	$oscurtm = '-';
	$osuptim = 0;
	$firstsi = '';
	$compnam = $obj->CSName;
	$oscname = $obj->Caption;
	$osversi = $obj->Version;
	$osservp = $obj->CSDVersion;
	$ossernm = $obj->SerialNumber;
	$ostotpr = $obj->NumberOfProcesses;
	$oslastb = $obj->LastBootUpTime;
	$oscurtm = $obj->LocalDateTime;
	$nUXDate1 = strtotime(substr($oslastb, 0, 4) . '-' . substr($oslastb, 4, 2) . '-' . substr($oslastb, 6, 2) . ' ' . substr($oslastb, 8, 2) . ':' . substr($oslastb, 10, 2) . ':' . substr($oslastb, 12, 2));
	$nUXDate2 = strtotime(substr($oscurtm, 0, 4) . '-' . substr($oscurtm, 4, 2) . '-' . substr($oscurtm, 6, 2) . ' ' . substr($oscurtm, 8, 2) . ':' . substr($oscurtm, 10, 2) . ':' . substr($oscurtm, 12, 2));
	$osuptim = (gmdate('d', round(($nUXDate2 - $nUXDate1), 0)) - 1) . ' days ' . (gmdate('G', round(($nUXDate2 - $nUXDate1), 0))) . gmdate(':i:s', round(($nUXDate2 - $nUXDate1), 0));
	$oscodom = '';
	if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['systeminfo'] > 0) {
	$oscodom = '.' . strtoupper($_GET['domain']);
	}
	$firstsi = $firstsi . '<h2><img src="img/oswin.png" width="32" height="32" />&nbsp;' . str_replace('.WORKGROUP', '', strtoupper($compnam) . $oscodom) . ':</h2>';
	$firstsi = $firstsi . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>OS Name:</strong></td><td width="70%" style="font-size:12px;"><strong>' . $oscname . '</strong></td></tr>';
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">OS Version:</td><td width="70%" style="font-size:12px;">' . $osversi . '</td></tr>';
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">OS Service Pack:</td><td width="70%" style="font-size:12px;">' . $osservp . '</td></tr>';
	if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['systeminfo'] > 0) {
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">OS Serial Number:</td><td width="70%" style="font-size:12px;">' . $ossernm . '</td></tr>';
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Manufacturer:</td><td width="70%" style="font-size:12px;">' . $_GET['manufacturer'] . '</td></tr>';
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Model:</td><td width="70%" style="font-size:12px;">' . $_GET['model'] . '</td></tr>';
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Domain:</td><td width="70%" style="font-size:12px;">' . $_GET['domain'] . '</td></tr>';
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Total Processes:</td><td width="70%" style="font-size:12px;">' . $ostotpr . '</td></tr>';
	}
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Local Date Time:</td><td width="70%" style="font-size:12px;">' . $oscurtm = substr($oscurtm, 6, 2) . '/' . substr($oscurtm, 4, 2) . '/' . substr($oscurtm, 0, 4) . ' ' . substr($oscurtm, 8, 2) . ':' . substr($oscurtm, 10, 2) . ':' . substr($oscurtm, 12, 2) . '</td></tr>';
	if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['systeminfo'] > 0) {
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Last BootUp Time:</td><td width="70%" style="font-size:12px;">' . $oslastb = substr($oslastb, 6, 2) . '/' . substr($oslastb, 4, 2) . '/' . substr($oslastb, 0, 4) . ' ' . substr($oslastb, 8, 2) . ':' . substr($oslastb, 10, 2) . ':' . substr($oslastb, 12, 2) . '</td></tr>';
	$firstsi = $firstsi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Uptime:</td><td width="70%" style="font-size:12px;">' . $osuptim . '</td></tr>';
	}
	$firstsi = $firstsi . '</table>';

	$freeram = 0;
	$totaram = 0;
	$ramused = 0;
	$ramuspc = 0;
	$freeram = round($obj->FreePhysicalMemory / 1024, 0);
	$totaram = round($obj->TotalVisibleMemorySize / 1024, 0);
	$ramused = round($totaram - $freeram, 0);
	if (($totaram - $freeram) > 0) { $ramuspc = round($ramused * 100 / $totaram, 0); }
	$systemi = $firstsi . $systemi;
	
	if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['systeminfo'] > 0) { 

	$systemi = $systemi . '<br /><h2><img src="img/ram.png" width="32" height="32" />&nbsp;Physical Memory:</h2>';
	$systemi = $systemi . '<div class="progress-bar"><div class="bar bg-color-orange" style="width: ' . $ramuspc . '%"></div></div>';
	$systemi = $systemi . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Memory Usage:</strong></td><td width="70%" style="font-size:12px;"><strong>' . $ramuspc . '%</strong></td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Total Memory:</td><td width="70%" style="font-size:12px;">' . number_format($totaram, 0, ',', '.') . '&nbsp;MB</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Used Memory:</td><td width="70%" style="font-size:12px;">' . number_format($ramused, 0, ',', '.') . '&nbsp;MB</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Free Memory:</td><td width="70%" style="font-size:12px;">' . number_format($freeram, 0, ',', '.') . '&nbsp;MB</td></tr>';
	$systemi = $systemi . '</table>';
	
	}
}


if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['systeminfo'] > 0) { 

$systemi = $systemi . '<br /><h2><img src="img/hdd.png" width="32" height="32" />&nbsp;Local Disk:</h2>';
$wmisclass = $wmi->ExecQuery("SELECT DeviceID, DriveType, FileSystem, FreeSpace, Size FROM Win32_LogicalDisk WHERE DriveType = 3");
foreach($wmisclass as $obj) {
	$freedsk = 0;
	$totadsk = 0;
	$dskused = 0;
	$dskuspc = 0;
	$dsktype = '-';
	$dskfisy = '-';
	$dskcapt = '-';
	$freedsk = round($obj->FreeSpace / 1024 / 1024 / 1024, 0);
	$totadsk = round($obj->Size / 1024 / 1024 / 1024, 0);
	$dskused = round($totadsk - $freedsk, 0);
	if (($totadsk - $freedsk) > 0) { $dskuspc = round($dskused * 100 / $totadsk, 0); }
	$dskfisy = $obj->FileSystem;
	$dskcapt = $obj->DeviceID;
	$systemi = $systemi . '<div class="progress-bar"><div class="bar bg-color-greenLight" style="width: ' . $dskuspc . '%"></div></div>';
	$systemi = $systemi . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Disk ' . str_replace(':', '', $dskcapt) . ' Usage:</strong></td><td width="70%" style="font-size:12px;"><strong>' . $dskuspc . '%</strong></td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">File System:</td><td width="70%" style="font-size:12px;">' . $dskfisy . '</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Total Memory:</td><td width="70%" style="font-size:12px;">' . number_format($totadsk, 0, ',', '.') . '&nbsp;GB</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Used Memory:</td><td width="70%" style="font-size:12px;">' . number_format($dskused, 0, ',', '.') . '&nbsp;GB</td></tr>';
	$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">Free Memory:</td><td width="70%" style="font-size:12px;">' . number_format($freedsk, 0, ',', '.') . '&nbsp;GB</td></tr>';
	$systemi = $systemi . '</table>';
}



$systemi = $systemi . '<br /><h2><img src="img/uss.png" width="32" height="32" />&nbsp;User Sessions:</h2>';
$systemi = $systemi . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
$systemi = $systemi . '<tr><td width="30%" style="font-size:12px;"><strong>Username:</strong></td><td width="30%" style="font-size:12px;"><strong>Domain:</strong></td><td width="20%" style="font-size:12px;" align="center"><strong>Uptime:</strong></td><td width="20%" style="font-size:12px;" align="center"><strong>Start Time:</strong></td></tr>';
$usersessarray = array();
$usersesscounter = 0;
$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_LogonSession WHERE LogonType = 2 OR LogonType = 8 OR LogonType = 9 OR LogonType = 10 OR LogonType = 11 OR LogonType = 12");
foreach($wmisclass as $obj) {
	$wmilogonusr = $wmi->ExecQuery("Associators of {Win32_LogonSession.LogonId=" . $obj->LogonId . "} WHERE AssocClass=Win32_LoggedOnUser Role=Dependent");
	$wmilogtypev = '';
	if ($obj->LogonType == '2') { $wmilogtypev = 'Interactive'; }
	if ($obj->LogonType == '8') { $wmilogtypev = 'Network Cleartext'; }
	if ($obj->LogonType == '9') { $wmilogtypev = 'New Credentials'; }
	if ($obj->LogonType == '10') { $wmilogtypev = 'Remote Interactive'; }
	if ($obj->LogonType == '11') { $wmilogtypev = 'Cached Interactive'; }
	if ($obj->LogonType == '12') { $wmilogtypev = 'Cached Remote Interactive'; }
	$usersessarray[$usersesscounter][1] = $obj->LogonId;
	$usersessarray[$usersesscounter][0] = '';
	$usersessarray[$usersesscounter][2] = '';
	foreach($wmilogonusr as $logonusr) {
		$usersessarray[$usersesscounter][0] = '<div style="font-size:12px;" title="' . $wmilogtypev . '">' . $logonusr->Name . '</div>';
		$usersessarray[$usersesscounter][2] = $logonusr->Domain;
	}
	$ussstrtm = $obj->StartTime;
	$usersessarray[$usersesscounter][3] = substr($ussstrtm, 6, 2) . '/' . substr($ussstrtm, 4, 2) . '/' . substr($ussstrtm, 0, 4) . ' ' . substr($ussstrtm, 8, 2) . ':' . substr($ussstrtm, 10, 2) . ':' . substr($ussstrtm, 12, 2);
	$nUXDate1 = strtotime(substr($ussstrtm, 0, 4) . '-' . substr($ussstrtm, 4, 2) . '-' . substr($ussstrtm, 6, 2) . ' ' . substr($ussstrtm, 8, 2) . ':' . substr($ussstrtm, 10, 2) . ':' . substr($ussstrtm, 12, 2));
	$ussuptim = (((gmdate('d', round(($nUXDate2 - $nUXDate1), 0)) - 1) * 24) + gmdate('H', round(($nUXDate2 - $nUXDate1), 0))) . gmdate(':i:s', round(($nUXDate2 - $nUXDate1), 0));
	$usersessarray[$usersesscounter][4] = $ussuptim;
	$usersesscounter = $usersesscounter + 1;
}

sort($usersessarray);

foreach ($usersessarray as $usersessrow) {
	if (strlen($usersessrow[0]) > 0) {
		$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">' . $usersessrow[0] . '</td><td width="30%" style="font-size:12px;">' . $usersessrow[2] . '</td><td width="20%" style="font-size:12px;" align="center">' . $usersessrow[4] . '</td><td width="20%" style="font-size:12px;" align="center">' . $usersessrow[3] . '</td></tr>';
	}
}
$systemi = $systemi . '</table>';


}

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('systeminfo'=>utf8_encode($systemi),'totaltime'=>$totaltime));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>