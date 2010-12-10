<?php
// Version: 1.0 beta5; TPhelp
// For use with SMF v2.0 RC3

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	echo '
	<span class="upperframe"><span></span></span>
	<div class="roundframe">
		<h3 class="titlebg"><span class="left"></span>' . $txt['tphelp_' . $context['TPortal']['helpsection']] . '</h3>
		<div style="padding: 1em;">';

         // main tp help
		echo $txt['tphelp_'. $context['TPortal']['helpsection'] . '_main'];

		echo '
		</div>
	</div>
	<span class="lowerframe"><span></span></span>';
}

?>