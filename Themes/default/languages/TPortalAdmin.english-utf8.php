<?php
/**
 * @package TinyPortal
 * @version 2.1.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2020 - The TinyPortal Team
 *
 */

// Menu
$txt['tp-adminheader1'] = 'Settings & Frontpage';
$txt['tp-settings'] = 'Settings';
$txt['tp-frontpage'] = 'Frontpage';
$txt['tp-articles'] = 'Articles';
$txt['tp-tabs4'] = 'Submissions';
$txt['tp-adminpanels'] = 'Panels and Blocks';
$txt['tp-menumanager'] = 'Menu manager';
$txt['tp-addmenu'] = 'Add menu';
$txt['custom_modules'] = 'TP Modules';
$txt['tp-dlmanager'] = 'TPDownloads';
 
// Settings page
$txt['tp-generalsettings'] = 'General settings';
$txt['tp-helpsettings'] = 'Here you can tweak various general settings for the portal.';
$txt['tp-formres'] = 'Select non responsive themes';
$txt['tp-deselectthemes'] = 'Deselect all themes';
$txt['tp-frontpagetitle'] = 'Use a custom browser tab title for your Frontpage';
$txt['tp-frontpagetitle2'] = 'This lets you select a specific name to display on the browser tab for your Frontpage (leave the textfield empty for no title).';
$txt['tp-redirectforum'] = 'How to redirect after login';
$txt['tp-redirectforum1'] = 'Redirect to forum';
$txt['tp-redirectforum2'] = 'Redirect to frontpage';
$txt['tp-hideadminmenu'] = 'Hide Tinyportal menu option';
$txt['tp-hideadminmenudesc'] = 'By default a menu option for Tinyportal is added to the menu. This option will automatically show for admins, users that have the permission to submit articles or all users if the downloads module is set to active. This setting will remove the Tinyportal option from the menu for all users except admins.';
$txt['tp-useroundframepanels'] = 'Use the roundframe style for left/right panels';
$txt['tp-hidecollapse'] = 'Allow the panels to be collapsed';
$txt['tp-hideediticon'] = 'Hide the block edit link';
$txt['tp-uselangoption'] = 'Use language visibility option for blocks';
$txt['tp-use_groupcolor'] = 'Use membergroup color for user names throughout the forum';
$txt['tp-use_groupcolordesc'] = 'This setting will activate the use of the member group colour for user names throughout the forum. Users names will be displayed in the forum in the color of the primary user group that a member is assigned to. Note that there is a similar settings in the shoutbox that will only activate this in the shoutbox: The general TP setting takes preference over the shoutbox setting.';
$txt['tp-maxrating'] = 'Max rating';
$txt['tp-stars'] = 'Display stars instead of numbers';

$txt['tp-useoldsidebar'] = 'Use old sidebar for admin';
$txt['tp-admin_showblocks'] = 'Admin can see all blocks';
$txt['tp-uselangoption2'] = 'NB! The language visibility setting is turned off. Note that the language choices will not work unless its turned on.';
$txt['tp-imageproxycheck'] = 'Perform the final image proxy check';
$txt['tp-imageproxycheckdesc'] = 'This defaults to ON and is only effective when SMF Image Proxy is Enabled. Deactivating this option may resolve conflicts with certain Mods, but will deactivate the image proxy for http images in PHP articles and HTML blocks.';
$txt['tp-fulltextsearch'] = 'Enable TinyPortal Full Text search';
$txt['tp-fulltextsearchdesc'] = 'This setting will enable advanced search options for the Article Search Functionality: this allows for the use of multiple search terms in the TP search function as well as the use of complex search operators. See for explanation the article on Search functionality. If this is set to \'No\' the search can only be done for a single search term.';
$txt['tp-disabletemplateeval'] = 'Disable the PHP eval function for templates';
$txt['tp-disabletemplateevaldesc'] = 'Disable the PHP eval function for rending article templates';
$txt['tp-imageuploadpath'] = 'Path where TinyPortal images are uploaded to on your server';
$txt['tp-imageuploadpathdesc'] = 'File path where the images uploaded via TinyPortal are stored';
$txt['tp-blockcodeuploadpath'] = 'Path where TinyPortal blockcodes are uploaded to on your server';
$txt['tp-blockcodeuploadpathdesc'] = 'File path where the blockcodes uploaded via TinyPortal are stored';
$txt['tp-downloaduploadpath'] = 'Path where TinyPortal downloads are uploaded to on your server';
$txt['tp-downloaduploadpathdesc'] = 'File path where the downloads uploaded via TinyPortal are stored';
$txt['tp-copyrightremoval'] = 'TinyPortal Copyright Removal';
$txt['tp-copyrightremovaldesc'] = 'Enter your unique key to remove the TinyPortal Copyright Notice';

