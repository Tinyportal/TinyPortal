<?php
/**
 * install.php
 *
 * @package TinyPortal
 * @version 2.0.0
 * @author tino - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2018 - The TinyPortal Team
 *
 */

define('TP_MINIMUM_PHP_VERSION', '5.4.0');

global $boarddir, $boardurl;

if(!defined('SMF') && file_exists('SSI.php')) {
	require_once('SSI.php');
	$manual = true;
}
elseif(!defined('SMF')) {
	die('<strong>Install Error:</strong> - please verify you put this file the same directory as SMF\'s index.php.');
}

echo '<html>';
$src_dir    = $boarddir.'/tp-images';
$dest_dir   = $boarddir.'/tp-files/tp-images';

moveTPFiles($src_dir, $dest_dir);
if(isset($_GET['remove'])) {
    removeTPFiles($src_dir);
}

$src_dir    = $boarddir.'/tp-downloads';
$dest_dir   = $boarddir.'/tp-files/tp-downloads';

moveTPFiles($src_dir, $dest_dir);
if(isset($_GET['remove'])) {
    removeTPFiles($src_dir);
}


echo 'File move completed. Please click this link to go to the <a href="'.$boardurl.'"> forum. </a> <br >';
echo '</html>';

function moveTPFiles($src_dir, $dest_dir) 
{

    if (is_dir($src_dir)) {
        if (is_dir($dest_dir)) {
            if (is_writable($dest_dir)) {
                if ($handle = opendir($src_dir)) {
                    while (false !== ($file = readdir($handle))) {
                        if($file != '.' && $file != '..') {
                            if (is_file($src_dir . '/' . $file)) {
                                rename($src_dir . '/' . $file, $dest_dir . '/' . $file);
                            }
                            elseif(is_dir($src_dir. '/' . $file)) {
                                moveTPFiles($src_dir. '/' . $file, $dest_dir. '/' . $file);
                            }
                        }
                    }
                    closedir($handle);
                }
                else {
                    echo "$src_dir could not be opened. <br >";
                }
            }
            else {
                echo "$dest_dir is not writable! <br >";
            }
        }
        else {
            echo "$dest_dir is not a directory! <br >";
        }
    }
    else {
        echo "$dest_dir does not exist <br >";
    }
}

function removeTPFiles( $remove_dir )
{

    if (is_dir($remove_dir)) {
        $objects = scandir($remove_dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($remove_dir."/".$object) == "dir") {
                    removeTPFiles($remove_dir."/".$object); 
                }
                else {
                    unlink($dir."/".$object);
                }
            }
        }
        reset($objects);
        rmdir($remove_dir);
    }

}
