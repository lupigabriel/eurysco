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

$config_settings = 'conf\\config_settings.xml';
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

header('Content-Type: text/html; charset=utf-8');

if (extension_loaded('zlib')) { ob_start('ob_gzhandler'); }

$config_server = 'conf\\config_server.xml';

if (file_exists($config_settings)) {
	$xmlserver = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_settings, true)))));
	$timezonesetting = $xmlserver->settings->timezonesetting;
}

$config_coresrv = 'conf\\config_core.xml';
if (file_exists($config_coresrv)) {
	$xmlcore = simplexml_load_file($config_coresrv);
	$eurysco_coreport = $xmlcore->settings->corelisteningport;
} else {
	$eurysco_coreport = 59980;
}

date_default_timezone_set($timezonesetting);

set_time_limit(300);

?>