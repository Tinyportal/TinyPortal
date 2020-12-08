<?php
/**
 * @package TinyPortal
 * @version 2.0.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2020 - The TinyPortal Team
 *
 */

function template_wap2_tp_frontpage()
{
	global $context, $txt, $scripturl;

	if(!isset($context['TPortal']['category']))
		return;

	echo '<p class="titlebg">' . $context['linktree'][0]['name']  . '</p>';

	unset($context['TPortal']['article']);
	if(!empty($context['TPortal']['category']['featured']))
	{
		$context['TPortal']['article'] = $context['TPortal']['category']['featured'];
		render_frontp();
	}
	unset($context['TPortal']['article']);

	if(isset($context['TPortal']['category']['col1']))
	{
		foreach($context['TPortal']['category']['col1'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	unset($context['TPortal']['article']);
	if(isset($context['TPortal']['category']['col2']))
	{
		foreach($context['TPortal']['category']['col2'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	// additonal links etc.
	echo '
		<p class="titlebg">', $txt['wireless_navigation'], '</p>
		<p class="windowbg">[0] <a href="', $scripturl . '?action=forum;wap2" accesskey="0">' . $txt['tp-forum'], '</a></p>';
}

function template_imode_tp_frontpage()
{
	global $context, $txt, $scripturl;

	if(!isset($context['TPortal']['category']))
		return;

	echo '
	<div id="tpwap1" class="tpwap">
	<div class="titlebg">' . $context['linktree'][0]['name']  . '</div>';

	unset($context['TPortal']['article']);
	if(!empty($context['TPortal']['category']['featured']))
	{
		$context['TPortal']['article'] = $context['TPortal']['category']['featured'];
		render_frontp();
	}
	unset($context['TPortal']['article']);

	if(isset($context['TPortal']['category']['col1']))
	{
		foreach($context['TPortal']['category']['col1'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	unset($context['TPortal']['article']);
	if(isset($context['TPortal']['category']['col2']))
	{
		foreach($context['TPortal']['category']['col2'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	// additonal links etc.
	echo '
		<div class="titlebg">', $txt['wireless_navigation'], '</div>
		<div class="windowbg">[0] <a href="', $scripturl . '?action=forum;wap2" accesskey="0">' . $txt['tp-forum'], '</a></div>
	</div>';
}

function template_wap_tp_frontpage()
{
	global $context, $txt, $scripturl;

	if(!isset($context['TPortal']['category']))
		return;

	echo '
	<card id="main" title="', $context['page_title'], '">
		<p>' . $context['linktree'][0]['name']  . '</p>';

	unset($context['TPortal']['article']);
	if(!empty($context['TPortal']['category']['featured']))
	{
		$context['TPortal']['article'] = $context['TPortal']['category']['featured'];
		render_frontp();
	}
	unset($context['TPortal']['article']);

	if(isset($context['TPortal']['category']['col1']))
	{
		foreach($context['TPortal']['category']['col1'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	unset($context['TPortal']['article']);
	if(isset($context['TPortal']['category']['col2']))
	{
		foreach($context['TPortal']['category']['col2'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	// additonal links etc.
	echo '
		<p>', $txt['wireless_navigation'], '</p>
		<p>[0] <a href="', $scripturl . '?action=forum;wap2" accesskey="0">' . $txt['tp-forum'], '</a></p>
	</card>';
}

function render_frontp($single = false)
{
	global $context, $scripturl, $txt;

	echo '
		<p class="' , isset($context['TPortal']['article']['boardnews']) || $single ? 'catbg' : 'titlebg' , '">';

	if(in_array('title',$context['TPortal']['article']['visual_options']))
	{
		if(isset($context['TPortal']['article']['boardnews']))
			echo $context['TPortal']['article']['subject'];
		else
			echo $context['TPortal']['article']['subject'];
	}
	echo '
		</p>';

	echo '
		<p class="windowbg">
		' , tp_renderarticle() , '
		</p>';

	echo '
		<p>';

	if(!$single)
	{
		if(isset($context['TPortal']['article']['boardnews']))
			echo '
			<a href="' . $scripturl . '?topic=' . $context['TPortal']['article']['id'] . ';wap2">' . $txt['tp-readmore'] . '</a>';
		else
			echo '
			<a href="' . $scripturl . '?page=' . (!empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id']) . ';wap2">' . $txt['tp-readmore'] . '</a>';
		echo '
		</p>';
	}

	if($single && !empty($context['TPortal']['article']['comment_posts']) && sizeof($context['TPortal']['article']['comment_posts'])>0)
	{
		$counter = 1;
		echo '
		<p class="titlebg">'.$txt['tp-comments'].'</p>';
		foreach($context['TPortal']['article']['comment_posts'] as $post)
		{
			echo '
		<p class="windowbg">
		['.$counter.'] <b>'.$post['subject'].' ' . $txt['by'] .' '.$post['poster'].'</b>
		<br>
		'.$post['text'].'
		</p>	';


			$counter++;
		}
	}
}
function template_wap2_tp()
{
	if(isset($_GET['page']))
		template_wap2_tp_page();
	elseif(isset($_GET['cat']))
		template_wap2_tp_cat();
	elseif(!isset($_GET['page']) && !isset($_GET['cat']))
		template_wap2_tp_frontpage();

}
function template_wap_tp()
{
	if(isset($_GET['page']))
		template_wap_tp_page();
	elseif(isset($_GET['cat']))
		template_wap_tp_cat();
	elseif(!isset($_GET['page']) && !isset($_GET['cat']))
		template_wap_tp_frontpage();
}
function template_imode_tp()
{
	if(isset($_GET['page']))
		template_imode_tp_page();
	elseif(isset($_GET['cat']))
		template_imode_tp_cat();
	elseif(!isset($_GET['page']) && !isset($_GET['cat']))
		template_imode_tp_frontpage();

}
function template_wap2_tp_page()
{
	global $context, $txt, $scripturl;

	if(!isset($context['TPortal']['article']))
		return;

	if (!empty($context['linktree']))
		echo '<p class="titlebg">' . $context['linktree'][0]['name']  . '</p>';

	render_frontp(true);

	// additonal links etc.
	echo '
		<p class="titlebg">', $txt['wireless_navigation'], '</p>
		<p class="windowbg">[0] <a href="', $scripturl . '?cat=' , $context['TPortal']['article']['category'] , ';wap2" accesskey="0">' , $context['TPortal']['article']['category_name'], '</a></p>
		<p class="windowbg">[#] <a href="', $scripturl . '?action=forum;wap2" accesskey="#">' . $txt['tp-forum'], '</a></p>';
}
function template_imode_tp_page()
{
	global $context, $txt, $scripturl;

	if(!isset($context['TPortal']['article']))
		return;

	echo '
		<div id="tpwap2" class="tpwap">
	     <div class="titlebg"> '. (!empty($context['linktree']) ? $context['linktree'][0]['name'] : '')  . '</div>';

	render_frontp(true);

	// additonal links etc.
	echo '
		<div class="titlebg">', $txt['wireless_navigation'], '</div>
		<div class="windowbg">[0] <a href="', $scripturl . '?cat=' , $context['TPortal']['article']['category'] , ';wap2" accesskey="0">' , $context['TPortal']['article']['category_name'], '</a></div>
		<div class="windowbg">[#] <a href="', $scripturl . '?action=forum;wap2" accesskey="#">' . $txt['tp-forum'], '</a></div>
	</div>';
}
function template_wap_tp_page()
{
	global $context, $txt, $scripturl;

	if(!isset($context['TPortal']['article']))
		return;

	echo '
	<card id="main" title="', $context['page_title'], '">
		'. (!empty($context['linktree']) ? '<p>' . $context['linktree'][0]['name']  . '</p>' : '');

	render_frontp(true);

	// additonal links etc.
	echo '
		<p>', $txt['wireless_navigation'], '</p>
		<p>[0] <a href="', $scripturl . '?cat=' , $context['TPortal']['article']['category'] , ';wap2" accesskey="0">' , $context['TPortal']['article']['category_name'], '</a></p>
		<p>[#] <a href="', $scripturl . '?action=forum;wap2" accesskey="#">' . $txt['tp-forum'], '</a></p>
	</card>';
}
function template_wap2_tp_cat()
{
	global $context, $txt, $scripturl;

	if(!isset($context['TPortal']['category']))
		return;

	echo '
	<p class="titlebg">' . $context['TPortal']['category']['catname'] . ' > ' . $context['TPortal']['category']['value1'] . '</p>';

	unset($context['TPortal']['article']);
	if(!empty($context['TPortal']['category']['featured']))
	{
		$context['TPortal']['article'] = $context['TPortal']['category']['featured'];
		render_frontp();
	}
	unset($context['TPortal']['article']);

	if(isset($context['TPortal']['category']['col1']))
	{
		foreach($context['TPortal']['category']['col1'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	unset($context['TPortal']['article']);
	if(isset($context['TPortal']['category']['col2']))
	{
		foreach($context['TPortal']['category']['col2'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	// additonal links etc.
	echo '
		<p class="titlebg">', $txt['wireless_navigation'], '</p>
		<p class="windowbg">[0] <a href="', $scripturl . '?cat=' , $context['TPortal']['category']['value2'] , ';wap2" accesskey="0">' , $context['TPortal']['category']['catname'], '</a></p>
		<p class="windowbg">[#] <a href="', $scripturl . '?action=forum;wap2" accesskey="#">' . $txt['tp-forum'], '</a></p>';
}

function template_imode_tp_cat()
{
	global $context, $txt, $scripturl;

	if(!isset($context['TPortal']['category']))
		return;

	echo '
		<div id="tpwap3" class="tpwap">
	     <div class="titlebg">' . $context['TPortal']['category']['catname'] . ' > ' . $context['TPortal']['category']['value1'] . '</div>';

	unset($context['TPortal']['article']);
	if(!empty($context['TPortal']['category']['featured']))
	{
		$context['TPortal']['article'] = $context['TPortal']['category']['featured'];
		render_frontp();
	}
	unset($context['TPortal']['article']);

	if(isset($context['TPortal']['category']['col1']))
	{
		foreach($context['TPortal']['category']['col1'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	unset($context['TPortal']['article']);
	if(isset($context['TPortal']['category']['col2']))
	{
		foreach($context['TPortal']['category']['col2'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	// additonal links etc.
	echo '
		<div class="titlebg">', $txt['wireless_navigation'], '</div>
		<div class="windowbg">[0] <a href="', $scripturl . '?cat=' , $context['TPortal']['category']['value2'] , ';wap2" accesskey="0">' , $context['TPortal']['category']['catname'], '</a></div>
		<div class="windowbg">[#] <a href="', $scripturl . '?action=forum;wap2" accesskey="#">' . $txt['tp-forum'], '</a></div>
	</div>';
}

function template_wap_tp_cat()
{
	global $context, $txt, $scripturl;

	if(!isset($context['TPortal']['category']))
		return;

	echo '
	<card id="main" title="', $context['page_title'], '">
	<p>' . $context['TPortal']['category']['catname'] . ' > ' . $context['TPortal']['category']['value1'] . '</p>';

	unset($context['TPortal']['article']);
	if(!empty($context['TPortal']['category']['featured']))
	{
		$context['TPortal']['article'] = $context['TPortal']['category']['featured'];
		render_frontp();
	}
	unset($context['TPortal']['article']);

	if(isset($context['TPortal']['category']['col1']))
	{
		foreach($context['TPortal']['category']['col1'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	unset($context['TPortal']['article']);
	if(isset($context['TPortal']['category']['col2']))
	{
		foreach($context['TPortal']['category']['col2'] as $article => $context['TPortal']['article'])
		{
			render_frontp();
			unset($context['TPortal']['article']);
		}
	}
	// additonal links etc.
	echo '
		<p>', $txt['wireless_navigation'], '</p>
		<p>[0] <a href="', $scripturl . '?cat=' , $context['TPortal']['category']['value2'] , ';wap2" accesskey="0">' , $context['TPortal']['category']['catname'], '</a></p>
		<p>[#] <a href="', $scripturl . '?action=forum;wap2" accesskey="#">' . $txt['tp-forum'], '</a></p>
	</card>';
}

function template_wap2_tp_dl_cat() { return; }
function template_wap2_tp_dl_item() { return; }
function template_wap2_tp_dl() { return; }
function template_wap2_tp_dl_main() { return; }
function template_imode_tp_dl() { return; }
function template_imode_tp_dl_main() { return; }
function template_imode_tp_dl_item() { return; }
function template_imode_tp_dl_cat() { return; }
function template_wap_tp_dl() { return; }
function template_wap_tp_dl_main() { return; }
function template_wap_tp_dl_item() { return; }
function template_wap_tp_dl_cat() { return; }

?>
