<?php

if (!isset($_SERVER["HTTP_X_FORWARDED_FOR"]) || !isset($_SERVER["HTTP_X_FORWARDED_HOST"]) || strpos($_SERVER["HTTP_HOST"], ':' . $_SERVER["SERVER_PORT"]) > 0) { header('HTTP/1.1 505 HTTP Version Not Supported'); exit; }

define('PHP_FIREWALL_REQUEST_URI', strip_tags( $_SERVER['REQUEST_URI'] ) );
define('PHP_FIREWALL_ACTIVATION', true );
if ( is_file( @dirname(__FILE__) . '/php-firewall/firewall.php' ) ) {
	include_once( @dirname(__FILE__) . '/php-firewall/firewall.php' );
}

$audit = '';

$envcomputername = 'localhost';
if (isset($_ENV["COMPUTERNAME"])) {
	$envcomputername = strtolower($_ENV["COMPUTERNAME"]);
}

$euryscoinstallpath = str_replace('\\server', '', $_SERVER['DOCUMENT_ROOT']);

$config_settings = str_replace('\\server', '\\conf', $_SERVER['DOCUMENT_ROOT']) . '\\config_settings.xml';
if (file_exists($config_settings)) {
	$xmlserver = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_settings, true)))));
	date_default_timezone_set($xmlserver->settings->timezonesetting);
	$nodesrrsetting = $xmlserver->settings->nodesrrsetting;
	$eurysco_coreport = $xmlserver->settings->corelisteningport;
} else {
	date_default_timezone_set('UTC');
	$nodesrrsetting = '15000';
	$eurysco_coreport = 59980;
}

require_once '/auth.php';

if (implode(array_keys($_POST)) != '') {
	foreach (array_keys($_POST) as $key) { 
		if (!is_null($_POST[$key]) && !is_string($_POST[$key]) && !is_numeric($_POST[$key])) {
			exit;
		} else {
			if (is_string($_POST[$key])) {
				if ($key != '') { $_POST[$key] = htmlspecialchars((string)$_POST[$key], ENT_QUOTES, 'UTF-8'); }
			}
		}
	}
}

header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');

if (extension_loaded('zlib')) { ob_start('ob_gzhandler'); }

$config_server = str_replace('\\server', '\\conf', $_SERVER['DOCUMENT_ROOT']) . '\\config_server.xml';

if (file_exists($config_settings)) {
	$xmlserver = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_settings, true)))));
	$timezonesetting = $xmlserver->settings->timezonesetting;
}

$config_coresrv = str_replace('\\server', '\\conf', $_SERVER['DOCUMENT_ROOT']) . '\\config_core.xml';
if (file_exists($config_coresrv)) {
	$xmlcore = simplexml_load_file($config_coresrv);
	$eurysco_coreport = $xmlcore->settings->corelisteningport;
} else {
	$eurysco_coreport = 59980;
}

date_default_timezone_set($timezonesetting);

set_time_limit(300);

?>