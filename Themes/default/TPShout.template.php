<?php
/**
 * @package TinyPortal
 * @version 1.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2012 - The TinyPortal Team
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
		<div class="windowbg" style="padding: 1em;">';
	
	echo '
			<table cellpadding="0" align="center" width="100%" cellspacing="0" style="table-layout: fixed;">
				<tr>
					<td><div class="smalltext" id="bigshout" style="width: 99%; height: 100%;">', $shouts, '</div></td>
				</tr>
			</table>';

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
		<table class="admintable">
			<caption class="catbg">'.$txt['tp-shoutboxsettings'].'</caption>
			<tbody>
				<tr>
					<td class="tborder" style="padding: 0; border: none;">
				<table class="multiplerow">
					<tr>
						<td colspan="2" align="left" valign="top" style="padding-left: 20px;"><strong>'.$txt['tp-shoutboxitems'].'</strong></td>
						<td colspan="2" valign="top" class="smalltext" align="right" style="padding-right: 20px;"><b>'. $context['TPortal']['shoutbox_pageindex'].'</b>
						</td>
					</tr>';
						
						
						
	foreach($context['TPortal']['admin_shoutbox_items'] as $admin_shouts){
		
		echo '
			<tr>
		        <td width="30%" valign="top">
					'.$admin_shouts['poster'].' ['.$admin_shouts['ip'].']<br />'.$admin_shouts['time'].'<br />
					'. $admin_shouts['sort_member'].' <br /> '.$admin_shouts['sort_ip'].'<br />'.$admin_shouts['single'].'
				</td>
				<td valign="top" colspan="2">
					<textarea style="vertical-align: middle; width: 99%;" rows="3" cols="40" wrap="auto" name="tp_shoutbox_item'.$admin_shouts['id'].'">' .html_entity_decode($admin_shouts['body']).'</textarea></td><td class="windowbg2" valign="top" width="100px" align="center"><strong>
					<input style="vertical-align: middle;" name="tp_shoutbox_remove'.$admin_shouts['id'].'" type="checkbox" value="ON"> '.$txt['tp-remove'].'
					</strong>
				</td>
			</tr>';
	}

	echo '<tr class="windowbg">
		     			<td colspan="2" align="left" class="normaltext">
						<input name="tp_shoutsdelall" type="checkbox" value="ON" onclick="javascript:return confirm(\''.$txt['tp-confirm'].'\')"> <strong>'.$txt['tp-deleteallshouts'].'</strong></td><td colspan="2" align="right" class="smalltext"><b>'.$context['TPortal']['shoutbox_pageindex'].'</b>
		     			</td>
		     		</tr>
				</table>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td class="windowbg3"><input type="submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'">
					</td>
				</tr>
			</tfoot>
		</table>
	</form>';
}	

function template_tpshout_admin_settings()
{
	global $context, $scripturl, $txt;

	echo '
	<form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpmod;shout=admin"  method="post" style="margin: 0px;">
		<input name="TPadmin_blocks" type="hidden" value="set" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="singlemenuedit">
		<table class="admintable">
			<caption class="catbg">'.$txt['tp-shoutboxsettings'].'</caption>
			<tbody>
				<tr>
					<td class="tborder" style="padding: 0; border: none;">
				<table class="multiplerow">
				<tr class="windowbg2">
					<td align="right">'.$txt['tp-shoutbox_showsmile'].'</td>
					<td>
						<input name="tp_shoutbox_smile" type="radio" value="1" ' , $context['TPortal']['show_shoutbox_smile']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
						<input name="tp_shoutbox_smile" type="radio" value="0" ' , $context['TPortal']['show_shoutbox_smile']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'
					</td>
				</tr>
				<tr class="windowbg2">
					<td align="right">'.$txt['tp-shoutbox_showicons'].'</td>
					<td>
						<input name="tp_shoutbox_icons" type="radio" value="1" ' , $context['TPortal']['show_shoutbox_icons']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
						<input name="tp_shoutbox_icons" type="radio" value="0" ' , $context['TPortal']['show_shoutbox_icons']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'
					</td>
				</tr>
				<tr class="windowbg2">
					<td width="40%" align="right">'.$txt['tp-shoutboxheight'].'</td>
					<td><input size="6" name="tp_shoutbox_height" type="text" value="' ,$context['TPortal']['shoutbox_height'], '" /></td>
				</tr>
				<tr class="windowbg2">
					<td align="right">'.$txt['tp-shoutboxusescroll'].'</td>
					<td>
						<input name="tp_shoutbox_usescroll" type="radio" value="0" ' , $context['TPortal']['shoutbox_usescroll'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br />
						<input name="tp_shoutbox_usescroll" type="radio" value="1" ' , $context['TPortal']['shoutbox_usescroll'] > 0 ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
					</td>
				</tr>
				<tr class="windowbg2">
					<td align="right">'.$txt['tp-shoutboxduration'].'</td>
					<td>
						<input type="text" size="6" name="tp_shoutbox_scrollduration" value="' . $context['TPortal']['shoutbox_scrollduration'] . '" />
					</td>
				</tr>
				<tr class="windowbg2">
					<td align="right">'.$txt['tp-shoutboxlimit'].'</td>
					<td><input size="6" name="tp_shoutbox_limit" type="text" value="' ,$context['TPortal']['shoutbox_limit'], '" /></td>
				</tr>
				<tr class="windowbg2">
					<td align="right">'.$txt['tp-shout-autorefresh'].'</td>
					<td><input size="6" name="tp_shoutbox_refresh" type="text" value="' ,$context['TPortal']['shoutbox_refresh'], '" /></td>
				</tr>
				<tr class="windowbg2">
					<td align="right">'.$txt['tp-show_profile_shouts'].'</td>
					<td>
						<input name="tp_show_profile_shouts" type="radio" value="1" ' , $context['TPortal']['profile_shouts_hide'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
						<input name="tp_show_profile_shouts" type="radio" value="0" ' , $context['TPortal']['profile_shouts_hide'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'
					</td>
				</tr>
				<tr class="windowbg2">
					<td align="right">'.$txt['tp-shout-allow-links'].'</td>
					<td>
					<input name="tp_shout_allow_links" type="radio" value="1" ' , $context['TPortal']['shout_allow_links'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
					<input name="tp_shout_allow_links" type="radio" value="0" ' , $context['TPortal']['shout_allow_links'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'
					</td>
				</tr>
				</table>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td class="windowbg3"><input type="submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'">
					</td>
				</tr>
			</tfoot>
		</table>
	</form>';
}		

function template_tpshout_shoutblock()
{
	global $context, $scripturl, $txt, $settings;

	if(!isset($context['TPortal']['shoutbox']))
		$context['TPortal']['shoutbox'] = '';

	$context['tp_shoutbox_form'] = 'tp_shoutbox';
	$context['tp_shout_post_box_name'] = 'tp_shout';

	if($context['TPortal']['shoutbox_usescroll'] > '0')
		echo '
		<marquee id="tp_marquee" behavior="scroll" direction="down" scrollamount="'. $context['TPortal']['shoutbox_scrollduration'] . '" height="'. $context['TPortal']['shoutbox_height'] . '">
			<div class="tp_shoutframe">'.$context['TPortal']['shoutbox'].'</div>
		</marquee>';
	else
		echo '
    <table cellpadding="0" align="center" width="100%" cellspacing="0" style="table-layout: fixed;">
		<tr>
			<td>
			<div class="middletext" style="width: 99%; height: '.$context['TPortal']['shoutbox_height'].'px; overflow: auto;">
			<div class="tp_shoutframe">'. $context['TPortal']['shoutbox']. '</div>
			</div></td>
		</tr>
	</table>';

	echo '
		<form  accept-charset="'. $context['character_set']. '" class="smalltext" style="padding: 0; text-align: center;" name="'. $context['tp_shoutbox_form']. '"  id="'. $context['tp_shoutbox_form']. '" action="'.$scripturl.'?action=tpmod;shout=save" method="post">';

	if(allowedTo('tp_can_shout'))
	{
		echo '
		<textarea class="editor" name="'. $context['tp_shout_post_box_name']. '" id="'. $context['tp_shout_post_box_name']. '" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);" style="width: 80%;margin-top: 1ex; height: 50px;"  tabindex="', $context['tabindex']++, '"></textarea><br />';
		
		if(!empty($context['TPortal']['show_shoutbox_smile']))
		{
			shout_smiley_code();
			print_shout_smileys();
		}
		if(!empty($context['TPortal']['show_shoutbox_icons']))
			shout_bcc_code();
			
		echo '
		<div>
			<a href="' , $scripturl , '?action=tpmod;shout=show50" title="'. $txt['tp-shout-history'] . '"><img src="' . $settings['tp_images_url'] . '/TPhistory.png" alt="" /></a>
			<input onclick="TPupdateShouts(\'save\'); return false;" style="padding: 6px; margin: 5px 10px 0;" class="smalltext" type="submit" name="shout_send" value="'.$txt['shout!'].'" tabindex="', $context['tabindex']++, '" />
			<a id="tp_shout_refresh" onclick="TPupdateShouts(\'fetch\'); return false;" href="' , $scripturl , '?action=tpmod;shout=refresh" title="'. $txt['tp-shout-refresh'] . '"><img src="' . $settings['tp_images_url'] . '/TPrefresh.png" alt="" /></a>
		</div>';
		
	}
	
	if($context['user']['is_guest'] && allowedTo('tp_can_shout'))
		echo '<br />
		<input style="margin-top: 4px;" id="tp-shout-name" size="20" class="smalltext" type="text" name="tp-shout-name" value="'.$txt['tp-guest'].'" />';
	elseif($context['user']['is_logged'])
		echo '
		<input type="hidden" id="tp-shout-name" name="tp-shout-name" value="'.$context['user']['name'].'" />';

	echo '
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
	</form>';

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
	<div class="bordercolor" style="margin-left: 1ex;">
		<h3 class="titlebg"><span class="left"></span>'.$txt['shoutboxprofile'].'</h3>
		<table width="100%" cellpadding="4" cellspacing="1" border="0">
			<tr class="windowbg">
				<td colspan="6" class="smalltext" style="padding: 2ex;">'.$txt['shoutboxprofile2'].'</td>
			</tr>
			<tr class="windowbg2">
				<td colspan="6" style="padding: 2ex;">';

	echo $txt['tp-prof_allshouts'].' <b>', !$context['TPortal']['profile_shouts_hide'] ? $context['TPortal']['all_shouts'] : '0' ,'</b><br />';
	echo '
				</td>
			</tr>
			<tr class="catbg">
				<td align="center" width="10%" nowrap="nowrap">'.$txt['date'].'</td>
				<td align="center" class="smalltext" nowrap="nowrap">',$txt['tp-shout'],'</td>
				<td align="center" nowrap="nowrap" width="10%">'. $txt['tp-edit'] .'</td>
			</tr>';
	if(!$context['TPortal']['profile_shouts_hide'] && isset($context['TPortal']['profile_shouts']) && sizeof($context['TPortal']['profile_shouts'])>0){
		foreach($context['TPortal']['profile_shouts'] as $art){
			echo '
				<tr class="windowbg2">
					<td valign="top" align="center" class="smalltext" nowrap="nowrap">',$art['created'],'</td>
					<td valign="top" class="smalltext" >',$art['shout'],'</td>
					<td valign="top" align="center">' , $art['editlink']!='' ? '<a href="'.$art['editlink'].'"><img border="0" src="'.$settings['tp_images_url'].'/TPmodify.gif" alt="" /></a>' : '' , '</td>
				</tr>';
		}
	}
	else
			echo '
				<tr class="windowbg2">
					<td valign="top" align="center" class="smalltext" colspan="3">',$txt['tpsummary_shout'],' 0</td></tr>';

	echo '
			<tr class="windowbg">
				<td colspan="6" style="padding: 2ex; font-weight: bold;">'.$context['TPortal']['pageindex'].'</td>
			</tr>
		</table>
	</div>';

}

function template_singleshout($row)
{
	global $scripturl, $context, $settings, $txt;

	$return = '
			<div style="padding-bottom: 5px;">
				<div class="tp_shoutcontainer">
					<div class="tp_shoutavatar">
						<div class="avy2"><a href="' . $scripturl. '?action=profile;u=' . $row['value5'] . '">' . $row['avatar'] . '</a></div>
						' . (allowedTo('tp_shoutbox') ? '
						<div style="float: right; margin-bottom: 3px;">
							<a href="' . $scripturl. '?action=tpmod;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img src="' . $settings['tp_images_url'] . '/TPmodify.gif" alt="'.$txt['tp-edit'].'" /></a>
							<a onclick="TPupdateShouts(\'del\', '. $row['id'] . '); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="' . $scripturl. '?action=tpmod;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img src="' . $settings['tp_images_url'] . '/tp-delete_shout.gif" alt="'.$txt['tp-delete'].'" /></a>
						</div>' : '') . '
						<h4>' . $row['realName'] . '</h4>
						<div class="smalltext">'. timeformat($row['value2']).'</div>
					</div>
					<div class="tp_shoutupper"></div>
					<div class="tp_shoutbody">' . $row['value1'] . '</div>
				</div>
			</div>';

	return $return;
}

function template_tpshout_ajax() {
	global $context;
	echo '
	<div class="smalltext" id="'. !empty($context['TPortal']['shoutError']) ? 'shoutError' : 'bigshout' . '" style="width: 99%; height: 100%;">', $context['TPortal']['rendershouts'], '</div>';
}

?>