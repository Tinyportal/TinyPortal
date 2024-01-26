<?php
/**
 * @package TinyPortal
 * @version 3.0.1
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
use \TinyPortal\Mentions as TPMentions;
use \TinyPortal\Block as TPBlock;
use \TinyPortal\Shout as TPShout;
use \TinyPortal\Util as TPUtil;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

function TPShout() {{{

    global $context, $settings, $options, $modSettings;

	$block_id = TPUtil::filter('b', 'request', 'int') ?? null;

    if(isset($_REQUEST['shout'])) {
        $shoutAction = TPUtil::filter('shout', 'request', 'string');
        if($shoutAction == 'admin') {
            TPShoutAdmin();
        }
        elseif($shoutAction == 'del') {
			$shoutbox_del			= TPUtil::filter('s', 'request', 'int') ?? null;
            TPShoutDelete( $shoutbox_del );
            tpshout_bigscreen(false, $context['TPortal']['shoutbox_limit'], $block_id);
        }
        elseif($shoutAction == 'save') {
            if (empty($context['TPortal']['shout_allow_links']) && shoutHasLinks() == true) {
                    return;
            }
            TPShoutPost();
            tpshout_bigscreen(false, $context['TPortal']['shoutbox_limit'], $block_id);
        }
        elseif($shoutAction == 'refresh') {
            var_dump(TPShoutFetch( $block_id, false, $context['TPortal']['shoutbox_limit'], true));
            die;
        }
        elseif($shoutAction == 'fetch') {
            tpshout_bigscreen(false, $context['TPortal']['shoutbox_limit'], $block_id);
        }
        else {
			isAllowedTo('tp_can_shout');
            $number = substr($shoutAction, 4);
            if(!is_numeric($number)) {
                $number = 10;
            }
            tpshout_bigscreen(true, $number, $block_id);
        }
    }

    return true;

}}}

function TPShoutLoad() {{{

    global $context, $settings, $options, $modSettings;

    if(loadLanguage('TPortal') == false) {
        loadLanguage('TPortal', 'english');
    }

    loadCSSFile('jquery.sceditor.css');

    // if in admin screen, turn off blocks
    if($context['TPortal']['action'] == 'tpshout' && isset($_GET['shout']) && substr($_GET['shout'], 0, 5) == 'admin') {
        $in_admin = true;
    }

    if($context['TPortal']['hidebars_admin_only'] =='1' && isset($in_admin)) {
        tp_hidebars();
    }

    // bbc code for shoutbox
    $context['html_headers'] .= '
        <script type="text/javascript"><!-- // --><![CDATA[
            var tp_images_url = "' .$settings['tp_images_url'] . '";
            var tp_session_id = "' . $context['session_id'] . '";
            var tp_session_var = "' . $context['session_var'] . '";
            var tp_shout_key_press = false;
            var current_header_smiley = ';

    if(empty($options['expand_header_smiley'])) {
        $context['html_headers'] .= 'false;';
    }
    else {
        $context['html_headers'] .= 'true;';
    }

    $context['html_headers'] .= 'var current_header_bbc = ';

    if(empty($options['expand_header_bbc'])) {
        $context['html_headers'] .= 'false;';
    }
    else {
        $context['html_headers'] .= 'true;';
    }

    $context['html_headers'] .= '
        // ]]></script>
        <script type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/tinyportal/TPShout.js?'.TPVERSION.'"></script>';

    if(file_exists($settings['theme_dir'].'/css/tp-shout.css')) {
        $context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/css/tp-shout.css?'.TPVERSION.'" />';
    }
    else {
        $context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['default_theme_url']. '/css/tp-shout.css?'.TPVERSION.'" />';
    }

}}}

// Post the shout via ajax
function TPShoutPost( ) {{{
	global $context, $smcFunc, $user_info, $scripturl, $sourcedir, $modSettings;

	isAllowedTo('tp_can_shout');

	if(!empty($_POST['tp_shout']) && !empty($_POST['tp-shout-name'])) {
		// Check the session id.
		checkSession('post');
		
		if(TP_SMF21) {
			require_once($sourcedir . '/Subs-Post.php');
		}

		$shout = $smcFunc['htmlspecialchars'](substr($_POST['tp_shout'], 0, $context['TPortal']['shoutbox_maxlength']));
		preparsecode($shout);

		// collect the color for shoutbox
		$request = $smcFunc['db_query']('', '
			SELECT grp.online_color AS onlineColor
			FROM {db_prefix}members AS m
            INNER JOIN {db_prefix}membergroups AS grp
			ON m.id_group = grp.id_group
            WHERE id_member = {int:user} LIMIT 1',
			array('user' => $context['user']['id'])
		);
		if($smcFunc['db_num_rows']($request) > 0) {
			$row = $smcFunc['db_fetch_row']($request);
			$context['TPortal']['usercolor'] = $row[0];
			$smcFunc['db_free_result']($request);
		}

		// Build the name with color for user, otherwise strip guests name of html tags.
		$shout_name = ($user_info['id'] != 0) ? '<a href="'.$scripturl.'?action=profile;u='.$user_info['id'].'"' : strip_tags($_POST['tp-shout-name']);
		if(!empty($context['TPortal']['usercolor'])) {
			$shout_name .= ' style="color: '. $context['TPortal']['usercolor'] . '"';
        }
		$shout_name .= ($user_info['id'] != 0) ? '>'.$context['user']['name'].'</a>' : '';

		$shout_time = time();

		// register the IP and userID, if any
		$ip         = $user_info['ip'];
		$member_id  = $user_info['id'];

		$shout      = str_ireplace(array("<br />","<br>","<br/>"), "\r\n", $shout);

        $block_id		= TPUtil::filter('b', 'post', 'int');
		$row			= TPBlock::getInstance()->getBlock($block_id);
		$set			= json_decode($row['settings'], TRUE);
		$shoutbox_id	= $set['shoutbox_id'];

        if(empty($shoutbox_id)) {
            $shoutbox_id = 0;
        }

        if($shout != '') {
            $tpShout = TPShout::getInstance();
            $shout_id = $tpShout->insertShout(
                array(
                    'content'       => $shout,
                    'time'          => $shout_time,
                    'member_link'   => $shout_name,
                    'type'          => 'shoutbox',
                    'member_ip'     => $ip,
                    'member_id'     => $member_id,
                    'edit'          => 0,
                    'shoutbox_id'   => $shoutbox_id
                )
            );
            $mention_data['id']             = $shout_id;
            $mention_data['content']        = $shout;
            $mention_data['type']           = 'shout';
            $mention_data['member_id']      = $user_info['id'];
            $mention_data['username']       = $user_info['username'];
            $mention_data['action']         = 'mention';
            $mention_data['event_title']    = 'Shoutbox Mention';
            $mention_data['text']           = 'Shout';

            $tpMention = TPMentions::getInstance();
            $tpMention->addMention($mention_data);
        }
    }

}}}

// This is to delete a shout via ajax
function TPShoutDelete( $shout_id = null ) {{{
    $tpShout = TPShout::getInstance();

	// A couple of security checks
	checkSession('post');
	isAllowedTo('tp_can_admin_shout');

	if(!empty($shout_id)) {
        $tpShout->deleteShout($shout_id);
	}

}}}

// fetch all the shouts for output
function TPShoutFetch($block_id = null, $render = true, $limit = 1, $ajaxRequest = false) {{{
	global $context, $scripturl, $modSettings, $smcFunc;
	global $image_proxy_enabled, $image_proxy_secret, $boardurl;

// Force this to reset each time
    $context['TPortal']['shoutbox'] = null;

    if(is_null($block_id) || $block_id === false) {
        redirectexit();
    }

    $row					= TPBlock::getInstance()->getBlock($block_id);
    if(!is_array($row) || (isset($row['type']) && ((int)$row['type'] != TP_BLOCK_SHOUTBOX))) {
        redirectexit();
    }
    $set					= json_decode($row['settings'], TRUE);
	$shoutbox_id			= $set['shoutbox_id'];
    $shoutbox_layout		= $set['shoutbox_layout'];
    $shoutbox_height		= $set['shoutbox_height'];
	$shoutbox_avatar		= $set['shoutbox_avatar'];
	$shoutbox_barposition	= $set['shoutbox_barposition'];
	$shoutbox_direction		= $set['shoutbox_direction'];

	// get x number of shouts
	$context['TPortal']['profile_shouts_hide'] = empty($context['TPortal']['profile_shouts_hide']) ? '0' : '1';
	$context['TPortal']['usercolor'] = '';
	// collect the color for shoutbox
	$request = $smcFunc['db_query']('', '
		SELECT grp.online_color AS onlineColor
		FROM {db_prefix}members AS m
        INNER JOIN {db_prefix}membergroups AS grp
		ON m.id_group = grp.id_group
		WHERE id_member = {int:user} LIMIT 1',
		array('user' => $context['user']['id'])
	);

	if($smcFunc['db_num_rows']($request) > 0){
		$row = $smcFunc['db_fetch_row']($request);
		$context['TPortal']['usercolor'] = $row[0];
		$smcFunc['db_free_result']($request);
	}

	if(is_numeric($context['TPortal']['shoutbox_limit']) && $limit == 1) {
		$limit = $context['TPortal']['shoutbox_limit'];
    }

	// don't fetch more than a hundred - save the poor server! :D
	$nshouts = '';
	if($limit > 100)
		$limit = 100;

	loadTemplate('TPShout');

    $block_shout = ' 1 = 1';
	if(!empty($shoutbox_id)) {
		$block_shout = ' s.shoutbox_id = {int:shoutbox_id} ';
	}
	else {
		redirectexit();
	}

	$members = array();
	$request =  $smcFunc['db_query']('', '
		SELECT s.*
			FROM {db_prefix}tp_shoutbox AS s
		WHERE
        '.$block_shout.'
		ORDER BY s.time DESC LIMIT {int:limit}',
		array(
            'limit' => $limit,
            'shoutbox_id' => $shoutbox_id,
        )
	);

	if($smcFunc['db_num_rows']($request) > 0 ) {
		while($row = $smcFunc['db_fetch_assoc']($request)) {
			$fetched[] = $row;
			if(!empty($row['member_id']) && !in_array($row['member_id'], $members)) {
				$members[] = $row['member_id'];
            }
		}
		$smcFunc['db_free_result']($request);
	}

	if(count($members) > 0 ) {
		$request2 =  $smcFunc['db_query']('', '
		    SELECT mem.id_member, mem.real_name AS real_name, mem.email_address AS email_address,
			    mem.avatar, COALESCE(a.id_attach,0) AS id_attach, a.filename, COALESCE(a.attachment_type,0) AS attachment_type, mgrp.online_color AS mg_online_color, pgrp.online_color AS pg_online_color
		    FROM {db_prefix}members AS mem
			LEFT JOIN {db_prefix}membergroups AS mgrp ON
				(mgrp.id_group = mem.id_group)
			LEFT JOIN {db_prefix}membergroups AS pgrp ON
				(pgrp.id_group = mem.id_post_group)
			LEFT JOIN {db_prefix}attachments AS a ON
                (a.id_member = mem.id_member and a.attachment_type!=3)
		    WHERE mem.id_member IN(' . implode(",",$members) . ')'
	    );
    }

	$memberdata = array();
	if(isset($request2) && $smcFunc['db_num_rows']($request2)>0) {
		while($row = $smcFunc['db_fetch_assoc']($request2)) {
            $row['avatar'] = set_avatar_data( array(
                    'avatar' => $row['avatar'],
                    'email' => $row['email_address'],
                    'filename' => !empty($row['filename']) ? $row['filename'] : '',
                    'id_attach' => $row['id_attach'],
                    'attachment_type' => $row['attachment_type'],
                )
            )['image'];
			$memberdata[$row['id_member']] = $row;
		}
		$smcFunc['db_free_result']($request2);
	}

	if(!empty($fetched) && count($fetched)>0) {
		$counter = 1;
		$ns = array();
		foreach($fetched as $b => $row) {
			$row['avatar'] = !empty($memberdata[$row['member_id']]['avatar']) ? $memberdata[$row['member_id']]['avatar'] : '';
			$row['real_name'] = !empty($memberdata[$row['member_id']]['real_name']) ? $memberdata[$row['member_id']]['real_name'] : $row['member_link'];
			$row['content'] = parse_bbc(censorText($row['content']), true);
			$row['online_color'] = !empty($memberdata[$row['member_id']]['mg_online_color']) ? $memberdata[$row['member_id']]['mg_online_color'] : (!empty($memberdata[$row['member_id']]['pg_online_color']) ? $memberdata[$row['member_id']]['pg_online_color'] : '');
			$row['counter'] = ++$counter;
			$ns[] = template_singleshout($row, $block_id);
		}

		if($shoutbox_direction == 1) { 
			$ns = array_reverse($ns);
		}

		$nshouts .= implode('', $ns);

		$context['TPortal']['shoutbox'] = $nshouts;
	}

	// its from a block, render it
	if($render && !$ajaxRequest) {
		template_tpshout_shoutblock($block_id);
    }
	else {
		return $nshouts;
    }


}}}

function tpshout_bigscreen($state = false, $number = 10, $block_id = 0) {{{
    global $context;

    loadTemplate('TPShout');
	$context['TPortalShoutboxId'] = $block_id;

	if ($state == false) {
        $context['template_layers']         = array();
        $context['sub_template']            = 'tpshout_ajax';
        $context['TPortal']['rendershouts'] = TPShoutFetch($block_id, $state, $number, true);
    }
    else {
        $context['TPortal']['rendershouts'] = TPShoutFetch($block_id, false, $number, false);
        TP_setThemeLayer('tpshout', 'TPortal', 'tpshout_bigscreen');
        $context['page_title'] = 'Shoutbox';
    }

}}}

function shout_bbc_code($shoutbox_id, $collapse = true) {{{
	global $context, $txt, $settings, $option;

	loadLanguage('Post');

	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		function tp_bbc_highlight(something, mode)
		{
			something.style.backgroundImage = "url(" + smf_images_url + (mode ? "/bbc/bbc_hoverbg.gif)" : "/bbc/bbc_bg.gif)");
		}
	// ]]></script>';

    // The below array makes it dead easy to add images to this page. Add it to the array and everything else is done for you!
    $context['tp_bbc_tags'] = array();
    $context['tp_bbc_tags2'] = array();

    global $editortxt;
    loadLanguage('Editor');

	$context['tp_bbc_tags'][] = array(
		'bold' => array('code' => 'b', 'before' => '[b]', 'after' => '[/b]', 'description' => $editortxt['bold']),
		'italic' => array('code' => 'i', 'before' => '[i]', 'after' => '[/i]', 'description' => $editortxt['italic']),
		'underline' => array('code' => 'u', 'before' => '[u]', 'after' => '[/u]', 'description' => $editortxt['underline']),
		'strike' => array('code' => 's', 'before' => '[s]', 'after' => '[/s]', 'description' => $editortxt['strikethrough']),
	);
	$context['tp_bbc_tags2'][] = array(
	);

	if($collapse) {
		echo '  <div class="tp_expand_bbcbox" id="expand_bbc_parent_' . $shoutbox_id . '" onclick="expandHeaderBBC(!current_header_bbc, \'' . $shoutbox_id . '\', ' . ($context['user']['is_guest'] ? 'true' : 'false') . ', \'' . $context['session_id'] . '\'); return false;">
		            <img class="tp_expand_shoutbbc" id="expand_bbc_' . $shoutbox_id . '" src="', $settings['tp_images_url'], '/', empty($options['expand_header_bbc']) ? 'TPexpand.png' : 'TPcollapse.png', '" alt="*" title="', array_key_exists('upshrink_description', $txt) ? $txt['upshrink_description'] : '', '" />
	            </div>';
    }

	$found_button = false;
	// Here loop through the array, printing the images/rows/separators!
	if(isset($context['tp_bbc_tags'][0]) && count($context['tp_bbc_tags'][0]) > 0) {
		foreach ($context['tp_bbc_tags'][0] as $image => $tag) {
			echo '<div class="sceditor-button sceditor-button-'.$image.'" onclick="surroundShoutText(\'', $tag['before'], '\', \'', $tag['after'], '\', \'', $context['tp_shout_post_box_name'], '\'); return false;" style="display: inline;padding:0px;"><div unselectable="on">'.$tag['description'].'</div></div>';
		}
	}

	if($collapse) {
		echo '<div id="expandHeaderBBC_' . $shoutbox_id . '"', empty($options['expand_header_bbc']) ? ' style="display: none;"' : 'style="display: inline;"' , '>';
    }
	else {
		echo '<div style="display: inline;">';
    }

	$found_button1 = false;
	// Here loop through the array, printing the images/rows/separators!
	if(isset($context['tp_bbc_tags2'][0]) && count($context['tp_bbc_tags2'][0])>0)
	{
		foreach ($context['tp_bbc_tags2'][0] as $image => $tag)
		{
			echo '<div class="sceditor-button sceditor-button-'.$image.'" onclick="surroundShoutText(\'', $tag['before'], '\', \'', $tag['after'], '\', \'', $context['tp_shout_post_box_name'], '\'); return false;" style="display: inline;padding:0px;"><div unselectable="on">'.$tag['description'].'</div></div>';
 		}
	}

	// Print a drop down list for all the colors we allow!
	if (!isset($context['shout_disabled_tags']['color']))
		echo ' <p class="clearthefloat"></p> 
				<select onchange="surroundShoutText(\'[color=\' + this.options[this.selectedIndex].value.toLowerCase() + \']\', \'[/color]\', \'', $context['tp_shout_post_box_name'], '\'); this.selectedIndex = 0; document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '.focus(document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '.caretPos);" style="margin-top: 5px;">
					<option value="" selected="selected">'.$txt['tp_change_color'].'</option>
					<option value="Black">'.$txt['tp_black'].'</option>
					<option value="Red">'.$txt['tp_red'].'</option>
					<option value="Yellow">'.$txt['tp_yellow'].'</option>
					<option value="Pink">'.$txt['tp_pink'].'</option>
					<option value="Green">'.$txt['tp_green'].'</option>
					<option value="Orange">'.$txt['tp_orange'].'</option>
					<option value="Purple">'.$txt['tp_purple'].'</option>
					<option value="Blue">'.$txt['tp_blue'].'</option>
					<option value="Beige">'.$txt['tp_beige'].'</option>
					<option value="Brown">'.$txt['tp_brown'].'</option>
					<option value="Teal">'.$txt['tp_teal'].'</option>
					<option value="Navy">'.$txt['tp_navy'].'</option>
					<option value="Maroon">'.$txt['tp_maroon'].'</option>
					<option value="LimeGreen">'.$txt['tp_limegreen'].'</option>
				</select>';
	echo '<br />';

	$found_button2 = false;
	// Print the bottom row of buttons!
	if(isset($context['tp_bbc_tags'][1]) && count($context['tp_bbc_tags'][1])>0)
	{
		foreach ($context['tp_bbc_tags'][1] as $image => $tag)
		{
			if (isset($tag['before']))
			{
				// Is this tag disabled?
				if (!empty($context['shout_disabled_tags'][$tag['code']]))
					continue;

				$found_button2 = true;

				// If there's no after, we're just replacing the entire selection in the post box.
				if (!isset($tag['after']))
					echo '<div style="display: inline;" onclick="replaceShoutText(\'', $tag['before'], '\', \'', $context['tp_shout_post_box_name'], '\'); return false;">';
				// On the other hand, if there is one we are surrounding the selection ;).
				else
					echo '<div style="display: inline;" onclick="surroundShoutText(\'', $tag['before'], '\', \'', $tag['after'], '\', \'', $context['shout_post_box_name'], '\'); return false;">';

				// Okay... we have the link. Now for the image and the closing </a>!
				echo '<img onmouseover="tp_bbc_highlight(this, true);" onmouseout="if (window.tp_bbc_highlight) tp_bbc_highlight(this, false);" src="', $settings['images_url'], '/bbc/', $image, '.gif" width="23" height="22" alt="', $tag['description'], '" title="', $tag['description'], '" style="background-image: url(', $settings['images_url'], '/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;vertical-align:bottom" /></div>';
			}
			// I guess it's a divider...
			elseif ($found_button2)
			{
				echo '<img src="', $settings['images_url'], '/bbc/divider.gif" alt="|" style="margin: 0 3px 0 3px;" />';
				$found_button2 = false;
			}
		}
	}
	echo '
	</div>';

}}}

function shout_smiley_code($shoutbox_id) {{{
    global $context, $settings, $user_info, $txt, $modSettings, $smcFunc;

    // Initialize smiley array...
    $context['tp_smileys'] = array(
        'postform' => array(),
        'popup' => array(),
    );

	if ($user_info['smiley_set'] != 'none')
	{
		// Cache for longer when customized smiley codes aren't enabled
		$cache_time = empty($modSettings['smiley_enable']) ? 7200 : 480;

		if (($temp = cache_get_data('tp_posting_smileys_' . $user_info['smiley_set'], $cache_time)) == null)
		{
			$request = $smcFunc['db_query']('', '
				SELECT s.code, f.filename, s.description, s.smiley_row, s.hidden
				FROM {db_prefix}smileys AS s
					JOIN {db_prefix}smiley_files AS f ON (s.id_smiley = f.id_smiley)
				WHERE s.hidden IN (0, 2)
					AND f.smiley_set = {string:smiley_set}' . (empty($modSettings['smiley_enable']) ? '
					AND s.code IN ({array_string:default_codes})' : '') . '
				ORDER BY s.hidden, s.smiley_row, s.smiley_order',
				array(
					'default_codes' => array('>:D', ':D', '::)', '>:(', ':))', ':)', ';)', ';D', ':(', ':o', '8)', ':P', '???', ':-[', ':-X', ':-*', ':\'(', ':-\\', '^-^', 'O0', 'C:-)', 'O:-)'),
					'smiley_set' => $user_info['smiley_set'],
				)
			);
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$row['description'] = !empty($txt['icon_' . strtolower($row['description'])]) ? $smcFunc['htmlspecialchars']($txt['icon_' . strtolower($row['description'])]) : $smcFunc['htmlspecialchars']($row['description']);

//				$context['tp_smileys'][empty($row['hidden']) ? 'postform' : 'popup'][$row['smiley_row']]['smileys'][] = $row;
				$context['tp_smileys']['postform'][$row['smiley_row']]['smileys'][] = $row;
			}
			$smcFunc['db_free_result']($request);

			foreach ($context['tp_smileys'] as $section => $smileyRows)
			{
				foreach ($smileyRows as $rowIndex => $smileys)
					$context['tp_smileys'][$section][$rowIndex]['smileys'][count($smileys['smileys']) - 1]['isLast'] = true;

				if (!empty($smileyRows))
					$context['tp_smileys'][$section][count($smileyRows) - 1]['isLast'] = true;
			}

			cache_put_data('tp_posting_smileys_' . $user_info['smiley_set'], $context['tp_smileys'], $cache_time);
		}
		else {
			$context['tp_smileys'] = $temp;
        }
	}

	$file_ext = '';

	// Clean house... add slashes to the code for javascript.
	foreach (array_keys($context['tp_smileys']) as $location)
	{
		foreach ($context['tp_smileys'][$location] as $j => $row)
		{
			$n = count($context['tp_smileys'][$location][$j]['smileys']);
			for ($i = 0; $i < $n; $i++)
			{
				$context['tp_smileys'][$location][$j]['smileys'][$i]['code']            = addslashes($context['tp_smileys'][$location][$j]['smileys'][$i]['code']);
                $context['tp_smileys'][$location][$j]['smileys'][$i]['js_description']  = addslashes($context['tp_smileys'][$location][$j]['smileys'][$i]['description']);
				$context['tp_smileys'][$location][$j]['smileys'][$i]['filename']        = $context['tp_smileys'][$location][$j]['smileys'][$i]['filename'].$file_ext;
			}

			$context['tp_smileys'][$location][$j]['smileys'][$n - 1]['last'] = true;
		}
		if (!empty($context['tp_smileys'][$location]))
			$context['tp_smileys'][$location][count($context['tp_smileys'][$location]) - 1]['last'] = true;
	}

	$settings['smileys_url'] = $modSettings['smileys_url'] . '/' . $user_info['smiley_set'];
}}}

function print_shout_smileys($shoutbox_id, $collapse = true) {{{
	global $context, $txt, $settings, $options;

	loadLanguage('Post');

	if (!empty($context['tp_smileys']['postform'])) {
		if($collapse) {
			echo '
			<div class="tp_expand_smileybox" id="expand_parent_smiley_' . $shoutbox_id . '" onclick="expandHeaderSmiley(!current_header_smiley, \'' . $shoutbox_id . '\', '. ($context['user']['is_guest'] ? 'true' : 'false') .', \''. $context['session_id'] .'\'); return false;">
				<img class="tp_expand_shoutsmiley" id="expand_smiley_' . $shoutbox_id . '" src="', $settings['tp_images_url'], '/', empty($options['expand_header_smiley']) ? 'TPexpand.png' : 'TPcollapse.png', '" alt="*" title="', array_key_exists('upshrink_description', $txt) ? $txt['upshrink_description'] : '', '" />
			</div>';
		}

		// Now start printing all of the smileys.
		// counter...
		$sm_counter = 0;
		// Show each row of smileys ;).
		foreach ($context['tp_smileys']['postform'] as $smiley_row) {
			foreach ($smiley_row['smileys'] as $smiley) {
				if($sm_counter == 5 && $collapse) {
					echo '
						<div class="tp_expand_smileybox" id="expandHeaderSmiley_' . $shoutbox_id . '" ', empty($options['expand_header_smiley']) ? ' style="display: none;"' : '' ,'>';
				}
					echo '
						<div style="display: inline;" onclick="replaceShoutText(\' ', $smiley['code'], '\', \'', $context['tp_shout_post_box_name'], '\'); return false;"><img src="', $settings['smileys_url'], '/', $smiley['filename'], '" style="vertical-align:bottom" alt="', $smiley['description'], '" title="', $smiley['description'], '" /></div>';
					$sm_counter++;
			}
		}
		if ($sm_counter > 4) {
		echo '
			</div>'; 
		}
	}
}}}

// show a dedicated frontpage
function tpshout_frontpage() {{{
	global $context;
	loadtemplate('TPShout');
    tpshout_bigscreen(true);
}}}

function shoutHasLinks() {{{
	global $context;
	$shout = !empty($_POST['tp_shout']) ? $_POST['tp_shout'] : '';
    if(TPUtil::hasLinks($shout)) {
		loadTemplate('TPShout');
		$context['TPortal']['shoutError'] = true;
		$context['TPortal']['rendershouts'] = 'Links are not allowed!';
		$context['template_layers'] = array();
		$context['sub_template'] = 'tpshout_ajax';
		return true;
	}
	return false;
}}}

function tp_shoutb($member_id) {{{
    global $txt, $context;
    loadtemplate('TPprofile');
    $context['page_title'] = $txt['shoutboxprofile'];
    tpshout_profile($member_id);
}}}

// fetch all the shouts for output
function tpshout_profile($member_id) {{{
    global $context, $scripturl, $txt, $smcFunc;
    $context['page_title'] = $txt['shoutboxprofile'] ;
    if(isset($context['TPortal']['mystart'])) {
        $start = $context['TPortal']['mystart'];
    }
    else {
        $start = 0;
    }
    $context['TPortal']['member_id'] = $member_id;
    $sorting = 'time';
    $max = 0;
    // get all shouts
    $request = $smcFunc['db_query']('', '
        SELECT COUNT(*) FROM {db_prefix}tp_shoutbox
        WHERE member_id = {int:member_id} AND type = {string:type}',
        array('member_id' => $member_id, 'type' => 'shoutbox')
    );
    $result = $smcFunc['db_fetch_row']($request);
    $max    = $result[0];
    $smcFunc['db_free_result']($request);
    $context['TPortal']['all_shouts'] = $max;
    $context['TPortal']['profile_shouts'] = array();
    $request = $smcFunc['db_query']('', '
        SELECT * FROM {db_prefix}tp_shoutbox
        WHERE member_id = {int:member_id}
        AND type = {string:type}
        ORDER BY {raw:sort} DESC LIMIT 15 OFFSET {int:start}',
        array('member_id' => $member_id, 'type' => 'shoutbox', 'sort' => $sorting, 'start' => $start)
    );
    if($smcFunc['db_num_rows']($request) > 0){
        while($row = $smcFunc['db_fetch_assoc']($request)){
            $context['TPortal']['profile_shouts'][] = array(
                'id' => $row['id'],
                'shout' => parse_bbc(censorText($row['content'])),
                'created' => timeformat($row['time']),
                'ip' => $row['member_ip'],
                'editlink' => allowedTo('tp_shoutbox') ? $scripturl.'?action=tpshout;shout=admin;u='.$member_id : '',
            );
        }
        $smcFunc['db_free_result']($request);
    }
    // construct pageindexes
    if($max > 0) {
        $context['TPortal']['pageindex'] = TPageIndex($scripturl.'?action=profile;area=tpshoutbox;u='.$member_id.';tpsort='.$sorting, $start, $max, '15', true);
    }
    else {
        $context['TPortal']['pageindex'] = '';
    }
    loadtemplate('TPShout');
    if(loadLanguage('TPortal') == false) {
        loadLanguage('TPortal', 'english');
    }
    $context['sub_template'] = 'tpshout_profile';
}}}

// Block Callback
function TPShoutBlock(&$row) {{{
    global $context, $txt, $sourcedir;

    if(loadLanguage('TPortal') == false) {
        loadLanguage('TPortal', 'english');
    }

	$id					= $row['id'];
	$row				= TPBlock::getInstance()->getBlock($id);
	$set				= json_decode($row['settings'], TRUE);
	$shoutbox_direction	= $set['shoutbox_direction'];

    $context['TPortal']['tpblocks']['blockrender'][$id] = array(
        'id'                   => $row['id'],
        'name'                 => $txt['tp-shoutbox'],
        'function'             => 'TPShoutFetch',
        'sourcefile'           => $sourcedir .'/TPShout.php',
    );

    if(!empty($context['TPortal']['shoutbox_refresh'])) {
        $context['html_headers'] .= '
        <script type="text/javascript"><!-- // --><![CDATA[
            window.setInterval("TPupdateShouts(\'fetch\', '.$row['id'].')", '. $context['TPortal']['shoutbox_refresh'] * 1000 . ');
        // ]]></script>';
    }

    if($context['TPortal']['shoutbox_usescroll'] > 0) {
        $context['html_headers'] .= '
        <script type="text/javascript" src="Themes/default/scripts/tinyportal/jquery.marquee.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("marquee").marquee("tp_marquee").mouseover(function () {
                        $(this).trigger("stop");
                    }).mouseout(function () {
                        $(this).trigger("start");
                    });
                });
        </script>';
    }

    if(!empty($context['TPortal']['shout_submit_returnkey'])) {
        if($context['TPortal']['shout_submit_returnkey'] == 1) {
            $context['html_headers'] .= '
            <script type="text/javascript"><!-- // --><![CDATA[
                $(document).ready(function() {
                    $("#tp_shout_'.$row['id'].'").keypress(function(event) {
                        if(event.which == 13 && !event.shiftKey) {
                            tp_shout_key_press = true;
                            // set a 100 millisecond timeout for the next key press
                            window.setTimeout(function() { tp_shout_key_press = false; $("#tp_shout_' . $row['id'] . '").setCursorPosition(0,0);}, 100);
                            TPupdateShouts(\'save\' , '.$row['id'].');
                        }
                    });
                });
            // ]]></script>';
        }
        else if($context['TPortal']['shout_submit_returnkey'] == 2) {
            $context['html_headers'] .= '
            <script type="text/javascript"><!-- // --><![CDATA[
            $(document).ready(function() {
                if ($("#tp_shout_'.$row['id'].'")) {
                    $("#tp_shout_'.$row['id'].'").keydown(function (event) {
                        if((event.metaKey || event.ctrlKey) && event.keyCode == 13) {
                            tp_shout_key_press = true;
                            // set a 100 millisecond timeout for the next key press
                            window.setTimeout(function() { tp_shout_key_press = false; }, 100);
                            TPupdateShouts(\'save\' , '.$row['id'].');
                        }
                        else if (event.keyCode == 13) {
							$("#tp_shout_' . $row['id'] . '").setCursorPosition(0,0);
                            event.preventDefault();
                        }
                    });
                }
            });
            // ]]></script>';
        }
    }
    else {
        $context['html_headers'] .= '
            <script type="text/javascript"><!-- // --><![CDATA[
            $(document).ready(function() {
                if ($("#tp_shout_'.$row['id'].'")) {
                    $("#tp_shout_'.$row['id'].'").keydown(function (event) {
                        if (event.keyCode == 13) {
                            event.preventDefault();
                        }
                    });
                }
            });
            // ]]></script>';
    }

	if($shoutbox_direction == 1) { 
		$context['html_headers'] .= '
		<script type="text/javascript"><!-- // --><![CDATA[
        $(document).ready(function() {
            $(".tp_shoutframe.tp_shoutframe_' . $id . '").parent().scrollTop($(document).height() + $(window).height());
        });
        // ]]></script>';
	}
	else {
        $context['html_headers'] .= '
		<script type="text/javascript"><!-- // --><![CDATA[
        $(document).ready(function() {
            $(".tp_shoutframe.tp_shoutframe_' . $id . '").parent().scrollTop(0);
        });
        // ]]></script>';
	}

}}}

// Admin Area
function TPShoutAdminActions(&$subActions) {{{

   $subActions = array_merge(
        array (
            'shout'      => array('TPShout.php', 'TPShoutAdmin',   array()),
        ),
        $subActions
    );

}}}

function TPShoutAdmin() {{{
	global $context, $scripturl, $txt, $smcFunc, $sourcedir;

	// check permissions
	isAllowedTo('tp_can_admin_shout');

    if(!(isset($_GET['shout']) && $_GET['shout'] == 'admin')) {
        return;
    }

	if(!isset($context['tp_panels'])) {
		$context['tp_panels'] = array();
    }

	if(isset($_GET['p']) && is_numeric($_GET['p'])) {
		$tpstart = $_GET['p'];
    }
	else {
		$tpstart = 0;
    }

	if(TP_SMF21) {
		require_once($sourcedir . '/Subs-Post.php');
	}
	loadtemplate('TPShout');

	$context['template_layers'][] = 'tpadm';
	$context['template_layers'][] = 'subtab';
	loadlanguage('TPortalAdmin');

	TPadminIndex('shout');
	$context['current_action'] = 'admin';

    // clear the linktree first
    TPstrip_linktree();

	// Set the linktree
	TPadd_linktree($scripturl.'?action=tpshout', 'TPshout');

	if(isset($_REQUEST['send']) || isset($_REQUEST[$txt['tp-send']]) || isset($_REQUEST['tp_preview']) || isset($_REQUEST['TPadmin_blocks'])) {
		$go = 0;
		$changeArray = array();
		foreach ($_POST as $what => $value) {
			if(substr($what, 0, 18) == 'tp_shoutbox_remove') {
				$val = substr($what, 18);
				$smcFunc['db_query']('', '
					DELETE FROM {db_prefix}tp_shoutbox
					WHERE id = {int:shout}',
					array('shout' => $val)
				);
				$go = 2;
			}
			elseif($what == 'tp_shoutsdelall' && $value == 'ON') {
				$smcFunc['db_query']('', '
					DELETE FROM {db_prefix}tp_shoutbox
					WHERE type = {string:type}',
					array('type' => 'shoutbox')
				);
				$go = 2;
			}
			elseif(substr($what, 0, 16) == 'tp_shoutbox_item') {
				$val = substr($what, 16);
				$bshout = $smcFunc['htmlspecialchars'](substr($value, 0, $context['TPortal']['shoutbox_maxlength']));
				preparsecode($bshout);
				$bshout = str_ireplace(array("<br />","<br>","<br/>"), "\r\n", $bshout);
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_shoutbox
					SET content = {string:val1}
					WHERE id = {int:val}',
					array('val1' => $bshout, 'val' => $val)
				);
				$go = 2;
			}
			else {
				$what = substr($what, 3);
				if($what == 'shoutbox_smile') {
					$changeArray['show_shoutbox_smile'] = $value;
                }

                if($what == 'shoutbox_icons') {
					$changeArray['show_shoutbox_icons'] = $value;
                }

                if($what == 'shoutbox_height') {
					$changeArray['shoutbox_height'] = $value;
                }

				if($what == 'shoutbox_usescroll') {
					$changeArray['shoutbox_usescroll'] = $value;
                }

				if($what == 'shoutbox_scrollduration') {
					if($value > 5) {
						$value = 5;
                    }
					else if($value < 1) {
						$value = 1;
                    }

					$changeArray['shoutbox_scrollduration'] = $value;
				}

				if($what == 'shoutbox_limit') {
					if(!is_numeric($value)) {
						$value = 10;
                    }
					$changeArray['shoutbox_limit'] = $value;
				}

				if($what == 'shoutbox_refresh') {
					if(empty($value)) {
						$value = '0';
                    }
					$changeArray['shoutbox_refresh'] = $value;
				}

				if($what == 'show_profile_shouts') {
					$changeArray['profile_shouts_hide'] = $value;
                }

				if($what == 'shout_allow_links') {
					$changeArray['shout_allow_links'] = $value;
                }

				if($what == 'shoutbox_layout') {
					$changeArray['shoutbox_layout'] = $value;
                }

				if($what == 'shout_submit_returnkey') {
					$changeArray['shout_submit_returnkey'] = $value;
                }

				if($what == 'shoutbox_stitle') {
					$changeArray['shoutbox_stitle'] = $value;
                }

				if($what == 'shoutbox_maxlength') {
					$changeArray['shoutbox_maxlength'] = $value;
                }

				if($what == 'shoutbox_timeformat2') {
					$changeArray['shoutbox_timeformat2'] = $value;
                }

				if($what == 'shoutbox_use_groupcolor') {
					$changeArray['shoutbox_use_groupcolor'] = $value;
                }

				if($what == 'shoutbox_textcolor') {
					$changeArray['shoutbox_textcolor'] = $value;
                }

				if($what == 'shoutbox_timecolor') {
					$changeArray['shoutbox_timecolor'] = $value;
                }

				if($what == 'shoutbox_linecolor1') {
					$changeArray['shoutbox_linecolor1'] = $value;
                }

				if($what == 'shoutbox_linecolor2') {
					$changeArray['shoutbox_linecolor2'] = $value;
				}
            }
		}
		updateTPSettings($changeArray, true);

		if(empty($go)) {
			redirectexit('action=tpshout;shout=admin;settings');
        }
		else {
			redirectexit('action=tpshout;shout=admin');
        }
	}

	// get latest shouts for admin section
	// check that a member has been filtered
	if(isset($_GET['u'])) {
		$member_id = $_GET['u'];
    }

	// check that a IP has been filtered
	if(isset($_GET['ip'])) {
		$ip = $_GET['ip'];
    }

	// check that a Shoutbox ID has been filtered
	if(isset($_GET['shoutbox_id'])) {
		$shoutbox_id = $_GET['shoutbox_id'];
    }

	if(isset($_GET['s'])) {
		$single = $_GET['s'];
    }

	$context['TPortal']['admin_shoutbox_items'] = array();

	if(isset($member_id)) {
		$shouts =  $smcFunc['db_query']('', '
			SELECT COUNT(*) FROM {db_prefix}tp_shoutbox
			WHERE type = {string:type}
			AND member_id = {int:val5}',
			array('type' => 'shoutbox', 'val5' => $member_id)
		);
		$weh = $smcFunc['db_fetch_row']($shouts);
		$smcFunc['db_free_result']($shouts);
		$allshouts = $weh[0];
		$context['TPortal']['admin_shoutbox_items_number'] = $allshouts;
		$context['TPortal']['shoutbox_pageindex'] = ''.$txt['tp-member'].'&nbsp;'.$member_id.' ' .$txt['tp-filtered'] . ' (<a href="'.$scripturl.'?action=tpshout;shout=admin">' . $txt['remove'] . '</a>) <br />'.TPageIndex($scripturl.'?action=tpshout;shout=admin;u='.$member_id, $tpstart, $allshouts, 10, true);
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_shoutbox
			WHERE type = {string:type}
			AND member_id = {int:val5}
			ORDER BY time DESC LIMIT {int:start},10',
			array('type' => 'shoutbox', 'val5'=> $member_id, 'start' => $tpstart)
		);
	}
	elseif(isset($ip)) {
		$shouts =  $smcFunc['db_query']('', '
			SELECT COUNT(*) FROM {db_prefix}tp_shoutbox
			WHERE type = {string:type}
			AND member_ip = {string:val4}',
			array('type' => 'shoutbox', 'val4' => $ip)
		);
		$weh = $smcFunc['db_fetch_row']($shouts);
		$smcFunc['db_free_result']($shouts);
		$allshouts = $weh[0];
		$context['TPortal']['admin_shoutbox_items_number'] = $allshouts;
		$context['TPortal']['shoutbox_pageindex'] = ''.$txt['tp-IP'].'&nbsp;'.$ip.' ' .$txt['tp-filtered'] . ' (<a href="'.$scripturl.'?action=tpshout;shout=admin">' . $txt['remove'] . '</a>) <br />'.TPageIndex($scripturl.'?action=tpshout;shout=admin;ip='.urlencode($ip) , $tpstart, $allshouts, 10,true);
		$request =  $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_shoutbox
			WHERE type = {string:type}
			AND member_ip = {string:val4}
			ORDER BY time DESC LIMIT {int:start}, 10',
			array('type' => 'shoutbox', 'val4' => $ip, 'start' => $tpstart)
		);
	}
	elseif(isset($shoutbox_id)) {
		$shouts =  $smcFunc['db_query']('', '
			SELECT COUNT(*) FROM {db_prefix}tp_shoutbox
			WHERE type = {string:type}
			AND shoutbox_id = {string:val4}',
			array('type' => 'shoutbox', 'val4' => $shoutbox_id)
		);
		$weh = $smcFunc['db_fetch_row']($shouts);
		$smcFunc['db_free_result']($shouts);
		$allshouts = $weh[0];
		$context['TPortal']['admin_shoutbox_items_number'] = $allshouts;
		$context['TPortal']['shoutbox_pageindex'] = ''.$txt['tp-shoutbox_id'].'&nbsp;'.$shoutbox_id.' ' .$txt['tp-filtered'] . ' (<a href="'.$scripturl.'?action=tpshout;shout=admin">' . $txt['remove'] . '</a>) <br />'.TPageIndex($scripturl.'?action=tpshout;shout=admin;shoutbox_id='.urlencode($shoutbox_id) , $tpstart, $allshouts, 10,true);
		$request =  $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_shoutbox
			WHERE type = {string:type}
			AND shoutbox_id = {string:val4}
			ORDER BY time DESC LIMIT {int:start}, 10',
			array('type' => 'shoutbox', 'val4' => $shoutbox_id, 'start' => $tpstart)
		);
	}
	elseif(isset($single)) {
		// check session
		checkSession('get');
		$context['TPortal']['shoutbox_pageindex'] = '';
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_shoutbox
			WHERE type = {string:type}
			AND id = {int:shout}',
			array('type' => 'shoutbox', 'shout' => $single)
		);
	}
	else {
		$shouts = $smcFunc['db_query']('', '
			SELECT COUNT(*) FROM {db_prefix}tp_shoutbox
			WHERE type = {string:type}',
			array('type' => 'shoutbox')
		);
		$weh = $smcFunc['db_fetch_row']($shouts);
		$smcFunc['db_free_result']($shouts);
		$allshouts = $weh[0];
		$context['TPortal']['admin_shoutbox_items_number'] = $allshouts;
		$context['TPortal']['shoutbox_pageindex'] = TPageIndex($scripturl.'?action=tpshout;shout=admin', $tpstart, $allshouts, 10,true);
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_shoutbox
			WHERE type = {string:type}
			ORDER BY time DESC LIMIT 10 OFFSET {int:start}',
			array('type' => 'shoutbox', 'start' => $tpstart)
		);
	}

	if($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['TPortal']['admin_shoutbox_items'][] = array(
				'id' => $row['id'],
				'body' => html_entity_decode($row['content'], ENT_QUOTES),
				'poster' => $row['member_link'],
				'timestamp' => $row['time'],
				'time' => timeformat($row['time']),
				'ip' => $row['member_ip'],
				'id_member' => $row['member_id'],
				'sort_member' => '<a href="'.$scripturl.'?action=tpshout;shout=admin;u='.$row['member_id'].'">'.$txt['tp-allshoutsbymember'].'</a>',
				'sort_ip' => '<a href="'.$scripturl.'?action=tpshout;shout=admin;ip='.$row['member_ip'].'">'.$txt['tp-allshoutsbyip'].'</a>',
				'sort_shoutbox_id' => '<a href="'.$scripturl.'?action=tpshout;shout=admin;shoutbox_id='.$row['shoutbox_id'].'">'.$txt['tp-allshoutsbyid'].'</a>',
				'single' => isset($single) ? '<hr><a href="'.$scripturl.'?action=tpshout;shout=admin"><b>'.$txt['tp-allshouts'].'</b></a>' : '',
				'shoutbox_id' => $row['shoutbox_id'],
			);
		}
		$smcFunc['db_free_result']($request);
	}

	$context['TPortal']['subtabs'] = '';
	// setup menu items
	if (allowedTo('tp_can_admin_shout')) {
		$context['TPortal']['subtabs'] = array(
			'shoutbox_settings' => array(
				'text' => 'tp-settings',
				'url' => $scripturl . '?action=tpshout;shout=admin;settings',
				'active' => (isset($_GET['action']) && ($_GET['action']=='tpshout' || $_GET['action']=='tpadmin' ) && isset($_GET['shout']) && $_GET['shout']=='admin' && isset($_GET['settings'])) ? true : false,
			),
			'shoutbox' => array(
				'text' => 'tp-shoutbox',
				'url' => $scripturl . '?action=tpshout;shout=admin',
				'active' => (isset($_GET['action']) && ($_GET['action']=='tpshout' || $_GET['action']=='tpadmin' ) && isset($_GET['shout']) && $_GET['shout']=='admin' && !isset($_GET['settings'])) ? true : false,
			),
		);
		$context['admin_header']['tp_shout'] = $txt['tp_shout'];
	}

	// on settings screen?
	if(isset($_GET['settings'])) {
		$context['sub_template'] = 'tpshout_admin_settings';
    }
	else {
		$context['sub_template'] = 'tpshout_admin';
    }

	$context['page_title'] = 'Shoutbox admin';

/*	tp_hidebars();*/
}}}

function TPShoutAdminAreas() {{{

    global $context, $scripturl;

	if (allowedTo('tp_can_admin_shout')) {
		$context['admin_tabs']['custom_modules']['tpshout'] = array(
			'title' => 'TPshout',
			'description' => '',
			'href' => $scripturl . '?action=tpshout;shout=admin;settings',
			'is_selected' => isset($_GET['shout']),
		);
		$admin_set = true;
	}

}}}

?>
