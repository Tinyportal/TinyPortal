<?php
/**
 * @package TinyPortal
 * @version 3.0.1
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) - The TinyPortal Team
 *
 */
namespace TinyPortal;

if (!defined('SMF')) {
    die('Hacking attempt...');
}

class Integrate
{

    public static function hookPreLoad()
    {
        global $boardurl;

        $paths = array (
            // Downloads
            '~^action=tpmod;dl=item([0-9]+)[\/]?$~'                     => '%1$s/index.php?action=tportal&amp;sa=download;dl=item%2$s',
            '~^action=tpmod;dl=cat([0-9]+)[\/]?$~'                      => '%1$s/index.php?action=tportal&amp;sa=download;dl=cat%2$s',
            '~^action=tpmod;dl=get([0-9]+)[\/]?$~'                      => '%1$s/index.php?action=tportal&amp;sa=download;dl=get%2$s',
        );

        if(is_array($_SERVER) && isset($_SERVER['QUERY_STRING'])) {
            foreach ($paths as $route => $destination) {
                if (preg_match($route, $_SERVER['QUERY_STRING'], $matches)) {
                    if (count($matches) > 1) {
                        $matches[0] = $boardurl;
                        $newUrl     = vsprintf($destination, $matches);
                        header("Location: $newUrl", true, 301);
                        exit;
                    }
                }
            }
        }


        // We need to load our autoloader outside of the main function
        if(!defined('SMF_BACKWARDS_COMPAT')) {
            define('SMF_BACKWARDS_COMPAT', true);
            self::setup_smf_backwards_compat();
            spl_autoload_register('TinyPortal\Integrate::TPortalAutoLoadClass');
        }

        $hooks = array (
            'SSI'                               => '$sourcedir/TPSSI.php|ssi_TPIntegrate',
            'load_permissions'                  => 'TinyPortal\Integrate::hookPermissions',
            'load_illegal_guest_permissions'    => 'TinyPortal\Integrate::hookIllegalPermissions',
            'buffer'                            => 'TinyPortal\Integrate::hookBuffer',
            'credits'                           => 'TinyPortal\Integrate::hookCredits',
            'menu_buttons'                      => 'TinyPortal\Integrate::hookMenuButtons',
            'display_buttons'                   => 'TinyPortal\Integrate::hookDisplayButton',
            'actions'                           => 'TinyPortal\Integrate::hookActions',
            'whos_online'                       => 'TinyPortal\Integrate::hookWhosOnline',
            'pre_log_stats'                     => 'TinyPortal\Integrate::hookPreLogStats',
            'tp_pre_subactions'                 => array (
                '$sourcedir/TPArticle.php|TPArticleActions',
                '$sourcedir/TPSearch.php|TPSearchActions',
                '$sourcedir/TPBlock.php|TPBlockActions',
                '$sourcedir/TPdlmanager.php|TPDownloadActions',
                '$sourcedir/TPcommon.php|TPCommonActions',
            ),
            'tp_post_subactions'                => array (
            ),
            'tp_post_init'                      => array (
                '$sourcedir/TPBlock.php|getBlocks',
                '$sourcedir/TPShout.php|TPShoutLoad',
            ),
            'tp_admin_areas'                    => array (
                '$sourcedir/TPdlmanager.php|TPDownloadAdminAreas',
                '$sourcedir/TPShout.php|TPShoutAdminAreas',
                '$sourcedir/TPListImages.php|TPListImageAdminAreas',
            ),
            'tp_shoutbox'                       => array (
                '$sourcedir/TPShout.php|TPShoutBlock',
            ),
            'tp_block'                          => array (
            ),
            'tp_pre_admin_subactions'           => array (
                '$sourcedir/TPBlock.php|TPBlockAdminActions',
                '$sourcedir/TPShout.php|TPShoutAdminActions',
                '$sourcedir/TPListImages.php|TPListImageAdminActions',
            ),

        );

        $hooks['redirect']              = 'TinyPortal\Integrate::hookRedirect';
        $hooks['pre_profile_areas']     = 'TinyPortal\Integrate::hookProfileArea';
        $hooks['pre_load_theme']        = 'TinyPortal\Integrate::hookPreLoadTheme';
        $hooks['load_theme']            = 'TinyPortal\Integrate::hookLoadTheme';
        $hooks['helpadmin']             = 'TinyPortal\Integrate::hookHelpadmin';
        if(!TP_SMF21) {
            $hooks['admin_areas']       = 'TinyPortal\Integrate::hookAdminAreas';
        }

        // We can use a hook of sorts for the default actions now
        updateSettings(array('integrate_default_action' => 'TinyPortal\Integrate::hookDefaultAction'));

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

        add_integration_function($hook, $call, $perm);

    }

