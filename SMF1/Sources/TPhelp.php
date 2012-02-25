<?php
/****************************************************************************
* TPhelp.php																*
*****************************************************************************
* TP version: 1.0 RC4														*
* Software Version:				SMF 1.1.x									*
* Founder:						Bloc (http://www.blocweb.net)				*
* Developer:					IchBin (ichbin@ichbin.us)					*
* Copyright 2005-2012 by:     	The TinyPortal Team							*
* Support, News, Updates at:  	http://www.tinyportal.net					*
****************************************************************************/

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

// Credits page entrance
function TPCredits()
{
	tp_hidebars();
	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = false;
	loadlanguage('TPhelp');
	loadtemplate('TPhelp');
}
?>
