<?php
/**
 * @package TinyPortal
 * @version 1.5.1
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2018 - The TinyPortal Team
 *
 */

function tpAddPermissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions) {
    global $forum_version;

	loadLanguage('TPShout');

	$permissionList['membergroup'] = array_merge(
		array(
			'tp_settings' => array(false, 'tp', 'tp'),
			'tp_blocks' => array(false, 'tp', 'tp'),
			'tp_articles' => array(false, 'tp', 'tp'),
			'tp_alwaysapproved' => array(false, 'tp', 'tp'),
			'tp_submithtml' => array(false, 'tp', 'tp'),
			'tp_submitbbc' => array(false, 'tp', 'tp'),
			'tp_editownarticle' => array(false, 'tp', 'tp'),
			'tp_artcomment' => array(false, 'tp', 'tp'),
			'tp_can_admin_shout' => array(false, 'tp', 'tp'),
			'tp_can_shout' => array(false, 'tp', 'tp'),
			'tp_dlmanager' => array(false, 'tp', 'tp'),
			'tp_dlupload' => array(false, 'tp', 'tp'),
			'tp_dlcreatetopic' => array(false, 'tp', 'tp'),
		),
		$permissionList['membergroup']
	);

  // This is to get around there being no hook to call to remove guest permissions in SMF 2.0
  if(strpos($forum_version, '2.0') !== false) {
    tpAddIllegalPermissions();
  }

}

// Adds TP copyright in the buffer so we don't have to edit an SMF file
function tpAddCopy($buffer)
{
	global $context, $scripturl, $txt, $forum_version;

	$bodyid = '';
	$bclass = '';

	// Dynamic body ID
	if (isset($context['TPortal']) && $context['TPortal']['action'] == 'profile') {
		$bodyid = "profilepage";		
	} elseif (isset($context['TPortal']) && $context['TPortal']['action'] == 'pm') {
		$bodyid = "pmpage";		
	} elseif (isset($context['TPortal']) && $context['TPortal']['action'] == 'calendar') {
		$bodyid = "calendarpage";	
	} elseif (isset($context['TPortal']) && $context['TPortal']['action'] == 'mlist') {
		$bodyid = "mlistpage";	
	} elseif (isset($context['TPortal']) && in_array($context['TPortal']['action'], array('search', 'search2'))) {
		$bodyid = "searchpage";	
	} elseif (isset($context['TPortal']) && $context['TPortal']['action'] == 'forum') {
		$bodyid = "forumpage";	
	} elseif (isset($_GET['board']) && !isset($_GET['topic'])) {
		$bodyid = "boardpage";
	} elseif (isset($_GET['board']) && isset($_GET['topic'])) {
		  $bodyid = "topicpage";		
	} elseif (isset($_GET['page'])) {
		$bodyid = "page";		
	} elseif (isset($_GET['cat'])) {
		$bodyid = "catpage";		
	} elseif (isset($context['TPortal']) && $context['TPortal']['is_frontpage']) {
		$bodyid = "frontpage";	
	} else {
		$bodyid = "tpbody";
	} 

	// Dynamic body classes
	if (isset($_GET['board']) && !isset($_GET['topic'])) {
		$bclass =  "boardpage board" . $_GET['board'];
	} elseif (isset($_GET['board']) && isset($_GET['topic'])) {
		$bclass =  "boardpage board" . $_GET['board'] . " " . "topicpage topic" . $_GET['topic'];
	} elseif (isset($_GET['page'])) {
		$bclass =  "page" . $_GET['page'];	
	} elseif (isset($_GET['cat'])) {
		$bclass =  "cat" . $_GET['cat'];
	} else {
		$bclass =  "tpcontnainer";
	}


	$string = '<a target="_blank" href="http://www.tinyportal.net" title="TinyPortal">TinyPortal</a> <a href="' . $scripturl . '?action=tpmod;sa=credits" title="TP 1.5.1">&copy; 2005-2018</a>';

	if (SMF == 'SSI' || empty($context['template_layers']) || (defined('WIRELESS') && WIRELESS ) || strpos($buffer, $string) !== false)
		return $buffer;

	$find = array(
		'<body>',
		'class="copywrite"',
	);
	$replace = array(
		'<body id="' . $bodyid . '" class="' . $bclass . '">',
		'class="copywrite" style="line-height: 1;"',
	);

	if (!in_array($context['current_action'], array('post', 'post2'))) {
		$finds[] = '[cutoff]';
		$replaces[] = '';
	}

	$buffer = str_replace($find, $replace, $buffer);

    if(strpos($forum_version, '2.1') !== false) {
        $find   = '<a href="'.$scripturl.'?action=help">'.$txt['help'].'</a>';
        $replace= '<a href="'.$scripturl.'?action=tpmod;sa=help">'.$txt['tp-tphelp'].'</a>';
	    $buffer = str_replace($find, $replace.' | '.$find, $buffer);
    }

	global $boardurl;
	$tmpurl = parse_url($boardurl, PHP_URL_HOST);

	if(!empty($context['TPortal']['copyrightremoval']) && (sha1('TinyPortal'.$tmpurl) == $context['TPortal']['copyrightremoval'])) {
        return $buffer;
    }
    else {
	    $find       = '<a href="http://www.simplemachines.org" title="Simple Machines" target="_blank" class="new_win">Simple Machines</a>';
		$replace    = '<a href="http://www.simplemachines.org" title="Simple Machines" target="_blank" class="new_win">Simple Machines</a><br />' . $string;
	    $buffer     = str_replace($find, $replace, $buffer);
    }

	if (strpos($buffer, $string) === false) {
		$string = '<div style="text-align: center; width: 100%; font-size: x-small; margin-bottom: 5px;">' . $string . '</div></body></html>';
		$buffer = preg_replace('~</body>\s*</html>~', $string, $buffer);
	}

	return $buffer;
}

