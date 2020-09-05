<?php
/**
 * @package TinyPortal
 * @version 2.0.0
 * @author tino - http://www.tinyportal.net
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

// ** Sections **
// Submit Article
// My Articles

// Submit Article
function template_submitarticle() 
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language, $smcFunc;

	$tpmonths=array(' ','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
    if(!empty($context['TPortal']['editarticle'])) {
	    $mg = $context['TPortal']['editarticle'];
    }
    else {
        $mg = false;
    }

	if(!isset($context['TPortal']['category_name'])) {
		$context['TPortal']['category_name'] = $txt['tp-uncategorised'];
    }

    $action = 'tportal;sa=savearticle';
    if(allowedTo('admin_forum') || allowedTo('tp_articles')) {
        $action = 'tpadmin';
    }
    else if(isset($mg['id'])) {
        $action .= ';article='.$mg['id'];
    }

    if(empty($mg['articletype']) && !empty($context['TPortal']['articletype'])) {
        $article_type = $context['TPortal']['articletype'];
    }
    elseif(!empty($mg['articletype'])) {
        $article_type = $mg['articletype'];
    }
    else {
        $article_type = 'html'; // Default to HTML
    }

	echo '
	<form accept-charset="', $context['character_set'], '" name="TPadmin3" action="' . $scripturl . '?action='.$action.'" enctype="multipart/form-data" method="post" onsubmit="submitonce(this);">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />';

    if(allowedTo('admin_forum') || allowedTo('tp_articles')) {
	    echo '<input name="article" type="hidden" value="'. $mg['id'] . '">';
	    echo '<input name="tpadmin_form" type="hidden" value="editarticle">';
    }

    echo'
		<div class="cat_bar"><h3 class="catbg"><img style="margin-right: 4px;" src="' .$settings['tp_images_url']. '/TP' , $mg['off']=='1' ? 'red' : 'green' , '.png" alt=""  />' , $mg['id']=='' ? $txt['tp-addarticle']. '' .$txt['tp-incategory'] . (html_entity_decode($context['TPortal']['category_name'])) : $txt['tp-editarticle']. ' ' .html_entity_decode($mg['subject']) , '' , $mg['id']==0 ? '' : '&nbsp;-&nbsp;<a href="'.$scripturl.'?page='.$mg['id'].'">['.$txt['tp-preview'].']</a>';
	echo '</h3></div>
		<div id="edit-add-single-article" class="admintable admin-area">
		<div class="windowbg noup">
			<div class="formtable padding-div">
			<dl class="settings tptitle">
				<dt>
					<div class="font-strong"><label for="tp_article_subject">' , $txt['tp-arttitle'] , '</label></div>
				</dt>
				<dd>
					<input style="width: 92%;" name="tp_article_subject" id="tp_article_subject" type="text" value="'. html_entity_decode($mg['subject'], ENT_QUOTES, $context['character_set']) .'" required>
				</dd>
				<dt>
					<div class="font-strong"><a href="', $scripturl, '?action=helpadmin;help=',$txt['tp-shortname_articledesc'],'" onclick=' . ((!TP_SMF21) ? '"return reqWin(this.href);"' : '"return reqOverlayDiv(this.href);"') . '><span class="tptooltip" title="', $txt['help'], '"></span></a><label for="tp_article_shortname">'.$txt['tp-shortname_article'].'</label></div>
				</dt>
				<dd>
					<input size=20 name="tp_article_shortname" id="tp_article_shortname" type="text" value="'.$mg['shortname'].'">
				</dd>
			</dl>
			<div>';
				$tp_use_wysiwyg = $context['TPortal']['show_wysiwyg'];
				if($article_type == 'php') {
					echo '<textarea name="tp_article_body" id="tp_article_body" wrap="auto">' ,  $mg['body'] , '</textarea><br>';
                }
				elseif(($tp_use_wysiwyg > 0) && ($article_type == 'html')) {
					TPwysiwyg('tp_article_body', $mg['body'], true, 'qup_tp_article_body', $tp_use_wysiwyg);
                }
				elseif(($tp_use_wysiwyg == 0) && ($article_type == 'html')) {
					echo '<textarea name="tp_article_body" id="tp_article_body" wrap="auto">' , $mg['body'], '</textarea><br>';
                }
				elseif($article_type == 'bbc') {
					TP_bbcbox($context['TPortal']['editor_id']);
                }
				else {
					echo '
					<dl class="settings tptitle">
						<dt>' , $txt['tp-importarticle'] , '</dt>
						<dd>
							<input size="50" style="max-width:97%;" name="tp_article_fileimport" type="text" value="' , $mg['fileimport'] , '">
						</dd>
					</dl>' ;
                }
				echo '
			</div><br>
			<dl class="settings tptitle">
					<dt>
						<a href="', $scripturl, '?action=helpadmin;help=',$txt['tp-useintrodesc'],'" onclick=' . ((!TP_SMF21) ? '"return reqWin(this.href);"' : '"return reqOverlayDiv(this.href);"') . '><span class="tptooltip" title="', $txt['help'], '"></span></a><label for="tp_article_useintro">', $txt['tp-useintro'], '</label>
					</dt>
					<dd>
							<input name="tp_article_useintro" type="radio" value="1" ', $mg['useintro']=='1' ? 'checked' : '' ,'> '.$txt['tp-yes'].'
							<input name="tp_article_useintro" type="radio" value="0" ', !$mg['useintro']=='1' ? 'checked' : '' ,'> '.$txt['tp-no'].'<br>
					</dd>
				</dl>
					';
				if($article_type == 'php' || $article_type == 'html')	{
					echo '<div id="tp_article_show_intro"', ($mg['useintro'] == 0) ? 'style="display:none;">' : '>' , '<div class="font-strong">'.$txt['tp-introtext'].'</div>';
					if( ( $tp_use_wysiwyg > 0 ) && ( $article_type == 'html' ) ) {
						TPwysiwyg('tp_article_intro',  $mg['intro'], true, 'qup_tp_article_intro', $tp_use_wysiwyg, false);
                    }
					else {
						echo '<textarea name="tp_article_intro" id="tp_article_intro" rows=5 cols=20 wrap="soft">'.$mg['intro'].'</textarea>';
                    }
					echo '</div>';
				}
				elseif($article_type == 'bbc' || $article_type == 'import') {
					echo '<div id="tp_article_show_intro"', ($mg['useintro'] == 0) ? 'style="display:none;">' : '>' ,
                    '<div class="font-strong">'.$txt['tp-introtext'].'</div>
					<div>
						<textarea name="tp_article_intro" id="tp_article_intro" rows=5 cols=20 wrap="soft">'. $mg['intro'] .'</textarea>
					</div>
                    </div>';
				}

				echo '
					<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>';

                echo '<input name="tp_article_timestamp" type="hidden" value="'.$mg['date'].'">';
 
			if(allowedTo('admin_forum') || allowedTo('tp_articles')) {
					echo '
				<hr>
				<dl class="tptitle settings">
					<dt>
						<label for="tp_article_authorid">', $txt['tp-author'], '</label>
					</dt>
					<dd>
						<b><a href="' . $scripturl . '?action=profile;u='.$mg['author_id'].'" target="_blank">'.$mg['real_name'].'</a></b>
						&nbsp;' . $txt['tp-assignnewauthor'] . ' <input size="8" maxlength="12" name="tp_article_authorid" id="tp_article_authorid" value="' . $mg['author_id'] . '" /><br><br>
					</dd>
					<dt>
						', $txt['tp-created'], '
					</dt>
					<dd>';
				
                // day
				$day = date("j",$mg['date']);
				$month = date("n",$mg['date']);
				$year = date("Y",$mg['date']);
				$hour = date("G",$mg['date']);
                echo '<select size="1" name="tp_article_day">';
				$minute = date("i",$mg['date']);
				for($a=1; $a<32;$a++) {
					echo '<option value="'.$a.'" ' , $day==$a ? ' selected' : '' , '>'.$a.'</option>  ';
                }
				echo '</select>';

				// month
				echo '<select size="1" name="tp_article_month">';
				for($a=1; $a<13; $a++) {
					echo '<option value="'.$a.'" ' , $month==$a ? ' selected' : '' , '>'.$tpmonths[$a].'</option>  ';
                }
				echo '</select>';
				// year
				echo '<select size="1" name="tp_article_year">';
				$now    = date("Y",time())+1;
				for($a=2004; $a<$now; $a++) {
					echo '<option value="'.$a.'" ' , $year==$a ? ' selected' : '' , '>'.$a.'</option>  ';
                }
				echo '</select>';

				// hours
				echo ' - <select size="1" name="tp_article_hour">';
				for($a=0; $a<24;$a++) {
					echo '<option value="'.$a.'" ' , $hour==$a ? ' selected' : '' , '>'.$a.'</option>  ';
                }
				echo '</select>';

				// minutes
				echo ' <b>:</b><select size="1" name="tp_article_minute">';
				for($a=0; $a<60;$a++) {
					echo '<option value="'.$a.'" ' , $minute==$a ? ' selected' : '' , '>'.$a.'</option>  ';
                }
				echo '</select><br><br>
					</dd>
					<dt>
						<label for="tp_article_approved">', $txt['tp-approved'], '</label>
					</dt>
					<dd>
						<input name="tp_article_approved" id="tp_article_approved" type="radio" value="1" ', $mg['approved']=='1' ? 'checked' : '' ,'>  '.$txt['tp-yes'].'
						<input name="tp_article_approved" type="radio" value="0" ', $mg['approved']=='0' ? 'checked' : '' ,'>  '.$txt['tp-no'].'
					</dd>
				</dl>
				<hr>
				<dl class="tptitle settings">
					<dt>
						', $txt['tp-switchmode'], '
					</dt>
					<dd>
						<input name="tp_article_type" id="gohtml" type="radio" value="html"' , $article_type == 'html' ? ' checked="checked"' : '' ,'><label for="gohtml"> '.$txt['tp-gohtml'] .'</label><br>
						<input name="tp_article_type" id="gophp" type="radio" value="php"' , $article_type == 'php' ? ' checked="checked"' : '' ,'><label for="gophp"> '.$txt['tp-gophp'] .'</label><br>
						<input name="tp_article_type" id="gobbc" type="radio" value="bbc"' , $article_type == 'bbc' ? ' checked="checked"' : '' ,'><label for="gobbc"> '.$txt['tp-gobbc'] .'</label><br>
						<input name="tp_article_type" id="goimport" type="radio" value="import"' , $article_type == 'import' ? ' checked="checked"' : '' ,'><label for="goimport"> '.$txt['tp-goimport'] .'</label><br><br>
					</dd>
					<dt>
						', $txt['tp-status'], ' <img style="margin:0 1ex;" src="' .$settings['tp_images_url']. '/TP' , $mg['off']=='1' ? 'red' : 'green' , '.png" alt=""  />
					</dt>
					<dd>
						<input name="tp_article_off" id="tp_article_on" type="radio" value="0" ' , $mg['off']=='0' ? 'checked' : '' , '><label for="tp_article_on"> '.$txt['tp-articleon'].'</label><br>
						<input name="tp_article_off" id="tp_article_off" type="radio" value="1" ' , $mg['off']=='1' ? 'checked' : '' , '><label for="tp_article_off"> '.$txt['tp-articleoff'].'</label>
					</dd>
				</dl>
				<hr>
				<dl class="tptitle settings">';

				if(!empty($context['TPortal']['allcats'])) {
					echo '
				<dt>
					<label for="tp_article_category">', $txt['tp-category'], '</label>
				</dt>
				<dd>
					<div>
						<select size="1" name="tp_article_category" id="tp_article_category">
							<option value="0">'.$txt['tp-none2'].'</option>';
				foreach($context['TPortal']['allcats'] as $cats) {
					if($cats['id'] < 9999 && $cats['id'] > 0) {
						echo '<option value="'.$cats['id'].'" ', $cats['id'] == $mg['category'] ? 'selected' : '' ,'>'. str_repeat("-", isset($cats['indent']) ? $cats['indent'] : 0) .' '.$cats['name'].'</option>';
					}
				}
				echo '</select>';
				if(allowedTo('admin_forum') || allowedTo('tp_articles')) {
					echo '&nbsp;<a href="', $scripturl, '?action=tpadmin;sa=categories;cu='.$mg['category'].';sesc=' .$context['session_id']. '">',$txt['tp-editcategory'],'</a>';
				}
				echo '
						</div><br>
					</dd>
					<dt>
						<a href="', $scripturl, '?action=helpadmin;help=',$txt['tp-statusdesc'],'" onclick=' . ((!TP_SMF21) ? '"return reqWin(this.href);"' : '"return reqOverlayDiv(this.href);"') . '><span class="tptooltip" title="', $txt['help'], '"></span></a><label for="field_name">', $txt['tp-status'], '</label>
					</dt>
					<dd>';
				if (!empty($context['TPortal']['editing_article'])) {
					// show checkboxes since we have these features aren't available until the article is saved.
					echo '
						<img style="cursor: pointer;" class="toggleFront" id="artFront' .$mg['id']. '" title="'.$txt['tp-setfrontpage'].'" src="' .$settings['tp_images_url']. '/TPfront' , $mg['frontpage']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-setfrontpage'].'"  />
						<img style="cursor: pointer;" class="toggleSticky" id="artSticky' .$mg['id']. '" title="'.$txt['tp-setsticky'].'" src="' .$settings['tp_images_url']. '/TPsticky' , $mg['sticky']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setsticky'].'"  />
						<img style="cursor: pointer;" class="toggleLock" id="artLock' .$mg['id']. '" title="'.$txt['tp-setlock'].'" src="' .$settings['tp_images_url']. '/TPlock' , $mg['locked']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setlock'].'"  />';
					}
				else {
					// Must be a new article, so lets show the check boxes instead.
					echo '
						<input type="checkbox" id="artFront'. $mg['id']. '" name="tp_article_frontpage" value="1" /><label for="artFront'. $mg['id']. '">'. $txt['tp-setfrontpage']. '</label><br>
						<input type="checkbox" id="artSticky'. $mg['id']. '" name="tp_article_sticky" value="1" /><label for="artSticky'. $mg['id']. '">'. $txt['tp-setsticky']. '</label><br>
						<input type="checkbox" id="artLock'. $mg['id']. '" name="tp_article_locked" value="1" /><label for="artLock'. $mg['id']. '">'. $txt['tp-setlock']. '</label>';
					}
					echo '
						<br><br>
					</dd>';
				}
			    if(allowedTo('admin_forum') || allowedTo('tp_articles')) {
                    echo '
					<dt>
						<label for="field_name">', $txt['tp-published'], '</label>
					</dt>
					<dd><div class="description" style="line-height: 1.6em;">
							<b>',$txt['tp-pub_start'],': </b><br>';
				// day
				echo '
							<input name="tp_article_pub_start" type="hidden" value="'.$mg['pub_start'].'">
							<select size="1" name="tp_article_pubstartday">
								<option value="0">' . $txt['tp-notset'] . '</option>';
				$day    = !empty($mg['pub_start']) ? date("j",$mg['pub_start']) : 0;
				$month  = !empty($mg['pub_start']) ? date("n",$mg['pub_start']) : 0;
				$year   = !empty($mg['pub_start']) ? date("Y",$mg['pub_start']) : 0;
				$hour   = !empty($mg['pub_start']) ? date("G",$mg['pub_start']) : 0;
				$minute = !empty($mg['pub_start']) ? date("i",$mg['pub_start']) : 0;
				for($a=1; $a<32;$a++)
					echo '
								<option value="'.$a.'" ' , $day==$a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>';
				// month
				echo '
							<select size="1" name="tp_article_pubstartmonth"><option value="0">' . $txt['tp-notset'] . '</option>';
				for($a=1; $a<13; $a++)
					echo '
								<option value="'.$a.'" ' , $month==$a ? ' selected' : '' , '>'.$tpmonths[$a].'</option>  ';
				echo '
							</select>';
				// year
				echo '
							<select size="1" name="tp_article_pubstartyear"><option value="0">' . $txt['tp-notset'] . '</option>';
				$now = date("Y",time())+1;
				for($a = 2004; $a < $now + 2; $a++)
					echo '
								<option value="'.$a.'" ' , $year == $a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>';
				// hours
				echo ' -
							<select size="1" name="tp_article_pubstarthour">';
				for($a=0; $a<24;$a++)
					echo '
								<option value="'.$a.'" ' , $hour == $a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>';
				// minutes
				echo ' <b>:</b>
							<select size="1" name="tp_article_pubstartminute">';
				for($a = 0; $a < 60; $a++)
					echo '
								<option value="'.$a.'" ' , $minute == $a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select><br>';
				// day
				echo '
							<input name="tp_article_pub_end" type="hidden" value="'.$mg['pub_end'].'">
							<b>',$txt['tp-pub_end'],':</b><br><select size="1" name="tp_article_pubendday"><option value="0">' . $txt['tp-notset'] . '</option>';
				$day = !empty($mg['pub_end']) ? date("j",$mg['pub_end']) : 0;
				$month = !empty($mg['pub_end']) ? date("n",$mg['pub_end']) : 0;
				$year = !empty($mg['pub_end']) ? date("Y",$mg['pub_end']) : 0;
				$hour = !empty($mg['pub_end']) ? date("G",$mg['pub_end']) : 0;
				$minute = !empty($mg['pub_end']) ? date("i",$mg['pub_end']) : 0;
				for($a=1; $a<32;$a++)
					echo '
								<option value="'.$a.'" ' , $day == $a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>';
				// month
				echo '
							<select size="1" name="tp_article_pubendmonth"><option value="0">' . $txt['tp-notset'] . '</option>';
				for($a = 1; $a < 13; $a++)
					echo '
								<option value="'.$a.'" ' , $month == $a ? ' selected' : '' , '>'.$tpmonths[$a].'</option>  ';
				echo '
							</select>';
				// year
				echo '
							<select size="1" name="tp_article_pubendyear"><option value="0">' . $txt['tp-notset'] . '</option>';
				$now = date("Y",time())+1;
				for($a = 2004; $a < $now + 2; $a++)
					echo '
								<option value="'.$a.'" ' , $year == $a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>';
				// hours
				echo ' -
							<select size="1" name="tp_article_pubendhour">';
				for($a = 0; $a < 24; $a++)
					echo '
								<option value="'.$a.'" ' , $hour == $a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>';
				// minutes
				echo ' <b>:</b>
							<select size="1" name="tp_article_pubendminute">';
				for($a = 0; $a < 60; $a++)
					echo '
								<option value="'.$a.'" ' , $minute == $a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>
							</div>
						</dd>
					</dl>';
                }
			}

                if(allowedTo('admin_forum') || allowedTo('tp_articles')) {
				echo '
				<dl class="tptitle settings">
					<dt>
						', $txt['tp-display'], '
					</dt>
					<dd>
							<input name="tp_article_frame" id="usetheme" type="radio" value="theme" ' , $mg['frame']=='theme' ? 'checked' : '' , '><label for="usetheme"> '.$txt['tp-useframe'].'</label><br>
							<input name="tp_article_frame" id="useframe" type="radio" value="frame" ' , $mg['frame']=='frame' ? 'checked' : '' , '><label for="useframe"> '.$txt['tp-useframe2'].'</label><br>
							<input name="tp_article_frame" id="usetitle" type="radio" value="title" ' , $mg['frame']=='title' ? 'checked' : '' , '><label for="usetitle"> '.$txt['tp-usetitle'].' </label><br>
							<input name="tp_article_frame" id="noframe" type="radio" value="none" ' , $mg['frame']=='none' ? 'checked' : '' , '><label for="noframe"> '.$txt['tp-noframe'].'</label><br><br>
					</dd>
					<dt>
						', $txt['tp-illustration'], '
					</dt>
					<dd>
						<div class="article_icon" style="background: top right url(' , $boardurl , '/tp-files/tp-articles/illustrations/' , !empty($mg['illustration']) ? $mg['illustration'] : 'TPno_illustration.png' , ')no-repeat;"></div><br>
					</dd>
					<dt>
						<label for="tp_article_illustration">', $txt['tp-illustration2'], '</label>
					</dt>
					<dd>
						<select size="1" name="tp_article_illustration" id="tp_article_illustration" onchange="changeIllu(document.getElementById(\'tp-illu\'), this.value);">
								<option value=""' , $mg['illustration']=='' ? ' selected="selected"' : '' , '>' . $txt['tp-none2'] . '</option>';
			foreach($context['TPortal']['articons']['illustrations'] as $ill) {
				echo '<option value="'.$ill['file'].'"' , $ill['file']==$mg['illustration'] ? ' selected="selected"' : '' , '>'.$ill['file'].'</option>';
            }
			echo '
							</select><br>
						<img id="tp-illu" src="' , $boardurl , '/tp-files/tp-articles/illustrations/' , !empty($mg['illustration']) ? $mg['illustration'] : 'TPno_illustration.png' , '" alt="" /><br><br>
					</dd>
					<dt>
						' . $txt['tp-uploadicon'] . '
					</dt>
					<dd>
						<input type="file" name="tp_article_illupload">
					</dd>
				</dl>
					';
				$opts = array('','date','title','author','linktree','top','cblock','rblock','lblock','bblock','tblock','lbblock','category','catlist','comments','commentallow','commentupshrink','views','rating','ratingallow','nolayer','avatar','inherit','social','nofrontsetting');
				$tmp = explode(',',$mg['options']);
				$options=array();
				foreach($tmp as $tp => $val){
					if(substr($val,0,11)=='rblockwidth')
						$options['rblockwidth']=substr($val,11);
					elseif(substr($val,0,11)=='lblockwidth')
						$options['lblockwidth']=substr($val,11);
					else
						$options[$val]=1;
				}
				echo '
					<hr>
					<div>
						<div class="font-strong">'.$txt['tp-articleoptions'].'</div>
						<div class="article-details">';
				// article details options
				echo '
							<dl class="tptitle settings">
								<dt>
									<label for="toggleoptions">', $txt['tp-checkall'], '</label><br>
								</dt>
								<dd>
									<input id="toggleoptions" type="checkbox" onclick="invertAll(this, this.form, \'tp_article_options_\');" />
								</dd>
							</dl>
							<div class="font-strong">' . $txt['tp-details'] . '</div>
							<dl class="tptitle settings">
								<dt>
									<label for="tp_article_options_'.$opts[4].'">', $txt['tp-articleoptions4'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[4].'" id="tp_article_options_'.$opts[4].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[4]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[2].'">', $txt['tp-articleoptions2'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[2].'" id="tp_article_options_'.$opts[2].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[2]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[3].'">', $txt['tp-articleoptions3'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[3].'" id="tp_article_options_'.$opts[3].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[3]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[1].'">', $txt['tp-articleoptions1'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[1].'" id="tp_article_options_'.$opts[1].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[1]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[17].'">', $txt['tp-articleoptions17'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[17].'" id="tp_article_options_'.$opts[17].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[17]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[19].'">', $txt['tp-articleoptions19'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[19].'" id="tp_article_options_'.$opts[19].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[19]]) ? 'checked' : '' , '><br>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[18].'">', $txt['tp-articleoptions18'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[18].'" id="tp_article_options_'.$opts[18].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[18]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[21].'">', $txt['tp-articleoptions21'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[21].'" id="tp_article_options_'.$opts[21].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[21]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[23].'">', $txt['tp-showsociallinks'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[23].'" id="tp_article_options_'.$opts[23].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[23]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[12].'">', $txt['tp-articleoptions12'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[12].'" id="tp_article_options_'.$opts[12].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[12]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[13].'">', $txt['tp-articleoptions13'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[13].'" id="tp_article_options_'.$opts[13].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[13]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[15].'">', $txt['tp-articleoptions15'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[15].'" id="tp_article_options_'.$opts[15].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[15]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[14].'">', $txt['tp-articleoptions14'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[14].'" id="tp_article_options_'.$opts[14].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[14]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[16].'">', $txt['tp-articleoptions16'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[16].'" id="tp_article_options_'.$opts[16].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[16]]) ? 'checked' : '' , '>
								</dd>';							
								/*<dt>
									<label for="tp_article_options_'.$opts[5].'">', $txt['tp-articleoptions5'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[5].'" id="tp_article_options_'.$opts[5].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[5]]) ? 'checked' : '' , '>
								</dd>*/
					echo '	</dl>
								<div class="font-strong">' . $txt['tp-panels'] . '</div>
							<dl class="tptitle settings">
								<dt>
									<label for="tp_article_options_'.$opts[8].'">', $txt['tp-articleoptions8'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[8].'" id="tp_article_options_'.$opts[8].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[8]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_lblockwidth">', $txt['tp-articleoptions23'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_lblockwidth" id="tp_article_options_lblockwidth" type="text" value="', !empty($options['lblockwidth']) ?  $options['lblockwidth'] : '' ,'"><br>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[7].'">', $txt['tp-articleoptions7'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[7].'" id="tp_article_options_'.$opts[7].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[7]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_rblockwidth">', $txt['tp-articleoptions22'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_rblockwidth" id="tp_article_options_rblockwidth" type="text" value="', !empty($options['rblockwidth']) ?  $options['rblockwidth'] : '' ,'"><br>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[10].'">', $txt['tp-articleoptions10'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[10].'" id="tp_article_options_'.$opts[10].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[10]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[6].'">', $txt['tp-articleoptions6'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[6].'" id="tp_article_options_'.$opts[6].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[6]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[11].'">', $txt['tp-articleoptions11'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[11].'" id="tp_article_options_'.$opts[11].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[11]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[9].'">', $txt['tp-articleoptions9'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[9].'" id="tp_article_options_'.$opts[9].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[9]]) ? 'checked' : '' , '>
								</dd>
							</dl>
						<br>
							<dl class="tptitle settings">
								<dt>
									<label for="tp_article_options_'.$opts[22].'">', $txt['tp-articleoptions24'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[22].'" id="tp_article_options_'.$opts[22].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[22]]) ? 'checked' : '' , '>
								</dd>
							</dl>
							<div class="font-strong">' . $txt['tp-others'] . '</div>
							<dl class="tptitle settings">
								<dt>
									<label for="tp_article_idtheme">', $txt['tp-chosentheme'], '</label><br>
								</dt>
								<dd>
									<select size="1" name="tp_article_idtheme" id="tp_article_idtheme">';
									echo '			<option value="0" ', $mg['id_theme']==0 ? 'selected' : '' ,'>'.$txt['tp-none-'].'</option>';
									foreach($context['TPthemes'] as $them)
										echo '
														<option value="'.$them['id'].'" ',$them['id']==$mg['id_theme'] ? 'selected' : '' ,'>'.$them['name'].'</option>';
									echo '
								</select>
								</dd>
								<dt>
									<label for="tp_article_options_'.$opts[20].'">', $txt['tp-articleoptions20'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[20].'" id="tp_article_options_'.$opts[20].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[20]]) ? 'checked' : '' , '>
								</dd>
							</dl>
								<div>
										' , $txt['tp-articleheaders'] , '<br>
										<textarea id="tp_article_intro" name="tp_article_headers" rows="5" cols="40">' , $mg['headers'] , '</textarea>
								</div>
						</div>
				</div><br>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>';
                }
                else {
                    echo '<input name="tp_article_type" type="hidden" value="'.$article_type.'">';
				// set defaults for submissions?
					echo '	<input name="tp_article_authorid" type="hidden" value="' . $context['user']['id'] . '">
							<input name="tp_article_frame" type="hidden" value="theme">
							<input name="tp_article_options_date" type="hidden" value="date">
							<input name="tp_article_options_title" type="hidden" value="title">
							<input name="tp_article_options_author" type="hidden" value="author">
							<input name="tp_article_options_linktree" type="hidden" value="linktree">
							<input name="tp_article_options_views" type="hidden" value="views">
							<input name="tp_article_options_inherit" type="hidden" value="inherit">
							<input name="tp_article_options_social" type="hidden" value="social">';
				}

                echo'
				</div>
			</div>
		</div>
	</form>';



    $context['insert_after_template'] =
        '<script>
        $(function () {
                $(\'input[type=radio][name=tp_article_useintro]\').change(function() {
                    switch($(this).val()){
                        case "1":
                            $("#tp_article_show_intro").show()
                            break;
                        case "0":
                            $("#tp_article_show_intro").hide()
                            break;
                        default:
                            $("#tp_article_show_intro").hide()
                }
            });
        });
        </script>';
}

