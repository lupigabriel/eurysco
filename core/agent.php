<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['agentconfig'] > 0) {  } else { header('location: /'); exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Agent<small>config</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-agent-button big page-back"></a>
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
						if(isset($_POST['agentservicedisplayname']) && $_POST['agentservicedisplayname'] != '') {
							$agentservicedisplayname = $_POST['agentservicedisplayname'];
							$agentservicedisplayname_ph = '';
							$agentservicedisplayname_xml = $agentservicedisplayname;
						} else {
							if (file_exists($config_agentsrv)) {
								$agentservicedisplayname = $xmlagent->settings->agentservicedisplayname;
								$agentservicedisplayname_ph = '';
								$agentservicedisplayname_xml = $agentservicedisplayname;
							} else {
								$agentservicedisplayname = '';
								$agentservicedisplayname_ph = 'eurysco Agent';
								$agentservicedisplayname_xml = $agentservicedisplayname_ph;
							}
						}

						if(isset($_POST['agentservicename']) && $_POST['agentservicename'] != '') {
							$agentservicename = $_POST['agentservicename'];
							$agentservicename_ph = '';
							$agentservicename_xml = $agentservicename;
						} else {
							if (file_exists($config_agentsrv)) {
								$agentservicename = $xmlagent->settings->agentservicename;
								$agentservicename_ph = '';
								$agentservicename_xml = $agentservicename;
							} else {
								$agentservicename = '';
								$agentservicename_ph = $eurysco_agentsrv;
								$agentservicename_xml = $agentservicename_ph;
							}
						}
						
						if(isset($_POST['agentservicestartuptype']) && $_POST['agentservicestartuptype'] != '') {
							$agentservicestartuptype = $_POST['agentservicestartuptype'];
							$agentservicestartuptype_ph = '';
							$agentservicestartuptype_xml = $agentservicestartuptype;
						} else {
							if (file_exists($config_agentsrv)) {
								$agentservicestartuptype = $xmlagent->settings->agentservicestartuptype;
								$agentservicestartuptype_ph = '';
								$agentservicestartuptype_xml = $agentservicestartuptype;
							} else {
								$agentservicestartuptype = '';
								$agentservicestartuptype_ph = 'auto';
								$agentservicestartuptype_xml = $agentservicestartuptype_ph;
							}
						}
						
						if(isset($_POST['agentservicelogonas']) && $_POST['agentservicelogonas'] != '') {
							$agentservicelogonas = $_POST['agentservicelogonas'];
							$agentservicelogonas_ph = '';
							$agentservicelogonas_xml = $agentservicelogonas;
						} else {
							if (file_exists($config_agentsrv)) {
								$agentservicelogonas = $xmlagent->settings->agentservicelogonas;
								$agentservicelogonas_ph = '';
								$agentservicelogonas_xml = $agentservicelogonas;
							} else {
								$agentservicelogonas = '';
								$agentservicelogonas_ph = 'LocalSystem';
								$agentservicelogonas_xml = $agentservicelogonas_ph;
							}
						}
						
						if(isset($_POST['serverconnectionaddress']) && $_POST['serverconnectionaddress'] != '') {
							$serverconnectionaddress = 'https://' . str_replace('https://', '', str_replace('http://', '', $_POST['serverconnectionaddress']));
							$serverconnectionaddress_ph = '';
							$serverconnectionaddress_xml = $serverconnectionaddress;
						} else {
							if (file_exists($config_agentsrv)) {
								$serverconnectionaddress = $xmlagent->settings->serverconnectionaddress;
								$serverconnectionaddress_ph = '';
								$serverconnectionaddress_xml = $serverconnectionaddress;
							} else {
								$serverconnectionaddress = '';
								if ($serverstatus == 'run') { $serverconnectionaddress_ph = 'https://' . strtoupper($envcomputername); } else { $serverconnectionaddress_ph = 'https://euryscoServer'; }
								$serverconnectionaddress_xml = $serverconnectionaddress_ph;
							}
						}
						
						if(isset($_POST['serverconnectionport']) && $_POST['serverconnectionport'] != '') {
							$serverconnectionport = $_POST['serverconnectionport'];
							$serverconnectionport_ph = '';
							$serverconnectionport_xml = $serverconnectionport;
						} else {
							if (file_exists($config_agentsrv)) {
								$serverconnectionport = $xmlagent->settings->serverconnectionport;
								$serverconnectionport_ph = '';
								$serverconnectionport_xml = $serverconnectionport;
							} else {
								$serverconnectionport = '';
								if ($eurysco_serverport != 0) { $serverconnectionport_ph = $eurysco_serverport; } else { $serverconnectionport_ph = '59982'; }
								$serverconnectionport_xml = $serverconnectionport_ph;
							}
						}
						
						if(isset($_POST['sslverifyhost'])) {
							$sslverifyhost_xml = 'true';
						} else {
							if (file_exists($config_agentsrv) && !isset($_POST['submitform'])) {
								$sslverifyhost_xml = $xmlagent->settings->sslverifyhost;
							} else {
								$sslverifyhost_xml = 'false';
							}
						}
						
						if(isset($_POST['sslverifypeer'])) {
							$sslverifypeer_xml = 'true';
						} else {
							if (file_exists($config_agentsrv) && !isset($_POST['submitform'])) {
								$sslverifypeer_xml = $xmlagent->settings->sslverifypeer;
							} else {
								$sslverifypeer_xml = 'false';
							}
						}
						
						if(isset($_POST['serverconnectionpassword'])) {
							$serverconnectionpassword = $_POST['serverconnectionpassword'];
							$serverconnectionpassword_ph = '&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;';
						} else {
							if (file_exists($config_agentsrv)) {
								$serverconnectionpassword = '';
								$serverconnectionpassword_ph = '&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;';
							} else {
								$serverconnectionpassword = '';
								$serverconnectionpassword_ph = 'Default Strong Password';
							}
						}
						
						if (isset($_POST['submitform']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
							$agentservicename_last = '';
							if (isset($xmlagent) == 1) { $agentservicename_last = $xmlagent->settings->agentservicename; }
							if (isset($_POST['startservice'])) {
								session_write_close();
								$agentstatusfile = $euryscoinstallpath . '\\agent\\temp\\agent.status';
								if (file_exists($agentstatusfile)) @unlink($agentstatusfile);
								exec('net.exe start ' . $agentservicename_last, $errorarray, $errorlevel);
								session_start();
								if ($errorlevel == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     agent config     eurysco agent started';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     agent config     eurysco agent not started';
								}
							}
							if (isset($_POST['stopservice'])) {
								session_write_close();
								exec('taskkill.exe /f /im "php_eurysco_agent.exe" /im "eurysco.agent.status.check.exe" /im "eurysco.agent.exec.timeout.exe" /t', $errorarray, $errorlevel);
								exec('net.exe stop ' . $agentservicename_last);
								session_start();
								if ($errorlevel == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     agent config     eurysco agent stopped';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     agent config     eurysco agent not stopped';
								}
								$agentstatusfile = $euryscoinstallpath . '\\agent\\temp\\agent.status';
								if (file_exists($agentstatusfile)) @unlink($agentstatusfile);
							}
							if (isset($_POST['deleteconfiguration']) && !isset($_POST['stopservice']) && !isset($_POST['startservice'])) {
								session_write_close();
								exec('taskkill.exe /f /im "php_eurysco_agent.exe" /im "eurysco.agent.status.check.exe" /im "eurysco.agent.exec.timeout.exe" /t', $errorarray, $errorlevel);
								exec('net.exe stop ' . $agentservicename_last, $errorarray, $errorlevel);
								if ($errorlevel == 0 || $errorlevel == 2) { exec('sc.exe delete ' . $agentservicename_last, $errorarray, $errorlevel); }
								if ($errorlevel == 0) { @unlink($config_agentsrv); }
								session_start();
								if ($errorlevel == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     agent config     eurysco agent configuration deleted';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     agent config     eurysco agent configuration not deleted';
								}
								$agentstatusfile = $euryscoinstallpath . '\\agent\\temp\\agent.status';
								if (file_exists($agentstatusfile)) @unlink($agentstatusfile);
								$agentkey = $euryscoinstallpath . '\\conf\\agent.key';
								if (file_exists($agentkey)) @unlink($agentkey);
							}
							if (isset($_POST['editcreateservice']) && !isset($_POST['deleteconfiguration'])) {
								if (is_null($agentservicename_last) || $agentservicename_last == '') { $agentservicename_last = 'eurysco_NULL_'; }
								$mcrykey = pack('H*', hash('sha256', hash('sha512', 'vNqgi_R1QX%C;z-724p4lFHm*?7c!e2%vG9tp+-*@#%=?!_;./' . hash('tiger128,4', $serverconnectionport_xml) . '-*@#%=?!_;./-f;bTh2XXqW%Zs%88+/-7pVb;X')));
								$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<agentservicedisplayname>' . $agentservicedisplayname_xml . '</agentservicedisplayname>' . "\n" . '		<agentservicename>' . $agentservicename_xml . '</agentservicename>' . "\n" . '		<agentservicestartuptype>' . $agentservicestartuptype_xml . '</agentservicestartuptype>' . "\n" . '		<agentservicelogonas>' . $agentservicelogonas_xml . '</agentservicelogonas>' . "\n" . '		<serverconnectionaddress>' . $serverconnectionaddress_xml . '</serverconnectionaddress>' . "\n" . '		<serverconnectionport>' . $serverconnectionport_xml . '</serverconnectionport>' . "\n" . '		<sslverifyhost>' . $sslverifyhost_xml . '</sslverifyhost>' . "\n" . '		<sslverifypeer>' . $sslverifypeer_xml . '</sslverifypeer>' . "\n" . '		<serverconnectionpassword>' . base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykey, $serverconnectionpassword . 'OLb324MJ0E7n/uAg+-*@#%=?!_;./' . hash('haval128,5', $serverconnectionport_xml) . '?!_;./Hdgl3LpPwUlzC*J8%D-ZadL', MCRYPT_MODE_CBC, $iv)) . '</serverconnectionpassword>' . "\n" . '	</settings>' . "\n" . '</config>';
								$writexml = fopen($config_agentsrv, 'w');
								fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
								fclose($writexml);
								session_write_close();
								exec('set sslprotocol=TLSv1.2 & "' . $euryscoinstallpath . '\\euryscosrv.bat" "' . $agentservicename_last . '" "' . $agentservicename_xml . '" "' . $agentservicestartuptype_xml . '" "' . $agentservicelogonas_xml . '" "' . $agentservicedisplayname_xml . '" "' . $serverconnectionport_xml . '" "0" "eurysco_agent" "agent"', $errorarray, $errorlevel);
								session_start();
								if ($errorlevel == 0) {
			                    	$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     agent config     eurysco agent configuration edited';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     agent config     eurysco agent configuration not edited';
								}
								$agentstatusfile = $euryscoinstallpath . '\\agent\\temp\\agent.status';
								if (file_exists($agentstatusfile)) @unlink($agentstatusfile);
								$agentkey = $euryscoinstallpath . '\\conf\\agent.key';
								if (file_exists($agentkey)) @unlink($agentkey);
							}
							header('location: ' . $_SERVER['PHP_SELF']);
							exit;
						}
						
						?>

                        <?php $span = '6'; ?>
						<?php include($euryscoinstallpath . '\\include\\agent_status_' . $agentstatus . '.php'); ?>
                        <p>&nbsp;</p>
                    	<form method="post">
						<div class="input-control text">
                        	<h3>Agent Service Display Name:</h3>
							<input type="text" id="agentservicedisplayname" name="agentservicedisplayname" placeholder="<?php echo $agentservicedisplayname_ph; ?>" value="<?php echo $agentservicedisplayname; ?>" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Agent Service Name:</h3>
							<input type="text" id="agentservicename" name="agentservicename" placeholder="<?php echo $agentservicename_ph; ?>" value="<?php echo $agentservicename; ?>" disabled="disabled" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control select">
                        	<h3>Agent Service Startup Type:</h3>
							<select id="agentservicestartuptype" name="agentservicestartuptype">
								<option value="auto" <?php if ($agentservicestartuptype == 'auto') { echo 'selected'; } ?>>Automatic</option>
								<option value="demand" <?php if ($agentservicestartuptype == 'demand') { echo 'selected'; } ?>>Manual</option>
								<option value="disabled" <?php if ($agentservicestartuptype == 'disabled') { echo 'selected'; } ?>>Disabled</option>
							</select>
						</div>
						<div class="input-control text">
                        	<h3>Agent Service Log On As:</h3>
							<input type="text" id="agentservicelogonas" name="agentservicelogonas" placeholder="<?php echo $agentservicelogonas_ph; ?>" value="<?php echo $agentservicelogonas; ?>" disabled="disabled" />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Server Connection Address:</h3>
							<input type="text" id="serverconnectionaddress" name="serverconnectionaddress" placeholder="<?php echo $serverconnectionaddress_ph; ?>" value="<?php echo $serverconnectionaddress; ?>" <?php if ($serverstatus == 'run') { echo 'disabled="disabled"'; } ?> />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Server Connection Port:</h3>
							<input type="text" id="serverconnectionport" name="serverconnectionport" placeholder="<?php echo $serverconnectionport_ph; ?>" value="<?php echo $serverconnectionport; ?>" <?php if ($serverstatus == 'run') { echo 'disabled="disabled"'; } ?> />
							<button class="btn-clear"></button>
						</div>
						<div class="input-control text">
                        	<h3>Server Connection Password:</h3>
							<?php if ($changepwdlocalsetting == 'Enable' && $_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1' && $_SERVER['HTTP_X_FORWARDED_FOR'] != '::1') { ?>
							<input type="hidden" id="serverconnectionpassword" name="serverconnectionpassword" value="<?php echo $serverconnectionpassword; ?>" />
							<input type="text" value="change password is allowed only for local connection" maxlength="30" disabled="disabled" />
							<button class="btn-clear"></button>
							<?php } else { ?>
							<input type="password" autocomplete="off" id="serverconnectionpassword" name="serverconnectionpassword" placeholder="<?php echo $serverconnectionpassword_ph; ?>" value="<?php echo $serverconnectionpassword; ?>" maxlength="30" />
							<button class="btn-clear"></button>
							<?php } ?>
						</div>
						<div class="input-control text">
                        	<h3>Server Connection Result:</h3>
							<input id="serverconnectionresult" name="serverconnectionresult" type="text" value="" style="" readonly="readonly" />
						</div>
							<input type="checkbox" id="sslverifyhost" name="sslverifyhost" <?php if ($sslverifyhost_xml == 'true' || !file_exists($config_agentsrv)) { echo 'checked'; } ?> />
                            <span class="helper">&nbsp;SSL Verify Host</span>
						<br />
							<input type="checkbox" id="sslverifypeer" name="sslverifypeer" <?php if ($sslverifypeer_xml == 'true' || !file_exists($config_agentsrv)) { echo 'checked'; } ?> />
                            <span class="helper">&nbsp;SSL Verify Peer</span>
						<?php if ($agentstatus != 'cfg') { ?>
						<br />
						<br />
							<input type="checkbox" id="deleteconfiguration" name="deleteconfiguration" />
                            <span class="helper">&nbsp;Delete Agent Configuration</span>
                        <?php } ?>
                        <p>&nbsp;</p>
	                        <input type="hidden" id="submitform" name="submitform" value="" />
	                        <input type="submit" id="editcreateservice" name="editcreateservice" value="<?php if ($agentstatus != 'cfg') { echo 'Edit Agent'; } else { echo 'Create Agent'; } ?>" style="background-color:#0072C6;" />
                            <?php if ($agentstatus == 'run' || $agentstatus == 'con') { echo '<input type="submit" id="stopservice" name="stopservice" class="bg-color-red" value="Stop Agent Service"/>'; } ?>
                            <?php if ($agentstatus == 'nrn') { echo '<input type="submit" id="startservice" name="startservice" class="bg-color-green" value="Start Agent Service"/>'; } ?>
							<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
						</form>
					<script language="javascript" type="text/javascript">
					update();
					setInterval(update, 3000);
					function update() {
						$.ajax({
							type: "GET",
							url: 'agentjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>',
							data: '',
							dataType: 'json',
							cache: false,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#serverconnectionresult').attr('value', data.connexitcode);
							$('#serverconnectionresult').attr('style', data.agentconstatusstyle);
							}
						});
					}
					</script>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>