<?php
/**
 * @package TinyPortal
 * @version 3.0.5
 * @author tinoest - https://www.tinyportal.net
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
 
function template_tp_list_images_admin()
{
	global $txt, $context, $scripturl;

	$escape = static function ($value) {
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	};
	$action = $escape($scripturl . '?action=tpadmin;sa=listimage');
	$session = '<input type="hidden" name="' . $escape($context['session_var']) . '" value="' . $escape($context['session_id']) . '" />';
	$member = $context['TPortal']['image_member'];

	echo '
		<form class="tborder" action="', $action, ';list" method="post">
			<div class="cat_bar"><h3 class="catbg">', $txt['tp-listimage'], '</h3></div>
			<p class="information">', $txt['tp-listimage-intro'], '</p>
			<div class="cat_bar"><h3 class="catbg">', $txt['tp-listimage-settings'], '</h3></div>
			<div id="tplistimages">
				<div class="windowbg noup">
					<div class="padding-div">
						', $session, '
						<select name="id_member" aria-label="', $escape($txt['tp-listimage-username']), '">';

	foreach ($context['TPortal']['image_members'] as $id => $name) {
		echo '<option value="', $escape($id), '"', (int) $id === $member ? ' selected' : '', '>', $escape($name), '</option>';
	}

	echo '
						</select>
						<button type="submit">', $txt['tp-listimage-list'], '</button>
					</div>
				</div>
			</div>
		</form>
		<div class="tp_liimwrapper">';

	foreach ($context['TPortal']['member_images'] as $image => $url) {
		$image = $escape($image);
		echo '
			<form class="tborder" action="', $action, ';remove" method="post" data-confirm="', $escape($txt['tp-listimage-confirm-remove']), '" onsubmit="return window.confirm(this.dataset.confirm)">
				<div>
					', $session, '
					<input type="hidden" name="id_member" value="', $member, '" />
					<input type="hidden" name="image" value="', $image, '" />
					<div class="tp_liimtile"><img src="', $escape($url), '" alt="', $image, '" title="', $image, '" /><br>', $image, '</div>
					<div class="tpcenter"><button type="submit">', $txt['tp-listimage-remove'], '</button></div>
				</div>
			</form>';
	}

	echo '
			<div class="padding-div"></div>
		</div>';
}
