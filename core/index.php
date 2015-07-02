<?php include("header.php"); ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<div class="page secondary">
	<div class="page-header">
		<div class="page-header-content">
			<h1>System<small>info</small></h1>
			<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-info-button big page-back"></a>
		</div>
	</div>
</div>

<br />

<div class="page" id="page-index">
	<div class="page-region">
		<div class="page-region-content">

			<?php $span = '5'; ?>

			<?php

			$cs_computername = '-';
			$cs_domain = '-';
			$cs_manufacturer = '-';
			$cs_model = '-';
			$wmisclass = $wmi->ExecQuery("SELECT Domain, Manufacturer, Model, Name FROM Win32_ComputerSystem");
			foreach($wmisclass as $obj) {
				$cs_computername = $obj->Name;
				$cs_domain = $obj->Domain;
				$cs_manufacturer = $obj->Manufacturer;
				$cs_model = $obj->Model;
			}

			?>

			<div class="grid">
				<div class="row">
		            <div class="span1"></div>
		            <div class="span10">

                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">
                    	<tr><td width="20%"><div style="white-space:nowrap; table-layout:fixed; overflow:hidden;" title="eurysco Address"><div class="icon-chrome" style="font-size:16px; color:#444;"></div>&nbsp;&nbsp;<div class="icon-firefox" style="font-size:16px; color:#444;"></div>&nbsp;&nbsp;<div class="icon-IE" style="font-size:16px; color:#444;"></div>&nbsp;&nbsp;<div class="icon-opera" style="font-size:16px; color:#444;"></div>&nbsp;&nbsp;<div class="icon-safari" style="font-size:16px; color:#444;"></div></div></td><td width="80%" style="font-size:12px;"><?php echo strtolower('https://' . $cs_computername . ':' . $eurysco_coreport); ?></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Page Loading Time:</div></td><td width="80%"><div id="totaltime" style="font-size:12px;"></div></td></tr>
                    	<tr><td width="20%"><div style="font-size:12px; white-space:nowrap; table-layout:fixed; overflow:hidden;">Reloading Time:</div></td><td width="80%" style="font-size:12px;"><?php if ($systeminforrsetting != 'Hold') { echo number_format(($systeminforrsetting / 1000), 0, ',', '.') . '&nbsp;sec&nbsp;&nbsp;'; } else { echo $systeminforrsetting . '&nbsp;&nbsp;'; } ?><a href="/" title="Reload Now"><div class="icon-loop"></div></a></td></tr>
                    </table>
                    <br />

                    <div id="systeminfo"></div>

					<script language="javascript" type="text/javascript">
					update();
					<?php if ($systeminforrsetting != 'Hold') { echo 'setInterval(update, ' . $systeminforrsetting . ');'; $phptimeout = $systeminforrsetting / 1000; } else { $phptimeout = 120; } ?>
					function update() {
						$.ajax({
							type: "GET",
							url: 'systemjq.php?domain=<?php echo urlencode($cs_domain); ?>&manufacturer=<?php echo urlencode($cs_manufacturer); ?>&model=<?php echo urlencode($cs_model); ?>&phptimeout=<?php echo $phptimeout; ?>',
							data: '',
							dataType: 'json',
							cache: false,
							timeout: <?php if ($systeminforrsetting != 'Hold') { echo $systeminforrsetting; } else { echo '120000'; } ?>,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#systeminfo').html(data.systeminfo);
							$('#totaltime').html(data.totaltime);
							}
						});
					}

					</script>

					<?php
					
					$cleartempdir = scandir($_SERVER['DOCUMENT_ROOT'] . '\\temp');
					foreach ($cleartempdir as $cleartemp) {
						if ($cleartemp != '.' && $cleartemp != '..') {
							if ((strtotime(date('Y-m-d H:i:s')) - strtotime(date('Y-m-d H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $cleartemp)))) > 86400) {
								@unlink($_SERVER['DOCUMENT_ROOT'] . '\\temp\\' . $cleartemp);
							}
						}
					}
					
					$systemi = '';
					$adminarray = array();
					$admincounter = 0;
					$wmisclass = $wmi->ExecQuery("SELECT PartComponent FROM Win32_GroupUser WHERE GroupComponent = \"Win32_Group.Domain='" . $cs_computername . "',Name='Administrators'\"");
					foreach($wmisclass as $obj) {
					$adminuser = '';
					$admingroup = '';

					$adminuser = explode('\\', str_replace('"', '', preg_replace('/.*Domain=/', '', str_replace('",Name="', '\\', $obj->PartComponent))));
					if (strpos($obj->PartComponent, 'UserAccount') == true) { $admingroup = 'User Account'; }
					if (strpos($obj->PartComponent, 'Group') == true) { $admingroup = 'Group'; }
					
					$adminarray[$admincounter][0] = $adminuser[1];
					$adminarray[$admincounter][1] = $adminuser[0];
					$adminarray[$admincounter][2] = $admingroup;
					
					$admincounter = $admincounter + 1;					
					}

					sort($adminarray);

					$systemi = $systemi . '<br /><h2><img src="img/adm.png" width="32" height="32" />&nbsp;Administrators:</h2>';
					$systemi = $systemi . '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="striped">';
					$systemi = $systemi . '<tr><td width="30%" style="font-size:12px;"><strong>Name:</strong></td><td width="30%" style="font-size:12px;"><strong>Domain:</strong></td><td width="40%" style="font-size:12px;"><strong>Type:</strong></td></tr>';
					foreach ($adminarray as $adminrow) {
						$systemi = $systemi . '<tr class="rowselect"><td width="30%" style="font-size:12px;">' . $adminrow[0] . '</td><td width="30%" style="font-size:12px;">' . $adminrow[1] . '</td><td width="40%" style="font-size:12px;">' . $adminrow[2] . '</td></tr>';
					}
					$systemi = $systemi . '</table>';

					echo $systemi;

					?>

					</div>
				</div>
			</div>

			<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systeminfo'] > 2) { ?>
			<div class="grid">
				<div class="row">
		            <div class="span1"></div>
					<?php include('/include/core_status_' . $corestatus . '.php'); ?>
					<?php include('/include/core_status_' . $corestatus . '_des.php'); ?>
				</div>
			</div>
            <?php } ?>

			<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['systeminfo'] > 1) { ?>
			<div class="grid">
				<div class="row">
		            <div class="span1"></div>
					<?php include('/include/executor_status_' . $executorstatus . '.php'); ?>
					<?php include('/include/executor_status_' . $executorstatus . '_des.php'); ?>
				</div>
			</div>
            <?php } ?>

            <?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systeminfo'] > 2) { ?>
	            <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\' . $config_server)) { ?>
				<div class="grid">
					<div class="row">
			            <div class="span1"></div>
						<?php include('/include/server_status_' . $serverstatus . '.php'); ?>
						<?php include('/include/server_status_' . $serverstatus . '_des.php'); ?>
					</div>
				</div>
	            <?php } ?>
            <?php } ?>

			<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['systeminfo'] > 1) { ?>
	            <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\' . $config_agentsrv)) { ?>
				<div class="grid">
					<div class="row">
			            <div class="span1"></div>
						<?php include('/include/agent_status_' . $agentstatus . '.php'); ?>
						<?php include('/include/agent_status_' . $agentstatus . '_des.php'); ?>
					</div>
				</div>
	            <?php } ?>
            <?php } ?>

		</div>
	</div>
</div>

<?php } ?>

<?php include("footer.php"); ?>