// Frontpage
$txt['tp-frontpage_settings'] = 'Frontpage settings';
$txt['tp-helpfrontpage'] = 'The frontpage settings can be tweaked here.';
$txt['tp-whattoshow'] = 'What to display on frontpage';
$txt['tp-selectedforum'] = 'Promoted topics only';
$txt['tp-selectbothforum'] = 'Promoted topics + articles - sorted on date';
$txt['tp-onlyforum'] = 'Only forum-topics';
$txt['tp-bothforum'] = 'Forum-topics and articles - sorted on date';
$txt['tp-onlyarticles'] = 'Only articles';
$txt['tp-singlepage'] = 'Featured article with frontpanel';
$txt['tp-frontblocks'] = 'Frontpanel only';
$txt['tp-boardindex'] = 'Go directly to forum index';
$txt['tp-frontblockoption'] = 'How frontpanel will be shown with content';
$txt['tp-frontblocksingle'] = 'Hide frontpanel, unless otherwise selected.';
$txt['tp-frontblockfirst'] = 'Frontpanel will be added before content';
$txt['tp-frontblocklast'] = 'Frontpanel will be added after content';
$txt['tp-frontpageoptions'] = 'Additional panels to display on frontpage';
$txt['tp-frontpageoptionsdesc'] = 'For frontpage only, this overrides any panel that has been turned on, but is also overridden by an article\'s \'single page\' option.';
$txt['tp-displayleftpanel'] = 'Display left panel';
$txt['tp-displayrightpanel'] = 'Display right panel';
$txt['tp-displaytoppanel'] = 'Display top panel';
$txt['tp-displaycenterpanel'] = 'Display upper panel';
$txt['tp-displaylowerpanel'] = 'Display lower panel';
$txt['tp-displaybottompanel'] = 'Display bottom panel';
$txt['tp-frontpage_layout'] = 'Articles layout for the frontpage';
$txt['tp-articlelayouts'] = 'Layout type';
$txt['tp-catlayout1'] = 'normal articles';
$txt['tp-catlayout2'] = '1st normal + avatars';
$txt['tp-catlayout4'] = 'articles + icons';
$txt['tp-catlayout8'] = 'articles + icons2';
$txt['tp-catlayout6'] = 'simple articles';
$txt['tp-catlayout5'] = 'normal + links';
$txt['tp-catlayout3'] = '1st avatar + links';
$txt['tp-catlayout9'] = 'just links';
$txt['tp-catlayout7'] = 'use custom template';
$txt['reset_custom_template_layout'] = 'Custom template code';
$txt['reset_custom_template_layoutdesc'] = 'In this code block you can completely define your own layout according to your requirements. Some understanding of HTML coding is required though. To activate this code block it is required that you select \'Use custom template\' option for Layout. To reset the custom layout to the default setting, clear all text in the box and save.';
$txt['tp-numberofposts'] = 'Number of articles/topics to display on frontpage';
$txt['tp-sortingoptions'] = 'Sorting order for articles on frontpage';
$txt['tp-sortingoptionsdesc'] = 'Sorting order for articles on frontpage: Note that this setting only works if display is set to \'Only articles\' on the frontpage.';
$txt['tp-sortoptions1'] = 'Sort by date';
$txt['tp-sortoptions2'] = 'Sort by author';
$txt['tp-sortoptions3'] = 'Sort by position';
$txt['tp-sortoptions4'] = 'Sort by id number';
$txt['tp-sortdirection1'] = 'Descending';
$txt['tp-sortdirection2'] = 'Ascending';
$txt['tp-allowguests'] = 'Allow full display of forum-topics on the frontpage';
$txt['tp-allowguestsdesc'] = 'Allow guests and members to see forum-topics on the frontpage, even if they are not allowed to see the boards themselves? [Yes]: users will get to see it, regardless of your SMF setting. [No]: users will \'not\' get to see it, regardless of your SMF setting.';
$txt['tp-showforumposts'] = 'Display forum-topics on frontpage from';
$txt['tp-lengthofposts'] = 'Number of characters to display per forum-topic';
$txt['tp-forumposts_avatar'] = 'Show avatars in forum-topics';
$txt['tp-forumposts_avatardesc'] = 'This setting will make the member avatars show in forum topics on the Frontpage of your forum. This will only have an effect if you are using one of the following layouts for your Frontpage: normal articles, 1st normal-avatars or normal-links.';
$txt['tp-useattachment'] = 'Use first post attachment preview as icon';
$txt['tp-useattachmentdesc'] = 'This setting will make the an image of the first attachment of the forum topic be used as the icon for the post on the frontpage of your forum. This will only have an effect if you are using one of the following layouts for your Frontpage: articles-icons or articles-icons2.';
$txt['tp-boardnews_divheader'] = 'Header style for frontpage topics';
$txt['tp-boardnews_headerstyle'] = 'Title style for frontpage topics';
$txt['tp-boardnews_divbody'] = 'Frame style for frontpage topics';

// Navigation links
$txt['tp-tabs5'] = 'Categories';
$txt['tp-tabs6'] = 'Add category';
$txt['tp-uncategorised'] = 'Uncategorized';
$txt['tp-tabs2'] = '+HTML';
$txt['tp-tabs3'] = '+PHP';
$txt['tp-addbbc'] = '+BBC';
$txt['tp-addimport'] = '+External';
$txt['tp-tabs11'] = 'Cat List';

// Categories page
$txt['tp-artcat'] = 'Article categories';
$txt['tp-helpcats'] = 'These are your article categories and sub-categories with the number of articles each category holds. Selecting a category will bring you the edit category page. The actions icons allow you to quickly access some category functions.';

