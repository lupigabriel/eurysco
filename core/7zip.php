<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) {  } else { header('location: /'); exit; } ?>

<?php $_SESSION['zipextract'] = $_SERVER['REQUEST_URI']; ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php

if (isset($_GET["file"])) {
	$filetoread = $_GET["file"];
} else {
	$filetoread = '';
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
	$extractfolder = '';
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

?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { ?>
<script type="text/javascript">
	function extract(){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-file-zip" style="position:inherit;"></div>&nbsp; Extract <strong><?php if (strlen($name) > 15) { echo str_replace('\'', '\\\'', substr($name, 0, 15)) . '&nbsp;[...]'; } else { echo str_replace('\'', '\\\'', $name); }; ?></strong></span>',
			'content'     : '<form id="extract" name="extract" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="35%" style="font-size:12px;">Folder:&nbsp;</td><td width="65%" style="font-size:10px;"><input type="text" id="extractfolder" name="extractfolder" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:135px; padding-left:4px; padding-right:4px; font-size:12px;" value="<?php echo str_replace('\'', '\\\'', $name); ?>.extract"></td></tr><tr><td width="35%" style="font-size:12px;">Password:&nbsp;</td><td width="65%" style="font-size:10px;"><input type="password" id="extractpass" name="extractpass" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:135px; padding-left:4px; padding-right:4px; font-size:12px;" value=""></td></tr></table><input type="hidden" id="zipextractconf" name="zipextractconf" value="zipextractconf" /></form>',
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
						document.getElementById("extract").submit();
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
							<?php echo strtolower($filetoread); ?><?php if ($download != '' && $path != '' ) { ?><?php if (file_exists($filetoread) && is_readable($filetoread)) { ?>&nbsp;&nbsp;<a href="download.php?download=<?php echo urlencode($download); ?>&path=<?php echo urlencode($path); ?>" style="font-size:12px;" title="Download"><div class="icon-download-2"></div></a><?php } ?><?php } ?>
						</blockquote>
					<?php } ?>
                    <br />
                    <div id="zipinfotop"></div>
					<div class="input-control textarea">
						<textarea id="zipoutput" readonly="readonly" name="zipoutput" wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:301px; font-weight:normal;"></textarea>
					</div>
					
					<?php
					if (isset($_POST['zipextractconf']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
						$extract = 1;
					} else {
						$extract = 0;
					}
					?>

					<script language="javascript" type="text/javascript">
					update();
					function update() {
						$.ajax({
							type: "GET",
							url: '7zipjq.php?file=<?php echo urlencode($filetoread); ?>&extract=<?php echo ($extract); ?>&path=<?php echo urlencode($path); ?>&extractfolder=<?php echo urlencode($extractfolder); ?>&extractpass=<?php echo urlencode($extractpass); ?>',
							data: '',
							dataType: 'json',
							cache: false,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#zipoutput').html(data.zipoutput);
							$('#zipinfotop').html(data.zipinfotop);
							$('#zipinfobottom').html(data.zipinfobottom);
							$('#zipoutput').scrollTop($('#zipoutput')[0].scrollHeight);
							}
						});
					}
					</script>
                    
                    <div id="zipinfobottom"></div>
                    
					<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { ?>
					<?php if (file_exists($filetoread)) { ?>
					<a href="javascript:extract()"><button style="background-color:#603CBA; color:#FFF;">Extract</button></a>
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