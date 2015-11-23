<?php
/**
 * @package TinyPortal
 * @version 1.2
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2015 - The TinyPortal Team
 *
 */

global $txt, $context, $scripturl;

// Navigation... //
// Rev 89
$txt['tp-admin9'] = 'TPdownloads';
$txt['tp-admin8'] = 'Articles and Categories';
$txt['tp-adminpanels'] = 'Panels and Blocks';
$txt['tp-settingsfrontpage'] = 'Settings and Frontpage';
$txt['tp-forum'] = 'Forum';
$txt['tp-smfhelp'] = 'SMF';
$txt['tp-tphelp'] = 'TinyPortal';
$txt['tp-tpnews'] = 'News and Credits';
$txt['tp-adminmodules1'] = 'Modules';

// General Miscellious... //
// Rev 89
$txt['tp-edit'] = 'Edit';
$txt['tp-send'] = 'Save';
$txt['tp-tpadmin'] = 'TP Admin';
$txt['tp-profilesection'] = 'TinyPortal';

// Articles... //
// Rev 89
$txt['tp_rate'] = 'Rate it!';
$txt['tp-arenew'] = 'are new';
$txt['tp-by'] = 'Written by';
$txt['tp-cannotcomment'] = 'You don\'t have permmission to comment, or comments have been turned off for this article.';
$txt['tp-comments'] = 'Comments';
$txt['tp-confirmdelete'] = 'Are you sure you want to delete this article?';
$txt['tp-confirmcommentdelete'] = 'Are you sure you want to delete this comment?';
$txt['tp-arthaverated'] = 'You have rated this article.';
$txt['tp-delete'] = 'Delete';
$txt['tp-editarticle']= 'Edit article';
$txt['tp-editarticle2']='Edit options';
$txt['tp-newcaptcha'] = 'Create a new image';
$txt['tp-ratearticle'] = 'Give this article a rating of ';
$txt['tp-ratingaverage'] = 'Rating: ';
$txt['tp-ratingvotes'] = 'Rates';
$txt['tp-readmore'] = 'Read More';
$txt['tp-submit'] = 'Send';
$txt['tp-views'] = 'Views';
$txt['tp-subject'] = 'Subject';
$txt['tp-articlessubmitted'] = 'Articles';
$txt['tp-articlenotexist'] = 'The article isn\'t active, hasn\'t been approved yet, or simply does not exist.';
$txt['tp-categorynotexist'] = 'The category doesn\'t exist.';
$txt['tp-last'] = 'Last';
$txt['tp-morefrom'] = '..more from ';
$txt['tp-next'] = 'Next story:';
$txt['tp-author'] = 'Author';
$txt['cannot_tp_articles'] = 'Sorry, you aren\'t allowed to manage articles.';
$txt['tp-viewpage'] = 'Viewing an';
$txt['tp-viewpage2'] = 'article.';
$txt['tp-viewcat'] = 'Viewing a';
$txt['tp-viewcat2'] = 'category of articles.';
$txt['tp-morearticles'] = 'More from same topic';
$txt['tp-first'] = 'First:';
$txt['tp-reporttomoderator'] = 'Report comment';
$txt['tp-written'] = 'written';
$txt['tp-noimportarticle'] = 'Sorry, the article could not be loaded.';
$txt['tp-noname'] = '-no name-';
$txt['tp-submitarticlebbc']='Write BBC article';
$txt['permissionname_tp_articles'] = 'Manage articles';
$txt['permissionhelp_tp_articles'] = 'Allows you to manage articles, categories for articles and the catlist.';
$txt['permissionname_tp_artcomment'] = 'Can comment on articles';
$txt['permissionhelp_tp_artcomment'] = 'Allows you to manage who can comment on articles.';
$txt['permissiongroup_tinyportal_submit'] = 'TinyPortal Articles';
$txt['permissionname_tp_submithtml'] = 'Can submit HTML articles';
$txt['permissionhelp_tp_submithtml'] = 'Allows you to write and submit HTML type of articles.';
$txt['cannot_tp_submithtml'] = 'Sorry, you aren\'t allowed to submit HTML articles.';
$txt['permissionname_tp_submitbbc'] = 'Can submit BBC articles';
$txt['permissionhelp_tp_submitbbc'] = 'Allows you to write and submit BBC type of articles.';
$txt['cannot_tp_submitbbc'] = 'Sorry, you aren\'t allowed to submit BBC articles.';
$txt['permissionname_tp_editownarticle'] = 'Can edit own articles';
$txt['permissionhelp_tp_editownarticle'] = 'Allows you to edit your own articles.';
$txt['cannot_tp_editownarticle'] = 'Sorry, you aren\'t allowed to edit your article.';
$txt['permissionname_tp_alwaysapproved'] = 'Submissions are always approved';
$txt['permissionhelp_tp_alwaysapproved'] = 'Allows you to write articles that will be automatically approved.';
$txt['cannot_tp_alwaysapproved'] = 'Article submission needs to be approved first.';
$txt['tp-permanent'] = 'Permanent?';
$txt['tp-print'] = 'Print';
$txt['tp-printgoback'] = 'Go back to article';
$txt['tp-ratings'] = 'Ratings';
$txt['tp_unapproved_members']='Unapproved: ';
$txt['tp-acronymdays'] = 'd ';
$txt['tp-acronymhours'] = 'h ';
$txt['tp-acronymminutes'] = 'm ';
$txt['tp-incategory'] = ' in ';


