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

//Admin page
//Settings page
//Submissions page
//FTP page
//Edit category page
//Add category page

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl;

	echo '
<div>
<script>
$(document).ready( function() {
var $clickme = $(".clickme"),
    $box = $(".box");
$box.hide();
$clickme.click( function(e) {
    $(this).text(($(this).text() === "'.$txt['tp-hide'].'" ? "'.$txt['tp-more'].'" : "'.$txt['tp-hide'].'")).next(".box").slideToggle();
    e.preventDefault();
});
});
</script>
</div>';
	// setup the screen
	echo '
<div id="dl_adminbox" class="">
	<form accept-charset="', $context['character_set'], '"  name="dl_admin" action="'.$scripturl.'?action=tportal;dl=admin" enctype="multipart/form-data" method="post" onsubmit="submitonce(this);">	';

	if($context['TPortal']['dlsub']=='admin')
//Admin page
	{
		echo '
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-dltabs4'].'</h3></div>
		<div id="user-download" class="admintable admin-area">
			<div class="windowbg noup padding-div">
	<table class="table_grid tp_grid" style="width:100%">
		<thead>
			<tr class="title_bar titlebg2">
			<th scope="col">
				<div class="catbg3">
					<div class="float-items pos" style="width:10%;"><strong>'.$txt['tp-pos'].'</strong></div>
					<div class="float-items name" style="width:30%;"><strong>'.$txt['tp-dlname'].'</strong></div>
					<div class="float-items title-admin-area tpcenter" style="width:15%"><strong>'.$txt['tp-dlicon'].'</strong></div>
					<div class="float-items title-admin-area tpcenter" style="width:15%"><strong>'.$txt['tp-dlfiles'].'</strong></div>
					<div class="float-items title-admin-area tpcenter" style="width:15%"><strong>'.$txt['tp-dlsubmitted'].'</strong></div>
					<div class="float-items title-admin-area tpcenter" style="width:15%"><strong>'.$txt['tp-dledit'].'</strong></div>
					<p class="clearthefloat"></p>
				</div>
			</th>
			</tr>
		</thead>
		<tbody>';
			// output all the categories, sort after childs
		if(isset($context['TPortal']['admcats']) && count($context['TPortal']['admcats'])>0)
		{
			foreach($context['TPortal']['admcats'] as $cat)
			{
				if($cat['parent']==0)
					echo '
			<tr class="windowbg">
			<td class="articles">
				<div>
					<div class="adm-pos float-items" style="width:10%;">
					  <input name="tp_dlcatpos'.$cat['id'].'" size="2" type="text" value="'.$cat['pos'].'">
					</div>
					<div class="adm-name float-items" style="width:30%;">
					  <img src="' .$settings['tp_images_url']. '/TPboard.png" alt="" style="margin: 0;vertical-align:top" /> <a href="'.$cat['href'].'">'.$cat['name'].'</a>
					</div>
					<a href="" class="clickme">'.$txt['tp-more'].'</a>
					<div class="box" style="width:60%;float:left;">
						<div class="smalltext fullwidth-on-res-layout float-items tpcenter" style="width:25%;">
							<div id="show-on-respnsive-layout"><strong>'.$txt['tp-dlicon'].'</strong></div>
							', !empty($cat['icon']) ? '<img src="'.$cat['icon'].'" alt="" />' : '' ,'
						</div>
						<div class="fullwidth-on-res-layout float-items tpcenter" style="width:25%;">
							<div id="show-on-respnsive-layout"><div class="smalltext"><strong>'.$txt['tp-dlfiles'].'</strong></div></div>
							'.$cat['items'].'
						</div>
						<div class="fullwidth-on-res-layout float-items tpcenter" style="width:25%;">
							<div id="show-on-respnsive-layout"><div class="smalltext"><strong>'.$txt['tp-dlsubmitted'].'</strong></div></div>
							'.$cat['submitted'].'
						</div>
						<div class="smalltext fullwidth-on-res-layout float-items tpcenter" style="width:25%;">
							<div id="show-on-respnsive-layout" style="word-break: break-all;"><strong>'.$txt['tp-dledit'].'</strong></div>
							<a href="',$scripturl, '?action=tportal;dl=cat',$cat['id'],'"><img title="'.$txt['tp-dlviewcat'].'" src="' .$settings['tp_images_url']. '/TPfilter.png" alt="" /></a>&nbsp;
							<a href="'.$cat['href2'].'"><img title="'.$txt['tp-edit'].'" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>&nbsp;
							<a href="'.$cat['href3'].'" onclick="javascript:return confirm(\''.$txt['tp-confirmdelete'].'\')"><img title="' .$txt['tp-dldelete']. '" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt=""  /></a>
						</div>
						<p class="clearthefloat"></p>
					</div>
					</div>
					<p class="clearthefloat"></p>
				</div>
			</td>
			</tr>';
			}
		}
		else
			echo '
			<tr class="windowbg">
			<td class="articles">
				<div class="padding-div">'.$txt['tp-nocats'].'</div>
			</td>
			</tr>';
		echo '
		</tbody>
	</table>
			<div style="padding:1%;"><input name="dlsend" type="submit" class="button button_submit" value="'.$txt['tp-submit'].'"></div>
			</div>
		</div>';
	}

