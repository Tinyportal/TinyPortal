<?php
/**
 * @package TinyPortal
 * @version 1.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2012 - The TinyPortal Team
 *
 */

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
