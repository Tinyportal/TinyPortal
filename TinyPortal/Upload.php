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

    public static function getInstance() {{{
	
    	if(self::$_instance == null) {
			self::$_instance = new self();
		}
	
    	return self::$_instance;
	
    }}}

    // Empty Clone method
    private function __clone() { }

    private function set_error( int $err_num ) {{{

        $errors[] = $err_num;

    }}}

    public function get_error( void ) {{{

        return $errros;

    }}}

    public function clear_error( void ) {{{

        $errors = array();

    }}}

    public function set_mime_types( array $mime_types, bool $reset = FALSE ) {{{

        if($reset === FALSE) {
            $allowed_mime_types = array_merge($allowed_mime_types, $mime_types);
        }
        else {
            $allowed_mime_types = $mime_types;
        }

    }}}

    public function set_max_file_size( int $file_size ) {{{
    
        $max_file_size = $file_size;

    }}}

    public function set_allowed_chars( string $chars ) {{{

        $allowed_chars = $chars;

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
        
        if(in_array($mime_type, $allowed_mime_types) {
            return TRUE;
        }

        return FALSE;
    }}}

    public function check_file_size( string $filename ) {{{

        

    }}}

    public function check_filename( string $filename ) {{{

        return preg_replace('/[^'.self::allowed_chars.']/i', "_", $filename)

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
        if(!is_dir($destination)) {
            self::set_error(104);
            return FALSE;
        }

        if(!is_writeable($destination)) {
            self::set_error(105);
            return FALSE;
        }

        return move_uploaded_file($source, $destination);

    }}}

    public function move_file( string $source, string $destination ) {{{


    }}}

    public function remove_file( string $filename ) {{{


    }}}

}

?>
