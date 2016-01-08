<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/wmiexplorer.php')) { exit; }

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
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['wmiexplorer'] > 0) {  } else { exit; }

if (!isset($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)])) { exit; } else { if ($_GET[substr(md5('$_GET' . $sessiontoken), 5, 15)] != substr(md5('$_GET' . $sessiontoken), 15, 25)) { exit; } }

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
		$pgkey = $_GET['page'];
	} else {
		$pgkey = 0;
	}
} else {
	$pgkey = 0;
}

if (isset($_GET['wminamespace'])) {
	if ($_GET['wminamespace'] != '') {
		$wminamespace = urldecode($_GET['wminamespace']);
	} else {
		$wminamespace = '';
	}
} else {
	$wminamespace = '';
}

if (isset($_GET['wmiclasses'])) {
	if ($_GET['wmiclasses'] != '') {
		$wmiclasses = urldecode($_GET['wmiclasses']);
	} else {
		$wmiclasses = '';
	}
} else {
	$wmiclasses = '';
}


$totalelement = 0;


if ($wminamespace == '' && $wmiclasses == '') {

	$wmiexptable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td><a style="font-size:12px; font-weight:bold; color:#2E92CF;">Namespace</a></td></tr>';

	$allnamespaces = '';

	function namespaces($namespace) {
		$GLOBALS['allnamespaces'] = $GLOBALS['allnamespaces'] . $namespace . ',';
		$wmi = new COM('winmgmts:{impersonationLevel=impersonate}//./' . $namespace);
		$wmisclass = $wmi->InstancesOf('__NAMESPACE');
		foreach($wmisclass as $obj) {
			namespaces($namespace .'/'. $obj->Name);
		}
	}

	namespaces('root');

	$listnamespaces = explode(",",substr($allnamespaces, 0, -1));
	sort($listnamespaces);

	$wmiexppagearray = array();
	$wmiexpcounter = 0;
	foreach ($listnamespaces as $namespacerow) {
			if ($namespacerow != 'root') {
			$checkfilter = 1;
			if (substr($filter, 0, 1) != '-') {
				if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', strtolower($namespacerow)) || strpos(strtolower($namespacerow), strtolower($filter)) > -1) {
					$checkfilter = 0;
				} else {
					$checkfilter = 1;
				}
			} else {
				$notfilter = substr($filter, 1);
				if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', strtolower($namespacerow)) && !strpos(strtolower($namespacerow), strtolower($notfilter))) {
					$checkfilter = 0;
				} else {
					$checkfilter = 1;
				}
			}
			if ($checkfilter == 0) {
				array_push($wmiexppagearray, '<tr class="rowselect"><td><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px;"><a href="?wminamespace=' . urlencode($namespacerow) . '&lastfilter=' . urlencode($filter) . '"><div class="icon-folder" style="margin-top:2px;" title="Namespace: ' . $namespacerow . '"></div></a>&nbsp;' . $namespacerow . '&nbsp;</div></td></tr>');
				$wmiexpcounter = $wmiexpcounter + 1;
				$totalelement = $totalelement + 1;
			}
		}
	}
	
	$wmiexparray = array_chunk($wmiexppagearray, 20);

	if ($pgkey > count($wmiexparray) - 1) { $pgkey = count($wmiexparray) - 1; }

	if (count($wmiexparray) > 0) {
		foreach($wmiexparray[$pgkey] as $wmirw) {
			$wmiexptable = $wmiexptable . $wmirw;
		}
	}

	if ($wmiexpcounter == 0) { $wmiexptable = $wmiexptable . '<tr class="rowselect"><td style="font-size:12px;" align="center">No Results...</td></tr>'; }
	
	$wmiexptable = $wmiexptable . '</table>';

	$wmiexppaging = '';
	if (count($wmiexparray) > 1) {
		if ($pgkey > 5) {
			$wmiexppaging = $wmiexppaging . '<a href="?page=1&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($wmiclasses) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
		}
		for($i = 1; $i < count($wmiexparray) + 1; $i++) {
			if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
			if ($i > $pgkey - 5 && $i < $pgkey + 7) {
				$wmiexppaging = $wmiexppaging . '<a href="?page=' . $i . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($wmiclasses) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
			}
		}
		if (count($wmiexparray) > $pgkey + 6) {
			$wmiexppaging = $wmiexppaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($wmiexparray) . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($wmiclasses) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($wmiexparray) . '</span></a>';
		}
		$wmiexptable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $wmiexppaging . '</blockquote><br />' . $wmiexptable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $wmiexppaging . '</blockquote>';
	}
	
	$totalelement = count($wmiexppagearray);

	$time = microtime();
	$time = explode(" ", $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

	echo json_encode(array('wmiexptable'=>utf8_encode($wmiexptable),'totalelement'=>$totalelement,'totaltime'=>$totaltime));

}


