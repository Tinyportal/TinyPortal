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

// ** Sections ** (ordered like in the admin screen):
// TP Admin Main overview page
// General Settings page
// Frontpage Settings page
// Articles page
// Articles in category Page
// Edit article / Add article Page
// Article Categories page
// Add category Page
// Category List Page
// Edit Article Category Page
// Uncategorized articles Page
// Article Settings Page
// Article Submissions Page
// Illustrative article icons to be used in category layouts Page
// Panel Settings Page
// Block Settings Page
// Add Block Page
// Edit Block Page (including settings per block type)
// Block Access Page
// Menu Manager Page
// Menu Manager Page: single menus
// Add Menu / Add Menu item Page
// Edit menu item Page
// Modules Page

function getElementById($id,$url){

$html = new DOMDocument();
$html->loadHtmlFile($url); //Pull the contents at a URL, or file name

$xpath = new DOMXPath($html); // So we can use XPath...

return($xpath->query("//*[@id='$id']")->item(0)); // Return the first item in element matching our id.

}

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	echo '
	<div id="tpadmin" class="tborder">
        <div class="title_bar">
            <h3 class="titlebg">'.$txt['tp-tpadmin'].'</h3>
        </div>
		<div style="padding-top: 5px;">';

	$go = isset($context['TPortal']['subaction']) ? 'template_' . $context['TPortal']['subaction'] : '';

	if($go == 'template_blocks' && isset($_GET['latest'])) {
		$go = 'template_latestblocks';
		$param = 'html';
	}
	elseif($go == 'template_credits') {
		$go = 'template_tpcredits';
		$param = '';
	}
	elseif($go == 'template_categories' && !empty($_GET['cu']) && is_numeric($_GET['cu'])) {
		$go = 'template_editcategory';
		$param = '';
	}
	elseif($go == 'template_blocks' && isset($_GET['blockedit'])) {
		$go = 'template_blockedit';
		$param = '';
	}
	elseif($go == 'template_blocks' && isset($_GET['overview'])) {
		$go = 'template_blockoverview';
		$param = '';
	}
	elseif($go == 'template_blocks' && isset($_GET['addblock'])) {
		$go = 'template_addblock';
		$param = '';
	}
	else {
		$param = '';
    }

	call_user_func($go, $param);

	echo '
		</div><p class="clearthefloat"></p>
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

// TP Admin Main overview page
function template_overview()
{
	global $context, $settings, $txt, $boardurl;

	echo '
	<div id="tp_overview" class="windowbg">';

	if(is_array($context['admin_tabs']) && count($context['admin_tabs']) > 0 ) {
		echo '<ul>';
		foreach($context['admin_tabs'] as $ad => $tab) {
			$tabs = array();
			foreach($tab as $t => $tb) {
				echo '<li><a href="' . $tb['href'] . '"><img style="margin-bottom: 8px;" src="' . $settings['tp_images_url'] . '/TPov_' . strtolower($t) . '.png" alt="TPov_' . strtolower($t) . '" /><br><b>'.$tb['title'].'</b></a></li>';
            }
		}
		echo '</ul>';
	}
	echo '</div>';
}

// General Settings page
function template_settings()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language, $smcFunc;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="settings">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-generalsettings'] . '</h3></div>
		<div id="settings" class="admintable admin-area">
			<div class="information smalltext">' , $txt['tp-helpsettings'] , '</div><div></div>
				<div class="windowbg noup">
					<div class="formtable padding-div">
						<!-- START non responsive themes form -->
							<div>
						       <div class="font-strong">'.$txt['tp-formres'].'</div>';
						       $tm=explode(",",$context['TPortal']['resp']);
						   echo '<input name="tp_resp" id="tp_resp" type="checkbox" value="0"><label for="tp_resp">'.$txt['tp-deselectthemes'].'</label><br><br> ';
							foreach($context['TPallthem'] as $them) {
					              if(TP_SMF21) {
									echo '
										  <img class="theme_icon" alt="*" src="'.$them['path'].'/thumbnail.png" />
										  <input name="tp_resp'.$them['id'].'" id="tp_resp'.$them['id'].'" type="checkbox" value="'.$them['id'].'" ';
										}
								  else {
									echo '
										  <img class="theme_icon" alt="*" src="'.$them['path'].'/thumbnail.gif" />
										  <input name="tp_resp'.$them['id'].'" id="tp_resp'.$them['id'].'" type="checkbox" value="'.$them['id'].'" ';
										}
					              if(in_array($them['id'],$tm)) {
					                echo ' checked="checked" ';
					              }
					              echo '><label for="tp_resp'.$them['id'].'">'.$them['name'].'</label><br>';
						       }
						       echo'<br><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'">
					        </div>
						<!-- END non responsive themes form -->
							<br><hr>

				<dl class="settings">
					<dt>
						<label for="tp_frontpage_title">', $txt['tp-frontpagetitle'], '</label>
						<div class="smalltext">' , $txt['tp-frontpagetitle2'] , '</div>
					</dt>
					<dd>
						<input size="50" name="tp_frontpage_title" id="tp_frontpage_title"type="text" value="' , !empty($context['TPortal']['frontpage_title']) ? $context['TPortal']['frontpage_title'] : '' , '">
					</dd>
					<dt>
						', $txt['tp-redirectforum'], '
					</dt>
					<dd>
						<input name="tp_redirectforum" id="tp_redirectforum1" type="radio" value="1" ' , $context['TPortal']['redirectforum']=='1' ? 'checked' : '' , '><label for="tp_redirectforum1"> '.$txt['tp-redirectforum1'].'</label>
					</dd>
					<dd>
						<input name="tp_redirectforum" id="tp_redirectforum2" type="radio" value="0" ' , $context['TPortal']['redirectforum']=='0' ? 'checked' : '' , '><label for="tp_redirectforum2"> '.$txt['tp-redirectforum2'].'</label>
					</dd>
				</dl>
					<hr>
				<dl class="settings">
					<dt>
						<label for="tp_useroundframepanels">', $txt['tp-useroundframepanels'], '</label>
					</dt>
					<dd>
						<input name="tp_useroundframepanels" type="radio" value="1" ' , $context['TPortal']['useroundframepanels']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_useroundframepanels" type="radio" value="0" ' , $context['TPortal']['useroundframepanels']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-hidecollapse'], '</label>
					</dt>
					<dd>
						<input name="tp_showcollapse" name="tp_showcollapse" type="radio" value="1" ' , $context['TPortal']['showcollapse']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_showcollapse" type="radio" value="0" ' , $context['TPortal']['showcollapse']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-hideediticon'], '</label>
					</dt>
					<dd>
						<input name="tp_blocks_edithide" type="radio" value="1" ' , $context['TPortal']['blocks_edithide']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_blocks_edithide" type="radio" value="0" ' , $context['TPortal']['blocks_edithide']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-uselangoption'], '</label>
					</dt>
					<dd>
						<input name="tp_uselangoption" type="radio" value="1" ' , $context['TPortal']['uselangoption']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_uselangoption" type="radio" value="0" ' , $context['TPortal']['uselangoption']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-use_groupcolor'], '</label>
						<div class="smalltext">'.$txt['tp-use_groupcolordesc'].'</div>
					</dt>
					<dd>
						<input name="tp_use_groupcolor" type="radio" value="1" ' , $context['TPortal']['use_groupcolor']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_use_groupcolor" type="radio" value="0" ' , $context['TPortal']['use_groupcolor']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
				</dl>
					<hr>
				<dl class="settings">
					<dt>
						<label for="tp_maxstars">', $txt['tp-maxrating'], '</label>
					</dt>
					<dd>
						<input name="tp_maxstars" id="tp_maxstars" size="4" type="text" value="'.$context['TPortal']['maxstars'].'">
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-stars'], '</label>
					</dt>
					<dd>
						<input name="tp_showstars" type="radio" value="1" ' , $context['TPortal']['showstars']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_showstars" type="radio" value="0" ' , $context['TPortal']['showstars']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
				</dl>
					<hr>
				<dl class="settings">
					<dt>
						<label for="field_name">', $txt['tp-useoldsidebar'], '</label>
					</dt>
					<dd>
						<input name="tp_oldsidebar" type="radio" value="1" ' , $context['TPortal']['oldsidebar']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_oldsidebar" type="radio" value="0" ' , $context['TPortal']['oldsidebar']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-admin_showblocks'], '</label>
					</dt>
					<dd>
						<input name="tp_admin_showblocks" type="radio" value="1" ' , $context['TPortal']['admin_showblocks']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_admin_showblocks" type="radio" value="0" ' , $context['TPortal']['admin_showblocks']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-imageproxycheck'], '</label>
						<div class="smalltext">'.$txt['tp-imageproxycheckdesc'].'</div>
					</dt>
					<dd>
						<input name="tp_imageproxycheck" type="radio" value="1" ' , $context['TPortal']['imageproxycheck']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_imageproxycheck" type="radio" value="0" ' , $context['TPortal']['imageproxycheck']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>';
                    db_extend('extra');
                    if(version_compare($smcFunc['db_get_version'](), '5.6', '>=')) {
                        echo '
                        <dt>
                            <label for="field_name">', $txt['tp-fulltextsearch'], '</label>
                            <div class="smalltext">' , $txt['tp-fulltextsearchdesc'] , '</div>
                        </dt>
                        <dd>
                            <input name="tp_fulltextsearch" type="radio" value="1" ' , $context['TPortal']['fulltextsearch']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
                            <input name="tp_fulltextsearch" type="radio" value="0" ' , $context['TPortal']['fulltextsearch']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
                        </dd>';
                    }
					echo '
					<dt>
						<label for="field_name">', $txt['tp-disabletemplateeval'], '</label>
						<div class="smalltext">' , $txt['tp-disabletemplateevaldesc'] , '</div>
					</dt>
					<dd>
                        <input name="tp_disable_template_eval" type="radio" value="1" ' , $context['TPortal']['disable_template_eval']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
                        <input name="tp_disable_template_eval" type="radio" value="0" ' , $context['TPortal']['disable_template_eval']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
                    <dt>
						<label for="tp_image_upload_path">', $txt['tp-imageuploadpath'], '</label>
						<div class="smalltext">' , $txt['tp-imageuploadpathdesc'] , '</div>
					</dt>
					<dd>
						<input name="tp_image_upload_path" id="tp_image_upload_path" type="text" value="' , !empty($context['TPortal']['image_upload_path']) ? $context['TPortal']['image_upload_path'] : '' , '">
					</dd>
                    <dt>
						<label for="tp_download_upload_path">', $txt['tp-downloaduploadpath'], '</label>
						<div class="smalltext">' , $txt['tp-downloaduploadpathdesc'] , '</div>
					</dt>
					<dd>
						<input name="tp_download_upload_path" id="tp_download_upload_path" type="text" value="' , !empty($context['TPortal']['download_upload_path']) ? $context['TPortal']['download_upload_path'] : '' , '">
					</dd>
                    <dt>
						<label for="tp_blockcode_upload_path">', $txt['tp-blockcodeuploadpath'], '</label>
						<div class="smalltext">' , $txt['tp-blockcodeuploadpathdesc'] , '</div>
					</dt>
					<dd>
						<input name="tp_blockcode_upload_path" id="tp_blockcode_upload_path" type="text" value="' , !empty($context['TPortal']['blockcode_upload_path']) ? $context['TPortal']['blockcode_upload_path'] : '' , '">
					</dd>
                    <dt>
						<label for="tp_copyrightremoval">', $txt['tp-copyrightremoval'], '</label>
						<div class="smalltext">' , $txt['tp-copyrightremovaldesc'] , '</div>
					</dt>
					<dd>
						<input size="50" name="tp_copyrightremoval" id="tp_copyrightremoval" type="text" value="' , !empty($context['TPortal']['copyrightremoval']) ? $context['TPortal']['copyrightremoval'] : '' , '">
					</dd>
				</div>
					<div class="padding-div;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
		</div>
	</form>';
}

// Frontpage Settings page
function template_frontpage()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language, $smcFunc;

		echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="frontpage">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-frontpage_settings'] . '</h3></div>
		<div id="frontpage-settings" class="admintable admin-area">
			<div class="information smalltext">' , $txt['tp-helpfrontpage'] , '</div><div></div>
			<div class="windowbg noup">
				<div class="formtable padding-div">
					<dl class="settings">
						<dt>
							', $txt['tp-whattoshow'], '
						</dt>
						<dd>
							<input name="tp_front_type" id="tp_front_type1" type="radio" value="forum_selected" ' , $context['TPortal']['front_type']=='forum_selected' ? 'checked' : '' , '><label for="tp_front_type1"> '.$txt['tp-selectedforum'].'</label><br>
							<input name="tp_front_type" id="tp_front_type2" type="radio" value="forum_selected_articles" ' , $context['TPortal']['front_type']=='forum_selected_articles' ? 'checked' : '' , '><label for="tp_front_type2"> '.$txt['tp-selectbothforum'].'</label><br>
							<input name="tp_front_type" id="tp_front_type3" type="radio" value="forum_only" ' , $context['TPortal']['front_type']=='forum_only' ? 'checked' : '' , '><label for="tp_front_type3"> '.$txt['tp-onlyforum'].'</label><br>
							<input name="tp_front_type" id="tp_front_type4" type="radio" value="forum_articles" ' , $context['TPortal']['front_type']=='forum_articles' ? 'checked' : '' , '><label for="tp_front_type4"> '.$txt['tp-bothforum'].'</label><br>
							<input name="tp_front_type" id="tp_front_type5" type="radio" value="articles_only" ' , $context['TPortal']['front_type']=='articles_only' ? 'checked' : '' , '><label for="tp_front_type5"> '.$txt['tp-onlyarticles'].'</label><br>
							<input name="tp_front_type" id="tp_front_type6" type="radio" value="single_page"  ' , $context['TPortal']['front_type']=='single_page' ? 'checked' : '' , '><label for="tp_front_type6"> '.$txt['tp-singlepage'].'</label><br>
							<input name="tp_front_type" id="tp_front_type7" type="radio" value="frontblock"  ' , $context['TPortal']['front_type']=='frontblock' ? 'checked' : '' , '><label for="tp_front_type7"> '.$txt['tp-frontblocks'].'</label><br>
							<input name="tp_front_type" id="tp_front_type8" type="radio" value="boardindex"  ' , $context['TPortal']['front_type']=='boardindex' ? 'checked' : '' , '><label for="tp_front_type8"> '.$txt['tp-boardindex'].'</label><br>
							<input name="tp_front_type" id="tp_front_type9" type="radio" value="module"  ' , $context['TPortal']['front_type']=='module' ? 'checked' : '' , '><label for="tp_front_type9"> '.$txt['tp-frontmodule'].'</label><br>
							<hr />
							<div style="padding-left: 2em;">';
			echo '      <br></dd>
						<dt>
							', $txt['tp-frontblockoption'], '
						</dt>
						<dd>
							<input name="tp_frontblock_type" id="tp_frontblock_type1" type="radio" value="single"  ' , $context['TPortal']['frontblock_type']=='single' ? 'checked' : '' , '><label for="tp_frontblock_type1"> '.$txt['tp-frontblocksingle'].'</label><br>
							<input name="tp_frontblock_type" id="tp_frontblock_type2" type="radio" value="first"  ' , $context['TPortal']['frontblock_type']=='first' ? 'checked' : '' , '><label for="tp_frontblock_type2"> '.$txt['tp-frontblockfirst'].'</label><br>
							<input name="tp_frontblock_type" id="tp_frontblock_type3" type="radio" value="last"  ' , $context['TPortal']['frontblock_type']=='last' ? 'checked' : '' , '><label for="tp_frontblock_type3"> '.$txt['tp-frontblocklast'].'</label><br><br>
						</dd>
						<dt>
							', $txt['tp-frontpageoptions'], '
						</dt>
						<dd>
							<input name="tp_frontpage_visual_left" id="tp_frontpage_visual_left" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['left']>0 ? 'checked' : '' , '><label for="tp_frontpage_visual_left"> ',$txt['tp-displayleftpanel'],'</label><br>
							<input name="tp_frontpage_visual_right" id="tp_frontpage_visual_right" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['right']>0 ? 'checked' : '' , '><label for="tp_frontpage_visual_right"> ',$txt['tp-displayrightpanel'],'</label><br>
							<input name="tp_frontpage_visual_top" id="tp_frontpage_visual_top" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['top']>0 ? 'checked' : '' , '><label for="tp_frontpage_visual_top"> ',$txt['tp-displaytoppanel'],'<br>
							<input name="tp_frontpage_visual_center" id="tp_frontpage_visual_center" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['center']>0 ? 'checked' : '' , '><label for="tp_frontpage_visual_center"> ',$txt['tp-displaycenterpanel'],'</label><br>
							<input name="tp_frontpage_visual_lower" id="tp_frontpage_visual_lower" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['lower']>0 ? 'checked' : '' , '><label for="tp_frontpage_visual_lower"> ',$txt['tp-displaylowerpanel'],'</label><br>
							<input name="tp_frontpage_visual_bottom" id="tp_frontpage_visual_bottom" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['bottom']>0 ? 'checked' : '' , '><label for="tp_frontpage_visual_bottom"> ',$txt['tp-displaybottompanel'],'</label><br>
							<input name="tp_frontpage_visual_header" id="tp_frontpage_visual_header" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['header']>0 ? 'checked' : '' , '><label for="tp_frontpage_visual_header"> ',$txt['tp-displaynews'],'</label><br><br>
						</dd>

					</dl>
					<hr>
					<div><strong>', $txt['tp-frontpage_layout'], '</strong></div>
					<div>
						<div class="tpartlayoutfp"><input name="tp_frontpage_layout" id="tp_frontpage_layout1" type="radio" value="1" ' ,
						$context['TPortal']['frontpage_layout']<2 ? 'checked' : '' , '><label for="tp_frontpage_layout1"> A ' ,
						$context['TPortal']['frontpage_layout']<2 ? '' : '' , '
							<div style="margin-top: 5px;">
								<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_a.png"/></label>
							</div>
						</div>
						<div class="tpartlayoutfp"><input name="tp_frontpage_layout" id="tp_frontpage_layout2" type="radio" value="2" ' ,
						$context['TPortal']['frontpage_layout']==2 ? 'checked' : '' , '><label for="tp_frontpage_layout2"> B ' ,
						$context['TPortal']['frontpage_layout']==2 ? '' : '' , '
							<div style="margin-top: 5px;">
								<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_b.png"/></label>
							</div>
						</div>
						<div class="tpartlayoutfp"><input name="tp_frontpage_layout" id="tp_frontpage_layout3" type="radio" value="3" ' ,
						$context['TPortal']['frontpage_layout']==3 ? 'checked' : '' , '><label for="tp_frontpage_layout3"> C ' ,
						$context['TPortal']['frontpage_layout']==3 ? '' : '' , '
							<div style="margin-top: 5px;">
								<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_c.png"/></label>
							</div>
						</div>
						<div class="tpartlayoutfp"><input name="tp_frontpage_layout" id="tp_frontpage_layout4" type="radio" value="4" ' ,
						$context['TPortal']['frontpage_layout']==4 ? 'checked' : '' , '><label for="tp_frontpage_layout4"> D ' ,
						$context['TPortal']['frontpage_layout']==4 ? '' : '' , '
							<div style="margin-top: 5px;">
								<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_d.png"/></label>
							</div>
						</div>
						<br style="clear: both;" /><br>
					</div>
					<div>
						<strong>', $txt['tp-articlelayouts'], '</strong>
					</div>
					<div>';	foreach($context['TPortal']['admin_layoutboxes'] as $box)
								echo '
									<div class="tpartlayouttype">
										<input type="radio" name="tp_frontpage_catlayout" id="tp_frontpage_catlayout'.$box['value'].'" value="'.$box['value'].'"' , $context['TPortal']['frontpage_catlayout']==$box['value'] ? ' checked="checked"' : '' , '><label for="tp_frontpage_catlayout'.$box['value'].'">
										'.$box['label'].'<br><img style="margin: 4px 4px 4px 10px;" src="' , $settings['tp_images_url'] , '/TPcatlayout'.$box['value'].'.png" alt="tplayout'.$box['value'].'" /></label>
									</div>';

							if(empty($context['TPortal']['frontpage_template']))
								$context['TPortal']['frontpage_template'] = '
					<span class="upperframe"><span></span></span>
					<div class="roundframe">
						<div class="title_bar">
							<h3 class="titlebg"><span class="left"></span>{article_shortdate} {article_title} </h3>
						</div>
						<div style="padding: 0; overflow: hidden;">
							{article_avatar}
							<div class="article_info">
								{article_category}
								{article_author}
								{article_date}
								{article_views}
								{article_rating}
								{article_options}
							</div>
							<div class="article_padding">{article_text}</div>
							{article_bookmark}
							{article_boardnews}
							{article_moreauthor}
							{article_morelinks}
						</div>
					</div>
					<span class="lowerframe" style="margin-bottom: 5px;"></span>';
							echo '<br style="clear: both;" />
				</div>
				<div>
					<h4>', $txt['reset_custom_template_layout'] ,'</h4>
					<textarea id="tp_customlayout" name="tp_frontpage_template">' . $context['TPortal']['frontpage_template'] . '</textarea><br><br>
				</div>
				<hr>
					<dl class="settings">
						<dt>
							<label for="tp_frontpage_limit">', $txt['tp-numberofposts'], '</label>
						</dt>
						<dd>
						  <input name="tp_frontpage_limit" id="tp_frontpage_limit" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['frontpage_limit'], '"><br><br>
						</dd>
						<dt>
							<label for="tp_frontpage_usorting">', $txt['tp-sortingoptions'], '</label>
						</dt>
						<dd>
							<select name="tp_frontpage_usorting" id="tp_frontpage_usorting">
								<option value="date"' , $context['TPortal']['frontpage_visualopts_admin']['sort']=='date' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions1'] , '</option>
								<option value="author_id"' , $context['TPortal']['frontpage_visualopts_admin']['sort']=='author_id' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions2'] , '</option>
								<option value="parse"' , $context['TPortal']['frontpage_visualopts_admin']['sort']=='parse' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions3'] , '</option>
								<option value="id"' , $context['TPortal']['frontpage_visualopts_admin']['sort']=='id' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions4'] , '</option>
							</select>&nbsp;
							<select name="tp_frontpage_sorting_order">
								<option value="desc"' , $context['TPortal']['frontpage_visualopts_admin']['sortorder']=='desc' ? ' selected="selected"' : '' , '>' , $txt['tp-sortdirection1'] , '</option>
								<option value="asc"' , $context['TPortal']['frontpage_visualopts_admin']['sortorder']=='asc' ? ' selected="selected"' : '' , '>' , $txt['tp-sortdirection2'] , '</option>
							</select>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>
							<label for="field_name">', $txt['tp-allowguests'], '</label>
						</dt>
						<dd>
							  <input name="tp_allow_guestnews" type="radio" value="1" ' , $context['TPortal']['allow_guestnews']==1 ? 'checked' : '' , '> '.$txt['tp-yes'].'
							  <input name="tp_allow_guestnews" type="radio" value="0" ' , $context['TPortal']['allow_guestnews']==0 ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-showforumposts'], '</label>
						</dt>
						<dd>';
		echo '
							<select size="5" name="tp_ssiboard" multiple="multiple">';
            if(is_countable($context['TPortal']['boards'])) {
                $tn = count($context['TPortal']['boards']);
            }
            else {
                $tn = 0;
            }

            for($n=0 ; $n<$tn; $n++) {
                echo '
								<option value="'.$context['TPortal']['boards'][$n]['id'].'"' , is_array($context['TPortal']['SSI_boards']) && in_array($context['TPortal']['boards'][$n]['id'] , $context['TPortal']['SSI_boards']) ? ' selected="selected"' : '' , '>'.$context['TPortal']['boards'][$n]['name'].'</option>';
            }
		
		echo '
							</select><br><br>
						</dd>
						<dt>
							<label for="tp_frontpage_limit_len">', $txt['tp-lengthofposts'], '</label>
						</dt>
						<dd>
						  <input name="tp_frontpage_limit_len" id="tp_frontpage_limit_len" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['frontpage_limit_len'], '"><br><br>
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-forumposts_avatar'], '</label>
						</dt>
						<dd>
							<input name="tp_forumposts_avatar" type="radio" value="1" ' , $context['TPortal']['forumposts_avatar']==1 ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_forumposts_avatar" type="radio" value="0" ' , $context['TPortal']['forumposts_avatar']==0 ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-useattachment'], '</label>
						</dt>
						<dd>
							<input name="tp_use_attachment" type="radio" value="1" ' , $context['TPortal']['use_attachment']==1 ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_use_attachment" type="radio" value="0" ' , $context['TPortal']['use_attachment']==0 ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
					</dl>
				</div>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// Articles page
