<?php
/**
 * @package TinyPortal
 * @version 3.0.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) - The TinyPortal Team
 *
 */

// Edit Block Page (including settings per block type)

function template_tp_above()
{
	global $context, $settings;

    // Body responsive classes
    $respClass = '';
	if (isset($context['TPortal'])) {
		$tm2 = '';
		$tm2=explode(",",$context['TPortal']['resp']);
		if (in_array($settings['theme_id'],$tm2)) {
			$respClass = "tp_nonresponsive";
			echo '
			<style>
				/** NON RESPONSIVE THEMES **/
				/** screen smaller then 900px **/
				@media all and (min-width: 0px) and (max-width: 900px) {
					body {
						min-width:900px!important;
					}
				}
			</style>';
		} else {$respClass = "tp_responsive";}
	}

    // Sidebars classes
    $sideclass = '';
	if (isset($context['TPortal']) && ($context['TPortal']['leftpanel']==0 && $context['TPortal']['rightpanel']==1)) {
		$sideclass =  "lrs rightpanelOn";
	}
	elseif (isset($context['TPortal']) && ($context['TPortal']['leftpanel']==1 && $context['TPortal']['rightpanel']==0)) {
		$sideclass =  "lrs leftpanelOn";
	}
	elseif (isset($context['TPortal']) && ($context['TPortal']['leftpanel']==1 && $context['TPortal']['rightpanel']==1)) {
		$sideclass =  "lrs lrON";
	}
	elseif (isset($context['TPortal']) && ($context['TPortal']['leftpanel']==0 && $context['TPortal']['rightpanel']==0)) {
		$sideclass =  "nosides";
	}
	else {
		$bclass =  "nosides";
	}

	echo '
	<div class="'. $sideclass .' '. $respClass .'">';

	if($context['TPortal']['toppanel']==1)
		echo '
		<div id="tptopbarHeader"' , in_array('tptopbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? ' style="display: none"' : '', '>
			'	, TPortal_panel('top') , '
		</div>';

	// Stick to old school floats here, until width gets down to 900px.
	echo '
		<div id="mainContainer">';

	// Tiny Portal left side bar - floated left by default.
	if($context['TPortal']['leftpanel']==1) {
		echo '
			<div id="tpleftbarHeader"', ($context['TPortal']['useroundframepanels']==1) ? ' class="roundframe"' : '', ' style="width:', ($context['TPortal']['leftbar_width']), 'px;', in_array('tpleftbarHeader', $context['tp_panels']) && ($context['TPortal']['showcollapse']==1) ? 'display:none' : '', '" >
				', TPortal_panel('left'), '
			</div><!-- #tpleftbarHeader -->';
	}

	// Tiny Portal right side bar - floated right by default.
	if($context['TPortal']['rightpanel']==1) {
		echo '
			<div id="tprightbarHeader"', ($context['TPortal']['useroundframepanels']==1) ? ' class="roundframe"' : '', ' style="width:', ($context['TPortal']['rightbar_width']), 'px;', in_array('tprightbarHeader', $context['tp_panels']) && ($context['TPortal']['showcollapse']==1) ? 'display:none' : '', '" >
				', TPortal_panel('right'), '
			</div><!-- #tprightbarHeader -->';
	}

	//	Tiny Portal centre panel - not floated.
	//	The important div for nailing width < 900px!
	echo '
			<div id="tpcenterContainer">
				<div id="tpcontentHeader">';

	if($context['TPortal']['centerpanel']==1) {
		echo '
					<div id="tpcenterbarHeader" style="' , in_array('tpcenterbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
						' , TPortal_panel('center') , '
					</div>';
    }
	echo '
                </div><!-- #tpcontentHeader -->';
}

// The board index goes here, naturally.
//	<div id="boardindex_table" class="boardindex_table"></div>
//	Then the info centre...
//	<div id="info_center" class="roundframe"></div>
//	Then wrap things up...

function template_tp_below()
{
	global $context;

	if($context['TPortal']['lowerpanel']==1)
		echo '
				<div id="tplowerbarHeader" style="' , in_array('tplowerbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
					' , TPortal_panel('lower') , '
				</div>';
	echo '
			</div><!-- #tpcenterContainer -->
		</div><!-- #mainContainer -->';
		// End #mainContainer

	if($context['TPortal']['bottompanel']==1)
		echo '
		<div id="tpbottombarHeader" style="' , in_array('tpbottombarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
			' , TPortal_panel('bottom') , '
		</div>';
	echo '
	</div>';
}

