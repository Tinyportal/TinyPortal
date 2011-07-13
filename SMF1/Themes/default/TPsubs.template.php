<?php
// Version: TinyPortal 1.0; TPsubs
// For use with SMF v1.1.x

// TPortal searchblock
function TPortal_searchbox()
{
	global $context, $settings, $options, $txt , $scripturl;

	echo '
	<form accept-charset="', $context['character_set'], '" action="', $scripturl, '?action=search2" method="post" style="padding: 0; text-align: center; margin: 0; ">
		<input type="text" name="search" value="" class="block_search" />
		<input type="submit" name="submit" value="', $txt[182], '" class="block_search_submit" /><br />
		<span class="smalltext"><a href="', $scripturl, '?action=search;advanced">', $txt['smf298'], '</a></span>
		<input type="hidden" name="advanced" value="0" />
	</form>';
}

// TPortal onlineblock
function TPortal_onlinebox()
{
	global $context, $settings, $options, $txt;

	echo '
	<div style="line-height: 1.4em;">' , ssi_whosOnline() , '</div>';
}

function TPortal_tpmodulebox($blockid)
{
	global $context, $settings, $options, $txt;

	// fetch the correct block
	if(!empty($context['TPortal']['moduleid']))
	{
		$tpm=$context['TPortal']['moduleid'];
		if(!empty($context['TPortal']['tpmodules']['blockrender'][$tpm]['function']) && function_exists($context['TPortal']['tpmodules']['blockrender'][$tpm]['function']))
			call_user_func($context['TPortal']['tpmodules']['blockrender'][$tpm]['function']);
	}
}

// php blocktype
function TPortal_phpbox()
{
	global $context, $settings, $options, $txt, $scripturl;

	// execute what is in the block, no echoing
	if(!empty($context['TPortal']['phpboxbody']));
		eval(tp_convertphp($context['TPortal']['phpboxbody'],true));
}

// an article
function TPortal_articlebox()
{
	global $context, $settings, $options, $txt, $scripturl;

	if(isset($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]))
		echo '<div class="block_article">', 	template_blockarticle() , '</div>';
}

// php blocktype
function TPortal_scriptbox()
{
	global $context, $settings, $options, $txt;

    echo $context['TPortal']['scriptboxbody'];
}

// TPortal recent topics block
function TPortal_recentbox()
{
	global $scripturl, $context, $settings, $options, $txt , $modSettings;

    // is it a number?
	if(!is_numeric($context['TPortal']['recentboxnum']))
		$context['TPortal']['recentboxnum']='10';

	// leave out the recycle board, if any
	if(isset($modSettings['recycle_board']))
		$bb=array($modSettings['recycle_board']);
	else
		$bb=array();

	if($context['TPortal']['useavatar']==0)
	{
	$what = ssi_recentTopics($context['TPortal']['recentboxnum'], $bb, 'array');

		// Output the topics
		echo '
		<ul class="middletext" style="line-height: 1.5em; ' , isset($context['TPortal']['recentboxscroll']) && $context['TPortal']['recentboxscroll']==1 ? 'overflow: auto; height: 20ex;' : '' , 'margin: 0; padding: 0;">';
		foreach($what as $wi => $w){
			echo '
			<li style="margin: 0; list-style: none; line-height: 1.5em; padding: 4px 0;"><a href="'.$w['href'].'" title="' . $w['subject'] . '">'.$w['short_subject'].'</a> ', $txt[525], ' ', $w['poster']['link'];
			if(!$w['new'])
				echo ' <a href="'.$w['href'].'"><img src="'. $settings['images_url'].'/'.$context['user']['language'].'/new.gif" alt="new" /></a> ';

			echo '	['.$w['time'].']
			</li>';
		}
		echo '
		</ul>';
	}
	else
	{
		$what = tp_recentTopics($context['TPortal']['recentboxnum'], $bb, 'array');

		// Output the topics
		echo '
		<ul class="recent_topics" style="' , isset($context['TPortal']['recentboxscroll']) && $context['TPortal']['recentboxscroll']==1 ? 'overflow: auto; height: 20ex;' : '' , 'margin: 0; padding: 0;">';
		foreach($what as $wi => $w){
			echo '
			<li>
				<span class="tpavatar"><a href="' . $scripturl. '?action=profile;u=' . $w['poster']['id'] . '">' , empty($w['poster']['avatar']) ? '<img src="' . $settings['tp_images_url'] . '/TPguest.png" alt="" />' : $w['poster']['avatar'] , '</a></span><a href="'.$w['href'].'">' . $w['short_subject'].'</a><br />
				 ', $txt[525], ' <b>', $w['poster']['link'],'</b><br />';
			if(!$w['new'])
				echo ' <a href="'.$w['href'].'"><img src="'. $settings['images_url'].'/'.$context['user']['language'].'/new.gif" alt="new" /></a> ';

			echo '['.$w['time'].']
			</li>';
		}
		echo '
		</ul>';
	}
}

