<?php
// Version: 2.1.0; TPortal

global $txt, $context, $scripturl;

// Permissions
$txt['permissiongroup_tp'] = 'TinyPortal';
$txt['permissiongroup_simple_tp'] = 'Manage TinyPortal';

$txt['permissionname_tp_settings'] = $txt['group_perms_name_tp_settings'] = 'Manage settings';
$txt['permissionhelp_tp_settings'] = 'Allows users to manage settings for TP.';
$txt['cannot_tp_settings'] = 'Sorry, you aren\'t allowed to manage TinyPortal settings.';

$txt['permissionname_tp_blocks'] = $txt['group_perms_name_tp_blocks'] = 'Manage blocks';
$txt['permissionhelp_tp_blocks'] = 'Allows users to manage blocks. Be careful with this permission as it allows creation of PHP blocks.';
$txt['cannot_tp_blocks'] = 'Sorry, you aren\'t allowed to manage blocks.';

$txt['permissionname_tp_articles'] = $txt['group_perms_name_tp_articles'] = 'Manage articles';
$txt['permissionhelp_tp_articles'] = 'Allows users to manage articles, categories for articles and the catlist.';
$txt['cannot_tp_articles'] = 'Sorry, you aren\'t allowed to manage articles.';

$txt['permissionname_tp_submithtml'] = $txt['group_perms_name_tp_submithtml'] = 'Can submit HTML articles';
$txt['permissionhelp_tp_submithtml'] = 'Allows users to write and submit HTML type of articles.';
$txt['cannot_tp_submithtml'] = 'Sorry, you aren\'t allowed to submit HTML articles.';

$txt['permissionname_tp_submitbbc'] = $txt['group_perms_name_tp_submitbbc'] = 'Can submit BBC articles';
$txt['permissionhelp_tp_submitbbc'] = 'Allows users to write and submit BBC type of articles.';
$txt['cannot_tp_submitbbc'] = 'Sorry, you aren\'t allowed to submit BBC articles.';

$txt['permissionname_tp_editownarticle'] = $txt['group_perms_name_tp_editownarticle'] = 'Can edit own articles';
$txt['permissionhelp_tp_editownarticle'] = 'Allows users to edit their own articles.';
$txt['cannot_tp_editownarticle'] = 'Sorry, you aren\'t allowed to edit your article.';

$txt['permissionname_tp_alwaysapproved'] = $txt['group_perms_name_tp_alwaysapproved'] = 'Article submissions and edits without approval';
$txt['permissionhelp_tp_alwaysapproved'] = 'Allows users to write articles that will be automatically approved. Subsequent updates will also not require admin approval.';
$txt['cannot_tp_alwaysapproved'] = 'Article submission or update needs to be approved first.';

$txt['permissionname_tp_artcomment'] = $txt['group_perms_name_tp_artcomment'] = 'Can comment on articles';
$txt['permissionhelp_tp_artcomment'] = 'Allows users to add comments to articles.';

$txt['permissionname_tp_can_admin_shout'] = $txt['group_perms_name_can_admin_shout'] = 'Manage TPshout';
$txt['permissionhelp_tp_can_admin_shout'] = 'Allows users to manage shouts.';
$txt['cannot_tp_can_admin_shout'] = 'Sorry, you aren\'t allowed to manage the Shoutbox.';

$txt['permissionname_tp_can_shout'] = $txt['group_perms_name_tp_can_shout'] = 'Can Post Shouts';
$txt['permissionhelp_tp_can_shout'] = 'Allows users to post in the shoutbox.';

$txt['permissionname_tp_dlmanager'] = $txt['group_perms_name_tp_dlmanager'] = 'Manage TPdownloads';
$txt['permissionhelp_tp_dlmanager'] = 'Allows users to visit and operate any part of the File manager admin screens.';
$txt['cannot_tp_dlmanager'] = 'Sorry, you aren\'t allowed to manage the File Manager module.';

$txt['permissionname_tp_dlupload'] = $txt['group_perms_name_tp_dlupload'] = 'Upload file';
$txt['permissionhelp_tp_dlupload'] = 'Allow you to upload files for the File manager';
$txt['cannot_tp_dlupload'] = 'Sorry, you aren\'t allowed to upload files to the File Manager.';

$txt['permissionname_tp_dlcreatetopic'] = $txt['group_perms_name_tp_dlcreatetopic'] = 'Create Download topic';
$txt['permissionhelp_tp_dlcreatetopic'] = 'Allows users to create a linked support topic, and also link the topic to the download. Uses pre-defined boards to choose from.';
$txt['cannot_tp_dlcreatetopic'] = 'Sorry, you aren\'t allowed to create support topics.';

