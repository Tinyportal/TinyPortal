<?php
/**
 * @package TinyPortal
 * @version 2.0.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2019 - The TinyPortal Team
 *
 */

if (!defined('SMF'))
        die('Hacking attempt...');

// TinyPortal module entrance
function TPmodules()
{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

	if(loadLanguage('TPmodules') == false)
		loadLanguage('TPmodules', 'english');
	if(loadLanguage('TPortalAdmin') == false)
		loadLanguage('TPortalAdmin', 'english');

	// get subaction
	$tpsub = '';
	if(isset($_GET['sa'])) {
		$context['TPortal']['subaction'] = $_GET['sa'];
		$tpsub = $_GET['sa'];
	}
	elseif(isset($_GET['sub'])) {
		$context['TPortal']['subaction'] = $_GET['sub'];
		$tpsub = $_GET['sub'];
	}

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// call the editor setup
	require_once($sourcedir. '/TPcommon.php');

	// download manager?
	if(isset($_GET['dl'])) {
		$context['TPortal']['dlsub'] = $_GET['dl'] == '' ? '0' : $_GET['dl'];
	}

	// clear the linktree first
	TPstrip_linktree();

	// include source files in case of modules
	if(isset($context['TPortal']['dlsub'])) {
		require_once( $sourcedir .'/TPdlmanager.php');
		TPdlmanager_init();
	}
	elseif(isset($_GET['getsnippets'])) {
		get_snippets_xml();
	// save the upshrink value
    }
	elseif(isset($_GET['upshrink']) && isset($_GET['state'])) {
		$blockid = $_GET['upshrink'];
		$state = $_GET['state'];
		if(isset($_COOKIE['tp-upshrinks'])) {
			$shrinks = explode(',', $_COOKIE['tp-upshrinks']);
			if($state == 0 && !in_array($blockid, $shrinks)) {
				$shrinks[] = $blockid;
            }
			elseif($state == 1 && in_array($blockid, $shrinks)) {
				$spos = array_search($blockid, $shrinks);
				if($spos > -1) {
					unset($shrinks[$spos]);
                }
			}
			$newshrink = implode(',', $shrinks);
			setcookie ('tp-upshrinks', $newshrink , time()+7776000);
		}
		else {
			if($state == 0) {
			    setcookie ('tp-upshrinks', $blockid, (time()+7776000));
            }
		}
		// Don't output anything...
		$tid = time();
		redirectexit($settings['images_url'] . '/blank.gif?ti='.$tid);
	}
	elseif($tpsub == 'updatelog') {
		$context['TPortal']['subaction'] = 'updatelog';
		$request = $smcFunc['db_query']('', '
            SELECT value1 FROM {db_prefix}tp_variables
            WHERE type = {string:type} ORDER BY id DESC',
            array('type' => 'updatelog')
        );
		if($smcFunc['db_num_rows']($request) > 0) {
			$check = $smcFunc['db_fetch_assoc']($request);
			$context['TPortal']['updatelog'] = $check['value1'];
			$smcFunc['db_free_result']($request);
		}
		else {
			$context['TPortal']['updatelog'] = "";
        }

		loadtemplate('TPmodules');
		$context['sub_template'] = 'updatelog';
	}
	elseif($tpsub == 'savesettings' ) {
		// check the session
		checkSession('post');
		if(isset($_POST['item'])) {
			$item = $_POST['item'];
        }
		else {
			$item = 0;
        }

		if(isset($_POST['memberid'])) {
			$mem = $_POST['memberid'];
        }
		else {
			$mem = 0; 
        }

		if(!isset($mem) || (isset($mem) && !is_numeric($mem))) {
			fatalerror('Member doesn\'t exist.');
        }

		foreach($_POST as $what => $value) {
			if($what == 'tpwysiwyg' && $item > 0) {
				 $smcFunc['db_query']('', '
                     UPDATE {db_prefix}tp_data
                     SET value = {int:val} WHERE id = {int:id}',
                     array('val' => $value, 'id' => $item)
		 	    );
			}
			elseif($what == 'tpwysiwyg' && $item == 0) {
				 $smcFunc['db_insert']('INSERT',
				 	'{db_prefix}tp_data',
					 array('type' => 'int', 'id_member' => 'int', 'value' => 'int'),
					 array(2, $mem, $value),
					 array('id')
				 );
            }
		}
		// go back to profile page
		redirectexit('action=profile;u='.$mem.';area=tparticles;sa=settings');
	}
	elseif($tpsub == 'rate_dlitem' && isset($_POST['tp_dlitem_rating_submit']) && $_POST['tp_dlitem_type'] == 'dlitem_rating') {
		// check the session
		checkSession('post');

		$commenter = $context['user']['id'];
		$dl = $_POST['tp_dlitem_id'];
		// check if the download indeed exists
		$request = $smcFunc['db_query']('', '
			SELECT rating, voters FROM {db_prefix}tp_dlmanager
			WHERE id = {int:dlid}',
			array('dlid' => $dl)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);

			$voters = array();
			$ratings = array();
			$voters = explode(',', $row[1]);
			$ratings = explode(',', $row[0]);
			// check if we haven't rated anyway
			if(!in_array($context['user']['id'],$voters))
			{
				if($row[0] != '')
				{
					$new_voters = $row[1].','.$context['user']['id'];
					$new_ratings = $row[0].','.$_POST['tp_dlitem_rating'];
				}
				else
				{
					$new_voters = $context['user']['id'];
					$new_ratings = $_POST['tp_dlitem_rating'];
				}
				// update ratings and raters
				$smcFunc['db_query']('', '
				 	UPDATE {db_prefix}tp_dlmanager
					SET rating = {string:rate}
					WHERE id = {int:dlid}',
					array('rate' => $new_ratings, 'dlid' => $dl)
				);
				$smcFunc['db_query']('', '
				 	UPDATE {db_prefix}tp_dlmanager
				 	SET voters = {string:vote}
				 	WHERE id = {int:dlid}',
					array('vote' => $new_voters, 'dlid' => $dl)
				);
			}
			// go back to the download
			redirectexit('action=tportal;dl=item'.$dl);
		}
	}
	elseif($tpsub == 'dlsubmitsuccess') {
		$context['TPortal']['subaction'] = 'dlsubmitsuccess';
		loadtemplate('TPmodules');
		$context['sub_template'] = 'dlsubmitsuccess';
	}
	else {
		redirectexit('action=forum');
	}
}

?>
