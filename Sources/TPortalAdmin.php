<?php
/**
 * @package TinyPortal
 * @version 3.0.3
 * @author IchBin - http://www.tinyportal.net
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
use TinyPortal\Admin as TPAdmin;
use TinyPortal\Article as TPArticle;
use TinyPortal\Block as TPBlock;
use TinyPortal\Util as TPUtil;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

// TinyPortal admin
function TPortalAdmin()
{
	global $scripturl, $sourcedir, $context, $txt;

	if (loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
	}
	if (loadLanguage('TPortal') == false) {
		loadLanguage('TPortal', 'english');
	}

	require_once $sourcedir . '/TPcommon.php';
	if (TP_SMF21) {
		require_once $sourcedir . '/Subs-Post.php';
	}

	$context['TPortal']['frontpage_visualopts_admin'] = [
		'left' => 0,
		'right' => 0,
		'center' => 0,
		'top' => 0,
		'bottom' => 0,
		'lower' => 0,
		'nolayer' => 0,
		'sort' => 'date',
		'sortorder' => 'desc'
	];

	$w = explode(',', $context['TPortal']['frontpage_visual']);

	if (in_array('left', $w)) {
		$context['TPortal']['frontpage_visualopts_admin']['left'] = 1;
	}
	if (in_array('right', $w)) {
		$context['TPortal']['frontpage_visualopts_admin']['right'] = 1;
	}
	if (in_array('center', $w)) {
		$context['TPortal']['frontpage_visualopts_admin']['center'] = 1;
	}
	if (in_array('top', $w)) {
		$context['TPortal']['frontpage_visualopts_admin']['top'] = 1;
	}
	if (in_array('bottom', $w)) {
		$context['TPortal']['frontpage_visualopts_admin']['bottom'] = 1;
	}
	if (in_array('lower', $w)) {
		$context['TPortal']['frontpage_visualopts_admin']['lower'] = 1;
	}
	if (in_array('nolayer', $w)) {
		$context['TPortal']['frontpage_visualopts_admin']['nolayer'] = 1;
	}
	foreach ($w as $r) {
		if (substr($r, 0, 5) == 'sort_') {
			$context['TPortal']['frontpage_visualopts_admin']['sort'] = substr($r, 5);
		}
		elseif (substr($r, 0, 10) == 'sortorder_') {
			$context['TPortal']['frontpage_visualopts_admin']['sortorder'] = substr($r, 10);
		}
	}

	TPadd_linktree($scripturl . '?action=tpadmin', $txt['tp-admin']);

	// some GET values set up
	$context['TPortal']['tpstart'] = isset($_GET['tpstart']) ? $_GET['tpstart'] : 0;

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// get all member groups
	tp_groups();

	// get the layout schemes
	get_catlayouts();

	// get the categories
	get_catnames();

	if (isset($_GET['id'])) {
		$context['TPortal']['subaction_id'] = $_GET['id'];
	}

	// check POST values
	$return = do_postchecks();

	if (!empty($return)) {
		redirectexit('action=tpadmin;sa=' . $return);
	}

	$tpsub = '';

	$subAction = TPUtil::filter('sa', 'get', 'string');
	$subActions = [];

	call_integration_hook('integrate_tp_pre_admin_subactions', [&$subActions]);

	$context['TPortal']['subaction'] = $subAction;
	// If it exists in our new subactions array load it
	if (!empty($subAction) && array_key_exists($subAction, $subActions)) {
		if (!empty($subActions[$subAction][0])) {
			require_once SOURCEDIR . '/' . $subActions[$subAction][0];
		}

		call_user_func_array($subActions[$subAction][1], $subActions[$subAction][2]);
	}
	elseif (isset($_GET['sa'])) {
		$context['TPortal']['subaction'] = $tpsub = $_GET['sa'];
		if (substr($_GET['sa'], 0, 11) == 'editarticle') {
			loadTemplate('TParticle');
			$context['sub_template'] = 'submitarticle';
			$tpsub = 'articles';
			$context['TPortal']['subaction'] = 'editarticle';
		}
		elseif (substr($_GET['sa'], 0, 11) == 'addarticle_') {
			loadTemplate('TParticle');
			$context['sub_template'] = 'submitarticle';
			$tpsub = 'articles';
			$context['TPortal']['subaction'] = $_GET['sa'];
			if ($_GET['sa'] == 'addarticle_html') {
				TPwysiwyg_setup();
			}
		}
		do_subaction($tpsub);
	}
	elseif (isset($_GET['blktype']) || isset($_GET['addblock']) || isset($_GET['blockon']) || isset($_GET['blockoff']) || isset($_GET['blockleft']) || isset($_GET['blockright']) || isset($_GET['blockcenter']) || isset($_GET['blocktop']) || isset($_GET['blockbottom']) || isset($_GET['blockfront']) || isset($_GET['blocklower']) || isset($_GET['blockdelete']) || isset($_GET['addpos']) || isset($_GET['subpos'])) {
		if (allowedTo('tp_blocks')) {
			$context['TPortal']['subaction'] = $tpsub = 'blocks';
			do_blocks($tpsub);
		}
		else {
			fatal_error($txt['tp-noadmin'], false);
		}
	}
	elseif (isset($_GET['linkon']) || isset($_GET['linkoff']) || isset($_GET['linkedit']) || isset($_GET['linkdelete']) || isset($_GET['linkdelete'])) {
		if (allowedTo('tp_blocks')) {
			$context['TPortal']['subaction'] = $tpsub = 'linkmanager';
			do_menus($tpsub);
		}
		else {
			fatal_error($txt['tp-noadmin'], false);
		}
	}
	elseif (isset($_GET['catdelete']) || isset($_GET['artfeat']) || isset($_GET['artfront']) || isset($_GET['artdelete']) || isset($_GET['arton']) || isset($_GET['artoff']) || isset($_GET['artsticky']) || isset($_GET['artlock']) || isset($_GET['catcollapse'])) {
		if (allowedTo('tp_articles')) {
			$context['TPortal']['subaction'] = $tpsub = 'articles';
			do_articles($tpsub);
		}
		else {
			fatal_error($txt['tp-noadmin'], false);
		}
	}
	else {
		$context['TPortal']['subaction'] = $tpsub = 'overview';
		do_admin($tpsub);
	}

	// done with all POST values, go to the correct screen
	$context['TPortal']['subtabs'] = '';
	if (in_array($tpsub, ['articles', 'addarticle_php', 'addarticle_html', 'addarticle_bbc', 'addarticle_import', 'strays', 'submission']) && allowedTo('tp_articles')) {
		$context['TPortal']['subtabs'] = [
			'articles' => [
				'lang' => true,
				'text' => 'tp-articles',
				'url' => $scripturl . '?action=tpadmin;sa=articles',
				'active' => ($context['TPortal']['subaction'] == 'articles' || $context['TPortal']['subaction'] == 'editarticle') && $context['TPortal']['subaction'] != 'strays',
			],
			'articles_nocat' => [
				'lang' => true,
				'text' => 'tp-uncategorised',
				'url' => $scripturl . '?action=tpadmin;sa=articles;sa=strays',
				'active' => $context['TPortal']['subaction'] == 'strays',
			],
			'submissions' => [
				'lang' => true,
				'text' => 'tp-tabs4',
				'url' => $scripturl . '?action=tpadmin;sa=articles;sa=submission',
				'active' => $context['TPortal']['subaction'] == 'submission',
			],
			'addarticle' => [
				'lang' => true,
				'text' => 'tp-tabs2',
				'url' => $scripturl . '?action=tpadmin;sa=addarticle_html' . (isset($_GET['cu']) ? ';cu=' . $_GET['cu'] : ''),
				'active' => $context['TPortal']['subaction'] == 'addarticle_html',
			],
			'addarticle_php' => [
				'lang' => true,
				'text' => 'tp-tabs3',
				'url' => $scripturl . '?action=tpadmin;sa=addarticle_php' . (isset($_GET['cu']) ? ';cu=' . $_GET['cu'] : ''),
				'active' => $context['TPortal']['subaction'] == 'addarticle_php',
			],
			'addarticle_bbc' => [
				'lang' => true,
				'text' => 'tp-addbbc',
				'url' => $scripturl . '?action=tpadmin;sa=addarticle_bbc' . (isset($_GET['cu']) ? ';cu=' . $_GET['cu'] : ''),
				'active' => $context['TPortal']['subaction'] == 'addarticle_bbc',
			],
			'article_import' => [
				'lang' => true,
				'text' => 'tp-addimport',
				'url' => $scripturl . '?action=tpadmin;sa=addarticle_import' . (isset($_GET['cu']) ? ';cu=' . $_GET['cu'] : ''),
				'active' => $context['TPortal']['subaction'] == 'addarticle_import',
			],
		];
	}
	elseif (in_array($tpsub, ['categories', 'addcategory', 'clist']) && allowedTo('tp_articles')) {
		$context['TPortal']['subtabs'] = [
			'categories' => [
				'lang' => true,
				'text' => 'tp-tabs5',
				'url' => $scripturl . '?action=tpadmin;sa=categories',
				'active' => $tpsub == 'categories',
			],
			'addcategory' => [
				'lang' => true,
				'text' => 'tp-tabs6',
				'url' => $scripturl . '?action=tpadmin;sa=addcategory',
				'active' => $tpsub == 'addcategory',
			],
			'clist' => [
				'lang' => true,
				'text' => 'tp-tabs11',
				'url' => $scripturl . '?action=tpadmin;sa=clist',
				'active' => $tpsub == 'clist',
			],
		];
	}
	elseif (in_array($tpsub, ['blocks']) && allowedTo('tp_blocks')) {
		$context['TPortal']['subtabs'] = [
			'blocks' => [
				'lang' => true,
				'text' => 'tp-blocks',
				'url' => $scripturl . '?action=tpadmin;sa=blocks',
				'active' => $tpsub == 'blocks' && !isset($_GET['overview']) && !isset($_GET['addblock']),
			],
			'blockoverview' => [
				'lang' => true,
				'text' => 'tp-blockoverview',
				'url' => $scripturl . '?action=tpadmin;sa=blocks;overview',
				'active' => $tpsub == 'blocks' && isset($_GET['overview']),
			],
			'addblock' => [
				'lang' => true,
				'text' => 'tp-addblock',
				'url' => $scripturl . '?action=tpadmin;addblock=;' . $context['session_var'] . '=' . $context['session_id'] . '',
				'active' => $tpsub == 'blocks' && isset($_GET['addblock']),
			],
		];
	}
	elseif (in_array($tpsub, ['menubox', 'addmenu']) && allowedTo('tp_blocks')) {
		$context['TPortal']['subtabs'] = [
			'menumanager' => [
				'lang' => true,
				'text' => 'tp-menumanager',
				'url' => $scripturl . '?action=tpadmin;sa=menubox',
				'active' => $tpsub == 'menubox',
			],
			'addmenu' => [
				'lang' => true,
				'text' => 'tp-addmenu',
				'url' => $scripturl . '?action=tpadmin;sa=addmenu;fullmenu',
				'active' => $tpsub == 'addmenu' && !isset($_GET['overview']),
			],
		];
	}

	if (array_search('tpadm', $context['template_layers']) === false) {
		// TP Admin menu layer
		$context['template_layers'][] = 'tpadm';
		// Shows subtab layer above for admin submenu links
		$context['template_layers'][] = 'subtab';
	}

	loadTemplate('TPortalAdmin');
	TPadminIndex($tpsub);

	call_integration_hook('integrate_tp_post_admin_subactions');
}

/* ******************************************************************************************************************** */
function do_subaction($tpsub)
{
	global $context, $txt;

	if (in_array($tpsub, ['articles', 'strays', 'categories', 'addcategory', 'submission', 'artsettings', 'articons', 'clist']) && (allowedTo(['tp_articles', 'tp_editownarticle']))) {
		do_articles();
	}
	elseif (in_array($tpsub, ['blocks', 'panels']) && (allowedTo('tp_blocks'))) {
		do_blocks();
	}
	elseif (in_array($tpsub, ['menubox', 'addmenu']) && (allowedTo('tp_blocks'))) {
		do_menus();
	}
	elseif (in_array($tpsub, ['frontpage', 'overview', 'credits', 'permissions']) && (allowedTo('tp_settings'))) {
		do_admin($tpsub);
	}
	elseif ($tpsub == 'settings' && (allowedTo('tp_settings'))) {
		do_admin('settings');
	}
	elseif (!$context['user']['is_admin']) {
		fatal_error($txt['tp-noadmin'], false);
	}
	else {
		redirectexit('action=tpadmin');
	}
}

