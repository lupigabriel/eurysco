<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['executorconfig'] > 0) {  } else { header('location: /'); exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Executor<small>config</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-executor-button big page-back"></a>
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
						if(isset($_POST['executorservicedisplayname']) && $_POST['executorservicedisplayname'] != '') {
							$executorservicedisplayname = $_POST['executorservicedisplayname'];
							$executorservicedisplayname_ph = '';
							$executorservicedisplayname_xml = $executorservicedisplayname;
						} else {
							if (file_exists($config_executorsrv)) {
								$executorservicedisplayname = $xmlexecutor->settings->executorservicedisplayname;
								$executorservicedisplayname_ph = '';
								$executorservicedisplayname_xml = $executorservicedisplayname;
							} else {
								$executorservicedisplayname = '';
								$executorservicedisplayname_ph = 'eurysco Executor';
								$executorservicedisplayname_xml = $executorservicedisplayname_ph;
							}
						}

						if(isset($_POST['executorservicename']) && $_POST['executorservicename'] != '') {
							$executorservicename = $_POST['executorservicename'];
							$executorservicename_ph = '';
							$executorservicename_xml = $executorservicename;
						} else {
							if (file_exists($config_executorsrv)) {
								$executorservicename = $xmlexecutor->settings->executorservicename;
								$executorservicename_ph = '';
								$executorservicename_xml = $executorservicename;
							} else {
								$executorservicename = '';
								$executorservicename_ph = $eurysco_executorsrv;
								$executorservicename_xml = $executorservicename_ph;
							}
						}
						
						if(isset($_POST['executorservicestartuptype']) && $_POST['executorservicestartuptype'] != '') {
							$executorservicestartuptype = $_POST['executorservicestartuptype'];
							$executorservicestartuptype_ph = '';
							$executorservicestartuptype_xml = $executorservicestartuptype;
						} else {
							if (file_exists($config_executorsrv)) {
								$executorservicestartuptype = $xmlexecutor->settings->executorservicestartuptype;
								$executorservicestartuptype_ph = '';
								$executorservicestartuptype_xml = $executorservicestartuptype;
							} else {
								$executorservicestartuptype = '';
								$executorservicestartuptype_ph = 'auto';
								$executorservicestartuptype_xml = $executorservicestartuptype_ph;
							}
						}
						
						if(isset($_POST['executorservicelogonas']) && $_POST['executorservicelogonas'] != '') {
							$executorservicelogonas = $_POST['executorservicelogonas'];
							$executorservicelogonas_ph = '';
							$executorservicelogonas_xml = $executorservicelogonas;
						} else {
							if (file_exists($config_executorsrv)) {
								$executorservicelogonas = $xmlexecutor->settings->executorservicelogonas;
								$executorservicelogonas_ph = '';
								$executorservicelogonas_xml = $executorservicelogonas;
							} else {
								$executorservicelogonas = '';
								$executorservicelogonas_ph = 'LocalSystem';
								$executorservicelogonas_xml = $executorservicelogonas_ph;
							}
						}
						
						if(isset($_POST['executorlisteningport']) && $_POST['executorlisteningport'] != '') {
							$executorlisteningport = $_POST['executorlisteningport'];
							$executorlisteningport_ph = '';
							$executorlisteningport_xml = $executorlisteningport;
						} else {
							if (file_exists($config_executorsrv)) {
								$executorlisteningport = $xmlexecutor->settings->executorlisteningport;
								$executorlisteningport_ph = '';
								$executorlisteningport_xml = $executorlisteningport;
							} else {
								$executorlisteningport = '';
								$executorlisteningport_ph = '59981';
								$executorlisteningport_xml = $executorlisteningport_ph;
							}
						}
						
						if(isset($_POST['executorphpport']) && $_POST['executorphpport'] != '') {
							$executorphpport = $_POST['executorphpport'];
							$executorphpport_ph = '';
							$executorphpport_xml = $executorphpport;
						} else {
							if (file_exists($config_executorsrv)) {
								$executorphpport = $xmlexecutor->settings->executorphpport;
								$executorphpport_ph = '';
								$executorphpport_xml = $executorphpport;
							} else {
								$executorphpport = '';
								$executorphpport_ph = '59971';
								$executorphpport_xml = $executorphpport_ph;
							}
						}
						
						if (isset($_POST['submitform']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
							$executorservicename_last = '';
							if (isset($xmlexecutor) == 1) { $executorservicename_last = $xmlexecutor->settings->executorservicename; }
							if (isset($_POST['startservice'])) {
								session_write_close();
								exec('net.exe start ' . $executorservicename_last, $errorarray, $errorlevel);
								exec('net.exe start ' . $executorservicename_last . 'SSL', $errorarray, $errorlevelssl);
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     executor config     eurysco executor started';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     executor config     eurysco executor not started';
								}
							}
							if (isset($_POST['stopservice'])) {
								session_write_close();
								exec('taskkill.exe /f /im "httpd_eurysco_executor.exe" /t', $errorarray, $errorlevelssl);
								exec('net.exe stop ' . $executorservicename_last . 'SSL');
								exec('taskkill.exe /f /im "php_eurysco_executor.exe" /t', $errorarray, $errorlevel);
								exec('net.exe stop ' . $executorservicename_last);
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     executor config     eurysco executor stopped';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     executor config     eurysco executor not stopped';
								}
							}
							if (isset($_POST['deleteconfiguration']) && !isset($_POST['stopservice']) && !isset($_POST['startservice'])) {
								session_write_close();
								exec('net.exe stop ' . $executorservicename_last . 'SSL', $errorarray, $errorlevelssl);
								if ($errorlevelssl == 0 || $errorlevelssl == 2) { exec('sc.exe delete ' . $executorservicename_last . 'SSL', $errorarray, $errorlevelssl); }
								exec('net.exe stop ' . $executorservicename_last, $errorarray, $errorlevel);
								if ($errorlevel == 0 || $errorlevel == 2) { exec('sc.exe delete ' . $executorservicename_last, $errorarray, $errorlevel); }
								if ($errorlevel == 0 && $errorlevelssl == 0) {
									@unlink($config_executorsrv);
									@unlink($euryscoinstallpath . '\\cert\\eurysco_executor.crt');
									@unlink($euryscoinstallpath . '\\cert\\eurysco_executor.key');
									@unlink($euryscoinstallpath . '\\cert\\eurysco_executor.csr');
									@unlink($euryscoinstallpath . '\\cert\\eurysco_executor.req');
								}
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     executor config     eurysco executor configuration deleted';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     executor config     eurysco executor configuration not deleted';
								}
							}
							if (isset($_POST['editcreateservice']) && !isset($_POST['deleteconfiguration'])) {
								if (isset($_POST['executortrustedcertificate'])) {
									if (!preg_match_all('/eurysco .* SSL Trusted Certificate/', $_POST['executortrustedcertificate'])) {
										$fp = @fopen($euryscoinstallpath . '\\cert\\eurysco_executor.crt', 'w');
										@fwrite($fp, $_POST['executortrustedcertificate']);
										@fclose($fp);
										@unlink($euryscoinstallpath . '\\cert\\eurysco_executor.csr');
									}
								}
								if (is_null($executorservicename_last) || $executorservicename_last == '') { $executorservicename_last = 'eurysco_NULL_'; }
								$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<executorservicedisplayname>' . $executorservicedisplayname_xml . '</executorservicedisplayname>' . "\n" . '		<executorservicename>' . $executorservicename_xml . '</executorservicename>' . "\n" . '		<executorservicestartuptype>' . $executorservicestartuptype_xml . '</executorservicestartuptype>' . "\n" . '		<executorservicelogonas>' . $executorservicelogonas_xml . '</executorservicelogonas>' . "\n" . '		<executorlisteningport>' . $executorlisteningport_xml . '</executorlisteningport>' . "\n" . '		<executorphpport>' . $executorphpport_xml . '</executorphpport>' . "\n" . '	</settings>' . "\n" . '</config>';
								$sxe = new SimpleXMLElement($xml);
								$sxe->asXML($config_executorsrv);
								session_write_close();
								exec('set sslprotocol=TLSv1.2 & "' . $euryscoinstallpath . '\\euryscosrv.bat" "' . $executorservicename_last . '" "' . $executorservicename_xml . '" "' . $executorservicestartuptype_xml . '" "' . $executorservicelogonas_xml . '" "' . $executorservicedisplayname_xml . '" "' . $executorlisteningport_xml . '" "' . $executorphpport_xml . '" "eurysco_executor" "core"', $errorarray, $errorlevel);
								session_start();
								if ($errorlevel == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     executor config     eurysco executor configuration edited';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     executor config     eurysco executor configuration not edited';
								}
							}
							header('location: ' . $_SERVER['PHP_SELF']);
							exit;
						}
						
						?>

                        <?php $span = '6'; ?>
						<?php include($euryscoinstallpath . '\\include\\executor_status_' . $executorstatus . '.php'); ?>
                        <p>&nbsp;</p>
                    	<form method="post">
						<div class="input-control text">
                        	<h3>Executor Service Display Name:</h3>
							<input type="text" id="executorservicedisplayname" name="executorservicedisplayname" placeholder="<?php echo $executorservicedisplayname_ph; ?>" value="<?php echo $executorservicedisplayname; ?>" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Executor Service Name:</h3>
							<input type="text" id="executorservicename" name="executorservicename" placeholder="<?php echo $executorservicename_ph; ?>" value="<?php echo $executorservicename; ?>" disabled="disabled" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control select">
                        	<h3>Executor Service Startup Type:</h3>
							<select id="executorservicestartuptype" name="executorservicestartuptype">
								<option value="auto" <?php if ($executorservicestartuptype == 'auto') { echo 'selected'; } ?>>Automatic</option>
								<option value="demand" <?php if ($executorservicestartuptype == 'demand') { echo 'selected'; } ?>>Manual</option>
								<option value="disabled" <?php if ($executorservicestartuptype == 'disabled') { echo 'selected'; } ?>>Disabled</option>
							</select>
						</div>
						<div class="input-control text">
                        	<h3>Executor Service Log On As:</h3>
							<input type="text" id="executorservicelogonas" name="executorservicelogonas" placeholder="<?php echo $executorservicelogonas_ph; ?>" value="<?php echo $executorservicelogonas; ?>" disabled="disabled" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Executor PHP Local Port:</h3>
							<input type="text" id="executorphpport" name="executorphpport" placeholder="<?php echo $executorphpport_ph; ?>" value="<?php echo $executorphpport; ?>" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Executor Listening SSL Port:</h3>
							<input type="text" id="executorlisteningport" name="executorlisteningport" placeholder="<?php echo $executorlisteningport_ph; ?>" value="<?php echo $executorlisteningport; ?>" />
							<button class="btn-clear"></button>
							<textarea wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:100px; font-weight:normal;" disabled="disabled"><?php
							
							if (file_exists($euryscoinstallpath . '\\cert\\eurysco_executor.csr')) {
								echo 'eurysco Executor SSL Self-Signed Certificate Information' . "\n\n";
							} else {
								echo 'eurysco Executor SSL Trusted Certificate Information' . "\n\n";
							}
							
							if (file_exists($euryscoinstallpath . '\\cert\\eurysco_executor.crt')) {
								
								$data = openssl_x509_parse(file_get_contents($euryscoinstallpath . '\\cert\\eurysco_executor.crt'));

								$validFrom = date('d/m/Y H:i:s', $data['validFrom_time_t']);
								$validTo = date('d/m/Y H:i:s', $data['validTo_time_t']);
								
								$fp = fopen($euryscoinstallpath . '\\cert\\eurysco_executor.crt', 'r'); 
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
						<?php $name = $euryscoinstallpath . '\\cert\\eurysco_executor.csr'; if (file_exists($name) && is_readable($name)) { ?>
						<div class="input-control text">
                        	<h3>Executor SSL Certificate Request:</h3>
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
                        	<h3>Executor SSL Import Certificate:</h3>
							<textarea id="executortrustedcertificate" name="executortrustedcertificate" wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:10px; height:100px; font-weight:normal;">-----BEGIN CERTIFICATE-----

							
							
eurysco Executor SSL Trusted Certificate ( Base 64 Encoded )



-----END CERTIFICATE-----</textarea>
						</div>
						<?php } else { ?>
						<div class="input-control text">
                        	<h3>Executor SSL Trusted Certificate:</h3>
							<textarea wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:10px; height:100px; font-weight:normal;" disabled="disabled"><?php
								$name = $euryscoinstallpath . '\\cert\\eurysco_executor.crt';
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
						<?php if ($executorstatus != 'cfg') { ?>
						<br />
							<input type="checkbox" id="deleteconfiguration" name="deleteconfiguration" />
                            <span class="helper">&nbsp;Delete Executor Configuration</span>
                        <?php } ?>
                        <p>&nbsp;</p>
	                        <input type="hidden" id="submitform" name="submitform" value="" />
	                        <input type="submit" id="editcreateservice" name="editcreateservice" value="<?php if ($executorstatus != 'cfg') { echo 'Edit Executor'; } else { echo 'Create Executor'; } ?>" style="background-color:#0072C6;" />
                            <?php if ($executorstatus == 'run') { echo '<input type="submit" id="stopservice" name="stopservice" class="bg-color-red" value="Stop Executor Service"/>'; } ?>
                            <?php if ($executorstatus == 'nrn') { echo '<input type="submit" id="startservice" name="startservice" class="bg-color-green" value="Start Executor Service"/>'; } ?>
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