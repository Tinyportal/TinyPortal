<?php
/**
 * TPStandalone.php
 *
 * @package TinyPortal
 * @version 2.0.0
 * @author tinoest - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2020 - The TinyPortal Team
 *
 */

$ssi_path 	    = '';
$settings_path 	= '';

ob_start('tp_url_rewrite');

global $boardurl, $context;
require_once($settings_path);

$context['TPortal'] = array();
$actual_boardurl    = $boardurl;

require_once($ssi_path);

TPortal_init();

writeLog();

TPortalMain();

obExit(true);

function tp_url_rewrite($buffer) {{{
    global $actual_boardurl, $boardurl;
    if (!empty($buffer) && stripos($buffer, $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) !== false) {
        $buffer = str_replace($boardurl, $actual_boardurl, $buffer);
    }

    return $buffer;
}}}

?>