// Blocks... //
// Rev 89
$txt['tp-guest']='Guest';
$txt['tp-guests'] = 'Guests';
$txt['tp-itemviews'] = 'Views';
$txt['tp-latest'] = 'Latest';
$txt['tp-loggedintime'] = 'Total Logged In:';
$txt['tp-mostonline'] = 'Online Ever';
$txt['tp-mostonline-today'] = 'Online Today';
$txt['tp-changetheme'] = 'Change';
$txt['tp-nothemeschosen'] = 'No themes are chosen';
$txt['tp-tools']='Tools';
$txt['tp-total'] = 'Total';
$txt['tp-unread']='Show unread';
$txt['tp-pm']='PM: ';
$txt['tp-pm2']='New: ';
$txt['tp-replies']='Show replies';
$txt['tp-showownposts'] = 'Show own posts';
$txt['tp-showshouts'] = 'Show last';
$txt['tp-stats'] = 'Stats';
$txt['tp-submitarticle']='Write HTML article';
$txt['tp-users'] = 'Users';
$txt['permissionname_tp_blocks'] = 'Manage blocks';
$txt['permissionhelp_tp_blocks'] = 'Allows you to manage blocks. Be careful with this permission as it allows creation of php blocks.';
$txt['cannot_tp_blocks'] = 'Sorry, you aren\'t allowed to manage blocks.';
$txt['edit_description'] = 'Edit Block Contents';
$txt['block-upshrink_description'] = 'Collapse or Expand Block';

// Panels... //
// Rev 89
$txt['left-tp-upshrink_description'] = 'Left panel';
$txt['right-tp-upshrink_description'] = 'Right Panel';
$txt['upper-tp-upshrink_description'] = 'Upper Panel';
$txt['lower-tp-upshrink_description'] = 'Lower Panel';
$txt['bottom-tp-upshrink_description'] = 'Bottom Panel';
$txt['top-tp-upshrink_description'] = 'Top Panel';
$txt['front-tp-upshrink_description'] = 'Frontpage';
$txt['center-tp-upshrink_description'] = 'Center Panel';

// Search... //
// Rev 89
$txt['tp-searcharticles'] = 'Extended search'; 
$txt['tp-searcharticles2'] = 'Search Articles';
$txt['tp-searchdownloads'] = 'Search Downloads Manager';
$txt['tp-nosearchentered'] = 'Nothing to search for!';
$txt['tp-search'] = 'Search';
$txt['tp-searchintitle'] = 'Search in titles';
$txt['tp-searchinbody'] = 'Search in article texts';
$txt['tp-searcharticleshelp'] = 'Search through all allowed articles.';

