<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['serverconfig'] > 0) {  } else { header('location: /'); exit; } ?>

<?php if ($eurysco_serverconaddress != '' && $eurysco_serverconaddress != 'https://' . strtoupper($envcomputername)) { exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($passwordexpired == 0) { ?>

<?php

$osversion = '';
$wmios = $wmi->ExecQuery("SELECT * FROM Win32_OperatingSystem");
foreach($wmios as $os) {
	$osversion = preg_replace('/\..*/', '', $os->Version);
}

?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Server<small>config</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-server-button big page-back"></a>
		</div>
	</div>
</div>

<br />

<div class="page" id="page-index">
	<div class="page-region">
		<div class="page-region-content">
			<div class="grid">
				<div class="row">
					<div class="span2"></div>
					<div class="span6">
                    	<?php
						if(isset($_POST['serverservicedisplayname']) && $_POST['serverservicedisplayname'] != '') {
							$serverservicedisplayname = $_POST['serverservicedisplayname'];
							$serverservicedisplayname_ph = '';
							$serverservicedisplayname_xml = $serverservicedisplayname;
						} else {
							if (file_exists($config_server)) {
								$serverservicedisplayname = $xmlserver->settings->serverservicedisplayname;
								$serverservicedisplayname_ph = '';
								$serverservicedisplayname_xml = $serverservicedisplayname;
							} else {
								$serverservicedisplayname = '';
								$serverservicedisplayname_ph = 'eurysco Server';
								$serverservicedisplayname_xml = $serverservicedisplayname_ph;
							}
						}

						if(isset($_POST['serverservicename']) && $_POST['serverservicename'] != '') {
							$serverservicename = $_POST['serverservicename'];
							$serverservicename_ph = '';
							$serverservicename_xml = $serverservicename;
						} else {
							if (file_exists($config_server)) {
								$serverservicename = $xmlserver->settings->serverservicename;
								$serverservicename_ph = '';
								$serverservicename_xml = $serverservicename;
							} else {
								$serverservicename = '';
								$serverservicename_ph = $eurysco_serversrv;
								$serverservicename_xml = $serverservicename_ph;
							}
						}
						
						if(isset($_POST['serverservicestartuptype']) && $_POST['serverservicestartuptype'] != '') {
							$serverservicestartuptype = $_POST['serverservicestartuptype'];
							$serverservicestartuptype_ph = '';
							$serverservicestartuptype_xml = $serverservicestartuptype;
						} else {
							if (file_exists($config_server)) {
								$serverservicestartuptype = $xmlserver->settings->serverservicestartuptype;
								$serverservicestartuptype_ph = '';
								$serverservicestartuptype_xml = $serverservicestartuptype;
							} else {
								$serverservicestartuptype = '';
								$serverservicestartuptype_ph = 'auto';
								$serverservicestartuptype_xml = $serverservicestartuptype_ph;
							}
						}
						
						if(isset($_POST['serverservicelogonas']) && $_POST['serverservicelogonas'] != '') {
							$serverservicelogonas = $_POST['serverservicelogonas'];
							$serverservicelogonas_ph = '';
							$serverservicelogonas_xml = $serverservicelogonas;
						} else {
							if (file_exists($config_server)) {
								$serverservicelogonas = $xmlserver->settings->serverservicelogonas;
								$serverservicelogonas_ph = '';
								$serverservicelogonas_xml = $serverservicelogonas;
							} else {
								$serverservicelogonas = '';
								$serverservicelogonas_ph = 'LocalSystem';
								$serverservicelogonas_xml = $serverservicelogonas_ph;
							}
						}
						
						if(isset($_POST['serverlisteningport']) && $_POST['serverlisteningport'] != '') {
							$serverlisteningport = $_POST['serverlisteningport'];
							$serverlisteningport_ph = '';
							$serverlisteningport_xml = $serverlisteningport;
						} else {
							if (file_exists($config_server)) {
								$serverlisteningport = $xmlserver->settings->serverlisteningport;
								$serverlisteningport_ph = '';
								$serverlisteningport_xml = $serverlisteningport;
							} else {
								$serverlisteningport = '';
								$serverlisteningport_ph = '59982';
								$serverlisteningport_xml = $serverlisteningport_ph;
							}
						}
						
						if(isset($_POST['serverphpport']) && $_POST['serverphpport'] != '') {
							$serverphpport = $_POST['serverphpport'];
							$serverphpport_ph = '';
							$serverphpport_xml = $serverphpport;
						} else {
							if (file_exists($config_server)) {
								$serverphpport = $xmlserver->settings->serverphpport;
								$serverphpport_ph = '';
								$serverphpport_xml = $serverphpport;
							} else {
								$serverphpport = '';
								$serverphpport_ph = '59972';
								$serverphpport_xml = $serverphpport_ph;
							}
						}
						
						if(isset($_POST['sslprotocolversion']) && $_POST['sslprotocolversion'] != '') {
							$sslprotocolversion = $_POST['sslprotocolversion'];
							$sslprotocolversion_ph = '';
							$sslprotocolversion_xml = $sslprotocolversion;
						} else {
							if (file_exists($config_server)) {
								$sslprotocolversion = $xmlserver->settings->sslprotocolversion;
								$sslprotocolversion_ph = '';
								$sslprotocolversion_xml = $sslprotocolversion;
							} else {
								$sslprotocolversion = '';
								$sslprotocolversion_ph = 'TLSv1';
								$sslprotocolversion_xml = $sslprotocolversion_ph;
							}
						}
						
						if(isset($_POST['serverpassword'])) {
							$serverpassword = $_POST['serverpassword'];
							$serverpassword_ph = '&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;';
						} else {
							if (file_exists($config_server)) {
								$serverpassword = '';
								$serverpassword_ph = '&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;';
							} else {
								$serverpassword = '';
								$serverpassword_ph = 'Default Strong Password';
							}
						}
						
						if (isset($_POST['submitform']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
							$serverservicename_last = '';
							if (isset($xmlserver) == 1) { $serverservicename_last = $xmlserver->settings->serverservicename; }
							if (isset($_POST['startservice'])) {
								session_write_close();
								exec('net.exe start ' . $serverservicename_last, $errorarray, $errorlevel);
								exec('net.exe start ' . $serverservicename_last . 'SSL', $errorarray, $errorlevelssl);
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     server config     eurysco server started';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     server config     eurysco server not started';
								}
							}
							if (isset($_POST['stopservice'])) {
								session_write_close();
								exec('taskkill.exe /f /im "httpd_eurysco_server.exe" /t', $errorarray, $errorlevelssl);
								exec('net.exe stop ' . $serverservicename_last . 'SSL');
								exec('taskkill.exe /f /im "php_eurysco_server.exe" /t', $errorarray, $errorlevel);
								exec('net.exe stop ' . $serverservicename_last);
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     server config     eurysco server stopped';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     server config     eurysco server not stopped';
								}
							}
							if (isset($_POST['deleteconfiguration']) && !isset($_POST['stopservice']) && !isset($_POST['startservice'])) {
								session_write_close();
								exec('net.exe stop ' . $serverservicename_last . 'SSL', $errorarray, $errorlevelssl);
								if ($errorlevelssl == 0 || $errorlevelssl == 2) { exec('sc.exe delete ' . $serverservicename_last . 'SSL', $errorarray, $errorlevelssl); }
								exec('net.exe stop ' . $serverservicename_last, $errorarray, $errorlevel);
								if ($errorlevel == 0 || $errorlevel == 2) { exec('sc.exe delete ' . $serverservicename_last, $errorarray, $errorlevel); }
								if ($errorlevel == 0 && $errorlevelssl == 0) {
									@unlink($config_server);
									@unlink($euryscoinstallpath . '\\cert\\eurysco_server.crt');
									@unlink($euryscoinstallpath . '\\cert\\eurysco_server.key');
									@unlink($euryscoinstallpath . '\\cert\\eurysco_server.csr');
									@unlink($euryscoinstallpath . '\\cert\\eurysco_server.req');
									@unlink($euryscoinstallpath . '\\sqlite\\euryscoServer');
								}
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     server config     eurysco server configuration deleted';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     server config     eurysco server configuration not deleted';
								}
							}
							if (isset($_POST['editcreateservice']) && !isset($_POST['deleteconfiguration'])) {
								if (isset($_POST['servertrustedcertificate'])) {
									if (!preg_match_all('/eurysco .* SSL Trusted Certificate/', $_POST['servertrustedcertificate'])) {
										$fp = @fopen($euryscoinstallpath . '\\cert\\eurysco_server.crt', 'w');
										@fwrite($fp, $_POST['servertrustedcertificate']);
										@fclose($fp);
										@unlink($euryscoinstallpath . '\\cert\\eurysco_server.csr');
									}
								}
								if (is_null($serverservicename_last) || $serverservicename_last == '') { $serverservicename_last = 'eurysco_NULL_'; }
								$newusername = hash('sha256', $serverlisteningport_xml . 'euryscoServer' . $serverlisteningport_xml);
								$newusertype = 'vNqgi_R1QX%C;z-724p4lFHm*?7c!e2%vG9tp+-*@#%=?!_;./' . hash('tiger128,4', $serverlisteningport_xml) . '-*@#%=?!_;./-f;bTh2XXqW%Zs%88+/-7pVb;X';
								$newuserpsw = $serverpassword . 'OLb324MJ0E7n/uAg+-*@#%=?!_;./' . hash('haval128,5', $serverlisteningport_xml) . '?!_;./Hdgl3LpPwUlzC*J8%D-ZadL';
								$mcrykey = pack('H*', hash('sha256', hash('sha512', $newusername . $newusertype)));
								$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<serverservicedisplayname>' . $serverservicedisplayname_xml . '</serverservicedisplayname>' . "\n" . '		<serverservicename>' . $serverservicename_xml . '</serverservicename>' . "\n" . '		<serverservicestartuptype>' . $serverservicestartuptype_xml . '</serverservicestartuptype>' . "\n" . '		<serverservicelogonas>' . $serverservicelogonas_xml . '</serverservicelogonas>' . "\n" . '		<serverlisteningport>' . $serverlisteningport_xml . '</serverlisteningport>' . "\n" . '		<serverphpport>' . $serverphpport_xml . '</serverphpport>' . "\n" . '		<sslprotocolversion>' . $sslprotocolversion_xml . '</sslprotocolversion>' . "\n" . '		<username>' . $newusername . '</username>' . "\n" . '		<usertype>' . hash('sha512', $newusername . $newusertype) . '</usertype>' . "\n" . '		<password>' . base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykey, md5($newusername . ':' . $realm . ':' . $newuserpsw), MCRYPT_MODE_CBC, $iv)) . '</password>' . "\n" . '	</settings>' . "\n" . '</config>';
								$writexml = fopen($config_server, 'w');
								fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
								fclose($writexml);
								session_write_close();
								exec('set sslprotocol=' . $sslprotocolversion_xml . ' & "' . $euryscoinstallpath . '\\euryscosrv.bat" "' . $serverservicename_last . '" "' . $serverservicename_xml . '" "' . $serverservicestartuptype_xml . '" "' . $serverservicelogonas_xml . '" "' . $serverservicedisplayname_xml . '" "' . $serverlisteningport_xml . '" "' . $serverphpport_xml . '" "eurysco_server" "server"', $errorarray, $errorlevel);
								session_start();
								if ($errorlevel == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     server config     eurysco server configuration edited';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     server config     eurysco server configuration not edited';
								}
							}
							header('location: ' . $_SERVER['PHP_SELF']);
							exit;
						}
						
						?>

                        <?php $span = '6'; ?>
						<?php include($euryscoinstallpath . '\\include\\server_status_' . $serverstatus . '.php'); ?>
                        <p>&nbsp;</p>
                    	<form method="post">
						<div class="input-control text">
                        	<h3>Server Service Display Name:</h3>
							<input type="text" id="serverservicedisplayname" name="serverservicedisplayname" placeholder="<?php echo $serverservicedisplayname_ph; ?>" value="<?php echo $serverservicedisplayname; ?>" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Server Service Name:</h3>
							<input type="text" id="serverservicename" name="serverservicename" placeholder="<?php echo $serverservicename_ph; ?>" value="<?php echo $serverservicename; ?>" disabled="disabled" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control select">
                        	<h3>Server Service Startup Type:</h3>
							<select id="serverservicestartuptype" name="serverservicestartuptype">
								<option value="auto" <?php if ($serverservicestartuptype == 'auto') { echo 'selected'; } ?>>Automatic</option>
								<option value="demand" <?php if ($serverservicestartuptype == 'demand') { echo 'selected'; } ?>>Manual</option>
								<option value="disabled" <?php if ($serverservicestartuptype == 'disabled') { echo 'selected'; } ?>>Disabled</option>
							</select>
						</div>
						<div class="input-control text">
                        	<h3>Server Service Log On As:</h3>
							<input type="text" id="serverservicelogonas" name="serverservicelogonas" placeholder="<?php echo $serverservicelogonas_ph; ?>" value="<?php echo $serverservicelogonas; ?>" disabled="disabled" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Server PHP Local Port:</h3>
							<input type="text" id="serverphpport" name="serverphpport" placeholder="<?php echo $serverphpport_ph; ?>" value="<?php echo $serverphpport; ?>" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Server Listening SSL Port:</h3>
							<input type="text" id="serverlisteningport" name="serverlisteningport" placeholder="<?php echo $serverlisteningport_ph; ?>" value="<?php echo $serverlisteningport; ?>" />
							<button class="btn-clear"></button>
							<textarea wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:100px; font-weight:normal;" disabled="disabled"><?php
							
							if (file_exists($euryscoinstallpath . '\\cert\\eurysco_server.csr')) {
								echo 'eurysco Server SSL Self-Signed Certificate Information' . "\n\n";
							} else {
								echo 'eurysco Server SSL Trusted Certificate Information' . "\n\n";
							}
							
							if (file_exists($euryscoinstallpath . '\\cert\\eurysco_server.crt')) {
								
								$data = openssl_x509_parse(file_get_contents($euryscoinstallpath . '\\cert\\eurysco_server.crt'));

								$validFrom = date('d/m/Y H:i:s', $data['validFrom_time_t']);
								$validTo = date('d/m/Y H:i:s', $data['validTo_time_t']);
								
								$fp = fopen($euryscoinstallpath . '\\cert\\eurysco_server.crt', 'r'); 
								$cert = fread($fp, 8192); 
								fclose($fp); 

								echo 'Valid from ' . $validFrom . ' to ' . $validTo;
								echo "\n\n";
								echo openssl_x509_read($cert);
								echo "\n";
								echo "*********************";
								echo "\n";
								echo 'Parse' . "\n";
								print_r(openssl_x509_parse($cert));
								
							} else {
								echo 'Certificate is not available...';
							}
							
							?></textarea>
						</div>
						<?php $name = $euryscoinstallpath . '\\cert\\eurysco_server.csr'; if (file_exists($name) && is_readable($name)) { ?>
						<div class="input-control text">
                        	<h3>Server SSL Certificate Request:</h3>
							<textarea wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:10px; height:100px; font-weight:normal;" readonly="readonly"><?php
								$filearr = file($name);
								$lastlines = array_slice($filearr, -10000);
								$csroutput = '';
								foreach ($lastlines as $lastline) {
									$csroutput = $csroutput . $lastline;
								}
								echo $csroutput;
							?></textarea>
						<div class="input-control text">
						</div>
                        	<h3>Server SSL Import Certificate:</h3>
							<textarea id="servertrustedcertificate" name="servertrustedcertificate" wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:10px; height:100px; font-weight:normal;">-----BEGIN CERTIFICATE-----

							
							
eurysco Server SSL Trusted Certificate ( Base 64 Encoded )



-----END CERTIFICATE-----</textarea>
						</div>
						<?php } else { ?>
						<div class="input-control text">
                        	<h3>Server SSL Trusted Certificate:</h3>
							<textarea wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:10px; height:100px; font-weight:normal;" disabled="disabled"><?php
								$name = $euryscoinstallpath . '\\cert\\eurysco_server.crt';
								$csroutput = 'Trusted Certificate Not Found...';
								if (file_exists($name) && is_readable($name)) {
									$filearr = file($name);
									$lastlines = array_slice($filearr, -10000);
									$csroutput = '';
									foreach ($lastlines as $lastline) {
										$csroutput = $csroutput . $lastline;
									}
								}
								echo $csroutput;
							?></textarea>
						</div>
						<?php } ?>
						<div class="input-control select">
                        	<h3>SSL Protocol Version:</h3>
							<select id="sslprotocolversion" name="sslprotocolversion">
								<option value="TLSv1" <?php if ($sslprotocolversion == 'TLSv1') { echo 'selected'; } ?>>TLS 1.0</option>
								<option value="TLSv1.2" <?php if ($sslprotocolversion == 'TLSv1.2') { echo 'selected'; } if ($osversion < 6) { echo ' disabled="disabled"'; } ?>>TLS 1.2</option>
							</select>
							<div style="font-size:12px;">* TLS 1.2 is not compatible with Windows XP and 2003 nodes</div>
						</div>
						<div class="input-control text">
                        	<h3>Server Password:</h3>
							<?php if ($changepwdlocalsetting == 'Enable' && $_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1' && $_SERVER['HTTP_X_FORWARDED_FOR'] != '::1') { ?>
							<input type="hidden" id="serverpassword" name="serverpassword" value="<?php echo $serverpassword; ?>" />
							<input type="text" value="change password is allowed only for local connection" maxlength="30" disabled="disabled" />
							<button class="btn-clear"></button>
							<?php } else { ?>
							<input type="password" autocomplete="off" id="serverpassword" name="serverpassword" placeholder="<?php echo $serverpassword_ph; ?>" value="<?php echo $serverpassword; ?>" maxlength="30" />
							<button class="btn-clear"></button>
							<?php } ?>
						</div>
						<?php if ($serverstatus != 'cfg') { ?>
						<br />
							<input type="checkbox" id="deleteconfiguration" name="deleteconfiguration" />
                            <span class="helper">&nbsp;Delete Server Configuration</span>
                        <?php } ?>
                        <p>&nbsp;</p>
	                        <input type="hidden" id="submitform" name="submitform" value="" />
	                        <input type="submit" id="editcreateservice" name="editcreateservice" value="<?php if ($serverstatus != 'cfg') { echo 'Edit Server'; } else { echo 'Create Server'; } ?>" style="background-color:#636363;" />
                            <?php if ($serverstatus == 'run') { echo '<input type="submit" id="stopservice" name="stopservice" class="bg-color-red" value="Stop Server Service"/>'; } ?>
                            <?php if ($serverstatus == 'nrn') { echo '<input type="submit" id="startservice" name="startservice" class="bg-color-purple" value="Start Server Service"/>'; } ?>
							<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>