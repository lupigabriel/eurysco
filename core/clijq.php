<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/cli.php')) { exit; }

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['commandline'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

$input = strtolower( $_GET['input'] );
$len = strlen($input);
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;

$aResultsFld = array();
$aResultsFls = array();
$aResultsSrv = array();
$aResultsPrc = array();
$countFld = 0;
$countFls = 0;
$countSrv = 0;
$countPrc = 0;

if ($len) {
	for ($i=0;$i<count($_SESSION['cmdlistfld']);$i++) {
		if (strtolower(substr(utf8_decode($_SESSION['cmdlistfld'][$i]),0,$len)) == $input) {
			$countFld++;
			$aResultsFld[] = array( 'id'=>($i+1) ,'value'=>$_SESSION['cmdlistfld'][$i] );
		}
		if ($limit && $countFld>=$limit) {
			break;
		}
	}
	for ($i=0;$i<count($_SESSION['cmdlistfls']);$i++) {
		if (strtolower(substr(utf8_decode($_SESSION['cmdlistfls'][$i]),0,$len)) == $input) {
			$countFls++;
			$aResultsFls[] = array( 'id'=>($i+1) ,'value'=>$_SESSION['cmdlistfls'][$i] );
		}
		if ($limit && $countFls>=$limit) {
			break;
		}
	}
	for ($i=0;$i<count($_SESSION['cmdlistsrv']);$i++) {
		if (strtolower(substr(utf8_decode($_SESSION['cmdlistsrv'][$i]),0,$len)) == $input) {
			$countSrv++;
			$aResultsSrv[] = array( 'id'=>($i+1) ,'value'=>$_SESSION['cmdlistsrv'][$i] );
		}
		if ($limit && $countSrv>=$limit) {
			break;
		}
	}
	for ($i=0;$i<count($_SESSION['cmdlistprc']);$i++) {
		if (strtolower(substr(utf8_decode($_SESSION['cmdlistprc'][$i]),0,$len)) == $input) {
			$countPrc++;
			$aResultsPrc[] = array( 'id'=>($i+1) ,'value'=>$_SESSION['cmdlistprc'][$i] );
		}
		if ($limit && $countPrc>=$limit) {
			break;
		}
	}
}

header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header ('Cache-Control: no-cache, must-revalidate');
header ('Pragma: no-cache');

if (isset($_REQUEST['json'])) {
	header('Content-Type: application/json');
	echo '{"results": [';
	$arr = array();
	for ($i=0;$i<count($aResultsFld);$i++) {
		$arr[] = '{\'id\': \''.$aResultsFld[$i]['value'].'\', \'value\': \''.$aResultsFld[$i]['value'].'\', \'info\': \'icon-folder\'}';
	}
	for ($i=0;$i<count($aResultsFls);$i++) {
		$arr[] = '{\'id\': \''.$aResultsFls[$i]['value'].'\', \'value\': \''.$aResultsFls[$i]['value'].'\', \'info\': \'icon-file\'}';
	}
	for ($i=0;$i<count($aResultsSrv);$i++) {
		$arr[] = '{\'id\': \''.$aResultsSrv[$i]['value'].'\', \'value\': \''.$aResultsSrv[$i]['value'].'\', \'info\': \'icon-cog\'}';
	}
	for ($i=0;$i<count($aResultsPrc);$i++) {
		$arr[] = '{\'id\': \''.$aResultsPrc[$i]['value'].'\', \'value\': \''.$aResultsPrc[$i]['value'].'\', \'info\': \'icon-bars\'}';
	}
		echo implode(", ", $arr);
		echo "]}";
}

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>