<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/programs.php')) { exit; }

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
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['installedprograms'] > 0) {  } else { exit; }
session_write_close();

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'Name';
}

if (isset($_GET['filter'])) {
	$filter = $_GET['filter'];
} else {
	$filter = '';
}

if (isset($_GET['page'])) {
	$pgkey = $_GET['page'];
} else {
	$pgkey = 0;
}

if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['installedprograms'] > 1) {
	$colspan = '6';
	$columnicon = '<td width="1%" style="font-size:12px; font-weight:bold;" align="center"></td>';
} else {
	$colspan = '5';
	$columnicon = '';
}

if ($orderby == 'Vendor') { $obyVendor = ' color:#8063C8;'; } else { $obyVendor = ''; }
if ($orderby == 'Name') { $obyName = ' color:#8063C8;'; } else { $obyName = ''; }
if ($orderby == 'Version') { $obyVersion = ' color:#8063C8;'; } else { $obyVersion = ''; }
if ($orderby == 'InstallDate') { $obyInstallDate = ' color:#8063C8;'; } else { $obyInstallDate = ''; }

$programtable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr>' . $columnicon . '<td width="25%" align="center"><a href="?orderby=Vendor&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyVendor . '" title="Ascending Order by Vendor">Vendor</a></td><td width="50%"><a href="?orderby=Name&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyName . '" title="Ascending Order by Name">Name</a></td><td width="15%" align="center"><a href="?orderby=Version&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyVersion . '" title="Descending Order by Version">Version</a></td><td width="10%" align="center"><a href="?orderby=InstallDate&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;' . $obyInstallDate . '" title="Descending Order by Install Date">Install Date</a></td></tr>';



