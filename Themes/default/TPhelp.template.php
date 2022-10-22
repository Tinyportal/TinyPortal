<?php
/**
 * @package TinyPortal
 * @version 2.2.3
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) - The TinyPortal Team
 *
 */

function template_main()
{
	global $context;

	if ($context['TPortal']['subaction'] == 'credits')
		template_tpcredits();
}

// Credits Page
function template_tpcredits()
{
	global $txt, $context;

	echo '
	<div class="tborder">
		<div class="cat_bar">
			<h3 class="catbg">' . $txt['tp-credits'] . ' (v' . $context['TPortal']['version'] . ')</h3>
		</div><div></div>
		<p class="information">' , $txt['tp-creditack2']  , '</p>
		<div class="windowbg">
			<span class="topslice"><span></span></span>
			<div class="content">
				'.$txt['tp-credit1'].'
			</div>
			<span class="botslice"><span></span></span>
		</div>
	</div>';
}

?>
