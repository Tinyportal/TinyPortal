<?php
/**
 * @package TinyPortal
 * @version 2.1.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2020 - The TinyPortal Team
 *
 */
use \TinyPortal\Article as TPArticle;
use \TinyPortal\Block as TPBlock;
use \TinyPortal\Util as TPUtil;

if (!defined('SMF')) {
        die('Hacking attempt...');
}

function TPBlockInit() {{{
	global $context, $txt;

	if(loadLanguage('TPmodules') == false) {
		loadLanguage('TPmodules', 'english');
    }

	if(loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
    }

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// call the editor setup
	require_once(SOURCEDIR. '/TPcommon.php');

	// clear the linktree first
	TPstrip_linktree();

}}}

function TPBlockActions(&$subActions) {{{

   $subActions = array_merge(
        array (
            'showblock'      => array('TPBlock.php', 'showBlock',   array()),
        ),
        $subActions
    );

}}}

function getBlocks() {{{

	global $context, $scripturl, $user_info, $smcFunc, $modSettings;

    $tpBlock    = TPBlock::getInstance();

	$now = time();
	// setup the containers
	$blocks = $tpBlock->getBlockType(); 


	$context['TPortal']['hide_frontbar_forum'] = 0;

	$fetch_articles = array();
	$fetch_article_titles = array();

    $count  = array_flip($tpBlock->getBlockPanel());
    foreach($count as $k => $v) {
        $count[$k] = 0;
    }

	$panels             = $tpBlock->getBlockBar(); 
    $availableBlocks    = $tpBlock->getBlockPermissions();
	if (is_array($availableBlocks) & count($availableBlocks)) {
        foreach($availableBlocks as $row) { 
            // decode the block settings
            $set = json_decode($row['settings'], true);
			// some tests to minimize sql calls
			if($row['type'] == TP_BLOCK_THEMEBOX) {
				$test_themebox = true;
            }
			elseif($row['type'] == TP_BLOCK_ARTICLEBOX) {
				$test_articlebox = true;
				if(is_numeric($row['body'])) {
					$fetch_articles[] = $row['body'];
                }
			}
			elseif($row['type'] == TP_BLOCK_CATMENU || $row['type'] == TP_BLOCK_SITEMAP  ) {
				$test_menubox = true;
			}
            elseif($row['type'] == TP_BLOCK_CATEGORYBOX) {
				$test_catbox = true;
				if(is_numeric($row['body'])) {
					$fetch_article_titles[] = $row['body'];
                }
			}
            elseif($row['type'] == TP_BLOCK_SHOUTBOX) {
                call_integration_hook('integrate_tp_shoutbox', array(&$row));
            }

			$can_edit = !empty($row['editgroups']) ? get_perm($row['editgroups'],'') : false;
			$can_manage = allowedTo('tp_blocks');
			if($can_manage) {
				$can_edit = false;
            }
			$blocks[$panels[$row['bar']]][$count[$panels[$row['bar']]]] = array(
				'frame'     => $row['frame'],
				'title'     => strip_tags($row['title'], '<center>'),
                'type'      => $tpBlock->getBlockType($row['type']),
				'body'      => $row['body'],
				'visible'   => $row['visible'],
                'settings'  => $row['settings'],
				'var1'      => $set['var1'],
				'var2'      => $set['var2'],
				'var3'      => $set['var3'],
				'var4'      => $set['var4'],
				'var5'      => $set['var5'],
				'id'        => $row['id'],
				'lang'      => $row['lang'],
				'access2'   => $row['access2'],
				'can_edit'  => $can_edit,
				'can_manage' => $can_manage,
			);
			$count[$panels[$row['bar']]]++;
		}
	}

    // if a block displays an article
    if(isset($test_articlebox)) {
		$context['TPortal']['blockarticles'] = array();
        $tpArticle  = new TPArticle();
        $articles   = $tpArticle->getArticle($fetch_articles);
        if(is_array($articles)) {
            foreach($articles as $article) {
				// allowed and all is well, go on with it.
				$context['TPortal']['blockarticles'][$article['id']] = $article;
				// setup the avatar code
				if ($modSettings['avatar_action_too_large'] == 'option_html_resize' || $modSettings['avatar_action_too_large'] == 'option_js_resize') {
					$avatar_width = !empty($modSettings['avatar_max_width_external']) ? ' width="' . $modSettings['avatar_max_width_external'] . '"' : '';
					$avatar_height = !empty($modSettings['avatar_max_height_external']) ? ' height="' . $modSettings['avatar_max_height_external'] . '"' : '';
				}
				else {
					$avatar_width = '';
					$avatar_height = '';
				}
                $context['TPortal']['blockarticles'][$article['id']]['avatar'] = set_avatar_data( array(      
                            'avatar' => $article['avatar'],
                            'email' => $article['email_address'],
                            'filename' => !empty($article['filename']) ? $article['filename'] : '',
                            'id_attach' => $article['id_attach'],
                            'attachment_type' => $article['attachment_type'],
                        )
                )['image'];
				// sort out the options
				$context['TPortal']['blockarticles'][$article['id']]['visual_options'] = array();
				// since these are inside blocks, some stuff has to be left out
				$context['TPortal']['blockarticles'][$article['id']]['frame'] = 'none';
			}
		}
	}

    // any cat listings from blocks?
    if(isset($test_catbox)) {
        $tpArticle  = new TPArticle();
        $categories = $tpArticle->getArticlesInCategory($fetch_article_titles, false, true);
        if (!isset($context['TPortal']['blockarticle_titles'])) {
			$context['TPortal']['blockarticle_titles'] = array();
        }
        if(is_array($categories)) {
            foreach($categories as $row) {
                if(empty($row['author'])) {
                    global $memberContext;
                    loadMemberData($row['author_id']);
                    loadMemberContext($row['author_id']);
                    $row['real_name'] = $memberContext[$row['author_id']]['username'];
                }
                else {
                    $row['real_name'] = $row['author'];
                }
				$context['TPortal']['blockarticle_titles'][$row['category']][$row['date'].'_'.$row['id']] = array(
					'id' => $row['id'],
					'subject' => $row['subject'],
					'shortname' => $row['shortname']!='' ?$row['shortname'] : $row['id'] ,
					'category' => $row['category'],
					'poster' => '<a href="'.$scripturl.'?action=profile;u='.$row['author_id'].'">'.$row['real_name'].'</a>',
				);
			}
		}
    }

	// get menubox items
	if(isset($test_menubox)) {
        TPortal_menubox();
	}

	// for tpadmin
	$context['TPortal']['adminleftpanel']   = $context['TPortal']['leftpanel'];
	$context['TPortal']['adminrightpanel']  = $context['TPortal']['rightpanel'];
	$context['TPortal']['admincenterpanel'] = $context['TPortal']['centerpanel'];
	$context['TPortal']['adminbottompanel'] = $context['TPortal']['bottompanel'];
	$context['TPortal']['admintoppanel']    = $context['TPortal']['toppanel'];
	$context['TPortal']['adminlowerpanel']  = $context['TPortal']['lowerpanel'];

	// if admin specifies no blocks, no blocks are shown! likewise, if in admin or tpadmin screen, turn off blocks
	if (in_array($context['TPortal']['action'], array('moderate', 'theme', 'tpadmin', 'admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers'))) {
	    $in_admin = true;
    }

	if($context['TPortal']['action'] == 'tportal' && isset($_GET['dl']) && substr($_GET['dl'], 0, 5) == 'admin') {
		$in_admin = true;
		$context['current_action'] = 'admin';
	}

	if(($context['user']['is_admin'] && isset($_GET['noblocks'])) || ($context['TPortal']['hidebars_admin_only']=='1' && isset($in_admin))) {
		tp_hidebars();
    }

	// check the panels
	foreach($panels as $p => $panel) {
		// any blocks at all?
		if($count[$panel] < 1) {
			$context['TPortal'][$panel.'panel'] = 0;
        }
		// check the hide setting
		if(!isset($context['TPortal']['not_forum']) && $context['TPortal']['hide_' . $panel . 'bar_forum']==1) {
			tp_hidebars($panel);
        }
	}
	$context['TPortal']['blocks'] = $blocks;

}}}