$programarray = array();
$programcounter = 0;
require('/include/class.WindowsRegistry.php');
$winReg = new WindowsRegistry();
$UninstallKeyPath = 'HKEY_LOCAL_MACHINE\\SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Uninstall';
$keyNames = $winReg->GetSubKeys($UninstallKeyPath);
if (is_array($keyNames)) {
	foreach ($keyNames as $programs) {
		
		$valueNames = $winReg->GetValueNames($UninstallKeyPath . '\\' . $programs);
		if (is_array($valueNames)) {
				
			$programchk = '';
			foreach ($valueNames as $program) { $programchk = $programchk . '#' . $program . '#'; }
				
			if (preg_match('/#Publisher#/', $programchk)) { $Vendor = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'Publisher'); } else { $Vendor = '-'; }
			if (preg_match('/#DisplayName#/', $programchk)) { $Name = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'DisplayName'); } else { $Name = '-'; }
			if (preg_match('/#DisplayVersion#/', $programchk)) { $Version = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'DisplayVersion'); } else { $Version = '-'; }
			if (preg_match('/#InstallDate#/', $programchk)) { $InstallDate = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'InstallDate'); } else { $InstallDate = '-'; }
			if (preg_match('/#UninstallString#/', $programchk)) { $UninstallString = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'UninstallString'); } else { $UninstallString = ''; }
			$IdentifyingNumber = $programs;
				
			if ($Name != '-') {

				$datarow = strtolower('<Vendor>' . $Vendor . '</Vendor><Name>' . $Name . '</Name><Version>' . $Version . '</Version><InstallDate>' . $InstallDate . '</InstallDate><IdentifyingNumber>' . $IdentifyingNumber . '</IdentifyingNumber>');
				$prefilter = $_SESSION['usersett']['installedprogramsf'];
				$checkprefilter = 1;
				if (substr($prefilter, 0, 1) != '-') {
					if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($prefilter))) . '/', $datarow) || strpos($datarow, strtolower($prefilter)) > -1) {
						$checkprefilter = 0;
					} else {
						$checkprefilter = 1;
					}
				} else {
					$notprefilter = substr($prefilter, 1);
					if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notprefilter))) . '/', $datarow) && !strpos($datarow, strtolower($notprefilter))) {
						$checkprefilter = 0;
					} else {
						$checkprefilter = 1;
					}
				}
				$checkfilter = 1;
				if (substr($filter, 0, 1) != '-') {
					if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', $datarow) || strpos($datarow, strtolower($filter)) > -1) {
						if ($checkprefilter == 0) {
							$checkfilter = 0;
						}
					} else {
						$checkfilter = 1;
					}
				} else {
					$notfilter = substr($filter, 1);
					if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', $datarow) && !strpos($datarow, strtolower($notfilter))) {
						if ($checkprefilter == 0) {
							$checkfilter = 0;
						}
					} else {
						$checkfilter = 1;
					}
				}
				if ($checkfilter == 0) {
					if ($orderby == 'Vendor') { if ($Vendor != '-') { $programarray[$programcounter][0] = strtolower($Vendor); } else { $programarray[$programcounter][0] = ''; } }
					if ($orderby == 'Name') { if ($Name != '-') { $programarray[$programcounter][0] = strtolower($Name); } else { $programarray[$programcounter][0] = ''; } }
					if ($orderby == 'Version') { if ($Version != '-') { $programarray[$programcounter][0] = strtolower(str_replace(',', '.', preg_replace('/[^0-9,]/', '', preg_replace('/[^0-9]/', ',', $Version, 1)))); } else { $programarray[$programcounter][0] = 0; } }
					if ($orderby == 'InstallDate') { if ($InstallDate != '-' && is_numeric($InstallDate) && strlen($InstallDate) == 8) { $programarray[$programcounter][0] = $InstallDate; } else { $programarray[$programcounter][0] = 0; } }
					$programarray[$programcounter][1] = $Vendor;
					$programarray[$programcounter][2] = $Name;
					if ($Version != '-') { $programarray[$programcounter][3] = preg_replace('/[^a-zA-Z0-9]/', '.', $Version); } else { $programarray[$programcounter][3] = $Version; }
					if ($InstallDate != '-' && is_numeric($InstallDate) && strlen($InstallDate) == 8) { $programarray[$programcounter][4] = substr($InstallDate, 6, 2) . '/' . substr($InstallDate, 4, 2) . '/' . substr($InstallDate, 0, 4); } else { $programarray[$programcounter][4] = '-'; }
					if (preg_match('/\{........-....-....-....-............\}/', $UninstallString) && preg_match('/\{........-....-....-....-............\}/', $IdentifyingNumber) && strlen($IdentifyingNumber) == 38) { $programarray[$programcounter][5] = str_replace('{', '', str_replace('}', '', $IdentifyingNumber)); } else { $programarray[$programcounter][5] = ''; }
					$programarray[$programcounter][6] = 'Raw Data View:' . "\n\n" . '<Vendor>' . $Vendor . '</Vendor>' . "\n" . '<Name>' . $Name . '</Name>' . "\n" . '<Version>' . $Version . '</Version>' . "\n" . '<InstallDate>' . $InstallDate . '</InstallDate>' . "\n" . '<IdentifyingNumber>' . $IdentifyingNumber . '</IdentifyingNumber>';
					$programcounter = $programcounter + 1;
				}
			}
			
		}

	}
}

if ($orderby != 'Version' && $orderby != 'InstallDate') {
	sort($programarray);
} else {
	rsort($programarray);
}

