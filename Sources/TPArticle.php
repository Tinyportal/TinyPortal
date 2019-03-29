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

if (!defined('SMF')) {
    die('Hacking attempt...');
}

// TinyPortal module entrance
function TPArticle() {{{

	global $settings, $context, $txt;

	if(loadLanguage('TParticle') == false) {
		loadLanguage('TParticle', 'english');
    }

	if(loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
    }

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// call the editor setup
	require_once(SOURCEDIR. '/TPcommon.php');

	// clear the linktree first
	TPstrip_linktree();

}}}

function TPArticleActions(&$subActions) {{{

    $subActions = array_merge(
        array (
            'showcomments'      => array('TPArticle.php', 'articleShowComments', array()),
            'comment'           => array('TPArticle.php', 'articleInsertComment', array()),
            'killcomment'       => array('TPArticle.php', 'articleDeleteComment', array()),
            'editcomment'       => array('TPArticle.php', 'articleEditComment', array()),
            'rate_article'      => array('TPArticle.php', 'articleRate', array()),
            'editarticle'       => array('TPArticle.php', 'articleEdit', array()),
            'tpattach'          => array('TPArticle.php', 'articleAttachment', array()),
            'myarticles'        => array('TPArticle.php', 'articleShow', array()),
            'submitarticle'     => array('TPArticle.php', 'articleNew', array()),
            'addarticle_html'   => array('TPArticle.php', 'articleNew', array()),
            'addarticle_bbc'    => array('TPArticle.php', 'articleNew', array()),
            'publish'           => array('TPArticle.php', 'articlePublish', array()),
            'savearticle'       => array('TPArticle.php', 'articleEdit', array()),
            'uploadimage'       => array('TPArticle.php', 'articleUploadImage', array()),
            'submitsuccess'     => array('TPArticle.php', 'articleSubmitSuccess', array()),
        ),
        $subActions
    );

}}}

function articleInsertComment() {{{

    global $context, $txt;

    // check the session
    checkSession('post');

    if (!allowedTo('tp_artcomment')) {
        fatal_error($txt['tp-nocomments'], false);
    }

    $commenter  = $context['user']['id'];
	$article    = TPUtil::filter('tp_article_id', 'post', 'int');
	$title      = TPUtil::filter('tp_article_comment_title', 'post', 'string');
    $comment    = substr(TPUtil::htmlspecialchars($_POST['tp_article_bodytext']), 0, 65536);
    if(!empty($context['TPortal']['allow_links_article_comments']) && TPUtil::hasLinks($comment)) {
        redirectexit('page='.$article.'#tp-comment');
    }

    require_once(SOURCEDIR.'/Subs-Post.php');
    preparsecode($comment);

    $tpArticle = new TPArticle();
    if($tpArticle->insertArticleComment($commenter, $article, $comment, $title))  {
        // go back to the article
        redirectexit('page='.$article.'#tp-comment');
    }

}}}

function articleShowComments() {{{
    global $smcFunc, $scripturl, $user_info, $txt, $context;

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
    $context['TPortal']['pageindex'] = TPageIndex($scripturl.'?action=tportal;sa=showcomments', $tpstart, $check[0], 15);
    $context['TPortal']['unreadcomments'] = true;
    $context['TPortal']['showall'] = $showall;
    $context['TPortal']['sub_template'] = 'showcomments';
    TPadd_linktree($scripturl.'?action=tportal;sa=showcomments' . ($showall ? ';showall' : '')  , $txt['tp-showcomments']);
    loadTemplate('TParticle');
    if(loadLanguage('TParticle') == false) {
        loadLanguage('TParticle', 'english');
    };

}}}

function articleDeleteComment() {{{

    global $context, $txt;

	// edit or deleting a comment?
	if($context['user']['is_logged']) {
		// check that you indeed can edit or delete
	    $comment = TPUtil::filter('comment', 'get', 'int');
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

}}}

function articleEditComment() {{{
    global $context, $txt;

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
                $context['TPortal']['sub_template'] = 'editcomment';
                loadTemplate('TParticle');
                if(loadLanguage('TParticle') == false) {
                    loadLanguage('TParticle', 'english');
                };
			}
			fatal_error($txt['tp-notallowed'], false);
		}
	}

}}}

