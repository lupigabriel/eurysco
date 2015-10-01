<?php

$badaut = scandir($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\');
if (count($badaut) > 22 && $_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1' && $_SERVER['HTTP_X_FORWARDED_FOR'] != '::1') {
	echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/html" lang="en"><head><meta charset="utf-8"><meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1"><meta name="description" content="eurysco"><meta name="author" content="eurysco"><meta name="keywords" content="eurysco"><link href="css/modern.css" rel="stylesheet"><link href="css/modern-responsive.css" rel="stylesheet"><title>eurysco Logout Completed</title></head><body class="metrouicss"><br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="border:none;" align="center"><a href="/" class="eurysco-bg big page-back"></a>';
	echo '<br />';
	echo '<h2>Forbidden Authentication</h2>';
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped" style="width:280px;">';
	echo '<tr><td colspan="2" style="font-size:12px;" align="center">Only Local Connection is Allowed</td></tr>';
	echo '<tr><td colspan="2" style="font-size:12px;" align="center">Too Many Authentication Failures</td></tr>';
	echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">Not Allowed</div></td></tr>';
	echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session ID:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . substr(hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id()), 0, 32) . '</div></td></tr>';
	echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Hashed IP:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . md5($_SERVER['HTTP_X_FORWARDED_FOR']) . '</div></td></tr>';
	echo '</table>';
	echo '</td></tr></table></body></html>';
	exit;
}

$realm = 'eurysco Authentication';
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

session_save_path($_SERVER['DOCUMENT_ROOT'] . '\\temp');
class EncryptedSessionHandler extends SessionHandler {
	private $key;
	public function __construct($key) {
		$this->key = $key;
	}
	public function read($id) {
		$data = parent::read($id);
		return mcrypt_decrypt(MCRYPT_3DES, $this->key, $data, MCRYPT_MODE_ECB);
	}
	public function write($id, $data) {
		$data = mcrypt_encrypt(MCRYPT_3DES, $this->key, $data, MCRYPT_MODE_ECB);
		return parent::write($id, $data);
	}
}
$handler = new EncryptedSessionHandler('mykey');
session_set_save_handler($handler, true);
session_start();

if (!isset($_SESSION['sessionstatus'])) { $_SESSION['sessionstatus'] = 'active'; }

if ($_SESSION['sessionstatus'] == 'logout') {
	session_regenerate_id(true);
	unset($_SESSION['sessionstatus']);
	unset($_SESSION['ELC']);
	unset($_SESSION['USRLCK']);
	unset($_SESSION['session']);
	unset($_SERVER['PHP_AUTH_DIGEST']);
	$_SESSION = array();
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
	}
	include('/include/unset.php');
	session_unset();
	session_destroy();
	echo '<script type="text/javascript">' . "\n";
	echo '	var xmlhttp;' . "\n";
	echo '	if (window.XMLHttpRequest) {' . "\n";
	echo '		xmlhttp = new XMLHttpRequest();' . "\n";
	echo '}' . "\n";
	echo '// code for IE' . "\n";
	echo 'else if (window.ActiveXObject) {' . "\n";
	echo '	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");' . "\n";
	echo '}' . "\n";
	echo 'if (window.ActiveXObject) {' . "\n";
	echo '// IE clear HTTP Authentication' . "\n";
	echo '	document.execCommand("ClearAuthenticationCache");' . "\n";
	echo '	window.location.href=\'/\';' . "\n";
	echo '} else {' . "\n";
	echo '	xmlhttp.open("GET", \'/path/that/will/return/200/OK\', true, "logout", "logout");' . "\n";
	echo '	xmlhttp.send("");' . "\n";
	echo '	xmlhttp.onreadystatechange = function() {' . "\n";
	echo '		if (xmlhttp.readyState == 4) {window.location.href=\'/\';}' . "\n";
	echo '		}' . "\n";
	echo '	}' . "\n";
	echo '</script>' . "\n";
}

if (isset($_SESSION['sessionstatus'])) {
	$sessionstatus = $_SESSION['sessionstatus'];
} else {
	$sessionstatus = '';
}

if (isset($_SESSION['logoutdsb'])) {
	$logoutdsb = $_SESSION['logoutdsb'];
} else {
	$logoutdsb = '';
}

if (isset($_SESSION['logoutnal'])) {
	$logoutnal = $_SESSION['logoutnal'];
} else {
	$logoutnal = '';
}

if (isset($_SESSION['logoutatl'])) {
	$logoutatl = $_SESSION['logoutatl'];
} else {
	$logoutatl = '';
}

