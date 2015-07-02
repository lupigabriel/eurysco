<?php

$xml = '<inventory>' . "\n";



$xml = $xml . '	<Computer_System>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_ComputerSystem");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->Caption != '') { $xml = $xml . '<Computer_System_Caption>' . $obj->Caption . '</Computer_System_Caption>'; }
	if ($obj->Domain != '') { $xml = $xml . '<Computer_System_Domain>' . $obj->Domain . '</Computer_System_Domain>'; }
	foreach($obj->Properties_ as $wmiprop) {
		if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|DNSHostName|')) > -1) {
			if ($obj->DNSHostName != '') { $xml = $xml . '<Computer_System_DNSHostName>' . $obj->DNSHostName . '</Computer_System_DNSHostName>'; }
		}
	}
	if ($obj->Manufacturer != '') { $xml = $xml . '<Computer_System_Manufacturer>' . urlencode($obj->Manufacturer) . '</Computer_System_Manufacturer>'; }
	if ($obj->Model != '') { $xml = $xml . '<Computer_System_Model>' . urlencode($obj->Model) . '</Computer_System_Model>'; }
	if ($obj->Description != '') { $xml = $xml . '<Computer_System_Description>' . $obj->Description . '</Computer_System_Description>'; }
	if ($obj->SystemType != '') { $xml = $xml . '<Computer_System_SystemType>' . $obj->SystemType . '</Computer_System_SystemType>'; }
	if ($obj->WakeUpType != '') { $xml = $xml . '<Computer_System_WakeUpType>' . $obj->WakeUpType . '</Computer_System_WakeUpType>'; }
	if ($obj->PrimaryOwnerName != '') { $xml = $xml . '<Computer_System_PrimaryOwnerName>' . urlencode($obj->PrimaryOwnerName) . '</Computer_System_PrimaryOwnerName>'; }
	if ($obj->NumberOfProcessors != '') { $xml = $xml . '<Computer_System_NumberOfProcessors>' . $obj->NumberOfProcessors . '</Computer_System_NumberOfProcessors>'; }
	foreach($obj->Properties_ as $wmiprop) {
		if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfLogicalProcessors|')) > -1) {
			if ($obj->NumberOfLogicalProcessors != '') { $xml = $xml . '<Computer_System_NumberOfLogicalProcessors>' . $obj->NumberOfLogicalProcessors . '</Computer_System_NumberOfLogicalProcessors>'; }
		}
	}
	if ($obj->TotalPhysicalMemory != '') { $xml = $xml . '<Computer_System_TotalPhysicalMemory>' . number_format(round($obj->TotalPhysicalMemory / 1024 / 1024, 0), 0, ',', '.') . ' MB</Computer_System_TotalPhysicalMemory>'; }
	$xml = $xml . "\n";
}
$xml = $xml . '	</Computer_System>' . "\n";



