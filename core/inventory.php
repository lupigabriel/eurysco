<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['systeminventory'] > 0) {  } else { header('location: /'); exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>System<small>inventory</small></h1>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-inventory-button big page-back"></a>
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
					<ul class="accordion span10" data-role="accordion">
						<li class="active">
							<a href="#" style="font-size:16px; color:#000;">Computer System:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_ComputerSystem");
									foreach($wmisclass as $obj) {
										if ($obj->Caption != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Caption . '</strong></td></tr>'; }
										if ($obj->Domain != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Domain</td><td width="70%" style="font-size:12px;">' . $obj->Domain . '</td></tr>'; }
										foreach($obj->Properties_ as $wmiprop) {
											if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|DNSHostName|')) > -1) {
												if ($obj->DNSHostName != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">DNS HostName</td><td width="70%" style="font-size:12px;">' . $obj->DNSHostName . '</td></tr>'; }
											}
										}
										if ($obj->Manufacturer != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Manufacturer</td><td width="70%" style="font-size:12px;">' . $obj->Manufacturer . '</td></tr>'; }
										if ($obj->Model != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Model</td><td width="70%" style="font-size:12px;">' . $obj->Model . '</td></tr>'; }
										if ($obj->Description != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Description</td><td width="70%" style="font-size:12px;">' . $obj->Description . '</td></tr>'; }
										if ($obj->SystemType != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">System Type</td><td width="70%" style="font-size:12px;">' . $obj->SystemType . '</td></tr>'; }
										if ($obj->WakeUpType != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">WakeUp Type</td><td width="70%" style="font-size:12px;">' . $obj->WakeUpType . '</td></tr>'; }
										if ($obj->PrimaryOwnerName != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Primary OwnerName</td><td width="70%" style="font-size:12px;">' . $obj->PrimaryOwnerName . '</td></tr>'; }
										if ($obj->NumberOfProcessors != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Number of Processors</td><td width="70%" style="font-size:12px;">' . $obj->NumberOfProcessors . '</td></tr>'; }
										foreach($obj->Properties_ as $wmiprop) {
											if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfLogicalProcessors|')) > -1) {
												if ($obj->NumberOfLogicalProcessors != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Number of Logical Processors</td><td width="70%" style="font-size:12px;">' . $obj->NumberOfLogicalProcessors . '</td></tr>'; }
											}
										}
										if ($obj->TotalPhysicalMemory != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Total Physical Memory</td><td width="70%" style="font-size:12px;">' . number_format(round($obj->TotalPhysicalMemory / 1024 / 1024, 0), 0, ',', '.') . '&nbsp;MB</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li class="active">
							<a href="#" style="font-size:16px; color:#000;">Operating System:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_OperatingSystem");
									foreach($wmisclass as $obj) {
										if ($obj->Caption != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Caption . '</strong></td></tr>'; }
										if ($obj->CSDVersion != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Service Pack</td><td width="70%" style="font-size:12px;">' . $obj->CSDVersion . '</td></tr>'; }
										if ($obj->Version != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Version</td><td width="70%" style="font-size:12px;">' . $obj->Version . '</td></tr>'; }
										if ($obj->Manufacturer != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Manufacturer</td><td width="70%" style="font-size:12px;">' . $obj->Manufacturer . '</td></tr>'; }
										foreach($obj->Properties_ as $wmiprop) {
											if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|OSArchitecture|')) > -1) {
												if ($obj->OSArchitecture != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">OS Architecture</td><td width="70%" style="font-size:12px;">' . str_replace('-', ' ', $obj->OSArchitecture) . '</td></tr>'; }
											}
										}
										if ($obj->OSLanguage != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">OS Language</td><td width="70%" style="font-size:12px;">' . str_replace('-', ' ', $obj->OSLanguage) . '</td></tr>'; }
										if ($obj->SerialNumber != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Serial Number</td><td width="70%" style="font-size:12px;">' . $obj->SerialNumber . '</td></tr>'; }
										if ($obj->CSName != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Computer Name</td><td width="70%" style="font-size:12px;">' . $obj->CSName . '</td></tr>'; }
										if ($obj->WindowsDirectory != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">System Directory</td><td width="70%" style="font-size:12px;">' . $obj->WindowsDirectory . '</td></tr>'; }
										if ($obj->Organization != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Organization</td><td width="70%" style="font-size:12px;">' . $obj->Organization . '</td></tr>'; }
										if ($obj->RegisteredUser != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Registered User</td><td width="70%" style="font-size:12px;">' . $obj->RegisteredUser . '</td></tr>'; }
										if ($obj->CurrentTimeZone != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Current Time Zone</td><td width="70%" style="font-size:12px;">' . $obj->CurrentTimeZone . '</td></tr>'; }
										if ($obj->InstallDate != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Install Date</td><td width="70%" style="font-size:12px;">' . substr($obj->InstallDate, 6, 2) . '/' . substr($obj->InstallDate, 4, 2) . '/' . substr($obj->InstallDate, 0, 4) . ' ' . substr($obj->InstallDate, 8, 2) . ':' . substr($obj->InstallDate, 10, 2) . ':' . substr($obj->InstallDate, 12, 2) . '</td></tr>'; }
										if ($obj->LastBootUpTime != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Last BootUp Time</td><td width="70%" style="font-size:12px;">' . substr($obj->LastBootUpTime, 6, 2) . '/' . substr($obj->LastBootUpTime, 4, 2) . '/' . substr($obj->LastBootUpTime, 0, 4) . ' ' . substr($obj->LastBootUpTime, 8, 2) . ':' . substr($obj->LastBootUpTime, 10, 2) . ':' . substr($obj->LastBootUpTime, 12, 2) . '</td></tr>'; }
										if ($obj->LocalDateTime != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Local Date Time</td><td width="70%" style="font-size:12px;">' . substr($obj->LocalDateTime, 6, 2) . '/' . substr($obj->LocalDateTime, 4, 2) . '/' . substr($obj->LocalDateTime, 0, 4) . ' ' . substr($obj->LocalDateTime, 8, 2) . ':' . substr($obj->LocalDateTime, 10, 2) . ':' . substr($obj->LocalDateTime, 12, 2) . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li class="active">
							<a href="#" style="font-size:16px; color:#000;">Bios:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT Manufacturer, Name, ReleaseDate, SerialNumber, SMBIOSBIOSVersion FROM Win32_BIOS");
									foreach($wmisclass as $obj) {
										if ($obj->Name != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Name . '</strong></td></tr>'; }
										if ($obj->Manufacturer != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Manufacturer</td><td width="70%" style="font-size:12px;">' . $obj->Manufacturer . '</td></tr>'; }
										if ($obj->SMBIOSBIOSVersion != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Version</td><td width="70%" style="font-size:12px;">' . $obj->SMBIOSBIOSVersion . '</td></tr>'; }
										if ($obj->ReleaseDate != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Release Date</td><td width="70%" style="font-size:12px;">' . substr($obj->ReleaseDate, 6, 2) . '/' . substr($obj->ReleaseDate, 4, 2) . '/' . substr($obj->ReleaseDate, 0, 4) . '</td></tr>'; }
										if ($obj->SerialNumber != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Serial Number</td><td width="70%" style="font-size:12px;">' . $obj->SerialNumber . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Processor:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_Processor");
									foreach($wmisclass as $obj) {
										if ($obj->Name != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Name . '</strong></td></tr>'; }
										if ($obj->DeviceID != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Device ID</td><td width="70%" style="font-size:12px;">' . $obj->DeviceID . '</td></tr>'; }
										if ($obj->Manufacturer != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Manufacturer</td><td width="70%" style="font-size:12px;">' . $obj->Manufacturer . '</td></tr>'; }
										if ($obj->Description != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Description</td><td width="70%" style="font-size:12px;">' . $obj->Description . '</td></tr>'; }
										if ($obj->SocketDesignation != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Socket Type</td><td width="70%" style="font-size:12px;">' . $obj->SocketDesignation . '</td></tr>'; }
										if ($obj->AddressWidth != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Architecture</td><td width="70%" style="font-size:12px;">' . $obj->AddressWidth . ' bit</td></tr>'; }
										if ($obj->MaxClockSpeed != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Max Clock Speed</td><td width="70%" style="font-size:12px;">' . number_format($obj->MaxClockSpeed, 0, ',', '.') . ' Mhz</td></tr>'; }
										if ($obj->ProcessorId != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Processor ID</td><td width="70%" style="font-size:12px;">' . $obj->ProcessorId . '</td></tr>'; }
										foreach($obj->Properties_ as $wmiprop) {
											if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfCores|')) > -1) {
												if ($obj->NumberOfCores != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Number of Cores</td><td width="70%" style="font-size:12px;">' . $obj->NumberOfCores . '</td></tr>'; }
											}
											if (strpos(strtolower('|' . $wmiprop->Name . '|'), strtolower('|NumberOfLogicalProcessors|')) > -1) {
												if ($obj->NumberOfLogicalProcessors != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Number of Logical Processors</td><td width="70%" style="font-size:12px;">' . $obj->NumberOfLogicalProcessors . '</td></tr>'; }
											}
										}
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Video Controller:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT DeviceID, DriverVersion, InfFilename, Name, VideoModeDescription, VideoProcessor FROM Win32_VideoController");
									foreach($wmisclass as $obj) {
										if ($obj->Name != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Name . '</strong></td></tr>'; }
										if ($obj->DeviceID != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Device ID</td><td width="70%" style="font-size:12px;">' . $obj->DeviceID . '</td></tr>'; }
										if ($obj->VideoProcessor != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Video Processor</td><td width="70%" style="font-size:12px;">' . $obj->VideoProcessor . '</td></tr>'; }
										if ($obj->DriverVersion != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Driver Version</td><td width="70%" style="font-size:12px;">' . $obj->DriverVersion . '</td></tr>'; }
										if ($obj->InfFilename != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Inf Filename</td><td width="70%" style="font-size:12px;">' . $obj->InfFilename . '</td></tr>'; }
										if ($obj->VideoModeDescription != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Video Mode Description</td><td width="70%" style="font-size:12px;">' . $obj->VideoModeDescription . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Motherboard:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT Manufacturer, Product FROM Win32_BaseBoard");
									foreach($wmisclass as $obj) {
										if ($obj->Manufacturer != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Manufacturer . '</strong></td></tr>'; }
										if ($obj->Product != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Model</td><td width="70%" style="font-size:12px;">' . $obj->Product . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Physical Memory:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT BankLabel, Capacity, DeviceLocator, Speed FROM Win32_PhysicalMemory");
									foreach($wmisclass as $obj) {
										if ($obj->DeviceLocator != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->DeviceLocator . '</strong></td></tr>'; }
										if ($obj->BankLabel != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Bank Label</td><td width="70%" style="font-size:12px;">' . $obj->BankLabel . '</td></tr>'; }
										if ($obj->Capacity != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Capacity</td><td width="70%" style="font-size:12px;">' . number_format(round($obj->Capacity / 1024 / 1024, 0), 0, ',', '.') . '&nbsp;MB</td></tr>'; }
										if ($obj->Speed != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Speed</td><td width="70%" style="font-size:12px;">' . $obj->Speed . ' Mhz</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Logical Disk:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT Description, FileSystem, FreeSpace, Name, Size FROM Win32_LogicalDisk");
									foreach($wmisclass as $obj) {
										if ($obj->Name != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Name . '</strong></td></tr>'; }
										if ($obj->Description != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Type</td><td width="70%" style="font-size:12px;">' . $obj->Description . '</td></tr>'; }
										if ($obj->FileSystem != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">File System</td><td width="70%" style="font-size:12px;">' . $obj->FileSystem . '</td></tr>'; }
										if ($obj->Size != '') {
											$sizebyte = $obj->Size;
											$sizeview = $sizebyte . '&nbsp;B';
											if ($sizebyte > 1023) { if (number_format(($sizebyte / 1024), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1024), 2, ',', '.') . '&nbsp;KB'; } else { $sizeview = number_format(($sizebyte / 1024), 0, ',', '.') . '&nbsp;KB'; } }
											if ($sizebyte > 1048575) { if (number_format(($sizebyte / 1048576), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1048576), 2, ',', '.') . '&nbsp;MB'; } else { $sizeview = number_format(($sizebyte / 1048576), 0, ',', '.') . '&nbsp;MB'; } }
											if ($sizebyte > 1073741823) { if (number_format(($sizebyte / 1073741824), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1073741824), 2, ',', '.') . '&nbsp;GB'; } else { $sizeview = number_format(($sizebyte / 1073741824), 0, ',', '.') . '&nbsp;GB'; } }
											if ($sizebyte > 1099511627775) { if (number_format(($sizebyte / 1099511627776), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1099511627776), 2, ',', '.') . '&nbsp;TB'; } else { $sizeview = number_format(($sizebyte / 1099511627776), 0, ',', '.') . '&nbsp;TB'; } }
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Size</td><td width="70%" style="font-size:12px;">' . $sizeview . '</td></tr>';
										}
										if ($obj->FreeSpace != '') {
											$sizebyte = $obj->FreeSpace;
											$sizeview = $sizebyte . '&nbsp;B';
											if ($sizebyte > 1023) { if (number_format(($sizebyte / 1024), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1024), 2, ',', '.') . '&nbsp;KB'; } else { $sizeview = number_format(($sizebyte / 1024), 0, ',', '.') . '&nbsp;KB'; } }
											if ($sizebyte > 1048575) { if (number_format(($sizebyte / 1048576), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1048576), 2, ',', '.') . '&nbsp;MB'; } else { $sizeview = number_format(($sizebyte / 1048576), 0, ',', '.') . '&nbsp;MB'; } }
											if ($sizebyte > 1073741823) { if (number_format(($sizebyte / 1073741824), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1073741824), 2, ',', '.') . '&nbsp;GB'; } else { $sizeview = number_format(($sizebyte / 1073741824), 0, ',', '.') . '&nbsp;GB'; } }
											if ($sizebyte > 1099511627775) { if (number_format(($sizebyte / 1099511627776), 0, ',', '.') < 10) { $sizeview = number_format(($sizebyte / 1099511627776), 2, ',', '.') . '&nbsp;TB'; } else { $sizeview = number_format(($sizebyte / 1099511627776), 0, ',', '.') . '&nbsp;TB'; } }
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Free Space</td><td width="70%" style="font-size:12px;">' . $sizeview . '</td></tr>';
										}
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Network Configuration:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT DefaultIPGateway, Description, DNSServerSearchOrder, IPAddress, IPSubnet, MACAddress FROM Win32_NetworkAdapterConfiguration");
									foreach($wmisclass as $obj) {
										if ($obj->MACAddress != '' && !is_null($obj->IPAddress)) {
											$IPAddress = '';
											$IPAddresses = $obj->IPAddress;
											foreach ($IPAddresses as $key=>$value) {
												if ($IPAddress == '') { $IPAddress = $value; } else { $IPAddress = $IPAddress . '&nbsp;&nbsp;(&nbsp;' . $value . '&nbsp;)'; }
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
													if ($DNSServerSearchOrder == '') { $DNSServerSearchOrder = $value; } else { $DNSServerSearchOrder = $DNSServerSearchOrder . '&nbsp;&nbsp;/&nbsp;&nbsp;' . $value; }
												}
											} else {
												$DNSServerSearchOrder = '-';
											}
											$DefaultIPGateway = '';
											if (!is_null($obj->DefaultIPGateway)) {
												$DefaultIPGateways = $obj->DefaultIPGateway;
												foreach ($DefaultIPGateways as $key=>$value) {
													if ($DefaultIPGateway == '') { $DefaultIPGateway = $value; } else { $DefaultIPGateway = $DefaultIPGateway . '&nbsp;&nbsp;/&nbsp;&nbsp;' . $value; }
												}
											} else {
												$DefaultIPGateway = '-';
											}
											if ($obj->Description != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Description . '</strong></td></tr>'; }
											if ($obj->MACAddress != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">MAC Address</td><td width="70%" style="font-size:12px;">' . $obj->MACAddress . '</td></tr>'; }
											if ($obj->IPAddress != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">IP Address</td><td width="70%" style="font-size:12px;">' . $IPAddress . '</td></tr>'; }
											if ($obj->IPSubnet != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">IP Subnet</td><td width="70%" style="font-size:12px;">' . $IPSubnet . '</td></tr>'; }
											if ($obj->DefaultIPGateway != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">IP Gateway</td><td width="70%" style="font-size:12px;">' . $DefaultIPGateway . '</td></tr>'; }
											if ($obj->DNSServerSearchOrder != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">DNS Server</td><td width="70%" style="font-size:12px;">' . $DNSServerSearchOrder . '</td></tr>'; }
										}
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Drivers:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT DeviceName, FriendlyName, DriverProviderName, DriverVersion, DeviceClass FROM Win32_PnPSignedDriver");
									foreach($wmisclass as $obj) {
										if ($obj->DeviceName != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Device Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->DeviceName . '</strong></td></tr>'; }
										if ($obj->DriverVersion != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Driver Version</td><td width="70%" style="font-size:12px;">' . $obj->DriverVersion . '</td></tr>'; }
										if ($obj->FriendlyName != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Friendly Name</td><td width="70%" style="font-size:12px;">' . $obj->FriendlyName . '</td></tr>'; }
										if ($obj->DriverProviderName != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Provider Name</td><td width="70%" style="font-size:12px;">' . $obj->DriverProviderName . '</td></tr>'; }
										if ($obj->DeviceClass != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Device Class</td><td width="70%" style="font-size:12px;">' . $obj->DeviceClass . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Printers:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT DriverName, Name, PortName FROM Win32_Printer");
									foreach($wmisclass as $obj) {
										if ($obj->Name != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Name . '</strong></td></tr>'; }
										if ($obj->DriverName != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Driver Name</td><td width="70%" style="font-size:12px;">' . $obj->DriverName . '</td></tr>'; }
										if ($obj->PortName != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Port Name</td><td width="70%" style="font-size:12px;">' . $obj->PortName . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Users:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
										$usersarray = array();
										$userscounter = 0;
										$wmisclass = $wmi->ExecQuery("SELECT PartComponent FROM Win32_GroupUser WHERE GroupComponent = \"Win32_Group.Domain='" . $envcomputername . "',Name='Administrators'\"");
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
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $usersrow[0] . '</strong></td></tr>';
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Group</td><td width="70%" style="font-size:12px;">Administrators</td></tr>';
											if ($usersrow[1] != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Domain</td><td width="70%" style="font-size:12px;">' . $usersrow[1] . '</td></tr>'; }
											if ($usersrow[2] != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Type</td><td width="70%" style="font-size:12px;">' . $usersrow[2] . '</td></tr>'; }
										}

										$usersarray = array();
										$userscounter = 0;
										$wmisclass = $wmi->ExecQuery("SELECT PartComponent FROM Win32_GroupUser WHERE GroupComponent = \"Win32_Group.Domain='" . $envcomputername . "',Name='Power Users'\"");
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
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $usersrow[0] . '</strong></td></tr>';
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Group</td><td width="70%" style="font-size:12px;">Power Users</td></tr>';
											if ($usersrow[1] != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Domain</td><td width="70%" style="font-size:12px;">' . $usersrow[1] . '</td></tr>'; }
											if ($usersrow[2] != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Type</td><td width="70%" style="font-size:12px;">' . $usersrow[2] . '</td></tr>'; }
										}

										$usersarray = array();
										$userscounter = 0;
										$wmisclass = $wmi->ExecQuery("SELECT PartComponent FROM Win32_GroupUser WHERE GroupComponent = \"Win32_Group.Domain='" . $envcomputername . "',Name='Remote Desktop Users'\"");
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
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $usersrow[0] . '</strong></td></tr>';
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Group</td><td width="70%" style="font-size:12px;">Remote Desktop Users</td></tr>';
											if ($usersrow[1] != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Domain</td><td width="70%" style="font-size:12px;">' . $usersrow[1] . '</td></tr>'; }
											if ($usersrow[2] != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Type</td><td width="70%" style="font-size:12px;">' . $usersrow[2] . '</td></tr>'; }
										}

										$usersarray = array();
										$userscounter = 0;
										$wmisclass = $wmi->ExecQuery("SELECT PartComponent FROM Win32_GroupUser WHERE GroupComponent = \"Win32_Group.Domain='" . $envcomputername . "',Name='Users'\"");
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
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $usersrow[0] . '</strong></td></tr>';
											echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Group</td><td width="70%" style="font-size:12px;">Users</td></tr>';
											if ($usersrow[1] != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Domain</td><td width="70%" style="font-size:12px;">' . $usersrow[1] . '</td></tr>'; }
											if ($usersrow[2] != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Type</td><td width="70%" style="font-size:12px;">' . $usersrow[2] . '</td></tr>'; }
										}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Profiles:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT LocalPath, LastUseTime, SID FROM Win32_UserProfile");
									foreach($wmisclass as $obj) {
										if ($obj->LocalPath != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . preg_replace('/.*\\\/', '', $obj->LocalPath) . '</strong></td></tr>'; }
										if ($obj->LastUseTime != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Last Use</td><td width="70%" style="font-size:12px;">' . substr($obj->LastUseTime, 6, 2) . '/' . substr($obj->LastUseTime, 4, 2) . '/' . substr($obj->LastUseTime, 0, 4) . ' ' . substr($obj->LastUseTime, 8, 2) . ':' . substr($obj->LastUseTime, 10, 2) . ':' . substr($obj->LastUseTime, 12, 2) . '</td></tr>'; }
										if ($obj->SID != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">SID</td><td width="70%" style="font-size:12px;">' . $obj->SID . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
		            <div class="span1"></div>
					<ul class="accordion span10" data-role="accordion">
						<li>
							<a href="#" style="font-size:16px; color:#000;">Shares:</a>
							<div>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
									<?php
									$wmisclass = $wmi->ExecQuery("SELECT Name, Description, Status, Path FROM Win32_Share");
									foreach($wmisclass as $obj) {
										if ($obj->Name != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;"><strong>Name</strong></td><td width="70%" style="font-size:12px;"><strong>' . $obj->Name . '</strong></td></tr>'; }
										if ($obj->Description != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Description</td><td width="70%" style="font-size:12px;">' . $obj->Description . '</td></tr>'; }
										if ($obj->Status != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Status</td><td width="70%" style="font-size:12px;">' . $obj->Status . '</td></tr>'; }
										if ($obj->Path != '') { echo '<tr class="rowselect"><td width="30%" style="font-size:12px;">Path</td><td width="70%" style="font-size:12px;">' . $obj->Path . '</td></tr>'; }
									}
									?>
								</table>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>