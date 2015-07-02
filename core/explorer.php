<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) {  } else { header('location: /'); exit; } ?>

<?php $_SESSION['explorer'] = $_SERVER['REQUEST_URI']; ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php

if (isset($_POST['openclidrive']) && isset($_POST['openclipath'])) {
	$_SESSION['cldrive'] = strtolower($_POST['openclidrive']);
	$_SESSION['cldrive_old'] = strtolower($_POST['openclidrive']);
	$_SESSION['clpath'] = $_POST['openclipath'];
	header('location: /cli.php');
}

if (isset($_GET['path'])) {
	$path = $_GET['path'];
} else {
	$path = 'C:\\';
}

if (isset($_POST['permpath'])) {
	$_SESSION['permpath'] = $_POST['permpath'];
}

if (isset($_SESSION['permpath'])) {
	if (!strpos('|' . $path, $_SESSION['permpath'])) {
		$path = $_SESSION['permpath'];
	}
}

$filebrowserblcount = 0;
if ($_SESSION['usersett']['filebrowserf'] != '') {
	$filebrowserblarray = array();
	$filebrowserblarray = (explode(',', preg_replace('/\r\n|\r|\n/', ',', $_SESSION['usersett']['filebrowserf'])));
	$filebrowserbllist = '';
	foreach ($filebrowserblarray as $filebrowserbl) {
		$filebrowserbl = rtrim($filebrowserbl, '\\') . '\\';
		if (is_dir($filebrowserbl) && !is_file($filebrowserbl)) {
			if (!isset($_SESSION['permpath']) && $filebrowserblcount == 0) { $_SESSION['permpath'] = $filebrowserbl; $path = $_SESSION['permpath']; }
			if (strpos('|' . $path, $filebrowserbl) > 0) { $filebrowserblsel = 'selected'; } else { $filebrowserblsel = ''; }
			$filebrowserbllist = $filebrowserbllist . '<option value="' . $filebrowserbl . '" ' . $filebrowserblsel . '>' . rtrim($filebrowserbl, '\\') . '&nbsp;&nbsp;&nbsp;</option>';
			$filebrowserblcount = $filebrowserblcount + 1;
		}
	}
}

