<?php
/****************************************************************************
* tp_upgrade.php															*
*****************************************************************************
* TP version: 1.0 RC2														*
* Software Version:				SMF 2.0										*
* Founder:						Bloc (http://www.blocweb.net)				*
* Developer:					IchBin (ichbin@ichbin.us)					*
* Copyright 2005-2011 by:     	The TinyPortal Team							*
* Support, News, Updates at:  	http://www.tinyportal.net					*
****************************************************************************/

global $settings, $scripturl, $boarddir, $context, $smcFunc, $db_prefix, $modSettings;

$manual = false;
$render = '';

if(!defined('SMF') && file_exists('SSI.php'))
{
	require_once('SSI.php');
	$manual = true;
}
elseif(!defined('SMF'))
	die('<strong>Install Error:</strong> - please verify you put this file the same directory as SMF\'s index.php.');
	
// Make sure we have all the $smcFunc stuff
if (!array_key_exists('db_create_table', $smcFunc))
    db_extend('packages');

// old empty blocks needs "actio=all"
$convertblocks = false;
// Grab the tables so we can check if they exist
$existing_tables = $smcFunc['db_list_tables'](false, '%tp%');

// Are we using UTF8 or not?
$utf8 = !empty($modSettings['global_character_set']) && $modSettings['global_character_set'] === 'UTF-8';

// Why $dp_prefix has the database name prepended in it I don't know. Stripping off the stuff we don't need.
$smf_prefix = trim(strstr($db_prefix, '.'), '.');

if ($manual)
	$render .= '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><head>
		<title>TinyPortal - v1.0 RC2 for SMF2.0</title>
		 <link rel="stylesheet" type="text/css" href="Themes/default/style.css" />
	</head><body>';


$render .= '<div id="hidemenow" style="z-index: 200; margin-bottom: 1em; position: absolute; top: 120px; left: 25%; width: 50%; height: 500px; border: solid 2px #222;background: white;">
<div style="margin: 0; padding: 8px;" class="catbg">Install/Upgrade TinyPortal v1.0 RC2 for SMF 2.0 &copy;2004-2011</div>
	<div class="middletext" style="padding: 1em; overflow: auto;">
		<ul class="normallist" style="line-height: 1.5em;">';

