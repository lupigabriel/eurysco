<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['changesettings'] > 0) {  } else { header('location: /'); exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>General<small>settings</small></h1>
			<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-settings-button big page-back"></a>
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
					$message = '';
					
					if(isset($_POST['submitform']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
						
						if(isset($_POST['timezonesetting'])) {
							$timezonesetting = $_POST['timezonesetting'];
						} else {
							$timezonesetting = '';
						}
						
						if(isset($_POST['systeminforrsetting'])) {
							$systeminforrsetting = $_POST['systeminforrsetting'];
						} else {
							$systeminforrsetting = '';
						}
						
						if(isset($_POST['programsrrsetting'])) {
							$programsrrsetting = $_POST['programsrrsetting'];
						} else {
							$programsrrsetting = '';
						}
						
						if(isset($_POST['processesrrsetting'])) {
							$processesrrsetting = $_POST['processesrrsetting'];
						} else {
							$processesrrsetting = '';
						}
						
						if(isset($_POST['servicesrrsetting'])) {
							$servicesrrsetting = $_POST['servicesrrsetting'];
						} else {
							$servicesrrsetting = '';
						}
						
						if(isset($_POST['schedulerrrsetting'])) {
							$schedulerrrsetting = $_POST['schedulerrrsetting'];
						} else {
							$schedulerrrsetting = '';
						}
						
						if(isset($_POST['eventsrrsetting'])) {
							$eventsrrsetting = $_POST['eventsrrsetting'];
						} else {
							$eventsrrsetting = '';
						}
						
						if(isset($_POST['nagiosrrsetting'])) {
							$nagiosrrsetting = $_POST['nagiosrrsetting'];
						} else {
							$nagiosrrsetting = '';
						}
						
						if(isset($_POST['netstatrrsetting'])) {
							$netstatrrsetting = $_POST['netstatrrsetting'];
						} else {
							$netstatrrsetting = '';
						}
						
						if(isset($_POST['registryrrsetting'])) {
							$registryrrsetting = $_POST['registryrrsetting'];
						} else {
							$registryrrsetting = '';
						}
						
						if(isset($_POST['explorerrrsetting'])) {
							$explorerrrsetting = $_POST['explorerrrsetting'];
						} else {
							$explorerrrsetting = '';
						}
						
						if(isset($_POST['wmiexprrsetting'])) {
							$wmiexprrsetting = $_POST['wmiexprrsetting'];
						} else {
							$wmiexprrsetting = '';
						}

						if(isset($_POST['tailrrsetting'])) {
							$tailrrsetting = $_POST['tailrrsetting'];
						} else {
							$tailrrsetting = '';
						}
						
						if(isset($_POST['nodesstatusrrsetting'])) {
							$nodesstatusrrsetting = $_POST['nodesstatusrrsetting'];
						} else {
							$nodesstatusrrsetting = '';
						}
						
						if(isset($_POST['nodesrrsetting'])) {
							$nodesrrsetting = $_POST['nodesrrsetting'];
						} else {
							$nodesrrsetting = '';
						}
						
						if(isset($_POST['statuscsetting'])) {
							$statuscsetting = $_POST['statuscsetting'];
						} else {
							$statuscsetting = '';
						}
						
						if(isset($_POST['processescsetting'])) {
							$processescsetting = $_POST['processescsetting'];
						} else {
							$processescsetting = '';
						}
						
						if(isset($_POST['servicescsetting'])) {
							$servicescsetting = $_POST['servicescsetting'];
						} else {
							$servicescsetting = '';
						}
						
						if(isset($_POST['taskscsetting'])) {
							$taskscsetting = $_POST['taskscsetting'];
						} else {
							$taskscsetting = '';
						}

						if(isset($_POST['eventscsetting'])) {
							$eventscsetting = $_POST['eventscsetting'];
						} else {
							$eventscsetting = '';
						}

						if(isset($_POST['nagioscsetting'])) {
							$nagioscsetting = $_POST['nagioscsetting'];
						} else {
							$nagioscsetting = '';
						}

						if(isset($_POST['netstatcsetting'])) {
							$netstatcsetting = $_POST['netstatcsetting'];
						} else {
							$netstatcsetting = '';
						}

						if(isset($_POST['programscsetting'])) {
							$programscsetting = $_POST['programscsetting'];
						} else {
							$programscsetting = '';
						}

						if(isset($_POST['inventorycsetting'])) {
							$inventorycsetting = $_POST['inventorycsetting'];
						} else {
							$inventorycsetting = '';
						}
						
						if(isset($_POST['nodesclearsetting'])) {
							$nodesclearsetting = $_POST['nodesclearsetting'];
						} else {
							$nodesclearsetting = '';
						}
						
						if(isset($_POST['nodescommandblacklist'])) {
							$nodescommandblacklist = $_POST['nodescommandblacklist'];
						} else {
							$nodescommandblacklist = '';
						}
						
						if(isset($_POST['localcommandblacklist'])) {
							$localcommandblacklist = $_POST['localcommandblacklist'];
						} else {
							$localcommandblacklist = '';
						}
						
						if(isset($_POST['tailextsetting'])) {
							$tailextsetting = $_POST['tailextsetting'];
						} else {
							$tailextsetting = '';
						}
						
						if(isset($_POST['zipextsetting'])) {
							$zipextsetting = $_POST['zipextsetting'];
						} else {
							$zipextsetting = '';
						}
						
						if(isset($_POST['uploadextsetting'])) {
							$uploadextsetting = $_POST['uploadextsetting'];
						} else {
							$uploadextsetting = '';
						}
						
						if(isset($_POST['uploadsetting'])) {
							$uploadsetting = $_POST['uploadsetting'];
						} else {
							$uploadsetting = '';
						}
						
						if(isset($_POST['mapsharesetting'])) {
							$mapsharesetting = $_POST['mapsharesetting'];
						} else {
							$mapsharesetting = '';
						}
						
						if(isset($_POST['loginmessagesetting'])) {
							$loginmessagesetting = $_POST['loginmessagesetting'];
						} else {
							$loginmessagesetting = '';
						}
						
						if(isset($_POST['mapsharesettingclear'])) {
							if ($_POST['mapsharesettingclear'] == 'on') {
								$mapsharesettingclear = 'Enable';
							} else {
								$mapsharesettingclear = 'Disable';
							}
						} else {
							$mapsharesettingclear = 'Disable';
						}
						
						if(isset($_POST['wmiexplorerhidevalues'])) {
							if ($_POST['wmiexplorerhidevalues'] == 'on') {
								$wmiexplorerhidevalues = 'Enable';
							} else {
								$wmiexplorerhidevalues = 'Disable';
							}
						} else {
							$wmiexplorerhidevalues = 'Disable';
						}
						
						if(isset($_POST['roottaskssetting'])) {
							if ($_POST['roottaskssetting'] == 'on') {
								$roottaskssetting = 'Enable';
							} else {
								$roottaskssetting = 'Disable';
							}
						} else {
							$roottaskssetting = 'Disable';
						}
						
						if(isset($_POST['changepwdlocalsetting'])) {
							if ($_POST['changepwdlocalsetting'] == 'on') {
								$changepwdlocalsetting = 'Enable';
							} else {
								$changepwdlocalsetting = 'Disable';
							}
						} else {
							$changepwdlocalsetting = 'Disable';
						}
						
						$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<timezonesetting>' . $timezonesetting . '</timezonesetting>' . "\n" . '		<systeminforrsetting>' . $systeminforrsetting . '</systeminforrsetting>' . "\n" . '		<programsrrsetting>' . $programsrrsetting . '</programsrrsetting>' . "\n" . '		<processesrrsetting>' . $processesrrsetting . '</processesrrsetting>' . "\n" . '		<servicesrrsetting>' . $servicesrrsetting . '</servicesrrsetting>' . "\n" . '		<schedulerrrsetting>' . $schedulerrrsetting . '</schedulerrrsetting>' . "\n" . '		<eventsrrsetting>' . $eventsrrsetting . '</eventsrrsetting>' . "\n" . '		<nagiosrrsetting>' . $nagiosrrsetting . '</nagiosrrsetting>' . "\n" . '		<netstatrrsetting>' . $netstatrrsetting . '</netstatrrsetting>' . "\n" . '		<registryrrsetting>' . $registryrrsetting . '</registryrrsetting>' . "\n" . '		<explorerrrsetting>' . $explorerrrsetting . '</explorerrrsetting>' . "\n" . '		<wmiexprrsetting>' . $wmiexprrsetting . '</wmiexprrsetting>' . "\n" . '		<tailrrsetting>' . $tailrrsetting . '</tailrrsetting>' . "\n" . '		<nodesstatusrrsetting>' . $nodesstatusrrsetting . '</nodesstatusrrsetting>' . "\n" . '		<nodesrrsetting>' . $nodesrrsetting . '</nodesrrsetting>' . "\n" . '		<statuscsetting>' . $statuscsetting . '</statuscsetting>' . "\n" . '		<processescsetting>' . $processescsetting . '</processescsetting>' . "\n" . '		<servicescsetting>' . $servicescsetting . '</servicescsetting>' . "\n" . '		<taskscsetting>' . $taskscsetting . '</taskscsetting>' . "\n" . '		<eventscsetting>' . $eventscsetting . '</eventscsetting>' . "\n" . '		<nagioscsetting>' . $nagioscsetting . '</nagioscsetting>' . "\n" . '		<netstatcsetting>' . $netstatcsetting . '</netstatcsetting>' . "\n" . '		<programscsetting>' . $programscsetting . '</programscsetting>' . "\n" . '		<inventorycsetting>' . $inventorycsetting . '</inventorycsetting>' . "\n" . '		<nodesclearsetting>' . $nodesclearsetting . '</nodesclearsetting>' . "\n" . '		<nodescommandblacklist>' . $nodescommandblacklist . '</nodescommandblacklist>' . "\n" . '		<localcommandblacklist>' . $localcommandblacklist . '</localcommandblacklist>' . "\n" . '		<tailextsetting>' . $tailextsetting . '</tailextsetting>' . "\n" . '		<zipextsetting>' . $zipextsetting . '</zipextsetting>' . "\n" . '		<uploadextsetting>' . $uploadextsetting . '</uploadextsetting>' . "\n" . '		<uploadsetting>' . $uploadsetting . '</uploadsetting>' . "\n" . '		<mapsharesetting>' . $mapsharesetting . '</mapsharesetting>' . "\n" . '		<loginmessagesetting>' . $loginmessagesetting . '</loginmessagesetting>' . "\n" . '		<wmiexplorerhidevalues>' . $wmiexplorerhidevalues . '</wmiexplorerhidevalues>' . "\n" . '		<roottaskssetting>' . $roottaskssetting . '</roottaskssetting>' . "\n" . '		<changepwdlocalsetting>' . $changepwdlocalsetting . '</changepwdlocalsetting>' . "\n" . '	</settings>' . "\n" . '</config>';
						$sxe = new SimpleXMLElement($xml);
						if (!isset($_POST['deploysettings'])) {
							$writexml = fopen($config_settings, 'w');
							fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
							fclose($writexml);
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     change settings     settings changed successfully';
							if ($mapsharesettingclear == 'Enable') {
								$clear_mapsharesetting = exec('net.exe use * /delete /y', $errorarray, $errorlevel);
								$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     change settings     settings changed and mapped shares refreshed successfully';							
							}
							if ($mapsharesetting != '') {
								$set_mapsharesetting = exec('net.exe use ' . preg_replace('/[\n\r]/', ' & net.exe use ', $mapsharesetting), $errorarray, $errorlevel);
							}
							header('location: ' . $_SERVER['PHP_SELF']);
						} else {
							$writexml = fopen($euryscoinstallpath . '\\server\\settings\\' . md5($_SESSION['username']) . '.xml', 'w');
							fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
							fclose($writexml);
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     change settings     settings deploy ready';
							if (!isset($_SESSION['nodes'])) { header('location: ' . $corelink . '/nodes.php'); } else { header('location: ' . $corelink . $_SESSION['nodes']); }
						}
					}
					?>
                    
                    <blockquote><div class="icon-warning" style="color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;&nbsp;&nbsp;be careful when changing the parameters</blockquote>
                    <br />
                    <br />

                    <form id="adminsettings" name="adminsettings" method="post">
					<h3><div class="icon-earth" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Timezone:</h3>
					<div class="input-control select">
						<select id="timezonesetting" name="timezonesetting">
							<option value="UTC">UTC</option>
							<?php

							$timezone_identifiers = DateTimeZone::listIdentifiers();

							foreach ($timezone_identifiers as $timezoneid) {
								if ($timezoneid != 'UTC') {									
									if ($timezoneid == $timezonesetting) {
										$timeselected = 'selected';
									} else {
										$timeselected = '';
									}
									echo '<option value="' . $timezoneid . '" ' . $timeselected . '>' . $timezoneid . '</option>';
								}
							}

							?>
						</select>
					</div>
					<div id="datetime"></div>
					<br />
                    <br />
                    <h3><div class="icon-loading" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Refresh Rate:</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                        <?php if ($serverstatus == 'run') { ?>
						<tr>
							<td style="background-color:#eeeaf6; font-size:13px;">Nodes Status:</td>
							<td style="background-color:#eeeaf6;">
                            	<select id="nodesstatusrrsetting" name="nodesstatusrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
                                	<option value="5000" <?php if ($nodesstatusrrsetting == '5000') { echo 'selected'; } ?>>5 sec</option>
                                	<option value="10000" <?php if ($nodesstatusrrsetting == '10000') { echo 'selected'; } ?>>10 sec</option>
                                	<option value="15000" <?php if ($nodesstatusrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($nodesstatusrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                </select>
                            </td>
						</tr>
                        <?php } else { ?>
							<input type="hidden" id="nodesstatusrrsetting" name="nodesstatusrrsetting" value="15000" />
                        <?php } ?>
						<tr>
							<td width="50%" style="font-size:13px;">System Info:</td>
							<td width="50%">
                            	<select id="systeminforrsetting" name="systeminforrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                                	<option value="5000" <?php if ($systeminforrsetting == '5000') { echo 'selected'; } ?>>5 sec</option>
                                    <option value="10000" <?php if ($systeminforrsetting == '10000') { echo 'selected'; } ?>>10 sec</option>
                                    <option value="15000" <?php if ($systeminforrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($systeminforrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="Hold" <?php if ($systeminforrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<?php if ($nagios_agent_status == 'Running') { ?>
						<tr>
							<td style="font-size:13px;">Nagios Status:</td>
							<td>
                            	<select id="nagiosrrsetting" name="nagiosrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                            		<option value="15000" <?php if ($nagiosrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($nagiosrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="60000" <?php if ($nagiosrrsetting == '60000') { echo 'selected'; } ?>>60 sec</option>
                                    <option value="90000" <?php if ($nagiosrrsetting == '90000') { echo 'selected'; } ?>>90 sec</option>
                                    <option value="Hold" <?php if ($nagiosrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<?php } else { ?>
							<input type="hidden" id="nagiosrrsetting" name="nagiosrrsetting" value="30000" />
                        <?php } ?>
						<tr>
							<td style="font-size:13px;">Installed Programs:</td>
							<td>
                            	<select id="programsrrsetting" name="programsrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                                	<option value="5000" <?php if ($programsrrsetting == '5000') { echo 'selected'; } ?>>5 sec</option>
                                    <option value="10000" <?php if ($programsrrsetting == '10000') { echo 'selected'; } ?>>10 sec</option>
                                    <option value="15000" <?php if ($programsrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($programsrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="Hold" <?php if ($programsrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="font-size:13px;">WMI Explorer:</td>
							<td>
                            	<select id="wmiexprrsetting" name="wmiexprrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                            		<option value="30000" <?php if ($wmiexprrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="60000" <?php if ($wmiexprrsetting == '60000') { echo 'selected'; } ?>>60 sec</option>
                                    <option value="90000" <?php if ($wmiexprrsetting == '90000') { echo 'selected'; } ?>>90 sec</option>
                                    <option value="Hold" <?php if ($wmiexprrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="font-size:13px;">Process Control:</td>
							<td>
                            	<select id="processesrrsetting" name="processesrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                                	<option value="5000" <?php if ($processesrrsetting == '5000') { echo 'selected'; } ?>>5 sec</option>
                                    <option value="10000" <?php if ($processesrrsetting == '10000') { echo 'selected'; } ?>>10 sec</option>
                                    <option value="15000" <?php if ($processesrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($processesrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="Hold" <?php if ($processesrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="font-size:13px;">Service Control:</td>
							<td>
                            	<select id="servicesrrsetting" name="servicesrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                                	<option value="5000" <?php if ($servicesrrsetting == '5000') { echo 'selected'; } ?>>5 sec</option>
                                    <option value="10000" <?php if ($servicesrrsetting == '10000') { echo 'selected'; } ?>>10 sec</option>
                                    <option value="15000" <?php if ($servicesrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($servicesrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="Hold" <?php if ($servicesrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="font-size:13px;">Network Stats:</td>
							<td>
                            	<select id="netstatrrsetting" name="netstatrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                            		<option value="5000" <?php if ($netstatrrsetting == '5000') { echo 'selected'; } ?>>5 sec</option>
                            		<option value="10000" <?php if ($netstatrrsetting == '10000') { echo 'selected'; } ?>>10 sec</option>
                            		<option value="15000" <?php if ($netstatrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($netstatrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="Hold" <?php if ($netstatrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="font-size:13px;">Scheduled Tasks:</td>
							<td>
                            	<select id="schedulerrrsetting" name="schedulerrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                                	<option value="5000" <?php if ($schedulerrrsetting == '5000') { echo 'selected'; } ?>>5 sec</option>
                                    <option value="10000" <?php if ($schedulerrrsetting == '10000') { echo 'selected'; } ?>>10 sec</option>
                                    <option value="15000" <?php if ($schedulerrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($schedulerrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="Hold" <?php if ($schedulerrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="font-size:13px;">Event Viewer:</td>
							<td>
                            	<select id="eventsrrsetting" name="eventsrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                            		<option value="5000" <?php if ($eventsrrsetting == '5000') { echo 'selected'; } ?>>5 sec</option>
                                    <option value="10000" <?php if ($eventsrrsetting == '10000') { echo 'selected'; } ?>>10 sec</option>
                                    <option value="15000" <?php if ($eventsrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($eventsrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="Hold" <?php if ($eventsrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="font-size:13px;">System Registry:</td>
							<td>
                            	<select id="registryrrsetting" name="registryrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                            		<option value="5000" <?php if ($registryrrsetting == '5000') { echo 'selected'; } ?>>5 sec</option>
                                    <option value="10000" <?php if ($registryrrsetting == '10000') { echo 'selected'; } ?>>10 sec</option>
                                    <option value="15000" <?php if ($registryrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($registryrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="Hold" <?php if ($registryrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="font-size:13px;">File Browser:</td>
							<td>
                            	<select id="explorerrrsetting" name="explorerrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                            		<option value="30000" <?php if ($explorerrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="60000" <?php if ($explorerrrsetting == '60000') { echo 'selected'; } ?>>60 sec</option>
                                    <option value="90000" <?php if ($explorerrrsetting == '90000') { echo 'selected'; } ?>>90 sec</option>
                                    <option value="Hold" <?php if ($explorerrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<td style="font-size:13px;">Text Reader:</td>
							<td>
                            	<select id="tailrrsetting" name="tailrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                                	<option value="2000" <?php if ($tailrrsetting == '2000') { echo 'selected'; } ?>>2 sec</option>
                                    <option value="4000" <?php if ($tailrrsetting == '4000') { echo 'selected'; } ?>>4 sec</option>
                                    <option value="60000" <?php if ($tailrrsetting == '60000') { echo 'selected'; } ?>>60 sec</option>
                                </select>
                            </td>
						</tr>
					</table>
                    <br />
                    <?php if ($agentstatus != 'cfg' || $serverstatus == 'run') { ?>
                    <h3><div class="icon-loading-2" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Agent Cycle:</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                        <?php if ($serverstatus == 'run') { ?>
						<tr>
							<td style="background-color:#eeeaf6; font-size:13px;">Node Cycle:</td>
							<td style="background-color:#eeeaf6;">
                            	<select id="nodesrrsetting" name="nodesrrsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
                            		<option value="15000" <?php if ($nodesrrsetting == '15000') { echo 'selected'; } ?>>15 sec</option>
                                    <option value="30000" <?php if ($nodesrrsetting == '30000') { echo 'selected'; } ?>>30 sec</option>
                                    <option value="60000" <?php if ($nodesrrsetting == '60000') { echo 'selected'; } ?>>60 sec</option>
                                    <option value="Hold" <?php if ($nodesrrsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
                        <?php } else { ?>
							<input type="hidden" id="nodesrrsetting" name="nodesrrsetting" value="15000" />
                        <?php } ?>
                        <?php if ($agentstatus != 'cfg') { ?>
						<tr>
							<td style="font-size:13px;">Status:</td>
							<td>
                            	<select id="statuscsetting" name="statuscsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                                	<option value="1" <?php if ($statuscsetting == '1') { echo 'selected'; $allcyclesel = ''; } ?>>1</option>
                                    <option value="Hold" <?php if ($statuscsetting == 'Hold') { echo 'selected'; $allcyclesel = ' disabled="disabled"'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<?php if ($nagios_agent_status == 'Running' || $serverstatus == 'run') { ?>
						<tr>
							<?php if ($statuscsetting == 'Hold') { ?>
								<input type="hidden" id="nagioscsetting" name="nagioscsetting" value="<?php echo $nagioscsetting; ?>" />
							<?php } ?>
							<td style="font-size:13px;">Nagios:</td>
							<td>
                            	<select id="nagioscsetting" name="nagioscsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;"<?php echo $allcyclesel; ?>>
                                	<option value="1" <?php if ($nagioscsetting == '1' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>1</option>
                                	<option value="2" <?php if ($nagioscsetting == '2' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>2</option>
                                    <option value="3" <?php if ($nagioscsetting == '3' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>3</option>
                                    <option value="4" <?php if ($nagioscsetting == '4' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>4</option>
                                    <option value="5" <?php if ($nagioscsetting == '5' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>5</option>
                                    <option value="6" <?php if ($nagioscsetting == '6' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>6</option>
                                    <option value="7" <?php if ($nagioscsetting == '7' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>7</option>
                                    <option value="8" <?php if ($nagioscsetting == '8' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>8</option>
                                    <option value="16" <?php if ($nagioscsetting == '16' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>16</option>
                                    <option value="32" <?php if ($nagioscsetting == '32' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>32</option>
                                    <option value="Hold" <?php if ($nagioscsetting == 'Hold' || $statuscsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<?php } else { ?>
							<input type="hidden" id="nagioscsetting" name="nagioscsetting" value="2" />
                        <?php } ?>
						<tr>
							<?php if ($statuscsetting == 'Hold') { ?>
								<input type="hidden" id="inventorycsetting" name="inventorycsetting" value="<?php echo $inventorycsetting; ?>" />
							<?php } ?>
							<td style="font-size:13px;">Inventory:</td>
							<td>
                            	<select id="inventorycsetting" name="inventorycsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;"<?php echo $allcyclesel; ?>>
                                	<option value="120" <?php if ($inventorycsetting == '120' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>120</option>
                                	<option value="131" <?php if ($inventorycsetting == '131' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>131</option>
                                    <option value="240" <?php if ($inventorycsetting == '240' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>240</option>
                                    <option value="251" <?php if ($inventorycsetting == '251' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>251</option>
                                    <option value="480" <?php if ($inventorycsetting == '480' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>480</option>
                                    <option value="491" <?php if ($inventorycsetting == '491' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>491</option>
                                    <option value="960" <?php if ($inventorycsetting == '960' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>960</option>
                                    <option value="967" <?php if ($inventorycsetting == '967' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>967</option>
                                    <option value="Hold" <?php if ($inventorycsetting == 'Hold' || $statuscsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<?php if ($statuscsetting == 'Hold') { ?>
								<input type="hidden" id="programscsetting" name="programscsetting" value="<?php echo $programscsetting; ?>" />
							<?php } ?>
							<td style="font-size:13px;">Programs:</td>
							<td>
                            	<select id="programscsetting" name="programscsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;"<?php echo $allcyclesel; ?>>
                                	<option value="60" <?php if ($programscsetting == '60' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>60</option>
                                	<option value="61" <?php if ($programscsetting == '61' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>61</option>
                                    <option value="120" <?php if ($programscsetting == '120' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>120</option>
                                    <option value="127" <?php if ($programscsetting == '127' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>127</option>
                                    <option value="240" <?php if ($programscsetting == '240' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>240</option>
                                    <option value="241" <?php if ($programscsetting == '241' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>241</option>
                                    <option value="480" <?php if ($programscsetting == '480' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>480</option>
                                    <option value="487" <?php if ($programscsetting == '487' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>487</option>
                                    <option value="Hold" <?php if ($programscsetting == 'Hold' || $statuscsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<?php if ($statuscsetting == 'Hold') { ?>
								<input type="hidden" id="processescsetting" name="processescsetting" value="<?php echo $processescsetting; ?>" />
							<?php } ?>
							<td style="font-size:13px;">Processes:</td>
							<td>
                            	<select id="processescsetting" name="processescsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;"<?php echo $allcyclesel; ?>>
                                	<option value="1" <?php if ($processescsetting == '1' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>1</option>
                                    <option value="2" <?php if ($processescsetting == '2' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>2</option>
                                    <option value="3" <?php if ($processescsetting == '3' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>3</option>
                                    <option value="4" <?php if ($processescsetting == '4' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>4</option>
                                    <option value="5" <?php if ($processescsetting == '5' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>5</option>
                                    <option value="6" <?php if ($processescsetting == '6' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>6</option>
                                    <option value="7" <?php if ($processescsetting == '7' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>7</option>
                                    <option value="8" <?php if ($processescsetting == '8' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>8</option>
                                    <option value="Hold" <?php if ($processescsetting == 'Hold' || $statuscsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<?php if ($statuscsetting == 'Hold') { ?>
								<input type="hidden" id="servicescsetting" name="servicescsetting" value="<?php echo $servicescsetting; ?>" />
							<?php } ?>
							<td style="font-size:13px;">Services:</td>
							<td>
                            	<select id="servicescsetting" name="servicescsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;"<?php echo $allcyclesel; ?>>
                                	<option value="1" <?php if ($servicescsetting == '1' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>1</option>
                                    <option value="2" <?php if ($servicescsetting == '2' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>2</option>
                                    <option value="3" <?php if ($servicescsetting == '3' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>3</option>
                                    <option value="4" <?php if ($servicescsetting == '4' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>4</option>
                                    <option value="5" <?php if ($servicescsetting == '5' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>5</option>
                                    <option value="6" <?php if ($servicescsetting == '6' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>6</option>
                                    <option value="7" <?php if ($servicescsetting == '7' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>7</option>
                                    <option value="8" <?php if ($servicescsetting == '8' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>8</option>
                                    <option value="Hold" <?php if ($servicescsetting == 'Hold' || $statuscsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<?php if ($statuscsetting == 'Hold') { ?>
								<input type="hidden" id="netstatcsetting" name="netstatcsetting" value="<?php echo $netstatcsetting; ?>" />
							<?php } ?>
							<td style="font-size:13px;">Netstat:</td>
							<td>
                            	<select id="netstatcsetting" name="netstatcsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;"<?php echo $allcyclesel; ?>>
                                	<option value="1" <?php if ($netstatcsetting == '1' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>1</option>
                                	<option value="2" <?php if ($netstatcsetting == '2' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>2</option>
                                	<option value="3" <?php if ($netstatcsetting == '3' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>3</option>
                                    <option value="4" <?php if ($netstatcsetting == '4' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>4</option>
                                    <option value="5" <?php if ($netstatcsetting == '5' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>5</option>
                                    <option value="6" <?php if ($netstatcsetting == '6' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>6</option>
                                    <option value="7" <?php if ($netstatcsetting == '7' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>7</option>
                                    <option value="8" <?php if ($netstatcsetting == '8' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>8</option>
                                    <option value="16" <?php if ($netstatcsetting == '16' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>16</option>
                                    <option value="32" <?php if ($netstatcsetting == '32' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>32</option>
                                    <option value="Hold" <?php if ($netstatcsetting == 'Hold' || $statuscsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<?php if ($statuscsetting == 'Hold') { ?>
								<input type="hidden" id="taskscsetting" name="taskscsetting" value="<?php echo $taskscsetting; ?>" />
							<?php } ?>
							<td style="font-size:13px;">Tasks:</td>
							<td>
                            	<select id="taskscsetting" name="taskscsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;"<?php echo $allcyclesel; ?>>
                                	<option value="2" <?php if ($taskscsetting == '2' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>2</option>
                                	<option value="3" <?php if ($taskscsetting == '3' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>3</option>
                                    <option value="4" <?php if ($taskscsetting == '4' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>4</option>
                                	<option value="5" <?php if ($taskscsetting == '5' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>5</option>
                                	<option value="6" <?php if ($taskscsetting == '6' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>6</option>
                                	<option value="7" <?php if ($taskscsetting == '7' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>7</option>
                                    <option value="8" <?php if ($taskscsetting == '8' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>8</option>
                                    <option value="10" <?php if ($taskscsetting == '10' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>10</option>
                                	<option value="12" <?php if ($taskscsetting == '12' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>12</option>
                                    <option value="Hold" <?php if ($taskscsetting == 'Hold' || $statuscsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<tr>
							<?php if ($statuscsetting == 'Hold') { ?>
								<input type="hidden" id="eventscsetting" name="eventscsetting" value="<?php echo $eventscsetting; ?>" />
							<?php } ?>
							<td style="font-size:13px;">Events:</td>
							<td>
                            	<select id="eventscsetting" name="eventscsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;"<?php echo $allcyclesel; ?>>
                                	<option value="29" <?php if ($eventscsetting == '29' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>29</option>
                                	<option value="30" <?php if ($eventscsetting == '30' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>30</option>
                                    <option value="59" <?php if ($eventscsetting == '59' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>59</option>
                                    <option value="60" <?php if ($eventscsetting == '60' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>60</option>
                                    <option value="113" <?php if ($eventscsetting == '113' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>113</option>
                                    <option value="120" <?php if ($eventscsetting == '120' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>120</option>
                                    <option value="239" <?php if ($eventscsetting == '239' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>239</option>
                                    <option value="240" <?php if ($eventscsetting == '240' && $statuscsetting != 'Hold') { echo 'selected'; } ?>>240</option>
                                    <option value="Hold" <?php if ($eventscsetting == 'Hold' || $statuscsetting == 'Hold') { echo 'selected'; } ?>>Hold</option>
                                </select>
                            </td>
						</tr>
						<?php } else { ?>
							<input type="hidden" id="statuscsetting" name="statuscsetting" value="1" />
							<input type="hidden" id="processescsetting" name="processescsetting" value="2" />
							<input type="hidden" id="servicescsetting" name="servicescsetting" value="2" />
							<input type="hidden" id="taskscsetting" name="taskscsetting" value="4" />
							<input type="hidden" id="eventscsetting" name="eventscsetting" value="60" />
							<input type="hidden" id="programscsetting" name="programscsetting" value="120" />
							<input type="hidden" id="inventorycsetting" name="inventorycsetting" value="240" />
							<input type="hidden" id="nagioscsetting" name="nagioscsetting" value="4" />
							<input type="hidden" id="netstatcsetting" name="netstatcsetting" value="2" />
						<?php } ?>
					</table>
                    <br />
					<?php } else { ?>
						<input type="hidden" id="nodesrrsetting" name="nodesrrsetting" value="15000" />
						<input type="hidden" id="statuscsetting" name="statuscsetting" value="1" />
						<input type="hidden" id="processescsetting" name="processescsetting" value="2" />
						<input type="hidden" id="servicescsetting" name="servicescsetting" value="2" />
						<input type="hidden" id="taskscsetting" name="taskscsetting" value="4" />
						<input type="hidden" id="eventscsetting" name="eventscsetting" value="60" />
						<input type="hidden" id="programscsetting" name="programscsetting" value="120" />
						<input type="hidden" id="inventorycsetting" name="inventorycsetting" value="240" />
						<input type="hidden" id="nagioscsetting" name="nagioscsetting" value="4" />
						<input type="hidden" id="netstatcsetting" name="netstatcsetting" value="2" />
					<?php } ?>
                    <?php if ($serverstatus == 'run') { ?>
                    <h3><div class="icon-share-2" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Nodes:</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
						<tr>
							<td style="background-color:#eeeaf6; font-size:13px;">Delete Disconnected Nodes:</td>
							<td style="background-color:#eeeaf6;">
                            	<select id="nodesclearsetting" name="nodesclearsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
                            		<option value="1296000" <?php if ($nodesclearsetting == '1296000') { echo 'selected'; } ?>>15 days</option>
                                    <option value="2592000" <?php if ($nodesclearsetting == '2592000') { echo 'selected'; } ?>>30 days</option>
                                    <option value="5184000" <?php if ($nodesclearsetting == '5184000') { echo 'selected'; } ?>>60 days</option>
                                    <option value="7776000" <?php if ($nodesclearsetting == '7776000') { echo 'selected'; } ?>>90 days</option>
                                    <option value="Never" <?php if ($nodesclearsetting == 'Never') { echo 'selected'; } ?>>Never</option>
                                </select>
                            </td>
						</tr>
					</table>
                    <br />
                    <?php } else { ?>
						<input type="hidden" id="nodesclearsetting" name="nodesclearsetting" value="1296000" />
                    <?php } ?>
                    <?php if ($serverstatus == 'run') { ?>
                    <h3><div class="icon-console" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Nodes Command:</h3>
					<?php if ($_SESSION['username'] != 'Administrator') { ?><div style="font-size:12px;">* only administrator can modify this parameter</div><?php } ?>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
						<tr>
							<td width="50%" style="background-color:#eeeaf6; font-size:13px;">BlackList:</td>
							<td width="50%" style="background-color:#eeeaf6;"><?php if ($_SESSION['username'] == 'Administrator') { ?><input type="text" id="nodescommandblacklist" name="nodescommandblacklist" placeholder="chkdsk,bcedit,del,dism,diskpart,format,nslookup,fsutil,move,net user,powershell.*clear,powershell.*disconnect,powershell.*dismount,powershell.*remove,powershell.*stop-computer,rd,ren,rename,rmdir,servercanagercmd.*install,servercanagercmd.*remove,sc config,sc delete,shutdown,wmic,reg,regedit,takeown,icacls,cacls,repadmin,dcpromo" value="<?php echo $nodescommandblacklist; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" /><?php } else { ?><input type="text" id="nodescommandblacklist" name="nodescommandblacklist" placeholder="chkdsk,bcedit,del,dism,diskpart,format,fsutil,move,net.*user.*delete,powershell.*clear,powershell.*disconnect,powershell.*dismount,powershell.*remove,powershell.*stop-computer,rd,ren,rename,rmdir,servercanagercmd.*install,servercanagercmd.*remove,sc.*config,sc.*delete,shutdown" value="<?php echo $nodescommandblacklist; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" disabled="disabled" /><input type="hidden" id="nodescommandblacklist" name="nodescommandblacklist" value="<?php echo $nodescommandblacklist; ?>" /><?php } ?></td>
						</tr>
					</table>
                    <br />
                    <?php } else { ?>
						<input type="hidden" id="nodescommandblacklist" name="nodescommandblacklist" value="<?php echo $nodescommandblacklist; ?>" />
                    <?php } ?>
                    <h3><div class="icon-console" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Local Command:</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
						<tr>
							<td width="50%" style="font-size:13px;">BlackList:</td>
							<td width="50%"><input type="text" id="localcommandblacklist" name="localcommandblacklist" placeholder="chkdsk,bcedit,dism,diskpart,format,nslookup,fsutil,net user,powershell.*clear,powershell.*disconnect,powershell.*dismount,powershell.*remove,powershell.*stop-computer,servercanagercmd.*install,servercanagercmd.*remove,sc config,sc delete,shutdown,wmic,reg,regedit,takeown,icacls,cacls,repadmin,dcpromo" value="<?php echo $localcommandblacklist; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" /></td>
						</tr>
					</table>
                    <br />
					<h3><div class="icon-play-alt" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Tail:</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
						<tr>
							<td width="50%" style="font-size:13px;">Extension Allowed:</td>
							<td width="50%"><input type="text" id="tailextsetting" name="tailextsetting" placeholder=", ,txt,log,csv,cfg,inf,ini,vbs,bat,cmd,htm,html,xml,css,ascx,asp,aspx,php" value="<?php echo $tailextsetting; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" /></td>
						</tr>
					</table>
                    <br />
					<h3><div class="icon-file-zip" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;7zip:</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
						<tr>
							<td width="50%" style="font-size:13px;">Extension Allowed:</td>
							<td width="50%"><input type="text" id="zipextsetting" name="zipextsetting" placeholder="7z,arj,bz2,bzip2,cab,cpio,deb,dmg,fat,gz,gzip,hfs,iso,lha,lzh,lzma,ntfs,rar,rpm,squashfs,swm,tar,taz,tbz,tbz2,tgz,tpz,txz,vhd,wim,xar,xz,z,zip" value="<?php echo $zipextsetting; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" /></td>
						</tr>
					</table>
                    <br />
					<h3><div class="icon-upload-3" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Upload:</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
						<tr>
							<td width="50%" style="font-size:13px;">Extension Allowed:</td>
							<td width="50%"><input type="text" id="uploadextsetting" name="uploadextsetting" placeholder="*" value="<?php echo $uploadextsetting; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" /></td>
						</tr>
						<tr>
							<td style="font-size:13px;">Single File Size:</td>
							<td>
                            	<select id="uploadsetting" name="uploadsetting" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
                                	<option value="5" <?php if ($uploadsetting == '25') { echo 'selected'; } ?>>25 MB</option>
                                	<option value="50" <?php if ($uploadsetting == '50') { echo 'selected'; } ?>>50 MB</option>
                                	<option value="100" <?php if ($uploadsetting == '100') { echo 'selected'; } ?>>100 MB</option>
                                	<option value="200" <?php if ($uploadsetting == '200') { echo 'selected'; } ?>>200 MB</option>
                                	<option value="400" <?php if ($uploadsetting == '400') { echo 'selected'; } ?>>400 MB</option>
                                	<option value="800" <?php if ($uploadsetting == '800') { echo 'selected'; } ?>>800 MB</option>
                                	<option value="1600" <?php if ($uploadsetting == '1600') { echo 'selected'; } ?>>1600 MB</option>
                                	<option value="3200" <?php if ($uploadsetting == '3200') { echo 'selected'; } ?>>3200 MB</option>
                                </select>
                            </td>
						</tr>
					</table>
                    <br />
					<h3><div class="icon-share-3" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Map Network Shares:</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
						<tr>
							<td width="22%" style="font-size:13px;"><input type="checkbox" id="mapsharesettingclear" name="mapsharesettingclear" /><span class="helper" style="font-size:13px;">&nbsp;Refresh</span></td>
							<td width="78%">
								<textarea id="mapsharesetting" name="mapsharesetting" placeholder="*: &quot;\\computername\sharename&quot;
*: &quot;\\computername\sharename&quot; /user:domain\user password
*: &quot;\\computername\sharename&quot; /user:domain\user password" wrap="off" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; height:55px; padding-left:5px; padding-right:5px;"><?php echo $mapsharesetting; ?></textarea>
							</td>
						</tr>
						<tr><td colspan="2" style="font-size:11px;"><?php if ($mapsharesetting != '') { $status_mapsharesetting = shell_exec('net.exe use | find /i ":"'); echo preg_replace('/[\n\r]/', '<br />', $status_mapsharesetting); } ?></td></tr>
					</table>
					<br />
					<h3><div class="icon-info-2" style="font-size:17px; color:#999999" title="Refresh Rate"></div>&nbsp;&nbsp;Login Message:</h3>
                    <input type="text" id="loginmessagesetting" name="loginmessagesetting" placeholder="Disabled" value="<?php echo $loginmessagesetting; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" />
					<br />
					<br />
					<br />
					<h3><div class="icon-grid-view" style="font-size:17px; color:#999999" title="WMI Explorer"></div>&nbsp;&nbsp;WMI Explorer:</h3>
					<input type="checkbox" id="wmiexplorerhidevalues" name="wmiexplorerhidevalues" <?php if ($wmiexplorerhidevalues == 'Enable') { echo 'checked'; } ?> />
					<span class="helper" style="font-size:13px;">&nbsp;Hide empty values</span>
                    <br />
                    <br />
                    <br />
					<h3><div class="icon-calendar" style="font-size:17px; color:#999999" title="Scheduler"></div>&nbsp;&nbsp;Scheduler:</h3>
                    <?php
					$checkosversion = '';
					$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_OperatingSystem");
					foreach($wmisclass as $obj) {
						if (preg_replace('/\..*/', '', $obj->Version) < 6) {
							$checkosversion = 'disabled="disabled"';
						}
					}
					?>
					<input type="checkbox" id="roottaskssetting" name="roottaskssetting" <?php if ($roottaskssetting == 'Enable') { echo 'checked'; } echo $checkosversion; ?> />
					<span class="helper" style="font-size:13px;">&nbsp;Display only root folder</span>
                    <br />
                    <br />
                    <br />
					<h3><div class="icon-key" style="font-size:17px; color:#999999" title="Password Management"></div>&nbsp;&nbsp;Password Management:</h3>
					<input type="checkbox" id="changepwdlocalsetting" name="changepwdlocalsetting" <?php if ($changepwdlocalsetting == 'Enable') { echo 'checked'; } ?> />
					<span class="helper" style="font-size:13px;">&nbsp;Change Password allowed only through local connection</span>
					
                    <input type="hidden" id="submitform" name="submitform" />
                    <br />
                    <br />
                    <br />
                    <br />
                    <input type="submit" style="background-color:#0072C6;" value="Save Settings" />
					<?php if ($serverstatus == 'run') { ?>
                    <input type="submit" id="deploysettings" name="deploysettings" class="bg-color-purple" value="Deploy Settings" />
                    <?php } ?>
					<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
                    </form>

					<script language="javascript" type="text/javascript">
					update();
					setInterval(update, 5000);
					function update() {
						$.ajax({
							type: "GET",
							url: 'datetimejq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>',
							data: '',
							dataType: 'json',
							cache: false,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#datetime').html(data.datetime);
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