//Settings page
	elseif($context['TPortal']['dlsub']=='adminsettings')
	{
		echo '
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-dlsettings'].'</h3></div>
		<div id="dlsettings" class="admintable admin-area">
			<div class="windowbg noup padding-div">
					<dl class="settings">
					<dt>
						',$txt['tp-showdownload'], ' <img style="margin:0 1ex;" src="' .$settings['tp_images_url']. '/TP' , $context['TPortal']['show_download']=='0' ? 'red' : 'green' , '.png" alt=""  />
					</dt>
					<dd>
						<input name="tp_show_download" id="tp_show_download_on" type="radio" value="1" ', $context['TPortal']['show_download']=='1' ? 'checked' : '' ,'><label for="tp_show_download_on"> '.$txt['tp-dlmanon'].'</label><br>
						<input name="tp_show_download" id="tp_show_download_off" type="radio" value="0" ', $context['TPortal']['show_download']=='0' ? 'checked' : '' ,'><label for="tp_show_download_off"> '.$txt['tp-dlmanoff'].'</label><br><br>
					</dd>
					<dt>
						<label for="tp_dl_allowed_types">'.$txt['tp-dlallowedtypes'].'</label>
					</dt>
					<dd>
						<input size=60 name="tp_dl_allowed_types" id="tp_dl_allowed_types" type="text" value="'.$context['TPortal']['dl_allowed_types'].'"><br><br>
					</dd>
					<dt>
						<label for="tp_dluploadsize">'.$txt['tp-dlallowedsize'].'</label>
					</dt>
					<dd>
						<input name="tp_dluploadsize" id="tp_dluploadsize" type="text" value="'.$context['TPortal']['dl_max_upload_size'].'"> '.$txt['tp-kb'].'<br><br>
					</dd>
					<dt>
						<label for="tp_dl_fileprefix">'.$txt['tp-dluseformat'].'</label>
					</dt>
					<dd>
						<input name="tp_dl_fileprefix" id="tp_dl_fileprefix1" type="radio" value="K" ', $context['TPortal']['dl_fileprefix']=='K' ? 'checked' : '' ,'> <label for="tp_dl_fileprefix1">'.$txt['tp-kb'].'</label><br>
						<input name="tp_dl_fileprefix" id="tp_dl_fileprefix2" type="radio" value="M" ', $context['TPortal']['dl_fileprefix']=='M' ? 'checked' : '' ,'> <label for="tp_dl_fileprefix2">'.$txt['tp-mb'].'</label><br>
						<input name="tp_dl_fileprefix" id="tp_dl_fileprefix3" type="radio" value="G" ', $context['TPortal']['dl_fileprefix']=='G' ? 'checked' : '' ,'> <label for="tp_dl_fileprefix3">'.$txt['tp-gb'].'</label><br><br>
					</dd>
					<dt>
						'.$txt['tp-dlusescreenshot'].'
					</dt>
					<dd>
						<input name="tp_dl_usescreenshot" type="radio" value="1" ', $context['TPortal']['dl_usescreenshot']=='1' ? 'checked' : '' ,'> '.$txt['tp-yes'].'&nbsp;&nbsp;
						<input name="tp_dl_usescreenshot" type="radio" value="0" ', $context['TPortal']['dl_usescreenshot']=='0' ? 'checked' : '' ,'> '.$txt['tp-sayno'].'<br><br>
					</dd>
					<dt>
						'.$txt['tp-dlscreenshotsize1'].'
					</dt>
					<dd>
						<input name="tp_dl_screenshotsize0" type="text" size="3" maxlength="3" value="'.$context['TPortal']['dl_screenshotsize'][0].'"> x <input name="tp_dl_screenshotsize1" type="text" size="3" maxlength="3" value="'.$context['TPortal']['dl_screenshotsize'][1].'"> px<br><br>
					</dd>
					<dt>
						'.$txt['tp-dlscreenshotsize2'].'
					</dt>
					<dd>
						<input name="tp_dl_screenshotsize2" type="text" size="3" maxlength="3" value="'.$context['TPortal']['dl_screenshotsize'][2].'"> x <input name="tp_dl_screenshotsize3" type="text" size="3" maxlength="3" value="'.$context['TPortal']['dl_screenshotsize'][3].'"> px<br><br>
					</dd>
					<dt>
						'.$txt['tp-dlmustapprove'].'
					</dt>
					<dd>
						<input name="tp_dl_approveonly" id="tp-approveyes" type="radio" value="1" ', $context['TPortal']['dl_approve']=='1' ? 'checked' : '' ,'><label for="tp-approveyes"> '.$txt['tp-approveyes'].'<br>
						<input name="tp_dl_approveonly" id="tp-approveno" type="radio" value="0" ', $context['TPortal']['dl_approve']=='0' ? 'checked' : '' ,'><label for="tp-approveno"> '.$txt['tp-approveno'].'<br><br>
					</dd>
					<dt>
						'.$txt['tp-dlcreatetopic'].'
					</dt>
					<dd>
						<input name="tp_dl_createtopic" type="radio" value="1" ', $context['TPortal']['dl_createtopic']=='1' ? 'checked' : '' ,'> '.$txt['tp-yes'].'&nbsp;&nbsp;
						<input name="tp_dl_createtopic" type="radio" value="0" ', $context['TPortal']['dl_createtopic']=='0' ? 'checked' : '' ,'> '.$txt['tp-no'].'<br><br>
					</dd>
					<dt>
						'.$txt['tp-dlcreatetopicboards'].'
					</dt>
					<dd>
						<div class="dl_perm tp_largelist" id="dl_createboard" ' , in_array('dl_createboard',$context['tp_panels']) ? ' style="display: none;"' : '' , '>
						';
					$brds=explode(",",$context['TPortal']['dl_createtopic_boards']);
					foreach($context['TPortal']['boards'] as $brd)
						echo '<div class="perm"><input type="checkbox" value="'.$brd['id'].'" name="tp_dlboards'.$brd['id'].'" id="tp_dlboards'.$brd['id'].'" ' , in_array($brd['id'],$brds) ? ' checked="checked"' : '' , ' /><label for="tp_dlboards'.$brd['id'].'"> ' . $brd['name'].'</label></div>';

					echo '<br style="clear: both;" />
						</div><br>
					</dd>
					<dt>
						'.$txt['tp-dlwysiwyg'].'
					</dt>
					<dd>
						<input name="tp_dl_wysiwyg" id="tp_dl_wysiwyg1" type="radio" value="" ', $context['TPortal']['dl_wysiwyg']=='' ? 'checked' : '' ,'><label for="tp_dl_wysiwyg1"> '.$txt['tp-no'].'</label><br>
						<input name="tp_dl_wysiwyg" id="tp_dl_wysiwyg2" type="radio" value="html" ', $context['TPortal']['dl_wysiwyg']=='html' ? 'checked' : '' ,'><label for="tp_dl_wysiwyg2"> '.$txt['tp-yes'].', HTML</label><br>
						<input name="tp_dl_wysiwyg" id="tp_dl_wysiwyg3" type="radio" value="bbc" ', $context['TPortal']['dl_wysiwyg']=='bbc' ? 'checked' : '' ,'><label for="tp_dl_wysiwyg3"> '.$txt['tp-yes'].', BBC</label><br><br>
					</dd>
				</dl>
			<hr>
				<div>
					<div><b>'.$txt['tp-dlintrotext'].'</b>
					</div>';
					if($context['TPortal']['dl_wysiwyg'] == 'html')
						TPwysiwyg('tp_dl_introtext', $context['TPortal']['dl_introtext'], true,'qup_tp_dl_introtext', isset($context['TPortal']['usersettings']['wysiwyg']) ? $context['TPortal']['usersettings']['wysiwyg'] : 0);
					elseif($context['TPortal']['dl_wysiwyg'] == 'bbc')
						TP_bbcbox($context['TPortal']['editor_id']);
					else
						echo '<textarea id="tp_article_body" name="tp_dl_introtext" >'.$context['TPortal']['dl_introtext'].'</textarea>';
					echo '
				</div>
			<hr>
				<dl class="settings">
					<dt>
						'.$txt['tp-dlusefeatured'].'
					</dt>
					<dd>
						<input name="tp_dl_showfeatured" type="radio" value="1" ', $context['TPortal']['dl_showfeatured']=='1' ? 'checked' : '' ,'> '.$txt['tp-yes'].'&nbsp;&nbsp;
						<input name="tp_dl_showfeatured" type="radio" value="0" ', $context['TPortal']['dl_showfeatured']=='0' ? 'checked' : '' ,'> '.$txt['tp-sayno'].'<br><br>
					</dd>
					<dt>
						<label for="tp_dl_featured">'.$txt['tp-dlfeatured'].'</label>
					</dt>
					<dd>
						<select name="tp_dl_featured" id="tp_dl_featured" size="5">';

				foreach($context['TPortal']['all_dlitems'] as $item)
				{
					echo '<option value="'.$item['id'].'"' , $context['TPortal']['dl_featured']==$item['id'] ? ' selected="selected"' : '' , '>'.$item['name'].'</option>';
				}

				echo '
					</select><br><br>
					</dd>
					<dt>
						'.$txt['tp-dluselatest'].'
					</dt>
					<dd>
						<input name="tp_dl_showrecent" type="radio" value="1" ', $context['TPortal']['dl_showlatest']=='1' ? 'checked' : '' ,'> '.$txt['tp-yes'].'&nbsp;&nbsp;
						<input name="tp_dl_showrecent" type="radio" value="0" ', $context['TPortal']['dl_showlatest']=='0' ? 'checked' : '' ,'> '.$txt['tp-sayno'].'<br><br>
					</dd>
					<dt>
						'.$txt['tp-dlusestats'].'
					</dt>
					<dd>
						<input name="tp_dl_showstats" type="radio" value="1" ', $context['TPortal']['dl_showstats']=='1' ? 'checked' : '' ,'> '.$txt['tp-yes'].'&nbsp;&nbsp;
						<input name="tp_dl_showstats" type="radio" value="0" ', $context['TPortal']['dl_showstats']=='0' ? 'checked' : '' ,'> '.$txt['tp-sayno'].'<br><br>
					</dd>
					<dt>
						'.$txt['tp-dlusecategorytext'].'
					</dt>
					<dd>
						<input name="tp_dl_showcategorytext" type="radio" value="1" ', $context['TPortal']['dl_showcategorytext']=='1' ? 'checked' : '' ,'> '.$txt['tp-yes'].'&nbsp;&nbsp;
						<input name="tp_dl_showcategorytext" type="radio" value="0" ', $context['TPortal']['dl_showcategorytext']=='0' ? 'checked' : '' ,'> '.$txt['tp-sayno'].'<br><br>
					</dd>
					<dt>
						'.$txt['tp-dlvisualoptions'].'
					</dt>
					<dd>
						<input name="tp_dl_visual_options1" type="checkbox" value="left" ', isset($context['TPortal']['dl_left']) ? 'checked' : '' ,'> '.$txt['tp-leftbar'].'<br>
						<input name="tp_dl_visual_options2" type="checkbox" value="right" ', isset($context['TPortal']['dl_right']) ? 'checked' : '' ,'> '.$txt['tp-rightbar'].'<br>
						<input name="tp_dl_visual_options4" type="checkbox" value="top" ', isset($context['TPortal']['dl_top']) ? 'checked' : '' ,'> '.$txt['tp-topbar'].'<br>
						<input name="tp_dl_visual_options3" type="checkbox" value="center" ', isset($context['TPortal']['dl_center']) ? 'checked' : '' ,'> '.$txt['tp-centerbar'].'<br>
						<input name="tp_dl_visual_options6" type="checkbox" value="lower" ', isset($context['TPortal']['dl_lower']) ? 'checked' : '' ,'> '.$txt['tp-lowerbar'].'<br>
						<input name="tp_dl_visual_options5" type="checkbox" value="bottom" ', isset($context['TPortal']['dl_bottom']) ? 'checked' : '' ,'> '.$txt['tp-bottombar'].'<br>
						<input name="tp_dl_visual_options8" type="hidden" value="not"><br><br>
					</dd>
					<dt>
						<label for="tp_dltheme">',$txt['tp-chosentheme'],'</label>
					</dt>
					<dd>
						<select size="1" name="tp_dltheme" id="tp_dltheme">';
  					echo '<option value="0" ', $context['TPortal']['dlmanager_theme']=='0' ? 'selected' : '' ,'>'.$txt['tp-noneicon'].'</option>';

				foreach($context['TPthemes'] as $them)
  					echo '<option value="'.$them['id'].'" ',$them['id']==$context['TPortal']['dlmanager_theme'] ? 'selected' : '' ,'>'.$them['name'].'</option>';

				echo '
						</select><br><br>
					</dd>
				</dl>
					<div class="padding-div;">
						<input type="hidden" name="dlsettings" value="1" />
						<input name="dlsend" type="submit" class="button button_submit" value="'.$txt['tp-submit'].'">
					</div>
			</div>
		</div>';
	}
	elseif(substr($context['TPortal']['dlsub'],0,8)=='admincat')
	{
		$mycat=substr($context['TPortal']['dlsub'],8);
		// output any subcats
		echo '
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-dltabs4'].'</h3></div>
		<div id="any-subcats" class="admintable admin-area">
			<div class="windowbg noup padding-div">
	<table class="table_grid tp_grid" style="width:100%">
		<thead>
			<tr class="title_bar titlebg2">
			<th scope="col" class="articles">
			<div class="catbg3">
				<div style="width:30%;" class="float-items pos"><strong>'.$txt['tp-dlname'].' / '.$txt['tp-pos'].'</strong></div>
				<div style="width:10%;" class="float-items title-admin-area tpcenter"><strong>'.$txt['tp-dlicon'].'</strong></div>
				<div style="width:20%;" class="float-items title-admin-area tpcenter"><strong>'.$txt['tp-dlviews'].'</strong></div>
				<div style="width:30%;" class="float-items title-admin-area"><strong>'.$txt['tp-dlfile'].'</strong></div>
				<div style="width:10%;" class="float-items title-admin-area tpcenter"><strong>'.$txt['tp-dlfilesize'].'</strong></div>
				<p class="clearthefloat"></p>
			</div>
			</th>
			</tr>
		</thead>
		<tbody>';
		if(isset($context['TPortal']['admcats']) && count($context['TPortal']['admcats'])>0)
		{
			foreach($context['TPortal']['admcats'] as $cat)
			{
				if($cat['parent']==$mycat)
					echo '
			<tr class="windowbg">
			<td class="articles">
			<div class="bigger-width">
			    <div class="fullwidth-on-res-layout float-items" style="width:30%;">
					<div style="width:25%;" class="float-items">
						<input name="tp_dlcatpos'.$cat['id'].'" size="2" type="text" value="'.$cat['pos'].'">
						<input type="hidden" name="admineditcatval" value="'.$cat['parent'].'" />
					</div>
					<div style="width:75%;" class="float-items">
						<img src="' .$settings['tp_images_url']. '/TPboard.png" alt="" style="margin: 0;vertical-align:top" /> <a href="'.$cat['href'].'">'.$cat['name'].'</a>
					</div>
					<p class="clearthefloat"></p>
			    </div>
			<a href="" class="clickme">'.$txt['tp-more'].'</a>
			<div class="box" style="width:70%;float:left;">				
			    <div style="width:14.5%;" class="fullwidth-on-res-layout float-items tpcenter">
					<div id="show-on-respnsive-layout">'.$txt['tp-dlicon'].'</div>
					', !empty($cat['icon']) ? '<img src="'.$cat['icon'].'" alt="" />' : '' ,'
			    </div>
			    <div style="width:29%;" class="fullwidth-on-res-layout float-items">
					<div id="show-on-respnsive-layout" style="word-break:break-all;">'.$txt['tp-dlviews'].'</div>
					<div id="size-on-respnsive-layout">
						<div style="width:48%;" class="float-items tpcenter">
						'.$cat['items'].'
					</div>
					<div style="width:48%;" class="float-items tpcenter">
					'.$cat['submitted'].'
					</div>
					<p class="clearthefloat"></p>
				</div>
			</div>
			<div style="width:56.5%;" class="fullwidth-on-res-layout float-items">
				<div id="show-on-respnsive-layout" style="margin-left:1%;">'.$txt['tp-dlfile'].'</div>
				<a href="',$scripturl, '?action=tportal;dl=cat',$cat['id'],'"><img title="'.$txt['tp-preview'].'" src="' .$settings['tp_images_url']. '/TPfilter.png" alt="" /></a>&nbsp;
				<a href="'.$cat['href2'].'"><img title="'.$txt['tp-edit'].'" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>&nbsp;
				<a href="'.$cat['href3'].'" onclick="javascript:return confirm(\''.$txt['tp-confirmdelete'].'\')"><img title="' .$txt['tp-dldelete']. '" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt=""  /></a>
			</div><p class="clearthefloat"></p>
		</div>
		</td>
		</tr>';
			}
		}
// output any subcats files
		if(isset($context['TPortal']['dl_admitems']) && count($context['TPortal']['dl_admitems'])>0)
		{
			foreach($context['TPortal']['dl_admitems'] as $cat)
			{
				echo '
		<tr class="windowbg">
		<td class="articles">
			<div id="up-file" class="bigger-width">
				<div style="width:30%;" class="fullwidth-on-res-layout float-items">
					<a href="',$scripturl, '?action=tportal;dl=item',$cat['id'],'"><img title="'.$txt['tp-preview'].'" src="' .$settings['tp_images_url']. '/TPfilter.png" alt="" /></a>
					<a href="'.$cat['href'].'">'.$cat['name'].'</a>
				</div>
			<a href="" class="clickme">'.$txt['tp-more'].'</a>
			<div class="box" style="width:70%;float:left;">				
				<div style="width:14.5%;" class="fullwidth-on-res-layout float-items tpcenter">
					<div id="show-on-respnsive-layout">'.$txt['tp-dlicon'].'</div>
				   ', !empty($cat['icon']) ? '<img src="'.$cat['icon'].'" alt="" />' : '' ,'
				</div>
			    <div class="fullwidth-on-res-layout float-items" style="width:29%;">
					<div id="show-on-respnsive-layout" style="word-break:break-all;">'.$txt['tp-dlviews'].'</div>
					<div id="size-on-respnsive-layout">
						<div style="width:48%;" class="float-items tpcenter">
						'.$cat['views'].'
						</div>
						<div style="width:48%;" class="float-items tpcenter">
						'.$cat['downloads'].'
						</div>
						<p class="clearthefloat"></p>
					</div>
				</div>
				<div class="fullwidth-on-res-layout float-items" style="width:42%;">
					<div id="show-on-respnsive-layout">'.$txt['tp-dlfile'].'</div>
					<div id="size-on-respnsive-layout"><div style="width:48%;word-break:break-all;" class="float-items">
				   '. (($cat['file']=='- empty item -' || $cat['file']=='') ? $txt['tp-noneicon'] : $cat['file']) .'
					</div>
					<div style="width:48%;" class="float-items">
					'.$txt['tp-authorby'].' '.$cat['author'].'
					</div>
					<p class="clearthefloat"></p>
				</div>
			</div>
			<div style="width:14.5%;" class="fullwidth-on-res-layout float-items tpcenter">
				<div id="show-on-respnsive-layout">'.$txt['tp-dlfilesize'].'</div>
				'. $cat['filesize'].''.$txt['tp-kb'].'
			</div>
			<p class="clearthefloat"></p>
		</div>
		</div>
			</td>
			</tr>';
			}
		}
		else
			echo '
		<tr class="windowbg">
		<td class="articles">
			<div class="padding-div">'.$txt['tp-nofiles'].'</div>
		</td>
		</tr>';
		echo '
		</tbody>
	</table>
			<div style="padding:1%;"><input name="dlsend" type="submit" class="button button_submit" value="'.$txt['tp-submit'].'">
			</div>
		</div>
	</div>';
	}
	elseif(substr($context['TPortal']['dlsub'],0,9)=='adminitem')
	{
		if(isset($context['TPortal']['dl_admitems']) && count($context['TPortal']['dl_admitems'])>0)
		{
			foreach($context['TPortal']['dl_admitems'] as $cat)
			{
//Edit file page
				echo '
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-useredit'].' : '.$cat['name'].' - <a href="'.$scripturl.'?action=tportal;dl=item'.$cat['id'].'">['.$txt['tp-dlpreview'].']</a></h3></div>
		<div id="edit-up-item" class="admintable admin-area">
			<div class="windowbg noup padding-div">
				<dl class="settings">
					<dt>
						<label for="dladmin_name'.$cat['id'].'"><b>'.$txt['tp-dluploadtitle'].'</b></label>
					</dt>
					<dd>
						<input style="width: 97%;" name="dladmin_name'.$cat['id'].'" id="dladmin_name'.$cat['id'].'" type="text" value="'.$cat['name'].'">
					</dd>
					<dt>
						<label for="dladmin_category"><b>'.$txt['tp-dluploadcategory'].'</b>
					</dt>
					<dd>
						<select size="1" name="dladmin_category'.$cat['id'].'" id="dladmin_category"> style="margin-top: 4px">';

		foreach($context['TPortal']['admuploadcats'] as $ucats)
		{
			echo '
						<option value="'.$ucats['id'].'" ', $ucats['id'] == abs($cat['category']) ? 'selected' : '' ,'>', (!empty($ucats['indent']) ? str_repeat("-",$ucats['indent']) : '') ,' '.$ucats['name'].'</option>';
		}
		echo '
						</select><br><br>
					</dd>
					<dt>
						'.$txt['tp-uploadedby'].'
					</dt>
					<dd>
						'.$context['TPortal']['admcurrent']['member'].'<br>
					</dd>
					<dt>
						'.$txt['tp-dlviews'].'
					</dt>
					<dd>
						 '.$cat['views'].' / '.$cat['downloads'].'<br>
					</dd>					
				</dl>
				<hr>			
				<div>
					<div><b>'.$txt['tp-dluploadtext'].'</b></div>';

				if($context['TPortal']['dl_wysiwyg'] == 'html')
					TPwysiwyg('dladmin_text'.$cat['id'], $cat['description'], true,'qup_dladmin_text', isset($context['TPortal']['usersettings']['wysiwyg']) ? $context['TPortal']['usersettings']['wysiwyg'] : 0);
				elseif($context['TPortal']['dl_wysiwyg'] == 'bbc')
					TP_bbcbox($context['TPortal']['editor_id']);
				else
					echo '<textarea name="dladmin_text'.$cat['id'].'" id="tp_article_body">'.$cat['description'].'</textarea>';
				echo '
				</div>
			<hr>
				<div class="padding-div" style="text-align:center;"><b><a href="'.$scripturl.'?action=tportal;dl=get'.$cat['id'].'">['.$txt['tp-download'].']</a></b>
				</div><br>
			<dl class="settings">
				<dt>
					<label for="dladmin_file">'.$txt['tp-dlfilename'].'</label>
				</dt>
				<dd>';
		if($cat['file']=='- empty item -' || $cat['file']==''|| $cat['file']=='- empty item - ftp')
		{
			if($cat['file']=='- empty item - ftp')
			{
				echo '
					<div style="padding: 5px 0 5px 0; font-weight: bold;">'.$txt['tp-onlyftpstrays'].'</div>';
			}
			echo '
				<select size="1" name="dladmin_file'.$cat['id'].'" id="dladmin_file">
					<option value="- empty item -">' . $txt['tp-noneicon'] . '</option>';

			foreach($context['TPortal']['tp-downloads'] as $file)
			{
				if($cat['file']=='- empty item - ftp')
				{
					// check the file against
					if(!in_array($file['file'], $context['TPortal']['dl_allitems']))
		  				echo '
						<option value="'.$file['file'].'">'.$file['file'].' - '.$file['size'].''.$txt['tp-kb'].'</option>';
				}
				else
	  				echo '
				  		<option value="'.$file['file'].'">'.$file['file'].' - '.$file['size'].''.$txt['tp-kb'].'</option>';
			}
			echo '
					</select>';
		}
		else
			echo '<input name="dladmin_file'.$cat['id'].'" ="dladmin_file" type="text" size="50" style="margin-bottom: 0.5em" value="'.$cat['file'].'">';

		echo '
				</dd>
				<dt>
					'.$txt['tp-dlfilesize'].'</dt>
				<dd>
					'.($cat['filesize']*1024).' '.$txt['tp-bytes'].'<br>
				</dd>
				<dt>
					<label for="tp_dluploadfile_edit">'.$txt['tp-uploadnewfileexisting'].'</label>
				</dt>
				<dd>
					<input name="tp_dluploadfile_edit" id="tp_dluploadfile_edit" type="file" value="">
					<input name="tp_dluploadfile_editID" type="hidden" value="'.$cat['id'].'"><br>
				</dd>
			</dl>
				<hr>
				<dl class="settings">
					<dt>
						<label for="dladmin_icon">'.$txt['tp-dluploadicon'].'</label>
					</dt>
					<dd>
						<select size="1" name="dladmin_icon'.$cat['id'].'" id="dladmin_icon" onchange="dlcheck(this.value)">';

			echo '
						<option value="blank.gif">'.$txt['tp-noneicon'].'</option>';

				// output the icons
				$selicon = substr($cat['icon'], strrpos($cat['icon'], '/')+1);
				foreach($context['TPortal']['dlicons'] as $dlicon => $value)
					echo '
						<option ' , ($selicon == $value) ? 'selected="selected" ' : '', 'value="'.$value.'">'. $value.'</option>';

				echo '
						</select>
						<img style="margin-left: 2ex;vertical-align:top" name="dlicon" src="'.$cat['icon'].'" alt="" />
					<script type="text/javascript">
					function dlcheck(icon)
						{
							document.dlicon.src= "'.$boardurl.'/tp-files/tp-downloads/icons/" + icon
						}
					</script><br>
					</dd>
				</dl>
				<dl class="settings">
					<dt>
						<label for="tp_dluploadpic_link">'.$txt['tp-uploadnewpicexisting'].'</label>
					</dt>
					<dd>
						<input name="tp_dluploadpic_link" id="tp_dluploadpic_link" size="50" type="text" value="'.$cat['screenshot'].'">
					</dd>
					<dd>
						<div class="padding-div">' , $cat['sshot']!='' ? '<img style="max-width:95%;" src="'.$cat['sshot'].'" alt="" />' : '' , '</div>
					</dd>
				</dl>
				<dl class="settings">
					<dt>
						<label for="tp_dluploadpic_edit">'.$txt['tp-uploadnewpic'].'</label>
					</dt>
					<dd>
						<input name="tp_dluploadpic_edit" id="tp_dluploadpic_edit" type="file" value="">
						<input name="tp_dluploadpic_editID" type="hidden" value="'.$cat['id'].'"><br>
					</dd>
				</dl>
				' , $cat['approved']=='0' ? '
				<dl class="settings">
					<dt>
						<label for="dl_admin_approve"><b> '.$txt['tp-dlapprove'].'</b></label>
					</dt>
					<dd>
						<input style="vertical-align: middle;" name="dl_admin_approve'.$cat['id'].'" id="dl_admin_approve" type="checkbox" value="ON">&nbsp;&nbsp;<img title="'.$txt['tp-approve'].'" src="' .$settings['tp_images_url']. '/TPthumbup.png" alt="'.$txt['tp-dlapprove'].'"  />
					</dd>
				</dl>' : '' , '
				<hr>
			<dl class="settings">
';
			}
		}
		// any extra files?
		if(isset($cat['subitem']) && sizeof($cat['subitem'])>0)
		{

		echo '
					<dt>
						<label for="dladmin_delete">'.$txt['tp-dlmorefiles'].'</label>
					</dt>
					<dd>';
			foreach($cat['subitem'] as $sub) {
				echo '<div><b><a href="' , $sub['href'], '">' , $sub['name'] , '</a></b><br>(',$sub['file'],')
						', $sub['filesize'] ,'<br><input name="dladmin_delete'.$sub['id'].'" id="dladmin_delete" type="checkbox" value="ON" onclick="javascript:return confirm(\''.$txt['tp-confirm'].'\')"> '.$txt['tp-dldelete'].'
						&nbsp;&nbsp;<input name="dladmin_subitem'.$sub['id'].'" type="checkbox" value="0"> '.$txt['tp-dlattachloose'].'
						<br></div>';
			}
		echo '
					</dd>';
		}
		// no, but maybe it can be a additional file itself?
		else {
			echo '
					<dt>
						<label for="dladmin_subitem">'.$txt['tp-dlmorefiles2'].'</label>
					</dt>
					<dd>
						<select size="1" name="dladmin_subitem'.$cat['id'].'" id="dladmin_subitem" style="margin-top: 4px;">
						<option value="0" selected>'.$txt['tp-no'].'</option>';

			foreach($context['TPortal']['admitems'] as $subs)
			echo '
						<option value="'.$subs['id'].'">'.$txt['tp-yes'].', '.$subs['name'].'</option>';
			echo '
						</select><br>
					</dd>';
		}
			echo '
				</dl>
				<hr>
				<dl class="settings">
					<dt>
						<b>'.$txt['tp-dldelete'].'</b>
					</dt>
					<dd>
						<input name="dladmin_delete'.$cat['id'].'"  type="checkbox" value="ON" onclick="javascript:return confirm(\''.$txt['tp-confirm'].'\')">&nbsp;&nbsp;<img title="'.$txt['tp-dldelete'].'" border="0" src="' .$settings['tp_images_url']. '/TPthumbdown.png" alt="'.$txt['tp-dldelete'].'"  />
					</dd>
				</dl>
			<div class="padding-div"><input name="dlsend" type="submit" class="button button_submit" value="'.$txt['tp-submit'].'"></div>
			</div>
	   </div>
	';
	}
