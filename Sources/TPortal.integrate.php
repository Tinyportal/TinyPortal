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

class TPortal_Integrate 
{

    public static function hookPreLoad()
    {
        // We need to load our autoloader outside of the main function    
        if(!defined('SMF_BACKWARDS_COMPAT')) {
            define('SMF_BACKWARDS_COMPAT', true);
            self::setup_smf_backwards_compat();
            spl_autoload_register('TPortal_Integrate::TPortalAutoLoadClass');
        }

        $hooks = array (
            'SSI'                               => '$sourcedir/TPSSI.php|ssi_TPIntegrate',
            'load_permissions'                  => 'TPortal_Integrate::hookPermissions',
            'load_illegal_guest_permissions'    => 'TPortal_Integrate::hookIllegalPermissions',
            'buffer'                            => 'TPortal_Integrate::hookBuffer',
            'menu_buttons'                      => 'TPortal_Integrate::hookMenuButtons',
            'display_buttons'                   => 'TPortal_Integrate::hookDisplayButton',
            'actions'                           => 'TPortal_Integrate::hookActions',
            'profile_areas'                     => 'TPortal_Integrate::hookProfileArea',
            'whos_online'                       => 'TPortal_Integrate::hookWhosOnline',
            'pre_log_stats'                     => 'TPortal_Integrate::hookPreLogStats',
            'tp_pre_subactions'                 => array ( 
                '$sourcedir/TPArticle.php|TPArticleActions',
                '$sourcedir/TPSearch.php|TPSearchActions',
                '$sourcedir/TPBlock.php|TPBlockActions',
            ),
            'tp_post_subactions'                 => array ( 
                '$sourcedir/TPcommon.php|TPUpshrink',
                '$sourcedir/TPdlmanager.php|TPdlmanager',
            ),           
            'tp_post_init'                      => array (
                '$sourcedir/TPShout.php|TPShoutLoad',
            ),
            'tp_admin_areas'                    => array (
                '$sourcedir/TPdlmanager.php|TPDownloadAdminAreas',
                '$sourcedir/TPShout.php|TPShoutAdminAreas',
                '$sourcedir/TPListImages.php|TPListImageAdminAreas',
            ),
            'tp_blocks'                         => array (
                '$sourcedir/TPShout.php|TPShoutBlock',
            ),

        );

        if(TP_SMF21) {
            $hooks['redirect']                = 'TPortal_Integrate::hookRedirect';
            $hooks['pre_profile_areas']       = 'TPortal_Integrate::hookProfileArea';
            $hooks['pre_load_theme']          = 'TPortal_Integrate::hookLoadTheme';
            unset($hooks['profile_areas']);
            // We can use a hook of sorts for the default actions now
            updateSettings(array('integrate_default_action' => 'TPortal_Integrate::hookDefaultAction'));
        }

		foreach ($hooks as $hook => $callable) {
            if(is_array($callable)) {
                foreach($callable as $call ) {
			        self::TPAddIntegrationFunction('integrate_' . $hook, $call, false);
                }
            }
            else {
			    self::TPAddIntegrationFunction('integrate_' . $hook, $callable, false);
            }
		}
        
    }

    public static function TPAddIntegrationFunction($hook, $call, $perm)
    {

        // SMF 2.0.x doesn't support the seperate file call so lets include it manually here. 
        if(TP_SMF21 == false && (strpos($call, '|') !== false) ) {
            $tmp = explode('|', $call);
            if( is_array($tmp) && isset($tmp[0]) && isset($tmp[1]) ) {
                $filePath = str_replace('$sourcedir', SOURCEDIR, $tmp[0]);
                if( file_exists($filePath) ) {
                    require_once($filePath);
                }
                if( is_callable($tmp[1]) ) {
                    $call = $tmp[1];
                }
            }
        }

	    add_integration_function($hook, $call, $perm);

    }

    public static function TPortalAutoLoadClass($className)
    {

        $classPrefix = mb_substr($className, 0, 2);

        if( 'TP' !== $classPrefix ) {
            return;
        }

        $dir        = BOARDDIR . '/tp-src/';

        $classFile  = $dir.$className . '.php';

        if ( file_exists( $classFile ) ) {
            require_once($classFile);
        }

    }

    public static function setup_smf_backwards_compat()
    {
        global $boarddir, $cachedir, $sourcedir, $db_type;

        if(defined('SMF_FULL_VERSION')) {
            // SMF 2.1 
            define('TP_SMF21', true);
        }
        else {
            // We must be on SMF 2.0
            define('TP_SMF21', false);
        }

        define('BOARDDIR', $boarddir);
        define('CACHEDIR', $cachedir);
        define('SOURCEDIR', $sourcedir);
        define('TPVERSION', 'v200');
        if($db_type == 'postgresql') {
            define('TP_PGSQL', true);
        }
        else {
            define('TP_PGSQL', false);
        }

    }

