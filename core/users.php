<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Auditors' || $_SESSION['usersett']['usermanagement'] > 0) {  } else { header('location: /'); exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<?php $grouplist = scandir($_SERVER['DOCUMENT_ROOT'] . '\\groups\\'); ?>

<?php
$message = '';
				
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) {
				
	if (isset($_POST['edituserfile']) && isset($_POST['editusername']) && isset($_POST['edituserpsw']) && isset($_POST['edituserpswc']) && isset($_POST['editusertype']) && isset($_POST['edituserauth']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
		if (isset($_POST['deleteuserconf'])) {
			@unlink($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_POST['edituserfile']);
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_POST['edituserfile'])) {
				$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">user <strong>' . $_POST['editusername'] . '</strong> not deleted</blockquote><br />';
				$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user management     user "' . $_POST['editusername'] . '" not deleted';
			} else {
				$message = '<blockquote style="background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">user <strong>' . $_POST['editusername'] . '</strong> deleted</blockquote><br />';
				$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user management     user "' . $_POST['editusername'] . '" deleted';
			}
		} else {
			$currentuserxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_POST['edituserfile'], true)))));
			
			$currentusertype = $currentuserxml->settings->usertype;
			$editusertype = '';
			if ($currentusertype != hash('sha512', $_POST['editusername'] . $_POST['editusertype'])) {
				$editusertype = hash('sha512', $_POST['editusername'] . $_POST['editusertype']);
			}
			
			$currentuserauth = $currentuserxml->settings->userauth;
			$edituserauth = '';
			if ($currentuserauth != hash('sha512', $_POST['editusername'] . $_POST['edituserauth'])) {
				$edituserauth = hash('sha512', $_POST['editusername'] . $_POST['edituserauth']);
			}
			
			$edituserpsw = '';
			$checkgroups = 0;
			if ($_POST['edituserpsw'] != '' || $_POST['edituserpswc'] != '') {
				if (strtolower($_POST['editusername']) == strtolower($_POST['edituserpsw'])) {
					$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">username and password cannot be equal</blockquote><br />';
					$edituserauth = '';
					$editusertype = '';
					$edituserpsw = '';
				} else {
					if ($_POST['edituserpsw'] != $_POST['edituserpswc']) {
						$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">confirm password mismatch</blockquote><br />';
						$edituserauth = '';
						$editusertype = '';
						$edituserpsw = '';
					} else {
						if (strlen($_POST['edituserpsw']) < 8) {
							$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">password must be at least 8 characters</blockquote><br />';
							$edituserauth = '';
							$editusertype = '';
							$edituserpsw = '';
						} else {
							if (!preg_match('/[\\\\\'\"\!\.\:\;\[\]\^\$\%\&\*\(\)\}\{\@\#\~\?\/\,\|\=\_\+\-]/', $_POST['edituserpsw']) || !preg_match('/[a-z]/', $_POST['edituserpsw']) || !preg_match('/[A-Z]/', $_POST['edituserpsw']) || !preg_match('/[0-9]/', $_POST['edituserpsw'])) {
								$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">password must be contain number <strong>0~9</strong>, lower <strong>a~z</strong>, upper <strong>A~Z</strong> and special characters <strong>\\\'",.:;!?^$%&()[]{}@#~\/|=*+-_</strong></blockquote><br />';
								$edituserauth = '';
								$editusertype = '';
								$edituserpsw = '';
							} else {
								if (preg_match('/[^a-zA-Z0-9\\\\\'\"\!\.\:\;\[\]\^\$\%\&\*\(\)\}\{\@\#\~\?\/\,\|\=\_\+\-]/', $_POST['edituserpsw'])) {
									$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">special characters allowed <strong>\\\'",.:;!?^$%&()[]{}@#~\/|=*+-_</strong></blockquote><br />';
									$edituserauth = '';
									$editusertype = '';
									$edituserpsw = '';
								} else {
									$passwchk = $currentuserxml->settings->passwchk;
									if (preg_match('/' . md5(substr(hash('whirlpool', hash('sha256', hash('sha384', hash('sha512', $_POST['editusername'] . ':' . $realm . ':' . $_POST['edituserpsw'])))), 0, -1)) . '/', base64_decode($passwchk))) {
										$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">password must be different than old password</blockquote><br />';
										$edituserauth = '';
										$editusertype = '';
										$edituserpsw = '';
									} else {
										if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $_POST['editusertype'] . '.xml')) {
											$checkgroups = 1;
											$checkgroupsxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $_POST['editusertype'] . '.xml', true)))));
											$mcrykey = pack('H*', hash('sha256', hash('sha512', $checkgroupsxml->settings->groupname)));
											$checkgroupsarray = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($checkgroupsxml->settings->groupsettings), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($checkgroupsxml->settings->groupsettings), 0, $iv_size)));
											if ($checkgroupsarray['name'] == $_POST['editusertype']) { $checkgroups = 0; }
											$checkgroups = 2;
											if ($checkgroupsarray['auth'] == 'Distributed' || $checkgroupsarray['auth'] == $_POST['edituserauth']) { $checkgroups = 0; }
										} else {
											$checkgroups = 3;
											if ($_POST['editusertype'] == 'Administrators' || $_POST['editusertype'] == 'Auditors' || $_POST['editusertype'] == 'Operators' || $_POST['editusertype'] == 'Users') { $checkgroups = 0; }
										}
										if ($checkgroups > 0) {
											if ($checkgroups == 1) { $message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">user group <strong>' . $_POST['editusertype'] . '</strong> is currupted</blockquote><br />'; }
											if ($checkgroups == 2) { $message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">user group <strong>' . $_POST['editusertype'] . '</strong> must be distributed</blockquote><br />'; }
											if ($checkgroups == 3) { $message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">user group <strong>' . $_POST['editusertype'] . '</strong> not exist</blockquote><br />'; }
										} else {
											if (strlen(base64_decode($passwchk)) > 416) { $passwchk = base64_encode(substr(base64_decode($passwchk), 0, -32)); }
											$passwchk = base64_encode(md5(substr(hash('whirlpool', hash('sha256', hash('sha384', hash('sha512', $_POST['editusername'] . ':' . $realm . ':' . $_POST['edituserpsw'])))), 0, -1)) . base64_decode($passwchk));
											$mcrykey = pack('H*', hash('sha256', hash('sha512', $_POST['editusername'] . $_POST['editusertype'])));
											$edituserpsw = base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykey, md5($_POST['editusername'] . ':' . $realm . ':' . $_POST['edituserpsw']), MCRYPT_MODE_CBC, $iv));
										}
									}
								}
							}
						}
					}
				}
			}
			
			if (($editusertype != '' || $edituserpsw != '' || $edituserauth != '') && isset($passwchk) && $checkgroups == 0) {
				if ($editusertype != '') {
					$message = $message . '<blockquote style="background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">user <strong>' . $_POST['editusername'] . '</strong> moved in <strong>' . $_POST['editusertype'] . '</strong> group</blockquote><br />';
					$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user management     user "' . $_POST['editusername'] . '" moved in "' . $_POST['editusertype'] . '" group';
				} else {
					$editusertype = $currentuserxml->settings->usertype;
				}
				if ($edituserauth != '') {
					$message = $message . '<blockquote style="background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">user <strong>' . $_POST['editusername'] . '</strong> authentication changed in <strong>' . strtolower($_POST['edituserauth']) . '</strong></blockquote><br />';
					$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user management     user "' . $_POST['editusername'] . '" authentication changed in "' . $_POST['edituserauth'] . '"';
				} else {
					$edituserauth = $currentuserxml->settings->userauth;
				}
				if ($edituserpsw != '') {
					$message = $message . '<blockquote style="background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">password changed for user <strong>' . $_POST['editusername'] . '</strong></blockquote><br />';
					$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user management     password changed for user "' . $_POST['editusername'] . '"';
				} else {
					$edituserpsw = $currentuserxml->settings->password;
				}
				$xml = '<?xml version="1.0"?>' . "\n" . '<config>' . "\n" . '	<settings>' . "\n" . '		<username>' . $_POST['editusername'] . '</username>' . "\n" . '		<usertype>' . $editusertype . '</usertype>' . "\n" . '		<userauth>' . $edituserauth . '</userauth>' . "\n" . '		<password>' . $edituserpsw . '</password>' . "\n" . '		<passwchk>' . $passwchk . '</passwchk>' . "\n" . '		<passwlck>' . md5($edituserpsw) . '</passwlck>' . "\n" . '		<expiration>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('2000-01-01 00:00:00'))))) . '</expiration>' . "\n" . '	</settings>' . "\n" . '</config>';
				$writexml = fopen($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_POST['edituserfile'], 'w');
				fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
				fclose($writexml);
				if ($agentstatus == 'con' && $_POST['edituserauth'] == 'Distributed') { @copy($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $_POST['edituserfile'], str_replace('\\core', '\\agent\\users', $_SERVER['DOCUMENT_ROOT']) . '\\' . $_POST['edituserfile']); }
			} else {
				if ($checkgroups == 0) { $message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">password must be different than old password</blockquote><br />'; }
			}
		}
		$message = $message . '<br />';
	}

}

$newaccordionst = '';

if (isset($_POST['newusertype'])) {
	$newusertype = $_POST['newusertype'];
} else {
	$newusertype = '';
}

if (isset($_POST['newuserauth'])) {
	$newuserauth = $_POST['newuserauth'];
} else {
	$newuserauth = '';
}

if (isset($_POST['newusername'])) {
	$newusername = preg_replace('/[^a-zA-Z0-9 \.-@_]*/', '', trim($_POST['newusername']));
} else {
	$newusername = '';
}

if (isset($_POST['newuserpsw'])) {
	$newuserpsw = $_POST['newuserpsw'];
} else {
	$newuserpsw = '';
}

if (isset($_POST['newuserpswc'])) {
	$newuserpswc = $_POST['newuserpswc'];
} else {
	$newuserpswc = '';
}

