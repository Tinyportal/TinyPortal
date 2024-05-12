<?php
/**
 * @package TinyPortal
 * @version 3.0.0
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

	echo '
	<form accept-charset="', $context['character_set'], '" name="TPsearcharticle" id="searchform" action="' . $scripturl . '?action=tportal;sa=searcharticle" method="post">
		<div class="tborder">
			<div class="cat_bar">
				<h3 class="catbg">' , $txt['tp-searcharticles2'] , '</h3>
			</div>
			<div id="advanced_search" class="roundframe">
				<dl class="settings" id="search_options">
					<dt><b>' . $txt['tp-search'] . ':</b></dt>
					<dd>
						<input type="text" id="searchbox" name="tpsearch_what" required/><br>
						<em class="smalltext"><em>' . $txt['tp-searcharticleshelp'] . '</em></em>
					</dd>
					<dt></dt>
					<dd>
						<input type="checkbox" name="tpsearch_title" checked="checked" /> ' . $txt['tp-searchintitle'] . '<br>
						<input type="checkbox" name="tpsearch_body" checked="checked" /> ' . $txt['tp-searchinbody'],'<br>
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
	<div class="tborder">
		<div class="cat_bar">
			<h3 class="catbg">' , $txt['tp-searchresults'] , '
			' . $txt['tp-searchfor'] . '  &quot;' . $context['TPortal']['searchterm'] . '&quot;</h3>
		</div>

		<div id="advanced_search" class="roundframe">
			<form accept-charset="', $context['character_set'], '"  name="TPsearcharticle" id="searchform" action="' . $scripturl . '?action=tportal;sa=searcharticle" method="post">
			<dl class="settings" id="search_options">
				<dt><b>' . $txt['tp-search'] . ':</b></dt>
				<dd>
					<input type="text" id="searchbox" name="tpsearch_what" value="' . $context['TPortal']['searchterm'] . '" required/><br>
					<em class="smalltext"><em>' . $txt['tp-searcharticleshelp'] . '</em></em>
				</dd>
				<dt></dt>
				<dd>
					<input type="checkbox" name="tpsearch_title" checked="checked" /> ' . $txt['tp-searchintitle'] . '<br>
					<input type="checkbox" name="tpsearch_body" checked="checked" /> ' . $txt['tp-searchinbody'],'<br>
					<input type="hidden" name="sc" value="' , $context['session_id'] , '" />
				</dd>
			</dl>
				<div class="padding-div"><input type="submit" class="button button_submit" value="' . $txt['tp-search'] . '"></div>';

	if ($context['TPortal']['fulltextsearch'] == 1) {
		echo '
			<div>' . $txt['tp-searcharticleshelp2'] . '</div>';
	}
	echo '
			</form>
		</div>
	</div>
	';
	$bb = 1;
	foreach ($context['TPortal']['searchresults'] as $res) {
		echo '
			<div class="windowbg padding-div" style="margin-bottom:3px;">
				<h4 class="tpresults"><a href="' . $scripturl . '?page=' . $res['id'] . '">' . $res['subject'] . '</a></h4>
				<hr>
				<div class="tpresults">
					<div class="middletext">' , $res['body'] . '</div>
					<div class="smalltext" style="padding-top: 0.4em;">' , $txt['tp-by'] . ' ' . $res['author'] . ' - ', timeformat($res['date']) , '</div>
				</div>
			</div>';
		$bb++;
	}
	echo '
	<div class="pagesection">
		<span>', $context['page_index'], '</span>
	</div>';
}
