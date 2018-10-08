<?php
/**
 * @package TinyPortal
 * @version 1.6.0
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

	// some extraction..
	if(substr($go,0,20) == 'template_editarticle')
	{
		$go = 'template_editarticle';
		$param = '';
	}
	elseif(substr($go,0,20) == 'template_addarticle_')
	{
		$go = 'template_editarticle';
		$param = substr($go,20);
	}
	elseif($go == 'template_addarticle')
	{
		$go = 'template_editarticle';
		$param = 'html';
	}
	elseif($go == 'template_blocks' && isset($_GET['latest']))
	{
		$go = 'template_latestblocks';
		$param = 'html';
	}
	elseif($go == 'template_credits')
	{
		$go = 'template_tpcredits';
		$param = '';
	}
	elseif($go == 'template_categories' && !empty($_GET['cu']) && is_numeric($_GET['cu']))
	{
		$go = 'template_editcategory';
		$param = '';
	}
	elseif($go == 'template_blocks' && isset($_GET['blockedit']))
	{
		$go = 'template_blockedit';
		$param = '';
	}
	elseif($go == 'template_blocks' && isset($_GET['overview']))
	{
		$go = 'template_blockoverview';
		$param = '';
	}
	elseif($go == 'template_blocks' && isset($_GET['addblock']))
	{
		$go = 'template_addblock';
		$param = '';
	}
	else
		$param = '';

	call_user_func($go,$param);

	echo '
		</div><p class="clearthefloat"></p>
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

// blocks overview block access
function template_blockoverview()
{
	global $context, $settings, $txt, $boardurl, $scripturl;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="blockoverview">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-blockoverview'] . '</h3></div><div></div>
		<div id="blocks-overview" class="admintable admin-area windowbg noup">
			<span class="topslice"><span></span></span>
			<div class="content">';

		$side=array('','left','right','center','front','bottom','top','lower');

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
					<hr /><br><div id="tp'.$block['id'].'" style="overflow: hidden;">
						<input type="hidden" value="control" name="' . rand(10000,19999) .'tpblock'.$block['id'].'" />';

					foreach($context['TPmembergroups'] as $grp)
						echo '
						<input type="checkbox" id="tpb' . $block['id'] . '" value="' . $grp['id'].'" ' , in_array($grp['id'],$block['access']) ? 'checked="checked" ' : '' , ' name="' . rand(10000,19999) .'tpblock'.$block['id'].'" /> '. $grp['name'].'<br>';

					echo '
					</div>
					<br><input type="checkbox" onclick="invertAll(this, this.form, \'tpb'.$block['id'].'\');" />'.$txt['tp-checkall'],'<br><br>
				</div>';
				}
			}
		}
		echo '
			</div>
			<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
		</div>
	</form>';
}

// TinyPortal admin page
function template_overview()
{
	global $context, $settings, $txt, $boardurl;

	echo '
	<div id="tp_overview" class="windowbg">';

	if(is_array($context['admin_tabs']) && count($context['admin_tabs'])>0)
	{
		echo '
			<ul>';
		foreach($context['admin_tabs'] as $ad => $tab)
		{
			$tabs=array();
			foreach($tab as $t => $tb)
				echo '<li><a href="' . $tb['href'] . '"><img style="margin-bottom: 8px;" src="' . $settings['tp_images_url'] . '/TPov_' . strtolower($t) . '.png" alt="TPov_' . strtolower($t) . '" /><br><b>'.$tb['title'].'</b></a></li>';

		}
		echo '
			</ul>';
	}
	echo '
	</div>';
}

// submissions
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
								<div class="catbg3 addborderleft">
									<div style="width:7%;" class="pos float-items"><strong>' , $context['TPortal']['sort']=='parse' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on position" /> ' : '' , '<a title="Sort on position" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=parse">' , $txt['tp-pos'] , '</a></strong></div>
									<div style="width:25%;" class="name float-items"><strong>' , $context['TPortal']['sort']=='subject' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on subject" /> ' : '' , '<a title="Sort on subject" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=subject">' , $txt['tp-name'] , '</a></strong></div>
									<div style="width:11%;" class="title-admin-area float-items"><strong> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=author_id">' , $txt['tp-author'] , '</a></strong></div>
									<div style="width:14%;" class="title-admin-area float-items"><strong> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=date">' , $txt['tp-date'] , '</a></strong></div>
									<div style="width:18%;" class="title-admin-area float-items"><strong>&nbsp;</strong></div>
									<div style="width:3%;" class="title-admin-area float-items"><strong> ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=type">' , $txt['tp-type'] , '</a></strong></div>
								    <p class="clearthefloat"></p>
								</div>';

		if(!empty($context['TPortal']['pageindex']))
			echo '
								<div class="middletext padding-div addborder">
									<strong>'.$context['TPortal']['pageindex'].'</strong>
								</div>';

		foreach($context['TPortal']['arts_submissions'] as $a => $alink)
		{
			$alink['pos'] = $alink['pos']=='' ? 0 : $alink['pos'];
			$catty = $alink['category'];

			echo '
				<div class="addborder">
				  <div style="width:7%;" class="adm-pos float-items">
					<a name="article'.$alink['id'].'"></a>
					<input type="text" size="2" value="'.$alink['pos'].'" name="tp_article_pos'.$alink['id'].'" />
				</div>
				<div style="width:25%;" class="adm-name float-items">
					' , $alink['locked']==0 ? '<a href="' . $scripturl . '?action=tpadmin;sa=editarticle' . $alink['id'] . '"> ' . (!empty($alink['subject']) ? $alink['subject'] : $txt['tp-noname']) . '</a>' : '&nbsp;' . (!empty($alink['subject']) ? $alink['subject'] : $txt['tp-noname']) , '
				</div>
				<a href="" class="clickme">'.$txt['tp-more'].'</a>
			    <div class="box" style="width:64%;float:left;">
				  <div style="width:17%;" class="fullwidth-on-res-layout float-items">
		            <div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=author_id">' , $txt['tp-author'] , '</a></div>
					<div id="size-on-respnsive-layout"><a href="' . $scripturl . '?action=profile;u=' , $alink['authorID'], '">'.$alink['author'] .'</a></div>
				  </div>
				  <div style="width:25.5%;" class="smalltext fullwidth-on-res-layout float-items">
					<div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=date">' , $txt['tp-date'] , '</a></div>
					<div id="size-on-respnsive-layout">' , timeformat($alink['date']) , '</div>
				 </div>
				 <div style="text-align:left;width:26%;" class="smalltext fullwidth-on-res-layout float-items">
					<div id="show-on-respnsive-layout" style="margin-top:0.5%;"><strong>Options</strong></div>
					<div id="size-on-respnsive-layout">
					  <img style="cursor: pointer;" class="toggleActive" id="artActive' .$alink['id']. '" title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TPactive' , $alink['off']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-activate'].'"  />
					  <a href="',$scripturl, '?page=',$alink['id'],'"><img title="'.$txt['tp-preview'].'" src="' .$settings['tp_images_url']. '/TPfilter.png" alt="" /></a>
					  ' , $alink['locked']==0 ?
					  '<a href="' . $scripturl . '?action=tpadmin;sa=editarticle'.$alink['id']. '"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>' : '<img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-islocked'].'"  />' , '

					  <img style="cursor: pointer;" class="toggleFront" id="artFront' .$alink['id']. '" title="'.$txt['tp-setfrontpage'].'" border="0" src="' .$settings['tp_images_url']. '/TPfront' , $alink['frontpage']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-setfrontpage'].'"  />
					  <img style="cursor: pointer;" class="toggleSticky" id="artSticky' .$alink['id']. '" title="'.$txt['tp-setsticky'].'" border="0" src="' .$settings['tp_images_url']. '/TPsticky' , $alink['sticky']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setsticky'].'"  />
					  <img style="cursor: pointer;" class="toggleLock" id="artLock' .$alink['id']. '" title="'.$txt['tp-setlock'].'" border="0" src="' .$settings['tp_images_url']. '/TPlock' , $alink['locked']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setlock'].'"  />
					  <img style="cursor: pointer;" class="toggleFeatured" id="artFeatured' .$alink['id']. '" title="'.$txt['tp-featured'].'" border="0" src="' .$settings['tp_images_url']. '/TPflag' , $alink['featured']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-turnoff'].'"  />
				    </div>
				</div>
			    <div class="smalltext fullwidth-on-res-layout float-items" style="text-align:center;width:8%;text-transform:uppercase;">
					<div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=submission;sort=type">' , $txt['tp-type'] , '</a></div>
					' , empty($alink['type']) ? 'html' : $alink['type'] , '
				</div>
				<div style="text-align:center;width:5%;" class="smalltext fullwidth-on-res-layout float-items">
				  <div id="show-on-respnsive-layout"><strong>Delete</strong></div>
				  <a href="' . $scripturl . '?action=tpadmin;cu=-1;' . $context['session_var'] . '=' . $context['session_id'].';artdelete=' .$alink['id']. '" onclick="javascript:return confirm(\''.$txt['tp-articleconfirmdelete'].'\')">
				    <img title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/tp-delete_shout.png" alt="'.$txt['tp-delete'].'"  />
				  </a>
				</div>
				<div style="width:5%;text-align:center;" class="smalltext fullwidth-on-res-layout float-items">
				  <div id="show-on-respnsive-layout"><strong>Select</strong></div>
				  <input type="checkbox" name="tp_article_submission'.$alink['id'].'" value="1"  />
			    </div>
			    <p class="clearthefloat"></p>
			</div>
			<p class="clearthefloat"></p>
		</div>';
			}
			if( !empty($context['TPortal']['pageindex']))
				echo '
								<div class="middletext padding-div">
									<strong>'.$context['TPortal']['pageindex'].'</strong>
								</div>';

		if(isset($context['TPortal']['allcats']))
		{
			echo '	<div class="padding-div"><p style="text-align: center;">
							<select name="tp_article_cat">
								<option value="0">' . $txt['tp-createnew2'] . '</option>';
			foreach($context['TPortal']['allcats'] as $submg)
  					echo '
								<option value="'.$submg['id'].'">'. $txt['tp-approveto'] . $submg['name'].'</option>';
			echo '
							</select>
							<input name="tp_article_new" value="" size="40" /> &nbsp;
						</p></div>';
		}
		echo '
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			    </div>';
	}
	else
		echo '
				<div class="windowbg2">
					<div class="windowbg3"></div>
				</div>';

	echo '
		</div>
	</form>';
}

// edit modules
function template_modules()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

//		tp_latestmodules();

		if(!empty($context['TPortal']['tpmodule_message']))
			echo '
		<div class="error">', $context['TPortal']['tpmodule_message'], '</div>';

		echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="modules">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-modules'] . '</h3></div>
		<div id="modules" class="admintable admin-area">
			<div class="windowbg noup padding-div">';

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
						<dt><a href="', $scripturl, '?action=tpmod;', $mod['subquery'], '"><strong>',$mod['title'],'</strong></a> - <a href="', $scripturl, '?action=tpmod;', $mod['subquery'], '=admin">Admin</a></dt>
						<dd><img src="' .$settings['tp_images_url']. '/' , $mod['active']==1 ? 'TPgreen' : 'TPred' , '.png" alt="" />
						<input name="tpmodule_state' , $mod['id'] , '" type="radio" value="1" ' , $mod['active']==1 ? 'checked="checked" /><b>'.$txt['tp-on'].'</b>' : '>'.$txt['tp-on'] , '
						<input name="tpmodule_state' , $mod['id'] , '" type="radio" value="0" ' , $mod['active']==0 ? 'checked="checked" /><b>'.$txt['tp-off'].'</b>' : '>'.$txt['tp-off'] , '<br>
						</dd>
						<dt> ', $txt['tp-author'] , ': <a href="mailto:', $mod['email'], '">', $mod['author'], '</a>
						</dt>
						<dd><div class="post">', !empty($mod['description']) ? parse_bbc($mod['description']) : '' , '</div>
						</dd>
					</dl>
					';
		echo '  <div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
		</div>
	</form>';
}
	// menu manager
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
				<div class="titlebg2 addborderleft addborder">
							<div style="width:9%;" class="pos float-items">'.$txt['tp-pos'].'</div>
							<div style="width:14%;" class="name float-items">'.$txt['tp-title'].'</div>
							<div style="width:8%;" class="title-admin-area float-items">'.$txt['tp-type'].'</div>
							<div style="width:12%;" class="title-admin-area float-items">'.$txt['tp-on'].' '.$txt['tp-off'].' '.$txt['tp-edit'].' </div>
							<div style="width:9%;" class="title-admin-area float-items">'.$txt['tp-item'].'</div>
							<div style="width:10%;" class="title-admin-area float-items">'.$txt['tp-sub_item'].'</div>
							<div style="width:11%;" class="title-admin-area float-items">'.$txt['tp-sitemap_on'].'</div>
							<div style="width:9%;" class="title-admin-area float-items">'.$txt['tp-delete'].' </div>
							<p class="clearthefloat"></p>
						</div>';
			if(!empty($context['TPortal']['menubox'][$mid]))
			{
				$tn=sizeof($context['TPortal']['menubox'][$mid]);
				$n=1;
				foreach($context['TPortal']['menubox'][$mid] as $lbox){
					echo '
							<div class="addborder windowbg' , $lbox['off']=='0' ? '2' : '' , '" >
								<div style="width:9%;" class="adm-pos float-items">
								  <input name="menu_pos' .$lbox['id']. '" type="text" size="4" value="' . (empty($lbox['subtype']) ? '0' :  $lbox['subtype']) . '">
								</div>
								<div style="width:14%;" class="adm-name float-items">
								  <a href="' . $scripturl . '?action=tpadmin;linkedit=' .$lbox['id']. ';' . $context['session_var'] . '=' . $context['session_id'].'">' .$lbox['name']. '</a>
								</div>
								<a href="" class="clickme">'.$txt['tp-more'].'</a>
			                    <div class="box" style="width:73%;float:left;">
							      <div style="width:13%;" class="fullwidth-on-res-layout float-items">
								    <div id="show-on-respnsive-layout">'.$txt['tp-type'].'</div>';
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
								  <div style="width:16%;" class="fullwidth-on-res-layout float-items" align="center">
								      <div id="show-on-respnsive-layout">'.$txt['tp-on'].' '.$txt['tp-off'].' '.$txt['tp-edit'].' </div>&nbsp;
								      <a href="' . $scripturl . '?action=tpadmin;linkon=' .$lbox['id']. ';mid=' , (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $_GET['mid'] : 0 , ';' . $context['session_var'] . '=' . $context['session_id'].'"><img title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TPgreen' , $lbox['off']!=0 ? '2' : '' , '.png" alt="'.$txt['tp-activate'].'"  /></a>
									  <a href="' . $scripturl . '?action=tpadmin;linkoff=' .$lbox['id']. ';mid=' , (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $_GET['mid'] : 0 , ';' . $context['session_var'] . '=' . $context['session_id'].'"><img title="'.$txt['tp-deactivate'].'" border="0" src="' .$settings['tp_images_url']. '/TPred' , $lbox['off']==0 ? '2' : '' , '.png" alt="'.$txt['tp-deactivate'].'"  /></a>
									  <a href="' . $scripturl . '?action=tpadmin;linkedit=' .$lbox['id']. ';mid=' , (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $_GET['mid'] : 0 , ';' . $context['session_var'] . '=' . $context['session_id'].'"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>
								  </div>
							      <div style="width:14%; overflow:hidden;" class="fullwidth-on-res-layout float-items">
								      <div id="show-on-respnsive-layout">'.$txt['tp-item'].'</div>
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
								  <div style="width:15%;" class="fullwidth-on-res-layout float-items">'; 
								      
									  if($lbox['type']!=='menu'){
									  
									  echo '
									  <div id="show-on-respnsive-layout">'.$txt['tp-sub_item'].'</div>
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
								  <div style="width:17%;" class="fullwidth-on-res-layout float-items">
								    <div id="show-on-respnsive-layout">'.$txt['tp-sitemap_on'].'</div>
									<input name="tp_menu_sitemap' .$lbox['id']. '" type="radio" value="1" ' , in_array($lbox['id'],$context['TPortal']['sitemap']) ? 'checked' : '' ,'>' . $txt['tp-yes'] .'
									<input name="tp_menu_sitemap' .$lbox['id']. '" type="radio" value="0" ' , !in_array($lbox['id'],$context['TPortal']['sitemap']) ? 'checked' : '' ,'> ' . $txt['tp-no'] . '
								  </div>
								  <div style="width:13%;" class="fullwidth-on-res-layout float-items">
								    <div id="show-on-respnsive-layout">'.$txt['tp-delete'].' </div>&nbsp;
									<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';linkdelete=' .$lbox['id']. '" onclick="javascript:return confirm(\''.$txt['tp-suremenu'].'\')"><img title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt="'.$txt['tp-delete'].'"  /></a>
								  </div>
								  <p class="clearthefloat"></p>
							  </div>
							  <p class="clearthefloat"></p>
					       </div>';
					$n++;
				}
			}
		}
		// ok, show the single menus
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
				<div style="padding:1%;" class="addborder">
					<dl class="settings">
						<dt>
							<strong><i>' . $txt['tp-internal'] . '</i></strong><br>
						</dt>
						<dd>
							<a href="' . $scripturl . '?action=tpadmin;sa=menubox;mid=0"><img height="16px" title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPedit.png" alt="'.$txt['tp-edit'].'"  /><strong>' .$txt['tp-edit'].'</strong></a><br>
						</dd>
					</dl>
				</div>';

				else
					echo '
				<div style="padding:1%;" class="addborder">
					<dl class="settings">
						<dt>
							<input name="tp_menu_name' .$mbox['id']. '" type="text" size="40" value="' .$mbox['name']. '"><br>
						</dt>
						<dd>
							<a href="' . $scripturl . '?action=tpadmin;sa=menubox;mid=' .$mbox['id']. '"><img height="16px"; title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPedit.png" alt="'.$txt['tp-edit'].'"  /><strong> '.$txt['tp-edit'].'</strong></a> &nbsp;&nbsp;&nbsp;
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';linkdelete=' .$mbox['id']. ';fullmenu" onclick="javascript:return confirm(\''.$txt['tp-suremenu'].'\')"><img height="16px" title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/TPdelete2.png" alt="'.$txt['tp-delete'].'"  /> <strong>'.$txt['tp-delete'].'</strong></a><br>
						</dd>
					</dl>
				</div>';
			}
		}
		echo '
				<div><br>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
			</div>
		</div>
	</form>';
}
	// add menu
function template_addmenu()
{

	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

		// new item?
		if(!isset($_GET['fullmenu'])) {
            // Just default this for now...
            $context['TPortal']['editmenuitem']['sub']      = 0;
            $context['TPortal']['editmenuitem']['newlink']  = '0';
            $context['TPortal']['editmenuitem']['type']     = 'cats';
            $context['TPortal']['editmenuitem']['position'] = 'home';

			echo '
	            <form accept-charset="', $context['character_set'], '" name="tpadminmenu" action="' . $scripturl . '?action=tpadmin" method="post">
		        <input type="hidden" name="sc" value="', $context['session_id'], '" />
		        <input name="tpadmin_form" type="hidden" value="menuaddsingle">
		        <div class="cat_bar"><h3 class="catbg">'.$txt['tp-addmenu'].'</h3></div>
				<dd><input name="newmenu" type="hidden" value="1">
					<input name="tp_menu_menuid" type="hidden" value="' , (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $_GET['mid'] : 0 , '">
				</dd>';

                template_menucore();
		}
		// full menu
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
							<dt><label for="field_name"><h4>'.$txt['tp-title'].'</h4><label>
							</dt>
							<dd><input name="tp_menu_title" type="text" size="40" value=""><br>
							</dd>
						</dl>
						<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
                    </div>
                </div>
            </form>';
		}
}
	// edit menuitem
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
    global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

    echo'
		<div id="new-item" class="admintable admin-area edit-menu-item">
		<div class="information smalltext">' , $txt['tp-helpmenuitems'] , '</div><div></div>
		<div class="windowbg noup padding-div">';

		echo '
					<dl class="settings">
						<dt>
                            <label for="tp_title"><h4>'.$txt['tp-title'].':</h4><label>
						</dt>
						<dd>
                            <input name="tp_menu_name" type="text" size="40" value="', isset($context['TPortal']['editmenuitem']['name']) ? $context['TPortal']['editmenuitem']['name'] : ''  ,'">
						</dd>
					</dl>
					
					<dl class="settings">
						<dt><label for="tp_menu_name"><h4>'.$txt['tp-type'].':</h4><label></dt>
                        <dd>
                        <select size="1" name="tp_menu_type" id="tp_menu_type">
                            <option value="cats" ', $context['TPortal']['editmenuitem']['type']=='cats' ? 'selected' : '', '>'.$txt['tp-category'].'</option>
                            <option value="arti" ', $context['TPortal']['editmenuitem']['type']=='arti' ? 'selected' : '', '>'.$txt['tp-article'].'</option>
                            <option value="link" ', $context['TPortal']['editmenuitem']['type']=='link' ? 'selected' : '', '>'.$txt['tp-link'].'</option>
                            <option value="head" ', $context['TPortal']['editmenuitem']['type']=='head' ? 'selected' : '', '>'.$txt['tp-header'].'</option>
                            <option value="spac" ', $context['TPortal']['editmenuitem']['type']=='spac' ? 'selected' : '', '>'.$txt['tp-spacer'].'</option>';
					// check for menu button
				if ((!empty($_GET['mid']) && $_GET['mid']==0) || ($context['TPortal']['editmenuitem']['menuID']==0)) {
					echo '*/
                            <option value="menu" ', $context['TPortal']['editmenuitem']['type']=='menu' ? 'selected' : '', '>'.$txt['tp-menu'].'</option>';
					}
					echo '
                        </select>
                        </dd>
					</dl>					
					<hr>
					<dl class="settings">
						<dt>
                            <label for="tp_item"><h4>'.$txt['tp-item'].':</h4><label>
						</dt>
					<dd>';
		// (category)
		echo '<select size="1" id="tp_menu_category" name="tp_menu_category" ' , $context['TPortal']['editmenuitem']['type']!='cats' ? '' : '' ,'>';
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
		echo '  </select>
			    <select size="1" id="tp_menu_article" name="tp_menu_article" >';
		if(count($context['TPortal']['edit_articles']) > 0 ) {
			foreach($context['TPortal']['edit_articles'] as $bmg){
 				echo '      <option value="', $bmg['id']  ,'"' , $context['TPortal']['editmenuitem']['type'] == 'arti' && $context['TPortal']['editmenuitem']['IDtype'] == $bmg['id'] ? ' selected' : ''  ,'> '.html_entity_decode($bmg['subject']).'</option>';
			}
		}
		else
			echo '          <option value=""></option>';

		echo '  </select>
                    <input "size="40" id="tp_menu_link" name="tp_menu_link" type="text" value="' , (in_array($context['TPortal']['editmenuitem']['type'], array ('link', 'menu' ))) ? $context['TPortal']['editmenuitem']['IDtype'] : ''  ,'" ' , !in_array($context['TPortal']['editmenuitem']['type'], array( 'link', 'menu' )) ? ' ' : '' ,'>
					</dd>
						<dt><label for="tp_menu_newlink"><h4>'.$txt['tp-windowmenu'].'?</h4><label>
                        <dd>
                        <select size="1" name="tp_menu_newlink" id="tp_menu_newlink">
                            <option value="0" ', $context['TPortal']['editmenuitem']['newlink'] == '0' ? 'selected' : '', '>'.$txt['tp-nowindowmenu'].'</option>
                            <option value="1" ', $context['TPortal']['editmenuitem']['newlink'] == '1' ? 'selected' : '', '>'.$txt['tp-windowmenu'].'</option>
                        </select>
						</dd>
						<dt>
                            <label for="tp_menu_sub"><h4>'.$txt['tp-sub_item'].':</h4><label>
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
                            <label for="tp_menu_position"><h4>'.$txt['tp-menu-after'].':</h4><label>
                        </dt>
                        <dd>
                        <select size="1" name="tp_menu_position" id="tp_menu_position">';
                        foreach($context['menu_buttons'] as $k => $v ) {
                            echo '<option value="', $k ,'" ', $context['TPortal']['editmenuitem']['position'] == $k ? 'selected' : '', '>', $v['title'], '</option>';
                        }
                        echo '
                        </select>
						</dd>
					</dl>
				<div>
					</div>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
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
                        $(\'label[for="tp_menu_position"]\').hide();
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
	// Panels
function template_panels()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language, $forum_version;

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
							<br><strong><label for="field_name">'.$txt['tp-hidebarsall'].'</label></strong>
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
							<label for="field_name">', $txt['tp-hidebarscustom'], '</label>
						</dt>
						<dd>
							<textarea cols="40" style="width: 94%; height: 100px;" name="tp_hidebars_custom">' . $context['TPortal']['hidebars_custom'].'</textarea>
						</dd>
					<dl class="settings">
					</dl>
						<dt>
							<label for="field_name">', $txt['tp-padding_between'], '</label>
						</dt>
						<dd>
							<input name="tp_padding" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['padding'], '">
							<span class="smalltext">'.$txt['tp-inpixels'].'</span>
						</dd>
					</dl>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
				<hr>';

	$allpanels = array('left','right','top','center','front','lower','bottom');
	$alternate = true;

	if(function_exists('ctheme_tp_getblockstyles'))
		$types = ctheme_tp_getblockstyles();
	if(strstr($forum_version, '2.1'))
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
		echo '<br>

			<div>
		<dl class="settings">


			';
		if($panl!='front')
		{
			if(in_array($panl, array("left","right")))
				echo '
								<dt>
									<span class="normaltext">'.$txt['tp-panelwidth'].':</span>
								</dt>
								<dd>
									<input name="tp_'.$panl.'bar_width" size="5" maxsize="5" type="text" value="' , $context['TPortal'][$panl. 'bar_width'] , '"><br>
								</dd>';
				echo '
								<dt>
									<span class="normaltext">'.$txt['tp-use'.$panl.'panel'].'</span>
								</dt>
								<dd>
									<input name="tp_'.$panl.'panel" type="radio" value="1" ' , $context['TPortal']['admin'.$panl.'panel']==1 ? 'checked' : '' , '> '.$txt['tp-on'].'
									<input name="tp_'.$panl.'panel" type="radio" value="0" ' , $context['TPortal']['admin'.$panl.'panel']==0 ? 'checked' : '' , '> '.$txt['tp-off'].'<br>
								<dt>
									<span style="text-align: right;"> '.$txt['tp-hide_'.$panl.'bar_forum'].' </span>
								</dt>
								<dd>
									<input name="tp_hide_'.$panl.'bar_forum" type="radio" value="1" ' , $context['TPortal']['hide_'.$panl.'bar_forum']==1 ? 'checked' : '' , '> '.$txt['tp-yes'].'
									<input name="tp_hide_'.$panl.'bar_forum" type="radio" value="0" ' , $context['TPortal']['hide_'.$panl.'bar_forum']==0 ? 'checked' : '' , '> '.$txt['tp-no'].'
									<br><br>
								</dd>';
		}
		echo '
								<dt> '.$txt['tp-vertical'].'<br></dt><dd><input name="tp_block_layout_'.$panl.'" type="radio" value="vert" ' , $context['TPortal']['block_layout_'.$panl]=='vert' ? 'checked' : '' , '></dd>
								<dt> '.$txt['tp-horisontal'].'<br></dt><dd><input name="tp_block_layout_'.$panl.'" type="radio" value="horiz" ' , $context['TPortal']['block_layout_'.$panl]=='horiz' ? 'checked' : '' , '></dd>
								<dt> '.$txt['tp-horisontal2cols'].'<br></dt><dd><input name="tp_block_layout_'.$panl.'" type="radio" value="horiz2" ' , $context['TPortal']['block_layout_'.$panl]=='horiz2' ? 'checked' : '' , '></dd>
								<dt> '.$txt['tp-horisontal3cols'].'<br></dt><dd><input name="tp_block_layout_'.$panl.'" type="radio" value="horiz3" ' , $context['TPortal']['block_layout_'.$panl]=='horiz3' ? 'checked' : '' , '></dd>
								<dt> '.$txt['tp-horisontal4cols'].'<br></dt><dd><input name="tp_block_layout_'.$panl.'" type="radio" value="horiz4" ' , $context['TPortal']['block_layout_'.$panl]=='horiz4' ? 'checked' : '' , '></dd>
								<dt> '.$txt['tp-grid'].'<br></dt><dd><input name="tp_block_layout_'.$panl.'" type="radio" value="grid" ' , $context['TPortal']['block_layout_'.$panl]=='grid' ? 'checked' : '' , '></dd>
								<dt></dt><dd><p>
										<input type="radio" name="tp_blockgrid_'.$panl.'" value="colspan3" ' , $context['TPortal']['blockgrid_'.$panl]=='colspan3' ? 'checked' : '' , ' /><img align="middle" src="' .$settings['tp_images_url']. '/TPgrid1.png" alt="colspan3" />
										<input type="radio" name="tp_blockgrid_'.$panl.'" value="rowspan1" ' , $context['TPortal']['blockgrid_'.$panl]=='rowspan1' ? 'checked' : '' , ' /><img align="middle" src="' .$settings['tp_images_url']. '/TPgrid2.png" alt="rowspan1" />
									</p></dd>
								<dt>
									<span class="middletext">'.$txt['tp-blockwidth'].':</span>
								</dt>
								<dd>
									<input name="tp_blockwidth_'.$panl.'" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['blockwidth_'.$panl], '"><br>
								</dd>
								<dt>
									<span class="middletext">'.$txt['tp-blockheight'].':</span>
								</dt>
								<dd>
									<input name="tp_blockheight_'.$panl.'" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['blockheight_'.$panl], '">
								</dd>
							</dl>
							<div>'.$txt['tp-panelstylehelp'].'<br>
								<div class="smalltext">'.$txt['tp-panelstylehelp2'].'</div>
								</div>

							<br><div class="panels-optionsbg">';

			foreach($types as $blo => $bl)
				echo '
								<div class="panels-options">
									<div class="smalltext" style="padding: 4px 0;">
										<input name="tp_panelstyle_'.$panl.'" type="radio" value="'.$blo.'" ' , $context['TPortal']['panelstyle_'.$panl]==$blo ? 'checked' : '' , '>
										<span' , $context['TPortal']['panelstyle_'.$panl]==$blo ? ' style="color: red;">' : '>' , $bl['class'] , '</span>
									</div>
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
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
		</div>
	</form>';
}