// Edit Block Page (including settings per block type)
function template_editblock()
{
	global $context, $settings, $txt, $scripturl, $boardurl, $modSettings;

	$newtitle = html_entity_decode(TPgetlangOption($context['TPortal']['blockedit']['lang'], $context['user']['language']));
	if(empty($newtitle)) {
		$newtitle = html_entity_decode($context['TPortal']['blockedit']['title']);
	}

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" enctype="multipart/form-data" action="' . $scripturl . '?action=tpadmin" method="post" onsubmit="submitonce(this);">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input type="hidden" name="tpadmin_form" value="blockedit">
		<input type="hidden" name="tpadmin_form_id" value="' . $context['TPortal']['blockedit']['id'] . '">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-editblock'] . '</h3></div>
		<div id="editblock" class="tp_admintable admin-area">
			<div class="windowbg noup padding-div">
				<div class="tp_formtable">
					<dl class="tp_title settings">
						<dt>
							<b>', $txt['tp-status'], '<img style="margin:0 1ex;" src="' . $settings['tp_images_url'] . '/TP' , $context['TPortal']['blockedit']['off']==0 ? 'green' : 'red' , '.png" alt="" /></b>
						</dt>
						<dd>
							<input type="radio" value="0" name="tp_block_off" id="tp_block_on"',$context['TPortal']['blockedit']['off']==0 ? ' checked="checked"' : '' ,' /><label for="tp_block_on">'.$txt['tp-on'].'</label>
							<input type="radio" value="1" name="tp_block_off" id="tp_block_off"',$context['TPortal']['blockedit']['off']==1 ? ' checked="checked"' : '' ,' /><label for="tp_block_off">'.$txt['tp-off'].'</label>
						</dd>
					</dl>
					<dl class="tp_title settings">
						<dt><label for="tp_block_title"><b>'.$txt['tp-title'].'</b></label></dt>
						<dd>
							<input type="text" id="tp_block_title" name="tp_block_title" value="' .$newtitle. '" size="50" required><br><br>
						</dd>
						<dt><label for="tp_block_type"><b>',$txt['tp-type'].'</b></label></dt>
						<dd>
							<select size="1" onchange="document.getElementById(\'blocknotice\').style.display=\'\';" name="tp_block_type" id="tp_block_type">
								<option value="0"' ,$context['TPortal']['blockedit']['type']=='0' ? ' selected' : '' , '>', $txt['tp-blocktype0'] , '</option>
								<option value="18"' ,$context['TPortal']['blockedit']['type']=='18' ? ' selected' : '' , '>', $txt['tp-blocktype18'] , '</option>
								<option value="19"' ,$context['TPortal']['blockedit']['type']=='19' ? ' selected' : '' , '>', $txt['tp-blocktype19'] , '</option>
								<option value="14"' ,$context['TPortal']['blockedit']['type']=='14' ? ' selected' : '' , '>', $txt['tp-blocktype14'] , '</option>
								<option value="5"' ,$context['TPortal']['blockedit']['type']=='5' ? ' selected' : '' , '>', $txt['tp-blocktype5'] , '</option>
								<option value="11"' ,$context['TPortal']['blockedit']['type']=='11' ? ' selected' : '' , '>', $txt['tp-blocktype11'] , '</option>
								<option value="10"' ,$context['TPortal']['blockedit']['type']=='10' ? ' selected' : '' , '>', $txt['tp-blocktype10'] , '</option>
								<option value="9"' ,$context['TPortal']['blockedit']['type']=='9' ? ' selected' : '' , '>', $txt['tp-blocktype9'] , '</option>
								<option value="2"' ,$context['TPortal']['blockedit']['type']=='2' ? ' selected' : '' , '>', $txt['tp-blocktype2'] , '</option>
								<option value="6"' ,$context['TPortal']['blockedit']['type']=='6' ? ' selected' : '' , '>', $txt['tp-blocktype6'] , '</option>
								<option value="12"' ,$context['TPortal']['blockedit']['type']=='12' ? ' selected' : '' , '>', $txt['tp-blocktype12'] , '</option>
								<option value="15"' ,$context['TPortal']['blockedit']['type']=='15' ? ' selected' : '' , '>', $txt['tp-blocktype15'] , '</option>
								<option value="4"' ,$context['TPortal']['blockedit']['type']=='4' ? ' selected' : '' , '>', $txt['tp-blocktype4'] , '</option>
								<option value="8"' ,$context['TPortal']['blockedit']['type']=='8' ? ' selected' : '' , '>', $txt['tp-blocktype8'] , '</option>
								<option value="16"' ,$context['TPortal']['blockedit']['type']=='16' ? ' selected' : '' , '>', $txt['tp-blocktype16'] , '</option>
								<option value="13"' ,$context['TPortal']['blockedit']['type']=='13' ? ' selected' : '' , '>', $txt['tp-blocktype13'] , '</option>
								<option value="3"' ,$context['TPortal']['blockedit']['type']=='3' ? ' selected' : '' , '>', $txt['tp-blocktype3'] , '</option>
								<option value="7"' ,$context['TPortal']['blockedit']['type']=='7' ? ' selected' : '' , '>', $txt['tp-blocktype7'] , '</option>
								<option value="1"' ,$context['TPortal']['blockedit']['type']=='1' ? ' selected' : '' , '>', $txt['tp-blocktype1'] , '</option>
							</select>
						</dd>
						<dt>
							<br><div class="padding-div"><input type="submit" class="button button_submit" value="' . $txt['tp-send'] . '" /></div>
						</dt>
						<dd>
							<div>
								<div id="blocknotice" class="smallpadding error middletext" style="display: none;">' , $txt['tp-blocknotice'] , '</div>
							</div>
						</dd>
					</dl>
					<hr>
					<div class="padding-div">';
// Block types: 5 (BBC code), 10 (PHP Code) and 11 (HTML & Javascript code)
			if($context['TPortal']['blockedit']['type']=='5' || $context['TPortal']['blockedit']['type']=='10' || $context['TPortal']['blockedit']['type']=='11')
			{
				if($context['TPortal']['blockedit']['type']=='11')
				{
					echo '<b>',$txt['tp-body'],'</b><br><textarea style="width: 94%;" name="tp_block_body" id="tp_block_body" rows="15" cols="40" wrap="auto">' , $context['TPortal']['blockedit']['body'], '</textarea>';
				}
				elseif($context['TPortal']['blockedit']['type']=='5')
				{
						echo '
						';
					TP_bbcbox($context['TPortal']['editor_id']);
				}
				else
						echo '<b>'.$txt['tp-body'].'</b><br>';
				if($context['TPortal']['blockedit']['type']=='10')
				{
					echo '
						<textarea style="width: 94%; margin: 0px 0px 10px;" name="tp_block_body" id="tp_block_body" rows="15" cols="40" wrap="auto">' ,  $context['TPortal']['blockedit']['body'] , '</textarea>
						<p><div class="tborder" style=""><p style="padding: 0 0 5px 0; margin: 0;">' , $txt['tp-blockcodes'] , ':</p>
							<select name="tp_blockcode" id="tp_blockcode" size="8" style="margin-bottom: 5px; width: 94%" onchange="changeSnippet(this.selectedIndex);">
								<option value="0" selected="selected">' , $txt['tp-none-'] , '</option>';
					if(!empty($context['TPortal']['blockcodes']))
					{
						foreach($context['TPortal']['blockcodes'] as $bc)
							echo '
								<option value="' , $bc['file'] , '">' , $bc['name'] , '</option>';
					}
					echo '
							</select>
							<p style="padding: 10px 0 10px 0; margin: 0;"><input type="button" value="' , $txt['tp-insert'] , '" name="blockcode_save" onclick="submit();" />
							<input type="checkbox" name="blockcode_overwrite" value="' . $context['TPortal']['blockedit']['id'] . '" /> ' , $txt['tp-blockcodes_overwrite'] , '</p>
						</div>
					<div id="blockcodeinfo" class="description" >&nbsp;</div>
					<script type="text/javascript"><!-- // --><![CDATA[
						function changeSnippet(indx)
						{
							var snipp = new Array();
							var snippAuthor = new Array();
							var snippTitle = new Array();
							snipp[0] = "";
							snippAuthor[0] = "";
							snippTitle[0] = "";';
					$count=1;
					foreach($context['TPortal']['blockcodes'] as $bc)
					{
						$what = str_replace(array(",",".","/","\n"),array("&#44;","&#46;","&#47;",""), $bc['text']);
						echo '
							snipp[' . $count . '] = "<div>' . $what . '</div>";
							snippTitle[' . $count . '] = "<h3 style=\"margin: 0 0 5px 0; padding: 0;\">' . $bc['name'].' <span style=\"font-weight: normal;\">' . $txt['tp-by'] . '</span> ' . $bc['author'] . '</h3>";
							';
							$count++;
					}
					echo '
							setInnerHTML(document.getElementById("blockcodeinfo"), snippTitle[indx] + snipp[indx]);
						}
					// ]]></script>';
				}
			}
// Block types: Recent Topics
			elseif($context['TPortal']['blockedit']['type']=='12'){
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$context['TPortal']['blockedit']['body']=10;
				echo '
					<dl class="settings">
						<dt>' . $txt['tp-rssblock-showavatar'].'</dt>
						<dd>
							<input type="radio" id="tp_block_useavataryes" name="tp_block_set_useavatar" value="1" ' , !$context['TPortal']['blockedit']['useavatar']== '0' ? ' checked' : '' ,' required><label for="tp_block_useavataryes">'.$txt['tp-yes'].'</label>
							<input type="radio" id="tp_block_useavatarno" name="tp_block_set_useavatar" value="0" ' , $context['TPortal']['blockedit']['useavatar']== '0' ? ' checked' : '' ,'><label for="tp_block_useavatarno">'.$txt['tp-no'].'</label>
						</dd>
						<dt>
							<label for="tp-recentlength">'.$txt['tp-lengthofrecenttopics'].'</label>
						</dt>
						<dd>
							<input type="number" id="tp-recentlength" name="tp_block_set_length" value="' ,(empty($context['TPortal']['blockedit']['length']) ? '25' : $context['TPortal']['blockedit']['length']), '" style="width: 6em" min="1" max="255" required><br>
						</dd>
						<dt><label for="tp_block_body">'.$txt['tp-numberofrecenttopics'].'</label></dt>
						<dd>
							<input type="number" id="tp_block_body" name="tp_block_body" value="' .$context['TPortal']['blockedit']['body']. '" style="width: 6em" min="1" required>
						</dd>
						<dt>'.$txt['tp-recentincexc'].'</dt>
						<dd>
							<input type="radio" id="tp_block_include" name="tp_block_set_include" value="1" ' , !$context['TPortal']['blockedit']['include']=='0' ? ' checked' : '' ,' required> <label for="tp_block_include">'.$txt['tp-recentinboard'].'</label><br>
							<input type="radio" id="tp_block_exclude" name="tp_block_set_include" value="0" ' , $context['TPortal']['blockedit']['include']=='0' ? 'checked' : '' ,'> <label for="tp_block_exclude">'.$txt['tp-recentexboard'].'</label>
						</dd>
						<dt><label for="tp_block_boards">'.$txt['tp-recentboards'].'</label></dt>
						<dd>
							<input type="text" id="tp_block_boards" name="tp_block_set_boards" value="' , $context['TPortal']['blockedit']['boards'] ,'" size="20" pattern="[0-9,]+">
						</dd>
						<dt>
							<label for="tp-minmessagetopics">'.$txt['tp-minmessagetopics'].'</label>
						</dt>
						<dd>
							<input type="number" id="tp-minmessagetopics" name="tp_block_set_minmessagetopics" value="' ,(empty($context['TPortal']['blockedit']['minmessagetopics']) ? '350' : $context['TPortal']['blockedit']['minmessagetopics']), '" style="width: 6em" min="1" max="1000000" required><br>
						</dd>
					</dl>';
	if($modSettings['allow_guestAccess'] == '0') {
		echo '
			<a href="', $scripturl, '?action=helpadmin;help=tp-noguest_accessdesc" onclick="return reqOverlayDiv(this.href);">
			<span class="tptooltip" title="', $txt['help'], '"></span></a>
			<span style="color: red;">' .$txt['tp-noguest_access'] .'</span>';
	}
			}
// Block type: SSI functions
			elseif($context['TPortal']['blockedit']['type']=='13'){
				if(!in_array($context['TPortal']['blockedit']['body'],array('recenttopics','recentposts','recentpoll','recentattachments','topboards','topreplies','topviews','toppoll','topposters','latestmember','randommember','online','welcome','calendar','birthday','holiday','event','recentevent','boardstats','news','boardnews','quicksearch')))
					$context['TPortal']['blockedit']['body']='';
						echo '
						',$txt['tp-ssiblockdesc'],'<a href="'.$boardurl.'/ssi_examples.php" target="_blank">ssi_examples.php</a><br><br>
						<dl class="tp_title settings">
						<dt>'.$txt['tp-showssibox'].'</dt>
						<dd>
						<div class="tp_largelist">
							<input type="radio" id="tp_block_body0" name="tp_block_body" value="" ' , $context['TPortal']['blockedit']['body']=='' ? 'checked' : '' , ' required><label for="tp_block_body0"> ' .$txt['tp-none-']. '</label><br>
							<input type="radio" id="tp_block_body1" name="tp_block_body" value="recenttopics" ' , $context['TPortal']['blockedit']['body']=='recenttopics' ? 'checked' : '' , '><label for="tp_block_body1"> '.$txt['tp-ssi-recenttopics']. '</label><br>
							<input type="radio" id="tp_block_body2" name="tp_block_body" value="recentposts" ' , $context['TPortal']['blockedit']['body']=='recentposts' ? 'checked' : '' , '><label for="tp_block_body2"> '.$txt['tp-ssi-recentposts']. '</label><br>
							<input type="radio" id="tp_block_body3" name="tp_block_body" value="recentpoll" ' , $context['TPortal']['blockedit']['body']=='recentpoll' ? 'checked' : '' , '><label for="tp_block_body3"> '.$txt['tp-ssi-recentpoll']. '</label><br>
							<input type="radio" id="tp_block_body4" name="tp_block_body" value="recentattachments" ' , $context['TPortal']['blockedit']['body']=='recentattachments' ? 'checked' : '' , '><label for="tp_block_body4"> '.$txt['tp-ssi-recentattachments']. '</label><br>
							<input type="radio" id="tp_block_body5" name="tp_block_body" value="topboards" ' , $context['TPortal']['blockedit']['body']=='topboards' ? 'checked' : '' , '><label for="tp_block_body5"> '.$txt['tp-ssi-topboards']. '</label><br>
							<input type="radio" id="tp_block_body6" name="tp_block_body" value="topreplies" ' , $context['TPortal']['blockedit']['body']=='topreplies' ? 'checked' : '' , '><label for="tp_block_body6"> '.$txt['tp-ssi-topreplies']. '</label><br>
							<input type="radio" id="tp_block_body7" name="tp_block_body" value="topviews" ' , $context['TPortal']['blockedit']['body']=='topviews' ? 'checked' : '' , '><label for="tp_block_body7"> '.$txt['tp-ssi-topviews']. '</label><br>
							<input type="radio" id="tp_block_body8" name="tp_block_body" value="toppoll" ' , $context['TPortal']['blockedit']['body']=='toppoll' ? 'checked' : '' , '><label for="tp_block_body8"> '.$txt['tp-ssi-toppoll']. '</label><br>
							<input type="radio" id="tp_block_body9" name="tp_block_body" value="topposters" ' , $context['TPortal']['blockedit']['body']=='topposters' ? 'checked' : '' , '><label for="tp_block_body9"> '.$txt['tp-ssi-topposters']. '</label><br>
							<input type="radio" id="tp_block_body10" name="tp_block_body" value="latestmember" ' , $context['TPortal']['blockedit']['body']=='latestmember' ? 'checked' : '' , '><label for="tp_block_body10"> '.$txt['tp-ssi-latestmember']. '</label><br>
							<input type="radio" id="tp_block_body11" name="tp_block_body" value="randommember" ' , $context['TPortal']['blockedit']['body']=='randommember' ? 'checked' : '' , '><label for="tp_block_body11"> '.$txt['tp-ssi-randommember']. '</label><br>
							<input type="radio" id="tp_block_body12" name="tp_block_body" value="online" ' , $context['TPortal']['blockedit']['body']=='online' ? 'checked' : '' , '><label for="tp_block_body12"> '.$txt['tp-ssi-online']. '</label><br>
							<input type="radio" id="tp_block_body13" name="tp_block_body" value="welcome" ' , $context['TPortal']['blockedit']['body']=='welcome' ? 'checked' : '' , '><label for="tp_block_body13"> '.$txt['tp-ssi-welcome']. '</label><br>
							<input type="radio" id="tp_block_body14" name="tp_block_body" value="calendar" ' , $context['TPortal']['blockedit']['body']=='calendar' ? 'checked' : '' , '><label for="tp_block_body14"> '.$txt['tp-ssi-calendar']. '</label><br>
							<input type="radio" id="tp_block_body15" name="tp_block_body" value="birthday" ' , $context['TPortal']['blockedit']['body']=='birthday' ? 'checked' : '' , '><label for="tp_block_body15"> '.$txt['tp-ssi-birthday']. '</label><br>
							<input type="radio" id="tp_block_body16" name="tp_block_body" value="holiday" ' , $context['TPortal']['blockedit']['body']=='holiday' ? 'checked' : '' , '><label for="tp_block_body16"> '.$txt['tp-ssi-holiday']. '</label><br>
							<input type="radio" id="tp_block_body17" name="tp_block_body" value="event" ' , $context['TPortal']['blockedit']['body']=='event' ? 'checked' : '' , '><label for="tp_block_body17"> '.$txt['tp-ssi-event']. '</label><br>
							<input type="radio" id="tp_block_body18" name="tp_block_body" value="recentevents" ' , $context['TPortal']['blockedit']['body']=='recentevents' ? 'checked' : '' , '><label for="tp_block_body18"> '.$txt['tp-ssi-recentevents']. '</label><br>
							<input type="radio" id="tp_block_body19" name="tp_block_body" value="boardstats" ' , $context['TPortal']['blockedit']['body']=='boardstats' ? 'checked' : '' , '><label for="tp_block_body19"> '.$txt['tp-ssi-boardstats']. '</label><br>
							<input type="radio" id="tp_block_body20" name="tp_block_body" value="news" ' , $context['TPortal']['blockedit']['body']=='news' ? 'checked' : '' , '><label for="tp_block_body20"> '.$txt['tp-ssi-news']. '</label><br>
							<input type="radio" id="tp_block_body21" name="tp_block_body" value="boardnews" ' , $context['TPortal']['blockedit']['body']=='boardnews' ? 'checked' : '' , '><label for="tp_block_body21"> '.$txt['tp-ssi-boardnews']. '</label><br>
							<input type="radio" id="tp_block_body22" name="tp_block_body" value="quicksearch" ' , $context['TPortal']['blockedit']['body']=='quicksearch' ? 'checked' : '' , '><label for="tp_block_body22"> '.$txt['tp-ssi-quicksearch']. '</label><br>
						</div>
						</dd>
					</dl>';
			}
// Block type: TP shoutbox
			elseif($context['TPortal']['blockedit']['type']=='8'){
                if(isset($context['TPortal']['tpblocks']['blockrender'])) {
					echo '
						<dl class="settings">
						<input type="hidden" name="tp_block_body" value="1">
						<dt>
							<label for="tp_shoutbox_stitle">'.$txt['tp-shoutboxtitle'].'</label>
						</dt>
						<dd>
							<textarea style="width: 90%; height: 50px;" id="tp_shoutbox_stitle" name="tp_block_body">' , !empty($context['TPortal']['blockedit']['body']) ? $context['TPortal']['blockedit']['body'] : '', '</textarea><br>
						</dd>
						<dt>
							<a href="', $scripturl, '?action=helpadmin;help=tp-shoutbox_iddesc" onclick="return reqOverlayDiv(this.href);">
							<span class="tptooltip" title="', $txt['help'], '"></span></a><label for="tp-shoutbox_id">' .$txt['tp-shoutbox_id']. '</label>
						</dt>
						<dd>
							<input type="number" id="tp-shoutbox_id" name="tp_block_set_shoutbox_id" value="' , (empty($context['TPortal']['blockedit']['shoutbox_id']) ? '1': $context['TPortal']['blockedit']['shoutbox_id']) ,'" style="width: 6em" min="1" max="9" step="1" required>
						</dd>
						<dt>
							<label for="tp-shoutboxheight">'.$txt['tp-shoutboxheight'].'</label>
						</dt>
						<dd>
							<input type="number" id="tp-shoutboxheight" name="tp_block_set_shoutbox_height" value="' ,(empty($context['TPortal']['blockedit']['shoutbox_height']) ? '250' : $context['TPortal']['blockedit']['shoutbox_height']), '" style="width: 6em" required><br>
						</dd>
						<dt>
							<label for="fieldname">'.$txt['shoutbox_layout'].'</label>
						</dt>
						<dd>
							<div class="float-items"><div><input type="radio" name="tp_block_set_shoutbox_layout" id="shout_layout1" value="0" ' , $context['TPortal']['blockedit']['shoutbox_layout'] == '0' ? ' checked="checked"' : '' , ' required></div><div><label for="shout_layout1"><img src="' . $settings['tp_images_url'] . '/shout_layout1.png" alt="Layout 1" style="text-align: right"></label></div></div>
							<div class="float-items"><div><input type="radio" name="tp_block_set_shoutbox_layout" id="shout_layout2" value="1" ' , $context['TPortal']['blockedit']['shoutbox_layout'] == '1' ? ' checked="checked"' : '' , '></div><div><label for="shout_layout2"><img src="' . $settings['tp_images_url'] . '/shout_layout2.png" alt="Layout 2"></label></div></div>
							<p class="clearthefloat"></p>
							<div class="float-items"><div><input type="radio" name="tp_block_set_shoutbox_layout" id="shout_layout3" value="2" ' , $context['TPortal']['blockedit']['shoutbox_layout'] == '2' ? ' checked="checked"' : '' , '></div><div><label for="shout_layout3"><img src="' . $settings['tp_images_url'] . '/shout_layout3.png" alt="Layout 3"></label></div></div>
							<div class="float-items"><div><input type="radio" name="tp_block_set_shoutbox_layout" id="shout_layout4" value="3" ' , $context['TPortal']['blockedit']['shoutbox_layout'] == '3' ? ' checked="checked"' : '' , '></div><div><label for="shout_layout4"><img src="' . $settings['tp_images_url'] . '/shout_layout4.png" alt="Layout 4"></label></div></div>
							<p class="clearthefloat"></p>
						</dd>
						<dt>
							'.$txt['tp-rssblock-showavatar'].'
						</dt>
						<dd>
							<input type="radio" id="tp_block_shoutbox_avataryes" name="tp_block_set_shoutbox_avatar" value="1" ' , $context['TPortal']['blockedit']['shoutbox_avatar'] == '1' ? ' checked="checked"' : '' ,' required><label for="tp_block_shoutbox_avataryes">'.$txt['tp-yes'].'</label>
							<input type="radio" id="tp_block_shoutbox_avatarno" name="tp_block_set_shoutbox_avatar" value="0" ' , $context['TPortal']['blockedit']['shoutbox_avatar'] == '0' ? ' checked="checked"' : '' ,'><label for="tp_block_shoutbox_avatarno">'.$txt['tp-no'].'</label>
						</dd>
						<dt>
							'.$txt['tp-shoutbox_direction'].'
						</dt>
						<dd>
							<input type="radio" id="tp_shoutbox_directiontop" name="tp_block_set_shoutbox_direction" value="0" ' , $context['TPortal']['blockedit']['shoutbox_direction'] == '0' ? ' checked="checked"' : '' ,'><label for="tp_shoutbox_directiontop">'.$txt['tp-shoutbox_directiontop'].'</label>
							<input type="radio" id="tp_shoutbox_directionbottom" name="tp_block_set_shoutbox_direction" value="1" ' , $context['TPortal']['blockedit']['shoutbox_direction'] == '1' ? ' checked="checked"' : '' ,' required><label for="tp_shoutbox_directionbottom">'.$txt['tp-shoutbox_directionbottom'].'</label>
						</dd>
						<dt>
							'.$txt['tp-shoutbox_barposition'].'
						</dt>
						<dd>
							<input type="radio" id="tp_shoutbox_barpositiontop" name="tp_block_set_shoutbox_barposition" value="0" ' , $context['TPortal']['blockedit']['shoutbox_barposition'] == '0' ? ' checked="checked"' : '' ,'><label for="tp_shoutbox_barpositiontop">'.$txt['tp-shoutbox_barpositiontop'].'</label>
							<input type="radio" id="tp_shoutbox_barpositionbottom" name="tp_block_set_shoutbox_barposition" value="1" ' , $context['TPortal']['blockedit']['shoutbox_barposition'] == '1' ? ' checked="checked"' : '' ,' required><label for="tp_shoutbox_barpositionbottom">'.$txt['tp-shoutbox_barpositionbottom'].'</label>
						</dd>
					</dl>';
                }
			}
// Block type: Article / Download functions
			elseif($context['TPortal']['blockedit']['type']=='14'){
				// Module block...choose module and module ID , check if module is active
						echo '
						<dl class="tp_title settings">
						<dt><label for="fieldname">'.$txt['tp-showstatsbox'].'</label></dt>
						<dd>
							<input type="radio" id="tp_block_body0" name="tp_block_body" value="" ' , $context['TPortal']['blockedit']['body']=='' ? 'checked' : '' , ' required><label for="tp_block_body0"> ' .$txt['tp-none-']. '</label><br>
							<input type="radio" id="tp_block_body1" name="tp_block_body" value="dl-stats" ' , $context['TPortal']['blockedit']['body']=='dl-stats' ? 'checked' : '' , '><label for="tp_block_body1"> '.$txt['tp-module1'].'</label><br>
							<input type="radio" id="tp_block_body2" name="tp_block_body" value="dl-stats2" ' , $context['TPortal']['blockedit']['body']=='dl-stats2' ? 'checked' : '' , '><label for="tp_block_body2"> '.$txt['tp-module2'].'</label><br>
							<input type="radio" id="tp_block_body3" name="tp_block_body" value="dl-stats3" ' , $context['TPortal']['blockedit']['body']=='dl-stats3' ? 'checked' : '' , '><label for="tp_block_body3"> '.$txt['tp-module3'].'</label><br>
							<input type="radio" id="tp_block_body4" name="tp_block_body" value="dl-stats4" ' , $context['TPortal']['blockedit']['body']=='dl-stats4' ? 'checked' : '' , '><label for="tp_block_body4"> '.$txt['tp-module4'].'</label><br>
							<input type="radio" id="tp_block_body5" name="tp_block_body" value="dl-stats5" ' , $context['TPortal']['blockedit']['body']=='dl-stats5' ? 'checked' : '' , '><label for="tp_block_body5"> '.$txt['tp-module5'].'</label><br>
							<input type="radio" id="tp_block_body6" name="tp_block_body" value="dl-stats6" ' , $context['TPortal']['blockedit']['body']=='dl-stats6' ? 'checked' : '' , '><label for="tp_block_body6"> '.$txt['tp-module6'].'</label><br>
							<input type="radio" id="tp_block_body7" name="tp_block_body" value="dl-stats7" ' , $context['TPortal']['blockedit']['body']=='dl-stats7' ? 'checked' : '' , '><label for="tp_block_body7"> '.$txt['tp-module7'].'</label><br>
							<input type="radio" id="tp_block_body8" name="tp_block_body" value="dl-stats8" ' , $context['TPortal']['blockedit']['body']=='dl-stats8' ? 'checked' : '' , '><label for="tp_block_body8"> '.$txt['tp-module8'].'</label><br>
							<input type="radio" id="tp_block_body9" name="tp_block_body" value="dl-stats9" ' , $context['TPortal']['blockedit']['body']=='dl-stats9' ? 'checked' : '' , '><label for="tp_block_body9"> '.$txt['tp-module9'].'</label><br>
						</dd>
					</dl>';
			}
// Block type: Stats
			elseif($context['TPortal']['blockedit']['type']=='3'){
				echo '
					<dl class="tp_title settings">
						<dt><label for="fieldname">'.$txt['tp-showuserbox'].'</label></dt>';
				if(isset($context['TPortal']['userbox']['avatar']) && $context['TPortal']['userbox']['avatar'])
					echo '<input type="hidden" name="tp_userbox_options0" value="avatar">';
				if(isset($context['TPortal']['userbox']['logged']) && $context['TPortal']['userbox']['logged'])
					echo '<input type="hidden" name="tp_userbox_options1" value="logged">';
				if(isset($context['TPortal']['userbox']['time']) && $context['TPortal']['userbox']['time'])
					echo '<input type="hidden" name="tp_userbox_options2" value="time">';
				if(isset($context['TPortal']['userbox']['unread']) && $context['TPortal']['userbox']['unread'])
					echo '<input type="hidden" name="tp_userbox_options3" value="unread">';
				echo '	<dd>
							<input type="checkbox" id="tp_userbox_options4" name="tp_userbox_options4" value="stats" ', (isset($context['TPortal']['userbox']['stats']) && $context['TPortal']['userbox']['stats']) ? 'checked' : '' , '><label for="tp_userbox_options4"> '.$txt['tp-userbox5'].'</label><br>
							<input type="checkbox" id="tp_userbox_options5" name="tp_userbox_options5" value="online" ', (isset($context['TPortal']['userbox']['online']) && $context['TPortal']['userbox']['online']) ? 'checked' : '' , '><label for="tp_userbox_options5"> '.$txt['tp-userbox6'].'</label><br>
							<input type="checkbox" id="tp_userbox_options6" name="tp_userbox_options6" value="stats_all" ', (isset($context['TPortal']['userbox']['stats_all']) && $context['TPortal']['userbox']['stats_all']) ? 'checked' : '' , '><label for="tp_userbox_options6"> '.$txt['tp-userbox7'].'</label>
						</dd>
					</dl>';
			}
// Block type: User
			elseif($context['TPortal']['blockedit']['type']=='1'){
				echo '
					<dl class="tp_title settings">
						<dt><label for="fieldname">'. $txt['tp-showuserbox2'].'</label></dt>
						<dd>
							<input type="checkbox" id="tp_userbox_options0" name="tp_userbox_options0" value="avatar" ', (isset($context['TPortal']['userbox']['avatar']) && $context['TPortal']['userbox']['avatar']) ? 'checked' : '' , '><label for="tp_userbox_options0"> '.$txt['tp-userbox1'].'</label><br>
							<input type="checkbox" id="tp_userbox_options1" name="tp_userbox_options1" value="logged" ', (isset($context['TPortal']['userbox']['logged']) && $context['TPortal']['userbox']['logged']) ? 'checked' : '' , '><label for="tp_userbox_options1"> '.$txt['tp-userbox2'].'</label><br>
							<input type="checkbox" id="tp_userbox_options2" name="tp_userbox_options2" value="time" ', (isset($context['TPortal']['userbox']['time']) && $context['TPortal']['userbox']['time']) ? 'checked' : '' , '><label for="tp_userbox_options2"> '.$txt['tp-userbox3'].'</label><br>
							<input type="checkbox" id="tp_userbox_options3" name="tp_userbox_options3" value="unread" ', (isset($context['TPortal']['userbox']['unread']) && $context['TPortal']['userbox']['unread']) ? 'checked' : '' , '><label for="tp_userbox_options3"> '.$txt['tp-userbox4'].'</label><br>
						</dd>
					</dl>';
				if(isset($context['TPortal']['userbox']['stats']) && $context['TPortal']['userbox']['stats'])
					echo '<input type="hidden" name="tp_userbox_options4" value="stats">';
				if(isset($context['TPortal']['userbox']['online']) && $context['TPortal']['userbox']['online'])
					echo '<input type="hidden" name="tp_userbox_options5" value="online">';
				if(isset($context['TPortal']['userbox']['stats_all']) && $context['TPortal']['userbox']['stats_all'])
					echo '<input type="hidden" name="tp_userbox_options6" value="stats_all">';
			}
// Block type: RSS
			elseif($context['TPortal']['blockedit']['type']=='15'){
				echo '
					<dl class="tp_title settings">
						<dt><label for="tp_block_body">' .	$txt['tp-rssblock'] . '</label></dt>
						<dd>
							<input name="tp_block_body" id="tp_block_body" value="' .$context['TPortal']['blockedit']['body']. '" style="width: 95%" required>
						</dd>
						<dt>'.$txt['tp-rssblock-useutf8'].'</dt>
						<dd>
							<input type="radio" id="tp_block_utf" name="tp_block_set_utf" value="1" ' , $context['TPortal']['blockedit']['utf']=='1' ? ' checked' : '' ,' required><label for="tp_block_utf">'.$txt['tp-utf8'].'</label><br>
							<input type="radio" id="tp_block_iso" name="tp_block_set_utf" value="0" ' , $context['TPortal']['blockedit']['utf']<>'1' ? ' checked' : '' ,'><label for="tp_block_iso">'.$txt['tp-iso'].'</label>
						</dd>
						<dt>'.$txt['tp-rssblock-showonlytitle'].'</dt>
						<dd>
							<input type="radio" id="tp_block_showtitleyes" name="tp_block_set_showtitle" value="1" ' , $context['TPortal']['blockedit']['showtitle'] == '1' ? ' checked' : '' ,' required><label for="tp_block_showtitleyes">'.$txt['tp-yes'].'</label>
							<input type="radio" id="tp_block_showtitleno" name="tp_block_set_showtitle" value="0" ' , $context['TPortal']['blockedit']['showtitle'] <> '1' ? ' checked' : '' ,'><label for="tp_block_showtitleno">'.$txt['tp-no'], '</label>
						</dd>
						<dt><label for="tp_block_maxwidth">' . $txt['tp-rssblock-maxwidth'].'</label></dt>
						<dd>
							<input id="tp_block_maxwidth" name="tp_block_set_maxwidth" value="' , $context['TPortal']['blockedit']['maxwidth'],'" style="width: 6em">
						</dd>
						<dt><label for="tp_block_maxshown">' . $txt['tp-rssblock-maxshown'].'</label></dt>
						<dd>
							<input type="number" id="tp_block_maxshown" name="tp_block_set_maxshown" value="' , $context['TPortal']['blockedit']['maxshown'],'" style="width: 6em">
						</dd>
					</dl>';
			}
// Block type: Sitemap
			elseif($context['TPortal']['blockedit']['type']=='16'){
				echo '
					<dl class="tp_title settings">
						<dt>' . $txt['tp-sitemapdesc'] . '</dt>
						<dd></dd>'; 
				if($context['TPortal']['show_download']=='1') {
					echo '
						<dt>' . $txt['tp-sitemapmodules'] . '<ul class="disc"></dt>
						<dd><li>' . $txt['tp-dldownloads'] . '</li></ul></dd>';
					}
					echo '
					</dl>';
			}
// Block type: Single Article
			elseif($context['TPortal']['blockedit']['type']=='18'){
				// check to see if it is numeric
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$lblock['body']='';
				echo '
					<dl class="tp_title settings">
						<dt><label for="fieldname">',$txt['tp-showarticle'],'</label></dt>
						<dd>
							<select name="tp_block_body">
							<option value="0">'.$txt['tp-none2'].'</option>';
				foreach($context['TPortal']['edit_articles'] as $art => $article ){
					echo '<option value="'.$article['id'].'" ' , $context['TPortal']['blockedit']['body']==$article['id'] ? ' selected="selected"' : '' ,' >'.html_entity_decode($article['subject']).'</option>';
				}
				echo '</select>
						</dd>
					</dl>';
			}
// Block type: Themes
			elseif($context['TPortal']['blockedit']['type']=='7') {
				echo '
					<dl class="tp_title settings">
						<dt>'.$txt['tp-themesavail'].'</dt>
						<dd>';
				// get the ids
				$myt=array();
				$thems=explode(",",$context['TPortal']['blockedit']['body']);
				foreach($thems as $g => $gh)
				{
					$wh=explode("|",$gh);
					$myt[]=$wh[0];
				}
					echo '
						<input type="hidden" name="blockbody' .$context['TPortal']['blockedit']['id']. '" value="' .$context['TPortal']['blockedit']['body'] . '" />
						<div style="padding: 5px; ">
							<div style="max-height: 25em; overflow: auto;">
							<input type="hidden" name="tp_theme-1" value="-1">
							<input type="hidden" name="tp_tpath-1" value="1">';
				foreach($context['TPthemes'] as $tema)
				{
					echo '
						<img class="tp_theme_icon" alt="*" src="'.$tema['path'].'/thumbnail.png" /> <input type="checkbox" id="tp_theme'.$tema['id'].'" name="tp_theme'.$tema['id'].'" value="'.$tema['name'].'"';
					if(in_array($tema['id'],$myt))
						echo ' checked';
					echo '><label for="tp_theme'.$tema['id'].'">'.$tema['name'].'</label><input type="hidden" value="'.$tema['path'].'" name="tp_path'.$tema['id'].'"><br>';
				}
				echo '
					</div><br>
					<input type="checkbox" id="invertall" onclick="invertAll(this, this.form, \'tp_theme\');" /><label for="invertall"> '.$txt['tp-checkall'],'</label>
					</div>
					</dd>
				</dl>
				';
			}
// Block type: Articles in a Category
			elseif($context['TPortal']['blockedit']['type']=='19') {
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$lblock['body']='';
				echo '
					<dl class="tp_title settings">
						<dt><label for="tp_block_body">'.$txt['tp-showcategory'].'</label></dt>
						<dd>
							<select name="tp_block_body" id="tp_block_body" required>
							<option value="0">'.$txt['tp-none2'].'</option>';
				foreach($context['TPortal']['catnames'] as $cat => $catname){
					echo '
								<option value="'.$cat.'" ' , $context['TPortal']['blockedit']['body']==$cat ? ' selected' : '' ,' >'.html_entity_decode($catname).'</option>';
				}
				echo '
							</select>
						</dd>
						<dt><label for="tp_block_block_height">'.$txt['tp-catboxheight'].'</label></dt>
						<dd>
							<input type="number" id="tp_block_block_height" name="tp_block_set_block_height" value="' , ((!is_numeric($context['TPortal']['blockedit']['block_height'])) || (($context['TPortal']['blockedit']['block_height']) == 0) ? '15' : $context['TPortal']['blockedit']['block_height']) ,'" style="width: 6em" min="1" required> em
						</dd>
						<dt>'.$txt['tp-catboxauthor'].'</dt>
						<dd>
							<input type="radio" id="tp_block_block_authoryes" name="tp_block_set_block_author" value="1" ' , $context['TPortal']['blockedit']['block_author']=='1' ? 'checked' : '' ,' required><label for="tp_block_block_authoryes"> ', $txt['tp-yes'], '</label><br>
							<input type="radio" id="tp_block_block_authorno" name="tp_block_set_block_author" value="0" ' , $context['TPortal']['blockedit']['block_author']=='0' ? 'checked' : '' ,'><label for="tp_block_block_authorno"> ', $txt['tp-no'], '</label>
						</dd>
					</dl>';
			}
// Block type: Menu
			elseif($context['TPortal']['blockedit']['type']=='9') {
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$lblock['body']='0';
				echo '
					<dl class="tp_title settings">
						<dt><label for="fieldname">',$txt['tp-showmenus'],'</label></dt>
						<dd>
							<select name="tp_block_body">';
				foreach($context['TPortal']['menus'] as $men){
					echo '
						<option value="'.$men['id'].'" ' , $context['TPortal']['blockedit']['body']==$men['id'] ? ' selected' : '' ,' >'.$men['name'].'</option>';
				}
				echo '
					</select>
						</dd>
						<dt><label for="fieldname">',$txt['tp-showmenustyle'],'</label></dt>
						<dd>
							<input type="radio" name="tp_block_set_style" value="0" ' , ($context['TPortal']['blockedit']['style']=='' || $context['TPortal']['blockedit']['style']=='0') ? ' checked' : '' ,' required> <img src="'.$settings['tp_images_url'].'/TPdivider2.png" alt="" /><br>
							<input type="radio" name="tp_block_set_style" value="1" ' , ($context['TPortal']['blockedit']['style']=='1') ? ' checked' : '' ,' > <img src="'.$settings['tp_images_url'].'/bullet3.png" alt="" /><br>
							<input type="radio" name="tp_block_set_style" value="2" ' , ($context['TPortal']['blockedit']['style']=='2') ? ' checked' : '' ,' > '.$txt['tp-none-'].'
						</dd>
					</dl>';
			}
// Block type: Online
			elseif($context['TPortal']['blockedit']['type']=='6') {
				echo '
					<dl class="tp_title settings">
						<dt>'.$txt['tp-rssblock-showavatar'].'</dt>
						<dd>
							<input type="radio" id="tp_block_useavataryes" name="tp_block_set_useavatar" value="1" ' , !$context['TPortal']['blockedit']['useavatar']=='0' ? ' checked' : '' ,' required><label for="tp_block_useavataryes">'.$txt['tp-yes'].'</label> <input type="radio" id="tp_block_useavatarno" name="tp_block_set_useavatar" value="0" ' , $context['TPortal']['blockedit']['useavatar']=='0' ? ' checked' : '' ,'><label for="tp_block_useavatarno">'.$txt['tp-no'].'</label>
						</dd>
					</dl>';
			}
// Block type: News
			elseif($context['TPortal']['blockedit']['type']=='2'){
				echo '
					<div>' . $txt['tp-newsdesc'] . '</div>';
			}
// Block type: Search
			elseif($context['TPortal']['blockedit']['type']=='4'){
				echo '
					<div>' . $txt['tp-searchdesc'] . '</div>';
			}
			else {
				echo '
			'.$txt['tp-noblocktype'].'';
            }

			echo '
				</div>
				<div><hr>
					<div>
						<a href="', $scripturl, '?action=helpadmin;help=tp-blockstylehelpdesc" onclick="return reqOverlayDiv(this.href);">
						<span class="tptooltip" title="', $txt['help'], '"></span></a>'.$txt['tp-blockstylehelp'].'<br>
					</div>
					<br><input type="radio" id="tp_block_panelstyle" name="tp_block_set_panelstyle" value="99" ' , $context['TPortal']['blockedit']['panelstyle']=='99' ? 'checked' : '' , '><span' , $context['TPortal']['blockedit']['panelstyle']=='99' ? ' style="color: red;">' : '><label for="tp_block_panelstyle">' , $txt['tp-blocksusepaneltyle'] , '</label></span>
				<div>
				<div class="tp_panelstyles-bg">';
			$types = tp_getblockstyles21();

			foreach($types as $blo => $bl) {
				echo '
					<div class="tp_panelstyles">
						<div>
							<input type="radio" id="tp_block_panelstyle'.$blo.'" name="tp_block_set_panelstyle" value="'.$blo.'" ' , $context['TPortal']['blockedit']['panelstyle']==$blo ? 'checked' : '' , '><label for="tp_block_panelstyle'.$blo.'"><span' , $context['TPortal']['blockedit']['panelstyle']==$blo ? ' style="color: red;">' : '>' , $bl['class'] , '</span></label>
						</div>
						' . $bl['code_title_left'] . 'title'. $bl['code_title_right'].'
						' . $bl['code_top'] . 'body' . $bl['code_bottom'] . '
					</div>';
            }

			echo '
						</div>
					</div>
				</div>
				<br>
					<dl class="tp_title settings">
						<dt><label for="fieldname">'.$txt['tp-blockframehelp'].'</label></dt>
						<dd>
							<input type="radio" id="useframe" name="tp_block_frame" value="theme" ' , $context['TPortal']['blockedit']['frame']=='theme' ? 'checked' : '' , '><label for="useframe"> '.$txt['tp-useframe'].'</label><br>
							<input type="radio" id="useframe2" name="tp_block_frame" value="frame" ' , $context['TPortal']['blockedit']['frame']=='frame' ? 'checked' : '' , '><label for="useframe2"> '.$txt['tp-useframe2'].' </label><br>
							<input type="radio" id="usetitle" name="tp_block_frame" value="title" ' , $context['TPortal']['blockedit']['frame']=='title' ? 'checked' : '' , '><label for="usetitle"> '.$txt['tp-usetitle'].' </label></br>
							<input type="radio" id="noframe" name="tp_block_frame" value="none" ' , $context['TPortal']['blockedit']['frame']=='none' ? 'checked' : '' , '><label for="noframe"> '.$txt['tp-noframe'].'</label><br><br>
						</dd>
						<dt><label for="fieldname"> '.$txt['tp-allowupshrink'].' </label></dt>
						<dd>
							<input type="radio" id="allowupshrink" name="tp_block_visible" value="1" ' , ($context['TPortal']['blockedit']['visible']=='' || $context['TPortal']['blockedit']['visible']=='1') ? 'checked' : '' , '><label for="allowupshrink"> '.$txt['tp-allowupshrink'].'</label><br>
							<input type="radio" id="notallowupshrink" name="tp_block_visible" value="0" ' , ($context['TPortal']['blockedit']['visible']=='0') ? 'checked' : '' , '><label for="notallowupshrink"> '.$txt['tp-notallowupshrink'].'</label><br><br>
						</dd>
						<dt>
							<a href="', $scripturl, '?action=helpadmin;help=tp-membergrouphelpdesc" onclick="return reqOverlayDiv(this.href);">
							<span class="tptooltip" title="', $txt['help'], '"></span></a><label for="fieldname">'.$txt['tp-membergrouphelp'].'</label></dt>
						<dd><div>
							  <div class="tp_largelist">';
			// loop through and set membergroups
			$tg=explode(',',$context['TPortal']['blockedit']['access']);
			if( !empty($context['TPmembergroups'])) {
				foreach($context['TPmembergroups'] as $mg) {
					if($mg['posts']=='-1' && $mg['id']!='1'){
						echo '<input type="checkbox" id="tp_group'.$mg['id'].'" name="tp_group'.$mg['id'].'" value="'.$context['TPortal']['blockedit']['id'].'"';
						if(in_array($mg['id'],$tg)) {
							echo ' checked';
                        }
						echo '><label for="tp_group'.$mg['id'].'"> '.$mg['name'].'</label><br>';
					}
				}
			}
			// if none is chosen, have a control value
			echo '
							</div>
								<input type="checkbox" id="checkallmg" onclick="invertAll(this, this.form, \'tp_group\');" /><label for="checkallmg">'.$txt['tp-checkall'].'</label><br><br>
							</div>
						</dd>
						<dt>
							<a href="', $scripturl, '?action=helpadmin;help=tp-langhelpdesc" onclick="return reqOverlayDiv(this.href);">
							<span class="tptooltip" title="', $txt['help'], '"></span></a><label for="field_name">'.$txt['tp-langhelp'].'</label></dt>
						<dd>';
			foreach($context['TPortal']['langfiles'] as $langlist => $lang) {
				if($lang!=$context['user']['language'] && $lang!='')
					echo '
						<dt>'. $lang.'</dt>
						<dd>
							<input type="text" name="tp_lang_'.$lang.'" value="' , !empty($context['TPortal']['blockedit']['langfiles'][$lang]) ? html_entity_decode($context['TPortal']['blockedit']['langfiles'][$lang], ENT_QUOTES) : html_entity_decode($context['TPortal']['blockedit']['title'],ENT_QUOTES) , '" size="50">
						</dd>';
			}
			echo '
						<dt>
							<a href="', $scripturl, '?action=helpadmin;help=tp-langdesc" onclick="return reqOverlayDiv(this.href);">
							<span class="tptooltip" title="', $txt['help'], '"></span></a><label for="fieldname">' . $txt['tp-lang'] . '</label>';
				// alert if the settings is off, supply link if allowed
				if(empty($context['TPortal']['uselangoption'])) {
					echo '
					<div class="noticebox">', $txt['tp-uselangoption2'] , ' ' , allowedTo('tp_settings') ? '<a href="'.$scripturl.'?action=tpadmin;sa=settings#uselangoption">&nbsp;['. $txt['tp-settings'] .']&nbsp;</a>' : '' , '</div>';
				}
				echo '
					</dt>
					<dd>';
				$a=1;
				foreach($context['TPortal']['langfiles'] as $bb => $lang) {
					echo '
							<input type="checkbox" id="langtype' . $a . '" name="langtype' . $a . '" value="'.$lang.'" ' , in_array($lang, $context['TPortal']['blockedit']['display']['lang']) ? 'checked="checked"' : '' , '><label for="langtype' . $a . '"> '.$lang.'</label><br>';
					$a++;
				}
				echo ' </dd>
					</dl>
				</div>';
		if($context['TPortal']['blockedit']['bar']!=4) {
			// extended visible options
				echo '
					<div class="tp_admintable">
						<div>'.$txt['tp-displayhelp'].'</div>
						<div id="collapse-options">
						', tp_hidepanel('blockopts', true) , '
				' , empty($context['TPortal']['blockedit']['display2']) ? '<div class="errorbox">' . $txt['tp-noaccess'] . '</div>' : '' , '
						<fieldset class="tborder" id="blockopts" ' , in_array('blockopts',$context['tp_panels']) ? ' style="display: none;"' : '' , '>
						<input type="hidden" name="TPadmin_blocks_vo" value="'.$mg['id'].'" />';
				if(!empty($context['TPortal']['return_url']))
					echo '
							<input type="hidden" name="fromblockpost" value="'.$context['TPortal']['return_url'].'" />';
					echo '
					<dl class="tp_title settings">
						<dt><b>' . $txt['tp-actions'] . '</b></dt>
						<dd>
							<div>
								<input name="actiontype1" id="actiontype1" type="checkbox" value="allpages" ' ,in_array('allpages',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype1"> '.$txt['tp-allpages'].'</label><br><br>
								<input name="actiontype2" id="actiontype2" type="checkbox" value="frontpage" ' ,in_array('frontpage',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype2"> '.$txt['tp-frontpage'].'</label><br>
								<input name="actiontype3" id="actiontype3" type="checkbox" value="forumall" ' ,in_array('forumall',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype3"> '.$txt['tp-forumall'].'</label><br>
								<input name="actiontype4" id="actiontype4" type="checkbox" value="forum" ' ,in_array('forum',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype4"> '.$txt['tp-forumfront'].'</label><br>
								<input name="actiontype5" id="actiontype5" type="checkbox" value="recent" ' ,in_array('recent',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype5"> '.$txt['tp-recent'].'</label><br>
								<input name="actiontype6" id="actiontype6" type="checkbox" value="unread" ' ,in_array('unread',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype6"> '.$txt['tp-unread'].'</label><br>
								<input name="actiontype7" id="actiontype7" type="checkbox" value="unreadreplies" ' ,in_array('unreadreplies',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype7"> '.$txt['tp-unreadreplies'].'</label><br>
								<input name="actiontype8" id="actiontype8" type="checkbox" value="profile" ' ,in_array('profile',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype8"> '.$txt['profile'].'</label><br>
								<input name="actiontype9" id="actiontype9" type="checkbox" value="pm" ' ,in_array('pm',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype9"> '.$txt['pm_short'].'</label><br>
								<input name="actiontype10" id="actiontype10" type="checkbox" value="calendar" ' ,in_array('calendar',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype10"> '.$txt['calendar'].'</label><br>
								<input name="actiontype11" id="actiontype11" type="checkbox" value="admin" ' ,in_array('admin',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype11"> '.$txt['admin'].'</label><br>
								<input name="actiontype12" id="actiontype12" type="checkbox" value="login" ' ,in_array('login',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype12"> '.$txt['login'].'</label><br>
								<input name="actiontype13" id="actiontype13" type="checkbox" value="logout" ' ,in_array('logout',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype13"> '.$txt['logout'].'</label><br>
								<input name="actiontype14" id="actiontype14" type="checkbox" value="register" ' ,in_array('register',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype14"> '.$txt['register'].'</label><br>
								<input name="actiontype15" id="actiontype15" type="checkbox" value="post" ' ,in_array('post',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype15"> '.$txt['post'].'</label><br>
								<input name="actiontype16" id="actiontype16" type="checkbox" value="stats" ' ,in_array('stats',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype16"> '.$txt['tp-stats'].'</label><br>
								<input name="actiontype17" id="actiontype17" type="checkbox" value="search" ' ,in_array('search',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype17"> '.$txt['search'].'</label><br>
								<input name="actiontype18" id="actiontype18" type="checkbox" value="mlist" ' ,in_array('mlist',$context['TPortal']['blockedit']['display']['action']) ? 'checked="checked"' : '' , '><label for="actiontype18"> '.$txt['tp-memberlist'].'</label><br><br>';
					// add the custom ones you added
					$count=19;
					foreach($context['TPortal']['blockedit']['display']['action'] as $po => $p) {
						if($p !== '') {
							if(!in_array($p, array('allpages','frontpage','forumall','forum','recent','unread','unreadreplies','profile','pm','calendar','admin','login','logout','register','post','stats','search','mlist')))
							{
								echo '<input type="checkbox" id="actiontype'.$count.'" name="actiontype'.$count.'" value="'.$p.'" checked="checked"><label for="name="actiontype'.$count.'">'.$p.'</label><br>';
								$count++;
							}
						}
					}
					echo '
							<p><label for="custotype0">'.$txt['tp-customactions'].'</label></p>
								<input type="text"id="custotype0" name="custotype0"  value="" style="width: 90%;">
								</div>
							</dd>
					</dl>
					<dl class="tp_title settings">
						<dt><b>' . $txt['tp-boards'] . '</b></dt>
						<dd>
							<div class="tp_largelist">';
				$a=1;
				if(!empty($context['TPortal']['boards']))
				{
					echo '<input type="checkbox" name="boardtype' , $a, '" value="-1" id="allboards" ' , in_array('-1', $context['TPortal']['blockedit']['display']['board']) ? 'checked="checked"' : '' , '><label for="allboards"> '.$txt['tp-allboards'].'</label><br><br>';
					$a++;
					foreach($context['TPortal']['boards'] as $bb)
					{
						echo '
								<input type="checkbox" name="boardtype' , $a, '" id="boardtype' , $a, '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['display']['board']) ? 'checked="checked"' : '' , '><label for="boardtype' , $a, '"> '.$bb['name'].'</label><br>';
						$a++;
					}
				}
				echo '
							 </div>
						</dd>
					</dl>
					<dl class="tp_title settings">
						<dt><b>' . $txt['tp-articles'] . '</b></dt>
						<dd>
							 <div class="tp_largelist">';
				$a=1;
				foreach($context['TPortal']['edit_articles'] as $bb)
				{
					echo '
								<input type="checkbox" id="articletype' , $a , '" name="articletype' , $a , '" value="'.$bb['id'].'" ' ,in_array($bb['id'], $context['TPortal']['blockedit']['display']['page']) ? 'checked="checked"' : '' , '><label for="articletype' , $a , '"> '.html_entity_decode($bb['subject'],ENT_QUOTES).'</label><br>';
					$a++;
				}
				// if none is chosen, have a control value
				echo '</div><input type="checkbox" id="togglearticle" onclick="invertAll(this, this.form, \'articletype\');" /><label for="togglearticle">'.$txt['tp-checkall'];
				echo '</label><br>
						</dd>
					</dl>
					<dl class="tp_title settings">
						<dt><b>' . $txt['tp-artcat'] . '</b></dt>
						<dd>
						    <div class="tp_largelist">';
				$a=1;
				if(isset($context['TPortal']['article_categories']))
				{
					foreach($context['TPortal']['article_categories'] as $bb)
					{
						echo '
								<input type="checkbox" id="categorytype' . $a . '" name="categorytype' . $a . '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['display']['cat']) ? 'checked="checked"' : '' , '><label for="categorytype' . $a . '"> '.$bb['name'].'</label><br>';
						$a++;
					}
				}
				// if none is chosen, have a control value
				echo '</div><input type="checkbox" id="togglecat" onclick="invertAll(this, this.form, \'categorytype\');" /><label for="togglecat">'.$txt['tp-checkall'];
				echo '</label><br>
						</dd>
					</dl>
					<dl class="tp_title settings">
						<dt><b>' . $txt['tp-dlmanager'] . '</b></dt>
						<dd>
							<div class="tp_largelist">';
				$a=1;
				if(!empty($context['TPortal']['dlcats']))
				{
					$a++;
					foreach($context['TPortal']['dlcats'] as $bb)
					{
						echo '
								<input type="checkbox" id="dlcattype' , $a, '" name="dlcattype' , $a, '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['display']['dlcat']) ? 'checked="checked"' : '' , '><label for="dlcattype' , $a, '"> '.$bb['name'].'</label><br>';
						$a++;
					}
				}
				// if none is chosen, have a control value
				echo '		</div><input type="checkbox" id="toggledlcat" onclick="invertAll(this, this.form, \'dlcattype\');" /><label for="toggledlcat">'.$txt['tp-checkall'];
				echo '</label<br>
						</dd>
					</dl>
				</fieldset>
				</div>
			</div>';
		}
			echo '
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}
?>