function template_submitsuccess()
{
	global $txt;

	echo '
		<div class="tborder">
                <div class="cat_bar">
				    <h3 class="catbg">'.$txt['tp-submitsuccess2'].'</h3>
                </div>
					<div class="windowbg padding-div" style="text-align: center;">'.$txt['tp-submitsuccess'].'
					<div class="padding-div">&nbsp;</div></div>
		</div>';
}

function template_editcomment()
{
    global $txt, $scripturl, $context;

    if(isset($context['TPortal']['comment_edit'])){
        echo '
            <form accept-charset="', $context['character_set'], '"  name="tp_edit_comment" action="'.$scripturl.'?action=tportal;sa=editcomment" method="post" style="margin: 1ex;">
                <input name="tp_editcomment_title" type="text" value="'.$context['TPortal']['comment_edit']['title'].'"> <br>
                <textarea name="tp_editcomment_body" rows="6" cols="20" style="width: 90%;" wrap="on">'.$context['TPortal']['comment_edit']['body'].'</textarea>
                <br>
                <input id="tp_editcomment_submit" type="submit" value="'.$txt['tp-submit'].'">
                <input name="tp_editcomment_type" type="hidden" value="article_comment">
                <input name="tp_editcomment_id" type="hidden" value="'.$context['TPortal']['comment_edit']['id'].'">
            </form>
        ';
    }


}

