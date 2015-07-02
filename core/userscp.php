<?php

include('/include/init.php');

if (!isset($_POST['xml'])) { exit; }
if (!isset($_POST['ath'])) { exit; }

set_time_limit(10);

if ($_SESSION['username'] == 'Administrator' && base64_decode($_POST['ath']) != 'Local') {
	echo 'authentication error';
} else {
	if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_SESSION['username'] . '.xml')) {
		echo 'authentication error';
	} else {
		$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_SESSION['username'] . '.xml', true)))));
		$usersuserauth = $userxml->settings->userauth;
		if (hash('sha512', $_SESSION['username'] . 'Distributed') != $usersuserauth && base64_decode($_POST['ath']) != 'Local') {
			echo 'authentication error';
		} else {
			$writexml = fopen($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_SESSION['username'] . '.xml', 'w');
			fwrite($writexml, base64_encode(base64_encode($_POST['xml'])));
			fclose($writexml);
			echo md5($_POST['xml']);
		}
	}
}

exit;

?>