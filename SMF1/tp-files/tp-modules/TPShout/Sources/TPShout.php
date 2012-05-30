<?php
/**
 * @package TinyPortal
 * @version 1.0
 * @author IchBin - ichbin@ichbin.us
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2012 - The TinyPortal Team
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');
	

global $maintenance, $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $ID_MEMBER, $boarddir, $boardurl, $options, $sourcedir;

// set version for databse updates.
$shoutboxversion='101';

$shoutboxtemplate='111';

$tp_prefix = $settings['tp_prefix'];

// check if it needs updating...
if((isset($context['TPortal']['shoutbox_version']) && $shoutboxversion != $context['TPortal']['shoutbox_version']) || !isset($context['TPortal']['shoutbox_version']))
	shoutbox_update();


	// bbc code for shoutbox
	$context['html_headers'] .= '
      <script type="text/javascript"><!-- // --><![CDATA[
                var current_header_smiley = ';
		if(empty($options['expand_header_smiley']))
			$context['html_headers'] .= 'false'; 
		else 
			$context['html_headers'] .= 'true';
		
		$context['html_headers'] .= '
                function expandHeaderSmiley(mode)
                {';

        // Guests don't have theme options!!
        if ($context['user']['is_guest'])
                $context['html_headers'] .= '
                        document.cookie = "expandsmiley=" + (mode ? 1 : 0);';
        else
                $context['html_headers'] .= '
                        smf_setThemeOption("expand_header_smily", mode ? 1 : 0, null, "'. $context['session_id']. '");';

        $context['html_headers'] .= '
                        document.getElementById("expand_smiley").src = mode ? "'.$settings['tp_images_url'].'/TPcollapse.gif" : "'.$settings['tp_images_url'].'/TPexpand.gif";

                        document.getElementById("expandHeaderSmiley").style.display = mode ? "" : "none";

                        current_header_smiley = mode;
                }
        // ]]></script>
		<script type="text/javascript"><!-- // --><![CDATA[
                var current_header_bbc = ';
		if(empty($options['expand_header_bbc']))
			$context['html_headers'] .= 'false'; 
		else 
			$context['html_headers'] .= 'true';
		
		$context['html_headers'] .= '
                function expandHeaderBBC(mode)
                {';

        if ($context['user']['is_guest'])
                $context['html_headers'] .= '
                        document.cookie = "expandbbc=" + (mode ? 1 : 0);';
        else
                $context['html_headers'] .= '
                        smf_setThemeOption("expand_header_bbc", mode ? 1 : 0, null, "'. $context['session_id']. '");';

        $context['html_headers'] .= '
                        document.getElementById("expand_bbc").src = mode ? "'.$settings['tp_images_url'].'/TPcollapse.gif" : "'.$settings['tp_images_url'].'/TPexpand.gif";

                        document.getElementById("expandHeaderBBC").style.display = mode ? "" : "none";

                        current_header_bbc = mode;
                }
        // ]]></script>';

	if(file_exists($settings['theme_dir'].'/TPShout.css'))
		$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/TPShout.css?fin11" />';
	else
		$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['default_theme_url']. '/TPShout.css?fin11" />';
	
	if($context['TPortal']['shoutbox_usescroll']>0)
		$context['html_headers'] .= '
		<style type="text/css">

		#marqueecontainer{
		position: relative;
		width: 95%; /*marquee width */
		height: '. $context['TPortal']['shoutbox_height'] . 'px; /*marquee height */
		overflow: hidden;
		padding: 2px;
		padding-left: 4px;
		}

		</style>

		<script type="text/javascript"><!-- // --><![CDATA[

		/***********************************************
		* Cross browser Marquee II- © Dynamic Drive (www.dynamicdrive.com)
		* This notice MUST stay intact for legal use
		* Visit http://www.dynamicdrive.com/ for this script and 100s more.
		***********************************************/

		var delayb4scroll=1000 //Specify initial delay before marquee starts to scroll on page (2000=2 seconds)
		var marqueespeed=' . (!empty($context['TPortal']['shoutbox_scrollspeed']) ? $context['TPortal']['shoutbox_scrollspeed'] : '1') . '
		var pauseit=1 //Pause marquee onMousever (0=no. 1=yes)?

		////NO NEED TO EDIT BELOW THIS LINE////////////

		var copyspeed=marqueespeed
		var pausespeed=(pauseit==0)? copyspeed: 0
		var actualheight=\'\'

		function scrollmarquee(){
		if (parseInt(cross_marquee.style.top)>(actualheight*(-1)+8))
		cross_marquee.style.top=parseInt(cross_marquee.style.top)-copyspeed+"px"
		else
		cross_marquee.style.top=parseInt(marqueeheight)+8+"px"
		}

		function initializemarquee(){
		cross_marquee=document.getElementById("vmarquee")
		if(cross_marquee == null)
		   return
		cross_marquee.style.top=0
		marqueeheight=document.getElementById("marqueecontainer").offsetHeight
		actualheight=cross_marquee.offsetHeight
		if (window.opera || navigator.userAgent.indexOf("Netscape/7")!=-1){ //if Opera or Netscape 7x, add scrollbars to scroll and exit
		cross_marquee.style.height=marqueeheight+"px"
		cross_marquee.style.overflow="scroll"
		return
		}
		setTimeout(\'lefttime=setInterval("scrollmarquee()",30)\', delayb4scroll)
		}

		if (window.addEventListener)
		window.addEventListener("load", initializemarquee, false)
		else if (window.attachEvent)
		window.attachEvent("onload", initializemarquee)
		else if (document.getElementById)
		window.onload=initializemarquee
	// ]]></script>';


if(isset($_GET['shout']))
{
	if($_GET['shout'] == 'admin')
		tpshout_admin();
	elseif($_GET['shout'] == 'save' && !isset($_POST['tp-shout-url']))
		redirectexit();
	else
	{
		$number = substr($_GET['shout'],4);
		if(!is_numeric($number))
			$number=10;
		tpshout_bigscreen(false, $number);
	}
}

// we got a shout!
if(isset($_POST['tp-shout-url']))
{
	 if(isset($_POST['tp_shout']))
	 {
		// Check the session id.
		checkSession('post');
		
		$oldshout=strip_tags(substr($_POST['tp_shout'],0,300));
		$shout = $oldshout;
		
		// collect the color for shoutbox
		$request= tp_query("SELECT grp.onlineColor FROM (" . $db_prefix . "members as m, " . $db_prefix . "membergroups as grp)
			WHERE m.ID_GROUP=grp.ID_GROUP
			AND ID_MEMBER=".$ID_MEMBER." LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$row=tpdb_fetch_row($request);
			$context['TPortal']['usercolor']=$row[0];
			tpdb_free_result($request);
		}
        if(empty($_POST['tp-shout-name']) || $user_info['is_guest'] && !$context['TPortal']['guest_shout'])
            redirectexit(strip_tags($_POST['tp-shout-url']),false,true);

        // Build the name with color for user, otherwise strip guests name of html tags.
        $shout_name = ($context['user']['id'] != 0) ? '<a href="'.$scripturl.'?action=profile;u='.$context['user']['id'].'"' : strip_tags($_POST['tp-shout-name']);
        if(!empty($context['TPortal']['usercolor']))
            $shout_name .= ' style="color: '. $context['TPortal']['usercolor'] . '"';
        $shout_name .= ($context['user']['id'] != 0) ? '>'.$context['user']['name'].'</a>' : '';
        
        $shout_time=time();
        
        // register the IP and userID, if any
        $ip=$user_info['ip'];
        $memID = $context['user']['id'];
        
        if($shout!='')
            tp_query("INSERT INTO " . $tp_prefix . "shoutbox (value1,value2,value3,type,value4, value5) 
                        VALUES('". mysql_real_escape_string($shout)."','".$shout_time."','".mysql_real_escape_string($shout_name)."','shoutbox','".$ip."',".$memID.")", __FILE__, __LINE__);
    }
	redirectexit(strip_tags($_POST['tp-shout-url']),false,true);
}

function tpshout_admin()
{

	global $maintenance, $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $ID_MEMBER, $boarddir, $boardurl, $options, $sourcedir;
	
	// check permissions
	isAllowedTo('tp_can_admin_shout');

	$tp_prefix = $settings['tp_prefix'];
	if(isset($_GET['p']) && is_numeric($_GET['p']))
		$tpstart=$_GET['p'];
	else
		$tpstart=0;

	loadtemplate('TPShout');
	loadlanguage('TPShout');
	$context['template_layers'][] = 'subtab';
	loadlanguage('TPortalAdmin');

	adminIndex('shout',true);
	$context['current_action'] = 'admin';

	if(isset($_REQUEST['send']) || isset($_REQUEST[$txt['tp-send']]) || isset($_REQUEST['tp_preview']) || isset($_REQUEST['TPadmin_blocks']))
	{
		$go=0;
		foreach ($_POST as $what => $value) 
		{
			// from shoutbox admin
			if($what=='tp_shoutbox_smile')
			{
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='".$value."' WHERE name='show_shoutbox_smile'", __FILE__, __LINE__);
				$go=1;
			}
			elseif($what=='tp_shoutbox_icons')
			{
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='show_shoutbox_icons'", __FILE__, __LINE__);
				$go=1;
			}
			elseif($what=='tp_shoutbox_height')
			{
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='shoutbox_height'", __FILE__, __LINE__);
				$go=1;
			}
			elseif($what=='tp_show_profile_shouts')
			{
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='profile_shouts_hide'", __FILE__, __LINE__);
			}
			elseif($what=='tp_shoutbox_limit')
			{
				if(!is_numeric($value))
					$value=10;
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='shoutbox_limit'", __FILE__, __LINE__);
				$go=1;
			}
			elseif($what=='tp_shoutbox_usescroll')
			{
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='shoutbox_usescroll'", __FILE__, __LINE__);
				$go=1;
			}
			elseif($what=='tp_shoutbox_scrollduration')
			{
				if($value>6000)
					$value=6000;
				elseif($value<500)
					$value=500;
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='shoutbox_scrollduration'", __FILE__, __LINE__);
				$go=1;
			}
			elseif($what=='tp_shoutbox_scrolldirection')
			{
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='shoutbox_scrolldirection'", __FILE__, __LINE__);
				$go=1;
			}
			elseif($what=='tp_shoutbox_scrolleasing')
			{
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='shoutbox_scrolleasing'", __FILE__, __LINE__);
				$go=1;
			}
			elseif($what=='tp_shoutbox_scrolldelay')
			{
				if($value>6000)
					$value=6000;
				elseif($value<500)
					$value=500;
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='shoutbox_scrolldelay'", __FILE__, __LINE__);
				$go=1;
			}
			// from shoutbox
			elseif($what=='tp_showshouts')
			{
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='show_shoutbox_archive'", __FILE__, __LINE__);
			}
			elseif(substr($what,0,16)=='tp_shoutbox_item')
			{
				$val=substr($what,16);
				 tp_query("UPDATE " . $tp_prefix . "shoutbox SET value1='". htmlspecialchars($value, ENT_QUOTES). "' WHERE id=" .$val, __FILE__, __LINE__);
				$go=2;
			}
			elseif(substr($what,0,18)=='tp_shoutbox_remove')
			{
				$val=substr($what,18);
				 tp_query("DELETE FROM " . $tp_prefix . "shoutbox WHERE id=$val", __FILE__, __LINE__);
				$go=2;
			}
			elseif($what=='tp_shoutsdelall' && $value=='ON')
			{
				 tp_query("DELETE FROM " . $tp_prefix . "shoutbox WHERE type='shoutbox'", __FILE__, __LINE__);
				$go=2;
			}
			elseif($what=='tp_guest_shout')
			{
				 tp_query("UPDATE " . $tp_prefix . "settings SET value='$value' WHERE name='guest_shout'", __FILE__, __LINE__);
			}
		}
		if($go==1)
			redirectexit('action=tpmod;shout=admin;settings');
		else
			redirectexit('action=tpmod;shout=admin');
	}
	
	// get latest shouts for admin section
	// check that a member has been filtered
	if(isset($_GET['u']))
		$memID=$_GET['u'];
	// check that a IP has been filtered
	if(isset($_GET['ip']))
		$ip=$_GET['ip'];
	if(isset($_GET['s']))
		$single=$_GET['s'];

	$context['TPortal']['admin_shoutbox_items']=array();

	if(isset($memID))
	{
		$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "shoutbox WHERE type='shoutbox' AND value5=$memID AND value7=0", __FILE__, __LINE__);
		$weh=tpdb_fetch_row($request);
		tpdb_free_result($request);
		$allshouts=$weh[0];
		$context['TPortal']['admin_shoutbox_items_number']=$allshouts;
		$context['TPortal']['shoutbox_pageindex'] = 'Member '.$memID.' filtered (<a href="'.$scripturl.'?action=tpmod;shout=admin">' . $txt[193] . '</a>) <br />'.TPageIndex($scripturl.'?action=tpmod;shout=admin;u='.$memID, $tpstart, $allshouts, 10, true);
		$request =  tp_query("SELECT * FROM " . $tp_prefix . "shoutbox WHERE type='shoutbox' AND value5=$memID AND value7=0 ORDER BY value2 DESC LIMIT $tpstart,10", __FILE__, __LINE__);
	}
	elseif(isset($ip))
	{
		$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "shoutbox WHERE type='shoutbox' AND value4='$ip' AND value7=0", __FILE__, __LINE__);
		$weh=tpdb_fetch_row($request);
		tpdb_free_result($request);
		$allshouts=$weh[0];
		$context['TPortal']['admin_shoutbox_items_number']=$allshouts;
		$context['TPortal']['shoutbox_pageindex'] = 'IP '.$ip.' filtered (<a href="'.$scripturl.'?action=tpmod;shout=admin">' . $txt[193] . '</a>) <br />'.TPageIndex($scripturl.'?action=tpadmin;sa=shoutbox,ip='.urlencode($ip) , $tpstart, $allshouts, 10,true);
		$request =  tp_query("SELECT * FROM " . $tp_prefix . "shoutbox WHERE type='shoutbox' AND value4='$ip' AND value7=0 ORDER BY value2 DESC LIMIT $tpstart,10", __FILE__, __LINE__);
	}
	elseif(isset($single))
	{
		// check session
		checkSession('get');
		$context['TPortal']['shoutbox_pageindex']='';
		$request =  tp_query("SELECT * FROM " . $tp_prefix . "shoutbox WHERE type='shoutbox' AND value7=0 AND id=".$single, __FILE__, __LINE__);
	}
	else
	{
		$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "shoutbox WHERE type='shoutbox' AND value7=0", __FILE__, __LINE__);
		$weh=tpdb_fetch_row($request);
		tpdb_free_result($request);
		$allshouts=$weh[0];
		$context['TPortal']['admin_shoutbox_items_number']=$allshouts;
		$context['TPortal']['shoutbox_pageindex'] = TPageIndex($scripturl.'?action=tpmod;shout=admin', $tpstart, $allshouts, 10,true);
		$request =  tp_query("SELECT * FROM " . $tp_prefix . "shoutbox WHERE type='shoutbox' AND value7=0 ORDER BY value2 DESC LIMIT $tpstart,10", __FILE__, __LINE__);
	}

	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			$context['TPortal']['admin_shoutbox_items'][] = array(
				'id' => $row['id'],
				'body' => html_entity_decode($row['value1'], ENT_QUOTES),
				'poster' => $row['value3'],
				'timestamp' => $row['value2'],
				'time' => timeformat($row['value2']),
				'ip' => $row['value4'],
				'ID_MEMBER' => $row['value5'],
				'sort_member' => '<a href="'.$scripturl.'?action=tpmod;shout=admin;u='.$row['value5'].'">'.$txt['tp-allshoutsbymember'].'</a>', 
				'is_whisper' => $row['value6'],
				'whisper_who' => $row['value8'],
				'sort_ip' => '<a href="'.$scripturl.'?action=tpmod;shout=admin;ip='.$row['value4'].'">'.$txt['tp-allshoutsbyip'].'</a>',
				'single' => isset($single) ? '<hr><a href="'.$scripturl.'?action=tpmod;shout=admin"><b>'.$txt['tp-allshouts'].'</b></a>' : '',
			);
		}
		tpdb_free_result($request);
	}


	// setup menu items
	$context['admin_area']= 'tp_can_admin_shout';
	$context['admin_tabs'] = array(
		'title' => $txt['tp-shoutboxheader'],
		'help' => $txt['tp-shoutboxhelp'],
		'description' => $txt['tp-shoutboxdescription'],
		'tabs' => array(),
	);
	if (allowedTo('tp_can_admin_shout'))
	{
		$context['admin_tabs']['tabs'] = array(
			'shoutbox_settings' => array(
				'title' => $txt['tp-settings'],
				'description' => '',
				'href' => $scripturl . '?action=tpmod;shout=admin;settings',
				'is_selected' => (isset($_GET['action']) && $_GET['action']=='tpmod' && isset($_GET['shout']) && $_GET['shout']=='admin' && isset($_GET['settings'])) ? true : false,
			),
			'shoutbox' => array(
				'title' => $txt['tp-tabs10'],
				'description' => '',
				'href' => $scripturl . '?action=tpmod;shout=admin',
				'is_selected' => (isset($_GET['action']) && $_GET['action']=='tpmod' && isset($_GET['shout']) && $_GET['shout']=='admin' && !isset($_GET['settings'])) ? true : false,
			),
		);
		$context['admin_header']['tp_shout']=$txt['tp_shout'];
	}
	// on settings screen?
	if(isset($_GET['settings']))
		$context['sub_template'] = 'tpshout_admin_settings';
	else
		$context['sub_template'] = 'tpshout_admin';
	
	$context['page_title'] = 'Shoutbox admin';

	tp_hidebars();

}
function tpshout_bigscreen($state, $number = 10)
{

	global $maintenance, $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $ID_MEMBER, $boarddir, $boardurl, $options, $sourcedir;
	
	$tp_prefix = $settings['tp_prefix'];
	loadtemplate('TPShout');

	$context['TPortal']['rendershouts'] = tpshout_fetch(false, $number);
	TP_setThemeLayer('tpshout','TPShout','tpshout_bigscreen');
	$context['page_title'] = 'Shoutbox';
}
// fetch all the shouts for output
function tpshout_fetch($render = true, $limit=1, $swap=false)
{
    global $maintenance, $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $ID_MEMBER, $boarddir, $boardurl, $options, $sourcedir;

	$tp_prefix = $settings['tp_prefix'];

	// get x number of shouts
	$context['TPortal']['profile_shouts_hide'] = empty($context['TPortal']['profile_shouts_hide']) ? '0' : '1';
	$context['TPortal']['usercolor']='';
	// collect the color for shoutbox
	$request= tp_query("SELECT grp.onlineColor FROM (" . $db_prefix . "members as m, " . $db_prefix . "membergroups as grp)
		WHERE m.ID_GROUP=grp.ID_GROUP
		AND ID_MEMBER=$ID_MEMBER LIMIT 1", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0){
		$row=tpdb_fetch_row($request);
		$context['TPortal']['usercolor']=$row[0];
		tpdb_free_result($request);
	}

	if(is_numeric($context['TPortal']['shoutbox_limit']) && $limit==1)
		$limit=$context['TPortal']['shoutbox_limit'];

	// don't fetch more than a hundred - save the poor server! :D
	$nshouts = '';
	if($limit>100) $limit=100;
	
	if($render)
		loadtemplate('TPShout');
	
	$scrolldirection = array('vert' => 'v', 'horiz' => 'h');
	$request =  tp_query("SELECT s.*, IFNULL(s.value3, mem.realName) as realName,
	mem.avatar,	IFNULL(a.ID_ATTACH, 0) AS ID_ATTACH, a.filename, a.attachmentType
	FROM " . $tp_prefix . "shoutbox as s 
	LEFT JOIN " . $db_prefix . "members AS mem ON (mem.ID_MEMBER = s.value5)
	LEFT JOIN " . $db_prefix . "attachments AS a ON (a.ID_MEMBER = s.value5 and a.attachmentType!=3)
	WHERE s.value7=0 
	ORDER BY s.value2 DESC LIMIT ". $limit, __FILE__, __LINE__);
	if (tpdb_num_rows($request) > 0)
	{
		$nshouts= $txt['tp-last'].' '.$limit.' '.$txt['tp-shouts'].'<br /><br /><div id="allshouts'.(!$render ? '_big' : '').'" class="qscroller'.(!$render ? '_big' : '').'"></div><div class="hide'.(!$render ? '_big' : '').'">';
		$ns=array();
		while($row = tpdb_fetch_assoc($request))
		{
            $row['value1'] = stripslashes($row['value1']);
			$row['avatar'] = $row['avatar'] == '' ? ($row['ID_ATTACH'] > 0 ? '<img src="' . (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) . '" alt="&nbsp;"  />' : '') : (stristr($row['avatar'], 'http://') ? '<img src="' . $row['avatar'] . '" alt="&nbsp;" />' : '<img src="' . $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avatar'], ENT_QUOTES) . '" alt="&nbsp;" />');
			$ns[] = template_singleshout($row);
		}
		$nshouts .= implode("", $ns);
		
		$nshouts .='</div>';

		$context['TPortal']['shoutbox']=$nshouts;
		tpdb_free_result($request);
	}
   // include the scolling code if shoutbox is set to scroll
	// its from a block, render it
	if($render)
		template_tpshout_shoutblock();
	else
		return $nshouts;
}

function shout_bcc_code($collapse=true) 
{
    global $maintenance, $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $ID_MEMBER, $boarddir, $boardurl, $options, $sourcedir;

	loadLanguage('Post');
	
	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		function tp_bbc_highlight(something, mode)
		{
			something.style.backgroundImage = "url(" + smf_images_url + (mode ? "/bbc/bbc_hoverbg.gif)" : "/bbc/bbc_bg.gif)");
		}
	// ]]></script>';

	// The below array makes it dead easy to add images to this page. Add it to the array and everything else is done for you!
	$context['tp_bbc_tags'] = array();
	$context['tp_bbc_tags2'] = array();
	$context['tp_bbc_tags'][] = array(
		'bold' => array('code' => 'b', 'before' => '[b]', 'after' => '[/b]', 'description' => $txt[253]),
		'italicize' => array('code' => 'i', 'before' => '[i]', 'after' => '[/i]', 'description' => $txt[254]),
		'img' => array('code' => 'img', 'before' => '[img]', 'after' => '[/img]', 'description' => $txt[435]),
		'quote' => array('code' => 'quote', 'before' => '[quote]', 'after' => '[/quote]', 'description' => $txt[260]),
	);
	$context['tp_bbc_tags2'][] = array(
		'underline' => array('code' => 'u', 'before' => '[u]', 'after' => '[/u]', 'description' => $txt[255]),
		'strike' => array('code' => 's', 'before' => '[s]', 'after' => '[/s]', 'description' => $txt[441]),
		'glow' => array('code' => 'glow', 'before' => '[glow=red,2,300]', 'after' => '[/glow]', 'description' => $txt[442]),
		'shadow' => array('code' => 'shadow', 'before' => '[shadow=red,left]', 'after' => '[/shadow]', 'description' => $txt[443]),
		'move' => array('code' => 'move', 'before' => '[move]', 'after' => '[/move]', 'description' => $txt[439]),
	);
	
	if($collapse)
		echo '
	<a href="#" onclick="expandHeaderBBC(!current_header_bbc); return false;"><img id="expand_bbc" src="', $settings['tp_images_url'], '/', empty($options['expand_header_bbc']) ? 'TPexpand.gif' : 'TPcollapse.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin-right: 5px; position: relative; top: 5px;" align="left" /></a>
<div id="shoutbox_bbc">';
	else
		echo '<div>';

	$found_button = false;
	// Here loop through the array, printing the images/rows/separators!
	if(isset($context['tp_bbc_tags'][0]) && count($context['tp_bbc_tags'][0])>0)
	{
		foreach ($context['tp_bbc_tags'][0] as $image => $tag)
		{
			// Is there a "before" part for this bbc button? If not, it can't be a button!!
			if (isset($tag['before']))
			{
				// Is this tag disabled?
				if (!empty($context['disabled_tags'][$tag['code']]))
					continue;

				$found_button = true;

				// If there's no after, we're just replacing the entire selection in the post box.
				if (!isset($tag['after']))
					echo '<a href="javascript:void(0);" onclick="replaceText(\'', $tag['before'], '\', document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '); return false;">';
				// On the other hand, if there is one we are surrounding the selection ;).
				else
					echo '<a href="javascript:void(0);" onclick="surroundText(\'', $tag['before'], '\', \'', $tag['after'], '\', document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '); return false;">';

				// Okay... we have the link. Now for the image and the closing </a>!
				echo '<img onmouseover="tp_bbc_highlight(this, true);" onmouseout="if (window.tp_bbc_highlight) tp_bbc_highlight(this, false);" src="', $settings['images_url'], '/bbc/', $image, '.gif" align="bottom" width="23" height="22" alt="', $tag['description'], '" title="', $tag['description'], '" style="background-image: url(', $settings['images_url'], '/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;" /></a>';
			}
			// I guess it's a divider...
			elseif ($found_button)
			{
				echo '<img src="', $settings['images_url'], '/bbc/divider.gif" alt="|" style="margin: 0 3px 0 3px;" />';
				$found_button = false;
			}
		}
	}
	
	if($collapse)
		echo '
	<div id="expandHeaderBBC"', empty($options['expand_header_bbc']) ? ' style="display: none;"' : 'style="display: inline;"' , '>';
	else
		echo '
	<div style="display: inline;">';

	$found_button = false;
	// Here loop through the array, printing the images/rows/separators!
	if(isset($context['tp_bbc_tags2'][0]) && count($context['tp_bbc_tags2'][0])>0)
	{
		foreach ($context['tp_bbc_tags2'][0] as $image => $tag)
		{
			// Is there a "before" part for this bbc button? If not, it can't be a button!!
			if (isset($tag['before']))
			{
				// Is this tag disabled?
				if (!empty($context['disabled_tags'][$tag['code']]))
					continue;

				$found_button = true;

				// If there's no after, we're just replacing the entire selection in the post box.
				if (!isset($tag['after']))
					echo '<a href="javascript:void(0);" onclick="replaceText(\'', $tag['before'], '\', document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '); return false;">';
				// On the other hand, if there is one we are surrounding the selection ;).
				else
					echo '<a href="javascript:void(0);" onclick="surroundText(\'', $tag['before'], '\', \'', $tag['after'], '\', document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '); return false;">';

				// Okay... we have the link. Now for the image and the closing </a>!
				echo '<img onmouseover="tp_bbc_highlight(this, true);" onmouseout="if (window.tp_bbc_highlight) tp_bbc_highlight(this, false);" src="', $settings['images_url'], '/bbc/', $image, '.gif" align="bottom" width="23" height="22" alt="', $tag['description'], '" title="', $tag['description'], '" style="background-image: url(', $settings['images_url'], '/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;" /></a>';
			}
			// I guess it's a divider...
			elseif ($found_button)
			{
				echo '<img src="', $settings['images_url'], '/bbc/divider.gif" alt="|" style="margin: 0 3px 0 3px;" />';
				$found_button = false;
			}
		}
	}
	// Print a drop down list for all the colors we allow!
	if (!isset($context['shout_disabled_tags']['color']))
		echo ' <br /><select onchange="surroundText(\'[color=\' + this.options[this.selectedIndex].value.toLowerCase() + \']\', \'[/color]\', document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '); this.selectedIndex = 0; document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '.focus(document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '.caretPos);" style="margin: 5px auto 10px auto;">
						<option value="" selected="selected">'. $txt['change_color']. '</option>
						<option value="Black">Black</option>
						<option value="Red">Red</option>
						<option value="Yellow">Yellow</option>
						<option value="Pink">Pink</option>
						<option value="Green">Green</option>
						<option value="Orange">Orange</option>
						<option value="Purple">Purple</option>
						<option value="Blue">Blue</option>
						<option value="Beige">Beige</option>
						<option value="Brown">Brown</option>
						<option value="Teal">Teal</option>
						<option value="Navy">Navy</option>
						<option value="Maroon">Maroon</option>
						<option value="LimeGreen">LimeGreen</option>
					</select>';
	echo '<br />';

	$found_button = false;
	// Print the buttom row of buttons!
	if(isset($context['tp_bbc_tags'][1]) && count($context['tp_bbc_tags'][1])>0)
	{
		foreach ($context['tp_bbc_tags'][1] as $image => $tag)
		{
			if (isset($tag['before']))
			{
				// Is this tag disabled?
				if (!empty($context['shout_disabled_tags'][$tag['code']]))
					continue;

				$found_button = true;

				// If there's no after, we're just replacing the entire selection in the post box.
				if (!isset($tag['after']))
					echo '<a href="javascript:void(0);" onclick="replaceText(\'', $tag['before'], '\', document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '); return false;">';
				// On the other hand, if there is one we are surrounding the selection ;).
				else
					echo '<a href="javascript:void(0);" onclick="surroundText(\'', $tag['before'], '\', \'', $tag['after'], '\', document.forms.', $context['tp_shoutbox_form'], '.', $context['shout_post_box_name'], '); return false;">';

				// Okay... we have the link. Now for the image and the closing </a>!
				echo '<img onmouseover="tp_bbc_highlight(this, true);" onmouseout="if (window.tp_bbc_highlight) tp_bbc_highlight(this, false);" src="', $settings['images_url'], '/bbc/', $image, '.gif" align="bottom" width="23" height="22" alt="', $tag['description'], '" title="', $tag['description'], '" style="background-image: url(', $settings['images_url'], '/bbc/bbc_bg.gif); margin: 1px 2px 1px 1px;" /></a>';
			}
			// I guess it's a divider...
			elseif ($found_button)
			{
				echo '<img src="', $settings['images_url'], '/bbc/divider.gif" alt="|" style="margin: 0 3px 0 3px;" />';
				$found_button = false;
			}
		}
	}
	echo '</div>
	</div>';

}

