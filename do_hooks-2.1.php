<?php
/**
 * @package TinyPortal
 * @version 1.5.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2018 - The TinyPortal Team
 *
 */
 
global $hooks, $mod_name;
$hooks = array(
	'integrate_pre_include' => '$sourcedir/TPassimilate.php,$sourcedir/TPortal.php',
	'integrate_load_permissions' => 'tpAddPermissions',
	'integrate_load_illegal_guest_permissions' => 'tpAddIllegalPermissions', 
	'integrate_credits' => 'tpAddCopy2',
	'integrate_buffer' => 'tpImageRewrite',
	'integrate_menu_buttons' => 'tpAddMenuItems',
	'integrate_display_buttons' => 'addPromoteButton',
	'integrate_actions' => 'addTPActions',
	'integrate_pre_profile_areas' => 'tpAddProfileMenu',
);
$mod_name = 'TinyPortal';

// ---------------------------------------------------------------------------------------------------------------------

if(file_exists( dirname( __FILE__ ).'/SSI.php' ) && !defined( 'SMF' ))
	require_once(dirname( __FILE__ ).'/SSI.php');
elseif(!defined( 'SMF' ))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

if(SMF == 'SSI')
{
	// Let's start the main job
	install_mod();
	// and then let's throw out the template! :P
	obExit( null, null, true );
}
else
{
	setup_hooks();
}

function install_mod()
{
	global $context, $mod_name;

	$context['mod_name'] = $mod_name;
	$context['sub_template'] = 'install_script';
	$context['page_title_html_safe'] = 'Hook installer for: '.$mod_name;
	if(isset($_GET['action']))
		$context['uninstalling'] = $_GET['action'] == 'uninstall' ? true : false;
	$context['html_headers'] .= '
	<style type="text/css">
    .buttonlist ul {
      margin:0 auto;
			display:table;
		}
	</style>';

	// Sorry, only logged in admins...
	isAllowedTo( 'admin_forum' );

	if(isset($context['uninstalling']))
		setup_hooks();
}

function setup_hooks()
{
	global $context, $hooks;

	$integration_function = empty($context['uninstalling']) ? 'add_integration_function' : 'remove_integration_function';
	foreach($hooks as $hook => $function)
		$integration_function( $hook, $function );

	$context['installation_done'] = true;
}
?>
