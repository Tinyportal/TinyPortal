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