function do_blocks()
{
	require_once SOURCEDIR . '/TPBlock.php';
	adminBlocks();
}

function do_menus()
{
	global $txt, $context, $scripturl, $smcFunc;
	TPadd_linktree($scripturl . '?action=tpadmin;sa=menubox', $txt['tp-menumanager']);

	$mid = isset($_GET['mid']) && is_numeric($_GET['mid']) ? $_GET['mid'] : 0;
	// first check any link stuff
	if (isset($_GET['linkon'])) {
		checksession('get');
		$what = is_numeric($_GET['linkon']) ? $_GET['linkon'] : 0;

		if ($what > 0) {
			$smcFunc['db_query'](
				'',
				'
				UPDATE {db_prefix}tp_variables
				SET value5 = {int:val5}
				WHERE id = {int:varid}',
				['val5' => 0, 'varid' => $what]
			);
		}
		redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
	}
	elseif (isset($_GET['linkoff'])) {
		checksession('get');
		$what = is_numeric($_GET['linkoff']) ? $_GET['linkoff'] : '0';

		if ($what > 0) {
			$smcFunc['db_query'](
				'',
				'
				UPDATE {db_prefix}tp_variables
				SET value5 = {int:val5}
				WHERE id = {int:varid}',
				['val5' => 1, 'varid' => $what]
			);
		}

		redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
	}
	elseif (isset($_GET['linkdelete'])) {
		checksession('get');
		$what = is_numeric($_GET['linkdelete']) ? $_GET['linkdelete'] : '0';

		if ($what > 0) {
			$smcFunc['db_query'](
				'',
				'
				DELETE FROM {db_prefix}tp_variables
				WHERE id = {int:varid}',
				['varid' => $what]
			);
		}

		redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
	}

	$context['TPortal']['menubox'] = [];
	$context['TPortal']['editmenuitem'] = [];
	$request = $smcFunc['db_query'](
		'',
		'
		SELECT * FROM {db_prefix}tp_variables
		WHERE type = {string:type}
		ORDER BY subtype ASC',
		['type' => 'menubox']
	);
	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			if ($row['value5'] == '-1') {
				$p = 'off';
				$status = '1';
			}
			else {
				$status = '0';
				$p = $row['value5'];
			}
			$mtype = substr($row['value3'], 0, 4);
			$idtype = substr($row['value3'], 4);

			if ($mtype != 'cats' && $mtype != 'arti' && $mtype != 'head' && $mtype != 'spac' && $mtype != 'menu') {
				$mtype = 'link';
				$idtype = $row['value3'];
			}
			elseif ($mtype == 'menu') {
				$idtype = substr($row['value3'], 4);
				$menuicon = $row['value8'];
			}

			if ($row['value2'] == '') {
				$newlink = '0';
			}
			else {
				$newlink = $row['value2'];
			}

			if ($mtype == 'head') {
				$mtype = 'head';
				$idtype = $row['value1'];
			}

			$context['TPortal']['menubox'][$row['subtype2']][] = [
				'id' => $row['id'],
				'menuID' => $row['subtype2'],
				'name' => $row['value1'],
				'pos' => $p,
				'type' => $mtype,
				'IDtype' => $idtype,
				'off' => $row['value5'],
				'sub' => $row['value4'],
				'position' => $row['value7'],
				'menuicon' => $row['value8'],
				'subtype' => $row['subtype'],
				'newlink' => $newlink,
			];
			if ($context['TPortal']['subaction'] == 'linkmanager') {
				$menuid = $_GET['linkedit'];
				if ($menuid == $row['id']) {
					$context['TPortal']['editmenuitem'] = [
						'id' => $row['id'],
						'menuID' => $row['subtype2'],
						'name' => $row['value1'],
						'pos' => $p,
						'type' => $mtype,
						'IDtype' => $idtype,
						'off' => $status,
						'sub' => $row['value4'],
						'position' => $row['value7'],
						'menuicon' => $row['value8'],
						'subtype' => $row['subtype'],
						'newlink' => $newlink,
					];
				}
			}
		}
		$smcFunc['db_free_result']($request);
	}

	$request = $smcFunc['db_query'](
		'',
		'
		SELECT * FROM {db_prefix}tp_variables
		WHERE type = {string:type}
		ORDER BY value1 ASC',
		['type' => 'menus']
	);
	$context['TPortal']['menus'] = [];
	$context['TPortal']['menus'][0] = [
		'id' => 0,
		'name' => 'Internal',
	];

	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['TPortal']['menus'][$row['id']] = [
				'id' => $row['id'],
				'name' => $row['value1'],
			];
		}
	}

	get_articles();
	// collect categories
	$request = $smcFunc['db_query'](
		'',
		'
		SELECT	id, value1 as name, value2 as parent
		FROM {db_prefix}tp_variables
		WHERE type = {string:type}',
		['type' => 'category']
	);

	$context['TPortal']['editcats'] = [];
	$allsorted = [];
	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$row['indent'] = 0;
			$allsorted[$row['id']] = $row;
		}
		$smcFunc['db_free_result']($request);
		if (count($allsorted) > 1) {
			$context['TPortal']['editcats'] = chain('id', 'parent', 'name', $allsorted);
		}
		else {
			$context['TPortal']['editcats'] = $allsorted;
		}
	}
	// add to linktree
	if (isset($_GET['mid'])) {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=menubox;mid=' . $_GET['mid'], $context['TPortal']['menus'][$_GET['mid']]['name']);
	}
	elseif (isset($_GET['linkedit']) && is_numeric($_GET['linkedit'])) {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=menubox;mid=' . $context['TPortal']['editmenuitem']['menuID'], $context['TPortal']['menus'][$context['TPortal']['editmenuitem']['menuID']]['name']);
		TPadd_linktree($scripturl . '?action=tpadmin;linkedit=' . $_GET['linkedit'], $context['TPortal']['editmenuitem']['name']);
	}
	if (isset($_GET['fullmenu'])) {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=addmenu', $txt['tp-addmenu']);
	}
	if (($context['TPortal']['subaction'] == 'addmenu') && (isset($_GET['mid']))) {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=addmenu', $txt['tp-addmenuitem']);
	}
}