function template_showcomments()
{
    global $context, $txt, $scripturl;

    if(!empty($context['TPortal']['showall'])) {
			echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-commentall'] . '</h3></div>
		<div></div>
			<div id="show-art-comm" class="windowbg padding-div">
			<table class="table_grid tp_grid" style="width:100%">
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col" class="tp_comments">
					<div style="word-break:break-all;">
						<div class="float-items tpleft" style="width:30%;">' . $txt['tp-article'] . '</div>
						<div class="float-items tpleft" style="width:15%;">' . $txt['tp-author'] . '</div>
						<div class="float-items tpleft" style="width:30%;">' . $txt['tp-comments'] . '</div>
						<div class="float-items tpleft" style="width:25%;">' . $txt['by'] . '</div>
						<p class="clearthefloat"></p>
					</div>
					</th>
					</tr>
				</thead>
				<tbody>';

			if(!empty($context['TPortal']['artcomments']['new'])) {
				foreach($context['TPortal']['artcomments']['new'] as $mes) {
					echo '
                        <tr class="windowbg">
                            <td class="tp_comments">
                            <div>
                                <div class="float-items" style="width:30%;">
                                    <a href="'.$scripturl.'?page='.$mes['page'].'#tp-comment">' . $mes['subject'] . ' ' , ($mes['is_read']==0 && !TP_SMF21) ? ' <img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/new.gif" alt="" />' : '' , '</a>
                                </div>
                                <div class="float-items" style="width:15%;"><a href="'.$scripturl.'?action=profile;u='.$mes['authorID'].'">' . $mes['author'] . '</a></div>
                                <div class="float-items" style="width:30%;"><div class="smalltext">' , $mes['title'] , '<br> ' , substr($mes['comment'],0,150) , '...</div></div>
                                <div class="float-items" style="width:25%;">' , !empty($mes['member_id']) ? ' <a href="'.$scripturl.'?action=profile;u='.$mes['member_id'].'">' . $mes['membername'] . '</a> ' :  $txt['tp-guest'] , '<div class="smalltext">' . $mes['time'] . '</div>
                            </div>
                            <p class="clearthefloat"></p>
                            </div>
                            </td>
                        </tr>';
                }
			}
			echo '
			<tr class="windowbg">
			    <td class="shouts">
				    <div class="padding-div tpright"><a href="' . $scripturl . '?action=tportal;sa=showcomments">' . $txt['tp-showcomments'] . '</a></div>
			    </td>
			</tr>';

			echo '
			</tbody>
		</table>
		<div class="tp_pad">'.$context['TPortal']['pageindex'].'</div>
		</div>
		';
		}
		else {
			echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-commentnew'] . '</h3></div>
		<div></div>
			<div id="latest-art-comm" class="windowbg padding-div">
			<table class="table_grid tp_grid" style="width:100%">
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col" class="tp_comments">
			<div>
				<div class="float-items tpleft" style="width:30%;">' . $txt['tp-article'] . '</div>
				<div class="float-items tpleft" style="width:15%;">' . $txt['tp-author'] . '</div>
				<div class="float-items tpleft" style="width:30%;">' . $txt['tp-comments'] . '</div>
				<div class="float-items tpleft" style="width:25%;">' . $txt['by'] . '</div>
				<p class="clearthefloat"></p>
			</div>
				</th>
				</tr>
			</thead>
			<tbody>';

			if(!empty($context['TPortal']['artcomments']['new'])) {
				foreach($context['TPortal']['artcomments']['new'] as $mes) {
					echo '
			<tr class="windowbg">
			<td class="tp_comments">
			<div>
				<div class="float-items" style="width:30%;"><a href="'.$scripturl.'?page='.$mes['page'].'#tp-comment">' . $mes['subject'] . '
				' , ($mes['is_read']==0 && !TP_SMF21) ? ' <img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/new.gif" alt="" />' : '' , '
				</a><div class="smalltext"> ' , $mes['title'] , '</div>
				</div>
				<div class="float-items" style="width:15%;"><a href="'.$scripturl.'?action=profile;u='.$mes['authorID'].'">' . $mes['author'] . '</a></div>
				<div class="float-items" style="width:30%;">' , $mes['title'] , '<br> ' , $mes['comment'] , '</div>
				<div class="float-items" style="width:25%;">' , !empty($mes['member_id']) ? ' <a href="'.$scripturl.'?action=profile;u='.$mes['member_id'].'">' . $mes['membername'] . '</a> ' :  $txt['tp-guest'] , '<div class="smalltext">' . $mes['time'] . '</div></div>
				<p class="clearthefloat"></p>
			</div>
			</td>
			</tr>';
                }
			}
			else {
				echo '
			<tr class="windowbg">
			<td class="tp_comments">
				<div class="padding-div">' . $txt['tp-nocomments2'] . '</div>
			</td>
			</tr>';
			echo '
			</tbody>
		</table>
		<div class="tp_pad">'.$context['TPortal']['pageindex'].'</div>
		</div>';
            }	
		}
}

