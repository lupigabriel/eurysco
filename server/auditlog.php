<?php

include(str_replace('\\server', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_server.php');

set_time_limit(60);

if (isset($_POST['cid'])) {
	$cid = $_POST['cid'];
} else {
	$cid = '';
}

if (isset($_POST['exitcode'])) {
	$exitcode = $_POST['exitcode'];
} else {
	$exitcode = '';
}

if (isset($_POST['auditlog'])) {
	if ($_POST['auditlog'] != '') {
		$fp = fopen($euryscoinstallpath . '\\audit\\audit-' . date('Ym') . '_' . date('M-Y') . '.log', 'a');
		fwrite($fp, $_POST['auditlog'] . "\r\n");
		fclose($fp);
		$auditsec = explode('     ', $_POST['auditlog']);
		$dbaudit = new SQLite3($euryscoinstallpath . '\\sqlite\\euryscoAudit');
		$dbaudit->busyTimeout(30000);
		$dbaudit->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');
		$dbaudit->query('INSERT INTO auditLog (date, cid, user, node, type, description, exitcode) VALUES ("' . trim($auditsec[0]) . '", "' . $cid . '", "' . trim($auditsec[1]) . '", "' . trim($auditsec[2]) . '", "' . trim($auditsec[3]) . '", "' . urlencode(trim($auditsec[4])) . '", "' . $exitcode . '")');
		$dbaudit->close();
		exec('eventcreate.exe /l "Application" /t INFORMATION /so "eurysco Audit : ' . $auditsec[1] . ' : ' . $auditsec[2] . '" /id 1 /d "' . str_replace('"', '\'', $_POST['auditlog']) . '"', $errorarray, $errorlevel);
	}
}

if (extension_loaded('zlib')) { ob_end_flush(); }

?>