if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) {

	if (isset($_POST['newaccount']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
		$newaccordionst = ' class="active"';
		if ($newusertype == '' || $newusername == '' || $newuserpsw == '' || $newuserpswc == '') {
			$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">all fields must be completed</blockquote><br />';
		} else {
			if (preg_match('/[^a-zA-Z0-9]/', $newusername)) {
				$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">special characters are not allowed in the name</blockquote><br />';
			} else {
				if (strtolower($newusername) == 'administrator' && strtolower($newuserauth) == 'distributed') {
					$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">administrator must be local</blockquote><br />';
				} else {
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $newusername . '.xml')) {
						$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">username already exists</blockquote><br />';
					} else {
						if (strtolower($newusername) == strtolower($newuserpsw)) {
							$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">username and password cannot be equal</blockquote><br />';
						} else {
							if ($newuserpsw != $newuserpswc) {
								$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">confirm password mismatch</blockquote><br />';
							} else {
								if (strlen($newuserpsw) < 8) {
									$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">password must be at least 8 characters</blockquote><br />';
								} else {
									if (!preg_match('/[\\\\\'\"\!\.\:\;\[\]\^\$\%\&\*\(\)\}\{\@\#\~\?\/\,\|\=\_\+\-]/', $newuserpsw) || !preg_match('/[a-z]/', $newuserpsw) || !preg_match('/[A-Z]/', $newuserpsw) || !preg_match('/[0-9]/', $newuserpsw)) {
										$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">password must be contain number <strong>0~9</strong>, lower <strong>a~z</strong>, upper <strong>A~Z</strong> and special characters <strong>\\\'",.:;!?^$%&()[]{}@#~\/|=*+-_</strong></blockquote><br />';
									} else {
										if (preg_match('/[^a-zA-Z0-9\\\\\'\"\!\.\:\;\[\]\^\$\%\&\*\(\)\}\{\@\#\~\?\/\,\|\=\_\+\-]/', $newuserpsw)) {
											$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">special characters allowed <strong>\\\'",.:;!?^$%&()[]{}@#~\/|=*+-_</strong></blockquote><br />';
										} else {
											$checkgroups = 0;
											if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $newusertype . '.xml')) {
												$checkgroups = 1;
												$checkgroupsxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $newusertype . '.xml', true)))));
												$mcrykey = pack('H*', hash('sha256', hash('sha512', $checkgroupsxml->settings->groupname)));
												$checkgroupsarray = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($checkgroupsxml->settings->groupsettings), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($checkgroupsxml->settings->groupsettings), 0, $iv_size)));
												if ($checkgroupsarray['name'] == $newusertype) { $checkgroups = 0; }
												$checkgroups = 2;
												if ($checkgroupsarray['auth'] == 'Distributed' || $checkgroupsarray['auth'] == $newuserauth) { $checkgroups = 0; }
											} else {
												$checkgroups = 3;
												if ($newusertype == 'Administrators' || $newusertype == 'Auditors' || $newusertype == 'Operators' || $newusertype == 'Users') { $checkgroups = 0; }
											}
											if ($checkgroups > 0) {
												if ($checkgroups == 1) { $message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">user group <strong>' . $newusertype . '</strong> is currupted</blockquote><br />'; }
												if ($checkgroups == 2) { $message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">user group <strong>' . $newusertype . '</strong> must be distributed</blockquote><br />'; }
												if ($checkgroups == 3) { $message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">user group <strong>' . $newusertype . '</strong> not exist</blockquote><br />'; }
											} else {
												$mcrykey = pack('H*', hash('sha256', hash('sha512', $newusername . $newusertype)));
												$passwcrypt = base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykey, md5($newusername . ':' . $realm . ':' . $newuserpsw), MCRYPT_MODE_CBC, $iv));
												$passwchk = base64_encode(md5(substr(hash('whirlpool', hash('sha256', hash('sha384', hash('sha512', $newusername . ':' . $realm . ':' . $newuserpsw)))), 0, -1)));
												$xml = '<?xml version="1.0"?>' . "\n" . '<config>' . "\n" . '	<settings>' . "\n" . '		<username>' . $newusername . '</username>' . "\n" . '		<usertype>' . hash('sha512', $newusername . $newusertype) . '</usertype>' . "\n" . '		<userauth>' . hash('sha512', $newusername . $newuserauth) . '</userauth>' . "\n" . '		<password>' . $passwcrypt . '</password>' . "\n" . '		<passwchk>' . $passwchk . '</passwchk>' . "\n" . '		<passwlck>' . md5($passwcrypt) . '</passwlck>' . "\n" . '		<expiration>' . base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('2000-01-01 00:00:00'))))) . '</expiration>' . "\n" . '	</settings>' . "\n" . '</config>';
												$writexml = fopen($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $newusername . '.xml', 'w');
												fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
												fclose($writexml);
												if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername) && $newuserauth == 'Distributed') { @copy($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $newusername . '.xml', str_replace('\\core', '\\agent\\users', $_SERVER['DOCUMENT_ROOT']) . '\\' . $newusername . '.xml'); }
												$message = '<blockquote style="background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">user <strong>' . $newusername . '</strong> created successfully</blockquote><br />';
												$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user management     user "' . $newusername . '" created successfully in "' . $newusertype . '" group with "' . $newuserauth . '" authentication';
												$newusername = '';
												$newusertype = '';
												$newuserauth = '';
												$newuserpsw = '';
												$newuserpswc = '';
												$newaccordionst = '';
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$message = $message . '<br />';
	}
	
}

$newaccordiongst = '';

$newgroup = array();