function tpAddIllegalPermissions()
{
	global $context;
	
	if (empty($context['non_guest_permissions']))
		$context['non_guest_permissions'] = array();
	
	$tp_illegal_perms = array(
		'tp_settings',
		'tp_blocks',
		'tp_articles',
		'tp_alwaysapproved',
		'tp_submithtml',
		'tp_submitbbc',
		'tp_editownarticle',
		'tp_artcomment',
		'tp_can_admin_shout',
		'tp_can_shout',
		'tp_dlmanager',
		'tp_dlupload',
		'tp_dlcreatetopic',
	);
	$context['non_guest_permissions'] = array_merge($context['non_guest_permissions'], $tp_illegal_perms);
}

function tpAddMenuItems(&$buttons)
{
    global $smcFunc, $context, $scripturl, $txt, $forum_version;

    // If SMF throws a fatal_error TP is not loaded. So don't even worry about menu items. 
    if(!isset($context['TPortal'])) {
        return;
    }

    // Set the forum button activated if needed.
    if(isset($_GET['board']) || isset($_GET['topic'])) {
        $context['current_action'] = 'forum';
    }
    elseif(isset($_GET['sa']) && $_GET['sa'] == 'help') {
        $context['current_action'] = 'help';
    }

    // This removes a edit in Load.php
    if( (strpos($forum_version, '2.1') !== false) && (!empty($context['linktree'])) ) {
        if (!empty($_GET)) {
            array_splice($context['linktree'], 1, 0, array(
                    array(
                        'url'   => $scripturl . '?action=forum',
                        'name'  => 'Forum'
                    )
                )
            );
        }

        if (!empty($context['linktree'][2])) {
            $context['linktree'][2]['url'] = str_replace('#', '?action=forum#', $context['linktree'][2]['url']);
        }
    }

    // Add the forum button     
    $buttons = array_merge(
            array_slice($buttons, 0, array_search('home', array_keys($buttons), true) + 1),
            array ( 
                'forum' => array (
                    'title' => isset($txt['tp-forum']) ? $txt['tp-forum'] : 'Forum',
                    'href' => $scripturl.'?action=forum',
                    'show' => ($context['TPortal']['front_type'] != 'boardindex') ? true : false,
                ),
            ),
            $buttons
    );


    // Add the admin button
    $buttons = array_merge(
            array_slice($buttons, 0, array_search('calendar', array_keys($buttons), true) + 1),
            array ( 
                'tpadmin' => array (
                    'icon' => 'tinyportal/menu_tp.png',
					'title' => $txt['tp-tphelp'],
                    'href' => $scripturl.'?action=tpadmin',
                    'show' =>  TPcheckAdminAreas(),
                    'sub_buttons' => tp_getbuttons(),
                ),
            ),
            $buttons
    );

    // Add the help
    if(array_key_exists('help', $buttons)) {
        $buttons['help']['sub_buttons'] = array(
            'tphelp' => array(
                'title' => $txt['tp-tphelp'],
                'href' => $scripturl.'?action=tpmod;sa=help',
                'show' => true,
            ),
        );
    }


    $request = $smcFunc['db_query']('', '
        SELECT value1 AS name , value3 AS href , value7 AS position
        FROM {db_prefix}tp_variables 
        WHERE type = {string:type} 
        AND value3 LIKE {string:mainmenu}
        AND value5 = 0',
        array (
            'type' => 'menubox', 
            'mainmenu' => 'menu%'
        )
    );

    if($smcFunc['db_num_rows']($request) > 0) {
        $i = 0;
        while($row = $smcFunc['db_fetch_assoc']($request)) {
            // Add the admin button
            $i++;
            $buttons = array_merge(
                    array_slice($buttons, 0, array_search($row['position'], array_keys($buttons), true) + 1),
                    array ( 
                        'tpbutton'.$i => array (
                            'title' => $row['name'],
                            'href' => substr($row['href'], 4),
                            'show' =>  true,
                        ),
                    ),
                    $buttons
                );
        }
        $smcFunc['db_free_result']($request);
    }
}

