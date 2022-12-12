<?php
/**
 * install.php
 *
 * @package TinyPortal
 * @version 2.2.3
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

define('TP_MINIMUM_PHP_VERSION', '6.9.9');

global $smcFunc, $db_prefix, $modSettings, $existing_tables, $boardurl, $db_type, $boarddir, $render;
$manual = false;
$render = '';

if(!defined('SMF') && file_exists('SSI.php')) {
	require_once('SSI.php');
	$manual = true;
}
elseif(!defined('SMF')) {
	die('<strong>Install Error:</strong> - please verify you put this file the same directory as SMF\'s index.php.');
}

if ((!function_exists('version_compare') || version_compare(TP_MINIMUM_PHP_VERSION, PHP_VERSION, '>'))) {
	die('<strong>Install Error:</strong> - please install a version of php greater than '.TP_MINIMUM_PHP_VERSION);
}

// make sure we have all the $smcFunc stuff
if (!array_key_exists('db_create_table', $smcFunc)) {
    db_extend('packages');
}

// grab the tables so we can check if they exist
$existing_tables = $smcFunc['db_list_tables'](false, '%tp%');
// are we using UTF8 or not?
$utf8 = (bool)( $db_type == 'mysql' && !empty($modSettings['global_character_set']) && $modSettings['global_character_set'] === 'UTF-8' );

// why $db_prefix has the database name prepended in it I don't know. Stripping off the stuff we don't need.
$smf_prefix = trim(strstr($db_prefix, '.'), '.');

global $forum_version;
if(isset($forum_version) && strpos($forum_version, '2.0') !== false) {
    $forumVersion = 'SMF 2.0.x';
}
else {
    $forumVersion = 'SMF 2.1.x';
}

if ($manual) {
	$render .= '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><head>
		<title>TinyPortal - v2.2.3 for '.$forumVersion.'</title>
		 <link rel="stylesheet" type="text/css" href="'. $boardurl . '/Themes/default/css/index.css" />
	</head><body>';
}

$render .= '<div id="hidemenow" style="z-index: 200; margin-bottom: 1em; position: absolute; top: 120px; left: 25%; width: 50%; background: inherit;
-webkit-box-shadow: 5px 5px 40px 0 rgba(0,0,0,0.6); box-shadow: 5px 5px 40px 0 rgba(0,0,0,0.6); border-radius: 12px 12px 0 0;">
<script>
	function closeNav() {
    document.getElementById("hidemenow").style.width = "0px";
    document.getElementById("hidemenow").style.height = "0px";
    document.getElementById("hidemenow").style.overflow = "hidden";
    }
</script>
<div class="cat_bar" style="position:relative;"><a href="javascript:void(0)" style="position:absolute;top:5px;right:5px;font-weight:bold;color:red;" onclick="closeNav()"><img src="' . $boardurl . '/Themes/default/images/tinyportal/TPdelete2.png" alt="*" /></a><h3 class="catbg">Install/Upgrade TinyPortal v2.2.3 for '.$forumVersion.'<h3/></div>
	<div class="windowbg middletext" style="padding: 2em; overflow: auto;">
		<ul class="normallist" style="line-height: 1.7em;">';

$tables = array(
    'tp_articles' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'date', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'body', 'type' => ($db_type == 'mysql' ? 'longtext' : 'text'), 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'intro', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'useintro', 'type' => 'smallint', 'size' => 1, 'default' => 0,),
            array('name' => 'category', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'frontpage', 'type' => 'smallint', 'size' => 1, 'default' => 0,),
            array('name' => 'subject', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '') ),
            array('name' => 'author_id', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'author', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '') ),
            array('name' => 'frame', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '') ),
            array('name' => 'approved', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'off', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'options', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'parse', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'comments', 'type' => 'smallint', 'size' => 4, 'default' => 0,),
            array('name' => 'comments_var', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'views', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'rating', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'voters', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'id_theme', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'shortname', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'sticky', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'fileimport', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'topic', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'locked', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'illustration', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'headers', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'type', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'featured', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'pub_start', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'pub_end', 'type' => 'int', 'size' => 11, 'default' => 0,),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_blocks' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'type', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
			array('name' => 'frame', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'title', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'body', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'bar', 'type' => 'smallint', 'size' => 4, 'default' => 0 ),
			array('name' => 'pos', 'type' => 'int', 'size' => 11, 'default' => 0 ),
            array('name' => 'off', 'type' => 'smallint', 'size' => 1, 'default' => 0 ),
			array('name' => 'visible', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'lang', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'access', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'display', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'settings', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_comments' => array (
        'columns' => array (
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'item_type', 'type' => 'varchar', 'size' => 255, 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'item_id', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'datetime', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'subject', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'comment', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'member_id', 'type' => 'int', 'size' => 11, 'default' => 0,),
        ),
        'indexes' => array (
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_data' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'type', 'type' => 'smallint', 'size' => 4, 'default' => 0,),
            array('name' => 'id_member', 'type' => 'int', 'size' => 11, 'default' => 0, 'old_name' => 'ID_MEMBER'),
            array('name' => 'value', 'type' => 'smallint', 'size' => 6, 'default' => 0,),
            array('name' => 'item', 'type' => 'int', 'size' => 11, 'default' => 0,),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id'),),
        ),
    ),
    'tp_dldata' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'bigint', 'size' => 20, 'auto' => true,),
            array('name' => 'views', 'type' => 'bigint', 'size' => 20, 'default' => 0 ),
            array('name' => 'downloads', 'type' => 'bigint', 'size' => 20, 'default' => 0 ),
            array('name' => 'item', 'type' => 'int', 'size' => 11, 'default' => 0 ),
            array('name' => 'week', 'type' => 'smallint', 'size' => 4, 'default' => 0 ),
            array('name' => 'year', 'type' => 'smallint', 'size' => 6, 'default' => 0 ),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_dlmanager' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'name', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'description', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'icon', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'category', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'type', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'downloads', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'views', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'file', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'created', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'last_access', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'filesize', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'parent', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'access', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'link', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'author_id', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'screenshot', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'rating', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'voters', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'subitem', 'type' => 'int', 'size' => 11, 'default' => 0,),
            array('name' => 'files', 'type' => 'int', 'size' => 11, 'default' => 0,),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_events' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'id_member', 'type' => 'int', 'size' => 11, 'default' => 0),
			array('name' => 'date', 'type' => 'int', 'size' => 11, 'default' => 0),
            array('name' => 'textvariable', 'type' => 'mediumtext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'link', 'type' => 'mediumtext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'description', 'type' => 'mediumtext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'allowed', 'type' => 'mediumtext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'eventid', 'type' => 'int', 'size' => 11, 'default' => 0),
            array('name' => 'on', 'type' => 'smallint', 'size' => 4, 'default' => 0),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_menu' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'name', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'type', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'link', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'parent', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'permissions', 'type' => 'mediumtext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'enabled', 'type' => 'smallint', 'size' => 4, 'default' => 0),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
	'tp_shoutbox' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'shoutbox_id', 'type' => 'int', 'size' => 3, 'default' => 1),
            array('name' => 'member_id', 'type' => 'int', 'size' => 11, 'default' => -2),
            array('name' => 'content', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'time', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'type', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'member_ip', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'member_link', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
            array('name' => 'edit', 'type' => 'smallint', 'size' => 4, 'default' => 0),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_variables' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'int', 'size' => 11, 'auto' => true,),
            array('name' => 'value1', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'value2', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'value3', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'type', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'value4', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'value5', 'type' => 'int', 'size' => 11, 'default' => -2,),
			array('name' => 'subtype', 'type' => 'tinytext', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'value7', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'value8', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
			array('name' => 'subtype2', 'type' => 'int', 'size' => 11, 'default' => 0,),
			array('name' => 'value9', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id')),
        ),
    ),
    'tp_settings' => array(
        'columns' => array(
            array('name' => 'id', 'type' => 'mediumint', 'size' => 9, 'auto' => true,),
            array('name' => 'name', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '') ),
            array('name' => 'value', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '') ),
        ),
        'indexes' => array(
            array('type' => 'primary', 'columns' => array('id'),),
        ),
    ),
);

foreach ($tables as $table => $col) {
    if (in_array($db_prefix . $table, $existing_tables)) {
		// updating the tables if they already exist
        $render .= '
            <li>'. $table .' already exists. Updating table if necessary.</li>';

        // change old column names to newer names
        if ($table == 'tp_articles') {
            articleChanges();
            db_extend('extra');
            if($db_type == 'mysql' && version_compare($smcFunc['db_get_version'](), '5.6', '>=')) {
                $request = $smcFunc['db_query']('','
                        SHOW INDEX FROM {db_prefix}tp_articles WHERE Key_name = \'search\' AND Index_type = \'FULLTEXT\''
                        );
                if($smcFunc['db_num_rows']($request) === 0) {
                    $smcFunc['db_query']('', '
                            CREATE FULLTEXT INDEX search ON
                            {db_prefix}tp_articles (subject, body)'
                            );
                }
                $request = $smcFunc['db_query']('','
                        SHOW INDEX FROM {db_prefix}tp_articles WHERE Key_name = \'search_subject\' AND Index_type = \'FULLTEXT\''
                        );
                if($smcFunc['db_num_rows']($request) === 0) {
                    $smcFunc['db_query']('', '
                            CREATE FULLTEXT INDEX search_subject ON
                            {db_prefix}tp_articles (subject)'
                            );
                }
                $request = $smcFunc['db_query']('','
                        SHOW INDEX FROM {db_prefix}tp_articles WHERE Key_name = \'search_body\' AND Index_type = \'FULLTEXT\''
                        );
                if($smcFunc['db_num_rows']($request) === 0) {
                    $smcFunc['db_query']('', '
                            CREATE FULLTEXT INDEX search_body ON
                            {db_prefix}tp_articles (body)'
                            );
                }
            }
        }
        else if ($table == 'tp_blocks') {
			$column = array('name' => 'settings', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : ''));
            $smcFunc['db_add_column']('{db_prefix}' . $table, $column);
            updateBlocks();
        }
        else if ($table == 'tp_comments') {
            updateComments();
        }
        else if ($table == 'tp_data') {
            dataTableChanges();
        }
        else if ($table == 'tp_dlmanager') {
            updateDownLoads();
        }
        else if ($table == 'tp_shoutbox') {
            updateShoutbox();
        }

        // if utf8 is set alter table to use utf8 character set.
        if ($utf8) {
            $smcFunc['db_query']('', '
                ALTER TABLE {db_prefix}{raw:table}
                CONVERT TO CHARACTER SET utf8',
                array('table' => $table)
            );
        }
        if($db_type == 'mysql') {
            foreach ($col['columns'] as $column) {
                if (!isset($column['old_name']) || !$smcFunc['db_change_column']($db_prefix . $table, $column['old_name'], $column))
                    $smcFunc['db_add_column']('{db_prefix}' . $table, $column);

                // if utf8 is set alter column to be utf8 if text or tinytext.
                if ($utf8 && in_array($column['type'], array('text', 'tinytext', 'longtext'))) {
                    $smcFunc['db_query']('', '
                        ALTER TABLE {db_prefix}{raw:table}
                        CHANGE {raw:name} {raw:name} {raw:type} CHARACTER SET utf8',
                        array('table' => $table, 'name' => $column['name'], 'type' => $column['type'])
                    );
                }
            }
        }
    }
	// creating the tables 
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
    checkTextColumnNull($table);
}

// remove unused database tables
$smcFunc['db_drop_table']('{db_prefix}tp_modules');
$smcFunc['db_drop_table']('{db_prefix}tp_rates');
$smcFunc['db_drop_table']('{db_prefix}tp_ratestats');

// check if we need to convert blocks and access
$request = $smcFunc['db_query']('', '
    SELECT * FROM {db_prefix}tp_settings
    WHERE name = {string:name} LIMIT 1',
    array('name' => 'version')
);

$convertblocks = false;
$convertaccess = false;

$row = $smcFunc['db_fetch_assoc']($request);
if(isset($row['value'])) {
	$version = preg_replace("/[^0-9]/", "", $row['value'] );
    if(substr($version, 0, 3) < 104)
        $convertblocks = true;
    if(substr($version, 0, 3) < 109)
        $convertaccess = true;
}

$smcFunc['db_free_result']($request);

if($convertblocks) {
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_variables
		SET value3 = {string:val3}
		WHERE value3 =\'\'
		AND type = {string:cat}',
		array('val3' => '-1,0', 'cat' => 'category')
	);
	$render .='<li>Updated old categories</li>';
}

if($convertaccess) {
	$request = $smcFunc['db_query']('', '
		SELECT id ,display FROM {db_prefix}tp_blocks WHERE 1=1'
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		$new = array();
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			unset($new);
			$new = array();
			$a = explode('|', $row['display']);
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
				$new[] = $row['display'];

			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_blocks
				SET display = {string:display}
				WHERE id = {int:blockid}',
				array(
					'blockid' => $row['id'],
					'display' => count($new) > 1 ? implode(',', $new) : $new[0],
				)
			);
		}
	$render .='<li>Updated old blocks</li>';
	}
	$smcFunc['db_free_result']($request);
}

// now we process all settings
$settings_array = array(
    // KEEP TRACK OF INTERNAL VERSION HERE
    'version' => '2.2.3',
    'frontpage_title' => '',
    'showforumfirst' => '0',
    'hideadminmenu' => '0',
    'useroundframepanels' => '0',
    'showcollapse' => '1',
    'blocks_edithide' => '0',
    'uselangoption' => '0',
    'use_groupcolor' => '0',
    'maxstars' => '5',
    'showstars' => '1',
    'oldsidebar' => '1',
    'admin_showblocks' => '1',
    'imageproxycheck' => '1',
    'fulltextsearch' => '0',
    'disable_template_eval' => '1',
    'copyrightremoval' => '',
    'image_upload_path'     => $boarddir.'/tp-images/',
    'download_upload_path'  => $boarddir.'/tp-downloads/',
    'blockcode_upload_path' => $boarddir.'/tp-files/tp-blockcodes/',
    // frontpage
    'front_type' => 'forum_articles',
    'frontblock_type' => 'first',
    'frontpage_visual' => 'left,right,center,top,bottom,lower,header',
    'frontpage_layout' => '1',
    'frontpage_catlayout' => '1',
    'frontpage_template' => '',
    'allow_guestnews' => '1',
    'SSI_board' => '1',
    'frontpage_limit' => '5',
    'frontpage_limit_len' => '300',
    'frontpage_topics' => '',
    'forumposts_avatar' => '1',
    'use_attachment' => '0',
    'boardnews_divheader' => 'cat_bar',
    'boardnews_headerstyle' => 'catbg',
    'boardnews_divbody' => 'windowbg noup',
    // article settings
    'use_wysiwyg' => '2',
    'editorheight' => '400',
    'use_dragdrop' => '0',
    'hide_editarticle_link' => '1',
    'print_articles' => '1',
    'allow_links_article_comments' => '1',
    'hide_article_facebook' => '0',
    'hide_article_twitter' => '0',
    'hide_article_reddit' => '0',
    'hide_article_digg' => '0',
    'hide_article_delicious' => '0',
    'hide_article_stumbleupon' => '0',
    'icon_width' => '100',
    'icon_height' => '100',
    'icon_max_size' => '500',
    'art_imagesizes' => '80,40,400,200',
    // Panels
    'hidebars_admin_only' => '1',
    'hidebars_profile' => '1',
    'hidebars_pm' => '1',
    'hidebars_memberlist' => '1',
    'hidebars_search' => '1',
    'hidebars_calendar' => '1',
    'hidebars_custom' => '',
    'padding' => '4',
    'leftbar_width' => '200',
    'rightbar_width' => '230',
    'showtop' => '1',
    'leftpanel' => '1',
    'rightpanel' => '1',
    'toppanel' => '1',
    'centerpanel' => '1',
    'frontpanel' => '1',
    'lowerpanel' => '1',
    'bottompanel' => '1',
    'hide_leftbar_forum' => '0',
    'hide_rightbar_forum' => '0',
    'hide_topbar_forum' => '0',
    'hide_centerbar_forum' => '0',
    'hide_lowerbar_forum' => '0',
    'hide_bottombar_forum' => '0',
    'block_layout_left' => 'vert',
    'block_layout_right' => 'vert',
    'block_layout_top' => 'vert',
    'block_layout_center' => 'vert',
    'block_layout_front' => 'vert',
    'block_layout_lower' => 'vert',
    'block_layout_bottom' => 'vert',
    'blockgrid_left' => 'colspan3',
    'blockgrid_right' => 'colspan3',
    'blockgrid_top' => 'colspan3',
    'blockgrid_center' => 'colspan3',
    'blockgrid_front' => 'colspan3',
    'blockgrid_lower' => 'colspan3',
    'blockgrid_bottom' => 'colspan3',
    'blockwidth_left' => '200',
    'blockwidth_right' => '150',
    'blockwidth_top' => '150',
    'blockwidth_center' => '150',
    'blockwidth_front' => '150',
    'blockwidth_lower' => '150',
    'blockwidth_bottom' => '150',
    'blockheight_left' => '',
    'blockheight_right' => '',
    'blockheight_top' => '',
    'blockheight_center' => '',
    'blockheight_front' => '',
    'blockheight_lower' => '',
    'blockheight_bottom' => '',
    'panelstyle_left' => '0',
    'panelstyle_right' => '0',
    'panelstyle_top' => '0',
    'panelstyle_upper' => '0',
    'panelstyle_center' => '0',
    'panelstyle_front' => '0',
    'panelstyle_lower' => '0',
    'panelstyle_bottom' => '0',
    // Shoutbox
    'show_shoutbox_smile' => '1',
    'show_shoutbox_icons' => '1',
    'shout_allow_links' => '0',
    'shoutbox_usescroll' => '0',
    'shoutbox_scrollduration' => '5',
    'shoutbox_refresh' => '0',
    'shout_submit_returnkey' => '0',
    'shoutbox_limit' => '5',
    'shoutbox_maxlength' => '256',
    'shoutbox_timeformat2' => '%Y-%m-%d, %H:%M:%S',
    'shoutbox_use_groupcolor' => '1',
    'shoutbox_textcolor' => '#000',
    'shoutbox_timecolor' => '#787878',
    'shoutbox_linecolor1' => '#f0f4f7',
    'shoutbox_linecolor2' => '#fdfdfd',
    'profile_shouts_hide' => '0',
    // Other
    'bottombar' => '1',
    'cat_list' => '1,2',
    'featured_article' => '0',
    'redirectforum' => '1',
    'resp' => '0',
    'rss_notitles' => '0',
    'sitemap_items' => '3',
    'temapaths' => '',
    'userbox_options' => 'avatar,logged,time,unread,stats,online,stats_all',
    // Downloads
    'show_download' => '1',
    'dl_allowed_types' => 'zip,rar,doc,docx,pdf,jpg,gif,png',
    'dl_max_upload_size' => '2000',
    'dl_fileprefix' => 'K',
    'dl_usescreenshot' => '1',
    'dl_screenshotsizes' => '80,80,200,200',
    'dl_approve' => '1',
    'dl_createtopic' => 1,
    'dl_createtopic_boards' => '',
    'dl_wysiwyg' => 'html',
    'dl_introtext' => '<p><strong>Welcome to the TinyPortal download manager!</strong></p>
<p><br></p>
<p>TPdownloads is a built-in function for TinyPortal that lets you offer files for your members to browse and download. It works by having the downloadable files placed in categories. These categories have permissions on them, letting you restrict membergroups access level for each category. You may also allow members to upload files, control which membergroups are allowed and what types of files they may upload.<br><br>Admins can access the TPdownloads settings from the menu &quot;TinyPortal &gt; Manage TPdownloads&quot; and select the [Settings] button.<br></p>
<p>If you do not wish to use TPdownloads you can deactivate the function completely by setting the option "Show Downloads" to OFF in the settings. The Downloads menu option will no longer be displayed to your users in the menu when TPdownloads is deactivated.</p>
<p><br></p>
<p>We hope you enjoy using TinyPortal.&nbsp; If you have any problems, please feel free to <a href="https://www.tinyportal.net/index.php">ask us for assistance</a>.<br></p>
<p><br>Thanks!<br>The TinyPortal team</p>',
    'dl_showfeatured' => '1',
    'dl_featured' => '',
    'dl_showlatest' => '1',
    'dl_showstats' => '1',
    'dl_showcategorytext' => '1',
	'dl_limit_length' => '300',
    'dl_visual_options' => 'left,right,center,top',
    'dlmanager_theme' => '0',
    'dl_allow_upload' => '1',
    'dl_approve_groups' => '',
);
$updates = 0;
$bars = array('leftpanel' => 'leftbar', 'rightpanel' => 'rightbar', 'toppanel' => 'topbar', 'centerpanel' => 'centerbar', 'bottompanel' => 'bottombar', 'lowerpanel' => 'lowerbar');
$barskey = array_keys($bars);

$updateSettings = array( 'userbox_options', 'download_upload_path', 'blockcode_upload_path' );
$updateBlockgrid = array( 'blockgrid_left', 'blockgrid_right', 'blockgrid_top', 'blockgrid_center', 'blockgrid_front', 'blockgrid_lower', 'blockgrid_bottom' );
 
// check each setting if it exists, and if not add it
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
		$render .= 'Updated internal version number to '.$val.'<br>';
		$smcFunc['db_free_result']($request);
	}
	elseif($smcFunc['db_num_rows']($request) > 0 && in_array($what, $updateSettings)){
		$smcFunc['db_query']('', '
            UPDATE {db_prefix}tp_settings
            SET value = {string:val}
            WHERE name = {string:name}',
            array('val' => $val, 'name' => $what)
        );
		$smcFunc['db_free_result']($request);
	}
	elseif($smcFunc['db_num_rows']($request) > 0 && in_array($what, $updateBlockgrid)){
		$smcFunc['db_query']('', '
            UPDATE {db_prefix}tp_settings
            SET value = {string:val}
            WHERE name = {string:name}
			AND value = \'\'',
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
	else {
		$smcFunc['db_free_result']($request);
    }
}
if($updates > 0) {
	$render .= 'Added '.$updates.' new setting(s)<br>';
}

// convert settings from "" to 0 for PHP8
$checkboxes	= array();
$checkboxes = array_merge($checkboxes, array('imageproxycheck', 'admin_showblocks', 'oldsidebar', 'disable_template_eval', 'fulltextsearch', 'hideadminmenu', 'useroundframepanels', 'showcollapse', 'blocks_edithide', 'uselangoption', 'use_groupcolor', 'showstars'));
$checkboxes = array_merge($checkboxes, array('allow_guestnews', 'forumposts_avatar', 'use_attachment'));
$checkboxes = array_merge($checkboxes, array('use_wysiwyg', 'use_dragdrop', 'hide_editarticle_link', 'print_articles', 'allow_links_article_comments', 'hide_article_facebook', 'hide_article_twitter', 'hide_article_reddit', 'hide_article_digg', 'hide_article_delicious', 'hide_article_stumbleupon'));
$checkboxes = array_merge($checkboxes, array('hidebars_admin_only', 'hidebars_profile', 'hidebars_pm', 'hidebars_memberlist', 'hidebars_search', 'hidebars_calendar'));

$updates	= 0;

foreach($checkboxes as $check) {
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_settings
		WHERE name = {string:name} LIMIT 1',
		array('name' => $check)
	);

	$row = $smcFunc['db_fetch_assoc']($request);
	if(isset($row['value']) && ($row['value'] == "")) {
		$updates++;
		
		$smcFunc['db_query']('', '
            UPDATE {db_prefix}tp_settings
            SET value = {string:val}
            WHERE name = {string:name}',
            array('val' => 0, 'name' => $check)
        );
	}
	$smcFunc['db_free_result']($request);
}

if($updates > 0) {
	$render .= 'Updated '.$updates.' setting(s)<br>';
}

// remove unused settings from settings table
$delete_settings_array = array(
    'allowed_membergroups',
    'approved_membergroups',
    'article_layout',
    'article_layout_width',
    'article_layout_cols',
    'articles_comment_captcha',
    'dl_maxfiles',
    'dl_total_items',
    'dl_totalcats',
    'dl_totalfiles',
    'dl_upload_max',
    'dl_uploadpath',
    'fixed_width',
    'front_module',
    'guest_shout',
    'hide_article_google',
    'linkmanager_theme',
    'margins',
    'opt_wysiwyg',
    'shoutbox_height',
    'shoutbox_layout',
    'shoutbox_scrolldelay',
    'shoutbox_scrolldirection',
    'shoutbox_scrolleasing',
    'shoutbox_stitle',
    'shoutbox_version',
    'shoutbox_whisper',
    'show_arcade',
    'show_gallery',
    'show_linkmanager',
    'show_shoutbox_archive',
    'show_teampage',
    'tagboards',
    'tagtopics',
    'teampage_theme',
    'temaer',
    'temanames',
    'topbar_align',
    'tpgallery_theme',
    'use_SSI',
    'use_tpads',
    'use_tpblog',
    'use_tpfrontpage',
    'use_tpgallery',
    'use_tpmainmenu',
	'shoutbox_timeformat',
);
$deletes = 0;

foreach($delete_settings_array as $what)
{
	$request = $smcFunc['db_query']('', '
        SELECT * FROM {db_prefix}tp_settings
        WHERE name = {string:name}',
        array('name' => $what)
    );
	if($smcFunc['db_num_rows']($request) > 0) {
		$smcFunc['db_query']('', '
        DELETE FROM {db_prefix}tp_settings
        WHERE name = {string:name}',
        array('name' => $what)
    );
		$deletes++;
	}
	else
		$smcFunc['db_free_result']($request);
}
if($deletes > 0)
	$render .= 'Removed '.$deletes.' old setting(s)<br>';

// add the default for blocks and articles settings here
addDefaults();

// add the changes for articles
if($db_type == 'mysql') {
    articleUpdates();
}

$render .= '</ul>
		<hr><p>TinyPortal\'s table structure is now installed/updated. </p>
		<b>Thank you for trying out TinyPortal!</b>';

if (!$manual)
	$render .= '
		<div style="padding-top: 3em; padding-right: 50px; text-align: center;">
			<a class="button_submit" style="font-size: 1.2em; display: block; width: 250px; padding: 1em;margin:0 auto;" href="'.$scripturl.'?action=tpadmin">Redirect to TP admin</a>
		</div>';

$render .= '
	</div></div>';

if($manual)
	echo $render . '</body></html>';
else
{
	echo $render;
}

function articleChanges()
{
	global $smcFunc, $render, $db_type;
    $smcFunc['db_change_column']('{db_prefix}tp_articles', 'parse', array( 'name' => 'parse', 'type' => 'smallint', 'size' => 6, 'default' => '0'));
    $smcFunc['db_change_column']('{db_prefix}tp_articles', 'ID_THEME', array( 'name' => 'id_theme', 'type' => 'smallint', 'size' => 6, 'default' => '0'));
    $smcFunc['db_change_column']('{db_prefix}tp_articles', 'authorID', array( 'name' => 'author_id', 'type' => 'int', 'size' => 11, 'default' => '0'));
    $smcFunc['db_change_column']('{db_prefix}tp_articles', 'body', array( 'name' => 'body', 'type' => ($db_type == 'mysql' ? 'longtext' : 'text'), 'default' => ($db_type == 'mysql' ? null : '')));
	$render .= 'Processed column definitions articles table<br>';
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
		}
	}
}

function updateBlocks()
{
	global $smcFunc, $render, $db_type;

    $smcFunc['db_change_column']('{db_prefix}tp_blocks', 'access2', array( 'name' => 'display', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '')));

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_blocks
		SET display = {string:display}
		WHERE display = \'\'',
		array( 'display' => 'allpages')
	);

    // make sure display is comma separated, change | to , in display field 
    $smcFunc['db_query']('', '
        UPDATE {db_prefix}tp_blocks
        SET display = REPLACE(display, \'|\', \',\')
        WHERE 1=1'
    );

    // remove action= from display field of blocks<
    $smcFunc['db_query']('', '
        UPDATE {db_prefix}tp_blocks
        SET display = REPLACE(display, \'actio=\', \'\')
        WHERE 1=1'
    );

    // update block order
    $request =  $smcFunc['db_query']('', '
        SELECT id, pos, bar
        FROM {db_prefix}tp_blocks
        WHERE 1=1',
        array (

        )
    );

    $data   = array();
    if($smcFunc['db_num_rows']($request) > 0) {
        while($row = $smcFunc['db_fetch_assoc']($request)) {
            $data[] = $row;
        }

        $pos  = array_column($data, 'pos');
        $bar = array_column($data, 'bar');

        array_multisort($bar, SORT_ASC, $pos, SORT_ASC, $data);

        $newPos = 0;
        $oldBar = null;

        foreach($data as $row) {
            if($row['bar'] != $oldBar) {
                $newPos = 0;
                $oldBar = $row['bar'];
            }
            $smcFunc['db_query']('', '
                UPDATE {db_prefix}tp_blocks
                SET pos = {int:pos}
                WHERE id = {int:id}',
                array(
                    'id'    => $row['id'],
                    'pos'   => $newPos++,
                )
            );
        }
    }
	$smcFunc['db_free_result']($request);
    $smcFunc['db_remove_column']('{db_prefix}tp_blocks', 'editgroups');

	// update Shoutbox blocks to new settings for 2.1.x
	$request = $smcFunc['db_query']('', '
		SELECT value FROM {db_prefix}tp_settings
		WHERE name = {string:value} LIMIT 1',
		array('value' => 'shoutbox_height' )
	);
	if($smcFunc['db_num_rows']($request) > 0) {
		$row = $smcFunc['db_fetch_assoc']($request);
		$shoutbox_height = $row['value'];
	}

	$request = $smcFunc['db_query']('', '
		SELECT value FROM {db_prefix}tp_settings
		WHERE name = {string:value} LIMIT 1',
		array('value' => 'shoutbox_layout' )
	);
	if($smcFunc['db_num_rows']($request) > 0) {
		$row = $smcFunc['db_fetch_assoc']($request);
		$shoutbox_layout = $row['value'];
	}

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}tp_blocks
		SET type = {string:type}, body = {int:body}, settings = {string:settings}
		WHERE type = 20',
		array('type' => '8', 'body' => 0, 'settings' => '{"var1":"1","var2":"1","var3":"' .(!empty($shoutbox_layout) ? $shoutbox_layout : '0'). '","var4":"' .(!empty($shoutbox_height) ? $shoutbox_height : '250'). '","var5":"99"}')
	);

	$settingUpdates = array (
		'1'		=> array( 'var5' => 'panelstyle' ),																										// User
		'2'		=> array( 'var5' => 'panelstyle' ),																										// News
		'3'		=> array( 'var5' => 'panelstyle' ),																										// Stats
		'4'		=> array( 'var5' => 'panelstyle' ),																										// Search
		'5'		=> array( 'var5' => 'panelstyle' ),																										// BBC
		'6'		=> array( 'var1' => 'useavatar', 'var5' => 'panelstyle' ),																				// Online
		'7'		=> array( 'var5' => 'panelstyle' ),																										// Theme
		'8'		=> array( 'var2' => 'shoutbox_id', 'var3' => 'shoutbox_layout', 'var4' => 'shoutbox_height', 'var5' => 'panelstyle' ), 					// Shoutbox
		'9'		=> array( 'var1' => 'style', 'var5' => 'panelstyle' ),																					// Menu
		'10'	=> array( 'var5' => 'panelstyle' ),																										// Php
		'11'	=> array( 'var5' => 'panelstyle' ),																										// Script
		'12'	=> array( 'var1' => 'useavatar', 'var2' => 'boards', 'var3' => 'include', 'var4' => 'length', 'var5' => 'panelstyle' ),					// Recent Topics
		'13'	=> array( 'var5' => 'panelstyle' ),																										// SSI
		'14'	=> array( 'var5' => 'panelstyle' ),																										// Module: Downloads/stats
		'15'	=> array( 'var1' => 'utf', 'var2' => 'showtitle', 'var3' => 'maxwidth', 'var4' => 'maxshown', 'var5' => 'panelstyle' ),					// RSS
		'16'	=> array( 'var5' => 'panelstyle' ),																										// Sitemap
		'18'	=> array( 'var5' => 'panelstyle' ),																										// Article
		'19'	=> array( 'var1' => 'block_height', 'var2' => 'block_author', 'var5' => 'panelstyle' ),													// Categories
	);

	$request = $smcFunc['db_query']('', '
		SELECT id, type, settings FROM {db_prefix}tp_blocks
		WHERE 1=1',
	);

	if($smcFunc['db_num_rows']($request) > 0) {
		while($row = $smcFunc['db_fetch_assoc']($request)) {
			if(array_key_exists($row['type'], $settingUpdates)) {
				$type		= $row['type'];
				$original	= json_decode($row['settings'], true);
				$updated	= array();
				foreach($original as $k => $v) {
					if(array_key_exists($k, $settingUpdates[$type])) {
						$key = $settingUpdates[$type][$k];
						$updated[$key] = $v;
					}
				}
				if(!empty($updated)) {
					$smcFunc['db_query']('', '
						UPDATE {db_prefix}tp_blocks
						SET settings = {string:settings}
						WHERE id = {int:id}',
						array( 'id' => $row['id'], 'settings' => json_encode($updated) )
					);
				}
			}
		}
	}

	$render .= 'Processed existing blocks<br>';
}

function updateComments()
{
	global $smcFunc, $render, $db_type;

    // move comments from variables to comments table
    $request =  $smcFunc['db_query']('', '
        SELECT var.*
        FROM {db_prefix}tp_variables AS var
        WHERE var.type = {string:type}',
        array (
            'type' => 'article_comment',
        )
    );

    if($smcFunc['db_num_rows']($request) > 0) {
        while($row = $smcFunc['db_fetch_assoc']($request)) {
          $smcFunc['db_insert']('INSERT',
                '{db_prefix}tp_comments',
                array (
                    'item_id'   => 'int',
                    'item_type' => 'string',
                    'subject'   => 'string',
                    'datetime'  => 'int',
                    'comment'   => 'string',
                    'member_id' => 'int'
                ),
                array (
                    $row['value5'],
                    'article_comment',
                    $row['value1'],
                    $row['value4'],
                    $row['value2'],
                    $row['value3'],
                ),
                array ('id')
            );
        }

		// remove article comments from variables table
		$request =  $smcFunc['db_query']('', '
			DELETE FROM {db_prefix}tp_variables
			WHERE type = {string:type}',
			array (
				'type' => 'article_comment',
			)
		);
	$render .= 'Processed comments table<br>';
    }
}

function dataTableChanges()
{
	global $smcFunc, $render;
	// update column names tp_data
    $smcFunc['db_change_column']('{db_prefix}tp_data', 'ID_MEMBER', array( 'name' => 'id_member', 'type' => 'int', 'size' => 11, 'default' => '0'));
	$render .= 'Processed column names data table<br>';
}

function updateDownLoads()
{
	global $smcFunc, $render;
	// update column names tp_dlmanager
    $smcFunc['db_change_column']('{db_prefix}tp_dlmanager', 'authorID', array( 'name' => 'author_id', 'type' => 'int', 'size' => 11, 'default' => '0'));
	$render .= 'Processed column names downloads table<br>';
}

function updateShoutbox()
{
	global $smcFunc, $render, $db_type;

    $smcFunc['db_change_column']('{db_prefix}tp_shoutbox', 'value1', array( 'name' => 'content', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '' )));
    $smcFunc['db_change_column']('{db_prefix}tp_shoutbox', 'value2', array( 'name' => 'time', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '' )));
    $smcFunc['db_change_column']('{db_prefix}tp_shoutbox', 'value3', array( 'name' => 'member_link', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '' )));
    $smcFunc['db_change_column']('{db_prefix}tp_shoutbox', 'value4', array( 'name' => 'member_ip', 'type' => 'text', 'default' => ($db_type == 'mysql' ? null : '' )));
    $smcFunc['db_change_column']('{db_prefix}tp_shoutbox', 'value5', array( 'name' => 'member_id', 'type' => 'int', 'size' => 11, 'default' => '-2' ));
    $smcFunc['db_add_column']('{db_prefix}tp_shoutbox', array('name' => 'shoutbox_id', 'type' => 'int', 'size' => 3, 'default' => '1' ));
    $smcFunc['db_remove_column']('{db_prefix}tp_shoutbox', 'value6');
    $smcFunc['db_remove_column']('{db_prefix}tp_shoutbox', 'value7');
    $smcFunc['db_remove_column']('{db_prefix}tp_shoutbox', 'value8');
    $smcFunc['db_remove_column']('{db_prefix}tp_shoutbox', 'sticky');
    $smcFunc['db_remove_column']('{db_prefix}tp_shoutbox', 'sticky_layout');
    $smcFunc['db_remove_column']('{db_prefix}tp_shoutbox', 'sitcky');
    $smcFunc['db_remove_column']('{db_prefix}tp_shoutbox', 'sitcky_layout');

	$render .= 'Processed column names shoutbox table<br>';
}

function addDefaults()
{
	global $smcFunc, $render, $boardurl;

	// remove the module server
	$result = $smcFunc['db_query']('', '
		DELETE FROM {db_prefix}package_servers
		WHERE name = {string:name}',
		array(
			'name' => 'TinyPortal',
		)
	);

	// check for blocks in table, if none insert default.
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_blocks LIMIT 1'
	);

	if ($smcFunc['db_num_rows']($request) < 1) {
        $blocks = array(
            'search' =>array(
                'type' => 4,
                'frame' => 'theme',
                'title' => 'Search',
                'body' => '',
                'bar' => 1,
                'pos' => 0,
                'off' => 0,
                'visible' => '',
                'lang' => '',
                'access' => '-1,0,1,2,3',
                'display' => 'allpages',
                'settings' => json_encode( array ('panelstyle' => 99 ) ),
            ),
            'user' =>array(
                'type' => 1,
                'frame' => 'theme',
                'title' => 'User',
                'body' => '',
                'bar' => 1,
                'pos' => 1,
                'off' => 0,
                'visible' => '',
                'lang' => '',
                'access' => '-1,0,1,2,3',
                'display' => 'allpages',
                'settings' => json_encode( array ('panelstyle' => 99 ) ),
            ),
            'shout' => array(
                'type' => 8,
                'frame' => 'theme',
                'title' => 'Shoutbox',
                'body' => '',
                'bar' => 1,
                'pos' => 2,
                'off' => 0,
                'visible' => '',
                'lang' => '',
                'access' => '-1,0,1,2,3',
                'display' => 'allpages',
                'settings' => json_encode( array ('panelstyle' => 99, 'shoutbox_id' => 1, 'shoutbox_layout' => 0, 'shoutbox_height' => 250 ) ),
            ),
            'recent' =>array(
                'type' => 12,
                'frame' => 'theme',
                'title' => 'Recent',
                'body' => '10',
                'bar' => 2,
                'pos' => 0,
                'off' => 0,
                'visible' => '',
                'lang' => '',
                'access' => '-1,0,1,2,3',
                'display' => 'allpages',
                'settings' => json_encode( array ('panelstyle' => 99, 'useavatar' => 1, 'boards' => '', 'include' => 0, 'length' => 25  ) ),
            ),
            'stats' =>array(
                'type' => 3,
                'frame' => 'theme',
                'title' => 'Stats',
                'body' => '10',
                'bar' => 2,
                'pos' => 1,
                'off' => 0,
                'visible' => '',
                'lang' => '',
                'access' => '-1,0,1,2,3',
                'display' => 'allpages',
                'settings' => json_encode( array ('panelstyle' => 99 ) ),
            ),
            'online' =>array(
                'type' => 6,
                'frame' => 'theme',
                'title' => 'Online',
                'body' => '',
                'bar' => 3,
                'pos' => 0,
                'off' => 0,
                'visible' => '0',
                'lang' => '',
                'access' => '-1,0,1,2,3',
                'display' => 'allpages',
                'settings' => json_encode( array ('panelstyle' => 99, 'useavatar' => 0 ) ),
            ),
        );

        $smcFunc['db_insert']('INSERT',
            '{db_prefix}tp_blocks',
            array(
                'type' => 'int',
                'frame' => 'string',
                'title' => 'string',
                'body' => 'string',
                'bar' => 'int',
                'pos' => 'int',
                'off' => 'int',
                'visible' => 'string',
                'lang' => 'string',
                'access' => 'string',
                'display' => 'string',
                'settings' => 'string',
            ),
            $blocks,
            array('id')
        );
        $smcFunc['db_free_result']($request);
		$render .= '<li>Added sample blocks</li>';
	}

    $changes    = array();
    $columns    = $smcFunc['db_list_columns']('{db_prefix}tp_blocks');
    foreach($columns as $id => $name) {
        switch($name) {
            case 'var1':
            case 'var2':
            case 'var3':
            case 'var4':
            case 'var5':
                $changes[] = $name;
                break;
            default:
                break;
        }
    }

    if(is_array($changes) && (count($changes) > 0)) {
        $str = implode(', ', $changes);
        // update the blocks table columns if needed.
        $request = $smcFunc['db_query']('', '
            SELECT id, '.$str.' FROM {db_prefix}tp_blocks WHERE 1=1'
        );
        if($smcFunc['db_num_rows']($request) != 0) {
            while($row = $smcFunc['db_fetch_assoc']($request)) {
                $id     = array_shift($row);
                foreach(array('var1', 'var2', 'var3', 'var4', 'var5') as $key) {
                    if(!array_key_exists($key, $row)) {
                        $row[$key] = "0";
                    }
                }
                $data   = json_encode($row);
                $smcFunc['db_query']('', 'UPDATE {db_prefix}tp_blocks
                        SET settings = {string:data}
                        WHERE id = {int:id}',
                        array( 'data' => $data, 'id' => $id )
                        );
            }
            $smcFunc['db_free_result']($request);
        }

        foreach($changes as $column) {
            $smcFunc['db_remove_column']('{db_prefix}tp_blocks', $column);
        }
    }

	// check for categories in downloads table, if none insert default.
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_dlmanager LIMIT 1'
	);
	if ($smcFunc['db_num_rows']($request) < 1) {
		$smcFunc['db_insert']('INSERT',
			'{db_prefix}tp_dlmanager',
			array('name' => 'string', 'icon' => 'string', 'access' => 'string', 'type' => 'string'),
			array('General', ''.$boardurl.'/tp-downloads/icons/folder.png' ,'-1,0,1', 'dlcat'),
			array('id')
		);
		$smcFunc['db_free_result']($request);
		$render .= '<li>Added sample download categories</li>';
    }

	// check for data in variables table, if none insert default values.
	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_variables LIMIT 1'
	);

	if ($smcFunc['db_num_rows']($request) < 1) {
		$vars = array(
			'var1' =>array(
				'value1' => 'Portal features',
				'value2' => '0',
				'value3' => '-1,0,2,3',
				'type' => 'category',
				'value4' => '',
				'value5' => -2,
				'subtype' => '',
				'value7' => 'sort=date|sortorder=desc|articlecount=5|layout=1|catlayout=1|showchild=0|leftpanel=1|rightpanel=1|toppanel=1|centerpanel=1|lowerpanel=1|bottompanel=1',
				'value8' => 'Features',
				'subtype2' => 0,
				'value9' => '',
			),
			'var2' =>array(
				'value1' => 'General Articles',
				'value2' => '0',
				'value3' => '-1,0,2,3',
				'type' => 'category',
				'value4' => '',
				'value5' => -2,
				'subtype' => 'General',
				'value7' => 'sort=date|sortorder=desc|articlecount=5|layout=1|catlayout=1|showchild=0|leftpanel=1|rightpanel=1|toppanel=1|centerpanel=1|lowerpanel=1|bottompanel=1',
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

		$smcFunc['db_insert']('INSERT',
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
		$render .= '<li>Added sample article categories</li>';
	}
}

function checkTextColumnNull($table)
{
	global $smcFunc, $db_prefix, $db_type;

    if($db_type != 'mysql') {
        return;
    }

	$columns = $smcFunc['db_list_columns']('{db_prefix}' . $table, true);
    foreach($columns as $column) {
        if(array_key_exists('name', $column) && array_key_exists('type', $column) && in_array($column['type'], array('tinytext', 'text', 'mediumtext', 'longtext'))) {
		    $smcFunc['db_query']('', 'ALTER TABLE {db_prefix}'. $table . ' CHANGE `' . $column['name'] . '` `' . $column['name'] . '` '. $column['type'] .' DEFAULT NULL');
        }
    }
}

?>
