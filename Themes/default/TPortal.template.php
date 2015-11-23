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

	tp_renderarticle($context['TPortal']['article']);
}

// the frontpage template
function template_frontpage()
{
	global $context;

	if($context['TPortal']['frontblock_type'] == 'first' || $context['TPortal']['front_type'] == 'frontblock')
		echo '<div id="tpfrontpanel_top" style="margin: 0 0 4px 0; padding: 0;">', TPortal_panel('front'), '</div>';
	
	if(!isset($context['TPortal']['category']))
	{
		// check the frontblocks first
		if($context['TPortal']['frontblock_type'] == 'last' && $context['TPortal']['front_type'] != 'frontblock')
			echo '<div id="tpfrontpanel_bottom">', TPortal_panel('front'), '</div>';

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
		TPortal_panel('front');

}

// This is the template for single article
function template_article($article, $single = false)
{
	global $context;

	if(isset($context['tportal']['article_expired']))
		template_notpublished();
	
	render_template(article_renders((!empty($article['category_opts']['catlayout']) ? $article['category_opts']['catlayout'] : 1) , true, true));
}

// the templates for article categories
function template_category()
{
	global $context, $scripturl;

	if(!empty($context['TPortal']['clist']))
	{
		$buts = array();
		foreach($context['TPortal']['clist'] as $cats)
		{
			$buts[$cats['id']] = array(
				'text' => 'catlist'. $cats['id'], 
				'image' => 'blank.gif', 
				'lang' => false, 
				'url' => $scripturl . '?cat=' . $cats['id'],
				'active' => false,
			);
			if($cats['selected'])
				$buts[$cats['id']]['active'] = true;

		}
		echo '<div style="overflow: hidden;">' , tp_template_button_strip($buts, 'top'), '</div>';
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