$programspagearray = array();
if ($_SESSION['csv_programs'] == 'csv_programs') {
	$tmpcsvexport = array();
	array_push($tmpcsvexport, '"eurysco CSV Export: Programs";' . "\n" . '"eurysco CSV Source Node: ' . $envcomputername . '";' . "\n" . '"eurysco CSV Filter: ' . $filter . '";' . "\n" . '"eurysco CSV Total Raws: ' . $programcounter . '";' . "\n\n");
	array_push($tmpcsvexport, '"Vendor";' . '"Name";' . '"Version";' . '"Install Date";' . '"Identifying Number";' . "\n\n");
}
foreach ($programarray as $programrow) {
	if (strlen($programrow[2]) > 50) { $LimitName = substr($programrow[2], 0, 50) . '&nbsp;[...]'; } else { $LimitName = $programrow[2]; }
	if (strlen($programrow[2]) > 16) { $TitleName = substr($programrow[2], 0, 16) . '&nbsp;[...]'; } else { $TitleName = $programrow[2]; }
	if (strlen($programrow[2]) > 26) { $UninstallName = substr($programrow[2], 0, 26) . '&nbsp;[...]'; } else { $UninstallName = $programrow[2]; }
	if (strlen($programrow[1]) > 20) { $LimitVendor = substr($programrow[1], 0, 20) . '&nbsp;[...]'; } else { $LimitVendor = $programrow[1]; }
	if (strlen($programrow[3]) > 14) { $LimitVersion = substr($programrow[3], 0, 14) . '&nbsp;[...]'; } else { $LimitVersion = $programrow[3]; }
	if ($programrow[5] != '') { $uninstallable = '<a href=\'javascript:uninstallprogram("' . $programrow[1] . '","' . $programrow[2] . '","' . $programrow[3] . '","' . $programrow[4] . '","' . $programrow[5] . '","' . $TitleName . '","' . $LimitVendor . '","' . $LimitVersion . '","' . $UninstallName . '");\'><img src="/images/collapse24-black.png" width="16" height="16" border="0" title="Uninstall: ' . $programrow[2] . '"></a>'; } else { $uninstallable = ''; }
	if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['installedprograms'] > 1) {
		$uninstallicon = '<td style="font-size:12px;" align="center">' . $uninstallable . '</td>';
	} else {
		$uninstallicon = '';
	}
	array_push($programspagearray, '<tr class="rowselect" title="' . htmlentities($programrow[6]) . '">' . $uninstallicon . '<td style="font-size:12px;" align="center"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="' . $programrow[1] . '">' . $LimitVendor . '</div></td><td><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="' . $programrow[2] . '">' . $LimitName . '</div></td><td style="font-size:12px;" align="center" title="' . $programrow[3] . '">' . $LimitVersion . '</td><td style="font-size:12px;" align="center">' . $programrow[4] . '</td></tr>');
	if ($_SESSION['csv_programs'] == 'csv_programs') { array_push($tmpcsvexport, '"' . $programrow[1] . '";' . '"' . $programrow[2] . '";' . '"' . $programrow[3] . '";' . '"' . $programrow[4] . '";' . '"' . $programrow[5] . '";' . "\n"); }
}

if ($_SESSION['csv_programs'] == '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/programs.php?csv_programs&orderby=' . $orderby . '&filter=' . urlencode($filter) . '" style="font-size:12px; color:#777;" title="Create CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }
if ($_SESSION['csv_programs'] == 'csv_programs') {
	session_start();
	$_SESSION['csv_programs'] = $tmpcsvexport;
	session_write_close();
}
if ($_SESSION['csv_programs'] != '') { $csvexport = '<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="/csv.php?export=csv_programs&source=' . $envcomputername . '" style="font-size:12px;" title="Export CSV"><div class="icon-download-2"></div></a>&nbsp;Total Elements:</div>'; }

$programspages = array_chunk($programspagearray, 100);

if ($pgkey > count($programspages) - 1) { $pgkey = count($programspages) - 1; }

if (count($programspages) > 0) {
	foreach($programspages[$pgkey] as $programrw) {
		$programtable = $programtable . $programrw;
	}
}

if ($programcounter == 0) { $programtable = $programtable . '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="' . $colspan . '">No Results...</td></tr>'; }

$programtable = $programtable . '</table>';

$programspaging = '';
if (count($programspages) > 1) {
	if ($pgkey > 5) {
		$programspaging = $programspaging . '<a href="?page=1&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($programspages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$programspaging = $programspaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($programspages) > $pgkey + 6) {
		$programspaging = $programspaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($programspages) . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($programspages) . '</span></a>';
	}
	$programtable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $programspaging . '</blockquote><br />' . $programtable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $programspaging . '</blockquote>';
}

$totalelement = count($programarray);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('programtable'=>utf8_encode($programtable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'csvexport'=>$csvexport));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>