// Profile... //
// Rev 89
$txt['articlesprofile'] = 'Articles';
$txt['downloadprofile'] = 'Uploaded files';
$txt['galleryprofile'] = 'Gallery items';
$txt['linksprofile'] = 'Submitted links';
$txt['shoutboxprofile'] = 'View shouts';
$txt['tp-category']='Category';
$txt['tpsummary'] = 'Portal Summary';
$txt['tpsummary_art'] = 'Total number of articles submitted:';
$txt['tpsummary_dl'] = 'Total number of uploaded files:';
$txt['tpsummary_shout'] = 'Total number of shouts:';
$txt['tp-wysiwygchoice'] = 'Use WYSIWYG editor';
$txt['downloadsprofile'] = 'Uploaded files';
$txt['articlesprofile2'] = 'All the articles the member has written. Note that articles currently not active will give an error when viewed and are also marked in italic style. Articles not approved yet are likewise marked with surrounding parentheses.';
$txt['downloadsprofile2'] = 'All the files uploaded in File Manager. Files not yet approved are shown in italic style.';
$txt['shoutboxprofile2'] = 'All shouts made by the member.';
$txt['tp-prof_allarticles'] = 'Total number of articles written:';
$txt['tp-prof_alldownloads'] = 'Total number of submitted files:';
$txt['tp-prof_allshouts'] = 'Total number of shouts:';
$txt['tp-prof_approvdownloads'] = 'Not approved uploads.';
$txt['tp-prof_offarticles'] = 'Articles not currently active:';
$txt['tp-prof_offarticles2'] = 'All articles are marked as active.';
$txt['tp-prof_waitapproval1'] = 'You have ';
$txt['tp-prof_waitapproval2'] = 'articles waiting approval.';

// News... //
// Rev 89
$txt['permissionname_tp_news'] = 'Can read TP news';
$txt['permissionhelp_tp_news'] = 'Allows you to read news, install updates etc.';
$txt['cannot_tp_news'] = 'Sorry, you aren\'t allowed to fetch news.';

// TP Settings... //
// Rev 89
$txt['permissiongroup_tinyportal'] = 'TinyPortal';
$txt['permissiongroup_simple_tinyportal'] = 'Manage TinyPortal';
$txt['permissionname_tp_settings'] = 'Manage settings';
$txt['permissionhelp_tp_settings'] = 'Allows you to manage settings and news for TP.';
$txt['cannot_tp_settings'] = 'Sorry, you aren\'t allowed to manage settings.';
$txt['tp_maintenace'] = 'Maintenance Mode';

// File Manager... //
// Rev 89
$txt['tp-dluploaded'] = 'Uploads';
$txt['tp-uploadedby'] = 'Uploaded by ';
$txt['permissiongroup_tinyportal_dl'] = 'TinyPortal File manager';
$txt['permissionname_tp_dlmanager'] = 'Manage Files';
$txt['permissionhelp_tp_dlmanager'] = 'Allows you to visit and operate any part of the File manager admin screens.';
$txt['cannot_tp_dlmanager'] = 'Sorry, you aren\'t allowed to manage the File Manager module.';
$txt['permissionname_tp_dlupload'] = 'Upload file';
$txt['permissionhelp_tp_dlupload'] = 'Allow you to upload files for the File manager';
$txt['cannot_tp_dlupload'] = 'Sorry, you aren\'t allowed to upload files to the File Manager.';
$txt['whoall_tpmod_dl'] = 'Viewing the File Manager.';
$txt['whoall_tpmod_dlcat'] = 'Viewing a File category.';
$txt['whoall_tpmod_dlitem'] = 'Viewing a files item.';
$txt['tp-dlsettings'] = 'Settings';
$txt['tp-downloads'] = 'Downloads';
$txt['tp-dlhaverated'] = 'You have rated this file.';

$txt['permissionhelp_tp_dlupload'] = 'Allow you to upload files for the File manager';
$txt['permissionname_tp_dlcreatetopic'] = 'Create Download topic';
$txt['permissionhelp_tp_dlcreatetopic'] = 'Allows you to create a linked support topic, and also link the topic to the download. Uses pre-defined boards to choose from.';
$txt['cannot_tp_dlcreatetopic'] = 'Sorry, you aren\'t allowed to create support topics.';

// Shoutbox... //
// Rev 89
$txt['tp-shouts'] = 'Shouts:';
$txt['shout!'] = 'Shout!';
$txt['tp-shout'] = 'Shout';

// Menus... //
// Rev 89
$txt['tp-miscblocks'] = 'Menus';

