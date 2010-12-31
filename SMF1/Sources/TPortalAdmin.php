<?php
/******************************************************************************
* TPortalAdmin.php                                                                   *
*******************************************************************************
* TP version: 1.0 RC1                                                                                                      *
* Software Version:           SMF 1.1.x                                                                                   *
* Software by:                Bloc (http://www.tinyportal.net)                                                      *
* Copyright 2005-2010 by:     Bloc (bloc@tinyportal.net)                                                         *
* Support, News, Updates at:  http://www.tinyportal.net                   *
*******************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

// TinyPortal admin
function TPortalAdmin()
{
	global $ID_MEMBER, $txt, $scripturl, $db_prefix,  $user_info, $sourcedir, $modSettings, $context, $settings , $boardurl , $boarddir;

	// prefix of the TP tables
	$tp_prefix=$settings['tp_prefix'];

	loadLanguage('TPortalAdmin');
	loadLanguage('TPortal');

	require_once($sourcedir . '/TPcommon.php');
	require_once($sourcedir . '/Subs-Post.php');

	$context['TPortal']['frontpage_visualopts_admin']=array('left' => 0, 'right' => 0, 'center' => 0, 'top' => 0, 'bottom' => 0,'lower' => 0,'header' => 0,'nolayer' => 0,	'sort' => 'date',	'sortorder' => 'desc');
	
	$w = explode(',', $context['TPortal']['frontpage_visual']);
	if(in_array('left',$w))
		$context['TPortal']['frontpage_visualopts_admin']['left']=1;
	if(in_array('right',$w))
		$context['TPortal']['frontpage_visualopts_admin']['right']=1;
	if(in_array('center',$w))
		$context['TPortal']['frontpage_visualopts_admin']['center']=1;
	if(in_array('top',$w))
		$context['TPortal']['frontpage_visualopts_admin']['top']=1;
	if(in_array('bottom',$w))
		$context['TPortal']['frontpage_visualopts_admin']['bottom']=1;
	if(in_array('lower',$w))
		$context['TPortal']['frontpage_visualopts_admin']['lower']=1;
	if(in_array('header',$w))
		$context['TPortal']['frontpage_visualopts_admin']['header']=1;
	if(in_array('nolayer',$w))
		$context['TPortal']['frontpage_visualopts_admin']['nolayer']=1;
	foreach($w as $r)
	{
		if(substr($r,0,5)=='sort_')
			$context['TPortal']['frontpage_visualopts_admin']['sort']=substr($r,5);
		elseif(substr($r,0,10)=='sortorder_')
			$context['TPortal']['frontpage_visualopts_admin']['sortorder']=substr($r,10);
	}

	// call up the editor
	TPwysiwyg_setup();

	TPadd_linktree($scripturl.'?action=tpadmin', 'TP Admin');
		
	// some GET values set up
	$tpart = isset($_GET['apage']) ? $_GET['apage'] : 0;
	$context['TPortal']['tpstart'] = isset($_GET['tpstart']) ? $_GET['tpstart'] : 0;

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum']=true;

	// admin can see all uploaded images as well as thsoe who manage articles
	if($context['user']['is_admin'] || allowedTo('tp_articles'))
		$imgdir = '/tp-images/Image';
	else
		$imgdir = '/tp-images/Image';

	// get all membergroups	
	tp_groups();

	// get the layout shcemes
	get_catlayouts();
	
	// get the caregories
	get_catnames();

	if(isset($_GET['id']))
		$context['TPortal']['subaction_id']=$_GET['id'];

	// check POST values
	$return = do_postchecks();
	
	if(!empty($return))
		redirectexit('action=tpadmin;sa=' . $return);	

	if(isset($_GET['sa']))
	{
		$context['TPortal']['subaction'] = $tpsub = $_GET['sa'];
		if(substr($_GET['sa'],0,11)=='editarticle')
		{
			$tpsub = 'articles';
			$context['TPortal']['subaction'] = 'editarticle';
		}
		elseif(substr($_GET['sa'],0,11)=='addarticle_')
		{
			$tpsub = 'articles';
			$context['TPortal']['subaction'] = $_GET['sa'];
		}
		elseif(isset($_GET['tags']))
		{
			$tpsub = 'tags';
			$context['TPortal']['subaction'] = $_GET['sa'];
		}
		do_subaction($tpsub);
	}
	elseif(isset($_GET['blktype']) || isset($_GET['addblock']) || isset($_GET['blockon']) || isset($_GET['blockoff']) || isset($_GET['blockleft']) || isset($_GET['blockright']) || isset($_GET['blockcenter']) || isset($_GET['blocktop']) || isset($_GET['blockbottom']) || isset($_GET['blockfront']) || isset($_GET['blocklower']) || isset($_GET['blockdelete']) || isset($_GET['blockedit']))
	{
		$context['TPortal']['subaction'] = $tpsub = 'blocks';
		do_blocks($tpsub);
	}
	elseif(isset($_GET['linkon']) || isset($_GET['linkoff']) || isset($_GET['linkedit']) || isset($_GET['linkdelete']) || isset($_GET['linkdelete']))
	{
		$context['TPortal']['subaction'] = $tpsub = 'linkmanager';
		do_menus($tpsub);
	}
	elseif(isset($_GET['catdelete']) || isset($_GET['artfeat']) || isset($_GET['artfront']) || isset($_GET['artdelete']) || isset($_GET['arton']) || isset($_GET['artoff']) || isset($_GET['artsticky']) || isset($_GET['artlock']) || isset($_GET['catcollapse']))
	{
		$context['TPortal']['subaction'] = $tpsub = 'articles';
		do_articles($tpsub);
	}
	elseif(isset($_GET['tags']) && $tpsub=='modules')
	{
		$context['TPortal']['subaction'] = $tpsub = 'modules';
		do_moduletags($tpsub);
	}
	else
	{
		$context['TPortal']['subaction'] = $tpsub = 'overview';	
		do_news($tpsub);
	}


	loadTemplate('TPortalAdmin');
	adminIndex('tportal');

	$context['TPortal']['subtabs']='';
	// setup admin tabs according to subaction
	if(in_array($tpsub, array('news','credits')))
	{
		$context['admin_area']= 'tp_news';
		$context['admin_tabs'] = array(
			'title' =>  $txt['tp-adminnews1'],
			'help' => $txt['tp-adminnews2'],
			'description' => $txt['tp-adminnews3'],
			'tabs' => array(),
			);
		if (allowedTo(array('tp_articles','tp_settings','tp_settings','tp_blocks')))
		{
			$context['admin_tabs']['tabs'] = array(
				'news' => array(
				'title' => $txt['tp-adminnews1'],
				'description' => $txt['tp-adminnews2'],
				'href' => $scripturl . '?action=tpadmin;sa=news',
				'is_selected' => $tpsub == 'news',
				),
					'credits' => array(
					'title' => $txt['tp-credits'],
					'description' => $txt['tp-creditsdesc1'],
					'href' => $scripturl . '?action=tpadmin;sa=credits',
					'is_selected' => $tpsub == 'credits',
				),
			);
		}
	}
	elseif(in_array($tpsub, array('settings','frontpage')))
	{
		$context['admin_area']= 'tp_settings';
		$context['admin_tabs'] = array(
			'title' => $txt['tp-adminheader1'],
			'help' => $txt['tp-adminheader3'],
			'description' => $txt['tp-adminheader2'],
			'tabs' => array(),
			);
		if (allowedTo('tp_settings'))
		{
			$context['admin_tabs']['tabs'] = array(
				'settings' => array(
					'title' => $txt['tp-settings'],
					'description' => $txt['tp-settingdesc1'],
					'href' => $scripturl . '?action=tpadmin;sa=settings',
					'is_selected' => $tpsub == 'settings',
				),
				'frontpage' => array(
					'title' => $txt['tp-frontpage'],
					'description' => $txt['tp-frontpagedesc1'],
					'href' => $scripturl . '?action=tpadmin;sa=frontpage',
					'is_selected' => $tpsub == 'frontpage',
				),
			);
		}
	}
	elseif(in_array($tpsub, array('articles','strays','categories','submission','addcategory','addarticle','addarticle_php','addarticle_bbc','addarticle_import','clist', 'artsettings','articons','artlabels','artpresets')) || substr($tpsub,0,11)=='editarticle' || substr($tpsub,0,12)=='articles_cat')
	{
		require_once($sourcedir . '/Subs-Post.php');
		
		$context['admin_area']= 'tp_articles';
		$context['admin_tabs'] = array(
			'title' => $txt['tp-articles'],
			'help' => $txt['tp-articlehelp'],
			'description' => $txt['tp-articledesc1'],
			'tabs' => array(),
		);
		if (allowedTo('tp_articles'))
		{
			if($tpsub=='articons')
			{
				// collect icons and illustrative icons
				$context['TPortal']['articons']=array();
			}
			$context['admin_tabs']['tabs'] = array(
				'articles' => array(
					'title' => $txt['tp-articles'],
					'description' => $txt['tp-articledesc1'],
					'href' => $scripturl . '?action=tpadmin;sa=articles',
					'is_selected' => (substr($tpsub,0,11)=='editarticle' || in_array($tpsub, array('articles','addarticle','addarticle_php'))),
				),
				'categories' => array(
					'title' => $txt['tp-tabs5'],
					'description' => $txt['tp-articledesc2'],
					'href' => $scripturl . '?action=tpadmin;sa=categories',
					'is_selected' => $tpsub == 'categories',
				),
				'artsettings' => array(
					'title' => $txt['tp-settings'],
					'description' => $txt['tp-articledesc3'],
					'href' => $scripturl . '?action=tpadmin;sa=artsettings',
					'is_selected' => $tpsub == 'artsettings',
				),
				'articles_nocat' => array(
					'title' => $txt['tp-uncategorised'] . ' (' . $context['TPortal']['total_nocategory'] .')',
					'description' => '',
					'href' => $scripturl . '?action=tpadmin;sa=articles;sa=strays',
					'is_selected' => $context['TPortal']['subaction'] == 'strays',
				),
				'icons' => array(
					'title' => $txt['tp-adminicons'],
					'description' => $txt['tp-articledesc5'],
					'href' => $scripturl . '?action=tpadmin;sa=articons',
					'is_selected' => $tpsub == 'articons',
				),	
			);
			if(in_array($tpsub,array('articles','addarticle_php','addarticle_html','addarticle_bbc','addarticle_import')))
				$context['TPortal']['subtabs'] = array(
						'addarticle' => array(
							'title' => $txt['tp-tabs2'],
							'description' => '',
							'href' => $scripturl . '?action=tpadmin;sa=addarticle_html' . (isset($_GET['artcat']) ? ';artcat='.$_GET['artcat'] : ''),
					'is_selected' => $context['TPortal']['subaction'] == 'addarticle_html',
						),
						'addarticle_php' => array(
							'title' => $txt['tp-tabs3'],
							'description' => '',
							'href' => $scripturl . '?action=tpadmin;sa=addarticle_php' . (isset($_GET['artcat']) ? ';artcat='.$_GET['artcat'] : ''),
							'is_selected' => $tpsub == 'addarticle_php',
						),
						'addarticle_bbc' => array(
							'title' => $txt['tp-addbbc'],
							'description' => '',
							'href' => $scripturl . '?action=tpadmin;sa=addarticle_bbc' . (isset($_GET['artcat']) ? ';artcat='.$_GET['artcat'] : ''),
							'is_selected' => $tpsub == 'addarticle_bbc',
						),
						'article_import' => array(
							'title' => $txt['tp-addimport'],
							'description' => '',
							'href' => $scripturl . '?action=tpadmin;sa=addarticle_import' . (isset($_GET['artcat']) ? ';artcat='.$_GET['artcat'] : ''),
							'is_selected' => $tpsub == 'addarticle_import',
						),
			);
			elseif(in_array($tpsub,array('addcategory','categories','clist')))
				$context['TPortal']['subtabs'] = array(
						'addcategory' => array(
							'title' => $txt['tp-tabs6'],
							'description' => '',
							'href' => $scripturl . '?action=tpadmin;sa=addcategory',
							'is_selected' => $tpsub == 'addcategory',
						),
						'clist' => array(
							'title' => $txt['tp-tabs11'],
							'description' => '',
							'href' => $scripturl . '?action=tpadmin;sa=clist',
							'is_selected' => $tpsub == 'clist',
						),
				);
		}
	}
	elseif(in_array($tpsub, array('panels', 'blocks','addlblock','addrblock','addcblock','addfblock','addbblock','addtblock','addloblock')) || substr($tpsub,0,5)=='block' || isset($_GET['blockedit']))
	{
		$context['admin_area']= 'tp_panels';
		$context['admin_tabs'] = array(
			'title' => $txt['tp-adminpanels'],
			'help' => $txt['tp-panelhelp'],
			'description' => $txt['tp-paneldesc1'],
			'tabs' => array(),
			);
		if (allowedTo('tp_blocks'))
		{
			$context['admin_tabs']['tabs'] = array(
				'panelsettings' => array(
					'title' => $txt['tp-allpanels'],
					'description' => $txt['tp-paneldesc1'],
					'href' => $scripturl . '?action=tpadmin;sa=panels',
					'is_selected' => $tpsub=='panels',
				),
				'blocks' => array(
					'title' => $txt['tp-allblocks'],
					'description' => $txt['tp-blocksdesc1'],
					'href' => $scripturl . '?action=tpadmin;sa=blocks',
					'is_selected' => $tpsub == 'blocks' && !isset($_GET['latest']), 
				),
				'latestblocks' => array(
					'title' => $txt['tp-latestblocks'],
					'description' => '',
					'href' => $scripturl . '?action=tpadmin;sa=blocks;latest',
					'is_selected' => $tpsub == 'blocks' && isset($_GET['latest']) 
				)
			);
		}
		// collect all available PHP block snippets
		$context['TPortal']['blockcodes'] = TPcollectSnippets();
	}
	elseif(in_array($tpsub, array('modules','tags')))
	{
		$context['admin_area']= 'tp_modules';
		$context['admin_tabs'] = array(
			'title' => $txt['tp-modules'],
			'help' => $txt['tp-modulehelp'],
			'description' => $txt['tp-moduledesc1'],
			'tabs' => array(),
		);
		$context['admin_tabs']['tabs'] = array(
				'modules' => array(
					'title' => $txt['tp-modules'],
					'description' => $txt['tp-moduledesc1'],
					'href' => $scripturl . '?action=tpadmin;sa=modules',
					'is_selected' => $tpsub == 'modules' && !isset($_GET['import']) && !isset($_GET['tags']),
				),
					'tags' => array(
					'title' => $txt['tp-tags'],
					'description' => $txt['tp-tags2'],
					'href' => $scripturl . '?action=tpadmin;sa=modules;tags',
					'is_selected' => $tpsub == 'tags',
				),
			);
		// collect modules and their permissions	
		$result = tp_query("SELECT * FROM " . $tp_prefix . "modules WHERE 1", __FILE__, __LINE__);
		if(tpdb_num_rows($result)>0)
		{
			while($row = tpdb_fetch_assoc($result))
			{
				$context['TPortal']['admmodules'][] = $row;
			}
			tpdb_free_result($result);
		}
	}
	elseif(in_array($tpsub, array('menubox','addmenu')) || substr($tpsub,0,12)=='editmenuitem' || substr($tpsub,0,4)=='link' || isset($_GET['linkedit']))
	{
		$context['admin_area']= 'tp_menubox';
		$context['admin_tabs'] = array(
			'title' => $txt['tp-blocks'],
			'help' => $txt['tp-adminheader8'],
			'description' => $txt['tp-adminheader9'],
			'tabs' => array(),
		);
		if (allowedTo('tp_blocks'))
		{
			$context['admin_tabs']['tabs'] = array(
				'menubox' => array(
					'title' => $txt['tp-menumanager'],
					'description' => '',
					'href' => $scripturl . '?action=tpadmin;sa=menubox',
					'is_selected' => $tpsub == 'menubox' ,
				),
				'addmenu' => array(
					'title' => isset($_GET['mid']) ? $txt['tp-addmenuitem'] : $txt['tp-addmenu'],
					'description' => '',
					'href' => (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $scripturl . '?action=tpadmin;sa=addmenu;mid='.$_GET['mid'] : $scripturl . '?action=tpadmin;sa=addmenu;fullmenu',
					'is_selected' => $tpsub == 'addmenu',
					),
				);
			
		}
	}
	$context['template_layers'][] = 'subtab';
}

function tp_notifyComments($memberlist, $message2, $subject)
{
	global $board, $topic, $txt, $scripturl, $db_prefix, $language, $user_info;
	global $ID_MEMBER, $modSettings, $sourcedir;

	require_once($sourcedir . '/Subs-Post.php');

	$message = $message2;

	// Censor the subject and body...
	censorText($subject);
	censorText($message);

	$subject = un_htmlspecialchars($subject);
	$message = trim(un_htmlspecialchars(strip_tags(strtr(parse_bbc($message, false), array('<br />' => "\n", '</div>' => "\n", '</li>' => "\n", '&#91;' => '[', '&#93;' => ']')))));

	// Find the members with notification on for this board.
	$tagquery = 'FIND_IN_SET(mem.id_member, "' . implode(",",$memberlist) .'")';
	$members = tp_query("
		SELECT
			mem.id_member, mem.emailAddress, 
			FROM " . $db_prefix . "members AS mem
			AND mem.id_member != $ID_MEMBER
			AND $tagquery
			AND mem.is_activated = 1	", __FILE__, __LINE__);
	while ($rowmember = tpdb_fetch_assoc($members))
	{

		// Setup the string for adding the body to the message, if a user wants it.
		$body_text = empty($modSettings['disallow_sendBody']) ? $txt['notification_new_topic_body'] . "\n\n" . $message . "\n\n" : '';

		$send_subject = sprintf($txt['notify_boards_subject'], $_POST['subject']);

		sendmail($rowmember['emailAddress'], $send_subject,
				sprintf($txt['notify_boards'], $_POST['subject'], $scripturl . '?topic=' . $topic . '.new#new', un_htmlspecialchars($user_info['name'])) .
				$txt['notify_boards_once'] . "\n\n" .
				(!empty($rowmember['notifySendBody']) ? $body_text : '') .
				$txt['notify_boardsUnsubscribe'] . ': ' . $scripturl . '?action=notifyboard;board=' . $board . ".0\n\n" .
				$txt[130], null, 't' . $topic);
	}
	tpdb_free_result($members);
}

/* ******************************************************************************************************************** */