function articleRate() {{{
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

}}}

function articleAttachment() {{{
	tpattach();
}}}

function articleEdit() {{{
	global $context, $smcFunc;

	checkSession('post');
	isAllowedTo(array('tp_articles', 'tp_submitbbc', 'tp_submithtml'));

	$options        = array();
	$article_data   = array();
    $article_data['approved'] = 0; // Preset to false
	foreach($_POST as $what => $value) {
		if(substr($what, 0, 11) == 'tp_article_') {
			$setting = substr($what, 11);
			if(substr($setting, 0, 8) == 'options_') {
				if(substr($setting, 0, 19) == 'options_lblockwidth' || substr($setting,0,19) == 'options_rblockwidth') {
					$options[] = substr($setting, 8).$value;
				}
				else {
					$options[] = substr($setting, 8);
				}
			} 
			else { 
				switch($setting) {
					case 'body_mode':
					case 'intro_mode':
					case 'illupload':
					case 'intro_pure':
					case 'body_pure':
					case 'body_choice':
						// We ignore all these
						break;
                    case 'title':
						$article_data['subject'] = $value;
                        break;
					case 'authorid':
						$article_data['author_id'] = $value;
						break;
					case 'idtheme':
						$article_data['id_theme'] = $value;
						break;
					case 'category':
						// for the event, get the allowed
						$request = $smcFunc['db_query']('', '
							SELECT value3 FROM {db_prefix}tp_variables
							WHERE id = {int:varid} LIMIT 1',
							array('varid' => is_numeric($value) ? $value : 0 )
						);
						if($smcFunc['db_num_rows']($request) > 0) {
							$row = $smcFunc['db_fetch_assoc']($request);
							$allowed = $row['value3'];
							$smcFunc['db_free_result']($request);
						    $article_data['category'] = $value;
						}
						break;
					case 'shortname':
						$article_data[$setting] = htmlspecialchars(str_replace(' ', '-', $value), ENT_QUOTES);
						break;
					case 'intro':
					case 'body':
						// If we came from WYSIWYG then turn it back into BBC regardless.
						if (!empty($_REQUEST['tp_article_body_mode']) && isset($_REQUEST['tp_article_body'])) {
							require_once($sourcedir . '/Subs-Editor.php');
							$_REQUEST['tp_article_body'] = html_to_bbc($_REQUEST['tp_article_body']);
							// We need to unhtml it now as it gets done shortly.
							$_REQUEST['tp_article_body'] = un_htmlspecialchars($_REQUEST['tp_article_body']);
							// We need this for everything else.
							if($setting == 'body') {
								$value = $_POST['tp_article_body'] = $_REQUEST['tp_article_body'];
							}
							elseif ($settings == 'intro') {
								$value = $_POST['tp_article_intro'] = $_REQUEST['tp_article_intro'];
							}
						}
						// in case of HTML article we need to check it
						if(isset($_POST['tp_article_body_pure']) && isset($_POST['tp_article_body_choice'])) {
							if($_POST['tp_article_body_choice'] == 0) {
								if ($setting == 'body') {
									$value = $_POST['tp_article_body_pure'];
								}
								elseif ($setting == 'intro') {
									$value = $_POST['tp_article_intro'];
								}
							}
							// save the choice too
							$request = $smcFunc['db_query']('', '
								SELECT id FROM {db_prefix}tp_variables
								WHERE subtype2 = {int:sub2}
								AND type = {string:type} LIMIT 1',
								array('sub2' => $where, 'type' => 'editorchoice')
							);
							if($smcFunc['db_num_rows']($request) > 0) {
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
							else {
								$smcFunc['db_insert']('INSERT',
									'{db_prefix}tp_variables',
									array('value1' => 'string', 'type' => 'string', 'subtype2' => 'int'),
									array($_POST['tp_article_body_choice'], 'editorchoice', $where),
									array('id')
								);
							}
						}
						$article_data[$setting] = $value;
						break;
					case 'day':
					case 'month':
					case 'year':
					case 'minute':
					case 'hour':
						$timestamp = mktime($_POST['tp_article_hour'], $_POST['tp_article_minute'], 0, $_POST['tp_article_month'], $_POST['tp_article_day'], $_POST['tp_article_year']);
						if(!isset($savedtime)) {
							$article_data['date'] = $timestamp;
						}
						break;
					case 'timestamp':
						if(!isset($savedtime)) {
						    $article_data['date'] = empty($_POST['tp_article_timestamp']) ? time() : $_POST['tp_article_timestamp'];
                        }
                        break;
					case 'pubstartday':
					case 'pubstartmonth':
					case 'pubstartyear':
					case 'pubstartminute':
					case 'pubstarthour':
					case 'pub_start':
						if(empty($_POST['tp_article_pubstarthour']) && empty($_POST['tp_article_pubstartminute']) && empty($_POST['tp_article_pubstartmonth']) && empty($_POST['tp_article_pubstartday']) && empty($_POST['tp_article_pubstartyear'])) {
							$article_data['pub_start'] = 0;
						}
						else {
							$timestamp = mktime($_POST['tp_article_pubstarthour'], $_POST['tp_article_pubstartminute'], 0, $_POST['tp_article_pubstartmonth'], $_POST['tp_article_pubstartday'], $_POST['tp_article_pubstartyear']);
							$article_data['pub_start'] = $timestamp;
						}
					break;
					case 'pubendday':
					case 'pubendmonth':
					case 'pubendyear':
					case 'pubendminute':
					case 'pubendhour':
					case 'pub_end':
						if(empty($_POST['tp_article_pubendhour']) && empty($_POST['tp_article_pubendminute']) && empty($_POST['tp_article_pubendmonth']) && empty($_POST['tp_article_pubendday']) && empty($_POST['tp_article_pubendyear'])) {
							$article_data['pub_end'] = 0;
						}
						else {
							$timestamp = mktime($_POST['tp_article_pubendhour'], $_POST['tp_article_pubendminute'], 0, $_POST['tp_article_pubendmonth'], $_POST['tp_article_pubendday'], $_POST['tp_article_pubendyear']);
							$article_data['pub_end'] = $timestamp;
						}
						break;
					default:
						$article_data[$setting] = $value;
						break;
				}
			}
		}
	}
	$article_data['options'] = implode(',', $options);
	// check if uploads are there
	if(array_key_exists('tp_article_illupload', $_FILES) && file_exists($_FILES['tp_article_illupload']['tmp_name'])) {
		$name = TPuploadpicture('tp_article_illupload', '', '180', 'jpg,gif,png', 'tp-files/tp-articles/illustrations');
		tp_createthumb('tp-files/tp-articles/illustrations/'. $name, 128, 128, 'tp-files/tp-articles/illustrations/s_'. $name);
		$article_data['illustration'] = $name;
	}

	$where      = TPUtil::filter('article', 'request', 'string');
	$tpArticle  = new TPArticle();
	if(empty($where)) {
		// We are inserting
		$where = $tpArticle->insertArticle($article_data);
	}
	else {
		// We are updating
		$tpArticle->updateArticle((int)$where, $article_data);
	}
	// Update the approved status
	if($article_data['approved'] == 1) {
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}tp_variables
			WHERE type = {string:type}
			AND value5 = {int:val5}',
			array('type' => 'art_not_approved', 'val5' => $where)
		);
	}
	elseif(empty($where)) {
		$smcFunc['db_insert']('insert',
			'{db_prefix}tp_variables',
			array (
				'type' => 'string', 
				'value5' => 'int'
			),
			array (  
				'art_not_approved',
				$where
			),
			array ( 
				'id' 
			)
		);
	}
	unset($tpArticle);
	// check if uploadad picture
	if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name'])) {
		$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
		tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
	}
	// if this was a new article
	if(array_key_exists('tp_article_approved', $_POST) && $_POST['tp_article_approved'] == 1 && $_POST['tp_article_off'] == 0) {
		tp_recordevent($timestamp, $_POST['tp_article_authorid'], 'tp-createdarticle', 'page=' . $where, 'Creation of new article.', (isset($allowed) ? $allowed : 0) , $where);
	}

    if(array_key_exists('tpadmin_form', $_POST)) {
	    return $_POST['tpadmin_form'].';article='.$where;
    }
    else {
        redirectexit('action=tportal;sa=submitsuccess');
    }

}}}

