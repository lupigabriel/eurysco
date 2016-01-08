<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesstatus'] > 0) {  } else { header('location: /'); exit; } ?>

<?php if (!isset($_GET['csv_nodes'])) { $_SESSION['nodes'] = htmlspecialchars((string)$_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'); } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<script type="text/javascript">
	function nodeinfo(ComputerName,Domain,ComputerIP,LastCom,Manufacturer,Model,OSName,CPUModel,ManufacturerFull,ModelFull,OSNameFull,CPUModelFull,DomainFull,ComDom,ComDomFull,Inventory,Programs,CorePort,ExecutorPort,Find,FindType,FilterPrograms,AgentVersion,ConnectionStatus){
		if (ComputerIP == 'localhost') { ToRemote = ''; } else { ToRemote = '<a href="https://' + ComputerIP + ':' + CorePort + '" style="font-size:13px;" target="_blank" title="Connect to Node"><div class="icon-enter"></div></a>&nbsp;'; }
		if (ConnectionStatus == 'Disconnected') { AgentRestart = ''; } else { AgentRestart = '<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesstatus'] > 1) { ?><a href="javascript:;" onclick="parentNode.submit();" title="Restart Agent" style="color:#8063C8;"><div class="icon-loop" style="font-size:16px;"></div></a><input type="hidden" id="agentrefresh" name="agentrefresh" value="' + ComputerName + '" />&nbsp;<?php } ?>'; }
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-feed" style="position:inherit;"></div>&nbsp; Node: <strong>' + ComputerName + '</strong></span>',
			'content'     : '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselectsrv"><td colspan="2" style="font-size:12px;" Title="' + ComDomFull + '">' + ToRemote + ComDom + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Domain:&nbsp;</td><td style="font-size:12px;"><a href="/nodes.php?find=domain.' + DomainFull + '&findtype=status" style="font-size:12px;" title="Filter by Domain: ' + DomainFull + '">' + Domain + '</a></td></tr><tr class="rowselectsrv"><td style="font-size:12px;">IP Address:&nbsp;</td><td style="font-size:12px;">' + ComputerIP + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;"><a href="/xml.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&export=status&source=' + ComputerName + '" style="font-size:12px;" title="Export Source XML"><div class="icon-file-xml"></div></a>&nbsp;Last&nbsp;Update:&nbsp;</td><td style="font-size:12px;">' + LastCom + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Manufacturer:&nbsp;</td><td style="font-size:12px;"><a href="/nodes.php?find=' + ManufacturerFull + '&findtype=status" style="font-size:12px;" title="Filter by Manufacturer: ' + ManufacturerFull + '"><div class="icon-tag"></div>&nbsp;' + Manufacturer + '</a></td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Model:&nbsp;</td><td style="font-size:12px;"><a href="/nodes.php?find=' + ModelFull + '&findtype=status" style="font-size:12px;" title="Filter by Model: ' + ModelFull + '"><div class="icon-monitor"></div>&nbsp;' + Model + '</a></td></tr><tr class="rowselectsrv"><td colspan="2" style="font-size:12px;" title="' + CPUModelFull + '"><a href="/nodes.php?find=' + CPUModelFull + '&findtype=status" style="font-size:12px;" title="Filter by CPU Model: ' + CPUModelFull + '"><div class="icon-checkbox-partial"></div>&nbsp;' + CPUModel + '</a></td></tr><tr class="rowselectsrv"><td colspan="2" style="font-size:12px;" title="' + OSNameFull + '"><a href="/nodes.php?find=' + OSNameFull + '&findtype=status" style="font-size:12px;" title="Filter by OS Name: ' + OSNameFull + '"><div class="icon-windows"></div>&nbsp;' + OSName + '</a></td></tr><tr class="rowselectsrv"><td style="font-size:12px;"><form id="agentrefreshform" name="agentrefreshform" method="post">' + AgentRestart + 'eurysco Agent:&nbsp;<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" /></form></td><td style="font-size:12px;">Version ' + AgentVersion + '</td></tr></table>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '45px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 164) + 'px'
			},
			'buttons'     : {
				<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodessysteminventory'] > 0) { ?>
				'Inventory'     : {
				'action': function(){
						if (Inventory == 'Inventory') { document.location.href = '/nodes_inventory.php?node=' + ComputerName + '&domain=' + DomainFull + '&computerip=' + ComputerIP + '&executorport=' + ExecutorPort; }
					}
				},
				<?php } ?>
				<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesinstalledprograms'] > 0) { ?>
				'Programs'     : {
				'action': function(){
						if (Programs == 'Programs') { document.location.href = '/nodes_programs.php?node=' + ComputerName + '&domain=' + DomainFull + '&computerip=' + ComputerIP + '&executorport=' + ExecutorPort + FilterPrograms; }
					}
				},
				<?php } ?>
				'Cancel'     : {
				'action': function(){}
				},
			}
		});
	};