//Submissions page
	elseif($context['TPortal']['dlsub']=='adminsubmission')
	{
		echo '
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-dlsubmissions'].'</h3></div>
		<div id="any-submitted" class="admintable admin-area">
			<div class="windowbg noup padding-div">
				<table class="table_grid tp_grid" style="width:100%">
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col" class="submissions">
						<div>
							<div class="float-items pos" style="width:30%;"><strong>'.$txt['tp-dlname'].'</strong></div>
							<div class="title-admin-area float-items" style="width:20%;"><strong>'.$txt['tp-dlfilename'].'</strong></div>
							<div class="title-admin-area float-items" style="width:20%;"><strong>'.$txt['tp-created'].'</strong></div>
							<div class="title-admin-area float-items" style="width:20%;"><strong>'.$txt['tp-uploadedby'].'</strong></div>
							<div class="title-admin-area float-items tpcenter" style="width:10%;"><strong>'.$txt['tp-dlfilesize'].'</strong></div>
							<p class="clearthefloat"></p>
						</div>
					</th>
					</tr>
				</thead>
				<tbody>';
		if(isset($context['TPortal']['dl_admitems']) && count($context['TPortal']['dl_admitems'])>0)
		{
			foreach($context['TPortal']['dl_admitems'] as $cat)
			{
				echo '
				<tr class="windowbg">
				<td class="articles">
					<div>
						<div class="fullwidth-on-res-layout  float-items" style="width:30%;"><a href="'.$cat['href'].'">'.$cat['name'].'</a></div>
						<div class="fullwidth-on-res-layout  float-items" style="width:20%;">
							<div id="show-on-respnsive-layout">'.$txt['tp-dlfilename'].'</div>
							<div id="size-on-respnsive-layout" style="word-break:break-all;">'.$cat['file'].'</div>
						</div>
						<div class="fullwidth-on-res-layout  float-items" style="width:20%;">
							<div id="show-on-respnsive-layout">'.$txt['tp-created'].'</div>
							<div id="size-on-respnsive-layout">'.$cat['date'].'</div>
						</div>
						<div class="fullwidth-on-res-layout  float-items" style="width:20%;">
							<div id="show-on-respnsive-layout">'.$txt['tp-uploadedby'].'</div>
							'.$cat['author'].'
						</div>
						<div class="fullwidth-on-res-layout float-items tpcenter" style="width:10%;">
							<div id="show-on-respnsive-layout">'.$txt['tp-dlfilesize'].'</div>
							'. $cat['filesize'].''.$txt['tp-kb'].'
						</div>
						<p class="clearthefloat"></p>
					</div>
				</td>
				</tr>';
			}
		}
		else
		{
			echo '
				<tr class="windowbg">
				<td class="articles">
					<div class="padding-div">'.$txt['tp-nosubmissions'].'</div>
				</td>
				</tr>';
		}
			echo '
			</tbody>
			</table>
			<div class="padding-div">&nbsp;</div>
		</div>
	</div>';
	}
