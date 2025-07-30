<?php
/**
 * @package TinyPortal
 * @version 3.0.3
 * @author tinoest - http://www.tinyportal.net
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
use TinyPortal\Article as TPArticle;
use TinyPortal\Block as TPBlock;
use TinyPortal\Mentions as TPMentions;
use TinyPortal\Util as TPUtil;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

// TinyPortal module entrance
function TPArticle()
{
	global $settings, $context, $txt;

	if (loadLanguage('TParticle') == false) {
		loadLanguage('TParticle', 'english');
	}

	if (loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
	}

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// call the editor setup
	require_once SOURCEDIR . '/TPcommon.php';

	// clear the linktree first
	TPstrip_linktree();
}

function TPArticleActions(&$subActions)
{
	$subActions = array_merge(
		[
			'showcomments' => ['TPArticle.php', 'articleShowComments', []],
			'comment' => ['TPArticle.php', 'articleInsertComment', []],
			'killcomment' => ['TPArticle.php', 'articleDeleteComment', []],
			'editcomment' => ['TPArticle.php', 'articleEditComment', []],
			'rate_article' => ['TPArticle.php', 'articleRate', []],
			'editarticle' => ['TPArticle.php', 'articleEdit', []],
			'tpattach' => ['TPArticle.php', 'articleAttachment', []],
			'myarticles' => ['TPArticle.php', 'articleShow', []],
			'submitarticle' => ['TPArticle.php', 'articleNew', []],
			'addarticle_html' => ['TPArticle.php', 'articleNew', []],
			'addarticle_bbc' => ['TPArticle.php', 'articleNew', []],
			'publish' => ['TPArticle.php', 'articlePublish', []],
			'savearticle' => ['TPArticle.php', 'articleEdit', []],
			'uploadimage' => ['TPArticle.php', 'articleUploadImage', []],
			'submitsuccess' => ['TPArticle.php', 'articleSubmitSuccess', []],
		],
		$subActions
	);
}

function articleInsertComment()
{
	global $user_info, $context, $txt;

	// check the session
	checkSession('post');

	if (!allowedTo('tp_artcomment')) {
		fatal_error($txt['tp-nocomments'], false);
	}

	$commenter = $context['user']['id'];
	$article = TPUtil::filter('tp_article_id', 'post', 'int');
	$title = TPUtil::filter('tp_article_comment_title', 'post', 'string');
	$comment = substr(TPUtil::htmlspecialchars($_POST['tp_article_bodytext']), 0, 65536);
	if (!empty($context['TPortal']['allow_links_article_comments']) == 0 && TPUtil::hasLinks($comment)) {
		redirectexit('page=' . $article . '#tp-comment');
	}

	require_once SOURCEDIR . '/Subs-Post.php';
	preparsecode($comment);

	$tpArticle = TPArticle::getInstance();
	$comment_id = $tpArticle->insertArticleComment($commenter, $article, $comment, $title);
	if ($comment_id > 0) {
		$mention_data['id'] = $article;
		$mention_data['content'] = $comment;
		$mention_data['type'] = 'tp_comment';
		$mention_data['member_id'] = $user_info['id'];
		$mention_data['username'] = $user_info['username'];
		$mention_data['action'] = 'mention';
		$mention_data['event_title'] = 'Article Mention';
		$mention_data['text'] = 'Article';

		$tpMention = TPMentions::getInstance();
		$tpMention->addMention($mention_data);
	}

	// go back to the article
	redirectexit('page=' . $article . '#tp-comment');
}

function articleShowComments()
{
	global $smcFunc, $scripturl, $user_info, $txt, $context;

	if (isset($context['TPortal']['mystart'])) {
		$tpstart = is_numeric($context['TPortal']['mystart']) ? $context['TPortal']['mystart'] : 0;
	}

	$mylast = 0;
	$mylast = $user_info['last_login'];
	$comments_per_page = 15;
	$showall = false;
	if (isset($_GET['showall'])) {
		$showall = true;
	}

	$request = $smcFunc['db_query'](
		'',
		'
        SELECT COUNT(var.subject)
        FROM ({db_prefix}tp_comments AS var, {db_prefix}tp_articles AS art)
        WHERE var.item_type = {string:type}
        ' . ((!$showall || $mylast == 0) ? 'AND var.datetime > ' . $mylast : '') . '
        AND art.id = var.item_id',
		['type' => 'article_comment']
	);
	$check = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	$request = $smcFunc['db_query'](
		'',
		'
        SELECT art.subject AS pagename, memb.real_name AS author, art.author_id AS author_id, var.subject, var.comment, var.member_id,
        var.item_id, var.datetime, mem.real_name AS real_name,
        ' . ($user_info['is_guest'] ? '1' : '(COALESCE(log.item, 0) >= var.datetime)') . ' AS isRead
        FROM ({db_prefix}tp_comments AS var, {db_prefix}tp_articles AS art)
        LEFT JOIN {db_prefix}members AS memb ON (art.author_id = memb.id_member)
        LEFT JOIN {db_prefix}members AS mem ON (var.member_id = mem.id_member)
        LEFT JOIN {db_prefix}tp_data AS log ON (log.value = art.id AND log.type = 1 AND log.id_member = ' . $context['user']['id'] . ')
        WHERE var.item_type = {string:type}
        AND art.id = var.item_id
        ' . ((!$showall || $mylast == 0) ? 'AND var.datetime > {int:last}' : '') . '
        ORDER BY var.datetime DESC LIMIT {int:limit} OFFSET {int:start}',
		['type' => 'article_comment',
			'last' => $mylast, 
			'start' => $tpstart,
			'limit' => $comments_per_page]
	);

	$context['TPortal']['artcomments']['new'] = [];

	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['TPortal']['artcomments']['new'][] = [
				'page' => $row['item_id'],
				'pagename' => $row['pagename'],
				'subject' => $row['subject'],
				'title' => $row['subject'],
				'comment' => $row['comment'],
				'membername' => $row['real_name'],
				'time' => timeformat($row['datetime']),
				'author' => $row['author'],
				'author_id' => $row['author_id'],
				'member_id' => $row['member_id'],
				'is_read' => $row['isRead'],
				'replies' => $check[0],
			];
		}
		$smcFunc['db_free_result']($request);
	}

	// construct the pages
	$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tportal;sa=showcomments', $tpstart, $check[0], $comments_per_page);
	$context['TPortal']['unreadcomments'] = true;
	$context['TPortal']['showall'] = $showall;
	TPadd_linktree($scripturl . '?action=tportal;sa=showcomments', $txt['tp-showcomments']);
	($showall ? (TPadd_linktree($scripturl . '?action=tportal;sa=showcomments;showall', $txt['tp-showall'])) : '');
	($showall ? ($context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tportal;sa=showcomments;showall', $tpstart, $check[0], $comments_per_page)) : '');
	loadTemplate('TParticle');
	$context['sub_template'] = 'showcomments';
	if (loadLanguage('TParticle') == false) {
		loadLanguage('TParticle', 'english');
	};
}

function articleDeleteComment()
{
	global $context, $txt;

	if (!allowedTo('tp_artcomment')) {
		fatal_error($txt['tp-nocomments'], false);
	}

	// edit or deleting a comment?
	if ($context['user']['is_logged']) {
		// check that you indeed can edit or delete
		$comment = TPUtil::filter('comment', 'get', 'int');
		if (!is_numeric($comment)) {
			fatal_error($txt['tp-noadmincomments'], false);
		}

		$tpArticle = TPArticle::getInstance();
		$comment = $tpArticle->getArticleComment($comment);
		if (is_array($comment)) {
			$tpArticle->deleteArticleComment($comment['id']);
			redirectexit('page=' . $comment['item_id']);
		}
	}
}

function articleEditComment()
{
	global $context, $txt;

	if (!allowedTo('tp_artcomment')) {
		fatal_error($txt['tp-nocomments'], false);
	}

	if ($context['user']['is_logged']) {
		// check that you indeed can edit or delete
		$comment = substr($_GET['sa'], 11);
		if (!is_numeric($comment)) {
			fatal_error($txt['tp-noadmincomments'], false);
		}

		$tpArticle = TPArticle::getInstance();
		$comment = $tpArticle->getArticleComment($comment);
		if (is_array($comment)) {
			if (allowedTo('tp_articles') || $comment['member_id'] == $context['user']['id']) {
				$context['TPortal']['comment_edit'] = [
					'id' => $row['id'],
					'title' => $row['value1'],
					'body' => $row['value2'],
				];
				$context['sub_template'] = 'editcomment';
				loadTemplate('TParticle');
				if (loadLanguage('TParticle') == false) {
					loadLanguage('TParticle', 'english');
				};
			}
			fatal_error($txt['tp-notallowed'], false);
		}
	}
}

function articleRate()
{
	global $context, $smcFunc;

	// rating is underway
	if (isset($_POST['tp_article_rating_submit']) && $_POST['tp_article_type'] == 'article_rating') {
		// check the session
		checkSession('post');

		$commenter = $context['user']['id'];
		$article = $_POST['tp_article_id'];
		// check if the article indeed exists
		$request = $smcFunc['db_query'](
			'',
			'
			SELECT rating, voters FROM {db_prefix}tp_articles
			WHERE id = {int:artid}',
			['artid' => $article]
		);
		if ($smcFunc['db_num_rows']($request) > 0) {
			$row = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);

			$voters = [];
			$ratings = [];
			$voters = explode(',', $row[1]);
			$ratings = explode(',', $row[0]);
			// check if we haven't rated anyway
			if (!in_array($context['user']['id'], $voters)) {
				if ($row[0] != '') {
					$new_voters = $row[1] . ',' . $context['user']['id'];
					$new_ratings = $row[0] . ',' . $_POST['tp_article_rating'];
				}
				else {
					$new_voters = $context['user']['id'];
					$new_ratings = $_POST['tp_article_rating'];
				}
				// update ratings and raters
				$smcFunc['db_query'](
					'',
					'
					UPDATE {db_prefix}tp_articles
					SET rating = {string:rate} WHERE id = {int:artid}',
					['rate' => $new_ratings, 'artid' => $article]
				);
				$smcFunc['db_query'](
					'',
					'
					UPDATE {db_prefix}tp_articles
					SET voters = {string:vote}
					WHERE id = {int:artid}',
					['vote' => $new_voters, 'artid' => $article]
				);
			}
			// go back to the article
			redirectexit('page=' . $article);
		}
	}
}

function articleAttachment()
{
	tpattach();
}

function articleEdit()
{
	global $context, $smcFunc;

	checkSession('post');
	isAllowedTo(['tp_articles', 'tp_editownarticle', 'tp_submitbbc', 'tp_submithtml']);

	$options = [];
	$article_data = [];
	if (allowedTo('tp_alwaysapproved')) {
		$article_data['approved'] = 1;
	} // No approval needed
	else {
		$article_data['approved'] = 0;
	} // Preset to false

	foreach ($_POST as $what => $value) {
		if (substr($what, 0, 11) == 'tp_article_') {
			$setting = substr($what, 11);
			if (substr($setting, 0, 8) == 'options_') {
					$options[] = substr($setting, 8);
			}
			else {
				switch ($setting) {
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
						$request = $smcFunc['db_query'](
							'',
							'
							SELECT value3 FROM {db_prefix}tp_variables
							WHERE id = {int:varid} LIMIT 1',
							['varid' => is_numeric($value) ? $value : 0]
						);
						if ($smcFunc['db_num_rows']($request) > 0) {
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
							require_once SOURCEDIR . '/Subs-Editor.php';
							$_REQUEST['tp_article_body'] = html_to_bbc($_REQUEST['tp_article_body']);
							// We need to unhtml it now as it gets done shortly.
							$_REQUEST['tp_article_body'] = un_htmlspecialchars($_REQUEST['tp_article_body']);
							// We need this for everything else.
							if ($setting == 'body') {
								$value = $_POST['tp_article_body'] = $_REQUEST['tp_article_body'];
							}
							elseif ($settings == 'intro') {
								$value = $_POST['tp_article_intro'] = $_REQUEST['tp_article_intro'];
							}
						}
						// in case of HTML article we need to check it
						if (isset($_POST['tp_article_body_pure']) && isset($_POST['tp_article_body_choice'])) {
							if ($_POST['tp_article_body_choice'] == 0) {
								if ($setting == 'body') {
									$value = $_POST['tp_article_body_pure'];
								}
								elseif ($setting == 'intro') {
									$value = $_POST['tp_article_intro'];
								}
							}
							// save the choice too
							$request = $smcFunc['db_query'](
								'',
								'
								SELECT id FROM {db_prefix}tp_variables
								WHERE subtype2 = {int:sub2}
								AND type = {string:type} LIMIT 1',
								['sub2' => $where, 'type' => 'editorchoice']
							);
							if ($smcFunc['db_num_rows']($request) > 0) {
								$row = $smcFunc['db_fetch_assoc']($request);
								$smcFunc['db_free_result']($request);
								$smcFunc['db_query'](
									'',
									'
									UPDATE {db_prefix}tp_variables
									SET value1 = {string:val1}
									WHERE subtype2 = {int:sub2}
									AND type = {string:type}',
									['val1' => $_POST['tp_article_body_choice'], 'sub2' => $where, 'type' => 'editorchoice']
								);
							}
							else {
								$smcFunc['db_insert'](
									'INSERT',
									'{db_prefix}tp_variables',
									['value1' => 'string', 'type' => 'string', 'subtype2' => 'int'],
									[$_POST['tp_article_body_choice'], 'editorchoice', $where],
									['id']
								);
							}
						}
						// BBC we need to encode quotes
						if (($_REQUEST['tp_article_type'] == 'bbc') && ($setting == 'body')) {
							$value = $smcFunc['htmlspecialchars']($value, ENT_QUOTES);
						}
						$article_data[$setting] = $value;
						break;
					case 'day':
					case 'month':
					case 'year':
					case 'minute':
					case 'hour':
						$timestamp = mktime($_POST['tp_article_hour'], $_POST['tp_article_minute'], 0, $_POST['tp_article_month'], $_POST['tp_article_day'], $_POST['tp_article_year']);
						if (!isset($savedtime)) {
							$article_data['date'] = $timestamp;
						}
						break;
					case 'timestamp':
						if (!isset($savedtime)) {
							$article_data['date'] = empty($_POST['tp_article_timestamp']) ? time() : $_POST['tp_article_timestamp'];
						}
						break;
					case 'pubstartday':
					case 'pubstartmonth':
					case 'pubstartyear':
					case 'pubstartminute':
					case 'pubstarthour':
					case 'pub_start':
						if (empty($_POST['tp_article_pubstarthour']) && empty($_POST['tp_article_pubstartminute']) && empty($_POST['tp_article_pubstartmonth']) && empty($_POST['tp_article_pubstartday']) && empty($_POST['tp_article_pubstartyear'])) {
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
						if (empty($_POST['tp_article_pubendhour']) && empty($_POST['tp_article_pubendminute']) && empty($_POST['tp_article_pubendmonth']) && empty($_POST['tp_article_pubendday']) && empty($_POST['tp_article_pubendyear'])) {
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
	if (array_key_exists('tp_article_illupload', $_FILES) && file_exists($_FILES['tp_article_illupload']['tmp_name'])) {
		$name = TPuploadpicture('tp_article_illupload', '', $context['TPortal']['icon_max_size'], 'jpg,gif,png', 'tp-files/tp-articles/illustrations');
		tp_createthumb('tp-files/tp-articles/illustrations/' . $name, $context['TPortal']['icon_width'], $context['TPortal']['icon_height'], 'tp-files/tp-articles/illustrations/s_' . $name);
		$article_data['illustration'] = $name;
	}

	$where = TPUtil::filter('article', 'request', 'string');
	$tpArticle = TPArticle::getInstance();
	if (empty($where)) {
		// We are inserting
		$where = $tpArticle->insertArticle($article_data);
	}
	else {
		// We are updating
		$tpArticle->updateArticle((int)$where, $article_data);
	}
	// Update the approved status
	if ($article_data['approved'] == 1) {
		$smcFunc['db_query'](
			'',
			'
			DELETE FROM {db_prefix}tp_variables
			WHERE type = {string:type}
			AND value5 = {int:val5}',
			['type' => 'art_not_approved', 'val5' => $where]
		);
	}
	elseif (empty($where)) {
		$smcFunc['db_insert'](
			'insert',
			'{db_prefix}tp_variables',
			[
				'type' => 'string',
				'value5' => 'int'
			],
			[
				'art_not_approved',
				$where
			],
			[
				'id'
			]
		);
	}
	unset($tpArticle);
	// check if uploaded picture
	if (isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name'])) {
		$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'] . 'uid', $context['TPortal']['image_max_size'], null, $context['TPortal']['image_upload_path']);
		tp_createthumb($context['TPortal']['image_upload_path'] . '/' . $name, 50, 50, $context['TPortal']['image_upload_path'] . '/thumbs/thumb_' . $name);
	}
	// if this was a new article
	if (array_key_exists('tp_article_approved', $_POST) && $_POST['tp_article_approved'] == 1 && $_POST['tp_article_off'] == 0) {
		tp_recordevent($timestamp, $_POST['tp_article_authorid'], 'tp-createdarticle', 'page=' . $where, 'Creation of new article.', (isset($allowed) ? $allowed : 0), $where);
	}

	if (array_key_exists('tpadmin_form', $_POST)) {
		return $_POST['tpadmin_form'] . ';article=' . $where;
	}
	else {
		redirectexit('action=tportal;sa=submitsuccess');
	}
}

function articleShow()
{
	global $context, $smcFunc, $scripturl, $txt;

	// show own articles?
	// not for guests
	if ($context['user']['is_guest']) {
		fatal_error($txt['tp-noarticlesfound'], false);
	}

	$articles_per_page = 20;

	// get all articles
	$request = $smcFunc['db_query'](
		'',
		'
        SELECT COUNT(*) FROM {db_prefix}tp_articles
        WHERE author_id = {int:author}',
		['author' => $context['user']['id']]
	);
	$row = $smcFunc['db_fetch_row']($request);
	$allmy = $row[0];

	$mystart = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
	// sorting?
	$sort = $context['TPortal']['tpsort'] = (!empty($_GET['tpsort']) && in_array($_GET['tpsort'], ['date', 'id', 'subject'])) ? $_GET['tpsort'] : 'date';
	$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tportal;sa=myarticles;tpsort=' . $sort, $mystart, $allmy, $articles_per_page);

	$context['TPortal']['subaction'] = 'myarticles';
	$context['TPortal']['myarticles'] = [];
	$request2 = $smcFunc['db_query'](
		'',
		'
        SELECT id, subject, date, locked, approved, off FROM {db_prefix}tp_articles
        WHERE author_id = {int:author}
        ORDER BY {raw:sort} {raw:sorter} LIMIT {int:start}, {int:limit}',
		['author' => $context['user']['id'],
			'sort' => $sort,
			'sorter' => in_array($sort, ['subject']) ? ' ASC ' : ' DESC ',
			'start' => $mystart,
			'limit' => $articles_per_page
		]
	);

	if ($smcFunc['db_num_rows']($request2) > 0) {
		$context['TPortal']['myarticles'] = [];
		while ($row = $smcFunc['db_fetch_assoc']($request2)) {
			$context['TPortal']['myarticles'][] = $row;
		}
		$smcFunc['db_free_result']($request2);
	}

	if (loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
	}

	loadTemplate('TParticle');
	$context['sub_template'] = 'showarticle';
}

function articleNew()
{
	global $context, $smcFunc, $settings;

	require_once SOURCEDIR . '/TPcommon.php';

	// a BBC article?
	if (isset($_GET['bbc']) || $_GET['sa'] == 'addarticle_bbc') {
		isAllowedTo('tp_submitbbc');
		$context['TPortal']['articletype'] = 'bbc';
		$context['html_headers'] .= '
            <script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/editor.js?' . TPVERSION . '"></script>';

		// Add in BBC editor before we call in template so the headers are there
		$context['TPortal']['editor_id'] = 'tp_article_body';
		TP_prebbcbox($context['TPortal']['editor_id']);
	}
	elseif ($_GET['sa'] == 'addarticle_html') {
		$context['TPortal']['articletype'] = 'html';
		isAllowedTo('tp_submithtml');
		TPwysiwyg_setup();
	}
	else {
		redirectexit('action=forum');
	}

	$context['TPortal']['subaction'] = 'submitarticle';
	if (loadLanguage('TParticle') == false) {
		loadLanguage('TParticle', 'english');
	}
	if (loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
	}
	loadTemplate('TParticle');
	$context['sub_template'] = 'submitarticle';
}

function articleSubmitSuccess()
{
	global $context;

	$context['TPortal']['subaction'] = 'submitsuccess';
	loadTemplate('TParticle');
	if (loadLanguage('TParticle') == false) {
		loadLanguage('TParticle', 'english');
	}
	$context['sub_template'] = 'submitsuccess';
}

function articlePublish()
{
	global $context;

	// promoting topics
	if (!isset($_GET['t'])) {
		redirectexit('action=forum');
	}

	$t = is_numeric($_GET['t']) ? $_GET['t'] : 0;

	if (empty($t)) {
		redirectexit('action=forum');
	}

	isAllowedTo('tp_settings');
	$existing = explode(',', $context['TPortal']['frontpage_topics']);
	if (in_array($t, $existing)) {
		unset($existing[array_search($t, $existing)]);
	}
	else {
		$existing[] = $t;
	}

	$newstring = implode(',', $existing);
	if (substr($newstring, 0, 1) == ',') {
		$newstring = substr($newstring, 1);
	}

	updateTPSettings(['frontpage_topics' => $newstring]);

	redirectexit('topic=' . $t . '.0');
}

function articleUploadImage()
{
	global $context, $boarddir, $boardurl;

	require_once SOURCEDIR . '/TPcommon.php';
	$name = TPuploadpicture('image', $context['user']['id'] . 'uid', $context['TPortal']['image_max_size'], null, $context['TPortal']['image_upload_path']);
	tp_createthumb($context['TPortal']['image_upload_path'] . $name, 50, 50, $context['TPortal']['image_upload_path'] . 'thumbs/thumb_' . $name);
	$response['data'] = str_replace($boarddir, $boardurl, $context['TPortal']['image_upload_path']) . $name;
	$response['success'] = 'true';
	header('Content-type: application/json');
	echo json_encode($response);
	// we want to just exit
	die;
}

function articleAjax()
{
	global $context, $boarddir, $boardurl, $smcFunc;

	$tpArticle = TPArticle::getInstance();

	// first check any ajax stuff
	if (isset($_GET['arton'])) {
		checksession('get');
		$id = is_numeric($_GET['arton']) ? $_GET['arton'] : '0';
		$col = 'off';
		$tpArticle->toggleColumnArticle($id, $col);
	}
	elseif (isset($_GET['artlock'])) {
		checksession('get');
		$id = is_numeric($_GET['artlock']) ? $_GET['artlock'] : '0';
		$col = 'locked';
		$tpArticle->toggleColumnArticle($id, $col);
	}
	elseif (isset($_GET['artsticky'])) {
		checksession('get');
		$id = is_numeric($_GET['artsticky']) ? $_GET['artsticky'] : '0';
		$col = 'sticky';
		$tpArticle->toggleColumnArticle($id, $col);
	}
	elseif (isset($_GET['artfront'])) {
		checksession('get');
		$id = is_numeric($_GET['artfront']) ? $_GET['artfront'] : '0';
		$col = 'frontpage';
		$tpArticle->toggleColumnArticle($id, $col);
	}
	elseif (isset($_GET['artfeat'])) {
		checksession('get');
		$id = is_numeric($_GET['artfeat']) ? $_GET['artfeat'] : '0';
		$col = 'featured';
		$tpArticle->toggleColumnArticle($id, $col);
	}
	elseif (isset($_GET['catdelete'])) {
		checksession('get');
		$what = is_numeric($_GET['catdelete']) ? $_GET['catdelete'] : '0';
		if ($what > 0) {
			// first get info
			$request = $smcFunc['db_query'](
				'',
				'
				SELECT id, value2 FROM {db_prefix}tp_variables
				WHERE id = {int:varid} LIMIT 1',
				['varid' => $what]
			);
			$row = $smcFunc['db_fetch_assoc']($request);
			$smcFunc['db_free_result']($request);

			$newcat = !empty($row['value2']) ? $row['value2'] : 0;

			$smcFunc['db_query'](
				'',
				'
				UPDATE {db_prefix}tp_variables
				SET value2 = {string:val2}
				WHERE value2 = {string:varid}',
				[
					'val2' => $newcat, 'varid' => $what
				]
			);

			$smcFunc['db_query'](
				'',
				'
				DELETE FROM {db_prefix}tp_variables
				WHERE id = {int:varid}',
				['varid' => $what]
			);
			$smcFunc['db_query'](
				'',
				'
				UPDATE {db_prefix}tp_articles
				SET category = {int:cat}
				WHERE category = {int:catid}',
				['cat' => $newcat, 'catid' => $what]
			);
			redirectexit('action=tpadmin;sa=categories');
		}
		else {
			redirectexit('action=tpadmin;sa=categories');
		}
	}
	elseif (isset($_GET['artdelete'])) {
		checksession('get');
		$what = is_numeric($_GET['artdelete']) ? $_GET['artdelete'] : '0';
		if (empty($cu)) {
			$cu = is_numeric($_GET['cu']) ? $_GET['cu'] : '';
			if ($cu == -1) {
				$strays = true;
				$cu = '';
			}
		}
		if ($what > 0) {
			$smcFunc['db_query'](
				'',
				'
				DELETE FROM {db_prefix}tp_articles
				WHERE id = {int:artid}',
				['artid' => $what]
			);
			$smcFunc['db_query'](
				'',
				'
				DELETE FROM {db_prefix}tp_variables
				WHERE value5 = {int:artid}',
				['artid' => $what]
			);
		}
		redirectexit('action=tpadmin' . (!empty($cu) ? ';sa=articles;cu=' . $cu : '') . (isset($strays) ? ';sa=strays' . $cu : ';sa=articles'));
	}

	unset($tpArticle);
}
