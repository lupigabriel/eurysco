<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/settings.php')) { exit; }

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

$datetime = '<div class="input-control text"><input type="text" value="' . date('r') . '" disabled="disabled" /></div>';

echo json_encode(array('datetime'=>utf8_encode($datetime)));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>