// articles
function do_articles()
{
	global $context, $txt, $settings, $boardurl, $scripturl, $smcFunc;

	if (allowedTo('tp_articles') == false) {
		if (isset($_GET['sa']) && substr($_GET['sa'], 0, 11) == 'editarticle') {
			$article = TPUtil::filter('article', 'get', 'string');
			$request = $smcFunc['db_query'](
				'',
				'
		        SELECT id FROM {db_prefix}tp_articles
		        WHERE id = {int:article_id}
                AND author_id = {int:member_id}',
				[
					'article_id' => $article,
					'member_id' => $context['user']['id']
				]
			);
			if ($smcFunc['db_num_rows']($request) == 0) {
				fatal_error($txt['tp-noadmin'], false);
			}
			$smcFunc['db_free_result']($request);
		}
		else {
			fatal_error($txt['tp-noadmin'], false);
		}
	}

	// do an update of stray articles and categories
	$acats = [];
	$request = $smcFunc['db_query'](
		'',
		'
		SELECT id FROM {db_prefix}tp_variables
		WHERE type = {string:type}',
		['type' => 'category']
	);
	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$acats[] = $row['id'];
		}
		$smcFunc['db_free_result']($request);
	}
	if (count($acats) > 0) {
		$smcFunc['db_query'](
			'',
			'
			UPDATE {db_prefix}tp_variables
			SET value2 = {int:val2}
			WHERE type = {string:type}
			AND value2 NOT IN ({array_string:value2})',
			['val2' => 0, 'type' => 'category', 'value2' => $acats]
		);
		$smcFunc['db_query'](
			'',
			'
			UPDATE {db_prefix}tp_articles
			SET category = {int:cat}
			WHERE category NOT IN({array_int:category})
			AND category > 0',
			['cat' => 0, 'category' => $acats]
		);
	}

	require_once SOURCEDIR . '/TPArticle.php';
	articleAjax();

	// for the non-category articles, do a count.
	$request = $smcFunc['db_query'](
		'',
		'
		SELECT COUNT(*) as total
		FROM {db_prefix}tp_articles
		WHERE category = 0 OR category = 9999'
	);

	$row = $smcFunc['db_fetch_assoc']($request);
	$context['TPortal']['total_nocategory'] = $row['total'];
	$smcFunc['db_free_result']($request);

	// for the submissions too
	$request = $smcFunc['db_query'](
		'',
		'
		SELECT COUNT(*) as total
		FROM {db_prefix}tp_articles
		WHERE approved = 0'
	);

	$row = $smcFunc['db_fetch_assoc']($request);
	$context['TPortal']['total_submissions'] = $row['total'];
	$smcFunc['db_free_result']($request);

	// we are on categories screen
	if (in_array($context['TPortal']['subaction'], ['categories', 'addcategory', 'clist'])) {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=categories', $txt['tp-categories']);
		// first check if we simply want to copy or set as child
		if (isset($_GET['cu']) && is_numeric($_GET['cu'])) {
			$ccat = $_GET['cu'];
			if (isset($_GET['copy'])) {
				$request = $smcFunc['db_query'](
					'',
					'
					SELECT * FROM {db_prefix}tp_variables
					WHERE id = {int:varid}',
					['varid' => $ccat]
				);
				if ($smcFunc['db_num_rows']($request) > 0) {
					$row = $smcFunc['db_fetch_assoc']($request);
					$row['value1'] .= $txt['tp-copystring'];
					$smcFunc['db_free_result']($request);
					$smcFunc['db_insert'](
						'insert',
						'{db_prefix}tp_variables',
						[
							'value1' => 'string',
							'value2' => 'string',
							'value3' => 'string',
							'type' => 'string',
							'value4' => 'string',
							'value5' => 'int',
							'subtype' => 'string',
							'value7' => 'string',
							'value8' => 'string',
							'subtype2' => 'int'
						],
						[
							(isset($row['value1']) ? $row['value1'] : ''),
							(isset($row['id']) ? $row['id'] : ''),
							(isset($row['value3']) ? $row['value3'] : ''),
							(isset($row['type']) ? $row['type'] : ''),
							(isset($row['value4']) ? $row['value4'] : ''),
							(isset($row['value5']) ? $row['value5'] : ''),
							(isset($row['subtype']) ? $row['subtype'] : ''),
							(isset($row['value7']) ? $row['value7'] : ''),
							(isset($row['value8']) ? $row['value8'] : ''),
							(isset($row['subtype2']) ? $row['subtype2'] : '')
						],
						['id']
					);
				}
				redirectexit('action=tpadmin;sa=categories');
			}
			elseif (isset($_GET['child'])) {
				$request = $smcFunc['db_query'](
					'',
					'
					SELECT * FROM {db_prefix}tp_variables
					WHERE id = {int:varid}',
					['varid' => $ccat]
				);
				if ($smcFunc['db_num_rows']($request) > 0) {
					$row = $smcFunc['db_fetch_assoc']($request);
					$row['value1'] .= $txt['tp-copystring'];
					$smcFunc['db_free_result']($request);
					$smcFunc['db_insert'](
						'INSERT',
						'{db_prefix}tp_variables',
						[
							'value1' => 'string',
							'value2' => 'string',
							'value3' => 'string',
							'type' => 'string',
							'value4' => 'string',
							'value5' => 'int',
							'subtype' => 'string',
							'value7' => 'string',
							'value8' => 'string',
							'subtype2' => 'int'
						],
						[
							(isset($row['value1']) ? $row['value1'] : ''),
							(isset($row['id']) ? $row['id'] : ''),
							(isset($row['value3']) ? $row['value3'] : ''),
							(isset($row['type']) ? $row['type'] : ''),
							(isset($row['value4']) ? $row['value4'] : ''),
							(isset($row['value5']) ? $row['value5'] : ''),
							(isset($row['subtype']) ? $row['subtype'] : ''),
							(isset($row['value7']) ? $row['value7'] : ''),
							(isset($row['value8']) ? $row['value8'] : ''),
							(isset($row['subtype2']) ? $row['subtype2'] : '')
						],
						['id']
					);
				}
				redirectexit('action=tpadmin;sa=categories');
			}
			// guess we only want the category then
			else {
				// get membergroups
				get_grps();
				$context['html_headers'] .= '
			<script type="text/javascript"><!-- // --><![CDATA[
				function changeIllu(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/illustrations/\' + name;
				}
			// ]]></script>';

				$request = $smcFunc['db_query'](
					'',
					'
					SELECT * FROM {db_prefix}tp_variables
					WHERE id = {int:varid} LIMIT 1',
					['varid' => $ccat]
				);
				if ($smcFunc['db_num_rows']($request) > 0) {
					$row = $smcFunc['db_fetch_assoc']($request);
					$o = explode('|', $row['value7']);
					foreach ($o as $t => $opt) {
						$b = explode('=', $opt);
						if (isset($b[1])) {
							$row[$b[0]] = $b[1];
						}
					}
					$smcFunc['db_free_result']($request);
					$check = ['layout', 'catlayout', 'toppanel', 'bottompanel', 'leftpanel', 'rightpanel', 'centerpanel', 'lowerpanel', 'showchild'];
					foreach ($check as $c => $ch) {
						if (!isset($row[$ch])) {
							$row[$ch] = 0;
						}
					}
					$context['TPortal']['editcategory'] = $row;
				}
				// fetch all categories and subcategories
				$request = $smcFunc['db_query'](
					'',
					'
					SELECT	id, value1 as name, value2 as parent, value3, value4,
						value5, subtype, value7, value8, subtype2
					FROM {db_prefix}tp_variables
					WHERE type = {string:type}',
					['type' => 'category']
				);

				$context['TPortal']['editcats'] = [];
				$allsorted = [];
				$alcats = [];
				if ($smcFunc['db_num_rows']($request) > 0) {
					while ($row = $smcFunc['db_fetch_assoc']($request)) {
						$row['indent'] = 0;
						$allsorted[$row['id']] = $row;
						$alcats[] = $row['id'];
					}
					$smcFunc['db_free_result']($request);
					if (count($allsorted) > 1) {
						$context['TPortal']['editcats'] = chain('id', 'parent', 'name', $allsorted);
					}
					else {
						$context['TPortal']['editcats'] = $allsorted;
					}
				}
				TPadd_linktree($scripturl . '?action=tpadmin;sa=categories;cu=' . $ccat, $txt['tp-editcategory']);
			}
			return;
		}

		// fetch all categories and subcategories
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT id, value1 as name, value2 as parent, value3, value4,
				value5, subtype, value7, value8, subtype2
			FROM {db_prefix}tp_variables
			WHERE type = {string:type}',
			['type' => 'category']
		);

		$context['TPortal']['editcats'] = [];
		$allsorted = [];
		$alcats = [];
		if ($smcFunc['db_num_rows']($request) > 0) {
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$row['indent'] = 0;
				$allsorted[$row['id']] = $row;
				$alcats[] = $row['id'];
			}
			$smcFunc['db_free_result']($request);
			if (count($allsorted) > 1) {
				$context['TPortal']['editcats'] = chain('id', 'parent', 'name', $allsorted);
			}
			else {
				$context['TPortal']['editcats'] = $allsorted;
			}
		}
		// get the filecount as well
		if (count($alcats) > 0) {
			$request = $smcFunc['db_query'](
				'',
				'
				SELECT	art.category as id, COUNT(art.id) as files
				FROM {db_prefix}tp_articles as art
				WHERE art.category IN ({array_int:cats})
				GROUP BY art.category',
				['cats' => $alcats]
			);

			if ($smcFunc['db_num_rows']($request) > 0) {
				$context['TPortal']['cats_count'] = [];
				while ($row = $smcFunc['db_fetch_assoc']($request)) {
					$context['TPortal']['cats_count'][$row['id']] = $row['files'];
				}
				$smcFunc['db_free_result']($request);
			}
		}
		if ($context['TPortal']['subaction'] == 'addcategory') {
			TPadd_linktree($scripturl . '?action=tpadmin;sa=addcategory', $txt['tp-addcategory']);
		}
		if ($context['TPortal']['subaction'] == 'clist') {
			TPadd_linktree($scripturl . '?action=tpadmin;sa=clist', $txt['tp-tabs11']);
		}

		return;
	}
	TPadd_linktree($scripturl . '?action=tpadmin;sa=articles', $txt['tp-articles']);
	// are we inside a category?
	if (isset($_GET['cu']) && is_numeric($_GET['cu'])) {
		$where = $_GET['cu'];
	}
	// show the no category articles?
	if (isset($_GET['sa']) && $_GET['sa'] == 'strays') {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=strays', $txt['tp-strays']);
		$show_nocategory = true;
	}

	// submissions?
	if (isset($_GET['sa']) && $_GET['sa'] == 'submission') {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=submission', $txt['tp-submissions']);
		$show_submission = true;
	}

	// single article?
	if (isset($_GET['sa']) && substr($_GET['sa'], 0, 11) == 'editarticle') {
		$whatarticle = TPUtil::filter('article', 'get', 'string');
		TPadd_linktree($scripturl . '?action=tpadmin;sa=' . $_GET['sa'] . ';article=' . $whatarticle, $txt['tp-editarticle']);
	}
	// are we starting a new one?
	if (isset($_GET['sa']) && substr($_GET['sa'], 0, 11) == 'addarticle_') {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=' . $_GET['sa'], $txt['tp-addarticle']);
		$context['TPortal']['editarticle'] = [
			'id' => '',
			'date' => time(),
			'body' => '',
			'intro' => '',
			'useintro' => 0,
			'category' => !empty($_GET['cu']) ? $_GET['cu'] : 0,
			'frontpage' => 1,
			'author_id' => $context['user']['id'],
			'subject' => '',
			'author' => $context['user']['name'],
			'frame' => 'theme',
			'approved' => 0,
			'off' => 1,
			'options' => 'date,title,author,linktree,top,cblock,rblock,lblock,bblock,tblock,lbblock,category,catlist,comments,commentallow,commentupshrink,views,rating,ratingallow,avatar,inherit,social,nofrontsetting',
			'parse' => 0,
			'comments' => 0,
			'comments_var' => '',
			'views' => 0,
			'rating' => 0,
			'voters' => '',
			'id_theme' => 0,
			'shortname' => '',
			'sticky' => 0,
			'fileimport' => '',
			'topic' => 0,
			'locked' => 0,
			'illustration' => '',
			'headers' => '',
			'type' => substr($_GET['sa'], 11),
			'featured' => 0,
			'real_name' => $context['user']['name'],
			'author_id' => $context['user']['id'],
			'articletype' => substr($_GET['sa'], 11),
			'id_theme' => 0,
			'pub_start' => 0,
			'pub_end' => 0,
		];
		$context['html_headers'] .= '
			<script type="text/javascript"><!-- // --><![CDATA[
				function changeIllu(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/illustrations/\' + name;
				}
			// ]]></script>';
		// Add in BBC editor before we call in template so the headers are there
		if (substr($_GET['sa'], 11) == 'bbc') {
			$context['TPortal']['editor_id'] = 'tp_article_body';
			TP_prebbcbox($context['TPortal']['editor_id']);
		}
	}

	// fetch categories and subcategories
	if (!isset($show_nocategory)) {
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT DISTINCT var.id AS id, var.value1 AS name, var.value2 AS parent
			FROM {db_prefix}tp_variables AS var
			WHERE var.type = {string:type}
			' . (isset($where) ? 'AND var.value2' . ((TP_PGSQL == true) ? '::Integer' : ' ') . ' = {int:whereval}' : '') . '
			ORDER BY parent, id DESC',
			['type' => 'category', 'whereval' => isset($where) ? $where : 0]
		);

		if ($smcFunc['db_num_rows']($request) > 0) {
			$context['TPortal']['basecats'] = isset($where) ? [$where] : ['0', '9999'];
			$cats = [];
			$context['TPortal']['cats'] = [];
			$sorted = [];
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$sorted[$row['id']] = $row;
				$cats[] = $row['id'];
			}
			$smcFunc['db_free_result']($request);
			if (count($sorted) > 1) {
				$context['TPortal']['cats'] = chain('id', 'parent', 'name', $sorted);
			}
			else {
				$context['TPortal']['cats'] = $sorted;
			}
		}
	}

	if (isset($show_submission) && $context['TPortal']['total_submissions'] > 0) {
		// check if we have any start values
		$start = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'], ['date', 'id', 'author_id', 'type', 'subject', 'parse'])) ? $_GET['sort'] : 'date';
		$articles_per_page = 20;
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpadmin;sa=submission;sort=' . $sort, $start, $context['TPortal']['total_submissions'], $articles_per_page);
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT	art.id, art.date, art.frontpage, art.category, art.author_id as author_id,
				COALESCE(mem.real_name, art.author) as author, art.subject, art.approved,
				art.sticky, art.type, art.featured, art.locked, art.off, art.parse as pos
			FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			WHERE art.approved = {int:approved}
			ORDER BY art.{raw:col} {raw:sort}
			LIMIT {int:start}, {int:limit}',
			[
				'approved' => 0,
				'col' => $sort,
				'start' => $start,
				'sort' => in_array($sort, ['sticky', 'locked', 'frontpage', 'date', 'active']) ? 'DESC' : 'ASC',
				'limit' => $articles_per_page,
			]
		);

		if ($smcFunc['db_num_rows']($request) > 0) {
			$context['TPortal']['arts_submissions'] = [];
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$context['TPortal']['arts_submissions'][] = $row;
			}
			$smcFunc['db_free_result']($request);
		}
	}

	if (isset($show_nocategory) && $context['TPortal']['total_nocategory'] > 0) {
		// check if we have any start values
		$start = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'], ['off', 'date', 'id', 'author_id', 'locked', 'frontpage', 'sticky', 'featured', 'type', 'subject', 'parse'])) ? $_GET['sort'] : 'date';
		$articles_per_page = 20;
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpadmin;sa=articles;sort=' . $sort, $start, $context['TPortal']['total_nocategory'], $articles_per_page);
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT	art.id, art.date, art.frontpage, art.category, art.author_id as author_id,
				COALESCE(mem.real_name, art.author) as author, art.subject, art.approved, art.sticky,
				art.type, art.featured,art.locked, art.off, art.parse as pos
			FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			WHERE (art.category = 0 OR art.category = 9999)
			ORDER BY art.{raw:col} {raw:sort}
			LIMIT {int:start}, {int:limit}',
			[
				'col' => $sort,
				'sort' => in_array($sort, ['sticky', 'locked', 'frontpage', 'date', 'active']) ? 'DESC' : 'ASC',
				'start' => $start,
				'limit' => $articles_per_page,
			]
		);

		if ($smcFunc['db_num_rows']($request) > 0) {
			$context['TPortal']['arts_nocat'] = [];
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$context['TPortal']['arts_nocat'][] = $row;
			}
			$smcFunc['db_free_result']($request);
		}
	}
	// ok, fetch single article
	if (isset($whatarticle)) {
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT	art.*,  COALESCE(mem.real_name, art.author) AS real_name, art.author_id AS author_id,
				art.type as articletype, art.id_theme as id_theme
			FROM {db_prefix}tp_articles as art
			LEFT JOIN {db_prefix}members as mem ON (art.author_id = mem.id_member)
			WHERE art.id = {int:artid}',
			[
				'artid' => is_numeric($whatarticle) ? $whatarticle : 0,
			]
		);

		if ($smcFunc['db_num_rows']($request) > 0) {
			$context['TPortal']['editarticle'] = $smcFunc['db_fetch_assoc']($request);
			$context['TPortal']['editing_article'] = true;
			if ($context['TPortal']['editarticle']['type'] != 'bbc') {
				$context['TPortal']['editarticle']['body'] = $smcFunc['htmlspecialchars']($context['TPortal']['editarticle']['body'], ENT_QUOTES);
			}
			$smcFunc['db_free_result']($request);
		}
		else {
			fatal_error($txt['tp-articlenotexist'], false);
		}

		if ($context['TPortal']['editarticle']['articletype'] == 'html') {
			TPwysiwyg_setup();
		}

		// Add in BBC editor before we call in template so the headers are there
		if ($context['TPortal']['editarticle']['articletype'] == 'bbc') {
			$context['TPortal']['editor_id'] = 'tp_article_body';
			TP_prebbcbox($context['TPortal']['editor_id'], strip_tags($context['TPortal']['editarticle']['body']));
		}

		// fetch the WYSIWYG value
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT value1 FROM {db_prefix}tp_variables
			WHERE subtype2 = {int:subtype}
			AND type = {string:type} LIMIT 1',
			[
				'subtype' => is_numeric($whatarticle) ? $whatarticle : 0, 'type' => 'editorchoice',
			]
		);
		if ($smcFunc['db_num_rows']($request) > 0) {
			$row = $smcFunc['db_fetch_assoc']($request);
			$smcFunc['db_free_result']($request);
			$context['TPortal']['editorchoice'] = $row['value1'];
		}
		else {
			$context['TPortal']['editorchoice'] = 1;
		}

		$context['html_headers'] .= '
			<script type="text/javascript"><!-- // --><![CDATA[
				function changeIllu(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/illustrations/\' + name;
				}
			// ]]></script>';
	}
	// fetch article count for these
	if (isset($cats)) {
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT	art.category as id, COUNT(art.id) as files
			FROM {db_prefix}tp_articles as art
			WHERE art.category IN ({array_int:cat})
			GROUP BY art.category',
			['cat' => $cats]
		);

		$context['TPortal']['cats_count'] = [];
		if ($smcFunc['db_num_rows']($request) > 0) {
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$context['TPortal']['cats_count'][$row['id']] = $row['files'];
			}
			$smcFunc['db_free_result']($request);
		}
	}
	// get the icons needed
	tp_collectArticleIcons();

	// fetch all categories and subcategories
	$request = $smcFunc['db_query'](
		'',
		'
		SELECT	id, value1 as name, value2 as parent
		FROM {db_prefix}tp_variables
		WHERE type = {string:type}',
		['type' => 'category']
	);

	$context['TPortal']['allcats'] = [];
	$allsorted = [];

	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$allsorted[$row['id']] = $row;
		}

		$smcFunc['db_free_result']($request);
		if (count($allsorted) > 1) {
			$context['TPortal']['allcats'] = chain('id', 'parent', 'name', $allsorted);
		}
		else {
			$context['TPortal']['allcats'] = $allsorted;
		}
	}
	// not quite done yet lol, now we need to sort out if articles are to be listed
	if (isset($where)) {
		// check if we have any start values
		$start = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'], ['off', 'date', 'id', 'author_id', 'locked', 'frontpage', 'sticky', 'featured', 'type', 'subject', 'parse'])) ? $_GET['sort'] : 'date';
		$articles_per_page = 20;
		$context['TPortal']['categoryID'] = $where;
		// get the name
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT value1
			FROM {db_prefix}tp_variables
			WHERE id = {int:varid} LIMIT 1',
			[
				'varid' => $where
			]
		);
		if ($smcFunc['db_num_rows']($request) > 0) {
			$f = $smcFunc['db_fetch_assoc']($request);
			$smcFunc['db_free_result']($request);
			$context['TPortal']['categoryNAME'] = $f['value1'];
		}
		else {
			fatal_error($txt['tp-categorynotexist'], false);
		}
		// get the total first
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT	COUNT(*) as total
			FROM {db_prefix}tp_articles
			WHERE category = {int:cat}',
			[
				'cat' => $where
			]
		);

		$row = $smcFunc['db_fetch_assoc']($request);
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpadmin;sa=articles;sort=' . $sort . ';cu=' . $where, $start, $row['total'], $articles_per_page);
		$smcFunc['db_free_result']($request);

		$request = $smcFunc['db_query'](
			'',
			'
			SELECT art.id, art.date, art.frontpage, art.category, art.author_id AS author_id,
				COALESCE(mem.real_name, art.author) AS author, art.subject, art.approved, art.sticky,
				art.type, art.featured, art.locked, art.off, art.parse AS pos
			FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			WHERE art.category = {int:cat}
			ORDER BY art.{raw:sort} {raw:sorter}
			LIMIT {int:limit} OFFSET {int:start}',
			['cat' => $where,
				'sort' => $sort,
				'sorter' => in_array($sort, ['sticky', 'locked', 'frontpage', 'date', 'active']) ? 'DESC' : 'ASC',
				'start' => $start,
				'limit' => $articles_per_page
			]
		);
		TPadd_linktree($scripturl . '?action=tpadmin;sa=articles;cu=' . $where, $txt['tp-blocktype19']);

		if ($smcFunc['db_num_rows']($request) > 0) {
			$context['TPortal']['arts'] = [];
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$context['TPortal']['arts'][] = $row;
			}
			$smcFunc['db_free_result']($request);
		}
	}

	// get all themes for selection
	$context['TPthemes'] = [];
	$request = $smcFunc['db_query'](
		'',
		'
            SELECT th.value AS name, th.id_theme as id_theme, tb.value AS path
            FROM {db_prefix}themes AS th
            LEFT JOIN {db_prefix}themes AS tb ON th.id_theme = tb.id_theme
            WHERE th.variable = {string:thvar}
            AND tb.variable = {string:tbvar}
            AND th.id_member = {int:id_member}
            ORDER BY th.value ASC',
		[
			'thvar' => 'name', 'tbvar' => 'images_url', 'id_member' => 0,
		]
	);
	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['TPthemes'][] = [
				'id' => $row['id_theme'],
				'path' => $row['path'],
				'name' => $row['name']
			];
		}
		$smcFunc['db_free_result']($request);
	}

	$context['html_headers'] .= '
	<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/editor.js?rc1"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		function getXMLHttpRequest()
		{
			if (window.XMLHttpRequest)
				return new XMLHttpRequest;
			else if (window.ActiveXObject)
				return new ActiveXObject("MICROSOFT.XMLHTTP");
			else
				alert("Sorry, but your browser does not support Ajax");
		}

		$(document).ready(function() {
			startToggle();
		});

		function startToggle()
		{
			var img = document.getElementsByTagName("img");

			for(var i = 0; i < img.length; i++)
			{
				if (img[i].className == "toggleFront")
					img[i].onclick = toggleFront;
				else if (img[i].className == "toggleSticky")
					img[i].onclick = toggleSticky;
				else if (img[i].className == "toggleLock")
					img[i].onclick = toggleLock;
				else if (img[i].className == "toggleActive")
					img[i].onclick = toggleActive;
				else if (img[i].className == "toggleFeatured")
					img[i].onclick = toggleFeatured;
			}
		}

		function toggleActive(e)
		{
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;

			while(target.className != "toggleActive")
				  target = target.parentNode;

			var id = target.id.replace("artActive", "");
			var Ajax = getXMLHttpRequest();

			Ajax.open("POST", "?action=tpadmin;arton=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");

			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"

			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPactive2.png" ? "' . $settings['tp_images_url'] . '/TPactive1.png" : "' . $settings['tp_images_url'] . '/TPactive2.png";
				}
			}

			var params = "?action=tpadmin;arton=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '";
			Ajax.send(params);
		}
		function toggleFront(e)
		{
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;

			while(target.className != "toggleFront")
				  target = target.parentNode;

			var id = target.id.replace("artFront", "");
			var Ajax = getXMLHttpRequest();

			Ajax.open("POST", "?action=tpadmin;artfront=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");

			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"

			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPfront.png" ? "' . $settings['tp_images_url'] . '/TPfront2.png" : "' . $settings['tp_images_url'] . '/TPfront.png";
				}
			}

			var params = "?action=tpadmin;artfront=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '";
			Ajax.send(params);
		}
		function toggleSticky(e)
		{
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;

			while(target.className != "toggleSticky")
				  target = target.parentNode;

			var id = target.id.replace("artSticky", "");
			var Ajax = getXMLHttpRequest();

			Ajax.open("POST", "?action=tpadmin;artsticky=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");

			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"

			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPsticky1.png" ? "' . $settings['tp_images_url'] . '/TPsticky2.png" : "' . $settings['tp_images_url'] . '/TPsticky1.png";
				}
			}

			var params = "?action=tpadmin;artsticky=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '";
			Ajax.send(params);
		}
		function toggleLock(e)
		{
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;

			while(target.className != "toggleLock")
				  target = target.parentNode;

			var id = target.id.replace("artLock", "");
			var Ajax = getXMLHttpRequest();

			Ajax.open("POST", "?action=tpadmin;artlock=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");

			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"

			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPlock1.png" ? "' . $settings['tp_images_url'] . '/TPlock2.png" : "' . $settings['tp_images_url'] . '/TPlock1.png";
				}
			}

			var params = "?action=tpadmin;artlock=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '";
			Ajax.send(params);
		}
		function toggleFeatured(e)
		{
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;

			var aP=document.getElementsByTagName(\'img\');
			for(var i=0; i<aP.length; i++)
			{
				if(aP[i].className===\'toggleFeatured\' && aP[i] != target)
				{
					aP[i].src=\'' . $settings['tp_images_url'] . '/TPflag2.png\';
				}
			}


			while(target.className != "toggleFeatured")
				  target = target.parentNode;

			var id = target.id.replace("artFeatured", "");
			var Ajax = getXMLHttpRequest();

			Ajax.open("POST", "?action=tpadmin;artfeat=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");

			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"

			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPflag.png" ? "' . $settings['tp_images_url'] . '/TPflag2.png" : "' . $settings['tp_images_url'] . '/TPflag.png";
				}
			}

			var params = "?action=tpadmin;artfeat=" + id + ";' . $context['session_var'] . '=' . $context['session_id'] . '";
			Ajax.send(params);
		}
	// ]]></script>';

	if ($context['TPortal']['subaction'] == 'artsettings') {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=artsettings', $txt['tp-artsettings']);
	}
	elseif ($context['TPortal']['subaction'] == 'articons') {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=articons', $txt['tp-adminicons']);
	}
}