function articleShow() {{{
    global $context, $smcFunc, $scripturl, $txt;

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
    $context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tportal;sa=myarticles;tpsort=' . $sort, $mystart, $allmy, 15);

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

    if($smcFunc['db_num_rows']($request2) > 0) {
        $context['TPortal']['myarticles']=array();
        while($row = $smcFunc['db_fetch_assoc']($request2)) {
            $context['TPortal']['myarticles'][] = $row;
        }
        $smcFunc['db_free_result']($request2);
    }

    if(loadLanguage('TPortalAdmin') == false) {
        loadLanguage('TPortalAdmin', 'english');
    }
    loadTemplate('TParticle');

}}}

function articleNew() {{{
    global $context, $smcFunc, $settings;

    require_once(SOURCEDIR. '/TPcommon.php');

    // a BBC article?
    if(isset($_GET['bbc']) || $_GET['sa'] == 'addarticle_bbc') {
        isAllowedTo('tp_submitbbc');
        $context['TPortal']['articletype'] = 'bbc';
        $context['html_headers'] .= '
            <script type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/editor.js?'.TPVERSION.'"></script>';

        // Add in BBC editor before we call in template so the headers are there
        $context['TPortal']['editor_id'] = 'tp_article_body';
        TP_prebbcbox($context['TPortal']['editor_id']);
    }
    else if($_GET['sa'] == 'addarticle_html') {
        $context['TPortal']['articletype'] = 'html';
        isAllowedTo('tp_submithtml');
        TPwysiwyg_setup();
    }
    else {
        redirectexit('action=forum');
    }

    $context['TPortal']['subaction'] = 'submitarticle';
	if(loadLanguage('TParticle') == false) {
		loadLanguage('TParticle', 'english');
    }
	if(loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
    }
    loadTemplate('TParticle');
    $context['sub_template'] = 'submitarticle';

}}}