// settings
function template_settings()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language, $forum_version;

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
						   echo '<input name="tp_resp" type="checkbox" value="0">'.$txt['tp-deselectthemes'].'<br><br> ';
							foreach($context['TPallthem'] as $them) {
					              if(strstr($forum_version, '2.1')) {
									echo '
										  <img class="theme_icon" alt="*" src="'.$them['path'].'/thumbnail.png" />
										  <input name="tp_resp'.$them['id'].'" type="checkbox" value="'.$them['id'].'" ';
										}
								  else {
									echo '
										  <img class="theme_icon" alt="*" src="'.$them['path'].'/thumbnail.gif" />
										  <input name="tp_resp'.$them['id'].'" type="checkbox" value="'.$them['id'].'" ';
										}
					              if(in_array($them['id'],$tm)) {
					                echo ' checked="checked" ';
					              }
					              echo '>'.$them['name'].'<br>';
						       }
						       echo'<br><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'">
					        </div>
						<!-- END non responsive themes form -->
							<br><hr>

				<dl class="settings">
					<dt>
						<label for="field_name">', $txt['tp-frontpagetitle'], '</label>
						<div class="smalltext">' , $txt['tp-frontpagetitle2'] , '</div>
					</dt>
					<dd>
						<input name="tp_frontpage_title" type="text" value="' , !empty($context['TPortal']['frontpage_title']) ? $context['TPortal']['frontpage_title'] : '' , '">
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-redirectforum'], '</label>
					</dt>
					<dd>
						<input name="tp_redirectforum" type="radio" value="1" ' , $context['TPortal']['redirectforum']=='1' ? 'checked' : '' , '> '.$txt['tp-redirectforum1'].'
					</dd>
					<dd>
						<input name="tp_redirectforum" type="radio" value="0" ' , $context['TPortal']['redirectforum']=='0' ? 'checked' : '' , '> '.$txt['tp-redirectforum2'].'
					</dd>
				</dl>
					<hr>
				<dl class="settings">
					<dt>
						<label for="field_name">', $txt['tp-useroundframepanels'], '</label>
					</dt>
					<dd>
						<input name="tp_useroundframepanels" type="radio" value="1" ' , $context['TPortal']['useroundframepanels']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_useroundframepanels" type="radio" value="0" ' , $context['TPortal']['useroundframepanels']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-hidecollapse'], '</label>
					</dt>
					<dd>
						<input name="tp_showcollapse" type="radio" value="1" ' , $context['TPortal']['showcollapse']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
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
				</dl>
					<hr>
				<dl class="settings">
					<dt>
						<label for="field_name">', $txt['tp-maxrating'], '</label>
					</dt>
					<dd>
						<input name="tp_maxstars" size="4" type="text" value="'.$context['TPortal']['maxstars'].'">
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
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-fulltextsearch'], '</label>
						<div class="smalltext">' , $txt['tp-fulltextsearchdesc'] , '</div>
					</dt>
					<dd>
						<input name="tp_fulltextsearch" type="radio" value="1" ' , $context['TPortal']['fulltextsearch']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
						<input name="tp_fulltextsearch" type="radio" value="0" ' , $context['TPortal']['fulltextsearch']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-copyrightremoval'], '</label>
						<div class="smalltext">' , $txt['tp-copyrightremovaldesc'] , '</div>
					</dt>
					<dd>
						<input name="tp_copyrightremoval" type="text" value="' , !empty($context['TPortal']['copyrightremoval']) ? $context['TPortal']['copyrightremoval'] : '' , '">
					</dd>
				</div>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
		</div>
	</form>';
}

//  clist
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
		echo '<br><input type="checkbox" onclick="invertAll(this, this.form, \'tp_clist\');" />  '.$txt['tp-checkall'].'
						</div>
		     			<br><div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="send"></div>
		     		</div>
				</div>
		     </form>';
}
	//   article settings
