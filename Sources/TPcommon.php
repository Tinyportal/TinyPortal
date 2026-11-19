<?php
/**
 * @package TinyPortal
 * @version 3.0.5
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
use TinyPortal\Util as TPUtil;
use TinyPortal\Upload as TPUpload;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

function TPCommonActions(&$subActions)
{
	$subActions = array_merge(
		[
			'upshrink' => ['TPcommon.php', 'upshrink', []],
		],
		$subActions
	);
}

function tp_createthumb($picture, $width, $height, $thumb)
{
	//code modified from http://www.akemapa.com/2008/07/10/php-gd-resize-transparent-image-png-gif/
	//Check if GD extension is loaded
	if (!extension_loaded('gd') && !extension_loaded('gd2')) {
		trigger_error('GD is not loaded', E_USER_WARNING);
		return false;
	}

	//Get Image size info
	$pictureInfo = getimagesize($picture);
	switch ($pictureInfo[2]) {
		case 1: $im = imagecreatefromgif($picture); break;
		case 2: $im = imagecreatefromjpeg($picture); break;
		case 3: $im = imagecreatefrompng($picture); break;
		default:  trigger_error('Unsupported filetype!', E_USER_WARNING); break;
	}

	//If image dimension is smaller, do not resize
	if ($pictureInfo[0] <= $width && $pictureInfo[1] <= $height) {
		$nHeight = $pictureInfo[1];
		$nWidth = $pictureInfo[0];
	}
	else {
		//yeah, resize it, but keep it proportional
		if ($width / $pictureInfo[0] > $height / $pictureInfo[1]) {
			$nWidth = $width;
			$nHeight = $pictureInfo[1] * ($width / $pictureInfo[0]);
		}
		else {
			$nWidth = $pictureInfo[0] * ($height / $pictureInfo[1]);
			$nHeight = $height;
		}
	}

	$nWidth = round($nWidth);
	$nHeight = round($nHeight);

	$newpicture = imagecreatetruecolor($nWidth, $nHeight);

	/* Check if this image is PNG or GIF, then set if Transparent*/
	if (($pictureInfo[2] == 1) or ($pictureInfo[2] == 3)) {
		imagealphablending($newpicture, false);
		imagesavealpha($newpicture, true);
		$transparent = imagecolorallocatealpha($newpicture, 255, 255, 255, 127);
		imagefilledrectangle($newpicture, 0, 0, $nWidth, $nHeight, $transparent);
	}
	imagecopyresampled($newpicture, $im, 0, 0, 0, 0, $nWidth, $nHeight, $pictureInfo[0], $pictureInfo[1]);

	//Generate the file, and rename it to $thumb
	switch ($pictureInfo[2]) {
		case 1: imagegif($newpicture, $thumb); break;
		case 2: imagejpeg($newpicture, $thumb); break;
		case 3: imagepng($newpicture, $thumb); break;
		default:  trigger_error('Failed resize image!', E_USER_WARNING); break;
	}

	return $thumb;
}

function TPuploadpicture($widthhat, $prefix, $maxsize = '1800', $exts = 'jpg,gif,png', $destdir = 'tp-images')
{
	global $context, $boarddir, $txt;

	loadLanguage('TPdlmanager');

	$upload = TPUpload::getInstance();
	$upload->clear_error();

	// Validate the PHP upload before accessing its temporary file.
	if (
		!isset($_FILES[$widthhat]) ||
		!isset($_FILES[$widthhat]['tmp_name']) ||
		!isset($_FILES[$widthhat]['error']) ||
		$_FILES[$widthhat]['error'] !== UPLOAD_ERR_OK ||
		!is_uploaded_file($_FILES[$widthhat]['tmp_name'])
	) {
		fatal_error(sprintf($txt['tp-dlnotuploaded'], 101), false);
		return false;
	}

	// Image uploads require an authenticated SMF account. The caller's
	// $prefix argument is retained for API compatibility, but is not
	// trusted for the stored filename.
	if (empty($context['user']['id']) || (int) $context['user']['id'] < 1) {
		fatal_error(sprintf($txt['tp-dlnotuploaded'], 101), false);
		return false;
	}

	if (!is_null($maxsize)) {
		$upload->set_max_file_size((int) $maxsize);
	}

	// Resolve the destination using TinyPortal's existing path rules.
	if (is_dir($destdir)) {
		$directory = $destdir;
	}
	else {
		$directory = $boarddir . '/' . $destdir;
	}

	if (!is_dir($directory) || !is_writable($directory)) {
		fatal_error(sprintf($txt['tp-dlnotuploaded'], 201), false);
		return false;
	}

	// Keep the existing TinyPortal convention: user ID followed by uid.
	// The random part and extension are generated inside Upload.php.
	$user_prefix = (int) $context['user']['id'] . 'uid';

	$name = $upload->upload_image_file(
		$_FILES[$widthhat]['tmp_name'],
		$directory,
		$user_prefix
	);

	if ($name === false) {
		@unlink($_FILES[$widthhat]['tmp_name']);
		$error_string = sprintf($txt['tp-dlnotuploaded'], $upload->get_error(true));
		fatal_error($error_string, false);
		return false;
	}

	return $name;
}
function tp_groups()
{
	global $txt, $smcFunc;

	// get all membergroups for permissions
	$grp = [];
	$grp[] = [
		'id' => '-1',
		'name' => $txt['tp-guests'],
		'posts' => '-1'
	];
	$grp[] = [
		'id' => '0',
		'name' => $txt['tp-ungroupedmembers'],
		'posts' => '-1'
	];

	$request = $smcFunc['db_query'](
		'',
		'
		SELECT * FROM {db_prefix}membergroups
		WHERE 1=1 ORDER BY id_group'
	);
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$grp[] = [
			'id' => $row['id_group'],
			'name' => $row['group_name'],
			'posts' => $row['min_posts']
		];
	}
	return $grp;
}

