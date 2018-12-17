<?php
/**
 * Handles all TinyPortal Util operations
 *
 * @name      	TinyPortal
 * @package 	TPBase
 * @copyright 	TinyPortal
 * @license   	MPL 1.1
 *
 * This file contains code covered by:
 * author: tinoest - https://tinoest.co.uk
 * license: BSD-3-Clause 
 *
 * @version 1.0.0
 *
 */
if (!defined('SMF')) {
	die('Hacking attempt...');
}

// Static method to call smcFunc calls which are not database related.
class TPUtil
{

	public static function __callStatic($call, $vars) {
		global $smcFunc;
		if(array_key_exists($call, $smcFunc)) {
			// It's faster to call directly, failover to call_user_func_array
			switch(count($vars)) {
				case 1:
					return $smcFunc[$call]($vars[0]);
					break;
				case 2:
					return $smcFunc[$call]($vars[0], $vars[1]);
					break;
				case 3:
					return $smcFunc[$call]($vars[0], $vars[1], $vars[2]);
					break;
				case 4:
					return $smcFunc[$call]($vars[0], $vars[1], $vars[2], $vars[3]);
					break;
				case 5:
					return $smcFunc[$call]($vars[0], $vars[1], $vars[2], $vars[3], $vars[4]);
					break;
				default:
					return call_user_func_array($smcFunc[$call], $vars);
					break;
			}
		}
		return false;
	}


    public static function shortenString($string, $length) {

        if (!empty($length) && TPUtil::strlen($string) > $length) {
            $tmpString  = TPUtil::substr($string, 0, $length);
            $lastTag    = preg_match('/.*\[([^]]+)\]/', $string);
            var_dump($lastTag);
            if(preg_match('/.*\<([^]]+)\>/', $string, $matches) > 0 ) {
                var_dump($matches);
            }

            // The first space or line break. (<br />, etc.)
            $cutoff = max(strrpos($string, ' '), strrpos($string, '>'));

            if ($cutoff !== false) {
                $string = TPUtil::substr($string, 0, $cutoff);
            }
        }

        return $string;

    }

    public static function isHTML( $string ) { 
        return preg_match("~\/[a-z]*>~i", $string ) != 0; 
    }

    public static function xssClean( $string ) {

        // URL decode
        $string = urldecode($string);
        // Convert Hexadecimals
        $string = preg_replace_callback('!(&#|\\\)[xX]([0-9a-fA-F]+);?!', function($m) {
            return chr(hexdec($m[2]));
        }, $string);
        // Clean up entities
        $string = preg_replace('!(&#0+[0-9]+)!','$1;',$string);
        // Decode entities
        $string = html_entity_decode($string, ENT_NOQUOTES, 'UTF-8');
        // Strip whitespace characters
        $string = preg_replace('!\s!','',$string);
        // Set the patterns we'll test against
        $patterns = array(
            // Match any attribute starting with "on" or xmlns
            '#(<[^>]+[\x00-\x20\"\'\/])(on|xmlns)[^>]*>?#iUu',
            // Match javascript:, livescript:, vbscript: and mocha: protocols
            '!((java|live|vb)script|mocha|feed|data):(\w)*!iUu',
            '#-moz-binding[\x00-\x20]*:#u',
            // Match style attributes
            '#(<[^>]+[\x00-\x20\"\'\/])style=[^>]*>?#iUu',
            // Match unneeded tags
            '#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>?#i'
        );

        foreach($patterns as $pattern) {
            $string = preg_replace($pattern, '', $string);
        }

        if(!empty($string)) {
            return $string;
        }
        else {
            return false;
        }
    }

    public static function filter($key, $type = 'get', $filterType = 'string', $options = array()) {
        
        switch($type) {
            case 'get':
                $data = $_GET;        
                break;
            case 'post':
                $data = $_POST;        
                break;
            default:
                return false;
                break;
        }

        if(!array_key_exists($key, $data)) {
            return false;
        }

        return filter_var($data[$key], self::filterType($filterType), $options);
    }

    private static function filterType($type) {
        switch (strtolower($type)) {
            case 'string':
                $filter = FILTER_SANITIZE_STRING;
                break;
            case 'int':
                $filter = FILTER_SANITIZE_NUMBER_INT;
                break;
            case 'float' || 'decimal':
                $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                break;
            case 'encoded':
                $filter = FILTER_SANITIZE_ENCODED;
                break;
            case 'url':
                $filter = FILTER_SANITIZE_URL;
                break;
            case 'email':
                $filter = FILTER_SANITIZE_EMAIL;
                break;
            default:
                $filter = FILTER_SANITIZE_STRING;
        }
        return $filter;
    }

}

?>
