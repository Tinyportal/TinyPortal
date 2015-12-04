<?php
/**
 * @package TinyPortal
 * @version 1.2
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2015 - The TinyPortal Team
 *
 */

// TPortal searchblock
function TPortal_searchbox()
{
	global $context, $txt, $scripturl;

	echo '
	<form accept-charset="', $context['character_set'], '" action="', $scripturl, '?action=search2" method="post" style="padding: 0; text-align: center; margin: 0; ">
		<input type="text" name="search" value="" class="block_search" />
		<input type="submit" name="submit" value="', $txt['search'], '" class="block_search_submit button_submit" /><br />
		<br /><span class="smalltext"><a href="', $scripturl, '?action=search;advanced">', $txt['search_advanced'], '</a></span>
		<input type="hidden" name="advanced" value="0" />
	</form>';
}

// TPortal onlineblock
function TPortal_onlinebox()
{
	global $context;

	if($context['TPortal']['useavataronline'] == 1)
		tpo_whos();
	else
		echo '
	<div style="line-height: 1.4em;">' , ssi_whosOnline() , '</div>';
}
function tpo_whos($buddy_only = false)
{
	global $txt, $scripturl;
	
	$whos = tpo_whosOnline();
	echo '
	<div>
	' . $whos['num_guests'] .' ' , $whos['num_guests'] == 1 ? $txt['guest'] : $txt['guests'] , ',
	' . $whos['num_users_online'] .' ' , $whos['num_users_online'] == 1 ? $txt['user'] : $txt['users'] , ' 
	</div>';
	if(isset($whos['users_online']) && count($whos['users_online']) > 0)
	{
		$ids = array(); 
		$names = array(); 
		$times = array();
		foreach($whos['users_online'] as $w => $wh)
		{
			$ids[] = $wh['id'];
			$names[$wh['id']] = $wh['name'];
			$times[$wh['id']] = timeformat($w);
		}
		$avy = progetAvatars($ids);
		foreach($avy as $a => $av)
			echo '
		<a class="avatar_single2" title="'.$names[$a].'" href="' . $scripturl . '?action=profile;u='.$a.'">' . $av . '</a>';
	}
}
function tpo_whosOnline()
{
	global $sourcedir;

	require_once($sourcedir . '/Subs-MembersOnline.php');
	$membersOnlineOptions = array(
		'show_hidden' => allowedTo('moderate_forum'),
		'sort' => 'log_time',
		'reverse_sort' => true,
	);
	$return = getMembersOnlineStats($membersOnlineOptions);
	return $return;
}
function progetAvatars($ids)
{
	global $user_info, $smcFunc, $modSettings, $scripturl;

	$request = $smcFunc['db_query']('', '
		SELECT
			mem.real_name, mem.member_name, mem.id_member, mem.show_online,mem.avatar,
			IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType
		FROM {db_prefix}members AS mem
		LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type != 3)
		WHERE mem.id_member IN ({array_int:ids})',
		array('ids' => $ids)
	);

	$avy = array();
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$avy[$row['id_member']] = $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? '<img ' . (in_array($row['id_member'], $user_info['buddies']) ? 'class="buddyoverlay"' : '' ). ' src="' . (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) . '" alt="&nbsp;"  />' : '') : (stristr($row['avatar'], 'http://') ? '<img ' . (in_array($row['id_member'], $user_info['buddies']) ? 'class="buddyoverlay"' : '' ). ' src="' . $row['avatar'] . '" alt="&nbsp;" />' : '<img ' . (in_array($row['id_member'], $user_info['buddies']) ? 'class="buddyoverlay"' : '' ). ' src="' . $modSettings['avatar_url'] . '/' . $smcFunc['htmlspecialchars']($row['avatar']) . '" alt="&nbsp;" />');

		$smcFunc['db_free_result']($request);
	}
	return $avy;
}
function TPortal_tpmodulebox($blockid)
{
	global $context;

	// fetch the correct block
	if(!empty($context['TPortal']['moduleid']))
	{
		$tpm = $context['TPortal']['moduleid'];
		if(!empty($context['TPortal']['tpmodules']['blockrender'][$tpm]['function']) && function_exists($context['TPortal']['tpmodules']['blockrender'][$tpm]['function']))
			call_user_func($context['TPortal']['tpmodules']['blockrender'][$tpm]['function']);
	}
}

// php blocktype
function TPortal_phpbox()
{
	global $context;

	// execute what is in the block, no echoing
	if(!empty($context['TPortal']['phpboxbody']));
		eval(tp_convertphp($context['TPortal']['phpboxbody'],true));
}

// an article
function TPortal_articlebox()
{
	global $context;

	if(isset($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]))
		echo '<div class="block_article">', 	template_blockarticle() , '</div>';
}

// php blocktype
function TPortal_scriptbox()
{
	global $context;

    echo $context['TPortal']['scriptboxbody'];
}

// TPortal recent topics block
function TPortal_recentbox()
{
	global $scripturl, $context, $settings, $txt, $modSettings;

    // is it a number?
	if(!is_numeric($context['TPortal']['recentboxnum']))
		$context['TPortal']['recentboxnum']='10';

	// leave out the recycle board, if any
	if(isset($modSettings['recycle_board']))
		$bb = $modSettings['recycle_board'];
	else
		$bb = 0;

	if($context['TPortal']['useavatar'] == 0)
	{
	$what = ssi_recentTopics($num_recent = $context['TPortal']['recentboxnum'] , $exclude_boards = array($bb),  $output_method = 'array');

		// Output the topics
		echo '
		<ul class="recent_topics" style="' , isset($context['TPortal']['recentboxscroll']) && $context['TPortal']['recentboxscroll'] == 1 ? 'overflow: auto; height: 20ex;' : '' , 'margin: 0; padding: 0;">';
		$coun = 1;
		foreach($what as $wi => $w)
		{
			echo '
			<li' , $coun<count($what) ? '' : ' style="border: none; margin-bottom: 0;padding-bottom: 0;"'  , '>
				<a href="' . $w['href'] . '" title="' . $w['subject'] . '">' . $w['short_subject'] . '</a>
				 ', $txt['by'], ' <b>', $w['poster']['link'],'</b> ';
			if(!$w['new'])
				echo ' <a href="'.$w['href'].'"><img src="'. $settings['images_url'].'/'.$context['user']['language'].'/new.gif" alt="new" /></a> ';

			echo '<br /><span class="smalltext">['.$w['time'].']</span>
			</li>';
			$coun++;
		}
		echo '
		</ul>';
	}
	else
	{
		$what = tp_recentTopics($num_recent = $context['TPortal']['recentboxnum'], $exclude_boards = array($bb), 'array');

		// Output the topics
		$coun = 1;
		echo '
		<ul class="recent_topics" style="' , isset($context['TPortal']['recentboxscroll']) && $context['TPortal']['recentboxscroll']==1 ? 'overflow: auto; height: 20ex;' : '' , 'margin: 0; padding: 0;">';
		foreach($what as $wi => $w)
		{
			echo '
			<li' , $coun<count($what) ? '' : ' style="border: none; margin-bottom: 0;padding-bottom: 0;"'  , '>
					<span class="tpavatar"><a href="' . $scripturl. '?action=profile;u=' . $w['poster']['id'] . '">' , empty($w['poster']['avatar']) ? '<img src="' . $settings['tp_images_url'] . '/TPguest.png" alt="" />' : $w['poster']['avatar'] , '</a></span><a href="'.$w['href'].'">' . $w['short_subject'].'</a>
				 ', $txt['by'], ' <b>', $w['poster']['link'],'</b> ';
			if(!$w['new'])
				echo ' <a href="'.$w['href'].'"><img src="'. $settings['images_url'].'/'.$context['user']['language'].'/new.gif" alt="new" /></a> ';

			echo '<br /><span class="smalltext">['.$w['time'].']</span>
			</li>';
			$coun++;
		}
		echo '
		</ul>';
	}
}

// TPortal categories
function TPortal_catmenu()
{
	global $context, $scripturl, $boardurl;

	if(isset($context['TPortal']['menu'][$context['TPortal']['menuid']]) && !empty($context['TPortal']['menu'][$context['TPortal']['menuid']])){
		echo '
	<ul class="tp_catmenu">';
		
		foreach($context['TPortal']['menu'][$context['TPortal']['menuid']] as $cn)
		{
			echo '
		<li', $cn['type']=='head' ? ' class="tp_catmenu_header"' : '' ,'>';
			if($context['TPortal']['menuvar1'] == '' || $context['TPortal']['menuvar1'] == '0')
				echo str_repeat("&nbsp;&nbsp;", ($cn['sub'] + 1));
			elseif($context['TPortal']['menuvar1'] == '1')
				echo str_repeat("&nbsp;&nbsp;", ($cn['sub'] + 1));
			elseif($context['TPortal']['menuvar1'] == '2')
				echo str_repeat("&nbsp;&nbsp;", ($cn['sub'] + 1));

			if((!isset($cn['icon']) || (isset($cn['icon']) && $cn['icon'] == '')) && $cn['type'] != 'head' && $cn['type'] != 'spac')
			{
				if($context['TPortal']['menuvar1'] == '' || $context['TPortal']['menuvar1'] == '0')
					echo '
			<img src="'.$boardurl.'/tp-images/icons/TPdivider2.gif" alt="" />&nbsp;';
				elseif($context['TPortal']['menuvar1'] == '1' && $cn['sub'] == 0)
					echo '
			<img src="'.$boardurl.'/tp-images/icons/bullet3.gif" alt="" />';
			
			}
			elseif(isset($cn['icon']) && $cn['icon'] != '' && $cn['type'] != 'head' && $cn['type'] != 'spac')
			{
				echo '
			<img alt="*" src="'.$cn['icon'].'" />&nbsp;';
			}
			switch($cn['type'])
			{
				case 'cats' :
					echo '
			<a href="'. $scripturl. '?cat='.$cn['IDtype'].'"' .( $cn['newlink']=='1' ? ' target="_blank"' : ''). '>'.$cn['name'].'</a>';
					break;
				case 'arti' :
					echo '
			<a href="'. $scripturl. '?page='.$cn['IDtype'].'"' .($cn['newlink']=='1' ? ' target="_blank"' : '') . '>'.$cn['name'].'</a>';
					break;
				case 'link' :
					echo '
			<a href="'.$cn['IDtype'].'"' . ($cn['newlink']=='1' ? ' target="_blank"' : '') . '>'.$cn['name'].'</a>';
					break;
				case 'head' :
					echo '
			<a class="tp_catmenu_header" name="header'.$cn['id'].'"><b>'.$cn['IDtype'].'</b></a>';
					break;
				case 'spac' :
					echo '
			<a name="spacer'.$cn['id'].'">&nbsp;</a>';
					break;
				default :
					echo '
			<a href="'.$cn['IDtype'].'"' . ($cn['newlink']=='1' ? ' target="_blank"' : '') . '>'.$cn['name'].'</a>';
					break;
			}
			echo '</li>';
		}
		echo '
	</ul>';
	}
}