if ($wminamespace != '' && $wmiclasses == '') {

	$wmiexptable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td><a style="font-size:12px; font-weight:bold; color:#2E92CF;">Class</a></td></tr>';

	$allclasses = '';

	$wmi = new COM('winmgmts:{impersonationLevel=impersonate}//./' . $wminamespace);
	$wmisclass = $wmi->SubclassesOf();

	foreach($wmisclass as $obj) {
		if (substr($obj->Path_->Class, 0, 2) != '__') {
			$allclasses = $allclasses . $obj->Path_->Class . ',';
		}
	}

	$listclasses = explode(",",substr($allclasses, 0, -1));
	sort($listclasses);

	$wmiexppagearray = array();
	$wmiexpcounter = 0;
	foreach ($listclasses as $classrow) {
		$checkfilter = 1;
		if (substr($filter, 0, 1) != '-') {
			if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', strtolower($classrow)) || strpos(strtolower($classrow), strtolower($filter)) > -1) {
				$checkfilter = 0;
			} else {
				$checkfilter = 1;
			}
		} else {
			$notfilter = substr($filter, 1);
			if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', strtolower($classrow)) && !strpos(strtolower($classrow), strtolower($notfilter))) {
				$checkfilter = 0;
			} else {
				$checkfilter = 1;
			}
		}
		if ($checkfilter == 0) {
			array_push($wmiexppagearray, '<tr class="rowselect"><td><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px;"><a href="?wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($classrow) . '&lastfilter=' . urlencode($filter) . '"><div class="icon-folder-2" style="margin-top:2px;" title="Class: ' . $classrow . '"></div></a>&nbsp;' . $classrow . '&nbsp;</div></td></tr>');
			$wmiexpcounter = $wmiexpcounter + 1;
			$totalelement = $totalelement + 1;
		}
	}

	$wmiexparray = array_chunk($wmiexppagearray, 200);

	if ($pgkey > count($wmiexparray) - 1) { $pgkey = count($wmiexparray) - 1; }

	if (count($wmiexparray) > 0) {
		foreach($wmiexparray[$pgkey] as $wmirw) {
			$wmiexptable = $wmiexptable . $wmirw;
		}
	}

	if ($wmiexpcounter == 0) { $wmiexptable = $wmiexptable . '<tr class="rowselect"><td style="font-size:12px;" align="center">No Results...</td></tr>'; }
	
	$wmiexptable = $wmiexptable . '</table>';

	$wmiexppaging = '';
	if (count($wmiexparray) > 1) {
		if ($pgkey > 5) {
			$wmiexppaging = $wmiexppaging . '<a href="?page=1&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($wmiclasses) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
		}
		for($i = 1; $i < count($wmiexparray) + 1; $i++) {
			if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
			if ($i > $pgkey - 5 && $i < $pgkey + 7) {
				$wmiexppaging = $wmiexppaging . '<a href="?page=' . $i . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($wmiclasses) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
			}
		}
		if (count($wmiexparray) > $pgkey + 6) {
			$wmiexppaging = $wmiexppaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($wmiexparray) . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($wmiclasses) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($wmiexparray) . '</span></a>';
		}
		$wmiexptable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $wmiexppaging . '</blockquote><br />' . $wmiexptable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $wmiexppaging . '</blockquote>';
	}
	
	$totalelement = count($wmiexppagearray);

	$time = microtime();
	$time = explode(" ", $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

	echo json_encode(array('wmiexptable'=>utf8_encode($wmiexptable),'totalelement'=>$totalelement,'totaltime'=>$totaltime));

}