if (isset($_SESSION['logoutstatus'])) {
	$logoutstatus = $_SESSION['logoutstatus'];
} else {
	$logoutstatus = '';
}

if ($sessionstatus == 'logout' || $logoutstatus == 'logout' || $logoutstatus == 'timeout') {
	echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/html" lang="en"><head><meta charset="utf-8"><meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1"><meta name="description" content="eurysco"><meta name="author" content="eurysco"><meta name="keywords" content="eurysco"><link href="css/modern.css" rel="stylesheet"><link href="css/modern-responsive.css" rel="stylesheet"><title>eurysco Logout Completed</title></head><body class="metrouicss"><br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="border:none;" align="center"><div class="eurysco-bg big page-back"></div>';
	echo '<br />';
	if ($logoutstatus == 'logout') {
		if ($logoutdsb == 'logoutdsb') {
			echo '<h2>Account Disabled</h2>Login Denied<br /><br />';
		}
		if ($logoutnal == 'logoutnal') {
			echo '<h2>Account Not Allowed</h2>Login Denied<br /><br />';
		}
		if ($logoutatl == 'logoutatl') {
			echo '<h2>Access Time Limits</h2>Login Denied<br /><br />';
		}
		if ($logoutdsb != 'logoutdsb' && $logoutnal != 'logoutnal' && $logoutatl != 'logoutatl') {
			echo '<h2>Logout Completed</h2>';
		}
	} else {
		echo '<h2>Session Timeout</h2>';
	}
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped" style="width:280px;">';
	echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">Closed</div></td></tr>';
	echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session ID:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . substr(hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id()), 0, 32) . '</div></td></tr>';
	echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Hashed IP:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . md5($_SERVER['HTTP_X_FORWARDED_FOR']) . '</div></td></tr>';
	echo '</table>';
	echo '</td></tr></table></body></html>';
	if ($_SESSION['sessionstatus'] != 'logout') {
		if (isset($_SESSION['agentstatus'])) {
			$agentstatus = $_SESSION['agentstatus'];
		} else {
			$agentstatus = '';
		}
		$config_agentsrv = 'conf\\config_agent.xml';
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
		if ($logoutstatus == 'logout') {
			if ($logoutdsb == 'logoutdsb') {
				$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user status     account disabled: login denied';
			}
			if ($logoutnal == 'logoutnal') {
				$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user status     account not allowed: login denied';
			}
			if ($logoutatl == 'logoutatl') {
				$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user status     access time limits: login denied';
			}
			if ($logoutdsb != 'logoutdsb' && $logoutnal != 'logoutnal' && $logoutatl != 'logoutatl') {
				$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user status     logged out';
			}
		} else {
			$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user status     session timeout';
		}
		include('/auditlog.php');
	}
	$_SESSION['sessionstatus'] = 'logout';
	$_SESSION['logoutstatus'] = '';
	$_SESSION['ELC'] = 0;
	$_SESSION['USRLCK'] = '';
	//header('location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	exit;
}

if (!isset($_SESSION['ELC'])) { $_SESSION['ELC'] = 0; }

if (!isset($_SESSION['USRLCK'])) { $_SESSION['USRLCK'] = ''; }

if (!isset($_SESSION['UID'])) { $_SESSION['UID'] = uniqid(); }