function template_artsettings()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

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
							<label for="field_name">', $txt['tp-editorheight'], '</label>
						</dt>
						<dd>
							<input name="tp_editorheight" type="text" size="4" value="' , $context['TPortal']['editorheight'] , '" />
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
							<label for="field_name">', $txt['tp-hidearticle-google'], '</label>
						</dt>
						<dd>
							<input name="tp_hide_article_google" type="radio" value="1" ' , $context['TPortal']['hide_article_google']=='1' ? 'checked' : '' , '> '.$txt['tp-yes'].'
							<input name="tp_hide_article_google" type="radio" value="0" ' , $context['TPortal']['hide_article_google']=='0' ? 'checked' : '' , '> '.$txt['tp-no'].'
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
					</dl>
				</div>
				<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// frontpage settings
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
							<label for="field_name">', $txt['tp-whattoshow'], '</label>
						</dt>
						<dd>
							<input name="tp_front_type" type="radio" value="forum_only" ' , $context['TPortal']['front_type']=='forum_only' ? 'checked' : '' , '> '.$txt['tp-onlyforum'].'<br>
							<input name="tp_front_type" type="radio" value="forum_selected" ' , $context['TPortal']['front_type']=='forum_selected' ? 'checked' : '' , '> '.$txt['tp-selectedforum'].'<br>
							<input name="tp_front_type" type="radio" value="forum_articles" ' , $context['TPortal']['front_type']=='forum_articles' ? 'checked' : '' , '> '.$txt['tp-bothforum'].'<br>
							<input name="tp_front_type" type="radio" value="forum_selected_articles" ' , $context['TPortal']['front_type']=='forum_selected_articles' ? 'checked' : '' , '> '.$txt['tp-selectbothforum'].'<br>
							<input name="tp_front_type" type="radio" value="articles_only" ' , $context['TPortal']['front_type']=='articles_only' ? 'checked' : '' , '> '.$txt['tp-onlyarticles'].'<br>
							<input name="tp_front_type" type="radio" value="single_page"  ' , $context['TPortal']['front_type']=='single_page' ? 'checked' : '' , '> '.$txt['tp-singlepage'].'<br>
							<input name="tp_front_type" type="radio" value="frontblock"  ' , $context['TPortal']['front_type']=='frontblock' ? 'checked' : '' , '> '.$txt['tp-frontblocks'].'<br>
							<input name="tp_front_type" type="radio" value="boardindex"  ' , $context['TPortal']['front_type']=='boardindex' ? 'checked' : '' , '> '.$txt['tp-boardindex'].'<br>
							<input name="tp_front_type" type="radio" value="module"  ' , $context['TPortal']['front_type']=='module' ? 'checked' : '' , '> '.$txt['tp-frontmodule'].'<br>
							<hr />
							<div style="padding-left: 2em;">';
			if(sizeof($context['TPortal']['tpmodules']['frontsection'])>0)
			{
				foreach($context['TPortal']['tpmodules']['frontsection'] as $tpm)
					echo '<input name="tp_front_module" type="radio" value="' . $tpm['id'] . '" ' , $context['TPortal']['front_module']==$tpm['id'] ? 'checked' : '' , '>'.$tpm['name'], '<br>';
			}
			else
				echo '<hr /><span class="smalltext">' . $txt['tp-nofrontmodule'] . '</span>';

			echo '      <br></dd>
						<dt>
							<label for="field_name">', $txt['tp-frontblockoption'], '</label>
						</dt>
						<dd>
							  <input name="tp_frontblock_type" type="radio" value="single"  ' , $context['TPortal']['frontblock_type']=='single' ? 'checked' : '' , '> '.$txt['tp-frontblocksingle'].'<br>
							<input name="tp_frontblock_type" type="radio" value="first"  ' , $context['TPortal']['frontblock_type']=='first' ? 'checked' : '' , '> '.$txt['tp-frontblockfirst'].'<br>
							<input name="tp_frontblock_type" type="radio" value="last"  ' , $context['TPortal']['frontblock_type']=='last' ? 'checked' : '' , '> '.$txt['tp-frontblocklast'].'<br><br>
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-frontpageoptions'], '</label>
						</dt>
						<dd>
							<input name="tp_frontpage_visual_left" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['left']>0 ? 'checked' : '' , '> ',$txt['tp-displayleftpanel'],'<br>
							<input name="tp_frontpage_visual_right" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['right']>0 ? 'checked' : '' , '> ',$txt['tp-displayrightpanel'],'<br>
							<input name="tp_frontpage_visual_center" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['center']>0 ? 'checked' : '' , '> ',$txt['tp-displayupperpanel'],'<br>
							<input name="tp_frontpage_visual_top" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['top']>0 ? 'checked' : '' , '> ',$txt['tp-displaytoppanel'],'<br>
							<input name="tp_frontpage_visual_bottom" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['bottom']>0 ? 'checked' : '' , '> ',$txt['tp-displaybottompanel'],'<br>
							<input name="tp_frontpage_visual_lower" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['lower']>0 ? 'checked' : '' , '> ',$txt['tp-displaylowerpanel'],'<br>
							<input name="tp_frontpage_visual_header" type="checkbox" value="1" ' , $context['TPortal']['frontpage_visualopts_admin']['header']>0 ? 'checked' : '' , '> ',$txt['tp-displaynews'],'<br><br>
						</dd>

					</dl>
					<hr>
					<div><strong><label for="field_name">', $txt['tp-frontpage_layout'], '</label></strong></div>
					<div>
						<div class="tpartlayoutfp"><input name="tp_frontpage_layout" type="radio" value="1" ' ,
						$context['TPortal']['frontpage_layout']<2 ? 'checked' : '' , '> A ' ,
						$context['TPortal']['frontpage_layout']<2 ? '' : '' , '
							<div style="margin-top: 5px;">
								<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_a.png"/>
							</div>
						</div>
						<div class="tpartlayoutfp"><input name="tp_frontpage_layout" type="radio" value="2" ' ,
						$context['TPortal']['frontpage_layout']==2 ? 'checked' : '' , '> B ' ,
						$context['TPortal']['frontpage_layout']==2 ? '' : '' , '
							<div style="margin-top: 5px;">
								<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_b.png"/>
							</div>
						</div>
						<div class="tpartlayoutfp"><input name="tp_frontpage_layout" type="radio" value="3" ' ,
						$context['TPortal']['frontpage_layout']==3 ? 'checked' : '' , '> C ' ,
						$context['TPortal']['frontpage_layout']==3 ? '' : '' , '
							<div style="margin-top: 5px;">
								<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_c.png"/>
							</div>
						</div>
						<div class="tpartlayoutfp"><input name="tp_frontpage_layout" type="radio" value="4" ' ,
						$context['TPortal']['frontpage_layout']==4 ? 'checked' : '' , '> D ' ,
						$context['TPortal']['frontpage_layout']==4 ? '' : '' , '
							<div style="margin-top: 5px;">
								<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_d.png"/>
							</div>
						</div>
						<br style="clear: both;" /><br>
					</div>
					<div>
						<strong><label for="field_name">', $txt['tp-articlelayouts'], '</label></strong>
					</div>
					<div>';	foreach($context['TPortal']['admin_layoutboxes'] as $box)
								echo '
												<div class="tpartlayouttype">
													<input type="radio" name="tp_frontpage_catlayout" value="'.$box['value'].'"' , $context['TPortal']['frontpage_catlayout']==$box['value'] ? ' checked="checked"' : '' , '>
													'.$box['label'].'<br><img style="margin: 4px 4px 4px 10px;" src="' , $settings['tp_images_url'] , '/TPcatlayout'.$box['value'].'.png" alt="tplayout'.$box['value'].'" />
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
							<label for="field_name">', $txt['tp-allowguests'], '</label>
						</dt>
						<dd>
							  <input name="tp_allow_guestnews" type="radio" value="1" ' , $context['TPortal']['allow_guestnews']==1 ? 'checked' : '' , '> '.$txt['tp-yes'].'
							  <input name="tp_allow_guestnews" type="radio" value="0" ' , $context['TPortal']['allow_guestnews']==0 ? 'checked' : '' , '> '.$txt['tp-no'].'
						</dd>

						<dt>
							<label for="field_name">', $txt['tp-numberofposts'], '</label>
						</dt>
						<dd>
						  <input name="tp_frontpage_limit" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['frontpage_limit'], '"><br><br>
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-sortingoptions'], '</label>
						</dt>
						<dd>
							<select name="tp_frontpage_usorting">
								<option value="date"' , $context['TPortal']['frontpage_visualopts_admin']['sort']=='date' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions1'] , '</option>
								<option value="authorID"' , $context['TPortal']['frontpage_visualopts_admin']['sort']=='authorID' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions2'] , '</option>
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
							<label for="field_name">', $txt['tp-showforumposts'], '</label>
						</dt>
						<dd>';

		echo '
							<select size="1" name="tp_ssiboard1">';
		$tn=sizeof($context['TPortal']['boards']);
		for($n=0 ; $n<$tn; $n++){
			echo '
								<option value="'.$context['TPortal']['boards'][$n]['id'].'" ' , isset($context['TPortal']['SSI_boards'][0]) && $context['TPortal']['boards'][$n]['id']==$context['TPortal']['SSI_boards'][0] ? 'selected' : '' , '>'.$context['TPortal']['boards'][$n]['name'].'</option>';
		}
		echo '
							</select><br>';
		// board 2
		echo '
							<select size="1" name="tp_ssiboard2"><option value="0">',$txt['tp-none-'],'</option>';
		for($n=0 ; $n<$tn; $n++){
			echo '
								<option value="'.$context['TPortal']['boards'][$n]['id'].'" ' , isset($context['TPortal']['SSI_boards'][1]) && $context['TPortal']['boards'][$n]['id']==$context['TPortal']['SSI_boards'][1] ? 'selected' : '' , '>'.$context['TPortal']['boards'][$n]['name'].'</option>';
		}
		echo '
							</select><br>';
		// board 3
		echo '
							<select size="1" name="tp_ssiboard3"><option value="0">',$txt['tp-none-'],'</option>';
		for($n=0 ; $n<$tn; $n++){
			echo '
								<option value="'.$context['TPortal']['boards'][$n]['id'].'" ' , isset($context['TPortal']['SSI_boards'][2]) && $context['TPortal']['boards'][$n]['id']==$context['TPortal']['SSI_boards'][2] ? 'selected' : '' , '>'.$context['TPortal']['boards'][$n]['name'].'</option>';
		}
		echo '
							</select><br>';
		// board 4
		echo '
							<select size="1" name="tp_ssiboard4"><option value="0">',$txt['tp-none-'],'</option>';
		for($n=0 ; $n<$tn; $n++){
			echo '
								<option value="'.$context['TPortal']['boards'][$n]['id'].'" ' , isset($context['TPortal']['SSI_boards'][3]) && $context['TPortal']['boards'][$n]['id']==$context['TPortal']['SSI_boards'][3] ? 'selected' : '' , '>'.$context['TPortal']['boards'][$n]['name'].'</option>';
		}
		echo '
							</select><br>';
		// board 5
		echo '
							<select size="1" name="tp_ssiboard5"><option value="0">',$txt['tp-none-'],'</option>';
		for($n=0 ; $n<$tn; $n++){
			echo '
								<option value="'.$context['TPortal']['boards'][$n]['id'].'" ' , isset($context['TPortal']['SSI_boards'][4]) && $context['TPortal']['boards'][$n]['id']==$context['TPortal']['SSI_boards'][4] ? 'selected' : '' , '>'.$context['TPortal']['boards'][$n]['name'].'</option>';
		}

		echo '
							</select><br><br>
						</dd>
						<dt>
							<label for="field_name">', $txt['tp-lengthofposts'], '</label>
						</dt>
						<dd>
						  <input name="tp_frontpage_limit_len" size="5" maxsize="5" type="text" value="' ,$context['TPortal']['frontpage_limit_len'], '"><br><br>
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
				<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}

// edit category
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
				<div class="addborder">
					<div class="float-items" style="width:120px;"><strong>' , $txt['tp-actions'] , '</strong></div>
					<div class="float-items" style="max-width:76%;"><strong>' , $txt['tp-name'] , '</strong></div>
					<p class="clearthefloat"></p>
				</div>';

	if(isset($context['TPortal']['editcats']) && count($context['TPortal']['editcats'])>0)
	{
		$alt=true;
		foreach($context['TPortal']['editcats'] as $c => $cat)
		{
			echo '
				<div class="addborder">
					<div class="float-items" style="width:120px;">
						<a href="' . $scripturl . '?cat=' . $cat['id'] . '"><img src="' . $settings['tp_images_url'] . '/TPfilter.png" alt="" /></a>
						<a href="' . $scripturl . '?action=tpadmin;sa=addcategory;child;cu=' . $cat['id'] . '" title="' . $txt['tp-addsubcategory'] . '"><img src="' . $settings['tp_images_url'] . '/TPadd.png" alt="" /></a>
						<a href="' . $scripturl . '?action=tpadmin;sa=addcategory;copy;cu=' . $cat['id'] . '" title="' . $txt['tp-copycategory'] . '"><img src="' . $settings['tp_images_url'] . '/TPcopy.png" alt="" /></a>
						&nbsp;&nbsp;<a href="' . $scripturl . '?action=tpadmin;catdelete='.$cat['id'].';' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="javascript:return confirm(\''.$txt['tp-confirmcat1'].'  \n'.$txt['tp-confirmcat2'].'\')" title="' . $txt['tp-delete'] . '"><img src="' . $settings['tp_images_url'] . '/tp-delete_shout.png" alt="" /></a>
					</div>
					<div class="float-items' , '" style="max-width:76%;">
					' , str_repeat("-",$cat['indent']) , '
						<a href="' . $scripturl . '?action=tpadmin;sa=categories;cu='.$cat['id'].'"><b>' , $cat['name'] , '</b></a>
						' , isset($context['TPortal']['cats_count'][$cat['id']]) ? '<a href="' . $scripturl. '?action=tpadmin;sa=articles;cu='.$cat['id'].'">('.$context['TPortal']['cats_count'][$cat['id']].' ' . ($context['TPortal']['cats_count'][$cat['id']]>1 ? $txt['tp-articles'] : $txt['tp-article']) . ')</a>' : '' , '
					</div>
					<p class="clearthefloat"></p>
				</div>';
			$alt = !$alt;
		}
	}
	echo '
				<br><div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'">
					</div>
				</div>
		</div>
	</form>';
}
// edit category
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
							<label for="field_name">', $txt['tp-name'], '</label>
						</dt>
						<dd>
							<input size="40" name="tp_category_value1" type="text" value="' ,html_entity_decode($mg['value1']), '">
						<dd>
						<dt>
							<label for="field_name">', $txt['tp-parent'], '</label>
						</dt>
						<dd>
							<select name="tp_category_value2" style="max-width:100%;">
								<option value="0"' , $mg['value2']==0 || $mg['value2']=='9999' ? ' selected="selected"' : '' , '>' , $txt['tp-noname'] , '</option>';
			foreach($context['TPortal']['editcats'] as $b => $parent)
			{
				if($parent['id']!= $mg['id'])
					echo '
										<option value="' . $parent['id'] . '"' , $parent['id']==$mg['value2'] ? ' selected="selected"' : '' , '>' , str_repeat("-",$parent['indent']) ,' ' , html_entity_decode($parent['name']) , '</option>';
			}
			echo '
									</select>
						<dd>
						<dt>
							<label for="field_name">', $txt['tp-sorting'], '</label>
						</dt>
						<dd>
							<select name="tp_category_sort">
								<option value="date"' , isset($mg['sort']) && $mg['sort']=='date' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions1'] , '</option>
								<option value="authorID"' , isset($mg['sort']) && $mg['sort']=='authorID' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions2'] , '</option>
								<option value="parse"' , isset($mg['sort']) && $mg['sort']=='parse' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions3'] , '</option>
								<option value="id"' , isset($mg['sort']) && $mg['sort']=='id' ? ' selected="selected"' : '' , '>' , $txt['tp-sortoptions4'] , '</option>
							</select>
							<select name="tp_category_sortorder">
								<option value="desc"' , isset($mg['sortorder']) && $mg['sortorder']=='desc' ? ' selected="selected"' : '' , '>' , $txt['tp-sortdirection1'] , '</option>
								<option value="asc"' , isset($mg['sortorder']) && $mg['sortorder']=='asc' ? ' selected="selected"' : '' , '>' , $txt['tp-sortdirection2'] , '</option>
							</select>
						<dd>
						<dt>
							<label for="field_name">', $txt['tp-articlecount'], '</label>
						</dt>
						<dd>
							<input size="6" name="tp_category_articlecount" type="text" value="' , empty($mg['articlecount']) ? $context['TPortal']['frontpage_limit'] : $mg['articlecount']  , '">
						<dd>
						<dt>
							<label for="field_name">', $txt['tp-shortname'], '</label>
						</dt>
						<dd>
							<input size="20" name="tp_category_value8" type="text" value="' , isset($mg['value8']) ? $mg['value8'] : '' , '">
						</dd>
					</dl>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
					<hr>
					<div>
						<div><strong><label for="field_name">', $txt['tp-catlayouts'], '</label></strong></div>

						<div class="tpartlayoutfp"><input name="tp_category_layout" type="radio" value="1" ' ,
							$mg['layout']==1 ? 'checked' : '' , '> A ' ,
							$mg['layout']==1 ? '' : '' , '
								<div class="tborder" style="margin-top: 5px;">
									<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_a.png"/>
								</div>
							</div>
							<div class="tpartlayoutfp"><input name="tp_category_layout" type="radio" value="2" ' ,
							$mg['layout']==2 ? 'checked' : '' , '> B ' ,
							$mg['layout']==2 ? '' : '' , '
								<div class="tborder" style="margin-top: 5px;">
									<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_b.png"/>
								</div>
							</div>
							<div class="tpartlayoutfp"><input name="tp_category_layout" type="radio" value="3" ' ,
							$mg['layout']==3 ? 'checked' : '' , '> C ' ,
							$mg['layout']==3 ? '' : '' , '
								<div class="tborder" style="margin-top: 5px;">
									<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_c.png"/>
								</div>
							</div>
							<div class="tpartlayoutfp"><input name="tp_category_layout" type="radio" value="4" ' ,
							$mg['layout']==4 ? 'checked' : '' , '> D ' ,
							$mg['layout']==4 ? '' : '' , '
								<div class="tborder" style="margin-top: 5px;">
									<img border="0" src="' .$settings['tp_images_url']. '/edit_art_cat_d.png"/>
								</div>
							</div>
							<p class="clearthefloat"></p><br>
						</div>
						<div>
							<div padding-div">
								<div><strong>'.$txt['tp-articlelayouts']. ':</strong></div>
								<div>';
		foreach($context['TPortal']['admin_layoutboxes'] as $box)
			echo '
									<div class="tpartlayouttype">
										<input type="radio" name="tp_category_catlayout" value="'.$box['value'].'"' , $mg['catlayout']==$box['value'] ? ' checked="checked"' : '' , '>
										'.$box['label'].'<br><img style="margin: 4px 4px 4px 10px;" src="' , $settings['tp_images_url'] , '/TPcatlayout'.$box['value'].'.png" alt="tplayout'.$box['value'].'" />
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
						echo '</div>
								<br style="clear: both;" />
                                <h4>', $txt['reset_custom_template_layout'] ,'</h4>
								<textarea id="tp_customlayout" name="tp_category_value9">' . $mg['value9'] . '</textarea><br><br>
							</div>
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
								<strong><label for="field_name">', $txt['tp-allpanels'], '</label></strong>
							</dt>
							<dt>
								<label for="field_name">', $txt['tp-displayleftpanel'], '</label>
							</dt>
							<dd>
								<input type="checkbox" name="tp_category_leftpanel" value="1"' , !empty($mg['leftpanel']) ? ' checked="checked"' : '' ,' />
							<dd>
							<dt>
								<label for="field_name">', $txt['tp-displayrightpanel'], '</label>
							</dt>
							<dd>
								<input type="checkbox" name="tp_category_rightpanel" value="1"' , !empty($mg['rightpanel']) ? ' checked="checked"' : '' ,' />
							<dd>
							<dt>
								<label for="field_name">', $txt['tp-displaytoppanel'], '</label>
							</dt>
							<dd>
								<input type="checkbox" name="tp_category_toppanel" value="1"' , !empty($mg['toppanel']) ? ' checked="checked"' : '' ,' />
							<dd>
							<dt>
								<label for="field_name">', $txt['tp-displaybottompanel'], '</label>
							</dt>
							<dd>
								<input type="checkbox" name="tp_category_bottompanel" value="1"' , !empty($mg['bottompanel']) ? ' checked="checked"' : '' ,' />
							<dd>
							<dt>
								<label for="field_name">', $txt['tp-displayupperpanel'], '</label>
							</dt>
							<dd>
								<input type="checkbox" name="tp_category_centerpanel" value="1"' , !empty($mg['centerpanel']) ? ' checked="checked"' : '' ,' />
							<dd>
							<dt>
								<label for="field_name">', $txt['tp-displaylowerpanel'], '</label>
							</dt>
							<dd>
								<input type="checkbox" name="tp_category_lowerpanel" value="1"' , !empty($mg['lowerpanel']) ? ' checked="checked"' : '' ,' />
							<dd>
						</dl>
						<dl class="settings">
							<dt><div class="font-strong">'.$txt['tp-allowedgroups']. ':</div>
							<div class="tp_largelist2">';
		// loop through and set membergroups
		$tg=explode(',',$mg['value3']);
		foreach($context['TPmembergroups'] as $g)
		{
			if($g['posts']=='-1' && $g['id']!='1')
			{
				echo '<input name="tp_category_group_'.$g['id'].'" type="checkbox" value="'.$mg['id'].'"';
				if(in_array($g['id'],$tg))
					echo ' checked';
				echo '> '.$g['name'].' <br>';
			}
		}
		// if none is chosen, have a control value
					echo '</div><br><input type="checkbox" onclick="invertAll(this, this.form, \'tp_category_group\');" /> '.$txt['tp-checkall'].'<input name="tp_catgroup-2" type="hidden" value="'.$mg['id'].'">
						</dt>
					</dl>
					</div>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
			</div>
	</form>';
}

