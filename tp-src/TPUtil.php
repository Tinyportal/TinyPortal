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

	public static function __callStatic($call, $vars) {{{
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

	}}}

    public static function http_parse_query($queryString, $argSeparator = '&', $decType = PHP_QUERY_RFC1738) {{{
        $result             = array();
        $parts              = explode($argSeparator, $queryString);

        foreach ($parts as $part) {
            list($paramName, $paramValue)   = explode('=', $part, 2);

            switch ($decType) {
                case PHP_QUERY_RFC3986:
                    $paramName      = rawurldecode($paramName);
                    $paramValue     = rawurldecode($paramValue);
                    break;

                case PHP_QUERY_RFC1738:
                default:
                    $paramName      = urldecode($paramName);
                    $paramValue     = urldecode($paramValue);
                    break;
            }


            if (preg_match_all('/\[([^\]]*)\]/m', $paramName, $matches)) {
                $paramName      = substr($paramName, 0, strpos($paramName, '['));
                $keys           = array_merge(array($paramName), $matches[1]);
            }
            else {
                $keys           = array($paramName);
            }

            $target             = &$result;

            foreach ($keys as $index) {
                if ($index === '') {
                    if (isset($target)) {
                        if (is_array($target)) {
                            $intKeys    = array_filter(array_keys($target), 'is_int');
                            $index      = count($intKeys) ? max($intKeys)+1 : 0;
                        } 
                        else {
                            $target     = array($target);
                            $index      = 1;
                        }
                    } 
                    else {
                        $target         = array();
                        $index          = 0;
                    }
                } 
                elseif (isset($target[$index]) && !is_array($target[$index])) {
                    $target[$index] = array($target[$index]);
                }

                $target         = &$target[$index];
            }

            if (is_array($target)) {
                $target[]   = $paramValue;
            }
            else {
                $target     = $paramValue;
            }
        }

        return $result;

    }}}


    public static function shortenString(&$string, $length) {{{

        if (!empty($length) && self::strlen($string) > $length) {
            $cutOffPos  = max(strpos($string, ' ', $length), strpos($string, '>', $length));
            $tmpString  = self::substr($string, 0, $cutOffPos);
            //$tmpString  = preg_replace("~^(.{1,$cutOffPos})(\s.*|$)~s", '\\1...', $string);

            // check we haven't cut any bbcode off
            if(preg_match('/.*\[([^]]+)\]/', $tmpString, $matches) > 0 ) {
                // Get the bbcode tag
                $search     = '/'.substr($matches[1], 0, strpos($matches[1], ' ')).']';
                if(strstr($matches[0], $search) === false) {
                    $strEnd     = strpos($string, $search, strlen($tmpString));
                    if($strEnd != 0) {
                        $tmpString  = self::substr($string, 0, $strEnd + strlen($search));
                    }
                }   
            }

            // check that no html has been cut off
            if(preg_match('/.*\<([^]]+)\>/', $tmpString, $matches) > 0 ) {
                if(strpos($matches[1], 'br') === false) {
                    // Get the html tag
                    $search     = '/'.substr($matches[1], 0, strpos($matches[1], ' ')).'>';
                    if(strstr($matches[0], $search) === false) {
                        $strEnd     = strpos($string, $search, strlen($tmpString));
                        if($strEnd != 0) {
                            $tmpString  = self::substr($string, 0, $strEnd + strlen($search));
                        }
                    }
                } 
            }

            $string = $tmpString;
            return true;
        }

        return false;

    }}}

    public static function isHTML( $string ) {{{
        return preg_match("~\/[a-z]*>~i", $string ) != 0;
    }}}

    public static function hasLinks($string) {{{

        if(empty($string)) {
            return false;
        }

        $pattern = '%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i';
        if (preg_match_all($pattern, $string, $matches, PREG_PATTERN_ORDER)) {
            return true;
        }

        return false;

    }}}

    public static function xssClean( $string ) {{{

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

    }}}

    public static function filter($key, $type, $filterType = 'string', $options = array()) {{{
        
        switch($type) {
            case 'get':
                $data = $_GET;
                break;
            case 'post':
                $data = $_POST;
                break;
            case 'request':
                $data = $_REQUEST;
                break;
            default:
                return false;
                break;
        }

        if(!array_key_exists($key, $data)) {
            return false;
        }

        return filter_var($data[$key], self::filterType($filterType), $options);
    }}}

    private static function filterType($type) {{{
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
    }}}

}

?>
