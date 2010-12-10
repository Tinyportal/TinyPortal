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
