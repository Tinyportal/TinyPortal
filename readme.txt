[size=5][b][color=orange][u]TinyPortal (TP)[/u][/color][/b][/size]

[b][size=12pt]What is TinyPortal?[/size][/b]
TinyPortal is a mod for SMF that adds a powerful and mature CMS to your SMF Forum in minutes, with native integration, without having to worry about bridging, database, and appearance. Your SMF and TP will be a homogenous ensemble from installation. It's up to you and your creativity to use TP's features to customize your site and organize its content.

[u][b][size=12pt]Features included in TinyPortal:[/size][/b][/u]
[list]
[li][b]Article System[/b]: The article system gives you a CMS system tightly integrated with your forum. Write articles in php, html or bbcode, and choose what member-groups can access them, who can contribute, and how they are approved.[/li]
[li][b]Blocks and Panels[/b]: Blocks is a term for the rectangles of information you can use throughout the portal. For the sake of simplicity the page has been divided into the "panels", which when combined can mimic many common page layouts. Blocks can contain regular html, BBC, php code or special written functions that collect specific types of information. You can show them according to which permissions the visitor has, and even show them just in certain sections.
TinyPortal comes with a number of standard block types, such as: Recent Posts, Theme switcher, Search, Calendar, Poll, RSS feeds, Menu, User Profile block and News. [/li]
[li][b]Menu Manager[/b]: The menu manager lets you add single level Buttons / Tabs to the SMF menu, but also create menu's within a portal block available to specific membergroups. You can create and organize various types of menu items: links to articles, categories, or even just a custom link. You can choose to have the link open in the current window or a new window.[/li]
[li][b]Downloads Manager[/b]: A built-in module for TP that lets you offer files for your members to browse and download. Its works by having the downloadable files placed in categories, letting you restrict member groups access level per each category. [/li]
[li][b]ShoutBox[/b]: An intergrated message box in which you can leave messages for others to see and thus maintain a simple "chat" on the site. Combined with permissions you will have much freedom in who can participate as well.[/li]
[/list]

For more detailed information about the features of TinyPortal and how to use them, check out the [url="https://www.tinyportal.net/docs/index.php"]TinyPortal Docs Site[/url]

[color=red][b]Note:[/b] You must uninstall any previous version of TinyPortal before you can install TinyPortal for SMF2.0.x or SMF2.1. Make a backup of all your SMF and TP files before you uninstall.[/color]

[b]Note:[/b] All versions of TinyPortal can be downloaded from the [url="http://www.tinyportal.net/"][b]Official TinyPortal Site[/b][/url].

[hr]
[b][color=red]Current Version:[/color] TinyPortal 2.2.1, 13th February 2022[/b]

TinyPortal 2.2.1 can be used on SMF 2.0 and 2.1 - 2.1.1
Minimum required PHP version : 7.0.0
Highest supported PHP version (tested): 8.1.1

[b]Changelog:[/b]
This release includes the following changes relevant to SMF 2.0 and 2.1:

[b]Bugfixes:[/b]
- fix for shoutbox block causing too many redirects on a new install
- restored PHP support for PHP 7.0 and 7.1

[b]Notes:[/b]
when updating from TinyPortal versions older than 2.1.0, recent topic blocks are enhanced with a new block setting: "Characters to display in titles". Due to this change any pre-existing recent topics blocks 'may' have a changed title length after installing the new version of TP. Please check these blocks and adjust the block setting "Characters to display in titles" to the desired length...

[b]NOTICE![/b] because of the database changes in the 2.x version of TinyPortal there's no going back to TP 1.x.x after installing this version. You can install previous versions, but by doing that you WILL lose TP data and you will need to manually fix or recreate your content!!! For these reasons it's advised to backup your database and files before proceeding.

[hr]
[b][color=red]Current Version:[/color] TinyPortal 1.6.9, 19th January 2021[/b]

This is a bug fix only support for older php versions
Minimum required PHP version : 5.4.0

[b]Changelog:[/b]
This release includes the following fixes relevant to 2.0 and 2.1:

- Fix search string not being correctly escaped when printing to the screen