?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { ?>
<script type="text/javascript">
	function exploreract(Name,ModDate,CrtDate,AccDate,Perm,PathFile,DetailName,LongName,OldName,NameEN,ObjType){
		$.ajax({
			type: "GET",
			url: 'explorerjqmime.php?pathfile=<?php echo urlencode(str_replace('\\\\', '\\', $path)); ?>' + NameEN,
			data: '',
			dataType: 'json',
			cache: false,
			contentType: "application/json; charset=utf-8",
			success: function (data) {
			$('#finfo_mime_type').html('<td style="font-size:12px;">Mime Type:&nbsp;</td><td style="font-size:12px;" title="' + data.title_mime_type + '">' + data.finfo_mime_type + '</td>');
			$('#finfo_symlink').html('<td style="font-size:12px;">Symlink:&nbsp;</td><td style="font-size:12px;" title="' + data.title_symlink + '">' + data.finfo_symlink + '</td>');
			$('#finfo_mime_encoding').html('<td style="font-size:12px;">Mime Encoding:&nbsp;</td><td style="font-size:12px;" title="' + data.title_mime_encoding + '">' + data.finfo_mime_encoding + '</td>');
			}
		});
		ObjTypeCopy = '';
		if (ObjType == 'File') { ObjTypeCopy = '<option value="Copy">&nbsp;COPY&nbsp;&nbsp;</option>'; }
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-folder" style="position:inherit;"></div>&nbsp; ' + ObjType + ': <strong>' + DetailName + '</strong></span>',
			'content'     : '<form id="updateform" name="updateform" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselect"><td width="50%" style="font-size:12px;">Name:&nbsp;</td><td width="50%" style="font-size:10px;"><input type="text" id="newname" name="newname" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:118px; padding-left:4px; padding-right:4px; font-size:12px;" value="' + Name + '"></td></tr><tr class="rowselect"><td style="font-size:12px;">Created:&nbsp;</td><td style="font-size:12px;">' + CrtDate + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Modified:&nbsp;</td><td style="font-size:12px;">' + ModDate + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Accessed:&nbsp;</td><td style="font-size:12px;">' + AccDate + '</td></tr><tr class="rowselect"><td style="font-size:12px;">Confirm&nbsp;Operation:&nbsp;</td><td style="font-size:10px;"><select id="confirmoperation" name="confirmoperation" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:118px;"><option value="nothing">&nbsp;-&nbsp;</option>' + ObjTypeCopy + '<option value="Move">&nbsp;MOVE&nbsp;&nbsp;</option><option value="delete">&nbsp;DELETE&nbsp;&nbsp;</option></select><input type="hidden" id="pathfile" name="pathfile" value="' + PathFile + '"><input type="hidden" id="oldname" name="oldname"></td></tr><tr class="rowselect" id="finfo_mime_type"></tr><tr class="rowselect" id="finfo_symlink"></tr><tr class="rowselect" id="finfo_mime_encoding"></tr></table></form>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '55px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 150) + 'px'
			},
			'buttons'     : {
				'Update'     : {
				'action': function(){
						document.getElementById("oldname").value = OldName;
						document.getElementById("updateform").submit();
					}
				},
				'Close'     : {
				'action': function(){}
				},
			}
		});
	};
	
	function exploreradd(){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-folder" style="position:inherit;"></div>&nbsp; Add New <strong>Folder</strong> or <strong>File</strong></span>',
			'content'     : '<form id="expAdd" name="expAdd" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselect"><td width="50%" style="font-size:12px;">Name:&nbsp;</td><td width="50%" style="font-size:10px;"><input type="text" id="expAddName" name="expAddName" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px; padding-left:4px; padding-right:4px; font-size:12px;" value="New Entry"></td></tr><tr class="rowselect"><td width="50%" style="font-size:12px;">Type:&nbsp;</td><td width="50%" style="font-size:10px;"><select id="expAddType" name="expAddType" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px;"><option value="Folder">&nbsp;FOLDER&nbsp;&nbsp;</option><option value="File">&nbsp;FILE&nbsp;&nbsp;</option></select></td></tr></table><textarea id="expAddValue" name="expAddValue" style="width:250px; font-family:\'Lucida Console\', Monaco, monospace; font-size:11px; height:75px; font-weight:normal;"></textarea></form>',
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
				'Add'     : {
				'action': function(){
						document.getElementById("expAdd").submit();
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
			<h1>File<small>browser</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-explorer-button big page-back"></a>
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
					
					if (isset($_GET['page'])) {
						$pgkey = $_GET['page'] - 1;
					} else {
						$pgkey = 0;
					}

					if (isset($_GET['lastpath'])) {
						$lastpath = $_GET['lastpath'];
					} else {
						$lastpath = '-';
					}

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

					if (strlen($path) < 4) {
						$currentpath = str_replace('\\\\', '\\', $path);
					} else {
						$currentpath = substr(str_replace('\\\\', '\\', $path), 0, -1);
					}
					$cpath = str_replace('\\\\', '\\', $path);
					
					if (strlen($lastpath) < 4) {
						$lastpath = str_replace('\\\\', '\\', $lastpath);
					} else {
						$lastpath = substr(str_replace('\\\\', '\\', $lastpath), 0, -1);
					}
					
					if (!file_exists(str_replace('\\\\', '\\', $path)) || !is_dir(str_replace('\\\\', '\\', $path)) || strpos('|' . str_replace('\\\\', '\\', $path), str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT'])) > 0) {
						header('location: ' . $_SERVER['PHP_SELF']);
						exit;
					}
					
					if (isset($_SESSION['explorermocooper']) && isset($_SESSION['explorermocopath']) && isset($_SESSION['explorermoconame'])) {
						if (isset($_POST['mococancelconf']) || !file_exists($_SESSION['explorermocopath'] . '\\' . $_SESSION['explorermoconame'])) {
							unset($_SESSION['explorermocooper']);
							unset($_SESSION['explorermocopath']);
							unset($_SESSION['explorermoconame']);
							unset($_POST['mococancelconf']);
							header('location: ' . $_SERVER['REQUEST_URI']);
						}
					}
					
					$message = '';
					
					if (isset($_POST['mocooperconf']) && isset($_SESSION['explorermocooper']) && isset($_SESSION['explorermocopath']) && isset($_SESSION['explorermoconame']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
						if (is_dir($_SESSION['explorermocopath'] . '\\' . $_SESSION['explorermoconame'])) {
							$mocotype = 'Folder';
						} else {
							$mocotype = 'File';
						}
						session_write_close();
						$mocooperoutput = exec(strtolower($_SESSION['explorermocooper']) . ' /y "' . $_SESSION['explorermocopath'] . '\\' . $_SESSION['explorermoconame'] . '" "' . $currentpath . '"', $errorarray, $errorlevel);
						session_start();
						if ($errorlevel == 0) {
							$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">' . $_SESSION['explorermocooper'] . ' ' . $mocotype . ' <strong>' . $_SESSION['explorermoconame'] . '</strong> in <strong>' . $currentpath . '</strong> completed</blockquote><br />';
		                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     ' . $_SESSION['explorermocooper'] . ' ' . $mocotype . ' "' . str_replace('\\\\', '\\', $_SESSION['explorermocopath'] . '\\' . $_SESSION['explorermoconame']) . '" in "' . $currentpath . '" completed';
						} else {
							$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">' . $_SESSION['explorermocooper'] . ' ' . $mocotype . ' <strong>' . $_SESSION['explorermoconame'] . '</strong> in <strong>' . $currentpath . '</strong> not completed</blockquote><br />';
		                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     ' . $_SESSION['explorermocooper'] . ' ' . $mocotype . ' "' . str_replace('\\\\', '\\', $_SESSION['explorermocopath'] . '\\' . $_SESSION['explorermoconame']) . '" in "' . $currentpath . '" not completed';
						}
						unset($_SESSION['explorermocooper']);
						unset($_SESSION['explorermocopath']);
						unset($_SESSION['explorermoconame']);
						unset($_POST['mococancelconf']);
					}
					
					if (isset($_POST['expAddName']) && isset($_POST['expAddType']) && isset($_POST['expAddValue']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
						if ($_POST['expAddName'] == '' || !preg_match('/^[a-zA-Z0-9 !\$%{}()=;_\'+,\.\-\[\]@#~]*$/', $_POST['expAddName'])) {
						$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">disallowed characters in <strong>' . $_POST['expAddName'] . '</strong> to create <strong>' . $_POST['expAddType'] . '</strong></blockquote><br />';
	                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     disallowed characters in "' . str_replace('\\\\', '\\', $currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName'])) . '" to create "' . $_POST['expAddType'] . '"';
						} else {
							if ($_POST['expAddType'] != 'File') {
								@mkdir($currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName']) . '\\', 0777, true);
								if (file_exists($currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName']) . '\\') && is_dir($currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName']) . '\\')) {
									$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">folder <strong>' . $_POST['expAddName'] . '</strong> created successfully</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     folder "' . str_replace('\\\\', '\\', $currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName'])) . '" created successfully';
								} else {
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">folder <strong>' . $_POST['expAddName'] . '</strong> not created</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     folder "' . str_replace('\\\\', '\\', $currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName'])) . '" not created';
								}
							} else {
								$fp = @fopen($currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName']), 'w');
								@fwrite($fp, iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddValue']));
								@fclose($fp);
								if (file_exists($currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName'])) && !is_dir($currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName']) . '\\')) {
									$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">file <strong>' . $_POST['expAddName'] . '</strong> created successfully</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     file "' . str_replace('\\\\', '\\', $currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName'])) . '" created successfully';
								} else {
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">file <strong>' . $_POST['expAddName'] . '</strong> not created</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     file "' . str_replace('\\\\', '\\', $currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['expAddName'])) . '" not created';
								}
							}
						}
					}
					
					if (isset($_POST['confirmoperation']) && isset($_POST['pathfile']) && isset($_POST['newname']) && isset($_POST['oldname']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
						if ($_POST['confirmoperation'] != 'delete' && iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['oldname']) != iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['newname'])) {
							$renamepathfile = $_POST['pathfile'];
							if (is_dir($renamepathfile)) {
								$objren = 'folder';
							} else {
								$objren = 'file';
							}
							@rename($currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['oldname']), $currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['newname']));
							if (file_exists($currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['newname'])) && !file_exists($currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['oldname']))) {
								$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">' . $objren . ' <strong>' . $_POST['oldname'] . '</strong> renamed in <strong>' . $_POST['newname'] . '</strong></blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     ' . $objren . ' "' . str_replace('\\\\', '\\', $currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['oldname'])) . '" renamed in "' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['newname']) . '"';
							} else {
								$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">' . $objren . ' <strong>' . $_POST['oldname'] . '</strong> not renamed</blockquote><br />';
			                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     ' . $objren . ' "' . str_replace('\\\\', '\\', $currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['oldname'])) . '" not renamed';
							}
						}
						
						if (isset($_SESSION['explorermocooper']) && isset($_SESSION['explorermocopath']) && isset($_SESSION['explorermoconame'])) {
							if ($_SESSION['explorermocooper'] != $_POST['confirmoperation'] && $_SESSION['explorermocopath'] . '\\' . $_SESSION['explorermoconame'] == $currentpath . '\\' . iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['oldname'])) {
								unset($_SESSION['explorermocooper']);
								unset($_SESSION['explorermocopath']);
								unset($_SESSION['explorermoconame']);
							}
						}
						if (($_POST['confirmoperation'] == 'Copy' || $_POST['confirmoperation'] == 'Move') && iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['oldname']) == iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['newname'])) {
							$_SESSION['explorermocooper'] = $_POST['confirmoperation'];
							$_SESSION['explorermocopath'] = $currentpath;
							$_SESSION['explorermoconame'] = iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['oldname']);
						}
						
						if ($_POST['confirmoperation'] == 'delete' && $_POST['pathfile'] != '') {
							$deletepathfile = $_POST['pathfile'];
							if (is_dir($deletepathfile)) {
								@rmdir($deletepathfile);
								if (file_exists($deletepathfile)) {
									$deletefoldernotempty = '';
									if (count(scandir($deletepathfile)) > 2) { $deletefoldernotempty = '... is not empty!'; }
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">folder <strong>' . $_POST['oldname'] . '</strong> not deleted' . $deletefoldernotempty . '</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     folder "' . $deletepathfile . '" not deleted' . $deletefoldernotempty;
								} else {
									$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">folder <strong>' . $_POST['oldname'] . '</strong> deleted</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     folder "' . $deletepathfile . '" deleted';
								}
							} else {
								@unlink($deletepathfile);
								if (file_exists($deletepathfile)) {
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">file <strong>' . $_POST['oldname'] . '</strong> not deleted</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     folder "' . $deletepathfile . '" not deleted';
								} else {
									$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">file <strong>' . $_POST['oldname'] . '</strong> deleted</blockquote><br />';
				                    $audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     file browser     file "' . $deletepathfile . '" deleted';
								}
							}
						}
					}
					
					if ($_SESSION['usersett']['filebrowserf'] != '') { if ($filebrowserbllist == '') { $path = 'No Results...'; $cpath = 'No Results...'; } }

					?>

                    <h2>Browse:</h2>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                        <form id="opencli" name="opencli" method="post">
							<input type="hidden" id="openclipath" name="openclipath" value="<?php echo str_replace('\\\\', '\\', $path); ?>" />
							<input type="hidden" id="openclidrive" name="openclidrive" value="<?php echo substr($path, 0, 2); ?>" />
						</form>
						<?php if ($_SESSION['usersett']['filebrowserf'] != '') { ?>
						<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Permitted Folders:</div></td><td width="80%">
						<form id="permpaths" name="permpaths" method="post">
							<select id="permpath" name="permpath" onChange="this.form.submit()" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; margin-top:2px; margin-bottom:2px; font-size:12px;">
								<?php if ($filebrowserbllist != '') { echo $filebrowserbllist; } else { echo '<option>No Results...</option>'; } ?>
							</select>
						</form>
						</td></tr>
						<?php } ?>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><?php if (file_exists($path) && ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['commandline'] > 0)) { echo '<a href="#" onclick="document.opencli.submit();" title="Open in Command Line"><div class="icon-console"></div></a>&nbsp;'; } ?>Current Path:</div></td><td width="80%" style="font-size:12px;">
						<?php
						$returnpath = '';
						if (isset($_SESSION['permpath'])) {
							if (strlen($path) > strlen($_SESSION['permpath'])) { $returnpath = '&nbsp;&nbsp;<a href="?orderby=' . $orderby . '&path=' . $_SESSION['permpath'] . '" title="Return to Root Permitted Folder"><div class="icon-reply-2" style="margin-top:2px;"></div></a>'; }
						} else {
							if (strlen($path) > 3) { $returnpath = '&nbsp;&nbsp;<a href="?orderby=' . $orderby . '&path=' . urlencode(substr($path, 0, 3)) . '" title="Return to Root Folder"><div class="icon-reply-2" style="margin-top:2px;"></div></a>'; }
						}
						?>
						<?php if ($_SESSION['usersett']['filebrowserf'] != '') { ?>
							<?php echo rtrim($cpath, '\\') . '&nbsp;&nbsp;'; echo $returnpath; ?>
						<?php } else { ?>
						<form id="pathform" name="pathform" method="get">
							<select id="path" name="path" onChange="this.form.submit()" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; margin-top:2px; margin-bottom:2px;">
								<?php
								$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_LogicalDisk");
								foreach($wmisclass as $obj) {
									if (is_null($obj->Size) == false) {
										$selected = '';
										if (strpos('|' . $path, strtoupper($obj->Caption)) == 1) { $selected = 'selected'; }
										echo '<option value="' . strtoupper($obj->Caption) . '\\" ' . $selected . '>' . strtoupper($obj->Caption) . '&nbsp;&nbsp;&nbsp;</option>';
									}
								}
								?>
							</select>&nbsp;&nbsp;<?php echo str_replace(' ', '&nbsp;', substr($currentpath, 2)) . '&nbsp;&nbsp;'; echo $returnpath; ?>
						</form>
						<?php } ?>
                        </td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Date Modified:</div></td><td width="80%" style="font-size:12px;"><?php echo date('d/m/Y H:i:s', filemtime($currentpath)); ?></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Total Elements:</div></td><td width="80%"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($explorerrrsetting != 'Hold') { echo number_format(($explorerrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $explorerrrsetting . '&nbsp;&nbsp;'; } ?><a href="?page=<?php echo $pgkey + 1; ?>&orderby=<?php echo $orderby; ?>&path=<?php echo urlencode($path); ?>&lastpath=<?php echo urlencode($lastpath); ?>&filter=<?php echo urlencode($filter); ?>&lastfilter=<?php echo $lastfilter; ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
						<?php if ($filter != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Filter:</div></td><td width="80%" style="font-size:12px;"><i><?php echo $filter; ?></i></td></tr><?php } ?>
                    </table>
                    
					<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    <blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR'; ?>">
                    	<form id="filterform" name="filterform" method="get">
                        	Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="filter" name="filter" placeholder="Regular Expression..." value="<?php echo $filter; ?>" title="<?php echo $filter; ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:170px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;" />&nbsp;&nbsp;<a href="javascript:;" onClick="document.getElementById('filterform').submit();" title="Filter by String or Regular Expression"><div class="icon-search"<?php if ($filter != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php if ($filter != '') { ?>&nbsp;<a href="?orderby=<?php echo $orderby; ?>&path=<?php echo urlencode($path); ?>&lastpath=<?php echo urlencode($lastpath); ?>" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
                            <input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
                            <input type="hidden" id="path" name="path" value="<?php echo $path; ?>" />
                            <input type="hidden" id="lastpath" name="lastpath" value="<?php echo $lastpath; ?>" />
						</form>
					</blockquote>
					</div>
					<br />
                    
					<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { ?>
                    <blockquote style="font-size:12px;" id="container">
                    	<div style="font-size:12px;">
                    	<?php if (is_writable($cpath)) { ?>
                        Upload:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="pickfiles" href="javascript:;" style="font-size:12px;" title="Maximum Upload File Size: <?php echo $uploadsetting; ?> MB"><strong>Select Files</strong></a>&nbsp;&nbsp;&nbsp;<a id="uploadfiles" href="javascript:;" style="font-size:12px;" title="Upload Selected Files"><div class="icon-upload-3"></div></a>&nbsp;<a id="clearlist" href="?page=<?php echo $pgkey + 1; ?>&orderby=<?php echo $orderby; ?>&path=<?php echo urlencode($path); ?>&lastpath=<?php echo urlencode($lastpath); ?>&filter=<?php echo urlencode($filter); ?>&lastfilter=<?php echo $lastfilter; ?>" style="font-size:12px;" title="Cancel Upload"></a><br /><div id="console"></div><div id="filelist"><span style="color:#900000; font-size:12px;"></span></div>
                        <?php } else { ?>
                        Upload:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#900000;">Folder Not Writable...</span>
						<?php } ?>
                        </div>
					</blockquote>

					<?php if (isset($_SESSION['explorermocooper']) && isset($_SESSION['explorermocopath']) && isset($_SESSION['explorermoconame'])) { ?>
					<br />
					<form id="mococancel" name="mococancel" method="post">
						<input type="hidden" id="mococancelconf" name="mococancelconf" value="mococancelconf" />
					</form>
					<form id="mocooper" name="mocooper" method="post">
						<input type="hidden" id="mocooperconf" name="mocooperconf" value="mocooperconf" />
					</form>
                    <?php
					if (is_dir($_SESSION['explorermocopath'] . '\\' . $_SESSION['explorermoconame'])) {
						$mocotype = 'Folder';
					} else {
						$mocotype = 'File';
					}
					?>
                    <blockquote style="font-size:12px;">
						<?php echo $_SESSION['explorermocooper'] . ' ' . $mocotype; ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo str_replace('\\\\', '\\', $_SESSION['explorermocopath'] . '\\' . $_SESSION['explorermoconame']); ?>&nbsp;<?php if (strtolower($currentpath) != strtolower($_SESSION['explorermocopath']) && (strpos('|' . strtolower($currentpath), strtolower($_SESSION['explorermocopath'] . '\\' . $_SESSION['explorermoconame'])) + 0) == 0) { ?>&nbsp;<a href="#" onclick="document.mocooper.submit();" title="<?php echo $_SESSION['explorermocooper']; ?> Here"><div class="icon-forward"></div></a><?php } ?>&nbsp;<a href="#" onclick="document.mococancel.submit();" title="Cancel Operation"><div class="icon-cancel"></div></a>
					</blockquote>
					<?php } ?>
                    <br />
					<?php } ?>
                    
                    <?php echo $message; ?>
                    
                    <div id="explorertable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($explorerrrsetting != 'Hold') { echo 'setInterval(update, ' . $explorerrrsetting . ');'; $phptimeout = $explorerrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'explorerjq.php?page=<?php echo $pgkey; ?>&orderby=<?php echo $orderby; ?>&path=<?php echo urlencode($path); ?>&lastpath=<?php echo urlencode($lastpath); ?>&filter=<?php echo urlencode($filter); ?>&lastfilter=<?php echo $lastfilter; ?>&phptimeout=<?php echo $phptimeout; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($explorerrrsetting != 'Hold') { echo $explorerrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#explorertable').html(data.explorertable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							}
						});
					}
					
					<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { ?>
					var uploader = new plupload.Uploader({
						runtimes : 'html5,html4',
						browse_button : 'pickfiles',
						chunk_size: '1048576',
						multipart : true,
						container: document.getElementById('container'),
						url : '/upload.php?path=<?php echo urlencode($path); ?>',

						filters : {
							prevent_duplicates : true,
							max_file_size : '<?php echo $uploadsetting; ?>mb',
							mime_types: [
								{title : "All Files", extensions : "<?php echo $uploadextsetting; ?>"}
							]
						},

						init: {
							PostInit: function() {
								document.getElementById('filelist').innerHTML = "";
								document.getElementById('clearlist').innerHTML = "";
								document.getElementById('uploadfiles').onclick = function() {
									uploader.start();
									return false;
								};
							},

							FilesAdded: function(up, files) {
								plupload.each(files, function(file) {
									document.getElementById('filelist').innerHTML += "<div id='" + file.id + "' style='font-size:12px;'><strong style='font-size:16px;'>&raquo;&nbsp;</strong>" + file.name + " (" + plupload.formatSize(file.size) + ") <b></b> <s1></s1></div>";
									document.getElementById('clearlist').innerHTML = "<div class='icon-cancel'></div>";
								});
							},

							UploadProgress: function(up, file) {
								document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = "<span style='font-size:12px;'>" + file.percent + "%</span>&nbsp;&nbsp;<span style='font-size:10px;'>" + (Math.round(file.size * file.percent * 0.01)).toLocaleString() + " Byte</span>";
								if (file.percent == 1) {
									document.getElementById('clearlist').innerHTML = "<div class='icon-cancel'></div>";
								}
								if (file.percent == 100) {
									document.getElementById('clearlist').innerHTML = "";
									update();
								}
							},
							
							Error: function(up, err) {
								document.getElementById('console').innerHTML += "<span style='color:#900000; font-size:12px;'>Error #" + err.code + ": " + err.message + "</span><br />";
							}
						}
					});

					uploader.init();
					<?php } ?>
					
					</script>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>