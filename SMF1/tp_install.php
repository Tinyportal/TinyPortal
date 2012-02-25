<?php

global $settings, $scripturl, $boarddir, $context, $db_prefix, $language;
$manual = false;

if(!defined('SMF') && file_exists('SSI.php'))
{
	require_once('SSI.php');
	$manual = true;
}
elseif(!defined('SMF'))
	die('SSI.php not found');
	
if(empty($db_prefix))
{
	
	require('Settings.php');
	$db_connection = @mysql_connect($db_server, $db_user, $db_passwd);

	if (!$db_connection || !@mysql_select_db($db_name, $db_connection))
			db_fatal_error();

	// use the same prefix as SMF
	$tp_prefix = $db_prefix.'tp_';

	$render .='
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><head>
		<title>TinyPortal - v1.0 for SMF1.1.x</title>
		 <link rel="stylesheet" type="text/css" href="Themes/default/style.css" />
	</head><body> ';
	$manual=true;

}
$render='';
// prefix of TinyPortal tables
// use the same prefix as SMF
$tp_prefix = $db_prefix.'tp_';

$render =	'<div id="hidemenow" style="z-index: 200; margin-bottom: 1em; position: absolute; top: 120px; left: 25%; width: 50%; height: 500px; border: solid 2px #222;background: white;">
<div style="margin: 0; padding: 8px;" class="catbg">Install/Upgrade v1.0 for SMF 1.1.x &copy;2004-2012</div>
	<div class="middletext" style="padding: 1em; overflow: auto;">
		<ul class="normallist" style="line-height: 1.5em;">';

if(!checkTable('data'))
{
	$request = itpdb_query("CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "data` (
  `id` int(11) NOT NULL auto_increment,
  `type` tinyint(4) NOT NULL default '0',
  `ID_MEMBER` int(11) NOT NULL default '0',
  `value` smallint(6) NOT NULL default '0',
  `item` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id`)
	) TYPE=MyISAM");
	$render .='<li>Data table was created</li>';
}