// Article settings
$txt['tp-articlesettings'] = 'Article settings';
$txt['tp-helpartsettings'] = 'These settings apply to ALL TinyPortal articles.';
$txt['tp-usewysiwyg'] = 'Use the WYSIWYG editor';
$txt['tp-editorheight'] = 'Height of WYSIWYG editor';
$txt['tp-usedragdrop'] = 'Enable the DragDrop feature in the WYSIWYG Editor';
$txt['tp-hidearticle-link'] = 'Display edit link in articles';
$txt['tp-printarticles'] = 'Display print link in articles';
$txt['tp-allow-links-article-comments'] = 'Allow article comments to contain links';
$txt['tp-iconmaxsize'] = 'Max image upload size';
$txt['tp-iconsize'] = 'Image size for article icons (width x height)';
$txt['tp-hidearticle-facebook'] = 'Hide Facebook button';
$txt['tp-hidearticle-twitter'] = 'Hide Twitter button';
$txt['tp-hidearticle-reddit'] = 'Hide Reddit button';
$txt['tp-hidearticle-digg'] = 'Hide Digg button';
$txt['tp-hidearticle-delicious'] = 'Hide Delicious button';
$txt['tp-hidearticle-stumbleupon'] = 'Hide Stumbleupon button';

// Articles overview
$txt['tp-helparticles'] = 'These are your article categories and sub-categories with the number of articles each category holds. Selecting a category will bring you the the articles listing for that category. The actions icons allow you to view the category page or directly edit the category settings.';
$txt['tp-helparticles2'] = 'This page shows all list of all articles in the chosen category. Selecting an article will bring you the the edit article page. The actions icons allow you to quickly toggle some article settings. Note that if an article is locked the edit option is disabled until the lock is removed.';
$txt['tp-setfrontpage'] = 'Toggle show on frontpage';
$txt['tp-setsticky'] = 'Toggle sticky/non-sticky';
$txt['tp-setlock'] = 'Toggle unlocked/locked';
$txt['tp-islocked'] = 'This article is locked for editing';
$txt['tp-featured'] = 'Toggle Featured on/off';
$txt['tp-editarticleoptions2'] = 'Options';
$txt['tp-articleconfirmdelete'] = 'Are you sure you want to delete this article?';
$txt['tp-select'] = 'Select';
$txt['tp-inboards2'] = 'Boards:';
$txt['tp-sort-on-position'] = 'Sort on position';
$txt['tp-sort-on-subject'] = 'Sort on subject';
$txt['tp-sort-on-author'] = 'Sort on author';
$txt['tp-sort-on-date'] = 'Sort on date';
$txt['tp-sort-on-active'] = 'Sort on active';
$txt['tp-sort-on-frontpage'] = 'Sort on frontpage';
$txt['tp-sort-on-sticky'] = 'Sort on sticky';
$txt['tp-sort-on-locked'] = 'Sort on locked';
$txt['tp-sort-on-type'] = 'Sort on type';

// Edit article
$txt['tp-preview'] = 'View article';
$txt['tp-arttitle'] = 'Title';
$txt['tp-shortname_article'] = 'Short name';
$txt['tp-shortname_articledesc'] = 'Here you can create \'meaningful URLs\': specify a short text to be used in the page URL display. Without a value the url will be displayed as: .../index.php?page=20. if a value is added (for example \'Articles\') the url will show as: .../index.php?page=Articles';
$txt['tp-importarticle'] = 'Path to external article';
$txt['tp-useintro'] = 'Use intro';
$txt['tp-useintrodesc'] = 'Here you can specify if an Intro text for the article is needed. This text can be displayed with a Read more... link on the category page. When you set this option to yes, you should also maintain an Intro text. ';
$txt['tp-introtext'] = 'Intro text';
$txt['tp-addcategory'] = 'Add category';
$txt['tp-author'] = 'Author';
$txt['tp-assignnewauthor'] = 'Author ID(change to re-assign to another)';
$txt['tp-created'] = 'Created at';
$txt['tp-published'] = 'Publish';
$txt['tp-pub_start'] = 'from';
$txt['tp-pub_end'] = 'until';
$txt['tp-notset'] = '- not set -';
$txt['tp-category'] = 'Category';
$txt['tp-editcategory'] = 'Edit Article Category';
$txt['tp-switchmode'] = 'Type of article';
$txt['tp-gohtml'] = 'HTML article';
$txt['tp-gophp'] = 'PHP article';
$txt['tp-gobbc'] = 'BBC article';
$txt['tp-goimport'] = 'External article';
$txt['tp-articleoff'] = 'Article is NOT active';
$txt['tp-articleon'] = 'Article is active';
$txt['tp-illustration'] = 'Article icon <span class="smalltext">(Preview)</span>';
$txt['tp-illustration2'] = 'Choose another image';
$txt['tp-uploadicon'] = 'Upload a new icon ';
$txt['tp-articleoptions'] = 'Viewing options';
$txt['tp-details'] = 'Details';
$txt['tp-articleoptions2'] = 'Display title';
$txt['tp-articleoptions13'] = 'Display posted in category';
$txt['tp-articleoptions3'] = 'Display author';
$txt['tp-articleoptions1'] = 'Display date';
$txt['tp-articleoptions12'] = 'Display all articles in category';
$txt['tp-articleoptions4'] = 'Display breadcrumb navigation';
$txt['tp-articleoptions14'] = 'Display comments underneath article';
$txt['tp-articleoptions15'] = 'Allow to comment';
$txt['tp-articleoptions5'] = 'Display top';
$txt['tp-articleoptions16'] = 'Do not collapse comments initially';
$txt['tp-articleoptions17'] = 'Display number of views';
$txt['tp-articleoptions18'] = 'Display ratings';
$txt['tp-articleoptions19'] = 'Allow to rate';
$txt['tp-articleoptions24'] = 'Use settings from category instead!';
$txt['tp-articleoptions8'] = 'Display left panel';
$txt['tp-articleoptions23'] = 'Width left panel (leave empty for default)';
$txt['tp-articleoptions7'] = 'Display right panel';
$txt['tp-articleoptions22'] = 'Width right panel (leave empty for default)';
$txt['tp-articleoptions10'] = 'Display top panel';
$txt['tp-articleoptions6'] = 'Display upper panel';
$txt['tp-articleoptions11'] = 'Display lower panel';
$txt['tp-articleoptions9'] = 'Display bottom panel';
$txt['tp-others'] = 'Others';
$txt['tp-articleoptions20'] = 'Do not use SMF templates';
$txt['tp-articleoptions21'] = 'Display author avatar/info';
$txt['tp-showsociallinks'] = 'Show Social Bookmarks buttons';
$txt['tp-chosentheme'] = 'Use only this theme ';
$txt['tp-articleheaders'] = 'Header code to be used in articles without any template/theme code';