$txt['permissionname_tp_can_list_images'] = $txt['group_perms_name_tp_can_list_images'] = 'Manage TPlistimages';
$txt['permissionhelp_tp_can_list_images'] = 'Allows users to remove uploaded images from the Tiny Portal Image Directory.';
$txt['cannot_tp_can_list_images'] = 'Sorry, you aren\'t allowed to manage article images.';

// Panels
$txt['bottom-tp-upshrink_description'] = 'Bottom Panel';
$txt['center-tp-upshrink_description'] = 'Upper Panel';
$txt['front-tp-upshrink_description'] = 'Frontpage';
$txt['left-tp-upshrink_description'] = 'Left Panel';
$txt['lower-tp-upshrink_description'] = 'Lower Panel';
$txt['right-tp-upshrink_description'] = 'Right Panel';
$txt['top-tp-upshrink_description'] = 'Top Panel';

// Who
$txt['tp-who-article'] = 'Viewing the Article &quot;<a href="%3$s?page=%2$s">%1$s</a>&quot;.';
$txt['tp-who-articles'] = 'Viewing Articles';
$txt['tp-who-article-search'] = 'Searching Articles.';
$txt['tp-who-categories'] = 'Viewing an article category page';
$txt['tp-who-category'] = 'Viewing the category page &quot;<a href="%3$s?cat=%2$s">%1$s</a>&quot;.';
$txt['tp-who-downloads'] = 'Viewing Downloads';
$txt['tp-who-forum-index'] = 'Viewing the Forum Index';
$txt['whoall_forum'] = 'Viewing the board index of <a href="' . $scripturl . '">' . (!empty($context['forum_name']) ? $context['forum_name'] : '*') . '</a>.';
$txt['whoall_tpmod_dl'] = 'Viewing the File Manager.';
$txt['whoall_tpmod_dlcat'] = 'Viewing a File category.';
$txt['whoall_tpmod_dlitem'] = 'Viewing a files item.';

// Articles
$txt['tp-author'] = 'Author';
$txt['tp-by'] = 'Written by';
$txt['tp-bycom'] = 'Comment by';
$txt['tp_rate'] = 'Rate it!';
$txt['tp-articlenotexist'] = 'This article is not available. You may not have permission to see the article, it isn\'t active, hasn\'t been approved yet, or simply does not exist.';
$txt['tp-articlessubmitted'] = 'New article(s): ';
$txt['tp-cannotcomment'] = 'You don\'t have permission to comment, or comments have been turned off for this article.';
$txt['tp-nolinkcomments'] = '(It is not allowed to post links in comments)';
$txt['tp-categorynotexist'] = 'The category doesn\'t exist.';
$txt['tp-categorynoarticles'] = 'This Category doesn\'t have any Articles assigned to it.';
$txt['tp-comments'] = 'Comments';
$txt['tp-comment'] = 'Comment';
$txt['tp-writecomment'] = 'Write Comment';
$txt['tp-confirmcommentdelete'] = 'Are you sure you want to delete this comment?';
$txt['tp-confirmdelete'] = 'Are you sure you want to delete this article?';
$txt['tp-delete'] = 'Delete';
$txt['tp-editarticle'] = 'Edit article';
$txt['tp-viewarticle'] = 'View article';
$txt['tp-incategory'] = ' in ';
$txt['tp-noimportarticle'] = 'Sorry, the article could not be loaded.';
$txt['tp-noname'] = '-no name-';
$txt['tp-permanent'] = 'Permanent?';
$txt['tp-print'] = 'Print';
$txt['tp-printgoback'] = 'Go back to article';
$txt['tp-ratingaverage'] = 'Rating: ';
$txt['tp-ratings'] = 'Ratings';
$txt['tp-ratingvotes'] = 'Rates: ';
$txt['tp-readmore'] = 'Read More';
$txt['tp-submit'] = 'Save';
$txt['tp-submitarticlebbc'] = 'Write BBC article';
$txt['tp-viewcat'] = 'Viewing a';
$txt['tp-viewcat2'] = 'category of articles.';
$txt['tp-viewpage'] = 'Viewing an';
$txt['tp-viewpage2'] = 'article.';
$txt['tp-views'] = 'Views';
$txt['tp-written'] = 'written';

