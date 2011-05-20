<?php
/****************************************************************************
* TPdlmanager.php															*
*****************************************************************************
* TP version: 1.0 RC1														*
* Software Version:				SMF 1.1.x									*
* Founder:						Bloc (http://www.blocweb.net)				*
* Developer:					IchBin (ichbin@ichbin.us)					*
* Copyright 2005-2011 by:     	The TinyPortal Team							*
* Support, News, Updates at:  	http://www.tinyportal.net					*
****************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

function TPdlmanager_init()
{
	global $context, $settings, $sourcedir;

	// load the needed strings
	loadlanguage('TPdlmanager');

	require_once($sourcedir . '/TPcommon.php');
	// get subaction
	if(isset($context['TPortal']['dlsub'])){

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum']=true;

	// see if its admin section
	if(substr($context['TPortal']['dlsub'],0,5)=='admin' || $context['TPortal']['dlsub']=='submission')
		TPortalDLAdmin();
	elseif(substr($context['TPortal']['dlsub'],0,8)=='useredit')
		TPortalDLUser(substr($context['TPortal']['dlsub'],8));
	else
		TPortalDLManager();
	}
}

// TinyPortal DLmanager
function TPortalDLManager()
{
	global $txt, $scripturl, $db_prefix, $ID_MEMBER, $user_info, $sourcedir, $boarddir, $boardurl;
	global $modSettings, $context, $settings, $func;

   // assume its the frontpage initially
   $context['TPortal']['dlaction']='main';

   $tp_prefix=$settings['tp_prefix'];

	// is even the manager active?
	if(!$context['TPortal']['show_download'])
		fatal_error($txt['tp-dlmanageroff']);

	$context['TPortal']['upshrinkpanel']='';
	
	// add visual options to thsi section
	$context['TPortal']['dl_visual']=array();
	$dl_visual=explode(',',$context['TPortal']['dl_visual_options']);
	$dv=array('left','right','center','lower','top','bottom');
	foreach($dv as $v => $val){
		if($context['TPortal'][$val.'panel']==1){
			if(in_array($val,$dl_visual))
				$context['TPortal'][$val.'panel']='1';
			else
				$context['TPortal'][$val.'panel']='0';
		}
		$context['TPortal']['dl_visual'][$val]=true;
	}

	if(in_array('top',$dl_visual))
		$context['TPortal']['showtop']='1';
	else
		$context['TPortal']['showtop']='0';

	// check that you can upload at all
	if(allowedTo('tp_dlupload'))
	    $context['TPortal']['can_upload']=true;
	else
	    $context['TPortal']['can_upload']=false;

	// fetch all files from tp-downloads
	if(isset($_GET['ftp']) && allowedTo('tp_dlmanager'))
		TP_dlftpfiles();

	// any uploads being sent?
	$context['TPortal']['uploads']=array();
	if(isset($_FILES['tp-dluploadfile']['tmp_name']) || isset($_POST['tp-dluploadnot']))
	{
		// skip the uplaod checks etc . if just an empty item
		if(!isset($_POST['tp-dluploadnot']))
		{
			// check if uploaded quick-list picture 
			if(isset($_FILES['qup_tp_dluploadtext']) && file_exists($_FILES['qup_tp_dluploadtext']['tmp_name']))
			{
				$item_id = isset($_GET['dl']) ? $_GET['dl'] : 'upload';
				$name = TPuploadpicture('qup_tp_dluploadtext', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
				redirectexit('action=tpmod;dl='. $item_id);
			}
			
			// check that nothing happended
			if(!file_exists($_FILES['tp-dluploadfile']['tmp_name']) || !is_uploaded_file($_FILES['tp-dluploadfile']['tmp_name']))
				fatal_error($txt['tp-dluploadfailure']);

			// first, can we upload at all?
			if(!$context['TPortal']['can_upload']){
				unlink($_FILES['tp-dluploadfile']['tmp_name']);
				fatal_error($txt['tp-dluploadnotallowed']);
			}
		}
		// a file it is
		$title= isset($_POST['tp-dluploadtitle']) ? strip_tags($_POST['tp-dluploadtitle']) : '-no title-';
		if($title=='')
			$title='-no title-';
		$text= isset($_POST['tp_dluploadtext']) ? $_POST['tp_dluploadtext'] : '';
		$category = isset($_POST['tp-dluploadcat']) ? $_POST['tp-dluploadcat'] : '0';
		// a screenshot?
		if(file_exists($_FILES['tp_dluploadpic']['tmp_name']) || is_uploaded_file($_FILES['tp_dluploadpic']['tmp_name']))
			$shot=true;
		else
			$shot=false;

		$icon= !empty($_POST['tp_dluploadicon']) ? $boardurl.'/tp-downloads/icons/'.$_POST['tp_dluploadicon'] : '';

		if(!isset($_POST['tp-dluploadnot'])){
			// process the file
			$filename=$_FILES['tp-dluploadfile']['name'];
			$name = strtr($filename, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
			$name = strtr($name, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
			$name = preg_replace(array('/\s/', '/[^\w_\.\-]/'), array('_', ''), $name);
		}
		else
			$name='- empty item -';

	    if(isset($_POST['tp-dlupload_ftpstray']))
    		$name='- empty item - ftp';

		$status = 'normal';

		if(!isset($_POST['tp-dluploadnot'])){
			// check the size
			$dlfilesize = filesize($_FILES['tp-dluploadfile']['tmp_name']);
			if($dlfilesize>(1000*$context['TPortal']['dl_max_upload_size'])){
				$status='maxsize';
				unlink($_FILES['tp-dluploadfile']['tmp_name']);
				$error = $txt['tp-dlmaxerror'].' '.($context['TPortal']['dl_max_upload_size']).' Kb<br /><br />'.$txt['tp-dlmaxerror2'].': '. ceil($dlfilesize/1000) .' Kb';
				fatal_error($error);
			}
		}
		else
			$dlfilesize=0;

		if(!isset($_POST['tp-dluploadnot'])){
			// check the extension
			$allowed=explode(',',$context['TPortal']['dl_allowed_types']);
			$match=false;
			foreach($allowed as $extension => $value)
			{
				$ext='.'.$value;
				$extlen=strlen($ext);
				if(substr($name, strlen($name)-$extlen, $extlen)==$ext)
					$match=true;
			}
			if(!$match){
				$status='wrongtype';
				unlink($_FILES['tp-dluploadfile']['tmp_name']);
				$error = $txt['tp-dlexterror'].':<b> <br />'.$context['TPortal']['dl_allowed_types'].'</b><br /><br />'.$txt['tp-dlexterror2'].': <b>'.$name.'</b>';
				fatal_error($error);
			}
		}

		// ok, go ahead
		if($status=='normal'){

			if(!isset($_POST['tp-dluploadnot'])){
				// check that no other file exists with same name
				if(file_exists($boarddir.'/tp-downloads/'.$name))
					$name= time().$name;

				$success=move_uploaded_file($_FILES['tp-dluploadfile']['tmp_name'],$boarddir.'/tp-downloads/'.$name);
			}

			if($shot)
			{
				$sfile = 'tp_dluploadpic';
				$uid = $context['user']['id'].'uid';
				$dim = '1800';
				$suf = 'jpg,gif,png';
				$dest = 'tp-images/dlmanager';
				$sname = TPuploadpicture($sfile, $uid, $dim, $suf, $dest);
				$screenshot = $sname;
				tp_createthumb($dest.'/'.$sname ,$context['TPortal']['dl_screenshotsize'][0],$context['TPortal']['dl_screenshotsize'][1], $dest.'/thumb/'.$sname);
				tp_createthumb($dest.'/'.$sname ,$context['TPortal']['dl_screenshotsize'][2],$context['TPortal']['dl_screenshotsize'][3], $dest.'/listing/'.$sname);
				tp_createthumb($dest.'/'.$sname ,$context['TPortal']['dl_screenshotsize'][4],$context['TPortal']['dl_screenshotsize'][5], $dest.'/single/'.$sname);
			}
			else{
				if(isset($_POST['tp_dluploadpic_link']))
					$screenshot=$_POST['tp_dluploadpic_link'];
				else
					$screenshot='';
			}
			// insert it into the database
			$now=time();

			// if all uploads needs to be approved: set category to -category , but not for dl admins
			if($context['TPortal']['dl_approve']=='1' && !allowedTo('tp_dlmanager')){
					$category = $category-$category-$category;
			}

			// get the category access
			$request = tp_query("SELECT access FROM ".$tp_prefix."dlmanager WHERE id = " . $category);
			if(tpdb_num_rows($request)>0)
			{
				$row = tpdb_fetch_assoc($request);
				$acc = $row['access'];
			}
			else
				$acc = '';
			$request =tp_query("INSERT INTO " . $tp_prefix . "dlmanager
						(name, description, icon, category, type, downloads, views, file, created, last_access, filesize, parent, access, link,authorID,screenshot,rating,voters,subitem )
			VALUES ('". $title . "', '". $func['htmlspecialchars']($text,ENT_QUOTES)."', '".$icon."' , ".$category.", 'dlitem', 0, 1, '".$name."', ".$now.", ".$now.", ".$dlfilesize.", 0, '', '', ".$context['user']['id'].", '".$screenshot."','','',0)", __FILE__, __LINE__);
			$newitem = tpdb_insert_id($request);
			
			// record the event
			if(($context['TPortal']['dl_approve']=='1' && allowedTo('tp_dlmanager')) || $context['TPortal']['dl_approve']=='0')
				tp_recordevent($now, $context['user']['id'], 'tp-createdupload', 'action=tpmod;dl=item' . $newitem, 'Uploaded new file.', $acc , $newitem);


			// should we create a topic?
			if(isset($_POST['create_topic']) && (allowedTo('admin_forum') || !empty($context['TPortal']['dl_create_topic'])))
			{
				$sticky=false; $announce=false;
				// sticky and announce?
				if(isset($_POST['create_topic_sticky']))
					$sticky=true;
				if(isset($_POST['create_topic_announce']) && allowedTo('admin_forum'))
					$announce=true;
				if(!empty($_POST['create_topic_board']))
					$brd=$_POST['create_topic_board'];
				if(isset($_POST['create_topic_body']))
					$body=$_POST['create_topic_body'];

				
				$body .= '[hr][b]'.$txt['tp-download'].':[/b][br]'.$scripturl.'?action=tpmod;dl=item'.$newitem;
				// ok, create the topic then
				$top=TP_createtopic($title,$body,'theme',$brd, $sticky ? 1 : 0 , $ID_MEMBER);
				// go to announce screen?
				if($top>0)
				{
					if($announce)
						redirectexit('action=announce;sa=selectgroup;topic=' . $top );
					else
						redirectexit('topic=' . $top );
				
				}
			}
			// put this into submissions - id and type
			if($category < 0)
			{
				$request =tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5 ) VALUES ('$title', '$now', '','dl_not_approved', '' , $newitem)", __FILE__, __LINE__);
				redirectexit('action=tpmod;sub=dlsubmitsuccess');
			}
			else{
				if(!isset($_POST['tp-dluploadnot']))
					redirectexit('action=tpmod;dl=item'.$newitem);
				else
					redirectexit('action=tpmod;dl=adminitem'.$newitem);
			}
		 }
	}

	// ok, on with the show :)
	TP_dluploadcats();
	TP_dlgeticons();
	// showing a category, or even a single item?
	$context['TPortal']['dlaction'] = '';
	if(isset($context['TPortal']['dlsub']))
	{
		// a category?
		if(substr($context['TPortal']['dlsub'],0,3)=='cat')
		{
				$context['TPortal']['dlcat']=substr($context['TPortal']['dlsub'],3);
				// check if its a number
				if(is_numeric($context['TPortal']['dlcat']))
					$context['TPortal']['dlaction']='cat';
				else{
						redirectexit('action=tpmod;dl');
				}
		}
		elseif($context['TPortal']['dlsub']=='tptag')
		{
				if(isset($context['TPortal']['myglobaltags']))
					$context['TPortal']['dlaction']='tptag';
				else{
						redirectexit('action=tpmod;dl');
				}
		}
		elseif(substr($context['TPortal']['dlsub'],0,4)=='item'){
				$context['TPortal']['dlitem']=substr($context['TPortal']['dlsub'],4);
				if(is_numeric($context['TPortal']['dlitem'])){
					$item=$context['TPortal']['dlitem'];
					$context['TPortal']['item']=$item;
					$context['TPortal']['dlaction']='item';
					$request = tp_query("SELECT category,subitem FROM " . $tp_prefix . "dlmanager WHERE id = $item AND type = 'dlitem' LIMIT 1", __FILE__, __LINE__);
					if(tpdb_num_rows($request)>0){
						$row=tpdb_fetch_assoc($request);
						$context['TPortal']['dlcat'] = $row['category'];
						tpdb_free_result($request);
						// check that it is indeed a main item, if not : redirect to the main one.
						if($row['subitem']>0)
							redirectexit('action=tpmod;dl=item'.$row['subitem']);
					}
					else{
						redirectexit('action=tpmod;dl');
					}
				}
				else{
						redirectexit('action=tpmod;dl');
				}
		}
		elseif($context['TPortal']['dlsub']=='stats'){
			$context['TPortal']['dlaction']='stats';
			$context['TPortal']['dlitem']='';
		}
		elseif($context['TPortal']['dlsub']=='search'){
			$context['TPortal']['dlaction']='search';
			$context['TPortal']['dlitem']='';
		}
		elseif($context['TPortal']['dlsub']=='results'){
			$context['TPortal']['dlaction']='results';
			$context['TPortal']['dlitem']='';
		}
		elseif($context['TPortal']['dlsub']=='submission'){
			$context['TPortal']['dlaction']='submission';
			$context['TPortal']['dlitem']='';
		}
		elseif(substr($context['TPortal']['dlsub'],0,3)=='get'){
			$context['TPortal']['dlitem']=substr($context['TPortal']['dlsub'],3);
			if(is_numeric($context['TPortal']['dlitem']))
				$context['TPortal']['dlaction']='get';
			else{
						redirectexit('action=tpmod;dl');
			}
		}
		elseif(substr($context['TPortal']['dlsub'],0,6)=='upload'){
			$context['TPortal']['dlitem']=substr($context['TPortal']['dlsub'],6);
			$context['TPortal']['dlaction']='upload';

			// check your permission for uploading
			isAllowedTo('tp_dlupload');

			TP_dlgeticons();
			
			// allow to attach this to another item
			$context['TPortal']['attachitems']=array();
			if(allowedTo('dlmanager'))
			{
				// get all items for a list
				$itemlist = tp_query("SELECT id, name FROM " . $tp_prefix . "dlmanager WHERE type = 'dlitem' AND subitem=0 ORDER BY name ASC", __FILE__, __LINE__);
				if(tpdb_num_rows($itemlist)>0){
					while($ilist=tpdb_fetch_assoc($itemlist))
					{
						$context['TPortal']['attachitems'][] = array(
							'id' => $ilist['id'],
							'name' => $ilist['name'],
						);
					}
					tpdb_free_result($itemlist);
				}
			}
			else
			{
				// how about attaching to one of your own?
				// get all items for a list
				$itemlist = tp_query("SELECT id,name FROM " . $tp_prefix . "dlmanager WHERE category>0 AND type = 'dlitem' AND subitem=0 AND authorID=$ID_MEMBER ORDER BY name ASC", __FILE__, __LINE__);
				if(isset($itemlist) && tpdb_num_rows($itemlist)>0){
					while($ilist=tpdb_fetch_assoc($itemlist))
					{
						$context['TPortal']['attachitems'][] = array(
							'id' => $ilist['id'],
							'name' => $ilist['name'],
						);
					}
					tpdb_free_result($itemlist);
				}
			}

			$context['TPortal']['boards']=array();
			// fetch all boards
			$request = tp_query("SELECT b.ID_BOARD, b.name FROM " . $db_prefix . "boards as b WHERE $user_info[query_see_board]",__FILE__,__LINE__);
			if (tpdb_num_rows($request)>0)
			{
				while($row=tpdb_fetch_assoc($request))
					$context['TPortal']['boards'][]=array('id' => $row['ID_BOARD'], 'name' => $row['name']);

				tpdb_free_result($request);
			}
		}

		// a category?
		else
		{
			// check its really exists
			$what=$context['TPortal']['dlsub'];
			$request = tp_query("SELECT id FROM " . $tp_prefix . "dlmanager WHERE link='$what' LIMIT 1", __FILE__, __LINE__);
			if(isset($request) && tpdb_num_rows($request)>0)
			{
				$row=tpdb_fetch_assoc($request);
				$context['TPortal']['dlcat'] = $row['id'];
				$context['TPortal']['dlsub'] = 'cat'.$row['id'];
				$context['TPortal']['dlaction']='cat';
				tpdb_free_result($request);
			}
		}
	}
	// add to the linktree
	TPadd_linktree($scripturl.'?action=tpmod;dl=0', $txt['tp-downloads']);

	// set the title
	$context['page_title']=$txt['tp-downloads'];
	$context['TPortal']['dl_title'] = $txt['tp-mainpage'];

    // load the dlmanager frontpage
    if($context['TPortal']['dlaction']=='')
    {
        if($context['TPortal']['dl_wysiwyg'] == 'bbc')
            $context['TPortal']['dl_introtext'] = parse_bbc($context['TPortal']['dl_introtext']);
        else
            $context['TPortal']['dl_introtext'] = html_entity_decode($context['TPortal']['dl_introtext'], ENT_QUOTES, $context['character_set']);

		$context['TPortal']['dlcats'] = array();
		$context['TPortal']['dlcatchilds'] = array();

		// add x most recent and feature the last one
		$context['TPortal']['dl_last_added'] = array();
		$context['TPortal']['dl_most_downloaded'] = array();
		$context['TPortal']['dl_week_downloaded'] = array();

		$mycats=array();
		dl_getcats();
		foreach($context['TPortal']['dl_allowed_cats'] as $ca)
			$mycats[]=$ca['id'];

		// empty?
		if(sizeof($mycats)>0)
		{
			$request = tp_query("SELECT dlm.id, dlm.name, dlm.category, dlm.file, dlm.downloads, dlm.views, dlm.authorID, dlm.created, dlm.screenshot, dlm.filesize,
			dlcat.name AS catname, mem.realName, LEFT(dlm.description,100) as description
			FROM (" . $tp_prefix . "dlmanager AS dlm, " . $db_prefix . "members AS mem)
			LEFT JOIN " . $tp_prefix . "dlmanager AS dlcat ON (dlcat.id=dlm.category)
			WHERE dlm.type = 'dlitem'
			AND dlm.category IN (" . implode(',' , $mycats) . ")
			AND dlm.authorID=mem.ID_MEMBER
			ORDER BY dlm.created DESC LIMIT 6", __FILE__, __LINE__);

			if(tpdb_num_rows($request)>0)
			{
				while ($row = tpdb_fetch_assoc($request))
				{
					$fs='';
					if($context['TPortal']['dl_fileprefix']=='K')
						$fs=ceil($row['filesize']/1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix']=='M')
						$fs=(ceil($row['filesize']/1000)/1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix']=='G')
						$fs=(ceil($row['filesize']/1000000)/1000).' Gb';

					if($context['TPortal']['dl_usescreenshot']==1)
					{
						if(!empty($row['screenshot'])) 
							$ico=$boardurl.'/tp-images/dlmanager/thumb/'.$row['screenshot'];
						else
							$ico='';	
					}
					else
						$ico='';

					$context['TPortal']['dl_last_added'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'category' => $row['category'],
					'description' => $context['TPortal']['dl_wysiwyg']=='bbc' ? parse_bbc(trim(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, $context['character_set'])))) : html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
					'file' => $row['file'],
					'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
					'downloads' => $row['downloads'],
					'views' => $row['views'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
					'authorID' => $row['authorID'],
					'date' => timeformat($row['created']),
					'screenshot' => $ico,
					'catname' => $row['catname'],
					'cathref' => $scripturl.'?action=tpmod;dl=cat'.$row['category'],
					'filesize' => $fs,
					);
				}
				tpdb_free_result($request);
			}
			$request = tp_query("SELECT dlm.id, dlm.name, dlm.category, dlm.file, dlm.downloads, dlm.views, dlm.authorID, dlm.created, dlm.filesize,
			dlcat.name AS catname, mem.realName
			FROM (" . $tp_prefix . "dlmanager AS dlm, " . $db_prefix . "members AS mem)
			LEFT JOIN " . $tp_prefix . "dlmanager AS dlcat ON dlcat.id=dlm.category
			WHERE dlm.type = 'dlitem'
			AND dlm.category IN (" . implode(',' , $mycats) . ")
			AND dlm.authorID=mem.ID_MEMBER
			ORDER BY dlm.downloads DESC LIMIT 10", __FILE__, __LINE__);

			if(tpdb_num_rows($request)>0)
			{
				while ($row = tpdb_fetch_assoc($request))
				{
					$fs='';
					if($context['TPortal']['dl_fileprefix']=='K')
						$fs=ceil($row['filesize']/1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix']=='M')
						$fs=(ceil($row['filesize']/1000)/1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix']=='G')
						$fs=(ceil($row['filesize']/1000000)/1000).' Gb';

					$context['TPortal']['dl_most_downloaded'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'category' => $row['category'],
					'file' => $row['file'],
					'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
					'downloads' => $row['downloads'],
					'views' => $row['views'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
					'authorID' => $row['authorID'],
					'date' => timeformat($row['created']),
					'catname' => $row['catname'],
					'cathref' => $scripturl.'?action=tpmod;dl=cat'.$row['category'],
					'filesize' => $fs,
					);
				}
				tpdb_free_result($request);
			}
			// fetch most downloaded this week
			$now=time();
			$week=date("W",$now);
			$year=date("Y",$now);
			$request = tp_query("SELECT dlm.id, dlm.name, dlm.category, dlm.file, data.downloads, dlm.views, dlm.authorID, dlm.created, dlm.screenshot, dlm.filesize,
			dlcat.name AS catname, mem.realName
			FROM (" . $tp_prefix . "dlmanager AS dlm, " . $tp_prefix . "dldata AS data, " . $db_prefix . "members AS mem)
			LEFT JOIN " . $tp_prefix . "dlmanager AS dlcat ON dlcat.id=dlm.category
			WHERE dlm.type = 'dlitem'
			AND dlm.category IN (" . implode(',' , $mycats) . ")
			AND data.item=dlm.id
			AND data.year=$year
			AND data.week = $week
			AND dlm.authorID=mem.ID_MEMBER
			ORDER BY data.downloads DESC LIMIT 10", __FILE__, __LINE__);

			if(tpdb_num_rows($request)>0)
			{
				while ($row = tpdb_fetch_assoc($request))
				{
					if($context['TPortal']['dl_usescreenshot']==1)
					{
						if(!empty($row['screenshot'])) 
							$ico=$boardurl.'/tp-images/dlmanager/thumb/'.$row['screenshot'];
						else
							$ico='';	
					}
					else
						$ico='';

					$fs='';
					if($context['TPortal']['dl_fileprefix']=='K')
						$fs=ceil($row['filesize']/1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix']=='M')
						$fs=(ceil($row['filesize']/1000)/1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix']=='G')
						$fs=(ceil($row['filesize']/1000000)/1000).' Gb';

					$context['TPortal']['dl_week_downloaded'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'category' => $row['category'],
					'file' => $row['file'],
					'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
					'downloads' => $row['downloads'],
					'views' => $row['views'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
					'authorID' => $row['authorID'],
					'date' => timeformat($row['created']),
					'screenshot' => $ico,
					'catname' => $row['catname'],
					'cathref' => $scripturl.'?action=tpmod;dl=cat'.$row['category'],
					'filesize' => $fs,
					);
				}
				tpdb_free_result($request);
			}

		
		}
		// fetch the categories, the number of files
		$request = tp_query("
		SELECT	a.access AS access,a.icon AS icon,a.link AS shortname,a.description AS description,
		a.name AS name,a.id AS id,a.parent AS parent,
	  	if (a.id = b.category, count(*), 0) AS files,b.category AS subchild
		FROM (" . $tp_prefix . "dlmanager AS a)
		LEFT JOIN " . $tp_prefix . "dlmanager AS b ON (a.id = b.category)
		WHERE a.type = 'dlcat'
	  	GROUP BY a.id
		ORDER BY a.downloads ASC", __FILE__, __LINE__);

		$fetched_cats=array();
		if(tpdb_num_rows($request)>0)
		{
			while ($row = tpdb_fetch_assoc($request))
			{
				$show = get_perm($row['access'], 'tp_dlmanager');
				if($show && $row['parent']==0){
					$context['TPortal']['dlcats'][$row['id']] = array(
							'id' => $row['id'],
							'name' => $row['name'],
							'parent' => $row['parent'],
						'description' => $context['TPortal']['dl_wysiwyg']=='bbc' ? parse_bbc(trim(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, $context['character_set'])))) : html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
							'access' => $row['access'],
							'icon' => $row['icon'],
							'href' => !empty($row['shortname']) ? $scripturl.'?action=tpmod;dl='.$row['shortname'] : $scripturl.'?action=tpmod;dl=cat'.$row['id'],
							'shortname' => !empty($row['shortname']) ? $row['shortname'] : $row['id'],
							'files' => $row['files'],
							);
					$fetched_cats[]=$row['id'];
				}
				elseif($show && $row['parent']>0){
						$context['TPortal']['dlcatchilds'][] = array(
							'id' => $row['id'],
							'name' => $row['name'],
							'parent' => $row['parent'],
							'href' => $scripturl.'?action=tpmod;dl=cat'.$row['id'],
							'files' => $row['files'],
							);
				}
			}
			tpdb_free_result($request);
		}
		// add filecount to parent
		foreach($context['TPortal']['dlcatchilds'] as $child){
			if(isset($context['TPortal']['dlcats'][$child['parent']]) && $context['TPortal']['dlcats'][$child['parent']]['parent']==0)
				$context['TPortal']['dlcats'][$child['parent']]['files'] = $context['TPortal']['dlcats'][$child['parent']]['files'] + $child['files'];
		}
		// do we need the featured one?
		if(!empty($context['TPortal']['dl_featured']))
		{
				// fetch the item data
				$item =	$context['TPortal']['dl_featured'];
				$request = tp_query("SELECT dl.* , m.realName
							FROM (" . $tp_prefix . "dlmanager AS dl, " . $db_prefix . "members AS m)
							WHERE dl.type = 'dlitem'
							AND dl.id=$item
							AND dl.authorID=m.ID_MEMBER
							LIMIT 1", __FILE__, __LINE__);
				if(tpdb_num_rows($request)>0)
				{
					$row = tpdb_fetch_assoc($request);
					if($context['TPortal']['dl_fileprefix']=='K')
						$fs=ceil($row['filesize']/1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix']=='M')
						$fs=(ceil($row['filesize']/1000)/1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix']=='G')
						$fs=(ceil($row['filesize']/1000000)/1000).' Gb';

					$rat=array();
					$rating_votes=0;
					$rat=explode(",",$row['rating']);
					$rating_votes=count($rat);
					if($row['rating']=='')
						$rating_votes=0;

					$total=0;
					foreach($rat as $mm => $mval)
						$total=$total+$mval;

					if($rating_votes>0 && $total>0)
						$rating_average=floor($total/$rating_votes);
					else
						$rating_average=0;

					   $decideshot=!empty($row['screenshot']) ? $boardurl. '/' . $row['screenshot'] : ''; 
						// does it exist? 
						if(file_exists($boarddir . '/tp-images/dlmanager/listing/' . $row['screenshot']) && !empty($row['screenshot']))
							$decideshot=$boardurl. '/tp-images/dlmanager/listing/' . $row['screenshot']; 

						if($context['user']['is_logged'])
							$can_rate = in_array($context['user']['id'], explode(",",$row['voters'])) ? false : true;
						else
							$can_rate = false;
							
						$context['TPortal']['featured'] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'description' => $context['TPortal']['dl_wysiwyg']=='bbc' ? parse_bbc(trim(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, $context['character_set'])))) : html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
						'category' => $row['category'],
						'file' => $row['file'],
						'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
						'downloads' => $row['downloads'],
						'views' => $row['views'],
						'link' => $row['link'],
						'date_last' => $row['last_access'],
						'author' => $row['realName'],
						'authorID' => $row['authorID'],
						'screenshot' => $row['screenshot'],
						'sshot' => $decideshot,
						'icon' => $row['icon'],
						'created' => $row['created'],
						'filesize' => $fs,
						'subitem' => isset($fdata) ? $fdata : '',
						'rating_votes' => $rating_votes,
						'rating_average' => $rating_average,
						'can_rate' => $can_rate,
						'global_tag' => $row['global_tag'],
					);
				}
				tpdb_free_result($request);
	
		}
		$context['TPortal']['dlheader'] = $txt['tp-downloads'];
	}
	// load a category
	elseif($context['TPortal']['dlaction']=='cat')
	{
		// check if sorting is specified
		if(isset($_GET['dlsort']) && in_array($_GET['dlsort'], array('id', 'name','last_access','created','downloads','authorID')))
			$context['TPortal']['dlsort'] = $dlsort = $_GET['dlsort'];
		else
			$context['TPortal']['dlsort'] = $dlsort = 'id';

		
		if(isset($_GET['asc']))
			$context['TPortal']['dlsort_way'] = $dlsort_way = 'asc';
		else
			$context['TPortal']['dlsort_way'] = $dlsort_way = 'desc';

		$currentcat = $context['TPortal']['dlcat'];
		//fetch all  categories and its childs
		$context['TPortal']['dlcats'] = array();
		$context['TPortal']['dlcatchilds'] = array();
		$context['TPortal']['dl_week_downloaded'] = array();

		// fetch most downloaded this week
		$now=time();
		$week=date("W",$now);
		$year=date("Y",$now);
		$request = tp_query("SELECT dlm.id, dlm.name, dlm.category, dlm.file, data.downloads, dlm.views, dlm.authorID, dlm.created, dlm.screenshot, dlm.filesize,
		dlcat.name AS catname, mem.realName
		FROM (" . $tp_prefix . "dlmanager AS dlm, " . $tp_prefix . "dldata AS data, " . $db_prefix . "members AS mem)
		LEFT JOIN " . $tp_prefix . "dlmanager AS dlcat ON dlcat.id=dlm.category
		WHERE dlm.type = 'dlitem'
		AND (dlm.category = $currentcat OR dlm.parent=$currentcat)
		AND data.item=dlm.id
		AND data.year=$year
		AND data.week = $week
		AND dlm.authorID=mem.ID_MEMBER
		ORDER BY data.downloads DESC LIMIT 10", __FILE__, __LINE__);

		if(tpdb_num_rows($request)>0)
		{
			while ($row = tpdb_fetch_assoc($request))
			{
				$fs='';
				if($context['TPortal']['dl_fileprefix']=='K')
					$fs=ceil($row['filesize']/1000).' Kb';
				elseif($context['TPortal']['dl_fileprefix']=='M')
					$fs=(ceil($row['filesize']/1000)/1000).' Mb';
				elseif($context['TPortal']['dl_fileprefix']=='G')
					$fs=(ceil($row['filesize']/1000000)/1000).' Gb';

				$context['TPortal']['dl_week_downloaded'][] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'category' => $row['category'],
				'file' => $row['file'],
				'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
				'downloads' => $row['downloads'],
				'views' => $row['views'],
				'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
				'authorID' => $row['authorID'],
				'date' => timeformat($row['created']),
				'screenshot' => !empty($row['screenshot']) ? $row['screenshot'] : '' ,
				'catname' => $row['catname'],
				'cathref' => $scripturl.'?action=tpmod;dl=cat'.$row['category'],
				'filesize' => $fs,
				);
			}
			tpdb_free_result($request);
		}

		// add x most recent and feature the last one
		$context['TPortal']['dl_last_added'] = dl_recentitems(5,'date','array',$context['TPortal']['dlcat']);
		$context['TPortal']['dl_most_downloaded'] = dl_recentitems(5,'downloads','array',$context['TPortal']['dlcat']);

		// do we have access then?
		$request = tp_query("SELECT parent,access,name FROM " . $tp_prefix . "dlmanager WHERE id = $currentcat", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			while ($row = tpdb_fetch_assoc($request))
			{
				$currentname=$row['name'];
				$context['page_title'] = $row['name'];
				$catparent = $row['parent'];
				if(!get_perm($row['access'], 'tp_dlmanager'))
				{
					// if a guest, make them login/register
					if($context['user']['is_guest'])
					{
						$context['description']=$txt['tp-needtoregister'];
						loadtemplate('Login');
						$context['sub_template']='login';
					}
					else
						redirectexit('action=tpmod;dl');
				}
			}
	        tpdb_free_result($request);
	    }
	    // nothing there, le them know
	    else
			redirectexit('action=tpmod;dl');

		$request = tp_query("
		SELECT
		a.access AS access,
		a.icon AS icon,
		a.link AS shortname,
		a.description AS description,
		a.name AS name,
		a.id AS id,
	  	a.parent AS parent,
	  	if (a.id = b.category, count(*), 0) AS files,
	  	b.category AS subchild
		FROM (" . $tp_prefix . "dlmanager AS a)
		LEFT JOIN " . $tp_prefix . "dlmanager AS b
	  		ON a.id = b.category
		WHERE a.type = 'dlcat'
		AND a.parent=$currentcat
	  	GROUP BY a.id
	  	ORDER BY a.downloads ASC", __FILE__, __LINE__);
		$context['TPortal']['dlchildren'] = array();
		if(tpdb_num_rows($request)>0)
		{
			while ($row = tpdb_fetch_assoc($request))
			{
				$show = get_perm($row['access'], 'tp_dlmanager');
				if($show && $row['parent']==$currentcat){
					$context['TPortal']['dlcats'][] = array(
							'id' => $row['id'],
							'name' => $row['name'],
							'parent' => $row['parent'],
						'description' => $context['TPortal']['dl_wysiwyg']=='bbc' ? parse_bbc(trim(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, $context['character_set'])))) : html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
							'access' => $row['access'],
							'icon' => $row['icon'],
							'href' => !empty($row['shortname']) ? $scripturl.'?action=tpmod;dl='.$row['shortname'] : $scripturl.'?action=tpmod;dl=cat'.$row['id'],
							'shortname' => !empty($row['shortname']) ? $row['shortname'] : $row['id'],
							'files' => $row['files'],
							);
				}
				elseif($show && $row['parent']!=$currentcat){
						$context['TPortal']['dlchildren'][]=$row['id'];
						$context['TPortal']['dlcatchilds'][] = array(
							'id' => $row['id'],
							'name' => $row['name'],
							'parent' => $row['parent'],
							'href' => !empty($row['shortname']) ? $scripturl.'?action=tpmod;dl='.$row['shortname'] : $scripturl.'?action=tpmod;dl=cat'.$row['id'],
							'shortname' => !empty($row['shortname']) ? $row['shortname'] : $row['id'],
							'files' => $row['files'],
							);
				}
			}
			tpdb_free_result($request);
		}

		// get any items in the category
			$context['TPortal']['dlitem'] = array();
			$start=0;
			if(isset($_GET['p']) && !is_numeric($_GET['p']))
				fatal_error('Attempt to specify a non-integer value!');
			elseif(isset($_GET['p']) && is_numeric($_GET['p']))
				$start=$_GET['p'];

			// get total count
			$request = tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "dlmanager WHERE type = 'dlitem' AND category=$currentcat AND subitem=0", __FILE__, __LINE__);
			$row=tpdb_fetch_row($request);
			$rows2=$row[0];

			$request = tp_query("SELECT dl.id, LEFT(dl.description, 200) as ingress,dl.name, dl.category, dl.file, 
			dl.downloads, dl.views, dl.link, dl.created, dl.last_access, 
			dl.authorID, dl.icon, dl.screenshot, dl.filesize, mem.realName 
			FROM " . $tp_prefix . "dlmanager as dl
			LEFT JOIN " . $db_prefix . "members as mem ON (dl.authorID=mem.ID_MEMBER)
			WHERE dl.type = 'dlitem' 
			AND dl.category=$currentcat 
			AND dl.subitem=0 
			ORDER BY dl.$dlsort $dlsort_way LIMIT $start,10 ", __FILE__, __LINE__);

			if(tpdb_num_rows($request)>0)
			{

				// set up the sorting links
				$context['TPortal']['sortlinks']='<span class="smalltext">' . $txt['tp-sortby'] . ': ';
				$what=array('id','name','downloads','last_access', 'created', 'authorID');
				foreach($what as $v)
				{
					if($context['TPortal']['dlsort']==$v)
					{
						$context['TPortal']['sortlinks'] .= '<a href="'.$scripturl.'?action=tpmod;dl=cat'.$currentcat.';dlsort='.$v.';';
						if($context['TPortal']['dlsort_way']=='asc')
							$context['TPortal']['sortlinks'] .= 'desc;p='.$start.'">'.$txt['tp-'.$v].' <img src="' .$settings['tp_images_url']. '/TPsort_up.gif" alt="" /></a> &nbsp;|&nbsp; ';
						else
							$context['TPortal']['sortlinks'] .= 'asc;p='.$start.'">'.$txt['tp-'.$v].' <img src="' .$settings['tp_images_url']. '/TPsort_down.gif" alt="" /></a> &nbsp;|&nbsp; ';
					}
					else
						$context['TPortal']['sortlinks'] .= '<a href="'.$scripturl.'?action=tpmod;dl=cat'.$currentcat.';dlsort='.$v.';desc;p='.$start.'">'.$txt['tp-'.$v].'</a> &nbsp;|&nbsp; ';
				}
				$context['TPortal']['sortlinks']=substr($context['TPortal']['sortlinks'],0,strlen($context['TPortal']['sortlinks'])-15);
				$context['TPortal']['sortlinks'] .= '</span>';

				while ($row = tpdb_fetch_assoc($request))
				{
					if(substr($row['screenshot'],0,16)== 'tp-images/Image/')
							$decideshot=$boardurl. '/' . $row['screenshot']; 
					else
						$decideshot=$boardurl. '/tp-images/dlmanager/thumb/' . $row['screenshot']; 

					if($context['TPortal']['dl_fileprefix']=='K')
						$fs=ceil($row['filesize']/1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix']=='M')
						$fs=(ceil($row['filesize']/1000)/1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix']=='G')
						$fs=(ceil($row['filesize']/1000000)/1000).' Gb';
					
					if($context['TPortal']['dl_usescreenshot']==1)
					{
						if(!empty($row['screenshot'])) 
							$ico=$boardurl.'/tp-images/dlmanager/thumb/'.$row['screenshot'];
						else
							$ico='';	
					}
					else
						$ico=$row['icon'];

					
					$context['TPortal']['dlitem'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'category' => $row['category'],
						'file' => $row['file'],
						'description' => '',
						'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
						'dlhref' => $scripturl.'?action=tpmod;dl=get'.$row['id'],
						'downloads' => $row['downloads'],
						'views' => $row['views'],
						'link' => $row['link'],
						'created' => $row['created'],
						'date_last' => $row['last_access'],
						'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
						'authorID' => $row['authorID'],
						'screenshot' => $row['screenshot'],
						'sshot' => $decideshot,
						'icon' => $ico,
						'date' => $row['created'],
						'filesize' => $fs,
						'ingress' => $context['TPortal']['dl_wysiwyg']=='bbc' ? parse_bbc(trim(strip_tags($row['ingress']))) : html_entity_decode($row['ingress'], ENT_QUOTES, $context['character_set']),
					);
				}
				tpdb_free_result($request);
			}
			if(isset($context['TPortal']['mystart']))
				$mystart=$context['TPortal']['mystart'];

			$currsorting='';
			if(!empty($dlsort))
				$currsorting .= ';dlsort='.$dlsort;
			if(!empty($dlsort_way))
				$currsorting .= ';'.$dlsort_way;

			// construct a pageindex
			$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpmod;dl=cat'.$currentcat.$currsorting, $mystart , $rows2, 10);

		// check backwards for parents
		$done=0;
		$context['TPortal']['parents']=array();
		while($catparent>0 || $done<2){
			if(!empty($context['TPortal']['cats'][$catparent])){
				$context['TPortal']['parents'][] = array(
					'id' => $catparent,
					'name' => $context['TPortal']['cats'][$catparent]['name'],
					'parent' => $context['TPortal']['cats'][$catparent]['parent']
					);
				$catparent=$context['TPortal']['cats'][$catparent]['parent'];
			}
			else{
				$catparent=0;
			}
			if($catparent==0)
				$done++;
		}

		// make the linktree
		if(sizeof($context['TPortal']['parents'])>0){
			$parts=array_reverse($context['TPortal']['parents']);
			// add to the linktree
			foreach($parts as $par){
				TPadd_linktree($scripturl.'?action=tpmod;dl=cat'.$par['id'] , $par['name']);
			}
		}
		// add to the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=cat'.$currentcat , $currentname);
		$context['TPortal']['dlheader'] = $currentname;

	}
	// tptags
	elseif($context['TPortal']['dlaction']=='tptag')
	{
		$context['TPortal']['dlsort'] = $dlsort = 'id';
		$context['TPortal']['dlsort_way'] = $dlsort_way = 'desc';

		// get any items in the category
			$context['TPortal']['dlitem'] = array();
			$start=0;
			if(isset($_GET['p']) && !is_numeric($_GET['p']))
				fatal_error('Attempt to specify a non-integer value!');
			elseif(isset($_GET['p']) && is_numeric($_GET['p']))
				$start=$_GET['p'];

			if(is_array($context['TPortal']['myglobaltags']))
				$tagquery = '(FIND_IN_SET(' . implode(', global_tag) OR FIND_IN_SET(', $context['TPortal']['myglobaltags']) . ', global_tag))';
			else
				$tagquery = 'global_tag LIKE '. $context['TPortal']['myglobaltags'];

			// get total count
			$request = tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "dlmanager WHERE type = 'dlitem' AND $tagquery AND subitem=0 ", __FILE__, __LINE__);
			$row=tpdb_fetch_row($request);
			$rows2=$row[0];

			$request = tp_query("SELECT id, name, category, file, downloads, views, link, created, last_access, authorID, icon, screenshot, filesize, global_tag FROM " . $tp_prefix . "dlmanager WHERE type = 'dlitem' AND $tagquery AND subitem=0 LIMIT $start,10 ", __FILE__, __LINE__);

			if(tpdb_num_rows($request)>0)
			{

				if(substr($row['screenshot'],0,16)== 'tp-images/Image/')
						$decideshot=$boardurl. '/' . $row['screenshot']; 
				else
					$decideshot=$boardurl. '/tp-images/dlmanager/thumb/' . $row['screenshot']; 

				// set up the sorting links
				$context['TPortal']['sortlinks']='';

				while ($row = tpdb_fetch_assoc($request))
				{
					if($context['TPortal']['dl_fileprefix']=='K')
						$fs=ceil($row['filesize']/1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix']=='M')
						$fs=(ceil($row['filesize']/1000)/1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix']=='G')
						$fs=(ceil($row['filesize']/1000000)/1000).' Gb';
					$context['TPortal']['dlitem'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'category' => $row['category'],
						'file' => $row['file'],
						'description' => '',
						'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
						'dlhref' => $scripturl.'?action=tpmod;dl=get'.$row['id'],
						'downloads' => $row['downloads'],
						'views' => $row['views'],
						'link' => $row['link'],
						'created' => $row['created'],
						'date_last' => $row['last_access'],
						'author' => '',
						'authorID' => $row['authorID'],
						'screenshot' => $row['screenshot'],
						'sshot' => $decideshot,
						'icon' => $row['icon'],
						'date' => $row['created'],
						'filesize' => $fs,
					);
				}
				tpdb_free_result($request);
			}
			if(isset($context['TPortal']['mystart']))
				$mystart=$context['TPortal']['mystart'];

			// construct a pageindex
			$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpmod;dl=tptag;tptag='.implode(",",$context['TPortal']['myglobaltags']) , $mystart , $rows2, 10);

		$context['TPortal']['dlheader'] = '';

	}
	elseif($context['TPortal']['dlaction']=='item')
	{
		//fetch the category
		$cat = $context['TPortal']['dlcat'];
		$context['TPortal']['dlcats'] = array();
		$catname='' ; $catdesc='';

		$request = tp_query("SELECT id,name,parent,icon,access,link FROM " . $tp_prefix . "dlmanager WHERE id = $cat AND type = 'dlcat' LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0){
			while ($row = tpdb_fetch_assoc($request))
			{
				$catshortname = $row['link'];
				$catname= $row['name'];
				$catparent = $row['parent'];
				$firstparent = $row['parent'];

				// check if you are allowed in here
				$show = get_perm($row['access'], 'tp_dlmanager');
				if(!$show)
					redirectexit('action=tpmod;dl');
			}
			tpdb_free_result($request);
		}

		// set the title
		$context['TPortal']['dl_title'] = $catname;

		$context['TPortal']['parents']=array();
		// check backwards for parents
		$done=0;
		while($catparent>0 || $done<2){
			if(!empty($context['TPortal']['cats'][$catparent])){
				$context['TPortal']['parents'][] = array(
					'id' => $catparent,
					'shortname' => $catshortname,
					'name' => $context['TPortal']['cats'][$catparent]['name'],
					'parent' => $context['TPortal']['cats'][$catparent]['parent']
					);
				$catparent=$context['TPortal']['cats'][$catparent]['parent'];
			}
			else{
				$catparent=0;
			}
			if($catparent==0)
				$done++;
		}

		// make the linktree
		if(sizeof($context['TPortal']['parents'])>0){
			$parts=array_reverse($context['TPortal']['parents'],TRUE);
			// add to the linktree
			foreach($parts as $parent){
				if(!empty($parent['shortname']))
					TPadd_linktree($scripturl.'?action=tpmod;dl='.$parent['shortname'] , $parent['name']);
				else
					TPadd_linktree($scripturl.'?action=tpmod;dl=cat'.$parent['id'] , $parent['name']);
			}
		}

		// fetch the item data
		$item =	$context['TPortal']['item']=$item;
		$context['TPortal']['dlitem'] = array();
		$request = tp_query("SELECT dl.* , m.realName
							FROM (" . $tp_prefix . "dlmanager AS dl, " . $db_prefix . "members AS m)
							WHERE dl.type = 'dlitem'
							AND dl.id=$item
							AND dl.authorID=m.ID_MEMBER
							LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0){
			$rows=tpdb_num_rows($request);
			while ($row = tpdb_fetch_assoc($request))
				{
					$subitem = $row['id'];
					$fetch = tp_query("SELECT id, name, file, downloads, filesize, created, views
							FROM " . $tp_prefix . "dlmanager
							WHERE type = 'dlitem'
							AND subitem=$subitem
							ORDER BY id DESC", __FILE__, __LINE__);
					
					if(tpdb_num_rows($fetch)>0)
					{
						$fdata=array();
							while($frow = tpdb_fetch_assoc($fetch))
							{
								if($context['TPortal']['dl_fileprefix']=='K')
									$ffs=ceil($row['filesize']/1000).' Kb';
								elseif($context['TPortal']['dl_fileprefix']=='M')
									$ffs=(ceil($row['filesize']/1000)/1000).' Mb';
								elseif($context['TPortal']['dl_fileprefix']=='G')
									$ffs=(ceil($row['filesize']/1000000)/1000).' Gb';
								
								$fdata[] = array(
									'id' => $frow['id'],
									'name' => $frow['name'],
									'file' => $frow['file'],
									'href' => $scripturl.'?action=tpmod;dl=get'.$frow['id'],
									'href2' => $scripturl.'?action=tpmod;dl=item'.$frow['id'],
									'downloads' => $frow['downloads'],
									'views' => $frow['views'],
									'created' => $frow['created'],
									'filesize' => $ffs,
								);
							}
							tpdb_free_result($fetch);
					}
					
					if($context['TPortal']['dl_fileprefix']=='K')
						$fs=ceil($row['filesize']/1000).' Kb';
					elseif($context['TPortal']['dl_fileprefix']=='M')
						$fs=(ceil($row['filesize']/1000)/1000).' Mb';
					elseif($context['TPortal']['dl_fileprefix']=='G')
						$fs=(ceil($row['filesize']/1000000)/1000).' Gb';

					$rat=array();
					$rating_votes=0;
					$rat=explode(",",$row['rating']);
					$rating_votes=count($rat);
					if($row['rating']=='')
						$rating_votes=0;

					$total=0;
					foreach($rat as $mm => $mval)
						$total=$total+$mval;

					if($rating_votes>0 && $total>0)
						$rating_average=floor($total/$rating_votes);
					else
						$rating_average=0;

					   $decideshot=!empty($row['screenshot']) ? $boardurl. '/' . $row['screenshot'] : ''; 
						// does it exist? 
						if(file_exists($boarddir . '/tp-images/dlmanager/listing/' . $row['screenshot']) && !empty($row['screenshot']))
							$decideshot=$boardurl. '/tp-images/dlmanager/listing/' . $row['screenshot']; 

						if($context['user']['is_logged'])
							$can_rate = in_array($context['user']['id'], explode(",",$row['voters'])) ? false : true;
						else
							$can_rate = false;
							
						$context['TPortal']['dlitem'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'description' => $context['TPortal']['dl_wysiwyg']=='bbc' ? parse_bbc(trim(strip_tags(html_entity_decode($row['description'], ENT_QUOTES, $context['character_set'])))) : html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
						'category' => $row['category'],
						'file' => $row['file'],
						'href' => $scripturl.'?action=tpmod;dl=get'.$row['id'],
						'downloads' => $row['downloads'],
						'views' => $row['views'],
						'link' => $row['link'],
						'date_last' => $row['last_access'],
						'author' => $row['realName'],
						'authorID' => $row['authorID'],
						'screenshot' => $row['screenshot'],
						'sshot' => $decideshot,
						'icon' => $row['icon'],
						'created' => $row['created'],
						'filesize' => $fs,
						'subitem' => isset($fdata) ? $fdata : '',
						'rating_votes' => $rating_votes,
						'rating_average' => $rating_average,
						'can_rate' => $can_rate,
						'global_tag' => $row['global_tag'],
					);
					$author = $row['authorID'];
					$parent_cat = $row['category'];
					$views = $row['views'];
					$itemname = $row['name'];
					$itemid = $row['id'];
					$context['page_title'] = $row['name'];
				}
				tpdb_free_result($request);
				TPadd_linktree($scripturl.'?action=tpmod;dl=cat'.$parent_cat , $catname);
				TPadd_linktree($scripturl.'?action=tpmod;dl=item'.$itemid , $itemname);
				// update the views and last access!
				$views++;
				$now=time();
				$year=date("Y",$now);
				$week=date("W",$now);	
				// update weekly views
				$req=tp_query("SELECT id FROM " . $tp_prefix . "dldata WHERE year=$year AND week=$week AND item=$itemid", __FILE__, __LINE__);
				if(tpdb_num_rows($req)>0)
				{
					$row=tpdb_fetch_assoc($req);
					tp_query("UPDATE " . $tp_prefix . "dldata SET views=views+1 WHERE id=$row[id]", __FILE__, __LINE__);
				}
				else
					tp_query("INSERT INTO " . $tp_prefix . "dldata (week,year,views, item) VALUES($week,$year,1,$itemid)", __FILE__, __LINE__);

				tp_query("UPDATE " . $tp_prefix . "dlmanager SET views=$views, last_access=$now WHERE id=$itemid", __FILE__, __LINE__);
				$context['TPortal']['dlheader'] = $itemname;
		}
	}
	elseif($context['TPortal']['dlaction']=='get'){
			TPdownloadme();
	}
	elseif($context['TPortal']['dlaction']=='stats'){
		TPdlstats();
	}
	elseif($context['TPortal']['dlaction']=='results'){
		TPdlresults();
	}
	elseif($context['TPortal']['dlaction']=='search'){
		TPdlsearch();
	}
	// For wireless, we use the Wireless template...
	if (WIRELESS)
	{
		loadTemplate('TPwireless');
		if($context['TPortal']['dlaction']=='')
			$what='main';
		elseif($context['TPortal']['dlaction']=='item' || $context['TPortal']['dlaction']=='cat')
			$what=$context['TPortal']['dlaction'];
		else
			$what='main';

		$context['sub_template'] = WIRELESS_PROTOCOL . '_tpdl_'. $what;
	}
	else
		loadTemplate('TPdlmanager');

}

// searched the files?
function TPdlresults()
{
	global $txt, $scripturl, $db_prefix, $modSettings, $context, $settings , $boarddir, $user_info;

	$tp_prefix=$settings['tp_prefix'];

	$start=0;

	if(isset($_GET['p']) && is_numeric($_GET['p']))
		$start=$_GET['p'];
	
	checkSession('post');

	// nothing to search for?
	if(empty($_POST['dl_search']))
		fatal_error($txt['tp-nosearchentered']);

	// clean the search
	$what2=str_replace(' ','%',strip_tags($_POST['dl_search']));
	$what=strip_tags($_POST['dl_search']);

	if(!empty($_POST['dl_searcharea_name']))
		$usetitle=true;
	else
		$usetitle=false;
	if(!empty($_POST['dl_searcharea_desc']))
		$usebody=true;
	else
		$usebody=false;

	if($usetitle && !$usebody)
		$query = 'd.name LIKE \'%' . $what2 . '%\'';
	elseif(!$usetitle && $usebody)
		$query = 'd.description LIKE \'%' . $what2 . '%\'';
	elseif($usetitle && $usebody)
		$query = 'd.name LIKE \'%' . $what2 . '%\' OR d.description LIKE \'%' . $what2 . '%\'';
	else
		$query = 'd.name LIKE \'%' . $what2 . '%\'';

	$dlquery = '(FIND_IN_SET(' . implode(', access) OR FIND_IN_SET(', $user_info['groups']) . ', access))';
	
	// find out which categoies you ahve access to:
	$request=tp_query("SELECT id FROM " . $tp_prefix . "dlmanager WHERE type='dlcat' AND $dlquery", __FILE__, __LINE__);
	$allowedcats=array();
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
			$allowedcats[]=$row['id'];
		tpdb_free_result($request);
	}
	else
		$allowedcats[0]=-1;

	$tagquery = 'FIND_IN_SET(d.category, "' . implode(",",$allowedcats) .'")';
	
	$context['TPortal']['dlsearchresults']=array();
	$context['TPortal']['dlsearchterm']=$what;
	
	// find how many first
	$check=tp_query("SELECT COUNT(d.id)
		FROM " . $tp_prefix . "dlmanager AS d
		WHERE $query
		AND $tagquery
		AND type='dlitem'", __FILE__, __LINE__);
	$tt=tpdb_fetch_row($check);
	$total=$tt[0];
	
	$request=tp_query("SELECT d.id, d.created, d.type, d.downloads, d.name, LEFT(d.description, 100) as body, d.authorID, m.realName
		FROM " . $tp_prefix . "dlmanager AS d
		LEFT JOIN " . $db_prefix . "members as m ON d.authorID=m.ID_MEMBER
		WHERE $query
		AND $tagquery
		AND type='dlitem'
		ORDER BY d.created DESC LIMIT $start,15", __FILE__, __LINE__);
	// create pagelinks


	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
		{
			$row['name'] = preg_replace('/'.$what.'/', '<span class="highlight">'.$what.'</span>', $row['name']);
			$row['body'] = preg_replace('/'.$what.'/', '<span class="highlight">'.$what.'</span>', $row['body']);
			$row['body']=strip_tags(html_entity_decode($row['body'], ENT_QUOTES, $context['character_set']));

			$context['TPortal']['dlsearchresults'][]=array(
				'id' => $row['id'],
				'type' => $row['type'],
				'date' => $row['created'],
				'downloads' => $row['downloads'],
				'name' => $row['name'],
				'body' => $row['body'],
				'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
				);
		}
		tpdb_free_result($request);
	}
	TPadd_linktree($scripturl.'?action=tpmod;dl=search' , $txt['tp-dlsearch']);
}
// searched the files?
function TPdlsearch()
{
	global $txt, $scripturl ;

	TPadd_linktree($scripturl.'?action=tpmod;dl=search' , $txt['tp-dlsearch']);
}

// show some stats
function TPdlstats()
{
	global $txt, $scripturl, $db_prefix, $modSettings, $context, $settings , $boarddir;

	$tp_prefix=$settings['tp_prefix'];

	$context['TPortal']['dl_scats']=array();
	$context['TPortal']['dl_sitems']=array();
	$context['TPortal']['dl_scount']=array();
	$context['TPortal']['topcats']=array();
	// count items in each category
	$request = tp_query("SELECT category FROM " . $tp_prefix . "dlmanager WHERE type = 'dlitem'", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0){
		while($row=tpdb_fetch_assoc($request)){
			if($row['category']>0){
				if(isset($context['TPortal']['dl_scount'][$row['category']]))
					$context['TPortal']['dl_scount'][$row['category']]++;
				else
					$context['TPortal']['dl_scount'][$row['category']]=1;
			}
		}
		tpdb_free_result($request);
	}


	//first : fetch all allowed categories
	$context['TPortal']['uploadcats'] = array();
	$request = tp_query("SELECT id, parent, name, access FROM " . $tp_prefix . "dlmanager WHERE type = 'dlcat'", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0){
		while ($row = tpdb_fetch_assoc($request))
		{
			$show = get_perm($row['access'], 'tp_dlmanager');
			if($show)
				$context['TPortal']['uploadcats'][$row['id']] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'parent' => $row['parent'],
					);
		}
		tpdb_free_result($request);
	}
	//no categories to select...
	else
		return;

	// fetch all categories with subcats
	$req = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE type = 'dlcat'", __FILE__, __LINE__);
	if(tpdb_num_rows($req)>0){
		while($brow=tpdb_fetch_assoc($req)){
    		if(get_perm($brow['access'], 'tp_dlmanager')){
				if(isset($context['TPortal']['dl_scount'][$brow['id']]))
					$items=$context['TPortal']['dl_scount'][$brow['id']];
				else
					$items=0;

				$context['TPortal']['topcats'][] = array(
					'items' => $items,
					'link' => '<a href="'.$scripturl.'?action=tpmod;dl=cat'.$brow['id'].'">'.$brow['name'].'</a>',
					);
				// add the category as viewable
				$context['TPortal']['viewcats'][] = $brow['id'];
			}
		}
	tpdb_free_result($req);
	// sort it
    if(sizeof($context['TPortal']['topcats'])>1)
		usort($context['TPortal']['topcats'], "dlsort");

	}

		// fetch all items
		$context['TPortal']['topitems'] = array();

		$request = tp_query("SELECT category,filesize,views,downloads,id,name FROM " . $tp_prefix . "dlmanager WHERE type = 'dlitem'", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0){
			while($row=tpdb_fetch_assoc($request)){
				if(isset($context['TPortal']['viewcats']) && isset($row['category']) && is_array($context['TPortal']['viewcats']) && in_array($row['category'],$context['TPortal']['viewcats']))
					$context['TPortal']['topitems'][] = array(
						'size' => $row['filesize'],
						'views' => $row['views'],
						'downloads' => $row['downloads'],
						'link' => '<a href="'.$scripturl.'?action=tpmod;dl=item'.$row['id'].'">'.$row['name'].'</a>',
					);
			}
			tpdb_free_result($request);
		// sort it by filesize,views and downloads
		$context['TPortal']['topsize'] = array();
		$context['TPortal']['topviews']=array();
		$context['TPortal']['topsize']=$context['TPortal']['topitems'];

		if(is_array($context['TPortal']['topsize']))
    		usort($context['TPortal']['topsize'], "dlsortsize");

		$context['TPortal']['topviews']=$context['TPortal']['topitems'];

		if(is_array($context['TPortal']['topviews']))
    		usort($context['TPortal']['topviews'], "dlsortviews");

		if(is_array($context['TPortal']['topitems']))
    		usort($context['TPortal']['topitems'], "dlsortdownloads");
		}
}

// download a file
function TPdownloadme()
{
	global $txt, $scripturl, $db_prefix, $modSettings, $context, $settings , $boarddir;

	$tp_prefix=$settings['tp_prefix'];

	$item = $context['TPortal']['dlitem'];
	$request = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE id=$item LIMIT 1", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0){
		$row = tpdb_fetch_assoc($request);
		$myfilename=$row['name'];
		$newname=TPDlgetname($row['file']);
		$real_filename=$row['file'];
			if($row['subitem']>0)
			{
				$parent=$row['subitem'];
				$req3 = tp_query("SELECT category FROM " . $tp_prefix . "dlmanager WHERE id=$parent LIMIT 1", __FILE__, __LINE__);
				$what=tpdb_fetch_assoc($req3);
				$cat=$what['category'];
				$request2 = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE id=$cat", __FILE__, __LINE__);
				if(tpdb_num_rows($request2)>0){
					$row2 = tpdb_fetch_assoc($request2);
					$show = get_perm($row2['access'], 'tp_dlmanager');
					tpdb_free_result($request2);
				}
			}
			else
			{
				$cat=$row['category'];
				$request2 = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE id=$cat", __FILE__, __LINE__);
				if(tpdb_num_rows($request2)>0){
					$row2 = tpdb_fetch_assoc($request2);
					$show = get_perm($row2['access'], 'tp_dlmanager');
					tpdb_free_result($request2);
				}
			}
		$filename= $boarddir.'/tp-downloads/'.$real_filename;
		tpdb_free_result($request);
	}
	else
		$show=false;

	// can we actually download?
	if($show==1 || allowedTo('tp_dlmanager'))
	{
		$now=time();
		$year=date("Y",$now);
		$week=date("W",$now);	

		// update weekly views
		$req=tp_query("SELECT id FROM " . $tp_prefix . "dldata WHERE year=$year AND week=$week AND item=$item", __FILE__, __LINE__);

		if(tpdb_num_rows($req)>0)
		{
			$row=tpdb_fetch_assoc($req);
			tp_query("UPDATE " . $tp_prefix . "dldata SET downloads=downloads+1 WHERE id=$row[id]", __FILE__, __LINE__);
		}
		else
			tp_query("INSERT INTO " . $tp_prefix . "dldata (week,year,downloads, item) VALUES($week,$year,1,$item)", __FILE__, __LINE__);

		tp_query("UPDATE LOW_PRIORITY " . $tp_prefix . "dlmanager SET downloads = downloads + 1 WHERE id = $item LIMIT 1", __FILE__, __LINE__);
		ob_end_clean();
		if (!empty($modSettings['enableCompressedOutput']) && @version_compare(PHP_VERSION, '4.2.0') >= 0 && @filesize($filename) <= 4194304)
			@ob_start('ob_gzhandler');
		else
		{
			ob_start();
			header('Content-Encoding: none');
		}

		if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime(array_shift(explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE']))) >= filemtime($filename))
		{
			ob_end_clean();
			header('HTTP/1.1 304 Not Modified');
			exit;
		}

		// Send the attachment headers.
		header('Pragma: no-cache'); 
		header('Cache-Control: max-age=' . 10 . ', private');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		if (!$context['browser']['is_gecko'])
		header('Content-Transfer-Encoding: binary');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 525600 * 60) . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT');
		header('Accept-Ranges: bytes');
		header('Set-Cookie:');
		header('Connection: close');

		header('Content-Disposition: attachment; filename="' . $newname . '"');
		header('Content-Type: application/octet-stream');

		if (filesize($filename) != 0)
		{
			$size = @getimagesize($filename);
			if (!empty($size) && $size[2] > 0 && $size[2] < 4)
				header('Content-Type: image/' . ($size[2] != 1 ? ($size[2] != 2 ? 'png' : 'jpeg') : 'gif'));
		}

		if (empty($modSettings['enableCompressedOutput']) || filesize($filename) > 4194304)
			header('Content-Length: ' . filesize($filename));

		@set_time_limit(0);

		if (in_array(substr($real_filename, -4), array('.txt', '.css', '.htm', '.php', '.xml')))
		{
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows') !== false)
				$callback = create_function('$buffer', 'return preg_replace(\'~[\r]?\n~\', "\r\n", $buffer);');
			elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false)
				$callback = create_function('$buffer', 'return preg_replace(\'~[\r]?\n~\', "\r", $buffer);');
			else
				$callback = create_function('$buffer', 'return preg_replace(\'~\r~\', "\r\n", $buffer);');
		}

		// Since we don't do output compression for files this large...
		if (filesize($filename) > 4194304)
		{
			// Forcibly end any output buffering going on.
			if (function_exists('ob_get_level'))
			{
				while (@ob_get_level() > 0)
					@ob_end_clean();
			}
			else
			{
				@ob_end_clean();
				@ob_end_clean();
				@ob_end_clean();
			}

			$fp = fopen($filename, 'rb');
			while (!feof($fp))
			{
				if (isset($callback))
					echo $callback(fread($fp, 8192));
				else
					echo fread($fp, 8192);
				flush();
			}
			fclose($fp);
		}
		// On some of the less-bright hosts, readfile() is disabled.  It's just a faster, more byte safe, version of what's in the if.
		elseif (isset($callback) || @readfile($filename) == null)
			echo isset($callback) ? $callback(file_get_contents($filename)) : file_get_contents($filename);

		obExit(false);
	}
	else
		redirectexit('action=tpmod;dl');

}

// TinyPortal DLmanager admin
function TPortalDLAdmin()
{

	global $txt, $scripturl, $db_prefix, $ID_MEMBER, $user_info, $sourcedir, $boarddir, $boardurl;
	global $modSettings, $context, $settings, $tp_prefix, $func;

	$tp_prefix=$settings['tp_prefix'];

	// check permissions
	if(isset($_POST['dl_useredit'])){
		checkSession('post');
	}
	else
		isAllowedTo('tp_dlmanager');

	// add visual options to this section
	$dl_visual=explode(',',$context['TPortal']['dl_visual_options']);
	$dv=array('left','right','center','top','bottom','lower');
	foreach($dv as $v => $val){
		if(in_array($val,$dl_visual)){
			$context['TPortal'][$val.'panel']='1';
			$context['TPortal']['dl_'.$val]='1';
		}
		else
			$context['TPortal'][$val.'panel']='0';
	}

	if(in_array('showtop',$dl_visual)){
		$context['TPortal']['showtop']=true;
		$context['TPortal']['dl_top']=true;
	}
	else
		$context['TPortal']['showtop']=false;

	if($context['TPortal']['hidebars_admin_only']=='1')
		tp_hidebars();
	

	// fetch membergroups so we can quickly set permissions
	// dlmanager, dlupload, dlcreatetopic
	$context['TPortal']['perm_all_groups'] = get_grps();
	$context['TPortal']['perm_groups'] = tp_fetchpermissions(array('tp_dlmanager' ,'tp_dlupload' ,'tp_dlcreatetopic' ));
	$context['TPortal']['boards']= tp_fetchboards();

	$context['TPortal']['all_dlitems']=array();
	$request=tp_query("SELECT id, name	FROM " . $tp_prefix . "dlmanager
	WHERE type = 'dlitem'
	ORDER BY name ASC", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
		{
			$context['TPortal']['all_dlitems'][]=array(
				'id' => $row['id'],
				'name' => $row['name'],
			);
		}
		tpdb_free_result($request);
	}
	
	
	// any items from the ftp screen?
	if(!empty($_POST['ftpdlsend']))
	{
		// new category?
		if(!empty($_POST['assign-ftp-newcat'])){
			$newcat=true;
			$newcatname=$_POST['assign-ftp-newcat'];
			if(isset($_POST['assign-ftp-cat']) && $_POST['assign-ftp-cat']>0)
				$newcatparent=$_POST['assign-ftp-cat'];
			else
				$newcatparent=0;
			if($newcatname=='')
				$newcatname='-no name-';
		}
		else{
			$newcat=false;
			$newcatname='';
			$newcatnow=$_POST['assign-ftp-cat'];
			$newcatparent=0;
		}
		// if new category create it first.
		if($newcat){
			$request =tp_query("INSERT INTO " . $tp_prefix . "dlmanager (name, description, icon, category, type, downloads, views, file, created, last_access, filesize, parent, access, link,authorID,screenshot,rating,voters,subitem ) VALUES ('$newcatname', '', '' , 0 , 'dlcat', 0, 0, '', 0, 0, 0, $newcatparent, '', '', $ID_MEMBER, '','','',0)", __FILE__, __LINE__);
			$newcatnow=tpdb_insert_id($request);
		}
		// now go through each file and put it into the table.
		foreach($_POST as $what => $value){
			if(substr($what,0,19)=='assign-ftp-checkbox')
			{
				$name=$value;
				$now=time();
				$fsize=filesize($boarddir.'/tp-downloads/'.$value);
				tp_query("INSERT INTO " . $tp_prefix . "dlmanager (name, description, icon, category, type, downloads, views, file, created, last_access, filesize, parent, access, link,authorID,screenshot,rating,voters,subitem )
																		VALUES ('$name', '', '' , $newcatnow , 'dlitem', 1, 1, '$value', $now, $now, $fsize, 0, '', '', $ID_MEMBER, '','','',0)", __FILE__, __LINE__);
			}
		}
		// done, set a value to make member aware of assigned category
  		redirectexit('action=tpmod;dl=adminftp;ftpcat='.$newcatnow);
	}

	// check for new category
	if(!empty($_POST['newdlsend'])){
		// get the items
		$name=strip_tags($_POST['newdladmin_name']);
		// no html here
		if(empty($value))
			$value='-no title-';

		$text=$_POST['newdladmin_text'];
		$parent=$_POST['newdladmin_parent'];
		$icon=$boardurl.'/tp-downloads/icons/'.$_POST['newdladmin_icon'];
		// special case, the access
    	$dlgrp=array();
           foreach ($_POST as $what => $value) {
                  if(substr($what,0,16)=='newdladmin_group'){
					$vv=substr($what,16);
					if($vv!='-2')
                        $dlgrp[]=$vv;
                  }
           }
			$access=implode(',',$dlgrp);
			// insert the category
			$request =tp_query("INSERT INTO " . $tp_prefix . "dlmanager (name, description, icon, category, type, downloads, views, file, created, last_access, filesize, parent, access, link,authorID,screenshot,rating,voters,subitem ) VALUES ('$name', '$text', '$icon' , 0 , 'dlcat', 0, 0, '', 0, 0, 0, $parent, '$access', '', $ID_MEMBER, '','','',0)", __FILE__, __LINE__);
			$newcat=tpdb_insert_id($request);
 			redirectexit('action=tpmod;dl=admineditcat'.$newcat);
	}

	$myid=0;
	// check if tag links are present
	if(isset($_POST['dladmin_itemtags']))
	{
		$itemid=$_POST['dladmin_itemtags'];
		// get title
		$request=tp_query("SELECT name FROM " . $tp_prefix . "dlmanager WHERE id=$itemid LIMIT 1", __FILE__, __LINE__);
		$title=tpdb_fetch_row($request);
		// remove old ones first
		tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type ='globaltag_item' AND value3='dladmin_itemtags' AND subtype2=$itemid", __FILE__, __LINE__);
		$alltags=array();
		foreach($_POST as $what => $value)
		{
			// a tag from edit items
			if(substr($what,0,17)=='dladmin_itemtags_')
			{
				$tag=substr($what,17);
				$itemid=$value;
				// insert new one
				$href='?action=tpmod;dl=item'.$itemid;
				$tg = '<span style="background: url('.$settings['tp_images_url'].'/glyph_download.png) no-repeat;" class="taglink">' . $title[0]. '</span>';
				if(!empty($tag))
				{
					tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5,subtype,value7,value8,subtype2) 
					VALUES('$href','$tg','dladmin_itemtags','globaltag_item','',0,'$tag','','',$itemid)", __FILE__, __LINE__);
					$alltags[]=$tag;
				}
	
			}
		}
		$tg=implode(',',$alltags);
		tp_query("UPDATE " . $tp_prefix . "dlmanager SET global_tag='$tg' WHERE id=$itemid", __FILE__, __LINE__);

		$myid=$itemid;
		$go=2;
		$newgo=2;
	}
	// check if tag links are present -categories
	if(isset($_POST['dladmin_cattags']))
	{
		$itemid=$_POST['dladmin_cattags'];
		// get title
		$request=tp_query("SELECT name FROM " . $tp_prefix . "dlmanager WHERE id=$itemid LIMIT 1", __FILE__, __LINE__);
		$title=tpdb_fetch_row($request);
		// remove old ones first
		tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type ='globaltag_item' AND value3='dladmin_cattags' AND subtype2=$itemid", __FILE__, __LINE__);
		foreach($_POST as $what => $value)
		{
			// a tag from edit category
			if(substr($what,0,16)=='dladmin_cattags_')
			{
				$tag=substr($what,16);
				$itemid=$value;
				// insert new one
				$href='?action=tpmod;dl=cat'.$itemtag;
				$title = $title[0].' ['.strtolower($txt['tp-downloads']).'] ';
				tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5,subtype,value7,value8,subtype2) 
					VALUES('$href','$title','dladmin_cattags','globaltag_item','',0,'$tag','','',$itemid)", __FILE__, __LINE__);
			}
		}
		$myid=$itemid;
		$go=3;
		$newgo=3;
	}

	// check for access value
	if(!empty($_POST['dlsend']))
	{
 		$admgrp=array(); $groupset=false;
 		$dlgrp=array(); $dlset=false; $visual=array(); $visualset=false;
        $creategrp=array(); $dlmanager_grp=array();
		$dlupload_grp=array(); $dlcreatetopic_grp=array();
		
		   foreach ($_POST as $what => $value) {
                  if(substr($what,0,13)=='dladmin_group'){
                        $val=substr($what,13);
						if($val!='-2')
                            $admgrp[]=$val;
                        $groupset=true;
                        $id=$value;
                  }
                  elseif(substr($what,0,8)=='tp_group'){
						if($value!='-2')
                            $dlgrp[]=$value;
                        $dlset=true;
                  }
                  elseif(substr($what,0,20)=='tp_dl_visual_options'){
						if($value!='not')
                            $visual[]=$value;
                        $visualset=true;
                  }
                  elseif(substr($what,0,11)=='tp_dlboards'){
                        $creategrp[]=$value;
                  }
                  elseif(substr($what,0,9)=='dlmanager'){
                        $dlmanager_grp[]=$value;
                  }
                  elseif(substr($what,0,8)=='dlupload'){
                        $dlupload_grp[]=$value;
                  }
                  elseif(substr($what,0,13)=='dlcreatetopic'){
                        $dlcreatetopic_grp[]=$value;
                  }
           }
		  if(!empty($_POST['dlsettings']))
		 {
				$dlb=implode(",",$creategrp);
		  		tp_query("UPDATE " . $tp_prefix . "settings SET value = '$dlb' WHERE name='dl_createtopic_boards'", __FILE__, __LINE__);
				// round up the access groups.
				// check which has the group
				$grp=array(); $grp2=array(); $grp3=array();
				$grp=tp_fetchpermissions(array('tp_dlmanager'));
				$grp2=tp_fetchpermissions(array('tp_dlupload'));
				$grp3=tp_fetchpermissions(array('tp_dlcreatetopic'));
				
				if (allowedTo('manage_permissions'))
				{
					tp_query("DELETE FROM " . $db_prefix . "permissions WHERE permission='tp_dlmanager' OR permission='tp_dlupload' OR permission='tp_dlcreatetopic'", __FILE__, __LINE__);
					foreach($dlmanager_grp as $pr => $val)
		  				tp_query("INSERT INTO " . $db_prefix . "permissions (ID_GROUP,permission,addDeny) VALUES($val, 'tp_dlmanager',1)", __FILE__, __LINE__);
					foreach($dlupload_grp as $pr => $val)
		  				tp_query("INSERT INTO " . $db_prefix . "permissions (ID_GROUP,permission,addDeny) VALUES($val, 'tp_dlupload',1)", __FILE__, __LINE__);
					foreach($dlcreatetopic_grp as $pr => $val)
		  				tp_query("INSERT INTO " . $db_prefix . "permissions (ID_GROUP,permission,addDeny) VALUES($val, 'tp_dlcreatetopic',1)", __FILE__, __LINE__);
				}
		  }

		  if($groupset){
           	$dlaccess=implode(',',$admgrp);
		  	tp_query("UPDATE " . $tp_prefix . "dlmanager SET access = '$dlaccess' WHERE id=$id", __FILE__, __LINE__);
		  }
		  if($dlset){
           	$dlaccess2=implode(',',$dlgrp);
		  	tp_query("UPDATE " . $tp_prefix . "settings SET value = '$dlaccess2' WHERE name='dl_approve_groups'", __FILE__, __LINE__);
		  }
		  if($visualset){
           	$dlvisual=implode(',',$visual);
		  	tp_query("UPDATE " . $tp_prefix . "settings SET value = '$dlvisual' WHERE name='dl_visual_options'", __FILE__, __LINE__);
		  }
		$go=0;

		if(!empty($_FILES['qup_dladmin_text']['tmp_name']) && (file_exists($_FILES['qup_dladmin_text']['tmp_name']) || is_uploaded_file($_FILES['qup_dladmin_text']['tmp_name'])))
		{
			$name=TPuploadpicture('qup_dladmin_text', $ID_MEMBER.'uid');
			tp_createthumb('tp-images/'.$name ,50,50, 'tp-images/thumbs/thumb_'.$name);
		}
		if(!empty($_FILES['qup_blockbody']['tmp_name']) && (file_exists($_FILES['qup_dladmin_text']['tmp_name']) || is_uploaded_file($_FILES['qup_dladmin_text']['tmp_name'])))
		{
			$name=TPuploadpicture('qup_dladmin_text', $ID_MEMBER.'uid');
			tp_createthumb('tp-images/'.$name ,50,50, 'tp-images/thumbs/thumb_'.$name);
		}

		// a screenshot from edit item screen?
		if(!empty($_FILES['tp_dluploadpic_edit']['tmp_name']) && (file_exists($_FILES['tp_dluploadpic_edit']['tmp_name']) || is_uploaded_file($_FILES['tp_dluploadpic_edit']['tmp_name'])))
			$shot=true;
		else
			$shot=false;

		if($shot){
			$sid = $_POST['tp_dluploadpic_editID'];		
			$sfile = 'tp_dluploadpic_edit';
			$uid = $context['user']['id'].'uid';
			$dim = '1800';
			$suf = 'jpg,gif,png';
			$dest = 'tp-images/dlmanager';
			$sname = TPuploadpicture($sfile, $uid, $dim, $suf, $dest);
			$screenshot = $sname;
			tp_createthumb($dest.'/'.$sname, $context['TPortal']['dl_screenshotsize'][0],$context['TPortal']['dl_screenshotsize'][1], $dest.'/thumb/'.$sname);
			tp_createthumb($dest.'/'.$sname, $context['TPortal']['dl_screenshotsize'][2],$context['TPortal']['dl_screenshotsize'][3], $dest.'/listing/'.$sname);
			tp_createthumb($dest.'/'.$sname, $context['TPortal']['dl_screenshotsize'][4],$context['TPortal']['dl_screenshotsize'][5], $dest.'/single/'.$sname);
			
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET screenshot='$screenshot' WHERE id=$sid", __FILE__, __LINE__);
			$uploaded=true;
		}
		else{
			$screenshot='';
			$uploaded=false;
		}

		if(isset($_POST['tp_dluploadpic_link']) && !$uploaded){
			$sid=$_POST['tp_dluploadpic_editID'];
			$screenshot = $_POST['tp_dluploadpic_link'];
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET screenshot='$screenshot' WHERE id=$sid", __FILE__, __LINE__);
		}
		else
			$screenshot='';

	// a new file uploaded?
		if(!empty($_FILES['tp_dluploadfile_edit']['tmp_name']) && is_uploaded_file($_FILES['tp_dluploadfile_edit']['tmp_name']))
		{	
			$shot=true;
		}
		else
			$shot=false;

		if($shot)
		{
			$sid=$_POST['tp_dluploadfile_editID'];
			$shotname=$_FILES['tp_dluploadfile_edit']['name'];
			$sname = strtr($shotname, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
			$sname = strtr($sname, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
			$sname = preg_replace(array('/\s/', '/[^\w_\.\-]/'), array('_', ''), $sname);
			$sname=time().$sname;
			// check the size
			$dlfilesize = filesize($_FILES['tp_dluploadfile_edit']['tmp_name']);
			if($dlfilesize>(1000*$context['TPortal']['dl_max_upload_size'])){
				unlink($_FILES['tp_dluploadfile_edit']['tmp_name']);
				$error = $txt['tp-dlmaxerror'].' '.($context['TPortal']['dl_max_upload_size']).' Kb<br /><br />'.$txt['tp-dlmaxerror2'].': '. ceil($dlfilesize/1000) .' Kb';
				fatal_error($error);
			}

			// check the extension
			$allowed=explode(',',$context['TPortal']['dl_allowed_types']);
			$match=false;
			foreach($allowed as $extension => $value)
			{
				$ext='.'.$value;
				$extlen=strlen($ext);
				if(substr($sname, strlen($sname)-$extlen, $extlen)==$ext)
					$match=true;
			}
			if(!$match){
				unlink($_FILES['tp_dluploadfile_edit']['tmp_name']);
				$error = $txt['tp-dlexterror'].':<b> <br />'.$context['TPortal']['dl_allowed_types'].'</b><br /><br />'.$txt['tp-dlexterror2'].': <b>'.$sname.'</b>';
				fatal_error($error);
			}
			$success2=move_uploaded_file($_FILES['tp_dluploadfile_edit']['tmp_name'],$boarddir.'/tp-downloads/'.$sname);
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET file='$sname' WHERE id=$sid", __FILE__, __LINE__);
			$new_upload=true;
			// update filesize as well
			$value=filesize($boarddir.'/tp-downloads/'.$sname);
			if(!is_numeric($value))
				$value=0;
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET filesize = $value WHERE id=$sid", __FILE__, __LINE__);
			$myid=$sid;
			$go=2;
		}
	// get all values from forms
	foreach($_POST as $what => $value){
		if(substr($what,0,12)=='dladmin_name'){
			$id=substr($what,12);
			// no html here
			$value=strip_tags($value);
			if(empty($value))
				$value='-no title-';
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET name = '$value' WHERE id=$id", __FILE__, __LINE__);
		}
		elseif(substr($what,0,12)=='dladmin_icon'){
			$id=substr($what,12);
			if($value!=''){
				$val=$boardurl.'/tp-downloads/icons/'.$value;
				tp_query("UPDATE " . $tp_prefix . "dlmanager SET icon = '$val' WHERE id=$id", __FILE__, __LINE__);
			}
		}
		elseif(substr($what,0,12)=='dladmin_text')
		{
			$id=substr($what,12);
			if(is_numeric($id))
			{
				if(isset($_POST['dladmin_text'.$id.'_pure']) && isset($_POST['dladmin_text'.$id.'_choice']))
				{
					if($_POST['dladmin_text'.$id.'_choice']==1)
						$value=$func['htmlspecialchars']($_POST['dladmin_text'.$id], ENT_QUOTES);
					else
						$value=$func['htmlspecialchars']($_POST['dladmin_text'.$id.'_pure'], ENT_QUOTES);
				}
				tp_query("UPDATE " . $tp_prefix . "dlmanager SET description = '" . mysql_real_escape_string(stripslashes($value)) . "' WHERE id=". $id, __FILE__, __LINE__);
			}
		}
		elseif(substr($what,0,14)=='dladmin_delete'){
			$id=substr($what,14);
				$request = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE id = $id", __FILE__, __LINE__);
				if(tpdb_num_rows($request)>0)
				{
					$row=tpdb_fetch_assoc($request);
					if ($row['type'] == 'dlitem')
					{
						$category = $row['category'];
						if ($category > 0)
						{
							tp_query("UPDATE " . $tp_prefix . "dlmanager SET downloads = downloads - 1 WHERE id = $category LIMIT 1", __FILE__, __LINE__);
						}
						// delete both screenshot and file
						if(!empty($row['file']) && file_exists($boarddir.'/tp-downloads/'.$row['file'])){
							$succ=unlink($boarddir.'/tp-downloads/'.$row['file']);
							if(!$succ)
								$err='Unable to delete the actual file, but the item was deleted. ('.$row['file'].')';
						}
						if(!empty($row['screenshot']) && file_exists($boarddir.'/'.$row['screenshot'])){
							$succ2=unlink($boarddir.'/'.$row['screenshot']);
							if(!$succ2)
								$err .='<br />Unable to delete the actual screenshot, but the item was deleted. ('.$row['screenshot'].')';
						}

					}
					tpdb_free_result($request);
				}
			tp_query("DELETE FROM " . $tp_prefix . "dlmanager WHERE id=$id", __FILE__, __LINE__);
			if(isset($err))
				fatal_error($err);
			redirectexit('action=tpmod;dl=admincat'.$category);
		}
		elseif(substr($what,0,15)=='dladmin_approve' && $value=='ON')
		{
			$id=abs(substr($what,15));
			$request = tp_query("SELECT category FROM " . $tp_prefix . "dlmanager WHERE id = $id", __FILE__, __LINE__);
			if(tpdb_num_rows($request)>0)
			{
				$row=tpdb_fetch_row($request);
				$newcat = abs($row[0]);
				tp_query("UPDATE " . $tp_prefix . "dlmanager SET category=$newcat WHERE id = $id", __FILE__, __LINE__);
				tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type = 'dl_not_approved' AND value5 = $id", __FILE__, __LINE__);
				tpdb_free_result($request);
			}
		}
		elseif(substr($what,0,16)=='dl_admin_approve' && $value=='ON'){
			$id=abs(substr($what,16));
			$request = tp_query("SELECT category FROM " . $tp_prefix . "dlmanager WHERE id = $id", __FILE__, __LINE__);
			if(tpdb_num_rows($request)>0)
			{
				$row=tpdb_fetch_row($request);
				$newcat = abs($row[0]);
				tp_query("UPDATE " . $tp_prefix . "dlmanager SET category=$newcat WHERE id = $id", __FILE__, __LINE__);
				tp_query("DELETE FROM " . $tp_prefix . "variables WHERE type = 'dl_not_approved' AND value5 = $id", __FILE__, __LINE__);
				tpdb_free_result($request);
			}
		}
		elseif(substr($what,0,16)=='dladmin_category'){
			$id=substr($what,16);
			// update, but not on negative values :)
			if($value>0)
				tp_query("UPDATE " . $tp_prefix . "dlmanager SET category = $value WHERE id=$id", __FILE__, __LINE__);
		}
		elseif(substr($what,0,14)=='dladmin_parent'){
			$id=substr($what,14);
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET parent = $value WHERE id=$id", __FILE__, __LINE__);
		}
		elseif(substr($what,0,15)=='dladmin_subitem'){
			$id=substr($what,15);
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET subitem = $value WHERE id=$id", __FILE__, __LINE__);
		}
		elseif(substr($what,0,11)=='tp_dlcatpos'){
			$id=substr($what,11);
			if(!empty($_POST['admineditcatval']))
			{
				$myid=$_POST['admineditcatval'];
				$go=4;
			}

			tp_query("UPDATE " . $tp_prefix . "dlmanager SET downloads = $value WHERE id=$id", __FILE__, __LINE__);
		}
		elseif(substr($what,0,18)=='dladmin_screenshot'){
			$id=substr($what,18);
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET screenshot = '$value' WHERE id=$id", __FILE__, __LINE__);
		}
		elseif(substr($what,0,12)=='dladmin_link'){
			$id=substr($what,12);
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET link = '$value' WHERE id=$id", __FILE__, __LINE__);
		}
		elseif(substr($what,0,12)=='dladmin_file' && !isset($new_upload)){
			$id=substr($what,12);
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET file = '$value' WHERE id=$id", __FILE__, __LINE__);
			$myid=$id;
			$go=2;
		}
		elseif(substr($what,0,12)=='dladmin_size' && !isset($new_upload)){
			$id=substr($what,12);
			// check the actual size
			$name=$_POST['dladmin_file'.$id];
			$value=filesize($boarddir.'/tp-downloads/'.$name);
			if(!is_numeric($value))
				$value=0;
			tp_query("UPDATE " . $tp_prefix . "dlmanager SET filesize = $value WHERE id=$id", __FILE__, __LINE__);
		}
		// from settings in DLmanager
		elseif($what=='tp_dl_allowed_types'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_allowed_types'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_usescreenshot'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_usescreenshot'", __FILE__, __LINE__);
			$go=1;
		}
		elseif(substr($what,0,20)=='tp_dl_screenshotsize'){
			// which one
			$who=substr($what,20);
			$result=tp_query("SELECT value FROM " . $tp_prefix . "settings WHERE name='dl_screenshotsizes' LIMIT 1", __FILE__, __LINE__);
			$row=tpdb_fetch_assoc($result);
			tpdb_free_result($result);
			$all=explode(",",$row['value']);
			$all[$who]=$value; $newval=implode(",",$all);
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$newval' WHERE name='dl_screenshotsizes'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_showfeatured'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_showfeatured'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_wysiwyg'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_wysiwyg'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_showrecent'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_showlatest'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_showstats'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_showstats'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_showcategorytext'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_showcategorytext'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_featured'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_featured'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_introtext'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = ' ". $func['htmlspecialchars']($value, ENT_QUOTES) ."' WHERE name='dl_introtext'", __FILE__, __LINE__);
			$go=1;
		}
	
		elseif($what=='tp_dluploadsize'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_max_upload_size'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_approveonly'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_approve'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dlallowupload'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_allow_upload'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dl_fileprefix'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dl_fileprefix'", __FILE__, __LINE__);
			$go=1;
		}
		elseif($what=='tp_dltheme'){
			tp_query("UPDATE " . $tp_prefix . "settings SET value = '$value' WHERE name='dlmanager_theme'", __FILE__, __LINE__);
			$go=1;
		}
   }
	// if we came from useredit screen..
	if(isset($_POST['dl_useredit']))
	   redirectexit('action=tpmod;dl=useredit'.$_POST['dl_useredit']);
	
	if(!empty($newgo))
		$go=$newgo;
	// guess not, admin screen then
	if($go==1)
	   redirectexit('action=tpmod;dl=adminsettings');
	elseif($go==2)
	   redirectexit('action=tpmod;dl=adminitem'.$myid);
	elseif($go==3)
	   redirectexit('action=tpmod;dl=admineditcat'.$myid);
	elseif($go==4)
	   redirectexit('action=tpmod;dl=admincat'.$myid);
}
	// ****************

	TP_dlgeticons();
	// get all themes
                $context['TPthemes'] = array();
				$request = tp_query("
                        SELECT value AS name, ID_THEME
                        FROM " . $db_prefix . "themes
                        WHERE variable = 'name'
                                AND ID_MEMBER = 0
                        ORDER BY value ASC", __FILE__, __LINE__);
                if(tpdb_num_rows($request)>0){
                  while ($row = tpdb_fetch_assoc($request))
                  {
						$context['TPthemes'][] = array(
                                'id' => $row['ID_THEME'],
                                'name' => $row['name']
                        );
                   }
                   tpdb_free_result($request);
                }

	// fetch all files from tp-downloads
	$context['TPortal']['tp-downloads'] = array();
	$count=1;
	if ($handle = opendir($boarddir.'/tp-downloads')) {
		while (false !== ($file = readdir($handle))) {
			if($file!= '.' && $file!='..' && $file!='.htaccess' && $file!='icons'){
				$size=(floor(filesize($boarddir.'/tp-downloads/'.$file)/102.4)/10);
				$context['TPortal']['tp-downloads'][$count] = array(
						'id' => $count,
						'file' => $file,
						'size' => $size,
						);
				$count++;
			}
		}
		closedir($handle);
	}
   // get all membergroups for permissions
	$context['TPortal']['dlgroups'] = get_grps(true,true);

	//fetch all categories
	$sorted = array();
	$context['TPortal']['linkcats'] = array();
	$srequest = tp_query("SELECT id, name, description, icon, access, parent FROM " . $tp_prefix . "dlmanager WHERE type = 'dlcat' ORDER BY downloads ASC", __FILE__, __LINE__);
	if(tpdb_num_rows($srequest)>0){
		while ($row = tpdb_fetch_assoc($srequest))
		{
			// for the linktree
			$context['TPortal']['linkcats'][$row['id']] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'parent' => $row['parent'],
				);

			$sorted[$row['id']] = array(
							'id' => $row['id'],
							'parent' => $row['parent'],
							'name' => $row['name'],
							'text' => $row['description'],
							'icon' => $row['icon'],
							);
		}
			tpdb_free_result($srequest);
	}
	// sort them
	if(count($sorted)>1)
		$context['TPortal']['admuploadcats'] = chain('id', 'parent', 'name', $sorted);
	else
		$context['TPortal']['admuploadcats'] = $sorted;


	$context['TPortal']['dl_admcats']=array();
	$context['TPortal']['dl_admcats2']=array();
	$context['TPortal']['dl_admitems']=array();
	$context['TPortal']['dl_admcount']=array();
	$context['TPortal']['dl_admsubmitted']=array();
	$context['TPortal']['dl_allitems']=array();
	// count items in each category
	$request = tp_query("SELECT file,category FROM " . $tp_prefix . "dlmanager WHERE type = 'dlitem'", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0){
		while($row=tpdb_fetch_assoc($request)){
			if($row['category']<0){
				if(isset($context['TPortal']['dl_admsubmitted'][abs($row['category'])]))
					$context['TPortal']['dl_admsubmitted'][abs($row['category'])]++;
				else
					$context['TPortal']['dl_admsubmitted'][abs($row['category'])]=1;
			}
			else{
				if(isset($context['TPortal']['dl_admcount'][$row['category']]))
					$context['TPortal']['dl_admcount'][$row['category']]++;
				else
					$context['TPortal']['dl_admcount'][$row['category']]=1;
			}
			$context['TPortal']['dl_allitems'][]=$row['file'];
		}
		tpdb_free_result($request);
	}


	// fetch all categories
	$admsub = substr($context['TPortal']['dlsub'],5);
	if($admsub==''){
		$context['TPortal']['dl_title'] = $txt['tp-dladmin'];
		// fetch all categories with subcats
		$req = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE type = 'dlcat' ORDER BY downloads ASC", __FILE__, __LINE__);
		if(tpdb_num_rows($req)>0){
			while($brow=tpdb_fetch_assoc($req)){
				if(isset($context['TPortal']['dl_admcount'][$brow['id']]))
					$items=$context['TPortal']['dl_admcount'][$brow['id']];
				else
					$items=0;

				if(isset($context['TPortal']['dl_admsubmitted'][$brow['id']]))
					$sitems=$context['TPortal']['dl_admsubmitted'][$brow['id']];
				else
					$sitems=0;

				$context['TPortal']['admcats'][] = array(
					'id' => $brow['id'],
					'name' => $brow['name'],
					'icon' => $brow['icon'],
					'access' => $brow['access'],
					'parent' => $brow['parent'],
					'description' => html_entity_decode($brow['description'], ENT_QUOTES, $context['character_set']),
					'shortname' => $brow['link'],
					'items' => $items,
					'submitted' => $sitems,
					'total' => ($items + $sitems),
					'href' => $scripturl.'?action=tpmod;dl=admincat'.$brow['id'],
					'href2' => $scripturl.'?action=tpmod;dl=admineditcat'.$brow['id'],
					'href3' => $scripturl.'?action=tpmod;dl=admindelcat'.$brow['id'],
					'pos' => $brow['downloads'],
					);
			}
			tpdb_free_result($req);
		}
	}
	elseif(substr($admsub,0,3)=='cat'){
		$cat=substr($admsub,3);
		// get the parent first
		$request = tp_query("SELECT parent,name,link FROM " . $tp_prefix . "dlmanager WHERE type = 'dlcat' AND id=$cat", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0){
			$row=tpdb_fetch_assoc($request);
			$catparent=abs($row['parent']);
			$catname=$row['name'];
			$catshortname=$row['link'];
			tpdb_free_result($request);
		}

		// fetch items within a category
		$request = tp_query("SELECT dl.*, m.realName
						FROM (" . $tp_prefix . "dlmanager AS dl, " . $db_prefix . "members AS m)
						WHERE abs(dl.category)=$cat
						AND dl.type = 'dlitem'
						AND dl.subitem = 0
						AND dl.authorID = m.ID_MEMBER
						ORDER BY dl.id DESC", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0){
			while($row=tpdb_fetch_assoc($request))
			{
				$context['TPortal']['dl_admitems'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'icon' => $row['icon'],
					'category' => abs($row['category']),
					'file' => $row['file'],
					'filesize' => floor($row['filesize']/1024),
					'views' => $row['views'],
					'authorID' => $row['authorID'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
					'created' => timeformat($row['created']),
					'last_access' => timeformat($row['last_access']),
					'description' => html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
					'downloads' => $row['downloads'],
					'sshot' => $row['screenshot'],
					'link' => $row['link'],
					'href' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
					'approved' => $row['category']<0 ? '0' : '1',
					'approve' => $scripturl.'?action=tpmod;dl=adminapprove'.$row['id'],
				);
			}
			tpdb_free_result($request);
		}
		// fetch all categories with subcats
		$request = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE type = 'dlcat' ORDER BY name ASC", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0){
			while($row=tpdb_fetch_assoc($request)){
				if(isset($context['TPortal']['dl_admcount'][$row['id']]))
					$items=$context['TPortal']['dl_admcount'][$row['id']];
				else
					$items=0;

				if(isset($context['TPortal']['dl_admsubmitted'][$row['id']]))
					$sitems=$context['TPortal']['dl_admsubmitted'][$row['id']];
				else
					$sitems=0;

				$context['TPortal']['admcats'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'pos' => $row['downloads'],
					'icon' => $row['icon'],
					'shortname' => $row['link'],
					'access' => $row['access'],
					'parent' => $row['parent'],
					'description' => html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
					'items' => $items,
					'submitted' => $sitems,
					'total' => ($items + $sitems),
					'href' => $scripturl.'?action=tpmod;dl=admincat'.$row['id'],
					'href2' => $scripturl.'?action=tpmod;dl=admineditcat'.$row['id'],
					'href3' => $scripturl.'?action=tpmod;dl=admindelcat'.$row['id'],
					);
			}
			tpdb_free_result($request);
		}
		// check to see if its child
		$parents=array();
		while($catparent>0 ){
			$parents[$catparent] = array(
				'id' => $catparent,
				'name' => $context['TPortal']['linkcats'][$catparent]['name'],
				'parent' => $context['TPortal']['linkcats'][$catparent]['parent']
				);
			$catparent = $context['TPortal']['linkcats'][$catparent]['parent'];
		}

		// make the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=admin', $txt['tp-dladmin']);

		if(isset($parents)){
			$parts=array_reverse($parents,TRUE);
			// add to the linktree
			foreach($parts as $parent){
				TPadd_linktree($scripturl.'?action=tpmod;dl=admincat'.$parent['id'] , $parent['name']);
			}
		}
		// add to the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=admincat'.$cat , $catname);
	}
	elseif($context['TPortal']['dlsub']=='adminsubmission'){
			// check any submissions if admin
			$submitted=array();
			isAllowedTo('tp_dlmanager');
			$context['TPortal']['dl_admitems'] = array();
			$request = tp_query("SELECT dl.id, dl.name, dl.file, dl.created, dl.filesize, dl.authorID, m.realName
						FROM (" . $tp_prefix . "dlmanager AS dl, " . $db_prefix . "members AS m)
						WHERE dl.type = 'dlitem'
						AND dl.category<0
						AND dl.authorID = m.ID_MEMBER", __FILE__, __LINE__);
			if(tpdb_num_rows($request)>0)
			{
				$rows=tpdb_num_rows($request);
				while ($row = tpdb_fetch_assoc($request))
				{
					$context['TPortal']['dl_admitems'][] = array(
						'id' => $row['id'],
						'name' => $row['name'],
						'file' => $row['file'],
						'filesize' => floor($row['filesize']/1024),
						'href' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
						'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
						'date' => timeformat($row['created']),
						);
						$submitted[]=$row['id'];
				}
				tpdb_free_result($request);
			}
			// check that submissions link to downloads
			$request = tp_query("SELECT id,value5 FROM " . $tp_prefix . "variables WHERE type='dl_not_approved'", __FILE__, __LINE__);
			if(tpdb_num_rows($request)>0){
				while($row=tpdb_fetch_assoc($request)){
					$what=$row['id'];
					if(!in_array($row['value5'],$submitted))
						tp_query("DELETE FROM " . $tp_prefix . "variables WHERE id=$what", __FILE__, __LINE__);
				}
				tpdb_free_result($request);
			}
	}
	elseif(substr($admsub,0,7)=='editcat'){
		$context['TPortal']['dl_title'] = '<a href="'.$scripturl.'?action=tpmod;dl=admin">'.$txt['tp-dladmin'].'</a>';
		$cat=substr($admsub,7);
		// edit category
		$request = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE id=$cat AND type = 'dlcat' LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0){
			while($row=tpdb_fetch_assoc($request)){
				$context['TPortal']['admcats'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'access' => $row['access'],
					'shortname' => $row['link'],
					'description' => html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
					'icon' => $row['icon'],
					'parent' => $row['parent'],
				);
			}
			tpdb_free_result($request);
		}
	}
	elseif(substr($admsub,0,6)=='delcat'){
		$context['TPortal']['dl_title'] = '<a href="'.$scripturl.'?action=tpmod;dl=admin">'.$txt['tp-dladmin'].'</a>';
		$cat=substr($admsub,6);
		// delete category and all item it's in
		$request = tp_query("DELETE FROM " . $tp_prefix . "dlmanager WHERE type='dlitem' AND category = $cat", __FILE__, __LINE__);
		$request = tp_query("DELETE FROM " . $tp_prefix . "dlmanager WHERE id=$cat LIMIT 1", __FILE__, __LINE__);
		redirectexit('action=tpmod;dl=admin');
	}
	elseif(substr($admsub,0,8)=='settings'){
		$context['TPortal']['dl_title'] = $txt['tp-dlsettings'];
	}
	elseif(substr($admsub,0,4)=='item'){
		$item=substr($admsub,4);
		$request = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE id=$item AND type = 'dlitem' LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0){
				$row=tpdb_fetch_assoc($request);

				// is it actually a subitem?
				if($row['subitem']>0)
					redirectexit('action=tpmod;dl=adminitem'.$row['subitem']);

				// get all items for a list
				$context['TPortal']['admitems']=array();
				$itemlist = tp_query("SELECT id,name FROM " . $tp_prefix . "dlmanager WHERE id!=$item AND type = 'dlitem' AND subitem=0 ORDER BY name ASC", __FILE__, __LINE__);
				if(tpdb_num_rows($itemlist)>0){
					while($ilist=tpdb_fetch_assoc($itemlist))
					{
						$context['TPortal']['admitems'][] = array(
							'id' => $ilist['id'],
							'name' => $ilist['name'],
						);
					}
				}

				// Any additional files then..?
				$subitem = $row['id'];
				$fdata=array();
				$fetch = tp_query("SELECT id, name, file, downloads, filesize,created
							FROM " . $tp_prefix . "dlmanager
							WHERE type = 'dlitem'
							AND subitem=$subitem", __FILE__, __LINE__);
					
				if(tpdb_num_rows($fetch)>0)
				{
					while($frow = tpdb_fetch_assoc($fetch))
					{
						if($context['TPortal']['dl_fileprefix']=='K')
							$ffs=ceil($row['filesize']/1000).' Kb';
						elseif($context['TPortal']['dl_fileprefix']=='M')
							$ffs=(ceil($row['filesize']/1000)/1000).' Mb';
						elseif($context['TPortal']['dl_fileprefix']=='G')
							$ffs=(ceil($row['filesize']/1000000)/1000).' Gb';
								
						$fdata[] = array(
									'id' => $frow['id'],
									'name' => $frow['name'],
									'file' => $frow['file'],
									'href' => $scripturl.'?action=tpmod;dl=item'.$frow['id'],
									'downloads' => $frow['downloads'],
									'created' => $frow['created'],
									'filesize' => $ffs,
								);
					}
					tpdb_free_result($fetch);
				}
				if(substr($row['screenshot'],0,10)=='tp-images/')
					$sshot=$boardurl.'/'.$row['screenshot'];
				else
					$sshot=$boardurl.'/tp-images/dlmanager/listing/'.$row['screenshot'];


				$context['TPortal']['dl_admitems'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'icon' => $row['icon'],
					'category' => $row['category'],
					'file' => $row['file'],
					'views' => $row['views'],
					'authorID' => $row['authorID'],
					'description' => html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
					'created' => timeformat($row['created']),
					'last_access' => timeformat($row['last_access']),
					'filesize' => (substr($row['file'],14)!='- empty item -') ? floor(filesize($boarddir.'/tp-downloads/'.$row['file'])/1024) : '0' ,
					'downloads' => $row['downloads'],
					'sshot' => $sshot,
					'screenshot' => $row['screenshot'],
					'link' => $row['link'],
					'href' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
					'approved' => $row['category'] < 0 ? '0' : '1' ,
					'approve' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
					'subitem' => $fdata,
					);
				$authorID=$row['authorID'];
				$catparent=$row['category'];
				$itemname=$row['name'];

			tpdb_free_result($request);
			$request = tp_query("SELECT realName FROM " . $db_prefix . "members WHERE ID_MEMBER=$authorID LIMIT 1", __FILE__, __LINE__);
			if(tpdb_num_rows($request)>0){
				$row=tpdb_fetch_assoc($request);
				$context['TPortal']['admcurrent']['member']=$row['realName'];
				tpdb_free_result($request);
			}
			else
				$context['TPortal']['admcurrent']['member']='-guest-';
		}
		// check to see if its child
		$parents=array();
		while($catparent>0 ){
			$parents[$catparent] = array(
				'id' => $catparent,
				'name' => $context['TPortal']['linkcats'][$catparent]['name'],
				'parent' => $context['TPortal']['linkcats'][$catparent]['parent']
				);
			$catparent = $context['TPortal']['linkcats'][$catparent]['parent'];
		}

		// make the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=admin', $txt['tp-dldownloads']);

		if(isset($parents)){
			$parts=array_reverse($parents,TRUE);
			// add to the linktree
			foreach($parts as $parent){
				TPadd_linktree($scripturl.'?action=tpmod;dl=admincat'.$parent['id'] , $parent['name']);
			}
		}
		// add to the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=adminitem'.$item , $itemname);
	}
	loadtemplate('TPdladmin');
	loadlanguage('TPmodules');
	loadlanguage('TPortalAdmin');
	adminIndex('tportal');

		// setup admin tabs according to subaction
			$context['admin_area']= 'tp_dlmanager';
			$context['admin_tabs'] = array(
				'title' => $txt['tp-dlheader1'],
				'help' => $txt['tp-dlheader2'],
				'description' => $txt['tp-dlheader3'],
				'tabs' => array(),
				);
			if (allowedTo('tp_dlmanager'))
			{
				$context['admin_tabs']['tabs'] = array(
					'admin' => array(
					'title' => $txt['tp-dltabs4'],
					'description' => '',
					'href' => $scripturl . '?action=tpmod;dl=admin',
					'is_selected' => substr($context['TPortal']['dlsub'],0,5)=='admin' && $context['TPortal']['dlsub']!='adminsettings' && $context['TPortal']['dlsub']!='adminaddcat' && $context['TPortal']['dlsub']!='adminftp' && $context['TPortal']['dlsub']!='adminsubmission',
					),
					'settings' => array(
					'title' => $txt['tp-dltabs1'],
					'description' => '',
					'href' => $scripturl . '?action=tpmod;dl=adminsettings',
					'is_selected' => $context['TPortal']['dlsub']=='adminsettings',
					),
					'addcategory' => array(
					'title' => $txt['tp-dltabs2'],
					'description' => '',
					'href' => $scripturl . '?action=tpmod;dl=adminaddcat',
					'is_selected' => $context['TPortal']['dlsub']=='adminaddcat',
					),
					'upload' => array(
					'title' => $txt['tp-dltabs3'],
					'description' => '',
					'href' => $scripturl . '?action=tpmod;dl=upload',
					'is_selected' => $context['TPortal']['dlsub'] == 'upload',
					),
					'submissions' => array(
					'title' => $txt['tp-dlsubmissions'].' ' , $context['TPortal']['submitcheck']['uploads']>0 ? '('.$context['TPortal']['submitcheck']['uploads'].')' : '' ,
					'description' => '',
					'href' => $scripturl . '?action=tpmod;dl=adminsubmission',
					'is_selected' => $context['TPortal']['dlsub'] == 'adminsubmission',
					),
					'ftp' => array(
					'title' => $txt['tp-dlftp'],
					'description' => '',
					'href' => $scripturl . '?action=tpmod;dl=adminftp',
					'is_selected' => $context['TPortal']['dlsub'] == 'adminftp',
					),
				);
			}
}
// edit screen for regular users
function TPortalDLUser($item)
{
	global $txt, $scripturl, $db_prefix, $ID_MEMBER, $user_info, $sourcedir, $boarddir, $boardurl;
	global $modSettings, $context, $settings , $tp_prefix;

	$tp_prefix=$settings['tp_prefix'];

	// check that it is indeed yours
	$request = tp_query("SELECT * FROM " . $tp_prefix . "dlmanager WHERE id=$item AND type='dlitem' AND authorID=$ID_MEMBER LIMIT 1", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		// ok, it is. :)
		$row=tpdb_fetch_assoc($request);

		// is it actually a subitem?
		if($row['subitem']>0)
			redirectexit('action=tpmod;dl=useredit'.$row['subitem']);

		// get all items for a list but only your own
		$context['TPortal']['useritems']=array();
		$context['TPortal']['dl_useredit']=array();
		$itemlist = tp_query("SELECT id,name FROM " . $tp_prefix . "dlmanager WHERE id!=$item AND authorID=$ID_MEMBER AND type = 'dlitem' AND subitem=0 ORDER BY name ASC", __FILE__, __LINE__);
		if(tpdb_num_rows($itemlist)>0)
		{
			while($ilist=tpdb_fetch_assoc($itemlist))
			{
				$context['TPortal']['useritems'][] = array(
					'id' => $ilist['id'],
					'name' => $ilist['name'],
					);
			}
		}

		// Any additional files then..?
		$subitem = $row['id'];
		$fdata=array();
		$fetch = tp_query("SELECT id, name, file, downloads, filesize
							FROM " . $tp_prefix . "dlmanager
							WHERE type = 'dlitem'
							AND subitem=$subitem", __FILE__, __LINE__);
					
		if(tpdb_num_rows($fetch)>0)
		{
			while($frow = tpdb_fetch_assoc($fetch))
			{
				if($context['TPortal']['dl_fileprefix']=='K')
					$ffs=ceil($row['filesize']/1000).' Kb';
				elseif($context['TPortal']['dl_fileprefix']=='M')
					$ffs=(ceil($row['filesize']/1000)/1000).' Mb';
				elseif($context['TPortal']['dl_fileprefix']=='G')
					$ffs=(ceil($row['filesize']/1000000)/1000).' Gb';
								
				$fdata[] = array(
					'id' => $frow['id'],
					'name' => $frow['name'],
					'file' => $frow['file'],
					'href' => $scripturl.'?action=tpmod;dl=item'.$frow['id'],
					'downloads' => $frow['downloads'],
					'created' => $frow['created'],
					'filesize' => $ffs,
					);
			}
			tpdb_free_result($fetch);
		}

		
		$context['TPortal']['dl_useredit'][] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'icon' => $row['icon'],
				'category' => $row['category'],
				'file' => $row['file'],
				'views' => $row['views'],
				'authorID' => $row['authorID'],
				'description' => html_entity_decode($row['description'], ENT_QUOTES, $context['character_set']),
				'created' => timeformat($row['created']),
				'last_access' => timeformat($row['last_access']),
				'filesize' => (substr($row['file'],14)!='- empty item -') ? floor(filesize($boarddir.'/tp-downloads/'.$row['file'])/1024) : '0' ,
				'downloads' => $row['downloads'],
				'sshot' => $row['screenshot'],
				'link' => $row['link'],
				'href' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
				'approved' => $row['category'] < 0 ? '0' : '1' ,
				'approve' => $scripturl.'?action=tpmod;dl=adminitem'.$row['id'],
				'subitem' => $fdata,
				);
		$authorID=$row['authorID'];
		$catparent=$row['category'];
		$itemname=$row['name'];

		tpdb_free_result($request);
		$request = tp_query("SELECT realName FROM " . $db_prefix . "members WHERE ID_MEMBER=$authorID LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$row=tpdb_fetch_assoc($request);
			$context['TPortal']['admcurrent']['member']=$row['realName'];
			tpdb_free_result($request);
		}
		else
			$context['TPortal']['admcurrent']['member']='-guest-';
		// add to the linktree
		TPadd_linktree($scripturl.'?action=tpmod;dl=useredit'.$item , $txt['tp-useredit'].': '.$itemname);
		$context['TPortal']['dlaction']='useredit';
		// fetch allowed categories
		TP_dluploadcats();
		// get the icons
		TP_dlgeticons();
		loadtemplate('TPdlmanager');
		loadlanguage('TPmodules');
		loadlanguage('TPortalAdmin');
	}
	else
		redirectexit('action=tpmod;dl');
}

function dlupdatefilecount($category, $total=true)
{
	global $scripturl, $db_prefix, $user_info, $context, $settings , $tp_prefix;

	$tp_prefix=$settings['tp_prefix'];

	// get all files in its own category first
	$request = tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "dlmanager WHERE category=$category AND type='dlitem'", __FILE__, __LINE__);
	$result=tpdb_fetch_row($request); $r=$result[0];
	$request = tp_query("UPDATE " . $tp_prefix . "dlmanager SET files=$r WHERE id=$category", __FILE__, __LINE__);

}

function dlsort($a, $b) {
	   return strnatcasecmp($b["items"], $a["items"]);
}
function dlsortviews($a, $b) {
	   return strnatcasecmp($b["views"], $a["views"]);
}
function dlsortsize($a, $b) {
	   return strnatcasecmp($b["size"], $a["size"]);
}
function dlsortdownloads($a, $b) {
	   return strnatcasecmp($b["downloads"], $a["downloads"]);
}

function TPDLgetname($oldname)
{
	if(strlen($oldname)>13 && is_numeric(substr($oldname,0,10))){
		$newname = substr($oldname,10);
	}
	else
		$newname=$oldname;

	return $newname;
 }
 function TP_dluploadcats()
 {
	global $scripturl, $db_prefix, $user_info, $context, $settings , $tp_prefix;

	$tp_prefix=$settings['tp_prefix'];

	//first : fetch all allowed categories
	$sorted = array();
	$request = tp_query("SELECT id, parent, name, access FROM " . $tp_prefix . "dlmanager WHERE type = 'dlcat'", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0){
		while ($row = tpdb_fetch_assoc($request))
		{
			$show = get_perm($row['access'], 'tp_dlmanager');
			if($show)
				$sorted[$row['id']] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'parent' => $row['parent'],
					'href' => $scripturl . '?action=tpmod;dl=cat'. $row['id'],
					);
		}
		tpdb_free_result($request);
	}
	$context['TPortal']['cats']=array();
	// sort them
	if(count($sorted)>1){
		$context['TPortal']['cats']=$sorted;
		$context['TPortal']['uploadcats'] = chain('id', 'parent', 'name', $sorted);
		$context['TPortal']['uploadcats2'] = chain('name', 'parent', 'id', $sorted);
	}
	else{
		$context['TPortal']['uploadcats'] = $sorted;
		$context['TPortal']['uploadcats2'] = $sorted;
		$context['TPortal']['cats']=$sorted;
	}
}

function TP_dlgeticons()
{
	global $context,$boarddir;

	// fetch icons, just read the directory
	$context['TPortal']['dlicons'] = array();
	if ($handle = opendir($boarddir.'/tp-downloads/icons')) 
	{
		while (false !== ($file = readdir($handle))) 
		{
			if($file!= '.' && $file!='..' && $file!='.htaccess' && in_array(substr($file,(strlen($file)-4),4), array('.jpg','.gif','.png')) )
				$context['TPortal']['dlicons'][] = $file;
		}
		closedir($handle);
		sort($context['TPortal']['dlicons']);
	}
}
 function TP_dlftpfiles()
 {
	global $scripturl, $context, $settings, $boarddir;

	$count=1;
	$sorted=array();
	if ($handle = opendir($boarddir.'/tp-downloads')) {
		while (false !== ($file = readdir($handle))) {
			if($file!= '.' && $file!='..' && $file!='.htaccess' && $file!='icons'){
				$size=floor(filesize($boarddir.'/tp-downloads/'.$file)/1024);
				$sorted[$count] = array(
						'id' => $count,
						'file' => $file,
						'size' => $size,
						);
				$count++;
			}
		}
		closedir($handle);
	}
	$context['TPortal']['tp-downloads'] = array();
	// sort them
	if(count($sorted)>1)
		$context['TPortal']['tp-downloads'] = chain('id', 'size', 'file', $sorted);
	else
		$context['TPortal']['tp-downloads'] = $sorted;
}

?>