<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/agent.php')) { exit; }

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['agentconfig'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

$eurysco_agentsrv = 'euryscoAgent';
if (file_exists($config_agentsrv)) {
	$xmlagent = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_agentsrv, true)))));
}

$eurysco_agent_status = 'Not Exist';
$wmisclass = $wmi->ExecQuery("SELECT Name, State FROM Win32_Service WHERE Name LIKE '" . $eurysco_agentsrv . "'");
foreach($wmisclass as $obj) {
	if ($obj->Name == $eurysco_agentsrv) { $eurysco_agent_status = $obj->State; }
}

$connexitcode = 'Disconnected';
if (file_exists($config_agentsrv) && $eurysco_agent_status != 'Not Exist') {
	if ($eurysco_agent_status == 'Running') {
		$agentstatus = 'run';
		$agentlinkst = 1;
		$agentprocst = '';
		$wmisclass = $wmi->ExecQuery("SELECT Caption FROM Win32_Process WHERE Caption LIKE 'php_eurysco_agent.exe'");
		foreach($wmisclass as $obj) {
			$agentprocst = $obj->Caption;
		}
		if (file_exists($euryscoinstallpath . '\\agent\\temp\\agent.status') && $agentprocst == 'php_eurysco_agent.exe') {
			$f = fopen($euryscoinstallpath . '\\agent\\temp\\agent.status', 'r');
			$connexitcode = fgets($f);
			fclose($f);
			if (strpos($connexitcode, 'Connection Success') > 0) { $agentstatus = 'con'; }
		}
	} else {
		$agentstatus = 'nrn';
		$agentlinkst = 0;
	}
} else {
	$agentstatus = 'cfg';
	$agentlinkst = 0;
}

$agentconstatusstyle = ' background-color:#eaeaea;';
if ($agentstatus == 'run') { $agentconstatusstyle = 'font-size:14px; background-color:#b91d47; border-color:#b91d47; color:#FFF;'; }
if ($agentstatus == 'con') { $agentconstatusstyle = 'font-size:14px; background-color:#603cba; border-color:#603cba; color:#FFF;'; }
$serverconnectionresult = '<input type="text" value="' . $connexitcode . '" style="font-size:14px;' . $agentconstatusstyle . '" readonly="readonly" />';

echo json_encode(array('connexitcode'=>$connexitcode,'agentconstatusstyle'=>$agentconstatusstyle));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>