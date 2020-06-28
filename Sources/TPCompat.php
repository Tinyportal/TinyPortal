<?php
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
