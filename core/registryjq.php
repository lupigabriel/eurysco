<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/registry.php')) { exit; }

if (isset($_GET['phptimeout'])) {
	if (is_numeric($_GET['phptimeout'])) {
		set_time_limit($_GET['phptimeout']);
	} else {
		set_time_limit(120);
	}
} else {
	set_time_limit(120);
}

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$start = $time;

include(str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT']) . '\\include\\init_core.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemregistry'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

require($euryscoinstallpath . '\\include\\class.WindowsRegistry.php');

if (isset($_GET['keypath'])) {
	$keypath = $_GET['keypath'];
} else {
	$keypath = '';
}

if (isset($_GET['hkey'])) {
	$hkey = $_GET['hkey'];
} else {
	$hkey = '';
}

if ($hkey == 'HKEY_CLASSES_ROOT') { $keypathreg = str_replace('\\', '\\\\', str_replace('\\\\HKEY_CLASSES_ROOT', 'HKCR', '\\\\' . $keypath)); }
if ($hkey == 'HKEY_CURRENT_USER') { $keypathreg = str_replace('\\', '\\\\', str_replace('\\\\HKEY_CURRENT_USER', 'HKCU', '\\\\' . $keypath)); }
if ($hkey == 'HKEY_LOCAL_MACHINE') { $keypathreg = str_replace('\\', '\\\\', str_replace('\\\\HKEY_LOCAL_MACHINE', 'HKLM', '\\\\' . $keypath)); }
if ($hkey == 'HKEY_USERS') { $keypathreg = str_replace('\\', '\\\\', str_replace('\\\\HKEY_USERS', 'HKU', '\\\\' . $keypath)); }
if ($hkey == 'HKEY_CURRENT_CONFIG') { $keypathreg = str_replace('\\', '\\\\', str_replace('\\\\HKEY_CURRENT_CONFIG', 'HKCC', '\\\\' . $keypath)); }

if (isset($_GET['orderby'])) {
	if ($_GET['orderby'] != '') {
		$orderby = $_GET['orderby'];
	} else {
		$orderby = 'Name';
	}
}

if (isset($_GET['page'])) {
	if (is_numeric($_GET['page'])) {
		$pgkey = $_GET['page'];
	} else {
		$pgkey = 0;
	}
} else {
	$pgkey = 0;
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

$registrytable = '';

if ($orderby == 'Name') { $obyName = ' color:#8063C8;'; } else { $obyName = ''; }
if ($orderby == 'Type') { $obyType = ' color:#8063C8;'; } else { $obyType = ''; }
if ($orderby == 'Value') { $obyValue = ' color:#8063C8;'; } else { $obyValue = ''; }

if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemregistry'] > 1) { $rgman = '<a href=\'javascript:regadd("' . str_replace('\'', '%27', $keypathreg) . '")\'><img src="/images/expand24-black.png" width="16" height="16" border="0" title="Add"></a>'; } else { $rgman = ''; }
$registrytable = $registrytable . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="1%">' . $rgman . '</td><td width="40%"><a href="?orderby=Name&filter=' . urlencode($filter) . '&lastpath=' . urlencode($keypath) . '&hkey=' . urlencode($hkey) . '&keypath=" style="font-size:12px; font-weight:bold;' . $obyName . '" title="Ascending Order by Name">Name</a></td><td width="16%" align="center"><a href="?orderby=Type&filter=' . urlencode($filter) . '&lastpath=' . urlencode($keypath) . '&hkey=' . urlencode($hkey) . '&keypath=" style="font-size:12px; font-weight:bold;' . $obyType . '" title="Ascending Order by Type">Type</a></td><td><a href="?orderby=Value&filter=' . urlencode($filter) . '&lastpath=' . urlencode($keypath) . '&hkey=' . urlencode($hkey) . '&keypath=" style="font-size:12px; font-weight:bold;' . $obyValue . '" title="Ascending Order by Value">Value</a></td></tr>';

$registryarray = array();
$registrycounter = 0;

$winReg = new WindowsRegistry();

$keyNames = $winReg->GetSubKeys($keypath);
if (is_array($keyNames)) {
	foreach ($keyNames as $keyName) {
		$keyType = '';
		$keyValue = '';
		$checkfilter = 1;
		if (substr($filter, 0, 1) != '-') {
			if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', strtolower('<KeyName>' . $keyName . '</KeyName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>')) || strpos(strtolower('<KeyName>' . $keyName . '</KeyName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>'), strtolower($filter)) > -1) {
				$checkfilter = 0;
			} else {
				$checkfilter = 1;
			}
		} else {
			$notfilter = substr($filter, 1);
			if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', strtolower('<KeyName>' . $keyName . '</KeyName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>')) && !strpos(strtolower('<KeyName>' . $keyName . '</KeyName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>'), strtolower($notfilter))) {
				$checkfilter = 0;
			} else {
				$checkfilter = 1;
			}
		}
		if ($checkfilter == 0) {
			if ($orderby == 'Name') { $registryarray[$registrycounter][0] = strtolower('0' . $keyName); }
			if ($orderby == 'Type') { $registryarray[$registrycounter][0] = strtolower('1' . $keyType); }
			if ($orderby == 'Value') { $registryarray[$registrycounter][0] = strtolower('1' . $keyValue); }
			$registryarray[$registrycounter][1] = htmlentities($keyName, ENT_QUOTES, 'UTF-8');
			$registryarray[$registrycounter][2] = $keyType;
			if (strlen($keyName) > 15) { $titleName = substr(htmlentities($keyName, ENT_QUOTES, 'UTF-8'), 0, 15) . '&nbsp;[...]'; } else { $titleName = htmlentities($keyName, ENT_QUOTES, 'UTF-8'); }
			$keyusericon = '';
			if ($keypath == 'HKEY_USERS') { $keyValue = preg_replace('/.*\\\/', '', strtolower($winReg->ReadValue('HKEY_LOCAL_MACHINE\\SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion\\ProfileList\\' . $keyName, 'ProfileImagePath'))); if (strlen($keyValue) > 0) { $keyusericon = '<div style="font-size:12px; color:#555;" class="icon-user" style="margin-top:0px;"></div>&nbsp;&nbsp;'; } }
			if (strlen($keyValue) > 34) { $registryarray[$registrycounter][3] = $keyusericon . substr($keyValue, 0, 34) . '&nbsp;[...]'; } else { $registryarray[$registrycounter][3] = $keyusericon . $keyValue; }
			if (!preg_match('/^[a-zA-Z0-9\.;:,#\{\}\[\]\*+-@_\?\^\/()~$%&=\s]*$/', $keyName)) {
				$objnameawd = '<a>';
			} else {
				$objnameawd = '<a href="?orderby=' . $orderby . '&lastpath=' . urlencode($keypath) . '&keypath=' . urlencode($keyName) . '&hkey=' . urlencode($hkey) . '&lastfilter=' . urlencode($filter) . '" title="Key&nbsp;&raquo;&nbsp;' . $keyName . '">';
			}
			if (strlen($keyName) > 25) {
				$registryarray[$registrycounter][4] = $objnameawd . '<div class="icon-folder" style="margin-top:1px;"></div></a>&nbsp;' . str_replace(' ', '&nbsp;', substr($keyName, 0, 25)) . '&nbsp;[...]';
			} else {
				$registryarray[$registrycounter][4] = $objnameawd . '<div class="icon-folder" style="margin-top:1px;"></div></a>&nbsp;' . str_replace(' ', '&nbsp;', $keyName);
			}
			$registryarray[$registrycounter][5] = $keyValue;
			if (!preg_match('/^[a-zA-Z0-9\.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\s]*$/', $keyName) || !preg_match('/^[a-zA-Z0-9\.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\s]*$/', $keyValue)) {
				$registryarray[$registrycounter][6] = '';
			} else {
				$registryarray[$registrycounter][6] = '<a href=\'javascript:reginfo("' . htmlentities(str_replace('\'', '%27', $keyName), ENT_QUOTES, 'UTF-8') . '","' . $keyType . '","' . preg_replace('/\r\n|\r|\n/','\n', str_replace('\\', '\\\\', htmlentities(str_replace('\'', '%27', $keyValue), ENT_QUOTES, 'UTF-8'))) . '","' .  htmlentities(str_replace('\'', '%27', $titleName), ENT_QUOTES, 'UTF-8') . '","' . str_replace('\'', '%27', $keypathreg) . '","1","1");\'><img src="/images/enter24-black.png" width="16" height="16" border="0" title="Edit:&nbsp;' . htmlentities($keyName, ENT_QUOTES, 'UTF-8') . '"></a>';
			}
			$registryarray[$registrycounter][7] = 'Raw Data View:' . "\n\n" . '<KeyName>' . $keyName . '</KeyName>' . "\n" . '<Type>' . $keyType . '</Type>' . "\n" . '<Value>' . $keyValue . '</Value>';
			$registrycounter = $registrycounter + 1;
		}
	}
}

