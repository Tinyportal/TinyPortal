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
 
if (!defined('SMF'))
	die('Hacking attempt...');

// TinyPortal admin
function TPortalAdmin()
{
	global $scripturl, $sourcedir, $context;

	if(loadLanguage('TPortalAdmin') == false)
		loadLanguage('TPortalAdmin', 'english');
	if(loadLanguage('TPortal') == false)
		loadLanguage('TPortal', 'english');

	require_once($sourcedir . '/TPcommon.php');
	require_once($sourcedir . '/Subs-Post.php');

	$context['TPortal']['frontpage_visualopts_admin'] = array(
		'left' => 0, 
		'right' => 0,
		'center' => 0,
		'top' => 0,
		'bottom' => 0,
		'lower' => 0,
		'header' => 0,
		'nolayer' => 0,
		'sort' => 'date',
		'sortorder' => 'desc'
	);
	
	$w = explode(',', $context['TPortal']['frontpage_visual']);

	if(in_array('left',$w))
		$context['TPortal']['frontpage_visualopts_admin']['left'] = 1;
	if(in_array('right',$w))
		$context['TPortal']['frontpage_visualopts_admin']['right'] = 1;
	if(in_array('center',$w))
		$context['TPortal']['frontpage_visualopts_admin']['center'] = 1;
	if(in_array('top',$w))
		$context['TPortal']['frontpage_visualopts_admin']['top'] = 1;
	if(in_array('bottom',$w))
		$context['TPortal']['frontpage_visualopts_admin']['bottom'] = 1;
	if(in_array('lower',$w))
		$context['TPortal']['frontpage_visualopts_admin']['lower'] = 1;
	if(in_array('header',$w))
		$context['TPortal']['frontpage_visualopts_admin']['header'] = 1;
	if(in_array('nolayer',$w))
		$context['TPortal']['frontpage_visualopts_admin']['nolayer'] = 1;
	foreach($w as $r)
	{
		if(substr($r, 0, 5) == 'sort_')
			$context['TPortal']['frontpage_visualopts_admin']['sort'] = substr($r, 5);
		elseif(substr($r ,0, 10) == 'sortorder_')
			$context['TPortal']['frontpage_visualopts_admin']['sortorder'] = substr($r, 10);
	}

	// call up the editor
	TPwysiwyg_setup();

	TPadd_linktree($scripturl.'?action=tpadmin', 'TP Admin');
		
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

	if(isset($_GET['id']))
		$context['TPortal']['subaction_id'] = $_GET['id'];

	// check POST values
	$return = do_postchecks();
	
	if(!empty($return))
		redirectexit('action=tpadmin;sa=' . $return);

	$tpsub = '';

	if(isset($_GET['sa']))
	{
		$context['TPortal']['subaction'] = $tpsub = $_GET['sa'];
		if(substr($_GET['sa'], 0, 11) == 'editarticle')
		{
			$tpsub = 'articles';
			$context['TPortal']['subaction'] = 'editarticle';
		}
		elseif(substr($_GET['sa'], 0, 11) == 'addarticle_')
		{
			$tpsub = 'articles';
			$context['TPortal']['subaction'] = $_GET['sa'];
		}
		do_subaction($tpsub);
	}
	elseif(isset($_GET['blktype']) || isset($_GET['addblock']) || isset($_GET['blockon']) || isset($_GET['blockoff']) || isset($_GET['blockleft']) || isset($_GET['blockright']) || isset($_GET['blockcenter']) || isset($_GET['blocktop']) || isset($_GET['blockbottom']) || isset($_GET['blockfront']) || isset($_GET['blocklower']) || isset($_GET['blockdelete']) || isset($_GET['blockedit']) || isset($_GET['addpos']) || isset($_GET['subpos']))
	{
		$context['TPortal']['subaction'] = $tpsub = 'blocks';
		do_blocks($tpsub);
	}
	elseif(isset($_GET['linkon']) || isset($_GET['linkoff']) || isset($_GET['linkedit']) || isset($_GET['linkdelete']) || isset($_GET['linkdelete']))
	{
		$context['TPortal']['subaction'] = $tpsub = 'linkmanager';
		do_menus($tpsub);
	}
	elseif(isset($_GET['catdelete']) || isset($_GET['artfeat']) || isset($_GET['artfront']) || isset($_GET['artdelete']) || isset($_GET['arton']) || isset($_GET['artoff']) || isset($_GET['artsticky']) || isset($_GET['artlock']) || isset($_GET['catcollapse']))
	{
		$context['TPortal']['subaction'] = $tpsub = 'articles';
		do_articles($tpsub);
	}
	else
	{
		$context['TPortal']['subaction'] = $tpsub = 'overview';	
		do_news($tpsub);
	}

	// done with all POST values, go to the correct screen	
	$context['TPortal']['subtabs'] = '';
	if(in_array($tpsub,array('articles', 'addarticle_php', 'addarticle_html', 'addarticle_bbc', 'addarticle_import', 'strays', 'categories', 'addcategory')))
		$context['TPortal']['subtabs'] = array(
			'categories' => array(
				'lang' => true, 
				'text' => 'tp-tabs5',
				'url' => $scripturl . '?action=tpadmin;sa=categories',
				'active' => $tpsub == 'categories',
			),
			'addcategory' => array(
				'lang' => true, 
				'text' => 'tp-tabs6',
				'url' => $scripturl . '?action=tpadmin;sa=addcategory',
				'active' => $tpsub == 'addcategory',
			),
			'articles' => array(
				'lang' => true, 
				'text' => 'tp-articles',
				'url' => $scripturl . '?action=tpadmin;sa=articles',
				'active' => ($context['TPortal']['subaction'] == 'articles' || $context['TPortal']['subaction'] == 'editarticle') && $context['TPortal']['subaction'] != 'strays',
			),
			'articles_nocat' => array(
				'lang' => true, 
				'text' => 'tp-uncategorised' ,
				'url' => $scripturl . '?action=tpadmin;sa=articles;sa=strays',
				'active' => $context['TPortal']['subaction'] == 'strays',
			),
			'addarticle' => array(
				'lang' => true, 
				'text' => 'tp-tabs2',
				'url' => $scripturl . '?action=tpadmin;sa=addarticle_html' . (isset($_GET['cu']) ? ';cu='.$_GET['cu'] : ''),
				'active' => $context['TPortal']['subaction'] == 'addarticle_html',
			),
			'addarticle_php' => array(
				'lang' => true, 
				'text' => 'tp-tabs3',
				'url' => $scripturl . '?action=tpadmin;sa=addarticle_php' . (isset($_GET['cu']) ? ';cu='.$_GET['cu'] : ''),
				'active' => $context['TPortal']['subaction'] == 'addarticle_php',
			),
			'addarticle_bbc' => array(
				'lang' => true, 
				'text' => 'tp-addbbc',
				'url' => $scripturl . '?action=tpadmin;sa=addarticle_bbc' . (isset($_GET['cu']) ? ';cu='.$_GET['cu'] : ''),
				'active' => $context['TPortal']['subaction'] == 'addarticle_bbc',
			),
			'article_import' => array(
				'lang' => true, 
				'text' => 'tp-addimport',
				'url' => $scripturl . '?action=tpadmin;sa=addarticle_import' . (isset($_GET['cu']) ? ';cu='.$_GET['cu'] : ''),
				'active' => $context['TPortal']['subaction'] == 'addarticle_import',
			),
			'clist' => array(
				'lang' => true, 
				'text' => 'tp-tabs11',
				'url' => $scripturl . '?action=tpadmin;sa=clist',
				'active' => $tpsub == 'clist',
			),
		);
	elseif(in_array($tpsub,array('addcategory','categories','clist')))
		$context['TPortal']['subtabs'] = array(
			'categories' => array(
				'lang' => true, 
				'text' => 'tp-tabs5',
				'url' => $scripturl . '?action=tpadmin;sa=categories',
				'active' => $tpsub == 'categories',
			),
			'addcategory' => array(
				'lang' => true, 
				'text' => 'tp-tabs6',
				'url' => $scripturl . '?action=tpadmin;sa=addcategory',
				'active' => $tpsub == 'addcategory',
			),
			'clist' => array(
				'lang' => true, 
				'text' => 'tp-tabs11',
				'url' => $scripturl . '?action=tpadmin;sa=clist',
				'active' => $tpsub == 'clist',
			),
		);
	elseif(in_array($tpsub,array('blocks','panels')))
		$context['TPortal']['subtabs'] = array(
			'blocks' => array(
				'lang' => true, 
				'text' => 'tp-blocks',
				'url' => $scripturl . '?action=tpadmin;sa=blocks',
				'active' => $tpsub == 'blocks' && !isset($_GET['overview']),
			),
			'panels' => array(
				'lang' => true, 
				'text' => 'tp-panels',
				'url' => $scripturl . '?action=tpadmin;sa=panels',
				'active' => $tpsub == 'panels',
			),
			'blockoverview' => array(
				'lang' => true, 
				'text' => 'tp-blockoverview',
				'url' => $scripturl . '?action=tpadmin;sa=blocks;overview',
				'active' => $tpsub == 'blocks' && isset($_GET['overview']),
			),
		);
	// TP Admin menu layer
	$context['template_layers'][] = 'tpadm';
	// Shows subtab layer above for admin submenu links
	$context['template_layers'][] = 'subtab';
	loadTemplate('TPortalAdmin');
	TPadminIndex($tpsub);
}

function tp_notifyComments($memberlist, $message2, $subject)
{
	global $board, $topic, $txt, $scripturl, $user_info, $modSettings, $sourcedir, $smcFunc;

	require_once($sourcedir . '/Subs-Post.php');

	// Censor the subject and body...
	censorText($subject);
	censorText($message2);

	$subject = un_htmlspecialchars($subject);
	$message = trim(un_htmlspecialchars(strip_tags(strtr(parse_bbc($message2, false), array('<br />' => "\n", '</div>' => "\n", '</li>' => "\n", '&#91;' => '[', '&#93;' => ']')))));

	// Find the members with notification on for this board.
	$members = $smcFunc['db_query']('', '
		SELECT mem.id_member, mem.email_address, 
		FROM {db_prefix}members AS mem
		AND mem.id_member != {int:mem_id}
		AND mem.is_activated = {int:active}',
		array(
			'mem_id' => $user_info['id'], 'active' => 1,
		)
	);
	while ($rowmember = $smcFunc['db_fetch_assoc']($members))
	{
		// Setup the string for adding the body to the message, if a user wants it.
		$body_text = empty($modSettings['disallow_sendBody']) ? $txt['notification_new_topic_body'] . "\n\n" . $message . "\n\n" : '';

		$send_subject = sprintf($txt['notify_boards_subject'], $_POST['subject']);

		sendmail($rowmember['emailAddress'], $send_subject,
				sprintf($txt['notify_boards'], $_POST['subject'], $scripturl . '?topic=' . $topic . '.new#new', un_htmlspecialchars($user_info['name'])) .
				$txt['notify_boards_once'] . "\n\n" .
				(!empty($rowmember['notifySendBody']) ? $body_text : '') .
				$txt['notify_boardsUnsubscribe'] . ': ' . $scripturl . '?action=notifyboard;board=' . $board . ".0\n\n" .
				$txt[130], null, 't' . $topic);
	}
	$smcFunc['db_free_result']($members);
}

/* ******************************************************************************************************************** */

function do_subaction($tpsub)
{
	if(in_array($tpsub, array('articles', 'strays', 'categories', 'addcategory', 'submission', 'artsettings', 'articons')))
		do_articles();
	elseif(in_array($tpsub, array('blocks', 'panels')))
		do_blocks();
	elseif(in_array($tpsub, array('modules')))
		do_modules();
	elseif(in_array($tpsub, array('menubox', 'addmenu')))
		do_menus();
	elseif(in_array($tpsub, array('frontpage', 'overview', 'news', 'credits', 'permissions')))
		do_news($tpsub);
	elseif($tpsub == 'settings')
		do_news('settings');
	else
		do_news();
}

