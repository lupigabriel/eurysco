<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnagiosstatus'] > 0) {  } else { header('location: /'); exit; } ?>

<?php

if (isset($_GET['node'])) {
	$node = $_GET['node'];
} else {
	header('location: ' . $corelink . '/nodes.php');
	exit;
}

if (isset($_SESSION['nodelist'])) {
	if (!strpos('#' . $_SESSION['nodelist'] . '#', '#' . $node . '#')) {
		header('location: ' . $corelink . '/nodes.php');
		exit;
	}
} else {
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

if (isset($_GET['csv_nodes_nagios'])) {
	$_SESSION['csv_nodes_nagios'] = 'csv_nodes_nagios';
} else {
	$_SESSION['csv_nodes_nagios'] = '';
}

$nagiosrrsetting = 5000;

$nodepath = $euryscoinstallpath . '\\nodes\\' . $node . '\\';

?>

<?php if (!isset($_GET['csv_nodes_nagios'])) { $_SESSION['nodes_nagios'] = '<a href="' . htmlspecialchars((string)$_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') . '" title="Nagios Status"><img src="/img/nagios_icon.png" width="20" height="16" style="vertical-align: middle;" /> ' . $node . '</a>'; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Nagios<small>status</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" class="eurysco-nagios-button big page-back"></a>
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
						$orderby = 'NagiosCommandExit';
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
                    
                    <h2 class="place-left"><?php echo $node; ?>:</h2>
					<div class="place-right" style="font-size:12px;" id="lastupdate"></div>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="<?php echo $corelink; if (!isset($_SESSION['nodes'])) { echo '/nodes.php'; } else { echo $_SESSION['nodes']; } ?>" style="font-size:12px;" title="Return to Nodes Status"><div class="icon-feed"></div></a>&nbsp;Current Node:</div></td><td style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><?php if ($computerip != 'localhost') { ?><a href="https://<?php echo $computerip; ?>:<?php echo $executorport; ?>/nagios.php" style="font-size:13px;" target="_blank" title="Connect to Nagios Status"><div class="icon-enter"></div></a>&nbsp;<?php } ?><?php echo $node . '.' . $domain; ?></td><?php $navspan = 8; if (file_exists($nodepath . 'nagios.xml.gz')) { ?><?php if (filesize($nodepath . 'nagios.xml.gz') > 40 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnagiosstatus'] > 0)) { $navspan = 9; ?><td width="5%" style="font-size:12px;"><a href="/nodes_nagios.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" style="color:#8063C8; font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Nagios Status">&nbsp;<img src="/img/nagios_section.png" width="10" height="13" style="vertical-align: middle; margin-left: 2px; margin-right: 6px; margin-bottom: 2px;" title="Nagios Status" /></a></td><?php } ?><?php } ?><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'inventory.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodessysteminventory'] > 0)) { ?>href="/nodes_inventory.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="System Inventory">&nbsp;<div class="icon-box"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'programs.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesinstalledprograms'] > 0)) { ?>href="/nodes_programs.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Installed Programs">&nbsp;<div class="icon-checkmark"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'processes.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesprocesscontrol'] > 0)) { ?>href="/nodes_processes.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Process Control">&nbsp;<div class="icon-bars"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'services.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesservicecontrol'] > 0)) { ?>href="/nodes_services.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Service Control">&nbsp;<div class="icon-cog"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'netstat.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnetworkstats'] > 0)) { ?>href="/nodes_netstat.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Network Stats">&nbsp;<div class="icon-tab"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'scheduler.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesscheduledtasks'] > 0)) { ?>href="/nodes_scheduler.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Scheduled Tasks">&nbsp;<div class="icon-calendar"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'events.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodeseventviewer'] > 0)) { ?>href="/nodes_eventviewer.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Events Viewer">&nbsp;<div class="icon-book"></div></a></td></tr>
                    	<tr><td width="20%"><div id="csvexport"></div></td><td colspan="<?php echo $navspan; ?>"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td colspan="<?php echo $navspan; ?>"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td colspan="<?php echo $navspan; ?>" style="font-size:12px;"><?php if ($nagiosrrsetting != 'Hold') { echo number_format(($nagiosrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $nagiosrrsetting . '&nbsp;&nbsp;'; } ?><a href="?orderby=<?php echo $orderby; ?>&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
						<?php if ($filter != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Filter:</div></td><td colspan="<?php echo $navspan; ?>" style="font-size:12px;"><i><?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?></i></td></tr><?php } ?>
                    </table>
                    
					<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    <blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR' . "\n" . 'Use Raw Data View for REFERENCES'; ?>">
                    	<form id="filterform" name="filterform" method="get">
                        	Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="filter" name="filter" placeholder="Regular Expression..." value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" title="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:150px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;" />&nbsp;&nbsp;<a href="javascript:;" onClick="document.getElementById('filterform').submit();" title="Filter by String or Regular Expression"><div class="icon-search"<?php if ($filter != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php if ($filter != '') { ?>&nbsp;<a href="/nodes.php?find=<?php echo urlencode($filter); ?>&findtype=nagios" title="Filter All Nodes"><div class="icon-reply-2"></div></a>&nbsp;<a href="?orderby=<?php echo $orderby; ?>&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
                            <input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
							<input type="hidden" id="node" name="node" value="<?php echo $node; ?>" />
							<input type="hidden" id="domain" name="domain" value="<?php echo $domain; ?>" />
							<input type="hidden" id="computerip" name="computerip" value="<?php echo $computerip; ?>" />
							<input type="hidden" id="executorport" name="executorport" value="<?php echo $executorport; ?>" />
							<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
						</form>
					</blockquote>
					</div>
					<br />
                    
                    <div id="nagiostable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($nagiosrrsetting != 'Hold') { echo 'setInterval(update, ' . $nagiosrrsetting . ');'; $phptimeout = $nagiosrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'nodes_nagiosjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&orderby=<?php echo $orderby; ?>&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey; ?>&phptimeout=<?php echo $phptimeout; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($nagiosrrsetting != 'Hold') { echo $nagiosrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#nagiostable').html(data.nagiostable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							$('#lastupdate').html('<?php if ($_SESSION['usersett']['nodesnagiosstatusf'] == '') { ?><a href="/xml.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&export=nagios&source=<?php echo $node; ?>" style="font-size:12px;" title="Export Source XML"><div class="icon-file-xml"></div></a><?php } ?>Last&nbsp;Update:&nbsp;' + data.lastupdate);
							$('#csvexport').html(data.csvexport);
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