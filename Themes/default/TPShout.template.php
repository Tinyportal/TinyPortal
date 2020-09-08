<?php
/**
 * @package TinyPortal
 * @version 2.0.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2020 - The TinyPortal Team
 *
 */

// ** Sections **
// Shoutbox administration Page
// Shoutbox settings Page
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

	if(isset($context['TPortal']['querystring']))
		$tp_where = $context['TPortal']['querystring'];
	else
		$tp_where='';

	$context['tp_shoutbox_form'] = 'tp_shoutbox';
	$context['tp_shout_post_box_name'] = 'tp_shout';

	echo '
	<div class="tborder">
		<div class="title_bar"><h3 class="titlebg">' , $txt['tp-shoutbox'] , '</h3></div>
		<div class="windowbg tp-pad">';

	echo '
			<div id="bigshout" style="width: 99%; height: 100%;">', $shouts, '</div>';

	echo '
			<form  accept-charset="', $context['character_set'], '" class="smalltext" style="padding: 10px; margin: 0; text-align: center;" name="'. $context['tp_shoutbox_form']. '"  id="'. $context['tp_shoutbox_form']. '" action="'.$scripturl.'?action=tpshout;shout=save" method="post" >
				<input type="hidden" name="tp-shout-name" value="'.$context['user']['name'].'" />
				<input name="tp-shout-url" type="hidden" value="'. $smcFunc['htmlspecialchars']($tp_where).'" />
				<input type="hidden" name="sc" value="', $context['session_id'], '" />
			</form>
		</div>
	</div>';

}

// Shoutbox administration Page
function template_tpshout_admin()
{
	global $context, $scripturl, $txt;

	 echo '
	<form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpshout;shout=admin"  method="post">
		<input name="TPadmin_blocks" type="hidden" value="set" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="singlemenuedit">
			<div class="cat_bar"><h3 class="catbg">'.$txt['tp-shoutboxadmin'].'</h3></div>
			<div id="tpshout_admin" class="admintable admin-area">
				<div class="windowbg noup">
					<div class="formtable padding-div">
						<div class="addborder">
							<div style="width:47%;" class="float-items"><strong>'.$txt['tp-shoutboxitems'].'</strong></div>
							<div class="smalltext float-items" style="width:47%;text-align:right">'.$context['TPortal']['shoutbox_pageindex'].'</div>
							<p class="clearthefloat"></p>
						</div>';

	foreach($context['TPortal']['admin_shoutbox_items'] as $admin_shouts) {
				echo '
					<div style="border-bottom:1px solid #ccc;">
						<div class="fullwidth-on-res-layout float-items ' ,  !empty($admin_shouts['sticky']) ? 'windowbg2' : '' , '" style="width:30%;">
							'.$admin_shouts['poster'].' ['.$admin_shouts['ip'].']<br>'.$admin_shouts['time'].'<br>
							'. $admin_shouts['sort_member'].' <br> '.$admin_shouts['sort_ip'].'<br>'.$admin_shouts['single'].'
						</div>
						<div class="float-items ' ,  !empty($admin_shouts['sticky']) ? 'windowbg2' : '' , '">
							<textarea style="vertical-align: middle; width: 99%;" rows="5" cols="40" wrap="auto" name="tp_shoutbox_item'.$admin_shouts['id'].'">' .html_entity_decode($admin_shouts['body']).'</textarea>
						</div>
						<div class="float-items ' ,  !empty($admin_shouts['sticky']) ? 'windowbg2' : '' , '">
							<input name="tp_shoutbox_hidden'.$admin_shouts['id'].'" type="hidden" value="1">
							<div style="text-align: right;"><strong><input style="vertical-align: middle;" name="tp_shoutbox_remove'.$admin_shouts['id'].'" type="checkbox" value="ON"> '.$txt['tp-remove'].'</strong></div>
					   </div>
					   <p class="clearthefloat"></p>
					</div>';
	}
		echo '
					<div>
						<div class="normaltext float-items" style="width:47%;">
							<input name="tp_shoutsdelall" type="checkbox" value="ON" onclick="javascript:return confirm(\''.$txt['tp-confirm'].'\')"> <strong>'.$txt['tp-deleteallshouts'].'</strong>&nbsp;&nbsp;
						</div>
						<div class="smalltext float-items" style="width:47%;text-align: right">'.$context['TPortal']['shoutbox_pageindex'].'</div>
					   <p class="clearthefloat"></p>
					</div>
				</div>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
		<p class="clearthefloat"></p>
	</form>';
}

