<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['coreconfig'] > 0) {  } else { header('location: /'); exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Core<small>config</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-core-button big page-back"></a>
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
						if(isset($_POST['coreservicedisplayname']) && $_POST['coreservicedisplayname'] != '') {
							$coreservicedisplayname = $_POST['coreservicedisplayname'];
							$coreservicedisplayname_ph = '';
							$coreservicedisplayname_xml = $coreservicedisplayname;
						} else {
							if (file_exists($config_coresrv)) {
								$coreservicedisplayname = $xmlcore->settings->coreservicedisplayname;
								$coreservicedisplayname_ph = '';
								$coreservicedisplayname_xml = $coreservicedisplayname;
							} else {
								$coreservicedisplayname = '';
								$coreservicedisplayname_ph = 'eurysco Core';
								$coreservicedisplayname_xml = $coreservicedisplayname_ph;
							}
						}

						if(isset($_POST['coreservicename']) && $_POST['coreservicename'] != '') {
							$coreservicename = $_POST['coreservicename'];
							$coreservicename_ph = '';
							$coreservicename_xml = $coreservicename;
						} else {
							if (file_exists($config_coresrv)) {
								$coreservicename = $xmlcore->settings->coreservicename;
								$coreservicename_ph = '';
								$coreservicename_xml = $coreservicename;
							} else {
								$coreservicename = '';
								$coreservicename_ph = $eurysco_coresrv;
								$coreservicename_xml = $coreservicename_ph;
							}
						}
						
						if(isset($_POST['coreservicestartuptype']) && $_POST['coreservicestartuptype'] != '') {
							$coreservicestartuptype = $_POST['coreservicestartuptype'];
							$coreservicestartuptype_ph = '';
							$coreservicestartuptype_xml = $coreservicestartuptype;
						} else {
							if (file_exists($config_coresrv)) {
								$coreservicestartuptype = $xmlcore->settings->coreservicestartuptype;
								$coreservicestartuptype_ph = '';
								$coreservicestartuptype_xml = $coreservicestartuptype;
							} else {
								$coreservicestartuptype = '';
								$coreservicestartuptype_ph = 'auto';
								$coreservicestartuptype_xml = $coreservicestartuptype_ph;
							}
						}
						
						if(isset($_POST['coreservicelogonas']) && $_POST['coreservicelogonas'] != '') {
							$coreservicelogonas = $_POST['coreservicelogonas'];
							$coreservicelogonas_ph = '';
							$coreservicelogonas_xml = $coreservicelogonas;
						} else {
							if (file_exists($config_coresrv)) {
								$coreservicelogonas = $xmlcore->settings->coreservicelogonas;
								$coreservicelogonas_ph = '';
								$coreservicelogonas_xml = $coreservicelogonas;
							} else {
								$coreservicelogonas = '';
								$coreservicelogonas_ph = 'LocalSystem';
								$coreservicelogonas_xml = $coreservicelogonas_ph;
							}
						}
						
						if(isset($_POST['corelisteningport']) && $_POST['corelisteningport'] != '') {
							$corelisteningport = $_POST['corelisteningport'];
							$corelisteningport_ph = '';
							$corelisteningport_xml = $corelisteningport;
						} else {
							if (file_exists($config_coresrv)) {
								$corelisteningport = $xmlcore->settings->corelisteningport;
								$corelisteningport_ph = '';
								$corelisteningport_xml = $corelisteningport;
							} else {
								$corelisteningport = '';
								$corelisteningport_ph = '59980';
								$corelisteningport_xml = $corelisteningport_ph;
							}
						}
						
						if(isset($_POST['corephpport']) && $_POST['corephpport'] != '') {
							$corephpport = $_POST['corephpport'];
							$corephpport_ph = '';
							$corephpport_xml = $corephpport;
						} else {
							if (file_exists($config_coresrv)) {
								$corephpport = $xmlcore->settings->corephpport;
								$corephpport_ph = '';
								$corephpport_xml = $corephpport;
							} else {
								$corephpport = '';
								$corephpport_ph = '59970';
								$corephpport_xml = $corephpport_ph;
							}
						}
						
						if (isset($_POST['submitform']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
							$coreservicename_last = '';
							if (isset($xmlcore) == 1) { $coreservicename_last = $xmlcore->settings->coreservicename; }
							if (isset($_POST['startservice'])) {
								session_write_close();
								exec('net.exe start ' . $coreservicename_last, $errorarray, $errorlevel);
								exec('net.exe start ' . $coreservicename_last . 'SSL', $errorarray, $errorlevelssl);
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core started';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core not started';
								}
							}
							if (isset($_POST['stopservice'])) {
								session_write_close();
								exec('taskkill.exe /f /im "httpd_eurysco_core.exe" /t', $errorarray, $errorlevelssl);
								exec('net.exe stop ' . $coreservicename_last . 'SSL');
								exec('taskkill.exe /f /im "php_eurysco_core.exe" /t', $errorarray, $errorlevel);
								exec('net.exe stop ' . $coreservicename_last);
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core stopped';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core not stopped';
								}
							}
							if (isset($_POST['deleteconfiguration']) && !isset($_POST['stopservice']) && !isset($_POST['startservice'])) {
								session_write_close();
								exec('net.exe stop ' . $coreservicename_last . 'SSL', $errorarray, $errorlevelssl);
								if ($errorlevelssl == 0 || $errorlevelssl == 2) { exec('sc.exe delete ' . $coreservicename_last . 'SSL', $errorarray, $errorlevelssl); }
								exec('net.exe stop ' . $coreservicename_last, $errorarray, $errorlevel);
								if ($errorlevel == 0 || $errorlevel == 2) { exec('sc.exe delete ' . $coreservicename_last, $errorarray, $errorlevel); }
								if ($errorlevel == 0 && $errorlevelssl == 0) {
									@unlink($config_coresrv);
									@unlink($euryscoinstallpath . '\\cert\\eurysco_core.crt');
									@unlink($euryscoinstallpath . '\\cert\\eurysco_core.key');
									@unlink($euryscoinstallpath . '\\cert\\eurysco_core.csr');
									@unlink($euryscoinstallpath . '\\cert\\eurysco_core.req');
								}
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core configuration deleted';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core configuration not deleted';
								}
							}
							if (isset($_POST['editcreateservice']) && !isset($_POST['deleteconfiguration'])) {
								if (isset($_POST['coretrustedcertificate'])) {
									if (!preg_match_all('/eurysco .* SSL Trusted Certificate/', $_POST['coretrustedcertificate'])) {
										$fp = @fopen($euryscoinstallpath . '\\cert\\eurysco_core.crt', 'w');
										@fwrite($fp, $_POST['coretrustedcertificate']);
										@fclose($fp);
										@unlink($euryscoinstallpath . '\\cert\\eurysco_core.csr');
									}
								}
								if (is_null($coreservicename_last) || $coreservicename_last == '') { $coreservicename_last = 'eurysco_NULL_'; }
								$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<coreservicedisplayname>' . $coreservicedisplayname_xml . '</coreservicedisplayname>' . "\n" . '		<coreservicename>' . $coreservicename_xml . '</coreservicename>' . "\n" . '		<coreservicestartuptype>' . $coreservicestartuptype_xml . '</coreservicestartuptype>' . "\n" . '		<coreservicelogonas>' . $coreservicelogonas_xml . '</coreservicelogonas>' . "\n" . '		<corelisteningport>' . $corelisteningport_xml . '</corelisteningport>' . "\n" . '		<corephpport>' . $corephpport_xml . '</corephpport>' . "\n" . '	</settings>' . "\n" . '</config>';
								$sxe = new SimpleXMLElement($xml);
								$sxe->asXML($config_coresrv);
								session_write_close();
								exec('set sslprotocol=TLSv1.2 & "' . $euryscoinstallpath . '\\euryscosrv.bat" "' . $coreservicename_last . '" "' . $coreservicename_xml . '" "' . $coreservicestartuptype_xml . '" "' . $coreservicelogonas_xml . '" "' . $coreservicedisplayname_xml . '" "' . $corelisteningport_xml . '" "' . $corephpport_xml . '" "eurysco_core" "core"', $errorarray, $errorlevel);
								$fp = fopen($euryscoinstallpath . '\\chromium\\euryscoLogin.prt', 'w');
								fwrite($fp, $corelisteningport_xml);
								fclose($fp);
								session_start();
								if ($errorlevel == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core configuration edited';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core configuration not edited';
								}
							}
							header('location: ' . $_SERVER['PHP_SELF']);
							exit;
						}
						
						?>

                        <?php $span = '6'; ?>
						<?php include($euryscoinstallpath . '\\include\\core_status_' . $corestatus . '.php'); ?>
                        <p>&nbsp;</p>
                    	<form method="post">
						<div class="input-control text">
                        	<h3>Core Service Display Name:</h3>
							<input type="text" id="coreservicedisplayname" name="coreservicedisplayname" placeholder="<?php echo $coreservicedisplayname_ph; ?>" value="<?php echo $coreservicedisplayname; ?>" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Core Service Name:</h3>
							<input type="text" id="coreservicename" name="coreservicename" placeholder="<?php echo $coreservicename_ph; ?>" value="<?php echo $coreservicename; ?>" disabled="disabled" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control select">
                        	<h3>Core Service Startup Type:</h3>
							<select id="coreservicestartuptype" name="coreservicestartuptype" disabled="disabled">
								<option value="auto" <?php if ($coreservicestartuptype == 'auto') { echo 'selected'; } ?>>Automatic</option>
								<option value="demand" <?php if ($coreservicestartuptype == 'demand') { echo 'selected'; } ?>>Manual</option>
								<option value="disabled" <?php if ($coreservicestartuptype == 'disabled') { echo 'selected'; } ?>>Disabled</option>
							</select>
						</div>
						<div class="input-control text">
                        	<h3>Core Service Log On As:</h3>
							<input type="text" id="coreservicelogonas" name="coreservicelogonas" placeholder="<?php echo $coreservicelogonas_ph; ?>" value="<?php echo $coreservicelogonas; ?>" disabled="disabled" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Core PHP Local Port:</h3>
							<input type="text" id="corephpport" name="corephpport" placeholder="<?php echo $corephpport_ph; ?>" value="<?php echo $corephpport; ?>" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Core Listening SSL Port:</h3>
							<input type="text" id="corelisteningport" name="corelisteningport" placeholder="<?php echo $corelisteningport_ph; ?>" value="<?php echo $corelisteningport; ?>" />
							<button class="btn-clear"></button>
							<textarea wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:100px; font-weight:normal;" disabled="disabled"><?php
							
							if (file_exists($euryscoinstallpath . '\\cert\\eurysco_core.csr')) {
								echo 'eurysco Core SSL Self-Signed Certificate Information' . "\n\n";
							} else {
								echo 'eurysco Core SSL Trusted Certificate Information' . "\n\n";
							}
							
							if (file_exists($euryscoinstallpath . '\\cert\\eurysco_core.crt')) {
								
								$data = openssl_x509_parse(file_get_contents($euryscoinstallpath . '\\cert\\eurysco_core.crt'));

								$validFrom = date('d/m/Y H:i:s', $data['validFrom_time_t']);
								$validTo = date('d/m/Y H:i:s', $data['validTo_time_t']);
								
								$fp = fopen($euryscoinstallpath . '\\cert\\eurysco_core.crt', 'r'); 
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
						<?php $name = $euryscoinstallpath . '\\cert\\eurysco_core.csr'; if (file_exists($name) && is_readable($name)) { ?>
						<div class="input-control text">
                        	<h3>Core SSL Certificate Request:</h3>
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
                        	<h3>Core SSL Import Certificate:</h3>
							<textarea id="coretrustedcertificate" name="coretrustedcertificate" wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:10px; height:100px; font-weight:normal;">-----BEGIN CERTIFICATE-----

							
							
eurysco Core SSL Trusted Certificate ( Base 64 Encoded )



-----END CERTIFICATE-----</textarea>
						</div>
						<?php } else { ?>
						<div class="input-control text">
                        	<h3>Core SSL Trusted Certificate:</h3>
							<textarea wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:10px; height:100px; font-weight:normal;" disabled="disabled"><?php
								$name = $euryscoinstallpath . '\\cert\\eurysco_core.crt';
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
						<?php if ($corestatus != 'cfg') { ?>
						<br />
							<input type="checkbox" id="deleteconfiguration" name="deleteconfiguration" />
                            <span class="helper">&nbsp;Delete Core Configuration</span>
                        <?php } ?>
                        <p>&nbsp;</p>
	                        <input type="hidden" id="submitform" name="submitform" value="" />
	                        <input type="submit" id="editcreateservice" name="editcreateservice" value="<?php if ($corestatus != 'cfg') { echo 'Edit Core'; } else { echo 'Create Core'; } ?>" style="background-color:#0072C6;" />
                            <?php if ($corestatus == 'run') { echo '<input type="submit" id="stopservice" name="stopservice" class="bg-color-red" value="Stop Core Service"/>'; } ?>
                            <?php if ($corestatus == 'nrn') { echo '<input type="submit" id="startservice" name="startservice" class="bg-color-green" value="Start Core Service"/>'; } ?>
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