function template_articles()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post" enctype="multipart/form-data" onsubmit="syncTextarea();">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="articles">
		<div class="cat_bar"><h3 class="catbg">' , $txt['tp-articles'] , !empty($context['TPortal']['categoryNAME']) ? $txt['tp-incategory']. ' ' . $context['TPortal']['categoryNAME'].' ' : '' ,  '</h3></div>
		<div id="edit-articles" class="admintable admin-area">
			<div class="information smalltext">' , $txt['tp-helparticles'] , '</div><div></div>
			<div class="windowbg noup padding-div">';

	if(isset($context['TPortal']['cats']) && count($context['TPortal']['cats'])>0)
	{
		echo '
		<table class="table_grid tp_grid" style="width:100%";>
		<thead>
			<tr class="title_bar titlebg2">
			<th scope="col" class="articles">
				<div>
					<div class="pos float-items" style="width:65%;"><strong>' , $txt['tp-name'] , '</strong></div>
					<div align="center" class="title-admin-area float-items" style="width:15%;"><strong>' , $txt['tp-articles'] , '</strong></div>
					<div align="center" class="title-admin-area float-items" style="width:20%;"><strong>' , $txt['tp-actions'] , '</strong></div>
					<p class="clearthefloat"></p>
				</div>
			</th>
			</tr>
		</thead>
		<tbody>';
		$alt=true;
		foreach($context['TPortal']['cats'] as $c => $cat)
		{
			if(in_array($cat['parent'],$context['TPortal']['basecats']))
			{
				echo '
			<tr class="windowbg">
			<td class="articles">
				<div>
					<div style="width:65%;" class="float-items">
					  <a href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$cat['id'].'">' , $cat['name'] , '</a>
					</div>
					<div align="center" style="width:15%;" class="float-items">' , isset($context['TPortal']['cats_count'][$cat['id']]) ? $context['TPortal']['cats_count'][$cat['id']] : '0' , '</div>
					<div align="center" style="width:20%;" class="float-items">
						<a href="' . $scripturl . '?cat=' . $cat['id'] . '"><img src="' . $settings['tp_images_url'] . '/TPfilter.png" alt="" /></a>
						<a href="' . $scripturl . '?action=tpadmin;sa=categories;cu=' . $cat['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="" /></a>
					</div><p class="clearthefloat"></p>
				</div>';
				// check if we got children
				foreach($context['TPortal']['cats'] as $d => $subcat)
				{
					if($subcat['parent']==$cat['id'])
					{
						echo '
				<div>
					<div style="width:65%;" class="float-items">&nbsp;&nbsp;<img src="' . $settings['tp_images_url'] . '/TPtree_article.png" alt="" />
						<a href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$subcat['id'].'">' , $subcat['name'] , '</a>
					</div>
					<div align="center" style="width:15%;" class="float-items">' , isset($context['TPortal']['cats_count'][$subcat['id']]) ? $context['TPortal']['cats_count'][$subcat['id']] : '0' , '</div>
					<div align="center" style="width:20%;" class="float-items">&nbsp;</div>
					<p class="clearthefloat"></p>
				</div>';
					}
				}
	echo '
			</td>
			</tr>';
				$alt = !$alt;
			}
		}
	echo '
		</tbody>
	</table><br>';
	}
	// Articles in category Page
	if(isset($context['TPortal']['arts']))
	{
		echo '
	<table class="table_grid tp_grid" style="width:100%";>
		<thead>
			<tr class="title_bar titlebg2">
			<th scope="col" class="articles">
				<div class="catbg3">
					<div style="width:7%;" class="pos float-items">' , $context['TPortal']['sort']=='parse' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on position" /> ' : '' , '<a title="Sort on position" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=parse"><strong>' , $txt['tp-pos'] , '</strong></a></div>
					<div style="width:25%;" class="name float-items">' , $context['TPortal']['sort']=='subject' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on subject" /> ' : '' , '<a title="Sort on subject" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=subject"><strong>' , $txt['tp-name'] , '</strong></a></div>
					<div style="width:10%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=author_id"><strong>' , $txt['tp-author'] , '</strong></a></div>
					<div style="width:20%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=date"><strong>' , $txt['tp-date'] , '</strong></a></div>
					<div style="width:25%;" class="title-admin-area float-items">
						' , $context['TPortal']['sort']=='off' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on active" /> ' : '' , '<a title="Sort on active" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=off"><img src="' . $settings['tp_images_url'] . '/TPactive2.png" alt="" /></a>
						' , $context['TPortal']['sort']=='frontpage' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on frontpage" /> ' : '' , '<a title="Sort on frontpage" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=frontpage"><img src="' . $settings['tp_images_url'] . '/TPfront.png" alt="*" /></a>
						' , $context['TPortal']['sort']=='sticky' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on sticky" /> ' : '' , '<a title="Sort on sticky" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=sticky"><img src="' . $settings['tp_images_url'] . '/TPsticky1.png" alt="" /></a>
						' , $context['TPortal']['sort']=='locked' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on locked" /> ' : '' , '<a title="Sort on locked" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=locked"><img src="' . $settings['tp_images_url'] . '/TPlock1.png" alt="" /></a>
					</div>
					<div style="width:13%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=type"><strong>' , $txt['tp-type'] , '</strong></a></div>
					<p class="clearthefloat"></p>
			 </div>
			</th>
			</tr>
		</thead>
		<tbody> ';

		foreach($context['TPortal']['arts'] as $a => $alink)
		{
			$alink['pos'] = $alink['pos']=='' ? 0 : $alink['pos'];
			$catty = $alink['category'];

			echo '
			<tr class="windowbg">
			<td class="articles">
				<div>
					<div style="width:7%;" class="adm-pos float-items">
						<a name="article'.$alink['id'].'"></a><input type="text" size="2" value="'.$alink['pos'].'" name="tp_article_pos'.$alink['id'].'" />
					</div>
					<div style="width:25%;" class="adm-name float-items">
						' , $alink['locked']==0 ? '<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article=' . $alink['id'] . '">' . $alink['subject'].'</a>' : '&nbsp;' . $alink['subject'] , '
					</div>
					<a href="" class="clickme">'.$txt['tp-more'].'</a>
					<div class="box" style="width:68%;float:left;">
						<div style="width:14.8%;" class="smalltext fullwidth-on-res-layout float-items">
							<div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . 	'/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=author_id"><strong>' , $txt['tp-author'] , '</a></strong>
							</div>
							<div id="size-on-respnsive-layout"><a href="' . $scripturl . '?action=profile;u=' , $alink['author_id'], '">'.$alink['author'] .'</a>
							</div>
						</div>
						<div style="width:29.8%;" class="smalltext fullwidth-on-res-layout float-items">
							<div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=date"><strong>' , $txt['tp-date'] , '</strong></a>
							</div>
							<div id="size-on-respnsive-layout">' , timeformat($alink['date']) , '</div>
						</div>
						<div style="width:37.5%;" class="smalltext fullwidth-on-res-layout float-items">
							<div id="show-on-respnsive-layout" style="margin-top:0.5%;"><strong>'.$txt['tp-editarticleoptions2'].'</strong></div>
							<div id="size-on-respnsive-layout">
								<img style="cursor: pointer;" class="toggleActive" id="artActive' .$alink['id']. '" title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TPactive' , $alink['off']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-activate'].'"  />
								<a href="',$scripturl, '?page=',$alink['id'],'"><img title="'.$txt['tp-preview'].'" src="' .$settings['tp_images_url']. '/TPfilter.png" alt="" /></a>
								' , $alink['locked']==0 ?
								'<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article='.$alink['id']. '"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>' : '<img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm2.png" alt="'.$txt['tp-islocked'].'"  />' , '
								<img style="cursor: pointer;" class="toggleFront" id="artFront' .$alink['id']. '" title="'.$txt['tp-setfrontpage'].'" border="0" src="' .$settings['tp_images_url']. '/TPfront' , $alink['frontpage']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-setfrontpage'].'"  />
								<img style="cursor: pointer;" class="toggleSticky" id="artSticky' .$alink['id']. '" title="'.$txt['tp-setsticky'].'" border="0" src="' .$settings['tp_images_url']. '/TPsticky' , $alink['sticky']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setsticky'].'"  />
								<img style="cursor: pointer;" class="toggleLock" id="artLock' .$alink['id']. '" title="'.$txt['tp-setlock'].'" border="0" src="' .$settings['tp_images_url']. '/TPlock' , $alink['locked']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setlock'].'"  />
								<img style="cursor: pointer;" class="toggleFeatured" id="artFeatured' .$alink['id']. '" title="'.$txt['tp-featured'].'" border="0" src="' .$settings['tp_images_url']. '/TPflag' , $alink['featured']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-turnoff'].'"  />
							</div>
						</div>
						<div style="width:7%;text-transform:uppercase;" class="smalltext fullwidth-on-res-layout float-items">
							<div id="show-on-respnsive-layout">
							' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=type"><strong>' , $txt['tp-type'] , '</strong></a>
							</div>
							' , empty($alink['type']) ? 'html' : $alink['type'] , '
						</div>
						<div style="width:6%;" class="smalltext fullwidth-on-res-layout float-items" align="center">
							<div id="show-on-respnsive-layout"><strong>'.$txt['tp-delete'].'</strong></div>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';artdelete=' .$alink['id'] , !empty($_GET['cu']) ? ';cu=' . $_GET['cu'] : '' , '" onclick="javascript:return confirm(\''.$txt['tp-articleconfirmdelete'].'\')">
							<img title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt="'.$txt['tp-delete'].'"  /></a>
						</div>
						<p class="clearthefloat"></p>
					</div>
					<p class="clearthefloat"></p>
				</div>
			</td>
			</tr>';
			}
	echo '
		</tbody>
	</table>';
			if( !empty($context['TPortal']['pageindex']))
				echo '
								<div class="middletext padding-div">
									'.$context['TPortal']['pageindex'].'
								</div>';
			echo '
							<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'">
						<input name="tpadmin_form_category" type="hidden" value="' . $catty . '"></div>';
	}
	else
		echo '
				<div class="padding-div"></div>';

		echo '
		</div></div>
	</form>';
}

// Article Categories page
function template_categories()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post" enctype="multipart/form-data" onsubmit="syncTextarea();">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="categories">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-article'], ' ' , $txt['tp-tabs5'] . '</h3></div>
		<div id="edit-category" class="admintable admin-area">
			<div class="windowbg noup padding-div">
				<table class="table_grid tp_grid" style="width:100%";>
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col">
						<div>
							<div class="float-items" style="width:120px;"><strong>' , $txt['tp-actions'] , '</strong></div>
							<div class="float-items" style="max-width:76%;"><strong>' , $txt['tp-name'] , '</strong></div>
							<p class="clearthefloat"></p>
						</div>
					</th>
					</tr>
				</thead>
				<tbody>';

		if(isset($context['TPortal']['editcats']) && count($context['TPortal']['editcats'])>0)
		{
			$alt=true;
			foreach($context['TPortal']['editcats'] as $c => $cat)
			{
				echo '
					<tr class="windowbg">
					<td class="articles">
						<div>';

				echo '
							<div class="float-items" style="width:120px;">
								<a href="' . $scripturl . '?cat=' . $cat['id'] . '"><img src="' . $settings['tp_images_url'] . '/TPfilter.png" alt="" /></a>
								<a href="' . $scripturl . '?action=tpadmin;sa=addcategory;child;cu=' . $cat['id'] . '" title="' . $txt['tp-addsubcategory'] . '"><img src="' . $settings['tp_images_url'] . '/TPadd.png" alt="" /></a>
								<a href="' . $scripturl . '?action=tpadmin;sa=addcategory;copy;cu=' . $cat['id'] . '" title="' . $txt['tp-copycategory'] . '"><img src="' . $settings['tp_images_url'] . '/TPcopy.png" alt="" /></a>
								&nbsp;&nbsp;<a href="' . $scripturl . '?action=tpadmin;catdelete='.$cat['id'].';' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="javascript:return confirm(\''.$txt['tp-confirmcat1'].'  \n'.$txt['tp-confirmcat2'].'\')" title="' . $txt['tp-delete'] . '"><img src="' . $settings['tp_images_url'] . '/TPdelete2.png" alt="" /></a>
							</div>
							<div class="float-items' , '" style="max-width:76%;">
								' , str_repeat("-",$cat['indent']) , '
								<a href="' . $scripturl . '?action=tpadmin;sa=categories;cu='.$cat['id'].'">' , $cat['name'] , '</a>
								' , isset($context['TPortal']['cats_count'][$cat['id']]) ? '<a href="' . $scripturl. '?action=tpadmin;sa=articles;cu='.$cat['id'].'">('.$context['TPortal']['cats_count'][$cat['id']].' ' . ($context['TPortal']['cats_count'][$cat['id']]>1 ? $txt['tp-articles'] : $txt['tp-article']) . ')</a>' : '' , '
							</div>
							<p class="clearthefloat"></p>
						</div>
					</td>
					</tr>';
				$alt = !$alt;
			}
		}
				echo '
				</tbody>
				</table>';
		echo '
				<br>
				<div class="padding-div;">
					<input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'">
				</div>
			</div>
		</div>
	</form>';
}

// Add category Page
function template_addcategory()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	if(isset($_GET['cu']) && is_numeric($_GET['cu']))
		$currcat = $_GET['cu'];

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="addcategory">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-addcategory'] . '</h3></div>
		<div id="new-category" class="admintable admin-area">
			<div class="information smalltext">' , $txt['tp-helpaddcategory'] , '</div><div></div>
			<div class="windowbg noup ">
				<div class="formtable padding-div">
					<dl class="settings">
						<dt>
							<label for="tp_cat_name"><h4>'.$txt['tp-name'].':</h></label>
						</dt>
						<dd>
							<input name="tp_cat_name" id="tp_cat_name" type="text" value=""><br><br>
						</dd>';
			// set up category to be sub of
			echo '
						<dt>
							<label for="tp_cat_parent"><h4>'.$txt['tp-subcatof'].'</h></label>
						</dt>
						<dd>
							<select size="1" name="tp_cat_parent" id="tp_cat_parent">
								<option value="0">'.$txt['tp-none2'].'</option>';
			if(isset($context['TPortal']['editcats'])){
				foreach($context['TPortal']['editcats'] as $s => $submg ){
						echo '
							<option value="'.$submg['id'].'"' , isset($currcat) && $submg['id']==$currcat ? ' selected="selected"' : '' , '>'. str_repeat("-",$submg['indent']) .' '.$submg['name'].'</option>';
				}
			}
			echo '
							</select><input name="newcategory" type="hidden" value="1">
						<dd>
					</dl>
				</div>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// Category List Page
function template_clist()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

		echo '
	<form  accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="clist">
		<div class="cat_bar"><h3 class="catbg">TinyPortal - '.$txt['tp-generalsettings'].'</h3></div>
		<div id="clist" class="admintable admin-area">
			<div class="windowbg noup">
				<div class="padding-div"><strong>'.$txt['tp-clist'].'</strong></div>
				<div class="padding-div">';

		$clist = explode(',',$context['TPortal']['cat_list']);
		echo '
					<input name="tp_clist-1" type="hidden" value="-1">';
		foreach($context['TPortal']['catnames'] as $ta => $val){
			echo '
					<input name="tp_clist'.$ta.'" type="checkbox" value="'.$ta.'"';
			if(in_array($ta, $clist))
				echo ' checked';
			echo '>  '.html_entity_decode($val).'<br>';
		}
		echo '
					<br><input type="checkbox" onclick="invertAll(this, this.form, \'tp_clist\');" />  '.$txt['tp-checkall'].'
				</div><br>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="send"></div>
			</div>
		</div>
	</form>';
}

