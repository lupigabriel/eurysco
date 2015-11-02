<?php

ini_set('display_errors', 0);

include('/include/init.php');

$eurysco_coresrv = 'euryscoCore';
$eurysco_coreport = 0;
$eurysco_corephpport = 0;
if (file_exists($config_coresrv)) {
	$xmlcore = simplexml_load_file($config_coresrv);
	$eurysco_coresrv = $xmlcore->settings->coreservicename;
	$eurysco_coreport = $xmlcore->settings->corelisteningport;
	$eurysco_corephpport = $xmlcore->settings->corephpport;
}
$corelink = 'https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport;

$eurysco_executorsrv = 'euryscoExecutor';
$eurysco_executorport = 0;
$eurysco_executorphpport = 0;
if (file_exists($config_executorsrv)) {
	$xmlexecutor = simplexml_load_file($config_executorsrv);
	$eurysco_executorsrv = $xmlexecutor->settings->executorservicename;
	$eurysco_executorport = $xmlexecutor->settings->executorlisteningport;
	$eurysco_executorphpport = $xmlexecutor->settings->executorphpport;
}
$executorlink = 'https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport;

$eurysco_serversrv = 'euryscoServer';
$eurysco_serverport = 0;
$eurysco_serverphpport = 0;
if (file_exists($config_server)) {
	$xmlserver = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_server, true)))));
	$eurysco_serversrv = $xmlserver->settings->serverservicename;
	$eurysco_serverport = $xmlserver->settings->serverlisteningport;
	$eurysco_serverphpport = $xmlserver->settings->serverphpport;
}
$serverlink = 'https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_serverport;

