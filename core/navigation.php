<?php $navselected = ' style="background-color:#F8F8F8;"'; ?>

<script type="text/javascript">
	function confirmlogout(){
		$.Dialog({
			'title'       : '<span style="font-size:16px;">&nbsp;<div class="icon-exit" style="position:inherit;"></div>&nbsp; Logout <strong>Confirmation</strong></span>',
			'content'     : '<span style="font-size:12px;">Please Confirm Logout from eurysco</span>',
			'draggable'   : true,
			'overlay'     : true,
			'closeButton' : false,
			'buttonsAlign': 'center',
			'keepOpened'  : true,
			'position'    : {
				'offsetY' : '100px',
				'offsetX' : ((document.documentElement.offsetWidth / 2) - 124) + 'px'
			},
			'buttons'     : {
				'Logout'     : {
				'action': function(){
						window.location = "?logout";
					}
				},
				'Cancel'     : {
				'action': function(){}
				},
			}
		});
	};
</script>

<?php

if (isset($_POST['stopservice']) || isset($_POST['deleteconfiguration'])) {
	include('/include/unset.php');
}

?>

<div class="page">
<div class="nav-bar"<?php if ($serverstatus == 'run') { echo ' style="background-color:#8063C8"'; } else { echo ' style="background-color:#637CC8"'; } ?>>
	<div class="nav-bar-inner padding10">
		<span class="pull-menu"></span>
			<a href="/about.php" title="About eurysco <?= include("version.phtml")?>"><img class="place-left" src="/img/eurysco-logo.png" style="height:30px"/></a>

		<div class="divider"></div>

		<ul class="menu">
			<li data-role="dropdown"><a href="#">eurysco <small><?= include("version.phtml")?></small>&nbsp;</a>
				<ul class="dropdown-menu">
                    <?php if ($executorlinkst == 1 || ($corelinkst != 1 && $executorlinkst != 1)) { ?>
                    <li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/core.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['coreconfig'] > 0) { ?><a href="<?php echo $executorlink; ?>/core.php"><div class="icon-target"></div>Core Config</a><?php } else { ?><a style="color:#999999;"><div class="icon-target" style="color:#999999;"></div>Core Config</a><?php } ?></li>
					<?php } ?>
                    <?php if ($corelinkst == 1) { ?>
                    <li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/executor.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['executorconfig'] > 0) { ?><a href="<?php echo $corelink; ?>/executor.php"><div class="icon-radio-checked"></div>Executor Config</a><?php } else { ?><a style="color:#999999;"><div class="icon-radio-checked" style="color:#999999;"></div>Executor Config</a><?php } ?></li>
                    <?php } ?>
                    <?php if ($corelinkst == 1) { ?>
                    <li class="divider"></li>
                    <?php if ($eurysco_serverconaddress == '' || $eurysco_serverconaddress == 'https://' . strtoupper($envcomputername)) { ?>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/server.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['serverconfig'] > 0) { ?><a href="<?php echo $corelink; ?>/server.php"><div class="icon-tree-view"></div>Server Config</a><?php } else { ?><a style="color:#999999;"><div class="icon-tree-view" style="color:#999999;"></div>Server Config</a><?php } ?></li>
					<?php if ($serverstatus != 'cfg') { ?><li><?php if ($_SESSION['username'] == 'Administrator') { ?><a href="<?php echo $corelink; ?>/phpliteadmin.php" target="_blank"><div class="icon-database"></div>Server Database</a><?php } else { ?><a style="color:#999999;"><div class="icon-database" style="color:#999999;"></div>SQLite Database</a><?php } ?></li><?php } ?>
					<li class="divider"></li>
					<?php } ?>
                    <li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/agent.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usersett']['agentconfig'] > 0) { ?><a href="<?php echo $corelink; ?>/agent.php"><div class="icon-arrow-up-2"></div>Agent Config</a><?php } else { ?><a style="color:#999999;"><div class="icon-arrow-up-2" style="color:#999999;"></div>Agent Config</a><?php } ?></li>
                    <?php } ?>
				</ul>
			</li>
            <?php if ($serverstatus == 'run') { ?>
			<?php $compnamelen = 8; ?>
			<?php $usernamelen = 8; ?>
            <?php } else { ?>
			<?php $compnamelen = 12; ?>
			<?php $usernamelen = 12; ?>
            <?php } ?>
            <?php if ($corelinkst == 1 && $executorlinkst == 1) { ?>
            <?php if ($serverstatus == 'run') { ?>
			<li data-role="dropdown"><a href="#"><div class="icon-share-2"></div>nodes&nbsp;</a>
				<ul class="dropdown-menu">
                    <li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/nodes.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nodesstatus'] > 0) { ?><a href="<?php echo $corelink; if (!isset($_SESSION['nodes'])) { echo '/nodes.php'; } else { echo $_SESSION['nodes']; } ?>"><div class="icon-feed"></div>Nodes Status</a><?php } else { ?><a style="color:#999999;"><div class="icon-feed" style="color:#999999;"></div>Nodes Status</a><?php } ?></li>
					<?php if (isset($_SESSION['nodes_nagios']) || isset($_SESSION['nodes_netstat']) || isset($_SESSION['nodes_eventviewer']) || isset($_SESSION['nodes_inventory']) || isset($_SESSION['nodes_processes']) || isset($_SESSION['nodes_programs']) || isset($_SESSION['nodes_scheduler']) || isset($_SESSION['nodes_services'])) { ?>
					<li class="divider"></li>
					<?php if (isset($_SESSION['nodes_nagios'])) { echo '<li>' . $_SESSION['nodes_nagios'] . '</li>'; } ?>
					<?php if (isset($_SESSION['nodes_netstat'])) { echo '<li>' . $_SESSION['nodes_netstat'] . '</li>'; } ?>
					<?php if (isset($_SESSION['nodes_inventory'])) { echo '<li>' . $_SESSION['nodes_inventory'] . '</li>'; } ?>
					<?php if (isset($_SESSION['nodes_programs'])) { echo '<li>' . $_SESSION['nodes_programs'] . '</li>'; } ?>
					<?php if (isset($_SESSION['nodes_processes'])) { echo '<li>' . $_SESSION['nodes_processes'] . '</li>'; } ?>
					<?php if (isset($_SESSION['nodes_services'])) { echo '<li>' . $_SESSION['nodes_services'] . '</li>'; } ?>
					<?php if (isset($_SESSION['nodes_scheduler'])) { echo '<li>' . $_SESSION['nodes_scheduler'] . '</li>'; } ?>
					<?php if (isset($_SESSION['nodes_eventviewer'])) { echo '<li>' . $_SESSION['nodes_eventviewer'] . '</li>'; } ?>
					<?php } ?>
				</ul>
			</li>
            <?php } ?>
			<li data-role="dropdown"><a href="#" title="<?php echo $envcomputername; ?>"><div class="icon-windows"></div><?php if (strlen($envcomputername) > $compnamelen) { echo substr($envcomputername, 0, $compnamelen) . '&nbsp;[...]'; } else { echo $envcomputername; } ?>&nbsp;</a>
				<ul class="dropdown-menu">
                    <li<?php if ($_SERVER['REQUEST_URI'] == '/') { echo $navselected; } ?>><a href="<?php echo $corelink; ?>/"><div class="icon-meter-slow"></div>System Info</a></li>
					<?php if ($nagios_agent_status == 'Running') { ?>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/nagios.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['nagiosstatus'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['nagios'])) { echo '/nagios.php'; } else { echo $_SESSION['nagios']; } ?>"><img src="/img/nagios_icon.png" width="20" height="16" style="vertical-align: middle;" /> Nagios Status</a><?php } else { ?><a style="color:#999999;"><img src="/img/nagios_icon_off.png" width="20" height="16" style="vertical-align: middle;" /> Nagios Status</a><?php } ?></li>
					<?php } ?>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/inventory.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['systeminventory'] > 0) { ?><a href="<?php echo $executorlink; ?>/inventory.php"><div class="icon-box"></div>System Inventory</a><?php } else { ?><a style="color:#999999;"><div class="icon-box" style="color:#999999;"></div>System Inventory</a><?php } ?></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/programs.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['installedprograms'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['installedprograms'])) { echo '/programs.php'; } else { echo $_SESSION['installedprograms']; } ?>"><div class="icon-checkmark"></div>Installed Programs</a><?php } else { ?><a style="color:#999999;"><div class="icon-checkmark" style="color:#999999;"></div>Installed Programs</a><?php } ?></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/wmiexplorer.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['wmiexplorer'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['wmiexplorer'])) { echo '/wmiexplorer.php'; } else { echo $_SESSION['wmiexplorer']; } ?>"><div class="icon-grid-view"></div>WMI Explorer</a><?php } else { ?><a style="color:#999999;"><div class="icon-grid-view" style="color:#999999;"></div>WMI Explorer</a><?php } ?></li>
                    <li class="divider"></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/shutdown.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemshutdown'] > 0) { ?><a href="<?php echo $executorlink; ?>/shutdown.php"><div class="icon-switch"></div>System Shutdown</a><?php } else { ?><a style="color:#999999;"><div class="icon-switch" style="color:#999999;"></div>System Shutdown</a><?php } ?></li>
				</ul>
			</li>
			<li data-role="dropdown"><a href="#"><div class="icon-newspaper"></div>tools&nbsp;</a>
				<ul class="dropdown-menu">
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/processes.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['processcontrol'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['processes'])) { echo '/processes.php'; } else { echo $_SESSION['processes']; } ?>"><div class="icon-bars"></div>Process Control</a><?php } else { ?><a style="color:#999999;"><div class="icon-bars" style="color:#999999;"></div>Process Control</a><?php } ?></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/services.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['servicecontrol'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['services'])) { echo '/services.php'; } else { echo $_SESSION['services']; } ?>"><div class="icon-cog"></div>Service Control</a><?php } else { ?><a style="color:#999999;"><div class="icon-cog" style="color:#999999;"></div>Service Control</a><?php } ?></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/netstat.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['networkstats'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['netstat'])) { echo '/netstat.php'; } else { echo $_SESSION['netstat']; } ?>"><div class="icon-tab"></div>Network Stats</a><?php } else { ?><a style="color:#999999;"><div class="icon-tab" style="color:#999999;"></div>Network Stats</a><?php } ?></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/scheduler.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['scheduledtasks'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['scheduler'])) { echo '/scheduler.php'; } else { echo $_SESSION['scheduler']; } ?>"><div class="icon-calendar"></div>Scheduled Tasks</a><?php } else { ?><a style="color:#999999;"><div class="icon-calendar" style="color:#999999;"></div>Scheduled Tasks</a><?php } ?></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/eventviewer.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Operators' || $_SESSION['usertype'] == 'Users' || $_SESSION['usersett']['eventviewer'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['eventviewer'])) { echo '/eventviewer.php'; } else { echo $_SESSION['eventviewer']; } ?>"><div class="icon-book"></div>Events Viewer</a><?php } else { ?><a style="color:#999999;"><div class="icon-book" style="color:#999999;"></div>Events Viewer</a><?php } ?></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/registry.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['systemregistry'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['registry'])) { echo '/registry.php'; } else { echo $_SESSION['registry']; } ?>"><div class="icon-grid"></div>System Registry</a><?php } else { ?><a style="color:#999999;"><div class="icon-grid" style="color:#999999;"></div>System Registry</a><?php } ?></li>
                    <li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/cli.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['commandline'] > 0) { ?><a href="<?php echo $executorlink; ?>/cli.php"><div class="icon-console"></div>Command Line</a><?php } else { ?><a style="color:#999999;"><div class="icon-console" style="color:#999999;"></div>Command Line</a><?php } ?></li>
                    <li class="divider"></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/explorer.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['filebrowser'] > 0) { ?><a href="<?php echo $executorlink; if (!isset($_SESSION['explorer'])) { echo '/explorer.php'; } else { echo $_SESSION['explorer']; } ?>"><div class="icon-folder"></div>File Browser</a><?php } else { ?><a style="color:#999999;"><div class="icon-folder" style="color:#999999;"></div>File Browser</a><?php } ?></li>
					<?php if (isset($_SESSION['textreader'])) { ?><li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/tail.php') > 0) { echo $navselected; } ?>><a href="<?php echo $executorlink; if (!isset($_SESSION['textreader'])) { echo '/tail.php'; } else { echo $_SESSION['textreader']; } ?>"><div class="icon-play-alt"></div>Text Reader</a></li><?php } ?>
					<?php if (isset($_SESSION['zipextract'])) { ?><li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/7zip.php') > 0) { echo $navselected; } ?>><a href="<?php echo $executorlink; if (!isset($_SESSION['zipextract'])) { echo '/7zip.php'; } else { echo $_SESSION['zipextract']; } ?>"><div class="icon-file-zip"></div>7zip Extractor</a></li><?php } ?>
				</ul>
			</li>
			<li data-role="dropdown"><a href="#"><div class="icon-locked-2"></div>admin&nbsp;</a>
				<ul class="dropdown-menu">
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/settings.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['changesettings'] > 0) { ?><a href="<?php echo $executorlink; ?>/settings.php"><div class="icon-list"></div>Change Settings</a><?php } else { ?><a style="color:#999999;"><div class="icon-list" style="color:#999999;"></div>Change Settings</a><?php } ?></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/users.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usersett']['usermanagement'] > 0) { ?><a href="<?php echo $executorlink; ?>/users.php"><div class="icon-user"></div>User Management</a><?php } else { ?><a style="color:#999999;"><div class="icon-user" style="color:#999999;"></div>User Management</a><?php } ?></li>
                    <li class="divider"></li>
					<li<?php if (strpos(strtolower('/' . $_SERVER['REQUEST_URI']), '/audit.php') > 0) { echo $navselected; } ?>><?php if ($_SESSION['usertype'] == 'Administrators' || $_SESSION['usertype'] == 'Auditors' || $_SESSION['usersett']['auditlog'] > 0) { ?><a href="<?php echo $executorlink; ?>/audit.php"><div class="icon-diary"></div>Audit Log</a><?php } else { ?><a style="color:#999999;"><div class="icon-diary" style="color:#999999;"></div>Audit Log</a><?php } ?></li>
				</ul>
			</li>
            <?php } ?>
			<li class="place-right"><a href="javascript:confirmlogout()"><div class="icon-exit"></div>logout</a></li><li class="place-right"><a href="javascript:;" onclick="document.getElementById('changepasswordform').submit();" title="Change Password: <?php echo $_SESSION['username']; ?>"><div class="icon-key"></div><?php if (strlen(strtolower($_SESSION['username'])) > $usernamelen) { echo substr(strtolower($_SESSION['username']), 0, $usernamelen) . '&nbsp;[...]'; } else { echo strtolower($_SESSION['username']); } ?></a></li>
		</ul>

	</div>