$xml = $xml . '	<Operating_System>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_OperatingSystem");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->Caption != '') { $xml = $xml . '<Operating_System_Caption>' . urlencode($obj->Caption) . '</Operating_System_Caption>'; }
	if ($obj->CSDVersion != '') { $xml = $xml . '<Operating_System_CSDVersion>' . $obj->CSDVersion . '</Operating_System_CSDVersion>'; }
	if ($obj->Version != '') { $xml = $xml . '<Operating_System_Version>' . $obj->Version . '</Operating_System_Version>'; }
	if ($obj->Manufacturer != '') { $xml = $xml . '<Operating_System_Manufacturer>' . $obj->Manufacturer . '</Operating_System_Manufacturer>'; }
	foreach($obj->Properties_ as $wmiprop) {
		if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|OSArchitecture|')) > -1) {
			if ($obj->OSArchitecture != '') { $xml = $xml . '<Operating_System_OSArchitecture>' . str_replace('-', ' ', $obj->OSArchitecture) . '</Operating_System_OSArchitecture>'; }
		}
	}
	if ($obj->OSLanguage != '') { $xml = $xml . '<Operating_System_OSLanguage>' . str_replace('-', ' ', $obj->OSLanguage) . '</Operating_System_OSLanguage>'; }
	if ($obj->SerialNumber != '') { $xml = $xml . '<Operating_System_SerialNumber>' . $obj->SerialNumber . '</Operating_System_SerialNumber>'; }
	if ($obj->CSName != '') { $xml = $xml . '<Operating_System_CSName>' . $obj->CSName . '</Operating_System_CSName>'; }
	if ($obj->WindowsDirectory != '') { $xml = $xml . '<Operating_System_WindowsDirectory>' . urlencode($obj->WindowsDirectory) . '</Operating_System_WindowsDirectory>'; }
	if ($obj->Organization != '') { $xml = $xml . '<Operating_System_Organization>' . urlencode($obj->Organization) . '</Operating_System_Organization>'; }
	if ($obj->RegisteredUser != '') { $xml = $xml . '<Operating_System_RegisteredUser>' . urlencode($obj->RegisteredUser) . '</Operating_System_RegisteredUser>'; }
	if ($obj->CurrentTimeZone != '') { $xml = $xml . '<Operating_System_CurrentTimeZone>' . $obj->CurrentTimeZone . '</Operating_System_CurrentTimeZone>'; }
	if ($obj->InstallDate != '') { $xml = $xml . '<Operating_System_InstallDate>' . substr($obj->InstallDate, 6, 2) . '/' . substr($obj->InstallDate, 4, 2) . '/' . substr($obj->InstallDate, 0, 4) . ' ' . substr($obj->InstallDate, 8, 2) . ':' . substr($obj->InstallDate, 10, 2) . ':' . substr($obj->InstallDate, 12, 2) . '</Operating_System_InstallDate>'; }
	if ($obj->LastBootUpTime != '') { $xml = $xml . '<Operating_System_LastBootUpTime>' . substr($obj->LastBootUpTime, 6, 2) . '/' . substr($obj->LastBootUpTime, 4, 2) . '/' . substr($obj->LastBootUpTime, 0, 4) . ' ' . substr($obj->LastBootUpTime, 8, 2) . ':' . substr($obj->LastBootUpTime, 10, 2) . ':' . substr($obj->LastBootUpTime, 12, 2) . '</Operating_System_LastBootUpTime>'; }
	if ($obj->LocalDateTime != '') { $xml = $xml . '<Operating_System_LocalDateTime>' . substr($obj->LocalDateTime, 6, 2) . '/' . substr($obj->LocalDateTime, 4, 2) . '/' . substr($obj->LocalDateTime, 0, 4) . ' ' . substr($obj->LocalDateTime, 8, 2) . ':' . substr($obj->LocalDateTime, 10, 2) . ':' . substr($obj->LocalDateTime, 12, 2) . '</Operating_System_LocalDateTime>'; }
	$xml = $xml . "\n";
}
$xml = $xml . '	</Operating_System>' . "\n";



$bios_count = 0;
$xml = $xml . '	<Bios>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT Manufacturer, Name, ReleaseDate, SerialNumber, SMBIOSBIOSVersion FROM Win32_BIOS");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->Name != '') { $xml = $xml . '<Bios_Name' . $bios_count . '>' . $obj->Name . '</Bios_Name' . $bios_count . '>'; }
	if ($obj->Manufacturer != '') { $xml = $xml . '<Bios_Manufacturer' . $bios_count . '>' . urlencode($obj->Manufacturer) . '</Bios_Manufacturer' . $bios_count . '>'; }
	if ($obj->SMBIOSBIOSVersion != '') { $xml = $xml . '<Bios_SMBIOSBIOSVersion' . $bios_count . '>' . $obj->SMBIOSBIOSVersion . '</Bios_SMBIOSBIOSVersion' . $bios_count . '>'; }
	if ($obj->ReleaseDate != '') { $xml = $xml . '<Bios_ReleaseDate' . $bios_count . '>' . substr($obj->ReleaseDate, 6, 2) . '/' . substr($obj->ReleaseDate, 4, 2) . '/' . substr($obj->ReleaseDate, 0, 4) . '</Bios_ReleaseDate' . $bios_count . '>'; }
	if ($obj->SerialNumber != '' && $obj->SerialNumber != ' ') { $xml = $xml . '<Bios_SerialNumber' . $bios_count . '>' . $obj->SerialNumber . '</Bios_SerialNumber' . $bios_count . '>'; }
	$xml = $xml . "\n";
	$bios_count = $bios_count + 1;
}
$xml = $xml . '	</Bios>' . "\n";