// Admin Actions
function TPBlockAdminActions(&$subActions) {{{

   $subActions = array_merge(
        array (
            'editblock'      => array('TPBlock.php', 'editBlock',   array()),
            'deleteblock'    => array('TPBlock.php', 'deleteBlock', array()),
            'saveblock'      => array('TPBlock.php', 'saveBlock',   array()),
        ),
        $subActions
    );

}}}

function adminBlocks() {{{
	global $context, $smcFunc, $txt, $settings, $scripturl;

	isAllowedTo('tp_blocks');
    
    $tpBlock    = TPBlock::getInstance();

	if(($context['TPortal']['subaction']=='blocks') && !isset($_GET['overview'])) {
		TPadd_linktree($scripturl.'?action=tpadmin;sa=blocks', $txt['tp-blocks']);
	}
	
	if(isset($_GET['addblock'])) {
		TPadd_linktree($scripturl.'?action=tpadmin;sa=addblock', $txt['tp-addblock']);
		// collect all available PHP block snippets
		$context['TPortal']['blockcodes']   = TPcollectSnippets();
		$context['TPortal']['copyblocks']   = $tpBlock->getBlocks();
	}

	// Move the block up or down in the panel list of blocks
	if(isset($_GET['addpos']) || isset($_GET['subpos'])) {
		checksession('get');
	    if(isset($_GET['addpos'])) {
		    $id         = is_numeric($_GET['addpos']) ? $_GET['addpos'] : 0;
            $current    = $tpBlock->getBlockData(array( 'pos', 'bar'), array( 'id' => $id) );
            $new        = $current[0]['pos'] + 1;
            $existing   = $tpBlock->getBlockData('id', array( 'bar' => $current[0]['bar'], 'pos' => $new ) );
            if(is_array($existing)) {
                $tpBlock->updateBlock($existing[0]['id'], array( 'pos' => $current[0]['pos']));
            }
        } 
        else {
		    $id         = is_numeric($_GET['subpos']) ? $_GET['subpos'] : 0;
            $current    = $tpBlock->getBlockData(array( 'pos', 'bar'), array( 'id' => $id) );
            $new        = $current[0]['pos'] - 1;
            $existing   = $tpBlock->getBlockData('id', array( 'bar' => $current[0]['bar'], 'pos' => $new ) );
            if(is_array($existing)) {
                $tpBlock->updateBlock($existing[0]['id'], array( 'pos' => $current[0]['pos']));
            }
        }
        $tpBlock->updateBlock($id, array( 'pos' => $new));
		redirectexit('action=tpadmin;sa=blocks');
	}

	// change the on/off
	if(isset($_GET['blockon'])) {
		checksession('get');
		$id         = is_numeric($_GET['blockon']) ? $_GET['blockon'] : 0;
        $current    = $tpBlock->getBlockData(array( 'off' ), array( 'id' => $id) );
        if(is_array($current)) {
            if($current[0]['off'] == 1) {
                $tpBlock->updateBlock($id, array( 'off' => '0' ));
            }
            else {
                $tpBlock->updateBlock($id, array( 'off' => '1' ));
            }
        }
        redirectexit('action=tpadmin;sa=blocks');
	}

	// remove it?
	if(isset($_GET['blockdelete'])) {
		checksession('get');
		$id         = is_numeric($_GET['blockdelete']) ? $_GET['blockdelete'] : 0;
        $tpBlock->deleteBlock($id);
		redirectexit('action=tpadmin;sa=blocks');
	}
   
    foreach( array ( 'blockright', 'blockleft', 'blockcenter', 'blockfront', 'blockbottom', 'blocktop', 'blocklower') as $block_location ) {
        if(array_key_exists($block_location, $_GET)) {
            checksession('get');
            $id     = is_numeric($_GET[$block_location]) ? $_GET[$block_location] : 0;
            $loc    = $tpBlock->getBlockBarId(str_replace('block', '', $block_location));
            $tpBlock->updateBlock($id, array( 'bar' => $loc ));
            redirectexit('action=tpadmin;sa=blocks');
        }
	}

	// are we on overview screen?
	if(isset($_GET['overview'])) {
		TPadd_linktree($scripturl.'?action=tpadmin;sa=blocks;overview', $txt['tp-blockoverview']);
		
		// fetch all blocks member group permissions
        $data   = $tpBlock->getBlockData(array('id', 'title', 'bar', 'access', 'type'), array( 'off' => 0 ) );
		if(is_array($data)) {
			$context['TPortal']['blockoverview'] = array();
            foreach($data as $row) {
				$context['TPortal']['blockoverview'][] = array(
					'id' => $row['id'],
					'title' => $row['title'],
					'bar' => $row['bar'],
					'type' => $row['type'],
					'access' => explode(',', $row['access']),
				);
			}
		}
		get_grps(true,true);
	}

	// or maybe adding it?
	if(isset($_GET['addblock'])) {
		get_articles();
		// check which side its mean to be on
		$context['TPortal']['blockside'] = $_GET['addblock'];
	}

	if($context['TPortal']['subaction']=='panels') {
		TPadd_linktree($scripturl.'?action=tpadmin;sa=panels', $txt['tp-panels']);
    }
	
	else {
		foreach($tpBlock->getBlockPanel() as $p => $pan) {
			if(isset($_GET[$pan])) {
				$context['TPortal']['panelside'] = $pan;
            }
		}
        $bars   = $tpBlock->getBlockBar();
        $blocks = $tpBlock->getBlocks();
		if (is_countable($blocks) && count($blocks) > 0) {
            $bar    = array_column($blocks, 'bar');
            $pos    = array_column($blocks, 'pos');
            if(array_multisort($bar, SORT_ASC, $pos, SORT_ASC, $blocks)) {
                foreach($blocks as $row) {
                    // decode the block settings
                    $set = json_decode($row['settings'], true);
                    $context['TPortal']['admin_'.$bars[$row['bar']].'block']['blocks'][] = array(
                        'frame' => $row['frame'],
                        'title' => $row['title'],
                        'type' => $tpBlock->getBlockType($row['type']),
                        'body' => $row['body'],
                        'id' => $row['id'],
                        'access' => $row['access'],
                        'pos' => $row['pos'],
                        'off' => $row['off'],
                        'visible' => $row['visible'],
                        'var1' => $set['var1'],
                        'var2' => $set['var2'],
                        'lang' => $row['lang'],
                        'access2' => $row['access2'],
                        'loose' => $row['access2'] != '' ? true : false,
                        'editgroups' => $row['editgroups']
                    );
                }
            }
		}
	}
	get_articles();

	$context['html_headers'] .= '
	<script type="text/javascript" src="'. $settings['default_theme_url']. '/scripts/editor.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
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
			Ajax.open("POST", "?action=tpadmin;blockon=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'");
			Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencode");
			var source = target.src;
			target.src = "' . $settings['tp_images_url'] . '/ajax.gif"
			Ajax.onreadystatechange = function()
			{
				if(Ajax.readyState == 4)
				{
					target.src = source == "' . $settings['tp_images_url'] . '/TPactive1.png" ? "' . $settings['tp_images_url'] . '/TPactive2.png" : "' . $settings['tp_images_url'] . '/TPactive1.png";
				}
			}
			var params = "?action=tpadmin;blockon=" + id + ";' . $context['session_var'] . '=' . $context['session_id'].'";
			Ajax.send(params);
		}
	// ]]></script>';

}}}