$tables = array(
    'tp_data' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'type', 'type' => 'tinyint', 'size' => 4, 'default' => 0,),
            array('name' => 'id_member', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'value', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'item', 'type' => 'int', 'size' => 11, 'default' => 0,),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id'),),
        ),
    ),
    'tp_settings' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'mediumint', 'size' => 9, 'auto' => true,),
            array('name' => 'name', 'type' => 'text',),
            array('name' => 'value', 'type' => 'text',),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id'),),
        ),
    ),
    'tp_blocks' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'type', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
			array('name' => 'frame', 'type' => 'tinytext',),
			array('name' => 'title', 'type' => 'tinytext',),
			array('name' => 'body', 'type' => 'text',),
			array('name' => 'access', 'type' => 'text',),
			array('name' => 'bar', 'type' => 'tinyint', 'size' => 4,),
			array('name' => 'pos', 'type' => 'int', 'size' => 11,),
			array('name' => 'off', 'type' => 'tinyint', 'size' => 4, 'default' => 0,),
			array('name' => 'visible', 'type' => 'text',),
			array('name' => 'var1', 'type' => 'int', 	'size' => 11,),
			array('name' => 'var2', 'type' => 'int', 'size' => 11,),
			array('name' => 'lang', 'type' => 'text',),
			array('name' => 'access2', 'type' => 'text',),
			array('name' => 'editgroups', 'type' => 'text',),
			array('name' => 'var3', 'type' => 'int', 'size' => 11,),
			array('name' => 'var4', 'type' => 'int', 'size' => 11,),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_variables' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'value1', 'type' => 'text',),
			array('name' => 'value2', 'type' => 'text',),
			array('name' => 'value3', 'type' => 'text',),
			array('name' => 'type', 'type' => 'tinytext',),
			array('name' => 'value4', 'type' => 'text',),
			array('name' => 'value5', 'type' => 'int', 'size' => 11, 'default' => -2,),
			array('name' => 'subtype', 'type' => 'tinytext',),
			array('name' => 'value7', 'type' => 'text',),
			array('name' => 'value8', 'type' => 'text',),
			array('name' => 'subtype2', 'type' => 'int', 'size' => 11, 'default' => 0,),
			array('name' => 'value9', 'type' => 'text',),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_articles' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'date', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'body', 'type' => 'longtext',),
            array('name' => 'intro', 'type' => 'text',),
            array('name' => 'useintro', 'type' => 'tinyint', 'size' => 4, 'default' => 1),
            array('name' => 'category', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'frontpage', 'type' => 'tinyint', 'size' => 4, 'default' => 1,),
            array('name' => 'subject', 'type' => 'text',),
            array('name' => 'author_id', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'author', 'type' => 'text',),
            array('name' => 'frame', 'type' => 'tinytext',),
            array('name' => 'approved', 'type' => 'tinyint', 'size' => 4, 'default' => 1,),
            array('name' => 'off', 'type' => 'tinyint', 'size' => 4, 'default' => 0,),
            array('name' => 'options', 'type' => 'text',),
            array('name' => 'parse', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'comments', 'type' => 'tinyint', 'size' => 4, 'default' => 0,),
            array('name' => 'comments_var', 'type' => 'text',),
            array('name' => 'views', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'rating', 'type' => 'text',),
            array('name' => 'voters', 'type' => 'text',),
            array('name' => 'id_theme', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'shortname', 'type' => 'tinytext',),
            array('name' => 'sticky', 'type' => 'tinyint', 'size' => 4, 'default' => 0,),
            array('name' => 'fileimport', 'type' => 'text'),
            array('name' => 'topic', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'locked', 'type' => 'tinyint', 'size' => 4, 'default' => 0,),
            array('name' => 'illustration', 'type' => 'text',),
            array('name' => 'headers', 'type' => 'text',),
            array('name' => 'type', 'type' => 'tinytext',),
            array('name' => 'global_tag', 'type' => 'text',),
            array('name' => 'featured', 'type' => 'tinyint', 'size' => 4, 'default' => 0,),
            array('name' => 'pub_start', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'pub_end', 'type' => 'int', 'size' => 11, 'default' => 0,),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_dlmanager' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'name', 'type' => 'tinytext',),
            array('name' => 'description', 'type' => 'text',),
            array('name' => 'icon', 'type' => 'text',),
            array('name' => 'category', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'type', 'type' => 'tinytext',),
            array('name' => 'downloads', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'views', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'file', 'type' => 'text',),
            array('name' => 'created', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'last_access', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'filesize', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'parent', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'access', 'type' => 'text',),
            array('name' => 'link', 'type' => 'text',),
            array('name' => 'author_id', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'screenshot', 'type' => 'text',),
            array('name' => 'rating', 'type' => 'text',),
            array('name' => 'voters', 'type' => 'text',),
            array('name' => 'subitem', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'files', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'global_tag', 'type' => 'text',),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_modules' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'version', 'type' => 'tinytext',),
            array('name' => 'modulename', 'type' => 'tinytext',),
            array('name' => 'title', 'type' => 'text',),
            array('name' => 'subquery', 'type' => 'tinytext',),
            array('name' => 'autoload_run', 'type' => 'tinytext',),
            array('name' => 'autoload_admin', 'type' => 'tinytext',),
            array('name' => 'autorun', 'type' => 'tinytext',),
            array('name' => 'autorun_admin', 'type' => 'tinytext',),
            array('name' => 'db', 'type' => 'text',),
            array('name' => 'permissions', 'type' => 'text',),
            array('name' => 'active', 'type' => 'tinyint', 'size' => 4, 'default' => 0),
            array('name' => 'languages', 'type' => 'text',),
            array('name' => 'blockrender', 'type' => 'tinytext',),
            array('name' => 'adminhook', 'type' => 'tinytext',),
            array('name' => 'logo', 'type' => 'tinytext',),
            array('name' => 'tpversion', 'type' => 'tinytext',),
            array('name' => 'smfversion', 'type' => 'tinytext',),
            array('name' => 'description', 'type' => 'text',),
            array('name' => 'author', 'type' => 'tinytext',),
            array('name' => 'email', 'type' => 'tinytext',),
            array('name' => 'website', 'type' => 'tinytext',),
            array('name' => 'profile', 'type' => 'tinytext',),
            array('name' => 'frontsection', 'type' => 'tinytext',),
            array('name' => 'globaltags', 'type' => 'tinytext',),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_dldata' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'bigint', 'size' => 20, 'auto' => true,),
            array('name' => 'views', 'type' => 'bigint', 'size' => 20,),
            array('name' => 'downloads', 'type' => 'bigint', 'size' => 20),
            array('name' => 'item', 'type' => 'int', 'size' => 11),
            array('name' => 'week', 'type' => 'tinyint', 'size' => 4),
            array('name' => 'year', 'type' => 'smallint', 'size' => 6,),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
	'tp_shoutbox' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'value1', 'type' => 'text',),
            array('name' => 'value2', 'type' => 'text',),
            array('name' => 'value3', 'type' => 'text',),
            array('name' => 'type', 'type' => 'tinytext',),
            array('name' => 'value4', 'type' => 'text',),
            array('name' => 'value5', 'type' => 'int', 'size' => 11, 'default' => -2),
            array('name' => 'value6', 'type' => 'text',),
            array('name' => 'value7', 'type' => 'tinyint', 'size' => 4,),
            array('name' => 'value8', 'type' => 'text',),
            array('name' => 'edit', 'type' => 'tinyint', 'size' => 4,),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
	'tp_rates' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'member_id', 'type' => 'int', 'size' => 11),
            array('name' => 'timestamp', 'type' => 'int', 'size' => 11),
            array('name' => 'rate', 'type' => 'tinyint', 'size' => 4),
            array('name' => 'rate_type', 'type' => 'tinytext',),
            array('name' => 'rate_id', 'type' => 'int', 'size' => 11),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_ratestats' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'average', 'type' => 'tinyint', 'size' => 4),
            array('name' => 'rate_type', 'type' => 'tinytext',),
            array('name' => 'rate_id', 'type' => 'int', 'size' => 11),
            array('name' => 'rates', 'type' => 'int', 'size' => 11),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_events' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'id_member', 'type' => 'int', 'size' => 11),
			array('name' => 'date', 'type' => 'int', 'size' => 11),
            array('name' => 'textvariable', 'type' => 'mediumtext',),
            array('name' => 'link', 'type' => 'mediumtext',),
            array('name' => 'description', 'type' => 'mediumtext',),
            array('name' => 'allowed', 'type' => 'mediumtext',),
            array('name' => 'eventid', 'type' => 'int', 'size' => 11, 'default' => null),
            array('name' => 'on', 'type' => 'tinyint', 'size' => 4),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
);

