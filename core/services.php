<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['servicecontrol'] > 0) {  } else { header('location: /'); exit; } ?>

<?php if (!isset($_GET['csv_services'])) { $_SESSION['services'] = $_SERVER['REQUEST_URI']; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php

$cpucount = 1;
$wmicpu = $wmi->ExecQuery("SELECT * FROM Win32_ComputerSystem");
foreach($wmicpu as $cpu) {
	$cpucount = $cpu->NumberOfProcessors;
	foreach($cpu->Properties_ as $wmiprop) {
		if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfLogicalProcessors|')) > -1) {
			$cpucount = $cpu->NumberOfLogicalProcessors;
		}
	}
}

?>

<script type="text/javascript">
	function serviceexec(ProcessId,DisplayName,Name,State,StartMode,ExitCode,StartName,LimitName,ShortName){
		$.ajax({
			type: "GET",
			url: 'servicesjqprc.php?idprocess=' + ProcessId + '&cpucount=<?php echo $cpucount; ?>',
			data: '',
			dataType: 'json',
			cache: false,
			contentType: "application/json; charset=utf-8",
			success: function (data) {
			if (data.Name == '') { $('#Name').html('-'); } else { $('#Name').html('<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['processcontrol'] > 0) { ?><a href="/processes.php?filter=' + data.NameURL + '" style="font-size:12px;" title="Filter Processes by Process Name"><div class="icon-bars"></div><?php } ?>' + data.NamePath + '<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['processcontrol'] > 0) { ?></a><?php } ?>&nbsp;&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0">'); }
			$('#Name').attr('title', data.ExecutablePath);
			$('#PercentProcessorTime').html(data.PercentProcessorTime);
			$('#WorkingSetPrivate').html(data.WorkingSetPrivate);
			}
		});
		SelectedAuto = '';
		SelectedManual = '';
		SelectedDisabled = '';
		if (StartMode == 'Auto') { SelectedAuto = ' selected="selected"'; }
		if (StartMode == 'Manual') { SelectedManual = ' selected="selected"'; }
		if (StartMode == 'Disabled') { SelectedDisabled = ' selected="selected"'; }
		if (ProcessId != '-') { ProcessId = '<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['processcontrol'] > 0) { ?><a href="/processes.php?filter=IDProcess.' + ProcessId + '..IDProcess" style="font-size:12px;" title="Filter Processes by PID"><div class="icon-bars"></div><?php } ?>' + ProcessId + '<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['processcontrol'] > 0) { ?></a><?php } ?>'; }
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-cog" style="position:inherit;"></div>&nbsp; Service: <strong>' + ShortName + '</strong></span>',
			'content'     : '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselect"><td colspan="2" style="font-size:12px;" title="' + DisplayName + '"><a href="/services.php?filter=' + DisplayName + '" style="font-size:12px;" title="Filter Services by Service Name"><div class="icon-cog"></div>' + LimitName + '</a></td></tr><tr class="rowselect"><td style="font-size:12px;">PID:&nbsp;</td><td style="font-size:12px;">' + ProcessId + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Name:&nbsp;</td><td style="font-size:12px;" title="' + Name + '">' + ShortName + '</td></tr><tr class="rowselect"><td style="font-size:12px;">State:&nbsp;</td><td style="font-size:12px;">' + State + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Start Mode:&nbsp;</td><td style="font-size:12px;"><form id="startuptypeform" name="startuptypeform" method="post"><select id="startuptype" name="startuptype" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa;"><option value="auto"' + SelectedAuto + '>&nbsp;Automatic&nbsp;&nbsp;</option><option value="demand"' + SelectedManual + '>&nbsp;Manual&nbsp;&nbsp;</option><option value="disabled"' + SelectedDisabled + '>&nbsp;Disabled&nbsp;&nbsp;</option></select><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['servicecontrol'] > 1) { ?>&nbsp;&nbsp;<a href="javascript:;" onclick="parentNode.submit();" title="Change Start Mode"><div class="icon-reply" style="font-size:16px;"></div></a><input type="hidden" id="servicenameexec" name="servicenameexec" value="' + DisplayName + '"><input type="hidden" id="startupname" name="startupname" value="' + Name + '"><?php } ?></form></td></tr><tr class="rowselect"><td style="font-size:12px;">Username:&nbsp;</td><td style="font-size:12px;">' + StartName + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Process:&nbsp;</td><td><div id="Name" style="font-size:12px;"></div></td></tr><tr class="rowselect"><td style="font-size:12px;">CPU Usage:&nbsp;</td><td><div id="PercentProcessorTime" style="font-size:12px;"></div></td></tr><tr class="rowselect"><td style="font-size:12px;">Memory Usage:&nbsp;</td><td><div id="WorkingSetPrivate" style="font-size:12px;"></div></td></tr></table>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '45px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 156) + 'px'
			},
			'buttons'     : {
				<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['servicecontrol'] > 1) { ?>
				'Start'     : {
				'action': function(){
						document.getElementById("serviceidexec").value = Name;
						document.getElementById("servicenameexec").value = DisplayName;
						document.getElementById("servicetypeexec").value = '1';
						document.getElementById("serviceexec").submit();
					}
				},
				'Stop'     : {
				'action': function(){
						document.getElementById("serviceidexec").value = Name;
						document.getElementById("servicenameexec").value = DisplayName;
						document.getElementById("servicetypeexec").value = '2';
						document.getElementById("serviceexec").submit();
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

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Service<small>control</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-services-button big page-back"></a>
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

					if (isset($_GET['orderby'])) {
						$orderby = $_GET['orderby'];
					} else {
						$orderby = 'DisplayName';
					}

					if (isset($_POST['serviceidexec'])) {
						$serviceidexec = $_POST['serviceidexec'];
					} else {
						$serviceidexec = '';
					}

					if (isset($_POST['servicenameexec'])) {
						$servicenameexec = $_POST['servicenameexec'];
					} else {
						$servicenameexec = '';
					}

					if (isset($_POST['servicetypeexec'])) {
						$servicetypeexec = $_POST['servicetypeexec'];
					} else {
						$servicetypeexec = '';
					}
					
					if (isset($_GET['csv_services'])) {
						$_SESSION['csv_services'] = 'csv_services';
					} else {
						$_SESSION['csv_services'] = '';
					}

					$message = '';
					
					if ($serviceidexec != '' && $servicetypeexec != '' && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
						if ($servicetypeexec == 1) {
							session_write_close();
							$serviceexecoutput = exec('sc.exe start "' . $serviceidexec . '"', $errorarray, $errorlevel);
							session_start();
							if ($errorlevel == 0) {
								$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">service <strong>' . $servicenameexec . '</strong> started</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     service control     service "' . $servicenameexec . '" started';
							} else {
								if ($errorlevel == 1056) {
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">service <strong>' . $servicenameexec . '</strong> already been started</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     service control     service "' . $servicenameexec . '" already been started';
								} else {
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">service <strong>' . $servicenameexec . '</strong> not started</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     service control     service "' . $servicenameexec . '" not started';
								}
							}
						}
						if ($servicetypeexec == 2) {
							session_write_close();
							$serviceexecoutput = exec('sc.exe stop "' . $serviceidexec . '"', $errorarray, $errorlevel);
							session_start();
							if ($errorlevel == 0) {
								$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">service <strong>' . $servicenameexec . '</strong> stopped</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     service control     service "' . $servicenameexec . '" stopped';
							} else {
								if ($errorlevel == 1062) {
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">service <strong>' . $servicenameexec . '</strong> already been stopped</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     service control     service "' . $servicenameexec . '" already been stopped';
								} else {
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">service <strong>' . $servicenameexec . '</strong> not stopped</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     service control     service "' . $servicenameexec . '" not stopped';
								}
							}
						}
					}
					
					
					if (isset($_POST['startupname'])) {
						$startupname = $_POST['startupname'];
					} else {
						$startupname = '';
					}

					if (isset($_POST['startuptype'])) {
						$startuptype = $_POST['startuptype'];
					} else {
						$startuptype = '';
					}
					
					if ($startupname != '' && $startuptype != '' && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
						session_write_close();
						exec('sc.exe config "' . $startupname . '" start= ' . $startuptype, $errorarray, $errorlevel);
						session_start();
						if ($errorlevel == 0) {
							$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">service <strong>' . $servicenameexec . '</strong> changed to <strong>' . $startuptype . '</strong></blockquote><br />';
		                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     service control     service "' . $servicenameexec . '" changed to "' . $startuptype . '"';
						} else {
							$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">service <strong>' . $servicenameexec . '</strong> not changed to <strong>' . $startuptype . '</strong></blockquote><br />';
		                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     service control     service "' . $servicenameexec . '" not changed to "' . $startuptype . '"';
						}
					}

					if (isset($_GET['filter'])) {
						$filter = $_GET['filter'];
					} else {
						$filter = '';
					}
					
					if (isset($_GET['page'])) {
						$pgkey = $_GET['page'] - 1;
					} else {
						$pgkey = 0;
					}

					?>
                    
                    <h2>Services:</h2>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div id="csvexport"></div></td><td width="80%"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($servicesrrsetting != 'Hold') { echo number_format(($servicesrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $servicesrrsetting . '&nbsp;&nbsp;'; } ?><a href="?orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
						<?php if ($filter != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Filter:</div></td><td width="80%" style="font-size:12px;"><i><?php echo $filter; ?></i></td></tr><?php } ?>
                    </table>
                    
					<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    <blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR' . "\n" . 'Use Raw Data View for REFERENCES'; ?>">
                    	<form id="filterform" name="filterform" method="get">
                        	Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="filter" name="filter" placeholder="Regular Expression..." value="<?php echo $filter; ?>" title="<?php echo $filter; ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:170px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;" />&nbsp;&nbsp;<a href="javascript:;" onClick="document.getElementById('filterform').submit();" title="Filter by String or Regular Expression"><div class="icon-search"<?php if ($filter != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php if ($filter != '') { ?>&nbsp;<a href="?orderby=<?php echo $orderby; ?>" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
                            <input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
						</form>
					</blockquote>
					</div>
					<br />
                    
                    <?php echo $message; ?>
                    
                    <div id="servicetable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($servicesrrsetting != 'Hold') { echo 'setInterval(update, ' . $servicesrrsetting . ');'; $phptimeout = $servicesrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'servicesjq.php?orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey; ?>&phptimeout=<?php echo $phptimeout; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($servicesrrsetting != 'Hold') { echo $servicesrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#servicetable').html(data.servicetable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							$('#csvexport').html(data.csvexport);
							}
						});
					}
					</script>

					<form id="serviceexec" name="serviceexec" method="post">
						<input type="hidden" id="serviceidexec" name="serviceidexec">
						<input type="hidden" id="servicenameexec" name="servicenameexec">
						<input type="hidden" id="servicetypeexec" name="servicetypeexec">
					</form>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>