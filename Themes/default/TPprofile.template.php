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
 * Copyright (C) 2019 - The TinyPortal Team
 *
 */
 
// ** Sections **
// Portal Summary Page
// Articles Page
// Articles Settings Page
// Uploaded Files Page

// Portal Summary Page
function template_tp_summary()
{
	global $settings, $txt, $context, $scripturl;

	echo '
	<div></div>
	<div class="cat_bar"><h3 class="catbg">'.$txt['tpsummary'].'</h3></div>
	<div id="tp_summary" class="windowbg padding-div">
		<div>
			<div class="float-items" style="width:38%;">'.$txt['tpsummary_art'].'</div>
			<div class="float-items" style="width:58%;font-weight: bold;">'.$context['TPortal']['tpsummary']['articles'].'</div>
		    <p class="clearthefloat"></p>
		</div>
		<div>
			<div class="float-items" style="width:38%;">'.$txt['tpsummary_dl'].'</div>
			<div class="float-items" style="width:58%;font-weight: bold;">'.$context['TPortal']['tpsummary']['uploads'].'</div>
		    <p class="clearthefloat"></p>
		</div>
		<div class="padding-div"></div>
	</div>';
}

// Articles Page
function template_tp_articles()
{
	global $settings, $txt, $context, $scripturl, $user_info;

	if($context['TPortal']['profile_action'] == ''){
		echo '
	<div>
		<div></div>
		<div id="tp_profile_articles" class="windowbg padding-div" >
			<div class="windowbg addborder tp_pad">';

		echo $txt['tp-prof_allarticles']. ' <b>'.$context['TPortal']['all_articles'].'</b><br>';
		if($context['TPortal']['approved_articles']>0)
			echo $txt['tp-prof_waitapproval1'].' <b>'.$context['TPortal']['approved_articles'].'</b> '.$txt['tp-prof_waitapproval2'].'<br>';

		if($context['TPortal']['off_articles']==0)
			echo $txt['tp-prof_offarticles2'].'<br>';
		else
			echo $txt['tp-prof_offarticles'].' <b>'.$context['TPortal']['off_articles'].'</b><br>';

		echo '
				</div><br>
	<table class="table_grid tp_grid" style="width:100%";>
		<thead>
			<tr class="title_bar titlebg2">
			<th scope="col" class="articles">
			<div class="font-strong" style="padding:0px;">
				<div align="left" class="float-items pos" style="width:25%;">', $context['TPortal']['tpsort']=='subject' ? '<img src="' .$settings['tp_images_url']. '/TPsort_up.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=subject">'.$txt['subject'].'</a></div>
				<div align="left" class="float-items title-admin-area" style="width:20%;">', ($context['TPortal']['tpsort']=='date'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=date">'.$txt['date'].'</a></div>
				<div align="center" class="float-items title-admin-area" style="width:10%;">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=views">'.$txt['views'].'</a></div>
				<div align="left" class="float-items title-admin-area" style="width:15%;">'.$txt['tp-ratings'].'</div>
				<div align="center" class="float-items title-admin-area" style="width:10%;">', $context['TPortal']['tpsort']=='comments' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=comments">'.$txt['tp-comments'].'</a></div>
				<div align="left" class="float-items title-admin-area" style="width:15%;">', $context['TPortal']['tpsort']=='category' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=category">'.$txt['tp-category'].'</a></div>
				<div align="center" class="float-items title-admin-area" style="width:5%;">'.$txt['tp-edit'].'</div>
			    <p class="clearthefloat"></p>
			</div>
			</th>
			</tr>
		</thead>
		<tbody>';
		if(isset($context['TPortal']['profile_articles']) && sizeof($context['TPortal']['profile_articles'])>0){
			foreach($context['TPortal']['profile_articles'] as $art){
				echo '
			<tr class="windowbg">
			<td class="articles">
				<div class="float-items fullwidth-on-res-layout" style="width:25%;"><a href="'.$art['href'].'" target="_blank">', $art['approved']==0 ? '(' : '' , $art['off']==1 ? '<i>' : '' ,  $art['subject'], $art['off']==1 ? '</i>' : '' , $art['approved']==0 ? ')' : '' ,  '</a></div>
				<a href="" class="clickme">'.$txt['tp-more'].'</a>
				<div class="box" style="width:75%;float:left;">
					<div class="smalltext float-items fullwidth-on-res-layout" style="width:27%;">
						<div id="show-on-respnsive-layout">', ($context['TPortal']['tpsort']=='date'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=date">'.$txt['date'].'</a></div>
						<div id="size-on-respnsive-layout">',$art['date'],'</div>
					</div>
					<div class="fullwidth-on-res-layout float-items" style="width:13.5%;text-align:center;">
						<div id="show-on-respnsive-layout">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=views">'.$txt['views'].'</a></div>
						',$art['views'],'
					</div>
					<div class="fullwidth-on-res-layout float-items" style="width:20%;">
						<div id="show-on-respnsive-layout">'.$txt['tp-ratings'].'</div>
						' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' .$settings['tp_images_url']. '/TPblue.png" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $art['rating_average'])) : $art['rating_average']) . ' (' . $art['rating_votes'] . ' ' . $txt['tp-ratingvotes'] . ')
					</div>
					<div class="fullwidth-on-res-layout float-items" style="width:13.5%;text-align:center;">
						<div id="show-on-respnsive-layout">', $context['TPortal']['tpsort']=='comments' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=comments">'.$txt['tp-comments'].'</a></div>
						',$art['comments'],'
					</div>
					<div class="fullwidth-on-res-layout float-items" style="width:21%;">
						<div id="show-on-respnsive-layout">', $context['TPortal']['tpsort']=='category' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=category">'.$txt['tp-category'].'</a></div>
						',$art['category'],'
					</div>
					<div class="fullwidth-on-res-layout float-items" style="width:4%;">
						<div id="show-on-respnsive-layout">'.$txt['tp-edit'].'</div>
						', allowedTo('tp_editownarticle') ? '<a href="'. $art['editlink'] .'"><img src="' .$settings['tp_images_url']. '/TPedit.png" alt="" /></a>' : '' , '
					</div><p class="clearthefloat"></p></div><p class="clearthefloat"></p>
			</td>
			</tr>';
			}
		}
	echo '
		</tbody>
	</table>';
			
		if(!empty($context['TPortal']['pageindex']))
			echo '
			<div class="padding-div">
				'.$context['TPortal']['pageindex'].'
			</div>';
		echo '
		</div>
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
	}
// Articles Settings Page
	elseif($context['TPortal']['profile_action'] == 'settings') {
		echo '
	<div id="tp_profile_articles_settings" class="bordercolor windowbg padding-div">
		<div class="font-strong padding-div">
			'.$txt['tp-dlsettings'].'
		</div>
			<div class="padding-div">
				<form name="TPadmin3" action="' . $scripturl . '?action=tportal;sa=savesettings" method="post">
					<input type="hidden" name="sc" value="', $context['session_id'], '" />
					<input type="hidden" name="memberid" value="', $context['TPortal']['selected_member'], '" />
					<input type="hidden" name="item" value="', $context['TPortal']['selected_member_choice_id'], '" />
                ';

		    if(!empty($context['TPortal']['allow_wysiwyg']) && ($user_info['id'] == $context['TPortal']['selected_member'])) {
			    echo '<div>
					<dl class="settings">
						<dt>'.$txt['tp-wysiwygchoice'].':
						</dt>
						<dd>
							<input name="tpwysiwyg" type="radio" value="2" ' , $context['TPortal']['selected_member_choice'] =='2' ? 'checked' : '' , '> '.$txt['tp-fckeditor'].'<br>
							<input name="tpwysiwyg" type="radio" value="0" ' , ($context['TPortal']['selected_member_choice'] =='0' || $context['TPortal']['selected_member_choice'] == '1') ? 'checked' : '' , '> '.$txt['tp-no'].'<br>
							</dd>
					</dl>
						<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="send"></div>
				</div>';
            }
			echo '
				</form>';
		echo '
			</div>
	</div>';
	}
}