// TPortal categories
function TPortal_catmenu()
{
	global $context, $settings, $options , $scripturl, $boardurl;

	if(isset($context['TPortal']['menu'][$context['TPortal']['menuid']]) && !empty($context['TPortal']['menu'][$context['TPortal']['menuid']])){
		echo '
	<ul class="tp_catmenu">';
		// we are on level 0
		$level=0; $oldlevel=0;
		foreach($context['TPortal']['menu'][$context['TPortal']['menuid']] as $cn)
		{
			echo '
		<li', $cn['type']=='head' ? ' class="tp_catmenu_header"' : '' ,'>';
			if($context['TPortal']['menuvar1']=='' || $context['TPortal']['menuvar1']=='0')
				echo str_repeat("&nbsp;&nbsp;", ($cn['sub']+1));
			elseif($context['TPortal']['menuvar1']=='1')
				echo str_repeat("&nbsp;&nbsp;", ($cn['sub']+1));
			elseif($context['TPortal']['menuvar1']=='2')
				echo str_repeat("&nbsp;&nbsp;", ($cn['sub']+1));

			if((!isset($cn['icon']) || (isset($cn['icon']) && $cn['icon']=='')) && $cn['type']!='head' && $cn['type']!='spac')
			{
				if($context['TPortal']['menuvar1']=='' || $context['TPortal']['menuvar1']=='0')
					echo '
			<img src="'.$boardurl.'/tp-images/icons/TPdivider2.gif" alt="" />&nbsp;';
				elseif($context['TPortal']['menuvar1']=='1' && $cn['sub']==0)
					echo '
			<img src="'.$boardurl.'/tp-images/icons/bullet3.gif" alt="" />';
			
			}
			elseif(isset($cn['icon']) && $cn['icon']!='' && $cn['type']!='head' && $cn['type']!='spac')
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
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

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
		echo '
		<h4>', $txt['hello_member'], ' ', $context['user']['name'], '</h4>
		<ul class="reset">';

		// Only tell them about their messages if they can read their messages!
		if ($context['allow_pm'])
		{
			echo '
			<li><a href="', $scripturl, '?action=pm">' .$bullet.$txt['tp-pm'].' ',  $context['user']['messages'], '</a></li>';
			if($context['user']['unread_messages']>0)
				echo '
			<li style="font-weight: bold; "><a href="', $scripturl, '?action=pm">' . $bullet. $txt['tp-pm2'].' ',$context['user']['unread_messages'] , '</a></li>';
		}
		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
			<li><a href="', $scripturl, '?action=viewmembers;sa=browse;type=approve">'.$bullet. $txt['tp_unapproved_members'].' '. $context['unapproved_members']  . '</a></li>';

		if(isset($context['TPortal']['userbox']['unread']))
			echo '
			<li><a href="', $scripturl, '?action=unread">' .$bullet.$txt['tp-unread'].'</a></li>
			<li><a href="', $scripturl, '?action=unreadreplies">'.$bullet.$txt['tp-replies'].'</a></li>
			<li><a href="', $scripturl, '?action=profile;u='.$context['user']['id'].';sa=showPosts">'.$bullet. $txt['tp-showownposts'].'</a></li>
			<li><a href="', $scripturl, '?action=tpmod;sa=showcomments">'.$bullet.$txt['tp-showcomments'].'</a></li>
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
		echo '
			<li>' . $bullet2.$context['current_time'].'</li>';
		
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
		if(sizeof($context['TPortal']['tpmodules']['adminhook'])>0)
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
		<div style="line-height: 1.4em;">', $bullet, $txt['welcome_guest'], '
		<br />', $bullet2, $context['current_time'], '</div>
		<form style="margin-top: 5px;" action="', $scripturl, '?action=login2" method="post" >
			<input type="text" name="user" size="10" /> <input type="password" name="passwrd" size="10" /><br />
			<select name="cookielength">
				<option value="60">', $txt['smf53'], '</option>
				<option value="1440">', $txt['smf47'], '</option>
				<option value="10080">', $txt['smf48'], '</option>
				<option value="302400">', $txt['smf49'], '</option>
				<option value="-1" selected="selected">', $txt['smf50'], '</option>
			</select>
			<input type="submit" value="', $txt[34], '" />
		</form>
		<div style="line-height: 1.4em;">', $txt['smf52'], '</div>
		<br />';
	}
	if (!empty($context['user']['avatar']) && isset($context['TPortal']['userbox']['avatar']))
		echo '<div style="margin-top: 5px;">' , $context['user']['avatar']['image'] , '</div>';

	echo '
	</div>';
}

// TPortal themebox
function TPortal_themebox()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $user_info;

	$what=explode(",",$context['TPortal']['themeboxbody']);
	$temaid=array();
	$temanavn=array();
	$temapaths=array();
	foreach($what as $wh => $wht)
	{
		$all=explode("|",$wht);	
		if($all[0]>-1)
		{
			$temaid[]=$all[0];
			$temanavn[]=isset($all[1]) ? $all[1] : '';
			$temapaths[]=isset($all[2]) ? $all[2] : '';
		}
	}
	
	if(isset($context['TPortal']['querystring']))
		$tp_where=htmlspecialchars(strip_tags($context['TPortal']['querystring']), ENT_QUOTES, $context['character_set']);
	else
		$tp_where='action=forum';

	if($tp_where!='')
		$tp_where .=';';

	// remove multiple theme=x in the string.
		$tp_where=preg_replace("'theme=[^>]*?;'si", "", $tp_where);

	 if(sizeof($temaid)>0){
        echo '
		<form name="jumpurl1" onsubmit="return jumpit()" class="middletext" action="" style="padding: 0; margin: 0; text-align: center;">
			<select style="width: 100%; margin: 5px 0px 5px 0px;" size="1" name="jumpurl2" onchange="check(this.value)">';
         for($a=0 ; $a<(sizeof($temaid)) ; $a++){
                echo '
				<option value="'.$temaid[$a].'" ', $settings['theme_id'] == $temaid[$a] ? 'selected="selected"' : '' ,'>'.substr($temanavn[$a],0,20).'</option>';
         }
         echo '
			</select><br />' , $context['user']['is_logged'] ?
			'<input type="checkbox" value=";permanent" onfocus="realtheme()" /> '. $txt['tp-permanent']. '<br />' : '' , '
			<input style="margin: 5px 0px 5px 10px;" type="button" value="'.$txt['tp-changetheme'].'" onclick="jumpit()" />
 			<input type="hidden" value="'.htmlspecialchars($scripturl . '?'.$tp_where.'theme='.$settings['theme_id'], ENT_QUOTES, $context['character_set']).'" name="jumpurl3" />
 			<div style="text-align: center; width: 95%; overflow: hidden;">
				<img src="'.$settings['images_url'].'/thumbnail.gif" alt="" id="chosen" name="chosen"  />
			</div>
		</form>
		<script type="text/javascript" language="Javascript">
			var extra = \'\';
			var themepath=new Array()';
         for($a=0 ; $a<(sizeof($temaid)) ; $a++){
			 echo '
			    themepath['.$temaid[$a].'] = "'.$temapaths[$a].'/thumbnail.gif"
				';
		 }

		echo '
		 function jumpit(){
                          window.location=document.jumpurl1.jumpurl3.value + extra
                          return false
                       }
                   </script>
					   <script type="text/javascript">
       function realtheme()
       {
			extra = \';permanent\';
       }
        function check(icon)
       {
			document.chosen.src= themepath[icon]
			document.jumpurl1.jumpurl3.value = \'' . $scripturl . '?'. $tp_where.'theme=\' + icon 
       }
		</script>';
	}
	else
		echo $txt['tp-nothemeschosen'];
}

// TPortal newsbox
function TPortal_newsbox()
{
    global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
	echo '
	<div class="tp_newsblock">', $context['random_news_line'], '</div>';
}