$valueName = '(Default)';
$keyType = $winReg->ReadType($keypath, '');
$keyValue = $winReg->ReadValue($keypath, '');
$checkfilter = 1;
if (substr($filter, 0, 1) != '-') {
	if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', strtolower('<ValueName>' . $valueName . '</ValueName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>')) || strpos(strtolower('<ValueName>' . $valueName . '</ValueName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>'), strtolower($filter)) > -1) {
		$checkfilter = 0;
	} else {
		$checkfilter = 1;
	}
} else {
	$notfilter = substr($filter, 1);
	if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', strtolower('<ValueName>' . $valueName . '</ValueName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>')) && !strpos(strtolower('<ValueName>' . $valueName . '</ValueName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>'), strtolower($notfilter))) {
		$checkfilter = 0;
	} else {
		$checkfilter = 1;
	}
}
if ($checkfilter == 0) {
	if ($orderby == 'Name') { $registryarray[$registrycounter][0] = strtolower('1' . $valueName); }
	if ($orderby == 'Type') { $registryarray[$registrycounter][0] = strtolower('0' . $keyType); }
	if ($orderby == 'Value') { $registryarray[$registrycounter][0] = strtolower('0' . $keyValue); }
	$registryarray[$registrycounter][1] = htmlentities($valueName, ENT_QUOTES, 'UTF-8');
	$registryarray[$registrycounter][2] = $keyType;
	if (strlen($valueName) > 15) { $titleName = substr(htmlentities($valueName, ENT_QUOTES, 'UTF-8'), 0, 15) . '&nbsp;[...]'; } else { $titleName = htmlentities($valueName, ENT_QUOTES, 'UTF-8'); }
	if (strlen($keyValue) > 34) { $registryarray[$registrycounter][3] = substr(htmlentities($keyValue, ENT_QUOTES, 'UTF-8'), 0, 34) . '&nbsp;[...]'; } else { $registryarray[$registrycounter][3] = htmlentities($keyValue, ENT_QUOTES, 'UTF-8'); }
	if (strlen($valueName) > 25) {
		$registryarray[$registrycounter][4] = '<a><div class="icon-libreoffice" style="margin-top:1px; margin-bottom:1px;"></div></a>&nbsp;' . substr(htmlentities($valueName, ENT_QUOTES, 'UTF-8'), 0, 25) . '&nbsp;[...]';
	} else {
		$registryarray[$registrycounter][4] = '<a><div class="icon-libreoffice" style="margin-top:1px; margin-bottom:1px;"></div></a>&nbsp;' . htmlentities($valueName, ENT_QUOTES, 'UTF-8');
	}
	$registryarray[$registrycounter][5] = htmlentities($keyValue, ENT_QUOTES, 'UTF-8');
	if (!preg_match('/^[a-zA-Z0-9\.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\s]*$/', $keyValue)) {
		$registryarray[$registrycounter][6] = '';
	} else {
		$registryarray[$registrycounter][6] = '<a href=\'javascript:reginfo("' . htmlentities(str_replace('\'', '%27', $valueName), ENT_QUOTES, 'UTF-8') . '","' . $keyType . '","' . preg_replace('/\r\n|\r|\n/','\n', str_replace('\\', '\\\\', htmlentities(str_replace('\'', '%27', $keyValue), ENT_QUOTES, 'UTF-8'))) . '","' .  htmlentities(str_replace('\'', '%27', $titleName), ENT_QUOTES, 'UTF-8') . '","' . str_replace('\'', '%27', $keypathreg) . '","1","0");\'><img src="/images/enter24-black.png" width="16" height="16" border="0" title="Edit:&nbsp;' .  htmlentities($valueName, ENT_QUOTES, 'UTF-8') . '"></a>';
	}
	$registryarray[$registrycounter][7] = 'Raw Data View:' . "\n\n" . '<ValueName>' . $valueName . '</ValueName>' . "\n" . '<Type>' . $keyType . '</Type>' . "\n" . '<Value>' . $keyValue . '</Value>';
	$registrycounter = $registrycounter + 1;
}

