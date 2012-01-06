<?php
/****************************************************************************
* TPcommon.php																*
*****************************************************************************
* TP version: 1.0 RC3														*
* Software Version:				SMF 2.0										*
* Founder:						Bloc (http://www.blocweb.net)				*
* Developer:					IchBin (ichbin@ichbin.us)					*
* Copyright 2005-2012 by:     	The TinyPortal Team							*
* Support, News, Updates at:  	http://www.tinyportal.net					*
****************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

function tp_rating_init()
{
	global $context, $boardurl;

	$context['html_headers'] .= '
	<script type="text/javascript" src="' . $boardurl . '/tp-files/tp-plugins/javascript/mootools.js"></script>
	<script language="JavaScript" type="text/javascript">
	window.addEvent(\'domready\', function(){
		// First Example
		var el = $(\'myElement\'),
			vote = $(\'myvote\');
		
		// Create the new slider instance
		new Slider(el, el.getElement(\'.knob\'), {
			steps: 10,	// There are 35 steps
			range: [0],	// Minimum value is 8
			onChange: function(value){
				// Everytime the value changes, we change the font of an element
				vote.value=value;
			}
		}).set(vote.value.toInt());
	});	
	</script>';
}

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

function UnsharpMask($simg, $amount, $radius, $threshold)    
{ 

////////////////////////////////////////////////////////////////////////////////////////////////  
////  
////                  Unsharp Mask for PHP - version 2.1.1  
////  
////    Unsharp mask algorithm by Torstein Hønsi 2003-07.  
////             thoensi_at_netcom_dot_no.  
////               Please leave this notice.  
////  
///////////////////////////////////////////////////////////////////////////////////////////////  



    // $img is an image that is already created within php using 
    // imgcreatetruecolor. No url! $img must be a truecolor image. 

    // Attempt to calibrate the parameters to Photoshop: 
    if ($amount > 500)
		$amount = 500; 
    $amount = $amount * 0.016; 
    if ($radius > 50)
		$radius = 50; 
    $radius = $radius * 2; 
    if ($threshold > 255)
		$threshold = 255; 
     
    $radius = abs(round($radius));     // Only integers make sense. 
    if ($radius == 0) { 
        return $img; 
		imagedestroy($img); 
		break;
	} 
    $w = imagesx($img); 
	$h = imagesy($img); 
    $imgCanvas = imagecreatetruecolor($w, $h); 
    $imgBlur = imagecreatetruecolor($w, $h); 
     
    // Gaussian blur matrix: 
    //                         
    //    1    2    1         
    //    2    4    2         
    //    1    2    1         
    //                         
    ////////////////////////////////////////////////// 
         

    if (function_exists('imageconvolution')) { // PHP >= 5.1  
            $matrix = array(  
				array( 1, 2, 1 ),  
				array( 2, 4, 2 ),  
				array( 1, 2, 1 )  
        	);  
        imagecopy ($imgBlur, $img, 0, 0, 0, 0, $w, $h); 
        imageconvolution($imgBlur, $matrix, 16, 0);  
    }  
    else {  

    // Move copies of the image around one pixel at the time and merge them with weight 
    // according to the matrix. The same matrix is simply repeated for higher radii. 
        for ($i = 0; $i < $radius; $i++)    { 
            imagecopy ($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left 
            imagecopymerge ($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right 
            imagecopymerge ($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center 
            imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h); 

            imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333 ); // up 
            imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down 
        } 
    } 

    if($threshold>0){ 
        // Calculate the difference between the blurred pixels and the original 
        // and set the pixels 
        for ($x = 0; $x < $w-1; $x++) { // each row
            for ($y = 0; $y < $h; $y++) { // each pixel 
                     
                $rgbOrig = ImageColorAt($img, $x, $y); 
                $rOrig = (($rgbOrig >> 16) & 0xFF); 
                $gOrig = (($rgbOrig >> 8) & 0xFF); 
                $bOrig = ($rgbOrig & 0xFF); 
                 
                $rgbBlur = ImageColorAt($imgBlur, $x, $y); 
                 
                $rBlur = (($rgbBlur >> 16) & 0xFF); 
                $gBlur = (($rgbBlur >> 8) & 0xFF); 
                $bBlur = ($rgbBlur & 0xFF); 
                 
                // When the masked pixels differ less from the original 
                // than the threshold specifies, they are set to their original value. 
                $rNew = (abs($rOrig - $rBlur) >= $threshold)  
                    ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))  
                    : $rOrig; 
                $gNew = (abs($gOrig - $gBlur) >= $threshold)  
                    ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))  
                    : $gOrig; 
                $bNew = (abs($bOrig - $bBlur) >= $threshold)  
                    ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))  
                    : $bOrig; 
                 
                 
                             
                if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) { 
                        $pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew); 
                        ImageSetPixel($img, $x, $y, $pixCol); 
                } 
            } 
        } 
    } 
    else{ 
        for ($x = 0; $x < $w; $x++) { // each row 
            for ($y = 0; $y < $h; $y++) { // each pixel 
                $rgbOrig = ImageColorAt($img, $x, $y); 
                $rOrig = (($rgbOrig >> 16) & 0xFF); 
                $gOrig = (($rgbOrig >> 8) & 0xFF); 
                $bOrig = ($rgbOrig & 0xFF); 
                 
                $rgbBlur = ImageColorAt($imgBlur, $x, $y); 
                 
                $rBlur = (($rgbBlur >> 16) & 0xFF); 
                $gBlur = (($rgbBlur >> 8) & 0xFF); 
                $bBlur = ($rgbBlur & 0xFF); 
                 
                $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig; 
                    if($rNew>255){$rNew=255;} 
                    elseif($rNew<0){$rNew=0;} 
                $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig; 
                    if($gNew>255){$gNew=255;} 
                    elseif($gNew<0){$gNew=0;} 
                $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig; 
                    if($bNew>255){$bNew=255;} 
                    elseif($bNew<0){$bNew=0;} 
                $rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew; 
                    ImageSetPixel($img, $x, $y, $rgbNew); 
            } 
        } 
    } 
    imagedestroy($imgCanvas); 
    imagedestroy($imgBlur); 
    return $simg; 
}

function TPuploadpicture($what, $prefix, $maxsize='1800', $exts='jpg,gif,png', $destdir = 'tp-images')
{
	global $context, $settings, $boardurl, $boarddir;

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
	global $context, $db_prefix, $txt, $smcFunc;

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