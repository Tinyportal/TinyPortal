<?php
/**
 * @package TinyPortal
 * @version 2.0.0
 * @author IchBin - https://www.tinyportal.net
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
use Tinyportal\Integrate as TPIntegrate;

global $boarddir;

require_once($boarddir . '/TinyPortal/Integrate.php');

function TPHookPreLoad()
{
    TPIntegrate::hookPreLoad();
}

function TPortalHookDefaultAction()
{
    return TPIntegrate::hookDefaultAction();
}

function TPortalInit()
{
	global $sourcedir;
    require_once($sourcedir . '/TPortal.php');

    TPortal_init();
}

function TPortalHookLoadTheme($id_theme)
{
    TPIntegrate::hookLoadTheme($id_theme);
}

?>