</div>
<?php if (count($badaut) > 22) { ?>
<?php if ($_SESSION['usertype'] == 'Administrators' ||  $_SESSION['usersett']['badaut'] > 0) { ?>
<?php
if (isset($_GET['clearbadauthagent'])) {
	foreach($badaut as $badautip)
	if($badautip != '.' && $badautip != '..') {
		unlink($_SERVER['DOCUMENT_ROOT'] . '\\badaut\\' . $badautip);
	}
	header('location: ' . $_SERVER['PHP_SELF']);
}
?>
<br /><blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;"><strong>warning!</strong> exceeded limit of 20 failed authentications... <a href="javascript:;" onClick="document.getElementById('badauthagent').submit();" style="font-size:12px; color:#FFFFFF;"><strong>click here</strong></a> to reset the counter and allow remote connections to the <strong>client</strong></blockquote>
<?php } else { ?>
<br /><blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;"><strong>warning!</strong> exceeded limit of 20 failed authentications... <strong>contact your administrator</strong> to reset the counter and allow remote connections to the <strong>client</strong></blockquote>
<?php } ?>
<form id="badauthagent" name="badauthagent" method="get">
	<input type="hidden" id="clearbadauthagent" name="clearbadauthagent" value="clear" />
</form>
<?php } ?>
<?php if ($serverstatus == 'run') { ?>
<?php $badauts = scandir(str_replace('\\core', '\\server', $_SERVER['DOCUMENT_ROOT']) . '\\badaut\\'); if (count($badauts) > 22) { ?>
<?php if ($_SESSION['usertype'] == 'Administrators' ||  $_SESSION['usersett']['badaut'] > 1) { ?>
<?php
if (isset($_GET['clearbadauthserver'])) {
	foreach($badauts as $badautsip)
	if($badautsip != '.' && $badautsip != '..') {
		unlink(str_replace('\\core', '\\server', $_SERVER['DOCUMENT_ROOT']) . '\\badaut\\' . $badautsip);
	}
	header('location: ' . $_SERVER['PHP_SELF']);
}
?>
<br /><blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;"><strong>warning!</strong> exceeded limit of 20 failed authentications... <a href="javascript:;" onClick="document.getElementById('badauthserver').submit();" style="font-size:12px; color:#FFFFFF;"><strong>click here</strong></a> to reset the counter and allow remote connections to the <strong>server</strong></blockquote>
<?php } else { ?>
<br /><blockquote style="font-size:12px; background-color:#B91D47; color:#FFFFFF; border-left-color:#863232;"><strong>warning!</strong> exceeded limit of 20 failed authentications... <strong>contact your administrator</strong> to reset the counter and allow remote connections to the <strong>server</strong></blockquote>
<?php } ?>
<form id="badauthserver" name="badauthserver" method="get">
	<input type="hidden" id="clearbadauthserver" name="clearbadauthserver" value="clear" />
</form>
<?php } ?>
<?php } ?>
</div>
<form id="changepasswordform" name="changepasswordform" method="post">
	<input type="hidden" id="changepassword" name="changepassword" value="" />
</form>
<?php if ($passwordexpired == 1) { echo '<p style="height:320px;">&nbsp;</p>'; } ?>