function do_subaction($tpsub)
{
	global $context,$txt,$settings,$boardurl,$scripturl,$boarddir,$userinfo,$db_prefix;
	
	if(in_array($tpsub, array('articles', 'strays','categories','addcategory','submission','artsettings','articons')))
		do_articles();
	elseif(in_array($tpsub, array('blocks','panels')))
		do_blocks();
	elseif(in_array($tpsub, array('modules','tags')))
		do_modules();
	elseif(in_array($tpsub, array('menubox','addmenu')))
		do_menus();
	elseif(in_array($tpsub, array('frontpage','overview','news','credits','permissions')))
		do_news($tpsub);
	elseif($tpsub=='settings')
		do_news('settings');
	else
		do_news();
}


function do_blocks()
{
	global $context,$txt,$settings,$boardurl,$scripturl,$boarddir,$user_info,$db_prefix, $modSettings;

	// prefix of the TP tables
	$tp_prefix=$settings['tp_prefix'];

	isAllowedTo('tp_blocks');

	$panels = array('left','right','center','top','bottom','lower','front');
	$blocktype=array('no','userbox','newsbox','statsbox','searchbox','html','onlinebox','themebox','oldshoutbox','catmenu','phpbox','scriptbox','recentbox','ssi','module','rss','sitemap','admin','articlebox','categorybox','tpmodulebox');
	$bars = array(1 => 'left', 2 => 'right', 3 => 'center', 4 => 'front', 5 => 'bottom', 6 => 'top', 7 => 'lower');

	if(isset($_GET['addblock']))
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=blocks', $txt['tp-blocks']);
		TPadd_linktree($scripturl.'?action=tpadmin;sa=addblock', $txt['tp-addblock']);
		// collect all available PHP block snippets
		$context['TPortal']['blockcodes'] = TPcollectSnippets();
		$request = tp_query("SELECT id,title,bar FROM " . $tp_prefix . "blocks WHERE 1", __FILE__, __LINE__);
		if (tpdb_num_rows($request) > 0)
		{
			$context['TPortal']['copyblocks'] = array();
			while($row = tpdb_fetch_assoc($request))
			{
				$context['TPortal']['copyblocks'][] = $row;
			}
			tpdb_free_result($request);
		}

	}
	// change the on/off
	if(isset($_GET['blockon']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockon']) ? $_GET['blockon'] : 0;
		$request = tp_query("UPDATE " . $tp_prefix . "blocks SET off = IF(off = 0 , 1, 0) WHERE id=" . $what, __FILE__, __LINE__);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	// remove it?
	if(isset($_GET['blockdelete']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockdelete']) ? $_GET['blockdelete'] : 0;
		$request = tp_query("DELETE FROM " . $tp_prefix . "blocks WHERE id=" . $what, __FILE__, __LINE__);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	// do the moving stuff
	if(isset($_GET['blockright']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockright']) ? $_GET['blockright'] : 0;
		$request = tp_query("UPDATE " . $tp_prefix . "blocks SET bar=2 WHERE id=" . $what, __FILE__, __LINE__);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blockleft']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockleft']) ? $_GET['blockleft'] : 0;
		$request = tp_query("UPDATE " . $tp_prefix . "blocks SET bar=1 WHERE id=" . $what, __FILE__, __LINE__);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blockcenter']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockcenter']) ? $_GET['blockcenter'] : 0;
		$request = tp_query("UPDATE " . $tp_prefix . "blocks SET bar=3 WHERE id=" . $what, __FILE__, __LINE__);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blockfront']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockfront']) ? $_GET['blockfront'] : 0;
		$request = tp_query("UPDATE " . $tp_prefix . "blocks SET bar=4 WHERE id=" . $what, __FILE__, __LINE__);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blockbottom']))
	{
		checksession('get');
		$what = is_numeric($_GET['blockbottom']) ? $_GET['blockbottom'] : 0;
		$request = tp_query("UPDATE " . $tp_prefix . "blocks SET bar=5 WHERE id=" . $what, __FILE__, __LINE__);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blocktop']))
	{
		checksession('get');
		$what = is_numeric($_GET['blocktop']) ? $_GET['blocktop'] : 0;
		$request = tp_query("UPDATE " . $tp_prefix . "blocks SET bar=6 WHERE id=" . $what, __FILE__, __LINE__);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	elseif(isset($_GET['blocklower']))
	{
		checksession('get');
		$what = is_numeric($_GET['blocklower']) ? $_GET['blocklower'] : 0;
		$request = tp_query("UPDATE " . $tp_prefix . "blocks SET bar=7 WHERE id=" . $what, __FILE__, __LINE__);
		redirectexit('action=tpadmin;sa=blocks');
	}	
	// are we on overview screen?
	if(isset($_GET['overview']))
	{
		// fetch all blocks membergroup permissions
		$request = tp_query("SELECT id,title,bar,access,type FROM " . $tp_prefix . "blocks WHERE off=0 ORDER BY bar,id", __FILE__, __LINE__);
		if (tpdb_num_rows($request) > 0)
		{
			$context['TPortal']['blockoverview'] = array();
			while($row = tpdb_fetch_assoc($request))
			{
				$context['TPortal']['blockoverview'][] = array(
					'id' => $row['id'],	
					'title' => $row['title'],	
					'bar' => $row['bar'],	
					'type' => $row['type'],	
					'access' => explode(",",$row['access']),	
				);
			}
			tpdb_free_result($request);
		}
		get_grps(true,true);
	}

	// are we editing a block?
	if(isset($_GET['blockedit']))
	{
		checksession('get');
		$blockedit = is_numeric($_GET['blockedit']) ? $_GET['blockedit'] : 0;
		TPadd_linktree($scripturl.'?action=tpadmin;sa=blocks', $txt['tp-blocks']);
		TPadd_linktree($scripturl.'?action=tpadmin;blockedit='.$blockedit . ';'.$context['session_var'].'='.$context['session_id'], $txt['tp-editblock']);
		$request = tp_query("SELECT * FROM " . $tp_prefix . "blocks WHERE id=" . $blockedit, __FILE__, __LINE__);
		if (tpdb_num_rows($request) > 0)
		{
			$row = tpdb_fetch_assoc($request);
			$acc2=explode(",",$row['access2']);
			$context['TPortal']['blockedit'] = $row;
			$context['TPortal']['blockedit']['access22'] = $context['TPortal']['blockedit']['access2'];
			$context['TPortal']['blockedit']['body'] = html_entity_decode($row['body'], ENT_NOQUOTES, $modSettings['global_character_set']);
			unset($context['TPortal']['blockedit']['access2']);
			$context['TPortal']['blockedit']['access2']=array(
				'action' => array(),
				'board' => array(),
				'page' => array(),
				'cat' => array(),
				'lang' => array(),
				'tpmod' => array(),
				'dlcat' => array(),
				'custo' => array(),
				);
			foreach($acc2 as $ss => $svalue)
			{
				if(substr($svalue,0,6)== 'actio=')
					$context['TPortal']['blockedit']['access2']['action'][]=substr($svalue,6);
				elseif(substr($svalue,0,6)== 'board=')
					$context['TPortal']['blockedit']['access2']['board'][]=substr($svalue,6);
				elseif(substr($svalue,0,6)== 'tpage=')
					$context['TPortal']['blockedit']['access2']['page'][]=substr($svalue,6);
				elseif(substr($svalue,0,6)== 'tpcat=')
					$context['TPortal']['blockedit']['access2']['cat'][]=substr($svalue,6);
				elseif(substr($svalue,0,6)== 'tpmod=')
					$context['TPortal']['blockedit']['access2']['tpmod'][]=substr($svalue,6);
				elseif(substr($svalue,0,6)== 'tlang=')
					$context['TPortal']['blockedit']['access2']['lang'][]=substr($svalue,6);
				elseif(substr($svalue,0,6)== 'dlcat=')
					$context['TPortal']['blockedit']['access2']['dlcat'][]=substr($svalue,6);
				elseif(substr($svalue,0,6)== 'custo=')
					$context['TPortal']['blockedit']['access2']['custo']=substr($svalue,6);
			}

			if($context['TPortal']['blockedit']['lang']!='')
			{
				$context['TPortal']['blockedit']['langfiles']=array();
				$lang = explode("|", $context['TPortal']['blockedit']['lang']);
				$num=count($lang);
				for($i=0; $i<$num ; $i=$i+2)
				{
					$context['TPortal']['blockedit']['langfiles'][$lang[$i]]=$lang[$i+1];
				}
			}
			tpdb_free_result($request);
			// collect all available PHP block snippets
			$context['TPortal']['blockcodes'] = TPcollectSnippets();
			
			get_grps();
			get_langfiles();
			get_boards();
			get_articles();

			$context['TPortal']['edit_categories'] = array();
			
			$request = tp_query("SELECT id,value1 as name FROM " . $tp_prefix . "variables WHERE type='category'", __FILE__, __LINE__);
			if(tpdb_num_rows($request)>0)
			{
				while($row=tpdb_fetch_assoc($request))
					$context['TPortal']['article_categories'][] = $row; 
				
				tpdb_free_result($request);
			}
			// get all themes for selection
			$context['TPthemes'] = array();
			$request = tp_query("
				SELECT th.value AS name, th.ID_THEME, tb.value AS path
				FROM " . $db_prefix . "themes AS th
				LEFT JOIN " . $db_prefix . "themes AS tb ON th.id_theme = tb.id_theme
				WHERE th.variable = 'name'
				AND tb.variable = 'images_url'
				AND th.id_member = 0
				ORDER BY th.value ASC", __FILE__, __LINE__);
			if(tpdb_num_rows($request)>0)
			{
				while ($row = tpdb_fetch_assoc($request))
				{
					$context['TPthemes'][] = array(
					'id' => $row['ID_THEME'],
					'path' => $row['path'],
					'name' => $row['name']
					);
				}
				tpdb_free_result($request);
			}
			$request = tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='menus' ORDER BY value1 ASC", __FILE__, __LINE__);
			$context['TPortal']['menus']=array();
			$context['TPortal']['menus'][0]=array(
				'id' => 0, 
				'name' => 'Internal', 
				'var1' => '', 
				'var2' => ''
			);

			if(tpdb_num_rows($request)>0)
			{
				while ($row = tpdb_fetch_assoc($request))
				{
					$context['TPortal']['menus'][$row['id']]=array(
							'id' => $row['id'], 
							'name' => $row['value1'], 
							'var1' => $row['value2'], 
							'var2' => $row['value3']
						);
				}
			}
		}
		// if not throw an error
		else
			fatal_error($txt['tp-blockfailure']);
	}
	// or maybe adding it?
	elseif(isset($_GET['addblock']))
	{
		get_articles();
		// check which side its mean to be on
		$context['TPortal']['blockside'] = $_GET['addblock'];
	}
	else
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=blocks', $txt['tp-blocks']);
		foreach($panels as $p => $pan)
		{
			if(isset($_GET[$pan]))
				$context['TPortal']['panelside'] = $pan;
		}

		$request =tp_query("SELECT * FROM " . $tp_prefix . "blocks WHERE 1 ORDER BY bar,pos,id ASC", __FILE__, __LINE__);
		if (tpdb_num_rows($request) > 0)
		{
			while($row = tpdb_fetch_assoc($request))
				$context['TPortal']['admin_'.$bars[$row['bar']].'block']['blocks'][]=array('frame' => $row['frame'], 'title' => $row['title'], 'type' => (isset($blocktype[$row['type']]) ? $blocktype[$row['type']] : $row['type']), 'body' => $row['body'], 'id' => $row['id'], 'access' => $row['access'], 'pos' => $row['pos'], 'off' => $row['off'] , 'visible' => $row['visible'], 'var1' => $row['var1'], 'var2' => $row['var2'],'lang' => $row['lang'], 'access2' => $row['access2'],'loose' => $row['access2']!='' ? true : false, 'editgroups' => $row['editgroups']);

			tpdb_free_result($request);
		}
	}
	get_articles();
	if($context['TPortal']['subaction']=='panels')
		TPadd_linktree($scripturl.'?action=tpadmin;sa=panels', $txt['tp-panels']);

	$context['html_headers'] .= '
	<script language="JavaScript" type="text/javascript">
		function getXMLHttpRequest()
		{
			if (window.XMLHttpRequest)
				return new XMLHttpRequest;
			else if (window.ActiveXObject)
				return new ActiveXObject("MICROSOFT.XMLHTTP");
			else
				alert("Sorry, but your browser does not support Ajax");
		}
		
		window.onload = startToggle;
		
		function startToggle()
		{
			var img = document.getElementsByTagName("img");
			
			for(var i = 0; i < img.length; i++)
			{
				if (img[i].className == "toggleButton")
					img[i].onclick = toggleBlock;
			}
		}
		
		function toggleBlock(e)
		{ 
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;
		
			while(target.className != "toggleButton")
				  target = target.parentNode;
			
			var id = target.id.replace("blockonbutton", "");
			var Ajax = getXMLHttpRequest();
			
			Ajax.open("POST", "?action=tpadmin;blockon=" + id + ";sesc='.$context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPactive1.gif" ? "' . $settings['tp_images_url'] . '/TPactive2.gif" : "' . $settings['tp_images_url'] . '/TPactive1.gif";
				}
			}
			
			var params = "?action=tpadmin;blockon=" + id + ";sesc='.$context['session_id'].'";
			Ajax.send(params);
		}
	</script>';

}
function do_menus()
{
	global $context,$txt,$settings,$boardurl,$scripturl,$boarddir,$userinfo,$db_prefix;

	$tp_prefix = $db_prefix.'tp_';

	// first check any link stuff
	if(isset($_GET['linkon']))
	{
		checksession('get');
		$what = is_numeric($_GET['linkon']) ? $_GET['linkon'] : '0';
		$mid = isset($_GET['mid']) ? $_GET['mid'] : '0';
		if($what>0)
			$request = tp_query("UPDATE " . $tp_prefix . "variables SET value5 = 0 WHERE id = " . $what , __FILE__, __LINE__);

		redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
	}
	elseif(isset($_GET['linkoff']))
	{
		checksession('get');
		$what = is_numeric($_GET['linkoff']) ? $_GET['linkoff'] : '0';
		$mid = isset($_GET['mid']) ? $_GET['mid'] : '0';
		if($what>0)
			$request = tp_query("UPDATE " . $tp_prefix . "variables SET value5 = 1 WHERE id = " . $what , __FILE__, __LINE__);

		redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
	}
	elseif(isset($_GET['linkdelete']))
	{
		checksession('get');
		$what = is_numeric($_GET['linkdelete']) ? $_GET['linkdelete'] : '0';
		$mid = isset($_GET['mid']) ? $_GET['mid'] : '0';
		if($what>0)
			$request = tp_query("DELETE FROM " . $tp_prefix . "variables WHERE id = " . $what , __FILE__, __LINE__);

		redirectexit('action=tpadmin;sa=menubox;mid=' . $mid);
	}

	$context['TPortal']['menubox']=array();
	$context['TPortal']['editmenuitem']=array();
	$request = tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='menubox' ORDER BY subtype + 0 ASC", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			if($row['value5']=='-1')
			{
				$p = 'off';
				$status='1';
			}
			else
			{
				$status= '0';
				$p=$row['value5'];
			}
			$mtype=substr($row['value3'],0,4);
			$idtype=substr($row['value3'],4);

			if($mtype!='cats' && $mtype!='arti' && $mtype!='head' && $mtype!='spac')
			{
				$mtype='link';
				$idtype=$row['value3'];
			}
			if($row['value2']=='')
				$newlink='0';
			else
				$newlink=$row['value2'];

			if($mtype=='head')
			{
				$mtype='head';
				$idtype=$row['value1'];
			}

			$context['TPortal']['menubox'][$row['subtype2']][] = array(
				'id' => $row['id'],
				'menuID' => $row['subtype2'],
				'name' => $row['value1'],
				'pos' => $p,
				'type' => $mtype,
				'IDtype' => $idtype,
				'off' => $row['value5'],
				'sub' => $row['value4'],
				'subtype' => $row['subtype'],
				'newlink' => $newlink,
				);
			if ($context['TPortal']['subaction']=='linkmanager')
			{
				$menuid=$_GET['linkedit'];
				if($menuid==$row['id'])
					$context['TPortal']['editmenuitem'] = array(
						'id' => $row['id'],
						'menuID' => $row['subtype2'],
						'name' => $row['value1'],
						'pos' => $p,
						'type' => $mtype,
						'IDtype' => $idtype,
						'off' => $status,
						'sub' => $row['value4'],
						'subtype' => $row['subtype'],
						'newlink' => $newlink ,
						);
			}
		}
		tpdb_free_result($request);
	}

	$request = tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='menus' ORDER BY value1 ASC", __FILE__, __LINE__);
	$context['TPortal']['menus']=array();
	$context['TPortal']['menus'][0]=array(
		'id' => 0, 
		'name' => 'Internal', 
		'var1' => '', 
		'var2' => ''
	);

	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			$context['TPortal']['menus'][$row['id']]=array(
					'id' => $row['id'], 
					'name' => $row['value1'], 
					'var1' => $row['value2'], 
					'var2' => $row['value3']
				);
		}
	}

	get_articles();
	// collect categories
	$request = tp_query("
		SELECT	id, value1 as name, value2 as parent 
		FROM " . $tp_prefix . "variables
		WHERE type = 'category'", __FILE__, __LINE__);
	
	$context['TPortal']['editcats']=array(); $allsorted=array(); 
	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			$row['indent'] = 0;
			$allsorted[$row['id']] = $row;
			$alcats[] = $row['id'];
		}
		tpdb_free_result($request);
		if(count($allsorted)>1)
			$context['TPortal']['editcats'] = chain('id', 'parent', 'name', $allsorted);
		else
			$context['TPortal']['editcats'] = $allsorted;
	}
	// add to linktree
	if(isset($_GET['mid']) && is_numeric($_GET['mid']))
		TPadd_linktree($scripturl.'?action=tpadmin;sa=menubox;mid='. $_GET['mid'] , $context['TPortal']['menus'][$_GET['mid']]['name']);
	elseif(isset($_GET['linkedit']) && is_numeric($_GET['linkedit']))
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=menubox;mid='. $context['TPortal']['editmenuitem']['menuID'] , $context['TPortal']['menus'][$context['TPortal']['editmenuitem']['menuID']]['name']);
		TPadd_linktree($scripturl.'?action=tpadmin;linkedit='. $_GET['linkedit'] , $context['TPortal']['editmenuitem']['name']);
	}
}

