<id>
TinyPortal
</id>

<version>
1.0.8
</version>

<mod info>
TinyPortal v1.0.8 beta 4
</mod info>


<author>
Bloc
</author>

<homepage>
http://www.tinyportal.net
</homepage>

<edit file>
$sourcedir/Subs.php
</edit file>

<search for>
function redirectexit($setLocation = '', $refresh = false)
</search for>

<replace>
function redirectexit($setLocation = '', $refresh = false, $tp_not = false)
</replace>

<search for>
	if ($setLocation == $scripturl)
</search for>

<replace>
	if ($setLocation == $scripturl && !$tp_not)
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
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?(?:' . SID . ';)((?:board|topic|page|cat|action)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html?' . SID . '\$2\"'", $buffer);
		else
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?((?:board|topic|page|cat|action)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html\$2\"'", $buffer);

</search for>

<replace>
		// Let's do something special for session ids!
		if (defined('SID') && SID != '')
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?(?:' . SID . ';)((?:board|topic|page|cat)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html?' . SID . '\$2\"'", $buffer);
		else
			$buffer = preg_replace('/"' . preg_quote($scripturl, '/') . '\?((?:board|topic|page|cat)=[^#"]+?)(#[^"]*?)?"/e', "'\"' . \$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html\$2\"'", $buffer);

</replace>

<edit file>
$sourcedir/Profile.php
</edit file>


<search for>
		// Tinyportal
		'tpsummary' => array(array('profile_view_any', 'profile_view_own'), array('profile_view_any')),
		'tparticles' => array(array('profile_extra_any', 'profile_extra_own'), array('tp_articles')),
		'tpdownload' => array(array('profile_extra_any', 'profile_extra_own'), array('tp_dlmanager')),
		'tpshoutbox' => array(array('profile_extra_any', 'profile_extra_own'), array('tp_blocks')),
		'tpgallery' => array(array('profile_extra_any', 'profile_extra_own'), array('tp_gallery')),
		'tplinks' => array(array('profile_extra_any', 'profile_extra_own'), array('tp_links')),
		// end Tinyportal
</search for>

<replace>
// removed code.
</replace>

<search for>

    // TinyPortal
	if (!$user_info['is_guest'] && (($context['user']['is_owner'] && allowedTo('profile_view_own')) || allowedTo(array('profile_view_any', 'moderate_forum', 'manage_permissions','tp_dlmanager','tp_blocks','tp_articles','tp_gallery','tp_linkmanager'))))
	{
		$context['profile_areas']['tinyportal'] = array(
			'title' => $txt['tp-profilesection'],
			'areas' => array()
		);

		$context['profile_areas']['tinyportal']['areas']['tpsummary'] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa=tpsummary">' . $txt['tpsummary'] . '</a>';
		if ($context['user']['is_owner'] || allowedTo('tp_articles'))
			$context['profile_areas']['tinyportal']['areas']['tparticles'] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa=tparticles">' . $txt['articlesprofile'] . '</a>';
		if(($context['user']['is_owner'] || allowedTo('tp_dlmanager')) && $context['TPortal']['show_download'])
			$context['profile_areas']['tinyportal']['areas']['tpdownload'] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa=tpdownload">' . $txt['downloadprofile'] . '</a>';
		if($context['user']['is_owner'] || allowedTo('tp_blocks'))
			$context['profile_areas']['tinyportal']['areas']['tpshoutbox'] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa=tpshoutbox">' . $txt['shoutboxprofile'] . '</a>';
		if(($context['user']['is_owner'] || allowedTo('tp_gallery')) && $context['TPortal']['show_gallery'])
			$context['profile_areas']['tinyportal']['areas']['tpgallery'] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa=tpgallery">' . $txt['galleryprofile'] . '</a>';
		if(($context['user']['is_owner'] || allowedTo('tp_linkmanager')) && $context['TPortal']['show_linkmanager'])
			$context['profile_areas']['tinyportal']['areas']['tplinks'] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa=tplinks">' . $txt['linksprofile'] . '</a>';
	}
    // end TinyPortal

</search for>

<replace>
// removed code2.
</replace>

<search for>
// Tinyportal
function tpsummary($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['tpsummary'];
	TP_profile_summary($memID);
}
function tparticles($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['articlesprofile'];
	TP_profile_articles($memID);
}
function tpdownload($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['downloadprofile'];
	TP_profile_download($memID);
}
function tpshoutbox($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['shoutboxprofile'];
	TP_profile_shoutbox($memID);
}
function tpgallery($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['galleryprofile'];
	TP_profile_gallery($memID);
}
function tplinks($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['linksprofile'];
	TP_profile_links($memID);
}

</search for>

<replace>
// removed code3.
</replace>

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