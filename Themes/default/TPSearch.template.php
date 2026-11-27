<?php
/**
 * @package TinyPortal
 * @version 3.0.5
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

// ** Sections **
// Search Articles Page
// Article search results Page

// Search Articles Page
function template_article_search_form()
{
	global $context, $settings, $txt, $scripturl;

	$params = $context['TPortal']['article_search_params'];

	echo '
	<form accept-charset="', $context['character_set'], '" name="TPsearcharticle" id="searchform" action="' . $scripturl . '?action=tportal;sa=searcharticle2" method="post">
		<div class="tborder">
			<div class="cat_bar">
				<h3 class="catbg">' , $txt['tp-searcharticles2'] , '</h3>
			</div>
			<div id="advanced_search" class="roundframe">
				<dl class="settings" id="search_options">
					<dt><b>' . $txt['tp-search'] . ':</b></dt>
					<dd>
						<input type="text" id="searchbox" name="tpsearch_what" value="', htmlspecialchars($params['search'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), '" required/><br>
						<em class="smalltext"><em>' . $txt['tp-searcharticleshelp'] . '</em></em>
					</dd>
					<dt></dt>
					<dd>
						<input type="checkbox" name="tpsearch_title"', $params['title'] ? ' checked' : '', ' /> ' . $txt['tp-searchintitle'] . '<br>
						<input type="checkbox" name="tpsearch_body"', $params['body'] ? ' checked' : '', ' /> ' . $txt['tp-searchinbody'],'<br>
						<input type="hidden" name="sc" value="' , $context['session_id'] , '" />
					</dd>
				</dl>
				<div class="padding-div"><input type="submit" class="button button_submit" value="' . $txt['tp-search'] . '"></div>';

	if ($context['TPortal']['fulltextsearch'] == 1) {
		echo '
				<div>' . $txt['tp-searcharticleshelp2'] . '</div>';
	}
	echo '
			</div>
		</div>
	</form>';
}

// Article search results Page
function template_article_search_results()
{
	global $context, $settings, $txt, $scripturl;

	echo '
	<div id="search_results">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['search_adjust_query'], '</h3>
		</div>
		<div class="roundframe">
			<form action="', $scripturl, '?action=tportal;sa=searcharticle2" method="post" accept-charset="', $context['character_set'], '">
				<label for="tp_search_again"><strong>', $txt['search_for'], ':</strong></label>
				<input type="text" size="40" id="tp_search_again" name="tpsearch_what" value="', htmlspecialchars($context['TPortal']['searchterm'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), '" required>
				<input type="hidden" name="tpsearch_title" value="', (int) $context['TPortal']['article_search_params']['title'], '">
				<input type="hidden" name="tpsearch_body" value="', (int) $context['TPortal']['article_search_params']['body'], '">
				<input type="hidden" name="sc" value="', $context['session_id'], '">
				<input type="submit" class="button" value="', $txt['search_adjust_submit'], '">
			</form>
		</div>
	</div>
	<div id="display_head" class="information">
		<h2 class="display_title">
			<span>', $txt['mlist_search_results'], ': ', htmlspecialchars($context['TPortal']['searchterm'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), '</span>
		</h2>
		<div class="floatleft">
			<a class="button" href="', $context['TPortal']['search_edit_url'], '">', $txt['search_adjust_query'], '</a>
		</div>
	</div>';

	if (empty($context['TPortal']['searchresults'])) {
		echo '
	<div class="roundframe noup">', $txt['tp-search-no-results'], '</div>';
		return;
	}

	echo '
	<div class="pagesection">
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';

	foreach ($context['TPortal']['searchresults'] as $res) {
		echo '
	<div class="windowbg">
		<div class="topic_details">
			<h5><a href="', $scripturl, '?page=', $res['id'], '">', $res['subject'], '</a></h5>
			<span class="smalltext">', $txt['tp-by'], ' ', $res['author'], ' - ', timeformat($res['date']), '</span>
		</div>
		<div class="list_posts">', $res['body'], '</div>
	</div>';
	}

	echo '
	<div class="pagesection">
		<div class="pagelinks">', $context['page_index'], '</div>
	</div>';
}