function do_blocks()
{
	global $context, $smcFunc, $txt, $settings, $scripturl;

	isAllowedTo('tp_blocks');

	$panels = array('left', 'right', 'center', 'top', 'bottom', 'lower', 'front');
	$blocktype = array('no', 'userbox', 'newsbox', 'statsbox', 'searchbox', 'html', 'onlinebox', 'themebox', 'oldshoutbox', 'catmenu', 'phpbox', 'scriptbox', 'recentbox', 'ssi', 'module', 'rss', 'sitemap', 'admin', 'articlebox', 'categorybox', 'tpmodulebox');
	$bars = array(1 => 'left', 2 => 'right', 3 => 'center', 4 => 'front', 5 => 'bottom', 6 => 'top', 7 => 'lower');

	if(isset($_GET['addblock']))
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=blocks', $txt['tp-blocks']);
		TPadd_linktree($scripturl.'?action=tpadmin;sa=addblock', $txt['tp-addblock']);
		// collect all available PHP block snippets
		$context['TPortal']['blockcodes'] = TPcollectSnippets();
		$request = $smcFunc['db_query']('', '
			SELECT id, title, bar 
			FROM {db_prefix}tp_blocks WHERE 1',
			array()
		);
		if ($smcFunc['db_num_rows']($request) > 0)
		{
			$context['TPortal']['copyblocks'] = array();
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$context['TPortal']['copyblocks'][] = $row;
			}
			$smcFunc['db_free_result']($request);
		}
	}
	// Move the block up or down in the panel list of blocks
	if(isset($_GET['addpos']))
	{
		checksession('get');
		$what = is_numeric($_GET['addpos']) ? $_GET['addpos'] : 0;
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks 
			SET pos = (pos + 11) 
			WHERE id = {int:blockid}',
			array('blockid' => $what)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}
	if(isset($_GET['subpos']))
	{
		checksession('get');
		$what = is_numeric($_GET['subpos']) ? $_GET['subpos'] : 0;	
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks SET pos = (pos - 11) 
			WHERE id = {int:blockid}',
			array('blockid' => $what)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	// change the on/off
	if(isset($_GET['blockon']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockon']) ? $_GET['blockon'] : 0;
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks 
			SET off = IF(off = 0 , 1, 0) 
			WHERE id = {int:blockid}',
			array(
				'blockid' => $what
			)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	// remove it?
	if(isset($_GET['blockdelete']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockdelete']) ? $_GET['blockdelete'] : 0;
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}tp_blocks 
			WHERE id = {int:blockid}',
			array(
				'blockid' => $what
			)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	// do the moving stuff
	if(isset($_GET['blockright']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockright']) ? $_GET['blockright'] : 0;
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks 
			SET bar = {int:bar} 
			WHERE id = {int:blockid}',
			array(
				'bar' => 2, 'blockid' => $what
			)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}
	elseif(isset($_GET['blockleft']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockleft']) ? $_GET['blockleft'] : 0;
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks 
			SET bar = {int:bar} 
			WHERE id = {int:blockid}',
			array(
				'bar' => 1, 'blockid' => $what,
			)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blockcenter']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockcenter']) ? $_GET['blockcenter'] : 0;
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks 
			SET bar = {int:bar} 
			WHERE id = {int:blockid}',
			array(
				'bar' => 3, 'blockid' => $what,
			)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blockfront']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockfront']) ? $_GET['blockfront'] : 0;
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks 
			SET bar = {int:bar} 
			WHERE id = {int:blockid}',
			array(
				'bar' => 4, 'blockid' => $what,
			)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blockbottom']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockbottom']) ? $_GET['blockbottom'] : 0;
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks 
			SET bar = {int:bar} 
			WHERE id = {int:blockid}',
			array(
				'bar' => 5, 'blockid' => $what,
			)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blocktop']))
	{
		checksession('get');
		$what = is_numeric($_GET['blocktop']) ? $_GET['blocktop'] : 0;
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks 
			SET bar = {int:bar} 
			WHERE id = {int:blockid}',
			array(
				'bar' => 6, 'blockid' => $what,
			)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blocklower']))
	{
		checksession('get');
		$what = is_numeric($_GET['blocklower']) ? $_GET['blocklower'] : 0;
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_blocks 
			SET bar = {int:bar} 
			WHERE id = {int:blockid}',
			array(
				'bar' => 7, 'blockid' => $what,
			)
		);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	// are we on overview screen?
	if(isset($_GET['overview']))
	{
		// fetch all blocks member group permissions
		$request = $smcFunc['db_query']('', '
			SELECT id, title, bar, access, type 
			FROM {db_prefix}tp_blocks 
			WHERE off = {int:off} 
			ORDER BY bar ,id',
			array(
				'off' => 0
			)
		);
		if ($smcFunc['db_num_rows']($request) > 0)
		{
			$context['TPortal']['blockoverview'] = array();
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$context['TPortal']['blockoverview'][] = array(
					'id' => $row['id'],	
					'title' => $row['title'],	
					'bar' => $row['bar'],	
					'type' => $row['type'],	
					'access' => explode(',', $row['access']),	
				);
			}
			$smcFunc['db_free_result']($request);
		}
		get_grps(true,true);
	}

	// are we editing a block?
	if(isset($_GET['blockedit']))
	{
		checksession('get');
		$blockedit = is_numeric($_GET['blockedit']) ? $_GET['blockedit'] : 0;
		TPadd_linktree($scripturl.'?action=tpadmin;sa=blocks', $txt['tp-blocks']);
		TPadd_linktree($scripturl.'?action=tpadmin;blockedit='.$blockedit . ';'.$context['session_var'].'='.$context['session_id'], $txt['tp-editblock']);
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_blocks 
			WHERE id = {int:blockid}',
			array(
				'blockid' => $blockedit
			)
		);
		if ($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			$acc2 = explode(',', $row['access2']);
			$context['TPortal']['blockedit'] = $row;
			$context['TPortal']['blockedit']['access22'] = $context['TPortal']['blockedit']['access2'];
			$context['TPortal']['blockedit']['body'] = $row['body'];
			unset($context['TPortal']['blockedit']['access2']);
			$context['TPortal']['blockedit']['access2'] = array(
				'action' => array(),
				'board' => array(),
				'page' => array(),
				'cat' => array(),
				'lang' => array(),
				'tpmod' => array(),
				'dlcat' => array(),
				'custo' => array(),
			);
			foreach($acc2 as $ss => $svalue)
			{
				if(substr($svalue, 0, 6)== 'actio=')
					$context['TPortal']['blockedit']['access2']['action'][]=substr($svalue,6);
				elseif(substr($svalue, 0,6) == 'board=')
					$context['TPortal']['blockedit']['access2']['board'][] = substr($svalue,6);
				elseif(substr($svalue, 0, 6) == 'tpage=')
					$context['TPortal']['blockedit']['access2']['page'][]  = substr($svalue,6);
				elseif(substr($svalue, 0, 6) == 'tpcat=')
					$context['TPortal']['blockedit']['access2']['cat'][] = substr($svalue,6);
				elseif(substr($svalue, 0, 6) == 'tpmod=')
					$context['TPortal']['blockedit']['access2']['tpmod'][] = substr($svalue,6);
				elseif(substr($svalue, 0, 6) == 'tlang=')
					$context['TPortal']['blockedit']['access2']['lang'][] = substr($svalue,6);
				elseif(substr($svalue, 0, 6) == 'dlcat=')
					$context['TPortal']['blockedit']['access2']['dlcat'][] = substr($svalue,6);
				elseif(substr($svalue, 0, 6) == 'custo=')
					$context['TPortal']['blockedit']['access2']['custo'] = substr($svalue,6);
			}

			// Add in BBC editor before we call in template so the headers are there
			if($context['TPortal']['blockedit']['type'] == '5')
			{
				$context['TPortal']['editor_id'] = 'tp_block_body';
				TP_prebbcbox($context['TPortal']['editor_id'], strip_tags($context['TPortal']['blockedit']['body'])); 			
			}			
			
			if($context['TPortal']['blockedit']['lang'] != '')
			{
				$context['TPortal']['blockedit']['langfiles'] = array();
				$lang = explode('|', $context['TPortal']['blockedit']['lang']);
				$num = count($lang);
				for($i = 0; $i < $num; $i = $i + 2)
				{
					$context['TPortal']['blockedit']['langfiles'][$lang[$i]] = $lang[$i+1];
				}
			}
			$smcFunc['db_free_result']($request);
			// collect all available PHP block snippets
			$context['TPortal']['blockcodes'] = TPcollectSnippets();
			
			get_grps();
			get_langfiles();
			get_boards();
			get_articles();
			tp_getDLcats();
			tp_getTPmodules();

			$context['TPortal']['edit_categories'] = array();
			
			$request = $smcFunc['db_query']('', '
				SELECT id, value1 as name 
				FROM {db_prefix}tp_variables 
				WHERE type = {string:type}',
				array(
					'type' => 'category'
				)
			);
			if($smcFunc['db_num_rows']($request) > 0)
			{
				while($row = $smcFunc['db_fetch_assoc']($request))
					$context['TPortal']['article_categories'][] = $row; 
				
				$smcFunc['db_free_result']($request);
			}
			// get all themes for selection
			$context['TPthemes'] = array();
			$request = $smcFunc['db_query']('', '
				SELECT th.value AS name, th.id_theme as ID_THEME, tb.value AS path
				FROM {db_prefix}themes AS th
				LEFT JOIN {db_prefix}themes AS tb ON th.ID_THEME = tb.ID_THEME
				WHERE th.variable = {string:thvar}
				AND tb.variable = {string:tbvar}
				AND th.id_member = {int:id_member}
				ORDER BY th.value ASC',
				array(
					'thvar' => 'name', 'tbvar' => 'images_url', 'id_member' => 0,
				)
			);
			if($smcFunc['db_num_rows']($request) > 0)
			{
				while ($row = $smcFunc['db_fetch_assoc']($request))
				{
					$context['TPthemes'][] = array(
						'id' => $row['ID_THEME'],
						'path' => $row['path'],
						'name' => $row['name']
					);
				}
				$smcFunc['db_free_result']($request);
			}
			$request = $smcFunc['db_query']('', '
				SELECT * FROM {db_prefix}tp_variables 
				WHERE type = {string:type} 
				ORDER BY value1 ASC',
				array(
					'type' => 'menus'
				)
			);
			$context['TPortal']['menus'] = array();
			$context['TPortal']['menus'][0] = array(
				'id' => 0, 
				'name' => 'Internal', 
				'var1' => '', 
				'var2' => ''
			);

			if($smcFunc['db_num_rows']($request) > 0)
			{
				while ($row = $smcFunc['db_fetch_assoc']($request))
				{
					$context['TPortal']['menus'][$row['id']] = array(
						'id' => $row['id'], 
						'name' => $row['value1'], 
						'var1' => $row['value2'], 
						'var2' => $row['value3']
					);
				}
			}
		}
		// if not throw an error
		else
			fatal_error($txt['tp-blockfailure']);
	}
	// or maybe adding it?
	elseif(isset($_GET['addblock']))
	{
		get_articles();
		// check which side its mean to be on
		$context['TPortal']['blockside'] = $_GET['addblock'];
	}
	else
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=blocks', $txt['tp-blocks']);
		foreach($panels as $p => $pan)
		{
			if(isset($_GET[$pan]))
				$context['TPortal']['panelside'] = $pan;
		}

		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_blocks 
			WHERE 1 ORDER BY bar, pos, id ASC',
			array()
		);
		if ($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
				$context['TPortal']['admin_'.$bars[$row['bar']].'block']['blocks'][] = array(
					'frame' => $row['frame'],
					'title' => $row['title'],
					'type' => (isset($blocktype[$row['type']]) ? $blocktype[$row['type']] : $row['type']),
					'body' => $row['body'],
					'id' => $row['id'],
					'access' => $row['access'],
					'pos' => $row['pos'],
					'off' => $row['off'],
					'visible' => $row['visible'],
					'var1' => $row['var1'],
					'var2' => $row['var2'],
					'lang' => $row['lang'],
					'access2' => $row['access2'],
					'loose' => $row['access2'] != '' ? true : false,
					'editgroups' => $row['editgroups']
				);

			$smcFunc['db_free_result']($request);
		}
	}
	get_articles();
	if($context['TPortal']['subaction']=='panels')
		TPadd_linktree($scripturl.'?action=tpadmin;sa=panels', $txt['tp-panels']);

	$context['html_headers'] .= '
	<script type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/editor.js?fin20"></script>
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
		
		window.onload = startToggle;
		
		function startToggle()
		{
			var img = document.getElementsByTagName("img");
			
			for(var i = 0; i < img.length; i++)
			{
				if (img[i].className == "toggleButton")
					img[i].onclick = toggleBlock;
			}
		}
		
		function toggleBlock(e)
		{ 
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;
		
			while(target.className != "toggleButton")
				  target = target.parentNode;
			
			var id = target.id.replace("blockonbutton", "");
			var Ajax = getXMLHttpRequest();
			
			Ajax.open("POST", "?action=tpadmin;blockon=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPactive1.gif" ? "' . $settings['tp_images_url'] . '/TPactive2.gif" : "' . $settings['tp_images_url'] . '/TPactive1.gif";
				}
			}
			
			var params = "?action=tpadmin;blockon=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'";
			Ajax.send(params);
		}

	// ]]></script>';
}