// Blocks
$txt['block-upshrink_description'] = 'Collapse or expand block';
$txt['edit_description'] = 'Edit block contents';
$txt['tp-changetheme'] = 'Change';
$txt['tp-guest'] = 'Guest';
$txt['tp-guests'] = 'Guests';
$txt['tp-itemviews'] = 'Views';
$txt['tp-latest'] = 'Latest';
$txt['tp-loggedintime'] = 'Total Logged In:';
$txt['tp-mostonline'] = 'Online Ever';
$txt['tp-mostonline-today'] = 'Online Today';
$txt['tp-nothemeschosen'] = 'No themes are chosen';
$txt['tp-pm'] = 'PM: ';
$txt['tp-pm2'] = 'New: ';
$txt['tp-replies'] = 'Show replies';
$txt['tp-showownposts'] = 'Show own posts';
$txt['tp-showshouts'] = 'Show last';
$txt['tp-stats'] = 'Stats';
$txt['tp-submitarticle'] = 'Write HTML article';
$txt['tp-total'] = 'Total';
$txt['tp-unread'] = 'Show unread';
$txt['tp-users'] = 'Users';

// Search
$txt['tp-nosearchentered'] = 'Nothing to search for!';
$txt['tp-search'] = 'Search';
$txt['tp-searcharticles'] = 'Extended search';
$txt['tp-searcharticles2'] = 'Search Articles';
$txt['tp-searcharticleshelp'] = 'Search through all allowed articles.';
$txt['tp-searcharticleshelp2'] = '<span class="smalltext"><b>You can use the following operators:</b><br>
<B>+</B>&nbsp;&nbsp;include, the word must be present.<br>
<B>-</B>&nbsp;&nbsp;exclude, the word must not be present.<br>
<B>*</B>&nbsp;&nbsp;wildcard at the end of the word.<br><br>
Example: <i>+Morrissey +album +live  <b>OR</B> +Morrissey +album -live <b>OR</B> morris*</i></span>';
$txt['tp-searchdownloads'] = 'Search Downloads Manager';
$txt['tp-searchinbody'] = 'Search in article texts';
$txt['tp-searchintitle'] = 'Search in titles';

// Profile
$txt['articlesprofile'] = 'Articles';
$txt['articlesprofile2'] = 'All the articles the member has written. Note that articles currently not active or not approved cannot be viewed from here. These can be accessed from the My Articles page in TinyPortal';
$txt['downloadsprofile'] = 'Uploaded files';
$txt['downloadsprofile2'] = 'All the files uploaded in File Manager. Files not yet approved are shown in italic style.';
$txt['shoutboxprofile'] = 'View shouts';
$txt['shoutboxprofile2'] = 'All shouts made by the member.';
$txt['tp-category'] = 'Category';
$txt['tp-locked'] = 'Locked';
$txt['tp-more'] = 'More ';
$txt['tp-notlocked'] = 'Not Locked';
$txt['tp-prof_allarticles'] = 'Total number of articles written:';
$txt['tp-prof_alldownloads'] = 'Total number of submitted files:';
$txt['tp-prof_allshouts'] = 'Total number of shouts:';
$txt['tp-prof_approvdownloads'] = 'Not approved uploads.';
$txt['tp-prof_offarticles'] = 'Articles not currently active:';
$txt['tp-prof_offarticles2'] = 'All articles are marked as active.';
$txt['tp-prof_waitapproval1'] = 'This member has ';
$txt['tp-prof_waitapproval2'] = 'articles waiting approval.';
$txt['tpsummary'] = 'Portal Summary';
$txt['tp-wysiwygchoice'] = 'Use WYSIWYG editor';

// File Manager
$txt['tp-dlhaverated'] = 'You have rated this file.';
$txt['tp-dlmanageroff'] = 'The DL Manager module is not active.';
$txt['tp-dlsettings'] = 'Settings';
$txt['tp-dluploaded'] = 'New upload(s)';
$txt['tp-downloads'] = 'Downloads';
$txt['tp-downloadss1'] = 'Files';
$txt['tp-uploadedby'] = 'Uploaded by ';

// Shoutbox
$txt['shout!'] = 'Shout!';
$txt['tp-shout'] = 'Shout';
$txt['tp-shouts'] = 'Shouts:';
$txt['tp-shoutbox'] = 'TinyPortal Shoutbox';
$txt['tp-shoutboxitems'] = 'Edit/Remove last shouts';
$txt['tp-shout-history'] = 'History';
$txt['tp-shout-refresh'] = 'Refresh';
$txt['tpsummary_noshout'] = 'No shout messages found';
$txt['alert_shout_mention'] = 'You have been mentioned by {user_mention} in a {text}!';
$txt['alert_tp_comment_mention'] = 'You have been mentioned by {user_mention} in a {text}!';

// SCE Editor
$txt['editor_tp_floatleft'] = 'Insert float left div';
$txt['editor_tp_floatright'] = 'Insert float right div';