// NEW category
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
							<label for="field_name"><h4>'.$txt['tp-name'].':</h></label>
						</dt>
						<dd><input name="tp_cat_name" type="text" value=""><br><br>
						</dd>';
		// set up category to be sub of
		echo '
						<dt>
							<label for="field_name"><h4>'.$txt['tp-subcatof'].'</h></label>
						</dt>
						<dd>
						<select size="1" name="tp_cat_parent">
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
				<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';
}
// edit articles
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
				<div class="addborder">
					<div class="pos float-items" style="width:68%;"><strong>' , $txt['tp-name'] , '</strong></div>
					<div class="title-admin-area float-items" style="width:9%;"><strong>' , $txt['tp-articles'] , '</strong></div>
					<div class="title-admin-area float-items" style="width:65px;"><strong>' , $txt['tp-actions'] , '</strong></div>
					<p class="clearthefloat"></p>
				</div>';
		$alt=true;
		foreach($context['TPortal']['cats'] as $c => $cat)
		{
			if(in_array($cat['parent'],$context['TPortal']['basecats']))
			{
				echo '
				<div class="addborder">
					<div style="width:70%;" class="float-items">
					  <a href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$cat['id'].'"><b>' , $cat['name'] , '</b></a>
					</div>
					<div style="width:8%;" class="float-items">' , isset($context['TPortal']['cats_count'][$cat['id']]) ? $context['TPortal']['cats_count'][$cat['id']] : '0' , '</div>
					<div style="width:65px;" class="float-items">
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
				<div class="windowbg">
					<div style="max-width:78%;" class="float-items">&nbsp;&nbsp;<img src="' . $settings['tp_images_url'] . '/TPtree_article.png" alt="" />
						<a href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$subcat['id'].'">' , $subcat['name'] , '</a>
					</div>
					<div style="width:8%;" class="float-items">' , isset($context['TPortal']['cats_count'][$subcat['id']]) ? $context['TPortal']['cats_count'][$subcat['id']] : '0' , '</div>
					<div style="width:8%;" class="float-items">&nbsp;</div>
					<p class="clearthefloat"></p>
				</div>';
					}
				}
				$alt = !$alt;
			}
		}
	}
	// ok, so onto the actual articles
	if(isset($context['TPortal']['arts']))
	{
		echo '
								<div class="catbg3 addborderleft">
									<div style="width:7%;" class="pos float-items">' , $context['TPortal']['sort']=='parse' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on position" /> ' : '' , '<a title="Sort on position" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=parse"><strong>' , $txt['tp-pos'] , '</strong></a></div>
									<div style="width:25%;" class="name float-items">' , $context['TPortal']['sort']=='subject' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on subject" /> ' : '' , '<a title="Sort on subject" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=subject"><strong>' , $txt['tp-name'] , '</strong></a></div>
									<div style="width:10%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=author_id"><strong>' , $txt['tp-author'] , '</strong></a></div>
									<div style="width:17%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=date"><strong>' , $txt['tp-date'] , '</strong></a></div>
									<div style="width:20%;" class="title-admin-area float-items">
										' , $context['TPortal']['sort']=='off' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on active" /> ' : '' , '<a title="Sort on active" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=off"><img src="' . $settings['tp_images_url'] . '/TPactive2.png" alt="" /></a>
										' , $context['TPortal']['sort']=='frontpage' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on frontpage" /> ' : '' , '<a title="Sort on frontpage" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=frontpage"><img src="' . $settings['tp_images_url'] . '/TPfront.png" alt="*" /></a>
										' , $context['TPortal']['sort']=='sticky' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on sticky" /> ' : '' , '<a title="Sort on sticky" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=sticky"><img src="' . $settings['tp_images_url'] . '/TPsticky1.png" alt="" /></a>
										' , $context['TPortal']['sort']=='locked' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on locked" /> ' : '' , '<a title="Sort on locked" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=locked"><img src="' . $settings['tp_images_url'] . '/TPlock1.png" alt="" /></a>
									</div>
									<div style="width:7%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=type"><strong>' , $txt['tp-type'] , '</strong></a></div>
									<p class="clearthefloat"></p>
								 </div>';

		if(!empty($context['TPortal']['pageindex']))
			echo '
								<div class="middletext padding-div addborder">
									'.$context['TPortal']['pageindex'].'
								</div>';

		foreach($context['TPortal']['arts'] as $a => $alink)
		{
			$alink['pos'] = $alink['pos']=='' ? 0 : $alink['pos'];
			$catty = $alink['category'];

			echo '
								<div class="addborder">
									<div style="width:7%;" class="adm-pos float-items">
										<a name="article'.$alink['id'].'"></a><input type="text" size="2" value="'.$alink['pos'].'" name="tp_article_pos'.$alink['id'].'" />
									</div>
									<div style="width:25%;" class="adm-name float-items">
										' , $alink['locked']==0 ? '<a href="' . $scripturl . '?action=tpadmin;sa=editarticle' . $alink['id'] . '">' . $alink['subject'].'</a>' : '&nbsp;' . $alink['subject'] , '
									</div>
									<a href="" class="clickme">'.$txt['tp-more'].'</a>
			                        <div class="box" style="width:64%;float:left;">
									  <div style="width:17%;" class="fullwidth-on-res-layout float-items">
									    <div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=author_id">' , $txt['tp-author'] , '</a></div>
									    <div id="size-on-respnsive-layout"><a href="' . $scripturl . '?action=profile;u=' , $alink['authorID'], '">'.$alink['author'] .'</a></div>
									  </div>
									  <div style="width:25.5%;" class="smalltext fullwidth-on-res-layout float-items">
									    <div id="show-on-respnsive-layout"> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=date">' , $txt['tp-date'] , '</a></div>
									    <div id="size-on-respnsive-layout">' , timeformat($alink['date']) , '</div>
									  </div>
									  <div style="width:34.5%;" class="smalltext fullwidth-on-res-layout float-items">
									     <div id="show-on-respnsive-layout">
										  ' , $context['TPortal']['sort']=='off' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on active" /> ' : '' , '<a title="Sort on active" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=off"><img src="' . $settings['tp_images_url'] . '/TPactive2.png" alt="" /></a>
										  ' , $context['TPortal']['sort']=='frontpage' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on frontpage" /> ' : '' , '<a title="Sort on frontpage" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=frontpage"><img src="' . $settings['tp_images_url'] . '/TPfront.png" alt="*" /></a>
										  ' , $context['TPortal']['sort']=='sticky' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on sticky" /> ' : '' , '<a title="Sort on sticky" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=sticky"><img src="' . $settings['tp_images_url'] . '/TPsticky1.png" alt="" /></a>
										  ' , $context['TPortal']['sort']=='locked' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on locked" /> ' : '' , '<a title="Sort on locked" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=locked"><img src="' . $settings['tp_images_url'] . '/TPlock1.png" alt="" /></a>
										 </div>
										 <div id="size-on-respnsive-layout">
										  <img style="cursor: pointer;" class="toggleActive" id="artActive' .$alink['id']. '" title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TPactive' , $alink['off']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-activate'].'"  />
										  <a href="',$scripturl, '?page=',$alink['id'],'"><img title="'.$txt['tp-preview'].'" src="' .$settings['tp_images_url']. '/TPfilter.png" alt="" /></a>
										  ' , $alink['locked']==0 ?
										  '<a href="' . $scripturl . '?action=tpadmin;sa=editarticle'.$alink['id']. '"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>' : '<img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm2.png" alt="'.$txt['tp-islocked'].'"  />' , '
										  <img style="cursor: pointer;" class="toggleFront" id="artFront' .$alink['id']. '" title="'.$txt['tp-setfrontpage'].'" border="0" src="' .$settings['tp_images_url']. '/TPfront' , $alink['frontpage']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-setfrontpage'].'"  />
										  <img style="cursor: pointer;" class="toggleSticky" id="artSticky' .$alink['id']. '" title="'.$txt['tp-setsticky'].'" border="0" src="' .$settings['tp_images_url']. '/TPsticky' , $alink['sticky']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setsticky'].'"  />
										  <img style="cursor: pointer;" class="toggleLock" id="artLock' .$alink['id']. '" title="'.$txt['tp-setlock'].'" border="0" src="' .$settings['tp_images_url']. '/TPlock' , $alink['locked']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setlock'].'"  />
										  <img style="cursor: pointer;" class="toggleFeatured" id="artFeatured' .$alink['id']. '" title="'.$txt['tp-featured'].'" border="0" src="' .$settings['tp_images_url']. '/TPflag' , $alink['featured']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-turnoff'].'"  />
									    </div>
									</div>
									<div style="width:7%;text-transform:uppercase;" class="smalltext fullwidth-on-res-layout float-items">
									 	<div id="show-on-respnsive-layout">
								         ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=articles;cu='.$context['TPortal']['categoryID'].';sort=type">' , $txt['tp-type'] , '</a>
									    </div>
										' , empty($alink['type']) ? 'html' : $alink['type'] , '
									</div>
									<div style="width:6%;" class="smalltext fullwidth-on-res-layout float-items" align="center">
									    <div id="show-on-respnsive-layout">Delete</div>
										<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';artdelete=' .$alink['id'] , !empty($_GET['cu']) ? ';cu=' . $_GET['cu'] : '' , '" onclick="javascript:return confirm(\''.$txt['tp-articleconfirmdelete'].'\')">
										<img title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/tp-delete_shout.png" alt="'.$txt['tp-delete'].'"  />
										</a>
									 </div>
									 <p class="clearthefloat"></p>
									</div><p class="clearthefloat"></p>
								</div>';
			}
			if( !empty($context['TPortal']['pageindex']))
				echo '
								<div class="middletext padding-div">
									'.$context['TPortal']['pageindex'].'
								</div>';
			echo '
							<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'">
						<input name="tpadmin_form_category" type="hidden" value="' . $catty . '"></div>';
	}
	else
		echo '
				<div class="padding-div"></div>';

		echo '
		</div></div>
	</form>';
}

