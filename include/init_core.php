<?php

if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) || !isset($_SERVER['HTTP_X_FORWARDED_HOST']) || strpos($_SERVER['HTTP_HOST'], ':' . $_SERVER['SERVER_PORT']) > 0) { header('HTTP/1.1 505 HTTP Version Not Supported'); exit; }

define('PHP_FIREWALL_REQUEST_URI', strip_tags( $_SERVER['REQUEST_URI'] ) );
define('PHP_FIREWALL_ACTIVATION', true );
if ( is_file( @dirname(__FILE__) . '/php-firewall/firewall.php' ) ) {
	include_once( @dirname(__FILE__) . '/php-firewall/firewall.php' );
}

$audit = '';

$envcomputername = 'localhost';
if (isset($_ENV['COMPUTERNAME'])) {
	$envcomputername = strtolower($_ENV['COMPUTERNAME']);
}

if (!strpos('|' . $_SERVER['HTTP_X_FORWARDED_HOST'], $envcomputername) && !strpos('|' . $_SERVER['HTTP_X_FORWARDED_HOST'], 'localhost') && !strpos('|' . $_SERVER['HTTP_HOST'], $envcomputername) && !strpos('|' . $_SERVER['HTTP_HOST'], 'localhost')) {
	header('HTTP/1.1 505 HTTP Version Not Supported'); exit;
}

$euryscoinstallpath = str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']);

$config_settings = $euryscoinstallpath . '\\conf\\config_settings.xml';
if (file_exists($config_settings)) {
	$xmlserver = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_settings, true)))));
	date_default_timezone_set($xmlserver->settings->timezonesetting);
} else {
	date_default_timezone_set('UTC');
}

$config_agentsrv = $euryscoinstallpath . '\\conf\\config_agent.xml';
$eurysco_agentsrv = 'euryscoAgent';
$eurysco_serverconaddress = '';
$eurysco_serverconport = '';
$eurysco_serverconpassword = '';
if (file_exists($config_agentsrv)) {
	$xmlagent = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_agentsrv, true)))));
	$eurysco_serverconaddress = $xmlagent->settings->serverconnectionaddress;
	$eurysco_serverconport = $xmlagent->settings->serverconnectionport;
	$eurysco_serverconpassword = $xmlagent->settings->serverconnectionpassword;
	$eurysco_sslverifyhost = $xmlagent->settings->sslverifyhost;
	$eurysco_sslverifypeer = $xmlagent->settings->sslverifypeer;
}

require_once '/auth.php';

if (implode(array_keys($_POST)) != '') {
	foreach (array_keys($_POST) as $key) { 
		if (!is_null($_POST[$key]) && !is_string($_POST[$key]) && !is_numeric($_POST[$key])) {
			header('location: ' . $_SERVER['SCRIPT_NAME']);
			exit;
		} else {
			if (is_string($_POST[$key])) {
				if ($key != 'cmd' && $key != 'mapsharesetting') { $_POST[$key] = htmlspecialchars((string)$_POST[$key], ENT_QUOTES, 'UTF-8'); }
			}
		}
	}
}

if (implode(array_keys($_GET)) != '') {
	foreach (array_keys($_GET) as $key) { 
		if (!is_null($_GET[$key]) && !is_string($_GET[$key]) && !is_numeric($_GET[$key])) {
			header('location: ' . $_SERVER['SCRIPT_NAME']);
			exit;
		} else {
			if (is_string($_GET[$key])) {
				if ($key == 'orderby' || $key == 'page' || $key == 'node' || $key == 'domain' || $key == 'computerip' || $key == 'executorport' || $key == 'cid' || $key == 'osversion' || $key == 'idprocess' || $key == 'confirmdeploy' || $key == 'results' || $key == 'remmetering' || $key == 'changegroup' || $key == 'pid' || $key == 'cpucount' || $key == 'manufacturer' || $key == 'model' || $key == 'domain' || $key == 'openeditconf' || $key == 'wminamespace' || $key == 'wmiclasses' || $key == 'phptimeout' || $key == 'findtype' || $key == 'message' || $key == 'osmver' || $key == 'results' || $key == 'export' || $key == 'source') { $_GET[$key] = str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$_GET[$key], ENT_QUOTES, 'UTF-8')))); }
			}
		}
	}
}