// Uncategorized articles
$txt['tp-uncategorised2'] = 'Uncategorized articles';
$txt['tp-helpstrays'] = 'Articles that have not yet been assigned to a category. The article category determines what member groups may actually view the article: as long as no category is assigned the article will not be visible to any guest or member.';
$txt['tp-createnew'] = 'Assign selected to a new category -> ';
$txt['tp-createnew2'] = 'Approve and assign selected to a new category -> ';
$txt['tp-createnewcategory'] = 'New category';
$txt['tp-assignto'] = 'Assign to ';
$txt['tp-approveto'] = 'Approve and assign to ';

// Submissions
$txt['tp-submissionsettings'] = 'Submissions';
$txt['tp-helpsubmissions'] = 'Articles that have been submitted or updated by members and need to be approved by an administrator. Unless a member is part of a membergroup that has permission to submit or update without approval all new articles and article updates must be approved prior to publication.';
$txt['tp-nosubmissions'] = 'Currently there are no submissions awaiting approval.';

// Icons
$txt['tp-adminicons7'] = 'Article icons';
$txt['tp-adminiconsinfo'] = 'PNG, JPG or GIF, Max-size: ' .$context['TPortal']['icon_max_size']. ' KB. Images will be resized so the shortest side is ' .$context['TPortal']['icon_width']. ' px, while retaining aspect ratio. Note that the top area of the image will be used as article icon.';
$txt['tp-adminicons6'] = 'Upload a new article icon';

// Add-Edit Article Category
$txt['tp-editcategory'] = 'Edit';
$txt['tp-viewcategory'] = 'View the category';
$txt['tp-addsubcategory'] = 'Add a category under this one';
$txt['tp-copycategory'] = 'Make a copy of this category';
$txt['tp-helpaddcategory'] = 'Add a new article category. Next screen will allow specifics about it.';
$txt['tp-none2'] = '* not assigned *';
$txt['tp-parent'] = 'Parent';
$txt['tp-nocat'] = '-no parent-';
$txt['tp-sorting'] = 'Sort options';
$txt['tp-articlecount'] = 'Articles per page';
$txt['tp-catlayouts'] = 'Articles layout for the category';
$txt['tp-showchilds'] = 'Display any child categories?';
$txt['tp-allowedgroups'] = 'Membergroups that can see this category';
$txt['tp-confirmcat1'] = 'Are you sure you want to delete this category ?';
$txt['tp-confirmcat2'] = '(Note that all articles belonging to this category will NOT be deleted - they will instead end up in the Stray Articles section)';

// Cat list
$txt['tp-clist'] = 'Which categories should appear in the Category tabs?';

// Panels admin
$txt['tp-panelsettings'] = 'Panel Settings';
$txt['tp-helppanels'] = 'Blocks are located in panels and each panel has its own set of settings.';
$txt['tp-panel'] = 'Panel';
$txt['tp-hidebarsall'] = 'Hide panels when in these sections';
$txt['tp-hidebarsadminonly'] = 'Hide panels in Admin section';
$txt['tp-hidebarsprofile'] = 'Hide panels in Profile';
$txt['tp-hidebarspm'] = 'Hide panels in Personal Messages';
$txt['tp-hidebarsmemberlist'] = 'Hide panels in Memberlist';
$txt['tp-hidebarssearch'] = 'Hide panels in Search';
$txt['tp-hidebarscalendar'] = 'Hide panels in Calendar';
$txt['tp-hidebarscustom'] = 'Hide panels in these custom actions';
$txt['tp-hidebarscustomdesc'] = 'This setting will indeed \'hide\' the panels per specified custom section. Separate the actions with comma: For example: \'gallery,arcade,shop\'';
$txt['tp-padding_between'] = 'Padding between panels';
$txt['tp-inpixels'] = 'in pixels';
$txt['tp-panelwidth'] = 'Width of this panel';
$txt['tp-useleftpanel'] = 'Use left panel?';
$txt['tp-userightpanel'] = 'Use right panel?';
$txt['tp-usetoppanel'] = 'Use top panel?';
$txt['tp-usecenterpanel'] = 'Use upper panel?';
$txt['tp-uselowerpanel'] = 'Use lower panel?';
$txt['tp-usebottompanel'] = 'Use bottom panel?';
$txt['tp-hide_leftbar_forum'] = 'Hide left panel when in forum?';
$txt['tp-hide_rightbar_forum'] = 'Hide right panel when in forum?';
$txt['tp-hide_topbar_forum'] = 'Hide top panel when in forum?';
$txt['tp-hide_centerbar_forum'] = 'Hide upper panel when in forum?';
$txt['tp-hide_lowerbar_forum'] = 'Hide lower panel when in forum?';
$txt['tp-hide_bottombar_forum'] = 'Hide bottom panel when in forum?';
$txt['tp-vertical'] = 'Display the blocks vertically(default)';
$txt['tp-horisontal'] = 'Display the blocks horizontally';
$txt['tp-horisontal2cols'] = 'Display the blocks in 2 columns';
$txt['tp-horisontal3cols'] = 'Display the blocks in 3 columns';
$txt['tp-horisontal4cols'] = 'Display the blocks in 4 columns';
$txt['tp-grid'] = 'Display the blocks according to a grid';
$txt['tp-blockwidth'] = '<b>Force</b> this width on each block (use 00px or 00%)';
$txt['tp-blockheight'] = '<b>Force</b> this height on each block (use 00px or 00%)';
$txt['tp-panelstylehelp'] = 'Default style for the blocks in the panel';
$txt['tp-panelstylehelpdesc'] = 'This setting determines the default style used for displaying blocks in this panel. If a specific style is chosen for a block in the block settings, the block style will take priority over any panel style chosen here.';