function do_menus()
{
	global $context, $scripturl, $smcFunc;
	
	$mid = isset($_GET['mid']) && is_numeric($_GET['mid']) ? $_GET['mid'] : 0;
	// first check any link stuff
	if(isset($_GET['linkon']))
	{
		checksession('get');
		$what = is_numeric($_GET['linkon']) ? $_GET['linkon'] : 0;

		if($what > 0)
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_variables 
				SET value5 = {int:val5}
				WHERE id = {int:varid}',
				array('val5' => 0, 'varid' => $what)
			);
		redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
	}
	elseif(isset($_GET['linkoff']))
	{
		checksession('get');
		$what = is_numeric($_GET['linkoff']) ? $_GET['linkoff'] : '0';

		if($what > 0)
			$smcFunc['db_query']('','
				UPDATE {db_prefix}tp_variables 
				SET value5 = {int:val5}
				WHERE id = {int:varid}',
				array('val5' => 1, 'varid' => $what)
			);

		redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
	}
	elseif(isset($_GET['linkdelete']))
	{
		checksession('get');
		$what = is_numeric($_GET['linkdelete']) ? $_GET['linkdelete'] : '0';

		if($what > 0)
			$smcFunc['db_query']('', '
				DELETE FROM {db_prefix}tp_variables
				WHERE id = {int:varid}',
				array('varid' => $what)
			);

		redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
	}

	$context['TPortal']['menubox'] = array();
	$context['TPortal']['editmenuitem'] = array();
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_variables 
		WHERE type = {string:type} 
		ORDER BY subtype + 0 ASC',
		array('type' => 'menubox')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			if($row['value5'] == '-1')
			{
				$p = 'off';
				$status = '1';
			}
			else
			{
				$status = '0';
				$p = $row['value5'];
			}
			$mtype = substr($row['value3'], 0, 4);
			$idtype = substr($row['value3'], 4);

			if($mtype != 'cats' && $mtype != 'arti' && $mtype != 'head' && $mtype != 'spac')
			{
				$mtype = 'link';
				$idtype = $row['value3'];
			}
			if($row['value2'] == '')
				$newlink = '0';
			else
				$newlink = $row['value2'];

			if($mtype == 'head')
			{
				$mtype = 'head';
				$idtype = $row['value1'];
			}

			$context['TPortal']['menubox'][$row['subtype2']][] = array(
				'id' => $row['id'],
				'menuID' => $row['subtype2'],
				'name' => $row['value1'],
				'pos' => $p,
				'type' => $mtype,
				'IDtype' => $idtype,
				'off' => $row['value5'],
				'sub' => $row['value4'],
				'subtype' => $row['subtype'],
				'newlink' => $newlink,
			);
			if ($context['TPortal']['subaction'] == 'linkmanager')
			{
				$menuid = $_GET['linkedit'];
				if($menuid == $row['id'])
					$context['TPortal']['editmenuitem'] = array(
						'id' => $row['id'],
						'menuID' => $row['subtype2'],
						'name' => $row['value1'],
						'pos' => $p,
						'type' => $mtype,
						'IDtype' => $idtype,
						'off' => $status,
						'sub' => $row['value4'],
						'subtype' => $row['subtype'],
						'newlink' => $newlink ,
					);
			}
		}
		$smcFunc['db_free_result']($request);
	}

	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_variables 
		WHERE type = {string:type} 
		ORDER BY value1 ASC',
		array('type' => 'menus')
	);
	$context['TPortal']['menus'] = array();
	$context['TPortal']['menus'][0] = array(
		'id' => 0, 
		'name' => 'Internal', 
		'var1' => '', 
		'var2' => ''
	);

	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$context['TPortal']['menus'][$row['id']] = array(
				'id' => $row['id'], 
				'name' => $row['value1'], 
				'var1' => $row['value2'], 
				'var2' => $row['value3']
			);
		}
	}

	get_articles();
	// collect categories
	$request = $smcFunc['db_query']('', '
		SELECT	id, value1 as name, value2 as parent 
		FROM {db_prefix}tp_variables
		WHERE type = {string:type}',
		array('type' => 'category')
	);
	
	$context['TPortal']['editcats']=array();
    $allsorted = array();
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$row['indent'] = 0;
			$allsorted[$row['id']] = $row;
		}
		$smcFunc['db_free_result']($request);
		if(count($allsorted) > 1)
			$context['TPortal']['editcats'] = chain('id', 'parent', 'name', $allsorted);
		else
			$context['TPortal']['editcats'] = $allsorted;
	}
	// add to linktree
	if(isset($_GET['mid']))
		TPadd_linktree($scripturl.'?action=tpadmin;sa=menubox;mid='. $_GET['mid'] , $context['TPortal']['menus'][$_GET['mid']]['name']);
	elseif(isset($_GET['linkedit']) && is_numeric($_GET['linkedit']))
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=menubox;mid='. $context['TPortal']['editmenuitem']['menuID'] , $context['TPortal']['menus'][$context['TPortal']['editmenuitem']['menuID']]['name']);
		TPadd_linktree($scripturl.'?action=tpadmin;linkedit='. $_GET['linkedit'] , $context['TPortal']['editmenuitem']['name']);
	}
}