// articles
function do_articles()
{
	global $context,$txt,$settings,$boardurl,$scripturl,$boarddir,$userinfo,$db_prefix, $modSettings;

	$tp_prefix = $db_prefix.'tp_';

	// do an update of stray articles and categories
	$acats = array();
	$request = tp_query("SELECT id FROM " . $tp_prefix . "variables WHERE type = 'category'" , __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while($row = tpdb_fetch_assoc($request))
			$acats[] = $row['id'];
		tpdb_free_result($request);
	}
	if(count($acats)>0)
	{
		$request = tp_query("UPDATE " . $tp_prefix . "variables SET value2 = 0 WHERE type='category' AND value2 NOT IN(" . implode(',',$acats) . ")", __FILE__, __LINE__);
		$request = tp_query("UPDATE " . $tp_prefix . "articles SET category = 0 WHERE category NOT IN(" . implode(',',$acats) . ") AND category>0", __FILE__, __LINE__);
	}
	// first check any ajax stuff
	if(isset($_GET['arton']))
	{
		checksession('get');
		$what = is_numeric($_GET['arton']) ? $_GET['arton'] : '0';
		if($what>0)
			$request = tp_query("UPDATE " . $tp_prefix . "articles SET off = IF(off = 0 , 1, 0) WHERE id = " . $what, __FILE__, __LINE__);
		else
			return;
	}
	elseif(isset($_GET['artlock']))
	{
		checksession('get');
		$what = is_numeric($_GET['artlock']) ? $_GET['artlock'] : '0';
		if($what>0)
			$request = tp_query("UPDATE " . $tp_prefix . "articles SET locked = IF(locked = 0 , 1, 0) WHERE id = " . $what, __FILE__, __LINE__);
		else
			return;
	}
	elseif(isset($_GET['artsticky']))
	{
		checksession('get');
		$what = is_numeric($_GET['artsticky']) ? $_GET['artsticky'] : '0';
		if($what>0)
			$request = tp_query("UPDATE " . $tp_prefix . "articles SET sticky = IF(sticky = 0 , 1, 0) WHERE id = " . $what, __FILE__, __LINE__);
		else
			return;
	}
	elseif(isset($_GET['artfront']))
	{
		checksession('get');
		$what = is_numeric($_GET['artfront']) ? $_GET['artfront'] : '0';
		if($what>0)
			$request = tp_query("UPDATE " . $tp_prefix . "articles SET frontpage = IF(frontpage = 0 , 1, 0) WHERE id = " . $what, __FILE__, __LINE__);
		else
			return;
	}
	elseif(isset($_GET['artfeat']))
	{
		checksession('get');
		$what = is_numeric($_GET['artfeat']) ? $_GET['artfeat'] : '0';
		if($what>0)
		{
			$request = tp_query("UPDATE " . $tp_prefix . "articles SET featured = IF(featured = 0 , 1, 0) WHERE id = " . $what, __FILE__, __LINE__);
			$request = tp_query("UPDATE " . $tp_prefix . "articles SET featured = 0 WHERE id != " . $what, __FILE__, __LINE__);
		}
		else
			return;
	}
	elseif(isset($_GET['catdelete']))
	{
		checksession('get');
		$what = is_numeric($_GET['catdelete']) ? $_GET['catdelete'] : '0';
		if($what>0)
		{
			// first get info
			$request = tp_query("SELECT id,value2 FROM " . $tp_prefix . "variables WHERE id = " . $what ." LIMIT 1" , __FILE__, __LINE__);
			$row = tpdb_fetch_assoc($request);
			tpdb_free_result($request);
			
			$newcat = !empty($row['value2']) ? $row['value2'] : 0;
			$request = tp_query("UPDATE " . $tp_prefix . "variables SET value2 = " . $newcat . " WHERE value2 = " . $what , __FILE__, __LINE__);

			$request = tp_query("DELETE FROM " . $tp_prefix . "variables WHERE id = " . $what , __FILE__, __LINE__);
			$request = tp_query("UPDATE " . $tp_prefix . "articles SET category = " . $newcat . " WHERE category = " . $what, __FILE__, __LINE__);
			redirectexit('action=tpadmin;sa=categories');
		}
		else
			redirectexit('action=tpadmin;sa=categories');
	}
	elseif(isset($_GET['artdelete']))
	{
		checksession('get');
		$what = is_numeric($_GET['artdelete']) ? $_GET['artdelete'] : '0';
		$cu = is_numeric($_GET['cu']) ? $_GET['cu'] : '';
		if($cu == -1)
		{
			$strays=true;
			$cu='';
		}
		if($what>0)
			$request = tp_query("DELETE FROM " . $tp_prefix . "articles WHERE id = " . $what , __FILE__, __LINE__);

		redirectexit('action=tpadmin' . (!empty($cu) ? ';cu='.$cu : '') . (isset($strays) ? ';sa=strays'.$cu : ';sa=articles'));
	}

	// for the non-category articles, do a count.
	$request = tp_query("
		SELECT	COUNT(*) as total
		FROM " . $tp_prefix . "articles
		WHERE category = 0 OR category = 9999", __FILE__, __LINE__);

	$row = tpdb_fetch_assoc($request);
	$context['TPortal']['total_nocategory'] = $row['total'];
	tpdb_free_result($request);

	// for the submissions too
	$request = tp_query("
		SELECT	COUNT(*) as total
		FROM " . $tp_prefix . "articles
		WHERE approved=0", __FILE__, __LINE__);

	$row = tpdb_fetch_assoc($request);
	$context['TPortal']['total_submissions'] = $row['total'];
	tpdb_free_result($request);

	// we are on categories screen
	if(in_array($context['TPortal']['subaction'], array('categories','addcategory')))
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=categories', $txt['tp-categories']);
		// first check if we simply want to copy or set as child
		if(isset($_GET['cu']) && is_numeric($_GET['cu']))
		{
			$ccat = $_GET['cu'];
			if(isset($_GET['copy']))
			{
				$request = tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE id = ". $ccat, __FILE__, __LINE__);
				if(tpdb_num_rows($request)>0)
				{
					$row=tpdb_fetch_assoc($request);
					$row['value1'] .= '__copy'; 
					tpdb_free_result($request);
					$request = tp_query("INSERT INTO " . $tp_prefix . "variables 
					(value1, value2, value3, type, value4, value5,subtype,value7,value8,subtype2)
					VALUES('" . $row['value1'] . "' ,'" . $row['value2'] . "' ,'" . $row['value3'] . "' ,'" . $row['type'] . "' ,'" . $row['value4'] . "' ,
					'" . $row['value5'] . "' ,'" . $row['subtype'] . "' ,'" . $row['value7'] . "' ,'" . $row['value8'] . "' ,'" . $row['subtype2'] . "')", __FILE__, __LINE__);
				}
				redirectexit('action=tpadmin;sa=categories');
			}
			elseif(isset($_GET['child']))
			{
				$request = tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE id = ". $ccat, __FILE__, __LINE__);
				if(tpdb_num_rows($request)>0)
				{
					$row=tpdb_fetch_assoc($request);
					$row['value1'] .= '__copy'; 
					tpdb_free_result($request);
					$request = tp_query("INSERT INTO " . $tp_prefix . "variables 
					(value1, value2, value3, type, value4, value5,subtype,value7,value8,subtype2)
					VALUES('" . $row['value1'] . "' ,'" . $row['id'] . "' ,'" . $row['value3'] . "' ,'" . $row['type'] . "' ,'" . $row['value4'] . "' ,
					'" . $row['value5'] . "' ,'" . $row['subtype'] . "' ,'" . $row['value7'] . "' ,'" . $row['value8'] . "' ,'" . $row['subtype2'] . "')", __FILE__, __LINE__);
				}
				redirectexit('action=tpadmin;sa=categories');
			}
			// guess we only want the category then
			else
			{
				// get membergroups
				get_grps();
			$context['html_headers'] .= '
			<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
				function changeIllu(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/illustrations/\' + name; 
				}

				function changeIcon(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/icons/\' + name; 
				}
			// ]]></script>';

				$request = tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE id = ". $ccat. " LIMIT 1", __FILE__, __LINE__);
				if(tpdb_num_rows($request)>0)
				{
					$row = tpdb_fetch_assoc($request);
					$row['value1'] = html_entity_decode($row['value1']);
					$o = explode("|",$row['value7']);
					foreach($o as $t => $opt)
					{
						$b = explode("=",$opt); 
						if(isset($b[1]))
							$row[$b[0]] = $b[1];
					}
					tpdb_free_result($request);
					$check = array('layout','catlayout','toppanel','bottompanel','leftpanel','rightpanel','upperpanel','lowerpanel','showchild');
					foreach($check as $c => $ch)
					{
						if(!isset($row[$ch]))
							$row[$ch] = 0;
					}
					$context['TPortal']['editcategory'] = $row;		
				}
				// fetch all categories and subcategories
				$request = tp_query("
					SELECT	id, value1 as name, value2 as parent, value3, value4, value5,subtype, value7,value8,subtype2 
					FROM " . $tp_prefix . "variables
					WHERE type = 'category'", __FILE__, __LINE__);
				
				$context['TPortal']['editcats']=array(); $allsorted=array(); $alcats = array();
				if(tpdb_num_rows($request)>0)
				{
					while ($row = tpdb_fetch_assoc($request))
					{
						$row['indent'] = 0;
						$row['name'] = html_entity_decode($row['name']);
						$allsorted[$row['id']] = $row;
						$alcats[] = $row['id'];
					}
					tpdb_free_result($request);
					if(count($allsorted)>1)
						$context['TPortal']['editcats'] = chain('id', 'parent', 'name', $allsorted);
					else
						$context['TPortal']['editcats'] = $allsorted;
				}
				TPadd_linktree($scripturl.'?action=tpadmin;sa=categories;cu='. $ccat, $txt['tp-editcategory']);
			}
			return;
		}
		
		// fetch all categories and subcategories
		$request = tp_query("
			SELECT	id, value1 as name, value2 as parent, value3, value4, value5,subtype, value7,value8,subtype2 
			FROM " . $tp_prefix . "variables
			WHERE type = 'category'", __FILE__, __LINE__);
		
		$context['TPortal']['editcats']=array(); $allsorted=array(); $alcats = array();
		if(tpdb_num_rows($request)>0)
		{
			while ($row = tpdb_fetch_assoc($request))
			{
				$row['indent'] = 0;
				$row['name'] = html_entity_decode($row['name']);
				$allsorted[$row['id']] = $row;
				$alcats[] = $row['id'];
			}
			tpdb_free_result($request);
			if(count($allsorted)>1)
				$context['TPortal']['editcats'] = chain('id', 'parent', 'name', $allsorted);
			else
				$context['TPortal']['editcats'] = $allsorted;
		}
		// get the filecount as well
		if(count($alcats)>0)
		{
			$request = tp_query("
				SELECT	art.category as id, COUNT(art.id) as files 
				FROM " . $tp_prefix . "articles as art
				WHERE art.category IN (" . implode(',',$alcats) . ")
				GROUP BY art.category", __FILE__, __LINE__);
			
			if(tpdb_num_rows($request)>0)
			{
				$context['TPortal']['cats_count']=array();
				while ($row = tpdb_fetch_assoc($request))
					$context['TPortal']['cats_count'][$row['id']] = $row['files'];
				tpdb_free_result($request);
			}
		}
		if($context['TPortal']['subaction']=='addcategory')
			TPadd_linktree($scripturl.'?action=tpadmin;sa=addcategory', $txt['tp-addcategory']);
		
		return;
	}
	TPadd_linktree($scripturl.'?action=tpadmin;sa=articles', $txt['tp-articles']);
	// are we inside a category?
	if(isset($_GET['cu']) && is_numeric($_GET['cu']))
	{
		$where = $_GET['cu'];
	}
	// show the no category articles?
	if(isset($_GET['sa']) && $_GET['sa']=='strays')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=strays', $txt['tp-strays']);
		$show_nocategory = true;
	}
	// submissions?
	if(isset($_GET['sa']) && $_GET['sa']=='submission')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=submission', $txt['tp-submissions']);
		$show_submission = true;
	}
	// single article?
	if(isset($_GET['sa']) && substr($_GET['sa'],0,11)=='editarticle')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa='.$_GET['sa'], $txt['tp-editarticle']);
		$whatarticle = substr($_GET['sa'],11);
	}
	// are we starting a new one?
	if(isset($_GET['sa']) && substr($_GET['sa'],0,11)=='addarticle_')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa='.$_GET['sa'], $txt['tp-addarticle']);
		$context['TPortal']['editarticle'] = array(		
            'id' => '',
            'date' => time(),
            'body' => '',
            'intro' => '',
            'useintro' => 0,
            'category' => !empty($_GET['cu']) ? $_GET['cu'] : 0,
            'frontpage' => 1,
            'author_id' => $context['user']['id'],
            'subject' => '',
            'author' => $context['user']['name'],
            'frame' => 'theme',
            'approved' => 0,
            'off' => 1,
            'options' => 'date,title,author,linktree,top,cblock,rblock,lblock,bblock,tblock,lbblock,category,catlist,comments,commentallow,commentupshrink,views,rating,ratingallow,avatar,inherit,social,globaltags,nofrontsetting',
            'parse' => 0,
            'comments' => 0,
            'comments_var' => '',
            'views' => 0,
            'rating' => 0,
            'voters' => '',
            'id_theme' => 0,
            'shortname' => '',
            'sticky' => 0,
            'fileimport' => '',
            'topic' => 0,
            'locked' => 0,
            'illustration' => '',
            'headers' => '',
            'type' => substr($_GET['sa'],11),
            'global_tag' => '',
            'featured' => 0,
            'realName' => $context['user']['name'],
            'authorID' => $context['user']['id'],
            'articletype' => substr($_GET['sa'],11),
            'ID_THEME' => 0,
        );
		$context['html_headers'] .= '
			<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
				function changeIllu(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/illustrations/\' + name; 
				}

				function changeIcon(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/icons/\' + name; 
				}
			// ]]></script>';
	}
	// fetch categories and subcategories
	if(!isset($show_nocategory))
	{
		$request = tp_query("
			SELECT	DISTINCT var.id as id, var.value1 as name, var.value2 as parent 
			FROM " . $tp_prefix . "variables AS var
			WHERE var.type = 'category'
			" . (isset($where) ? 'AND var.value2=' . $where : '') . "
			ORDER BY parent, id DESC", __FILE__, __LINE__);
		
		if(tpdb_num_rows($request)>0)
		{
			$context['TPortal']['basecats'] = isset($where) ? array($where) : array('0','9999');
			$cats = array();
			$context['TPortal']['cats']=array(); $sorted=array();
			while ($row = tpdb_fetch_assoc($request))
			{
				$row['name'] = html_entity_decode($row['name']);
				$sorted[$row['id']] = $row;
				$cats[] = $row['id'];
			}
			tpdb_free_result($request);
			if(count($sorted)>1)
				$context['TPortal']['cats'] = chain('id', 'parent', 'name', $sorted);
			else
				$context['TPortal']['cats'] = $sorted;
		}
	}

	if(isset($show_submission) && $context['TPortal']['total_submissions']>0)
	{
		// clean up notices
		$request2 =  tp_query("SELECT id,value5 FROM " . $tp_prefix . "variables WHERE type = 'art_not_approved'", __FILE__, __LINE__);
		if(tpdb_num_rows($request2)>0)
		{
			$ids=array();
			while($row=tpdb_fetch_assoc($request2))
				$ids[$row['id']] = $row['value5'];
			tpdb_free_result($request2);
		}
		$request =  tp_query("SELECT id,approved FROM " . $tp_prefix . "articles WHERE FIND_IN_SET(id, '".implode(',', $ids)."')", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			while($row=tpdb_fetch_assoc($request))
			{
				
			}
			tpdb_free_result($request2);
		}
				
		
		// check if we have any start values
		$start = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'],array('date','id','author_id' , 'type','subject','parse'))) ? $_GET['sort'] : 'date';
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpadmin;sa=submission;sort=' . $sort , $start, $context['TPortal']['total_submissions'], 15);
		$request = tp_query("
			SELECT	art.id,art.date,art.frontpage, art.category, art.authorID, IFNULL(mem.realName, art.author) as author, art.subject,
			art.approved,art.sticky,art.type, art.featured,art.locked, art.off, art.parse as pos	
			FROM " . $tp_prefix . "articles AS art
			LEFT JOIN " . $db_prefix . "members AS mem ON (art.authorID = mem.ID_MEMBER)
			WHERE art.approved=0
			ORDER BY art." . $sort . " " . (in_array($sort, array('sticky','locked','frontpage','date','active')) ? 'DESC' : 'ASC') . " 
			LIMIT " . $start .",15", __FILE__, __LINE__);
		
		if(tpdb_num_rows($request)>0)
		{
			$context['TPortal']['arts_submissions']=array();
			while ($row = tpdb_fetch_assoc($request))
			{
				$row['subject'] = html_entity_decode($row['subject']);
				$context['TPortal']['arts_submissions'][] = $row;
			}
			tpdb_free_result($request);
		}
	}

	if(isset($show_nocategory) && $context['TPortal']['total_nocategory']>0)
	{
		// check if we have any start values
		$start = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'],array('off', 'date','id','author_id' , 'locked', 'frontpage','sticky','featured','type','subject','parse'))) ? $_GET['sort'] : 'date';
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpadmin;sa=articles;sort=' . $sort , $start, $context['TPortal']['total_nocategory'], 15);
		$request = tp_query("
			SELECT	art.id,art.date,art.frontpage, art.category, art.authorID, IFNULL(mem.realName, art.author) as author, art.subject,
			art.approved,art.sticky,art.type, art.featured,art.locked, art.off, art.parse as pos	
			FROM " . $tp_prefix . "articles AS art
			LEFT JOIN " . $db_prefix . "members AS mem ON (art.authorID = mem.ID_MEMBER)
			WHERE (art.category = 0 OR art.category = 9999)
			ORDER BY art." . $sort . " " . (in_array($sort, array('sticky','locked','frontpage','date','active')) ? 'DESC' : 'ASC') . " 
			LIMIT " . $start .",15", __FILE__, __LINE__);
		
		if(tpdb_num_rows($request)>0)
		{
			$context['TPortal']['arts_nocat']=array();
			while ($row = tpdb_fetch_assoc($request))
			{
				$row['subject'] = html_entity_decode($row['subject']);
				$context['TPortal']['arts_nocat'][] = $row;
			}
			tpdb_free_result($request);
		}
	}
	// ok, fetch single article
	if(isset($whatarticle))
	{
		$request = tp_query("
			SELECT	art.*, IFNULL(mem.realName, art.author) as realName, art.type as articletype 
			FROM " . $tp_prefix . "articles as art
			LEFT JOIN " . $db_prefix . "members as mem ON (art.authorID = mem.ID_MEMBER)
			WHERE art.id = " . (is_numeric($whatarticle) ? $whatarticle : 0), __FILE__, __LINE__);
		
		if(tpdb_num_rows($request)>0)
		{
			$context['TPortal']['editarticle']= tpdb_fetch_assoc($request);
			$context['TPortal']['editarticle']['body'] = html_entity_decode($context['TPortal']['editarticle']['body'],ENT_QUOTES, $modSettings['global_character_set']);
			$context['TPortal']['editarticle']['intro'] = html_entity_decode($context['TPortal']['editarticle']['intro'],ENT_QUOTES, $modSettings['global_character_set']);
			$context['TPortal']['editarticle']['subject'] = html_entity_decode($context['TPortal']['editarticle']['subject'], ENT_COMPAT, $modSettings['global_character_set']);
			
			tpdb_free_result($request);
		}
		// fetch the WYSIWYG value
		$request = tp_query("SELECT value1 FROM " . $tp_prefix . "variables WHERE subtype2=" . $whatarticle  . "  AND type='editorchoice' LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$row=tpdb_fetch_assoc($request);
			tpdb_free_result($request);
			$context['TPortal']['editorchoice'] = $row['value1'];
		}
		else
			$context['TPortal']['editorchoice'] = 1;
		
		$context['html_headers'] .= '
			<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
				function changeIllu(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/illustrations/\' + name; 
				}

				function changeIcon(node,name)
				{
					node.src = \'' . $boardurl . '/tp-files/tp-articles/icons/\' + name; 
				}
			// ]]></script>';

	}
	// fetch articlecount for these
	if(isset($cats))
	{
		$request = tp_query("
			SELECT	art.category as id, COUNT(art.id) as files 
			FROM " . $tp_prefix . "articles as art
			WHERE art.category IN (" . implode(',',$cats) . ")
			GROUP BY art.category", __FILE__, __LINE__);
		
		$context['TPortal']['cats_count']=array();
		if(tpdb_num_rows($request)>0)
		{
			while ($row = tpdb_fetch_assoc($request))
				$context['TPortal']['cats_count'][$row['id']] = $row['files'];
			tpdb_free_result($request);
		}
	}
	// get the icons needed
	tp_collectArticleIcons();

	// fetch all categories and subcategories
	$request = tp_query("
		SELECT	id, value1 as name, value2 as parent 
		FROM " . $tp_prefix . "variables
		WHERE type = 'category'", __FILE__, __LINE__);
	
	$context['TPortal']['allcats']=array(); $allsorted=array();
	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
			$allsorted[$row['id']] = $row;

		tpdb_free_result($request);
		if(count($allsorted)>1)
			$context['TPortal']['allcats'] = chain('id', 'parent', 'name', $allsorted);
		else
			$context['TPortal']['allcats'] = $allsorted;
	}
	// not quite done yet lol, now we need to sort out if articles are to be listed
	if(isset($where))
	{
		// check if we have any start values
		$start = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'],array('off', 'date','id','author_id' , 'locked', 'frontpage','sticky','featured','type','subject','parse'))) ? $_GET['sort'] : 'date';
		$context['TPortal']['categoryID'] = $where;
		// get the name
		$request = tp_query("SELECT value1 FROM " . $tp_prefix . "variables WHERE id = " . $where . " LIMIT 1" , __FILE__, __LINE__);
		$f = tpdb_fetch_assoc($request);
		tpdb_free_result($request);
		$context['TPortal']['categoryNAME'] = $f['value1'];
		// get the total first
		$request = tp_query("
			SELECT	COUNT(*) as total
			FROM " . $tp_prefix . "articles
			WHERE category = " . $where , __FILE__, __LINE__);

		$row = tpdb_fetch_assoc($request);
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpadmin;sa=articles;sort=' . $sort . ';cu=' . $where, $start, $row['total'], 15);
		tpdb_free_result($request);

		$request = tp_query("
			SELECT	art.id,art.date,art.frontpage, art.category, art.authorID, IFNULL(mem.realName, art.author) as author, art.subject,
			art.approved,art.sticky,art.type, art.featured,art.locked, art.off, art.parse as pos	
			FROM " . $tp_prefix . "articles AS art
			LEFT JOIN " . $db_prefix . "members AS mem ON (art.authorID = mem.ID_MEMBER)
			WHERE art.category = " . $where . "
			ORDER BY art." . $sort . " " . (in_array($sort, array('sticky','locked','frontpage','date','active')) ? 'DESC' : 'ASC') . " 
			LIMIT " . $start .",15", __FILE__, __LINE__);
		TPadd_linktree($scripturl.'?action=tpadmin;sa=articles;cu='.$where, $txt['tp-blocktype19']);
		
		if(tpdb_num_rows($request)>0)
		{
			$context['TPortal']['arts']=array();
			while ($row = tpdb_fetch_assoc($request))
			{
				$row['subject'] = html_entity_decode($row['subject']);
				$context['TPortal']['arts'][] = $row;
			}
			tpdb_free_result($request);
		}
	}
	$context['html_headers'] .= '
	<script language="JavaScript" type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/editor.js?rc1"></script>
	<script language="JavaScript" type="text/javascript">
		function getXMLHttpRequest()
		{
			if (window.XMLHttpRequest)
				return new XMLHttpRequest;
			else if (window.ActiveXObject)
				return new ActiveXObject("MICROSOFT.XMLHTTP");
			else
				alert("Sorry, but your browser does not support Ajax");
		}
		
		window.onload = startToggle;
		
		function startToggle()
		{
			var img = document.getElementsByTagName("img");
			
			for(var i = 0; i < img.length; i++)
			{
				if (img[i].className == "toggleFront")
					img[i].onclick = toggleFront;
				else if (img[i].className == "toggleSticky")
					img[i].onclick = toggleSticky;
				else if (img[i].className == "toggleLock")
					img[i].onclick = toggleLock;
				else if (img[i].className == "toggleActive")
					img[i].onclick = toggleActive;
				else if (img[i].className == "toggleFeatured")
					img[i].onclick = toggleFeatured;
			}
		}
		
		function toggleActive(e)
		{ 
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;
		
			while(target.className != "toggleActive")
				  target = target.parentNode;
			
			var id = target.id.replace("artActive", "");
			var Ajax = getXMLHttpRequest();
			
			Ajax.open("POST", "?action=tpadmin;arton=" + id + ";sesc='.$context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPactive2.gif" ? "' . $settings['tp_images_url'] . '/TPactive1.gif" : "' . $settings['tp_images_url'] . '/TPactive2.gif";
				}
			}
			
			var params = "?action=tpadmin;arton=" + id + ";sesc='.$context['session_id'].'";
			Ajax.send(params);
		}
		function toggleFront(e)
		{ 
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;
		
			while(target.className != "toggleFront")
				  target = target.parentNode;
			
			var id = target.id.replace("artFront", "");
			var Ajax = getXMLHttpRequest();
			
			Ajax.open("POST", "?action=tpadmin;artfront=" + id + ";sesc='.$context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPfront.gif" ? "' . $settings['tp_images_url'] . '/TPfront2.gif" : "' . $settings['tp_images_url'] . '/TPfront.gif";
				}
			}
			
			var params = "?action=tpadmin;artfront=" + id + ";sesc='.$context['session_id'].'";
			Ajax.send(params);
		}
		function toggleSticky(e)
		{ 
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;
		
			while(target.className != "toggleSticky")
				  target = target.parentNode;
			
			var id = target.id.replace("artSticky", "");
			var Ajax = getXMLHttpRequest();
			
			Ajax.open("POST", "?action=tpadmin;artsticky=" + id + ";sesc='.$context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPsticky1.gif" ? "' . $settings['tp_images_url'] . '/TPsticky2.gif" : "' . $settings['tp_images_url'] . '/TPsticky1.gif";
				}
			}
			
			var params = "?action=tpadmin;artsticky=" + id + ";sesc='.$context['session_id'].'";
			Ajax.send(params);
		}
		function toggleLock(e)
		{ 
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;
		
			while(target.className != "toggleLock")
				  target = target.parentNode;
			
			var id = target.id.replace("artLock", "");
			var Ajax = getXMLHttpRequest();
			
			Ajax.open("POST", "?action=tpadmin;artlock=" + id + ";sesc='.$context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPlock1.gif" ? "' . $settings['tp_images_url'] . '/TPlock2.gif" : "' . $settings['tp_images_url'] . '/TPlock1.gif";
				}
			}
			
			var params = "?action=tpadmin;artlock=" + id + ";sesc='.$context['session_id'].'";
			Ajax.send(params);
		}
		function toggleFeatured(e)
		{ 
			var e = e ? e : window.event;
			var target = e.target ? e.target : e.srcElement;
		
			var aP=document.getElementsByTagName(\'img\');
			for(var i=0; i<aP.length; i++) 
			{
				if(aP[i].className===\'toggleFeatured\' && aP[i] != target) 
				{
					aP[i].src=\'' . $settings['tp_images_url'] . '/TPflag2.gif\';
				}
			}

			
			while(target.className != "toggleFeatured")
				  target = target.parentNode;
			
			var id = target.id.replace("artFeatured", "");
			var Ajax = getXMLHttpRequest();
			
			Ajax.open("POST", "?action=tpadmin;artfeat=" + id + ";sesc='.$context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPflag.gif" ? "' . $settings['tp_images_url'] . '/TPflag2.gif" : "' . $settings['tp_images_url'] . '/TPflag.gif";
				}
			}
			
			var params = "?action=tpadmin;artfeat=" + id + ";sesc='.$context['session_id'].'";
			Ajax.send(params);
		}
	</script>
	';
	if($context['TPortal']['subaction']=='artsettings')
		TPadd_linktree($scripturl.'?action=tpadmin;sa=artsettings', $txt['tp-settings']);
	elseif($context['TPortal']['subaction']=='articons')
		TPadd_linktree($scripturl.'?action=tpadmin;sa=articons', $txt['tp-adminicons']);

}

function do_modules()
{
	global $context,$txt,$settings,$boardurl,$scripturl,$boarddir,$userinfo,$db_prefix;

	// prefix of the TP tables
	$tp_prefix = $db_prefix.'tp_';
	
	isAllowedTo('tp_settings');
	
	// tags maybe?
	if(isset($_GET['tags']))
	{
		$context['TPortal']['global_tags']=array();
		$request = tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='globaltag'", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			while($row=tpdb_fetch_assoc($request))
			{
				$context['TPortal']['global_tags'][$row['value1']] = array(
					'id' => $row['id'],
					'tag' => $row['value1'],
					'related' => $row['value2'],
				);
			}
			tpdb_free_result($request);
		}
	}
	else
	{
		$context['TPortal']['adm_modules']=array();
		// fetch modules
		$request = tp_query("SELECT * FROM " . $tp_prefix . "modules WHERE 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			while ($row = tpdb_fetch_assoc($request))
				$context['TPortal']['adm_modules'][] = $row;
			tpdb_free_result($request);
		}
		$context['TPortal']['internal_modules'][]=array(
				'adminlink' => '<a href="'.$scripturl.'?action=tpmod;dl=admin">'.$txt['tp-mod-dladmin'].'</a>',
				'modulelink' => '<a href="'.$scripturl.'?action=tpmod;dl=0">'.$txt['tp-mod-dlmanager'].'</a>',
				'state' => $context['TPortal']['show_download'],
				'fieldname' => 'tp_show_download',
			);
	}
}

function do_tags()
{
	global $context,$txt,$settings,$boardurl,$scripturl,$boarddir,$userinfo,$db_prefix;

	// prefix of the TP tables
	$tp_prefix = $db_prefix.'tp_';
	
	isAllowedTo('tp_settings');
	
}

function do_news($tpsub = 'overview')
{
	global $context,$txt,$settings,$boardurl,$scripturl,$boarddir,$userinfo,$db_prefix;
	
	get_boards();
	$context['TPortal']['SSI_boards'] = explode(",",$context['TPortal']['SSI_board']);
	
	if($tpsub == 'overview')
	{
		if(!TPcheckAdminAreas())
			fatal_error($txt['tp-notallowed']);
	}
	elseif($tpsub == 'permissions')
	{
		TPadd_linktree($scripturl.'?action=tpadmin;sa=permissions', $txt['tp-permissions']);
		$context['TPortal']['perm_all_groups'] = get_grps(true,true);
		$context['TPortal']['perm_groups'] = tp_fetchpermissions($context['TPortal']['modulepermissions']);
	}
	else
	{
		if($tpsub == 'news')
			TPadd_linktree($scripturl.'?action=tpadmin;sa=news', $txt[102]);
		elseif($tpsub == 'settings')
			TPadd_linktree($scripturl.'?action=tpadmin;sa=settings', $txt['tp-settings']);
		elseif($tpsub == 'frontpage')
			TPadd_linktree($scripturl.'?action=tpadmin;sa=frontpage', $txt['tp-frontpage']);

		isAllowedTo('tp_settings');
	}
}

function do_postchecks()
{
	global $context,$txt,$settings,$boardurl,$scripturl,$boarddir,$userinfo,$db_prefix, $sourcedir;

	// prefix of the TP tables
	$tp_prefix = $db_prefix.'tp_';

	// tag links from topics?
	if(isset($_POST['tpadmin_topictags']))
	{
		$itemid=$_POST['tpadmin_topictags'];
		// get title
		$request=tp_query("SELECT m.subject FROM " . $db_prefix . "messages as m, " . $db_prefix . "topics as t 
		WHERE t.ID_TOPIC=$itemid 
		AND t.ID_FIRST_MSG=m.ID_MSG
		LIMIT 1", __FILE__, __LINE__);
		$title=tpdb_fetch_row($request);
		tpdb_free_result($request);
		
		// remove old ones first
		tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type ='globaltag_item' AND value3='tpadmin_topictags' AND subtype2=$itemid", __FILE__, __LINE__);
		foreach($_POST as $what => $value)
		{
			// a tag from edit items
			if(substr($what,0,17)=='tpadmin_topictags' && !empty($value))
			{
				$tag=substr($what,18);
				$itemid=$value;
				// insert new one
				$href='?topic='.$itemid.'.0';
				$subject = '<span style="background: url('.$settings['tp_images_url'].'/glyph_topic.png) no-repeat;" class="taglink">' . $title[0]. '</span>';
				if(!empty($tag))
				{
					tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5,subtype,value7,value8,subtype2) 
					VALUES('$href','$subject','tpadmin_topictags','globaltag_item','',0,'$tag','','',$itemid)", __FILE__, __LINE__);
				}
			}
			elseif(substr($what,0,22)=='tpadmin_topictags_xyzx' && !empty($value))
			{
				// create the tag as well
				$itemid=substr($what,22);
				$allowed="/[^a-zA-Z0-9_]/";
				$value=preg_replace($allowed,"",$value);
				$tag=$value;
				tp_query("REPLACE INTO " . $tp_prefix . "variables (value1,type) VALUES('$value','globaltag')", __FILE__, __LINE__);
			
				// insert new one
				$href='?topic='.$itemid.'.0';
				$subject = '<span style="background: url('.$settings['tp_images_url'].'/glyph_topic.png) no-repeat;" class="taglink">' . $title[0]. '</span>';
				if(!empty($tag))
				{
					tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5,subtype,value7,value8,subtype2) 
					VALUES('$href','$subject','tpadmin_topictags','globaltag_item','',0,'$tag','','',$itemid)", __FILE__, __LINE__);
				}
			}
		}
		redirectexit('topic='.$itemid);
	}
	// tag links from boards?
	if(isset($_POST['tpadmin_boardtags']))
	{
		$itemid=$_POST['tpadmin_boardtags'];
		// get title
		$request=tp_query("SELECT name FROM " . $db_prefix . "boards 
		WHERE ID_BOARD = " . $itemid . " 
		LIMIT 1", __FILE__, __LINE__);
		$title=tpdb_fetch_row($request);
		tpdb_free_result($request);
		// remove old ones first
		tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type ='globaltag_item' AND value3='tpadmin_boardtags' AND subtype2=$itemid", __FILE__, __LINE__);
		foreach($_POST as $what => $value)
		{
			// a tag from edit items
			if(substr($what,0,17)=='tpadmin_boardtags' && !empty($value))
			{
				$tag=substr($what,18);
				$itemid=$value;
				// insert new one
				$href='?board='.$itemid.'.0';
				$subject = '<div style="background: url('.$settings['tp_images_url'].'/glyph_board.png) no-repeat;" class="taglink">' . $title[0]. '</div>';
				if(!empty($tag))
				{
					tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5,subtype,value7,value8,subtype2) 
					VALUES('$href','$subject','tpadmin_boardtags','globaltag_item','',0,'$tag','','',$itemid)", __FILE__, __LINE__);
				}
			}
			elseif(substr($what,0,23)=='xyzx_tpadmin_boardtags_' && !empty($value))
			{
				// create the tag as well
				$itemid=substr($what,23);
				$allowed="/[^a-zA-Z0-9_]/";
				$value=preg_replace($allowed,"",$value);
				$tag=$value;
				tp_query("REPLACE INTO " . $tp_prefix . "variables (value1,type) VALUES('$value','globaltag')", __FILE__, __LINE__);
			
				// insert new one
				$href='?board='.$itemid.'.0';
				$subject = '<span style="background: url('.$settings['tp_images_url'].'/glyph_board.png) no-repeat;" class="taglink">' . $title[0]. '</span>';
				if(!empty($tag))
				{
					tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5,subtype,value7,value8,subtype2) 
					VALUES('$href','$subject','tpadmin_boardtags','globaltag_item','',0,'$tag','','',$itemid)", __FILE__, __LINE__);
				}
			}
		}
		redirectexit('board='.$itemid);
	}
	

	// which screen do we come frm?
	if(!empty($_POST['tpadmin_form']))
	{
		// get it
		$from = $_POST['tpadmin_form'];
		//news
		if($from == 'news')
			return 'news';
		// settings and frontpage
		elseif($from=='perms')
		{
			checkSession('post');
			isAllowedTo('tp_settings');
			
			tp_query("DELETE FROM " . $db_prefix . "permissions WHERE FIND_IN_SET(permission, '" . implode(",",$context['TPortal']['modulepermissions']) . "')", __FILE__, __LINE__);

			foreach($_POST as $what => $value)
			{
				$where = strpos($what,'_perm_');
				if($where === false)
					continue;
				else
				{
					$perm = substr($what,0,$where);
 					tp_query("INSERT INTO " . $db_prefix . "permissions (id_group,permission,add_deny) VALUES(". $value. ", '".$perm."',1)", __FILE__, __LINE__);
				}
			}
			return 'permissions';
		}
		// settings and frontpage
		elseif($from=='blockoverview')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			$block = array();
			foreach($_POST as $what => $value)
			{
				if(substr($what,5,7)=='tpblock')
				{
					// get the id
					$bid = substr($what,12);
					if(!is_array($block[$bid]))
						$block[$bid]=array();

					if($value != 'control' && !in_array($value,$block[$bid]))
						$block[$bid][]=$value;
				}
			}
			foreach($block as $bl => $blo)
			{
				$request = tp_query("SELECT access FROM " . $tp_prefix . "blocks WHERE id = ".$bl, __FILE__, __LINE__);
				if(tpdb_num_rows($request) > 0)
				{
					$row = tpdb_fetch_assoc($request);
					tpdb_free_result($request);
					$request = tp_query("UPDATE " . $tp_prefix . "blocks SET access = '" . implode(",",$blo) . "' WHERE id = ".$bl, __FILE__, __LINE__);
				}
			}
			return 'blocks;overview';
		}
		elseif(in_array($from, array('settings', 'frontpage', 'artsettings', 'panels')))
		{
			checkSession('post');
			isAllowedTo('tp_settings');
			$w = array(); $ssi = array();
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0,3) == 'tp_')
				{
					$where = substr($what,3);
					$clean =$value;
					// for frontpage, do some extra
					if($from == 'frontpage')
					{
						if(substr($what,0,20)=='tp_frontpage_visual_')
						{
							$w[]=substr($what,20);
							unset($clean);
						}
						elseif(substr($what,0,21)=='tp_frontpage_usorting')
						{
							$w[]='sort_'.$value;
							unset($clean);
						}
						elseif(substr($what,0,26)=='tp_frontpage_sorting_order')
						{
							$w[]='sortorder_'.$value;
							unset($clean);
						}
						// SSI boards
						elseif(substr($what,0,11)=='tp_ssiboard')
						{
							if($value !=0)
								$ssi[$value] = $value;
						}
					}
					if($from == 'settings' && $what == 'tp_frontpage_title')
						tp_query("UPDATE " . $tp_prefix . "settings SET value = '" . htmlentities($clean, ENT_QUOTES) . "' WHERE name='frontpage_title' LIMIT 1", __FILE__, __LINE__);
					else
					{
						if(isset($clean))
							tp_query("UPDATE " . $tp_prefix . "settings SET value = '" . $clean . "' WHERE name='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
					}
				}
			}
			// check the frontpage visual setting..
			if($from == 'frontpage')
			{
				tp_query("UPDATE " . $tp_prefix . "settings SET value = '" . implode(",",$w) . "' WHERE name='frontpage_visual'", __FILE__, __LINE__);
				// SSI boards
				tp_query("UPDATE " . $tp_prefix . "settings SET value = '" . implode(",",$ssi) . "' WHERE name='SSI_board'", __FILE__, __LINE__);
			}
			return $from;
		}
		// categories
		elseif($from == 'categories')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0,3) == 'tp_')
				{
					$clean = tp_sanitize($value);
					// for frontpage, do some extra
					if($from == 'categories')
					{
						if(substr($what,0,19)=='tp_category_value2_')
						{
							$where = tp_sanitize(substr($what,19));
							//make sure parent are not its own parent
							$request =tp_query("SELECT value2 FROM " . $tp_prefix . "variables WHERE id=" . $value  . " LIMIT 1", __FILE__, __LINE__);
							$row = tpdb_fetch_assoc($request);
							tpdb_free_result($request);
							if($row['value2'] == $where)
								tp_query("UPDATE " . $tp_prefix . "variables SET value2 = 0 WHERE id=" . $value  . " LIMIT 1", __FILE__, __LINE__);

							tp_query("UPDATE " . $tp_prefix . "variables SET value2 = '" . $value . "' WHERE id=" . $where  . " LIMIT 1", __FILE__, __LINE__);
						}
					}
				}
			}
			return $from;
		}
		// articles
		elseif($from == 'articles')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			foreach($_POST as $what => $value)
			{
				if(substr($what,0,14)=='tp_article_pos')
				{
					$where = tp_sanitize(substr($what,14));
						tp_query("UPDATE " . $tp_prefix . "articles SET parse = '" . $value . "' WHERE id=" . $where  . " LIMIT 1", __FILE__, __LINE__);
				}
			}
			if(isset($_POST['tpadmin_form_category']) && is_numeric($_POST['tpadmin_form_category']))
				return $from.';cu=' . $_POST['tpadmin_form_category'];
			else
				return $from;
		}
		// modules
		elseif($from == 'modules')
		{
			checkSession('post');
			isAllowedTo('tp_settings');
			
			foreach($_POST as $what => $value)
			{
				if($what == 'tp_show_download')
					tp_query("UPDATE " . $tp_prefix . "settings SET value = '" . $value . "' WHERE name= 'show_download'", __FILE__, __LINE__);
				elseif(substr($what,0,14) == 'tpmodule_state')
					tp_query("UPDATE " . $tp_prefix . "modules SET active = " . $value . " WHERE id = " . substr($what, 14), __FILE__, __LINE__);
			}
			return $from;
		}
		// all the items
		elseif($from == 'menuitems')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
	
			$all = explode(",",$context['TPortal']['sitemap_items']);
			foreach($_POST as $what => $value)
			{
				if(substr($what,0,8) == 'menu_pos')
					tp_query("UPDATE " . $tp_prefix . "variables SET subtype = '" . tp_sanitize($value) . "' WHERE id= ". substr($what,8), __FILE__, __LINE__);
				elseif(substr($what,0,8) == 'menu_sub')
					tp_query("UPDATE " . $tp_prefix . "variables SET value4 = '" . tp_sanitize($value) . "' WHERE id= ". substr($what,8), __FILE__, __LINE__);
				elseif(substr($what,0,15) == 'tp_menu_sitemap')
				{
					$new = substr($what,15);
					if($value == 0 && in_array($new, $all))
					{
						foreach ($all as $key => $value) 
						{
							if ($all[$key] == $new) 
								unset($all[$key]);
						}
					}
					elseif($value==1 && !in_array($new,$all))
						$all[] = $new;

					tp_query("UPDATE " . $tp_prefix . "settings SET value = '" . implode(',',$all) . "' WHERE name= 'sitemap_items'", __FILE__, __LINE__);
				}
			}
			redirectexit('action=tpadmin;sa=menubox;mid='. $_POST['tp_menuid']);
		}
		// all the menus
		elseif($from == 'menus')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			foreach($_POST as $what => $value)
			{
				if(substr($what,0,12) == 'tp_menu_name')
					tp_query("UPDATE " . $tp_prefix . "variables SET value1 = '" . tp_sanitize($value) . "' WHERE id= ". substr($what,12), __FILE__, __LINE__);
			}
			redirectexit('action=tpadmin;sa=menubox');
		}
		elseif($from == 'singlemenuedit')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			$where = isset($_POST['tpadmin_form_id']) ? $_POST['tpadmin_form_id'] : 0;

			foreach($_POST as $what => $value)
			{
				if($what=='tp_menu_name')
				{
					// make sure special charachters can't be done
					$value = preg_replace('~&#\d+$~', '', $value);
					tp_query("UPDATE " . $tp_prefix . "variables SET value1='" . $value . "' WHERE id=". $where, __FILE__, __LINE__);
				}
				elseif($what=='tp_menu_newlink')
					tp_query("UPDATE " . $tp_prefix . "variables SET value2='" . $value . "' WHERE id=". $where, __FILE__, __LINE__);
				elseif($what =='tp_menu_menuid')
					tp_query("UPDATE " . $tp_prefix . "variables SET subtype2=" . $value . " WHERE id=". $where, __FILE__, __LINE__);
				elseif($what=='tp_menu_type')
				{
					if($value=='cats')
						$idtype='cats'.$_POST['tp_menu_category'];
					elseif($value=='arti')
						$idtype='arti'.$_POST['tp_menu_article'];
					elseif($value=='link')
						$idtype=$_POST['tp_menu_link'];
					elseif($value=='head')
						$idtype='head';
					elseif($value=='spac')
						$idtype='spac';

					tp_query("UPDATE " . $tp_prefix . "variables SET value3='" . $idtype . "' WHERE id=" . $where, __FILE__, __LINE__);
				}
				elseif($what =='tp_menu_sub')
					tp_query("UPDATE " . $tp_prefix . "variables SET value4='" . $value . "' WHERE id=" . $where, __FILE__, __LINE__);
				elseif(substr($what,0,15)=='tp_menu_newlink')
					tp_query("UPDATE " . $tp_prefix . "variables SET value2='" . $value . "' WHERE id=" . $where, __FILE__, __LINE__);
			}
			redirectexit('action=tpadmin;linkedit='.$where.';sesc='.$context['session_id']);
		}
		// modules
		elseif($from == 'tags')
		{
			checkSession('post');
			isAllowedTo('tp_settings');
			
			$mytags=array();
			// first, remove all globaltags
			tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type='globaltag'", __FILE__, __LINE__);

			foreach ($_POST as $what => $value) 
			{
				if(substr($what,0,7)=='tp_tags')
				{
					// check the value, only letters and underscore allowed
					$allowed="/[^a-zA-Z0-9_]/";
					$value = preg_replace($allowed,"",$value); 
					if(!empty($value))
						tp_query("REPLACE INTO " . $tp_prefix . "variables (value1,type) VALUES('$value','globaltag')", __FILE__, __LINE__);
				}
			}
			redirectexit('action=tpadmin;sa=modules;tags');
		}
		// add a category
		elseif($from == 'addcategory')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			$name = !empty($_POST['tp_cat_name']) ? $_POST['tp_cat_name'] : $txt['tp-noname'];
			$parent = !empty($_POST['tp_cat_parent']) ? $_POST['tp_cat_parent'] : 0;

			$request = tp_query("INSERT INTO " . $tp_prefix . "variables 
			(value1, value2, value3, type ,value4, value5, subtype,value7, value8, subtype2, value9) 
			VALUES('" . strip_tags($name) . "','".$parent."','','category','',0,'','',0,'','')", __FILE__, __LINE__);
			$go = tpdb_insert_id($request);
			redirectexit('action=tpadmin;sa=categories;cu='.$go);
		}
		// the categort list
		elseif($from == 'clist')
		{
			checkSession('post');
			isAllowedTo('tp_articles');

			$cats = array(); 
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0,8) == 'tp_clist')
					$cats[] = $value;

			}
			if(sizeof($cats)>0)
				$catnames = implode(",",$cats);
			else
				$catnames = '';

			tp_query("UPDATE " . $tp_prefix . "settings SET value = '" . $catnames . "' WHERE name='cat_list' LIMIT 1", __FILE__, __LINE__);
			return $from;
		}

		// edit a category
		elseif($from == 'editcategory')
		{
			checkSession('post');
			isAllowedTo('tp_articles');

			$options = array(); $groups=array();
			$where = $_POST['tpadmin_form_id'];
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0,3) == 'tp_')
				{
					$clean = tp_sanitize($value);
					$param = substr($what, 12);
					if(in_array($param, array('value5','value6','value8')))
						tp_query("UPDATE " . $tp_prefix . "variables SET " . $param . " = '" . $clean . "' WHERE id=" . $where  . " LIMIT 1", __FILE__, __LINE__);
					// parents needs some checking..
					elseif($param == 'value2')
					{
						//make sure parent are not its own parent
						$request =tp_query("SELECT value2 FROM " . $tp_prefix . "variables WHERE id=" . $value  . " LIMIT 1", __FILE__, __LINE__);
						$row = tpdb_fetch_assoc($request);
						tpdb_free_result($request);
						if($row['value2'] == $where)
							tp_query("UPDATE " . $tp_prefix . "variables SET value2 = 0 WHERE id=" . $value  . " LIMIT 1", __FILE__, __LINE__);

						tp_query("UPDATE " . $tp_prefix . "variables SET value2 = '" . $value . "' WHERE id=" . $where  . " LIMIT 1", __FILE__, __LINE__);
					}
					elseif($param == 'value1')
						tp_query("UPDATE " . $tp_prefix . "variables SET value1 = '" . strip_tags($value) . "' WHERE id=" . $where  . " LIMIT 1", __FILE__, __LINE__);
					elseif($param == 'value4')
						tp_query("UPDATE " . $tp_prefix . "variables SET value4 = '" . $value . "' WHERE id=" . $where  . " LIMIT 1", __FILE__, __LINE__);
					elseif($param == 'value9')
						tp_query("UPDATE " . $tp_prefix . "variables SET value9 = '" . $value . "' WHERE id=" . $where  . " LIMIT 1", __FILE__, __LINE__);
					elseif(substr($param, 0,6) == 'group_')
						$groups[] = substr($param,6);
					else
						$options[] = $param. '=' . $value;
				}
			}
			tp_query("UPDATE " . $tp_prefix . "variables SET value3 = '" . implode(",", $groups) . "', value7 = '" . implode("|",$options) . "' WHERE id=" . $where  . " LIMIT 1", __FILE__, __LINE__);
			$from = 'categories;cu=' . $where;
			return $from;
		}
		// stray articles
		elseif($from == 'strays')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			$ccats = array();
			// check if we have some values
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0,16) == 'tp_article_stray')
					$ccats[] = substr($what,16);
				elseif($what == 'tp_article_cat')
					$straycat = $value;
				elseif($what == 'tp_article_new')
					$straynewcat = htmlentities($value,ENT_QUOTES);
			}	
			// update
			if(isset($straycat) && sizeof($ccats)>0)
			{
				$category = $straycat;
				if($category == 0 && !empty($straynewcat))
				{
					$request =tp_query("INSERT INTO " . $tp_prefix . "variables (value1, value2, type) VALUES('" . strip_tags($straynewcat) . "', '0','category')", __FILE__, __LINE__);
					$newcategory = tpdb_insert_id($request);
					tpdb_free_result($request);
				}
				$request =tp_query("UPDATE " . $tp_prefix . "articles SET category = " . (!empty($newcategory) ? $newcategory : $category) .  " WHERE id IN (" . implode("," ,$ccats) . ")", __FILE__, __LINE__);
			}
			return $from;
		}
		// from articons...
		elseif($from == 'articons')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			// any icons sent?
			if(file_exists($_FILES['tp_article_newicon']['tmp_name']))
				TPuploadpicture('tp_article_newicon', '', '300', 'jpg,gif,png', 'tp-files/tp-articles/icons');

			if(file_exists($_FILES['tp_article_newillustration']['tmp_name']))
			{
				$name = TPuploadpicture('tp_article_newillustration', '', '500', 'jpg,gif,png', 'tp-files/tp-articles/illustrations');
				tp_createthumb('tp-files/tp-articles/illustrations/'. $name, 128, 128, 'tp-files/tp-articles/illustrations/s_'. $name);
				unlink('tp-files/tp-articles/illustrations/'. $name);
			}
			// how about deleted?
			foreach($_POST as $what => $value)
			{
				if(substr($what,0,7)=='articon')
					unlink($boarddir.'/tp-files/tp-articles/icons/'.$value);
				elseif(substr($what,0,15)=='artillustration')
					unlink($boarddir.'/tp-files/tp-articles/illustrations/'.$value);
			}
			return $from;
		}
		// adding a full menu.
		elseif($from == 'menuadd')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
				
			if(!empty($_POST['tp_menu_title']))
			{
				$mtitle = htmlentities(strip_tags($_POST['tp_menu_title']),ENT_QUOTES);
				tp_query("INSERT INTO " . $tp_prefix . "variables (value1,type) VALUES('" . $mtitle . "','menus')", __FILE__, __LINE__);
				redirectexit('action=tpadmin;sa=menubox');
			}
		}
		// adding a menu item.
		elseif($from == 'menuaddsingle')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
				
			$mid = $_POST['tp_menu_menuid'];
			$mtitle = htmlentities(strip_tags($_POST['tp_menu_title']),ENT_QUOTES);
			if($mtitle=='')
				$mtitle = $txt['tp-no_title'];
			
			$mtype=$_POST['tp_menu_type'];
			$mcat = isset($_POST['tp_menu_category']) ? 	$_POST['tp_menu_category'] : '';
			$mart = isset($_POST['tp_menu_article']) ? $_POST['tp_menu_article'] : '';
			$mlink = isset($_POST['tp_menu_link']) ? $_POST['tp_menu_link'] : ''; 
			$mhead = isset($_POST['tp_menu_head']) ? $_POST['tp_menu_head'] : ''; 
			$mnewlink = isset($_POST['tp_menu_newlink']) ? $_POST['tp_menu_newlink'] : '0';

			if($mtype=='cats')
				$mtype='cats'.$mcat;
			elseif($mtype=='arti')
				$mtype='arti'.$mart;
			elseif($mtype=='head')
				$mtype='head'.$mhead;
			elseif($mtype=='spac')
				$mtype='spac';
			else
				$mtype=$mlink;

			$msub=$_POST['tp_menu_sub'];
			tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5,subtype2) VALUES('" . $mtitle . "','" . $mnewlink . "','" . $mtype . "','menubox','" . $msub . "',-1, " . $mid . ")", __FILE__, __LINE__);
			redirectexit('action=tpadmin;sa=menubox;mid='.$mid);
		}
		// submitted ones
		elseif($from == 'submission')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			
			$ccats = array();
			// check if we have some values
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0,21) == 'tp_article_submission')
					$ccats[] = substr($what,21);
				elseif($what == 'tp_article_cat')
					$straycat = $value;
				elseif($what == 'tp_article_new')
					$straynewcat = $value;
			}	
			// update
			if(isset($straycat) && sizeof($ccats)>0)
			{
				$category = $straycat;
				if($category == 0 && !empty($straynewcat))
				{
					$request =tp_query("INSERT INTO " . $tp_prefix . "variables (value1, value2, type) VALUES('" . $straynewcat . "', '0','category')", __FILE__, __LINE__);
					$newcategory = tpdb_insert_id($request);
					tpdb_free_result($request);
				}
				$request =tp_query("UPDATE " . $tp_prefix . "articles SET approved = 1, category = " . (!empty($newcategory) ? $newcategory : $category) .  " WHERE id IN (" . implode("," ,$ccats) . ")", __FILE__, __LINE__);
				$request =tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type = 'art_not_approved' AND value5 IN (" . implode("," ,$ccats) . ")", __FILE__, __LINE__);
			}
			return $from;
		}
		// from blocks screen
		elseif($from == 'blocks')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			foreach($_POST as $what => $value)
			{
				if(substr($what,0,3) == 'pos')
				{
					$where = substr($what,3);
					if(is_numeric($where))
						$request =tp_query("UPDATE " . $tp_prefix . "blocks SET pos = '" . $value .  "' WHERE id = " . $where , __FILE__, __LINE__);
				}
				elseif(substr($what,0,6) == 'addpos')
				{
					$where = substr($what,6);
					if(is_numeric($where))
						$request =tp_query("UPDATE " . $tp_prefix . "blocks SET pos = (pos + 11) WHERE id = " . $where , __FILE__, __LINE__);
				}
				elseif(substr($what,0,6) == 'subpos')
				{
					$where = substr($what,6);
					if(is_numeric($where))
						$request =tp_query("UPDATE " . $tp_prefix . "blocks SET pos = (pos - 11) WHERE id = " . $where , __FILE__, __LINE__);
				}
				elseif(substr($what,0,4) == 'type')
				{
					$where = substr($what,4);
					$request =tp_query("UPDATE " . $tp_prefix . "blocks SET type = " . $value . " WHERE id = " . $where , __FILE__, __LINE__);
				}
				elseif(substr($what,0,5) == 'title')
				{
					$where = strip_tags(substr($what,5));
					$request =tp_query("UPDATE " . $tp_prefix . "blocks SET title = '" . htmlentities($value, ENT_QUOTES) . "' WHERE id = " . $where , __FILE__, __LINE__);
				}
				elseif(substr($what,0,9) == 'blockbody')
				{
					$where = tp_sanitize(substr($what,9));
					$request =tp_query("UPDATE " . $tp_prefix . "blocks SET body = '" . $value . "' WHERE id = " . $where , __FILE__, __LINE__);
				}
			}
			redirectexit('action=tpadmin;sa=blocks');
		}
		// from editing block
		elseif($from == 'addblock')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			$title = empty($_POST['tp_addblocktitle']) ? '-no title-' : tp_sanitize($_POST['tp_addblocktitle']);
			$panel = $_POST['tp_addblockpanel'];
			$type = $_POST['tp_addblock'];
			if(!is_numeric($type))
			{
				if(substr($type,0,3) == 'mb_')
				{
					$request =tp_query("SELECT * FROM " . $tp_prefix . "blocks WHERE id =" . substr($type,3), __FILE__, __LINE__);
					if(tpdb_num_rows($request)>0)
					{
						$cp = tpdb_fetch_assoc($request);
						tpdb_free_result($request);
					}
				}
				else
					$od = TPparseModfile(file_get_contents($boarddir . '/tp-files/tp-blockcodes/' . $type.'.blockcode') , array('code')); 
			}
			if(isset($od['code']))
			{
				$body = tp_convertphp($od['code']);
				$type = 10;
			}
			else
				$body = '';

			if(isset($cp))
				$request = tp_query("INSERT INTO " . $tp_prefix . "blocks 
			(type,frame,title,body,access,bar,pos,off,visible,var1,var2,lang,access2,editgroups)
			VALUES(" . $cp['type'] . ", '" . $cp['frame'] . "', '" . $title . "', '" . htmlentities($cp['body'],ENT_QUOTES) . "','" . $cp['access'] . "', 
			" . $panel .", 0, 1,1," . $cp['var1'] . "," . $cp['var2'] . ",'" . $cp['lang'] . "','" . $cp['access2'] . "','" . $cp['editgroups'] . "') " , __FILE__, __LINE__);
			else
				$request = tp_query("INSERT INTO " . $tp_prefix . "blocks 
			(type,frame,title,body,access,bar,pos,off,visible,var1,var2,lang,access2,editgroups)
			VALUES(" . $type . ", 'theme', '" . $title . "', '" . htmlentities($body, ENT_QUOTES) . "', '-1,0,1', " . $panel .", 0, 1,1,0,0,'','actio=allpages','') " , __FILE__, __LINE__);

			$where = tpdb_insert_id($request);	
			if(!empty($where))
				redirectexit('action=tpadmin;blockedit='.$where.';sesc='. $context['session_id']);
			else
				redirectexit('action=tpadmin;sa=blocks');
		}
		// from editing block
		elseif($from == 'blockedit')
		{
			checkSession('post');
			isAllowedTo('tp_blocks');
			
			$where = is_numeric($_POST['tpadmin_form_id']) ? $_POST['tpadmin_form_id'] : 0;
			$tpgroups = array(); $editgroups = array();
			$access = array(); $lang = array(); 
			foreach($_POST as $what => $value)
			{
				if(substr($what,0,9) == 'tp_block_')
				{
					$setting = substr($what,9);
					
					if($setting == 'body')
					{
						// PHP block?
						if($_POST['tp_block_type']==10)
							$value= tp_convertphp($value);

						$request =tp_query("UPDATE " . $tp_prefix . "blocks SET " . $setting . " = '" . $value .  "' WHERE id = " . $where , __FILE__, __LINE__);
					}
					elseif($setting == 'title')
					{
						$request =tp_query("UPDATE " . $tp_prefix . "blocks SET title = '" . tp_sanitize($value) .  "' WHERE id = " . $where , __FILE__, __LINE__);
					}
					elseif($setting == 'body_mode' || $setting == 'body_choice' || $setting == 'body_pure')
						$go='';
					else
						$request =tp_query("UPDATE " . $tp_prefix . "blocks SET " . $setting . " = '" . $value .  "' WHERE id = " . $where , __FILE__, __LINE__);
				}
				elseif(substr($what,0,8) == 'tp_group')
					$tpgroups[] = substr($what,8);
				elseif(substr($what,0,12) == 'tp_editgroup')
					$editgroups[] = substr($what,12);
				elseif(substr($what,0,10) == 'actiontype')
					$access[] = 'actio=' . $value;
				elseif(substr($what,0,9) == 'boardtype')
					$access[] = 'board=' . $value;
				elseif(substr($what,0,11) == 'articletype')
					$access[] = 'tpage=' . $value;
				elseif(substr($what,0,12) == 'categorytype')
					$access[] = 'tpcat=' . $value;
				elseif(substr($what,0,8) == 'langtype')
					$access[] = 'tlang=' . $value;
				elseif(substr($what,0,9) == 'custotype' && !empty($value))
				{
					$items = explode(",",$value);
					foreach($items as $iti => $it)
						$access[] = 'actio=' . $it;
				}
				elseif(substr($what,0,8) == 'tp_lang_')
				{
					if(substr($what,8) != '' )
						$lang[] = substr($what, 8). '|' . tp_sanitize($value);
				}
				elseif(substr($what,0,18) == 'tp_userbox_options')
				{
					if(!isset($userbox))
						$userbox = array();
					$userbox[] = $value;
				}
				elseif(substr($what,0,8) == 'tp_theme')
				{
					$theme = substr($what,8);
					if(!isset($themebox))
						$themebox = array();
					// get the path too
					if(isset($_POST['tp_path'.$theme]))
						$tpath = $_POST['tp_path'.$theme];
					else
						$tpath = '';

					$themebox[] = $theme . '|' . $value . '|' . $tpath;
				}
			}
			// construct the access++
			$request =tp_query("UPDATE " . $tp_prefix . "blocks SET 
			access2 = '" . implode(",",$access) .  "', 
			access = '" . implode(",",$tpgroups) .  "', 
			lang = '" . implode("|",$lang) .  "', 
			editgroups = '" . implode(",",$editgroups) .  "' 
			WHERE id = " . $where , __FILE__, __LINE__);
			
			if(isset($userbox))
				$request =tp_query("UPDATE " . $tp_prefix . "settings SET value = '" . implode(",",$userbox) .  "' WHERE name = 'userbox_options'" , __FILE__, __LINE__);

			if(isset($themebox))
				$request =tp_query("UPDATE " . $tp_prefix . "blocks SET body = '" . implode(",",$themebox) .  "' WHERE id = " . $where , __FILE__, __LINE__);

			// anything from PHP block?
			if(isset($_POST['blockcode_overwrite']))
			{
				// get the blockcode
				$newval = TPparseModfile(file_get_contents($boarddir . '/tp-files/tp-blockcodes/' . $_POST['tp_blockcode'].'.blockcode') , array('code')); 
				tp_query("UPDATE " . $tp_prefix . "blocks SET body='" . addslashes($newval['code']) . "' WHERE id=" . $where, __FILE__, __LINE__);
			}

			// check if uploadad picture 
			if(isset($_FILES['qup_blockbody']) && file_exists($_FILES['qup_blockbody']['tmp_name']))
			{
				$name = TPuploadpicture('qup_blockbody', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
			}
			redirectexit('action=tpadmin;blockedit='.$where.';sesc='.$context['session_id']);
		}
		// settings and frontpage
		elseif(substr($from,0,11) == 'editarticle')
		{
			checkSession('post');
			isAllowedTo('tp_articles');
			$w = array();
			
			$where = substr($from,11);

			if(empty($where))
			{
				// we need to create one first
				$request =tp_query("INSERT INTO " . $tp_prefix . "articles (date) VALUES(" . time() . ")", __FILE__, __LINE__);
				$where = tpdb_insert_id($request);
				$new=true;
				$from = 'editarticle' . $where;
			}
			
			// check if uploads are thre
			if(file_exists($_FILES['tp_article_illupload']['tmp_name']))
			{
				$name = TPuploadpicture('tp_article_illupload', '', '180', 'jpg,gif,png', 'tp-files/tp-articles/illustrations');
				tp_createthumb('tp-files/tp-articles/illustrations/'. $name, 128, 128, 'tp-files/tp-articles/illustrations/s_'. $name);
				tp_query("UPDATE " . $tp_prefix . "articles SET illustration = 's_" . $value . "' WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
			}
			// check if uploadad picture 
			if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
			{
				$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
			}
			$options = array();
			foreach($_POST as $what => $value)
			{
				if(substr($what, 0,11) == 'tp_article_' && !empty($where))
				{
					$setting = substr($what,11);
					
					if($setting=='authorid')
					{
						tp_query("UPDATE " . $tp_prefix . "articles SET authorID = " . $value . " WHERE id=" . $where . " LIMIT 1", __FILE__, __LINE__);
					}
					elseif($setting=='idtheme')
					{
						tp_query("UPDATE " . $tp_prefix . "articles SET ID_THEME = " . $value . " WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
					}
					elseif($setting=='subject')
					{
						tp_query("UPDATE " . $tp_prefix . "articles SET subject = '" . tp_sanitize($value) . "' WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
					}
					elseif($setting=='category')
					{
						// for the event, get the allowed
						$request = tp_query("SELECT value3 FROM " . $tp_prefix . "variables WHERE id=" . $value  . " LIMIT 1", __FILE__, __LINE__);
						if(tpdb_num_rows($request)>0) 
						{
							$row = tpdb_fetch_assoc($request);
							$allowed = $row['value3'];
							tpdb_free_result($request);
						}
						tp_query("UPDATE " . $tp_prefix . "articles SET category = " . $value . " WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
					}
					elseif(in_array($setting, array('body','intro')))
					{
						// in case of HTML article we need to check it
						if(isset($_POST['tp_article_body_pure']) && isset($_POST['tp_article_body_choice']))
						{
							if($_POST['tp_article_body_choice']==0)
							{
								$value=$_POST['tp_article_body_pure'];
							}
							
							// save the choice too
							$request = tp_query("SELECT id FROM " . $tp_prefix . "variables WHERE subtype2=" . $where  . "  AND type='editorchoice' LIMIT 1", __FILE__, __LINE__);
							if(tpdb_num_rows($request)>0)
							{
								$row=tpdb_fetch_assoc($request);
								tpdb_free_result($request);
								$request = tp_query("UPDATE " . $tp_prefix . "variables SET value1 = '" . $_POST['tp_article_body_choice'] . "' WHERE subtype2=" . $where  . " AND type='editorchoice'", __FILE__, __LINE__);
							}
							else
								tp_query("INSERT INTO " . $tp_prefix . "variables (value1,type,subtype2) VALUES('" . $_POST['tp_article_body_choice'] . "', 'editorchoice',".$where.")", __FILE__, __LINE__);
						}
						
						$newvalue = tp_convertphp($value);
						tp_query("UPDATE " . $tp_prefix . "articles SET " . $setting . " = '" . htmlentities($newvalue,ENT_QUOTES) . "' WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
					}
					elseif(in_array($setting, array('day','month','year','minute','hour','timestamp')))
					{
						$timestamp = mktime($_POST['tp_article_hour'],$_POST['tp_article_minute'],0,$_POST['tp_article_month'],$_POST['tp_article_day'],$_POST['tp_article_year']);
						if(!isset($savedtime))
							tp_query("UPDATE " . $tp_prefix . "articles SET date = " . $timestamp . " WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
						$savedtime=1;
					}
					elseif(substr($setting,0,8) == 'options_')
					{
						if(substr($setting,0,19) == 'options_lblockwidth' || substr($setting,0,19) == 'options_rblockwidth')
							$options[] = substr($setting,8).$value;
						else
							$options[] = substr($setting,8);
						
					}
					elseif(in_array($setting, array('body_mode','intro_mode','illupload','body_pure','body_choice')))
					{
						// ignore it
						continue;
					}
					elseif($setting=='approved')
					{
						tp_query("UPDATE " . $tp_prefix . "articles SET approved = '" . $value . "' WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
						if($value == 1)
							tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type = 'art_not_approved' AND value5='".$where."'", __FILE__, __LINE__);
					}
					else
					{
						tp_query("UPDATE " . $tp_prefix . "articles SET " . $setting . " = '" . $value . "' WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
					}
				}
			}
			// if this was a new article
			if($_POST['tp_article_approved']==1 && $_POST['tp_article_off']==0)
				tp_recordevent($timestamp, $_POST['tp_article_authorid'], 'tp-createdarticle', 'page=' . $where, 'Creation of new article.', (isset($allowed) ? $allowed : 0) , $where);
			
			tp_query("UPDATE " . $tp_prefix . "articles SET options = '" . implode(",",$options) . "' WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);

			// tag links?
			if(isset($_POST['tparticle_itemtags']))
			{
				$mytags = array();
				$itemid = $_POST['tparticle_itemtags'];
				// get title
				$request =tp_query("SELECT subject FROM " . $tp_prefix . "articles WHERE id=" . $itemid . " LIMIT 1", __FILE__, __LINE__);
				$title = tpdb_fetch_row($request);
				tpdb_free_result($request);
				// remove old ones first
				tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type ='globaltag_item' AND value3='tparticle_itemtags' AND subtype2=$itemid", __FILE__, __LINE__);
				foreach($_POST as $what => $value)
				{
					// a tag from edit items
					if(substr($what,0,18)=='tparticle_itemtags' && !empty($value))
					{
						$tag=substr($what,19);
						$itemid=$value;
						// insert new one
						$href='?page='.$itemid;
						$subject = '<div  class="taglink">' . $title[0]. '</div>';
						if(!empty($tag))
						{
							tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5,subtype,value7,value8,subtype2) 
							VALUES('" . $href. "','". $subject ."','tparticle_itemtags','globaltag_item','',0,'" . $tag. "','',''," . $itemid . ")", __FILE__, __LINE__);
							$mytags[] = $tag;
						}
					}
					elseif(substr($what,0,24)=='xyzx_tparticle_itemtags_' && !empty($value))
					{
						// create the tag as well
						$itemid=substr($what,24);
						$allowed="/[^a-zA-Z0-9_]/";
						$value=preg_replace($allowed,"",$value);
						$tag=$value;
						tp_query("REPLACE INTO " . $tp_prefix . "variables (value1,type) VALUES('$value','globaltag')", __FILE__, __LINE__);
					
						// insert new one
						$href='?page='.$itemid;
						$subject = '<span style="background: url('.$settings['tp_images_url'].'/glyph_article.png) no-repeat;" class="taglink">' . $title[0]. '</span>';
						if(!empty($tag))
						{
							tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5,subtype,value7,value8,subtype2) 
							VALUES('$href','$subject','tparticle_itemtags','globaltag_item','',0,'$tag','','',$itemid)", __FILE__, __LINE__);
							$mytags[] = $tag;
						}
					}
				}
				// save it
				if(count($mytags)>0)
					$taglist = implode(",", $mytags);
				else 
					$taglist = '';
				tp_query("UPDATE " . $tp_prefix . "articles SET global_tag = '" . $taglist . "' WHERE id='" . $where  . "' LIMIT 1", __FILE__, __LINE__);
			}
			return $from;
		}
	}
	else
		return;
}


function get_langfiles()
{
	global $context, $settings;

	// get all languages for blocktitles
	$language_dir = $settings['default_theme_dir'] . '/languages';
	$context['TPortal']['langfiles']=array();
	$dir = dir($language_dir);
	while ($entry = $dir->read())
		if (substr($entry,0,6)=='index.' && substr($entry,(strlen($entry)-4),4)=='.php' && strlen($entry)>9)
	$context['TPortal']['langfiles'][] = substr(substr($entry,6),0,-4);
	$dir->close();
}

function get_catlayouts()
{
	global $context, $txt;

	// setup the layoutboxes
	$context['TPortal']['admin_layoutboxes']=array(
		array(
			'value' => '1',
			'label' => $txt['tp-catlayout1'],
			),
		array(
			'value' => '2',
			'label' => $txt['tp-catlayout2'],
			),
		array(
			'value' => '3',
			'label' => $txt['tp-catlayout3'],
			),
		array(
			'value' => '4',
			'label' => $txt['tp-catlayout4'],
			),
		array(
			'value' => '5',
			'label' => $txt['tp-catlayout5'],
			),
		array(
			'value' => '6',
			'label' => $txt['tp-catlayout6'],
			),
		array(
			'value' => '7',
			'label' => $txt['tp-catlayout7'],
			),
		array(
			'value' => '8',
			'label' => $txt['tp-catlayout8'],
			),
		);
}

function get_boards()
{
	global $context, $db_prefix, $user_info;

	$context['TPortal']['boards'] = array();
	$request = tp_query("SELECT b.ID_BOARD as id, b.name FROM " . $db_prefix . "boards as b WHERE 1", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
			$context['TPortal']['boards'][] = $row; 
		
		tpdb_free_result($request);
	}
}

function get_articles()
{

	global $context,$db_prefix,$user_info;
	
	// prefix of the TP tables
	$tp_prefix = $db_prefix.'tp_';

	$context['TPortal']['edit_articles'] = array();
	
	$request = tp_query("SELECT id,subject, shortname FROM " . $tp_prefix . "articles WHERE approved=1 AND off=0", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
			$context['TPortal']['edit_articles'][] = $row; 
		
		tpdb_free_result($request);
	}
}

function get_catnames()
{

	global $context,$db_prefix,$user_info;
	
	// prefix of the TP tables
	$tp_prefix = $db_prefix.'tp_';

	$context['TPortal']['catnames'] = array();
	
	$request = tp_query("SELECT id,value1 FROM " . $tp_prefix . "variables WHERE type='category'", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
			$context['TPortal']['catnames'][$row['id']] = $row['value1']; 
		
		tpdb_free_result($request);
	}
}
?>