// Navigation links called from source files
$txt['tp-admin'] = 'TP Admin';
$txt['tp-left'] = 'Left';
$txt['tp-right'] = 'Right';
$txt['tp-top'] = 'Top';
$txt['tp-center'] = 'Upper';
$txt['tp-front'] = 'Front';
$txt['tp-bottom'] = 'Bottom';
$txt['tp-lower'] = 'Lower';
$txt['tp-allpanels'] = 'Panels';
$txt['tp-allblocks'] = 'Blocks';
$txt['tp-addarticle'] = 'Add article';
$txt['tp-guests'] = 'Guests';
$txt['tp-adminicons'] = 'Icons';
$txt['tp-permissions'] = 'Permissions';
$txt['tp-editarticle'] = 'Edit article';

// Header, Links & Help descriptions
$txt['tp-articlehelp'] = 'Articles can be created, either as HTML, PHP, BBC, or even import one. You can edit them and also the visual options for each article. They will allow you to turn on/off many visual attributes of the article. Setting a specific theme is also possible. In addition you create and mange categories for articles here.';
$txt['tp-articledesc1'] = 'Manage your articles. You can create HTML, PHP, BBC, or even import an article.';
$txt['tp-articledesc2'] = 'Manage the categories in which your articles can be placed.';
$txt['tp-articledesc3'] = 'Manage the settings for all created and existing articles.';
$txt['tp-articledesc4'] = 'Manage submissions for all articles. Decide who can submit articles, and approve the ones you want.';
$txt['tp-articledesc5'] = 'Manage the icons and pictures used by articles and specfic article layouts.';
$txt['tp-settingdesc1'] = 'Administrate your TinyPortal installation from here.';
$txt['tp-frontpagedesc1'] = 'All of the settings here control everything about what appears on your frontpage.';
$txt['tp-paneldesc1'] = 'Decide how your panels should display its blocks, how wide you want it, and if you want it hidden in specfic areas or not.';
$txt['tp-blocksdesc1'] = 'Decide which panel you want specfic blocks to to show up in, toggle them on or off, or quick edit your blocks from here.';
$txt['tp-adminonly'] = 'You have no access. Only Admins can view this section.';
$txt['tp-blockfailure'] = 'Block does not exist';
$txt['tp-helpmenuitems'] = 'Here you can create and change the menu items for the menu. The menu item types available are: Category, Article, Link, Header, Spacer or Menu button (only in the <i>Internal</i> menu)<br>';

// Blocks admin
$txt['tp-blocks'] = 'Blocks';
$txt['tp-blocksettings'] = 'Block Settings';
$txt['tp-addleftblock'] = 'Add left block';
$txt['tp-addrightblock'] = 'Add right block';
$txt['tp-addtopblock'] = 'Add top block';
$txt['tp-addcenterblock'] = 'Add upper block';
$txt['tp-addfrontblock'] = 'Add front page block';
$txt['tp-addlowerblock'] = 'Add lower block';
$txt['tp-addbottomblock'] = 'Add bottom block';
$txt['tp-choosepanel'] = 'Choose panel';
$txt['tp-leftpanel'] = 'Left Panel';
$txt['tp-rightpanel'] = 'Right Panel';
$txt['tp-toppanel'] = 'Top Panel';
$txt['tp-centerpanel'] = 'Upper Panel';
$txt['tp-frontpanel'] = 'Front Panel';
$txt['tp-lowerpanel'] = 'Lower Panel';
$txt['tp-bottompanel'] = 'Bottom Panel';
$txt['tp-chooseblock'] = 'Choose type of block';
$txt['tp-blocktype0'] = '- not set -';
$txt['tp-blocktype18'] = 'Article: single';
$txt['tp-blocktype19'] = 'Articles in a category';
$txt['tp-blocktype14'] = 'Article/download stats functions';
$txt['tp-blocktype5'] = 'Code: BBC';
$txt['tp-blocktype11'] = 'Code: HTML/Javascript';
$txt['tp-blocktype10'] = 'Code: PHP';
$txt['tp-blocktype9'] = 'Menu';
$txt['tp-blocktype2'] = 'News';
$txt['tp-blocktype8'] = 'Shoutbox';
$txt['tp-blocktype6'] = 'Online';
$txt['tp-blocktype12'] = 'Recent topics';
$txt['tp-blocktype15'] = 'RSS';
$txt['tp-blocktype4'] = 'Search';
$txt['tp-blocktype16'] = 'Sitemap';
$txt['tp-blocktype13'] = 'SSI functions';
$txt['tp-blocktype3'] = 'Stats';
$txt['tp-blocktype7'] = 'Theme selector';
$txt['tp-blocktype1'] = 'User';
$txt['tp-chooseblocktype'] = '..or use existing blockcode';
$txt['tp-chooseblockcopy'] = '..or copy from existing block.';