foreach ($tables as $table => $col) {
    if (in_array($smf_prefix . $table, $existing_tables)) {
        $render .= '
        <li>'. $table .' already exists. Updating table if necessary.</li>';
        
        // Change old column names to newer names
        if ($table == 'tp_articles')
			articleChanges();
		elseif ($table == 'tp_dlmanager')
			updateDownLoads();
		elseif ($table == 'tp_data')
			dataTableChanges();
				
        // If utf8 is set alter table to use utf8 character set.
        if ($utf8) {
            $smcFunc['db_query']('', '
                ALTER TABLE {db_prefix}{raw:table}
                CONVERT TO CHARACTER SET utf8',
                array('table' => $table)
            );
        }
        foreach ($col['columns'] as $column) {
        	$smcFunc['db_add_column']('{db_prefix}' . $table, $column);
            
            // If utf8 is set alter column to be utf8 if text or tinytext.
            if ($utf8 && in_array($column['type'], array('text', 'tinytext', 'longtext'))) {
                $smcFunc['db_query']('', '
                    ALTER TABLE {db_prefix}{raw:table}
                    CHANGE {raw:name} {raw:name} {raw:type} CHARACTER SET utf8',
                    array('table' => $table, 'name' => $column['name'], 'type' => $column['type'])
                );
            }
        }
    }
    else {
        $smcFunc['db_create_table']($db_prefix . $table, $col['columns'], $col['indexes'], array(), 'ignore');
        
        if ($utf8) {
            $smcFunc['db_query']('', '
                ALTER TABLE {db_prefix}{raw:table}
                CONVERT TO CHARACTER SET utf8',
                array('table' => $table)
            );
            
            foreach ($col['columns'] as $column) {
                if (!in_array($column['type'], array('text', 'tinytext')))
                    continue;
                    
                $smcFunc['db_query']('', '
                    ALTER TABLE {db_prefix}{raw:table}
                    CHANGE {raw:name} {raw:name} {raw:type}
                    CHARACTER SET utf8 COLLATE utf8_general_ci',
                    array('table' => $table, 'name' => $column['name'], 'type' => $column['type'])
                );
            }
        }
        $render .= '
        <li>'. $table .' table has been created.</li>';
    }    
}

$request = $smcFunc['db_query']('', '
    SELECT * FROM {db_prefix}tp_settings 
    WHERE name = {string:name} LIMIT 1',
    array('name' => 'version')
);

$row = $smcFunc['db_fetch_assoc']($request);

if($row['value'] < 104)
	$convertblocks = true;
/*if($row['value'] < 105)
	$convertmodule = true;*/
if($row['value'] < 1090)
	$convertaccess = true;
	
$smcFunc['db_free_result']($request);