if (isset($_POST['newgroupname']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['name'] = $_POST['newgroupname']; } else { $newgroup['name'] = ''; }
if (isset($_POST['newgroupcoreconfig']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['coreconfig'] = $_POST['newgroupcoreconfig']; } else { $newgroup['coreconfig'] = 0; }
if (isset($_POST['newgroupexecutorconfig']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['executorconfig'] = $_POST['newgroupexecutorconfig']; } else { $newgroup['executorconfig'] = 0; }
if (isset($_POST['newgroupserverconfig']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['serverconfig'] = $_POST['newgroupserverconfig']; } else { $newgroup['serverconfig'] = 0; }
if (isset($_POST['newgroupagentconfig']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['agentconfig'] = $_POST['newgroupagentconfig']; } else { $newgroup['agentconfig'] = 0; }
if (isset($_POST['newgroupnodesstatus']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesstatus'] = $_POST['newgroupnodesstatus']; } else { $newgroup['nodesstatus'] = 0; }
if (isset($_POST['newgroupnodesnagiosstatus']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesnagiosstatus'] = $_POST['newgroupnodesnagiosstatus']; } else { $newgroup['nodesnagiosstatus'] = 0; }
if (isset($_POST['newgroupnodessysteminventory']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodessysteminventory'] = $_POST['newgroupnodessysteminventory']; } else { $newgroup['nodessysteminventory'] = 0; }
if (isset($_POST['newgroupnodesinstalledprograms']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesinstalledprograms'] = $_POST['newgroupnodesinstalledprograms']; } else { $newgroup['nodesinstalledprograms'] = 0; }
if (isset($_POST['newgroupnodesprocesscontrol']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesprocesscontrol'] = $_POST['newgroupnodesprocesscontrol']; } else { $newgroup['nodesprocesscontrol'] = 0; }
if (isset($_POST['newgroupnodesservicecontrol']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesservicecontrol'] = $_POST['newgroupnodesservicecontrol']; } else { $newgroup['nodesservicecontrol'] = 0; }
if (isset($_POST['newgroupnodesnetworkstats']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesnetworkstats'] = $_POST['newgroupnodesnetworkstats']; } else { $newgroup['nodesnetworkstats'] = 0; }
if (isset($_POST['newgroupnodesscheduledtasks']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesscheduledtasks'] = $_POST['newgroupnodesscheduledtasks']; } else { $newgroup['nodesscheduledtasks'] = 0; }
if (isset($_POST['newgroupnodeseventviewer']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodeseventviewer'] = $_POST['newgroupnodeseventviewer']; } else { $newgroup['nodeseventviewer'] = 0; }
if (isset($_POST['newgroupsysteminfo']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['systeminfo'] = $_POST['newgroupsysteminfo']; } else { $newgroup['systeminfo'] = 0; }
if (isset($_POST['newgroupnagiosstatus']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nagiosstatus'] = $_POST['newgroupnagiosstatus']; } else { $newgroup['nagiosstatus'] = 0; }
if (isset($_POST['newgroupsysteminventory']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['systeminventory'] = $_POST['newgroupsysteminventory']; } else { $newgroup['systeminventory'] = 0; }
if (isset($_POST['newgroupinstalledprograms']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['installedprograms'] = $_POST['newgroupinstalledprograms']; } else { $newgroup['installedprograms'] = 0; }
if (isset($_POST['newgroupwmiexplorer']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['wmiexplorer'] = $_POST['newgroupwmiexplorer']; } else { $newgroup['wmiexplorer'] = 0; }
if (isset($_POST['newgroupsystemshutdown']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['systemshutdown'] = $_POST['newgroupsystemshutdown']; } else { $newgroup['systemshutdown'] = 0; }
if (isset($_POST['newgroupprocesscontrol']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['processcontrol'] = $_POST['newgroupprocesscontrol']; } else { $newgroup['processcontrol'] = 0; }
if (isset($_POST['newgroupservicecontrol']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['servicecontrol'] = $_POST['newgroupservicecontrol']; } else { $newgroup['servicecontrol'] = 0; }
if (isset($_POST['newgroupnetworkstats']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['networkstats'] = $_POST['newgroupnetworkstats']; } else { $newgroup['networkstats'] = 0; }
if (isset($_POST['newgroupscheduledtasks']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['scheduledtasks'] = $_POST['newgroupscheduledtasks']; } else { $newgroup['scheduledtasks'] = 0; }
if (isset($_POST['newgroupeventviewer']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['eventviewer'] = $_POST['newgroupeventviewer']; } else { $newgroup['eventviewer'] = 0; }
if (isset($_POST['newgroupsystemregistry']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['systemregistry'] = $_POST['newgroupsystemregistry']; } else { $newgroup['systemregistry'] = 0; }
if (isset($_POST['newgroupcommandline']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['commandline'] = $_POST['newgroupcommandline']; } else { $newgroup['commandline'] = 0; }
if (isset($_POST['newgroupfilebrowser']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['filebrowser'] = $_POST['newgroupfilebrowser']; } else { $newgroup['filebrowser'] = 0; }
if (isset($_POST['newgroupfiletransfer']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['filetransfer'] = $_POST['newgroupfiletransfer']; } else { $newgroup['filetransfer'] = 0; }
if (isset($_POST['newgroupchangesettings']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['changesettings'] = $_POST['newgroupchangesettings']; } else { $newgroup['changesettings'] = 0; }
if (isset($_POST['newgroupusermanagement']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['usermanagement'] = $_POST['newgroupusermanagement']; } else { $newgroup['usermanagement'] = 0; }
if (isset($_POST['newgroupauditlog']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['auditlog'] = $_POST['newgroupauditlog']; } else { $newgroup['auditlog'] = 0; }
if (isset($_POST['newgroupnodesstatusf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesstatusf'] = $_POST['newgroupnodesstatusf']; } else { $newgroup['nodesstatusf'] = ''; }
if (isset($_POST['newgroupnodescommandf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodescommandf'] = $_POST['newgroupnodescommandf']; } else { $newgroup['nodescommandf'] = ''; }
if (isset($_POST['newgroupnodesnagiosstatusf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesnagiosstatusf'] = $_POST['newgroupnodesnagiosstatusf']; } else { $newgroup['nodesnagiosstatusf'] = ''; }
if (isset($_POST['newgroupnodesinstalledprogramsf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesinstalledprogramsf'] = $_POST['newgroupnodesinstalledprogramsf']; } else { $newgroup['nodesinstalledprogramsf'] = ''; }
if (isset($_POST['newgroupnodesprocesscontrolf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesprocesscontrolf'] = $_POST['newgroupnodesprocesscontrolf']; } else { $newgroup['nodesprocesscontrolf'] = ''; }
if (isset($_POST['newgroupnodesservicecontrolf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesservicecontrolf'] = $_POST['newgroupnodesservicecontrolf']; } else { $newgroup['nodesservicecontrolf'] = ''; }
if (isset($_POST['newgroupnodesnetworkstatsf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesnetworkstatsf'] = $_POST['newgroupnodesnetworkstatsf']; } else { $newgroup['nodesnetworkstatsf'] = ''; }
if (isset($_POST['newgroupnodesscheduledtasksf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodesscheduledtasksf'] = $_POST['newgroupnodesscheduledtasksf']; } else { $newgroup['nodesscheduledtasksf'] = ''; }
if (isset($_POST['newgroupnodeseventviewerf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nodeseventviewerf'] = $_POST['newgroupnodeseventviewerf']; } else { $newgroup['nodeseventviewerf'] = ''; }
if (isset($_POST['newgroupnagiosstatusf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['nagiosstatusf'] = $_POST['newgroupnagiosstatusf']; } else { $newgroup['nagiosstatusf'] = ''; }
if (isset($_POST['newgroupinstalledprogramsf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['installedprogramsf'] = $_POST['newgroupinstalledprogramsf']; } else { $newgroup['installedprogramsf'] = ''; }
if (isset($_POST['newgroupprocesscontrolf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['processcontrolf'] = $_POST['newgroupprocesscontrolf']; } else { $newgroup['processcontrolf'] = ''; }
if (isset($_POST['newgroupservicecontrolf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['servicecontrolf'] = $_POST['newgroupservicecontrolf']; } else { $newgroup['servicecontrolf'] = ''; }
if (isset($_POST['newgroupnetworkstatsf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['networkstatsf'] = $_POST['newgroupnetworkstatsf']; } else { $newgroup['networkstatsf'] = ''; }
if (isset($_POST['newgroupscheduledtasksf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['scheduledtasksf'] = $_POST['newgroupscheduledtasksf']; } else { $newgroup['scheduledtasksf'] = ''; }
if (isset($_POST['newgroupeventviewerf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['eventviewerf'] = $_POST['newgroupeventviewerf']; } else { $newgroup['eventviewerf'] = ''; }
if (isset($_POST['newgroupcommandlinef']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['commandlinef'] = $_POST['newgroupcommandlinef']; } else { $newgroup['commandlinef'] = ''; }
if (isset($_POST['newgroupfilebrowserf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['filebrowserf'] = $_POST['newgroupfilebrowserf']; } else { $newgroup['filebrowserf'] = ''; }
if (isset($_POST['newgroupregeditf']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['regeditf'] = $_POST['newgroupregeditf']; } else { $newgroup['regeditf'] = ''; }
if (isset($_POST['newgroupauth']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['auth'] = $_POST['newgroupauth']; } else { $newgroup['auth'] = 'Local'; }
if (isset($_POST['newgrouppwdexp']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['pwdexp'] = $_POST['newgrouppwdexp']; } else { $newgroup['pwdexp'] = 1; }
if (isset($_POST['newgroupdisable']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['disable'] = $_POST['newgroupdisable']; } else { $newgroup['disable'] = 7; }
if (isset($_POST['newgroupsastarttime']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { if ($_POST['newgroupsastarttime'] != '') { $newgroup['sastarttime'] = $_POST['newgroupsastarttime']; } else { $newgroup['sastarttime'] = '00:00:00'; } } else { $newgroup['sastarttime'] = '00:00:00'; }
if (isset($_POST['newgroupsastoptime']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { if ($_POST['newgroupsastoptime'] != '') { $newgroup['sastoptime'] = $_POST['newgroupsastoptime']; } else { $newgroup['sastoptime'] = '00:00:00'; } } else { $newgroup['sastoptime'] = '00:00:00'; }
if (!isset($_POST['newgroupsasunday']) && !isset($_POST['newgroupsamonday']) && !isset($_POST['newgroupsatuesday']) && !isset($_POST['newgroupsawednesday']) && !isset($_POST['newgroupsathursday']) && !isset($_POST['newgroupsafriday']) && !isset($_POST['newgroupsasaturday'])) {
	$newgroup['sasunday'] = 'on';
	$newgroup['samonday'] = 'on';
	$newgroup['satuesday'] = 'on';
	$newgroup['sawednesday'] = 'on';
	$newgroup['sathursday'] = 'on';
	$newgroup['safriday'] = 'on';
	$newgroup['sasaturday'] = 'on';
} else {
	if (isset($_POST['newgroupsasunday']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['sasunday'] = $_POST['newgroupsasunday']; } else { $newgroup['sasunday'] = ''; }
	if (isset($_POST['newgroupsamonday']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['samonday'] = $_POST['newgroupsamonday']; } else { $newgroup['samonday'] = ''; }
	if (isset($_POST['newgroupsatuesday']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['satuesday'] = $_POST['newgroupsatuesday']; } else { $newgroup['satuesday'] = ''; }
	if (isset($_POST['newgroupsawednesday']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['sawednesday'] = $_POST['newgroupsawednesday']; } else { $newgroup['sawednesday'] = ''; }
	if (isset($_POST['newgroupsathursday']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['sathursday'] = $_POST['newgroupsathursday']; } else { $newgroup['sathursday'] = ''; }
	if (isset($_POST['newgroupsafriday']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['safriday'] = $_POST['newgroupsafriday']; } else { $newgroup['safriday'] = ''; }
	if (isset($_POST['newgroupsasaturday']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['sasaturday'] = $_POST['newgroupsasaturday']; } else { $newgroup['sasaturday'] = ''; }
}
if (isset($_POST['newgroupbadaut']) && !isset($_POST['editgroupcancel']) && !isset($_POST['deletegroup'])) { $newgroup['badaut'] = $_POST['newgroupbadaut']; } else { $newgroup['badaut'] = 0; }

if (!isset($_SESSION['changegroup'])) {
	$_SESSION['changegroup'] = '';
}

if (isset($_GET['changegroup'])) {
	$_SESSION['changegroup'] = $_GET['changegroup'];
}

if (isset($_POST['editgroupcancel'])) {
	$_SESSION['changegroup'] = '';
}

if ($_SESSION['changegroup'] != '' && !isset($_POST['newgroupname'])) {
	$mcrykey = pack('H*', hash('sha256', hash('sha512', $_SESSION['changegroup'])));
	$groupsxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $_SESSION['changegroup'] . '.xml', true)))));
	$newgroup = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($groupsxml->settings->groupsettings), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($groupsxml->settings->groupsettings), 0, $iv_size)));
	$newaccordiongst = ' class="active"';
}


if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) {

	if (isset($_POST['newgroupname']) && !isset($_POST['editgroupcancel']) && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
		if (isset($_POST['deletegroup'])) {
			@unlink($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $_SESSION['changegroup'] . '.xml');
			$message = '<blockquote style="background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">group <strong>' . $_SESSION['changegroup'] . '</strong> deleted</blockquote><br />';
			$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user management     group "' . $_SESSION['changegroup'] . '" deleted';
			$_SESSION['changegroup'] = '';
		} else {
			$newaccordiongst = ' class="active"';
			if ($newgroup['name'] == '') {
				$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">all fields must be completed</blockquote><br />';
			} else {
				if (strtolower($newgroup['name']) == 'administrators' || strtolower($newgroup['name']) == 'operators' || strtolower($newgroup['name']) == 'users' || strtolower($newgroup['name']) == 'auditors' || (isset($_POST['addgroup']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $newgroup['name'] . '.xml')) || (isset($_POST['editgroup']) && $_SESSION['changegroup'] != $newgroup['name'] && file_exists($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $newgroup['name'] . '.xml'))) {
					$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">groupname already exists</blockquote><br />';
				} else {
					if (preg_match('/[^a-zA-Z0-9 ]/', $newgroup['name'])) {
						$message = '<blockquote style="background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;">special characters are not allowed except spaces</blockquote><br />';
					} else {
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $_SESSION['changegroup'] . '.xml')) { @unlink($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $_SESSION['changegroup'] . '.xml'); }
						$mcrykey = pack('H*', hash('sha256', hash('sha512', $newgroup['name'])));
						$newgroupxml = base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $mcrykey, serialize($newgroup), MCRYPT_MODE_CBC, $iv));
						$xml = '<?xml version="1.0"?>' . "\n" . '<config>' . "\n" . '	<settings>' . "\n" . '		<groupname>' . $newgroup['name'] . '</groupname>' . "\n" . '		<groupauth>' . hash('sha512', $newgroup['name'] . $newgroup['auth']) . '</groupauth>' . "\n" . '		<groupsettings>' . $newgroupxml . '</groupsettings>' . "\n" . '	</settings>' . "\n" . '</config>';
						$writexml = fopen($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $newgroup['name'] . '.xml', 'w');
						fwrite($writexml, base64_encode(base64_encode(base64_encode($xml))));
						fclose($writexml);
						if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername) && $newgroup['auth'] == 'Distributed') { @copy($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $newgroup['name'] . '.xml', str_replace('\\core', '\\agent\\groups', $_SERVER['DOCUMENT_ROOT']) . '\\' . $newgroup['name'] . '.xml'); }
						if (isset($_POST['addgroup'])) { $msgconf = 'created'; } else { $msgconf = 'saved'; }
						$message = '<blockquote style="background-color:#0072C6; color:#FFFFFF; border-left-color:#324886;">group <strong>' . $newgroup['name'] . '</strong> ' . $msgconf . ' successfully</blockquote><br />';
						$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     user management     group "' . $newgroup['name'] . '" ' . $msgconf . ' successfully';
						$_SESSION['changegroup'] = '';
						$newaccordiongst = '';
						$newgroup['name'] = '';
						$newgroup['coreconfig'] = 0;
						$newgroup['executorconfig'] = 0;
						$newgroup['serverconfig'] = 0;
						$newgroup['agentconfig'] = 0;
						$newgroup['nodesstatus'] = 0;
						$newgroup['nodesnagiosstatus'] = 0;
						$newgroup['nodessysteminventory'] = 0;
						$newgroup['nodesinstalledprograms'] = 0;
						$newgroup['nodesprocesscontrol'] = 0;
						$newgroup['nodesservicecontrol'] = 0;
						$newgroup['nodesnetworkstats'] = 0;
						$newgroup['nodesscheduledtasks'] = 0;
						$newgroup['nodeseventviewer'] = 0;
						$newgroup['systeminfo'] = 0;
						$newgroup['nagiosstatus'] = 0;
						$newgroup['systeminventory'] = 0;
						$newgroup['installedprograms'] = 0;
						$newgroup['wmiexplorer'] = 0;
						$newgroup['systemshutdown'] = 0;
						$newgroup['processcontrol'] = 0;
						$newgroup['servicecontrol'] = 0;
						$newgroup['networkstats'] = 0;
						$newgroup['scheduledtasks'] = 0;
						$newgroup['eventviewer'] = 0;
						$newgroup['systemregistry'] = 0;
						$newgroup['commandline'] = 0;
						$newgroup['filebrowser'] = 0;
						$newgroup['changesettings'] = 0;
						$newgroup['usermanagement'] = 0;
						$newgroup['auditlog'] = 0;
						$newgroup['nodesstatusf'] = '';
						$newgroup['nodescommandf'] = '';
						$newgroup['nodesnagiosstatusf'] = '';
						$newgroup['nodesinstalledprogramsf'] = '';
						$newgroup['nodesprocesscontrolf'] = '';
						$newgroup['nodesservicecontrolf'] = '';
						$newgroup['nodesnetworkstatsf'] = '';
						$newgroup['nodesscheduledtasksf'] = '';
						$newgroup['nodeseventviewerf'] = '';
						$newgroup['nagiosstatusf'] = '';
						$newgroup['installedprogramsf'] = '';
						$newgroup['processcontrolf'] = '';
						$newgroup['servicecontrolf'] = '';
						$newgroup['networkstatsf'] = '';
						$newgroup['scheduledtasksf'] = '';
						$newgroup['eventviewerf'] = '';
						$newgroup['commandlinef'] = '';
						$newgroup['filebrowserf'] = '';
						$newgroup['regeditf'] = '';
						$newgroup['auth'] = 'Local';
						$newgroup['pwdexp'] = 1;
						$newgroup['disable'] = 7;
						$newgroup['sastarttime'] = '00:00:00';
						$newgroup['sastoptime'] = '00:00:00';
						$newgroup['sasunday'] = 'on';
						$newgroup['samonday'] = 'on'; 
						$newgroup['satuesday'] = 'on';
						$newgroup['sawednesday'] = 'on';
						$newgroup['sathursday'] = 'on';
						$newgroup['safriday'] = 'on';
						$newgroup['sasaturday'] = 'on';
						$newgroup['badaut'] = 0;
					}
				}
			}
			$message = $message . '<br />';
		}
	}
	
}

?>

<?php $grouplist = scandir($_SERVER['DOCUMENT_ROOT'] . '\\groups\\'); ?>

<script type="text/javascript">
	function changeuser(UserFile,UserName,UserType,UserAuth){
		SelectedAdmin = '';
		SelectedAudit = '';
		SelectedOpera = '';
		SelectedUsers = '';
		SelectedLocal = '';
		SelectedDistr = '';
		FieldROnlLoca = '';
		FieldReadOnly = '';
		FieldHdFields = '';
		StarInforLock = '';
		StarInforLoca = '';
		StarInforDist = '';
		AdminLockIcon = '';
		CheckServConn = '<?php if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername)) { echo 'Connected'; } ?>';
		UserStatusMsg = '';
		if (UserType == 'Administrators') { SelectedAdmin = ' selected="selected"'; }
		if (UserType == 'Auditors') { SelectedAudit = ' selected="selected"'; }
		if (UserType == 'Operators') { SelectedOpera = ' selected="selected"'; }
		if (UserType == 'Users') { SelectedUsers = ' selected="selected"'; }
		<?php foreach ($grouplist as $group) { if (pathinfo($group)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group) > 0) { $group = str_replace('.xml', '', $group); ?>
		if (UserType == '<?php echo $group; ?>') { Selected<?php echo str_replace(' ', '_', $group); ?> = ' selected="selected"'; } else { Selected<?php echo str_replace(' ', '_', $group); ?> = ''; }
		<?php } } ?>
		if (UserAuth == 'Local') { SelectedLocal = ' selected="selected"'; }
		if (UserAuth == 'Distributed') { SelectedDistr = ' selected="selected"'; }
		if (UserName == 'Administrator') { StarInforLock = '* '; UserStatusMsg = '<br /><i>* change allowed for password only</i>'; FieldReadOnly = ' disabled="disabled"'; FieldHdFields = '<input type="hidden" id="editusertype" name="editusertype" value="Administrators" /><input type="hidden" id="edituserauth" name="edituserauth" value="Local" />'; AdminLockIcon = '&nbsp;&nbsp;<div class="icon-locked-2" style="font-size:12px; color:#cdab16;" title="Protected"></div>'; }
		if (UserAuth == 'Local' && CheckServConn == 'Connected' && UserName != 'Administrator') { StarInforLoca = '* '; UserStatusMsg = '<br /><i>* change allowed from server only</i>'; FieldROnlLoca = ' disabled="disabled"'; }
		if (UserAuth == 'Distributed' && CheckServConn == 'Connected') { StarInforDist = '* '; UserStatusMsg = '<br /><i>* change allowed from server only</i>'; FieldReadOnly = ' disabled="disabled"'; FieldHdFields = '<input type="hidden" id="editusertype" name="editusertype" value="' + UserType + '" /><input type="hidden" id="edituserauth" name="edituserauth" value="' + UserAuth + '" />'; }
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-user" style="position:inherit;"></div>&nbsp; <strong>Change</strong> User Information</span>',
			'content'     : '<form id="changeuserform" name="changeuserform" method="post" action="users.php"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td style="font-size:12px;" align="center" colspan="2"><strong>' + UserName + '</strong>' + AdminLockIcon + UserStatusMsg + '</td></tr><tr><td style="font-size:12px;">' + StarInforLock + 'Password:</td><td style="font-size:12px;"><input type="password" id="edituserpsw" name="edituserpsw" placeholder="&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px; padding-left:4px; padding-right:4px; font-size:12px;" value=""></td></tr><tr><td style="font-size:12px;">' + StarInforLock + 'Confirmation:</td><td style="font-size:12px;"><input type="password" id="edituserpswc" name="edituserpswc" placeholder="&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;&#x25cf;" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:105px; padding-left:4px; padding-right:4px; font-size:12px;" value=""></td></tr><tr><td style="font-size:12px;">' + StarInforDist + 'Type:</td><td style="font-size:12px;"><select id="editusertype" name="editusertype" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa;"><option value="Administrators"' + SelectedAdmin + FieldReadOnly + '>&nbsp;Administrators&nbsp;&nbsp;</option><option value="Auditors"' + SelectedAudit + FieldReadOnly + '>&nbsp;Auditors&nbsp;&nbsp;</option><option value="Operators"' + SelectedOpera + FieldReadOnly + '>&nbsp;Operators&nbsp;&nbsp;</option><option value="Users"' + SelectedUsers + FieldReadOnly + '>&nbsp;Users&nbsp;&nbsp;</option><?php foreach ($grouplist as $group) { if (pathinfo($group)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group) > 0) { $group = str_replace('.xml', '', $group); ?><option value="<?php echo $group; ?>"' + Selected<?php echo str_replace(' ', '_', $group); ?> + FieldReadOnly + '>&nbsp;<?php echo str_replace(' ', '&nbsp;', $group); ?>&nbsp;&nbsp;</option><?php } } ?></select></td></tr><tr><td style="font-size:12px;">' + StarInforLoca + StarInforDist + 'Authentication:</td><td style="font-size:12px;"><select id="edituserauth" name="edituserauth" style="font-family:\'Segoe UI Light\',\'Open Sans\',Verdana,Arial,Helvetica,sans-serif; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa;"><option value="Local"' + SelectedLocal + FieldReadOnly + '>&nbsp;Local&nbsp;&nbsp;</option><option value="Distributed"' + SelectedDistr + FieldROnlLoca + FieldReadOnly + '>&nbsp;Distributed&nbsp;&nbsp;</option></select></td></tr></table><input type="checkbox" id="deleteuserconf" name="deleteuserconf" ' + FieldReadOnly + ' /><span class="helper" style="font-size:12px;">&nbsp;&nbsp;' + StarInforDist + 'Delete This User</span><input type="hidden" id="edituserfile" name="edituserfile" value="' + UserFile + '" /><input type="hidden" id="editusername" name="editusername" value="' + UserName + '" />' + FieldHdFields + '</form>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '55px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 136) + 'px'
			},
			'buttons'     : {
				<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) { ?>
				'Change'     : {
				'action': function(){
						document.getElementById("changeuserform").submit();
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
			<h1>User<small>management</small></h1>
			<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-users-button big page-back"></a>
		</div>
	</div>
</div>

<br />

<div class="page" id="page-index">
	<div class="page-region">
		<div class="page-region-content">
			<div class="grid">
				<div class="row">
		            <div class="span2"></div>
		            <div class="span6">
					
					<?php echo $message; ?>
					
					<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) { ?>
					<h2><img src="/img/uss.png" width="32" height="32" title="Users" />&nbsp;Users:</h2>
					<ul class="accordion" data-role="accordion">
						<li<?php echo $newaccordionst; ?>>
							<a href="#" style="font-size:16px; color:#000;">Add New:</a>
							<div>
		                   	<form method="post" action="users.php">
								<div class="input-control text">
			                       	<h3><img src="/img/ussa.png" width="20" height="20" title="Username" />&nbsp;Username:</h3>
									<input type="text" id="newusername" name="newusername" placeholder="Username" value="<?php echo $newusername; ?>" />
									<button class="btn-clear"></button>
								</div>
								<div class="input-control text">
			                       	<h3><div class="icon-key" style="font-size:17px; color:#b5b5b5" title="Password"></div>&nbsp;Password:</h3>
									<input type="password" id="newuserpsw" name="newuserpsw" placeholder="Password" value="<?php echo $newuserpsw; ?>" />
									<button class="btn-clear"></button>
								</div>
								<div class="input-control text">
			                       	<h3><div class="icon-key" style="font-size:17px; color:#b5b5b5" title="Password Confirm"></div>&nbsp;Password Confirm:</h3>
									<input type="password" id="newuserpswc" name="newuserpswc" placeholder="Password Confirm" value="<?php echo $newuserpswc; ?>" />
									<button class="btn-clear"></button>
								</div>
								<div class="input-control select">
			                       	<h3><img src="/img/adma.png" width="20" height="20" title="Type" />&nbsp;Type:</h3>
									<select id="newusertype" name="newusertype">
										<option value=""></option>
										<option value="Administrators" <?php if ($newusertype == 'Administrators') { echo 'selected'; } ?>>Administrators</option>
										<option value="Auditors" <?php if ($newusertype == 'Auditors') { echo 'selected'; } ?>>Auditors</option>
										<option value="Operators" <?php if ($newusertype == 'Operators') { echo 'selected'; } ?>>Operators</option>
										<option value="Users" <?php if ($newusertype == 'Users') { echo 'selected'; } ?>>Users</option>
										<?php foreach ($grouplist as $group) { if (pathinfo($group)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group) > 0) { $group = str_replace('.xml', '', $group); ?>
										<option value="<?php echo $group; ?>" <?php if ($newusertype == $group) { echo 'selected'; } ?>><?php echo $group; ?></option>
										<?php } } ?>
									</select>
								</div>
								<div class="input-control select">
			                       	<h3><div class="icon-share-2" style="font-size:17px; color:#b5b5b5" title="Authentication"></div>&nbsp;Authentication:</h3>
									<select id="newuserauth" name="newuserauth">
										<option value="Local" <?php if ($newuserauth == 'Local') { echo 'selected'; } ?>>Local</option>
										<?php if (($agentstatus == 'con' || $serverstatus == 'cfg') && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername)) { ?>
										<option disabled="disabled">Distributed allowed from server only</option>
										<?php } else { ?>
										<option value="Distributed" <?php if ($newuserauth == 'Distributed') { echo 'selected'; } ?>>Distributed</option>
										<?php } ?>
									</select>
								</div>
								<br />
				                    <input type="hidden" id="newaccount" name="newaccount" />
									<input type="submit" id="adduser" name="adduser" style="background-color:#0072C6;" value="Add"/>
							</form>
							</div>
						</li>
					</ul>
					<br />
					<?php } ?>
					<h2><img src="/img/adm.png" width="32" height="32" title="Groups" />&nbsp;Groups:</h2>
					<?php if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername)) { ?>
					<div style="font-size:12px;">* distributed group management allowed from server only</div>
					<?php } ?>
					<ul class="accordion" data-role="accordion">
						<li<?php echo $newaccordiongst; ?>>
							<a href="#" style="font-size:16px; color:#000;"><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) { ?><?php if ($_SESSION['changegroup'] == '') { echo 'Add New:'; } else { echo 'Edit ' . $_SESSION['changegroup'] . ':'; } ?><?php } else { ?>Info:<?php } ?></a>
							<div>
		                   	<form method="post" action="users.php"<?php if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername) && $_SESSION['changegroup'] != '' && $newgroup['auth'] == 'Distributed') { echo ' disabled="disabled"'; } ?>>
								<div class="input-control text">
			                       	<h3><img src="/img/adma.png" width="20" height="20" title="Groupname" />&nbsp;Groupname:</h3>
									<input type="text" id="newgroupname" name="newgroupname" placeholder="Groupname" value="<?php echo $newgroup['name']; ?>" />
									<button class="btn-clear"></button>
								</div>
								<br />
								<div class="input-control select">
									<h3><div class="icon-locked-2" style="font-size:17px; color:#b5b5b5" title="Permissions"></div>&nbsp;Permissions:</h3>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
										<tr>
											<td width="50%" style="font-size:13px;">Core Config:</td>
											<td width="50%">
												<select id="newgroupcoreconfig" name="newgroupcoreconfig" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Operators, Users, Auditors" <?php if ($newgroup['coreconfig'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators" <?php if ($newgroup['coreconfig'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Executor Config:</td>
											<td>
												<select id="newgroupexecutorconfig" name="newgroupexecutorconfig" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Users, Auditors" <?php if ($newgroup['executorconfig'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators" <?php if ($newgroup['executorconfig'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Server Config:</td>
											<td>
												<select id="newgroupserverconfig" name="newgroupserverconfig" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Operators, Users, Auditors" <?php if ($newgroup['serverconfig'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators" <?php if ($newgroup['serverconfig'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Agent Config:</td>
											<td>
												<select id="newgroupagentconfig" name="newgroupagentconfig" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Users, Auditors" <?php if ($newgroup['agentconfig'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators" <?php if ($newgroup['agentconfig'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
									</table>
									<?php if ($serverstatus == 'run' || $newgroup['auth'] == 'Distributed') { ?>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
										<tr>
											<td width="50%" style="background-color:#eeeaf6; font-size:13px;">Nodes Status:</td>
											<td width="50%" style="background-color:#eeeaf6;">
												<select id="newgroupnodesstatus" name="newgroupnodesstatus" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nodesstatus'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Users" <?php if ($newgroup['nodesstatus'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Operators" <?php if ($newgroup['nodesstatus'] == 2) { echo 'selected'; } ?>>Restart Agents</option>
													<option value="3" title="Administrators" <?php if ($newgroup['nodesstatus'] == 3) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Nagios Status:</td>
											<td style="background-color:#eeeaf6;">
												<select id="newgroupnodesnagiosstatus" name="newgroupnodesnagiosstatus" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nodesnagiosstatus'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators, Users" <?php if ($newgroup['nodesnagiosstatus'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes System Inventory:</td>
											<td style="background-color:#eeeaf6;">
												<select id="newgroupnodessysteminventory" name="newgroupnodessysteminventory" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nodessysteminventory'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators, Users" <?php if ($newgroup['nodessysteminventory'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Installed Programs:</td>
											<td style="background-color:#eeeaf6;">
												<select id="newgroupnodesinstalledprograms" name="newgroupnodesinstalledprograms" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nodesinstalledprograms'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Users" <?php if ($newgroup['nodesinstalledprograms'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators, Operators" <?php if ($newgroup['nodesinstalledprograms'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Process Control:</td>
											<td style="background-color:#eeeaf6;">
												<select id="newgroupnodesprocesscontrol" name="newgroupnodesprocesscontrol" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nodesprocesscontrol'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Users" <?php if ($newgroup['nodesprocesscontrol'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators, Operators" <?php if ($newgroup['nodesprocesscontrol'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Service Control:</td>
											<td style="background-color:#eeeaf6;">
												<select id="newgroupnodesservicecontrol" name="newgroupnodesservicecontrol" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nodesservicecontrol'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Users" <?php if ($newgroup['nodesservicecontrol'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators, Operators" <?php if ($newgroup['nodesservicecontrol'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Network Stats:</td>
											<td style="background-color:#eeeaf6;">
												<select id="newgroupnodesnetworkstats" name="newgroupnodesnetworkstats" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nodesnetworkstats'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators, Users" <?php if ($newgroup['nodesnetworkstats'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Scheduled Tasks:</td>
											<td style="background-color:#eeeaf6;">
												<select id="newgroupnodesscheduledtasks" name="newgroupnodesscheduledtasks" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nodesscheduledtasks'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Users" <?php if ($newgroup['nodesscheduledtasks'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators, Operators" <?php if ($newgroup['nodesscheduledtasks'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Event Viewer:</td>
											<td style="background-color:#eeeaf6;">
												<select id="newgroupnodeseventviewer" name="newgroupnodeseventviewer" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nodeseventviewer'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators, Users" <?php if ($newgroup['nodeseventviewer'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
									</table>
									<?php } else { ?>
										<input type="hidden" id="newgroupnodesstatus" name="newgroupnodesstatus" value="<?php echo $newgroup['nodesstatus'] ?>" />
										<input type="hidden" id="newgroupnodesnagiosstatus" name="newgroupnodesnagiosstatus" value="<?php echo $newgroup['nodesnagiosstatus'] ?>" />
										<input type="hidden" id="newgroupnodessysteminventory" name="newgroupnodessysteminventory" value="<?php echo $newgroup['nodessysteminventory'] ?>" />
										<input type="hidden" id="newgroupnodesinstalledprograms" name="newgroupnodesinstalledprograms" value="<?php echo $newgroup['nodesinstalledprograms'] ?>" />
										<input type="hidden" id="newgroupnodesprocesscontrol" name="newgroupnodesprocesscontrol" value="<?php echo $newgroup['nodesprocesscontrol'] ?>" />
										<input type="hidden" id="newgroupnodesservicecontrol" name="newgroupnodesservicecontrol" value="<?php echo $newgroup['nodesservicecontrol'] ?>" />
										<input type="hidden" id="newgroupnodesnetworkstats" name="newgroupnodesnetworkstats" value="<?php echo $newgroup['nodesnetworkstats'] ?>" />
										<input type="hidden" id="newgroupnodesscheduledtasks" name="newgroupnodesscheduledtasks" value="<?php echo $newgroup['nodesscheduledtasks'] ?>" />
										<input type="hidden" id="newgroupnodeseventviewer" name="newgroupnodeseventviewer" value="<?php echo $newgroup['nodeseventviewer'] ?>" />
									<?php } ?>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
										<tr>
											<td width="50%" style="font-size:13px;">System Info:</td>
											<td width="50%">
												<select id="newgroupsysteminfo" name="newgroupsysteminfo" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['systeminfo'] == 0) { echo 'selected'; } ?>>Minimal</option>
													<option value="1" title="Users" <?php if ($newgroup['systeminfo'] == 1) { echo 'selected'; } ?>>Light</option>
													<option value="2" title="Operators" <?php if ($newgroup['systeminfo'] == 2) { echo 'selected'; } ?>>Partial</option>
													<option value="3" title="Administrators" <?php if ($newgroup['systeminfo'] == 3) { echo 'selected'; } ?>>Complete</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Nagios Status:</td>
											<td>
												<select id="newgroupnagiosstatus" name="newgroupnagiosstatus" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['nagiosstatus'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators, Users" <?php if ($newgroup['nagiosstatus'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">System Inventory:</td>
											<td>
												<select id="newgroupsysteminventory" name="newgroupsysteminventory" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['systeminventory'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators, Users" <?php if ($newgroup['systeminventory'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Installed Programs:</td>
											<td>
												<select id="newgroupinstalledprograms" name="newgroupinstalledprograms" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['installedprograms'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Users" <?php if ($newgroup['installedprograms'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators, Operators" <?php if ($newgroup['installedprograms'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">WMI Explorer:</td>
											<td>
												<select id="newgroupwmiexplorer" name="newgroupwmiexplorer" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['wmiexplorer'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators, Users" <?php if ($newgroup['wmiexplorer'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">System Shutdown:</td>
											<td>
												<select id="newgroupsystemshutdown" name="newgroupsystemshutdown" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Users, Auditors" <?php if ($newgroup['systemshutdown'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators" <?php if ($newgroup['systemshutdown'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
									</table>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
										<tr>
											<td width="50%" style="font-size:13px;">Process Control:</td>
											<td width="50%">
												<select id="newgroupprocesscontrol" name="newgroupprocesscontrol" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['processcontrol'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Users" <?php if ($newgroup['processcontrol'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators, Operators" <?php if ($newgroup['processcontrol'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Service Control:</td>
											<td>
												<select id="newgroupservicecontrol" name="newgroupservicecontrol" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['servicecontrol'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Users" <?php if ($newgroup['servicecontrol'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators, Operators" <?php if ($newgroup['servicecontrol'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Network Stats:</td>
											<td>
												<select id="newgroupnetworkstats" name="newgroupnetworkstats" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['networkstats'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators, Users" <?php if ($newgroup['networkstats'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Scheduled Tasks:</td>
											<td>
												<select id="newgroupscheduledtasks" name="newgroupscheduledtasks" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['scheduledtasks'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Users" <?php if ($newgroup['scheduledtasks'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators, Operators" <?php if ($newgroup['scheduledtasks'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Event Viewer:</td>
											<td>
												<select id="newgroupeventviewer" name="newgroupeventviewer" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Auditors" <?php if ($newgroup['eventviewer'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Operators, Users" <?php if ($newgroup['eventviewer'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">System Registry:</td>
											<td>
												<select id="newgroupsystemregistry" name="newgroupsystemregistry" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Operators, Users, Auditors" <?php if ($newgroup['systemregistry'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="" <?php if ($newgroup['systemregistry'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators" <?php if ($newgroup['systemregistry'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Command Line:</td>
											<td>
												<select id="newgroupcommandline" name="newgroupcommandline" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Operators, Users, Auditors" <?php if ($newgroup['commandline'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators" <?php if ($newgroup['commandline'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">File Browser:</td>
											<td>
												<select id="newgroupfilebrowser" name="newgroupfilebrowser" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Operators, Users, Auditors" <?php if ($newgroup['filebrowser'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="" <?php if ($newgroup['filebrowser'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators" <?php if ($newgroup['filebrowser'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">File Transfer:</td>
											<td>
												<select id="newgroupfiletransfer" name="newgroupfiletransfer" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Operators, Users, Auditors" <?php if ($newgroup['filetransfer'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="" <?php if ($newgroup['filetransfer'] == 1) { echo 'selected'; } ?>>Upload</option>
													<option value="2" title="" <?php if ($newgroup['filetransfer'] == 2) { echo 'selected'; } ?>>Download</option>
													<option value="3" title="Administrators" <?php if ($newgroup['filetransfer'] == 3) { echo 'selected'; } ?>>Both</option>
												</select>
											</td>
										</tr>
									</table>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
										<tr>
											<td width="50%" style="font-size:13px;">Change Settings:</td>
											<td width="50%">
												<select id="newgroupchangesettings" name="newgroupchangesettings" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Operators, Users, Auditors" <?php if ($newgroup['changesettings'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators" <?php if ($newgroup['changesettings'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">User Management:</td>
											<td>
												<select id="newgroupusermanagement" name="newgroupusermanagement" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Operators, Users, Auditors" <?php if ($newgroup['usermanagement'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Auditors" <?php if ($newgroup['usermanagement'] == 1) { echo 'selected'; } ?>>Read Only</option>
													<option value="2" title="Administrators" <?php if ($newgroup['usermanagement'] == 2) { echo 'selected'; } ?>>Full Control</option>
												</select>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Audit Log:</td>
											<td>
												<select id="newgroupauditlog" name="newgroupauditlog" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%;">
													<option value="0" title="Operators, Users" <?php if ($newgroup['auditlog'] == 0) { echo 'selected'; } ?>>Denied</option>
													<option value="1" title="Administrators, Auditors" <?php if ($newgroup['auditlog'] == 1) { echo 'selected'; } ?>>Allowed</option>
												</select>
											</td>
										</tr>
									</table>
								</div>
								<div class="input-control select">
									<h3><div class="icon-search" style="font-size:17px; color:#b5b5b5" title="Pre-Filter"></div>&nbsp;Pre-Filter:</h3>
									<?php if ($serverstatus == 'run' || $newgroup['auth'] == 'Distributed') { ?>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
										<tr title="<?php echo 'Valid Raw Data Example for Login Limitation:' . "\n\n" . '<computername>computer name</computername>' . "\n" . '<osname>operating system name</osname>' . "\n" . '<osversion>operating system version</osversion>' . "\n" . '<osservicepack>operating system service pack</osservicepack>' . "\n" . '<osserialnumber>operating system serial number</osserialnumber>' . "\n" . '<manufacturer>computer manufacturer</manufacturer>' . "\n" . '<model>computer model</model>' . "\n" . '<domain>computer domain</domain>'; ?>">
											<td width="50%" style="background-color:#eeeaf6; font-size:13px;">Nodes:</td>
											<td width="50%" style="background-color:#eeeaf6;">
												<input type="text" id="newgroupnodesstatusf" name="newgroupnodesstatusf" placeholder="Regular Expression..." value="<?php echo $newgroup['nodesstatusf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
									</table>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
										<tr>
											<td width="50%" style="background-color:#eeeaf6; font-size:13px;">Nodes Command:</td>
											<td width="50%" style="background-color:#eeeaf6;">
												<input type="text" id="newgroupnodescommandf" name="newgroupnodescommandf" placeholder="Comma Separated RegExp..." value="<?php echo $newgroup['nodescommandf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Nagios Status:</td>
											<td style="background-color:#eeeaf6;">
												<input type="text" id="newgroupnodesnagiosstatusf" name="newgroupnodesnagiosstatusf" placeholder="Regular Expression..." value="<?php echo $newgroup['nodesnagiosstatusf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Installed Programs:</td>
											<td style="background-color:#eeeaf6;">
												<input type="text" id="newgroupnodesinstalledprogramsf" name="newgroupnodesinstalledprogramsf" placeholder="Regular Expression..." value="<?php echo $newgroup['nodesinstalledprogramsf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Process Control:</td>
											<td style="background-color:#eeeaf6;">
												<input type="text" id="newgroupnodesprocesscontrolf" name="newgroupnodesprocesscontrolf" placeholder="Regular Expression..." value="<?php echo $newgroup['nodesprocesscontrolf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Service Control:</td>
											<td style="background-color:#eeeaf6;">
												<input type="text" id="newgroupnodesservicecontrolf" name="newgroupnodesservicecontrolf" placeholder="Regular Expression..." value="<?php echo $newgroup['nodesservicecontrolf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Network Stats:</td>
											<td style="background-color:#eeeaf6;">
												<input type="text" id="newgroupnodesnetworkstatsf" name="newgroupnodesnetworkstatsf" placeholder="Regular Expression..." value="<?php echo $newgroup['nodesnetworkstatsf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Scheduled Tasks:</td>
											<td style="background-color:#eeeaf6;">
												<input type="text" id="newgroupnodesscheduledtasksf" name="newgroupnodesscheduledtasksf" placeholder="Regular Expression..." value="<?php echo $newgroup['nodesscheduledtasksf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="background-color:#eeeaf6; font-size:13px;">Nodes Event Viewer:</td>
											<td style="background-color:#eeeaf6;">
												<input type="text" id="newgroupnodeseventviewerf" name="newgroupnodeseventviewerf" placeholder="Regular Expression..." value="<?php echo $newgroup['nodeseventviewerf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#f4f2f9; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
									</table>
									<?php } else { ?>
										<input type="hidden" id="newgroupnodesstatusf" name="newgroupnodesstatusf" value="<?php echo $newgroup['nodesstatusf'] ?>" />
										<input type="hidden" id="newgroupnodesnagiosstatusf" name="newgroupnodesnagiosstatusf" value="<?php echo $newgroup['nodesnagiosstatusf'] ?>" />
										<input type="hidden" id="newgroupnodesinstalledprogramsf" name="newgroupnodesinstalledprogramsf" value="<?php echo $newgroup['nodesinstalledprogramsf'] ?>" />
										<input type="hidden" id="newgroupnodesprocesscontrolf" name="newgroupnodesprocesscontrolf" value="<?php echo $newgroup['nodesprocesscontrolf'] ?>" />
										<input type="hidden" id="newgroupnodesservicecontrolf" name="newgroupnodesservicecontrolf" value="<?php echo $newgroup['nodesservicecontrolf'] ?>" />
										<input type="hidden" id="newgroupnodesnetworkstatsf" name="newgroupnodesnetworkstatsf" value="<?php echo $newgroup['nodesnetworkstatsf'] ?>" />
										<input type="hidden" id="newgroupnodesscheduledtasksf" name="newgroupnodesscheduledtasksf" value="<?php echo $newgroup['nodesscheduledtasksf'] ?>" />
										<input type="hidden" id="newgroupnodeseventviewerf" name="newgroupnodeseventviewerf" value="<?php echo $newgroup['nodeseventviewerf'] ?>" />
									<?php } ?>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
										<tr>
											<td width="50%" style="font-size:13px;">Nagios Status:</td>
											<td width="50%">
												<input type="text" id="newgroupnagiosstatusf" name="newgroupnagiosstatusf" placeholder="Regular Expression..." value="<?php echo $newgroup['nagiosstatusf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Installed Programs:</td>
											<td>
												<input type="text" id="newgroupinstalledprogramsf" name="newgroupinstalledprogramsf" placeholder="Regular Expression..." value="<?php echo $newgroup['installedprogramsf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
									</table>
									<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
										<tr>
											<td width="50%" style="font-size:13px;">Process Control:</td>
											<td width="50%">
												<input type="text" id="newgroupprocesscontrolf" name="newgroupprocesscontrolf" placeholder="Regular Expression..." value="<?php echo $newgroup['processcontrolf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Service Control:</td>
											<td>
												<input type="text" id="newgroupservicecontrolf" name="newgroupservicecontrolf" placeholder="Regular Expression..." value="<?php echo $newgroup['servicecontrolf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Network Stats:</td>
											<td>
												<input type="text" id="newgroupnetworkstatsf" name="newgroupnetworkstatsf" placeholder="Regular Expression..." value="<?php echo $newgroup['networkstatsf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Scheduled Tasks:</td>
											<td>
												<input type="text" id="newgroupscheduledtasksf" name="newgroupscheduledtasksf" placeholder="Regular Expression..." value="<?php echo $newgroup['scheduledtasksf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Event Viewer:</td>
											<td>
												<input type="text" id="newgroupeventviewerf" name="newgroupeventviewerf" placeholder="Regular Expression..." value="<?php echo $newgroup['eventviewerf']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Command Line:</td>
											<td>
												<input type="text" id="newgroupcommandlinef" name="newgroupcommandlinef" placeholder="Comma Separated RegExp..." value="<?php echo $newgroup['commandlinef']; ?>" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; padding-left:5px; padding-right:5px;" />
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Permitted Folders:</td>
											<td>
												<textarea id="newgroupfilebrowserf" name="newgroupfilebrowserf" placeholder="Absolute Path...
Absolute Path...
Absolute Path..." wrap="off" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; height:55px; padding-left:5px; padding-right:5px;"><?php echo $newgroup['filebrowserf']; ?></textarea>
											</td>
										</tr>
										<tr>
											<td style="font-size:13px;">Permitted Registry Keys:</td>
											<td>
												<textarea id="newgroupregeditf" name="newgroupregeditf" placeholder="hkey_current_user\system...
hkey_local_machine\software...
hkey_users\s-1-5-20\system..." wrap="off" style="font-family:'Segoe UI Light','Open Sans',Verdana,Arial,Helvetica,sans-serif; font-size:12px; border:solid; border-width:1px; border-color:#e5e5e5; background-color:#fafafa; width:100%; height:55px; padding-left:5px; padding-right:5px;"><?php echo $newgroup['regeditf']; ?></textarea>
											</td>
										</tr>
									</table>
								</div>
								<div class="input-control select">
			                       	<h3><div class="icon-blocked" style="font-size:17px; color:#b5b5b5" title="Bad Authentications Block Removal"></div>&nbsp;Bad Authentications Block Removal:</h3>
									<select id="newgroupbadaut" name="newgroupbadaut">
										<option value="0" <?php if ($newgroup['badaut'] == '0') { echo 'selected'; } ?>>Denied</option>
										<option value="1" <?php if ($newgroup['badaut'] == '1') { echo 'selected'; } ?>>Client Only</option>
										<option value="2" <?php if ($newgroup['badaut'] == '2') { echo 'selected'; } ?>>Full Control</option>
									</select>
								</div>
								<br />
								<div class="input-control select">
			                       	<h3><div class="icon-share-2" style="font-size:17px; color:#b5b5b5" title="Authentication"></div>&nbsp;Authentication:</h3>
									<select id="newgroupauth" name="newgroupauth">
										<option value="Local" <?php if ($newgroup['auth'] == 'Local') { echo 'selected'; } ?>>Local</option>
										<?php if (($agentstatus == 'con' || $serverstatus == 'cfg') && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername) && $newgroup['auth'] != 'Distributed') { ?>
										<option disabled="disabled">Distributed</option>
										<?php } else { ?>
										<option value="Distributed" <?php if ($newgroup['auth'] == 'Distributed') { echo 'selected'; } ?>>Distributed</option>
										<?php } ?>
									</select>
								</div>
								<br />
								<div class="input-control select">
			                       	<h3><div class="icon-history" style="font-size:17px; color:#b5b5b5" title="Access Time Limits"></div>&nbsp;Access Time Limits:</h3>
									<table style="border:0px;">
									  <tr style="border:0px;">
										<td style="border:0px;" valign="top"><div style="font-size:13px;">Start Time:</div></td>
										<td style="border:0px;" valign="top"><div style="font-size:13px;">Stop Time:</div></td>
									  </tr>
									  <tr style="border:0px;">
										<td style="border:0px;" valign="top">
										<div class="input-control text">
											<input type="text" id="newgroupsastarttime" name="newgroupsastarttime" autocomplete="off" value="<?php echo $newgroup['sastarttime']; ?>" />
										</div>
										</td>
										<td style="border:0px;" valign="top">
										<div class="input-control text">
											<input type="text" id="newgroupsastoptime" name="newgroupsastoptime" autocomplete="off" value="<?php echo $newgroup['sastoptime']; ?>" />
										</div>
										</td>
									  </tr>
									  <tr style="border:0px;">
										<td style="border:0px;" valign="top"><div style="font-size:13px;"><input type="checkbox" id="newgroupsasunday" name="newgroupsasunday"<?php if ($newgroup['sasunday'] == 'on') { echo ' checked="checked"'; } ?> /><span class="helper" style="font-size:13px;">&nbsp;Sunday</span><br /><input type="checkbox" id="newgroupsatuesday" name="newgroupsatuesday"<?php if ($newgroup['satuesday'] == 'on') { echo ' checked="checked"'; } ?> /><span class="helper" style="font-size:13px;">&nbsp;Tuesday</span><br /><input type="checkbox" id="newgroupsathursday" name="newgroupsathursday"<?php if ($newgroup['sathursday'] == 'on') { echo ' checked="checked"'; } ?> /><span class="helper" style="font-size:13px;">&nbsp;Thursday</span><br /><input type="checkbox" id="newgroupsasaturday" name="newgroupsasaturday"<?php if ($newgroup['sasaturday'] == 'on') { echo ' checked="checked"'; } ?> /><span class="helper" style="font-size:13px;">&nbsp;Saturday</span><br /></div></td>
										<td style="border:0px;" valign="top"><div style="font-size:13px;"><input type="checkbox" id="newgroupsamonday" name="newgroupsamonday"<?php if ($newgroup['samonday'] == 'on') { echo ' checked="checked"'; } ?> /><span class="helper" style="font-size:13px;">&nbsp;Monday</span><br /><input type="checkbox" id="newgroupsawednesday" name="newgroupsawednesday"<?php if ($newgroup['sawednesday'] == 'on') { echo ' checked="checked"'; } ?> /><span class="helper" style="font-size:13px;">&nbsp;Wednesday</span><br /><input type="checkbox" id="newgroupsafriday" name="newgroupsafriday"<?php if ($newgroup['safriday'] == 'on') { echo ' checked="checked"'; } ?> /><span class="helper" style="font-size:13px;">&nbsp;Friday</span><br /></div></td>
									  </tr>
									</table>
									<script type="text/javascript">
									$(function () {
										$('#newgroupsastarttime').timeEntry();
										$('#newgroupsastoptime').timeEntry();
									});
									</script>
								</div>
								<div class="input-control select">
			                       	<h3><div class="icon-calendar" style="font-size:17px; color:#b5b5b5" title="Password Expiration Days"></div>&nbsp;Password Expiration Days:</h3>
									<select id="newgrouppwdexp" name="newgrouppwdexp">
										<option value="1" <?php if ($newgroup['pwdexp'] == '1') { echo 'selected'; } ?>>1</option>
										<option value="2" <?php if ($newgroup['pwdexp'] == '2') { echo 'selected'; } ?>>2</option>
										<option value="3" <?php if ($newgroup['pwdexp'] == '3') { echo 'selected'; } ?>>3</option>
										<option value="4" <?php if ($newgroup['pwdexp'] == '4') { echo 'selected'; } ?>>4</option>
										<option value="5" <?php if ($newgroup['pwdexp'] == '5') { echo 'selected'; } ?>>5</option>
										<option value="6" <?php if ($newgroup['pwdexp'] == '6') { echo 'selected'; } ?>>6</option>
										<option value="7" <?php if ($newgroup['pwdexp'] == '7') { echo 'selected'; } ?>>7</option>
										<option value="8" <?php if ($newgroup['pwdexp'] == '8') { echo 'selected'; } ?>>8</option>
										<option value="9" <?php if ($newgroup['pwdexp'] == '9') { echo 'selected'; } ?>>9</option>
										<option value="10" <?php if ($newgroup['pwdexp'] == '10') { echo 'selected'; } ?>>10</option>
										<option value="11" <?php if ($newgroup['pwdexp'] == '11') { echo 'selected'; } ?>>11</option>
										<option value="12" <?php if ($newgroup['pwdexp'] == '12') { echo 'selected'; } ?>>12</option>
										<option value="13" <?php if ($newgroup['pwdexp'] == '13') { echo 'selected'; } ?>>13</option>
										<option value="14" <?php if ($newgroup['pwdexp'] == '14') { echo 'selected'; } ?>>14</option>
										<option value="15" <?php if ($newgroup['pwdexp'] == '15') { echo 'selected'; } ?>>15</option>
										<option value="16" <?php if ($newgroup['pwdexp'] == '16') { echo 'selected'; } ?>>16</option>
										<option value="17" <?php if ($newgroup['pwdexp'] == '17') { echo 'selected'; } ?>>17</option>
										<option value="18" <?php if ($newgroup['pwdexp'] == '18') { echo 'selected'; } ?>>18</option>
										<option value="19" <?php if ($newgroup['pwdexp'] == '19') { echo 'selected'; } ?>>19</option>
										<option value="20" <?php if ($newgroup['pwdexp'] == '20') { echo 'selected'; } ?>>20</option>
										<option value="21" <?php if ($newgroup['pwdexp'] == '21') { echo 'selected'; } ?>>21</option>
										<option value="22" <?php if ($newgroup['pwdexp'] == '22') { echo 'selected'; } ?>>22</option>
										<option value="23" <?php if ($newgroup['pwdexp'] == '23') { echo 'selected'; } ?>>23</option>
										<option value="24" <?php if ($newgroup['pwdexp'] == '24') { echo 'selected'; } ?>>24</option>
										<option value="25" <?php if ($newgroup['pwdexp'] == '25') { echo 'selected'; } ?>>25</option>
										<option value="26" <?php if ($newgroup['pwdexp'] == '26') { echo 'selected'; } ?>>26</option>
										<option value="27" <?php if ($newgroup['pwdexp'] == '27') { echo 'selected'; } ?>>27</option>
										<option value="28" <?php if ($newgroup['pwdexp'] == '28') { echo 'selected'; } ?>>28</option>
										<option value="29" <?php if ($newgroup['pwdexp'] == '29') { echo 'selected'; } ?>>29</option>
										<option value="30" <?php if ($newgroup['pwdexp'] == '30') { echo 'selected'; } ?>>30</option>
									</select>
								</div>
								<div class="input-control select">
			                       	<h3><div class="icon-switch" style="font-size:17px; color:#b5b5b5" title="Disable From Expiration Days"></div>&nbsp;Disable From Expiration Days:</h3>
									<select id="newgroupdisable" name="newgroupdisable">
										<option value="0" <?php if ($newgroup['disable'] == '0') { echo 'selected'; } ?>>0</option>
										<option value="1" <?php if ($newgroup['disable'] == '1') { echo 'selected'; } ?>>1</option>
										<option value="2" <?php if ($newgroup['disable'] == '2') { echo 'selected'; } ?>>2</option>
										<option value="3" <?php if ($newgroup['disable'] == '3') { echo 'selected'; } ?>>3</option>
										<option value="5" <?php if ($newgroup['disable'] == '5') { echo 'selected'; } ?>>5</option>
										<option value="7" <?php if ($newgroup['disable'] == '7') { echo 'selected'; } ?>>7</option>
										<option value="10" <?php if ($newgroup['disable'] == '10') { echo 'selected'; } ?>>10</option>
										<option value="15" <?php if ($newgroup['disable'] == '15') { echo 'selected'; } ?>>15</option>
										<option value="20" <?php if ($newgroup['disable'] == '20') { echo 'selected'; } ?>>20</option>
										<option value="30" <?php if ($newgroup['disable'] == '30') { echo 'selected'; } ?>>30</option>
										<option value="40" <?php if ($newgroup['disable'] == '40') { echo 'selected'; } ?>>40</option>
										<option value="60" <?php if ($newgroup['disable'] == '60') { echo 'selected'; } ?>>60</option>
										<option value="90" <?php if ($newgroup['disable'] == '90') { echo 'selected'; } ?>>90</option>
									</select>
								</div>
								<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) { ?>
								<br />
								<br />
								<?php } ?>
			                    <input type="hidden" id="newgroup" name="newgroup" />
								<?php if ($_SESSION['changegroup'] == '') { ?>
								<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) { ?>
								<input type="submit" id="addgroup" name="addgroup" style="background-color:#0072C6;" value="Add"/>
								<?php } ?>
								<?php } else { ?>
								<?php if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername) && $newgroup['auth'] == 'Distributed') { ?><?php } else { ?><input type="checkbox" id="deletegroup" name="deletegroup" /><span class="helper" style="font-size:13px;">&nbsp;Delete This Group</span><br /><br /><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 1) { ?><input type="submit" id="editgroup" name="editgroup" style="background-color:#0072C6;" value="Edit"/> <?php } ?><?php } ?><input type="submit" id="editgroupcancel" name="editgroupcancel" style="background-color:#888888;" value="Cancel" <?php if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername) && $newgroup['auth'] == 'Distributed') { ?>onclick="document.forms['editgroupcancel'].submit();" <?php } ?>/>
								<?php } ?>
							</form>
							<?php if ($agentstatus == 'con' && strtolower($eurysco_serverconaddress) != strtolower('https://' . $envcomputername) && $newgroup['auth'] == 'Distributed') { ?>
							<form id="editgroupcancel" name="editgroupcancel" method="post" action="users.php">
								<input type="hidden" id="editgroupcancel" name="editgroupcancel" value="Cancel" />
							</form>
							<?php } ?>
							</div>
						</li>
					</ul>
					<br />
					<br />
					<br />
					<?php

					$usersadminarray = array();
					$usersauditarray = array();
					$usersoperaarray = array();
					$usersusersarray = array();
					$userscurruarray = array();
					foreach ($grouplist as $group) {
						if (pathinfo($group)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group) > 0) {
							$group = str_replace('.xml', '', $group);
							$groupnamearray = 'users' . strtolower($group) . 'array';
							$$groupnamearray = array();
							$groupnamesetti_disable = 'users' . strtolower($group) . 'setti_disable';
							$groupnamesetti = array();
							$mcrykey = pack('H*', hash('sha256', hash('sha512', $group)));
							$groupsxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group . '.xml', true)))));
							$groupnamesetti = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($groupsxml->settings->groupsettings), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($groupsxml->settings->groupsettings), 0, $iv_size)));
							$$groupnamesetti_disable = $groupnamesetti['disable'];
						}
					}
					
					$userlist = scandir($_SERVER['DOCUMENT_ROOT'] . '\\users\\');
					foreach ($userlist as $user) {
						if (pathinfo($user)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $user) > 0) {
							$userxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $user, true)))));

							$usertypeintegrity = 0;
							$usertype = 'Currupted';
							$userauthintegrity = 0;
							$userauth = 'Currupted';
							$userauthicon = '<div style="font-size:12px; color:#888;" class="icon-user" style="margin-top:0px;" title="Local"></div>&nbsp;&nbsp;';
							$userstatus = '';
							$userrow = '';
							
							if ($userxml->settings->passwlck == md5($userxml->settings->password . 3)) {
								$userstatus = '&nbsp;&nbsp;<div class="icon-warning" style="font-size:12px; color:#993300;" title="User Status: Locked Out' . "\n\n" . 'Locked Out Time: ' . $userxml->settings->lckouttm . "\n" . 'Locked Out Node: ' . $userxml->settings->lckoutcm . "\n" . 'Locked Out From: ' . $userxml->settings->lckoutip . '"></div>';
							}
							
							if ($userxml->settings->passwlck == md5($userxml->settings->password . 2)) {
								$userstatus = '&nbsp;&nbsp;<div class="icon-warning" style="font-size:12px; color:#c97d15;" title="User Status: 1 Login Attempt Remaining' . "\n\n" . 'Login Failed Time: ' . $userxml->settings->lckouttm . "\n" . 'Login Failed Node: ' . $userxml->settings->lckoutcm . "\n" . 'Login Failed From: ' . $userxml->settings->lckoutip . '"></div>';
							}
							
							if ($userxml->settings->passwlck == md5($userxml->settings->password . 1)) {
								$userstatus = '&nbsp;&nbsp;<div class="icon-warning" style="font-size:12px; color:#cdab16;" title="User Status: 2 Login Attempts Remaining' . "\n\n" . 'Login Failed Time: ' . $userxml->settings->lckouttm . "\n" . 'Login Failed Node: ' . $userxml->settings->lckoutcm . "\n" . 'Login Failed From: ' . $userxml->settings->lckoutip . '"></div>';
							}
							
							if (hash('sha512', $userxml->settings->username . 'Local') == $userxml->settings->userauth) {
								$userauth = 'Local';
								$userauthintegrity = 1;
							}
							
							if (hash('sha512', $userxml->settings->username . 'Distributed') == $userxml->settings->userauth) {
								$userauth = 'Distributed';
								$userauthintegrity = 1;
								$userauthicon = '<div style="font-size:12px; color:#603CBA;" class="icon-user" style="margin-top:0px;" title="Distributed"></div>&nbsp;&nbsp;';
							}
							
							if (hash('sha512', $userxml->settings->username . 'Administrators') == $userxml->settings->usertype) {
								$usertype = 'Administrators';
								$usertypeintegrity = 1;
								if ($userxml->settings->username != 'Administrator') { $AdminLockIcon = ''; } else { $AdminLockIcon = '&nbsp;&nbsp;<div class="icon-locked-2" style="color:#cdab16;" title="Protected"></div>'; }
								if ($userxml->settings->username == $_SESSION['username'] || (hash('sha512', $userxml->settings->username . 'Distributed') == $userxml->settings->userauth && $serverstatus == 'cfg' && $agentstatus != 'cfg')) { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $AdminLockIcon . $userstatus . '</blockquote><br />'; } else { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $AdminLockIcon . $userstatus . '<a class="place-right" href="javascript:changeuser(\'' . $user . '\',\'' . $userxml->settings->username . '\',\'Administrators\',\'' . $userauth . '\');" title="Change: ' . $userxml->settings->username . '"><div class="icon-key"></div></a></blockquote><br />'; }
							}

							if (hash('sha512', $userxml->settings->username . 'Auditors') == $userxml->settings->usertype) {
								$usertype = 'Auditors';
								$usertypeintegrity = 1;
								if ($userxml->settings->username == $_SESSION['username'] || (hash('sha512', $userxml->settings->username . 'Distributed') == $userxml->settings->userauth && $serverstatus == 'cfg' && $agentstatus != 'cfg')) { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $userstatus . '</blockquote><br />'; } else { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $userstatus . '<a class="place-right" href="javascript:changeuser(\'' . $user . '\',\'' . $userxml->settings->username . '\',\'Auditors\',\'' . $userauth . '\');" title="Change: ' . $userxml->settings->username . '"><div class="icon-key"></div></a></blockquote><br />'; }
							}

							if (hash('sha512', $userxml->settings->username . 'Operators') == $userxml->settings->usertype) {
								$usertype = 'Operators';
								$usertypeintegrity = 1;
								if ($userxml->settings->username == $_SESSION['username'] || (hash('sha512', $userxml->settings->username . 'Distributed') == $userxml->settings->userauth && $serverstatus == 'cfg' && $agentstatus != 'cfg')) { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $userstatus . '</blockquote><br />'; } else { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $userstatus . '<a class="place-right" href="javascript:changeuser(\'' . $user . '\',\'' . $userxml->settings->username . '\',\'Operators\',\'' . $userauth . '\');" title="Change: ' . $userxml->settings->username . '"><div class="icon-key"></div></a></blockquote><br />'; }
							}

							if (hash('sha512', $userxml->settings->username . 'Users') == $userxml->settings->usertype) {
								$usertype = 'Users';
								$usertypeintegrity = 1;
								if ($userxml->settings->username == $_SESSION['username'] || (hash('sha512', $userxml->settings->username . 'Distributed') == $userxml->settings->userauth && $serverstatus == 'cfg' && $agentstatus != 'cfg')) { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $userstatus . '</blockquote><br />'; } else { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $userstatus . '<a class="place-right" href="javascript:changeuser(\'' . $user . '\',\'' . $userxml->settings->username . '\',\'Users\',\'' . $userauth . '\');" title="Change: ' . $userxml->settings->username . '"><div class="icon-key"></div></a></blockquote><br />'; }
							}

							foreach ($grouplist as $group) {
								if (pathinfo($group)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group) > 0) {
									$group = str_replace('.xml', '', $group);
									if (hash('sha512', $userxml->settings->username . $group) == $userxml->settings->usertype) {
										$groupnamesetti_disable = 'users' . strtolower($group) . 'setti_disable';
										if ((strtotime(date('Y-m-d H:i:s', strtotime(base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($userxml->settings->expiration))))) . ' + ' . $$groupnamesetti_disable . ' days'))) - strtotime(date('Y-m-d H:i:s'))) < 0 && $userxml->settings->expiration != base64_encode(base64_encode(base64_encode(base64_encode(base64_encode('2000-01-01 00:00:00'))))) && $userstatus == '') {
											$userstatus = '&nbsp;&nbsp;<div class="icon-switch" style="font-size:12px; color:#993300;" title="User Status: Account Disabled"></div>';
										}
										$usertype = $group;
										$usertypeintegrity = 1;
										if ($userxml->settings->username == $_SESSION['username'] || (hash('sha512', $userxml->settings->username . 'Distributed') == $userxml->settings->userauth && $serverstatus == 'cfg' && $agentstatus != 'cfg')) { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $userstatus . '</blockquote><br />'; } else { $userrow = '<div name="' . $userxml->settings->username . '"></div>' . $userauthicon . $userxml->settings->username . $userstatus . '<a class="place-right" href="javascript:changeuser(\'' . $user . '\',\'' . $userxml->settings->username . '\',\'' . $group . '\',\'' . $userauth . '\');" title="Change: ' . $userxml->settings->username . '"><div class="icon-key"></div></a></blockquote><br />'; }
									}
								}
							}
					
							if ($usertype == 'Currupted') {
								$userrow = $userauthicon . $userxml->settings->username . $userstatus . '<a class="place-right" href="javascript:changeuser(\'' . $user . '\',\'' . $userxml->settings->username . '\',\'Currupted\',\'' . $userauth . '\');" title="Change: ' . $userxml->settings->username . '"><div class="icon-key"></div></a></blockquote><br />';
							}

							if (pathinfo($user)['filename'] != $userxml->settings->username) { $usertypeintegrity = 0; }

							if ($usertypeintegrity == 0 || $userauthintegrity == 0) { $userintegritymsg = '<div class="icon-link-2" style="color:#933000;" title="User File Corrupted"></div>&nbsp;&nbsp;'; } else { $userintegritymsg = ''; }

							if ($usertype == 'Administrators') { array_push($usersadminarray, '<blockquote>' . $userintegritymsg . $userrow); }
							if ($usertype == 'Auditors') { array_push($usersauditarray, '<blockquote>' . $userintegritymsg . $userrow); }
							if ($usertype == 'Operators') { array_push($usersoperaarray, '<blockquote>' . $userintegritymsg . $userrow); }
							if ($usertype == 'Users') { array_push($usersusersarray, '<blockquote>' . $userintegritymsg . $userrow); }
							foreach ($grouplist as $group) {
								if (pathinfo($group)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group) > 0) {
									$group = str_replace('.xml', '', $group);
									$groupnamearray = 'users' . strtolower($group) . 'array';
									if ($usertype == $group) { array_push($$groupnamearray, '<blockquote>' . $userintegritymsg . $userrow); }
								}
							}
							if ($usertype == 'Currupted') { array_push($userscurruarray, '<blockquote>' . $userintegritymsg . $userrow); }

						}
						if (filesize($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $user) == 0) { @unlink($_SERVER['DOCUMENT_ROOT'] . '\\users\\' . $user); }
					}
					
					sort($usersadminarray);
					sort($usersauditarray);
					sort($usersoperaarray);
					sort($usersusersarray);
					sort($userscurruarray);
					foreach ($grouplist as $group) {
						if (pathinfo($group)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group) > 0) {
							$group = str_replace('.xml', '', $group);
							$groupnamearray = 'users' . strtolower($group) . 'array';
							sort($$groupnamearray);
						}
					}
					
					?>
                    
					<?php if (count($usersadminarray) > 0) { ?>
             	       <h3><img src="/img/adml.png" width="26" height="26" />&nbsp;Administrators:</h3>
						<?php
						foreach($usersadminarray as $usersadmin) {
							echo $usersadmin;
						}						
						?>
						<br />
                    <?php } ?>
                    
					<?php if (count($usersauditarray) > 0) { ?>
						<h3><img src="/img/adml.png" width="26" height="26" />&nbsp;Auditors:</h3>
						<?php
						foreach($usersauditarray as $usersaudit) {
							echo $usersaudit;
						}						
						?>
						<br />
                    <?php } ?>
                    
					<?php if (count($usersoperaarray) > 0) { ?>
						<h3><img src="/img/adml.png" width="26" height="26" />&nbsp;Operators:</h3>
						<?php
						foreach($usersoperaarray as $usersopera) {
							echo $usersopera;
						}						
						?>
						<br />
                    <?php } ?>
                    
					<?php if (count($usersusersarray) > 0) { ?>
						<h3><img src="/img/adml.png" width="26" height="26" />&nbsp;Users:</h3>
						<?php
						foreach($usersusersarray as $usersusers) {
							echo $usersusers;
						}						
						?>
						<br />
                    <?php } ?>
                    
					<?php foreach ($grouplist as $group) { if (pathinfo($group)['extension'] == 'xml' && filesize($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group) > 0) { $group = str_replace('.xml', '', $group); $groupnamearray = 'users' . strtolower($group) . 'array'; $groupnameusers = 'users' . strtolower($group); $mcrykey = pack('H*', hash('sha256', hash('sha512', $group))); $groupsxml = simplexml_load_string(base64_decode(base64_decode(base64_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '\\groups\\' . $group . '.xml', true))))); $groupname = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mcrykey, substr(base64_decode($groupsxml->settings->groupsettings), $iv_size), MCRYPT_MODE_CBC, substr(base64_decode($groupsxml->settings->groupsettings), 0, $iv_size))); ?>
						<h3><a href="?changegroup=<?php echo $group; ?>" title="Change: <?php echo $groupname['auth']; ?> Group"><img src="/img/<?php if ($groupname['auth'] == 'Local') { echo 'admle'; } else { echo 'admled'; } ?>.png" width="26" height="26" /></a>&nbsp;<?php echo $group; ?>:<?php if ($groupname['sastarttime'] != $groupname['sastoptime'] || $groupname['sasunday'] != 'on' || $groupname['samonday'] != 'on' || $groupname['satuesday'] != 'on' || $groupname['sawednesday'] != 'on' || $groupname['sathursday'] != 'on' || $groupname['safriday'] != 'on' || $groupname['sasaturday'] != 'on') { if ($groupname['sa' . strtolower(date('l'))] == 'on' && ($groupname['sastarttime'] == $groupname['sastoptime'] || (date('His', strtotime($groupname['sastarttime'])) < date('His', strtotime($groupname['sastoptime'])) && date('His', strtotime($groupname['sastarttime'])) <= date('His') && date('His') < date('His', strtotime($groupname['sastoptime']))) || (date('His', strtotime($groupname['sastarttime'])) > date('His', strtotime($groupname['sastoptime'])) && (date('His', strtotime($groupname['sastarttime'])) <= date('His') || date('His') < date('His', strtotime($groupname['sastoptime'])))))) { echo '&nbsp;&nbsp;<div class="icon-history" style="font-size:17px; color:#b5b5b5" title="Access Time Limits: Login Allowed"></div>'; } else { echo '&nbsp;&nbsp;<div class="icon-history" style="font-size:17px; color:#993300;" title="Access Time Limits: Login Denied"></div>'; } } ?></h3>
						<?php
						foreach($$groupnamearray as $$groupnameusers) {
							echo $$groupnameusers;
						}						
						?>
						<br />
                    <?php } } ?>
                    
					<?php if (count($userscurruarray) > 0) { ?>
						<h3 style="color:#933000;">Currupted:</h3>
						<?php
						foreach($userscurruarray as $userscurru) {
							echo $userscurru;
						}						
						?>
						<br />
                    <?php } ?>
                    
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>