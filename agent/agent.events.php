<?php

$xml = '<events>' . "\n";

$eventviewercount = 0;
$wmisclass = $wmi->ExecQuery("SELECT ComputerName, EventCode, EventIdentifier, EventType, Logfile, Message, RecordNumber, SourceName, TimeGenerated, Type, User FROM Win32_NTLogEvent WHERE Logfile = 'System'");
foreach($wmisclass as $obj) {

	$ComputerName = '';
	$EventCode = '';
	$EventIdentifier = '';
	$Type = '';
	$EventType = '';
	$Logfile = '';
	$Message = '';
	$RecordNumber = '';
	$SourceName = '';
	$TimeGenerated = '';
	$Type = '';
	$User = '';
	$ComputerName = $obj->ComputerName;
	$EventCode = $obj->EventCode;
	$EventIdentifier = $obj->EventIdentifier;
	$Type = $obj->Type;
	$EventType = $obj->EventType;
	$Logfile = $obj->Logfile;
	$Message = $obj->Message;
	$RecordNumber = $obj->RecordNumber;
	$SourceName = $obj->SourceName;
	$TimeGenerated = $obj->TimeGenerated;
	$utc_date = DateTime::createFromFormat('Y-m-d H:i:s', substr($TimeGenerated, 0, 4) . '-' . substr($TimeGenerated, 4, 2) . '-' . substr($TimeGenerated, 6, 2) . ' ' . substr($TimeGenerated, 8, 2) . ':' . substr($TimeGenerated, 10, 2) . ':' . substr($TimeGenerated, 12, 2), new DateTimeZone('UTC'));
	$nyc_date = $utc_date;
	if (substr($obj->TimeGenerated, 22, 3) == 000) { $nyc_date->setTimeZone(new DateTimeZone($timezonesetting)); }
	$TimeGenForm = $nyc_date->format('d/m/Y H:i:s');
	$User = $obj->User;

	$xml = $xml . '	<id' . $RecordNumber . '>' . "\n";
	$xml = $xml . '		<TimeGenerated>' . $TimeGenForm . '</TimeGenerated>';
	$xml = $xml . '<EventCode>' . $EventCode . '</EventCode>';
	$xml = $xml . '<EventIdentifier>' . $EventIdentifier . '</EventIdentifier>';
	$xml = $xml . '<Type>' . $Type . '</Type>';
	$xml = $xml . '<EventType>' . $EventType . '</EventType>';
	$xml = $xml . '<Logfile>' . $Logfile . '</Logfile>';
	$xml = $xml . '<Message>' . urlencode($Message) . '</Message>';
	$xml = $xml . '<RecordNumber>' . $RecordNumber . '</RecordNumber>';
	$xml = $xml . '<SourceName>' . urlencode($SourceName) . '</SourceName>';
	$xml = $xml . '<ComputerName>' . $ComputerName . '</ComputerName>';
	$xml = $xml . '<User>' . urlencode($User) . '</User>' . "\n";
	$xml = $xml . '	</id' . $RecordNumber . '>' . "\n";
	
	if ($obj->EventType == 2) { $events_warning = $events_warning + 1; }
	if ($obj->EventType == 1) { $events_error = $events_error + 1; }
	
	$eventviewercount = $eventviewercount + 1;
	if ($eventviewercount > 249) { break; }

}

$eventviewercount = 0;
$wmisclass = $wmi->ExecQuery("SELECT ComputerName, EventCode, EventIdentifier, EventType, Logfile, Message, RecordNumber, SourceName, TimeGenerated, Type, User FROM Win32_NTLogEvent WHERE Logfile = 'Application'");
foreach($wmisclass as $obj) {

	$ComputerName = '';
	$EventCode = '';
	$EventIdentifier = '';
	$Type = '';
	$EventType = '';
	$Logfile = '';
	$Message = '';
	$RecordNumber = '';
	$SourceName = '';
	$TimeGenerated = '';
	$Type = '';
	$User = '';
	$ComputerName = $obj->ComputerName;
	$EventCode = $obj->EventCode;
	$EventIdentifier = $obj->EventIdentifier;
	$Type = $obj->Type;
	$EventType = $obj->EventType;
	$Logfile = $obj->Logfile;
	$Message = $obj->Message;
	$RecordNumber = $obj->RecordNumber;
	$SourceName = $obj->SourceName;
	$TimeGenerated = $obj->TimeGenerated;
	$utc_date = DateTime::createFromFormat('Y-m-d H:i:s', substr($TimeGenerated, 0, 4) . '-' . substr($TimeGenerated, 4, 2) . '-' . substr($TimeGenerated, 6, 2) . ' ' . substr($TimeGenerated, 8, 2) . ':' . substr($TimeGenerated, 10, 2) . ':' . substr($TimeGenerated, 12, 2), new DateTimeZone('UTC'));
	$nyc_date = $utc_date;
	if (substr($obj->TimeGenerated, 22, 3) == 000) { $nyc_date->setTimeZone(new DateTimeZone($timezonesetting)); }
	$TimeGenForm = $nyc_date->format('d/m/Y H:i:s');
	$User = $obj->User;

	$xml = $xml . '	<id' . $RecordNumber . '>' . "\n";
	$xml = $xml . '		<TimeGenerated>' . $TimeGenForm . '</TimeGenerated>';
	$xml = $xml . '<EventCode>' . $EventCode . '</EventCode>';
	$xml = $xml . '<EventIdentifier>' . $EventIdentifier . '</EventIdentifier>';
	$xml = $xml . '<Type>' . $Type . '</Type>';
	$xml = $xml . '<EventType>' . $EventType . '</EventType>';
	$xml = $xml . '<Logfile>' . $Logfile . '</Logfile>';
	$xml = $xml . '<Message>' . urlencode($Message) . '</Message>';
	$xml = $xml . '<RecordNumber>' . $RecordNumber . '</RecordNumber>';
	$xml = $xml . '<SourceName>' . urlencode($SourceName) . '</SourceName>';
	$xml = $xml . '<ComputerName>' . $ComputerName . '</ComputerName>';
	$xml = $xml . '<User>' . urlencode($User) . '</User>' . "\n";
	$xml = $xml . '	</id' . $RecordNumber . '>' . "\n";
	
	if ($obj->EventType == 2) { $events_warning = $events_warning + 1; }
	if ($obj->EventType == 1) { $events_error = $events_error + 1; }
	
	$eventviewercount = $eventviewercount + 1;
	if ($eventviewercount > 249) { break; }

}