$settings_array = array(
    // KEEP TRACK OF VERSION HERE
    'version' => '1104',
    'padding' => '4',
    'margins' => '2',
    'topbar_align' => 'center',
    'leftbar_width' => '170',
    'rightbar_width' => '170',
    'fixed_width' => '0',
    'use_SSI' => '1',
    'frontpage_limit' => '5',
    'SSI_board' => '1',
    'temaer' => '',
    'temanames' => '',
    'temapaths' => '',
    'showtop' => '1',
    'frontpage_limit_len' => '300',
    'featured_article' => '3',
    'front_type' => 'forum_articles',
    'showforumfirst' => '0',
    'allow_guestnews' => '1',
    'use_wysiwyg' => '1',
    'allowed_membergroups' => '',
    'approved_membergroups' => '',
    'userbox_options' => 'avatar,logged,time,unread,stats,online,stats_all',
    'hide_leftbar_forum' => '0',
    'hide_rightbar_forum' => '0',
    'hide_centerbar_forum' => '0',
    'show_download' => '1',
    'show_gallery' => '0',
    'show_arcade' => '1',
    'dl_total_items' => '0',
    'dl_maxfiles' => '10',
    'dl_allowed_types' => 'zip,rar,doc,jpg,gif,png',
    'dl_max_upload_size' => '2000',
    'dl_totalcats' => '0',
    'dl_totalfiles' => '0',
    'dl_allow_upload' => '1',
    'dl_upload_max' => '2',
    'dl_approve' => '1',
    'dl_approve_groups' => '',
    'hidebars_admin_only' => '1',
    'dl_uploadpath' => 'tp-downloads',
    'dl_showlatest' => '1',
    'frontpage_layout' => '1',
    'opt_wysiwyg' => '1',
    'show_linkmanager' => '0',
    'show_teampage' => '0',
    'sitemap_items' => '3',
    'cat_list' => '1',
    'dl_visual_options' => 'left,right,center,top',
    'dl_fileprefix' => 'K',
    'showstars' => '1',
    'maxstars' => '5',
    'hidebars_profile' => '1',
    'hidebars_pm' => '1',
    'hidebars_memberlist' => '1',
    'dlmanager_theme' => '0',
    'teampage_theme' => '0',
    'linkmanager_theme' => '0',
    'tpgallery_theme' => '0',
    'blocks_edithide' => '0',
    'profile_shouts_hide' => '0',
    'frontblock_type' => 'first',
    'rss_notitles' => '0',
    'hide_editarticle_link' => '0',
    'hide_bottombar_forum' => '0',
    'hide_topbar_forum' => '0',
    'bottombar' => '1',
    'blockwidth_left' => '200',
    'blockwidth_right' => '150',
    'blockwidth_center' => '150',
    'blockwidth_front' => '150',
    'blockwidth_bottom' => '150',
    'blockwidth_top' => '150',
    'blockwidth_lower' => '150',
    'blockgrid_left' => '',
    'blockgrid_right' => '',
    'blockgrid_center' => '',
    'blockgrid_front' => '',
    'blockgrid_bottom' => '',
    'blockgrid_top' => '',
    'blockgrid_lower' => '',
    'block_layout_left' => 'vert',
    'block_layout_right' => 'vert',
    'block_layout_center' => 'vert',
    'block_layout_front' => 'vert',
    'block_layout_bottom' => 'vert',
    'block_layout_top' => 'vert',
    'block_layout_lower' => 'vert',
    'frontpage_visual' => 'left,right,center,top,bottom,lower,header',
    'hidebars_calendar' => '1',
    'hidebars_search' => '1',
    'hidebars_custom' => '',
    'toppanel' => '1',
    'leftpanel' => '1',
    'rightpanel' => '1',
    'centerpanel' => '1',
    'bottompanel' => '1',
    'frontpanel' => '1',
    'lowerpanel' => '1',
    'hide_lowerbar_forum' => '0',
    'articles_comment_captcha' => '1',
    'print_articles' => '1',
    'article_layout' => '1',
    'article_layout_width' => '100%',
    'article_layout_cols' => '1',
    'frontpage_catlayout' => '1',
    'showcollapse' => '1',
    'remove_modulesettings' => '0',
    'front_module' => '',
    'forumposts_avatar' => '1',
    'dl_usescreenshot' => '1',
    'dl_screenshotsizes' => '80,80,200,200,800,800',
    'editorheight' => '400',
    'tagboards' => '1',
    'tagtopics' => '1',
    'blockheight_left' => '',
    'blockheight_right' => '',
    'blockheight_center' => '',
    'blockheight_front' => '',
    'blockheight_bottom' => '',
    'blockheight_top' => '',
    'blockheight_lower' => '',
    'dl_createtopic' => 1,
    'dl_createtopic_boards' => '',
    'art_imagesizes' => '80,40,400,200',
    'dl_showstats' => '1',
    'dl_showfeatured' => '1',
    'dl_introtext' => '',
    'dl_showcategorytext' => '1',
    'dl_featured' => '',
    'dl_wysiwyg' => 'html',
    'oldsidebar' => '1',
    'use_attachment' => '1',
    'frontpage_template' => '',
    'shoutbox_height' => '250',
    'shoutbox_limit' => '5',
    'guest_shout' => '0',
    'shoutbox_usescroll' => '0',
    'shoutbox_scrollduration' => '2000',
    'shoutbox_scrolldelay' => '1000',
    'shoutbox_scrolldirection' => 'vert',
    'shoutbox_scrolleasing' => 'Cubic',
    'show_shoutbox_archive' => '50',
    ///shoutbox settings
    'show_shoutbox_smile' => '1',
    'show_shoutbox_icons' => '1',
    'profile_shouts_hide' => '0',
    'shoutbox_version' => '100',
    'frontpage_topics' => '',
    'use_tpmainmenu' => '1',
    'use_tpgallery' => '0',
    'use_tpblog' => '0',
    'use_tpads' => '0',
    'use_tpfrontpage' => '0',
    'frontpage_title' => '',
    'shoutbox_whisper' => '1',
    'redirectforum' => '1',
    'useroundframepanels' => '0',
    'panelstyle_left' => '0',
    'panelstyle_right' => '0',
    'panelstyle_center' => '0',
    'panelstyle_top' => '0',
    'panelstyle_bottom' => '0',
    'panelstyle_lower' => '0',
    'panelstyle_upper' => '0',
    'panelstyle_front' => '0',
    'admin_showblocks' => '1',
    'uselangoption' => '0',
);
$updates = 0;
$bars = array('leftpanel' => 'leftbar', 'rightpanel' => 'rightbar', 'toppanel' => 'topbar', 'centerpanel' => 'centerbar', 'bottompanel' => 'bottombar', 'lowerpanel' => 'lowerbar');
$barskey = array_keys($bars);

