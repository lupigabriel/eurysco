<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['processcontrol'] > 0) {  } else { header('location: /'); exit; } ?>

<?php if (!isset($_GET['csv_processes'])) { $_SESSION['processes'] = $_SERVER['REQUEST_URI']; } ?>

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
	function endprocess(IDProcess,Name,PercentProcessorTime,WorkingSetPrivate,CreatingProcessID,LimitName){
		$.ajax({
			type: "GET",
			url: 'processesjqsrv.php?idprocess=' + IDProcess + '&pid=' + CreatingProcessID,
			data: '',
			dataType: 'json',
			cache: false,
			contentType: "application/json; charset=utf-8",
			success: function (data) {
			if (data.Name == '-') { $('#Name').html(data.LimitName); } else { $('#Name').html('<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['servicecontrol'] > 0) { ?><a href="/services.php?filter=' + data.NameURL + '" style="font-size:12px;" title="Filter Services by Service Name"><div class="icon-cog"></div><?php } ?>' + data.LimitName + '<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['servicecontrol'] > 0) { ?></a><?php } ?>'); }
			$('#Name').attr('title', data.Name);
			if (data.ParentName == '-') { $('#ParentName').html(data.ParentName); } else { $('#ParentName').html('<a href="/processes.php?filter=' + data.ParentNameURL + '" style="font-size:12px;" title="Filter by Parent Name"><div class="icon-bars"></div>' + data.ParentName + '</a>'); }
			if (data.FilePath == '') { $('#FileName').html(data.FileName & '&nbsp;&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0">'); } else { $('#FileName').html('<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) { ?><a href="/explorer.php?path=' + data.FilePath + '" style="font-size:12px;" title="Browse Folder"><div class="icon-folder"></div><?php } ?>' + data.FileName + '<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) { ?></a><?php } ?>&nbsp;&nbsp;<img src="img/titlealt.png" width="12" height="10" border="0">'); }
			if (data.FilePath == '-') { $('#FileName').html(data.FileName); }
			$('#FileName').attr('title', data.ExecutablePath);
			$('#UserName').html(data.UserName);
			}
		});
		if (IDProcess == '0') { IDProcessHr = IDProcess; } else { IDProcessHr = '<a href="/processes.php?filter=IDProcess.' + IDProcess + '..IDProcess" style="font-size:12px;" title="Filter by PID"><div class="icon-bars"></div>' + IDProcess + '</a>'; }
		if (CreatingProcessID == '0') { CreatingProcessIDHr = CreatingProcessID; } else { CreatingProcessIDHr = '<a href="/processes.php?filter=CreatingProcessID.' + CreatingProcessID + '..CreatingProcessID" style="font-size:12px;" title="Filter by Parent PID"><div class="icon-bars"></div>' + CreatingProcessID + '</a>'; }
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-bars" style="position:inherit;"></div>&nbsp; Process: <strong>' + LimitName + '</strong></span>',
			'content'     : '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselect"><td style="font-size:12px;">PID:&nbsp;</td><td style="font-size:12px;">' + IDProcessHr + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Name:&nbsp;</td><td style="font-size:12px;" title="' + Name + '">' + LimitName + '</td></tr><tr class="rowselect"><td style="font-size:12px;">CPU Usage:&nbsp;</td><td style="font-size:12px;">' + PercentProcessorTime + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Memory Usage:&nbsp;</td><td style="font-size:12px;">' + WorkingSetPrivate + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Username:&nbsp;</td><td style="font-size:12px;"><div id="UserName" style="font-size:12px;"></div></td></tr><tr class="rowselect"><td style="font-size:12px;">File:&nbsp;</td><td style="font-size:12px;"><div id="FileName" style="font-size:12px;"></div></td></tr><tr class="rowselect"><td style="font-size:12px;">Parent PID:&nbsp;</td><td style="font-size:12px;">' + CreatingProcessIDHr + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Parent Name:&nbsp;</td><td><div id="ParentName" style="font-size:12px;"></div></td></tr><tr class="rowselect"><td style="font-size:12px;">Service:&nbsp;</td><td><div id="Name" style="font-size:12px;"></div></td></tr></table>',
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
				<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['processcontrol'] > 1) { ?>
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
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-processes-button big page-back"></a>
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
					
					if (isset($_GET['csv_processes'])) {
						$_SESSION['csv_processes'] = 'csv_processes';
					} else {
						$_SESSION['csv_processes'] = '';
					}

					$message = '';
					
					if ($endidprocess != '' && $endtypeprocess != '' && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
						if ($endtypeprocess == 1) {
							session_write_close();
							$endprocessoutput = exec('taskkill.exe /f /pid ' . $endidprocess, $errorarray, $errorlevel);
							session_start();
							if ($errorlevel == 0) {
								$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">process <strong>' . $endnameprocess . '</strong> ended</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     process control     process "' . $endnameprocess . '" ended';
							} else {
								$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">process <strong>' . $endnameprocess . '</strong> not ended</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     process control     process "' . $endnameprocess . '" not ended';
							}
						}
						if ($endtypeprocess == 2) {
							session_write_close();
							$endprocessoutput = exec('taskkill.exe /f /pid ' . $endidprocess . ' /t', $errorarray, $errorlevel);
							session_start();
							if ($errorlevel == 0) {
								$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">tree process <strong>' . $endnameprocess . '</strong> ended</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     process control     tree process "' . $endnameprocess . '" ended';
							} else {
								$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">tree process <strong>' . $endnameprocess . '</strong> not ended</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     process control     tree process "' . $endnameprocess . '" not ended';
							}
						}
					}

					if (isset($_GET['page'])) {
						$pgkey = $_GET['page'] - 1;
					} else {
						$pgkey = 0;
					}

					?>
                    
                    <h2>Processes:</h2>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div id="csvexport"></div></td><td width="80%"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($processesrrsetting != 'Hold') { echo number_format(($processesrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $processesrrsetting . '&nbsp;&nbsp;'; } ?><a href="?orderby=<?php echo $orderby; ?>&cpucount=<?php echo $cpucount; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
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
					
                    <div id="processtable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($processesrrsetting != 'Hold') { echo 'setInterval(update, ' . $processesrrsetting . ');'; $phptimeout = $processesrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'processesjq.php?orderby=<?php echo $orderby; ?>&cpucount=<?php echo $cpucount; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey; ?>&phptimeout=<?php echo $phptimeout; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($processesrrsetting != 'Hold') { echo $processesrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#processtable').html(data.processtable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							$('#csvexport').html(data.csvexport);
							}
						});
					}
					</script>

					<form id="endprocess" name="endprocess" method="post">
						<input type="hidden" id="endidprocess" name="endidprocess" />
						<input type="hidden" id="endnameprocess" name="endnameprocess" />
						<input type="hidden" id="endtypeprocess" name="endtypeprocess" />
					</form>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>