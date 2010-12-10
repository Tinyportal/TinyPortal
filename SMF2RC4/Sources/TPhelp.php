<?php
/******************************************************************************
* TPhelp.php                                                                   *
******************************************************************************
* TP version: 1.0 RC1                                                                                                      *
* Software Version:           SMF 2.0                                                                                      *
* Software by:                Bloc (http://www.tinyportal.net)                                                      *
* Copyright 2005-2010 by:     Bloc (bloc@tinyportal.net)                                                         *
* Support, News, Updates at:  http://www.tinyportal.net                   *
*******************************************************************************/

if (!defined('SMF'))
        die('Hacking attempt...');


// TinyPortal module entrance
function TPhelp_init()
{
	global $context, $scripturl, $txt;

	$context['TPortal']['helptabs']=array('introduction','articles','frontpage','panels','blocks','modules','plugins');

	tp_hidebars();
	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum']=false;
	loadlanguage('TPhelp');
	loadtemplate('TPhelp');

	// setup menu items
	if(isset($_GET['p']) && in_array($_GET['p'],$context['TPortal']['helptabs']))
		$p = $_GET['p'];
	else
		$p= 'introduction';

	$context['admin_tabs'] = array();
	$context['admin_header']['tinyportal'] = 'TinyPortal';
	foreach($context['TPortal']['helptabs'] as $tab)
	{
		$context['admin_tabs']['tinyportal'][$tab] = array(
				'title' => $txt['tphelp_' . $tab],
				'description' => '',
				'href' => $scripturl . '?action=tpmod;sa=help;p=' . $tab,
				'is_selected' => $p == $tab ? true : false,
			);
	}

	$context['template_layers'][] = 'tpadm';
	loadlanguage('TPortalAdmin');
}

?>