// articles
function do_articles()
{
	global $context, $txt, $settings, $boardurl, $scripturl, $smcFunc;

	// do an update of stray articles and categories
	$acats = array();
	$request = $smcFunc['db_query']('', '
		SELECT id FROM {db_prefix}tp_variables 
		WHERE type = {string:type}',
		array('type' => 'category')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
			$acats[] = $row['id'];
		$smcFunc['db_free_result']($request);
	}
	if(count($acats) > 0)
	{
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_variables 
			SET value2 = {int:val2} 
			WHERE type = {string:type} 
			AND value2 NOT IN ({array_string:value2})',
			array('val2' => 0, 'type' => 'category', 'value2' => $acats)
		);
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_articles 
			SET category = {int:cat} 
			WHERE category NOT IN({array_int:category}) 
			AND category > 0',
			array('cat' => 0, 'category' => $acats)
		);
	}
	// first check any ajax stuff
	if(isset($_GET['arton']))
	{
		checksession('get');
		$what = is_numeric($_GET['arton']) ? $_GET['arton'] : '0';
		if($what > 0)
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_articles 
				SET off = IF(off = 0 , 1, 0) 
				WHERE id = {int:artid}',
				array('artid' => $what)
			);
		else
			return;
	}
	elseif(isset($_GET['artlock']))
	{
		checksession('get');
		$what = is_numeric($_GET['artlock']) ? $_GET['artlock'] : '0';
		if($what > 0)
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_articles 
				SET locked = IF(locked = 0 , 1, 0) 
				WHERE id = {int:artid}',
				array('artid' => $what)
			);
		else
			return;
	}
	elseif(isset($_GET['artsticky']))
	{
		checksession('get');
		$what = is_numeric($_GET['artsticky']) ? $_GET['artsticky'] : '0';
		if($what > 0)
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_articles 
				SET sticky = IF(sticky = 0 , 1, 0) 
				WHERE id = {int:artid}',
				array('artid' => $what)
			);
		else
			return;
	}
	elseif(isset($_GET['artfront']))
	{
		checksession('get');
		$what = is_numeric($_GET['artfront']) ? $_GET['artfront'] : '0';
		if($what > 0)
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_articles 
				SET frontpage = IF(frontpage = 0 , 1, 0) 
				WHERE id = {int:artid}',
				array('artid' => $what)
			);
		else
			return;
	}
	elseif(isset($_GET['artfeat']))
	{
		checksession('get');
		$what = is_numeric($_GET['artfeat']) ? $_GET['artfeat'] : '0';
		if($what > 0)
		{
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_articles 
				SET featured = IF(featured = 0, 1, 0) 
				WHERE id = {int:artid}',
				array('artid' => $what)
			);
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_articles 
				SET featured = {int:featured} 
				WHERE id != {int:artid}',
				array('featured' => 0, 'artid' => $what)
			);
		}
		else
			return;
	}
	elseif(isset($_GET['catdelete']))
	{
		checksession('get');
		$what = is_numeric($_GET['catdelete']) ? $_GET['catdelete'] : '0';
		if($what > 0)
		{
			// first get info
			$request = $smcFunc['db_query']('', '
				SELECT id, value2 FROM {db_prefix}tp_variables 
				WHERE id = {int:varid} LIMIT 1',
				array('varid' => $what)
			);
			$row = $smcFunc['db_fetch_assoc']($request);
			$smcFunc['db_free_result']($request);
			
			$newcat = !empty($row['value2']) ? $row['value2'] : 0;
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_variables 
				SET value2 = {int:val2} 
				WHERE value2 = {int:varid}',
				array(
					'val2' => $newcat, 'varid' => $what
				)
			);

			$smcFunc['db_query']('', '
				DELETE FROM {db_prefix}tp_variables 
				WHERE id = {int:varid}',
				array('varid' => $what)
			);
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_articles 
				SET category = {int:cat} 
				WHERE category = {int:catid}',
				array('cat' => $newcat, 'catid' => $what)
			);
			redirectexit('action=tpadmin;sa=categories');
		}
		else
			redirectexit('action=tpadmin;sa=categories');
	}
	elseif(isset($_GET['artdelete']))
	{
		checksession('get');
		$what = is_numeric($_GET['artdelete']) ? $_GET['artdelete'] : '0';
		$cu = is_numeric($_GET['cu']) ? $_GET['cu'] : '';
		if($cu == -1)
		{
			$strays=true;
			$cu = '';
		}
		if($what > 0) 
		{
			$smcFunc['db_query']('', '
				DELETE FROM {db_prefix}tp_articles 
				WHERE id = {int:artid}',
				array('artid' => $what)
			);
			$smcFunc['db_query']('', '
				DELETE FROM {db_prefix}tp_variables
				WHERE value5 = {int:artid}',
				array('artid' => $what)
			);
		}

		redirectexit('action=tpadmin' . (!empty($cu) ? ';cu='.$cu : '') . (isset($strays) ? ';sa=strays'.$cu : ';sa=articles'));
	}

	// for the non-category articles, do a count.
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) as total
		FROM {db_prefix}tp_articles
		WHERE category = 0 OR category = 9999'
	);

	$row = $smcFunc['db_fetch_assoc']($request);
	$context['TPortal']['total_nocategory'] = $row['total'];
	$smcFunc['db_free_result']($request);

	// for the submissions too
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) as total
		FROM {db_prefix}tp_articles
		WHERE approved = 0'
	);

	$row = $smcFunc['db_fetch_assoc']($request);
	$context['TPortal']['total_submissions'] = $row['total'];
	$smcFunc['db_free_result']($request);

	// we are on categories screen
	if(in_array($context['TPortal']['subaction'], array('categories', 'addcategory')))
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=categories', $txt['tp-categories']);
		// first check if we simply want to copy or set as child
		if(isset($_GET['cu']) && is_numeric($_GET['cu']))
		{
			$ccat = $_GET['cu'];
			if(isset($_GET['copy']))
			{
				$request = $smcFunc['db_query']('', '
					SELECT * FROM {db_prefix}tp_variables 
					WHERE id = {int:varid}',
					array('varid' => $ccat)
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					$row = $smcFunc['db_fetch_assoc']($request);
					$row['value1'] .= '__copy'; 
					$smcFunc['db_free_result']($request);
					$smcFunc['db_insert']('insert',
						'{db_prefix}tp_variables', 
						array(
							'value1' => 'string',
							'value2' => 'string',
							'value3' => 'string',
							'type' => 'string',
							'value4' => 'string',
							'value5' => 'int',
							'subtype' => 'string',
							'value7' => 'string',
							'value8' => 'string',
							'subtype2'=> 'int'						
						),
						array(
							$row['value1'],
							$row['value2'],
							$row['value3'],
							$row['type'],
							$row['value4'],
							$row['value5'],
							$row['subtype'],
							$row['value7'],
							$row['value8'],
							$row['subtype2']
						),
						array('id')
					);
				}
				redirectexit('action=tpadmin;sa=categories');
			}
			elseif(isset($_GET['child']))
			{
				$request = $smcFunc['db_query']('', '
					SELECT * FROM {db_prefix}tp_variables 
					WHERE id = {int:varid}',
					array('varid' => $ccat)
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					$row = $smcFunc['db_fetch_assoc']($request);
					$row['value1'] .= '__copy'; 
					$smcFunc['db_free_result']($request);
					$smcFunc['db_insert']('INSERT',
						'{db_prefix}tp_variables',
						array(
							'value1' => 'string',
							'value2' => 'string',
							'value3' => 'string',
							'type' => 'string',
							'value4' => 'string',
							'value5' => 'int',
							'subtype' => 'string',
							'value7' => 'string',
							'value8' => 'string',
							'subtype2'=> 'int'
						),
						array(
							$row['value1'],
							$row['id'],
							$row['value3'],
							$row['type'],
							$row['value4'],
							$row['value5'],
							$row['subtype'],
							$row['value7'],
							$row['value8'],
							$row['subtype2']
						),
						array('id')
					);
				}
				redirectexit('action=tpadmin;sa=categories');
			}
			// guess we only want the category then
			else
			{
				// get membergroups
				get_grps();
			$context['html_headers'] .= '
			<script type="text/javascript"><!-- // --><![CDATA[
				function changeIllu(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/illustrations/\' + name; 
				}

				function changeIcon(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/icons/\' + name; 
				}
			// ]]></script>';

				$request = $smcFunc['db_query']('', '
					SELECT * FROM {db_prefix}tp_variables 
					WHERE id = {int:varid} LIMIT 1',
					array('varid' => $ccat)
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					$row = $smcFunc['db_fetch_assoc']($request);
					$o = explode('|', $row['value7']);
					foreach($o as $t => $opt)
					{
						$b = explode('=', $opt); 
						if(isset($b[1]))
							$row[$b[0]] = $b[1];
					}
					$smcFunc['db_free_result']($request);
					$check = array('layout', 'catlayout', 'toppanel', 'bottompanel', 'leftpanel', 'rightpanel', 'upperpanel', 'lowerpanel', 'showchild');
					foreach($check as $c => $ch)
					{
						if(!isset($row[$ch]))
							$row[$ch] = 0;
					}
					$context['TPortal']['editcategory'] = $row;		
				}
				// fetch all categories and subcategories
				$request = $smcFunc['db_query']('', '
					SELECT	id, value1 as name, value2 as parent, value3, value4, 
						value5, subtype, value7, value8, subtype2 
					FROM {db_prefix}tp_variables
					WHERE type = {string:type}',
					array('type' => 'category')
				);
				
				$context['TPortal']['editcats'] = array();
				$allsorted = array();
				$alcats = array();
				if($smcFunc['db_num_rows']($request) > 0)
				{
					while ($row = $smcFunc['db_fetch_assoc']($request))
					{
						$row['indent'] = 0;
						$allsorted[$row['id']] = $row;
						$alcats[] = $row['id'];
					}
					$smcFunc['db_free_result']($request);
					if(count($allsorted) > 1)
						$context['TPortal']['editcats'] = chain('id', 'parent', 'name', $allsorted);
					else
						$context['TPortal']['editcats'] = $allsorted;
				}
				TPadd_linktree($scripturl.'?action=tpadmin;sa=categories;cu='. $ccat, $txt['tp-editcategory']);
			}
			return;
		}
		
		// fetch all categories and subcategories
		$request = $smcFunc['db_query']('', '
			SELECT id, value1 as name, value2 as parent, value3, value4,
				value5, subtype, value7, value8, subtype2 
			FROM {db_prefix}tp_variables
			WHERE type = {string:type}',
			array('type' => 'category')
		);
		
		$context['TPortal']['editcats'] = array();
		$allsorted = array();
		$alcats = array();
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$row['indent'] = 0;
				$allsorted[$row['id']] = $row;
				$alcats[] = $row['id'];
			}
			$smcFunc['db_free_result']($request);
			if(count($allsorted) > 1)
				$context['TPortal']['editcats'] = chain('id', 'parent', 'name', $allsorted);
			else
				$context['TPortal']['editcats'] = $allsorted;
		}
		// get the filecount as well
		if(count($alcats) > 0)
		{
			$request = $smcFunc['db_query']('', '
				SELECT	art.category as id, COUNT(art.id) as files 
				FROM {db_prefix}tp_articles as art
				WHERE art.category IN ({string:cats})
				GROUP BY art.category',
				array('cats' => implode(',', $alcats))
			);
			
			if($smcFunc['db_num_rows']($request) > 0)
			{
				$context['TPortal']['cats_count'] = array();
				while ($row = $smcFunc['db_fetch_assoc']($request))
					$context['TPortal']['cats_count'][$row['id']] = $row['files'];
				$smcFunc['db_free_result']($request);
			}
		}
		if($context['TPortal']['subaction'] == 'addcategory')
			TPadd_linktree($scripturl.'?action=tpadmin;sa=addcategory', $txt['tp-addcategory']);
		
		return;
	}
	TPadd_linktree($scripturl.'?action=tpadmin;sa=articles', $txt['tp-articles']);
	// are we inside a category?
	if(isset($_GET['cu']) && is_numeric($_GET['cu']))
	{
		$where = $_GET['cu'];
	}
	// show the no category articles?
	if(isset($_GET['sa']) && $_GET['sa'] == 'strays')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=strays', $txt['tp-strays']);
		$show_nocategory = true;
	}
	// submissions?
	if(isset($_GET['sa']) && $_GET['sa'] == 'submission')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=submission', $txt['tp-submissions']);
		$show_submission = true;
	}
	// single article?
	if(isset($_GET['sa']) && substr($_GET['sa'], 0, 11) == 'editarticle')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa='.$_GET['sa'], $txt['tp-editarticle']);
		$whatarticle = substr($_GET['sa'], 11);
	}
	// are we starting a new one?
	if(isset($_GET['sa']) && substr($_GET['sa'], 0, 11) == 'addarticle_')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa='.$_GET['sa'], $txt['tp-addarticle']);
		$context['TPortal']['editarticle'] = array(		
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
            'type' => substr($_GET['sa'],11),
            'featured' => 0,
            'realName' => $context['user']['name'],
            'authorID' => $context['user']['id'],
            'articletype' => substr($_GET['sa'],11),
            'ID_THEME' => 0,
			'pub_start' => 0,
			'pub_end' => 0,
        );
		$context['html_headers'] .= '
			<script type="text/javascript"><!-- // --><![CDATA[
				function changeIllu(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/illustrations/\' + name; 
				}

				function changeIcon(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/icons/\' + name; 
				}
			// ]]></script>';
		// Add in BBC editor before we call in template so the headers are there
		if(substr($_GET['sa'], 11) == 'bbc')
		{
			$context['TPortal']['editor_id'] = 'tp_article_body';
			TP_prebbcbox($context['TPortal']['editor_id']); 			
		}			
	}
	// fetch categories and subcategories
	if(!isset($show_nocategory))
	{
		$request = $smcFunc['db_query']('', '
			SELECT DISTINCT var.id as id, var.value1 as name, var.value2 as parent 
			FROM {db_prefix}tp_variables AS var
			WHERE var.type = {string:type}
			' . (isset($where) ? 'AND var.value2 = {int:whereval}' : '') . '
			ORDER BY parent, id DESC',
			array('type' => 'category', 'whereval' => isset($where) ? $where : 0)
		);
		
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$context['TPortal']['basecats'] = isset($where) ? array($where) : array('0', '9999');
			$cats = array();
			$context['TPortal']['cats'] = array(); 
			$sorted = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$sorted[$row['id']] = $row;
				$cats[] = $row['id'];
			}
			$smcFunc['db_free_result']($request);
			if(count($sorted) > 1)
				$context['TPortal']['cats'] = chain('id', 'parent', 'name', $sorted);
			else
				$context['TPortal']['cats'] = $sorted;
		}
	}

	if(isset($show_submission) && $context['TPortal']['total_submissions'] > 0)
	{
		// check if we have any start values
		$start = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'], array('date', 'id','author_id', 'type', 'subject', 'parse'))) ? $_GET['sort'] : 'date';
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpadmin;sa=submission;sort=' . $sort , $start, $context['TPortal']['total_submissions'], 15);
		$request = $smcFunc['db_query']('', '
			SELECT	art.id, art.date, art.frontpage, art.category, art.author_id as authorID, 
				IFNULL(mem.real_name, art.author) as author, art.subject, art.approved,
				art.sticky, art.type, art.featured, art.locked, art.off, art.parse as pos	
			FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			WHERE art.approved = {int:approved}
			ORDER BY art.{raw:col} {raw:sort}
			LIMIT {int:start}, 15',
			array(
				'approved' => 0,
				'col' => $sort,
				'start' => $start,
				'sort' => in_array($sort, array('sticky', 'locked', 'frontpage', 'date', 'active')) ? 'DESC' : 'ASC',				
			)
		);
								
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$context['TPortal']['arts_submissions']=array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$context['TPortal']['arts_submissions'][] = $row;
			}
			$smcFunc['db_free_result']($request);
		}
	}

	if(isset($show_nocategory) && $context['TPortal']['total_nocategory'] > 0)
	{
		// check if we have any start values
		$start = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'], array('off', 'date', 'id', 'author_id', 'locked', 'frontpage', 'sticky', 'featured', 'type', 'subject', 'parse'))) ? $_GET['sort'] : 'date';
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpadmin;sa=articles;sort=' . $sort , $start, $context['TPortal']['total_nocategory'], 15);
		$request = $smcFunc['db_query']('', '
			SELECT	art.id, art.date, art.frontpage, art.category, art.author_id as authorID, 
				IFNULL(mem.real_name, art.author) as author, art.subject, art.approved, art.sticky,
				art.type, art.featured,art.locked, art.off, art.parse as pos	
			FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			WHERE (art.category = 0 OR art.category = 9999)
			ORDER BY art.{raw:col} {raw:sort} 
			LIMIT {int:start}, 15',
			array(
				'col' => $sort,
				'sort' => in_array($sort, array('sticky', 'locked', 'frontpage', 'date', 'active')) ? 'DESC' : 'ASC',
				'start' => $start,				
			)
		);
		
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$context['TPortal']['arts_nocat'] = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$context['TPortal']['arts_nocat'][] = $row;
			}
			$smcFunc['db_free_result']($request);
		}
	}
	// ok, fetch single article
	if(isset($whatarticle))
	{	
		$request = $smcFunc['db_query']('', '
			SELECT	art.*, IFNULL(mem.real_name, art.author) as realName, art.author_id as authorID,
				art.type as articletype, art.id_theme as ID_THEME 
			FROM {db_prefix}tp_articles as art
			LEFT JOIN {db_prefix}members as mem ON (art.author_id = mem.id_member)
			WHERE art.id = {int:artid}',
			array(
				'artid' => is_numeric($whatarticle) ? $whatarticle : 0,
			)
		);
		
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$context['TPortal']['editarticle'] = $smcFunc['db_fetch_assoc']($request);
			$context['TPortal']['editing_article'] = true;
			$context['TPortal']['editarticle']['body'] = $smcFunc['htmlspecialchars']($context['TPortal']['editarticle']['body'], ENT_QUOTES);
			$smcFunc['db_free_result']($request);
		}
		
		// Add in BBC editor before we call in template so the headers are there
		if($context['TPortal']['editarticle']['articletype'] == 'bbc')
		{
			$context['TPortal']['editor_id'] = 'tp_article_body';
			TP_prebbcbox($context['TPortal']['editor_id'], strip_tags($context['TPortal']['editarticle']['body'])); 			
		}
		
		// fetch the WYSIWYG value
		$request = $smcFunc['db_query']('', '
			SELECT value1 FROM {db_prefix}tp_variables 
			WHERE subtype2 = {int:subtype}  
			AND type = {string:type} LIMIT 1',
			array(
				'subtype' => $whatarticle, 'type' => 'editorchoice',
			)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			$smcFunc['db_free_result']($request);
			$context['TPortal']['editorchoice'] = $row['value1'];
		}
		else
			$context['TPortal']['editorchoice'] = 1;
		
		$context['html_headers'] .= '
			<script type="text/javascript"><!-- // --><![CDATA[
				function changeIllu(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/illustrations/\' + name; 
				}

				function changeIcon(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/icons/\' + name; 
				}
			// ]]></script>';

	}
	// fetch article count for these
	if(isset($cats))
	{
		$request = $smcFunc['db_query']('', '
			SELECT	art.category as id, COUNT(art.id) as files 
			FROM {db_prefix}tp_articles as art
			WHERE art.category IN ({array_int:cat})
			GROUP BY art.category',
			array('cat' => $cats)
		);
		
		$context['TPortal']['cats_count'] = array();
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$context['TPortal']['cats_count'][$row['id']] = $row['files'];
			$smcFunc['db_free_result']($request);
		}
	}
	// get the icons needed
	tp_collectArticleIcons();

	// fetch all categories and subcategories
	$request = $smcFunc['db_query']('', '
		SELECT	id, value1 as name, value2 as parent 
		FROM {db_prefix}tp_variables
		WHERE type = {string:type}',
		array('type' => 'category')
	);
	
	$context['TPortal']['allcats'] = array();
	$allsorted = array();
	
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$allsorted[$row['id']] = $row;

		$smcFunc['db_free_result']($request);
		if(count($allsorted) > 1)
			$context['TPortal']['allcats'] = chain('id', 'parent', 'name', $allsorted);
		else
			$context['TPortal']['allcats'] = $allsorted;
	}
	// not quite done yet lol, now we need to sort out if articles are to be listed
	if(isset($where))
	{
		// check if we have any start values
		$start = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'], array('off', 'date', 'id', 'author_id' , 'locked', 'frontpage', 'sticky', 'featured', 'type', 'subject', 'parse'))) ? $_GET['sort'] : 'date';
		$context['TPortal']['categoryID'] = $where;
		// get the name
		$request = $smcFunc['db_query']('', '
			SELECT value1 
			FROM {db_prefix}tp_variables 
			WHERE id = {int:varid} LIMIT 1',
			array(
				'varid' => $where
			)
		);
		$f = $smcFunc['db_fetch_assoc']($request);
		$smcFunc['db_free_result']($request);
		$context['TPortal']['categoryNAME'] = $f['value1'];
		// get the total first
		$request = $smcFunc['db_query']('', '
			SELECT	COUNT(*) as total
			FROM {db_prefix}tp_articles
			WHERE category = {int:cat}',
			array(
				'cat' => $where
			)
		);

		$row = $smcFunc['db_fetch_assoc']($request);
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpadmin;sa=articles;sort=' . $sort . ';cu=' . $where, $start, $row['total'], 15);
		$smcFunc['db_free_result']($request);

		$request = $smcFunc['db_query']('', '
			SELECT art.id, art.date, art.frontpage, art.category, art.author_id as authorID,
				IFNULL(mem.real_name, art.author) as author, art.subject, art.approved, art.sticky,
				art.type, art.featured, art.locked, art.off, art.parse as pos	
			FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			WHERE art.category = {int:cat}
			ORDER BY art.{raw:sort} {raw:sorter} 
			LIMIT {int:start}, 15',
			array('cat' => $where,
				'sort' => $sort,
				'sorter' => in_array($sort, array('sticky', 'locked', 'frontpage', 'date', 'active')) ? 'DESC' : 'ASC',
				'start' => $start
			)
		);
		TPadd_linktree($scripturl.'?action=tpadmin;sa=articles;cu='.$where, $txt['tp-blocktype19']);
		
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$context['TPortal']['arts']=array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$context['TPortal']['arts'][] = $row;
			}
			$smcFunc['db_free_result']($request);
		}
	}
	$context['html_headers'] .= '
	<script type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/editor.js?rc1"></script>
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
		
		window.onload = startToggle;
		
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
			
			Ajax.open("POST", "?action=tpadmin;arton=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPactive2.gif" ? "' . $settings['tp_images_url'] . '/TPactive1.gif" : "' . $settings['tp_images_url'] . '/TPactive2.gif";
				}
			}
			
			var params = "?action=tpadmin;arton=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'";
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
			
			Ajax.open("POST", "?action=tpadmin;artfront=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPfront.gif" ? "' . $settings['tp_images_url'] . '/TPfront2.gif" : "' . $settings['tp_images_url'] . '/TPfront.gif";
				}
			}
			
			var params = "?action=tpadmin;artfront=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'";
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
			
			Ajax.open("POST", "?action=tpadmin;artsticky=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPsticky1.gif" ? "' . $settings['tp_images_url'] . '/TPsticky2.gif" : "' . $settings['tp_images_url'] . '/TPsticky1.gif";
				}
			}
			
			var params = "?action=tpadmin;artsticky=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'";
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
			
			Ajax.open("POST", "?action=tpadmin;artlock=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPlock1.gif" ? "' . $settings['tp_images_url'] . '/TPlock2.gif" : "' . $settings['tp_images_url'] . '/TPlock1.gif";
				}
			}
			
			var params = "?action=tpadmin;artlock=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'";
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
					aP[i].src=\'' . $settings['tp_images_url'] . '/TPflag2.gif\';
				}
			}

			
			while(target.className != "toggleFeatured")
				  target = target.parentNode;
			
			var id = target.id.replace("artFeatured", "");
			var Ajax = getXMLHttpRequest();
			
			Ajax.open("POST", "?action=tpadmin;artfeat=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPflag.gif" ? "' . $settings['tp_images_url'] . '/TPflag2.gif" : "' . $settings['tp_images_url'] . '/TPflag.gif";
				}
			}
			
			var params = "?action=tpadmin;artfeat=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'";
			Ajax.send(params);
		}
	// ]]></script>';
	
	if($context['TPortal']['subaction'] == 'artsettings')
		TPadd_linktree($scripturl.'?action=tpadmin;sa=artsettings', $txt['tp-settings']);
	elseif($context['TPortal']['subaction'] == 'articons')
		TPadd_linktree($scripturl.'?action=tpadmin;sa=articons', $txt['tp-adminicons']);

}

