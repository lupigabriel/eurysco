<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['wmiexplorer'] > 0) {  } else { header('location: /'); exit; } ?>

<?php $_SESSION['wmiexplorer'] = htmlspecialchars((string)$_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'); ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>WMI<small>explorer</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-wmi-button big page-back"></a>
		</div>
	</div>
</div>

<div class="page" id="page-index">
	<div class="page-region">
		<div class="page-region-content">
			<div class="grid">
				<div class="row">
		            <div class="span1"></div>
		            <div class="span10">

					<?php

					if (isset($_GET['filter'])) {
						$filter = $_GET['filter'];
					} else {
						$filter = '';
					}
					
					if (isset($_GET['lastfilter'])) {
						$lastfilter = $_GET['lastfilter'];
					} else {
						$lastfilter = '';
					}

					if (isset($_GET['wminamespace'])) {
						$wminamespace = $_GET['wminamespace'];
					} else {
						$wminamespace = '';
					}

					if (isset($_GET['wmiclasses'])) {
						$wmiclasses = $_GET['wmiclasses'];
					} else {
						$wmiclasses = '';
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
                    
                    <h2>Browse Instrumentation:</h2>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Current Path:</div></td><td width="80%"><?php
                        if ($wminamespace == '' && $wmiclasses == '') {
							echo '<div style="font-size:12px;">root</div>';
						}
                        if ($wminamespace != '' && $wmiclasses == '') {
							echo '<div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px;">' . $wminamespace . '&nbsp;&nbsp;<a href="wmiexplorer.php?filter=' . strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$lastfilter, ENT_QUOTES, 'UTF-8'))))) . '" title="Return to Root WMI"><div class="icon-reply" style="margin-top:1px;"></div></a></div>';
						}
                        if ($wminamespace != '' && $wmiclasses != '') {
							echo '<div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px;">' . $wminamespace . '/' . $wmiclasses . '&nbsp;&nbsp;<a href="?wminamespace=' . urlencode($wminamespace) . '&filter=' . strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$lastfilter, ENT_QUOTES, 'UTF-8'))))) . '" title="Return to ' . $wminamespace . ' Namespace"><div class="icon-reply" style="margin-top:1px;"></div></a></div>';
						}
						?></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Total Elements:</div></td><td width="80%"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($wmiexprrsetting != 'Hold') { echo number_format(($wmiexprrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $wmiexprrsetting . '&nbsp;&nbsp;'; } ?><a href="?wminamespace=<?php echo urlencode($wminamespace); ?>&wmiclasses=<?php echo urlencode($wmiclasses); ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>&lastfilter=<?php echo strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$lastfilter, ENT_QUOTES, 'UTF-8'))))); ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
						<?php if ($filter != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Filter:</div></td><td width="80%" style="font-size:12px;"><i><?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?></i></td></tr><?php } ?>
                    </table>
                    
					<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    <blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR'; ?>">
                    	<form id="filterform" name="filterform" method="get">
                        	Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="filter" name="filter" placeholder="Regular Expression..." value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" title="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:170px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;" />&nbsp;&nbsp;<a href="javascript:;" onClick="document.getElementById('filterform').submit();" title="Filter by String or Regular Expression"><div class="icon-search"<?php if ($filter != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php if ($filter != '') { ?>&nbsp;<a href="?wminamespace=<?php echo urlencode($wminamespace); ?>&wmiclasses=<?php echo urlencode($wmiclasses); ?>" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
                            <input type="hidden" id="wminamespace" name="wminamespace" value="<?php echo $wminamespace; ?>" />
                            <input type="hidden" id="wmiclasses" name="wmiclasses" value="<?php echo $wmiclasses; ?>" />
							<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
						</form>
					</blockquote>
					</div>
					<br />
                    
                    <div id="wmiexptable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($wmiexprrsetting != 'Hold') { echo 'setInterval(update, ' . $wmiexprrsetting . ');'; $phptimeout = $wmiexprrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'wmiexplorerjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&wminamespace=<?php echo urlencode($wminamespace); ?>&wmiclasses=<?php echo urlencode($wmiclasses); ?>&filter=<?php echo urlencode($filter); ?>&lastfilter=<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$lastfilter, ENT_QUOTES, 'UTF-8')))); ?>&page=<?php echo $pgkey; ?>&phptimeout=<?php echo $phptimeout; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($wmiexprrsetting != 'Hold') { echo $wmiexprrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#wmiexptable').html(data.wmiexptable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
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