function editBlock( $block_id = 0 ) {{{

	global $settings, $context, $scripturl, $txt, $boarddir, $smcFunc;

    $tpBlock = TPBlock::getInstance();

    if(empty($block_id)) {
	    $block_id  = TPUtil::filter('id', 'get', 'int');
    }

    if(!is_numeric($block_id)) {
        fatal_error($txt['tp-notablock'], false);
    }

    if(loadLanguage('TPortalAdmin') == false) {
        loadLanguage('TPortalAdmin', 'english');
    }

	checksession('get');

    require_once(SOURCEDIR.'/TPortalAdmin.php');

	TPadd_linktree($scripturl.'?action=tpadmin;sa=blocks', $txt['tp-blocks']);
	TPadd_linktree($scripturl.'?action=tpadmin&sa=editblock&id='.$block_id . ';'.$context['session_var'].'='.$context['session_id'], $txt['tp-editblock']);

    $row = $tpBlock->getBlock($block_id);
    if(is_array($row)) {
		$acc2 = explode(',', $row['access2']);
		$context['TPortal']['blockedit'] = $row;
		$context['TPortal']['blockedit']['var1']    = json_decode($row['settings'],true)['var1'];
		$context['TPortal']['blockedit']['var2']    = json_decode($row['settings'],true)['var2'];
		$context['TPortal']['blockedit']['var3']    = json_decode($row['settings'],true)['var3'];
		$context['TPortal']['blockedit']['var4']    = json_decode($row['settings'],true)['var4'];
		$context['TPortal']['blockedit']['var5']    = json_decode($row['settings'],true)['var5'];
		$context['TPortal']['blockedit']['access22'] = $context['TPortal']['blockedit']['access2'];
		$context['TPortal']['blockedit']['body'] = $row['body'];
		unset($context['TPortal']['blockedit']['access2']);
		$context['TPortal']['blockedit']['access2'] = array(
			'action' => array(),
			'board' => array(),
			'page' => array(),
			'cat' => array(),
			'lang' => array(),
			'tpmod' => array(),
			'dlcat' => array(),
			'custo' => array(),
		);

		foreach($acc2 as $ss => $svalue) {
			if(substr($svalue, 0, 6)== 'actio=')
				$context['TPortal']['blockedit']['access2']['action'][]=substr($svalue,6);
			elseif(substr($svalue, 0,6) == 'board=')
				$context['TPortal']['blockedit']['access2']['board'][] = substr($svalue,6);
			elseif(substr($svalue, 0, 6) == 'tpage=')
				$context['TPortal']['blockedit']['access2']['page'][]  = substr($svalue,6);
			elseif(substr($svalue, 0, 6) == 'tpcat=')
				$context['TPortal']['blockedit']['access2']['cat'][] = substr($svalue,6);
			elseif(substr($svalue, 0, 6) == 'tpmod=')
				$context['TPortal']['blockedit']['access2']['tpmod'][] = substr($svalue,6);
			elseif(substr($svalue, 0, 6) == 'tlang=')
				$context['TPortal']['blockedit']['access2']['lang'][] = substr($svalue,6);
			elseif(substr($svalue, 0, 6) == 'dlcat=')
				$context['TPortal']['blockedit']['access2']['dlcat'][] = substr($svalue,6);
			elseif(substr($svalue, 0, 6) == 'custo=')
				$context['TPortal']['blockedit']['access2']['custo'] = substr($svalue,6);
		}

		// Add in BBC editor before we call in template so the headers are there
		if($context['TPortal']['blockedit']['type'] == '5') {
			$context['TPortal']['editor_id'] = 'tp_block_body';
			TP_prebbcbox($context['TPortal']['editor_id'], strip_tags($context['TPortal']['blockedit']['body']));
		}
        elseif($row['type'] == 8) {
            call_integration_hook('integrate_tp_shoutbox', array(&$row));
        }        
        elseif($row['type'] == 20) {
            call_integration_hook('integrate_tp_blocks', array(&$row));
        }

		if($context['TPortal']['blockedit']['lang'] != '') {
			$context['TPortal']['blockedit']['langfiles'] = array();
			$lang = explode('|', $context['TPortal']['blockedit']['lang']);
			$num = count($lang);
			for($i = 0; $i < $num; $i = $i + 2)
			{
				$context['TPortal']['blockedit']['langfiles'][$lang[$i]] = $lang[$i+1];
			}
		}
		// collect all available PHP block snippets
		$context['TPortal']['blockcodes'] = TPcollectSnippets();
        get_catnames();
		get_grps();
		get_langfiles();
		get_boards();
		get_articles();
		tp_getDLcats();
		$context['TPortal']['edit_categories'] = array();
		$request = $smcFunc['db_query']('', '
			SELECT id, value1 as name
			FROM {db_prefix}tp_variables
			WHERE type = {string:type}
			ORDER BY value1',
			array(
				'type' => 'category'
			)
		);

		if($smcFunc['db_num_rows']($request) > 0) {
			while($row = $smcFunc['db_fetch_assoc']($request))
				$context['TPortal']['article_categories'][] = $row;
			$smcFunc['db_free_result']($request);
		}
		// get all themes for selection
		$context['TPthemes'] = array();
		$request = $smcFunc['db_query']('', '
			SELECT th.value AS name, th.id_theme as id_theme, tb.value AS path
			FROM {db_prefix}themes AS th
			LEFT JOIN {db_prefix}themes AS tb ON th.id_theme = tb.id_theme
			WHERE th.variable = {string:thvar}
			AND tb.variable = {string:tbvar}
			AND th.id_member = {int:id_member}
			ORDER BY th.value ASC',
			array(
				'thvar' => 'name', 'tbvar' => 'images_url', 'id_member' => 0,
			)
		);

		if($smcFunc['db_num_rows']($request) > 0) {
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$context['TPthemes'][] = array(
					'id' => $row['id_theme'],
					'path' => $row['path'],
					'name' => $row['name']
				);
			}
			$smcFunc['db_free_result']($request);
		}
		$request = $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_variables
			WHERE type = {string:type}
			ORDER BY value1 ASC',
			array(
				'type' => 'menus'
			)
		);
		$context['TPortal']['menus'] = array();
		$context['TPortal']['menus'][0] = array(
			'id' => 0,
			'name' => 'Internal',
			'var1' => '',
			'var2' => ''
		);
		if($smcFunc['db_num_rows']($request) > 0) {
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$context['TPortal']['menus'][$row['id']] = array(
					'id' => $row['id'],
					'name' => $row['value1'],
					'var1' => $row['value2'],
					'var2' => $row['value3']
				);
			}
		}
	}
	// if not throw an error
	else {
		fatal_error($txt['tp-blockfailure'], false);
	}

	$context['sub_template'] = 'editblock';


    loadtemplate('TPBlockLayout');

}}}