    public static function TPortalAutoLoadClass($className)
    {

        $classPrefix    = mb_substr($className, 0, 10);

        if( 'TinyPortal' !== $classPrefix ) {
            return;
        }

        $className  = str_replace('\\', '/', $className);
        $classFile  = BOARDDIR . '/' . $className . '.php';

        if ( file_exists( $classFile ) ) {
            require_once($classFile);
        }

    }

    public static function setup_smf_backwards_compat()
    {
        global $boarddir, $cachedir, $sourcedir, $db_type, $languagesdir;

        if(defined('SMF_FULL_VERSION')) {
            // SMF 2.1 or SMF 3.0
            if(substr(SMF_FULL_VERSION, 0, 7) === 'SMF 3.0') {
                define('TP_SMF21', false);
            }
            else {
                define('TP_SMF21', true);
            }
        }
        else {
            // We must be on SMF 2.0
            define('TP_SMF21', false);
        }

        define('BOARDDIR', $boarddir);
        define('CACHEDIR', $cachedir);
        define('SOURCEDIR', $sourcedir);
        define('TPLANGUAGEDIR', $languagesdir ?? $boarddir . '/Themes/default/languages');
        define('TPVERSION', 'v301');
        if($db_type == 'postgresql') {
            define('TP_PGSQL', true);
        }
        else {
            define('TP_PGSQL', false);
        }

    }

    public static function hookAdminAreas(&$adminArea) 
    {

    }

    public static function hookPermissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
    {

        $permissionList['membergroup'] = array_merge(
            array(
                'tp_settings' => array(false, 'tp', 'tp'),
                'tp_blocks' => array(false, 'tp', 'tp'),
                'tp_articles' => array(false, 'tp', 'tp'),
                'tp_submithtml' => array(false, 'tp', 'tp'),
                'tp_submitbbc' => array(false, 'tp', 'tp'),
                'tp_editownarticle' => array(false, 'tp', 'tp'),
                'tp_alwaysapproved' => array(false, 'tp', 'tp'),
                'tp_artcomment' => array(false, 'tp', 'tp'),
                'tp_can_admin_shout' => array(false, 'tp', 'tp'),
                'tp_can_shout' => array(false, 'tp', 'tp'),
                'tp_dlmanager' => array(false, 'tp', 'tp'),
                'tp_dlupload' => array(false, 'tp', 'tp'),
                'tp_dlcreatetopic' => array(false, 'tp', 'tp'),
                'tp_can_list_images' => array(false, 'tp', 'tp'),
                'tp_can_search' => array(false, 'tp', 'tp'),
            ),
            $permissionList['membergroup']
        );

    }