function do_admin($tpsub = 'overview')
{
	global $context, $txt, $scripturl;

	get_boards();
	$context['TPortal']['SSI_boards'] = explode(',', $context['TPortal']['SSI_board']);

	if ($tpsub == 'overview') {
		if (!TPcheckAdminAreas()) {
			fatal_error($txt['tp-noadmin'], false);
		}
	}
	elseif ($tpsub == 'permissions') {
		TPadd_linktree($scripturl . '?action=tpadmin;sa=permissions', $txt['tp-permissions']);
		$context['TPortal']['perm_all_groups'] = get_grps(true, true);
		$context['TPortal']['perm_groups'] = tp_fetchpermissions($context['TPortal']['modulepermissions']);
	}
	else {
		if ($tpsub == 'settings') {
			TPadd_linktree($scripturl . '?action=tpadmin;sa=settings', $txt['tp-settings']);
		}
		elseif ($tpsub == 'frontpage') {
			TPadd_linktree($scripturl . '?action=tpadmin;sa=frontpage', $txt['tp-frontpage']);
		}

		isAllowedTo('tp_settings');
	}
}

function do_postchecks()
{
	global $context, $txt, $settings, $boarddir, $smcFunc, $sourcedir;

	// If we have any setting changes add them to this array
	$updateArray = [];
	if ($context['TPortal']['action'] && (isset($_GET['sa']) && $_GET['sa'] == 'settings')) {
		// get all the themes
		$context['TPallthem'] = [];
		$request = $smcFunc['db_query'](
			'',
			'
				SELECT th.value AS name, th.id_theme as id_theme, tb.value AS path
				FROM {db_prefix}themes AS th
				LEFT JOIN {db_prefix}themes AS tb ON th.id_theme = tb.id_theme
				WHERE th.variable = {string:thvar}
				AND tb.variable = {string:tbvar}
				AND th.id_member = {int:id_member}
				ORDER BY th.value ASC',
			[
				'thvar' => 'name', 'tbvar' => 'images_url', 'id_member' => 0,
			]
		);
		if ($smcFunc['db_num_rows']($request) > 0) {
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$context['TPallthem'][] = [
					'id' => $row['id_theme'],
					'path' => $row['path'],
					'name' => $row['name']
				];
			}
			$smcFunc['db_free_result']($request);
		}
	}
	// which screen do we come from?
	if (!empty($_POST['tpadmin_form'])) {
		// get it
		$from = $_POST['tpadmin_form'];
		// block permissions overview
		if ($from == 'blockoverview') {
			checkSession('post');
			isAllowedTo('tp_blocks');

			$block = [];
			foreach ($_POST as $what => $value) {
				if (substr($what, 5, 7) == 'tpblock') {
					// get the id
					$bid = substr($what, 12);
					if (!isset($block[$bid])) {
						$block[$bid] = [];
					}

					if ($value != 'control' && !in_array($value, $block[$bid])) {
						$block[$bid][] = $value;
					}
				}
			}
			foreach ($block as $bl => $blo) {
				$request = $smcFunc['db_query'](
					'',
					'
					SELECT access FROM {db_prefix}tp_blocks
					WHERE id = {int:blockid}',
					['blockid' => $bl]
				);
				if ($smcFunc['db_num_rows']($request) > 0) {
					$row = $smcFunc['db_fetch_assoc']($request);
					$smcFunc['db_free_result']($request);
					$request = $smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_blocks
						SET access = {string:access} WHERE id = {int:blockid}',
						[
							'access' => implode(',', $blo),
							'blockid' => $bl,
						]
					);
				}
			}
			return 'blocks;overview';
		}
		elseif (in_array($from, ['settings', 'frontpage', 'artsettings', 'panels'])) {
			checkSession('post');
			isAllowedTo('tp_settings');
			$w = [];
			$ssi = [];
			$checkboxes = [];
			$default_image_upload_path = $boarddir . "/tp-images/";

			switch ($from) {
				case 'settings':
					$checkboxes = ['imageproxycheck', 'oldsidebar', 'disable_template_eval', 'fulltextsearch', 'hideadminmenu', 'hideprofileoption', 'use_promote', 'use_groupcolor', 'showstars'];
					break;
				case 'frontpage':
					$checkboxes = ['allow_guestnews', 'forumposts_avatar', 'use_attachment'];
					break;
				case 'artsettings':
					$checkboxes = ['use_wysiwyg', 'use_dragdrop', 'hide_editarticle_link', 'print_articles', 'allow_links_article_comments', 'hide_article_facebook', 'hide_article_twitter', 'hide_article_reddit', 'hide_article_digg', 'hide_article_delicious', 'hide_article_stumbleupon'];
					break;
				case 'panels':
					$checkboxes = ['useroundframepanels', 'showcollapse', 'hidebars_admin_only', 'hidebars_profile', 'hidebars_pm', 'hidebars_memberlist', 'hidebars_search', 'hidebars_calendar'];
					break;
				default:
					break;
			}

			if (!empty($checkboxes)) {
				foreach ($checkboxes as $v) {
					if (TPUtil::checkboxChecked('tp_' . $v)) {
						$updateArray[$v] = 1;
					}
					else {
						$updateArray[$v] = 0;
					}
					// remove the variable so we don't process it twice before the old logic is removed
					unset($_POST['tp_' . $v]);
				}
			}

			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 3) == 'tp_') {
					$where = substr($what, 3);
					$clean = $value;
					// for frontpage, do some extra
					if ($from == 'frontpage') {
						if (substr($what, 0, 20) == 'tp_frontpage_visual_') {
							$w[] = substr($what, 20);
							unset($clean);
						}
						elseif (substr($what, 0, 21) == 'tp_frontpage_usorting') {
							$w[] = 'sort_' . $value;
							unset($clean);
						}
						elseif (substr($what, 0, 26) == 'tp_frontpage_sorting_order') {
							$w[] = 'sortorder_' . $value;
							unset($clean);
						}
						// SSI boards
						elseif (substr($what, 0, 11) == 'tp_ssiboard') {
							$data = file_get_contents('php://input');
							$output = TPUtil::http_parse_query($data)['tp_ssiboard'];
							if (is_string($output)) {
								$ssi[] = $output;
							}
							elseif (is_array($output)) {
								$ssi = $output;
							}
							else {
								$ssi = [];
							}
						}
					}
					if ($from == 'settings' && $what == 'tp_frontpage_title') {
						$updateArray['frontpage_title'] = $clean;
					}
					else {
						if (isset($clean)) {
							$updateArray[$where] = $clean;
						}
					}
					if ($what == 'tp_image_upload_path') {
						unset($updateArray['image_upload_path']);
						if (empty($value)) {
							$updateArray['image_upload_path'] = $default_image_upload_path;
						}
						elseif (strcmp($context['TPortal']['image_upload_path'], $value) != 0) {
							// Only allow if part of the boarddir
							if (strncmp($value, $boarddir, strlen($boarddir)) == 0) {
								// It cann't be part of the existing path
								if (strncmp($value, $context['TPortal']['image_upload_path'], strlen($context['TPortal']['image_upload_path'])) != 0) {
									if (tp_create_dir($value)) {
										tp_recursive_copy($context['TPortal']['image_upload_path'], $value);
										tp_delete_dir($context['TPortal']['image_upload_path']);
										$updateArray['image_upload_path'] = $value;
									}
								}
							}
						}
						
					}
				}
			}

			// check the frontpage visual setting..
			if ($from == 'frontpage') {
				$updateArray['frontpage_visual'] = implode(',', $w);
				$updateArray['SSI_board'] = implode(',', $ssi);
			}
			updateTPSettings($updateArray);
			return $from;
		}
		// categories
		elseif ($from == 'categories') {
			checkSession('post');
			isAllowedTo('tp_articles');

			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 3) == 'tp_') {
					// for frontpage, do some extra
					if ($from == 'categories') {
						if (substr($what, 0, 19) == 'tp_category_value2_') {
							$where = substr($what, 19);
							//make sure parent are not its own parent
							$request = $smcFunc['db_query'](
								'',
								'
								SELECT value2 FROM {db_prefix}tp_variables
								WHERE id = {string:varid} LIMIT 1',
								[
									'varid' => $value
								]
							);
							$row = $smcFunc['db_fetch_assoc']($request);
							$smcFunc['db_free_result']($request);
							if ($row['value2'] == $where) {
								$smcFunc['db_query'](
									'',
									'
									UPDATE {db_prefix}tp_variables
									SET value2 = {string:val2}
									WHERE id = {string:varid}',
									[
										'val2' => '0',
										'varid' => $value,
									]
								);
							}

							$smcFunc['db_query'](
								'',
								'
								UPDATE {db_prefix}tp_variables
								SET value2 = {string:val2}
								WHERE id = {string:varid}',
								[
									'val2' => $value,
									'varid' => $where,
								]
							);
						}
					}
				}
			}
			return $from;
		}
		// articles
		elseif ($from == 'articles') {
			checkSession('post');
			isAllowedTo('tp_articles');

			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 14) == 'tp_article_pos') {
					$where = substr($what, 14);
					$smcFunc['db_query'](
						'',
						'
							UPDATE {db_prefix}tp_articles
							SET parse = {int:parse}
							WHERE id = {int:artid}',
						[
							'parse' => $value,
							'artid' => $where,
						]
					);
				}
			}
			if (isset($_POST['tpadmin_form_category']) && is_numeric($_POST['tpadmin_form_category'])) {
				return $from . ';cu=' . $_POST['tpadmin_form_category'];
			}
			else {
				return $from;
			}
		}
		// all the items
		elseif ($from == 'menuitems') {
			checkSession('post');
			isAllowedTo('tp_blocks');

			$all = explode(',', $context['TPortal']['sitemap_items']);
			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 8) == 'menu_pos') {
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET subtype = {string:subtype}
						WHERE id = {int:varid}',
						[
							'subtype' => $value,
							'varid' => substr($what, 8),
						]
					);
				}
				elseif (substr($what, 0, 8) == 'menu_sub') {
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET value4 = {string:val4}
						WHERE id = {int:varid}',
						[
							'val4' => $value,
							'varid' => substr($what, 8),
						]
					);
				}
				elseif (substr($what, 0, 15) == 'tp_menu_sitemap') {
					$new = substr($what, 15);
					if ($value == 0 && in_array($new, $all)) {
						foreach ($all as $key => $value) {
							if ($all[$key] == $new) {
								unset($all[$key]);
							}
						}
					}
					elseif ($value == 1 && !in_array($new, $all)) {
						$all[] = $new;
					}

					$updateArray['sitemap_items'] = implode(',', $all);
				}
			}
			updateTPSettings($updateArray);

			redirectexit('action=tpadmin;sa=menubox;mid=' . $_POST['tp_menuid']);
		}
		// all the menus
		elseif ($from == 'menus') {
			checkSession('post');
			isAllowedTo('tp_blocks');

			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 12) == 'tp_menu_name') {
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET value1 = {string:val1}
						WHERE id = {int:varid}',
						[
							'val1' => $value,
							'varid' => substr($what, 12),
						]
					);
				}
			}
			redirectexit('action=tpadmin;sa=menubox');
		}
		elseif ($from == 'singlemenuedit') {
			checkSession('post');
			isAllowedTo('tp_blocks');

			$where = isset($_POST['tpadmin_form_id']) ? $_POST['tpadmin_form_id'] : 0;

			foreach ($_POST as $what => $value) {
				if ($what == 'tp_menu_name') {
					// make sure special charachters can't be done
					$value = preg_replace('~&#\d+$~', '', $value);
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET value1 = {string:val1}
						WHERE id = {int:varid}',
						[
							'val1' => $value,
							'varid' => $where,
						]
					);
				}
				elseif ($what == 'tp_menu_newlink') {
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET value2 = {string:var2}
						WHERE id = {int:varid}',
						[
							'var2' => $value,
							'varid' => $where,
						]
					);
				}
				elseif ($what == 'tp_menu_menuid') {
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET subtype2 = {int:subtype2}
						WHERE id = {int:varid}',
						[
							'subtype2' => $value,
							'varid' => $where,
						]
					);
				}
				elseif ($what == 'tp_menu_type') {
					if ($value == 'cats') {
						$idtype = 'cats' . $_POST['tp_menu_category'];
					}
					elseif ($value == 'arti') {
						$idtype = 'arti' . $_POST['tp_menu_article'];
					}
					elseif ($value == 'link') {
						$idtype = $_POST['tp_menu_link'];
					}
					elseif ($value == 'head') {
						$idtype = 'head';
					}
					elseif ($value == 'spac') {
						$idtype = 'spac';
					}
					elseif ($value == 'menu') {
						$idtype = 'menu' . $_POST['tp_menu_link'];
					}

					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET value3 = {string:val3}
						WHERE id = {int:varid}',
						[
							'val3' => $idtype,
							'varid' => $where,
						]
					);
				}
				elseif ($what == 'tp_menu_sub') {
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET value4 = {string:val4}
						WHERE id = {int:varid}',
						[
							'val4' => $value,
							'varid' => $where,
						]
					);
				}
				elseif ($what == 'tp_menu_position') {
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET value7 = {string:val7}
						WHERE id = {int:varid}',
						[
							'val7' => $value,
							'varid' => $where,
						]
					);
				}
				elseif ($what == 'tp_menu_icon') {
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET value8 = {string:val8}
						WHERE id = {int:varid}',
						[
							'val8' => $value,
							'varid' => $where,
						]
					);
				}
				elseif (substr($what, 0, 15) == 'tp_menu_newlink') {
					$smcFunc['db_query'](
						'',
						'
						UPDATE {db_prefix}tp_variables
						SET value2 =
						WHERE id = {int:varid}',
						[
							'val2' => $value,
							'varid' => $where,
						]
					);
				}
			}
			redirectexit('action=tpadmin;linkedit=' . $where . ';' . $context['session_var'] . '=' . $context['session_id']);
		}
		// add a category
		elseif ($from == 'addcategory') {
			checkSession('post');
			isAllowedTo('tp_articles');
			$name = !empty($_POST['tp_cat_name']) ? $_POST['tp_cat_name'] : $txt['tp-noname'];
			$parent = !empty($_POST['tp_cat_parent']) ? $_POST['tp_cat_parent'] : '0';
			$shortname = !empty($_POST['tp_cat_shortname']) ? $_POST['tp_cat_shortname'] : '';

			$smcFunc['db_insert'](
				'INSERT',
				'{db_prefix}tp_variables',
				[
					'value1' => 'string',
					'value2' => 'string',
					'value3' => 'string',
					'type' => 'string',
					'value4' => 'string',
					'value5' => 'int',
					'subtype' => 'string',
					'value7' => 'string',
					'value8' => 'string',
					'subtype2' => 'int'
				],
				[strip_tags($name), $parent, '', 'category', '', 0, '', 'sort=date|sortorder=desc|articlecount=5|layout=1|catlayout=1|showchild=0|leftpanel=1|rightpanel=1|toppanel=1|centerpanel=1|lowerpanel=1|bottompanel=1', $shortname, 0],
				['id']
			);

			$go = $smcFunc['db_insert_id']('{db_prefix}tp_variables', 'id');
			redirectexit('action=tpadmin;sa=categories;cu=' . $go);
		}
		// the categort list
		elseif ($from == 'clist') {
			checkSession('post');
			isAllowedTo('tp_articles');

			$cats = [];
			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 8) == 'tp_clist') {
					$cats[] = $value;
				}
			}
			if (sizeof($cats) > 0) {
				$catnames = implode(',', $cats);
			}
			else {
				$catnames = '';
			}

			$updateArray['cat_list'] = $catnames;

			updateTPSettings($updateArray);

			return $from;
		}

		// edit a category
		elseif ($from == 'editcategory') {
			checkSession('post');
			isAllowedTo('tp_articles');

			$options = [];
			$groups = [];
			$where = $_POST['tpadmin_form_id'];
			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 3) == 'tp_') {
					$clean = $value;
					$param = substr($what, 12);
					if (in_array($param, ['value5', 'value6', 'value8'])) {
						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_variables
							SET ' . $param . ' = {string:val}
							WHERE id = {int:varid}',
							['val' => $value, 'varid' => $where]
						);
					}
					// parents needs some checking..
					elseif ($param == 'value2') {
						//make sure parent are not its own parent
						$request = $smcFunc['db_query'](
							'',
							'
							SELECT value2 FROM {db_prefix}tp_variables
							WHERE id = {int:varid} LIMIT 1',
							['varid' => $value]
						);
						$row = $smcFunc['db_fetch_assoc']($request);
						$smcFunc['db_free_result']($request);
						if (isset($row['value2']) && ($row['value2'] == $where)) {
							$smcFunc['db_query'](
								'',
								'
								UPDATE {db_prefix}tp_variables
								SET value2 = {string:val2}
								WHERE id = {int:varid}',
								['val2' => '0', 'varid' => $value]
							);
						}

						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_variables
							SET value2 = {string:val2}
							WHERE id = {int:varid}',
							['val2' => $value, 'varid' => $where]
						);
					}
					elseif ($param == 'value1') {
						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_variables
							SET value1 = {string:val1}
							WHERE id = {int:varid}',
							['val1' => strip_tags($value), 'varid' => $where]
						);
					}
					elseif ($param == 'value4') {
						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_variables
							SET value4 = {string:val4}
							WHERE id = {int:varid}',
							['val4' => $value, 'varid' => $where]
						);
					}
					elseif ($param == 'value9') {
						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_variables
							SET value9 = {string:val9}
							WHERE id = {int:varid}',
							['val9' => $value, 'varid' => $where]
						);
					}
					elseif (substr($param, 0, 6) == 'group_') {
						$groups[] = substr($param, 6);
					}
					else {
						$options[] = $param . '=' . $value;
					}
				}
			}
			$smcFunc['db_query'](
				'',
				'
				UPDATE {db_prefix}tp_variables
				SET value3 = {string:val3}, value7 = {string:val7}
				WHERE id = {int:varid}',
				['val3' => implode(',', $groups), 'val7' => implode('|', $options), 'varid' => $where]
			);
			$from = 'categories;cu=' . $where;
			return $from;
		}
		// stray articles
		elseif ($from == 'strays') {
			checkSession('post');
			isAllowedTo('tp_articles');

			$ccats = [];
			// check if we have some values
			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 16) == 'tp_article_stray') {
					$ccats[] = substr($what, 16);
				}
				elseif ($what == 'tp_article_cat') {
					$straycat = $value;
				}
				elseif ($what == 'tp_article_new') {
					$straynewcat = $value;
				}
			}
			// if new category create it first.
			if (isset($straycat) && sizeof($ccats) > 0) {
				$category = $straycat;
				if ($category == 0 && !empty($straynewcat)) {
					$request = $smcFunc['db_insert'](
						'INSERT',
						'{db_prefix}tp_variables',
						['value1' => 'string', 'value2' => 'string', 'value3' => 'string', 'type' => 'string', 'value4' => 'string', 'subtype' => 'string', 'value7' => 'string', 'value8' => 'string', 'value9' => 'string'],
						[strip_tags($straynewcat), '0', '', 'category', '', '', 'sort=date|sortorder=desc|articlecount=10|layout=1|catlayout=1|showchild=1|leftpanel=1|rightpanel=1|toppanel=1|centerpanel=1|lowerpanel=1|bottompanel=1', strip_tags($straynewcat), ''],
						['id']
					);

					$newcategory = $smcFunc['db_insert_id']('{db_prefix}tp_variables', 'id');
				}
				// now go through each article and put it into the category.
				$smcFunc['db_query'](
					'',
					'
					UPDATE {db_prefix}tp_articles
					SET category = {int:cat}
					WHERE id IN ({array_int:artid})',
					[
						'cat' => (!empty($newcategory) ? $newcategory : $category),
						'artid' => $ccats,
					]
				);
			}
			return $from;
		}
		// from articons...
		elseif ($from == 'articons') {
			checkSession('post');
			isAllowedTo('tp_articles');

			if (file_exists($_FILES['tp_article_newillustration']['tmp_name'])) {
				$name = TPuploadpicture('tp_article_newillustration', '', $context['TPortal']['icon_max_size'], 'jpg,gif,png', 'tp-files/tp-articles/illustrations');
				tp_createthumb('tp-files/tp-articles/illustrations/' . $name, $context['TPortal']['icon_width'], $context['TPortal']['icon_height'], 'tp-files/tp-articles/illustrations/s_' . $name);
				unlink('tp-files/tp-articles/illustrations/' . $name);
			}
			// how about deleted?
			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 15) == 'artillustration') {
					unlink($boarddir . '/tp-files/tp-articles/illustrations/' . $value);
				}
			}
			return $from;
		}
		// adding a full menu.
		elseif ($from == 'menuadd') {
			checkSession('post');
			isAllowedTo('tp_blocks');

			if (!empty($_POST['tp_menu_title'])) {
				$mtitle = strip_tags($_POST['tp_menu_title']);
				$smcFunc['db_insert'](
					'INSERT',
					'{db_prefix}tp_variables',
					['value1' => 'string', 'type' => 'string'],
					[$mtitle, 'menus'],
					['id']
				);
				redirectexit('action=tpadmin;sa=menubox');
			}
		}
		// adding a menu item.
		elseif ($from == 'menuaddsingle') {
			checkSession('post');
			isAllowedTo('tp_blocks');

			$mid = $_POST['tp_menu_menuid'];
			$mtitle = strip_tags($_POST['tp_menu_name']);
			if ($mtitle == '') {
				$mtitle = $txt['tp-no_title'];
			}

			$mtype = $_POST['tp_menu_type'];
			$mcat = isset($_POST['tp_menu_category']) ? $_POST['tp_menu_category'] : '';
			$mart = isset($_POST['tp_menu_article']) ? $_POST['tp_menu_article'] : '';
			$mlink = isset($_POST['tp_menu_link']) ? $_POST['tp_menu_link'] : '';
			$mhead = isset($_POST['tp_menu_head']) ? $_POST['tp_menu_head'] : '';
			$mnewlink = isset($_POST['tp_menu_newlink']) ? $_POST['tp_menu_newlink'] : '0';
			$menuicon = isset($_POST['tp_menu_icon']) ? $_POST['tp_menu_icon'] : '0';

			if ($mtype == 'cats') {
				$mtype = 'cats' . $mcat;
			}
			elseif ($mtype == 'arti') {
				$mtype = 'arti' . $mart;
			}
			elseif ($mtype == 'head') {
				$mtype = 'head' . $mhead;
			}
			elseif ($mtype == 'spac') {
				$mtype = 'spac';
			}
			elseif ($mtype == 'menu') {
				$mtype = 'menu' . $mlink;
			}
			else {
				$mtype = $mlink;
			}

			$msub = $_POST['tp_menu_sub'];
			$mpos = $_POST['tp_menu_position'];
			$smcFunc['db_insert'](
				'INSERT',
				'{db_prefix}tp_variables',
				[
					'value1' => 'string',
					'value2' => 'string',
					'value3' => 'string',
					'type' => 'string',
					'value4' => 'string',
					'value5' => 'int',
					'subtype2' => 'int',
					'value7' => 'string',
					'value8' => 'string'
				],
				[$mtitle, $mnewlink, $mtype, 'menubox', $msub, -1, $mid, $mpos, $menuicon],
				['id']
			);

			redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
		}
		// submitted ones
		elseif ($from == 'submission') {
			checkSession('post');
			isAllowedTo('tp_articles');

			$ccats = [];
			// check if we have some values
			foreach ($_POST as $what => $value) {
				if (substr($what, 0, 21) == 'tp_article_submission') {
					$ccats[] = substr($what, 21);
				}
				elseif ($what == 'tp_article_cat') {
					$straycat = $value;
				}
				elseif ($what == 'tp_article_new') {
					$straynewcat = $value;
				}
			}
			// update
			if (isset($straycat) && sizeof($ccats) > 0) {
				$category = $straycat;
				if ($category == 0 && !empty($straynewcat)) {
					// if new category create it first.
					$request = $smcFunc['db_insert'](
						'INSERT',
						'{db_prefix}tp_variables',
						[
							'value1' => 'string',
							'value2' => 'string',
							'type' => 'string',
						],
						[$straynewcat, '0', 'category'],
						['id']
					);
					$newcategory = $smcFunc['db_insert_id']('{db_prefix}tp_variables', 'id');
				}
				// now go through each article and put it into the category.
				$smcFunc['db_query'](
					'',
					'
					UPDATE {db_prefix}tp_articles
					SET approved = {int:approved}, category = {int:cat}
					WHERE id IN ({array_int:artid})',
					[
						'approved' => 1,
						'cat' => !empty($newcategory) ? $newcategory : $category,
						'artid' => $ccats,
					]
				);
				$smcFunc['db_query'](
					'',
					'
					DELETE FROM {db_prefix}tp_variables
					WHERE type = {string:type}
					AND value5 IN ({array_int:val5})',
					[
						'type' => 'art_not_approved',
						'val5' => $ccats,
					]
				);
			}
			return $from;
		}
		// from blocks screen
		elseif ($from == 'blocks') {
			checkSession('post');

			if (allowedTo('tp_settings')) {
				foreach (['admin_showblocks', 'blocks_edithide', 'uselangoption'] as $v) {
					if (TPUtil::checkboxChecked('tp_' . $v)) {
						$updateArray[$v] = 1;
					}
					else {
						$updateArray[$v] = 0;
					}
					// remove the variable so we don't process it twice before the old logic is removed
					unset($_POST['tp_' . $v]);
				}
				updateTPSettings($updateArray);
			}

			if (allowedTo('tp_blocks')) {
				foreach ($_POST as $what => $value) {
					if (substr($what, 0, 3) == 'pos') {
						$where = substr($what, 3);
						if (is_numeric($where)) {
							$smcFunc['db_query'](
								'',
								'
								UPDATE {db_prefix}tp_blocks
								SET pos = {int:pos}
								WHERE id = {int:blockid}',
								[
									'pos' => $value,
									'blockid' => $where
								]
							);
						}
					}
					elseif (substr($what, 0, 6) == 'addpos') {
						$where = substr($what, 6);
						if (is_numeric($where)) {
							$smcFunc['db_query'](
								'',
								'
								UPDATE {db_prefix}tp_blocks
								SET pos = (pos + 11)
								WHERE id = {int:blockid}',
								[
									'blockid' => $where
								]
							);
						}
					}
					elseif (substr($what, 0, 6) == 'subpos') {
						$where = substr($what, 6);
						if (is_numeric($where)) {
							$smcFunc['db_query'](
								'',
								'
								UPDATE {db_prefix}tp_blocks SET pos = (pos - 11)
								WHERE id = {int:blockid}',
								[
									'blockid' => $where
								]
							);
						}
					}
					elseif (substr($what, 0, 5) == 'title') {
						$where = strip_tags(substr($what, 5));
						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_blocks
							SET title = {string:title}
							WHERE id = {int:blockid}',
							[
								'title' => $value,
								'blockid' => $where,
							]
						);
					}
					elseif (substr($what, 0, 9) == 'blockbody') {
						$where = substr($what, 9);
						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_blocks
							SET body = {string:body}
							WHERE id = {int:blockid}',
							[
								'body' => $value,
								'blockid' => $where,
							]
						);
					}
					elseif (substr($what, 0, 4) == 'type') {
						// Check to see if the type has changed.
						$where = substr($what, 4);
						$request = $smcFunc['db_query'](
							'',
							'
                        	SELECT type FROM {db_prefix}tp_blocks
                        	WHERE id = {int:id} LIMIT 1',
							['id' => $where]
						);
						if ($smcFunc['db_num_rows']($request) > 0) {
							$row = $smcFunc['db_fetch_assoc']($request);
							$smcFunc['db_free_result']($request);
							if ($row['type'] != $value) {
								$defaultSetting = TPBlock::getInstance()->getBlockDefault($value) ?? '';
								$smcFunc['db_query'](
									'',
									'
									UPDATE {db_prefix}tp_blocks
									SET settings = {string:data}, type = {int:blocktype}, body = {string:defaultbody}
									WHERE id = {int:blockid}',
									['data' => json_encode($defaultSetting), 'blocktype' => $value, 'blockid' => $where, 'defaultbody' => '']
								);
							}
						}
					}
				}
			}
			redirectexit('action=tpadmin;sa=blocks');
		}
		// from editing block
		elseif ($from == 'addblock') {
			checkSession('post');
			isAllowedTo('tp_blocks');

			$title = empty($_POST['tp_addblocktitle']) ? $txt['tp-no_title'] : ($_POST['tp_addblocktitle']);
			$panel = $_POST['tp_addblockpanel'];
			$type = $_POST['tp_addblock'];
			if (!is_numeric($type)) {
				if (substr($type, 0, 3) == 'mb_') {
					$request = $smcFunc['db_query'](
						'',
						'
						SELECT * FROM {db_prefix}tp_blocks
						WHERE id = {int:blockid}',
						[
							'blockid' => substr($type, 3)
						]
					);
					if ($smcFunc['db_num_rows']($request) > 0) {
						$cp = $smcFunc['db_fetch_assoc']($request);
						$smcFunc['db_free_result']($request);
					}
				}
				else {
					$od = TPparseModfile(file_get_contents($context['TPortal']['blockcode_upload_path'] . $type . '.blockcode'), ['code']);
				}
			}
			if (isset($od['code'])) {
				$body = tp_convertphp($od['code']);
				$type = 10;
			}
			$defblocks = ['18', '19'];
			if (in_array($type, $defblocks)) {
				$body = '0';
			}
			else {
				$body = '';
			}

			$request = $smcFunc['db_query'](
				'',
				'
                SELECT pos FROM {db_prefix}tp_blocks
                WHERE bar = {int:bar}
                ORDER BY pos DESC LIMIT 1',
				[
					'bar' => $panel
				]
			);
			if ($smcFunc['db_num_rows']($request) > 0) {
				$pos = $smcFunc['db_fetch_assoc']($request);
				$pos = $pos['pos'] + 1;
				$smcFunc['db_free_result']($request);
			}
			else {
				$pos = 0;
			}

			if (isset($cp)) {
				$smcFunc['db_insert'](
					'INSERT',
					'{db_prefix}tp_blocks',
					[
						'type' => 'int',
						'frame' => 'string',
						'title' => 'string',
						'body' => 'string',
						'access' => 'string',
						'bar' => 'int',
						'pos' => 'int',
						'off' => 'int',
						'visible' => 'string',
						'lang' => 'string',
						'display' => 'string',
						'settings' => 'string',
					],
					[
						$cp['type'],
						$cp['frame'],
						$title,
						$cp['body'],
						$cp['access'],
						$panel,
						$pos,
						1,
						1,
						$cp['lang'],
						$cp['display'],
						$cp['settings'],
					],
					['id']
				);
			}
			else {
				$defaultBlock = TPBlock::getInstance()->getBlockDefault();

				$smcFunc['db_insert'](
					'INSERT',
					'{db_prefix}tp_blocks',
					[
						'type' => 'int',
						'frame' => 'string',
						'title' => 'string',
						'body' => 'string',
						'access' => 'string',
						'bar' => 'int',
						'pos' => 'int',
						'off' => 'int',
						'visible' => 'string',
						'lang' => 'string',
						'display' => 'string',
						'settings' => 'string',
					],
					[$type, 'theme', $title, $body, '-1,0,1', $panel, $pos, 1, 1, '', 'allpages', json_encode($defaultBlock[$type])],
					['id']
				);
			}

			$where = $smcFunc['db_insert_id']('{db_prefix}tp_blocks', 'id');
			if (!empty($where)) {
				redirectexit('action=tpadmin&amp;sa=editblock&amp;id=' . $where . ';sesc=' . $context['session_id']);
			}
			else {
				redirectexit('action=tpadmin;sa=blocks');
			}
		}
		// from editing block
		elseif ($from == 'blockedit') {
			checkSession('post');
			isAllowedTo('tp_blocks');

			$where = is_numeric($_POST['tpadmin_form_id']) ? $_POST['tpadmin_form_id'] : 0;
			$tpgroups = [];
			$access = [];
			$lang = [];
			$typechange = false;

			foreach ($_POST as $what => $value) {
				// We have a empty post value just skip it
				//				if(empty($value) && $value == '') {
				//					continue;
				//				}
				if (substr($what, 0, 9) == 'tp_block_') {
					$setting = substr($what, 9);

					if ($setting == 'body') {
						// If we came from WYSIWYG then turn it back into BBC regardless.
						if (!empty($_REQUEST['tp_block_body_mode']) && isset($_REQUEST['tp_block_body'])) {
							require_once $sourcedir . '/Subs-Editor.php';
							$_REQUEST['tp_block_body'] = html_to_bbc($_REQUEST['tp_block_body']);
							// We need to unhtml it now as it gets done shortly.
							$_REQUEST['tp_block_body'] = un_htmlspecialchars($_REQUEST['tp_block_body']);
							// We need this for everything else.
							$value = $_POST['tp_block_body'] = $_REQUEST['tp_block_body'];
						}

						if ($_POST['tp_block_type'] == 5) {
							$value = $smcFunc['htmlspecialchars']($value, ENT_QUOTES);
						}

						// PHP block?
						if ($_POST['tp_block_type'] == 10) {
							$value = tp_convertphp($value);
						}

						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_blocks
							SET ' . $setting . ' = {string:value}
							WHERE id = {int:blockid}',
							['value' => $value, 'blockid' => $where]
						);
					}
					elseif ($setting == 'title') {
						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_blocks
							SET title = {string:title}
							WHERE id = {int:blockid}',
							['title' => $value, 'blockid' => $where]
						);
					}
					elseif ($setting == 'body_mode' || $setting == 'body_choice' || $setting == 'body_pure') {
						$go = '';
					}
					elseif ($setting == 'frame') {
						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_blocks
							SET frame = {string:val}
							WHERE id = {int:blockid}',
							['val' => $value, 'blockid' => $where]
						);
					}
					elseif (substr($setting, 0, 4) == 'set_') {
						// Replace set_ as we don't want it anymore
						$setting = str_replace('set_', '', $setting);

						// Check for blocks in table, if none insert default blocks.
						$request = $smcFunc['db_query'](
							'',
							'
                            SELECT settings FROM {db_prefix}tp_blocks
                            WHERE id = {int:varid} LIMIT 1',
							['varid' => $where]
						);

						$data = [];
						if ($smcFunc['db_num_rows']($request) > 0) {
							$row = $smcFunc['db_fetch_assoc']($request);
							$data = json_decode($row['settings'], true);
							$smcFunc['db_free_result']($request);
						}
						$data[$setting] = $value;
						$smcFunc['db_query'](
							'',
							'
                            UPDATE {db_prefix}tp_blocks
                            SET settings = {string:data}
                            WHERE id = {int:blockid}',
							['data' => json_encode($data), 'blockid' => $where]
						);
					}
					elseif ($setting == 'type') {
						// Check to see if the type has changed.
						$request = $smcFunc['db_query'](
							'',
							'
							SELECT type FROM {db_prefix}tp_blocks
							WHERE id = {int:id} LIMIT 1',
							['id' => $where]
						);
						if ($smcFunc['db_num_rows']($request) > 0) {
							$row = $smcFunc['db_fetch_assoc']($request);
							$smcFunc['db_free_result']($request);
							if ($row['type'] != $value) {
								$typechange = true;
								$defaultSetting = TPBlock::getInstance()->getBlockDefault($value) ?? '';
								$smcFunc['db_query'](
									'',
									'
									UPDATE {db_prefix}tp_blocks
									SET settings = {string:data}, type = {int:blocktype}
									WHERE id = {int:blockid}',
									['data' => json_encode($defaultSetting), 'blocktype' => $value, 'blockid' => $where]
								);
							}
						}
					}
					else {
						$smcFunc['db_query'](
							'',
							'
							UPDATE {db_prefix}tp_blocks
							SET ' . $setting . ' = {raw:val}
							WHERE id = {int:blockid}',
							['val' => $value, 'blockid' => $where]
						);
					}
				}
				elseif (substr($what, 0, 8) == 'tp_group') {
					$tpgroups[] = substr($what, 8);
				}
				elseif (substr($what, 0, 10) == 'actiontype') {
					$access[] = '' . $value;
				}
				elseif (substr($what, 0, 9) == 'boardtype') {
					$access[] = 'board=' . $value;
				}
				elseif (substr($what, 0, 11) == 'articletype') {
					$access[] = 'tpage=' . $value;
				}
				elseif (substr($what, 0, 12) == 'categorytype') {
					$access[] = 'tpcat=' . $value;
				}
				elseif (substr($what, 0, 8) == 'langtype') {
					$access[] = 'tlang=' . $value;
				}
				elseif (substr($what, 0, 9) == 'dlcattype') {
					$access[] = 'dlcat=' . $value;
				}
				elseif (substr($what, 0, 9) == 'tpmodtype') {
					$access[] = 'tpmod=' . $value;
				}
				elseif (substr($what, 0, 9) == 'custotype' && !empty($value)) {
					$items = explode(',', $value);
					foreach ($items as $iti => $it) {
						$access[] = '' . $it;
					}
				}
				elseif (substr($what, 0, 8) == 'tp_lang_') {
					if (substr($what, 8) != '') {
						$lang[] = substr($what, 8) . '|' . $value;
					}
				}
				elseif (substr($what, 0, 18) == 'tp_userbox_options') {
					if (!isset($userbox)) {
						$userbox = [];
					}
					$userbox[] = $value;
				}
				elseif (substr($what, 0, 8) == 'tp_theme') {
					$theme = substr($what, 8);
					if (!isset($themebox)) {
						$themebox = [];
					}
					// get the path too
					if (isset($_POST['tp_path' . $theme])) {
						$tpath = $_POST['tp_path' . $theme];
					}
					else {
						$tpath = '';
					}

					$themebox[] = $theme . '|' . $value . '|' . $tpath;
				}
			}
			// construct the access++
			$smcFunc['db_query'](
				'',
				'
				UPDATE {db_prefix}tp_blocks
				SET	display = {string:acc2},
					access = {string:acc},
					lang = {string:lang}
				WHERE id = {int:blockid}',
				[
					'acc2' => implode(',', $access),
					'acc' => implode(',', $tpgroups),
					'lang' => implode('|', $lang),
					'blockid' => $where,
				]
			);

			if (isset($userbox)) {
				$updateArray['userbox_options'] = implode(',', $userbox);
			}

			if (isset($themebox)) {
				$smcFunc['db_query'](
					'',
					'
					UPDATE {db_prefix}tp_blocks
					SET body = {string:body}
					WHERE id = {int:blockid}',
					['body' => implode(',', $themebox), 'blockid' => $where, ]
				);
			}

			// anything from PHP block?
			if (isset($_POST['blockcode_overwrite'])) {
				// get the blockcode
				$newval = TPparseModfile(file_get_contents($context['TPortal']['blockcode_upload_path'] . $_POST['tp_blockcode'] . '.blockcode'), ['code']);
				$smcFunc['db_query'](
					'',
					'
					UPDATE {db_prefix}tp_blocks
					SET body = {string:body}
					WHERE id = {int:blockid}',
					['body' => $newval['code'], 'blockid' => $where]
				);
			}

			// check if uploaded picture
			if (isset($_FILES['qup_blockbody']) && file_exists($_FILES['qup_blockbody']['tmp_name'])) {
				$name = TPuploadpicture('qup_blockbody', $context['user']['id'] . 'uid', null, null, $context['TPortal']['image_upload_path']);
				tp_createthumb($context['TPortal']['image_upload_path'] . $name, 50, 50, $context['TPortal']['image_upload_path'] . 'thumbs/thumb_' . $name);
			}

			// if type changed, we wipe the body
			if ($typechange == true) {
				$smcFunc['db_query'](
					'',
					'
					UPDATE {db_prefix}tp_blocks
					SET body = {string:defaultbody}
					WHERE id = {int:blockid}',
					['blockid' => $where, 'defaultbody' => '']
				);
			}
			updateTPSettings($updateArray);

			redirectexit('action=tpadmin&amp;sa=editblock&amp;id=' . $where . ';' . $context['session_var'] . '=' . $context['session_id']);
		}
		// Editing an article?
		elseif (substr($from, 0, 11) == 'editarticle') {
			require_once SOURCEDIR . '/TPArticle.php';
			return articleEdit();
		}
	}
	else {
		return;
	}
}