// Shoutbox settings Page
function template_tpshout_admin_settings()
{
	global $context, $scripturl, $txt, $settings;

	echo '
	<form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpshout;shout=admin"  method="post" style="margin: 0px;">
		<input name="TPadmin_blocks" type="hidden" value="set" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="singlemenuedit">
			<div class="cat_bar"><h3 class="catbg">'.$txt['tp-shoutboxsettings'].'</h3></div>
			<div id="tpshout_admin_settings" class="admintable admin-area">
				<div class="windowbg noup">
					<div class="formtable padding-div">
					<dl class="settings">
						<dt>
							<label for="tp_shoutbox_stitle">'.$txt['tp-shoutboxtitle'].'</label>
						</dt>
						<dd>
							<textarea style="width: 90%; height: 50px;" name="tp_shoutbox_stitle" id="tp_shoutbox_stitle">' , !empty($context['TPortal']['shoutbox_stitle']) ? $context['TPortal']['shoutbox_stitle'] : '', '</textarea><br>
						</dd>
						<dt>
							'.$txt['tp-shoutbox_showsmile'].'
						</dt>
						<dd>
							<input name="tp_shoutbox_smile" type="radio" value="1" ' , $context['TPortal']['show_shoutbox_smile']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shoutbox_smile" type="radio" value="0" ' , $context['TPortal']['show_shoutbox_smile']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br>
						</dd>
						<dt>'.$txt['tp-shoutbox_showicons'].'
						</dt>
						<dd>
							<input name="tp_shoutbox_icons" type="radio" value="1" ' , $context['TPortal']['show_shoutbox_icons']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shoutbox_icons" type="radio" value="0" ' , $context['TPortal']['show_shoutbox_icons']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br>
						</dd>
						<dt>
							'.$txt['tp-shout-allow-links'].'
						</dt>
						<dd>
							<input name="tp_shout_allow_links" type="radio" value="1" ' , $context['TPortal']['shout_allow_links'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shout_allow_links" type="radio" value="0" ' , $context['TPortal']['shout_allow_links'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br>
						</dd>
						<dt>
							'.$txt['tp-shoutboxusescroll'].'
						</dt>
						<dd>
							<input name="tp_shoutbox_usescroll" type="radio" value="1" ' , $context['TPortal']['shoutbox_usescroll'] > 0 ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shoutbox_usescroll" type="radio" value="0" ' , $context['TPortal']['shoutbox_usescroll'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br>
						</dd>
						<dt>
							<label for="tp_shoutbox_scrollduration">'.$txt['tp-shoutboxduration'].'</label>
						</dt>
						<dd>
							<input type="text" pattern="[0-9]+" size="6" name="tp_shoutbox_scrollduration" id="tp_shoutbox_scrollduration" value="'.$context['TPortal']['shoutbox_scrollduration'].'" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_refresh">'.$txt['tp-shout-autorefresh'].'</label>
						</dt>
						<dd>
							<input size="6" name="tp_shoutbox_refresh" id="tp_shoutbox_refresh" type="text" pattern="[0-9]+" value="' ,$context['TPortal']['shoutbox_refresh'], '" /><br>
						</dd>
						<dt>
							'.$txt['shout_submit_returnkey'].'
						</dt>
						<dd>
							<input name="tp_shout_submit_returnkey" id="tp_shout_submit_returnkey1" type="radio" value="1" ' , $context['TPortal']['shout_submit_returnkey'] == '1' ? ' checked="checked"' : '' , ' /> <label for="tp_shout_submit_returnkey1">'.$txt['tp-yes-enter'].'</label><br>
							<input name="tp_shout_submit_returnkey" id="tp_shout_submit_returnkey2" type="radio" value="2" ' , $context['TPortal']['shout_submit_returnkey'] == '2' ? ' checked="checked"' : '' , ' /> <label for="tp_shout_submit_returnkey2">'.$txt['tp-yes-ctrl'].'</label><br>
							<input name="tp_shout_submit_returnkey" id="tp_shout_submit_returnkey3" type="radio" value="0" ' , $context['TPortal']['shout_submit_returnkey'] == '0' ? ' checked="checked"' : '' , ' /> <label for="tp_shout_submit_returnkey3">'.$txt['tp-yes-shout'].'</label><br>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>
						'.$txt['shoutbox_layout'].'<br>
						</dt>
						<dd>
						<div class="float-items"><div><input name="tp_shoutbox_layout" id="shout_layout1" type="radio" value="0" ' , $context['TPortal']['shoutbox_layout'] == '0' ? ' checked="checked"' : '' , ' /></div><div><label for="shout_layout1"><img src="' . $settings['tp_images_url'] . '/shout_layout1.png" alt="Layout 1" style="text-align: right"/></label></div></div>
						<div class="float-items"><div><input name="tp_shoutbox_layout" id="shout_layout2" type="radio" value="1" ' , $context['TPortal']['shoutbox_layout'] == '1' ? ' checked="checked"' : '' , ' /></div><div><label for="shout_layout2"><img src="' . $settings['tp_images_url'] . '/shout_layout2.png" alt="Layout 2" /></label></div></div>
						<p class="clearthefloat"></p>
						<div class="float-items"><div><input name="tp_shoutbox_layout" id="shout_layout3" type="radio" value="2" ' , $context['TPortal']['shoutbox_layout'] == '2' ? ' checked="checked"' : '' , ' /></div><div><label for="shout_layout3"><img src="' . $settings['tp_images_url'] . '/shout_layout3.png" alt="Layout 3" /></label></div></div>
						<div class="float-items"><div><input name="tp_shoutbox_layout" id="shout_layout4" type="radio" value="3" ' , $context['TPortal']['shoutbox_layout'] == '3' ? ' checked="checked"' : '' , ' /></div><div><label for="shout_layout4"><img src="' . $settings['tp_images_url'] . '/shout_layout4.png" alt="Layout 4" /></label></div></div>
						<p class="clearthefloat"></p>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>
							<label for="tp_shoutbox_height">'.$txt['tp-shoutboxheight'].'</label>
						</dt>
						<dd>
							<input size="6" name="tp_shoutbox_height" id="tp_shoutbox_height" type="text" pattern="[0-9]+" value="' ,$context['TPortal']['shoutbox_height'], '" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_limit">'.$txt['tp-shoutboxlimit'].'</label>
						</dt>
						<dd>
							<input size="6" name="tp_shoutbox_limit" id="tp_shoutbox_limit" type="text" pattern="[0-9]+" value="' ,$context['TPortal']['shoutbox_limit'], '" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_maxlength">'.$txt['tp-shoutboxmaxlength'].'</label>
						</dt>
						<dd>
							<input size="6" name="tp_shoutbox_maxlength" id="tp_shoutbox_maxlength" type="text" pattern="[0-9]+" value="' ,$context['TPortal']['shoutbox_maxlength'], '" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_timeformat">'.$txt['tp-shoutboxtimeformat'].'</label>
						</dt>
						<dd>
							<input size="15" name="tp_shoutbox_timeformat" id="tp_shoutbox_timeformat" type="text" value="' ,$context['TPortal']['shoutbox_timeformat'], '" /><br>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt><h4><a href="', $scripturl, '?action=helpadmin;help=',$txt['tp-shoutboxcolorsdesc'],'" onclick=' . ((!TP_SMF21) ? '"return reqWin(this.href);"' : '"return reqOverlayDiv(this.href);"') . '><span class="tptooltip" title="', $txt['help'], '"></span></a>'.$txt['tp-shoutboxcolors'].'</h4>
						</dt>
						<dt>
							'.$txt['tp-shoutbox_use_groupcolor'].'
							' , (!empty($context['TPortal']['use_groupcolor'])) ? '<div class="smalltext" style="color:red;">'.$txt['tp-shoutbox_use_groupcolordesc'].'</div>' : '' , '
						</dt>
						<dd>
							<input name="tp_shoutbox_use_groupcolor" type="radio" value="1" ' , $context['TPortal']['shoutbox_use_groupcolor']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shoutbox_use_groupcolor" type="radio" value="0" ' , $context['TPortal']['shoutbox_use_groupcolor']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br>
						</dd>
						<dt>
							<label for="tp_shoutbox_textcolor">'.$txt['tp-shoutboxtextcolor'].'</label>
						</dt>
						<dd>
							<input size="10" name="tp_shoutbox_textcolor" id="tp_shoutbox_textcolor" type="text" value="' ,$context['TPortal']['shoutbox_textcolor'], '" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_timecolor">'.$txt['tp-shoutboxtimecolor'].'</label>
						</dt>
						<dd>
							<input size="10" name="tp_shoutbox_timecolor" id="tp_shoutbox_timecolor" type="text" value="' ,$context['TPortal']['shoutbox_timecolor'], '" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_linecolor1">'.$txt['tp-shoutboxlinecolor1'].'</label>
						</dt>
						<dd>
							<input size="10" name="tp_shoutbox_linecolor1" id="tp_shoutbox_linecolor1" type="text" value="' ,$context['TPortal']['shoutbox_linecolor1'], '" /><br>
						</dd>
						<dt>
							<label for="tp_shoutbox_linecolor2">'.$txt['tp-shoutboxlinecolor2'].'</label>
						</dt>
						<dd>
							<input size="10" name="tp_shoutbox_linecolor2" id="tp_shoutbox_linecolor2" type="text" value="' ,$context['TPortal']['shoutbox_linecolor2'], '" /><br>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>
							'.$txt['tp-show_profile_shouts'].'
						</dt>
						<dd>
							<input name="tp_show_profile_shouts" type="radio" value="1" ' , $context['TPortal']['profile_shouts_hide'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_show_profile_shouts" type="radio" value="0" ' , $context['TPortal']['profile_shouts_hide'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br>
						</dd>
					</dl>
				</div>
			   <div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
		<p class="clearthefloat"></p>
	</form>';
}

// Shoutbox Block template
function template_tpshout_shoutblock()
{
	global $context, $scripturl, $txt, $settings, $modSettings, $user_info;

	if(!isset($context['TPortal']['shoutbox'])) {
		$context['TPortal']['shoutbox'] = '';
    }

	$context['tp_shoutbox_form'] = 'tp_shoutbox';
	$context['tp_shout_post_box_name'] = 'tp_shout';

	if(!empty($context['TPortal']['shoutbox_stitle'])) {
		echo '
		<p style="margin-top: 0;">' . parse_bbc($context['TPortal']['shoutbox_stitle'],true) . '</p><hr>';
    }

	if($context['TPortal']['shoutbox_usescroll'] > '0') {
		echo '
		<marquee id="tp_marquee" behavior="scroll" direction="down" scrollamount="'. $context['TPortal']['shoutbox_scrollduration'] . '" height="'. $context['TPortal']['shoutbox_height'] . '">
			<div class="tp_shoutframe">'.$context['TPortal']['shoutbox'].'</div>
		</marquee>';
    }
	else {
		echo '
			<div id="shoutboxContainer">
				<div class="middletext" style="width: 100%; height: '.$context['TPortal']['shoutbox_height'].'px; overflow: auto;">
					<div class="tp_shoutframe">'. $context['TPortal']['shoutbox']. '</div>
				</div>
			<!--shoutboxContainer-->';
    }
	if(!$context['user']['is_guest'] && allowedTo('tp_can_shout')) {
	    if ( in_array($context['TPortal']['shoutbox_layout'], array('2','3'), true ) ) {
            echo '
                <form  accept-charset="'. $context['character_set']. '" class="smalltext" name="'. $context['tp_shoutbox_form']. '"  id="'. $context['tp_shoutbox_form']. '" action="'.$scripturl.'?action=tpshout;shout=save" method="post" ><hr>
                <div style="margin-bottom: 5px;">
                    <input type="text" maxlength="' .$context['TPortal']['shoutbox_maxlength']. '" class="shoutbox_input'. $context['TPortal']['shoutbox_layout']. '" name="'. $context['tp_shout_post_box_name']. '" id="'. $context['tp_shout_post_box_name']. '" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);" tabindex="', $context['tabindex']++, '"></input>
                    <input onclick="TPupdateShouts(\'save\'); return false;" type="submit" name="shout_send" value="&nbsp;'.$txt['shout!'].'&nbsp;" tabindex="', $context['tabindex']++, '" class="button_submit" />
                    <a href="' , $scripturl , '?action=tpshout;shout=show50" title="'. $txt['tp-shout-history'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPhistory.png" alt="" /></a>
                    <a id="tp_shout_refresh" onclick="TPupdateShouts(\'fetch\'); return false;" href="' , $scripturl , '?action=tpshout;shout=refresh" title="'. $txt['tp-shout-refresh'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPrefresh.png" alt="" /></a>
                    <p class="clearthefloat"></p>
                </div>';

			if(!empty($context['TPortal']['show_shoutbox_smile']) && $user_info['smiley_set'] != 'none') {
			    echo '
			        <div style="display: inline-block;min-width:150px;vertical-align: top;">';
				    shout_smiley_code();
				    print_shout_smileys();
			    echo '
			        </div>';
			}

			if(!empty($context['TPortal']['show_shoutbox_icons'])) {
			    echo '
			        <div style="display: inline-block;min-width:150px;vertical-align: top;">';
				    shout_bcc_code();
			    echo '
			        </div>';
			}
			echo '
			    <br>
			    <input type="hidden" id="tp-shout-name" name="tp-shout-name" value="'.$context['user']['name'].'" />
			    <input type="hidden" name="sc" value="', $context['session_id'], '" />
		    </form>';
		}
	    else {
            echo '
                <form  accept-charset="'. $context['character_set']. '" class="smalltext" style="text-align: center; width: 99%;" name="'. $context['tp_shoutbox_form']. '"  id="'. $context['tp_shoutbox_form']. '" action="'.$scripturl.'?action=tpshout;shout=save" method="post" ><hr>
                <textarea class="shoutbox_editor'. $context['TPortal']['shoutbox_layout']. '" maxlength="' .$context['TPortal']['shoutbox_maxlength']. '" name="'. $context['tp_shout_post_box_name']. '" id="'. $context['tp_shout_post_box_name']. '" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);" tabindex="', $context['tabindex']++, '"></textarea><br>';

                if(!empty($context['TPortal']['show_shoutbox_smile']) && $user_info['smiley_set'] != 'none') {
                    shout_smiley_code();
                    print_shout_smileys();
                }
                if(!empty($context['TPortal']['show_shoutbox_icons'])) {
                    shout_bcc_code();
                }

                echo '
                <div id="shout_errors"></div>
                <p class="clearthefloat"></p>
                <hr>
                <div style="overflow: hidden;">
                    <a href="' , $scripturl , '?action=tpshout;shout=show50" title="'. $txt['tp-shout-history'] . '"><img class="floatleft" src="' . $settings['tp_images_url'] . '/TPhistory.png" alt="" /></a>
                    <input onclick="TPupdateShouts(\'save\'); return false;" type="submit" name="shout_send" value="&nbsp;'.$txt['shout!'].'&nbsp;" tabindex="', $context['tabindex']++, '" class="button_submit" />
                    <a id="tp_shout_refresh" onclick="TPupdateShouts(\'fetch\'); return false;" href="' , $scripturl , '?action=tpshout;shout=refresh" title="'. $txt['tp-shout-refresh'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPrefresh.png" alt="" /></a>
                </div>
                <input type="hidden" id="tp-shout-name" name="tp-shout-name" value="'.$context['user']['name'].'" />
                <input type="hidden" name="sc" value="', $context['session_id'], '" />
            </form>';
		}	
	}
}

// Shoutbox single shout template
function template_singleshout($row)
{
	global $scripturl, $context, $settings, $txt;

	$layoutOptions = array(
	 '0' => '
	<div style="padding-bottom: 5px;">
		<div class="tp_shoutcontainer showhover">
			<div class="tp_shoutavatar">
				<div class="avy2"><a href="' . $scripturl. '?action=profile;u=' . $row['member_id'] . '">' . $row['avatar'] . '</a></div>
				' . (allowedTo('tp_can_admin_shout') ? '
				<div class="shoutbox_edit">
					<a href="' . $scripturl. '?action=tpshout;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="height:14px;" src="' . $settings['tp_images_url'] . '/TPmodify_shout.png" alt="'.$txt['tp-edit'].'" /></a>
					<a onclick="TPupdateShouts(\'del\', '. $row['id'] . '); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="' . $scripturl. '?action=tpshout;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="height:14px;" src="' . $settings['tp_images_url'] . '/TPdelete_shout.png" alt="'.$txt['tp-delete'].'" /></a>
				</div>' : '') . '
				<h4><a ' .(!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' .$row['online_color']. ';"' : '') . ' href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['real_name'] . '</a></h4>
				<span class="smalltext clear" style="padding-top: .5em;color:' .$context['TPortal']['shoutbox_timecolor']. ';">'. date($context['TPortal']['shoutbox_timeformat'], $row['time']).'</span>
			</div>
			<div class="bubble speech" ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' .$context['TPortal']['shoutbox_textcolor']. '">' : '>') . '' .$row['content']. '</div>
		</div>
	</div>',
	'1' => '
	<div style="padding-bottom: 5px;">
		<div class="tp_shoutcontainer showhover">
			<a ' .(!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' .$row['online_color']. '"' : '"') .' href="' .$scripturl. '?action=profile;u=' . $row['member_id']. '">'. $row['real_name'] .'</a>:
			' .(allowedTo('tp_can_admin_shout') ? '
			<div class="shoutbox_edit">
				<a href="' . $scripturl. '?action=tpshout;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="height:14px;" src="' . $settings['tp_images_url'] . '/TPmodify_shout.png" alt="'.$txt['tp-edit'].'" /></a>
				<a onclick="TPupdateShouts(\'del\', '. $row['id'] . '); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="' . $scripturl. '?action=tpshout;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="height:14px;" src="' . $settings['tp_images_url'] . '/TPdelete_shout.png" alt="'.$txt['tp-delete'].'" /></a>
			</div>' : ''). '
			<div class="smalltext shout_date" style="padding-top: .5em;color:' .$context['TPortal']['shoutbox_timecolor']. ';">'. date($context['TPortal']['shoutbox_timeformat'], $row['time']).'</div>
			<div class="shoutbody_layout1" '. (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' .$context['TPortal']['shoutbox_textcolor']. '">' : '>') . '' . $row['content'] .'</div>
		</div>
	</div>',
	'2' => '	
		<div class="shoutbody_layout2" style="background:' . (($row['id'] % 2) ? ($context['TPortal']['shoutbox_linecolor2']) : ($context['TPortal']['shoutbox_linecolor1'])) . ';">
			<div class="showhover">
                <div class="shoutbox_time">	
				    <span class="smalltext" style="color:' .$context['TPortal']['shoutbox_timecolor']. ';">'. date($context['TPortal']['shoutbox_timeformat'], $row['time'] ).'</span>
                </div>
				<div class="shoutbox_edit">	
					' . (allowedTo( 'tp_can_admin_shout' ) ? '
					<a href="'.$scripturl.'?action=tpshout;shout=admin;s='.$row['id'].';'.$context['session_var'].'='.$context['session_id'].'"><img style="height:14px;" src="'.$settings['tp_images_url'].'/TPmodify_shout.png" alt="'.$txt['tp-edit'].'" /></a>
					<a onclick="TPupdateShouts(\'del\', '. $row['id'].'); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="'.$scripturl.'?action=tpshout;shout=del;s='.$row['id'].';'.$context['session_var'].'='.$context['session_id'].'"><img style="height:14px;" src="'.$settings['tp_images_url'].'/TPdelete_shout.png" alt="'.$txt['tp-delete'].'" /></a>' : '').'
				</div>
				<b><a ' .(!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' .$row['online_color']. ';"' : '') . '
				href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['real_name'] . '</a></b>: <span ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' .$context['TPortal']['shoutbox_textcolor']. '">' : '>') . ''.$row['content'].'</span>
				<p class="clearthefloat"></p>
			</div>
		</div>',
	'3' => '	
		<div class="shoutbody_layout3" style="background:' . (($row['id'] % 2) ? ($context['TPortal']['shoutbox_linecolor2']) : ($context['TPortal']['shoutbox_linecolor1'])) . ';">
			<div class="showhover">
				<div class="shoutbox_edit">	
					' . (allowedTo( 'tp_can_admin_shout' ) ? '
					<a href="'.$scripturl.'?action=tpshout;shout=admin;s='.$row['id'].';'.$context['session_var'].'='.$context['session_id'].'"><img style="height:14px;" src="'.$settings['tp_images_url'].'/TPmodify_shout.png" alt="'.$txt['tp-edit'].'" /></a>
					<a onclick="TPupdateShouts(\'del\', '. $row['id'].'); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="'.$scripturl.'?action=tpshout;shout=del;s='.$row['id'].';'.$context['session_var'].'='.$context['session_id'].'"><img style="height:14px;" src="'.$settings['tp_images_url'].'/TPdelete_shout.png" alt="'.$txt['tp-delete'].'" /></a>' : '').'
				</div>
				<a ' .(!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' .$row['online_color']. ';"' : '') . '
				href="' . $scripturl . '?action=profile;u=' . $row['member_id'] . '">' . $row['real_name'] . '</a>: <span ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' .$context['TPortal']['shoutbox_textcolor']. '">' : '>') . ''.$row['content'].'</span>
				<span class="smalltext" style="color:' .$context['TPortal']['shoutbox_timecolor']. ';">'. date($context['TPortal']['shoutbox_timeformat'], $row['time'] ).'</span>
				<p class="clearthefloat"></p>
			</div>
		</div>',
	);

	return $layoutOptions[$context['TPortal']['shoutbox_layout']];
}