    public static function hookPermissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions) 
	{
    
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
                'tp_can_list_images' => array(false, 'tp', 'tp'),
            ),
            $permissionList['membergroup']
        );

      // This is to get around there being no hook to call to remove guest permissions in SMF 2.0
      if(!TP_SMF21) {
        self::hookIllegalPermissions();
      }
    }

    // Adds TP copyright in the buffer so we don't have to edit an SMF file
    public static function hookBuffer($buffer)
    {
        global $context, $scripturl, $txt;
        global $image_proxy_enabled, $image_proxy_secret, $boardurl;

        $bodyid = '';
        $bclass = '';

        // add upshrink buttons
        if( TP_SMF21 && array_key_exists('TPortal', $context) && !empty($context['TPortal']['upshrinkpanel']) ) {
            $buffer = preg_replace('~<div class="navigate_section">\s*<ul>~', '<div class="navigate_section"><ul><li class="tp_upshrink21">'.$context['TPortal']['upshrinkpanel'].'</li>', $buffer, 1);
        }
        
        // apply user membergroup colors ony when set in TP settings.
        if(!empty($context['TPortal']['use_groupcolor'])) {
            $user_match     = '~href="' . preg_quote($scripturl) . '\?action=profile;u=(\d+)"~';
            if(preg_match_all($user_match, $buffer, $matches)) {
                $user_ids       = array_values(array_unique($matches[1]));
                $user_colour    = TPGetMemberColour($user_ids);
                foreach($user_ids as $id) {
                    if(array_key_exists($id, $user_colour)){
                        $user_replace   = '~href="' . preg_quote($scripturl) . '\?action=profile;u='.$id.'"~';
                        $buffer         = preg_replace($user_replace, ' style="color:'.$user_colour[$id].';" $0', $buffer);
                    }
                }
            }
        }
        
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


        $string = '<a target="_blank" href="https://www.tinyportal.net" title="TinyPortal">TinyPortal 2.0.0</a> &copy; <a href="' . $scripturl . '?action=tportal;sa=credits" title="Credits">2005-2019</a>';

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

        if( TP_SMF21 ) {
            $tmp    = isset($txt['tp-tphelp']) ? $txt['tp-tphelp'] : 'Help';
            $find   = '<a href="'.$scripturl.'?action=help">'.$txt['help'].'</a>';
            $replace= '<a href="'.$scripturl.'?action=tportal;sa=help">'.$tmp.'</a>';
            $buffer = str_replace($find, $replace.' | '.$find, $buffer);
        }

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
 
        $tmpurl = parse_url($boardurl, PHP_URL_HOST);
        if(!empty($context['TPortal']['copyrightremoval']) && (sha1('TinyPortal'.$tmpurl) == $context['TPortal']['copyrightremoval'])) {
            return $buffer;
        }
        else {
            if( TP_SMF21 ) {
                $find       = '//www.simplemachines.org" title="Simple Machines" target="_blank" rel="noopener">Simple Machines</a>';
                $replace    = '//www.simplemachines.org" title="Simple Machines" target="_blank" rel="noopener">Simple Machines</a>, ' . $string;
            } 
            else {
                $find       = '//www.simplemachines.org" title="Simple Machines" target="_blank" class="new_win">Simple Machines</a>';
                $replace    = '//www.simplemachines.org" title="Simple Machines" target="_blank" class="new_win">Simple Machines</a><br />' . $string;
            }
            $buffer     = str_replace($find, $replace, $buffer);
        }

        if (strpos($buffer, $string) === false) {
            $string = '<div style="text-align: center; width: 100%; font-size: x-small; margin-bottom: 5px;">' . $string . '</div></body></html>';
            $buffer = preg_replace('~</body>\s*</html>~', $string, $buffer);
        }

        return $buffer;
    }

    public static function hookIllegalPermissions()
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
            'tp_can_list_images',
        );
        $context['non_guest_permissions'] = array_merge($context['non_guest_permissions'], $tp_illegal_perms);
    }

    public static function hookMenuButtons(&$buttons)
    {
        global $smcFunc, $context, $scripturl, $txt;

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
        if( TP_SMF21 && (!empty($context['linktree'])) ) {
            if (!empty($_GET) && array_key_exists('TPortal', $context) && empty($context['TPortal']['not_forum'])) {
                array_splice($context['linktree'], 1, 0, array(
                        array(
                            'url'   => $scripturl . '?action=forum',
                            'name'  => isset($txt['tp-forum']) ? $txt['tp-forum'] : 'Forum'
                        )
                    )
                );
            }

            if (!empty($context['linktree'][2]) && array_key_exists('url', $context['linktree'][2])) {
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
                        'icon' => 'menu_tpforum',
                    ),
                ),
                $buttons
        );


        // Add the admin button
		if(allowedTo('tp_settings') || allowedTo('tp_articles') || allowedTo('tp_blocks') || allowedTo('tp_dlmanager') || allowedTo('tp_shoutbox')) {
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
		}
        else {
            $buttons = array_merge(
                array_slice($buttons, 0, array_search('calendar', array_keys($buttons), true) + 1),
                array (
                    'tpadmin' => array (
                        'icon' => 'tinyportal/menu_tp.png',
                        'title' => $txt['tp-tphelp'],
                        'href' => '#',
                        'show' =>  TPcheckAdminAreas(),
                        'sub_buttons' => tp_getbuttons(),
                        ),
                    ),
                $buttons
            );
        }

        // Add the help
        if(array_key_exists('help', $buttons)) {
            $buttons['help']['sub_buttons'] = array(
                'tphelp' => array(
                    'title' => $txt['tp-tphelp'],
                    'href' => $scripturl.'?action=tportal;sa=help',
                    'show' => true,
                ),
            );
        }


        $request = $smcFunc['db_query']('', '
            SELECT value1 AS name , value3 AS href , value7 AS position , value8 AS menuicon
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
                                'icon' => $row['menuicon'],
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

    public static function hookProfileArea(&$profile_areas)
    {
        global $txt, $context;
        
        $profile_areas['tp'] = array(
            'title' => 'Tinyportal',
            'areas' => array(),
        );
               // Profile area for 2.1
        if( TP_SMF21 ) {
            $profile_areas['tp']['areas']['tpsummary'] = array(
                'label' => $txt['tpsummary'],
                'file' => 'TPSubs.php',
                'function' => 'tp_summary',
                'icon' => 'menu_tp',
                'permission' => array(
                    'own' => 'profile_view_own',
                    'any' => 'profile_view_any',
                ),
            );

            if (!$context['TPortal']['use_wysiwyg']=='0') {
                $profile_areas['tp']['areas']['tparticles'] = array(
                    'label' => $txt['articlesprofile'],
                    'file' => 'TPSubs.php',
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
            }
            else {
                $profile_areas['tp']['areas']['tparticles'] = array(
                    'label' => $txt['articlesprofile'],
                    'file' => 'TPSubs.php',
                    'function' => 'tp_articles',
                    'icon' => 'menu_tparticle',
                    'permission' => array(
                        'own' => 'profile_view_own',
                        'any' => 'profile_view_any',
                    ),
                );
            }

            if(!empty($context['TPortal']['show_download'])) {
                $profile_areas['tp']['areas']['tpdownload'] = array(
                    'label' => $txt['downloadsprofile'],
                    'file' => 'TPSubs.php',
                    'function' => 'tp_download',
                    'icon' => 'menu_tpdownload',
                    'permission' => array(
                        'own' => 'profile_view_own' && !empty($context['TPortal']['show_download']),
                        'any' => 'profile_view_any' && !empty($context['TPortal']['show_download']),
                    ),
                );
            }

            if(!$context['TPortal']['profile_shouts_hide']) {
                $profile_areas['tp']['areas']['tpshoutbox'] = array(
                    'label' => $txt['shoutboxprofile'],
                    'file' => 'TPShout.php',
                    'function' => 'tp_shoutb',
                    'icon' => 'menu_tpshout',
                    'permission' => array(
                        'own' => 'profile_view_own',
                        'any' => 'profile_view_any',
                    ),
                );
            }
        }
        else {
            // Profile area for 2.0 - no icons 
            $profile_areas['tp']['areas']['tpsummary'] = array(
                'label' => $txt['tpsummary'],
                'file' => 'TPSubs.php',
                'function' => 'tp_summary',
                'permission' => array(
                    'own' => 'profile_view_own',
                    'any' => 'profile_view_any',
                ),
            );

            if (!$context['TPortal']['use_wysiwyg']=='0') {
                $profile_areas['tp']['areas']['tparticles'] = array(
                    'label' => $txt['articlesprofile'],
                    'file' => 'TPSubs.php',
                    'function' => 'tp_articles',
                    'permission' => array(
                        'own' => 'profile_view_own',
                        'any' => 'profile_view_any',
                    ),
                    'subsections' => array(
                        'articles' => array($txt['tp-articles'], array('profile_view_own', 'profile_view_any')),
                        'settings' => array($txt['tp-settings'], array('profile_view_own', 'profile_view_any')),
                    ),
                );
            }
            else {
                $profile_areas['tp']['areas']['tparticles'] = array(
                    'label' => $txt['articlesprofile'],
                    'file' => 'TPSubs.php',
                    'function' => 'tp_articles',
                    'permission' => array(
                        'own' => 'profile_view_own',
                        'any' => 'profile_view_any',
                    ),
                );
            }

            if(!empty($context['TPortal']['show_download'])) {
                $profile_areas['tp']['areas']['tpdownload'] = array(
                    'label' => $txt['downloadsprofile'],
                    'file' => 'TPSubs.php',
                    'function' => 'tp_download',
                    'permission' => array(
                        'own' => 'profile_view_own',
                        'any' => 'profile_view_any',
                    ),
                );
            }

            if(!$context['TPortal']['profile_shouts_hide']) {
                $profile_areas['tp']['areas']['tpshoutbox'] = array(
                    'label' => $txt['shoutboxprofile'],
                    'file' => 'TPShout.php',
                    'function' => 'tp_shoutb',
                    'permission' => array(
                        'own' => 'profile_view_own',
                        'any' => 'profile_view_any',
                    ),
                );
            }

        }

    }

    public static function hookActions(&$actionArray)
    {
        $actionArray = array_merge(
            array (
                'tpadmin'   => array('TPortalAdmin.php',    'TPortalAdmin'),
                'forum'     => array('BoardIndex.php',      'BoardIndex'),
                'tportal'   => array('TPortal.php',         'TPortal'),
                'tpshout'   => array('TPShout.php',         'TPShout'),
            ),
            $actionArray
        );
    }

    public static function hookDefaultAction()
    {
        global $topic, $board, $context;

        $theAction = false;
        // first..if the action is set, but empty, don't go any further
        if (isset($_REQUEST['action']) && $_REQUEST['action']=='') {
            require_once(SOURCEDIR . '/BoardIndex.php');
            $theAction = 'BoardIndex';
        }

        // Action and board are both empty... maybe the portal page?
        if (empty($board) && empty($topic) && $context['TPortal']['front_type'] != 'boardindex') {
            require_once(SOURCEDIR . '/TPortal.php');
            $theAction = 'TPortalMain';
        }

        // If frontpage set to boardindex but it's an article or category
        if (empty($board) && empty($topic) && $context['TPortal']['front_type'] == 'boardindex' && (isset($_GET['cat']) || isset($_GET['page']))) {
            require_once(SOURCEDIR . '/TPortal.php');
            $theAction = 'TPortalMain';
        }
        // Action and board are still both empty...and no portal startpage - BoardIndex!
        elseif (empty($board) && empty($topic) && $context['TPortal']['front_type'] == 'boardindex') {
            require_once(SOURCEDIR . '/BoardIndex.php');
            $theAction = 'BoardIndex';
        }

        // SMF 2.1 has a default action hook so less source edits
        if(!TP_SMF21) {
            return $theAction;
        }
        else {
            // We need to manually call the action as this function was called be default
            call_user_func($theAction);
        }

    }

    public static function hookWhosOnline($actions)
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
        if(isset($actions['cat'])) {
            if(is_numeric($actions['cat'])) {
                $request = $smcFunc['db_query']('', '
                    SELECT 	value1 FROM {db_prefix}tp_variables
                    WHERE id = {int:id}
                    LIMIT 1',
                    array (
                        'id' => $actions['cat'],
                    )
                );
            }
            else {
                $request = $smcFunc['db_query']('', '
                    SELECT value1 FROM {db_prefix}tp_variables
                    WHERE value8 = {string:shortname}
                    LIMIT 1',
                    array (
                        'shortname' => $actions['cat'],
                    )
                );
            }
            $category = array();
            if($smcFunc['db_num_rows']($request) > 0) {
                while($row = $smcFunc['db_fetch_assoc']($request)) {
                    $category = $row;
                }
                $smcFunc['db_free_result']($request);
            }
            if(!empty($category)) {
                return sprintf($txt['tp-who-category'], $category['value1'], $actions['cat'], $scripturl );
            }
            else {
                return $txt['tp-who-categories'];
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

    public static function hookPreLogStats(&$no_stat_actions)
    {
        $no_stat_actions = array_merge($no_stat_actions, array('shout'));

        // We can also call init from here although it's not meant for this
        TPortal_init();
    }

    public static function hookRedirect(&$setLocation, &$refresh, &$permanent)
    {
        global $scripturl, $context;

        if ($setLocation == $scripturl && !empty($context['TPortal']['redirectforum'])) {
            $setLocation .= '?action=forum';
        }

    }

    public static function hookSearchLayers()
    {
        global $context;

        // are we on search page? then add TP search options as well!
        if($context['TPortal']['action'] == 'search') {
            $context['template_layers'][] = 'TPsearch';
        }

    }

    public static function hookDisplayButton(&$normal_buttons)
    {
        global $context, $scripturl;

        if(allowedTo(array('tp_settings')) && (($context['TPortal']['front_type']=='forum_selected' || $context['TPortal']['front_type']=='forum_selected_articles'))) {
            if(!in_array($context['current_topic'], explode(',', $context['TPortal']['frontpage_topics']))) {
                $normal_buttons['publish'] = array('active' => true, 'text' => 'tp-publish', 'lang' => true, 'url' => $scripturl . '?action=tportal;sa=publish;t=' . $context['current_topic']);
            }
            else {
                $normal_buttons['unpublish'] = array('active' => true, 'text' => 'tp-unpublish', 'lang' => true, 'url' => $scripturl . '?action=tportal;sa=publish;t=' . $context['current_topic']);
            }
        }
    }

    public static function hookLoadTheme(&$id_theme)
    {
        global $smcFunc, $modSettings;

        require_once(SOURCEDIR . '/TPSubs.php');

        $theme = 0;

        // are we on a article? check it for custom theme
        if(isset($_GET['page']) && !isset($_GET['action'])) {
            if (($theme = cache_get_data('tpArticleTheme', 120)) == null) {
                // fetch the custom theme if any
                $pag = TPUtil::filter('page', 'get', 'string');
                if (is_numeric($pag)) {
                    $request = $smcFunc['db_query']('', '
                        SELECT id_theme FROM {db_prefix}tp_articles
                        WHERE id = {int:page}',
                        array('page' => (int) $pag)
                    );
                }
                else {
                    $request =  $smcFunc['db_query']('', '
                        SELECT id_theme FROM {db_prefix}tp_articles
                        WHERE shortname = {string:short}',
                        array('short' => $pag)
                    );
                }
                if($smcFunc['db_num_rows']($request) > 0) {
                    $theme = $smcFunc['db_fetch_row']($request)[0];
                    $smcFunc['db_free_result']($request);
                }

                if (!empty($modSettings['cache_enable'])) {
                    cache_put_data('tpArticleTheme', $theme, 120);
                }
            }
        }
        // are we on frontpage? and it shows fetured article?
        else if(!isset($_GET['page']) && !isset($_GET['action']) && !isset($_GET['board']) && !isset($_GET['topic'])) {
            if (($theme = cache_get_data('tpFrontTheme', 120)) == null) {
                // fetch the custom theme if any
                $request = $smcFunc['db_query']('', '
                        SELECT COUNT(*) FROM {db_prefix}tp_settings
                        WHERE name = {string:name}
                        AND value = {string:value}',
                        array('name' => 'front_type', 'value' => 'single_page')
                    );
                if($smcFunc['db_num_rows']($request) > 0) {
                    $smcFunc['db_free_result']($request);
                    $request = $smcFunc['db_query']('', '
                        SELECT art.id_theme
                        FROM {db_prefix}tp_articles AS art
                        WHERE featured = 1' 
                    );
                    if($smcFunc['db_num_rows']($request) > 0) {
                        $theme = $smcFunc['db_fetch_row']($request)[0];
                        $smcFunc['db_free_result']($request);
                    }
                }
                if (!empty($modSettings['cache_enable'])) {
                    cache_put_data('tpFrontTheme', $theme, 120);
                }
            }
        }
        // how about dlmanager, any custom theme there?
        else if(isset($_GET['action']) && $_GET['action'] == 'tpmod' && isset($_GET['dl'])) {
            if (($theme = cache_get_data('tpDLTheme', 120)) == null) {
                // fetch the custom theme if any
                $request =  $smcFunc['db_query']('', '
                    SELECT value FROM {db_prefix}tp_settings
                    WHERE name = {string:name}',
                    array('name' => 'dlmanager_theme')
                );
                if($smcFunc['db_num_rows']($request) > 0) {
                    $theme = $smcFunc['db_fetch_row']($request)[0];
                    $smcFunc['db_free_result']($request);
                }
                if (!empty($modSettings['cache_enable'])) {
                    cache_put_data('tpDLTheme', $theme, 120);
                }
            }
        }

        if($theme != $id_theme && $theme > 0) {
            $id_theme = $theme;
        }

        return $id_theme;
    }

}

?>
