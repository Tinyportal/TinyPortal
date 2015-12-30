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

function template_tp_above()
{
	global $context;

	if(!empty($context['TPortal']['upshrinkpanel']))
		echo '
	<div style="float: right; margin-right: 0.5em; margin-top: -1.5em;">', $context['TPortal']['upshrinkpanel'] , '</div>';

	if($context['TPortal']['toppanel']==1)
		echo '
	<div id="tptopbarHeader" style="' , in_array('tptopbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , 'clear: both;">
	'	, TPortal_panel('top') , '
	</div>';	

	echo '
	<div class="tp_main">';

	// TinyPortal integrated bars
	if($context['TPortal']['leftpanel']==1)
	{
		echo '
		<div class="HiddenUppercontainer"', $context['TPortal']['leftpanel']==1 ? ' style="width:' .$context['TPortal']['rightbar_width']. 'px;"' : '', '>
			<div id="tpleftbarContainer" style="width:' , ($context['TPortal']['leftbar_width']) , 'px;' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? ' display: none;' : '' , '">
				<div id="tpleftbarHeader" style="' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , $context['TPortal']['useroundframepanels']==1 ?
				'<span class="upperframe"><span></span></span>
				<div class="roundframe" style="overflow: auto;">' : '
					', TPortal_panel('left') , 
					$context['TPortal']['useroundframepanels']==1 ?
				'</div>
				<span class="lowerframe"><span></span></span>' : '' , '
				</div>
			</div>
		</div>';

	}
	echo '		
			<div class="tpcenterbarContainer">
				<div id="tpcontentHeader">';
  
	if($context['TPortal']['centerpanel']==1)
		echo '
					<div id="tpcenterbarHeader" style="' , in_array('tpcenterbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
						' , TPortal_panel('center') , '
					</div>';

	echo '
				</div>';
}

function template_tp_below()
{
	global $context;

	if($context['TPortal']['lowerpanel']==1)
		echo '
				<div id="tplowerbarHeader" style="' , in_array('tplowerbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
					' , TPortal_panel('lower') , '
				</div>';	

	echo '
			</div>
			<div class="HiddenLowercontainer"', $context['TPortal']['rightpanel']==1 ? ' style="width:' .$context['TPortal']['rightbar_width']. 'px;"' : '', '>';


		// TinyPortal integrated bars
		if($context['TPortal']['leftpanel']==1)
		{
			echo '
				<div class="tpleftbottomContainer tpContent-show', $context['TPortal']['rightpanel']!=1 ? ' tpfull-width' : '', '" id="tpbottomleftbarContainer" style="width:' , ($context['TPortal']['leftbar_width']) , 'px; ' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
					<div id="tpbottomleftbarHeader" style="' , in_array('tpleftbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
					' , $context['TPortal']['useroundframepanels']==1 ?
					'<span class="upperframe"><span></span></span>
					<div class="roundframe" style="overflow: auto;">' : '
						', TPortal_panel('left') , 
						$context['TPortal']['useroundframepanels']==1 ?
					'</div>
					<span class="lowerframe"><span></span></span>' : '' , '
					</div>
				</div>';

		}

		// TinyPortal integrated bars
		if($context['TPortal']['rightpanel']==1)
		{
			echo '
				<div class="tprightbottomContainer ', $context['TPortal']['leftpanel']!=1 ? ' tpfull-width' : '', '"" id="tprightbarContainer" style="width:' ,$context['TPortal']['rightbar_width'], 'px;' , in_array('tprightbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? ' display: none;' : '' , '">
					<div id="tprightbarHeader" style="' , in_array('tprightbarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
					' , $context['TPortal']['useroundframepanels']==1 ?
					'<span class="upperframe"><span></span></span>
					<div class="roundframe">' : ''
					, TPortal_panel('right') , 
					$context['TPortal']['useroundframepanels']==1 ?
					'</div>
					<span class="lowerframe"><span></span></span>' : '' , '
					</div>
				</div>';

		}
	
	echo '
		</div>
	</div>';

	if($context['TPortal']['bottompanel']==1)
		echo '
		<div id="tpbottombarHeader" style="clear: both;' , in_array('tpbottombarHeader',$context['tp_panels']) && $context['TPortal']['showcollapse']==1 ? 'display: none;' : '' , '">
				' , TPortal_panel('bottom') , '
		</div>';	
	
}

?>