function saveBlock( $block_id = 0 ) {{{
	global $settings, $context, $scripturl, $txt, $boarddir, $smcFunc;

    if(empty($block_id)) {
	    $block_id  = TPUtil::filter('id', 'get', 'int');
    }

    // save a block?
    if(!is_numeric($block_id)) {
        fatal_error($txt['tp-notablock'], false);
    }
    $request =  $smcFunc['db_query']('', '
        SELECT editgroups FROM {db_prefix}tp_blocks
        WHERE id = {int:blockid} LIMIT 1',
        array('blockid' => $block_id)
    );

    if($smcFunc['db_num_rows']($request) > 0) {
        $row = $smcFunc['db_fetch_assoc']($request);
        // check permission
        if(allowedTo('tp_blocks') || get_perm($row['editgroups'])) {
            $ok = true;
        }
        else {
            fatal_error($txt['tp-blocknotallowed'], false);
        }
        $smcFunc['db_free_result']($request);

        // loop through the values and save them
        foreach ($_POST as $what => $value) {
            if(substr($what, 0, 10) == 'blocktitle') {
                // make sure special charachters can't be done
                $value = strip_tags($value);
                $value = preg_replace('~&#\d+$~', '', $value);
                $val = substr($what,10);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET title = {string:title}
                        WHERE id = {int:blockid}',
                        array('title' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 9) == 'blockbody' && substr($what, -4) != 'mode') {
                // If we came from WYSIWYG then turn it back into BBC regardless.
                if (!empty($_REQUEST[$what.'_mode']) && isset($_REQUEST[$what])) {
                    require_once(SOURCEDIR . '/Subs-Editor.php');
                    $_REQUEST[$what] = html_to_bbc($_REQUEST[$what]);
                    // We need to unhtml it now as it gets done shortly.
                    $_REQUEST[$what] = un_htmlspecialchars($_REQUEST[$what]);
                    // We need this for everything else.
                    $value = $_POST[$what] = $_REQUEST[$what];
                }

                $val = (int) substr($what, 9);

                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET body = {string:body}
                        WHERE id = {int:blockid}',
                        array('body' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 10) == 'blockframe') {
                $val = substr($what, 10);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET frame = {string:frame}
                        WHERE id = {int:blockid}',
                        array('frame' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 12) == 'blockvisible') {
                $val = substr($what, 12);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET visible = {string:vis}
                        WHERE id = {int:blockid}',
                        array('vis' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 9) == 'blockvar1') {
                $val=substr($what, 9);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET var1 = {string:var1}
                        WHERE id = {int:blockid}',
                        array('var1' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 9) == 'blockvar2') {
                $val = substr($what, 9);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET var2 = {string:var2}
                        WHERE id = {int:blockid}',
                        array('var2' => $value, 'blockid' => $val)
                        );
            }
        }
        redirectexit('action=tportal;sa=editblock'.$whatID);
    }
    else {
        fatal_error($txt['tp-notablock'], false);
    }

}}}

?>
