<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/audit.php')) { exit; }

include('/include/init.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Auditors' || $_SESSION['usersett']['auditlog'] > 0) {  } else { exit; }

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'date';
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

if (isset($_GET["file"])) {
	$name = $_GET["file"];
} else {
	$name = 'Audit Not Collected...';
}

if (isset($_GET['pause'])) {
	$pause = '&pause';
} else {
	$pause = '';
}

$tailinfotop = '';
$tailinfobottom = '';
$filename = 'Audit Not Collected...';
$fileexte = '-';
$filemtim = '-';
$filectim = '-';
$filesize = '0';
$fileperm = '-';
$filemity = '-';
$filesyml = '-';
$filemien = '-';

if ($name != 'Audit Not Collected...' && file_exists($name) && is_readable($name)) {

	$finfo_mime_type = finfo_open(FILEINFO_MIME_TYPE);
	$finfo_symlink = finfo_open(FILEINFO_SYMLINK);
	$finfo_mime_encoding = finfo_open(FILEINFO_MIME_ENCODING);

	$filename = pathinfo($name)['basename'];
	$fileexte = strtoupper(pathinfo($name)['extension']);
	$filemtim = date("d/m/Y H:i:s", filemtime($name));
	$filectim = date("d/m/Y H:i:s", filectime($name));
	$filesize = filesize($name);
	$fileperm = fileperms($name);
	$filemity = finfo_file($finfo_mime_type, $name);
	$filesyml = finfo_file($finfo_symlink, $name);
	$filemien = finfo_file($finfo_mime_encoding, $name);

	finfo_close($finfo_mime_type);
	finfo_close($finfo_symlink);
	finfo_close($finfo_mime_encoding);

}

$tailinfotop = $tailinfotop . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
$tailinfotop = $tailinfotop . '<tr><td width="30%" style="font-size:12px;"><strong>File Name:</strong></td><td width="70%" style="font-size:12px;"><strong>' . str_replace(' ', '&nbsp;', $filename) . '</strong></td></tr>';
$tailinfotop = $tailinfotop . '<tr><td width="30%" style="font-size:12px;">File Size:</td><td width="70%" style="font-size:12px;">' . number_format($filesize, 0, ',', '.') . ' Byte</td></tr>';
$tailinfotop = $tailinfotop . '<tr><td width="30%" style="font-size:12px;">Date Last Modified:</td><td width="70%" style="font-size:12px;">' . $filemtim . '</td></tr>';
$tailinfotop = $tailinfotop . '</table>';

$tailinfobottom = $tailinfobottom . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
$tailinfobottom = $tailinfobottom . '<tr><td width="30%" style="font-size:12px;">Mime Symlink:</td><td width="70%" style="font-size:12px;">' . $filesyml . '</td></tr>';
$tailinfobottom = $tailinfobottom . '<tr><td width="30%" style="font-size:12px;">Date Created:</td><td width="70%" style="font-size:12px;">' . $filectim . '</td></tr>';
$tailinfobottom = $tailinfobottom . '<tr><td width="30%" style="font-size:12px;">File Extension:</td><td width="70%" style="font-size:12px;">' . $fileexte . '</td></tr>';
$tailinfobottom = $tailinfobottom . '<tr><td width="30%" style="font-size:12px;">File Permission:</td><td width="70%" style="font-size:12px;">' . $fileperm . '</td></tr>';
$tailinfobottom = $tailinfobottom . '<tr><td width="30%" style="font-size:12px;">Mime Type:</td><td width="70%" style="font-size:12px;">' . $filemity . '</td></tr>';
$tailinfobottom = $tailinfobottom . '<tr><td width="30%" style="font-size:12px;">Mime Encoding:</td><td width="70%" style="font-size:12px;">' . $filemien . '</td></tr>';
$tailinfobottom = $tailinfobottom . '</table>';


$tailoutput = '[AUDIT LOG CANNOT BE OPENNED]';
$audittable = '';

