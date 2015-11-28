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

function template_main()
{
	global $context, $settings, $txt, $scripturl, $modSettings, $boardurl;

	// if dl manager is off, throw a error screen but don't log it.
	if($context['TPortal']['show_download']==0 && !allowedTo('tp_dlmanager'))
		fatal_error($txt['tp-dlmanageroff'], false);
		
		echo '
	<div class="dl_container">
		<div class="title_bar">
			<h3 class="titlebg">',  $txt['tp-downloads'], '	</h3>
		</div>
		<div>';
		$dlbuttons = array(
			'frontdl' => array('text' => 'tp-downloads', 'image' => 'search.gif', 'lang' => true, 'url' => $scripturl . '?action=tpmod;dl' ),
			'search' => array('text' => 'tp-search', 'image' => 'search.gif', 'lang' => true, 'url' => $scripturl . '?action=tpmod;dl=search'),
			'stats' => array('text' => 'tp-stats', 'image' => 'stats.gif', 'lang' => true, 'url' => $scripturl . '?action=tpmod;dl=stats'),
			'upload' => array('text' => 'tp-dlupload', 'test' => 'can_tp_dlupload', 'image' => 'upload.gif', 'lang' => true, 'url' => $scripturl . '?action=tpmod;dl=upload'),
		);

		if(in_array($context['TPortal']['dlaction'],array('frontdl','search','stats','upload')))
			$dlbuttons[$context['TPortal']['dlaction']]['active'] = true;
		else
			$dlbuttons['frontdl']['active'] = true;

		echo '
		<div style="overflow: hidden; margin: 0 0 0.5em 0;">';
			template_button_strip($dlbuttons, 'right');

		echo '
		</div>';

	if($context['TPortal']['dlaction']=='' || $context['TPortal']['dlaction']=='cat')
	{

		if(!empty($context['TPortal']['dl_introtext']))
			echo '
			<div class="windowbg2" style="padding: 1em;">' . parse_bbc($context['TPortal']['dl_introtext']) . '</div>';

		if(!empty($context['TPortal']['dl_showfeatured']) && !empty($context['TPortal']['featured']))
		{
			echo '
				<div class="cat_bar">
					<h3 class="catbg">' . $txt['tp-dlfeatured'] . '</h3>
				</div>
				<div class="windowbg" style="overflow: hidden; padding: 1em;">'; 

			if(!empty($context['TPortal']['featured']['sshot']))
				 echo '
				<div class="screenshot" style="margin: 4px 4px 4px 1em;float: right; width: '.$context['TPortal']['dl_screenshotsize'][2].'px; height: '.$context['TPortal']['dl_screenshotsize'][3].'px;background: url('.$context['TPortal']['featured']['sshot'].') no-repeat;"></div>';

			echo '
				<h4 class="h4dl"><a href="' . $scripturl . '?action=tpmod;dl=item'.$context['TPortal']['featured']['id'].'">' . $context['TPortal']['featured']['name'] . '</a></h4>
				<span class="middletext">'. $txt['tp-uploadedby'] . ' <a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['featured']['authorID'].'">' . $context['TPortal']['featured']['author'].'</a></span>
				<p>' . $context['TPortal']['featured']['description'] , '</p>
			</div>';
		}

		// render last added and most downloaded.
	if($context['TPortal']['dl_showlatest']==1 && ($context['TPortal']['dl_showstats']==1))
	{
		echo '
		<span class="upperframe"><span></span></span>
        <div class="roundframe">
			<div class="">';
		if($context['TPortal']['dl_showlatest']==1)
			echo '
			<a href="javascript: void(0); " onclick="dlshowtab(\'dlrecent\');"><b>' , $txt['tp-recentuploads'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</b></a>';
		if($context['TPortal']['dl_showstats']==1)
		{	
			echo '
			 ' , $context['TPortal']['dl_showlatest']==1 ? '&nbsp;|&nbsp; ' : '' , '<a href="javascript: void(0);" onclick="dlshowtab(\'dlweekpop\');">' , $txt['tp-mostpopweek'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</a>
			&nbsp;|&nbsp; <a href="javascript: void(0); " onclick="dlshowtab(\'dlpop\');">' , $txt['tp-mostpop'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</a> ';
		}
		echo '
			</div>
        </div>
		<span class="lowerframe" style="margin-bottom: 5px;"><span></span></span>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
		<div style="padding: 1em;">';
	}

		if($context['TPortal']['dl_showlatest']==1)
		{	
			echo '	
			<div id="dlrecent">
			<h4 class="h4dl">' , $txt['tp-recentuploads'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</h4>';
		
			$count=1;
			if(!empty($context['TPortal']['dl_last_added']))
			{
				foreach($context['TPortal']['dl_last_added'] as $last)
				{
					echo '
			<div class="recentdl">';
					
					if(!empty($last['screenshot'])) 
						echo '<div style="margin-right: 15px; background: url('.$last['screenshot'].') no-repeat; float: left; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="windowbg3"></div>';
					else
						echo '<div style="margin-right: 15px; background: url('.$settings['tp_images_url'].'/TPnodl.png) 50% 50% no-repeat; float: left; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="windowbg3"></div>';

					echo '				
				<div class="dl_most_downloaded">
						<a href="'.$last['href'].'"><b>'.$last['name'].'</b></a> 
						<div class="smalltext"> '.$txt['tp-uploadedby'] .' ' . $last['author'].' '.$last['date'].'</div>
						<div class="smalltext">'.$last['downloads'].' '.strtolower($txt['tp-downloads']).'</div>
				</div>
			</div>';
				}
			}
			echo '	
			</div>';
		}
		if($context['TPortal']['dl_showstats']==1)
		{	
			echo '	
			<div id="dlweekpop" ' , $context['TPortal']['dl_showlatest']==1 ? 'style="display: none;"' : '' , '>
			<h4 class="h4dl">' , $txt['tp-mostpopweek'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</h4>';

			$count=1;
			if(!empty($context['TPortal']['dl_week_downloaded']))
			{
				foreach($context['TPortal']['dl_week_downloaded'] as $wost)
				{	
					echo '
				<div class="dl_most_downloaded">
					<div class="dl_number">'.$count.'.</div>
					<div class="dl_number_right">
						<a href="'.$wost['href'].'"><b>'.$wost['name'].'</b></a> 
						<div class="smalltext"> '.$txt['tp-uploadedby'] .' ' . $wost['author'].' '.$wost['date'].'</div>
						<div class="smalltext">'.$wost['downloads'].' '.strtolower($txt['tp-downloads']).'</div>
					</div>
				</div>';
					$count++;
				}		
			}
			echo '		
			</div>
			<div id="dlpop" style="display: none;">
			<h4 class="h4dl">' , $txt['tp-mostpop'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</h4>';

			$count=1;
			if(!empty($context['TPortal']['dl_most_downloaded']))
			{
				foreach($context['TPortal']['dl_most_downloaded'] as $wost)
				{
					echo '
				<div class="dl_most_downloaded">
					<div class="dl_number">'.$count.'.</div>
					<div class="dl_number_right">
						<a href="'.$wost['href'].'"><b>'.$wost['name'].'</b></a> 
						<div class="smalltext"> '.$txt['tp-uploadedby'] .' ' . $wost['author'].' '.$wost['date'].'</div>
						<div class="smalltext">'.$wost['downloads'].' '.strtolower($txt['tp-downloads']).'</div>
					</div>
				</div>';
					$count++;
				}
			}
		}
			echo '		
			</div>';
		echo '
		</div>
		<span class="botslice"><span></span></span>
	</div>';
	
	if($context['TPortal']['dl_showlatest'] ==0)
		echo '
		<script type="text/javascript"><!-- // --><![CDATA[
		function dlshowtab( target )
		{
			document.getElementById(\'dlpop\').style.display= \'none\';
			document.getElementById(\'dlweekpop\').style.display= \'none\';
			
			document.getElementById(target).style.display= \'\';
		}
	// ]]></script>';
	elseif($context['TPortal']['dl_showstats']==0)
	echo '';
	else
	echo '

	<script type="text/javascript"><!-- // --><![CDATA[
		function dlshowtab( target )
		{
			document.getElementById(\'dlrecent\').style.display= \'none\';
			document.getElementById(\'dlpop\').style.display= \'none\';
			document.getElementById(\'dlweekpop\').style.display= \'none\';
			
			document.getElementById(target).style.display= \'\';
		}
	// ]]></script>
	<br />
	
	<div class="cat_bar">
		<h3 class="catbg">'.$txt['tp-categories'] .'</h3>
	</div>	
	<div class="windowbg">';

		
		//show all categories
		foreach($context['TPortal']['dlcats'] as $dlcat)
		{
			// any subcategories?
			if(!empty($context['TPortal']['dlcatchilds']) && sizeof($context['TPortal']['dlcatchilds'])>1)
			{
				$content='';
				foreach($context['TPortal']['dlcatchilds'] as $dlchild)
				{
					if($dlchild['parent']==$dlcat['id'])
					{
						$content .= '
					<li>
						<img style="margin: 0;" alt="" src="' .$settings['tp_images_url']. '/TPboard.gif' . '" border="0" />
							<a href="'.$dlchild['href'].'">'.$dlchild['name'].'</a>';
						if($dlchild['files']>0)
							$content .= ' (' . $dlchild['files'].')';
						$content .= '
					</li>';
					}
				}
			}
			echo '
			<div class="dlcategory"' , !empty($content) ? ' style="margin-bottom: 0;"' : '' ,'>
				<div style="overflow: visible;">
				<img style="float: left; margin: 0 10px 5px 0;" src="' , !empty($dlcat['icon']) ? (substr($dlcat['icon'],0,4)=='http' ? $dlcat['icon'] :  $boardurl. '/' . $dlcat['icon']) : $settings['images_url'].'/board.gif' , '" alt="" />
					<div class="details">' ,	$dlcat['files']>0 ? $dlcat['files'].' '.$txt['tp-dlfiles'] : '0 '.$txt['tp-dlfiles'] , '</div>
					<h4><a href="'. $dlcat['href'] .'">'.$dlcat['name'].'</a></h4>
					<div class="post middletext">'. $dlcat['description'] . '</div>
				</div>
			</div>';
			if(!empty($content))
				echo '
			<div class="dlcategory" style="padding-left: 5em;"><ul class="tp-subcategories">'.$content.'</ul></div>';
		}

		// show any files?
		if($context['TPortal']['dlaction']=='cat' && sizeof($context['TPortal']['dlitem'])>0)		
		{		
			echo '
		</div>
		<div style="padding: 0.5em 1em;">
			<div style="overflow: hidden;">';
				
			if(!empty($context['TPortal']['sortlinks']))
				echo '
				<div style="float: right;">' . $context['TPortal']['sortlinks'] . '</div>';
			else
				echo $txt['tp-dlfiles'];
			
			if($context['TPortal']['dlaction']!='item' && !empty($context['TPortal']['pageindex']))
				echo '<div style="padding-bottom: 0.3em;">' . $context['TPortal']['pageindex'] . '</div>';
			echo '
			</div>
		</div>
		<div class="windowbg2" style="padding: 1em;">
			<div style="overflow: hidden;">';
	
			foreach($context['TPortal']['dlitem'] as $dlitem)
			{
				echo '
				<div class="dlitemgrid">
					<h4 class="h4dl" style="padding-bottom: 0; font-size: 1.3em;"><a href="'.$dlitem['href'].'">'. $dlitem['name'] .'</a></h4>
					<div  style="float: left; padding: 1em 1em 1em 0; ">' , $dlitem['icon']!='' ? '<img src="'. (substr($dlitem['icon'],0,4)=='http' ? $dlitem['icon'] :  $boardurl. '/' . $dlitem['icon']).'" border="0" alt="'.$dlitem['name'].'"  />' : '<img src="' . $settings['tp_images_url'] . '/TPnodl.png" alt="" />' , '	</div>';

				unset($details);
				$details=array();
				$det2=array();
				
				// edit the file?
				if(allowedTo('tp_dlmanager'))
					$details[] = '<a href="' . $scripturl . '?action=tpmod;dl=adminitem' . $dlitem['id'] . '">' . $txt['tp-edit'] . '</a>';
				elseif($dlitem['authorID']==$context['user']['id'])
					$details[] ='<a href="' . $scripturl . '?action=tpmod;dl=useredit' . $dlitem['id'] . '">' . $txt['tp-edit'] . '</a>';

				if(isset($dlitem['ingress']))
					echo '
					<div class="dlpost">' . $dlitem['ingress'] . '</div>';

				echo '
					<div class="post">' , $dlitem['description'] , '</div>';

				if(isset($dlitem['filesize']))
					$details[] = $dlitem['filesize'];
	
				$details[] = $dlitem['views'] . ' ' . $txt['tp-views'];
				$details[] = $dlitem['downloads'] . ' ' . $txt['tp-downloads'];
				$det2[] = $txt['tp-itemlastdownload'] . ' ' . timeformat($dlitem['date_last']);
				$det2[] = $dlitem['author'];
				echo '
					<div class="itemdetails smalltext">' , implode(" | ",$details) , '<br />' , implode(" | ",$det2) , '</div>
				<br /><br />
				</div>';
			}


			echo '
			</div>
		</div>
		<div class="windowbg" style="padding: 0.5em 1em;">
		';
			if($context['TPortal']['dlaction']!='item' && !empty($context['TPortal']['pageindex']))
				echo $context['TPortal']['pageindex'];
		}
		echo '
		</div>
	</div>';
	}
	elseif($context['TPortal']['dlaction']=='item')
	{
		echo '
	<div class="tborder">
		<div >';

		foreach($context['TPortal']['dlitem'] as $dlitem)
		{
			echo '
			<div class="windowbg">
				<span class="topslice"><span></span></span>
				<div class="content">
				<h4 class="h4dl">
				<a href="'.$dlitem['href'].'">'. $dlitem['name'] .'</a>';
			
			// edit the file?
			if(allowedTo('tp_dlmanager'))
				echo '&nbsp;&nbsp;<small>[<a href="' , $scripturl , '?action=tpmod;dl=adminitem' , $dlitem['id'] , '">' , $txt['tp-edit'] , '</a>]</small>';
			elseif($dlitem['authorID']==$context['user']['id'])
				echo '&nbsp;&nbsp;<small>[<a href="' , $scripturl , '?action=tpmod;dl=useredit' , $dlitem['id'] , '">' , $txt['tp-edit'] , '</a>]</small>';

			echo '
				</h4><hr>
					<ul class="tp_details" style="line-height: 1.4em; font-size: 0.95em;">
						<li>'  , $txt['tp-dlfilesize'] , ': ', isset($dlitem['filesize']) ? $dlitem['filesize']: '' , '</li>
						<li>'. $txt['tp-views']. ': ' . $dlitem['views'].' </li>
						<li>'.$txt['tp-downloads'].': '.$dlitem['downloads'].' </li>
						<li>' , $txt['tp-created'] . ': ' .  timeformat($dlitem['created']).'</li>
						<li>' , $txt['tp-itemlastdownload'] , ': ' . timeformat($dlitem['date_last']).'</li>
					</ul>	
					<div id="rating">' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' .$settings['tp_images_url']. '/TPblue.gif" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $dlitem['rating_average'])) : $dlitem['rating_average']) . ' (' . $dlitem['rating_votes'] . ' ' . $txt['tp-ratingvotes'] . ')</div>';

			if($dlitem['can_rate'])
			{
				echo '
					<form class="ratingoption" style="padding-left: 0;" name="tp_dlitem_rating" action="',$scripturl,'?action=tpmod;sa=rate_dlitem" method="post">
						' , $txt['tp-ratedownload'] , ' 
						<select size="1" name="tp_dlitem_rating">';
				for($u=$context['TPortal']['maxstars'] ; $u>0 ; $u--)
				{
					echo '
							<option value="'.$u.'">'.$u.'</option>';
				}
				echo '
						</select>
						<input type="submit" name="tp_dlitem_rating_submit" value="',$txt['tp_rate'],'" />
						<input name="tp_dlitem_type" type="hidden" value="dlitem_rating" />
						<input name="tp_dlitem_id" type="hidden" value="'.$dlitem['id'].'" />
						<input type="hidden" name="sc" value="', $context['session_id'], '" />
					</form>
				';
			}
			else
			{
			if (!$context['user']['is_guest'])
				echo '
					<div class="ratingoption"><em class="smalltext">'.$txt['tp-dlhaverated'].'</em></div>';
			}
			echo '
					<hr />
					<div class="post">
						<p class="floatright" style="padding: 0 0 0.1em 1em;"><a href="'.$dlitem['href'].'"><img src="' .$settings['tp_images_url']. '/TPdownloadfile.gif" alt="'.$txt['tp-download'].'" /></a></p>
						' . $dlitem['description'] . '
					</div>';

			// any extra files attached?
			if(isset($dlitem['subitem']) && is_array($dlitem['subitem']))
			{
				echo '
					<div class="morefiles">
						<h4>'.$txt['tp-dlmorefiles'].'</h4>
						<div class="post">
							<ul>';
				foreach($dlitem['subitem'] as $sub)
				{
					echo '
								<li><a href="' , $sub['href'] , '"><b>', $sub['name'] ,'</b></a>&nbsp;&nbsp;<span class="smalltext">' , $sub['filesize'], ' / ', $sub['downloads'],' ',$txt['tp-downloads'],' / ', $sub['views'],' ',$txt['tp-views'],'</span></li>';
				}
				echo '
							</ul>
						</div>
					</div>';
			}
		}
		// any screenshot?
		if(!empty($dlitem['sshot']))
			 echo '
					<br /><img src="'.$dlitem['bigshot'].'" style="max-width: 100%;" alt="" /></div>';
		echo '
			<span class="botslice"><span></span></span>
				</div>
			</div>
			</div>
		</div>
	</div>';
			
	}
	// the submit upload form
	elseif($context['TPortal']['dlaction']=='upload')
	{
		// check that you can upload
		if(allowedTo('tp_dlmanager') || allowedTo('tp_dlupload'))
			$show=true;
		else
			fatal_error($txt['tp-adminonly']);

	   echo '
	 <div class="tborder">
        <div class="title_bar">
            <h4 class="titlebg">'.$txt['tp-dlupload'].'</h4>
        </div>
		<div class="windowbg">
			<span class="topslice"><span></span></span>
		 <table width="100%" border="0" cellspacing="0" cellpadding="8">
			<tr class="windowbg"><td>
				<form accept-charset="', $context['character_set'], '" name="tp_dlupload" id="tp_dlupload" action="'.$scripturl.'?action=tpmod;dl=upload" method="post" enctype="multipart/form-data" onsubmit="submitonce(this);">
				';

		if($context['TPortal']['dl_approve']=='1' && !allowedTo('tp_dlmanager'))
			echo '<b>! '.$txt['tp-warnsubmission'].'</b><br />';

		echo '<div style="text-align: center;" class="smalltext">'. $txt['tp-maxuploadsize'].': '. $context['TPortal']['dl_max_upload_size'].'Kb</div><br />
					<table class="formtable">
						<tr>
							<td class="left" style="width: 130px;">'.$txt['tp-dluploadtitle'].'</td>
							<td class="windbg">
								<input name="tp-dluploadtitle" type="text" value="-no title-" size="40">
							</td>
						</tr><tr>
							<td valign="top" align="right" class="windowbg">'.$txt['tp-dluploadcategory'].'</td>
							<td class="windowbg">
								<select size="1" name="tp-dluploadcat">';

		foreach($context['TPortal']['uploadcats'] as $ucats)
		{
			echo '
									<option value="'.$ucats['id'].'">', !empty($ucats['indent']) ? str_repeat("-",$ucats['indent']) : '' ,' ' . $ucats['name'].'</option>';
		}
		echo '				</select>
							</td>
						</tr>
						<tr>
							<td align="right" class="windowbg">'.$txt['tp-dluploadtext'].'</td>
							<td class="windowbg">';

		if($context['TPortal']['dl_wysiwyg']== 'html')
			TPwysiwyg('tp_dluploadtext', '', true,'qup_tp_dluploadtext', $context['TPortal']['show_wysiwyg'], false);
		elseif($context['TPortal']['dl_wysiwyg']=='bbc')
			TP_bbcbox($context['TPortal']['editor_id']);
		else
			echo '<textarea name="tp_dluploadtext" rows=5 cols=50 wrap="on"></textarea>';

		echo '			</td>
						</tr>
						<tr>
							<td height="40" align="right" class="windowbg">'.$txt['tp-dluploadfile'].'</td>
							<td class="windowbg">';
		if((allowedTo('tp_dlmanager') && !isset($_GET['ftp'])) || !allowedTo('tp_dlmanager'))
			echo '<input name="tp-dluploadfile" id="tp-dluploadfile" type="file"> ('.$context['TPortal']['dl_allowed_types'].')';

		// file already uploaded?
		if(allowedTo('tp_dlmanager') && !isset($_GET['ftp'])){
			echo '<br /><input name="tp-dluploadnot" type="checkbox" value="ON"> '. $txt['tp-dlnoupload'];
		}
		elseif(allowedTo('tp_dlmanager') && isset($_GET['ftp'])){
			if(isset($_GET['ftp']))
				echo '<input name="tp-dluploadnot" type="hidden" value="ON"><input name="tp-dlupload_ftpstray" type="hidden" value="'.$_GET['ftp'].'">
				<b>'.$txt['tp-dlmakeitem2'].':</b><br />' . $context['TPortal']['tp-downloads'][$_GET['ftp']]['file'];

		}

		// make a new topic too? 
		if(allowedTo('tp_dlcreatetopic') || !empty($context['TPortal']['dl_create_topic']))
		{
			$allowed=explode(",",$context['TPortal']['dl_createtopic_boards']);
			if(empty($context['TPortal']['dl_createtopic_boards']))
			{
				echo '
					</td></tr>
					<tr>
						<td height="40" valign="top" align="right" class="windbg2">'.$txt['tp-dlcreatetopic'].'</td>
						<td class="windbg2">
							<div class="information">' . $txt['tp-dlmissingboards'] . '</div>';
			}
			else
			{
				echo '
						</td></tr>
						<tr>
							<td height="40" valign="top" align="right" class="windbg2">'.$txt['tp-dlcreatetopic'].'</td>
							<td class="windbg2">
						<input type="checkbox" name="create_topic" /> ' . $txt['tp-dlcreatetopic'] . '<br />';

				if(allowedTo('make_sticky') && !empty($modSettings['enableStickyTopics']))
					echo '
						<input type="checkbox" name="create_topic_sticky" /> ' . $txt['tp-dlcreatetopic_sticky'] . '<br />';
				if(allowedTo('announce_topic'))
					echo '
						<input type="checkbox" name="create_topic_announce" /> ' . $txt['tp-dlcreatetopic_announce'] . '<br />';
				
				echo '
									<select size="1" name="create_topic_board" style="margin: 3px;">';
				foreach($context['TPortal']['boards'] as $brd)
				{
					if(in_array($brd['id'],$allowed))
						echo '
										<option value="'.$brd['id'].'">', $brd['name'].'</option>';
				}
				echo '				</select> ', $txt['tp-dlchooseboard'], '
				<div style="padding: 5px;">
					<textarea name="create_topic_body" style="width: 100%; height: 200px;" rows=5 cols=50 wrap="on"></textarea>
				</div>';
			}
		}
		
		// can you attach it?
		if(!empty($context['TPortal']['attachitems']))
		{
			echo '		</td>
						</tr>
						<tr>
							<td align="right" valign="top" class="windowbg">'.$txt['tp-dluploadattach'].'</td>
							<td class="windowbg">
								<select size="1" name="tp_dluploadattach">
									<option value="0" selected>'.$txt['tp-none'].'</option>';
			foreach($context['TPortal']['attachitems'] as $att)
				echo '
									<option value="'.$att['id'].'">'.$att['name'].'</option>';
			echo '
								</select>';
		}
		echo '
							</td>
						</tr>
						<tr>
							<td align="right" valign="top" class="windowbg">'.$txt['tp-dluploadicon'].'</td>
							<td class="windowbg">
								<select size="1" name="tp_dluploadicon" onchange="dlcheck(this.value)">
									<option value="blank.gif" selected>'.$txt['tp-noneicon'].'</option>';
		// output the icons
		foreach($context['TPortal']['dlicons'] as $dlicon => $value)
			echo '
									<option value="'.$value.'">'.substr($value,0,strlen($value)-4).'</option>';

		echo '
								</select>
								<img align="top" style="margin-left: 2ex;" name="dlicon" src="' .$settings['tp_images_url']. '/TPblank.gif" alt="" />
							</td>
						</tr>
						<tr>
							<td align="right" class="windowbg">'.$txt['tp-dluploadpic'].'</td>
							<td height="80" class="windowbg">
								<input name="tp_dluploadpic" id="tp_dluploadpic" type="file" size="60">
								<input name="tp-uploadcat" type="hidden" value="'.$context['TPortal']['dlitem'].'">
								<input name="tp-uploaduser" type="hidden" value="'.$context['user']['id'].'">
							</td>
						<tr class="windowbg">
							<td colspan="2" align="center">
								<input type="submit" name="tp-uploadsubmit" id="tp-uploadsubmit" value="'.$txt['tp-dosubmit'].'">
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
			<span class="botslice"><span></span></span>
		</div>	
	</div>
	<script type="text/javascript">
		function dlcheck(icon)
		{
			document.dlicon.src= "'.$boardurl.'/tp-downloads/icons/" + icon
		 }
	</script>';
	}

	// show the stats page...
	if($context['TPortal']['dlaction'] == 'stats')
	{
		$maxcount = 10;
	   echo '
	<div class="tborder">
		<div class="cat_bar">
			<h3 class="catbg">'.$txt['tp-downloadsection'].' - '.$txt['tp-stats'].'</h3>
		</div>
		<table width="100%" border="0" cellspacing="1" cellpadding="5" class="bordercolor">
			<tr class="titlebg">
				<td colspan="2">'.$maxcount.' '.$txt['tp-dlstatscats'].'</td>
				<td colspan="2">'.$maxcount.' '.$txt['tp-dlstatsviews'].'</td>
			</tr>
			<tr>
				<td class="windowbg2" ><img src="' .$settings['tp_images_url']. '/TPboard.gif" alt="" /></td><td class="windowbg" valign="top" width="50%">';

		// top categories
		echo '<table width="100%" cellpadding="2" cellspacing="0">';
		$counter=0;
		if(isset($context['TPortal']['topcats'][0]['items']))
			$maxval=$context['TPortal']['topcats'][0]['items'];

		if(isset($context['TPortal']['topcats']) && count($context['TPortal']['topcats'])>0){
			foreach($context['TPortal']['topcats'] as $cats){
				if($counter<$maxcount){
					echo '
						<tr>
							<td width="60%">
								'.$cats['link'].'</td><td><img src="' .$settings['tp_images_url']. '/TPbar.gif" height="15" alt="" width="' , $cats['items']>0 ? ceil(100*($cats['items']/$maxval)) : '1' , '%" /></td><td  width="5%" style="padding-left: 2ex;">'.$cats['items'].'
							</td>
						</tr>';
					$counter++;
				}
			}
		}
		else
			echo '
						<tr><td>&nbsp;</td></tr>';

		echo '
					</table>';

		echo '
				</td>
				<td class="windowbg2">
					<img src="' .$settings['tp_images_url']. '/TPinfo.gif" alt="" />
				</td>
				<td class="windowbg" valign="top" width="50%">';

		// top views
		echo '<table width="100%" cellpadding="2" cellspacing="0">';
		$counter=0;
		if(isset($context['TPortal']['topviews'][0]['views']))
			$maxval=$context['TPortal']['topviews'][0]['views'];
		if(isset($context['TPortal']['topviews']) && count($context['TPortal']['topviews'])>0){
			foreach($context['TPortal']['topviews'] as $cats){
				if($counter<$maxcount){
					echo '
						<tr>
							<td width="60%">'.$cats['link'].'</td>
							<td>
								<img src="' .$settings['tp_images_url']. '/TPbar.gif" height="15" alt="" width="' , $cats['views']>0 ? ceil(100*($cats['views']/$maxval)) : '1' , '%" />
							</td>
							<td  width="5%" style="padding-left: 2ex;">'.$cats['views'].'</td>
						</tr>';
					$counter++;
				}
			}
		}
		else
			echo '
						<tr><td>&nbsp;</td></tr>';

		echo '
					</table>';

		echo '
				</td>
			</tr>
			<tr class="titlebg">
				<td colspan="2">'.$maxcount.' '.$txt['tp-dlstatsdls'].'</td>
				<td colspan="2">'.$maxcount.' '.$txt['tp-dlstatssize'].'</td>
			</tr>
			<tr>
				<td class="windowbg2"><img src="' .$settings['tp_images_url']. '/TPinfo2.gif" alt="" /></td>
				<td class="windowbg" valign="top">';

		// top downloads
		echo '
					<table width="100%" cellpadding="2" cellspacing="0">';
		$counter=0;
		if(isset($context['TPortal']['topitems'][0]['downloads']))
			$maxval=$context['TPortal']['topitems'][0]['downloads'];
		if(isset($context['TPortal']['topitems']) && count($context['TPortal']['topitems'])>0){
			foreach($context['TPortal']['topitems'] as $cats){
				if($counter<$maxcount){
					echo '
						<tr>
							<td width="60%">'.$cats['link'].'</td>
							<td><img src="' .$settings['tp_images_url']. '/TPbar.gif" height="15" alt="" width="' , ($maxval > 0) ? ceil(100*($cats['downloads']/$maxval)) : 0 , '%" /></td>
							<td width="5%">'.$cats['downloads'].'</td>
						</tr>';
					$counter++;
				}
			}
		}
		else
			echo '
						<tr><td>&nbsp;</td></tr>';

		echo '
					</table>';

		echo '
				</td>
				<td class="windowbg2"><img src="' .$settings['tp_images_url']. '/TPinfo2.gif" alt="" /></td>
				<td class="windowbg" valign="top">';

		// top filesize
		echo '
					<table width="100%" cellpadding="2" cellspacing="0">';
		$counter=0;
		if(isset($context['TPortal']['topsize'][0]['size']))
			$maxval=$context['TPortal']['topsize'][0]['size'];

		if(isset($context['TPortal']['topsize']) && count($context['TPortal']['topsize'])>0){
			foreach($context['TPortal']['topsize'] as $cats){
				if($counter<$maxcount){
					echo '
						<tr>
							<td width="60%">'.$cats['link'].'</td>
							<td><img src="' .$settings['tp_images_url']. '/TPbar.gif" height="15" alt="" width="' , ceil(100*($cats['size']/$maxval)) , '%" /></td>
							<td width="5%">'. floor($cats['size']/1000).'kb</td>
						</tr>';
					$counter++;
				}
			}
		}
		else
			echo '
						<tr><td>&nbsp;</td></tr>';

		echo '
					</table>
				</td>
			</tr>
		</table>
	</div>
	</div>';
	}

	// how about user-editing?
	if($context['TPortal']['dlaction']=='useredit')
	{
		foreach($context['TPortal']['dl_useredit'] as $cat)
		{
			echo '
		<form accept-charset="', $context['character_set'], '" name="dl_useredit" action="'.$scripturl.'?action=tpmod;dl=admin" enctype="multipart/form-data" onsubmit="syncTextarea();" method="post">
			<table width="100%" cellspacing="1" cellpadding="7" class="tborder">
				<tr>
					<td colspan="2" class="titlebg">'.$txt['tp-useredit'].'</td>
				</tr>
				<tr>
					<td class="windowbg2" align="right" width="25%">
						<a href="'.$scripturl.'?action=tpmod;dl=item'.$cat['id'].'">['.$txt['tp-preview'].']</a>
						'.$txt['tp-dluploadtitle'].'</td>
					<td valign="top" class="windowbg2"><input style="width: 30ex;" name="dladmin_name'.$cat['id'].'" type="text" value="'.$cat['name'].'">
					</td>
				<tr>
					<td valign="top" class="windowbg2" colspan="2">
						<br />';
						
				if($context['TPortal']['dl_wysiwyg'] == 'html')
					TPwysiwyg('dladmin_text'.$cat['id'], html_entity_decode($cat['description'],ENT_QUOTES), true,'qup_dladmin_text', $context['TPortal']['show_wysiwyg']);
				elseif($context['TPortal']['dl_wysiwyg'] == 'bbc')
					TP_bbcbox($context['TPortal']['editor_id']);
				else
					echo '<textarea name="dladmin_text'.$cat['id'].'" style="width: 99%; height: 300px;">'. html_entity_decode($cat['description'],ENT_QUOTES).'</textarea>';

			echo '
					</td>
				</tr><tr>
					<td class="windowbg2" valign="top" align="right">'.$txt['tp-dluploadicon'].'</td>
					<td valign="top" class="windowbg2">
						<select size="1" name="dladmin_icon'.$cat['id'].'" onchange="dlcheck(this.value)">
							<option value="blank.gif">'.$txt['tp-noneicon'].'</option>';

			// output the icons
			$selicon = substr($cat['icon'], strrpos($cat['icon'], '/')+1);
			foreach($context['TPortal']['dlicons'] as $dlicon => $value)
				echo '
							<option ' , ($selicon == $value) ? 'selected="selected" ' : '', 'value="'.$value.'">'. $value.'</option>';

			echo '
						</select>
						<br /><br /><img name="dlicon" src="', substr($cat['icon'],0,4)=='http' ? $cat['icon'] :  $boardurl. '/' . $cat['icon'] , '" alt="" />
						<script type="text/javascript">
						function dlcheck(icon)
							{
								document.dlicon.src= "'.$boardurl.'/tp-downloads/icons/" + icon
							}
						</script>
					</td>
				</tr><tr>
					<td class="windowbg2" align="right">'.$txt['tp-dlviews'].':</td>
					<td valign="top" class="windowbg2">'.$cat['views'].' / '.$cat['downloads'].'</td>
				</tr><tr>
					<td class="windowbg2" valign="top" align="right">'.$txt['tp-dlfilename'].'</td>
					<td valign="top" class="windowbg2">'.$cat['file'].'
						<br /><a href="'.$scripturl.'?action=tpmod;dl=get'.$cat['id'].'">['.$txt['tp-download'].']</a>
					</td>
				</tr>
				<tr>
					<td class="windowbg2" valign="top" align="right">'.$txt['tp-uploadnewfileexisting'].':</td>
					<td class="windowbg2">
						<input name="tp_dluploadfile_edit" style="width: 90%;" type="file" value="">
						<input name="tp_dluploadfile_editID" type="hidden" value="'.$cat['id'].'">
					</td>
				</tr><tr>
					<td class="windowbg2" align="right">&nbsp;</td>
					<td valign="top" class="windowbg2">
					'.($cat['filesize']*1024).' bytes
					</td>
				</tr><tr>
				</tr><tr>
					<td class="windowbg2" align="right">'.$txt['tp-uploadedby'].':</td>
					<td valign="top" class="windowbg2">'.$context['TPortal']['admcurrent']['member'].'</td>
				</tr>
						' , $cat['approved']=='0' ? '
				<tr>
					<td class="windowbg2" align="right"><img title="'.$txt['tp-approve'].'" border="0" src="' .$settings['tp_images_url']. '/TPexclamation.gif" alt="'.$txt['tp-dlapprove'].'"  /> </td>
					<td valign="top" class="windowbg2"><b>'.$txt['tp-dlnotapprovedyet'].'</b>
					</td>' : '' , ' ';
		}
		// any extra files?
		if(isset($cat['subitem']) && sizeof($cat['subitem'])>0)
		{
			echo '
				</tr><tr>
					<td class="windowbg2" align="right">'.$txt['tp-dlmorefiles'].'</td>
					<td class="windowbg2"><ul>	';
			foreach($cat['subitem'] as $sub)
			{
				echo '<li><b><a href="' , $sub['href'], '">' , $sub['name'] , '</a></b> (',$sub['file'],')
							', $sub['filesize'] ,' &nbsp;&nbsp;<input style="vertical-align: middle;" name="dladmin_delete'.$sub['id'].'" type="checkbox" value="ON" onclick="javascript:return confirm(\''.$txt['tp-confirm'].'\')"> '.$txt['tp-dldelete'].'
							&nbsp;&nbsp;<input style="vertical-align: middle;" name="dladmin_subitem'.$sub['id'].'" type="checkbox" value="0"> '.$txt['tp-dlattachloose'].'
							</li>';
			}
			echo '</ul>
					</td>';
		}
		// no, but maybe it can be a additional file itself?
		else
		{
			echo '
				</tr><tr>
					<td class="windowbg2" align="right"><b>'.$txt['tp-dlmorefiles2'].'</b></td>
					<td class="windowbg2">
						<select size="1" name="dladmin_subitem'.$cat['id'].'" style="margin-top: 4px;">
							<option value="0" selected>'.$txt['tp-no'].'</option>';

			foreach($context['TPortal']['useritems'] as $subs)
				echo '
							<option value="'.$subs['id'].'">'.$txt['tp-yes'].', '.$subs['name'].'</option>';
			echo '
						</select></td>';

		}
		// which category?
		echo '
				</tr><tr>
					<td class="windowbg2" align="right">'.$txt['tp-dluploadcategory'].'</td>
					<td class="windowbg2">
						<select size="1" name="dladmin_category'.$cat['id'].'" style="margin-top: 4px;">';

		foreach($context['TPortal']['uploadcats'] as $ucats)
		{
			echo '
							<option value="'.$ucats['id'].'" ', $ucats['id']==abs($cat['category']) ? 'selected' : '' ,'>', !empty($ucats['indent']) ? str_repeat("-",$ucats['indent']) : '' ,' '.$ucats['name'].'</option>';
		}
		echo '
						</select>
					 </td>
				</tr>
				<tr>
					<td class="windowbg2" align="right">'.$txt['tp-uploadnewpic'].':</td>
					<td class="windowbg2">
						<input name="tp_dluploadpic_edit" style="width: 90%;" type="file" value="">
						<input name="tp_dluploadpic_editID" type="hidden" value="'.$cat['id'].'">
						</td>
				</tr>
				<tr>
					<td class="windowbg2" valign="top" align="right">'.$txt['tp-uploadnewpicexisting'].':</td>
					<td class="windowbg2">
						<input name="tp_dluploadpic_link" size="60" type="text" value="'.$cat['sshot'].'"><br /><br />
						<div style="overflow: auto;">' , $cat['sshot']!='' ? '<img src="' . (substr($cat['sshot'],0,4)=='http' ? $cat['sshot'] :  $boardurl. '/' . $cat['sshot']) . '" alt="" />' : '&nbsp;' , '</div></td>
				</tr>
				<tr>
					<td colspan="2" class="windowbg">
						<input name="dlsend" type="submit" value="'.$txt['tp-submit'].'">
						<input name="sc" type="hidden" value="'.$context['session_id'].'">
						<input name="dl_useredit" type="hidden" value="'.$cat['id'].'">
					</td>
				</tr>
			</table>
		</form>';
	}

	if($context['TPortal']['dlaction']=='search')
	{
	   echo '
		<div class="tborder">
			<div class="cat_bar">
				<h3 class="catbg">'.$txt['tp-downloadsection'].' - '.$txt['tp-dlsearch'].'</h3>
			</div>
			<div class="windowbg">
				<span class="topslice"><span></span></span>
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td >
							<form accept-charset="', $context['character_set'], '" id="dl_search_form" action="'.$scripturl.'?action=tpmod;dl=results" enctype="multipart/form-data" method="post">
								<table cellpadding="5" cellspacing="1" width="100%" >
									<tr>
										<td align="right">' , $txt['tp-search'] , ':</td>
										<td class="input_td">
											<input type="text" size="60" name="dl_search" id="dl_search" />
										</td>
									</tr>
									<tr>
										<td valign="top" align="right"></td>
										<td  class="input_td">
										<input type="checkbox" id="dl_searcharea_name" /> ' , $txt['tp-searcharea-name'] , '<br />
										<input type="checkbox" id="dl_searcharea_descr" /> ' , $txt['tp-searcharea-descr'] , '<br />
										</td>
									</tr>
									<tr>
										<td colspan="2" align="center">
											<input type="submit" value="' , $txt['tp-search'] , '" />
											<input type="hidden" name="sc" value="' , $context['session_id'] , '" />
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
				</table>
				<span class="botslice"><span></span></span>
			</div>
		</div>
		</div>';
	}

	if($context['TPortal']['dlaction']=='results')
	{
		echo '
		<div class="tborder">
			<div class="cat_bar">
				<h3 class="catbg">' , $txt['tp-dlsearchresults'] , '
					' . $txt['tp-searchfor'] . '  &quot;'.$context['TPortal']['dlsearchterm'].'&quot;
				</h3>
			</div>
			<form style="margin: 0; padding: 0;" accept-charset="', $context['character_set'], '"  id="dl_search_form" action="'.$scripturl.'?action=tpmod;dl=results" method="post">
				<div style="padding: 10px;" class="windowbg">
					<input type="text" style="font-size: 1em; margin-bottom: 0.5em; padding: 3px; width: 90%;" value="'.$context['TPortal']['dlsearchterm'].'" name="dl_search" /><br />
					<input type="checkbox" name="dl_searcharea_name" checked="checked" /> ' , $txt['tp-searcharea-name'] , '
					<input type="checkbox" name="dl_searcharea_desc" checked="checked" /> ' , $txt['tp-searcharea-descr'] , '
					<input type="hidden" name="sc" value="' , $context['session_id'] , '" />
					<input type="submit" value="' , $txt['tp-send'] , '" />
				</div>
			</form>
				';
		$bb=1;
		foreach($context['TPortal']['dlsearchresults'] as $res)
		{
			echo '
			<h4 class="tpresults windowbg"><a href="' . $scripturl . '?action=tpmod;dl=item' . $res['id'] . '">' . $res['name'] . '</a></h4>
			<div class="windowbg tpresults" style="padding-top: 2px;">
				<div class="middletext">' , $res['body'] . '</div>
				<div class="smalltext" style="padding-top: 0.4em;">' , $txt['tp-by'] . ' ' . $res['author'] . ' - ', timeformat($res['date']) , '</div>
			</div>';
			$bb++;	
		}
		echo '
		</div>
		</div>';
	}
	echo '
	</div>';
}

?>