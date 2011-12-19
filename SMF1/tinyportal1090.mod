<id>
TinyPortal
</id>

<version>
1.105
</version>

<mod info>
TinyPortal 1.0 RC3
</mod info>

<author>
Bloc
</author>

<homepage>
http://www.tinyportal.net
</homepage>

<edit file>
$boarddir/index.php
</edit file>

<search for>
require_once($sourcedir . '/Security.php');
</search for>

<add after>
	// TinyPortal include
	require_once($sourcedir . '/TPortal.php');
</add after>

<search for>
	// Is the forum in maintenance mode? (doesn't apply to administrators.)
</search for>

<add before>
	// TinyPortal
	TPortal_init();

</add before>

<search for>
		// Action and board are both empty... BoardIndex!
		if (empty($board) && empty($topic))
		{
			require_once($sourcedir . '/BoardIndex.php');
			return 'BoardIndex';
		}
</search for>
<replace>
		// first..if the action is set, but empty, don't go any further
		if (isset($_REQUEST['action']) && $_REQUEST['action']=='')
		{
			require_once($sourcedir . '/BoardIndex.php');
			return 'BoardIndex';
		}

		// Action and board are both empty... maybe the portal page?
		if (empty($board) && empty($topic) && $settings['TPortal_front_type']!='boardindex')
		{
			require_once($sourcedir . '/TPortal.php');
			return 'TPortal';
		}
		if (empty($board) && empty($topic) && $settings['TPortal_front_type']=='boardindex' && (isset($_GET['cat']) || isset($_GET['page'])))
		{
			require_once($sourcedir . '/TPortal.php');
			return 'TPortal';
		}
		// Action and board are still both empty...and no portal startpage - BoardIndex!
		elseif (empty($board) && empty($topic) && $settings['TPortal_front_type']=='boardindex')
		{
			require_once($sourcedir . '/BoardIndex.php');
			return 'BoardIndex';
		}
</replace>

<search for>
		'.xml' => array('News.php', 'ShowXmlFeed'),
</search for>
<add after>
		'tpadmin' => array('TPortalAdmin.php', 'TPortalAdmin'),
		'forum' => array('BoardIndex.php', 'BoardIndex'),
		'tpmod' => array('TPmodules.php', 'TPmodules'),
</add after>


<edit file>
$sourcedir/Load.php
</edit file>
<search for>
	$settings = $themeData[0];
</search for>
<add after>
	if (!empty($context['TPortal']['front_type'])){
		$settings['TPortal_front_type'] = $context['TPortal']['front_type'];
	}
</add after>

<search for>
	// Start the linktree off empty..
	$context['linktree'] = array();
</search for>
<replace>
	// Start the linktree off empty..not quite, have to insert forum
	$context['linktree'] = array(array('url' => $scripturl . '?action=forum', 'name' => 'Forum'));
</replace>

