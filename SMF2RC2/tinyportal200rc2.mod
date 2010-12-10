<id>
TinyPortal
</id>

<version>
1.0
</version>

<mod info>
TinyPortal v2
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
				'url' => $scripturl . '#c' . $board_info['cat']['id'],
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
		$id_theme = $modSettings['theme_guests'];
</search for>

<add after>

	// TinyPortal
	$newtheme=TP_loadTheme();
	if($newtheme!=$id_theme && $newtheme>0)
		$id_theme=$newtheme;
	// end TinyPortal
</add after>


<edit file>
$sourcedir/Subs.php
</edit file>

<search for>
			'help' => array(
</search for>
<add before>
			'forum' => array(
				'title' => $txt['tp-forum'],
				'href' => $scripturl . '?action=forum',
				'show' => true,
			),
</add before>
<search for>
			'help' => array(
				'title' => $txt['help'],
				'href' => $scripturl . '?action=help',
				'show' => true,
				'sub_buttons' => array(
</search for>
<add after>
					'tphelp' => array(
						'title' => 'TinyPortal',
						'href' => $scripturl . '?action=tpmod;sa=help',
						'show' => true,
					),
</add after>
<search for>
			'calendar' => array(
</search for>

<add before>
			'tpadmin' => array(
				'title' => 'TinyPortal',
				'href' => $scripturl . '?action=tpadmin',
				'show' =>  TPcheckAdminAreas(),
				'sub_buttons' => array(
				),
			),
</add before>

<search for>
		// Now we put the buttons in the context so the theme can use them.
</search for>
<add before>

		// tinyportal //
		$buttons['tpadmin']['sub_buttons'] = tp_getbuttons();

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
$boarddir/SSI.php
</edit file>

<search for>
require_once($sourcedir . '/Security.php');
</search for>

<add after>
require_once($sourcedir . '/TPortal.php');
</add after>

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
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?(?:' . SID . '(?:;|&|&amp;))((?:board|topic)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html?' . SID . '\$2\"'", $buffer);
		else
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?((?:board|topic)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html\$2\"'", $buffer);
</search for>

<replace>
		// Let's do something special for session ids!
		if (defined('SID') && SID != '')
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?(?:' . SID . '(?:;|&|&amp;))((?:board|topic|page|cat)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html?' . SID . '\$2\"'", $buffer);
		else
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?((?:board|topic|page|cat)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html\$2\"'", $buffer);
</replace>

<edit file>
$sourcedir/Subs-Editor.php
</edit file>

<search>
function theme_postbox($msg)
{
	global $context;

	return template_control_richedit($context['post_box_name']);
}
</search>

<replace>
function theme_postbox($msg, $from_tp = false)
{
	global $context;

	return template_control_richedit($context['post_box_name'], $from_tp);
}
</replace>


<edit file>
$sourcedir/Profile.php
</edit file>

<search for>
	// Do some cleaning ready for the menu function.
	$context['password_areas'] = array();
</search for>
<add before>

	// TinyPortal
	require_once($sourcedir. '/TPmodules.php');
	tp_getprofileareas($profile_areas);

</add before>