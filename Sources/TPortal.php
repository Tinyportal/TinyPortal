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
 * Copyright (C) 2018 - The TinyPortal Team
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

// Load the language file straight away for the check in SMF2.0 and Load.php
global $txt;
if(loadLanguage('TPortal') == false) {
    loadLanguage('TPortal', 'english');
}

// TinyPortal init
function TPortal_init()
{
	global $context, $txt, $user_info, $settings, $boarddir, $modSettings, $forum_version, $db_type;

	// has init been run before? if so return!
	if(isset($context['TPortal']['redirectforum'])) {
		return;
    }

    if(!defined('SMF_BACKWARDS_COMPAT')) {
        define('SMF_BACKWARDS_COMPAT', true);
        setup_smf_backwards_compat();
        spl_autoload_register('tpAutoLoadClass');
    }

	if(loadLanguage('TPortal') == false) {
		loadLanguage('TPortal', 'english');
    }

	$context['TPortal'] = array();
	$context['TPortal']['querystring'] = $_SERVER['QUERY_STRING'];

	if(!isset($context['forum_name'])) {
		$context['forum_name'] = '';
    }

	// Include a ton of functions.
	require_once(SOURCEDIR.'/TPSubs.php');

	// Add all the TP settings into ['TPortal']
	setupTPsettings();

	// go back on showing attachments..
	if(isset($_GET['action']) && $_GET['action'] == 'dlattach') {
		return;
    }

	// Grab the SSI for its functionality
	require_once($boarddir. '/SSI.php');

	// Load JQuery if it's not set (anticipated for SMF2.1)
	if (!isset($modSettings['jquery_source'])) {
		$context['html_headers'] .= '<script src="https://code.jquery.com/jquery-1.10.1.min.js"></script>';
    }

	fetchTPhooks();
	doModules();

	// set up the layers, but not for certain actions
	if(!isset($_REQUEST['preview']) && !isset($_REQUEST['quote']) && !isset($_REQUEST['xml']) && !isset($aoptions['nolayer'])) {
		$context['template_layers'][] = $context['TPortal']['hooks']['tp_layer'];
    }

	loadtemplate('TPsubs');
	loadtemplate('TPBlockLayout');

	// is the permanent theme option set?
	if(isset($_GET['permanent']) && !empty($_GET['theme']) && $context['user']['is_logged']) {
		TP_permaTheme($_GET['theme']);
    }

	// do after action
	if(isset($_GET['page']) && !isset($context['current_action'])) {
		$context['shortID'] = doTPpage();
    }
	else if(isset($_GET['cat'])) {
		$context['catshortID'] = doTPcat();
    }
	else if(!isset($_GET['action']) && !isset($_GET['board']) && !isset($_GET['topic'])) {
		doTPfrontpage();
	}

	// determine the blocks
	doTPblocks();
	// determine which sidebars to hide
	tpWhichHideBars();
	// Load the stylesheet stuff
	tpLoadCSS();

	// if we are in permissions admin section, load all permissions
	if((isset($_GET['action']) && $_GET['action'] == 'permissions') || (isset($_GET['area']) && $_GET['area'] == 'permissions')) {
		TPcollectPermissions();
    }

	// Show search/frontpage topic layers?
	tpDoTagSearchLayers();

	// set cookie change for selected upshrinks
	tpSetupUpshrinks();

	// finally..any errors finding an article or category?
	if(!empty($context['art_error'])) {
		fatal_error($txt['tp-articlenotexist'], false);
    }

	if(!empty($context['cat_error'])) {
		fatal_error($txt['tp-categorynotexist'], false);
    }

	// let a module take over
	if($context['TPortal']['front_type'] == 'module' && !isset($_GET['page']) && !isset($_GET['cat']) && !isset($_GET['action'])) {
		// let the module take over
		require_once($context['TPortal']['tpmodules']['frontsection'][$context['TPortal']['front_module']]['sourcefile']);
		if(function_exists($context['TPortal']['tpmodules']['frontsection'][$context['TPortal']['front_module']]['function'])) {
			call_user_func($context['TPortal']['tpmodules']['frontsection'][$context['TPortal']['front_module']]['function']);
        }
		else {
			echo $txt['tp-nomodule'];
        }
	}
}


function tpWhichHideBars()
{
	global $maintenance, $context;

	// if we are in maintance mode, just hide panels
	if (!empty($maintenance) && !allowedTo('admin_forum')) {
		tp_hidebars('all');
    }
		
	// for some very large forum sections, give the option to hide bars
	if($context['TPortal']['hidebars_profile'] == '1' && $context['TPortal']['action'] == 'profile') {
		tp_hidebars('all');
    }
	else if($context['TPortal']['hidebars_pm'] == '1' && $context['TPortal']['action'] == 'pm') {
		tp_hidebars('all');
    }
	else if($context['TPortal']['hidebars_calendar'] == '1' && $context['TPortal']['action'] == 'calendar') {
		tp_hidebars('all');
    }
	else if($context['TPortal']['hidebars_search'] == '1' && in_array($context['TPortal']['action'], array('search', 'search2'))) {
		tp_hidebars('all');
    }
	else if($context['TPortal']['hidebars_memberlist'] == '1' && $context['TPortal']['action'] == 'mlist') {
		tp_hidebars('all');
    }

	// if custom actions is specified, hide panels there as well
	if(!empty($context['TPortal']['hidebars_custom'])) {
		$cactions = explode(',', $context['TPortal']['hidebars_custom']);
		if(in_array($context['TPortal']['action'], $cactions)) {
			tp_hidebars('all');
        }
	}

	// finally..wap modes should not display the bars
	if(isset($_GET['wap']) || isset($_GET['wap2']) || isset($_GET['imode'])) {
		tp_hidebars('all');
    }

	// maybe we are at the password pages?
	if(isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('login2', 'profile2'))) {
		tp_hidebars('all');
    }

}

function tpLoadCSS()
{
	global $context, $settings;

	$context['html_headers'] .=  "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"/>";
	
    // load both stylesheets to be sure all is in, but not if things aren't setup!
	if(!empty($settings['default_theme_url']) && !empty($settings['theme_url']) && file_exists($settings['theme_dir'].'/css/tp-style.css')) {
		$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="' . $settings['theme_url'] . '/css/tp-style.css?'.TPVERSION.'" />';
    }
	else {
		$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/tp-style.css?'.TPVERSION.'" />';
    }

	if(!empty($settings['default_theme_url']) && !empty($settings['theme_url']) && file_exists($settings['theme_dir'].'/css/tp-responsive.css')) {
		$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="' . $settings['theme_url'] . '/css/tp-responsive.css?'.TPVERSION.'" />';
    }
	else {
		$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/tp-responsive.css?'.TPVERSION.'" />';
    }

	if(!empty($context['TPortal']['padding'])) {
		$context['html_headers'] .= '
            <style type="text/css">
                .block_leftcontainer,
                .block_rightcontainer,
                .block_centercontainer,
                .block_uppercontainer,
                .block_lowercontainer,
                .block_topcontainer,
                .block_bottomcontainer {
                    padding-bottom: ' . $context['TPortal']['padding'] . 'px;
                }

                #tpleftbarHeader {
                    margin-right: ' . $context['TPortal']['padding'] . 'px;
                }

                #tprightbarHeader {
                    margin-left: ' . $context['TPortal']['padding'] . 'px;
                }

            </style>';
    }
}


function setupTPsettings()
{
	global $maintenance, $context, $txt, $settings, $smcFunc, $modSettings;

	$context['TPortal']['always_loaded'] = array();

	// Try to load it from the cache
	if (($context['TPortal'] = cache_get_data('tpSettings', 90)) == null)
	{
		// get the settings
		$request =  $smcFunc['db_query']('', '
			SELECT name, value FROM {db_prefix}tp_settings', array()
		);
		if ($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_row']($request))
			{
				$context['TPortal'][$row[0]] = $row[1];
				// ok, any module that like to load?
				if(substr($row[0], 0, 11) == 'load_module')
					$context['TPortal']['always_loaded'][] = $row[1];
			}
			$smcFunc['db_free_result']($request);
		}

		if (!empty($modSettings['cache_enable']))
			cache_put_data('tpSettings', $context['TPortal'], 90);
	}

	// setup the userbox settings
	$userbox = explode(',', $context['TPortal']['userbox_options']);
	foreach($userbox as $u => $val)
		$context['TPortal']['userbox'][$val] = 1;

	// setup sizes for DL and articles
	$context['TPortal']['dl_screenshotsize'] = explode(',', $context['TPortal']['dl_screenshotsizes']);
	$context['TPortal']['art_imagesize'] = explode(',', $context['TPortal']['art_imagesizes']);

	// another special case: sitemap items
	$context['TPortal']['sitemap'] = array();
	foreach($context['TPortal'] as $what => $value)
	{
		if(substr($what, 0, 14) == 'sitemap_items_' && !empty($value))
			$context['TPortal']['sitemap_items'] .= ','. $value;
	}
	if(isset($context['TPortal']['sitemap_items']))
	{
		$context['TPortal']['sitemap'] = explode(',', $context['TPortal']['sitemap_items']);
	}
	// yet another special case: category list
	$context['TPortal']['category_list'] = array();
	if(isset($context['TPortal']['cat_list']))
		$context['TPortal']['category_list'] = explode(',', $context['TPortal']['cat_list']);

	// setup path for TP images, fallback on default theme - but not if its set already!
	if(!isset($settings['tp_images_url']))
	{
		// check if the them has a folder
		if(file_exists($settings['theme_dir'].'/images/tinyportal/TParticle.png'))
			$settings['tp_images_url'] = $settings['images_url'] . '/tinyportal';
		else
			$settings['tp_images_url'] = $settings['default_images_url'] . '/tinyportal';
	}

	// hooks setting up
	$context['TPortal']['hooks'] = array(
		'topic_check' => array(),
		'board_check' => array(),
		'tp_layer' => 'tp',
		'tp_block' => 'TPblock',
	);


	// start of things
	$context['TPortal']['mystart'] = 0;
	if(isset($_GET['p']) && $_GET['p'] != '' && is_numeric($_GET['p']))
		$context['TPortal']['mystart'] = TPUtil::filter('p', 'get', 'int');

	$context['tp_html_headers'] = '';

   // any sorting taking place?
   if(isset($_GET['tpsort']))
		$context['TPortal']['tpsort'] = $_GET['tpsort'];
   else
		$context['TPortal']['tpsort'] = '';

	// if not in forum start off empty
	$context['TPortal']['is_front'] = false;
	$context['TPortal']['is_frontpage'] = false;
	if(!isset($_GET['action']) && !isset($_GET['board']) && !isset($_GET['topic']))
	{
		TPstrip_linktree();
		// a switch to make it clear what is "forum" and not
		$context['TPortal']['not_forum'] = true;
	}
	// are we actually on frontpage then?
	if(!isset($_GET['cat']) && !isset($_GET['page']) && !isset($_GET['action']))
	{
		$context['TPortal']['is_front'] = true;
		$context['TPortal']['is_frontpage'] = true;
	}

	// Set the page title.
	if($context['TPortal']['is_front'] && !empty($context['TPortal']['frontpage_title']))
		$context['page_title'] = $context['TPortal']['frontpage_title'];
	if(isset($_GET['action']) && $_GET['action'] == 'tpadmin')
		$context['page_title'] = $context['forum_name'] . ' - ' . $txt['tp-admin'];

	// if we are in maintance mode, just hide panels
	if (!empty($maintenance) && !allowedTo('admin_forum'))
		tp_hidebars('all');

	// save the action value
	$context['TPortal']['action'] = !empty($_GET['action']) ? tp_sanitize($_GET['action']) : '';

	// save the frontapge setting for SMF
	$settings['TPortal_front_type'] = $context['TPortal']['front_type'];
	if(empty($context['page_title']))
		$context['page_title'] = $context['forum_name'];

	if(empty($context['TPortal']['standalone'])) {
		$request = $smcFunc['db_query']('', '
			SELECT value
			FROM {db_prefix}tp_settings
			WHERE name = \'standalone_mode\''
		);
		$context['TPortal']['standalone'] = $smcFunc['db_fetch_assoc']($request)['value'];
		$smcFunc['db_free_result']($request);
	}
}

