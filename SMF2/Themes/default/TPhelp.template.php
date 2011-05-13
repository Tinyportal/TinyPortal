<?php
// Version: TinyPortal 1.0; TPhelp
// For use with SMF v2.0

function template_main()
{
	global $context;

	if ($context['TPortal']['subaction'] == 'credits')
		template_tpcredits();
	else
		template_tphelp();
}

function template_tpcredits()
{
	global $context, $txt, $scripturl;
	
	echo '
	<div class="tborder">
		<div class="cat_bar">
			<h3 class="catbg">' . $txt['tp-credits'] . '</h3>
		</div>
		<p class="description">' , $txt['tp-creditack2']  , '</p>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
			<div class="content" style="line-height: 1.6em; padding: 0 1em;">
							'.$txt['tp-credit1'].'
			</div>
			<span class="botslice"><span></span></span>
		</div>
	</div>';
}

function template_tphelp()
{
	global $context, $txt;
	
	echo '
	<span class="upperframe"><span></span></span>
	<div class="roundframe">
		<div class="title_bar">
			<h3 class="titlebg"><span class="left"></span>' . $txt['tphelp_' . $context['TPortal']['helpsection']] . '</h3>
		</div>
		<div style="padding: 1em;">';

         // main tp help
		echo $txt['tphelp_'. $context['TPortal']['helpsection'] . '_main'];

		echo '
		</div>
	</div>
	<span class="lowerframe"><span></span></span>';
}

?>