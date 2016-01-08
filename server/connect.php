<?php

include(str_replace('\\server', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_server.php');

set_time_limit(60);

$agentkeyresp = '<agentkeyresp>null</agentkeyresp>' . "\n";
if (isset($_POST['agentkey']) && isset($_POST['computername'])) {
	$corepath = $euryscoinstallpath . '\\core';
	$nodespath = $euryscoinstallpath . '\\nodes';

	if (!file_exists($nodespath . '\\' . strtolower($_POST['computername']) . '\\')) { mkdir($nodespath . '\\' . strtolower($_POST['computername']) . '\\', 0777); @copy($euryscoinstallpath . '\\conf\\config_settings.xml', $nodespath . '\\' . strtolower($_POST['computername']) . '\\config_settings.xml'); }

	if (strlen($_POST['agentkey']) > 32) {
		$mcrykeycmd = pack('H*', hash('sha256', md5(strtolower($_POST['computername']))));
		$_POST['agentkey'] = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykeycmd, substr(base64_decode($_POST['agentkey']), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($_POST['agentkey']), 0, $iv_size)));
		$fp = fopen($nodespath . '\\' . strtolower($_POST['computername']) . '\\agent.key', 'w');
		fwrite($fp, trim($_POST['agentkey']));
		fclose($fp);
		$agentkeyresp = '<agentkeyresp>' . hash('sha512', $_POST['agentkey']) . '</agentkeyresp>' . "\n";
	}
	if (strlen($_POST['agentkey']) == 32 && file_exists($nodespath . '\\' . strtolower($_POST['computername']) . '\\agent.key')) { 
		$agentkeyresp = '<agentkeyresp>' . md5($_POST['agentkey'] . fgets(fopen($nodespath . '\\' . strtolower($_POST['computername']) . '\\agent.key', 'r'))) . '</agentkeyresp>' . "\n";
	}
}

echo $agentkeyresp;
echo '</euryscoServer>';

foreach (get_defined_vars() as $key=>$val) {
	if ($key != '_GET' && $key != '_POST' && $key != '_COOKIE' && $key != '_FILES' && $key != '_SERVER' && $key != '_SESSION' && $key != '_ENV') {
		$$key = null;
		unset($$key);
	}
}

if (extension_loaded('zlib')) { ob_end_flush(); }

?>