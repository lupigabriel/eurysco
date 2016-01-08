<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['scheduledtasks'] > 0) {  } else { header('location: /'); exit; } ?>

<?php if (!isset($_GET['csv_scheduler'])) { $_SESSION['scheduler'] = htmlspecialchars((string)$_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'); } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php

$osversion = '';
$wmios = $wmi->ExecQuery("SELECT * FROM Win32_OperatingSystem");
foreach($wmios as $os) {
	$osversion = preg_replace('/\..*/', '', $os->Version);
}

?>

<script type="text/javascript">
	function commandtask(TaskNameNR,ScheduledTaskState,NextRunTimeAlt,LastRunTime,TitleName,ResultCode,RunAsUser,TaskName,Creator,ScheduledType,Status,TaskToRun,LimitName,RunAsUserShort,CreatorShort){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-calendar" style="position:inherit;"></div>&nbsp; Task: <strong>' + TitleName + '</strong></span>',
			'content'     : '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselect"><td colspan="2" style="font-size:12px;" Title="' + TaskNameNR + ' &raquo; ' + TaskToRun + '"><a href="/scheduler.php?filter=' + TaskNameNR + '" style="font-size:12px;" title="Filter Scheduled Tasks by Task Name"><div class="icon-calendar"></div>' + LimitName + '</a>&nbsp;&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0"></td></tr><tr class="rowselect"><td style="font-size:12px;">State:&nbsp;</td><td style="font-size:12px;">' + ScheduledTaskState + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Next Run:&nbsp;</td><td style="font-size:12px;">' + NextRunTimeAlt + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Last Run:&nbsp;</td><td style="font-size:12px;">' + LastRunTime + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Result Code:&nbsp;</td><td style="font-size:12px;">' + ResultCode + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Status:&nbsp;</td><td style="font-size:12px;">' + Status + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Type:&nbsp;</td><td style="font-size:12px;">' + ScheduledType + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Run As User:&nbsp;</td><td style="font-size:12px;" title="' + RunAsUser + '">' + RunAsUserShort + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Creator:&nbsp;</td><td style="font-size:12px;" title="' + Creator + '">' + CreatorShort + '</td></tr></table>',
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
				<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['scheduledtasks'] > 1) { ?>
				'Run'     : {
				'action': function(){
						document.getElementById("commandtaskname").value = TaskName;
						document.getElementById("commandtitletask").value = TaskNameNR;
						document.getElementById("commandtype").value = '1';
						document.getElementById("commandform").submit();
					}
				},
				'End'     : {
				'action': function(){
						document.getElementById("commandtaskname").value = TaskName;
						document.getElementById("commandtitletask").value = TaskNameNR;
						document.getElementById("commandtype").value = '2';
						document.getElementById("commandform").submit();
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
			<h1>Scheduled<small>tasks</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-scheduler-button big page-back"></a>
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
						$orderby = 'TaskName';
					}

					if (isset($_POST['commandtaskname'])) {
						$commandtaskname = $_POST['commandtaskname'];
					} else {
						$commandtaskname = '';
					}

					if (isset($_POST['commandtitletask'])) {
						$commandtitletask = $_POST['commandtitletask'];
					} else {
						$commandtitletask = '';
					}

					if (isset($_POST['commandtype'])) {
						$commandtype = $_POST['commandtype'];
					} else {
						$commandtype = '';
					}
					
					if (isset($_GET['csv_scheduler'])) {
						$_SESSION['csv_scheduler'] = 'csv_scheduler';
					} else {
						$_SESSION['csv_scheduler'] = '';
					}

					$message = '';
					
					if ($commandtaskname != '' && $commandtype != '' && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
						if ($commandtype == 1) {
							session_write_close();
							$commandformoutput = exec('schtasks.exe /run /tn "' . $commandtaskname . '"', $errorarray, $errorlevel);
							session_start();
							if ($errorlevel == 0) {
								$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">task <strong>' . $commandtitletask . '</strong> started</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     scheduled tasks     task "' . $commandtitletask . '" started';
							} else {
								$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">task <strong>' . $commandtitletask . '</strong> not started</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     scheduled tasks     task "' . $commandtitletask . '" not started';
							}
						}
						if ($commandtype == 2) {
							session_write_close();
							$commandformoutput = exec('schtasks.exe /end /tn "' . $commandtaskname . '"', $errorarray, $errorlevel);
							session_start();
							if ($errorlevel == 0) {
								$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">task <strong>' . $commandtitletask . '</strong> stopped</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     scheduled tasks     task "' . $commandtitletask . '" stopped';
							} else {
								$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">task <strong>' . $commandtitletask . '</strong> not stopped</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     scheduled tasks     task "' . $commandtitletask . '" not stopped';
							}
						}
					}

					if (isset($_GET['filter'])) {
						$filter = $_GET['filter'];
					} else {
						$filter = '';
					}
					
					if (isset($_GET['page'])) {
						if (is_numeric($_GET['page'])) {
							$pgkey = $_GET['page'] - 1;
						} else {
							$pgkey = 0;
						}
					} else {
						$pgkey = 0;
					}
					
					?>
                    
                    <h2>Tasks:</h2>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div id="csvexport"></div></td><td width="80%"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($schedulerrrsetting != 'Hold') { echo number_format(($schedulerrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $schedulerrrsetting . '&nbsp;&nbsp;'; } ?><a href="?orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
						<?php if ($filter != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Filter:</div></td><td width="80%" style="font-size:12px;"><i><?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?></i></td></tr><?php } ?>
                    </table>
                    
					<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    <blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR' . "\n" . 'Use Raw Data View for REFERENCES'; ?>">
                    	<form id="filterform" name="filterform" method="get">
                        	Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="filter" name="filter" placeholder="Regular Expression..." value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" title="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:170px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;" />&nbsp;&nbsp;<a href="javascript:;" onClick="document.getElementById('filterform').submit();" title="Filter by String or Regular Expression"><div class="icon-search"<?php if ($filter != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php if ($filter != '') { ?>&nbsp;<a href="?orderby=<?php echo $orderby; ?>" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
                            <input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
							<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
						</form>
					</blockquote>
					</div>
					<br />
                    
                    <?php echo $message; ?>
                    
                    <div id="scheduletable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($schedulerrrsetting != 'Hold') { echo 'setInterval(update, ' . $schedulerrrsetting . ');'; $phptimeout = $schedulerrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'schedulerjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey; ?>&phptimeout=<?php echo $phptimeout; ?>&osmver=<?php echo $osversion; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($schedulerrrsetting != 'Hold') { echo $schedulerrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#scheduletable').html(data.scheduletable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							$('#csvexport').html(data.csvexport);
							}
						});
					}
					</script>

					<form id="commandform" name="commandform" method="post">
						<input type="hidden" id="commandtaskname" name="commandtaskname">
						<input type="hidden" id="commandtitletask" name="commandtitletask">
						<input type="hidden" id="commandtype" name="commandtype">
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