<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['installedprograms'] > 0) {  } else { header('location: /'); exit; } ?>

<?php if (!isset($_GET['csv_programs'])) { $_SESSION['installedprograms'] = htmlspecialchars((string)$_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'); } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['installedprograms'] > 1) { ?>
<script type="text/javascript">
	function uninstallprogram(Vendor,Name,Version,InstallDate,IdentifyingNumber,TitleName,LimitVendor,LimitVersion,UninstallName){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-minus" style="position:inherit;"></div>&nbsp; Uninstall: <strong>' + TitleName + '</strong></span>',
			'content'     : '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselect"><td style="font-size:12px;" align="center" title="' + Vendor + '">' + LimitVendor + '</td></tr><tr class="rowselect"><td style="font-size:12px;" align="center" title="' + Name + '">' + UninstallName + '</td></tr><tr class="rowselect"><td style="font-size:12px;" align="center" title="' + Version + '">' + LimitVersion + '</td></tr><tr class="rowselect"><td style="font-size:12px;" align="center" title="' + InstallDate + '">' + InstallDate + '</td></tr><tr class="rowselect"><td style="font-size:10px;" align="center" title="' + IdentifyingNumber + '">' + IdentifyingNumber + '</td></tr></table>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '55px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 145) + 'px'
			},
			'buttons'     : {
				'Uninstall'     : {
				'action': function(){
						document.getElementById("uninstallnameprogram").value = Name;
						document.getElementById("uninstallversprogram").value = Version;
						document.getElementById("uninstallidprogram").value = IdentifyingNumber;
						document.getElementById("uninstallprogramconf").value = '1';
						document.getElementById("uninstallprogram").submit();
					}
				},
				'Close'     : {
				'action': function(){}
				},
			}
		});
	};
</script>
<?php } ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Installed<small>programs</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-programs-button big page-back"></a>
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
						$orderby = 'Name';
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

					if (isset($_POST['uninstallnameprogram'])) {
						$uninstallnameprogram = $_POST['uninstallnameprogram'];
					} else {
						$uninstallnameprogram = '';
					}

					if (isset($_POST['uninstallversprogram'])) {
						$uninstallversprogram = $_POST['uninstallversprogram'];
					} else {
						$uninstallversprogram = '';
					}

					if (isset($_POST['uninstallidprogram'])) {
						$uninstallidprogram = $_POST['uninstallidprogram'];
					} else {
						$uninstallidprogram = '';
					}

					if (isset($_POST['uninstallprogramconf'])) {
						$uninstallprogramconf = $_POST['uninstallprogramconf'];
					} else {
						$uninstallprogramconf = '';
					}

					if (isset($_GET['csv_programs'])) {
						$_SESSION['csv_programs'] = 'csv_programs';
					} else {
						$_SESSION['csv_programs'] = '';
					}

					$message = '';
					
					if ($uninstallidprogram != '' && $uninstallprogramconf != '' && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
						if ($uninstallprogramconf == 1 && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['installedprograms'] > 1)) {
							if ($uninstallversprogram != '-') {
								$uninstallversprogramh = ' version <strong>' . $uninstallversprogram . '</strong>';
								$uninstallversprogramt = ' version "' . $uninstallversprogram . '"';
							} else {
								$uninstallversprogramh = '';
								$uninstallversprogramt = '';
							}
							session_write_close();
							set_time_limit(600);
							$uninstalloutput = exec('msiexec.exe /X{' . $uninstallidprogram . '} /qn', $errorarray, $errorlevel);
							session_start();
							if ($errorlevel == 0) {
								$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">uninstall <strong>' . $uninstallnameprogram . '</strong>' . $uninstallversprogramh . ' completed</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     installed programs     uninstall "' . $uninstallnameprogram . '"' . $uninstallversprogramt . ' completed';
							} else {
								$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">uninstall <strong>' . $uninstallnameprogram . '</strong>' . $uninstallversprogramh . ' error</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     installed programs     uninstall "' . $uninstallnameprogram . '"' . $uninstallversprogramt . ' error';
							}
						}
					}

					?>
                    
                    <h2>Products:</h2>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div id="csvexport"></div></td><td width="80%"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($programsrrsetting != 'Hold') { echo number_format(($programsrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $programsrrsetting . '&nbsp;&nbsp;'; } ?><a href="?orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
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

                    <div id="programtable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($programsrrsetting != 'Hold') { echo 'setInterval(update, ' . $programsrrsetting . ');'; $phptimeout = $programsrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'programsjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey; ?>&phptimeout=120',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: 120000,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#programtable').html(data.programtable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							$('#csvexport').html(data.csvexport);
							}
						});
					}
					</script>

					<form id="uninstallprogram" name="uninstallprogram" method="post">
						<input type="hidden" id="uninstallnameprogram" name="uninstallnameprogram" />
						<input type="hidden" id="uninstallversprogram" name="uninstallversprogram" />
						<input type="hidden" id="uninstallidprogram" name="uninstallidprogram" />
						<input type="hidden" id="uninstallprogramconf" name="uninstallprogramconf" />
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