// uncategorized
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
	if(isset($context['TPortal']['arts_nocat']))
	{
		echo '
				<div class="windowbg noup padding-div">
					<div>
								<div class="catbg3 addborderleft">
									<div style="width:7%;" class="pos float-items">' , $context['TPortal']['sort']=='parse' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on position" /> ' : '' , '<a title="Sort on position" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=parse">' , $txt['tp-pos'] , '</a></div>
									<div style="width:20%;" class="name float-items">' , $context['TPortal']['sort']=='subject' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on subject" /> ' : '' , '<a title="Sort on subject" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=subject">' , $txt['tp-name'] , '</a></div>
									<div style="width:10%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=author_id">' , $txt['tp-author'] , '</a></div>
									<div style="width:15%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=date">' , $txt['tp-date'] , '</a></div>
									<div style="width:20%;" class="title-admin-area float-items">
										' , $context['TPortal']['sort']=='off' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on active" /> ' : '' , '<a title="Sort on active" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=off"><img src="' . $settings['tp_images_url'] . '/TPactive2.png" alt="" /></a>
										' , $context['TPortal']['sort']=='frontpage' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on frontpage" /> ' : '' , '<a title="Sort on frontpage" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=frontpage"><img src="' . $settings['tp_images_url'] . '/TPfront.png" alt="*" /></a>
										' , $context['TPortal']['sort']=='sticky' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on sticky" /> ' : '' , '<a title="Sort on sticky" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=sticky"><img src="' . $settings['tp_images_url'] . '/TPsticky1.png" alt="" /></a>
										' , $context['TPortal']['sort']=='locked' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on locked" /> ' : '' , '<a title="Sort on locked" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=locked"><img src="' . $settings['tp_images_url'] . '/TPlock1.png" alt="" /></a>
									</div>
									<div  style="width:15%;" class="title-admin-area float-items"> ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=type">' , $txt['tp-type'] , '</a></div>
									<p class="clearthefloat"></p>
								</div>';

		if(!empty($context['TPortal']['pageindex']))
			echo '
								<div class="middletext padding-div addborder">
									'.$context['TPortal']['pageindex'].'
								</div>';

		foreach($context['TPortal']['arts_nocat'] as $a => $alink)
		{
			$alink['pos'] = $alink['pos']=='' ? 0 : $alink['pos'];
			$catty = $alink['category'];

			echo '
								<div class="addborder">
									<div style="width:7%;max-width:100%;" class="adm-pos float-items">
										<a name="article'.$alink['id'].'"></a><input type="text" size="2" value="'.$alink['pos'].'" name="tp_article_pos'.$alink['id'].'" />
									</div>
									<div style="width:20%;" class="adm-name float-items">
										' , $alink['locked']==0 ? '<a href="' . $scripturl . '?action=tpadmin;sa=editarticle' . $alink['id'] . '">' . (!empty($alink['subject']) ? $alink['subject'] : $txt['tp-noname']) . '</a>' : '&nbsp;' . (!empty($alink['subject']) ? $alink['subject'] : $txt['tp-noname']) , '
									</div>
									<a href="" class="clickme">'.$txt['tp-more'].'</a>
									<div class="box" style="width:69%;float:left;">
									  <div style="width:17%;" class="fullwidth-on-res-layout float-items">
									    <div id="show-on-respnsive-layout">
									       ' , $context['TPortal']['sort']=='author_id' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on author" /> ' : '' , '<a title="Sort on author" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=author_id">' , $txt['tp-author'] , '</a>
									    </div>
									    <div id="size-on-respnsive-layout">
									     <a href="' . $scripturl . '?action=profile;u=' , $alink['authorID'], '">'.$alink['author'] .'</a>
									    </div>
									  </div>
									  <div style="width:22%;" class="smalltext fullwidth-on-res-layout float-items">
									    <div id="show-on-respnsive-layout">
									     ' , $context['TPortal']['sort']=='date' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on date" /> ' : '' , '<a title="Sort on date" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=date">' , $txt['tp-date'] , '</a>
									    </div>
									    <div id="size-on-respnsive-layout">' , timeformat($alink['date']) , '</div>
									  </div>
									  <div style="width:31%;" class="smalltext fullwidth-on-res-layout float-items">
									    <div id="show-on-respnsive-layout">
										 ' , $context['TPortal']['sort']=='off' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on active" /> ' : '' , '<a title="Sort on active" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=off"><img src="' . $settings['tp_images_url'] . '/TPactive2.png" alt="" /></a>
										 ' , $context['TPortal']['sort']=='frontpage' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on frontpage" /> ' : '' , '<a title="Sort on frontpage" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=frontpage"><img src="' . $settings['tp_images_url'] . '/TPfront.png" alt="*" /></a>
										 ' , $context['TPortal']['sort']=='sticky' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on sticky" /> ' : '' , '<a title="Sort on sticky" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=sticky"><img src="' . $settings['tp_images_url'] . '/TPsticky1.png" alt="" /></a>
										 ' , $context['TPortal']['sort']=='locked' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on locked" /> ' : '' , '<a title="Sort on locked" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=locked"><img src="' . $settings['tp_images_url'] . '/TPlock1.png" alt="" /></a>
										</div>
										<div id="size-on-respnsive-layout">
										  <img style="cursor: pointer;" class="toggleActive" id="artActive' .$alink['id']. '" title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TPactive' , $alink['off']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-activate'].'"  />
										  <a href="',$scripturl, '?page=',$alink['id'],'"><img title="'.$txt['tp-preview'].'" src="' .$settings['tp_images_url']. '/TPfilter.png" alt="" /></a>
										  ' , $alink['locked']==0 ?
										  '<a href="' . $scripturl . '?action=tpadmin;sa=editarticle'.$alink['id']. '"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm.png" alt="'.$txt['tp-edit'].'"  /></a>' : '<img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPconfig_sm2.png" alt="'.$txt['tp-islocked'].'"  />' , '

										  <img style="cursor: pointer;" class="toggleFront" id="artFront' .$alink['id']. '" title="'.$txt['tp-setfrontpage'].'" border="0" src="' .$settings['tp_images_url']. '/TPfront' , $alink['frontpage']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-setfrontpage'].'"  />
										  <img style="cursor: pointer;" class="toggleSticky" id="artSticky' .$alink['id']. '" title="'.$txt['tp-setsticky'].'" border="0" src="' .$settings['tp_images_url']. '/TPsticky' , $alink['sticky']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setsticky'].'"  />
										  <img style="cursor: pointer;" class="toggleLock" id="artLock' .$alink['id']. '" title="'.$txt['tp-setlock'].'" border="0" src="' .$settings['tp_images_url']. '/TPlock' , $alink['locked']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setlock'].'"  />
										  <img style="cursor: pointer;" class="toggleFeatured" id="artFeatured' .$alink['id']. '" title="'.$txt['tp-featured'].'" border="0" src="' .$settings['tp_images_url']. '/TPflag' , $alink['featured']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-turnoff'].'"  />
									     </div>
									  </div>
									  <div style="width:7%;text-transform:uppercase;" class="smalltext fullwidth-on-res-layout float-items" align="center" >
									     <div id="show-on-respnsive-layout">
									      ' , $context['TPortal']['sort']=='type' ? '<img src="' . $settings['tp_images_url'] . '/TPsort_down.png" alt="Sort on type" /> ' : '' , '<a title="Sort on type" href="' . $scripturl . '?action=tpadmin;sa=strays;sort=type">' , $txt['tp-type'] , '</a>
									     </div>
										 ' , empty($alink['type']) ? 'html' : $alink['type'] , '
									  </div>
									  <div style="width:7%;" class="smalltext fullwidth-on-res-layout float-items" align="center">
									    <div id="show-on-respnsive-layout">Delete</div>
										<a href="' . $scripturl . '?action=tpadmin;cu=-1;' . $context['session_var'] . '=' . $context['session_id'].';artdelete=' .$alink['id']. '" onclick="javascript:return confirm(\''.$txt['tp-articleconfirmdelete'].'\')">
										<img title="'.$txt['tp-delete'].'" border="0" src="' .$settings['tp_images_url']. '/tp-delete_shout.png" alt="'.$txt['tp-delete'].'"  />
										</a>
									  </div>
									  <div style="width:4%;" class="smalltext fullwidth-on-res-layout float-items" align="center">
									    <div id="show-on-respnsive-layout">Select</div>
										<input type="checkbox" name="tp_article_stray'.$alink['id'].'" value="1"  />
									  </div><p class="clearthefloat"></p>
								  </div><p class="clearthefloat"></p>
								</div>';
			}
			if( !empty($context['TPortal']['pageindex']))
				echo '
								<div class="middletext padding-div">
									'.$context['TPortal']['pageindex'].'
								</div>';
			echo '
						</div>';

		if(isset($context['TPortal']['allcats']))
		{
			echo '	<p style="text-align: right;padding:1%;max-width:100%;">
							<select name="tp_article_cat">
								<option value="0">' . $txt['tp-createnew'] . '</option>';
			foreach($context['TPortal']['allcats'] as $submg)
  					echo '
								<option value="'.$submg['id'].'">',  $submg['indent']>1 ? str_repeat("-", ($submg['indent']-1)) : '' , ' '. $txt['tp-assignto'] . $submg['name'].'</option>';
			echo '
							</select>
							<input name="tp_article_new" value="" size="40"  /> &nbsp;
						</p>';
		}
		echo '
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>';
	}
	else
		echo '
				<div class="windowbg2">
					<div class="windowbg3"></div>
				</div>';

	echo '
		</div>
	</form>';
}

// edit/add single article
function template_editarticle($type = '')
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language, $smcFunc;

	$tpmonths=array(' ','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
	$mg = $context['TPortal']['editarticle'];
	if(!isset($context['TPortal']['categoryNAME']))
		$context['TPortal']['categoryNAME'] = $txt['tp-uncategorised'];

	echo '
	<form accept-charset="', $context['character_set'], '" name="TPadmin3" action="' . $scripturl . '?action=tpadmin" enctype="multipart/form-data" method="post" onsubmit="submitonce(this);">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="editarticle' . $mg['id'] . '">
		<div class="cat_bar"><h3 class="catbg"><img style="margin-right: 4px;" border="0" src="' .$settings['tp_images_url']. '/TP' , $mg['off']=='1' ? 'red' : 'green' , '.png" alt=""  />' , $mg['id']=='' ? $txt['tp-addarticle']. '' .$txt['tp-incategory'] . (html_entity_decode($context['TPortal']['categoryNAME'])) : $txt['tp-editarticle']. ' ' .html_entity_decode($mg['subject']) , '' , $mg['id']==0 ? '' : '&nbsp;-&nbsp;<a href="'.$scripturl.'?page='.$mg['id'].'">['.$txt['tp-preview'].']</a>';
	echo '</h3></div>
		<div id="edit-add-single-article" class="admintable admin-area">
		<div class="windowbg noup">
			<div class="formtable padding-div">
			<div>
						<div class="font-strong">' , $txt['tp-title'] , ':</div>
						<input style="width: 92%;" name="tp_article_subject" type="text" value="'. htmlentities($mg['subject'], ENT_QUOTES) .'">
					</div>
					<div>
						<div class="font-strong">'.$txt['tp-shortname_article'].'&nbsp;</div>
						<input size=20 name="tp_article_shortname" type="text" value="'.$mg['shortname'].'">
					</div>
					<br>
					<div>';

				$tp_use_wysiwyg = $context['TPortal']['show_wysiwyg'];

				if($mg['articletype'] == 'php')
					echo '
							<textarea name="tp_article_body" id="tp_article_body" wrap="auto">' ,  $mg['body'] , '</textarea><br>';
				elseif($tp_use_wysiwyg > 0 && ($mg['articletype'] == '' || $mg['articletype'] == 'html'))
					TPwysiwyg('tp_article_body', $mg['body'], true, 'qup_tp_article_body', $tp_use_wysiwyg);
				elseif($tp_use_wysiwyg == 0 && ($mg['articletype'] == '' || $mg['articletype'] == 'html'))
					echo '
							<textarea name="tp_article_body" id="tp_article_body" wrap="auto">' , $mg['body'], '</textarea><br>';
				elseif($mg['articletype'] == 'bbc')
					TP_bbcbox($context['TPortal']['editor_id']);
				else
					echo $txt['tp-importarticle'] , ' &nbsp;<input size="60" name="tp_article_fileimport" type="text" value="' , $mg['fileimport'] , '">' ;

					echo '
					</div>
				<div class="padding-div"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			<hr><br>
				<dl class="settings">
					<dt>
						<label for="field_name">', $txt['tp-status'], '</label>
					</dt>
					<dd>';

					if (!empty($context['TPortal']['editing_article']))
					{
						// show checkboxes since we have these features aren't available until the article is saved.
						echo '
							<img style="cursor: pointer;" class="toggleFront" id="artFront' .$mg['id']. '" title="'.$txt['tp-setfrontpage'].'" border="0" src="' .$settings['tp_images_url']. '/TPfront' , $mg['frontpage']=='1' ? '' : '2' , '.png" alt="'.$txt['tp-setfrontpage'].'"  />
							<img style="cursor: pointer;" class="toggleSticky" id="artSticky' .$mg['id']. '" title="'.$txt['tp-setsticky'].'" border="0" src="' .$settings['tp_images_url']. '/TPsticky' , $mg['sticky']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setsticky'].'"  />
							<img style="cursor: pointer;" class="toggleLock" id="artLock' .$mg['id']. '" title="'.$txt['tp-setlock'].'" border="0" src="' .$settings['tp_images_url']. '/TPlock' , $mg['locked']=='1' ? '1' : '2' , '.png" alt="'.$txt['tp-setlock'].'"  />';
					}
					else
					{
						// Must be a new article, so lets show the check boxes instead.
						echo '
							<input type="checkbox" id="artFront'. $mg['id']. '" name="tp_article_frontpage" value="1" /> '. $txt['tp-setfrontpage']. '<br>
							<input type="checkbox" id="artSticky'. $mg['id']. '" name="tp_article_sticky" value="1" /> '. $txt['tp-setsticky']. '<br>
							<input type="checkbox" id="artLock'. $mg['id']. '" name="tp_article_locked" value="1" /> '. $txt['tp-setlock']. '';
					}
						echo '<br><br>
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-approved'], '</label>
					</dt>
					<dd>
							<input name="tp_article_approved" type="radio" value="0" ', $mg['approved']=='0' ? 'checked' : '' ,'>  '.$txt['tp-no'].'
							<input name="tp_article_approved" type="radio" value="1" ', $mg['approved']=='1' ? 'checked' : '' ,'>  '.$txt['tp-yes'].'<br><br>
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-author'], '</label>
					</dt>
					<dd>
							<b><a href="' . $scripturl . '?action=profile;u='.$mg['authorID'].'" target="_blank">'.$mg['realName'].'</a></b>
							&nbsp;' . $txt['tp-assignnewauthor'] . ' <input size="8" maxsize="12" name="tp_article_authorid" value="' . $mg['authorID'] . '" /><br><br>
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-created'], '</label>
					</dt>
					<dd>';
				// day
				echo '
							<input name="tp_article_timestamp" type="hidden" value="'.$mg['date'].'">
							<select size="1" name="tp_article_day">';

				$day = date("j",$mg['date']);
				$month = date("n",$mg['date']);
				$year = date("Y",$mg['date']);
				$hour = date("G",$mg['date']);
				$minute = date("i",$mg['date']);

				for($a=1; $a<32;$a++)
					echo '
								<option value="'.$a.'" ' , $day==$a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>';
				// month
				echo '
							<select size="1" name="tp_article_month">';
				for($a=1; $a<13; $a++)
					echo '
								<option value="'.$a.'" ' , $month==$a ? ' selected' : '' , '>'.$tpmonths[$a].'</option>  ';
				echo '
							</select>';
				// year
				echo '
							<select size="1" name="tp_article_year">';

				$now=date("Y",time())+1;
				for($a=2004; $a<$now; $a++)
					echo '
								<option value="'.$a.'" ' , $year==$a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>';
				// hours
				echo ' -
							<select size="1" name="tp_article_hour">';
				for($a=0; $a<24;$a++)
					echo '
								<option value="'.$a.'" ' , $hour==$a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select>';
				// minutes
				echo ' <b>:</b>
							<select size="1" name="tp_article_minute">';
				for($a=0; $a<60;$a++)
					echo '
								<option value="'.$a.'" ' , $minute==$a ? ' selected' : '' , '>'.$a.'</option>  ';
				echo '
							</select><br><br>
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-published'], '</label>
					</dt>
					<dd><div class="description" style="line-height: 1.6em; padding: 5px;">
							<b>',$txt['tp-pub_start'],': </b><br>';
				// day
				echo '
							<input name="tp_article_pub_start" type="hidden" value="'.$mg['pub_start'].'">
							<select size="1" name="tp_article_pubstartday">
								<option value="0">' . $txt['tp-notset'] . '</option>';

				$day = !empty($mg['pub_start']) ? date("j",$mg['pub_start']) : 0;
				$month = !empty($mg['pub_start']) ? date("n",$mg['pub_start']) : 0;
				$year = !empty($mg['pub_start']) ? date("Y",$mg['pub_start']) : 0;
				$hour = !empty($mg['pub_start']) ? date("G",$mg['pub_start']) : 0;
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
					<dt>
						<label for="field_name">', $txt['tp-category'], '</label>
					</dt>
					<dd>
						<div>
							<select size="1" name="tp_article_category">
								<option value="0">'.$txt['tp-none2'].'</option>';

				foreach($context['TPortal']['allcats'] as $cats)
				{
					if($cats['id']<9999 && $cats['id']>0)
						echo '
								<option value="'.$cats['id'].'" ', $cats['id'] == $mg['category'] ? 'selected' : '' ,'>'. str_repeat("-", isset($cats['indent']) ? $cats['indent'] : 0) .' '.$cats['name'].'</option>';
				}
				echo '
							</select>
							<a href="', $scripturl, '?action=tpadmin;sa=categories;cu='.$mg['category'].';sesc=' .$context['session_id']. '">',$txt['tp-editcategory'],'</a>
						</div><br>
					</dd>
					<dt>
						<label for="tp_article_useintro">', $txt['tp-useintro'], '</label>
					</dt>
					<dd>
							<input name="tp_article_useintro" type="radio" value="0" ', $mg['useintro']=='0' ? 'checked' : '' ,'> '.$txt['tp-no'].'
							<input name="tp_article_useintro" type="radio" value="1" ', $mg['useintro']=='1' ? 'checked' : '' ,'> '.$txt['tp-yes'].'<br>
					</dd>
				</dl>
					';
				if($mg['articletype'] == 'php' || $mg['articletype'] == '' || $mg['articletype'] == 'html')
				{
					echo '<div id="tp_article_show_intro"', ($mg['useintro'] == 0) ? 'style="display:none;">' : '>' ,
                        '<div class="font-strong">'.$txt['tp-introtext'].'</div>';

					if($tp_use_wysiwyg > 0 && ($mg['articletype'] == '' || $mg['articletype'] == 'html'))
						TPwysiwyg('tp_article_intro',  $mg['intro'], true, 'qup_tp_article_intro', $tp_use_wysiwyg, false);
					else
						echo '
							<textarea name="tp_article_intro" id="tp_article_intro" rows=5 cols=20 wrap="soft">'.$mg['intro'].'</textarea>';
					echo '
						</div>';
				}
				elseif($mg['articletype'] == 'bbc' || $mg['articletype'] == 'import')
				{
					echo '<div id="tp_article_show_intro"', ($mg['useintro'] == 0) ? 'style="display:none;">' : '>' ,
                    '<div class="font-strong">'.$txt['tp-introtext'].'</div>
					<div>
						<textarea name="tp_article_intro" id="tp_article_intro" rows=5 cols=20 wrap="on">'. $mg['intro'] .'</textarea>
					</div>
                    </div>';
				}
				echo '<br><hr>
				<dl class="settings">
					<dt>
						<label for="field_name">', $txt['tp-switchmode'], '</label>
					</dt>
					<dd>
							<input align="middle" name="tp_article_type" type="radio" value="html"' , $mg['articletype']=='' || $mg['articletype']=='html' ? ' checked="checked"' : '' ,'> '.$txt['tp-gohtml'] .'<br>
							<input align="middle" name="tp_article_type" type="radio" value="php"' , $mg['articletype']=='php' ? ' checked="checked"' : '' ,'> '.$txt['tp-gophp'] .'<br>
							<input align="middle" name="tp_article_type" type="radio" value="bbc"' , $mg['articletype']=='bbc' ? ' checked="checked"' : '' ,'> '.$txt['tp-gobbc'] .'<br>
							<input align="middle" name="tp_article_type" type="radio" value="import"' , $mg['articletype']=='import' ? ' checked="checked"' : '' ,'> '.$txt['tp-goimport'] .'<br><br>
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-display'], '</label>
					</dt>
					<dd>
							<input name="tp_article_frame" type="radio" value="theme" ' , $mg['frame']=='theme' ? 'checked' : '' , '> '.$txt['tp-useframe'].'<br>
							<input name="tp_article_frame" type="radio" value="title" ' , $mg['frame']=='title' ? 'checked' : '' , '> '.$txt['tp-usetitle'].' <br>
							<input name="tp_article_frame" type="radio" value="none" ' , $mg['frame']=='none' ? 'checked' : '' , '> '.$txt['tp-noframe'].'<br><br>
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-status'], ': <img style="margin:0 1ex;" border="0" src="' .$settings['tp_images_url']. '/TP' , $mg['off']=='1' ? 'red' : 'green' , '.png" alt=""  /></label>
					</dt>
					<dd>
							  <input name="tp_article_off" type="radio" value="1" ' , $mg['off']=='1' ? 'checked' : '' , '> '.$txt['tp-articleoff'].'<br>
							  <input name="tp_article_off" type="radio" value="0" ' , $mg['off']=='0' ? 'checked' : '' , '> '.$txt['tp-articleon'].'<br><br>
					</dd>
					<dt>
						<label for="field_name">', $txt['tp-illustration'], '</label><br>
					</dt>
					<dd>
						<div class="article_icon" style="background: top right url(' , $boardurl , '/tp-files/tp-articles/illustrations/' , !empty($mg['illustration']) ? $mg['illustration'] : 'TPno_illustration.png' , ')no-repeat;"></div>
					</dd>
				</dl>
				<dl class="settings">
					<dt>
						<label for="field_name">', $txt['tp-illustration2'], '</label><br>
						<div><img id="tp-illu" src="' , $boardurl , '/tp-files/tp-articles/illustrations/' , !empty($mg['illustration']) ? $mg['illustration'] : 'TPno_illustration.png' , '" alt="" /></div>
					</dt>
					<dd>
							<select size="10" name="tp_article_illustration" onchange="changeIllu(document.getElementById(\'tp-illu\'), this.value);">
								<option value=""' , $mg['illustration']=='' ? ' selected="selected"' : '' , '>' . $txt['tp-none2'] . '</option>';

			foreach($context['TPortal']['articons']['illustrations'] as $ill)
				echo '<option value="'.$ill['file'].'"' , $ill['file']==$mg['illustration'] ? ' selected="selected"' : '' , '>'.$ill['file'].'</option>';

			echo '
							</select>
							<p>' . $txt['tp-uploadicon'] . ':<br><input type="file" name="tp_article_illupload"></p>
					</dd>
				</dl>
					';
				// set options for an article...
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
					<div>
						<div class="font-strong">'.$txt['tp-articleoptions'].'</div>
						<div class="article-details">';
				// article details options
				echo '
							<div class="title_bar"><h3 class="titlebg">' . $txt['tp-details'] . '</h3></div>
							<br>
							<dl class="settings">
								<dt>
									<label for="field_name">', $txt['tp-articleoptions1'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[1].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[1]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions2'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[2].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[2]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions12'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[12].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[12]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions13'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[13].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[13]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions3'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[3].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[3]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions4'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[4].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[4]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions14'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[14].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[14]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions15'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[15].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[15]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions5'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[5].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[5]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions16'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[16].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[16]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions17'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[17].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[17]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions18'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[18].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[18]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions19'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[19].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[19]]) ? 'checked' : '' , '><br><br>
								</dd>
							</dl>
								<div class="title_bar">
								<h3 class="titlebg">' . $txt['tp-panels'] . '</h3>
								</div><br>
							<dl class="settings">
								<dt>
									<label for="field_name">', $txt['tp-articleoptions24'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[22].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[22]]) ? 'checked' : '' , '>
								</dd>
							</dl>
						<hr /><br>
							<dl class="settings">
								<dt>
									<label for="field_name">', $txt['tp-articleoptions7'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[7].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[7]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions22'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_rblockwidth" type="text" value="', !empty($options['rblockwidth']) ?  $options['rblockwidth'] : '' ,'"><br>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions8'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[8].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[8]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions23'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_lblockwidth" type="text" value="', !empty($options['lblockwidth']) ?  $options['lblockwidth'] : '' ,'"><br>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions6'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[6].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[6]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions9'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[9].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[9]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions10'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[10].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[10]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions11'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[11].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[11]]) ? 'checked' : '' , '>
								</dd>
							</dl><br>
								<div class="title_bar">
								<h3 class="titlebg">' . $txt['tp-others'] . '</h3>
								</div><br>
							<dl class="settings">
								<dt>
									<label for="field_name">', $txt['tp-articleoptions20'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[20].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[20]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-articleoptions21'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[21].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[21]]) ? 'checked' : '' , '>
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-showsociallinks'], '</label><br>
								</dt>
								<dd>
									<input name="tp_article_options_'.$opts[23].'" type="checkbox" value="'.$mg['id'].'" ' , isset($options[$opts[23]]) ? 'checked' : '' , '>
								</dd>
							</dl>
						<br><hr>
							<dl class="settings">
								<dt>
									<label for="field_name">', $txt['tp-checkall'], '</label><br>
								</dt>
								<dd>
									<input type="checkbox" onclick="invertAll(this, this.form, \'tp_article_options_\');" />
								</dd>
								<dt>
									<label for="field_name">', $txt['tp-chosentheme'], '</label><br>
								</dt>
								<dd>
									<select size="1" name="tp_article_idtheme">';
									echo '			<option value="0" ', $mg['ID_THEME']==0 ? 'selected' : '' ,'>'.$txt['tp-none-'].'</option>';
									foreach($context['TPthemes'] as $them)
										echo '
														<option value="'.$them['id'].'" ',$them['id']==$mg['ID_THEME'] ? 'selected' : '' ,'>'.$them['name'].'</option>';

									echo '
								</select>
								</dd>
							</dl>
								<div>
										' , $txt['tp-articleheaders'] , '<br>
										<textarea id="tp_article_intro" name="tp_article_headers" rows="5" cols="40">' , $mg['headers'] , '</textarea>
								</div>
				    </div>
				</div>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
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

