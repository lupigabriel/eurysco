<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/tail.php')) { exit; }

include('/include/init.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) {  } else { exit; }

if (isset($_SESSION['agentstatus'])) {
	$agentstatus = $_SESSION['agentstatus'];
} else {
	$agentstatus = '';
}

if (isset($_SESSION['textreader'])) {
	$textreader = $_SESSION['textreader'];
} else {
	$textreader = '';
}

if (isset($_GET["file"])) {
	$name = $_GET["file"];
} else {
	$name = 'File Not Found...';
}

if (isset($_GET['path'])) {
	$path = $_GET['path'];
} else {
	$path = 'C:\\';
}

if (isset($_GET['openeditconf'])) {
	$openeditconf = $_GET['openeditconf'];
} else {
	$openeditconf = '';
}

$tailinfotop = '';
$tailinfobottom = '';
$filename = 'File Not Found...';
$fileexte = '-';
$filemtim = '-';
$filectim = '-';
$filesize = '0';
$fileperm = '-';
$filemity = '-';
$filesyml = '-';
$filemien = '-';

if ($name != 'File Not Found...' && file_exists($name) && is_readable($name)) {

	$finfo_mime_type = finfo_open(FILEINFO_MIME_TYPE);
	$finfo_symlink = finfo_open(FILEINFO_SYMLINK);
	$finfo_mime_encoding = finfo_open(FILEINFO_MIME_ENCODING);

	$filename = pathinfo($name)['basename'];
	if (isset(pathinfo($name)['extension'])) {
		$fileexte = strtoupper(pathinfo($name)['extension']);
	} else {
		$fileexte = '';
	}
	$filemtim = date("d/m/Y H:i:s", filemtime($name));
	$filectim = date("d/m/Y H:i:s", filectime($name));
	$filesize = filesize($name);
	if ($filesize < 0) {
		$filesize = exec('explorerfs.cmd "' . $name . '"', $errorarray, $errorlevel);
		if ($errorlevel != 0) {
			$filesize = '0';
		}
	}
	$fileperm = fileperms($name);
	$filemity = finfo_file($finfo_mime_type, $name);
	$filesyml = finfo_file($finfo_symlink, $name);
	$filemien = finfo_file($finfo_mime_encoding, $name);

	finfo_close($finfo_mime_type);
	finfo_close($finfo_symlink);
	finfo_close($finfo_mime_encoding);

}

$tailinfotop = $tailinfotop . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
$tailinfotop = $tailinfotop . '<tr><td width="30%" style="font-size:12px;"><a href="/explorer.php?path=' . urlencode($path) . '" title="Browse Folder"><div class="icon-folder" style="margin-top:2px;"></div></a>&nbsp;<strong>File Name:</strong></td><td width="70%" style="font-size:12px;"><strong>' . str_replace(' ', '&nbsp;', $filename) . '</strong>&nbsp;&nbsp;<a href="' . $textreader . '&close" title="Close File"><div class="icon-cancel"></div></a></td></tr>';
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


$tailoutput = '[FILE CANNOT BE OPENNED]';

if ($name != 'File Not Found...' && file_exists($name) && is_readable($name)) {

	$filearr = file($name);
	if ($openeditconf == 'on' && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1)) { $lastlines = array_slice($filearr, 0); } else { $lastlines = array_slice($filearr, -1000); }
	$tailoutput = '';	

	foreach ($lastlines as $lastline) {
		$tailoutput = $tailoutput . $lastline;
	}

}

echo json_encode(array('tailinfotop'=>utf8_encode($tailinfotop),'tailoutput'=>htmlspecialchars(utf8_encode($tailoutput), ENT_HTML401,'UTF-8', true),'tailinfobottom'=>utf8_encode($tailinfobottom)));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>