function template_addsuccess()
{
    global $txt;

    echo '
        <div class="tborder">
            <div style="padding: 30px 10px 30px 10px;text-align:center;" class="windowbg">'.$txt['tp-addsuccess'].'</div>
        </div>
    ';

}
// My Articles
function template_showarticle()
{
	global $txt, $context, $settings, $scripturl;

	echo '
        <div class="cat_bar">
            <h3 class="catbg">' .$txt['tp-myarticles'] . '</h3>
        </div>
		<div class="windowbg padding-div">
	<table class="table_grid tp_grid" style="width:100%";>
		<thead>
			<tr class="title_bar titlebg2">
			<th scope="col" class="myarticles">
				<div class="font-strong" style="padding:0px;">
					<div align="center" class="float-items">', $context['TPortal']['tpsort']=='subject' ? '<img src="' .$settings['tp_images_url']. '/TPsort_up.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=tportal;sa=myarticles;tpsort=subject">'.$txt['tp-arttitle'].'</a></div>
				</div>
			</th>
			</tr>
		</thead>
		<tbody>';
			if(count($context['TPortal']['myarticles']) > 0) {
				foreach($context['TPortal']['myarticles'] as $art) {
					echo '
					<tr class="windowbg">
					<td class="articles">
						<div style="overflow: hidden; padding: 3px;">
							<div style="float: right;">';
					if($art['approved'] == 0) {
							echo '<img src="' . $settings['tp_images_url'] . '/TPthumbdown.png" title="'. $txt['tp-notapproved'] .'" alt="*" />&nbsp; ';
					}
					if((allowedTo('tp_editownarticle') || allowedTo('tp_articles')) && $art['locked']==0) {
						echo '
						<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article='.$art['id'].'" title="'. $txt['tp-editarticle'] .'"><img src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="*" /></a>&nbsp; ';
					} 
					if($art['off']==0) { 
							echo '<img src="' . $settings['tp_images_url'] . '/TPactive2.png" title="" alt="*" />&nbsp; ';
					}
					else {
							echo '<img src="' . $settings['tp_images_url'] . '/TPactive1.png" title="'. $txt['tp-noton'] .'" alt="*" />&nbsp; ';
					}
                    echo '
                        </div>';
					
					if($art['locked']==1) { 
						echo '
						<img title="'.$txt['tp-islocked'].'" src="' .$settings['tp_images_url']. '/TPlock1.png" alt="'.$txt['tp-islocked'].'"  />&nbsp';
					}
				
                    if($art['off'] == 0 && $art['approved'] == 1) {
                        echo '
                        <a href="' . $scripturl . '?page='.$art['id'].'" title="'. $txt['tp-viewarticle'] .'">' . html_entity_decode($art['subject']) . '</a>'; 
                    }
                    else {
                        echo '
                    (<i>' . html_entity_decode($art['subject']). '</i>)';
                    }
					echo '
						</div>
					</td>
					</tr>';
				}
			}
			else {
				echo '
					<tr class="windowbg">
					<td class="articles"> 
					'. $txt['tp-noarticlesfound'] .'
					</td>
					</tr>';
			}
		echo '
			</tbody>
		</table>';

		if(!empty($context['TPortal']['pageindex'])) {
				echo '
				<div class="middletext padding-div">' . $context['TPortal']['pageindex'] . '</div>
				<div class="padding-div"></div>';
		}

		echo '
			</div>';

}

?>