$valueNames = $winReg->GetValueNames($keypath);
if (is_array($valueNames)) {
	foreach ($valueNames as $valueName) {
		if ($valueName != '') {
			$keyType = $winReg->ReadType($keypath, $valueName);
			$keyValue = $winReg->ReadValue($keypath, $valueName);
			$checkfilter = 1;
			if (substr($filter, 0, 1) != '-') {
				if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', strtolower('<ValueName>' . $valueName . '</ValueName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>')) || strpos(strtolower('<ValueName>' . $valueName . '</ValueName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>'), strtolower($filter)) > -1) {
					$checkfilter = 0;
				} else {
					$checkfilter = 1;
				}
			} else {
				$notfilter = substr($filter, 1);
				if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', strtolower('<ValueName>' . $valueName . '</ValueName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>')) && !strpos(strtolower('<ValueName>' . $valueName . '</ValueName><Type>' . $keyType . '</Type><Value>' . $keyValue . '</Value>'), strtolower($notfilter))) {
					$checkfilter = 0;
				} else {
					$checkfilter = 1;
				}
			}
			if ($checkfilter == 0) {
				if ($orderby == 'Name') { $registryarray[$registrycounter][0] = strtolower('1' . $valueName); }
				if ($orderby == 'Type') { $registryarray[$registrycounter][0] = strtolower('0' . $keyType); }
				if ($orderby == 'Value') { $registryarray[$registrycounter][0] = strtolower('0' . $keyValue); }
				$iconvalue = '';
				if ($keyType == 'REG_NONE') { $iconvalue = 'icon-file'; }
				if ($keyType == 'REG_SZ') { $iconvalue = 'icon-file'; }
				if ($keyType == 'REG_EXPAND_SZ') { $iconvalue = 'icon-file'; }
				if ($keyType == 'REG_BINARY') { $iconvalue = 'icon-libreoffice'; }
				if ($keyType == 'REG_DWORD') { $iconvalue = 'icon-libreoffice'; }
				if ($keyType == 'REG_LINK') { $iconvalue = 'icon-link'; }
				if ($keyType == 'REG_MULTI_SZ') { $iconvalue = 'icon-file'; }
				if ($keyType == 'REG_RESOURCE_LIST') { $iconvalue = 'icon-file-openoffice'; }
				if ($keyType == 'REG_FULL_RESOURCE_DESCRIPTOR') { $iconvalue = 'icon-file-openoffice'; }
				if ($keyType == 'REG_RESOURCE_REQUIREMENTS_LIST') { $iconvalue = 'icon-file-openoffice'; }
				if ($keyType == 'REG_QWORD') { $iconvalue = 'icon-libreoffice'; }
				$registryarray[$registrycounter][1] = htmlentities($valueName, ENT_QUOTES, 'UTF-8');
				$registryarray[$registrycounter][2] = $keyType;
				if (strlen($valueName) > 15) { $titleName = substr(htmlentities($valueName, ENT_QUOTES, 'UTF-8'), 0, 15) . '&nbsp;[...]'; } else { $titleName = htmlentities($valueName, ENT_QUOTES, 'UTF-8'); }
				if (strlen($keyValue) > 34) { $registryarray[$registrycounter][3] = substr(htmlentities($keyValue, ENT_QUOTES, 'UTF-8'), 0, 34) . '&nbsp;[...]'; } else { $registryarray[$registrycounter][3] = htmlentities($keyValue, ENT_QUOTES, 'UTF-8'); }
				if (strlen($valueName) > 30) {
					$registryarray[$registrycounter][4] = '<a><div class="' . $iconvalue . '" style="margin-top:1px;"></div></a>&nbsp;' . str_replace(' ', '&nbsp;', substr(htmlentities($valueName, ENT_QUOTES, 'UTF-8'), 0, 30)) . '&nbsp;[...]';
				} else {
					$registryarray[$registrycounter][4] = '<a><div class="' . $iconvalue . '" style="margin-top:1px;"></div></a>&nbsp;' . str_replace(' ', '&nbsp;', htmlentities($valueName, ENT_QUOTES, 'UTF-8'));
				}
				$registryarray[$registrycounter][5] = htmlentities($keyValue, ENT_QUOTES, 'UTF-8');
				if (!preg_match('/^[a-zA-Z0-9\.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\s]*$/', $valueName) || !preg_match('/^[a-zA-Z0-9\.;\\\\:,#\[\]\*+-@_\?\^\/()~$%&=\s]*$/', $keyValue)) {
					$registryarray[$registrycounter][6] = '';
				} else {
					$registryarray[$registrycounter][6] = '<a href=\'javascript:reginfo("' . htmlentities(str_replace('\'', '%27', $valueName), ENT_QUOTES, 'UTF-8') . '","' . $keyType . '","' . preg_replace('/\r\n|\r|\n/','\n', str_replace('\\', '\\\\', htmlentities(str_replace('\'', '%27', $keyValue), ENT_QUOTES, 'UTF-8'))) . '","' .  str_replace(' ', '&nbsp;', htmlentities(str_replace('\'', '%27', $titleName), ENT_QUOTES, 'UTF-8')) . '","' . str_replace('\'', '%27', $keypathreg) . '","0","0");\'><img src="/images/enter24-black.png" width="16" height="16" border="0" title="Edit:&nbsp;' .  htmlentities($valueName, ENT_QUOTES, 'UTF-8') . '"></a>';
				}
				$registryarray[$registrycounter][7] = 'Raw Data View:' . "\n\n" . '<ValueName>' . $valueName . '</ValueName>' . "\n" . '<Type>' . $keyType . '</Type>' . "\n" . '<Value>' . $keyValue . '</Value>';
				$registrycounter = $registrycounter + 1;
			}
		}
	}
}


