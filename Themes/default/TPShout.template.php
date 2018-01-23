<?php
/**
 * @package TinyPortal
 * @version 1.4R
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
		<div class="windowbg" style="padding: 1em;">';
	
	echo '
			<div id="bigshout" style="width: 99%; height: 100%;">', $shouts, '</div>';

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
		<div id="tpshout_admin" class="admintable admin-area">
			<div class="catbg">'.$txt['tp-shoutboxsettings'].'</div>
				<div class="multiplerow">
				  <div style="border-bottom:1px solid #ccc;">
					<div style="width:47%;border-left:1px solid #ccc;" class="float-items"><strong>'.$txt['tp-shoutboxitems'].'</strong></div>
					<div class="smalltext float-items" align="left" style="width:47%;border-left:1px solid #ccc;"><b>'. $context['TPortal']['shoutbox_pageindex'].'</b></div>
					<p class="clearthefloat"></p>
				  </div>';
						
					
	foreach($context['TPortal']['admin_shoutbox_items'] as $admin_shouts)
	{	
		echo '<div style="border-bottom:1px solid #ccc;">
		           <div class="fullwidth-on-res-layout float-items ' ,  !empty($admin_shouts['sticky']) ? 'windowbg2' : '' , '" style="width:30%;">
					'.$admin_shouts['poster'].' ['.$admin_shouts['ip'].']<br />'.$admin_shouts['time'].'<br />
					'. $admin_shouts['sort_member'].' <br /> '.$admin_shouts['sort_ip'].'<br />'.$admin_shouts['single'].'
				   </div>
				   <div class="float-items ' ,  !empty($admin_shouts['sticky']) ? 'windowbg2' : '' , '">
					<textarea style="vertical-align: middle; width: 99%;" rows="5" cols="40" wrap="auto" name="tp_shoutbox_item'.$admin_shouts['id'].'">' .html_entity_decode($admin_shouts['body']).'</textarea>
				   </div>
				   <div class="float-items ' ,  !empty($admin_shouts['sticky']) ? 'windowbg2' : '' , '">
					<input name="tp_shoutbox_hidden'.$admin_shouts['id'].'" type="hidden" value="1">
					<div style="text-align: right;"><strong><input style="vertical-align: middle;" name="tp_shoutbox_remove'.$admin_shouts['id'].'" type="checkbox" value="ON"> '.$txt['tp-remove'].'</strong></div>
				   </div><p class="clearthefloat"></p>
			 </div>';
	}

	echo '<div style="border-bottom:1px solid #ccc;">
		     	   <div class="normaltext float-items" style="width:47%;border-left:1px solid #ccc;">
					<input name="tp_shoutsdelall" type="checkbox" value="ON" onclick="javascript:return confirm(\''.$txt['tp-confirm'].'\')"> <strong>'.$txt['tp-deleteallshouts'].'</strong>&nbsp;&nbsp;
				   </div>
				   <div class="smalltext float-items" style="width:47%;border-left:1px solid #ccc;">
				     <b>'.$context['TPortal']['shoutbox_pageindex'].'</b>
				   </div>
				   <p class="clearthefloat"></p>
		 </div>
	 </div>
	 <div class="windowbg" style="padding:1%;"><input type="submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
	</div>
	</form><p class="clearthefloat"></p>';
}	

function template_tpshout_admin_settings()
{
	global $context, $scripturl, $txt;

	echo '
	<form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpmod;shout=admin"  method="post" style="margin: 0px;">
		<input name="TPadmin_blocks" type="hidden" value="set" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="singlemenuedit">
		<div id="tpshout_admin_settings" class="admintable admin-area">
			<div class="catbg">'.$txt['tp-shoutboxsettings'].'</div>
						<div class="multiplerow">
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['tp-sticky-title'].'</div>
								<div class="float-items" style="width:48%;">
									<textarea style="width: 90%; height: 50px;" name="tp_shoutbox_stitle">' , !empty($context['TPortal']['shoutbox_stitle']) ? $context['TPortal']['shoutbox_stitle'] : '', '</textarea>
								</div>
								<p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['tp-shoutbox_showsmile'].'</div>
								<div class="float-items" style="width:48%;">
									<input name="tp_shoutbox_smile" type="radio" value="1" ' , $context['TPortal']['show_shoutbox_smile']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
									<input name="tp_shoutbox_smile" type="radio" value="0" ' , $context['TPortal']['show_shoutbox_smile']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'
								</div>
								<p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['tp-shoutbox_showicons'].'</div>
								<div class="float-items" style="width:48%;">
									<input name="tp_shoutbox_icons" type="radio" value="1" ' , $context['TPortal']['show_shoutbox_icons']=='1' ? 'checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
									<input name="tp_shoutbox_icons" type="radio" value="0" ' , $context['TPortal']['show_shoutbox_icons']=='0' ? 'checked="checked"' : '' , ' /> '.$txt['tp-no'].'
								</div>
								<p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div width="40%" align="right" class="float-items" style="width:48%;">'.$txt['tp-shoutboxheight'].'</div>
								<div class="float-items" style="width:48%;"><input size="6" name="tp_shoutbox_height" type="text" value="' ,$context['TPortal']['shoutbox_height'], '" /></div>
							    <p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['tp-shoutboxusescroll'].'</div>
								<div class="float-items" style="width:48%;">
									<input name="tp_shoutbox_usescroll" type="radio" value="0" ' , $context['TPortal']['shoutbox_usescroll'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'<br />
									<input name="tp_shoutbox_usescroll" type="radio" value="1" ' , $context['TPortal']['shoutbox_usescroll'] > 0 ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
								</div>
								<p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['tp-shoutboxduration'].'</div>
								<div class="float-items" style="width:48%;">
									<input type="text" size="6" name="tp_shoutbox_scrollduration" value="' . $context['TPortal']['shoutbox_scrollduration'] . '" />
								</div>
								<p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['tp-shoutboxlimit'].'</div>
								<div class="float-items" style="width:48%;"><input size="6" name="tp_shoutbox_limit" type="text" value="' ,$context['TPortal']['shoutbox_limit'], '" /></div>
							    <p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['tp-shout-autorefresh'].'</div>
								<div class="float-items" style="width:48%;"><input size="6" name="tp_shoutbox_refresh" type="text" value="' ,$context['TPortal']['shoutbox_refresh'], '" /></div>
							    <p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['tp-show_profile_shouts'].'</div>
								<div class="float-items" style="width:48%;">
									<input name="tp_show_profile_shouts" type="radio" value="1" ' , $context['TPortal']['profile_shouts_hide'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
									<input name="tp_show_profile_shouts" type="radio" value="0" ' , $context['TPortal']['profile_shouts_hide'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'
								</div>
								<p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['tp-shout-allow-links'].'</div>
								<div class="float-items" style="width:48%;">
									<input name="tp_shout_allow_links" type="radio" value="1" ' , $context['TPortal']['shout_allow_links'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
									<input name="tp_shout_allow_links" type="radio" value="0" ' , $context['TPortal']['shout_allow_links'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'
								</div>
								<p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['shout_submit_returnkey'].'</div>
								<div class="float-items" style="width:48%;">
									<input name="tp_shout_submit_returnkey" type="radio" value="1" ' , $context['TPortal']['shout_submit_returnkey'] == '1' ? ' checked="checked"' : '' , ' /> '.$txt['tp-yes'].'
									<input name="tp_shout_submit_returnkey" type="radio" value="0" ' , $context['TPortal']['shout_submit_returnkey'] == '0' ? ' checked="checked"' : '' , ' /> '.$txt['tp-no'].'
								</div>
								<p class="clearthefloat"></p>
							</div>
							<div class="windowbg2">
								<div align="right" class="float-items" style="width:48%;">'.$txt['shoutbox_layout'].'</div>
								<div class="float-items" style="width:48%;">
									<div class="float-items" style="width:47%;"><input name="tp_shoutbox_layout" type="radio" value="0" ' , $context['TPortal']['shoutbox_layout'] == '0' ? ' checked="checked"' : '' , ' /> <img style="max-width: 80% !important;" src="tp-images/icons/shout_layout1.png" alt="Layout 1" align="right"/></div>
									<div class="float-items" style="width:47%;"><input name="tp_shoutbox_layout" type="radio" value="1" ' , $context['TPortal']['shoutbox_layout'] == '1' ? ' checked="checked"' : '' , ' /> <img style="max-width: 80% !important;" src="tp-images/icons/shout_layout2.png" alt="Layout 2" align="right"/></div>
								    <p class="clearthefloat"></p>
								</div>
								<p class="clearthefloat"></p>
							</div>
						</div>
					   <div class="windowbg3" style="padding:1%;"><input type="submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
		</div>
	</form><p class="clearthefloat"></p>';
}		

function template_tpshout_shoutblock()
{
	global $context, $scripturl, $txt, $settings;

	if(!isset($context['TPortal']['shoutbox']))
		$context['TPortal']['shoutbox'] = '';

	$context['tp_shoutbox_form'] = 'tp_shoutbox';
	$context['tp_shout_post_box_name'] = 'tp_shout';

	if(!empty($context['TPortal']['shoutbox_stitle'])) 
		echo 
	'<p style="margin-top: 0;">' . parse_bbc($context['TPortal']['shoutbox_stitle'],true) . '</p><hr><br>';
	
	if($context['TPortal']['shoutbox_usescroll'] > '0')
		echo '
		<marquee id="tp_marquee" behavior="scroll" direction="down" scrollamount="'. $context['TPortal']['shoutbox_scrollduration'] . '" height="'. $context['TPortal']['shoutbox_height'] . '">
			<div class="tp_shoutframe">'.$context['TPortal']['shoutbox'].'</div>
		</marquee>';
	else
		echo '

				<div id="shoutboxContainer"><div class="middletext" style="width: 99%; height: '.$context['TPortal']['shoutbox_height'].'px; overflow: auto;">
					<div class="tp_shoutframe">'. $context['TPortal']['shoutbox']. '</div>
				</div></div>';

	if(!$context['user']['is_guest'] && allowedTo('tp_can_shout'))
	{
		echo '
		<form  accept-charset="'. $context['character_set']. '" class="smalltext" style="padding: 0; text-align: center; margin: 0; width: 95%;" name="'. $context['tp_shoutbox_form']. '"  id="'. $context['tp_shoutbox_form']. '" action="'.$scripturl.'?action=tpmod;shout=save" method="post" ><hr>
		<textarea class="editor" name="'. $context['tp_shout_post_box_name']. '" id="'. $context['tp_shout_post_box_name']. '" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" onchange="storeCaret(this);" style="width: 100%;margin-top: 1em; height: 80px;"  tabindex="', $context['tabindex']++, '"></textarea><br />';
	
		if(!empty($context['TPortal']['show_shoutbox_smile']))
		{
			shout_smiley_code();
			print_shout_smileys();
		}
		if(!empty($context['TPortal']['show_shoutbox_icons']))
			shout_bcc_code();
			
		echo '
		<div id="shout_errors"></div><hr>
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
		<div class="title_bar"><h3 class="titlebg">'.$txt['shoutboxprofile'].'</h3></div>
		<div id="tpshout_profile" style="width:100%;">
			<div class="windowbg">
				<div class="smalltext" style="padding: 2ex;">'.$txt['shoutboxprofile2'].'</div>
			</div>
			<div class="windowbg2" style="margin-top:1px;margin-bottom:1px;">
				<div style="padding: 2ex;">';

	echo $txt['tp-prof_allshouts'].' <b>', !$context['TPortal']['profile_shouts_hide'] ? $context['TPortal']['all_shouts'] : '0' ,'</b><br />';
	echo '
				</div>
			</div>
			<div class="catbg">
				<div class="float-items" align="center" style="width:22%;border-right:1px solid #ffffff;">'.$txt['date'].'</div>
				<div align="center" class="smalltext float-items" style="width:51%;">',$txt['tp-shout'],'</div>
				<div class="float-items" align="center" style="width:19%;border-left:1px solid #ffffff;">'. $txt['tp-edit'] .'</div>
			    <p class="clearthefloat"></p>
		  </div>';
	if(!$context['TPortal']['profile_shouts_hide'] && isset($context['TPortal']['profile_shouts']) && sizeof($context['TPortal']['profile_shouts'])>0){
		foreach($context['TPortal']['profile_shouts'] as $art){
			echo '
			<div class="windowbg2" style="margin-bottom:1px;">
					<div align="center" class="smalltext float-items" style="width:22%;" >',$art['created'],'</div>
					<div class="smalltext float-items" style="width:51%;" >',$art['shout'],'</div>
					<div class="float-items" align="center" style="width:21%;" >' , $art['editlink']!='' ? '<a href="'.$art['editlink'].'"><img border="0" src="'.$settings['tp_images_url'].'/TPmodify.gif" alt="" /></a>' : '' , '</div>
				    <p class="clearthefloat"></p>
			</div>';
		}
	}
	else
			echo '
				<div class="windowbg2">
					<div align="center" class="smalltext">',$txt['tpsummary_shout'],' 0</div>
				</div>';

	echo '
			<div class="windowbg">
				<div style="padding: 2ex; font-weight: bold;">'.$context['TPortal']['pageindex'].'</div>
			</div>
		</div>
	</div>';

}

function template_singleshout($row)
{
	global $scripturl, $context, $settings, $txt;
	
	$layoutOptions = array(
	 '0' => '
	<div style="padding-bottom: 5px;">
		<div class="tp_shoutcontainer">
			<div class="tp_shoutavatar">
				<div class="avy2"><a href="' . $scripturl. '?action=profile;u=' . $row['value5'] . '">' . $row['avatar'] . '</a></div>
				' . (allowedTo('tp_can_admin_shout') ? '
				<div style="float: right; margin-bottom: 3px;">
					<a href="' . $scripturl. '?action=tpmod;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img src="' . $settings['tp_images_url'] . '/TPmodify.gif" alt="'.$txt['tp-edit'].'" /></a>
					<a onclick="TPupdateShouts(\'del\', '. $row['id'] . '); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="' . $scripturl. '?action=tpmod;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img src="' . $settings['tp_images_url'] . '/tp-delete_shout.gif" alt="'.$txt['tp-delete'].'" /></a>
				</div>' : '') . '
				<h4><a href="' . $scripturl . '?action=profile;u=' . $row['value5'] . '">' . $row['realName'] . '</a></h4>
				<div class="smalltext clear" style="padding-top: .5em;">'. timeformat($row['value2']).'</div>
			</div>
			<div class="bubble speech">' . $row['value1'] . '</div>
		</div>
	</div>',
	'1' => '	
	<div style="padding-bottom: 5px;">
		<div class="tp_shoutcontainer">							
			<div class="shout_options">
				' . $row['realName'] . ':
				' . (allowedTo('tp_can_admin_shout') ? '
				<a href="' . $scripturl. '?action=tpmod;shout=admin;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img src="' . $settings['tp_images_url'] . '/TPmodify.gif" alt="'.$txt['tp-edit'].'" /></a>
				<a onclick="TPupdateShouts(\'del\', '. $row['id'] . '); return false;" class="shout_delete" title="'.$txt['tp-delete'].'" href="' . $scripturl. '?action=tpmod;shout=del;s=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'].'"><img src="' . $settings['tp_images_url'] . '/tp-delete_shout.gif" alt="'.$txt['tp-delete'].'" /></a>' : ''). '
			</div> 
			<div class="shout_date">'. date('M. d Y - g:ia', $row['value2']).'</div>
			<div class="shoutbody_layout1">' . $row['value1'] . '</div>
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