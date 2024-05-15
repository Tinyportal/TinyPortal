<?php
/**
 * TinyPortal Standalone Mode
 *
 * @package TinyPortal
 * @version 3.0.1
 * @author tinoest - http://www.tinyportal.net
 * @author Rupurudu! - https://elmaci.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) - The TinyPortal Team
 *
 */

//######### Standalone Mode Setup ##########
// For the standalone mode to function, we need to know where SMF is installed.
// Write the absolute path to the SMF's installation directory. (not just '.'!)
$boarddir = dirname(__FILE__);

// Note: You shouldn't touch anything after this.
global $boarddir, $context, $mbname, $scripturl, $sourcedir, $txt;

if (!file_exists($boarddir . '/SSI.php')) {
	die('<h2>TinyPortal Standalone Mode</h2><p>Wrong $boarddir value. Please make sure that the $boarddir variable points to your forum\'s directory.</p>');
}

require_once $boarddir . '/SSI.php';

if ($context['TPortal']['front_placement'] != 'standalone') {
	loadLanguage('TPortalAdmin');
	die('<h1>' . $mbname . '</h1><h2>' . $txt['tp-frontpage_standalone_mode'] . '</h2><p>' . $txt['tp-frontpage_standalone_mode_text'] . '<a href="' . $scripturl . '?action=tpadmin;sa=frontpage">' . $txt['tp-frontpage_settings'] . '</a>.</p>');
}

doTPfrontpage();
writeLog();
TPortalMain();
obExit(true);
