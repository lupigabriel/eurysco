<?php

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');

if (!isset($_POST['xml'])) { exit; }
if (!isset($_POST['ath'])) { exit; }

$action = '';
if (isset($_POST['act'])) {
	$action = base64_decode($_POST['act']);
}

$usr = '';
if (isset($_POST['usr'])) {
	$usr = $_POST['usr'];
}

if ($action == 'logon') { echo 'logon'; exit; }
if ($action == 'verifypass') { echo 'verifypass'; exit; }
if ($action == 'prechangepass') { echo 'prechangepass'; exit; }
if ($action == 'prereconcilepass') { echo 'prereconcilepass'; exit; }
if ($action != '') { echo $action; }

set_time_limit(10);

if ($action == 'prereconcilepass' || $action == 'reconcilepass') {
	if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) {  } else { echo 'authorization error'; exit; }
	$changeuser = base64_decode($usr);
} else {
	$changeuser = $_SESSION['username'];
}

if ($changeuser == 'Administrator' && base64_decode($_POST['ath']) != 'Local') {
	echo 'authentication error';
} else {
	if (!file_exists($euryscoinstallpath . '\\users\\' . $changeuser . '.xml')) {
		echo 'authentication error';
	} else {
		$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($euryscoinstallpath . '\\users\\' . $changeuser . '.xml', true)))));
		$usersuserauth = $userxml->settings->userauth;
		if (hash('sha512', $changeuser . 'Distributed') != $usersuserauth && base64_decode($_POST['ath']) != 'Local') {
			echo 'authentication error';
		} else {
			$writexml = fopen($euryscoinstallpath . '\\users\\' . $changeuser . '.xml', 'w');
			fwrite($writexml, base64_encode(base64_encode($_POST['xml'])));
			fclose($writexml);
			if ($action != '') { $_SESSION['serverstatus'] = 'run'; $audit = date('r') . '     ' . $changeuser . '     ' . $envcomputername . '     user management     cpm ' . $action; }
		}
	}
}

include('/auditlog.php');

exit;

?>