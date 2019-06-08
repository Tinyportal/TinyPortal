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

if (!defined('SMF')) {
        die('Hacking attempt...');
}

function TPBlock_init() {{{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

	if(loadLanguage('TPmodules') == false) {
		loadLanguage('TPmodules', 'english');
    }

	if(loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
    }

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// call the editor setup
	require_once($sourcedir. '/TPcommon.php');

	// clear the linktree first
	TPstrip_linktree();

}}}

function editBlock( $blockID ) {{{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

    if(!is_numeric($blockID)) {
        fatal_error($txt['tp-notablock'], false);
    }
    // get one block
    $context['TPortal']['subaction'] = 'editblock';
    $context['TPortal']['blockedit'] = array();
    $request =  $smcFunc['db_query']('', '
        SELECT * FROM {db_prefix}tp_blocks
        WHERE id = {int:blockid} LIMIT 1',
        array('blockid' => $blockID)
    );
    if($smcFunc['db_num_rows']($request) > 0) {
        $row = $smcFunc['db_fetch_assoc']($request);

        $can_edit = !empty($row['editgroups']) ? get_perm($row['editgroups'],'') : false;

        // check permission
        if(allowedTo('tp_blocks') || $can_edit) {
            $ok=true;
        }
        else {
            fatal_error($txt['tp-blocknotallowed'], false);
        }

        $context['TPortal']['editblock'] = array();
        $context['TPortal']['blockedit']['id'] = $row['id'];
        $context['TPortal']['blockedit']['title'] = $row['title'];
        $context['TPortal']['blockedit']['body'] = $row['body'];
        $context['TPortal']['blockedit']['frame'] = $row['frame'];
        $context['TPortal']['blockedit']['type'] = $row['type'];
        $context['TPortal']['blockedit']['var1'] = $row['var1'];
        $context['TPortal']['blockedit']['var2'] = $row['var2'];
        $context['TPortal']['blockedit']['visible'] = $row['visible'];
        $context['TPortal']['blockedit']['editgroups'] = $row['editgroups'];
        $smcFunc['db_free_result']($request);
    }
    else {
        fatal_error($txt['tp-notablock'], false);
    }

    // Add in BBC editor before we call in template so the headers are there
    if($context['TPortal']['blockedit']['type'] == '5') {
        $context['TPortal']['editor_id'] = 'blockbody' . $context['TPortal']['blockedit']['id'];
        TP_prebbcbox($context['TPortal']['editor_id'], strip_tags($context['TPortal']['blockedit']['body']));
    }

    if(loadLanguage('TPortalAdmin') == false) {
        loadLanguage('TPortalAdmin', 'english');
    }
    loadtemplate('TPmodules');

}}}

