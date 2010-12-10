<?php
/******************************************************************************
* TPhelp.php                                                                   *
******************************************************************************
* TP version: 1.0 RC1                                                                                                      *
* Software Version:           SMF 1.1.x                                                                                   *
* Software by:                Bloc (http://www.tinyportal.net)                                                      *
* Copyright 2005-2010 by:     Bloc (bloc@tinyportal.net)                                                         *
* Support, News, Updates at:  http://www.tinyportal.net                   *
*******************************************************************************/

if (!defined('SMF'))
        die('Hacking attempt...');


// TinyPortal module entrance
function TPhelp_init()
{
	global $db_prefix, $settings, $modSettings, $context, $scripturl,$txt , $user_info , $sourcedir, $boardurl,$ID_MEMBER;


	$tp_prefix=$settings['tp_prefix'];

		$context['TPortal']['helptabs']=array('introduction','articles','frontpage','panels','blocks','modules','plugins');

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum']=false;
	loadlanguage('TPhelp');
	loadtemplate('TPhelp');

}

?>
