<?php
/**
 * @package TinyPortal
 * @version 3.0.1
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
use TinyPortal\Block as TPBlock;

// ** Sections **
// Shoutbox settings Page
// Shoutbox administration Page
// Shoutbox Block template
// Shoutbox single shout template
// View shouts Profile page

function template_tpshout_above()
{
	return;
}

function template_tpshout_below()
{
	return;
}

function template_tpshout_bigscreen()
{
	global $context, $scripturl, $txt, $smcFunc;

	$shouts = $context['TPortal']['rendershouts'];

	if (isset($context['TPortal']['querystring'])) {
		$tp_where = $context['TPortal']['querystring'];
	}
	else {
		$tp_where = '';
	}

	$context['tp_shoutbox_form'] = 'tp_shoutbox';
	$context['tp_shout_post_box_name'] = 'tp_shout';

	echo '
	<div id="tpshout_bigscreen">
		<div></div>
		<div class="title_bar"><h3 class="titlebg">' , $txt['tp-shoutbox'] , '</h3></div>
		<div class="windowbg noup">';

	echo '
			<div>', $shouts, '</div>';

	echo '
			<form  accept-charset="', $context['character_set'], '" class="smalltext" name="' . $context['tp_shoutbox_form'] . '"  id="' . $context['tp_shoutbox_form'] . '" action="' . $scripturl . '?action=tpshout;shout=save" method="post" >
				<input type="hidden" name="tp-shout-name" value="' . $context['user']['name'] . '" />
				<input type="hidden" name="tp-shout-url" value="' . $smcFunc['htmlspecialchars']($tp_where) . '" />
				<input type="hidden" name="sc" value="', $context['session_id'], '" />
			</form>
		</div>
	</div>';
}

// Shoutbox settings Page
function template_tpshout_admin_settings()
{
	global $context, $scripturl, $txt, $settings;

	echo '
	<form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpshout;shout=admin"  method="post">
		<input type="hidden" name="TPadmin_blocks" value="set" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input type="hidden" name="tpadmin_form" value="singlemenuedit">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-shoutboxsettings'] . '</h3></div>
		<p class="information">' , $txt['tp-shoutboxsettingsinfo'] , '</p>
			<div class="cat_bar"><h3 class="catbg">' . $txt['tp-settings'] . '</h3></div>
			<div id="tpshout_admin_settings">
				<div class="windowbg noup">
					<dl class="settings">
						<dt>
							' . $txt['tp-shoutbox_showsmile'] . '
						</dt>
						<dd>
							<input type="radio" name="tp_shoutbox_smile" value="1" ' , $context['TPortal']['show_shoutbox_smile'] == '1' ? 'checked="checked"' : '' , ' /> ' . $txt['tp-yes'] . '
							<input type="radio" name="tp_shoutbox_smile" value="0" ' , $context['TPortal']['show_shoutbox_smile'] == '0' ? 'checked="checked"' : '' , ' /> ' . $txt['tp-no'] . '<br>
						</dd>
						<dt>
							' . $txt['tp-shoutbox_showicons'] . '
						</dt>
						<dd>
							<input type="radio" name="tp_shoutbox_icons" value="1" ' , $context['TPortal']['show_shoutbox_icons'] == '1' ? 'checked="checked"' : '' , ' /> ' . $txt['tp-yes'] . '
							<input type="radio" name="tp_shoutbox_icons" value="0" ' , $context['TPortal']['show_shoutbox_icons'] == '0' ? 'checked="checked"' : '' , ' /> ' . $txt['tp-no'] . '<br>
						</dd>
						<dt>
							' . $txt['tp-shout-allow-links'] . '
						</dt>
						<dd>
							<input type="radio" name="tp_shout_allow_links" value="1" ' , $context['TPortal']['shout_allow_links'] == '1' ? ' checked="checked"' : '' , ' /> ' . $txt['tp-yes'] . '
							<input type="radio" name="tp_shout_allow_links" value="0" ' , $context['TPortal']['shout_allow_links'] == '0' ? ' checked="checked"' : '' , ' /> ' . $txt['tp-no'] . '<br>
						</dd>
						<dt>
							' . $txt['tp-shoutboxusescroll'] . '
						</dt>
						<dd>
							<input type="radio" name="tp_shoutbox_usescroll" value="1" ' , $context['TPortal']['shoutbox_usescroll'] > 0 ? ' checked="checked"' : '' , ' /> ' . $txt['tp-yes'] . '
							<input type="radio" name="tp_shoutbox_usescroll" value="0" ' , $context['TPortal']['shoutbox_usescroll'] == '0' ? ' checked="checked"' : '' , ' /> ' . $txt['tp-no'] . '<br>
						</dd>
						<dt>
							<label for="tp_shoutbox_scrollduration">' . $txt['tp-shoutboxduration'] . '</label>
						</dt>
						<dd>
							<input type="number" name="tp_shoutbox_scrollduration" id="tp_shoutbox_scrollduration" value="' . $context['TPortal']['shoutbox_scrollduration'] . '" style="width: 6em" min="1" max="5" step="1" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_refresh">' . $txt['tp-shout-autorefresh'] . '</label><br>
							<div class="noticebox">' . $txt['tp-shout-autorefreshdesc'] . '</div>
						</dt>
						<dd>
							<input type="number" name="tp_shoutbox_refresh" id="tp_shoutbox_refresh" value="' ,$context['TPortal']['shoutbox_refresh'], '" style="width: 6em" min="0" max="60" step="1" /><br>
						</dd>
						<dt>
							' . $txt['shout_submit_returnkey'] . '
						</dt>
						<dd>
							<input type="radio" name="tp_shout_submit_returnkey" id="tp_shout_submit_returnkey1" value="1" ' , $context['TPortal']['shout_submit_returnkey'] == '1' ? ' checked="checked"' : '' , ' /> <label for="tp_shout_submit_returnkey1">' . $txt['tp-yes-enter'] . '</label><br>
							<input type="radio" name="tp_shout_submit_returnkey" id="tp_shout_submit_returnkey2" value="2" ' , $context['TPortal']['shout_submit_returnkey'] == '2' ? ' checked="checked"' : '' , ' /> <label for="tp_shout_submit_returnkey2">' . $txt['tp-yes-ctrl'] . '</label><br>
							<input type="radio" name="tp_shout_submit_returnkey" id="tp_shout_submit_returnkey3" value="0" ' , $context['TPortal']['shout_submit_returnkey'] == '0' ? ' checked="checked"' : '' , ' /> <label for="tp_shout_submit_returnkey3">' . $txt['tp-yes-shout'] . '</label><br>
						</dd>
					</dl>
					<hr>
					<dl class="settings">';
	echo '
						<dt>
							<label for="tp_shoutbox_limit">' . $txt['tp-shoutboxlimit'] . '</label>
						</dt>
						<dd>
							<input type="number" id="tp_shoutbox_limit" name="tp_shoutbox_limit" value="' ,$context['TPortal']['shoutbox_limit'], '" style="width: 6em" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_maxlength">' . $txt['tp-shoutboxmaxlength'] . '</label>
						</dt>
						<dd>
							<input type="number" id="tp_shoutbox_maxlength" name="tp_shoutbox_maxlength" value="' ,$context['TPortal']['shoutbox_maxlength'], '" style="width: 6em" /><br>
						</dd>
						<dt>
							<a href="', $scripturl, '?action=helpadmin;help=time_format" onclick="return reqOverlayDiv(this.href);">
							<span class="tptooltip" title="', $txt['help'], '"></span></a><label for="tp_shoutbox_timeformat2">' . $txt['tp-shoutboxtimeformat'] . '</label>
						</dt>
						<dd>
							<input type="text" id="tp_shoutbox_timeformat2" name="tp_shoutbox_timeformat2" value="' ,$context['TPortal']['shoutbox_timeformat2'], '" style="width: 15em" /><br>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>
							<span class="font-strong">
							<a href="', $scripturl, '?action=helpadmin;help=tp-shoutboxcolorsdesc" onclick="return reqOverlayDiv(this.href);">
							<span class="tptooltip" title="', $txt['help'], '"></span></a>' . $txt['tp-shoutboxcolors'] . '</span>
						</dt>
						<dd></dd>';
	if (isset($context['TPortal']['use_groupcolor']) && (($context['TPortal']['use_groupcolor']) != 1)) {
		echo '
						<dt>
							<a href="', $scripturl, '?action=helpadmin;help=tp-shoutbox_use_groupcolordesc" onclick="return reqOverlayDiv(this.href);">
							<span class="tptooltip" title="', $txt['help'], '"></span></a>' . $txt['tp-shoutbox_use_groupcolor'] . '
						</dt>
						<dd>
							<input type="radio" name="tp_shoutbox_use_groupcolor" value="1" ' , $context['TPortal']['shoutbox_use_groupcolor'] == '1' ? 'checked="checked"' : '' , ' /> ' . $txt['tp-yes'] . '
							<input type="radio" name="tp_shoutbox_use_groupcolor" value="0" ' , $context['TPortal']['shoutbox_use_groupcolor'] == '0' ? 'checked="checked"' : '' , ' /> ' . $txt['tp-no'] . '<br>
						</dd>';
	}
	echo '
						<dt>
							<label for="tp_shoutbox_textcolor">' . $txt['tp-shoutboxtextcolor'] . '</label>
						</dt>
						<dd>
							<input type="text" id="tp_shoutbox_textcolor" name="tp_shoutbox_textcolor" value="' ,$context['TPortal']['shoutbox_textcolor'], '" size="10" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_timecolor">' . $txt['tp-shoutboxtimecolor'] . '</label>
						</dt>
						<dd>
							<input type="text" id="tp_shoutbox_timecolor" name="tp_shoutbox_timecolor" value="' ,$context['TPortal']['shoutbox_timecolor'], '" size="10" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_linecolor1">' . $txt['tp-shoutboxlinecolor1'] . '</label>
						</dt>
						<dd>
							<input type="text" id="tp_shoutbox_linecolor1" name="tp_shoutbox_linecolor1" value="' ,$context['TPortal']['shoutbox_linecolor1'], '" size="10" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_linecolor2">' . $txt['tp-shoutboxlinecolor2'] . '</label>
						</dt>
						<dd>
							<input type="text" id="tp_shoutbox_linecolor2" name="tp_shoutbox_linecolor2" value="' ,$context['TPortal']['shoutbox_linecolor2'], '" size="10" /><br>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>
							' . $txt['tp-show_profile_shouts'] . '
						</dt>
						<dd>
							<input type="radio" name="tp_show_profile_shouts" value="1" ' , $context['TPortal']['profile_shouts_hide'] == '1' ? ' checked="checked"' : '' , ' /> ' . $txt['tp-yes'] . '
							<input type="radio" name="tp_show_profile_shouts" value="0" ' , $context['TPortal']['profile_shouts_hide'] == '0' ? ' checked="checked"' : '' , ' /> ' . $txt['tp-no'] . '<br>
						</dd>
					</dl>
				<input type="submit" class="button" value="' . $txt['tp-send'] . '" name="' . $txt['tp-send'] . '">
				<p class="clearthefloat"></p>
			</div>
		</div>
	</form>';
}

// Shoutbox administration Page
function template_tpshout_admin()
{
	global $context, $scripturl, $txt;

	echo '
	<form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpshout;shout=admin"  method="post">
		<input type="hidden" name="TPadmin_blocks" value="set" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input type="hidden" name="tpadmin_form" value="singlemenuedit">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-shoutboxadmin'] . '</h3></div>
		<p class="information">' , $txt['tp-shoutboxadmininfo'] , '</p>
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-shoutmessages'] . '</h3></div>
		<div id="tpshout_admin">
			<div class="windowbg noup">
				<div class="tp_addborder">
					<div class="tp_flexbox">
						<div class="tp_name"><b>' . $txt['tp-shoutboxitems'] . '</b></div>
						<div>' . $context['TPortal']['shoutbox_pageindex'] . '</div>
					</div>
				</div>';

	foreach ($context['TPortal']['admin_shoutbox_items'] as $admin_shouts) {
		echo '
				<div class="tp_flexbox tp_addborder">
					<div class="tp_shoutip fullwidth-on-res-layout">
						' . $admin_shouts['poster'] . ' [' . $admin_shouts['ip'] . ']<br>' . $admin_shouts['time'] . '<br>
						' . $admin_shouts['sort_shoutbox_id'] . '&nbsp;(' . $admin_shouts['shoutbox_id'] . ') <br> ' . $admin_shouts['sort_member'] . ' <br> ' . $admin_shouts['sort_ip'] . '<br>' . $admin_shouts['single'] . '
					</div>
					<div>
						<textarea name="tp_shoutbox_item' . $admin_shouts['id'] . '" class="tp_shoutbox_item" rows="5" cols="40">' . html_entity_decode($admin_shouts['body']) . '</textarea>
					</div>
					<div>
						<input type="hidden" name="tp_shoutbox_hidden' . $admin_shouts['id'] . '" value="1">
						<div><b><input type="checkbox" name="tp_shoutbox_remove' . $admin_shouts['id'] . '" value="ON"> ' . $txt['tp-remove'] . '</b></div>
					</div>
				</div>';
	}
	echo '
				<div class="tpright">' . $context['TPortal']['shoutbox_pageindex'] . '</div>
				<div class="padding-div">
					<input type="checkbox" name="tp_shoutsdelall" value="ON" onclick="javascript:return confirm(\'' . $txt['tp-confirm'] . '\')"> <strong>' . $txt['tp-deleteallshouts'] . '</strong>
					<input type="submit" class="button" name="' . $txt['tp-send'] . '" value="' . $txt['tp-send'] . '">
					<p class="clearthefloat"></p>
				</div>
			</div>
		</div>
	</form>';
}

// Shoutbox Block template
function template_tpshout_shoutblock($block_id = 0)
{
	global $context, $scripturl, $txt, $settings, $modSettings, $user_info;

	$row = TPBlock::getInstance()->getBlock($block_id);
	$set = json_decode($row['settings'], true);
	$shoutbox_id = $set['shoutbox_id'];
	$shoutbox_layout = $set['shoutbox_layout'];
	$shoutbox_height = $set['shoutbox_height'];
	$shoutbox_avatar = $set['shoutbox_avatar'];
	$shoutbox_barposition = $set['shoutbox_barposition'];
	$shoutbox_direction = $set['shoutbox_direction'];

	if (!isset($context['TPortal']['shoutbox'])) {
		$context['TPortal']['shoutbox'] = '';
	}

	$context['tp_shoutbox_form'] = 'tp_shoutbox';
	$context['tp_shout_post_box_name'] = 'tp_shout_' . $block_id;

	if (!empty($context['TPortal']['shoutbox_stitle'])) {
		echo '
		' . parse_bbc($context['TPortal']['shoutbox_stitle'], true) . '<hr>';
	}
	if ($shoutbox_barposition == 1) {
		if ($context['TPortal']['shoutbox_usescroll'] > '0') {
			echo '
				<marquee id="tp_marquee_' . $block_id . '" behavior="scroll" direction="down" scrollamount="' . ($context['TPortal']['shoutbox_scrollduration'] / 2) . '" height="' . $context['TPortal']['shoutbox_height'] . '">
					<div id="tp_shoutframe_' . $block_id . '" class="tp_shoutframe">' . $context['TPortal']['shoutbox'] . '</div>
				</marquee>
				<hr>';
		}
		else {
			echo '
				<div id="shoutboxContainer_' . $block_id . '">
					<div style="width: 100%; height: ' . $context['TPortal']['shoutbox_height'] . 'px; overflow: auto;">
						<div id="tp_shoutframe_' . $block_id . '" class="tp_shoutframe">' . $context['TPortal']['shoutbox'] . '</div>
					</div>
				</div><!--shoutboxContainer--><hr>';
		}
	}
	if (!$context['user']['is_guest'] && allowedTo('tp_can_shout')) {
		if (in_array($context['TPortal']['shoutbox_layout'], ['2', '3'], true)) {
			echo '
				<form  accept-charset="' . $context['character_set'] . '" class="smalltext" name="' . $context['tp_shoutbox_form'] . '_' . $block_id . '"  id="' . $context['tp_shoutbox_form'] . '_' . $block_id . '" action="' . $scripturl . '?action=tpshout;shout=save;block=' . $block_id . '" method="post" >
				<div>
					<input type="text" id="' . $context['tp_shout_post_box_name'] . '" class="tp_shoutbox_input' . $context['TPortal']['shoutbox_layout'] . '" name="' . $context['tp_shout_post_box_name'] . '" maxlength="' . $context['TPortal']['shoutbox_maxlength'] . '"  onselect="tpShoutFocusTextArea(\'' . $context['tp_shout_post_box_name'] . '\');" onclick="tpShoutFocusTextArea(\'' . $context['tp_shout_post_box_name'] . '\');" onkeyup="tpShoutFocusTextArea(\'' . $context['tp_shout_post_box_name'] . '\');" onchange="tpShoutFocusTextArea(\'' . $context['tp_shout_post_box_name'] . '\');" tabindex="', $context['tabindex']++, '"></input>
					<input onclick="TPupdateShouts(\'save\', ' . $block_id . '); return false;" type="submit" name="shout_send" value="&nbsp;' . $txt['shout!'] . '&nbsp;" tabindex="', $context['tabindex']++, '" class="button_submit" />';
			if (allowedTo('tp_can_admin_shout')) {
				echo '
						<a href="' , $scripturl , '?action=tpshout;shout=admin;settings" title="' . $txt['tp-shoutboxsettings'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPsettings.png" alt="" /></a>';
			}
			echo '
					<a href="' , $scripturl , '?action=tpshout;shout=show50;b=' . $block_id . ';l=' . $shoutbox_layout . '" title="' . $txt['tp-shout-history'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPhistory.png" alt="" /></a>
					<a id="tp_shout_refresh_' . $block_id . '" onclick="TPupdateShouts(\'fetch\', ' . $block_id . ' ); return false;" href="' , $scripturl , '?action=tpshout;shout=refresh" title="' . $txt['tp-shout-refresh'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPrefresh.png" alt="" /></a>
					<p class="clearthefloat"></p>
				</div>';

			if (!empty($context['TPortal']['show_shoutbox_smile']) && $user_info['smiley_set'] != 'none') {
				echo '
					<div class="tp_shout_smileybox">';
				shout_smiley_code($shoutbox_id);
				print_shout_smileys($shoutbox_id);
				echo '
					</div>';
			}

			if (!empty($context['TPortal']['show_shoutbox_icons'])) {
				echo '
					<div class="tp_shout_bbcbox">';
				shout_bbc_code($shoutbox_id);
				echo '
					</div>';
			}
			echo '
				<br>
				<input type="hidden" id="tp-shout-name_' . $block_id . '" name="tp-shout-name_' . $block_id . '" value="' . $context['user']['name'] . '" />
				<input type="hidden" id="tp_shout_direction_' . $block_id . '" name="tp_shout_direction_' . $block_id . '" value="' . $shoutbox_direction . '" />
				<input type="hidden" name="sc" value="', $context['session_id'], '" />
			</form>';
		}
		else {
			echo '
				<form  accept-charset="' . $context['character_set'] . '" class="smalltext" style="text-align: center; width: 99%;" name="' . $context['tp_shoutbox_form'] . '_' . $block_id . '"  id="' . $context['tp_shoutbox_form'] . '_' . $block_id . '" action="' . $scripturl . '?action=tpshout;shout=save" method="post" >
				<textarea class="tp_shoutbox_editor' . $context['TPortal']['shoutbox_layout'] . '" maxlength="' . $context['TPortal']['shoutbox_maxlength'] . '" name="' . $context['tp_shout_post_box_name'] . '" id="' . $context['tp_shout_post_box_name'] . '" onselect="tpShoutFocusTextArea(\'' . $context['tp_shout_post_box_name'] . '\');" onclick="tpShoutFocusTextArea(\'' . $context['tp_shout_post_box_name'] . '\');" onkeyup="tpShoutFocusTextArea(\'' . $context['tp_shout_post_box_name'] . '\');" onchange="tpShoutFocusTextArea(\'' . $context['tp_shout_post_box_name'] . '\');" tabindex="', $context['tabindex']++, '"></textarea><br>';

			if (!empty($context['TPortal']['show_shoutbox_smile']) && $user_info['smiley_set'] != 'none') {
				echo '
					<div class="tp_shout_smileybox">';
				shout_smiley_code($shoutbox_id);
				print_shout_smileys($shoutbox_id);
				echo '
					</div>';
			}
			if (!empty($context['TPortal']['show_shoutbox_icons'])) {
				echo '
					<div class="tp_shout_bbcbox">';
				shout_bbc_code($shoutbox_id);
				echo '
					</div>';
			}

			echo '
				<p class="clearthefloat"></p>
				<hr>
				<div style="overflow: hidden;">
				   <a id="tp_shout_refresh_' . $block_id . '" onclick="TPupdateShouts(\'fetch\', ' . $block_id . '); return false;" href="' , $scripturl , '?action=tpshout;shout=refresh" title="' . $txt['tp-shout-refresh'] . '"><img class="floatleft" src="' . $settings['tp_images_url'] . '/TPrefresh.png" alt="" /></a>
					<input onclick="TPupdateShouts(\'save\', ' . $block_id . '); return false;" type="submit" name="shout_send" value="&nbsp;' . $txt['shout!'] . '&nbsp;" tabindex="', $context['tabindex']++, '" class="button_submit" />';
			if (allowedTo('tp_can_admin_shout')) {
				echo '
						<a href="' , $scripturl , '?action=tpshout;shout=admin;settings" title="' . $txt['tp-shoutboxsettings'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPsettings.png" alt="" /></a>';
			}
			echo '
					<a href="' , $scripturl , '?action=tpshout;shout=show50;b=' . $block_id . ';l=' . $shoutbox_layout . '" title="' . $txt['tp-shout-history'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPhistory.png" alt="" /></a>
				</div>
				<input type="hidden" id="tp-shout-name_' . $block_id . '" name="tp-shout-name_' . $block_id . '" value="' . $context['user']['name'] . '" />
				<input type="hidden" id="tp_shout_direction_' . $block_id . '" name="tp_shout_direction_' . $block_id . '" value="' . $shoutbox_direction . '" />
				<input type="hidden" name="sc" value="', $context['session_id'], '" />
			</form>';
		}
	}
	if ($shoutbox_barposition == 0) {
		if ($context['TPortal']['shoutbox_usescroll'] > '0') {
			echo '
				<hr><marquee id="tp_marquee_' . $block_id . '" behavior="scroll" direction="down" scrollamount="' . ($context['TPortal']['shoutbox_scrollduration'] / 2) . '" height="' . $context['TPortal']['shoutbox_height'] . '">
					<div id="tp_shoutframe_' . $block_id . '" class="tp_shoutframe">' . $context['TPortal']['shoutbox'] . '</div>
				</marquee>';
		}
		else {
			echo '
				<hr><div id="shoutboxContainer_' . $block_id . '">
					<div style="width: 100%; height: ' . $context['TPortal']['shoutbox_height'] . 'px; overflow: auto;">
						<div id="tp_shoutframe_' . $block_id . '" class="tp_shoutframe">' . $context['TPortal']['shoutbox'] . '</div>
					</div>
				</div><!--shoutboxContainer-->';
		}
	}
}

// Shoutbox single shout template
function template_singleshout($row, $block_id)
{
	global $scripturl, $context, $settings, $txt;

	$data = TPBlock::getInstance()->getBlock($block_id);
	$set = json_decode($data['settings'], true);
	$shoutbox_id = $set['shoutbox_id'];
	$shoutbox_layout = $set['shoutbox_layout'];
	$shoutbox_height = $set['shoutbox_height'];
	$shoutbox_avatar = $set['shoutbox_avatar'];
	$shoutbox_barposition = $set['shoutbox_barposition'];
	$shoutbox_direction = $set['shoutbox_direction'];

	if (is_null($shoutbox_layout) && isset($context['TPortal']['shoutbox_layout'])) {
		$shoutbox_layout = $context['TPortal']['shoutbox_layout'];
	}

	$layoutOptions = [
		'0' => '
	<div style="padding-bottom: 5px;">
		<div class="tp_shoutbody_layout0">
			<div class="tp_shoutavatar">
				' . ($shoutbox_avatar == '1' ? '<div class="tp_shoutavatar2"><a href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['avatar'] . '</a></div>' : '') . '
				<h4><a ' . (!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' . $row['online_color'] . ';"' : '') . ' href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['real_name'] . '</a></h4>
				' . (allowedTo('tp_can_admin_shout') ? '
				<div class="tp_shoutbox_edit">
					<a href="' . $scripturl . '?action=tpshout;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['tp_images_url'] . '/TPmodify_shout.png" title="' . $txt['tp-edit'] . '" alt="' . $txt['tp-edit'] . '" /></a>
					<a onclick="TPupdateShouts(\'del\', ' . $block_id . ', ' . $row['id'] . '); return false;" class="shout_delete" title="' . $txt['tp-delete'] . '" href="' . $scripturl . '?action=tpshout;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['tp_images_url'] . '/TPdelete_shout.png" alt="' . $txt['tp-delete'] . '" /></a>
				</div>' : '') . '
				<span class="smalltext clear tp_shoutbox_time" style="padding-top:.5em' . (!empty($context['TPortal']['shoutbox_timecolor']) ? '; color:' . $context['TPortal']['shoutbox_timecolor'] . '">' : '">') . '</span>' . tptimeformat($row['time'], true, $context['TPortal']['shoutbox_timeformat2']) . '</span>
			</div>
			<div class="tp_bubble" ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' . $context['TPortal']['shoutbox_textcolor'] . '">' : '>') . '' . $row['content'] . '</div>
		</div>
	</div>',
		'1' => '
		<div class="tp_shoutbody_layout1">
			' . ($shoutbox_avatar == '1' ? '<div class="tp_shoutavatar2"><a href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['avatar'] . '</a></div>' : '') . '
			<a ' . (!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' . $row['online_color'] . '"' : '"') . ' href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['real_name'] . '</a>:
			' . (allowedTo('tp_can_admin_shout') ? '
			<div class="tp_shoutbox_edit">
				<a href="' . $scripturl . '?action=tpshout;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['tp_images_url'] . '/TPmodify_shout.png" title="' . $txt['tp-edit'] . '" alt="' . $txt['tp-edit'] . '" /></a>
				<a onclick="TPupdateShouts(\'del\', ' . $block_id . ', ' . $row['id'] . '); return false;" class="shout_delete" title="' . $txt['tp-delete'] . '" href="' . $scripturl . '?action=tpshout;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['tp_images_url'] . '/TPdelete_shout.png" alt="' . $txt['tp-delete'] . '" /></a>
			</div>' : '') . '
			<div class="smalltext" style="padding-bottom: .5em;">
				<span class="smalltext tp_shoutbox_time" ' . (!empty($context['TPortal']['shoutbox_timecolor']) ? '; style="color:' . $context['TPortal']['shoutbox_timecolor'] . '">' : '>') . '' . tptimeformat($row['time'], true, $context['TPortal']['shoutbox_timeformat2']) . '</span>
			</div>
			<span class="tp_shoutbox_text" ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' . $context['TPortal']['shoutbox_textcolor'] . '">' : '>') . '' . $row['content'] . '</span>
		</div>',
		'2' => '
		<div class="tp_shoutbody_layout2" ' . (($row['counter'] % 2) ? (!empty($context['TPortal']['shoutbox_linecolor2']) ? 'style="background:' . ($context['TPortal']['shoutbox_linecolor2']) . '"' : '') : (!empty($context['TPortal']['shoutbox_linecolor1']) ? 'style="background:' . ($context['TPortal']['shoutbox_linecolor1']) . '"' : '')) . '>
			' . (allowedTo('tp_can_admin_shout') ? '
			<div class="tp_shoutbox_edit">
				<a href="' . $scripturl . '?action=tpshout;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['tp_images_url'] . '/TPmodify_shout.png" title="' . $txt['tp-edit'] . '" alt="' . $txt['tp-edit'] . '" /></a>
				<a onclick="TPupdateShouts(\'del\', ' . $block_id . ', ' . $row['id'] . '); return false;" class="shout_delete" title="' . $txt['tp-delete'] . '" href="' . $scripturl . '?action=tpshout;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['tp_images_url'] . '/TPdelete_shout.png" alt="' . $txt['tp-delete'] . '" /></a>
			</div>' : '') . '
			<div class="tp_shoutbox_time">
				<span class="smalltext tp_shoutbox_time" ' . (!empty($context['TPortal']['shoutbox_timecolor']) ? '; style="color:' . $context['TPortal']['shoutbox_timecolor'] . '">' : '>') . '' . tptimeformat($row['time'], true, $context['TPortal']['shoutbox_timeformat2']) . '</span>
			</div>
			' . ($shoutbox_avatar == '1' ? '<div class="tp_shoutavatar2"><a href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['avatar'] . '</a></div>' : '') . '
			<a ' . (!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' . $row['online_color'] . ';"' : '') . '
			href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['real_name'] . '</a>: <span class="tp_shoutbox_text" ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' . $context['TPortal']['shoutbox_textcolor'] . '">' : '>') . '' . $row['content'] . '</span>
			<p class="clearthefloat"></p>
		</div>',
		'3' => '
		<div class="tp_shoutbody_layout3" ' . (($row['counter'] % 2) ? (!empty($context['TPortal']['shoutbox_linecolor2']) ? 'style="background:' . ($context['TPortal']['shoutbox_linecolor2']) . '"' : '') : (!empty($context['TPortal']['shoutbox_linecolor1']) ? 'style="background:' . ($context['TPortal']['shoutbox_linecolor1']) . '"' : '')) . '>
			' . (allowedTo('tp_can_admin_shout') ? '
			<div class="tp_shoutbox_edit">
				<a href="' . $scripturl . '?action=tpshout;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['tp_images_url'] . '/TPmodify_shout.png" title="' . $txt['tp-edit'] . '" alt="' . $txt['tp-edit'] . '" /></a>
				<a onclick="TPupdateShouts(\'del\', ' . $block_id . ', ' . $row['id'] . '); return false;" class="shout_delete" title="' . $txt['tp-delete'] . '" href="' . $scripturl . '?action=tpshout;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['tp_images_url'] . '/TPdelete_shout.png" alt="' . $txt['tp-delete'] . '" /></a>
			</div>' : '') . '
			' . ($shoutbox_avatar == '1' ? '<div class="tp_shoutavatar2"><a href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['avatar'] . '</a></div>' : '') . '
			<a ' . (!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' . $row['online_color'] . ';"' : '') . '
			href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['real_name'] . '</a>: <span class="tp_shoutbox_text" ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' . $context['TPortal']['shoutbox_textcolor'] . '">' : '>') . '' . $row['content'] . '</span>
			<span class="smalltext tp_shoutbox_time" ' . (!empty($context['TPortal']['shoutbox_timecolor']) ? '; style="color:' . $context['TPortal']['shoutbox_timecolor'] . '">' : '>') . '' . tptimeformat($row['time'], true, $context['TPortal']['shoutbox_timeformat2']) . '</span>
			<p class="clearthefloat"></p>
		</div>',
	];
	if (!empty($layoutOptions[$shoutbox_layout])) {
		return $layoutOptions[$shoutbox_layout];
	}
}

function template_tpshout_ajax($block_id = 0)
{
	global $context;

	$block_id = !empty($context['TPortalShoutboxId']) ? (int)$context['TPortalShoutboxId'] : $block_id;
	echo '
	<div class="' . (!empty($context['TPortal']['shoutError']) ? 'errorbox' : 'tp_bigshout') . '" id="' . (!empty($context['TPortal']['shoutError']) ? 'shoutError_' . $block_id : 'bigshout_' . $block_id) . '">' . $context['TPortal']['rendershouts'] . '</div>';
}

// View shouts Profile page
function template_tpshout_profile()
{
	global $settings, $txt, $context;

	echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['shoutboxprofile'] . '</h3></div>
		<p class="information">' . $txt['shoutboxprofile2'] . '</p>
		<div id="tpshout_profile">
			<div class="windowbg">
				' . $txt['tp-prof_allshouts'] . ' <b>', $context['TPortal']['all_shouts'] ,'</b>
			</div><br>';

	if (isset($context['TPortal']['profile_shouts']) && sizeof($context['TPortal']['profile_shouts']) > 0) {
		echo '
			<table class="table_grid">
				<thead>
					<tr class="title_bar">
					<th scope="col">
						<div class="tp_flexrow">
							<div class="tp_date tpleft">' . $txt['date'] . '</div>
							<div class="tp_name tpleft">',$txt['tp-shout'],'</div>
						</div>
					</th>
					</tr>
				</thead>
				<tbody>';

		foreach ($context['TPortal']['profile_shouts'] as $art) {
			echo '
					<tr class="windowbg">
					<td>
						<div class="tp_flexrow">
							<div class="tp_date">',$art['created'],'</div>
							<div class="tp_name">',$art['shout'],'</div>
						</div>
					</td>
					</tr>';
		}
		echo '
				</tbody>
			</table>
			<div class="padding-div">' . $context['TPortal']['pageindex'] . '</div>
		</div>';
	}
}
