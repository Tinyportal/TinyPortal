	<?php
/**
 * @package TinyPortal
 * @version 3.0.1
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

// Settings page
// Admin page
// Files in category page
// Submissions page
// FTP page
// Edit category page
// Add category page

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl;

	echo '
	<div>
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
	// setup the screen
	echo '
	<div id="tpadmin">
		<form accept-charset="', $context['character_set'], '"  name="dl_admin" action="' . $scripturl . '?action=tportal;sa=download;dl=admin" enctype="multipart/form-data" method="post" onsubmit="submitonce(this);">	';

	// Settings page
	if ($context['TPortal']['dlsub'] == 'adminsettings') {
		echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-dlsettings'] . '</h3></div>
		<div id="dlsettings">
			<div class="windowbg noup">
					<dl class="tp_title settings">
					<dt>
						<label for="tp_show_download_on">',$txt['tp-showdownload'], '</label>
					</dt>
					<dd>
						<div class="switch-field">
							<input type="radio" class="switch-on" id="tp_show_download_on" name="tp_show_download" value="1" ', $context['TPortal']['show_download'] == '1' ? 'checked' : '' ,'>
							<label for="tp_show_download_on"> ' . $txt['tp-on'] . '</label>
							<input type="radio" class="switch-off" id="tp_show_download_off" name="tp_show_download" value="0" ', $context['TPortal']['show_download'] == '0' ? 'checked' : '' ,'>
							<label for="tp_show_download_off"> ' . $txt['tp-off'] . '</label>
						</div>
					</dd>
					<dt>
						<label for="tp_dl_allowed_types">' . $txt['tp-dlallowedtypes'] . '</label>
					</dt>
					<dd>
						<input type="text" id="tp_dl_allowed_types" name="tp_dl_allowed_types" value="' . $context['TPortal']['dl_allowed_types'] . '" size=60><br><br>
					</dd>
					<dt>
						<label for="tp_dluploadsize">' . $txt['tp-dlallowedsize'] . '</label>
					</dt>
					<dd>
						<input type="number" id="tp_dluploadsize" name="tp_dluploadsize" value="' . $context['TPortal']['dl_max_upload_size'] . '" size="10"> ' . $txt['tp-kb'] . '<br><br>
					</dd>
					<dt>
						' . $txt['tp-dlmustapprove'] . '
					</dt>
					<dd>
						<input type="radio" id="tp-approveyes" name="tp_dl_approveonly" value="1" ', $context['TPortal']['dl_approve'] == '1' ? 'checked' : '' ,'><label for="tp-approveyes"> ' . $txt['tp-approveyes'] . '<br>
						<input type="radio" id="tp-approveno" name="tp_dl_approveonly" value="0" ', $context['TPortal']['dl_approve'] == '0' ? 'checked' : '' ,'><label for="tp-approveno"> ' . $txt['tp-approveno'] . '<br><br>
					</dd>
					<dt>
						<a href="', $scripturl, '?action=helpadmin;help=tp-dlwysiwygdesc" onclick="return reqOverlayDiv(this.href);">
						<span class="tptooltip" title="', $txt['help'], '"></span></a>
						' . $txt['tp-dlwysiwyg'] . '
					</dt>
					<dd>
						<input type="radio" id="tp_dl_wysiwyg1" name="tp_dl_wysiwyg" value="" ', $context['TPortal']['dl_wysiwyg'] == '' ? 'checked' : '' ,'><label for="tp_dl_wysiwyg1"> ' . $txt['tp-no'] . '</label><br>
						<input type="radio" id="tp_dl_wysiwyg2" name="tp_dl_wysiwyg" value="html" ', $context['TPortal']['dl_wysiwyg'] == 'html' ? 'checked' : '' ,'><label for="tp_dl_wysiwyg2"> ' . $txt['tp-yes'] . ', HTML</label><br>
						<input type="radio" id="tp_dl_wysiwyg3" name="tp_dl_wysiwyg" value="bbc" ', $context['TPortal']['dl_wysiwyg'] == 'bbc' ? 'checked' : '' ,'><label for="tp_dl_wysiwyg3"> ' . $txt['tp-yes'] . ', BBC</label>
					</dd>
				</dl>
			<hr>
				<div>
					<div>
						<b>' . $txt['tp-dlintrotext'] . '</b>
					</div>';
		if ($context['TPortal']['dl_wysiwyg'] == 'html') {
			TPwysiwyg('tp_dl_introtext', $context['TPortal']['dl_introtext'], true, 'qup_tp_dl_introtext', isset($context['TPortal']['usersettings']['wysiwyg']) ? $context['TPortal']['usersettings']['wysiwyg'] : 0);
		}
		elseif ($context['TPortal']['dl_wysiwyg'] == 'bbc') {
			TP_bbcbox($context['TPortal']['editor_id']);
		}
		else {
			echo '<textarea id="tp_article_body" name="tp_dl_introtext" >' . $context['TPortal']['dl_introtext'] . '</textarea>';
		}
		echo '
				</div>
			<hr>
				<dl class="settings">
					<dt>
						<label for="tp_dl_fileprefix">' . $txt['tp-dluseformat'] . '</label>
					</dt>
					<dd>
						<input type="radio" id="tp_dl_fileprefix1" name="tp_dl_fileprefix" value="K" ', $context['TPortal']['dl_fileprefix'] == 'K' ? 'checked' : '' ,'><label for="tp_dl_fileprefix1">' . $txt['tp-kb'] . '</label>
						<input type="radio" id="tp_dl_fileprefix2" name="tp_dl_fileprefix" value="M" ', $context['TPortal']['dl_fileprefix'] == 'M' ? 'checked' : '' ,'><label for="tp_dl_fileprefix2">' . $txt['tp-mb'] . '</label>
						<input type="radio" id="tp_dl_fileprefix3" name="tp_dl_fileprefix" value="G" ', $context['TPortal']['dl_fileprefix'] == 'G' ? 'checked' : '' ,'><label for="tp_dl_fileprefix3">' . $txt['tp-gb'] . '</label><br><br>
					</dd>
					<dt>
						' . $txt['tp-dlusescreenshot'] . '
					</dt>
					<dd>
						<input type="radio" id="tp_dl_usescreenshotyes" name="tp_dl_usescreenshot" value="1" ', $context['TPortal']['dl_usescreenshot'] == '1' ? 'checked' : '' ,'><label for="tp_dl_usescreenshotyes">' . $txt['tp-yes'] . '</label>
						<input type="radio" id="tp_dl_usescreenshotno" name="tp_dl_usescreenshot" value="0" ', $context['TPortal']['dl_usescreenshot'] == '0' ? 'checked' : '' ,'><label for="tp_dl_usescreenshotno">' . $txt['tp-sayno'] . '</label><br><br>
					</dd>
					<dt>
						' . $txt['tp-dlscreenshotsize1'] . '
					</dt>
					<dd>
						<input type="number" name="tp_dl_screenshotsize0" value="' . $context['TPortal']['dl_screenshotsize'][0] . '" size="6" maxlength="3"> x <input type="number" name="tp_dl_screenshotsize1"value="' . $context['TPortal']['dl_screenshotsize'][1] . '" size="6" maxlength="3" > px<br><br>
					</dd>
					<dt>
						' . $txt['tp-dlscreenshotsize2'] . '
					</dt>
					<dd>
						<input type="number" name="tp_dl_screenshotsize2" value="' . $context['TPortal']['dl_screenshotsize'][2] . '" size="6" maxlength="3"> x <input type="number" name="tp_dl_screenshotsize3" value="' . $context['TPortal']['dl_screenshotsize'][3] . '" size="6" maxlength="3"> px<br><br>
					</dd>
					<dt>
						' . $txt['tp-dlcreatetopic'] . '
					</dt>
					<dd>
						<input type="radio" name="tp_dl_createtopic" value="1" ', $context['TPortal']['dl_createtopic'] == '1' ? 'checked' : '' ,'> ' . $txt['tp-yes'] . '&nbsp;&nbsp;
						<input type="radio" name="tp_dl_createtopic" value="0" ', $context['TPortal']['dl_createtopic'] == '0' ? 'checked' : '' ,'> ' . $txt['tp-no'] . '<br><br>
					</dd>
					<dt>
						' . $txt['tp-dlcreatetopicboards'] . '
					</dt>
					<dd>
						<div class="tp_largelist" id="dl_createboard" ' , in_array('dl_createboard', $context['tp_panels']) ? ' style="display: none;"' : '' , '>
						';
		$brds = explode(',', $context['TPortal']['dl_createtopic_boards']);
		foreach ($context['TPortal']['boards'] as $brd) {
			echo '<div class="perm"><input type="checkbox" value="' . $brd['id'] . '" name="tp_dlboards' . $brd['id'] . '" id="tp_dlboards' . $brd['id'] . '" ' , in_array($brd['id'], $brds) ? ' checked="checked"' : '' , ' /><label for="tp_dlboards' . $brd['id'] . '"> ' . $brd['name'] . '</label></div>';
		}

		echo '<br style="clear: both;" />
						</div><br>
					</dd>
				</dl>
			<hr>
				<dl class="settings">
					<dt>
						' . $txt['tp-dlusefeatured'] . '
					</dt>
					<dd>
						<input type="radio" name="tp_dl_showfeatured" value="1" ', $context['TPortal']['dl_showfeatured'] == '1' ? 'checked' : '' ,'> ' . $txt['tp-yes'] . '&nbsp;&nbsp;
						<input type="radio" name="tp_dl_showfeatured" value="0" ', $context['TPortal']['dl_showfeatured'] == '0' ? 'checked' : '' ,'> ' . $txt['tp-sayno'] . '<br><br>
					</dd>
					<dt>
						<label for="tp_dl_featured">' . $txt['tp-dlfeatured'] . '</label>
					</dt>
					<dd>
						<select size="1" name="tp_dl_featured" id="tp_dl_featured">';

		foreach ($context['TPortal']['all_dlitems'] as $item) {
			echo '<option value="' . $item['id'] . '"' , $context['TPortal']['dl_featured'] == $item['id'] ? ' selected="selected"' : '' , '>' . $item['name'] . '</option>';
		}

		echo '
					</select><br><br>
					</dd>
					<dt>
						' . $txt['tp-dluselatest'] . '
					</dt>
					<dd>
						<input type="radio" name="tp_dl_showrecent" value="1" ', $context['TPortal']['dl_showlatest'] == '1' ? 'checked' : '' ,'> ' . $txt['tp-yes'] . '&nbsp;&nbsp;
						<input type="radio" name="tp_dl_showrecent" value="0" ', $context['TPortal']['dl_showlatest'] == '0' ? 'checked' : '' ,'> ' . $txt['tp-sayno'] . '<br><br>
					</dd>
					<dt>
						' . $txt['tp-dlusestats'] . '
					</dt>
					<dd>
						<input type="radio" name="tp_dl_showstats" value="1" ', $context['TPortal']['dl_showstats'] == '1' ? 'checked' : '' ,'> ' . $txt['tp-yes'] . '&nbsp;&nbsp;
						<input type="radio" name="tp_dl_showstats" value="0" ', $context['TPortal']['dl_showstats'] == '0' ? 'checked' : '' ,'> ' . $txt['tp-sayno'] . '<br><br>
					</dd>
					<dt>
						' . $txt['tp-dlusecategorytext'] . '
					</dt>
					<dd>
						<input type="radio" name="tp_dl_showcategorytext" value="1" ', $context['TPortal']['dl_showcategorytext'] == '1' ? 'checked' : '' ,'> ' . $txt['tp-yes'] . '&nbsp;&nbsp;
						<input type="radio" name="tp_dl_showcategorytext" value="0" ', $context['TPortal']['dl_showcategorytext'] == '0' ? 'checked' : '' ,'> ' . $txt['tp-sayno'] . '<br><br>
					</dd>
					<dt>
						<label for="tp_dl_limit_length">', $txt['tp-dllimitlength'], '</label>
						</dt>
						<dd>
						  <input type="number" id="tp_dl_limit_length" name="tp_dl_limit_length"value="' ,$context['TPortal']['dl_limit_length'], '" style="width: 6em" maxlength="5" min="100"><br><br>
						</dd>
					<dt>
						' . $txt['tp-dlvisualoptions'] . '
					</dt>
					<dd>
						<input type="checkbox" id="tp_dl_visual_options1" name="tp_dl_visual_options1" value="left" ', isset($context['TPortal']['dl_left']) ? 'checked' : '' ,'><label for="tp_dl_visual_options1"> ' . $txt['tp-leftbar'] . '</label><br>
						<input type="checkbox" id="tp_dl_visual_options2" name="tp_dl_visual_options2" value="right" ', isset($context['TPortal']['dl_right']) ? 'checked' : '' ,'><label for="tp_dl_visual_options2"> ' . $txt['tp-rightbar'] . '</label><br>
						<input type="checkbox" id="tp_dl_visual_options4" name="tp_dl_visual_options4" value="top" ', isset($context['TPortal']['dl_top']) ? 'checked' : '' ,'><label for="tp_dl_visual_options4"> ' . $txt['tp-topbar'] . '</label><br>
						<input type="checkbox" id="tp_dl_visual_options3" name="tp_dl_visual_options3" value="center" ', isset($context['TPortal']['dl_center']) ? 'checked' : '' ,'><label for="tp_dl_visual_options3"> ' . $txt['tp-centerbar'] . '</label><br>
						<input type="checkbox" id="tp_dl_visual_options6" name="tp_dl_visual_options6" value="lower" ', isset($context['TPortal']['dl_lower']) ? 'checked' : '' ,'><label for="tp_dl_visual_options6"> ' . $txt['tp-lowerbar'] . '</label><br>
						<input type="checkbox" id="tp_dl_visual_options5" name="tp_dl_visual_options5" value="bottom" ', isset($context['TPortal']['dl_bottom']) ? 'checked' : '' ,'><label for="tp_dl_visual_options5"> ' . $txt['tp-bottombar'] . '</label><br>
						<input type="hidden" name="tp_dl_visual_options8" value="not"><br><br>
					</dd>
					<dt>
						<label for="tp_dltheme">',$txt['tp-chosentheme'],'</label>
					</dt>
					<dd>
						<select size="1" name="tp_dltheme" id="tp_dltheme">';
		echo '<option value="0" ', $context['TPortal']['dlmanager_theme'] == '0' ? 'selected' : '' ,'>' . $txt['tp-noneicon'] . '</option>';

		foreach ($context['TPthemes'] as $them) {
			echo '<option value="' . $them['id'] . '" ',$them['id'] == $context['TPortal']['dlmanager_theme'] ? 'selected' : '' ,'>' . $them['name'] . '</option>';
		}

		echo '
						</select><br><br>
					</dd>
				</dl>
				<input type="hidden" name="dlsettings" value="1" />
				<input type="submit" class="button" name="dlsend" value="' . $txt['tp-submit'] . '">
			</div>
		</div>';
	}
	// Admin page
	elseif ($context['TPortal']['dlsub'] == 'admin') {
		echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-dltabs4'] . ' - ' . $txt['tp-categories'] . '</h3></div>
		<div id="user-download">
			<p class="information">' , $txt['tp-helpdownload1'] , '</p>';
		echo '
			<table class="table_grid">
				<thead>
					<tr class="title_bar">
						<th scope="col">
						<div class="tp_admflexbox">
							<div class="tp_pos">' . $txt['tp-pos'] . '</div>
							<div class="tp_name">' . $txt['tp-dluploadcategory'] . '</div>
							<div class="tp_articleopts80 title-admin-area">' . $txt['tp-dlicon'] . '</div>
							<div class="tp_articleopts80 title-admin-area">' . $txt['tp-dlfiles'] . '</div>
							<div class="tp_articleopts80 title-admin-area">' . $txt['tp-dlsubmitted'] . '</div>
							<div class="tp_articleopts80 title-admin-area">' . $txt['tp-dledit'] . '</div>
						</div>
						</th>
					</tr>
				</thead>
				<tbody>';
		// output all the categories, sort after childs
		if (isset($context['TPortal']['admcats']) && $context['TPortal']['admcats'] > 0) {
			foreach ($context['TPortal']['admcats'] as $cat) {
				if ($cat['parent'] == 0) {
					echo '
					<tr class="windowbg">
					<td>
						<div class="tp_admflexbox">
							<div class="tp_admfirst">
								<div class="tp_pos">
									<input type="text" name="tp_dlcatpos' . $cat['id'] . '" value="' . $cat['pos'] . '" size="6">
								</div>
								<div class="tp_name float-items">
									<a href="' . $cat['href'] . '">' . $cat['name'] . '</a>
								</div>
							</div>
							<div class="tp_admlast">
								<a href="" class="clickme">' . $txt['tp-more'] . '</a>
								<div class="box">
									<div class="tp_articleopts80 fullwidth-on-res-layout float-items tpcenter">
										<div class="show-on-responsive">' . $txt['tp-dlicon'] . '</div>
										', !empty($cat['icon']) ? '<img src="' . $cat['icon'] . '" alt="" />' : '' ,'
									</div>
									<div class="tp_articleopts80 fullwidth-on-res-layout float-items tpcenter">
										<div class="show-on-responsive">' . $txt['tp-dlfiles'] . '</div>
										' . $cat['items'] . '
									</div>
									<div class="tp_articleopts80 fullwidth-on-res-layout float-items tpcenter">
										<div class="show-on-responsive">' . $txt['tp-dlsubmitted'] . '</div>
										' . $cat['submitted'] . '
									</div>
									<div class="tp_articleopts80 fullwidth-on-res-layout float-items tpcenter">
										<div class="show-on-responsive" style="word-break: break-all;">' . $txt['tp-dledit'] . '</div>
										<a href="',$scripturl, '?action=tportal;sa=download;dl=cat',$cat['id'],'"><img title="' . $txt['tp-dlviewcat'] . '" src="' . $settings['tp_images_url'] . '/TPfilter.png" alt="" /></a>&nbsp;
										<a href="' . $cat['href2'] . '"><img title="' . $txt['tp-edit'] . '" src="' . $settings['tp_images_url'] . '/TPconfig_sm.png" alt="' . $txt['tp-edit'] . '"  /></a>&nbsp;
										<a href="' . $cat['href3'] . '" onclick="javascript:return confirm(\'' . $txt['tp-confirmdelete'] . '\')"><img title="' . $txt['tp-dldelete'] . '" src="' . $settings['tp_images_url'] . '/TPdelete2.png" alt=""  /></a>
									</div>
									<p class="clearthefloat"></p>
								</div>
								</div>
							</div>
						</div>
					</td>
					</tr>';
				}
			}
		}
		else {
			echo '
					<tr class="windowbg">
					<td>
						<div class="float-items">' . $txt['tp-nocats'] . '</div>
					</td>
					</tr>';
		}
		echo '
					</tbody>
				</table>
			<div class="padding-div"><input type="submit" class="button" name="dlsend" value="' . $txt['tp-submit'] . '"></div>
		</div>';
	}
	// Files in category page
	elseif (substr($context['TPortal']['dlsub'], 0, 8) == 'admincat') {
		$mycat = substr($context['TPortal']['dlsub'], 8);
		$ccount = 0;
		if (isset($context['TPortal']['admcats'])) {
			foreach ($context['TPortal']['admcats'] as $list) {
				if ($list['parent'] == $mycat) {
					$ccount++;
				}
			}
		}
		// output any subcats
		echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-dltabs4'] . '</h3></div>
		<div id="any-subcats" class=>
			<p class="information">' , $txt['tp-helpdownload2'] , '</p>';
		if (isset($context['TPortal']['admcats']) && $ccount > 0) {
			echo '
			<div class="padding-div"><b>' . $txt['tp-childcategories'] . '</b></div>
			<table class="table_grid">
				<thead>
					<tr class="title_bar">
						<th scope="col">
						<div class="tp_admflexbox">
						<div class="tp_admfirst">
							<div class="tp_pos">' . $txt['tp-pos'] . '</div>
							<div class="tp_name float-items">' . $txt['tp-dluploadcategory'] . '</div>
							<div class="tp_articleopts80 float-items title-admin-area">' . $txt['tp-dlicon'] . '</div>
							<div class="tp_articleopts80 float-items title-admin-area">' . $txt['tp-dlfiles'] . '</div>
							<div class="tp_articleopts80 float-items title-admin-area">' . $txt['tp-dlsubmitted'] . '</div>
							<div class="tp_articleopts80 float-items title-admin-area">' . $txt['tp-dledit'] . '</div>
						</div>
						</div>
						</th>
					</tr>';
			foreach ($context['TPortal']['admcats'] as $cat) {
				if ($cat['parent'] == $mycat) {
					echo '
					<tr class="windowbg">
					<td>
						<div class="tp_admflexbox">
							<div class="tp_admfirst">
								<div class="tp_pos">
									<input type="text" name="tp_dlcatpos' . $cat['id'] . '" value="' . $cat['pos'] . '" size="6">
								</div>
								<div class="tp_name float-items">
									<a href="' . $cat['href'] . '">' . $cat['name'] . '</a>
								</div>
							</div>
							<div class="tp_admlast">
								<a href="" class="clickme">' . $txt['tp-more'] . '</a>
								<div class="box">
									<div class="tp_articleopts80 smalltext fullwidth-on-res-layout float-items tpcenter">
										<div class="show-on-responsive">' . $txt['tp-dlicon'] . '</div>
										', !empty($cat['icon']) ? '<img src="' . $cat['icon'] . '" alt="" />' : '' ,'
									</div>
									<div class="tp_articleopts80 fullwidth-on-res-layout float-items tpcenter">
										<div class="show-on-responsive">' . $txt['tp-dlfiles'] . '</div>
										' . $cat['items'] . '
									</div>
									<div class="tp_articleopts80 fullwidth-on-res-layout float-items tpcenter">
										<div class="show-on-responsive">' . $txt['tp-dlsubmitted'] . '</div>
										' . $cat['submitted'] . '
									</div>
									<div class="tp_articleopts80 smalltext fullwidth-on-res-layout float-items tpcenter">
										<div class="show-on-responsive" style="word-break: break-all;">' . $txt['tp-dledit'] . '</div>
										<a href="',$scripturl, '?action=tportal;sa=download;dl=cat',$cat['id'],'"><img title="' . $txt['tp-dlviewcat'] . '" src="' . $settings['tp_images_url'] . '/TPfilter.png" alt="" /></a>&nbsp;
										<a href="' . $cat['href2'] . '"><img title="' . $txt['tp-edit'] . '" src="' . $settings['tp_images_url'] . '/TPconfig_sm.png" alt="' . $txt['tp-edit'] . '"  /></a>&nbsp;
										<a href="' . $cat['href3'] . '" onclick="javascript:return confirm(\'' . $txt['tp-confirmdelete'] . '\')"><img title="' . $txt['tp-dldelete'] . '" src="' . $settings['tp_images_url'] . '/TPdelete2.png" alt=""  /></a>
									</div>
								</div>
							</div>
						</div>
					</td>
					</tr>';
				}
			}
			echo '
				</tbody>
			</table>
			<div class="padding-div"><input type="submit" class="button" name="dlsend" value="' . $txt['tp-submit'] . '"></div>
		</div>
		<p class="clearthefloat"></p>';
		}
		// output any subcats files
		echo '
		<div class="padding-div">
			<b>' . $txt['tp-dlfiles'] . '</b>';
		if (!empty($context['TPortal']['sortlinks'])) {
			echo '
			<div class="tp_dlsortlinks floatright">' . $context['TPortal']['sortlinks'] . '</div>';
		}
		echo '
		</div>';

		if (isset($context['TPortal']['dl_admitems']) && count($context['TPortal']['dl_admitems']) > 0) {
			echo '
		<table class="table_grid">
		<thead>
			<tr class="title_bar">
				<th scope="col">
				<div class="tp_admflexbox">
					<div class="tp_admfirst">
						<div class="tp_dlicon">' . $txt['tp-dlicon'] . '</div>
						<div class="tp_name tpleft">' . $txt['tp-dlname'] . '</div>
					</div>
					<div>
						<div class="tp_counter float-items title-admin-area">' . $txt['tp-dlviews'] . '</div>
						<div class="tp_filename tpleft float-items title-admin-area">' . $txt['tp-dlfile'] . '</div>
						<div class="tp_filesize float-items title-admin-area">' . $txt['tp-dlfilesize'] . '</div>
						<div class="tp_pos float-items title-admin-area"></div>
						<p class="clearthefloat"></p>
					</div>
				</div>
				</th>
			</tr>
		</thead>
		<tbody>';
			foreach ($context['TPortal']['dl_admitems'] as $cat) {
				echo '
			<tr class="windowbg">
			<td>
				<div class="tp_admflexbox">
					<div class="tp_admfirst">
						<div class="tp_dlicon">
							' , ($cat['icon'] != '' && strpos($cat['icon'], 'blank.gif') == false) ? '<img src="' . $cat['icon'] . '" alt="' . $cat['name'] . '" />' : '<img class="dl_icon" src="' . $settings['tp_images_url'] . '/TPnodl.png" alt="' . $cat['name'] . '"  />' , '
						</div>
						<div class="tp_name float-items">
							<a href="' . $cat['href'] . '">' . $cat['name'] . '</a>
						</div>
					</div>
					<div class="tp_admlast">
						<a href="" class="clickme">' . $txt['tp-more'] . '</a>
						<div class="box">
							<div class="tp_counter fullwidth-on-res-layout float-items tpcenter">
								<div class="show-on-responsive">' . $txt['tp-dlviews'] . '</div>
								<div class="size-on-responsive">
									' . $cat['views'] . ' / ' . $cat['downloads'] . '
								</div>
							</div>
							<div class="tp_filename fullwidth-on-res-layout float-items">
								<div class="show-on-responsive">' . $txt['tp-dlfile'] . '</div>
								<div class="size-on-responsive">
									<div style="word-break:break-all;">
									' . (($cat['file'] == '- empty item -' || $cat['file'] == '') ? $txt['tp-noneicon'] : $cat['file']) . '
									</div>
									<div>
									' . $txt['tp-authorby'] . ' ' . $cat['author'] . '
									</div>
								</div>
							</div>
							<div class="tp_filesize fullwidth-on-res-layout float-items tpright">
								<div class="show-on-responsive">' . $txt['tp-dlfilesize'] . '</div>
								<div class="size-on-responsive">
								' . $cat['filesize'] . '' . $txt['tp-kb'] . '
								</div>
							</div>
							<div class="tp_pos fullwidth-on-res-layout float-items tpcenter">
								<div class="show-on-responsive">' . $txt['tp-dlpreview'] . '</div>
								<a href="',$scripturl, '?action=tportal;sa=download;dl=item',$cat['id'],'"><img title="' . $txt['tp-dlpreview'] . '" src="' . $settings['tp_images_url'] . '/TPfilter.png" alt="" /></a>
							</div>
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
		}
		else {
			echo '
				<div class="noticebox">' . $txt['tp-nofiles'] . '</div>';
		}

		echo '
	</div>
	<p class="clearthefloat"></p>';
	}
	// Edit file page
	elseif (substr($context['TPortal']['dlsub'], 0, 9) == 'adminitem') {
		if (isset($context['TPortal']['dl_admitems']) && count($context['TPortal']['dl_admitems']) > 0) {
			foreach ($context['TPortal']['dl_admitems'] as $cat) {
				echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-useredit'] . ' : ' . $cat['name'] . ' - <a href="' . $scripturl . '?action=tportal;sa=download;dl=item' . $cat['id'] . '">[' . $txt['tp-dlpreview'] . ']</a></h3></div>
		<div id="edit-up-item">
			<div class="windowbg noup">
				<dl class="tp_title settings">
					<dt>
						<label for="dladmin_name' . $cat['id'] . '"><b>' . $txt['tp-dluploadtitle'] . '</b></label>
					</dt>
					<dd>
						<input type="text" id="dladmin_name' . $cat['id'] . '" name="dladmin_name' . $cat['id'] . '" value="' . $cat['name'] . '" style="width: 92%;" required>
					</dd>
					<dt>
						<label for="dladmin_category"><b>' . $txt['tp-dluploadcategory'] . '</b>
					</dt>
					<dd>
						<select size="1" name="dladmin_category' . $cat['id'] . '" id="dladmin_category"> style="margin-top: 4px">';

				foreach ($context['TPortal']['admuploadcats'] as $ucats) {
					echo '
						<option value="' . $ucats['id'] . '" ', $ucats['id'] == abs($cat['category']) ? 'selected' : '' ,'>', (!empty($ucats['indent']) ? str_repeat('-', $ucats['indent']) : '') ,' ' . $ucats['name'] . '</option>';
				}
				echo '
						</select><br><br>
					</dd>
					<dt>
						' . $txt['tp-uploadedby'] . '
					</dt>
					<dd>
						' . $context['TPortal']['admcurrent']['member'] . '<br>
					</dd>
					<dt>
						' . $txt['tp-dlviews'] . '
					</dt>
					<dd>
						 ' . $cat['views'] . ' / ' . $cat['downloads'] . '<br>
					</dd>
				</dl>
				<hr>
				<div>
					<div><b>' . $txt['tp-dluploadtext'] . '</b></div>';

				if ($context['TPortal']['dl_wysiwyg'] == 'html') {
					TPwysiwyg('dladmin_text' . $cat['id'], $cat['description'], true, 'qup_dladmin_text', isset($context['TPortal']['usersettings']['wysiwyg']) ? $context['TPortal']['usersettings']['wysiwyg'] : 0);
				}
				elseif ($context['TPortal']['dl_wysiwyg'] == 'bbc') {
					TP_bbcbox($context['TPortal']['editor_id']);
				}
				else {
					echo '<textarea name="dladmin_text' . $cat['id'] . '" id="tp_article_body">' . $cat['description'] . '</textarea>';
				}
				echo '
				</div>
			<hr>
				<div class="padding-div tpcenter"><b><a href="' . $scripturl . '?action=tportal;sa=download;dl=get' . $cat['id'] . '">[' . $txt['tp-download'] . ']</a></b>
				</div><br>
			<dl class="settings">
				<dt>
					<label for="dladmin_file">' . $txt['tp-dlfilename'] . '</label>
				</dt>
				<dd>';
				if ($cat['file'] == '- empty item -' || $cat['file'] == '') {
					echo '
				<select size="1" name="dladmin_file' . $cat['id'] . '" id="dladmin_file">
					<option value="- empty item -">' . $txt['tp-noneicon'] . '</option>';

					foreach ($context['TPortal']['tp-downloads'] as $file) {
						echo '
			  		<option value="' . $file['file'] . '">' . $file['file'] . ' - ' . $file['size'] . '' . $txt['tp-kb'] . '</option>';
					}
					echo '
				</select>';
				}
				else {
					echo '
				<input type="text" name="dladmin_file' . $cat['id'] . '" id="dladmin_file" value="' . $cat['file'] . '" style="margin-bottom: 0.5em" size="50">';
				}

				echo '
				</dd>
				<dt>
					' . $txt['tp-dlfilesize'] . '</dt>
				<dd>
					' . $cat['filesize'] . '' . $txt['tp-kb'] . '
				</dd>
				<dt>
					<label for="tp_dluploadfile_edit">' . $txt['tp-uploadnewfileexisting'] . '</label>
				</dt>
				<dd>
					<input type="file" id="tp_dluploadfile_edit" name="tp_dluploadfile_edit" value="">
					<input type="hidden" name="tp_dluploadfile_editID" value="' . $cat['id'] . '"><br>
				</dd>
			</dl>
				<hr>
				<dl class="settings">
					<dt>
						<label for="dladmin_icon">' . $txt['tp-dluploadicon'] . '</label>
					</dt>
					<dd>
						<select size="1" name="dladmin_icon' . $cat['id'] . '" id="dladmin_icon" onchange="dlcheck(this.value)">';

				echo '
						<option value="blank.gif">' . $txt['tp-noneicon'] . '</option>';

				// output the icons
				$selicon = substr($cat['icon'], strrpos($cat['icon'], '/') + 1);
				foreach ($context['TPortal']['dlicons'] as $dlicon => $value) {
					echo '
						<option ' , ($selicon == $value) ? 'selected="selected" ' : '', 'value="' . $value . '">' . $value . '</option>';
				}

				echo '
						</select>
						<img style="margin-left: 2ex;vertical-align:top" name="dlicon" src="' . $cat['icon'] . '" alt="" />
					<script type="text/javascript">
					function dlcheck(icon)
						{
							document.dlicon.src= "' . $boardurl . '/tp-downloads/icons/" + icon
						}
					</script><br>
					</dd>
				</dl>
				<dl class="settings">
					<dt>
						<label for="tp_dluploadpic_link">' . $txt['tp-uploadnewpicexisting'] . '</label>
					</dt>
					<dd>
						<input type="text" id="tp_dluploadpic_link" name="tp_dluploadpic_link" value="' . $cat['screenshot'] . '" size="50">
					</dd>
					<dd>
						<div class="padding-div">' , $cat['sshot'] != '' ? '<img style="max-width:95%;" src="' . $cat['sshot'] . '" alt="" />' : '' , '</div>
					</dd>
				</dl>
				<dl class="settings">
					<dt>
						<label for="tp_dluploadpic_edit">' . $txt['tp-uploadnewpic'] . '</label>
					</dt>
					<dd>
						<input type="file" id="tp_dluploadpic_edit" name="tp_dluploadpic_edit" value="">
						<input type="hidden" name="tp_dluploadpic_editID" value="' . $cat['id'] . '"><br>
					</dd>
				</dl>
				' , $cat['approved'] == '0' ? '
				<dl class="settings">
					<dt>
						<label for="dl_admin_approve"><b> ' . $txt['tp-dlapprove'] . '</b></label>
					</dt>
					<dd>
						<input type="checkbox"  id="dl_admin_approve" name="dl_admin_approve' . $cat['id'] . '" value="ON" style="vertical-align: middle;">&nbsp;&nbsp;<img title="' . $txt['tp-approve'] . '" src="' . $settings['tp_images_url'] . '/TPthumbup.png" alt="' . $txt['tp-dlapprove'] . '"  />
					</dd>
				</dl>' : '' , '
				<hr>
			<dl class="settings">';
			}
		}
		// any extra files?
		if (isset($cat['subitem']) && sizeof($cat['subitem']) > 0) {
			echo '
					<dt>
						<label for="dladmin_delete">' . $txt['tp-dlmorefiles'] . '</label>
					</dt>
					<dd>';
			foreach ($cat['subitem'] as $sub) {
				echo '<div><b><a href="' , $sub['href'], '">' , $sub['name'] , '</a></b><br>(',$sub['file'],')
						', $sub['filesize'] ,'<br><input type="checkbox" id="dladmin_delete" name="dladmin_delete' . $sub['id'] . '" value="ON" onclick="javascript:return confirm(\'' . $txt['tp-confirm'] . '\')"> ' . $txt['tp-dldelete'] . '
						&nbsp;&nbsp;<input type="checkbox" name="dladmin_subitem' . $sub['id'] . '" value="0"> ' . $txt['tp-dlattachloose'] . '
						<br></div>';
			}
			echo '
					</dd>';
		}
		// no, but maybe it can be a additional file itself?
		else {
			echo '
					<dt>
						<label for="dladmin_subitem">' . $txt['tp-dlmorefiles2'] . '</label>
					</dt>
					<dd>
						<select size="1" name="dladmin_subitem' . $cat['id'] . '" id="dladmin_subitem" style="margin-top: 4px;">
						<option value="0" selected>' . $txt['tp-no'] . '</option>';

			foreach ($context['TPortal']['admitems'] as $subs) {
				echo '
						<option value="' . $subs['id'] . '">' . $txt['tp-yes'] . ', ' . $subs['name'] . '</option>';
			}
			echo '
						</select><br>
					</dd>';
		}
		echo '
				</dl>
				<hr>
				<dl class="settings">
					<dt>
						<label for="dladmin_delete"><b>' . $txt['tp-dldelete'] . '</b></label><br>
						<span class="smalltext">', $txt['tp-articledeletedesc'], '</span>
					</dt>
					<dd>
						<input type="checkbox" id="dladmin_delete" name="dladmin_delete' . $cat['id'] . '" value="ON" onclick="javascript:return confirm(\'' . $txt['tp-confirm'] . '\')">&nbsp;&nbsp;<img title="' . $txt['tp-dldelete'] . '" border="0" src="' . $settings['tp_images_url'] . '/TPdelete2.png" alt="' . $txt['tp-dldelete'] . '"  />
					</dd>
				</dl>
				<input type="submit" class="button" name="dlsend" value="' . $txt['tp-submit'] . '">
			</div>';
	}
	// Submissions page
	elseif ($context['TPortal']['dlsub'] == 'adminsubmission') {
		echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-dlsubmissions'] . '</h3></div>
		<p class="information">' , $txt['tp-helpdlsubmissions'] , '</p>
		<div id="any-submitted">';
		if (isset($context['TPortal']['dl_admitems']) && count($context['TPortal']['dl_admitems']) > 0) {
			echo '
			<table class="table_grid tp_admin">
			<thead>
				<tr class="title_bar">
					<th scope="col">
					<div class="tp_admflexbox">
						<div class="tp_name float-items tpleft">' . $txt['tp-dlname'] . '</div>
						<div class="tp_filename title-admin-area float-items tpleft">' . $txt['tp-dlfilename'] . '</div>
						<div class="tp_date title-admin-area float-items">' . $txt['tp-created'] . '</div>
						<div class="tp_author title-admin-area float-items">' . $txt['tp-uploadedby'] . '</div>
						<div class="tp_filesize title-admin-area float-items">' . $txt['tp-dlfilesize'] . '</strong></div>
						<p class="clearthefloat"></p>
					</div>
					</th>
				</tr>
			</thead>
			<tbody>';
			foreach ($context['TPortal']['dl_admitems'] as $cat) {
				echo '
				<tr class="windowbg">
				<td>
				<div class="tp_admflexbox">
					<div class="tp_admfirst">
						<div class="tp_name float-items">
							<a href="' . $cat['href'] . '">' . $cat['name'] . '</a>
						</div>
					</div>
					<div class="tp_admlast">
						<a href="" class="clickme">' . $txt['tp-more'] . '</a>
						<div class="box">
							<div class="tp_filename fullwidth-on-res-layout float-items">
								<div class="show-on-responsive">' . $txt['tp-dlfilename'] . '</div>
								<div class="size-on-responsive" style="word-break:break-all;">' . $cat['file'] . '</div>
							</div>
							<div class="tp_date fullwidth-on-res-layout float-items">
								<div class="show-on-responsive">' . $txt['tp-created'] . '</div>
								<div class="size-on-responsive">' . $cat['date'] . '</div>
							</div>
							<div class="tp_author fullwidth-on-res-layout float-items">
								<div class="show-on-responsive">' . $txt['tp-uploadedby'] . '</div>
								' . $cat['author'] . '
							</div>
							<div class="tp_filesize fullwidth-on-res-layout float-items tpcenter">
								<div class="show-on-responsive">' . $txt['tp-dlfilesize'] . '</div>
								' . $cat['filesize'] . '' . $txt['tp-kb'] . '
							</div>
						<p class="clearthefloat"></p>
						</div>
					</div>
				</div>
				</td>
				</tr>';
			}
		echo '
			</tbody>
			</table>';
		}
		else {
			echo '
				<div class="infobox">' . $txt['tp-nosubmissions'] . '</div>';
		}
		echo '
		</div>';
	}
	// FTP page
	elseif ($context['TPortal']['dlsub'] == 'adminftp') {
		echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-dlftp'] . '</h3></div>
		<p class="information">' . $txt['tp-assignftp'] . '</p>
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-ftpstrays'] . '</h3></div>
		<div id="ftp-files" class="windowbg noup">
			<dl class="tp_title settings">
				<dt>
					<a href="', $scripturl, '?action=helpadmin;help=tp-ftpfolderdesc" onclick="return reqOverlayDiv(this.href);">
					<span class="tptooltip" title="', $txt['help'], '"></span></a>' . $txt['tp-ftpfolder'] . '
				</dt>
				<dd>
					' . $context['TPortal']['download_upload_path'] . '
				</dd>
			</dl>';

		if (!empty($_GET['ftpcat'])) {
			// alert or information processsing multiple files
			if ($_GET['ftpcat'] === 'nocat') {
				echo '
				<div class="errorbox">' . $txt['tp-adminftp_nonewfiles'] . '</div>';
			}
			else {
				echo '
				<div class="infobox">' . $txt['tp-adminftp_newfiles'] . '<a href="' . $scripturl . '?action=tportal;sa=download;dl=admincat' . $_GET['ftpcat'] . '">' . $txt['tp-adminftp_newfilescat'] . '</a></div>';
			}
		}
		if (!empty($_GET['ftpitem'])) {
			// alert or information processing a single file
			if ($_GET['ftpitem'] === 'noitem') {
				echo '
				<div class="errorbox">' . $txt['tp-adminftp_nonewfiles'] . '</div>';
			}
			else {
				echo '
				<div class="infobox">' . $txt['tp-adminftp_newfile'] . '<a href="' . $scripturl . '?action=tportal;sa=download;dl=adminitem' . $_GET['ftpitem'] . '">' . $txt['tp-adminftp_newfileview'] . '</a></div>';
			}
		}
		$ccount = 0;
		echo '
				<div class="tp_largelist2">';
		foreach ($context['TPortal']['tp-downloads'] as $file) {
			if (!in_array($file['file'], $context['TPortal']['dl_allitems'])) {
				echo '
					<div><input type="checkbox" name="assign-ftp-checkbox' . $ccount . '" value="' . $file['file'] . '"> ' . substr($file['file'], 0, 40) . '', strlen($file['file']) > 40 ? '..' : '' , '  [' . $file['size'] . ' ' . $txt['tp-kb'] . ']  - <b><a href="' . $scripturl . '?action=tportal;sa=download;dl=upload;ftp=' . $file['file'] . '">' . $txt['tp-dlmakeitem'] . '</a></b></div>';
				$ccount++;
			}
		}
		if ($ccount == 0) {
			echo '
					<div class="padding-div">' . $txt['tp-noftpstrays'] . '</div>';
		}
		echo '
				</div>';
		if ($ccount > 0) {
			echo '
				<div class="padding-div"><input type="checkbox" id="toggleoptions" onclick="invertAll(this, this.form, \'assign-ftp-checkbox\');" /><label for="toggleoptions">', $txt['tp-checkall'], '</label></div>
				<hr>
				<dl class="tp_title settings">
					<dt>
						<select name="assign-ftp-cat">
						<option value="0">' . $txt['tp-createnew'] . '</option>';
			if (count($context['TPortal']['admuploadcats']) > 0) {
				foreach ($context['TPortal']['admuploadcats'] as $ucats) {
					echo '
						<option value="' . $ucats['id'] . '">', (!empty($ucats['indent']) ? str_repeat('-', $ucats['indent']) : '') ,' ' . $txt['tp-assigncatparent'] . $ucats['name'] . '</option>';
				}
			}
			else {
				echo '
					<option value="0">' . $txt['tp-none-'] . '</option>';
			}
			echo '
					</select>
				</dt>
				<dd>
					<input type="text" name="assign-ftp-newcat" placeholder= "' . $txt['tp-newcatassign'] . '" value="" size="40">
				</dd>
				<dt>
					<label for="tp_newdladmin_icon">' . $txt['tp-dluploadicon'] . '</label>
				</dt>
				<dd>
					<select size="1" name="tp_newdladmin_icon" id="tp_newdladmin_icon" onchange="dlcheck(this.value)">
					<option value="ftp.png">ftp.png</option>';

			// output the icons
			$selicon = 'ftp.png';
			foreach ($context['TPortal']['dlicons'] as $dlicon => $value) {
				echo '
						<option ' , ($selicon == $value) ? 'selected="selected" ' : '', 'value="' . $value . '">' . $value . '</option>';
			}
			echo '
					</select>
					<img style="margin-left: 2ex;vertical-align:top" name="dlicon" src="' . $settings['tp_images_url'] . '/' . $selicon . '" alt="" />
				<script type="text/javascript">
				function dlcheck(icon) {
						document.dlicon.src= "' . $boardurl . '/tp-downloads/icons/" + icon
				}
				</script><br>
				</dd>
			</dl>';
			echo '
				<input type="submit" class="button" name="ftpdlsend" value="' . $txt['tp-submitftp'] . '">
			</div>';
		}
	}
	// Edit category page
	elseif (substr($context['TPortal']['dlsub'], 0, 12) == 'admineditcat') {
		if (isset($context['TPortal']['admcats']) && count($context['TPortal']['admcats']) > 0) {
			foreach ($context['TPortal']['admcats'] as $cat) {
				echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-dlcatedit'] . '</h3></div>
		<div id="editupcat">
			<div class="windowbg noup">
				<dl class="tp_title settings">
					<dt>
						<label for="dladmin_name"><b>' . $txt['tp-dlname'] . '</b></label>
					</dt>
					<dd>
						<input type="text" id="dladmin_name"  name="dladmin_name' . $cat['id'] . '" value="' . $cat['name'] . '" size="50" style="max-width:92%;" required>
					</dd>
					<dt>
						<label for="dladmin_link"><b>' . $txt['tp-shortname'] . '</b></label>
					</dt>
					<dd>
						<input type="text" id="dladmin_link" name="dladmin_link' . $cat['id'] . '" value="' . $cat['shortname'] . '" size="20" pattern="[^\'\x22;:]+"><br><br>
					</dd>
					<dt>
						<label for="dladmin_parent">' . $txt['tp-dlparent'] . '</label>
					</dt>
					<dd>';
				// which parent category?
				echo '
					<select size="1" name="dladmin_parent' . $cat['id'] . '" id="dladmin_parent" style="margin-top: 4px;">
						<option value="0" ', $cat['parent'] == 0 ? 'selected' : '' ,'>' . $txt['tp-dlnocategory'] . '</option>';

				if (count($context['TPortal']['admuploadcats']) > 0) {
					foreach ($context['TPortal']['admuploadcats'] as $ucats) {
						if ($ucats['id'] != $cat['id']) {
							echo '
						<option value="' . $ucats['id'] . '" ', $ucats['id'] == $cat['parent'] ? 'selected' : '' ,'>', (!empty($ucats['indent']) ? str_repeat('-', $ucats['indent']) : '') ,' ' . $ucats['name'] . '</option>';
						}
					}
				}
				else {
					echo '
						<option value="0">' . $txt['tp-none-'] . '</option>';
				}
			}
			echo '
					</select><br>
					</dd>
					<dt>
						<label for="dladmin_icon">' . $txt['tp-icon'] . '</label>
					</dt>
					<dd>
						<div><select size="1" name="dladmin_icon' . $cat['id'] . '" id="dladmin_icon" onchange="dlcheck(this.value)">
						<option value="blank.gif" selected>' . $txt['tp-chooseicon'] . '</option>
						<option value="blank.gif">' . $txt['tp-noneicon'] . '</option>';

			// output the icons
			$selicon = substr($cat['icon'], strrpos($cat['icon'], '/') + 1);
			foreach ($context['TPortal']['dlicons'] as $dlicon => $value) {
				echo '
						<option ', ($selicon == $value) ? 'selected="selected" ' : '','value="' . $value . '">' . $value . '</option>';
			}

			echo '
					</select>
					<br><br><img name="dlicon" src="' . $cat['icon'] . '" alt="" />
					<script type="text/javascript">
						function dlcheck(icon)
						{
							document.dlicon.src= "' . $boardurl . '/tp-downloads/icons/" + icon
						}
					</script>
					<br><br>
						</div>
					</dd>
				</dl>
				<hr>
				<div class="padding-div"><b>' . $txt['tp-dluploadtext'] . ':</b><br>';

			if ($context['TPortal']['dl_wysiwyg'] == 'html') {
				TPwysiwyg('dladmin_text' . $cat['id'], html_entity_decode($cat['description'], ENT_QUOTES), true, 'qup_dladmin_text', isset($context['TPortal']['usersettings']['wysiwyg']) ? $context['TPortal']['usersettings']['wysiwyg'] : 0);
			}
			elseif ($context['TPortal']['dl_wysiwyg'] == 'bbc') {
				TP_bbcbox($context['TPortal']['editor_id']);
			}
			else {
				echo '<textarea name="dladmin_text' . $cat['id'] . '" id="tp_article_body">' . html_entity_decode($cat['description'], ENT_QUOTES) . '</textarea>';
			}

			echo '
				</div>
			<hr>
			<dl class="settings">
				<dt>
					<span class="font-strong">' . $txt['tp-dlaccess'] . '</span>
				</dt>
				<dd><div class="tp_largelist2">';
			// access groups
			// loop through and set membergroups
			if (!empty($cat['access'])) {
				$tg = explode(',', $cat['access']);
			}
			else {
				$tg = [];
			}

			foreach ($context['TPortal']['dlgroups'] as $mg) {
				if ($mg['posts'] == '-1' && $mg['id'] != '1') {
					echo '
					<input type="checkbox" id="dladmin_group' . $mg['id'] . '" name="dladmin_group' . $mg['id'] . '" value="' . $cat['id'] . '"';
					if (in_array($mg['id'], $tg)) {
						echo ' checked';
					}
					echo '><label for="dladmin_group' . $mg['id'] . '"> ' . $mg['name'] . ' </label><br>';
				}
			}
			// if none is chosen, have a control value
			echo '</div><br><input type="checkbox" id="tp-checkall" onclick="invertAll(this, this.form, \'dladmin_group\');" /><label for="tp-checkall">' . $txt['tp-checkall'] . '</label>
					<input type="hidden" name="dladmin_group-2" value="' . $cat['id'] . '">
				</dd>
			</dl>';
		}
		echo '
			<input type="submit" class="button" name="dlsend" value="' . $txt['tp-submit'] . '">
		</div>
	</div>';
	}
	elseif ($context['TPortal']['dlsub'] == 'adminaddcat') {
		// Add category page
		echo '
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-dlcatadd'] . '</h3></div>
		<div id="dl-addcat">
			<div class="windowbg noup">
				<dl class="tp_title settings">
					<dt>
						<label for="newdladmin_name"><b>' . $txt['tp-name'] . '</b></label>
					</dt>
					<dd>
						<input type="text" id="newdladmin_name" name="newdladmin_name" value="" size="50" style="max-width:92%;" required>
					</dd>
					<dt>
						<label for="newdladmin_link"><b>' . $txt['tp-shortname'] . '</b></label>
					</dt>
					<dd>
						<input type="text" id="newdladmin_link" name="newdladmin_link" value="" size="20" pattern="[^\'\x22;:]+"><br><br>
					</dd>
					<dt>
						<label for="newdladmin_parent">' . $txt['tp-dlparent'] . '</label>
					</dt>
					<dd>';
		// which parent category?
		echo '
					<select size="1" name="newdladmin_parent" id="newdladmin_parent" style="margin-top: 4px;">
						<option value="0" selected>' . $txt['tp-dlnocategory'] . '</option>';

		foreach ($context['TPortal']['admuploadcats'] as $ucats) {
			echo '
					<option value="' . $ucats['id'] . '">', (!empty($ucats['indent']) ? str_repeat('-', $ucats['indent']) : '') ,' ' . $ucats['name'] . '</option>';
		}
		echo '
					</select><br>
					</dd>
					<dt>
						<label for="newdladmin_icon" >' . $txt['tp-icon'] . '</label>
					</dt>
					<dd>
						<div><select size="1" name="newdladmin_icon" id="newdladmin_icon"  onchange="dlcheck(this.value)">';

		echo '
				<option value="blank.gif" selected>' . $txt['tp-noneicon'] . '</option>';

		// output the icons
		foreach ($context['TPortal']['dlicons'] as $dlicon => $value) {
			echo '
						<option value="' . $value . '">' . $value . '</option>';
		}

		echo '
					</select>
					<br><br><img name="dlicon" src="' . $boardurl . '/tp-downloads/icons/blank.gif" alt="" />
				<script type="text/javascript">
					function dlcheck(icon)
					{
						document.dlicon.src= "' . $boardurl . '/tp-downloads/icons/" + icon
					}
				</script>
				</div>
					</dd>
				</dl>
				<hr>
				<b>' . $txt['tp-dluploadtext'] . ':</b><br>';

		if ($context['TPortal']['dl_wysiwyg'] == 'html') {
			TPwysiwyg('newdladmin_text', '', true, 'qup_dladmin_text', isset($context['TPortal']['usersettings']['wysiwyg']) ? $context['TPortal']['usersettings']['wysiwyg'] : 0);
		}
		elseif ($context['TPortal']['dl_wysiwyg'] == 'bbc') {
			TP_bbcbox($context['TPortal']['editor_id']);
		}
		else {
			echo '<textarea name="newdladmin_text" id="tp_article_body"></textarea>';
		}
		echo '
			<hr>
				<dl class="settings">
					<dt>
						<span class="font-strong">' . $txt['tp-dlaccess'] . '</span>
					</dt>
					<dd>
						<div class="tp_largelist2">';
		// access groups
		// loop through and set membergroups
		if (!empty($cat['access'])) {
			$tg = explode(',', $cat['access']);
		}
		else {
			$tg = [];
		}

		foreach ($context['TPortal']['dlgroups'] as $mg) {
			if ($mg['posts'] == '-1' && $mg['id'] != '1') {
				echo '
					<input type="checkbox" id="newdladmin_group' . $mg['id'] . '" name="newdladmin_group' . $mg['id'] . '" value="1"';
				if (in_array($mg['id'], $tg)) {
					echo ' checked';
				}
				echo '><label for="newdladmin_group' . $mg['id'] . '"> ' . $mg['name'] . ' </label><br>';
			}
		}
		// if none is chosen, have a control value
		echo '		</div>
						<input type="checkbox" id="dladmin_group-2" onclick="invertAll(this, this.form, \'newdladmin_group\');" /><label for="dladmin_group-2">' . $txt['tp-checkall'] . '</label>
						<input type="hidden" name="dladmin_group-2" value="1">
					</dd>
				</dl>';

		echo '
				<input type="submit" class="button" name="newdlsend" value="' . $txt['tp-submit'] . '">
			</div>
		</div>';
	}
	echo '
		</form>
	</div>';
}

?>
