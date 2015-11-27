<?php

if ($_SERVER['SCRIPT_NAME'] == '/index.php') { echo '<euryscoServer>' . "\n"; }

$badaut = scandir($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\');
if (count($badaut) > 22 && $_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1' && $_SERVER['HTTP_X_FORWARDED_FOR'] != '::1') {
	if ($_SERVER['SCRIPT_NAME'] == '/index.php') {
		echo '<connectionstatus>eurysco Server &#x25cf; ' . strtolower($envcomputername) . ' &#x25cf; Forbidden Authentication</connectionstatus>' . "\n";
		echo '</euryscoServer>';
	}
	exit;
}

$realm = '';
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

if (file_exists('conf\\config_server.xml')) {
	$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents('conf\\config_server.xml', true)))));
	$usersusername = $userxml->settings->username;
	$usersusertype = $userxml->settings->usertype;
	$userspassword = $userxml->settings->password;
	$users["$usersusername"] = "$userspassword";
}
	
if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
	header('HTTP/1.1 401 Unauthorized');
	header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . hash('whirlpool', uniqid()) . '",opaque="' . hash('sha512', $realm) . '"');
	if ($_SERVER['SCRIPT_NAME'] == '/index.php') {
		echo '<connectionstatus>eurysco Server &#x25cf; ' . strtolower($envcomputername) . ' &#x25cf; Authentication Required</connectionstatus>' . "\n";
		echo '</euryscoServer>';
	}
	exit;
}

if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$data['username']])) {
	header('HTTP/1.1 401 Unauthorized');
	header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . hash('whirlpool', uniqid()) . '",opaque="' . hash('sha512', $realm) . '"');
	if ($_SERVER['SCRIPT_NAME'] == '/index.php') {
		echo '<connectionstatus>eurysco Server &#x25cf; ' . strtolower($envcomputername) . ' &#x25cf; Authentication Error</connectionstatus>' . "\n";
		echo '</euryscoServer>';
	}
	failBlk ();
	exit;
}

$mcrykey = pack('H*', hash('sha256', $usersusertype));
$A1 = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($users[$data['username']]), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($users[$data['username']]), 0, $iv_size)));
$A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
$valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);

if ($data['response'] != $valid_response) {
	header('HTTP/1.1 401 Unauthorized');
	header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . hash('whirlpool', uniqid()) . '",opaque="' . hash('sha512', $realm) . '"');
	if ($_SERVER['SCRIPT_NAME'] == '/index.php') {
		echo '<connectionstatus>eurysco Server &#x25cf; ' . strtolower($envcomputername) . ' &#x25cf; Authentication Error</connectionstatus>' . "\n";
		echo '</euryscoServer>';
	}
	failBlk ();
	exit;
}

function http_digest_parse($txt) {
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

$badautipdc = strtotime(date('Y-m-d H:i:s', time()));
$badaut = scandir($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\');				
foreach($badaut as $badautip)
if($badautip != '.' && $badautip != '..') {
	if ((($badautipdc - (strtotime(date('Y-m-d H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\' . $badautip))))) / 60 / 60) > 24) {
		unlink($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\' . $badautip);
	}
}

if ($_SERVER['SCRIPT_NAME'] == '/index.php') { echo '<connectionstatus>eurysco Server &#x25cf; ' . strtolower($envcomputername) . ' &#x25cf; Connection Successful</connectionstatus>'; }

?>