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

// TinyPortal module entrance
function TPmodules()
{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

	$ID_MEMBER = $context['user']['id'];

	if(loadLanguage('TPmodules') == false)
		loadLanguage('TPmodules', 'english');
	if(loadLanguage('TPortalAdmin') == false)
		loadLanguage('TPortalAdmin', 'english');

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
	TPwysiwyg_setup();
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
	// a comment is sent
	elseif($tpsub == 'comment' && isset($_POST['tp_article_type']) && $_POST['tp_article_type'] == 'article_comment' )
	{
		// check the session
		checkSession('post');

		if (!allowedTo('tp_artcomment'))
			fatal_error($txt['tp-nocomments']);

		$commenter = $context['user']['id'];
		$article = $_POST['tp_article_id'];

		// check if the article indeed exists
		$request =  $smcFunc['db_query']('', '
            SELECT comments FROM {db_prefix}tp_articles 
            WHERE id = {int:artid}',
            array('artid' => $article)
        );
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_row']($request);
			$num_comments = $row[0] + 1;
			$smcFunc['db_free_result']($request);
			$title = strip_tags($_POST['tp_article_comment_title']);
			$comment = substr($smcFunc['htmlspecialchars']($_POST['tp_article_bodytext']), 0, 65536);

			require_once($sourcedir.'/Subs-Post.php');
			preparsecode($comment);
			$time = time();

			// insert the comment
			$smcFunc['db_insert']('INSERT',
                '{db_prefix}tp_variables',
                array('value1' => 'string', 'value2' => 'string', 'value3' => 'string', 'type' => 'string', 'value4' => 'string', 'value5' => 'int'),
                array($title, $comment, $ID_MEMBER, 'article_comment', $time, $article),
                array('id')
            );
            
			// count and increase the number of comments
			$smcFunc['db_query']('', '
                UPDATE {db_prefix}tp_articles 
                SET comments = {int:com} 
                WHERE id = {int:artid}',
                array('com' => $num_comments, 'artid' => $article)
            );
			// go back to the article
			redirectexit('page='.$article.'#tp-comment');
		}
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
	elseif($tpsub == 'showcomments')
	{
		if(!empty($_GET['tpstart']) && is_numeric($_GET['tpstart']))
			$tpstart = $_GET['tpstart'];
		else
			$tpstart = 0;
		
		$mylast = 0;
		$mylast = $user_info['last_login'];
		$showall = false;
		if(isset($_GET['showall']))
			$showall = true;

		$request = $smcFunc['db_query']('', '
        	SELECT COUNT(var.value1)
        	FROM ({db_prefix}tp_variables as var, {db_prefix}tp_articles as art)
			WHERE var.type = {string:type}
			' . ((!$showall || $mylast == 0) ? 'AND var.value4 > '.$mylast : '') .'
			AND art.id = var.value5',
			array('type' => 'article_comment')
		);
		$check = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
	
		$request = $smcFunc['db_query']('', '
			SELECT art.subject, memb.real_name as author, art.author_id as authorID, var.value1, var.value3, 
			var.value5, var.value4, mem.real_name as realName,
			' . ($user_info['is_guest'] ? '1' : '(IFNULL(log.item, 0) >= var.value4)') . ' AS isRead
			FROM ({db_prefix}tp_variables as var, {db_prefix}tp_articles as art)
			LEFT JOIN {db_prefix}members as memb ON (art.author_id = memb.id_member)
			LEFT JOIN {db_prefix}members as mem ON (var.value3 = mem.id_member)
			LEFT JOIN {db_prefix}tp_data as log ON (log.value = art.id AND log.type = 1 AND log.id_member = '.$context['user']['id'].')
			WHERE var.type = {string:type}
			AND art.id = var.value5
			' . ((!$showall || $mylast == 0 ) ? 'AND var.value4 > {int:last}' : '') .'
			ORDER BY var.value4 DESC LIMIT {int:start}, 15',
			array('type' => 'article_comment', 'last' => $mylast, 'start' => $tpstart)
		);

		$context['TPortal']['artcomments']['new'] = array();
		
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($row=$smcFunc['db_fetch_assoc']($request))
				$context['TPortal']['artcomments']['new'][] = array(
				'page' => $row['value5'],	
				'subject' => $row['subject'],	
				'title' => $row['value1'],	
				'membername' => $row['realName'],	
				'time' => timeformat($row['value4']),	
				'author' => $row['author'],	
				'authorID' => $row['authorID'],	
				'member_id' => $row['value3'],	
				'is_read' => $row['isRead'],
				'replies' => $check[0],
				);	
			$smcFunc['db_free_result']($request);
		}

		// construct the pages
		$context['TPortal']['pageindex'] = TPageIndex($scripturl.'?action=tpmod;sa=showcomments', $tpstart, $check[0], 15);
		$context['TPortal']['unreadcomments'] = true;
		$context['TPortal']['showall'] = $showall;
		$context['TPortal']['subaction'] = 'showcomments';
		TPadd_linktree($scripturl.'?action=tpmod;sa=showcomments' . ($showall ? ';showall' : '')  , $txt['tp-showcomments']);
		loadtemplate('TPmodules'); 
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
	// edit or deleting a comment?
	elseif((substr($tpsub, 0, 11) == 'killcomment' || substr($tpsub, 0, 11) == 'editcomment') && $context['user']['is_logged'])
	{
		// check that you indeed can edit or delete
		$comment = substr($tpsub, 11);
		if(!is_numeric($comment))
			fatal_error($txt['tp-noadmincomments']);

		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_variables 
			WHERE id = {int:varid} LIMIT 1',
			array('varid' => $comment)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			$smcFunc['db_free_result']($request);
			if(allowedTo('tp_articles') || $row['value3'] == $ID_MEMBER)
			{
				// deleting the comment
				if(substr($tpsub, 0, 11) == 'killcomment')
				{
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_variables 
						SET value5 = -value5 
						WHERE id = {int:varid}',
						array('varid' => $comment)
					);
					redirectexit('page='.$row['value5']);
				}
				elseif(substr($tpsub, 0, 11) == 'editcomment')
				{
					$context['TPortal']['comment_edit'] = array(
						'id' => $row['id'],
						'title' => $row['value1'],
						'body' => $row['value2'],
					);
					$context['TPortal']['subaction'] = 'editcomment';
					loadtemplate('TPmodules');
				}
			}
			fatal_error($txt['tp-notallowed']);
		}
	}
	// rating is underway
	elseif($tpsub == 'rate_article' && isset($_POST['tp_article_rating_submit']) && $_POST['tp_article_type'] == 'article_rating')
	{
		// check the session
		checkSession('post');

		$commenter = $context['user']['id'];
		$article = $_POST['tp_article_id'];
		// check if the article indeed exists
		$request = $smcFunc['db_query']('', '
			SELECT rating, voters FROM {db_prefix}tp_articles 
			WHERE id = {int:artid}',
			array('artid' => $article)
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
			if(!in_array($ID_MEMBER, $voters))
			{
				if($row[0] != '')
				{
					$new_voters = $row[1].','.$ID_MEMBER;
					$new_ratings = $row[0].','.$_POST['tp_article_rating'];
				}
				else
				{
					$new_voters = $ID_MEMBER;
					$new_ratings = $_POST['tp_article_rating'];
				}
				// update ratings and raters
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_articles 
					SET rating = {string:rate} WHERE id = {int:artid}',
					array('rate' => $new_ratings, 'artid' => $article)
				);
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_articles 
					SET voters = {string:vote} 
					WHERE id = {int:artid}',
					array('vote' => $new_voters, 'artid' => $article)
				);
			}
			// go back to the article
			redirectexit('page='.$article);
		}
	}
	// rating from download manager
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
	// search from articles?
	elseif($tpsub == 'searcharticle')
	{
		TPadd_linktree($scripturl.'?action=tpmod;sa=searcharticle' , $txt['tp-searcharticles2']);
		loadtemplate('TPmodules');
	}
	// search from articles?
	elseif($tpsub == 'tpattach')
	{
		tpattach();
	}
	// search from articles?
	elseif($tpsub == 'searcharticle2')
	{
		$start = 0;
		checkSession('post');
		// any parameters then?
		// nothing to search for?
		if(empty($_POST['tpsearch_what']))
			fatal_error($txt['tp-nosearchentered']);
		// clean the search
		$what = strip_tags($_POST['tpsearch_what']);

		if(!empty($_POST['tpsearch_title']))
			$usetitle = true;
		else
			$usetitle = false;
		if(!empty($_POST['tpsearch_body']))
			$usebody = true;
		else
			$usebody = false;

		if($usetitle && !$usebody)
			$query = 'a.subject LIKE \'%' . $what . '%\'';
		elseif(!$usetitle && $usebody)
			$query = 'a.body LIKE \'%' . $what . '%\'';
		elseif($usetitle && $usebody)
			$query = 'a.subject LIKE \'%' . $what . '%\' OR a.body LIKE \'%' . $what . '%\'';
		else
			$query = 'a.subject LIKE \'%' . $what . '%\'';

		$context['TPortal']['searchresults'] = array();
		$context['TPortal']['searchterm'] = $what;
        $now = forum_time();
        
		$request= $smcFunc['db_query']('', '
			SELECT a.id, a.date, a.views, a.subject, LEFT(a.body, 100) as body, a.author_id as authorID, a.type, m.real_name as realName
			FROM {db_prefix}tp_articles AS a
			LEFT JOIN {db_prefix}members as m ON a.author_id = m.id_member
			WHERE {raw:query}
			AND ((a.pub_start = 0 AND a.pub_end = 0) 
			OR (a.pub_start != 0 AND a.pub_start < '.$now.' AND a.pub_end = 0) 
			OR (a.pub_start = 0 AND a.pub_end != 0 AND a.pub_end > '.$now.') 
			OR (a.pub_start != 0 AND a.pub_end != 0 AND a.pub_end > '.$now.' AND a.pub_start < '.$now.'))
			AND a.off = 0 
			ORDER BY a.date DESC LIMIT 20',
			array('query' => $query)
		);
		
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				if($row['type'] == 'bbc')
					$row['body'] = parse_bbc($row['body']);
				elseif($row['type'] == 'php')
					$row['body'] = '[PHP]';
				else
					$row['body'] = strip_tags($row['body']);

				$row['subject'] = preg_replace('/'.$what.'/', '<span class="highlight">'.$what.'</span>', $row['subject']);
				$row['body'] = preg_replace('/'.$what.'/', '<span class="highlight">'.$what.'</span>', $row['body']);
				$context['TPortal']['searchresults'][]=array(
					'id' => $row['id'],
					'date' => $row['date'],
					'views' => $row['views'],
					'subject' => $row['subject'],
					'body' => $row['body'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
				);
			}
			$smcFunc['db_free_result']($request);
		}
		TPadd_linktree($scripturl.'?action=tpmod;sa=searcharticle' , $txt['tp-searcharticles2']);
		loadtemplate('TPmodules');
	}
	// edit your own articles?
	elseif(substr($tpsub, 0, 11) == 'editarticle')
	{
		$what = substr($tpsub, 11);
		if(!is_numeric($what))
			fatal_error($txt['tp-notanarticle']);
	   
		// get one article
		$context['TPortal']['subaction'] = 'editarticle';
		$context['TPortal']['editarticle'] = array();
		$request =  $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_articles 
			WHERE id = {int:artid} LIMIT 1',
			array('artid' => $what)
		);
		if($smcFunc['db_num_rows']($request))
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			// check permission
			if(!allowedTo('tp_articles') && $ID_MEMBER != $row['author_id'])
				fatal_error($txt['tp-articlenotallowed']);
			// can you edit your own then..?
			isAllowedTo('tp_editownarticle');

			if($row['locked'] == 1)
				fatal_error($txt['tp-articlelocked']);			
			
			// Add in BBC editor before we call in template so the headers are there
			if($row['type'] == 'bbc')
			{
				$context['TPortal']['editor_id'] = 'tp_article_body' . $row['id'];
				TP_prebbcbox($context['TPortal']['editor_id'], strip_tags($row['body'])); 			
			}
			
			$context['TPortal']['editarticle'] = array(
				'id' => $row['id'],
				'date' => array(
					'timestamp' => $row['date'],
					'day' => date("j",$row['date']),
					'month' => date("m",$row['date']),
					'year' => date("Y",$row['date']),
					'hour' => date("G",$row['date']),
					'minute' => date("i",$row['date']),
				),
				'body' => $row['body'],
				'intro' => $row['intro'],
				'useintro' => $row['useintro'],
				'category' => $row['category'],
				'frontpage' => $row['frontpage'],
				'subject' => $row['subject'],
				'authorID' => $row['author_id'],
				'author' => $row['author'],
				'frame' => !empty($row['frame']) ? $row['frame'] : 'theme',
				'approved' => $row['approved'],
				'off' => $row['off'],
				'options' => $row['options'],
				'ID_THEME' => $row['id_theme'],
				'shortname' => $row['shortname'],
				'sticky' => $row['sticky'],
				'locked' => $row['locked'],
				'fileimport' => $row['fileimport'],
				'topic' => $row['topic'],
				'illustration' => $row['illustration'],
				'headers' => $row['headers'],
				'articletype' => $row['type'],
			);
			$smcFunc['db_free_result']($request);
		}
		else
			fatal_error($txt['tp-notanarticlefound']);
		
		if(loadLanguage('TPortalAdmin') == false)
			loadLanguage('TPortalAdmin', 'english');
		loadtemplate('TPmodules');
	}
	// show own articles?
	elseif($tpsub == 'myarticles')
	{
		// not for guests
		if($context['user']['is_guest'])
			fatal_error($txt['tp-noarticlesfound']);

		// get all articles
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(*) FROM {db_prefix}tp_articles 
			WHERE author_id = {int:author}',
			array('author' => $context['user']['id'])
		);
		$row = $smcFunc['db_fetch_row']($request);
		$allmy = $row[0];

		$mystart = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'], array('date', 'id', 'subject'))) ? $_GET['sort'] : 'date';
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpmod;sa=myarticles;sort=' . $sort, $mystart, $allmy, 15);
		
		$context['TPortal']['subaction'] = 'myarticles';
		$context['TPortal']['myarticles'] = array();
		$request2 =  $smcFunc['db_query']('', '
			SELECT id, subject, date, locked, approved, off FROM {db_prefix}tp_articles 
			WHERE author_id = {int:author} 
			ORDER BY {string:sort} DESC LIMIT {int:start}, 15',
			array('author' => $context['user']['id'], 'sort' => $sort, 'start' => $mystart)
		);

		if($smcFunc['db_num_rows']($request2) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request2))
				$context['TPortal']['myarticles'][] = $row;

			$smcFunc['db_free_result']($request2);
		}
		
		if(loadLanguage('TPortalAdmin') == false)
			loadLanguage('TPortalAdmin', 'english');
		loadtemplate('TPmodules');
	}
	elseif(in_array($tpsub, array('submitarticle', 'addarticle_html', 'addarticle_bbc')))
	{
		global $sourcedir, $settings;

		require_once($sourcedir. '/TPcommon.php');

		// a BBC article?
		if(isset($_GET['bbc']) || $tpsub == 'addarticle_bbc')
		{
			isAllowedTo('tp_submitbbc');
			$context['TPortal']['submitbbc'] = 1;
			$context['html_headers'] .= '
				<script type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/editor.js?rc1"></script>';
			
			// Add in BBC editor before we call in template so the headers are there
			$context['TPortal']['editor_id'] = 'tp_article_body';
			TP_prebbcbox($context['TPortal']['editor_id']); 							
		}
		else
			isAllowedTo('tp_submithtml');

		$context['TPortal']['subaction'] = 'submitarticle';
		loadtemplate('TPmodules');
		$context['sub_template'] = 'submitarticle';
	}
	elseif($tpsub == 'submitsuccess')
	{
		$context['TPortal']['subaction'] = 'submitsuccess';
		loadtemplate('TPmodules');
		$context['sub_template'] = 'submitsuccess';
	}
	elseif($tpsub == 'dlsubmitsuccess')
	{
		$context['TPortal']['subaction'] = 'dlsubmitsuccess';
		loadtemplate('TPmodules');
		$context['sub_template'] = 'dlsubmitsuccess';
	}
	// article
	elseif($tpsub == 'submitarticle2')
	{
		require_once($sourcedir. '/TPcommon.php');
		
		if(isset($_POST['tp_article_approved']) || allowedTo('tp_alwaysapproved'))
			$artpp = '0';
		else
			$artpp = '1';

		$arttype = isset($_POST['submittedarticle']) ? $_POST['submittedarticle'] : '';
		$arts = strip_tags($_POST['tp_article_title']);
		$artd = $_POST['tp_article_date'];
		$artimp = isset($_POST['tp_article_fileimport']) ? $_POST['tp_article_fileimport'] : '';
		$artbb = $_POST['tp_article_body'];
		$artu = isset($_POST['tp_article_useintro']) ? $_POST['tp_article_useintro'] : 0;
		$arti = isset($_POST['tp_article_intro']) ? $_POST['tp_article_intro'] : '';
		$artc = !empty($_POST['tp_article_category']) ? $_POST['tp_article_category'] : 0;
		$artf = $_POST['tp_article_frontpage'];
		$artframe = 'theme';
		$artoptions = 'date,title,author,linktree,top,cblock,rblock,lblock,tblock,lbblock,views,rating,ratingallow,avatar';
		$name = $user_info['name'];
		$nameb = $ID_MEMBER;
		if($arts == '')
			$arts = $txt['tp-no_title'];
		// escape any php code
		if($artu == -1 && !get_magic_quotes_gpc())
			$artbb = addslashes($artbb);

		$request = $smcFunc['db_insert']('INSERT',
			'{db_prefix}tp_articles',
			array(
				'date' => 'int',
				'body' => 'string',
				'intro' => 'string',
				'useintro' => 'int',
				'category' => 'int', 
                'frontpage' => 'int', 
                'subject' => 'string', 
                'author_id' => 'int', 
                'author' => 'string', 
                'frame' => 'string', 
                'approved' => 'int', 
                'off' => 'int', 
                'options' => 'string', 
                'parse' => 'int', 
                'comments' => 'int', 
                'comments_var' => 'string', 
                'views' => 'int', 
                'rating' => 'string', 
                'voters' => 'string', 
                'id_theme' => 'int', 
                'shortname' => 'string', 
                'fileimport' => 'string', 
                'type' => 'string'),
			array($artd, $artbb, $arti, $artu, $artc, $artf, $arts, $nameb, $name, $artframe, $artpp, '0', $artoptions, 0, 0, '', 0, '', '', 0, '', $artimp, $arttype),
			array('id')
		);

		$newitem = $smcFunc['db_insert_id']('{db_prefix}tp_articles', 'id');
		// put this into submissions - id and type
		$title = $arts;
		$now = $artd;
		if($artpp == '0')
			 $smcFunc['db_insert']('INSERT',
			 	'{db_prefix}tp_variables',
			 	array('value1' => 'string', 'value2' => 'string', 'value3' => 'string', 'type' => 'string', 'value4'  => 'string', 'value5' => 'int'),
			 	array($title, $now, '', 'art_not_approved', '' , $newitem),
			 	array('id')
			 );

		if(isset($_POST['pre_approved']))
			redirectexit('action=tpmod;sa=addsuccess');

		if(allowedTo('tp_editownarticle') && !allowedTo('tp_articles'))
		{
			// did we get a picture as well?
			if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
			{
				$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
			}
			redirectexit('action=tpmod;sa=editarticle'.$newitem);
		}
		elseif(allowedTo('tp_articles'))
		{
			// did we get a picture as well?
			if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
			{
				$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
			}
			redirectexit('action=tpadmin;sa=editarticle'.$newitem);
		}
		else
			redirectexit('action=tpmod;sa=submitsuccess');
	}
	// edit a block?
	elseif(substr($tpsub, 0, 9) == 'editblock')
	{
		$what = substr($tpsub, 9);
		if(!is_numeric($what))
			fatal_error($txt['tp-notablock']);
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
				fatal_error($txt['tp-blocknotallowed']);

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
			fatal_error($txt['tp-notablock']);

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
	// promoting topics
	elseif($tpsub == 'publish')
	{
		if(!isset($_GET['t']))
			redirectexit('action=forum');
		
		$t = is_numeric($_GET['t']) ? $_GET['t'] : 0;

		if(empty($t))
			redirectexit('action=forum');

		isAllowedTo('tp_settings');		
		$existing = explode(',', $context['TPortal']['frontpage_topics']);
		if(in_array($t, $existing))
			unset($existing[array_search($t, $existing)]);
		else
			$existing[] = $t;

		$newstring = implode(',', $existing);
		if(substr($newstring, 0, 1) == ',')
			$newstring = substr($newstring, 1);

		updateTPSettings(array('frontpage_topics' => $newstring));

		redirectexit('topic='. $t . '.0');
	}
	// save a block?
	elseif(substr($tpsub, 0, 9) == 'saveblock')
	{
		
		$whatID = substr($tpsub, 9);
		if(!is_numeric($whatID))
			fatal_error($txt['tp-notablock']);
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
				fatal_error($txt['tp-blocknotallowed']);
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
			fatal_error($txt['tp-notablock']);
	}
	// save an article
	elseif($tpsub == 'savearticle')
	{
		if(isset($_REQUEST['send']))
		{
			foreach ($_POST as $what => $value) 
			{
				if(substr($what, 0, 16) == 'tp_article_title')
				{
					$val = substr($what, 16);
					if(is_numeric($val) && $val > 0)
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET subject = {string:subject}
							WHERE id = {int:artid}',
							array('subject' => $value, 'artid' => $val)
						);
				}
				elseif(substr($what, 0, 15) == 'tp_article_body' && substr($what, -4) != 'mode')
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
					$val = substr($what, 15);
					if(is_numeric($val) && $val > 0)
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET body = {string:body}
							WHERE id = {int:artid}',
							array('body' => $value, 'artid' => $val)
						);
				}
				elseif(substr($what, 0, 19) == 'tp_article_useintro')
				{
					$val = substr($what, 19);
					if(is_numeric($val) && $val > 0)
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_articles 
							SET useintro = {string:useintro}
							WHERE id = {int:artid}',
							array('useintro' => $value, 'artid' => $val)
						);
				}
				elseif(substr($what, 0, 16) == 'tp_article_intro')
				{
					$val = (int) substr($what, 16);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_articles 
						SET intro = {string:intro}
						WHERE id = {int:artid}',
						array('intro' => $value, 'artid' => $val)
					);
				}
				elseif($what == 'tp_wysiwyg')
				{
					$result = $smcFunc['db_query']('', '
						SELECT id FROM {db_prefix}tp_data 
						WHERE type = {int:type} 
						AND id_member = {int:id_mem}',
						array('type' => 2, 'id_mem' => $ID_MEMBER)
					);
					if($smcFunc['db_num_rows']($result) > 0)
					{
						$row = $smcFunc['db_fetch_assoc']($result);
						$wysid = $row['id'];
						$smcFunc['db_free_result']($result);
					}
					if(isset($wysid))
						$smcFunc['db_query']('', '
							UPDATE {db_prefix}tp_data 
							SET value = {int:val} 
							WHERE id = {int:dataid}',
							array('val' => $value, 'dataid' => $wysid)
						);
					else
						$smcFunc['db_query']('INSERT',
							'{db_prefix}tp_data}',
							array('type' => 'int', 'id_member' => 'int', 'value' => 'int', 'item' => 'int'),
							array(2, $ID_MEMBER, $value, 0),
							array('id')
						);
				}
			}
			if(allowedTo('tp_editownarticle') && !allowedTo('tp_articles'))
			{
				// did we get a picture as well?
				if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
				{
					$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
					tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
				}
				redirectexit('action=tpmod;sa=editarticle'.$val);
			}
			elseif(allowedTo('tp_articles'))
			{
				// did we get a picture as well?
				if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
				{
					$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
					tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
				}
				redirectexit('action=tpadmin;sa=editarticle'.$val);
			}
			else
				fatal_error($txt['tp-notallowed']);
		}
	}
	elseif($tpsub == 'credits')
	{
		require_once( $sourcedir .'/TPhelp.php');
		TPCredits();
	}	
	else
			redirectexit('action=forum');
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
		WHERE author_id = {int:auth} AND off = {int:off}',
		array('auth' => $memID, 'off' => 1)
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max_off = $result[0];
	$smcFunc['db_free_result']($request);

	$context['TPortal']['all_articles'] = $max;
	$context['TPortal']['approved_articles'] = $max_approve;
	$context['TPortal']['off_articles'] = $max_off;

	if(!in_array($sorting, array('date', 'subject', 'views', 'category', 'comments')))
		$sorting = 'date';

	$request = $smcFunc['db_query']('', '
		SELECT art.id, art.date, art.subject, art.approved, art.off, art.comments, art.views, art.rating, art.voters,
			art.author_id as authorID, art.category, art.locked	
		FROM {db_prefix}tp_articles AS art
		WHERE art.author_id = {int:auth}
		ORDER BY art.{raw:sort} DESC LIMIT {int:start}, 10',
		array('auth' => $memID, 'sort' => $sorting, 'start' => $start)
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
				$total = $total + $mval;

			if($rating_votes > 0 && $total > 0)
				$rating_average = floor($total / $rating_votes);
			else
				$rating_average = 0;
			
			$can_see = true;

			if(($row['approved'] != 1 || $row['off'] == 1) && !isAllowedTo('tp_articles'))
				$can_see = false;

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
		fatal_error($txt['tp-dlmanageroff']);

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
		ORDER BY {string:sort} DESC LIMIT {int:start}, 10',
		array('auth' => $memID, 'type' => 'dlitem', 'sort' => $sorting, 'start' => $start)
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
				$total = $total + $mval;

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
				'editlink' => allowedTo('tp_shoutbox') ? $scripturl.'?action=tpmod;shout=admin;u='.$memID : '',
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