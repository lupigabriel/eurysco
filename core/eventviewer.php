<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['eventviewer'] > 0) {  } else { header('location: /'); exit; } ?>

<?php if (!isset($_GET['csv_eventviewer'])) { $_SESSION['eventviewer'] = $_SERVER['REQUEST_URI']; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<script type="text/javascript">
	function eventinfo(TimeGenForm,EventCode,EventIdentifier,Message,RecordNumber,SourceName,ComputerName,Type,User,Logfile,SourceNameT){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-book" style="position:inherit;"></div>&nbsp; ' + Logfile + ' Event ID: <strong>' + EventCode + '</strong></span>',
			'content'     : '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselect"><td style="font-size:12px;" colspan="2" align="center" title="' + SourceNameT + '"><a href="/eventviewer.php?filter=SourceName.' + SourceNameT + '..SourceName" style="font-size:12px;" title="Filter Events by Event Name"><div class="icon-book"></div>' + SourceName + '</a></td></tr><tr class="rowselect"><td style="font-size:12px;">Time Generated:&nbsp;</td><td style="font-size:12px;">' + TimeGenForm + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Type:&nbsp;</td><td style="font-size:12px;">' + Type + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Logfile:&nbsp;</td><td style="font-size:12px;">' + Logfile + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Event ID:&nbsp;</td><td style="font-size:12px;"><a href="/eventviewer.php?filter=EventCode.' + EventCode + '..EventCode" style="font-size:12px;" title="Filter Events by Event ID"><div class="icon-book"></div>' + EventCode + '</a></td></tr></table><textarea readonly="readonly" style="width:100%; font-family:\'Lucida Console\', Monaco, monospace; font-size:11px; height:75px; font-weight:normal;">' + Message + '</textarea>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '55px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 140) + 'px'
			},
			'buttons'     : {
				'Close'     : {
				'action': function(){}
				},
			}
		});
	};
</script>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Event<small>viewer</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-events-button big page-back"></a>
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
						$orderby = 'TimeGenerated';
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
					
					if (isset($_GET['csv_eventviewer'])) {
						$_SESSION['csv_eventviewer'] = 'csv_eventviewer';
					} else {
						$_SESSION['csv_eventviewer'] = '';
					}

					?>
                    
                    <h2>Events:</h2>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div id="csvexport"></div></td><td width="80%"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($eventsrrsetting != 'Hold') { echo number_format(($eventsrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $eventsrrsetting . '&nbsp;&nbsp;'; } ?><a href="?orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
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
                    
                    <div id="eventstable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($eventsrrsetting != 'Hold') { echo 'setInterval(update, ' . $eventsrrsetting . ');'; $phptimeout = $eventsrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'eventviewerjq.php?orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey; ?>&phptimeout=<?php echo $phptimeout; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($eventsrrsetting != 'Hold') { echo $eventsrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#eventstable').html(data.eventstable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
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