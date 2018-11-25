<?php
/**
 * @package TinyPortal
 * @version 1.6.1
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2018 - The TinyPortal Team
 *
 */

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
		<div class="catbg" style="padding: 5px 5px 5px 1em;">' , $txt['tp-tabs10'] , '</div>
		<div class="windowbg tp_pad"">';

	echo '
			<div id="bigshout">', $shouts, '</div>';

	echo '
			<form  accept-charset="', $context['character_set'], '" class="smalltext" style="padding: 10px; margin: 0; text-align: center;" name="'. $context['tp_shoutbox_form']. '"  id="'. $context['tp_shoutbox_form']. '" action="'.$scripturl.'?action=tpmod;shout=save" method="post" >
				<input type="hidden" name="tp-shout-name" value="'.$context['user']['name'].'" />
				<input name="tp-shout-url" type="hidden" value="'. $smcFunc['htmlspecialchars']($tp_where).'" />
				<input type="hidden" name="sc" value="', $context['session_id'], '" />
			</form>
		</div>
	</div>';

}

function template_tpshout_admin()
{
	global $context, $scripturl, $txt;

	 echo '
	<form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpmod;shout=admin"  method="post" style="margin: 0px;">
		<input name="TPadmin_blocks" type="hidden" value="set" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="singlemenuedit">
			<div class="cat_bar"><h3 class="catbg">'.$txt['tp-shoutboxsettings'].'</h3></div>
			<div id="tpshout_admin" class="admintable admin-area">
				<div class="windowbg noup">
					<div class="formtable padding-div">
						<div class="addborder">
							<div style="width:47%;" class="float-items"><strong>'.$txt['tp-shoutboxitems'].'</strong></div>
							<div class="smalltext float-items" align="right" style="width:47%;"><b>'. $context['TPortal']['shoutbox_pageindex'].'</b></div>
							<p class="clearthefloat"></p>
						</div>';


	foreach($context['TPortal']['admin_shoutbox_items'] as $admin_shouts)
	{
		echo '			<div class="addborder">
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

		echo '			<div>
							<div class="normaltext float-items" style="width:47%;">
								<input name="tp_shoutsdelall" type="checkbox" value="ON" onclick="javascript:return confirm(\''.$txt['tp-confirm'].'\')"> <strong>'.$txt['tp-deleteallshouts'].'</strong>&nbsp;&nbsp;
							</div>
							<div class="smalltext float-items" align="right"  style="width:47%;">
								<b>'.$context['TPortal']['shoutbox_pageindex'].'</b>
						   </div>
						   <p class="clearthefloat"></p>
						</div>
					</div>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
			</div>
	</form><p class="clearthefloat"></p>';
}

function template_tpshout_admin_settings()
{
	global $context, $scripturl, $txt, $settings;

	echo '
	<form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpmod;shout=admin"  method="post">
		<input name="TPadmin_blocks" type="hidden" value="set" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="singlemenuedit">
			<div class="cat_bar"><h3 class="catbg">'.$txt['tp-shoutboxsettings'].'</h3></div>
			<div id="tpshout_admin_settings" class="admintable admin-area">
				<div class="windowbg noup">
					<div class="formtable padding-div">
					<dl class="settings">
						<dt>
							'.$txt['tp-sticky-title'].'
						</dt>
						<dd>
							<textarea style="width: 90%; height: 50px;" name="tp_shoutbox_stitle">' , !empty($context['TPortal']['shoutbox_stitle']) ? $context['TPortal']['shoutbox_stitle'] : '', '</textarea><br><br>
						</dd>
						<dt>
							'.$txt['tp-shoutbox_showsmile'].'
						</dt>
						<dd>
							<input name="tp_shoutbox_smile" type="radio" value="1" ' , $context['TPortal']['show_shoutbox_smile']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shoutbox_smile" type="radio" value="0" ' , $context['TPortal']['show_shoutbox_smile']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br><br>
						</dd>
						<dt>'.$txt['tp-shoutbox_showicons'].'
						</dt>
						<dd>
							<input name="tp_shoutbox_icons" type="radio" value="1" ' , $context['TPortal']['show_shoutbox_icons']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shoutbox_icons" type="radio" value="0" ' , $context['TPortal']['show_shoutbox_icons']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br><br>
						</dd>
						<dt>
							'.$txt['tp-shout-allow-links'].'
						</dt>
						<dd>
							<input name="tp_shout_allow_links" type="radio" value="1" ' , $context['TPortal']['shout_allow_links'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shout_allow_links" type="radio" value="0" ' , $context['TPortal']['shout_allow_links'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br><br>
						</dd>
						<dt>
							'.$txt['tp-shoutboxusescroll'].'
						</dt>
						<dd>
							<input name="tp_shoutbox_usescroll" type="radio" value="1" ' , $context['TPortal']['shoutbox_usescroll'] > 0 ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shoutbox_usescroll" type="radio" value="0" ' , $context['TPortal']['shoutbox_usescroll'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br><br>
						</dd>
						<dt>
							'.$txt['tp-shoutboxduration'].'
						</dt>
						<dd>
							<input type="text" size="6" name="tp_shoutbox_scrollduration" value="' . $context['TPortal']['shoutbox_scrollduration'] . '" /><br><br>
						</dd>
						<dt>
							'.$txt['tp-shout-autorefresh'].'
						</dt>
						<dd>
							<input size="6" name="tp_shoutbox_refresh" type="text" value="' ,$context['TPortal']['shoutbox_refresh'], '" /><br><br>
						</dd>
						<dt>
							'.$txt['shout_submit_returnkey'].'
						</dt>
						<dd>
							<input name="tp_shout_submit_returnkey" type="radio" value="2" ' , $context['TPortal']['shout_submit_returnkey'] == '2' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes-ctrl'].'
							<input name="tp_shout_submit_returnkey" type="radio" value="1" ' , $context['TPortal']['shout_submit_returnkey'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shout_submit_returnkey" type="radio" value="0" ' , $context['TPortal']['shout_submit_returnkey'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						'.$txt['shoutbox_layout'].'<br>
						<div class="float-items"><div><input name="tp_shoutbox_layout" type="radio" value="0" ' , $context['TPortal']['shoutbox_layout'] == '0' ? ' checked="checked"' : '' , ' /></div><div><img src="' . $settings['tp_images_url'] . '/shout_layout1.png" alt="Layout 1" align="right"/></div></div>
						<div class="float-items"><div><input name="tp_shoutbox_layout" type="radio" value="1" ' , $context['TPortal']['shoutbox_layout'] == '1' ? ' checked="checked"' : '' , ' /></div><div><img src="' . $settings['tp_images_url'] . '/shout_layout2.png" alt="Layout 2" /></div></div>
						<div class="float-items"><div><input name="tp_shoutbox_layout" type="radio" value="2" ' , $context['TPortal']['shoutbox_layout'] == '2' ? ' checked="checked"' : '' , ' /></div><div><img src="' . $settings['tp_images_url'] . '/shout_layout3.png" alt="Layout 3" /></div></div>
						<div class="float-items"><div><input name="tp_shoutbox_layout" type="radio" value="3" ' , $context['TPortal']['shoutbox_layout'] == '3' ? ' checked="checked"' : '' , ' /></div><div><img src="' . $settings['tp_images_url'] . '/shout_layout4.png" alt="Layout 4" /></div></div>
						<p class="clearthefloat"></p>
					</dl>
					<dl class="settings">
						<dt>
							'.$txt['tp-shoutboxheight'].'
						</dt>
						<dd>
							<input size="6" name="tp_shoutbox_height" type="text" value="' ,$context['TPortal']['shoutbox_height'], '" /><br><br>
						</dd>
						<dt>
							'.$txt['tp-shoutboxlimit'].'
						</dt>
						<dd>
							<input size="6" name="tp_shoutbox_limit" type="text" value="' ,$context['TPortal']['shoutbox_limit'], '" /><br><br>
						</dd>
						<dt>'.$txt['tp-shoutboxmaxlength'].'
						</dt>
						<dd>
							<input size="6" name="tp_shoutbox_maxlength" type="text" value="' ,$context['TPortal']['shoutbox_maxlength'], '" /><br><br>
						</dd>
						<dt>'.$txt['tp-shoutboxtimeformat'].'
						</dt>
						<dd>
							<input size="15" name="tp_shoutbox_timeformat" type="text" value="' ,$context['TPortal']['shoutbox_timeformat'], '" /><br><br>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>'.$txt['tp-shoutboxcolors'].'
						</dt>
						<dt>
							'.$txt['tp-shoutbox_use_groupcolor'].'
							' , (!empty($context['TPortal']['use_groupcolor'])) ? '<div class="smalltext" style="color:red;">'.$txt['tp-use_groupcolordesc'].'</div>' : '' , '
						</dt>
						<dd>
							<input name="tp_shoutbox_use_groupcolor" type="radio" value="1" ' , $context['TPortal']['shoutbox_use_groupcolor']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_shoutbox_use_groupcolor" type="radio" value="0" ' , $context['TPortal']['shoutbox_use_groupcolor']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br><br>
						</dd>
						<dt>'.$txt['tp-shoutboxtextcolor'].'
						</dt>
						<dd>
							<input size="10" name="tp_shoutbox_textcolor" type="text" value="' ,$context['TPortal']['shoutbox_textcolor'], '" /><br><br>
						</dd>
						<dt>'.$txt['tp-shoutboxtimecolor'].'
						</dt>
						<dd>
							<input size="10" name="tp_shoutbox_timecolor" type="text" value="' ,$context['TPortal']['shoutbox_timecolor'], '" /><br><br>
						</dd>
						<dt>'.$txt['tp-shoutboxlinecolor1'].'
						</dt>
						<dd>
							<input size="10" name="tp_shoutbox_linecolor1" type="text" value="' ,$context['TPortal']['shoutbox_linecolor1'], '" /><br><br>
						</dd>
						<dt>'.$txt['tp-shoutboxlinecolor2'].'
						</dt>
						<dd>
							<input size="10" name="tp_shoutbox_linecolor2" type="text" value="' ,$context['TPortal']['shoutbox_linecolor2'], '" /><br><br>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>
							'.$txt['tp-show_profile_shouts'].'
						</dt>
						<dd>
							<input name="tp_show_profile_shouts" type="radio" value="1" ' , $context['TPortal']['profile_shouts_hide'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
							<input name="tp_show_profile_shouts" type="radio" value="0" ' , $context['TPortal']['profile_shouts_hide'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br><br>
						</dd>
					</dl>
				</div>
			   <div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form><p class="clearthefloat"></p>';
}

function template_tpshout_shoutblock()
{
	global $context, $scripturl, $txt, $settings, $user_info;

	if(!isset($context['TPortal']['shoutbox']))
		$context['TPortal']['shoutbox'] = '';

	$context['tp_shoutbox_form'] = 'tp_shoutbox';
	$context['tp_shout_post_box_name'] = 'tp_shout';

	if(!empty($context['TPortal']['shoutbox_stitle']))
		echo '
		<p style="margin-top: 0;">' . parse_bbc($context['TPortal']['shoutbox_stitle'],true) . '</p><hr>';

	if($context['TPortal']['shoutbox_usescroll'] > '0')
		echo '
		<marquee id="tp_marquee" behavior="scroll" direction="down" scrollamount="'. $context['TPortal']['shoutbox_scrollduration'] . '" height="'. $context['TPortal']['shoutbox_height'] . '">
			<div class="tp_shoutframe">'.$context['TPortal']['shoutbox'].'</div>
		</marquee>';
	else
		echo '
			<div id="shoutboxContainer">
				<div class="middletext" style="width: 100%; height: '.$context['TPortal']['shoutbox_height'].'px; overflow: auto;">
					<div class="tp_shoutframe">'. $context['TPortal']['shoutbox']. '</div>
				</div>
			</div>';

	if(!$context['user']['is_guest'] && allowedTo('tp_can_shout'))
	{
	if ( in_array($context['TPortal']['shoutbox_layout'], array('2','3'), true ) )
		{
		echo '
			<form  accept-charset="'. $context['character_set']. '" class="smalltext" name="'. $context['tp_shoutbox_form']. '"  id="'. $context['tp_shoutbox_form']. '" action="'.$scripturl.'?action=tpmod;shout=save" method="post" ><hr>
			<div style="margin-bottom: 5px;"><input type="text" maxlength="' .$context['TPortal']['shoutbox_maxlength']. '" class="shoutbox_editor'. $context['TPortal']['shoutbox_layout']. '" name="'. $context['tp_shout_post_box_name']. '" id="'. $context['tp_shout_post_box_name']. '" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);" tabindex="', $context['tabindex']++, '"></input>
			<input onclick="TPupdateShouts(\'save\'); return false;" type="submit" name="shout_send" value="&nbsp;'.$txt['shout!'].'&nbsp;" tabindex="', $context['tabindex']++, '" class="button_submit" />
			<a href="' , $scripturl , '?action=tpmod;shout=show50" title="'. $txt['tp-shout-history'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPhistory.png" alt="" /></a>
			<a id="tp_shout_refresh" onclick="TPupdateShouts(\'fetch\'); return false;" href="' , $scripturl , '?action=tpmod;shout=refresh" title="'. $txt['tp-shout-refresh'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPrefresh.png" alt="" /></a>
			<p class="clearthefloat"></p>
			</div>';

			if(!empty($context['TPortal']['show_shoutbox_smile']) && $user_info['smiley_set'] != 'none')
			{
			echo '
			<div style="display: inline-block;min-width:150px;vertical-align: top;">';
				shout_smiley_code();
				print_shout_smileys();
			echo '
			</div>';				
			}
			if(!empty($context['TPortal']['show_shoutbox_icons']))
			{
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
	else
		{
		echo '
			<form  accept-charset="'. $context['character_set']. '" class="smalltext" style="text-align: center; width: 99%;" name="'. $context['tp_shoutbox_form']. '"  id="'. $context['tp_shoutbox_form']. '" action="'.$scripturl.'?action=tpmod;shout=save" method="post" ><hr>
			<textarea class="shoutbox_editor'. $context['TPortal']['shoutbox_layout']. '" maxlength="' .$context['TPortal']['shoutbox_maxlength']. '" name="'. $context['tp_shout_post_box_name']. '" id="'. $context['tp_shout_post_box_name']. '" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);" tabindex="', $context['tabindex']++, '"></textarea><br>';

			if(!empty($context['TPortal']['show_shoutbox_smile']) && $user_info['smiley_set'] != 'none')
			{
				shout_smiley_code();
				print_shout_smileys();
			}
			if(!empty($context['TPortal']['show_shoutbox_icons']))
				shout_bcc_code();

			echo '
			<div id="shout_errors"></div>
			<p class="clearthefloat"></p>
			<hr>
			<div style="overflow: hidden;">
				<a href="' , $scripturl , '?action=tpmod;shout=show50" title="'. $txt['tp-shout-history'] . '"><img class="floatleft" src="' . $settings['tp_images_url'] . '/TPhistory.png" alt="" /></a>
				<input onclick="TPupdateShouts(\'save\'); return false;" type="submit" name="shout_send" value="&nbsp;'.$txt['shout!'].'&nbsp;" tabindex="', $context['tabindex']++, '" class="button_submit" />
				<a id="tp_shout_refresh" onclick="TPupdateShouts(\'fetch\'); return false;" href="' , $scripturl , '?action=tpmod;shout=refresh" title="'. $txt['tp-shout-refresh'] . '"><img class="floatright" src="' . $settings['tp_images_url'] . '/TPrefresh.png" alt="" /></a>
			</div>
			<input type="hidden" id="tp-shout-name" name="tp-shout-name" value="'.$context['user']['name'].'" />
			<input type="hidden" name="sc" value="', $context['session_id'], '" />
		</form>';
		}	
	}
}
function template_tpshout_frontpage()
{

	global $context;

	echo 'test';
}

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
					<div align="left" class="float-items" align="center" style="width:30%;">'.$txt['date'].'</div>
					<div align="left" class="smalltext float-items" style="width:60%;">',$txt['tp-shout'],'</div>
					<div class="float-items" align="center" style="width:10%;">'. $txt['tp-edit'] .'</div>
				</th>
				</tr>
			</thead>
			<tbody>';
	if(isset($context['TPortal']['profile_shouts']) && sizeof($context['TPortal']['profile_shouts'])>0){
		foreach($context['TPortal']['profile_shouts'] as $art){
			echo '
				<tr class="windowbg">
				<td class="shouts">
					<div align="left" class="smalltext float-items" style="width:30%;" >',$art['created'],'</div>
					<div class="smalltext float-items" style="width:60%;" >',$art['shout'],'</div>
					<div class="float-items" align="center" style="width:10%;" >' , $art['editlink']!='' ? '<a href="'.$art['editlink'].'"><img border="0" src="'.$settings['tp_images_url'].'/TPedit.png" alt="" /></a>' : '' , '</div>
				</td>
				</tr>';
		}
	}
	else
		echo '
				<tr class="windowbg">
					<td class="tpshout_date" colspan="3">
					<div align="center" class="smalltext">',$txt['tpsummary_noshout'],'</div>	
					</td>
				</tr>';

	echo '
			</tbody>
		</table>
	<div style="padding: 3ex;">'.$context['TPortal']['pageindex'].'</div>
		</div>';

}

function template_singleshout($row)
{
	global $scripturl, $context, $settings, $txt;

	$layoutOptions = array(
	 '0' => '
	<div style="padding-bottom: 5px;">
		<div class="tp_shoutcontainer showhover">
			<div class="tp_shoutavatar">
				<div class="avy2"><a href="' . $scripturl. '?action=profile;u=' . $row['value5'] . '">' . $row['avatar'] . '</a></div>
				' . (allowedTo('tp_can_admin_shout') ? '
				<div class="shoutbox_edit">
					<a href="' . $scripturl. '?action=tpmod;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="height:14px;" src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="'.$txt['tp-edit'].'" /></a>
					<a onclick="TPupdateShouts(\'del\', '. $row['id'] . '); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="' . $scripturl. '?action=tpmod;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="height:14px;" src="' . $settings['tp_images_url'] . '/tp-delete_shout.png" alt="'.$txt['tp-delete'].'" /></a>
				</div>' : '') . '
				<h4><a ' .(!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' .$row['online_color']. ';"' : '') . ' href="' . $scripturl . '?action=profile;u=' . $row['value5'] . '">' . $row['realName'] . '</a></h4>
				<span class="smalltext clear" style="padding-top: .5em;color:' .$context['TPortal']['shoutbox_timecolor']. ';">'. date($context['TPortal']['shoutbox_timeformat'], $row['value2']).'</span>
			</div>
			<div class="bubble speech" ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' .$context['TPortal']['shoutbox_textcolor']. '">' : '>') . '' .$row['value1']. '</div>
		</div>
	</div>',
	'1' => '
	<div style="padding-bottom: 5px;">
		<div class="tp_shoutcontainer showhover">
			<a ' .(!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' .$row['online_color']. '"' : '"') .' href="' .$scripturl. '?action=profile;u=' . $row['value5']. '">'. $row['realName'] .'</a>:
			' .(allowedTo('tp_can_admin_shout') ? '
			<div class="shoutbox_edit">
				<a href="' . $scripturl. '?action=tpmod;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="height:14px;" src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="'.$txt['tp-edit'].'" /></a>
				<a onclick="TPupdateShouts(\'del\', '. $row['id'] . '); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="' . $scripturl. '?action=tpmod;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="height:14px;" src="' . $settings['tp_images_url'] . '/tp-delete_shout.png" alt="'.$txt['tp-delete'].'" /></a>
			</div>' : ''). '
			<div class="smalltext shout_date" style="padding-top: .5em;color:' .$context['TPortal']['shoutbox_timecolor']. ';">'. date($context['TPortal']['shoutbox_timeformat'], $row['value2']).'</div>
			<div class="shoutbody_layout1" '. (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' .$context['TPortal']['shoutbox_textcolor']. '">' : '>') . '' . $row['value1'] .'</div>
		</div>
	</div>',
	'2' => '	
		<div class="shoutbody_layout2" style="background:' . (($row['id'] % 2) ? ($context['TPortal']['shoutbox_linecolor2']) : ($context['TPortal']['shoutbox_linecolor1'])) . ';">
			<div class="showhover"
                <div class="shoutbox_time">	
				    <span class="smalltext" style="color:' .$context['TPortal']['shoutbox_timecolor']. ';">'. date($context['TPortal']['shoutbox_timeformat'], $row['value2'] ).'</span>
                </div>
				<div class="shoutbox_edit">	
					' . (allowedTo( 'tp_can_admin_shout' ) ? '
					<a href="'.$scripturl.'?action=tpmod;shout=admin;s='.$row['id'].';'.$context['session_var'].'='.$context['session_id'].'"><img style="height:14px;" src="'.$settings['tp_images_url'].'/TPmodify.png" alt="'.$txt['tp-edit'].'" /></a>
					<a onclick="TPupdateShouts(\'del\', '. $row['id'].'); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="'.$scripturl.'?action=tpmod;shout=del;s='.$row['id'].';'.$context['session_var'].'='.$context['session_id'].'"><img style="height:14px;" src="'.$settings['tp_images_url'].'/tp-delete_shout.png" alt="'.$txt['tp-delete'].'" /></a>' : '').'
				</div>
				<b><a ' .(!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' .$row['online_color']. ';"' : '') . '
				href="' . $scripturl . '?action=profile;u=' . $row['value5'] . '">' . $row['realName'] . '</a></b>: <span ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' .$context['TPortal']['shoutbox_textcolor']. '">' : '>') . ''.$row['value1'].'</span>
				<p class="clearthefloat"></p>
			</div>
		</div>',
	'3' => '	
		<div class="shoutbody_layout3" style="background:' . (($row['id'] % 2) ? ($context['TPortal']['shoutbox_linecolor2']) : ($context['TPortal']['shoutbox_linecolor1'])) . ';">
			<div class="showhover">
				<div class="shoutbox_edit">	
					' . (allowedTo( 'tp_can_admin_shout' ) ? '
					<a href="'.$scripturl.'?action=tpmod;shout=admin;s='.$row['id'].';'.$context['session_var'].'='.$context['session_id'].'"><img style="height:14px;" src="'.$settings['tp_images_url'].'/TPmodify.png" alt="'.$txt['tp-edit'].'" /></a>
					<a onclick="TPupdateShouts(\'del\', '. $row['id'].'); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="'.$scripturl.'?action=tpmod;shout=del;s='.$row['id'].';'.$context['session_var'].'='.$context['session_id'].'"><img style="height:14px;" src="'.$settings['tp_images_url'].'/tp-delete_shout.png" alt="'.$txt['tp-delete'].'" /></a>' : '').'
				</div>
				<a ' .(!empty($context['TPortal']['shoutbox_use_groupcolor']) ? 'style="color:' .$row['online_color']. ';"' : '') . '
				href="' . $scripturl . '?action=profile;u=' . $row['value5'] . '">' . $row['realName'] . '</a>: <span ' . (!empty($context['TPortal']['shoutbox_textcolor']) ? 'style="color:' .$context['TPortal']['shoutbox_textcolor']. '">' : '>') . ''.$row['value1'].'</span>
				<span class="smalltext" style="color:' .$context['TPortal']['shoutbox_timecolor']. ';">'. date($context['TPortal']['shoutbox_timeformat'], $row['value2'] ).'</span>
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

?>