// manage article icons and pictures
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
							<label for="field_name">', $txt['tp-adminicons6'], '</label><br>
						</dt>
						<dd>
							<input type="file" name="tp_article_newillustration" />
						</dd>
					</dl>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
					<hr><br>';
					
				$alt=true;
		if(count($context['TPortal']['articons']['illustrations'])>0)
		{
			foreach($context['TPortal']['articons']['illustrations'] as $icon)
			{
				echo '
					<div class="smalltext" style="float:left; padding:1%;">
						<div class="article_icon" style="background: top right url(' . $icon['background'] . ') no-repeat;">
						</div>
							<input type="checkbox" name="artillustration'.$icon['id'].'" id="artillustration'.$icon['id'].'" style="vertical-align: top;" value="'.$icon['file'].'"  /> <label style="vertical-align: top;"  for="artiillustration'.$icon['id'].'">'.$txt['tp-remove'].'?</label>
					</div>
							';
				$alt = !$alt;
			}
		}

		echo '
						<p class="clearthefloat"></p>
		<hr></div>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
		</div>
	</form>';
}

// add a block
function template_addblock()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boarddir, $boardurl, $language;

	$side = $_GET['addblock'];
	$panels = array('','left','right','center','front','bottom','top','lower');

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
										<input type="input" name="tp_addblocktitle" size="60" value="" />
									</dd>
								</dl>
								<dl class="settings">
									<dt><h3>' , $txt['tp-choosepanel'] , '</h3>
									</dt>
									<dd>
										<input type="radio" name="tp_addblockpanel" value="1" ' , $side=='left' ? 'checked' : '' , ' />' . $txt['tp-leftpanel'] . '<br>
										<input type="radio" name="tp_addblockpanel" value="2" ' , $side=='right' ? 'checked' : '' , ' />' . $txt['tp-rightpanel'] . '<br>
										<input type="radio" name="tp_addblockpanel" value="3" ' , $side=='upper' || $side=='center' ? 'checked' : '' , ' />' . $txt['tp-centerpanel'] . '<br>
										<input type="radio" name="tp_addblockpanel" value="4" ' , $side=='front' ? 'checked' : '' , ' />' . $txt['tp-frontpanel'] . '<br>
										<input type="radio" name="tp_addblockpanel" value="5" ' , $side=='bottom' ? 'checked' : '' , ' />' . $txt['tp-bottompanel'] . '<br>
										<input type="radio" name="tp_addblockpanel" value="6" ' , $side=='top' ? 'checked' : '' , ' />' . $txt['tp-toppanel'] . '<br>
										<input type="radio" name="tp_addblockpanel" value="7" ' , $side=='lower' ? 'checked' : '' , ' />' . $txt['tp-lowerpanel'] . '<br>
									</dd>
								</dl>
							<hr>
								<dl class="settings">
									<dt><h3>' , $txt['tp-chooseblock'] , '</h3>
								    </dt>
									<dd>
									<div class="tp_largelist2">
									<input type="radio" name="tp_addblock" value="1" checked />' . $txt['tp-blocktype1'] . '<br>
									<input type="radio" name="tp_addblock" value="2" />' . $txt['tp-blocktype2'] . '<br>
									<input type="radio" name="tp_addblock" value="3" />' . $txt['tp-blocktype3'] . '<br>
									<input type="radio" name="tp_addblock" value="4" />' . $txt['tp-blocktype4'] . '<br>
									<input type="radio" name="tp_addblock" value="5" />' . $txt['tp-blocktype5'] . '<br>
									<input type="radio" name="tp_addblock" value="6" />' . $txt['tp-blocktype6'] . '<br>
									<input type="radio" name="tp_addblock" value="7" />' . $txt['tp-blocktype7'] . '<br>
									<input type="radio" name="tp_addblock" value="9" />' . $txt['tp-blocktype9'] . '<br>
									<input type="radio" name="tp_addblock" value="10" />' . $txt['tp-blocktype10'] . '<br>
									<input type="radio" name="tp_addblock" value="11" />' . $txt['tp-blocktype11'] . '<br>
									<input type="radio" name="tp_addblock" value="12" />' . $txt['tp-blocktype12'] . '<br>
									<input type="radio" name="tp_addblock" value="13" />' . $txt['tp-blocktype13'] . '<br>
									<input type="radio" name="tp_addblock" value="14" />' . $txt['tp-blocktype14'] . '<br>
									<input type="radio" name="tp_addblock" value="15" />' . $txt['tp-blocktype15'] . '<br>
									<input type="radio" name="tp_addblock" value="16" />' . $txt['tp-blocktype16'] . '<br>
									<input type="radio" name="tp_addblock" value="17" />' . $txt['tp-blocktype17'] . '<br>
									<input type="radio" name="tp_addblock" value="18" />' . $txt['tp-blocktype18'] . '<br>
									<input type="radio" name="tp_addblock" value="19" />' . $txt['tp-blocktype19'] . '<br>
									<input type="radio" name="tp_addblock" value="20" />' . $txt['tp-blocktype20'] . '<br>
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
									<dt><h3>' , $txt['tp-chooseblocktype'] , '</h3>
									</dt>
									<dd>
									<div class="tp_largelist2">';

					foreach($context['TPortal']['blockcodes'] as $bc)
						echo '
									<div style="padding:1%;">
									<input type="radio" name="tp_addblock" value="' . $bc['file']. '"  />
										<b>' . $bc['name'].'</b> ' . $txt['tp-by'] . ' ' . $bc['author'] . '</b>
										<div style="margin: 4px 0; padding-left: 24px;" class="smalltext">' , $bc['text'] , '</div>
									</div>';

					echo '
									</div>
									</dd>
								</dl>
								<dl class="settings">
									<dt><h3 style="padding:1%;">' , $txt['tp-chooseblockcopy'] , '</h3>
									</dt>
									<dd>
									<div class="tp_largelist2">';

					foreach($context['TPortal']['copyblocks'] as $bc)
						echo '
									<div style="padding:1%;">
										<input type="radio" name="tp_addblock" value="mb_' . $bc['id']. '"  />' . $bc['title'].' [' . $panels[$bc['bar']] . ']
									</div>';

					echo ' 			</div>
									</dd>
								</dl>
							</div>
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
		</div>
	</form>';
}