if ($wminamespace != '' && $wmiclasses != '') {

	$totalelement = $totalelement + 1;
	$wmiexptable = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr><td width="30%"><a style="font-size:12px; font-weight:bold; color:#2E92CF;">Property</a></td><td><a style="font-size:12px; font-weight:bold; color:#2E92CF;">Value</a></td></tr>';

	$wmi = new COM('winmgmts://./' . $wminamespace);

	$wmiclass = $wmi->ExecQuery("SELECT * FROM " . $wmiclasses);
	$countlimit = 0;
	$wmiexppagearray = array();
	$wmiexpcounter = 0;
	$wmipropcounter = 0;
	foreach($wmiclass as $obj) {
		if ($wmiexpcounter > 0 && $wmipropcounter > 0) { array_push($wmiexppagearray, '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="2"><hr /><hr /><hr /></td></tr>'); $totalelement = $totalelement + 1; }
		$wmipropcounter = 0;
		foreach($obj->Properties_ as $wmiprop) {
			if (is_object($wmiprop->Value) == 1) {
				$wmipropobjres = '';
				foreach($wmiprop->Value as $wmipropobj) {
					$wmipropobjres = $wmipropobjres . $wmipropobj . ', ';
				}
				$wmipropobjres = str_replace(', No Data', '', $wmipropobjres . 'No Data');
				if (($wmipropobjres != '' && $wmipropobjres != ' ') || $wmiexplorerhidevalues == 'Disable') {
					$checkfilter = 1;
					if (substr($filter, 0, 1) != '-') {
						if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', strtolower($wmiprop->Name . $wmipropobjres)) || strpos(strtolower($wmiprop->Name . $wmipropobjres), strtolower($filter)) > -1) {
							$checkfilter = 0;
						} else {
							$checkfilter = 1;
						}
					} else {
						$notfilter = substr($filter, 1);
						if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', strtolower($wmiprop->Name . $wmipropobjres)) && !strpos(strtolower($wmiprop->Name . $wmipropobjres), strtolower($notfilter))) {
							$checkfilter = 0;
						} else {
							$checkfilter = 1;
						}
					}
					if ($checkfilter == 0) {
						array_push($wmiexppagearray, '<tr class="rowselect"><td style="font-size:12px;">' . $wmiprop->Name . '</td><td style="font-size:12px;">' . $wmipropobjres . '</td></tr>');
						$wmiexpcounter = $wmiexpcounter + 1;
						$wmipropcounter = $wmipropcounter + 1;
					}
				}
			} else {
				if (($wmiprop->Value != '' && $wmiprop->Value != ' ') || $wmiexplorerhidevalues == 'Disable') {
					$wmipropres = $wmiprop->Value;
					if (strlen($wmipropres) == 25 && strpos($wmipropres, '.') == 14 && strpos($wmipropres, '+') == 21) {
						$wmipropres = substr($wmipropres, 6, 2) . '/' . substr($wmipropres, 4, 2) . '/' . substr($wmipropres, 0, 4) . ' ' . substr($wmipropres, 8, 2) . ':' . substr($wmipropres, 10, 2) . ':' . substr($wmipropres, 12, 2);
					}
					if (substr($filter, 0, 1) != '-') {
						if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', strtolower($wmiprop->Name . $wmipropres)) || strpos(strtolower($wmiprop->Name . $wmipropres), strtolower($filter)) > -1) {
							$checkfilter = 0;
						} else {
							$checkfilter = 1;
						}
					} else {
						$notfilter = substr($filter, 1);
						if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', strtolower($wmiprop->Name . $wmipropres)) && !strpos(strtolower($wmiprop->Name . $wmipropres), strtolower($notfilter))) {
							$checkfilter = 0;
						} else {
							$checkfilter = 1;
						}
					}
					if ($checkfilter == 0) {
						array_push($wmiexppagearray, '<tr class="rowselect"><td style="font-size:12px;">' . $wmiprop->Name . '</td><td style="font-size:12px;">' . $wmipropres . '</td></tr>');
						$wmiexpcounter = $wmiexpcounter + 1;
						$wmipropcounter = $wmipropcounter + 1;
					}
				}
			}
		}
	$countlimit = $countlimit + 1;
	if ($countlimit > 1000) { break; }
	}

	$wmiexparray = array_chunk($wmiexppagearray, 200);

	if ($pgkey > count($wmiexparray) - 1) { $pgkey = count($wmiexparray) - 1; }

	if (count($wmiexparray) > 0) {
		foreach($wmiexparray[$pgkey] as $wmirw) {
			$wmiexptable = $wmiexptable . $wmirw;
		}
	}

	if ($wmiexpcounter == 0) { $wmiexptable = $wmiexptable . '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="2">No Results...</td></tr>'; }
	
	$wmiexptable = $wmiexptable . '</table>';

	$wmiexppaging = '';
	if (count($wmiexparray) > 1) {
		if ($pgkey > 5) {
			$wmiexppaging = $wmiexppaging . '<a href="?page=1&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($wmiclasses) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
		}
		for($i = 1; $i < count($wmiexparray) + 1; $i++) {
			if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
			if ($i > $pgkey - 5 && $i < $pgkey + 7) {
				$wmiexppaging = $wmiexppaging . '<a href="?page=' . $i . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($wmiclasses) . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
			}
		}
		if (count($wmiexparray) > $pgkey + 6) {
			$wmiexppaging = $wmiexppaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($wmiexparray) . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '&wminamespace=' . urlencode($wminamespace) . '&wmiclasses=' . urlencode($wmiclasses) . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($wmiexparray) . '</span></a>';
		}
		$wmiexptable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $wmiexppaging . '</blockquote><br />' . $wmiexptable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $wmiexppaging . '</blockquote>';
	}


	$time = microtime();
	$time = explode(" ", $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

	echo json_encode(array('wmiexptable'=>utf8_encode($wmiexptable),'totalelement'=>$totalelement,'totaltime'=>$totaltime));

}

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>