function do_modules()
{
	global $context, $txt, $scripturl, $smcFunc;
	
	isAllowedTo('tp_settings');
	
	$context['TPortal']['adm_modules'] = array();
		// fetch modules
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_modules 
			WHERE 1',
			array()
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$context['TPortal']['adm_modules'][] = $row;
			$smcFunc['db_free_result']($request);
		}
		$context['TPortal']['internal_modules'][] = array(
			'adminlink' => '<a href="'.$scripturl.'?action=tpmod;dl=admin">'.$txt['tp-mod-dladmin'].'</a>',
			'modulelink' => '<a href="'.$scripturl.'?action=tpmod;dl=0">'.$txt['tp-mod-dlmanager'].'</a>',
			'state' => $context['TPortal']['show_download'],
			'fieldname' => 'tp_show_download',
		);
}

function do_news($tpsub = 'overview')
{
	global $context, $txt, $scripturl;
	
	get_boards();
	$context['TPortal']['SSI_boards'] = explode(',', $context['TPortal']['SSI_board']);
	
	if($tpsub == 'overview')
	{
		if(!TPcheckAdminAreas())
			fatal_error($txt['tp-notallowed']);
	}
	elseif($tpsub == 'permissions')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=permissions', $txt['tp-permissions']);
		$context['TPortal']['perm_all_groups'] = get_grps(true, true);
		$context['TPortal']['perm_groups'] = tp_fetchpermissions($context['TPortal']['modulepermissions']);
	}
	else
	{
		if($tpsub == 'news')
			TPadd_linktree($scripturl.'?action=tpadmin;sa=news', $txt['news']);
		elseif($tpsub == 'settings')
			TPadd_linktree($scripturl.'?action=tpadmin;sa=settings', $txt['tp-settings']);
		elseif($tpsub == 'frontpage')
			TPadd_linktree($scripturl.'?action=tpadmin;sa=frontpage', $txt['tp-frontpage']);

		isAllowedTo('tp_settings');
	}
}