function fetchTPhooks()
{
	global $context, $smcFunc, $boarddir;

	// are we inside a board?
	if (isset($context['current_topic']))
	{
		$what = 'what_topic';
		$param = $context['current_topic'];
	}
	// perhaps a topic then?
	elseif (isset($context['current_board']))
	{
		$what = 'what_board';
		$param = $context['current_board'];
	}
	// alright, an article?
	elseif (isset($_GET['page']) && $context['current_action'] != 'help')
	{
		$what = 'what_page';
		$param = $_GET['page'];
	}
	// a category of articles?
	elseif (isset($_GET['cat']))
	{
		$what = 'what_cat';
		$param = $_GET['cat'];
	}
	// guess neither..
	else
		$param = 0;

	// something should always load? + submissions
	$types = array('layerhook', 'art_not_approved', 'dl_not_approved');

	$request2 = $smcFunc['db_query']('', '
		SELECT *
		FROM {db_prefix}tp_variables
		WHERE type IN ({array_string:type})',
		array(
			'type' => $types
		)
	);

	$context['TPortal']['submitcheck'] = array('articles' => 0, 'uploads' => 0);

	// do the actual hooks
	while ($row = $smcFunc['db_fetch_assoc']($request2))
	{
		if (isset($what) && $row['value1'] == $what && $row['type'] == 'layerhook' && file_exists(SOURCEDIR . '/' .$row['value2']))
		{
				require_once(SOURCEDIR. '/' .$row['value2']);
				if (function_exists($row['value3']))
					call_user_func($row['value3'], $param);
		}
		if ($row['type'] == 'art_not_approved' && allowedTo('tp_articles'))
			$context['TPortal']['submitcheck']['articles']++;
		// check submission on dl manager, but only if its active
		elseif ($row['type'] == 'dl_not_approved' && $context['TPortal']['show_download'] && allowedTo('tp_dlmanager'))
			$context['TPortal']['submitcheck']['uploads']++;
		// something alwasy loads?
		elseif ($row['type'] == 'layerhook')
		{
			if ($row['value1'] == 'what_all' && file_exists(SOURCEDIR . '/' . $row['value2']))
			{
				require_once(SOURCEDIR. '/' .$row['value2']);
				if (function_exists($row['value3']))
					call_user_func($row['value3'], $param);
			}
			// something always loads?
			elseif ($row['value1'] == 'what_all_tpmodule' && file_exists($boarddir . '/tp-files/tp-modules/' .$row['value2'] . '/Sources/' . $row['value2'] . '.php'))
			{
				// is it installed at all?
				if (isset($context['TPortal'][$row['value4']]) && $context['TPortal'][$row['value4']] == 1)
				{
					require_once($boarddir . '/tp-files/tp-modules/' . $row['value2'] . '/Sources/' . $row['value2'] . '.php');
					if (function_exists($row['value3']))
						call_user_func($row['value3'], $param);
				}
			}
		}
	}
	$smcFunc['db_free_result']($request2);
}