$txt['tp-quicklist'] = 'My Images Quicklist';
$txt['tp-quicklist2'] = 'The images below are small thumbnails of the original pictures. By clicking them the  picture is inserted into the editor. Be sure to click in the editor to make it active
before selecting any of the images.';
$txt['tp-uploadfile'] = 'Upload a new image into the quick-list: ';
$txt['tp-tagitem'] = 'Download file(s)';
$txt['tp-tagcategory'] = 'Download file(s)';
$txt['tp-articletagitem'] = 'Read article(s)';
$txt['tp-showrelated'] = 'Related';
$txt['tp-morecategories'] = 'More';
$txt['tp-tagtopics'] = 'Tag this topic';
$txt['tp-tagboards'] = 'Tag this board';
$txt['tp-article'] = 'Article';
$txt['tp-hide'] = 'Hide';
$txt['tp-show'] = 'Show';
$txt['tp-latestcodes'] = 'Latest blockcodes from tinyportal.net';
$txt['tp-latestmods'] = 'Latest modules from tinyportal.net';

$txt['tp-poster1'] = ' registered at ';
$txt['tp-poster2'] = ' on ';
$txt['tp-poster3'] = ' and has posted ';
$txt['tp-poster4'] = ' posts in the boards since then. Last visit was  ';
$txt['tp-authorinfo'] = 'About the author';
$txt['tp-showcomments'] = 'Show latest comments';

$txt['tp-inarticles'] = ' [Articles]';
$txt['tp-intopics'] = ' [Topics]';
$txt['tp-inboards'] = ' [Boards]';
$txt['tp-indownloads'] = ' [Downloads]';
$txt['tp-fullscreenshot'] = 'Show full picture';

$txt['tp-from'] = 'From ';
$txt['tp-newtag'] = 'Create new tag:';

$txt['tp-rates'] = 'rates';
$txt['tp-haverated'] = 'You have rated.';

$txt['tp-admin'] = 'TP Admin';
$txt['tp-settings'] = 'Settings';
$txt['tp-blocks'] = 'Blocks';
$txt['tp-modules'] = 'Modules';
$txt['tp-articles'] = 'Articles';
$txt['tp-newcomment'] = 'New Comment';
$txt['tp-cannotfetchfile'] = 'Text is unavailable.';
$txt['tp-fromcategory'] = 'posted in ';

$txt['tp-poster5'] = ' is no longer a member with this community.';
$txt['tp-poster6'] = 'The member has written ';
$txt['tp-poster7'] = ' articles.';

$txt['tp-toggle'] = 'Toggle panel';
$txt['tp-addarticle']='Add';
$txt['tp-none'] = '-none-';
$txt['tp-notallowed'] = 'Sorry, you are not allowed to view this article.';
$txt['tp-error'] = 'TP error';

$txt['tp-publish'] = 'Promote to frontpage';
$txt['tp-unpublish'] = 'Remove from frontpage';
$txt['tp-categories'] = 'Categories';
$txt['tp-panels'] = 'Panels';
$txt['tp-strays'] = 'Uncategorized';
$txt['tp-permissions'] = 'Permissions';
$txt['tp-menumanager'] = 'Menu Manager';
$txt['tp-submissions'] = 'Submissions';
$txt['tp-frontpage'] = 'Frontpage';
$txt['tp-myarticles'] = 'My articles';
$txt['tp_articles_help'] = '';
$txt['tp-notapproved'] = 'This article hasn\'t been approved yet.';
$txt['tp-noton'] = 'This article isn\'t active.';
$txt['tp-nocategory'] = 'This article isn\'t assigned yet.';
$txt['tp_modreports'] = 'Modreports:';

$txt['tp-blockoverview'] = 'Block Access';
$txt['tp-ungroupedmembers']='Ungrouped members';
$txt['tp-noarticlesfound'] = 'Sorry, but no articles were found.';
$txt['whoall_forum'] = 'Viewing the board index of <a href="' . $scripturl . '">' . $context['forum_name'] . '</a>.';
$txt['tp-showlatest'] = 'Show 50 latest';

$txt['tp-expired-start'] = 'The article is currently not published!';
$txt['tp-expired-start2'] = ' until ';

$txt['tp-nomodule'] = 'No output from module.';

// Permissions
$txt['permissiongroup_tp'] = 'TinyPortal';
$txt['permissiongroup_simple_tp'] = 'Manage TinyPortal';
?>