function upshrink()
{
	global $settings;

	if (isset($_GET['id']) && isset($_GET['state'])) {
		$blockid = TPUtil::filter('id', 'get', 'string');
		$state = TPUtil::filter('state', 'get', 'string');
		if (isset($_COOKIE['tp-upshrinks'])) {
			$shrinks = explode(',', $_COOKIE['tp-upshrinks']);
			if ($state == 0 && !in_array($blockid, $shrinks)) {
				$shrinks[] = $blockid;
			}
			elseif ($state == 1 && in_array($blockid, $shrinks)) {
				$spos = array_search($blockid, $shrinks);
				if ($spos > -1) {
					unset($shrinks[$spos]);
				}
			}
			$newshrink = implode(',', $shrinks);
			setcookie('tp-upshrinks', $newshrink, time() + 7776000);
		}
		else {
			if ($state == 0) {
				setcookie('tp-upshrinks', $blockid, (time() + 7776000));
			}
		}
		// Don't output anything...
		$tid = time();
		redirectexit($settings['images_url'] . '/blank.png?ti=' . $tid);
	}
	else {
		redirectexit();
	}
}


/**
 * Store a download under an unpredictable server-generated name.
 * Downloads must also be served as attachments, with execution disabled at the web server.
 */
function TPsecureDownloadUpload($field, $directory, $maxKb, $configuredTypes)
{
    global $txt, $context;
    if (!allowedTo('tp_dlupload') && !allowedTo('tp_dlmanager')) {
        fatal_error($txt['tp-secuploadnotallowed'], false);
    }
    if (empty($context['user']['id']) || !isset($_FILES[$field]) ||
        !is_array($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK ||
        empty($_FILES[$field]['tmp_name']) || !is_uploaded_file($_FILES[$field]['tmp_name'])) {
        fatal_error($txt['tp-secuploadinvalid'], false);
    }
    $tmp = $_FILES[$field]['tmp_name'];
    $size = filesize($tmp);
    if ($size === false || $size < 1 || $size > max(1, (int) $maxKb) * 1024) {
        fatal_error($txt['tp-secuploadtoolarge'], false);
    }
    $original = (string) ($_FILES[$field]['name'] ?? '');
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    // Only inert file types; never accept script, HTML, SVG, or server configuration formats.
    $safe = ['pdf', 'txt', 'csv', 'zip', 'jpg', 'jpeg', 'png', 'gif',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'rtf', 'mp3', 'mp4'];
    $configured = array_filter(array_map('trim', explode(',', strtolower((string) $configuredTypes))));
    if (!$ext || !in_array($ext, $safe, true) || !in_array($ext, $configured, true)) {
        fatal_error($txt['tp-secuploadtypenotallowed'], false);
    }
    if (!is_dir($directory) || !is_writable($directory)) {
        fatal_error($txt['tp-secuploadnodir'], false);
    }
    // Verify images really are images, and reject embedded PHP markers in any file.
    $head = file_get_contents($tmp, false, null, 0, 4096);
    if ($head === false || preg_match('/<\?(?:php|=)|<script\b/i', $head)) {
        fatal_error($txt['tp-secuploadnotsafe'], false);
    }
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
        $info = @getimagesize($tmp);
        $types = ['jpg' => IMAGETYPE_JPEG, 'jpeg' => IMAGETYPE_JPEG,
            'png' => IMAGETYPE_PNG, 'gif' => IMAGETYPE_GIF];
        if (!$info || $info[2] !== $types[$ext]) {
            fatal_error($txt['tp-secuploadinvalidimage'], false);
        }
    }
    $name = (int) $context['user']['id'] . 'uid' . bin2hex(random_bytes(16)) . '.' . $ext;
    $destination = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $name;
    if (file_exists($destination) || !move_uploaded_file($tmp, $destination)) {
        fatal_error($txt['tp-secuploadnotstored'], false);
    }
    @chmod($destination, 0644);
    return $name;
}