// TPortal stats box
function TPortal_statsbox()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	$bullet = '<img src="'.$settings['tp_images_url'].'/TPdivider.gif" alt=""  style="margin:0 4px 0 0;" />';
	$bullet2 = '<img src="'.$settings['tp_images_url'].'/TPdivider2.gif" alt="" style="margin:0 4px 0 0;" />';
	
	echo'
	<div class="tp_statsblock">';

	if(isset($context['TPortal']['userbox']['stats']))
		// members stats
		echo '
		<h5 class="mlist"><a href="'.$scripturl.'?action=mlist">'.$txt[19].'</a></h5>
		<ul>
			<li>' .$bullet. $txt[488].': ' , isset($modSettings['memberCount']) ? $modSettings['memberCount'] : $modSettings['totalMembers'] , '</li>
			<li>' . $bullet. $txt['tp-latest']. ': <a href="', $scripturl, '?action=profile;u=', $modSettings['latestMember'], '"><strong>', $modSettings['latestRealName'], '</strong></a></li>
		</ul>';
	if(isset($context['TPortal']['userbox']['stats_all']))
		// more stats
		echo '
		<h5 class="stats"><a href="'.$scripturl.'?action=stats">'.$txt['tp-stats'].'</a></h5>
		<ul>
			<li>'. $bullet. $txt[489].': '.$modSettings['totalMessages']. '</li>
			<li>'. $bullet. $txt[490].': '.$modSettings['totalTopics']. '</li>
			<li>' . $bullet. $txt['tp-mostonline-today'].': '.$modSettings['mostOnlineToday'].'</li>
			<li>' . $bullet. $txt['tp-mostonline'].': '.$modSettings['mostOnline'].'</li>
			<li>('.timeformat($modSettings['mostDate']).')</li>
		</ul>';

	if(isset($context['TPortal']['userbox']['online']))
	{
		// add online users
		echo '
		<h5 class="online"><a href="'.$scripturl.'?action=who">'.$txt[158].'</a></h5>
		<div style="line-height: 1.3em;">';
		
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
       global $context, $settings, $options, $scripturl, $txt, $modSettings;
       echo '
	<div style="padding: 5px;" class="smalltext">';
       if($context['TPortal']['ssifunction']=='recentpoll')
           ssi_recentPoll();
       elseif($context['TPortal']['ssifunction']=='toppoll')
           ssi_topPoll();
       elseif($context['TPortal']['ssifunction']=='topboards')
           ssi_topBoards();
       elseif($context['TPortal']['ssifunction']=='topposters')
           ssi_topPoster(5);
       elseif($context['TPortal']['ssifunction']=='topreplies')
           ssi_topTopicsReplies();
       elseif($context['TPortal']['ssifunction']=='topviews')
           ssi_topTopicsViews();
       elseif($context['TPortal']['ssifunction']=='calendar')
          ssi_todaysCalendar();

       echo '
    </div>';
}
// TPortal module
function TPortal_module()
{
       global $context, $settings, $options, $scripturl, $txt, $modSettings;

		switch($context['TPortal']['moduleblock'])
		{
		case 'dl-stats':
			dl_recentitems('8','date','echo');
			break;
		case 'dl-stats2':
			dl_recentitems('8','downloads','echo');
			break;
		case 'dl-stats3':
			dl_recentitems('8','views','echo');
			break;
		case 'dl-stats4':
			$it=array();
			$it=dl_recentitems('1','date','array');
			if(sizeof($it)>0){
				foreach($it as $item){
					echo '
					<img src="'.$item['icon'].'" align="right" style="margin-left: 4px; " alt="" />
						<a href="'.$item['href'].'"><b>'.$item['name'].'</b></a>
						<p class="smalltext">'.$txt['tp-uploadedby'].' <b>'.$item['author'].'</b> <br />( '.$item['date'].')<br />
						'.$txt['tp-downloads'].'/'.$txt['tp-itemviews'].': <b>'.$item['downloads'].' / '.$item['views'].'</b></p>';
				}
			}
			break;
		case 'dl-stats5':
			$it=array();
			$it=dl_recentitems('1','downloads','array');
			if(sizeof($it)>0){
				foreach($it as $item){
					echo '
					<img src="'.$item['icon'].'" align="right" style="margin-left: 4px; " alt="" />
						<a href="'.$item['href'].'"><b>'.$item['name'].'</b></a>
						<p class="smalltext">'.$txt['tp-uploadedby'].' <b>'.$item['author'].'</b> <br />( '.$item['date'].')<br />
						'.$txt['tp-downloads'].'/'.$txt['tp-itemviews'].': <b>'.$item['downloads'].' / '.$item['views'].'</b></p>';
				}
			}
			break;
		case 'dl-stats6':
			$it=array();
			$it=dl_recentitems('1','views','array');
			if(sizeof($it)>0){
				foreach($it as $item){
					echo '
					<img src="'.$item['icon'].'" align="right" style="margin-left: 4px; " alt="" />
						<a href="'.$item['href'].'"><b>'.$item['name'].'</b></a>
						<p class="smalltext">'.$txt['tp-uploadedby'].' <b>'.$item['author'].'</b> <br />( '.$item['date'].')<br />
						'.$txt['tp-downloads'].'/'.$txt['tp-itemviews'].': <b>'.$item['downloads'].' / '.$item['views'].'</b></p>';
				}
			}
			break;
		case 'dl-stats7':
			$it=array();
			$it=art_recentitems('5','date');
			if(sizeof($it)>0){
				foreach($it as $item){
					echo '<span class="smalltext"><a title="'.$item['date'].'" href="'.$scripturl.'?page='.$item['id'].'">'.$item['subject'].'</a>
						</span><br />';
				}
			}
			break;
		case 'dl-stats8':
			$it=array();
			$it=art_recentitems('5','views');
			if(sizeof($it)>0){
				foreach($it as $item){
					echo '<span class="smalltext"><a title="'.$item['views'].' '.$txt['tp-views'].'" href="'.$scripturl.'?page='.$item['id'].'">'.$item['subject'].'</a>
						</span><br />';
				}
			}
			break;
		case 'dl-stats9':
			$it=array();
			$it=art_recentitems('5','comments');
			if(sizeof($it)>0){
				foreach($it as $item){
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
        global $context, $settings, $options, $scripturl, $txt, $modSettings;

        echo '<div style="padding: 5px; ' , !empty($context['TPortal']['rsswidth']) ? 'max-width: ' . $context['TPortal']['rsswidth'] .';' : '' , '" class="middletext">' , TPparseRSS('', $context['TPortal']['rss_utf8']) , '</div>';
}

// Tportal sitemap menu
function TPortal_sitemap()
{
        global $context, $settings, $options, $scripturl, $txt, $modSettings;

		$current='';
        // check where we are
        if(isset($_GET['action']) && $_GET['action']=='tpmod'){
			if(isset($_GET['dl']))
				$current='dl';
			elseif(isset($_GET['link']))
				$current='link';
			elseif(isset($_GET['show']))
				$current='show';
			elseif(isset($_GET['team']))
				$current='team';
			else
				$current='';
        }
         echo '
	<div class="tborder">
		<ul class="tpsitemap">';
		if($context['TPortal']['show_download']=='1')
			echo '<li><a class="windowbg2 tpsitemapheader" href="'.$scripturl.'?action=tpmod;dl"><img src="' .$settings['tp_images_url']. '/TPmodule2.gif" border="0" alt="" /> '.$txt['tp-downloads'].'</a></li>';

		if(!empty($context['TPortal']['sitemap']) && !empty($context['TPortal']['menu'])){
			foreach($context['TPortal']['menu'] as $main)
			{
				foreach($main as $cn)
				{
					// check if we can find the link on current tpage
					$catclass = 'windowbg';
					if($cn['type']=='cats')
					{
						if(isset($_GET['cat']) && $cn['IDtype']==$_GET['cat'])
							$catclass = 'windowbg3 tpsitemapheader';
					}
					elseif($cn['type']=='arti'){
						if(isset($_GET['page']) && $cn['IDtype']==$_GET['page'])
							$catclass = 'windowbg3 tpsitemapheader';
					}
					elseif($cn['type']=='link'){
						if(!empty($context['TPortal']['querystring']))
							$qs=$scripturl.'?'.$context['TPortal']['querystring'];
						else
							$qs=$scripturl;

						if($qs==$cn['IDtype'])
							$catclass = 'windowbg3 tpsitemapheader';
					}

					if($cn['sitemap']=='1'){
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
         echo '</ul></div>';
}

// category listing blocktype
function TPortal_categorybox()
{
    global $context, $settings, $options, $txt, $scripturl;

	if(isset($context['TPortal']['blockarticle_titles'][$context['TPortal']['blocklisting']])){
		echo '<div class="middletext" ', (sizeof($context['TPortal']['blockarticle_titles'][$context['TPortal']['blocklisting']])>$context['TPortal']['blocklisting_height'] && $context['TPortal']['blocklisting_height']!='0') ? ' style="overflow: auto; width: 100%; height: '.$context['TPortal']['blocklisting_height'].'em;"' : '' ,'>';
		foreach($context['TPortal']['blockarticle_titles'][$context['TPortal']['blocklisting']] as $listing){
			if($listing['category']==$context['TPortal']['blocklisting'])
				echo '<b><a href="'.$scripturl.'?page='.$listing['shortname'].'">'.$listing['subject'].'</a></b> ' , $context['TPortal']['blocklisting_author']=='1' ? $txt[525].' '.$listing['poster'] : '' , '<br />';
		}
		echo '</div>';
	}
 }

// a dummy layer for layer articles
function template_nolayer_above()
{
	global $context, $boardurl;

	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<meta name="keywords" content="Tinyportal, themes, Bloc" />
		<title>' , $context['page_title'] , '</title>
		' , $context['tp_html_headers'] , '
	</head>
	<body><div id="nolayer_frame">';
}

function template_nolayer_below()
{
	global $scripturl, $context;

	echo '<small id="nolayer_copyright">',theme_copyright(),'<br />',tportal_version(),'</small>
	</div></body></html>';
}

// article search page 1
function template_TPsearch_above()
{
	global $context, $boardurl, $txt, $scripturl;
	

 echo '
	<div style="padding: 0 5px;">
		<h3 class="catbg"><span class="left"></span>' , $txt['tp-searcharticles'] , '</h3>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
			<p style="margin: 0; padding: 0 1em;">
				<a href="' . $scripturl. '?action=tpmod;sa=searcharticle">' . $txt['tp-searcharticles2'] . '</a> |
				<a href="' . $scripturl. '?action=tpmod;dl=search">' . $txt['tp-searchdownloads'] . '</a>';

	// any others?
	if(!empty($context['TPortal']['searcharray']) && count($context['TPortal']['searcharray'])>0)
		echo implode(" | ", $context['TPortal']['searcharray']);

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

function template_TPtagboards_above()
{
	global $context, $boardurl, $txt , $board, $scripturl;

	echo '
	<div class="tborder" style="margin-bottom: 3px;">
		<div class="titlebg" style="padding: 4px;">' . $txt['tp-tagboards'] . '  ' , tp_hidepanel('tagpanel3',true) , '</div>
		<div class="windowbg" style="padding: 4px;" id="tagpanel3" ' , in_array('tagpanel3',$context['tp_panels']) ? ' style="display: none;"' : '' , '>
				<form accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpadmin" method="post" style="margin: 0px;">
					<input type="hidden" name="sc" value="', $context['session_id'], '" />
					<input name="TPtagboards" type="hidden" value="set">';

	TPsshowgtags('tpadmin_boardtags', 'tpadmin_boardtags', $board);
	
	echo '
				<input name="send" type="submit" value="'.$txt['tp-submit'].'">
			</form>
		</div>
	</div>';

}
function template_TPtagboards_below()
{
	return;
}
function template_TPtagboardsGeneral_above()
{
	global $context, $boardurl, $txt , $board, $scripturl;

	$tags=TPsshowgtags('tpadmin_boardtags', 'tpadmin_boardtags', $board, true);
	$taglinks=TPget_globaltags($tags,$board);
	$any=tp_renderglobaltags($taglinks,true);
	if(!empty($any))
		echo '
	<div class="tborder">
		<h3 class="titlebg"><span class="left"></span>' . $txt['tp-showrelated'] . '</h3>
		<div class="windowbg" style="padding: 4px; ">
		' , $any , '
		</div>
	</div>';

}
function template_TPtagboardsGeneral_below()
{
	return;
}

// tag them topics!
function template_TPtagtopics_above()
{
	global $context, $boardurl, $txt , $topic, $scripturl;

	echo '
	<div class="tborder" style="margin-bottom: 3px;">
		<div class="titlebg" style="padding: 4px;">' . $txt['tp-tagtopics'] . ' ' , tp_hidepanel('tagpanel4',true) , '</div>
		<div class="windowbg" style="padding: 4px;" id="tagpanel4" ' , in_array('tagpanel4',$context['tp_panels']) ? ' style="display: none;"' : '' , '>
				<form accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpadmin" method="post" style="margin: 0px;">
					<input type="hidden" name="sc" value="', $context['session_id'], '" />
					<input name="TPtagtopics" type="hidden" value="set">';

	TPsshowgtags('tpadmin_topictags', 'tpadmin_topictags', $topic);
	
	echo '
				<input name="send" type="submit" value="'.$txt['tp-submit'].'">
			</form>
		</div>
	</div>';

}

function template_TPtagtopics_below()
{
	return;
}


function template_tpfrontpagetopics_above()
{
	global $context, $boardurl, $txt , $board, $scripturl;

	// not in wireless
	if(WIRELESS)
		return;

	if(!in_array($context['current_topic'], explode(",",$context['TPortal']['frontpage_topics'])))
		$tpbuttons = array(
			'publish' => array('active' => true, 'text' => 'tp-publish', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=tpmod;sa=publish;t=' . $context['current_topic']),
		);
	else
		$tpbuttons = array(
			'unpublish' => array('active' => true, 'text' => 'tp-unpublish', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=tpmod;sa=publish;t=' . $context['current_topic']),
		);

	echo '<table cellpadding="0" cellspacing="0" align="right" style="margin-right: 5px;">' , template_button_strip($tpbuttons, 'bottom'), '</table>
	<div class="tborder" style="clear: both;"><div class="catbg" style="padding: 5px;">TP ' . $txt['tp-frontpage'] . '</div></div>
		
	';
}
function template_tpfrontpagetopics_below()
{
	return;
}

// tag them topics!
function template_TPtagtopicsGeneral_above()
{
	global $context, $boardurl, $txt , $topic, $scripturl;

	// not in wireless
	if(WIRELESS)
		return;

	$tags=TPsshowgtags('tpadmin_topictags', 'tpadmin_topictags', $topic, true);
	$taglinks=TPget_globaltags($tags,$topic);
	$any=tp_renderglobaltags($taglinks,true);
	if(!empty($any))
		echo '
	<div class="tborder">
		<div class="titlebg" style="padding: 4px;">' . $txt['tp-showrelated'] . '</div>
		<div class="windowbg" style="padding: 4px; ">
		' , $any , '
		</div>
	</div>';

}

function template_TPtagtopicsGeneral_below()
{
	return;
}

function template_tperror_above()
{
	global $context;

	echo '<div style="color: red; padding: 1em; background-color: #fffdfd; border: 2px solid; margin-bottom: 1em;">
			<div style="padding: 1em;">'.$context['TPortal']['tperror'].'</div>
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
	global $txt, $context, $scripturl, $settings;

	if(!empty($context['TPortal']['tptabs']))
	{
		$buts=array(); 
		echo '
	<div class="tptabs">';
		foreach($context['TPortal']['tptabs'] as $tab)
			$buts[] = '<a' . ($tab['is_selected'] ? ' class="tpactive"' : '') . ' href="' . $tab['href'] . '">' . $tab['title'] . '</a>';

		echo implode(" | ", $buts) , '
	</div>';
	}
}

function template_tptabs_below() 
{
	global $txt, $context, $scripturl, $settings;

}

function TPblock($block, $theme, $side, $double=false)
{
	global $context , $scripturl, $settings, $language , $txt;

	// setup a container that can be massaged through css
	echo '
	<div class="block_' . $side . 'container">';
	
	if(function_exists('ctheme_tp_getblockstyles'))
		$types = ctheme_tp_getblockstyles();
	else
		$types = tp_getblockstyles();

	// check
	if($block['var4']=='')
		$block['var4']=0;

	if($block['var4']==0)
		$block['var4'] = $context['TPortal']['panelstyle_'.$side];	

	// its a normal block..
	if(in_array($block['frame'],array('theme','frame','title','none')))
	{
		echo	'
	<div class="', (($theme || $block['frame']=='frame') ? 'tborder tp_'.$side.'block_frame' : 'tp_'.$side.'block_noframe'), '">';

		// show the frame and title
		if ($theme || $block['frame'] == 'title')
		{
			echo $types[$block['var4']]['code_title_left'];

			if($block['visible']=='' || $block['visible']=='1')
				echo '<a href="javascript: void(0); return false" onclick="toggle(\''.$block['id'].'\'); return false"><img id="blockcollapse'.$block['id'].'" style="margin: 0 0 0 4px; " align="right" src="' .$settings['tp_images_url']. '/' , !in_array($block['id'],$context['TPortal']['upshrinkblocks'])  ? 'TPcollapse' : 'TPexpand' , '.gif" border="0" alt="" title="'.$txt['block-upshrink_description'].'" /></a>';

			// can you edit the block?
			if($block['can_edit'] && !$context['TPortal']['blocks_edithide'])
				echo '<a href="',$scripturl,'?action=tpmod;sa=editblock'.$block['id'].';sesc=' . $context['session_id'].'"><img style="margin: 0 4px 0 0;" border="0" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';
			elseif($block['can_manage'] && !$context['TPortal']['blocks_edithide'])
				echo '<a href="',$scripturl,'?action=tpadmin;blockedit='.$block['id'].';sesc=' . $context['session_id'].'"><img border="0" style=" 0 4px 0  0;" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';

			echo $block['title'];
			echo $types[$block['var4']]['code_title_right'];
		}
		else
		{
			if(($block['visible']=='' || $block['visible']=='1') && $block['frame']!='frame')
			{
				echo '
		<div style="padding: 4px;">';
				if($block['visible']=='' || $block['visible']=='1')
					echo '<a href="javascript: void(0); return false" onclick="toggle(\''.$block['id'].'\'); return false"><img id="blockcollapse'.$block['id'].'" style="margin: 0 4px 0 0;" align="right" src="' .$settings['tp_images_url']. '/' , !in_array($block['id'],$context['TPortal']['upshrinkblocks']) ? 'TPcollapse' : 'TPexpand' , '.gif" border="0" alt="" title="'.$txt['block-upshrink_description'].'" /></a>';
				echo '&nbsp;
		</div>';
			}
		}
		echo '
		<div class="', (($theme || $block['frame']=='frame') ? 'tp_'.$side.'block_body' : ''), '"', in_array($block['id'],$context['TPortal']['upshrinkblocks']) ? ' style="display: none;"' : ''  , ' id="block'.$block['id'].'">';
		if($theme || $block['frame']=='frame')	
			echo $types[$block['var4']]['code_top'];

		$func = 'TPortal_' . $block['type'];
		if (function_exists($func))
		{
			if($double)
			{
				// figure out the height
				$h=$context['TPortal']['blockheight_'.$side];
				if(substr($context['TPortal']['blockheight_'.$side],strlen($context['TPortal']['blockheight_'.$side])-2,2)=='px')
					$nh = ((substr($context['TPortal']['blockheight_'.$side],0,strlen($context['TPortal']['blockheight_'.$side])-2)*2) + 43).'px';
				elseif(substr($context['TPortal']['blockheight_'.$side],strlen($context['TPortal']['blockheight_'.$side])-1,1)=='%')
					$nh= (substr($context['TPortal']['blockheight_'.$side],0,strlen($context['TPortal']['blockheight_'.$side])-1)*2).'%';
			}
			echo '<div class="blockbody" style="overflow: auto;' , !empty($context['TPortal']['blockheight_'.$side]) ? 'height: '. ($double ? $nh : $context['TPortal']['blockheight_'.$side]) .';' : '' , '">';
			$func($block['id']);
			echo '</div>';
		}
		else
			echo '<div class="blockbody" style="overflow: auto;' , !empty($context['TPortal']['blockheight_'.$side]) ? 'height: '.$context['TPortal']['blockheight_'.$side].';' : '' , '">' , parse_bbc($block['body']) , '</div>';

		if($theme || $block['frame']=='frame')	
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
			$context['TPortal']['blocktheme'][$block['frame']]=array(
				'frame' =>		array(
									'before' => '',
									'after' => ''),
				'title' =>		array(
									'before' => '',
									'after' => ''),
				'body' =>		array(
									'before' => '',
									'after' => '')
				);
				
		
		echo $context['TPortal']['blocktheme'][$block['frame']]['frame']['before'];
		echo $context['TPortal']['blocktheme'][$block['frame']]['title']['before'];

		// can you edit the block?
		if($block['can_edit'] && !$context['TPortal']['blocks_edithide'])
			echo '<a href="',$scripturl,'?action=tpmod;sa=editblock'.$block['id'].';sesc='.$context['session_id'].'"><img style="margin-right: 4px;" border="0" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';
		elseif($block['can_manage'] && !$context['TPortal']['blocks_edithide'])
			echo '<a href="',$scripturl,'?action=tpadmin;blockedit'.substr($side,0,1).'='.$block['id'].';sesc='.$context['session_id'].'"><img border="0" style="margin-right: 4px;" align="right" src="' .$settings['tp_images_url']. '/TPedit2.gif" alt="" title="'.$txt['edit_description'].'" /></a>';

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
	global $context, $txt;
	$code = '';
	if($type == 1)
	{
		// decide the header style, different for forumposts
		$headerstyle = isset($context['TPortal']['article']['boardnews']) ? 'catbg' : 'titlebg';
		
		$code = '
	<div style="margin-bottom: 4px;"
	' . ($context['TPortal']['article']['frame'] == 'theme' ? '
	 class="tborder"' : '') . '>
		<h3 style="padding: 0.4em; margin: 0;" ' . (in_array($context['TPortal']['article']['frame'], array('theme','title')) ? ' class="' . $headerstyle . '"' : ' class="article_title"') . '>{article_shortdate} {article_title} </h3>
		<div' . ($context['TPortal']['article']['frame'] == 'theme' ? ' style="padding: 0; overflow: hidden;"' : '') . '>
			' . (!$single ? '{article_avatar}' : '') .  '
			<div class="article_info">
				{article_category}
				{article_author} 
				{article_date} 
				{article_views} 
				{article_rating} 
				{article_options} 
			</div>
			<div class="article_padding">{article_text}</div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '<div class="article_padding">{article_bookmark}</div>' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '<div class="article_padding">{article_boardnews}</div>' : '') . '
			' . ($single ? '
			{article_moreauthor}
			{article_bookmark}
			{article_morelinks}
			{article_globaltags}
			{article_comments}' : '') . ' 
		</div>
	</div>';
	}
	elseif($type == 2)
	{
		$code = '
	<div class="article" style="padding: 0 0.5em;">
		<div class="article_iconcolumn">{article_iconcolumn}</div>
		<div class="render2">
			<h3 class="article_title titlebg" style="margin: 0; padding: 0; border: none;"><span class="left"></span>{article_shortdate} {article_title} </h3>
			<div class="article_info' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg2' : '') . '" style="border: none; margin-top: 2px;">
				{article_category}
				{article_author}
				{article_date} 
				{article_views} 
				{article_rating} 
				{article_options} 
			</div>
			<div class="article_padding windowbg2">{article_text}</div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '<div class="article_padding">{article_bookmark}</div>' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '<div class="article_padding">{article_boardnews}</div>' : '') . '
			' . ($single ? '
			<div class="tp_container' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg2' : '') . '">
				<div class="tp_col8">	
					{article_moreauthor}
				</div>
				<div class="tp_col8">
					{article_bookmark}
				</div>
			</div>
			<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg2' : '') . '">{article_morelinks}</div>
			<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg2' : '') . '">{article_globaltags}</div>
			<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? ' windowbg2' : '') . '">{article_comments}</div>' : '') . ' 
		</div>
	</div>';
		if($first)
			$code .= '<br /><br />'; 
	}
	elseif($type == 3)
	{
		// decide the header style, different for forumposts
		$headerstyle = isset($context['TPortal']['article']['boardnews']) ? '' : '2';

		if(!$first)
			$code = '
		<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? 'windowbg'.$headerstyle : '') . '" style="padding: 0; margin: 0 8px 4px 8px;">
			' . ($context['TPortal']['article']['frame'] == 'theme' ? '<span class="topslice"><span></span></span>' : '') . '
			<div style="padding: 0 1em;">	
				<span class="smalltext">{article_shortdate}</span> <strong>{article_title}</strong>
				<div class="smalltext" style="padding: 0.5em 0;">{article_text}</div>
			</div>
			' . ($context['TPortal']['article']['frame'] == 'theme' ? '<span class="botslice"><span></span></span>' : '') . '
		</div>';
		else
			$code = '
	<div class="article" style="padding: 0 0.7em; margin-bottom: 4px;">
		' . ($context['TPortal']['article']['frame'] == 'theme' ? '<span class="upperframe"><span></span></span>
		<div class="roundframe">' : '') . '
			<h3 class="titlebg article_title" style="padding: 0;margin: 0; border: none;"><span class="left"></span>{article_shortdate} {article_title} </h3>
			<div class="article_iconcolumn" style="margin: 10px;">{article_iconcolumn}</div>
			<div class="article_info">
				{article_category}
				{article_author}
				{article_date} 
				{article_views} 
				{article_rating} 
				{article_options} 
			</div>
			<div class="article_padding">{article_text}</div>
			' . (!isset($context['TPortal']['article']['boardnews']) && !$single ? '<div >{article_bookmark}</div>' : '') . '
			' . (isset($context['TPortal']['article']['boardnews']) ? '<div>{article_boardnews}</div>' : '') . '
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
			{article_globaltags}
			{article_comments}' : '') . ' 
		</div>
		' . ($context['TPortal']['article']['frame'] == 'theme' ? '<span class="lowerframe"><span></span></span>' : '') . '
	</div>
		';
	}
	elseif($type == 4)
	{
		$code = '
	<div class="tparticle" style="padding: 0 0.5em; margin-bottom: 1em;">
		<div class="article_picturecolumn">{article_picturecolumn}</div>
		<div class="render4">
			<h3 class="catbg"><span class="left"></span>{article_title} </h3>
			<div class="article_info">
				{article_category}
				{article_author}
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
			{article_globaltags}
			{article_comments}' : '') . ' 
		</div>
	</div><br />';
	}
	elseif($type == 5)
	{
		if(!$first)
			$code = '
		<h3 class="' . (isset($context['TPortal']['article']['boardnews']) ? ' catbg' : ' titlebg') . '" style="margin-top: 3px; font-weight: normal;"><span class="left"></span>{article_title}</h3>';
		else
			$code = '
	<div class="tparticle">
		<h3 class="article_title ' . (isset($context['TPortal']['article']['boardnews']) ? 'catbg' : 'titlebg') . '"><span class="left"></span>{article_shortdate} <strong>{article_title}</strong> </h3>
		<div class="' . ($context['TPortal']['article']['frame'] == 'theme' ? 'windowbg2' : '') . '">
			' . ($context['TPortal']['article']['frame'] == 'theme' ? '<span class="topslice"><span></span></span>' : '') . '
				<div class="article_info">
					{article_category}
					{article_author}
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
				{article_globaltags}
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
			<div><span style="font-size: 90%; letter-spacing: 1px;">{article_shortdate}</span> &nbsp;<strong style="font-size: 105%;">{article_title}</strong></div>
			<div class="catlayout6_text">
				{article_text}
			</div><br />
		</div>';
		else
			$code = '
		<div class="tborder">
			<h3 class="article_title catbg"><span class="left"></span>{article_title}</h3>
			<div class="article_info">
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
			<div' . ($context['TPortal']['article']['frame'] == 'theme' ? ' class="windowbg2"' : '')  . '>{article_globaltags}</div>
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
				{article_category}
				{article_author}
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
			{article_globaltags}
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
		$code = '
		<span class="article_date"> ' . (timeformat($context['TPortal']['article']['date'])) . '</span>';
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}

function article_iconcolumn($render=true)
{
	global $context, $scripturl, $settings;
	
	if(!empty($context['TPortal']['article']['avatar'])) 
		$code = '
	<div style="overflow: hidden;">
		' . $context['TPortal']['article']['avatar'] . '
	</div>'; 
	else
		$code = '
	<div style="overflow: hidden;">
		<img src="' . $settings['tp_images_url'] . '/TPnoimage' . (isset($context['TPortal']['article']['boardnews']) ? '_forum' : '') . '.gif" alt="" />
	</div>'; 

	if($render) { echo $code; } else { return $code; }
}

function article_picturecolumn($render=true)
{
	global $context, $scripturl, $settings, $boardurl;
	
	if(!empty($context['TPortal']['article']['illustration']) && !isset($context['TPortal']['article']['boardnews'])) 
		$code = '
	<div style="width: 128px; height: 128px; background: top right url(' . $boardurl . '/tp-files/tp-articles/illustrations/' . $context['TPortal']['article']['illustration'] . ') no-repeat;"></div>'; 
	elseif(!empty($context['TPortal']['article']['illustration']) && isset($context['TPortal']['article']['boardnews'])) 
		$code = '
	<div style="width: 128px; height: 128px; background: top right url(' . $context['TPortal']['article']['illustration'] . ') no-repeat;"></div>'; 
	else
		$code = '
	<div style="width: 128px; height: 128px; background: top right url(' . $settings['tp_images_url'] . '/TPno_illustration.gif) no-repeat;"></div>'; 

	if($render) { echo $code; } else { return $code; }
}

function article_shortdate($render=true)
{
	global $context, $txt;

	if(in_array('date',$context['TPortal']['article']['visual_options']))
		$code = '
		<span class="article_shortdate">' . tptimeformat($context['TPortal']['article']['date'], true, '%d %b %Y').' - </span>';
	else
		$code = '';

	if($render) { echo $code; } else { return $code; }
}

function article_boardnews($render=true)
{
	global $context, $scripturl, $txt;
	
	if(!isset($context['TPortal']['article']['replies']))
		return;

	$code = '<div class=" tp_pad">
		<span class="article_boardnews">
			<a href="' . $scripturl . '?topic=' . $context['TPortal']['article']['id'] . '.0">' . $context['TPortal']['article']['replies'] . ' ' . ($context['TPortal']['article']['replies'] == 1 ? $txt['smf_news_1'] : $txt['smf_news_2']) . '</a>';
	if($context['TPortal']['article']['locked'] == 0)
		$code .= '
			&nbsp;|&nbsp;' . '<a href="' . $scripturl . '?action=post;topic=' . $context['TPortal']['article']['id'] . '.' . $context['TPortal']['article']['replies'] . ';num_replies=' . $context['TPortal']['article']['replies'] . '">' . $txt['smf_news_3'] . '</a>';
	
	$code .= '
		</span></div>';

	if($render) { echo $code; } else { return $code; }
}

function article_author($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('author',$context['TPortal']['article']['visual_options']))
	{
		if($context['TPortal']['article']['dateRegistered']>1000)
			$code = '
		<span class="article_author">' . $txt['tp-by'] . ' <a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['authorID'] . '">' . $context['TPortal']['article']['realName'] . '</a></span>';
		else
			$code = '
		<span class="article_author">' . $txt['tp-by'] . ' ' . $context['TPortal']['article']['realName'] . '</span>';
	}
	else
		$code = '';

	if($render) { echo $code; } else { return $code; }
}

function article_views($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('views',$context['TPortal']['article']['visual_options']))
		$code = '
		<span class="article_views">' . $context['TPortal']['article']['views'] . ' ' . $txt['tp-views'] . '</span>';
	else
		$code = '';

	if($render) { echo $code; } else { return $code; }
}

function article_title($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('title',$context['TPortal']['article']['visual_options']))
	{
		if(isset($context['TPortal']['article']['boardnews']))
			$code = '
		<a href="' . $scripturl . '?topic=' . $context['TPortal']['article']['id'] . '">' . $context['TPortal']['article']['subject'] . '</a>';
		else
			$code = '
		<a href="' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . '">' . $context['TPortal']['article']['subject'] . '</a>';
	}
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}

function article_category($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(!empty($context['TPortal']['article']['category_name']))
	{
		if(isset($context['TPortal']['article']['boardnews']))
			$code = '
		<span class="article_category">' . $txt['tp-fromcategory'] . '<a href="' . $scripturl . '?board=' . $context['TPortal']['article']['category'] . '">' . $context['TPortal']['article']['category_name'] . '</a></span>';
		else
			$code = '
		<span class="article_category">' . $txt['tp-fromcategory'] . '<a href="' . $scripturl . '?cat=' . $context['TPortal']['article']['category'] . '">' . $context['TPortal']['article']['category_name'] . '</a></span>';
	}
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}

function article_lead($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('lead',$context['TPortal']['article']['visual_options']))
		$code = '
	<div class="article_lead">' . tp_renderarticle('intro') . '</div>';
	else
		$code = '';

	if($render) { echo $code; } else { return $code; }
}

function article_options($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	$code = '';
	if(!isset($context['TPortal']['article']['boardnews']))
	{
		// give 'em a edit link? :)
		if(allowedTo('tp_articles') && $context['TPortal']['hide_editarticle_link']=='0')
			$code .= '
					<span class="article_rating"><a href="' . $scripturl . '?action=tpadmin;sa=editarticle' . $context['TPortal']['article']['id'] . '">' . $txt['tp-edit'] . '</a></span>';
		// their own article?
		elseif(allowedTo('tp_editownarticle') && !allowedTo('tp_articles') && $context['TPortal']['article']['authorID'] == $context['user']['id'] && $context['TPortal']['hide_editarticle_link'] == '0')
			$code .= '
					<span class="article_rating"><a href="' . $scripturl . '?action=tpmod;sa=editarticle' . $context['TPortal']['article']['id'] . '">' . $txt['tp-edit'] . '</a></span>';
		else
			$code .= '';
		
	}	
	if($context['TPortal']['print_articles'] == 0 || !in_array('author',$context['TPortal']['article']['visual_options']))
		$code .= '';
	else
	{
		if(isset($context['TPortal']['article']['boardnews']))
			$code .= '
		<span class="article_rating"><a href="' . $scripturl . '?action=printpage;topic=' . $context['TPortal']['article']['id'] . '">' . $txt[465] . '</a></span>';
		else
			$code .= '
		<span class="article_rating"><a href="' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . ';print">' . $txt['tp-print'] . '</a></span>';
	}
	if($render) { echo $code; } else { return $code; }
}

function article_text($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	$code = '
	<div class="article_bodytext">' . tp_renderarticle() . '</div>';

	if($render) { echo $code; } else { return $code; }
}

function article_rating($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('rating',$context['TPortal']['article']['visual_options']))
	{
		if(!empty($context['TPortal']['article']['voters']))
			$code = '
		<span class="article_rating">' . (render_rating($context['TPortal']['article']['rating'], count(explode(",",$context['TPortal']['article']['voters'])), $context['TPortal']['article']['id'], (isset($context['TPortal']['article']['can_rate']) ? $context['TPortal']['article']['can_rate'] : false))) . '</span>';
		else
			$code = '
		<span class="article_rating">' . (render_rating($context['TPortal']['article']['rating'], 0 , $context['TPortal']['article']['id'], (isset($context['TPortal']['article']['can_rate']) ? $context['TPortal']['article']['can_rate'] : false))) . '</span>';
	}
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}

function article_moreauthor($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('avatar', $context['TPortal']['article']['visual_options']))
	{
		if($context['TPortal']['article']['dateRegistered']>1000)
			$code = '<br />
		<div class="article_authorinfo tp_pad">
			<h3>'.$txt['tp-authorinfo'].'</h3>
			' . ( !empty($context['TPortal']['article']['avatar']) ? '<a class="avatar" href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['authorID'] . '" title="' . $context['TPortal']['article']['realName'] . '">' . $context['TPortal']['article']['avatar'] . '</a>' : '') . '
			<div class="authortext">
				<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['authorID'] . '">' . $context['TPortal']['article']['realName'] . '</a>' . $txt['tp-poster1'] . $context['forum_name'] . $txt['tp-poster2'] . timeformat($context['TPortal']['article']['dateRegistered']) . $txt['tp-poster3'] . 
				$context['TPortal']['article']['posts'] . $txt['tp-poster4'] . timeformat($context['TPortal']['article']['lastLogin']) . '.
			</div>			
		</div><br />';
		else
			$code = '
		<div class="article_authorinfo tp_pad">
			<h3>'.$txt['tp-authorinfo'].'</h3>
			<div class="authortext">
				<em>' . $context['TPortal']['article']['realName'] . $txt['tp-poster5'] .  '</em>
			</div>			
		</div>';
	}
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}

