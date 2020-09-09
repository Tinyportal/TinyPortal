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

//Upload page
//Edit file page
//Downloads search page

function template_main()
{
	global $context, $settings, $txt, $scripturl, $modSettings, $boardurl;

	// if dl manager is off, throw a error screen but don't log it.
	if($context['TPortal']['show_download']==0 && !allowedTo('tp_dlmanager'))
		fatal_error($txt['tp-dlmanageroff'], false);

		echo '
	<div class="dl_container">
		<div class="title_bar">
			<h3 class="titlebg">', ($context['TPortal']['dlaction']=='item' || $context['TPortal']['dlaction']=='cat') ? $txt['tp-downloads'] . ':&nbsp;' .$context['TPortal']['dlheader'] : $txt['tp-downloads'] ,'</h3>
		</div>
		<div>';
		$dlbuttons = array(
			'frontdl' => array('text' => 'tp-downloadss1', 'lang' => true, 'url' => $scripturl . '?action=tportal;sa=download;dl' ),
			'upload' => array('text' => 'tp-dlupload', 'test' => 'can_tp_dlupload', 'lang' => true, 'url' => $scripturl . '?action=tportal;sa=download;dl=upload'),
			'search' => array('text' => 'tp-search', 'lang' => true, 'url' => $scripturl . '?action=tportal;sa=download;dl=search'),
			'stats' => array('text' => 'tp-stats', 'lang' => true, 'url' => $scripturl . '?action=tportal;sa=download;dl=stats'),
		);

		if(in_array($context['TPortal']['dlaction'],array('frontdl','upload','search','stats')))
			$dlbuttons[$context['TPortal']['dlaction']]['active'] = true;
		else
			$dlbuttons['frontdl']['active'] = true;

		echo '
			<div style="overflow: hidden; margin: 0 0 5px 0;">';
				template_button_strip($dlbuttons, 'right');

		echo '
			</div>';

	if($context['TPortal']['dlaction']=='' || $context['TPortal']['dlaction']=='cat')
	{
		if(!empty($context['TPortal']['dl_introtext']) && (!$context['TPortal']['dlaction'])=='cat')
			echo '
			<div class="windowbg tp_pad" style="margin-bottom: 5px">' , $context['TPortal']['dl_wysiwyg'] == 'bbc' ? parse_bbc($context['TPortal']['dl_introtext']) : $context['TPortal']['dl_introtext'] , '</div>';

		if(!empty($context['TPortal']['dl_showfeatured']) && !empty($context['TPortal']['featured']))
		{
			echo '
			<div class="cat_bar">
				<h3 class="catbg">' . $txt['tp-dlfeatured'] . '</h3>
			</div>
			<div class="windowbg noup tp_pad">';

			echo '
				<h3 class="h3dl"><a href="' . $scripturl . '?action=tportal;sa=download;dl=item'.$context['TPortal']['featured']['id'].'">' . $context['TPortal']['featured']['name'] . '</a></h4>
				<span class="middletext">'. $txt['tp-uploadedby'] . ' <a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['featured']['author_id'].'">' . $context['TPortal']['featured']['author'] . '</a></span>
				<hr>';

			if(!empty($context['TPortal']['featured']['sshot']))
				 echo '
				<div class="dl_featureshot floatright tp_pad" style="width: '.$context['TPortal']['dl_screenshotsize'][2].'px; height: '.$context['TPortal']['dl_screenshotsize'][3].'px;background: url('.$context['TPortal']['featured']['sshot'].') no-repeat;"></div>';
			echo '
				<p>' . $context['TPortal']['featured']['description'] , '</p>
				<p class="clearthefloat"></p>
			</div>';
		}

		// render last added and most downloaded.
		if(($context['TPortal']['dl_showlatest']==1 || ($context['TPortal']['dl_showstats']==1)) && (!empty($context['TPortal']['dl_last_added'])))
		{
			echo '
				<span class="upperframe"><span></span></span>
				<div class="roundframe">
					<div>';
			if($context['TPortal']['dl_showlatest']==1)
				echo '
				<a href="javascript:void(0);" onclick="dlshowtab(\'dlrecent\');">' , $txt['tp-recentuploads'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</a>';
			if($context['TPortal']['dl_showstats']==1)
			{
				echo '
				 ' , $context['TPortal']['dl_showlatest']==1 ? '&nbsp;|&nbsp; ' : '' , '<a href="javascript:void(0);" onclick="dlshowtab(\'dlweekpop\');">' , $txt['tp-mostpopweek'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</a>
				&nbsp;|&nbsp; <a href="javascript:void(0);" onclick="dlshowtab(\'dlpop\');">' , $txt['tp-mostpop'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</a>';
			}
			echo '
				</div>
			</div>
			<span class="lowerframe" style="margin-bottom: 5px;"><span></span></span>
			<div class="windowbg" style="margin-bottom: 5px;">';
		}

		if(($context['TPortal']['dl_showlatest']==1) && (!empty($context['TPortal']['dl_last_added'])))
		{
			echo '
			<div id="dlrecent">
			<div class="title_bar">
			<h3 class="titlebg">' , $txt['tp-recentuploads'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</h3>
			</div>';

			$count=1;
			if(!empty($context['TPortal']['dl_last_added']))
			{
				foreach($context['TPortal']['dl_last_added'] as $last)
				{
					echo '
					<div class="dl_recentdl">';

					if(!empty($last['screenshot']))
						echo '<div style="background: url('.$last['screenshot'].') no-repeat; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="dl_screenshot"></div>';
					elseif(!empty($last['icon']) && strpos($last['icon'], 'blank.gif') == false)
						echo '<div style="background: url('.$last['icon'].') 50% 50% no-repeat; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="dl_screenshot"></div>';
					else
						echo '<div style="background: url('.$settings['tp_images_url'].'/TPnodl.png) 50% 50% no-repeat; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="dl_screenshot"></div>';

					echo '
						<div class="dl_most_downloaded">
							<a href="'.$last['href'].'"><b>'.$last['name'].'</b></a>
							<div class="smalltext"> '.$txt['tp-uploadedby'] .' ' . $last['author'].'<br>'.$last['date'].'</div>
							<div class="smalltext"> '.strtolower($txt['tp-downloads']).': '.$last['downloads'].'</div>
						</div>
					</div>';
				}
			}
			echo '
			</div>';
		}
		if(($context['TPortal']['dl_showstats']==1) && (!empty($context['TPortal']['dl_most_downloaded'])))
		{
			echo '
			<div id="dlweekpop" ' , $context['TPortal']['dl_showlatest']==1 ? 'style="display: none;"' : '' , '>
				<div class="title_bar">
					<h3 class="titlebg">' , $txt['tp-mostpopweek'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</h3>
				</div>';

			$count=1;
			if(!empty($context['TPortal']['dl_week_downloaded']))
			{
				foreach($context['TPortal']['dl_week_downloaded'] as $wost)
				{
					echo '
					<div class="dl_recentdl">';
					
					if(!empty($wost['screenshot']))
						echo '<div style="background: url('.$wost['screenshot'].') no-repeat; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="dl_screenshot"></div>';
					elseif(!empty($wost['icon']) && strpos($wost['icon'], 'blank.gif') == false)
						echo '<div style="background: url('.$wost['icon'].') 50% 50% no-repeat; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="dl_screenshot"></div>';
					else
						echo '<div style="background: url('.$settings['tp_images_url'].'/TPnodl.png) 50% 50% no-repeat; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="dl_screenshot"></div>';
					echo '
						<div class="dl_most_downloaded">
							<a href="'.$wost['href'].'"><b>'.$count.'.&nbsp;'.$wost['name'].'</b></a>
							<div class="smalltext"> '.$txt['tp-uploadedby'] .' ' . $wost['author'].'<br>'.$wost['date'].'</div>
							<div class="smalltext">'.strtolower($txt['tp-downloads']).': '.$wost['downloads'].'</div>
						</div>
					</div>';
					$count++;
				}
			}
			echo '
			</div>
			
			<div id="dlpop" style="display: none;">
				<div class="title_bar">
					<h3 class="titlebg">' , $txt['tp-mostpop'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</h3>
				</div>';

			$count=1;
			if(!empty($context['TPortal']['dl_most_downloaded']))
			{
				foreach($context['TPortal']['dl_most_downloaded'] as $wost)
				{
					echo '
					<div class="dl_recentdl">';
					
					if(!empty($wost['screenshot']))
						echo '<div style="background: url('.$wost['screenshot'].') no-repeat; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="dl_screenshot"></div>';
					elseif(!empty($wost['icon']) && strpos($wost['icon'], 'blank.gif') == false)
						echo '<div style="background: url('.$wost['icon'].') 50% 50% no-repeat; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="dl_screenshot"></div>';
					else
						echo '<div style="background: url('.$settings['tp_images_url'].'/TPnodl.png) 50% 50% no-repeat; width: '.$context['TPortal']['dl_screenshotsize'][0].'px; height: '.$context['TPortal']['dl_screenshotsize'][1].'px;" class="dl_screenshot"></div>';
					echo '
						<div class="dl_most_downloaded">
							<a href="'.$wost['href'].'"><b>'.$count.'.&nbsp;'.$wost['name'].'</b></a>
							<div class="smalltext"> '.$txt['tp-uploadedby'] .' ' . $wost['author'].'<br>'.$wost['date'].'</div>
							<div class="smalltext">'.strtolower($txt['tp-downloads']).': '.$wost['downloads'].' </div>
						</div>
					</div>';
					$count++;
				}
			}
			echo '
			</div>';
		}
		echo '
		<span class="botslice"></span>
		</div>
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
		// ]]></script>';

	// output the category block?
		if(sizeof($context['TPortal']['dlcats'])>0)
		{
		echo '
			<div class="cat_bar">
				<h3 class="catbg">' , $context['TPortal']['dlaction']=='cat' ? $txt['tp-childcategories'] : $txt['tp-categories'] , '</h3>
			</div>
			<div class="windowbg noup padding-div">';
				//show all categories
				foreach($context['TPortal']['dlcats'] as $dlcat)
				{
					// any subcategories?
					if(!empty($context['TPortal']['dlcats']) && sizeof($context['TPortal']['dlcatchilds'])>1)
					{
						$content='';
						foreach($context['TPortal']['dlcatchilds'] as $dlchild)
						{
							if($dlchild['parent']==$dlcat['id'])
							{
								$content .= '
							<li>
								<img alt="" src="' .$settings['tp_images_url']. '/TPboard.png' . '" />
									<a href="'.$dlchild['href'].'">'.$dlchild['name'].'</a>';
								if($dlchild['files']>0)
									$content .= ' (' . $dlchild['files'].')';
								$content .= '
							</li>';
							}
						}
					}

					echo '
					<div class="dl_category"' , !empty($content) ? ' style="margin-bottom: 0;"' : '' ,'>
						<div>
						<img class="dl_caticon" src="' , !empty($dlcat['icon']) ? (substr($dlcat['icon'],0,4)=='http' ? $dlcat['icon'] :  $boardurl. '/' . $dlcat['icon']) : $settings['images_url'].'/board.gif' , '" alt="" /></div>
							<div style="overflow: visible;">
							<div class="details">',$dlcat['files']==1 ? $dlcat['files'].' '.$txt['tp-dl1file'] : ' '.$txt['tp-dlfiles'],': '.$dlcat['files'].'</div>
							<h4><a href="'. $dlcat['href'] .'">'.$dlcat['name'].'</a></h4>
							<div class="dl_catpost">', (($context['TPortal']['dl_showcategorytext']==0) && ($context['TPortal']['dlaction']=='cat')) ? '' : $dlcat['description'] , '</div>';
					if(!empty($content))
						echo '
					<div class="dl_category dl_subcats"><ul class="tp-subcategories">'.$content.'</ul></div>';
						echo '
						<p class="clearthefloat"></p>
						</div>
					</div>';

				}
		echo '
			</div>'; 	
		}

		// output the files in the category
		if($context['TPortal']['dlaction']=='cat')
		{
			echo '
			<div class="cat_bar">
				<h3 class="catbg">' , $txt['tp-dlfiles'] , $context['TPortal']['dlaction']=='cat' ? ' '.$txt['tp-incategory']. '&quot;' . $context['TPortal']['dlheader'].'&quot;' : '' , '</h3>
			</div>
			<div class="windowbg noup padding-div">';
			// show any files?
			if($context['TPortal']['dlaction']=='cat' && sizeof($context['TPortal']['dlitem'])>0)
			{
				echo '
				<div class="dl_pageindex padding-div">';

				if(!empty($context['TPortal']['sortlinks']))
					echo '
					<div style="float: right;">' . $context['TPortal']['sortlinks'] . '</div>';
				else
					echo $txt['tp-dlfiles'];

				if($context['TPortal']['dlaction']!='item' && !empty($context['TPortal']['pageindex']))
					echo '
					<div style="padding-bottom: 0.3em;">' . $context['TPortal']['pageindex'] . '</div>';
				echo '
				</div>
				
				<div class="tp_pad" style="overflow: hidden;">';

				foreach($context['TPortal']['dlitem'] as $dlitem)
				{
				echo '
					<div class="dl_itemgrid">
						<div>';
				echo '
					' , ($dlitem['icon']!='' && strpos($dlitem['icon'], 'blank.gif') == false) ? '<img class="dl_icon" src="'. (substr($dlitem['icon'],0,4)=='http' ? $dlitem['icon'] :  $boardurl. '/' . $dlitem['icon']).'" alt="'.$dlitem['name'].'"  />' : '<img class="dl_icon" src="' . $settings['tp_images_url'] . '/TPnodl.png" alt="" />' , '	';
				echo '
					<h4 class="h4dl"><a href="'.$dlitem['href'].'">'. $dlitem['name'] .'</a></h4>
					</div>
						<p class="clearthefloat"></p>';

					if(isset($dlitem['description']))
						echo '
						<div class="dl_post dl_summary">' . $dlitem['description'] . '</div>';

					unset($details);
					$details=array();
					$det2=array();

					// edit the file?
					if(allowedTo('tp_dlmanager'))
						$details[] = '<a href="' . $scripturl . '?action=tportal;sa=download;dl=adminitem' . $dlitem['id'] . '">' . $txt['tp-edit'] . '</a>';
					elseif($dlitem['author_id']==$context['user']['id'])
						$details[] ='<a href="' . $scripturl . '?action=tportal;sa=download;dl=useredit' . $dlitem['id'] . '">' . $txt['tp-edit'] . '</a>';

					if(isset($dlitem['filesize']))
						$details[] = $dlitem['filesize'];

					$details[] = $txt['tp-views'] . ': ' . $dlitem['views'];
					$details[] = $txt['tp-downloads'] . ': ' . $dlitem['downloads'];
					$det2[] = $txt['tp-itemlastdownload'] . ' ' . timeformat($dlitem['date_last']);
					$det2[] = $dlitem['author'];
					echo '
						<div class="itemdetails smalltext">' , implode(" | ",$details) , '<br>' , implode(" | ",$det2) , '</div>
					</div>';
				}
				echo '
				</div>
			
				<p class="clearthefloat"></p>
				<div class="padding-div">';
					if($context['TPortal']['dlaction']!='item' && !empty($context['TPortal']['pageindex']))
						echo $context['TPortal']['pageindex'];
				echo '
				</div>
			</div>';
			}
			else
			{
				echo '
			<div class="padding-div">'.$txt['tp-nofiles'].'</div>'; 
			}
		}
	}
	elseif($context['TPortal']['dlaction']=='item')
	{
		echo '
	<div class="tborder">
		<div>';

		foreach($context['TPortal']['dlitem'] as $dlitem)
		{
			echo '
			<div class="windowbg">
				<span class="topslice"><span></span></span>
				<div class="content">';
			echo '
				' , ($dlitem['icon']!='' && strpos($dlitem['icon'], 'blank.gif') == false) ? '<img class="dl_icon" src="'. (substr($dlitem['icon'],0,4)=='http' ? $dlitem['icon'] :  $boardurl. '/' . $dlitem['icon']).'" alt="'.$dlitem['name'].'"  />' : '<img class="dl_icon" src="' . $settings['tp_images_url'] . '/TPnodl.png" alt="" />' , '	';
			echo '
				<h3 class="h3dl">
				',$dlitem['file'] == '- empty item -' ? ''. $dlitem['name'] .'' : '<a href="'.$dlitem['href'].'">'. $dlitem['name'] .'</a>';

			// edit the file?
			if(allowedTo('tp_dlmanager'))
				echo '&nbsp;&nbsp;<small>[<a href="' , $scripturl , '?action=tportal;sa=download;dl=adminitem' , $dlitem['id'] , '">' , $txt['tp-edit'] , '</a>]</small>';
			elseif($dlitem['author_id']==$context['user']['id'])
				echo '&nbsp;&nbsp;<small>[<a href="' , $scripturl , '?action=tportal;sa=download;dl=useredit' , $dlitem['id'] , '">' , $txt['tp-edit'] , '</a>]</small>';

			echo '
				</h4>
				<p class="clearthefloat"></p>
				<hr>
					<p style="float:right;">',$dlitem['file'] == '- empty item -' ? '<img title="'.$txt['tp-downloadss3'].'" src="' .$settings['tp_images_url']. '/TPnodownloadfile.png" alt="'.$txt['tp-nodownload'].'" />' : '<a href="'.$dlitem['href'].'"><img title="'.$txt['tp-downloadss2'].'" src="' .$settings['tp_images_url']. '/TPdownloadfile.png" alt="'.$txt['tp-download'].'" /></a>','</p>
					<ul class="dl_details">
						<li>'.$txt['tp-dlfilesize'].': ',isset($dlitem['filesize']) ? $dlitem['filesize']: '','</li>
						<li>'.$txt['tp-views'].': '.$dlitem['views'].'</li>
						<li>'.$txt['tp-downloads'].': '.$dlitem['downloads'].'</li>
						<li>'.$txt['tp-created'].': '.timeformat($dlitem['created']).'</li>
						<li>'.$txt['tp-itemlastdownload'].': '.timeformat($dlitem['date_last']).'</li>
					</ul>
					<div id="rating">' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' .$settings['tp_images_url']. '/TPblue.png" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $dlitem['rating_average'])) : $dlitem['rating_average']) . ' (' . $txt['tp-ratingvotes'] . ' ' . $dlitem['rating_votes'] . ')</div>';

			if($dlitem['can_rate'])
			{
				echo '
					<form name="tp_dlitem_rating" action="',$scripturl,'?action=tportal;sa=rate_dlitem" method="post">
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
						<input type="hidden" name="tp_dlitem_type" value="dlitem_rating" />
						<input type="hidden" name="tp_dlitem_id" value="'.$dlitem['id'].'" />
						<input type="hidden" name="sc" value="', $context['session_id'], '" />
					</form>
				';
			}
			else
			{
			if (!$context['user']['is_guest'])
				echo '
					<div class="dl_ratingoption"><em class="smalltext">'.$txt['tp-dlhaverated'].'</em></div>';
			}
			echo '
					<hr />
					<div class="dl_post">'.$dlitem['description'].'</div>';

			// any extra files attached?
			if(isset($dlitem['subitem']) && is_array($dlitem['subitem']))
			{
				echo '
					<div class="dl_morefiles">
						<h4>'.$txt['tp-dlmorefiles'].'</h4>
						<div class="dl_post">
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
					<br><img src="'.$dlitem['bigshot'].'" style="max-width: 100%;" alt="" />';
		echo '
				</div>
			<span class="botslice"><span></span></span>
			</div>
			</div>
		</div>
	</div>';
	}
//Upload page
	elseif($context['TPortal']['dlaction']=='upload')
	{
		// check that you can upload
		if(allowedTo('tp_dlmanager') || allowedTo('tp_dlupload'))
			$show=true;
		else
			fatal_error($txt['tp-adminonly']);

	   echo '
	 <div id="tpUpload" class="tborder">
		<div></div>
        <div class="cat_bar">
            <h3 class="catbg">'.$txt['tp-dlupload'].'</h3>
        </div>
		<div class="windowbg noup" style="padding:0px">
			<span class="topslice"><span></span></span>
			  <div>
				<form accept-charset="', $context['character_set'], '" name="tp_dlupload" id="tp_dlupload" action="'.$scripturl.'?action=tportal;sa=download;dl=upload" method="post" enctype="multipart/form-data" onsubmit="submitonce(this);">
				';

		if($context['TPortal']['dl_approve']=='1' && !allowedTo('tp_dlmanager'))
			echo '<div class="padding-div" style="text-align:center;"><b>! '.$txt['tp-warnsubmission'].'</b></div>';

		echo '<div style="text-align:center;" class="smalltext padding-div"><b>'. $txt['tp-maxuploadsize'].': '. $context['TPortal']['dl_max_upload_size'].''.$txt['tp-kb'].'</b></div>
					<div class="formtable padding-div">
						<dl class="settings tptitle">
							<dt>
								<label for="tp-dluploadtitle"><b>'.$txt['tp-dluploadtitle'].'</b></label>
							</dt>
							<dd>
								<input type="text" id="tp-dluploadtitle" name="tp-dluploadtitle" value="" style="width: 92%;" required>
							</dd>
							<dt>
								<label for="tp-dluploadcat"><b>'.$txt['tp-dluploadcategory'].'</b></label>
							</dt>
							<dd>
								<select id="tp-dluploadcat" name="tp-dluploadcat">';

		foreach($context['TPortal']['uploadcats'] as $ucats)
		{
			echo '
									<option value="'.$ucats['id'].'">', !empty($ucats['indent']) ? str_repeat("- ",$ucats['indent']) : '' ,' ' . $ucats['name'].'</option>';
		}
		echo '				</select><br>
							</dd>
						</dl>
						<hr>
							<div><b>'.$txt['tp-dluploadtext'].'</b></div>
							<div>';

		if($context['TPortal']['dl_wysiwyg']== 'html')
			TPwysiwyg('tp_dluploadtext', '', true,'qup_tp_dluploadtext', $context['TPortal']['show_wysiwyg'], false);
		elseif($context['TPortal']['dl_wysiwyg']=='bbc')
			TP_bbcbox($context['TPortal']['editor_id']);
		else
			echo '<textarea id="tp_article_body" name="tp_dluploadtext" wrap="auto"></textarea>';

		echo '	</div>
				<hr>
				<dl class="settings">
					<dt>
						<label for="tp-dluploadfile">'.$txt['tp-dluploadfile'].'</label><br>
						 ('.$context['TPortal']['dl_allowed_types'].')
					</dt>
					<dd>';
		if((allowedTo('tp_dlmanager') && !isset($_GET['ftp'])) || !allowedTo('tp_dlmanager')) {
			echo '<input type="file" id="tp-dluploadfile" name="tp-dluploadfile">
					</dd>';

            echo '<dt>
						<label for="tp-dlexternalfile">'.$txt['tp-dlexternalfile'].'</label>
					</dt>
					<dd>
                        <input type="text" id="tp-dlexternalfile" name="tp-dlexternalfile" style="width: 92%;">
					</dd>';
        }
		// file already uploaded?
		if(allowedTo('tp_dlmanager') && !isset($_GET['ftp'])){
			echo '
					<dt>
						<label for="tp-dluploadnot">'. $txt['tp-dlnoupload'].'</label>
					</dt>
					<dd>
						<input type="checkbox" id="tp-dluploadnot" name="tp-dluploadnot" value="ON"><br>
					</dd>';
		}
		elseif(allowedTo('tp_dlmanager') && isset($_GET['ftp'])){
			if(isset($_GET['ftp']))
				echo '
					<dt>
						<label for="tp-dluploadnot"><b>'.$txt['tp-dlmakeitem2'].':</b></label><br>'.$context['TPortal']['tp-downloads'][$_GET['ftp']]['file'].';
					</dt>
					<dd>
						<input type="hidden" id="tp-dluploadnot" name="tp-dluploadnot" value="ON"><input type="hidden" name="tp-dlupload_ftpstray" value="'.$_GET['ftp'].'">
					</dd>';
		}
		echo '</dl>';

		echo '<hr>
				<dl class="settings">
					<dt>
						<label for="tp_dluploadicon">'.$txt['tp-dluploadicon'].'</label>
					</dt>
					<dd>
						<select size="1" name="tp_dluploadicon" id="tp_dluploadicon" onchange="dlcheck(this.value)">
						<option value="blank.gif" selected>'.$txt['tp-noneicon'].'</option>';
		// output the icons
		foreach($context['TPortal']['dlicons'] as $dlicon => $value)
			echo '
						<option value="'.$value.'">'.$value.'</option>';

		echo '
						</select>
						<img style="margin-left: 2ex;vertical-align:top" name="dlicon" src="' .$settings['tp_images_url']. '/TPblank.png" alt="" />
					</dd>
					<dt>
						<label for="tp_dluploadpic">'.$txt['tp-dluploadpic'].'</label>
					</dt>
					<dd>
						<input type="file" id="tp_dluploadpic" name="tp_dluploadpic" size="60">
						<input type="hidden" name="tp-uploadcat" value="'.$context['TPortal']['dlitem'].'">
						<input type="hidden" name="tp-uploaduser" value="'.$context['user']['id'].'">
					</dd>
				</dl>';
		// can you attach it?
		if(!empty($context['TPortal']['attachitems']))
		{
			echo '
				<hr>
				<dl class="settings">
					<dt>
						<label for="tp_dluploadattach">'.$txt['tp-dluploadattach'].'</label>
					</dt>
					<dd>
						 <select size="1" name="tp_dluploadattach" id="tp_dluploadattach">
						<option value="0" selected>'.$txt['tp-none'].'</option>';
			foreach($context['TPortal']['attachitems'] as $att)
				echo '
						<option value="'.$att['id'].'">'.$att['name'].'</option>';
			echo '
						</select>
					<br>
					</dd>
				</dl>';
		}
		// make a new topic too?
		if(allowedTo('tp_dlcreatetopic') && !empty($context['TPortal']['dl_createtopic']))
		{
			$allowed=explode(",",$context['TPortal']['dl_createtopic_boards']);
			if(empty($context['TPortal']['dl_createtopic_boards']))
			{
				echo '
			<hr>
				<dl class="settings">
					<dt>
						'.$txt['tp-dlcreatetopic'].'
					</dt>
					<dd>
						'.$txt['tp-dlmissingboards'].'
					</dd>
				</dl>';
			}
			else
			{
				echo '
			<hr>
				<dl class="settings">
					</dd>
					<dt>
						<label for="create_topic">'.$txt['tp-dlcreatetopic'].'</label>
					</dt>
					<dd>
						<input type="checkbox" id="create_topic" name="create_topic" />
					<dd>';

			if(allowedTo('make_sticky') && !empty($modSettings['enableStickyTopics']))
				echo '
					<dt>
						<label for="create_topic_sticky">'.$txt['tp-dlcreatetopic_sticky'].'</label>
					</dt>
					<dd>
						<input type="checkbox" id="create_topic_sticky" name="create_topic_sticky" /><br>
					</dd>';
			if(allowedTo('announce_topic'))
				echo '
					<dt>
						<label for="create_topic_announce">'.$txt['tp-dlcreatetopic_announce'].'</label>
					</dt>
					<dd>
						<input type="checkbox" id="create_topic_announce" name="create_topic_announce" /><br>
					</dd>';

			echo '
					<dt>
						<label for="create_topic_board">'.$txt['tp-dlchooseboard'].'</label>
					</dt>
					<dd>
						<select size="1" name="create_topic_board" id="create_topic_board" style="margin: 3px;">';
			foreach($context['TPortal']['boards'] as $brd) {
				if(in_array($brd['id'],$allowed))
					echo '
						<option value="'.$brd['id'].'">', $brd['name'].'</option>';
				}
				echo '	</select>
					</dd>
				</dl>
				<textarea name="create_topic_body" id="tp_article_intro" wrap="auto"></textarea>
			</dl>';
			}
		}
		echo '
					<div class="padding-div">
						<input type="submit" id="tp-uploadsubmit" class="button button_submit" name="tp-uploadsubmit" value="'.$txt['tp-dosubmit'].'">
					</div>
				</div>
			</div>
		</form>
			</div>
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
	<div id="stats-page" class="tborder"><div></div>
		<div class="cat_bar">
			<h3 class="catbg">'.$txt['tp-downloadsection'].' - '.$txt['tp-stats'].'</h3>
		</div>
		<div class="windowbg noup padding-div">
			<div class="title_bar"><h3 class="titlebg">'.$maxcount.' '.$txt['tp-dlstatscats'].'</h3></div>
			<div style="width:100%;">
			  <div class="float-items" style="width:5%;"><img src="' .$settings['tp_images_url']. '/TPboard.png" alt="" /></div>
			  <div class="float-items" style="width:91%;">';

		// top categories
		echo '<div>';
		$counter=0;
		if(isset($context['TPortal']['topcats'][0]['items']))
			$maxval=$context['TPortal']['topcats'][0]['items'];

		if(isset($context['TPortal']['topcats']) && count($context['TPortal']['topcats'])>0){
			foreach($context['TPortal']['topcats'] as $cats){
				if($counter<$maxcount){
					echo '
							<div class="float-items" style="width:60%;">'.$cats['link'].'</div>
							<div class="float-items" style="width:19%;height:13px;margin-bottom:2px;overflow:hidden;"><img src="' .$settings['tp_images_url']. '/TPbar.png" height="15" alt="" width="' , $cats['items']>0 ? ceil(100*($cats['items']/$maxval)) : '1' , '%" /></div>
							<div class="float-items" style="width:15%;">'.$cats['items'].'</div>
					        <p class="clearthefloat"></p>';
					$counter++;
				}
			}
		}
		else
			echo '
						<div>&nbsp;</div>';

		echo '
					</div>';

		echo '
				</div><p class="clearthefloat"></p></div>
				<div class="title_bar"><h3 class="titlebg">'.$maxcount.' '.$txt['tp-dlstatsviews'].'</h3></div>
				<div style="width:100%;"><div class="float-items" style="width:5%;"><img src="' .$settings['tp_images_url']. '/TPinfo.png" alt="" /></div>
				<div class="float-items" style="width:91%;">';

		// top views
		echo '<div>';
		$counter=0;
		if(isset($context['TPortal']['topviews'][0]['views']))
			$maxval=$context['TPortal']['topviews'][0]['views'];
		if(isset($context['TPortal']['topviews']) && count($context['TPortal']['topviews'])>0){
			foreach($context['TPortal']['topviews'] as $cats){
				if($counter<$maxcount){
					echo '
							<div class="float-items" style="width:60%;">'.$cats['link'].'</div>
							<div class="float-items" style="width:19%;height:13px;margin-bottom:2px;overflow:hidden;"><img src="' .$settings['tp_images_url']. '/TPbar.png" height="15" alt="" width="' , $cats['views']>0 ? ceil(100*($cats['views']/$maxval)) : '1' , '%" /></div>
							<div class="float-items" style="width="15%";">'.$cats['views'].'</div>
					        <p class="clearthefloat"></p>';
					$counter++;
				}
			}
		}
		else
			echo '
						<div>&nbsp;</div>';

		echo '
					</div>';

		echo '
			</div><p class="clearthefloat"></p></div>
			<div class="title_bar"><h3 class="titlebg">'.$maxcount.' '.$txt['tp-dlstatsdls'].'</h3></div>
			<div style="width:100%;"><div class="float-items" style="width:5%;"><img src="' .$settings['tp_images_url']. '/TPinfo2.png" alt="" /></div>
				<div class="float-items" style="width:91%;">';

		// top downloads
		echo '
					<div>';
		$counter=0;
		if(isset($context['TPortal']['topitems'][0]['downloads']))
			$maxval=$context['TPortal']['topitems'][0]['downloads'];
		if(isset($context['TPortal']['topitems']) && count($context['TPortal']['topitems'])>0){
			foreach($context['TPortal']['topitems'] as $cats){
				if($counter<$maxcount){
					echo '
							<div class="float-items" style="width:60%;">'.$cats['link'].'</div>
							<div class="float-items" style="width:19%;height:13px;margin-bottom:2px;overflow:hidden;"><img src="' .$settings['tp_images_url']. '/TPbar.png" height="15" alt="" width="' , ($maxval > 0) ? ceil(100*($cats['downloads']/$maxval)) : 0 , '%" /></div>
							<div class="float-items" style="width:15%;">'.$cats['downloads'].'</div>
							<p class="clearthefloat"></p>';
					$counter++;
				}
			}
		}
		else
			echo '
						<div>&nbsp;</div>';

		echo '
					</div>';

		echo '
				</div><p class="clearthefloat"></p></div>
			<div class="title_bar"><h3 class="titlebg">'.$maxcount.' '.$txt['tp-dlstatssize'].'</h3></div>
				<div style="width:100%;"><div class="float-items" style="width:5%;"><img src="' .$settings['tp_images_url']. '/TPinfo3.png" alt="" /></div>
				<div class="float-items" style="width:91%;">';

		// top filesize
		echo '
					<div>';
		$counter=0;
		if(isset($context['TPortal']['topsize'][0]['size']))
			$maxval=$context['TPortal']['topsize'][0]['size'];

		if(isset($context['TPortal']['topsize']) && count($context['TPortal']['topsize'])>0){
			foreach($context['TPortal']['topsize'] as $cats){
				if($counter<$maxcount){
					echo '
							<div class="float-items" style="width:60%;">'.$cats['link'].'</div>
							<div class="float-items" style="width:19%;height:13px;margin-bottom:2px;overflow:hidden;"><img src="' .$settings['tp_images_url']. '/TPbar.png" height="15" alt="" width="' , ceil(100*($cats['size']/$maxval)) , '%" /></div>
							<div class="float-items" style="width:15%;">'. floor($cats['size']/1000).''.$txt['tp-kb'].'</div>
							<p class="clearthefloat"></p>';
					$counter++;
				}
			}
		}
		else
			echo '
						<div>&nbsp;</div>';

		echo '
					</div>
				</div><p class="clearthefloat"></p></div>
		</div>
	</div>
	</div>';
	}

//Edit file page
	if($context['TPortal']['dlaction']=='useredit')
	{
		foreach($context['TPortal']['dl_useredit'] as $cat)
		{
			echo '
		<form accept-charset="', $context['character_set'], '" name="dl_useredit" action="'.$scripturl.'?action=tportal;sa=download;dl=admin" enctype="multipart/form-data" onsubmit="syncTextarea();" method="post">
			<div id="useredit-upfiles" class="tborder">
				<div></div>
				<div class="cat_bar"><h3 class="catbg">'.$txt['tp-useredit'].' : '.$cat['name'].' - <a href="'.$scripturl.'?action=tportal;sa=download;dl=item'.$cat['id'].'">['.$txt['tp-dlpreview'].']</a></h3></div>
				<div class="windowbg noup padding-div">
					<dl class="settings tptitle">
						<dt>
							<label for="dladmin_name'.$cat['id'].'"><b>'.$txt['tp-dluploadtitle'].'</b></label>
						</dt>
						<dd>
							<input type="text" id="dladmin_name'.$cat['id'].'" name="dladmin_name'.$cat['id'].'" value="'.$cat['name'].'" style="width: 97%;">
						</dd>
						<dt>
							<label for="dladmin_category'.$cat['id'].'"><b>'.$txt['tp-dluploadcategory'].'</b></label>
						</dt>
						<dd>
							<select size="1" name="dladmin_category'.$cat['id'].'" id="dladmin_category'.$cat['id'].'" style="margin-top: 4px;">';

			foreach($context['TPortal']['uploadcats'] as $ucats)
			{
				echo '
							<option value="'.$ucats['id'].'" ', $ucats['id']==abs($cat['category']) ? 'selected' : '' ,'>', !empty($ucats['indent']) ? str_repeat("-",$ucats['indent']) : '' ,' '.$ucats['name'].'</option>';
			}
			echo '
							</select>
						</dd>	
						<dt>
							'.$txt['tp-uploadedby'].':
						</dt>
						<dd>
							'.$context['TPortal']['admcurrent']['member'].'
						</dd>
						<dt>'.$txt['tp-dlviews'].':</dt>
						<dd>
							'.$cat['views'].' / '.$cat['downloads'].'
						</dd>						
					</dl>
				<hr>
				<div>
					<div><b>'.$txt['tp-dluploadtext'].'</b><br><br></div>';

				if($context['TPortal']['dl_wysiwyg'] == 'html')
					TPwysiwyg('dladmin_text'.$cat['id'], $cat['description'], true,'qup_dladmin_text', isset($context['TPortal']['usersettings']['wysiwyg']) ? $context['TPortal']['usersettings']['wysiwyg'] : 0);
				elseif($context['TPortal']['dl_wysiwyg'] == 'bbc')
					TP_bbcbox($context['TPortal']['editor_id']);
				else
					echo '<textarea name="dladmin_text'.$cat['id'].'" id="tp_article_body" wrap="auto">'.$cat['description'].'</textarea>';

			echo '
				</div>
			<hr>
				<div class="padding-div" style="text-align:center;"><b><a href="'.$scripturl.'?action=tportal;sa=download;dl=get'.$cat['id'].'">['.$txt['tp-download'].']</a></b>
				</div><br>
				<dl class="settings">
					<dt>
						'.$txt['tp-dlfilename'].'
					</dt>
					<dd>
					'.$cat['file'].'
					</dd>
					<dt>
						'.$txt['tp-dlfilesize'].'
					</dt>
					<dd>
						'.($cat['filesize']*1024).' '.$txt['tp-bytes'].'
					</dd>
					<dt>
						'.$txt['tp-uploadnewfileexisting'].':</dt>
					<dd>
						<input type="file" name="tp_dluploadfile_edit" value="" style="width: 90%;">
						<input type="hidden" name="tp_dluploadfile_editID" value="'.$cat['id'].'">
					</dd>
				</dl>
				<hr>
				<dl class="settings">
					<dt>
						<label for="dladmin_icon">'.$txt['tp-dluploadicon'].'</label>
					</dt>
					<dd>
						<select size="1" name="dladmin_icon'.$cat['id'].'" id="dladmin_icon" onchange="dlcheck(this.value)">
						<option value="blank.gif">'.$txt['tp-noneicon'].'</option>';

			// output the icons
			$selicon = substr($cat['icon'], strrpos($cat['icon'], '/')+1);
			foreach($context['TPortal']['dlicons'] as $dlicon => $value)
				echo '
							<option ' , ($selicon == $value) ? 'selected="selected" ' : '', 'value="'.$value.'">'. $value.'</option>';

			echo '
						</select>
						<img style="margin-left: 2ex;vertical-align:top" name="dlicon" src="', substr($cat['icon'],0,4)=='http' ? $cat['icon'] :  $boardurl. '/' . $cat['icon'] , '" alt="" />
						<script type="text/javascript">
						function dlcheck(icon)
							{
								document.dlicon.src= "'.$boardurl.'/tp-downloads/icons/" + icon
							}
						</script><br><br>
					</dd>
					<dt>
						<label for="tp_dluploadpic_link">'.$txt['tp-uploadnewpicexisting'].'</label>
					</dt>
					<dd>
						<input type="text" id="tp_dluploadpic_link" name="tp_dluploadpic_link" value="'.$cat['sshot'].'" size="60"><br>
						<div style="overflow: auto;">' , $cat['sshot']!='' ? '<img src="' . (substr($cat['sshot'],0,4)=='http' ? $cat['sshot'] :  $boardurl. '/' . $cat['sshot']) . '" alt="" />' : '&nbsp;' , '</div>
				   	</dd>
					<dt>
						'.$txt['tp-uploadnewpic'].'
					</dt>
					<dd>
						<input type="file" name="tp_dluploadpic_edit" value="" style="width: 90%;">
						<input type="hidden" name="tp_dluploadpic_editID" value="'.$cat['id'].'">
					</dd>
				</dl>
				' , $cat['approved']=='0' ? '
				<dl class="settings">
					<dt>
						<img title="'.$txt['tp-approve'].'" src="' .$settings['tp_images_url']. '/TPexclamation.png" alt="'.$txt['tp-dlapprove'].'"  />
					</dt>
					<dd>
						<b>'.$txt['tp-dlnotapprovedyet'].'</b>
					</dd>
				</dl>' : '' , ' ';
		}
		// any extra files?
		if(isset($cat['subitem']) && sizeof($cat['subitem'])>0)
		{
			echo '
				<hr>
				<dl class="settings">
					<dt>
						'.$txt['tp-dlmorefiles'].'
					</dt>
					<dd>';
			foreach($cat['subitem'] as $sub)
			{
				echo '<div><b><a href="' , $sub['href'], '">' , $sub['name'] , '</a></b><br>(',$sub['file'],')
							', $sub['filesize'] ,' &nbsp;&nbsp;<br><input type="checkbox" name="dladmin_delete'.$sub['id'].'" value="ON" onclick="javascript:return confirm(\''.$txt['tp-confirm'].'\')"> '.$txt['tp-dldelete'].'
							&nbsp;&nbsp;<input type="checkbox" name="dladmin_subitem'.$sub['id'].'" value="0"> '.$txt['tp-dlattachloose'].'
							<br></div>';
			}
			echo '</dd>
				</dl>';
		}
		// no, but maybe it can be a additional file itself?
		else
		{
			echo '
				<hr>
				<dl class="settings">
					<dt>
						<label for="dladmin_subitem">'.$txt['tp-dlmorefiles2'].'</label>
					</dt>
					<dd>
						<select size="1" name="dladmin_subitem'.$cat['id'].'" id="dladmin_subitem" style="margin-top: 4px;">
							<option value="0" selected>'.$txt['tp-no'].'</option>';

			foreach($context['TPortal']['useritems'] as $subs)
				echo '
							<option value="'.$subs['id'].'">'.$txt['tp-yes'].', '.$subs['name'].'</option>';
			echo '
						</select>
				    </dd>
				</dl>';
		}

		echo '
				<div class="padding-div"><input type="submit" class="button button_submit" name="dlsend" value="'.$txt['tp-submit'].'">
				<input type="hidden" name="sc" value="'.$context['session_id'].'">
				<input type="hidden" name="dl_useredit" value="'.$cat['id'].'">
				</div>
			</div>
		</div>
		</form>
	</div>';
	}

	if($context['TPortal']['dlaction']=='search')
//Downloads search page
	{
	   echo '
		<form accept-charset="', $context['character_set'], '" id="dl_search_form" action="'.$scripturl.'?action=tportal;sa=download;dl=results" enctype="multipart/form-data" method="post">
			<div class="tborder" id="dlfiles-search">
				<div class="cat_bar">
					<h3 class="catbg">'.$txt['tp-downloadsection'].' - '.$txt['tp-dlsearch'].'</h3>
				</div>
				<span class="upperframe"><span></span></span>
				<div class="roundframe noup">
					<div class="tp_pad">
						<b>'.$txt['tp-search'].':</b><br>
						<input type="text" id="searchbox" name="dl_search" required/><br>
						<input type="checkbox" id="tp-searcharea-name" checked="checked"/><label for="tp-searcharea-name">'.$txt['tp-searcharea-name'].'</label><br>
						<input type="checkbox" id="dl_searcharea_desc" checked="checked"/><label for="dl_searcharea_desc"> '.$txt['tp-searcharea-descr'].'</label><br>
						<input type="hidden" name="sc" value="'.$context['session_id'].'" /><br>
						<input type="submit" class="button button_submit" value="'.$txt['tp-search'].'">
					</div>
				</div>
				<span class="lowerframe"><span></span></span>
			</div>
		</form>
	</div>';
	}

	if($context['TPortal']['dlaction']=='results')
	{
		echo '
		<div class="tborder">
			<div class="cat_bar">
				<h3 class="catbg">' , $txt['tp-dlsearchresults'] , '
					' . $txt['tp-searchfor'] . '  &quot;'.$context['TPortal']['dlsearchterm'].'&quot;</h3>
			</div>
			<span class="upperframe"><span></span></span>
			<div class="roundframe noup">
				<div class="padding-div">
					<form style="margin: 0; padding: 0;" accept-charset="', $context['character_set'], '"  id="dl_search_form" action="'.$scripturl.'?action=tportal;sa=download;dl=results" method="post">
					<div class="tp_pad">
						<b>'.$txt['tp-search'].':</b><br>
						<input type="text" id="searchbox" value="'.$context['TPortal']['dlsearchterm'].'" name="dl_search" /><br>
						<input type="checkbox" name="dl_searcharea_name" checked="checked" /> ' , $txt['tp-searcharea-name'] , '<br>
						<input type="checkbox" name="dl_searcharea_desc" checked="checked" /> ' , $txt['tp-searcharea-descr'] , '<br>
						<input type="hidden" name="sc" value="' , $context['session_id'] , '" /><br>
						<input type="submit" class="button button_submit" value="'.$txt['tp-search'].'" />
					</div>
					</form>
				</div>
			</div>
			<span class="lowerframe"><span></span></span>
		</div>
				';
		$bb=1;
		foreach($context['TPortal']['dlsearchresults'] as $res)
		{
			echo '
				<div class="windowbg padding-div" style="margin-bottom:5px;">
					<h4 class="tpresults"><a href="' . $scripturl . '?action=tportal;sa=download;dl=item' . $res['id'] . '">' . $res['name'] . '</a></h4>
					<hr>
					<div class="tpresults" style="padding-top: 4px;">
						<div>' , $res['body'] . '</div>
						<div class="smalltext" style="padding-top: 0.4em;">' , $txt['tp-by'] . ' ' . $res['author'] . ' - ', timeformat($res['date']) , '</div>
					</div>
				</div>';
			$bb++;
		}
		echo '
	</div>';
	}
	echo '
	</div>';
}

function template_dlsubmitsuccess()
{
    global $txt;

    echo '
        <div class="tborder">
                <div class="cat_bar">
                    <h3 class="catbg">'.$txt['tp-dlsubmitsuccess2'].'</h3>
                </div>
                    <div class="windowbg padding-div" style="text-align: center;">'.$txt['tp-dlsubmitsuccess'].'
                    <div class="padding-div">&nbsp;</div></div>
        </div>';
}

?>
