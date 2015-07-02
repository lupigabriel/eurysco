<?php

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
	$oscurtm = substr($oscurtm, 6, 2) . '/' . substr($oscurtm, 4, 2) . '/' . substr($oscurtm, 0, 4) . ' ' . substr($oscurtm, 8, 2) . ':' . substr($oscurtm, 10, 2) . ':' . substr($oscurtm, 12, 2);
	$oslastb = substr($oslastb, 6, 2) . '/' . substr($oslastb, 4, 2) . '/' . substr($oslastb, 0, 4) . ' ' . substr($oslastb, 8, 2) . ':' . substr($oslastb, 10, 2) . ':' . substr($oslastb, 12, 2);
	$osuptim = (((gmdate('d', round(($nUXDate2 - $nUXDate1), 0)) - 1) * 24) + gmdate('H', round(($nUXDate2 - $nUXDate1), 0))) . gmdate(':i:s', round(($nUXDate2 - $nUXDate1), 0));
	$freeram = 0;
	$totaram = 0;
	$ramused = 0;
	$ramuspc = 0;
	$freeram = round($obj->FreePhysicalMemory / 1024, 0);
	$totaram = round($obj->TotalVisibleMemorySize / 1024, 0);
	$ramused = round($totaram - $freeram, 0);
	if (($totaram - $freeram) > 0) { $ramuspc = round($ramused * 100 / $totaram, 0); }
}

$wmisclass = $wmi->ExecQuery("SELECT DeviceID, DriveType, FileSystem, FreeSpace, Size FROM Win32_LogicalDisk WHERE DeviceID = 'C:'");
foreach($wmisclass as $obj) {
	$freedsk = 0;
	$totadsk = 0;
	$dskused = 0;
	$dskuspc = 0;
	$dsktype = '-';
	$dskfisy = '-';
	$freedsk = number_format(round($obj->FreeSpace / 1024 / 1024 / 1024, 0), 0, ',', '.');
	$totadsk = number_format(round($obj->Size / 1024 / 1024 / 1024, 0), 0, ',', '.');
	$dskused = number_format(round($totadsk - $freedsk, 0), 0, ',', '.');
	if (($totadsk - $freedsk) > 0) { $dskuspc = round($dskused * 100 / $totadsk, 0); }
	$dskfisy = $obj->FileSystem;
}

?>