// old empty blocks needs "actio=all"
$convertblocks=false;
if(!checkTable('settings'))
{
	$rq=itpdb_query("CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "settings` (
  `id` mediumint(9) NOT NULL auto_increment,
  `name` text NULL,
  `value` text NULL,
	PRIMARY KEY  (`id`)
	) TYPE=MyISAM");
	$render .='<li>Settings table was created</li>';
}
else
{
	$result=itpdb_query("SELECT * FROM " . $tp_prefix . "settings WHERE name='version' LIMIT 1");
	$row=itpdb_fetch_assoc($result);
	if($row['value']<104)
		$convertblocks=true;
	if($row['value']<105)
		$convertmodule=true;
	if($row['value']<1090)
		$convertaccess=true;
	itpdb_free_result($result);
}

$settings_array=array(
 'padding' => '4' ,
 'margins' => '2' ,
 'topbar_align' => 'center' ,
 'leftbar_width' => '170' ,
 'rightbar_width' => '170' ,
 'fixed_width' => '0' ,
 'use_SSI' => '1' ,
 'frontpage_limit' => '5' ,
 'SSI_board' => '1' ,
 'temaer' => '' ,
 'temanames' => '' ,
 'temapaths' => '' ,
 'showtop' => '1' ,
 'frontpage_limit_len' => '300' ,
 'featured_article' => '' ,
 'front_type' => 'forum_articles' ,
 'showforumfirst' => '0' ,
 'allow_guestnews' => '1' ,
 'version' => '1106' ,
 'use_wysiwyg' => '1' ,
 'allowed_membergroups' => '' ,
 'approved_membergroups' => '' ,
 'userbox_options' => 'avatar,logged,time,unread,stats,online,stats_all' ,
 'hide_leftbar_forum' => '0' ,
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
 'sitemap_items' => '',
 'cat_list' => '',
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
 'hide_bottombar_forum' => '0' ,
 'hide_topbar_forum' => '0' ,
 'bottombar' => '1' ,
 'blockwidth_left' => '200' ,
 'blockwidth_right' => '150' ,
 'blockwidth_center' => '150' ,
 'blockwidth_front' => '150' ,
 'blockwidth_bottom' => '150' ,
 'blockwidth_top' => '150' ,
 'blockwidth_lower' => '150' ,
 'blockgrid_left' => '' ,
 'blockgrid_right' => '' ,
 'blockgrid_center' => '' ,
 'blockgrid_front' => '' ,
 'blockgrid_bottom' => '' ,
 'blockgrid_top' => '' ,
 'blockgrid_lower' => '' ,
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
 'centerpanel' => '1' ,
 'bottompanel' => '1' ,
 'frontpanel' => '1' ,
 'lowerpanel' => '1' ,
 'hide_lowerbar_forum' => '0' ,
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
 'blockheight_left' => '' ,
 'blockheight_right' => '' ,
 'blockheight_center' => '' ,
 'blockheight_front' => '' ,
 'blockheight_bottom' => '' ,
 'blockheight_top' => '' ,
 'blockheight_lower' => '' ,
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
 'shoutbox_height' => '250' ,
 'shoutbox_limit' => '5' ,
 'guest_shout' => '0' ,
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
$updates=0;
foreach($settings_array as $what => $val)
{
	$sjekk=itpdb_query("SELECT * FROM " . $tp_prefix . "settings WHERE name='" . $what . "'");
	if(itpdb_num_rows($sjekk)<1){
		itpdb_query("INSERT INTO " . $tp_prefix . "settings (name,value) VALUES ( '". $what . "', '". $val ."')");
		$updates++;
	}
	elseif(itpdb_num_rows($sjekk)>0 && $what=='version'){
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='" . $val . "' WHERE name='" . $what . "'");
		$render .= '<li>Updated version to '.$val.'</li>';
		itpdb_free_result($sjekk);
	}
	elseif(itpdb_num_rows($sjekk)>0 && $what=='userbox_options'){
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='" . $val . "' WHERE name='" . $what . "'");
		itpdb_free_result($sjekk);
	}
	elseif(itpdb_num_rows($sjekk)>0 && $what=='leftbar'){
		$row=itpdb_fetch_row($sjekk);
		$val=$row[0];
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='" . $val . "' WHERE name='leftpanel'");
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='0' WHERE name='leftbar'");
		itpdb_free_result($sjekk);
	}
	elseif(itpdb_num_rows($sjekk)>0 && $what=='rightbar'){
		$row=itpdb_fetch_row($sjekk);
		$val=$row[0];
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='" . $val . "' WHERE name='rightpanel'");
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='0' WHERE name='rightbar'");
		itpdb_free_result($sjekk);
	}
	elseif(itpdb_num_rows($sjekk)>0 && $what=='topbar'){
		$row=itpdb_fetch_row($sjekk);
		$val=$row[0];
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='" . $val . "' WHERE name='toppanel'");
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='0' WHERE name='topbar'");
		itpdb_free_result($sjekk);
	}
	elseif(itpdb_num_rows($sjekk)>0 && $what=='centerbar'){
		$row=itpdb_fetch_row($sjekk);
		$val=$row[0];
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='" . $val . "' WHERE name='centerpanel'");
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='0' WHERE name='centerbar'");
		itpdb_free_result($sjekk);
	}
	elseif(itpdb_num_rows($sjekk)>0 && $what=='bottombar'){
		$row=itpdb_fetch_row($sjekk);
		$val=$row[0];
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='" . $val . "' WHERE name='bottompanel'");
		itpdb_query("UPDATE " . $tp_prefix . "settings SET value='0' WHERE name='bottombar'");
		itpdb_free_result($sjekk);
	}
	else
		itpdb_free_result($sjekk);
}
if($updates>0)
	$render .='<li>Settings table updated</li>
	<li>Added '.$updates.' new setting(s)</li>';

if(checkTable('blocks'))
{
	checkColumn('blocks','var1','ADD `var1` INT NOT NULL');
	checkColumn('blocks','var2','ADD `var2` INT NOT NULL');
	checkColumn('blocks','visible','ADD `visible` TEXT NULL');
	checkColumn('blocks','lang','ADD `lang` TEXT NULL');

	checkColumn('blocks','access2','ADD `access2` TEXT NULL');
	checkColumn('blocks','editgroups','ADD editgroups TEXT NULL');
	checkColumn('blocks','pos',' CHANGE `pos` `pos` INT NOT NULL',true);

	checkColumn('blocks','var3','ADD `var3` INT NOT NULL');
	checkColumn('blocks','var4','ADD `var4` INT NOT NULL');

	// convert empty blocks
	if($convertblocks)
	{
		itpdb_query("UPDATE " . $tp_prefix . "blocks SET access2='actio=allpages' WHERE access2=''");
		$render .='<li>Updated old blocks</li>';
	}
	// make sure access2 is comma separated
	itpdb_query("UPDATE " . $tp_prefix . "blocks SET access2 = REPLACE(access2, '|', ',') WHERE 1");
	$render .='<li>Updated access field of blocks</li>';
}
else
{
	// attempt to create it then
	itpdb_query("CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "blocks` (
	id int(11) NOT NULL auto_increment,
	type smallint(6) NOT NULL default '0',
	frame tinytext NULL,
	title text NULL,
	body text NULL,
	access text NULL,
	bar tinyint(4) NOT NULL,
	pos int NOT NULL,
	off tinyint(4) NOT NULL default '0',
	visible text NULL,
	var1 INT NULL,
	var2 INT NULL,
	lang text NULL,
	access2 TEXT NULL,
	editgroups TEXT NULL,
	var3 INT NOT NULL,
	var4 INT NOT NULL,
	PRIMARY KEY  (id)
	) TYPE=MyISAM");
	$render .='<li>Blocks table was created</li>';

	// install the values on a fresh install
	itpdb_query("INSERT INTO " . $tp_prefix . "blocks VALUES (1, 1, 'theme', 'User', '', '-1,0,1', 1, 10, 0, '', 0, 0, '', 'actio=allpages', '','','')");
	itpdb_query("INSERT INTO " . $tp_prefix . "blocks VALUES (2, 4, 'theme', 'Search', '', '-1,0,1', 1, 0, 0, '', 0, 0, '', 'actio=allpages', '','','')");
	itpdb_query("INSERT INTO " . $tp_prefix . "blocks VALUES (3, 16, 'none', 'Sitemap', '-no content-', '-1,0,2,3,-2', 2, 20, 0, '0', 0, 0, '', 'actio=allpages', '-2','','')");
	itpdb_query("INSERT INTO " . $tp_prefix . "blocks VALUES (4, 12, 'theme', 'Recent', '10', '-1,0,1', 2, 0, 0, '', 0, 0, '', 'actio=allpages', '','','')");
	itpdb_query("INSERT INTO " . $tp_prefix . "blocks VALUES (5, 3, 'theme', 'Stats', '10', '-1,0,1', 2, 10, 0, '', 0, 0, '', 'actio=allpages', '','','')");
	itpdb_query("INSERT INTO " . $tp_prefix . "blocks VALUES (6, 20, 'theme', 'Shoutbox', '', '-1,0,1', 1, 10, 0, '', 1, 0, '', 'actio=allpages', '','','')");
	$render .='<li>Added some sample values to the blocks table</li>';
}

if(checkTable('variables'))
{
	checkColumn('variables','value4','ADD `value4` TEXT NULL');
	checkColumn('variables','value5','MODIFY `value5` INT DEFAULT -2 NOT NULL',true);
	checkColumn('variables','subtype','ADD `subtype` TINYTEXT NULL');
	checkColumn('variables','value7','ADD `value7` TEXT NULL');

	checkColumn('variables','subtype2','ADD subtype2 INT NOT NULL DEFAULT 0');
	checkColumn('variables','value8','ADD `value8` TEXT NULL');
	checkColumn('variables','value9','ADD `value9` TEXT NULL');
	checkColumn('variables','subtype2',' CHANGE `subtype2` `subtype2` INT NOT NULL default 0',true);
	// convert empty categories
	if($convertblocks)
	{
		itpdb_query("UPDATE " . $tp_prefix . "variables SET value3='-1,0' WHERE value3='' AND type='category'");
		$render .='<li>Updated old categories</li>';
	}
}
else
{
	itpdb_query("CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "variables` (
	`id` int(11) NOT NULL auto_increment,
	`value1` text NULL,
	`value2` text NULL,
	`value3` text NULL,
	`type` tinytext NULL,
	`value4` text NULL,
	`value5` int NOT NULL default '-2',
	`subtype` tinytext NULL,
	`value7` text NULL,
	`value8` text NULL,
	`subtype2` int NOT NULL default '0',
	`value9` text NULL,
	PRIMARY KEY  (`id`)
	) TYPE=MyISAM");
	$render .='<li>Variables table created.</li>';
	itpdb_query("INSERT INTO `" . $tp_prefix . "variables` VALUES (1, 'Portal features', '', '-1,0', 'category', '', -2,'')");
	itpdb_query("INSERT INTO `" . $tp_prefix . "variables` VALUES (2, 'General Articles', '', '-1,0', 'category', '', -2,'')");
	itpdb_query("UPDATE " . $tp_prefix . "settings SET value='1' WHERE name='catlist'");
	itpdb_query("UPDATE " . $tp_prefix . "settings SET value='3' WHERE name='sitemap_items'");
	itpdb_query("UPDATE " . $tp_prefix . "settings SET value='3' WHERE name='featured_article'");
	itpdb_query("INSERT INTO `" . $tp_prefix . "variables` VALUES (3, 'Demo articles', '0', 'cats1', 'menubox', '0', 10,'')");
	$render .='<li>Sample values added to variables table</li>';
}

if(checkTable('articles'))
{
	checkColumn('articles','frame','ADD `frame` TINYTEXT NULL');
	checkColumn('articles','approved','ADD `approved` TINYINT DEFAULT 1 NOT NULL');
	checkColumn('articles','off','ADD `off` TINYINT DEFAULT 0 NOT NULL');
	checkColumn('articles','options','ADD `options` TINYTEXT NULL');
	checkColumn('articles','parse',' CHANGE `parse` `parse` SMALLINT DEFAULT 0 NOT NULL',true);
	checkColumn('articles','comments','ADD `comments` TINYTEXT DEFAULT 0 NOT NULL');

	checkColumn('articles','comments_var','ADD `comments_var` TEXT NULL');
	checkColumn('articles','views','ADD `views` INT default 0 NOT NULL');
	checkColumn('articles','rating','ADD `rating` TEXT NULL');
	checkColumn('articles','voters','ADD `voters` TEXT NULL');

	checkColumn('articles','ID_THEME','ADD `ID_THEME` smallint(6) default 0 NOT NULL');

	checkColumn('articles','shortname','ADD `shortname` tinytext NULL');
	checkColumn('articles','body','CHANGE `body` `body` LONGTEXT NULL',true);
	checkColumn('articles','sticky','ADD `sticky` tinyint default 0 NOT NULL');

	checkColumn('articles','featured','ADD `featured` tinyint default 0 NOT NULL');
	checkColumn('articles','locked','ADD `locked` tinyint default 0 NOT NULL');
	checkColumn('articles','fileimport','ADD `fileimport` text NULL');
	checkColumn('articles','topic','ADD `topic` int default 0 NOT NULL');
	checkColumn('articles','illustration',' ADD `illustration` text NULL');

	checkColumn('articles','headers','ADD `headers` text NULL');
	checkColumn('articles','type','ADD `type` tinytext NULL');
	checkColumn('articles','pub_start','ADD `pub_start`INT default 0 NOT NULL');
	checkColumn('articles','pub_end','ADD `pub_end` INT default 0 NOT NULL');
 
	// change to types
	itpdb_query("UPDATE " . $tp_prefix . "articles SET type='php', useintro=0 WHERE useintro=-1");
	itpdb_query("UPDATE " . $tp_prefix . "articles SET type='bbc', useintro=0 WHERE useintro=-2");
	itpdb_query("UPDATE " . $tp_prefix . "articles SET type='import' WHERE useintro=-3");

	checkColumn('articles','global_tag','ADD `global_tag` text NULL');
	checkColumn('articles','pub_start','ADD `pub_start` INT default 0 NOT NULL');
	checkColumn('articles','pub_end','ADD `pub_end` default 0 INT NOT NULL');
			
	// make sure featured is updated
	$sjekk=itpdb_query("SELECT value FROM " . $tp_prefix . "settings WHERE name='featured_article'");
	if(itpdb_num_rows($sjekk)>0)
	{
		$row=itpdb_fetch_assoc($sjekk);
		itpdb_free_result($sjekk);
		if(!empty($row['value']))
		{
			itpdb_query("UPDATE " . $tp_prefix . "articles SET featured=1 WHERE id=" . $row['value']);
		$render .='<li>Update featured article</li>';
		}
	}
}
else
{
	// attempt to create it then
	itpdb_query("CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "articles` (
	`id` int(11) NOT NULL auto_increment,
	`date` int(11) NOT NULL default '0',
	`body` longtext NULL,
	`intro` text NULL,
	`useintro` tinyint(4) NOT NULL default '1',
	`category` smallint(6) NOT NULL default '0',
	`frontpage` tinyint(4) NOT NULL default '1',
	`subject` text NULL,
	`authorID` int(11) NOT NULL default '0',
	`author` text NULL,
	`frame` tinytext NULL,
	`approved` tinyint default '1' NOT NULL,
	`off` tinyint default '0' NOT NULL,
	`options` text NULL,
	`parse` smallint default '0' NOT NULL,
	`comments` tinyint default '0' NOT NULL,
	`comments_var` text NULL,
	`views` int default '0' NOT NULL,
	`rating` text NULL,
	`voters` text NULL,
	`ID_THEME` smallint(6) default '0' NOT NULL,
	`shortname` tinytext NULL,
	`sticky` TINYINT default '0' NOT NULL ,
	`fileimport` TEXT NULL,
	`topic` INT default '0' NOT NULL,
	`locked` TINYINT default '0' NOT NULL,
	`illustration` TEXT NULL,
	`headers` TEXT NULL,
	`type` TINYTEXT NULL,
	`global_tag` TEXT NULL,
	`featured` TINYINT default '0' NOT NULL ,
	`pub_start` INT(11) default '0' NOT NULL,
	`pub_end` INT(11) default '0' NOT NULL,
	PRIMARY KEY  (`id`)
	) TYPE=MyISAM AUTO_INCREMENT=2;");
	$render .='<li>Articles table created</li>';
}

if(checkTable('dlmanager'))
{
	checkColumn('dlmanager','rating','ADD `rating` TEXT NULL');
	checkColumn('dlmanager','subitem','ADD `subitem` INT default 0 NOT NULL');
	checkColumn('dlmanager','files','ADD `files` INT default 0 NOT NULL');
	checkColumn('dlmanager','global_tag','ADD `global_tag` TEXT NULL');
}
else
{
	itpdb_query("CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "dlmanager` (
	`id` int(11) NOT NULL auto_increment,
	`name` tinytext NULL,
	`description` text NULL,
	`icon` text NULL,
	`category` int(11) NOT NULL default '0',
	`type` tinytext NULL,
	`downloads` int(11) NOT NULL default '0',
	`views` int(11) NOT NULL default '0',
	`file` text NULL,
	`created` int(11) NOT NULL default '0',
	`last_access` int(11) NOT NULL default '0',
	`filesize` int(11) NOT NULL default '0',
	`parent` int(11) NOT NULL default '0',
	`access` text NULL,
	`link` text NULL,
	`authorID` int(11) NOT NULL default '0',
	`screenshot` text NULL,
	`rating` text NULL,
	`voters` text NULL,
	`subitem` int(11) NOT NULL default '0',
	`files` int(11) NOT NULL default '0',
	`global_tag` text NULL,
	PRIMARY KEY  (`id`)
	) TYPE=MyISAM");
	$render .='<li>DL manager table created</li>';
	itpdb_query("INSERT INTO " . $tp_prefix . "dlmanager (name,access,type) VALUES ( 'General', '-1,0,1','dlcat')");
}

if(checkTable('modules'))
{
	checkColumn('modules','website','ADD `website` tinytext NULL');
	checkColumn('modules','profile','ADD profile tinytext NULL');
	checkColumn('modules','website','ADD `website` tinytext NULL');
	checkColumn('modules','frontsection','ADD frontsection tinytext NULL');
	checkColumn('modules','globaltags','ADD globaltags tinytext NULL');
}
else
{
	itpdb_query("
	CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "modules` (
	`id` int(11) NOT NULL auto_increment,
	`version` tinytext NULL,
	`modulename` tinytext NULL,
	`title` text NULL,
	`subquery` tinytext NULL,
	`autoload_run` tinytext NULL,
	`autoload_admin` tinytext NULL,
	`autorun` tinytext NULL,
	`autorun_admin` tinytext NULL,
	`db` text NULL,
	`permissions` text NULL,
	`active` tinyint(4) NOT NULL default '0',
	`languages` text NULL,
	`blockrender` tinytext NULL,
	`adminhook` tinytext NULL,
	`logo` tinytext NULL,
	`tpversion` tinytext NULL,
	`smfversion` tinytext NULL,
	`description` text NULL,
	`author` tinytext NULL,
	`email` tinytext NULL,
	`website` tinytext NULL,
	`profile` tinytext NULL,
	`frontsection` tinytext NULL,
	`globaltags` tinytext NULL,
	PRIMARY KEY  (`id`)
	) TYPE=MyISAM;");
	$render .='<li>Modules table created</li>';
}

if(!checkTable('dldata'))
{
	itpdb_query("
	 CREATE TABLE  IF NOT EXISTS `" . $tp_prefix . "dldata` (
	`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`views` BIGINT NOT NULL ,
	`downloads` BIGINT NOT NULL ,
	`item` INT NOT NULL ,
	`week` TINYINT NOT NULL ,
	`year` SMALLINT NOT NULL
	) TYPE = MYISAM"); 
	$render .='<li>DL Manager Data table created</li>';
}

// add the module server
$result=itpdb_query("SELECT * FROM " . $db_prefix . "package_servers WHERE name='TinyPortal'");
if(itpdb_num_rows($result)>0)
	itpdb_free_result($result);
else
	$result=itpdb_query("INSERT INTO " . $db_prefix . "package_servers (name,url) VALUES('TinyPortal','http://www.tinyportal.net/tpmods')");

// check for utf-8 setting
$sjekk=itpdb_query("SELECT variable,value FROM " . $db_prefix . "settings WHERE variable='global_character_set' && value='UTF-8'");
if(itpdb_num_rows($sjekk)>0)
{
	itpdb_free_result($sjekk);
	itpdb_query("ALTER TABLE `" . $tp_prefix . "articles` DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci") or die('table could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "articles` CHANGE `body` `body` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('article1 body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "articles` CHANGE `intro` `intro` TEXT CHARACTER SET utf8  COLLATE utf8_general_ci NULL") or die('article2 body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "articles` CHANGE `subject` `subject` TEXT CHARACTER SET utf8  COLLATE utf8_general_ci NULL") or die('article3 body could not be updated');

	itpdb_query("ALTER TABLE `" . $tp_prefix . "settings` DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci") or die('table could not be updated');

	itpdb_query("ALTER TABLE `" . $tp_prefix . "blocks` DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci") or die('table could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "blocks` CHANGE `title` `title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('blocks1 body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "blocks` CHANGE `body` `body` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('blocks2 body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "blocks` CHANGE `lang` `lang` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('blocks3 body could not be updated');

	itpdb_query("ALTER TABLE `" . $tp_prefix . "variables` DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci") or die('table could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "variables` CHANGE `value1` `value1` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('article body could not be updated');
//	itpdb_query("ALTER TABLE `" . $tp_prefix . "variables` CHANGE `value2` `value2` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('article body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "variables` CHANGE `value3` `value3` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('article body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "variables` CHANGE `value4` `value4` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('article body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "variables` CHANGE `value7` `value7` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('article body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "variables` CHANGE `value8` `value8` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('article body could not be updated');

	$render .='<li style="list-style: square; margin-left: 1em; padding-left: 5px; ">Converted variables table for utf-8</li>';
	itpdb_query("ALTER TABLE `" . $tp_prefix . "dlmanager` DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci") or die('table could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "dlmanager` CHANGE `name` `name` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('article1 body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "dlmanager` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL") or die('article body2 could not be updated');

	$render .='<li>Converted tables for utf-8</li>';
}

if(checkTable('shoutbox'))
{
	checkColumn('shoutbox','value6','ADD `value6` text NOT NULL');
	checkColumn('shoutbox','value7','ADD `value7` tinyint NOT NULL');
	checkColumn('shoutbox','value8','ADD `value8` text NOT NULL');
	checkColumn('shoutbox','edit','ADD `edit` tinyint NOT NULL');
}
else
{
	$rq11=itpdb_query("
	CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "shoutbox` (
	`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
	`value1` text NOT NULL ,
	`value2` text NOT NULL ,
	`value3` text NOT NULL ,
	`type` tinytext NOT NULL ,
	`value4` text NOT NULL ,
	`value5` int( 11 ) NOT NULL default '-2',
	`value6` text NOT NULL ,
	`value7` tinyint NOT NULL ,
	`value8` text NOT NULL ,
	`edit` tinyint NOT NULL ,
	PRIMARY KEY ( `id` )
	) TYPE = MYISAM");
	$render .='<li>Shoutbox table is created.</li>';
}

// check for utf-8 setting
$sjekk=itpdb_query("SELECT variable,value FROM ". $db_prefix . "settings WHERE variable='global_character_set' && value='UTF-8'");
if(itpdb_num_rows($sjekk)>0)
{
	itpdb_free_result($sjekk);
	itpdb_query("ALTER TABLE `" . $tp_prefix . "shoutbox` DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci") or die('table could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "shoutbox` CHANGE `value1` `value1` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL") or die('article body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "shoutbox` CHANGE `value2` `value2` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL") or die('article body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "shoutbox` CHANGE `value3` `value3` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL") or die('article body could not be updated');
	itpdb_query("ALTER TABLE `" . $tp_prefix . "shoutbox` CHANGE `value4` `value4` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL") or die('article body could not be updated');
	$render .='<li>Converted shoutbox table for utf-8.</li>';
}

//install the rating tables 
if(!checkTable('rates'))
	itpdb_query("
 CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "rates` (
`id` INT NOT NULL AUTO_INCREMENT,
`member_id` INT NOT NULL ,
`timestamp` INT NOT NULL ,
`rate` TINYINT NOT NULL ,
`rate_type` TINYTEXT NOT NULL ,
`rate_id` INT NOT NULL ,
	PRIMARY KEY ( `id` )
) TYPE = MYISAM ");

if(!checkTable('ratestats'))
	itpdb_query("
 CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "ratestats` (
`id` INT NOT NULL AUTO_INCREMENT ,
`average` TINYINT NOT NULL ,
`rate_type` TINYTEXT NOT NULL ,
`rate_id` INT NOT NULL ,
`rates` INT NOT NULL ,
	PRIMARY KEY ( `id` )
) TYPE = MYISAM"); 

if(!checkTable('events'))
	itpdb_query("
 CREATE TABLE IF NOT EXISTS `" . $tp_prefix . "events` (
`id` INT NOT NULL AUTO_INCREMENT ,
 `id_member` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `textvariable` mediumtext NOT NULL,
  `link` mediumtext NOT NULL,
  `description` mediumtext NOT NULL,
  `allowed` mediumtext NOT NULL,
  `eventid` int(11) default NULL,
  `on` tinyint(4) default NULL,
	PRIMARY KEY ( `id` )
) TYPE = MYISAM"); 

// make sure TPShout is available
$request=itpdb_query("SELECT id FROM " . $tp_prefix . "modules WHERE modulename='TPShout'");
if(itpdb_num_rows($request)>0)
{
	$row=itpdb_fetch_row($request);
	itpdb_free_result($request);
	itpdb_query("UPDATE " . $tp_prefix . "modules SET logo='tpshoutbox.png'");
}
else
{
	$newmod=array(
	'version' => '1.0',
	'name' => 'TPShout',		// must be exactly equal to the folder.
	'title' => 'TP Simple Shout', 
	'subquery' => 'shout',	// the subcall that let TP knows which module is running.
	'sourcefile' => 'TPShout.php',		// The main source file, entry for more source files.
	'blockaccess' => '',		// 
	'permissions' => 'tp_can_admin_shout|1',  //permiss 
	'languages' => 'english',
	'blockrender' => 'tpshout_fetch',
	'adminhook' => 'tpshout_adminhook',
	'logo' => 'tpshoutbox.png',
	'tpversion' => '1.0',
	'smfversion' => '1.1.x',
	'description' => '[b]TP Simple Shoutbox[/b] is the original shoutbox from v0.9 series of TinyPortal, now converted to a TP module. It allows shout in BBC format,scrolling of shouts, insert of BBC codes and smilies and an admin interface to delete or modify shouts.<br />	',
	'author' => 'Bloc',
	'contact' => 'bloc@tinyportal.net',
	'website' => 'http://www.tinyportal.net',
	'profilehook' => 'tpshout_profile',
	'frontpagehook' => 'tpshout_frontpage',
	'globaltags' => '',
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
	$request = itpdb_query("SELECT id,access2 FROM " . $tp_prefix . "blocks WHERE 1");
	if(itpdb_num_rows($request)>0)
	{
		$new = array();
		while($row=itpdb_fetch_assoc($request))
		{
			unset($new); $new=array();
			$a=explode("|", $row['access2']);
			if(count($a)>1)
			{
				foreach($a as $b => $what)
				{
					$first=substr($what, 0,6);
					$second=substr($what, 6);
					$third = explode(",",$second);
					// build new ones
					if(count($third)>1)
					{
						foreach($third as $t => $tr)
							$new[]=$first.$tr;
					}
					else
						$new[] = $first.$second;
				}
			}
			else
				$new[] = $row['access2'];
			
			itpdb_query("UPDATE " . $tp_prefix . "blocks SET access2 = '" . (count($new)>1 ? implode(",",$new) : $new[0]) . "' WHERE id = ". $row['id']);
		}
	}
	itpdb_free_result($request);
}

$render .='</ul>		
		<hr><p>TinyPortal\'s table structure is now installed/updated. </p>
		<b>Thank you for trying out TinyPortal!</b>
		<div style="padding-top: 3em; text-align: center;"><a style="font-size: 1.1em; " href="javascript:void(0);" onclick="document.getElementById(\'hidemenow\').style.display = \'none\'; return false;">Remove this window</a></div>
	</div></div>';

if($manual)
	echo $render . '</body></html>';
else
{
	echo $render; 
}


function checkTable($table)
{
	global $settings, $scripturl, $boarddir, $context, $sourcedir, $db_prefix, $language;
	
	// use the same prefix as SMF
	$tp_prefix = $db_prefix.'tp_';
	$result = mysql_query("SHOW COLUMNS FROM `" . $tp_prefix . $table. "` LIKE 'id'");
	if(is_resource($result) && mysql_num_rows($result)>0)
	{
		mysql_free_result($result);
		return true;
	}
	else
		return false;
}
function checkColumn($table,$col,$action, $modify = false)
{
	global $render, $settings, $scripturl, $boarddir, $context, $sourcedir, $db_prefix, $language;
	// use the same prefix as SMF
	$tp_prefix = $db_prefix.'tp_';
	if(!$modify)
	{
		$result = mysql_query("SHOW COLUMNS FROM `" . $tp_prefix . $table. "` LIKE '" . $col . "'");
		if(mysql_num_rows($result)>0)
		{
			mysql_free_result($result);
		}
		else
		{
			mysql_query("ALTER TABLE `" . $tp_prefix . $table. "` ". $action);
			$render .='<li>Added ' . $col . ' in ' . $table . ' table</li>';
		}
	}
	else
	{
		$result = mysql_query("SHOW COLUMNS FROM `" . $tp_prefix . $table. "` LIKE '" . $col . "'" );
		if(mysql_num_rows($result)>0)
		{
			mysql_free_result($result);
			mysql_query("ALTER TABLE `" . $tp_prefix . $table. "` ". $action);
			$render .='<li>Changed ' . $col . ' in ' . $table . ' table</li>';
		}
	}
}
function itpdb_query($query,$val1='',$val2='')
{
	// for SMF 1.1	
	$req =  mysql_query($query);
	return $req;
}

function itp_query($query,$val1='',$val2='')
{
	// for SMF 1.1	
	$req =  mysql_query($query);
	return $req;
}

function itpdb_num_rows($request)
{
	// for SMF 1.1	
	$req = mysql_num_rows($request);
	return $req;
}

function itpdb_fetch_row($request)
{
	// for SMF 1.1	
	$req = mysql_fetch_row($request);
	return $req;
}

function itpdb_fetch_assoc($request)
{
	// for SMF 1.1	
	$req = mysql_fetch_assoc($request);
	return $req;
}

function itpdb_insert_id($request)
{
	// for SMF 1.1	
	$req = mysql_insert_id($request);
	return $req;
}

function itpdb_free_result($request)
{
	// for SMF 1.1	
	if(is_resource($request))
		mysql_free_result($request);
	
	return;
}

?>