function get_langfiles()
{
	global $context;

	// get all languages for blocktitles
	$context['TPortal']['langfiles'] = [];
	$languages = getLanguages();

	if (is_array($languages)) {
		foreach ($languages as $l => $ln) {
			$context['TPortal']['langfiles'][$l] = $ln['name'];
		}
	}
}

function get_catlayouts()
{
	global $context, $txt;

	// setup the layoutboxes
	$context['TPortal']['admin_layoutboxes'] = [
		['value' => '1', 'label' => $txt['tp-catlayout1']],
		['value' => '2', 'label' => $txt['tp-catlayout2']],
		['value' => '4', 'label' => $txt['tp-catlayout4']],
		['value' => '8', 'label' => $txt['tp-catlayout8']],
		['value' => '6', 'label' => $txt['tp-catlayout6']],
		['value' => '5', 'label' => $txt['tp-catlayout5']],
		['value' => '3', 'label' => $txt['tp-catlayout3']],
		['value' => '9', 'label' => $txt['tp-catlayout9']],
		['value' => '7', 'label' => $txt['tp-catlayout7']],
	];
}

function get_boards()
{
	global $context, $smcFunc;

	$context['TPortal']['boards'] = [];
	$request = $smcFunc['db_query'](
		'',
		'
		SELECT b.id_board as id, b.name, b.board_order
		FROM {db_prefix}boards as b
		WHERE 1=1
		ORDER BY b.board_order ASC',
		[]
	);
	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['TPortal']['boards'][] = $row;
		}

		$smcFunc['db_free_result']($request);
	}
}