</script>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesstatus'] > 1) { ?>
<script type="text/javascript">
	function removemetering(MeteringFile,MeteringName,LimitMeteringName){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-stats-up" style="position:inherit;"></div>&nbsp; Remove <strong>Metering</strong></span>',
			'content'     : '<strong style="font-size:12px;">' + LimitMeteringName + '</strong><br /><span style="font-size:12px;">Remove Metering Confirmation...</span>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '100px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 114) + 'px'
			},
			'buttons'     : {
				'Remove'     : {
				'action': function(){
						document.getElementById("remmetering").value = MeteringFile;
						document.getElementById("remmeteringname").value = MeteringName;
						document.getElementById("remmeteringform").submit();
					}
				},
				'Cancel'     : {
				'action': function(){
					}
				},
			}
		});
	};
</script>

<script type="text/javascript">
	function removedeploy(ChangedDate){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-list" style="position:inherit;"></div>&nbsp; Remove <strong>Deploy Settings</strong></span>',
			'content'     : '<strong style="font-size:12px;">' + ChangedDate + '</strong><br /><span style="font-size:12px;">Cancel Deploy Confirmation...</span>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '100px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 114) + 'px'
			},
			'buttons'     : {
				'Remove'     : {
				'action': function(){
						document.getElementById("removedeployform").submit();
					}
				},
				'Cancel'     : {
				'action': function(){
					}
				},
			}
		});
	};
</script>

<script type="text/javascript">
	function confirmdeploy(ChangedDate){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-list" style="position:inherit;"></div>&nbsp; Confirm <strong>Deploy Settings</strong></span>',
			'content'     : '<strong style="font-size:12px;">' + ChangedDate + '</strong><br /><span style="font-size:12px;">Deploy Confirmation...</span>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '100px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 114) + 'px'
			},
			'buttons'     : {
				'OK'     : {
				'action': function(){
						document.getElementById("confirmdeployform").submit();
					}
				},
				'Cancel'     : {
				'action': function(){
					}
				},
			}
		});
	};
</script>

<script type="text/javascript">
	function resetallagent(){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-loop" style="position:inherit; font-size:18px;"></div>&nbsp; Restart <strong>Current Agents</strong></span>',
			'content'     : '<span style="font-size:12px;">Restart Confirmation...</span><form id="agentrefreshform" name="agentrefreshform" method="post"><input type="hidden" id="agentrefresh" name="agentrefresh" value="" /><input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" /></form>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '100px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 114) + 'px'
			},
			'buttons'     : {
				'Restart'     : {
				'action': function(){
						document.getElementById("agentrefreshform").submit();
					}
				},
				'Cancel'     : {
				'action': function(){
					}
				},
			}
		});
	};
</script>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['nodesstatus'] > 2) { ?>
<script type="text/javascript">
	function confirmcommand(){
		RunCommand = document.getElementById("cmd").value;
		if (RunCommand == '') { return; }
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-console" style="position:inherit;"></div>&nbsp; Confirm <strong>Command</strong></span>',
			'content'     : '<textarea style="font-size:12px; font-weight:bold; border:0px; width:100%;">' + RunCommand + '</textarea><br /><span style="font-size:12px;">Command Confirmation...</span>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '100px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 114) + 'px'
			},
			'buttons'     : {
				'OK'     : {
				'action': function(){
						document.getElementById("clicommand").submit();
					}
				},
				'Cancel'     : {
				'action': function(){
					}
				},
			}
		});
	};
</script>
<?php } ?>
<?php } ?>
<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Nodes<small>status</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-allnodes-button big page-back"></a>
		</div>
	</div>
</div>

<br />