function shout_smiley_code()
{
	global $context, $settings, $user_info, $txt, $modSettings, $db_prefix;
  
	// Initialize smiley array...
	$context['tp_smileys'] = array(
		'postform' => array(),
		'popup' => array(),
	);

		$context['tp_smileys']['postform'][] = array(
			'smileys' => array(
				array('code' => ':)', 'filename' => 'smiley.gif', 'description' => $txt[287]),
				array('code' => ';)', 'filename' => 'wink.gif', 'description' => $txt[292]),
				array('code' => ':D', 'filename' => 'cheesy.gif', 'description' => $txt[289]),
				array('code' => ';D', 'filename' => 'grin.gif', 'description' => $txt[293]),
				array('code' => '>:(', 'filename' => 'angry.gif', 'description' => $txt[288]),
				array('code' => ':(', 'filename' => 'sad.gif', 'description' => $txt[291]),
				array('code' => ':o', 'filename' => 'shocked.gif', 'description' => $txt[294]),
				array('code' => '8)', 'filename' => 'cool.gif', 'description' => $txt[295]),
				array('code' => '???', 'filename' => 'huh.gif', 'description' => $txt[296]),
				array('code' => '::)', 'filename' => 'rolleyes.gif', 'description' => $txt[450]),
				array('code' => ':P', 'filename' => 'tongue.gif', 'description' => $txt[451]),
				array('code' => ':-[', 'filename' => 'embarrassed.gif', 'description' => $txt[526]),
				array('code' => ':-X', 'filename' => 'lipsrsealed.gif', 'description' => $txt[527]),
				array('code' => ':-\\', 'filename' => 'undecided.gif', 'description' => $txt[528]),
				array('code' => ':-*', 'filename' => 'kiss.gif', 'description' => $txt[529]),
				array('code' => ':\'(', 'filename' => 'cry.gif', 'description' => $txt[530])
			),
			'last' => true,
		);

	// Clean house... add slashes to the code for javascript.
	foreach (array_keys($context['tp_smileys']) as $location)
	{
		foreach ($context['tp_smileys'][$location] as $j => $row)
		{
			$n = count($context['tp_smileys'][$location][$j]['smileys']);
			for ($i = 0; $i < $n; $i++)
			{
				$context['tp_smileys'][$location][$j]['smileys'][$i]['code'] = addslashes($context['tp_smileys'][$location][$j]['smileys'][$i]['code']);
				$context['tp_smileys'][$location][$j]['smileys'][$i]['js_description'] = addslashes($context['tp_smileys'][$location][$j]['smileys'][$i]['description']);
			}

			$context['tp_smileys'][$location][$j]['smileys'][$n - 1]['last'] = true;
		}
		if (!empty($context['tp_smileys'][$location]))
			$context['tp_smileys'][$location][count($context['tp_smileys'][$location]) - 1]['last'] = true;
	}
	$settings['smileys_url'] = $modSettings['smileys_url'] . '/' . $user_info['smiley_set'];
}

