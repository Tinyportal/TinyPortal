<?php
/**
 * @package TinyPortal
 * @version 3.0.5
 * @author tinoest - https://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) - The TinyPortal Team
 *
 */

if (!defined('SMF')) {
	die('Hacking attempt...');
}

function TPListImageAdmin()
{
	global $context;

	if (loadLanguage('TPListImages') == false) {
		loadLanguage('TPListImages', 'english');
	}

	isAllowedTo('tp_can_list_images');

	if (!isset($context['tp_panels'])) {
		$context['tp_panels'] = [];
	}
	$context['current_action'] = 'admin';
	$context['sub_template'] = 'tp_list_images_admin';
	loadTemplate('TPListImages');

	if ($context['TPortal']['hidebars_admin_only'] == '1') {
		tp_hidebars();
	}

	$member = $_POST['id_member'] ?? '';
	$member = is_string($member) && ctype_digit($member) ? (int) $member : 0;
	if (isset($_GET['remove'])) {
		if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
			fatal_lang_error('no_access', false);
		}

		checkSession('post');
		TPRemoveImage($_POST['image'] ?? null, $member);
	}

	$context['TPortal']['image_members'] = TPMembers();
	$context['TPortal']['image_member'] = $member;
	$context['TPortal']['member_images'] = $member > 0 ? TPListMemberImages($member) : [];
}

function TPListMemberImages($member)
{
	global $boarddir, $boardurl, $context;

	$path = $context['TPortal']['image_upload_path'];
	$url = str_replace($boarddir, $boardurl, $path);
	$prefix = 'thumb_' . $member . 'uid';
	$images = [];
	foreach (is_dir($path . 'thumbs') ? (scandir($path . 'thumbs') ?: []) : [] as $thumbnail) {
		if (strncmp($thumbnail, $prefix, strlen($prefix)) !== 0 || !is_file($path . 'thumbs/' . $thumbnail)) {
			continue;
		}

		$image = substr($thumbnail, strlen('thumb_'));
		$images[$image] = is_file($path . $image)
			? $url . rawurlencode($image)
			: $url . 'thumbs/' . rawurlencode($thumbnail);
	}

	return $images;
}

function TPRemoveImage($image, $member)
{
	global $context;

	$prefix = $member . 'uid';
	if ($member <= 0 || !is_string($image) || strncmp($image, $prefix, strlen($prefix)) !== 0
		|| preg_match('~[\\\\/:\x00-\x1f]|[. ]$~D', $image)) {
		fatal_lang_error('no_access', false);
		return;
	}

	$path = $context['TPortal']['image_upload_path'];
	$original = $path . $image;
	$thumbnail = $path . 'thumbs/thumb_' . $image;
	if (!array_key_exists($image, TPListMemberImages($member)) || is_link($thumbnail) || is_link($original)
		|| (file_exists($original) && !is_file($original))) {
		fatal_lang_error('tp-listimage-remove-failed', false);
		return;
	}

	if (TPImageIsInUse($image, $prefix)) {
		fatal_lang_error('tp-listimage-in-use', false);
		return;
	}

	// Keep the thumbnail in the list until the original has been removed.
	if ((is_file($original) && !@unlink($original)) || !@unlink($thumbnail)) {
		fatal_lang_error('tp-listimage-remove-failed', false);
	}
}

function TPImageIsInUse($image, $prefix)
{
	global $smcFunc;

	// The uploader prefix survives URL encoding and narrows the search for legacy filenames.
	$pattern = '~(?:^|[\s/"\x27=\[\(,:])(?:thumb_)?' . preg_quote($image, '~')
		. '(?=$|[\s"\x27?<>&#\[\]\),}])~i';
	$sources = [
		'tp_articles' => ['body', 'intro', 'illustration', 'headers'],
		'tp_dlmanager' => ['description', 'screenshot', 'icon', 'file', 'link'],
		'tp_blocks' => ['body'],
		'tp_settings' => ['value'],
	];

	foreach ($sources as $table => $columns) {
		$conditions = [];
		foreach ($columns as $column) {
			$conditions[] = $column . ' LIKE {string:prefix}';
		}
		$where = '(' . implode(' OR ', $conditions) . ')';
		if ($table === 'tp_settings') {
			$where .= ' AND name LIKE {string:download_settings} ESCAPE {string:escape}';
		}

		$request = $smcFunc['db_query']('', '
			SELECT ' . implode(', ', $columns) . '
			FROM {db_prefix}' . $table . '
			WHERE ' . $where,
			['prefix' => '%' . $prefix . '%', 'download_settings' => 'dl!_%', 'escape' => '!']
		);
		if ($request === false) {
			fatal_lang_error('tp-listimage-remove-failed', false);
			return true;
		}

		while ($row = $smcFunc['db_fetch_row']($request)) {
			foreach ($row as $value) {
				$value = rawurldecode(html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8'));
				if (preg_match($pattern, $value)) {
					$smcFunc['db_free_result']($request);
					return true;
				}
			}
		}
		$smcFunc['db_free_result']($request);
	}

	return false;
}

function TPMembers()
{
	global $smcFunc, $context, $txt;

	$users = [];
	$directory = $context['TPortal']['image_upload_path'] . 'thumbs';
	if (is_dir($directory) && ($handle = opendir($directory))) {
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..' && $file != '.htaccess') {
				if (preg_match('/^thumb_([1-9][0-9]*)uid/', $file, $matches)) {
					$users[$matches[1]] = $matches[1];
				}
			}
		}
		closedir($handle);
	}

	foreach ($users as $user_id) {
		$data = $smcFunc['db_query'](
			'',
			'
			SELECT member_name FROM {db_prefix}members
			WHERE id_member = {int:member_id}',
			[
				'member_id' => $user_id
			]
		);
		if ($member = $smcFunc['db_fetch_assoc']($data)) {
			$users[$user_id] = $member['member_name'];
		}
		else {
			$users[$user_id] = $txt['tp-listimage-username'] . ' ' . $user_id;
		}
		$smcFunc['db_free_result']($data);
	}

	return $users;
}

function TPListImageAdminAreas()
{
	global $context, $scripturl, $txt;

	if (allowedTo('tp_can_list_images')) {
		if (loadLanguage('TPListImages') == false) {
			loadLanguage('TPListImages', 'english');
		}

		$context['admin_tabs']['custom_modules']['tplistimage'] = [
			'title' => $txt['tp-listimage'],
			'description' => '',
			'href' => $scripturl . '?action=tpadmin;sa=listimage;list',
			'is_selected' => ($context['TPortal']['subaction'] ?? '') === 'listimage',
		];
	}
}

function TPListImageAdminActions(&$subActions)
{
	$subActions = array_merge(
		[
			'listimage' => ['TPListImages.php', 'TPListImageAdmin',   []],
		],
		$subActions
	);
}
