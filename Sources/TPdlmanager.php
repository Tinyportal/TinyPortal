<?php
/**
 * @package TinyPortal
 * @version 1.2
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2015 - The TinyPortal Team
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

function TPdlmanager_init()
{
	global $context, $settings, $sourcedir;

	// load the needed strings
	if(loadLanguage('TPdlmanager') == false)
		loadLanguage('TPdlmanager', 'english');

	$context['can_tp_dlupload'] = allowedTo('tp_dlupload');

	require_once($sourcedir . '/TPcommon.php');
	// get subaction
	if(isset($context['TPortal']['dlsub']))
	{
		// a switch to make it clear what is "forum" and not
		$context['TPortal']['not_forum'] = true;
	
		$context['html_headers'] .= '
			<script type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/editor.js?rc1"></script>';
	
		// see if its admin section
		if(substr($context['TPortal']['dlsub'], 0, 5) == 'admin' || $context['TPortal']['dlsub']=='submission')
			TPortalDLAdmin();
		elseif(substr($context['TPortal']['dlsub'], 0, 8) == 'useredit')
			TPortalDLUser(substr($context['TPortal']['dlsub'], 8));
		else
			TPortalDLManager();
	}
}

// TinyPortal DLmanager
function TPortalDLManager()
{
	global $txt, $scripturl, $boarddir, $boardurl, $context, $settings, $smcFunc;

	// assume its the frontpage initially
	$context['TPortal']['dlaction'] = 'main';;

	// is even the manager active?
	if(!$context['TPortal']['show_download'])
		fatal_error($txt['tp-dlmanageroff']);

	$context['TPortal']['upshrinkpanel'] = '';
	
	// add visual options to thsi section
	$context['TPortal']['dl_visual'] = array();
	$dl_visual = explode(',',$context['TPortal']['dl_visual_options']);
	$dv = array('left', 'right', 'center', 'lower', 'top', 'bottom');
	foreach($dv as $v => $val)
	{
		if($context['TPortal'][$val.'panel'] == 1)
		{
			if(in_array($val,$dl_visual))
				$context['TPortal'][$val.'panel'] = '1';
			else
				$context['TPortal'][$val.'panel'] = '0';
		}
		$context['TPortal']['dl_visual'][$val] = true;
	}

	if(in_array('top', $dl_visual))
		$context['TPortal']['showtop'] = '1';
	else
		$context['TPortal']['showtop'] = '0';

	// check that you can upload at all
	if(allowedTo('tp_dlupload'))
	    $context['TPortal']['can_upload'] = true;
	else
	    $context['TPortal']['can_upload'] = false;

	// fetch all files from tp-downloads
	if(isset($_GET['ftp']) && allowedTo('tp_dlmanager'))
		TP_dlftpfiles();

	// any uploads being sent?
	$context['TPortal']['uploads'] = array();
	if(isset($_FILES['tp-dluploadfile']['tmp_name']) || isset($_POST['tp-dluploadnot']))
	{
		// skip the uplaod checks etc . if just an empty item
		if(!isset($_POST['tp-dluploadnot']))
		{
			// check if uploaded quick-list picture 
			if(isset($_FILES['qup_tp_dluploadtext']) && file_exists($_FILES['qup_tp_dluploadtext']['tmp_name']))
			{
				$item_id = isset($_GET['dl']) ? $_GET['dl'] : 'upload';
				$name = TPuploadpicture('qup_tp_dluploadtext', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
				redirectexit('action=tpmod;dl='. $item_id);
			}			
			// check that nothing happended
			if(!file_exists($_FILES['tp-dluploadfile']['tmp_name']) || !is_uploaded_file($_FILES['tp-dluploadfile']['tmp_name']))
				fatal_error($txt['tp-dluploadfailure']);

			// first, can we upload at all?
			if(!$context['TPortal']['can_upload'])
			{
				unlink($_FILES['tp-dluploadfile']['tmp_name']);
				fatal_error($txt['tp-dluploadnotallowed']);
			}
		}
		// a file it is
		$title = isset($_POST['tp-dluploadtitle']) ? strip_tags($_POST['tp-dluploadtitle']) : $txt['tp-no_title'];
		if($title == '')
			$title = $txt['tp-no_title'];
		$text = isset($_POST['tp_dluploadtext']) ? $_POST['tp_dluploadtext'] : '';
		$category = isset($_POST['tp-dluploadcat']) ? (int) $_POST['tp-dluploadcat'] : 0;
		// a screenshot?
		if(file_exists($_FILES['tp_dluploadpic']['tmp_name']) || is_uploaded_file($_FILES['tp_dluploadpic']['tmp_name']))
			$shot = true;
		else
			$shot = false;

		$icon = !empty($_POST['tp_dluploadicon']) ? $boardurl.'/tp-downloads/icons/'.$_POST['tp_dluploadicon'] : '';

		if(!isset($_POST['tp-dluploadnot']))
		{
			// process the file
			$filename = $_FILES['tp-dluploadfile']['name'];
			$name = strtr($filename, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
			$name = strtr($name, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
			$name = preg_replace(array('/\s/', '/[^\w_\.\-]/'), array('_', ''), $name);
		}
		else
			$name = '- empty item -';

	    if(isset($_POST['tp-dlupload_ftpstray']))
    		$name = '- empty item - ftp';

		$status = 'normal';

		if(!isset($_POST['tp-dluploadnot']))
		{
			// check the size
			$dlfilesize = filesize($_FILES['tp-dluploadfile']['tmp_name']);
			if($dlfilesize > (1000 * $context['TPortal']['dl_max_upload_size']))
			{
				$status = 'maxsize';
				unlink($_FILES['tp-dluploadfile']['tmp_name']);
				$error = $txt['tp-dlmaxerror'].' '.($context['TPortal']['dl_max_upload_size']).' Kb<br /><br />'.$txt['tp-dlmaxerror2'].': '. ceil($dlfilesize/1000) .' Kb';
				fatal_error($error);
			}
		}
		else
			$dlfilesize = 0;

		if(!isset($_POST['tp-dluploadnot']))
		{
			// check the extension
			$allowed = explode(',', $context['TPortal']['dl_allowed_types']);
			$match = false;
			foreach($allowed as $extension => $value)
			{
				$ext = '.'.$value;
				$extlen = strlen($ext);
				if(substr($name, strlen($name) - $extlen, $extlen) == $ext)
					$match = true;
			}
			if(!$match)
			{
				$status = 'wrongtype';
				unlink($_FILES['tp-dluploadfile']['tmp_name']);
				$error = $txt['tp-dlexterror'].':<b> <br />'.$context['TPortal']['dl_allowed_types'].'</b><br /><br />'.$txt['tp-dlexterror2'].': <b>'.$name.'</b>';
				fatal_error($error);
			}
		}

		// ok, go ahead
		if($status == 'normal')
		{
			if(!isset($_POST['tp-dluploadnot']))
			{
				// check that no other file exists with same name
				if(file_exists($boarddir.'/tp-downloads/'.$name))
					$name = time().$name;

				$success = move_uploaded_file($_FILES['tp-dluploadfile']['tmp_name'], $boarddir.'/tp-downloads/'.$name);
			}

			if($shot)
			{
				$sfile = 'tp_dluploadpic';
				$uid = $context['user']['id'].'uid';
				$dim = '1800';
				$suf = 'jpg,gif,png';
				$dest = 'tp-images/dlmanager';
				$sname = TPuploadpicture($sfile, $uid, $dim, $suf, $dest);
				$screenshot = $sname;
				tp_createthumb($dest.'/'.$sname ,$context['TPortal']['dl_screenshotsize'][0],$context['TPortal']['dl_screenshotsize'][1], $dest.'/thumb/'.$sname);
				tp_createthumb($dest.'/'.$sname ,$context['TPortal']['dl_screenshotsize'][2],$context['TPortal']['dl_screenshotsize'][3], $dest.'/listing/'.$sname);
				tp_createthumb($dest.'/'.$sname ,$context['TPortal']['dl_screenshotsize'][4],$context['TPortal']['dl_screenshotsize'][5], $dest.'/single/'.$sname);
			}
			else
			{
				if(isset($_POST['tp_dluploadpic_link']))
					$screenshot = $_POST['tp_dluploadpic_link'];
				else
					$screenshot = '';
			}
			// insert it into the database
			$now = time();

			// if all uploads needs to be approved: set category to -category , but not for dl admins
			if($context['TPortal']['dl_approve'] == '1' && !allowedTo('tp_dlmanager'))
				$category = $category - $category - $category;

			// get the category access
			$request = $smcFunc['db_query']('', '
				SELECT access FROM {db_prefix}tp_dlmanager WHERE id = {int:cat}',
				array('cat' => $category)
			);
			if($smcFunc['db_num_rows']($request) > 0)
			{
				$row = $smcFunc['db_fetch_assoc']($request);
				$acc = $row['access'];
			}
			else
				$acc = '';

			$request = $smcFunc['db_insert']('INSERT', 
				'{db_prefix}tp_dlmanager',
				array('name' => 'string', 'description' => 'string', 'icon' => 'string', 'category' => 'int', 'type' => 'string', 'downloads' => 'int', 'views' => 'int',
			 		'file' => 'string', 'created' => 'int', 'last_access' => 'int', 'filesize' => 'int', 'parent' => 'int', 'access' => 'string', 'link' => 'string',
					 'author_id' => 'int', 'screenshot' => 'string', 'rating' => 'string', 'voters' => 'string', 'subitem' => 'int'),
				array($title, $text, $icon, $category, 'dlitem', 0, 1, $name, $now, $now, $dlfilesize, 0, '', '', $context['user']['id'], $screenshot, '', '', 0),
				array('id')
			);

			$newitem = $smcFunc['db_insert_id']($request);
			
			// record the event
			if(($context['TPortal']['dl_approve'] == '1' && allowedTo('tp_dlmanager')) || $context['TPortal']['dl_approve']=='0')
				tp_recordevent($now, $context['user']['id'], 'tp-createdupload', 'action=tpmod;dl=item' . $newitem, 'Uploaded new file.', $acc , $newitem);

			// should we create a topic?
			if(isset($_POST['create_topic']) && (allowedTo('admin_forum') || !empty($context['TPortal']['dl_create_topic'])))
			{
				$sticky = false;
				$announce = false;
				// sticky and announce?
				if(isset($_POST['create_topic_sticky']))
					$sticky = true;
				if(isset($_POST['create_topic_announce']) && allowedTo('admin_forum'))
					$announce = true;
				if(!empty($_POST['create_topic_board']))
					$brd = $_POST['create_topic_board'];
				if(isset($_POST['create_topic_body']))
					$body = $_POST['create_topic_body'];

				
				$body .= '[hr][b]'.$txt['tp-download'].':[/b][br]'.$scripturl.'?action=tpmod;dl=item'.$newitem;
				// ok, create the topic then
				$top = TP_createtopic($title, $body, 'theme', $brd, $sticky ? 1 : 0 , $context['user']['id']);
				// go to announce screen?
				if($top > 0)
				{
					if($announce)
						redirectexit('action=announce;sa=selectgroup;topic=' . $top );
					else
						redirectexit('topic=' . $top );
				
				}
			}
			// put this into submissions - id and type
			if($category < 0)
			{
				$smcFunc['db_insert']('INSERT',
					'{db_prefix}tp_variables',
					array('value1' => 'string', 'value2' => 'string', 'value3' => 'string', 'type' => 'string', 'value4' => 'string', 'value5' => 'int'),
					array($title, $now, '', 'dl_not_approved', '', $newitem),
					array('id')
				);
				redirectexit('action=tpmod;sub=dlsubmitsuccess');
			}
			else{
				if(!isset($_POST['tp-dluploadnot']))
					redirectexit('action=tpmod;dl=item'.$newitem);
				else
					redirectexit('action=tpmod;dl=adminitem'.$newitem);
			}
		 }
	}

	// ok, on with the show :)
	TP_dluploadcats();
	TP_dlgeticons();
	// showing a category, or even a single item?
	$context['TPortal']['dlaction'] = '';
	if(isset($context['TPortal']['dlsub']))
	{
		// a category?
		if(substr($context['TPortal']['dlsub'], 0, 3) == 'cat')
		{
				$context['TPortal']['dlcat'] = substr($context['TPortal']['dlsub'], 3);
				// check if its a number
				if(is_numeric($context['TPortal']['dlcat']))
					$context['TPortal']['dlaction'] = 'cat';
				else
					redirectexit('action=tpmod;dl');
		}
		elseif(substr($context['TPortal']['dlsub'], 0, 4) == 'item')
		{
				$context['TPortal']['dlitem'] = substr($context['TPortal']['dlsub'], 4);
				if(is_numeric($context['TPortal']['dlitem']))
				{
					$item = $context['TPortal']['dlitem'];
					$context['TPortal']['item'] = $item;
					$context['TPortal']['dlaction'] = 'item';
					$request = $smcFunc['db_query']('', '
						SELECT category, subitem 
						FROM {db_prefix}tp_dlmanager 
						WHERE id = {int:dl} AND type = {string:type} LIMIT 1',
						array('dl' => $item, 'type' => 'dlitem')
					);
					if($smcFunc['db_num_rows']($request) > 0)
					{
						$row = $smcFunc['db_fetch_assoc']($request);
						$context['TPortal']['dlcat'] = $row['category'];
						$smcFunc['db_free_result']($request);
						// check that it is indeed a main item, if not : redirect to the main one.
						if($row['subitem'] > 0)
							redirectexit('action=tpmod;dl=item'.$row['subitem']);
					}
					else
						redirectexit('action=tpmod;dl');
				}
				else
					redirectexit('action=tpmod;dl');
		}
		elseif($context['TPortal']['dlsub'] == 'stats')
		{
			$context['TPortal']['dlaction'] = 'stats';
			$context['TPortal']['dlitem'] = '';
		}
		elseif($context['TPortal']['dlsub'] == 'search')
		{
			$context['TPortal']['dlaction'] = 'search';
			$context['TPortal']['dlitem'] = '';
		}
		elseif($context['TPortal']['dlsub'] == 'results')
		{
			$context['TPortal']['dlaction'] = 'results';
			$context['TPortal']['dlitem'] = '';
		}
		elseif($context['TPortal']['dlsub'] == 'submission')
		{
			$context['TPortal']['dlaction'] = 'submission';
			$context['TPortal']['dlitem'] = '';
		}
		elseif(substr($context['TPortal']['dlsub'], 0, 3) == 'get')
		{
			$context['TPortal']['dlitem'] = substr($context['TPortal']['dlsub'], 3);
			if(is_numeric($context['TPortal']['dlitem']))
				$context['TPortal']['dlaction'] = 'get';
			else
				redirectexit('action=tpmod;dl');
		}
		elseif(substr($context['TPortal']['dlsub'], 0, 6) == 'upload')
		{
			$context['TPortal']['dlitem'] = substr($context['TPortal']['dlsub'], 6);
			$context['TPortal']['dlaction'] = 'upload';

			// check your permission for uploading
			isAllowedTo('tp_dlupload');

			// Add in BBC editor before we call in template so the headers are there
			if($context['TPortal']['dl_wysiwyg'] == 'bbc')
			{
				$context['TPortal']['editor_id'] = 'tp_dluploadtext';
				TP_prebbcbox($context['TPortal']['editor_id']); 			
			}
			TP_dlgeticons();
			
			// allow to attach this to another item
			$context['TPortal']['attachitems'] = array();
			if(allowedTo('dlmanager'))
			{
				// get all items for a list
				$itemlist = $smcFunc['db_query']('', '
					SELECT id, name FROM {db_prefix}tp_dlmanager 
					WHERE type = {string:type} AND subitem = {int:sub} ORDER BY name ASC',
					array('type' => 'dlitem', 'sub' => 0)
				);
				if($smcFunc['db_num_rows']($itemlist) > 0)
				{
					while($ilist = $smcFunc['db_fetch_assoc']($itemlist))
					{
						$context['TPortal']['attachitems'][] = array(
							'id' => $ilist['id'],
							'name' => $ilist['name'],
						);
					}
					$smcFunc['db_free_result']($itemlist);
				}
			}
			else
			{
				// how about attaching to one of your own?
				// get all items for a list
				$itemlist = $smcFunc['db_query']('', '
					SELECT id,name FROM {db_prefix}tp_dlmanager 
					WHERE category > {int:cat} 
					AND type = {string:type} 
					AND subitem = {int:sub} 
					AND author_id = {int:auth}
					ORDER BY name ASC',
					array('cat' => 0, 'type' => 'dlitem', 'sub' => 0, 'auth' => $context['user']['id'])
				);
				if(isset($itemlist) && $smcFunc['db_num_rows']($itemlist) > 0)
				{
					while($ilist = $smcFunc['db_fetch_assoc']($itemlist))
					{
						$context['TPortal']['attachitems'][] = array(
							'id' => $ilist['id'],
							'name' => $ilist['name'],
						);
					}
					$smcFunc['db_free_result']($itemlist);
				}
			}

			$context['TPortal']['boards'] = array();
			// fetch all boards
			$request = $smcFunc['db_query']('', '
				SELECT b.ID_BOARD, b.name FROM {db_prefix}boards as b
				WHERE {query_see_board}');
			if ($smcFunc['db_num_rows']($request) > 0)
			{
				while($row = $smcFunc['db_fetch_assoc']($request))
					$context['TPortal']['boards'][] = array('id' => $row['ID_BOARD'], 'name' => $row['name']);

				$smcFunc['db_free_result']($request);
			}
		}

		// a category?
		else
		{
			// check its really exists
			$what = $context['TPortal']['dlsub'];
			$request = $smcFunc['db_query']('', '
				SELECT id FROM {db_prefix}tp_dlmanager 
				WHERE link = {string:link} LIMIT 1',
				array('link' => $what));
			if(isset($request) && $smcFunc['db_num_rows']($request) > 0)
			{
				$row = $smcFunc['db_fetch_assoc']($request);
				$context['TPortal']['dlcat'] = $row['id'];
				$context['TPortal']['dlsub'] = 'cat'.$row['id'];
				$context['TPortal']['dlaction'] = 'cat';
				$smcFunc['db_free_result']($request);
			}
		}
	}
	// add to the linktree
	TPadd_linktree($scripturl.'?action=tpmod;dl=0', $txt['tp-downloads']);

	// set the title
	$context['page_title'] = $txt['tp-downloads'];
	$context['TPortal']['dl_title'] = $txt['tp-mainpage'];

	// load the dlmanager frontpage
	if($context['TPortal']['dlaction'] == '')
	{
		$context['TPortal']['dlcats'] = array();
		$context['TPortal']['dlcatchilds'] = array();

		// add x most recent and feature the last one
		$context['TPortal']['dl_last_added'] = array();
		$context['TPortal']['dl_most_downloaded'] = array();
		$context['TPortal']['dl_week_downloaded'] = array();

		$mycats = array();
		dl_getcats();
		foreach($context['TPortal']['dl_allowed_cats'] as $ca)
			$mycats[] = $ca['id'];

		// empty?
		if(sizeof($mycats) > 0)
		{
			$request = $smcFunc['db_query']('', '
				SELECT dlm.id, dlm.name, dlm.category, dlm.file, dlm.downloads, dlm.views,
					dlm.author_id as authorID, dlm.created, dlm.screenshot, dlm.filesize,
					dlcat.name AS catname, mem.real_name as realName, LEFT(dlm.description,100) as 	description
				FROM ({db_prefix}tp_dlmanager AS dlm, {db_prefix}members AS mem)
				LEFT JOIN {db_prefix}tp_dlmanager AS dlcat ON (dlcat.id = dlm.category)
				WHERE dlm.type = {string:type}
				AND dlm.category IN ({array_int:cat})
				AND dlm.author_id = mem.id_member
				ORDER BY dlm.created DESC LIMIT 6',
				array('type' => 'dlitem', 'cat' => $mycats)
			);

			if($smcFunc['db_num_rows']($request) > 0)
			{
				while ($row = $smcFunc['db_fetch_assoc']($request))
				{
					$fs = '';
					if($context['TPortal']['dl_fileprefix'] == 'K')
						$fs = ceil($row['filesize'] / 1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix'] == 'M')
						$fs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix'] == 'G')
						$fs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';

					if($context['TPortal']['dl_usescreenshot'] == 1)
					{
						if(!empty($row['screenshot'])) 
							$ico = $boardurl.'/tp-images/dlmanager/thumb/'.$row['screenshot'];
						else
							$ico = '';	
					}
					else
						$ico = '';

					$context['TPortal']['dl_last_added'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'category' => $row['category'],
						'description' => $context['TPortal']['dl_wysiwyg'] == 'bbc' ? parse_bbc(trim(strip_tags($row['description']))) : $row['description'],
						'file' => $row['file'],
						'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
						'downloads' => $row['downloads'],
						'views' => $row['views'],
						'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
						'authorID' => $row['authorID'],
						'date' => timeformat($row['created']),
						'screenshot' => $ico,
						'catname' => $row['catname'],
						'cathref' => $scripturl.'?action=tpmod;dl=cat'.$row['category'],
						'filesize' => $fs,
					);
				}
				$smcFunc['db_free_result']($request);
			}
			$request = $smcFunc['db_query']('', '
				SELECT dlm.id, dlm.name, dlm.category, dlm.file, dlm.downloads, dlm.views,
					dlm.author_id as authorID, dlm.created, dlm.filesize, dlcat.name AS catname, 
					mem.real_name as realName
				FROM ({db_prefix}tp_dlmanager AS dlm, {db_prefix}members AS mem)
				LEFT JOIN {db_prefix}tp_dlmanager AS dlcat ON dlcat.id = dlm.category
				WHERE dlm.type = {string:type}
				AND dlm.category IN ({array_string:cat})
				AND dlm.author_id = mem.id_member
				ORDER BY dlm.downloads DESC LIMIT 10',
				array('type' => 'dlitem', 'cat' => $mycats)
			);

			if($smcFunc['db_num_rows']($request) > 0)
			{
				while ($row = $smcFunc['db_fetch_assoc']($request))
				{
					$fs = '';
					if($context['TPortal']['dl_fileprefix'] == 'K')
						$fs = ceil($row['filesize'] / 1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix'] == 'M')
						$fs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix'] == 'G')
						$fs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';

					$context['TPortal']['dl_most_downloaded'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'category' => $row['category'],
						'file' => $row['file'],
						'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
						'downloads' => $row['downloads'],
						'views' => $row['views'],
						'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
						'authorID' => $row['authorID'],
						'date' => timeformat($row['created']),
						'catname' => $row['catname'],
						'cathref' => $scripturl.'?action=tpmod;dl=cat'.$row['category'],
						'filesize' => $fs,
					);
				}
				$smcFunc['db_free_result']($request);
			}
			// fetch most downloaded this week
			$now = time();
			$week = (int) date("W",$now);
			$year = (int) date("Y",$now);
			$request = $smcFunc['db_query']('', '
				SELECT dlm.id, dlm.name, dlm.category, dlm.file, data.downloads, dlm.views, 
					dlm.author_id as authorID, dlm.created, dlm.screenshot, dlm.filesize,
					dlcat.name AS catname, mem.real_name as realName
				FROM ({db_prefix}tp_dlmanager AS dlm, {db_prefix}tp_dldata AS data, {db_prefix}members AS mem)
				LEFT JOIN {db_prefix}tp_dlmanager AS dlcat ON dlcat.id = dlm.category
				WHERE dlm.type = {string:type}
				AND dlm.category IN ({array_string:cat})
				AND data.item = dlm.id
				AND data.year = {int:yr}
				AND data.week = {int:week}
				AND dlm.author_id = mem.id_member
				ORDER BY data.downloads DESC LIMIT 10',
				array('type' => 'dlitem', 'cat' => $mycats, 'yr' => $year, 'week' => $week)
			);

			if($smcFunc['db_num_rows']($request) > 0)
			{
				while ($row = $smcFunc['db_fetch_assoc']($request))
				{
					if($context['TPortal']['dl_usescreenshot'] == 1)
					{
						if(!empty($row['screenshot'])) 
							$ico = $boardurl.'/tp-images/dlmanager/thumb/'.$row['screenshot'];
						else
							$ico = '';	
					}
					else
						$ico = '';

					$fs = '';
					if($context['TPortal']['dl_fileprefix'] == 'K')
						$fs = ceil($row['filesize'] / 1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix'] == 'M')
						$fs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix'] == 'G')
						$fs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';

					$context['TPortal']['dl_week_downloaded'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'category' => $row['category'],
						'file' => $row['file'],
						'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
						'downloads' => $row['downloads'],
						'views' => $row['views'],
						'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
						'authorID' => $row['authorID'],
						'date' => timeformat($row['created']),
						'screenshot' => $ico,
						'catname' => $row['catname'],
						'cathref' => $scripturl.'?action=tpmod;dl=cat'.$row['category'],
						'filesize' => $fs,
					);
				}
				$smcFunc['db_free_result']($request);
			}
		}
		// fetch the categories, the number of files
		$request = $smcFunc['db_query']('', '
			SELECT a.access AS access, a.icon AS icon, a.link AS shortname, a.description AS description,
				a.name AS name, a.id AS id, a.parent AS parent,
	  			if (a.id = b.category, count(*), 0) AS files, b.category AS subchild
			FROM ({db_prefix}tp_dlmanager AS a)
			LEFT JOIN {db_prefix}tp_dlmanager AS b ON (a.id = b.category)
			WHERE a.type = {string:type}
		  	GROUP BY a.id
			ORDER BY a.downloads ASC',
			array('type' => 'dlcat')
		);

		$fetched_cats = array();
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$show = get_perm($row['access'], 'tp_dlmanager');
				if($show && $row['parent'] == 0)
				{
					$context['TPortal']['dlcats'][$row['id']] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'parent' => $row['parent'],
						'description' => $context['TPortal']['dl_wysiwyg'] == 'bbc' ? parse_bbc(trim(strip_tags($row['description']))) : $row['description'],
						'access' => $row['access'],
						'icon' => $row['icon'],
						'href' => !empty($row['shortname']) ? $scripturl.'?action=tpmod;dl='.$row['shortname'] : $scripturl.'?action=tpmod;dl=cat'.$row['id'],
						'shortname' => !empty($row['shortname']) ? $row['shortname'] : $row['id'],
						'files' => $row['files'],
					);
					$fetched_cats[] = $row['id'];
				}
				elseif($show && $row['parent'] > 0)
				{
						$context['TPortal']['dlcatchilds'][] = array(
							'id' => $row['id'],
							'name' => $row['name'],
							'parent' => $row['parent'],
							'href' => $scripturl.'?action=tpmod;dl=cat'.$row['id'],
							'files' => $row['files'],
						);
				}
			}
			$smcFunc['db_free_result']($request);
		}
		// add filecount to parent
		foreach($context['TPortal']['dlcatchilds'] as $child)
		{
			if(isset($context['TPortal']['dlcats'][$child['parent']]) && $context['TPortal']['dlcats'][$child['parent']]['parent'] == 0)
				$context['TPortal']['dlcats'][$child['parent']]['files'] = $context['TPortal']['dlcats'][$child['parent']]['files'] + $child['files'];
		}
		// do we need the featured one?
		if(!empty($context['TPortal']['dl_featured']))
		{
				// fetch the item data
				$item =	$context['TPortal']['dl_featured'];
				$request = $smcFunc['db_query']('', '
					SELECT dl.* , dl.author_id as authorID, m.real_name as realName
					FROM ({db_prefix}tp_dlmanager AS dl, {db_prefix}members AS m)
					WHERE dl.type = {string:type}
					AND dl.id = {int:item}
					AND dl.author_id = m.id_member
					LIMIT 1',
					array('type' => 'dlitem', 'item' => $item)
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					$row = $smcFunc['db_fetch_assoc']($request);
					if($context['TPortal']['dl_fileprefix'] == 'K')
						$fs = ceil($row['filesize'] / 1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix'] == 'M')
						$fs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix'] == 'G')
						$fs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';

					$rat = array();
					$rating_votes = 0;
					$rat = explode(',', $row['rating']);
					$rating_votes = count($rat);
					if($row['rating'] == '')
						$rating_votes = 0;

					$total = 0;
					foreach($rat as $mm => $mval)
						$total = $total + $mval;

					if($rating_votes > 0 && $total > 0)
						$rating_average = floor($total / $rating_votes);
					else
						$rating_average = 0;

					   $decideshot = !empty($row['screenshot']) ? $boardurl. '/' . $row['screenshot'] : ''; 
						// does it exist? 
						if(file_exists($boarddir . '/tp-images/dlmanager/listing/' . $row['screenshot']) && !empty($row['screenshot']))
							$decideshot = $boardurl. '/tp-images/dlmanager/listing/' . $row['screenshot']; 

						if($context['user']['is_logged'])
							$can_rate = in_array($context['user']['id'], explode(',', $row['voters'])) ? false : true;
						else
							$can_rate = false;
							
						$context['TPortal']['featured'] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'description' => $context['TPortal']['dl_wysiwyg'] == 'bbc' ? parse_bbc(trim(strip_tags($row['description']))) : $row['description'],
						'category' => $row['category'],
						'file' => $row['file'],
						'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
						'downloads' => $row['downloads'],
						'views' => $row['views'],
						'link' => $row['link'],
						'date_last' => $row['last_access'],
						'author' => $row['realName'],
						'authorID' => $row['authorID'],
						'screenshot' => $row['screenshot'],
						'sshot' => $decideshot,
						'icon' => $row['icon'],
						'created' => $row['created'],
						'filesize' => $fs,
						'subitem' => isset($fdata) ? $fdata : '',
						'rating_votes' => $rating_votes,
						'rating_average' => $rating_average,
						'can_rate' => $can_rate,
					);
				}
				$smcFunc['db_free_result']($request);
	
		}
		$context['TPortal']['dlheader'] = $txt['tp-downloads'];
	}
	// load a category
	elseif($context['TPortal']['dlaction'] == 'cat')
	{
		// check if sorting is specified
		if(isset($_GET['dlsort']) && in_array($_GET['dlsort'], array('id', 'name', 'last_access', 'created', 'downloads', 'author_id')))
			$context['TPortal']['dlsort'] = $dlsort = $_GET['dlsort'];
		else
			$context['TPortal']['dlsort'] = $dlsort = 'id';
		
		if(isset($_GET['asc']))
			$context['TPortal']['dlsort_way'] = $dlsort_way = 'asc';
		else
			$context['TPortal']['dlsort_way'] = $dlsort_way = 'desc';

		$currentcat = $context['TPortal']['dlcat'];
		//fetch all  categories and its childs
		$context['TPortal']['dlcats'] = array();
		$context['TPortal']['dlcatchilds'] = array();
		$context['TPortal']['dl_week_downloaded'] = array();

		// fetch most downloaded this week
		$now = time();
		$week = (int) date("W",$now);
		$year = (int) date("Y",$now);
		$request = $smcFunc['db_query']('', '
			SELECT dlm.id, dlm.name, dlm.category, dlm.file, dlm.downloads, dlm.views, dlm.author_id as authorID, dlm.created, dlm.screenshot, dlm.filesize,
			dlcat.name AS catname, mem.real_name as realName
			FROM ({db_prefix}tp_dlmanager AS dlm, {db_prefix}tp_dldata AS data, {db_prefix}members AS mem)
			LEFT JOIN {db_prefix}tp_dlmanager AS dlcat ON dlcat.id=dlm.category
			WHERE dlm.type = {string:type}
			AND (dlm.category = {int:cat} OR dlm.parent = {int:cat})
			AND data.item = dlm.id
			AND data.year = {int:year}
			AND data.week = {int:week}
			AND dlm.author_id = mem.id_member
			ORDER BY dlm.downloads DESC LIMIT 10',
			array('type' => 'dlitem', 'cat' => $currentcat, 'year' => $year, 'week' => $week)
		);

		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$fs = '';
				if($context['TPortal']['dl_fileprefix'] == 'K')
					$fs = ceil($row['filesize'] / 1000).' Kb';
				elseif($context['TPortal']['dl_fileprefix'] == 'M')
					$fs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
				elseif($context['TPortal']['dl_fileprefix'] == 'G')
					$fs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';

				$context['TPortal']['dl_week_downloaded'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'category' => $row['category'],
					'file' => $row['file'],
					'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
					'downloads' => $row['downloads'],
					'views' => $row['views'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
					'authorID' => $row['authorID'],
					'date' => timeformat($row['created']),
					'screenshot' => !empty($row['screenshot']) ? $row['screenshot'] : '' ,
					'catname' => $row['catname'],
					'cathref' => $scripturl.'?action=tpmod;dl=cat'.$row['category'],
					'filesize' => $fs,
				);
			}
			$smcFunc['db_free_result']($request);
		}

		// add x most recent and feature the last one
		$context['TPortal']['dl_last_added'] = dl_recentitems(5, 'date', 'array', $context['TPortal']['dlcat']);
		$context['TPortal']['dl_most_downloaded'] = dl_recentitems(5, 'downloads', 'array', $context['TPortal']['dlcat']);

		// do we have access then?
		$request = $smcFunc['db_query']('', '
			SELECT parent, access, name 
			FROM {db_prefix}tp_dlmanager 
			WHERE id = {int:cat}',
			array('cat' => $currentcat)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$currentname = $row['name'];
				$context['page_title'] = $row['name'];
				$catparent = $row['parent'];
				if(!get_perm($row['access'], 'tp_dlmanager'))
				{
					// if a guest, make them login/register
					if($context['user']['is_guest'])
					{
						redirectexit('action=login');;
					}
					else
						redirectexit('action=tpmod;dl');
				}
			}
	        $smcFunc['db_free_result']($request);
	    }
	    // nothing there, le them know
	    else
			redirectexit('action=tpmod;dl');

		$request = $smcFunc['db_query']('', '
			SELECT a.access AS access, a.icon AS icon,	a.link AS shortname, a.description AS description,
				a.name AS name,	a.id AS id, a.parent AS parent, if (a.id = b.category, count(*), 0) AS files,
		  		b.category AS subchild
			FROM ({db_prefix}tp_dlmanager AS a)
			LEFT JOIN {db_prefix}tp_dlmanager AS b
		  		ON a.id = b.category
			WHERE a.type = {string:type}
			AND a.parent = {int:cat}
		  	GROUP BY a.id
		  	ORDER BY a.downloads ASC',
			array('type' => 'dlcat', 'cat' => $currentcat));
		$context['TPortal']['dlchildren'] = array();
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$show = get_perm($row['access'], 'tp_dlmanager');
				if($show && $row['parent'] == $currentcat)
				{
					$context['TPortal']['dlcats'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'parent' => $row['parent'],
						'description' => $context['TPortal']['dl_wysiwyg'] == 'bbc' ? parse_bbc(trim(strip_tags($row['description']))) : $row['description'],
						'access' => $row['access'],
						'icon' => $row['icon'],
						'href' => !empty($row['shortname']) ? $scripturl.'?action=tpmod;dl='.$row['shortname'] : $scripturl.'?action=tpmod;dl=cat'.$row['id'],
						'shortname' => !empty($row['shortname']) ? $row['shortname'] : $row['id'],
						'files' => $row['files'],
					);
				}
				elseif($show && $row['parent'] != $currentcat)
				{
					$context['TPortal']['dlchildren'][] = $row['id'];
					$context['TPortal']['dlcatchilds'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'parent' => $row['parent'],
						'href' => !empty($row['shortname']) ? $scripturl.'?action=tpmod;dl='.$row['shortname'] : $scripturl.'?action=tpmod;dl=cat'.$row['id'],
						'shortname' => !empty($row['shortname']) ? $row['shortname'] : $row['id'],
						'files' => $row['files'],
					);
				}
			}
			$smcFunc['db_free_result']($request);
		}

		// get any items in the category
			$context['TPortal']['dlitem'] = array();
			$start = 0;
			if(isset($_GET['p']) && !is_numeric($_GET['p']))
				fatal_error('Attempt to specify a non-integer value!');
			elseif(isset($_GET['p']) && is_numeric($_GET['p']))
				$start = $_GET['p'];

			// get total count
			$request = $smcFunc['db_query']('', '
				SELECT COUNT(*) FROM {db_prefix}tp_dlmanager 
				WHERE type = {string:type} 
				AND category = {int:cat} 
				AND subitem = {int:sub}',
				array('type' => 'dlitem', 'cat' => $currentcat, 'sub' => 0)
			);
			$row = $smcFunc['db_fetch_row']($request);
			$rows2 = $row[0];

			$request = $smcFunc['db_query']('', '
				SELECT dl.id, LEFT(dl.description, 400) as ingress,dl.name, dl.category, dl.file, 
					dl.downloads, dl.views, dl.link, dl.created, dl.last_access, 
					dl.author_id as authorID, dl.icon, dl.screenshot, dl.filesize, mem.real_name as realName 
				FROM {db_prefix}tp_dlmanager as dl
				LEFT JOIN {db_prefix}members as mem ON (dl.author_id=mem.id_member)
				WHERE dl.type = {string:type} 
				AND dl.category = {int:cat} 
				AND dl.subitem = {int:sub} 
				ORDER BY dl.'.$dlsort.' '. $dlsort_way .' LIMIT {int:start}, 10',
				array('type' => 'dlitem', 'cat' => $currentcat, 'sub' => 0, 'start' => $start)
			);

			if($smcFunc['db_num_rows']($request) > 0)
			{
				// set up the sorting links
				$context['TPortal']['sortlinks'] = '<span class="smalltext">' . $txt['tp-sortby'] . ': ';
				$what = array('id', 'name', 'downloads', 'last_access', 'created', 'authorID');
				foreach($what as $v)
				{
					if($context['TPortal']['dlsort'] == $v)
					{
						$context['TPortal']['sortlinks'] .= '<a href="'.$scripturl.'?action=tpmod;dl=cat'.$currentcat.';dlsort='.$v.';';
						if($context['TPortal']['dlsort_way'] == 'asc')
							$context['TPortal']['sortlinks'] .= 'desc;p='.$start.'">'.$txt['tp-'.$v].' <img src="' .$settings['tp_images_url']. '/TPsort_up.gif" alt="" /></a> &nbsp;|&nbsp; ';
						else
							$context['TPortal']['sortlinks'] .= 'asc;p='.$start.'">'.$txt['tp-'.$v].' <img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /></a> &nbsp;|&nbsp; ';
					}
					else
						$context['TPortal']['sortlinks'] .= '<a href="'.$scripturl.'?action=tpmod;dl=cat'.$currentcat.';dlsort='.$v.';desc;p='.$start.'">'.$txt['tp-'.$v].'</a> &nbsp;|&nbsp; ';
				}
				$context['TPortal']['sortlinks'] = substr($context['TPortal']['sortlinks'], 0, strlen($context['TPortal']['sortlinks']) - 15);
				$context['TPortal']['sortlinks'] .= '</span>';

				while ($row = $smcFunc['db_fetch_assoc']($request))
				{
					if(substr($row['screenshot'], 0, 16) == 'tp-images/Image/')
							$decideshot = $boardurl. '/' . $row['screenshot']; 
					else
						$decideshot = $boardurl. '/tp-images/dlmanager/thumb/' . $row['screenshot']; 

					if($context['TPortal']['dl_fileprefix'] == 'K')
						$fs = ceil($row['filesize'] / 1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix'] == 'M')
						$fs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix']=='G')
						$fs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';
					
					if($context['TPortal']['dl_usescreenshot'] == 1)
					{
						if(!empty($row['screenshot'])) 
							$ico = $boardurl.'/tp-images/dlmanager/thumb/'.$row['screenshot'];
						else
							$ico = '';	
					}
					else
						$ico = $row['icon'];

					$context['TPortal']['dlitem'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'category' => $row['category'],
						'file' => $row['file'],
						'description' => '',
						'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
						'dlhref' => $scripturl.'?action=tpmod;dl=get'.$row['id'],
						'downloads' => $row['downloads'],
						'views' => $row['views'],
						'link' => $row['link'],
						'created' => $row['created'],
						'date_last' => $row['last_access'],
						'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
						'authorID' => $row['authorID'],
						'screenshot' => $row['screenshot'],
						'sshot' => $decideshot,
						'icon' => $ico,
						'date' => $row['created'],
						'filesize' => $fs,
						'ingress' => $context['TPortal']['dl_wysiwyg']=='bbc' ? parse_bbc(trim(strip_tags($row['ingress']))) : $row['ingress'],
					);
				}
				$smcFunc['db_free_result']($request);
			}
			if(isset($context['TPortal']['mystart']))
				$mystart = $context['TPortal']['mystart'];

			$currsorting = '';
			if(!empty($dlsort))
				$currsorting .= ';dlsort='.$dlsort;
			if(!empty($dlsort_way))
				$currsorting .= ';'.$dlsort_way;

			// construct a pageindex
			$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpmod;dl=cat'.$currentcat.$currsorting, $mystart , $rows2, 10);

		// check backwards for parents
		$done = 0;
		$context['TPortal']['parents'] = array();
		while($catparent > 0 || $done < 2)
		{
			if(!empty($context['TPortal']['cats'][$catparent]))
			{
				$context['TPortal']['parents'][] = array(
					'id' => $catparent,
					'name' => $context['TPortal']['cats'][$catparent]['name'],
					'parent' => $context['TPortal']['cats'][$catparent]['parent']
				);
				$catparent = $context['TPortal']['cats'][$catparent]['parent'];
			}
			else
				$catparent = 0;
				
			if($catparent == 0)
				$done++;
		}

		// make the linktree
		if(sizeof($context['TPortal']['parents']) > 0)
		{
			$parts = array_reverse($context['TPortal']['parents']);
			// add to the linktree
			foreach($parts as $par)
			{
				TPadd_linktree($scripturl.'?action=tpmod;dl=cat'.$par['id'], $par['name']);
			}
		}
		// add to the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=cat'.$currentcat, $currentname);
		$context['TPortal']['dlheader'] = $currentname;
	}
	// tptags
	elseif($context['TPortal']['dlaction'] == 'tptag')
	{
		$context['TPortal']['dlsort'] = $dlsort = 'id';
		$context['TPortal']['dlsort_way'] = $dlsort_way = 'desc';

		// get any items in the category
		$context['TPortal']['dlitem'] = array();
		$start = 0;
		if(isset($_GET['p']) && !is_numeric($_GET['p']))
			fatal_error($txt['tp-dlnonint']);
		elseif(isset($_GET['p']) && is_numeric($_GET['p']))
			$start = $_GET['p'];

		// get total count
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(*) FROM {db_prefix}tp_dlmanager 
			WHERE type = {string:type}
			AND subitem = {int:sub}',
			array('type' => 'dlitem', 'sub' => 0)
		);
		$row = $smcFunc['db_fetch_row']($request);
		$rows2 = $row[0];

		$request = $smcFunc['db_query']('', '
			SELECT id, name, category, file, downloads, views, link, created,
				last_access, author_id as authorID, icon, screenshot, filesize,
				global_tag 
			FROM {db_prefix}tp_dlmanager 
			WHERE type = {string:type} 
			AND subitem = {int:sub} LIMIT {int:start}, 10',
			array('type' => 'dlitem', 'sub' => 0, 'start' => $start)
		);

		if($smcFunc['db_num_rows']($request) > 0)
		{
			if(substr($row['screenshot'], 0, 16) == 'tp-images/Image/')
					$decideshot = $boardurl. '/' . $row['screenshot']; 
			else
				$decideshot = $boardurl. '/tp-images/dlmanager/thumb/' . $row['screenshot']; 

			// set up the sorting links
			$context['TPortal']['sortlinks'] = '';

			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				if($context['TPortal']['dl_fileprefix'] == 'K')
					$fs = ceil($row['filesize'] / 1000).' Kb';
				elseif($context['TPortal']['dl_fileprefix']=='M')
					$fs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
				elseif($context['TPortal']['dl_fileprefix']=='G')
					$fs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';
				$context['TPortal']['dlitem'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'category' => $row['category'],
					'file' => $row['file'],
					'description' => '',
					'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
					'dlhref' => $scripturl.'?action=tpmod;dl=get'.$row['id'],
					'downloads' => $row['downloads'],
					'views' => $row['views'],
					'link' => $row['link'],
					'created' => $row['created'],
					'date_last' => $row['last_access'],
					'author' => '',
					'authorID' => $row['authorID'],
					'screenshot' => $row['screenshot'],
					'sshot' => $decideshot,
					'icon' => $row['icon'],
					'date' => $row['created'],
					'filesize' => $fs,
				);
			}
			$smcFunc['db_free_result']($request);
		}
		if(isset($context['TPortal']['mystart']))
			$mystart = $context['TPortal']['mystart'];

		$context['TPortal']['dlheader'] = '';
	}
	elseif($context['TPortal']['dlaction'] == 'item')
	{
		//fetch the category
		$cat = $context['TPortal']['dlcat'];
		$context['TPortal']['dlcats'] = array();
		$catname = '';
		$catdesc = '';

		$request = $smcFunc['db_query']('', '
			SELECT id, name, parent, icon, access, link 
			FROM {db_prefix}tp_dlmanager 
			WHERE id = {int:cat}
			AND type = {string:type} LIMIT 1',
			array('cat' => $cat, 'type' => 'dlcat')
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$catshortname = $row['link'];
				$catname = $row['name'];
				$catparent = $row['parent'];
				$firstparent = $row['parent'];

				// check if you are allowed in here
				$show = get_perm($row['access'], 'tp_dlmanager');
				if(!$show)
					redirectexit('action=tpmod;dl');
			}
			$smcFunc['db_free_result']($request);
		}

		// set the title
		$context['TPortal']['dl_title'] = $catname;

		$context['TPortal']['parents'] = array();
		// check backwards for parents
		$done = 0;
		while($catparent > 0 || $done < 2)
		{
			if(!empty($context['TPortal']['cats'][$catparent]))
			{
				$context['TPortal']['parents'][] = array(
					'id' => $catparent,
					'shortname' => $catshortname,
					'name' => $context['TPortal']['cats'][$catparent]['name'],
					'parent' => $context['TPortal']['cats'][$catparent]['parent']
				);
				$catparent=$context['TPortal']['cats'][$catparent]['parent'];
			}
			else{
				$catparent = 0;
			}
			if($catparent == 0)
				$done++;
		}

		// make the linktree
		if(sizeof($context['TPortal']['parents']) > 0)
		{
			$parts = array_reverse($context['TPortal']['parents'], TRUE);
			// add to the linktree
			foreach($parts as $parent)
			{
				if(!empty($parent['shortname']))
					TPadd_linktree($scripturl.'?action=tpmod;dl='.$parent['shortname'] , $parent['name']);
				else
					TPadd_linktree($scripturl.'?action=tpmod;dl=cat'.$parent['id'] , $parent['name']);
			}
		}

		// fetch the item data
		$item =	$context['TPortal']['item'] = $item;
		$context['TPortal']['dlitem'] = array();
		$request = $smcFunc['db_query']('', '
			SELECT dl.*, dl.author_id as authorID, m.real_name as realName
			FROM ({db_prefix}tp_dlmanager AS dl)
			LEFT JOIN {db_prefix}members AS m ON (m.id_member = dl.author_id)
			WHERE dl.type = {string:type}
			AND dl.id = {int:item}
			LIMIT 1',
			array('type' => 'dlitem', 'item' => $item)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$rows = $smcFunc['db_num_rows']($request);
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$subitem = $row['id'];
				$fetch = $smcFunc['db_query']('', '
					SELECT id, name, file, downloads, filesize, created, views
					FROM {db_prefix}tp_dlmanager
					WHERE type = {string:type}
					AND subitem = {int:sub}
					ORDER BY id DESC',
					array('type' => 'dlitem', 'sub' => $subitem)
				);
					
				if($smcFunc['db_num_rows']($fetch) > 0)
				{
					$fdata = array();
					while($frow = $smcFunc['db_fetch_assoc']($fetch))
					{
						if($context['TPortal']['dl_fileprefix'] == 'K')
							$ffs = ceil($row['filesize'] / 1000).' Kb';
						elseif($context['TPortal']['dl_fileprefix'] == 'M')
							$ffs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
						elseif($context['TPortal']['dl_fileprefix'] == 'G')
							$ffs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';
						
						$fdata[] = array(
							'id' => $frow['id'],
							'name' => $frow['name'],
							'file' => $frow['file'],
							'href' => $scripturl.'?action=tpmod;dl=get'.$frow['id'],
							'href2' => $scripturl.'?action=tpmod;dl=item'.$frow['id'],
							'downloads' => $frow['downloads'],
							'views' => $frow['views'],
							'created' => $frow['created'],
							'filesize' => $ffs,
						);
					}
					$smcFunc['db_free_result']($fetch);
				}
					
				if($context['TPortal']['dl_fileprefix'] == 'K')
					$fs = ceil($row['filesize'] / 1000).' Kb';
				elseif($context['TPortal']['dl_fileprefix'] == 'M')
					$fs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
				elseif($context['TPortal']['dl_fileprefix'] == 'G')
					$fs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';

				$rat = array();
				$rating_votes = 0;
				$rat = explode(',', $row['rating']);
				$rating_votes = count($rat);
				if($row['rating'] == '')
					$rating_votes = 0;

				$total = 0;
				foreach($rat as $mm => $mval)
					$total = $total+$mval;

				if($rating_votes > 0 && $total > 0)
					$rating_average = floor($total/$rating_votes);
				else
					$rating_average=0;

				$bigshot = $decideshot = !empty($row['screenshot']) ? $boardurl. '/' . $row['screenshot'] : ''; 
				// does it exist? 
				if(file_exists($boarddir . '/tp-images/dlmanager/listing/' . $row['screenshot']) && !empty($row['screenshot']))
					$decideshot = $boardurl. '/tp-images/dlmanager/listing/' . $row['screenshot']; 
				if(file_exists($boarddir . '/tp-images/dlmanager/' . $row['screenshot']) && !empty($row['screenshot']))
					$bigshot = $boardurl. '/tp-images/dlmanager/' . $row['screenshot']; 

				if($context['user']['is_logged'])
					$can_rate = in_array($context['user']['id'], explode(',', $row['voters'])) ? false : true;
				else
					$can_rate = false;
							
					$context['TPortal']['dlitem'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'description' => $context['TPortal']['dl_wysiwyg'] == 'bbc' ? parse_bbc(trim(strip_tags($row['description']))) : $row['description'],
					'category' => $row['category'],
					'file' => $row['file'],
					'href' => $scripturl.'?action=tpmod;dl=get'.$row['id'],
					'downloads' => $row['downloads'],
					'views' => $row['views'],
					'link' => $row['link'],
					'date_last' => $row['last_access'],
					'author' => $row['realName'],
					'authorID' => $row['authorID'],
					'screenshot' => $row['screenshot'],
					'sshot' => $decideshot,
					'bigshot' => $bigshot,
					'icon' => $row['icon'],
					'created' => $row['created'],
					'filesize' => $fs,
					'subitem' => isset($fdata) ? $fdata : '',
					'rating_votes' => $rating_votes,
					'rating_average' => $rating_average,
					'can_rate' => $can_rate,
					'global_tag' => $row['global_tag'],
				);
				$author = $row['authorID'];
				$parent_cat = $row['category'];
				$views = $row['views'];
				$itemname = $row['name'];
				$itemid = $row['id'];
				$context['page_title'] = $row['name'];
			}
			$smcFunc['db_free_result']($request);
			TPadd_linktree($scripturl.'?action=tpmod;dl=cat'.$parent_cat, $catname);
			TPadd_linktree($scripturl.'?action=tpmod;dl=item'.$itemid, $itemname);
			// update the views and last access!
			$views++;
			$now = time();
			$year = (int) date("Y",$now);
			$week = (int) date("W",$now);	
			// update weekly views
			$req=$smcFunc['db_query']('', '
				SELECT id FROM {db_prefix}tp_dldata 
				WHERE year = {int:year} 
				AND week = {int:week} 
				AND item = {int:item}',
				array('year' => $year, 'week' => $week, 'item' => $itemid)
			);
			if($smcFunc['db_num_rows']($req) > 0)
			{
				$row = $smcFunc['db_fetch_assoc']($req);
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_dldata 
					SET views = views + 1 
					WHERE id = {int:item}',
					array('item' => $row['id'])
				);
			}
			else
				$smcFunc['db_insert']('INSERT', 
					'{db_prefix}tp_dldata',
					array('week' => 'int', 'year' => 'int', 'views' => 'int', 'item' => 'int'),
					array($week, $year, 1, $itemid),
					array('id')
				);

			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_dlmanager 
				SET views = {int:views}, last_access = {int:last}
				WHERE id = {int:item}',
				array('views' => $views, 'last' => $now, 'item' => $itemid)
			);
			$context['TPortal']['dlheader'] = $itemname;
		}
	}
	elseif($context['TPortal']['dlaction'] == 'get')
		TPdownloadme();
	elseif($context['TPortal']['dlaction'] == 'stats')
		TPdlstats();
	elseif($context['TPortal']['dlaction'] == 'results')
		TPdlresults();
	elseif($context['TPortal']['dlaction'] == 'search')
		TPdlsearch();

	// For wireless, we use the Wireless template...
	if (WIRELESS)
	{
		loadTemplate('TPwireless');
		if($context['TPortal']['dlaction'] == 'item' || $context['TPortal']['dlaction']=='cat')
			$what = $context['TPortal']['dlaction'];
		else
			$what = 'main';

		$context['sub_template'] = WIRELESS_PROTOCOL . '_tp_dl_'. $what;
	}
	else
		loadTemplate('TPdlmanager');

}

// searched the files?
function TPdlresults()
{
	global $txt, $scripturl, $context, $user_info, $smcFunc;

	$start = 0;

	if(isset($_GET['p']) && is_numeric($_GET['p']))
		$start = $_GET['p'];
	
	checkSession('post');

	// nothing to search for?
	if(empty($_POST['dl_search']))
		fatal_error($txt['tp-nosearchentered']);

	// clean the search
	$what2 = str_replace(' ', '%', strip_tags($_POST['dl_search']));
	$what = strip_tags($_POST['dl_search']);

	if(!empty($_POST['dl_searcharea_name']))
		$usetitle = true;
	else
		$usetitle = false;
	if(!empty($_POST['dl_searcharea_desc']))
		$usebody = true;
	else
		$usebody = false;

	if($usetitle && !$usebody)
		$query = 'd.name LIKE \'%{raw:what}%\'';
	elseif(!$usetitle && $usebody)
		$query = 'd.description LIKE \'%{raw:what}%\'';
	elseif($usetitle && $usebody)
		$query = 'd.name LIKE \'%{raw:what}%\' OR d.description LIKE \'%{raw:what}%\'';
	else
		$query = 'd.name LIKE \'%{raw:what}%\'';

	$dlquery = '(FIND_IN_SET(' . implode(', access) OR FIND_IN_SET(', $user_info['groups']) . ', access))';
	
	// find out which categoies you ahve access to:
	$request=$smcFunc['db_query']('', '
		SELECT id FROM {db_prefix}tp_dlmanager 
		WHERE type = {string:type} 
		AND '. $dlquery,
		array('type' => 'dlcat')
	);
	$allowedcats = array();
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
			$allowedcats[] = $row['id'];
		$smcFunc['db_free_result']($request);
	}
	else
		$allowedcats[0] = -1;

	$context['TPortal']['dlsearchresults'] = array();
	$context['TPortal']['dlsearchterm'] = $what;
	
	// find how many first
	$check=$smcFunc['db_query']('', '
		SELECT COUNT(d.id)
		FROM {db_prefix}tp_dlmanager AS d
		WHERE '. $query .'
		AND type = {string:type}',
		array('type' => 'dlitem', 'what' => $what2)
	);
	$tt = $smcFunc['db_fetch_row']($check);
	$total = $tt[0];
	
	$request = $smcFunc['db_query']('substring', '
		SELECT d.id, d.created, d.type, d.downloads, d.name, SUBSTRING(d.description, 0, 100) as body, d.author_id as authorID, m.real_name as realName
		FROM {db_prefix}tp_dlmanager AS d
		LEFT JOIN {db_prefix}members as m ON d.author_id = m.id_member
		WHERE '. $query .'
		AND type = {string:type}
		ORDER BY d.created DESC LIMIT {int:start}, 15',
		array('type' => 'dlitem', 'what' => $what2, 'start' => $start)
	);
	// create pagelinks


	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row=$smcFunc['db_fetch_assoc']($request))
		{
			$row['name'] = preg_replace('/'.$what.'/', '<span class="highlight">'.$what.'</span>', $row['name']);
			$row['body'] = preg_replace('/'.$what.'/', '<span class="highlight">'.$what.'</span>', $row['body']);
			$row['body'] = strip_tags($row['body']);

			$context['TPortal']['dlsearchresults'][] = array(
				'id' => $row['id'],
				'type' => $row['type'],
				'date' => $row['created'],
				'downloads' => $row['downloads'],
				'name' => $row['name'],
				'body' => $row['body'],
				'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
			);
		}
		$smcFunc['db_free_result']($request);
	}
	TPadd_linktree($scripturl.'?action=tpmod;dl=search' , $txt['tp-dlsearch']);
}
// searched the files?
function TPdlsearch()
{
	global $txt, $scripturl;

	TPadd_linktree($scripturl.'?action=tpmod;dl=search', $txt['tp-dlsearch']);
}

// show some stats
function TPdlstats()
{
	global $scripturl, $smcFunc, $context;

	$context['TPortal']['dl_scats'] = array();
	$context['TPortal']['dl_sitems'] = array();
	$context['TPortal']['dl_scount'] = array();
	$context['TPortal']['topcats'] = array();
	// count items in each category
	$request = $smcFunc['db_query']('', '
		SELECT category FROM {db_prefix}tp_dlmanager 
		WHERE type = {string:type}',
		array('type' => 'dlitem')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row=$smcFunc['db_fetch_assoc']($request)){
			if($row['category'] > 0)
			{
				if(isset($context['TPortal']['dl_scount'][$row['category']]))
					$context['TPortal']['dl_scount'][$row['category']]++;
				else
					$context['TPortal']['dl_scount'][$row['category']] = 1;
			}
		}
		$smcFunc['db_free_result']($request);
	}

	//first : fetch all allowed categories
	$context['TPortal']['uploadcats'] = array();
	$request = $smcFunc['db_query']('', '
		SELECT id, parent, name, access 
		FROM {db_prefix}tp_dlmanager 
		WHERE type = {string:type}',
		array('type' => 'dlcat')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$show = get_perm($row['access'], 'tp_dlmanager');
			if($show)
				$context['TPortal']['uploadcats'][$row['id']] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'parent' => $row['parent'],
				);
		}
		$smcFunc['db_free_result']($request);
	}
	//no categories to select...
	else
		return;

	// fetch all categories with subcats
	$req = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_dlmanager 
		WHERE type = {string:type}',
		array('type' => 'dlcat')
	);
	if($smcFunc['db_num_rows']($req) > 0)
	{
		while($brow=$smcFunc['db_fetch_assoc']($req))
		{
    		if(get_perm($brow['access'], 'tp_dlmanager'))
			{
				if(isset($context['TPortal']['dl_scount'][$brow['id']]))
					$items = $context['TPortal']['dl_scount'][$brow['id']];
				else
					$items = 0;

				$context['TPortal']['topcats'][] = array(
					'items' => $items,
					'link' => '<a href="'.$scripturl.'?action=tpmod;dl=cat'.$brow['id'].'">'.$brow['name'].'</a>',
				);
				// add the category as viewable
				$context['TPortal']['viewcats'][] = $brow['id'];
			}
		}
		$smcFunc['db_free_result']($req);
		// sort it
    	if(sizeof($context['TPortal']['topcats']) > 1)
		usort($context['TPortal']['topcats'], 'dlsort');
	}

	// fetch all items
	$context['TPortal']['topitems'] = array();

	$request = $smcFunc['db_query']('', '
		SELECT category, filesize, views, downloads, id, name 
		FROM {db_prefix}tp_dlmanager 
		WHERE type = {string:type}',
		array('type' => 'dlitem')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			if(isset($context['TPortal']['viewcats']) && isset($row['category']) && is_array($context['TPortal']['viewcats']) && in_array($row['category'],$context['TPortal']['viewcats']))
				$context['TPortal']['topitems'][] = array(
					'size' => $row['filesize'],
					'views' => $row['views'],
					'downloads' => $row['downloads'],
					'link' => '<a href="'.$scripturl.'?action=tpmod;dl=item'.$row['id'].'">'.$row['name'].'</a>',
				);
		}
		$smcFunc['db_free_result']($request);
		// sort it by filesize,views and downloads
		$context['TPortal']['topsize'] = array();
		$context['TPortal']['topviews']=array();
		$context['TPortal']['topsize']=$context['TPortal']['topitems'];

		if(is_array($context['TPortal']['topsize']))
			usort($context['TPortal']['topsize'], "dlsortsize");

		$context['TPortal']['topviews']=$context['TPortal']['topitems'];

		if(is_array($context['TPortal']['topviews']))
			usort($context['TPortal']['topviews'], "dlsortviews");

		if(is_array($context['TPortal']['topitems']))
			usort($context['TPortal']['topitems'], "dlsortdownloads");
	}
}

// download a file
function TPdownloadme()
{
	global $smcFunc, $modSettings, $context, $boarddir;

	$item = $context['TPortal']['dlitem'];
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_dlmanager 
		WHERE id = {int:item} LIMIT 1',
		array('item' => $item)
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		$row = $smcFunc['db_fetch_assoc']($request);
		$myfilename = $row['name'];
		$newname = TPDlgetname($row['file']);
		$real_filename = $row['file'];
		if($row['subitem'] > 0)
		{
			$parent = $row['subitem'];
			$req3 = $smcFunc['db_query']('', '
				SELECT category FROM {db_prefix}tp_dlmanager 
				WHERE id = {int:parent} LIMIT 1',
				array('parent' => $parent)
			);
			$what = $smcFunc['db_fetch_assoc']($req3);
			$cat = $what['category'];
			$request2 = $smcFunc['db_query']('', '
				SELECT * FROM {db_prefix}tp_dlmanager 
				WHERE id = {int:cat}',
				array('cat' => $cat));
			if($smcFunc['db_num_rows']($request2) > 0)
			{
				$row2 = $smcFunc['db_fetch_assoc']($request2);
				$show = get_perm($row2['access'], 'tp_dlmanager');
				$smcFunc['db_free_result']($request2);
			}
		}
		else
		{
			$cat = $row['category'];
			$request2 = $smcFunc['db_query']('', '
				SELECT * FROM {db_prefix}tp_dlmanager 
				WHERE id = {int:cat}',
				array('cat' => $cat));
			if($smcFunc['db_num_rows']($request2) > 0)
			{
				$row2 = $smcFunc['db_fetch_assoc']($request2);
				$show = get_perm($row2['access'], 'tp_dlmanager');
				$smcFunc['db_free_result']($request2);
			}
		}
		$filename = $boarddir.'/tp-downloads/'.$real_filename;
		$smcFunc['db_free_result']($request);
	}
	else
		$show = false;

	// can we actually download?
	if($show == 1 || allowedTo('tp_dlmanager'))
	{
		$now = time();
		$year = (int) date("Y",$now);
		$week = (int) date("W",$now);	

		// update weekly views
		$req = $smcFunc['db_query']('', '
			SELECT id FROM {db_prefix}tp_dldata 
			WHERE year = {int:year}
			AND week = {int:week}
			AND item = {int:item}',
			array('year' => $year, 'week' => $week, 'item' => $item)
		);

		if($smcFunc['db_num_rows']($req) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($req);
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_dldata 
				SET downloads = downloads + 1 
				WHERE id = {int:dlitem}',
				array('dlitem' => $row['id'])
			);
		}
		else
			$smcFunc['db_insert']('INSERT',
				'{db_prefix}tp_dldata',
				array('week' => 'int', 'year' => 'int', 'downloads' => 'int', 'item' => 'int'),
				array($week, $year, 1, $item),
				array('id')
			);

		$smcFunc['db_query']('', '
			UPDATE LOW_PRIORITY {db_prefix}tp_dlmanager 
			SET downloads = downloads + 1 
			WHERE id = {int:item} LIMIT 1',
			array('item' => $item));
		ob_end_clean();
		if (!empty($modSettings['enableCompressedOutput']) && @version_compare(PHP_VERSION, '4.2.0') >= 0 && @filesize($filename) <= 4194304)
			@ob_start('ob_gzhandler');
		else
		{
			ob_start();
			header('Content-Encoding: none');
		}

		if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime(array_shift(explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE']))) >= filemtime($filename))
		{
			ob_end_clean();
			header('HTTP/1.1 304 Not Modified');
			exit;
		}

		// Send the attachment headers.
		header('Pragma: no-cache'); 
		header('Cache-Control: max-age=' . 10 . ', private');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		if (!$context['browser']['is_gecko'])
			header('Content-Transfer-Encoding: binary');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 525600 * 60) . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT');
		header('Accept-Ranges: bytes');
		header('Set-Cookie:');
		header('Connection: close');
		header('Content-Disposition: attachment; filename="' . $newname . '"');
		header('Content-Type: application/octet-stream');

		if (filesize($filename) != 0)
		{
			$size = @getimagesize($filename);
			if (!empty($size) && $size[2] > 0 && $size[2] < 4)
				header('Content-Type: image/' . ($size[2] != 1 ? ($size[2] != 2 ? 'png' : 'jpeg') : 'gif'));
		}

		if (empty($modSettings['enableCompressedOutput']) || filesize($filename) > 4194304)
			header('Content-Length: ' . filesize($filename));

		@set_time_limit(0);

		if (in_array(substr($real_filename, -4), array('.txt', '.css', '.htm', '.php', '.xml')))
		{
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows') !== false)
				$callback = create_function('$buffer', 'return preg_replace(\'~[\r]?\n~\', "\r\n", $buffer);');
			elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false)
				$callback = create_function('$buffer', 'return preg_replace(\'~[\r]?\n~\', "\r", $buffer);');
			else
				$callback = create_function('$buffer', 'return preg_replace(\'~\r~\', "\r\n", $buffer);');
		}

		// Since we don't do output compression for files this large...
		if (filesize($filename) > 4194304)
		{
			// Forcibly end any output buffering going on.
			if (function_exists('ob_get_level'))
			{
				while (@ob_get_level() > 0)
					@ob_end_clean();
			}
			else
			{
				@ob_end_clean();
				@ob_end_clean();
				@ob_end_clean();
			}

			$fp = fopen($filename, 'rb');
			while (!feof($fp))
			{
				if (isset($callback))
					echo $callback(fread($fp, 8192));
				else
					echo fread($fp, 8192);
				flush();
			}
			fclose($fp);
		}
		// On some of the less-bright hosts, readfile() is disabled.  It's just a faster, more byte safe, version of what's in the if.
		elseif (isset($callback) || @readfile($filename) == null)
			echo isset($callback) ? $callback(file_get_contents($filename)) : file_get_contents($filename);

		obExit(false);
	}
	else
		redirectexit('action=tpmod;dl');
}

// TinyPortal DLmanager admin
function TPortalDLAdmin()
{
	global $txt, $scripturl, $boarddir, $boardurl, $smcFunc, $context, $settings, $sourcedir;

	// check permissions
	if(isset($_POST['dl_useredit']))
		checkSession('post');
	else
		isAllowedTo('tp_dlmanager');

	// add visual options to this section
	$dl_visual = explode(',', $context['TPortal']['dl_visual_options']);
	$dv = array('left', 'right', 'center', 'top', 'bottom', 'lower');
	foreach($dv as $v => $val)
	{
		if(in_array($val,$dl_visual))
		{
			$context['TPortal'][$val.'panel'] = '1';
			$context['TPortal']['dl_'.$val] = '1';
		}
		else
			$context['TPortal'][$val.'panel'] = '0';
	}

	if(in_array('showtop',$dl_visual))
	{
		$context['TPortal']['showtop'] = true;
		$context['TPortal']['dl_top'] = true;
	}
	else
		$context['TPortal']['showtop'] = false;

	if($context['TPortal']['hidebars_admin_only'] == '1')
		tp_hidebars();
	
	// fetch membergroups so we can quickly set permissions
	// dlmanager, dlupload, dlcreatetopic
	$context['TPortal']['perm_all_groups'] = get_grps();
	$context['TPortal']['perm_groups'] = tp_fetchpermissions(array('tp_dlmanager', 'tp_dlupload', 'tp_dlcreatetopic'));
	$context['TPortal']['boards'] = tp_fetchboards();

	$context['TPortal']['all_dlitems'] = array();
	$request=$smcFunc['db_query']('', '
		SELECT id, name	FROM {db_prefix}tp_dlmanager
		WHERE type = {string:type}
		ORDER BY name ASC',
		array('type' => 'dlitem')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			$context['TPortal']['all_dlitems'][] = array(
				'id' => $row['id'],
				'name' => $row['name'],
			);
		}
		$smcFunc['db_free_result']($request);
	}
	
	// Add in BBC editor before we call in template so the headers are there
	if($context['TPortal']['dl_wysiwyg'] == 'bbc')
	{
		if ($context['TPortal']['dlsub'] == 'adminaddcat')
		{
			$context['TPortal']['editor_id'] = 'newdladmin_text';
			TP_prebbcbox($context['TPortal']['editor_id']);
		} 
		else
		{
			$context['TPortal']['editor_id'] = 'tp_dl_introtext';
			TP_prebbcbox($context['TPortal']['editor_id'], $context['TPortal']['dl_introtext']);
		}	
	}	
	
	// any items from the ftp screen?
	if(!empty($_POST['ftpdlsend']))
	{
		// new category?
		if(!empty($_POST['assign-ftp-newcat']))
		{
			$newcat = true;
			$newcatname = $_POST['assign-ftp-newcat'];
			if(isset($_POST['assign-ftp-cat']) && $_POST['assign-ftp-cat'] > 0)
				$newcatparent = $_POST['assign-ftp-cat'];
			else
				$newcatparent = 0;
			if($newcatname == '')
				$newcatname = '-no name-';
		}
		else
		{
			$newcat = false;
			$newcatname = '';
			$newcatnow = $_POST['assign-ftp-cat'];
			$newcatparent = 0;
		}

		// if new category create it first.
		if($newcat)
		{
			$request = $smcFunc['db_insert']('INSERT',
				'{db_prefix}tp_dlmanager',
				array(
					'name' => 'string',
					'description' => 'string', 
					'icon' => 'string',
					'category' => 'int',
					'type' => 'string',
					'downloads' => 'int',
					'views' => 'int',
					'file' => 'string',
					'created' => 'int',
					'last_access' => 'int',
					'filesize' => 'int',
					'parent' => 'int',
					'access' => 'string',
					'link' => 'string',
					'author_id' => 'int',
					'screenshot' => 'string',
					'rating' => 'string',
					'voters' => 'string',
					'subitem' => 'int'),
				array($newcatname, '', '', 0, 'dlcat', 0, 0, '', 0, 0, 0, $newcatparent, '', '', $context['user']['id'], '', '', '', 0),
				array('id')
			);
			$newcatnow = $smcFunc['db_insert_id']($request);
		}
		// now go through each file and put it into the table.
		foreach($_POST as $what => $value)
		{
			if(substr($what, 0, 19) == 'assign-ftp-checkbox')
			{
				$name = $value;
				$now = time();
				$fsize = filesize($boarddir.'/tp-downloads/'.$value);
				$smcFunc['db_insert']('INSERT', 
					'{db_prefix}tp_dlmanager',
					array(
					'name' => 'string',
					'description' => 'string', 
					'icon' => 'string',
					'category' => 'int',
					'type' => 'string',
					'downloads' => 'int',
					'views' => 'int',
					'file' => 'string',
					'created' => 'int',
					'last_access' => 'int',
					'filesize' => 'int',
					'parent' => 'int',
					'access' => 'string',
					'link' => 'string',
					'author_id' => 'int',
					'screenshot' => 'string',
					'rating' => 'string',
					'voters' => 'string',
					'subitem' => 'int'),
					array($name, '', '', $newcatnow, 'dlitem', 1, 1, $value, $now, $now, $fsize, 0, '', '', $context['user']['id'], '', '', '', 0),
					array('id')
				);
			}
		}
		// done, set a value to make member aware of assigned category
  		redirectexit('action=tpmod;dl=adminftp;ftpcat='.$newcatnow);
	}

	// check for new category
	if(!empty($_POST['newdlsend']))
	{
		// get the items
		$name = strip_tags($_POST['newdladmin_name']);
		// no html here
		if(empty($name))
			$name = $txt['tp-dlnotitle'];

		$text = $_POST['newdladmin_text'];
		$parent = $_POST['newdladmin_parent'];
		$icon = $boardurl.'/tp-downloads/icons/'.$_POST['newdladmin_icon'];
		// special case, the access
    	$dlgrp = array();
		foreach ($_POST as $what => $value)
		{
			if(substr($what, 0, 16) == 'newdladmin_group')
			{
				$vv = substr($what,16);
				if($vv != '-2')
				    $dlgrp[] = $vv;
			}
		}
		$access = implode(',', $dlgrp);
		// insert the category
		$request = $smcFunc['db_insert']('INSERT', 
			'{db_prefix}tp_dlmanager',
			array(
				'name' => 'string',
				'description' => 'string', 
				'icon' => 'string',
				'category' => 'int',
				'type' => 'string',
				'downloads' => 'int',
				'views' => 'int',
				'file' => 'string',
				'created' => 'int',
				'last_access' => 'int',
				'filesize' => 'int',
				'parent' => 'int',
				'access' => 'string',
				'link' => 'string',
				'author_id' => 'int',
				'screenshot' => 'string',
				'rating' => 'string',
				'voters' => 'string',
				'subitem' => 'int'),
			array($name, $text, $icon, 0, 'dlcat', 0, 0, '', 0, 0, 0, $parent, $access, '', $context['user']['id'], '', '', '', 0),
			array('id')
		);
		$newcat = $smcFunc['db_insert_id']($request);
		redirectexit('action=tpmod;dl=admineditcat'.$newcat);
	}

	$myid = 0;
	// check if tag links are present
	if(isset($_POST['dladmin_itemtags']))
	{
		$itemid = $_POST['dladmin_itemtags'];
		// get title
		$request = $smcFunc['db_query']('', '
			SELECT name FROM {db_prefix}tp_dlmanager 
			WHERE id = {int:item} LIMIT 1',
			array('item' => $itemid)
		);
		$title = $smcFunc['db_fetch_row']($request);
		// remove old ones first
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}tp_variables 
			WHERE value3 = {string:val3} 
			AND subtype2 = {int:sub}',
			array('val3' => 'dladmin_itemtags', 'sub' => $itemid)
		);
		$alltags = array();
		foreach($_POST as $what => $value)
		{
			// a tag from edit items
			if(substr($what, 0, 17) == 'dladmin_itemtags_')
			{
				$tag = substr($what, 17);
				$itemid = $value;
				// insert new one
				$href = '?action=tpmod;dl=item'.$itemid;
				$tg = '<span style="background: url('.$settings['tp_images_url'].'/glyph_download.png) no-repeat;" class="taglink">' . $title[0] . '</span>';
				if(!empty($tag))
				{
					$smcFunc['db_query']('INSERT', 
						'{db_prefix}tp_variables',
						array('value1' => 'string', 'value2' => 'string', 'value3' => 'string', 'type' => 'string', 'value4' => 'string', 'value5' => 'int', 'subtype' => 'string', 'value7' => 'string', 'value8' => 'string', 'subtype2' => 'int'),
						array($href, $tg, 'dladmin_itemtags', '', 0, $tag, '', '', $itemid),
						array('id')
					);
					$alltags[] = $tag;
				}
			}
		}
		$tg = implode(',', $alltags);
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}tp_dlmanager 
			SET global_tag = {string:tag} 
			WHERE id = {int:item}',
			array('tag' => $tg, 'item' => $itemid)
		);

		$myid = $itemid;
		$go = 2;
		$newgo = 2;
	}
	// check if tag links are present -categories
	if(isset($_POST['dladmin_cattags']))
	{
		$itemid = $_POST['dladmin_cattags'];
		// get title
		$request = $smcFunc['db_query']('', '
			SELECT name FROM {db_prefix}tp_dlmanager 
			WHERE id = {int:item} LIMIT 1',
			array('item' => $itemid)
		);
		$title = $smcFunc['db_fetch_row']($request);
		// remove old ones first
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}tp_variables 
			WHERE value3 = {string:val3} 
			AND subtype2 = {int:sub}',
			array('val3' => 'dladmin_cattags', 'sub' => $itemid));
		foreach($_POST as $what => $value)
		{
			// a tag from edit category
			if(substr($what, 0, 16) == 'dladmin_cattags_')
			{
				$tag = substr($what, 16);
				$itemid = $value;
				// insert new one
				$href = '?action=tpmod;dl=cat'.$itemid;
				$title = $title[0].' ['.strtolower($txt['tp-downloads']).'] ';
				$smcFunc['db_query']('INSERT', 
					'{db_prefix}tp_variables',
					array('value1' => 'string', 'value2' => 'string', 'value3' => 'string', 'type' => 'string', 'value4' => 'string', 'value5' => 'int', 'subtype' => 'string', 'value7' => 'string', 'value8' => 'string', 'subtype2' => 'int'), 
					array($href, $title, 'dladmin_cattags', '', 0, $tag, '', '', $itemid),
					array('id')
				);
			}
		}
		$myid = $itemid;
		$go = 3;
		$newgo = 3;
	}

	// check for access value
	if(!empty($_POST['dlsend']))
	{
 		$admgrp = array();
		$groupset = false;
 		$dlgrp = array();
		$dlset = false;
		$visual = array();
		$visualset = false;
        $creategrp = array();
		$dlmanager_grp = array();
		$dlupload_grp = array();
		$dlcreatetopic_grp = array();
		
		// Our settings array to send to updateTPSettings();
		$changeArray = array();
		
		foreach ($_POST as $what => $value) 
		{
			if(substr($what, 0, 13) == 'dladmin_group')
			{
			    $val = substr($what, 13);
				if($val != '-2')
			        $admgrp[] = $val;
			    $groupset = true;
			    $id = $value;
			}
			elseif(substr($what, 0, 8) == 'tp_group')
			{
				if($value != '-2')
			        $dlgrp[] = $value;
			    $dlset = true;
			}
			elseif(substr($what, 0, 20) == 'tp_dl_visual_options')
			{
				if($value != 'not')
			        $visual[] = $value;
			    $visualset = true;
			}
			elseif(substr($what, 0, 11) == 'tp_dlboards')
			    $creategrp[] = $value;
		}
		if($groupset)
		{
			$dlaccess = implode(',', $admgrp);
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_dlmanager 
				SET access = {string:access} 
				WHERE id = {int:item}',
				array('access' => $dlaccess, 'item' => $id)
			);
		}
		
		if(!empty($_POST['dlsettings']))
			$changeArray['dl_createtopic_boards'] = implode(',', $creategrp);

		if($dlset)
			$changeArray['dl_approve_groups'] = implode(',', $dlgrp);

		if($visualset)
			$changeArray['dl_visual_options'] = implode(',', $visual);

		$go = 0;

		if(!empty($_FILES['qup_dladmin_text']['tmp_name']) && (file_exists($_FILES['qup_dladmin_text']['tmp_name']) || is_uploaded_file($_FILES['qup_dladmin_text']['tmp_name'])))
		{
			$name = TPuploadpicture('qup_dladmin_text', $context['user']['id'].'uid');
			tp_createthumb('tp-images/'.$name, 50, 50, 'tp-images/thumbs/thumb_'.$name);
		}
		if(!empty($_FILES['qup_blockbody']['tmp_name']) && (file_exists($_FILES['qup_dladmin_text']['tmp_name']) || is_uploaded_file($_FILES['qup_dladmin_text']['tmp_name'])))
		{
			$name = TPuploadpicture('qup_dladmin_text', $context['user']['id'].'uid');
			tp_createthumb('tp-images/'.$name, 50, 50, 'tp-images/thumbs/thumb_'.$name);
		}

		// a screenshot from edit item screen?
		if(!empty($_FILES['tp_dluploadpic_edit']['tmp_name']) && (file_exists($_FILES['tp_dluploadpic_edit']['tmp_name']) || is_uploaded_file($_FILES['tp_dluploadpic_edit']['tmp_name'])))
			$shot = true;
		else
			$shot = false;

		if($shot)
		{
			$sid = $_POST['tp_dluploadpic_editID'];		
			$sfile = 'tp_dluploadpic_edit';
			$uid = $context['user']['id'].'uid';
			$dim = '1800';
			$suf = 'jpg,gif,png';
			$dest = 'tp-images/dlmanager';
			$sname = TPuploadpicture($sfile, $uid, $dim, $suf, $dest);
			$screenshot = $sname;
			
			tp_createthumb($dest.'/'.$sname, $context['TPortal']['dl_screenshotsize'][0],$context['TPortal']['dl_screenshotsize'][1], $dest.'/thumb/'.$sname);
			tp_createthumb($dest.'/'.$sname, $context['TPortal']['dl_screenshotsize'][2],$context['TPortal']['dl_screenshotsize'][3], $dest.'/listing/'.$sname);
			tp_createthumb($dest.'/'.$sname, $context['TPortal']['dl_screenshotsize'][4],$context['TPortal']['dl_screenshotsize'][5], $dest.'/single/'.$sname);
			
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_dlmanager 
				SET screenshot = {string:ss} 
				WHERE id = {int:item}',
				array('ss' => $screenshot, 'item' => $sid)
			);
			$uploaded = true;
		}
		else{
			$screenshot = '';
			$uploaded = false;
		}

		if(isset($_POST['tp_dluploadpic_link']) && !$uploaded)
		{
			$sid = $_POST['tp_dluploadpic_editID'];
			$screenshot = $_POST['tp_dluploadpic_link'];
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_dlmanager 
				SET screenshot = {string:ss} 
				WHERE id = {int:item}',
				array('ss' => $screenshot, 'item' => $sid)
			);
		}
		else
			$screenshot = '';

		// a new file uploaded?
		if(!empty($_FILES['tp_dluploadfile_edit']['tmp_name']) && is_uploaded_file($_FILES['tp_dluploadfile_edit']['tmp_name']))
		{	
			$shot = true;
		}
		else
			$shot = false;

		if($shot)
		{
			$sid = $_POST['tp_dluploadfile_editID'];
			$shotname = $_FILES['tp_dluploadfile_edit']['name'];
			$sname = strtr($shotname, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
			$sname = strtr($sname, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
			$sname = preg_replace(array('/\s/', '/[^\w_\.\-]/'), array('_', ''), $sname);
			$sname = time().$sname;
			// check the size
			$dlfilesize = filesize($_FILES['tp_dluploadfile_edit']['tmp_name']);
			if($dlfilesize > (1000 * $context['TPortal']['dl_max_upload_size']))
			{
				unlink($_FILES['tp_dluploadfile_edit']['tmp_name']);
				$error = $txt['tp-dlmaxerror'].' '.($context['TPortal']['dl_max_upload_size']).' Kb<br /><br />'.$txt['tp-dlmaxerror2'].': '. ceil($dlfilesize/1000) .' Kb';
				fatal_error($error);
			}

			// check the extension
			$allowed=explode(',', $context['TPortal']['dl_allowed_types']);
			$match = false;
			foreach($allowed as $extension => $value)
			{
				$ext = '.'.$value;
				$extlen = strlen($ext);
				if(substr($sname, strlen($sname) - $extlen, $extlen) == $ext)
					$match = true;
			}
			if(!$match)
			{
				unlink($_FILES['tp_dluploadfile_edit']['tmp_name']);
				$error = $txt['tp-dlexterror'].':<b> <br />'.$context['TPortal']['dl_allowed_types'].'</b><br /><br />'.$txt['tp-dlexterror2'].': <b>'.$sname.'</b>';
				fatal_error($error);
			}
			$success2 = move_uploaded_file($_FILES['tp_dluploadfile_edit']['tmp_name'],$boarddir.'/tp-downloads/'.$sname);
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_dlmanager 
				SET file = {string:file} 
				WHERE id = {int:item}',
				array('file' => $sname, 'item' => $sid)
			);
			$new_upload = true;
			// update filesize as well
			$value = filesize($boarddir.'/tp-downloads/'.$sname);
			if(!is_numeric($value))
				$value = 0;
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_dlmanager 
				SET filesize = {int:size}
				WHERE id = {int:item}',
				array('size' => $value, 'item' => $sid)
			);
			$myid = $sid;
			$go = 2;
		}
		// get all values from forms
		foreach($_POST as $what => $value)
		{
			if(substr($what,0,12)=='dladmin_name')
			{
				$id = substr($what, 12);
				// no html here
				$value = strip_tags($value);
				if(empty($value))
					$value = '-no title-';
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_dlmanager 
					SET name = {string:name} 
					WHERE id = {int:item}',
					array('name' => $value, 'item' => $id)
				);
			}
			elseif(substr($what, 0, 12) == 'dladmin_icon')
			{
				$id = substr($what, 12);
				if($value != '')
				{
					$val = $boardurl.'/tp-downloads/icons/'.$value;
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_dlmanager 
						SET icon = {string:icon} 
						WHERE id = {int:item}',
						array('icon' => $val, 'item' => $id)
					);
				}
			}
			elseif(substr($what, 0, 12) == 'dladmin_text')
			{
				$id = substr($what, 12);
				if(is_numeric($id))
				{
					// If we came from WYSIWYG then turn it back into BBC regardless.
					if (!empty($_REQUEST[$what.'_mode']) && isset($_REQUEST[$what]))
					{
						require_once($sourcedir . '/Subs-Editor.php');
						$_REQUEST[$what] = html_to_bbc($_REQUEST[$what]);
						// We need to unhtml it now as it gets done shortly.
						$_REQUEST[$what] = un_htmlspecialchars($_REQUEST[$what]);
						// We need this for everything else.
						$value = $_POST[$what] = $_REQUEST[$what];

					}					
					if(isset($_POST['dladmin_text'.$id.'_pure']) && isset($_POST['dladmin_text'.$id.'_choice']))
					{
						if($_POST['dladmin_text'.$id.'_choice'] == 1)
							$value = $_POST['dladmin_text'.$id];
						else
							$value = $_POST['dladmin_text'.$id.'_pure'];
					}
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_dlmanager 
						SET description = {string:desc} 
						WHERE id = {int:item}',
						array('desc' => $value, 'item' => $id)
					);
				}
			}
			elseif(substr($what, 0, 14) == 'dladmin_delete')
			{
				$id = substr($what, 14);
					$request = $smcFunc['db_query']('', '
						SELECT * FROM {db_prefix}tp_dlmanager 
						WHERE id = {int:item}',
						array('item' => $id)
					);
					if($smcFunc['db_num_rows']($request) > 0)
					{
						$row = $smcFunc['db_fetch_assoc']($request);
						if ($row['type'] == 'dlitem')
						{
							$category = $row['category'];
							if ($category > 0)
							{
								$smcFunc['db_query']('', '
									UPDATE {db_prefix}tp_dlmanager 
									SET downloads = downloads - 1 
									WHERE id = {int:cat} LIMIT 1',
									array('cat' => $category)
								);
							}
							// delete both screenshot and file
							if(!empty($row['file']) && file_exists($boarddir.'/tp-downloads/'.$row['file']))
							{
								$succ = unlink($boarddir.'/tp-downloads/'.$row['file']);
								if(!$succ)
									$err = $txt['tp-dlfilenotdel'] . ' ('.$row['file'].')';
							}
							if(!empty($row['screenshot']) && file_exists($boarddir.'/'.$row['screenshot']))
							{
								$succ2 = unlink($boarddir.'/'.$row['screenshot']);
								if(!$succ2)
									$err .= '<br />' . $txt['tp-dlssnotdel'] . ' ('.$row['screenshot'].')';
							}
	
						}
						$smcFunc['db_free_result']($request);
					}
				$smcFunc['db_query']('', '
					DELETE FROM {db_prefix}tp_dlmanager 
					WHERE id = {int:item}',
					array('item' => $id)
				);
				if(isset($err))
					fatal_error($err);
				redirectexit('action=tpmod;dl=admincat'.$category);
			}
			elseif(substr($what, 0, 15) == 'dladmin_approve' && $value == 'ON')
			{
				$id = abs(substr($what, 15));
				$request = $smcFunc['db_query']('', '
					SELECT category FROM {db_prefix}tp_dlmanager 
					WHERE id = {int:item}',
					array('item' => $id)
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					$row = $smcFunc['db_fetch_row']($request);
					$newcat = abs($row[0]);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_dlmanager 
						SET category = {int:cat} 
						WHERE id = {int:item}',
						array('cat' => $newcat, 'item' => $id)
					);
					$smcFunc['db_query']('', '
						DELETE FROM {db_prefix}tp_variables 
						WHERE type = {string:type} 
						AND value5 = {int:val5}',
						array('type' => 'dl_not_approved', 'val5' => $id)
					);
					$smcFunc['db_free_result']($request);
				}
			}
			elseif(substr($what, 0, 16) == 'dl_admin_approve' && $value == 'ON')
			{
				$id = abs(substr($what, 16));
				$request = $smcFunc['db_query']('', '
					SELECT category FROM {db_prefix}tp_dlmanager 
					WHERE id = {int:item}',
					array('item' => $id)
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					$row = $smcFunc['db_fetch_row']($request);
					$newcat = abs($row[0]);
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_dlmanager 
						SET category = {int:cat} 
						WHERE id = {int:item}',
						array('cat' => $newcat, 'item' => $id)
					);
					$smcFunc['db_query']('', '
						DELETE FROM {db_prefix}tp_variables 
						WHERE type = {string:type} 
						AND value5 = {int:val5}',
						array('type' => 'dl_not_approved', 'val5' => $id)
					);
					$smcFunc['db_free_result']($request);
				}
			}
			elseif(substr($what, 0, 16) == 'dladmin_category')
			{
				$id = substr($what, 16);
				// update, but not on negative values :)
				if($value>0)
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_dlmanager 
						SET category = {int:cat} 
						WHERE id = {int:item}',
						array('cat' => $value, 'item' => $id)
					);
			}
			elseif(substr($what, 0, 14) == 'dladmin_parent')
			{
				$id = substr($what, 14);
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_dlmanager 
					SET parent = {int:parent}
					WHERE id = {int:item}',
					array('parent' => $value, 'item' => $id)
				);
			}
			elseif(substr($what, 0, 15) == 'dladmin_subitem')
			{
				$id = substr($what, 15);
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_dlmanager 
					SET subitem = {int:sub}
					WHERE id = {int:item}',
					array('sub' => $value, 'item' => $id)
				);
			}
			elseif(substr($what, 0, 11) == 'tp_dlcatpos')
			{
				$id = substr($what, 11);
				if(!empty($_POST['admineditcatval']))
				{
					$myid = $_POST['admineditcatval'];
					$go = 4;
				}
	
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_dlmanager 
					SET downloads = {int:down}
					WHERE id = {int:item}',
					array('down' => $value, 'item' => $id)
				);
			}
			elseif(substr($what, 0, 18) == 'dladmin_screenshot')
			{
				$id = substr($what, 18);
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_dlmanager 
					SET screenshot = {string:ss} 
					WHERE id = {int:item}',
					array('ss' => $value, 'item' => $id)
				);
			}
			elseif(substr($what, 0, 12) == 'dladmin_link')
			{
				$id = substr($what, 12);
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_dlmanager 
					SET link = {string:link} 
					WHERE id = {int:item}',
					array('link' => $value, 'item' => $id)
				);
			}
			elseif(substr($what, 0, 12) == 'dladmin_file' && !isset($new_upload))
			{
				$id = substr($what, 12);
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_dlmanager 
					SET file = {string:file}
					WHERE id = {int:item}',
					array('file' => $value, 'item' => $id)
				);
				$myid = $id;
				$go = 2;
			}
			elseif(substr($what, 0, 12) == 'dladmin_size' && !isset($new_upload))
			{
				$id = substr($what, 12);
				// check the actual size
				$name = $_POST['dladmin_file'.$id];
				$value = filesize($boarddir.'/tp-downloads/'.$name);
				if(!is_numeric($value))
					$value = 0;
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_dlmanager 
					SET filesize = {int:size}
					WHERE id = {int:item}',
					array('size' => $value, 'item' => $id)
				);
			}
			// from settings in DLmanager
			elseif($what=='tp_dl_allowed_types')
			{
				$changeArray['dl_allowed_types'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dl_usescreenshot')
			{
				$changeArray['dl_usescreenshot'] = $value;
				$go = 1;
			}
			elseif(substr($what, 0, 20) == 'tp_dl_screenshotsize')
			{
				// which one
				$who = substr($what, 20);
				$result = $smcFunc['db_query']('', '
					SELECT value FROM {db_prefix}tp_settings 
					WHERE name = {string:name} LIMIT 1',
					array('name' => 'dl_screenshotsizes')
				);
				$row = $smcFunc['db_fetch_assoc']($result);
				$smcFunc['db_free_result']($result);
				$all = explode(',', $row['value']);
				$all[$who] = $value;

				$changeArray['dl_screenshotsizes'] = implode(',', $all);
				$go = 1;
			}
			elseif($what == 'tp_dl_showfeatured')
			{
				$changeArray['dl_showfeatured'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dl_wysiwyg')
			{
				$changeArray['dl_wysiwyg'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dl_showrecent')
			{
				$changeArray['dl_showlatest'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dl_showstats')
			{
				$changeArray['dl_showstats'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dl_showcategorytext')
			{
				$changeArray['dl_showcategorylist'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dl_featured')
			{
				$changeArray['dl_featured'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dl_introtext')
			{
				if($context['TPortal']['dl_wysiwyg'] == 'bbc')
				{
					// If we came from WYSIWYG then turn it back into BBC regardless.
					if (!empty($_REQUEST['tp_dl_introtext']) && isset($_REQUEST['tp_dl_introtext']))
					{
						require_once($sourcedir . '/Subs-Editor.php');
						$_REQUEST['tp_dl_introtext'] = html_to_bbc($_REQUEST['tp_dl_introtext']);
						// We need to unhtml it now as it gets done shortly.
						$_REQUEST['tp_dl_introtext'] = un_htmlspecialchars($_REQUEST['tp_dl_introtext']);
						// We need this for everything else.
						$value = $_POST['tp_dl_introtext'] = $_REQUEST['tp_dl_introtext'];
					}
				}
				$changeArray['dl_introtext'] = trim($value);
				$go = 1;
			}
		
			elseif($what == 'tp_dluploadsize')
			{
				$changeArray['dl_max_upload_size'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dl_approveonly')
			{
				$changeArray['dl_approve'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dlallowupload')
			{
				$changeArray['dl_allow_upload'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dl_fileprefix')
			{
				$changeArray['dl_fileprefix'] = $value;
				$go = 1;
			}
			elseif($what == 'tp_dltheme')
			{
				$changeArray['dlmanager_theme'] = $value;
				$go = 1;
			}
		}
		
		// Update all the changes settings finally
		updateTPSettings($changeArray);
		
		// if we came from useredit screen..
		if(isset($_POST['dl_useredit']))
		   redirectexit('action=tpmod;dl=useredit'.$_POST['dl_useredit']);
		
		if(!empty($newgo))
			$go = $newgo;
		// guess not, admin screen then
		if($go == 1)
			redirectexit('action=tpmod;dl=adminsettings');
		elseif($go == 2)
			redirectexit('action=tpmod;dl=adminitem'.$myid);
		elseif($go == 3)
			redirectexit('action=tpmod;dl=admineditcat'.$myid);
		elseif($go == 4)
			redirectexit('action=tpmod;dl=admincat'.$myid);
	}
	// ****************

	TP_dlgeticons();
	// get all themes
    $context['TPthemes'] = array();
	$request = $smcFunc['db_query']('', '
		SELECT value AS name, id_theme as ID_THEME
		FROM {db_prefix}themes
		WHERE variable = {string:var}
		AND id_member = {int:id_mem}
		ORDER BY value ASC',
		array('var' => 'name', 'id_mem' => 0)
	);
    if($smcFunc['db_num_rows']($request) > 0)
	{
    	while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$context['TPthemes'][] = array(
				'id' => $row['ID_THEME'],
				'name' => $row['name']
			);
		}
		$smcFunc['db_free_result']($request);
    }

	// fetch all files from tp-downloads
	$context['TPortal']['tp-downloads'] = array();
	$count = 1;
	if ($handle = opendir($boarddir.'/tp-downloads'))
	{
		while (false !== ($file = readdir($handle)))
		{
			if($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'icons')
			{
				$size = (floor(filesize($boarddir.'/tp-downloads/'.$file) / 102.4) / 10);
				$context['TPortal']['tp-downloads'][$count] = array(
					'id' => $count,
					'file' => $file,
					'size' => $size,
				);
				$count++;
			}
		}
		closedir($handle);
	}
   // get all membergroups for permissions
	$context['TPortal']['dlgroups'] = get_grps(true,true);

	//fetch all categories
	$sorted = array();
	$context['TPortal']['linkcats'] = array();
	$srequest = $smcFunc['db_query']('', '
		SELECT id, name, description, icon, access, parent 
		FROM {db_prefix}tp_dlmanager 
		WHERE type = {string:type} ORDER BY downloads ASC',
		array('type' => 'dlcat')
	);
	if($smcFunc['db_num_rows']($srequest) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($srequest))
		{
			// for the linktree
			$context['TPortal']['linkcats'][$row['id']] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'parent' => $row['parent'],
			);

			$sorted[$row['id']] = array(
				'id' => $row['id'],
				'parent' => $row['parent'],
				'name' => $row['name'],
				'text' => $row['description'],
				'icon' => $row['icon'],
			);
		}
			$smcFunc['db_free_result']($srequest);
	}
	// sort them
	if(count($sorted) > 1)
		$context['TPortal']['admuploadcats'] = chain('id', 'parent', 'name', $sorted);
	else
		$context['TPortal']['admuploadcats'] = $sorted;

	$context['TPortal']['dl_admcats'] = array();
	$context['TPortal']['dl_admcats2'] = array();
	$context['TPortal']['dl_admitems'] = array();
	$context['TPortal']['dl_admcount'] = array();
	$context['TPortal']['dl_admsubmitted'] = array();
	$context['TPortal']['dl_allitems'] = array();
	// count items in each category
	$request = $smcFunc['db_query']('', '
		SELECT file, category 
		FROM {db_prefix}tp_dlmanager 
		WHERE type = {string:type}',
		array('type' => 'dlitem')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			if($row['category'] < 0)
			{
				if(isset($context['TPortal']['dl_admsubmitted'][abs($row['category'])]))
					$context['TPortal']['dl_admsubmitted'][abs($row['category'])]++;
				else
					$context['TPortal']['dl_admsubmitted'][abs($row['category'])] = 1;
			}
			else{
				if(isset($context['TPortal']['dl_admcount'][$row['category']]))
					$context['TPortal']['dl_admcount'][$row['category']]++;
				else
					$context['TPortal']['dl_admcount'][$row['category']] = 1;
			}
			$context['TPortal']['dl_allitems'][] = $row['file'];
		}
		$smcFunc['db_free_result']($request);
	}

	// fetch all categories
	$admsub = substr($context['TPortal']['dlsub'], 5);
	if($admsub == '')
	{
		$context['TPortal']['dl_title'] = $txt['tp-dladmin'];
		// fetch all categories with subcats
		$req = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_dlmanager 
			WHERE type = {string:type} 
			ORDER BY downloads ASC',
			array('type' => 'dlcat')
		);
		if($smcFunc['db_num_rows']($req) > 0)
		{
			while($brow = $smcFunc['db_fetch_assoc']($req))
			{
				if(isset($context['TPortal']['dl_admcount'][$brow['id']]))
					$items = $context['TPortal']['dl_admcount'][$brow['id']];
				else
					$items = 0;

				if(isset($context['TPortal']['dl_admsubmitted'][$brow['id']]))
					$sitems = $context['TPortal']['dl_admsubmitted'][$brow['id']];
				else
					$sitems = 0;

				$context['TPortal']['admcats'][] = array(
					'id' => $brow['id'],
					'name' => $brow['name'],
					'icon' => $brow['icon'],
					'access' => $brow['access'],
					'parent' => $brow['parent'],
					'description' => $brow['description'],
					'shortname' => $brow['link'],
					'items' => $items,
					'submitted' => $sitems,
					'total' => ($items + $sitems),
					'href' => $scripturl.'?action=tpmod;dl=admincat'.$brow['id'],
					'href2' => $scripturl.'?action=tpmod;dl=admineditcat'.$brow['id'],
					'href3' => $scripturl.'?action=tpmod;dl=admindelcat'.$brow['id'],
					'pos' => $brow['downloads'],
				);
			}
			$smcFunc['db_free_result']($req);
		}
	}
	elseif(substr($admsub, 0, 3) == 'cat')
	{
		$cat = substr($admsub, 3);
		// get the parent first
		$request = $smcFunc['db_query']('', '
			SELECT parent, name, link 
			FROM {db_prefix}tp_dlmanager 
			WHERE type = {string:type}
			AND id = {int:item}',
			array('type' => 'dlcat', 'item' => $cat)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			$catparent = abs($row['parent']);
			$catname = $row['name'];
			$catshortname = $row['link'];
			$smcFunc['db_free_result']($request);
		}

		// fetch items within a category
		$request = $smcFunc['db_query']('', '
			SELECT dl.*, dl.author_id as authorID,m.real_name as realName
			FROM ({db_prefix}tp_dlmanager AS dl, {db_prefix}members AS m)
			WHERE abs(dl.category) = {int:cat}
			AND dl.type = {string:type}
			AND dl.subitem = {int:sub}
			AND dl.author_id = m.id_member
			ORDER BY dl.id DESC',
			array('cat' => $cat, 'type' => 'dlitem', 'sub' => 0)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$context['TPortal']['dl_admitems'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'icon' => $row['icon'],
					'category' => abs($row['category']),
					'file' => $row['file'],
					'filesize' => floor($row['filesize'] / 1024),
					'views' => $row['views'],
					'authorID' => $row['authorID'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
					'created' => timeformat($row['created']),
					'last_access' => timeformat($row['last_access']),
					'description' => $row['description'],
					'downloads' => $row['downloads'],
					'sshot' => $row['screenshot'],
					'link' => $row['link'],
					'href' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
					'approved' => $row['category']<0 ? '0' : '1',
					'approve' => $scripturl.'?action=tpmod;dl=adminapprove'.$row['id'],
				);
			}
			$smcFunc['db_free_result']($request);
		}
		// fetch all categories with subcats
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_dlmanager 
			WHERE type = {string:type}
			ORDER BY name ASC',
			array('type' => 'dlcat')
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				if(isset($context['TPortal']['dl_admcount'][$row['id']]))
					$items = $context['TPortal']['dl_admcount'][$row['id']];
				else
					$items = 0;

				if(isset($context['TPortal']['dl_admsubmitted'][$row['id']]))
					$sitems = $context['TPortal']['dl_admsubmitted'][$row['id']];
				else
					$sitems = 0;

				$context['TPortal']['admcats'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'pos' => $row['downloads'],
					'icon' => $row['icon'],
					'shortname' => $row['link'],
					'access' => $row['access'],
					'parent' => $row['parent'],
					'description' => $row['description'],
					'items' => $items,
					'submitted' => $sitems,
					'total' => ($items + $sitems),
					'href' => $scripturl.'?action=tpmod;dl=admincat'.$row['id'],
					'href2' => $scripturl.'?action=tpmod;dl=admineditcat'.$row['id'],
					'href3' => $scripturl.'?action=tpmod;dl=admindelcat'.$row['id'],
				);
			}
			$smcFunc['db_free_result']($request);
		}
		// check to see if its child
		$parents = array();
		while($catparent > 0)
		{
			$parents[$catparent] = array(
				'id' => $catparent,
				'name' => $context['TPortal']['linkcats'][$catparent]['name'],
				'parent' => $context['TPortal']['linkcats'][$catparent]['parent']
			);
			$catparent = $context['TPortal']['linkcats'][$catparent]['parent'];
		}

		// make the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=admin', $txt['tp-dladmin']);

		if(isset($parents))
		{
			$parts = array_reverse($parents, TRUE);
			// add to the linktree
			foreach($parts as $parent)
			{
				TPadd_linktree($scripturl.'?action=tpmod;dl=admincat'.$parent['id'] , $parent['name']);
			}
		}
		// add to the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=admincat'.$cat , $catname);
	}
	elseif($context['TPortal']['dlsub'] == 'adminsubmission')
	{
		// check any submissions if admin
		$submitted = array();
		isAllowedTo('tp_dlmanager');
		$context['TPortal']['dl_admitems'] = array();
		$request = $smcFunc['db_query']('', '
			SELECT dl.id, dl.name, dl.file, dl.created, dl.filesize, dl.author_id as authorID, m.real_name as realName
			FROM ({db_prefix}tp_dlmanager AS dl, {db_prefix}members AS m)
			WHERE dl.type = {string:type}
			AND dl.category < 0
			AND dl.author_id = m.id_member',
			array('type' => 'dlitem')
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$rows = $smcFunc['db_num_rows']($request);
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$context['TPortal']['dl_admitems'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'file' => $row['file'],
					'filesize' => floor($row['filesize'] / 1024),
					'href' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
					'date' => timeformat($row['created']),
				);
				$submitted[] = $row['id'];
			}
			$smcFunc['db_free_result']($request);
		}
		// check that submissions link to downloads
		$request = $smcFunc['db_query']('', '
			SELECT id,value5 FROM {db_prefix}tp_variables 
			WHERE type = {string:type}',
			array('type' => 'dl_not_approved')
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$what = $row['id'];
				if(!in_array($row['value5'], $submitted))
					$smcFunc['db_query']('', '
						DELETE FROM {db_prefix}tp_variables 
						WHERE id = {int:item}',
						array('item' => $what)
					);
			}
			$smcFunc['db_free_result']($request);
		}
	}
	elseif(substr($admsub, 0, 7) == 'editcat')
	{
		$context['TPortal']['dl_title'] = '<a href="'.$scripturl.'?action=tpmod;dl=admin">'.$txt['tp-dladmin'].'</a>';
		$cat = substr($admsub, 7);
		// edit category
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_dlmanager 
			WHERE id = {int:item} 
			AND type = {string:type} LIMIT 1',
			array('item' => $cat, 'type' => 'dlcat')
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$context['TPortal']['admcats'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'access' => $row['access'],
					'shortname' => $row['link'],
					'description' => $row['description'],
					'icon' => $row['icon'],
					'parent' => $row['parent'],
				);
			}
			$smcFunc['db_free_result']($request);
		}
		
		if($context['TPortal']['dl_wysiwyg'] == 'bbc')
		{
			$context['TPortal']['editor_id'] = 'dladmin_text'.$context['TPortal']['admcats'][0]['id'];
			TP_prebbcbox($context['TPortal']['editor_id'], $context['TPortal']['admcats'][0]['description']);
		}		
	}
	elseif(substr($admsub, 0, 6) == 'delcat')
	{
		$context['TPortal']['dl_title'] = '<a href="'.$scripturl.'?action=tpmod;dl=admin">'.$txt['tp-dladmin'].'</a>';
		$cat = substr($admsub, 6);
		// delete category and all item it's in
		$request = $smcFunc['db_query']('', '
			DELETE FROM {db_prefix}tp_dlmanager 
			WHERE type = {string:type}
			AND category = {int:cat}',
			array('type' => 'dlitem', 'cat' => $cat)
		);
		$request = $smcFunc['db_query']('', '
			DELETE FROM {db_prefix}tp_dlmanager 
			WHERE id = {int:cat} LIMIT 1',
			array('cat' => $cat)
		);
		redirectexit('action=tpmod;dl=admin');
	}
	elseif(substr($admsub, 0, 8) == 'settings')
	{
		$context['TPortal']['dl_title'] = $txt['tp-dlsettings'];
	}
	elseif(substr($admsub, 0, 4) == 'item')
	{
		$item = substr($admsub, 4);
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_dlmanager 
			WHERE id = {int:item} 
			AND type = {string:type} LIMIT 1',
			array('item' => $item, 'type' => 'dlitem')
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);

			// is it actually a subitem?
			if($row['subitem']>0)
				redirectexit('action=tpmod;dl=adminitem'.$row['subitem']);

			// Add in BBC editor before we call in template so the headers are there
			if($context['TPortal']['dl_wysiwyg'] == 'bbc')
			{
				$context['TPortal']['editor_id'] = 'dladmin_text' . $item;
				TP_prebbcbox($context['TPortal']['editor_id'], $row['description']); 			
			}			

			// get all items for a list
			$context['TPortal']['admitems'] = array();
			$itemlist = $smcFunc['db_query']('', '
				SELECT id, name FROM {db_prefix}tp_dlmanager 
				WHERE id != {int:item} 
				AND type = {string:type} 
				AND subitem = 0 
				ORDER BY name ASC',
				array('item' => $item, 'type' => 'dlitem')
			);
			if($smcFunc['db_num_rows']($itemlist) > 0)
			{
				while($ilist = $smcFunc['db_fetch_assoc']($itemlist))
				{
					$context['TPortal']['admitems'][] = array(
						'id' => $ilist['id'],
						'name' => $ilist['name'],
					);
				}
			}

			// Any additional files then..?
			$subitem = $row['id'];
			$fdata = array();
			$fetch = $smcFunc['db_query']('', '
				SELECT id, name, file, downloads, filesize, created
				FROM {db_prefix}tp_dlmanager
				WHERE type = {string:type}
				AND subitem = {int:sub}',
				array('type' => 'dlitem', 'sub' => $subitem)
			);
					
			if($smcFunc['db_num_rows']($fetch) > 0)
			{
				while($frow = $smcFunc['db_fetch_assoc']($fetch))
				{
					if($context['TPortal']['dl_fileprefix'] == 'K')
						$ffs = ceil($row['filesize']/ 1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix'] == 'M')
						$ffs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix'] == 'G')
						$ffs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';
							
					$fdata[] = array(
						'id' => $frow['id'],
						'name' => $frow['name'],
						'file' => $frow['file'],
						'href' => $scripturl.'?action=tpmod;dl=item'.$frow['id'],
						'downloads' => $frow['downloads'],
						'created' => $frow['created'],
						'filesize' => $ffs,
					);
				}
				$smcFunc['db_free_result']($fetch);
			}
			if (!empty($row['screenshot']))
			{
				if(substr($row['screenshot'], 0, 10) == 'tp-images/')
					$sshot = $boardurl.'/'.$row['screenshot'];
				else
				$sshot = $boardurl.'/tp-images/dlmanager/listing/'.$row['screenshot'];
			}

			$context['TPortal']['dl_admitems'][] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'icon' => $row['icon'],
				'category' => $row['category'],
				'file' => $row['file'],
				'views' => $row['views'],
				'authorID' => $row['author_id'],
				'description' => $row['description'],
				'created' => timeformat($row['created']),
				'last_access' => timeformat($row['last_access']),
				'filesize' => (substr($row['file'],14)!='- empty item -') ? floor(filesize($boarddir.'/tp-downloads/'.$row['file']) / 1024) : '0',
				'downloads' => $row['downloads'],
				'sshot' => !empty($sshot) ? $sshot : '',
				'screenshot' => $row['screenshot'],
				'link' => $row['link'],
				'href' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
				'approved' => $row['category'] < 0 ? '0' : '1' ,
				'approve' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
				'subitem' => $fdata,
			);
			$authorID = $row['author_id'];
			$catparent = $row['category'];
			$itemname = $row['name'];

			$smcFunc['db_free_result']($request);
			$request = $smcFunc['db_query']('', '
				SELECT mem.real_name as realName 
				FROM {db_prefix}members as mem 
				WHERE mem.id_member = {int:id_mem}',
				array('id_mem' => $authorID)
			);
			if($smcFunc['db_num_rows']($request) > 0)
			{
				$row = $smcFunc['db_fetch_assoc']($request);
				$context['TPortal']['admcurrent']['member'] = $row['realName'];
				$smcFunc['db_free_result']($request);
			}
			else
				$context['TPortal']['admcurrent']['member'] = '-' . $txt['guest_title'] . '-';
		}
		// check to see if its child
		$parents = array();
		while($catparent > 0 )
		{
			$parents[$catparent] = array(
				'id' => $catparent,
				'name' => $context['TPortal']['linkcats'][$catparent]['name'],
				'parent' => $context['TPortal']['linkcats'][$catparent]['parent']
			);
			$catparent = $context['TPortal']['linkcats'][$catparent]['parent'];
		}

		// make the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=admin', $txt['tp-dldownloads']);

		if(isset($parents))
		{
			$parts = array_reverse($parents, TRUE);
			// add to the linktree
			foreach($parts as $parent)
			{
				TPadd_linktree($scripturl.'?action=tpmod;dl=admincat'.$parent['id'] , $parent['name']);
			}
		}
		// add to the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=adminitem'.$item , $itemname);
	}
	loadTemplate('TPdladmin');
	if(loadLanguage('TPmodules') == false)
		loadLanguage('TPmodules', 'english');
	if(loadLanguage('TPortalAdmin') == false)
		loadLanguage('TPortalAdmin', 'english');

	// setup admin tabs according to subaction
	$context['admin_area'] = 'tp_dlmanager';
	$context['admin_tabs'] = array(
		'title' => $txt['tp-dlheader1'],
		'help' => $txt['tp-dlheader2'],
		'description' => $txt['tp-dlheader3'],
		'tabs' => array(),
	);
	if (allowedTo('tp_dlmanager'))
	{
		$context['TPortal']['subtabs'] = array(
			'admin' => array(
				'text' => 'tp-dltabs4',
				'url' => $scripturl . '?action=tpmod;dl=admin',
				'active' => substr($context['TPortal']['dlsub'], 0, 5) == 'admin' && $context['TPortal']['dlsub'] != 'adminsettings' && $context['TPortal']['dlsub'] != 'adminaddcat' && $context['TPortal']['dlsub'] != 'adminftp' && $context['TPortal']['dlsub'] != 'adminsubmission',
			),
			'settings' => array(
				'text' => 'tp-dltabs1',
				'url' => $scripturl . '?action=tpmod;dl=adminsettings',
				'active' => $context['TPortal']['dlsub'] == 'adminsettings',
			),
			'addcategory' => array(
				'text' => 'tp-dltabs2',
				'url' => $scripturl . '?action=tpmod;dl=adminaddcat',
				'active' => $context['TPortal']['dlsub'] == 'adminaddcat',
			),
			'upload' => array(
				'text' => 'tp-dltabs3',
				'url' => $scripturl . '?action=tpmod;dl=upload',
				'active' => $context['TPortal']['dlsub'] == 'upload',
			),
			'submissions' => array(
				'text' => 'tp-dlsubmissions' ,
				'url' => $scripturl . '?action=tpmod;dl=adminsubmission',
				'active' => $context['TPortal']['dlsub'] == 'adminsubmission',
			),
			'ftp' => array(
				'text' => 'tp-dlftp',
				'url' => $scripturl . '?action=tpmod;dl=adminftp',
				'active' => $context['TPortal']['dlsub'] == 'adminftp',
			),
		);
	}
	$context['template_layers'][] = 'tpadm';
	$context['template_layers'][] = 'subtab';
	TPadminIndex('');
	$context['current_action'] = 'admin';
}
// edit screen for regular users
function TPortalDLUser($item)
{
	global $txt, $scripturl, $boarddir, $context, $smcFunc;

	// check that it is indeed yours
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_dlmanager 
		WHERE id = {int:item} 
		AND type = {string:type}
		AND author_id = {int:auth} LIMIT 1',
		array('item' => $item, 'type' => 'dlitem', 'auth' => $context['user']['id'])
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		// ok, it is. :)
		$row = $smcFunc['db_fetch_assoc']($request);

		// is it actually a subitem?
		if($row['subitem'] > 0)
			redirectexit('action=tpmod;dl=useredit'.$row['subitem']);

		// get all items for a list but only your own
		$context['TPortal']['useritems'] = array();
		$context['TPortal']['dl_useredit'] = array();
		$itemlist = $smcFunc['db_query']('', '
			SELECT id, name FROM {db_prefix}tp_dlmanager 
			WHERE id != {int:item}
			AND author_id = {int:auth} 
			AND type = {string:type} 
			AND subitem = 0 
			ORDER BY name ASC',
			array('item' => $item, 'auth' => $context['user']['id'], 'type' => 'dlitem')
		);
		if($smcFunc['db_num_rows']($itemlist) > 0)
		{
			while($ilist = $smcFunc['db_fetch_assoc']($itemlist))
			{
				$context['TPortal']['useritems'][] = array(
					'id' => $ilist['id'],
					'name' => $ilist['name'],
				);
			}
		}

		// Any additional files then..?
		$subitem = $row['id'];
		$fdata = array();
		$fetch = $smcFunc['db_query']('', '
			SELECT id, name, file, downloads, filesize
			FROM {db_prefix}tp_dlmanager
			WHERE type = {string:type}
			AND subitem = {int:sub}',
			array('type' => 'dlitem', 'sub' => $subitem)
		);
					
		if($smcFunc['db_num_rows']($fetch) > 0)
		{
			while($frow = $smcFunc['db_fetch_assoc']($fetch))
			{
				if($context['TPortal']['dl_fileprefix'] == 'K')
					$ffs = ceil($row['filesize'] / 1000).' Kb';
				elseif($context['TPortal']['dl_fileprefix'] == 'M')
					$ffs = (ceil($row['filesize'] / 1000) / 1000).' Mb';
				elseif($context['TPortal']['dl_fileprefix'] == 'G')
					$ffs = (ceil($row['filesize'] / 1000000) / 1000).' Gb';
								
				$fdata[] = array(
					'id' => $frow['id'],
					'name' => $frow['name'],
					'file' => $frow['file'],
					'href' => $scripturl.'?action=tpmod;dl=item'.$frow['id'],
					'downloads' => $frow['downloads'],
					'created' => $frow['created'],
					'filesize' => $ffs,
				);
			}
			$smcFunc['db_free_result']($fetch);
		}

		$context['TPortal']['dl_useredit'][] = array(
			'id' => $row['id'],
			'name' => $row['name'],
			'icon' => $row['icon'],
			'category' => $row['category'],
			'file' => $row['file'],
			'views' => $row['views'],
			'authorID' => $row['author_id'],
			'description' => $row['description'],
			'created' => timeformat($row['created']),
			'last_access' => timeformat($row['last_access']),
			'filesize' => (substr($row['file'], 14) != '- empty item -') ? floor(filesize($boarddir.'/tp-downloads/'.$row['file']) / 1024) : '0',
			'downloads' => $row['downloads'],
			'sshot' => $row['screenshot'],
			'link' => $row['link'],
			'href' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
			'approved' => $row['category'] < 0 ? '0' : '1' ,
			'approve' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
			'subitem' => $fdata,
		);
		$authorID = $row['author_id'];
		$catparent = $row['category'];
		$itemname = $row['name'];

		$smcFunc['db_free_result']($request);
		$request = $smcFunc['db_query']('', '
			SELECT real_name as realName 
			FROM {db_prefix}members 
			WHERE id_member = {int:auth} LIMIT 1',
			array('auth' => $authorID)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$row = $smcFunc['db_fetch_assoc']($request);
			$context['TPortal']['admcurrent']['member'] = $row['realName'];
			$smcFunc['db_free_result']($request);
		}
		else
			$context['TPortal']['admcurrent']['member'] = '-' . $txt['guest_title'] . '-';
		// add to the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=useredit'.$item , $txt['tp-useredit'].': '.$itemname);
		$context['TPortal']['dlaction'] = 'useredit';
		// fetch allowed categories
		TP_dluploadcats();
		// get the icons
		TP_dlgeticons();

		loadTemplate('TPdlmanager');
		if(loadLanguage('TPmodules') == false)
			loadLanguage('TPmodules', 'english');
		if(loadLanguage('TPortalAdmin') == false)
			loadLanguage('TPortalAdmin', 'english');

	}
	else
		redirectexit('action=tpmod;dl');
}

function dlupdatefilecount($category, $total = true)
{
	global $smcFunc;

	// get all files in its own category first
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_dlmanager 
		WHERE category = {int:cat} 
		AND type = {string:type}',
		array('cat' => $category, 'type' => 'dlitem')
	);
	$result = $smcFunc['db_fetch_row']($request);
	$r = $result[0];
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_dlmanager 
		SET files = {int:file} 
		WHERE id = {int:item}',
		array('file' => $r, 'item' => $category)
	);
}

function dlsort($a, $b)
{
	   return strnatcasecmp($b["items"], $a["items"]);
}
function dlsortviews($a, $b)
{
	   return strnatcasecmp($b["views"], $a["views"]);
}
function dlsortsize($a, $b)
{
	   return strnatcasecmp($b["size"], $a["size"]);
}
function dlsortdownloads($a, $b)
{
	   return strnatcasecmp($b["downloads"], $a["downloads"]);
}

function TPDLgetname($oldname)
{
	if(strlen($oldname) > 13 && is_numeric(substr($oldname, 0, 10)))
		$newname = substr($oldname, 10);
	else
		$newname = $oldname;

	return $newname;
 }
function TP_dluploadcats()
{
	global $scripturl, $context, $smcFunc;

	//first : fetch all allowed categories
	$sorted = array();
	$request = $smcFunc['db_query']('', '
		SELECT id, parent, name, access 
		FROM {db_prefix}tp_dlmanager 
		WHERE type = {string:type}',
		array('type' => 'dlcat')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$show = get_perm($row['access'], 'tp_dlmanager');
			if($show)
				$sorted[$row['id']] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'parent' => $row['parent'],
					'href' => $scripturl . '?action=tpmod;dl=cat'. $row['id'],
				);
		}
		$smcFunc['db_free_result']($request);
	}
	$context['TPortal']['cats'] = array();
	// sort them
	if(count($sorted) > 1)
	{
		$context['TPortal']['cats'] = $sorted;
		$context['TPortal']['uploadcats'] = chain('id', 'parent', 'name', $sorted);
		$context['TPortal']['uploadcats2'] = chain('name', 'parent', 'id', $sorted);
	}
	else
	{
		$context['TPortal']['uploadcats'] = $sorted;
		$context['TPortal']['uploadcats2'] = $sorted;
		$context['TPortal']['cats'] = $sorted;
	}
}

function TP_dlgeticons()
{
	global $context, $boarddir;

	// fetch icons, just read the directory
	$context['TPortal']['dlicons'] = array();
	if ($handle = opendir($boarddir.'/tp-downloads/icons')) 
	{
		while (false !== ($file = readdir($handle))) 
		{
			if($file != '.' && $file != '..' && $file != '.htaccess' && in_array(substr($file,(strlen($file)-4), 4), array('.jpg','.gif','.png')))
				$context['TPortal']['dlicons'][] = $file;
		}
		closedir($handle);
		sort($context['TPortal']['dlicons']);
	}
}
function TP_dlftpfiles()
{
	global $context, $boarddir;

	$count = 1;
	$sorted = array();
	if ($handle = opendir($boarddir.'/tp-downloads'))
	{
		while (false !== ($file = readdir($handle)))
		{
			if($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'icons')
			{ 
				$size = floor(filesize($boarddir.'/tp-downloads/'.$file) / 1024);
				$sorted[$count] = array(
					'id' => $count,
					'file' => $file,
					'size' => $size,
				);
				$count++;
			}
		}
		closedir($handle);
	}
	$context['TPortal']['tp-downloads'] = array();
	// sort them
	if(count($sorted) > 1)
		$context['TPortal']['tp-downloads'] = chain('id', 'size', 'file', $sorted);
	else
		$context['TPortal']['tp-downloads'] = $sorted;
}

?>