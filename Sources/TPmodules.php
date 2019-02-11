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
function TPmodules()
{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

	$ID_MEMBER = $context['user']['id'];

	if(loadLanguage('TPmodules') == false)
		loadLanguage('TPmodules', 'english');
	if(loadLanguage('TPortalAdmin') == false)
		loadLanguage('TPortalAdmin', 'english');

    require_once($sourcedir.'/TPortalArticle.php');
	$subAction = TPUtil::filter('sa', 'get', 'string');
    switch($subAction) {
        case 'showcomments':
            articleShowComments();
            break;
        case 'comment':
            articleInsertComment();
            break;
        case 'killcomment':
            articleDeleteComment();
            break;
        case 'editcomment':
            articleEditComment();
            break;
        case 'rate_article':
            articleRate();
            break;
        case 'editarticle':
            articleEdit();
            break;
        case 'tpattach':
            articleAttachment();
            break;
        case 'myarticles':
            articleShow();
            break;
	    case 'submitarticle':
        case 'addarticle_html':
        case 'addarticle_bbc': 
            articleInsert();
            break;
        case 'publish':
            articlePublish();
            break;
        case 'savearticle':
            articleSave();
            break;
        case 'uploadimage':
            articleUploadImage();
            break;
        default:
		    //redirectexit('action=forum');
            break;
    }

	// get subaction
	$tpsub = '';
	if(isset($_GET['sa']))
	{
		$context['TPortal']['subaction'] = $_GET['sa'];
		$tpsub = $_GET['sa'];
	}
	elseif(isset($_GET['sub']))
	{
		$context['TPortal']['subaction'] = $_GET['sub'];
		$tpsub = $_GET['sub'];
	}

	// for help pages
	if(isset($_GET['p']))
	{
		$helpOptions = array('introduction', 'articles', 'frontpage', 'panels', 'blocks', 'modules', 'plugins');
		if(in_array($_GET['p'], $helpOptions))
			$context['TPortal']['helpsection'] = $_GET['p'];
		else
			$context['TPortal']['helpsection'] = 'introduction';
	}
	else
		$context['TPortal']['helpsection'] = 'introduction';

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// call the editor setup
	require_once($sourcedir. '/TPcommon.php');

	// download manager?
	if(isset($_GET['dl']))
	{
		$context['TPortal']['dlsub'] = $_GET['dl'] == '' ? '0' : $_GET['dl'];
	}

	// fetch all extensions and compare
	$result =  $smcFunc['db_query']('', '
        SELECT modulename, autoload_run, subquery
        FROM {db_prefix}tp_modules WHERE active = {int:active}',
        array('active' => 1)
    );
	if($smcFunc['db_num_rows']($result) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($result))
		{
			if(isset($_GET[$row['subquery']]))
				$tpmodule=$boarddir .'/tp-files/tp-modules/' . $row['modulename']. '/Sources/'. $row['autoload_run'];
		}
		$smcFunc['db_free_result']($result);
	}

	// clear the linktree first
	TPstrip_linktree();

	// include source files in case of modules
	if(isset($context['TPortal']['dlsub']))
	{
		require_once( $sourcedir .'/TPdlmanager.php');
		TPdlmanager_init();
	}
	elseif(!empty($tpmodule))
		require_once($tpmodule);
	// get xml code
	elseif(isset($_GET['getsnippets']))
		get_snippets_xml();
	// save the upshrink value
	elseif(isset($_GET['upshrink']) && isset($_GET['state']))
	{
		$blockid = $_GET['upshrink'];
		$state = $_GET['state'];
		if(isset($_COOKIE['tp-upshrinks']))
		{
			$shrinks = explode(',', $_COOKIE['tp-upshrinks']);
			if($state == 0 && !in_array($blockid, $shrinks))
				$shrinks[] = $blockid;
			elseif($state == 1 && in_array($blockid, $shrinks))
			{
				$spos = array_search($blockid, $shrinks);
				if($spos > -1)
					unset($shrinks[$spos]);
			}
			$newshrink = implode(',', $shrinks);
			setcookie ('tp-upshrinks', $newshrink , time()+7776000);
		}
		else
		{
			if($state == 0)
			setcookie ('tp-upshrinks', $blockid, (time()+7776000));
		}
		// Don't output anything...
		$tid = time();
		redirectexit($settings['images_url'] . '/blank.gif?ti='.$tid);
	}
	elseif($tpsub == 'updatelog')
	{
		$context['TPortal']['subaction'] = 'updatelog';
		$request = $smcFunc['db_query']('', '
            SELECT value1 FROM {db_prefix}tp_variables
            WHERE type = {string:type} ORDER BY id DESC',
            array('type' => 'updatelog')
        );
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$check = $smcFunc['db_fetch_assoc']($request);
			$context['TPortal']['updatelog'] = $check['value1'];
			$smcFunc['db_free_result']($request);
		}
		else
			$context['TPortal']['updatelog'] = "";

		loadtemplate('TPmodules');
		$context['sub_template'] = 'updatelog';
	}
	elseif($tpsub == 'savesettings' )
	{
		// check the session
		checkSession('post');
		if(isset($_POST['item']))
			$item = $_POST['item'];
		else
			$item = 0;

		if(isset($_POST['memberid']))
			$mem = $_POST['memberid'];
		else
			$mem = 0;

		if(!isset($mem) || (isset($mem) && !is_numeric($mem)))
			fatalerror('Member doesn\'t exist.');

		foreach($_POST as $what => $value){
			if($what == 'tpwysiwyg' && $item > 0){
				 $smcFunc['db_query']('', '
				 UPDATE {db_prefix}tp_data
				 SET value = {int:val} WHERE id = {int:id}',
				 array('val' => $value, 'id' => $item)
		 	);
			}
			elseif($what == 'tpwysiwyg' && $item == 0)
				 $smcFunc['db_insert']('INSERT',
				 	'{db_prefix}tp_data',
					 array('type' => 'int', 'id_member' => 'int', 'value' => 'int'),
					 array(2, $mem, $value),
					 array('id')
				 );

		}
		// go back to profile page
		redirectexit('action=profile;u='.$mem.';area=tparticles;sa=settings');
	}
	elseif($tpsub == 'rate_dlitem' && isset($_POST['tp_dlitem_rating_submit']) && $_POST['tp_dlitem_type'] == 'dlitem_rating')
	{
		// check the session
		checkSession('post');

		$commenter = $context['user']['id'];
		$dl = $_POST['tp_dlitem_id'];
		// check if the download indeed exists
		$request = $smcFunc['db_query']('', '
			SELECT rating, voters FROM {db_prefix}tp_dlmanager
			WHERE id = {int:dlid}',
			array('dlid' => $dl)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);

			$voters = array();
			$ratings = array();
			$voters = explode(',', $row[1]);
			$ratings = explode(',', $row[0]);
			// check if we haven't rated anyway
			if(!in_array($ID_MEMBER,$voters))
			{
				if($row[0] != '')
				{
					$new_voters = $row[1].','.$ID_MEMBER;
					$new_ratings = $row[0].','.$_POST['tp_dlitem_rating'];
				}
				else
				{
					$new_voters = $ID_MEMBER;
					$new_ratings = $_POST['tp_dlitem_rating'];
				}
				// update ratings and raters
				$smcFunc['db_query']('', '
				 	UPDATE {db_prefix}tp_dlmanager
					SET rating = {string:rate}
					WHERE id = {int:dlid}',
					array('rate' => $new_ratings, 'dlid' => $dl)
				);
				$smcFunc['db_query']('', '
				 	UPDATE {db_prefix}tp_dlmanager
				 	SET voters = {string:vote}
				 	WHERE id = {int:dlid}',
					array('vote' => $new_voters, 'dlid' => $dl)
				);
			}
			// go back to the download
			redirectexit('action=tpmod;dl=item'.$dl);
		}
	}
	elseif($tpsub == 'help')
	{
		$context['current_action'] = 'help';
		require_once( $sourcedir .'/TPhelp.php');
		TPhelp_init();
	}
	elseif($tpsub == 'dlsubmitsuccess')
	{
		$context['TPortal']['subaction'] = 'dlsubmitsuccess';
		loadtemplate('TPmodules');
		$context['sub_template'] = 'dlsubmitsuccess';
	}
	// edit a block?
	elseif(substr($tpsub, 0, 9) == 'editblock')
	{
		$what = substr($tpsub, 9);
		if(!is_numeric($what))
			fatal_error($txt['tp-notablock'], false);
		// get one block
		$context['TPortal']['subaction'] = 'editblock';
		$context['TPortal']['blockedit'] = array();
		$request =  $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_blocks
			WHERE id = {int:blockid} LIMIT 1',
			array('blockid' => $what)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);

			$can_edit = !empty($row['editgroups']) ? get_perm($row['editgroups'],'') : false;

			// check permission
			if(allowedTo('tp_blocks') || $can_edit)
				$ok=true;
			else
				fatal_error($txt['tp-blocknotallowed'], false);

			$context['TPortal']['editblock'] = array();
			$context['TPortal']['blockedit']['id'] = $row['id'];
			$context['TPortal']['blockedit']['title'] = $row['title'];
			$context['TPortal']['blockedit']['body'] = $row['body'];
			$context['TPortal']['blockedit']['frame'] = $row['frame'];
			$context['TPortal']['blockedit']['type'] = $row['type'];
			$context['TPortal']['blockedit']['var1'] = $row['var1'];
			$context['TPortal']['blockedit']['var2'] = $row['var2'];
			$context['TPortal']['blockedit']['visible'] = $row['visible'];
			$context['TPortal']['blockedit']['editgroups'] = $row['editgroups'];
			$smcFunc['db_free_result']($request);
		}
		else
			fatal_error($txt['tp-notablock'], false);

		// Add in BBC editor before we call in template so the headers are there
		if($context['TPortal']['blockedit']['type'] == '5')
		{
			$context['TPortal']['editor_id'] = 'blockbody' . $context['TPortal']['blockedit']['id'];
			TP_prebbcbox($context['TPortal']['editor_id'], strip_tags($context['TPortal']['blockedit']['body']));
		}

		if(loadLanguage('TPortalAdmin') == false)
			loadLanguage('TPortalAdmin', 'english');
		loadtemplate('TPmodules');
	}
	// save a block?
	elseif(substr($tpsub, 0, 9) == 'saveblock')
	{

		$whatID = substr($tpsub, 9);
		if(!is_numeric($whatID))
			fatal_error($txt['tp-notablock'], false);
		$request =  $smcFunc['db_query']('', '
			SELECT editgroups FROM {db_prefix}tp_blocks
			WHERE id = {int:blockid} LIMIT 1',
			array('blockid' => $whatID)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			// check permission
			if(allowedTo('tp_blocks') || get_perm($row['editgroups']))
				$ok = true;
			else
				fatal_error($txt['tp-blocknotallowed'], false);
			$smcFunc['db_free_result']($request);

			// loop through the values and save them
			foreach ($_POST as $what => $value)
			{
				if(substr($what, 0, 10) == 'blocktitle')
				{
					// make sure special charachters can't be done
					$value = strip_tags($value);
					$value = preg_replace('~&#\d+$~', '', $value);
					$val = substr($what,10);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks
						SET title = {string:title}
						WHERE id = {int:blockid}',
						array('title' => $value, 'blockid' => $val)
					);
				}
				elseif(substr($what, 0, 9) == 'blockbody' && substr($what, -4) != 'mode')
				{
					// If we came from WYSIWYG then turn it back into BBC regardless.
					if (!empty($_REQUEST[$what.'_mode']) && isset($_REQUEST[$what]))
					{
						require_once($sourcedir . '/Subs-Editor.php');
						$_REQUEST[$what] = html_to_bbc($_REQUEST[$what]);
						// We need to unhtml it now as it gets done shortly.
						$_REQUEST[$what] = un_htmlspecialchars($_REQUEST[$what]);
						// We need this for everything else.
						$value = $_POST[$what] = $_REQUEST[$what];
					}

					$val = (int) substr($what, 9);

					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks
						SET body = {string:body}
						WHERE id = {int:blockid}',
						array('body' => $value, 'blockid' => $val)
					);
				}
				elseif(substr($what, 0, 10) == 'blockframe')
				{
					$val = substr($what, 10);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks
						SET frame = {string:frame}
						WHERE id = {int:blockid}',
						array('frame' => $value, 'blockid' => $val)
					);
				}
				elseif(substr($what, 0, 12) == 'blockvisible')
				{
					$val = substr($what, 12);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks
						SET visible = {string:vis}
						WHERE id = {int:blockid}',
						array('vis' => $value, 'blockid' => $val)
					);
				}
				elseif(substr($what, 0, 9) == 'blockvar1')
				{
					$val=substr($what, 9);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks
						SET var1 = {string:var1}
						WHERE id = {int:blockid}',
						array('var1' => $value, 'blockid' => $val)
					);
				}
				elseif(substr($what, 0, 9) == 'blockvar2')
				{
					$val = substr($what, 9);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks
						SET var2 = {string:var2}
						WHERE id = {int:blockid}',
						array('var2' => $value, 'blockid' => $val)
					);
				}
			}
			redirectexit('action=tpmod;sa=editblock'.$whatID);
		}
		else
			fatal_error($txt['tp-notablock'], false);
	}
	elseif($tpsub == 'credits')
	{
		require_once( $sourcedir .'/TPhelp.php');
		TPCredits();
	}
	else
	{
		redirectexit('action=forum');
	}

}