// dummy for old templates
function TPortal_sidebar()
{
	return;
}

// Tportal userbox
function TPortal_userbox()
{
	global $context, $settings, $scripturl, $txt;

	$bullet = '<img src="'.$settings['tp_images_url'].'/TPdivider.gif" alt="" style="margin:0 4px 0 0;" />';
	$bullet2 = '<img src="'.$settings['tp_images_url'].'/TPdivider2.gif" alt="" style="margin:0 4px 0 0;" />';
	$bullet3 = '<img src="'.$settings['tp_images_url'].'/TPdivider3.gif" alt="" style="margin:0 4px 0 0;" />';
	$bullet4 = '<img src="'.$settings['tp_images_url'].'/TPmodule2.gif" alt="" style="margin:0 4px 0 0;" />';
	$bullet5 = '<img src="'.$settings['tp_images_url'].'/TPmodule2.gif" alt=""  style="margin:0 4px 0 0;" />';

	echo'
	<div class="tp_userblocknew">';


	// If the user is logged in, display stuff like their name, new messages, etc.
	
	if ($context['user']['is_logged'])
	{
		
		if (!empty($context['user']['avatar']) &&  isset($context['TPortal']['userbox']['avatar']))
			echo '
				<span class="tpavatar">', $context['user']['avatar']['image'], '</span>';
		echo '
		<strong>', $context['user']['name'], '</strong>
		<ul class="reset">';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm'])
		{
			echo '
			<li><a href="', $scripturl, '?action=pm">' .$bullet.$txt['tp-pm'].' ',  $context['user']['messages'], '</a></li>';
			if($context['user']['unread_messages'] > 0)
				echo '
			<li style="font-weight: bold; "><a href="', $scripturl, '?action=pm">' . $bullet. $txt['tp-pm2'].' ',$context['user']['unread_messages'] , '</a></li>';
		}
		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
				<li><a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve;' . $context['session_var'] . '=' . $context['session_id'].'">'. $bullet. $txt['tp_unapproved_members'].' '. $context['unapproved_members']  . '</a></li>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
				<li><a href="', $scripturl, '?action=moderate;area=reports">'.$bullet.$txt['tp_modreports'].' ' . $context['open_mod_reports']. '</a></li>';

		if(isset($context['TPortal']['userbox']['unread']))
			echo '
			<li><hr><a href="', $scripturl, '?action=unread">' .$bullet.$txt['tp-unread'].'</a></li>
			<li><a href="', $scripturl, '?action=unreadreplies">'.$bullet.$txt['tp-replies'].'</a></li>
			<li><a href="', $scripturl, '?action=profile;u='.$context['user']['id'].';area=showposts">'.$bullet.$txt['tp-showownposts'].'</a></li>
			<li><a href="', $scripturl, '?action=tpmod;sa=showcomments">'.$bullet.$txt['tp-showcomments'].'</a><hr></li>
			';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
			<li>' .$bullet2.$txt['tp_maintenace']. '</li>';
		// Show the total time logged in?
		if (!empty($context['user']['total_time_logged_in']) && isset($context['TPortal']['userbox']['logged']))
		{
			echo '
			<li>' .$bullet2.$txt['tp-loggedintime'] . '</li>
			<li>'.$bullet2.$context['user']['total_time_logged_in']['days'] . $txt['tp-acronymdays']. $context['user']['total_time_logged_in']['hours'] . $txt['tp-acronymhours']. $context['user']['total_time_logged_in']['minutes'] .$txt['tp-acronymminutes'].'</li>';
		}
		if (isset($context['TPortal']['userbox']['time']))
		echo '
			<li>' . $bullet2.$context['current_time'].' <hr></li>';
		
		// admin parts etc.
         if(!isset($context['TPortal']['can_submit_article']))
            $context['TPortal']['can_submit_article']=0;
		
		// can we submit an article?
       	if(allowedTo('tp_submithtml'))
			echo '
		<li><a href="', $scripturl, '?action=tp' . (allowedTo('tp_articles') ? 'admin' : 'mod') . ';sa=addarticle_html">' . $bullet3.$txt['tp-submitarticle']. '</a></li>';
       	if(allowedTo('tp_submitbbc'))
					echo '
		<li><a href="', $scripturl, '?action=tp' . (allowedTo('tp_articles') ? 'admin' : 'mod') . ';sa=addarticle_bbc">' . $bullet3.$txt['tp-submitarticlebbc']. '</a></li>';
       	
		if(allowedTo('tp_editownarticle'))
					echo '
		<li><a href="', $scripturl, '?action=tpmod;sa=myarticles">' . $bullet3.$txt['tp-myarticles']. '</a></li>';
		
		// upload a file?
        if(allowedTo('tp_dlupload') || allowedTo('tp_dlmanager'))
             echo '
			<li><a href="', $scripturl, '?action=tpmod;dl=upload">' . $bullet3.$txt['permissionname_tp_dlupload']. '</a></li>';

		// tpadmin checks
		if (allowedTo('tp_settings'))
			echo '
			<li><a href="' . $scripturl . '?action=tpadmin;sa=settings">' . $bullet4.$txt['permissionname_tp_settings'] . '</a></li>';
		if (allowedTo('tp_blocks'))
					echo '
			<li><a href="' . $scripturl . '?action=tpadmin;sa=blocks">' . $bullet4.$txt['permissionname_tp_blocks'] . '</a></li>';
		if (allowedTo('tp_articles'))
		{
					echo '
			<li><a href="' . $scripturl . '?action=tpadmin;sa=articles">' . $bullet4.$txt['permissionname_tp_articles'] . '</a></li>';
					// any submissions?
					if($context['TPortal']['submitcheck']['articles']>0)
						echo '
			<li><a href="' . $scripturl . '?action=tpadmin;sa=submission"><b>' . $bullet4.$context['TPortal']['submitcheck']['articles'] . ' ' .$txt['tp-articlessubmitted'] . '</b></a></li>';
		}
		if (allowedTo('tp_dlmanager'))
		{
					echo '
			<li><a href="' . $scripturl . '?action=tpmod;dl=admin">' . $bullet5.$txt['permissionname_tp_dlmanager'] . '</a></li>';
					// any submissions?
					if($context['TPortal']['submitcheck']['uploads']>0)
						echo '
			<li><a href="' . $scripturl . '?action=tpmod;dl=adminsubmission"><b>' . $bullet5.$context['TPortal']['submitcheck']['uploads'] . ' ' .$txt['tp-dluploaded'] . '</b></a></li>';
		}

		// add adminhooks
		if(sizeof($context['TPortal']['tpmodules']['adminhook']) > 0)
		{
			foreach($context['TPortal']['tpmodules']['adminhook'] as $link)
				echo '<li><a href="' . $scripturl . '?'.$link['action'].'">' . $bullet5.$link['title']. '</a></li>';
		}
		
		echo '
		</ul>';
	}
	// Otherwise they're a guest - so politely ask them to register or login.
	else{
		echo '
		<div style="line-height: 1.4em;">', $bullet , sprintf($txt['welcome_guest'], $txt['guest_title']), '<br />', $bullet2, $context['current_time'], '</div>
		<form style="margin-top: 5px;" action="', $scripturl, '?action=login2" method="post" >
			<input type="text" name="user" size="10" /> <input type="password" name="passwrd" size="10" /><br />
			<select name="cookielength">
				<option value="60">', $txt['one_hour'], '</option>
				<option value="1440">', $txt['one_day'], '</option>
				<option value="10080">', $txt['one_week'], '</option>
				<option value="302400">', $txt['one_month'], '</option>
				<option value="-1" selected="selected">', $txt['forever'], '</option>
			</select>
			<input type="submit" value="', $txt['login'], '" />
		</form>
		<div style="line-height: 1.4em;">', $txt['quick_login_dec'], '</div>
		<br />';
	}
	echo '
	</div>';
}

// TPortal themebox
function TPortal_themebox()
{
	global $context, $settings, $scripturl, $txt, $smcFunc;

	$what = explode(',', $context['TPortal']['themeboxbody']);
	$temaid = array();
	$temanavn = array();
	$temapaths = array();
	foreach($what as $wh => $wht)
	{
		$all = explode('|', $wht);	
		if($all[0] > -1)
		{
			$temaid[] = $all[0];
			$temanavn[] = isset($all[1]) ? $all[1] : '';
			$temapaths[] = isset($all[2]) ? $all[2] : '';
		}
	}
	
	if(isset($context['TPortal']['querystring']))
		$tp_where = $smcFunc['htmlspecialchars'](strip_tags($context['TPortal']['querystring']));
	else
		$tp_where = 'action=forum';

	if($tp_where != '')
		$tp_where .= ';';

	// remove multiple theme=x in the string.
	$tp_where=preg_replace("'theme=[^>]*?;'si", "", $tp_where);

	 if(sizeof($temaid) > 0){
        echo '
		<form name="jumpurl1" onsubmit="return jumpit()" class="middletext" action="" style="padding: 0; margin: 0; text-align: center;">
			<select style="width: 100%; margin: 5px 0px 5px 0px;" size="1" name="jumpurl2" onchange="check(this.value)">';
         for($a=0 ; $a<(sizeof($temaid)); $a++)
		 {
                echo '
				<option value="'.$temaid[$a].'" ', $settings['theme_id'] == $temaid[$a] ? 'selected="selected"' : '' ,'>'.substr($temanavn[$a],0,20).'</option>';
         }
         echo '
			</select><br />' , $context['user']['is_logged'] ?
			'<input type="checkbox" value=";permanent" onclick="realtheme()" /> '. $txt['tp-permanent']. '<br />' : '' , '<br>
			<input class="button_submit" type="button" value="'.$txt['tp-changetheme'].'" onclick="jumpit()" /><br><br>
 			<input type="hidden" value="'.$smcFunc['htmlspecialchars']($scripturl . '?'.$tp_where.'theme='.$settings['theme_id']).'" name="jumpurl3" />
 			<div style="text-align: center; width: 95%; overflow: hidden;">
				<img src="'.$settings['images_url'].'/thumbnail.gif" alt="" id="chosen" name="chosen" style="width: 100%;" />
			</div>
		</form>
		<script type="text/javascript"><!-- // --><![CDATA[
			var extra = \'\';
			var themepath = new Array();';
         for($a=0 ; $a<(sizeof($temaid)); $a++){
			 echo '
			    themepath['.$temaid[$a].'] = "'.$temapaths[$a].'/thumbnail.gif";
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
				document.jumpurl1.jumpurl3.value = \'' . $scripturl . '?'. $tp_where.'theme=\' + icon 
			}
		// ]]></script>';
	}
	else
		echo $txt['tp-nothemeschosen'];
}