if (isset($_GET['srvrun'])) {
	if ($orderby == 'date') { $obyAuditDate = ' color:#8063C8;'; } else { $obyAuditDate = ''; }
	if ($orderby == 'user') { $obyAuditUser = ' color:#8063C8;'; } else { $obyAuditUser = ''; }
	if ($orderby == 'node') { $obyAuditNode = ' color:#8063C8;'; } else { $obyAuditNode = ''; }
	if ($orderby == 'description') { $obyAuditDesc = ' color:#8063C8;'; } else { $obyAuditDesc = ''; }
	if ($orderby == 'exitcode') { $obyAuditExit = ' color:#8063C8;'; } else { $obyAuditExit = ''; }
	$audittable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="10%"><a href="?orderby=date&filter=' . urlencode($filter) . $pause . '" style="font-size:12px; font-weight:bold;' . $obyAuditDate . '" title="Descending Order by Date">Date</a></td><td width="10%" align="center"><a href="?orderby=user&filter=' . urlencode($filter) . $pause . '" style="font-size:12px; font-weight:bold;' . $obyAuditUser . '" title="Ascending Order by User">User</a></td><td width="10%" align="center"><a href="?orderby=node&filter=' . urlencode($filter) . $pause . '" style="font-size:12px; font-weight:bold;' . $obyAuditNode . '" title="Ascending Order by Node">Node</a></td><td width="70%"><a href="?orderby=description&filter=' . urlencode($filter) . $pause . '" style="font-size:12px; font-weight:bold;' . $obyAuditDesc . '" title="Ascending Order by Description">Description</a></td></tr>';
	$dbaudit = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoAudit');
	$dbaudit->busyTimeout(30000);
	$dbaudit->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');
	$auditlogs = $dbaudit->query('SELECT * FROM auditLog WHERE date LIKE "%' . $_GET["srvrun"] . '%"');
	$auditarray = array();
	$auditcounter = 0;
	while ($auditlog = $auditlogs->fetchArray()) {
		$checkfilter = 1;
		if (substr($filter, 0, 1) != '-') {
			if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', strtolower('<Date>' . $auditlog['date'] . '</Date><User>' . $auditlog['user'] . '</User><Node>' . $auditlog['node'] . '</Node><Type>' . $auditlog['type'] . '</Type><Description>' . $auditlog['description'] . '</Description><Exitcode>' . $auditlog['exitcode'] . '</Exitcode>')) || strpos(strtolower('<Date>' . $auditlog['date'] . '</Date><User>' . $auditlog['user'] . '</User><Node>' . $auditlog['node'] . '</Node><Type>' . $auditlog['type'] . '</Type><Description>' . $auditlog['description'] . '</Description><Exitcode>' . $auditlog['exitcode'] . '</Exitcode>'), strtolower($filter)) > -1) {
				$checkfilter = 0;
			} else {
				$checkfilter = 1;
			}
		} else {
			$notfilter = substr($filter, 1);
			if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', strtolower('<Date>' . $auditlog['date'] . '</Date><User>' . $auditlog['user'] . '</User><Node>' . $auditlog['node'] . '</Node><Type>' . $auditlog['type'] . '</Type><Description>' . $auditlog['description'] . '</Description><Exitcode>' . $auditlog['exitcode'] . '</Exitcode>')) && !strpos(strtolower('<Date>' . $auditlog['date'] . '</Date><User>' . $auditlog['user'] . '</User><Node>' . $auditlog['node'] . '</Node><Type>' . $auditlog['type'] . '</Type><Description>' . $auditlog['description'] . '</Description><Exitcode>' . $auditlog['exitcode'] . '</Exitcode>'), strtolower($notfilter))) {
				$checkfilter = 0;
			} else {
				$checkfilter = 1;
			}
		}
		if ($checkfilter == 0) {
			if ($orderby == 'date') { $auditarray[$auditcounter][0] = date('YmdHis', strtotime($auditlog['date'])); } else { $auditarray[$auditcounter][0] = strtolower($auditlog[$orderby]); }
			$auditarray[$auditcounter][1] = date('d/m/Y H:i:s', strtotime($auditlog['date']));
			$auditarray[$auditcounter][2] = $auditlog['user'];
			$auditarray[$auditcounter][3] = $auditlog['node'];
			$auditarray[$auditcounter][4] = $auditlog['type'];
			$auditarray[$auditcounter][5] = urldecode($auditlog['description']);
			$auditarray[$auditcounter][6] = $auditlog['exitcode'];
			$auditarray[$auditcounter][7] = 'Raw Data View:' . "\n\n" . '<Date>' . $auditlog['date'] . '</Date>' . "\n" . '<User>' . $auditlog['user'] . '</User>' . "\n" . '<Node>' . $auditlog['node'] . '</Node>' . "\n" . '<Type>' . $auditlog['type'] . '</Type>' . "\n" . '<Description>' . $auditlog['description'] . '</Description>' . "\n" . '<Exitcode>' . $auditlog['exitcode'] . '</Exitcode>';
			$auditcounter = $auditcounter + 1;
		}
	}
	$dbaudit->close();
	if ($orderby == 'user' || $orderby == 'node' || $orderby == 'type' || $orderby == 'description') {
		sort($auditarray);
	} else {
		rsort($auditarray);
	}
	$auditpagearray = array();
	foreach ($auditarray as $auditrow) {
		array_push($auditpagearray, '<tr class="rowselect" title="' . htmlentities($auditrow[7]) . '"><td style="font-size:12px;"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">' . $auditrow[1] . '</div></td><td style="font-size:12px;" align="center">' . $auditrow[2] . '</td><td style="font-size:12px;" align="center">' . $auditrow[3] . '</td><td style="font-size:12px;" title="' . $auditrow[4] . '">' . $auditrow[5] . '</td></tr>');
	}
	$auditpages = array_chunk($auditpagearray, 250);
	if ($pgkey > count($auditpages) - 1) { $pgkey = count($auditpages) - 1; }
	if (count($auditpages) > 0) {
		foreach($auditpages[$pgkey] as $audit) {
			$audittable = $audittable . $audit;
		}
	}
	if ($auditcounter == 0) { $audittable = $audittable . '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="5">No Results...</td></tr>'; }
	$audittable = $audittable . '</table>';
	$auditpaging = '';
	if (count($auditpages) > 1) {
		if ($pgkey > 5) {
			$auditpaging = $auditpaging . '<a href="?page=1&orderby=' . $orderby . '&filter=' . urlencode($filter) . $pause . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
		}
		for($i = 1; $i < count($auditpages) + 1; $i++) {
			if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
			if ($i > $pgkey - 5 && $i < $pgkey + 7) {
				$auditpaging = $auditpaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . $pause . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
			}
		}
		if (count($auditpages) > $pgkey + 6) {
			$auditpaging = $auditpaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($auditpages) . '&orderby=' . $orderby . '&filter=' . urlencode($filter) . $pause . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($auditpages) . '</span></a>';
		}
		$audittable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $auditpaging . '</blockquote><br />' . $audittable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $auditpaging . '</blockquote>';
	}
} else {
	if ($name != 'Audit Not Collected...' && file_exists($name) && is_readable($name)) {
		$filearr = file($name);
		$lastlines = array_slice($filearr, -1000);
		$tailoutput = '';	
		foreach ($lastlines as $lastline) {
			$tailoutput = $tailoutput . $lastline;
		}
	}
}

echo json_encode(array('tailinfotop'=>utf8_encode($tailinfotop),'tailoutput'=>htmlspecialchars(utf8_encode($tailoutput), ENT_HTML401,'UTF-8', true),'tailinfobottom'=>utf8_encode($tailinfobottom),'audittable'=>utf8_encode($audittable)));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>