$processor_count = 0;
$xml = $xml . '	<Processor>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_Processor");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->Name != '') { $xml = $xml . '<Processor_Name' . $processor_count . '>' . urlencode($obj->Name) . '</Processor_Name' . $processor_count . '>'; }
	if ($obj->DeviceID != '') { $xml = $xml . '<Processor_DeviceID' . $processor_count . '>' . $obj->DeviceID . '</Processor_DeviceID' . $processor_count . '>'; }
	if ($obj->Manufacturer != '') { $xml = $xml . '<Processor_Manufacturer' . $processor_count . '>' . urlencode($obj->Manufacturer) . '</Processor_Manufacturer' . $processor_count . '>'; }
	if ($obj->Description != '') { $xml = $xml . '<Processor_Description' . $processor_count . '>' . urlencode($obj->Description) . '</Processor_Description' . $processor_count . '>'; }
	if ($obj->SocketDesignation != '') { $xml = $xml . '<Processor_SocketDesignation' . $processor_count . '>' . urlencode($obj->SocketDesignation) . '</Processor_SocketDesignation' . $processor_count . '>'; }
	if ($obj->AddressWidth != '') { $xml = $xml . '<Processor_AddressWidth' . $processor_count . '>' . $obj->AddressWidth . '</Processor_AddressWidth' . $processor_count . '>'; }
	if ($obj->MaxClockSpeed != '') { $xml = $xml . '<Processor_MaxClockSpeed' . $processor_count . '>' . number_format($obj->MaxClockSpeed, 0, ',', '.') . ' Mhz</Processor_MaxClockSpeed' . $processor_count . '>'; }
	if ($obj->ProcessorId != '') { $xml = $xml . '<Processor_ProcessorId' . $processor_count . '>' . $obj->ProcessorId . '</Processor_ProcessorId' . $processor_count . '>'; }
	foreach($obj->Properties_ as $wmiprop) {
		if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfCores|')) > -1) {
			if ($obj->NumberOfCores != '') { $xml = $xml . '<Processor_NumberOfCores' . $processor_count . '>' . $obj->NumberOfCores . '</Processor_NumberOfCores' . $processor_count . '>'; }
		}
		if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfLogicalProcessors|')) > -1) {
			if ($obj->NumberOfLogicalProcessors != '') { $xml = $xml . '<Processor_NumberOfLogicalProcessors' . $processor_count . '>' . $obj->NumberOfLogicalProcessors . '</Processor_NumberOfLogicalProcessors' . $processor_count . '>'; }
		}
	}
	$xml = $xml . "\n";
	$processor_count = $processor_count + 1;
}
$xml = $xml . '	</Processor>' . "\n";



$video_controller_count = 0;
$xml = $xml . '	<Video_Controller>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT DeviceID, DriverVersion, InfFilename, Name, VideoModeDescription, VideoProcessor FROM Win32_VideoController");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->Name != '') { $xml = $xml . '<Video_Controller_Name' . $video_controller_count . '>' . urlencode($obj->Name) . '</Video_Controller_Name' . $video_controller_count . '>'; }
	if ($obj->DeviceID != '') { $xml = $xml . '<Video_Controller_DeviceID' . $video_controller_count . '>' . $obj->DeviceID . '</Video_Controller_DeviceID' . $video_controller_count . '>'; }
	if ($obj->VideoProcessor != '') { $xml = $xml . '<Video_Controller_VideoProcessor' . $video_controller_count . '>' . urlencode($obj->VideoProcessor) . '</Video_Controller_VideoProcessor' . $video_controller_count . '>'; }
	if ($obj->DriverVersion != '') { $xml = $xml . '<Video_Controller_DriverVersion' . $video_controller_count . '>' . $obj->DriverVersion . '</Video_Controller_DriverVersion' . $video_controller_count . '>'; }
	if ($obj->InfFilename != '') { $xml = $xml . '<Video_Controller_InfFilename' . $video_controller_count . '>' . $obj->InfFilename . '</Video_Controller_InfFilename' . $video_controller_count . '>'; }
	if ($obj->VideoModeDescription != '') { $xml = $xml . '<Video_Controller_VideoModeDescription' . $video_controller_count . '>' . $obj->VideoModeDescription . '</Video_Controller_VideoModeDescription' . $video_controller_count . '>'; }
	$xml = $xml . "\n";
	$video_controller_count = $video_controller_count + 1;
}
$xml = $xml . '	</Video_Controller>' . "\n";



