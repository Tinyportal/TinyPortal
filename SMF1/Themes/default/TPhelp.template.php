<?php
// Version: TinyPortal 1.0; TPhelp
// For use with SMF v1.1.x

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

		echo '
	<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 10px;">
		<tr>
			<td class="mirrortab_first">&nbsp;</td>
			<td valign="top" class="mirrortab_back">
				<a href="', $scripturl, '?action=help">' , $txt['tp-smfhelp'] , '</a>
			</td>
			<td class="mirrortab_active_first">&nbsp;</td>
			<td valign="top" class="mirrortab_active_back">
				<a href="', $scripturl, '?action=tpmod;sa=help">' , $txt['tp-tphelp'] , '</a>
			</td>
			<td class="mirrortab_active_last">&nbsp;</td>
			<td class="mirrortab_last">&nbsp;</td>
	     </tr>
	</table>';

	echo '
	<div class="tborder">
		<div class="titlebg" style="padding: 4px;">';
         // the menu of help
		foreach ($context['TPortal']['helptabs'] as $tab => $value)
		{
			if ($value == $context['TPortal']['helpsection'])
				$tab_items[] = '<span class="error" style="font-weight: bold;">' . $txt['tphelp_' . $value] . '</span>';
			else
				$tab_items[] = '<a href="' . $scripturl . '?action=tpmod;sa=help;p=' . $value . '">' . $txt['tphelp_' . $value] . '</a>';
		}
		echo implode(' &bull; ', $tab_items);

		echo '
		</div>
		<div style="padding: 2ex;" class="windowbg2">';

         // main tp help
		echo $txt['tphelp_'. $context['TPortal']['helpsection'] . '_main'];

		echo '
		</div>
	</div>';
}

?>