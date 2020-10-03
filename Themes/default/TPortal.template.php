<?php
/**
 * @package TinyPortal
 * @version 2.1.0
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

// ** Sections **
// Frontpage template
// Single article template
// Article categories template


function template_main()
{
	global $context;

	$context['TPortal']['single_article'] = false;

	// is a article defined?
	if(isset($context['TPortal']['article']['id']))
	{
		// switch to indicate single article
		$context['TPortal']['single_article'] = true;

		// if nolayer, swith to that subtemplat
		if(!empty($context['TPortal']['article']['visual_options']['nolayer']))
			template_nolayer();
		else
			template_article($context['TPortal']['article']);
	}
	// its a category?
	elseif(isset($context['TPortal']['category']['id']))
		template_category();
	// nope, so frontpage then
	else
		template_frontpage();
}

function template_main_nolayer()
{
	global $context;

	echo tp_renderarticle($context['TPortal']['article']);
}

// Frontpage template
function template_frontpage()
{
	global $context;

	if($context['TPortal']['frontblock_type'] == 'first' || $context['TPortal']['front_type'] == 'frontblock')
		echo '<div id="tpfrontpanel_top">', TPortal_panel('front'), '<p class="clearthefloat"></p></div>';

	if(!isset($context['TPortal']['category']))
	{
		// check the frontblocks first
		if($context['TPortal']['frontblock_type'] == 'last' && $context['TPortal']['front_type'] != 'frontblock')
			echo '<div id="tpfrontpanel_bottom">', TPortal_panel('front'), '<p class="clearthefloat"></p></div>';

		return;
	}

	$front = $context['TPortal']['category'];

	// get the grids
	$grid = tp_grids();

	// any pageindex?
	if(!empty($context['TPortal']['pageindex']))
		echo '
	<div class="tp_pageindex_upper">' , $context['TPortal']['pageindex'] , '</div>';

	// use a customised template or the built-in?
	render_template_layout($grid[(!empty($front['options']['layout']) ? $front['options']['layout'] : $context['TPortal']['frontpage_layout'])]['code'], 'category_');

	// any pageindex?
	if(!empty($context['TPortal']['pageindex']))
		echo '
	<div class="tp_pageindex_lower">' , $context['TPortal']['pageindex'] , '</div>';

	if($context['TPortal']['frontblock_type'] == 'last' && $context['TPortal']['front_type'] != 'frontblock')
		echo '<div id="tpfrontpanel_bottom">', TPortal_panel('front'), '<p class="clearthefloat"></p></div>';
}

// Single article template
function template_article($article, $single = false)
{
	global $context;

	if(isset($context['tportal']['article_expired']))
		template_notpublished();

	render_template(article_renders((!empty($article['category_opts']['catlayout']) ? $article['category_opts']['catlayout'] : 1) , true, true));
}

// Article categories template
function template_category()
{
	global $txt, $context, $scripturl;

	if(!empty($context['TPortal']['clist'])) {
		$buts = array();
		foreach($context['TPortal']['clist'] as $cats) {
			$buts[$cats['id']] = array(
				'text' => 'catlist'. $cats['id'],
				'lang' => false,
				'url' => $scripturl . '?cat=' . $cats['id'],
				'active' => false,
			);
			if($cats['selected']) {
				$buts[$cats['id']]['active'] = true;
            }
		}
		echo '<div style="overflow: hidden;">' , tp_template_button_strip($buts, 'top'), '</div>';
	}
    elseif(isset($context['TPortal']['category']['no_articles']) && $context['TPortal']['category']['no_articles'] == true) {
		fatal_error($txt['tp-categorynoarticles'], false);
    }

	$category = $context['TPortal']['category'];

	// get the grids
	$grid = tp_grids();

	// fallback
	if(!isset($category['options']['layout']))
		$category['options']['layout']=1;

	// any pageindex?
	if(!empty($context['TPortal']['pageindex']))
		echo '
	<div class="tp_pageindex_upper">' , $context['TPortal']['pageindex'] , '</div>';

	// any child categories?
	if(!empty($context['TPortal']['category']['children']))
		category_childs();

	render_template_layout($grid[$category['options']['layout']]['code'], 'category_');

	// any pageindex?
	if(!empty($context['TPortal']['pageindex']))
		echo '
	<div class="tp_pageindex_lower">' , $context['TPortal']['pageindex'] , '</div>';
}

?>