$baseboard_count = 0;
$xml = $xml . '	<Motherboard>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT Manufacturer, Product FROM Win32_BaseBoard");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->Manufacturer != '') { $xml = $xml . '<Motherboard_Manufacturer' . $baseboard_count . '>' . urlencode($obj->Manufacturer) . '</Motherboard_Manufacturer' . $baseboard_count . '>'; }
	if ($obj->Product != '') { $xml = $xml . '<Motherboard_Product' . $baseboard_count . '>' . urlencode($obj->Product) . '</Motherboard_Product' . $baseboard_count . '>'; }
	$xml = $xml . "\n";
	$baseboard_count = $baseboard_count + 1;
}
$xml = $xml . '	</Motherboard>' . "\n";


$physical_memory_count = 0;
$xml = $xml . '	<Physical_Memory>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT BankLabel, Capacity, DeviceLocator, Speed FROM Win32_PhysicalMemory");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->DeviceLocator != '') { $xml = $xml . '<Physical_Memory_DeviceLocator' . $physical_memory_count . '>' . $obj->DeviceLocator . '</Physical_Memory_DeviceLocator' . $physical_memory_count . '>'; }
	if ($obj->BankLabel != '') { $xml = $xml . '<Physical_Memory_BankLabel' . $physical_memory_count . '>' . $obj->BankLabel . '</Physical_Memory_BankLabel' . $physical_memory_count . '>'; }
	if ($obj->Capacity != '') { $xml = $xml . '<Physical_Memory_Capacity' . $physical_memory_count . '>' . number_format(round($obj->Capacity / 1024 / 1024, 0), 0, ',', '.') . '</Physical_Memory_Capacity' . $physical_memory_count . '>'; }
	if ($obj->Speed != '') { $xml = $xml . '<Physical_Memory_Speed' . $physical_memory_count . '>' . $obj->Speed . '</Physical_Memory_Speed' . $physical_memory_count . '>'; }
	$xml = $xml . "\n";
	$physical_memory_count = $physical_memory_count + 1;
}
$xml = $xml . '	</Physical_Memory>' . "\n";


$logical_disk_count = 0;
$xml = $xml . '	<Logical_Disk>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT Description, FileSystem, FreeSpace, Name, Size FROM Win32_LogicalDisk");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->Name != '') { $xml = $xml . '<Logical_Disk_Name' . $logical_disk_count . '>' . $obj->Name . '</Logical_Disk_Name' . $logical_disk_count . '>'; }
	if ($obj->Description != '') { $xml = $xml . '<Logical_Disk_Description' . $logical_disk_count . '>' . $obj->Description . '</Logical_Disk_Description' . $logical_disk_count . '>'; }
	if ($obj->FileSystem != '') { $xml = $xml . '<Logical_Disk_FileSystem' . $logical_disk_count . '>' . $obj->FileSystem . '</Logical_Disk_FileSystem' . $logical_disk_count . '>'; }
	if ($obj->Size != '') {
		$sizebyte = $obj->Size;
		$sizeview = $sizebyte . ' B';
		if ($sizebyte > 1023) { if (number_format(($sizebyte / 1024), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1024), 2, ',', '.') . ' KB'; } else { $sizeview = number_format(($sizebyte / 1024), 0, ',', '.') . ' KB'; } }
		if ($sizebyte > 1048575) { if (number_format(($sizebyte / 1048576), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1048576), 2, ',', '.') . ' MB'; } else { $sizeview = number_format(($sizebyte / 1048576), 0, ',', '.') . ' MB'; } }
		if ($sizebyte > 1073741823) { if (number_format(($sizebyte / 1073741824), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1073741824), 2, ',', '.') . ' GB'; } else { $sizeview = number_format(($sizebyte / 1073741824), 0, ',', '.') . ' GB'; } }
		if ($sizebyte > 1099511627775) { if (number_format(($sizebyte / 1099511627776), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1099511627776), 2, ',', '.') . ' TB'; } else { $sizeview = number_format(($sizebyte / 1099511627776), 0, ',', '.') . ' TB'; } }
		$xml = $xml . '<Logical_Disk_Size' . $logical_disk_count . '>' . $sizeview . '</Logical_Disk_Size' . $logical_disk_count . '>';
	}
	if ($obj->FreeSpace != '') {
		$sizebyte = $obj->FreeSpace;
		$sizeview = $sizebyte . ' B';
		if ($sizebyte > 1023) { if (number_format(($sizebyte / 1024), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1024), 2, ',', '.') . ' KB'; } else { $sizeview = number_format(($sizebyte / 1024), 0, ',', '.') . ' KB'; } }
		if ($sizebyte > 1048575) { if (number_format(($sizebyte / 1048576), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1048576), 2, ',', '.') . ' MB'; } else { $sizeview = number_format(($sizebyte / 1048576), 0, ',', '.') . ' MB'; } }
		if ($sizebyte > 1073741823) { if (number_format(($sizebyte / 1073741824), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1073741824), 2, ',', '.') . ' GB'; } else { $sizeview = number_format(($sizebyte / 1073741824), 0, ',', '.') . ' GB'; } }
		if ($sizebyte > 1099511627775) { if (number_format(($sizebyte / 1099511627776), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1099511627776), 2, ',', '.') . ' TB'; } else { $sizeview = number_format(($sizebyte / 1099511627776), 0, ',', '.') . ' TB'; } }
		$xml = $xml . '<Logical_Disk_FreeSpace' . $logical_disk_count . '>' . $sizeview . '</Logical_Disk_FreeSpace' . $logical_disk_count . '>';
	}
	$xml = $xml . "\n";
	$logical_disk_count = $logical_disk_count + 1;
}
$xml = $xml . '	</Logical_Disk>' . "\n";



