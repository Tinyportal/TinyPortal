<?php
/**
 * @package TinyPortal
 * @version 1.0.0
 * @author tinoest
 * @license BSD 3
 *
 * Copyright (C) 2018 - tinoest
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');


function loadTPModuleLanguage()
{
    global $txt, $boarddir;

    $filePath = $boarddir.'/tp-files/tp-modules/TPListImages/languages/TPListImages.english.php';

    if(is_file($filePath)) {
        require_once($filePath);
    }
}

function template_main()
{

	global $txt, $context, $boarddir, $scripturl;

    loadTPModuleLanguage();

    $ret = '';
    if(array_key_exists('listimage', $_GET)) {
	    switch($_GET['listimage']) {
		    case 'remove':
			    TPRemoveImage($_POST['image']);
			    break;
	    }
    }

    if(array_key_exists('id_member', $_POST)) {
		$ret = TPListImages($_POST['id_member']);
    }

    $users = TPMembers();

    echo '
    <div class="title_bar">
        <h3 class="titlebg">'.$txt['tp-listimage-settings'].'</h3>
    </div>
	<div class="windowbg noup padding-div">
    <form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpmod;listimage=list"  method="post" style="margin: 0px;">
        <div class="padding-div">
            <input type="hidden" name="sc" value="', $context['session_id'], '" />
            <select name="id_member">';

            foreach ( $users as $id => $name)
                echo '<option value="'.$id.'">'.$name.'</option>';

    echo '
            </select>
            <input type="submit" value="'.$txt['tp-listimage-list'].'" name="'.$txt['tp-listimage-list'].'">
        </div>
    </form>
	</div>
	    <p class="clearthefloat"></p>';

    echo $ret;
}

function TPListImages($user_id) 
{
    global $txt, $boarddir, $boardurl, $context, $scripturl;

    loadTPModuleLanguage();

    $html = '';
    // fetch all images you have uploaded
    $imgfiles = array();
    if ($handle = opendir($boarddir.'/tp-images/thumbs')) 
    {
        while (false !== ($file = readdir($handle))) 
        {
            if($file != '.' && $file !='..' && $file !='.htaccess' && substr($file, 0, strlen($user_id) + 9) == 'thumb_'.$user_id.'uid')
            {
                $imgfiles[filectime($boarddir.'/tp-images/thumbs/'.$file)] = $file;
            }
        }
        closedir($handle);
        ksort($imgfiles);
        $imgs = array_reverse($imgfiles);
    }
    $html .='
        <div class="windowbg tp_pad" style="max-height: 800px; overflow: auto;">
            <div class="tpthumb" style="padding: 4px; margin-top: 4px; overflow: auto;">';
    if(!empty($imgs)) {
        foreach($imgs as $im) {

            $html .= '<form class="tborder" accept-charset="'.$context['character_set'].'" name="TPadmin" action="' . $scripturl . '?action=tpmod;listimage=remove"  method="post" style="margin: 0px;">
                <div style="float:left; padding:1%;">
                    <input type="hidden" name="sc" value="'.$context['session_id'].'" />
                    <input type="hidden" name="id_member" value="'.$user_id.'" />
                    <input type="hidden" name="image" value="'.substr($im,6).'" />
                    <div style="width: 160px; height: 180px;"><img src="'.$boardurl.'/tp-images/'.substr($im,6). '"  border="none" alt="" /><br>
                    <input type="submit" value="'.$txt['tp-listimage-remove'].'" name="'.$txt['tp-listimage-remove'].'"><br ></div>
                </div>
            </form>';
        }

    }
    $html .= '  
			</div>
		<div class="padding-div"></div>
	</div>';

    return $html;
}

function TPRemoveImage( $image ) 
{
    global $boarddir;

    $fileNameThumb  = $boarddir.'/tp-images/thumbs/thumb_'.$image;
    $fileName       = $boarddir.'/tp-images/'.$image;
    if(file_exists($fileNameThumb)) {
        unlink($fileNameThumb);
    }

    if(file_exists($fileName)) {
        unlink($fileName);
    }
}

function TPMembers()
{
	global $smcFunc, $boarddir, $txt;

	$users	= array();
    if ($handle = opendir($boarddir.'/tp-images/thumbs')) {
        while (false !== ($file = readdir($handle))) {
            if($file != '.' && $file !='..' && $file !='.htaccess') {
                if(preg_match('/thumb_(.*)uid/', $file, $matches)) {
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
		$member	= $smcFunc['db_fetch_assoc']($data)['member_name'];
		if(!is_null($member)) {
			$users[$user_id] = $member;
		}
        else {
            $users[$user_id] = $txt['tp-listimage-username'].' '.$user_id;
        }
		$smcFunc['db_free_result']($data);
	}

	return $users;
}

?>
