<?php

$programcounter = 0;

$xml = '<programs>' . "\n";

$UninstallKeyPath = 'HKEY_LOCAL_MACHINE\\SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Uninstall';
$keyNames = $winReg->GetSubKeys($UninstallKeyPath);
if (is_array($keyNames)) {
	foreach ($keyNames as $programs) {
		
		$valueNames = $winReg->GetValueNames($UninstallKeyPath . '\\' . $programs);
		if (is_array($valueNames)) {
				
			$programchk = '';
			foreach ($valueNames as $program) { $programchk = $programchk . '#' . $program . '#'; }
				
			if (preg_match('/#Publisher#/', $programchk)) { $Vendor = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'Publisher'); } else { $Vendor = '-'; }
			if (preg_match('/#DisplayName#/', $programchk)) { $Name = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'DisplayName'); } else { $Name = '-'; }
			if (preg_match('/#DisplayVersion#/', $programchk)) { $Version = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'DisplayVersion'); } else { $Version = '-'; }
			if (preg_match('/#InstallDate#/', $programchk)) { $InstallDate = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'InstallDate'); } else { $InstallDate = '-'; }
			if (preg_match('/#UninstallString#/', $programchk)) { $UninstallString = $winReg->ReadValue($UninstallKeyPath . '\\' . $programs, 'UninstallString'); } else { $UninstallString = ''; }
			$IdentifyingNumber = $programs;
				
			if ($Name != '-') {
				$programcounter = $programcounter + 1;
				$xml = $xml . '	<id' . $programcounter . '>' . "\n";
				$xml = $xml . '		<Vendor>' . urlencode($Vendor) . '</Vendor>';
				$xml = $xml . '<Name>' . urlencode($Name) . '</Name>';
				$xml = $xml . '<Version>' . urlencode($Version) . '</Version>';
				$xml = $xml . '<InstallDate>' . urlencode($InstallDate) . '</InstallDate>';
				$xml = $xml . '<IdentifyingNumber>' . urlencode($IdentifyingNumber) . '</IdentifyingNumber>';
				if (preg_match('/\{........-....-....-....-............\}/', $UninstallString) && preg_match('/\{........-....-....-....-............\}/', $IdentifyingNumber) && strlen($IdentifyingNumber) == 38) { $Uninstallable = 1; } else { $Uninstallable = 0; }
				$xml = $xml . '<Uninstallable>' . $Uninstallable . '</Uninstallable>' . "\n";
				$xml = $xml . '	</id' . $programcounter . '>' . "\n";
			}
			
		}

	}
}

$programs_status = 1;

$xml = $xml . '</programs>';
$zp = gzopen($_SESSION['agentpath'] . '\\temp\\programs.xml.gz', "w9");
gzwrite($zp, $xml);
gzclose($zp);

?>