function print_shout_smileys($collapse=true) 
{
  global $context, $txt, $settings, $options;
  
  loadLanguage('Post');
  
if($collapse)
	echo '
<a href="#" onclick="expandHeaderSmiley(!current_header_smiley); return false;"><img id="expand_smiley" src="', $settings['tp_images_url'], '/', empty($options['expand_header_smiley']) ? 'TPexpand.gif' : 'TPcollapse.gif', '" alt="*" title="', $txt['upshrink_description'], '" style="margin-right: 5px; position: relative; top: 2px;" align="left" /></a>
<div id="shoutbox_smiley">
		';
else
	echo '
	<div>';

	// Now start printing all of the smileys.
	if (!empty($context['tp_smileys']['postform']))
	{
		// counter...
		$sm_counter=0;
		// Show each row of smileys ;).
		foreach ($context['tp_smileys']['postform'] as $smiley_row)
		{
			foreach ($smiley_row['smileys'] as $smiley)
			{
				if($sm_counter==5 && $collapse)
					echo '
			<div id="expandHeaderSmiley"', empty($options['expand_header_smiley']) ? ' style="display: none;"' : 'style="display: inline;"' , '>';

				echo '
					<a href="javascript:void(0);" onclick="replaceText(\' ', $smiley['code'], '\', document.forms.', $context['tp_shoutbox_form'], '.', $context['tp_shout_post_box_name'], '); return false;"><img src="', $settings['smileys_url'], '/', $smiley['filename'], '" align="bottom" alt="', $smiley['description'], '" title="', $smiley['description'], '" /></a>';
				$sm_counter++;
			}
			// If this isn't the last row, show a break.
			if (empty($smiley_row['last']))
				echo '<br />';
		}
	}

	echo '
		</div>
	</div>';
}



