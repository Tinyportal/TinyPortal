<?php
/**
 * @package TinyPortal
 * @version 2.3.0
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
use TinyPortal\Util as TPUtil;
use TinyPortal\Database as TPDatabase;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

function TPSearch()
{
	global $scripturl, $txt, $context;

	isAllowedTo('tp_can_search');

	if (loadLanguage('TPmodules') == false) {
		loadLanguage('TPmodules', 'english');
	}

	if ((is_array($_POST) && count($_POST) > 0) || (!empty($_REQUEST['params']))) {
		TPSearchArticle();
		TPadd_linktree($scripturl . '?action=tportal;sa=searcharticle', $txt['tp-searcharticles2']);
		loadtemplate('TPSearch');
		$context['sub_template'] = 'article_search_results';
	}
	else {
		TPadd_linktree($scripturl . '?action=tportal;sa=searcharticle', $txt['tp-searcharticles2']);
		loadtemplate('TPSearch');
		$context['sub_template'] = 'article_search_form';
	}
}

function TPSearchActions(&$subActions)
{
	$subActions = array_merge(
		[
			'searcharticle' => ['TPSearch.php', 'TPSearch', []],
			'searcharticle2' => ['TPSearch.php', 'TPSearch', []],
		],
		$subActions
	);
}

function TPSearchArticle()
{
	global $scripturl, $txt, $context;

	isAllowedTo('tp_can_search');

	$start = 0;
	$max_results = 20;
	$usebody = false;
	$usetitle = false;

	if (empty($_REQUEST['start'])) {
		$start = 0;
	}
	else {
		$start = TPUtil::filter('start', 'request', 'int');
	}

	if (!empty($_REQUEST['params'])) {
		$params = TPUtil::filter('params', 'request', 'string');
		if (!empty($params)) {
			$params = json_decode(base64_decode($params), true);
			$usebody = $params['body'];
			$usetitle = $params['title'];
			$what = $params['search'];
		}
		else {
			fatal_error($txt['tp-nosearchentered'], false);
		}
	}
	elseif (empty($_POST['tpsearch_what'])) {
		fatal_error($txt['tp-nosearchentered'], false);
	}
	else {
		checkSession('post');
		// clean the search
		$what = TPUtil::filter('tpsearch_what', 'post', 'string');
		if (!empty($_POST['tpsearch_title'])) {
			$usetitle = true;
		}
		if (!empty($_POST['tpsearch_body'])) {
			$usebody = true;
		}
	}

	$select = '';
	$query = '';
	$order_by = '';
	if (TP_PGSQL || $context['TPortal']['fulltextsearch'] == 0) {
		$search = TPDatabase::getInstance()->db_quote('{string:what}', ['what' => '%' . $what . '%']);
		if ($usetitle && !$usebody) {
			$query = 'a.subject LIKE ' . $search;
		}
		elseif (!$usetitle && $usebody) {
			$query = 'a.body LIKE ' . $search;
		}
		elseif ($usetitle && $usebody) {
			$query = 'a.subject LIKE ' . $search . ' OR a.body LIKE ' . $search;
		}
		else {
			$query = 'a.subject LIKE ' . $search;
		}
	}
	else {
		$splitWords = preg_split("#\s{1,}#", $what, -1);
		if (is_array($splitWords)) {
			$words = [];
			foreach ($splitWords as $word) {
				$word = trim($word);
				$operator = substr($word, 0, 1);
				// First Character
				switch ($operator) {
					// Allowed operators
					case '-':
					case '+':
					case '>':
					case '<':
					case '~':
						$word = substr($word, 1);
						break;
					default:
						// Last Character of a word
						$operator = substr($word, -1);
						switch ($operator) {
							// Allowed operators
							case '-':
							case '+':
							case '>':
							case '<':
							case '~':
								$word = substr($word, 0, -1);
								break;
							default:
								$operator = '';
								break;
						}
				}
				$word = preg_replace("#(-|\+|<|>|~|@)#s", '', $word);
				$words[] = $operator . $word;
			}
			$what = implode(' ', $words);
			$search = TPDatabase::getInstance()->db_quote('{string:what}', ['what' => '%' . $what . '%']);
		}
		if ($usetitle && !$usebody) {
			$select = ', MATCH (subject) AGAINST (' . $search . ') AS score';
			$query = 'MATCH (subject) AGAINST (' . $search . ' IN BOOLEAN MODE) > 0';
		}
		elseif (!$usetitle && $usebody) {
			$select = ', MATCH (body) AGAINST (' . $search . ') AS score';
			$query = 'MATCH (body) AGAINST (' . $search . ' IN BOOLEAN MODE) > 0';
		}
		elseif ($usetitle && $usebody) {
			$select = ', MATCH (subject, body) AGAINST (' . $search . ') AS score';
			$query = 'MATCH (subject, body) AGAINST (' . $search . ' IN BOOLEAN MODE) > 0';
		}
		else {
			$select = ', MATCH (subject) AGAINST (' . $search . ') AS score';
			$query = 'MATCH (subject) AGAINST (' . $search . ' IN BOOLEAN MODE) > 0';
		}
		$order_by = 'score DESC, ';
	}
	$num_results = 0;
	$context['TPortal']['searchresults'] = [];
	$context['TPortal']['searchterm'] = $what;
	$context['TPortal']['searchpage'] = $start;
	$now = forum_time();
	$request = TPDatabase::getInstance()->db_query(
		'',
		'
        SELECT a.id, a.date, a.views, a.subject, a.body AS body, a.author_id AS author_id, a.type, m.real_name AS real_name {raw:select}
        FROM {db_prefix}tp_articles AS a
        LEFT JOIN {db_prefix}members as m ON a.author_id = m.id_member
        WHERE {raw:query}
        AND ((a.pub_start = 0 AND a.pub_end = 0)
            OR (a.pub_start != 0 AND a.pub_start < {int:now} AND a.pub_end = 0)
            OR (a.pub_start = 0 AND a.pub_end != 0 AND a.pub_end > {int:now} )
            OR (a.pub_start != 0 AND a.pub_end != 0 AND a.pub_end > {int:now} AND a.pub_start < {int:now}))
        AND a.off = 0
        ORDER BY {raw:order_by} a.date DESC LIMIT {int:limit} OFFSET {int:start}',
		[
			'select' => $select,
			'query' => $query,
			'limit' => $max_results,
			'start' => $start,
			'now' => $now,
			'order_by' => $order_by,
		]
	);
	if (TPDatabase::getInstance()->db_num_rows($request) > 0) {
		while ($row = TPDatabase::getInstance()->db_fetch_assoc($request)) {
			TPUtil::shortenString($row['body'], 400);
			if ($row['type'] == 'bbc') {
				$row['body'] = parse_bbc($row['body']);
			}
			elseif ($row['type'] == 'php') {
				$row['body'] = '[PHP]';
			}
			else {
				$row['body'] = strip_tags($row['body']);
			}

			$row['subject'] = preg_replace('/' . preg_quote($what, '/') . '/iu', '<mark class="highlight">$0</mark>', $row['subject']);
			$row['body'] = preg_replace('/' . preg_quote($what, '/') . '/iu', '<mark class="highlight">$0</mark>', $row['body']);
			$context['TPortal']['searchresults'][] = [
				'id' => $row['id'],
				'date' => $row['date'],
				'views' => $row['views'],
				'subject' => $row['subject'],
				'body' => $row['body'],
				'author' => '<a href="' . $scripturl . '?action=profile;u=' . $row['author_id'] . '">' . $row['real_name'] . '</a>',
			];
		}
		TPDatabase::getInstance()->db_free_result($request);
	}

	$request = TPDatabase::getInstance()->db_query(
		'',
		'
        SELECT COUNT(id) AS num_results
        FROM {db_prefix}tp_articles AS a
        LEFT JOIN {db_prefix}members as m ON a.author_id = m.id_member
        WHERE {raw:query}
        AND ((a.pub_start = 0 AND a.pub_end = 0)
            OR (a.pub_start != 0 AND a.pub_start < {int:now} AND a.pub_end = 0)
            OR (a.pub_start = 0 AND a.pub_end != 0 AND a.pub_end > {int:now} )
            OR (a.pub_start != 0 AND a.pub_end != 0 AND a.pub_end > {int:now} AND a.pub_start < {int:now}))
        AND a.off = 0',
		[
			'query' => $query,
			'now' => $now,
		]
	);

	$num_results = TPDatabase::getInstance()->db_fetch_assoc($request)['num_results'];
	TPDatabase::getInstance()->db_free_result($request);

	$params = base64_encode(json_encode(['search' => $what, 'title' => $usetitle, 'body' => $usebody]));

	// Now that we know how many results to expect we can start calculating the page numbers.
	$context['page_index'] = constructPageIndex($scripturl . '?action=tportal;sa=searcharticle2;params=' . $params, $start, $num_results, $max_results, false);
}