// edit single block
function template_blockedit()
{
	global $context, $settings, $txt, $scripturl, $boardurl, $forum_version;


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
						<div>
							<div><b>'.$txt['tp-title'].':</b></div>
							<div><input size=60 name="tp_block_title" type="text" value="' .$newtitle. '"><br><br></div>
						</div>
						<div>
							<div><b>',$txt['tp-type'].':</b></div>
							<div>
								<select size="1" onchange="document.getElementById(\'blocknotice\').style.display=\'\';" name="tp_block_type">
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
							</div>
						</div>
						<div style="padding:1%;"><input type="submit" class="button button_submit" value="' . $txt['tp-send'] . '" /></div>
						<div>
							<div id="blocknotice" class="smallpadding error middletext" style="display: none;">' , $txt['tp-blocknotice'] , '</a></div>
						</div>
						<div class="windowbg2">
							<div>';
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
							<input type="button" value="' , $txt['tp-insert'] , '" name="blockcode_save" onclick="submit();" />
							<input type="checkbox" value="' . $context['TPortal']['blockedit']['id'] . '" name="blockcode_overwrite" /> ' , $txt['tp-blockcodes_overwrite'] , '
						</div>
						</p><br>
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
			elseif($context['TPortal']['blockedit']['type']=='12'){
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$context['TPortal']['blockedit']['body']=10;

				echo $txt['tp-numberofrecenttopics'].'</div>
				      <div><input style="width: 50px;" name="tp_block_body" value="' .$context['TPortal']['blockedit']['body']. '"></div>
					  <div><br>' . $txt['tp-rssblock-showavatar'].'</div>
					  <div>
					<input name="tp_block_var1" type="radio" value="0" ' , $context['TPortal']['blockedit']['var1']=='0' ? ' checked' : '' ,'>'.$txt['tp-no'].'
					<input name="tp_block_var1" type="radio" value="1" ' , ($context['TPortal']['blockedit']['var1']=='1' || $context['TPortal']['blockedit']['var1']=='') ? ' checked' : '' ,'>'.$txt['tp-yes'].'<br>';
			}
			elseif($context['TPortal']['blockedit']['type']=='13'){
				// SSI block..which function?
				if(!in_array($context['TPortal']['blockedit']['body'],array('recentpoll','toppoll','topposters','topboards','topreplies','topviews','calendar')))
					$context['TPortal']['blockedit']['body']='';
						echo '
						</div><div>';
					echo '<input name="tp_block_body" type="radio" value="" ' , $context['TPortal']['blockedit']['body']=='' ? 'checked' : '' , '> ' .$txt['tp-none-'];
					echo '<br><input name="tp_block_body" type="radio" value="topboards" ' , $context['TPortal']['blockedit']['body']=='topboards' ? 'checked' : '' , '> '.$txt['tp-ssi-topboards'];
					echo '<br><input name="tp_block_body" type="radio" value="topposters" ' , $context['TPortal']['blockedit']['body']=='topposters' ? 'checked' : '' , '> '.$txt['tp-ssi-topposters'];
					echo '<br><input name="tp_block_body" type="radio" value="topreplies" ' , $context['TPortal']['blockedit']['body']=='topreplies' ? 'checked' : '' , '> '.$txt['tp-ssi-topreplies'];
					echo '<br><input name="tp_block_body" type="radio" value="topviews" ' , $context['TPortal']['blockedit']['body']=='topviews' ? 'checked' : '' , '> '.$txt['tp-ssi-topviews'];
					echo '<br><input name="tp_block_body" type="radio" value="calendar" ' , $context['TPortal']['blockedit']['body']=='calendar' ? 'checked' : '' , '> '.$txt['tp-ssi-calendar'];
					echo '<br>';
			}
			elseif($context['TPortal']['blockedit']['type']=='20'){
				// TP modules
						echo '
						</div><div>';
				foreach($context['TPortal']['tpmodules']['blockrender'] as $tpm)
					echo '<br><input name="tp_block_var1" type="radio" value="' . $tpm['id'] . '" ' , $context['TPortal']['blockedit']['var1']==$tpm['id'] ? 'checked' : '' , '>'.$tpm['name'];
			}
			elseif($context['TPortal']['blockedit']['type']=='14'){
				// Module block...choose module and module ID , check if module is active
						echo '
						</div><div>';
				echo '<input name="tp_block_body" type="radio" value="dl-stats" ' , $context['TPortal']['blockedit']['body']=='dl-stats' ? 'checked' : '' , '> '.$txt['tp-module1'];
				echo '<br><input name="tp_block_body" type="radio" value="dl-stats2" ' , $context['TPortal']['blockedit']['body']=='dl-stats2' ? 'checked' : '' , '> '.$txt['tp-module2'];
				echo '<br><input name="tp_block_body" type="radio" value="dl-stats3" ' , $context['TPortal']['blockedit']['body']=='dl-stats3' ? 'checked' : '' , '> '.$txt['tp-module3'];
				echo '<br><input name="tp_block_body" type="radio" value="dl-stats4" ' , $context['TPortal']['blockedit']['body']=='dl-stats4' ? 'checked' : '' , '> '.$txt['tp-module4'];
				echo '<br><input name="tp_block_body" type="radio" value="dl-stats5" ' , $context['TPortal']['blockedit']['body']=='dl-stats5' ? 'checked' : '' , '> '.$txt['tp-module5'];
				echo '<br><input name="tp_block_body" type="radio" value="dl-stats6" ' , $context['TPortal']['blockedit']['body']=='dl-stats6' ? 'checked' : '' , '> '.$txt['tp-module6'];
				echo '<br><input name="tp_block_body" type="radio" value="dl-stats7" ' , $context['TPortal']['blockedit']['body']=='dl-stats7' ? 'checked' : '' , '> '.$txt['tp-module7'];
				echo '<br><input name="tp_block_body" type="radio" value="dl-stats8" ' , $context['TPortal']['blockedit']['body']=='dl-stats8' ? 'checked' : '' , '> '.$txt['tp-module8'];
				echo '<br><input name="tp_block_body" type="radio" value="dl-stats9" ' , $context['TPortal']['blockedit']['body']=='dl-stats9' ? 'checked' : '' , '> '.$txt['tp-module9'].'<br>';
			}
			elseif($context['TPortal']['blockedit']['type']=='3'){
				// userbox type
				echo $txt['tp-showuserbox'].'</div><div>';

				if(isset($context['TPortal']['userbox']['avatar']) && $context['TPortal']['userbox']['avatar'])
					echo '<input name="tp_userbox_options0" type="hidden" value="avatar">';
				if(isset($context['TPortal']['userbox']['logged']) && $context['TPortal']['userbox']['logged'])
					echo '<input name="tp_userbox_options1" type="hidden" value="logged">';
				if(isset($context['TPortal']['userbox']['time']) && $context['TPortal']['userbox']['time'])
					echo '<input name="tp_userbox_options2" type="hidden" value="time">';
				if(isset($context['TPortal']['userbox']['unread']) && $context['TPortal']['userbox']['unread'])
					echo '<input name="tp_userbox_options3" type="hidden" value="unread">';

				echo '<input name="tp_userbox_options4" type="checkbox" value="stats" ', (isset($context['TPortal']['userbox']['stats']) && $context['TPortal']['userbox']['stats']) ? 'checked' : '' , '> '.$txt['tp-userbox5'].'<br>';
				echo '<input name="tp_userbox_options5" type="checkbox" value="online" ', (isset($context['TPortal']['userbox']['online']) && $context['TPortal']['userbox']['online']) ? 'checked' : '' , '> '.$txt['tp-userbox6'].'<br>';
				echo '<input name="tp_userbox_options6" type="checkbox" value="stats_all" ', (isset($context['TPortal']['userbox']['stats_all']) && $context['TPortal']['userbox']['stats_all']) ? 'checked' : '' , '> '.$txt['tp-userbox7'].'<br>
					';
			}
			elseif($context['TPortal']['blockedit']['type']=='1'){
				// userbox type
				echo $txt['tp-showuserbox2'].'</div><div>
					<input name="tp_userbox_options0" type="checkbox" value="avatar" ', (isset($context['TPortal']['userbox']['avatar']) && $context['TPortal']['userbox']['avatar']) ? 'checked' : '' , '> '.$txt['tp-userbox1'].'<br>';
				echo '<input name="tp_userbox_options1" type="checkbox" value="logged" ', (isset($context['TPortal']['userbox']['logged']) && $context['TPortal']['userbox']['logged']) ? 'checked' : '' , '> '.$txt['tp-userbox2'].'<br>';
				echo '<input name="tp_userbox_options2" type="checkbox" value="time" ', (isset($context['TPortal']['userbox']['time']) && $context['TPortal']['userbox']['time']) ? 'checked' : '' , '> '.$txt['tp-userbox3'].'<br>';
				echo '<input name="tp_userbox_options3" type="checkbox" value="unread" ', (isset($context['TPortal']['userbox']['unread']) && $context['TPortal']['userbox']['unread']) ? 'checked' : '' , '> '.$txt['tp-userbox4'].'<br>';

				if(isset($context['TPortal']['userbox']['stats']) && $context['TPortal']['userbox']['stats'])
					echo '<input name="tp_userbox_options4" type="hidden" value="stats">';
				if(isset($context['TPortal']['userbox']['online']) && $context['TPortal']['userbox']['online'])
					echo '<input name="tp_userbox_options5" type="hidden" value="online">';
				if(isset($context['TPortal']['userbox']['stats_all']) && $context['TPortal']['userbox']['stats_all'])
					echo '<input name="tp_userbox_options6" type="hidden" value="stats_all">';
			}
			elseif($context['TPortal']['blockedit']['type']=='15'){
						echo $txt['tp-rssblock'] . '
						<div>';
				// RSS feed type
				echo '<input style="width: 95%" name="tp_block_body" value="' .$context['TPortal']['blockedit']['body']. '"><br><br>
						<dl class="settings">
							<dt>' , $txt['tp-rssblock-useutf8'].'
							<dd>
								<input name="tp_block_var1" type="radio" value="1" ' , $context['TPortal']['blockedit']['var1']=='1' ? ' checked' : '' ,'>'.$txt['tp-utf8'].'
								<input name="tp_block_var1" type="radio" value="0" ' , ($context['TPortal']['blockedit']['var1']=='0' || $context['TPortal']['blockedit']['var1']=='') ? ' checked' : '' ,'>'.$txt['tp-iso'].'<br>
							</dd>
							<dt>' . $txt['tp-rssblock-showonlytitle'].'
							</dt>
							<dd>
								<input name="tp_block_var2" type="radio" value="1" ' , $context['TPortal']['blockedit']['var2']=='1' ? ' checked' : '' ,'>'.$txt['tp-yes'].'
								<input name="tp_block_var2" type="radio" value="0" ' , ($context['TPortal']['blockedit']['var2']=='0' || $context['TPortal']['blockedit']['var2']=='') ? ' checked' : '' ,'>'.$txt['tp-no'], '
							</dd>
							<dt>' . $txt['tp-rssblock-maxwidth'].'
							</dt>
							<dd>
								<input name="tp_block_var3" type="text" value="' , $context['TPortal']['blockedit']['var3'],'">
							</dd>
							<dt>' . $txt['tp-rssblock-maxshown'].'
							</dt>
							<dd>
								<input name="tp_block_var4" type="text" value="' , $context['TPortal']['blockedit']['var4'],'">
							</dd>
						</dl>
					</div>
  	                                ';
			}
			elseif($context['TPortal']['blockedit']['type']=='16'){
				echo $txt['tp-sitemapmodules'].'</div><div><ul>';
				if($context['TPortal']['show_download']=='1')
					echo '<li>&nbsp;'.$txt['tp-dldownloads'].'</li>';

				echo '</ul>';
			}
			elseif($context['TPortal']['blockedit']['type']=='18'){
				// check to see if it is numeric
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$lblock['body']='';

				echo $txt['tp-showarticle'],'</div><div>
				<select name="tp_block_body">';
				foreach($context['TPortal']['edit_articles'] as $art => $article ){
					echo '<option value="'.$article['id'].'" ' , $context['TPortal']['blockedit']['body']==$article['id'] ? ' selected="selected"' : '' ,' >'.html_entity_decode($article['subject']).'</option>';
				}
				echo '</select>';
			}
			elseif($context['TPortal']['blockedit']['type']=='7')
			{
				// get the ids
				$myt=array();
				$thems=explode(",",$context['TPortal']['blockedit']['body']);
				foreach($thems as $g => $gh)
				{
					$wh=explode("|",$gh);
					$myt[]=$wh[0];
				}
						echo '
						</div><div>
				<input type="hidden" name="blockbody' .$context['TPortal']['blockedit']['id']. '" value="' .$context['TPortal']['blockedit']['body'] . '" />
				<div  style="padding: 5px;">
					<div style="max-height: 25em; overflow: auto;">
							<input name="tp_theme-1" type="hidden" value="-1">
							<input type="hidden" value="1" name="tp_tpath-1">';
				foreach($context['TPthemes'] as $tema)
				{
					if(strstr($forum_version, '2.1')) {
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
				echo '</div><br><hr /><br><input type="checkbox" onclick="invertAll(this, this.form, \'tp_theme\');" /> '.$txt['tp-checkall'],'
				</div>';
			}
			elseif($context['TPortal']['blockedit']['type']=='19')
			{
				// check to see if it is numeric
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$lblock['body']='';
				if(!is_numeric($context['TPortal']['blockedit']['var1']))
					$lblock['var1']='15';
				if($context['TPortal']['blockedit']['var1']=='0')
					$lblock['var1']='15';

				echo '
					<dl class="settings">
						<dt>'.$txt['tp-showcategory'].'
						</dt>
						<dd>
							<select name="tp_block_body">';
				foreach($context['TPortal']['catnames'] as $cat => $catname){
					echo '<option value="'.$cat.'" ' , $context['TPortal']['blockedit']['body']==$cat ? ' selected' : '' ,' >'.html_entity_decode($catname).'</option>';
				}
				echo '</select>
						</dd>
						<dt>'.$txt['tp-catboxheight'].'
						</dt>
						<dd>
							<input name="tp_block_var1" size="4" type="text" value="' , $context['TPortal']['blockedit']['var1'] ,'"> em
						</dd>
						<dt>'.$txt['tp-catboxauthor'].'
						</dt>
						<dd>
							<input name="tp_block_var2" type="radio" value="1" ' , $context['TPortal']['blockedit']['var2']=='1' ? 'checked' : '' ,'> ', $txt['tp-yes'], '<br>
							<input name="tp_block_var2" type="radio" value="0" ' , $context['TPortal']['blockedit']['var2']=='0' ? 'checked' : '' ,'> ', $txt['tp-no'], '
						</dd>
					</dl>';
			}
			// menubox
			elseif($context['TPortal']['blockedit']['type']=='9')
			{
				// check to see if it is numeric
				if(!is_numeric($context['TPortal']['blockedit']['body']))
					$lblock['body']='0';

				echo $txt['tp-showmenus'],'</div><div>
					<select name="tp_block_body">';
				foreach($context['TPortal']['menus'] as $men){
					echo '
						<option value="'.$men['id'].'" ' , $context['TPortal']['blockedit']['body']==$men['id'] ? ' selected' : '' ,' >'.$men['name'].'</option>';
				}
				echo '
					</select><br>',$txt['tp-showmenustyle'],' <br>
					<input name="tp_block_var1" type="radio" value="0" ' , ($context['TPortal']['blockedit']['var1']=='' || $context['TPortal']['blockedit']['var1']=='0') ? ' checked' : '' ,' > <img src="'.$settings['tp_images_url'].'/TPdivider2.png" alt="" /><br>
					<input name="tp_block_var1" type="radio" value="1" ' , ($context['TPortal']['blockedit']['var1']=='1') ? ' checked' : '' ,' > <img src="'.$settings['tp_images_url'].'/bullet3.png" alt="" /><br>
					<input name="tp_block_var1" type="radio" value="2" ' , ($context['TPortal']['blockedit']['var1']=='2') ? ' checked' : '' ,' > '.$txt['tp-none-'].'<br>
					';
			}
			elseif($context['TPortal']['blockedit']['type']=='6')
			{
				echo $txt['tp-rssblock-showavatar'].'</div><div>
					<input name="tp_block_var1" type="radio" value="0" ' , $context['TPortal']['blockedit']['var1']=='0' ? ' checked' : '' ,'>'.$txt['tp-no'].'
					<input name="tp_block_var1" type="radio" value="1" ' , ($context['TPortal']['blockedit']['var1']=='1' || $context['TPortal']['blockedit']['var1']=='') ? ' checked' : '' ,'>'.$txt['tp-yes'].'<br>';
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
					<div class="smalltext" style="padding: 4px 0;">
						<br><input name="tp_block_var5" type="radio" value="99" ' , $context['TPortal']['blockedit']['var5']=='99' ? 'checked' : '' , '><span' , $context['TPortal']['blockedit']['var5']=='99' ? ' style="color: red;">' : '>' , $txt['tp-blocksusepaneltyle'] , '</span>
					</div>
				<div>
			<div class="panels-optionsbg">';

			if(function_exists('ctheme_tp_getblockstyles'))
				$types = ctheme_tp_getblockstyles();	
			if(strstr($forum_version, '2.1'))
				$types = tp_getblockstyles21();
			else
				$types = tp_getblockstyles();
			foreach($types as $blo => $bl)
				echo '
			<div class="panels-options">
				<div class="smalltext" style="padding: 4px 0;"><input name="tp_block_var5" type="radio" value="'.$blo.'" ' , $context['TPortal']['blockedit']['var5']==$blo ? 'checked' : '' , '><span' , $context['TPortal']['blockedit']['var5']==$blo ? ' style="color: red;">' : '>' , $bl['class'] , '</span>
				</div>' . $bl['code_title_left'] . 'title'. $bl['code_title_right'].'
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
						<input name="tp_block_frame" type="radio" value="theme" ' , $context['TPortal']['blockedit']['frame']=='theme' ? 'checked' : '' , '><label for="field_name"> '.$txt['tp-useframe'].'</label><br>
						<input name="tp_block_frame" type="radio" value="frame" ' , $context['TPortal']['blockedit']['frame']=='frame' ? 'checked' : '' , '><label for="field_name"> '.$txt['tp-useframe2'].' </label><br>
						<input name="tp_block_frame" type="radio" value="title" ' , $context['TPortal']['blockedit']['frame']=='title' ? 'checked' : '' , '><label for="field_name"> '.$txt['tp-usetitle'].' </label></br>
						<input name="tp_block_frame" type="radio" value="none" ' , $context['TPortal']['blockedit']['frame']=='none' ? 'checked' : '' , '><label for="field_name"> '.$txt['tp-noframe'].'</label>
					</dd>
				</dl>
				<br>
				<dl class="settings">
					<dt><label for="field_name"> '.$txt['tp-allowupshrink'].': </label></dt>
					<dd>
						<input name="tp_block_visible" type="radio" value="1" ' , ($context['TPortal']['blockedit']['visible']=='' || $context['TPortal']['blockedit']['visible']=='1') ? 'checked' : '' , '> '.$txt['tp-allowupshrink'].'</label><br>
						<input name="tp_block_visible" type="radio" value="0" ' , ($context['TPortal']['blockedit']['visible']=='0') ? 'checked' : '' , '><label for="field_name"> '.$txt['tp-notallowupshrink'].'</label>
					</dd>
				</dl>
				<br>
				<dl class="settings">
					<dt><label for="field_name"> '.$txt['tp-membergrouphelp'].'</label></dt>
					<dd><div>
						  <div class="tp_largelist">';
			// loop through and set membergroups
			$tg=explode(',',$context['TPortal']['blockedit']['access']);

			if( !empty($context['TPmembergroups']))
			{
				foreach($context['TPmembergroups'] as $mg)
				{
					if($mg['posts']=='-1' && $mg['id']!='1'){
						echo '<input name="tp_group'.$mg['id'].'" type="checkbox" value="'.$context['TPortal']['blockedit']['id'].'"';
						if(in_array($mg['id'],$tg))
							echo ' checked';
						echo '> '.$mg['name'].' <br>';
					}
				}
			}
			// if none is chosen, have a control value
			echo '</div><input type="checkbox" onclick="invertAll(this, this.form, \'tp_group\');" />'.$txt['tp-checkall'].'<br>
						</div>
					</dd>
				</dl>';
			//edit membergroups
			echo '
				<dl class="settings">
					<dt><label for="field_name"> '.$txt['tp-editgrouphelp'].'</label></dt>
					<dd>
						<div>
							<div class="tp_largelist">';
			$tg=explode(',',$context['TPortal']['blockedit']['editgroups']);
			foreach($context['TPmembergroups'] as $mg){
				if($mg['posts']=='-1' && $mg['id']!='1' && $mg['id']!='-1' && $mg['id']!='0'){
					echo '<input name="tp_editgroup'.$mg['id'].'" type="checkbox" value="'.$context['TPortal']['blockedit']['id'].'"';
					if(in_array($mg['id'],$tg))
						echo ' checked';
					echo '> '.$mg['name'].' <br>';
				}
			}
			// if none is chosen, have a control value
			echo '			</div><input type="checkbox" onclick="invertAll(this, this.form, \'tp_editgroup\');" />'.$txt['tp-checkall'];
			echo '<br>
						</div>
					</dd>
				</dl>
				<dl class="settings">
					<dt><label for="field_name">'.$txt['tp-langhelp'].'</label></dt>
					<dd>
						<div>';
			foreach($context['TPortal']['langfiles'] as $langlist => $lang){
				if($lang!=$context['user']['language'] && $lang!='')
					echo '<input name="tp_lang_'.$lang.'" type="text" value="' , !empty($context['TPortal']['blockedit']['langfiles'][$lang]) ? html_entity_decode($context['TPortal']['blockedit']['langfiles'][$lang], ENT_QUOTES) : html_entity_decode($context['TPortal']['blockedit']['title'],ENT_QUOTES) , '"> '. $lang.'<br>';
			}
			echo '		</div>
					</dd>
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
							<div class="padding-div">
				<dl class="settings">
					<dt><label for="field_name"><h4>' . $txt['tp-actions'] . ':</h4></label></dt>
					<dd>
									<div>
											<input name="actiontype1" type="checkbox" value="allpages" ' ,in_array('allpages',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['tp-allpages'].'<br><br>
											<input name="actiontype2" type="checkbox" value="frontpage" ' ,in_array('frontpage',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['tp-frontpage'].'<br>
											<input name="actiontype3" type="checkbox" value="forumall" ' ,in_array('forumall',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['tp-forumall'].'<br>
											<input name="actiontype4" type="checkbox" value="forum" ' ,in_array('forum',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['tp-forumfront'].'<br>
											<input name="actiontype5" type="checkbox" value="recent" ' ,in_array('recent',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['tp-recent'].'<br>
											<input name="actiontype6" type="checkbox" value="unread" ' ,in_array('unread',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['tp-unread'].'<br>
											<input name="actiontype7" type="checkbox" value="unreadreplies" ' ,in_array('unreadreplies',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['tp-unreadreplies'].'<br>
											<input name="actiontype8" type="checkbox" value="profile" ' ,in_array('profile',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['profile'].'<br>
											<input name="actiontype9" type="checkbox" value="pm" ' ,in_array('pm',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['pm_short'].'<br>
											<input name="actiontype10" type="checkbox" value="calendar" ' ,in_array('calendar',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['calendar'].'<br>
											<input name="actiontype11" type="checkbox" value="admin" ' ,in_array('admin',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['admin'].'<br>
											<input name="actiontype12" type="checkbox" value="login" ' ,in_array('login',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['login'].'<br>
											<input name="actiontype13" type="checkbox" value="logout" ' ,in_array('logout',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['logout'].'<br>
											<input name="actiontype14" type="checkbox" value="register" ' ,in_array('register',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['register'].'<br>
											<input name="actiontype15" type="checkbox" value="post" ' ,in_array('post',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['post'].'<br>
											<input name="actiontype16" type="checkbox" value="stats" ' ,in_array('stats',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['tp-stats'].'<br>
											<input name="actiontype17" type="checkbox" value="search" ' ,in_array('search',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['search'].'<br>
											<input name="actiontype18" type="checkbox" value="mlist" ' ,in_array('mlist',$context['TPortal']['blockedit']['access2']['action']) ? 'checked="checked"' : '' , '> '.$txt['tp-memberlist'].'<br><br>';
					// add the custom ones you added
					$count=19;
					foreach($context['TPortal']['blockedit']['access2']['action'] as $po => $p)
					{
						if(!in_array($p, array('allpages','frontpage','forumall','forum','recent','unread','unreadreplies','profile','pm','calendar','admin','login','logout','register','post','stats','mlist')))
						{
							echo '<input name="actiontype'.$count.'" type="checkbox" value="'.$p.'" checked="checked">'.$p.'<br>';
							$count++;
						}
					}
					echo '
							<p>'.$txt['tp-customactions'].'</p>
								<input style="width: 90%;" type="text" name="custotype0" value="">
								</div>
							</dd>
					</dl>
					<dl class="settings">
						<dt><label for="field_name"><h4>Boards:</h4></label></dt>
						<dd>
									  <div class="tp_largelist">';
				$a=1;
				if(!empty($context['TPortal']['boards']))
				{
					echo '<input type="checkbox" name="boardtype' , $a, '" value="-1" ' , in_array('-1', $context['TPortal']['blockedit']['access2']['board']) ? 'checked="checked"' : '' , '> '.$txt['tp-allboards'].'<br><br>';
					$a++;
					foreach($context['TPortal']['boards'] as $bb)
					{
						echo '
											<input type="checkbox" name="boardtype' , $a, '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['access2']['board']) ? 'checked="checked"' : '' , '> '.$bb['name'].'<br>';
						$a++;
					}
				}
				echo '
									  </div>
						</dd>
					</dl>
					<dl class="settings">
						<dt><label for="field_name"><h4>' . $txt['tp-articles'] . ':</h4></label></dt>
						<dd>
							 <div class="tp_largelist">';
				$a=1;
				foreach($context['TPortal']['edit_articles'] as $bb)
				{
					echo '
										<input type="checkbox" name="articletype' , $a , '" value="'.$bb['id'].'" ' ,in_array($bb['id'], $context['TPortal']['blockedit']['access2']['page']) ? 'checked="checked"' : '' , '> '.html_entity_decode($bb['subject'],ENT_QUOTES).'<br>';
					$a++;
				}
				// if none is chosen, have a control value
				echo '</div><input type="checkbox" onclick="invertAll(this, this.form, \'articletype\');" />'.$txt['tp-checkall'];
				echo '<br>
							</div>
						</dd>
					</dl>
					</dl>
					<dl class="settings">
						<dt><label for="field_name"><h4>' . $txt['tp-artcat'] . ':</h4></label></dt>
						<dd>
						    <div class="tp_largelist">';
				$a=1;
				if(isset($context['TPortal']['article_categories']))
				{
					foreach($context['TPortal']['article_categories'] as $bb)
					{
						echo '
											<input type="checkbox" name="categorytype' . $a . '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['access2']['cat']) ? 'checked="checked"' : '' , '> '.$bb['name'].'<br>';
						$a++;
					}
				}
				// if none is chosen, have a control value
				echo '</div><input type="checkbox" onclick="invertAll(this, this.form, \'categorytype\');" />'.$txt['tp-checkall'];
				echo '<br>

						</dd>
					</dl>
					<dl class="settings">
						<dt><label for="field_name"><h4>' . $txt['tp-lang'] . ':</h4></label></dt>
						<dd>';

				// alert if the settings is off, supply link if allowed
				if(empty($context['TPortal']['uselangoption']))
				{
					echo '
					<p class="error">', $txt['tp-uselangoption2'] , ' ' , allowedTo('tp_settings') ? '<a href="'.$scripturl.'?action=tpadmin;sa=settings#uselangoption">['. $txt['tp-settings'] .']</a>' : '' , '</p>';
				}

				$a=1;
				foreach($context['TPortal']['langfiles'] as $bb => $lang)
				{
					echo '
											<input type="checkbox" name="langtype' . $a . '" value="'.$lang.'" ' , in_array($lang, $context['TPortal']['blockedit']['access2']['lang']) ? 'checked="checked"' : '' , '> '.$lang.'<br>';
					$a++;
				}
				echo ' </dd>
					</dl>
					<dl class="settings">
						<dt><label for="field_name"><h4>' . $txt['tp-dlmanager'] . ':</h4></dt>
						<dd><div class="tp_largelist">';
				$a=1;

				if(!empty($context['TPortal']['dlcats']))
				{
					$a++;
					foreach($context['TPortal']['dlcats'] as $bb)
					{
						echo '
								<input type="checkbox" name="dlcattype' , $a, '" value="'.$bb['id'].'" ' , in_array($bb['id'], $context['TPortal']['blockedit']['access2']['dlcat']) ? 'checked="checked"' : '' , '> '.$bb['name'].'<br>';
						$a++;
					}
				}
				// if none is chosen, have a control value
				echo '</div><input type="checkbox" onclick="invertAll(this, this.form, \'dlcattype\');" />'.$txt['tp-checkall'];
				echo '<br>
						</dd>
					</dl>
					<dl class="settings">
						<dt><label for="field_name"><h4>'.$txt['tp-modules'].'</h4><label></dt>
						<dd>
							<div>';
				$a=1;

				if(!empty($context['TPortal']['tpmods']))
				{
					$a++;
					foreach($context['TPortal']['tpmods'] as $bb)
					{
						echo '
								<input type="checkbox" name="tpmodtype' , $a, '" value="'.$bb['subquery'].'" ' , in_array($bb['subquery'], $context['TPortal']['blockedit']['access2']['tpmod']) ? 'checked="checked"' : '' , '> '.$bb['title'].'<br>';
						$a++;
					}
				}


				echo '
							</div>
						</dd>
					</dl>
				</div>
			</div>
		</fieldset>
	';
		}
			echo '
					<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
				</div>
			</div>
	</form>';
}


	// all the blocks
function template_latestblocks()
{
	tp_latestblockcodes();
}

// all the blocks
function template_blocks()
{
	global $context, $settings, $txt, $scripturl;

	echo '
	<form accept-charset="', $context['character_set'], '" name="tpadmin_news" action="' . $scripturl . '?action=tpadmin" method="post">
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
		<input name="tpadmin_form" type="hidden" value="blocks">
		<div class="cat_bar"><h3 class="catbg">' . $txt['tp-blocksettings'] . '</h3></div>
		<div id="all-the-blocks" class="admintable admin-area">
			<div class="windowbg noup">';

		$side=array('left','right','top','center','front','lower','bottom');
		$sd=array('lb','rb','tb','cb','fb','lob','bb');

		for($i=0 ; $i<7 ; $i++)
		{
			echo '
					<div class="title_bar"><h3 class="titlebg">
						<b>'.$txt['tp-'.$side[$i].'sideblocks'].'</b>
						<a href="'.$scripturl.'?action=tpadmin;addblock=' . $side[$i] . ';' . $context['session_var'] . '=' . $context['session_id'].'">
								<span style="float: right;">[' , $txt['tp-addblock'] , ']</span>
						</a>
					</h3></div>
					';
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
					<div class="titlebg2">
						<div style="width:7%;" class="smalltext pos float-items">'.$txt['tp-pos'].'</div>
						<div style="width:20%;" class="smalltext name float-items">'.$txt['tp-title'].'</div>
						<div style="width:18%;" class="smalltext title-admin-area float-items" >'.$txt['tp-type'].'</div>
 						<div style="width:7%;" class="smalltext title-admin-area float-items" align="center">'.$txt['tp-activate'].'</div>
						<div style="width:14%;" class="smalltext title-admin-area float-items" align="center">'.$txt['tp-move'].'</div>
						<div style="width:10%;" class="smalltext title-admin-area float-items" align="center">'.$txt['tp-editsave'].'</div>
						<div style="width:7%;" class="smalltext title-admin-area float-items" align="center">'.$txt['tp-delete'].'</div>
						<p class="clearthefloat"></p>
					</div>';

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
						if($lblock['off']==0)
							$class='windowbg2';
						else
							$class='windowbg';
					}
					echo '
					<div id="blocksDiv" class="',$class,'">
						<div style="width:7%;" class="adm-pos float-items">', ($lblock['editgroups']!='' && $lblock['editgroups']!='-2') ? '#' : '' ,'
							<input name="pos' .$lblock['id']. '" type="text" size="1em" maxlength="3" value="' .($n*10). '">
							<a name="block' .$lblock['id']. '"></a><br>';
					echo '
						    <a class="tpbut" title="'.$txt['tp-sortdown'].'" href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';addpos=' .$lblock['id']. '"><img src="' .$settings['tp_images_url']. '/TPsort_down.png" value="' .(($n*10)+11). '" /></a>';

					if($n>0)
						echo '
						    <a class="tpbut" title="'.$txt['tp-sortup'].'"  href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';subpos=' .$lblock['id']. '"><img src="' .$settings['tp_images_url']. '/TPsort_up.png" value="' .(($n*10)-11). '" /></a>';

					echo '
						</div>
						<div style="width:20%;max-width:100%;" class="adm-name float-items">
						     <input name="title' .$lblock['id']. '" type="text" size="15" value="' .html_entity_decode($newtitle). '">
						</div>
						<div style="width:20%;" class="fullwidth-on-res-layout block-opt float-items">
						    <div id="show-on-respnsive-layout">'.$txt['tp-type'].'</div>
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
					echo '</select>
						</div>
						<div style="width:6%;" align="center" class="fullwidth-on-res-layout float-items">
						    <div id="show-on-respnsive-layout">'.$txt['tp-activate'].'</div>
							&nbsp;<a name="'.$lblock['id'].'"></a>
						    <img class="toggleButton" id="blockonbutton' .$lblock['id']. '" title="'.$txt['tp-activate'].'" border="0" src="' .$settings['tp_images_url']. '/TP' , $lblock['off']=='0' ? 'active2' : 'active1' , '.png" alt="'.$txt['tp-activate'].'"  />';
				echo '
						</div>
						<div  style="width:15%;" align="center" class="fullwidth-on-res-layout float-items">
						  <div id="show-on-respnsive-layout">'.$txt['tp-move'].'</div>';

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
						<div  style="width:8%;" align="center" class="fullwidth-on-res-layout float-items">
						    <div id="show-on-respnsive-layout">'.$txt['tp-editsave'].'</div>
							<a href="' . $scripturl . '?action=tpadmin;blockedit=' .$lblock['id']. ';' . $context['session_var'] . '=' . $context['session_id'].'"><img title="'.$txt['tp-edit'].'" border="0" src="' .$settings['tp_images_url']. '/TPmodify.png" alt="'.$txt['tp-edit'].'"  /></a>';

				echo '
							<input class="tpbut" style="height:16px; vertical-align:top;" type="image" src="' .$settings['tp_images_url']. '/TPsave.png" alt="'.$txt['tp-send'].'" value="�" onClick="javascript: submit();">';
						echo '
						</div>
	                    <div style="width:6%;" align="center" class="fullwidth-on-res-layout float-items">
						    <div id="show-on-respnsive-layout">'.$txt['tp-delete'].'</div>
							<a href="' . $scripturl . '?action=tpadmin;' . $context['session_var'] . '=' . $context['session_id'].';blockdelete=' .$lblock['id']. '" onclick="javascript:return confirm(\''.$txt['tp-blockconfirmdelete'].'\')"><img title="'.$txt['tp-delete'].'"  border="0" src="' .$settings['tp_images_url']. '/tp-delete_shout.png" alt="'.$txt['tp-delete'].'"  /></a>
						</div>
						<p class="clearthefloat"></p>
					</div>';
					if($lblock['type']=='recentbox'){
						// check to see if it is numeric
						if(!is_numeric($lblock['body']))
							$lblock['body']='10';
						echo '
			     	<div class="windowbg">
						<div align="center" style="padding:1%;">
							'.$txt['tp-numberofrecenttopics'].'<input size=4 name="blockbody' .$lblock['id']. '" value="' .$lblock['body']. '">
						</div>
					</div>';
					}
					elseif($lblock['type']=='ssi'){
						// SSI block..which function?
						if(!in_array($lblock['body'],array('recentpoll','toppoll','topposters','topboards','topreplies','topviews','calendar')))
							$lblock['body']='';
						echo '
					<div class="windowbg">
						<div align="center" style="padding:1%;">
							<select name="blockbody' .$lblock['id']. '"><option value="" ' , $lblock['body']=='' ? 'selected' : '' , '>' .$txt['tp-none-'].'</option>';
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
					</div>';
					}
					elseif($lblock['type']=='rss'){
						echo '
					<div class="windowbg">
						<div align="center" style="padding:1%;">
							'.$txt['tp-rssblock'].'<input style="width: 75%;" name="blockbody' .$lblock['id']. '" value="' .$lblock['body']. '">
						</div>
					</div>';
					}
					elseif($lblock['type']=='module'){
						echo '
								<div class="windowbg">
									<div align="center" style="padding:1%;">
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
								</div>';
					}
					elseif($lblock['type']=='articlebox'){
						// check to see if it is numeric
						if(!is_numeric($lblock['body']))
							$lblock['body']='';
						echo '
								<div class="windowbg">
									<div align="center" style="padding:1%;">
										<select name="blockbody' .$lblock['id']. '">';
						foreach($context['TPortal']['edit_articles'] as $article){
							echo '
											<option value="'.$article['id'].'" ' ,$lblock['body']==$article['id'] ? ' selected' : '' ,' >'. html_entity_decode($article['subject'],ENT_QUOTES).'</option>';
						}
						echo '
										</select>
									</div>
								</div>';
					}
					elseif($lblock['type']=='categorybox'){
						// check to see if it is numeric
						if(!is_numeric($lblock['body']))
							$lblock['body']='';

						echo '
								<div class="windowbg">
									<div align="center" style="padding:1%;">
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
								</div>';
					}
					$n++;
				}
			}

		}
		echo '

			<div style="padding:1%;"><input type="submit" class="button button_submit" value="'.$txt['tp-send'].'" name="'.$txt['tp-send'].'"></div>
			</div>
		</div>
	</form>';

}

?>