if (!isset($_SESSION['session']) || $_SESSION['session'] != hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id())) {
	
	$users = array();
	$userlist = scandir($_SERVER['DOCUMENT_ROOT'] . '\\users\\');				
	foreach($userlist as $user)
	if(pathinfo($user)['extension'] == 'xml') {
		$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $user, true)))));
		$usersusername = $userxml->settings->username;
		$userspassword = $userxml->settings->password;
		$users["$usersusername"] = "$userspassword";
	}
	
	if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
		if ($_SESSION['ELC'] < 3) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
			$_SESSION['ELC'] = $_SESSION['ELC'] + 1;
		}
		if ((3 - $_SESSION['ELC']) > 0) {
			$sessionstatus = 'Active';
			$loginattempts = '';
		} else {
			$sessionstatus = 'Locked';
			$loginattempts = '<tr><td colspan="2" style="font-size:12px;" align="center">Login Limit Exceeded</td></tr>';
			failBlk ();
		}
		echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/html" lang="en"><head><meta charset="utf-8"><meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1"><meta name="description" content="eurysco"><meta name="author" content="eurysco"><meta name="keywords" content="eurysco"><link href="css/modern.css" rel="stylesheet"><link href="css/modern-responsive.css" rel="stylesheet"><title>eurysco Authentication Required</title></head><body class="metrouicss"><br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="border:none;" align="center"><a href="/" class="eurysco-bg big page-back"></a>';
		echo '<br />';
		echo '<h2>Authentication Required</h2>';
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped" style="width:280px;">';
		echo '<tr><td colspan="2" style="font-size:12px;" align="center">Empty Credentials</td></tr>';
		echo $loginattempts;
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . $sessionstatus . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session ID:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . substr(hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id()), 0, 32) . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Hashed IP:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . md5($_SERVER['HTTP_X_FORWARDED_FOR']) . '</div></td></tr>';
		echo '</table>';
		echo '</td></tr></table></body></html>';
		exit;
	}

	if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$data['username']])) {
		if ($_SESSION['ELC'] < 3) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
			$_SESSION['ELC'] = $_SESSION['ELC'] + 1;
		}
		if ((3 - $_SESSION['ELC']) > 0) {
			$sessionstatus = 'Active';
			$loginattempts = '';
		} else {
			$sessionstatus = 'Locked';
			$loginattempts = '<tr><td colspan="2" style="font-size:12px;" align="center">Login Limit Exceeded</td></tr>';
		}
		echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/html" lang="en"><head><meta charset="utf-8"><meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1"><meta name="description" content="eurysco"><meta name="author" content="eurysco"><meta name="keywords" content="eurysco"><link href="css/modern.css" rel="stylesheet"><link href="css/modern-responsive.css" rel="stylesheet"><title>eurysco Authentication Error</title></head><body class="metrouicss"><br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="border:none;" align="center"><a href="/" class="eurysco-bg big page-back"></a>';
		echo '<br />';
		echo '<h2>Authentication Error</h2>';
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped" style="width:280px;">';
		echo '<tr><td colspan="2" style="font-size:12px;" align="center">Wrong Credentials</td></tr>';
		echo $loginattempts;
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . $sessionstatus . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session ID:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . substr(hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id()), 0, 32) . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Hashed IP:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . md5($_SERVER['HTTP_X_FORWARDED_FOR']) . '</div></td></tr>';
		echo '</table>';
		echo '</td></tr></table></body></html>';
		failBlk ();
		exit;
	}

	if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $data['username'] . '.xml')) {
		echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/html" lang="en"><head><meta charset="utf-8"><meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1"><meta name="description" content="eurysco"><meta name="author" content="eurysco"><meta name="keywords" content="eurysco"><link href="css/modern.css" rel="stylesheet"><link href="css/modern-responsive.css" rel="stylesheet"><title>eurysco Authentication Error</title></head><body class="metrouicss"><br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="border:none;" align="center"><a href="/" class="eurysco-bg big page-back"></a>';
		echo '<br />';
		echo '<h2>User Disabled</h2>';
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped" style="width:280px;">';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">Closed</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">User Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">Disabled</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session ID:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . substr(hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id()), 0, 32) . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Hashed IP:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . md5($_SERVER['HTTP_X_FORWARDED_FOR']) . '</div></td></tr>';
		echo '</table>';
		echo '</td></tr></table></body></html>';
		exit;
	}
	$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $data['username'] . '.xml', true)))));
	$usersusertype = $userxml->settings->usertype;
	$usersuserauth = $userxml->settings->userauth;
	$mcrykey = pack('H*', hash('sha256', $usersusertype));
	$A1 = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($users[$data['username']]), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($users[$data['username']]), 0, $iv_size)));
	$A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
	$valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);
	$_SESSION['username'] = $data['username'];
	$_SESSION['usertype'] = '';
	$_SESSION['usersett'] = array(); $_SESSION['usersett']['name'] = ''; $_SESSION['usersett']['coreconfig'] = 0; $_SESSION['usersett']['executorconfig'] = 0; $_SESSION['usersett']['serverconfig'] = 0; $_SESSION['usersett']['agentconfig'] = 0; $_SESSION['usersett']['nodesstatus'] = 0; $_SESSION['usersett']['nodesnagiosstatus'] = 0; $_SESSION['usersett']['nodessysteminventory'] = 0; $_SESSION['usersett']['nodesinstalledprograms'] = 0; $_SESSION['usersett']['nodesprocesscontrol'] = 0; $_SESSION['usersett']['nodesservicecontrol'] = 0; $_SESSION['usersett']['nodesnetworkstats'] = 0; $_SESSION['usersett']['nodesscheduledtasks'] = 0; $_SESSION['usersett']['nodeseventviewer'] = 0; $_SESSION['usersett']['systeminfo'] = 0; $_SESSION['usersett']['nagiosstatus'] = 0; $_SESSION['usersett']['systeminventory'] = 0; $_SESSION['usersett']['installedprograms'] = 0; $_SESSION['usersett']['wmiexplorer'] = 0; $_SESSION['usersett']['systemshutdown'] = 0; $_SESSION['usersett']['processcontrol'] = 0; $_SESSION['usersett']['servicecontrol'] = 0; $_SESSION['usersett']['networkstats'] = 0; $_SESSION['usersett']['scheduledtasks'] = 0; $_SESSION['usersett']['eventviewer'] = 0; $_SESSION['usersett']['systemregistry'] = 0; $_SESSION['usersett']['commandline'] = 0; $_SESSION['usersett']['filebrowser'] = 0; $_SESSION['usersett']['filetransfer'] = 0; $_SESSION['usersett']['changesettings'] = 0; $_SESSION['usersett']['usermanagement'] = 0; $_SESSION['usersett']['auditlog'] = 0; $_SESSION['usersett']['nodesstatusf'] = ''; $_SESSION['usersett']['nodesnagiosstatusf'] = ''; $_SESSION['usersett']['nodesinstalledprogramsf'] = ''; $_SESSION['usersett']['nodesprocesscontrolf'] = ''; $_SESSION['usersett']['nodesservicecontrolf'] = ''; $_SESSION['usersett']['nodesnetworkstatsf'] = ''; $_SESSION['usersett']['nodesscheduledtasksf'] = ''; $_SESSION['usersett']['nodeseventviewerf'] = ''; $_SESSION['usersett']['nagiosstatusf'] = ''; $_SESSION['usersett']['installedprogramsf'] = ''; $_SESSION['usersett']['processcontrolf'] = ''; $_SESSION['usersett']['servicecontrolf'] = ''; $_SESSION['usersett']['networkstatsf'] = ''; $_SESSION['usersett']['scheduledtasksf'] = ''; $_SESSION['usersett']['eventviewerf'] = ''; $_SESSION['usersett']['commandlinef'] = ''; $_SESSION['usersett']['filebrowserf'] = ''; $_SESSION['usersett']['regeditf'] = ''; $_SESSION['usersett']['auth'] = 'Local'; $_SESSION['usersett']['pwdexp'] = 1; $_SESSION['usersett']['disable'] = 7; $_SESSION['usersett']['sastarttime'] = '00:00:00'; $_SESSION['usersett']['sastoptime'] = '00:00:00'; $_SESSION['usersett']['sasunday'] = 'on'; $_SESSION['usersett']['samonday'] = 'on'; $_SESSION['usersett']['satuesday'] = 'on'; $_SESSION['usersett']['sawednesday'] = 'on'; $_SESSION['usersett']['sathursday'] = 'on'; $_SESSION['usersett']['safriday'] = 'on'; $_SESSION['usersett']['sasaturday'] = 'on'; $_SESSION['usersett']['badaut'] = 0; 
	if (hash('sha512', $_SESSION['username'] . 'Local') == $usersuserauth) { $_SESSION['userauth'] = 'Local'; }
	if (hash('sha512', $_SESSION['username'] . 'Distributed') == $usersuserauth) { $_SESSION['userauth'] = 'Distributed'; }
	if (hash('sha512', $_SESSION['username'] . 'Administrators') == $usersusertype) { $_SESSION['usertype'] = 'Administrators'; }
	if (hash('sha512', $_SESSION['username'] . 'Auditors') == $usersusertype) { $_SESSION['usertype'] = 'Auditors'; }
	if (hash('sha512', $_SESSION['username'] . 'Operators') == $usersusertype) { $_SESSION['usertype'] = 'Operators'; }
	if (hash('sha512', $_SESSION['username'] . 'Users') == $usersusertype) { $_SESSION['usertype'] = 'Users'; }
	if ($_SESSION['usertype'] == '') {
		$grouplist = scandir($_SERVER['DOCUMENT_ROOT'] . '\\groups\\');
		foreach ($grouplist as $group) {
			if (pathinfo($group)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group) > 0) {
				$group = str_replace('.xml', '', $group);
				if (hash('sha512', $_SESSION['username'] . $group) == $usersusertype) {
					$mcrykey = pack('H*', hash('sha256', hash('sha512', $group)));
					$groupsxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group . '.xml', true)))));
					$_SESSION['usersett'] = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($groupsxml->settings->groupsettings), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($groupsxml->settings->groupsettings), 0, $iv_size)));
					$_SESSION['usertype'] = $group;
				}
			}
		}
	}
	if ($_SESSION['usersett']['nodesstatusf'] != '') {
		$wmi = new COM('winmgmts://');
		$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_ComputerSystem");
		$wl_computername = '';
		$wl_domain = '';
		$wl_manufacturer = '';
		$wl_model = '';
		foreach($wmisclass as $obj) {
			if ($obj->Caption != '') { $wl_computername = $obj->Caption; }
			if ($obj->Domain != '') { $wl_domain = $obj->Domain; }
			if ($obj->Manufacturer != '') { $wl_manufacturer = $obj->Manufacturer; }
			if ($obj->Model != '') { $wl_model = $obj->Model; }
		}
		$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_OperatingSystem");
		$wl_osname = '';
		$wl_osversion = '';
		$wl_osservicepack = '';
		$wl_osserialnumber = '';
		foreach($wmisclass as $obj) {
			if ($obj->Caption != '') { $wl_osname = $obj->Caption; }
			if ($obj->Version != '') { $wl_osversion = $obj->Version; }
			if ($obj->CSDVersion != '') { $wl_osservicepack = $obj->CSDVersion; }
			if ($obj->SerialNumber != '') { $wl_osserialnumber = $obj->SerialNumber; }
		}
		$wl_xmlstatus = strtolower('<computername>' . $wl_computername . '</computername><osname>' . $wl_osname . '</osname><osversion>' . $wl_osversion . '</osversion><osservicepack>' . $wl_osservicepack . '</osservicepack><osserialnumber>' . $wl_osserialnumber . '</osserialnumber><manufacturer>' . $wl_manufacturer . '</manufacturer><model>' . $wl_model . '</model><domain>' . $wl_domain . '</domain>');
		$prefilter = $_SESSION['usersett']['nodesstatusf'];
		$checkprefilter = 1;
		if (substr($prefilter, 0, 1) != '-') {
			if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($prefilter))) . '/', $wl_xmlstatus) || strpos($wl_xmlstatus, strtolower($prefilter)) > -1) {
				$checkprefilter = 0;
			} else {
				$checkprefilter = 1;
			}
		} else {
			$notprefilter = substr($prefilter, 1);
			if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notprefilter))) . '/', $wl_xmlstatus) && !strpos($wl_xmlstatus, strtolower($notprefilter))) {
				$checkprefilter = 0;
			} else {
				$checkprefilter = 1;
			}
		}
		if ($checkprefilter != 0) { if (strtolower($eurysco_serverconaddress) != 'https://' . strtolower($envcomputername) || $_SESSION['usersett']['nodesstatus'] == 0) { $_SESSION['logoutnal'] = 'logoutnal'; } else { $_SESSION['usersett']['coreconfig'] = 0; $_SESSION['usersett']['executorconfig'] = 0; $_SESSION['usersett']['serverconfig'] = 0; $_SESSION['usersett']['agentconfig'] = 0; $_SESSION['usersett']['systeminfo'] = 0; $_SESSION['usersett']['nagiosstatus'] = 0; $_SESSION['usersett']['systeminventory'] = 0; $_SESSION['usersett']['installedprograms'] = 0; $_SESSION['usersett']['wmiexplorer'] = 0; $_SESSION['usersett']['systemshutdown'] = 0; $_SESSION['usersett']['processcontrol'] = 0; $_SESSION['usersett']['servicecontrol'] = 0; $_SESSION['usersett']['networkstats'] = 0; $_SESSION['usersett']['scheduledtasks'] = 0; $_SESSION['usersett']['eventviewer'] = 0; $_SESSION['usersett']['systemregistry'] = 0; $_SESSION['usersett']['commandline'] = 0; $_SESSION['usersett']['filebrowser'] = 0; $_SESSION['usersett']['changesettings'] = 0; $_SESSION['usersett']['usermanagement'] = 0; $_SESSION['usersett']['auditlog'] = 0; } }
	}
	
	if ($data['username'] != '') { $_SESSION['USRLCK'] = $_SESSION['USRLCK'] . $data['username']; }

	if ($userxml->settings->passwlck == md5($userxml->settings->password . 3)) {
		echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/html" lang="en"><head><meta charset="utf-8"><meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1"><meta name="description" content="eurysco"><meta name="author" content="eurysco"><meta name="keywords" content="eurysco"><link href="css/modern.css" rel="stylesheet"><link href="css/modern-responsive.css" rel="stylesheet"><title>eurysco Authentication Error</title></head><body class="metrouicss"><br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="border:none;" align="center"><a href="/" class="eurysco-bg big page-back"></a>';
		echo '<br />';
		echo '<h2>User Locked Out</h2>';
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped" style="width:280px;">';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">Locked</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">User Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">Locked Out</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Locked Out Time:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . $userxml->settings->lckouttm . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Locked Out Node:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . $userxml->settings->lckoutcm . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Locked Out From:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . $userxml->settings->lckoutip . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session ID:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . substr(hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id()), 0, 32) . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Hashed IP:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . md5($_SERVER['HTTP_X_FORWARDED_FOR']) . '</div></td></tr>';
		echo '</table>';
		echo '</td></tr></table></body></html>';
		exit;
	}

	$agentstatus = '';
	if (file_exists(str_replace('\\core', '\\agent\\temp\\agent.status', $_SERVER['DOCUMENT_ROOT']))) {
		$f = fopen(str_replace('\\core', '\\agent\\temp\\agent.status', $_SERVER['DOCUMENT_ROOT']), 'r');
		$connexitcode = fgets($f);
		fclose($f);
		if (strpos($connexitcode, 'Connection Success') > 0) { $agentstatus = 'con'; }
	}
	if (isset($_POST['ath'])) {
		$ath = base64_decode($_POST['ath']);
	} else {
		$ath = '';
	}
	if ($ath != 'Local') {
		if (isset($data['username']) && isset($agentstatus) && isset($eurysco_serverconaddress) && isset($eurysco_serverconport) && isset($eurysco_serverconpassword)) {
			if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != 'https://' . strtolower($envcomputername)) {
				$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
				$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
				$mcrykey = pack('H*', hash('sha256', hash('sha512', 'vNqgi_R1QX%C;z-724p4lFHm*?7c!e2%vG9tp+-*@#%=?!_;./' . hash('tiger128,4', $eurysco_serverconport) . '-*@#%=?!_;./-f;bTh2XXqW%Zs%88+/-7pVb;X')));
				if ($userxml->settings->userauth == hash('sha512', $data['username'] . 'Distributed')) {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $eurysco_sslverifypeer);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $eurysco_sslverifyhost);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10000);
					curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
					curl_setopt($ch, CURLOPT_USERPWD, hash('sha256', $eurysco_serverconport . 'euryscoServer' . $eurysco_serverconport) . ':' . mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($eurysco_serverconpassword), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($eurysco_serverconpassword), 0, $iv_size)));
					curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
					curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/userscp.php');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POST, true);
					$datausr = array(
						'usr' => base64_encode($data['username']),
						'pwd' => base64_encode($data['response']),
						'lgn' => base64_encode(':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2)
					);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $datausr);
					curl_exec($ch);
					curl_close($ch);
				}
			}
		}
	}
	
	if ($data['response'] != $valid_response) {
		$lockedouttime = date('r');
		$lockedoutcnam = $envcomputername;
		$lockedoutsrip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$passwlck = md5($userxml->settings->password . 3);
		if ($userxml->settings->passwlck == md5($userxml->settings->password)) { $passwlck = md5($userxml->settings->password . 1); }
		if ($userxml->settings->passwlck == md5($userxml->settings->password . 1)) { $passwlck = md5($userxml->settings->password . 2); }
		if ($userxml->settings->passwlck == md5($userxml->settings->password . 2)) { $passwlck = md5($userxml->settings->password . 3); }
		if ($data['username'] != 'Administrator') {
			$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<username>' . $userxml->settings->username . '</username>' . "\n" . '		<usertype>' . $userxml->settings->usertype . '</usertype>' . "\n" . '		<userauth>' . $userxml->settings->userauth . '</userauth>' . "\n" . '		<password>' . $userxml->settings->password . '</password>' . "\n" . '		<passwchk>' . $userxml->settings->passwchk . '</passwchk>' . "\n" . '		<passwlck>' . $passwlck . '</passwlck>' . "\n" . '		<lckouttm>' . $lockedouttime . '</lckouttm>' . "\n" . '		<lckoutcm>' . $lockedoutcnam . '</lckoutcm>' . "\n" . '		<lckoutip>' . $lockedoutsrip . '</lckoutip>' . "\n" . '		<expiration>' . $userxml->settings->expiration . '</expiration>' . "\n" . '	</settings>' . "\n" . '</config>';
			$writexml = fopen($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $data['username'] . '.xml', 'w');
			fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
			fclose($writexml);
		}
		$userlockoutst = '';
		if ($_SESSION['ELC'] < 3 && $passwlck != md5($userxml->settings->password . 3)) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
			$_SESSION['ELC'] = $_SESSION['ELC'] + 1;
			
		}
		if ((3 - $_SESSION['ELC']) > 0 && $passwlck != md5($userxml->settings->password . 3)) {
			$sessionstatus = 'Active';
			$loginattempts = '';
		} else {
			$sessionstatus = 'Locked';
			$loginattempts = '<tr><td colspan="2" style="font-size:12px;" align="center">Login Limit Exceeded</td></tr>';
		}
		if ($data['username'] != 'Administrator' && (substr_count($_SESSION['USRLCK'], $data['username']) == 3 || $passwlck == md5($userxml->settings->password . 3))) {
			$userlockoutst = $userlockoutst . '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">User Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">Locked Out</div></td></tr>';
			$userlockoutst = $userlockoutst . '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Locked Out Time:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . $lockedouttime . '</div></td></tr>';
			$userlockoutst = $userlockoutst . '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Locked Out Node:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . $envcomputername . '</div></td></tr>';
			$userlockoutst = $userlockoutst . '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Locked Out From:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . $lockedoutsrip . '</div></td></tr>';
			if ($passwlck == md5($userxml->settings->password . 3)) {
				$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<username>' . $userxml->settings->username . '</username>' . "\n" . '		<usertype>' . $userxml->settings->usertype . '</usertype>' . "\n" . '		<userauth>' . $userxml->settings->userauth . '</userauth>' . "\n" . '		<password>' . $userxml->settings->password . '</password>' . "\n" . '		<passwchk>' . $userxml->settings->passwchk . '</passwchk>' . "\n" . '		<passwlck>' . $passwlck . '</passwlck>' . "\n" . '		<lckouttm>' . $lockedouttime . '</lckouttm>' . "\n" . '		<lckoutcm>' . $lockedoutcnam . '</lckoutcm>' . "\n" . '		<lckoutip>' . $lockedoutsrip . '</lckoutip>' . "\n" . '		<expiration>' . $userxml->settings->expiration . '</expiration>' . "\n" . '	</settings>' . "\n" . '</config>';
				$writexml = fopen($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $data['username'] . '.xml', 'w');
				fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
				fclose($writexml);
				$audit = $lockedouttime . '     ' . $data['username'] . '     ' . $envcomputername . '     user status     locked out from IP ' . $lockedoutsrip;
				$auditresponse = 'local';
				if (isset($agentstatus) && isset($eurysco_serverconaddress) && isset($eurysco_serverconport) && isset($eurysco_serverconpassword)) {
					if ($agentstatus == 'con' && $eurysco_serverconaddress != 'https://' . strtoupper($envcomputername)) {
						$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
						$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
						$mcrykey = pack('H*', hash('sha256', hash('sha512', 'vNqgi_R1QX%C;z-724p4lFHm*?7c!e2%vG9tp+-*@#%=?!_;./' . hash('tiger128,4', $eurysco_serverconport) . '-*@#%=?!_;./-f;bTh2XXqW%Zs%88+/-7pVb;X')));
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $eurysco_sslverifypeer);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $eurysco_sslverifyhost);
						curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/auditlog.php');
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10000);
						curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
						curl_setopt($ch, CURLOPT_USERPWD, hash('sha256', $eurysco_serverconport . 'euryscoServer' . $eurysco_serverconport) . ':' . mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($eurysco_serverconpassword), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($eurysco_serverconpassword), 0, $iv_size)));
						curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
						curl_setopt($ch, CURLOPT_POST, true);
						$dataadt = array(
							'auditlog' => $audit
						);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $dataadt);
						if (curl_exec($ch) === false) {
							$auditresponse = 'local';
						} else {
							$auditresponse = 'remote';
						}
						curl_close($ch);
					}
				}
				if ($auditresponse == 'local') {
					$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '\\audit\\audit-' . date('Ym') . '_' . date('M-Y') . '.log', 'a');
					fwrite($fp, $audit . "\r\n");
					fclose($fp);
					$auditsec = explode('     ', $audit);
					exec('eventcreate.exe /l "Application" /t INFORMATION /so "eurysco Audit : ' . $auditsec[1] . ' : ' . $auditsec[2] . '" /id 1 /d "' . str_replace('"', '\'', $audit) . '"', $errorarray, $errorlevel);
				}
			}
		}
		echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/html" lang="en"><head><meta charset="utf-8"><meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1"><meta name="description" content="eurysco"><meta name="author" content="eurysco"><meta name="keywords" content="eurysco"><link href="css/modern.css" rel="stylesheet"><link href="css/modern-responsive.css" rel="stylesheet"><title>eurysco Authentication Error</title></head><body class="metrouicss"><br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="border:none;" align="center"><a href="/" class="eurysco-bg big page-back"></a>';
		echo '<br />';
		echo '<h2>Authentication Error</h2>';
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped" style="width:280px;">';
		echo '<tr><td colspan="2" style="font-size:12px;" align="center">Invalid Credentials</td></tr>';
		echo $loginattempts;
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session Status:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . $sessionstatus . '</div></td></tr>';
		echo $userlockoutst;
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Session ID:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . substr(hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id()), 0, 32) . '</div></td></tr>';
		echo '<tr><td width="20%"><div style="font-size:10px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Hashed IP:</div></td><td width="80%"><div id="totaltime" style="font-size:10px;">' . md5($_SERVER['HTTP_X_FORWARDED_FOR']) . '</div></td></tr>';
		echo '</table>';
		echo '</td></tr></table></body></html>';
		failBlk ();
		exit;
	}

}