// TPortal newsbox
function TPortal_newsbox()
{
    global $context, $settings;

	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
	echo '
	<div class="tp_newsblock">', $context['random_news_line'], '</div>';
}

// TPortal stats box
function TPortal_statsbox()
{
	global $context, $settings, $scripturl, $txt, $modSettings;

	$bullet = '<img src="'.$settings['tp_images_url'].'/TPdivider.gif" alt=""  style="margin:0 4px 0 0;" />';
	$bullet2 = '<img src="'.$settings['tp_images_url'].'/TPdivider2.gif" alt="" style="margin:0 4px 0 0;" />';
	
	echo'
	<div class="tp_statsblock">';

	if(isset($context['TPortal']['userbox']['stats']))
		// members stats
		echo '
		<h5 class="mlist"><a href="'.$scripturl.'?action=mlist">'.$txt['members'].'</a></h5>
		<ul>
			<li>' . $bullet. $txt['total_members'].': ' , isset($modSettings['memberCount']) ? $modSettings['memberCount'] : $modSettings['totalMembers'] , '</li>
			<li>' . $bullet. $txt['tp-latest']. ': <a href="', $scripturl, '?action=profile;u=', $modSettings['latestMember'], '"><strong>', $modSettings['latestRealName'], '</strong></a></li>
		</ul>';
	if(isset($context['TPortal']['userbox']['stats_all']))
		// more stats
		echo '
		<h5 class="stats"><a href="'.$scripturl.'?action=stats">'.$txt['tp-stats'].'</a></h5>
		<ul>
			<li>'.  $bullet. $txt['total_posts'].': '.$modSettings['totalMessages']. '</li>
			<li>'.  $bullet. $txt['total_topics'].': '.$modSettings['totalTopics']. '</li>
			<li>' . $bullet. $txt['tp-mostonline-today'].': '.$modSettings['mostOnlineToday'].'</li>
			<li>' . $bullet. $txt['tp-mostonline'].': '.$modSettings['mostOnline'].'</li>
			<li>('.timeformat($modSettings['mostDate']).')</li>
		</ul>';

	if(isset($context['TPortal']['userbox']['online']))
	{
		// add online users
		echo '
		<h5 class="online"><a href="'.$scripturl.'?action=who">'.$txt['online_users'].'</a></h5>
		<div class="tp_stats_users" style="line-height: 1.3em;">';
		
		$online = ssi_whosOnline('array');
		echo  $bullet. $txt['tp-users'].': '.$online['num_users']. '<br />
			'. $bullet. $txt['tp-guests'].': '.$online['guests'].'<br />
			'. $bullet. $txt['tp-total'].': '.$online['total_users'].'<br />
			<div style="max-height: 23em; overflow: auto;">';

		foreach($online['users'] as $user)
		{
			echo  $bullet2 , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];
			echo '<br />';
		}
		echo '
			</div></div>';
	}
	echo '
	</div>';
}

// TPortal ssi box
function TPortal_ssi()
{
       global $context;
       echo '
	<div style="padding: 5px;" class="smalltext">';
       if($context['TPortal']['ssifunction'] == 'topboards')
           ssi_topBoards();
       elseif($context['TPortal']['ssifunction'] == 'topposters')
           ssi_topPoster(5);
       elseif($context['TPortal']['ssifunction'] == 'topreplies')
           ssi_topTopicsReplies();
       elseif($context['TPortal']['ssifunction'] == 'topviews')
           ssi_topTopicsViews();
       elseif($context['TPortal']['ssifunction'] == 'calendar')
          ssi_todaysCalendar();

       echo '
    </div>';
}
// TPortal module
function TPortal_module()
{
   global $context, $scripturl, $txt;

	switch($context['TPortal']['moduleblock'])
	{
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
			$it = array();
			$it = dl_recentitems('1', 'date', 'array');
			if(sizeof($it) > 0)
			{
				foreach($it as $item)
				{
					echo '
					<img src="'.$item['icon'].'" align="right" style="margin-left: 4px; " alt="" />
						<a href="'.$item['href'].'"><b>'.$item['name'].'</b></a>
						<p class="smalltext">'.$txt['tp-uploadedby'].' <b>'.$item['author'].'</b> <br />( '.$item['date'].')<br />
						'.$txt['tp-downloads'].'/'.$txt['tp-itemviews'].': <b>'.$item['downloads'].' / '.$item['views'].'</b></p>';
				}
			}
			break;
		case 'dl-stats5':
			$it = array();
			$it = dl_recentitems('1', 'downloads', 'array');
			if(sizeof($it) > 0)
			{
				foreach($it as $item)
				{
					echo '
					<img src="'.$item['icon'].'" align="right" style="margin-left: 4px; " alt="" />
						<a href="'.$item['href'].'"><b>'.$item['name'].'</b></a>
						<p class="smalltext">'.$txt['tp-uploadedby'].' <b>'.$item['author'].'</b> <br />( '.$item['date'].')<br />
						'.$txt['tp-downloads'].'/'.$txt['tp-itemviews'].': <b>'.$item['downloads'].' / '.$item['views'].'</b></p>';
				}
			}
			break;
		case 'dl-stats6':
			$it = array();
			$it = dl_recentitems('1', 'views', 'array');
			if(sizeof($it) > 0)
			{
				foreach($it as $item)
				{
					echo '
					<img src="'.$item['icon'].'" align="right" style="margin-left: 4px; " alt="" />
						<a href="'.$item['href'].'"><b>'.$item['name'].'</b></a>
						<p class="smalltext">'.$txt['tp-uploadedby'].' <b>'.$item['author'].'</b> <br />( '.$item['date'].')<br />
						'.$txt['tp-downloads'].'/'.$txt['tp-itemviews'].': <b>'.$item['downloads'].' / '.$item['views'].'</b></p>';
				}
			}
			break;
		case 'dl-stats7':
			$it = array();
			$it = art_recentitems('5','date');
			if(sizeof($it) > 0)
			{
				foreach($it as $item)
				{
					echo '<span class="smalltext"><a title="'.$item['date'].'" href="'.$scripturl.'?page='.$item['id'].'">'.$item['subject'].'</a>
						</span><br />';
				}
			}
			break;
		case 'dl-stats8':
			$it = array();
			$it = art_recentitems('5', 'views');
			if(sizeof($it) > 0)
			{
				foreach($it as $item)
				{
					echo '<span class="smalltext"><a title="'.$item['views'].' '.$txt['tp-views'].'" href="'.$scripturl.'?page='.$item['id'].'">'.$item['subject'].'</a>
						</span><br />';
				}
			}
			break;
		case 'dl-stats9':
			$it = array();
			$it = art_recentitems('5', 'comments');
			if(sizeof($it) > 0)
			{
				foreach($it as $item)
				{
					echo '<span class="smalltext"><a title="'.$item['comments'].'" href="'.$scripturl.'?page='.$item['id'].'">'.$item['subject'].'</a>
						('.$item['comments'].')<br /></span>';
				}
			}
				break;
     }
}
// Tportal RSS block
function TPortal_rss()
{
	global $context;
	
	echo '<div style="padding: 5px; ' , !empty($context['TPortal']['rsswidth']) ? 'max-width: ' . $context['TPortal']['rsswidth'] .';' : '' , '" class="middletext">' , TPparseRSS('', $context['TPortal']['rss_utf8']) , '</div>';
}

