<?php
/**
 * @package TinyPortal
 * @version 1.0 RC4
 * @author IchBin - ichbin@ichbin.us
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2012 - The TinyPortal Team
 *
 */

function template_main()
{
	global $context;

	if ($context['TPortal']['subaction'] == 'credits')
		template_tpcredits();
	else
		template_tphelp();
}

function template_tphelp()
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

function template_tpcredits()
{
	global $context, $txt;

	echo '
	<table class="admintable">
		<caption class="catbg">' . $txt['tp-credits'] . '</caption>
		<thead>
			<tr>
				<th class="windowbg3 smalltext">' , $txt['tp-creditack2']  , '</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="windowbg2" id="credits">
					'.$txt['tp-credit1'].'
				</td>
			</tr>
		</tbody>
	</table>';
}
?>