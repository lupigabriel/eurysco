<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) {  } else { header('location: /'); exit; } ?>

<?php $_SESSION['textreader'] = htmlspecialchars((string)$_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'); ?>

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

$message = '';
if (isset($_POST['tailoutput']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
	if (mb_detect_encoding($_POST['tailoutput']) == 'ASCII') {
		$origfmd = hash_file('md5', $filetoread);
		session_write_close();
		$fp = @fopen($filetoread, 'w');
		@fwrite($fp, $_POST['tailoutput']);
		@fclose($fp);
		session_start();
		if ($origfmd != hash_file('md5', $filetoread)) {
			$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">file content changed</blockquote><br />';
			$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     text reader     file content in "' . str_replace('\\\\', '\\', $filetoread) . '" changed';
		} else {
			$message = '<blockquote style="font-size:12px; background-color:#DB5400; color:#FFFFFF; border-left-color:#863232;">file content not changed</blockquote><br />';
			$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     text reader     file content in "' . str_replace('\\\\', '\\', $filetoread) . '" not changed';
		}
	} else {
		$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">file not edited... only ASCII characters are allowed</blockquote><br />';
		$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     text reader     file "' . str_replace('\\\\', '\\', $filetoread) . '" not edited... only ASCII characters are allowed';
	}
}

if (isset($_POST['openeditconf']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0) {
	$openeditconf = 'on';
} else {
	$openeditconf = '';
}

if ((isset($_GET["pause"]) || isset($_POST['openeditconf'])) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0) {
	$tailcommand = '&resume';
	if (isset($_POST['openeditconf'])) { $tailbuttval = 'Tail'; } else { $tailbuttval = 'Resume'; }
	$tailbuttcol = '0072C6';
	$tailinterva = '';
} else {
	$tailcommand = '&pause';
	$tailbuttval = 'Pause';
	$tailbuttcol = '603CBA';
	$tailinterva = 'setInterval(update, ' . $tailrrsetting . ');';
}

if (isset($_GET["close"]) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0) {
	unset($_GET["close"]);
	unset($_SESSION['textreader']);
	header('location: /explorer.php?path=' . urlencode($path));
	exit;
}

?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>Text<small>reader</small></h1>
            <a href="tail.php?file=<?php echo urlencode($filetoread); ?>&path=<?php echo urlencode($path); ?>" class="eurysco-textreader-button big page-back"></a>
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

					<h2>Tail:</h2>
					<?php if ($filetoread != '') { ?>
						<blockquote style="font-size:12px;">
							<?php echo strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filetoread, ENT_QUOTES, 'UTF-8'))))); ?><?php if ($download != '' && $path != '' ) { ?><?php if (file_exists($filetoread) && is_readable($filetoread)) { ?><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filetransfer'] > 1) { ?>&nbsp;&nbsp;<a href="download.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&download=<?php echo urlencode($download); ?>&path=<?php echo urlencode($path); ?>" style="font-size:12px;" title="Download"><div class="icon-download-2"></div></a><?php } ?><?php } ?><?php } ?>
						</blockquote>
					<?php } ?>
					<br />
                    <?php echo $message; ?>
                    <div id="tailinfotop"></div>
					<div class="input-control textarea">
					<form name="tailedit" method="post">
						<textarea id="tailoutput"<?php if (!isset($_POST['openeditconf'])) { ?> readonly="readonly"<?php } ?> name="tailoutput" wrap="off" style="width:100%; font-family:'Lucida Console', Monaco, monospace; font-size:12px; height:301px; font-weight:normal;"></textarea>
						<input type="hidden" id="openeditconf" name="openeditconf" />
						<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
					</form>
					<form name="openedit" method="post">
						<input type="hidden" id="openeditconf" name="openeditconf" />
						<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
					</form>
					</div>
					
					<?php
					
					if (!isset($_GET["pause"]) && !isset($_GET["resume"]) && !isset($_POST["tailoutput"]) && !isset($_POST["openeditconf"])) { $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     text reader     read request for file "' . str_replace('\\\\', '\\', $filetoread) . '"'; }
					
					?>

					<script language="javascript" type="text/javascript">
					update();
					<?php echo $tailinterva; ?>
					function update() {
						$.ajax({
							type: "GET",
							url: 'tailjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&file=<?php echo urlencode($filetoread); ?>&path=<?php echo urlencode($path); ?>&openeditconf=<?php echo $openeditconf; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							contentType: "application/json; charset=utf-16",
							success: function (data) {
							$('#tailoutput').html(data.tailoutput);
							$('#tailinfotop').html(data.tailinfotop);
							$('#tailinfobottom').html(data.tailinfobottom);
							<?php if (!isset($_POST['openeditconf'])) { ?>$('#tailoutput').scrollTop($('#tailoutput')[0].scrollHeight);<?php } ?>
							}
						});
					}
					</script>
                    
                    <div id="tailinfobottom"></div>
                    
                    <a href="tail.php?file=<?php echo urlencode($filetoread); ?>&download=<?php echo urlencode($download); ?>&path=<?php echo urlencode($path); ?><?php echo $tailcommand; ?>"><button style="background-color:#<?php echo $tailbuttcol; ?>; color:#FFF;"><?php echo $tailbuttval; ?></button></a><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { ?> <?php if (isset($_POST['openeditconf'])) { ?><a href="javascript:document.tailedit.submit();"><button style="background-color:#000; color:#FFF;">Save</button></a><?php } else { ?><a href="javascript:document.openedit.submit();"><button style="background-color:#888; color:#FFF;">Edit</button></a><?php } ?><?php } ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>