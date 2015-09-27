<?php

if (!isset($_SERVER['HTTP_REFERER'])) { exit; }
if (!strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . '/explorer.php')) { exit; }

if (isset($_GET['phptimeout'])) {
	set_time_limit($_GET['phptimeout']);
} else {
	set_time_limit(120);
}

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$start = $time;

include('/include/init.php');
if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) {  } else { exit; }
session_write_close();

if (isset($_GET['path'])) {
	$path = $_GET['path'];
} else {
	$path = 'C:\\';
}

if (isset($_GET['lastpath'])) {
	$lastpath = $_GET['lastpath'];
} else {
	$lastpath = '';
}

if (isset($_GET['orderby'])) {
	$orderby = $_GET['orderby'];
} else {
	$orderby = 'Name';
}

if (isset($_GET['page'])) {
	$pgkey = $_GET['page'];
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

$explorertable = '';

if ($orderby == 'Name') { $obyName = ' color:#8063C8;'; } else { $obyName = ''; }
if ($orderby == 'Date') { $obyDate = ' color:#8063C8;'; } else { $obyDate = ''; }
if ($orderby == 'Size') { $obySize = ' color:#8063C8;'; } else { $obySize = ''; }
if ($orderby == 'Type') { $obyType = ' color:#8063C8;'; } else { $obyType = ''; }

if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { $fbman = '<td width="1%" style="font-size:12px; font-weight:bold;" align="center"><a href="javascript:exploreradd()"><img src="/images/expand24-black.png" width="16" height="16" border="0" title="Add"></a></td>'; $colspan = 5; } else { $fbman = ''; $colspan = 4; }
$explorertable = $explorertable . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped"><tr>' . $fbman . '<td width="60%"><a href="?orderby=Name&path=' . urlencode($path) . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyName . '" title="Ascending Order by Name">Name</a></td><td width="15%" align="center"><a href="?orderby=Date&path=' . urlencode($path) . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyDate . '" title="Descending Order by Date Modified">Date Modified</a></td><td width="10%" align="center"><a href="?orderby=Size&path=' . urlencode($path) . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obySize . '" title="Descending Order by Size">Size</a></td><td align="center"><a href="?orderby=Type&path=' . urlencode($path) . '&filter=' . urlencode($filter) . '" style="font-size:12px; font-weight:bold;' . $obyType . '" title="Ascending Order by Type">Type</a></td></tr>';

$filename = '';
$fileexte = '';
$fileatim = '';
$filemtim = '';
$filectim = '';
$filetype = '';
$filesize = '';
$fileperm = '';
$filemity = '';
$filesyml = '';
$filemien = '';
$filcount = 0;
$dircount = 0;
$explorerarray = array();
$explorercounter = 0;
$explorerpgcount = 0;

$dirlocation = $path;
if (is_readable($dirlocation)) {
	$allfiles = scandir($dirlocation);
} else {
	$allfiles = array();
}

foreach ($allfiles as $name) {
	$chkn = $name;
	$name = $dirlocation . $name;
	$checkfilter = 1;
	if (substr($filter, 0, 1) != '-') {
		if (preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($filter))) . '/', strtolower($chkn)) || strpos(strtolower($chkn), strtolower($filter)) > -1) {
			$checkfilter = 0;
		} else {
			$checkfilter = 1;
		}
	} else {
		$notfilter = substr($filter, 1);
		if (!preg_match_all('/' . str_replace('\\', '.', str_replace('/', '.', strtolower($notfilter))) . '/', strtolower($chkn)) && !strpos(strtolower($chkn), strtolower($notfilter))) {
			$checkfilter = 0;
		} else {
			$checkfilter = 1;
		}
	}
	if (is_readable($name) && $chkn != '.' && $chkn != '..' && $checkfilter == 0) {
		$filename = pathinfo($name)['basename'];
		if (is_dir($name) && !is_file($name)) {
			$dircount = $dircount + 1;
			$fileexte = 'folder';
		} else {
			$filcount = $filcount + 1;
		}
		if (is_file($name) && !is_dir($name)) {
			$fileexte = '';
		}
		if (is_file($name) && strpos($name, '.') && !is_dir($name)) {
			$fileexte = strtolower(pathinfo($name)['extension']);
		}
		$fileatim = date('d/m/Y H:i:s', fileatime($name));
		$filemtim = date('d/m/Y H:i:s', filemtime($name));
		$filemtio = date('YmdHis', filemtime($name));
		$filectim = date('d/m/Y H:i:s', filectime($name));
		$filetype = filetype($name);
		if (is_file($name)) {
			$filesize = filesize($name);
			if ($filesize < 0) {
				$filesize = exec('explorerfs.cmd "' . $name . '"', $errorarray, $errorlevel);
				if ($errorlevel != 0) {
					$filesize = '0';
				}
			}
		} else {
			$filesize = '0';
		}
		$fileperm = fileperms($name);
		$filesizc = $filesize . '&nbsp;B';
		if ($filesize > 1023) { if (number_format(($filesize / 1024), 0, ',', '.') < 10) { $filesizc = number_format(($filesize / 1024), 2, ',', '.') . '&nbsp;KB'; } else { $filesizc = number_format(($filesize / 1024), 0, ',', '.') . '&nbsp;KB'; } }
		if ($filesize > 1048575) { if (number_format(($filesize / 1048576), 0, ',', '.') < 10) { $filesizc = number_format(($filesize / 1048576), 2, ',', '.') . '&nbsp;MB'; } else { $filesizc = number_format(($filesize / 1048576), 0, ',', '.') . '&nbsp;MB'; } }
		if ($filesize > 1073741823) { if (number_format(($filesize / 1073741824), 0, ',', '.') < 10) { $filesizc = number_format(($filesize / 1073741824), 2, ',', '.') . '&nbsp;GB'; } else { $filesizc = number_format(($filesize / 1073741824), 0, ',', '.') . '&nbsp;GB'; } }
		if ($filesize > 1099511627775) { if (number_format(($filesize / 1099511627776), 0, ',', '.') < 10) { $filesizc = number_format(($filesize / 1099511627776), 2, ',', '.') . '&nbsp;TB'; } else { $filesizc = number_format(($filesize / 1099511627776), 0, ',', '.') . '&nbsp;TB'; } }
		if (strtolower($filetype) != '') { $filetpdt = $fileexte; } else { $filetpdt = $filetype; }
		if (strtolower($filetype) == 'dir') { $filesizc = ''; }
		if (strtolower($filetpdt) != 'folder') { $filetpor = $filetpdt; } else { $filetpor = 'zzzzzzzzzz'; }
		if ($orderby == 'Name') { $ordering = strtolower($filetype); }
		if ($orderby == 'Date') { $ordering = strtolower($filemtio); }
		if ($orderby == 'Size') { if (strtolower($filetpdt) != 'folder') { $ordering = strtolower($filesize); } else { $ordering = -1; } }
		if ($orderby == 'Type') { $ordering = strtolower($filetpor); }

		$explorerarray[$explorercounter][0] = $ordering;
		
		$explorerarray[$explorercounter][1] = strtolower($filename);
		$explorerarray[$explorercounter][2] = $filename;
		$explorerarray[$explorercounter][3] = $filemtim;
		$explorerarray[$explorercounter][4] = $filesizc;
		$explorerarray[$explorercounter][5] = $filetpdt;
		
		$explorerarray[$explorercounter][6] = $filectim;
		$explorerarray[$explorercounter][7] = $fileatim;
		$explorerarray[$explorercounter][8] = $fileperm;
		
		$explorercounter = $explorercounter + 1;
		
		
	}
}

