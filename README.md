[TinyPortal](https://www.tinyportal.net/) - Portal Mod for SMF
==================================================
![](https://www.tinyportal.net/Themes/alphacentauri203/images/theme/logo_light.png)

[![Latest Version](https://img.shields.io/github/release/TinyPortal/TinyPortal.svg)](https://github.com/TinyPortal/TinyPortal/releases)
[![Total Downloads](https://img.shields.io/github/downloads/TinyPortal/TinyPortal/total.svg)](https://github.com/TinyPortal/TinyPortal/releases)
[![GitHub issues](https://img.shields.io/github/issues/TinyPortal/TinyPortal.svg)](https://github.com/TinyPortal/TinyPortal/issues)

# What is TinyPortal?

TinyPortal is a mod for SMF that adds a powerful and mature CMS to your SMF Forum in minutes, with native integration, without having to worry about bridging, database, and appearance. Your SMF and TP will be a homogenous ensemble from installation. It's up to you and your creativity to use TP's features to customize your site and organize its content.

## Features included in TinyPortal:

- **Article System:** The article system gives you a CMS system tightly integrated with your forum. Write articles in php, html or bbcode, and choose what member-groups can access them, who can contribute, and how they are approved.[/li]

- **Blocks and Panels:** Blocks is a term for the rectangles of information you can use throughout the portal. For the sake of simplicity the page has been divided into the "panels", which when combined can mimic many common page layouts. Blocks can contain regular html, BBC, php code or special written functions that collect specific types of information. You can show them according to which permissions the visitor has, and even show them just in certain sections. 
TinyPortal comes with a number of standard block types, such as: Recent Posts, Theme switcher, Search, Calendar, Poll, RSS feeds, Menu, User Profile block and News.*

- **Menu Manager:** The menu manager lets you add single level Buttons / Tabs to the SMF menu, but also create menu's within a portal block available to specific membergroups. You can create and organize various types of menu items: links to articles, categories, or even just a custom link. You can choose to have the link open in the current window or a new window.[/li]

- **Downloads Manager**: A built-in module for TP that lets you offer files for your members to browse and download. Its works by having the downloadable files placed in categories, letting you restrict member groups access level per each category. [/li]

- **ShoutBox:** An intergrated message box in which you can leave messages for others to see and thus maintain a simple "chat" on the site. Combined with permissions you will have much freedom in who can participate as well.


For more detailed information about the features of TinyPortal and how to use them, check out the https://www.tinyportal.net/docs

**Note:** You must uninstall any previous version of TinyPortal before you can install TinyPortal for SMF2.0.x or SMF2.1 - 2.1.1. Make a backup of all your SMF and TP files before you uninstall.[/color]

***

## Current Version: TinyPortal 2.2.0, 11th February 2022

##### TinyPortal (TP)

**The TinyPortal Team is pleased to announce the release of TinyPortal 2.2.0**

TinyPortal 2.2.0 can be used on SMF 2.0 and 2.1 - 2.1.1

Minimum required PHP version : 7.2.0

Highest supported PHP version (tested) 8.0.0

[b]Changelog: TinyPortal 2.2.0[/b]

Relevant to SMF 2.0 and 2.1:

**New functionality:**

- added most standard SSI functions as options to SSI block
- updated stats block to use thousand separators
- added font-awesome icons for menu options in specials themes
- updated installer to provide more consistent log information

**Bugfixes:**

- fixed block access page shows incorrect panels for blocks
- fixed Save button on block access pages showing between blocks when uneven #items
- fixed missing div on Downloads page when top downloads not activated
- fixed broken category links in profile articles view and use short names when available
- fixed link to article category from Profile
- fixed mini calendar blockcode for PHP 8.1
- fixed singleshout blockcode for 2.1.x
- general code updates for PHP8 compatibility

**Note:**

when updating from TinyPortal versions older than 2.1.0, shoutbox blocks are enhanced with a new block setting: "Characters to display in titles".

Due to this change any pre-existing shoutbox blocks 'may' have a changed title length after installing the new version of TP. Please check any shoutbox blocks and adjust the block setting "Characters to display in titles" to the desired length...

***

**Download:** https://www.tinyportal.net/index.php?action=tportal;sa=download;dl=item172

**NOTICE!** because of the database changes in TP 2.x.x versions there's no going back to TP 1.x.x after installing version 2.0.1 or 2.1.0. You can install previous versions, but by doing that you WILL lose TP data and you will need to manually fix or recreate your content!!! For these reasons it's advised to backup your database and files before proceeding.

A huge thankyou to tino and @rjen, and all our beta testers for all their hard work, and making this release possible.

***

### Current Version: TinyPortal 1.6.9, 19th January 2021

**TinyPortal (TP)**

This is a bug fix only support for older php versions

Minimum required PHP version : 5.4.0

**Changelog TinyPortal 1.6.9**

This release includes the following fixes:

**Relevant to 2.0 and 2.1:**

- Fix search string not being correctly escaped when printing to the screen

***

***Users running versions of TinyPortal prior to 1.6.6 should update immediately by downloading the latest version.***

**Download:** https://www.tinyportal.net/index.php?action=tportal;sa=download;dl=cat61

http://www.tinyportal.net/"Official_TinyPortal_Site

**Note:** Older versions of TinyPortal can be found on the official TinyPortal website.

License Information:
--------------------------------------

The contents of this file are subject to the Mozilla Public License Version 2.0 (the "License");
you may not use this package except in compliance with the License. You may obtain a copy of the License at
https://www.mozilla.org/en-US/MPL/2.0/


Questions?
----------

If you have any questions, please feel free to ask on the
[TinyPortal Support forum](https://www.tinyportal.net)