// Edit block
$txt['tp-blockstylehelp'] = 'Choose style for the block';
$txt['tp-blockstylehelpdesc'] = 'This setting determines the style used for displaying the block. If a specific style is chosen for a block, the block style will take priority over the panel style.';
$txt['tp-adminshowblocks'] = 'Let admin show all blocks?';
$txt['tp-blocksusepaneltyle'] = 'Use Panel style';
$txt['tp-blockframehelp'] = 'Frame options';
$txt['tp-useframe'] = 'Use frame and title style from theme';
$txt['tp-useframe2'] = 'Use frame style, but not title style';
$txt['tp-usetitle'] = 'Use just the title style';
$txt['tp-noframe'] = 'Do not use title/frame styles';
$txt['tp-allowupshrink'] = 'Allow block to collapse';
$txt['tp-notallowupshrink'] = 'Do not allow block to collapse';
$txt['tp-membergrouphelp'] = 'Choose the membergroups that will able to see this block.';
$txt['tp-membergrouphelpdesc'] = 'Choose your membergroups that will able to see this block. Note that if none are chosen it will only display it to admin.';
$txt['tp-membergrouptext'] = 'Choose your membergroup access after the block is saved. Blocks are always set to OFF upon creation.';
$txt['tp-editgrouphelp'] = 'Choose the membergroups that can edit this block.';
$txt['tp-editgrouphelpdesc'] = 'Choose your extra membergroups that can edit this block only. Note that \'manage_blocks\' and \'admin\' permission will always give this right regardless.';
$txt['tp-langhelp'] = 'Block title per language. ';
$txt['tp-langhelpdesc'] = 'Here you can add a custom block title for any installed language - except for default. ';
$txt['tp-lang'] = 'Block visible for these languages';
$txt['tp-langdesc'] = 'Upon installation of Tinyportal the Language visibility option will be switched OFF by default. When you access the block settings for a block you will see the language visibility option with a warning message, pointing out that the  language visibility setting is off. As long as the setting is off the blocks will show regardless of the user language. Language visibility is an additional setting for controlling when blocks are visible in the panels: the settings that choose where the block should appear will always be respected. If the settings do not allow the block to show, setting language visibility will also not make it show.';
$txt['tp-access2help'] = 'Choose where the block should appear.';
$txt['tp-actions'] = 'Actions';
$txt['tp-allpages'] = 'Display on all pages and sections';
$txt['tp-forumall'] = 'All Forum related sections';
$txt['tp-forumfront'] = 'Board Index';
$txt['tp-customactions'] = 'Custom actions (format: action1,action2) - will be added to actions list above. To remove, simply uncheck.';
$txt['tp-boards'] = 'Boards';
$txt['tp-allboards'] = 'Display on all boards';

// Block overview
$txt['tp-addblock'] = 'Add block';
$txt['tp-activate'] = 'Activate';
$txt['tp-activated'] = 'Activated';
$txt['tp-sortdown'] = 'Move down';
$txt['tp-sortup'] = 'Move up';
$txt['tp-move'] = 'Move Block';
$txt['tp-moveright'] = 'Move to the right panel';
$txt['tp-moveleft'] = 'Move to the left panel';
$txt['tp-moveup'] = 'Move to top panel';
$txt['tp-movecenter'] = 'Move to upper panel';
$txt['tp-movefront'] = 'Move to front panel';
$txt['tp-movelower'] = 'Move to lower panel';
$txt['tp-movedown'] = 'Move to bottom panel';
$txt['tp-editsave'] = 'Edit - Save';
$txt['tp-leftsideblocks'] = 'Left Panel Blocks';
$txt['tp-rightsideblocks'] = 'Right Panel Blocks';
$txt['tp-topsideblocks'] = 'Top Panel Blocks';
$txt['tp-centersideblocks'] = 'Upper Panel Blocks';
$txt['tp-frontsideblocks'] = 'Front Panel Blocks';
$txt['tp-lowersideblocks'] = 'Lower Panel Blocks';
$txt['tp-bottomsideblocks'] = 'Bottom Panel Blocks';
$txt['tp-blockconfirmdelete'] = 'Are you sure you want to delete this block?';
$txt['tp-panelclosed'] = 'Note: this panel has been turned off in panel settings.';
$txt['tp-noblocks'] = 'There are no blocks in this panel.';
$txt['tp-noaccess'] = 'This block is not set to display on any page or section yet.';
$txt['tp-editblock'] = 'Edit Block';
$txt['tp-editblocks'] = 'Blocks';
$txt['tp-blockcodes'] = 'Insert Block Code Snippet';
$txt['tp-blockcodes_overwrite'] = 'Overwrite?';
$txt['tp-text'] = '-text-';
$txt['tp-recent'] = 'Recent';
$txt['tp-no_title'] = '-no title-';
$txt['tp-blocknotice'] = 'NB! The type of block has changed.<br>Please save first to see the new options!';

