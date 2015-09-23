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
} else {
	date_default_timezone_set('UTC');
}

$config_agentsrv = 'conf\\config_agent.xml';
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

$wmi = new COM('winmgmts://');

header('Content-Type: text/html; charset=utf-8');

if (extension_loaded('zlib')) { ob_start('ob_gzhandler'); }

$config_coresrv = 'conf\\config_core.xml';
$config_executorsrv = 'conf\\config_executor.xml';
$config_server = 'conf\\config_server.xml';

if (!file_exists($config_settings)) {
	$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<timezonesetting>UTC</timezonesetting>' . "\n" . '		<systeminforrsetting>15000</systeminforrsetting>' . "\n" . '		<programsrrsetting>30000</programsrrsetting>' . "\n" . '		<processesrrsetting>10000</processesrrsetting>' . "\n" . '		<servicesrrsetting>10000</servicesrrsetting>' . "\n" . '		<schedulerrrsetting>15000</schedulerrrsetting>' . "\n" . '		<eventsrrsetting>10000</eventsrrsetting>' . "\n" . '		<nagiosrrsetting>30000</nagiosrrsetting>' . "\n" . '		<netstatrrsetting>15000</netstatrrsetting>' . "\n" . '		<registryrrsetting>15000</registryrrsetting>' . "\n" . '		<explorerrrsetting>90000</explorerrrsetting>' . "\n" . '		<wmiexprrsetting>Hold</wmiexprrsetting>' . "\n" . '		<tailrrsetting>60000</tailrrsetting>' . "\n" . '		<nodesstatusrrsetting>15000</nodesstatusrrsetting>' . "\n" . '		<nodesrrsetting>15000</nodesrrsetting>' . "\n" . '		<statuscsetting>1</statuscsetting>' . "\n" . '		<processescsetting>2</processescsetting>' . "\n" . '		<servicescsetting>2</servicescsetting>' . "\n" . '		<taskscsetting>4</taskscsetting>' . "\n" . '		<eventscsetting>60</eventscsetting>' . "\n" . '		<nagioscsetting>4</nagioscsetting>' . "\n" . '		<netstatcsetting>2</netstatcsetting>' . "\n" . '		<programscsetting>120</programscsetting>' . "\n" . '		<inventorycsetting>240</inventorycsetting>' . "\n" . '		<nodesclearsetting>1296000</nodesclearsetting>' . "\n" . '		<nodescommandblacklist>chkdsk,bcedit,del,dism,diskpart,format,fsutil,move,net user,powershell.*clear,powershell.*disconnect,powershell.*dismount,powershell.*remove,powershell.*stop-computer,rd,ren,rename,rmdir,servercanagercmd.*install,servercanagercmd.*remove,sc config,sc delete,shutdown,wmic,reg,regedit,takeown,icacls,cacls</nodescommandblacklist>' . "\n" . '		<tailextsetting>, ,txt,log,csv,cfg,inf,ini,vbs,bat,cmd,htm,html,xml,css,ascx,asp,aspx,php</tailextsetting>' . "\n" . '		<zipextsetting>7z,arj,bz2,bzip2,cab,cpio,deb,dmg,fat,gz,gzip,hfs,iso,lha,lzh,lzma,ntfs,rar,rpm,squashfs,swm,tar,taz,tbz,tbz2,tgz,tpz,txz,vhd,wim,xar,xz,z,zip</zipextsetting>' . "\n" . '		<uploadextsetting>*</uploadextsetting>' . "\n" . '		<uploadsetting>200</uploadsetting>' . "\n" . '		<mapsharesetting></mapsharesetting>' . "\n" . '		<wmiexplorerhidevalues>Enable</wmiexplorerhidevalues>' . "\n" . '		<roottaskssetting>Enable</roottaskssetting>' . "\n" . '		<changepwdlocalsetting>Disabled</changepwdlocalsetting>' . "\n" . '	</settings>' . "\n" . '</config>';
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
	$tailextsetting = $xmlserver->settings->tailextsetting;
	$zipextsetting = $xmlserver->settings->zipextsetting;
	$uploadextsetting = $xmlserver->settings->uploadextsetting;
	$uploadsetting = $xmlserver->settings->uploadsetting;
	$mapsharesetting = $xmlserver->settings->mapsharesetting;
	$wmiexplorerhidevalues = $xmlserver->settings->wmiexplorerhidevalues;
	$roottaskssetting = $xmlserver->settings->roottaskssetting;
	$changepwdlocalsetting = $xmlserver->settings->changepwdlocalsetting;
}

date_default_timezone_set($timezonesetting);

set_time_limit(300);

?>