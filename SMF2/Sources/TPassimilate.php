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

function tpAddPermissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions) {

	$permissionList['membergroup'] = array_merge(
		array(
			'tp_settings' => array(false, 'tp', 'tp'),
			'tp_blocks' => array(false, 'tp', 'tp'),
			'tp_articles' => array(false, 'tp', 'tp'),
			'tp_alwaysapproved' => array(false, 'tp', 'tp'),
			'tp_submithtml' => array(false, 'tp', 'tp'),
			'tp_submitbbc' => array(false, 'tp', 'tp'),
			'tp_editownarticle' => array(false, 'tp', 'tp'),
			'tp_can_admin_shout' => array(false, 'tp', 'tp'),
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

	$string = '<a target="_blank" href="http://www.tinyportal.net" title="TinyPortal">TinyPortal</a> <a href="' . $scripturl . '?action=tpmod;sa=credits" title="TP 1.0">&copy; 2005-2012</a>';

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

?>