$network_configuration_count = 0;
$xml = $xml . '	<Network_Configuration>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT DefaultIPGateway, Description, DNSServerSearchOrder, IPAddress, IPSubnet, MACAddress FROM Win32_NetworkAdapterConfiguration");
foreach($wmisclass as $obj) {
	if ($obj->MACAddress != '' && !is_null($obj->IPAddress)) {
		$xml = $xml . '		';
		$IPAddress = '';
		$IPAddresses = $obj->IPAddress;
		foreach ($IPAddresses as $key=>$value) {
			if ($IPAddress == '') { $IPAddress = $value; } else { $IPAddress = $IPAddress . '  ( ' . $value . ' )'; }
		}
		$IPSubnet = '';
		if (!is_null($obj->IPSubnet)) {
			$IPSubnets = $obj->IPSubnet;
			foreach ($IPSubnets as $key=>$value) {
				if ($IPSubnet == '') { $IPSubnet = $value; }
			}
		} else {
			$IPSubnet = '-';
		}
		$DNSServerSearchOrder = '';
		if (!is_null($obj->DNSServerSearchOrder)) {
			$DNSServersSearchOrder = $obj->DNSServerSearchOrder;
			foreach ($DNSServersSearchOrder as $key=>$value) {
				if ($DNSServerSearchOrder == '') { $DNSServerSearchOrder = $value; } else { $DNSServerSearchOrder = $DNSServerSearchOrder . '  /  ' . $value; }
			}
		} else {
			$DNSServerSearchOrder = '-';
		}
		$DefaultIPGateway = '';
		if (!is_null($obj->DefaultIPGateway)) {
			$DefaultIPGateways = $obj->DefaultIPGateway;
			foreach ($DefaultIPGateways as $key=>$value) {
				if ($DefaultIPGateway == '') { $DefaultIPGateway = $value; } else { $DefaultIPGateway = $DefaultIPGateway . '  /  ' . $value; }
			}
		} else {
			$DefaultIPGateway = '-';
		}
		if ($obj->Description != '') { $xml = $xml . '<Network_Configuration_Description' . $network_configuration_count . '>' . urlencode($obj->Description) . '</Network_Configuration_Description' . $network_configuration_count . '>'; }
		if ($obj->MACAddress != '') { $xml = $xml . '<Network_Configuration_MACAddress' . $network_configuration_count . '>' . $obj->MACAddress . '</Network_Configuration_MACAddress' . $network_configuration_count . '>'; }
		if ($obj->IPAddress != '') { $xml = $xml . '<Network_Configuration_IPAddress' . $network_configuration_count . '>' . $IPAddress . '</Network_Configuration_IPAddress' . $network_configuration_count . '>'; }
		if ($obj->IPSubnet != '') { $xml = $xml . '<Network_Configuration_IPSubnet' . $network_configuration_count . '>' . $IPSubnet . '</Network_Configuration_IPSubnet' . $network_configuration_count . '>'; }
		if ($obj->DefaultIPGateway != '') { $xml = $xml . '<Network_Configuration_DefaultIPGateway' . $network_configuration_count . '>' . $DefaultIPGateway . '</Network_Configuration_DefaultIPGateway' . $network_configuration_count . '>'; }
		if ($obj->DNSServerSearchOrder != '') { $xml = $xml . '<Network_Configuration_DNSServerSearchOrder' . $network_configuration_count . '>' . $DNSServerSearchOrder . '</Network_Configuration_DNSServerSearchOrder' . $network_configuration_count . '>'; }
		$xml = $xml . "\n";
		$network_configuration_count = $network_configuration_count + 1;
	}
}
$xml = $xml . '	</Network_Configuration>' . "\n";