foreach($settings_array as $what => $val)
{
	$request = $smcFunc['db_query']('', '
        SELECT * FROM {db_prefix}tp_settings 
        WHERE name = {string:name}',
        array('name' => $what)
    );
	if($smcFunc['db_num_rows']($request) < 1) {
		$smcFunc['db_insert']('INSERT',
            '{db_prefix}tp_settings',
            array('name' => 'string', 'value' => 'string'),
            array($what, $val),
            array('id')
        );
		$updates++;
	}
	elseif($smcFunc['db_num_rows']($request) > 0 && $what == 'version'){
		$smcFunc['db_query']('', '
            UPDATE {db_prefix}tp_settings 
            SET value = {string:val} 
            WHERE name = {string:name}',
            array('val' => $val, 'name' => $what)
        );
		$render .= '<li>Updated internal version number to '.$val.'</li>';
		$smcFunc['db_free_result']($request);
	}
	elseif($smcFunc['db_num_rows']($request) > 0 && $what == 'userbox_options'){
		$smcFunc['db_query']('', '
            UPDATE {db_prefix}tp_settings 
            SET value = {string:val} 
            WHERE name = {string:name}',
            array('val' => $val, 'name' => $what)
        );
		$smcFunc['db_free_result']($request);
	}
    elseif($smcFunc['db_num_rows']($request) > 0 && in_array($what, $barskey)) {
        $row = $smcFunc['db_fetch_row']($request);
        $val = $row[2];
        $smcFunc['db_query']('', '
            UPDATE {db_prefix}tp_settings
            SET value = {string:val}
            WHERE name = {string:name}',
            array('val' => $val, 'name' => $what)
        );
        $smcFunc['db_query']('', '
            UPDATE {db_prefix}tp_settings
            SET value = {string:val}
            WHERE name = {string:name}',
            array('val' => '0', 'name' => $bars[$what])
        );
    }
	else
		$smcFunc['db_free_result']($request);
}
if($updates > 0)
	$render .= '
    <li>Settings table updated</li>
	<li>Added '.$updates.' new setting(s)</li>';


// convert empty blocks
if($convertblocks)
{
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_blocks 
		SET access2 = {string:access2} 
		WHERE access2 = ""',
		array('access2' => 'actio-allpages')
	);
	$render .= '<li>Updated old blocks</li>';
}

// make sure access2 is comma separated
$smcFunc['db_query']('', '
	UPDATE {db_prefix}tp_blocks 
	SET access2 = REPLACE(access2, "|", ",") 
	WHERE 1'
);
$render .= '<li>Updated access field of blocks</li>';


if($convertblocks)
{
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_variables 
		SET value3 = {string:val3} 
		WHERE value3 = "" 
		AND type = {string:cat}',
		array('val3' => '-1,0', 'cat' => 'category')
	);
	$render .='<li>Updated old categories</li>';
}

// Add the default for blocks and articles settings here
addDefaults();

// Add the changes for articles
articleUpdates();

