<?php

if (!isset($_SESSION['session'])) { exit; }
if ($_SESSION['session'] != hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id())) { exit; }

if ($audit != '') {
	$auditresponse = 'local';
	if (isset($agentstatus) && isset($eurysco_serverconaddress) && isset($eurysco_serverconport) && isset($eurysco_serverconpassword)) {
		if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername)) {
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$mcrykey = pack('H*', hash('sha256', hash('sha512', 'vNqgi_R1QX%C;z-724p4lFHm*?7c!e2%vG9tp+-*@#%=?!_;./' . hash('tiger128,4', $eurysco_serverconport) . '-*@#%=?!_;./-f;bTh2XXqW%Zs%88+/-7pVb;X')));
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $eurysco_sslverifypeer);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $eurysco_sslverifyhost);
			curl_setopt($ch, CURLOPT_URL, $eurysco_serverconaddress . ':' . $eurysco_serverconport . '/auditlog.php');
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10000);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
			curl_setopt($ch, CURLOPT_USERPWD, hash('sha256', $eurysco_serverconport . 'euryscoServer' . $eurysco_serverconport) . ':' . mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($eurysco_serverconpassword), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($eurysco_serverconpassword), 0, $iv_size)));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($ch, CURLOPT_POST, true);
			$data = array(
				'auditlog' => $audit
			);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			if (curl_exec($ch) === false) {
				$auditresponse = 'local';
			} else {
				$auditresponse = 'remote';
			}
			curl_close($ch);
		}
	}
	if ($auditresponse == 'local') {
		$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '\\audit\\audit-' . date('Ym') . '_' . date('M-Y') . '.log', 'a');
		fwrite($fp, $audit . "\r\n");
		fclose($fp);
		$auditsec = explode('     ', $audit);
		if (isset($_SESSION['serverstatus'])) {
			if ($_SESSION['serverstatus'] == 'run') {
				$dbaudit = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoAudit');
				$dbaudit->busyTimeout(30000);
				$dbaudit->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');
				$dbaudit->query('INSERT INTO auditLog (date, user, node, type, description) VALUES ("' . trim($auditsec[0]) . '", "' . trim($auditsec[1]) . '", "' . trim($auditsec[2]) . '", "' . trim($auditsec[3]) . '", "' . urlencode(trim($auditsec[4])) . '")');
				$dbaudit->close();
			}
		}
		exec('eventcreate.exe /l "Application" /t INFORMATION /so "eurysco Audit : ' . $auditsec[1] . ' : ' . $auditsec[2] . '" /id 1 /d "' . str_replace('"', '\'', $audit) . '"', $errorarray, $errorlevel);
	}
}



foreach (get_defined_vars() as $key=>$val) {
	if ($key != '_GET' && $key != '_POST' && $key != '_COOKIE' && $key != '_FILES' && $key != '_SERVER' && $key != '_SESSION' && $key != '_ENV' && $key != 'serverstatus' && $key != 'zipinfotop' && $key != 'zipoutput' && $key != 'zipinfobottom') {
		$$key = null;
		unset($$key);
	}
}

?>