    // Adds TP copyright in the buffer so we don't have to edit an SMF file
    public static function hookBuffer($buffer)
    {
        global $context, $scripturl, $txt;
        global $image_proxy_enabled, $image_proxy_secret, $boardurl;

        $bodyid = '';
        $bclass = '';

        // add upshrink buttons
        if( array_key_exists('TPortal', $context) && !empty($context['TPortal']['upshrinkpanel']) ) {
            $buffer = preg_replace('~<div class="navigate_section">\s*<ul>~', '<div class="navigate_section"><ul><li class="tp_upshrink">'.$context['TPortal']['upshrinkpanel'].'</li>', $buffer, 1);
        }

        // apply user membergroup colors ony when set in TP settings.
        if(!empty($context['TPortal']['use_groupcolor'])) {
            $user_match     = '~href="' . preg_quote($scripturl) . '\?action=profile;u=(\d+)"~';
            if(preg_match_all($user_match, $buffer, $matches)) {
                $user_ids       = array_values(array_unique($matches[1]));
                $user_colour    = TPGetMemberColour($user_ids);
                foreach($user_ids as $id) {
                    if(array_key_exists($id, $user_colour) && !empty($user_colour[$id])){
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
            $bclass =  "tpcontainer";
        }

        $tpversion = isset($context['TPortal']['version']) ? $context['TPortal']['version'] : ' ';
        $string = '<a target="_blank" href="https://www.tinyportal.net" title="TinyPortal">TinyPortal ' . $tpversion . '</a> &copy; <a href="' . $scripturl . '?action=tportal;sa=credits" title="Credits">2005-2024</a>';

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

        $tmpurl = parse_url($boardurl, PHP_URL_HOST);
        if(!empty($context['TPortal']['copyrightremoval']) && (sha1('TinyPortal'.$tmpurl) == $context['TPortal']['copyrightremoval'])) {
            return $buffer;
        }
        else {
            $tmp    = isset($txt['tp-tphelp']) ? $txt['tp-tphelp'] : 'Help';
            $find   = '<a href="'.$scripturl.'?action=help">'.$txt['help'].'</a>';
            $replace= '<a href="https://www.tinyportal.net/docs/" target=_blank>'.$tmp.'</a>';
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

        if(!empty($context['TPortal']['copyrightremoval']) && (sha1('TinyPortal'.$tmpurl) == $context['TPortal']['copyrightremoval'])) {
            return $buffer;
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
            'tp_submithtml',
            'tp_submitbbc',
            'tp_editownarticle',
            'tp_alwaysapproved',
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
        global $context, $scripturl, $txt;

        // If SMF throws a fatal_error TP is not loaded. So don't even worry about menu items.
        if(!isset($context['TPortal']) || isset($context['uninstalling'])) {
            return;
        }
		
		// If we have disabled the front page, this is not needed...
		if($context['TPortal']['front_placement'] != 'disabled') {

			// Set the forum button activated if needed.
			if(!isset($_GET['action']) && array_key_exists('TPortal', $context) && empty($context['TPortal']['not_forum'])) {
				$context['current_action'] = 'forum';
			}

			// Change the href of the 'home' button
			if($context['TPortal']['front_placement'] == 'standalone')
				$buttons['home']['href'] = $context['TPortal']['front_placement_url'];

			// This removes a edit in Load.php
			if( !empty($context['linktree']) ) {
				if($context['TPortal']['front_placement'] == 'standalone')
					$context['linktree'][0]['url'] = $context['TPortal']['front_placement_url'];

				if (array_key_exists('TPortal', $context) && empty($context['TPortal']['not_forum'])) {
					array_splice($context['linktree'], 1, 0, array(
							array(
								'url'   => ($context['TPortal']['front_placement'] == 'boardindex') ? $scripturl.'?action=forum' : $scripturl,
								'name'  => isset($txt['tp-forum']) ? $txt['tp-forum'] : 'Forum'
							)
						)
					);
				}

				if (!empty($context['linktree'][2]) && array_key_exists('url', $context['linktree'][2]) && $context['TPortal']['front_placement'] == 'boardindex') {
					$context['linktree'][2]['url'] = str_replace('#', '?action=forum#', $context['linktree'][2]['url']);
				}
			}

			// Add the forum button
			$buttons = array_merge(
					array_slice($buttons, 0, array_search('home', array_keys($buttons), true) + 1),
					array (
						'forum' => array (
							'title' => isset($txt['tp-forum']) ? $txt['tp-forum'] : 'Forum',
							'href' => ($context['TPortal']['front_placement'] == 'boardindex') ? $scripturl.'?action=forum' : $scripturl,
							'show' => true,
							'icon' => 'menu_tpforum',
						),
					),
					$buttons
			);
		};

        // Add the admin button
        if(!$context['TPortal']['hideadminmenu']=='1') {
            if(allowedTo('tp_settings') || allowedTo('tp_articles') || allowedTo('tp_blocks') || allowedTo('tp_dlmanager') || allowedTo('tp_shoutbox') || allowedTo('tp_can_admin_shout') || allowedTo('tp_can_list_images')) {
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
        }
        if(allowedTo('tp_settings')) {
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

        $dB = Database::getInstance();

        $request = $dB->db_query('', '
            SELECT value1 AS name , value2 AS newlink , value3 AS href , value7 AS position , value8 AS menuicon
            FROM {db_prefix}tp_variables
            WHERE type = {string:type}
            AND value3 LIKE {string:mainmenu}
            AND value5 = 0',
            array (
                'type' => 'menubox',
                'mainmenu' => 'menu%'
            )
        );

        if($dB->db_num_rows($request) > 0) {
            $i = 0;
            while($row = $dB->db_fetch_assoc($request)) {
                // Add the admin button
                $i++;
                $buttons = array_merge(
                        array_slice($buttons, 0, array_search($row['position'], array_keys($buttons), true) + 1),
                        array (
                            'tpbutton'.$i => array (
                                'icon' => $row['menuicon'],
                                'title' => $row['name'],
                                'target' => (($row['newlink'] == 1) ? ' target="_blank"' : ''),
                                'href' => substr($row['href'], 4),
                                'show' =>  true,
                            ),
                        ),
                        $buttons
                    );
            }
            $dB->db_free_result($request);
        }
    }

    public static function hookProfileArea(&$profile_areas)
    {
        global $txt, $context;

		if(!$context['TPortal']['hideprofileoption']=='1') {

        $profile_areas['tp'] = array(
            'title' => 'TinyPortal',
            'areas' => array(),
        );

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
    }}

    public static function hookCredits()
    {
        global $context, $scripturl, $boardurl;

        $context['copyrights']['mods'][] = '<a target="_blank" href="https://www.tinyportal.net" title="TinyPortal">TinyPortal ' . $context['TPortal']['version'] . '</a> by the TinyPortal team &copy; <a href="' . $scripturl . '?action=tportal;sa=credits" title="TinyPortal - Credits">2005-2024</a>';
    }

    public static function hookActions(&$actionArray)
    {
        $actionArray['tpadmin']     = array('TPortalAdmin.php',     'TPortalAdmin');
        $actionArray['tportal']     = array('TPortal.php',          'TPortal');
        $actionArray['tpshout']     = array('TPShout.php',          'TPShout');
        
        if(TP_SMF21) {
            $actionArray['forum']   = array('BoardIndex.php', 'BoardIndex');
        }
        else {
            $actionArray['forum']   = array('', 'SMF\\Actions\\BoardIndex::call');
        }
    }

    public static function hookDefaultAction()
    {
        global $topic, $board, $context;
        
        $theAction = false;
        // first..if the action is set, but empty, don't go any further
        if (isset($_REQUEST['action']) && $_REQUEST['action']=='') {
            if(TP_SMF21) {
                require_once(SOURCEDIR . '/BoardIndex.php');
                $theAction = 'BoardIndex';
            }
            else {
                $theAction = 'SMF\\Actions\\BoardIndex::call';
            }
        }

        // Action and board are both empty... maybe the portal page?
        if (empty($board) && empty($topic) && $context['TPortal']['front_placement'] == 'boardindex') {
            require_once(SOURCEDIR . '/TPortal.php');
            $theAction = 'TPortalMain';
        }

        // If frontpage set to boardindex but it's an article or category
        if (empty($board) && empty($topic) && $context['TPortal']['front_placement'] != 'boardindex' && (isset($_GET['cat']) || isset($_GET['page']))) {
            require_once(SOURCEDIR . '/TPortal.php');
            $theAction = 'TPortalMain';
        }
        // Action and board are still both empty...and no portal startpage - BoardIndex!
        elseif (empty($board) && empty($topic) && $context['TPortal']['front_placement'] != 'boardindex') {
            if(TP_SMF21) {
                require_once(SOURCEDIR . '/BoardIndex.php');
                $theAction = 'BoardIndex';
            }
            else {
                $theAction = 'SMF\\Actions\\BoardIndex::call';
            }
        }

        // We need to manually call the action as this function was called be default
        call_user_func($theAction);

    }

    public static function hookWhosOnline($actions)
    {
        global $txt, $scripturl;

        loadLanguage('TPortal');

        $dB = Database::getInstance();

        if(isset($actions['page'])) {
            if(is_numeric($actions['page'])) {
                $request = $dB->db_query('', '
                    SELECT subject FROM {db_prefix}tp_articles
                    WHERE id = {int:id}
                    LIMIT 1',
                    array (
                        'id' => $actions['page'],
                    )
                );
            }
            else {
                $request = $dB->db_query('', '
                    SELECT subject FROM {db_prefix}tp_articles
                    WHERE shortname = {string:shortname}
                    LIMIT 1',
                    array (
                        'shortname' => $actions['page'],
                    )
                );
            }
            $article = array();
            if($dB->db_num_rows($request) > 0) {
                while($row = $dB->db_fetch_assoc($request)) {
                    $article = $row;
                }
                $dB->db_free_result($request);
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
                $request = $dB->db_query('', '
                    SELECT  value1 FROM {db_prefix}tp_variables
                    WHERE id = {int:id}
                    LIMIT 1',
                    array (
                        'id' => $actions['cat'],
                    )
                );
            }
            else {
                $request = $dB->db_query('', '
                    SELECT value1 FROM {db_prefix}tp_variables
                    WHERE value8 = {string:shortname}
                    LIMIT 1',
                    array (
                        'shortname' => $actions['cat'],
                    )
                );
            }
            $category = array();
            if($dB->db_num_rows($request) > 0) {
                while($row = $dB->db_fetch_assoc($request)) {
                    $category = $row;
                }
                $dB->db_free_result($request);
            }
            if(!empty($category)) {
                return sprintf($txt['tp-who-category'], $category['value1'], $actions['cat'], $scripturl );
            }
            else {
                return $txt['tp-who-categories'];
            }
        }

        if(isset($actions['action']) && $actions['action'] == 'tportal' && isset($actions['dl'])) {
            return $txt['tp-who-downloads'];
        }

        if(isset($actions['action']) && $actions['action'] == 'tportal' && isset($actions['sa']) && ( $actions['sa'] == 'searcharticle' || $actions['sa'] == 'searcharticle2' )) {
            return $txt['tp-who-article-search'];
        }

        if(isset($actions['action']) && $actions['action'] == 'forum') {
            return $txt['tp-who-forum-index'];
        }

    }

    public static function hookPreLogStats(&$no_stat_actions)
    {
        $no_stat_actions = array_merge($no_stat_actions, array('tpshout' => true));
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
        if($context['TPortal']['action'] == 'search' && allowedTo(array('tp_can_search'))) {
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

    public static function hookLoadTheme()
    {
        require_once(SOURCEDIR . '/TPortal.php');
        \TPortal_init();
    }

    public static function hookPreLoadTheme(&$id_theme)
    {
        global $modSettings;

        require_once(SOURCEDIR . '/TPSubs.php');

        $theme  = 0;
        $dB     = Database::getInstance();

        // are we on a article? check it for custom theme
        if(isset($_GET['page']) && !isset($_GET['action'])) {
            if (($theme = cache_get_data('tpArticleTheme', 120)) == null) {
                // fetch the custom theme if any
                $pag = Util::filter('page', 'get', 'string');
                if (is_numeric($pag)) {
                    $request = $dB->db_query('', '
                        SELECT id_theme FROM {db_prefix}tp_articles
                        WHERE id = {int:page}',
                        array('page' => (int) $pag)
                    );
                }
                else {
                    $request = $dB->db_query('', '
                        SELECT id_theme FROM {db_prefix}tp_articles
                        WHERE shortname = {string:short}',
                        array('short' => $pag)
                    );
                }
                if($dB->db_num_rows($request) > 0) {
                    $theme = $dB->db_fetch_row($request)[0];
                    $dB->db_free_result($request);
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
                $request = $dB->db_query('', '
                        SELECT COUNT(*) FROM {db_prefix}tp_settings
                        WHERE name = {string:name}
                        AND value = {string:value}',
                        array('name' => 'front_type', 'value' => 'single_page')
                    );
                if($dB->db_num_rows($request) > 0) {
                    $dB->db_free_result($request);
                    $request = $dB->db_query('', '
                        SELECT art.id_theme
                        FROM {db_prefix}tp_articles AS art
                        WHERE featured = 1'
                    );
                    if($dB->db_num_rows($request) > 0) {
                        $theme = $dB->db_fetch_row($request)[0];
                        $dB->db_free_result($request);
                    }
                }
                if (!empty($modSettings['cache_enable'])) {
                    cache_put_data('tpFrontTheme', $theme, 120);
                }
            }
        }
        // how about dlmanager, any custom theme there?
        else if(isset($_GET['action']) && $_GET['action'] == 'tportal' && isset($_GET['dl'])) {
            if (($theme = cache_get_data('tpDLTheme', 120)) == null) {
                // fetch the custom theme if any
                $request = $dB->db_query('', '
                    SELECT value FROM {db_prefix}tp_settings
                    WHERE name = {string:name}',
                    array('name' => 'dlmanager_theme')
                );
                if($dB->db_num_rows($request) > 0) {
                    $theme = $dB->db_fetch_row($request)[0];
                    $dB->db_free_result($request);
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

    public static function hookHelpadmin()
    {
        if (isset($_GET['help']))
        {
            loadLanguage('TPortal');
            loadLanguage('TPortalAdmin');
            loadLanguage('TPdlmanager');
        }
    }

}

?>
