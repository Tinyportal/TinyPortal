<?php
/**
 * TinyPortal Standalone Mode
 *
 * @package TinyPortal
 * @version 3.0.1
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

########## Standalone Mode Setup ##########
# For the standalone mode to function, we need to know where SMF is installed.
# Write the absolute path to the SMF's installation directory. (not just '.'!)
$boarddir = dirname(__FILE__);

# Note: You shouldn't touch anything after this.
global $boarddir, $context, $mbname, $scripturl, $sourcedir;

if (!file_exists($boarddir . '/index.php'))
	die('<h2>TinyPortal Standalone Mode</h2><p>Wrong $boarddir value. Please make sure that the $boarddir variable points to your forum\'s directory.</p>');

require_once($boarddir . '/SSI.php');
require_once($sourcedir . '/TPortal.php');

TPortal_init();

if ($context['TPortal']['front_placement'] != 'standalone')
	die('<h1>' . $mbname . '</h1><h2>TinyPortal Standalone Mode</h2><p>Standalone Mode is not enabled. To enable standalone mode, visit the <a href="' . $scripturl . '?action=tpadmin;sa=frontpage">TinyPortal frontpage settings</a>.</p>');

doTPfrontpage();
writeLog();
TPortalMain();
obExit(true);
?>