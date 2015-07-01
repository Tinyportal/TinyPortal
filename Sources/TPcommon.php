<?php
/**
 * @package TinyPortal
 * @version 1.1
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
	// find out how big the picture is
	list($src_width, $src_height) = getimagesize($picture);

	// if the desired width > original, keep it
	if($width > $src_width)
	{
		$width = $src_width;
		$height = $src_height;
	}

	if($src_width > $src_height)
	{
		$ratio = $height / $src_height;
		$nheight = $height;
		$nwidth = $src_width * $ratio;
	}
	elseif($src_width < $src_height)
	{
		$ratio = $width / $src_width;
		$nwidth = $width;
		$nheight = $src_height * $ratio;
	}
	else
	{
		$nheight = $height;
		$nwidth = $width;
	}
	$dest = ImageCreateTrueColor($width, $height);
	$dest2 = ImageCreateTrueColor($nwidth, $nheight);
	
	// determine format
	$format = strtolower(substr($picture, strlen($picture)-3, 3));
	if(!in_array($format, array('jpg', 'gif', 'png')))
		return;

	// go ahead
	if($format == 'jpg')
		$source = imagecreatefromjpeg($picture);
	elseif($format == 'gif')
		$source = imagecreatefromgif($picture);
	elseif($format == 'png')
		$source = imagecreatefrompng($picture);
	
	imagecopyresampled ($dest2, $source, 0 , 0, 0, 0, $nwidth, $nheight, $src_width, $src_height);
	imagecopymerge ($dest, $dest2, 0, 0, 0, 0, $nwidth, $nheight, 100);
	
	if($format == 'jpg')
		imagejpeg($dest, $thumb, 85);
	elseif($format == 'gif')
		imagegif($dest, $thumb);
	elseif($format == 'png')
		imagepng($dest, $thumb);

	// Free the memory.
	imagedestroy($dest);
	imagedestroy($dest2);
	imagedestroy($source);
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