// Edit Article Category Page
function template_editcategory()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language, $smcFunc;

		$mg = $context['TPortal']['editcategory'];
		echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="editcategory">
		<input name="tpadmin_form_id" type="hidden" value="' . $mg['id'] . '">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-editcategory'] . '</h3></div>
		<div id="edit-art-category" class="admintable admin-area">
			<div class="windowbg noup">
				<div class="formtable padding-div">
					<dl class="settings">
						<dt>
							<label for="tp_category_value1">', $txt['tp-name'], '</label>
						</dt>
						<dd>
							<input size="40" name="tp_category_value1" id="tp_category_value1" type="text" value="' ,html_entity_decode($mg['value1']), '">
						<dd>
						<dt>
							<label for="tp_category_value2">', $txt['tp-parent'], '</label>
						</dt>
						<dd>
							<select name="tp_category_value2" id="tp_category_value2" style="max-width:100%;">
								<option value="0"' , $mg['value2']==0 || $mg['value2']=='9999' ? ' selected="selected"' : '' , '>' , $txt['tp-noname'] , '</option>';
				foreach($context['TPortal']['editcats'] as $b => $parent) {
					if($parent['id']!= $mg['id'])
						echo '
								<option value="' . $parent['id'] . '"' , $parent['id']==$mg['value2'] ? ' selected="selected"' : '' , '>' , str_repeat("-",$parent['indent']) ,' ' , html_entity_decode($parent['name']) , '</option>';
				}
					echo '
							</select>
						<dd>
						<dt>
							<label for="tp_category_sort">', $txt['tp-sorting'], '</label>
						</dt>
						<dd>
							<select name="tp_category_sort" id="tp_category_sort">
								<option value="date"' , isset($mg['sort']) && $mg['sort']=='date' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions1'] , '</option>
								<option value="author_id"' , isset($mg['sort']) && $mg['sort']=='author_id' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions2'] , '</option>
								<option value="parse"' , isset($mg['sort']) && $mg['sort']=='parse' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions3'] , '</option>
								<option value="id"' , isset($mg['sort']) && $mg['sort']=='id' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions4'] , '</option>
							</select>
							<select name="tp_category_sortorder">
								<option value="desc"' , isset($mg['sortorder']) && $mg['sortorder']=='desc' ? ' selected="selected"' : '' , '>' , $txt['tp-sortdirection1'] , '</option>
								<option value="asc"' , isset($mg['sortorder']) && $mg['sortorder']=='asc' ? ' selected="selected"' : '' , '>' , $txt['tp-sortdirection2'] , '</option>
							</select>
						<dd>
						<dt>
							<label for="tp_category_articlecount">', $txt['tp-articlecount'], '</label>
						</dt>
						<dd>
							<input size="6" name="tp_category_articlecount" id="tp_category_articlecount" type="text" value="' , empty($mg['articlecount']) ? $context['TPortal']['frontpage_limit'] : $mg['articlecount']  , '">
						<dd>
						<dt>
							<label for="tp_category_value8">', $txt['tp-shortname'], '</label>
						</dt>
						<dd>
							<input size="20" name="tp_category_value8" id="tp_category_value8" type="text" value="' , isset($mg['value8']) ? $mg['value8'] : '' , '">
						</dd>
					</dl>
					<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
					<hr>
					<div>
						<div><strong>', $txt['tp-catlayouts'], '</strong></div>

						<div class="tpartlayoutfp"><input name="tp_category_layout" id="tp_category_layout1" type="radio" value="1" ' ,
							$mg['layout']==1 ? 'checked' : '' , '> A ' ,
							$mg['layout']==1 ? '' : '' , '
							<div class="tborder" style="margin-top: 5px;">
								 <label for="tp_category_layout1"><img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_a.png"/></label>
							</div>
						</div>
						<div class="tpartlayoutfp"><input name="tp_category_layout" id="tp_category_layout2" type="radio" value="2" ' ,
							$mg['layout']==2 ? 'checked' : '' , '> B ' ,
							$mg['layout']==2 ? '' : '' , '
							<div class="tborder" style="margin-top: 5px;">
								<label for="tp_category_layout2"><img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_b.png"/></label>
							</div>
						</div>
						<div class="tpartlayoutfp"><input name="tp_category_layout" id="tp_category_layout3" type="radio" value="3" ' ,
							$mg['layout']==3 ? 'checked' : '' , '> C ' ,
							$mg['layout']==3 ? '' : '' , '
							<div class="tborder" style="margin-top: 5px;">
								<label for="tp_category_layout3"><img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_c.png"/></label>
							</div>
						</div>
						<div class="tpartlayoutfp"><input name="tp_category_layout" id="tp_category_layout4" type="radio" value="4" ' ,
							$mg['layout']==4 ? 'checked' : '' , '> D ' ,
							$mg['layout']==4 ? '' : '' , '
							<div class="tborder" style="margin-top: 5px;">
								<label for="tp_category_layout4"><img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_d.png"/></label>
							</div>
						</div>
						<p class="clearthefloat"></p><br>
					</div>
					<div>
						<div><strong>'.$txt['tp-articlelayouts']. ':</strong></div>
						<div>';
			foreach($context['TPortal']['admin_layoutboxes'] as $box)
				echo '
							<div class="tpartlayouttype">
								<input type="radio" name="tp_category_catlayout" id="tp_category_catlayout'.$box['value'].'" value="'.$box['value'].'"' , $mg['catlayout']==$box['value'] ? ' checked="checked"' : '' , '>
								<label for="tp_category_catlayout'.$box['value'].'">'.$box['label'].'<br><img style="margin: 4px 4px 4px 10px;" src="' , $settings['tp_images_url'] , '/TPcatlayout'.$box['value'].'.png" alt="tplayout'.$box['value'].'" /></label>
							</div>';
				if(empty($mg['value9']))
					$mg['value9'] = '
							<div class="tparticle">
								<div class="cat_bar">
									<h3 class="catbg"><span class="left"></span>{article_shortdate} {article_title} {article_category}</h3>
								</div>
								<div class="windowbg2">
									<span class="topslice"><span></span></span>
									<div class="content">
										{article_avatar}
										<div class="article_info">
											{article_author}
											{article_date}
											{article_views}
											{article_rating}
											{article_options}
										</div>
										<div class="article_padding">{article_text}</div>
										<div class="article_padding">{article_moreauthor}</div>
										<div class="article_padding">{article_bookmark}</div>
										<div class="article_padding">{article_morelinks}</div>
										<div class="article_padding">{article_comments}</div>
									</div>
									<span class="botslice"><span></span></span>
								</div>
							</div>';
				echo '	</div>
						<br style="clear: both;" />
						<h4>', $txt['reset_custom_template_layout'] ,'</h4>
						<textarea id="tp_customlayout" name="tp_category_value9">' . $mg['value9'] . '</textarea><br><br>
					</div>
					<hr>
					<dl class="settings">
						<dt>
							<label for="field_name">', $txt['tp-showchilds'], '</label>
						</dt>
						<dd>
							<input name="tp_category_showchild" type="radio" value="0"' , ((isset($mg['showchild']) && $mg['showchild']==0) || !isset($mg['showchild'])) ? ' checked="checked"' : '' , '> ' , $txt['tp-no'] , '
							<input name="tp_category_showchild" type="radio" value="1"' , (isset($mg['showchild']) && $mg['showchild']==1) ? ' checked="checked"' : '' , '> ' , $txt['tp-yes'] , '<br><br>
						<dd>
						<dt>
							<strong>', $txt['tp-allpanels'], '</strong>
						</dt>
						<dt>
							<label for="tp_category_leftpanel">', $txt['tp-displayleftpanel'], '</label>
						</dt>
						<dd>
							<input type="checkbox" name="tp_category_leftpanel" id="tp_category_leftpanel" value="1"' , !empty($mg['leftpanel']) ? ' checked="checked"' : '' ,' />
						<dd>
						<dt>
							<label for="tp_category_rightpanel">', $txt['tp-displayrightpanel'], '</label>
						</dt>
						<dd>
							<input type="checkbox" name="tp_category_rightpanel" id="tp_category_rightpanel" value="1"' , !empty($mg['rightpanel']) ? ' checked="checked"' : '' ,' />
						<dd>
						<dt>
							<label for="tp_category_toppanel">', $txt['tp-displaytoppanel'], '</label>
						</dt>
						<dd>
							<input type="checkbox" name="tp_category_toppanel" id="tp_category_toppanel" value="1"' , !empty($mg['toppanel']) ? ' checked="checked"' : '' ,' />
						<dd>
						<dt>
							<label for="tp_category_bottompanel">', $txt['tp-displaybottompanel'], '</label>
						</dt>
						<dd>
							<input type="checkbox" name="tp_category_bottompanel" id="tp_category_bottompanel" value="1"' , !empty($mg['bottompanel']) ? ' checked="checked"' : '' ,' />
						<dd>
						<dt>
							<label for="tp_category_centerpanel">', $txt['tp-displaycenterpanel'], '</label>
						</dt>
						<dd>
							<input type="checkbox" name="tp_category_centerpanel" id="tp_category_centerpanel" value="1"' , !empty($mg['centerpanel']) ? ' checked="checked"' : '' ,' />
						<dd>
						<dt>
							<label for="tp_category_lowerpanel">', $txt['tp-displaylowerpanel'], '</label>
						</dt>
						<dd>
							<input type="checkbox" name="tp_category_lowerpanel" id="tp_category_lowerpanel" value="1"' , !empty($mg['lowerpanel']) ? ' checked="checked"' : '' ,' />
						<dd>
					</dl>
					<dl class="settings">
						<dt>
							<div class="font-strong">'.$txt['tp-allowedgroups']. ':</div>
							<div class="tp_largelist2">';
			// loop through and set membergroups
			$tg=explode(',',$mg['value3']);
			foreach($context['TPmembergroups'] as $g) {
				if($g['posts']=='-1' && $g['id']!='1') {
					echo '<input name="tp_category_group_'.$g['id'].'" id="'.$g['name'].'" type="checkbox" value="'.$mg['id'].'"';
					if(in_array($g['id'],$tg))
						echo ' checked';
					echo '><label for="'.$g['name'].'"> '.$g['name'].' <br>';
				}
			}
			// if none is chosen, have a control value
				echo '
							</div><br>
							<input type="checkbox" id="tp_catgroup-2" onclick="invertAll(this, this.form, \'tp_category_group\');" /><label for="tp_catgroup-2"> '.$txt['tp-checkall'].'</label><input name="tp_catgroup-2" type="hidden" value="'.$mg['id'].'">
						</dt>
					</dl>
				</div>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// Uncategorized articles Page
function template_strays()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post" enctype="multipart/form-data" onsubmit="syncTextarea();">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="strays">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-uncategorised2'] . '</h3></div>
		<div id="uncategorized" class="admintable admin-area">
			<div class="information smalltext">' , $txt['tp-helpstrays'] , '</div><div></div>';
	if(isset($context['TPortal']['arts_nocat'])) {
		echo '
			<div class="windowbg noup padding-div">
				<div>
					<table class="table_grid tp_grid" style="width:100%";>
					<thead>
						<tr class="title_bar titlebg2">
						<th scope="col">
							<div>
								<div style="width:7%;" class="pos float-items">' , $context['TPortal']['sort']=='parse' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on position" /> ' : '' , '<a title="Sort on position" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=parse"><strong>' , $txt['tp-pos'] , '</strong></a></div>
								<div style="width:25%;" class="name float-items">' , $context['TPortal']['sort']=='subject' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on subject" /> ' : '' , '<a title="Sort on subject" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=subject"><strong>' , $txt['tp-name'] , '</strong></a></div>
								<div style="width:10%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=author_id"><strong>' , $txt['tp-author'] , '</strong></a></div>
								<div style="width:20%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=date"><strong>' , $txt['tp-date'] , '</strong></a></div>
								<div style="width:25%;" class="title-admin-area float-items">
									' , $context['TPortal']['sort']=='off' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on active" /> ' : '' , '<a title="Sort on active" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=off"><img src="' . $settings['tp_images_url'] . '/TPactive2.png" alt="" /></a>
									' , $context['TPortal']['sort']=='frontpage' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on frontpage" /> ' : '' , '<a title="Sort on frontpage" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=frontpage"><img src="' . $settings['tp_images_url'] . '/TPfront.png" alt="*" /></a>
									' , $context['TPortal']['sort']=='sticky' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on sticky" /> ' : '' , '<a title="Sort on sticky" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=sticky"><img src="' . $settings['tp_images_url'] . '/TPsticky1.png" alt="" /></a>
									' , $context['TPortal']['sort']=='locked' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on locked" /> ' : '' , '<a title="Sort on locked" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=locked"><img src="' . $settings['tp_images_url'] . '/TPlock1.png" alt="" /></a>
								</div>
								<div style="width:13%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=type"><strong>' , $txt['tp-type'] , '</strong></a></div>
								<p class="clearthefloat"></p>
							</div>
						</th>
						</tr>
					</thead>
					<tbody>';

		foreach($context['TPortal']['arts_nocat'] as $a => $alink) {
			$alink['pos'] = $alink['pos']=='' ? 0 : $alink['pos'];
			$catty = $alink['category'];

			echo '
						<tr class="windowbg">
						<td class="articles">
							<div>
								<div style="width:7%;max-width:100%;" class="adm-pos float-items">
									<a name="article'.$alink['id'].'"></a><input type="text" size="2" value="'.$alink['pos'].'" name="tp_article_pos'.$alink['id'].'" />
								</div>
								<div style="width:25%;" class="adm-name float-items">
									' , $alink['locked']==0 ? '<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article=' . $alink['id'] . '">' . (!empty($alink['subject']) ? $alink['subject'] : $txt['tp-noname']) . '</a>' : '&nbsp;' . (!empty($alink['subject']) ? $alink['subject'] : $txt['tp-noname']) , '
								</div>
								<a href="" class="clickme">'.$txt['tp-more'].'</a>
								<div class="box" style="width:68%;float:left;">
									<div style="width:14.8%;" class="smalltext fullwidth-on-res-layout float-items">
										<div id="show-on-respnsive-layout">
											' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=author_id"><strong>' , $txt['tp-author'] , '</strong></a>
										</div>
										<div id="size-on-respnsive-layout">
											<a href="' . $scripturl . '?action=profile;u=' , $alink['author_id'], '">'.$alink['author'] .'</a>
										</div>
									</div>
									<div style="width:29.8%;" class="smalltext fullwidth-on-res-layout float-items">
										<div id="show-on-respnsive-layout">
											' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=date"><strong>' , $txt['tp-date'] , '</strong></a>
										</div>
										<div id="size-on-respnsive-layout">' , timeformat($alink['date']) , '</div>
									</div>
									<div style="width:36%;" class="smalltext fullwidth-on-res-layout float-items">
										<div id="show-on-respnsive-layout" style="margin-top:0.5%;"><strong>'.$txt['tp-editarticleoptions2'].'</strong></div>
										<div id="size-on-respnsive-layout">
											<img style="cursor: pointer;" class="toggleActive" id="artActive' .$alink['id']. '" title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TPactive' , $alink['off']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-activate'].'"  />
											<a href="',$scripturl, '?page=',$alink['id'],'"><img title="'.$txt['tp-preview'].'" src="' .$settings['tp_images_url']. '/TPfilter.png" alt="" /></a>
											' , $alink['locked']==0 ?
											'<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article='.$alink['id']. '"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>' : '<img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm2.png" alt="'.$txt['tp-islocked'].'"  />' , '
											<img style="cursor: pointer;" class="toggleFront" id="artFront' .$alink['id']. '" title="'.$txt['tp-setfrontpage'].'" border="0" src="' .$settings['tp_images_url']. '/TPfront' , $alink['frontpage']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-setfrontpage'].'"  />
											<img style="cursor: pointer;" class="toggleSticky" id="artSticky' .$alink['id']. '" title="'.$txt['tp-setsticky'].'" border="0" src="' .$settings['tp_images_url']. '/TPsticky' , $alink['sticky']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setsticky'].'"  />
											<img style="cursor: pointer;" class="toggleLock" id="artLock' .$alink['id']. '" title="'.$txt['tp-setlock'].'" border="0" src="' .$settings['tp_images_url']. '/TPlock' , $alink['locked']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setlock'].'"  />
											<img style="cursor: pointer;" class="toggleFeatured" id="artFeatured' .$alink['id']. '" title="'.$txt['tp-featured'].'" border="0" src="' .$settings['tp_images_url']. '/TPflag' , $alink['featured']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-turnoff'].'"  />
										</div>
									</div>
									<div style="width:7%;text-transform:uppercase;" class="smalltext fullwidth-on-res-layout float-items" align="center" >
										<div id="show-on-respnsive-layout">
										' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=type"><strong>' , $txt['tp-type'] , '</strong></a>
										</div>
										' , empty($alink['type']) ? 'html' : $alink['type'] , '
									</div>
									<div style="width:6%;" class="smalltext fullwidth-on-res-layout float-items" align="center">
										<div id="show-on-respnsive-layout"><strong>'.$txt['tp-delete'].'<strong></div>
										<a href="' . $scripturl . '?action=tpadmin;cu=-1;' . $context['session_var'] . '=' . $context['session_id'].';artdelete=' .$alink['id']. '" onclick="javascript:return confirm(\''.$txt['tp-articleconfirmdelete'].'\')">
										<img title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt="'.$txt['tp-delete'].'"  /></a>
									</div>
									<div style="width:4%;" class="smalltext fullwidth-on-res-layout float-items" align="center">
										<div id="show-on-respnsive-layout">Select</div>
										<input type="checkbox" name="tp_article_stray'.$alink['id'].'" value="1"  />
									</div>
									<p class="clearthefloat"></p>
							  </div>
							  <p class="clearthefloat"></p>
						</div>
						</td>
						</tr>';
		}
			echo '
					</tbody>
					</table>';
			if( !empty($context['TPortal']['pageindex'])) {
				echo '
					<div class="middletext padding-div">
						'.$context['TPortal']['pageindex'].'
					</div>';
            }
			echo '
				</div>';

		if(isset($context['TPortal']['allcats'])) {
			echo '
				<p style="text-align: right;padding:1%;max-width:100%;">
				<select name="tp_article_cat">
					<option value="0">' . $txt['tp-createnew'] . '</option>';
			foreach($context['TPortal']['allcats'] as $submg) {
  					echo '
						<option value="'.$submg['id'].'">',  ( isset($submg['indent']) && $submg['indent'] > 1 ) ? str_repeat("-", ($submg['indent']-1)) : '' , ' '. $txt['tp-assignto'] . $submg['name'].'</option>';
            }
			echo '
				</select>
				<input name="tp_article_new" value="" size="40"  /> &nbsp;
				</p>';
		}
		echo '
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>';
	}
	else {
		echo '
			<div class="windowbg2">
				<div class="windowbg3"></div>
			</div>';
    }
	echo '
		</div>
	</form>';
}

// Article Settings Page
function template_artsettings()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language, $date;

		echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="artsettings">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-articlesettings'] . '</h3></div>
		<div id="article-settings" class="admintable admin-area">
			<div class="information smalltext">' , $txt['tp-helpartsettings'] , '</div><div></div>
			<div class="windowbg noup">
				<div class="formtable padding-div">
					<dl class="settings">
						<dt>
							<label for="field_name">', $txt['tp-usewysiwyg'], '</label>
						</dt>
						<dd>
							<input name="tp_use_wysiwyg" type="radio" value="2" ' , ($context['TPortal']['use_wysiwyg']=='2' || $context['TPortal']['use_wysiwyg']=='1') ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_use_wysiwyg" type="radio" value="0" ' , $context['TPortal']['use_wysiwyg']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="tp_editorheight">', $txt['tp-editorheight'], '</label>
						</dt>
						<dd>
							<input name="tp_editorheight" id="tp_editorheight" type="text" size="4" value="' , $context['TPortal']['editorheight'] , '" />
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-usedragdrop'], '</label>
						</dt>
						<dd>
							<input name="tp_use_dragdrop" type="radio" value="1" ' , $context['TPortal']['use_dragdrop'] == '1' ? 'checked' : '', '> '.$txt['tp-yes'].'
							<input name="tp_use_dragdrop" type="radio" value="0" ' , $context['TPortal']['use_dragdrop'] == '0' ? 'checked' : '', '> '.$txt['tp-no'].'
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>
							<label for="field_name">', $txt['tp-hidearticle-link'], '</label>
						</dt>
						<dd>
							<input name="tp_hide_editarticle_link" type="radio" value="1" ' , $context['TPortal']['hide_editarticle_link']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hide_editarticle_link" type="radio" value="0" ' , $context['TPortal']['hide_editarticle_link']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">'.$txt['tp-printarticles'].'&nbsp;&nbsp;<img src="' . $settings['tp_images_url'] . '/TPprint.png" alt="" /></label>
						</dt>
						<dd>
							<input name="tp_print_articles" type="radio" value="1" ' , $context['TPortal']['print_articles']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_print_articles" type="radio" value="0" ' , $context['TPortal']['print_articles']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt>
							<label for="field_name">', $txt['tp-hidearticle-facebook'], '</label>
						</dt>
						<dd>
							<input name="tp_hide_article_facebook" type="radio" value="1" ' , $context['TPortal']['hide_article_facebook']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hide_article_facebook" type="radio" value="0" ' , $context['TPortal']['hide_article_facebook']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-hidearticle-twitter'], '</label>
						</dt>
						<dd>
							<input name="tp_hide_article_twitter" type="radio" value="1" ' , $context['TPortal']['hide_article_twitter']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hide_article_twitter" type="radio" value="0" ' , $context['TPortal']['hide_article_twitter']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-hidearticle-reddit'], '</label>
						</dt>
						<dd>
							<input name="tp_hide_article_reddit" type="radio" value="1" ' , $context['TPortal']['hide_article_reddit']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hide_article_reddit" type="radio" value="0" ' , $context['TPortal']['hide_article_reddit']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-hidearticle-digg'], '</label>
						</dt>
						<dd>
							<input name="tp_hide_article_digg" type="radio" value="1" ' , $context['TPortal']['hide_article_digg']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hide_article_digg" type="radio" value="0" ' , $context['TPortal']['hide_article_digg']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-hidearticle-delicious'], '</label>
						</dt>
						<dd>
							<input name="tp_hide_article_delicious" type="radio" value="1" ' , $context['TPortal']['hide_article_delicious']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hide_article_delicious" type="radio" value="0" ' , $context['TPortal']['hide_article_delicious']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-hidearticle-stumbleupon'], '</label>
						</dt>
						<dd>
							<input name="tp_hide_article_stumbleupon" type="radio" value="1" ' , $context['TPortal']['hide_article_stumbleupon']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hide_article_stumbleupon" type="radio" value="0" ' , $context['TPortal']['hide_article_stumbleupon']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
                        <dt>
							<label for="field_name">', $txt['tp-allow-links-article-comments'], '</label>
						</dt>
						<dd>
							<input name="tp_allow_links_article_comments" type="radio" value="1" ' , $context['TPortal']['allow_links_article_comments']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_allow_links_article_comments" type="radio" value="0" ' , $context['TPortal']['allow_links_article_comments']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
					</dl>
				</div>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// Article Submissions Page
function template_submission()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post" enctype="multipart/form-data" onsubmit="syncTextarea();">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="submission">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-submissionsettings']  . '</div></h3>
		<div id="submissions" class="admintable admin-area">
			<div class="windowbg noup padding-div">';
	if(isset($context['TPortal']['arts_submissions']))
	{
		echo '
				<table class="table_grid tp_grid" style="width:100%";>
					<thead>
						<tr class="title_bar titlebg2">
						<th scope="col" class="articles">
							<div class="catbg3">
								<div style="width:7%;" class="pos float-items"><strong>Select</div>
								<div style="width:25%;" class="name float-items"><strong>' , $context['TPortal']['sort']=='subject' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_up.png" alt="Sort on subject" /> ' : '' , '<a title="Sort on subject" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=subject">' , $txt['tp-name'] , '</a></strong></div>
								<div style="width:10%;" class="title-admin-area float-items"><strong> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_up.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=author_id">' , $txt['tp-author'] , '</a></strong></div>
								<div style="width:20%;" class="title-admin-area float-items"><strong> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=date">' , $txt['tp-date'] , '</a></strong></div>
								<div style="width:25%;" class="title-admin-area float-items"><strong>&nbsp;</strong></div>
								<div style="width:13%;" class="title-admin-area float-items"><strong> ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_up.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=type">' , $txt['tp-type'] , '</a></strong></div>
							    <p class="clearthefloat"></p>
							</div>
						</th>
						</tr>
					</thead>
					<tbody>';

		foreach($context['TPortal']['arts_submissions'] as $a => $alink)
		{
			$alink['pos'] = $alink['pos']=='' ? 0 : $alink['pos'];
			$catty = $alink['category'];

			echo '
						<tr class="windowbg">
						<td class="articles">
							<div>
								<div style="width:7%;" class="adm-pos float-items">
									<input type="checkbox" name="tp_article_submission'.$alink['id'].'" value="1"  />
								</div>
								<div style="width:25%;" class="adm-name float-items">
									' , $alink['locked']==0 ? '<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article=' . $alink['id'] . '"> ' . (!empty($alink['subject']) ? $alink['subject'] : $txt['tp-noname']) . '</a>' : '&nbsp;' . (!empty($alink['subject']) ? $alink['subject'] : $txt['tp-noname']) , '
								</div>
								<a href="" class="clickme">'.$txt['tp-more'].'</a>
								<div class="box" style="width:68%;float:left;">
									<div style="width:14.8%;" class="smalltext fullwidth-on-res-layout float-items">
										<div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=author_id"><strong>' , $txt['tp-author'] , '</strong></a></div>
										<div id="size-on-respnsive-layout"><a href="' . $scripturl . '?action=profile;u=' , $alink['author_id'], '">'.$alink['author'] .'</a></div>
									</div>
									<div style="width:29.8%;" class="smalltext fullwidth-on-res-layout float-items">
										<div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=date"><strong>' , $txt['tp-date'] , '</strong></a></div>
										<div id="size-on-respnsive-layout">' , timeformat($alink['date']) , '</div>
									</div>
									<div style="text-align:left;width:37.5%;" class="smalltext fullwidth-on-res-layout float-items">
										<div id="show-on-respnsive-layout" style="margin-top:0.5%;"><strong>'.$txt['tp-editarticleoptions2'].'</strong></div>
										<div id="size-on-respnsive-layout">
										<img style="cursor: pointer;" class="toggleActive" id="artActive' .$alink['id']. '" title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TPactive' , $alink['off']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-activate'].'"  />
										<a href="',$scripturl, '?page=',$alink['id'],'"><img title="'.$txt['tp-preview'].'" src="' .$settings['tp_images_url']. '/TPfilter.png" alt="" /></a>
										' , $alink['locked']==0 ?
										'<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article='.$alink['id']. '"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>' : '<img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-islocked'].'"  />' , '
										<img style="cursor: pointer;" class="toggleFront" id="artFront' .$alink['id']. '" title="'.$txt['tp-setfrontpage'].'" border="0" src="' .$settings['tp_images_url']. '/TPfront' , $alink['frontpage']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-setfrontpage'].'"  />
										<img style="cursor: pointer;" class="toggleSticky" id="artSticky' .$alink['id']. '" title="'.$txt['tp-setsticky'].'" border="0" src="' .$settings['tp_images_url']. '/TPsticky' , $alink['sticky']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setsticky'].'"  />
										<img style="cursor: pointer;" class="toggleLock" id="artLock' .$alink['id']. '" title="'.$txt['tp-setlock'].'" border="0" src="' .$settings['tp_images_url']. '/TPlock' , $alink['locked']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setlock'].'"  />
									<img style="cursor: pointer;" class="toggleFeatured" id="artFeatured' .$alink['id']. '" title="'.$txt['tp-featured'].'" border="0" src="' .$settings['tp_images_url']. '/TPflag' , $alink['featured']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-turnoff'].'"  />
									</div>
								</div>
								<div class="smalltext fullwidth-on-res-layout float-items" style="text-align:center;width:7%;text-transform:uppercase;">
									<div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=type"><strong>' , $txt['tp-type'] , '</strong></a></div>
									' , empty($alink['type']) ? 'html' : $alink['type'] , '
									</div>
									<div style="text-align:center;width:6%;" class="smalltext fullwidth-on-res-layout float-items">
										<div id="show-on-respnsive-layout"><strong>'.$txt['tp-delete'].'</strong></div>
										<a href="' . $scripturl . '?action=tpadmin;cu=-1;' . $context['session_var'] . '=' . $context['session_id'].';artdelete=' .$alink['id']. '" onclick="javascript:return confirm(\''.$txt['tp-articleconfirmdelete'].'\')">
										<img title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt="'.$txt['tp-delete'].'"  /></a>
									</div>
									<p class="clearthefloat"></p>
								</div>
								<p class="clearthefloat"></p>
							</div>
						</td>
						</tr>';
		}
			echo '
					</tbody>
				</table>';
			
			if( !empty($context['TPortal']['pageindex']))
				echo '
				<div class="middletext padding-div">
					<strong>'.$context['TPortal']['pageindex'].'</strong>
				</div>';

		if(isset($context['TPortal']['allcats']))
		{
			echo '	
				<div class="padding-div">
					<p style="text-align: center;">
					<select name="tp_article_cat">
						<option value="0">' . $txt['tp-createnew2'] . '</option>';
			foreach($context['TPortal']['allcats'] as $submg)
  					echo '
						<option value="'.$submg['id'].'">'. $txt['tp-approveto'] . $submg['name'].'</option>';
			echo '
					</select>
					<input name="tp_article_new" value="" size="40" /> &nbsp;
					</p>
				</div>';
		}
		echo '
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>';
	}
	else
		echo '
			<div class="windowbg2">
				<div class="padding-div">'.$txt['tp-nosubmissions'].'</div>
				<div class="padding-div">&nbsp;</div>
			</div>';

		echo '
		</div>
	</form>';
}

