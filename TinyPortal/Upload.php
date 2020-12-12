<?php
/**
 * Handles all TinyPortal Upload operations
 *
 * @name      	TinyPortal
 * @package 	Upload
 * @copyright 	TinyPortal
 * @license   	MPL 1.1
 *
 * This file contains code covered by:
 * author: tinoest - https://tinoest.co.uk
 * license: BSD-3-Clause 
 *
 * @version 2.1.0
 *
 */
namespace TinyPortal;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

class Upload
{
    private static $_instance   = null;
    private $allowed_mime_types = array();
    private $errors             = array();
    private $max_file_size      = 1024;
    private $allowed_chars      = "a-z0-9_.-";
    private $mime_types         = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    public static function getInstance() {{{
	
    	if(self::$_instance == null) {
			self::$_instance = new self();
		}
	
    	return self::$_instance;
	
    }}}

    // Empty Clone method
    private function __clone() { }

    private function set_error( int $err_num ) {{{

        $this->errors[] = $err_num;

    }}}

    public function get_error( bool $last = FALSE ) {{{

        if($last === TRUE) {
            return end($this->errors);
        }
        else {
            return $this->errors;
        }

    }}}

    public function clear_error( ) {{{

        $this->errors = array();

    }}}

    public function set_mime_types( array $mime_types, bool $reset = FALSE ) {{{

        if($reset === TRUE) {
            $this->allowed_mime_types = array();
        }

        foreach($mime_types as $type) {
            if(array_key_exists($type, $this->mime_types)) {
                $this->allowed_mime_types[] = $this->mime_types[$type];
            }
        }

    }}}

    public function set_max_file_size( int $file_size ) {{{
    
        $this->max_file_size = $file_size;

    }}}

    public function set_allowed_chars( string $chars ) {{{

        $this->allowed_chars = $chars;

    }}}

    public function check_mime_type( string $filename ) {{{

        $mime_type = '';

        if(function_exists('finfo_open')) {
            $finfo      = finfo_open(FILEINFO_MIME);
            $mime_type  = finfo_file($finfo, $filename);
            finfo_close($finfo);
        } 
        elseif(function_exists('mime_content_type')) {
            $mime_type = mime_content_type($filename);
        }

        if(!empty($mime_type) && strpos($mime_type, ';')) {
            list($mime_type, ) = explode(';', $mime_type);
        }

        if(in_array($mime_type, $this->allowed_mime_types)) {
            return TRUE;
        }

        return FALSE;
    }}}

    public function check_file_size( string $filename ) {{{

        

    }}}

    public function check_filename( string $filename ) {{{

        return preg_replace('/[^'.$this->allowed_chars.']/i', "_", $filename);

    }}}

    public function upload_file( string $source, string $destination ) {{{

        // Check File Exists
        if(!file_exists($source)) {
            self::set_error(100);
            return FALSE;
        }

        // Check File was uploaded 
        if(!is_uploaded_file($source)) {
            self::set_error(101);
            return FALSE;
        }

        if(!self::check_filename($source)) {
            self::set_error(102);
            return FALSE;
        }

        // Check mime type is allowed
        if(!self::check_mime_type($source)) {
            self::set_error(103);
            return FALSE;
        }

        // Check destination exists
        if(!is_dir(dirname($destination))) {
            self::set_error(104);
            return FALSE;
        }

        if(!is_writeable(dirname($destination))) {
            self::set_error(105);
            return FALSE;
        }

        if(is_file($destination)) {
            self::set_error(106);
            return FALSE;
        }

        return move_uploaded_file($source, $destination);

    }}}

    public function move_file( string $source, string $destination ) {{{


    }}}

    public function remove_file( string $filename ) {{{

        if(!file_exists($filename)) {
            self::set_error(801);
            return FALSE;
        }

        if(!is_writable($filename)) {
            self::set_error(802);
            return FALSe;
        }

        if(unlink($filename) == FALSE) {
            self::set_error(803);
            return FALSE;
        }

        return TRUE;
    }}}

}

?>
