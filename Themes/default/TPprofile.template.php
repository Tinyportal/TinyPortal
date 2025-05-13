<?php
/**
 * @package TinyPortal
 * @version 3.0.3
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
	<div class="cat_bar"><h3 class="catbg">' . $txt['tpsummary'] . '</h3></div>
	<div id="tp_summary" class="windowbg">
		<div>
			' . $txt['tp-prof_allarticles'] . ' <b>' . $context['TPortal']['tpsummary']['articles'] . '</b>
		</div>
		<div>
			' . $txt['tp-prof_alldownloads'] . ' <b>' . $context['TPortal']['tpsummary']['uploads'] . '</b>
		</div>
	</div><br>';
}

// Articles Page
function template_tp_articles()
{
	global $settings, $txt, $context, $scripturl, $user_info;

	if ($context['TPortal']['profile_action'] == '') {
		echo '
	<div>
		<div id="tp_profile_articles">
			<div class="windowbg">
				' . $txt['tp-prof_allarticles'] . ' <b>' . $context['TPortal']['all_articles'] . '</b><br>';

		if ($context['TPortal']['all_articles'] > 0) {
			if ($context['TPortal']['approved_articles'] > 0) {
				echo '
				' . $txt['tp-prof_waitapproval1'] . ' <b>' . $context['TPortal']['approved_articles'] . '</b> ' . $txt['tp-prof_waitapproval2'] . '<br>';
			}

			if ($context['TPortal']['off_articles'] == 0) {
				echo '
				' . $txt['tp-prof_offarticles2'] . '<br>';
			}
			else {
				echo '
					' . $txt['tp-prof_offarticles'] . ' <b>' . $context['TPortal']['off_articles'] . '</b><br>';
			}
		}
		echo '
		</div>';

		if (isset($context['TPortal']['profile_articles']) && sizeof($context['TPortal']['profile_articles']) > 0) {

		if (!empty($context['TPortal']['pageindex'])) {
		echo '
			<div class="pagesection">
				<div class="pagelinks floatleft">
					<a href="#bot" class="button">', $txt['go_down'], '</a>
					' . $context['TPortal']['pageindex'] . '
				</div>
			</div>';
		}
		echo '
	<table class="table_grid">
		<thead>
			<tr class="title_bar">
			<th scope="col">
				<div class="tp_admflexbox">
					<div class="tp_admfirst">
						<div class="tp_name tpleft">', $context['TPortal']['tpsort'] == 'subject' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_up.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';area=tparticles;tpsort=subject">' . $txt['tp-arttitle'] . '</a></div>
						<div class="tp_date title-admin-area tpleft">', ($context['TPortal']['tpsort'] == 'date' || $context['TPortal']['tpsort'] == '') ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';area=tparticles;tpsort=date">' . $txt['date'] . '</a></div>
						<div class="tp_counter title-admin-area tpcenter">', $context['TPortal']['tpsort'] == 'views' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';area=tparticles;tpsort=views">' . $txt['views'] . '</a></div>
						<div class="tp_counter title-admin-area">', $context['TPortal']['tpsort'] == 'comments' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';area=tparticles;tpsort=comments">' . $txt['tp-comments'] . '</a></div>
						<div class="tp_rating title-admin-area tpleft">' . $txt['tp-ratings'] . '</div>
					</div>
				</div>
			</th>
			</tr>
		</thead>
		<tbody>';
			foreach ($context['TPortal']['profile_articles'] as $art) {
				echo '
			<tr class="windowbg">
			<td>
				<div class="tp_admflexbox">
					<div class="tp_admfirst">
						<div class="tp_name">
							', $art['off'] == 1 ? '<img src="' . $settings['tp_images_url'] . '/TPactive1.png" title="' . $txt['tp-noton'] . '" alt="*" />&nbsp; ' : '' , '', $art['approved'] == 0 ? '<img src="' . $settings['tp_images_url'] . '/TPthumbdown.png" title="' . $txt['tp-notapproved'] . '" alt="*" />&nbsp; ' : '' , '';
				if (($art['approved'] == 0) || ($art['off'] == 1)) {
					echo '
						' ,$art['subject'], '';
				}
				else {
					echo '
						<a href="' . $art['href'] . '" target="_blank">' ,$art['subject'], '</a>';
				}
				echo '
						</div>
					</div>
					<div class="tp_admlast">
						<a href="" class="clickme">' . $txt['tp-more'] . '</a>
						<div class="box">
							<div class="tp_date fullwidth-on-res-layout float-items">
								<div class="show-on-responsive">', ($context['TPortal']['tpsort'] == 'date' || $context['TPortal']['tpsort'] == '') ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';area=tparticles;tpsort=date">' . $txt['date'] . '</a></div>
								<div class="size-on-responsive">',$art['date'],'</div>
							</div>
							<div class="tp_counter fullwidth-on-res-layout float-items tpcenter">
								<div class="show-on-responsive">', $context['TPortal']['tpsort'] == 'views' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';area=tparticles;tpsort=views">' . $txt['views'] . '</a></div>
								',$art['views'],'
							</div>
							<div class="tp_counter fullwidth-on-res-layout float-items tpcenter">
								<div class="show-on-responsive">', $context['TPortal']['tpsort'] == 'comments' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';area=tparticles;tpsort=comments">' . $txt['tp-comments'] . '</a></div>
								',$art['comments'],'
							</div>
							<div class="tp_rating fullwidth-on-res-layout float-items">';
				if ($art['rating_votes'] > 0) {
					echo '
								<div class="show-on-responsive">' . $txt['tp-ratings'] . '</div>
								<div style="display:inline-block;"' . ($art['rating_votes'] > 0 ? $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' . $settings['tp_images_url'] . '/TPblue.png" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $art['rating_average'])) : $art['rating_average']) : '') . '<br>' . $txt['tp-ratingvotes'] . ' ' . $art['rating_votes'] . '</div>';
				}
				echo '
							</div>
						</div>
					</div>
			</td>
			</tr>';
			}
		echo '
		</tbody>
	</table>';
			if (!empty($context['TPortal']['pageindex'])) {
				echo '
				<div class="pagesection">
					<div class="pagelinks floatleft">
							<a href="#top" class="button" id="bot">', $txt['go_up'], '</a>
							' . $context['TPortal']['pageindex'] . '
					</div>
				</div>';
			}
		}

		echo '
		</div>
		<script>
			$(document).ready( function() {
				var $clickme = $(".clickme"),
					$box = $(".box");
				if ($box) {
					$box.hide();
				}
				if ($clickme) {
					$clickme.click( function(e) {
						$(this).text(($(this).text() === "' . $txt['tp-hide'] . '" ? "' . $txt['tp-more'] . '" : "' . $txt['tp-hide'] . '")).next(".box").slideToggle();
						e.preventDefault();
					});
				}
			});
		</script>
	</div>';
	}
	// Articles Settings Page
	elseif ($context['TPortal']['profile_action'] == 'settings') {
		echo '
	<div></div>
	<div id="tp_profile_articles_settings" class="windowbg">
		<form name="TPadmin3" action="' . $scripturl . '?action=tportal;sa=savesettings" method="post">
			<input type="hidden" name="sc" value="', $context['session_id'], '" />
			<input type="hidden" name="memberid" value="', $context['TPortal']['selected_member'], '" />
			<input type="hidden" name="item" value="', $context['TPortal']['selected_member_choice_id'], '" />';

		if ((!empty($context['TPortal']['allow_wysiwyg']) && ($user_info['id'] == $context['TPortal']['selected_member'])) || allowedTo('profile_view_any')) {
			echo '
			<div>
				<dl class="tp_title settings">
					<dt><strong>' . $txt['tp-wysiwygchoice'] . ':</strong>
					</dt>
					<dd><fieldset>
						<input type="radio" name="tpwysiwyg" value="2" ' , $context['TPortal']['selected_member_choice'] == '2' ? 'checked' : '' , '> ' . $txt['tp-yes'] . '<br>
						<input type="radio" name="tpwysiwyg" value="0" ' , ($context['TPortal']['selected_member_choice'] == '0' || $context['TPortal']['selected_member_choice'] == '1') ? 'checked' : '' , '> ' . $txt['tp-no'] . '<br>
						</fieldset>
					</dd>
				</dl>';
			if (($user_info['id'] == $context['TPortal']['selected_member']) || allowedTo('admin_forum')) {
				echo '
				<input type="submit" class="button" value="' . $txt['tp-send'] . '" name="send">';
			}
			echo '
			</div>';
		}
		echo '
		</form>
	</div><br>';
	}
}

// Uploaded Files Page
function template_tp_download()
{
	global $settings, $txt, $context, $scripturl;

	echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['downloadsprofile'] . '</h3></div>
		<p class="information">' . $txt['downloadsprofile2'] . '</p>
		<div id="tp_profile_uploaded">
			<div class="windowbg">
				' . $txt['tp-prof_alldownloads'] . ' <b>' . $context['TPortal']['all_downloads'] . '</b><br>';
	if ($context['TPortal']['approved_downloads'] > 0) {
		echo '
				' . $txt['tp-prof_approvdownloads'] . ' <b>' . $context['TPortal']['approved_downloads'] . '</b><br>';
	}
	echo '
			</div>';

	if (isset($context['TPortal']['profile_uploads']) && sizeof($context['TPortal']['profile_uploads']) > 0) {
		
		if (!empty($context['TPortal']['pageindex'])) {
		echo '
			<div class="pagesection">
				<div class="pagelinks floatleft">
					<a href="#bot" class="button">', $txt['go_down'], '</a>
					' . $context['TPortal']['pageindex'] . '
				</div>
			</div>';
		}
		echo '
		<table class="table_grid">
			<thead>
				<tr class="title_bar">
				<th scope="col">
				<div class="tp_admflexbox">
					<div class="tp_name float-items tpleft">', $context['TPortal']['tpsort'] == 'name' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_up.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;area=tpdownload;u=' . $context['TPortal']['memID'] . ';tpsort=name">' . $txt['subject'] . '</a></div>
					<div class="tp_date title-admin-area float-items tpleft">', ($context['TPortal']['tpsort'] == 'created' || $context['TPortal']['tpsort'] == '') ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;area=tpdownload;u=' . $context['TPortal']['memID'] . ';tpsort=created">' . $txt['date'] . '</a></div>
					<div class="tp_counter title-admin-area float-items">', $context['TPortal']['tpsort'] == 'views' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;area=tpdownload;u=' . $context['TPortal']['memID'] . ';tpsort=views">' . $txt['views'] . '</a></div>
					<div class="tp_counter title-admin-area float-items">', $context['TPortal']['tpsort'] == 'downloads' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;area=tpdownload;u=' . $context['TPortal']['memID'] . ';tpsort=downloads">' . $txt['tp-downloads'] . '</a></div>
					<div class="tp_rating title-admin-area float-items">' . $txt['tp-ratings'] . '</div>
				</div>
				</th>
				</tr>
			</thead>
			<tbody>';

		foreach ($context['TPortal']['profile_uploads'] as $art) {
			echo '
				<tr class="windowbg">
				<td>
				<div class="tp_admflexbox">
					<div class="tp_admfirst">
						<div class="tp_name fullwidth-on-res-layout float-items">
						  <a href="' . $art['href'] . '" target="_blank">', $art['approved'] == 0 ? '(' : '' , $art['name'], $art['approved'] == 0 ? ')' : '' ,  '</a>
						</div>
					</div>
					<div class="tp_admlast">
						<a href="" class="clickme">' . $txt['tp-more'] . '</a>
						<div class="box">
							<div class="tp_date fullwidth-on-res-layout float-items">
								<div class="show-on-responsive">', ($context['TPortal']['tpsort'] == 'created' || $context['TPortal']['tpsort'] == '') ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';sa=tpdownloads;tpsort=created">' . $txt['date'] . '</a></div>
								<div class="size-on-responsive">',$art['created'],'</div>
							</div>
							<div class="tp_counter fullwidth-on-res-layout float-items tpcenter">
								<div class="show-on-responsive">', $context['TPortal']['tpsort'] == 'views' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';sa=tpdownloads;tpsort=views">' . $txt['views'] . '</a></div>
								',$art['views'],'
							</div>
							<div class="tp_counter fullwidth-on-res-layout float-items tpcenter">
								<div class="show-on-responsive" style="word-break:break-all;">', $context['TPortal']['tpsort'] == 'downloads' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="" /> ' : '' ,'<a href="' . $scripturl . '?action=profile;u=' . $context['TPortal']['memID'] . ';sa=tpdownloads;tpsort=downloads">' . $txt['tp-downloads'] . '</a></div>
								',$art['downloads'],'
							</div>
							<div class="tp_rating fullwidth-on-res-layout float-items">
								<div class="show-on-responsive">' . $txt['tp-ratings'] . '</div>';
			if ($art['rating_votes'] > 0) {
				echo '
									<div style="display: inline-block;">' . $txt['tp-ratingaverage'] . ' ' . ($context['TPortal']['showstars'] ? (str_repeat('<img src="' . $settings['tp_images_url'] . '/TPblue.png" style="width: .7em; height: .7em; margin-right: 2px;" alt="" />', $art['rating_average'])) : $art['rating_average']) . '<br>' . $txt['tp-ratingvotes'] . ' ' . $art['rating_votes'] . '</div>';
			}
			echo '
								</div>
							<p class="clearthefloat"></p>
						</div>
					</div>
				</td>
				</tr>';
		}
		echo '
			</tbody>
		</table>';
			if (!empty($context['TPortal']['pageindex'])) {
				echo '
				<div class="pagesection">
					<div class="pagelinks floatleft">
							<a href="#top" class="button" id="bot">', $txt['go_up'], '</a>
							' . $context['TPortal']['pageindex'] . '
					</div>
				</div>';
			}
		echo '
		<script>
			$(document).ready( function() {
				var $clickme = $(".clickme"),
					$box = $(".box");
				if ($box) {
					$box.hide();
				}
				if ($clickme) {
					$clickme.click( function(e) {
						$(this).text(($(this).text() === "' . $txt['tp-hide'] . '" ? "' . $txt['tp-more'] . '" : "' . $txt['tp-hide'] . '")).next(".box").slideToggle();
						e.preventDefault();
					});
				}
			});
		</script>
	</div>';
	}
}
