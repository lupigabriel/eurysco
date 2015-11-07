<?php

if (!isset($_ENV['euryscopath']) || !isset($_ENV['usernm']) || !isset($_ENV['usertype']) || !isset($_ENV['userauth']) || !isset($_ENV['nextexppwddays']) || !isset($_ENV['action']) || !isset($_ENV['pmpass'])) { exit(2112); }

$pmnewpass = '';
if (isset($_ENV['pmnewpass'])) {
	$pmnewpass = $_ENV['pmnewpass'];
}

$reconcileuser = '';
if (isset($_ENV['reconcileuser'])) {
	$reconcileuser = $_ENV['reconcileuser'];
}

$reconcilepass = '';
if (isset($_ENV['pmmaxextrapassindex'])) {
	if (isset($_ENV['pmextrapass' . $_ENV['pmmaxextrapassindex']])) {
		$reconcilepass = $_ENV['pmextrapass' . $_ENV['pmmaxextrapassindex']];
	}
}

$config_settings = $_ENV['euryscopath'] . '\\agent\\conf\\config_settings.xml';
if (file_exists($config_settings)) {
	$xmlsettings = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_settings, true)))));
	date_default_timezone_set($xmlsettings->settings->timezonesetting);
} else {
	date_default_timezone_set('UTC');
}

$eurysco_serverconaddress = '';
$eurysco_serverconport = '';
$eurysco_serverconpassword = '';
$eurysco_sslverifyhost = 'true';
$eurysco_sslverifypeer = 'true';
$config_agentsrv = $_ENV['euryscopath'] . '\\agent\\conf\\config_agent.xml';
if (file_exists($config_agentsrv)) {
	$xmlagent = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($config_agentsrv, true)))));
	$eurysco_serverconaddress = $xmlagent->settings->serverconnectionaddress;
	$eurysco_serverconport = $xmlagent->settings->serverconnectionport;
	$eurysco_serverconpassword = $xmlagent->settings->serverconnectionpassword;
	$eurysco_sslverifyhost = $xmlagent->settings->sslverifyhost;
	$eurysco_sslverifypeer = $xmlagent->settings->sslverifypeer;
} else {
	exit(2104);
}

$username = str_replace('UserName=', '', trim($_ENV['usernm']));

$realm = 'eurysco Authentication';
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
$mcrykey = pack('H*', hash('sha256', hash('sha512', 'vNqgi_R1QX%C;z-724p4lFHm*?7c!e2%vG9tp+-*@#%=?!_;./' . hash('tiger128,4', $eurysco_serverconport) . '-*@#%=?!_;./-f;bTh2XXqW%Zs%88+/-7pVb;X')));

$mcrykeyusr = pack('H*', hash('sha256', hash('sha512', $username . $_ENV['usertype'])));
$passwcrypt = base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykeyusr, md5($username . ':' . $realm . ':' . $pmnewpass), MCRYPT_MODE_CBC, $iv));
$passwchk = base64_encode(md5(substr(hash('whirlpool', hash('sha256', hash('sha384', hash('sha512', $username . ':' . $realm . ':' . $pmnewpass)))), 0, -1)));
$xml = '<?xml version="1.0"?>' . "\n" . '<config>' . "\n" . '	<settings>' . "\n" . '		<username>' . $username . '</username>' . "\n" . '		<usertype>' . hash('sha512', $username . $_ENV['usertype']) . '</usertype>' . "\n" . '		<userauth>' . hash('sha512', $username . $_ENV['userauth']) . '</userauth>' . "\n" . '		<password>' . $passwcrypt . '</password>' . "\n" . '		<passwchk>' . $passwchk . '</passwchk>' . "\n" . '		<passwlck>' . md5($passwcrypt) . '</passwlck>' . "\n" . '		<expiration>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(date('Y-m-d H:i:s', strtotime(date('Y') . '-' . date('m') . '-' . date('d') . ' ' . date('H') . ':' . date('i') . ':' . date('s') . ' + ' . $_ENV['nextexppwddays'] . ' days'))))))) . '</expiration>' . "\n" . '	</settings>' . "\n" . '</config>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $eurysco_sslverifypeer);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $eurysco_sslverifyhost);
curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/userscp.php');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10000);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_USERPWD, hash('sha256', $eurysco_serverconport . 'euryscoServer' . $eurysco_serverconport) . ':' . mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($eurysco_serverconpassword), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($eurysco_serverconpassword), 0, $iv_size)));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$data = array(
	'usr' => base64_encode($username),
	'pwd' => base64_encode($_ENV['pmpass']),
	'xml' => base64_encode($xml),
	'act' => base64_encode(strtolower(trim($_ENV['action']))),
	'rus' => base64_encode($reconcileuser),
	'rpw' => base64_encode($reconcilepass)
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = 2101;
$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
	exit(2102);
} else {
	if (preg_match_all('/locked out/', strtolower($response))) {
		exit(2115);
	} else {
		if (preg_match_all('/user disabled/', strtolower($response))) {
			exit(2116);
		} else {
			if (preg_match_all('/authentication error/', strtolower($response))) {
				exit(2103);
			} else {
				if (preg_match_all('/authorization error/', strtolower($response))) {
					exit(2114);
				} else {
					exit(0);
				}
			}
		}
	}
}

?>