// Illustrative article icons to be used in category layouts Page
function template_articons()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

		tp_collectArticleIcons();

		echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" enctype="multipart/form-data" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="articons">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-adminicons7'] . '</h3></div>
		<div id="article-icons-pictures" class="admintable admin-area">
			<div class="information smalltext">' , $txt['tp-adminiconsinfo'] , '</div><div></div>
				<div class="windowbg noup padding-div">
				<div class="formtable"><br>
					<dl class="settings">
						<dt>
							', $txt['tp-adminicons6'], '<br>
						</dt>
						<dd>
							<input type="file" name="tp_article_newillustration" />
						</dd>
					</dl>
					<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
					<hr><br>';
					
				$alt=true;
		if(count($context['TPortal']['articons']['illustrations'])>0)
		{
			foreach($context['TPortal']['articons']['illustrations'] as $icon)
			{
				echo '
					<div class="smalltext padding-div" style="float:left;">
						<div class="article_icon" style="background: top right url(' . $icon['background'] . ') no-repeat;"></div>
						<input type="checkbox" name="artillustration'.$icon['id'].'" id="artillustration'.$icon['id'].'" style="vertical-align: top;" value="'.$icon['file'].'"  /> <label style="vertical-align: top;"  for="artiillustration'.$icon['id'].'">'.$txt['tp-remove'].'?</label>
					</div>
							';
				$alt = !$alt;
			}
		}

		echo '
					<p class="clearthefloat"></p>
					<hr>
				</div>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// Panel Settings Page
function template_panels()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="panels">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-panelsettings'] . '</h3></div>
			<div id="panels-admin" class="admintable admin-area">
			<div class="information smalltext">' , $txt['tp-helppanels'] , '</div><div></div>
			<div class="windowbg noup">
				<div class="formtable padding-div">
					<dl class="settings">
						<dt>
							<label for="field_name">', $txt['tp-hidebarsadminonly'], '</label>
						</dt>
						<dd>
							<input name="tp_hidebars_admin_only" type="radio" value="1" ' , $context['TPortal']['hidebars_admin_only']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hidebars_admin_only" type="radio" value="0" ' , $context['TPortal']['hidebars_admin_only']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<br><strong>'.$txt['tp-hidebarsall'].'</strong>
						</dt>
						<dt>
							<label for="field_name">', $txt['tp-hidebarsprofile'], '</label>
						</dt>
						<dd>
							<input name="tp_hidebars_profile" type="radio" value="1" ' , $context['TPortal']['hidebars_profile']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hidebars_profile" type="radio" value="0" ' , $context['TPortal']['hidebars_profile']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-hidebarspm'], '</label>
						</dt>
						<dd>
							<input name="tp_hidebars_pm" type="radio" value="1" ' , $context['TPortal']['hidebars_pm']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hidebars_pm" type="radio" value="0" ' , $context['TPortal']['hidebars_pm']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-hidebarsmemberlist'], '</label>
						</dt>
						<dd>
							<input name="tp_hidebars_memberlist" type="radio" value="1" ' , $context['TPortal']['hidebars_memberlist']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hidebars_memberlist" type="radio" value="0" ' , $context['TPortal']['hidebars_memberlist']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-hidebarssearch'], '</label>
						</dt>
						<dd>
							<input name="tp_hidebars_search" type="radio" value="1" ' , $context['TPortal']['hidebars_search']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hidebars_search" type="radio" value="0" ' , $context['TPortal']['hidebars_search']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-hidebarscalendar'], '</label>
						</dt>
						<dd>
							<input name="tp_hidebars_calendar" type="radio" value="1" ' , $context['TPortal']['hidebars_calendar']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hidebars_calendar" type="radio" value="0" ' , $context['TPortal']['hidebars_calendar']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>
					<dl class="settings">
					</dl>
						<dt>
							<label for="tp_hidebars_custom">', $txt['tp-hidebarscustom'], '</label>
						</dt>
						<dd>
							<textarea cols="40" style="width: 94%; height: 100px;" name="tp_hidebars_custom" id="tp_hidebars_custom">' . $context['TPortal']['hidebars_custom'].'</textarea>
						</dd>
					<dl class="settings">
					</dl>
						<dt>
							<label for="tp_padding">', $txt['tp-padding_between'], '</label>
						</dt>
						<dd>
							<input name="tp_padding" id="tp_padding" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['padding'], '">
							<span class="smalltext">'.$txt['tp-inpixels'].'</span>
						</dd>
					</dl>
					<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
				<hr>';

	$allpanels = array('left','right','top','center','front','lower','bottom');
	$alternate = true;

	if(function_exists('ctheme_tp_getblockstyles'))
		$types = ctheme_tp_getblockstyles();
	if(TP_SMF21)
		$types = tp_getblockstyles21();
	else
		$types = tp_getblockstyles();

	foreach($allpanels as $pa => $panl)
	{
		echo '
				<div id="panels-options" class="padding-div">
					<div class="title_bar"><h3 class="titlebg">';
		if($panl!='front')
			echo $txt['tp-'.$panl.'panel'].'</h3></div>
					<a name="'.$panl.'"></a><br>
					<img style="margin: 5px;" src="' .$settings['tp_images_url']. '/TPpanel_'.$panl.'' , $context['TPortal']['admin'.$panl.'panel'] ? '' : '_off' , '.png" alt="" />';
		else
			echo $txt['tp-'.$panl.'panel'].'</h3></div>
					<a name="'.$panl.'"></a><br>
					<img style="margin: 5px;" src="' .$settings['tp_images_url']. '/TPpanel_'.$panl.'.png" alt="" />';
		echo '
					<br>
				<div>
				<dl class="settings">';
		if($panl!='front')
		{
			if(in_array($panl, array("left","right")))
				echo '
					<dt>
						<label for="tp_'.$panl.'bar_width">'.$txt['tp-panelwidth'].':</label>
					</dt>
					<dd>
						<input name="tp_'.$panl.'bar_width" id="tp_'.$panl.'bar_width" size="5" maxsize="5" type="text" value="' , $context['TPortal'][$panl. 'bar_width'] , '"><br>
					</dd>';
				echo '
					<dt>
						'.$txt['tp-use'.$panl.'panel'].'
					</dt>
					<dd>
						<input name="tp_'.$panl.'panel" type="radio" value="1" ' , $context['TPortal']['admin'.$panl.'panel']==1 ? 'checked' : '' , '> '.$txt['tp-on'].'
						<input name="tp_'.$panl.'panel" type="radio" value="0" ' , $context['TPortal']['admin'.$panl.'panel']==0 ? 'checked' : '' , '> '.$txt['tp-off'].'<br>
					</dd>
					<dt>
						'.$txt['tp-hide_'.$panl.'bar_forum'].'
					</dt>
					<dd>
						<input name="tp_hide_'.$panl.'bar_forum" type="radio" value="1" ' , $context['TPortal']['hide_'.$panl.'bar_forum']==1 ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_hide_'.$panl.'bar_forum" type="radio" value="0" ' , $context['TPortal']['hide_'.$panl.'bar_forum']==0 ? 'checked' : '' , '> '.$txt['tp-no'].'
						<br><br>
					</dd>';
		}
		echo '
					<dt>
						'.$txt['tp-vertical'].'<br>
					</dt>
					<dd>
						<input name="tp_block_layout_'.$panl.'" type="radio" value="vert" ' , $context['TPortal']['block_layout_'.$panl]=='vert' ? 'checked' : '' , '>
					</dd>
					<dt>
						'.$txt['tp-horisontal'].'<br>
					</dt>
					<dd>
						<input name="tp_block_layout_'.$panl.'" type="radio" value="horiz" ' , $context['TPortal']['block_layout_'.$panl]=='horiz' ? 'checked' : '' , '>
					</dd>
					<dt>
						'.$txt['tp-horisontal2cols'].'<br>
					</dt>
					<dd>
						<input name="tp_block_layout_'.$panl.'" type="radio" value="horiz2" ' , $context['TPortal']['block_layout_'.$panl]=='horiz2' ? 'checked' : '' , '>
					</dd>
					<dt>
						'.$txt['tp-horisontal3cols'].'<br>
					</dt>
					<dd>
						<input name="tp_block_layout_'.$panl.'" type="radio" value="horiz3" ' , $context['TPortal']['block_layout_'.$panl]=='horiz3' ? 'checked' : '' , '>
					</dd>
					<dt>
						'.$txt['tp-horisontal4cols'].'<br>
					</dt>
					<dd>
						<input name="tp_block_layout_'.$panl.'" type="radio" value="horiz4" ' , $context['TPortal']['block_layout_'.$panl]=='horiz4' ? 'checked' : '' , '>
					</dd>
					<dt>
						'.$txt['tp-grid'].'<br>
					</dt>
					<dd>
						<input name="tp_block_layout_'.$panl.'" type="radio" value="grid" ' , $context['TPortal']['block_layout_'.$panl]=='grid' ? 'checked' : '' , '>
					</dd>
					<dt></dt>
					<dd>
						<p>
						<input name="tp_blockgrid_'.$panl.'" id="tp_blockgrid_'.$panl.'1" type="radio" value="colspan3" ' , $context['TPortal']['blockgrid_'.$panl]=='colspan3' ? 'checked' : '' , ' /><label for="tp_blockgrid_'.$panl.'1"><img align="middle" src="' .$settings['tp_images_url']. '/TPgrid1.png" alt="colspan3" /></label>
						<input name="tp_blockgrid_'.$panl.'" id="tp_blockgrid_'.$panl.'2" type="radio" value="rowspan1" ' , $context['TPortal']['blockgrid_'.$panl]=='rowspan1' ? 'checked' : '' , ' /><label for="tp_blockgrid_'.$panl.'2"><img align="middle" src="' .$settings['tp_images_url']. '/TPgrid2.png" alt="rowspan1" /></label></p>
					</dd>
					<dt>
						<label for="tp_blockwidth_'.$panl.'">'.$txt['tp-blockwidth'].':</label>
					</dt>
					<dd>
						<input name="tp_blockwidth_'.$panl.'" id="tp_blockwidth_'.$panl.'" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['blockwidth_'.$panl], '"><br>
					</dd>
					<dt>
						<label for="tp_blockheight_'.$panl.'">'.$txt['tp-blockheight'].':</label>
					</dt>
					<dd>
						<input name="tp_blockheight_'.$panl.'" id="tp_blockheight_'.$panl.'" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['blockheight_'.$panl], '">
					</dd>
				</dl>
				<div>'.$txt['tp-panelstylehelp'].'<br>
					<div class="smalltext">'.$txt['tp-panelstylehelp2'].'</div>
				</div><br>
				<div class="panels-optionsbg">';

			foreach($types as $blo => $bl)
				echo '
					<div class="panels-options">
						<div class="smalltext" style="padding: 4px 0;">
							<input name="tp_panelstyle_'.$panl.'" id="tp_panelstyle_'.$panl.''.$blo.'" type="radio" value="'.$blo.'" ' , $context['TPortal']['panelstyle_'.$panl]==$blo ? 'checked' : '' , '><label for="tp_panelstyle_'.$panl.''.$blo.'">
							<span' , $context['TPortal']['panelstyle_'.$panl]==$blo ? ' style="color: red;">' : '>' , $bl['class'] , '</span>
						</div></label>
						' . $bl['code_title_left'] . 'title'. $bl['code_title_right'].'
						' . $bl['code_top'] . 'body' . $bl['code_bottom'] . '
					</div>';
			echo '
				</div>
			</div>
			<hr>
		</div>';
		$alternate = !$alternate;
	}

		echo '
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// All the blocks (is this still used?)
function template_latestblocks()
{
	tp_latestblockcodes();
}