//FTP page
	elseif($context['TPortal']['dlsub']=='adminftp')
	{
		echo '
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-ftpstrays'].'</h3></div>
			<div id="ftp-files" class="admintable admin-area">
				<div class="information smalltext">'.$txt['tp-assignftp'].'</div>
				<div class="windowbg noup padding-div">';

		// alert if new files were added recently
		if(!empty($_GET['ftpcat']))
			echo '<div style="margin-bottom:1ex;text-align:center;border:dotted 2px red;padding:2ex;"><b><a href="'.$scripturl.'?action=tportal;dl=admincat'.$_GET['ftpcat'].'">'.$txt['tp-adminftp_newfiles'].'</a></b><br></div>';

		if(count($context['TPortal']['tp-downloads'])>0){
			$ccount=0;
			foreach($context['TPortal']['tp-downloads'] as $file){
				if(!in_array($file['file'], $context['TPortal']['dl_allitems']))
					echo '<div><input name="assign-ftp-checkbox'.$ccount.'" type="checkbox" value="'.$file['file'].'"> '.substr($file['file'],0,40).'', strlen($file['file'])>40 ? '..' : '' , '  ['.$file['size'].' '.$txt['tp-kb'].']  - <b><a href="'.$scripturl.'?action=tportal;dl=upload;ftp='.$file['id'].'">'.$txt['tp-dlmakeitem'].'</a></b></div>';
					$ccount++;
			}
			echo '<div style="padding: 5px;"><span class="smalltext">
			 '.$txt['tp-newcatassign'].' <input name="assign-ftp-newcat" type="text" value=""> ';
			// the parent category - or the one to use
				// which parent category?
				echo $txt['tp-assigncatparent'].'</span>
					<select size="1" name="assign-ftp-cat" style="margin-top: 4px;">
						<option value="0" selected>'.$txt['tp-dlnocategory'].'</option>';
				if(count($context['TPortal']['admuploadcats'])>0)
				{
					foreach($context['TPortal']['admuploadcats'] as $ucats)
					{
							echo '
						<option value="'.$ucats['id'].'">', (!empty($ucats['indent']) ? str_repeat("-", $ucats['indent']) : '') ,' '.$ucats['name'].'</option>';
					}
				}
				else
					echo '
						<option value="0">'.$txt['tp-none-'].'</option>';
			echo '
					</select><p class="clearthefloat"></p><br><hr /><br>';

			echo '<input name="ftpdlsend" type="submit" class="button button_submit" value="'.$txt['tp-submit'].'">
				  </div>';
		}
		echo '</div></div>';
	}
	elseif(substr($context['TPortal']['dlsub'],0,12)=='admineditcat')
	{
		if(isset($context['TPortal']['admcats']) && count($context['TPortal']['admcats'])>0)
		{
			foreach($context['TPortal']['admcats'] as $cat)
			{
//Edit category page
				echo '
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-dlcatedit'].'</h3></div>
		<div id="editupcat" class="admintable admin-area">
			<div class="windowbg noup padding-div">
				<dl class="settings">
					<dt>
						<label for="dladmin_name">'.$txt['tp-dlname'].'</label>
					</dt>
					<dd>
						<input style="width: 100ex;max-width:98%!important;" name="dladmin_name'.$cat['id'].'"  id="dladmin_name" type="text" value="'.$cat['name'].'"><br>
					<dt>
						<label for="dladmin_parent">'.$txt['tp-dlparent'].'</label>
					</dt>
					<dd>';
				// which parent category?
				echo '
					<select size="1" name="dladmin_parent'.$cat['id'].'" id="dladmin_parent" style="margin-top: 4px;">
						<option value="0" ', $cat['parent']==0 ? 'selected' : '' ,'>'.$txt['tp-dlnocategory'].'</option>';

				if(count($context['TPortal']['admuploadcats'])>0)
				{
					foreach($context['TPortal']['admuploadcats'] as $ucats)
					{
						if($ucats['id']!=$cat['id'])
						{
							echo '
						<option value="'.$ucats['id'].'" ', $ucats['id']==$cat['parent'] ? 'selected' : '' ,'>', (!empty($ucats['indent']) ? str_repeat("-",$ucats['indent']) : '') ,' '.$ucats['name'].'</option>';
						}
					}
				}
				else
					echo '
						<option value="0">'.$txt['tp-none-'].'</option>';
			}
			echo '
					</select><br>
					</dd>
					<dt>
						<label for="dladmin_link">'.$txt['tp-shortname'].'</label>
					</dt>
					<dd>
						<input name="dladmin_link'.$cat['id'].'" id="dladmin_link" type="text" value="'.$cat['shortname'].'"><br>
					</dd>
					<dt>
						<label for="dladmin_icon">'.$txt['tp-icon'].'</label>
					</dt>
					<dd>
						<div><select size="1" name="dladmin_icon'.$cat['id'].'" id="dladmin_icon" onchange="dlcheck(this.value)">
						<option value="blank.gif" selected>'.$txt['tp-chooseicon'].'</option>
						<option value="blank.gif">'.$txt['tp-noneicon'].'</option>';

				// output the icons
				$selicon = substr($cat['icon'], strrpos($cat['icon'], '/')+1);
				foreach($context['TPortal']['dlicons'] as $dlicon => $value)
					echo '
						<option ', ($selicon == $value) ? 'selected="selected" ' : '','value="'.$value.'">'. $value.'</option>';

				echo '
					</select>
					<br><br><img name="dlicon" src="'.$cat['icon'].'" alt="" />
					<script type="text/javascript">
						function dlcheck(icon)
						{
							document.dlicon.src= "'.$boardurl.'/tp-files/tp-downloads/icons/" + icon
						}
					</script>
					<br><br>
						</div>
					</dd>
				</dl>
				<hr>
				<div style="padding:1%;"><b>'.$txt['tp-body'].':</b><br>';

				if($context['TPortal']['dl_wysiwyg'] == 'html')
					TPwysiwyg('dladmin_text'.$cat['id'], html_entity_decode($cat['description'],ENT_QUOTES), true,'qup_dladmin_text', isset($context['TPortal']['usersettings']['wysiwyg']) ? $context['TPortal']['usersettings']['wysiwyg'] : 0);
				elseif($context['TPortal']['dl_wysiwyg'] == 'bbc')
					TP_bbcbox($context['TPortal']['editor_id']);
				else
					echo '<textarea name="dladmin_text'.$cat['id'].'" id="tp_article_body">'. html_entity_decode($cat['description'],ENT_QUOTES).'</textarea>';


				echo '
				</div>

		<hr />
			<dl class="settings">
				<dt>
					<span class="font-strong">'.$txt['tp-dlaccess'].'</span>
				</dt>
				<dd><div class="tp_largelist2">';
    		// access groups
    		// loop through and set membergroups
			if(!empty($cat['access']))
				$tg=explode(',',$cat['access']);
			else
				$tg=array();

    		foreach($context['TPortal']['dlgroups'] as $mg)
    		{
    			if($mg['posts']=='-1' && $mg['id']!='1')
    			{
					echo '
					<input name="dladmin_group'.$mg['id'].'" id="dladmin_group'.$mg['id'].'" type="checkbox" value="'.$cat['id'].'"';
             		if(in_array($mg['id'],$tg))
             			echo ' checked';
             		echo '><label for="dladmin_group'.$mg['id'].'"> '.$mg['name'].' </label><br>';
         		}
    		}
   			// if none is chosen, have a control value
			echo '</div><br><input type="checkbox" onclick="invertAll(this, this.form, \'dladmin_group\');" />'.$txt['tp-checkall'].'
					<input name="dladmin_group-2" type="hidden" value="'.$cat['id'].'">
				</dd>
			</dl>';
		}
		echo '
				<div style="padding:1%;"><input name="dlsend" type="submit" class="button button_submit" value="'.$txt['tp-submit'].'"></div>
			</div>
		</div>';
	}
	elseif($context['TPortal']['dlsub']=='adminaddcat')
	{
//Add category page
		echo '
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-dlcatadd'].'</h3></div>
		<div id="dl-addcat" class="admintable admin-area">
			<div class="windowbg noup padding-div">
				<dl class="settings">
					<dt>
						<label for="newdladmin_name">'.$txt['tp-name'].'</label>
					</dt>
					<dd>
						<input size="100" name="newdladmin_name" id="newdladmin_name" type="text" value="">
					</dd>
					<dt>
						<label for="newdladmin_link">'.$txt['tp-shortname'].'</label>
					</dt>
					<dd>
						<input name="newdladmin_link" id="newdladmin_link" type="text" value="">
					</dd>
					<dt>
						<label for="newdladmin_parent">'.$txt['tp-dlparent'].'</label>
					</dt>
					<dd>';
		// which parent category?
		echo '
					<select size="1" name="newdladmin_parent" id="newdladmin_parent" style="margin-top: 4px;">
						<option value="0" selected>'.$txt['tp-dlnocategory'].'</option>';

		foreach($context['TPortal']['admuploadcats'] as $ucats)
		{
			echo '
			     		<option value="'.$ucats['id'].'">', (!empty($ucats['indent']) ? str_repeat("-",$ucats['indent']) : '') ,' '.$ucats['name'].'</option>';
		}
		echo '
					</select><br>
					</dd>
					<dt>
						<label for="newdladmin_icon" >'.$txt['tp-icon'].'</label>
					</dt>
					<dd>
						<div><select size="1" name="newdladmin_icon" id="newdladmin_icon"  onchange="dlcheck(this.value)">';

		echo '
				<option value="blank.gif" selected>'.$txt['tp-noneicon'].'</option>';

		// output the icons
		foreach($context['TPortal']['dlicons'] as $dlicon => $value)
			echo '
						<option value="'.$value.'">'.$value.'</option>';

		echo '
					</select>
					<br><br><img name="dlicon" src="'.$boardurl.'/tp-files/tp-downloads/icons/blank.gif" alt="" />
				<script type="text/javascript">
					function dlcheck(icon)
					{
						document.dlicon.src= "'.$boardurl.'/tp-files/tp-downloads/icons/" + icon
					}
				</script>
				</div>
					</dd>
				</dl>
				<hr>
				<div style="padding:1%;"><b>'.$txt['tp-body'].':</b><br>';

				if($context['TPortal']['dl_wysiwyg'] == 'html')
					TPwysiwyg('newdladmin_text', '', true,'qup_dladmin_text', isset($context['TPortal']['usersettings']['wysiwyg']) ? $context['TPortal']['usersettings']['wysiwyg'] : 0);
				elseif($context['TPortal']['dl_wysiwyg'] == 'bbc')
					TP_bbcbox($context['TPortal']['editor_id']);
				else
					echo '<textarea name="newdladmin_text" id="tp_article_body"></textarea>';
			echo '</div>
			<hr>
				<dl class="settings">
					<dt>
						<span class="font-strong">'.$txt['tp-dlaccess'].'</span>
					</dt>
					<dd>
						<div class="tp_largelist2">';
    		// access groups
    		// loop through and set membergroups
			if(!empty($cat['access']))
				$tg=explode(',',$cat['access']);
			else
				$tg=array();

    		foreach($context['TPortal']['dlgroups'] as $mg)
    		{
    			if($mg['posts']=='-1' && $mg['id']!='1')
    			{
					echo '
					<input name="newdladmin_group'.$mg['id'].'" id="newdladmin_group'.$mg['id'].'" type="checkbox" value="1"';
             		if(in_array($mg['id'],$tg))
             			echo ' checked';
             		echo '><label for="newdladmin_group'.$mg['id'].'"> '.$mg['name'].' </label><br>';
         		}
    		}
   			// if none is chosen, have a control value
			echo '		</div>
						<input id="dladmin_group-2" type="checkbox" onclick="invertAll(this, this.form, \'newdladmin_group\');" /><label for="dladmin_group-2">'.$txt['tp-checkall'].'</label>
						<input name="dladmin_group-2" type="hidden" value="1">
					</dd>
				</dl>';

		echo '
				<div class="padding-div">
					<input name="newdlsend" type="submit" class="button button_submit" value="'.$txt['tp-submit'].'">
				</div>
			</div>
		</div>';
	}
	echo '
	</form>
</div><p class="clearthefloat"></p>';
}

?>
