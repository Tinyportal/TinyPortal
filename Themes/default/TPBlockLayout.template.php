<?php
/**
 * @package TinyPortal
 * @version 1.4R
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




function template_tp_above()
{
	global $context, $settings;	
	
// body responsive classes
$respClass = ''; 
if (isset($context['TPortal'])) {
$tm2 = '';
$tm2=explode(",",$context['TPortal']['resp']);			
if (in_array($settings['theme_id'],$tm2)) {
	$respClass = "tp_nonresponsive";
	echo '<style>
/** NON REPONSIVE THEMES **/
/** screen smaller then 900px **/
@media all and (min-width: 0px) and (max-width: 900px) {
body {
	min-width:900px!important;
}
}		
	</style>';
} else {$respClass = "tp_responsive";}	
}
// sidebars classses
$sideclass = '';
if (isset($context['TPortal']) && ($context['TPortal']['leftpanel']==0 && $context['TPortal']['rightpanel']==1)) {
	$sideclass =  "lrs rightpanelOn";
} elseif (isset($context['TPortal']) && ($context['TPortal']['leftpanel']==1 && $context['TPortal']['rightpanel']==0)) {
	$sideclass =  "lrs leftpanelOn";
} elseif (isset($context['TPortal']) && ($context['TPortal']['leftpanel']==1 && $context['TPortal']['rightpanel']==1)) {
	$sideclass =  "lrs lrON";
} elseif (isset($context['TPortal']) && ($context['TPortal']['leftpanel']==0 && $context['TPortal']['rightpanel']==0)) {
	$sideclass =  "nosides";
} else {$bclass =  "nosides";} 


echo '<div class="'. $sideclass .' '. $respClass .'">';		
	if(!empty($context['TPortal']['upshrinkpanel']))
		echo '
	<div style="float: right; margin-right: 0.5em; margin-top: -1.5em;">', $context['TPortal']['upshrinkpanel'] , '</div>';

	if($context['TPortal']['toppanel']==1)
		echo '
	<div id="tptopbarHeader" style="' , in_array('tptopbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , 'clear: both;">
	'	, TPortal_panel('top') , '
	<p class="clearthefloat"></p></div>';	

	echo '<div id="mainContainer" style="clear: both;">';

	// TinyPortal integrated bars
	if($context['TPortal']['leftpanel']==1)
	{
		echo '
			<div id="tpleftbarContainer" style="width:' , ($context['TPortal']['leftbar_width']) , 'px; ' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '" >
				<div id="tpleftbarHeader" style="' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , $context['TPortal']['useroundframepanels']==1 ?
				'<span class="upperframe"><span></span></span>
				<div class="roundframe" style="overflow: auto;">' : ''
				, TPortal_panel('left') , 
				$context['TPortal']['useroundframepanels']==1 ?
				'</div>
				<span class="lowerframe"><span></span></span>' : '' , '
				<p class="clearthefloat"></p></div>
			</div>';

	}
	// TinyPortal integrated bars
	if($context['TPortal']['rightpanel']==1)
	{
		echo '
			<div id="tprightbarContainer" style="width:' ,$context['TPortal']['rightbar_width'], 'px;' , in_array('tprightbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '" >
				<div id="tprightbarHeader" style="' , in_array('tprightbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , $context['TPortal']['useroundframepanels']==1 ?
				'<span class="upperframe"><span></span></span>
				<div class="roundframe">' : ''
				, TPortal_panel('right') , 
				$context['TPortal']['useroundframepanels']==1 ?
				'</div>
				<span class="lowerframe"><span></span></span>' : '' , '
				<p class="clearthefloat"></p></div>
			</div>';

	}	
	echo '		
		<div id="centerContainer">
			<div id="tpcontentHeader">';
  
	if($context['TPortal']['centerpanel']==1)
		echo '
				<div id="tpcenterbarHeader" style="' , in_array('tpcenterbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('center') , '
				<p class="clearthefloat"></p></div>';

	echo '
			</div>';
}

function template_tp_below()
{
	global $context;
	global $image_proxy_enabled, $image_proxy_secret, $boardurl;

	if($context['TPortal']['lowerpanel']==1)
		echo '
				<div id="tplowerbarHeader" style="' , in_array('tplowerbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('lower') , '<p class="clearthefloat"></p></div>';	
// end centerContainer
	echo '</div>';
// end mainContainer	
	echo '<p class="clearthefloat" style="padding:0px;margin:0px;"></p>
	</div>';

	if($context['TPortal']['bottompanel']==1)
		echo '
		<div id="tpbottombarHeader" style="clear: both;' , in_array('tpbottombarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('bottom') , '
		<p class="clearthefloat"></p></div>';	
	echo '</div>';	

	if ($image_proxy_enabled && ( $context['TPortal']['imageproxycheck'] > 0 ) ) {
		$post = ob_get_contents();

		ob_clean();

		if (!empty($post) && stripos($post, 'http://') !== false)
		{
			$post = preg_replace_callback("~<img([\w\W]+?)/>~",
				function( $matches ) use ( $boardurl, $image_proxy_secret ) {
					if (stripos($matches[0], 'http://') !== false) {
						$matches[0] = preg_replace_callback("~src\=(?:\"|\')(.+?)(?:\"|\')~",
							function( $src ) use ( $boardurl, $image_proxy_secret ) {
								if (stripos($src[1], 'http://') !== false)
									return ' src="'. $boardurl . '/proxy.php?request='.urlencode($src[1]).'&hash=' . md5($src[1] . $image_proxy_secret) .'"';
								else
									return $src[0];
							},
							$matches[0]);
					}
					return $matches[0];
				},
				$post);
		}
		echo $post;
	}
}

?>