function do_postchecks()
{
	global $context, $txt, $settings, $boarddir, $smcFunc, $sourcedir;

	// If we have any setting changes add them to this array
	$updateArray = array();
	
	// which screen do we come from?
	if(!empty($_POST['tpadmin_form']))
	{
		// get it
		$from = $_POST['tpadmin_form'];
		//news
		if($from == 'news')
			return 'news';
		// block permissions overview
		elseif($from == 'blockoverview')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			$block = array();
			foreach($_POST as $what => $value)
			{
				if(substr($what, 5, 7) == 'tpblock')
				{
					// get the id
					$bid = substr($what, 12);
					if(!isset($block[$bid]))
						$block[$bid] = array();

					if($value != 'control' && !in_array($value, $block[$bid]))
						$block[$bid][] = $value;
				}
			}
			foreach($block as $bl => $blo)
			{
				$request = $smcFunc['db_query']('', '
					SELECT access FROM {db_prefix}tp_blocks 
					WHERE id = {int:blockid}',
					array('blockid' => $bl)
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					$row = $smcFunc['db_fetch_assoc']($request);
					$smcFunc['db_free_result']($request);
					$request = $smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks 
						SET access = {string:access} WHERE id = {int:blockid}',
						array(
							'access' => implode(',', $blo),
							'blockid' => $bl,
						)
					);
				}
			}
			return 'blocks;overview';
		}
		elseif(in_array($from, array('settings', 'frontpage', 'artsettings', 'panels')))
		{
			checkSession('post');
			isAllowedTo('tp_settings');
			$w = array();
			$ssi = array();
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 3) == 'tp_')
				{
					$where = substr($what, 3);
					$clean = $value;
					// for frontpage, do some extra
					if($from == 'frontpage')
					{
						if(substr($what, 0, 20) == 'tp_frontpage_visual_')
						{
							$w[] = substr($what, 20);
							unset($clean);
						}
						elseif(substr($what, 0, 21) == 'tp_frontpage_usorting')
						{
							$w[] = 'sort_'.$value;
							unset($clean);
						}
						elseif(substr($what, 0, 26) == 'tp_frontpage_sorting_order')
						{
							$w[] = 'sortorder_'.$value;
							unset($clean);
						}
						// SSI boards
						elseif(substr($what, 0, 11) == 'tp_ssiboard')
						{
							if($value != 0)
								$ssi[$value] = $value;
						}
					}
					if($from == 'settings' && $what == 'tp_frontpage_title')
						$updateArray['frontpage_title'] = $clean;
					else
					{
						if(isset($clean))
							$updateArray[$where] = $clean;
					}
				}
			}
			// check the frontpage visual setting..
			if($from == 'frontpage')
			{
				$updateArray['frontpage_visual'] = implode(',', $w);
				$updateArray['SSI_board'] = implode(',', $ssi);
			}
			updateTPSettings($updateArray);
			return $from;
		}
		// categories
		elseif($from == 'categories')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 3) == 'tp_')
				{
					// for frontpage, do some extra
					if($from == 'categories')
					{
						if(substr($what, 0, 19) == 'tp_category_value2_')
						{
							$where = tp_sanitize(substr($what, 19));
							//make sure parent are not its own parent
							$request = $smcFunc['db_query']('', '
								SELECT value2 FROM {db_prefix}tp_variables 
								WHERE id = {string:varid} LIMIT 1',
								array(
									'varid' => $value
								)
							);
							$row = $smcFunc['db_fetch_assoc']($request);
							$smcFunc['db_free_result']($request);
							if($row['value2'] == $where)
								$smcFunc['db_query']('', '
									UPDATE {db_prefix}tp_variables 
									SET value2 = {string:val2}
									WHERE id = {string:varid} LIMIT 1',
									array(
										'val2' => '0',
										'varid' => $value,
									)
								);

							$smcFunc['db_query']('', '
								UPDATE {db_prefix}tp_variables 
								SET value2 = {string:val2}
								WHERE id = {string:varid} LIMIT 1',
								array(
									'val2' => $value,
									'varid' => $where,
								)
							);
						}
					}
				}
			}
			return $from;
		}
		// articles
		elseif($from == 'articles')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 14) == 'tp_article_pos')
				{
					$where = tp_sanitize(substr($what, 14));
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET parse = {int:parse} 
							WHERE id = {int:artid} LIMIT 1',
							array(
								'parse' => $value,
								'artid' => $where,
							)
						);
				}
			}
			if(isset($_POST['tpadmin_form_category']) && is_numeric($_POST['tpadmin_form_category']))
				return $from.';cu=' . $_POST['tpadmin_form_category'];
			else
				return $from;
		}
		// modules
		elseif($from == 'modules')
		{
			checkSession('post');
			isAllowedTo('tp_settings');
			
			foreach($_POST as $what => $value)
			{
				if($what == 'tp_show_download')
					$updateArray['show_download'] = $value;
				elseif(substr($what, 0, 14) == 'tpmodule_state')
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_modules 
						SET active = {int:active} 
						WHERE id = {int:modid}',
						array(
							'active' => $value,
							'modid' => substr($what, 14),
						)
					);
			}
			updateTPSettings($updateArray);
				
			return $from;
		}
		// all the items
		elseif($from == 'menuitems')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
	
			$all = explode(',', $context['TPortal']['sitemap_items']);
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 8) == 'menu_pos')
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables 
						SET subtype = {string:subtype} 
						WHERE id = {int:varid}',
						array(
							'subtype' => tp_sanitize($value),
							'varid' => substr($what, 8),
						)
					);
				elseif(substr($what, 0, 8) == 'menu_sub')
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables 
						SET value4 = {string:val4} 
						WHERE id = {int:varid}',
						array(
							'val4' => tp_sanitize($value),
							'varid' => substr($what, 8),
						)
					);
				elseif(substr($what, 0, 15) == 'tp_menu_sitemap')
				{
					$new = substr($what, 15);
					if($value == 0 && in_array($new, $all))
					{
						foreach ($all as $key => $value) 
						{
							if ($all[$key] == $new) 
								unset($all[$key]);
						}
					}
					elseif($value == 1 && !in_array($new, $all))
						$all[] = $new;

					$updateArray['sitemap_items'] = implode(',', $all);
				}
			}
			updateTPSettings($updateArray);
				
			redirectexit('action=tpadmin;sa=menubox;mid='. $_POST['tp_menuid']);
		}
		// all the menus
		elseif($from == 'menus')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 12) == 'tp_menu_name')
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables 
						SET value1 = {string:val1} 
						WHERE id = {int:varid}',
						array(
							'val1' => tp_sanitize($value),
							'varid' => substr($what, 12),
						)
					);
			}
			redirectexit('action=tpadmin;sa=menubox');
		}
		elseif($from == 'singlemenuedit')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			$where = isset($_POST['tpadmin_form_id']) ? $_POST['tpadmin_form_id'] : 0;

			foreach($_POST as $what => $value)
			{
				if($what == 'tp_menu_name')
				{
					// make sure special charachters can't be done
					$value = preg_replace('~&#\d+$~', '', $value);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables 
						SET value1 = {string:val1} 
						WHERE id = {int:varid}',
						array(
							'val1' => $value,
							'varid' => $where,
						)
					);
				}
				elseif($what == 'tp_menu_newlink')
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables 
						SET value2 = {string:var2} 
						WHERE id = {int:varid}',
						array(
							'var2' => $value,
							'varid' => $where,
						)
					);
				elseif($what == 'tp_menu_menuid')
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables 
						SET subtype2 = {int:subtype2} 
						WHERE id = {int:varid}',
						array(
							'subtype2' => $value,
							'varid' => $where,
						)
					);
				elseif($what == 'tp_menu_type')
				{
					if($value == 'cats')
						$idtype = 'cats'.$_POST['tp_menu_category'];
					elseif($value == 'arti')
						$idtype = 'arti'.$_POST['tp_menu_article'];
					elseif($value == 'link')
						$idtype = $_POST['tp_menu_link'];
					elseif($value == 'head')
						$idtype = 'head';
					elseif($value == 'spac')
						$idtype = 'spac';

					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables 
						SET value3 = {string:val3} 
						WHERE id = {int:varid}',
						array(
							'val3' => $idtype,
							'varid' => $where,
						)
					);
				}
				elseif($what == 'tp_menu_sub')
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables
						SET value4 = {string:val4}
						WHERE id = {int:varid}',
						array(
							'val4' => $value,
							'varid' => $where,
						)
					);
				elseif(substr($what, 0, 15) == 'tp_menu_newlink')
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables 
						SET value2 =  
						WHERE id = {int:varid}',
						array(
							'val2' => $value,
							'varid' => $where,
						)
					);
			}
			redirectexit('action=tpadmin;linkedit='.$where.';' . $context['session_var'] . '=' . $context['session_id']);
		}
		// add a category
		elseif($from == 'addcategory')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			$name = !empty($_POST['tp_cat_name']) ? $_POST['tp_cat_name'] : $txt['tp-noname'];
			$parent = !empty($_POST['tp_cat_parent']) ? $_POST['tp_cat_parent'] : '0';

			$smcFunc['db_insert']('INSERT',
				'{db_prefix}tp_variables',
				array(
					'value1' => 'string',
					'value2' => 'string',
					'value3' => 'string',
					'type' => 'string',
					'value4' => 'string',
					'value5' => 'int',
					'subtype' => 'string',
					'value7' => 'string',
					'value8' => 'string',
					'subtype2'=> 'int'
				),
				array(strip_tags($name), $parent, '', 'category', '', 0, '', 'catlayout=1|layout=1', 0, 0),
				array('id')
			);
			
			$go = $smcFunc['db_insert_id']('{db_prefix}tp_variables', 'id');
			redirectexit('action=tpadmin;sa=categories;cu='.$go);
		}
		// the categort list
		elseif($from == 'clist')
		{
			checkSession('post');
			isAllowedTo('tp_articles');

			$cats = array(); 
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 8) == 'tp_clist')
					$cats[] = $value;
			}
			if(sizeof($cats) > 0)
				$catnames = implode(',', $cats);
			else
				$catnames = '';
				
			$updateArray['cat_list'] = $catnames;
			
			updateTPSettings($updateArray);
				
			return $from;
		}

		// edit a category
		elseif($from == 'editcategory')
		{
			checkSession('post');
			isAllowedTo('tp_articles');

			$options = array(); 
			$groups = array();
			$where = $_POST['tpadmin_form_id'];
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 3) == 'tp_')
				{
					$clean = tp_sanitize($value);
					$param = substr($what, 12);
					if(in_array($param, array('value5', 'value6', 'value8')))
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_variables 
							SET '. $param .' = {string:val} 
							WHERE id = {int:varid} LIMIT 1',
							array('val' => $value, 'varid' => $where)
						);
					// parents needs some checking..
					elseif($param == 'value2')
					{
						//make sure parent are not its own parent
						$request = $smcFunc['db_query']('', '
							SELECT value2 FROM {db_prefix}tp_variables 
							WHERE id = {int:varid} LIMIT 1',
							array('varid' => $value)
						);
						$row = $smcFunc['db_fetch_assoc']($request);
						$smcFunc['db_free_result']($request);
						if($row['value2'] == $where)
							$smcFunc['db_query']('', '
								UPDATE {db_prefix}tp_variables 
								SET value2 = {string:val2} 
								WHERE id = {int:varid} LIMIT 1',
								array('val2' => '0', 'varid' => $value)
							);

						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_variables 
							SET value2 = {string:val2} 
							WHERE id = {int:varid} LIMIT 1',
							array('val2' => $value, 'varid' => $where)
						);
					}
					elseif($param == 'value1')
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_variables 
							SET value1 = {string:val1} 
							WHERE id = {int:varid} LIMIT 1',
							array('val1' => strip_tags($value), 'varid' => $where)
						);							
					elseif($param == 'value4')
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_variables 
							SET value4 = {string:val4} 
							WHERE id = {int:varid} LIMIT 1',
							array('val4' => $value, 'varid' => $where)
						);
					elseif($param == 'value9')
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_variables 
							SET value9 = {string:val9} 
							WHERE id = {int:varid} LIMIT 1',
							array('val9' => $value, 'varid' => $where)
						);
					elseif(substr($param, 0, 6) == 'group_')
						$groups[] = substr($param, 6);
					else
						$options[] = $param. '=' . $value;
				}
			}
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_variables 
				SET value3 = {string:val3}, value7 = {string:val7} 
				WHERE id = {int:varid} LIMIT 1',
				array('val3' => implode(',', $groups), 'val7' => implode('|', $options), 'varid' => $where)
			);
			$from = 'categories;cu=' . $where;
			return $from;
		}
		// stray articles
		elseif($from == 'strays')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			$ccats = array();
			// check if we have some values
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 16) == 'tp_article_stray')
					$ccats[] = substr($what, 16);
				elseif($what == 'tp_article_cat')
					$straycat = $value;
				elseif($what == 'tp_article_new')
					$straynewcat = $value;
			}	
			// update
			if(isset($straycat) && sizeof($ccats) > 0)
			{
				$category = $straycat;
				if($category == 0 && !empty($straynewcat))
				{
					$request = $smcFunc['db_insert']('INSERT',
						'{db_prefix}tp_variables',
						array('value1' => 'string', 'value2' => 'string', 'type' => 'string'),
						array(strip_tags($straynewcat), '0', 'category'),
						array('id')
					);
					
					$newcategory = $smcFunc['db_insert_id']('{db_prefix}tp_variables', 'id');
					$smcFunc['db_free_result']($request);
				}
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_articles 
					SET category = {int:cat} 
					WHERE id IN ({array_int:artid})',
					array(
						'cat' => !empty($newcategory) ? $newcategory : $category,
						'artid' => $ccats,
					)
				);
			}
			return $from;
		}
		// from articons...
		elseif($from == 'articons')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			// any icons sent?
			if(file_exists($_FILES['tp_article_newicon']['tmp_name']))
				TPuploadpicture('tp_article_newicon', '', '300', 'jpg,gif,png', 'tp-files/tp-articles/icons');

			if(file_exists($_FILES['tp_article_newillustration']['tmp_name']))
			{
				$name = TPuploadpicture('tp_article_newillustration', '', '500', 'jpg,gif,png', 'tp-files/tp-articles/illustrations');
				tp_createthumb('tp-files/tp-articles/illustrations/'. $name, 128, 128, 'tp-files/tp-articles/illustrations/s_'. $name);
				unlink('tp-files/tp-articles/illustrations/'. $name);
			}
			// how about deleted?
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 7) == 'articon')
					unlink($boarddir.'/tp-files/tp-articles/icons/'.$value);
				elseif(substr($what, 0, 15) == 'artillustration')
					unlink($boarddir.'/tp-files/tp-articles/illustrations/'.$value);
			}
			return $from;
		}
		// adding a full menu.
		elseif($from == 'menuadd')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
				
			if(!empty($_POST['tp_menu_title']))
			{
				$mtitle = strip_tags($_POST['tp_menu_title']);
				$smcFunc['db_insert']('INSERT', 
					'{db_prefix}tp_variables',
					array('value1' => 'string', 'type' => 'string'),
					array($mtitle, 'menus'),
					array('id')
				);
				redirectexit('action=tpadmin;sa=menubox');
			}
		}
		// adding a menu item.
		elseif($from == 'menuaddsingle')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
				
			$mid = $_POST['tp_menu_menuid'];
			$mtitle = strip_tags($_POST['tp_menu_title']);
			if($mtitle == '')
				$mtitle = $txt['tp-no_title'];
			
			$mtype = $_POST['tp_menu_type'];
			$mcat = isset($_POST['tp_menu_category']) ? $_POST['tp_menu_category'] : '';
			$mart = isset($_POST['tp_menu_article']) ? $_POST['tp_menu_article'] : '';
			$mlink = isset($_POST['tp_menu_link']) ? $_POST['tp_menu_link'] : ''; 
			$mhead = isset($_POST['tp_menu_head']) ? $_POST['tp_menu_head'] : ''; 
			$mnewlink = isset($_POST['tp_menu_newlink']) ? $_POST['tp_menu_newlink'] : '0';

			if($mtype == 'cats')
				$mtype = 'cats'.$mcat;
			elseif($mtype == 'arti')
				$mtype = 'arti'.$mart;
			elseif($mtype == 'head')
				$mtype = 'head'.$mhead;
			elseif($mtype == 'spac')
				$mtype = 'spac';
			else
				$mtype = $mlink;

			$msub = $_POST['tp_menu_sub'];
			$smcFunc['db_insert']('INSERT',
				'{db_prefix}tp_variables',
				array(
					'value1' => 'string',
					'value2' => 'string',
					'value3' => 'string',
					'type' => 'string',
					'value4' => 'string',
					'value5' => 'int',
					'subtype2'=> 'int'
				),
				array($mtitle, $mnewlink, $mtype, 'menubox', $msub, -1, $mid),
				array('id')
			);

			redirectexit('action=tpadmin;sa=menubox;mid='.$mid);
		}
		// submitted ones
		elseif($from == 'submission')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			$ccats = array();
			// check if we have some values
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 21) == 'tp_article_submission')
					$ccats[] = substr($what,21);
				elseif($what == 'tp_article_cat')
					$straycat = $value;
				elseif($what == 'tp_article_new')
					$straynewcat = $value;
			}	
			// update
			if(isset($straycat) && sizeof($ccats) > 0)
			{
				$category = $straycat;
				if($category == 0 && !empty($straynewcat))
				{
					$request = $smcFunc['db_insert']('INSERT',
						'{db_prefix}tp_variables',
						array(
							'value1' => 'string',
							'value2' => 'string',
							'type' => 'string',
						),
						array($straynewcat, '0', 'category'),
						array('id')
					);

					$newcategory = $smcFunc['db_insert_id']('{db_prefix}tp_variables', 'id');
					$smcFunc['db_free_result']($request);
				}
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_articles 
					SET approved = {int:approved}, category = {int:cat} 
					WHERE id IN ({array_int:artid})',
					array(
						'approved' => 1,
						'cat' => !empty($newcategory) ? $newcategory : $category,
						'artid' => $ccats,
					)
				);
				$smcFunc['db_query']('', '
					DELETE FROM {db_prefix}tp_variables 
					WHERE type = {string:type} 
					AND value5 IN ({array_int:val5})',
					array(
						'type' => 'art_not_approved',
						'val5' => $ccats,
					)
				);
			}
			return $from;
		}
		// from blocks screen
		elseif($from == 'blocks')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 3) == 'pos')
				{
					$where = substr($what, 3);
					if(is_numeric($where))
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_blocks 
							SET pos = {int:pos} 
							WHERE id = {int:blockid}',
							array(
								'pos' => $value,
								'blockid' => $where
							)
						);
				}
				elseif(substr($what, 0, 6) == 'addpos')
				{
					$where = substr($what, 6);
					if(is_numeric($where))
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_blocks 
							SET pos = (pos + 11) 
							WHERE id = {int:blockid}',
							array(
								'blockid' => $where
							)
						);
				}
				elseif(substr($what, 0, 6) == 'subpos')
				{
					$where = substr($what, 6);
					if(is_numeric($where))
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_blocks SET pos = (pos - 11) 
							WHERE id = {int:blockid}',
							array(
								'blockid' => $where
							)
						);
				}
				elseif(substr($what, 0, 4) == 'type')
				{
					$where = substr($what, 4);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks 
						SET type = {int:type} 
						WHERE id = {int:blockid}',
						array(
							'type' => $value,
							'blockid' => $where,
						)
					);
				}
				elseif(substr($what, 0, 5) == 'title')
				{
					$where = strip_tags(substr($what, 5));
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks 
						SET title = {string:title} 
						WHERE id = {int:blockid}',
						array(
							'title' => $value,
							'blockid' => $where,
						)
					);
				}
				elseif(substr($what, 0, 9) == 'blockbody')
				{
					$where = tp_sanitize(substr($what, 9));
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks 
						SET body = {string:body} 
						WHERE id = {int:blockid}',
						array(
							'body' => $value,
							'blockid' => $where,
						)
					);
				}
			}
			redirectexit('action=tpadmin;sa=blocks');
		}
		// from editing block
		elseif($from == 'addblock')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			$title = empty($_POST['tp_addblocktitle']) ? '-no title-' : ($_POST['tp_addblocktitle']);
			$panel = $_POST['tp_addblockpanel'];
			$type = $_POST['tp_addblock'];
			if(!is_numeric($type))
			{
				if(substr($type, 0, 3) == 'mb_')
				{
					$request = $smcFunc['db_query']('', '
						SELECT * FROM {db_prefix}tp_blocks 
						WHERE id = {int:blockid}',
						array(
							'blockid' => substr($type, 3)
						)
					);
					if($smcFunc['db_num_rows']($request) > 0)
					{
						$cp = $smcFunc['db_fetch_assoc']($request);
						$smcFunc['db_free_result']($request);
					}
				}
				else
					$od = TPparseModfile(file_get_contents($boarddir . '/tp-files/tp-blockcodes/' . $type.'.blockcode') , array('code')); 
			}
			if(isset($od['code']))
			{
				$body = tp_convertphp($od['code']);
				$type = 10;
			}
			else
				$body = '';

			if(isset($cp))
				$smcFunc['db_insert']('INSERT',
					'{db_prefix}tp_blocks',
					array(
						'type' => 'int',
						'frame' => 'string',
						'title' => 'string',
						'body' => 'string',
						'access' => 'string',
						'bar' => 'int',
						'pos' => 'int',
						'off' => 'int',
						'visible' => 'string',
						'var1' => 'int',
						'var2' => 'int',
						'lang' => 'string',
						'access2' => 'string',
						'editgroups' => 'string',
					),
					array(
						$cp['type'],
						$cp['frame'],
						$title,
						$cp['body'],
						$cp['access'], 
						$panel, 
						0, 
						1,
						1,
						$cp['var1'],
						$cp['var2'],
						$cp['lang'],
						$cp['access2'],
						$cp['editgroups']
					),
					array('id')
				); 
			else
				$smcFunc['db_insert']('INSERT',
					'{db_prefix}tp_blocks',
					array(
						'type' => 'int',
						'frame' => 'string',
						'title' => 'string',
						'body' => 'string',
						'access' => 'string',
						'bar' => 'int',
						'pos' => 'int',
						'off' => 'int',
						'visible' => 'string',
						'var1' => 'int',
						'var2' => 'int',
						'lang' => 'string',
						'access2' => 'string',
						'editgroups' => 'string',
					),
					array(
						$type, 'theme', $title, $body, '-1,0,1', $panel, 0, 1, 1, 0, 0, '', 'actio=allpages', '',
					),
					array('id')
				);

			$where = $smcFunc['db_insert_id']('{db_prefix}tp_blocks', 'id');	
			if(!empty($where))
				redirectexit('action=tpadmin;blockedit='.$where.';sesc='. $context['session_id']);
			else
				redirectexit('action=tpadmin;sa=blocks');
		}
		// from editing block
		elseif($from == 'blockedit')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			$where = is_numeric($_POST['tpadmin_form_id']) ? $_POST['tpadmin_form_id'] : 0;
			$tpgroups = array();
			$editgroups = array();
			$access = array();
			$lang = array(); 
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 9) == 'tp_block_')
				{
					$setting = substr($what, 9);

					if($setting == 'body')
					{
						// If we came from WYSIWYG then turn it back into BBC regardless.
						if (!empty($_REQUEST['tp_block_body_mode']) && isset($_REQUEST['tp_block_body']))
						{
							require_once($sourcedir . '/Subs-Editor.php');
							$_REQUEST['tp_block_body'] = html_to_bbc($_REQUEST['tp_block_body']);
							// We need to unhtml it now as it gets done shortly.
							$_REQUEST['tp_block_body'] = un_htmlspecialchars($_REQUEST['tp_block_body']);
							// We need this for everything else.
							$value = $_POST['tp_block_body'] = $_REQUEST['tp_block_body'];
						}

						// PHP block?
						if($_POST['tp_block_type'] == 10)
							$value = tp_convertphp($value);

						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_blocks 
							SET '. $setting .' = {string:value} 
							WHERE id = {int:blockid}',
							array('value' => $value, 'blockid' => $where)
						);
					}
					elseif($setting == 'title')
					{
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_blocks 
							SET title = {string:title} 
							WHERE id = {int:blockid}',
							array('title' => $value, 'blockid' => $where)
						);
					}
					elseif($setting == 'body_mode' || $setting == 'body_choice' || $setting == 'body_pure')
						$go = '';
					elseif($setting == 'frame')
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_blocks 
							SET frame = {string:val}
							WHERE id = {int:blockid}',
							array('val' => $value, 'blockid' => $where)
						);
					else
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_blocks 
							SET '. $setting .' = {raw:val}
							WHERE id = {int:blockid}',
							array('val' => $value, 'blockid' => $where)
						);
				}
				elseif(substr($what, 0, 8) == 'tp_group')
					$tpgroups[] = substr($what, 8);
				elseif(substr($what, 0, 12) == 'tp_editgroup')
					$editgroups[] = substr($what, 12);
				elseif(substr($what, 0, 10) == 'actiontype')
					$access[] = 'actio=' . $value;
				elseif(substr($what, 0, 9) == 'boardtype')
					$access[] = 'board=' . $value;
				elseif(substr($what, 0, 11) == 'articletype')
					$access[] = 'tpage=' . $value;
				elseif(substr($what, 0, 12) == 'categorytype')
					$access[] = 'tpcat=' . $value;
				elseif(substr($what, 0, 8) == 'langtype')
					$access[] = 'tlang=' . $value;
				elseif(substr($what, 0, 9) == 'dlcattype')
					$access[] = 'dlcat=' . $value;
				elseif(substr($what, 0, 9) == 'tpmodtype')
					$access[] = 'tpmod=' . $value;
				elseif(substr($what, 0, 9) == 'custotype' && !empty($value))
				{
					$items = explode(',', $value);
					foreach($items as $iti => $it)
						$access[] = 'actio=' . $it;
				}
				elseif(substr($what, 0, 8) == 'tp_lang_')
				{
					if(substr($what, 8) != '' )
						$lang[] = substr($what, 8). '|' . $value;
				}
				elseif(substr($what, 0, 18) == 'tp_userbox_options')
				{
					if(!isset($userbox))
						$userbox = array();
					$userbox[] = $value;
				}
				elseif(substr($what, 0, 8) == 'tp_theme')
				{
					$theme = substr($what, 8);
					if(!isset($themebox))
						$themebox = array();
					// get the path too
					if(isset($_POST['tp_path'.$theme]))
						$tpath = $_POST['tp_path'.$theme];
					else
						$tpath = '';

					$themebox[] = $theme . '|' . $value . '|' . $tpath;
				}
			}
			// construct the access++
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_blocks 
				SET	access2 = {string:acc2},
					access = {string:acc},
					lang = {string:lang},
					editgroups = {string:editgrp}
				WHERE id = {int:blockid}',
				array(
					'acc2' => implode(',', $access),
					'acc' => implode(',', $tpgroups),
					'lang' => implode('|', $lang),
					'editgrp' => implode(',', $editgroups),
					'blockid' => $where,
				)
			);
			
			if(isset($userbox))
				$updateArray['userbox_options'] = implode(',', $userbox);

			if(isset($themebox))
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_blocks 
					SET body = {string:body}
					WHERE id = {int:blockid}',
					array('body' => implode(',', $themebox), 'blockid' => $where,)
				);

			// anything from PHP block?
			if(isset($_POST['blockcode_overwrite']))
			{
				// get the blockcode
				$newval = TPparseModfile(file_get_contents($boarddir . '/tp-files/tp-blockcodes/' . $_POST['tp_blockcode'].'.blockcode') , array('code')); 
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_blocks 
					SET body = {string:body}
					WHERE id = {int:blockid}',
					array('body' => $newval['code'], 'blockid' => $where)
				);
			}

			// check if uploadad picture 
			if(isset($_FILES['qup_blockbody']) && file_exists($_FILES['qup_blockbody']['tmp_name']))
			{
				$name = TPuploadpicture('qup_blockbody', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
			}
			updateTPSettings($updateArray);
			
			redirectexit('action=tpadmin;blockedit='.$where.';' . $context['session_var'] . '=' . $context['session_id']);
		}
		// Editing an article?
		elseif(substr($from, 0, 11) == 'editarticle')
		{
			checkSession('post');
			isAllowedTo('tp_articles');

			$new = false;
			$where = substr($from, 11);

			if(empty($where))
			{
				// we need to create one first
				$smcFunc['db_insert']('INSERT',
					'{db_prefix}tp_articles',
					array('date' => 'int'),
					array(time()),
					array('id')
				);

				$where = $smcFunc['db_insert_id']('{db_prefix}tp_articles', 'id');
				$new = true;
				$from = 'editarticle' . $where;
			}
			
			// check if uploads are there
			if(file_exists($_FILES['tp_article_illupload']['tmp_name']))
			{
				$name = TPuploadpicture('tp_article_illupload', '', '180', 'jpg,gif,png', 'tp-files/tp-articles/illustrations');
				tp_createthumb('tp-files/tp-articles/illustrations/'. $name, 128, 128, 'tp-files/tp-articles/illustrations/s_'. $name);
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_articles 
					SET illustration = {string:ill} 
					WHERE id = {int:artid} LIMIT 1',
					array('ill' => 's_' . $name, 'artid' => $where)
				);
			}
			// check if uploadad picture 
			if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
			{
				$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
			}
			$options = array();
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0, 11) == 'tp_article_' && !empty($where))
				{
					$setting = substr($what, 11);
					
					if($setting == 'authorid')
					{
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET author_id = {int:auth} 
							WHERE id = {int:artid} LIMIT 1',
							array('auth' => $value, 'artid' => $where)
						);
					}
					elseif($setting == 'idtheme')
					{
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET id_theme = {int:id_theme} 
							WHERE id = {int:artid} LIMIT 1',
							array('id_theme' => $value, 'artid' => $where)
						);
					}
					elseif($setting == 'subject')
					{
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET subject = {string:subject} 
							WHERE id = {int:artid} LIMIT 1',
							array('subject' => $value, 'artid' => $where)
						);
					}
					elseif($setting == 'shortname')
					{
						$value = htmlspecialchars(str_replace(' ', '-', $value), ENT_QUOTES);
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET shortname = {string:shortname} 
							WHERE id = {int:artid} LIMIT 1',
							array('shortname' => $value, 'artid' => $where)
						);
					}
					elseif($setting == 'category')
					{
						// for the event, get the allowed
						$request = $smcFunc['db_query']('', '
							SELECT value3 FROM {db_prefix}tp_variables 
							WHERE id = {int:varid} LIMIT 1',
							array('varid' => $value)
						);
							
						if($smcFunc['db_num_rows']($request) > 0) 
						{
							$row = $smcFunc['db_fetch_assoc']($request);
							$allowed = $row['value3'];
							$smcFunc['db_free_result']($request);
						}
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET category = {int:cat} 
							WHERE id = {int:artid} LIMIT 1',
							array('cat' => $value, 'artid' => $where)
						);
					}
					elseif(in_array($setting, array('body', 'intro')))
					{
						// If we came from WYSIWYG then turn it back into BBC regardless.
						if (!empty($_REQUEST['tp_article_body_mode']) && isset($_REQUEST['tp_article_body']))
						{
							require_once($sourcedir . '/Subs-Editor.php');
							$_REQUEST['tp_article_body'] = html_to_bbc($_REQUEST['tp_article_body']);
							// We need to unhtml it now as it gets done shortly.
							$_REQUEST['tp_article_body'] = un_htmlspecialchars($_REQUEST['tp_article_body']);
							// We need this for everything else.
							if($setting == 'body')
								$value = $_POST['tp_article_body'] = $_REQUEST['tp_article_body'];
							elseif ($settings == 'intro')
								$value = $_POST['tp_article_intro'] = $_REQUEST['tp_article_intro'];
						}
						
						// in case of HTML article we need to check it
						if(isset($_POST['tp_article_body_pure']) && isset($_POST['tp_article_body_choice']))
						{
							if($_POST['tp_article_body_choice'] == 0)
							{
								if ($setting == 'body')
									$value = $_POST['tp_article_body_pure'];
								elseif ($setting == 'intro')
									$value = $_POST['tp_article_intro'];
							}
							
							// save the choice too
							$request = $smcFunc['db_query']('', '
								SELECT id FROM {db_prefix}tp_variables 
								WHERE subtype2 = {int:sub2}  
								AND type = {string:type} LIMIT 1',
								array('sub2' => $where, 'type' => 'editorchoice')
							);
							if($smcFunc['db_num_rows']($request) > 0)
							{
								$row = $smcFunc['db_fetch_assoc']($request);
								$smcFunc['db_free_result']($request);
								$smcFunc['db_query']('', '
									UPDATE {db_prefix}tp_variables 
									SET value1 = {string:val1}
									WHERE subtype2 = {int:sub2} 
									AND type = {string:type}',
									array('val1' => $_POST['tp_article_body_choice'], 'sub2' => $where, 'type' => 'editorchoice')
								);
							}
							else
								$smcFunc['db_insert']('INSERT',
									'{db_prefix}tp_variables',
									array('value1' => 'string', 'type' => 'string', 'subtype2' => 'int'),
									array($_POST['tp_article_body_choice'], 'editorchoice', $where),
									array('id')
								); 
						}
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET '. $setting .' = {string:val} 
							WHERE id = {int:artid} LIMIT 1',
							array('val' => $value, 'artid' => $where)
						);
					}
					elseif(in_array($setting, array('day', 'month', 'year', 'minute', 'hour', 'timestamp')))
					{
						$timestamp = mktime($_POST['tp_article_hour'], $_POST['tp_article_minute'], 0, $_POST['tp_article_month'], $_POST['tp_article_day'], $_POST['tp_article_year']);
						if(!isset($savedtime))
							$smcFunc['db_query']('', '
								UPDATE {db_prefix}tp_articles 
								SET date = {int:date} 
								WHERE id = {int:artid} LIMIT 1',
								array('date' => $timestamp, 'artid' => $where)
							);
						$savedtime = 1;
					}
					elseif(in_array($setting, array('pubstartday', 'pubstartmonth', 'pubstartyear', 'pubstartminute', 'pubstarthour', 'pub_start')))
					{
						// are all zero? then skip
						if(empty($_POST['tp_article_pubstarthour']) && empty($_POST['tp_article_pubstartminute']) && empty($_POST['tp_article_pubstartmonth']) && empty($_POST['tp_article_pubstartday']) && empty($_POST['tp_article_pubstartyear']))
								$smcFunc['db_query']('', '
									UPDATE {db_prefix}tp_articles 
									SET pub_start = {int:start}
									WHERE id = {int:artid} LIMIT 1',
									array('start' => 0, 'artid' => $where)
								);
						else
							$timestamp = mktime($_POST['tp_article_pubstarthour'], $_POST['tp_article_pubstartminute'], 0, $_POST['tp_article_pubstartmonth'], $_POST['tp_article_pubstartday'], $_POST['tp_article_pubstartyear']);
							if(!isset($pubstart))
								$smcFunc['db_query']('', '
									UPDATE {db_prefix}tp_articles 
									SET pub_start = {int:start} 
									WHERE id = {int:artid} LIMIT 1',
									array('start' => $timestamp, 'artid' => $where)
								);

						$pubstart = 1;
					}
					elseif(in_array($setting, array('pubendday', 'pubendmonth', 'pubendyear', 'pubendminute', 'pubendhour', 'pub_start')))
					{
						// are all zero? then skip
						if(empty($_POST['tp_article_pubendhour']) && empty($_POST['tp_article_pubendminute']) && empty($_POST['tp_article_pubendmonth']) && empty($_POST['tp_article_pubendday']) && empty($_POST['tp_article_pubendyear']))
								$smcFunc['db_query']('', '
									UPDATE {db_prefix}tp_articles 
									SET pub_end = {int:end}
									WHERE id = {int:artid} LIMIT 1',
									array('end' => 0, 'artid' => $where)
								);
						else
							$timestamp = mktime($_POST['tp_article_pubendhour'], $_POST['tp_article_pubendminute'], 0, $_POST['tp_article_pubendmonth'], $_POST['tp_article_pubendday'], $_POST['tp_article_pubendyear']);
							if(!isset($pubend))
								$smcFunc['db_query']('', '
									UPDATE {db_prefix}tp_articles 
									SET pub_end = {int:end}
									WHERE id = {int:artid} LIMIT 1',
									array('end' => $timestamp, 'artid' => $where)
								);

						$pubend = 1;
					}
					elseif(substr($setting, 0, 8) == 'options_')
					{
						if(substr($setting, 0, 19) == 'options_lblockwidth' || substr($setting,0,19) == 'options_rblockwidth')
							$options[] = substr($setting, 8).$value;
						else
							$options[] = substr($setting, 8);
						
					}
					elseif(in_array($setting, array('body_mode', 'intro_mode', 'illupload', 'body_pure', 'body_choice')))
					{
						// ignore it
						continue;
					}
					elseif($setting == 'approved')
					{
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET approved = {int:approved} 
							WHERE id = {int:artid} LIMIT 1',
							array('approved' => $value, 'artid' => $where)
						);
						if($value == 1)
							$smcFunc['db_query']('', '
								DELETE FROM {db_prefix}tp_variables 
								WHERE type = {string:type} 
								AND value5 = {int:val5}',
								array('type' => 'art_not_approved', 'val5' => $where)
							);
						elseif ($new)
							$smcFunc['db_insert']('replace',
								'{db_prefix}tp_variables',
								array('type' => 'string', 'value5' => 'int'),
								array('art_not_approved', $where),
								array('id')
							);						
					}
					else
					{
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET '.$setting.' = {string:val}
							WHERE id = {int:artid} LIMIT 1',
							array('val' => $value, 'artid' => $where)
						);
					}
				}
			}
			// if this was a new article
			if($_POST['tp_article_approved'] == 1 && $_POST['tp_article_off'] == 0)
				tp_recordevent($timestamp, $_POST['tp_article_authorid'], 'tp-createdarticle', 'page=' . $where, 'Creation of new article.', (isset($allowed) ? $allowed : 0) , $where);
			
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_articles 
				SET options = {string:opt} 
				WHERE id = {int:artid} LIMIT 1',
				array(
					'opt' => implode(',', $options),
					'artid' => $where,
				)
			);

			return $from;
		}
	}
	else
		return;
}

