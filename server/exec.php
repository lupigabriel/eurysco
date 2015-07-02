<?php

include('/include/init.php');

set_time_limit(60);

$nodespath = str_replace('\\server', '\\nodes', $_SERVER['DOCUMENT_ROOT']);

if (isset($_POST['computername'])) {
	$computername = $_POST['computername'];
} else {
	exit;
}

if (isset($_POST['clearcommands'])) {
	$clearcommands = $_POST['clearcommands'];
} else {
	exit;
}

@unlink($nodespath . '\\' . $computername . '\\exec.on');

echo '<exec>' . "\n";

$execcount = 0;
$execlist = scandir($nodespath . '\\' . $computername . '\\');
foreach($execlist as $exec) {
	if(pathinfo($exec)['extension'] == 'exec') {
		if (filesize($nodespath . '\\' . $computername . '\\' . $exec) > 0 && $clearcommands == 'off') {
			$execxml = simplexml_load_file($nodespath . '\\' . $computername . '\\' . $exec);
			echo '<exe' . $execcount . '>' . "\n";
			echo '<auditok>' . $execxml->auditok . '</auditok>' . "\n";
			echo '<auditko>' . $execxml->auditko . '</auditko>' . "\n";
			echo '<auditnl>' . $execxml->auditnl . '</auditnl>' . "\n";
			echo '<cid>' . $execxml->cid . '</cid>' . "\n";
			echo '<timeout>' . $execxml->timeout . '</timeout>' . "\n";
			echo '<command>' . $execxml->command . '</command>' . "\n";
			echo '</exe' . $execcount . '>' . "\n";
			$execcount = $execcount + 1;
		}
		@unlink($nodespath . '\\' . $computername . '\\' . $exec);
	}
}

echo '</exec>';



foreach (get_defined_vars() as $key=>$val) {
	if ($key != '_GET' && $key != '_POST' && $key != '_COOKIE' && $key != '_FILES' && $key != '_SERVER' && $key != '_SESSION' && $key != '_ENV') {
		$$key = null;
		unset($$key);
	}
}

if (extension_loaded('zlib')) { ob_end_flush(); }

?>