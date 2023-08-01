<?php
/**
 * @package TinyPortal
 * @version 3.0.0
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
use \TinyPortal\Util as TPUtil;
use \TinyPortal\Upload as TPUpload;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

function TPCommonActions(&$subActions) {{{

    $subActions = array_merge(
        array (
            'upshrink'      => array('TPcommon.php', 'upshrink', array()),
        ),
        $subActions
    );

}}}

function tp_createthumb($picture, $width, $height, $thumb) {{{

	//code modified from http://www.akemapa.com/2008/07/10/php-gd-resize-transparent-image-png-gif/
	//Check if GD extension is loaded
	if (!extension_loaded('gd') && !extension_loaded('gd2')) {
		trigger_error("GD is not loaded", E_USER_WARNING);
		return false;
	}

	//Get Image size info
	$pictureInfo = getimagesize($picture);
	switch ($pictureInfo[2]) {
		case 1: $im = imagecreatefromgif($picture); break;
		case 2: $im = imagecreatefromjpeg($picture);  break;
		case 3: $im = imagecreatefrompng($picture); break;
		default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
	}

	//If image dimension is smaller, do not resize
	if ($pictureInfo[0] <= $width && $pictureInfo[1] <= $height) {
		$nHeight = $pictureInfo[1];
		$nWidth = $pictureInfo[0];
	}
	else {
		//yeah, resize it, but keep it proportional
		if ($width/$pictureInfo[0] > $height/$pictureInfo[1]) {
			$nWidth = $width;
			$nHeight = $pictureInfo[1]*($width/$pictureInfo[0]);
		}
		else {
			$nWidth = $pictureInfo[0]*($height/$pictureInfo[1]);
			$nHeight = $height;
		}
	}

	$nWidth     = round($nWidth);
	$nHeight    = round($nHeight);

	$newpicture = imagecreatetruecolor($nWidth, $nHeight);

	/* Check if this image is PNG or GIF, then set if Transparent*/
	if(($pictureInfo[2] == 1) OR ($pictureInfo[2]==3)) {
		imagealphablending($newpicture, false);
		imagesavealpha($newpicture,true);
		$transparent = imagecolorallocatealpha($newpicture, 255, 255, 255, 127);
		imagefilledrectangle($newpicture, 0, 0, $nWidth, $nHeight, $transparent);
	}
	imagecopyresampled($newpicture, $im, 0, 0, 0, 0, $nWidth, $nHeight, $pictureInfo[0], $pictureInfo[1]);

	//Generate the file, and rename it to $thumb
	switch ($pictureInfo[2]) {
		case 1: imagegif($newpicture,$thumb); break;
		case 2: imagejpeg($newpicture,$thumb);  break;
		case 3: imagepng($newpicture,$thumb); break;
		default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
	}

	return $thumb;
}}}

function TPuploadpicture($widthhat, $prefix, $maxsize='1800', $exts='jpg,gif,png', $destdir = 'tp-images') {{{
	global $boarddir, $txt;

	loadLanguage('TPdlmanager');

    $upload = TPUpload::getInstance();

    if(!is_null($maxsize)) {
        $upload->set_max_file_size($maxsize);
    }

    if(is_null($exts)) {
        $exts = array('jpg', 'gif', 'png');
    }
    elseif(is_string($exts)) {
        $exts = explode(',', $exts);
    }
    $upload->set_mime_types($exts);

	// add prefix
    $name   = $_FILES[$widthhat]['name'];
    $name   = $upload->check_filename($name);
	$sname  = $prefix.$name;

    if(is_dir($destdir)) {
        $dstPath = $destdir . '/' . $sname;
    }
    else {
        $dstPath = $boarddir . '/'. $destdir .'/' . $sname;
    }

    if($upload->check_file_exists($dstPath)) {
        $dstPath = dirname($dstPath) . '/' . $prefix . $upload->generate_filename(dirname($dstPath)) . $sname;
    }

    if($upload->upload_file($_FILES[$widthhat]['tmp_name'], $dstPath) === FALSE) {
		unlink($_FILES[$widthhat]['tmp_name']);
        $error_string = sprintf($txt['tp-dlnotuploaded'], $upload->get_error(TRUE));
		fatal_error($error_string, false);
    }

	return basename($dstPath);
}}}

function tp_groups() {{{
	global $txt, $smcFunc;

	// get all membergroups for permissions
	$grp    = array();
	$grp[]  = array(
		'id' => '-1',
		'name' => $txt['tp-guests'],
		'posts' => '-1'
	);
	$grp[]  = array(
		'id' => '0',
		'name' => $txt['tp-ungroupedmembers'],
		'posts' => '-1'
	);

	$request =  $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}membergroups
		WHERE 1=1 ORDER BY id_group'
	);
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$grp[] = array(
			'id' => $row['id_group'],
			'name' => $row['group_name'],
			'posts' => $row['min_posts']
		);
	}
	return $grp;
}}}

function upshrink() {{{

    global $settings;

    if(isset($_GET['id']) && isset($_GET['state'])) {
        $blockid    = TPUtil::filter('id', 'get', 'string');
        $state      = TPUtil::filter('state', 'get', 'string');
        if(isset($_COOKIE['tp-upshrinks'])) {
            $shrinks = explode(',', $_COOKIE['tp-upshrinks']);
            if($state == 0 && !in_array($blockid, $shrinks)) {
                $shrinks[] = $blockid;
            }
            elseif($state == 1 && in_array($blockid, $shrinks)) {
                $spos = array_search($blockid, $shrinks);
                if($spos > -1) {
                    unset($shrinks[$spos]);
                }
            }
            $newshrink = implode(',', $shrinks);
            setcookie ('tp-upshrinks', $newshrink , time()+7776000);
        }
        else {
            if($state == 0) {
                setcookie ('tp-upshrinks', $blockid, (time()+7776000));
            }
        }
        // Don't output anything...
        $tid = time();
        redirectexit($settings['images_url'] . '/blank.png?ti='.$tid);
    }
    else {
        redirectexit();
    }

}}}

?>
