<?php

$wmiservices = $wmi->ExecQuery("SELECT DisplayName, ExitCode, Name, ProcessId, StartMode, StartName, State FROM Win32_Service");
$servicecounter = 0;

$xml = '<services>' . "\n";

foreach($wmiservices as $service) {

	$ProcessId = '';
	$Name = '';
	$DisplayName = '';
	$State = '';
	$StartMode = '';
	$ExitCode = '';
	$StartName = '';

	$ProcessId = $service->ProcessId;
	$Name = $service->Name;
	$DisplayName = $service->DisplayName;
	$State = $service->State;
	$StartMode = $service->StartMode;
	$ExitCode = $service->ExitCode;
	$StartName = $service->StartName;

	$servicecounter = $servicecounter + 1;
	
	if ($ProcessId != '') { $services_running = $services_running + 1; }
	if ($ExitCode <> 1077 && $ExitCode > 0) { $services_error = $services_error + 1; }
	
	$xml = $xml . '	<id' . $servicecounter . '>' . "\n";
	$xml = $xml . '		<DisplayName>' . urlencode($DisplayName) . '</DisplayName>';
	$xml = $xml . '<ProcessId>' . $ProcessId . '</ProcessId>';
	$xml = $xml . '<Name>' . urlencode($Name) . '</Name>';
	$xml = $xml . '<State>' . $State . '</State>';
	$xml = $xml . '<StartMode>' . $StartMode . '</StartMode>';
	$xml = $xml . '<ExitCode>' . $ExitCode . '</ExitCode>';
	$xml = $xml . '<StartName>' . urlencode(strtolower($StartName)) . '</StartName>' . "\n";
	$xml = $xml . '	</id' . $servicecounter . '>' . "\n";

}

$xml = $xml . '</services>';
$zp = gzopen($_SESSION['agentpath'] . '\\temp\\services.xml.gz', "w9");
gzwrite($zp, $xml);
gzclose($zp);

$services_total = $servicecounter;

?>