function get_langfiles()
{
	global $context, $settings;

	// get all languages for blocktitles
	$language_dir = $settings['default_theme_dir'] . '/languages';
	$context['TPortal']['langfiles'] = array();
	$dir = dir($language_dir);
	while ($entry = $dir->read())
		if (substr($entry, 0, 6) == 'index.' && substr($entry,(strlen($entry) - 4) ,4) == '.php' && strlen($entry) > 9)
	$context['TPortal']['langfiles'][] = substr(substr($entry, 6), 0, -4);
	$dir->close();
}

function get_catlayouts()
{
	global $context, $txt;

	// setup the layoutboxes
	$context['TPortal']['admin_layoutboxes'] = array(
		array('value' => '1', 'label' => $txt['tp-catlayout1']),
		array('value' => '2', 'label' => $txt['tp-catlayout2']),
		array('value' => '3', 'label' => $txt['tp-catlayout3']),
		array('value' => '4', 'label' => $txt['tp-catlayout4']),
		array('value' => '5', 'label' => $txt['tp-catlayout5']),
		array('value' => '6', 'label' => $txt['tp-catlayout6']),
		array('value' => '7', 'label' => $txt['tp-catlayout7']),
		array('value' => '8', 'label' => $txt['tp-catlayout8']),
	);
}

