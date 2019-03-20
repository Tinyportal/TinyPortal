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

function template_main()
{
	global $context, $settings, $txt, $scripturl;

	if(isset($context['TPortal']['subaction'])){
		switch($context['TPortal']['subaction']){
			case 'editcomment':
				if(isset($context['TPortal']['comment_edit'])){
					echo '
		<form accept-charset="', $context['character_set'], '"  name="tp_edit_comment" action="'.$scripturl.'?action=tportal;sa=editcomment" method="post" style="margin: 1ex;">
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
		<form accept-charset="', $context['character_set'], '"  name="TPadmin3" action="' . $scripturl . '?action=tportal;sa=savearticle" method="post" enctype="multipart/form-data" onsubmit="submitonce(this);">
			<div id="users-editarticle" class="bordercolor users-area">
				<div class="cat_bar">
					<h3 class="catbg">'.$txt['tp-editarticle'].'&nbsp;' ,$mg['subject'], '&nbsp;-&nbsp;<a href="'.$scripturl.'?page='.$mg['id'].'">['.$txt['tp-preview'].']</a> </h3>
				</div><div></div>
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
		case 'showcomments':
		if(!empty($context['TPortal']['showall'])){
			echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-commentall'] . '</h3></div>
		<div></div>
			<div id="show-art-comm" class="windowbg padding-div">
			<table class="table_grid tp_grid" style="width:100%">
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col" class="comments">
					<div style="word-break:break-all;">
						<div align="left" class="float-items" style="width:30%;">' . $txt['tp-article'] . '</div>
						<div align="left" class="float-items" style="width:15%;">' . $txt['tp-author'] . '</div>
						<div align="left" class="float-items" style="width:30%;">' . $txt['tp-comments'] . '</div>
						<div align="left" class="float-items" style="width:25%;">' . $txt['by'] . '</div>
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
				' , ($mes['is_read']==0 && !TP_SMF21) ? ' <img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/new.gif" alt="" />' : '' , '
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
				<div align="right" style="padding:1%;"><a href="' . $scripturl . '?action=tportal;sa=showcomments">' . $txt['tp-showcomments'] . '</a></div>
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
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-commentnew'] . '</h3></div>
		<div></div>
			<div id="latest-art-comm" class="windowbg padding-div">
			<table class="table_grid tp_grid" style="width:100%">
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col" class="comments">
			<div>
				<div align="left" class="float-items" style="width:30%;">' . $txt['tp-article'] . '</div>
				<div align="left" class="float-items" style="width:15%;">' . $txt['tp-author'] . '</div>
				<div align="left" class="float-items" style="width:30%;">' . $txt['tp-comments'] . '</div>
				<div align="left" class="float-items" style="width:25%;">' . $txt['by'] . '</div>
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
				<div align="right" style="padding:1%;"><a href="' . $scripturl . '?action=tportal;sa=showcomments;showall">' . $txt['tp-showall'] . '</a></div>
			</td>
			</tr>';
			echo '
			</tbody>
		</table>
		<div class="tp_pad">'.$context['TPortal']['pageindex'].'</div>
		</div>';
	
		}
			break;
		case 'myarticles':
			echo '
        <div class="cat_bar">
            <h3 class="catbg">' .$txt['tp-myarticles'] . '</h3>
        </div>
		<div class="windowbg padding-div">
	<table class="table_grid tp_grid tp_grid" style="width:100%";>
		<thead>
			<tr class="title_bar titlebg2">
			<th scope="col" class="myarticles">
				<div class="font-strong" style="padding:0px;">
					<div align="center" class="float-items title-admin-area">', $context['TPortal']['tpsort']=='subject' ? '<img src="' .$settings['tp_images_url']. '/TPsort_up.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=tportal;sa=myarticles;tpsort=subject">'.$txt['subject'].'</a></div>
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
					<a href="' . $scripturl . '?action=tportal;sa=editarticle;article='.$art['id'].'"><img src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="*" /></a>';
				elseif(allowedTo('tp_articles'))
					echo '
					<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article='.$art['id'].'"><img src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="*" /></a>';

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
	<form style="clear: both;" accept-charset="', $context['character_set'], '" name="TPadmin3" action="' . $scripturl . '?action=tportal;sa=editarticle" method="post" enctype="multipart/form-data" onsubmit="submitonce(this);">
				<input name="TPadmin_submit" type="hidden" value="set">
				<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<div id="users-addarticle" class="bordercolor">
			<div class="cat_bar">
				<h3 class="catbg">' , (isset($context['TPortal']['submitbbc'])) ? $txt['tp-submitarticlebbc'] : $txt['tp-submitarticle'] , '</h3>
			</div><div></div>
			<div class="windowbg noup tp_pad">
				<div class="font-strong">'.$txt['tp-arttitle'].'</div>
				<input style="width: 92%;" name="tp_article_title" type="text" value="">
				<br><br>
				<div class="font-strong">'.$txt['tp-artbodytext'].' </div>
				<div>';

			$tp_use_wysiwyg = $context['TPortal']['show_wysiwyg'];

			if($tp_use_wysiwyg > 0 && !isset($context['TPortal']['submitbbc'])) {
				TPwysiwyg('tp_article_body', '', true,'qup_tp_article_body', $tp_use_wysiwyg);
            }
			elseif($tp_use_wysiwyg == 0 && !isset($context['TPortal']['submitbbc'])) {
				echo '
					<textarea name="tp_article_body" id="tp_article_body" wrap="auto"></textarea><br>';
            }
			elseif(isset($context['TPortal']['submitbbc'])) {
				TP_bbcbox($context['TPortal']['editor_id']);
            }

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

			if($tp_use_wysiwyg > 0 && !isset($context['TPortal']['submitbbc'])) {
				TPwysiwyg('tp_article_intro', '', true,'qup_tp_article_intro', $tp_use_wysiwyg, false);
            }
			elseif($tp_use_wysiwyg == 0 && !isset($context['TPortal']['submitbbc'])) {
				echo '
					<textarea name="tp_article_intro" id="tp_article_intro" wrap="auto"></textarea><br>';
            }
			elseif(isset($context['TPortal']['submitbbc'])) {
				echo '<textarea name="tp_article_intro" id="tp_article_intro" wrap="auto"></textarea><br>';
            }

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
