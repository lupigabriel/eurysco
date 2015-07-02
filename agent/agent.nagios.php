<?php

$nagioscommandname = '';
$nagioscommandstrn = '';
$nagioscommandmsgs = '';
$nagioscommandexit = 0;
$nagiostotalcount = 0;
$nagiosnormacount = 0;
$nagioswarnicount = 0;
$nagioscriticount = 0;
$nagiosunknocount = 0;
$nagios_status = -1;

$xml = '<nagios>' . "\n";

$wminagsrv = $wmi->ExecQuery("SELECT Name, PathName FROM Win32_Service WHERE (Name = 'NRPE_NT' AND State = 'Running') OR (Name = 'nscp' AND State = 'Running')");

$nrpepathname = '';
$nscppathname = '';
foreach($wminagsrv as $nagsrv) {
	if ($nagsrv->Name == 'NRPE_NT') {
		$nrpepathname = $nagsrv->PathName;
	}
	if ($nagsrv->Name == 'nscp') {
		$nscppathname = $nagsrv->PathName;
	}
}


$NrpePath = preg_replace('/nrpe_nt.exe.*/i', '', $nrpepathname);
if ($nrpepathname != '' && file_exists($NrpePath . 'nrpe_nt.exe') && file_exists($NrpePath . 'nrpe.cfg') && is_readable($NrpePath . 'nrpe.cfg')) {
	
	$filearr = file($NrpePath . 'nrpe.cfg');
	$lastlines = array_slice($filearr, -1000);

	foreach ($lastlines as $lastline) {
		if (preg_match('/command\[/i', $lastline) && !preg_match('/#.*command\[/i', $lastline)) {
			$nagioscommandstrn = preg_replace('/[\n\r]/', '', preg_replace('/.*=/', '', $lastline));
			if (strpos($nagioscommandstrn, ':\\') > 0) {
				if (!file_exists(preg_replace('/\.exe.*/', '.exe', $nagioscommandstrn))) {
					$nagioscommandstrn = '';
				}
			} else {
				if (file_exists(preg_replace('/\.exe.*/', '.exe', $NrpePath . $nagioscommandstrn))) {
					$nagioscommandstrn = $NrpePath . $nagioscommandstrn;
				} else {
					$nagioscommandstrn = '';
				}
			}
			if ($nagioscommandstrn != '') {
				
				$nagioscommandname = preg_replace('/.*\[/', '', preg_replace('/\].*/', '', $lastline));
				$nagioscommandmsgs = exec($nagioscommandstrn, $errorarray, $nagioscommandexit);
				if ($nagioscommandexit == 0) { $nagiosnormacount = $nagiosnormacount + 1; }
				if ($nagioscommandexit == 2) { $nagioscriticount = $nagioscriticount + 1; }
				if ($nagioscommandexit == 1 || $nagioscommandexit > 3 || $nagioscommandexit < 0) { $nagioswarnicount = $nagioswarnicount + 1; }
				if ($nagioscommandexit == 3) { $nagiosunknocount = $nagiosunknocount + 1; }
				$nagiostotalcount = $nagiostotalcount + 1;
				
				$xml = $xml . '	<id' . $nagiostotalcount . '>' . "\n";
				$xml = $xml . '		<NagiosCommandName>' . urlencode($nagioscommandname) . '</NagiosCommandName>';
				$xml = $xml . '<NagiosCommandStrn>' . urlencode($nagioscommandstrn) . '</NagiosCommandStrn>';
				$xml = $xml . '<NagiosCommandMsgs>' . urlencode($nagioscommandmsgs) . '</NagiosCommandMsgs>';
				$xml = $xml . '<NagiosCommandExit>' . $nagioscommandexit . '</NagiosCommandExit>' . "\n";
				$xml = $xml . '	</id' . $nagiostotalcount . '>' . "\n";
				
			}
		}
	}

}

$NscpPath = str_replace('"', '', preg_replace('/nscp.exe.*/i', '', $nscppathname));
if ($nscppathname != '' && file_exists($NscpPath . 'nscp.exe') && file_exists($NscpPath . 'nsclient.ini') && is_readable($NscpPath . 'nsclient.ini')) {

	$filearr = file($NscpPath . 'nsclient.ini');
	$lastlines = array_slice($filearr, -1000);
	
	$checkinitalias = 0;
	foreach ($lastlines as $lastline) {
		if (preg_match('/\[.*alias.*\]/i', $lastline)) { $checkinitalias = 1; }
		if ($checkinitalias == 1 && preg_match('/=/i', $lastline) && !preg_match('/;/i', $lastline)) {
			$nagioscommandstrn = trim(preg_replace('/.*@\|@/', '', preg_replace('/=/', '@|@', $lastline, 1)));
			$nagioscommandname = trim(preg_replace('/@\|@.*/', '', preg_replace('/=/', '@|@', $lastline, 1)));

			if ($nagioscommandname != '' && $nagioscommandstrn != '') {
			
				$nagioscommandmsgs = exec('"' . $NscpPath . 'nscp.exe" client --query ' . $nagioscommandname, $errorarray, $nagioscommandexit);
				if ($nagioscommandexit == 0) { $nagiosnormacount = $nagiosnormacount + 1; }
				if ($nagioscommandexit == 2) { $nagioscriticount = $nagioscriticount + 1; }
				if ($nagioscommandexit == 1 || $nagioscommandexit > 3 || $nagioscommandexit < 0) { $nagioswarnicount = $nagioswarnicount + 1; }
				if ($nagioscommandexit == 3) { $nagiosunknocount = $nagiosunknocount + 1; }
				$nagiostotalcount = $nagiostotalcount + 1;
				
				$xml = $xml . '	<id' . $nagiostotalcount . '>' . "\n";
				$xml = $xml . '		<NagiosCommandName>' . urlencode($nagioscommandname) . '</NagiosCommandName>';
				$xml = $xml . '<NagiosCommandStrn>' . urlencode($nagioscommandstrn) . '</NagiosCommandStrn>';
				$xml = $xml . '<NagiosCommandMsgs>' . urlencode($nagioscommandmsgs) . '</NagiosCommandMsgs>';
				$xml = $xml . '<NagiosCommandExit>' . $nagioscommandexit . '</NagiosCommandExit>' . "\n";
				$xml = $xml . '	</id' . $nagiostotalcount . '>' . "\n";
				
			}
		}
	}

}


if ($nagiosnormacount > 0) { $nagios_status = 0; }
if ($nagiosunknocount > 0) { $nagios_status = 3; }
if ($nagioswarnicount > 0) { $nagios_status = 1; }
if ($nagioscriticount > 0) { $nagios_status = 2; }

$xml = $xml . '</nagios>';
$zp = gzopen($_SESSION['agentpath'] . '\\temp\\nagios.xml.gz', "w9");
gzwrite($zp, $xml);
gzclose($zp);

?>