// Block Types
$txt['tp-userbox1'] = 'Avatar (if any)';
$txt['tp-userbox2'] = 'Logged-in time';
$txt['tp-userbox3'] = 'Time';
$txt['tp-userbox4'] = 'Unread replies/unread posts';
$txt['tp-userbox5'] = 'Member Stats';
$txt['tp-userbox6'] = 'Online users';
$txt['tp-userbox7'] = 'Forum Stats';
$txt['tp-module7'] = 'Articles: newest additions';
$txt['tp-module8'] = 'Articles: most viewed';
$txt['tp-module9'] = 'Articles: most comments';
$txt['tp-ssi-calendar'] = 'Todays calendar';
$txt['tp-ssi-recentpoll'] = 'Most recent poll';
$txt['tp-ssi-topboards'] = 'Top Boards';
$txt['tp-ssi-toppoll'] = 'Top Poll';
$txt['tp-ssi-topposters'] = 'Top Posters';
$txt['tp-ssi-topreplies'] = 'Top Replies';
$txt['tp-ssi-topviews'] = 'Top Views';
$txt['tp-rssblock'] = 'RSS feed ';
$txt['tp-rssblock-showonlytitle'] = 'Display only titles?';
$txt['tp-rssblock-useutf8'] = 'What encoding to use in this feed ';
$txt['tp-utf8'] = 'UTF-8';
$txt['tp-iso'] = 'ISO-8859-1 (default)';
$txt['tp-rssblock-showavatar'] = 'Show avatars';
$txt['tp-rssblock-maxwidth'] = 'Max width of rss feed';
$txt['tp-rssblock-maxshown'] = 'Maximum number of items shown in rss feed ( 0 sets max to 20 )';
$txt['tp-showstatsbox'] = 'Display these stats in the article/downloads box';
$txt['tp-showssibox'] = 'Display this SSI function in the box';
$txt['tp-showuserbox'] = 'Display these items in the statbox';
$txt['tp-showuserbox2'] = 'Display these items in the userbox';
$txt['tp-themesavail'] = 'Themes available in the themebox';
$txt['tp-sitemapmodules'] = 'Active Modules';
$txt['tp-showarticle'] = 'Display article';
$txt['tp-showcategory'] = 'Display article listing from category';
$txt['tp-numberofrecenttopics'] = 'Number of recent topics to display ';
$txt['tp-recentboards'] = 'Board Id\'s (comma separated, blank will include all)';
$txt['tp-recentincexc'] = 'Include or exclude boards';
$txt['tp-recentinboard'] = 'Include boards';
$txt['tp-recentexboard'] = 'Exclude boards';
$txt['tp-catboxauthor'] = 'Show author?';
$txt['tp-catboxheight'] = 'Height of the article box before scrollbar';
$txt['tp-insert'] = 'Insert code';
$txt['tp-unreadreplies'] = 'Unread replies';

// Menu manager
$txt['tp-helpmenus'] = 'The built in menu manager allows you to create multiple menu\'s. These menu\'s are typically displayed in a block. You get all the features of blocks to display these menu\'s in different places along with choosing who gets to see the blocks based on permissions. Here you can edit the Internal menu of create and manage new menu\'s';
$txt['tp-internalmenu'] = 'Internal';
$txt['tp-nomenuitem'] = 'There are no items in this menu.';
$txt['tp-addmenuitem'] = 'Add menu item';
$txt['tp-header'] = 'Header';
$txt['tp-editmenu'] = 'Edit menu item';
$txt['tp-windowopen'] = 'Open in';
$txt['tp-suremenu'] = 'Are you sure you want to delete this item?';
$txt['tp-nowindowmenu'] = 'Same window';
$txt['tp-windowmenu'] = 'Open in a new window';
$txt['tp-link'] = 'Link';
$txt['tp-spacer'] = 'Spacer';
$txt['tp-menu'] = 'Menu button';
$txt['tp-item'] = 'Item';
$txt['tp-sitemap_on'] = 'Sitemap?';
$txt['tp-showmenustyle'] = 'Menu style';
$txt['tp-showmenus'] = 'Use menu';
$txt['tp-showmenusvar1'] = 'Type of menu';
$txt['tp-showmenusvar2'] = 'Menu type variable';
$txt['tp-menu-after'] = 'Add menu after';
$txt['tp-menu-icon'] = 'Menu icon';
$txt['tp-menu-icon2'] = '<small>Note: the path for menu icons is relative to folder /Themes/default/images. You can place your own icons in that folder using ftp and assign them here. Please note that folder /Themes/default/images/tinyportal is <b>removed</b> when uninstalling Tinyportal, so any file manually placed there will be lost upon uninstall!</small>';