$driver_count = 0;
$xml = $xml . '	<Drivers>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT DeviceName, FriendlyName, DriverProviderName, DriverVersion, DeviceClass FROM Win32_PnPSignedDriver");
foreach($wmisclass as $obj) {
	if ($obj->DeviceName != '') {
		$xml = $xml . '		';
		$xml = $xml . '<Driver_DeviceName' . $driver_count . '>' . urlencode($obj->DeviceName) . '</Driver_DeviceName' . $driver_count . '>';
		if ($obj->DriverVersion != '') { $xml = $xml . '<Driver_DriverVersion' . $driver_count . '>' . $obj->DriverVersion . '</Driver_DriverVersion' . $driver_count . '>'; }
		if ($obj->FriendlyName != '') { $xml = $xml . '<Driver_FriendlyName' . $driver_count . '>' . urlencode($obj->FriendlyName) . '</Driver_FriendlyName' . $driver_count . '>'; }
		if ($obj->DriverProviderName != '') { $xml = $xml . '<Driver_DriverProviderName' . $driver_count . '>' . $obj->DriverProviderName . '</Driver_DriverProviderName' . $driver_count . '>'; }
		if ($obj->DeviceClass != '') { $xml = $xml . '<Driver_DeviceClass' . $driver_count . '>' . $obj->DeviceClass . '</Driver_DeviceClass' . $driver_count . '>'; }
		$xml = $xml . "\n";
		$driver_count = $driver_count + 1;
	}
}
$xml = $xml . '	</Drivers>' . "\n";



$printer_count = 0;
$xml = $xml . '	<Printer>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT DriverName, Name, PortName FROM Win32_Printer");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->Name != '') { $xml = $xml . '<Printer_Name' . $printer_count . '>' . urlencode($obj->Name) . '</Printer_Name' . $printer_count . '>'; }
	if ($obj->DriverName != '') { $xml = $xml . '<Printer_DriverName' . $printer_count . '>' . urlencode($obj->DriverName) . '</Printer_DriverName' . $printer_count . '>'; }
	if ($obj->PortName != '') { $xml = $xml . '<Printer_PortName' . $printer_count . '>' . $obj->PortName . '</Printer_PortName' . $printer_count . '>'; }
	$xml = $xml . "\n";
	$printer_count = $printer_count + 1;
}
$xml = $xml . '	</Printer>' . "\n";



