<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemregistry'] > 0) {  } else { header('location: /'); exit; } ?>

<?php $_SESSION['registry'] = htmlspecialchars((string)$_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'); ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php require($euryscoinstallpath . '\\include\\class.WindowsRegistry.php'); ?>

<script type="text/javascript">
	function reginfo(valueName,keyType,keyValue,titleName,keyPath,keyDef,isKey){
		SelectedNONE = '';
		SelectedSZ = '';
		SelectedEXPAND_SZ = '';
		SelectedBINARY = '';
		SelectedDWORD = '';
		SelectedLINK = '';
		SelectedMULTI_SZ = '';
		SelectedRESOURCE_LIST = '';
		SelectedFULL_RESOURCE_DESCRIPTOR = '';
		SelectedRESOURCE_REQUIREMENTS_LIST = '';
		SelectedQWORD = '';
		SelectedKEY = '';
		ReadOnlykeyDef = '';
		SelectDisable = '';
		TypeName = 'Value';
		if (keyType == 'REG_SZ') { SelectedSZ = ' selected="selected"'; }
		if (keyType == 'REG_NONE') { SelectedNONE = ' selected="selected"'; }
		if (keyType == 'REG_EXPAND_SZ') { SelectedEXPAND_SZ = ' selected="selected"'; }
		if (keyType == 'REG_BINARY') { SelectedBINARY = ' selected="selected"'; }
		if (keyType == 'REG_DWORD') { SelectedDWORD = ' selected="selected"'; }
		if (keyType == 'REG_LINK') { SelectedLINK = ' selected="selected"'; }
		if (keyType == 'REG_MULTI_SZ') { SelectedMULTI_SZ = ' selected="selected"'; }
		if (keyType == 'REG_RESOURCE_LIST') { SelectedRESOURCE_LIST = ' selected="selected"'; }
		if (keyType == 'REG_FULL_RESOURCE_DESCRIPTOR') { SelectedFULL_RESOURCE_DESCRIPTOR = ' selected="selected"'; }
		if (keyType == 'REG_RESOURCE_REQUIREMENTS_LIST') { SelectedRESOURCE_REQUIREMENTS_LIST = ' selected="selected"'; }
		if (keyType == 'REG_QWORD') { SelectedQWORD = ' selected="selected"'; }
		if (isKey == '1') { SelectedKEY = '<option value="KEY">&nbsp;KEY&nbsp;&nbsp;</option>'; SelectDisable = ' disabled="disabled"'; ReadOnlykeyDef = ' readonly="readonly"'; TypeName = 'Key'; }
		if (keyDef == '1') { ReadOnlykeyDef = ' readonly="readonly"'; }
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-grid" style="position:inherit;"></div>&nbsp; ' + TypeName + ': <strong>' + titleName + '</strong></span>',
			'content'     : '<form id="keyUpdate" name="keyUpdate" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselect"><td width="50%" style="font-size:12px;">' + TypeName + ' Name:&nbsp;</td><td width="50%" style="font-size:10px;"><input type="text" id="keyNameReg" name="keyNameReg" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px; padding-left:4px; padding-right:4px; font-size:12px;" value="' + valueName + '" ' + ReadOnlykeyDef + '></td></tr><tr class="rowselect"><td width="50%" style="font-size:12px;">Type:&nbsp;</td><td width="50%" style="font-size:10px;"><select id="keyTypeReg" name="keyTypeReg" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px;"' + SelectDisable + '>' + SelectedKEY + '<option value="REG_SZ"' + SelectedSZ + '>&nbsp;REG_SZ&nbsp;&nbsp;</option><option value="REG_NONE"' + SelectedNONE + '>&nbsp;REG_NONE&nbsp;&nbsp;</option><option value="REG_EXPAND_SZ"' + SelectedEXPAND_SZ + '>&nbsp;REG_EXPAND_SZ&nbsp;&nbsp;</option><option value="REG_BINARY"' + SelectedBINARY + '>&nbsp;REG_BINARY&nbsp;&nbsp;</option><option value="REG_DWORD"' + SelectedDWORD + '>&nbsp;REG_DWORD&nbsp;&nbsp;</option><option value="REG_LINK"' + SelectedLINK + ' disabled="disabled">&nbsp;REG_LINK&nbsp;&nbsp;</option><option value="REG_MULTI_SZ"' + SelectedMULTI_SZ + '>&nbsp;REG_MULTI_SZ&nbsp;&nbsp;</option><option value="REG_RESOURCE_LIST"' + SelectedRESOURCE_LIST + ' disabled="disabled">&nbsp;REG_RES_LIST&nbsp;&nbsp;</option><option value="REG_FULL_RESOURCE_DESCRIPTOR"' + SelectedFULL_RESOURCE_DESCRIPTOR + ' disabled="disabled">&nbsp;REG_FULL_RES&nbsp;&nbsp;</option><option value="REG_RESOURCE_REQUIREMENTS_LIST"' + SelectedRESOURCE_REQUIREMENTS_LIST + ' disabled="disabled">&nbsp;REG_RES_REQ&nbsp;&nbsp;</option><option value="REG_QWORD"' + SelectedQWORD + ' disabled="disabled">&nbsp;REG_QWORD&nbsp;&nbsp;</option></select></td></tr><tr class="rowselect"><td style="font-size:12px;">Confirm&nbsp;Deletion:&nbsp;</td><td style="font-size:10px;"><select id="confirmdelete" name="confirmdelete" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px;"><option value="donotdelete">&nbsp;DO&nbsp;NOT&nbsp;DELETE&nbsp;&nbsp;</option><option value="delete">&nbsp;DELETE&nbsp;&nbsp;</option></select></td></tr></table><input type="hidden" id="keyPathReg" name="keyPathReg"><input type="hidden" id="keyOldNameReg" name="keyOldNameReg"><input type="hidden" id="keyDefReg" name="keyDefReg"><input type="hidden" id="isKey" name="isKey"><textarea  id="keyValueReg" name="keyValueReg" style="width:250px; font-family:\'Lucida Console\', Monaco, monospace; font-size:11px; height:75px; font-weight:normal;"' + SelectDisable + '>' + keyValue + '</textarea><input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" /></form>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '55px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 146) + 'px'
			},
			'buttons'     : {
				<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemregistry'] > 1) { ?>
				'Update'     : {
				'action': function(){
						document.getElementById("keyPathReg").value = keyPath;
						document.getElementById("keyDefReg").value = keyDef;
						document.getElementById("isKey").value = isKey;
						document.getElementById("keyOldNameReg").value = valueName;
						document.getElementById("keyUpdate").submit();
					}
				},
				<?php } ?>
				'Close'     : {
				'action': function(){}
				},
			}
		});
	};
	<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemregistry'] > 1) { ?>
	function regadd(keyPath){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-grid" style="position:inherit;"></div>&nbsp; Add New <strong>Key</strong> or <strong>Value</strong></span>',
			'content'     : '<form id="keyAdd" name="keyAdd" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr class="rowselect"><td width="50%" style="font-size:12px;">Name:&nbsp;</td><td width="50%" style="font-size:10px;"><input type="text" id="keyAddName" name="keyAddName" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px; padding-left:4px; padding-right:4px; font-size:12px;" value="New Entry"></td></tr><tr class="rowselect"><td width="50%" style="font-size:12px;">Type:&nbsp;</td><td width="50%" style="font-size:10px;"><select id="keyAddType" name="keyAddType" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px;"><option value="KEY">&nbsp;KEY&nbsp;&nbsp;</option><option value="" disabled="disabled"></option><option value="REG_SZ">&nbsp;REG_SZ&nbsp;&nbsp;</option><option value="REG_NONE">&nbsp;REG_NONE&nbsp;&nbsp;</option><option value="REG_EXPAND_SZ">&nbsp;REG_EXPAND_SZ&nbsp;&nbsp;</option><option value="REG_BINARY">&nbsp;REG_BINARY&nbsp;&nbsp;</option><option value="REG_DWORD">&nbsp;REG_DWORD&nbsp;&nbsp;</option><option value="REG_MULTI_SZ">&nbsp;REG_MULTI_SZ&nbsp;&nbsp;</option></select></td></tr></table><input type="hidden" id="keyPathReg" name="keyPathReg"><textarea id="keyAddValue" name="keyAddValue" style="width:250px; font-family:\'Lucida Console\', Monaco, monospace; font-size:11px; height:75px; font-weight:normal;"></textarea><input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" /></form>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '55px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 146) + 'px'
			},
			'buttons'     : {
				'Add'     : {
				'action': function(){
						document.getElementById("keyPathReg").value = keyPath;
						document.getElementById("keyAdd").submit();
					}
				},
				'Close'     : {
				'action': function(){}
				},
			}
		});
	};
	<?php } ?>