function get_boards()
{
	global $context, $smcFunc;

	$context['TPortal']['boards'] = array();
	$request = $smcFunc['db_query']('', '
		SELECT b.id_board as id, b.name 
		FROM {db_prefix}boards as b 
		WHERE 1',
		array()
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
			$context['TPortal']['boards'][] = $row; 
		
		$smcFunc['db_free_result']($request);
	}
}

function get_articles()
{

	global $context, $smcFunc;

	$context['TPortal']['edit_articles'] = array();
	
	$request = $smcFunc['db_query']('', '
		SELECT id, subject, shortname FROM {db_prefix}tp_articles 
		WHERE approved = 1 AND off = 0');
		
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row=$smcFunc['db_fetch_assoc']($request))
			$context['TPortal']['edit_articles'][] = $row; 
		
		$smcFunc['db_free_result']($request);
	}
}

function get_catnames()
{

	global $context, $smcFunc;

	$context['TPortal']['catnames'] = array();
	
	$request = $smcFunc['db_query']('', '
		SELECT id,value1 FROM {db_prefix}tp_variables 
		WHERE type = {string:type}',
		array('type' => 'category')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
			$context['TPortal']['catnames'][$row['id']] = $row['value1']; 
		
		$smcFunc['db_free_result']($request);
	}
}
?>