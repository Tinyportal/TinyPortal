<?php
/**
 * @package TinyPortal
 * @version 1.5.0
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

function template_tp_summary()
{
	global $settings, $txt, $context, $scripturl;

	echo '
	<div class="title_bar"><h3 class="titlebg">'.$txt['tpsummary'].'</h3></div>
	<div style="width:100%;" align="center" id="tp_summary" class="bordercolor">
		<div class="windowbg2" style="margin-bottom:1px;text-align:left;">
			<div class="float-items" style="width:38%;">'.$txt['tpsummary_art'].'</div>
			<div class="float-items" style="width:58%;font-weight: bold;">'.$context['TPortal']['tpsummary']['articles'].'</div>
		    <p class="clearthefloat"></p>
		</div>
		<div class="windowbg2" style="text-align:left;">
			<div class="float-items" style="width:38%;">'.$txt['tpsummary_dl'].'</div>
			<div class="float-items" style="width:58%;font-weight: bold;">'.$context['TPortal']['tpsummary']['uploads'].'</div>
		    <p class="clearthefloat"></p>
		</div>
	</div>';
}

function template_tp_articles()
{
	global $settings, $txt, $context, $scripturl;

	if($context['TPortal']['profile_action'] == ''){
		echo '
	<div>
		<div id="tp_profile_articles" class="tpProfile bigger-width" style="width:100%;">
			<div class="windowbg2">
				<div style="padding: 1em;">';

		echo $txt['tp-prof_allarticles']. ' <b>'.$context['TPortal']['all_articles'].'</b><br />';
		if($context['TPortal']['approved_articles']>0)
			echo $txt['tp-prof_waitapproval1'].' <b>'.$context['TPortal']['approved_articles'].'</b> '.$txt['tp-prof_waitapproval2'].'<br />';

		if($context['TPortal']['off_articles']==0)
			echo $txt['tp-prof_offarticles2'].'<br />';
		else
			echo $txt['tp-prof_offarticles'].' <b>'.$context['TPortal']['off_articles'].'</b><br />';

		echo '
				</div>
			</div>
			<div class="catbg addborderleft" style="padding:0px;">
				<div align="center" class="float-items pos" style="width:26%;">', $context['TPortal']['tpsort']=='subject' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=subject">'.$txt['subject'].'</a></div>
				<div align="center" class="float-items title-admin-area" style="width:10%;">', ($context['TPortal']['tpsort']=='date'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=date">'.$txt['date'].'</a></div>
				<div align="center" class="float-items title-admin-area" style="width:7%;">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=views">'.$txt['views'].'</a></div>
				<div align="center" class="float-items title-admin-area" style="width:14%;">'.$txt['tp-ratings'].'</div>
				<div align="center" class="float-items title-admin-area" style="width:10%;">', $context['TPortal']['tpsort']=='comments' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=comments">'.$txt['tp-comments'].'</a></div>
				<div align="center" class="float-items title-admin-area" style="width:13%;">', $context['TPortal']['tpsort']=='category' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=category">'.$txt['tp-category'].'</a></div>
				<div align="center" class="float-items title-admin-area" style="width:5%;">'.$txt['tp-edit'].'</div>
			    <p class="clearthefloat"></p>
			</div>';
		if(isset($context['TPortal']['profile_articles']) && sizeof($context['TPortal']['profile_articles'])>0){
			foreach($context['TPortal']['profile_articles'] as $art){
				echo '
			<div class="windowbg2 addborder">
				<div class="float-items fullwidth-on-res-layout" style="width:26%;"><a href="'.$art['href'].'" target="_blank">', $art['approved']==0 ? '(' : '' , $art['off']==1 ? '<i>' : '' ,  $art['subject'], $art['off']==1 ? '</i>' : '' , $art['approved']==0 ? ')' : '' ,  '</a></div>
				<a href="" class="clickme">More</a>
				<div class="box" style="width:72%;float:left;">				
				<div class="smalltext float-items fullwidth-on-res-layout" style="width:15%;">
				  <div id="show-on-respnsive-layout">', ($context['TPortal']['tpsort']=='date'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=date">'.$txt['date'].'</a></div>
				  <div id="size-on-respnsive-layout">',$art['date'],'</div>
				</div>
				<div class="fullwidth-on-res-layout float-items" style="width:11%;text-align:center;">
				  <div id="show-on-respnsive-layout">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=views">'.$txt['views'].'</a></div>
				  ',$art['views'],'
				</div>
				<div class="fullwidth-on-res-layout float-items" style="width:20%;">
				  <div id="show-on-respnsive-layout">'.$txt['tp-ratings'].'</div>
				  ' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' .$settings['tp_images_url']. '/TPblue.png" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $art['rating_average'])) : $art['rating_average']) . ' (' . $art['rating_votes'] . ' ' . $txt['tp-ratingvotes'] . ')
				</div>
				<div class="fullwidth-on-res-layout float-items" style="width:15%;text-align:center;">
				  <div id="show-on-respnsive-layout">', $context['TPortal']['tpsort']=='comments' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=comments">'.$txt['tp-comments'].'</a></div>
				  ',$art['comments'],'
				</div>
				<div class="fullwidth-on-res-layout float-items" style="width:19%;">
				  <div id="show-on-respnsive-layout">', $context['TPortal']['tpsort']=='category' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=category">'.$txt['tp-category'].'</a></div>
				  ',$art['category'],'
				</div>
				<div class="fullwidth-on-res-layout float-items" style="width:8%;">
				    <div id="show-on-respnsive-layout">'.$txt['tp-edit'].'</div>
					', allowedTo('tp_editownarticle') ? '<a href="'. $art['editlink'] .'"><img border="0" src="' .$settings['tp_images_url']. '/TPmodify.png" alt="" /></a>' : '' , '
				</div><p class="clearthefloat"></p></div><p class="clearthefloat"></p>
			</div>';
			}
		}
		if(!empty($context['TPortal']['pageindex']))
			echo '
			<div class="windowbg" style="padding:1%;">
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
    $(this).text(($(this).text() === "Hide" ? "More" : "Hide")).next(".box").slideToggle();
    e.preventDefault();
});
});
</script>		
	</div>';
	}
	elseif($context['TPortal']['profile_action'] == 'settings'){
		echo '
	<div id="tp_profile_articles_settings" class="bordercolor" style="margin-left: 1ex;">
		<div width="100%">
			<div class="titlebg" style="padding:1%;">
				'.$txt['tp-dlsettings'].'
			</div>
			<div class="windowbg2">
				<div style="padding: 2ex;">
					<form name="TPadmin3" action="' . $scripturl . '?action=tpmod;sa=savesettings" method="post">
						<input type="hidden" name="sc" value="', $context['session_id'], '" />
						<input type="hidden" name="memberid" value="', $context['TPortal']['selected_member'], '" />
						<input type="hidden" name="item" value="', $context['TPortal']['selected_member_choice_id'], '" />
						';
		if(!empty($context['TPortal']['allow_wysiwyg']))
			echo '<div>
						<div>'.$txt['tp-wysiwygchoice'].':</div>
						<div>
							<input name="tpwysiwyg" type="radio" value="0" ' , ($context['TPortal']['selected_member_choice'] =='0' || $context['TPortal']['selected_member_choice'] == '1') ? 'checked' : '' , '> '.$txt['tp-no'].'<br />
							<input name="tpwysiwyg" type="radio" value="2" ' , $context['TPortal']['selected_member_choice'] =='2' ? 'checked' : '' , '> '.$txt['tp-fckeditor'].'<br />
						</div>
						<div align="center"><input type="submit" value="'.$txt['tp-send'].'" name="send"></div>
				</div>';
			echo '
		</form>';


		echo '
				</div>
			</div>
		</div>
	</div>';
	}
}

function template_tp_download()
{
	global $settings, $txt, $context, $scripturl;

	echo '
<div class="title_bar">
	<h3 class="titlebg">'.$txt['downloadsprofile'].'</h3>
</div>
	<div id="tp_profile_uploaded" class="tpProfile bordercolor">
		<div width="100%">
			<div class="windowbg">
				<div class="smalltext" style="padding: 2ex;">'.$txt['downloadsprofile2'].'</div>
			</div>
			<div class="windowbg2">
				<div style="padding: 2ex;">';

	echo $txt['tp-prof_alldownloads'].' <b>'.$context['TPortal']['all_downloads'].'</b><br />';
	if($context['TPortal']['approved_downloads']>0)
		echo $txt['tp-prof_approvarticles'].' <b>'.$context['TPortal']['approved_downloads'].'</b> '.$txt['tp-prof_approvdownloads'].'<br />';

	echo '
			   </div>
			</div>
			<div class="catbg addborderleft" style="padding:0px;">
				<div align="center" style="width:17%;" class="float-items pos">', $context['TPortal']['tpsort']=='name' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=name">'.$txt['subject'].'</a></div>
				<div align="center" style="width:19%;" class="float-items title-admin-area">', ($context['TPortal']['tpsort']=='created'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=created">'.$txt['date'].'</a></div>
				<div align="center" style="width:14%;" class="float-items title-admin-area">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=views">'.$txt['views'].'</a></div>
				<div align="center" style="width:14%;" class="float-items title-admin-area">'.$txt['tp-ratings'].'</div>
				<div align="center" style="width:14%;" class="float-items title-admin-area">', $context['TPortal']['tpsort']=='downloads' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=downloads">'.$txt['tp-downloads'].'</a></div>
				<div align="center" style="width:9%;" class="float-items title-admin-area">'. $txt['tp-edit'] .'</div>
			    <p class="clearthefloat"></p>
		  </div>';
if(isset($context['TPortal']['profile_uploads']) && sizeof($context['TPortal']['profile_uploads'])>0){
	foreach($context['TPortal']['profile_uploads'] as $art){
		echo '
			<div class="windowbg2 addborder">
				<div style="width:17%;" class="fullwidth-on-res-layout float-items">
				  <a href="'.$art['href'].'" target="_blank">', $art['approved']==0 ? '(' : '' , $art['name'], $art['approved'] == 0 ? ')' : '' ,  '</a>
				</div>

				<a href="" class="clickme">More</a>
				<div class="box" style="width:81%;float:left;">				
				  <div style="width:24%;" class="fullwidth-on-res-layout smalltext float-items">
				    <div id="show-on-respnsive-layout">', ($context['TPortal']['tpsort']=='created'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=created">'.$txt['date'].'</a></div>
				    <div id="size-on-respnsive-layout">',$art['created'],'</div>
				  </div>
				  <div style="width:18%;" class="fullwidth-on-res-layout float-items" align="center">
				    <div id="show-on-respnsive-layout">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=views">'.$txt['views'].'</a></div>
				    ',$art['views'],'
				  </div>
				  <div style="width:18%;" class="fullwidth-on-res-layout float-items">
				    <div id="show-on-respnsive-layout" style="word-break:break-all;">'.$txt['tp-ratings'].'</div>
				    ' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' .$settings['tp_images_url']. '/TPblue.png" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $art['rating_average'])) : $art['rating_average']) . ' (' . $art['rating_votes'] . ' ' . $txt['tp-ratingvotes'] . ') 
				  </div>
				  <div style="width:18%;" class="fullwidth-on-res-layout float-items" align="center">
				    <div id="show-on-respnsive-layout" style="word-break:break-all;">', $context['TPortal']['tpsort']=='downloads' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=downloads">'.$txt['tp-downloads'].'</a></div>
				    ',$art['downloads'],'
				   </div>
				   <div style="width:12%;" class="fullwidth-on-res-layout float-items" align="center">
				    <div id="show-on-respnsive-layout">'. $txt['tp-edit'] .'</div>
				     ' , $art['editlink']!='' ? '<a href="'.$art['editlink'].'"><img border="0" src="' .$settings['tp_images_url']. '/TPedit.png" alt="" /></a>' : '' , '
				   </div>
			       <p class="clearthefloat"></p>
				</div>
				<p class="clearthefloat"></p>		   
		    </div>';
	}
}
	echo '
			<div class="windowbg">
				<div style="padding: 2ex; font-weight: bold;">'.$context['TPortal']['pageindex'].'</div>
			</div>
		</div>
<script>
$(document).ready( function() {
var $clickme = $(".clickme"),
    $box = $(".box");

$box.hide();

$clickme.click( function(e) {
    $(this).text(($(this).text() === "Hide" ? "More" : "Hide")).next(".box").slideToggle();
    e.preventDefault();
});
});
</script>			
	</div>';
}

?>
