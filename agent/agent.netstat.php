<?php

$netstatestcount = 0;
$netstatliscount = 0;
$netstattimcount = 0;
$netstatclocount = 0;

$netstatprerarray = array();
$netstatprecounter = 0;

$xml = '<netstat>' . "\n";

$output = shell_exec('netstat.exe -abno');
$netstats = preg_split("/\r\n|\n|\r/", $output);

foreach($netstats as $netstat) {

	$netstatprerarray[$netstatprecounter][0] = '';
	$netstatprerarray[$netstatprecounter][1] = '';
	$netstatprerarray[$netstatprecounter][2] = '';
	$netstatprerarray[$netstatprecounter][3] = '';
	$netstatprerarray[$netstatprecounter][4] = '';
	$netstatprerarray[$netstatprecounter][5] = '';
	$netstatprerarray[$netstatprecounter][6] = '';
	
	if (preg_match('/:/', $netstat) && !preg_match('/\\\/', $netstat)) {
		$netstatfl = split('  ', $netstat);
		$flcount = 1;
		foreach($netstatfl as $netstatflc) {
			if (trim($netstatflc) != '') {
				if ($flcount == 1) { $netstatprerarray[$netstatprecounter][0] = trim($netstatflc); }
				if ($flcount == 2) { $netstatprerarray[$netstatprecounter][1] = trim($netstatflc); }
				if ($flcount == 3) { $netstatprerarray[$netstatprecounter][2] = trim($netstatflc); }
				if ($flcount == 4) { if (!is_numeric(trim($netstatflc))) { $netstatprerarray[$netstatprecounter][3] = trim($netstatflc); } else { $netstatprerarray[$netstatprecounter][4] = trim($netstatflc); } }
				if ($flcount == 5) { $netstatprerarray[$netstatprecounter][4] = trim($netstatflc); }
				$flcount = $flcount + 1;
			}			
		}
		$netstatprecounter = $netstatprecounter + 1;
	}
	
	if (!preg_match('/:/', trim($netstat)) && !preg_match('/\\\/', trim($netstat)) && !preg_match('/\./', trim($netstat)) && !preg_match('/ /', trim($netstat)) && trim($netstat) != '') {
		$netstatprerarray[$netstatprecounter - 1][5] = trim($netstat);
	}
	
	if (!preg_match('/:/', trim($netstat)) && !preg_match('/\\\/', trim($netstat)) && preg_match('/\[.*\]/', trim($netstat)) && trim($netstat) != '') {
		$netstatprerarray[$netstatprecounter - 1][6] = substr(trim($netstat), 1, -1);
	}
	
}

$netstatrarray = array();
$netstatcounter = 0;
	
foreach ($netstatprerarray as $netstatprerow) {

	$Protocol = $netstatprerow[0];
	$LocalAddress = $netstatprerow[1];
	$ForeignAddress = $netstatprerow[2];
	$State = $netstatprerow[3];
	$PID = $netstatprerow[4];
	$ProcessName = $netstatprerow[6];
	$ServiceName = $netstatprerow[5];

	if (preg_match('/established/', strtolower($State))) {
		$netstatestcount = $netstatestcount + 1;
	}
	
	if (preg_match('/listening/', strtolower($State))) {
		$netstatliscount = $netstatliscount + 1;
	}
	
	if (preg_match('/time_wait/', strtolower($State))) {
		$netstattimcount = $netstattimcount + 1;
	}
	
	if (preg_match('/close_wait/', strtolower($State))) {
		$netstatclocount = $netstatclocount + 1;
	}
	
	$netstatcounter = $netstatcounter + 1;
		
	if ($Protocol != '') {
		$xml = $xml . '	<id' . $netstatcounter . '>' . "\n";
		$xml = $xml . '		<Protocol>' . urlencode($Protocol) . '</Protocol>';
		$xml = $xml . '<LocalAddress>' . urlencode($LocalAddress) . '</LocalAddress>';
		$xml = $xml . '<ForeignAddress>' . urlencode($ForeignAddress) . '</ForeignAddress>';
		$xml = $xml . '<State>' . urlencode($State) . '</State>';
		$xml = $xml . '<PID>' . urlencode($PID) . '</PID>';
		$xml = $xml . '<ProcessName>' . urlencode($ProcessName) . '</ProcessName>';
		$xml = $xml . '<ServiceName>' . urlencode($ServiceName) . '</ServiceName>' . "\n";
		$xml = $xml . '	</id' . $netstatcounter . '>' . "\n";
	}

}

if ($netstatcounter > 0) { $netstat_status = 1; }

$xml = $xml . '</netstat>';
$zp = gzopen($_SESSION['agentpath'] . '\\temp\\netstat.xml.gz', "w9");
gzwrite($zp, $xml);
gzclose($zp);

?>