// Shoutbox settings
$txt['tp_shout'] = 'Shoutbox';
$txt['tp-shoutboxsettings'] = 'Shoutbox settings';
$txt['tp-shoutboxtitle'] = 'Place a fixed message or announcement at the top of the shoutbox (BBC allowed)';
$txt['tp-shoutbox_showsmile'] = 'Display smilies buttons';
$txt['tp-shoutbox_showicons'] = 'Display BBC buttons';
$txt['tp-shout-allow-links'] = 'Allow links to be posted';
$txt['tp-shoutboxusescroll'] = 'Use scrolling';
$txt['tp-shoutboxduration'] = 'Speed for the scrolling (1 - 5)';
$txt['tp-shout-autorefresh'] = 'Auto refresh in seconds (0 = disabled)<br><span style="color:#CC0000">Setting this too low can eat server resources</span>';
$txt['shout_submit_returnkey'] = 'Choose how to submit Shout';
$txt['tp-yes-enter'] = 'with Enter';
$txt['tp-yes-ctrl'] = 'with Ctrl/Cmd+Enter';
$txt['tp-yes-shout'] = 'with Shout button only';
$txt['tp-shoutbox_id'] = 'Choose the Shoutbox ID for this block';
$txt['shoutbox_layout'] = 'Shoutbox layout';
$txt['tp-shoutboxheight'] = 'Height of shoutbox in pixels';
$txt['tp-shoutboxlimit'] = 'Limit posts in shoutbox to ';
$txt['tp-shoutboxmaxlength'] = 'Maximum shout length in characters';
$txt['tp-shoutboxtimeformat'] = 'Time format';
$txt['tp-shoutboxcolors'] = 'Shoutbox color settings';
$txt['tp-shoutboxcolorsdesc'] = 'Shoutbox color settings: Use hex color codes. An example of a Hex color representation is \'hashtag\'123456. To use the default theme colors leave these fields blank.';
$txt['tp-shoutbox_use_groupcolor'] = 'Use membergroup color for user names';
$txt['tp-shoutbox_use_groupcolordesc'] = '(The general TP setting takes preference over the shoutbox setting.)';
$txt['tp-shoutboxtextcolor'] = 'Default shout text color';
$txt['tp-shoutboxtimecolor'] = 'Time text color';
$txt['tp-shoutboxlinecolor1'] = 'Layouts 3 and 4: background color odd lines';
$txt['tp-shoutboxlinecolor2'] = 'Layouts 3 and 4: background color even lines';
$txt['tp-show_profile_shouts'] = 'Hide shouts in the profile';
$txt['tp-shoutboxadmin'] = 'Shoutbox administration';
$txt['tp-shoutboxitems'] = 'Edit/Remove last shouts';
$txt['tp-filtered'] = 'Filtered';
$txt['tp-deleteallshouts'] = 'Delete all shouts?';
$txt['tp-allshoutsbyid'] = 'All shouts in this shoutbox';
$txt['tp-allshoutsbymember'] = 'All shouts by this member';
$txt['tp-allshoutsbyip'] = 'All shouts by this IP';
$txt['tp-allshouts'] = 'Show all shouts';
$txt['tp-allowguestshout'] = 'Allow guest shouts';

// Downloads Manager
$txt['tp-dldownloads'] = 'Downloads';
$txt['tp-module1'] = 'Downloads: x latest files';
$txt['tp-module2'] = 'Downloads: x most downloaded';
$txt['tp-module3'] = 'Downloads: x most viewed';
$txt['tp-module4'] = 'Downloads: latest uploaded file';
$txt['tp-module5'] = 'Downloads: most downloaded file';
$txt['tp-module6'] = 'Downloads: most viewed file';
$txt['tp-mod-dladmin'] = 'Downloads Admin';
$txt['tp-mod-dlmanager'] = 'Downloads Manager';
$txt['tp-dlmaxerror'] = 'The maximum size for an uploaded file is currently ';
$txt['tp-childcategories'] = 'Child categories';
$txt['tp-icon'] = 'Icon';

// Miscellaneous, used by various parts of TP
$txt['tp-approve'] = 'Approve';
$txt['tp-approved'] = 'Approved';
$txt['tp-article'] = 'Article';
$txt['tp-incategory'] = ' in category ';
$txt['tp-body'] = 'Body';
$txt['tp-checkall'] = '<em>Toggle all</em>';
$txt['tp-confirm'] = 'Are you sure?';
$txt['tp-date'] = 'Date';
$txt['tp-deactivate'] = 'De-activate';
$txt['tp-deactivated'] = 'De-activated';
$txt['tp-delete'] = 'Delete';
$txt['tp-display'] = 'Display';
$txt['tp-on'] = 'On';
$txt['tp-off'] = 'Off';
$txt['tp-yes'] = 'Yes';
$txt['tp-no'] = 'No';
$txt['tp-type'] = 'Type';
$txt['tp-unread'] = 'Unread';
$txt['tp-memberlist'] = 'Memberlist';
$txt['tp-stats'] = 'Stats';
$txt['tp-status'] = 'Status';
$txt['tp-statusdesc'] = 'Three icons that can be toggled on or off: - Show On Frontpage: will make the article appear on the frontpage of TP. If you set the option of \'use intro\' the introtext content will be used instead. - Set as sticky / non-sticky article: will make the article appear on top of the article list in the category.  - Lock this article for editing: will lock the article.';
$txt['tp-sub_item'] = 'Position';
$txt['tp-title'] = 'Title';
$txt['tp-remove'] = 'Remove';
$txt['tp-pos'] = 'Pos';
$txt['tp-send'] = 'Save';
$txt['tp-more'] = 'More ';
$txt['tp-none-'] = '- none -';
$txt['tp-hide'] = 'Hide ';
$txt['tp-adminmenu'] = 'TP Admin menu';
$txt['tp-menus'] = 'Sections';
$txt['tp-name'] = 'Name';
$txt['tp-shortname'] = 'Short name';
$txt['tp-shortnamedesc'] = 'Here you can create \'meaningful URLs\': specify a short text to be used in the page URL display. Without a value the url will be displayed as: .../index.php?cat=20. if a value is added (for example \'Categoryname\') the url will show as: .../index.php?page=Categoryname';

?>