</script>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>System<small>registry</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-registry-button big page-back"></a>
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
					
					if (isset($_GET['lastfilter'])) {
						$lastfilter = $_GET['lastfilter'];
					} else {
						$lastfilter = '';
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

					if (isset($_GET['hkey'])) {
						$hkey = $_GET['hkey'];
					} else {
						$hkey = 'HKEY_LOCAL_MACHINE';
					}

					if (isset($_GET['lastpath'])) {
						$lastpath = $_GET['lastpath'];
					} else {
						$lastpath = '';
					}
					
					if (isset($_GET['keypath'])) {
						if ($_GET['keypath'] != '') {
							$keypath = '\\' . $_GET['keypath'];
						} else {
							$keypath = '';
						}
					} else {
						$keypath = $hkey;
					}
					
					$keypath = $lastpath . $keypath;
					
					$random = md5($keypath . session_id());
					
					if (isset($_POST['permkey'])) {
						$_SESSION['permkey'] = $_POST['permkey'];
					}

					if (isset($_SESSION['permkey'])) {
						if (!strpos('|' . $keypath, $_SESSION['permkey'])) {
							$keypath = $_SESSION['permkey'];
						}
					}

					$regeditblcount = 0;
					$regeditbllist = '';
					if ($_SESSION['usersett']['regeditf'] != '') {
						$regeditblarray = array();
						$regeditblarray = (explode(',', preg_replace('/\r\n|\r|\n/', ',', $_SESSION['usersett']['regeditf'])));
						foreach ($regeditblarray as $regeditbl) {
							if (!isset($_SESSION['permkey']) && $regeditblcount == 0) { $_SESSION['permkey'] = $regeditbl; $keypath = $_SESSION['permkey']; }
							if (strpos('|' . $keypath, $regeditbl) > 0) { $regeditblsel = 'selected'; } else { $regeditblsel = ''; }
							$regeditbllist = $regeditbllist . '<option value="' . $regeditbl . '" ' . $regeditblsel . '>' . rtrim($regeditbl, '\\') . '&nbsp;&nbsp;&nbsp;</option>';
							$regeditblcount = $regeditblcount + 1;
						}
					}

					$message = '';
					
					if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemregistry'] > 1) {
					
						if (isset($_POST['regexportconf']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
							$regexportpath = str_replace('\\\\', '\\', $_POST['regexportconf']);
							if (file_exists($euryscoinstallpath . '\\temp\\core\\' . $random . '.reg')) { @unlink($euryscoinstallpath . '\\temp\\core\\' . $random . '.reg'); }
							session_write_close();
							exec('reg.exe export "' . $regexportpath . '" "' . $euryscoinstallpath . '\\temp\\core\\' . $random . '.reg' . '"', $errorarray, $errorlevel);
							session_start();
							if ($errorlevel == 0) {
								$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">export key <strong>' . $regexportpath . '</strong> completed</blockquote><br />';
								$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     export key "' . $regexportpath . '" completed';
								header('location: /download.php?' . substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25) . '&download=' . $random . '.reg&path=' . $euryscoinstallpath . '\\temp\\core');
								exit;
							} else {
								$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">export key <strong>' . $regexportpath . '</strong> not completed</blockquote><br />';
								$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     export key "' . $regexportpath . '" not completed';
							}
							unset($_POST['regexportconf']);
						}
						
						if (isset($_POST['keyPathReg']) && isset($_POST['keyAddName']) && isset($_POST['keyAddType']) && isset($_POST['keyAddValue']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
							if ($_POST['keyAddName'] == '' || (!is_numeric($_POST['keyAddValue']) && $_POST['keyAddType'] == 'REG_DWORD') || (!is_numeric($_POST['keyAddValue']) && $_POST['keyAddType'] == 'REG_QWORD') || (!preg_match('/^[0-9\s]*$/', $_POST['keyAddValue']) && $_POST['keyAddType'] == 'REG_BINARY') || !preg_match('/^[a-zA-Z0-9\.;:,#\[\]\*+-@_\?\^\/()~$%&=\s]*$/', $_POST['keyAddName']) || !preg_match('/^[a-zA-Z0-9\.;:,#\[\]\*+-@_\?\^\/()~$%&=\s\r\n\\\]*$/', $_POST['keyAddValue'])) {
							$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">disallowed characters for <strong>' . $_POST['keyAddType'] . '</strong> in <strong>' . $_POST['keyAddName'] . '</strong></blockquote><br />';
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     disallowed characters for "' . $_POST['keyAddType'] . '" in "' . $_POST['keyPathReg'] . '\\' . $_POST['keyAddName'] . '"';
							} else {
								if ($_POST['keyAddType'] != 'KEY') {
									if ($_POST['keyAddType'] != 'REG_MULTI_SZ') { $regValue = preg_replace('/\r\n|\r|\n/',' ', $_POST['keyAddValue']); } else { $regValue = preg_replace('/\r\n|\r|\n/','\\\\0', $_POST['keyAddValue']); }
									session_write_close();
									exec('reg.exe add "' . $_POST['keyPathReg'] . '" /v "' . $_POST['keyAddName'] . '" /t ' . $_POST['keyAddType'] . ' /f /d "' . str_replace('\\"', '\\\\"', $regValue . '"'), $errorarray, $errorlevel);
									session_start();
									if ($errorlevel == 0) {
										$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">value <strong>' . $_POST['keyAddName'] . '</strong> added successfully</blockquote><br />';
										$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     value "' . $_POST['keyPathReg'] . '\\' . $_POST['keyAddName'] . '" added successfully';
									} else {
										$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">value <strong>' . $_POST['keyAddName'] . '</strong> not added</blockquote><br />';
										$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     value "' . $_POST['keyPathReg'] . '\\' . $_POST['keyAddName'] . '" not added';
									}
								} else {
									session_write_close();
									exec('reg.exe add "' . $_POST['keyPathReg'] . '\\' . $_POST['keyAddName'] . '" /f', $errorarray, $errorlevel);
									session_start();
									if ($errorlevel == 0) {
										$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">key <strong>' . $_POST['keyAddName'] . '</strong> added successfully</blockquote><br />';
										$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     key "' . $_POST['keyPathReg'] . '\\' . $_POST['keyAddName'] . '" added successfully';
									} else {
										$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">key <strong>' . $_POST['keyAddName'] . '</strong> not added</blockquote><br />';
										$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     key "' . $_POST['keyPathReg'] . '\\' . $_POST['keyAddName'] . '" not added';
									}
								}
							}
						}
						
						if (isset($_POST['keyPathReg']) && isset($_POST['keyNameReg']) && isset($_POST['keyOldNameReg']) && isset($_POST['keyTypeReg']) && isset($_POST['confirmdelete']) && isset($_POST['keyValueReg']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']) > 0 && isset($_POST[substr(md5('$_POST' . $_SESSION['tokenl']), 5, 15)])) {
							if ($_POST['keyNameReg'] == '' || (!is_numeric($_POST['keyValueReg']) && $_POST['keyTypeReg'] == 'REG_DWORD') || (!is_numeric($_POST['keyValueReg']) && $_POST['keyTypeReg'] == 'REG_QWORD') || (!preg_match('/^[0-9\s]*$/', $_POST['keyValueReg']) && $_POST['keyTypeReg'] == 'REG_BINARY') || !preg_match('/^[a-zA-Z0-9\.;:,#\[\]\*+-@_\?\^\/()~$%&=\s]*$/', $_POST['keyNameReg']) || !preg_match('/^[a-zA-Z0-9\.;:,#\[\]\*+-@_\?\^\/()~$%&=\s\r\n\\\]*$/', $_POST['keyValueReg'])) {
								$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">disallowed characters for <strong>' . $_POST['keyTypeReg'] . '</strong> in <strong>' . $_POST['keyNameReg'] . '</strong></blockquote><br />';
								$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     disallowed characters for "' . $_POST['keyTypeReg'] . '" in "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '"';
							} else {
								if ($_POST['isKey'] != '1') {
									if ($_POST['keyTypeReg'] != 'REG_MULTI_SZ') { $regValue = preg_replace('/\r\n|\r|\n/',' ', $_POST['keyValueReg']); } else { $regValue = preg_replace('/\r\n|\r|\n/','\\\\0', $_POST['keyValueReg']); }
									if ($_POST['keyDefReg'] != '1') { $regName = '/v "' . $_POST['keyNameReg'] . '"'; $regOldName = '/v "' . $_POST['keyOldNameReg'] . '"'; } else { $regName = '/ve'; $regOldName = '/ve'; }
									if ($_POST['keyNameReg'] != $_POST['keyOldNameReg'] || $_POST['confirmdelete'] == 'delete') {
										session_write_close();
										exec('reg.exe delete "' . $_POST['keyPathReg'] . '" ' . $regOldName . ' /f', $errorarray, $errorlevel);
										session_start();
										if ($errorlevel == 0) {
											$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">value <strong>' . $_POST['keyNameReg'] . '</strong> deleted successfully</blockquote><br />';
											$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     value "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '" deleted successfully';
										} else {
											$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">value <strong>' . $_POST['keyNameReg'] . '</strong> not deleted</blockquote><br />';
											$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     value "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '" not deleted';
										}
									}
									if ($_POST['confirmdelete'] != 'delete') {
										session_write_close();
										exec('reg.exe add "' . $_POST['keyPathReg'] . '" ' . htmlspecialchars_decode($regName) . ' /t ' . $_POST['keyTypeReg'] . ' /f /d "' . htmlspecialchars_decode(str_replace('\\"', '\\\\"', $regValue . '"')), $errorarray, $errorlevel);
										session_start();
										if ($errorlevel == 0) {
											if ($_POST['keyNameReg'] != $_POST['keyOldNameReg']) {
												$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">value <strong>' . $_POST['keyOldNameReg'] . '</strong> updated in <strong>' . $_POST['keyNameReg'] . '</strong> successfully</blockquote><br />';
												$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     value "' . $_POST['keyPathReg'] . '\\' . $_POST['keyOldNameReg'] . '" updated in "' . $_POST['keyNameReg'] . '" successfully';
											} else {
												$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">value <strong>' . $_POST['keyOldNameReg'] . '</strong> updated successfully</blockquote><br />';
												$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     value "' . $_POST['keyPathReg'] . '\\' . $_POST['keyOldNameReg'] . '" updated successfully';
											}
										} else {
											$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">value <strong>' . $_POST['keyNameReg'] . '</strong> not updated</blockquote><br />';
											$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     value "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '" not updated';
										}
									}
								}
							}
						}
						
						if (isset($_POST['keyPathReg']) && isset($_POST['keyNameReg']) && isset($_POST['confirmdelete']) && isset($_POST['isKey']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
							if ($_POST['isKey'] == '1' && $_POST['confirmdelete'] == 'delete') {
								session_write_close();
								exec('reg.exe delete "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '" /f', $errorarray, $errorlevel);
								session_start();
								if ($errorlevel == 0) {
									$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">key <strong>' . $_POST['keyNameReg'] . '</strong> deleted successfully</blockquote><br />';
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     key "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '" deleted successfully';
								} else {
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">key <strong>' . $_POST['keyNameReg'] . '</strong> not deleted</blockquote><br />';
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     key "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '" not deleted';
								}
							}
							if ($_POST['isKey'] == '1' && $_POST['confirmdelete'] != 'delete') {
								session_write_close();
								exec('reg.exe add "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '"', $errorarray, $errorlevel);
								session_start();
								if ($errorlevel == 0) {
									$message = '<blockquote style="font-size:12px; background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">key <strong>' . $_POST['keyNameReg'] . '</strong> added successfully</blockquote><br />';
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     key "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '" added successfully';
								} else {
									$message = '<blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">key <strong>' . $_POST['keyNameReg'] . '</strong> not added</blockquote><br />';
									$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system registry     key "' . $_POST['keyPathReg'] . '\\' . $_POST['keyNameReg'] . '" not added';
								}
							}
						}
					
					}
					
					?>
                    
                    <h2>Registry:</h2>
					<form id="regexport" name="regexport" method="post">
                    	<input type="hidden" id="regexportconf" name="regexportconf" value="" />
						<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
					</form>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
						<?php if ($_SESSION['usersett']['regeditf'] != '') { ?>
						<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Permitted Keys:</div></td><td width="80%">
						<form id="permkey" name="permkey" method="post">
							<select id="permkey" name="permkey" onChange="this.form.submit()" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; margin-top:2px; margin-bottom:2px; font-size:12px;">
								<?php if ($regeditbllist != '') { echo $regeditbllist; } else { echo '<option>No Results...</option>'; } ?>
							</select>
							<input type="hidden" id="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_POST' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_POST' . $sessiontoken), 15, 25); ?>" />
						</form>
						</td></tr>
						<?php } ?>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;"><?php if (strlen(str_replace($hkey, '', $keypath)) > 1) { echo '<a href="#" onclick="document.regexport.submit();" style="font-size:12px;" title="Export REG"><div class="icon-download-2"></div></a>&nbsp;'; } ?>Current Path:</div></td><td width="80%" style="font-size:12px;">
                        <?php if ($_SESSION['usersett']['regeditf'] != '') { ?>
						<?php echo $keypath; if (strlen(str_replace($hkey, '', $keypath)) > 1) { if (strtolower($keypath) != strtolower($_SESSION['permkey'])) { echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="?orderby=' . $orderby . '&hkey=' . $hkey . '" title="Return to Root Permitted Key"><div class="icon-reply-2" style="margin-top:2px;"></div></a>'; } } ?>
						<?php } else { ?>
						<form id="pathform" name="pathform" method="get">
							<select id="hkey" name="hkey" onChange="this.form.submit()" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; margin-top:2px; margin-bottom:2px;">
								<option value="HKEY_CLASSES_ROOT" <?php if (strpos('|' . $keypath, 'HKEY_CLASSES_ROOT') == 1) { echo 'selected'; } ?>>HKEY_CLASSES_ROOT</option>
								<option value="HKEY_CURRENT_USER" <?php if (strpos('|' . $keypath, 'HKEY_CURRENT_USER') == 1) { echo 'selected'; } ?>>HKEY_CURRENT_USER</option>
								<option value="HKEY_LOCAL_MACHINE" <?php if (strpos('|' . $keypath, 'HKEY_LOCAL_MACHINE') == 1) { echo 'selected'; } ?>>HKEY_LOCAL_MACHINE</option>
								<option value="HKEY_USERS" <?php if (strpos('|' . $keypath, 'HKEY_USERS') == 1) { echo 'selected'; } ?>>HKEY_USERS</option>
								<option value="HKEY_CURRENT_CONFIG" <?php if (strpos('|' . $keypath, 'HKEY_CURRENT_CONFIG') == 1) { echo 'selected'; } ?>>HKEY_CURRENT_CONFIG</option>
							</select>&nbsp;&nbsp;<?php echo str_replace(' ', '&nbsp;', str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)str_replace($hkey, '', $keypath), ENT_QUOTES, 'UTF-8'))))); if (strlen(str_replace($hkey, '', $keypath)) > 1) { echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="?orderby=' . $orderby . '&hkey=' . str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$hkey, ENT_QUOTES, 'UTF-8')))) . '" title="Return to Root Key"><div class="icon-reply-2" style="margin-top:2px;"></div></a>'; } ?>
							<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
						</form>
						<?php } ?>
                        </td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Total Elements:</div></td><td width="80%"><div id="totalelement" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($registryrrsetting != 'Hold') { echo number_format(($registryrrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $registryrrsetting . '&nbsp;&nbsp;'; } ?><a href="?page=<?php echo $pgkey + 1; ?>&orderby=<?php echo $orderby; ?>&lastpath=<?php echo urlencode($keypath); ?>&keypath=&filter=<?php echo urlencode($filter); ?>&lastfilter=<?php echo strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$lastfilter, ENT_QUOTES, 'UTF-8'))))); ?>" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
						<?php if ($filter != '') { ?><tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Filter:</div></td><td width="80%" style="font-size:12px;"><i><?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?></i></td></tr><?php } ?>
                    </table>
                    
					<div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    <blockquote style="font-size:12px; height:33px;" title="<?php echo 'Use Normal String for SIMPLE SEARCH' . "\n" . 'Use Regular Expression for COMPLEX SEARCH' . "\n" . 'Use Minus  -  for NOT CONTAIN' . "\n" . 'Use Pipe  |  for OR OPERATOR' . "\n" . 'Use Raw Data View for REFERENCES'; ?>">
                    	<form id="filterform" name="filterform" method="get">
                        	Filter:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="filter" name="filter" placeholder="Regular Expression..." value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" title="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$filter, ENT_QUOTES, 'UTF-8')))); ?>" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; width:170px; height:23px; padding-top:0px; padding-left:4px; padding-right:4px;" />&nbsp;&nbsp;<a href="javascript:;" onClick="document.getElementById('filterform').submit();" title="Filter by String or Regular Expression"><div class="icon-search"<?php if ($filter != '') { echo ' style="color:#8063C8;"'; } ?>></div></a><?php if ($filter != '') { ?>&nbsp;<a href="?orderby=<?php echo $orderby; ?>&lastpath=<?php echo urlencode($lastpath); ?>&hkey=<?php echo urlencode($hkey); ?>&keypath=" title="Clear Filter"><div class="icon-cancel"></div></a><?php } ?>
                            <input type="hidden" id="orderby" name="orderby" value="<?php echo $orderby; ?>" />
                            <input type="hidden" id="lastpath" name="lastpath" value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$keypath, ENT_QUOTES, 'UTF-8')))); ?>" />
                            <input type="hidden" id="hkey" name="hkey" value="<?php echo str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$hkey, ENT_QUOTES, 'UTF-8')))); ?>" />
                            <input type="hidden" id="keypath" name="keypath" value="" />
							<input type="hidden" id="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" name="<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15); ?>" value="<?php echo substr(md5('$_GET' . $sessiontoken), 15, 25); ?>" />
						</form>
					</blockquote>
					</div>
					<br />
                    
                    <?php echo $message; ?>
                    
                    <div id="registrytable"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($registryrrsetting != 'Hold') { echo 'setInterval(update, ' . $registryrrsetting . ');'; $phptimeout = $registryrrsetting / 1000; } else { $phptimeout = 120; } ?>
					
					function update() {
						$.ajax({
							type: "GET",
							url: 'registryjq.php?<?php echo substr(md5('$_GET' . $sessiontoken), 5, 15) . '=' . substr(md5('$_GET' . $sessiontoken), 15, 25); ?>&orderby=<?php echo $orderby; ?>&filter=<?php echo urlencode($filter); ?>&lastfilter=<?php echo strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$lastfilter, ENT_QUOTES, 'UTF-8'))))); ?>&page=<?php echo $pgkey; ?>&keypath=<?php echo urlencode($keypath); ?>&hkey=<?php echo urlencode(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$hkey, ENT_QUOTES, 'UTF-8'))))); ?>&phptimeout=<?php echo $phptimeout; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($registryrrsetting != 'Hold') { echo $registryrrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#registrytable').html(data.registrytable);
							$('#totalelement').html(data.totalelement + '&nbsp;&nbsp;<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?lastpath=<?php echo urlencode($lastpath); ?>&hkey=<?php echo urlencode($hkey); ?>&lastfilter=<?php echo strtolower(str_replace(')', '&rpar;', str_replace('(', '&lpar;', str_replace('=', '&equals;', htmlspecialchars((string)$lastfilter, ENT_QUOTES, 'UTF-8'))))); ?>&keypath=" title="Reset View"><div class="icon-undo"></div></a>');
							$('#totaltime').html(data.totaltime);
							$('#regexportconf').attr('value', data.regexportconf);
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