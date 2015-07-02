<?php

include('/include/init.php');

if (!isset($_POST['usr'])) { exit; }
if (!isset($_POST['pwd'])) { exit; }

set_time_limit(10);

if (isset($_POST['lgn'])) {
	if (!file_exists(str_replace('\\server', '\\core\\users', $_SERVER['DOCUMENT_ROOT']) . '\\' . base64_decode($_POST['usr']) . '.xml')) {
		exit;
	} else {
		$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents(str_replace('\\server', '\\core\\users', $_SERVER['DOCUMENT_ROOT']) . '\\' . base64_decode($_POST['usr']) . '.xml'), true))));
		if ($userxml->settings->userauth == hash('sha512', base64_decode($_POST['usr']) . 'Distributed')) {
			$usersusertype = $userxml->settings->usertype;
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$mcrykey = pack('H*', hash('sha256', $usersusertype));
			$A1 = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($userxml->settings->password), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($userxml->settings->password), 0, $iv_size)));
			$valid_response = md5($A1 . base64_decode($_POST['lgn']));
			$lockedouttime = date('r');
			$lockedoutcnam = $envcomputername;
			$lockedoutsrip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			if (base64_decode($_POST['pwd']) != $valid_response) {
				$passwlck = md5($userxml->settings->password . 3);
				if ($userxml->settings->passwlck == md5($userxml->settings->password)) { $passwlck = md5($userxml->settings->password . 1); }
				if ($userxml->settings->passwlck == md5($userxml->settings->password . 1)) { $passwlck = md5($userxml->settings->password . 2); }
				if ($userxml->settings->passwlck == md5($userxml->settings->password . 2)) { $passwlck = md5($userxml->settings->password . 3); }
				if (base64_decode($_POST['usr']) != 'Administrator') {
					$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<username>' . $userxml->settings->username . '</username>' . "\n" . '		<usertype>' . $userxml->settings->usertype . '</usertype>' . "\n" . '		<userauth>' . $userxml->settings->userauth . '</userauth>' . "\n" . '		<password>' . $userxml->settings->password . '</password>' . "\n" . '		<passwchk>' . $userxml->settings->passwchk . '</passwchk>' . "\n" . '		<passwlck>' . $passwlck . '</passwlck>' . "\n" . '		<lckouttm>' . $lockedouttime . '</lckouttm>' . "\n" . '		<lckoutcm>' . $lockedoutcnam . '</lckoutcm>' . "\n" . '		<lckoutip>' . $lockedoutsrip . '</lckoutip>' . "\n" . '		<expiration>' . $userxml->settings->expiration . '</expiration>' . "\n" . '	</settings>' . "\n" . '</config>';
					$writexml = fopen(str_replace('\\server', '\\core\\users', $_SERVER['DOCUMENT_ROOT']) . '\\' . base64_decode($_POST['usr']) . '.xml', 'w');
					fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
					fclose($writexml);
				}
			} else {
				if ($userxml->settings->passwlck != md5($userxml->settings->password)) {
					$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<username>' . $userxml->settings->username . '</username>' . "\n" . '		<usertype>' . $userxml->settings->usertype . '</usertype>' . "\n" . '		<userauth>' . $userxml->settings->userauth . '</userauth>' . "\n" . '		<password>' . $userxml->settings->password . '</password>' . "\n" . '		<passwchk>' . $userxml->settings->passwchk . '</passwchk>' . "\n" . '		<passwlck>' . md5($userxml->settings->password) . '</passwlck>' . "\n" . '		<lckouttm>' . $lockedouttime . '</lckouttm>' . "\n" . '		<lckoutcm>' . $lockedoutcnam . '</lckoutcm>' . "\n" . '		<lckoutip>' . $lockedoutsrip . '</lckoutip>' . "\n" . '		<expiration>' . $userxml->settings->expiration . '</expiration>' . "\n" . '	</settings>' . "\n" . '</config>';
					$writexml = fopen(str_replace('\\server', '\\core\\users', $_SERVER['DOCUMENT_ROOT']) . '\\' . base64_decode($_POST['usr']) . '.xml', 'w');
					fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
					fclose($writexml);
				}
			}
		}
	}
	exit;
}

if (!isset($_POST['xml'])) { exit; }

$authresponse = '';
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10000);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_USERPWD, base64_decode($_POST['usr']) . ':' . base64_decode($_POST['pwd']));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
curl_setopt($ch, CURLOPT_URL, 'https://127.0.0.1:' . $eurysco_coreport . '/userscp.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$data = array(
	'ath' => base64_encode('Distributed'),
	'xml' => $_POST['xml']
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$authresponse = curl_exec($ch);
curl_close($ch);

echo $authresponse;

exit;

?>