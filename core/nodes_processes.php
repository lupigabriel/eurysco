<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesprocesscontrol'] > 0) {  } else { header('location: /'); exit; } ?>

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

if (isset($_GET['csv_nodes_processes'])) {
	$_SESSION['csv_nodes_processes'] = 'csv_nodes_processes';
} else {
	$_SESSION['csv_nodes_processes'] = '';
}

$processesrrsetting = 5000;

$nodepath = $euryscoinstallpath . '\\nodes\\' . $node . '\\';

?>

<?php if (!isset($_GET['csv_nodes_processes'])) { $_SESSION['nodes_processes'] = '<a href="' . htmlspecialchars((string)$_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') . '" title="Process Control"><div class="icon-bars"></div>' . $node . '</a>'; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<script type="text/javascript">
	function endprocess(IDProcess,Name,PercentProcessorTime,WorkingSetPrivate,CreatingProcessID,LimitName,UrlName,UserName){
		$.ajax({
			type: "GET",
			url: 'nodes_processesjqsrv.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&idprocess=' + IDProcess + '&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&pid=' + CreatingProcessID,
			data: '',
			dataType: 'json',
			cache: false,
			contentType: "application/json; charset=utf-8",
			success: function (data) {
			if (data.Name == '-') { $('#Name').html(data.LimitName); } else { $('#Name').html('<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesservicecontrol'] > 0) { ?><a href="/nodes_services.php?filter=' + data.NameURL + '&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" style="font-size:12px;" title="Filter Services by Service Name"><div class="icon-cog"></div><?php } ?>' + data.LimitName + '<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesservicecontrol'] > 0) { ?></a><?php } ?>'); }
			$('#Name').attr('title', data.Name);
			if (data.ParentName == '-') { $('#ParentName').html(data.ParentName); } else { $('#ParentName').html('<a href="/nodes_processes.php?filter=' + data.ParentNameURL + '&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" style="font-size:12px;" title="Filter by Parent Name"><div class="icon-bars"></div>' + data.ParentName + '</a>'); }
			if (data.FilePath == '') { $('#FileName').html(data.FileName & '&nbsp;&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0">'); } else { $('#FileName').html(data.FileName + '&nbsp;&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0">'); }
			if (data.FilePath == '-') { $('#FileName').html(data.FileName); }
			$('#FileName').attr('title', data.ExecutablePath);
			$('#UserName').html(data.UserName);
			}
		});
		if (IDProcess == '0') { IDProcessHr = IDProcess; } else { IDProcessHr = '<a href="/nodes_processes.php?filter=IDProcess.' + IDProcess + '..IDProcess&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" style="font-size:12px;" title="Filter by PID"><div class="icon-bars"></div>' + IDProcess + '</a>'; }
		if (CreatingProcessID == '0') { CreatingProcessIDHr = CreatingProcessID; } else { CreatingProcessIDHr = '<a href="/nodes_processes.php?filter=IDProcess.' + CreatingProcessID + '..IDProcess&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" style="font-size:12px;" title="Filter by Parent PID"><div class="icon-bars"></div>' + CreatingProcessID + '</a>'; }
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-bars" style="position:inherit;"></div>&nbsp; Process: <strong>' + LimitName + '</strong></span>',
			'content'     : '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselectsrv"><td width="50%" style="font-size:12px;">PID:&nbsp;</td><td width="50%" style="font-size:12px;">' + IDProcessHr + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Name:&nbsp;</td><td style="font-size:12px;" title="' + Name + '"><a href="/nodes.php?find=' + UrlName + '&findtype=processes" style="font-size:12px;" title="Filter All Nodes"><div class="icon-search"></div>' + LimitName + '</a></td></tr><tr class="rowselectsrv"><td style="font-size:12px;">CPU Usage:&nbsp;</td><td style="font-size:12px;">' + PercentProcessorTime + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Memory Usage:&nbsp;</td><td style="font-size:12px;">' + WorkingSetPrivate + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Username:&nbsp;</td><td style="font-size:12px;">' + UserName + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Parent PID:&nbsp;</td><td style="font-size:12px;">' + CreatingProcessIDHr + '</td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Parent Name:&nbsp;</td><td><div id="ParentName" style="font-size:12px;"></div></td></tr><tr class="rowselectsrv"><td style="font-size:12px;">Service:&nbsp;</td><td><div id="Name" style="font-size:12px;"></div></td></tr></table>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '55px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 156) + 'px'
			},
			'buttons'     : {
				<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['nodesprocesscontrol'] > 1) { ?>
				'End'     : {
				'action': function(){
						document.getElementById("endidprocess").value = IDProcess;
						document.getElementById("endnameprocess").value = Name;
						document.getElementById("endtypeprocess").value = '1';
						document.getElementById("endprocess").submit();
					}
				},
				'End Tree'     : {
				'action': function(){
						document.getElementById("endidprocess").value = IDProcess;
						document.getElementById("endnameprocess").value = Name;
						document.getElementById("endtypeprocess").value = '2';
						document.getElementById("endprocess").submit();
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
			<h1>Process<small>control</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" class="eurysco-processes-button big page-back"></a>
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
						$orderby = 'PercentProcessorTime';
					}

					if (isset($_POST['endidprocess'])) {
						$endidprocess = $_POST['endidprocess'];
					} else {
						$endidprocess = '';
					}

					if (isset($_POST['endnameprocess'])) {
						$endnameprocess = $_POST['endnameprocess'];
					} else {
						$endnameprocess = '';
					}

					if (isset($_POST['endtypeprocess'])) {
						$endtypeprocess = $_POST['endtypeprocess'];
					} else {
						$endtypeprocess = '';
					}
					
					if (isset($_GET['filter'])) {
						$filter = $_GET['filter'];
					} else {
						$filter = '';
					}
					
					$message = '';
					$cid = '';
					$exectimeout = 30000;
					
					if ($endidprocess != '' && $endtypeprocess != '' && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
						$cid = md5(date('r') . $_SESSION['username'] . $node);
						if ($endtypeprocess == 1 && file_exists($euryscoinstallpath . '\\nodes\\' . strtolower($node) . '\\agent.key')) {
							$mcrykeycmd = pack('H*', hash('sha256', fgets(fopen($euryscoinstallpath . '\\nodes\\' . strtolower($node) . '\\agent.key', 'r'))));
							$endprocessoutput = 'taskkill.exe /f /pid ' . $endidprocess;
							$xml = '<exec>' . "\n";
							$xml = $xml . '	<auditok>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $node . '     process control     process "' . $endnameprocess . '" ended (command sent from server "' . $envcomputername . '")')))))) . '</auditok>' . "\n";
							$xml = $xml . '	<auditko>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $node . '     process control     process "' . $endnameprocess . '" not ended (command sent from server "' . $envcomputername . '")')))))) . '</auditko>' . "\n";
							$xml = $xml . '	<auditnl>null</auditnl>' . "\n";
							$xml = $xml . '	<cid>' . $cid . '</cid>' . "\n";
							$xml = $xml . '	<timeout>' . $exectimeout . '</timeout>' . "\n";
							$xml = $xml . '	<command>' . base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykeycmd, $endprocessoutput, MCRYPT_MODE_CBC, $iv)) . '</command>' . "\n";
							$xml = $xml . '</exec>';
							$fp = fopen($nodepath . date('YmdHis') . md5($endidprocess . 'process') . '.exec', 'w');
							fwrite($fp, $xml);
							fclose($fp);
							$fp = fopen($nodepath . 'exec.on', 'w');
							fclose($fp);
							$message = 'process <strong>' . $endnameprocess . '</strong> end command sent to node <strong>' . $node . '</strong>';
		                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     process control     process "' . $endnameprocess . '" end command sent to node "' . $node . '"';
						}
						if ($endtypeprocess == 2 && file_exists($euryscoinstallpath . '\\nodes\\' . strtolower($node) . '\\agent.key')) {
							$mcrykeycmd = pack('H*', hash('sha256', fgets(fopen($euryscoinstallpath . '\\nodes\\' . strtolower($node) . '\\agent.key', 'r'))));
							$endprocessoutput = 'taskkill.exe /f /pid ' . $endidprocess . ' /t';
							$xml = '<exec>' . "\n";
							$xml = $xml . '	<auditok>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $node . '     process control     tree process "' . $endnameprocess . '" ended (command sent from server "' . $envcomputername . '")')))))) . '</auditok>' . "\n";
							$xml = $xml . '	<auditko>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('     ' . $_SESSION['username'] . '     ' . $node . '     process control     tree process "' . $endnameprocess . '" not ended (command sent from server "' . $envcomputername . '")')))))) . '</auditko>' . "\n";
							$xml = $xml . '	<auditnl>null</auditnl>' . "\n";
							$xml = $xml . '	<cid>' . $cid . '</cid>' . "\n";
							$xml = $xml . '	<timeout>' . $exectimeout . '</timeout>' . "\n";
							$xml = $xml . '	<command>' . base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykeycmd, $endprocessoutput, MCRYPT_MODE_CBC, $iv)) . '</command>' . "\n";
							$xml = $xml . '</exec>';
							$fp = fopen($nodepath . date('YmdHis') . md5($endidprocess . 'process') . '.exec', 'w');
							fwrite($fp, $xml);
							fclose($fp);
							$fp = fopen($nodepath . 'exec.on', 'w');
							fclose($fp);
							$message = 'tree process <strong>' . $endnameprocess . '</strong> end command sent to node <strong>' . $node . '</strong>';
		                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     process control     tree process "' . $endnameprocess . '" end command sent to node "' . $node . '"';
						}
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
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><a href="<?php echo $corelink; if (!isset($_SESSION['nodes'])) { echo '/nodes.php'; } else { echo $_SESSION['nodes']; } ?>" style="font-size:12px;" title="Return to Nodes Status"><div class="icon-feed"></div></a>&nbsp;Current Node:</div></td><td style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><?php if ($computerip != 'localhost') { ?><a href="https://<?php echo $computerip; ?>:<?php echo $executorport; ?>/processes.php" style="font-size:13px;" target="_blank" title="Connect to Process Control"><div class="icon-enter"></div></a>&nbsp;<?php } ?><?php echo $node . '.' . $domain; ?></td><?php $navspan = 8; if (file_exists($nodepath . 'nagios.xml.gz')) { ?><?php if (filesize($nodepath . 'nagios.xml.gz') > 40 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnagiosstatus'] > 0)) { $navspan = 9; ?><td width="5%" style="font-size:12px;"><a href="/nodes_nagios.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" style="color:#8063C8; font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Nagios Status">&nbsp;<img src="/img/nagios_normal.png" width="10" height="13" style="vertical-align: middle; margin-left: 2px; margin-right: 6px; margin-bottom: 2px;" title="Nagios Status" /></a></td><?php } ?><?php } ?><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'inventory.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodessysteminventory'] > 0)) { ?>href="/nodes_inventory.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="System Inventory">&nbsp;<div class="icon-box"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'programs.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesinstalledprograms'] > 0)) { ?>href="/nodes_programs.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Installed Programs">&nbsp;<div class="icon-checkmark"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'processes.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesprocesscontrol'] > 0)) { ?>href="/nodes_processes.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="color:#8063C8; font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Process Control">&nbsp;<div class="icon-bars"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'services.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesservicecontrol'] > 0)) { ?>href="/nodes_services.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Service Control">&nbsp;<div class="icon-cog"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'netstat.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesnetworkstats'] > 0)) { ?>href="/nodes_netstat.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Network Stats">&nbsp;<div class="icon-tab"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'scheduler.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesscheduledtasks'] > 0)) { ?>href="/nodes_scheduler.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Scheduled Tasks">&nbsp;<div class="icon-calendar"></div></a></td><td width="5%" style="font-size:12px;"><a <?php if (file_exists($nodepath . 'events.xml.gz') && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodeseventviewer'] > 0)) { ?>href="/nodes_eventviewer.php?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" <?php } ?>style="font-size:12px; font-weight:bold; white-space:nowrap; table-layout:fixed; overflow:hidden;" title="Events Viewer">&nbsp;<div class="icon-book"></div></a></td></tr>
                    	<tr><td width="20%"><div id="csvexport"></div></td><td colspan="<?php echo $navspan; ?>"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td colspan="<?php echo $navspan; ?>"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td colspan="<?php echo $navspan; ?>" style="font-size:12px;"><?php if ($processesrrsetting != 'Hold') { echo number_format(($processesrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $processesrrsetting . '&nbsp;&nbsp;'; } ?><a href="?orderby=<?php echo $orderby; ?>&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
						<?php if ($filter != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Filter:</div></td><td colspan="<?php echo $navspan; ?>" style="font-size:12px;"><i><?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?></i></td></tr><?php } ?>
                    </table>
                    
					<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    <blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR' . "\n" . 'Use Raw Data View for REFERENCES'; ?>">
                    	<form id="filterform" name="filterform" method="get">
                        	Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="filter" name="filter" placeholder="Regular Expression..." value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" title="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:150px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;" />&nbsp;&nbsp;<a href="javascript:;" onClick="document.getElementById('filterform').submit();" title="Filter by String or Regular Expression"><div class="icon-search"<?php if ($filter != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php if ($filter != '') { ?>&nbsp;<a href="/nodes.php?find=<?php echo urlencode($filter); ?>&findtype=processes" title="Filter All Nodes"><div class="icon-reply-2"></div></a>&nbsp;<a href="?orderby=<?php echo $orderby; ?>&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
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

					<div id="message"></div>
					
                    <div id="processtable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($processesrrsetting != 'Hold') { echo 'setInterval(update, ' . $processesrrsetting . ');'; $phptimeout = $processesrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'nodes_processesjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&orderby=<?php echo $orderby; ?>&node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey; ?>&phptimeout=<?php echo $phptimeout; ?>&cid=<?php echo $cid; ?>&message=<?php echo urlencode($message); ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($processesrrsetting != 'Hold') { echo $processesrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#processtable').html(data.processtable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?node=<?php echo $node; ?>&domain=<?php echo $domain; ?>&computerip=<?php echo $computerip; ?>&executorport=<?php echo $executorport; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							$('#lastupdate').html('<?php if ($_SESSION['usersett']['nodesprocesscontrolf'] == '') { ?><a href="/xml.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&export=processes&source=<?php echo $node; ?>" style="font-size:12px;" title="Export Source XML"><div class="icon-file-xml"></div></a><?php } ?>Last&nbsp;Update:&nbsp;' + data.lastupdate);
							$('#csvexport').html(data.csvexport);
							$('#message').html(data.message);
							}
						});
					}
					</script>

					<form id="endprocess" name="endprocess" method="post">
						<input type="hidden" id="endidprocess" name="endidprocess" />
						<input type="hidden" id="endnameprocess" name="endnameprocess" />
						<input type="hidden" id="endtypeprocess" name="endtypeprocess" />
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