sort($registryarray);


$registrypagearray = array();
foreach ($registryarray as $registryrow) {
	if ($registryrow[2] . '&nbsp;&raquo;&nbsp;' . $registryrow[5] != '&nbsp;&raquo;&nbsp;') { $titleinfo = $registryrow[2] . '&nbsp;&raquo;&nbsp;' . $registryrow[5]; } else { $titleinfo = ''; }
	array_push($registrypagearray, '<tr class="rowselect" title="' . htmlentities($registryrow[7]) . '"><td>' . $registryrow[6] . '</td><td style="font-size:12px;" title="' . $titleinfo . '"><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px; height:20px;">' . $registryrow[4] . '</div></td><td align="center" style="font-size:12px;">' . $registryrow[2] . '</td><td style="font-size:12px;" title="' . $registryrow[5] . '"><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px; height:20px;">' . $registryrow[3] . '</div></td></tr>');
}

$registrypages = array_chunk($registrypagearray, 100);

$checkretkey = 0;
if (strlen(str_replace($hkey, '', $keypath)) > 1) {
	if (isset($_SESSION['permkey'])) {
		if (strtolower($keypath) != strtolower($_SESSION['permkey'])) {
			$checkretkey = 1;
		}
	} else {
		$checkretkey = 1;
	}
}
if ($checkretkey == 1) { $registrytable = $registrytable . '<tr class="rowselect"><td>&nbsp;</td><td style="font-size:12px;" title="Return to Last Key"><a href="?orderby=' . $orderby . '&lastpath=' . urlencode(implode("\\", explode('\\', substr(str_replace('\\\\', '\\', $keypath), 0, -1), -1))) . '&hkey=' . urlencode($hkey) . '&filter=' . $lastfilter . '&keypath="><div class="icon-reply"></div></a>&nbsp;..</td><td>&nbsp;</td><td>&nbsp;</td></tr>'; }