// Tportal sitemap menu
function TPortal_sitemap()
{
    global $context, $settings, $scripturl, $txt;

	$current = '';
    // check where we are
    if(isset($_GET['action']) && $_GET['action'] == 'tpmod')
	{
		if(isset($_GET['dl']))
			$current = 'dl';
		elseif(isset($_GET['link']))
			$current = 'link';
		elseif(isset($_GET['show']))
			$current = 'show';
		elseif(isset($_GET['team']))
			$current = 'team';
		else
			$current = '';
    }
         echo '
	<div class="tborder">
		<ul class="tpsitemap">';
	if($context['TPortal']['show_download'] == '1')
		echo '<li><a class="tpsitemapheader" href="'.$scripturl.'?action=tpmod;dl"><img src="' .$settings['tp_images_url']. '/TPmodule2.gif" border="0" alt="" /> '.$txt['tp-downloads'].'</a></li>';

	if(!empty($context['TPortal']['sitemap']) && !empty($context['TPortal']['menu']))
	{
		foreach($context['TPortal']['menu'] as $main)
		{
			foreach($main as $cn)
			{
				// check if we can find the link on current tpage
				$catclass = '';
				if($cn['type'] == 'cats')
				{
					if(isset($_GET['cat']) && $cn['IDtype'] == $_GET['cat'])
						$catclass = 'tpsitemapheader';
				}
				elseif($cn['type'] == 'arti'){
					if(isset($_GET['page']) && $cn['IDtype'] == $_GET['page'])
						$catclass = 'tpsitemapheader';
				}
				elseif($cn['type'] == 'link'){
					if(!empty($context['TPortal']['querystring']))
						$qs = $scripturl.'?'.$context['TPortal']['querystring'];
					else
						$qs = $scripturl;

					if($qs == $cn['IDtype'])
						$catclass = 'tpsitemapheader';
				}

				if($cn['sitemap'] == '1'){
					switch($cn['type']){
							case 'cats' :
								echo '<li><a class="' , $catclass ,'" href="'. $scripturl. '?cat='.$cn['IDtype'].'" ' , $cn['newlink']=='1' ? 'target="_blank"' : '' , '><img src="' .$settings['tp_images_url']. '/TPdivider.gif" border="0" alt="" /> '.$cn['name'].'</a></li>';
								break;
							case 'arti' :
								echo '<li><a class="' , $catclass ,'" href="'. $scripturl. '?page='.$cn['IDtype'].'"' , $cn['newlink']=='1' ? 'target="_blank"' : '' , '><img src="' .$settings['tp_images_url']. '/TPdivider.gif" border="0" alt="" /> '.$cn['name'].'</a></li>';
								break;
							case 'link' :
								echo '<li><a class="' , $catclass ,'" href="'.$cn['IDtype'].'"' , $cn['newlink']=='1' ? 'target="_blank"' : '' , '><img src="' .$settings['tp_images_url']. '/TPdivider.gif" border="0" alt="" /> '.$cn['name'].'</a></li>';
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

// category listing blocktype
function TPortal_categorybox()
{
    global $context, $txt, $scripturl;

	if(isset($context['TPortal']['blockarticle_titles'][$context['TPortal']['blocklisting']])){
		echo '<div class="middletext" ', (sizeof($context['TPortal']['blockarticle_titles'][$context['TPortal']['blocklisting']])>$context['TPortal']['blocklisting_height'] && $context['TPortal']['blocklisting_height']!='0') ? ' style="overflow: auto; width: 100%; height: '.$context['TPortal']['blocklisting_height'].'em;"' : '' ,'>';
		foreach($context['TPortal']['blockarticle_titles'][$context['TPortal']['blocklisting']] as $listing){
			if($listing['category'] == $context['TPortal']['blocklisting'])
				echo '<b><a href="'.$scripturl.'?page='.$listing['shortname'].'">'.$listing['subject'].'</a></b> ' , $context['TPortal']['blocklisting_author']=='1' ? $txt['by'].' '.$listing['poster'] : '' , '<br />';
		}
		echo '</div>';
	}
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
	
	if($context['TPortal']['show_download']==0)
	{
		echo '
	<div style="padding: 0 5px;">
		<div class="cat_bar">
			<h3 class="catbg">' , $txt['tp-searcharticles'] , '</h3>
		</div>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
			<p style="margin: 0; padding: 0 1em;">
				<a href="' . $scripturl. '?action=tpmod;sa=searcharticle">' . $txt['tp-searcharticles2'] . '</a>';
	}			
	else

 echo '
	<div style="padding: 0 5px;">
		<div class="cat_bar">
			<h3 class="catbg">' , $txt['tp-searcharticles'] , '</h3>
		</div>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
			<p style="margin: 0; padding: 0 1em;">
				<a href="' . $scripturl. '?action=tpmod;sa=searcharticle">' . $txt['tp-searcharticles2'] . '</a> |
				<a href="' . $scripturl. '?action=tpmod;dl=search">' . $txt['tp-searchdownloads'] . '</a>';


	// any others?
	if(!empty($context['TPortal']['searcharray']) && count($context['TPortal']['searcharray']) > 0)
		echo implode(' | ', $context['TPortal']['searcharray']);

	echo '
			</p>
			<span class="botslice"><span></span></span>
		</div>
	</div>';

}
function template_TPsearch_below()
{
	return;
}

function template_tperror_above()
{
	global $context;

	echo '
	<div class="title_bar">
		<h3 class="titlebg"><span class="left"></span><span class="error">'.$context['TPortal']['tperror'].'</span></h3>
	</div>';

}
function template_notpublished()
{
	global $context;
	echo '
<div style="padding-bottom: 4px;">
	<span class="clear upperframe"><span></span></span>
	<div class="roundframe"><div class="innerframe">
		<div style="line-height: 1.5em; text-align: center;">'.$context['TPortal']['tperror'].'</div>
	</div></div>
	<span class="lowerframe"><span></span></span>
</div>';

}
function template_tperror_below()
{
	return;
}
function template_tpnotify_above()
{
	global $context;

	echo '<div style="color: green; padding: 1em; background-color: #fdfffd; border: 2px solid; margin-bottom: 1em;">
			<div style="padding: 1em;">'.$context['TPortal']['tpnotify'].'</div>
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

	if(!empty($context['TPortal']['tptabs']))
	{
		$buts = array(); 
		echo '
	<div class="tptabs">';
		foreach($context['TPortal']['tptabs'] as $tab)
			$buts[] = '<a' . ($tab['is_selected'] ? ' class="tpactive"' : '') . ' href="' . $tab['href'] . '">' . $tab['title'] . '</a>';

		echo implode(' | ', $buts) , '
	</div>';
	}
}

function template_tptabs_below() 
{
	global $context;

}

function TPblock($block, $theme, $side, $double=false)
{
	global $context , $scripturl, $settings, $txt;

	// setup a container that can be massaged through css
	echo '
	<div class="block_' . $side . 'container">';
	
	if(function_exists('ctheme_tp_getblockstyles'))
		$types = ctheme_tp_getblockstyles();
	else
		$types = tp_getblockstyles();

	// check
	if($block['var4'] == '')
		$block['var4'] = 0;

	if($block['var4'] == 0)
		$block['var4'] = $context['TPortal']['panelstyle_'.$side];	

	// its a normal block..
	if(in_array($block['frame'],array('theme', 'frame', 'title', 'none')))
	{
		echo	'
	<div class="', (($theme || $block['frame'] == 'frame') ? 'tborder tp_'.$side.'block_frame' : 'tp_'.$side.'block_noframe'), '">';

		// show the frame and title
		if ($theme || $block['frame'] == 'title')
		{
			echo $types[$block['var4']]['code_title_left'];

			if($block['visible'] == '' || $block['visible'] == '1')
				echo '<a href="javascript: void(0); return false" onclick="toggle(\''.$block['id'].'\'); return false"><img id="blockcollapse'.$block['id'].'" style="margin: 8px 0 0 0; " align="right" src="' .$settings['tp_images_url']. '/' , !in_array($block['id'],$context['TPortal']['upshrinkblocks'])  ? 'TPcollapse' : 'TPexpand' , '.gif" border="0" alt="" title="'.$txt['block-upshrink_description'].'" /></a>';

			// can you edit the block?
			if($block['can_edit'] && !$context['TPortal']['blocks_edithide'])
				echo '<a href="',$scripturl,'?action=tpmod;sa=editblock'.$block['id'].';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="margin: 8px 4px 0 0;" border="0" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';
			elseif($block['can_manage'] && !$context['TPortal']['blocks_edithide'])
				echo '<a href="',$scripturl,'?action=tpadmin;blockedit='.$block['id'].';' . $context['session_var'] . '=' . $context['session_id'].'"><img border="0" style="margin: 8px 4px 0 0;" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';

			echo $block['title'];
			echo $types[$block['var4']]['code_title_right'];
		}
		else
		{
			if(($block['visible'] == '' || $block['visible'] == '1') && $block['frame'] != 'frame')
			{
				echo '
		<div style="padding: 4px;">';
				if($block['visible'] == '' || $block['visible'] == '1')
					echo '<a href="javascript: void(0); return false" onclick="toggle(\''.$block['id'].'\'); return false"><img id="blockcollapse'.$block['id'].'" style="margin: 0;" align="right" src="' .$settings['tp_images_url']. '/' , !in_array($block['id'],$context['TPortal']['upshrinkblocks']) ? 'TPcollapse' : 'TPexpand' , '.gif" border="0" alt="" title="'.$txt['block-upshrink_description'].'" /></a>';
				echo '&nbsp;
		</div>';
			}
		}
		echo '
		<div class="', (($theme || $block['frame'] == 'frame') ? 'tp_'.$side.'block_body' : ''), '"', in_array($block['id'],$context['TPortal']['upshrinkblocks']) ? ' style="display: none;"' : ''  , ' id="block'.$block['id'].'">';
		if($theme || $block['frame'] == 'frame')	
			echo $types[$block['var4']]['code_top'];

		$func = 'TPortal_' . $block['type'];
		if (function_exists($func))
		{
			if($double)
			{
				// figure out the height
				$h = $context['TPortal']['blockheight_'.$side];
				if(substr($context['TPortal']['blockheight_'.$side],strlen($context['TPortal']['blockheight_'.$side])-2,2) == 'px')
					$nh = ((substr($context['TPortal']['blockheight_'.$side],0,strlen($context['TPortal']['blockheight_'.$side])-2)*2) + 43).'px';
				elseif(substr($context['TPortal']['blockheight_'.$side],strlen($context['TPortal']['blockheight_'.$side])-1,1) == '%')
					$nh = (substr($context['TPortal']['blockheight_'.$side],0,strlen($context['TPortal']['blockheight_'.$side])-1)*2).'%';
			}
			echo '<div class="blockbody" style="overflow: auto;' , !empty($context['TPortal']['blockheight_'.$side]) ? 'height: '. ($double ? $nh : $context['TPortal']['blockheight_'.$side]) .';' : '' , '">';
			$func($block['id']);
			echo '</div>';
		}
		else
			echo '<div class="blockbody" style="overflow: auto;' , !empty($context['TPortal']['blockheight_'.$side]) ? 'height: '.$context['TPortal']['blockheight_'.$side].';' : '' , '">' , parse_bbc($block['body']) , '</div>';

		if($theme || $block['frame'] == 'frame')	
			echo $types[$block['var4']]['code_bottom'];
		echo '
		</div>
	</div>';
	}
	// use a pre-defined layout
	else
	{
		// check if the layout actually exist
		if(!isset($context['TPortal']['blocktheme'][$block['frame']]['body']['before']))
			$context['TPortal']['blocktheme'][$block['frame']] = array(
				'frame' => array('before' => '', 'after' => ''),
				'title' => array('before' => '', 'after' => ''),
				'body' => array('before' => '', 'after' => '')
			);
				
		echo $context['TPortal']['blocktheme'][$block['frame']]['frame']['before'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['title']['before'];

		// can you edit the block?
		if($block['can_edit'] && !$context['TPortal']['blocks_edithide'])
			echo '<a href="',$scripturl,'?action=tpmod;sa=editblock'.$block['id'].';' . $context['session_var'] . '=' . $context['session_id'].'"><img style="margin-right: 4px;" border="0" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';
		elseif($block['can_manage'] && !$context['TPortal']['blocks_edithide'])
			echo '<a href="',$scripturl,'?action=tpadmin;blockedit'.substr($side,0,1).'='.$block['id'].';' . $context['session_var'] . '=' . $context['session_id'].'"><img border="0" style="margin-right: 4px;" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';

		echo $block['title'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['title']['after'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['body']['before'];

		$func = 'TPortal_' . $block['type'];
		if (function_exists($func))
			$func();
		else
			echo parse_bbc($block['body']);

		echo $context['TPortal']['blocktheme'][$block['frame']]['body']['after'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['frame']['after'];
	}
	echo '
	</div>';
}

// and its built-in types..
function article_renders($type = 1, $single = false, $first = false)
{
	global $context;
	$code = '';
	// decide the header style, different for forumposts
    $useFrame = in_array($context['TPortal']['article']['frame'], array('theme', 'title'));
	$headerstyle = isset($context['TPortal']['article']['boardnews']) ? 'catbg' : 'titlebg';
	$divheader = isset($context['TPortal']['article']['boardnews']) ? 'cat_bar' : 'title_bar';   

	if($type == 1)
	{       
		$code = '
<div style="margin-bottom: 5px; overflow: hidden;">
    ' . ($useFrame ? '<div class="'. $divheader .'">' : '') . '
	   <h3' . ($useFrame ? ' class="' . $headerstyle . '"' : ' class="article_title"') . '>{article_shortdate} {article_title} </h3>
    ' . ($useFrame ? '</div>' : '') . '
	<div' . ($context['TPortal']['article']['frame'] == 'theme' ? ' class="windowbg2" ' : '') . '>
		<div class="article_info' . ($context['TPortal']['article']['frame'] == 'theme' ? '' : '') . '">
		' . (!$single ? '<div class="floatleft">{article_avatar}</div><div style="clear: right;">' : '') .  '
			{article_author} 
			{article_category}
			{article_date} 
			{article_views} 
			{article_rating} 
			{article_options} 
			' . (!$single ? '</div>' : '') .  '
		</div>
		<div class="article_padding article_text" style="clear: both;">{article_text}</div>
		' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '{article_bookmark}' : '') . '
		' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
		' . (!$single ? '<span class="botslice"><span></span></span>' : '') . '
	</div>
		' . ($single ? '
		{article_moreauthor}
		{article_bookmark}
		{article_morelinks}
		{article_comments}' : '') . ' 
</div>
		';
	}
	elseif($type == 2)
	{
		if($first)
			$code = '
<div style="margin-bottom: 5px; overflow: hidden;">
    ' . ($useFrame ? '<div class="'. $divheader .'">' : '') . '
	   <h3' . ($useFrame ? ' class="' . $headerstyle . '"' : ' class="article_title"') . '>{article_shortdate} {article_title} </h3>
    ' . ($useFrame ? '</div>' : '') . '
	<div' . ($context['TPortal']['article']['frame'] == 'theme' ? ' class="windowbg2" ' : '') . '>
		<div class="article_info' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg' : '') . '">
		' . (!$single ? '{article_avatar}' : '') .  '
			{article_author} 
			{article_category}
			{article_date} 
			{article_views} 
			{article_rating} 
			{article_options} 
		</div>
		<div class="article_padding">{article_text}</div>
		' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '<div class="article_padding">{article_bookmark}</div>' : '') . '
		' . (isset($context['TPortal']['article']['boardnews']) ? '<div class="article_padding">{article_boardnews}</div>' : '') . '
		' . (!$single ? '<span class="botslice"><span></span></span>' : '') . '
	</div>
		' . ($single ? '
		{article_moreauthor}
		{article_bookmark}
		{article_morelinks}
		{article_comments}' : '') . ' 
</div><br />
		';
		else
			$code = '
	<div class="article" style="padding: 0 0.5em;">
		<div class="article_iconcolumn">{article_iconcolumn}</div>
		<div class="render2">
			<h3 class="article_title" style="margin-left: 5px;">{article_shortdate} {article_title} </h3>
			<div class="article_info" style="border: none; margin-top: 2px;">
				{article_author}
				{article_category}
				{article_date} 
				{article_views} 
				{article_rating} 
				{article_options} 
			</div>
			<div class="article_padding">{article_text}</div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '<div class="article_padding">{article_bookmark}</div>' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '<div class="article_padding">{article_boardnews}</div>' : '') . '
			' . ($single ? '
			<div class="tp_container">
				<div class="tp_col8">	
					{article_moreauthor}
				</div>
				<div class="tp_col8">
					{article_bookmark}
				</div>
			</div>
			<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg2' : '') . '">{article_morelinks}</div>
			<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg2' : '') . '">{article_comments}</div>' : '') . ' 
		</div>
	</div>';
	}
	elseif($type == 3)
	{
		if(!$first)
			$code = '
		<div style="padding: 2px 1em;"><div class="align_right">{article_date}</div><strong>{article_title}</strong><hr /></div>';
		elseif($single || $first)
			$code = '
	<div class="article" style="padding: 0 0.5em;">
		<div class="article_iconcolumn">{article_iconcolumn}</div>
		<div class="render2">
			<div class="title_bar">
				<h3 class="titlebg article_title" style="padding: 0;margin: 0; border: none;">{article_shortdate} {article_title} </h3>
			</div>
			<div class="article_info" style="border: none; margin-top: 2px;">
				{article_author}
				{article_category}
				{article_date} 
				{article_views} 
				{article_rating} 
				{article_options} 
			</div>
			<div class="article_padding">{article_text}</div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '<div class="article_padding">{article_bookmark}</div>' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '<div class="article_padding">{article_boardnews}</div>' : '') . '
			' . ($single ? '
			<div class="tp_container">
				<div class="tp_col8">	
					{article_moreauthor}
				</div>
				<div class="tp_col8">
					{article_bookmark}
				</div>
			</div>
			<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg2' : '') . '">{article_morelinks}</div>
			<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg2' : '') . '">{article_comments}</div>' : '') . ' 
		</div>
	</div>
		';
	}
	elseif($type == 4)
	{
		$code = '
	<div class="tparticle" style="padding: 0 0.5em; margin-bottom: 1em;">
		<div class="article_picturecolumn">{article_picturecolumn}</div>
		<div class="render4">
			<div class="cat_bar">
				<h3 class="catbg">{article_title} </h3>
			</div>
			<div class="article_info">
		' . (!$single ? '{article_avatar}' : '') .  '
				{article_author}
				{article_category}
				{article_date} 
				{article_views} 
				{article_rating} 
				{article_options} 
			</div>
			<div class="article_padding">{article_text}</div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '{article_bookmark}' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
			' . ($single ? '
			<div class="tp_container">
				<div class="tp_col8">	
					{article_moreauthor}
				</div>
				<div class="tp_col8">
					{article_bookmark}
				</div>
			</div>
			{article_morelinks}
			{article_comments}' : '') . ' 
		</div>
	</div><br />';
	}
	elseif($type == 5)
	{		
		if(!$first)
			$code = '
		<div class="' . $divheader . '">
			<h3 class="' . $headerstyle . '" style="margin-top: 3px; font-weight: normal;">{article_title}</h3>
		</div>';
		else
			$code = '
	<div class="tparticle">
		<div class="' . $divheader . '">
			<h3 class="article_title ' . $headerstyle . '">{article_shortdate} <strong>{article_title}</strong> </h3>
		</div>
		<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? 'windowbg2' : '') . '">
			' . ($context['TPortal']['article']['frame'] == 'theme' ? '<span class="topslice"><span></span></span>' : '') . '
				<div class="article_info">
		' . (!$single ? '{article_avatar}' : '') .  '
					{article_author}
					{article_category}
					{article_date} 
					{article_views} 
					{article_rating} 
					{article_options} 
				</div>
			<div style="padding: 0 5px;">
				<div class="article_padding">{article_text}</div>
					' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '{article_bookmark}' : '') . '
					' . (isset($context['TPortal']['article']['boardnews']) ? '{article_boardnews}' : '') . '
					' . ($single ? '
				<div class="tp_container">
					<div class="tp_col8">	
						{article_moreauthor}
					</div>
					<div class="tp_col8">
						{article_bookmark}
					</div>
				</div>
				{article_morelinks}
				{article_comments}' : '') . ' 
			</div>
			' . ($context['TPortal']['article']['frame'] == 'theme' ? '<span class="botslice"><span></span></span>' : '') . '
		</div>
	</div>
		';
	}
	elseif($type == 6)
	{
		if(!$single)
			$code = '
		<div class="article_title' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg' : '') .'" style="margin: 0 0 3px 0; padding: 1em;">
			<div><strong style="font-size: 105%;">{article_title}</strong></div>
			<div class="catlayout6_text">
				{article_text}
			</div><br />
		</div>';
		else
			$code = '
		<div class="tborder">
			<div class="cat_bar">
				<h3 class="article_title catbg">{article_title}</h3>
			</div>
			<div class="article_info">
		' . (!$single ? '{article_avatar}' : '') .  '
				{article_author}
				{article_date} 
				{article_views} 
				{article_rating} 
				{article_options} 
			</div>
			<div class="article_text">{article_text}</div>
			<div class="tp_container">
				<div class="tp_col8">	
					<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? 'windowbg2' : '') . '">{article_moreauthor}</div>
				</div>
				<div class="tp_col8">
					<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? 'windowbg2' : '') . '">{article_bookmark}</div>
				</div>
			</div>
			<div' . ($context['TPortal']['article']['frame'] == 'theme' ? ' class="windowbg2"' : '') . '>{article_morelinks}</div>
			<div class="article_padding">{article_comments}</div>
		</div>';
	}
	elseif($type == 7)
	{
		$code = $context['TPortal']['frontpage_template'];
	}
	elseif($type == 8)
	{
		$code = '
<div class="tborder" style="margin-bottom: 5px;"> 
	<div class="article' . (isset($context['TPortal']['article']['boardnews']) ? ' windowbg2' : ' windowbg') . '" style="margin: 0;">
	<span class="topslice"><span></span></span>
		<div class="article_picturecolumn smallpad">{article_picturecolumn}</div>
		<div class="render4 smallpad">
			<h2 class="article_title" style="padding-left: 0;">{article_title} </h2>
			<div class="article_info">
		' . (!$single ? '{article_avatar}' : '') .  '
				{article_author}
				{article_category}
				{article_date} 
				{article_views} 
				{article_rating} 
				{article_options} 
			</div>
			<div class="article_padding">{article_text}</div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '<div>{article_bookmark}</div>' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '<div class="article_padding">{article_boardnews}</div>' : '') . '
			' . ($single ? '
			<div class="tp_container">
				<div class="tp_col8">	
					{article_moreauthor}
				</div>
				<div class="tp_col8">
					{article_bookmark}
				</div>
			</div>
			{article_morelinks}
			{article_comments}' : '') . ' 
		</div>
	<span class="botslice"><span></span></span>
	</div>
</div>';
	}
	return $code;
}

/* ********************************************** */
/* these are the prototype functions that can be called from an article template */
function article_edit() { return; }
function article_date($render=true)
{
	global $context;
	
	if(in_array('date',$context['TPortal']['article']['visual_options']))
	echo '
		<span class="article_date"> ' . (timeformat($context['TPortal']['article']['date'])) . '</span>';
	else
		echo '';
}

function article_iconcolumn($render = true)
{
	global $context, $settings;
	
	if(!empty($context['TPortal']['article']['avatar'])) 
		echo '
	<div style="overflow: hidden;">
		' . $context['TPortal']['article']['avatar'] . '
	</div>'; 
	else
		echo '
	<div style="overflow: hidden;">
		<img src="' . $settings['tp_images_url'] . '/TPnoimage' . (isset($context['TPortal']['article']['boardnews']) ? '_forum' : '') . '.gif" alt="" />
	</div>'; 
}

function article_picturecolumn($render = true)
{
	global $context, $settings, $boardurl;
	
	if(!empty($context['TPortal']['article']['illustration']) && !isset($context['TPortal']['article']['boardnews'])) 
		echo '
	<div style="width: 128px; height: 128px; background: top right url(' . $boardurl . '/tp-files/tp-articles/illustrations/' . $context['TPortal']['article']['illustration'] . ') no-repeat;"></div>'; 
	elseif(!empty($context['TPortal']['article']['illustration']) && isset($context['TPortal']['article']['boardnews'])) 
		echo '
	<div style="width: 128px; height: 128px; background: top right url(' . $context['TPortal']['article']['illustration'] . ') no-repeat;"></div>'; 
	else
		echo '
	<div style="width: 128px; height: 128px; background: top right url(' . $settings['tp_images_url'] . '/TPno_illustration.gif) no-repeat;"></div>'; 

}

function article_shortdate($render = true)
{
	global $context;

	if(in_array('date',$context['TPortal']['article']['visual_options']))
		echo '
		<span class="article_shortdate">' . tptimeformat($context['TPortal']['article']['date'], true, '%d %b %Y').' - </span>';
	else
		echo '';
}

function article_boardnews($render = true)
{
	global $context, $scripturl, $txt;
	
	if(!isset($context['TPortal']['article']['replies']))
		return;

	echo '<div class="tp_pad">
		<span class="article_boardnews">
			<a href="' . $scripturl . '?topic=' . $context['TPortal']['article']['id'] . '.0">' . $context['TPortal']['article']['replies'] . ' ' . ($context['TPortal']['article']['replies'] == 1 ? $txt['ssi_comment'] : $txt['ssi_comments']) . '</a>';
	if($context['TPortal']['article']['locked'] == 0 && !$context['user']['is_guest'])
		echo '
			&nbsp;|&nbsp;' . '<a href="' . $scripturl . '?action=post;topic=' . $context['TPortal']['article']['id'] . '.' . $context['TPortal']['article']['replies'] . ';num_replies=' . $context['TPortal']['article']['replies'] . '">' . $txt['ssi_write_comment']. '</a>';
	
	echo '
		</span></div>';
}

function article_author($render = true)
{
	global $scripturl, $txt, $context;
	
	if(in_array('author', $context['TPortal']['article']['visual_options']))
	{
		if($context['TPortal']['article']['dateRegistered'] > 1000)
			echo '
		'. $txt['tp-by'] . ' <a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['authorID'] . '">' . $context['TPortal']['article']['realName'] . '</a>';
		else
			echo '&nbsp;
		' . $txt['tp-by'] . ' ' . $context['TPortal']['article']['realName'];
	}
}

function article_views($render = true)
{
	global $txt, $context;
	
	if(in_array('views',$context['TPortal']['article']['visual_options']))
		echo '
		<span class="article_views">' . $context['TPortal']['article']['views'] . ' ' . $txt['tp-views'] . '</span>';
	else
		echo '';

}

function article_title($render = true)
{
	global $scripturl, $context;
	
	if(in_array('title',$context['TPortal']['article']['visual_options']))
	{
		if(isset($context['TPortal']['article']['boardnews']))
			echo '
		<a href="' . $scripturl . '?topic=' . $context['TPortal']['article']['id'] . '">' . $context['TPortal']['article']['subject'] . '</a>';
		else
			echo '
		<a href="' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '">' . $context['TPortal']['article']['subject'] . '</a>';
	}
}

function article_category($render=true)
{
	global $scripturl, $txt, $context;

	$catNameOrId = !empty($context['TPortal']['article']['category_shortname']) ? $context['TPortal']['article']['category_shortname'] : $context['TPortal']['article']['category'];

	if(!empty($context['TPortal']['article']['category_name']))
	{
		if(isset($context['TPortal']['article']['boardnews']))
			echo '
		<span class="article_category">' . $txt['tp-fromcategory'] . '<a href="' . $scripturl . '?board=' . $catNameOrId . '">' . $context['TPortal']['article']['category_name'] . '</a></span>';
		else
			echo '
		<span class="article_category">' . $txt['tp-fromcategory'] . '<a href="' . $scripturl . '?cat=' . $catNameOrId . '">' . $context['TPortal']['article']['category_name'] . '</a></span>';
	}
}

function article_lead($render = true)
{
	global $context;
	
	if(in_array('lead',$context['TPortal']['article']['visual_options']))
		echo '
	<div class="article_lead">' . tp_renderarticle('intro') . '</div>';
	else
		echo '';

}

function article_options($render=true)
{
	global $scripturl, $txt, $context;
	
	echo '';
	if(!isset($context['TPortal']['article']['boardnews']))
	{
		// give 'em a edit link? :)
		if(allowedTo('tp_articles') && $context['TPortal']['hide_editarticle_link']=='0')
			echo '
					<span class="article_rating"><a href="' . $scripturl . '?action=tpadmin;sa=editarticle' . $context['TPortal']['article']['id'] . '">' . $txt['tp-edit'] . '</a></span>';
		// their own article?
		elseif(allowedTo('tp_editownarticle') && !allowedTo('tp_articles') && $context['TPortal']['article']['authorID'] == $context['user']['id'] && $context['TPortal']['hide_editarticle_link'] == '0' && $context['TPortal']['article']['locked'] == '0')
			echo '
					<span class="article_rating"><a href="' . $scripturl . '?action=tpmod;sa=editarticle' . $context['TPortal']['article']['id'] . '">' . $txt['tp-edit'] . '</a></span>';
		else
			echo '';
		
	}	
	if($context['TPortal']['print_articles'] == 0 )
		echo '';
	else
	{
		if(isset($context['TPortal']['article']['boardnews']) && !$context['user']['is_guest'])
			echo '
		<span class="article_rating"><a href="' . $scripturl . '?action=printpage;topic=' . $context['TPortal']['article']['id'] . '">' . $txt['print_page'] . '</a></span>';
		elseif (!$context['user']['is_guest'])
			echo '
		<span class="article_rating"><a href="' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . ';print">' . $txt['tp-print'] . '</a></span>';
	} 

}

function article_text($render = true)
{
	echo '
	<div class="article_bodytext">' . tp_renderarticle() . '</div>';

}

function article_rating($render = true)
{
	global $context;
	
	if(in_array('rating',$context['TPortal']['article']['visual_options']))
	{
		if(!empty($context['TPortal']['article']['voters']))
			echo '
		<span class="article_rating">' . (render_rating($context['TPortal']['article']['rating'], count(explode(',', $context['TPortal']['article']['voters'])), $context['TPortal']['article']['id'], (isset($context['TPortal']['article']['can_rate']) ? $context['TPortal']['article']['can_rate'] : false))) . '</span>';
		else
			echo '
		<span class="article_rating">' . (render_rating($context['TPortal']['article']['rating'], 0, $context['TPortal']['article']['id'], (isset($context['TPortal']['article']['can_rate']) ? $context['TPortal']['article']['can_rate'] : false))) . '</span>';
	}
	else
		echo '';

}

function article_moreauthor($render = true)
{
	global $scripturl, $txt, $context;
	
	if(in_array('avatar', $context['TPortal']['article']['visual_options']))
	{
		echo '
	<div class="windowbg2">';
		if($context['TPortal']['article']['dateRegistered']>1000)
			echo '
		<div class="article_authorinfo tp_pad">
			<h2 class="author_h2">'.$txt['tp-authorinfo'].'</h2>
			' . ( !empty($context['TPortal']['article']['avatar']) ? '<a class="avatar" href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['authorID'] . '" title="' . $context['TPortal']['article']['realName'] . '">' . $context['TPortal']['article']['avatar'] . '</a>' : '') . '
			<div class="authortext">
				<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['authorID'] . '">' . $context['TPortal']['article']['realName'] . '</a>' . $txt['tp-poster1'] . $context['forum_name'] . $txt['tp-poster2'] . timeformat($context['TPortal']['article']['dateRegistered']) . $txt['tp-poster3'] . 
				$context['TPortal']['article']['posts'] . $txt['tp-poster4'] . timeformat($context['TPortal']['article']['lastLogin']) . '.
			</div>			
		</div><br />';
		else
			echo '
		<div class="article_authorinfo tp_pad">
			<h3>'.$txt['tp-authorinfo'].'</h3>
			<div class="authortext">
				<em>' . $context['TPortal']['article']['realName'] . $txt['tp-poster5'] .  '</em>
			</div>			
		</div>';
		echo '
	</div>';
	}
	else
		echo '';

}

function article_avatar($render = true)
{
	global $scripturl, $context;
	
	if(in_array('avatar', $context['TPortal']['article']['visual_options']))
	{
		echo (!empty($context['TPortal']['article']['avatar']) ? '<div class="avatar_single" ><a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['authorID'] . '" title="' . $context['TPortal']['article']['realName'] . '">' . $context['TPortal']['article']['avatar'] . '</a></div>' : '');
	}
	else
		echo '';

}
function article_bookmark($render = true)
{
	global $scripturl, $settings, $context;
	
	if(in_array('social',$context['TPortal']['article']['visual_options']))
		echo '
	<div class="windowbg2" style="margin: 1px 0; padding-bottom: 1em;">
		<div class="article_socialbookmark">
			<a href="http://www.facebook.com/sharer.php?u=' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/social/facebook.png" alt="Share on Facebook!" title="Share on Facebook!" /></a>
			<a href="http://twitter.com/home/?status=' . $scripturl.'?page='. $context['TPortal']['article']['id'] . '" target="_blank"><img title="Share on Twitter!" src="' . $settings['tp_images_url'] . '/social/twitter.png" alt="Share on Twitter!" /></a>
			<a href="http://plusone.google.com/_/+1/confirm?hl=en&url=' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/social/gplus.png" alt="g+" title="Share on Google Plus" /></a>
			<a href="http://www.reddit.com/submit?url=' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/social/reddit.png" alt="Reddit" title="Reddit" /></a>
			<a href="http://digg.com/submit?url=' . $scripturl.'?page='. $context['TPortal']['article']['id'] . '&title=' . $context['TPortal']['article']['subject'].'" target="_blank"><img title="Digg this story!" src="' . $settings['tp_images_url'] . '/social/digg.png" alt="Digg this story!" /></a>
			<a href="http://del.icio.us/post?url=' . $scripturl.'?page=' . $context['TPortal']['article']['id'] . '&title=' . $context['TPortal']['article']['subject'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/social/delicious.png" alt="Del.icio.us" title="Del.icio.us" /></a>
			<a href="http://www.stumbleupon.com/submit?url=' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/social/stumbleupon.png" alt="StumbleUpon" title="Stumbleupon" /></a>
		</div>
	</div>';
	else
		echo '';

}

function article_comments($render = true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('comments', $context['TPortal']['article']['visual_options']))
	{
		echo '
	<h2 class="titlebg" style="padding: 0 1em;">' .	$txt['tp-comments'] . '  ' . (tp_hidepanel('articlecomments', false, true, '5px 5px 0 5px')) . '</h2>
	<div id="articlecomments"' . (in_array('articlecomments',$context['tp_panels']) ? ' style="display: none;"' : '') . '>
		<div class="windowbg2" style="padding: 1em 2em;">';

		$counter = 1;
				if(isset($context['TPortal']['article']['comment_posts']))
		{
			foreach($context['TPortal']['article']['comment_posts'] as $comment)
			{
				if ($comment['posterID'] == 0)
					echo '
				<div class="othercomment">
						<a id="comment'.$comment['id'].'"></a>
						<strong>' . $counter++ .') ' . $comment['subject'] . '</strong>
						<div class="middletext" style="padding-top: 0.5em;"> '.$txt['tp-by'].' '.$txt['guest_title'].' '. $txt['on'] . ' ' . $comment['date'] . '</div>
						' . (($comment['is_new'] && $context['user']['is_logged']) ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/new.gif" alt="" />' : '') . '
						<div class="textcomment"><div class="body">' . $comment['text'] . '</div></div><br />';
				else
					echo '
					<div class="' . ($context['TPortal']['article']['authorID']!=$comment['posterID'] ? 'mycomment' : 'othercomment') . '">
					<a id="comment'.$comment['id'].'"></a>
					<span class="comment_author">' . (!empty($comment['avatar']['image']) ? $comment['avatar']['image'] : '') . '</span>
					<strong>' . $counter++ .') ' . $comment['subject'] . '</strong>
					' . (($comment['is_new'] && $context['user']['is_logged']) ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/new.gif" alt="" />' : '') . '
					<div class="middletext" style="padding-top: 0.5em;"> '.$txt['tp-by'].' <a href="'.$scripturl.'?action=profile;u='.$comment['posterID'].'">'.$comment['poster'].'</a>
					' . $txt['on'] . ' ' . $comment['date'] . '</div>
					<div class="textcomment"><div class="body">' . $comment['text'] . '</div></div><br />';


					// can we edit the comment or are the owner of it?
				if(allowedTo('tp_articles') || $comment['posterID'] == $context['user']['id'] && !$context['user']['is_guest'])  
					echo '
						<div class="buttonlist align_right"><ul><li><a class="active" href="' . $scripturl . '?action=tpmod;sa=killcomment' . $comment['id'] . '" onclick="javascript:return confirm(\'' . $txt['tp-confirmdelete'] . '\')"><span>' . $txt['tp-delete'] . '</span></a></li></ul></div><br />';

				echo '
				</div>';
			}
		echo '
			</div>';
		}
	}
			
		if(in_array('commentallow', $context['TPortal']['article']['visual_options']) && !empty($context['TPortal']['can_artcomment']))
		{
				echo '
			<div class="windowbg" style="margin-top: 10px; padding: 1em;">
				<form accept-charset="' . $context['character_set'] . '"  name="tp_article_comment" action="' . $scripturl . '?action=tpmod;sa=comment" method="post" style="margin: 0; padding: 0;">
						<input name="tp_article_comment_title" type="text" style="width: 99%;" value="Re: ' . strip_tags($context['TPortal']['article']['subject']) . '">
						<textarea style="width: 99%; height: 8em;" name="tp_article_bodytext"></textarea>
';

				echo '
						<br />&nbsp;<input id="tp_article_comment_submit" type="submit" value="' . $txt['tp-submit'] . '">
						<input name="tp_article_type" type="hidden" value="article_comment">
						<input name="tp_article_id" type="hidden" value="' . $context['TPortal']['article']['id'] . '">
						<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
				</form>
			</div>';
		}
		else
			echo '
			<div style="padding: 1ex;" class="windowbg"><em>' . $txt['tp-cannotcomment'] . '</em></div>';

		echo '
			</div>';

}