function template_tpshout_ajax()
{
	global $context;

	echo '
	<div id="'. (!empty($context['TPortal']['shoutError']) ? 'shoutError' : 'bigshout') . '">'. $context['TPortal']['rendershouts']. '</div>';
}

// View shouts Profile page
function template_tpshout_profile()
{
	global $settings, $txt, $context;

	echo '
		<div class="cat_bar"><h3 class="catbg">'.$txt['shoutboxprofile'].'</h3></div>
		<p class="information">'.$txt['shoutboxprofile2'].'</p>
		<div></div>
		<div id="tpshout_profile" class="windowbg padding-div">
			<div class="windowbg addborder tp_pad">';
	echo $txt['tp-prof_allshouts'].' <b>', $context['TPortal']['all_shouts'] ,'</b><br>';
	echo '
			</div><br>
			<table class="table_grid tp_grid" style="width:100%">
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col" class="shouts">
						<div class="float-items" style="width:30%;text-align:left">'.$txt['date'].'</div>
						<div class="smalltext float-items" style="width:70%;text-align:left">',$txt['tp-shout'],'</div>
					</th>
					</tr>
				</thead>
				<tbody>';
		if(isset($context['TPortal']['profile_shouts']) && sizeof($context['TPortal']['profile_shouts'])>0){
			foreach($context['TPortal']['profile_shouts'] as $art){
				echo '
					<tr class="windowbg">
					<td class="shouts">
						<div class="smalltext float-items" style="width:30%;text-align:left" >',$art['created'],'</div>
						<div class="smalltext float-items" style="width:70%;" >',$art['shout'],'</div>
					</td>
					</tr>';
			}
		}
		else
			echo '
					<tr class="windowbg">
					<td class="tpshout_date" colspan="3">
						<div class="smalltext">',$txt['tpsummary_noshout'],'</div>	
					</td>
					</tr>';

		echo '
				</tbody>
			</table>
			<div class="padding-div">'.$context['TPortal']['pageindex'].'</div>
		</div>';
}

?>
