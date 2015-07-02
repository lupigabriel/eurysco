<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/explorer.php')) { exit; }

include('/include/init.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) {  } else { exit; }
session_write_close();

if (isset($_GET['pathfile'])) {
	$pathfile = str_replace('\\\\', '\\', $_GET['pathfile']);
} else {
	$pathfile = 'C:\\';
}

$finfo_mime_type = finfo_open(FILEINFO_MIME_TYPE);
$finfo_symlink = finfo_open(FILEINFO_SYMLINK);
$finfo_mime_encoding = finfo_open(FILEINFO_MIME_ENCODING);

$filemity_title = finfo_file($finfo_mime_type, $pathfile);
$filesyml_title = finfo_file($finfo_symlink, $pathfile);
$filemien_title = finfo_file($finfo_mime_encoding, $pathfile);

$filemity_name = strtolower($filemity_title);
$filesyml_name = strtolower($filesyml_title);
$filemien_name = strtolower($filemien_title);

if (strlen($filemity_name) > 15) { $filemity_name = substr($filemity_name, 0, 15) . '&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0">'; }
if (strlen($filesyml_name) > 15) { $filesyml_name = substr($filesyml_name, 0, 15) . '&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0">'; }
if (strlen($filemien_name) > 15) { $filemien_name = substr($filemien_name, 0, 15) . '&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0">'; }

echo json_encode(array('finfo_mime_type'=>utf8_encode($filemity_name),'finfo_symlink'=>utf8_encode($filesyml_name),'finfo_mime_encoding'=>utf8_encode($filemien_name),'title_mime_type'=>utf8_encode($filemity_title),'title_symlink'=>utf8_encode($filesyml_title),'title_mime_encoding'=>utf8_encode($filemien_title)));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>