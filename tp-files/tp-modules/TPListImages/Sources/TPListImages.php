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

    if(array_key_exists('listimage', $_GET)) {
	switch($_GET['listimage']) {
		case 'list';
			$html = TPListImages($_POST['id_member']);
			break;
		case 'remove':
			TPRemoveImage($_POST['image']);
			break;
	    }
    }

    if(!empty($html)) {
        echo '
        <div class="title_bar">
            <h3 class="titlebg">'.$txt['tp-listimage-settings'].'</h3>
        </div>';
        echo $html;
    }
    else {
        $users = TPMembers();

        echo '
        <div class="title_bar">
            <h3 class="titlebg">'.$txt['tp-listimage-settings'].'</h3>
        </div>
        <form class="tborder" accept-charset="', $context['character_set'], '" name="TPadmin" action="' . $scripturl . '?action=tpmod;listimage=list"  method="post" style="margin: 0px;">
            <div class="windowbg" style="padding:1%;">
                <input type="hidden" name="sc" value="', $context['session_id'], '" />
                <select name="id_member">';

                foreach ( $users as $id => $name)
                    echo '<option value="'.$id.'">'.$name.'</option>';

        echo '
                </select>
                <input type="submit" value="'.$txt['tp-listimage-list'].'" name="'.$txt['tp-listimage-list'].'">
            </div>
        </form>
        <p class="clearthefloat"></p>';
    }
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
        <div class="windowbg" style="padding: 4px; margin-top: 4px; max-height: 200px; overflow: auto;">
            <div class="tpthumb" style="padding: 4px; margin-top: 4px; overflow: auto;">';
    if(!empty($imgs)) {
        foreach($imgs as $im) {

            $html .= '<form class="tborder" accept-charset="'.$context['character_set'].'" name="TPadmin" action="' . $scripturl . '?action=tpmod;listimage=remove"  method="post" style="margin: 0px;">
                <div class="windowbg" style="padding:1%;">
                    <input type="hidden" name="sc" value="'.$context['session_id'].'" />
                    <input type="hidden" name="image" value="'.substr($im,6).'" />
                    <img src="'.$boardurl.'/tp-images/'.substr($im,6). '"  border="none" alt="" /><br >
                    <input type="submit" value="'.$txt['tp-listimage-remove'].'" name="'.$txt['tp-listimage-remove'].'"><br >
                </div>
            </form>';
        }

    }
    $html .= '  </div>
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
	global $smcFunc;

	$users	= array();
	$query	=  $smcFunc['db_query']('', '
		SELECT author_id FROM {db_prefix}tp_articles
		GROUP BY author_id'
	);
	while($row = $smcFunc['db_fetch_assoc']($query))
	{
		$data	=  $smcFunc['db_query']('', '
			SELECT member_name FROM {db_prefix}members
			WHERE id_member = {int:member_id}',
			array (
				'member_id' => $row['author_id']
			)
		);
		$member	= $smcFunc['db_fetch_assoc']($data)['member_name'];
		if(!is_null($member)) {
			$users[$row['author_id']] = $member;
		}
		$smcFunc['db_free_result']($data);
	}
	$smcFunc['db_free_result']($query);

	return $users;
}

?>