function article_avatar($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('avatar', $context['TPortal']['article']['visual_options']))
	{
		$code = (!empty($context['TPortal']['article']['avatar']) ? '<div class="avatar_single" style="margin: 0 8px;"><a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['article']['authorID'] . '" title="' . $context['TPortal']['article']['realName'] . '">' . $context['TPortal']['article']['avatar'] . '</a></div>' : '');
	}
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}
function article_bookmark($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('social',$context['TPortal']['article']['visual_options']))
		$code = '
		<div class="article_socialbookmark">
			<a href="http://twitter.com/home/?status=' . $scripturl.'?page='. $context['TPortal']['article']['id'] . '" target="_blank"><img title="Share on Twitter!" src="' . $settings['tp_images_url'] . '/twitter.gif" alt="Share on Twitter!" /></a>
			<a href="http://digg.com/submit?url=' . $scripturl.'?page='. $context['TPortal']['article']['id'] . '&title=' . $context['TPortal']['article']['subject'].'" target="_blank"><img title="Digg this story!" src="' . $settings['tp_images_url'] . '/digg.gif" alt="Digg this story!" /></a>
			<a href="http://del.icio.us/post?url=' . $scripturl.'?page=' . $context['TPortal']['article']['id'] . '&title=' . $context['TPortal']['article']['subject'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/delicious.gif" alt="Del.icio.us" title="Del.icio.us" /></a>
			<a href="http://www.facebook.com/sharer.php?u=' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/facebook.gif" alt="Share on Facebook!" title="Share on Facebook!" /></a>
			<a href="http://www.technorati.com/faves?add=' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/technorati.gif" alt="Technorati" title="Technorati" /></a>
			<a href="http://www.reddit.com/submit?url=' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/reddit.gif" alt="Reddit" title="Reddit" /></a>
			<a href="http://www.stumbleupon.com/submit?url=' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . '" target="_blank"><img src="' . $settings['tp_images_url'] . '/stumble_upon_icon.gif" alt="StumbleUpon" title="Stumbleupon" /></a>
		</div>';
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}

