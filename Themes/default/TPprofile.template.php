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

function template_tp_summary()
{
	global $settings, $txt, $context, $scripturl;

	echo '
	<div class="title_bar"><h3 class="titlebg">'.$txt['tpsummary'].'</h3></div>
	<table width="100%" cellpadding="4" cellspacing="1" border="0" align="center" class="bordercolor">
		<tr class="windowbg2">
			<td>'.$txt['tpsummary_art'].'</td>
			<td width="60%" style="font-weight: bold;">'.$context['TPortal']['tpsummary']['articles'].'</td>
		</tr>
		<tr class="windowbg2">
			<td>'.$txt['tpsummary_dl'].'</td>
			<td  style="font-weight: bold;">'.$context['TPortal']['tpsummary']['uploads'].'</td>
		</tr>
	</table>';
}

function template_tp_articles()
{
	global $settings, $txt, $context, $scripturl;

	if($context['TPortal']['profile_action'] == ''){
		echo '
	<div>
		<table width="100%" cellpadding="8" cellspacing="1" border="0">
			<tr class="windowbg2">
				<td colspan="7" style="padding: 1em;">';

		echo $txt['tp-prof_allarticles']. ' <b>'.$context['TPortal']['all_articles'].'</b><br />';
		if($context['TPortal']['approved_articles']>0)
			echo $txt['tp-prof_waitapproval1'].' <b>'.$context['TPortal']['approved_articles'].'</b> '.$txt['tp-prof_waitapproval2'].'<br />';

		if($context['TPortal']['off_articles']==0)
			echo $txt['tp-prof_offarticles2'].'<br />';
		else
			echo $txt['tp-prof_offarticles'].' <b>'.$context['TPortal']['off_articles'].'</b><br />';

		echo '
				</td>
			</tr>
			<tr class="catbg">
				<td align="center" nowrap="nowrap">', $context['TPortal']['tpsort']=='subject' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=subject">'.$txt['subject'].'</a></td>
				<td align="center" nowrap="nowrap">', ($context['TPortal']['tpsort']=='date'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=date">'.$txt['date'].'</a></td>
				<td align="center" nowrap="nowrap">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=views">'.$txt['views'].'</a></td>
				<td align="center" nowrap="nowrap">'.$txt['tp-ratings'].'</td>
				<td align="center" nowrap="nowrap">', $context['TPortal']['tpsort']=='comments' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=comments">'.$txt['tp-comments'].'</a></td>
				<td align="center" nowrap="nowrap">', $context['TPortal']['tpsort']=='category' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';area=tparticles;tpsort=category">'.$txt['tp-category'].'</a></td>
				<td align="center" nowrap="nowrap">'.$txt['tp-edit'].'</td>
			</tr>';
		if(isset($context['TPortal']['profile_articles']) && sizeof($context['TPortal']['profile_articles'])>0){
			foreach($context['TPortal']['profile_articles'] as $art){
				echo '
			<tr class="windowbg2">
				<td><a href="'.$art['href'].'" target="_blank">', $art['approved']==0 ? '(' : '' , $art['off']==1 ? '<i>' : '' ,  $art['subject'], $art['off']==1 ? '</i>' : '' , $art['approved']==0 ? ')' : '' ,  '</td>
				<td align="center" class="smalltext" nowrap="nowrap">',$art['date'],'</td>
				<td align="center">',$art['views'],'</td>
				<td nowrap="nowrap">' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' .$settings['tp_images_url']. '/TPblue.gif" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $art['rating_average'])) : $art['rating_average']) . ' (' . $art['rating_votes'] . ' ' . $txt['tp-ratingvotes'] . ') </td>
				<td align="center">',$art['comments'],'</td>
				<td>',$art['category'],'</td>
				<td align="center">
					', allowedTo('tp_editownarticle') ? '<a href="'. $art['editlink'] .'"><img border="0" src="' .$settings['tp_images_url']. '/TPmodify.gif" alt="" /></a>' : '' , '
				</td>
			</tr>';
			}
		}
		if(!empty($context['TPortal']['pageindex']))
			echo '
			<tr class="windowbg">
				<td colspan="7">'.$context['TPortal']['pageindex'].'</td>
			</tr>';
		echo '
		</table>
	</div>';
	}
	elseif($context['TPortal']['profile_action'] == 'settings'){
		echo '
	<div class="bordercolor" style="margin-left: 1ex;">
		<table width="100%" cellpadding="4" cellspacing="1" border="0">
			<tr class="titlebg">
				<td colspan="7">'.$txt['tp-dlsettings'].'</td>
			</tr>
			<tr class="windowbg2">
				<td colspan="7" style="padding: 2ex;">
					<form name="TPadmin3" action="' . $scripturl . '?action=tpmod;sa=savesettings" method="post">
						<input type="hidden" name="sc" value="', $context['session_id'], '" />
						<input type="hidden" name="memberid" value="', $context['TPortal']['selected_member'], '" />
						<input type="hidden" name="item" value="', $context['TPortal']['selected_member_choice_id'], '" />
						';
		if(!empty($context['TPortal']['allow_wysiwyg']))
			echo '<table cellpadding="8">
					<tr>
						<td valign="top" align="right">'.$txt['tp-wysiwygchoice'].':</td>
						<td>
							<input name="tpwysiwyg" type="radio" value="0" ' , ($context['TPortal']['selected_member_choice'] =='0' || $context['TPortal']['selected_member_choice'] == '1') ? 'checked' : '' , '> '.$txt['tp-no'].'<br />
							<input name="tpwysiwyg" type="radio" value="2" ' , $context['TPortal']['selected_member_choice'] =='2' ? 'checked' : '' , '> '.$txt['tp-fckeditor'].'<br />
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" value="'.$txt['tp-send'].'" name="send"></td>
					</tr>
				</table>';
			echo '
		</form>';


		echo '
				</td>
			</tr>
		</table>
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
	<div class="bordercolor">
		<table width="100%" cellpadding="4" cellspacing="1" border="0">
			<tr class="windowbg">
				<td colspan="6" class="smalltext" style="padding: 2ex;">'.$txt['downloadsprofile2'].'</td>
			</tr>
			<tr class="windowbg2">
				<td colspan="6" style="padding: 2ex;">';

	echo $txt['tp-prof_alldownloads'].' <b>'.$context['TPortal']['all_downloads'].'</b><br />';
	if($context['TPortal']['approved_downloads']>0)
		echo $txt['tp-prof_approvarticles'].' <b>'.$context['TPortal']['approved_downloads'].'</b> '.$txt['tp-prof_approvdownloads'].'<br />';

	echo '
				</td>
			</tr>
			<tr class="catbg">
				<td align="center" nowrap="nowrap">', $context['TPortal']['tpsort']=='name' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=name">'.$txt['subject'].'</a></td>
				<td align="center" nowrap="nowrap">', ($context['TPortal']['tpsort']=='created'  || $context['TPortal']['tpsort']=='') ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=created">'.$txt['date'].'</a></td>
				<td align="center" nowrap="nowrap">', $context['TPortal']['tpsort']=='views' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=views">'.$txt['views'].'</a></td>
				<td align="center" nowrap="nowrap">'.$txt['tp-ratings'].'</td>
				<td align="center" nowrap="nowrap">', $context['TPortal']['tpsort']=='downloads' ? '<img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=profile;u='.$context['TPortal']['memID'].';sa=tpdownloads;tpsort=downloads">'.$txt['tp-downloads'].'</a></td>
				<td align="center" nowrap="nowrap">'. $txt['tp-edit'] .'</td>
			</tr>';
if(isset($context['TPortal']['profile_uploads']) && sizeof($context['TPortal']['profile_uploads'])>0){
	foreach($context['TPortal']['profile_uploads'] as $art){
		echo '
			<tr class="windowbg2">
				<td><a href="'.$art['href'].'" target="_blank">', $art['approved']==0 ? '(' : '' , $art['name'], $art['approved'] == 0 ? ')' : '' ,  '</td>
				<td align="center" class="smalltext" nowrap="nowrap">',$art['created'],'</td>
				<td align="center">',$art['views'],'</td>
				<td nowrap="nowrap">' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' .$settings['tp_images_url']. '/TPblue.gif" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $art['rating_average'])) : $art['rating_average']) . ' (' . $art['rating_votes'] . ' ' . $txt['tp-ratingvotes'] . ') </td>
				<td align="center">',$art['downloads'],'</td>
				<td align="center">' , $art['editlink']!='' ? '<a href="'.$art['editlink'].'"><img border="0" src="' .$settings['tp_images_url']. '/TPedit.gif" alt="" /></a>' : '' , '</td>
			</tr>';
	}
}
	echo '
			<tr class="windowbg">
				<td colspan="6" style="padding: 2ex; font-weight: bold;">'.$context['TPortal']['pageindex'].'</td>
			</tr>
		</table>
	</div>';
}

?>