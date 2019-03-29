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
 * Copyright (C) 2019 - The TinyPortal Team
 *
 */

function template_main()
{
	global $context, $settings, $txt, $scripturl;

	if(isset($context['TPortal']['subaction'])){
		switch($context['TPortal']['subaction']){
		case 'myarticles':
			echo '
        <div class="cat_bar">
            <h3 class="catbg">' .$txt['tp-myarticles'] . '</h3>
        </div>
		<div class="windowbg padding-div">
	<table class="table_grid tp_grid tp_grid" style="width:100%";>
		<thead>
			<tr class="title_bar titlebg2">
			<th scope="col" class="myarticles">
				<div class="font-strong" style="padding:0px;">
					<div align="center" class="float-items title-admin-area">', $context['TPortal']['tpsort']=='subject' ? '<img src="' .$settings['tp_images_url']. '/TPsort_up.png" alt="" /> ' : '' ,'<a href="'.$scripturl.'?action=tportal;sa=myarticles;tpsort=subject">'.$txt['subject'].'</a></div>
				</div>
			</th>
			</tr>
		</thead>
		<tbody>';

			if(count($context['TPortal']['myarticles'])>0)
			{
				foreach($context['TPortal']['myarticles'] as $art)
				{
					echo '
				<tr class="windowbg">
				<td class="articles">
					<div style="overflow: hidden; padding: 3px;">
						<div style="float: right;">';
				if($art['approved']==0)
						echo '<img src="' . $settings['tp_images_url'] . '/TPthumbdown.png" alt="*" /> ';
				if($art['off']==0 && $art['approved']==1)
						echo '<img src="' . $settings['tp_images_url'] . '/TPactive2.png" alt="*" /> ';
				else
						echo '<img src="' . $settings['tp_images_url'] . '/TPactive1.png" alt="*" /> ';

				if($art['locked']==1)
						echo '<img src="' . $settings['tp_images_url'] . '/TPlock1.png" alt="*" /> ';

				if((allowedTo('tp_editownarticle') && $art['locked']==0) && !allowedTo('tp_articles'))
					echo '
					<a href="' . $scripturl . '?action=tportal;sa=editarticle;article='.$art['id'].'"><img src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="*" /></a>';
				elseif(allowedTo('tp_articles'))
					echo '
					<a href="' . $scripturl . '?action=tpadmin;sa=editarticle;article='.$art['id'].'"><img src="' . $settings['tp_images_url'] . '/TPmodify.png" alt="*" /></a>';

					echo '
						</div>';

					if($art['off'] == 0 && $art['approved'] == 1)
						echo '
						<a href="' . $scripturl . '?page='.$art['id'].'">' . html_entity_decode($art['subject']) . '</a>';
					else
						echo '
					(<i>' . html_entity_decode($art['subject']). '</i>)';

					echo '
					</div>
				</td>
				</tr>';
				}
			}
			else
			{
				echo '
				<tr class="windowbg">
				<td class="articles"> 
				'. $txt['tp-noarticlesfound'] .'
				</td>
				</tr>';
			}
		echo '
			</tbody>
		</table>';

			if(!empty($context['TPortal']['pageindex']))
				echo '
				<div class="middletext padding-div">' . $context['TPortal']['pageindex'] . '</div>
				<div class="padding-div"></div>';
			echo '
		</div>';

			break;
		}
	}
	else
		redirectexit();
}

function template_dlsubmitsuccess()
{
	global $txt;

	echo '
		<div class="tborder">
                <div class="cat_bar">
				    <h3 class="catbg">'.$txt['tp-dlsubmitsuccess2'].'</h3>
                </div>
					<div class="windowbg padding-div" style="text-align: center;">'.$txt['tp-dlsubmitsuccess'].'
					<div class="padding-div">&nbsp;</div></div>
		</div>';
}

function template_updatelog()
{
	global $context;

	echo '<div class="tborder">' . $context['TPortal']['updatelog'] , '<hr /></div>';
}


?>
