<?php
/**
 * @package TinyPortal
 * @version 1.2
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2015 - The TinyPortal Team
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

function tp_createthumb($picture, $width, $height, $thumb)
{

	//code modified from http://www.akemapa.com/2008/07/10/php-gd-resize-transparent-image-png-gif/
	//Check if GD extension is loaded
	if (!extension_loaded('gd') && !extension_loaded('gd2'))
	{
		trigger_error("GD is not loaded", E_USER_WARNING);
		return false;
	}

	//Get Image size info
	$pictureInfo = getimagesize($picture);
	switch ($pictureInfo[2])
	{
		case 1: $im = imagecreatefromgif($picture); break;
		case 2: $im = imagecreatefromjpeg($picture);  break;
		case 3: $im = imagecreatefrompng($picture); break;
		default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
	}

	//If image dimension is smaller, do not resize
	if ($pictureInfo[0] <= $width && $pictureInfo[1] <= $height)
	{
		$nHeight = $pictureInfo[1];
		$nWidth = $pictureInfo[0];
	}
	else
	{
		//yeah, resize it, but keep it proportional
		if ($width/$pictureInfo[0] > $height/$pictureInfo[1]) {
			$nWidth = $width;
			$nHeight = $pictureInfo[1]*($width/$pictureInfo[0]);
		}
		else
		{
			$nWidth = $pictureInfo[0]*($height/$pictureInfo[1]);
			$nHeight = $height;
		}
	}

	$nWidth = round($nWidth);
	$nHeight = round($nHeight);

	$newpicture = imagecreatetruecolor($nWidth, $nHeight);
 
	/* Check if this image is PNG or GIF, then set if Transparent*/ 
	if(($pictureInfo[2] == 1) OR ($pictureInfo[2]==3))
	{
		imagealphablending($newpicture, false);
		imagesavealpha($newpicture,true);
		$transparent = imagecolorallocatealpha($newpicture, 255, 255, 255, 127);
		imagefilledrectangle($newpicture, 0, 0, $nWidth, $nHeight, $transparent);
	}
	imagecopyresampled($newpicture, $im, 0, 0, 0, 0, $nWidth, $nHeight, $pictureInfo[0], $pictureInfo[1]);

	//Generate the file, and rename it to $thumb
	switch ($pictureInfo[2])
	{
		case 1: imagegif($newpicture,$thumb); break;
		case 2: imagejpeg($newpicture,$thumb);  break;
		case 3: imagepng($newpicture,$thumb); break;
		default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
	}

	return $thumb;
}

function TPuploadpicture($what, $prefix, $maxsize='1800', $exts='jpg,gif,png', $destdir = 'tp-images')
{
	global $boarddir, $txt;

	loadLanguage('TPdlmanager');

	// check that nothing happended
	if(!file_exists($_FILES[$what]['tmp_name']) || !is_uploaded_file($_FILES[$what]['tmp_name']))
		fatal_error($txt['tp-dlnotuploaded']);
	// process the file
	$filename=$_FILES[$what]['name'];
	$name = strtr($filename, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
	$name = strtr($name, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
	$name = preg_replace(array('/\s/', '/[^\w_\.\-]/'), array('_', ''), $name);

	$filesize = filesize($_FILES[$what]['tmp_name']);
	if($filesize > (1024 * $maxsize))
	{
		unlink($_FILES[$what]['tmp_name']);
		fatal_error($txt['tp-dlmaxerror'] . $maxsize.' Kb.');
	}

	// check the extension
	$allowed = explode(',', $exts);
	$match = false;
	foreach($allowed as $extension => $value)
	{
		$ext = '.'.$value;
		$extlen = strlen($ext);
		if(strtolower(substr($name, strlen($name)-$extlen, $extlen)) == strtolower($ext))
			$match = true;
	}
	if(!$match)
	{
		unlink($_FILES[$what]['tmp_name']);
		fatal_error($txt['tp-dlallowedtypes'] . ': ' . $exts);
	}

	// check that no other file exists with same name
	if(file_exists($boarddir.'/'.$destdir.'/'.$name))
		$name = time().$name;
	
	// add prefix
	$sname = $prefix.$name;

	if(move_uploaded_file($_FILES[$what]['tmp_name'],$boarddir.'/'.$destdir.'/'.$sname))
		return $sname;
	else
		return;
}

function tp_groups()
{
	global $txt, $smcFunc;

	// get all membergroups for permissions
	$grp = array();
	$grp[] = array(
		'id' => '-1',
		'name' => $txt['tp-guests'],
		'posts' => '-1'
	);
	$grp[] = array(
		'id' => '0',
		'name' => $txt['tp-ungroupedmembers'],
		'posts' => '-1'
	);

	$request =  $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}membergroups 
		WHERE 1 ORDER BY id_group'
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$grp[] = array(
			'id' => $row['id_group'],
			'name' => $row['group_name'],
			'posts' => $row['min_posts']
		);
	}
	return $grp;
}
?>