$users_count = 0;
$xml = $xml . '	<Users>' . "\n";
$usersarray = array();
$userscounter = 0;
$wmisclass = $wmi->ExecQuery("SELECT PartComponent FROM Win32_GroupUser WHERE GroupComponent = \"Win32_Group.Domain='" . $cs_computername . "',Name='Administrators'\"");
foreach($wmisclass as $obj) {
$usersuser = '';
$usersgroup = '';
$usersuser = explode('\\', str_replace('"', '', preg_replace('/.*Domain=/', '', str_replace('",Name="', '\\', $obj->PartComponent))));
if (strpos($obj->PartComponent, 'UserAccount') == true) { $usersgroup = 'User Account'; }
if (strpos($obj->PartComponent, 'Group') == true) { $usersgroup = 'Group'; }
$usersarray[$userscounter][0] = $usersuser[1];
$usersarray[$userscounter][1] = $usersuser[0];
$usersarray[$userscounter][2] = $usersgroup;
$userscounter = $userscounter + 1;					
}
sort($usersarray);
foreach ($usersarray as $usersrow) {
	$xml = $xml . '		';
	$xml = $xml . '<User_Name' . $users_count . '>' . $usersrow[0] . '</User_Name' . $users_count . '>';
	$xml = $xml . '<User_Group' . $users_count . '>Administrators</User_Group' . $users_count . '>';
	if ($usersrow[1] != '') { $xml = $xml . '<User_Domain' . $users_count . '>' . $usersrow[1] . '</User_Domain' . $users_count . '>'; }
	if ($usersrow[2] != '') { $xml = $xml . '<User_Type' . $users_count . '>' . $usersrow[2] . '</User_Type' . $users_count . '>'; }
	$xml = $xml . "\n";
	$users_count = $users_count + 1;
}
$usersarray = array();
$userscounter = 0;
$wmisclass = $wmi->ExecQuery("SELECT PartComponent FROM Win32_GroupUser WHERE GroupComponent = \"Win32_Group.Domain='" . $cs_computername . "',Name='Power Users'\"");
foreach($wmisclass as $obj) {
$usersuser = '';
$usersgroup = '';
$usersuser = explode('\\', str_replace('"', '', preg_replace('/.*Domain=/', '', str_replace('",Name="', '\\', $obj->PartComponent))));
if (strpos($obj->PartComponent, 'UserAccount') == true) { $usersgroup = 'User Account'; }
if (strpos($obj->PartComponent, 'Group') == true) { $usersgroup = 'Group'; }
$usersarray[$userscounter][0] = $usersuser[1];
$usersarray[$userscounter][1] = $usersuser[0];
$usersarray[$userscounter][2] = $usersgroup;
$userscounter = $userscounter + 1;					
}
sort($usersarray);
foreach ($usersarray as $usersrow) {
	$xml = $xml . '		';
	$xml = $xml . '<User_Name' . $users_count . '>' . $usersrow[0] . '</User_Name' . $users_count . '>';
	$xml = $xml . '<User_Group' . $users_count . '>Power Users</User_Group' . $users_count . '>';
	if ($usersrow[1] != '') { $xml = $xml . '<User_Domain' . $users_count . '>' . $usersrow[1] . '</User_Domain' . $users_count . '>'; }
	if ($usersrow[2] != '') { $xml = $xml . '<User_Type' . $users_count . '>' . $usersrow[2] . '</User_Type' . $users_count . '>'; }
	$xml = $xml . "\n";
	$users_count = $users_count + 1;
}
$usersarray = array();
$userscounter = 0;
$wmisclass = $wmi->ExecQuery("SELECT PartComponent FROM Win32_GroupUser WHERE GroupComponent = \"Win32_Group.Domain='" . $cs_computername . "',Name='Remote Desktop Users'\"");
foreach($wmisclass as $obj) {
$usersuser = '';
$usersgroup = '';
$usersuser = explode('\\', str_replace('"', '', preg_replace('/.*Domain=/', '', str_replace('",Name="', '\\', $obj->PartComponent))));
if (strpos($obj->PartComponent, 'UserAccount') == true) { $usersgroup = 'User Account'; }
if (strpos($obj->PartComponent, 'Group') == true) { $usersgroup = 'Group'; }
$usersarray[$userscounter][0] = $usersuser[1];
$usersarray[$userscounter][1] = $usersuser[0];
$usersarray[$userscounter][2] = $usersgroup;
$userscounter = $userscounter + 1;					
}
sort($usersarray);
foreach ($usersarray as $usersrow) {
	$xml = $xml . '		';
	$xml = $xml . '<User_Name' . $users_count . '>' . $usersrow[0] . '</User_Name' . $users_count . '>';
	$xml = $xml . '<User_Group' . $users_count . '>Remote Desktop Users</User_Group' . $users_count . '>';
	if ($usersrow[1] != '') { $xml = $xml . '<User_Domain' . $users_count . '>' . $usersrow[1] . '</User_Domain' . $users_count . '>'; }
	if ($usersrow[2] != '') { $xml = $xml . '<User_Type' . $users_count . '>' . $usersrow[2] . '</User_Type' . $users_count . '>'; }
	$xml = $xml . "\n";
	$users_count = $users_count + 1;
}
$usersarray = array();
$userscounter = 0;
$wmisclass = $wmi->ExecQuery("SELECT PartComponent FROM Win32_GroupUser WHERE GroupComponent = \"Win32_Group.Domain='" . $cs_computername . "',Name='Users'\"");
foreach($wmisclass as $obj) {
$usersuser = '';
$usersgroup = '';
$usersuser = explode('\\', str_replace('"', '', preg_replace('/.*Domain=/', '', str_replace('",Name="', '\\', $obj->PartComponent))));
if (strpos($obj->PartComponent, 'UserAccount') == true) { $usersgroup = 'User Account'; }
if (strpos($obj->PartComponent, 'Group') == true) { $usersgroup = 'Group'; }
$usersarray[$userscounter][0] = $usersuser[1];
$usersarray[$userscounter][1] = $usersuser[0];
$usersarray[$userscounter][2] = $usersgroup;
$userscounter = $userscounter + 1;					
}
sort($usersarray);
foreach ($usersarray as $usersrow) {
	$xml = $xml . '		';
	$xml = $xml . '<User_Name' . $users_count . '>' . $usersrow[0] . '</User_Name' . $users_count . '>';
	$xml = $xml . '<User_Group' . $users_count . '>Users</User_Group' . $users_count . '>';
	if ($usersrow[1] != '') { $xml = $xml . '<User_Domain' . $users_count . '>' . $usersrow[1] . '</User_Domain' . $users_count . '>'; }
	if ($usersrow[2] != '') { $xml = $xml . '<User_Type' . $users_count . '>' . $usersrow[2] . '</User_Type' . $users_count . '>'; }
	$xml = $xml . "\n";
	$users_count = $users_count + 1;
}
$xml = $xml . '	</Users>' . "\n";



