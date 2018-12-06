<?php
/**
 * Handles all TPBase operations
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


class TPBase 
{
	protected $dB = null;

	function __construct()
	{

		if(is_null($this->dB)) {
			$this->dB = new TPortalDB();
		}

	}


}

?>
