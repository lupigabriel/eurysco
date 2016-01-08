<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/7zip.php')) { exit; }

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

if (isset($_GET['file'])) {
	$name = $_GET['file'];
} else {
	$name = 'Archive Not Found...';
}

if (isset($_GET['path'])) {
	$path = $_GET['path'];
} else {
	$path = 'C:\\';
}

if (isset($_GET['extractfolder'])) {
	$extractfolder = $_GET['extractfolder'];
} else {
	$extractfolder = '';
}

if (isset($_GET['extractpass'])) {
	$extractpass = $_GET['extractpass'];
} else {
	$extractpass = '';
}

if (isset($_SESSION['agentstatus'])) {
	$agentstatus = $_SESSION['agentstatus'];
} else {
	$agentstatus = '';
}

if (isset($_GET['lock'])) {
	$lock = $_GET['lock'];
} else {
	$lock = '';
}

if (!isset($_SESSION['7zip_' . $lock])) {
	$_SESSION['7zip_' . $lock] = 0;
}

$zipinfotop = '';
$zipinfobottom = '';
$filename = 'Archive Not Found...';
$fileexte = '-';
$filemtim = '-';
$filectim = '-';
$filesize = '0';
$fileperm = '-';
$filemity = '-';
$filesyml = '-';
$filemien = '-';

if ($name != 'Archive Not Found...' && file_exists($name) && is_readable($name)) {

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

$zipinfotop = $zipinfotop . '<tr><td width="30%" style="font-size:12px;"><a href="/explorer.php?path=' . urlencode($path) . '" title="Browse Folder"><div class="icon-folder" style="margin-top:2px;"></div></a>&nbsp;<strong>Archive Name:</strong></td><td width="70%" style="font-size:12px;"><strong>' . str_replace(' ', '&nbsp;', $filename) . '</strong>&nbsp;&nbsp;<a href="' . $_SESSION['zipextract'] . '&close" title="Close Archive"><div class="icon-cancel"></div></a></td></tr>';
$zipinfotop = $zipinfotop . '<tr><td width="30%" style="font-size:12px;">File Size:</td><td width="70%" style="font-size:12px;">' . number_format($filesize, 0, ',', '.') . ' Byte</td></tr>';
$zipinfotop = $zipinfotop . '<tr><td width="30%" style="font-size:12px;">Date Last Modified:</td><td width="70%" style="font-size:12px;">' . $filemtim . '</td></tr>';

$zipinfobottom = $zipinfobottom . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
$zipinfobottom = $zipinfobottom . '<tr><td width="30%" style="font-size:12px;">Mime Symlink:</td><td width="70%" style="font-size:12px;">' . $filesyml . '</td></tr>';
$zipinfobottom = $zipinfobottom . '<tr><td width="30%" style="font-size:12px;">Date Created:</td><td width="70%" style="font-size:12px;">' . $filectim . '</td></tr>';
$zipinfobottom = $zipinfobottom . '<tr><td width="30%" style="font-size:12px;">File Extension:</td><td width="70%" style="font-size:12px;">' . $fileexte . '</td></tr>';
$zipinfobottom = $zipinfobottom . '<tr><td width="30%" style="font-size:12px;">File Permission:</td><td width="70%" style="font-size:12px;">' . $fileperm . '</td></tr>';
$zipinfobottom = $zipinfobottom . '<tr><td width="30%" style="font-size:12px;">Mime Type:</td><td width="70%" style="font-size:12px;">' . $filemity . '</td></tr>';
$zipinfobottom = $zipinfobottom . '<tr><td width="30%" style="font-size:12px;">Mime Encoding:</td><td width="70%" style="font-size:12px;">' . $filemien . '</td></tr>';
$zipinfobottom = $zipinfobottom . '</table>';


$zipoutput = '[ARCHIVE CANNOT BE OPENNED]';

$message = '';

if ($name != 'Archive Not Found...' && file_exists($name) && is_readable($name)) {
	
	$tempzipt = $euryscoinstallpath . '\\temp\\core\\' . md5(session_id()) . '.7zt';
	if ($_GET["extract"] == 0) {
		session_write_close();
		$zipexec = exec('"' . $euryscoinstallpath . '\\ext\\7zip.exe" l "' . $name . '" -p' . $extractpass . '>"' . $tempzipt . '"', $errorarray, $errorlevel);
		session_start();
		if ($errorlevel == 0) {
			if ($extractpass != '') { $_SESSION['7zip_' . $lock] = 0; }
			$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     7zip extractor     listing archive "' . str_replace('\\\\', '\\', $name) . '" success';
		} else {
			if ($extractpass != '') { $_SESSION['7zip_' . $lock] = $_SESSION['7zip_' . $lock] + 1; }
			$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     7zip extractor     listing archive "' . str_replace('\\\\', '\\', $name) . '" failed';
		}
	}
	if ($_GET["extract"] == 1 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1)) {
		session_write_close();
		$zipexec = exec('"' . $euryscoinstallpath . '\\ext\\7zip.exe" x "' . $name . '" -o"' . str_replace('\\\\', '\\', $path . '\\' . $extractfolder) . '" -y -p' . $extractpass . '>"' . $tempzipt . '"', $errorarray, $errorlevel);
		session_start();
		if ($errorlevel == 0) {
			if ($extractpass != '') { $_SESSION['7zip_' . $lock] = 0; }
			$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">extract archive <strong>' . str_replace(' ', '&nbsp;', $filename) . '</strong> in <strong>' . str_replace('\\\\', '\\', $path . '\\' . $extractfolder) . '</strong> success</blockquote><br />';
			$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     7zip extractor     extract archive "' . str_replace('\\\\', '\\', $name) . '" in "' . str_replace('\\\\', '\\', $path . '\\' . $extractfolder) . '" success';
		} else {
			if ($extractpass != '') { $_SESSION['7zip_' . $lock] = $_SESSION['7zip_' . $lock] + 1; }
			$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">extract archive <strong>' . str_replace(' ', '&nbsp;', $filename) . '</strong> in <strong>' . str_replace('\\\\', '\\', $path . '\\' . $extractfolder) . '</strong> failed</blockquote><br />';
			$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     7zip extractor     extract archive "' . str_replace('\\\\', '\\', $name) . '" in "' . str_replace('\\\\', '\\', $path . '\\' . $extractfolder) . '" failed';
		}
	}

	if (file_exists($tempzipt) && is_readable($tempzipt)) {
		$filearr = file($tempzipt);
		$lastlines = array_slice($filearr, -10000);
		$zipoutput = '';	
		
		foreach ($lastlines as $lastline) {
			$zipoutput = $zipoutput . $lastline;
		}
		
		unlink($tempzipt);
	}
}

$zipinfotop = $zipinfotop . $message;

include('/auditlog.php');

echo json_encode(array('zipinfotop'=>utf8_encode($zipinfotop),'zipoutput'=>htmlspecialchars(utf8_encode($zipoutput), ENT_HTML401,'UTF-8', true),'zipinfobottom'=>utf8_encode($zipinfobottom),'ziplockstatus'=>$_SESSION['7zip_' . $lock]));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>