function articleSubmitSuccess() {{{
    global $context;

    $context['TPortal']['subaction'] = 'submitsuccess';
    loadTemplate('TParticle');
	if(loadLanguage('TParticle') == false) {
		loadLanguage('TParticle', 'english');
    }
    $context['sub_template'] = 'submitsuccess';

}}}

function articleSubmit() {{{
    global $context, $user_info, $smcFunc, $txt;

    // article
    require_once(SOURCEDIR. '/TPcommon.php');

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
    if($artpp == '0') {
        $smcFunc['db_insert']('INSERT',
            '{db_prefix}tp_variables',
            array('value1' => 'string', 'value2' => 'string', 'value3' => 'string', 'type' => 'string', 'value4'  => 'string', 'value5' => 'int'),
            array($title, $now, '', 'art_not_approved', '' , $newitem),
            array('id')
        );
    }

    if(isset($_POST['pre_approved'])) {
        redirectexit('action=tportal;sa=addsuccess');
    }

    if(allowedTo('tp_editownarticle') && !allowedTo('tp_articles')) {
        // did we get a picture as well?
        if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name'])) {
            $name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
            tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
        }
        redirectexit('action=tportal;sa=editarticle'.$newitem);
    }
    elseif(allowedTo('tp_articles')) {
        // did we get a picture as well?
        if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name'])) {
            $name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
            tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
        }
        redirectexit('action=tpadmin;sa=editarticle'.$newitem);
    }
    else {
        redirectexit('action=tportal;sa=submitsuccess');
    }

}}}

function articlePublish() {{{
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

}}}

function articleUploadImage() {{{
    $context;

    require_once(SOURCEDIR.'/TPcommon.php');
    $name = TPuploadpicture( 'image', $context['user']['id'].'uid' );
    tp_createthumb( 'tp-images/'.$name, 50, 50, 'tp-images/thumbs/thumb_'.$name );
    $response['data'] = 'tp-images/'.$name;
    $response['success'] = 'true';
    header( 'Content-type: application/json' );
    echo json_encode( $response );
    // we want to just exit
    die;

}}}

?>