function tpAddProfileMenu(&$profile_areas)
{
	global $txt;
	
	$profile_areas['tp'] = array(
		'title' => 'Tinyportal',
		'areas' => array(),
	);

	$profile_areas['tp']['areas']['tpsummary'] = array(
		'label' => $txt['tpsummary'],
		'file' => 'TPmodules.php',
		'function' => 'tp_summary',
		'icon' => 'menu_tp',
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
	);

	$profile_areas['tp']['areas']['tparticles'] = array(
		'label' => $txt['articlesprofile'],
		'file' => 'TPmodules.php',
		'function' => 'tp_articles',
		'icon' => 'menu_tparticle',
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
		'subsections' => array(
			'articles' => array($txt['tp-articles'], array('profile_view_own', 'profile_view_any')),
			'settings' => array($txt['tp-settings'], array('profile_view_own', 'profile_view_any')),
		),
	);

	$profile_areas['tp']['areas']['tpdownload'] = array(
		'label' => $txt['downloadprofile'],
		'file' => 'TPmodules.php',
		'function' => 'tp_download',
		'icon' => 'menu_tpdownload',
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
	);

	$profile_areas['tp']['areas']['tpshoutbox'] = array(
		'label' => $txt['shoutboxprofile'],
		'file' => 'TPmodules.php',
		'function' => 'tp_shoutb',
		'icon' => 'menu_tpshout',
		'permission' => array(
			'own' => 'profile_view_own',
			'any' => 'profile_view_any',
		),
	);
}

function addTPActions(&$actionArray)
{
	$actionArray = array_merge(
		array(
			'tpadmin' => array('TPortalAdmin.php', 'TPortalAdmin'),
			'forum' => array('BoardIndex.php', 'BoardIndex'),
			'tpmod' => array('TPmodules.php', 'TPmodules'),
		),
		$actionArray
	);
}