// make sure TPShout is available
$request = $smcFunc['db_query']('', '
	SELECT id FROM {db_prefix}tp_modules 
	WHERE modulename = {string:name}',
	array('name' => 'TPShout')
);
if($smcFunc['db_num_rows']($request) > 0)
{
	$row = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_modules 
		SET logo = {string:logo}',
		array('logo' => 'tpshoutbox.png')
	);
}
else
{
	$newmod = array(
		'version' => '1.0',
		'modulename' => 'TPShout',	// must be exactly equal to the folder.
		'title' => 'TP Simple Shout', 
		'subquery' => 'shout',	// the subcall that let TP knows which module is running.
		'autoload_run' => 'TPShout.php',
		'autoload_admin' => 'TPShout.php',
		'autorun' => '',
		'autorun_admin' => '',
		'db' => '', 
		'permissions' => 'tp_can_admin_shout|1',	//permiss
		'active' => 1, 
		'languages' => 'english',
		'blockrender' => 'tpshout_fetch',
		'adminhook' => 'tpshout_adminhook',
		'logo' => 'tpshoutbox.png',
		'tpversion' => '1.0',
		'smfversion' => '1.1.x',
		'description' => '[b]TP Simple Shoutbox[/b] is the original shoutbox from v0.9 series of TinyPortal, now converted to a TP module. It allows shout in BBC format, scrolling of shouts, insert of BBC codes and smilies and an admin interface to delete or modify shouts.<br />	',
		'author' => 'IchBin',
		'email' => 'ichbin@ichbin.us',
		'website' => 'http://www.tinyportal.net',
		'profile' => 'tpshout_profile',
		'frontsection' => 'tpshout_frontpage',
		'globaltags' => '',
	);

	require_once($sourcedir . '/Subs-Post.php');
	preparsecode($newmod['description']);

	// ok, insert this into modules table. 
	$smcFunc['db_insert']('INSERT',
		'{db_prefix}tp_modules',
		array(
			'version' => 'string',
			'modulename' => 'string',
			'title' => 'string',
			'subquery' => 'string',
			'autoload_run' => 'string',
			'autoload_admin' => 'string',
			'autorun' => 'string',
			'autorun_admin' => 'string',
			'db' => 'string',
			'permissions' => 'string',
			'active' => 'int',
			'languages' => 'string',
			'blockrender' => 'string',
			'adminhook' => 'string',
			'logo' => 'string',
			'tpversion' => 'string',
			'smfversion' => 'string',
			'description' => 'string',
			'author' => 'string',
			'email' => 'string',
			'website' => 'string',
			'profile' => 'string',
			'frontsection' => 'string',
			'globaltags' => 'string',
		),
		$newmod,
		array('id')
	);
}


/*
// make sure TPGallery is available
$request=itpdb_query("SELECT COUNT(*) FROM " . $tp_prefix . "modules WHERE modulename='TPgallery'");
$row=itpdb_fetch_row($request);
itpdb_free_result($request);

if($row[0]==0)
{
	$newmod=array(
	'version' => '1.0',
	'name' => 'TPgallery',		// must be exactly equal to the folder.
	'title' => 'TP Gallery', 
	'subquery' => 'gallery',	// the subcall that let TP knows which module is running.
	'sourcefile' => 'TPgallery.php',		// The main source file, entry for more source files.
	'blockaccess' => '',		// 
	'permissions' => 'tpgallery_admin|1,tpgallery_moderator|1,tpgallery_upload|0,tpgallery_comment|0,tpgallery_view|0',  //permiss 
	'languages' => 'english',
	'blockrender' => 'tpgallery_blockrender',
	'adminhook' => 'tpgallery_adminhook',
	'logo' => 'tpgallery.png',
	'tpversion' => '1.0',
	'smfversion' => '1.1.x',
	'description' => '[b]TP Gallery[/b] is a new gallery for TP.',
	'author' => 'Bloc',
	'contact' => 'bloc@tinyportal.net',
	'website' => 'http://www.tinyportal.net',
	'profilehook' => 'tpgallery_profile',
	'frontpagehook' => 'tpgallery_frontpage',
	'globaltags' => 'tpgallery_globaltags',
	);

	require_once($sourcedir . '/Subs-Post.php');
	preparsecode($newmod['description']);

	// ok, insert this into modules table. 
	itpdb_query("INSERT INTO " . $tp_prefix . "modules (version,modulename,title,subquery,autoload_run,autoload_admin,
			autorun,autorun_admin,db,permissions,active,languages,blockrender,adminhook,logo,	tpversion,smfversion,	description,
			author,	email, website, profile, frontsection, globaltags) 
			VALUES('$newmod[version]',	'$newmod[name]',	'$newmod[title]','$newmod[subquery]','$newmod[sourcefile]','$newmod[sourcefile]',
			'',	'',	'$newmod[blockaccess]',	'$newmod[permissions]',1,'$newmod[languages]',	'$newmod[blockrender]',	'$newmod[adminhook]',	'$newmod[logo]','$newmod[tpversion]',
			'$newmod[smfversion]',	'$newmod[description]',	'$newmod[author]','$newmod[contact]', '$newmod[website]', '$newmod[profilehook]','$newmod[frontpagehook]','$newmod[globaltags]'
			)");
}
*/
// check if blocks access2 needs converting
if(isset($convertaccess))
{
	$request = $smcFunc['db_query']('', '
		SELECT id ,access2 FROM {db_prefix}tp_blocks WHERE 1'
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		$new = array();
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			unset($new); 
			$new = array();
			$a = explode('|', $row['access2']);
			if(count($a) > 1)
			{
				foreach($a as $b => $what)
				{
					$first = substr($what, 0, 6);
					$second = substr($what, 6);
					$third = explode(',', $second);
					// build new ones
					if(count($third) > 1)
					{
						foreach($third as $t => $tr)
							$new[] = $first.$tr;
					}
					else
						$new[] = $first.$second;
				}
			}
			else
				$new[] = $row['access2'];
			
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_blocks 
				SET access2 = {string:access2} 
				WHERE id = {int:blockid}',
				array(
					'blockid' => $row['id'],
					'access2' => count($new) > 1 ? implode(',', $new) : $new[0],
				)
			);
		}
	}
	$smcFunc['db_free_result']($request);
}


