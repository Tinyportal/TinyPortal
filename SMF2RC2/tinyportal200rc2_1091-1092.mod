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
<search for>
			'tpadmin' => array(
				'title' => 'TinyPortal',
				'href' => $scripturl . '?action=tpadmin',
				'show' =>  TPcheckAdminAreas(),
				'sub_buttons' => array(
					'tpnews' => array(
						'title' => $txt['tp-settings'],
						'href' => $scripturl . '?action=tpadmin',
						'show' => true,
					),
					'tpblocks' => array(
						'title' => $txt['tp-blocks'],
						'href' => $scripturl . '?action=tpadmin;sa=blocks',
						'show' => allowedTo('tp_blocks'),
					),
					'tparticles' => array(
						'title' => $txt['tp-articles'],
						'href' => $scripturl . '?action=tpadmin;sa=articles',
						'show' => allowedTo('tp_articles'),
					),
					'tpmodules' => array(
						'title' => $txt['tp-modules'],
						'href' => $scripturl . '?action=tpadmin;sa=modules',
						'show' => allowedTo('tp_modules'),
					),
				),
			),
</search for>
<replace>
			'tpadmin' => array(
				'title' => 'TinyPortal',
				'href' => $scripturl . '?action=tpadmin',
				'show' =>  TPcheckAdminAreas(),
				'sub_buttons' => array(
				),
			),
</replace>

<search for>
		// Now we put the buttons in the context so the theme can use them.
</search for>
<add before>

		// tinyportal //
		$buttons['tpadmin']['sub_buttons'] = tp_getbuttons();

</add before>


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