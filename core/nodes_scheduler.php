<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesscheduledtasks'] > 0) {  } else { header('location: /'); exit; } ?>

<?php

if (isset($_GET['node'])) {
	$node = $_GET['node'];
} else {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (!strpos('#' . $_SESSION['nodelist'] . '#', '#' . $node . '#') || !isset($_SESSION['nodelist'])) {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (isset($_GET['domain'])) {
	$domain = $_GET['domain'];
} else {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (isset($_GET['computerip'])) {
	$computerip = $_GET['computerip'];
} else {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (isset($_GET['executorport'])) {
	$executorport = $_GET['executorport'];
} else {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (isset($_GET['csv_nodes_scheduler'])) {
	$_SESSION['csv_nodes_scheduler'] = 'csv_nodes_scheduler';
} else {
	$_SESSION['csv_nodes_scheduler'] = '';
}

$schedulerrrsetting = 5000;

$nodepath = str_replace('\\core', '\\nodes', $_SERVER['DOCUMENT_ROOT']) . '\\' . $node . '\\';

?>

<?php if (!isset($_GET['csv_nodes_scheduler'])) { $_SESSION['nodes_scheduler'] = '<a href="' . $_SERVER['REQUEST_URI'] . '" title="Scheduled Tasks"><div class="icon-calendar"></div>' . $node . '</a>'; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php

$db = new SQLite3(str_replace('\\core', '\\sqlite', $_SERVER['DOCUMENT_ROOT']) . '\\euryscoServer');
$db->busyTimeout(30000);
$db->query('PRAGMA page_size = 2048; PRAGMA cache_size = 4000; PRAGMA temp_store = 2; PRAGMA journal_mode = OFF; PRAGMA synchronous = 0;');

if (isset($_GET['osversion'])) {
	$osversion = $_GET['osversion'];
} else {
	$osversion = $db->querySingle('SELECT osversion FROM nodesStatus WHERE node = "' . $node . '"');
}

$db->close();

?>

<script type="text/javascript">
	function commandtask(TaskNameShort,ScheduledTaskState,NextRunTimeAlt,LastRunTime,TitleName,ResultCode,RunAsUser,TaskName,Creator,ScheduledType,Status,TaskToRun,LimitName,RunAsUserShort,CreatorShort,UrlTaskName,TaskNameNR){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-calendar" style="position:inherit;"></div>&nbsp; Task: <strong>' + TitleName + '</strong></span>',
			'content'     : '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselectsrv"><td colspan="2" style="font-size:12px;" Title="' + TaskNameNR + ' &raquo; ' + TaskToRun + '"><a href="/nodes.php?find=' + TaskNameNR + '&findtype=scheduler" style="font-size:12px;" title="Filter All Nodes"><div class="icon-search"></div>' + LimitName + '</a>&nbsp;&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0"></td></tr><tr class="rowselectsrv"><td style="font-size:12px;">State:&nbsp;</td><td style="font-size:12px;">' + ScheduledTaskState + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Next Run:&nbsp;</td><td style="font-size:12px;">' + NextRunTimeAlt + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Last Run:&nbsp;</td><td style="font-size:12px;">' + LastRunTime + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Result Code:&nbsp;</td><td style="font-size:12px;">' + ResultCode + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Status:&nbsp;</td><td style="font-size:12px;">' + Status + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Type:&nbsp;</td><td style="font-size:12px;">' + ScheduledType + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Run As User:&nbsp;</td><td style="font-size:12px;" title="' + RunAsUser + '">' + RunAsUserShort + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Creator:&nbsp;</td><td style="font-size:12px;" title="' + Creator + '">' + CreatorShort + '</td></tr></table>',
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
				<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesscheduledtasks'] > 1) { ?>
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
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" class="eurysco-scheduler-button big page-back"></a>
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
					
					$message = '';
					$cid = '';
					$exectimeout = 10000;
					
					if ($commandtaskname != '' && $commandtype != '' && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
						$cid = md5(date('r') . $_SESSION['username'] . $node);
						if ($commandtype == 1) {
							$mcrykeycmd = pack('H*', hash('sha256', md5(strtolower($node))));
							$commandformoutput = 'schtasks.exe /run /tn "' . $commandtaskname . '"';
							$xml = '<exec>' . "\n";
							$xml = $xml . '	<auditok>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $node . '     scheduled tasks     task "' . $commandtitletask . '" started (command sent from server "' . $envcomputername . '")')))))) . '</auditok>' . "\n";
							$xml = $xml . '	<auditko>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $node . '     scheduled tasks     task "' . $commandtitletask . '" not started (command sent from server "' . $envcomputername . '")')))))) . '</auditko>' . "\n";
							$xml = $xml . '	<auditnl>null</auditnl>' . "\n";
							$xml = $xml . '	<cid>' . $cid . '</cid>' . "\n";
							$xml = $xml . '	<timeout>' . $exectimeout . '</timeout>' . "\n";
							$xml = $xml . '	<command>' . base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykeycmd, $commandformoutput, MCRYPT_MODE_CBC, $iv)) . '</command>' . "\n";
							$xml = $xml . '</exec>';
							$fp = fopen($nodepath . date('YmdHis') . md5($commandtaskname . 'process') . '.exec', 'w');
							fwrite($fp, $xml);
							fclose($fp);
							$fp = fopen($nodepath . 'exec.on', 'w');
							fclose($fp);
							$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">task <strong>' . $commandtitletask . '</strong> start command sent to node <strong>' . $node . '</strong></blockquote><br />';
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     scheduled tasks     task "' . $commandtitletask . '" start command sent to node "' . $node . '"';
						}
						if ($commandtype == 2) {
							$mcrykeycmd = pack('H*', hash('sha256', md5(strtolower($node))));
							$commandformoutput = 'schtasks.exe /end /tn "' . $commandtaskname . '"';
							$xml = '<exec>' . "\n";
							$xml = $xml . '	<auditok>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $node . '     scheduled tasks     task "' . $commandtitletask . '" stopped (command sent from server "' . $envcomputername . '")')))))) . '</auditok>' . "\n";
							$xml = $xml . '	<auditko>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $node . '     scheduled tasks     task "' . $commandtitletask . '" not stopped (command sent from server "' . $envcomputername . '")')))))) . '</auditko>' . "\n";
							$xml = $xml . '	<auditnl>null</auditnl>' . "\n";
							$xml = $xml . '	<cid>' . $cid . '</cid>' . "\n";
							$xml = $xml . '	<timeout>' . $exectimeout . '</timeout>' . "\n";
							$xml = $xml . '	<command>' . base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykeycmd, $commandformoutput, MCRYPT_MODE_CBC, $iv)) . '</command>' . "\n";
							$xml = $xml . '</exec>';
							$fp = fopen($nodepath . date('YmdHis') . md5($commandtaskname . 'process') . '.exec', 'w');
							fwrite($fp, $xml);
							fclose($fp);
							$fp = fopen($nodepath . 'exec.on', 'w');
							fclose($fp);
							$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">task <strong>' . $commandtitletask . '</strong> stop command sent to node <strong>' . $node . '</strong></blockquote><br />';
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     scheduled tasks     task "' . $commandtitletask . '" stop command sent to node "' . $node . '"';
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
                    
                    <h2 class="place-left"><?php echo $node; ?>:</h2>
					<div class="place-right" style="font-size:12px;" id="lastupdate"></div>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="<?php echo $corelink; if (!isset($_SESSION['nodes'])) { echo '/nodes.php'; } else { echo $_SESSION['nodes']; } ?>" style="font-size:12px;" title="Return to Nodes Status"><div class="icon-feed"></div></a>&nbsp;Current Node:</div></td><td style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><?php if ($computerip != 'localhost') { ?><a href="https://<?php echo $computerip; ?>:<?php echo $executorport; ?>/scheduler.php" style="font-size:13px;" target="_blank" title="Connect to Scheduled Tasks"><div class="icon-enter"></div></a>&nbsp;<?php } ?><?php echo $node . '.' . $domain; ?></td><?php $navspan = 8; if (file_exists($nodepath . 'nagios.xml.gz')) { ?><?php if (filesize($nodepath . 'nagios.xml.gz') > 40 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnagiosstatus'] > 0)) { $navspan = 9; ?><td width="5%" style="font-size:12px;"><a href="/nodes_nagios.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" style="color:#8063C8; font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Nagios Status">&nbsp;<img src="/img/nagios_normal.png" width="10" height="13" style="vertical-align: middle; margin-left: 2px; margin-right: 6px; margin-bottom: 2px;" title="Nagios Status" /></a></td><?php } ?><?php } ?><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'inventory.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodessysteminventory'] > 0)) { ?>href="/nodes_inventory.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="System Inventory">&nbsp;<div class="icon-box"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'programs.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesinstalledprograms'] > 0)) { ?>href="/nodes_programs.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Installed Programs">&nbsp;<div class="icon-checkmark"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'processes.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesprocesscontrol'] > 0)) { ?>href="/nodes_processes.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Process Control">&nbsp;<div class="icon-bars"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'services.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesservicecontrol'] > 0)) { ?>href="/nodes_services.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Service Control">&nbsp;<div class="icon-cog"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'netstat.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnetworkstats'] > 0)) { ?>href="/nodes_netstat.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Network Stats">&nbsp;<div class="icon-tab"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'scheduler.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesscheduledtasks'] > 0)) { ?>href="/nodes_scheduler.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="color:#8063C8; font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Scheduled Tasks">&nbsp;<div class="icon-calendar"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'events.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodeseventviewer'] > 0)) { ?>href="/nodes_eventviewer.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Events Viewer">&nbsp;<div class="icon-book"></div></a></td></tr>
                    	<tr><td width="20%"><div id="csvexport"></div></td><td colspan="<?php echo $navspan; ?>"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td colspan="<?php echo $navspan; ?>"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td colspan="<?php echo $navspan; ?>" style="font-size:12px;"><?php if ($schedulerrrsetting != 'Hold') { echo number_format(($schedulerrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $schedulerrrsetting . '&nbsp;&nbsp;'; } ?><a href="?orderby=<?php echo $orderby; ?>&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>&osversion=<?php echo $osversion; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
						<?php if ($filter != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Filter:</div></td><td colspan="<?php echo $navspan; ?>" style="font-size:12px;"><i><?php echo $filter; ?></i></td></tr><?php } ?>
                    </table>
                    
					<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    <blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR' . "\n" . 'Use Raw Data View for REFERENCES'; ?>">
                    	<form id="filterform" name="filterform" method="get">
                        	Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="filter" name="filter" placeholder="Regular Expression..." value="<?php echo $filter; ?>" title="<?php echo $filter; ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:150px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;" />&nbsp;&nbsp;<a href="javascript:;" onClick="document.getElementById('filterform').submit();" title="Filter by String or Regular Expression"><div class="icon-search"<?php if ($filter != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php if ($filter != '') { ?>&nbsp;<a href="/nodes.php?find=<?php echo urlencode($filter); ?>&findtype=scheduler" title="Filter All Nodes"><div class="icon-reply-2"></div></a>&nbsp;<a href="?orderby=<?php echo $orderby; ?>&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>&osversion=<?php echo $osversion; ?>" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
							<input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
							<input type="hidden" id="node" name="node" value="<?php echo $node; ?>" />
							<input type="hidden" id="domain" name="domain" value="<?php echo $domain; ?>" />
							<input type="hidden" id="osversion" name="osversion" value="<?php echo $osversion; ?>" />
							<input type="hidden" id="computerip" name="computerip" value="<?php echo $computerip; ?>" />
							<input type="hidden" id="executorport" name="executorport" value="<?php echo $executorport; ?>" />
						</form>
					</blockquote>
					</div>
					<br />
                    
					<div id="message"></div>
                    
                    <div id="scheduletable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($schedulerrrsetting != 'Hold') { echo 'setInterval(update, ' . $schedulerrrsetting . ');'; $phptimeout = $schedulerrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'nodes_schedulerjq.php?orderby=<?php echo $orderby; ?>&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey; ?>&phptimeout=<?php echo $phptimeout; ?>&osmver=<?php echo substr($osversion, 0, 1); ?>&cid=<?php echo $cid; ?>&message=<?php echo urlencode($message); ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($schedulerrrsetting != 'Hold') { echo $schedulerrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#scheduletable').html(data.scheduletable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>&osversion=<?php echo $osversion; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							$('#lastupdate').html('<?php if ($_SESSION['usersett']['nodesscheduledtasksf'] == '') { ?><a href="/xml.php?export=scheduler&source=<?php echo $node; ?>" style="font-size:12px;" title="Export Source XML"><div class="icon-file-xml"></div></a><?php } ?>Last&nbsp;Update:&nbsp;' + data.lastupdate);
							$('#csvexport').html(data.csvexport);
							$('#message').html(data.message);
							}
						});
					}
					</script>

					<form id="commandform" name="commandform" method="post">
						<input type="hidden" id="commandtaskname" name="commandtaskname">
						<input type="hidden" id="commandtitletask" name="commandtitletask">
						<input type="hidden" id="commandtype" name="commandtype">
					</form>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>