function http_digest_parse($txt) {
	$_SESSION['password'] = $txt;
	$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
	$data = array();
	$keys = implode('|', array_keys($needed_parts));

	preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

	foreach ($matches as $m) {
		$data[$m[1]] = $m[3] ? $m[3] : $m[4];
		unset($needed_parts[$m[1]]);
	}

	return $needed_parts ? false : $data;
}

function failBlk () {
	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\' . md5($_SERVER['HTTP_X_FORWARDED_FOR']) . '.txt', 'w');
	fwrite($fp, 'UTC ' . date('Y-m-d H:i:s', time()) . ' - IP: ' . $_SERVER['HTTP_X_FORWARDED_FOR'] . PHP_EOL);
	fclose($fp);
}

if (!isset($_SESSION['session']) || $_SESSION['session'] != hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id())) {
	$_SESSION['UID'] = uniqid();
	$_SESSION['session'] = hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id());
	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user status     logged in';
	if (!isset($_POST['lcl']) && $userxml->settings->passwlck != md5($userxml->settings->password)) {
		$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<username>' . $userxml->settings->username . '</username>' . "\n" . '		<usertype>' . $userxml->settings->usertype . '</usertype>' . "\n" . '		<userauth>' . $userxml->settings->userauth . '</userauth>' . "\n" . '		<password>' . $userxml->settings->password . '</password>' . "\n" . '		<passwchk>' . $userxml->settings->passwchk . '</passwchk>' . "\n" . '		<passwlck>' . md5($userxml->settings->password) . '</passwlck>' . "\n" . '		<lckouttm>' . $userxml->settings->lckouttm . '</lckouttm>' . "\n" . '		<lckoutcm>' . $userxml->settings->lckoutcm . '</lckoutcm>' . "\n" . '		<lckoutip>' . $userxml->settings->lckoutip . '</lckoutip>' . "\n" . '		<expiration>' . $userxml->settings->expiration . '</expiration>' . "\n" . '	</settings>' . "\n" . '</config>';
		$writexml = fopen($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_SESSION['username'] . '.xml', 'w');
		fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
		fclose($writexml);
	}
	include('/include/unset.php');
	header('location: ' . $_SERVER["SCRIPT_NAME"]);
}

