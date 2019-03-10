<?php
/**
 * @package TinyPortal
 * @version 2.0.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2019 - The TinyPortal Team
 *
 */

if (!defined('SMF'))
        die('Hacking attempt...');

// TinyPortal module entrance
function TPhelp_init()
{
	global $context, $scripturl, $txt;

    $context['TPortal']['helpsection'] = 'introduction';
    $option = TPUtil::filter('p', 'get', 'string');
    if($option) {
        $helpOptions = array('introduction', 'articles', 'frontpage', 'panels', 'blocks', 'modules', 'plugins');
        if(in_array($option, $helpOptions)) {
            $context['TPortal']['helpsection'] = $option;
        }
    }
    $context['current_action'] = 'help';

	$context['TPortal']['helptabs'] = array('introduction', 'articles', 'frontpage', 'panels', 'blocks', 'modules', 'plugins');

	tp_hidebars();
	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = false;

	if(loadLanguage('TPhelp') == false)
		loadLanguage('TPhelp', 'english');

	loadtemplate('TPhelp');

	// setup menu items
	if(isset($_GET['p']) && in_array($_GET['p'],$context['TPortal']['helptabs']))
		$p = $_GET['p'];
	else
		$p = 'introduction';

	$context['admin_tabs'] = array();
	$context['admin_header']['tinyportal'] = 'TinyPortal';
	foreach($context['TPortal']['helptabs'] as $tab)
	{
		$context['admin_tabs']['tinyportal'][$tab] = array(
			'title' => $txt['tphelp_' . $tab],
			'description' => '',
			'href' => $scripturl . '?action=tportal;sa=help;p=' . $tab,
			'is_selected' => $p == $tab ? true : false,
		);
	}

	$context['template_layers'][] = 'tpadm';

	if(loadLanguage('TPortalAdmin') == false)
		loadLanguage('TPortalAdmin', 'english');
}

function TPCredits()
{
	tp_hidebars();
	$context['TPortal']['not_forum'] = false;

	if(loadLanguage('TPhelp') == false)
		loadLanguage('TPhelp', 'english');

	loadtemplate('TPhelp');
}
?>
