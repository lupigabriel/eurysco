<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Auditors' || $_SESSION['usersett']['auditlog'] > 0) {  } else { header('location: /'); exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'date';
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

$path = $euryscoinstallpath . '\\audit\\';

if (isset($_GET["file"])) {
	$filetoread = $path . $_GET['file'];
	$download = $_GET['file'];
} else {
	$filetoread = '';
	$download = '';
}

$allfiles = scandir($euryscoinstallpath . '\\audit\\');
$auditarray = array();
$auditcounter = 0;
foreach ($allfiles as $name) {
	if (is_file($euryscoinstallpath . '\\audit\\' . $name) && preg_match('/audit-.*.log/', $name)) {
		$auditarray[$auditcounter][0] = $name;
		if (!isset($_GET["file"])) {
			$filetoread = $euryscoinstallpath . '\\audit\\' . $name;
			$download = $name;
		}
		$auditcounter = $auditcounter + 1;
	}
}

rsort($auditarray);
$fileselect = '';
$filecount = 0;
foreach ($auditarray as $auditrow) {
	if ($filecount < 24) {
		if ($auditrow[0] != $download) { $optionselect = ''; } else { $optionselect = ' selected="selected"'; }
		$fileselect = $fileselect . '<option value="' . $auditrow[0] . '"' . $optionselect . '>&nbsp;' . str_replace('-', ' ', str_replace('.log', ' ', preg_replace('/.*_/', '', $auditrow[0]))) . '&nbsp;&nbsp;</option>';
		$filecount = $filecount + 1;
	}
}

if (isset($_GET["pause"]) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0) {
	$tailcommand = '';
	$tailbuttval = 'Resume';
	$tailbuttcol = '0072C6';
	$tailinterva = '';
} else {
	$tailcommand = '&pause';
	$tailbuttval = 'Pause';
	$tailbuttcol = '603CBA';
	$tailinterva = 'setInterval(update, 5000);';
}

?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Audit<small>log</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-auditlogging-button big page-back"></a>
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

					<h2>Auditing:</h2>
					<?php if ($filetoread != '') { ?>
						<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="striped">
							<tr>
								<td width="1%" style="font-size:12px;">
									<form id="auditlogform" name="auditlogform" method="get">
										<select id="file" name="file" onchange="this.form.submit()" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa;">
											<?php echo $fileselect; ?>
										</select>
										<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
									</form>
								</td>
								<td width="99%" style="font-size:12px;">&nbsp;&nbsp;<?php echo strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filetoread, ENT_QUOTES, 'UTF-8'))))); ?><?php if ($download != '' && $path != '' ) { ?>&nbsp;&nbsp;<a href="download.php<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&?download=<?php echo urlencode($download); ?>&path=<?php echo urlencode($path); ?>" title="Download"><div class="icon-download-2"></div></a><?php } ?></td>
							</tr>
						</table>
					<?php } ?>
					<div id="tailinfotop"></div>
					<?php if ($serverstatus == 'run') { ?>
						<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
						<blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR' . "\n" . 'Use Raw Data View for REFERENCES'; ?>">
							<form id="filterform" name="filterform" method="get">
								Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="filter" name="filter" placeholder="Regular Expression..." value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" title="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:170px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;" />&nbsp;&nbsp;<a href="javascript:;" onClick="document.getElementById('filterform').submit();" title="Filter by String or Regular Expression"><div class="icon-search"<?php if ($filter != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php if ($filter != '') { ?>&nbsp;<a href="?orderby=<?php echo $orderby; ?>" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
								<input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
								<?php if (isset($_GET["pause"])) { ?>
								<input type="hidden" id="pause" name="pause" value="" />
								<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
								<?php } ?>
							</form>
						</blockquote>
						</div>
						<br />
						<div id="audittable"></div>
					<?php } else { ?>
						<div class="input-control textarea">
							<textarea id="tailoutput" readonly="readonly" name="tailoutput" wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:301px; font-weight:normal;"></textarea>
						</div>
					<?php } ?>

					<script language="javascript" type="text/javascript">
					update();
					<?php echo $tailinterva; ?>
					function update() {
						$.ajax({
							type: "GET",
							url: 'auditjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey; ?>&file=<?php echo urlencode($filetoread); ?><?php if ($serverstatus == 'run') { echo '&srvrun=' . str_replace('-', ' ', str_replace('.log', ' ', preg_replace('/.*_/', '', str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$download, ENT_QUOTES, 'UTF-8'))))))); } ?><?php if (isset($_GET["pause"])) { echo '&pause'; } ?>',
							data: '',
							dataType: 'json',
							cache: false,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							<?php if ($serverstatus == 'run') { ?>
							$('#audittable').html(data.audittable);
							<?php } else { ?>
							$('#tailoutput').html(data.tailoutput);
							$('#tailoutput').scrollTop($('#tailoutput')[0].scrollHeight);
							<?php } ?>
							$('#tailinfotop').html(data.tailinfotop);
							$('#tailinfobottom').html(data.tailinfobottom);
							}
						});
					}
					</script>
                    
                    <div id="tailinfobottom"></div>
                    
                    <a href="audit.php?orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&page=<?php echo $pgkey + 1; ?>&file=<?php echo urlencode($download); ?><?php echo $tailcommand; ?>"><button style="background-color:#<?php echo $tailbuttcol; ?>; color:#FFF;"><?php echo $tailbuttval; ?></button></a>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>