// profile summary
function tp_profile_summary($memID)
{
	global $txt, $context, $smcFunc;

	$context['page_title'] = $txt['tpsummary'];

	// get all articles written by member
	$request =  $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_articles
		WHERE author_id = {int:author}',
		array('author' => $memID)
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max_art = $result[0];
	$smcFunc['db_free_result']($request);

	$max_upload = 0;
	if($context['TPortal']['show_download'])
	{
		// get all uploads
		$request =  $smcFunc['db_query']('', '
			SELECT COUNT(*) FROM {db_prefix}tp_dlmanager
			WHERE author_id = {int:author} AND type = {string:type}',
			array('author' => $memID, 'type' => 'dlitem')
		);
		$result = $smcFunc['db_fetch_row']($request);
		$max_upload = $result[0];
		$smcFunc['db_free_result']($request);
	}
	$context['TPortal']['tpsummary']=array(
		'articles' => $max_art,
		'uploads' => $max_upload,
	);
 }
// articles and comments made by the member
function tp_profile_articles($memID)
{
	global $txt, $context, $scripturl, $smcFunc;

	$context['page_title'] = $txt['articlesprofile'];

	if(isset($context['TPortal']['mystart']))
		$start = is_numeric($context['TPortal']['mystart']) ? $context['TPortal']['mystart'] : 0;
	else
		$start = 0;

	$context['TPortal']['memID'] = $memID;

	if($context['TPortal']['tpsort'] != '')
		$sorting = $context['TPortal']['tpsort'];
	else
		$sorting = 'date';

	$max = 0;
	// get all articles written by member
	$request =  $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_articles
		WHERE author_id = {int:auth}',
		array('auth' => $memID)
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max = $result[0];
	$smcFunc['db_free_result']($request);

	// get all not approved articles
	$request =  $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_articles
		WHERE author_id = {int:auth} AND approved = {int:approved}',
		array('auth' => $memID, 'approved' => 0)
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max_approve = $result[0];
	$smcFunc['db_free_result']($request);

	// get all articles currently being off
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_articles
		WHERE author_id = {int:auth} AND off = {int:off} AND approved = {int:approved}',
		array('auth' => $memID, 'off' => 1,  'approved' => 1)
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max_off = $result[0];
	$smcFunc['db_free_result']($request);

	$context['TPortal']['all_articles'] = $max - $max_approve - $max_off;
	$context['TPortal']['approved_articles'] = $max_approve;
	$context['TPortal']['off_articles'] = $max_off;

	if(!in_array($sorting, array('date', 'subject', 'views', 'category', 'comments')))
		$sorting = 'date';

	$request = $smcFunc['db_query']('', '
		SELECT art.id, art.date, art.subject, art.approved, art.off, art.comments, art.views, art.rating, art.voters,
			art.author_id as authorID, art.category, art.locked
		FROM {db_prefix}tp_articles AS art
		WHERE art.author_id = {int:auth}
		ORDER BY art.{raw:sort} {raw:sorter} LIMIT {int:start}, 10',
		array('auth' => $memID, 
		'sort' => $sorting, 
		'sorter' => in_array($sorting, array('date', 'views', 'comments')) ? 'DESC' : 'ASC',
		'start' => $start
		)
	);

	if($smcFunc['db_num_rows']($request) > 0){
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			$rat = array();
			$rating_votes = 0;
			$rat = explode(',', $row['rating']);
			$rating_votes = count($rat);
			if($row['rating'] == '')
				$rating_votes = 0;

			$total = 0;
			foreach($rat as $mm => $mval)
			{
				if(is_numeric($mval))
					$total = $total + $mval;
			}

			if($rating_votes > 0 && $total > 0)
				$rating_average = floor($total / $rating_votes);
			else
				$rating_average = 0;

			$can_see = true;

			if(($row['approved'] != 1 || $row['off'] == 1))
				$can_see = allowedTo('tp_articles');

			if($can_see)
				$context['TPortal']['profile_articles'][] = array(
					'id' => $row['id'],
					'subject' => $row['subject'],
					'date' => timeformat($row['date']),
					'timestamp' => $row['date'],
					'href' => '' . $scripturl . '?page='.$row['id'],
					'comments' => $row['comments'],
					'views' => $row['views'],
					'rating_votes' => $rating_votes,
					'rating_average' => $rating_average,
					'approved' => $row['approved'],
					'off' => $row['off'],
					'locked' => $row['locked'],
					'catID' => $row['category'],
					'category' => '<a href="'.$scripturl.'?mycat='.$row['category'].'">' . (isset($context['TPortal']['catnames'][$row['category']]) ? $context['TPortal']['catnames'][$row['category']] : '') .'</a>',
					'editlink' => allowedTo('tp_articles') ? $scripturl.'?action=tpadmin;sa=editarticle'.$row['id'] : $scripturl.'?action=tpmod;sa=editarticle'.$row['id'],
				);
		}
		$smcFunc['db_free_result']($request);
	}
	// construct pageindexes
	if($max > 0)
		$context['TPortal']['pageindex'] = TPageIndex($scripturl.'?action=profile;area=tparticles;u='.$memID.';tpsort='.$sorting, $start, $max, '10');
	else
		$context['TPortal']['pageindex'] = '';

	// setup subaction
	$context['TPortal']['profile_action'] = '';
	if(isset($_GET['sa']) && $_GET['sa'] == 'settings')
		$context['TPortal']['profile_action'] = 'settings';

	
	// Create the tabs for the template.
	$context[$context['profile_menu_name']]['tab_data'] = array(
		'title' => $txt['articlesprofile'],
		'description' => $txt['articlesprofile2'],
		'tabs' => array(
			'articles' => array(),
			'settings' => array(),
			),
	);

	// setup values for personal settings - for now only editor choice
	// type = 1 -
	// type = 2 - editor choice

	$result = $smcFunc['db_query']('', '
		SELECT id, value FROM {db_prefix}tp_data
		WHERE type = {int:type} AND id_member = {int:id_mem} LIMIT 1',
		array('type' => 2, 'id_mem' => $memID)
	);
	if($smcFunc['db_num_rows']($result) > 0)
	{
		$row = $smcFunc['db_fetch_assoc']($result);
		$context['TPortal']['selected_member_choice'] = $row['value'];
		$context['TPortal']['selected_member_choice_id'] = $row['id'];
		$smcFunc['db_free_result']($result);
	}
	else
	{
		$context['TPortal']['selected_member_choice'] = 0;
		$context['TPortal']['selected_member_choice_id'] = 0;
	}
	$context['TPortal']['selected_member'] = $memID;
	if(loadLanguage('TPortalAdmin') == false)
		loadLanguage('TPortalAdmin', 'english');
}

