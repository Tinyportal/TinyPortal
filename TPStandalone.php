<?php
/**
 * TPStandalone.php
 *
 * @package TinyPortal
 * @version 1.6.7
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
ob_start('tp_url_rewrite');
global $boardurl, $context, $txt;

$txt['tp-tphelp']   = 'TinyPortal';

// Change to SMF 2.1 if running a 2.1 forum
$forum_version      = 'SMF 2.0.17';
$forum_path 	    = '';

require_once($forum_path . '/Settings.php');

$context['TPortal'] = array();
$actual_boardurl    = $boardurl;

require_once($forum_path . '/SSI.php');

TPortal_init();

writeLog();

call_user_func(whichTPAction());

obExit(true);

function tp_url_rewrite($buffer) {{{
    global $actual_boardurl, $boardurl;
    if (!empty($buffer) && stripos($buffer, $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) !== false) {
        $buffer = str_replace($boardurl, $actual_boardurl, $buffer);
    }

    return $buffer;
}}}

?>
