<id>
TinyPortal
</id>

<version>
1.0
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