<search for>
		// Build up the linktree.
		$context['linktree'] = array_merge(
			$context['linktree'],
			array(array(
				'url' => $scripturl . '#' . $board_info['cat']['id'],
				'name' => $board_info['cat']['name']
			)),
</search for>
<replace>
		// Build up the linktree (adding TPortal forum index)
		$context['linktree'] = array_merge(
			$context['linktree'],
				array(array(
					'url' => $scripturl . '?action=forum#' . $board_info['cat']['id'],
					'name' => $board_info['cat']['name']
			)),
</replace>

<search for>
	// The theme is the forum's default.
	else
		$ID_THEME = $modSettings['theme_guests'];
</search for>

<add after>

	// TinyPortal
	$newtheme=TP_loadTheme();
	if($newtheme!=$ID_THEME && $newtheme>0)
		$ID_THEME=$newtheme;
	// end TinyPortal
</add after>

<edit file>
$themedir/index.template.php
</edit file>

<search for>
	if (in_array($context['current_action'], array('search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm')))
</search for>
<replace>
	if (in_array($context['current_action'], array('search', 'admin', 'calendar', 'profile', 'mlist', 'register', 'login', 'help', 'pm', 'forum', 'tpadmin')))
</replace>

<search for>
		$current_action = 'search';
</search for>
<add after>

	if (isset($_GET['dl']))
		$current_action = 'dlmanager';

	if (isset($_GET['board']) || isset($_GET['topic']) || $context['current_action']=='forum')
		$current_action = 'forum';

	if ($context['current_action']=='tpadmin')
		$current_action = 'admin';

</add after>


<search for>
	// Show the [help] button.
</search for>

<add before>
if($settings['TPortal_front_type']!='boardindex')
	// Show the [forum] button.
	echo ($current_action=='forum' || $context['browser']['is_ie4']) ? '<td class="maintab_active_first">&nbsp;</td>' : '' , '
				<td valign="top" class="maintab_' , $current_action=='forum' ? 'active_back' : 'back' , '">
					<a href="', $scripturl, '?action=forum">'.$txt['tp-forum'].'</a>
				</td>' , $current_action=='forum' ? '<td class="maintab_active_last">&nbsp;</td>' : '';
</add before>

<edit file>
$boarddir/Themes/babylon/index.template.php
</edit file>


<search for>
		<a href="javascript:void(0);" onclick="shrinkHeader(!current_header); return false;"><img id="upshrink" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin: 2px 2ex 2px 0;" border="0" /></a>';
</search for>
<replace>
';
// TinyPortal

         if($context['TPortal']['showtop'])
             echo '<a href="javascript:void(0);" onclick="shrinkHeader(!current_header); return false;"><img id="upshrink" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin: 2px 0;" border="0" /></a><img id="upshrinkTemp" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 1ex;" /> ';
         if($context['TPortal']['leftbar'])
             echo '<a href="javascript:void(0);" onclick="shrinkHeaderLeftbar(!current_leftbar); return false;"><img id="upshrinkLeftbar" src="', $settings['images_url'], '/', empty($options['collapse_leftbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin: 2px 0;" border="0" /></a><img id="upshrinkTempLeftbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" />';
         if($context['TPortal']['rightbar'])
             echo '<a href="javascript:void(0);" onclick="shrinkHeaderRightbar(!current_rightbar); return false;"><img id="upshrinkRightbar" src="', $settings['images_url'], '/', empty($options['collapse_rightbar']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin: 2px 0;" border="0" /></a><img id="upshrinkTempRightbar" src="', $settings['images_url'], '/blank.gif" alt="" style="margin-right: 0ex;" />';
// TinyPortal end
</replace>

<search for>
	// Show the [home] and [help] buttons.
	echo '
				<a href="', $scripturl, '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/home.gif" alt="' . $txt[103] . '" style="margin: 2px 0;" border="0" />' : $txt[103]), '</a>', $context['menu_separator'], '
				<a href="', $scripturl, '?action=help">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/help.gif" alt="' . $txt[119] . '" style="margin: 2px 0;" border="0" />' : $txt[119]), '</a>', $context['menu_separator'];
</search for>
<replace>
	// Show the [home] and [help] buttons.
	echo '
                     <a href="', $scripturl, '">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/home.gif" alt="' . $txt[103] . '" style="margin: 2px 0;" border="0" />' : $txt[103]), '</a>', $context['menu_separator'];
	if($settings['TPortal_front_type']!='boardindex')
		echo '        <a href="', $scripturl, '?action=forum">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/forum.gif" alt="Forum Index" style="margin: 2px 0;" border="0" />' : 'Forum Index'), '</a>', $context['menu_separator'];
	
	echo '        <a href="', $scripturl, '?action=help" target="_blank">', ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/' . $context['user']['language'] . '/help.gif" alt="' . $txt[119] . '" style="margin: 2px 0;" border="0" />' : $txt[119]), '</a>', $context['menu_separator'];
</replace>


<edit file>
$sourcedir/Subs.php
</edit file>

<search for>
	$context['allow_admin'] = allowedTo(array('admin_forum', 'manage_boards', 'manage_permissions', 'moderate_forum', 'manage_membergroups', 'manage_bans', 'send_mail', 'edit_news', 'manage_attachments', 'manage_smileys'));
</search for>

<add after>

	// tinyportal //
	global $sourcedir;
	require_once($sourcedir.'/TPSubs.php');
	TPcheckAdminAreas();
	// end //
</add after>

<search for>
	// Admin area 'Members'.
</search for>

<add before>
	// TinyPortal
	global $sourcedir;
	require_once($sourcedir.'/TPSubs.php');
	TPsetupAdminAreas();
	// TinyPortal end

</add before>

<search for>
function redirectexit($setLocation = '', $refresh = false)
</search for>

<replace>
function redirectexit($setLocation = '', $refresh = false, $tp_not = false)
</replace>

<search for>
	// Put the session ID in.
	if (defined('SID') && SID != '')
</search for>

<add before>
	// TinyPortal
	if ($setLocation == $scripturl && !$tp_not && !empty($context['TPortal']['redirectforum']))
		$setLocation .= '?action=forum';
	// end
	
</add before>

<edit file>
$sourcedir/ManagePermissions.php
</edit file>

<search for>
	// This is just a helpful array of permissions guests... cannot have.
</search for>

<add before>
	// TPortal 	
	foreach($context['TPortal']['permissonlist'] as $perm )
			$permissionList['membergroup'][$perm['title']] = $perm['perms'];
	
	// end TinyPortal

</add before>

<search for>
	// All permission groups that will be shown in the left column.
	$leftPermissionGroups = array(
		'general',
		'calendar',
		'maintenance',
		'member_admin',
		'general_board',
		'topic',
		'post',
	);
</search for>

<replace>
	// All permission groups that will be shown in the left column.
	$leftPermissionGroups = array(
		'general',
		'maintenance',
		'member_admin',
		'profile',
		'general_board',
		'topic',
		'post',
	);
</replace>

<edit file>
$sourcedir/Security.php
</edit file>

<search for>
// Require a user who is logged in. (not a guest.)
function is_not_guest($message = '')
{
	global $user_info, $txt, $context;
</search for>

<add after>

	// TinyPortal
	TPortal_init();
</add after>

<edit file>
$sourcedir/Errors.php
</edit file>

<search for>
	// We don't have $txt yet, but that's okay...
	if (empty($txt))
		die($error);
</search for>

<add after>

	// TinyPortal
	if(!isset($context['tp_prefix']))
		TPortal_init();
	// end
</add after>



<edit file>
$themedir/Help.template.php
</edit file>

<search for>
	global $context, $settings, $options, $txt, $scripturl;

	echo '
	<div class="tborder" style="margin-top: 1ex;">
		<div id="helpmenu" class="titlebg" style="padding: 4px;">';
</search for>

<replace>
	global $context, $settings, $options, $txt, $scripturl;

	// Tinyportal
	echo '
	<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 10px;">
		<tr>
			<td class="mirrortab_first">&nbsp;</td>
			<td class="mirrortab_active_first">&nbsp;</td>
			<td valign="top" class="mirrortab_active_back">
				<a href="', $scripturl, '?action=help">' , $txt['tp-smfhelp'] , '</a>
			</td>
			<td class="mirrortab_active_last">&nbsp;</td>
			<td valign="top" class="mirrortab_back">
				<a href="', $scripturl, '?action=tpmod;sa=help">' , $txt['tp-tphelp'] , '</a>
			</td>
			<td class="mirrortab_last">&nbsp;</td>
	     </tr>
	</table>';

	echo '
	<div class="tborder">
		<div id="helpmenu" class="titlebg" style="padding: 4px;">';
	// end Tinyportal
</replace>

<edit file>
$sourcedir/Profile.php
</edit file>

<search for>
	// Set the profile layer to be displayed.
</search for>

<add before>

	// TinyPortal
	$tp_areas = TP_fetchprofile_areas();
	foreach($tp_areas as $tp)
		$sa_allowed[$tp['name']] = array(array('profile_view_any', 'profile_view_own'), array($tp['permission']));
	// end TinyPortal

</add before>

<search for>
	// If you have permission to do something with this profile, you'll see one or more actions.
</search for>

<add before>

	// TinyPortal
	TP_fetchprofile_areas2($memID);
	
</add before>

<search for>
	$_REQUEST['sa']($memID);
</search for>

<replace>
	if(isset($_GET['tpmodule']))
	{
		global $boarddir, $db_prefix;
		
		// prefix of the TP tables
		$tp_prefix = $db_prefix.'tp_';
		
		$request = db_query("SELECT modulename,autoload_run FROM {$tp_prefix}modules WHERE active=1 and profile = '" . $_GET['sa'] . "'", __FILE__, __LINE__);
		if(mysql_num_rows($request)>0)
		{
			$what=mysql_fetch_assoc($request);
			mysql_free_result($request);
			// load the appropiate source file
			if(file_exists($boarddir .'/tp-files/tp-modules/' . $what['modulename']. '/Sources/'. $what['autoload_run']))
			{
				require_once($boarddir .'/tp-files/tp-modules/' . $what['modulename']. '/Sources/'. $what['autoload_run']);
			}
		}
		$_GET['sa']($memID);
	}
	else
		$_REQUEST['sa']($memID);
</replace>

<edit file>
$boarddir/SSI.php
</edit file>

<search for>
require_once($sourcedir . '/Security.php');
</search for>

<add after>
require_once($sourcedir . '/TPortal.php');
</add after>

<edit file>
$sourcedir/BoardIndex.php
</edit file>

<search for>
				'href' => $scripturl . '#' . $row_board['ID_CAT'],
</search for>

<replace>
				'href' => $scripturl . '?action=forum#' . $row_board['ID_CAT'],
</replace>

<edit file>
$sourcedir/QueryString.php
</edit file>

<search for>
	// If $scripturl is set to nothing, or the SID is not defined (SSI?) just quit.
</search for>
<add before>
	// A better place
	if (function_exists('tp_addcopy'))
		$buffer = tp_addcopy($buffer);

</add before>


<search for>
		// Let's do something special for session ids!
		if (defined('SID') && SID != '')
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?(?:' . SID . ';)((?:board|topic)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html?' . SID . '\$2\"'", $buffer);
		else
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?((?:board|topic)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html\$2\"'", $buffer);
</search for>

<replace>
		// Let's do something special for session ids!
		if (defined('SID') && SID != '')
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?(?:' . SID . ';)((?:board|topic|page|cat)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html?' . SID . '\$2\"'", $buffer);
		else
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?((?:board|topic|page|cat)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html\$2\"'", $buffer);

</replace>

<edit file>
$sourcedir/Subs-Post.php
</edit file>

<search>
function theme_postbox($msg)
</search>

<replace>
function theme_postbox($msg, $from_tp = false)
</replace>

<search>
	// Load the Post template and language file.
	loadLanguage('Post');
	loadTemplate('Post');
</search>

<replace>
	// Load the Post template and language file.
	loadLanguage('Post');
	if(!$from_tp)
		loadTemplate('Post');
</replace>

<search>
	// Go!  Supa-sub-template-smash!
	template_postbox($msg);
</search>

<replace>
	// Go!  Supa-sub-template-smash!
	if(!$from_tp)
		template_postbox($msg);
	else
		tp_renderbbc($msg);
</replace>

<edit file>
$sourcedir/Admin.php
</edit file>
<search for>
	// You have to be able to do at least one of the below to see this page.
	isAllowedTo(array('admin_forum', 'manage_permissions', 'moderate_forum', 'manage_membergroups', 'manage_bans', 'send_mail', 'edit_news', 'manage_boards', 'manage_smileys', 'manage_attachments'));
</search for>
<replace>
	// You have to be able to do at least one of the below to see this page.
	$admPerms = TP_addPerms();
	isAllowedTo($admPerms);
</replace>


<edit file>
$sourcedir/Post.php
</edit file>

<search for>
	// Finally, load the template.
</search for>

<add before>
	// load TP
	if(!in_array('tp',$context['template_layers']))
	{
		if(!isset($_REQUEST['preview']) || (!isset($_REQUEST['xml']) && isset($_REQUEST['preview'])))
			$context['template_layers'][] = 'tp';
	}

</add before>