function whichTPAction()
{
	global $topic, $board, $sourcedir, $context, $forum_version;

	$theAction = false;
	// first..if the action is set, but empty, don't go any further
	if (isset($_REQUEST['action']) && $_REQUEST['action']=='')
	{
		require_once($sourcedir . '/BoardIndex.php');
		$theAction = 'BoardIndex';
	}
	// Action and board are both empty... maybe the portal page?
	if (empty($board) && empty($topic) && $context['TPortal']['front_type'] != 'boardindex')
	{
		require_once($sourcedir . '/TPortal.php');
		$theAction = 'TPortal';
	}
	// If frontpage set to boardindex but it's an article or category
	if (empty($board) && empty($topic) && $context['TPortal']['front_type'] == 'boardindex' && (isset($_GET['cat']) || isset($_GET['page'])))
	{
		require_once($sourcedir . '/TPortal.php');
		$theAction = 'TPortal';
	}
	// Action and board are still both empty...and no portal startpage - BoardIndex!
	elseif (empty($board) && empty($topic) && $context['TPortal']['front_type'] == 'boardindex')
	{
		require_once($sourcedir . '/BoardIndex.php');
		$theAction = 'BoardIndex';
	}

    // SMF 2.1 has a default action hook so less source edits
    if(strpos($forum_version, '2.0') !== false) {
        return $theAction;
    }
    else {
        // We need to manually call the action as this function was called be default
        call_user_func($theAction);
    }
}

function tpImageRewrite($buffer)
{
	global $context;
	global $image_proxy_enabled, $image_proxy_secret, $boardurl;

	if ($image_proxy_enabled && ( array_key_exists('TPortal', $context) && $context['TPortal']['imageproxycheck'] > 0 ) ) {
		if (!empty($buffer) && stripos($buffer, 'http://') !== false) {
			$buffer = preg_replace_callback("~<img([\w\W]+?)/>~",
				function( $matches ) use ( $boardurl, $image_proxy_secret ) {
					if (stripos($matches[0], 'http://') !== false) {
						$matches[0] = preg_replace_callback("~src\=(?:\"|\')(.+?)(?:\"|\')~",
							function( $src ) use ( $boardurl, $image_proxy_secret ) {
								if (stripos($src[1], 'http://') !== false)
									return ' src="'. $boardurl . '/proxy.php?request='.urlencode($src[1]).'&hash=' . md5($src[1] . $image_proxy_secret) .'"';
								else
									return $src[0];
							},
							$matches[0]);
					}
					return $matches[0];
				},
				$buffer);
		}
	}
	return $buffer;
}

function tpWhosOnline($actions)
{
    global $txt, $smcFunc, $scripturl;

    loadLanguage('TPortal');

    if(isset($actions['page'])) {
        if(is_numeric($actions['page'])) {
            $request = $smcFunc['db_query']('', '
                SELECT subject FROM {db_prefix}tp_articles
                WHERE id = {int:id}
                LIMIT 1',
                array (
                    'id' => $actions['page'], 
                )
            );
        }
        else {
            $request = $smcFunc['db_query']('', '
                SELECT subject FROM {db_prefix}tp_articles
                WHERE shortname = {string:shortname}
                LIMIT 1',
                array (
                    'shortname' => $actions['page'], 
                )
            );
        }
        $article = array();
        if($smcFunc['db_num_rows']($request) > 0) {
            while($row = $smcFunc['db_fetch_assoc']($request)) {
                $article = $row;
            }
            $smcFunc['db_free_result']($request);
        }
        if(!empty($article)) {
            return sprintf($txt['tp-who-article'], $article['subject'], $actions['page'], $scripturl );
        }
        else {
            return $txt['tp-who-articles'];
        }
    }

    if(isset($actions['action']) && $actions['action'] == 'tpmod' && isset($actions['dl'])) {
        return $txt['tp-who-downloads'];
    }

    if(isset($actions['action']) && $actions['action'] == 'tpmod' && isset($actions['sa']) && ( $actions['sa'] == 'searcharticle' || $actions['sa'] == 'searcharticle2' )) {
        return $txt['tp-who-article-search'];
    }

    if(isset($actions['action']) && $actions['action'] == 'forum') {
        return $txt['tp-who-forum-index'];
    }

}

function tpStatsIgnore(&$no_stat_actions) 
{
    $no_stat_actions = array_merge($no_stat_actions, array('shout'));

	// We can also call init from here although it's not meant for this 
	TPortal_init();
}

function tpIntegrateRedirect(&$setLocation, &$refresh, &$permanent)
{
    global $scripturl, $context;

    if ($setLocation == $scripturl && !empty($context['TPortal']['redirectforum'])) {
        $setLocation .= '?action=forum';
    }

}

function tpLoadTheme()
{


}

?>