if (!isset($_SESSION['token'])) {
	$_SESSION['token'] = md5(rand(1000000,9999999) . $envcomputername . $_SERVER['SCRIPT_NAME']);
}
$sessiontoken = $_SESSION['token'];

if (!isset($_SESSION['tokenl'])) {
	$_SESSION['tokenl'] = $_SESSION['token'];
}

$wmi = new COM('winmgmts://');

header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');

if (extension_loaded('zlib')) { ob_start('ob_gzhandler'); }

$config_coresrv = $euryscoinstallpath . '\\conf\\config_core.xml';
$config_executorsrv = $euryscoinstallpath . '\\conf\\config_executor.xml';
$config_server = $euryscoinstallpath . '\\conf\\config_server.xml';

if (!file_exists($config_settings)) {
	$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<timezonesetting>UTC</timezonesetting>' . "\n" . '		<systeminforrsetting>15000</systeminforrsetting>' . "\n" . '		<programsrrsetting>30000</programsrrsetting>' . "\n" . '		<processesrrsetting>10000</processesrrsetting>' . "\n" . '		<servicesrrsetting>10000</servicesrrsetting>' . "\n" . '		<schedulerrrsetting>15000</schedulerrrsetting>' . "\n" . '		<eventsrrsetting>10000</eventsrrsetting>' . "\n" . '		<nagiosrrsetting>30000</nagiosrrsetting>' . "\n" . '		<netstatrrsetting>15000</netstatrrsetting>' . "\n" . '		<registryrrsetting>15000</registryrrsetting>' . "\n" . '		<explorerrrsetting>90000</explorerrrsetting>' . "\n" . '		<wmiexprrsetting>Hold</wmiexprrsetting>' . "\n" . '		<tailrrsetting>60000</tailrrsetting>' . "\n" . '		<nodesstatusrrsetting>15000</nodesstatusrrsetting>' . "\n" . '		<nodesrrsetting>15000</nodesrrsetting>' . "\n" . '		<statuscsetting>1</statuscsetting>' . "\n" . '		<processescsetting>2</processescsetting>' . "\n" . '		<servicescsetting>2</servicescsetting>' . "\n" . '		<taskscsetting>4</taskscsetting>' . "\n" . '		<eventscsetting>60</eventscsetting>' . "\n" . '		<nagioscsetting>4</nagioscsetting>' . "\n" . '		<netstatcsetting>2</netstatcsetting>' . "\n" . '		<programscsetting>120</programscsetting>' . "\n" . '		<inventorycsetting>240</inventorycsetting>' . "\n" . '		<nodesclearsetting>1296000</nodesclearsetting>' . "\n" . '		<nodescommandblacklist>chkdsk,bcedit,del,dism,diskpart,format,nslookup,fsutil,move,net user,powershell.*clear,powershell.*disconnect,powershell.*dismount,powershell.*remove,powershell.*stop-computer,rd,ren,rename,rmdir,servercanagercmd.*install,servercanagercmd.*remove,sc config,sc delete,shutdown,wmic,reg,regedit,takeown,icacls,cacls</nodescommandblacklist>' . "\n" . '		<localcommandblacklist>chkdsk,bcedit,dism,diskpart,format,nslookup,fsutil,net user,powershell.*clear,powershell.*disconnect,powershell.*dismount,powershell.*remove,powershell.*stop-computer,servercanagercmd.*install,servercanagercmd.*remove,sc config,sc delete,shutdown,wmic,reg,regedit,takeown,icacls,cacls,repadmin,dcpromo</localcommandblacklist>' . "\n" . '		<tailextsetting>, ,txt,log,csv,cfg,inf,ini,vbs,bat,cmd,htm,html,xml,css,ascx,asp,aspx,php</tailextsetting>' . "\n" . '		<zipextsetting>7z,arj,bz2,bzip2,cab,cpio,deb,dmg,fat,gz,gzip,hfs,iso,lha,lzh,lzma,ntfs,rar,rpm,squashfs,swm,tar,taz,tbz,tbz2,tgz,tpz,txz,vhd,wim,xar,xz,z,zip</zipextsetting>' . "\n" . '		<uploadextsetting>*</uploadextsetting>' . "\n" . '		<uploadsetting>200</uploadsetting>' . "\n" . '		<mapsharesetting></mapsharesetting>' . "\n" . '		<loginmessagesetting></loginmessagesetting>' . "\n" . '		<wmiexplorerhidevalues>Enable</wmiexplorerhidevalues>' . "\n" . '		<roottaskssetting>Enable</roottaskssetting>' . "\n" . '		<changepwdlocalsetting>Disabled</changepwdlocalsetting>' . "\n" . '	</settings>' . "\n" . '</config>';
	$writexml = fopen($config_settings, 'w');
	fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
	fclose($writexml);
}