function tp_profile_download($memID)
{
	global $txt, $context, $scripturl, $smcFunc;

	$context['page_title'] = $txt['downloadprofile'] ;

	// is dl manager on?
	if($context['TPortal']['show_download']==0)
      fatal_lang_error('tp-dlmanageroff', false);

	if(isset($context['TPortal']['mystart']))
		$start = $context['TPortal']['mystart'];
	else
		$start = 0;

	$context['TPortal']['memID'] = $memID;

	if($context['TPortal']['tpsort'] != '')
		$sorting = $context['TPortal']['tpsort'];
	else
		$sorting = 'date';

	$max = 0;
	// get all uploads
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_dlmanager
		WHERE author_id = {int:auth} AND type = {string:type}',
		array('auth' => $memID, 'type' => 'dlitem')
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max = $result[0];
	$smcFunc['db_free_result']($request);

	// get all not approved uploads
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_dlmanager
		WHERE author_id = {int:auth}
		AND type = {string:type}
		AND category < 0',
		array('auth' => $memID, 'type' => 'dlitem')
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max_approve = $result[0];
	$smcFunc['db_free_result']($request);

	$context['TPortal']['all_downloads'] = $max;
	$context['TPortal']['approved_downloads'] = $max_approve;
	$context['TPortal']['profile_uploads'] = array();
	if(!in_array($sorting, array('name', 'created', 'views', 'downloads', 'category')))
		$sorting = 'created';

	$request = $smcFunc['db_query']('', '
		SELECT id, name, category, downloads, views, created, filesize, rating, voters
		FROM {db_prefix}tp_dlmanager
		WHERE author_id = {int:auth}
		AND type = {string:type}
		ORDER BY {raw:sort} {raw:sorter} LIMIT {int:start}, 15',
		array('auth' => $memID, 
		'type' => 'dlitem', 
		'sort' => $sorting,
		'sorter' => in_array($sorting, array('created', 'views', 'downloads')) ? 'DESC' : 'ASC',
		'start' => $start)
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			$rat = array();
			$rating_votes = 0;
			$rat = explode(',', $row['rating']);
			$rating_votes = count($rat);
			if($row['rating'] == '')
				$rating_votes = 0;

			$total = 0;
			foreach($rat as $mm => $mval)
			{
				if(is_numeric($mval))
					$total = $total + $mval;
			}

			if($rating_votes > 0 && $total > 0)
				$rating_average = floor($total / $rating_votes);
			else
				$rating_average = 0;

			$editlink = '';
			if(allowedTo('tp_dlmanager'))
				$editlink = $scripturl.'?action=tpmod;dl=adminitem'.$row['id'];
			elseif($memID == $context['user']['id'])
				$editlink = $scripturl.'?action=tpmod;dl=useredit'.$row['id'];

			$context['TPortal']['profile_uploads'][] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'created' => timeformat($row['created']),
				'category' => $row['category'],
				'href' => $scripturl . '?action=tpmod;dl=item'.$row['id'],
				'views' => $row['views'],
				'rating_votes' => $rating_votes,
				'rating_average' => $rating_average,
				'approved' => $row['category']>0 ? '1' : '0',
				'downloads' => $row['downloads'],
				'catID' => abs($row['category']),
				'category' => $row['category'],
				'editlink' => $editlink,
			);
		}
		$smcFunc['db_free_result']($request);
	}
	// construct pageindexes
	if($max > 0)
		$context['TPortal']['pageindex']=TPageIndex($scripturl.'?action=profile;area=tpdownload;u='.$memID.';tpsort='.$sorting, $start, $max, '10');
	else
		$context['TPortal']['pageindex'] = '';
}

