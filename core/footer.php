<?php

if (!isset($_SESSION['session'])) { exit; }
if ($_SESSION['session'] != hash('sha512', $_SERVER['HTTP_X_FORWARDED_FOR'] . session_id())) { exit; }

include('/auditlog.php');

?>
    <div class="page">
        <div class="nav-bar"<?php if ($serverstatus == 'run') { echo ' style="background-color:#8063c8"'; } else { echo ' style="background-color:#637CC8"'; } ?>>
            <div class="nav-bar-inner padding10">
                <span class="element" style="white-space:nowrap; table-layout:fixed; overflow:hidden;">
                    &copy; 2013 - <?php echo date('Y'); ?> <a href="http://www.eurysco.com" style="color:#FFFFFF;">eurysco.com</a> | styled by <a href="http://metroui.org.ua" style="color:#FFFFFF;">&copy; 2012 - 2013 Metro UI CSS</a>
                </span>
            </div>
        </div>
    </div>
    </body>
</html>

<?php flush(); if (extension_loaded('zlib')) { ob_end_flush(); } ?>