if ($_SERVER['SCRIPT_NAME'] == '/index.php' && $eurysco_corephpport == 0 && $eurysco_executorphpport == 0) { header('location: /executor.php'); }
if ($_SERVER['SCRIPT_NAME'] == '/executor.php' && $eurysco_corephpport == 0 && $eurysco_executorphpport != 0) { header('location: ' . $executorlink . '/core.php'); }
if ($_SERVER['SCRIPT_NAME'] == '/executor.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/server.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/core.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/index.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/about.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nodes.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nodes_nagios.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nodes_netstat.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nodes_eventviewer.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nodes_inventory.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nodes_processes.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nodes_programs.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nodes_scheduler.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nodes_services.php' && $_SERVER['SERVER_PORT'] != $eurysco_corephpport && $eurysco_corephpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/nagios.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/netstat.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/inventory.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/programs.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/wmiexplorer.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/shutdown.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/processes.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/services.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/scheduler.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/eventviewer.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/registry.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/cli.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/explorer.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/tail.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/7zip.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/settings.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/users.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }
if ($_SERVER['SCRIPT_NAME'] == '/audit.php' && $_SERVER['SERVER_PORT'] != $eurysco_executorphpport && $eurysco_executorphpport != 0) { header('location: https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . $_SERVER['REQUEST_URI']); exit; }



$eurysco_core_status = 'Not Exist';
$eurysco_executor_status = 'Not Exist';
$eurysco_agent_status = 'Not Exist';
$nagios_agent_status = 'Not Exist';
$nrpepathname = '';
$nscppathname = '';
$wmisclass = $wmi->ExecQuery("SELECT Name, PathName, State FROM Win32_Service WHERE Name = '" . $eurysco_coresrv . "' OR Name = '" . $eurysco_executorsrv . "' OR Name = '" . $eurysco_agentsrv . "' OR (Name = 'NRPE_NT' AND State = 'Running') OR (Name = 'nscp' AND State = 'Running')");
foreach($wmisclass as $obj) {
	if ($obj->Name == $eurysco_coresrv) { $eurysco_core_status = $obj->State; }
	if ($obj->Name == $eurysco_executorsrv) { $eurysco_executor_status = $obj->State; }
	if ($obj->Name == $eurysco_agentsrv) { $eurysco_agent_status = $obj->State; }
	if ($obj->Name == 'NRPE_NT') {
		$nagios_agent_status = $obj->State;
		$nrpepathname = urlencode($obj->PathName);
	}
	if ($obj->Name == 'nscp') {
		$nagios_agent_status = $obj->State;
		$nscppathname = urlencode($obj->PathName);
	}
}

if (file_exists($config_coresrv) && $eurysco_core_status != 'Not Exist') {
	if ($eurysco_core_status == 'Running') {
	$corestatus = 'run';
	$corelinkst = 1;
	} else {
	$corestatus = 'nrn';
	$corelinkst = 0;
	}
} else {
	$corestatus = 'cfg';
	$corelinkst = 0;
}

if (file_exists($config_executorsrv) && $eurysco_executor_status != 'Not Exist') {
	if ($eurysco_executor_status == 'Running') {
	$executorstatus = 'run';
	$executorlinkst = 1;
	} else {
	$executorstatus = 'nrn';
	$executorlinkst = 0;
	}
} else {
	$executorstatus = 'cfg';
	$executorlinkst = 0;
}

if (file_exists($config_server)) {
	$eurysco_server_status = 'Not Exist';
	$wmisclass = $wmi->ExecQuery("SELECT Name, State FROM Win32_Service WHERE Name = '" . $eurysco_serversrv . "'");
	foreach($wmisclass as $obj) {
		if ($obj->Name == $eurysco_serversrv) { $eurysco_server_status = $obj->State; }
	}
	if ($eurysco_server_status != 'Not Exist') {
		if ($eurysco_server_status == 'Running') {
		$serverstatus = 'run';
		$serverlinkst = 1;
		} else {
		$serverstatus = 'nrn';
		$serverlinkst = 0;
		}
	} else {
		$serverstatus = 'cfg';
		$serverlinkst = 0;
	}
} else {
	$serverstatus = 'cfg';
	$serverlinkst = 0;
}
$_SESSION['serverstatus'] = $serverstatus;

$connexitcode = 'Disconnected';
if (file_exists($config_agentsrv) && $eurysco_agent_status != 'Not Exist') {
	if ($eurysco_agent_status == 'Running') {
		$agentstatus = 'run';
		$agentlinkst = 1;
		$agentprocst = '';
		$wmisclass = $wmi->ExecQuery("SELECT Caption FROM Win32_Process WHERE Caption = 'php_eurysco_agent.exe'");
		foreach($wmisclass as $obj) {
			$agentprocst = $obj->Caption;
		}
		if (file_exists(str_replace('\\core', '\\agent\\temp\\agent.status', $_SERVER['DOCUMENT_ROOT'])) && $agentprocst == 'php_eurysco_agent.exe') {
			$f = fopen(str_replace('\\core', '\\agent\\temp\\agent.status', $_SERVER['DOCUMENT_ROOT']), 'r');
			$connexitcode = fgets($f);
			fclose($f);
			if (strpos($connexitcode, 'Connection Success') > 0) { $agentstatus = 'con'; $_SESSION['agentstatus'] = 'con'; }
		}
	} else {
		$agentstatus = 'nrn';
		$agentlinkst = 0;
	}
} else {
	$agentstatus = 'cfg';
	$agentlinkst = 0;
}

if (isset($_GET['logout'])) {
	$_SESSION['logoutstatus'] = 'logout';
	header('location: https://' . $_SERVER['HTTP_HOST'] . '/' . str_replace('/', '', $_SERVER['PHP_SELF']));
}

$sessiontimeoutsec = 900;
if (!isset($_SESSION['session_timeout'])) {
	$_SESSION['session_timeout'] = gmdate('Y-m-d H:i:s', time());
} else {
	if (strtotime(gmdate('Y-m-d H:i:s', time())) - strtotime($_SESSION['session_timeout']) > $sessiontimeoutsec) {
		$_SESSION['logoutstatus'] = 'timeout';
		header('location: https://' . $_SERVER['HTTP_HOST'] . '/' . str_replace('/', '', $_SERVER['PHP_SELF']));
		exit;
	} else {
		$_SESSION['session_timeout'] = gmdate('Y-m-d H:i:s', time());
	}
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html" lang="en">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="eurysco">
    <meta name="author" content="eurysco">
    <meta name="keywords" content="eurysco">
	<meta name="msapplication-TileImage" content="/img/ico/ms-icon-144x144.png">

    <link href="/css/modern.css" rel="stylesheet">
    <link href="/css/modern-responsive.css" rel="stylesheet">
    <link href="/css/site.css" rel="stylesheet" type="text/css">
	<link href="/autosuggest/css/autosuggest_inquisitor.css" rel="stylesheet" type="text/css" media="screen" charset="utf-8" />
	<link rel="apple-touch-icon" sizes="57x57" href="/img/ico/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/img/ico/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/img/ico/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/img/ico/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/img/ico/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/img/ico/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/img/ico/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/img/ico/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/img/ico/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/img/ico/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/img/ico/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/img/ico/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/img/ico/favicon-16x16.png">
	<link rel="manifest" href="/img/ico/manifest.json">
	<style type="text/css">
	.rowselect:hover {
		background-color: #EFF2FA !important;
	}
	</style>
	<style type="text/css">
	.rowselectsrv:hover {
		background-color: #F5F3FB !important;
	}
	</style>

    <script type="text/javascript" src="/js/assets/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="/autosuggest/js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
    <script type="text/javascript" src="/js/assets/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" src="/js/assets/moment.js"></script>
    <script type="text/javascript" src="/js/modern/dropdown.js"></script>
    <script type="text/javascript" src="/js/modern/accordion.js"></script>
    <script type="text/javascript" src="/js/modern/buttonset.js"></script>
    <script type="text/javascript" src="/js/modern/input-control.js"></script>
    <script type="text/javascript" src="/js/modern/pagecontrol.js"></script>
    <script type="text/javascript" src="/js/modern/calendar.js"></script>
    <script type="text/javascript" src="/js/modern/dialog.js"></script>
    <script type="text/javascript" src="/js/modern/pagelist.js"></script>
    <script type="text/javascript" src="/js/assets/jquery.timeentry.js"></script>
	<script type="text/javascript" src="/plupload/js/plupload.full.min.js"></script>

    <?php
	
	$exppwdmessage = '';
	if (isset($_POST['exppwduserpsw']) && isset($_POST['exppwduserpswc']) && isset($_POST['oldpwduserpsw'])) {
	
		$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_SESSION['username'] . '.xml', true)))));
		$exppwduserpsw = '';

		if ($_POST['exppwduserpsw'] == '' || $_POST['exppwduserpswc'] == '' || $_POST['oldpwduserpsw'] == '') {
			$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">all fields must be completed</td></tr>';
		} else {
			if (strtolower($_SESSION['username']) == strtolower($_POST['exppwduserpsw'])) {
				$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">username and password<br />cannot be equal</td></tr>';
			} else {
				if ($_POST['exppwduserpsw'] != $_POST['exppwduserpswc']) {
					$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">confirm password mismatch</td></tr>';
				} else {
					if (strlen($_POST['exppwduserpsw']) < 8) {
						$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">password must be at least<br />8 characters</td></tr>';
					} else {
						if (!preg_match('/[\\\\\'\"\!\.\:\;\[\]\^\$\%\&\*\(\)\}\{\@\#\~\?\/\,\|\=\_\+\-]/', $_POST['exppwduserpsw']) || !preg_match('/[a-z]/', $_POST['exppwduserpsw']) || !preg_match('/[A-Z]/', $_POST['exppwduserpsw']) || !preg_match('/[0-9]/', $_POST['exppwduserpsw'])) {
							$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">password must be contain<br />number <strong>0~9</strong>, lower <strong>a~z</strong>, upper <strong>A~Z</strong><br />and special characters<br /><strong>\\\'",.:;!?^$%&()[]{}@#~\/|=*+-_</strong></td></tr>';
						} else {
							if (preg_match('/[^a-zA-Z0-9\\\\\'\"\!\.\:\;\[\]\^\$\%\&\*\(\)\}\{\@\#\~\?\/\,\|\=\_\+\-]/', $_POST['exppwduserpsw'])) {
								$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">special characters allowed<br /><strong>\\\'",.:;!?^$%&()[]{}@#~\/|=*+-_</strong></td></tr>';
							} else {
								$mcrykey = pack('H*', hash('sha256', hash('sha512', $_SESSION['username'] . $_SESSION['usertype'])));
								$exppwduserpsw = base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykey, md5($_SESSION['username'] . ':' . $realm . ':' . $_POST['exppwduserpsw']), MCRYPT_MODE_CBC, $iv));
								$passwchk = $userxml->settings->passwchk;
								if (preg_match('/' . md5(substr(hash('whirlpool', hash('sha256', hash('sha384', hash('sha512', $_SESSION['username'] . ':' . $realm . ':' . $_POST['exppwduserpsw'])))), 0, -1)) . '/', base64_decode($passwchk))) {
									$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">new password must be different<br />than your old password</td></tr>';
								} else {
									if (strlen(base64_decode($passwchk)) > 416) { $passwchk = base64_encode(substr(base64_decode($passwchk), 0, -32)); }
									$passwchk = base64_encode(md5(substr(hash('whirlpool', hash('sha256', hash('sha384', hash('sha512', $_SESSION['username'] . ':' . $realm . ':' . $_POST['exppwduserpsw'])))), 0, -1)) . base64_decode($passwchk));
								}
							}
						}
					}
				}
			}
		}
		
		if ($exppwdmessage == '') {
			$nextexppwdday = $_SESSION['usersett']['pwdexp'];
			if ($_SESSION['usertype'] == 'Administrators') { $nextexppwdday = 7; }
			if ($_SESSION['usertype'] == 'Auditors') { $nextexppwdday = 28; }
			if ($_SESSION['usertype'] == 'Operators') { $nextexppwdday = 14; }
			if ($_SESSION['usertype'] == 'Users') { $nextexppwdday = 21; }
			$xml = '<?xml version="1.0"?>' . "\n" . '<config>' . "\n" . '	<settings>' . "\n" . '		<username>' . $_SESSION['username'] . '</username>' . "\n" . '		<usertype>' . $userxml->settings->usertype . '</usertype>' . "\n" . '		<userauth>' . $userxml->settings->userauth . '</userauth>' . "\n" . '		<password>' . $exppwduserpsw . '</password>' . "\n" . '		<passwchk>' . $passwchk . '</passwchk>' . "\n" . '		<passwlck>' . md5($exppwduserpsw) . '</passwlck>' . "\n" . '		<expiration>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(date('Y-m-d H:i:s', strtotime(date('Y') . '-' . date('m') . '-' . date('d') . ' ' . date('H') . ':' . date('i') . ':' . date('s') . ' + ' . $nextexppwdday . ' days'))))))) . '</expiration>' . "\n" . '	</settings>' . "\n" . '</config>';
			$authresponse = '';
			$authaddress = '';
			if ($_SESSION['userauth'] == 'Distributed' && $agentstatus == 'con' && $serverstatus != 'run') {
				$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
				$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
				$mcrykey = pack('H*', hash('sha256', hash('sha512', 'vNqgi_R1QX%C;z-724p4lFHm*?7c!e2%vG9tp+-*@#%=?!_;./' . hash('tiger128,4', $eurysco_serverconport) . '-*@#%=?!_;./-f;bTh2XXqW%Zs%88+/-7pVb;X')));
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10000);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($ch, CURLOPT_USERPWD, hash('sha256', $eurysco_serverconport . 'euryscoServer' . $eurysco_serverconport) . ':' . mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($eurysco_serverconpassword), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($eurysco_serverconpassword), 0, $iv_size)));
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
				curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/userscp.php');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				$data = array(
					'usr' => base64_encode($_SESSION['username']),
					'pwd' => base64_encode($_POST['oldpwduserpsw']),
					'xml' => base64_encode($xml)
				);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				$authresponse = curl_exec($ch);
				curl_close($ch);
			}
			if (!strpos($_SERVER['HTTP_HOST'], ':' . $eurysco_coreport) && $authaddress == '') { $authaddress = 'https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_coreport . '/userscp.php'; }
			if (!strpos($_SERVER['HTTP_HOST'], ':' . $eurysco_executorport) && $authaddress == '') { $authaddress = 'https://' . preg_replace('/:.*/', ':', $_SERVER['HTTP_HOST']) . $eurysco_executorport . '/userscp.php'; }
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10000);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_USERPWD, $_SESSION['username'] . ':' . $_POST['oldpwduserpsw']);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($ch, CURLOPT_URL, $authaddress);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			$data = array(
				'ath' => base64_encode('Local'),
				'xml' => base64_encode($xml)
			);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			if ($authresponse == '') { $authresponse = curl_exec($ch); } else { curl_exec($ch); }
			curl_close($ch);
			if ($authresponse === md5(base64_encode($xml))) {
				$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user management     self password changed';
				if (isset($_POST['changepassword'])) { header('location: ' . $_SERVER['REQUEST_URI']); }
			} else {
				if (preg_match_all('/locked out/', strtolower($authresponse))) {
					$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">account locked out</td></tr>';
				} else {
					if (preg_match_all('/user disabled/', strtolower($authresponse))) {
						$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">account disabled</td></tr>';
					} else {
						$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">authentication error</td></tr>';
					}
				}
			}
		}

	}
	
	$passwordexpired = 0;
	$exppwdtitle = '';
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_SESSION['username'] . '.xml')) {
		$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_SESSION['username'] . '.xml', true)))));
		$usersexpiration = base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($userxml->settings->expiration)))));
		if ((strtotime(date('Y-m-d H:i:s', strtotime($usersexpiration . ' + ' . $_SESSION['usersett']['disable'] . ' days'))) - strtotime(date('Y-m-d H:i:s'))) < 0 && $userxml->settings->expiration != base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('2000-01-01 00:00:00'))))) && $userxml->settings->usertype != hash('sha512', $userxml->settings->username . 'Administrators') && $userxml->settings->usertype != hash('sha512', $userxml->settings->username . 'Auditors') && $userxml->settings->usertype != hash('sha512', $userxml->settings->username . 'Operators') && $userxml->settings->usertype != hash('sha512', $userxml->settings->username . 'Users')) {
			$_SESSION['logoutdsb'] = 'logoutdsb'; $_SESSION['logoutstatus'] = 'logout'; header('location: https://' . $_SERVER['HTTP_HOST'] . '/' . str_replace('/', '', $_SERVER['PHP_SELF'])); exit;
		}
		if (isset($_POST['changepassword'])) {
			$exppwdtitle = 'Password <strong>Change</strong>';
		}
		if ((strtotime($usersexpiration) - strtotime(date('Y-m-d H:i:s'))) < 0) {
			$passwordexpired = 1;
			if ($usersexpiration == '2000-01-01 00:00:00') {
				$exppwdtitle = 'Password <strong>Must Be Change</strong>';
			} else {
				$exppwdtitle = 'Password <strong>Expired</strong>';
			}
		}
	}
	
	$pswchangeallow = 1;
	$pswinputfields = '';
	if (($passwordexpired == 1 || isset($_POST['changepassword'])) && $changepwdlocalsetting == 'Enable') {
		if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1' && $_SERVER['HTTP_X_FORWARDED_FOR'] != '::1') {
			$pswchangeallow = 0;
			$pswinputfields = ' disabled="disabled"';
			$exppwdmessage = '<tr><td align="center" colspan="2" style="background-color:#B91D47; color:#FFFFFF; font-size:12px;">change password is allowed<br />only for local connection</td></tr>';
		}
	}

	?>
	<script type="text/javascript">
		function exppwduser(){
			$.Dialog({
				'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-user" style="position:inherit;"></div>&nbsp; <?php echo $exppwdtitle; ?></span>',
				'content'     : '<form id="exppwduserform" name="exppwduserform" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td style="font-size:12px;" align="center" colspan="2"><strong><?php echo $_SESSION['username']; ?></strong></td></tr><?php echo $exppwdmessage; ?><tr><?php if ($_SESSION['userauth'] == 'Distributed' && $agentstatus != 'con' && $serverstatus != 'run') { ?><tr><td align="center" colspan="2" style="background-color:#C97D15; color:#FFFFFF; font-size:12px;">if the agent is not connected<br />the password will not be synced</td></tr><?php } ?><td style="font-size:12px;">Old Password:</td><td style="font-size:12px;"><input type="password" id="oldpwduserpsw" name="oldpwduserpsw" placeholder="&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px; padding-left:4px; padding-right:4px; font-size:12px;"<?php echo $pswinputfields ?> value=""></td></tr><tr><td style="font-size:12px;">New Password:</td><td style="font-size:12px;"><input type="password" id="exppwduserpsw" name="exppwduserpsw" placeholder="&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px; padding-left:4px; padding-right:4px; font-size:12px;"<?php echo $pswinputfields ?> value=""></td></tr><tr><td style="font-size:12px;">Confirmation:</td><td style="font-size:12px;"><input type="password" id="exppwduserpswc" name="exppwduserpswc" placeholder="&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px; padding-left:4px; padding-right:4px; font-size:12px;"<?php echo $pswinputfields ?> value=""></td></tr><tr><td style="font-size:12px;">Type:</td><td style="font-size:12px;"><?php echo $_SESSION['usertype']; ?></td></tr></table><?php if (isset($_POST['changepassword'])) { echo '<input type="hidden" id="changepassword" name="changepassword" value="" />'; } ?></form>',
				'draggable'   : true,
				'overlay'     : true,
				'closeButton' : false,
				'buttonsAlign': 'center',
				'keepOpened'  : true,
				'position'    : {
					'offsetY' : '50px',
					'offsetX' : ((document.documentElement.offsetWidth / 2) - 138) + 'px'
				},
				'buttons'     : {
					<?php if ($pswchangeallow == 1) { ?>
					'Change'     : {
					'action': function(){
							document.getElementById("exppwduserform").submit();
						}
					},
					<?php } ?>
					<?php if (isset($_POST['changepassword']) && $passwordexpired == 0) { ?>
					'Cancel'     : {
					'action': function(){
						}
					},
					<?php } else { ?>
					'Logout'     : {
					'action': function(){
							window.location = "?logout";
						}
					},
					<?php } ?>
				}
			});
		};
	</script>
    
	<style>
	@font-face {
		font-family:CLIfont;
		src: url("/fonts/the_one_true_font_system_8x12.eot?") format("eot"),url("/fonts/the_one_true_font_system_8x12.woff") format("woff"),url("/fonts/the_one_true_font_system_8x12.ttf") format("truetype"),url("/fonts/the_one_true_font_system_8x12.svg#The-One-True-Font-(System-8x12)") format("svg");
		font-weight:normal;
		font-style:normal;
	}

	div {
		font-family:CLIfont;
	}
	</style> 

    <title>&nbsp;<?php echo $envcomputername; ?>&nbsp;|&nbsp;eurysco <?php include("version.phtml"); ?></title>
</head>
<body class="metrouicss" <?php if (isset($_POST['changepassword']) || $passwordexpired == 1) { echo 'onLoad="javascript:exppwduser();"'; } ?>>