function tp_profile_gallery($memID)
{
	global $txt, $context;
	$context['page_title'] = $txt['galleryprofile'] ;
}
function tp_profile_links($memID)
{
	global $txt, $context;
	$context['page_title'] = $txt['linksprofile'] ;
}

function tp_pro_shoutbox()
{
	global $txt, $context;
	$context['page_title'] = $txt['tp-shouts'];

}

// Tinyportal
function tp_summary($memID)
{
	global $txt, $context;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['tpsummary'];
	tp_profile_summary($memID);
}
function tp_articles($memID)
{
	global $txt, $context;

	TP_article_categories();
	loadtemplate('TPprofile');
	$context['page_title'] = $txt['articlesprofile'];
	tp_profile_articles($memID);
}
function tp_download($memID)
{
	global $txt, $context;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['downloadprofile'];
	tp_profile_download($memID);
}
function tp_shoutb($memID)
{
	global $txt, $context;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['shoutboxprofile'];
	tpshout_profile($memID);
}

// fetch all the shouts for output
function tpshout_profile($memID)
{
    global $context, $scripturl, $txt, $smcFunc;

	$context['page_title'] = $txt['shoutboxprofile'] ;

	if(isset($context['TPortal']['mystart']))
		$start = $context['TPortal']['mystart'];
	else
		$start = 0;

	$context['TPortal']['memID'] = $memID;

	$sorting = 'value2';

	$max = 0;
	// get all shouts
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_shoutbox
		WHERE value5 = {int:val5} AND type = {string:type}',
		array('val5' => $memID, 'type' => 'shoutbox')
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max = $result[0];
	$smcFunc['db_free_result']($request);

	$context['TPortal']['all_shouts'] = $max;
	$context['TPortal']['profile_shouts'] = array();

	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_shoutbox
		WHERE value5 = {int:val5}
		AND type = {string:type}
		ORDER BY {raw:sort} DESC LIMIT {int:start}, 10',
		array('val5' => $memID, 'type' => 'shoutbox', 'sort' => $sorting, 'start' => $start)
	);
	if($smcFunc['db_num_rows']($request) > 0){
		while($row = $smcFunc['db_fetch_assoc']($request)){
			$context['TPortal']['profile_shouts'][] = array(
				'id' => $row['id'],
				'shout' => parse_bbc(censorText($row['value1'])),
				'created' => timeformat($row['value2']),
				'ip' => $row['value4'],
				'editlink' => allowedTo('tp_shoutbox') ? $scripturl.'?action=tpshout;shout=admin;u='.$memID : '',
			);
		}
		$smcFunc['db_free_result']($request);
	}
	// construct pageindexes
	if($max > 0)
		$context['TPortal']['pageindex'] = TPageIndex($scripturl.'?action=profile;area=tpshoutbox;u='.$memID.';tpsort='.$sorting, $start, $max, '10', true);
	else
		$context['TPortal']['pageindex'] = '';


	loadtemplate('TPShout');

	if(loadLanguage('TPShout') == false)
		loadLanguage('TPShout', 'english');

	$context['sub_template'] = 'tpshout_profile';
}
?>
