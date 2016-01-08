<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) {  } else { header('location: /'); exit; } ?>

<?php $_SESSION['zipextract'] = htmlspecialchars((string)$_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'); ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php

if (isset($_GET["file"])) {
	$filetoread = $_GET['file'];
} else {
	unset($_SESSION['zipextract']);
	header('location: /'); exit;
}

if (isset($_GET['path'])) {
	$path = $_GET['path'];
} else {
	$path = '';
}

if (isset($_GET['download'])) {
	$download = $_GET['download'];
} else {
	$download = '';
}

if (isset($_GET['name'])) {
	$name = $_GET['name'];
} else {
	$name = '';
}

if (isset($_POST['extractfolder'])) {
	$extractfolder = $_POST['extractfolder'];
} else {
	$extractfolder = $name . '.extract';
}

if (isset($_POST['extractpass'])) {
	$extractpass = $_POST['extractpass'];
} else {
	$extractpass = '';
}

if (isset($_GET["close"])) {
	unset($_GET["close"]);
	unset($_SESSION['zipextract']);
	header('location: /explorer.php?path=' . urlencode($path));
	exit;
}

if (!isset($_SESSION['7zip_' . md5($filetoread)])) {
	$_SESSION['7zip_' . md5($filetoread)] = 0;
}

?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { ?>
<script type="text/javascript">
	function extract(){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-file-zip" style="position:inherit;"></div>&nbsp; Extract <strong><?php if (strlen($name) > 15) { echo str_replace('\'', '\\\'', substr($name, 0, 15)) . '&nbsp;[...]'; } else { echo str_replace('\'', '\\\'', $name); }; ?></strong></span>',
			'content'     : '<span style="font-size:12px;">Please Confirm Extraction</span>',
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
				'Extract'     : {
				'action': function(){
						document.getElementById("extract").value = '1';
						document.getElementById("listextract").submit();
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
			<h1>7zip<small>extractor</small></h1>
            <a href="7zip.php?file=<?php echo urlencode($filetoread); ?>&path=<?php echo urlencode($path); ?>" class="eurysco-zipextract-button big page-back"></a>
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

					<h2>Archive:</h2>
					<?php if ($filetoread != '') { ?>
						<blockquote style="font-size:12px;">
							<?php echo strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filetoread, ENT_QUOTES, 'UTF-8'))))); ?><?php if ($download != '' && $path != '' ) { ?><?php if (file_exists($filetoread) && is_readable($filetoread)) { ?><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filetransfer'] > 1) { ?>&nbsp;&nbsp;<a href="download.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&download=<?php echo urlencode($download); ?>&path=<?php echo urlencode($path); ?>" style="font-size:12px;" title="Download"><div class="icon-download-2"></div></a><?php } ?><?php } ?><?php } ?>
						</blockquote>
					<?php } ?>
                    <br />
                    <div id="zipinfotop"></div>
					<div class="input-control textarea">
						<textarea id="zipoutput" readonly="readonly" name="zipoutput" wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:301px; font-weight:normal;"></textarea>
					</div>
					
					<?php
					$extract = 0;
					if (isset($_POST['extract']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
						if ($_POST['extract'] == 1) {
							$extract = 1;
						}
					}
					?>

					<script language="javascript" type="text/javascript">
					update();
					function update() {
						$.ajax({
							type: "GET",
							url: '7zipjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&file=<?php echo urlencode($filetoread); ?>&extract=<?php echo $extract; ?>&path=<?php echo urlencode($path); ?>&extractfolder=<?php echo urlencode($extractfolder); ?>&extractpass=<?php echo urlencode($extractpass); ?>&lock=<?php echo urlencode(md5($filetoread)); ?>',
							data: '',
							dataType: 'json',
							cache: false,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#zipoutput').html(data.zipoutput);
							if (data.ziplockstatus > 2) {
								$('#zipinfotop').html('<form id="listextract" name="listextract" method="post"><input type="hidden" id="extract" name="extract" /><table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">' + data.zipinfotop + '<tr><td width="30%" style="font-size:12px;">Extract Folder:</td><td width="70%" style="font-size:12px;"><input type="text" id="extractfolder" name="extractfolder" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:200px; padding-left:4px; padding-right:4px; font-size:12px;" value="<?php echo str_replace('\'', '\\\'', strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$extractfolder, ENT_QUOTES, 'UTF-8')))))); ?>" /></td></tr><tr><td width="30%" style="font-size:12px;">Password:</td><td width="70%" style="font-size:12px;"><i>Locked by Current Session</i><input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" /></td></tr></table></form>');
							} else {
								$('#zipinfotop').html('<form id="listextract" name="listextract" method="post"><input type="hidden" id="extract" name="extract" /><table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">' + data.zipinfotop + '<tr><td width="30%" style="font-size:12px;">Extract Folder:</td><td width="70%" style="font-size:12px;"><input type="text" id="extractfolder" name="extractfolder" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:200px; padding-left:4px; padding-right:4px; font-size:12px;" value="<?php echo str_replace('\'', '\\\'', strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$extractfolder, ENT_QUOTES, 'UTF-8')))))); ?>" /></td></tr><tr><td width="30%" style="font-size:12px;">Password:</td><td width="70%" style="font-size:12px;"><input type="password" autocomplete="off" id="extractpass" name="extractpass" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:200px; padding-left:4px; padding-right:4px; font-size:12px;" value="<?php echo $extractpass; ?>" /></td></tr></table><input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" /></form>');
							}
							$('#zipinfobottom').html(data.zipinfobottom);
							$('#zipoutput').scrollTop($('#zipoutput')[0].scrollHeight);
							}
						});
					}
					</script>
                    
                    <div id="zipinfobottom"></div>
                    
					<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { ?>
					<?php if (file_exists($filetoread)) { ?>
					<a href="#" onclick="document.listextract.submit();"><button style="background-color:#603CBA; color:#FFF;">List</button></a>&nbsp;<a href="javascript:extract()"><button style="background-color:#888; color:#FFF;">Extract</button></a>
                    <?php } ?>
					<?php } ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>