function article_morelinks($render = true)
{
	global $scripturl, $txt, $context;
	
	if(in_array('category',$context['TPortal']['article']['visual_options']))
	{
		echo '';
		if(in_array('category',$context['TPortal']['article']['visual_options']) && isset($context['TPortal']['article']['others']))
		{
			echo '
	<h2 class="titlebg" style="padding: 0 1em;"><a href="' . $scripturl . '?cat='. (!empty($context['TPortal']['article']['value8']) ? $context['TPortal']['article']['value8'] : $context['TPortal']['article']['category']) .'">' . $txt['tp-articles'] . ' ' . $txt['in'] . ' &#171; ' . $context['TPortal']['article']['value1'] . ' &#187;</span></a></h2>
	<div class="windowbg2" style="overflow: hidden;">
		<ul style="margin: 0; padding: 1em 2em;">';
			foreach($context['TPortal']['article']['others'] as $art)
				echo '
			<li' . (isset($art['selected']) ? ' class="selected"' : '') . '><a href="' . $scripturl . '?page=' . (!empty($art['shortname']) ? $art['shortname'] : $art['id']) . '">' . html_entity_decode($art['subject']) . '</a></li>';
			echo '
		</ul>
	</div>';
		}
	}
	else
		echo '';

}

function render_rating($total, $votes, $id, $can_rate = false)
{
	global $txt, $context, $settings, $scripturl;
	
	if($total == 0 && $votes > 0)
		echo ' '.  $txt['tp-ratingaverage'] . ' 0 (' . $votes . ' ' . $txt['tp-ratingvotes'] . ')';
	elseif($total == 0 && $votes == 0)
		echo ' '.  $txt['tp-ratingaverage'] . ' 0 (0 ' . $txt['tp-ratingvotes'] . ')';
	else
		echo ' '.  $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src=" '. $settings['tp_images_url'].'/TPblue.gif" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />' , ceil($total/$votes))) : ceil($total/$votes)) . ' (' . $votes . ' ' . $txt['tp-ratingvotes'] . ')';

	// can we rate it?
	if($context['TPortal']['single_article'])
	{
		if($context['user']['is_logged'] && $can_rate)
		{
				echo '
			<form action="' . $scripturl . '?action=tpmod;sa=rate_article" style="margin: 0; padding: 0; display: inline;" method="post">
				<select size="1" name="tp_article_rating">';
				
				for($u=$context['TPortal']['maxstars'] ; $u>0 ; $u--)
					echo '
					<option value="' . $u . '">' . $u . '</option>';

				echo '
				</select>
				<input type="submit" name="tp_article_rating_submit" value="' . $txt['tp_rate'] . '">
				<input name="tp_article_type" type="hidden" value="article_rating">
				<input name="tp_article_id" type="hidden" value="' . $id . '">
				<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
			</form>';
		}
		else
		{
			if (!$context['user']['is_guest'])
			echo ' 	<em class="tp_article_rate smalltext">'. $txt['tp-dlhaverated'].'</em>';
		}
	}	

}