// show a dedicated frontpage
function tpshout_frontpage()
{
    global $maintenance, $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $ID_MEMBER, $boarddir, $boardurl, $options, $sourcedir;

	$tp_prefix = $settings['tp_prefix'];
	loadtemplate('TPShout');
	loadlanguage('TPShout');

//	$context['sub_template'] = 'tpshout_frontpage';
	$context['page_title'] = 'Shoutbox frontpage';

	$context['sub_template'] = 'tpshout_bigscreen';
	$context['page_title'] = 'Shoutbox';
	$context['TPortal']['rendershouts'] = tpshout_fetch(false, '30');
	
}
function shoutbox_update()
{
	global $settings;
	
	$tp_prefix = $settings['tp_prefix'];

	$settings_array=array(
	 'shoutbox_height' => '120' ,
	 'shoutbox_limit' => '5' ,
	 'guest_shout' => '0' ,
	 'shoutbox_usescroll' => '0',
	 'shoutbox_scrollduration' => '2000',
	 'shoutbox_scrolldelay' => '1000',
	 'shoutbox_scrolldirection' => 'vert',
	 'shoutbox_scrolleasing' => 'Cubic',
	 'show_shoutbox_archive' => '50',
	 'shoutbox_version' => '101',
	 'show_shoutbox_smile' => '1',
	 'show_shoutbox_icons' => '1',
		'profile_shouts_hide' => '0',
 	);
	$updates=0;
	foreach($settings_array as $what => $val){
		$sjekk=tp_query("SELECT * FROM " . $tp_prefix . "settings WHERE name='$what'", __FILE__, __LINE__);
		if(tpdb_num_rows($sjekk)<1){
			tp_query("INSERT INTO " . $tp_prefix . "settings (name,value) VALUES ( '$what', '$val')", __FILE__, __LINE__);
		}
		else
		{
			if($what == 'shoutbox_version')
				tp_query("UPDATE " . $tp_prefix . "settings SET value='$val' WHERE name='$what'", __FILE__, __LINE__);
				
			tpdb_free_result($sjekk);
		}
	}
	// update profile section
	tp_query("UPDATE " . $tp_prefix . "modules SET profile='tpshout_profile' WHERE modulename LIKE 'TPshout'", __FILE__, __LINE__);

	return;
}

?>