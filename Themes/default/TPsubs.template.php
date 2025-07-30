<?php
/**
 * @package TinyPortal
 * @version 3.0.3
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
use TinyPortal\Util as TPUtil;

// Block template
function TPblock($block, $theme, $side, $flow, $double = false)
{
	global $context , $scripturl, $settings, $txt;

	// set class for responsive
	$showwidth = (!empty($block['showwidth']) ? $block['showwidth'] : '');
	$blockstyle = (!empty($block['custblockstyle']) ? ' style="' . $block['custblockstyle'] . '"' : '');

	// setup a container that can be massaged through css
	if ($block['type'] == 'ssi') {
		if ($block['body'] == 'toptopics') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="ssitoptopics">';
		}
		elseif ($block['body'] == 'topboards') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="ssitopboards">';
		}
		elseif ($block['body'] == 'topposters') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="ssitopposters">';
		}
		elseif ($block['body'] == 'topreplies') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="ssitopreplies">';
		}
		elseif ($block['body'] == 'topviews') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="ssitopviews">';
		}
		elseif ($block['body'] == 'calendar') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="ssicalendar">';
		}
		else {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="ssiblock">';
		}
	}
	elseif ($block['type'] == 'module') {
		if ($block['body'] == 'dl-stats') {
			echo ' <div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dl-stats">';
		}
		elseif ($block['body'] == 'dl-stats2') {
			echo ' <div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dl-stats2">';
		}
		elseif ($block['body'] == 'dl-stats3') {
			echo ' <div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dl-stats3">';
		}
		elseif ($block['body'] == 'dl-stats4') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dl-stats4">';
		}
		elseif ($block['body'] == 'dl-stats5') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dl-stats5">';
		}
		elseif ($block['body'] == 'dl-stats6') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dl-stats6">';
		}
		elseif ($block['body'] == 'dl-stats7') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dl-stats7">';
		}
		elseif ($block['body'] == 'dl-stats8') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dl-stats8">';
		}
		elseif ($block['body'] == 'dl-stats9') {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dl-stats9">';
		}
		else {
			echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="module_dlstats">';
		}
	}
	elseif ($block['type'] == 'shoutbox') {
		echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="shoutbox_' . preg_replace('/[^a-zA-Z]/', '', strip_tags($block['title'])) . '">';
	}
	elseif ($block['type'] == 'html') {
		echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container ' . $block['type'] . 'box" ' . $blockstyle . ' id="htmlbox_' . preg_replace('/[^a-zA-Z]/', '', strip_tags($block['title'])) . '">';
	}
	elseif ($block['type'] == 'phpbox') {
		echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container ' . $block['type'] . '" ' . $blockstyle . ' id="phpbox_' . preg_replace('/[^a-zA-Z]/', '', strip_tags($block['title'])) . '">';
	}
	elseif ($block['type'] == 'scriptbox') {
		echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container ' . $block['type'] . '" ' . $blockstyle . ' id="scriptbox_' . preg_replace('/[^a-zA-Z]/', '', strip_tags($block['title'])) . '">';
	}
	else {
		echo '<div class="' . $flow . ' ' . $showwidth . ' block_' . $side . 'container" ' . $blockstyle . ' id="block_' . $block['type'] . '">';
	}

	$types = tp_getblockstyles21();

	// check
	if (!isset($block['panelstyle']) || ($block['panelstyle'] == '') || ($block['panelstyle'] == 99)) {
		$block['panelstyle'] = $context['TPortal']['panelstyle_' . $side];
	}

	// its a normal block..
	if (in_array($block['frame'], ['theme', 'frame', 'title', 'none'])) {
		echo	'
	<div class="', (($theme || $block['frame'] == 'frame') ? 'tborder tp_' . $side . 'block_frame' : 'tp_' . $side . 'block_noframe'), '">';

		// show the frame and title
		if ($theme || $block['frame'] == 'title') {
			echo $types[$block['panelstyle']]['code_title_left'];

			if ($block['visible'] == '' || $block['visible'] == '1') {
				echo '<a href="javascript:void(0);return%20false" onclick="toggle(\'' . $block['id'] . '\'); return false"><img class="tp_edit" id="blockcollapse' . $block['id'] . '" src="' . $settings['tp_images_url'] . '/' , !in_array($block['id'], $context['TPortal']['upshrinkblocks']) ? 'TPcollapse' : 'TPexpand' , '.png" alt="" title="' . $txt['block-upshrink_description'] . '" /></a>';
			}

			// can you edit the block?
			if ($block['can_manage'] && !$context['TPortal']['blocks_edithide']) {
				echo '<a href="',$scripturl,'?action=tpadmin&amp;sa=editblock&amp;id=' . $block['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img class="tp_edit" src="' . $settings['tp_images_url'] . '/TPedit2.png" alt="" title="' . $txt['edit_description'] . '" /></a>';
			}
			echo $block['title'];
			echo $types[$block['panelstyle']]['code_title_right'];
		}
		else {
			if (($block['visible'] == '' || $block['visible'] == '1') && $block['frame'] != 'frame') {
				echo '
		<div class="tp_dummy_title">';
				if ($block['visible'] == '' || $block['visible'] == '1') {
					echo '<a href="javascript:void(0);return%20false" onclick="toggle(\'' . $block['id'] . '\'); return false"><img id="blockcollapse' . $block['id'] . '" style="margin: 0;float:right" src="' . $settings['tp_images_url'] . '/' , !in_array($block['id'], $context['TPortal']['upshrinkblocks']) ? 'TPcollapse' : 'TPexpand' , '.png" alt="" title="' . $txt['block-upshrink_description'] . '" /></a>';
				}
				echo '&nbsp;
		</div>';
			}
		}
		echo '
		<div class="', (($theme || $block['frame'] == 'frame') ? 'tp_' . $side . 'block_body' : ''), '"', in_array($block['id'], $context['TPortal']['upshrinkblocks']) ? ' style="display: none;"' : ''  , ' id="block' . $block['id'] . '">';
		if ($theme || $block['frame'] == 'frame') {
			echo $types[$block['panelstyle']]['code_top'];
		}

		$func = 'TPortal_' . $block['type'];
		if (function_exists($func)) {
			echo '<div class="tp_blockbody" ' , !empty($context['TPortal']['blockheight_' . $side]) ? 'style="height: ' . $context['TPortal']['blockheight_' . $side] . '";' : '' , '>';
			$func($block['id']);
			echo '</div>';
		}
		else {
			echo '<div class="tp_blockbody" ' , !empty($context['TPortal']['blockheight_' . $side]) ? 'style="height: ' . $context['TPortal']['blockheight_' . $side] . '";' : '' , '>' , parse_bbc($block['body']) , '</div>';
		}

		if ($theme || $block['frame'] == 'frame') {
			echo $types[$block['panelstyle']]['code_bottom'];
		}
		echo '
		</div>
	</div>';
	}
	// use a pre-defined layout
	else {
		// check if the layout actually exist
		if (!isset($context['TPortal']['blocktheme'][$block['frame']]['body']['before'])) {
			$context['TPortal']['blocktheme'][$block['frame']] = [
				'frame' => ['before' => '', 'after' => ''],
				'title' => ['before' => '', 'after' => ''],
				'body' => ['before' => '', 'after' => '']
			];
		}

		echo $context['TPortal']['blocktheme'][$block['frame']]['frame']['before'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['title']['before'];

		// can you edit the block?
		if ($block['can_manage'] && !$context['TPortal']['blocks_edithide']) {
			echo '<a href="',$scripturl,'?action=tpadmin&amp;sa=editblock&amp;id=' . $block['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img style="margin-right: 4px;float:right" src="' . $settings['tp_images_url'] . '/TPedit2.png" alt="" title="' . $txt['edit_description'] . '" /></a>';
		}

		echo $block['title'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['title']['after'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['body']['before'];

		$func = 'TPortal_' . $block['type'];
		if (function_exists($func)) {
			$func();
		}
		else {
			echo parse_bbc($block['body']);
		}

		echo $context['TPortal']['blocktheme'][$block['frame']]['body']['after'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['frame']['after'];
	}
	echo '
	</div>';
}

// blocktype 1: User
function TPortal_userbox()
{
	global $context, $settings, $scripturl, $txt, $user_info, $modSettings;

	echo '
	<div class="tp_userblock">';

	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged']) {
		if (!empty($context['user']['avatar']) && isset($context['TPortal']['userbox']['avatar'])) {
			echo '
		<span class="tp_avatar">', $context['user']['avatar']['image'], '</span>';
		}
		echo '
		<strong><a class="subject"  href="' . $scripturl . '?action=profile;u=' . $context['user']['id'] . '">', $context['user']['name'], '</a></strong>
		<ul class="tp_user_pm">';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm']) {
			echo '
			<li><a href="', $scripturl, '?action=pm">' . $txt['tp-pm'] . ' ',  $context['user']['messages'], '</a></li>';
			if ($context['user']['unread_messages'] > 0) {
				echo '
			<li><a href="', $scripturl, '?action=pm">' . $txt['tp-pm2'] . ' ',$context['user']['unread_messages'] , '</a></li>';
			}
		}
		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members'])) {
			echo '
			<li><a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve;' . $context['session_var'] . '=' . $context['session_id'] . '">' . $txt['tp_unapproved_members'] . ' ' . $context['unapproved_members'] . '</a></li>';
		}
		// Are there any moderation reports?
		if (!empty($user_info['mod_cache']) && $user_info['mod_cache']['bq'] != '0=1' && !empty($context['open_mod_reports'])) {
			echo '
		</ul>
		<hr>
		<ul class="tp_user_moderate">
			<li><a href="', $scripturl, '?action=moderate;area=reports">' . $txt['tp_modreports'] . ' ' . $context['open_mod_reports'] . '</a></li>';
		}
		if (isset($context['TPortal']['userbox']['unread'])) {
			echo '
		</ul>
		<hr>
		<ul class="tp_user_unread">
			<li><a href="', $scripturl, '?action=unread">' . $txt['tp-unread'] . '</a></li>
			<li><a href="', $scripturl, '?action=unreadreplies">' . $txt['tp-replies'] . '</a></li>
			<li><a href="', $scripturl, '?action=profile;u=' . $context['user']['id'] . ';area=showposts">' . $txt['tp-showownposts'] . '</a></li>
			<li><a href="', $scripturl, '?action=tportal;sa=showcomments">' . $txt['tp-showcomments'] . '</a></li>
			';
		}
		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin']) {
			echo '
		</ul>
		<hr>
		<ul class="tp_user_maintenance">
			<li>' . $txt['tp_maintenace'] . '</li>';
		}
		// Show the total time logged in?
		if (!empty($context['user']['total_time_logged_in']) && isset($context['TPortal']['userbox']['logged'])) {
			if (!TP_SMF21) {
				$days = date('d', $context['user']['total_time_logged_in']);
				$hours = date('H', $context['user']['total_time_logged_in']);
				$minutes = date('i', $context['user']['total_time_logged_in']);
			}
			else {
				$days = $context['user']['total_time_logged_in']['days'];
				$hours = $context['user']['total_time_logged_in']['hours'];
				$minutes = $context['user']['total_time_logged_in']['minutes'];
			}
			echo '
		</ul>
		<hr>
		<ul class="tp_user_loggedintime">
			<li>' . $txt['tp-loggedintime'] . '</li>
			<li>' . $days . $txt['tp-acronymdays'] . $hours . $txt['tp-acronymhours'] . $minutes . $txt['tp-acronymminutes'] . '</li>';
		}
		if (isset($context['TPortal']['userbox']['time'])) {
			echo '
			<li>' . $context['current_time'] . '</li>';
		}

		// admin parts etc.
		if (!isset($context['TPortal']['can_submit_article'])) {
			$context['TPortal']['can_submit_article'] = 0;
		}

		// can we submit an article?
		if (allowedTo('tp_submithtml')) {
			echo '
		</ul>
		<hr>
		<ul class="tp_user_subart">
			<li><a href="', $scripturl, '?action=' . (allowedTo('tp_articles') ? 'tpadmin' : 'tportal') . ';sa=addarticle_html">' . $txt['tp-submitarticle'] . '</a></li>';
		}
		if (allowedTo('tp_submitbbc')) {
			echo '
			<li><a href="', $scripturl, '?action=' . (allowedTo('tp_articles') ? 'tpadmin' : 'tportal') . ';sa=addarticle_bbc">' . $txt['tp-submitarticlebbc'] . '</a></li>';
		}

		if (allowedTo('tp_editownarticle')) {
			echo '
			<li><a href="', $scripturl, '?action=tportal;sa=myarticles">' . $txt['tp-myarticles'] . '</a></li>';
		}

		// upload a file?
		if (allowedTo('tp_dlupload') || allowedTo('tp_dlmanager')) {
			echo '
			<li><a href="', $scripturl, '?action=tportal;sa=download;dl=upload">' . $txt['permissionname_tp_dlupload'] . '</a></li>';
		}

		// tpadmin checks
		if (allowedTo('tp_settings')) {
			echo '
		</ul>
		<hr>
		<ul class="tp_user_settings">
			<li><a href="' . $scripturl . '?action=tpadmin;sa=settings">' . $txt['permissionname_tp_settings'] . '</a></li>';
		}
		if (allowedTo('tp_blocks')) {
			echo '
			<li><a href="' . $scripturl . '?action=tpadmin;sa=blocks">' . $txt['permissionname_tp_blocks'] . '</a></li>';
		}
		if (allowedTo('tp_articles')) {
			echo '
			<li><a href="' . $scripturl . '?action=tpadmin;sa=articles">' . $txt['permissionname_tp_articles'] . '</a></li>';
			// any submissions?
			if ($context['TPortal']['submitcheck']['articles'] > 0) {
				echo '
			<li><a href="' . $scripturl . '?action=tpadmin;sa=submission"><b> ' . $txt['tp-articlessubmitted'] . ' ' . $context['TPortal']['submitcheck']['articles'] . '</b></a></li>';
			}
		}
		if (allowedTo('tp_dlmanager')) {
			echo '
			<li><a href="' . $scripturl . '?action=tportal;sa=download;dl=admin">' . $txt['permissionname_tp_dlmanager'] . '</a></li>';
			// any submissions?
			if ($context['TPortal']['submitcheck']['uploads'] > 0) {
				echo '
			<li><a href="' . $scripturl . '?action=tportal;sa=download;dl=adminsubmission"><b>' . $context['TPortal']['submitcheck']['uploads'] . ' ' . $txt['tp-dluploaded'] . '</b></a></li>';
			}
		}

		echo '
		</ul>';
	}
	// Otherwise they're a guest - so politely ask them to register or login.
// 			<input type="text" class="input_text" name="user" size="10" style="max-width: 45%!important;"/> <input type="password" class="input_password" name="passwrd" size="10" style="max-width: 45%!important;"/><br>

	else {
		echo '
		<div style="line-height: 1.4em;">
			', sprintf($txt[$context['can_register'] ? 'tp-welcome_guest_register' : 'tp-welcome_guest'], $context['forum_name_html_safe'], $scripturl . '?action=login', 'return reqOverlayDiv(this.href, ' . JavaScriptEscape($txt['login']) . ');', $scripturl . '?action=signup'), '<br><br>
		</div>';

		echo '
		<form class="login" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '">';
	if (isset($context['TPortal']['userbox']['loginform'])) {
		echo '
			<dl>
				<dt>', $txt['username'], ':</dt>
				<dd>
					<input type="text" id="', !empty($context['from_ajax']) ? 'ajax_' : '', 'loginuser" name="user" size="20" required>
				</dd>
				<dt>', $txt['password'], ':</dt>
				<dd>
					<input type="password" id="', !empty($context['from_ajax']) ? 'ajax_' : '', 'loginpass" name="passwrd" size="20" required>
				</dd>
			</dl>
			<dl>
				<dt>', $txt['time_logged_in'], ':</dt>
				<dd>
					<select name="cookielength" id="cookielength">';

	foreach ($context['login_cookie_times'] as $cookie_time => $cookie_txt)
		echo '
					<option value="', $cookie_time, '"', $modSettings['cookieTime'] == $cookie_time ? ' selected' : '', '>', $txt[$cookie_txt], '</option>';
	echo '
					</select>
				</dd>
			</dl>';
	}
	else {
		echo '
			<input type="text" class="input_text" name="user" size="10" style="max-width: 45%!important;"/> <input type="password" class="input_password" name="passwrd" size="10" style="max-width: 45%!important;"/><br>
			<select name="cookielength" id="cookielength">';

	foreach ($context['login_cookie_times'] as $cookie_time => $cookie_txt)
		echo '
				<option value="', $cookie_time, '"', $modSettings['cookieTime'] == $cookie_time ? ' selected' : '', '>', $txt[$cookie_txt], '</option>';
	echo '
			</select>';
	}
		if (!isset($context['TPortal']['userbox']['loginform'])) {
			echo '
				<div style="line-height: 1.4em;" class="middletext">', $txt['tp-quick_login_dec'], '</div>';
		}
		echo '
			<p><input type="submit" value="', $txt['login'], '" class="button"></p>
			<p class="smalltext"><a href="', $scripturl, '?action=reminder">', $txt['forgot_your_password'], '</a></p>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			<input type="hidden" name="', $context['login_token_var'], '" value="', $context['login_token'], '">';
		echo '
		</form>';

	}
	echo '
		</div>';
}

// blocktype 2: News
function TPortal_newsbox()
{
	global $context;

	// Show a random news item? (or you could pick one from news_lines...)
	echo '<div class="tp_newsblock">', $context['random_news_line'], '</div>';
}

// blocktype 3: Stats
function TPortal_statsbox()
{
	global $context, $settings, $scripturl, $txt, $modSettings;

	echo '
	<div class="tp_statsblock">';

	if (isset($context['TPortal']['userbox']['stats'])) {
		// members stats
		echo '
		<h5 class="mlist"><a href="' . $scripturl . '?action=mlist">' . $txt['members'] . '</a></h5>
		<ul class="tp_stats_members">
			<li>' . $txt['total_members'] . ': ' , isset($modSettings['memberCount']) ? comma_format($modSettings['memberCount']) : comma_format($modSettings['totalMembers']) , '</li>
			<li>' . $txt['tp-latest'] . ': <a href="', $scripturl, '?action=profile;u=', $modSettings['latestMember'], '"><strong>', $modSettings['latestRealName'], '</strong></a></li>
		</ul>';
	}
	if (isset($context['TPortal']['userbox']['stats_all'])) {
		// more stats
		echo '
		<h5 class="stats"><a href="' . $scripturl . '?action=stats">' . $txt['tp-stats'] . '</a></h5>
		<ul class="tp_stats_forum">
			<li>' . $txt['total_posts'] . ': ' . comma_format($modSettings['totalMessages']) . '</li>
			<li>' . $txt['total_topics'] . ': ' . comma_format($modSettings['totalTopics']) . '</li>
			<li>' . $txt['tp-mostonline-today'] . ': ' . comma_format($modSettings['mostOnlineToday']) . '</li>
			<li>' . $txt['tp-mostonline'] . ': ' . comma_format($modSettings['mostOnline']) . '&nbsp;(' . timeformat($modSettings['mostDate']) . ')</li>
		</ul>';
	}

	if (isset($context['TPortal']['userbox']['online'])) {
		// add online users
		$online = ssi_whosOnline('array');
		echo '
		<h5 class="online"><a href="' . $scripturl . '?action=who">' . $txt['online_users'] . '</a></h5>
		<ul class="tp_stats_users">
			<li>' . $txt['tp-users'] . ': ' . $online['num_users'] . '</li>
			<li>' . $txt['tp-guests'] . ': ' . $online['guests'] . '</li>
			<li>' . $txt['tp-total'] . ': ' . $online['total_users'] . '</li>';

		foreach ($online['users'] as $user) {
			echo '<li class="tp_stats_online_users">' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];
			echo '</li>';
		}
		echo '
		</ul>';
	}
	echo '
	</div>';
}

// blocktype 4: search
function TPortal_searchbox()
{
	global $context, $txt, $scripturl;

	echo '
	<form accept-charset="', $context['character_set'], '" action="', $scripturl, '?action=search2" method="post" style="padding: 0; text-align: center; margin: 0; ">
		<input type="text" class="tp_searchblock" name="search" value="" />
		<input type="submit" name="submit" value="', $txt['search'], '" class="tp_searchblock_submit button_submit" /><br>
		<br><span class="smalltext"><a href="', $scripturl, '?action=search;advanced">', $txt['search_advanced'], '</a></span>
		<input type="hidden" name="advanced" value="0" />
	</form>';
}

// blocktype 6: online
function TPortal_onlinebox()
{
	global $context;

	if ($context['TPortal']['useavatar'] == 1) {
		tpo_whos();
	}
	else {
		echo '
	<div style="line-height: 1.4em;">' , ssi_whosOnline() , '</div>';
	}
}

// blocktype 7: Themes
function TPortal_themebox()
{
	global $context, $settings, $scripturl, $txt, $smcFunc;

	$what = explode(',', $context['TPortal']['themeboxbody']);
	$temaid = [];
	$temanavn = [];
	$temapaths = [];
	foreach ($what as $wh => $wht) {
		$all = explode('|', $wht);
		if ($all[0] > -1) {
			$temaid[] = $all[0];
			$temanavn[] = isset($all[1]) ? $all[1] : '';
			$temapaths[] = isset($all[2]) ? $all[2] : '';
		}
	}

	if (isset($context['TPortal']['querystring'])) {
		$tp_where = $smcFunc['htmlspecialchars'](strip_tags($context['TPortal']['querystring']));
	}
	else {
		$tp_where = 'action=forum';
	}

	if ($tp_where != '') {
		$tp_where .= ';';
	}

	// remove multiple theme=x in the string.
	$tp_where = preg_replace("'theme=[^>]*?;'si", '', $tp_where);

	if (is_countable($temaid) && count($temaid) > 0) {
		echo '
		<form name="jumpurl1" onsubmit="return jumpit()" class="middletext" action="#" style="padding: 0; margin: 0; text-align: center;">
			<select style="width: 100%; margin: 5px 0px 5px 0px;" size="1" name="jumpurl2" onchange="check(this.value)">';
		for ($a = 0 ; $a < (count($temaid)); $a++) {
			echo '
				<option value="' . $temaid[$a] . '" ', $settings['theme_id'] == $temaid[$a] ? 'selected="selected"' : '' ,'>' . substr($temanavn[$a], 0, 25) . '</option>';
		}
		echo '
			</select><br>' , $context['user']['is_logged'] ?
			'<input type="checkbox" id="tp-permanent" value=";permanent" onclick="realtheme()" /> ' . $txt['tp-permanent'] . '<br>' : '' , '<br>
			<input type="button" class="button_submit" value="' . $txt['tp-changetheme'] . '" onclick="jumpit()" /><br><br>
			<input type="hidden" value="' . $smcFunc['htmlspecialchars']($scripturl . '?' . $tp_where . 'theme=' . $settings['theme_id']) . '" name="jumpurl3" />
			<div style="text-align: center; width: 95%; overflow: hidden;">
			<img src="' . $settings['images_url'] . '/thumbnail.png" alt="" id="chosen" name="chosen" />
			</div>
		</form>
		<script type="text/javascript"><!-- // --><![CDATA[
			var extra = \'\';
			var themepath = new Array();';
		for ($a = 0 ; $a < (count($temaid)); $a++) {
			echo '
				themepath[' . $temaid[$a] . '] = "' . $temapaths[$a] . '/thumbnail.png";
				';
		}

		echo '
			function jumpit()
			{
				window.location = document.jumpurl1.jumpurl3.value + extra;
				return false;
			}
			function realtheme()
			{
				if (extra === ";permanent")
					extra = "";
				else
					extra = ";permanent";
			}
			function check(icon)
			{
				document.chosen.src= themepath[icon]
				document.jumpurl1.jumpurl3.value = \'' . $scripturl . '?' . $tp_where . 'theme=\' + icon
			}
		// ]]></script>';
	}
	else {
		echo $txt['tp-nothemeschosen'];
	}
}

// blocktype 8: TP Shoutbox
function TPortal_shoutbox($block_id)
{
	global $context;

	// fetch the correct block
	$tpm = $block_id;
	if (!empty($context['TPortal']['tpblocks']['blockrender'][$tpm]['function']) && function_exists($context['TPortal']['tpblocks']['blockrender'][$tpm]['function'])) {
		call_user_func($context['TPortal']['tpblocks']['blockrender'][$tpm]['function'], $block_id);
	}
}

// blocktype 9: Menu
function TPortal_catmenu()
{
	global $context, $scripturl, $boardurl, $settings;

	if (isset($context['TPortal']['menu'][$context['TPortal']['menuid']]) && !empty($context['TPortal']['menu'][$context['TPortal']['menuid']])) {
		echo '
	<ul class="tp_catmenu">';

		foreach ($context['TPortal']['menu'][$context['TPortal']['menuid']] as $cn) {
			echo '
		<li', $cn['type'] == 'head' ? ' class="tp_catmenu_header"' : '' ,'>';
			if ($context['TPortal']['menuvar1'] == '' || $context['TPortal']['menuvar1'] == '0') {
				echo str_repeat('&nbsp;&nbsp;', ($cn['sub'] + 1));
			}
			elseif ($context['TPortal']['menuvar1'] == '1') {
				echo str_repeat('&nbsp;&nbsp;', ($cn['sub'] + 1));
			}
			elseif ($context['TPortal']['menuvar1'] == '2') {
				echo str_repeat('&nbsp;&nbsp;', ($cn['sub'] + 1));
			}

			if ((!isset($cn['icon']) || (isset($cn['icon']) && $cn['icon'] == '')) && $cn['type'] != 'head' && $cn['type'] != 'spac') {
				if ($context['TPortal']['menuvar1'] == '' || $context['TPortal']['menuvar1'] == '0') {
					echo '
			<img src="' . $settings['tp_images_url'] . '/TPdivider2.png" alt="" />&nbsp;';
				}
				elseif ($context['TPortal']['menuvar1'] == '1') {
					echo '
			<img src="' . $settings['tp_images_url'] . '/bullet3.png" alt="" />';
				}
			}
			elseif (isset($cn['icon']) && $cn['icon'] != '' && $cn['type'] != 'head' && $cn['type'] != 'spac') {
				echo '
			<img alt="*" src="' . $cn['icon'] . '" />&nbsp;';
			}
			switch ($cn['type']) {
				case 'cats':
					echo '
			<a href="' . $scripturl . '?cat=' . $cn['IDtype'] . '"' . ($cn['newlink'] == '1' ? ' target="_blank"' : '') . '>' . $cn['name'] . '</a>';
					break;
				case 'arti':
					echo '
			<a href="' . $scripturl . '?page=' . $cn['IDtype'] . '"' . ($cn['newlink'] == '1' ? ' target="_blank"' : '') . '>' . $cn['name'] . '</a>';
					break;
				case 'link':
					echo '
			<a href="' . $cn['IDtype'] . '"' . ($cn['newlink'] == '1' ? ' target="_blank"' : '') . '>' . $cn['name'] . '</a>';
					break;
				case 'head':
					echo '
			<a class="tp_catmenu_header" name="header' . $cn['id'] . '"><b>' . $cn['IDtype'] . '</b></a>';
					break;
				case 'spac':
					echo '
			<a name="spacer' . $cn['id'] . '">&nbsp;</a>';
					break;
				default:
					echo '
			<a href="' . $cn['IDtype'] . '"' . ($cn['newlink'] == '1' ? ' target="_blank"' : '') . '>' . $cn['name'] . '</a>';
					break;
			}
			echo '</li>';
		}
		echo '
	</ul>';
	}
}

// blocktype 10: PHP code
function TPortal_phpbox()
{
	global $context;

	// execute what is in the block, no echoing
	if (!empty($context['TPortal']['phpboxbody']));
	eval(tp_convertphp($context['TPortal']['phpboxbody'], true));
}

// blocktype 11: HTML & Javascript code
function TPortal_scriptbox()
{
	global $context;

	echo $context['TPortal']['scriptboxbody'];
}

// blocktype 12: Recent Topics
function TPortal_recentbox()
{
	global $scripturl, $context, $settings, $txt, $modSettings, $user_info;

	// if no guest access to forum, then no recent topics
	if (empty($modSettings['allow_guestAccess']) && $user_info['is_guest']) {
		echo '' . $txt['tp-noguest_access'] . '';
		return;
	}
	else {
		// set variable
		if (is_numeric($context['TPortal']['minmessagetopics'])) {
			$context['min_message_topics'] = $context['TPortal']['minmessagetopics'];
		}
		else {
			$context['min_message_topics'] = 350;
		}
		// is it a number?
		if (is_numeric($context['TPortal']['recentlength'])) {
			$recentlength = $context['TPortal']['recentlength'];
		}
		else {
			$recentlength = '25';
		}
		// exclude boards
		if (isset($context['TPortal']['recentboards']) && $context['TPortal']['boardmode'] == 0) {
			$exclude_boards = $context['TPortal']['recentboards'];
		}
		else {
			// leave out the recycle board, if any
			if (isset($modSettings['recycle_board']) && $modSettings['recycle_enable'] = 1) {
				$bb = [$modSettings['recycle_board']];
			}
			$exclude_boards = $bb;
		}

		// include boards
		if (isset($context['TPortal']['recentboards']) && !$context['TPortal']['boardmode'] == 0) {
			$include_boards = $context['TPortal']['recentboards'];
		}
		else {
			$include_boards = null;
		}

		$what = ssi_recentTopics($num_recent = $context['TPortal']['recentboxnum'], $exclude_boards, $include_boards, $output_method = 'array');
		if ($context['TPortal']['useavatar'] == 0) {
			// Output the topics
			echo '
		<ul class="tp_recenttopics" style="' , isset($context['TPortal']['recentboxscroll']) && $context['TPortal']['recentboxscroll'] == 1 ? 'overflow: auto; height: 20ex;' : '' , 'margin: 0; padding: 0;">';
			$coun = 1;
			foreach ($what as $wi => $w) {
				$tpshortsubject = $w['subject'];
				$w['readmore'] = '';
				if (TPUtil::shortenString($tpshortsubject, $recentlength)) {
					$w['readmore'] = '...';
				}
				echo '
			<li' , $coun < count($what) ? '' : ' style="border: none; margin-bottom: 0;padding-bottom: 0;"'  , '>';
				if ($w['is_new']) {
					echo '
					<a href="' . $scripturl . '?topic=' . $w['topic'] . '.msg' . $w['new_from'] . ';topicseen#new" rel="nofollow" class="new_posts" style="margin:0px;">' . $txt['new'] . '</a> ';
				}
				echo '
				<a href="' . $w['href'] . '" title="' . $w['subject'] . '">' . $tpshortsubject . '' . $w['readmore'] . '</a>
				 ', $txt['by'], ' <b>', $w['poster']['link'],'</b>
				 <br><span class="smalltext">[' . $w['time'] . ']</span>
				</li>';
				$coun++;
			}
			echo '
		</ul>';
		}
		else {
			$member_ids = [];
			foreach ($what as $wi => $w) {
				$member_ids[] = $w['poster']['id'];
			}

			if (!empty($member_ids)) {
				$avatars = progetAvatars($member_ids);
			}
			else {
				$avatars = [];
			}

			// Output the topics
			$coun = 1;
			echo '
		<ul class="tp_recenttopics" style="' , isset($context['TPortal']['recentboxscroll']) && $context['TPortal']['recentboxscroll'] == 1 ? 'overflow: auto; height: 20ex;' : '' , 'margin: 0; padding: 0;">';

			foreach ($what as $wi => $w) {
				$tpshortsubject = $w['subject'];
				$w['readmore'] = '';
				if (TPUtil::shortenString($tpshortsubject, $recentlength)) {
					$w['readmore'] = '...';
				}
				echo '
			<li' , $coun < count($what) ? '' : ' style="border: none; margin-bottom: 0;padding-bottom: 0;"'  , '>';
				if ($w['is_new']) {
					echo ' 
					<a href="' . $scripturl . '?topic=' . $w['topic'] . '.msg' . $w['new_from'] . ';topicseen#new" rel="nofollow" class="new_posts" style="margin:0px;">' . $txt['new'] . '</a> ';
				}
				echo '
				<span class="tp_avatar"><a href="' . $scripturl . '?action=profile;u=' . $w['poster']['id'] . '">' , empty($avatars[$w['poster']['id']]) ? '<img class="avatar" src="' . $settings['tp_images_url'] . '/TPguest.png" alt="" />' : $avatars[$w['poster']['id']] , '</a></span><a href="' . $w['href'] . '" title="' . $w['subject'] . '">' . $tpshortsubject . '' . $w['readmore'] . '</a>
				 ', $txt['by'], ' <b>', $w['poster']['link'],'</b>
				 <br><span class="smalltext">[' . $w['time'] . ']</span>
				</li>';
				$coun++;
			}
			echo '
		</ul>';
		}
	}
}
// blocktype 13: SSI functions
function TPortal_ssi()
{
	global $context, $txt;
	echo '<div class="tp_ssifunction smalltext">';
	if ($context['TPortal']['ssifunction'] == 'recenttopics') {
		ssi_recentTopics();
	}
	elseif ($context['TPortal']['ssifunction'] == 'recentposts') {
		ssi_recentPosts();
	}
	elseif ($context['TPortal']['ssifunction'] == 'recentpoll') {
		ssi_recentPoll();
	}
	elseif ($context['TPortal']['ssifunction'] == 'recentattachments') {
		ssi_recentAttachments();
	}
	elseif ($context['TPortal']['ssifunction'] == 'topboards') {
		ssi_topBoards();
	}
	elseif ($context['TPortal']['ssifunction'] == 'topreplies') {
		ssi_topTopicsReplies();
	}
	elseif ($context['TPortal']['ssifunction'] == 'topviews') {
		ssi_topTopicsViews();
	}
	elseif ($context['TPortal']['ssifunction'] == 'toppoll') {
		ssi_topPoll();
	}
	elseif ($context['TPortal']['ssifunction'] == 'topposters') {
		ssi_topPoster(5);
	}
	elseif ($context['TPortal']['ssifunction'] == 'latestmember') {
		ssi_latestMember();
	}
	elseif ($context['TPortal']['ssifunction'] == 'randommember') {
		ssi_randomMember('day');
	}
	elseif ($context['TPortal']['ssifunction'] == 'online') {
		ssi_whosOnline();
	}
	elseif ($context['TPortal']['ssifunction'] == 'whosonline') {
		ssi_whosOnline();
	}
	elseif ($context['TPortal']['ssifunction'] == 'welcome') {
		ssi_welcome();
	}
	elseif ($context['TPortal']['ssifunction'] == 'calendar') {
		ssi_todaysCalendar();
	}
	elseif ($context['TPortal']['ssifunction'] == 'birthday') {
		ssi_todaysBirthdays();
	}
	elseif ($context['TPortal']['ssifunction'] == 'holiday') {
		ssi_todaysHolidays();
	}
	elseif ($context['TPortal']['ssifunction'] == 'event') {
		ssi_todaysEvents();
	}
	elseif ($context['TPortal']['ssifunction'] == 'recentevents') {
		ssi_recentEvents();
	}
	elseif ($context['TPortal']['ssifunction'] == 'boardstats') {
		ssi_boardStats();
	}
	elseif ($context['TPortal']['ssifunction'] == 'news') {
		ssi_news();
	}
	elseif ($context['TPortal']['ssifunction'] == 'boardnews') {
		ssi_boardNews();
	}
	elseif ($context['TPortal']['ssifunction'] == 'quicksearch') {
		ssi_quickSearch();
	}

	echo '</div>';
}

// blocktype 14: Article / Download functions
function TPortal_module()
{
	global $context, $scripturl, $txt;

	switch ($context['TPortal']['moduleblock']) {
		case 'dl-stats':
			dl_recentitems('8', 'date', 'echo');
			break;
		case 'dl-stats2':
			dl_recentitems('8', 'downloads', 'echo');
			break;
		case 'dl-stats3':
			dl_recentitems('8', 'views', 'echo');
			break;
		case 'dl-stats4':
			$it = [];
			$it = dl_recentitems('1', 'date', 'array');
			if (is_countable($it) && count($it) > 0) {
				foreach ($it as $item) {
					echo '
					<div class="tp_flexrow">
						<div class="tp_dlicon"><img src="' . $item['icon'] . '" alt="" /></div>
						<div class="tp_dldetails">
							<div class="tp_dltitle"><a href="' . $item['href'] . '">' . $item['name'] . '</a></div>
							<div class="tp_dlinfo">
								<div>' . $txt['tp-uploadedby'] . ' <b>' . $item['author'] . '</b></div>
								<div>' . $item['date'] . '</div>
								<div>' . $txt['tp-downloads'] . ': <b>' . $item['downloads'] . '</b></div>
								<div>' . $txt['tp-itemviews'] . ': <b>' . $item['views'] . '</b></div>
							</div>
						</div>
					</div>';
				}
			}
			break;
		case 'dl-stats5':
			$it = [];
			$it = dl_recentitems('1', 'downloads', 'array');
			if (is_countable($it) && count($it) > 0) {
				foreach ($it as $item) {
					echo '
					<div class="tp_flexrow">
						<div class="tp_dlicon"><img src="' . $item['icon'] . '" alt="" /></div>
						<div class="tp_dldetails">
							<div class="tp_dltitle"><a href="' . $item['href'] . '">' . $item['name'] . '</a></div>
							<div class="tp_dlinfo">
								<div>' . $txt['tp-downloads'] . ': <b>' . $item['downloads'] . '</b></div>
								<div>' . $txt['tp-itemviews'] . ': <b>' . $item['views'] . '</b></div>
								<div>' . $txt['tp-uploadedby'] . ' <b>' . $item['author'] . '</b></div>
								<div>' . $item['date'] . '</div>
							</div>
						</div>
					</div>';
				}
			}
			break;
		case 'dl-stats6':
			$it = [];
			$it = dl_recentitems('1', 'views', 'array');
			if (is_countable($it) && count($it) > 0) {
				foreach ($it as $item) {
					echo '
					<div class="tp_flexrow">
						<div class="tp_dlicon"><img src="' . $item['icon'] . '" alt="" /></div>
						<div class="tp_dldetails">
							<div class="tp_dltitle"><a href="' . $item['href'] . '">' . $item['name'] . '</a></div>
							<div class="tp_dlinfo">
								<div>' . $txt['tp-itemviews'] . ': <b>' . $item['views'] . '</b></div>
								<div>' . $txt['tp-downloads'] . ': <b>' . $item['downloads'] . '</b></div>
								<div>' . $txt['tp-uploadedby'] . ' <b>' . $item['author'] . '</b></div>
								<div>' . $item['date'] . '</div>
							</div>
						</div>
					</div>';
				}
			}
			break;
		case 'dl-stats7':
			$it = [];
			$it = art_recentitems('5', 'date');
			if (is_countable($it) && count($it) > 0) {
				foreach ($it as $item) {
					echo '<span class="smalltext"><a title="' . $item['date'] . '" href="' . $scripturl . '?page=' . $item['id'] . '">' . $item['subject'] . '</a>
						</span><br>';
				}
			}
			break;
		case 'dl-stats8':
			$it = [];
			$it = art_recentitems('5', 'views');
			if (is_countable($it) && count($it) > 0) {
				foreach ($it as $item) {
					echo '<span class="smalltext"><a title="' . $txt['tp-views'] . ': ' . $item['views'] . '" href="' . $scripturl . '?page=' . $item['id'] . '">' . $item['subject'] . '</a>
						</span><br>';
				}
			}
			break;
		case 'dl-stats9':
			$it = [];
			$it = art_recentitems('5', 'comments');
			if (is_countable($it) && count($it) > 0) {
				foreach ($it as $item) {
					echo '<span class="smalltext"><a title="' . $item['comments'] . '" href="' . $scripturl . '?page=' . $item['id'] . '">' . $item['subject'] . '</a>
						(' . $item['comments'] . ')<br></span>';
				}
			}
			break;
	}
}

// blocktype 15: RSS block
function TPortal_rss()
{
	global $context;

	echo '<div style="' , !empty($context['TPortal']['rsswidth']) ? 'max-width: ' . $context['TPortal']['rsswidth'] . ';' : '' , '" class="tp_rssfunction middletext">' , TPparseRSS('', $context['TPortal']['rss_utf8']) , '</div>';
}

// blocktype 16: sitemap
function TPortal_sitemap()
{
	global $context, $settings, $scripturl, $txt;

	$current = '';
	// check where we are
	if (isset($_GET['action']) && $_GET['action'] == 'tpmod') {
		if (isset($_GET['dl'])) {
			$current = 'dl';
		}
		elseif (isset($_GET['link'])) {
			$current = 'link';
		}
		elseif (isset($_GET['show'])) {
			$current = 'show';
		}
		elseif (isset($_GET['team'])) {
			$current = 'team';
		}
		else {
			$current = '';
		}
	}
	echo '
	<div class="tborder">
		<ul class="tp_sitemap">';
	if ($context['TPortal']['show_download'] == '1') {
		echo '<li><a class="tp_sitemapheader" href="' . $scripturl . '?action=tportal;sa=download;dl"><img src="' . $settings['tp_images_url'] . '/TPmodule2.png" alt="" /> ' . $txt['tp-downloads'] . '</a></li>';
	}

	if (!empty($context['TPortal']['sitemap']) && !empty($context['TPortal']['menu'])) {
		foreach ($context['TPortal']['menu'] as $main) {
			foreach ($main as $cn) {
				// check if we can find the link on current tpage
				$catclass = '';
				if ($cn['type'] == 'cats') {
					if (isset($_GET['cat']) && $cn['IDtype'] == $_GET['cat']) {
						$catclass = 'tp_sitemapheader';
					}
				}
				elseif ($cn['type'] == 'arti') {
					if (isset($_GET['page']) && $cn['IDtype'] == $_GET['page']) {
						$catclass = 'tp_sitemapheader';
					}
				}
				elseif ($cn['type'] == 'link') {
					if (!empty($context['TPortal']['querystring'])) {
						$qs = $scripturl . '?' . $context['TPortal']['querystring'];
					}
					else {
						$qs = $scripturl;
					}

					if ($qs == $cn['IDtype']) {
						$catclass = 'tp_sitemapheader';
					}
				}

				if ($cn['sitemap'] == '1') {
					switch ($cn['type']) {
						case 'cats':
							echo '<li><a class="' , $catclass ,'" href="' . $scripturl . '?cat=' . $cn['IDtype'] . '" ' , $cn['newlink'] == '1' ? 'target="_blank"' : '' , '><img src="' . $settings['tp_images_url'] . '/TPdivider.png" alt="" /> ' . $cn['name'] . '</a></li>';
							break;
						case 'arti':
							echo '<li><a class="' , $catclass ,'" href="' . $scripturl . '?page=' . $cn['IDtype'] . '"' , $cn['newlink'] == '1' ? 'target="_blank"' : '' , '><img src="' . $settings['tp_images_url'] . '/TPdivider.png" alt="" /> ' . $cn['name'] . '</a></li>';
							break;
						case 'link':
							echo '<li><a class="' , $catclass ,'" href="' . $cn['IDtype'] . '"' , $cn['newlink'] == '1' ? 'target="_blank"' : '' , '><img src="' . $settings['tp_images_url'] . '/TPdivider.png" alt="" /> ' . $cn['name'] . '</a></li>';
							break;
					}
				}
			}
		}
	}
	echo '
		</ul>
	</div>';
}

// blocktype 18: Single Article
function TPortal_articlebox()
{
	global $context;

	if (isset($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']])) {
		echo '<div class="tp_articleblock">', template_blockarticle() ,'</div>';
	}
}

// blocktype 19: Articles in a Category
function TPortal_categorybox()
{
	global $context, $txt, $scripturl;

	if (isset($context['TPortal']['blockarticle_titles'][$context['TPortal']['blocklisting']])) {
		echo '<div class="middletext" ', (count($context['TPortal']['blockarticle_titles'][$context['TPortal']['blocklisting']]) > $context['TPortal']['blocklisting_height'] && $context['TPortal']['blocklisting_height'] != '0') ? ' style="overflow: auto; width: 100%; height: ' . $context['TPortal']['blocklisting_height'] . 'em;"' : '' ,'>';
		foreach ($context['TPortal']['blockarticle_titles'][$context['TPortal']['blocklisting']] as $listing) {
			if ($listing['category'] == $context['TPortal']['blocklisting']) {
				echo '<b><a href="' . $scripturl . '?page=' . $listing['shortname'] . '">' . $listing['subject'] . '</a></b> ' , $context['TPortal']['blocklisting_author'] == '1' ? $txt['by'] . ' ' . $listing['poster'] : '' , '<br>';
			}
		}
		echo '</div>';
	}
}

// blocktype 21: Promoted Topics
function TPortal_promotedbox()
{
	global $scripturl, $context, $settings, $txt, $modSettings, $user_info, $smcFunc;

	// if no guest access to forum, then no promoted topics
	if (empty($modSettings['allow_guestAccess']) && $user_info['is_guest']) {
		echo '' . $txt['tp-noguest_access'] . '';
		return;
	}
	else {
		// is it a number?
		if (is_numeric($context['TPortal']['length'])) {
			$length = $context['TPortal']['length'];
		}
		else {
			$length = '25';
		}
		// exclude boards
		if (isset($context['TPortal']['promotedboards']) && $context['TPortal']['boardmode'] == 0) {
			$exclude_boards = $context['TPortal']['promotedboards'];
		}
		else {
			// leave out the recycle board, if any
			if (isset($modSettings['recycle_board']) && $modSettings['recycle_enable'] = 1) {
				$bb = [$modSettings['recycle_board']];
			}
			$exclude_boards = $bb;
		}

		// include boards
		if (isset($context['TPortal']['promotedboards']) && !$context['TPortal']['boardmode'] == 0) {
			$include_boards = $context['TPortal']['promotedboards'];
		}
		else {
			$include_boards = null;
		}

    $request = $smcFunc['db_query'](
	'', 
	'
        SELECT t.id_topic, t.id_first_msg, m.subject, m.id_member, m.poster_name, m.poster_time, b.id_board, b.name
        FROM {db_prefix}topics AS t
        INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
		INNER JOIN {db_prefix}boards AS b ON t.id_board = b.id_board
        WHERE t.id_topic IN ({raw:topics})
        ORDER BY m.poster_time DESC
        LIMIT {int:limit}',
        array(
            'topics' => $context['TPortal']['frontpage_topics'],
            'limit' => $context['TPortal']['promotedboxnum'],
			'board' => $context['TPortal']['promotedboards'],
        )
    );

    $what = array();
	if ($smcFunc['db_num_rows']($request) > 0) {
 	 while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$what[] = $row;
		}
		$smcFunc['db_free_result']($request);

			if ($context['TPortal']['useavatar'] == 0) {
				// Output the topics
				echo '
			<ul class="tp_recenttopics" style="margin: 0; padding: 0;">';
				$coun = 1;
				foreach ($what as $wi => $w) {
					$tpshortsubject = $w['subject'];
					$w['readmore'] = '';
					if (TPUtil::shortenString($tpshortsubject, $length)) {
						$w['readmore'] = '...';
					}
					echo '
				<li' , $coun < count($what) ? '' : ' style="border: none; margin-bottom: 0;padding-bottom: 0;"'  , '>';

					echo '
					<div style="font-weight: bold; margin-bottom: 5px;">
						<a href="', $scripturl, '?topic=', $w['id_topic'], '.0" title="' . $w['subject'] . '">' . $tpshortsubject . '' . $w['readmore'] . '</a>
					</div>
					', $txt['tp-fromcategory'] ,' <a href="', $scripturl, '?board=', $w['id_board'], '.0">' . $w['name'] . '</a> ', $txt['by'], ' <b><a href="' . $scripturl . '?action=profile;u=' . $w['id_member'] . '">', htmlspecialchars($w['poster_name']),'</a></b><br>
					 <span class="smalltext">' . timeformat($w['poster_time']) . '</span>
					</li>';
					$coun++;
				}
				echo '
			</ul>';
			}
			else {
				$member_ids = [];
				foreach ($what as $wi => $w) {
					$member_ids[] = $w['id_member'];
				}
				empty($member_ids) ? $avatars = [] : $avatars = progetAvatars($member_ids);

				// Output the topics
				$coun = 1;
				echo '
			<ul class="tp_recenttopics" style="margin: 0; padding: 0;">';

				foreach ($what as $wi => $w) {
					$tpshortsubject = $w['subject'];
					$w['readmore'] = '';
					if (TPUtil::shortenString($tpshortsubject, $length)) {
						$w['readmore'] = '...';
					}
					echo '
				<li' , $coun < count($what) ? '' : ' style="border: none; margin-bottom: 0;padding-bottom: 0;"'  , '>';

					echo '
					<span class="tp_avatar"><a href="' . $scripturl . '?action=profile;u=' . $w['id_member'] . '">' , empty($avatars[$w['id_member']]) ? '<img class="avatar" src="' . $settings['tp_images_url'] . '/TPguest.png" alt="" />' : $avatars[$w['id_member']] , '</a></span>
					<div style="font-weight: bold; margin-bottom: 5px;">
						<a href="', $scripturl, '?topic=', $w['id_topic'], '.0" title="' . $w['subject'] . '">' . $tpshortsubject . '' . $w['readmore'] . '</a>
					</div>
					', $txt['tp-fromcategory'] ,' <a href="', $scripturl, '?board=', $w['id_board'], '.0">' . $w['name'] . '</a> ', $txt['by'], ' <b><a href="' . $scripturl . '?action=profile;u=' . $w['id_member'] . '">', htmlspecialchars($w['poster_name']),'</a></b><br>
					<span class="smalltext">' . timeformat($w['poster_time']) . '</span>
					</li>';
					$coun++;
				}
				echo '
			</ul>';
			}
		}
	else {
		echo '
			'. $txt['tp-notopics'] .'';
	}
	}
}

// dummy for old templates
function TPortal_sidebar()
{
	return;
}

// Some functions, not templates
function tpo_whos($buddy_only = false)
{
	global $txt, $scripturl;

	$whos = tpo_whosOnline();
	echo '
	<div>
	' . $whos['num_guests'] . ' ' , $whos['num_guests'] == 1 ? $txt['guest'] : $txt['guests'] , ',
	' . $whos['num_users_online'] . ' ' , $whos['num_users_online'] == 1 ? $txt['user'] : $txt['users'] , '
	</div>';
	if (isset($whos['users_online']) && count($whos['users_online']) > 0) {
		$ids = [];
		$names = [];
		$times = [];
		foreach ($whos['users_online'] as $w => $wh) {
			// For reasons historical, SMF produces the timestamp as
			// the timestamp followed by the user's name, so let's fix it.
			$timestamp = (int) strtr($w, [$wh['username'] => '']);
			$ids[] = $wh['id'];
			$names[$wh['id']] = $wh['name'];
			$times[$wh['id']] = timeformat($timestamp);
		}
		$avy = progetAvatars($ids);
		foreach ($avy as $a => $av) {
			echo '
		<a class="tp_avatar_single2" title="' . $names[$a] . '" href="' . $scripturl . '?action=profile;u=' . $a . '">' . $av . '</a>';
		}
	}
}

function tpo_whosOnline()
{
	global $sourcedir;

	require_once $sourcedir . '/Subs-MembersOnline.php';
	$membersOnlineOptions = [
		'show_hidden' => allowedTo('moderate_forum'),
		'sort' => 'log_time',
		'reverse_sort' => true,
	];
	$return = getMembersOnlineStats($membersOnlineOptions);
	return $return;
}

function progetAvatars($ids)
{
	global $user_info, $smcFunc, $modSettings, $scripturl;
	global $image_proxy_enabled, $image_proxy_secret, $boardurl;
	$request = $smcFunc['db_query'](
		'',
		'
		SELECT
			mem.real_name, mem.member_name, mem.id_member, mem.show_online, mem.avatar, mem.email_address AS email_address,
			COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type AS attachment_type
		FROM {db_prefix}members AS mem
		LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type != 3)
		WHERE mem.id_member IN ({array_int:ids})',
		['ids' => $ids]
	);

	$avy = [];
	if ($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$avy[$row['id_member']] = set_avatar_data(
				[
					'avatar' => $row['avatar'],
					'email' => $row['email_address'],
					'filename' => !empty($row['filename']) ? $row['filename'] : '',
					'id_attach' => $row['id_attach'],
					'attachment_type' => $row['attachment_type'],
				]
			)['image'];
		}
		$smcFunc['db_free_result']($request);
	}

	return $avy;
}

// a dummy layer for layer articles
function template_nolayer_above()
{
	global $context;

	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<meta name="keywords" content="' . $context['meta_keywords'] . '" />
		<title>' , $context['page_title'] , '</title>
		' , $context['tp_html_headers'] , '
	</head>
	<body><div id="nolayer_frame">';
}

function template_nolayer_below()
{
	echo '<small id="nolayer_copyright">',theme_copyright(),'</small>
	</div></body></html>';
}

// article search page 1
function template_TPsearch_above()
{
	global $context, $txt, $scripturl;

	echo '
		<div class="cat_bar">
			<h3 class="catbg">' , $txt['tp-searcharticles'] , '</h3>
		</div>
		<div class="windowbg noup">
			<a href="' . $scripturl . '?action=tportal;sa=searcharticle">' . $txt['tp-searcharticles2'] . '</a>';

	if (!empty($context['TPortal']['show_download'])) {
		echo '
			| <a href="' . $scripturl . '?action=tportal;sa=download;dl=search">' . $txt['tp-searchdownloads'] . '</a>';
	}
	echo '
		</div>';
}

function template_TPsearch_below()
{
	return;
}

// Error page
function template_tperror_above()
{
	global $context;

	echo '
	<div class="errorbox">' . $context['TPortal']['tperror'] . '</div>';
}

function template_tperror_below()
{
	return;
}

function template_tpnotify_above()
{
	global $context;

	echo '
	<div class="infobox">
		<p class="centertext">' . $context['TPortal']['tpnotify'] . '</p>
	</div>';
}

function template_tpnotify_below()
{
	return;
}

// the TP tabs routine
function template_tptabs_above()
{
	global $context;

	if (!empty($context['TPortal']['tptabs'])) {
		$buts = [];
		echo '
	<div class="tp_tabs">';
		foreach ($context['TPortal']['tptabs'] as $tab) {
			$buts[] = '<a' . ($tab['is_selected'] ? ' class="tp_active"' : '') . ' href="' . $tab['href'] . '">' . $tab['title'] . '</a>';
		}

		echo implode(' | ', $buts) , '
	</div>';
	}
}

function template_tptabs_below()
{
	global $context;
}

// article layout types
function article_renders($type = 1, $single = false, $first = false)
{
	global $context;
	$code = '';
	// decide the header style, different for forumposts
	$usetitlestyle = in_array($context['TPortal']['article']['frame'], ['theme', 'title']);
	$useframestyle = in_array($context['TPortal']['article']['frame'], ['theme', 'frame']);
	$showtitle = in_array('title', $context['TPortal']['article']['visual_options']);
	$divheader = isset($context['TPortal']['article']['boardnews']) ? $context['TPortal']['boardnews_divheader'] : $context['TPortal']['articles_divheader'];
	$headerstyle = isset($context['TPortal']['article']['boardnews']) ? $context['TPortal']['boardnews_headerstyle'] : $context['TPortal']['articles_headerstyle'];
	$divbody = isset($context['TPortal']['article']['boardnews']) ? $context['TPortal']['boardnews_divbody'] : ($usetitlestyle ? $context['TPortal']['articles_divbody'] : 'windowbg');
	$nodivheader = 'tp_nodivheader';
	$noheaderstyle = 'tp_noheaderstyle';
	$nodivbody = 'tp_nodivbody';

	if ($type == 1) {
		// Layout type: normal articles
		$code = '
	<div class="tp_article render1">
		<div style="clear: both;"></div>
		<div class="' . ($usetitlestyle ? $divheader : $nodivheader) . '">
			<h3 class="' . ($usetitlestyle ? $headerstyle : $noheaderstyle) . '">' . ($showtitle ? '{article_title}' : '&nbsp;') . '</h3>
		</div>
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<div class="tp_article_info">
				' . (!$single ? '{article_avatar}' : '') . '
				{article_options}
				{article_category}
				{article_date}
				{article_author}
				{article_views}
				{article_rating}
			' . ($single ? '{article_print}' : '') . '
			</div>
			{article_text}
			<div style="clear: both;"></div>
			' . (!isset($context['TPortal']['article']['boardnews']) ? '{article_bookmark}' : '') . '
			' . (!$single ? '{article_comments_total}' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
			' . ($single ? '
				{article_moreauthor}
				{article_comments}
				{article_morelinks}' : '') . '
		</div>
	</div>';
	}
	elseif ($type == 2) {
		// Layout type: 1st normal + avatars
		if ($first) {
			$code = '
	<div class="tp_article render1">
		<div style="clear: both;"></div>
		<div class="' . ($usetitlestyle ? $divheader : $nodivheader) . '">
			<h3 class="' . ($usetitlestyle ? $headerstyle : $noheaderstyle) . '">' . ($showtitle ? '{article_title}' : '&nbsp;') . '</h3>
		</div>
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<div class="tp_article_info">
				' . (!$single ? '{article_avatar}' : '') . '
				{article_options}
				{article_category}
				{article_date}
				{article_author}
				{article_views}
				{article_rating}
			' . ($single ? '{article_print}' : '') . '
			</div>
			{article_text}
			<div style="clear: both;"></div>
			' . (!isset($context['TPortal']['article']['boardnews']) ? '{article_bookmark}' : '') . '
			' . (!$single ? '{article_comments_total}' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
			' . ($single ? '
				{article_moreauthor}
				{article_comments}
				{article_morelinks}' : '') . '
		</div>
	</div>';
		}
		else {
			$code = '
	<div class="tp_article render2">
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<span class="topslice"><span></span></span>
			<div class="tp_article_header">
				<div class="tp_article_iconcolumn">{article_iconcolumn}</div>
				{article_options}
				' . ($showtitle ? '<h2 class="tp_article_title" style="padding-left: 0;">{article_title}</h2>' : '') . '
			<div class="tp_article_info" style="padding-left: 0;">
				{article_author}
				{article_category}
				{article_date}
				{article_views}
				{article_rating}
			' . ($single ? '{article_print}' : '') . '
			</div>
			</div>
			{article_text}
			<p class="clearthefloat"></p>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '{article_bookmark}' : '') . '
			' . (!$single ? '{article_comments_total}' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
			' . ($single ? '
			<div class="tp_container">
				<div class="tp_twocolumn">
					{article_bookmark}
				</div>
				<div class="tp_twocolumn">
					{article_moreauthor}
				</div>
			</div>
			{article_comments}
			{article_morelinks}' : '') . '
		</div>
	</div>';
		}
	}
	elseif ($type == 4) {
		// Layout type: articles + icons
		$code = '
	<div class="tp_article render4">
		<div style="clear: both;"></div>
		<div class="' . ($usetitlestyle ? $divheader : $nodivheader) . '">
			<h3 class="' . ($usetitlestyle ? $headerstyle : $noheaderstyle) . '">' . ($showtitle ? '{article_title}' : '&nbsp;') . '</h3>
		</div>
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<div class="tp_article_info">
				<div class="tp_article_picturecolumn">{article_picturecolumn}</div>
				{article_options}
				{article_category}
				{article_date}
				{article_author}
				{article_views}
				{article_rating}
				' . ($single ? '{article_print}' : '') . '
			</div>
			{article_text}
			<div style="clear: both;"></div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '{article_bookmark}' : '') . '
			' . (!$single ? '{article_comments_total}' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
			' . ($single ? '
				<div class="tp_container">
				{article_bookmark}
				{article_moreauthor}
				</div>
				{article_comments}
				{article_morelinks}' : '') . '
		</div>
	</div>';
	}
	elseif ($type == 8) {
		// Layout type: articles + icons2
		$code = '
	<div class="tp_article render8">
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<div class="tp_article_header">
				<div class="tp_article_picturecolumn">{article_picturecolumn}</div>
				{article_options}
				' . ($showtitle ? '<h2 class="tp_article_title" style="padding-left: 0;">{article_title}</h2>' : '') . '
				<div class="tp_article_info" style="padding-left: 0;">
					{article_category}
					{article_date}
					{article_author}
					{article_views}
					{article_rating}
				' . ($single ? '{article_print}' : '') . '
				</div>
			</div>
			{article_text}
			<div style="clear: both;"></div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '{article_bookmark}' : '') . '
			' . (!$single ? '{article_comments_total}' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
			' . ($single ? '
					{article_bookmark}
					{article_moreauthor}
					{article_comments}
					{article_morelinks}' : '') . '
				' . ($useframestyle ? '<span class="botslice"><span></span></span>' : '') . '
		</div>
	</div>';
	}
	elseif ($type == 6) {
		// Layout type: simple articles
		if ($single) {
			$code = '
	<div class="tp_article render6">
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<div class="tp_article_header" style="padding-bottom: 0.5em;">
				{article_options}
				<h2 class="tp_article_title">{article_title}</h2>
			</div>
			<div class="tp_article_info">
				{article_date}
				{article_author}
				{article_views}
				{article_rating}
				{article_print}
			</div>
			{article_text}
			<div style="clear: both;"></div>
			{article_bookmark}
			' . (!$single ? '{article_comments_total}' : '') . '
			{article_moreauthor}
			{article_comments}
			{article_morelinks}
		</div>
	</div>';
		}
		else {
			$code = '
	<div class="tp_article render6">
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<div class="tp_article_header" style="padding-bottom: 0.5em;">
				{article_options}
				<h2 class="tp_article_title">{article_title}</h2>
			</div>
			{article_text}
			<div style="clear: both;"></div>
			{article_comments_total}
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
		</div>
	</div>';
		}
	}
	elseif ($type == 5) {
		// Layout type: normal + links
		if ($first) {
			$code = '
	<div class="tp_article render1">
		<div style="clear: both;"></div>
		<div class="' . ($usetitlestyle ? $divheader : $nodivheader) . '">
			<h3 class="' . ($usetitlestyle ? $headerstyle : $noheaderstyle) . '">' . ($showtitle ? '{article_title}' : '&nbsp;') . '</h3>
		</div>
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<div class="tp_article_info">
				' . (!$single ? '{article_avatar}' : '') . '
				{article_options}
				{article_category}
				{article_date}
				{article_author}
				{article_views}
				{article_rating}
			' . ($single ? '{article_print}' : '') . '
			</div>
			{article_text}
			<div style="clear: both;"></div>
			' . (!isset($context['TPortal']['article']['boardnews']) ? '{article_bookmark}' : '') . '
			' . (!$single ? '{article_comments_total}' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
			' . ($single ? '
				{article_moreauthor}
				{article_comments}
				{article_morelinks}' : '') . '
		</div>
	</div>';
		}
		else {
			$code = '
	' . ($showtitle ? '<div class="' . $divheader . '">
		<h3 class="' . $headerstyle . '" style="font-weight: normal;">{article_title}</h3>
	</div>' : '') . '';
		}
	}
	elseif ($type == 3) {
		// Layout type: 1st avatar + links
		if ($first) {
			$code = '
	<div class="tp_article render3">
		<div style="clear: both;"></div>
		<div class="' . ($usetitlestyle ? $divheader : $nodivheader) . '">
			<h3 class="' . ($usetitlestyle ? $headerstyle : $noheaderstyle) . '">' . ($showtitle ? '{article_title}' : '&nbsp;') . '</h3>
		</div>
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<div class="tp_article_header">
				<div class="tp_article_iconcolumn">{article_iconcolumn}</div>
				<div class="tp_article_info">
					{article_options}
					{article_author}
					{article_category}
					{article_date}
					{article_views}
					{article_rating}
				' . ($single ? '{article_print}' : '') . '
				</div>
			</div>
			{article_text}
			<div style="clear: both;"></div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '{article_bookmark}' : '') . '
			' . (!$single ? '{article_comments_total}' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
			' . ($single ? '
				<div>
					<div class="tp_container">
						{article_bookmark}
						{article_moreauthor}
					</div>
					{article_comments}
					{article_morelinks}
				</div>' : '') . '
		</div>
	</div>';
		}
		else {
			$code = '
	' . ($showtitle ? '<div style="padding: 2px 1em;">
		<div class="align_right">
			<strong>{article_title}</strong>
		</div>
		{article_date}
		<hr />
	</div>' : '') . '';
		}
	}
	elseif ($type == 9) {
		// Layout type: just links
		if ($single) {
			$code = '
	<div class="tp_article render1">
		<div style="clear: both;"></div>
		<div class="' . ($usetitlestyle ? $divheader : $nodivheader) . '">
			<h3 class="' . ($usetitlestyle ? $headerstyle : $noheaderstyle) . '">' . ($showtitle ? '{article_title}' : '&nbsp;') . '</h3>
		</div>
		<div class="' . ($useframestyle ? $divbody : $nodivbody) . '">
			<div class="tp_article_info">
				' . (!$single ? '{article_avatar}' : '') . '
				{article_options}
				{article_category}
				{article_date}
				{article_author}
				{article_views}
				{article_rating}
			' . ($single ? '{article_print}' : '') . '
			</div>
			{article_text}
			<div style="clear: both;"></div>
			' . (!isset($context['TPortal']['article']['boardnews']) ? '{article_bookmark}' : '') . '
			' . (!$single ? '{article_comments_total}' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
			' . ($single ? '
				{article_moreauthor}
				{article_comments}
				{article_morelinks}' : '') . '
		</div>
	</div>';
		}
		else {
			$code = '
	<div class="render9">
		<div class="windowbg">
			<strong>{article_title}</strong>
			{article_date}
		</div>
	</div>';
		}
	}
	elseif ($type == 7) {
		// Layout type: use custom template
		$code = '
	<div class="tp_article rendercustom">
		<div style="clear: both;"></div>
		' . $context['TPortal']['frontpage_template'] . '
	</div>';
	}
	return $code;
}

/* ********************************************** */
/* these are the prototype functions that can be called from an article template */
function article_edit()
{
	return;
}
function article_date($render = true)
{
	global $context;

	$data = '';
	if (in_array('date', $context['TPortal']['article']['visual_options'])) {
		$data = '<div class="article_date">' . (timeformat($context['TPortal']['article']['date'])) . '</div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_iconcolumn($render = true)
{
	global $context, $settings;

	if (!empty($context['TPortal']['article']['avatar'])) {
		$data = '
        <div style="overflow: hidden;">
            ' . $context['TPortal']['article']['avatar'] . '
        </div>';
	}
	else {
		$data = '
        <div style="overflow: hidden;">
            <img src="' . $settings['tp_images_url'] . '/TPnoimage' . (isset($context['TPortal']['article']['boardnews']) ? '_forum' : '') . '.png" alt="" />
        </div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_picturecolumn($render = true)
{
	global $context, $settings, $boardurl;

	if (!empty($context['TPortal']['article']['illustration']) && !isset($context['TPortal']['article']['boardnews'])) {
		$data = '
	<div class="tp_article_picture" style="width: ' . $context['TPortal']['icon_width'] . 'px; max-height: ' . $context['TPortal']['icon_width'] . 'px;"><img src="' . $boardurl . '/tp-files/tp-articles/illustrations/' . $context['TPortal']['article']['illustration'] . '"></div>';
	}
	elseif (!empty($context['TPortal']['article']['illustration']) && isset($context['TPortal']['article']['boardnews']) && ($context['TPortal']['use_attachment'] == 1)) {
		$data = '
	    <div class="tp_article_picture" style="width: ' . $context['TPortal']['icon_width'] . 'px; max-height: ' . $context['TPortal']['icon_width'] . 'px;"><img src="' . $context['TPortal']['article']['illustration'] . '"></div>';
	}
	else {
		$data = '
	<div class="tp_article_picture" style="width: ' . $context['TPortal']['icon_width'] . 'px; max-height: ' . $context['TPortal']['icon_width'] . 'px;"><img src="' . $settings['tp_images_url'] . '/TPno_illustration.png"></div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_shortdate($render = true)
{
	global $context;

	$data = '';

	if (in_array('date', $context['TPortal']['article']['visual_options'])) {
		$data = '<div class="tp_article_shortdate">' . tptimeformat($context['TPortal']['article']['date'], true, '%d %b %Y') . ' - </div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_boardnews($render = true)
{
	global $context, $scripturl, $txt;

	if (!isset($context['TPortal']['article']['replies'])) {
		return;
	}

	$data = '
		<div class="tp_article_boardnews">
			<a href="' . $scripturl . '?topic=' . $context['TPortal']['article']['id'] . '.0">' . $context['TPortal']['article']['replies'] . ' ' . ($context['TPortal']['article']['replies'] == 1 ? $txt['tp-comment'] : $txt['tp-comments']) . '</a>';
	if ($context['TPortal']['article']['locked'] == 0 && !$context['user']['is_guest']) {
		$data .= '
			&nbsp;|&nbsp;' . '<a href="' . $scripturl . '?action=post;topic=' . $context['TPortal']['article']['id'] . '.' . $context['TPortal']['article']['replies'] . ';num_replies=' . $context['TPortal']['article']['replies'] . '">' . $txt['tp-writecomment'] . '</a>';
	}

	$data .= '
		</div>';

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_author($render = true)
{
	global $scripturl, $txt, $context;

	$data = '';

	if (in_array('author', $context['TPortal']['article']['visual_options'])) {
		if ($context['TPortal']['article']['date_registered'] > 1000) {
			$data = '<div class="tp_article_author">
		' . $txt['tp-by'] . ' <a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['author_id'] . '">' . $context['TPortal']['article']['real_name'] . '</a></div>';
		}
		else {
			$data = '<div class="tp_article_author">
		' . $txt['tp-by'] . ' ' . $context['TPortal']['article']['real_name'] . '</div>';
		}
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_views($render = true)
{
	global $txt, $context;

	$data = '';

	if (in_array('views', $context['TPortal']['article']['visual_options'])) {
		$data = '
		<div class="article_views"> ' . $txt['tp-views'] . ': ' . $context['TPortal']['article']['views'] . '</div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_comments_total($render = true)
{
	global $scripturl, $txt, $context;

	$data = '';

	if ((in_array('comments', $context['TPortal']['article']['visual_options'])) || (in_array('commentallow', $context['TPortal']['article']['visual_options']))) {
		$data = '
		<div class="tp_article_boardnews">
		<a href="' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '#tp-comment">' . $context['TPortal']['article']['comments'] . ' ' . ($context['TPortal']['article']['comments'] == 1 ? $txt['tp-comment'] : $txt['tp-comments']) . '</a>';

		if (in_array('commentallow', $context['TPortal']['article']['visual_options']) && isset($context['TPortal']['can_artcomment']) == 1) {
			$data .= '
			&nbsp;|&nbsp;' . '<a href="' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '#tp-comment">' . $txt['tp-writecomment'] . '</a>';
		}

		$data .= '
			</div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_title($render = true)
{
	global $scripturl, $context;

	$data = '';

	if (in_array('title', $context['TPortal']['article']['visual_options'])) {
		if (isset($context['TPortal']['article']['boardnews'])) {
			$data = '
		<a href="' . $scripturl . '?topic=' . $context['TPortal']['article']['id'] . '">' . $context['TPortal']['article']['subject'] . '</a>';
		}
		else {
			$data = '
		<a href="' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '">' . $context['TPortal']['article']['subject'] . '</a>';
		}
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_category($render = true)
{
	global $scripturl, $txt, $context;

	$data = '';

	$catNameOrId = !empty($context['TPortal']['article']['category_shortname']) ? $context['TPortal']['article']['category_shortname'] : $context['TPortal']['article']['category'];

	if (!empty($context['TPortal']['article']['category_name'])) {
		if (isset($context['TPortal']['article']['boardnews'])) {
			$data = '
		<div class="tp_article_category">' . $txt['tp-fromcategory'] . '<a href="' . $scripturl . '?board=' . $catNameOrId . '">' . $context['TPortal']['article']['category_name'] . '</a></div>';
		}
		else {
			if (in_array('catlist', $context['TPortal']['article']['visual_options'])) {
				$data = '
			<div class="tp_article_category">' . $txt['tp-fromcategory'] . '<a href="' . $scripturl . '?cat=' . $catNameOrId . '">' . $context['TPortal']['article']['category_name'] . '</a></div>';
			}
		}
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_lead($render = true)
{
	global $context;

	$data = '';

	if (in_array('lead', $context['TPortal']['article']['visual_options'])) {
		$data = '<div class="article_lead">' . tp_renderarticle('intro') . '</div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_options($render = true)
{
	global $scripturl, $txt, $context, $settings;

	$data = '';

	if (!isset($context['TPortal']['article']['boardnews'])) {
		// give 'em a edit link? :)
		if (allowedTo('tp_articles') && ($context['TPortal']['hide_editarticle_link'] == 1)) {
			$data .= '
					<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article=' . $context['TPortal']['article']['id'] . '"><img class="tp_edit" src="' . $settings['tp_images_url'] . '/TPedit2.png" alt="" title="' . $txt['tp-edit'] . '" /></a>';
		}
		// their own article?
		elseif (allowedTo('tp_editownarticle') && !allowedTo('tp_articles') && ($context['TPortal']['article']['author_id'] == $context['user']['id']) && $context['TPortal']['hide_editarticle_link'] == 1 && $context['TPortal']['article']['locked'] != 1) {
			$data .= '
					<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article=' . $context['TPortal']['article']['id'] . '"><img class="tp_edit" src="' . $settings['tp_images_url'] . '/TPedit2.png" alt="" title="' . $txt['tp-edit'] . '" /></a>';
		}
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_print($render = true)
{
	global $scripturl, $txt, $context;

	$data = '';

	if ($context['TPortal']['print_articles'] == 1) {
		if (isset($context['TPortal']['article']['boardnews']) && !$context['user']['is_guest']) {
			$data .= '
					<div class="article_rating"><a href="' . $scripturl . '?action=printpage;topic=' . $context['TPortal']['article']['id'] . '">' . $txt['print_page'] . '</a></div>';
		}
		elseif (!$context['user']['is_guest']) {
			$data .= '
					<div class="article_rating"><a href="' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . ';print">' . $txt['tp-print'] . '</a></div>';
		}
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_text($render = true)
{
	$data = '<div class="tp_article_bodytext">' . tp_renderarticle() . '</div>';

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_rating($render = true)
{
	global $context;

	$data = '';

	if (in_array('rating', $context['TPortal']['article']['visual_options'])) {
		if (!empty($context['TPortal']['article']['voters'])) {
			$data = '<div class="article_rating">' . (render_rating($context['TPortal']['article']['rating'], count(explode(',', $context['TPortal']['article']['voters'])), $context['TPortal']['article']['id'], (isset($context['TPortal']['article']['can_rate']) ? $context['TPortal']['article']['can_rate'] : false), $render)) . '</div>';
		}
		else {
			$data = '<div class="article_rating">' . (render_rating($context['TPortal']['article']['rating'], 0, $context['TPortal']['article']['id'], (isset($context['TPortal']['article']['can_rate']) ? $context['TPortal']['article']['can_rate'] : false), $render)) . '</div>';
		}
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_moreauthor($render = true)
{
	global $scripturl, $txt, $context;

	$data = '';

	if (in_array('avatar', $context['TPortal']['article']['visual_options'])) {
		$data .= '<div>
                <div class="tp_article_authorinfo">
                    <h2 class="tp_article_author">' . $txt['tp-authorinfo'] . '</h2>';
		if ($context['TPortal']['article']['date_registered'] > 1000) {
			$data .= '
                    ' . (!empty($context['TPortal']['article']['avatar']) ? '<a class="tp_avatar_author" href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['author_id'] . '" title="' . $context['TPortal']['article']['real_name'] . '">' . $context['TPortal']['article']['avatar'] . '</a>' : '') . '
                    <div class="tp_author_text">
                        <a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['author_id'] . '">' . $context['TPortal']['article']['real_name'] . '</a>' . $txt['tp-poster1'] . $context['forum_name'] . $txt['tp-poster2'] . timeformat($context['TPortal']['article']['date_registered']) . $txt['tp-poster3'] .
						$context['TPortal']['article']['posts'] . $txt['tp-poster4'] . timeformat($context['TPortal']['article']['last_login']) . '.';
		}
		else {
			$data .= '
                    <div class="tp_author_text">
                        <em>' . $context['TPortal']['article']['real_name'] . $txt['tp-poster5'] . '</em>';
		}
		$data .= '</div>
                </div>
				</div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_avatar($render = true)
{
	global $scripturl, $context;

	$data = '';

	if (in_array('avatar', $context['TPortal']['article']['visual_options'])) {
		$data = (!empty($context['TPortal']['article']['avatar']) ? '<div class="tp_avatar_single" ><a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['author_id'] . '" title="' . $context['TPortal']['article']['real_name'] . '">' . $context['TPortal']['article']['avatar'] . '</a></div>' : '');
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_bookmark($render = true)
{
	global $scripturl, $settings, $context;

	$data = '';

	if (in_array('social', $context['TPortal']['article']['visual_options'])) {
		$data .= '
	<div>
		<div class="tp_article_social">';
		if (!$context['TPortal']['hide_article_facebook'] == '1') {
			$data .= '<a href="http://www.facebook.com/sharer.php?u=' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '" target="_blank"><img class="tp_social" src="' . $settings['tp_images_url'] . '/social/facebook.png" alt="Share on Facebook!" title="Share on Facebook!" /></a>';
		}
		if (!$context['TPortal']['hide_article_twitter'] == '1') {
			$data .= '<a href="https://x.com/intent/post?url=' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '" target="_blank"><img class="tp_social" title="Share on X!" src="' . $settings['tp_images_url'] . '/social/twitter-x.png" alt="Share on Twitter!" /></a>';
		}
		if (!$context['TPortal']['hide_article_reddit'] == '1') {
			$data .= '<a href="http://www.reddit.com/submit?url=' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '" target="_blank"><img class="tp_social" src="' . $settings['tp_images_url'] . '/social/reddit.png" alt="Reddit" title="Reddit" /></a>';
		}
		if (!$context['TPortal']['hide_article_digg'] == '1') {
			$data .= '<a href="http://digg.com/submit?url=' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '&title=' . $context['TPortal']['article']['subject'] . '" target="_blank"><img class="tp_social" title="Digg this story!" src="' . $settings['tp_images_url'] . '/social/digg.png" alt="Digg this story!" /></a>';
		}
		if (!$context['TPortal']['hide_article_delicious'] == '1') {
			$data .= '<a href="http://del.icio.us/post?url=' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '&title=' . $context['TPortal']['article']['subject'] . '" target="_blank"><img class="tp_social" src="' . $settings['tp_images_url'] . '/social/delicious.png" alt="Del.icio.us" title="Del.icio.us" /></a>';
		}
		if (!$context['TPortal']['hide_article_stumbleupon'] == '1') {
			$data .= '<a href="http://www.stumbleupon.com/submit?url=' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '" target="_blank"><img class="tp_social" src="' . $settings['tp_images_url'] . '/social/stumbleupon.png" alt="StumbleUpon" title="Stumbleupon" /></a>';
		}
		$data .= '
		</div>
	</div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_comments($render = true)
{
	global $scripturl, $txt, $settings, $context;

	$data = '';

	if ((in_array('comments', $context['TPortal']['article']['visual_options'])) || (in_array('commentallow', $context['TPortal']['article']['visual_options']))) {
		$data .= '
	<a name="tp-comment"></a>
	<h2 class="titlebg tp_article_extra">' . $txt['tp-comments'] . ': ' . $context['TPortal']['article_comments_count'] . '' . (tp_hidepanel('articlecomments', false, true, '5px 5px 0 5px')) . '</h2> ';
	}

	if (in_array('comments', $context['TPortal']['article']['visual_options']) && !$context['TPortal']['article_comments_count'] == 0) {
		$data .= '
	<div id="articlecomments" class="tp_commentsbox"' . (in_array('articlecomments', $context['tp_panels']) ? ' style="display: none;"' : '') . '>';

		$counter = 1;
		if (isset($context['TPortal']['article']['comment_posts'])) {
			foreach ($context['TPortal']['article']['comment_posts'] as $comment) {
				$data .= '
					<div class="tp_article_comment ' . ($context['TPortal']['article']['author_id'] != $comment['poster_id'] ? 'tp_owncomment' : 'tp_othercomment') . '">
					<a id="comment' . $comment['id'] . '"></a>';
				// can we edit the comment or are the owner of it?
				if (allowedTo('tp_articles') || $comment['poster_id'] == $context['user']['id'] && !$context['user']['is_guest']) {
					$data .= '<div class="floatright"><i><a class="active" href="' . $scripturl . '?action=tportal;sa=killcomment;comment=' . $comment['id'] . '" onclick="javascript:return confirm(\'' . $txt['tp-confirmcommentdelete'] . '\')"><span>' . $txt['tp-delete'] . '</span></a></i></div>';
				}
				// not a guest
				if ($comment['poster_id'] > 0) {
					$data .= '
					<span class="tp_comment_author">' . (!empty($comment['avatar']['image']) ? $comment['avatar']['image'] : '') . '</span>';
				}
				$data .= '
					<strong>' . $counter++ . ') ' . $comment['subject'] . '</strong>
					' . (($comment['is_new'] && $context['user']['is_logged']) ? '<a href="" id="newicon" class="new_posts" >' . $txt['new'] . '</a>' : '') . '';
				if ($comment['poster_id'] > 0) {
					$data .= '
						<div class="middletext" style="padding-top: 0.5em;"> ' . $txt['tp-bycom'] . ' <a href="' . $scripturl . '?action=profile;u=' . $comment['poster_id'] . '">' . $comment['poster'] . '</a>&nbsp;' . $txt['on'] . ' ' . $comment['date'] . '</div>';
				}
				else {
					$data .= '
						<div class="middletext" style="padding-top: 0.5em;"> ' . $txt['tp-bycom'] . ' ' . $txt['guest_title'] . '&nbsp;' . $txt['on'] . ' ' . $comment['date'] . '</div>';
				}
				$data .= '
					<p class="clearthefloat"></p>
					<div class="tp_textcomment"><div class="body">' . $comment['text'] . '</div></div>';
				$data .= '
				</div>';
			}
		}
		$data .= '
			</div>';
	}

	if (in_array('commentallow', $context['TPortal']['article']['visual_options']) && isset($context['TPortal']['can_artcomment']) == 1) {
		$data .= '
			<div class="tp_pad">
				<form accept-charset="' . $context['character_set'] . '"  name="tp_article_comment" action="' . $scripturl . '?action=tportal;sa=comment" method="post" style="margin: 0; padding: 0;">
						<input type="text" name="tp_article_comment_title" style="width: 99%;" value="Re: ' . strip_tags($context['TPortal']['article']['subject']) . '">
						<textarea style="width: 99%; height: 8em;" name="tp_article_bodytext"></textarea><br>';

		if (!empty($context['TPortal']['allow_links_article_comments']) == 0) {
			$data .= '<em>' . $txt['tp-nolinkcomments'] . '<em>';
		}

		$data .= '
						<div class="tp_pad"><input type="submit" id="tp_article_comment_submit" class="button button_submit" value="' . $txt['tp-submit'] . '"></div>
						<input type="hidden" name="tp_article_type" value="article_comment">
						<input type="hidden" name="tp_article_id" value="' . $context['TPortal']['article']['id'] . '">
						<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
				</form>
			</div>';
	}
	elseif (in_array('commentallow', $context['TPortal']['article']['visual_options']) && isset($context['TPortal']['can_artcomment']) != 1) {
		$data .= '
			<div class="tp_pad"><em>' . $txt['tp-cannotcomment'] . '</em></div>';
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function article_morelinks($render = true)
{
	global $scripturl, $txt, $context;

	$data = '';

	if (in_array('category', $context['TPortal']['article']['visual_options'])) {
		if (in_array('category', $context['TPortal']['article']['visual_options']) && isset($context['TPortal']['article']['others'])) {
			$data .= '
	<h2 class="titlebg tp_article_extra"><a href="' . $scripturl . '?cat=' . (!empty($context['TPortal']['article']['category_shortname']) ? $context['TPortal']['article']['category_shortname'] : $context['TPortal']['article']['category']) . '">' . $txt['tp-articles'] . ' ' . $txt['in'] . ' &#171; ' . $context['TPortal']['article']['category_name'] . ' &#187;</a></h2>

	<div style="overflow: hidden;">
		<ul class="disc">';
			foreach ($context['TPortal']['article']['others'] as $art) {
				$data .= '<li' . (isset($art['selected']) ? ' class="selected"' : '') . '><a href="' . $scripturl . '?page=' . (!empty($art['shortname']) ? $art['shortname'] : $art['id']) . '">' . html_entity_decode($art['subject']) . '</a></li>';
			}
			$data .= '
		</ul>
	</div>';
		}
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function render_rating($total, $votes, $id, $can_rate = false, $render = true)
{
	global $txt, $context, $settings, $scripturl;

	$data = '';

	if (!is_numeric($total)) {
		$total = (int)$total;
	}

	if (!is_numeric($votes)) {
		$votes = (int)$votes;
	}

	if ($total == 0 && $votes > 0) {
		$data .= ' ' . $txt['tp-ratingaverage'] . ' 0 (' . $txt['tp-ratingvotes'] . ' ' . $votes . ')';
	}
	elseif ($total == 0 && $votes == 0) {
		$data .= ' ' . $txt['tp-ratingaverage'] . ' 0 (' . $txt['tp-ratingvotes'] . ' 0)';
	}
	else {
		$data .= ' ' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src=" ' . $settings['tp_images_url'] . '/TPblue.png" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', ceil($total / $votes))) : ceil($total / $votes)) . ' (' . $txt['tp-ratingvotes'] . ' ' . $votes . ')';
	}

	// can we rate it?
	if ($context['TPortal']['single_article']) {
		if ($context['user']['is_logged'] && $can_rate) {
			$data .= '
			<form action="' . $scripturl . '?action=tportal;sa=rate_article" style="margin: 0; padding: 0; display: inline;" method="post">
				<select name="tp_article_rating" size="1" style="width: 4em;">';

			for ($u = $context['TPortal']['maxstars'] ; $u > 0 ; $u--) {
				$data .= '
					<option value="' . $u . '">' . $u . '</option>';
			}

			$data .= '
				</select>
				<input type="submit" name="tp_article_rating_submit" value="' . $txt['tp_rate'] . '">
				<input type="hidden" name="tp_article_type" value="article_rating">
				<input type="hidden" name="tp_article_id" value="' . $id . '">
				<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
			</form>';
		}
		else {
			if (!$context['user']['is_guest']) {
				$data .= ' 	<em class="tp_article_rate smalltext">' . $txt['tp-dlhaverated'] . '</em>';
			}
		}
	}

	if ($render) {
		echo $data;
	}
	else {
		return $data;
	}
}

function tp_grids()
{
	// the built-in grids
	$grid = [
		// vertical
		1 => [
			'cols' => 1,
			'code' => '
			<div class="tp_container">
				<div class="tp_onecolumn">{featured}</div>
			</div>
			<div class="tp_container">
				<div class="tp_onecolumn">{col1}{col2}</div>
			</div>'
		],
		// featured 1 col, 2 cols
		2 => [
			'cols' => 2,
			'code' => '
			<div class="tp_container">
				<div class="tp_onecolumn">{featured}</div>
			</div>
			<div class="tp_container">
				<div class="tp_twocolumn"><div class="tp_leftcol">{col1}</div></div>
				<div class="tp_twocolumn"><div class="tp_rightcol">{col2}</div></div>
			</div>'
		],
		// featured left col, rest right col
		3 => [
			'cols' => 1,
			'code' => '
			<div class="tp_container">
				<div class="tp_twocolumn"><div class="tp_leftcol">{featured}</div></div>
				<div class="tp_twocolumn"><div class="tp_rightcol">{col1}{col2}</div></div>
			</div>'
		],
		// 2 cols
		4 => [
			'cols' => 2,
			'code' => '
			<div class="tp_container">
				<div class="tp_twocolumn"><div class="tp_leftcol">{featured}{col1}</div></div>
				<div class="tp_twocolumn"><div class="tp_rightcol">{col2}</div></div>
			</div>'
		],
		// 2 cols, then featured at bottom
		5 => [
			'cols' => 1,
			'code' => '
			<div class="tp_container">
				<div class="tp_onecolumn">{col1}{col2}</div>
			</div>
			<div class="tp_container">
				<div class="tp_onecolumn">{featured}</div>
			</div>'
		],
		// rest left col, featured right col
		6 => [
			'cols' => 1,
			'code' => '
			<div class="tp_container">
				<div class="tp_twocolumn"><div class="tp_rightcol">{col1}{col2}</div></div>
				<div class="tp_twocolumn"><div class="tp_leftcol">{featured}</div></div>
			</div>'
		],
	];
	return $grid;
}

/* for blockarticles */
// This is the template for single article
function template_blockarticle()
{
	global $context;

	// use a customised template or the built-in?
	if (!empty($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['template'])) {
		render_template($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['template']);
	}
	else {
		render_template(blockarticle_renders());
	}
}
function blockarticle_renders()
{
	$code = '
	<div class="tp_blockarticle render1">
		<div class="tp_article_info">
			{blockarticle_author}
			{blockarticle_date}
			{blockarticle_views}
		</div>
		{blockarticle_text}
		{blockarticle_moreauthor}
	</div>
		';
	return $code;
}

function blockarticle_date($render = true)
{
	global $context;

	if (in_array('date', $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options'])) {
		echo '
		<span class="article_date"> ' . (timeformat($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['date'])) . '</span>';
	}
	else {
		echo '';
	}
}

function blockarticle_author($render = true)
{
	global $scripturl, $txt, $context;

	if (in_array('author', $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options'])) {
		if ($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['date_registered'] > 1000) {
			echo '
		<span class="tp_article_author">' . $txt['tp-by'] . ' <a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['author_id'] . '">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['real_name'] . '</a></span>';
		}
		else {
			echo '
		<span class="tp_article_author">' . $txt['tp-by'] . ' ' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['real_name'] . '</span>';
		}
	}
	else {
		echo '';
	}
}

function blockarticle_views($render = true)
{
	global $txt, $context;

	if (in_array('views', $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options'])) {
		echo '
		<span class="article_views">' . $txt['tp-views'] . ': ' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['views'] . '</span>';
	}
	else {
		echo '';
	}
}

function blockarticle_text($render = true)
{
	echo '
	<div class="tp_article_bodytext">' . tp_renderblockarticle() . '</div>';
}

function blockarticle_moreauthor($render = true)
{
	global $scripturl, $txt, $context;

	if (in_array('avatar', $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options'])) {
		if ($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['date_registered'] > 1000) {
			echo '
		<div class="tp_article_authorinfo">
			<h3>' . $txt['tp-authorinfo'] . '</h3>
			' . (!empty($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['avatar']) ? '<a class="tp_avatar_author" href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['author_id'] . '" title="' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['real_name'] . '">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['avatar'] . '</a>' : '') . '
			<div class="tp_author_text">
				<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['author_id'] . '">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['real_name'] . '</a>' . $txt['tp-poster1'] . $context['forum_name'] . $txt['tp-poster2'] . timeformat($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['date_registered']) . $txt['tp-poster3'] .
				$context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['posts'] . $txt['tp-poster4'] . timeformat($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['last_login']) . '.
			</div>
		</div>';
		}
		else {
			echo '
		<div class="tp_article_authorinfo">
			<h3>' . $txt['tp-authorinfo'] . '</h3>
			<div class="tp_author_text">
				<em>' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['real_name'] . $txt['tp-poster5'] . '</em>
			</div>
		</div>';
		}
	}
	else {
		echo '';
	}
}

function category_childs()
{
	global $context, $scripturl;

	if (!empty($context['TPortal']['category']['options']['showchild']) == 1) {

	echo '
		<ul class="tp_category_children">';
		foreach ($context['TPortal']['category']['children'] as $ch => $child) {	

			echo '<li><a href="' , $scripturl , '?cat=' , $child['id'] , '">' , $child['value1'] ,' (' , $child['articlecount'] , ')</a></li>';
		}
	}

	echo '
	</ul>';

	return;
}

function template_subtab_above()
{
	global $context, $txt;

	if (isset($context['TPortal']['subtabs']) && (is_countable($context['TPortal']['subtabs']) && count($context['TPortal']['subtabs']) > 1)) {

		tp_template_button_strip($context['TPortal']['subtabs']);

	}
}

function template_subtab_below()
{
	return;
}

function template_tpadm_above()
{
	global $context, $txt;

	echo '
	<!-- #tp_admin_menu -->
	<div class="tp_admin_menu">
		<div class="cat_bar">
			<h3 class="catbg">' . $txt['tp-adminmenu'] . '</h3>
		</div>
		<div class="roundframe noup">';

	if (is_array($context['admin_tabs']) && count($context['admin_tabs']) > 0) {
		echo '
			<ul style="padding-bottom: 10px;">';
		foreach ($context['admin_tabs'] as $ad => $tab) {
			echo '
				<li><div class="largetext">' , isset($context['admin_header'][$ad]) ? $context['admin_header'][$ad] : '' , '</div>
					';
			$tbas = [];
			foreach ($tab as $tb) {
				$tbas[] = '<a href="' . $tb['href'] . '">' . ($tb['is_selected'] ? '<b>' . $tb['title'] . '</b>' : $tb['title']) . '</a>';
			}

			// if new style...
			if ($context['TPortal']['oldsidebar'] == 0) {
				echo '<div class="normaltext">' , implode(', ', $tbas) , '</div>
				</li>';
			}
			else {
				echo '<div class="middletext" style="margin: 0; line-height: 1.3em;">' , implode('<br>', $tbas) , '</div>
				</li>';
			}
		}
		echo '
			<div style="clear:both;"></div></ul>';
	}

	echo '
		</div>
	</div>
	<div class="tp_admin_content" style="margin-top: 0;">';
}

function template_tpadm_below()
{
	echo '

	</div>
	<div style="clear:both;"></div><!-- #tp_admin_menu -->';

	return;
}

function template_tp_fatal_error()
{
	global $context, $txt;

	echo '
	<div id="fatal_error">
		<div class="cat_bar">
			<h3 class="catbg">' , $txt['tp-error'], '</h3>
		</div>
		<div class="windowbg">
			<div class="padding">', $context['TPortal']['errormessage'] , '</div>
		</div>
	</div>';

	// Show a back button (using javascript.)
	echo '
	<div class="centertext"><a href="javascript:history.go(-1)">', $txt['back'], '</a></div>';
}

// Format a time to make it look purdy.
function tptimeformat($log_time, $show_today = true, $format = '%d %b %Y')
{
	global $context, $user_info, $txt, $modSettings, $smcFunc;

	$time = $log_time + ($user_info['time_offset'] + (isset($modSettings['time_offset']) ? $modSettings['time_offset'] : 0)) * 3600;

	// We can't have a negative date (on Windows, at least.)
	if ($log_time < 0) {
		$log_time = 0;
	}

	// Today and Yesterday?
	if ($modSettings['todayMod'] >= 1 && $show_today === true) {
		// Get the current time.
		$nowtime = forum_time();

		$then = @getdate($time);
		$now = @getdate($nowtime);

		// Try to make something of a time format string...
		$s = strpos($format, '%S') === false ? '' : ':%S';
		if (strpos($format, '%H') === false && strpos($format, '%T') === false) {
			$h = strpos($format, '%l') === false ? '%I' : '%l';
			$today_fmt = $h . ':%M' . $s . ' %p';
		}
		else {
			$today_fmt = '%H:%M' . $s;
		}

		// Same day of the year, same year.... Today!
		if ($then['yday'] == $now['yday'] && $then['year'] == $now['year']) {
			return $txt['today'] . tptimeformat($log_time, $today_fmt, $format);
		}

		// Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
		if ($modSettings['todayMod'] == '2' && (($then['yday'] == $now['yday'] - 1 && $then['year'] == $now['year']) || ($now['yday'] == 0 && $then['year'] == $now['year'] - 1) && $then['mon'] == 12 && $then['mday'] == 31)) {
			return $txt['yesterday'] . tptimeformat($log_time, $today_fmt, $format);
		}
	}

	$str = !is_bool($show_today) ? $show_today : $format;

	if (setlocale(LC_TIME, $txt['lang_locale'])) {
		foreach (['%a', '%A', '%b', '%B'] as $token) {
			if (strpos($str, $token) !== false) {
				$str = str_replace($token, !empty($txt['lang_capitalize_dates']) ? $smcFunc['ucwords'](smf_strftime($token, $time)) : smf_strftime($token, $time), $str);
			}
		}
	}
	else {
		// Do-it-yourself time localization.  Fun.
		foreach (['%a' => 'days_short', '%A' => 'days', '%b' => 'months_short', '%B' => 'months'] as $token => $text_label) {
			if (strpos($str, $token) !== false) {
				$str = str_replace($token, $txt[$text_label][(int) smf_strftime($token === '%a' || $token === '%A' ? '%w' : '%m', $time)], $str);
			}
		}
		if (strpos($str, '%p')) {
			$str = str_replace('%p', (smf_strftime('%H', $time) < 12 ? 'am' : 'pm'), $str);
		}
	}

	// Windows doesn't support %e; on some versions, smf_strftime fails altogether if used, so let's prevent that.
	if ($context['server']['is_windows'] && strpos($str, '%e') !== false) {
		$str = str_replace('%e', ltrim(smf_strftime('%d', $time), '0'), $str);
	}

	// Format any other characters..
	return smf_strftime($str, $time);
}

// Generate a strip of buttons.
function tp_template_button_strip($button_strip, $direction = 'top', $strip_options = [])
{
	global $context, $txt;

	if (!is_array($strip_options)) {
		$strip_options = [];
	}

	// Create the buttons...
	$buttons = [];
	foreach ($button_strip as $key => $value) {
		if (!isset($value['test']) || !empty($context[$value['test']])) {
			$buttons[] = '<a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button button_strip_' . $key . '' . ($value['active'] ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a>';
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons)) {
		return;
	}

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="tpbuttons buttonlist', !empty($direction) ? ' align_' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"' : ''), '>',
	implode('', $buttons), '
			<p class="clearthefloat"></p>
		</div>';
}

function template_updatelog()
{
	global $context;

	echo '<div class="tborder">' . $context['TPortal']['updatelog'] , '<hr /></div>';
}
