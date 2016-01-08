<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/shutdown.php')) { exit; }

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemshutdown'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

$shutdown_xml = $euryscoinstallpath . '\\temp\\shutdown.xml';

if (file_exists($shutdown_xml)) {
	$xmlshutdown = simplexml_load_file($shutdown_xml);
	$xml_shutdowntype = strtolower($xmlshutdown->settings->shutdowntype);
	$xml_shutdowndatetime = $xmlshutdown->settings->shutdowntimenorm;
	$currentdatetime = date('Y-m-d H:i:s');
	$shutdowntimediff = strtotime($xml_shutdowndatetime) - strtotime($currentdatetime);
	if ($shutdowntimediff < 0) { $shutdowntimediff = 0; }
	$shutdownstatus = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">system will ' . $xml_shutdowntype . ': ' . date('d/m/Y H:i:s', strtotime($xml_shutdowndatetime)) . ' (' . number_format(intval($shutdowntimediff / 60 / 60), 0, ',', '.') . date(':i:s', $shutdowntimediff) . ')</blockquote>';
} else {
	$shutdownstatus = '<blockquote>schedule shutdown command (at least 30 seconds)</blockquote>';
}

echo json_encode(array('shutdownstatus'=>utf8_encode($shutdownstatus)));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>