function get_articles()
{
	global $context, $smcFunc;

	$context['TPortal']['edit_articles'] = [];

	$request = $smcFunc['db_query']('', '
		SELECT id, subject, shortname FROM {db_prefix}tp_articles
		WHERE approved = 1 AND off = 0
		ORDER BY subject ASC');

	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['TPortal']['edit_articles'][] = $row;
		}

		$smcFunc['db_free_result']($request);
	}
}

function get_catnames()
{
	global $context, $smcFunc;

	$context['TPortal']['catnames'] = [];

	$request = $smcFunc['db_query'](
		'',
		'
		SELECT id, value1 FROM {db_prefix}tp_variables
		WHERE type = {string:type}',
		['type' => 'category']
	);
	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['TPortal']['catnames'][$row['id']] = $row['value1'];
		}

		$smcFunc['db_free_result']($request);
	}
}

function tp_create_dir($path)
{
	global $sourcedir;

	require_once $sourcedir . '/Subs-Package.php';

	// Load up the package FTP information?
	create_chmod_control();

	if (!mktree($path, 0755)) {
		deltree($path, true);
		fatal_error($txt['tp-failedcreatedir'], false);
	}

	return true;
}

function tp_delete_dir($path)
{
	global $sourcedir;

	require_once $sourcedir . '/Subs-Package.php';

	// Load up the package FTP information?
	create_chmod_control();

	deltree($path, true);

	return true;
}

function tp_recursive_copy($src, $dst)
{
	$dir = opendir($src);
	tp_create_dir($dst);
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src . '/' . $file)) {
				tp_recursive_copy($src . '/' . $file, $dst . '/' . $file);
			}
			else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}
