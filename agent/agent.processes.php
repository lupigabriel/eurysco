<?php

$euryscoprocagentstatus = 0;

$strUser = new Variant('', VT_BSTR);
$processjarray = array();
$CommandLine = '';
$FileName = '';
$ExecutablePath = '';
$UserName = '';
$processjarray[0][1] = $CommandLine;
$processjarray[0][2] = $FileName;
$processjarray[0][3] = $ExecutablePath;
$processjarray[0][4] = $UserName;
$ProcessId = '-';
$colProcesses = $wmi->ExecQuery("SELECT * FROM Win32_Process WHERE ProcessId > 0");
foreach ($colProcesses as $objProcess) {
	$CommandLine = $objProcess->CommandLine;
	$FileName = $objProcess->Name;
	$ExecutablePath = $objProcess->ExecutablePath;
	$UserName = '-';
	try { $re_turn = $objProcess->GetOwner($strUser); } catch(Exception $e) { }
	if ($re_turn == 0) { $UserName = strtolower($strUser); }
	$ProcessId = $objProcess->ProcessId;
	$processjarray[$ProcessId][1] = $CommandLine;
	$processjarray[$ProcessId][2] = $FileName;
	$processjarray[$ProcessId][3] = $ExecutablePath;
	$processjarray[$ProcessId][4] = $UserName;
}

$wmiprocesses = $wmi->ExecQuery("SELECT * FROM Win32_PerfFormattedData_PerfProc_Process");
$processarray = array();
$procecounter = 0;
$idlecpu = 100;
$WorkingSetPrivatePropName = 'WorkingSetPrivate';
$checkprops = 1;

foreach($wmiprocesses as $process) {
	
	$IDProcess = '';
	$Name = '';
	$PercentProcessorTime = '';
	$WorkingSetPrivate = '';
	$CreatingProcessID = '';
	
	if ($checkprops != 0) {
		foreach($process->Properties_ as $wmiprop) {
			if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|WorkingSet|')) > -1) {
				$WorkingSetPrivatePropName = 'WorkingSet';
			}
			if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|WorkingSetPrivate|')) > -1) {
				$WorkingSetPrivatePropName = 'WorkingSetPrivate';
			}
		}
		$checkprops = 0;
	}
	$Name = $process->Name;
	$PercentProcessorTime = round(($process->PercentProcessorTime) / $cpucount, 0);
	$WorkingSetPrivate = number_format(($process->$WorkingSetPrivatePropName) / 1024, 0, ',', '.');
	$IDProcess = $process->IDProcess;
	$CreatingProcessID = $process->CreatingProcessID;
	$CommandLine = '';
	$FileName = '';
	$ExecutablePath = '';
	$UserName = '';
	if (array_key_exists($IDProcess, $processjarray)) {
		$CommandLine = str_replace('"', '\'', $processjarray[$IDProcess][1]);
		$FileName = $processjarray[$IDProcess][2];
		$ExecutablePath = $processjarray[$IDProcess][3];
		$UserName = $processjarray[$IDProcess][4];
	}
	
	if (strtolower($Name) == 'idle') {
		$processarray[0][0] = $IDProcess;
		$processarray[0][1] = $Name;
		$processarray[0][3] = $WorkingSetPrivate . ' KB';
		$processarray[0][4] = $CreatingProcessID;
		$processarray[0][5] = '';
		$processarray[0][6] = '';
		$processarray[0][7] = '';
		$processarray[0][8] = '';
	}
	
	if (strtolower($Name) != '_total' && strtolower($Name) != 'idle') {
		$idlecpu = $idlecpu - $PercentProcessorTime;
		$procecounter = $procecounter + 1;
		$processarray[$procecounter][0] = $IDProcess;
		$processarray[$procecounter][1] = $Name;
		$processarray[$procecounter][2] = $PercentProcessorTime . '%';
		$processarray[$procecounter][3] = $WorkingSetPrivate . ' KB';
		$processarray[$procecounter][4] = $CreatingProcessID;
		$processarray[$procecounter][5] = $CommandLine;
		$processarray[$procecounter][6] = $FileName;
		$processarray[$procecounter][7] = $ExecutablePath;
		$processarray[$procecounter][8] = $UserName;
		if ($Name == 'php_eurysco_agent') {
			$euryscoprocagentstatus = str_replace('.', '', $WorkingSetPrivate);
		}
	}
	
}

if ($idlecpu <= 0) {
	$processarray[0][2] = '0%';
} else {
	$processarray[0][2] = $idlecpu . '%';
}

$xml = '<processes>' . "\n";
foreach ($processarray as $processrow) {
	$xml = $xml . '	<id' . $processrow[0] . '>' . "\n";
	$xml = $xml . '		<IDProcess>' . $processrow[0] . '</IDProcess>';
	$xml = $xml . '<Name>' . urlencode($processrow[1]) . '</Name>';
	$xml = $xml . '<PercentProcessorTime>' . $processrow[2] . '</PercentProcessorTime>';
	$xml = $xml . '<WorkingSetPrivate>' . $processrow[3] . '</WorkingSetPrivate>';
	$xml = $xml . '<CreatingProcessID>' . $processrow[4] . '</CreatingProcessID>';
	$xml = $xml . '<CommandLine>' . urlencode($processrow[5]) . '</CommandLine>';
	$xml = $xml . '<FileName>' . urlencode($processrow[6]) . '</FileName>';
	$xml = $xml . '<ExecutablePath>' . urlencode($processrow[7]) . '</ExecutablePath>';
	$xml = $xml . '<UserName>' . $processrow[8] . '</UserName>' . "\n";
	$xml = $xml . '	</id' . $processrow[0] . '>' . "\n";
	$processes_total = $processes_total + 1;
}
$xml = $xml . '</processes>';
$zp = gzopen($_SESSION['agentpath'] . '\\temp\\processes.xml.gz', "w9");
gzwrite($zp, $xml);
gzclose($zp);

if ($euryscoprocagentstatus > 100000) { exec('sc.exe stop "euryscoAgent" & sc.exe start "euryscoAgent"', $errorarray, $errorlevel); }

?>