// Uploaded Files Page
function template_tp_download()
{
	global $settings, $txt, $context, $scripturl;

	echo '
		<div></div>
		<div class="cat_bar"><h3 class="catbg">'.$txt['downloadsprofile'].'</h3></div>
		<p class="information">'.$txt['downloadsprofile2'].'</p>
		<div id="tp_profile_uploaded" class="windowbg padding-div">
			<div class="windowbg addborder tp_pad">';

	echo $txt['tp-prof_alldownloads'].' <b>'.$context['TPortal']['all_downloads'].'</b><br>';
	if($context['TPortal']['approved_downloads']>0)
		echo $txt['tp-prof_approvarticles'].' <b>'.$context['TPortal']['approved_downloads'].'</b> '.$txt['tp-prof_approvdownloads'].'<br>';

	echo '
			</div><br>
		<table class="table_grid tp_grid" style="width:100%">
			<thead>
				<tr class="title_bar titlebg2 titlebg2">
				<th scope="col" class="tp_profile_uploaded">
				<div align="left" style="width:30%;" class="font-strong float-items pos">', $context['TPortal']['tpsort']=='name' ? '<img src="' .$settings['tp_images_url']. '/TPsort_up.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;area=tpdownload;u='.$context['TPortal']['memID'].';tpsort=name">'.$txt['subject'].'</a></div>
				<div align="left" style="width:25%;" class="font-strong float-items title-admin-area">', ($context['TPortal']['tpsort']=='created'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;area=tpdownload;u='.$context['TPortal']['memID'].';tpsort=created">'.$txt['date'].'</a></div>
				<div align="center" style="width:10%;" class="font-strong float-items title-admin-area">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;area=tpdownload;u='.$context['TPortal']['memID'].';tpsort=views">'.$txt['views'].'</a></div>
				<div align="left" style="width:20%;" class="font-strong float-items title-admin-area">'.$txt['tp-ratings'].'</div>
				<div align="center" style="width:10%;" class="font-strong float-items title-admin-area">', $context['TPortal']['tpsort']=='downloads' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;area=tpdownload;u='.$context['TPortal']['memID'].';tpsort=downloads">'.$txt['tp-downloads'].'</a></div>
				<div align="center" style="width:5%;" class="font-strong float-items title-admin-area">'. $txt['tp-edit'] .'</div>
			    <p class="clearthefloat"></p>
				</th>
				</tr>
			</thead>
			<tbody>';
    if(isset($context['TPortal']['profile_uploads']) && sizeof($context['TPortal']['profile_uploads'])>0)
    {
        foreach($context['TPortal']['profile_uploads'] as $art)
        {
            echo '
                <tr class="windowbg">
                <td class="uploads">
                    <div style="width:30%;" class="fullwidth-on-res-layout float-items">
                      <a href="'.$art['href'].'" target="_blank">', $art['approved']==0 ? '(' : '' , $art['name'], $art['approved'] == 0 ? ')' : '' ,  '</a>
                    </div>
                    <a href="" class="clickme">'.$txt['tp-more'].'</a>
                    <div class="box" style="width:70%;float:left;">
                      <div style="width:33.5%;" class="fullwidth-on-res-layout smalltext float-items">
                        <div id="show-on-respnsive-layout">', ($context['TPortal']['tpsort']=='created'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=created">'.$txt['date'].'</a></div>
                        <div id="size-on-respnsive-layout">',$art['created'],'</div>
                      </div>
                      <div style="width:16.5%;" class="fullwidth-on-res-layout float-items" align="center">
                        <div id="show-on-respnsive-layout">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=views">'.$txt['views'].'</a></div>
                        ',$art['views'],'
                      </div>
                      <div style="width:28%;" class="fullwidth-on-res-layout float-items">
                        <div id="show-on-respnsive-layout" style="word-break:break-all;">'.$txt['tp-ratings'].'</div>
                        ' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' .$settings['tp_images_url']. '/TPblue.png" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $art['rating_average'])) : $art['rating_average']) . ' (' . $art['rating_votes'] . ' ' . $txt['tp-ratingvotes'] . ')
                      </div>
                      <div style="width:16%;" class="fullwidth-on-res-layout float-items" align="center">
                        <div id="show-on-respnsive-layout" style="word-break:break-all;">', $context['TPortal']['tpsort']=='downloads' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=downloads">'.$txt['tp-downloads'].'</a></div>
                        ',$art['downloads'],'
                       </div>
                       <div style="width:6%;" class="fullwidth-on-res-layout float-items" align="center">
                        <div id="show-on-respnsive-layout">'. $txt['tp-edit'] .'</div>
                         ' , $art['editlink']!='' ? '<a href="'.$art['editlink'].'"><img src="' .$settings['tp_images_url']. '/TPedit.png" alt="" /></a>' : '' , '
                       </div>
                       <p class="clearthefloat"></p>
                    </div>
                    <p class="clearthefloat"></p>
                </td>
                </tr>';
        }
    }
	echo '
			</tbody>
		</table>
				<div class="tp_pad">'.$context['TPortal']['pageindex'].'</div>

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
}

?>
