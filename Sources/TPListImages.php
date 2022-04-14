<?php
/**
 * @package TinyPortal
 * @version 2.2.0
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

if (!defined('SMF')) {
	die('Hacking attempt...');
}

function template_tp_list_images_admin() {

	global $txt, $context, $boarddir, $scripturl;

 	if(loadLanguage('TPListImages') == false) {
		loadLanguage('TPListImages', 'english');
    }

	isAllowedTo('tp_can_list_images');

    if(!isset($context['tp_panels']))
        $context['tp_panels'] = array();

    $context['template_layers'][]   = 'tpadm';
    $context['template_layers'][]   = 'subtab';
    TPadminIndex();
    $context['current_action']      = 'admin';
    $context['sub_template']        = 'tp_list_images_admin';

    if($context['TPortal']['hidebars_admin_only'] == '1') {
        tp_hidebars();
    }

    if(array_key_exists('remove', $_GET)) {
	    TPRemoveImage($_POST['image']);
    }

    $ret = '';

    if(array_key_exists('id_member', $_POST)) {
		$ret = TPListImageAdmin($_POST['id_member']);
    }

    $users = TPMembers();

    echo '
		<form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpadmin;sa=listimage;list"  method="post" style="margin: 0px;">
		<div class="cat_bar"><h3 class="catbg">'.$txt['tp-listimage-settings'].'</h3></div>
		<div id="tplistimages" class="admintable admin-area">
			<div class="information smalltext">' , $txt['tp-listimage-intro'] , '</div><div></div>
			<div class="windowbg noup">
				<div class="padding-div">
					<input type="hidden" name="sc" value="', $context['session_id'], '" />
					<select name="id_member">';

            foreach ( $users as $id => $name)
                echo '<option value="'.$id.'">'.$name.'</option>';

    echo '
				</select>
				<input type="submit" value="'.$txt['tp-listimage-list'].'" name="'.$txt['tp-listimage-list'].'">
				</div>
			</div>
		</form>';

    echo $ret;

}

function TPListImageAdmin($user_id = 0) {{{
    global $txt, $boarddir, $boardurl, $context, $scripturl;

 	if(loadLanguage('TPListImages') == false) {
		loadLanguage('TPListImages', 'english');
    }

    if($user_id == 0) {
        $context['sub_template'] = 'tp_list_images_admin';
        return;
    }

    $html = '';
    // fetch all images you have uploaded
    $imgfiles = array();
    if ($handle = opendir($context['TPortal']['image_upload_path'].'thumbs/')) {
        while (false !== ($file = readdir($handle))) {
            if($file != '.' && $file !='..' && $file !='.htaccess' && substr($file, 0, strlen($user_id) + 9) == 'thumb_'.$user_id.'uid') {
                $imgfiles[($context['TPortal']['image_upload_path'].'thumbs/'.$file)] = $file;
            }
        }
        closedir($handle);
        ksort($imgfiles);
        $imgs = $imgfiles;
    }

    $html .='
        <div class="roundframe tp_pad" style="max-height: 800px; overflow: auto;">
            <div class="tpthumb" style="overflow: auto;">';

    if(!empty($imgs)) {
        foreach($imgs as $im) {
            if(!is_file($context['TPortal']['image_upload_path'].''.substr($im, 6))) {
                if(is_file($context['TPortal']['image_upload_path'].'thumbs/'.$im)) {
                    $image      = substr($im, 6);
                    $imageUrl   = str_replace($boarddir, $boardurl, $context['TPortal']['image_upload_path']).'/thumbs/'.$im;
                }
                else {
                    continue;
                }
            }
            else {
                $image          = substr($im, 6);
                $imageUrl       = str_replace($boarddir, $boardurl, $context['TPortal']['image_upload_path']).''.$image;
            }

            $html .= '<form class="tborder" accept-charset="'.$context['character_set'].'" name="TPadmin" action="' . $scripturl . '?action=tpadmin;sa=listimage;remove"  method="post" style="margin: 0px;">
                <div class="windowbg tp_floatleft;">
                    <input type="hidden" name="sc" value="'.$context['session_id'].'" />
                    <input type="hidden" name="id_member" value="'.$user_id.'" />
                    <input type="hidden" name="image" value="'.$image.'" />
                    <div class="tp_listimage"><img src="'.$imageUrl.'" alt="'.$image.'" title="'.$image.'" /><br>
					'.$image.'
					</div>
                    <div style="text-align:center;"><input type="submit" value="'.$txt['tp-listimage-remove'].'" name="'.$txt['tp-listimage-remove'].'"></div>
				</div>
            </form>';
        }

    }

    $html .= '
			</div>
		<div class="padding-div"></div>
	</div>';

    return $html;
}}}

function TPRemoveImage( $image ) {{{
    global $boarddir, $context;

    $fileNameThumb  = $context['TPortal']['image_upload_path'].'thumbs/thumb_'.$image;
    $fileName       = $context['TPortal']['image_upload_path'].''.$image;
    if(file_exists($fileNameThumb)) {
        unlink($fileNameThumb);
    }

    if(file_exists($fileName)) {
        unlink($fileName);
    }
}}}

function TPMembers() {{{
	global $smcFunc, $context, $boarddir, $txt;

	$users	= array();
    if ($handle = opendir($context['TPortal']['image_upload_path'].'thumbs')) {
        while (false !== ($file = readdir($handle))) {
            if($file != '.' && $file !='..' && $file !='.htaccess') {
                if(preg_match('/thumb_(.*?)uid/', $file, $matches)) {
                    $users[$matches[1]] = $matches[1];
                }
            }
        }
        closedir($handle);
    }

    foreach ($users as $user_id) {
		$data	=  $smcFunc['db_query']('', '
			SELECT member_name FROM {db_prefix}members
			WHERE id_member = {int:member_id}',
			array (
				'member_id' => $user_id
			)
		);
		if ($smcFunc['db_affected_rows']() != 0) {
			$member	= $smcFunc['db_fetch_assoc']($data)['member_name'];
			$users[$user_id] = $member;
		}
        else {
            $users[$user_id] = $txt['tp-listimage-username'].' '.$user_id;
        }
		$smcFunc['db_free_result']($data);
	}

	return $users;
}}}

function TPListImageAdminAreas() {{{

    global $context, $scripturl;

	if (allowedTo('tp_can_list_images')) {
		$context['admin_tabs']['custom_modules']['tplistimage'] = array(
			'title' => 'TPlistimage',
			'description' => '',
			'href' => $scripturl . '?action=tpadmin;sa=listimage;list',
			'is_selected' => isset($_GET['listimage']),
		);
		$admin_set = true;
	}

}}}

function TPListImageAdminActions(&$subActions) {{{

   $subActions = array_merge(
        array (
            'listimage'      => array('TPListImages.php', 'TPListImageAdmin',   array()),
        ),
        $subActions
    );

}}}
?>
