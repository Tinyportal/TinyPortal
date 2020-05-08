<?php
/**
 * @package TinyPortal
 * @version 1.6.6
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

function template_main()
{
	global $context, $settings, $txt, $scripturl, $forum_version;

	if(isset($context['TPortal']['subaction'])){
		switch($context['TPortal']['subaction']){
			case 'editcomment':
				if(isset($context['TPortal']['comment_edit'])){
					echo '
		<form accept-charset="', $context['character_set'], '"  name="tp_edit_comment" action="'.$scripturl.'?action=tpmod;sa=editcomment" method="post" style="margin: 1ex;">
			<input name="tp_editcomment_title" type="text" value="'.$context['TPortal']['comment_edit']['title'].'"> <br>
			<textarea name="tp_editcomment_body" rows="6" cols="20" style="width: 90%;" wrap="on">'.$context['TPortal']['comment_edit']['body'].'</textarea>
			<br><input id="tp_editcomment_submit" type="submit" value="'.$txt['tp-submit'].'">
			<input name="tp_editcomment_type" type="hidden" value="article_comment">
			<input name="tp_editcomment_id" type="hidden" value="'.$context['TPortal']['comment_edit']['id'].'">
		</form>';
				}
				break;
			case 'addsuccess':
				echo '
		<div class="tborder">
			<div style="padding: 30px 10px 30px 10px;text-align:center;" class="windowbg">'.$txt['tp-addsuccess'].'</div>
		</div>';
				break;
			case 'editarticle':
				$mg=$context['TPortal']['editarticle'];
				echo '
		<form accept-charset="', $context['character_set'], '"  name="TPadmin3" action="' . $scripturl . '?action=tpmod;sa=savearticle" method="post" enctype="multipart/form-data" onsubmit="submitonce(this);">
			<div id="users-editarticle" class="bordercolor users-area">
				<div></div>
				<div class="cat_bar">
					<h3 class="catbg">'.$txt['tp-editarticle'].'&nbsp;' ,$mg['subject'], '&nbsp;-&nbsp;<a href="'.$scripturl.'?page='.$mg['id'].'">['.$txt['tp-preview'].']</a> </h3>
				</div>
				<div class="windowbg noup tp_pad">				';
				if($mg['locked']==1)
				{
					echo '
					<div class="error padding_div" style="text-align:center;">
						'.$txt['tp-articlelocked'].'
					</div>';
				}
				if($mg['approved']==0)
				{
					echo '
					<div class="error padding_div" style="text-align:center;">
						'.$txt['tp-notapproved'].'
					</div>';
				}
					echo '
					<div class="font-strong">'.$txt['tp-arttitle'].'
					</div>
					<input style="width: 92%;" name="tp_article_title'.$mg['id'].'" type="text" value="'.$mg['subject'].'">
				<br><br>
				<div class="font-strong">'.$txt['tp-artbodytext'].' </div>
				<div>';

				$tp_use_wysiwyg = $context['TPortal']['show_wysiwyg'];

				if($mg['articletype']=='php')
					echo '
						<textarea name="tp_article_body'.$mg['id'].'" id="tp_article_body'.$mg['id'].'" wrap="auto">' , $mg['body'], '</textarea><br>';

				elseif($mg['articletype']=='html' && $tp_use_wysiwyg > 0)
					TPwysiwyg('tp_article_body'.$mg['id'], $mg['body'], true,'qup_tp_article_body', $tp_use_wysiwyg);

				elseif($mg['articletype']=='html' && $tp_use_wysiwyg == 0 )
					echo '
						<textarea name="tp_article_body'.$mg['id'].'" id="tp_article_body" wrap="auto">' , $mg['body'], '</textarea><br>';

				elseif($mg['articletype']=='bbc')
				{
					TP_bbcbox($context['TPortal']['editor_id']);
				}
				else
					echo $txt['tp-importarticle'] , '</div>
					<div><input size="40" name="tp_article_importlink'.$mg['id'].'" type="text" value="' , $mg['fileimport'] , '"> ' ;

					
				echo '
				<hr>
				<dl class="settings">
					<dt>
						<label for="tp_article_useintro">', $txt['tp-useintro'], '</label>
					</dt>
					<dd>
							<input name="tp_article_useintro'.$mg['id'].'" type="radio" value="1" ', $mg['useintro']=='1' ? 'checked' : '' ,'> '.$txt['tp-yes'].'
							<input name="tp_article_useintro'.$mg['id'].'" type="radio" value="0" ', $mg['useintro']=='0' ? 'checked' : '' ,'> '.$txt['tp-no'].'<br>
					</dd>
				</dl>
				<div id="tp_article_show_intro"', ($mg['useintro'] == 0) ? 'style="display:none;">' : '>' ,
                    '<div class="font-strong">' . $txt['tp-artintrotext']. '</div>';

				if($mg['articletype']=='php')
					echo '
						<textarea name="tp_article_intro'.$mg['id'].'" id="tp_article_intro" wrap="auto">' , $mg['intro'], '</textarea><br>';

				elseif($mg['articletype'] == 'html' && $tp_use_wysiwyg > 0)
						TPwysiwyg('tp_article_intro'.$mg['id'], $mg['intro'], true,'qup_tp_article_intro', $tp_use_wysiwyg, false);

				elseif($mg['articletype'] == 'html' && $tp_use_wysiwyg == 0)
					echo '
							<textarea name="tp_article_intro'.$mg['id'].'" id="tp_article_intro" wrap="auto">' , $mg['intro'], '</textarea><br>';

				elseif($mg['articletype']=='bbc')
				{
					echo '
					<textarea name="tp_article_intro'.$mg['id'].'" id="tp_article_intro" wrap="auto">' , $mg['intro'], '</textarea><br>';
				}

				echo '
				</div></div>
				<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="send"></div>
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
		
				break;
			case 'editblock':
				echo '
		<form accept-charset="', $context['character_set'], '"  name="TPadmin3" action="' . $scripturl . '?action=tpmod;sa=saveblock'.$context['TPortal']['blockedit']['id'].'" method="post" onsubmit="submitonce(this);">
			<div id="super-user" class="bordercolor">
				<div class="catbg">
					<div style="padding:1%;">'.$txt['tp-editblock'].' </div>
				</div>
				<div class="windowbg2" style="padding:1%;">
					<div>
					'.$txt['tp-title'].'<br><input style="width: 94%" name="blocktitle' .$context['TPortal']['blockedit']['id']. '" type="text" value="' .$context['TPortal']['blockedit']['title']. '">
					<br>';
				if($context['TPortal']['blockedit']['type']=='11')
				{
					if($context['TPortal']['use_wysiwyg'] && !empty($context['TPortal']['usersettings']['wysiwyg']))
						TPwysiwyg('blockbody' .$context['TPortal']['blockedit']['id'], $context['TPortal']['blockedit']['body'], true, 'qup_blockbody', 2, false);
					else
						echo '
					<textarea style="width: 100%; height: ' . $context['TPortal']['editorheight'] . 'px;" name="blockbody'.$context['TPortal']['blockedit']['id'].'_pure" id="blockbody'.$context['TPortal']['blockedit']['id'].'_pure">'. $context['TPortal']['blockedit']['body'] .'</textarea><br>';
				}
				elseif($context['TPortal']['blockedit']['type']=='5')
				{
					TP_bbcbox($context['TPortal']['editor_id']);
				}
				elseif($context['TPortal']['blockedit']['type']=='10')
				{
					echo $txt['tp-body'].' <br><textarea style="width: 94%;" name="blockbody' .$context['TPortal']['blockedit']['id']. '" rows=15 cols=40 wrap="auto">' . $context['TPortal']['blockedit']['body'] . '</textarea>';
				}
				elseif($context['TPortal']['blockedit']['type']=='12'){
					// check to see if it is numeric
					if(!is_numeric($context['TPortal']['blockedit']['body']))
						$context['TPortal']['blockedit']['body']='10';

					echo '<br>'.$txt['tp-numberofrecenttopics'].'<input style="width: 50px;" name="blockbody' .$context['TPortal']['blockedit']['id']. '" value="' .$context['TPortal']['blockedit']['body']. '"><br>';
				}
				elseif($context['TPortal']['blockedit']['type']=='13'){
					// SSI block..which function?
					if(!in_array($context['TPortal']['blockedit']['body'],array('recentpoll','toppoll','topposters','topboards','topreplies','topviews','calendar')))
						$context['TPortal']['blockedit']['body']='';
					echo '<br>';
					echo '<input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="" ' , $context['TPortal']['blockedit']['body']=='' ? 'checked' : '' , '>' .$txt['tp-none-'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="recentpoll" ' , $context['TPortal']['blockedit']['body']=='recentpoll' ? 'checked' : '' , '>'.$txt['tp-ssi-recentpoll'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="toppoll" ' , $context['TPortal']['blockedit']['body']=='toppoll' ? 'checked' : '' , '>'.$txt['tp-ssi-toppoll'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="topboards" ' , $context['TPortal']['blockedit']['body']=='topboards' ? 'checked' : '' , '>'.$txt['tp-ssi-topboards'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="topposters" ' , $context['TPortal']['blockedit']['body']=='topposters' ? 'checked' : '' , '>'.$txt['tp-ssi-topposters'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="topreplies" ' , $context['TPortal']['blockedit']['body']=='topreplies' ? 'checked' : '' , '>'.$txt['tp-ssi-topreplies'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="topviews" ' , $context['TPortal']['blockedit']['body']=='topviews' ? 'checked' : '' , '>'.$txt['tp-ssi-topviews'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="calendar" ' , $context['TPortal']['blockedit']['body']=='calendar' ? 'checked' : '' , '>'.$txt['tp-ssi-calendar'];
					echo '<hr />';
				}
				elseif($context['TPortal']['blockedit']['type']=='14'){
					// Module block...choose module and module ID , check if module is active
					echo '<br>';
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="dl-stats" ' , $context['TPortal']['blockedit']['body']=='dl-stats' ? 'checked' : '' , '>'.$txt['tp-module1'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="dl-stats2" ' , $context['TPortal']['blockedit']['body']=='dl-stats2' ? 'checked' : '' , '>'.$txt['tp-module2'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="dl-stats3" ' , $context['TPortal']['blockedit']['body']=='dl-stats3' ? 'checked' : '' , '>'.$txt['tp-module3'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="dl-stats4" ' , $context['TPortal']['blockedit']['body']=='dl-stats4' ? 'checked' : '' , '>'.$txt['tp-module4'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="dl-stats5" ' , $context['TPortal']['blockedit']['body']=='dl-stats5' ? 'checked' : '' , '>'.$txt['tp-module5'];
					echo '<br><input name="blockbody' .$context['TPortal']['blockedit']['id']. '" type="radio" value="dl-stats6" ' , $context['TPortal']['blockedit']['body']=='dl-stats6' ? 'checked' : '' , '>'.$txt['tp-module6'].'<br>';
				}
				elseif($context['TPortal']['blockedit']['type']=='3'){
					// userbox type
					echo '<br>'.$txt['tp-showuserbox'].'<br>
						<input name="tp_userbox_options0" type="checkbox" value="avatar" ', (isset($context['TPortal']['userbox']['avatar']) && $context['TPortal']['userbox']['avatar']) ? 'checked' : '' , '> '.$txt['tp-userbox1'].'<br>';
					echo '<input name="tp_userbox_options1" type="checkbox" value="logged" ', (isset($context['TPortal']['userbox']['logged']) && $context['TPortal']['userbox']['logged']) ? 'checked' : '' , '> '.$txt['tp-userbox2'].'<br>';
					echo '<input name="tp_userbox_options2" type="checkbox" value="time" ', (isset($context['TPortal']['userbox']['time']) && $context['TPortal']['userbox']['time']) ? 'checked' : '' , '> '.$txt['tp-userbox3'].'<br>';
					echo '<input name="tp_userbox_options3" type="checkbox" value="unread" ', (isset($context['TPortal']['userbox']['unread']) && $context['TPortal']['userbox']['unread']) ? 'checked' : '' , '> '.$txt['tp-userbox4'].'<br>';
					echo '<input name="tp_userbox_options4" type="checkbox" value="stats" ', (isset($context['TPortal']['userbox']['stats']) && $context['TPortal']['userbox']['stats']) ? 'checked' : '' , '> '.$txt['tp-userbox5'].'<br>';
					echo '<input name="tp_userbox_options5" type="checkbox" value="online" ', (isset($context['TPortal']['userbox']['online']) && $context['TPortal']['userbox']['online']) ? 'checked' : '' , '> '.$txt['tp-userbox6'].'<br>';
					echo '<input name="tp_userbox_options6" type="checkbox" value="stats_all" ', (isset($context['TPortal']['userbox']['stats_all']) && $context['TPortal']['userbox']['stats_all']) ? 'checked' : '' , '> '.$txt['tp-userbox7'].'<br>
							<br>';
				}
				elseif($context['TPortal']['blockedit']['type']=='15'){
					// RSS feed type
					echo '<br><input style="width: 95%" name="blockbody' .$context['TPortal']['blockedit']['id']. '" value="' .$context['TPortal']['blockedit']['body']. '"><br><br>';
					echo $txt['tp-rssblock-showonlytitle'].'
							<input name="blockvar2' .$context['TPortal']['blockedit']['id']. '" type="radio" value="1" ' , $context['TPortal']['blockedit']['var2']=='1' ? ' checked' : '' ,'>'.$txt['tp-yes'].'
							<input name="blockvar2' .$context['TPortal']['blockedit']['id']. '" type="radio" value="0" ' , ($context['TPortal']['blockedit']['var2']=='0' || $context['TPortal']['blockedit']['var2']=='') ? ' checked' : '' ,'>'.$txt['tp-no'].'<br><br>';
				}
				elseif($context['TPortal']['blockedit']['type']=='16'){
					echo '<br>'.$txt['tp-sitemapmodules'].'<ul class="disc">';
					if($context['TPortal']['show_download']=='1')
						echo '<li>'.$txt['tp-dldownloads'].'</li>';
					if($context['TPortal']['show_gallery']=='1')
						echo '<li>'.$txt['tp-admin15'].'</li>';
					if($context['TPortal']['show_linkmanager']=='1')
						echo '<li>'.$txt['tp-admin13'].'</li>';
					if($context['TPortal']['show_teampage']=='1')
						echo '<li>'.$txt['tp-admin17'].'</li>';

					echo '</ul>';
				}
				elseif($context['TPortal']['blockedit']['type']=='18'){
					// check to see if it is numeric
					if(!is_numeric($context['TPortal']['blockedit']['body']))
						$lblock['body']='';

					echo '<br>',$txt['tp-showarticle'],' <select name="blockbody' .$context['TPortal']['blockedit']['id']. '">';
					foreach($context['TPortal']['edit_articles'] as $article){
						echo '<option value="'.$article['id'].'" ' , $context['TPortal']['blockedit']['body']==$article['id'] ? ' selected' : '' ,' >'.$article['subject'].'</option>';
					}
					echo '</select><br>';
				}
				elseif($context['TPortal']['blockedit']['type']=='19'){
					// check to see if it is numeric
					if(!is_numeric($context['TPortal']['blockedit']['body']))
						$lblock['body']='';
					if(!is_numeric($context['TPortal']['blockedit']['var1']))
						$lblock['var1']='15';
					if($context['TPortal']['blockedit']['var1']=='0')
						$lblock['var1']='15';

					echo '<br>',$txt['tp-showcategory'],' <select name="blockbody' .$context['TPortal']['blockedit']['id']. '">';
					foreach($context['TPortal']['admin_categories'] as $cats){
						echo '<option value="'.$cats['id'].'" ' , $context['TPortal']['blockedit']['body']==$cats['id'] ? ' selected' : '' ,' >'.$cats['name'].'</option>';
				}
				echo '</select><br>';
				echo $txt['tp-catboxheight'].'
						<input name="blockvar1' .$context['TPortal']['blockedit']['id']. '" type="text" value="' , $context['TPortal']['blockedit']['var1'] ,'"> em';
			}
			echo '<br>
						<input name="blockframe' .$context['TPortal']['blockedit']['id']. '" type="radio" value="theme" ' , $context['TPortal']['blockedit']['frame']=='theme' ? 'checked' : '' , '> '.$txt['tp-useframe'].'<br>
						<input name="blockframe' .$context['TPortal']['blockedit']['id']. '" type="radio" value="frame" ' , $context['TPortal']['blockedit']['frame']=='frame' ? 'checked' : '' , '> '.$txt['tp-useframe2'].' <br>
						<input name="blockframe' .$context['TPortal']['blockedit']['id']. '" type="radio" value="title" ' , $context['TPortal']['blockedit']['frame']=='title' ? 'checked' : '' , '> '.$txt['tp-usetitle'].' <br>
						<input name="blockframe' .$context['TPortal']['blockedit']['id']. '" type="radio" value="none" ' , $context['TPortal']['blockedit']['frame']=='none' ? 'checked' : '' , '> '.$txt['tp-noframe'].'<br>';

			echo '<hr />
						<input name="blockvisible' .$context['TPortal']['blockedit']['id']. '" type="radio" value="1" ' , ($context['TPortal']['blockedit']['visible']=='' || $context['TPortal']['blockedit']['visible']=='1') ? 'checked' : '' , '> '.$txt['tp-allowupshrink'].'<br>
						<input name="blockvisible' .$context['TPortal']['blockedit']['id']. '" type="radio" value="0" ' , ($context['TPortal']['blockedit']['visible']=='0') ? 'checked' : '' , '> '.$txt['tp-notallowupshrink'].'<br>

					</div>
			     </div>';

			echo '
				<div class="windowbg">
					<div class="tpcenter" style="padding:1%;><input type="submit" value="'.$txt['tp-send'].'" name="send"></div>
				</div>
			</div>
		</form>';
			break;
		case 'searcharticle':
			echo '
		<form accept-charset="', $context['character_set'], '" name="TPsearcharticle" action="' . $scripturl . '?action=tpmod;sa=searcharticle2" method="post">
			<div class="tborder" style="margin: auto;">
                <div class="cat_bar">
				    <h3 class="catbg">' , $txt['tp-searcharticles2'] , '</h3>
                </div>
			<span class="upperframe"><span></span></span>
				<div class="roundframe noup">
					<div class="tp_pad">'.$txt['tp-searcharticleshelp'].'</div>
					<div class="tp_pad">
						<b>'.$txt['tp-search'].':</b><br>
						<input id="searchbox" type="text" name="tpsearch_what" required/><br>
						<input type="checkbox" name="tpsearch_title" checked="checked" /> '.$txt['tp-searchintitle'].'<br>
						<input type="checkbox" name="tpsearch_body" checked="checked" /> '.$txt['tp-searchinbody'],'<br>
						<input type="hidden" name="sc" value="' , $context['session_id'] , '" /><br>
						<input type="submit" class="button button_submit" value="'.$txt['tp-search'].'">
					</div>';
					
				if ($context['TPortal']['fulltextsearch']==1) {
				echo '
					<div class="tp_pad">'.$txt['tp-searcharticleshelp2'].'</div>';
				}
				echo '
				</div>
				<span class="lowerframe"><span></span></span>
			</div>
		</form>
			';
			break;
		case 'showcomments':
		if(!empty($context['TPortal']['showall'])){
			echo '
		<div></div>
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-commentall'] . '</h3></div>
			<div id="show-art-comm" class="windowbg padding-div">
			<table class="table_grid tp_grid" style="width:100%">
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col" class="comments">
					<div style="word-break:break-all;">
						<div class="float-items" style="width:30%; text-align:left">' . $txt['tp-article'] . '</div>
						<div class="float-items" style="width:15%; text-align:left">' . $txt['tp-author'] . '</div>
						<div class="float-items" style="width:30%; text-align:left">' . $txt['tp-comments'] . '</div>
						<div class="float-items" style="width:25%; text-align:left">' . $txt['by'] . '</div>
						<p class="clearthefloat"></p>
					</div>
					</th>
					</tr>
				</thead>
				<tbody>';

			if(!empty($context['TPortal']['artcomments']['new']))
			{
				foreach($context['TPortal']['artcomments']['new'] as $mes)
					echo '
			<tr class="windowbg">
			<td class="comments">
			<div>
				<div class="float-items" style="width:30%;"><a href="'.$scripturl.'?page='.$mes['page'].'#tp-comment">' . $mes['subject'] . '
				' , ($mes['is_read']==0 && strstr($forum_version, '2.0')) ? ' <img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/new.gif" alt="" />' : '' , '
				</a>
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
			echo '
			<tr class="windowbg">
			<td class="shouts">
				<div style="padding:1%; text-align:right"><a href="' . $scripturl . '?action=tpmod;sa=showcomments">' . $txt['tp-showcomments'] . '</a></div>
			</td>
			</tr>';

			echo '
			</tbody>
		</table>
		<div class="tp_pad">'.$context['TPortal']['pageindex'].'</div>
		</div>
		';
		}
		else
		{
			echo '
		<div></div>
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-commentnew'] . '</h3></div>
			<div id="latest-art-comm" class="windowbg padding-div">
			<table class="table_grid tp_grid" style="width:100%">
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col" class="comments">
			<div>
				<div class="float-items" style="width:30%; text-align:left">' . $txt['tp-article'] . '</div>
				<div class="float-items" style="width:15%; text-align:left">' . $txt['tp-author'] . '</div>
				<div class="float-items" style="width:30%; text-align:left">' . $txt['tp-comments'] . '</div>
				<div class="float-items" style="width:25%; text-align:left">' . $txt['by'] . '</div>
				<p class="clearthefloat"></p>
			</div>
				</th>
				</tr>
			</thead>
			<tbody>';

			if(!empty($context['TPortal']['artcomments']['new']))
			{
				foreach($context['TPortal']['artcomments']['new'] as $mes)
					echo '
			<tr class="windowbg">
			<td class="comments">
			<div>
				<div class="float-items" style="width:30%;"><a href="'.$scripturl.'?page='.$mes['page'].'#tp-comment">' . $mes['subject'] . '
				' , ($mes['is_read']==0 && strstr($forum_version, '2.0')) ? ' <img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/new.gif" alt="" />' : '' , '
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
			else
				echo '
			<tr class="windowbg">
			<td class="comments">
				<div style="padding:1%;">' . $txt['tp-nocomments2'] . '</div>
			</td>
			</tr>';
			echo '
			<tr class="windowbg">
			<td class="comments">
				<div style="padding:1%; text-align:right"><a href="' . $scripturl . '?action=tpmod;sa=showcomments;showall">' . $txt['tp-showall'] . '</a></div>
			</td>
			</tr>';
			echo '
			</tbody>
		</table>
		<div class="tp_pad">'.$context['TPortal']['pageindex'].'</div>
		</div>';
	
		}
			break;
		case 'searcharticle2':
			echo '
		<div class="tborder">
            <div class="cat_bar">
                <h3 class="catbg">' , $txt['tp-searchresults'] , '
                ' . $txt['tp-searchfor'] . '  &quot;'.$context['TPortal']['searchterm'].'&quot;</h3>
            </div>
			<span class="upperframe"><span></span></span>
			<div class="roundframe noup">
				<div class="tp_pad">'.$txt['tp-searcharticleshelp'].'</div>
					<div class="tp_pad">
					<form style="margin: 0; padding: 0;" accept-charset="', $context['character_set'], '"  name="TPsearcharticle" action="' . $scripturl . '?action=tpmod;sa=searcharticle2" method="post">
					<div class="tp_pad">
						<b>'.$txt['tp-search'].':</b><br>
						<input id="searchbox" type="text" value="'.$context['TPortal']['searchterm'].'" name="tpsearch_what" required/><br>
						<input type="checkbox" name="tpsearch_title" checked="checked" /> '.$txt['tp-searchintitle'].'<br>
						<input type="checkbox" name="tpsearch_body" checked="checked" /> '.$txt['tp-searchinbody'],'<br>
						<input type="hidden" name="sc" value="' , $context['session_id'] , '" /><br>
						<input type="submit" class="button button_submit" value="'.$txt['tp-search'].'">
					</div>';
					
				if ($context['TPortal']['fulltextsearch']==1) {
				echo '
					<div class="tp_pad">'.$txt['tp-searcharticleshelp2'].'</div>';
				}
				echo '
					</form>
				</div>
			</div>
			<span class="lowerframe"><span></span></span>
		</div>
			';
			$bb = 1;
			foreach($context['TPortal']['searchresults'] as $res)
			{
				echo '
					<div class="windowbg padding-div" style="margin-bottom:3px;">
						<h4 class="tpresults"><a href="' . $scripturl . '?page=' . $res['id'] . '">' . $res['subject'] . '</a></h4>
						<hr>
						<div class="tpresults" style="padding-top: 4px;">
							<div class="middletext">' , $res['body'] . '</div>
							<div class="smalltext" style="padding-top: 0.4em;">' , $txt['tp-by'] . ' ' . $res['author'] . ' - ', timeformat($res['date']) , '</div>
						</div>
					</div>';
				$bb++;
			}

			break;
		case 'myarticles':
			echo '
        <div class="cat_bar">
            <h3 class="catbg">' .$txt['tp-myarticles'] . '</h3>
        </div>
		<div class="windowbg padding-div">
	<table class="table_grid tp_grid tp_grid" style="width:100%">
		<thead>
			<tr class="title_bar titlebg2">
			<th scope="col" class="myarticles">
				<div class="font-strong" style="padding:0px;">
					<div class="float-items title-admin-area tpcenter">', $context['TPortal']['tpsort']=='subject' ? '<img src="' .$settings['tp_images_url']. '/TPsort_up.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=tpmod;sa=myarticles;tpsort=subject">'.$txt['subject'].'</a></div>
				</div>
			</th>
			</tr>
		</thead>
		<tbody>';

			if(count($context['TPortal']['myarticles'])>0)
			{
				foreach($context['TPortal']['myarticles'] as $art)
				{
					echo '
				<tr class="windowbg">
				<td class="articles">
					<div style="overflow: hidden; padding: 3px;">
						<div style="float: right;">';
				if($art['approved']==0)
						echo '<img src="' . $settings['tp_images_url'] . '/TPthumbdown.png" alt="*" /> ';
				if($art['off']==0 && $art['approved']==1)
						echo '<img src="' . $settings['tp_images_url'] . '/TPactive2.png" alt="*" /> ';
				else
						echo '<img src="' . $settings['tp_images_url'] . '/TPactive1.png" alt="*" /> ';

				if($art['locked']==1)
						echo '<img src="' . $settings['tp_images_url'] . '/TPlock1.png" alt="*" /> ';

				if((allowedTo('tp_editownarticle') && $art['locked']==0) && !allowedTo('tp_articles'))
					echo '
					<a href="' . $scripturl . '?action=tpmod;sa=editarticle'.$art['id'].'"><img src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="*" /></a>';
				elseif(allowedTo('tp_articles'))
					echo '
					<a href="' . $scripturl . '?action=tpadmin;sa=editarticle'.$art['id'].'"><img src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="*" /></a>';

					echo '
						</div>';

					if($art['off'] == 0 && $art['approved'] == 1)
						echo '
						<a href="' . $scripturl . '?page='.$art['id'].'">' . html_entity_decode($art['subject']) . '</a>';
					else
						echo '
					(<i>' . html_entity_decode($art['subject']). '</i>)';

					echo '
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
				'. $txt['tp-noarticlesfound'] .'
				</td>
				</tr>';
			}
		echo '
			</tbody>
		</table>';

			if(!empty($context['TPortal']['pageindex']))
				echo '
				<div class="middletext padding-div">' . $context['TPortal']['pageindex'] . '</div>
				<div class="padding-div"></div>';
			echo '
		</div>';

			break;
		}
	}
	else
		redirectexit();
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

function template_submitarticle()
{
	global $context, $txt, $scripturl;

	echo '
	<form style="clear: both;" accept-charset="', $context['character_set'], '" name="TPadmin3" action="' . $scripturl . '?action=tpmod;sa=submitarticle2" method="post" enctype="multipart/form-data" onsubmit="submitonce(this);">
				<input name="TPadmin_submit" type="hidden" value="set">
				<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<div id="users-addarticle" class="bordercolor">
			<div></div>
			<div class="cat_bar">
				<h3 class="catbg">' , (isset($context['TPortal']['submitbbc'])) ? $txt['tp-submitarticlebbc'] : $txt['tp-submitarticle'] , '</h3>
			</div>
			<div class="windowbg noup tp_pad">
				<div class="font-strong">'.$txt['tp-arttitle'].'</div>
				<input style="width: 92%;" name="tp_article_title" type="text" value="">
				<br><br>
				<div class="font-strong">'.$txt['tp-artbodytext'].' </div>
				<div>';

			$tp_use_wysiwyg = $context['TPortal']['show_wysiwyg'];

			if($tp_use_wysiwyg > 0 && !isset($context['TPortal']['submitbbc']))
				TPwysiwyg('tp_article_body', '', true,'qup_tp_article_body', $tp_use_wysiwyg);

			elseif($tp_use_wysiwyg == 0 && !isset($context['TPortal']['submitbbc']))
				echo '
					<textarea name="tp_article_body" id="tp_article_body" wrap="auto"></textarea><br>';

			elseif(isset($context['TPortal']['submitbbc']))
				TP_bbcbox($context['TPortal']['editor_id']);

			echo '
				<hr>
				<dl class="settings">
					<dt>
						<label for="tp_article_useintro">', $txt['tp-useintro'], '</label>
					</dt>
					<dd>
						<input name="tp_article_useintro" type="radio" value="1">'.$txt['tp-yes'].'
						<input name="tp_article_useintro" type="radio" value="0" checked> '.$txt['tp-no'].'<br>
					</dd>
				</dl>
				<div id="tp_article_show_intro" style="display:none;">' ,
                    '<div class="font-strong">' . $txt['tp-artintrotext']. '</div>';

			if($tp_use_wysiwyg > 0 && !isset($context['TPortal']['submitbbc']))
				TPwysiwyg('tp_article_intro', '', true,'qup_tp_article_intro', $tp_use_wysiwyg, false);

			elseif($tp_use_wysiwyg == 0 && !isset($context['TPortal']['submitbbc']))
				echo '
					<textarea name="tp_article_intro" id="tp_article_intro" wrap="auto"></textarea><br>';

			elseif(isset($context['TPortal']['submitbbc']))
				echo '<textarea name="tp_article_intro" id="tp_article_intro" wrap="auto"></textarea><br>';

				echo '
					</div>
				</div>
				<div style="padding:1%;">  <input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="send"></div>
				<input name="submittedarticle" type="hidden" value="', isset($context['TPortal']['submitbbc']) ? 'bbc' : 'html' , '">
				<input name="tp_article_date" type="hidden" value="',time(),'">
				<input name="newarticle" type="hidden" value="1">
				<input name="tp_article_approved" type="hidden" value="0">
				<input name="tp_article_category" type="hidden" value="">
				<input name="tp_article_frontpage" type="hidden" value="0">
				<input name="tp_article_frame" type="hidden" value="theme">
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

function template_updatelog()
{
	global $context;

	echo '<div class="tborder">' . $context['TPortal']['updatelog'] , '<hr /></div>';
}


?>