$eventviewercount = 0;
$wmisclass = $wmi->ExecQuery("SELECT ComputerName, EventCode, EventIdentifier, EventType, Logfile, Message, RecordNumber, SourceName, TimeGenerated, Type, User FROM Win32_NTLogEvent WHERE Logfile = 'Security'");
foreach($wmisclass as $obj) {

	$ComputerName = '';
	$EventCode = '';
	$EventIdentifier = '';
	$Type = '';
	$EventType = '';
	$Logfile = '';
	$Message = '';
	$RecordNumber = '';
	$SourceName = '';
	$TimeGenerated = '';
	$Type = '';
	$User = '';
	$ComputerName = $obj->ComputerName;
	$EventCode = $obj->EventCode;
	$EventIdentifier = $obj->EventIdentifier;
	$Type = $obj->Type;
	$EventType = $obj->EventType;
	$Logfile = $obj->Logfile;
	$Message = $obj->Message;
	$RecordNumber = $obj->RecordNumber;
	$SourceName = $obj->SourceName;
	$TimeGenerated = $obj->TimeGenerated;
	$utc_date = DateTime::createFromFormat('Y-m-d H:i:s', substr($TimeGenerated, 0, 4) . '-' . substr($TimeGenerated, 4, 2) . '-' . substr($TimeGenerated, 6, 2) . ' ' . substr($TimeGenerated, 8, 2) . ':' . substr($TimeGenerated, 10, 2) . ':' . substr($TimeGenerated, 12, 2), new DateTimeZone('UTC'));
	$nyc_date = $utc_date;
	if (substr($obj->TimeGenerated, 22, 3) == 000) { $nyc_date->setTimeZone(new DateTimeZone($timezonesetting)); }
	$TimeGenForm = $nyc_date->format('d/m/Y H:i:s');
	$User = $obj->User;

	$xml = $xml . '	<id' . $RecordNumber . '>' . "\n";
	$xml = $xml . '		<TimeGenerated>' . $TimeGenForm . '</TimeGenerated>';
	$xml = $xml . '<EventCode>' . $EventCode . '</EventCode>';
	$xml = $xml . '<EventIdentifier>' . $EventIdentifier . '</EventIdentifier>';
	$xml = $xml . '<Type>' . $Type . '</Type>';
	$xml = $xml . '<EventType>' . $EventType . '</EventType>';
	$xml = $xml . '<Logfile>' . $Logfile . '</Logfile>';
	$xml = $xml . '<Message>' . urlencode($Message) . '</Message>';
	$xml = $xml . '<RecordNumber>' . $RecordNumber . '</RecordNumber>';
	$xml = $xml . '<SourceName>' . urlencode($SourceName) . '</SourceName>';
	$xml = $xml . '<ComputerName>' . $ComputerName . '</ComputerName>';
	$xml = $xml . '<User>' . urlencode($User) . '</User>' . "\n";
	$xml = $xml . '	</id' . $RecordNumber . '>' . "\n";
	
	if ($obj->EventType == 5) { $events_error = $events_error + 1; }
	
	$eventviewercount = $eventviewercount + 1;
	if ($eventviewercount > 249) { break; }

}

$xml = $xml . '</events>';
$zp = gzopen($_SESSION['agentpath'] . '\\temp\\events.xml.gz', "w9");
gzwrite($zp, $xml);
gzclose($zp);

?>