// Menu texts
$txt['tp-adminheader1'] = 'Settings & Frontpage';
$txt['tp_menuarticles'] = 'Articles and Categories';
$txt['tp-adminpanels'] = 'Panels and Blocks';
$txt['tp-menumanager'] = 'Menu manager';
$txt['custom_modules'] = 'TP Modules';

// Various
$txt['tp-forum'] = 'Forum';
$txt['tp-tphelp'] = 'TinyPortal';
$txt['tp-edit'] = 'Edit';
$txt['tp-send'] = 'Save';
$txt['tp-tpadmin'] = 'TP Admin';
$txt['tp-profilesection'] = 'TinyPortal';
$txt['tp-acronymdays'] = 'd ';
$txt['tp-acronymhours'] = 'h ';
$txt['tp-acronymminutes'] = 'm ';
$txt['tp_unapproved_members'] = 'Unapproved: ';
$txt['tp_maintenace'] = 'Maintenance Mode';
$txt['tp_articles_help'] = '';
$txt['tp_modreports'] = 'Modreports:';
$txt['tp-addarticle'] = 'Add';
$txt['tp-admin'] = 'TP Admin';
$txt['tp-article'] = 'Article';
$txt['tp-articles'] = 'Articles';
$txt['tp-articlesettings'] = 'Article settings';
$txt['tp-authorinfo'] = 'About the author';
$txt['tp-blockoverview'] = 'Block access';
$txt['tp-blocks'] = 'Blocks';
$txt['tp-cannotfetchfile'] = 'Text is unavailable.';
$txt['tp-categories'] = 'Categories';
$txt['tp-error'] = 'TP error';
$txt['tp-expired-start'] = 'This article is not published!';
$txt['tp-expired-start2'] = ' until ';
$txt['tp-from'] = 'From ';
$txt['tp-fromcategory'] = 'Posted in ';
$txt['tp-frontpage'] = 'Frontpage';
$txt['tp-fullscreenshot'] = 'Show full picture';
$txt['tp-generalsettings'] = 'General settings';
$txt['tp-haverated'] = 'You have rated.';
$txt['tp-hide'] = 'Hide';
$txt['tp-miscblocks'] = 'Menus';
$txt['tp-morecategories'] = 'More';
$txt['tp-myarticles'] = 'My articles';
$txt['tp-newcomment'] = 'New comment';
$txt['tp-no-sa-url'] = 'No subaction found in url';
$txt['tp-no-sa-list'] = 'This is not a valid subaction';
$txt['tp-noadmin'] = 'Sorry, you are not allowed to access this TinyPortal admin page.';
$txt['tp-noarticlesfound'] = 'Sorry, but no articles were found.';
$txt['tp-nocategory'] = 'This article isn\'t assigned to a category yet.';
$txt['tp-none'] = '-none-';
$txt['tp-notallowed'] = 'Sorry, you are not allowed to view this article.';
$txt['tp-notapproved'] = 'This article hasn\'t been approved yet.';
$txt['tp-noton'] = 'This article isn\'t active.';
$txt['tp-panels'] = 'Panels';
$txt['tp-permissions'] = 'Permissions';
$txt['tp-poster1'] = ' registered at ';
$txt['tp-poster2'] = ' on ';
$txt['tp-poster3'] = ' and has posted ';
$txt['tp-poster4'] = ' posts in the boards since then. Last visit was  ';
$txt['tp-poster5'] = ' is no longer a member with this community.';
$txt['tp-poster6'] = 'The member has written ';
$txt['tp-poster7'] = ' articles.';
$txt['tp-publish'] = 'Promote to frontpage';
$txt['tp-quicklist'] = 'My Images Quicklist';
$txt['tp-quicklist2'] = 'The images below are small thumbnails of the original pictures. By dragging them into the editor the picture is inserted into the editor.';
$txt['tp-rates'] = 'rates';
$txt['tp-settings'] = 'Settings';
$txt['tp-show'] = 'Show';
$txt['tp-showcomments'] = 'Show latest comments';
$txt['tp-showlatest'] = 'Show 50 latest';
$txt['tp-showrelated'] = 'Related';
$txt['tp-strays'] = 'Uncategorized';
$txt['tp-submissions'] = 'Submissions';
$txt['tp-toggle'] = 'Toggle panel';
$txt['tp-ungroupedmembers'] = 'Ungrouped members';
$txt['tp-unpublish'] = 'Remove from frontpage';
$txt['tp-uploadfile'] = 'Upload a new image into the quick-list: ';

$txt['tp-bytes'] = ' bytes';
$txt['tp-kb'] = ' Kb';
$txt['tp-mb'] = ' Mb';
$txt['tp-gb'] = ' Gb';
?>
