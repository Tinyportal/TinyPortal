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

function TPSearch()
{
    global $scripturl, $txt, $context;

    if( is_array($_POST) && count($_POST) > 0 ) {
	    $context['TPortal']['subaction'] = 'searcharticle2';
        TPSearchArticle();
    } 
    else {
	    $context['TPortal']['subaction'] = TPUtil::filter('sa', 'get', 'string');
	    TPadd_linktree($scripturl.'?action=tpsearch;sa=searcharticle' , $txt['tp-searcharticles2']);
	    loadtemplate('TPSearch');
    }

}

function TPSearchArticle()
{
	global $scripturl, $txt, $context, $smcFunc;

	$start = 0;
	checkSession('post');
	// any parameters then?
	// nothing to search for?
	if(empty($_POST['tpsearch_what'])) {
		fatal_error($txt['tp-nosearchentered'], false);
	}

	// clean the search
	$what = TPUtil::filter('tpsearch_what', 'post', 'string');
	if(!empty($_POST['tpsearch_title'])) {
		$usetitle = true;
	}
	else {
		$usetitle = false;
	}

	if(!empty($_POST['tpsearch_body'])) {
		$usebody = true;
	}
	else {
		$usebody = false;
	}

	$select     = '';
	$query      = '';
	$order_by   = '';
	if($context['TPortal']['fulltextsearch'] == 0) {
		if($usetitle && !$usebody) {
			$query = 'a.subject LIKE \'%' . $what . '%\'';
		}
		elseif(!$usetitle && $usebody) {
			$query = 'a.body LIKE \'%' . $what . '%\'';
		}
		elseif($usetitle && $usebody) {
			$query = 'a.subject LIKE \'%' . $what . '%\' OR a.body LIKE \'%' . $what . '%\'';
		}
		else {
			$query = 'a.subject LIKE \'%' . $what . '%\'';
		}
	}
	else {
		$splitWords = preg_split("#\s{1,}#", $what, -1);
		if(is_array($splitWords)) {
			$words  = array();
			foreach($splitWords as $word) {
				$word       = trim($word);
				$operator   = substr($word, 0, 1);
				// First Character 
				switch($operator) {
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
						$operator   = substr($word, -1); 
						switch($operator) {
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
				$word       = preg_replace("#(-|\+|<|>|~|@)#s", '', $word);
				$words[]    = $operator.$word;
			}
			$what = implode(' ',$words);
		}
		if($usetitle && !$usebody) {
			$select     = ', MATCH (subject) AGAINST (\''.$what.'\') AS score';
			$query      = 'MATCH (subject) AGAINST (\''.$what.'\' IN BOOLEAN MODE) > 0';
		}
		elseif(!$usetitle && $usebody) {
			$select     = ', MATCH (body) AGAINST (\''.$what.'\') AS score';
			$query      = 'MATCH (body) AGAINST (\''.$what.'\' IN BOOLEAN MODE) > 0';
		}
		elseif($usetitle && $usebody) { 
			$select     = ', MATCH (subject, body) AGAINST (\''.$what.'\') AS score';
			$query      = 'MATCH (subject, body) AGAINST (\''.$what.'\' IN BOOLEAN MODE) > 0';
		}
		else {
			$select     = ', MATCH (subject) AGAINST (\''.$what.'\') AS score';
			$query      = 'MATCH (subject) AGAINST (\''.$what.'\' IN BOOLEAN MODE) > 0';
		}
		$order_by   = 'score DESC, ';
	}
	$context['TPortal']['searchresults']    = array();
	$context['TPortal']['searchterm']       = $what;
	$now = forum_time();
	$request    = $smcFunc['db_query']('', '
			SELECT a.id, a.date, a.views, a.subject, LEFT(a.body, 300) AS body, a.author_id AS authorID, a.type, m.real_name AS realName {raw:select}
			FROM {db_prefix}tp_articles AS a
			LEFT JOIN {db_prefix}members as m ON a.author_id = m.id_member
			WHERE {raw:query}
			AND ((a.pub_start = 0 AND a.pub_end = 0)
				OR (a.pub_start != 0 AND a.pub_start < '.$now.' AND a.pub_end = 0)
				OR (a.pub_start = 0 AND a.pub_end != 0 AND a.pub_end > '.$now.')
				OR (a.pub_start != 0 AND a.pub_end != 0 AND a.pub_end > '.$now.' AND a.pub_start < '.$now.'))
			AND a.off = 0
			ORDER BY {raw:order_by} a.date DESC LIMIT 20',
			array (
				'select'    => $select,
				'query'     => $query,
				'order_by'  => $order_by,
				)
			);
	if($smcFunc['db_num_rows']($request) > 0) {
		while($row = $smcFunc['db_fetch_assoc']($request)) {
			if($row['type'] == 'bbc') {
				$row['body'] = parse_bbc($row['body']);
			}
			elseif($row['type'] == 'php') {
				$row['body'] = '[PHP]';
			}
			else {
				$row['body'] = strip_tags($row['body']);
			}

			$row['subject'] = preg_replace('/'.preg_quote($what).'/', '<span class="highlight">'.$what.'</span>', $row['subject']);
			$row['body']    = preg_replace('/'.preg_quote($what).'/', '<span class="highlight">'.$what.'</span>', $row['body']);
			$context['TPortal']['searchresults'][]=array(
				'id' 		=> $row['id'],
				'date' 		=> $row['date'],
				'views' 	=> $row['views'],
				'subject' 	=> $row['subject'],
				'body' 		=> $row['body'],
				'author' 	=> '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
			);
		}
		$smcFunc['db_free_result']($request);
	}
	TPadd_linktree($scripturl.'?action=tpsearch;sa=searcharticle' , $txt['tp-searcharticles2']);
	loadtemplate('TPSearch');
}
?>
