<?php

$ssi_path 	= '/var/www/html/SMF2.0/SSI.php';
$settings_path 	= '/var/www/html/SMF2.0/Settings.php';

require_once($settings_path);
global $boardurl;
$actual_boardurl 	= $boardurl;

require_once($ssi_path);
$boardurl 	= $actual_boardurl;
$scripturl 	= $actual_boardurl;

loadTheme(1, false);

TPortal_init();
writeLog();

call_user_func(whichTPAction());

obExit(true);

?>