function doTPpage()
{

	global $context, $scripturl, $txt, $modSettings, $boarddir, $smcFunc, $user_info;

	$now = time();
	// Set the avatar height/width
	$avatar_width = '';
	$avatar_height = '';
	if ($modSettings['avatar_action_too_large'] == 'option_html_resize' || $modSettings['avatar_action_too_large'] == 'option_js_resize') {
		$avatar_width = !empty($modSettings['avatar_max_width_external']) ? ' width="' . $modSettings['avatar_max_width_external'] . '"' : '';
		$avatar_height = !empty($modSettings['avatar_max_height_external']) ? ' height="' . $modSettings['avatar_max_height_external'] . '"' : '';
	}

	// check validity and fetch it
	if(!empty($_GET['page'])) {
		$page = tp_sanitize($_GET['page']);

		$_SESSION['login_url'] = $scripturl . '?page=' . $page;

        $tpArticle  = new TPArticle();
        $article    = $tpArticle->getArticle($page)[0];
        if(is_array($article)) {
            $shown  = false;
			$valid  = true;

			// if its not approved, say so.
			if($article['approved'] == 0) {
				TP_error($txt['tp-notapproved']);
				$shown = true;
            }

			// and for no category
			if($article['category'] < 1 || $article['category'] > 9999 && $shown == false) {
				TP_error($txt['tp-nocategory']);
				$shown = true;
            }
	
			// likewise for off.
			if($article['off'] == 1 && $shown == false) {
				TP_error($txt['tp-noton']);
				$shown = true;
            }	

        	if($shown == true && !allowedTo('tp_articles')) {
				$valid = false;
            }

			if( get_perm($article['value3']) && $valid) {
				// compability towards old articles
				if(empty($article['type'])) {
					$article['type'] = $article['rendertype'] = 'html';
				}

				// shortname title
				$article['shortname'] = un_htmlspecialchars($article['shortname']);
				// Add ratings together
				$article['rating'] = array_sum(explode(',', $article['rating']));
				// allowed and all is well, go on with it.
				$context['TPortal']['article'] = $article;

                $context['TPortal']['article']['avatar'] = set_avatar_data( array(      
                        'avatar' => $article['avatar'],
                        'email' => $article['email_address'],
                        'filename' => !empty($article['filename']) ? $article['filename'] : '',
                        'id_attach' => $article['id_attach'],
                        'attachement_type' => $article['attachement_type'],
                     )
                )['image'];

                $tpArticle->updateArticleViews($page);

                $comments = $tpArticle->getArticleComments($context['user']['id'] , $article['id']);

				require_once(SOURCEDIR . '/TPcommon.php');

                $context['TPortal']['article']['countarticles'] = $tpArticle->getTotalAuthorArticles($context['TPortal']['article']['author_id']);

				// We'll use this in the template to allow comment box
				if (allowedTo('tp_artcomment')) {
					$context['TPortal']['can_artcomment'] = true;
                }

				$context['TPortal']['article_comments_count']   = 0;
                $context['TPortal']['article']['comment_posts'] = array();
                if(is_array($comments)) {
                    $last = $comments['last'];
                    $context['TPortal']['article_comments_new']     = $comments['new_count'];
					$context['TPortal']['article_comments_count']   = $comments['comment_count'];
                    unset($comments['last']);
                    unset($comments['new_count']);
                    unset($comments['comment_count']);
					
                    foreach($comments as $row) {

                        $avatar = set_avatar_data( array(      
                                    'avatar'            => $row['avatar'],
                                    'email'             => $row['email_address'],
                                    'filename'          => !empty($row['filename']) ? $row['filename'] : '',
                                    'id_attach'         => $row['id_attach'],
                                    'attachement_type'  => $row['attachment_type'],
                                )
                        )['image'];

						$context['TPortal']['article']['comment_posts'][] = array(
							'id'        => $row['id'],
							'subject'   => '<a href="'.$scripturl.'?page='.$context['TPortal']['article']['id'].'#comment'. $row['id'].'">'.$row['value1'].'</a>',
							'text'      => parse_bbc($row['value2']),
							'timestamp' => $row['value4'],
							'date'      => timeformat($row['value4']),
							'poster_id' => $row['value3'],
							'poster'    => $row['real_name'],
							'is_new'    => ( $row['value4'] > $last ) ? true : false,
							'avatar' => array (
								'name' => &$row['avatar'],
								'image' => $avatar,
								'href'  => $row['avatar'] == '' ? ($row['id_attach'] > 0 ? (empty($row['attachement_type']) ? $scripturl . '?action=tpmod;sa=tpattach;attach=' . $row['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) : '') : (stristr($row['avatar'], 'https://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar']),
								'url'   => $row['avatar'] == '' ? '' : (stristr($row['avatar'], 'https://') ? $row['avatar'] : $modSettings['avatar_url'] . '/' . $row['avatar'])
							),
						);
					}
				}

				// the frontblocks should not display here
				$context['TPortal']['frontpanel'] = 0;
				// sort out the options
				$context['TPortal']['article']['visual_options'] = explode(',', $article['options']);

				// the custom widths
				foreach ($context['TPortal']['article']['visual_options'] as $pt) {
					if(substr($pt, 0, 11) == 'lblockwidth') {
						$context['TPortal']['blockwidth_left'] = substr($pt, 11);
                    }
					if(substr($pt, 0, 11) == 'rblockwidth') {
						$context['TPortal']['blockwidth_right'] = substr($pt, 11);
                    }
				}
				// check if no theme is to be applied
				if(in_array('nolayer', $context['TPortal']['article']['visual_options'])) {
					$context['template_layers'] = array('nolayer');
					// add the headers!
					$context['tp_html_headers'] .= $article['headers'];
				}
				// set bars on/off according to options, setting override
				$all = array('showtop', 'centerpanel', 'leftpanel', 'rightpanel', 'toppanel', 'bottompanel', 'lowerpanel');
				$all2=array('top', 'cblock', 'lblock', 'rblock', 'tblock', 'bblock', 'lbblock', 'comments', 'views', 'rating', 'date', 'title',
				'commentallow', 'commentupshrink', 'ratingallow', 'nolayer', 'avatar');

				for($p = 0; $p < 6; $p++) {
					$primary = $context['TPortal'][$all[$p]];
					if(in_array($all2[$p], $context['TPortal']['article']['visual_options'])) {
						$secondary = 1;
                    }
					else {
						$secondary = 0;
                    }

					if($primary == '1') {
						$context['TPortal'][$all[$p]] = $secondary;
                    }
				}
				$ct = explode('|', $article['value7']);
				$cat_opts = array();
				foreach($ct as $cc => $val) {
					$ts = explode('=', $val);
					if(isset($ts[0]) && isset($ts[1])) {
						$cat_opts[$ts[0]] = $ts[1];
                    }
				}

				// decide the template
				if(isset($cat_opts['catlayout']) && $cat_opts['catlayout'] == 7) {
					$cat_opts['template'] = $article['value9'];
                }

				$context['TPortal']['article']['category_opts'] = $cat_opts;

				// the article should follow panel settngs from category?
				if(in_array('inherit', $context['TPortal']['article']['visual_options'])) {
					// set bars on/off according to options, setting override
					$all=array('upperpanel', 'leftpanel', 'rightpanel', 'toppanel', 'bottompanel', 'lowerpanel');
					for($p = 0; $p < 5; $p++) {
						if(isset($cat_opts[$all[$p]])) {
							$context['TPortal'][$all[$p]] = $cat_opts[$all[$p]];
                        }
					}
				}

				// should we supply links to articles in same category?
				if(in_array('category', $context['TPortal']['article']['visual_options'])) {
					$request = $smcFunc['db_query']('', '
						SELECT id, subject, shortname
						FROM {db_prefix}tp_articles
						WHERE category = {int:cat}
						AND off = 0
						AND approved = 1
						ORDER BY parse',
						array('cat' => $context['TPortal']['article']['category'])
					);
					if($smcFunc['db_num_rows']($request) > 0) {
						$context['TPortal']['article']['others'] = array();
						while($row = $smcFunc['db_fetch_assoc']($request)) {
							if($row['id'] == $context['TPortal']['article']['id']) {
								$row['selected'] = 1;
                            }

							$context['TPortal']['article']['others'][] = $row;
						}
						$smcFunc['db_free_result']($request);
					}
				}

				// can we rate this article?
				$context['TPortal']['article']['can_rate'] = in_array($context['user']['id'], explode(',', $article['voters'])) ? false : true;

				// are we rather printing this article and printing page is allowed?
				if(isset($_GET['print']) && $context['TPortal']['print_articles'] == 1) {
					if(!isset($article['id'])) {
						redirectexit();
                    }
					$what = '<h2>' . $article['subject'] . ' </h2>'. $article['body'];
					$pwhat = 'echo \'<h2>\' . $article[\'subject\'] . \'</h2>\';' . $article['body'];
					if($article['type'] == 'php') {
						$context['TPortal']['printbody'] = eval($pwhat);
                    }
					elseif($article['type'] == 'import') {
						if(!file_exists($boarddir. '/' . $article['fileimport'])) {
							echo '<em>' , $txt['tp-cannotfetchfile'] , '</em>';
                        }
						else {
							include($article['fileimport']);
                        }
						$context['TPortal']['printbody'] = '';
					}
					elseif($article['type'] == 'bbc') {
						$context['TPortal']['printbody'] = parse_bbc($what);
                    }
					else {
						$context['TPortal']['printbody'] = $what;
                    }

					$context['TPortal']['print'] = '<a href="' .$scripturl . '?page='. $article['id'] . '"><strong>' . $txt['tp-printgoback'] . '</strong></a>';

					loadtemplate('TPprint');
					$context['template_layers'] = array('tp_print');
					$context['sub_template'] = 'tp_print_body';
					tp_hidebars();
				}
				// linktree?
				if(!in_array('linktree', $context['TPortal']['article']['visual_options'])) {
					$context['linktree'][0] = array('url' => '', 'name' => '');
                }
				else {
					// we need the categories for the linktree
					$allcats = array();
					$request =  $smcFunc['db_query']('', '
						SELECT * FROM {db_prefix}tp_variables
						WHERE type = {string:type}',
						array('type' => 'category')
					);
					if($smcFunc['db_num_rows']($request) > 0) {
						while($row = $smcFunc['db_fetch_assoc']($request)) {
							$allcats[$row['id']] = $row;
                        }

						$smcFunc['db_free_result']($request);
					}

					// setup the linkree
					TPstrip_linktree();

					// do the category have any parents?
					$parents = array();
					$parent = $context['TPortal']['article']['category'];
					if(count($allcats) > 0) {
						while($parent !=0 && isset($allcats[$parent]['id'])) {
							$parents[] = array(
								'id' => $allcats[$parent]['id'],
								'name' => $allcats[$parent]['value1'],
								'shortname' => !empty($allcats[$parent]['value8']) ? $allcats[$parent]['value8'] : $allcats[$parent]['id'],
							);
							$parent = $allcats[$parent]['value2'];
						}
					}
					// make the linktree
					$parts = array_reverse($parents, TRUE);
					// add to the linktree
					foreach($parts as $parent) {
						TPadd_linktree($scripturl.'?cat='. $parent['shortname'], $parent['name']);
                    }

					TPadd_linktree($scripturl.'?page='. (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']), $context['TPortal']['article']['subject']);
				}

				$context['page_title'] = $context['TPortal']['article']['subject'];

				if (defined('WIRELESS') && WIRELESS) {
					$context['TPortal']['single_article'] = true;
					loadtemplate('TPwireless');
					// decide what subtemplate
					$context['sub_template'] = WIRELESS_PROTOCOL . '_tp_page';
				}

			}
			else {
				$context['art_error'] = true;
			}

			if(allowedTo('tp_articles')) {
				$now = time();
				if((!empty($article['pub_start']) && $article['pub_start'] > $now) || (!empty($article['pub_end']) && $article['pub_end'] < $now)) {
					$context['tportal']['article_expired'] = $article['id'];
					$context['TPortal']['tperror'] = '<span class="error largetext">'.$txt['tp-expired-start'] . '</span><p>'. timeformat($article['pub_start']) . '&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;' . timeformat($article['pub_end']).'</p>';
				}
			}
			return $article['id'];
		}
		else {
			$context['art_error'] = true;
        }
	}
	else {
		return;
    }

    return;
}

function doTPcat()
{
	//return if not quite a category
	if((isset($_GET['area']) && $_GET['area'] == 'manageboards') || isset($_GET['action']))
		return;

	global $context, $scripturl, $txt, $modSettings, $smcFunc;

	$now = time();

	// check validity and fetch it
	if(!empty($_GET['cat']))
	{
		$cat    = tp_sanitize($_GET['cat']);
		$catid  = is_numeric($cat) ? 'id = {int:cat}' : 'value8 = {string:cat}';

		// get the category first
		$request =  $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_variables
			WHERE '. $catid .' LIMIT 1',
			array('cat' => is_numeric($cat) ? (int) $cat : $cat)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$category = $smcFunc['db_fetch_assoc']($request);
			$smcFunc['db_free_result']($request);
			// check permission
			if(get_perm($category['value3']))
			{
				// get the sorting from the category
				$op = explode('|', $category['value7']);
				$options = array();
				foreach($op as $po => $val)
				{
					$a = explode('=', $val);
					if(isset($a[1]))
						$options[$a[0]] = $a[1];
				}

				$catsort    = isset($options['sort']) ? $options['sort'] : 'date';
				if($catsort == 'authorID') {
					$catsort = 'author_id';
                }

				$catsort_order  = isset($options['sortorder']) ? $options['sortorder'] : 'desc';
				$max            = empty($options['articlecount']) ? $context['TPortal']['frontpage_limit'] : $options['articlecount'];
				$start          = $context['TPortal']['mystart'];

				// some swapping to avoid compability issues
				$options['catlayout'] = isset($options['catlayout']) ? $options['catlayout'] : 1;

				// make the template
				if($options['catlayout'] == 7)
					$context['TPortal']['frontpage_template'] = $category['value9'];

				// allowed and all is well, go on with it.
				$context['TPortal']['category'] = $category;
				$context['TPortal']['category']['articles'] = array();

				// copy over the options as well
				$context['TPortal']['category']['options'] = $options;

				// set bars on/off according to options, setting override
				$all = array('centerpanel', 'leftpanel', 'rightpanel', 'toppanel', 'bottompanel', 'lowerpanel');
				for($p = 0; $p < 5; $p++)
				{
					if(isset($options[$all[$p]]) && $context['TPortal'][$all[$p]] == 1)
						$context['TPortal'][$all[$p]] = 1;
					else
						$context['TPortal'][$all[$p]] = 0;
				}

				// fallback value
				if(!isset($context['TPortal']['category']['options']['catlayout']))
					$context['TPortal']['category']['options']['catlayout'] = 1;

				$request = $smcFunc['db_query']('', '
				    SELECT art.id, ( CASE WHEN art.useintro = 1 THEN art.intro ELSE  art.body END ) AS body, mem.email_address AS email_address,
						art.date, art.category, art.subject, art.author_id as author_id, art.frame, art.comments, art.options,
						art.comments_var, art.views, art.rating, art.voters, art.shortname, art.useintro, art.intro,
						art.fileimport, art.topic, art.illustration, COALESCE(art.type, "html") as rendertype ,COALESCE(art.type, "html") as type,
						COALESCE(mem.real_name, art.author) as real_name, mem.avatar, mem.posts, mem.date_registered as date_registered,mem.last_login as last_login,
						COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type as attachement_type
					FROM {db_prefix}tp_articles AS art
					LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
					LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type != 3)
					WHERE art.category = {int:cat}
					AND ((art.pub_start = 0 AND art.pub_end = 0)
					OR (art.pub_start !=0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
					OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
					OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
					AND art.off = 0
					AND art.approved = 1
					ORDER BY art.sticky desc, art.'.$catsort.' '.$catsort_order.'
					LIMIT {int:start}, {int:max}',
					array(
						'cat' => $category['id'],
						'start' => $start,
						'max' => $max,
					)
				);

				if($smcFunc['db_num_rows']($request) > 0)
				{
					$total = $smcFunc['db_num_rows']($request);
					$col1 = ceil($total / 2);
					$counter = 0;
					$context['TPortal']['category']['col1'] = array(); $context['TPortal']['category']['col2'] = array();
					while($row = $smcFunc['db_fetch_assoc']($request))
					{
						// Add the rating together
						$row['rating'] = array_sum(explode(',', $row['rating']));
						// expand the vislaoptions
						$row['visual_options'] = explode(',', $row['options']);

                        $row['avatar'] = set_avatar_data( array(      
                                    'avatar' => $row['avatar'],
                                    'email' => $row['email_address'],
                                    'filename' => !empty($row['filename']) ? $row['filename'] : '',
                                    'id_attach' => $row['id_attach'],
                                    'attachement_type' => $row['attachement_type'],
                                )
                        )['image'];

						if($counter == 0)
							$context['TPortal']['category']['featured'] = $row;
						elseif($counter < $col1 )
							$context['TPortal']['category']['col1'][] = $row;
						elseif($counter > $col1 || $counter == $col1)
							$context['TPortal']['category']['col2'][] = $row;

						$counter++;
					}
					$smcFunc['db_free_result']($request);
				}

				// any children then?
				$allcats = array();
				$context['TPortal']['category']['children'] = array();
				$request =  $smcFunc['db_query']('', '
					SELECT cat.id, cat.value1, cat.value2, COUNT(art.id) as articlecount
					FROM ({db_prefix}tp_variables as cat)
					LEFT JOIN {db_prefix}tp_articles as art ON (art.category = cat.id)
					WHERE cat.type = {string:type} GROUP BY art.category, cat.id, cat.value1, cat.value2',
					array('type' => 'category')
				);
				if($smcFunc['db_num_rows']($request) > 0)
				{
					while($row = $smcFunc['db_fetch_assoc']($request))
					{
						// get any children
						if($row['value2'] == $cat)
							$context['TPortal']['category']['children'][] = $row;

						$allcats[$row['id']] = $row;
					}
					$smcFunc['db_free_result']($request);
				}

				// get how many articles in all
				$request =  $smcFunc['db_query']('', '
					SELECT COUNT(*) FROM {db_prefix}tp_articles as art
					WHERE art.category = {int:cat}
					AND ((art.pub_start = 0 AND art.pub_end = 0)
					OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
					OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
					OR (art.pub_start !=0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
					AND art.off = 0 AND art.approved = 1',
					array('cat' => $category['id'])
				);
				if($smcFunc['db_num_rows']($request)>0)
				{
					$row = $smcFunc['db_fetch_row']($request);
					$all_articles = $row[0];
				}
				else
					$all_articles = 0;

				// make the pageindex!
				$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?cat=' . $cat, $start, $all_articles, $max);

				// setup the linkree
				TPstrip_linktree();

				// do the category have any parents?
				$parents = array();
				$parent = $context['TPortal']['category']['value2'];
				// save the immediate for wireless

				if (defined('WIRELESS') && WIRELESS)
				{
					if($context['TPortal']['category']['value2'] > 0)
						$context['TPortal']['category']['catname'] =  $allcats[$context['TPortal']['category']['value2']]['value1'];
					else
						$context['TPortal']['category']['catname'] =  $txt['tp-frontpage'];
				}
				while($parent != 0)
				{
					$parents[] = array(
						'id' => $allcats[$parent],
						'name' => $allcats[$parent]['value1'],
						'shortname' => !empty($allcats[$parent]['value8']) ? $allcats[$parent]['value8'] : $allcats[$parent]['id'],
					);
					$parent = $allcats[$parent]['value2'];
				}

				// make the linktree
				$parts = array_reverse($parents, TRUE);
				// add to the linktree
				foreach($parts as $parent)
					TPadd_linktree($scripturl.'?cat='. $parent['shortname'] , $parent['name']);
				if(!empty($context['TPortal']['category']['shortname']))
					TPadd_linktree($scripturl.'?cat='. $context['TPortal']['category']['value8'], $context['TPortal']['category']['value1']);
				else
					TPadd_linktree($scripturl.'?cat='. $context['TPortal']['category']['id'], $context['TPortal']['category']['value1']);

				// check clist
				$context['TPortal']['clist'] = array();
				foreach(explode(',' , $context['TPortal']['cat_list']) as $cl => $value)
				{
					if(isset($allcats[$value]) && is_numeric($value))
					{
						$context['TPortal']['clist'][] = array(
								'id' => $value,
								'name' => $allcats[$value]['value1'],
								'selected' => $value == $cat ? true : false,
								);
						$txt['catlist'. $value] = $allcats[$value]['value1'];
					}
				}
				$context['TPortal']['show_catlist'] = count($context['TPortal']['clist']) > 0 ? true : false;

				if (defined('WIRELESS') && WIRELESS)
				{
					$context['TPortal']['single_article'] = false;
					loadtemplate('TPwireless');
					// decide what subtemplate
					$context['sub_template'] = WIRELESS_PROTOCOL . '_tp_cat';
				}
				$context['page_title'] = $context['TPortal']['category']['value1'];
				return $category['id'];
			}
			else
				return;
		}
		else
			$context['cat_error'] = true;
	}
	else
		return;

}

// do the frontpage
function doTPfrontpage()
{
	global $context, $scripturl, $user_info, $modSettings, $smcFunc, $txt, $db_type;

	// check we aren't in any other section because 'cat' is used in SMF and TP
	if(isset($_GET['action']) || isset($_GET['board']) || isset($_GET['topic']))
		return;

	$now = time();
	// set up visual options for frontpage
	$context['TPortal']['visual_opts'] = explode(',', $context['TPortal']['frontpage_visual']);

	// first, the panels
	foreach(array('left', 'right', 'center', 'top', 'bottom', 'lower') as $pan => $panel)
	{
		if($context['TPortal'][$panel.'panel'] == 1 && in_array($panel, $context['TPortal']['visual_opts']))
			$context['TPortal'][$panel.'panel'] = 1;
		else
			$context['TPortal'][$panel.'panel'] = 0;
	}
	// get the sorting
	foreach($context['TPortal']['visual_opts'] as $vi => $vo)
	{
		if(substr($vo, 0, 5) == 'sort_')
			$catsort = substr($vo, 5);
		else
			$catsort = 'date';

		if(substr($vo, 0, 10) == 'sortorder_')
			$catsort_order = substr($vo, 10);
		else
			$catsort_order = 'desc';
	}
	if(!in_array($catsort, array('date', 'author_id', 'id', 'parse')))
		$catsort = 'date';

	$max = $context['TPortal']['frontpage_limit'];
	$start = $context['TPortal']['mystart'];

	// fetch the articles, sorted
	switch($context['TPortal']['front_type']) {
        case 'articles_only': 
		// first, get all available
		$artgroups = '';
		if(!$context['user']['is_admin']) {
            global $db_type;
            if($db_type == 'mysql') {
			    $artgroups = 'AND (FIND_IN_SET(' . implode(', var.value3) OR FIND_IN_SET(', $user_info['groups']) . ', var.value3))';
            }
            else {
                foreach($user_info['groups'] as $k => $v) {
                    $artgroups .= "AND '$v' = ANY (string_to_array(var.value3, ',' ) )";
                }
            }
        }


        $tpArticle          = new TPArticle();
        $articles_total     = $tpArticle->getTotalArticles($artgroups);
		// make the pageindex!
		$context['TPortal']['pageindex'] = TPageIndex($scripturl .'?frontpage', $start, $articles_total, $max);

		$request =  $smcFunc['db_query']('', '
			SELECT art.id, ( CASE WHEN art.useintro = 1 THEN art.intro ELSE  art.body END ) AS body,
				art.date, art.category, art.subject, art.author_id as author_id, var.value1 as category_name, var.value8 as category_shortname,
				art.frame, art.comments, art.options, art.intro, art.useintro,
				art.comments_var, art.views, art.rating, art.voters, art.shortname,
				art.fileimport, art.topic, art.illustration,art.type as rendertype ,
				COALESCE(mem.real_name, art.author) as real_name, mem.avatar, mem.posts, mem.date_registered as date_registered,mem.last_login as last_login,
				COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type as attachement_type, mem.email_address AS email_address
			FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}tp_variables AS var ON(var.id = art.category)
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type!=3)
			WHERE art.off = 0
			' . $artgroups . '
			AND ((art.pub_start = 0 AND art.pub_end = 0)
			OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
			OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
			OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
			AND art.category > 0
			AND art.approved = 1
			AND (art.frontpage = 1 OR art.featured = 1)
			ORDER BY art.featured DESC, art.sticky DESC, art.'.$catsort.' '. $catsort_order .'
			LIMIT {int:start}, {int:max}',
			array('start' => $start, 'max' => $max)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			$total = $smcFunc['db_num_rows']($request);
			$col1 = ceil($total / 2);
			$col2 = $total - $col1;
			$counter = 0;

			$context['TPortal']['category'] = array(
				'articles' => array(),
				'col1' => array(),
				'col2' => array(),
				'options' => array(
					'catlayout' => $context['TPortal']['frontpage_catlayout'],
				)
			);

			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				// expand the vislaoptions
				$row['visual_options'] = explode(',', $row['options']);

                $row['avatar'] = set_avatar_data( array(      
                            'avatar' => $row['avatar'],
                            'email' => $row['email_address'],
                            'filename' => !empty($row['filename']) ? $row['filename'] : '',
                            'id_attach' => $row['id_attach'],
                            'attachement_type' => $row['attachement_type'],
                        )
                )['image'];

            	if($counter == 0)
					$context['TPortal']['category']['featured'] = $row;
				elseif($counter < $col1 )
					$context['TPortal']['category']['col1'][] = $row;
				elseif($counter > $col1 || $counter == $col1)
					$context['TPortal']['category']['col2'][] = $row;

				$counter++;
			}
			$smcFunc['db_free_result']($request);
		}
        break;
    case 'single_page':
		$request =  $smcFunc['db_query']('', '
			SELECT art.id, ( CASE WHEN art.useintro = 1 THEN art.intro ELSE  art.body END ) AS body,
				art.date, art.category, art.subject, art.author_id as author_id, var.value1 as category_name, var.value8 as category_shortname,
				art.frame, art.comments, art.options, art.intro, art.useintro,
				art.comments_var, art.views, art.rating, art.voters, art.shortname,
				art.fileimport, art.topic, art.illustration,art.type as rendertype ,
				COALESCE(mem.real_name, art.author) as real_name, mem.avatar, mem.posts, mem.date_registered as date_registered,mem.last_login as last_login,
				COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type as attachement_type, mem.email_address AS email_address
			FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}tp_variables AS var ON(var.id = art.category)
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type!=3)
			WHERE art.off = 0
			AND ((art.pub_start = 0 AND art.pub_end = 0)
			OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
			OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
			OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
			AND art.featured = 1
			AND art.approved = 1
			LIMIT 1'
		);
		if($smcFunc['db_num_rows']($request) > 0) {
			$context['TPortal']['category'] = array(
				'articles' => array(),
				'col1' => array(),
				'col2' => array(),
				'options' => array(
					'catlayout' => $context['TPortal']['frontpage_catlayout'],
				)
			);

			$row = $smcFunc['db_fetch_assoc']($request);
			// expand the vislaoptions
			$row['visual_options'] = explode(',', $row['options']);
            
            $row['avatar'] = set_avatar_data( array(      
                        'avatar' => $row['avatar'],
                        'email' => $row['email_address'],
                        'filename' => !empty($row['filename']) ? $row['filename'] : '',
                        'id_attach' => $row['id_attach'],
                        'attachement_type' => $row['attachement_type'],
                    )
            )['image'];

			$context['TPortal']['category']['featured'] = $row;
			$smcFunc['db_free_result']($request);
		}
        break;
	case 'forum_only':
    case 'forum_selected':
		$totalmax = 200;

		loadLanguage('Stats');

		// Find the post ids.
		if($context['TPortal']['front_type'] == 'forum_only')
        {
			$request =  $smcFunc['db_query']('', '
				SELECT t.id_first_msg as ID_FIRST_MSG
				FROM ({db_prefix}topics as t, {db_prefix}boards as b)
				WHERE t.id_board = b.id_board
				AND t.id_board IN({raw:board})
				' . ($context['TPortal']['allow_guestnews'] == 0 ? 'AND {query_see_board}' : '') . '
				ORDER BY t.id_first_msg DESC
				LIMIT {int:max}',
				array(
					'board' => $context['TPortal']['SSI_board'],
					'max' => $totalmax)
			);
        }
		else
        {
			$request =  $smcFunc['db_query']('', '
				SELECT t.id_first_msg as ID_FIRST_MSG
				FROM ({db_prefix}topics as t, {db_prefix}boards as b)
				WHERE t.id_board = b.id_board
				AND t.id_topic IN(' . (empty($context['TPortal']['frontpage_topics']) ? 0 : '{raw:topics}') .')
				' . ($context['TPortal']['allow_guestnews'] == 0 ? 'AND {query_see_board}' : '') . '
				ORDER BY t.id_first_msg DESC',
				array(
					'topics' => $context['TPortal']['frontpage_topics']
				)
			);
        }

		$posts = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$posts[] = $row['ID_FIRST_MSG'];
		$smcFunc['db_free_result']($request);

		if (empty($posts))
			return array();

		// do some conversion
		if($catsort == 'date')
            $catsort = 'poster_time';
		elseif($catsort == 'author_id')
            $catsort = 'id_member';
		elseif($catsort == 'parse' || $catsort == 'id')
            $catsort = 'id_msg';
		else
			$catsort = 'poster_time';

		$request =  $smcFunc['db_query']('', '
			SELECT m.subject, m.body,
				COALESCE(mem.real_name, m.poster_name) AS real_name, m.poster_time AS date, mem.avatar, mem.posts, mem.date_registered AS date_registered, mem.last_login AS last_login,
				COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type AS attachement_type, t.id_board AS category, b.name AS category_name,
				t.num_replies AS numReplies, t.id_topic AS id, m.id_member AS author_id, t.num_views AS views, t.num_replies AS replies, t.locked,
				COALESCE(thumb.id_attach, 0) AS thumb_id, thumb.filename AS thumb_filename, mem.email_address AS email_address
			FROM ({db_prefix}topics AS t, {db_prefix}messages AS m)
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member AND a.attachment_type !=3)
			LEFT JOIN {db_prefix}attachments AS thumb ON (t.id_first_msg = thumb.id_msg AND thumb.attachment_type = 3)
			LEFT JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			WHERE t.id_first_msg IN ({array_int:posts})
			AND m.id_msg = t.id_first_msg
			ORDER BY m.{raw:catsort} DESC
			LIMIT {int:max} OFFSET {int:start}',
			array(
				'posts' => $posts,
				'catsort' => $catsort,
				'start' => $start,
				'max' => $max,
			)
		);

		// make the pageindex!
		$context['TPortal']['pageindex'] = TPageIndex($scripturl .'?frontpage', $start, count($posts), $max);

		if($smcFunc['db_num_rows']($request) > 0)
		{
			$total = $smcFunc['db_num_rows']($request);
			$col1 = ceil($total / 2);
			$col2 = $total - $col1;
			$counter = 0;

			$context['TPortal']['category'] = array(
				'articles' => array(),
				'col1' => array(),
				'col2' => array(),
				'options' => array(
					'catlayout' => $context['TPortal']['frontpage_catlayout'],
					)
				);

			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$length = $context['TPortal']['frontpage_limit_len'];
				if (!empty($length) && $smcFunc['strlen']($row['body']) > $length)
				{
					$row['body'] = $smcFunc['substr']($row['body'], 0, $length);

					// The first space or line break. (<br />, etc.)
					$cutoff = max(strrpos($row['body'], ' '), strrpos($row['body'], '>'));

					if ($cutoff !== false)
						$row['body'] = $smcFunc['substr']($row['body'], 0, $cutoff);

					$row['readmore'] = '... <p class="tp_readmore"><strong><a href="'. $scripturl. '?topic='. $row['id']. '">'. $txt['tp-readmore']. '</a></strong></p>';
				}

				// some needed addons
				$row['rendertype'] = 'bbc';
				$row['frame'] = 'theme';
				$row['boardnews'] = 1;
				if(!isset($context['TPortal']['frontpage_visopts']))
					$context['TPortal']['frontpage_visopts'] = 'date,title,author,views' . ($context['TPortal']['forumposts_avatar'] == 1 ? ',avatar' : '');

				$row['visual_options'] = explode(',', $context['TPortal']['frontpage_visopts']);
				$row['useintro'] = '0';
                $row['avatar'] = set_avatar_data( array(      
                            'avatar' => $row['avatar'],
                            'email' => $row['email_address'],
                            'filename' => !empty($row['filename']) ? $row['filename'] : '',
                            'id_attach' => $row['id_attach'],
                            'attachement_type' => $row['attachement_type'],
                        )
                )['image'];

				if(!empty($row['thumb_id']))
					$row['illustration'] = $scripturl . '?action=tpmod;sa=tpattach;topic=' . $row['id'] . '.0;attach=' . $row['thumb_id'] . ';image';

				if($counter == 0)
					$context['TPortal']['category']['featured'] = $row;
				elseif($counter < $col1 && $counter > 0)
					$context['TPortal']['category']['col1'][] = $row;
				elseif($counter > $col1 || $counter == $col1)
					$context['TPortal']['category']['col2'][] = $row;

				$counter++;
			}
			$smcFunc['db_free_result']($request);
		}
        break;
    case 'forum_articles':
    case 'forum_selected_articles':
		// first, get all available
		$artgroups = '';
		if(!$context['user']['is_admin']) {
            global $db_type;
            if($db_type == 'mysql') {
			    $artgroups = 'AND (FIND_IN_SET(' . implode(', var.value3) OR FIND_IN_SET(', $user_info['groups']) . ', var.value3))';
            }
            else {
                foreach($user_info['groups'] as $k => $v) {
                    $artgroups .= "AND '$v' = ANY (string_to_array(var.value3, ',' ) )";
                }
            }
        }

		$totalmax = 200;
		loadLanguage('Stats');
		$year = 10000000;
		$year2 = 100000000;

		$request =  $smcFunc['db_query']('',
		'SELECT art.id, art.date, art.sticky, art.featured
			FROM {db_prefix}tp_articles AS art
			INNER JOIN {db_prefix}tp_variables AS var
            ON var.id = art.category
			WHERE art.off = 0
			' . $artgroups . '
			AND ((art.pub_start = 0 AND art.pub_end = 0)
			OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
			OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
			OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
			AND art.category > 0
			AND art. approved = 1
			AND (art.frontpage = 1 OR art. featured = 1)
			ORDER BY art.featured DESC, art.sticky desc, art.date DESC'
		);

		$posts = array();
		if($smcFunc['db_num_rows']($request) > 0) {
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				if($row['sticky'] == 1) {
					$row['date'] += $year;
                }
				if($row['featured'] == 1) {
					$row['date'] += $year2;
                }
				$posts[$row['date'].'_' . sprintf("%06s", $row['id'])] = 'a_' . $row['id'];
			}
			$smcFunc['db_free_result']($request);
		}

		// Find the post ids.
		if($context['TPortal']['front_type'] == 'forum_articles') {
			$request =  $smcFunc['db_query']('', '
				SELECT t.id_first_msg AS id_first_msg , m.poster_time AS date
				FROM {db_prefix}topics AS t
                INNER JOIN {db_prefix}boards AS b
                ON t.id_board = b.id_board
                INNER JOIN {db_prefix}messages AS m
				ON t.id_first_msg = m.id_msg
				WHERE t.id_board IN({raw:board})
				' . ($context['TPortal']['allow_guestnews'] == 0 ? 'AND {query_see_board}' : '') . '
				ORDER BY date DESC
				LIMIT {int:max}',
				array('board' => $context['TPortal']['SSI_board'], 'max' => $totalmax)
			);
        }
		else {
			$request =  $smcFunc['db_query']('', '
				SELECT t.id_first_msg AS id_first_msg , m.poster_time AS date
				FROM {db_prefix}topics AS t
                INNER JOIN {db_prefix}boards AS b
				ON t.id_board = b.id_board
                INNER JOIN {db_prefix}messages AS m
				ON t.id_first_msg = m.id_msg
				WHERE t.id_topic IN(' . (empty($context['TPortal']['frontpage_topics']) ? '0' : $context['TPortal']['frontpage_topics']) .')
				' . ($context['TPortal']['allow_guestnews'] == 0 ? 'AND {query_see_board}' : '') . '
				ORDER BY date DESC'
			);
        }

		if($smcFunc['db_num_rows']($request) > 0) {
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$posts[$row['date'].'_' . sprintf("%06s", $row['id_first_msg'])] = 'm_' . $row['id_first_msg'];
            }
			$smcFunc['db_free_result']($request);
		}

		// Sort the articles/posts before grabing the limit, otherwise they are out of order
		ksort($posts, SORT_NUMERIC);
		$posts = array_reverse($posts);

		// which should we select
		$aposts = array();
        $mposts = array();
        $a = 0;
		foreach($posts as $ab => $val)
		{
			if(($a == $start || $a > $start) && $a < ($start + $max))
			{
				if(substr($val, 0, 2) == 'a_')
					$aposts[] = substr($val, 2);
				elseif(substr($val, 0, 2) == 'm_')
					$mposts[] = substr($val, 2);
			}
			$a++;
		}

		$thumbs = array();
		if(count($mposts) > 0)
		{
			// Find the thumbs.
			$request =  $smcFunc['db_query']('', '
				SELECT id_thumb FROM {db_prefix}attachments
				WHERE id_msg IN ({array_int:posts})
				AND id_thumb > 0',
				array('posts' => $mposts)
			);

			if($smcFunc['db_num_rows']($request) > 0)
			{
				while ($row = $smcFunc['db_fetch_assoc']($request))
					$thumbs[] = $row['id_thumb'];
				$smcFunc['db_free_result']($request);
			}
		}
		// make the pageindex!
		$context['TPortal']['pageindex'] = TPageIndex($scripturl .'?frontpage', $start, count($posts), $max);

		// Clear request so that the check further down works correctly
		$request = false;

		$context['TPortal']['category'] = array(
			'articles' => array(),
			'col1' => array(),
			'col2' => array(),
			'options' => array(
				'catlayout' => $context['TPortal']['frontpage_catlayout'],
				'layout' => $context['TPortal']['frontpage_layout'],
			),
			'category_opts' => array(
				'catlayout' => $context['TPortal']['frontpage_catlayout'],
				'template' => $context['TPortal']['frontpage_template'],
			)
		);

		$forumPosts = $posts = array();
        // ok we got the post ids now, fetch each one, forum first
		if(count($mposts) > 0) {
            $forumPosts = ssi_fetchPosts($mposts, false, 'array');
        }

        global $memberContext;

		// insert the forumposts into $posts
		if(is_array($forumPosts) && count($forumPosts) > 0) {
			$length = $context['TPortal']['frontpage_limit_len'];
            foreach($forumPosts as $k => $row) {
                // FIXME 
                $row['date'] = $row['timestamp'];
                $row['real_name'] = $row['poster']['name'];
                $row['author_id'] = $row['poster']['id'];
                $row['category'] = $row['board']['name'];
                $row['views'] = 0;
                $row['date_registered'] = 0;
                // FIXME 

                // Load their context data.
                loadMemberData($row['author_id']);
                loadMemberContext($row['author_id']);

                // Store this member's information.
                if(array_key_exists($row['author_id'], $memberContext)) {
                    $avatar         = $memberContext[$row['author_id']];
                    $row['avatar']  = $avatar['avatar']['image'];
                }
                else {
                    $row['avatar']  = '';
                }


                if(TPUtil::shortenString($row['body'], $context['TPortal']['frontpage_limit_len'])) {
					$row['readmore'] = '... <p class="tp_readmore"><strong><a href="'. $scripturl. '?topic='. $row['id']. '">'. $txt['tp-readmore']. '</a></strong></p>';
                }

				// some needed addons
				$row['rendertype'] = 'bbc';
				$row['frame'] = 'theme';
				$row['boardnews'] = 1;

				if(!isset($context['TPortal']['frontpage_visopts'])) {
					$context['TPortal']['frontpage_visopts'] = 'date,title,author,views' . ($context['TPortal']['forumposts_avatar'] == 1 ? ',avatar' : '');
                }

				$row['visual_options'] = explode(',', $context['TPortal']['frontpage_visopts']);
				$row['useintro'] = '0';

				if(!empty($row['thumb_id'])) {
					$row['illustration'] = $scripturl . '?action=tpmod;sa=tpattach;topic=' . $row['id'] . '.0;attach=' . $row['thumb_id'] . ';image';
                }

				$posts[$row['timestamp'].'0' . sprintf("%06s", $row['id'])] = $row;
			}
		}
		// next up is articles
		if(count($aposts) > 0) {
            $tpArticle  = new TPArticle();
            $articles   = $tpArticle->getArticle($aposts);
            foreach ( $articles as $k => $row ) {
                // expand the vislaoptions
                $row['visual_options'] = explode(',', $row['options']);
                $row['visual_options']['layout'] = $context['TPortal']['frontpage_layout'];
                $row['rating'] = array_sum(explode(',', $row['rating']));
                $row['avatar'] = set_avatar_data( array(      
                            'avatar' => $row['avatar'],
                            'email' => $row['email_address'],
                            'filename' => !empty($row['filename']) ? $row['filename'] : '',
                            'id_attach' => $row['id_attach'],
                            'attachement_type' => $row['attachement_type'],
                        )
                )['image'];
                // we need some trick to put featured/sticky on top
                $sortdate = $row['date'];
                if($row['sticky'] == 1) {
                    $sortdate = $row['date'] + $year;
                }
                if($row['featured'] == 1) {
                    $sortdate = $row['date'] + $year + $year;
                }
                $posts[$sortdate.'0' . sprintf("%06s", $row['id'])] = $row;
			}
            unset($tpArticle);
		}
		$total      = count($posts);
        $col1       = ceil($total / 2);
		$col2       = $total - $col1;
		$counter    = 0;

		// divide it
		ksort($posts,SORT_NUMERIC);
		$all = array_reverse($posts);

		foreach($all as $p => $row) {
			if($counter == 0) {
				$context['TPortal']['category']['featured'] = $row;
            }
			else if($counter < $col1 && $counter > 0) {
				$context['TPortal']['category']['col1'][] = $row;
            }
			else if($counter > $col1 || $counter == $col1) {
				$context['TPortal']['category']['col2'][] = $row;
            }
			$counter++;
		}
        break;
    }

	// collect up frontblocks
	$blocks = array('front' => array());
	$blocktype = array('no','userbox','newsbox','statsbox','searchbox','html',
		'onlinebox','themebox','oldshoutbox','catmenu','phpbox','scriptbox','recentbox',
		'ssi','module','rss','sitemap','oldadmin','articlebox','categorybox','tpmodulebox');

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
	$request =  $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_blocks
		WHERE off = 0
		AND bar = 4
		AND '. $access .'
		ORDER BY pos,id ASC'
	);

	$count = array('front' => 0);
	$fetch_articles = array();
	$fetch_article_titles = array();
	$panels = array(4 => 'front');

	if ($smcFunc['db_num_rows']($request) > 0) {
		while($row = $smcFunc['db_fetch_assoc']($request)) {
			// some tests to minimize sql calls
			if($row['type'] == 7) {
				$test_themebox = true;
            }
			elseif($row['type'] == 18) {
				$test_articlebox = true;
				if(is_numeric($row['body'])) {
					$fetch_articles[]=$row['body'];
                }
			}
			elseif($row['type'] == 9) {
				$test_menubox = true;
            }
			elseif($row['type'] == 19) {
				$test_catbox = true;
				if(is_numeric($row['body'])) {
					$fetch_article_titles[] = $row['body'];
                }
			}
			$can_edit = get_perm($row['editgroups'], '');
			$can_manage = allowedTo('tp_blocks');
			if($can_manage) {
				$can_edit = false;
            }

			$blocks[$panels[$row['bar']]][$count[$panels[$row['bar']]]] = array(
				'frame' => $row['frame'],
				'title' => strip_tags($row['title'], '<center>'),
				'type' => $blocktype[$row['type']],
				'body' => $row['body'],
				'visible' => $row['visible'],
				'var1' => $row['var1'],
				'var2' => $row['var2'],
				'var3' => $row['var3'],
				'var4' => $row['var4'],
				'var5' => $row['var5'],
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

	if(count($fetch_articles) > 0) {
		$fetchart = '(art.id='. implode(' OR art.id=', $fetch_articles).')';
    }
	else {
		$fetchart=''; 
    }

	if(count($fetch_article_titles) > 0) {
		$fetchtitles= '(art.category='. implode(' OR art.category=', $fetch_article_titles).')';
    }
	else {
		$fetchtitles='';
    }

    // if a block displays an article
    if(isset($test_articlebox) && $fetchart != '') {
		$context['TPortal']['blockarticles'] = array();
		$request =  $smcFunc['db_query']('', '
			SELECT art.*, var.value1, var.value2, var.value3, var.value4, var.value5, var.value7, var.value8, art.type as rendertype,
				COALESCE(mem.real_name,art.author) as real_name, mem.avatar, mem.posts, mem.date_registered as date_registered, mem.last_login as last_login,
				COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type as attachement_type, var.value9, mem.email_address AS email_address
			FROM {db_prefix}tp_articles as art
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = art.author_id)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = art.author_id AND a.attachment_type !=3)
			LEFT JOIN {db_prefix}tp_variables as var ON (var.id= art.category)
			WHERE ' . $fetchart.'
			AND art.off = 0
			AND ((art.pub_start = 0 AND art.pub_end = 0)
			OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
			OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
			OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
			AND art.category > 0
			AND art.approved = 1
			AND art.category > 0 AND art.category < 9999'
		);
		if($smcFunc['db_num_rows']($request) > 0) {
			while($article = $smcFunc['db_fetch_assoc']($request)) {
				// allowed and all is well, go on with it.
				$context['TPortal']['blockarticles'][$article['id']] = $article;
                $context['TPortal']['blockarticles'][$article['id']]['avatar'] = set_avatar_data( array(      
                            'avatar' => $row['avatar'],
                            'email' => $row['email_address'],
                            'filename' => !empty($row['filename']) ? $row['filename'] : '',
                            'id_attach' => $row['id_attach'],
                            'attachement_type' => $row['attachement_type'],
                        )
                )['image'];

				// sort out the options
				$context['TPortal']['blockarticles'][$article['id']]['visual_options'] = array();
				// since these are inside blocks, some stuff has to be left out
				$context['TPortal']['blockarticles'][$article['id']]['frame'] = 'none';
			}
			$smcFunc['db_free_result']($request);
		}
	}

   // any cat listings from blocks?
    if(isset($test_catbox) && $fetchtitles != '') {
		$request =  $smcFunc['db_query']('', '
			SELECT art.id, art.subject, art.date, art.category, art.author_id as author_id, art.shortname,
			COALESCE(mem.real_name,art.author) as real_name FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			WHERE ' . 	$fetchtitles . '
			AND ((art.pub_start = 0 AND art.pub_end = 0)
			OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
			OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
			OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
			AND art.off = 0
			AND art.category > 0
			AND art.approved = 1'
		);

		if (!isset($context['TPortal']['blockarticle_titles']))
			$context['TPortal']['blockarticle_titles'] = array();

		if ($smcFunc['db_num_rows']($request) > 0) {
			while($row = $smcFunc['db_fetch_assoc']($request)) {
				$context['TPortal']['blockarticle_titles'][$row['category']][$row['date'].'_'.$row['id']] = array(
					'id' => $row['id'],
					'subject' => $row['subject'],
					'shortname' => $row['shortname'] != '' ? $row['shortname'] : $row['id'] ,
					'category' => $row['category'],
					'poster' => '<a href="'.$scripturl.'?action=profile;u='.$row['author_id'].'">'.$row['real_name'].'</a>',
				);
			}
			$smcFunc['db_free_result']($request);
		}
    }
	// get menubox items
	if(isset($test_menubox))
	{
		$context['TPortal']['menu'] = array();
		$request =  $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_variables
			WHERE type = {string:type} ORDER BY value5 ASC',
			array('type' => 'menubox')
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$icon = '';
				if($row['value5'] != -1 && $row['value2'] != '-1')
				{
					$mtype = substr($row['value3'], 0, 4);
					$idtype = substr($row['value3'], 4);
                    if($mtype != 'cats' && $mtype != 'arti' && $mtype != 'head' && $mtype != 'spac') {
						$mtype = 'link';
						$idtype = $row['value3'];
					}
					if($mtype == 'cats')
					{
						if(isset($context['TPortal']['article_categories']['icon'][$idtype]))
							$icon=$context['TPortal']['article_categories']['icon'][$idtype];
					}
					if($mtype == 'head')
					{
						$mtype = 'head';
						$idtype = $row['value1'];
					}
					$menupos = $row['value5'];

					$context['TPortal']['menu'][$row['subtype2']][] = array(
						'id' => $row['id'],
						'menuID' => $row['subtype2'],
						'name' => $row['value1'],
						'pos' => $menupos,
						'type' => $mtype,
						'IDtype' => $idtype,
						'off' => '0',
						'sub' => $row['value4'],
						'icon' => $icon,
						'newlink' => $row['value2'],
						'sitemap' => (in_array($row['id'],$context['TPortal']['sitemap'])) ? true : false,
					);
				}
			}
			$smcFunc['db_free_result']($request);
		}
	}

	// check the panels
	foreach($panels as $p => $panel)
	{
		// any blocks at all?
		if($count[$panel] < 1)
			$context['TPortal'][$panel.'panel'] = 0;

	}

	$context['TPortal']['frontblocks'] = $blocks;

	if (defined('WIRELESS') && WIRELESS)
	{
		$context['TPortal']['single_article'] = false;
		loadtemplate('TPwireless');
		// decide what subtemplate
		$context['sub_template'] = WIRELESS_PROTOCOL . '_tp_frontpage';
	}
}

// do the blocks
function doTPblocks()
{
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

	if(allowedTo('tp_blocks') && (!empty($context['TPortal']['admin_showblocks']) || !isset($context['TPortal']['admin_showblocks'])))
		$access = '1=1';

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
		' . (!empty($context['TPortal']['uselangoption']) ? '{string:lang} IN ( access2 ) OR ' : '') . '
		' . $access2 . ')
		AND ' . $access . '
		ORDER BY bar, pos, id ASC',
		array(
			'bar' => 4,
			'lang' => 'tlang=' . $user_info['language'],
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
				'var1' => $row['var1'],
				'var2' => $row['var2'],
				'var3' => $row['var3'],
				'var4' => $row['var4'],
				'var5' => $row['var5'],
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

	$fetchart = '';
	if(count($fetch_articles) > 0) {
		$fetchart = '(art.id='. implode(' OR art.id=', $fetch_articles).')';
    }

	$fetchtitles = '';
	if(count($fetch_article_titles) > 0) {
		$fetchtitles= '(art.category='. implode(' OR art.category=', $fetch_article_titles).')';
    }

    // if a block displays an article
    if(isset($test_articlebox) && $fetchart != '') {
		$context['TPortal']['blockarticles'] = array();
		$request =  $smcFunc['db_query']('', '
			SELECT art.*, var.value1, var.value2, var.value3, var.value4, var.value5, var.value7, var.value8, art.type as rendertype,
			COALESCE(mem.real_name,art.author) as real_name, mem.avatar, mem.posts, mem.date_registered as date_registered,mem.last_login as last_login,
			COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type as attachement_type, var.value9, mem.email_address AS email_address
			FROM {db_prefix}tp_articles as art
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = art.author_id)
			LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = art.author_id)
			LEFT JOIN {db_prefix}tp_variables as var ON (var.id= art.category)
			WHERE ' . $fetchart. '
			AND art.off = 0
			AND ((art.pub_start = 0 AND art.pub_end = 0)
			OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
			OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
			OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
			AND art.approved = 1
			AND art.category > 0 AND art.category < 9999'
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while($article = $smcFunc['db_fetch_assoc']($request))
			{
				// allowed and all is well, go on with it.
				$context['TPortal']['blockarticles'][$article['id']] = $article;

				// setup the avatar code
				if ($modSettings['avatar_action_too_large'] == 'option_html_resize' || $modSettings['avatar_action_too_large'] == 'option_js_resize')
				{
					$avatar_width = !empty($modSettings['avatar_max_width_external']) ? ' width="' . $modSettings['avatar_max_width_external'] . '"' : '';
					$avatar_height = !empty($modSettings['avatar_max_height_external']) ? ' height="' . $modSettings['avatar_max_height_external'] . '"' : '';
				}
				else
				{
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
			$smcFunc['db_free_result']($request);
		}
	}

   // any cat listings from blocks?
    if(isset($test_catbox) && $fetchtitles != '')
	{
		$request =  $smcFunc['db_query']('', '
			SELECT art.id, art.subject, art.date, art.category, art.author_id AS author_id, art.shortname,
	 		COALESCE(mem.real_name,art.author) as real_name 
            FROM {db_prefix}tp_articles AS art
			LEFT JOIN {db_prefix}members AS mem ON (art.author_id = mem.id_member)
			WHERE  '. 	$fetchtitles . '
			AND art.off = 0
			AND ((art.pub_start = 0 AND art.pub_end = 0)
			OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
			OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
			OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
			AND art.approved = 1'
		);

		if (!isset($context['TPortal']['blockarticle_titles']))
			$context['TPortal']['blockarticle_titles'] = array();

		if ($smcFunc['db_num_rows']($request) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($request))
			{
				$context['TPortal']['blockarticle_titles'][$row['category']][$row['date'].'_'.$row['id']] = array(
					'id' => $row['id'],
					'subject' => $row['subject'],
					'shortname' => $row['shortname']!='' ?$row['shortname'] : $row['id'] ,
					'category' => $row['category'],
					'poster' => '<a href="'.$scripturl.'?action=profile;u='.$row['author_id'].'">'.$row['real_name'].'</a>',
				);
			}
			$smcFunc['db_free_result']($request);
		}
    }
	// get menubox items
	if(isset($test_menubox))
	{
        TPortal_menubox();
	}

	// for tpadmin
	$context['TPortal']['adminleftpanel'] = $context['TPortal']['leftpanel'];
	$context['TPortal']['adminrightpanel'] = $context['TPortal']['rightpanel'];
	$context['TPortal']['admincenterpanel'] = $context['TPortal']['centerpanel'];
	$context['TPortal']['adminbottompanel'] = $context['TPortal']['bottompanel'];
	$context['TPortal']['admintoppanel'] = $context['TPortal']['toppanel'];
	$context['TPortal']['adminlowerpanel'] = $context['TPortal']['lowerpanel'];

	// if admin specifies no blocks, no blocks are shown! likewise, if in admin or tpadmin screen, turn off blocks
	if (in_array($context['TPortal']['action'], array('help', 'moderate', 'theme', 'tpadmin', 'admin', 'ban', 'boardrecount', 'cleanperms', 'detailedversion', 'dumpdb', 'featuresettings', 'featuresettings2', 'findmember', 'maintain', 'manageattachments', 'manageboards', 'managecalendar', 'managesearch', 'membergroups', 'modlog', 'news', 'optimizetables', 'packageget', 'packages', 'permissions', 'pgdownload', 'postsettings', 'regcenter', 'repairboards', 'reports', 'serversettings', 'serversettings2', 'smileys', 'viewErrorLog', 'viewmembers')))
		$in_admin = true;
	if($context['TPortal']['action'] == 'tpmod' && isset($_GET['dl']) && substr($_GET['dl'], 0, 5) == 'admin')
	{
		$in_admin = true;
		$context['current_action'] = 'admin';
	}
	if(($context['user']['is_admin'] && isset($_GET['noblocks'])) || ($context['TPortal']['hidebars_admin_only']=='1' && isset($in_admin)))
		tp_hidebars();

	// check the panels
	foreach($panels as $p => $panel)
	{
		// any blocks at all?
		if($count[$panel] < 1)
			$context['TPortal'][$panel.'panel'] = 0;

		// check the hide setting
		if(!isset($context['TPortal']['not_forum']) && $context['TPortal']['hide_' . $panel . 'bar_forum']==1)
			tp_hidebars($panel);
	}

	$context['TPortal']['blocks'] = $blocks;
}

// TPortal side bar, left or right.
function TPortal_panel($side)
{
	global $context, $scripturl, $settings;

	if(function_exists('ctheme_tportal_panel'))
	{
		ctheme_tportal_panel($side);
		return;
	}

	// decide for $flow
	$flow = $context['TPortal']['block_layout_' . $side];

	$panelside = $paneltype = ($side == 'front' ? 'frontblocks' : 'blocks');

	// $code = '
	// <div class="tp_'.$side.'panel" style="overflow: hidden;">';
/*
$code outputs an empty div in every panel after the div that contains the panel blocks
$code has no closing tag </div>
and gets output by calling return $code; at the end of this function that make the div take the closing tag from TPBlockLayout.template.php.
Takes the closing tag of tptopbarHeader in the case of Top Panel Blocks, the closing tag of tpleftbarHeader in the case of Left Panel Blocks etc.
Also I belive the code below is meant to be the closing tag but because is before return $code; then it's closing tptopbarHeader not $code div.
[code]	// the upshrink routine for blocks
	echo '</div>[/code]
*/

	// set the grid type
	if($flow == 'grid')
	{
		$grid_selected=$context['TPortal']['blockgrid_' . $side];
		if($grid_selected == 'colspan3')
			$grid_recycle = 4;
		elseif($grid_selected == 'rowspan1')
			$grid_recycle = 5;

		$grid_entry = 0;
		// fetch the grids..
		TP_blockgrids();
	}
	// check if we left out the px!!
	if(is_numeric($context['TPortal']['blockwidth_'.$side]))
		$context['TPortal']['blockwidth_'.$side] .= 'px';

	// for the cols, calculate numbers
	if($flow == 'horiz2')
	{
		$flowgrid = array(
			'1' => array(1, 0),
			'2' => array(1, 1),
			'3' => array(2, 1),
			'4' => array(2, 2),
			'5' => array(3, 2),
			'6' => array(3, 3),
			'7' => array(4, 3),
			'8' => array(4, 4),
			'9' => array(5, 4),
			'10' => array(5, 5),
			'11' => array(6, 5),
			'12' => array(6, 6),
			'13' => array(7, 6),
			'14' => array(7, 7),
			'15' => array(8, 7),
			'16' => array(8, 8),
		);
		$switch = ceil(count($context['TPortal'][$panelside][$side]) / 2);
	}
	elseif($flow == 'horiz3')
	{
		$flowgrid = array(
			'1' => array(1, 0, 0),
			'2' => array(1, 1, 0),
			'3' => array(1, 1, 1),
			'4' => array(2, 1, 1),
			'5' => array(2, 2, 1),
			'6' => array(2, 2, 2),
			'7' => array(3, 2, 2),
			'8' => array(3, 3, 2),
			'9' => array(3, 3, 3),
			'10' => array(4, 3, 3),
			'11' => array(4, 4, 3),
			'12' => array(4, 4, 4),
			'13' => array(5, 4, 4),
			'14' => array(5, 5, 4),
			'15' => array(5, 5, 5),
			'16' => array(6, 5, 5),
		);
	}
	elseif($flow == 'horiz4')
	{
		$flowgrid = array(
			'1' => array(1, 0, 0, 0),
			'2' => array(1, 1, 0, 0),
			'3' => array(1, 1, 1, 0),
			'4' => array(1, 1, 1, 1),
			'5' => array(2, 1, 1, 1),
			'6' => array(2, 2, 1, 1),
			'7' => array(2, 2, 2, 1),
			'8' => array(2, 2, 2, 2),
			'9' => array(3, 2, 2, 2),
			'10' => array(3, 3, 2, 2),
			'11' => array(3, 3, 3, 2),
			'12' => array(3, 3, 3, 3),
			'13' => array(4, 3, 3, 3),
			'14' => array(4, 4, 3, 3),
			'15' => array(4, 4, 4, 3),
			'16' => array(4, 4, 4, 4),
		);

	}
	if(in_array($flow, array('horiz2', 'horiz3', 'horiz4')))
	{
		$pad = $context['TPortal']['padding'];
		if($flow == 'horiz2')
			$wh = 50;
		elseif($flow == 'horiz3')
			$wh = 33;
		elseif($flow == 'horiz4')
			$wh = 25;

		echo '<div style="width:100%;"><div class="panelsColumns" align="top" style="' . (isset($wh) ? 'width: '.$wh.'%;' : '' ) . 'padding-right: '.$pad.'px;float:left;">';
	}
	$flowmain = 0;
	$flowsub = 0;
	$bcount = 0;
	$flowcount = isset($context['TPortal'][$panelside][$side]) ? count($context['TPortal'][$panelside][$side]) : 0;
	if(!isset($context['TPortal'][$panelside][$side]))
		$context['TPortal'][$panelside][$side] = array();


	$n = count($context['TPortal'][$paneltype][$side]);
	$context['TPortal'][$panelside][$side] = (array) $context['TPortal'][$panelside][$side];
	foreach ($context['TPortal'][$panelside][$side] as $i => &$block)
	{
		if(!isset($block['frame']))
			continue;

		$theme = $block['frame'] == 'theme';

		// check if a language title string exists
		$newtitle = TPgetlangOption($block['lang'], $context['user']['language']);
		if(!empty($newtitle))
			$block['title'] = $newtitle;

		$use = true;
		// special title links and variables for special types
		switch($block['type']){
			case 'searchbox':
				$mp = '<a class="subject" href="'.$scripturl.'?action=search">'.$block['title'].'</a>';
				$block['title'] = $mp;
				break;
			case 'onlinebox':
				$mp = '<a class="subject"  href="'.$scripturl.'?action=who">'.$block['title'].'</a>';
				$block['title'] = $mp;
				if($block['var1'] == 0)
					$context['TPortal']['useavataronline'] = 0;
				else
					$context['TPortal']['useavataronline'] = 1;
				break;
			case 'userbox':
				if($context['user']['is_logged'])
					$mp = ''.$block['title'].'';
				else
					$mp = '<a class="subject"  href="'.$scripturl.'?action=login">'.$block['title'].'</a>';
				$block['title'] = $mp;
				break;
			case 'statsbox':
				$mp='<a class="subject"  href="'.$scripturl.'?action=stats">'.$block['title'].'</a>';
				$block['title'] = $mp;
				break;
			case 'recentbox':
				$mp = '<a class="subject"  href="'.$scripturl.'?action=recent">'.$block['title'].'</a>';
				$context['TPortal']['recentboxnum'] = $block['body'];
				$context['TPortal']['useavatar'] = $block['var1'];
				if($block['var1'] == '')
					$context['TPortal']['useavatar'] = 1;
				if(!empty($block['var2']))
					$context['TPortal']['recentbox_options'] = explode(',', $block['var2']);
				break;
			case 'scriptbox':
				$block['title'] = '<span class="header">' . $block['title'] . '</span>';
				$context['TPortal']['scriptboxbody'] = $block['body'];
				break;
			case 'phpbox':
				$block['title']='<span class="header">' . $block['title'] . '</span>';
				$context['TPortal']['phpboxbody'] = $block['body'];
				break;
			case 'ssi':
				$block['title'] = '<span class="header">' . $block['title'] . '</span>';
				$context['TPortal']['ssifunction'] = $block['body'];
				break;
			case 'module':
				$block['title'] = '<span class="header">' . $block['title'] . '</span>';
				$context['TPortal']['moduleblock'] = $block['body'];
				$context['TPortal']['modulevar2'] = $block['var2'];
				break;
			case 'themebox':
				$block['title'] = '<span class="header">' . $block['title'] . '</span>';
				$context['TPortal']['themeboxbody'] = $block['body'];
				break;
			case 'newsbox':
				$block['title'] = '<span class="header">' . $block['title'] . '</span>';
				if($context['random_news_line'] == '')
					$use = false;
				break;
			case 'articlebox':
				$block['title'] = '<span class="header">' . $block['title'] . '</span>';
				$context['TPortal']['blockarticle'] = $block['body'];
				break;
			case 'rss':
				$block['title'] = '<span class="header rss">' . $block['title'] . '</span>';
				$context['TPortal']['rss'] = $block['body'];
				$context['TPortal']['rss_notitles'] = $block['var2'];
				$context['TPortal']['rss_utf8'] = $block['var1'];
				$context['TPortal']['rsswidth'] = isset($block['var3']) ? $block['var3'] : '';
				$context['TPortal']['rssmaxshown'] = !empty($block['var4']) ? $block['var4'] : '20';
				break;
			case 'categorybox':
				$block['title'] = '<span class="header">' . $block['title'] . '</span>';
				$context['TPortal']['blocklisting'] = $block['body'];
				$context['TPortal']['blocklisting_height'] = $block['var1'];
				$context['TPortal']['blocklisting_author'] = $block['var2'];
				break;
			case 'tpmodulebox':
				$block['title'] = '<span class="header">' . $block['title'] . '</span>';
				$context['TPortal']['moduleid'] = $block['var1'];
				$context['TPortal']['modulevar2'] = $block['var2'];
				$context['TPortal']['modulebody'] = $block['body'];
				break;
			case 'catmenu':
				$block['title'] = '<span class="header">' . $block['title'] . '</span>';
				$context['TPortal']['menuid'] = is_numeric($block['body']) ? $block['body'] : 0;
				$context['TPortal']['menuvar1'] = $block['var1'];
				$context['TPortal']['menuvar2'] = $block['var2'];
				$context['TPortal']['blockid'] = $block['id'];
				break;
		}
		// render them horisontally
		if($flow == 'horiz')
		{
			$pad = $context['TPortal']['padding'];
			if($i == ($flowcount-1))
				$pad=0;

			echo '<div class="panelsColumnsHorizontally" style="float: left; width: ' . $context['TPortal']['blockwidth_'.$side].';"><div style="padding-right: ' . $pad . 'px; padding-bottom: '.$pad.'px;">';
			call_user_func($context['TPortal']['hooks']['tp_block'], $block, $theme, $side);
			echo '</div></div>';
		}
		// render them horisontally
		elseif(in_array($flow, array('horiz2', 'horiz3', 'horiz4')))
		{
			$pad = $context['TPortal']['padding'];

			if($flow == 'horiz2') {
				$wh = 50;
			}
			elseif($flow == 'horiz3')
			{
					$wh = 33;
			}
			elseif($flow == 'horiz4')
				$wh = 25;

			if(isset($flowgrid) && $flowsub == $flowgrid[$flowcount][$flowmain])
			{
				$flowsub = 0;
				$flowmain++;
				if($flow == 'horiz2' && $flowmain == 1) {$pad = 0;}
				elseif($flow == 'horiz3' && $flowmain == 2) {$pad = 0;$wh = 34;}
                elseif($flow == 'horiz4' && $flowmain == 3) {$pad = 0;}
				echo '</div><div class="panelsColumns" align="top" style="' . (isset($wh) ? 'width: '. $wh.'%;' : '') .  'padding-right: '.$pad.'px;float:left;">';
			}
			call_user_func($context['TPortal']['hooks']['tp_block'], $block, $theme, $side);
		}
		// according to a grid
		elseif($flow == 'grid')
		{
			echo TP_blockgrid($block, $theme, $grid_entry, $side, $grid_entry == ($grid_recycle - 1) ? true : false, $grid_selected);
			$grid_entry++;
			if($grid_recycle == $grid_entry)
				$grid_entry = 0;
			// what if its the last block, but in the middle of the recycle?
			if($i == $n - 1)
			{
				if($grid_entry > 0)
				{
					for($a = $grid_entry; $a < $grid_recycle; $a++)
						echo TP_blockgrid(0, 0, $a, $side, $a == ($grid_recycle-1) ? true : false, $grid_selected,true);

				}
			}
		}
		// or just plain vertically
		else
			call_user_func($context['TPortal']['hooks']['tp_block'], $block, $theme, $side);

		$bcount++;
		$flowsub++;
	}
	if(in_array($flow, array('horiz2', 'horiz3', 'horiz4')))
		echo '</div><p class="clearthefloat"></p></div>';

	// the upshrink routine for blocks
	// echo '</div>
		echo '<script type="text/javascript"><!-- // --><![CDATA[
				function toggle( targetId )
				{
					var state = 0;
					var blockname = "block" + targetId;
					var blockimage = "blockcollapse" + targetId;

					if ( document.getElementById ) {
						target = document.getElementById( blockname );
						if ( target.style.display == "none" ) {
							target.style.display = "";
							state = 1;
						}
						else {
							target.style.display = "none";
							state = 0;
						}

						document.getElementById( blockimage ).src = "'.$settings['tp_images_url'].'" + (state ? "/TPcollapse.png" : "/TPexpand.png");
						var tempImage = new Image();
						tempImage.src = "'.$scripturl.'?action=tpmod;upshrink=" + targetId + ";state=" + state + ";" + (new Date().getTime());

					}
				}
			// ]]></script>';

	// return $code;
}

function tpSetupUpshrinks()
{
	global $context, $settings, $smcFunc;

	$context['tp_panels'] = array();
	if(isset($_COOKIE['tp_panels'])){
		$shrinks = explode(',', $_COOKIE['tp_panels']);
		foreach($shrinks as $sh => $val)
			$context['tp_panels'][] = $val;
	}

	// the generic panel upshrink code
	$context['html_headers'] .= '
	  <script type="text/javascript"><!-- // --><![CDATA[
		' . (count($context['tp_panels']) > 0 ? '
		var tpPanels = new Array(\'' . (implode("','",$context['tp_panels'])) . '\');' : '
		var tpPanels = new Array();') . '
		function togglepanel( targetID )
		{
			var pstate = 0;
			var panel = targetID;
			var img = "toggle_" + targetID;
			var ap = 0;

			if ( document.getElementById ) {
				target = document.getElementById( panel );
				if ( target.style.display == "none" ) {
					target.style.display = "";
					pstate = 1;
					removeFromArray(targetID, tpPanels);
					document.cookie="tp_panels=" + tpPanels.join(",") + "; expires=Wednesday, 01-Aug-2040 08:00:00 GMT";
					document.getElementById(img).src = \'' . $settings['tp_images_url'] . '/TPupshrink.png\';
				}
				else {
					target.style.display = "none";
					pstate = 0;
					tpPanels.push(targetID);
					document.cookie="tp_panels=" + tpPanels.join(",") + "; expires=Wednesday, 01-Aug-2040 08:00:00 GMT";
					document.getElementById(img).src = \'' . $settings['tp_images_url'] . '/TPupshrink2.png\';
				}
			}
		}
		function removeFromArray(value, array){
			for(var x=0;x<array.length;x++){
				if(array[x]==value){
					array.splice(x, 1);
				}
			}
			return array;
		}
		function inArray(value, array){
			for(var x=0;x<array.length;x++){
				if(array[x]==value){
					return 1;
				}
			}
			return 0;
		}
	// ]]></script>';

	$panels = array('Left', 'Right', 'Top', 'Center', 'Lower', 'Bottom');
	$context['TPortal']['upshrinkpanel'] = '';

	if($context['TPortal']['showcollapse'] == 1)
	{
		foreach($panels as $pa => $pan)
		{
			$side = strtolower($pan);
			if($context['TPortal'][$side.'panel'] == 1)
			{
				// add to the panel
				if($pan == 'Left' || $pan == 'Right')
					$context['TPortal']['upshrinkpanel'] .= tp_hidepanel2('tp' . strtolower($pan) . 'barHeader', 'tp' . strtolower($pan) . 'barContainer', strtolower($pan).'-tp-upshrink_description');
				else
					$context['TPortal']['upshrinkpanel'] .= tp_hidepanel2('tp' . strtolower($pan) . 'barHeader', '', strtolower($pan).'-tp-upshrink_description');

			}
		}
	}
	// get user values
	if($context['user']['is_logged'])
	{
		// set some values based on user-prefs
		$result = $smcFunc['db_query']('', '
			SELECT type, value, item
			FROM {db_prefix}tp_data
			WHERE type = {int:type}
			AND id_member = {int:id_mem}',
			array('type' => 2, 'id_mem' => $context['user']['id'])
		);
		if($smcFunc['db_num_rows']($result) > 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($result))
			{
				$context['TPortal']['usersettings']['wysiwyg'] = $row['value'];
			}
			$smcFunc['db_free_result']($result);
		}
		$context['TPortal']['use_wysiwyg'] = (int) $context['TPortal']['use_wysiwyg'];
		$context['TPortal']['show_wysiwyg'] = $context['TPortal']['use_wysiwyg'];

		if ($context['TPortal']['use_wysiwyg'] > 0)
		{
			$context['TPortal']['allow_wysiwyg'] = true;
			if (isset($context['TPortal']['usersettings']['wysiwyg'])) {
				$context['TPortal']['show_wysiwyg'] = (int) $context['TPortal']['usersettings']['wysiwyg'];
			}
		}
		else
		{
			$context['TPortal']['show_wysiwyg'] = $context['TPortal']['use_wysiwyg'];
			$context['TPortal']['allow_wysiwyg'] = false;
		}

		// check that we are not in admin section
		if((isset($_GET['action']) && $_GET['action'] == 'tpadmin') && ((isset($_GET['sa']) && $_GET['sa'] == 'settings') || !isset($_GET['sa'])))
			$in_admin = true;
	}
	// get the cookie for upshrinks
	$context['TPortal']['upshrinkblocks'] = array();
	if(isset($_COOKIE['tp-upshrinks'])){
		$shrinks = explode(',', $_COOKIE['tp-upshrinks']);
		foreach($shrinks as $sh => $val)
			$context['TPortal']['upshrinkblocks'][] = $val;
	}
	return;
}

function TP_blockgrid($block, $theme, $pos, $side, $last = false, $gridtype, $empty = false)
{
	global $context;

	// first, set the table, equal in all grids
	if($pos == 0)
		echo '<div style="width:100%;">';

	if(isset($context['TPortal']['grid'][$gridtype][$pos]['doubleheight']))
		$dh = true;
	else
		$dh = false;

	// render if its not empty
	if(!$empty)
		echo $context['TPortal']['grid'][$gridtype][$pos]['before'] , call_user_func($context['TPortal']['hooks']['tp_block'], $block, $theme, $side, $dh) , $context['TPortal']['grid'][$gridtype][$pos]['after'];
	else
		echo $context['TPortal']['grid'][$gridtype][$pos]['before'] . '&nbsp;' . $context['TPortal']['grid'][$gridtype][$pos]['after'];

	// last..if its the last block,close the table
	if($last)
		echo '<p class="clearthefloat"></p></div>';

}

function TP_blockgrids()
{
	global $context;

	$context['TPortal']['grid'] = array();
	$context['TPortal']['grid']['colspan3'][0] = array('before' => '<div class="gridColumns" align="top" style="padding-bottom:5px;">', 'after' => '</div>');
	$context['TPortal']['grid']['colspan3'][1] = array('before' => '<div><div class="gridColumns" align="top" style="width:32.3%;padding-right:0.7%;padding-bottom: 5px;float:left;">', 'after' => '</div>');
	$context['TPortal']['grid']['colspan3'][2] = array('before' => '<div class="gridColumns" align="top" style="width:32.3%;padding-right:0.7%;padding-bottom: 5px;float:left;">', 'after' => '</div>');
	$context['TPortal']['grid']['colspan3'][3] = array('before' => '<div class="gridColumns" align="top" style="width:34%;padding-bottom: 5px;float:left;">', 'after' => '</div><p class="clearthefloat"></p></div>');

	$context['TPortal']['grid']['rowspan1'][0] = array('before' => '<div class="gridC" align="top" style="width:32.3%;padding-right: 0.7%; padding-bottom: 5px;float:left;">', 'after' => '</div>', 'doubleheight' => true);
	$context['TPortal']['grid']['rowspan1'][1] = array('before' => '<div class="gridC" align="top" style="width:67%;padding-bottom: 5px;float:left;"><div class="gridColumns" align="top" style="width:49%;padding-right: 1%;padding-bottom: 5px;float:left;">', 'after' => '</div>');
	$context['TPortal']['grid']['rowspan1'][2] = array('before' => '<div class="gridColumns" align="top" style="width:50%;padding-bottom: 5px;float:left;">', 'after' => '</div>');
	$context['TPortal']['grid']['rowspan1'][3] = array('before' => '<div class="gridColumns" align="top" style="width:49%;padding-right: 1%;padding-bottom: 5px;float:left;">', 'after' => '</div>');
	$context['TPortal']['grid']['rowspan1'][4] = array('before' => '<div class="gridColumns" align="top" style="width:50%;padding-bottom: 5px;float:left;">', 'after' => '</div><p class="clearthefloat"></p></div>');
}

function doModules()
{
	global $context, $boarddir, $smcFunc;

    // fetch any block render hooks and notifications from tpmodules
	$context['TPortal']['tpmodules'] = array(
		'blockrender' => array(),
		'adminhook' => array(),
		'frontsection' => array(),
	);
	$context['TPortal']['modulepermissions'] = array('tp_settings', 'tp_blocks', 'tp_articles', 'tp_alwaysapproved', 'tp_submithtml', 'tp_submitbbc', 'tp_editownarticle');

	$request = $smcFunc['db_query']('', '
		SELECT id, modulename, blockrender, autoload_run, adminhook,
		frontsection, permissions
		FROM {db_prefix}tp_modules WHERE active = {int:active}',
		array('active' => 1)
	);

	if($smcFunc['db_num_rows']($request) > 0) {
		while($row = $smcFunc['db_fetch_assoc']($request)) {
			if(!empty($row['permissions'])) {
				$all = explode(',', $row['permissions']);
				foreach($all as $one) {
					$real = explode('|', $one);
					$context['TPortal']['modulepermissions'][] = $real[0];
					unset($real);
				}
			}
			if(!empty($row['blockrender'])) {
				$context['TPortal']['tpmodules']['blockrender'][$row['id']] = array(
						'id' => $row['id'],
						'name' => $row['modulename'],
						'function' => $row['blockrender'],
						'sourcefile' => $boarddir .'/tp-files/tp-modules/' . $row['modulename']. '/Sources/'. $row['autoload_run'],
				);
            }
			if(!empty($row['frontsection'])) {
				$context['TPortal']['tpmodules']['frontsection'][$row['id']] = array(
					'id' => $row['id'],
					'name' => $row['modulename'],
					'function' => $row['frontsection'],
					'sourcefile' => $boarddir .'/tp-files/tp-modules/' . $row['modulename']. '/Sources/'. $row['autoload_run'],
				);
            }
		}
		$smcFunc['db_free_result']($request);
	}
}

// TPortal leftblocks
function TPortal_leftbar()
{
	TPortal_sidebar('left');
}

// TPortal centerbar
function TPortal_centerbar()
{
	TPortal_sidebar('center');
}

// TPortal rightbar
function TPortal_rightbar()
{
	TPortal_sidebar('right');
}

function TPortal_menubox()
{

    global $context, $smcFunc;

    $context['TPortal']['menu'] = array();
    $request =  $smcFunc['db_query']('', '
        SELECT *
        FROM {db_prefix}tp_variables
        WHERE type = {string:type}
        ORDER BY subtype + 0 ASC',
        array('type' => 'menubox')
    );
    if($smcFunc['db_num_rows']($request) > 0)
    {
        while ($row = $smcFunc['db_fetch_assoc']($request))
        {
            $icon = '';
            if($row['value5'] < 1)
            {
                $mtype = substr($row['value3'], 0, 4);
                $idtype = substr($row['value3'], 4);
                if($mtype == 'menu') {
                    continue;
                }
                elseif($mtype != 'cats' && $mtype != 'arti' && $mtype != 'head' && $mtype != 'spac') {
                    $mtype = 'link';
                    $idtype = $row['value3'];
                }

                if($mtype == 'cats')
                {
                    if(isset($context['TPortal']['article_categories']['icon'][$idtype]))
                        $icon = $context['TPortal']['article_categories']['icon'][$idtype];
                }
                if($mtype == 'head')
                {
                    $mtype = 'head';
                    $idtype = $row['value1'];
                }
                $menupos = $row['value5'];

                $context['TPortal']['menu'][$row['subtype2']][] = array(
                        'id' => $row['id'],
                        'menuID' => $row['subtype2'],
                        'name' => $row['value1'],
                        'pos' => $menupos,
                        'type' => $mtype,
                        'IDtype' => $idtype,
                        'off' => '0',
                        'sub' => $row['value4'],
                        'icon' => $icon,
                        'newlink' => $row['value2'],
                        'sitemap' => (in_array($row['id'],$context['TPortal']['sitemap'])) ? true : false,
                        );
            }
        }
        $smcFunc['db_free_result']($request);
    }
}

function tpAutoLoadClass($className)
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

function setup_smf_backwards_compat()
{
    global $boarddir, $cachedir, $sourcedir, $db_type;

    define('BOARDDIR', $boarddir);
    define('CACHEDIR', $cachedir);
    define('SOURCEDIR', $sourcedir);
    define('TPVERSION', 'v200');
    if($db_type == 'postgresql') {
        define('PGSQL', true);
    }
    else {
        define('PGSQL', false);
    }
}


?>
