<?php
/**
 * TPStandalone.php
 *
 * @package TinyPortal
 * @version 1.6.2
 * @author tinoest - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2019 - The TinyPortal Team
 *
 */


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
