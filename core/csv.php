<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }

if (!isset($_GET['export'])) {
	exit;
}

if (!isset($_GET['source'])) {
	exit;
}

include('/include/init.php');

if (!isset($_SESSION[$_GET['export']])) {
	header('location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

if ($_SESSION[$_GET['export']] == '') {
	header('location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

set_time_limit(60);

$table = 'eurysco_' . $_GET['source'] . '_' . $_GET['export'] . '_' . time();

header("Pragma: public"); 
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Cache-Control: private", false); 
header("Content-Type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=\"$table.csv\";" ); 
header("Content-Transfer-Encoding: binary");  

foreach ($_SESSION[$_GET['export']] as $csvrow) {
	echo $csvrow;
}

$_SESSION[$_GET['export']] = '';

$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     csv export     export request for "' . $table . '.csv" completed';
include('/auditlog.php');

exit;

?>