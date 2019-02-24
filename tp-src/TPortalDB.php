<?php
/**
 * Handles all TinyPortal Database operations
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

class TPortalDB 
{
	public function __call($call, $vars) {{{
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
}

?>