$badautipdc = strtotime(date('Y-m-d H:i:s', time()));
$badaut = scandir($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\');				
foreach($badaut as $badautip)
if($badautip != '.' && $badautip != '..') {
	if ((($badautipdc - (strtotime(date('Y-m-d H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\' . $badautip))))) / 60 / 60) > 24) {
		unlink($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\' . $badautip);
	}
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_SESSION['username'] . '.xml')) {
	if (preg_match('/lckouttm/', file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_SESSION['username'] . '.xml'))) {
		header('location: /?logout');
	}
} else {
	header('location: /?logout');
}

if (isset($_SESSION['logoutnal'])) { if ($_SESSION['logoutnal'] == 'logoutnal') { $_SESSION['logoutstatus'] = 'logout'; header('location: https://' . $_SERVER['HTTP_HOST'] . '/' . str_replace('/', '', $_SERVER['PHP_SELF'])); } }

if ($_SESSION['usersett']['sa' . strtolower(date('l'))] == 'on' && ($_SESSION['usersett']['sastarttime'] == $_SESSION['usersett']['sastoptime'] || (date('His', strtotime($_SESSION['usersett']['sastarttime'])) < date('His', strtotime($_SESSION['usersett']['sastoptime'])) && date('His', strtotime($_SESSION['usersett']['sastarttime'])) <= date('His') && date('His') < date('His', strtotime($_SESSION['usersett']['sastoptime']))) || (date('His', strtotime($_SESSION['usersett']['sastarttime'])) > date('His', strtotime($_SESSION['usersett']['sastoptime'])) && (date('His', strtotime($_SESSION['usersett']['sastarttime'])) <= date('His') || date('His') < date('His', strtotime($_SESSION['usersett']['sastoptime'])))))) {  } else { $_SESSION['logoutatl'] = 'logoutatl'; $_SESSION['logoutstatus'] = 'logout'; header('location: https://' . $_SERVER['HTTP_HOST'] . '/' . str_replace('/', '', $_SERVER['PHP_SELF'])); }

?>