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
$sourcedir/Subs.php
</edit file>

<search for>
	elseif ($context['current_action'] == 'groups' && $context['allow_moderation_center'])
		$current_action = 'moderate';
</search for>
<add after>
	elseif ($context['current_action'] == '' && (isset($_GET['board']) || isset($_GET['topic'])))
		$current_action = 'forum';
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
