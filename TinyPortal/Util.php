<?php
/**
 * @package TinyPortal
 * @version 2.1.0
 * @author tinoest - https://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * Handles all TinyPortal Util operations
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2020 - The TinyPortal Team
 *
 * This file contains code covered by:
 * author: tinoest - https://tinoest.co.uk
 * license: BSD-3-Clause 
 *
 */
namespace TinyPortal;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

// Static method to call smcFunc calls which are not database related.
class Util
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

    public static function find_in_set($data, $field, $arg = 'OR') {{{

        $dB = Database::getInstance();

        if( ($arg != "OR") && ($arg != "AND") ) {
            return;
        }

        array_walk($data, function (&$value, $key) use ($dB) {
                $value = $dB->db_quote('{string:value}', array( 'value' => $value));
            }
        );

        $str = '';
        if(TP_PGSQL == false) {
            if($arg == 'OR') {
                $str = '(FIND_IN_SET(' . implode(', '.$field.') '.$arg.' FIND_IN_SET(', $data) . ', '.$field.'))';
            } 
            else {
                $str = 'AND (FIND_IN_SET(' . implode(', '.$field.') OR FIND_IN_SET(', $data) . ', '.$field.'))';
            }
        }
        else {
            if($arg == 'OR') {
                foreach($data as $k => $v) {
                    $str .= ' '.$v.' = ANY (string_to_array('.$field.', \',\' ) ) '.$arg.' ';
                }
                $str = rtrim($str,' '.$arg.' ');
            }
            else {
                $str = 'AND ( '. implode('\' = ANY (string_to_array( '.$field.', \',\' )) OR \'', $data) . ' = ANY (string_to_array('.$field.', \',\')))';
            }
        }

        return $str;

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

    public static function checkboxChecked($checkbox) {{{

        return self::filter($checkbox, 'post', 'string');

    }}}

    public static function shortenString(&$string, $length) {{{

        $shorten = FALSE;

        if(!empty($length)) {
            // Remove all the entities and change them to a space..
            $string     = preg_replace('/&nbsp;|&zwnj;|&raquo;|&laquo;|&gt;/', ' ', $string);
            // Change all the new lines to \r\n
            $string     = str_ireplace(array("<br />","<br>","<br/>","<br />","&lt;br /&gt;","&lt;br/&gt;","&lt;br&gt;"), "\r\n", $string);
            
            if( self::strlen($string) > $length ) {
                $shorten    = TRUE;
                // Now we can find the closest space character
                $cutOffPos  = max(mb_strpos($string, ' ', $length), mb_strpos($string, '>', $length));
                if($cutOffPos !== false) {
                    $tmpString  = self::substr($string, 0, $cutOffPos);

                    // Find all the bbc tags then loop through finding the closing one
                    if(preg_match_all('/\[([a-zA-Z0-9_\-]+?)\]/', $tmpString, $matches) > 0 ) {
                        foreach($matches[1] as $key) { 
                            // check we haven't cut any bbcode off
                            if(preg_match_all('/\[(['.$key.']+?)\](.+?)\[\/\1\]/', $tmpString, $match, PREG_SET_ORDER) == 0 ) {
                                // Search from the old cut off position to the next similar tag
                                $cutOffPos  = mb_strpos($string, '[/'.$key.']', $cutOffPos);
                                if($cutOffPos !== false) {
                                    $tmpString  = self::substr($string, 0, $cutOffPos);
                                }
                            }
                        }
                    }

                    // check that no html has been cut off
                    if(self::isHTML($string)) {
                        // Change the newlines back to <br>
                        $string = str_ireplace("\r\n", '<br>', $string);

                        $reachedLimit   = false;
                        $totalLen       = 0;
                        $toRemove       = array();

                        $dom = new \DomDocument('1.0', 'UTF-8');

						// set error level
						$internalErrors = libxml_use_internal_errors(true);

                        $dom->loadHTML(mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8'));

						// Restore error level
						libxml_use_internal_errors($internalErrors);

                        self::walkHTML($dom, $length, $reachedLimit, $totalLen, $toRemove);

                        foreach ($toRemove as $child) {
                            $child->parentNode->removeChild($child);
                        }

                        $tmpString = $dom->saveHTML();
                        // Strip out the doctype and html body
                        if(($pos = strpos($tmpString, '<html><body>')) !== FALSE) {
                            $tmpString = substr($tmpString, $pos + 12);
                        }

                    }
                    
                    // Assign it back to the string
                    $string = $tmpString;
                }
            }

            // Change the newlines back to <br>
            $string = str_ireplace("\r\n", '<br>', $string);
        }

        return $shorten;

    }}}

    public static function walkHTML(\DomNode $node, $length, &$reachedLimit, &$totalLen, &$toRemove) {{{

        if($reachedLimit == true) {
            $toRemove[] = $node;
        } 
        else {
            if($node instanceof \DomText) {
                $nodeLen    = mb_strlen($node->nodeValue);
                $totalLen   += $nodeLen;


                if($totalLen > $length) {
                    $node->nodeValue    = mb_substr($node->nodeValue, 0, $nodeLen - ($totalLen - $length));
                    $reachedLimit       = true;
                }
            }

            if(isset($node->childNodes)) {
                foreach ($node->childNodes as $child) {
                    self::walkHTML($child, $length, $reachedLimit, $totalLen, $toRemove);
                }
            }
        }

        return;
    }}}

    public static function parseBBC($string) {{{

        if(preg_match_all('/\[([a-zA-Z=0-9_\-]+?)\](.+?)\[\/\1\]/', $string, $matches) > 0 ) {
            return $matches;
        }

        return false;

    }}} 

    public static function isHTML( $string ) {{{

        // Remove any HTML which might be in bbc html tags for this check, this means bbc with html will break the shortenString function
        $string = preg_replace('/\[([html]+?)\](.+?)\[\/\1\]/', '', $string);

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
