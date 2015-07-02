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
						
						if (isset($_POST['submitform']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
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
									@unlink($_SERVER['DOCUMENT_ROOT'] . '\\' . $config_coresrv);
									@unlink(str_replace('\\core', '\\cert\\eurysco_core.crt', $_SERVER['DOCUMENT_ROOT']));
									@unlink(str_replace('\\core', '\\cert\\eurysco_core.key', $_SERVER['DOCUMENT_ROOT']));
								}
								session_start();
								if ($errorlevel == 0 && $errorlevelssl == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core configuration deleted';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core configuration not deleted';
								}
							}
							if (isset($_POST['editcreateservice']) && !isset($_POST['deleteconfiguration'])) {
								if (is_null($coreservicename_last) || $coreservicename_last == '') { $coreservicename_last = 'eurysco_NULL_'; }
								$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<coreservicedisplayname>' . $coreservicedisplayname_xml . '</coreservicedisplayname>' . "\n" . '		<coreservicename>' . $coreservicename_xml . '</coreservicename>' . "\n" . '		<coreservicestartuptype>' . $coreservicestartuptype_xml . '</coreservicestartuptype>' . "\n" . '		<coreservicelogonas>' . $coreservicelogonas_xml . '</coreservicelogonas>' . "\n" . '		<corelisteningport>' . $corelisteningport_xml . '</corelisteningport>' . "\n" . '		<corephpport>' . $corephpport_xml . '</corephpport>' . "\n" . '	</settings>' . "\n" . '</config>';
								$sxe = new SimpleXMLElement($xml);
								$sxe->asXML($config_coresrv);
								session_write_close();
								exec('set sslprotocol=TLSv1.2 & "' . str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\euryscosrv.bat" "' . $coreservicename_last . '" "' . $coreservicename_xml . '" "' . $coreservicestartuptype_xml . '" "' . $coreservicelogonas_xml . '" "' . $coreservicedisplayname_xml . '" "' . $corelisteningport_xml . '" "' . $corephpport_xml . '" "eurysco_core" "core"', $errorarray, $errorlevel);
								$fp = fopen(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\chromium\\euryscoLogin.prt', 'w');
								fwrite($fp, $corelisteningport_xml);
								fclose($fp);
								copy($config_coresrv, str_replace('\\core', '\\agent', $_SERVER['DOCUMENT_ROOT']) . '\\' . $config_coresrv);
								copy($config_coresrv, str_replace('\\core', '\\server', $_SERVER['DOCUMENT_ROOT']) . '\\' . $config_coresrv);
								session_start();
								if ($errorlevel == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core configuration edited';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     core config     eurysco core configuration not edited';
								}
							}
							header('location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
						}
						
						?>

                        <?php $span = '6'; ?>
						<?php include('/include/core_status_' . $corestatus . '.php'); ?>
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
							
							echo 'eurysco Core SSL Certificate Information' . "\n\n";
							
							if (file_exists(str_replace('\\core', '\\cert\\eurysco_core.crt', $_SERVER['DOCUMENT_ROOT']))) {
								
								$data = openssl_x509_parse(file_get_contents(str_replace('\\core', '\\cert\\eurysco_core.crt', $_SERVER['DOCUMENT_ROOT'])));

								$validFrom = date('d/m/Y H:i:s', $data['validFrom_time_t']);
								$validTo = date('d/m/Y H:i:s', $data['validTo_time_t']);
								
								$fp = fopen(str_replace('\\core', '\\cert\\eurysco_core.crt', $_SERVER['DOCUMENT_ROOT']), 'r'); 
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
                        </form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>