$render .= '</ul>		
		<hr><p>TinyPortal\'s table structure is now installed/updated. </p>
		<b>Thank you for trying out TinyPortal!</b>';
		
if (!$manual)
	$render .= '
		<div style="padding-top: 3em; text-align: center;">
			<a style="font-size: 1.1em; " href="javascript:void(0);" onclick="document.getElementById(\'hidemenow\').style.display = \'none\'; return false;">Remove this window</a>
		</div>';
		
$render .= '
	</div></div>';

if($manual)
	echo $render . '</body></html>';
else
{
	echo $render; 
}

function checkColumn($table, $col, $action)
{
	global $render, $existing_tables, $smcFunc, $smf_prefix;
	
	if (in_array($smf_prefix . $table, $existing_tables))
	{
		$columns = $smcFunc['db_list_columns']('{db_prefix}' . $table);
		
		if(in_array($col, $columns))
			$smcFunc['db_query']('', '
				ALTER TABLE {db_prefix}'. $table .' ' . $action);
				
		$render .= '
		<li>Changed ' . $col . ' in ' . $table . ' table</li>';		
	}	
}

function updateDownLoads()
{
	// Update old column names
	checkColumn('tp_dlmanager', 'authorID', 'CHANGE `authorID` `author_id` int default 0 NOT NULL');
	$render .= '<li>Updated old columns in downloads table</li>';
}

function articleChanges()
{
	global $smcFunc, $render;
	checkColumn('tp_articles', 'parse', ' CHANGE `parse` `parse` SMALLINT DEFAULT 0 NOT NULL');
	checkColumn('tp_articles', 'ID_THEME', 'CHANGE `ID_THEME` `id_theme` smallint(6) default 0 NOT NULL');
	checkColumn('tp_articles', 'authorID', 'CHANGE `authorID` `author_id` int default 0 NOT NULL');
	checkColumn('tp_articles', 'body', 'CHANGE `body` `body` LONGTEXT NULL');
	$render .= '<li>Updated old columns in articles table</li>';		
}
function dataTableChanges()
{
	// Update old column names
	checkColumn('tp_data', 'ID_MEMBER', 'CHANGE `ID_MEMBER` `id_member` int default 0 NOT NULL');
	$render .= '<li>Updated old columns in data table</li>';	
}

function articleUpdates()
{
	global $smcFunc, $render;
	// change to types
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_articles 
		SET type = {string:type}, useintro = {int:useintro} 
		WHERE useintro = -1',
		array('type' => 'php', 'useintro' => 0)
	);
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_articles 
		SET type = {string:type}, useintro = {int:useintro} 
		WHERE useintro = -2',
		array('type' => 'bbc', 'useintro' => 0)
	);
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_articles 
		SET type = {string:type} 
		WHERE useintro = -3',
		array('type' => 'import')
	);
	
	// make sure featured is updated
	$request = $smcFunc['db_query']('', '
		SELECT value FROM {db_prefix}tp_settings 
		WHERE name = {string:name}',
		array('name' => 'featured_article')
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		$row = $smcFunc['db_fetch_assoc']($request);
		$smcFunc['db_free_result']($request);
		if(!empty($row['value']))
		{
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_articles 
				SET featured = 1 
				WHERE id = {int:art_id}',
				array('art_id' => $row['value'])
			);
			$render .='<li>Update featured article</li>';
		}
	}	
}
function addDefaults()
{
	global $smcFunc, $render;

	// add the module server
	$result = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}package_servers 
		WHERE name = {string:name}',
		array('name' => 'TinyPortal')
	);
	if($smcFunc['db_num_rows']($result) > 0)
		$smcFunc['db_free_result']($result);
	else
		$result = $smcFunc['db_insert']('INSERT',
			'{db_prefix}package_servers',
			array('name' => 'text', 'url' => 'text'),
			array('TinyPortal', 'http://www.tinyportal.net/tpmods'),
			array('id_server')
		);
	// Check for blocks in table, if none insert default blocks.
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_dlmanager LIMIT 1'
	);
	if ($smcFunc['db_num_rows']($request) < 1)			
		$smcFunc['db_insert']('INSERT',
			'{db_prefix}tp_dlmanager',
			array('name' => 'string', 'access' => 'string', 'type' => 'string'),
			array('General', '-1,0,1', 'dlcat'),
			array('id')
		);
	
	// Check for blocks in table, if none insert default blocks.
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_blocks LIMIT 1'
	);
	
	if ($smcFunc['db_num_rows']($request) < 1) 
	{
		$blocks = array(
			'online' =>array(
				'type' => 6,
				'frame' => 'theme',
				'title' => 'Online',
				'body' => '',
				'access' => '-1,0,2,3,-2',
				'bar' => 3,
				'pos' => 20,
				'off' => 0,
				'visible' => '0',
				'var1' => 1,
				'var2' => 0,
				'lang' => '',
				'access2' => 'actio=allpages',
				'editgroups' => '-2',
				'var3' => 0,
				'var4' => 0,
			),
			'search' =>array(
				'type' => 4,
				'frame' => 'theme',
				'title' => 'Search',
				'body' => '',
				'access' => '-1,0,1',
				'bar' => 1,
				'pos' => 0,
				'off' => 0,
				'visible' => '',
				'var1' => 0,
				'var2' => 0,
				'lang' => '',
				'access2' => 'actio=allpages',
				'editgroups' => '',
				'var3' => 0,
				'var4' => 0,
			),
			'user' =>array(
				'type' => 1,
				'frame' => 'theme',
				'title' => 'User',
				'body' => '',
				'access' => '-1,0,1',
				'bar' => 1,
				'pos' => 10,
				'off' => 0,
				'visible' => '',
				'var1' => 0,
				'var2' => 0,
				'lang' => '',
				'access2' => 'actio=allpages',
				'editgroups' => '',
				'var3' => 0,
				'var4' => 0,
			),
			'recent' =>array(
				'type' => 12,
				'frame' => 'theme',
				'title' => 'Recent',
				'body' => '10',
				'access' => '-1,0,1',
				'bar' => 2,
				'pos' => 0,
				'off' => 0,
				'visible' => '',
				'var1' => 1,
				'var2' => 0,
				'lang' => '',
				'access2' => 'actio=allpages',
				'editgroups' => '',
				'var3' => 0,
				'var4' => 0,
			),
			'stats' =>array(
				'type' => 3,
				'frame' => 'theme',
				'title' => 'Stats',
				'body' => '10',
				'access' => '-1,0,1',
				'bar' => 2,
				'pos' => 10,
				'off' => 0,
				'visible' => '',
				'var1' => 0,
				'var2' => 0,
				'lang' => '',
				'access2' => 'actio=allpages',
				'editgroups' => '',
				'var3' => 0,
				'var4' => 0,
			),
			'shout' => array(
				'type' => 20,
				'frame' => 'theme',
				'title' => 'Shoutbox',
				'body' => '',
				'access' => '-1,0,1',
				'bar' => 1,
				'pos' => 10,
				'off' => 0,
				'visible' => '',
				'var1' => 1,
				'var2' => 0,
				'lang' => '',
				'access2' => 'actio=allpages',
				'editgroups' => '',
				'var3' => 0,
				'var4' => 0,
			),										
		);	
		
		$smcFunc['db_insert']('ignore',
			'{db_prefix}tp_blocks',
			array(
				'type' => 'int',
				'frame' => 'string',
				'title' => 'string',
				'body' => 'string',
				'access' => 'string',
				'bar' => 'int',
				'pos' => 'int',
				'off' => 'int',
				'visible' => 'string',
				'var1' => 'int',
				'var2' => 'int',
				'lang' => 'string',
				'access2' => 'string',
				'editgroups' => 'string',
				'var3' => 'int',
				'var4' => 'int',
			),
			$blocks,
			array('id')
		);
		$smcFunc['db_free_result']($request);
		$render .= '<li>Added some sample values for some default blocks</li>';	
	}
	
	// Check for date in variables table, if none insert default values.
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_variables LIMIT 1'
	);
	
	if ($smcFunc['db_num_rows']($request) < 1) {
		$vars = array(
			'var1' =>array(
				'value1' => 'Portal features',
				'value2' => '',
				'value3' => '-1,0',
				'type' => 'category',
				'value4' => '',
				'value5' => -2,
				'subtype' => '',
				'value7' => 'catlayout=1|layout=1',
				'value8' => '',
				'subtype2' => 0,
				'value9' => '',
			),
			'var2' =>array(
				'value1' => 'General Articles',
				'value2' => '',
				'value3' => '-1,0',
				'type' => 'category',
				'value4' => '',
				'value5' => -2,
				'subtype' => '',
				'value7' => 'catlayout=1|layout=1',
				'value8' => '',
				'subtype2' => 0,
				'value9' => '',
			),
			'var3' =>array(
				'value1' => 'Demo Articles',
				'value2' => '0',
				'value3' => 'cats1',
				'type' => 'menubox',
				'value4' => '0',
				'value5' => 10,
				'subtype' => '',
				'value7' => '',
				'value8' => '',
				'subtype2' => 0,
				'value9' => '',
			),
		);
		
		$smcFunc['db_insert']('ignore',
			'{db_prefix}tp_variables',
			array(
				'value1' => 'string',
				'value2' => 'string',
				'value3' => 'string',
				'type' => 'string',
				'value4' => 'string',
				'value5' => 'int',
				'subtype' => 'string',
				'value7' => 'string',
				'value8' => 'string',
				'subtype2' => 'int',
				'value9' => 'string',
			),
			$vars,
			array('id')
		);
		$smcFunc['db_free_result']($request);		
		$render .= '<li>Added some sample values to the variables table</li>';
	}					
}

?>