<div class="page" id="page-index">
	<div class="page-region">
		<div class="page-region-content">
			<div class="grid">
				<div class="row">
		            <div class="span1"></div>
		            <div class="span10">

					<?php
					
					$nodespath = $euryscoinstallpath . '\\nodes\\';

					if (isset($_GET['orderby'])) {
						$orderby = $_GET['orderby'];
					} else {
						$orderby = '';
					}

					if (isset($_GET['find'])) {
						$find = $_GET['find'];
					} else {
						$find = '';
					}
					
					if (isset($_GET['findtype'])) {
						$findtype = $_GET['findtype'];
						$sessionfind = md5($find . $findtype);
					} else {
						$findtype = '';
						$sessionfind = '';
					}
					
					if (isset($_GET['csv_nodes'])) {
						$_SESSION['csv_nodes'] = 'csv_nodes';
					} else {
						$_SESSION['csv_nodes'] = '';
					}

					if ($findtype == '-') { $find = ''; $findtype = ''; }
					
					if ($find == '') { $findtype = ''; }
					
					if (isset($_GET['page'])) {
						if (is_numeric($_GET['page'])) {
							$pgkey = $_GET['page'] - 1;
						} else {
							$pgkey = 0;
						}
					} else {
						$pgkey = 0;
					}
					
					if (isset($_POST['removedeploy']) && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesstatus'] > 1)) {
						$deploysettingspath = $euryscoinstallpath . '\\server\\settings\\' . md5($_SESSION['username']) . '.xml';
						if (file_exists($deploysettingspath)) {
							@unlink($deploysettingspath);
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     change settings     deploy settings cancel';
						}
					}

					if (isset($_POST['confirmdeploy'])) {
						$confirmdeploy = $_POST['confirmdeploy'];
					} else {
						$confirmdeploy = '';
					}
					
					if (isset($_GET['cmdnodesclear'])) {
						unset($_SESSION['cmdnodes']);
						unset($_SESSION['cmdnodescurr']);
						unset($_SESSION['cmdnodeslast']);
						unset($_SESSION['cmdfilter']);
						unset($_GET['cmdnodesclear']);
					}
					
					if (!isset($_SESSION['cmdnodes'])) {
						$_SESSION['cmdnodes'] = '';
					}
					
					if (!isset($_SESSION['cmdnodescurr'])) {
						$_SESSION['cmdnodescurr'] = '';
					}
					
					if (!isset($_SESSION['cmdnodeslast'])) {
						$_SESSION['cmdnodeslast'] = '';
					}
					
					if (isset($_GET['cmdfilter'])) {
						$_SESSION['cmdfilter'] = $_GET['cmdfilter'];
					} else {
						$_SESSION['cmdfilter'] = '';
					}
					
					if (isset($_POST['cltimeout'])) {
						$_SESSION['cltimeout'] = $_POST['cltimeout'];
					} else {
						if (!isset($_SESSION['cltimeout'])) {
							$_SESSION['cltimeout'] = '30000';
						}
					}

					$nodescommandblmessage = '';
					$nodescommandblcheck = 0;
					if ((isset($_POST['cmd']) && $_SESSION['usertype'] == 'Administrators') || (isset($_POST['cmd']) && $_SESSION['usersett']['nodesstatus'] > 2)) {
						if ($_SESSION['username'] != 'Administrator') {
							if ($nodescommandblacklist != '') {
								$nodescommandblarray = array();
								$nodescommandblarray = (explode(',', $nodescommandblacklist));
								$nodescommandcmdrray = array();
								$nodescommandcmdrray = (explode('&', $_POST['cmd']));
								foreach ($nodescommandcmdrray as $nodescommandcm) {
									if ($nodescommandblcheck == 0) {
										foreach ($nodescommandblarray as $nodescommandbl) {
											if ($nodescommandblcheck == 0) {
												$nodescommandblcheck = 1;
												if (!preg_match('/^' . $nodescommandbl . '/', trim(preg_replace('/\s+/', ' ', str_replace('.com', '', str_replace('.exe', '', $nodescommandcm))), ' '))) {
													$nodescommandblcheck = 0;
												}
											}
										}
									}
								}
							}
							if ($_SESSION['usersett']['nodescommandf'] != '' && $nodescommandblcheck == 0) {
								$nodescommandblarray = array();
								$nodescommandblarray = (explode(',', $_SESSION['usersett']['nodescommandf']));
								$nodescommandcmdrray = array();
								$nodescommandcmdrray = (explode('&', $_POST['cmd']));
								foreach ($nodescommandcmdrray as $nodescommandcm) {
									if ($nodescommandblcheck == 0) {
										$nodescommandblcheck = 2;
										foreach ($nodescommandblarray as $nodescommandbl) {
											if (preg_match('/^' . $nodescommandbl . '/', trim(preg_replace('/\s+/', ' ', str_replace('.com', '', str_replace('.exe', '', $nodescommandcm))), ' '))) {
												$nodescommandblcheck = 0;
											}
										}
									}
								}
							}
						}
						if ($nodescommandblcheck == 0) {
							$cmd = trim($_POST['cmd'], ' ');
							$_SESSION['cmdnodes'] = $cmd;
							$_SESSION['cmdnodescurr'] = md5(date('r') . $cmd);
						}
						if ($nodescommandblcheck == 1) {
							$cmd = '';
							$nodescommandblmessage = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">only administrator can run <strong>' . trim($_POST['cmd'], ' ') . '</strong></blockquote><br />';
						}
						if ($nodescommandblcheck == 2) {
							$cmd = '';
							$nodescommandblmessage = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">your account is not allowed to run <strong>' . trim($_POST['cmd'], ' ') . '</strong></blockquote><br />';
						}
					} else {
						$cmd = '';
					}
					
					if (!isset($_SESSION['cmdmemnodes'])) {
						$_SESSION['cmdmemnodes'] = '';
					}
					
					if ($cmd != '') {
						$_SESSION['cmdmemnodes'] = "<option value='" . $cmd . "'>" . str_replace(" ", "&nbsp;", $cmd) . "</option>" . $_SESSION['cmdmemnodes'] . "\n";
						$cmdacco = ' class="active"';
						$cmdplaceholder = 'Command Sent...';
					} else {
						$cmdacco = '';
						$cmdplaceholder = 'Run Command...';
					}
							
					$message = '';
					
					if (isset($_POST['agentrefresh']) && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesstatus'] > 1) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
						if ($_POST['agentrefresh'] != '' && file_exists($euryscoinstallpath . '\\nodes\\' . strtolower($_POST['agentrefresh']) . '\\agent.key')) {
							$mcrykeycmd = pack('H*', hash('sha256', fgets(fopen($euryscoinstallpath . '\\nodes\\' . strtolower($_POST['agentrefresh']) . '\\agent.key', 'r'))));
							$cid = '';
							$exectimeout = 10000;
							$agentrefreshexecoutput = 'taskkill.exe /f /im "eurysco.agent.exec.timeout.exe" /t & taskkill.exe /f /im "php_eurysco_core.exe" /t & taskkill.exe /f /im "httpd_eurysco_core.exe" /t & taskkill.exe /f /im "php_eurysco_executor.exe" /t & taskkill.exe /f /im "httpd_eurysco_executor.exe" /t & sc.exe stop "euryscoAgent" & sc.exe stop "euryscoCore" & sc.exe stop "euryscoCoreSSL" & sc.exe stop "euryscoExecutor" & sc.exe stop "euryscoExecutorSSL" & sc.exe start "euryscoAgent" & sc.exe start "euryscoCore" & sc.exe start "euryscoCoreSSL" & sc.exe start "euryscoExecutor" & sc.exe start "euryscoExecutorSSL"';
							$xml = '<exec>' . "\n";
							$xml = $xml . '	<auditok>null</auditok>' . "\n";
							$xml = $xml . '	<auditko>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . strtolower($_POST['agentrefresh']) . '     nodes control     eurysco Agent Restart command not executed (command sent from server "' . $envcomputername . '")')))))) . '</auditko>' . "\n";
							$xml = $xml . '	<auditnl>null</auditnl>' . "\n";
							$xml = $xml . '	<cid>' . $cid . '</cid>' . "\n";
							$xml = $xml . '	<timeout>' . $exectimeout . '</timeout>' . "\n";
							$xml = $xml . '	<command>' . base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykeycmd, $agentrefreshexecoutput, MCRYPT_MODE_CBC, $iv)) . '</command>' . "\n";
							$xml = $xml . '</exec>';
							$fp = fopen($nodespath . '\\' . strtolower($_POST['agentrefresh']) . '\\' . date('YmdHis') . md5(strtolower($_POST['agentrefresh']) . 'agent') . '.exec', 'w');
							fwrite($fp, $xml);
							fclose($fp);
							$fp = fopen($nodespath . '\\' . strtolower($_POST['agentrefresh']) . '\\exec.on', 'w');
							fclose($fp);
							$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">eurysco Agent Restart command sent to node <strong>' . strtolower($_POST['agentrefresh']) . '</strong></blockquote><br />';
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     nodes control     eurysco Agent Restart command sent to node "' . strtolower($_POST['agentrefresh']) . '"';
						} else {
							$_SESSION['agentrefresh'] = '';
							$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">eurysco Agent Restart command sent to <strong>selected nodes</strong></blockquote><br />';
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     nodes control     eurysco Agent Restart command sent to selected nodes';
						}
					}

					if (isset($_GET['results']) && !isset($_POST['cmd'])) {
						$results = $_GET['results'];
					} else {
						$results = 0;
					}
					
					if ($results == 0) {
						$resultssw = 1;
						$resultsic = 'icon-zoom-in';
						$resultsti = 'View Filter Results';
					} else {
						$resultssw = 0;
						$resultsic = 'icon-zoom-out';
						$resultsti = 'Disable Filter Results';
						$nodesstatusrrsetting = 'Hold';
					}
					
					?>
                    
                    <h2>Nodes:</h2>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div id="csvexport"></div></td><td width="80%"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($nodesstatusrrsetting != 'Hold') { echo number_format(($nodesstatusrrsetting / 1000), 0, ',', '.') . '&nbsp;sec'; } else { echo $nodesstatusrrsetting; } ?>&nbsp;&nbsp;<a href="?orderby=<?php echo $orderby; ?>&find=<?php echo urlencode($find); ?>&findtype=<?php echo urlencode($findtype); ?>&page=<?php echo $pgkey + 1; ?>&results=<?php echo $results; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Node Cycle:</div></td><td width="80%" style="font-size:12px;"><?php if ($nodesrrsetting != 'Hold') { echo number_format(($nodesrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $nodesrrsetting . '&nbsp;&nbsp;'; } ?></td></tr>
						<?php if ($find != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><?php echo str_replace('Filter :', 'Filter:', 'Filter ' . ucfirst(str_replace('metering', 'Metering', str_replace('_', ' ', preg_replace('/.*\./', '', $findtype)))) . ':'); ?></div></td><td width="80%" style="font-size:12px;"><div id="sessionfindres" style="font-size:12px;"></div></td></tr><?php } ?>
						<?php if ($_SESSION['cmdnodes'] != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Command Result:</div></td><td width="80%" style="font-size:12px;"><form id="cmdfilterform" name="cmdfilterform" method="get"><input type="hidden" id="find" name="find" value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$find, ENT_QUOTES, 'UTF-8')))); ?>" /><input type="hidden" id="findtype" name="findtype" value="<?php echo $findtype; ?>" /><select id="cmdfilter" name="cmdfilter" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; border:solid; border:0px; background-color:transparent;" onchange="this.form.submit();"><option id="cmdnodesex" value="all"<?php if ($_SESSION['cmdfilter'] == 'all') { echo ' selected="selected"'; } ?>></option><option id="cmdnodesok" value="success"<?php if ($_SESSION['cmdfilter'] == 'success') { echo ' selected="selected"'; } ?>></option><option id="cmdnodesko" value="error"<?php if ($_SESSION['cmdfilter'] == 'error') { echo ' selected="selected"'; } ?>></option></select><input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />&nbsp;&nbsp;&nbsp;<i><?php echo $_SESSION['cmdnodes']; ?></i>&nbsp;&nbsp;<a href="?orderby=<?php echo $orderby; ?>&cmdnodesclear" style="font-size:12px;" title="Clear Command"><div class="icon-cancel"></div></a></form></td></tr><?php } ?>
                    </table>
					
					<div id="deploysettings"></div>
					
					<div id="filterprogress"></div>

					<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    <blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR' . "\n" . 'Use Raw Data View for REFERENCES'; ?>">
                    	<form id="findform" name="findform" method="get">
                        	Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="find" name="find" placeholder="Regular Expression..." value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$find, ENT_QUOTES, 'UTF-8')))); ?>" title="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$find, ENT_QUOTES, 'UTF-8')))); ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:150px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;"<?php if (strpos($findtype, '_metering') > 0) { echo ' disabled="disabled"'; } ?> />&nbsp;<select id="findtype" name="findtype" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:0px; border-color:#e5e5e5; background-color:#fafafa; width:75px; height:23px;"<?php if (strpos($findtype, '_metering') > 0) { echo ' disabled="disabled"'; } ?>><option value="status">Status</option><option value="-"></option><option value="nagios" <?php if ($findtype == 'nagios') { echo 'selected'; } ?>>Nagios</option><option value="inventory" <?php if ($findtype == 'inventory') { echo 'selected'; } ?>>Inventory</option><option value="programs" <?php if ($findtype == 'programs') { echo 'selected'; } ?>>Programs</option><option value="processes" <?php if ($findtype == 'processes') { echo 'selected'; } ?>>Processes</option><option value="services" <?php if ($findtype == 'services') { echo 'selected'; } ?>>Services</option><option value="netstat" <?php if ($findtype == 'netstat') { echo 'selected'; } ?>>Netstat</option><option value="scheduler" <?php if ($findtype == 'scheduler') { echo 'selected'; } ?>>Scheduler</option><option value="events" <?php if ($findtype == 'events') { echo 'selected'; } ?>>Events</option><?php if (strpos($findtype, '_metering') > 0) { ?><option></option><option selected>Metering</option><?php } ?></select>&nbsp;<?php if (!strpos($findtype, '_metering')) { ?>&nbsp;<a href="javascript:;" onClick="document.getElementById('findform').submit();" title="Find String"><div class="icon-search"<?php if ($find != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php } ?><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesstatus'] > 1) { ?><?php if ($findtype != 'status' && $findtype != '' && !strpos($findtype, '_metering')) { ?>&nbsp;<a href="javascript:;" onClick="document.getElementById('meteringform').submit();" title="<?php echo ucfirst($findtype); ?> Metering"><div class="icon-stats-up"></div></a><?php } ?><?php } ?><?php if ($find != '' && $findtype != '') { ?>&nbsp;<a href="?orderby=<?php echo $orderby; ?>" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
							<input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
							<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
						</form>
					</blockquote>
					</div>
					<br />
					
					<form id="meteringform" name="meteringform" method="get">
						<input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
						<input type="hidden" id="find" name="find" value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$find, ENT_QUOTES, 'UTF-8')))); ?>" />
						<input type="hidden" id="findtype" name="findtype" value="<?php echo $findtype; ?>" />
						<input type="hidden" id="metering" name="metering" value="start" />
						<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
					</form>
                   	<form id="remmeteringform" name="remmeteringform" method="get">
						<input type="hidden" id="remmetering" name="remmetering" />
						<input type="hidden" id="remmeteringname" name="remmeteringname" />
						<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
					</form>
                   	<form id="removedeployform" name="removedeployform" method="post">
						<input type="hidden" id="removedeploy" name="removedeploy" />
						<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
					</form>
                   	<form id="confirmdeployform" name="confirmdeployform" method="post">
						<input type="hidden" id="confirmdeploy" name="confirmdeploy" value="confirmdeploy" />
						<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
					</form>
					<form id="agentrefreshform" name="agentrefreshform" method="post">
						<input type="hidden" id="agentrefresh" name="agentrefresh" value="" />
						<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
					</form>
										
					<?php echo $nodescommandblmessage; ?>
                    
					<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['nodesstatus'] > 2) { ?>
					<ul class="accordion" data-role="accordion">
						<li<?php echo $cmdacco; ?>>
							<a href="#" style="font-size:16px; color:#000;"><div class="icon-console" style="font-size:21px;"></div>Run Command:</a>
							<div>
							<h3>Command:</h3>
							<form id="clicommand" name="clicommand" onsubmit="if(!confirmcommand()){return false;}" method="post">
								<div class="input-control input span6">
									<input type="text" id="cmd" name="cmd" placeholder="<?php echo $cmdplaceholder; ?>" value="" autocomplete="off" style="color:#cbc2cc; background-color:#000000; font-size:16px; font-family:CLIfont; font-weight:normal; line-height:75%;">
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
								<input type="submit" style="background-color:#8063C8;" value="Run">
								<input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
								<input type="hidden" id="find" name="find" value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$find, ENT_QUOTES, 'UTF-8')))); ?>" />
								<input type="hidden" id="findtype" name="findtype" value="<?php echo $findtype; ?>" />
								<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
							</form>
							<?php if ($_SESSION['cmdmemnodes'] != '') { ?>
							<h3>History:</h3>
							<div class="input-control select">
								<select id="cmdmemlist" name="cmdmemlist" multiple="6" style="color:#cbc2cc; background-color:#000000; font-size:16px; font-family:CLIfont; font-weight:normal; line-height:75%;">
									<?php echo $_SESSION['cmdmemnodes']; ?>
								</select>
							</div>
							<script type="text/javascript">
								$(document).ready(function(e) {
									$("#cmdmemlist").change(function(){
										var textval = $(":selected",this).val(); 
										$('input[name=cmd]').val(textval);
									})
								});
							</script>
							<?php } ?>
							</div>
						</li>
					</ul>
					<?php } ?>
					
					<?php
					$meteringpath = $euryscoinstallpath . '\\metering';
					$meteringglob = array_multisort(array_map('filemtime', ($metering = glob($meteringpath . '\\*.*', GLOB_BRACE))), SORT_DESC, $metering);

					if (isset($_GET['meteringactive'])) { $meteringacco = ' class="active"'; } else { $meteringacco = ''; }
					$meteringcnt = intval(count($metering));
					
					$meteringname = $meteringpath . '\\' . md5($find . '.' . $findtype) . '.' . $findtype . '_metering';
					if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesstatus'] > 1) {
						if (isset($_GET['metering'])) {
							if ($meteringcnt < 25) {
								if ($_GET['metering'] == 'start') {
									if (file_exists($meteringname)) {
										$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">' . $findtype . ' metering <strong>' . $find . '</strong> already exist</blockquote><br />';
										$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     nodes control     ' . $findtype . ' metering "' . $find . '" already exist';
									} else {
										$xml = '<metering>' . "\n" . '	<filter>' . base64_encode($find) . '</filter>' . "\n" . '	<starttime>' . base64_encode(date('Y-m-d H:i:s')) . '</starttime>' . "\n" . '	<username>' . base64_encode($_SESSION['username']) . '</username>' . "\n" . '</metering>';
										$fp = fopen($meteringname, 'w');
										fwrite($fp, $xml);
										fclose($fp);
										if (file_exists($meteringname)) {
											$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">' . $findtype . ' metering <strong>' . $find . '</strong> not added</blockquote><br />';
											$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     nodes control     ' . $findtype . ' metering "' . $find . '" added';
										} else {
											$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">' . $findtype . ' metering <strong>' . $find . '</strong> not added</blockquote><br />';
											$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     nodes control     ' . $findtype . ' metering "' . $find . '" not added';
										}
									}
									header('location: ' . $_SERVER['PHP_SELF'] . '?meteringactive');
									exit;
								}
							} else {
								$meteringacco = '';
								$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">allowed maximum 25 concurrent metering</blockquote><br />';
							}
						}
						if (isset($_GET['remmetering']) && isset($_GET['remmeteringname'])) {
							$checkname = '';
							if (file_exists($meteringpath . '\\' . $_GET['remmetering'])) {
							echo $meteringpath . '\\' . $_GET['remmetering'];
								$xml = simplexml_load_file($meteringpath . '\\' . $_GET['remmetering']);
								$checkname = base64_decode($xml->username);
							}
							if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['username'] == $checkname || $_SESSION['usersett']['nodesstatus'] > 2) {
							if (file_exists($meteringpath . '\\' . $_GET['remmetering'])) {
								@unlink($meteringpath . '\\' . $_GET['remmetering']);
								if (!file_exists($meteringpath . '\\' . $_GET['remmetering'])) {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     nodes control     metering "' . $_GET['remmeteringname'] . '" removed';
								} else {
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     nodes control     metering "' . $_GET['remmeteringname'] . '" not removed';
								}
								$meteringcoll = $euryscoinstallpath . '\\nodes';
								$cleanmetering = scandir($meteringcoll);
								foreach ($cleanmetering as $cleannode) {
									if ($cleannode != '.' && $cleannode != '..') {
										if (file_exists($meteringcoll . '\\' . $cleannode . '\\' . $_GET['remmetering'])) {
											@unlink($meteringcoll . '\\' . $cleannode . '\\' . $_GET['remmetering']);
										}
									}
								}
								}
							}
							header('location: ' . $_SERVER['PHP_SELF'] . '?meteringactive');
							exit;
						}
					}
					
					if (count($metering) > 0) {	?>
						<ul class="accordion" data-role="accordion">
							<li<?php echo $meteringacco; ?>>
								<a href="#" style="font-size:16px; color:#000;"><div class="icon-stats-up" style="font-size:17px;"></div>Metering:</a>
								<div>
								<?php
								$meteringcount = 0;
								foreach ($metering as $name) {
									$name = preg_replace('/.*\\\/', '', $name);
									if(preg_replace('/.*_/', '', pathinfo($name)['extension']) == 'metering') { ?>
										<?php $xml = simplexml_load_file($meteringpath . '\\' . $name); ?>
										<?php if (strlen(urlencode(base64_decode($xml->filter))) > 20) { $LimitMeteringName = (substr(urldecode(base64_decode($xml->filter)), 0, 20)) . '&nbsp;[...]'; } else { $LimitMeteringName = urldecode(base64_decode($xml->filter)); } ?>
										<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;"><?php echo preg_replace('/_.*/', '', pathinfo($name)['extension']) . ' <strong>' . base64_decode($xml->filter); ?></strong> metering - <strong><?php echo round((strtotime(date('Y-m-d H:i:s')) - strtotime(base64_decode($xml->starttime))) / 60 / 60 / 24); ?></strong> days - added on <strong><?php echo date('d/m/Y H:i:s', strtotime(base64_decode($xml->starttime))); ?></strong> by user <strong><?php echo base64_decode($xml->username); ?></strong><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['username'] == base64_decode($xml->username) || $_SESSION['usersett']['nodesstatus'] > 2) { ?>&nbsp;&nbsp;<a href="/nodes.php?find=<?php echo urlencode(base64_decode($xml->filter)); ?>&findtype=<?php echo urlencode($name); ?>" style="font-size:12px; color:#FFFFFF;" title="View Metering: <?php echo base64_decode($xml->filter); ?>"><div class="icon-reply-2"></div></a><a href='javascript:removemetering("<?php echo $name; ?>","<?php echo urlencode(base64_decode($xml->filter)); ?>","<?php echo $LimitMeteringName; ?>");' style="font-size:12px; color:#FFFFFF;" title="Remove Metering"><div class="icon-cancel"></div></a><?php } ?></blockquote>
										<?php
										$meteringcount = $meteringcount + 1;
									}
								} ?>
								</div>
							</li>
						</ul>
					<?php }	?>
                    
					<?php echo $message; ?>
					
                    <div id="nodetable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($nodesstatusrrsetting != 'Hold') { echo 'setInterval(update, ' . $nodesstatusrrsetting . ');'; $phptimeout = $nodesstatusrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'nodesjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&orderby=<?php echo $orderby; ?>&find=<?php echo urlencode($find); ?>&findtype=<?php echo urlencode($findtype); ?>&page=<?php echo $pgkey; ?>&phptimeout=<?php echo $phptimeout; ?>&confirmdeploy=<?php echo $confirmdeploy; ?><?php if ($sessionfind != '') { echo '&sessionfind=' . $sessionfind; } ?>&results=<?php echo $results; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($nodesstatusrrsetting != 'Hold') { echo $nodesstatusrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#nodetable').html(data.nodetable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							$('#cmdresult').html(data.cmdresult);
							$('#deploysettings').html(data.deploysettings);
							$('#csvexport').html(data.csvexport);
							$('#filterprogress').html(data.filterprogress);
							$('#cmdnodesex').html(data.cmdnodesex);
							$('#cmdnodesok').html(data.cmdnodesok);
							$('#cmdnodesko').html(data.cmdnodesko);
							if (data.filterprogress == 0) { $('#sessionfindres').html('<i><?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$find, ENT_QUOTES, 'UTF-8')))); ?></i><?php if (!strpos($findtype, '_metering')) { ?>&nbsp;&nbsp;<a href="?orderby=<?php echo $orderby; ?>&find=<?php echo urlencode($find); ?>&findtype=<?php echo urlencode($findtype); ?>&page=<?php echo $pgkey + 1; ?>&results=<?php echo $resultssw; ?>" style="font-size:14px;" title="<?php echo $resultsti; ?>"><div class="<?php echo $resultsic; ?>"></div></a><?php } ?>'); } else { $('#sessionfindres').html('<i><?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$find, ENT_QUOTES, 'UTF-8')))); ?></i>'); }
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