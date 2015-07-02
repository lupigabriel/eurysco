<?php include("header.php"); ?>

<?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemshutdown'] > 0) {  } else { header('location: /'); exit; } ?>

<?php include("navigation.php"); ?>

<?php if ($corelinkst != 1 && $executorlinkst == 1) { header('location: ' . $executorlink . '/core.php'); } ?>
<?php if ($corelinkst == 1 && $executorlinkst != 1) { header('location: ' . $corelink . '/executor.php'); } ?>

<?php if ($passwordexpired == 0) { ?>

<script type="text/javascript">
	function toolscommand(Form,Title,Description){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-switch" style="position:inherit;"></div>&nbsp; <strong>' + Title + '</strong></span>',
			'content'     : '<span style="font-size:12px;">' + Description + '</span>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '155px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 132) + 'px'
			},
			'buttons'     : {
				'Confirm'     : {
				'action': function(){
						document.getElementById(Form).submit();
					}
				},
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
			<h1>System<small>Shutdown</small></h1>
			<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="eurysco-shutdown-button big page-back"></a>
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
                    
                    <?php
					
					$shutdown_xml = 'temp\\shutdown.xml';
					
					if (file_exists($shutdown_xml)) {
						$xmlcreate = date("Y-m-d H:i:s", filemtime($shutdown_xml));
						$wmisclass = $wmi->ExecQuery("SELECT * FROM Win32_OperatingSystem");
						$lastboot = '';
						foreach($wmisclass as $obj) {
							$lastboot = substr($obj->LastBootUpTime, 0, 4) . '-' . substr($obj->LastBootUpTime, 4, 2) . '-' . substr($obj->LastBootUpTime, 6, 2) . ' ' . substr($obj->LastBootUpTime, 8, 2) . ':' . substr($obj->LastBootUpTime, 10, 2) . ':' . substr($obj->LastBootUpTime, 12, 2);
							if (strtotime($xmlcreate) < strtotime($lastboot)) {
								unlink($shutdown_xml);
							}
						}
					}
					if (file_exists($shutdown_xml)) {
						$xmlshutdown = simplexml_load_file($shutdown_xml);
						$xml_shutdowndatetime = $xmlshutdown->settings->shutdowntimenorm;
						$currentdatetime = date('Y-m-d H:i:s');
						$shutdowntimediff = strtotime($xml_shutdowndatetime) - strtotime($currentdatetime);
						if ($shutdowntimediff < 0) {
							unlink($shutdown_xml);
						}
					}
					
					if (isset($_POST['shutdowncommand'])) {
						$shutdowncommand = $_POST['shutdowncommand'];
					} else {
						$shutdowncommand = '';
					}

					if (!file_exists($shutdown_xml) && isset($_POST['shutdowntype']) && isset($_POST['shutdowndate']) && isset($_POST['shutdowntime']) && $shutdowncommand == 'Launch' && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'])) > 0) {
						$shutdowndatetime = date('Y-m-d', strtotime($_POST['shutdowndate'])) . ' ' . $_POST['shutdowntime'];
						$currentdatetime = date('Y-m-d H:i:s');
						$shutdowntimediff = strtotime($shutdowndatetime) - strtotime($currentdatetime);
						if ($_POST['shutdowntype'] == 'Shutdown') { $shutdowntypecommand = 's'; } else { $shutdowntypecommand = 'r'; }
						if ($shutdowntimediff < 30) { $shutdowntimecommand = 30; $shutdownmessage = ' in ' . $shutdowntimecommand . ' seconds'; } else { $shutdowntimecommand = $shutdowntimediff; $shutdownmessage = ' to ' . date('r', strtotime($_POST['shutdowndate'] . ' ' . $_POST['shutdowntime'])); }
						session_write_close();
						exec('shutdown.exe -a & shutdown.exe -' . $shutdowntypecommand . ' -f -t ' . $shutdowntimecommand . ' -c "eurysco: ' . $_SESSION['username'] . ' planned System ' . $_POST['shutdowntype'] . $shutdownmessage . '"', $errorarray, $errorlevel);
						session_start();
						if ($errorlevel == 0) {
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system shutdown     system will "' . strtolower($_POST['shutdowntype']) . '" on "' . $shutdowndatetime . '"';
						} else {
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system shutdown     system will not "' . strtolower($_POST['shutdowntype']) . '" on "' . $shutdowndatetime . '"';
						}
						if ($shutdowntimediff < 30) { $xmlshutdownnewtime = date('H:i:s', time() + 30); $shutdowndatetime = date('Y-m-d', strtotime($_POST['shutdowndate'])) . ' ' . $xmlshutdownnewtime; } else { $xmlshutdownnewtime = $_POST['shutdowntime']; }
						$xml = '<config>' . "\n" . '	<settings>' . "\n" . '		<shutdowntype>' . $_POST['shutdowntype'] . '</shutdowntype>' . "\n" . '		<shutdowndate>' . $_POST['shutdowndate'] . '</shutdowndate>' . "\n" . '		<shutdowntime>' . $xmlshutdownnewtime . '</shutdowntime>' . "\n" . '		<shutdowntimenorm>' . $shutdowndatetime . '</shutdowntimenorm>' . "\n" . '	</settings>' . "\n" . '</config>';
						$sxe = new SimpleXMLElement($xml);
						if ($errorlevel == 0) { $sxe->asXML($shutdown_xml); }
						include('/auditlog.php');
						header('location: /shutdown.php');
						exit;
					}

					if (file_exists($shutdown_xml) && $shutdowncommand == 'Abort') {
						unlink($shutdown_xml);
						session_write_close();
						exec('shutdown.exe -a', $errorarray, $errorlevel);
						session_start();
						if ($errorlevel == 0) {
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system shutdown     system shutdown aborted';
						} else {
							$audit = date('r') . '     ' . $_SESSION['username'] . '     ' . $envcomputername . '     system shutdown     system shutdown not aborted';
						}							
					}

					$xml_shutdowntype = '';
					$xml_shutdowndate = '';
					$xml_shutdowntime = '';
					if (file_exists($shutdown_xml)) {
						$xmlshutdown = simplexml_load_file($shutdown_xml);
						$xml_shutdowntype = $xmlshutdown->settings->shutdowntype;
						$xml_shutdowndate = $xmlshutdown->settings->shutdowndate;
						$xml_shutdowntime = $xmlshutdown->settings->shutdowntime;
					}

					$disableform = '';
					$buttoncolor = 'bg-color-red';
					$buttontext = 'Shutdown';
					$datepicker = 'datepicker';
					$popupdescr = 'Launch';
					if (file_exists($shutdown_xml)) {
						$disableform = 'disabled';
						$buttoncolor = 'bg-color-green';
						$buttontext = 'Cancel';
						$datepicker = '';
						$popupdescr = 'Abort';
					}
					
					?>

					<h2><img src="img/restart.png" width="32" height="32" />&nbsp;Shutdown the system:</h2>
                    <div id="shutdownstatus"></div>
                    <br />
					<?php
					session_write_close();
					$checkshutdownt = shell_exec('shutdown.exe /? | find /i " 0-"');
					session_start();
					if ($checkshutdownt != '') { echo '<blockquote style="font-size:12px;">' . trim($checkshutdownt) . '</blockquote><br />'; }
					?>
                    <form id="shutdown" name="shutdown" method="post">
					<h3>Type:</h3>
					<div class="input-control select">
						<select id="shutdowntype" name="shutdowntype" <?php echo $disableform; ?>>
							<option value="Restart" <?php if ($xml_shutdowntype == 'Restart') { echo 'selected'; } ?>>Restart</option>
							<option value="Shutdown" <?php if ($xml_shutdowntype == 'Shutdown') { echo 'selected'; } ?>>Shutdown</option>
						</select>
					</div>
					<h3>Date and Time:</h3>
					<div class="input-control text <?php echo $datepicker; ?>" data-role="<?php echo $datepicker; ?>">
						<input type="text" id="shutdowndate" name="shutdowndate" value="<?php echo $xml_shutdowndate; ?>" <?php echo $disableform; ?> />
					</div>
                    <br />
					<div class="input-control text">
						<input type="text" id="shutdowntime" name="shutdowntime" autocomplete="off" value="<?php if ($xml_shutdowntime == '') { echo date('H:i:s'); } else { echo $xml_shutdowntime; } ?>" <?php echo $disableform; ?> />
						<script type="text/javascript">
						$(function () {
							$('#shutdowntime').timeEntry();
						});
						</script>
					</div>
                    <input type="hidden" id="shutdowncommand" name="shutdowncommand" value="<?php echo $popupdescr; ?>" />
                    <br />
                    </form>
                   	<a href="javascript:toolscommand('shutdown','<?php echo $popupdescr; ?> System Shutdown','<?php echo $popupdescr; ?> the following Shutdown:<br /><br /><table width=\'100%\' border=\'0\' cellspacing=\'0\' cellpadding=\'0\' class=\'striped\'><tr><td style=\'font-size:12px;\'>Type: </td><td style=\'font-size:12px;\'>' + shutdown.shutdowntype.value + '</td></tr><tr><td style=\'font-size:12px;\'>Date: </td><td style=\'font-size:12px;\'>' + shutdown.shutdowndate.value + '</td></tr><tr><td style=\'font-size:12px;\'>Time: </td><td style=\'font-size:12px;\'>' + shutdown.shutdowntime.value + '</td></tr></table>');"><button class="<?php echo $buttoncolor; ?>" style="color:#FFFFFF;" /><?php echo $buttontext; ?></button></a>

					<script language="javascript" type="text/javascript">
					update();
					setInterval(update, 1000);
					function update() {
						$.ajax({
							type: "GET",
							url: 'shutdownjq.php',
							data: '',
							dataType: 'json',
							cache: false,
							contentType: "application/json; charset=utf-8",
							success: function (data) {
							$('#shutdownstatus').html(data.shutdownstatus);
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

<form id="toolsformcommand" name="toolsformcommand" method="post">
	<input type="hidden" id="toolssendcommand" name="toolssendcommand">
</form>

<?php } ?>

<?php include("footer.php"); ?>