function article_globaltags($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(!isset($context['TPortal']['article']['global_tag']))
		return;

	$taglinks = TPget_globaltags($context['TPortal']['article']['global_tag'] , $context['TPortal']['article']['id']);

	if(in_array('globaltags', $context['TPortal']['article']['visual_options']) && !empty($taglinks))
	{
		$code = '
		<div class="article_gtags tp_pad">
			<h3>' . $txt['tp-showrelated'] . '</h3>';
		
		$code .= '
			<ul>';
		foreach($taglinks as $tag)
				$code .= '
				<li><a href="' . $scripturl . $tag['href'] . '"' . ($tag['type']=='tparticle_itemtags' && $tag['itemid']==$context['TPortal']['article']['id'] ? ' class="selected"' : '') . '>' . strip_tags(html_entity_decode($tag['title'], ENT_QUOTES, $context['character_set']), '<a>') . '</a></li>';

		$code .= '
			</ul>';
		$code .= '
		</div>';
	}
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}

function article_comments($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	$code = '';

	if(in_array('comments',$context['TPortal']['article']['visual_options']))
	{
		$code = '
<h3 class="catbg">' .	$txt['tp-comments'] . '  ' . (tp_hidepanel('articlecomments', false)) . '</h3>
<div class="windowbg2 tp_pad">	
	<div id="articlecomments"' . (in_array('articlecomments',$context['tp_panels']) ? ' style="display: none;"' : '') . '>';
		$code .= '
		<div class="comments">';

		$counter = 1;
		if(isset($context['TPortal']['article']['comment_posts']))
		{
			foreach($context['TPortal']['article']['comment_posts'] as $comment)
			{
				$code .= '
				<div class="single">
					<span class="comment_author">' . (!empty($comment['avatar']['image']) ? $comment['avatar']['image'] : '') . '</span>
					<span class="counter">' . $counter++ .'</span>
					<strong class="subject">' . $comment['subject'] . '</strong>
					' . (($comment['is_new'] && $context['user']['is_logged']) ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/new.gif" alt="" />' : '') . '
					<span class="author"> '.$txt['tp-by'].' <a href="'.$scripturl.'?action=profile;u='.$comment['posterID'].'">'.$comment['poster'].'</a>
					' . $txt['on'] . ' ' . $comment['date'] . '</span>
					<div class="text">' . $comment['text'] . '</div>';

				// can we edit the comment or are the owner of it?
				if(allowedTo('tp_articles') || $comment['posterID'] == $context['user']['id'])
					$code .= '
					<a class="delete" href="' . $scripturl . '?action=tpmod;sa=killcomment' . $comment['id'] . '" onclick="javascript:return confirm(\'' . $txt['tp-confirmdelete'] . '\')">' . $txt['tp-delete'] . '</a>';

				$code .= '
				</div>';
			}
		}
		$code .= '
		</div>';
		if(in_array('commentallow',$context['TPortal']['article']['visual_options']) && $context['user']['is_logged'])
		{
			$code .= '
			<br />
			<form accept-charset="' . $context['character_set'] . '"  name="tp_article_comment" action="' . $scripturl . '?action=tpmod;sa=comment" method="post" style="margin: 0; padding: 0;">
				<div class="tp_container">
					<div class="tp_col16">
						<input name="tp_article_comment_title" type="text" style="width: 99%;" value="Re: ' . strip_tags($context['TPortal']['article']['subject']) . '">
						<textarea style="width: 99%; height: 8em;" name="tp_article_bodytext"></textarea>
					</div>
					<div class="tp_col16">';

			if (!empty($context['TPortal']['articles_comment_captcha']))
			{
				if ($context['use_graphic_library'])
					$code .= '
						<img src="' . $context['verification_image_href'] . '" alt="' . $txt['tp-visual_verification_description'] . '" style="margin: 0 5px 5px 5px;" id="verificiation_image" />';
				else
					$code .= '
						<img src="' . $context['verification_image_href'] . ';letter=1" alt="' . $txt['tp-visual_verification_description'] . '" id="verificiation_image_1" />
						<img src="' . $context['verification_image_href'] . ';letter=2" alt="' . $txt['tp-visual_verification_description'] . '" id="verificiation_image_2" />
						<img src="' . $context['verification_image_href'] . ';letter=3" alt="' . $txt['tp-visual_verification_description'] . '" id="verificiation_image_3" />
						<img src="' . $context['verification_image_href'] . ';letter=4" alt="' . $txt['tp-visual_verification_description'] . '" id="verificiation_image_4" />
						<img src="' . $context['verification_image_href'] . ';letter=5" alt="' . $txt['tp-visual_verification_description'] . '" id="verificiation_image_5" />';
				
				$code .= '
						<br /><input type="text" style="margin: 0 5px 5px 5px;" name="visual_verification_code" size="6" maxlength="6" tabindex="' . $context['tabindex']++ . '" />
						<br />&nbsp;&nbsp;<a href="' . $scripturl . '?page=' . $context['TPortal']['article']['id'] . '" onclick="refreshImages(); return false;">' . $txt['tp-newcaptcha'] . '</a>';
			}

			$code .= '
					</div>
				</div>
				<br />&nbsp;<input id="tp_article_comment_submit" type="submit" value="' . $txt['tp-submit'] . '">
				<input name="tp_article_type" type="hidden" value="article_comment">
				<input name="tp_article_id" type="hidden" value="' . $context['TPortal']['article']['id'] . '">
				<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
			</form>';
		}
		else
			$code .= '
			<div style="padding: 1ex;" class="smalltext"><em>' . $txt['tp-cannotcomment'] . '</em></div>';

		$code .= '
		</div></div>';
	}
	else
		$code .='';


	if($render) { echo $code; } else { return $code; }
}

