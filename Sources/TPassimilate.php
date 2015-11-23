<?php
/**
 * @package TinyPortal
 * @version 1.2
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2015 - The TinyPortal Team
 *
 */

function tpAddPermissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions) {

	loadLanguage('TPShout');

	$permissionList['membergroup'] = array_merge(
		array(
			'tp_settings' => array(false, 'tp', 'tp'),
			'tp_blocks' => array(false, 'tp', 'tp'),
			'tp_articles' => array(false, 'tp', 'tp'),
			'tp_alwaysapproved' => array(false, 'tp', 'tp'),
			'tp_submithtml' => array(false, 'tp', 'tp'),
			'tp_submitbbc' => array(false, 'tp', 'tp'),
			'tp_editownarticle' => array(false, 'tp', 'tp'),
			'tp_artcomment' => array(false, 'tp', 'tp'),
			'tp_can_admin_shout' => array(false, 'tp', 'tp'),
			'tp_can_shout' => array(false, 'tp', 'tp'),
			'tp_dlmanager' => array(false, 'tp', 'tp'),
			'tp_dlupload' => array(false, 'tp', 'tp'),
			'tp_dlcreatetopic' => array(false, 'tp', 'tp'),
		),
		$permissionList['membergroup']
	);

}

// Adds TP copyright in the buffer so we don't have to edit an SMF file
function tpAddCopy($buffer)
{
	global $context, $scripturl;

	$string = '<a target="_blank" href="http://www.tinyportal.net" title="TinyPortal">TinyPortal</a> <a href="' . $scripturl . '?action=tpmod;sa=credits" title="TP 1.2">&copy; 2005-2015</a>';

	if (SMF == 'SSI' || empty($context['template_layers']) || WIRELESS || strpos($buffer, $string) !== false)
		return $buffer;

	$find = array(
		'Simple Machines</a>',
		'class="copywrite"',
	);
	$replace = array(
		'Simple Machines</a><br />' . $string,
		'class="copywrite" style="line-height: 1;"',
	);

	if (!in_array($context['current_action'], array('post', 'post2')))
	{
		$finds[] = '[cutoff]';
		$replaces[] = '';
	}

	$buffer = str_replace($find, $replace, $buffer);

	if (strpos($buffer, $string) === false)
	{
		$string = '<div style="text-align: center; width: 100%; font-size: x-small; margin-bottom: 5px;">' . $string . '</div></body></html>';
		$buffer = preg_replace('~</body>\s*</html>~', $string, $buffer);
	}

	return $buffer;
}

function tpAddIllegalPermissions()
{
	global $context;
	
	if (empty($context['non_guest_permissions']))
		$context['non_guest_permissions'] = array();
	
	$tp_illegal_perms = array(
		'tp_settings',
		'tp_blocks',
		'tp_articles',
		'tp_alwaysapproved',
		'tp_submithtml',
		'tp_submitbbc',
		'tp_editownarticle',
		'tp_artcomment',
		'tp_can_admin_shout',
		'tp_can_shout',
		'tp_dlmanager',
		'tp_dlupload',
		'tp_dlcreatetopic',
	);
	$context['non_guest_permissions'] = array_merge($context['non_guest_permissions'], $tp_illegal_perms);
}

function tpAddMenuItems(&$buttons)
{
	global $context, $scripturl, $txt;
	
	// If SMF throws a fatal_error TP is not loaded. So don't even worry about menu items. 
	if (!isset($context['TPortal']))
		return;
		
	// Set the forum button activated if needed.
	if (isset($_GET['board']) || isset($_GET['topic']))
		$context['current_action'] = 'forum';
	elseif (isset($_GET['sa']) && $_GET['sa'] == 'help')
		$context['current_action'] = 'help';
				
	$new_buttons = array();
	foreach($buttons as $but => $val)
	{
		$new_buttons[$but] = $buttons[$but];
		
		if($but == 'home')
		{
			$new_buttons['forum'] = array(
				'title' => isset($txt['tp-forum']) ? $txt['tp-forum'] : 'Forum',
				'href' => $scripturl . '?action=forum',
				'show' => ($context['TPortal']['front_type'] != 'boardindex') ? true : false,
			);		
		}
		
		if($but == 'calendar')
		{
			$new_buttons['tpadmin'] = array(
				'title' => $txt['tp-tphelp'],
				'href' => $scripturl . '?action=tpadmin',
				'show' =>  TPcheckAdminAreas(),
				'sub_buttons' => tp_getbuttons(),
			);		
		}
		if($but == 'help')
		{
			$new_buttons['help']['sub_buttons'] = array(
				'tphelp' => array(
					'title' => $txt['tp-tphelp'],
					'href' => $scripturl . '?action=tpmod;sa=help',
					'show' => true,
				),
			);
		}
	}
	$buttons = $new_buttons;	
}

function tpAddProfileMenu(&$profile_areas)
{
	global $txt;
	
	$profile_areas['tp'] = array(
		'title' => 'Tinyportal',
		'areas' => array(),
	);

	$profile_areas['tp']['areas']['tpsummary'] = array(
		'label' => $txt['tpsummary'],
		'file' => 'TPmodules.php',
		'function' => 'tp_summary',
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
	);

	$profile_areas['tp']['areas']['tparticles'] = array(
		'label' => $txt['articlesprofile'],
		'file' => 'TPmodules.php',
		'function' => 'tp_articles',
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
		'subsections' => array(
			'articles' => array($txt['tp-articles'], array('profile_view_own', 'profile_view_any')),
			'settings' => array($txt['tp-settings'], array('profile_view_own', 'profile_view_any')),
		),
	);

	$profile_areas['tp']['areas']['tpdownload'] = array(
		'label' => $txt['downloadprofile'],
		'file' => 'TPmodules.php',
		'function' => 'tp_download',
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
	);

	$profile_areas['tp']['areas']['tpshoutbox'] = array(
		'label' => $txt['shoutboxprofile'],
		'file' => 'TPmodules.php',
		'function' => 'tp_shoutb',
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
	);
}

function addTPActions(&$actionArray)
{
	$actionArray = array_merge(
		array(
			'tpadmin' => array('TPortalAdmin.php', 'TPortalAdmin'),
			'forum' => array('BoardIndex.php', 'BoardIndex'),
			'tpmod' => array('TPmodules.php', 'TPmodules'),
		),
		$actionArray
	);
}

function whichTPAction()
{
	global $topic, $board, $sourcedir, $context;
	
	$theAction = false;
	// first..if the action is set, but empty, don't go any further
	if (isset($_REQUEST['action']) && $_REQUEST['action']=='')
	{
		require_once($sourcedir . '/BoardIndex.php');
		$theAction = 'BoardIndex';
	}
	// Action and board are both empty... maybe the portal page?
	if (empty($board) && empty($topic) && $context['TPortal']['front_type'] != 'boardindex')
	{
		require_once($sourcedir . '/TPortal.php');
		$theAction = 'TPortal';
	}
	// If frontpage set to boardindex but it's an article or category
	if (empty($board) && empty($topic) && $context['TPortal']['front_type'] == 'boardindex' && (isset($_GET['cat']) || isset($_GET['page'])))
	{
		require_once($sourcedir . '/TPortal.php');
		$theAction = 'TPortal';
	}
	// Action and board are still both empty...and no portal startpage - BoardIndex!
	elseif (empty($board) && empty($topic) && $context['TPortal']['front_type'] == 'boardindex')
	{
		require_once($sourcedir . '/BoardIndex.php');
		$theAction = 'BoardIndex';
	}
	return $theAction;
}

?>