if (file_exists($config_settings)) {
	$xmlserver = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_settings, true)))));
	$timezonesetting = $xmlserver->settings->timezonesetting;
	$systeminforrsetting = $xmlserver->settings->systeminforrsetting;
	$programsrrsetting = $xmlserver->settings->programsrrsetting;
	$processesrrsetting = $xmlserver->settings->processesrrsetting;
	$servicesrrsetting = $xmlserver->settings->servicesrrsetting;
	$schedulerrrsetting = $xmlserver->settings->schedulerrrsetting;
	$eventsrrsetting = $xmlserver->settings->eventsrrsetting;
	$nagiosrrsetting = $xmlserver->settings->nagiosrrsetting;
	$netstatrrsetting = $xmlserver->settings->netstatrrsetting;
	$registryrrsetting = $xmlserver->settings->registryrrsetting;
	$explorerrrsetting = $xmlserver->settings->explorerrrsetting;
	$wmiexprrsetting = $xmlserver->settings->wmiexprrsetting;
	$nodesclearsetting = $xmlserver->settings->nodesclearsetting;
	$tailrrsetting = $xmlserver->settings->tailrrsetting;
	$nodesstatusrrsetting = $xmlserver->settings->nodesstatusrrsetting;
	$nodesrrsetting = $xmlserver->settings->nodesrrsetting;
	$statuscsetting = $xmlserver->settings->statuscsetting;
	$processescsetting = $xmlserver->settings->processescsetting;
	$servicescsetting = $xmlserver->settings->servicescsetting;
	$taskscsetting = $xmlserver->settings->taskscsetting;
	$eventscsetting = $xmlserver->settings->eventscsetting;
	$nagioscsetting = $xmlserver->settings->nagioscsetting;
	$netstatcsetting = $xmlserver->settings->netstatcsetting;
	$programscsetting = $xmlserver->settings->programscsetting;
	$inventorycsetting = $xmlserver->settings->inventorycsetting;
	$nodescommandblacklist = $xmlserver->settings->nodescommandblacklist;
	$localcommandblacklist = $xmlserver->settings->localcommandblacklist;
	$tailextsetting = $xmlserver->settings->tailextsetting;
	$zipextsetting = $xmlserver->settings->zipextsetting;
	$uploadextsetting = $xmlserver->settings->uploadextsetting;
	$uploadsetting = $xmlserver->settings->uploadsetting;
	$mapsharesetting = $xmlserver->settings->mapsharesetting;
	$loginmessagesetting = $xmlserver->settings->loginmessagesetting;
	$wmiexplorerhidevalues = $xmlserver->settings->wmiexplorerhidevalues;
	$roottaskssetting = $xmlserver->settings->roottaskssetting;
	$changepwdlocalsetting = $xmlserver->settings->changepwdlocalsetting;
}

date_default_timezone_set($timezonesetting);

set_time_limit(300);

?>