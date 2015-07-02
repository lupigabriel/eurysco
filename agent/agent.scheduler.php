<?php

$schtasks = $_SESSION['agentpath'] . '\\temp\\scheduler.csv';
if ($roottaskssetting == 'Enable') { $schtasksfilter = ' | findstr /v /r /c:%computername%...\\\\[^^\\"]*\\\\'; } else { $schtasksfilter = ''; }
$output = shell_exec('schtasks.exe /query /fo csv /v | find /i "%computername%"' . $schtasksfilter);
$fp = fopen($schtasks, 'w');
fwrite($fp, $output);
fclose($fp);
$file = file_get_contents($schtasks);
file_put_contents($schtasks, implode(PHP_EOL, file($schtasks, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
$output = fopen($schtasks, 'r');

$xml = '<scheduler>' . "\n";

while($column = fgetcsv($output, 4096, ',', '"')) {

	$scheduler_total = $scheduler_total + 1;
	
	if ($osversion > 5) {
		$HostName = $column[0];
		$TaskName = $column[1];
		$NextRunTime = $column[2];
		$RunAsUser = $column[14];
		$Status = $column[3];
		$LastResult = $column[6];
		if ($LastResult != 0 & $LastResult != 267009) { $scheduler_error = $scheduler_error + 1; }
		$ScheduledTaskState = $column[11];
		$LastRunTime = $column[5];
		$Creator = $column[7];
		$ScheduledType = $column[18];
		$TaskToRun = $column[8];
	} else {
		if (count($column) > 27) {
			$HostName = $column[0];
			$TaskName = $column[1];
			$NextRunTime = $column[2];
			$RunAsUser = $column[19];
			$Status = $column[3];
			$LastResult = $column[6];
			if ($LastResult != 0 & $LastResult != 267009) { $scheduler_error = $scheduler_error + 1; }
			$ScheduledTaskState = $column[12];
			$LastRunTime = $column[5];
			$Creator = $column[7];
			$ScheduledType = $column[13];
			$TaskToRun = $column[9];
		} else {
			$HostName = $column[0];
			$TaskName = $column[1];
			$NextRunTime = $column[2];
			$RunAsUser = $column[18];
			$Status = $column[3];
			$LastResult = $column[5];
			if ($LastResult != 0) { $scheduler_error = $scheduler_error + 1; }
			$ScheduledTaskState = $column[11];
			$LastRunTime = $column[4];
			$Creator = $column[6];
			$ScheduledType = $column[12];
			$TaskToRun = $column[8];
		}
	}
	
	$xml = $xml . '	<id' . $scheduler_total . '>' . "\n";
	$xml = $xml . '		<HostName>' . urlencode($HostName) . '</HostName>';
	$xml = $xml . '<TaskName>' . urlencode($TaskName) . '</TaskName>';
	$xml = $xml . '<NextRunTime>' . urlencode($NextRunTime) . '</NextRunTime>';
	$xml = $xml . '<RunAsUser>' . urlencode($RunAsUser) . '</RunAsUser>';
	$xml = $xml . '<Status>' . urlencode($Status) . '</Status>';
	$xml = $xml . '<LastResult>' . urlencode($LastResult) . '</LastResult>';
	$xml = $xml . '<ScheduledTaskState>' . urlencode($ScheduledTaskState) . '</ScheduledTaskState>';
	$xml = $xml . '<LastRunTime>' . urlencode($LastRunTime) . '</LastRunTime>';
	$xml = $xml . '<Creator>' . urlencode($Creator) . '</Creator>';
	$xml = $xml . '<ScheduledType>' . urlencode($ScheduledType) . '</ScheduledType>';
	$xml = $xml . '<TaskToRun>' . urlencode($TaskToRun) . '</TaskToRun>' . "\n";
	$xml = $xml . '	</id' . $scheduler_total . '>' . "\n";
		
}

fclose($output);

$xml = $xml . '</scheduler>';
$zp = gzopen($_SESSION['agentpath'] . '\\temp\\scheduler.xml.gz', "w9");
gzwrite($zp, $xml);
gzclose($zp);

?>