function article_morelinks($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('category',$context['TPortal']['article']['visual_options']))
	{
		$code='';
		if(in_array('category',$context['TPortal']['article']['visual_options']) && isset($context['TPortal']['article']['others']))
		{
			$code .= '
			<h3 class="titlebg"><a href="' . $scripturl . '?cat='. (!empty($context['TPortal']['article']['value8']) ? $context['TPortal']['article']['value8'] : $context['TPortal']['article']['category']) .'">' . $txt['tp-articles'] . ' ' . $txt['smf88'] . ' &#171; ' . $context['TPortal']['article']['value1'] . ' &#187;</span></a></h3>
		<div class="windowbg2 tp_pad">
			<ul>';
			foreach($context['TPortal']['article']['others'] as $art)
				$code .= '
				<li' . (isset($art['selected']) ? ' class="selected"' : '') . '><a href="' . $scripturl . '?page=' . (!empty($art['shortname']) ? $art['shortname'] : $art['id']) . '">' . html_entity_decode($art['subject'], ENT_QUOTES, $context['character_set']) . '</a></li>';
			$code .= '
			</ul>
		</div>';
		}
	}
	else
		$code = '';

	if($render) { echo $code; } else { return $code; }
}

function render_rating($total, $votes, $id, $can_rate = false)
{
	global $txt, $context, $settings, $scripturl;
	
	if($total==0 && $votes>0)
		$code = $txt['tp-ratingaverage'] . ' 0 (' . $votes . ' ' . $txt['tp-ratingvotes'] . ')';
	elseif($total==0 && $votes==0)
		$code = $txt['tp-ratingaverage'] . ' 0 (0 ' . $txt['tp-ratingvotes'] . ')';
	else
		$code = $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src=" '. $settings['tp_images_url'].'/TPblue.gif" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />' , ceil($total/$votes))) : ceil($total/$votes)) . ' (' . $votes . ' ' . $txt['tp-ratingvotes'] . ')';

	// can we rate it?
	if($context['TPortal']['single_article'])
	{
		if($context['user']['is_logged'] && $can_rate)
		{
				$code .= '
			<form action="' . $scripturl . '?action=tpmod;sa=rate_article" style="margin: 0; padding: 0; display: inline;" method="post">
				<select size="1" name="tp_article_rating">';
				
				for($u=$context['TPortal']['maxstars'] ; $u>0 ; $u--)
					$code .= '
					<option value="' . $u . '">' . $u . '</option>';

				$code .= '
				</select>
				<input type="submit" name="tp_article_rating_submit" value="' . $txt['tp_rate'] . '">
				<input name="tp_article_type" type="hidden" value="article_rating">
				<input name="tp_article_id" type="hidden" value="' . $id . '">
				<input type="hidden" name="sc" value="' . $context['session_id'] . '" />
			</form>';
		}
		else
			$code .= '
			<em class="tp_article_rate smalltext">'. $txt['tp-dlhaverated'].'</em>';
	}	
	return $code;
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
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// use a customised template or the built-in?
	if(!empty($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['template']))
		render_template($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['template']);
	else
		render_template(blockarticle_renders());
}
function blockarticle_renders()
{
	global $context, $txt;

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

function blockarticle_date($render=true)
{
	global $context;
	
	if(in_array('date',$context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options']))
		$code = '
		<span class="article_date"> ' . (timeformat($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['date'])) . '</span>';
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}

function blockarticle_author($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('author',$context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options']))
	{
		if($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['dateRegistered']>1000)
			$code = '
		<span class="article_author">' . $txt['tp-by'] . ' <a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['authorID'] . '">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . '</a></span>';
		else
			$code = '
		<span class="article_author">' . $txt['tp-by'] . ' ' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . '</span>';
	}
	else
		$code = '';

	if($render) { echo $code; } else { return $code; }
}

function blockarticle_views($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('views',$context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options']))
		$code = '
		<span class="article_views">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['views'] . ' ' . $txt['tp-views'] . '</span>';
	else
		$code = '';

	if($render) { echo $code; } else { return $code; }
}

function blockarticle_text($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	$code = '
	<div class="article_bodytext">' . tp_renderblockarticle() . '</div>';

	if($render) { echo $code; } else { return $code; }
}

function blockarticle_moreauthor($render=true)
{
	global $scripturl, $txt, $settings, $context;
	
	if(in_array('avatar', $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['visual_options']))
	{
		if($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['dateRegistered']>1000)
			$code = '
		<div class="article_authorinfo">
			<h3>'.$txt['tp-authorinfo'].'</h3>
			' . ( !empty($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['avatar']) ? '<a class="avatar" href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['authorID'] . '" title="' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . '">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['avatar'] . '</a>' : '') . '
			<div class="authortext">
				<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['authorID'] . '">' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . '</a>' . $txt['tp-poster1'] . $context['forum_name'] . $txt['tp-poster2'] . timeformat($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['dateRegistered']) . $txt['tp-poster3'] . 
				$context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['posts'] . $txt['tp-poster4'] . timeformat($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['lastLogin']) . '.
			</div>			
		</div>';
		else
			$code = '
		<div class="article_authorinfo">
			<h3>'.$txt['tp-authorinfo'].'</h3>
			<div class="authortext">
				<em>' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['realName'] . $txt['tp-poster5'] .  '</em>
			</div>			
		</div>';
	}
	else
		$code='';

	if($render) { echo $code; } else { return $code; }
}
function category_childs()
{
	global $context, $scripturl, $settings;

	echo '
	<ul class="category_children">';
	foreach($context['TPortal']['category']['children'] as $ch => $child)
		echo '<li><a href="' , $scripturl , '?cat=' , $child['id'] , '">' , $child['value1'] ,' (' , $child['articlecount'] , ')</a></li>';
	
	echo '
	</ul>';

	return;
}

function template_subtab_above()
{
	global $context, $txt, $settings;

	if(sizeof($context['TPortal']['subtabs'])>1)
	{
		// The admin tabs.
		echo '
			<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 10px;">
				<tr>
					<td class="mirrortab_first">&nbsp;</td>';

		// Print out all the items in this tab.
		foreach ($context['TPortal']['subtabs']  as $tab)
		{
			if(!isset($tab['active']))
				$tab['active'] = $tab['is_selected'];

			if ($tab['active'])
			{
				echo '
					<td class="mirrortab_active_first">&nbsp;</td>
					<td valign="top" class="mirrortab_active_back">
						<a href="', $tab['href'], '">', $tab['title'], '</a>
					</td>
					<td class="mirrortab_active_last">&nbsp;</td>';
			}
			else
				echo '
					<td valign="top" class="mirrortab_back">
						<a href="', $tab['href'], '">', $tab['title'], '</a>
					</td>';
		}

		// the end of tabs
		echo '
					<td class="mirrortab_last">&nbsp;</td>
				</tr>
			</table>';
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
	<div>
		<div class="tborder" id="tpadmin_menu">
	<span class="upperframe"><span></span></span>
	<div class="roundframe"><div class="innerframe">
		<h3 class="catbg"><span class="left"></span>' . $txt['tp-tpmenu'] .'</h3>';

	
	if(is_array($context['admin_tabs']) && count($context['admin_tabs'])>0)
	{
		echo '
			<ul style="padding-bottom: 10px;">';
		foreach($context['admin_tabs'] as $ad => $tab)
		{
			echo '
				<li><div class="largetext">' , isset($context['admin_header'][$ad]) ? $context['admin_header'][$ad] : '' , '</div>
					';
			$tbas=array();
			foreach($tab as $tb)
				$tbas[]='<a href="' . $tb['href'] . '">' .($tb['is_selected'] ? '<b>'.$tb['title'].'</b>' : $tb['title']) . '</a>';
			
			// if new style...
			if($context['TPortal']['oldsidebar'] == 0)
				echo '<div class="normaltext">' , implode(", ",$tbas) , '</div>
				</li>';
			else
				echo '<div class="middletext" style="margin: 0; line-height: 1.3em;">' , implode("<br />",$tbas) , '</div>
				</li>';

		}
		echo '	
			</ul>';
	}

	echo '
	</div></div>
	<span class="lowerframe"><span></span></span>
		</div>
	</div>
	<div id="tpadmin_content">';
}

function template_tpadm_below()
{
	echo '
		
	</div>';
	return;
}

function template_tp_fatal_error()
{
	global $context, $settings, $options, $txt;

	echo '
	<div id="fatal_error">
		<h3 class="catbg"><span class="left"></span>
			', $txt['tp-error'], '
		</h3>
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
			return $txt['smf10'] . tptimeformat($log_time, $today_fmt, $format);

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
function tp_template_button_strip($button_strip, $direction = 'top', $force_reset = false, $custom_td = '')
{
	global $settings, $buttons, $context, $txt, $scripturl;

	if (empty($button_strip))
		return '<td>&nbsp;</td>';

	// Are we using right-to-left orientation?
	if ($context['right_to_left'])
	{
		$first = 'last';
		$last = 'first';
	}
	else
	{
		$first = 'first';
		$last = 'last';
	}

	echo '
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'last' : 'first' , '">&nbsp;</td>';

	// Create the buttons...
	foreach ($button_strip as $key => $value)
	{
		if(!isset($value['active']))
			$value['active'] = $value['is_selected'];

		echo $value['active'] ? '<td class="maintab_active_' . $first . '">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $value['active'] ? 'active_back' : 'back' , '">
					<a href="', $value['url'], '">' , $txt[$value['text']] , '</a>
				</td>' , $value['active'] ? '<td class="maintab_active_' . $last . '">&nbsp;</td>' : '';
	}
	echo '
		<td class="', $direction == 'top' ? 'main' : 'mirror', 'tab_' , $context['right_to_left'] ? 'first' : 'last' , '">&nbsp;</td>';
}
?>