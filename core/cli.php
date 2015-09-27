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
							if ($_SESSION['usersett']['filebrowserf'] != '') {
								$filebrowserblarray = array();
								$filebrowserblarray = (explode(',', preg_replace('/\r\n|\r|\n/', ',', $_SESSION['usersett']['filebrowserf'])));
								foreach ($filebrowserblarray as $filebrowserbl) {
									$filebrowserbl = rtrim($filebrowserbl, '\\') . '\\';
									if (is_dir($filebrowserbl) && !is_file($filebrowserbl)) {
										if (!isset($_SESSION['permpathcli']) && $filebrowserblcount == 0) { $_SESSION['permpathcli'] = str_replace(substr($filebrowserbl, 0, 2), strtoupper(substr($filebrowserbl, 0, 2)), $filebrowserbl); $_SESSION['clpath'] = $_SESSION['permpathcli']; }
										if (strpos('|' . trim(strtolower($_SESSION['clpath']), '\\'), trim(strtolower($filebrowserbl), '\\')) > 0) { $filebrowserblsel = 'selected'; } else { $filebrowserblsel = ''; }
										$filebrowserbllist = $filebrowserbllist . '<option value="' . str_replace(substr($filebrowserbl, 0, 2), strtoupper(substr($filebrowserbl, 0, 2)), $filebrowserbl) . '" ' . $filebrowserblsel . '>' . rtrim($filebrowserbl, '\\') . '&nbsp;&nbsp;&nbsp;</option>';
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
									$_SESSION['cldrive'] = 'C:';
									$_SESSION['cldrive_old'] = 'C:';
									$_SESSION['clpath'] = '\\';
								}
							}
							
							$commandblcheck = 0;
							if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['commandline'] > 0) {
								if (isset($_POST['cmd']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
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

									$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.out', 'w');
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
									$cldrive = 'C:';
								}
							}
							$_SESSION['cldrive'] = $cldrive;

							if (isset($_SESSION['cldrive_old'])) {
								$cldrive_old = $_SESSION['cldrive_old'];
							} else {
								$cldrive_old = 'C:';
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
							
							if (strtolower($cmd) == 'c:' || strtolower($cldrive) != strtolower($cldrive_old)) {
								$frrt = ' & cd \\';
							} else {
								$frrt = '';
							}

							if ($cldrive != $cldrive_old) {
								$cmdmem = "<option value='" . $cldrive . "'>" . str_replace(" ", "&nbsp;", $cldrive) . "</option>" . $cmdmem . "\n";
							} else {
								if ($cmd != '') { $cmdmem = "<option value='" . $cmd . "'>" . str_replace(" ", "&nbsp;", $cmd) . "</option>" . $cmdmem . "\n"; }
							}


							$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.cmd', 'w');
							fwrite($fp, $cmd);
							fclose($fp);
							
							if (strlen(strtolower($cmd)) == 2 && substr(strtolower($cmd), -1) == ':') {
								$cldrive = strtolower($cmd);
								$clpath = '\\';
							}
							
							session_write_close();
							$clioutput = shell_exec($cldrive . ' & cd "' . $clpath . '" & "' . $_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.cmd">>"' . $_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.out"' . $frrt . ' & dir /b /o:n /a:d>"' . $_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.dir" & dir /b /o:n /a:-d>"' . $_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.files" & cd>"' . $_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.path" & echo. & cd');
							session_start();
							
							if (isset($_SESSION['permpathcli'])) {
								if (!strpos('|' . trim(strtolower($clioutput)) . '\\', strtolower($_SESSION['permpathcli']))) {
									$clioutput = str_replace(substr($_SESSION['permpathcli'], 0, 2), strtoupper(substr($_SESSION['permpathcli'], 0, 2)), $_SESSION['permpathcli']);
								}
							}
							$clioutput = str_replace(substr($clioutput, 0, 2), strtoupper(substr($clioutput, 0, 2)), $clioutput);

							if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.cmd') || !file_exists($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.out') || !file_exists($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.dir') || !file_exists($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.files') || !file_exists($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.path')) {
								header('location: ' . $_SERVER['PHP_SELF'] . '?resetviewconf');
							}
							
							$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.path', 'r');
							$clpath = preg_replace('/\r\n|\r|\n/','',fgets($fp));
							fclose($fp);
							$clpath = str_replace(substr($clpath, 0, 2), strtoupper(substr($clpath, 0, 2)), $clpath);
							
							$data = array_slice(file($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.out'), -900);
							foreach ($data as $line) {
								$outputh = $outputh . $line;
							}
							@unlink($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.out');
							$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.out', 'w');
							fwrite($fp, $outputh);
							fclose($fp);
							$outputh = '';
							$data = array_slice(file($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.out'), -900);
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
							</form>
                        	<h3>Path:</h3>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
								<?php if ($_SESSION['usersett']['filebrowserf'] != '') { ?>
								<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Permitted Folders:</div></td><td width="80%">
								<form id="permpaths" name="permpaths" method="post">
									<select id="permpath" name="permpath" onChange="this.form.submit()" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; margin-top:2px; margin-bottom:2px; font-size:12px;">
										<?php if ($filebrowserbllist != '') { echo $filebrowserbllist; } else { echo '<option>No Results...</option>'; } ?>
									</select>
								</form>
								</td></tr>
								<?php } ?>
								<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) { ?><a href="/explorer.php?path=<?php echo urlencode(str_replace('\\\\', '\\', $_SESSION['clpath'])); if (strlen(str_replace('\\\\', '\\', $_SESSION['clpath'])) > 3) { echo '%5C'; } ?>" title="Browse Folder"><div class="icon-folder"></div></a>&nbsp;<?php } ?>Current Path:</div></td><td width="80%" style="font-size:12px;"><?php echo str_replace(substr($_SESSION['clpath'], 0, 2), strtoupper(substr($_SESSION['clpath'], 0, 2)), $_SESSION['clpath']); ?>&nbsp;&nbsp;<a href="#" onclick="document.resetview.submit();" title="Reset CLI"><div class="icon-undo"></div></a></td></tr>
								<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Date Modified:</div></td><td width="80%" style="font-size:12px;"><?php echo date('d/m/Y H:i:s', filemtime($_SESSION['clpath'])); ?></td></tr>
							</table>
                        	<h3>Ouput:</h3>
                        	<textarea id="output" name="output" style="color:#cbc2cc; background-color:#000000; font-size:16px; height:280px; font-family:CLIfont; font-weight:normal; line-height:75%;"><?php echo $outputh . $clioutput; ?></textarea>
                        </div>
						
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
													if ($cldrive == strtolower($obj->Caption) && strlen(strtolower($cmd)) != 2 && substr(strtolower($cmd), -1) != ':' || strtolower($cmd) == strtolower($obj->Caption)) { $selected = 'selected'; }
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
											$data = array_slice(file($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.dir'), -10000);
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
											$data = array_slice(file($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.files'), -10000);
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
									<div class="input-control input span7">
		                            	<input type="text" id="cmd" name="cmd" value="" autofocus="on" autocomplete="off" style="color:#cbc2cc; background-color:#000000; font-size:16px; font-family:CLIfont; font-weight:normal; line-height:75%;">
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
									script:"clijq.php?json=true&limit=4&",
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
@unlink($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.cmd');
@unlink($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.dir');
@unlink($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.files');
@unlink($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.out');
@unlink($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $random . '.path');
?>

<?php } ?>

<?php include("footer.php"); ?>