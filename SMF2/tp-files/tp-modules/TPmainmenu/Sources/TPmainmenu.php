<?php
/******************************************************************************
* TPmainmenu.php                                                                 *
*******************************************************************************
* TP version: 1.0 beta 5                                                                                                      *
* Software Version:           SMF 2.0                                                                                      *
* Software by:                Bloc (http://www.tinyportal.net)                                                      *
* Copyright 2005-2012 by:     Bloc (bloc@tinyportal.net)                                                         *
* Support, News, Updates at:  http://www.tinyportal.net                   *
*******************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');
	

global $maintenance, $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $ID_MEMBER, $boarddir, $boardurl, $options, $sourcedir;

// just show we are here...

if(isset($_GET['mmenu']))
{
	loadtemplate('TPmainmenu');
	loadlanguage('TPmainmenu');

	$where = $_GET['mmenu'];
	if($where == 'admin')
		tp_mainmenu_admin();
	else
		tp_mainmenu_credit();

}
else
	return;

function tp_mainmenu_credit()
{
	global $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $boarddir, $boardurl, $options, $sourcedir;

	$context['sub_template'] = 'tpmmenu_credit';
	$context['page_title'] = 'TP Main Menu manager - information';
}

function tp_mainmenu_fetch()
{
	global $context;

	$context['mmenu'] = 1;
}

function tp_mainmenu_admin()
{
	global $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $boarddir, $boardurl, $options, $sourcedir;
	
	// admin it
	$context['TPortal']['mmenu']['action'] = 'admin';
	$context['template_layers'][] = 'tpadm';
	$context['template_layers'][] = 'subtab';

	TPadminIndex('mmenu',true);
	$context['current_action'] = 'admin';

	$context['sub_template'] = 'tpmmenu_admin';
	$context['page_title'] = 'TP Main Menu manager - admin';

	// setup menu items
	if (allowedTo('tp_mainmenu'))
	{
		$context['TPortal']['subtabs'] = array(
			'mmenu_admin' => array(
				'title' => $txt['admin'],
				'label' => 'admin',
				'description' => '',
				'href' => $scripturl . '?action=tpmod;mmenu=admin',
				'is_selected' => (isset($_GET['action']) && ($_GET['action']=='tpmod') && isset($_GET['mmenu']) && $_GET['mmenu']=='admin' && !isset($_GET['sa'])) ? true : false,
			),
			'mmenu_add' => array(
				'title' => $txt['tp-add'],
				'label' => 'tp-add',
				'description' => '',
				'href' => $scripturl . '?action=tpmod;mmenu=admin;sa=add',
				'is_selected' => (isset($_GET['action']) && ($_GET['action']=='tpmod') && isset($_GET['mmenu']) && $_GET['mmenu']=='admin' && isset($_GET['sa'])) ? true : false,
			),
		);
		$context['admin_header']['tp_mainmenu']=$txt['tp-mainmenu'];
	}

	tp_hidebars();
}

?>