function tp_grids()
{
	// the built-in grids
	$grid = array(
		// vertical
		1 => array(
				'cols' => 1,
				'code' => '
			<div class="tp_container">
				<div class="tp_col16">{featured}</div>
			</div>
			<div class="tp_container">
				<div class="tp_col16">{col1}{col2}</div>
			</div>'
		),
		// featured 1 col, 2 cols
		2 => array(
				'cols' => 2,
				'code' => '
			<div class="tp_container">
				<div class="tp_col16">{featured}</div>
			</div>
			<div class="tp_container">
				<div class="tp_col8"><div class="leftcol">{col1}</div></div>
				<div class="tp_col8"><div class="rightcol">{col2}</div></div>
			</div>'
		),
		// featured left col, rest right col
		3 => array(
				'cols' => 1,
				'code' => '
			<div class="tp_container">
				<div class="tp_col8" ><div class="leftcol">{featured}</div></div>
				<div class="tp_col8"><div class="rightcol">{col1}{col2}</div></div>
			</div>'
		),
		// 2 cols
		4 => array(
				'cols' => 2,
				'code' => '
			<div class="tp_container">
				<div class="tp_col8"><div class="leftcol">{featured}{col1}</div></div>
				<div class="tp_col8"><div class="rightcol">{col2}</div></div>
			</div>'
		),
		// 2 cols, then featured at bottom
		5 => array(
				'cols' => 1,
				'code' => '
			<div class="tp_container">
				<div class="tp_col16">{col1}{col2}</div>
			</div>
			<div class="tp_container">
				<div class="tp_col16">{featured}</div>
			</div>'
		),
		// rest left col, featured right col
		6 => array(
				'cols' => 1,
				'code' => '
			<div class="tp_container">
				<div class="tp_col8"><div class="rightcol">{col1}{col2}</div></div>
				<div class="tp_col8" ><div class="leftcol">{featured}</div></div>
			</div>'
		),
	);
	return $grid;
}