if ($pgkey > count($registrypages) - 1) { $pgkey = count($registrypages) - 1; }

if (count($registrypages) > 0) {
	foreach($registrypages[$pgkey] as $registrypgrow) {
		$registrytable = $registrytable . $registrypgrow;
	}
}

if ($registrycounter == 0) { $registrytable = $registrytable . '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="4">No Results...</td></tr>'; }

$registrytable = $registrytable . '</table>';

$registrypaging = '';
if (count($registrypages) > 1) {
	if ($pgkey > 5) {
		$registrypaging = $registrypaging . '<a href="?page=1&orderby=' . $orderby . '&lastpath=' . urlencode($keypath) . '&hkey=' . urlencode($hkey) . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&keypath="><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($registrypages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$registrypaging = $registrypaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&lastpath=' . urlencode($keypath) . '&hkey=' . urlencode($hkey) . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&keypath="><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($registrypages) > $pgkey + 6) {
		$registrypaging = $registrypaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($registrypages) . '&orderby=' . $orderby . '&lastpath=' . urlencode($keypath) . '&hkey=' . urlencode($hkey) . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&keypath="><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($registrypages) . '</span></a>';
	}
	$registrytable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $registrypaging . '</blockquote><br />' . $registrytable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $registrypaging . '</blockquote>';
}

$totalelement = count($registrypagearray);

unset($winReg);

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('registrytable'=>utf8_encode($registrytable),'totalelement'=>$totalelement,'totaltime'=>$totaltime,'regexportconf'=>str_replace('\'', '%27', $keypathreg)));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>