// Block Settings Page
function template_blocks()
{
	global $context, $settings, $txt, $scripturl;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="blocks">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-blocksettings'] . '</h3></div>
		<div id="all-the-blocks" class="admintable admin-area">
			<div class="windowbg noup padding-div">';

		$side=array('left','right','top','center','front','lower','bottom');
		$sd=array('lb','rb','tb','cb','fb','lob','bb');

		for($i=0 ; $i<7 ; $i++)
		{
			echo '
				<div class="title_bar"><h3 class="titlebg">
					<b>'.$txt['tp-'.$side[$i].'sideblocks'].'</b>
					<a href="'.$scripturl.'?action=tpadmin;addblock=' . $side[$i] . ';' . $context['session_var'] . '=' . $context['session_id'].'">
					<span style="float: right;">[' , $txt['tp-addblock'] , ']</span></a></h3>
				</div>';
			if(isset($context['TPortal']['admin' . $side[$i].'panel']) && $context['TPortal']['admin' . $side[$i].'panel']==0 && $side[$i]!='front')
				echo '
				<div class="windowbg2">
					<div class="tborder error smalltext" style="padding: 2px;"><a style="color: red;" href="' . $scripturl.'?action=tpadmin;sa=panels">',$txt['tp-panelclosed'] , '</a></div>
				</div>';

			if(isset($context['TPortal']['admin_'.$side[$i].'block']['blocks']))
				$tn=count($context['TPortal']['admin_'.$side[$i].'block']['blocks']);
			else
				$tn=0;

			if($tn>0)
				echo '
				<table class="table_grid tp_grid" style="width:100%";>
					<thead>
						<tr class="title_bar titlebg2">
						<th scope="col" class="blocks">
							<div>
								<div style="width:10%;" class="smalltext pos float-items"><strong>'.$txt['tp-pos'].'</strong></div>
								<div style="width:20%;" class="smalltext name float-items"><strong>'.$txt['tp-title'].'</strong></div>
								<div style="width:20%;" class="smalltext title-admin-area float-items" ><strong>'.$txt['tp-type'].'</strong></div>
								<div style="width:10%;" class="smalltext title-admin-area float-items" align="center"><strong>'.$txt['tp-activate'].'</strong></div>
								<div style="width:20%;" class="smalltext title-admin-area float-items" align="center"><strong>'.$txt['tp-move'].'</strong></div>
								<div style="width:10%;" class="smalltext title-admin-area float-items" align="center"><strong>'.$txt['tp-editsave'].'</strong></div>
								<div style="width:10%;" class="smalltext title-admin-area float-items" align="center"><strong>'.$txt['tp-delete'].'</strong></div>
								<p class="clearthefloat"></p>
							</div>
						</th>
						</tr>
					</thead>
					<tbody>';

			$n=0;
			if($tn>0)
			{
				foreach($context['TPortal']['admin_'.$side[$i].'block']['blocks'] as $lblock)
				{
					$newtitle = TPgetlangOption($lblock['lang'], $context['user']['language']);
					if(empty($newtitle))
						$newtitle = $lblock['title'];

					if(!$lblock['loose'])
						$class="windowbg3";
					else{
						$class='windowbg';
					}
					echo '
						<tr class="',$class,'">
						<td class="blocks">
						<div id="blocksDiv">
							<div style="width:10%;" class="adm-pos float-items">', ($lblock['editgroups']!='' && $lblock['editgroups']!='-2') ? '#' : '' ,'
								<input name="pos' .$lblock['id']. '" type="text" size="1em" maxlength="3" value="' .($n*10). '">
								<a name="block' .$lblock['id']. '"></a>';
					echo '
								<a class="tpbut" title="'.$txt['tp-sortdown'].'" href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';addpos=' .$lblock['id']. '"><img src="' .$settings['tp_images_url']. '/TPsort_down.png" value="' .(($n*10)+11). '" /></a>';

					if($n>0)
						echo '
								<a class="tpbut" title="'.$txt['tp-sortup'].'"  href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';subpos=' .$lblock['id']. '"><img src="' .$settings['tp_images_url']. '/TPsort_up.png" value="' .(($n*10)-11). '" /></a>';

					echo '
							</div>
						<div style="width:20%;max-width:100%;" class="adm-name float-items">
						     <input name="title' .$lblock['id']. '" type="text" size="25" value="' .html_entity_decode($newtitle). '">
						</div>
						<div style="width:20%;" class="fullwidth-on-res-layout block-opt float-items">
						    <div id="show-on-respnsive-layout">
								<div class="smalltext"><strong>'.$txt['tp-type'].'</strong></div>
							</div>
							<select size="1" name="type' .$lblock['id']. '">
								<option value="0"' ,$lblock['type']=='no' ? ' selected' : '' , '>', $txt['tp-blocktype0'] , '</option>
								<option value="1"' ,$lblock['type']=='userbox' ? ' selected' : '' , '>', $txt['tp-blocktype1'] , '</option>
								<option value="2"' ,$lblock['type']=='newsbox' ? ' selected' : '' , '>', $txt['tp-blocktype2'] , '</option>
								<option value="3"' ,$lblock['type']=='statsbox' ? ' selected' : '' , '>', $txt['tp-blocktype3'] , '</option>
								<option value="4"' ,$lblock['type']=='searchbox' ? ' selected' : '' , '>', $txt['tp-blocktype4'] , '</option>
								<option value="5"' ,$lblock['type']=='html' ? ' selected' : '' , '>', $txt['tp-blocktype5'] , '</option>
								<option value="6"' ,$lblock['type']=='onlinebox' ? ' selected' : '' , '>', $txt['tp-blocktype6'] , '</option>
								<option value="7"' ,$lblock['type']=='themebox' ? ' selected' : '' , '>', $txt['tp-blocktype7'] , '</option>
								<option value="9"' ,$lblock['type']=='catmenu' ? ' selected' : '' , '>', $txt['tp-blocktype9'] , '</option>
								<option value="10"' ,$lblock['type']=='phpbox' ? ' selected' : '' , '>', $txt['tp-blocktype10'] , '</option>
								<option value="11"' ,$lblock['type']=='scriptbox' ? ' selected' : '' , '>', $txt['tp-blocktype11'] , '</option>
								<option value="12"' ,$lblock['type']=='recentbox' ? ' selected' : '' , '>', $txt['tp-blocktype12'] , '</option>
								<option value="13"' ,$lblock['type']=='ssi' ? ' selected' : '' , '>', $txt['tp-blocktype13'] , '</option>
								<option value="14"' ,$lblock['type']=='module' ? ' selected' : '' , '>', $txt['tp-blocktype14'] , '</option>
								<option value="15"' ,$lblock['type']=='rss' ? ' selected' : '' , '>', $txt['tp-blocktype15'] , '</option>
								<option value="16"' ,$lblock['type']=='sitemap' ? ' selected' : '' , '>', $txt['tp-blocktype16'] , '</option>
								<option value="18"' ,$lblock['type']=='articlebox' ? ' selected' : '' , '>', $txt['tp-blocktype18'] , '</option>
								<option value="19"' ,$lblock['type']=='categorybox' ? ' selected' : '' , '>', $txt['tp-blocktype19'] , '</option>
								<option value="20"' ,$lblock['type']=='tpmodulebox' ? ' selected' : '' , '>', $txt['tp-blocktype20'] , '</option>';
			// theme hooks
			if(function_exists('ctheme_tp_blocks'))
			{
				ctheme_tp_blocks('listblocktypes2', $lblock['type']);
			}
					echo '	</select>
						</div>
						<div style="width:10%;" align="center" class="smalltext fullwidth-on-res-layout float-items">
						    <div id="show-on-respnsive-layout"><strong>'.$txt['tp-activate'].'</strong></div>
							&nbsp;<a name="'.$lblock['id'].'"></a>
						    <img class="toggleButton" id="blockonbutton' .$lblock['id']. '" title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TP' , $lblock['off']=='0' ? 'active2' : 'active1' , '.png" alt="'.$txt['tp-activate'].'"  />';
				echo '
						</div>
						<div style="width:20%;" align="center" class="smalltext fullwidth-on-res-layout float-items">
							<div id="show-on-respnsive-layout"><strong>'.$txt['tp-move'].'</strong></div>';

					switch($side[$i]){
						case 'left':
 							echo '
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockright=' .$lblock['id']. '"><img title="'.$txt['tp-moveright'].'" src="' .$settings['tp_images_url']. '/TPselect_right.png" alt="'.$txt['tp-moveright'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockcenter=' .$lblock['id']. '"><img title="'.$txt['tp-movecenter'].'" src="' .$settings['tp_images_url']. '/TPselect_upper.png" alt="'.$txt['tp-movecenter'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockfront=' .$lblock['id']. '"><img title="'.$txt['tp-movefront'].'" src="' .$settings['tp_images_url']. '/TPselect_front.png" alt="'.$txt['tp-movefront'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockbottom=' .$lblock['id']. '"><img title="'.$txt['tp-movedown'].'" src="' .$settings['tp_images_url']. '/TPselect_bottom.png" alt="'.$txt['tp-movedown'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocktop=' .$lblock['id']. '"><img title="'.$txt['tp-moveup'].'" src="' .$settings['tp_images_url']. '/TPselect_top.png" alt="'.$txt['tp-moveup'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocklower=' .$lblock['id']. '"><img title="'.$txt['tp-movelower'].'" src="' .$settings['tp_images_url']. '/TPselect_lower.png" alt="'.$txt['tp-movelower'].'" /></a>';
							break;
						case 'right':
 							echo '
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockleft=' .$lblock['id']. '"><img title="'.$txt['tp-moveleft'].'" src="' .$settings['tp_images_url']. '/TPselect_left.png" alt="'.$txt['tp-moveleft'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockcenter=' .$lblock['id']. '"><img title="'.$txt['tp-movecenter'].'" src="' .$settings['tp_images_url']. '/TPselect_upper.png" alt="'.$txt['tp-movecenter'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockfront=' .$lblock['id']. '"><img title="'.$txt['tp-movefront'].'" src="' .$settings['tp_images_url']. '/TPselect_front.png" alt="'.$txt['tp-movefront'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockbottom=' .$lblock['id']. '"><img title="'.$txt['tp-movedown'].'" src="' .$settings['tp_images_url']. '/TPselect_bottom.png" alt="'.$txt['tp-movedown'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocktop=' .$lblock['id']. '"><img title="'.$txt['tp-moveup'].'" src="' .$settings['tp_images_url']. '/TPselect_top.png" alt="'.$txt['tp-moveup'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocklower=' .$lblock['id']. '"><img title="'.$txt['tp-movelower'].'" src="' .$settings['tp_images_url']. '/TPselect_lower.png" alt="'.$txt['tp-movelower'].'" /></a>';
							break;
						case 'center':
							echo '
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockleft=' .$lblock['id']. '"><img title="'.$txt['tp-moveleft'].'" src="' .$settings['tp_images_url']. '/TPselect_left.png" alt="'.$txt['tp-moveleft'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockright=' .$lblock['id']. '"><img title="'.$txt['tp-moveright'].'" src="' .$settings['tp_images_url']. '/TPselect_right.png" alt="'.$txt['tp-moveright'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockfront=' .$lblock['id']. '"><img title="'.$txt['tp-movefront'].'" src="' .$settings['tp_images_url']. '/TPselect_front.png" alt="'.$txt['tp-movefront'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockbottom=' .$lblock['id']. '"><img title="'.$txt['tp-movedown'].'" src="' .$settings['tp_images_url']. '/TPselect_bottom.png" alt="'.$txt['tp-movedown'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocktop=' .$lblock['id']. '"><img title="'.$txt['tp-moveup'].'" src="' .$settings['tp_images_url']. '/TPselect_top.png" alt="'.$txt['tp-moveup'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocklower=' .$lblock['id']. '"><img title="'.$txt['tp-movelower'].'" src="' .$settings['tp_images_url']. '/TPselect_lower.png" alt="'.$txt['tp-movelower'].'" /></a>';
							break;
						case 'front':
							echo '
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockleft=' .$lblock['id']. '"><img title="'.$txt['tp-moveleft'].'" src="' .$settings['tp_images_url']. '/TPselect_left.png" alt="'.$txt['tp-moveleft'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockright=' .$lblock['id']. '"><img title="'.$txt['tp-moveright'].'" src="' .$settings['tp_images_url']. '/TPselect_right.png" alt="'.$txt['tp-moveright'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockcenter=' .$lblock['id']. '"><img title="'.$txt['tp-movecenter'].'" src="' .$settings['tp_images_url']. '/TPselect_upper.png" alt="'.$txt['tp-movecenter'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockbottom=' .$lblock['id']. '"><img title="'.$txt['tp-movedown'].'" src="' .$settings['tp_images_url']. '/TPselect_bottom.png" alt="'.$txt['tp-movedown'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocktop=' .$lblock['id']. '"><img title="'.$txt['tp-moveup'].'" src="' .$settings['tp_images_url']. '/TPselect_top.png" alt="'.$txt['tp-moveup'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocklower=' .$lblock['id']. '"><img title="'.$txt['tp-movelower'].'" src="' .$settings['tp_images_url']. '/TPselect_lower.png" alt="'.$txt['tp-movelower'].'" /></a>';
							break;
						case 'bottom':
							echo '
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockleft=' .$lblock['id']. '"><img title="'.$txt['tp-moveleft'].'" src="' .$settings['tp_images_url']. '/TPselect_left.png" alt="'.$txt['tp-moveleft'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockright=' .$lblock['id']. '"><img title="'.$txt['tp-moveright'].'" src="' .$settings['tp_images_url']. '/TPselect_right.png" alt="'.$txt['tp-moveright'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockcenter=' .$lblock['id']. '"><img title="'.$txt['tp-movecenter'].'" src="' .$settings['tp_images_url']. '/TPselect_upper.png" alt="'.$txt['tp-movecenter'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockfront=' .$lblock['id']. '"><img title="'.$txt['tp-movefront'].'" src="' .$settings['tp_images_url']. '/TPselect_front.png" alt="'.$txt['tp-movefront'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocktop=' .$lblock['id']. '"><img title="'.$txt['tp-moveup'].'" src="' .$settings['tp_images_url']. '/TPselect_top.png" alt="'.$txt['tp-moveup'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocklower=' .$lblock['id']. '"><img title="'.$txt['tp-movelower'].'" src="' .$settings['tp_images_url']. '/TPselect_lower.png" alt="'.$txt['tp-movelower'].'" /></a>';
							break;
						case 'top':
							echo '
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockleft=' .$lblock['id']. '"><img title="'.$txt['tp-moveleft'].'" src="' .$settings['tp_images_url']. '/TPselect_left.png" alt="'.$txt['tp-moveleft'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockright=' .$lblock['id']. '"><img title="'.$txt['tp-moveright'].'" src="' .$settings['tp_images_url']. '/TPselect_right.png" alt="'.$txt['tp-moveright'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockcenter=' .$lblock['id']. '"><img title="'.$txt['tp-movecenter'].'" src="' .$settings['tp_images_url']. '/TPselect_upper.png" alt="'.$txt['tp-movecenter'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockfront=' .$lblock['id']. '"><img title="'.$txt['tp-movefront'].'" src="' .$settings['tp_images_url']. '/TPselect_front.png" alt="'.$txt['tp-movefront'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockbottom=' .$lblock['id']. '"><img title="'.$txt['tp-movedown'].'" src="' .$settings['tp_images_url']. '/TPselect_bottom.png" alt="'.$txt['tp-movedown'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocklower=' .$lblock['id']. '"><img title="'.$txt['tp-movelower'].'" src="' .$settings['tp_images_url']. '/TPselect_lower.png" alt="'.$txt['tp-movelower'].'" /></a>';
							break;
						case 'lower':
 							echo '
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockleft=' .$lblock['id']. '"><img title="'.$txt['tp-moveleft'].'" src="' .$settings['tp_images_url']. '/TPselect_left.png" alt="'.$txt['tp-moveleft'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockright=' .$lblock['id']. '"><img title="'.$txt['tp-moveright'].'" src="' .$settings['tp_images_url']. '/TPselect_right.png" alt="'.$txt['tp-moveright'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockcenter=' .$lblock['id']. '"><img title="'.$txt['tp-movecenter'].'" src="' .$settings['tp_images_url']. '/TPselect_upper.png" alt="'.$txt['tp-movecenter'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockfront=' .$lblock['id']. '"><img title="'.$txt['tp-movefront'].'" src="' .$settings['tp_images_url']. '/TPselect_front.png" alt="'.$txt['tp-movefront'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockbottom=' .$lblock['id']. '"><img title="'.$txt['tp-movedown'].'" src="' .$settings['tp_images_url']. '/TPselect_bottom.png" alt="'.$txt['tp-movedown'].'" /></a>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blocktop=' .$lblock['id']. '"><img title="'.$txt['tp-moveup'].'" src="' .$settings['tp_images_url']. '/TPselect_top.png" alt="'.$txt['tp-moveup'].'" /></a>';
							break;
					}
					echo '
						</div>
						<div  style="width:10%;" align="center" class="smalltext fullwidth-on-res-layout float-items">
						    <div id="show-on-respnsive-layout"><strong>'.$txt['tp-editsave'].'</strong></div>
							<a href="' . $scripturl . '?action=tpadmin;blockedit=' .$lblock['id']. ';' . $context['session_var'] . '=' . $context['session_id'].'"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPmodify.png" alt="'.$txt['tp-edit'].'"  /></a>';
					echo '
							<input class="tpbut" style="height:16px; vertical-align:top;" type="image" src="' .$settings['tp_images_url']. '/TPsave.png" alt="'.$txt['tp-send'].'" value="�" onClick="javascript: submit();">';
					echo '
						</div>
	                    <div style="width:10%;" align="center" class="smalltext fullwidth-on-res-layout float-items">
						    <div id="show-on-respnsive-layout"><strong>'.$txt['tp-delete'].'</strong></div>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockdelete=' .$lblock['id']. '" onclick="javascript:return confirm(\''.$txt['tp-blockconfirmdelete'].'\')"><img title="'.$txt['tp-delete'].'"  border="0" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt="'.$txt['tp-delete'].'"  /></a>
						</div>
						<p class="clearthefloat"></p>
					</div>
					</td>
					</tr>';
					if($lblock['type']=='recentbox'){
						// check to see if it is numeric
						if(!is_numeric($lblock['body']))
							$lblock['body']='10';
						echo '
					<tr class="windowbg">
					<td class="blocks">
						<div>
							<div align="center" class="padding-div">
								'.$txt['tp-numberofrecenttopics'].'<input size=4 name="blockbody' .$lblock['id']. '" value="' .$lblock['body']. '">
							</div>
						</div>
					</td>
					</tr>';
					}
					elseif($lblock['type']=='ssi'){
						// SSI block..which function?
						if(!in_array($lblock['body'],array('recentpoll','toppoll','topposters','topboards','topreplies','topviews','calendar')))
							$lblock['body']='';
						echo '
					<tr class="windowbg">
					<td class="blocks">
						<div>
							<div align="center" class="padding-div">
								<select name="blockbody' .$lblock['id']. '">
									<option value="" ' , $lblock['body']=='' ? 'selected' : '' , '>' .$txt['tp-none-'].'</option>';
						echo '
									<option value="recentpoll" ' , $lblock['body']=='recentpoll' ? 'selected' : '' , '>'.$txt['tp-ssi-recentpoll'].'</option>';
						echo '
									<option value="toppoll" ' , $lblock['body']=='toppoll' ? 'selected' : '' , '>'.$txt['tp-ssi-toppoll'].'</option>';
						echo '
									<option value="topboards" ' , $lblock['body']=='topboards' ? 'selected' : '' , '>'.$txt['tp-ssi-topboards'].'</option>';
						echo '
									<option value="topposters" ' , $lblock['body']=='topposters' ? 'selected' : '' , '>'.$txt['tp-ssi-topposters'].'</option>';
						echo '
									<option value="topreplies" ' , $lblock['body']=='topreplies' ? 'selected' : '' , '>'.$txt['tp-ssi-topreplies'].'</option>';
						echo '
									<option value="topviews" ' , $lblock['body']=='topviews' ? 'selected' : '' , '>'.$txt['tp-ssi-topviews'].'</option>';
						echo '
									<option value="calendar" ' , $lblock['body']=='calendar' ? 'selected' : '' , '>'.$txt['tp-ssi-calendar'].'</option>
								</select>
							</div>
						</div>
					</td>
					</tr>';
					}
					elseif($lblock['type']=='rss'){
						echo '
					<tr class="windowbg">
					<td class="blocks">
						<div>
							<div align="center" class="padding-div">
								'.$txt['tp-rssblock'].'<input style="width: 75%;" name="blockbody' .$lblock['id']. '" value="' .$lblock['body']. '">
							</div>
						</div>
					</td>
					</tr>';
					}
					elseif($lblock['type']=='module'){
						echo '
					<tr class="windowbg">
					<td class="blocks">
						<div>
							<div align="center" class="padding-div">
								<select name="blockbody' .$lblock['id']. '">
									<option value="dl-stats" ' , $lblock['body']=='dl-stats' ? 'selected' : '' , '>' .$txt['tp-module1'].'</option>
									<option value="dl-stats2" ' , $lblock['body']=='dl-stats2' ? 'selected' : '' , '>' .$txt['tp-module2'].'</option>
									<option value="dl-stats3" ' , $lblock['body']=='dl-stats3' ? 'selected' : '' , '>' .$txt['tp-module3'].'</option>
									<option value="dl-stats4" ' , $lblock['body']=='dl-stats4' ? 'selected' : '' , '>' .$txt['tp-module4'].'</option>
									<option value="dl-stats5" ' , $lblock['body']=='dl-stats5' ? 'selected' : '' , '>' .$txt['tp-module5'].'</option>
									<option value="dl-stats6" ' , $lblock['body']=='dl-stats6' ? 'selected' : '' , '>' .$txt['tp-module6'].'</option>
									<option value="dl-stats7" ' , $lblock['body']=='dl-stats7' ? 'selected' : '' , '>' .$txt['tp-module7'].'</option>
									<option value="dl-stats8" ' , $lblock['body']=='dl-stats8' ? 'selected' : '' , '>' .$txt['tp-module8'].'</option>
									<option value="dl-stats9" ' , $lblock['body']=='dl-stats9' ? 'selected' : '' , '>' .$txt['tp-module9'].'</option>
								</select>
							</div>
						</div>
					</td>
					</tr>';
					}
					elseif($lblock['type']=='articlebox'){
						// check to see if it is numeric
						if(!is_numeric($lblock['body']))
							$lblock['body']='';
						echo '
					<tr class="windowbg">
					<td class="blocks">
						<div>
							<div align="center" class="padding-div">
								<select name="blockbody' .$lblock['id']. '">';
				foreach($context['TPortal']['edit_articles'] as $article){
					echo '
									<option value="'.$article['id'].'" ' ,$lblock['body']==$article['id'] ? ' selected' : '' ,' >'. html_entity_decode($article['subject'],ENT_QUOTES).'</option>';
				}
						echo '
								</select>
							</div>
						</div>
					</td>
					</tr>';
					}
					elseif($lblock['type']=='categorybox'){
						// check to see if it is numeric
						if(!is_numeric($lblock['body']))
							$lblock['body']='';

						echo '
					<tr class="windowbg">
					<td class="blocks">
						<div>
							<div align="center" class="padding-div">
								<select name="blockbody' .$lblock['id']. '">';
					if(isset($context['TPortal']['catnames']) && count($context['TPortal']['catnames'])>0)
					{
						foreach($context['TPortal']['catnames'] as $cat => $val)
						{
							echo '
									<option value="'.$cat.'" ' , $lblock['body']==$cat ? ' selected' : '' ,' >'.html_entity_decode($val).'</option>';
						}
					}
					echo '
								</select>
							</div>
						</div>
					</td>
					</tr>';
					}
					$n++;
				}
			}
			echo '
					</tbody>
				</table>';
		}
		echo '
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// Add block Page
function template_addblock()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	$side = $_GET['addblock'];
	$panels = array('','left','right','top','center','front','lower','bottom');

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" enctype="multipart/form-data" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="addblock">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-addblock'] . '</h3></div>
		<div id="add-block" class="admintable admin-area">
			<div class="windowbg2">
				<div class="formtable padding-div">
					<dl class="settings">
						<dt><h3>' , $txt['tp-title'] , ':</h3>
						</dt>
						<dd>
							<input type="input" name="tp_addblocktitle" size="50" value="" />
						</dd>
					</dl>
					<dl class="settings">
						<dt><h3>' , $txt['tp-choosepanel'] , '</h3></dt>
						<dd>
							<input type="radio" name="tp_addblockpanel" id="tp_addblockpanel1" value="1" ' , $side=='left' ? 'checked' : '' , ' /><label for="tp_addblockpanel1"">' . $txt['tp-leftpanel'] . '</label><br>
							<input type="radio" name="tp_addblockpanel" id="tp_addblockpanel2" value="2" ' , $side=='right' ? 'checked' : '' , ' /><label for="tp_addblockpanel2"">' . $txt['tp-rightpanel'] . '</label><br>
							<input type="radio" name="tp_addblockpanel" id="tp_addblockpanel6" value="6" ' , $side=='top' ? 'checked' : '' , ' /><label for="tp_addblockpanel6"">' . $txt['tp-toppanel'] . '</label><br>
							<input type="radio" name="tp_addblockpanel" id="tp_addblockpanel3" value="3" ' , $side=='upper' || $side=='center' ? 'checked' : '' , ' /><label for="tp_addblockpanel3"">' . $txt['tp-centerpanel'] . '</label><br>
							<input type="radio" name="tp_addblockpanel" id="tp_addblockpanel4" value="4" ' , $side=='front' ? 'checked' : '' , ' /><label for="tp_addblockpanel4"">' . $txt['tp-frontpanel'] . '</label><br>
							<input type="radio" name="tp_addblockpanel" id="tp_addblockpanel7" value="7" ' , $side=='lower' ? 'checked' : '' , ' /><label for="tp_addblockpanel7"">' . $txt['tp-lowerpanel'] . '</label><br>
							<input type="radio" name="tp_addblockpanel" id="tp_addblockpanel5" value="5" ' , $side=='bottom' ? 'checked' : '' , ' /><label for="tp_addblockpanel5"">' . $txt['tp-bottompanel'] . '</label><br>
						</dd>
					</dl>
					<hr>
					<dl class="settings">
						<dt><h3>' , $txt['tp-chooseblock'] , '</h3></dt>
						<dd>
							<div class="tp_largelist2">
								<input type="radio" name="tp_addblock" id="tp_addblock1" value="1" checked /><label for="tp_addblock1">' . $txt['tp-blocktype1'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock2" value="2" /><label for="tp_addblock2">' . $txt['tp-blocktype2'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock3" value="3" /><label for="tp_addblock3">' . $txt['tp-blocktype3'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock4" value="4" /><label for="tp_addblock4">' . $txt['tp-blocktype4'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock5" value="5" /><label for="tp_addblock5">' . $txt['tp-blocktype5'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock6" value="6" /><label for="tp_addblock6">' . $txt['tp-blocktype6'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock7" value="7" /><label for="tp_addblock7">' . $txt['tp-blocktype7'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock9" value="9" /><label for="tp_addblock9">' . $txt['tp-blocktype9'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock10" value="10" /><label for="tp_addblock10">' . $txt['tp-blocktype10'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock11" value="11" /><label for="tp_addblock11">' . $txt['tp-blocktype11'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock12" value="12" /><label for="tp_addblock12">' . $txt['tp-blocktype12'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock13" value="13" /><label for="tp_addblock13">' . $txt['tp-blocktype13'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock14" value="14" /><label for="tp_addblock14">' . $txt['tp-blocktype14'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock15" value="15" /><label for="tp_addblock15">' . $txt['tp-blocktype15'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock16" value="16" /><label for="tp_addblock16">' . $txt['tp-blocktype16'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock18" value="18" /><label for="tp_addblock18">' . $txt['tp-blocktype18'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock19" value="19" /><label for="tp_addblock19">' . $txt['tp-blocktype19'] . '</label><br>
								<input type="radio" name="tp_addblock" id="tp_addblock20" value="20" /><label for="tp_addblock20">' . $txt['tp-blocktype20'] . '</label><br>
							</div>
						</dd>
					</dl>';
			// theme hooks
			if(function_exists('ctheme_tp_blocks'))
			{
				ctheme_tp_blocks('listaddblocktypes');
			}
					echo '
					<dl class="settings">
						<dt><h3>' , $txt['tp-chooseblocktype'] , '</h3></dt>
						<dd>
							<div class="tp_largelist2">';

		foreach($context['TPortal']['blockcodes'] as $bc)
			echo '
						<div class="padding-div">
							<input type="radio" name="tp_addblock" id="tp_addblock' . $bc['name'].'" value="' . $bc['file']. '"  />
							<label for="tp_addblock' . $bc['name'].'"><b>' . $bc['name'].'</b> ' . $txt['tp-by'] . ' ' . $bc['author'] . '</b></label>
							<div style="margin: 4px 0; padding-left: 24px;" class="smalltext">' , $bc['text'] , '</div>
						</div>';

		echo '
							</div>
						</dd>
					</dl>
					<dl class="settings">
						<dt><h3 class="padding-div">' , $txt['tp-chooseblockcopy'] , '</h3></dt>
						<dd>
							<div class="tp_largelist2">';

		foreach($context['TPortal']['copyblocks'] as $bc)
			echo '
						<div class="padding-div">
							<input type="radio" name="tp_addblock" id="tp_addblock_' . $bc['id']. '" value="mb_' . $bc['id']. '"  /><label for="tp_addblock_' . $bc['id']. '">' . $bc['title'].' </label>[' . $panels[$bc['bar']] . ']
						</div>';

		echo ' 				</div>
						</dd>
					</dl>
				</div>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// Edit Block Page (including settings per block type)
function template_blockedit()
{
	global $context, $settings, $txt, $scripturl, $boardurl;

	$newtitle = html_entity_decode(TPgetlangOption($context['TPortal']['blockedit']['lang'], $context['user']['language']));
	if(empty($newtitle))
		$newtitle = html_entity_decode($context['TPortal']['blockedit']['title']);

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" enctype="multipart/form-data" action="' . $scripturl . '?action=tpadmin" method="post" onsubmit="submitonce(this);">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="blockedit">
		<input name="tpadmin_form_id" type="hidden" value="' . $context['TPortal']['blockedit']['id'] . '">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-editblock'] . '</h3></div>
		<div id="editblock" class="admintable admin-area">
			<div class="windowbg noup padding-div">
				<div class="formtable">
					<dl class="settings">
						<dt>
							<label for="field_name"><h4>', $txt['tp-status'], ':<img style="margin:0 1ex;" border="0" src="' . $settings['tp_images_url'] . '/TP' , $context['TPortal']['blockedit']['off']==0 ? 'green' : 'red' , '.png" alt="" /></h4></label>
						</dt>
						<dd>
							<input type="radio" value="0" name="tp_block_off"',$context['TPortal']['blockedit']['off']==0 ? ' checked="checked"' : '' ,' />'.$txt['tp-on'].'
							<input type="radio" value="1" name="tp_block_off"',$context['TPortal']['blockedit']['off']==1 ? ' checked="checked"' : '' ,' />'.$txt['tp-off'].'
						</dd>
					</dl>
					<dl class="settings">
						<dt><label for="tp_block_title"><b>'.$txt['tp-title'].':</b></label></dt>
						<dd>
							<input size=60 name="tp_block_title" id="tp_block_title" type="text" value="' .$newtitle. '"><br><br>
						</dd>
						<dt><b><label for="tp_block_type">',$txt['tp-type'].':</b></label></dt>
						<dd>
							<select size="1" onchange="document.getElementById(\'blocknotice\').style.display=\'\';" name="tp_block_type" id="tp_block_type">
								<option value="0"' ,$context['TPortal']['blockedit']['type']=='0' ? ' selected' : '' , '>', $txt['tp-blocktype0'] , '</option>
								<option value="1"' ,$context['TPortal']['blockedit']['type']=='1' ? ' selected' : '' , '>', $txt['tp-blocktype1'] , '</option>
								<option value="2"' ,$context['TPortal']['blockedit']['type']=='2' ? ' selected' : '' , '>', $txt['tp-blocktype2'] , '</option>
								<option value="3"' ,$context['TPortal']['blockedit']['type']=='3' ? ' selected' : '' , '>', $txt['tp-blocktype3'] , '</option>
								<option value="4"' ,$context['TPortal']['blockedit']['type']=='4' ? ' selected' : '' , '>', $txt['tp-blocktype4'] , '</option>
								<option value="5"' ,$context['TPortal']['blockedit']['type']=='5' ? ' selected' : '' , '>', $txt['tp-blocktype5'] , '</option>
								<option value="6"' ,$context['TPortal']['blockedit']['type']=='6' ? ' selected' : '' , '>', $txt['tp-blocktype6'] , '</option>
								<option value="7"' ,$context['TPortal']['blockedit']['type']=='7' ? ' selected' : '' , '>', $txt['tp-blocktype7'] , '</option>
								<option value="9"' ,$context['TPortal']['blockedit']['type']=='9' ? ' selected' : '' , '>', $txt['tp-blocktype9'] , '</option>
								<option value="10"' ,$context['TPortal']['blockedit']['type']=='10' ? ' selected' : '' , '>', $txt['tp-blocktype10'] , '</option>
								<option value="11"' ,$context['TPortal']['blockedit']['type']=='11' ? ' selected' : '' , '>', $txt['tp-blocktype11'] , '</option>
								<option value="12"' ,$context['TPortal']['blockedit']['type']=='12' ? ' selected' : '' , '>', $txt['tp-blocktype12'] , '</option>
								<option value="13"' ,$context['TPortal']['blockedit']['type']=='13' ? ' selected' : '' , '>', $txt['tp-blocktype13'] , '</option>
								<option value="14"' ,$context['TPortal']['blockedit']['type']=='14' ? ' selected' : '' , '>', $txt['tp-blocktype14'] , '</option>
								<option value="15"' ,$context['TPortal']['blockedit']['type']=='15' ? ' selected' : '' , '>', $txt['tp-blocktype15'] , '</option>
								<option value="16"' ,$context['TPortal']['blockedit']['type']=='16' ? ' selected' : '' , '>', $txt['tp-blocktype16'] , '</option>
								<option value="18"' ,$context['TPortal']['blockedit']['type']=='18' ? ' selected' : '' , '>', $txt['tp-blocktype18'] , '</option>
								<option value="19"' ,$context['TPortal']['blockedit']['type']=='19' ? ' selected' : '' , '>', $txt['tp-blocktype19'] , '</option>
								<option value="20"' ,$context['TPortal']['blockedit']['type']=='20' ? ' selected' : '' , '>', $txt['tp-blocktype20'] , '</option>
								<br><br>';
		// theme hooks
		if(function_exists('ctheme_tp_blocks'))
		{
			ctheme_tp_blocks('listblocktypes');
		}

		echo '
							</select>
						</dd>
					</dl>
					<div class="padding-div"><input type="submit" class="button button_submit" value="' . $txt['tp-send'] . '" /></div>
					<div>
						<div id="blocknotice" class="smallpadding error middletext" style="display: none;">' , $txt['tp-blocknotice'] , '</a></div>
					</div>
					<div class="windowbg2 padding-div">
					 <div>';
// Block types: 5 (BBC code), 10 (PHP Code) and 11 (HTML & Javascript code)
			if($context['TPortal']['blockedit']['type']=='5' || $context['TPortal']['blockedit']['type']=='10' || $context['TPortal']['blockedit']['type']=='11')
			{
				if($context['TPortal']['blockedit']['type']=='11')
				{
					echo '</div><div><b>',$txt['tp-body'],'</b> <br><textarea style="width: 94%;" name="tp_block_body" id="tp_block_body" rows="15" cols="40" wrap="auto">' , $context['TPortal']['blockedit']['body'], '</textarea>';
				}
				elseif($context['TPortal']['blockedit']['type']=='5')
				{
						echo '
						</div><div>';
					TP_bbcbox($context['TPortal']['editor_id']);
				}
				else
						echo '<b>'.$txt['tp-body'].'</b>';

				if($context['TPortal']['blockedit']['type']=='10')
				{
					echo '
						</div><div>
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
							<input type="checkbox" value="' . $context['TPortal']['blockedit']['id'] . '" name="blockcode_overwrite" /> ' , $txt['tp-blockcodes_overwrite'] , '</p>
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
					<hr>
					<dl class="settings">
						<dt>'.$txt['tp-numberofrecenttopics'].'</dt>
						<dd>
							<input style="width: 50px;" name="tp_block_body" value="' .$context['TPortal']['blockedit']['body']. '">
						</dd>
						<dt>Board Id\'s (comma separated, blank will include all)</dt>
						<dd>
							<input name="tp_block_var2" size="20" type="text" value="' , $context['TPortal']['blockedit']['var2'] ,'">
						</dd>';
//						<dt><h4>Boards:</h4></dt>
//						<dd>
//							<div class="tp_largelist">';
//				$a=1;
//				if(!empty($context['TPortal']['boards']))
//				{
//					echo '<input type="checkbox" name="boardtype' , $a, '" value="-1" id="allboards" ' , in_array('-1', $context['TPortal']['blockedit']['access2']['board']) ? 'checked="checked"' : '' , '><label for="allboards"> '.$txt['tp-allboards'].'</label><br><br>';
//					$a++;
//					foreach($context['TPortal']['boards'] as $bb)
//					{
//						echo '
//								<input type="checkbox" name="boardtype' , $a, '" id="boardtype' , $a, '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['access2']['board']) ? 'checked="checked"' : '' , '><label for="boardtype' , $a, '"> '.$bb['name'].'</label><br>';
//						$a++;
//					}
//				}
//				echo '
//							 </div>
//						</dd>
				echo '
						<dt>Include or exclude boards</dt>
						<dd>
							<input name="tp_block_var3" type="radio" value="1" ' , ($context['TPortal']['blockedit']['var3']=='1' || $context['TPortal']['blockedit']['var3']=='') ? ' checked' : '' ,'> Include boards<br>
							<input name="tp_block_var3" type="radio" value="0" ' , $context['TPortal']['blockedit']['var3']=='0' ? 'checked' : '' ,'> Exclude boards
						</dd>
						<dt>' . $txt['tp-rssblock-showavatar'].'</dt>
						<dd>
							<input name="tp_block_var1" type="radio" value="1" ' , ($context['TPortal']['blockedit']['var1']=='1' || $context['TPortal']['blockedit']['var1']=='') ? ' checked' : '' ,'>'.$txt['tp-yes'].'
							<input name="tp_block_var1" type="radio" value="0" ' , $context['TPortal']['blockedit']['var1']=='0' ? ' checked' : '' ,'>'.$txt['tp-no'].'
						</dd>
					</dl>';
			}
// Block type: SSI functions
			elseif($context['TPortal']['blockedit']['type']=='13'){
				if(!in_array($context['TPortal']['blockedit']['body'],array('recentpoll','toppoll','topposters','topboards','topreplies','topviews','calendar')))
					$context['TPortal']['blockedit']['body']='';
						echo '
						</div><div>';
						echo '
						<hr><dl class="settings">
						<dt></dt>
						<dd>
							<input name="tp_block_body" type="radio" value="" ' , $context['TPortal']['blockedit']['body']=='' ? 'checked' : '' , '> ' .$txt['tp-none-']. '<br>
							<input name="tp_block_body" type="radio" value="topboards" ' , $context['TPortal']['blockedit']['body']=='topboards' ? 'checked' : '' , '> '.$txt['tp-ssi-topboards']. '<br>
							<input name="tp_block_body" type="radio" value="topposters" ' , $context['TPortal']['blockedit']['body']=='topposters' ? 'checked' : '' , '> '.$txt['tp-ssi-topposters']. '<br>
							<input name="tp_block_body" type="radio" value="topreplies" ' , $context['TPortal']['blockedit']['body']=='topreplies' ? 'checked' : '' , '> '.$txt['tp-ssi-topreplies']. '<br>
							<input name="tp_block_body" type="radio" value="topviews" ' , $context['TPortal']['blockedit']['body']=='topviews' ? 'checked' : '' , '> '.$txt['tp-ssi-topviews']. '<br>
							<input name="tp_block_body" type="radio" value="calendar" ' , $context['TPortal']['blockedit']['body']=='calendar' ? 'checked' : '' , '> '.$txt['tp-ssi-calendar']. '<br>
						</dd>
					</dl>';
			}
// Block type: TP module
			elseif($context['TPortal']['blockedit']['type']=='20'){
					echo '
						</div><div>
						<hr><dl class="settings">
						<dt></dt>
						<dd>';
				foreach($context['TPortal']['tpmodules']['blockrender'] as $tpm)
					echo '
						<br><input name="tp_block_var1" type="radio" value="' . $tpm['id'] . '" ' , $context['TPortal']['blockedit']['var1']==$tpm['id'] ? 'checked' : '' , '>'.$tpm['name'];
					echo '
						</dd>
					</dl>'; 
			}
// Block type: Article / Download functions
			elseif($context['TPortal']['blockedit']['type']=='14'){
				// Module block...choose module and module ID , check if module is active
						echo '
						</div><div>
						<hr><dl class="settings">
						<dt></dt>
						<dd>
							<input name="tp_block_body" type="radio" value="dl-stats" ' , $context['TPortal']['blockedit']['body']=='dl-stats' ? 'checked' : '' , '> '.$txt['tp-module1'].'<br>
							<input name="tp_block_body" type="radio" value="dl-stats2" ' , $context['TPortal']['blockedit']['body']=='dl-stats2' ? 'checked' : '' , '> '.$txt['tp-module2'].'<br>
							<input name="tp_block_body" type="radio" value="dl-stats3" ' , $context['TPortal']['blockedit']['body']=='dl-stats3' ? 'checked' : '' , '> '.$txt['tp-module3'].'<br>
							<input name="tp_block_body" type="radio" value="dl-stats4" ' , $context['TPortal']['blockedit']['body']=='dl-stats4' ? 'checked' : '' , '> '.$txt['tp-module4'].'<br>
							<input name="tp_block_body" type="radio" value="dl-stats5" ' , $context['TPortal']['blockedit']['body']=='dl-stats5' ? 'checked' : '' , '> '.$txt['tp-module5'].'<br>
							<input name="tp_block_body" type="radio" value="dl-stats6" ' , $context['TPortal']['blockedit']['body']=='dl-stats6' ? 'checked' : '' , '> '.$txt['tp-module6'].'<br>
							<input name="tp_block_body" type="radio" value="dl-stats7" ' , $context['TPortal']['blockedit']['body']=='dl-stats7' ? 'checked' : '' , '> '.$txt['tp-module7'].'<br>
							<input name="tp_block_body" type="radio" value="dl-stats8" ' , $context['TPortal']['blockedit']['body']=='dl-stats8' ? 'checked' : '' , '> '.$txt['tp-module8'].'<br>
							<input name="tp_block_body" type="radio" value="dl-stats9" ' , $context['TPortal']['blockedit']['body']=='dl-stats9' ? 'checked' : '' , '> '.$txt['tp-module9'].'<br>
						</dd>
					</dl>';
			}
// Block type: Stats
			elseif($context['TPortal']['blockedit']['type']=='3'){
				echo '
					</div><div>
					<hr><dl class="settings">
						<dt>'.$txt['tp-showuserbox'].'</dt>';

				if(isset($context['TPortal']['userbox']['avatar']) && $context['TPortal']['userbox']['avatar'])
					echo '<input name="tp_userbox_options0" type="hidden" value="avatar">';
				if(isset($context['TPortal']['userbox']['logged']) && $context['TPortal']['userbox']['logged'])
					echo '<input name="tp_userbox_options1" type="hidden" value="logged">';
				if(isset($context['TPortal']['userbox']['time']) && $context['TPortal']['userbox']['time'])
					echo '<input name="tp_userbox_options2" type="hidden" value="time">';
				if(isset($context['TPortal']['userbox']['unread']) && $context['TPortal']['userbox']['unread'])
					echo '<input name="tp_userbox_options3" type="hidden" value="unread">';

				echo '	<dd>
							<input name="tp_userbox_options4" id="tp_userbox_options4" type="checkbox" value="stats" ', (isset($context['TPortal']['userbox']['stats']) && $context['TPortal']['userbox']['stats']) ? 'checked' : '' , '><label for="tp_userbox_options4"> '.$txt['tp-userbox5'].'</label><br>
							<input name="tp_userbox_options5" id="tp_userbox_options5" type="checkbox" value="online" ', (isset($context['TPortal']['userbox']['online']) && $context['TPortal']['userbox']['online']) ? 'checked' : '' , '><label for="tp_userbox_options5"> '.$txt['tp-userbox6'].'</label><br>
							<input name="tp_userbox_options6" id="tp_userbox_options6" type="checkbox" value="stats_all" ', (isset($context['TPortal']['userbox']['stats_all']) && $context['TPortal']['userbox']['stats_all']) ? 'checked' : '' , '><label for="tp_userbox_options6"> '.$txt['tp-userbox7'].'</label>
						</dd>
					</dl>';
			}
// Block type: User
			elseif($context['TPortal']['blockedit']['type']=='1'){
				echo '
					</div><div>
					<hr><dl class="settings">
						<dt>'. $txt['tp-showuserbox2'].'</dt>
						<dd>
							<input name="tp_userbox_options0" id="tp_userbox_options0" type="checkbox" value="avatar" ', (isset($context['TPortal']['userbox']['avatar']) && $context['TPortal']['userbox']['avatar']) ? 'checked' : '' , '><label for="tp_userbox_options0"> '.$txt['tp-userbox1'].'</label><br>
							<input name="tp_userbox_options1" id="tp_userbox_options1" type="checkbox" value="logged" ', (isset($context['TPortal']['userbox']['logged']) && $context['TPortal']['userbox']['logged']) ? 'checked' : '' , '><label for="tp_userbox_options1"> '.$txt['tp-userbox2'].'</label><br>
							<input name="tp_userbox_options2" id="tp_userbox_options2" type="checkbox" value="time" ', (isset($context['TPortal']['userbox']['time']) && $context['TPortal']['userbox']['time']) ? 'checked' : '' , '><label for="tp_userbox_options2"> '.$txt['tp-userbox3'].'</label><br>
							<input name="tp_userbox_options3" id="tp_userbox_options3" type="checkbox" value="unread" ', (isset($context['TPortal']['userbox']['unread']) && $context['TPortal']['userbox']['unread']) ? 'checked' : '' , '><label for="tp_userbox_options3"> '.$txt['tp-userbox4'].'</label><br>
						</dd>
					</dl>';

				if(isset($context['TPortal']['userbox']['stats']) && $context['TPortal']['userbox']['stats'])
					echo '<input name="tp_userbox_options4" type="hidden" value="stats">';
				if(isset($context['TPortal']['userbox']['online']) && $context['TPortal']['userbox']['online'])
					echo '<input name="tp_userbox_options5" type="hidden" value="online">';
				if(isset($context['TPortal']['userbox']['stats_all']) && $context['TPortal']['userbox']['stats_all'])
					echo '<input name="tp_userbox_options6" type="hidden" value="stats_all">';
			}
// Block type: RSS
			elseif($context['TPortal']['blockedit']['type']=='15'){
				echo '
					<hr><dl class="settings">
						<dt>' .	$txt['tp-rssblock'] . '</dt>
						<dd>
							<input style="width: 95%" name="tp_block_body" value="' .$context['TPortal']['blockedit']['body']. '">
						</dd>
						<dt>' , $txt['tp-rssblock-useutf8'].'</dt>
						<dd>
							<input name="tp_block_var1" type="radio" value="1" ' , $context['TPortal']['blockedit']['var1']=='1' ? ' checked' : '' ,'>'.$txt['tp-utf8'].'<br>
							<input name="tp_block_var1" type="radio" value="0" ' , ($context['TPortal']['blockedit']['var1']=='0' || $context['TPortal']['blockedit']['var1']=='') ? ' checked' : '' ,'>'.$txt['tp-iso'].'
						</dd>
						<dt>' . $txt['tp-rssblock-showonlytitle'].'</dt>
						<dd>
							<input name="tp_block_var2" type="radio" value="1" ' , $context['TPortal']['blockedit']['var2']=='1' ? ' checked' : '' ,'>'.$txt['tp-yes'].'
							<input name="tp_block_var2" type="radio" value="0" ' , ($context['TPortal']['blockedit']['var2']=='0' || $context['TPortal']['blockedit']['var2']=='') ? ' checked' : '' ,'>'.$txt['tp-no'], '
						</dd>
						<dt>' . $txt['tp-rssblock-maxwidth'].'</dt>
						<dd>
							<input name="tp_block_var3" type="text" value="' , $context['TPortal']['blockedit']['var3'],'">
						</dd>
						<dt>' . $txt['tp-rssblock-maxshown'].'</dt>
						<dd>
							<input name="tp_block_var4" type="text" value="' , $context['TPortal']['blockedit']['var4'],'">
						</dd>
					</dl>
				</div>';
			}
// Block type: Sitemap
			elseif($context['TPortal']['blockedit']['type']=='16'){
				echo '
					</div><div>
					<hr>
					<dl class="settings">
						<dt>'.$txt['tp-sitemapmodules'].'<ul class="disc"></dt>
						<dd>';
				if($context['TPortal']['show_download']=='1')
					echo '<li>&nbsp;'.$txt['tp-dldownloads'].'</li>';
				echo '</ul>
						</dd>
					</dl>';
			}
// Block type: Single Article
			elseif($context['TPortal']['blockedit']['type']=='18'){
				// check to see if it is numeric
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$lblock['body']='';

				echo '
					</div><div>
					<dl class="settings">
						<dt>',$txt['tp-showarticle'],'</dt>
						<dd>
							<select name="tp_block_body">';
				foreach($context['TPortal']['edit_articles'] as $art => $article ){
					echo '<option value="'.$article['id'].'" ' , $context['TPortal']['blockedit']['body']==$article['id'] ? ' selected="selected"' : '' ,' >'.html_entity_decode($article['subject']).'</option>';
				}
				echo '</select>
						</dd>
					</dl>';
			}
// Block type: Themes
			elseif($context['TPortal']['blockedit']['type']=='7') {
				// get the ids
				$myt=array();
				$thems=explode(",",$context['TPortal']['blockedit']['body']);
				foreach($thems as $g => $gh)
				{
					$wh=explode("|",$gh);
					$myt[]=$wh[0];
				}
					echo '
						<hr><input type="hidden" name="blockbody' .$context['TPortal']['blockedit']['id']. '" value="' .$context['TPortal']['blockedit']['body'] . '" />
						<div style="padding: 5px;">
							<div style="max-height: 25em; overflow: auto;">
							<input name="tp_theme-1" type="hidden" value="-1">
							<input type="hidden" value="1" name="tp_tpath-1">';
				foreach($context['TPthemes'] as $tema)
				{
					if(TP_SMF21) {
						echo '
							<img class="theme_icon" alt="*" src="'.$tema['path'].'/thumbnail.png" /> <input name="tp_theme'.$tema['id'].'" type="checkbox" value="'.$tema['name'].'"';
						}
					else {
						echo '
							<img class="theme_icon" alt="*" src="'.$tema['path'].'/thumbnail.gif" /> <input name="tp_theme'.$tema['id'].'" type="checkbox" value="'.$tema['name'].'"';
						}
					if(in_array($tema['id'],$myt))
						echo ' checked';
					echo '>'.$tema['name'].'<input type="hidden" value="'.$tema['path'].'" name="tp_path'.$tema['id'].'"><br>';
				}
				echo '
					</div>
					<br>
					<input type="checkbox" onclick="invertAll(this, this.form, \'tp_theme\');" /> '.$txt['tp-checkall'],'
				';
			}
// Block type: Articles in a Category
			elseif($context['TPortal']['blockedit']['type']=='19') {
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$lblock['body']='';
				if(!is_numeric($context['TPortal']['blockedit']['var1']))
					$lblock['var1']='15';
				if($context['TPortal']['blockedit']['var1']=='0')
					$lblock['var1']='15';

				echo '
					<hr><dl class="settings">
						<dt>'.$txt['tp-showcategory'].'</dt>
						<dd>
							<select name="tp_block_body">';
				foreach($context['TPortal']['catnames'] as $cat => $catname){
					echo '
								<option value="'.$cat.'" ' , $context['TPortal']['blockedit']['body']==$cat ? ' selected' : '' ,' >'.html_entity_decode($catname).'</option>';
				}
				echo '
							</select>
						</dd>
						<dt>'.$txt['tp-catboxheight'].'</dt>
						<dd>
							<input name="tp_block_var1" size="4" type="text" value="' , $context['TPortal']['blockedit']['var1'] ,'"> em
						</dd>
						<dt>'.$txt['tp-catboxauthor'].'</dt>
						<dd>
							<input name="tp_block_var2" type="radio" value="1" ' , $context['TPortal']['blockedit']['var2']=='1' ? 'checked' : '' ,'> ', $txt['tp-yes'], '<br>
							<input name="tp_block_var2" type="radio" value="0" ' , $context['TPortal']['blockedit']['var2']=='0' ? 'checked' : '' ,'> ', $txt['tp-no'], '
						</dd>
					</dl>';
			}
// Block type: Menu
			elseif($context['TPortal']['blockedit']['type']=='9') {
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$lblock['body']='0';

				echo '
					<hr><dl class="settings">
						<dt>',$txt['tp-showmenus'],'</dt>
						<dd>
							<select name="tp_block_body">';
				foreach($context['TPortal']['menus'] as $men){
					echo '
						<option value="'.$men['id'].'" ' , $context['TPortal']['blockedit']['body']==$men['id'] ? ' selected' : '' ,' >'.$men['name'].'</option>';
				}
				echo '
					</select>
						</dd>
						<dt>',$txt['tp-showmenustyle'],' </dt>
						<dd>
							<input name="tp_block_var1" type="radio" value="0" ' , ($context['TPortal']['blockedit']['var1']=='' || $context['TPortal']['blockedit']['var1']=='0') ? ' checked' : '' ,' > <img src="'.$settings['tp_images_url'].'/TPdivider2.png" alt="" /><br>
							<input name="tp_block_var1" type="radio" value="1" ' , ($context['TPortal']['blockedit']['var1']=='1') ? ' checked' : '' ,' > <img src="'.$settings['tp_images_url'].'/bullet3.png" alt="" /><br>
							<input name="tp_block_var1" type="radio" value="2" ' , ($context['TPortal']['blockedit']['var1']=='2') ? ' checked' : '' ,' > '.$txt['tp-none-'].'
						</dd>
					</dl>';
			}
// Block type: Online
			elseif($context['TPortal']['blockedit']['type']=='6') {
				echo '
					<hr><dl class="settings">
						<dt>'.$txt['tp-rssblock-showavatar'].'</dt>
						<dd>
							<input name="tp_block_var1" type="radio" value="1" ' , ($context['TPortal']['blockedit']['var1']=='1' || $context['TPortal']['blockedit']['var1']=='') ? ' checked' : '' ,'>'.$txt['tp-yes'].' <input name="tp_block_var1" type="radio" value="0" ' , $context['TPortal']['blockedit']['var1']=='0' ? ' checked' : '' ,'>'.$txt['tp-no'].'
						</dd>
					</dl>';
			}
			// theme hooks
			elseif($context['TPortal']['blockedit']['type']>'50' && function_exists('ctheme_tp_blocks'))
			{
				ctheme_tp_blocks('blockoptions');
			}
			else
				echo '
			</div><div>';

			echo '
					</div>
				</div>
				<div><hr>
					<div>'.$txt['tp-blockstylehelp'].':<br>
						<div class="smalltext">'.$txt['tp-blockstylehelp2'].'</div>
					</div>				
					<br><input name="tp_block_var5" id="tp_block_var5" type="radio" value="99" ' , $context['TPortal']['blockedit']['var5']=='99' ? 'checked' : '' , '><span' , $context['TPortal']['blockedit']['var5']=='99' ? ' style="color: red;">' : '><label for="tp_block_var5">' , $txt['tp-blocksusepaneltyle'] , '</label></span>
				<div>
				<div class="panels-optionsbg">';

			if(function_exists('ctheme_tp_getblockstyles'))
				$types = ctheme_tp_getblockstyles();
			if(TP_SMF21)
				$types = tp_getblockstyles21();
			else
				$types = tp_getblockstyles();
			foreach($types as $blo => $bl)
				echo '
					<div class="panels-options">
						<div>
							<input name="tp_block_var5" id="tp_block_var5'.$blo.'" type="radio" value="'.$blo.'" ' , $context['TPortal']['blockedit']['var5']==$blo ? 'checked' : '' , '><label for="tp_block_var5'.$blo.'"><span' , $context['TPortal']['blockedit']['var5']==$blo ? ' style="color: red;">' : '>' , $bl['class'] , '</span></label>
						</div>
						' . $bl['code_title_left'] . 'title'. $bl['code_title_right'].'
						' . $bl['code_top'] . 'body' . $bl['code_bottom'] . '
					</div>';
			echo '
						</div>
					</div>
				</div>
				<br>
					<dl class="settings">
						<dt>'.$txt['tp-blockframehelp'].':</dt>
						<dd>
							<input name="tp_block_frame" id="useframe" type="radio" value="theme" ' , $context['TPortal']['blockedit']['frame']=='theme' ? 'checked' : '' , '><label for="useframe"> '.$txt['tp-useframe'].'</label><br>
							<input name="tp_block_frame" id="useframe2" type="radio" value="frame" ' , $context['TPortal']['blockedit']['frame']=='frame' ? 'checked' : '' , '><label for="useframe2"> '.$txt['tp-useframe2'].' </label><br>
							<input name="tp_block_frame" id="usetitle" type="radio" value="title" ' , $context['TPortal']['blockedit']['frame']=='title' ? 'checked' : '' , '><label for="usetitle"> '.$txt['tp-usetitle'].' </label></br>
							<input name="tp_block_frame" id="noframe" type="radio" value="none" ' , $context['TPortal']['blockedit']['frame']=='none' ? 'checked' : '' , '><label for="noframe"> '.$txt['tp-noframe'].'</label>
						</dd>
					</dl>
					<br>
					<dl class="settings">
						<dt> '.$txt['tp-allowupshrink'].': </dt>
						<dd>
							<input name="tp_block_visible" id="allowupshrink" type="radio" value="1" ' , ($context['TPortal']['blockedit']['visible']=='' || $context['TPortal']['blockedit']['visible']=='1') ? 'checked' : '' , '><label for="allowupshrink"> '.$txt['tp-allowupshrink'].'</label><br>
							<input name="tp_block_visible" id="notallowupshrink" type="radio" value="0" ' , ($context['TPortal']['blockedit']['visible']=='0') ? 'checked' : '' , '><label for="notallowupshrink"> '.$txt['tp-notallowupshrink'].'</label>
						</dd>
					</dl>
					<br>
					<dl class="settings">
						<dt> '.$txt['tp-membergrouphelp'].'</dt>
						<dd><div>
							  <div class="tp_largelist">';
			// loop through and set membergroups
			$tg=explode(',',$context['TPortal']['blockedit']['access']);

			if( !empty($context['TPmembergroups']))
			{
				foreach($context['TPmembergroups'] as $mg)
				{
					if($mg['posts']=='-1' && $mg['id']!='1'){
						echo '<input name="tp_group'.$mg['id'].'" id="tp_group'.$mg['id'].'" type="checkbox" value="'.$context['TPortal']['blockedit']['id'].'"';
						if(in_array($mg['id'],$tg))
							echo ' checked';
						echo '><label for="tp_group'.$mg['id'].'"> '.$mg['name'].'</label><br>';
					}
				}
			}
			// if none is chosen, have a control value
			echo '
							</div>
								<input id="checkallmg" type="checkbox" onclick="invertAll(this, this.form, \'tp_group\');" /><label for="checkallmg">'.$txt['tp-checkall'].'<label><br>
							</div>
						</dd>
					</dl>';
			//edit membergroups
			echo '
					<dl class="settings">
						<dt>'.$txt['tp-editgrouphelp'].'</dt>
						<dd>
							<div>
								<div class="tp_largelist">';
			$tg=explode(',',$context['TPortal']['blockedit']['editgroups']);
			foreach($context['TPmembergroups'] as $mg){
				if($mg['posts']=='-1' && $mg['id']!='1' && $mg['id']!='-1' && $mg['id']!='0'){
					echo '<input name="tp_editgroup'.$mg['id'].'" id="tp_editgroup'.$mg['id'].'" type="checkbox" value="'.$context['TPortal']['blockedit']['id'].'"';
					if(in_array($mg['id'],$tg))
						echo ' checked';
					echo '><label for="tp_editgroup'.$mg['id'].'"> '.$mg['name'].'</label><br>';
				}
			}
			// if none is chosen, have a control value
			echo '				</div><input id="checkalleditmg" type="checkbox" onclick="invertAll(this, this.form, \'tp_editgroup\');" /><label for="checkalleditmg">'.$txt['tp-checkall'];
			echo '				<label><br>
							</div>
						</dd>
					</dl>
					<dl class="settings">
						<dt><label for="field_name">'.$txt['tp-langhelp'].'</label></dt>
						<dd>
							<div>';
			foreach($context['TPortal']['langfiles'] as $langlist => $lang){
				if($lang!='')
					echo '<input size="50" name="tp_lang_'.$lang.'" type="text" value="' , !empty($context['TPortal']['blockedit']['langfiles'][$lang]) ? html_entity_decode($context['TPortal']['blockedit']['langfiles'][$lang], ENT_QUOTES) : html_entity_decode($context['TPortal']['blockedit']['title'],ENT_QUOTES) , '"> '. $lang.'<br>';
			}
			echo '			</div>
						<br></dd>
						<dt>' . $txt['tp-lang'] . ':';

				// alert if the settings is off, supply link if allowed
				if(empty($context['TPortal']['uselangoption']))
				{
					echo '
					<p class="error">', $txt['tp-uselangoption2'] , ' ' , allowedTo('tp_settings') ? '<a href="'.$scripturl.'?action=tpadmin;sa=settings#uselangoption">['. $txt['tp-settings'] .']</a>' : '' , '</p>';
				}						
				echo '
					</dt>
					<dd>';

				$a=1;
				foreach($context['TPortal']['langfiles'] as $bb => $lang)
				{
					echo '
							<input type="checkbox" name="langtype' . $a . '" id="langtype' . $a . '" value="'.$lang.'" ' , in_array($lang, $context['TPortal']['blockedit']['access2']['lang']) ? 'checked="checked"' : '' , '><label for="langtype' . $a . '"> '.$lang.'</label><br>';
					$a++;
				}
				echo ' </dd>
					</dl>
				</div>';
		if($context['TPortal']['blockedit']['bar']!=4)
		{
			// extended visible options
				echo '
					<div class="admintable">
						<div>'.$txt['tp-access2help'].'</div>
						<div id="collapse-options">
						', tp_hidepanel('blockopts', true) , '
				' , empty($context['TPortal']['blockedit']['access22']) ? '<div class="tborder error" style="margin: 1em 0; padding: 4px 4px 4px 20px;">' . $txt['tp-noaccess'] . '</div>' : '' , '
						<fieldset class="tborder" id="blockopts" ' , in_array('blockopts',$context['tp_panels']) ? ' style="display: none;"' : '' , '>
						<input type="hidden" name="TPadmin_blocks_vo" value="'.$mg['id'].'" />';

				if(!empty($context['TPortal']['return_url']))
					echo '
							<input type="hidden" name="fromblockpost" value="'.$context['TPortal']['return_url'].'" />';
					echo '
					<dl class="settings">
						<dt><h4>' . $txt['tp-actions'] . ':</h4></dt>
						<dd>
							<div>
								<input name="actiontype1" id="actiontype1" type="checkbox" value="allpages" ' ,in_array('allpages',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype1"> '.$txt['tp-allpages'].'</label><br><br>
								<input name="actiontype2" id="actiontype2" type="checkbox" value="frontpage" ' ,in_array('frontpage',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype2"> '.$txt['tp-frontpage'].'</label><br>
								<input name="actiontype3" id="actiontype3" type="checkbox" value="forumall" ' ,in_array('forumall',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype3"> '.$txt['tp-forumall'].'</label><br>
								<input name="actiontype4" id="actiontype4" type="checkbox" value="forum" ' ,in_array('forum',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype4"> '.$txt['tp-forumfront'].'</label><br>
								<input name="actiontype5" id="actiontype5" type="checkbox" value="recent" ' ,in_array('recent',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype5"> '.$txt['tp-recent'].'</label><br>
								<input name="actiontype6" id="actiontype6" type="checkbox" value="unread" ' ,in_array('unread',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype6"> '.$txt['tp-unread'].'</label><br>
								<input name="actiontype7" id="actiontype7" type="checkbox" value="unreadreplies" ' ,in_array('unreadreplies',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype7"> '.$txt['tp-unreadreplies'].'</label><br>
								<input name="actiontype8" id="actiontype8" type="checkbox" value="profile" ' ,in_array('profile',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype8"> '.$txt['profile'].'</label><br>
								<input name="actiontype9" id="actiontype9" type="checkbox" value="pm" ' ,in_array('pm',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype9"> '.$txt['pm_short'].'</label><br>
								<input name="actiontype10" id="actiontype10" type="checkbox" value="calendar" ' ,in_array('calendar',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype10"> '.$txt['calendar'].'</label><br>
								<input name="actiontype11" id="actiontype11" type="checkbox" value="admin" ' ,in_array('admin',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype11"> '.$txt['admin'].'</label><br>
								<input name="actiontype12" id="actiontype12" type="checkbox" value="login" ' ,in_array('login',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype12"> '.$txt['login'].'</label><br>
								<input name="actiontype13" id="actiontype13" type="checkbox" value="logout" ' ,in_array('logout',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype13"> '.$txt['logout'].'</label><br>
								<input name="actiontype14" id="actiontype14" type="checkbox" value="register" ' ,in_array('register',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype14"> '.$txt['register'].'</label><br>
								<input name="actiontype15" id="actiontype15" type="checkbox" value="post" ' ,in_array('post',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype15"> '.$txt['post'].'</label><br>
								<input name="actiontype16" id="actiontype16" type="checkbox" value="stats" ' ,in_array('stats',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype16"> '.$txt['tp-stats'].'</label><br>
								<input name="actiontype17" id="actiontype17" type="checkbox" value="search" ' ,in_array('search',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype17"> '.$txt['search'].'</label><br>
								<input name="actiontype18" id="actiontype18" type="checkbox" value="mlist" ' ,in_array('mlist',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '><label for="actiontype18"> '.$txt['tp-memberlist'].'</label><br><br>';
					// add the custom ones you added
					$count=19;
					foreach($context['TPortal']['blockedit']['access2']['action'] as $po => $p) {
						if(!in_array($p, array('allpages','frontpage','forumall','forum','recent','unread','unreadreplies','profile','pm','calendar','admin','login','logout','register','post','stats','search','mlist')))
						{
							echo '<input name="actiontype'.$count.'" id="actiontype'.$count.'" type="checkbox" value="'.$p.'" checked="checked"><label for="name="actiontype'.$count.'">'.$p.'</label><br>';
							$count++;
						}
					}
					echo '
							<p><label for="custotype0">'.$txt['tp-customactions'].'</label></p>
								<input style="width: 90%;" type="text" name="custotype0" id="custotype0" value="">
								</div>
							</dd>
					</dl>
					<dl class="settings">
						<dt><h4>Boards:</h4></dt>
						<dd>
							<div class="tp_largelist">';
				$a=1;
				if(!empty($context['TPortal']['boards']))
				{
					echo '<input type="checkbox" name="boardtype' , $a, '" value="-1" id="allboards" ' , in_array('-1', $context['TPortal']['blockedit']['access2']['board']) ? 'checked="checked"' : '' , '><label for="allboards"> '.$txt['tp-allboards'].'</label><br><br>';
					$a++;
					foreach($context['TPortal']['boards'] as $bb)
					{
						echo '
								<input type="checkbox" name="boardtype' , $a, '" id="boardtype' , $a, '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['access2']['board']) ? 'checked="checked"' : '' , '><label for="boardtype' , $a, '"> '.$bb['name'].'</label><br>';
						$a++;
					}
				}
				echo '
							 </div>
						</dd>
					</dl>
					<dl class="settings">
						<dt><h4>' . $txt['tp-articles'] . ':</h4></dt>
						<dd>
							 <div class="tp_largelist">';
				$a=1;
				foreach($context['TPortal']['edit_articles'] as $bb)
				{
					echo '
								<input type="checkbox" name="articletype' , $a , '" id="articletype' , $a , '" value="'.$bb['id'].'" ' ,in_array($bb['id'], $context['TPortal']['blockedit']['access2']['page']) ? 'checked="checked"' : '' , '><label for="articletype' , $a , '"> '.html_entity_decode($bb['subject'],ENT_QUOTES).'</label><br>';
					$a++;
				}
				// if none is chosen, have a control value
				echo '</div><input type="checkbox" id="togglearticle" onclick="invertAll(this, this.form, \'articletype\');" /><label for="togglearticle">'.$txt['tp-checkall'];
				echo '</label><br>
						</dd>
					</dl>
					</dl>
					<dl class="settings">
						<dt><h4>' . $txt['tp-artcat'] . ':</h4></dt>
						<dd>
						    <div class="tp_largelist">';
				$a=1;
				if(isset($context['TPortal']['article_categories']))
				{
					foreach($context['TPortal']['article_categories'] as $bb)
					{
						echo '
								<input type="checkbox" name="categorytype' . $a . '" id="categorytype' . $a . '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['access2']['cat']) ? 'checked="checked"' : '' , '><label for="categorytype' . $a . '"> '.$bb['name'].'</label><br>';
						$a++;
					}
				}
				// if none is chosen, have a control value
				echo '</div><input type="checkbox" id="togglecat" onclick="invertAll(this, this.form, \'categorytype\');" /><label for="togglecat">'.$txt['tp-checkall'];
				echo '</label><br>
						</dd>
					</dl>
					<dl class="settings">
						<dt><h4>' . $txt['tp-dlmanager'] . ':</h4></dt>
						<dd>
							<div class="tp_largelist">';
				$a=1;
				if(!empty($context['TPortal']['dlcats']))
				{
					$a++;
					foreach($context['TPortal']['dlcats'] as $bb)
					{
						echo '
								<input type="checkbox" name="dlcattype' , $a, '" id="dlcattype' , $a, '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['access2']['dlcat']) ? 'checked="checked"' : '' , '><label for="dlcattype' , $a, '"> '.$bb['name'].'</label><br>';
						$a++;
					}
				}
				// if none is chosen, have a control value
				echo '		</div><input id="toggledlcat" type="checkbox" onclick="invertAll(this, this.form, \'dlcattype\');" /><label for="toggledlcat">'.$txt['tp-checkall'];
				echo '</label<br>
						</dd>
					</dl>
					<dl class="settings">
						<dt><h4>'.$txt['tp-modules'].'</h4></dt>
						<dd>
							<div>';
				$a=1;
				if(!empty($context['TPortal']['tpmods']))
				{
					$a++;
					foreach($context['TPortal']['tpmods'] as $bb)
					{
						echo '
								<input type="checkbox" name="tpmodtype' , $a, '" id="tpmodtype' , $a, '" value="'.$bb['subquery'].'" ' , in_array($bb['subquery'], $context['TPortal']['blockedit']['access2']['tpmod']) ? 'checked="checked"' : '' , '><label for="tpmodtype' , $a, '"> '.$bb['title'].'</label><br>';
						$a++;
					}
				}
				echo '
							</div>
						</dd>
					</dl>
				</div>
			</div>
		</fieldset>';
		}
			echo '
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// Block Access Page
function template_blockoverview()
{
	global $context, $settings, $txt, $boardurl, $scripturl;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="blockoverview">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-blockoverview'] . '</h3></div><div></div>
		<div id="blocks-overview" class="admintable admin-area windowbg noup">
			<div class="content">';

		$side=array('','left','right','top','center','front','lower','bottom');

		if(allowedTo('tp_blocks') && isset($context['TPortal']['blockoverview']))
		{
			// list by block or by membergroup?
			if(!isset($_GET['grp']))
			{
				foreach($context['TPortal']['blockoverview'] as $block)
				{
					echo '
				<div class="tp_col8">
					<p><a href="' . $scripturl . '?action=tpadmin;blockedit='.$block['id'].';' . $context['session_var'] . '=' . $context['session_id'].'" title="'.$txt['tp-edit'].'"><b>' . $block['title'] . '</a></b> ( ' . $txt['tp-blocktype' . $block['type']] . ' | ' . $txt['tp-' .$side[$block['bar']]] . ')</p>
					<hr /><br>
					<div id="tp'.$block['id'].'" style="overflow: hidden;">
						<input type="hidden" value="control" name="' . rand(10000,19999) .'tpblock'.$block['id'].'" />';

					foreach($context['TPmembergroups'] as $grp)
						echo '
						<input type="checkbox" id="tpb' . $block['id'] . '' . $grp['id'].'" value="' . $grp['id'].'" ' , in_array($grp['id'],$block['access']) ? 'checked="checked" ' : '' , ' name="' . rand(10000,19999) .'tpblock'.$block['id'].'" /><label for="tpb' . $block['id'] . '' . $grp['id'].'"> '. $grp['name'].'</label><br>';

					echo '
					</div>
					<br><input id="toggletpb'.$block['id'].'" type="checkbox" onclick="invertAll(this, this.form, \'tpb'.$block['id'].'\');" /><label for="toggletpb'.$block['id'].'">'.$txt['tp-checkall'],'</label><br><br>
				</div>';
				}
			}
		}
		echo '
			</div>
			<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
		</div>
	</form>';
}

// Menu Manager Page
function template_menubox()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

		// is it a single menu?
		if(isset($_GET['mid']))
		{
			$mid=is_numeric($_GET['mid']) ? $_GET['mid'] : 0;
			echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="menuitems">
		<input name="tp_menuid" type="hidden" value="'.$mid.'">
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-menumanager'].' - '.$context['TPortal']['menus'][$mid]['name'] . '  <a href="' . $scripturl . '?action=tpadmin;sa=addmenu;mid=' , (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $_GET['mid'] : 0 , '">['.$txt['tp-addmenuitem'].']</a></h3></div>
		<div id="menu-manager" class="admintable admin-area bigger-width">
		<div class="information smalltext">' , $txt['tp-helpmenuitems'] , '</div><div></div>
			<div class="windowbg noup padding-div">
			<table class="table_grid tp_grid" style="width:100%";>
				<thead>
					<tr class="title_bar titlebg2">
					<th scope="col" class="menuitems">			
						<div class="font-strong">
							<div style="width:7%;" class="pos float-items">'.$txt['tp-pos'].'</div>
							<div style="width:15%;" class="name float-items">'.$txt['tp-title'].'</div>
							<div style="width:10%;" class="title-admin-area float-items">'.$txt['tp-type'].'</div>
							<div style="width:12%;" class="title-admin-area float-items" align="center">'.$txt['tp-on'].' '.$txt['tp-off'].' '.$txt['tp-edit'].' </div>
							<div style="width:15%;" class="title-admin-area float-items">'.$txt['tp-item'].'</div>
							<div style="width:18%;" class="title-admin-area float-items">'.$txt['tp-sub_item'].'</div>
							<div style="width:15%;" class="title-admin-area float-items">'.$txt['tp-sitemap_on'].'</div>
							<div style="width:7%;" class="title-admin-area float-items">'.$txt['tp-delete'].' </div>
							<p class="clearthefloat"></p>
						</div>
					</th>
					</tr>
				</thead>
				<tbody>';
			if(!empty($context['TPortal']['menubox'][$mid]))
			{
				$tn=sizeof($context['TPortal']['menubox'][$mid]);
				$n=1;
				foreach($context['TPortal']['menubox'][$mid] as $lbox){
					echo '
					<tr class="windowbg' , $lbox['off']=='0' ? '' : '' , '">
					<td class="blocks">
						<div>
							<div style="width:7%;" class="adm-pos float-items">
								<input name="menu_pos' .$lbox['id']. '" type="text" size="4" value="' . (empty($lbox['subtype']) ? '0' :  $lbox['subtype']) . '">
							</div>
							<div style="width:15%;" class="adm-name float-items">
								<a href="' . $scripturl . '?action=tpadmin;linkedit=' .$lbox['id']. ';' . $context['session_var'] . '=' . $context['session_id'].'">' .$lbox['name']. '</a>
							</div>
							<a href="" class="clickme">'.$txt['tp-more'].'</a>
							<div class="box" style="width:78%;float:left;">
								<div style="width:13%;" class="smalltext fullwidth-on-res-layout float-items">
									<div id="show-on-respnsive-layout"><strong>'.$txt['tp-type'].'</strong></div>';
				if($lbox['type']=='cats')
					echo $txt['tp-category'];
				elseif($lbox['type']=='arti')
					echo $txt['tp-article'];
				elseif($lbox['type']=='head')
					echo $txt['tp-header'];
				elseif($lbox['type']=='spac')
					echo $txt['tp-spacer'];
				elseif($lbox['type']=='menu')
					echo $txt['tp-menu'];
				else
					echo $txt['tp-link'];

				echo '
								</div>
								<div style="width:15%;" class="smalltext fullwidth-on-res-layout float-items" align="center">
									<div id="show-on-respnsive-layout"><strong>'.$txt['tp-on'].' '.$txt['tp-off'].' '.$txt['tp-edit'].'</strong></div>
									<a href="' . $scripturl . '?action=tpadmin;linkon=' .$lbox['id']. ';mid=' , (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $_GET['mid'] : 0 , ';' . $context['session_var'] . '=' . $context['session_id'].'"><img title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TPgreen' , $lbox['off']!=0 ? '2' : '' , '.png" alt="'.$txt['tp-activate'].'"  /></a>
									<a href="' . $scripturl . '?action=tpadmin;linkoff=' .$lbox['id']. ';mid=' , (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $_GET['mid'] : 0 , ';' . $context['session_var'] . '=' . $context['session_id'].'"><img title="'.$txt['tp-deactivate'].'" border="0" src="' .$settings['tp_images_url']. '/TPred' , $lbox['off']==0 ? '2' : '' , '.png" alt="'.$txt['tp-deactivate'].'"  /></a>
									<a href="' . $scripturl . '?action=tpadmin;linkedit=' .$lbox['id']. ';mid=' , (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $_GET['mid'] : 0 , ';' . $context['session_var'] . '=' . $context['session_id'].'"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>
								</div>
								<div style="width:19.2%; overflow:hidden;" class="smalltext fullwidth-on-res-layout float-items">
									<div id="show-on-respnsive-layout"><strong>'.$txt['tp-item'].'</strong></div>
									<div id="size-on-respnsive-layout">';
					if($lbox['type']=='cats'){
						// is it a cats ( category)?
						foreach($context['TPortal']['editcats'] as $bmg)
						{
							if($lbox['IDtype']==$bmg['id'])
								echo html_entity_decode($bmg['name']);
						}
					}
					elseif($lbox['type']=='arti'){
						// or a arti (article)?
						foreach($context['TPortal']['edit_articles'] as $bmg)
						{
							if($lbox['IDtype']==$bmg['id'])
								echo html_entity_decode($bmg['subject']);
						}
					}
					elseif($lbox['type']=='head'){
						// or a head (header)?
						echo ' ';
					}
					elseif($lbox['type']=='spac'){
						echo ' ';
					}
                    elseif($lbox['type']=='menu'){
						echo '<span title="'.$lbox['IDtype'].'">'.$lbox['IDtype'].'</span>';
					}
					else{
						// its a link then.
						echo '<span title="'.$lbox['IDtype'].'">'.$lbox['IDtype'].'</span>';
					}

					echo '
									</div>
								</div>
									<div style="width:23%;" class="smalltext fullwidth-on-res-layout float-items">';			      
					if($lbox['type']!=='menu'){
						echo '
										<div id="show-on-respnsive-layout"><strong>'.$txt['tp-sub_item'].'</strong></div>
										<input name="menu_sub' .$lbox['id']. '" type="radio" value="0" ' , $lbox['sub']=='0' ? 'checked' : '' ,'>
										<input name="menu_sub' .$lbox['id']. '" type="radio" value="1" ' , $lbox['sub']=='1' ? 'checked' : '' ,'>
										<input name="menu_sub' .$lbox['id']. '" type="radio" value="2" ' , $lbox['sub']=='2' ? 'checked' : '' ,'>
										<input name="menu_sub' .$lbox['id']. '" type="radio" value="3" ' , $lbox['sub']=='3' ? 'checked' : '' ,'>';
					}
					 else {
						echo '
										<div id="show-on-respnsive-layout">'.$txt['tp-sub_item'].'</div>
										'.$txt['tp-none-'].'';
					}
						echo '			
									</div>
									<div style="width:23%;" class="smalltext fullwidth-on-res-layout float-items">
										<div id="show-on-respnsive-layout"><strong>'.$txt['tp-sitemap_on'].'</strong></div>
										<input name="tp_menu_sitemap' .$lbox['id']. '" type="radio" value="1" ' , in_array($lbox['id'],$context['TPortal']['sitemap']) ? 'checked' : '' ,'>' . $txt['tp-yes'] .'
										<input name="tp_menu_sitemap' .$lbox['id']. '" type="radio" value="0" ' , !in_array($lbox['id'],$context['TPortal']['sitemap']) ? 'checked' : '' ,'> ' . $txt['tp-no'] . '
									</div>
									<div style="width:5%;" class="smalltext fullwidth-on-res-layout float-items">
										<div id="show-on-respnsive-layout"><strong>'.$txt['tp-delete'].'</strong></div>
										<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';linkdelete=' .$lbox['id']. '" onclick="javascript:return confirm(\''.$txt['tp-suremenu'].'\')"><img title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt="'.$txt['tp-delete'].'"  /></a>
									</div>
									<p class="clearthefloat"></p>
								</div>
								<p class="clearthefloat"></p>
							</div>
						</td>
						</tr>';
					$n++;
				}
			}
		echo '
				</tbody>
			</table>';
		}

// Menu Manager Page: single menus
		else
		{
			echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="menus">
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-menumanager'].' <a href="' . $scripturl . '?action=tpadmin;sa=addmenu;fullmenu">['.$txt['tp-addmenu'].']</a></h3></div>
		<div id="single-menus" class="admintable admin-area">
			<div class="windowbg noup padding-div">';

			foreach($context['TPortal']['menus'] as $mbox)
			{
			if($mbox['id']==0)
				echo '
				<table class="table_grid tp_grid" style="width:100%";>
				<tbody>
					<tr class="windowbg">
					<td class="menu">
						<div style="padding-div"><br>
							<dl class="settings">
								<dt>
									<strong><i>' . $txt['tp-internal'] . '</i></strong><br>
								</dt>
								<dd>
									<a href="' . $scripturl . '?action=tpadmin;sa=menubox;mid=0"><img height="16px" title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPedit.png" alt="'.$txt['tp-edit'].'"  /><strong>' .$txt['tp-edit'].'</strong></a><br>
								</dd>
							</dl>
						</div>
					</td>
					</tr>';
			else
				echo '
					<tr class="windowbg">
					<td class="menu">
						<div style="padding-div"><br>
							<dl class="settings">
								<dt>
									<input name="tp_menu_name' .$mbox['id']. '" type="text" size="40" value="' .$mbox['name']. '"><br>
								</dt>
								<dd>
									<a href="' . $scripturl . '?action=tpadmin;sa=menubox;mid=' .$mbox['id']. '"><img height="16px"; title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPedit.png" alt="'.$txt['tp-edit'].'"  /><strong> '.$txt['tp-edit'].'</strong></a> &nbsp;&nbsp;&nbsp;
									<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';linkdelete=' .$mbox['id']. ';fullmenu" onclick="javascript:return confirm(\''.$txt['tp-suremenu'].'\')"><img height="16px" title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt="'.$txt['tp-delete'].'"  /> <strong>'.$txt['tp-delete'].'</strong></a><br>
								</dd>
							</dl>
						</div>
					</td>
					</tr>';
			}
		}
		echo '
				</tbody>
				</table>
				<div><br>
					<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
			</div>
		</div>
	</form>';
}

// Add Menu / Add Menu item Page
function template_addmenu()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	// Add a Menu item Page
	if(!isset($_GET['fullmenu'])) {
		// Just default this for now...
		$context['TPortal']['editmenuitem']['sub']      = 0;
		$context['TPortal']['editmenuitem']['newlink']  = '0';
		$context['TPortal']['editmenuitem']['type']     = 'cats';
		$context['TPortal']['editmenuitem']['position'] = 'home';
		$context['TPortal']['editmenuitem']['menuicon'] = 'tinyportal/menu_tpmenu.png';

		echo '
	<form accept-charset="', $context['character_set'], '" name="tpadminmenu" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="menuaddsingle">
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-addmenu'].'</h3></div>
		<input name="newmenu" type="hidden" value="1">
		<input name="tp_menu_menuid" type="hidden" value="' , (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $_GET['mid'] : 0 , '">';

		template_menucore();
	}

	// Add Menu Page
	else {
		// get the menu ID
		if(isset($_GET['mid']) && is_numeric($_GET['mid']))
			$mid = $_GET['mid'];
		else
			$mid = 0;

		echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="menuadd">
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-addmenu'].'</h3></div>
		<div id="add-menu" class="admintable admin-area">
			<div class="windowbg noup padding-div">
				<dl class="settings">
					<dt><label for="tp_menu_title"><h4>'.$txt['tp-title'].'</h4><label>
					</dt>
					<dd><input name="tp_menu_title" id="tp_menu_title" type="text" size="40" value=""><br>
					</dd>
				</dl>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
	}
}

// Edit menu item Page
function template_linkmanager()
{
    global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

		echo '    
	<form accept-charset="', $context['character_set'], '" name="tpadminmenu" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="singlemenuedit">
		<input name="tpadmin_form_id" type="hidden" value="'.$context['TPortal']['editmenuitem']['id'].'">
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-editmenu'].'</h3></div>';

    template_menucore();
}

function template_menucore()
{
    global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language, $forum_version;

    echo'
		<div id="new-item" class="admintable admin-area edit-menu-item">
		<div class="information smalltext">' , $txt['tp-helpmenuitems'] , '</div><div></div>
		<div class="windowbg noup padding-div">
			<dl class="settings">
				<dt>
					<label for="tp_title"><b>'.$txt['tp-title'].':</b><label>
				</dt>
				<dd>
					<input name="tp_menu_name" type="text" size="40" value="', isset($context['TPortal']['editmenuitem']['name']) ? $context['TPortal']['editmenuitem']['name'] : ''  ,'">
				</dd>
			</dl>	
			<dl class="settings">
				<dt>
					<label for="tp_menu_name"><b>'.$txt['tp-type'].':</b><label>
				</dt>
				<dd>
					<select size="1" name="tp_menu_type" id="tp_menu_type">
						<option value="cats" ', $context['TPortal']['editmenuitem']['type']=='cats' ? 'selected' : '', '>'.$txt['tp-category'].'</option>
						<option value="arti" ', $context['TPortal']['editmenuitem']['type']=='arti' ? 'selected' : '', '>'.$txt['tp-article'].'</option>
						<option value="link" ', $context['TPortal']['editmenuitem']['type']=='link' ? 'selected' : '', '>'.$txt['tp-link'].'</option>
						<option value="head" ', $context['TPortal']['editmenuitem']['type']=='head' ? 'selected' : '', '>'.$txt['tp-header'].'</option>
						<option value="spac" ', $context['TPortal']['editmenuitem']['type']=='spac' ? 'selected' : '', '>'.$txt['tp-spacer'].'</option>';
				// check for menu button in createmenuitem
				if (isset($_GET['mid']))
					{
					if($_GET['mid']==0)
						{
						echo '
                            <option value="menu" ', $context['TPortal']['editmenuitem']['type']=='menu' ? 'selected' : '', '>'.$txt['tp-menu'].'</option>';
						}
					}
				// check for menu button in editmenuitem
				elseif ($context['TPortal']['editmenuitem']['menuID']==0)
					{
					echo '
                            <option value="menu" ', $context['TPortal']['editmenuitem']['type']=='menu' ? 'selected' : '', '>'.$txt['tp-menu'].'</option>';
					}
					echo '
					</select>
				</dd>
			</dl>					
			<hr>
			<dl class="settings">
				<dt>
					<label for="tp_item"><b>'.$txt['tp-item'].':</b><label>
				</dt>
				<dd>';
		// (category)
				echo '
					<select size="1" id="tp_menu_category" name="tp_menu_category" ' , $context['TPortal']['editmenuitem']['type']!='cats' ? '' : '' ,'>';

				if(count($context['TPortal']['editcats'])>0){
					foreach($context['TPortal']['editcats'] as $bmg){
						echo '
						<option value="',  $bmg['id']  ,'"' , $context['TPortal']['editmenuitem']['type'] =='cats' && isset($context['TPortal']['editmenuitem']['IDtype']) && $context['TPortal']['editmenuitem']['IDtype'] == $bmg['id'] ? ' selected' : ''  ,' > '. html_entity_decode($bmg['name']).'</option>';
					}
				}
				else
					echo '
 						<option value=""></option>';

		//  (article)
				echo '  
					</select>
					<select size="1" id="tp_menu_article" name="tp_menu_article" >';

				if(count($context['TPortal']['edit_articles']) > 0 ) {
					foreach($context['TPortal']['edit_articles'] as $bmg){
						echo '
						<option value="', $bmg['id']  ,'"' , $context['TPortal']['editmenuitem']['type'] == 'arti' && $context['TPortal']['editmenuitem']['IDtype'] == $bmg['id'] ? ' selected' : ''  ,'> '.html_entity_decode($bmg['subject']).'</option>';
					}
				}
				else
					echo '
						<option value=""></option>';

				echo '
					</select>
                    <input "size="40" id="tp_menu_link" name="tp_menu_link" type="text" value="' , (in_array($context['TPortal']['editmenuitem']['type'], array ('link', 'menu' ))) ? $context['TPortal']['editmenuitem']['IDtype'] : ''  ,'" ' , !in_array($context['TPortal']['editmenuitem']['type'], array( 'link', 'menu' )) ? ' ' : '' ,'>
				</dd>
				<dt>
					<label for="tp_menu_newlink"><b>'.$txt['tp-windowmenu'].'?</b><label>
				</dt>
				<dd>
					<select size="1" name="tp_menu_newlink" id="tp_menu_newlink">
						<option value="0" ', $context['TPortal']['editmenuitem']['newlink'] == '0' ? 'selected' : '', '>'.$txt['tp-nowindowmenu'].'</option>
						<option value="1" ', $context['TPortal']['editmenuitem']['newlink'] == '1' ? 'selected' : '', '>'.$txt['tp-windowmenu'].'</option>
					</select>
				</dd>
				<dt>
					<label for="tp_menu_sub"><b>'.$txt['tp-sub_item'].':</b><label>
				</dt>
				<dd>
					<select size="1" name="tp_menu_sub" id="tp_menu_sub">
						<option value="0" ', $context['TPortal']['editmenuitem']['sub'] == '0' ? 'selected' : '', '>0</option>
						<option value="1" ', $context['TPortal']['editmenuitem']['sub'] == '1' ? 'selected' : '', '>1</option>
						<option value="2" ', $context['TPortal']['editmenuitem']['sub'] == '2' ? 'selected' : '', '>2</option>
						<option value="3" ', $context['TPortal']['editmenuitem']['sub'] == '3' ? 'selected' : '', '>3</option>
					</select>
				</dd>
				<dt>
					<label for="tp_menu_position"><b>'.$txt['tp-menu-after'].':</b><label>
				</dt>
				<dd>
					<select size="1" name="tp_menu_position" id="tp_menu_position">';
					foreach($context['menu_buttons'] as $k => $v ) {
						echo '
						<option value="', $k ,'" ', $context['TPortal']['editmenuitem']['position'] == $k ? 'selected' : '', '>', $v['title'], '</option>';
					}
					echo '
					</select>
				</dd>';
		if(TP_SMF21) { 
			echo '	
				<dt>
					<label for="tp_menu_icon"><b>'.$txt['tp-menu-icon'].':</b><br>
						'.$txt['tp-menu-icon2'].'<label>
				</dt>
				<dd>
					<input name="tp_menu_icon" id="tp_menu_icon" type="text" size="40" value="', isset($context['TPortal']['editmenuitem']['menuicon']) ? $context['TPortal']['editmenuitem']['menuicon'] : ''  ,'">
				</dd>';
		} 
			echo '	
			</dl>
			<div>
				</div>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';

    $context['insert_after_template'] =
        '<script>
            $(\'#tp_menu_type\').on(\'change\',function(){
                switch($(this).val()){
                    case "link":
                        $("#tp_menu_link").show()
                        $("#tp_menu_newlink").show()
                        $("#tp_menu_category").hide()
                        $("#tp_menu_article").hide()
                        $("#tp_menu_sub").show()
                        $("#tp_menu_position").hide()
						$("#tp_menu_icon").hide()
                        $(\'label[for="tp_menu_position"]\').hide();
						$(\'label[for="tp_menu_icon"]\').hide();
                        $(\'label[for="tp_menu_sub"]\').show();
                        $(\'label[for="tp_menu_newlink"]\').show();
                        $(\'label[for="tp_item"]\').show();
                        break;
                    case "menu":
                        $("#tp_menu_link").show()
                        $("#tp_menu_newlink").hide()
                        $("#tp_menu_category").hide()
                        $("#tp_menu_article").hide()
                        $("#tp_menu_sub").hide()
                        $("#tp_menu_position").show()
						$("#tp_menu_icon").show()
						$(\'label[for="tp_menu_icon"]\').show();
                        $(\'label[for="tp_menu_position"]\').show();
                        $(\'label[for="tp_menu_sub"]\').hide();
                        $(\'label[for="tp_menu_newlink"]\').hide();
                        $(\'label[for="tp_item"]\').show();
                        break;
                    case "spac":
                        $("#tp_menu_link").hide()
                        $("#tp_menu_category").hide()
                        $("#tp_menu_article").hide()
                        $("#tp_menu_newlink").hide()
                        $("#tp_menu_sub").show()
                        $("#tp_menu_position").hide()
						$("#tp_menu_icon").hide()
						$(\'label[for="tp_menu_icon"]\').hide();
                        $(\'label[for="tp_menu_position"]\').hide();
                        $(\'label[for="tp_menu_sub"]\').show();
                        $(\'label[for="tp_menu_newlink"]\').hide();
                        $(\'label[for="tp_item"]\').hide();
                        break;
                    case "head":
                        $("#tp_menu_link").hide()
                        $("#tp_menu_category").hide()
                        $("#tp_menu_article").hide()
                        $("#tp_menu_newlink").hide()
                        $("#tp_menu_sub").show()
                        $("#tp_menu_position").hide()
						$("#tp_menu_icon").hide()
						$(\'label[for="tp_menu_icon"]\').hide();
                        $(\'label[for="tp_menu_position"]\').hide();
                        $(\'label[for="tp_menu_sub"]\').show();
                        $(\'label[for="tp_menu_newlink"]\').hide();
                        $(\'label[for="tp_item"]\').hide();
                        break;
                    case "cats":
                        $("#tp_menu_link").hide()
                        $("#tp_menu_category").show()
                        $("#tp_menu_article").hide()
                        $("#tp_menu_newlink").hide()
                        $("#tp_menu_sub").show()
                        $("#tp_menu_position").hide()
						$("#tp_menu_icon").hide()
                        $(\'label[for="tp_menu_icon"]\').hide();
						$(\'label[for="tp_menu_position"]\').hide();
                        $(\'label[for="tp_menu_sub"]\').show();
                        $(\'label[for="tp_menu_newlink"]\').hide();
                        $(\'label[for="tp_item"]\').show();
                        break;
                    case "arti":
                        $("#tp_menu_link").hide()
                        $("#tp_menu_category").hide()
                        $("#tp_menu_article").show()
                        $("#tp_menu_newlink").hide()
                        $("#tp_menu_sub").show()
                        $("#tp_menu_position").hide()
						$("#tp_menu_icon").hide()
                        $(\'label[for="tp_menu_icon"]\').hide();
                        $(\'label[for="tp_menu_position"]\').hide();
                        $(\'label[for="tp_menu_sub"]\').show();
                        $(\'label[for="tp_menu_newlink"]\').hide();
                        $(\'label[for="tp_item"]\').show();
                        break;
                    default:
                        $("#tp_menu_link").hide()
                        $("#tp_menu_newlink").hide()
                        $("#tp_menu_category").show()
                        $("#tp_menu_article").show()
                        $("#tp_menu_sub").show()
                        $("#tp_menu_position").hide()
						$("#tp_menu_icon").hide()
                        $(\'label[for="tp_menu_icon"]\').hide();
                        $(\'label[for="tp_menu_position"]\').hide();
                        $(\'label[for="tp_menu_sub"]\').show();
                        $(\'label[for="tp_menu_newlink"]\').hide();
                        $(\'label[for="tp_item"]\').show();
                }
            });
        $(function () {
            $("#tp_menu_type").change();
        });
        </script>';

}

// Modules Page
function template_modules()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

		echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="modules">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-modules'] . '</h3></div>
		<div id="modules" class="admintable admin-area">
			<div class="windowbg noup padding-div">';

		// Internal TP modules
		foreach($context['TPortal']['internal_modules'] as $modul)
			echo '
				<dl class="settings">
					<dt><b>',$modul['modulelink'],'</b> - ',$modul['adminlink'],'</dt>
					<dd><img src="' .$settings['tp_images_url']. '/' , $modul['state']==1 ? 'TPgreen' : 'TPred' , '.png" alt="" />
					<input name="' , $modul['fieldname'] , '" type="radio" value="1" ' , $modul['state']==1 ? 'checked><b>'.$txt['tp-on'].'</b>' : '>'.$txt['tp-on'] , '
					<input name="' , $modul['fieldname'] , '" type="radio" value="0" ' , $modul['state']==0 ? 'checked><b>'.$txt['tp-off'].'</b>' : '>'.$txt['tp-off'] , '
					</dd>
				</dl>';

		// New TP modules
		foreach($context['TPortal']['adm_modules'] as $mod)
			echo '
				<dl class="settings">
					<dt>
						<a href="', $scripturl, '?action=tportal;', $mod['subquery'], '"><strong>',$mod['title'],'</strong></a> - <a href="', $scripturl, '?action=tportal;', $mod['subquery'], '=admin">Admin</a>
					</dt>
					<dd>
						<img src="' .$settings['tp_images_url']. '/' , $mod['active']==1 ? 'TPgreen' : 'TPred' , '.png" alt="" />
						<input name="tpmodule_state' , $mod['id'] , '" type="radio" value="1" ' , $mod['active']==1 ? 'checked="checked" /><b>'.$txt['tp-on'].'</b>' : '>'.$txt['tp-on'] , '
						<input name="tpmodule_state' , $mod['id'] , '" type="radio" value="0" ' , $mod['active']==0 ? 'checked="checked" /><b>'.$txt['tp-off'].'</b>' : '>'.$txt['tp-off'] , '<br>
					</dd>
					<dt>
						', $txt['tp-author'] , ': <a href="mailto:', $mod['email'], '">', $mod['author'], '</a>
					</dt>
					<dd>
						<div class="post">', !empty($mod['description']) ? parse_bbc($mod['description']) : '' , '</div>
					</dd>
				</dl>';
		echo '  <div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

?>