/* for blockarticles */
// This is the template for single article
function template_blockarticle()
{
	global $context;

	// use a customised template or the built-in?
	if(!empty($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['template']))
		render_template($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['template']);
	else
		render_template(blockarticle_renders());
}
function blockarticle_renders()
{
	$code = '
	<div class="blockarticle render1">
		<div class="article_info">
			{blockarticle_author} 
			{blockarticle_date} 
			{blockarticle_views} 
		</div>
		<div class="article_padding">{blockarticle_text}</div>
		<div class="article_padding">{blockarticle_moreauthor}</div>
	</div>	
		';
	return $code;
}
 

function blockarticle_date($render = true)
{
	global $context;
	
	if(in_array('date',$context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options']))
		echo '
		<span class="article_date"> ' . (timeformat($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['date'])) . '</span>';
	else
		echo '';

}

function blockarticle_author($render = true)
{
	global $scripturl, $txt, $context;
	
	if(in_array('author',$context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options']))
	{
		if($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['dateRegistered'] > 1000)
			echo '
		<span class="article_author">' . $txt['tp-by'] . ' <a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['authorID'] . '">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . '</a></span>';
		else
			echo '
		<span class="article_author">' . $txt['tp-by'] . ' ' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . '</span>';
	}
	else
		echo '';
		
}

function blockarticle_views($render = true)
{
	global $txt, $context;
	
	if(in_array('views',$context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options']))
		echo '
		<span class="article_views">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['views'] . ' ' . $txt['tp-views'] . '</span>';
	else
		echo '';

}

function blockarticle_text($render = true)
{
	echo '
	<div class="article_bodytext">' . tp_renderblockarticle() . '</div>';

}

function blockarticle_moreauthor($render = true)
{
	global $scripturl, $txt, $context;
	
	if(in_array('avatar', $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options']))
	{
		if($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['dateRegistered'] > 1000)
			echo '
		<div class="article_authorinfo">
			<h3>'.$txt['tp-authorinfo'].'</h3>
			' . ( !empty($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['avatar']) ? '<a class="avatar" href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['authorID'] . '" title="' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . '">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['avatar'] . '</a>' : '') . '
			<div class="authortext">
				<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['authorID'] . '">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . '</a>' . $txt['tp-poster1'] . $context['forum_name'] . $txt['tp-poster2'] . timeformat($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['dateRegistered']) . $txt['tp-poster3'] . 
				$context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['posts'] . $txt['tp-poster4'] . timeformat($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['lastLogin']) . '.
			</div>			
		</div>';
		else
			echo '
		<div class="article_authorinfo">
			<h3>'.$txt['tp-authorinfo'].'</h3>
			<div class="authortext">
				<em>' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . $txt['tp-poster5'] .  '</em>
			</div>			
		</div>';
	}
	else
		echo '';

}
function category_childs()
{
	global $context, $scripturl;

	echo '
	<ul class="category_children">';
	foreach($context['TPortal']['category']['children'] as $ch => $child)
		if ($context['TPortal']['category']['options']['showchild'] == 1)
			echo '<li><a href="' , $scripturl , '?cat=' , $child['id'] , '">' , $child['value1'] ,' (' , $child['articlecount'] , ')</a></li>';
	
	echo '
	</ul>';

	return;
}

function template_subtab_above()
{
	global $context, $txt;

	if(isset($context['TPortal']['subtabs']) && sizeof($context['TPortal']['subtabs']) > 1)
	{
		echo '
		<div class="tborder" style="margin-bottom: 0.5em;">
			<div class="cat_bar">
				<h3 class="catbg">' . $txt['tp-menus'] .'</h3>
			</div>
			<div style="padding: 2px; overflow: hidden;">';
		
		tp_template_button_strip($context['TPortal']['subtabs']);
		
		echo '
			</div>
		</div>';
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
	<div  id="tpadmin_menu">
		<div class="cat_bar">
			<h3 class="catbg">' . $txt['tp-tpmenu'] .'</h3>
		</div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe">';

	
	if(is_array($context['admin_tabs']) && count($context['admin_tabs']) > 0)
	{
		echo '
			<ul style="padding-bottom: 10px;">';
		foreach($context['admin_tabs'] as $ad => $tab)
		{
			echo '
				<li><div class="largetext">' , isset($context['admin_header'][$ad]) ? $context['admin_header'][$ad] : '' , '</div>
					';
			$tbas = array();
			foreach($tab as $tb)
				$tbas[]='<a href="' . $tb['href'] . '">' .($tb['is_selected'] ? '<b>'.$tb['title'].'</b>' : $tb['title']) . '</a>';
			
			// if new style...
			if($context['TPortal']['oldsidebar'] == 0)
				echo '<div class="normaltext">' , implode(', ', $tbas) , '</div>
				</li>';
			else
				echo '<div class="middletext" style="margin: 0; line-height: 1.3em;">' , implode('<br />', $tbas) , '</div>
				</li>';

		}
		echo '	
			</ul>';
	}

	echo '
		</div>
		<span class="lowerframe"><span></span></span>
	</div>
	<div id="tpadmin_content" style="margin-top: 0;">';
}

function template_tpadm_below()
{
	echo '
		
	</div>';
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
			<span class="topslice"><span></span></span>
			<div class="padding">', $context['TPortal']['errormessage'] , '</div>
			<span class="botslice"><span></span></span>
		</div>
	</div>';

	// Show a back button (using javascript.)
	echo '
	<div class="centertext"><a href="javascript:history.go(-1)">', $txt['back'], '</a></div>';
}

// Format a time to make it look purdy.
function tptimeformat($log_time, $show_today = true, $format)
{
	global $context, $user_info, $txt, $modSettings, $smcFunc;

	$time = $log_time + ($user_info['time_offset'] + $modSettings['time_offset']) * 3600;

	// We can't have a negative date (on Windows, at least.)
	if ($log_time < 0)
		$log_time = 0;

	// Today and Yesterday?
	if ($modSettings['todayMod'] >= 1 && $show_today === true)
	{
		// Get the current time.
		$nowtime = forum_time();

		$then = @getdate($time);
		$now = @getdate($nowtime);

		// Try to make something of a time format string...
		$s = strpos($format, '%S') === false ? '' : ':%S';
		if (strpos($format, '%H') === false && strpos($format, '%T') === false)
		{
			$h = strpos($format, '%l') === false ? '%I' : '%l';
			$today_fmt = $h . ':%M' . $s . ' %p';
		}
		else
			$today_fmt = '%H:%M' . $s;

		// Same day of the year, same year.... Today!
		if ($then['yday'] == $now['yday'] && $then['year'] == $now['year'])
			return $txt['today'] . tptimeformat($log_time, $today_fmt, $format);

		// Day-of-year is one less and same year, or it's the first of the year and that's the last of the year...
		if ($modSettings['todayMod'] == '2' && (($then['yday'] == $now['yday'] - 1 && $then['year'] == $now['year']) || ($now['yday'] == 0 && $then['year'] == $now['year'] - 1) && $then['mon'] == 12 && $then['mday'] == 31))
			return $txt['yesterday'] . tptimeformat($log_time, $today_fmt, $format);
	}

	$str = !is_bool($show_today) ? $show_today : $format;

	if (setlocale(LC_TIME, $txt['lang_locale']))
	{
		foreach (array('%a', '%A', '%b', '%B') as $token)
			if (strpos($str, $token) !== false)
				$str = str_replace($token, !empty($txt['lang_capitalize_dates']) ? $smcFunc['ucwords'](strftime($token, $time)) : strftime($token, $time), $str);
	}
	else
	{
		// Do-it-yourself time localization.  Fun.
		foreach (array('%a' => 'days_short', '%A' => 'days', '%b' => 'months_short', '%B' => 'months') as $token => $text_label)
			if (strpos($str, $token) !== false)
				$str = str_replace($token, $txt[$text_label][(int) strftime($token === '%a' || $token === '%A' ? '%w' : '%m', $time)], $str);
		if (strpos($str, '%p'))
			$str = str_replace('%p', (strftime('%H', $time) < 12 ? 'am' : 'pm'), $str);
	}

	// Windows doesn't support %e; on some versions, strftime fails altogether if used, so let's prevent that.
	if ($context['server']['is_windows'] && strpos($str, '%e') !== false)
		$str = str_replace('%e', ltrim(strftime('%d', $time), '0'), $str);

	// Format any other characters..
	return strftime($str, $time);
}

// Generate a strip of buttons.
function tp_template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $context, $txt;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . '' . ($value['active'] ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' align_' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}
?>
