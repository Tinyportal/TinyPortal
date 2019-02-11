<?php
/**
 * @package TinyPortal
 * @version 2.0.0
 * @author tino - http://www.tinyportal.net
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
function TPortalArticle()
{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

	if(loadLanguage('TPmodules') == false)
		loadLanguage('TPmodules', 'english');

	if(loadLanguage('TPortalAdmin') == false)
		loadLanguage('TPortalAdmin', 'english');

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// call the editor setup
	require_once($sourcedir. '/TPcommon.php');

	// clear the linktree first
	TPstrip_linktree();

	$subAction = TPUtil::filter('sa', 'get', 'string');
    switch($subAction) {
        case 'showcomments':
            articleShowComments();
            break;
        case 'addcomment':
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
		    redirectexit('action=forum');
            break;
    }
}


function articleInsertComment() {

    // check the session
    checkSession('post');

    if (!allowedTo('tp_artcomment'))
        fatal_error($txt['tp-nocomments'], false);

    $commenter  = $context['user']['id'];
    $article    = $_POST['tp_article_id'];
    $title      = strip_tags($_POST['tp_article_comment_title']);
    $comment    = substr(TPUtil::htmlspecialchars($_POST['tp_article_bodytext']), 0, 65536);

    require_once($sourcedir.'/Subs-Post.php');
    preparsecode($comment);

    $tpArticle = new TPArticle();
    if($tpArticle->insertArticleComment($commenter, $article, $comment, $title))  {
        // go back to the article
        redirectexit('page='.$article.'#tp-comment');
    }

}

function articleShowComments() {

    global $smcFunc;

    if(!empty($_GET['tpstart']) && is_numeric($_GET['tpstart'])) {
        $tpstart = $_GET['tpstart'];
    }
    else {
        $tpstart = 0;
    }

    $mylast = 0;
    $mylast = $user_info['last_login'];
    $showall = false;
    if(isset($_GET['showall'])) {
        $showall = true;
    }

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
        SELECT art.subject, memb.real_name as author, art.author_id as authorID, var.value1, var.value2, var.value3,
        var.value5, var.value4, mem.real_name as realName,
        ' . ($user_info['is_guest'] ? '1' : '(COALESCE(log.item, 0) >= var.value4)') . ' AS isRead
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

    if($smcFunc['db_num_rows']($request) > 0) {
        while($row=$smcFunc['db_fetch_assoc']($request)) {
            $context['TPortal']['artcomments']['new'][] = array(
                'page' => $row['value5'],
                'subject' => $row['subject'],
                'title' => $row['value1'],
                'comment' => $row['value2'],
                'membername' => $row['realName'],
                'time' => timeformat($row['value4']),
                'author' => $row['author'],
                'authorID' => $row['authorID'],
                'member_id' => $row['value3'],
                'is_read' => $row['isRead'],
                'replies' => $check[0],
            );
        }
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

function articleDeleteComment() {
    global $context;

	// edit or deleting a comment?
	if($context['user']['is_logged']) {
		// check that you indeed can edit or delete
		$comment = substr($_GET['sa'], 11);
		if(!is_numeric($comment)) {
			fatal_error($txt['tp-noadmincomments'], false);
        }

        $tpArticle  = new TPArticle();
        $comment    = $tpArticle->getArticleComment($comment);
		if(is_array($comment)) {
            $tpArticle->deleteArticleComment($comment['id']);
			redirectexit('page='.$comment['item_id']);
        }
    }

}

function articleEditComment() {
    global $context;

	if($context['user']['is_logged']) {
		// check that you indeed can edit or delete
		$comment = substr($_GET['sa'], 11);
		if(!is_numeric($comment)) {
			fatal_error($txt['tp-noadmincomments'], false);
        }

        $tpArticle  = new TPArticle();
        $comment    = $tpArticle->getArticleComment($comment);
		if(is_array($comment)) {
			if(allowedTo('tp_articles') || $comment['member_id'] == $context['user']['id']) {
                $context['TPortal']['comment_edit'] = array(
                    'id' => $row['id'],
                    'title' => $row['value1'],
                    'body' => $row['value2'],
                );
                $context['TPortal']['subaction'] = 'editcomment';
                loadtemplate('TPmodules');
			}
			fatal_error($txt['tp-notallowed'], false);
		}
	}

}


function articleRate() {
    global $context, $smcFunc;

	// rating is underway
	if(isset($_POST['tp_article_rating_submit']) && $_POST['tp_article_type'] == 'article_rating') {
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
		if($smcFunc['db_num_rows']($request) > 0) {
			$row = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);

			$voters = array();
			$ratings = array();
			$voters = explode(',', $row[1]);
			$ratings = explode(',', $row[0]);
			// check if we haven't rated anyway
			if(!in_array($context['user']['id'], $voters)) {
				if($row[0] != '') {
					$new_voters     = $row[1].','.$context['user']['id'];
					$new_ratings    = $row[0].','.$_POST['tp_article_rating'];
				}
				else {
					$new_voters     = $context['user']['id'];
					$new_ratings    = $_POST['tp_article_rating'];
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

}

function articleAttachment() {
	tpattach();
}

function articleEdit() {
    global $context, $smcFunc;

	// edit your own articles?
    $what = substr($_GET['sa'], 11);
    if(!is_numeric($what)) {
        fatal_error($txt['tp-notanarticle'], false);
    }

    // get one article
    $context['TPortal']['subaction'] = 'editarticle';
    $context['TPortal']['editarticle'] = array();
    $request =  $smcFunc['db_query']('', '
        SELECT * FROM {db_prefix}tp_articles
        WHERE id = {int:artid} LIMIT 1',
        array('artid' => $what)
    );
    if($smcFunc['db_num_rows']($request)) {
        $row = $smcFunc['db_fetch_assoc']($request);
        // check permission
        if(!allowedTo('tp_articles') && $context['user']['id'] != $row['author_id'])
            fatal_error($txt['tp-articlenotallowed'], false);
        // can you edit your own then..?
        isAllowedTo('tp_editownarticle');

        if($row['locked'] == 1)
            fatal_error($txt['tp-articlelocked'], false);

        // Add in BBC editor before we call in template so the headers are there
        if($row['type'] == 'bbc')
        {
            $context['TPortal']['editor_id'] = 'tp_article_body' . $row['id'];
            TP_prebbcbox($context['TPortal']['editor_id'], strip_tags($row['body']));
        }
        if($row['type'] == 'html')
        {
        TPwysiwyg_setup();
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
            'id_theme' => $row['id_theme'],
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
        fatal_error($txt['tp-notanarticlefound'], false);

    if(loadLanguage('TPortalAdmin') == false)
        loadLanguage('TPortalAdmin', 'english');
    loadtemplate('TPmodules');
}

function articleShow() {
    global $context, $smcFunc;
	// show own articles?
    // not for guests
    if($context['user']['is_guest'])
        fatal_error($txt['tp-noarticlesfound'], false);

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
    $sort = $context['TPortal']['tpsort'] = (!empty($_GET['tpsort']) && in_array($_GET['tpsort'], array('date', 'id', 'subject'))) ? $_GET['tpsort'] : 'date';
    $context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpmod;sa=myarticles;tpsort=' . $sort, $mystart, $allmy, 15);

    $context['TPortal']['subaction'] = 'myarticles';
    $context['TPortal']['myarticles'] = array();
    $request2 =  $smcFunc['db_query']('', '
        SELECT id, subject, date, locked, approved, off FROM {db_prefix}tp_articles
        WHERE author_id = {int:author}
        ORDER BY {raw:sort} {raw:sorter} LIMIT {int:start}, 15',
        array('author' => $context['user']['id'], 
        'sort' => $sort,
        'sorter' => in_array($sort, array('subject')) ? ' ASC ' : ' DESC ',
        'start' => $mystart
        )
    );

    if($smcFunc['db_num_rows']($request2) > 0)
    {
        $context['TPortal']['myarticles']=array();
        while($row = $smcFunc['db_fetch_assoc']($request2))
        {
            $context['TPortal']['myarticles'][] = $row;
        }
        $smcFunc['db_free_result']($request2);
    }

    if(loadLanguage('TPortalAdmin') == false)
        loadLanguage('TPortalAdmin', 'english');
    loadtemplate('TPmodules');
}

function articleInsert() {
    global $context, $smcFunc, $sourcedir, $settings;

    require_once($sourcedir. '/TPcommon.php');

    // a BBC article?
    if(isset($_GET['bbc']) || $_GET['sa'] == 'addarticle_bbc') {
        isAllowedTo('tp_submitbbc');
        $context['TPortal']['submitbbc'] = 1;
        $context['html_headers'] .= '
            <script type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/editor.js?rc1"></script>';

        // Add in BBC editor before we call in template so the headers are there
        $context['TPortal']['editor_id'] = 'tp_article_body';
        TP_prebbcbox($context['TPortal']['editor_id']);
    }
    else {
        isAllowedTo('tp_submithtml');
    }

    if($_GET['sa'] == 'addarticle_html') {
        TPwysiwyg_setup();
    }
    $context['TPortal']['subaction'] = 'submitarticle';
    loadtemplate('TPmodules');
    $context['sub_template'] = 'submitarticle';
}

function articleInsertSuccess() {
	elseif($tpsub == 'submitsuccess') {
		$context['TPortal']['subaction'] = 'submitsuccess';
		loadtemplate('TPmodules');
		$context['sub_template'] = 'submitsuccess';
	}
	// article
	elseif($tpsub == 'submitarticle2') {
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
		$artoptions = 'date,title,author,linktree,category,catlist,comments,commentallow,views,rating,ratingallow,inherit,avatar,social';
		$name = $user_info['name'];
		$nameb = $context['user']['id'];
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
}

function articlePublish() {
    global $context;
    
    // promoting topics
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

function articleSave() {
	// save an article
    if(isset($_REQUEST['send'])) {
        foreach ($_POST as $what => $value) {
            if(substr($what, 0, 16) == 'tp_article_title') {
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
                    array('type' => 2, 'id_mem' => $context['user']['id'])
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
                        array(2, $context['user']['id'], $value, 0),
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
            fatal_error($txt['tp-notallowed'], false);
}

function articleUploadImage() {
    require_once($sourcedir.'/TPcommon.php');
    $name = TPuploadpicture( 'image', $context['user']['id'].'uid' );
    tp_createthumb( 'tp-images/'.$name, 50, 50, 'tp-images/thumbs/thumb_'.$name );
    $response['data'] = 'tp-images/'.$name;
    $response['success'] = 'true';
    header( 'Content-type: application/json' );
    echo json_encode( $response );
    // we want to just exit
    die;
}

?>