function saveBlock( $blockID ) {{{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

    // save a block?
    if(!is_numeric($blockID)) {
        fatal_error($txt['tp-notablock'], false);
    }
    $request =  $smcFunc['db_query']('', '
        SELECT editgroups FROM {db_prefix}tp_blocks
        WHERE id = {int:blockid} LIMIT 1',
        array('blockid' => $blockID)
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
                    require_once($sourcedir . '/Subs-Editor.php');
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

function TPBlockActions(&$subActions) {{{

   $subActions = array_merge(
        array (
            'showblock'      => array('TPBlock.php', 'showBlock',   array()),
            'editblock'      => array('TPBlock.php', 'editBlock',   array()),
            'deleteblock'    => array('TPBlock.php', 'deleteBlock', array()),
            'saveblock'      => array('TPBlock.php', 'saveBlock',   array()),
        ),
        $subActions
    );


}}}

// do the blocks
function getBlocks() {{{

	global $context, $scripturl, $user_info, $smcFunc, $modSettings, $db_type;

	$now = time();
	// setup the containers
	$blocks = array('left' => array(), 'right' => array(), 'center' => array(), 'front' => array(), 'bottom' => array(), 'top' => array() , 'lower' => array());
	$blocktype = array('no', 'userbox', 'newsbox', 'statsbox', 'searchbox', 'html',
		'onlinebox', 'themebox', 'oldshoutbox', 'catmenu', 'phpbox', 'scriptbox', 'recentbox',
		'ssi', 'module', 'rss', 'sitemap', 'oldadmin', 'articlebox', 'categorybox', 'tpmodulebox');

	// construct the spot we are in
	$sqlarray = array();
	// any action?
	if(!empty($_GET['action'])) {
		$sqlarray[] = 'actio=' . preg_replace('/[^A-Za-z0-9]/', '', $_GET['action']);
		if(in_array($_GET['action'], array('forum', 'collapse', 'post', 'calendar', 'search', 'login', 'logout', 'register', 'unread', 'unreadreplies', 'recent', 'stats', 'pm', 'profile', 'post2', 'search2', 'login2'))) {
			$sqlarray[] = 'actio=forumall';
        }
	}

	if(!empty($_GET['board'])) {
		if(!isset($_GET['action'])) {
			$sqlarray[] = 'board=-1';
        }
		$sqlarray[] = 'board=' . $_GET['board'];
		$sqlarray[] = 'actio=forumall';
	}

	if(!empty($_GET['topic'])) {
		if(!isset($_GET['action'])) {
			$sqlarray[] = 'board=-1';
        }
		$sqlarray[] = 'topic=' . $_GET['topic'];
		$sqlarray[] = 'actio=forumall';
	}

	if(!empty($_GET['dl']) && substr($_GET['dl'], 0, 3) == 'cat') {
			$down = true;
	}
    $action = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

    if(!empty($action) && array_key_exists('action', $action)) {
        $sqlarray[] = 'actio='.$action['action'];
    }

    
	if(!empty($context['TPortal']['uselangoption']) {
		$sqlarray[] = 'tlang=' . $user_info['language'],
    }

	// frontpage
	if(!isset($_GET['action']) && !isset($_GET['board']) && !isset($_GET['topic']) && !isset($_GET['page']) && !isset($_GET['cat'])) {
		$front = true;
    }

	$sqlarray[] = 'actio=allpages';

	// set the location access
    $access2 = '';
    if($db_type == 'mysql') {
        $access2 = 'FIND_IN_SET(\'' . implode('\', access2) OR FIND_IN_SET(\'', $sqlarray) . '\', access2)';
    }
    else {
        foreach($sqlarray as $k => $v) {
            $access2 .= " '$v' = ANY (string_to_array(access2, ',' ) ) OR ";
        }
    }
    $access2 = rtrim($access2,' OR ');

	// set the membergroup access
    $access = '';
    if($db_type == 'mysql') {
        $access = '(FIND_IN_SET(' . implode(', access) OR FIND_IN_SET(', $user_info['groups']) . ', access))';
    }
    else {
        foreach($user_info['groups'] as $k => $v) {
            $access .= " '$v' = ANY (string_to_array(access, ',' ) ) OR ";
        }
    }
    $access = rtrim($access,' OR ');

	if(allowedTo('tp_blocks') && (!empty($context['TPortal']['admin_showblocks']) || !isset($context['TPortal']['admin_showblocks']))) {
		$access = '1=1';
    }

	// get the blocks
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_blocks
		WHERE off = 0
		AND bar != {int:bar}
		AND (' . (!empty($_GET['page']) ? '{string:page} IN ( access2 ) OR ' : '') . '
		' . (!empty($_GET['cat']) ? '{string:cat} IN ( access2 ) OR ' : '') . '
		' . (!empty($_GET['shout']) ? '{string:shout} IN ( access2 ) OR ' : '') . '
		' . (!empty($front) ? '{string:front} IN ( access2 ) OR ' : '') . '
		' . (!empty($down) ? '{string:down} IN ( access2 ) OR ' : '') . '
		' . $access2 . ')
		AND ' . $access . '
		ORDER BY bar, pos, id ASC',
		array(
			'bar' => 4,
			'page' => !empty($_GET['page']) ? !empty($context['shortID']) ? 'tpage=' . $context['shortID'] : 'tpage=' . $_GET['page'] : '',
			'cat' => !empty($_GET['cat']) ? !empty($context['catshortID']) ? 'tpcat=' . $context['catshortID'] : 'tpcat=' . $_GET['cat'] : '',
			'front' => 'actio=frontpage',
			'down' => (!empty($down) ? 'dlcat=' . substr($_GET['dl'], 3) : ''),
			'shout' => 'tpmod=shout',
		)
	);
	$context['TPortal']['hide_frontbar_forum'] = 0;

	$count = array('left' => 0, 'right' => 0, 'center' => 0, 'front' => 0, 'bottom' => 0, 'top' => 0, 'lower' => 0);

	$fetch_articles = array();
	$fetch_article_titles = array();

	$panels = array(1 => 'left', 2 => 'right', 3 => 'center', 4 => 'front', 5 => 'bottom', 6 => 'top', 7 => 'lower');
	if ($smcFunc['db_num_rows']($request) > 0) {
		while($row = $smcFunc['db_fetch_assoc']($request)) {
            // decode the block settings
            $set = json_decode($row['settings'], true);
			// some tests to minimize sql calls
			if($row['type'] == 7) {
				$test_themebox = true;
            }
			elseif($row['type'] == 18) {
				$test_articlebox = true;
				if(is_numeric($row['body'])) {
					$fetch_articles[] = $row['body'];
                }
			}
			elseif($row['type'] == 9 || $row['type'] == 16  ) {
				$test_menubox = true;
			}
            elseif($row['type'] == 19) {
				$test_catbox = true;
				if(is_numeric($row['body'])) {
					$fetch_article_titles[] = $row['body'];
                }
			}
            elseif($row['type'] == 20) {
                call_integration_hook('integrate_tp_blocks', array(&$row));
            }
			$can_edit = !empty($row['editgroups']) ? get_perm($row['editgroups'],'') : false;
			$can_manage = allowedTo('tp_blocks');
			if($can_manage) {
				$can_edit = false;
            }
			$blocks[$panels[$row['bar']]][$count[$panels[$row['bar']]]] = array(
				'frame' => $row['frame'],
				'title' => strip_tags($row['title'], '<center>'),
				'type' => isset($blocktype[$row['type']]) ? $blocktype[$row['type']] : $row['type'],
				'body' => $row['body'],
				'visible' => $row['visible'],
				'var1' => $set['var1'],
				'var2' => $set['var2'],
				'var3' => $set['var3'],
				'var4' => $set['var4'],
				'var5' => $set['var5'],
				'id' => $row['id'],
				'lang' => $row['lang'],
				'access2' => $row['access2'],
				'can_edit' => $can_edit,
				'can_manage' => $can_manage,
			);
			$count[$panels[$row['bar']]]++;
		}
		$smcFunc['db_free_result']($request);
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
                            'attachement_type' => $article['attachement_type'],
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
        $categories = $tpArticle->getArticlesInCategory($fetch_article_titles);
		
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
	if (in_array($context['TPortal']['action'], array('help', 'moderate', 'theme', 'tpadmin', 'admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers'))) {
	    $in_admin = true;
    }

	if($context['TPortal']['action'] == 'tpmod' && isset($_GET['dl']) && substr($_GET['dl'], 0, 5) == 'admin') {
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

?>
