<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['commandline'] > 0) {  } else { header('location: /'); exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Command<small>line</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-cli-button big page-back"></a>
		</div>
	</div>
</div>

<br />
<?php $random = rand() . md5(session_id()); ?>
<div class="page" id="page-index">
	<div class="page-region">
		<div class="page-region-content">
			<div class="grid">
				<div class="row">
		            <div class="span1"></div>
		            <div class="span10">

                    	<div class="input-control textarea">
							<?php
							
							if (isset($_POST['openexppath']) && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
								if (isset($_SESSION['permpathcli'])) {
									$_SESSION['permpath'] = $_SESSION['permpathcli'];
								}
								header('location: /explorer.php?path=' . $_POST['openexppath']);
								exit;
							}

							if (!isset($_SESSION['mapsharesetting'])) {
								if ($mapsharesetting != '') {
									$_SESSION['mapsharesetting'] = exec('net.exe use ' . preg_replace('/[\n\r]/', ' & net.exe use ', $mapsharesetting), $errorarray, $errorlevel);
								} else {
									$_SESSION['mapsharesetting'] = 'null';
								}
							}
							
							if (isset($_POST['resetviewconf']) || isset($_GET['resetviewconf'])) {
								unset($_SESSION['cldrive']);
								unset($_SESSION['cldrive_old']);
								unset($_SESSION['clpath']);
								unset($_SESSION['outputh']);
								unset($_SESSION['cmdmem']);
								unset($_POST['resetviewconf']);
							}
							
							if (isset($_POST['cltimeout'])) {
								$_SESSION['cltimeout'] = $_POST['cltimeout'];
							} else {
								if (!isset($_SESSION['cltimeout'])) {
									$_SESSION['cltimeout'] = '30000';
								}
							}

							if (isset($_POST['permpath'])) {
								$_SESSION['permpathcli'] = $_POST['permpath'];
							}

							if (!isset($_SESSION['clpath']) && $_SESSION['usersett']['filebrowserf'] != '') {
								$_SESSION['clpath'] = '';
							}

							if (isset($_SESSION['permpathcli'])) {
								if (!strpos('|' . trim(strtolower($_SESSION['clpath'])) . '\\', strtolower($_SESSION['permpathcli']))) {
									$_SESSION['cldrive'] = strtoupper(substr($_SESSION['permpathcli'], 0, 2));
									$_SESSION['cldrive_old'] = strtoupper(substr($_SESSION['permpathcli'], 0, 2));
									$_SESSION['clpath'] = str_replace(substr($_SESSION['permpathcli'], 0, 2), strtoupper(substr($_SESSION['permpathcli'], 0, 2)), $_SESSION['permpathcli']);
								}
							}

							$filebrowserblcount = 0;
							$filebrowserbllist = '';
							$filebrowserblfd = 'C:';
							if ($_SESSION['usersett']['filebrowserf'] != '') {
								$filebrowserblarray = array();
								$filebrowserblarray = (explode(',', preg_replace('/\r\n|\r|\n/', ',', $_SESSION['usersett']['filebrowserf'])));
								foreach ($filebrowserblarray as $filebrowserbl) {
									$filebrowserbl = rtrim(str_replace(substr($filebrowserbl, 0, 2), strtoupper(substr($filebrowserbl, 0, 2)), $filebrowserbl), '\\') . '\\';
									if (is_dir($filebrowserbl) && !is_file($filebrowserbl)) {
										if (!isset($_SESSION['permpathcli']) && $filebrowserblcount == 0) { $filebrowserblfd = strtoupper(substr($filebrowserbl, 0, 2)); $_SESSION['permpathcli'] = $filebrowserbl; $_SESSION['clpath'] = $_SESSION['permpathcli']; }
										if (strpos('|' . trim(strtolower($_SESSION['clpath']), '\\'), trim(strtolower($filebrowserbl), '\\')) > 0) { $filebrowserblsel = 'selected'; } else { $filebrowserblsel = ''; }
										$filebrowserbllist = $filebrowserbllist . '<option value="' . $filebrowserbl . '" ' . $filebrowserblsel . '>' . rtrim($filebrowserbl, '\\') . '&nbsp;&nbsp;&nbsp;</option>';
										$filebrowserblcount = $filebrowserblcount + 1;
									}
								}
							}
							
							if ($_SESSION['usersett']['filebrowserf'] != '') {
								if (!isset($_SESSION['clpaths'])) {
									$_SESSION['clpaths'] = 1;
								}
								if ($_SESSION['clpaths'] == 1) {
									$localcommandblacklist = trim('.*\\\,.:,.* .:,.*\.\.,' . $localcommandblacklist, ',');
								} else {
									$localcommandblacklist = trim('.*\\\,.:,.* .:,' . $localcommandblacklist, ',');
								}
								if ($filebrowserbllist == '') {
									$_SESSION['cldrive'] = $filebrowserblfd;
									$_SESSION['cldrive_old'] = $filebrowserblfd;
									$_SESSION['clpath'] = '\\';
								}
							}
							
							$commandblcheck = 0;
							if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['commandline'] > 0) {
								if (isset($_POST['cmd']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
									if ($_POST['cmd'] != '') {
										if ($localcommandblacklist != '') {
											$commandblarray = array();
											$commandblarray = (explode(',', $localcommandblacklist));
											$commandcmdrray = array();
											$commandcmdrray = (explode('&', $_POST['cmd']));
											foreach ($commandcmdrray as $commandcm) {
												if ($commandblcheck == 0) {
													foreach ($commandblarray as $commandbl) {
														if ($commandblcheck == 0) {
															$commandblcheck = 1;
															if (!preg_match('/^' . $commandbl . '/', trim(preg_replace('/\s+/', ' ', str_replace('.com', '', str_replace('.exe', '', $commandcm))), ' '))) {
																$commandblcheck = 0;
															}
														}
													}
												}
											}
										}
										if ($_SESSION['usertype'] != 'Administrators' && $_SESSION['usersett']['commandlinef'] != '') {
											$commandblarray = array();
											$commandblarray = (explode(',', $_SESSION['usersett']['commandlinef']));
											$commandcmdrray = array();
											$commandcmdrray = (explode('&', $_POST['cmd']));
											foreach ($commandcmdrray as $commandcm) {
												if ($commandblcheck == 0) {
													$commandblcheck = 2;
													foreach ($commandblarray as $commandbl) {
														if (preg_match('/^' . $commandbl . '/', trim(preg_replace('/\s+/', ' ', str_replace('.com', '', str_replace('.exe', '', $commandcm))), ' '))) {
															$commandblcheck = 0;
														}
													}
												}
											}
										}
										if ($commandblcheck == 0) {
											$cmd = trim($_POST['cmd'], ' ');
											$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     command line     ' . $cmd;
										}
										if ($commandblcheck == 1) {
											$cmd = '';
											echo '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">command <strong>' . trim($_POST['cmd'], ' ') . '</strong> is not permitted</blockquote><br />';
										}
										if ($commandblcheck == 2) {
											$cmd = '';
											echo '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">your account is not allowed to run <strong>' . trim($_POST['cmd'], ' ') . '</strong></blockquote><br />';
										}
									} else {
										$cmd = '';
									}
								} else {
									if (isset($_SESSION['cmdmem'])) {
										$cmd = '';
									} else {
										$cmd = 'dir /o:gn /a';
									}

									$fp = fopen($euryscoinstallpath . '\\temp\\core\\' . $random . '.out', 'w');
									fwrite($fp, '');
									fclose($fp);
								}
							}
	
							if (isset($_SESSION['cmdmem'])) {
								$cmdmem = $_SESSION['cmdmem'];
							} else {
								$cmdmem = '';
							}
							
							if (isset($_POST['cldrive'])) {
								$cldrive = $_POST['cldrive'];
							} else {
								if (isset($_SESSION['cldrive'])) {
									$cldrive = $_SESSION['cldrive'];
								} else {
									$cldrive = $filebrowserblfd;
								}
							}
							$_SESSION['cldrive'] = $cldrive;

							if (isset($_SESSION['cldrive_old'])) {
								$cldrive_old = $_SESSION['cldrive_old'];
							} else {
								$cldrive_old = $filebrowserblfd;
								$_SESSION['cldrive_old'] = $cldrive_old;
							}

							if (isset($_SESSION['outputh'])) {
								$outputh = $_SESSION['outputh'];
							} else {
								$outputh = '';
							}

							if (isset($_SESSION['clpath'])) {
								$clpath = $_SESSION['clpath'];
							} else {
								$clpath = '\\';
							}
							
							if (strtolower($cmd) == strtolower($filebrowserblfd) || strtolower($cldrive) != strtolower($cldrive_old)) {
								$frrt = ' & cd \\';
							} else {
								$frrt = '';
							}

							if (strtolower($cldrive) != strtolower($cldrive_old)) {
								$cmdmem = "<option value='" . $cldrive . "'>" . str_replace(" ", "&nbsp;", $cldrive) . "</option>" . $cmdmem . "\n";
							} else {
								if ($cmd != '') { $cmdmem = "<option value='" . $cmd . "'>" . str_replace(" ", "&nbsp;", $cmd) . "</option>" . $cmdmem . "\n"; }
							}


							$fp = fopen($euryscoinstallpath . '\\temp\\core\\' . $random . '.cmd', 'w');
							fwrite($fp, $cmd);
							fclose($fp);
							
							if (strlen(strtolower($cmd)) == 2 && substr(strtolower($cmd), -1) == ':') {
								$cldrive = strtolower($cmd);
								$clpath = '\\';
							}
							session_write_close();
							pclose(popen('start "eurysco core exec timeout" /b "' . $euryscoinstallpath . '\\ext\\eurysco.executor.exec.timeout.exe" ' . $_SESSION['cltimeout'] . ' >nul 2>nul', 'r'));
							$clioutput = shell_exec('cd\\ & ' . $cldrive . ' & cd\\ & cd "' . $clpath . '" & "' . $euryscoinstallpath . '\\temp\\core\\' . $random . '.cmd">>"' . $euryscoinstallpath . '\\temp\\core\\' . $random . '.out"' . $frrt . ' & dir /b /o:n /a:d>"' . $euryscoinstallpath . '\\temp\\core\\' . $random . '.dir" & dir /b /o:n /a:-d>"' . $euryscoinstallpath . '\\temp\\core\\' . $random . '.files" & cd>"' . $euryscoinstallpath . '\\temp\\core\\' . $random . '.path" & echo. & cd');
							$corecmdto = exec('taskkill.exe /f /im "eurysco.executor.exec.timeout.exe" /t >nul 2>nul', $errorarrayto, $errorlevelto);
							session_start();
							
							if (substr(trim($clioutput), 1, 1) == ':') {
								$cldrive = substr(trim($clioutput), 0, 2);
								$_SESSION['cldrive'] = $cldrive;
							}
							
							if (isset($_SESSION['permpathcli'])) {
								if (!strpos('|' . trim(strtolower($clioutput)) . '\\', strtolower($_SESSION['permpathcli']))) {
									$clioutput = str_replace(substr($_SESSION['permpathcli'], 0, 2), strtoupper(substr($_SESSION['permpathcli'], 0, 2)), $_SESSION['permpathcli']);
								}
							}
							$clioutput = str_replace(substr($clioutput, 0, 2), strtoupper(substr($clioutput, 0, 2)), $clioutput);

							if (!file_exists($euryscoinstallpath . '\\temp\\core\\' . $random . '.cmd') || !file_exists($euryscoinstallpath . '\\temp\\core\\' . $random . '.out') || !file_exists($euryscoinstallpath . '\\temp\\core\\' . $random . '.dir') || !file_exists($euryscoinstallpath . '\\temp\\core\\' . $random . '.files') || !file_exists($euryscoinstallpath . '\\temp\\core\\' . $random . '.path')) {
								header('location: ' . $_SERVER['PHP_SELF'] . '?resetviewconf');
							}
							
							if (file_exists($euryscoinstallpath . '\\temp\\core\\' . $random . '.path')) {
								$fp = fopen($euryscoinstallpath . '\\temp\\core\\' . $random . '.path', 'r');
								$clpath = preg_replace('/\r\n|\r|\n/','',fgets($fp));
								fclose($fp);
							} else {
								unset($_SESSION['cldrive']);
								unset($_SESSION['cldrive_old']);
								unset($_SESSION['clpath']);
								unset($_SESSION['cltimeout']);
								unset($_SESSION['outputh']);
								unset($_SESSION['cmdmem']);
								unset($_SESSION['clpaths']);
								header('location: ' . $_SERVER["SCRIPT_NAME"]);
								exit;
							}
							$clpath = str_replace(substr($clpath, 0, 2), strtoupper(substr($clpath, 0, 2)), $clpath);
							
							$data = array_slice(file($euryscoinstallpath . '\\temp\\core\\' . $random . '.out'), -900);
							foreach ($data as $line) {
								$outputh = $outputh . $line;
							}
							@unlink($euryscoinstallpath . '\\temp\\core\\' . $random . '.out');
							$fp = fopen($euryscoinstallpath . '\\temp\\core\\' . $random . '.out', 'w');
							fwrite($fp, $outputh);
							fclose($fp);
							$outputh = '';
							$data = array_slice(file($euryscoinstallpath . '\\temp\\core\\' . $random . '.out'), -900);
							foreach ($data as $line) {
								$outputh = $outputh . $line;
							}
							
							$_SESSION['cldrive_old'] = $cldrive;
							$_SESSION['clpath'] = $clpath;
							$_SESSION['outputh'] = $outputh;

							if ($_SESSION['usersett']['filebrowserf'] != '') {
								if (trim(strtolower($_SESSION['clpath']), '\\') == trim(strtolower($_SESSION['permpathcli']), '\\')) {
									$_SESSION['clpaths'] = 1;
								} else {
									$_SESSION['clpaths'] = 0;
								}
							}

							if (isset($_SESSION['permpathcli'])) {
								if (!strpos('|' . trim(strtolower($_SESSION['clpath'])) . '\\', strtolower($_SESSION['permpathcli']))) {
									$_SESSION['clpath'] = str_replace(substr($_SESSION['permpathcli'], 0, 2), strtoupper(substr($_SESSION['permpathcli'], 0, 2)), $_SESSION['permpathcli']);
								}
							}
							?>

							<form id="resetview" name="resetview" method="post">
								<input type="hidden" id="resetviewconf" name="resetviewconf" value="resetviewconf" />
								<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
							</form>
                        	<h3>Path:</h3>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
								<?php if ($_SESSION['usersett']['filebrowserf'] != '') { ?>
								<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Permitted Folders:</div></td><td width="80%">
								<form id="permpaths" name="permpaths" method="post">
									<select id="permpath" name="permpath" onChange="this.form.submit()" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; margin-top:2px; margin-bottom:2px; font-size:12px;">
										<?php if ($filebrowserbllist != '') { echo $filebrowserbllist; } else { echo '<option>No Results...</option>'; } ?>
									</select>
									<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
								</form>
								</td></tr>
								<?php } ?>
								<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) { ?><a href="#" onclick="document.openexp.submit();" title="Browse Folder"><div class="icon-folder"></div></a>&nbsp;<?php } ?>Current Path:</div></td><td width="80%" style="font-size:12px;"><?php echo str_replace(substr($_SESSION['clpath'], 0, 2), strtoupper(substr($_SESSION['clpath'], 0, 2)), $_SESSION['clpath']); ?>&nbsp;&nbsp;<a href="#" onclick="document.resetview.submit();" title="Reset CLI"><div class="icon-undo"></div></a></td></tr>
								<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Date Modified:</div></td><td width="80%" style="font-size:12px;"><?php echo date('d/m/Y H:i:s', filemtime($_SESSION['clpath'])); ?></td></tr>
							</table>
                        	<h3>Ouput:</h3>
                        	<textarea id="output" name="output" style="color:#cbc2cc; background-color:#000000; font-size:16px; height:280px; font-family:CLIfont; font-weight:normal; line-height:75%;"><?php echo $outputh . $clioutput; ?></textarea>
                        </div>
						
						<form id="openexp" name="openexp" method="post">
							<input type="hidden" id="openexppath" name="openexppath" value="<?php echo urlencode(str_replace('\\\\', '\\', $_SESSION['clpath'])); if (strlen(str_replace('\\\\', '\\', $_SESSION['clpath'])) > 3) { echo '%5C'; } ?>" />
							<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
						</form>
						<form id="clicommand" name="clicommand" method="post">
							<div class="grid">
								<div class="row">
		                           	<h3>Command:</h3>
									<div class="input-control select span1.5">
										<select id="cldrive" name="cldrive" onchange="this.form.submit()" style="color:#cbc2cc; background-color:#000000; font-size:16px; font-family:CLIfont; font-weight:normal; line-height:75%;"<?php if ($_SESSION['usersett']['filebrowserf'] != '') { echo ' disabled'; } ?>>
											<?php
											$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_LogicalDisk");
												foreach($wmisclass as $obj) {
												if (is_null($obj->Size) == false) {
													$selected = '';
													if (strtolower($cldrive) == strtolower($obj->Caption) || strtolower($cmd) == strtolower($obj->Caption)) { $selected = 'selected'; }
													echo '<option value="' . strtolower($obj->Caption) . '" ' . $selected . '>' . strtoupper($obj->Caption) . '\></option>';
												}
											}
											?>
										</select>
											<?php if ($_SESSION['usersett']['filebrowserf'] != '') { ?>
												<input type="hidden" id="cldrive" name="cldrive" value="<?php echo $cldrive; ?>" />
											<?php } ?>
											<?php
											$_SESSION['cmdlistfld'] = array();
											$_SESSION['cmdlistfls'] = array();
											$_SESSION['cmdlistsrv'] = array();
											$_SESSION['cmdlistprc'] = array();
											if (file_exists($euryscoinstallpath . '\\temp\\core\\' . $random . '.dir')) {
												$data = array_slice(file($euryscoinstallpath . '\\temp\\core\\' . $random . '.dir'), -10000);
												foreach ($data as $line) {
													if (!preg_match('/\'/', $line)) {
														$quotecmd = '';
														if (substr_count($line, ' ') > 0) {
															$quotecmd = '"' . preg_replace('/\r\n|\r|\n/', '', $line) . '"';
														} else {
															$quotecmd = preg_replace('/\r\n|\r|\n/', '', $line);
														}
														array_push($_SESSION['cmdlistfld'], 'cd ' . $quotecmd);
														array_push($_SESSION['cmdlistfld'], 'dir ' . $quotecmd . ' /a');
													}
												}
											} else {
												unset($_SESSION['cldrive']);
												unset($_SESSION['cldrive_old']);
												unset($_SESSION['clpath']);
												unset($_SESSION['cltimeout']);
												unset($_SESSION['outputh']);
												unset($_SESSION['cmdmem']);
												unset($_SESSION['clpaths']);
												header('location: ' . $_SERVER["SCRIPT_NAME"]);
												exit;
											}
											if (file_exists($euryscoinstallpath . '\\temp\\core\\' . $random . '.files')) {
												$data = array_slice(file($euryscoinstallpath . '\\temp\\core\\' . $random . '.files'), -10000);
												foreach ($data as $line) {
													if (!preg_match('/\'/', $line)) {
														$quotecmd = '';
														if (substr_count($line, ' ') > 0) {
															$quotecmd = '"' . preg_replace('/\r\n|\r|\n/', '', $line) . '"';
														} else {
															$quotecmd = preg_replace('/\r\n|\r|\n/', '', $line);
														}
														array_push($_SESSION['cmdlistfls'], 'type ' . $quotecmd);
														array_push($_SESSION['cmdlistfls'], 'dir ' . $quotecmd . ' /a');
													}
												}
											} else {
												unset($_SESSION['cldrive']);
												unset($_SESSION['cldrive_old']);
												unset($_SESSION['clpath']);
												unset($_SESSION['cltimeout']);
												unset($_SESSION['outputh']);
												unset($_SESSION['cmdmem']);
												unset($_SESSION['clpaths']);
												header('location: ' . $_SERVER["SCRIPT_NAME"]);
												exit;
											}
											$wmiservices = $wmi->ExecQuery("SELECT Name FROM Win32_Service");
											foreach($wmiservices as $service) {
												$quotecmd = '';
												if (substr_count(trim($service->Name), ' ') > 0) {
													$quotecmd = '"' . trim($service->Name) . '"';
												} else {
													$quotecmd = trim($service->Name);
												}
												array_push($_SESSION['cmdlistsrv'], 'sc query ' . $quotecmd);
											}
											$wmiprocesses = $wmi->ExecQuery("SELECT Name FROM Win32_Process");
											$processchk = '';
											foreach($wmiprocesses as $processes) {
												if (!strpos('|' . $processchk . '|', '|' . trim($processes->Name) . '|')) {
													array_push($_SESSION['cmdlistprc'], 'tasklist /fi "imagename eq ' . trim($processes->Name) . '"');
												}
												$processchk = $processchk . '|' . trim($processes->Name) . '|';
											}
										
											?>
									</div>
									<div class="input-control input span5">
		                            	<input type="text" id="cmd" name="cmd" value="" autofocus="on" autocomplete="off" style="color:#cbc2cc; background-color:#000000; font-size:16px; font-family:CLIfont; font-weight:normal; line-height:75%;">
									</div>
									<div class="input-control select span1.5">
										<select id="cltimeout" name="cltimeout" style="color:#cbc2cc; background-color:#000000; font-size:16px; font-family:CLIfont; font-weight:normal; line-height:75%;">
											<option value="30000" <?php if ($_SESSION['cltimeout'] == '30000') { echo 'selected'; } ?>>Timeout 30 Sec</option>
											<option value="60000" <?php if ($_SESSION['cltimeout'] == '60000') { echo 'selected'; } ?>>Timeout 60 Sec</option>
											<option value="300000" <?php if ($_SESSION['cltimeout'] == '300000') { echo 'selected'; } ?>>Timeout 05 Min</option>
											<option value="600000" <?php if ($_SESSION['cltimeout'] == '600000') { echo 'selected'; } ?>>Timeout 10 Min</option>
											<option value="900000" <?php if ($_SESSION['cltimeout'] == '900000') { echo 'selected'; } ?>>Timeout 15 Min</option>
											<option value="1800000" <?php if ($_SESSION['cltimeout'] == '1800000') { echo 'selected'; } ?>>Timeout 30 Min</option>
										</select>
									</div>
									<input type="submit" style="background-color:#0072C6;" value="Run">
								</div>
							</div>
                            
                           	<h3>History:</h3>
							<div class="input-control select">
								<select id="cmdmemlist" name="cmdmemlist" multiple="6" style="color:#cbc2cc; background-color:#000000; font-size:16px; font-family:CLIfont; font-weight:normal; line-height:75%;">
                                	<?php echo $cmdmem; ?>
								</select>
                                <?php $_SESSION['cmdmem'] = $cmdmem; ?>
							</div>
							<script type="text/javascript">
								var options = {
									script:"clijq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&json=true&limit=4&",
									varname:"input",
									json:true,
									shownoresults:false,
									maxresults:900,
									callback: function (obj) { document.getElementById('cmd').value = obj.id; }
								};
								var as_json = new bsn.AutoSuggest('cmd', options);
							</script>
							<script type="text/javascript">
								$(document).ready(function(e) {
									$("#cmdmemlist").change(function(){
										var textval = $(":selected",this).val(); 
										$('input[name=cmd]').val(textval);
									})
								});
							</script>
							<script type="text/javascript">
								$(document).ready(function(){
									$('#output').scrollTop($('#output')[0].scrollHeight);
								});
							</script>
							<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
						</form>
                        <h3>Help:</h3>
                        <blockquote>
                        	 auto complete enabled for <strong>dir</strong>, <strong>cd</strong>, <strong>type</strong>, <strong>sc</strong> and <strong>tasklist</strong> command
                        </blockquote>
                        <br />
                        <blockquote>
	                        <span class="label warning" style="font-size:14px; font-weight:normal;"><strong>warning</strong></span> do not run any commands that require user interaction, otherwise you must restart <a href="<?php echo $corelink; ?>/executor.php">Executor</a>
                        </blockquote>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
@unlink($euryscoinstallpath . '\\temp\\core\\' . $random . '.cmd');
@unlink($euryscoinstallpath . '\\temp\\core\\' . $random . '.dir');
@unlink($euryscoinstallpath . '\\temp\\core\\' . $random . '.files');
@unlink($euryscoinstallpath . '\\temp\\core\\' . $random . '.out');
@unlink($euryscoinstallpath . '\\temp\\core\\' . $random . '.path');
?>

<?php } ?>

<?php include("footer.php"); ?>