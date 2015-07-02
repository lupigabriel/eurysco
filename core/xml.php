<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }

if (!isset($_GET['export'])) {
	exit;
}

if (!isset($_GET['source'])) {
	exit;
}

include('/include/init.php');

set_time_limit(60);

$table = 'eurysco_' . $_GET['source'] . '_' . $_GET['export'] . '_' . time();

header("Pragma: public"); 
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Cache-Control: private", false); 
header("Content-Type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=\"$table.xml\";" ); 
header("Content-Transfer-Encoding: binary");  

$nodepath = str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $_GET['source'] . '\\';

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$db = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoServer');
$db->busyTimeout(30000);
$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');
$xml = $db->querySingle('SELECT xml FROM xml' . ucfirst($_GET['export']) . ' WHERE node = "' . $_GET['source'] . '"');
if ($xml == '') {
	$fp = gzopen($nodepath . strtolower($_GET['export']) . '.xml.gz', 'rb');
	$bl = '';
	while (!feof($fp)) {
		$gz = gzread($fp, 2048);
		$bl = $bl . $gz;
	}
	fclose($fp);
	$xml = $bl;
}
echo $xml;
$db->close();

$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     xml export     export request for "' . $table . '.xml" completed';
include('/auditlog.php');

exit;

?>