if ($orderby == 'Name' || $orderby == 'Type') {
	sort($explorerarray);
} else {
	rsort($explorerarray);
}

$explorerpagearray = array();
$tailicon = '';
$zipicon = '';

foreach ($explorerarray as $explorerrow) {
	if (is_dir($path . '\\' . $explorerrow[2] . '\\')) {
		$objtype = 'Directory';
		if (!preg_match('/^[a-zA-Z0-9 !\$%{}()=;_\'+,\.\-\[\]@#~]*$/', $explorerrow[2]) || strpos('|' . str_replace('\\\\', '\\', $path . '\\' . $explorerrow[2]), str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT'])) > 0) {
			$objnameawd = '<a>';
		} else {
			$objnameawd = '<a href="?orderby=' . $orderby . '&path=' . urlencode($path . '\\' . $explorerrow[2] . '\\') . '&lastpath=' . urlencode($path) . '&lastfilter=' . urlencode($filter) . '" title="Open: ' . $explorerrow[2] . '">';
		}
		$expicon = $objnameawd . '<div class="icon-folder" style="margin-top:1px;"></div></a>';
		if ($explorerrow[2] == '..') { $expicon = '<a href="?orderby=' . $orderby . '&path=' . urlencode(implode("\\", explode('\\', substr(str_replace('\\\\', '\\', $path), 0, -1), -1)) . '\\') . '&lastpath=' . urlencode($path) . '"><div class="icon-reply" style="margin-top:1px;"></div></a>'; }
	} else {
		$objtype = 'File';
		$downloadfile = 'download.php?download=' . urlencode($explorerrow[2]) . '&path=' . urlencode($path);
		$tailfile = 'tail.php?file=' . urlencode(str_replace('\\\\', '\\', $path . '\\' . $explorerrow[2])) . '&download=' . urlencode($explorerrow[2]) . '&path=' . urlencode($path);
		$zipfile = '7zip.php?file=' . urlencode(str_replace('\\\\', '\\', $path . '\\' . $explorerrow[2])) . '&download=' . urlencode($explorerrow[2]) . '&path=' . urlencode($path) . '&name=' . urlencode($explorerrow[2]);
		$expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-file" style="margin-top:1px;"></div></a>';
		if (strpos('||asf|3gp|asx|avi|m1s|m2a|m2s|m2v|mkv|mov|mp2|mp2v|mp4|mpa|mpe|mpeg|mpg|ogm|qt|ram|rm|rmvb|wmv|wmx|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-film" style="margin-top:1px;"></div></a>'; }
		if (strpos('||aac|aiff|atrac|au|dct|gsm|mp3|ogg|ra|vox|wav|wma|flac|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-playlist" style="margin-top:1px;"></div></a>'; }
		if (strpos('||ascx|asp|aspx|php|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-file-css" style="margin-top:1px;"></div></a>'; }
		if (strpos('||htm|html|xml|css|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-file-xml" style="margin-top:1px;"></div></a>'; }
		if (strpos('||bat|cmd|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-console" style="margin-top:1px;"></div></a>'; }
		if (strpos('||exe|msi|msu|msp|com|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-windows" style="margin-top:1px;"></div></a>'; }
		if (strpos('||dll|drv|sys|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-embed" style="margin-top:1px;"></div></a>'; }
		if (strpos('||vbs|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-cog" style="margin-top:1px;"></div></a>'; }
		if (strpos('||pdf|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-file-pdf" style="margin-top:1px;"></div></a>'; }
		if (strpos('||log|txt|csv|inf|ini|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-libreoffice" style="margin-top:1px;"></div></a>'; }
		if (strpos('||7z|arj|bz2|bzip2|cab|cpio|deb|dmg|fat|gz|gzip|hfs|iso|lha|lzh|lzma|ntfs|rar|rpm|squashfs|swm|tar|taz|tbz|tbz2|tgz|tpz|txz|vhd|wim|xar|xz|z|zip|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-file-zip" style="margin-top:1px;"></div></a>'; }
		if (strpos('||doc|docm|docx|dot|dotm|dotx|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-file-word" style="margin-top:1px;"></div></a>'; }
		if (strpos('||xl|xla|xlb|xlc|xld|xlk|xll|xlm|xls|xlsm|xlsx|xlt|xltm|xltx|xlv|xlw|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-file-excel" style="margin-top:1px;"></div></a>'; }
		if (strpos('||potm|potx|ppam|pps|ppsm|ppsx|ppt|pptm|pptx|sldm|sldx|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-file-powerpoint" style="margin-top:1px;"></div></a>'; }
		if (strpos('||art|bmp|gif|ico|j2c|j2k|jfif-tbnl|jfif|jif|jp2|jpe|jpeg|jpg|jps|jpx|png|tif|tiff|wbmp|', '|' . $explorerrow[5] . '|')) { $expicon = '<a href="' . $downloadfile . '" title="Download: ' . $explorerrow[2] . '"><div class="icon-pictures" style="margin-top:1px;"></div></a>'; }
		if (strpos('||' . str_replace(' ', '', str_replace(';', '|', str_replace(',', '|', $tailextsetting))) . '|', '|' . $explorerrow[5] . '|') || (strpos('||' . str_replace(', ,', ',,', str_replace(';', ',', $tailextsetting)), ',,') > 0 && $explorerrow[5] == '')) { $tailicon = '&nbsp;&nbsp;<a href="' . $tailfile . '" title="Tail: ' . $explorerrow[2] . '"><img src="img/titlealt.png" width="12" height="10" border="0"></a>'; } else { $tailicon = ''; }
		if (strpos('||' . str_replace(' ', '', str_replace(';', '|', str_replace(',', '|', $zipextsetting))) . '|', '|' . $explorerrow[5] . '|') || (strpos('||' . str_replace(', ,', ',,', str_replace(';', ',', $zipextsetting)), ',,') > 0 && $explorerrow[5] == '')) { $zipicon = '&nbsp;&nbsp;<a href="' . $zipfile . '" title="Extract: ' . $explorerrow[2] . '"><img src="img/titlealt.png" width="12" height="10" border="0"></a>'; } else { $zipicon = ''; }
	}
	if (strlen($explorerrow[2]) > 40) { $ElementName = substr($explorerrow[2], 0, 40) . '&nbsp;[...]'; } else { $ElementName = $explorerrow[2]; }
	if (strlen($explorerrow[2]) > 18) { $DetailName = strtolower(substr($explorerrow[2], 0, 18)) . '&nbsp;[...]'; } else { $DetailName = strtolower($explorerrow[2]); }
	if (strlen($explorerrow[2]) > 25) { $LongName = substr($explorerrow[2], 0, 25) . '&nbsp;[...]'; } else { $LongName = $explorerrow[2]; }
	if (strlen($explorerrow[5]) > 6) { $ElementExt = substr($explorerrow[5], 0, 6) . '&nbsp;[...]'; } else { $ElementExt = $explorerrow[5]; }
	$pathfilejs = str_replace('\\', '\\\\', str_replace('\\\\', '\\', $path . '\\' . $explorerrow[2]));
	if (!preg_match('/^[a-zA-Z0-9 !\$%{}()=;_\'+,\.\-\[\]@#~]*$/', $explorerrow[2]) || strpos('|' . str_replace('\\\\', '\\', $path . '\\' . $explorerrow[2]), str_replace('\\core', '', $_SERVER['DOCUMENT_ROOT'])) > 0) {
		$objnameawd = '';
	} else {
		$objnameawd = '<a href=\'javascript:exploreract("' . str_replace(' ', '&nbsp;', str_replace('\'', '%27', $explorerrow[2])) . '","' . $explorerrow[3] . '","' . $explorerrow[6] . '","' . $explorerrow[7] . '","' . $explorerrow[8] . '","' . str_replace('\'', '%27', $pathfilejs) . '","' . str_replace('\'', '%27', str_replace(' ', '&nbsp;', $DetailName)) . '","' . str_replace('\'', '%27', $LongName) . '","' . str_replace('\'', '%27', $explorerrow[2]) . '","' . rawurlencode(str_replace('\'', '%27', $explorerrow[2])) . '","' . $objtype . '");\'><img src="/images/enter24-black.png" width="16" height="16" border="0" title="Info:&nbsp;' . $explorerrow[2] . '"></a>';
	}
	if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { $fbman = '<td style="font-size:12px;" align="center">' . $objnameawd . '</td>'; } else { $fbman = ''; }
	array_push($explorerpagearray, '<tr class="rowselect">' . $fbman . '<td title="' . $explorerrow[2] . '"><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px; height:20px;">' . $expicon . '&nbsp;' . str_replace(' ', '&nbsp;', $ElementName) . $tailicon . $zipicon . '</div></td><td align="center"><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px;">' . $explorerrow[3] . '</div></td><td align="right"><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px;">' . $explorerrow[4] . '</div></td><td align="center" title="' . $explorerrow[5] . '"><div style="white-space:nowrap; table-layout:fixed; overflow:hidden; font-size:12px;">' . $ElementExt . '</div></td></tr>');
}

$explorerpages = array_chunk($explorerpagearray, 200);

if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 1) { $fbman = '<td>&nbsp;</td>'; } else { $fbman = ''; }

$checkretpath = 0;
if (strlen($path) > 3) {
	if (isset($_SESSION['permpath'])) {
		if (strtolower($path) != strtolower($_SESSION['permpath'])) {
			$checkretpath = 1;
		}
	} else {
		$checkretpath = 1;
	}
}
if ($checkretpath == 1) { $explorertable = $explorertable . '<tr class="rowselect">' . $fbman . '<td style="font-size:12px;" title="Return to Last Folder"><a href="?orderby=' . $orderby . '&path=' . urlencode(implode("\\", explode('\\', substr(str_replace('\\\\', '\\', $path), 0, -1), -1)) . '\\') . '&lastpath=' . urlencode($path) . '&filter=' . $lastfilter . '"><div class="icon-reply"></div></a>&nbsp;..</td><td>&nbsp;</td><td style="font-size:12px;" align="center">&nbsp;</td><td>&nbsp;</td></tr>'; }

if ($pgkey > count($explorerpages) - 1) { $pgkey = count($explorerpages) - 1; }

if (count($explorerpages) > 0) {
	foreach($explorerpages[$pgkey] as $file) {
		$explorertable = $explorertable . $file;
	}
}

if ($explorercounter == 0) { $explorertable = $explorertable . '<tr class="rowselect"><td style="font-size:12px;" align="center" colspan="' . $colspan . '">No Results...</td></tr>'; }

$explorertable = $explorertable . '</table>';

$explorerpaging = '';
if (count($explorerpages) > 1) {
	if ($pgkey > 5) {
		$explorerpaging = $explorerpaging . '<a href="?page=1&orderby=' . $orderby . '&path=' . urlencode($path) . '&lastpath=' . urlencode($path) . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">1</span></a>&nbsp;&nbsp;<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;';
	}
	for($i = 1; $i < count($explorerpages) + 1; $i++) {
		if ($pgkey == $i - 1) { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#2D89EF; color:#FFFFFF; margin:1px; width:30px'; } else { $selected = 'font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px'; }
		if ($i > $pgkey - 5 && $i < $pgkey + 7) {
			$explorerpaging = $explorerpaging . '<a href="?page=' . $i . '&orderby=' . $orderby . '&path=' . urlencode($path) . '&lastpath=' . urlencode($path) . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '"><span class="label" style="' . $selected . '">' . $i . '</span></a>&nbsp;&nbsp;';
		}
	}
	if (count($explorerpages) > $pgkey + 6) {
		$explorerpaging = $explorerpaging . '<div class="icon-flickr" style="font-size:12px; color:#AAAAAA;"></div>&nbsp;&nbsp;<a href="?page=' . count($explorerpages) . '&orderby=' . $orderby . '&path=' . urlencode($path) . '&lastpath=' . urlencode($path) . '&filter=' . urlencode($filter) . '&lastfilter=' . $lastfilter . '"><span class="label" style="font-size:12px; font-weight:normal; text-align:center; background-color:#CCCCCC; color:#000000; margin:1px; width:30px">' . count($explorerpages) . '</span></a>';
	}
	$explorertable = '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $explorerpaging . '</blockquote><br />' . $explorertable . '<blockquote style="font-size:12px;">Page:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $explorerpaging . '</blockquote>';
}

if (is_readable($dirlocation)) {
	$totalelement = count($explorerpagearray);
} else {
	$totalelement = utf8_encode('<span style="color:#900000;">Access Denied</span>');
}

$time = microtime();
$time = explode(" ", $time);
$time = $time[1] + $time[0];
$finish = $time;
$totaltime = number_format(($finish - $start), 3, ',', '.') . ' sec';

echo json_encode(array('explorertable'=>utf8_encode($explorertable),'totalelement'=>$totalelement,'totaltime'=>$totaltime));

flush();

if (extension_loaded('zlib')) { ob_end_flush(); }

?>