$profiles_count = 0;
$xml = $xml . '	<Profiles>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT LocalPath, LastUseTime, SID FROM Win32_UserProfile");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->LocalPath != '') { $xml = $xml . '<Profile_Name' . $profiles_count . '>' . preg_replace('/.*\\\/', '', $obj->LocalPath) . '</Profile_Name' . $profiles_count . '>'; }
	if ($obj->LastUseTime != '') { $xml = $xml . '<Profile_LastUseTime' . $profiles_count . '>' . substr($obj->LastUseTime, 6, 2) . '/' . substr($obj->LastUseTime, 4, 2) . '/' . substr($obj->LastUseTime, 0, 4) . ' ' . substr($obj->LastUseTime, 8, 2) . ':' . substr($obj->LastUseTime, 10, 2) . ':' . substr($obj->LastUseTime, 12, 2) . '</Profile_LastUseTime' . $profiles_count . '>'; }
	if ($obj->SID != '') { $xml = $xml . '<Profile_SID' . $profiles_count . '>' . $obj->SID . '</Profile_SID' . $profiles_count . '>'; }
	$xml = $xml . "\n";
	$profiles_count = $profiles_count + 1;
}
$xml = $xml . '	</Profiles>' . "\n";



$shares_count = 0;
$xml = $xml . '	<Shares>' . "\n";
$wmisclass = $wmi->ExecQuery("SELECT Name, Description, Status, Path FROM Win32_Share");
foreach($wmisclass as $obj) {
	$xml = $xml . '		';
	if ($obj->Name != '') { $xml = $xml . '<Share_Name' . $shares_count . '>' . urlencode($obj->Name) . '</Share_Name' . $shares_count . '>'; }
	if ($obj->Description != '') { $xml = $xml . '<Share_Description' . $shares_count . '>' . urlencode($obj->Description) . '</Share_Description' . $shares_count . '>'; }
	if ($obj->Status != '') { $xml = $xml . '<Share_Status' . $shares_count . '>' . $obj->Status . '</Share_Status' . $shares_count . '>'; }
	if ($obj->Path != '') { $xml = $xml . '<Share_Path' . $shares_count . '>' . urlencode($obj->Path) . '</Share_Path' . $shares_count . '>'; }
	$xml = $xml . "\n";
	$shares_count = $shares_count + 1;
}
$xml = $xml . '	</Shares>' . "\n";

$inventory_status = 1;

$xml = $xml . '</inventory>';
$zp = gzopen($_SESSION['agentpath'] . '\\temp\\inventory.xml.gz', "w9");
gzwrite($zp, $xml);
gzclose($zp);

?>