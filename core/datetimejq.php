<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/settings.php')) { exit; }

include('/include/init.php');

$datetime = '<div class="input-control text"><input type="text" value="' . date('r') . '" disabled="disabled" /></div>';

echo json_encode(array('datetime'=>utf8_encode($datetime)));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>