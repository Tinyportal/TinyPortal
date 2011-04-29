<?php
/****************************************************************************
* TPmodules.php																*
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

// TinyPortal module entrance
function TPmodules()
{
	global $db_prefix, $settings, $modSettings, $context, $scripturl,$txt , $user_info , $sourcedir, $boardurl,$ID_MEMBER, $boarddir;

	$tp_prefix=$settings['tp_prefix'];

	if(loadlanguage('TPmodules')==false)
		loadlanguage('TPmodules', 'english');
	if(loadlanguage('TPortalAdmin')==false)
		loadlanguage('TPortalAdmin', 'english');

	// get subaction
	$tpsub='';
	if(isset($_GET['sa']))
	{
		$context['TPortal']['subaction'] = $_GET['sa'];
		$tpsub = $_GET['sa'];
	}
	elseif(isset($_GET['sub']))
	{
		$context['TPortal']['subaction']=$_GET['sub'];
		$tpsub=$_GET['sub'];
	}

	// for help pages
	if(isset($_GET['p']))
		$context['TPortal']['helpsection'] = $_GET['p'];
	else
		$context['TPortal']['helpsection'] = 'introduction';

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// call the editor setup
	TPwysiwyg_setup();
	require_once($sourcedir. '/TPcommon.php');

	// download manager?
	if(isset($_GET['dl']))
	{
		$context['TPortal']['dlsub']=$_GET['dl']=='' ? '0' : $_GET['dl'];
	}

	// fetch all extensions and compare
	$result =  tp_query("SELECT modulename,autoload_run, subquery FROM " . $tp_prefix . "modules WHERE active=1", __FILE__, __LINE__);
	if(tpdb_num_rows($result)>0)
	{
		while($row = tpdb_fetch_assoc($result))
		{
			if(isset($_GET[$row['subquery']]))
				$tpmodule=$boarddir .'/tp-files/tp-modules/' . $row['modulename']. '/Sources/'. $row['autoload_run'];
		}
		tpdb_free_result($result);
	}
	
	// clear the linktree first
	TPstrip_linktree();

	// include source files in case of modules
	if(isset($context['TPortal']['dlsub']))
	{
		require_once( $sourcedir .'/TPdlmanager.php');
		TPdlmanager_init();
	}
	elseif(!empty($tpmodule))
		require_once($tpmodule);
	// get xml code
	elseif(isset($_GET['getsnippets']))
		get_snippets_xml();
	// save the upshrink value
	elseif(isset($_GET['upshrink']) && isset($_GET['state']))
	{
		$blockid=$_GET['upshrink'];
		$state=$_GET['state'];
		if(isset($_COOKIE['tp-upshrinks']))
		{
			$shrinks=explode(",",$_COOKIE['tp-upshrinks']);
			if($state==0 && !in_array($blockid,$shrinks))
				$shrinks[]=$blockid;
			elseif($state==1 && in_array($blockid,$shrinks))
			{
				$spos=array_search($blockid,$shrinks);
				if($spos>-1)
					unset($shrinks[$spos]);
			}
			$newshrink = implode(",",$shrinks);
			setcookie ("tp-upshrinks", $newshrink , time()+7776000);
		}
		else
		{
			if($state==0)
			setcookie ("tp-upshrinks", $blockid, (time()+7776000));
		}
		// Don't output anything...
		$tid=time();
		redirectexit($settings['images_url'] . '/blank.gif?ti='.$tid);
	}
	// a comment is sent
	elseif($tpsub=='comment' && isset($_POST['tp_article_type']) && $_POST['tp_article_type']=='article_comment' )
	{
		// check the session
		checkSession('post');

		if ($user_info['is_guest'])
			fatal_error('guest not allowed');

		// Check whether the visual verification code was entered correctly.
		if ($context['TPortal']['articles_comment_captcha'] && (empty($_REQUEST['visual_verification_code']) || strtoupper($_REQUEST['visual_verification_code']) !== $_SESSION['visual_verification_code']))
		{
			$_SESSION['visual_errors'] = isset($_SESSION['visual_errors']) ? $_SESSION['visual_errors'] + 1 : 1;
			if ($_SESSION['visual_errors'] > 3 && isset($_SESSION['visual_verification_code']))
				unset($_SESSION['visual_verification_code']);

			fatal_lang_error('visual_verification_failed', false);
		}
		elseif (isset($_SESSION['visual_errors']))
			unset($_SESSION['visual_errors']);
		 
		$commenter = $context['user']['id'];
		$article = $_POST['tp_article_id'];

		// check if the article indeed exists
		$request =  tp_query("SELECT comments FROM " . $tp_prefix . "articles WHERE id=$article", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$row=tpdb_fetch_row($request);
			$num_comments=$row[0]+1;
			tpdb_free_result($request);
			$title = htmlentities(strip_tags($_POST['tp_article_comment_title']));
			$comment = substr($_POST['tp_article_bodytext'],0,65536);

			require_once($sourcedir.'/Subs-Post.php');
			preparsecode($comment);
			$time=time();

			// insert the comment
			tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5)
							VALUES('". $title. "','". $comment. "','$ID_MEMBER','article_comment','$time',$article)", __FILE__, __LINE__);
			// count and increase the number of comments
			tp_query("UPDATE " . $tp_prefix . "articles SET comments=$num_comments WHERE id=$article", __FILE__, __LINE__);
			// go back to the article
			redirectexit('page='.$article.'#tp-comment');
		}
	}
	elseif($tpsub=='updatelog')
	{
		$context['TPortal']['subaction']='updatelog';
		$request =  tp_query("SELECT value1	FROM " . $tp_prefix . "variables WHERE type = 'updatelog' ORDER BY id DESC", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$check = tpdb_fetch_assoc($request);
			$context['TPortal']['updatelog'] = $check['value1'];
			tpdb_free_result($request);
		}
		else
			$context['TPortal']['updatelog'] = $check['value1'];

		loadtemplate('TPmodules');
		$context['sub_template'] = 'updatelog';
	}
	elseif($tpsub=='showcomments')
	{
		if(!empty($_GET['tpstart']) && is_numeric($_GET['tpstart']))
			$tpstart=$_GET['tpstart'];
		else
			$tpstart=0;
		
		$mylast=0;
		$mylast=$user_info['last_login'];
		$showall=false;
		if(isset($_GET['showall']))
			$showall=true;

		$request =  tp_query("SELECT COUNT(var.value1)
		FROM (" . $tp_prefix . "variables as var, " . $tp_prefix . "articles as art)
		WHERE var.type = 'article_comment'
		" . ((!$showall || $mylast==0) ? 'AND var.value4>'.$mylast : '') ."
		AND art.id=var.value5", __FILE__, __LINE__);
		$check=tpdb_fetch_row($request);
		tpdb_free_result($request);
	
		$request =  tp_query("SELECT art.subject, memb.realName as author, art.authorID, var.value1, var.value3, var.value5, var.value4, mem.realName ,
		" . ($user_info['is_guest'] ? '1' : '(IFNULL(log.item, 0) >= var.value4)') . " AS isRead
		FROM (" . $tp_prefix . "variables as var, " . $tp_prefix . "articles as art)
		LEFT JOIN " . $db_prefix . "members as memb ON (art.authorID=memb.ID_MEMBER)
		LEFT JOIN " . $db_prefix . "members as mem ON (var.value3=mem.ID_MEMBER)
		LEFT JOIN " . $tp_prefix . "data as log ON (log.value=art.id AND log.type=1 AND log.ID_MEMBER=$ID_MEMBER)
		WHERE var.type = 'article_comment'
		AND art.id=var.value5
		" . ((!$showall || $mylast==0) ? 'AND var.value4>'.$mylast : '') ."
		ORDER BY var.value4 DESC LIMIT $tpstart,15", __FILE__, __LINE__);

		$context['TPortal']['artcomments']['new'] = array();
		
		if(tpdb_num_rows($request)>0)
		{
			while($row=tpdb_fetch_assoc($request))
				$context['TPortal']['artcomments']['new'][]=array(
				'page' => $row['value5'],	
				'subject' => $row['subject'],	
				'title' => $row['value1'],	
				'membername' => $row['realName'],	
				'time' => timeformat($row['value4']),	
				'author' => $row['author'],	
				'authorID' => $row['authorID'],	
				'member_id' => $row['value3'],	
				'is_read' => $row['isRead'],
				'replies' => $check[0],
				);	
			tpdb_free_result($request);
		}

		// construct the pages
		$context['TPortal']['pageindex'] = TPageIndex($scripturl.'?action=tpmod;sa=showcomments' , $tpstart , $check[0], 15);
		$context['TPortal']['unreadcomments']=true;
		$context['TPortal']['showall']= $showall;
		$context['TPortal']['subaction']='showcomments';
		TPadd_linktree($scripturl.'?action=tpmod;sa=showcomments' . ($showall ? ';showall' : '')  , $txt['tp-showcomments']);
		loadtemplate('TPmodules');
	}
	elseif($tpsub=='savesettings' )
	{
		// check the session
		checkSession('post');
		if(isset($_POST['item']))
			$item=$_POST['item'];
		else
			$item=0;
		
		if(isset($_POST['memberid']))
			$mem=$_POST['memberid'];
		else
			$mem=0;

		if(!isset($mem) || (isset($mem) && !is_numeric($mem)))
			fatalerror('Member doesn\'t exist.');

		foreach($_POST as $what => $value){
			if($what=='tpwysiwyg' && $item>0){
				 tp_query("UPDATE " . $tp_prefix . "data SET value=$value WHERE id=$item", __FILE__, __LINE__);
			}
			elseif($what=='tpwysiwyg' && $item==0)
				 tp_query("INSERT INTO " . $tp_prefix . "data (type,ID_MEMBER,value) VALUES(2, $mem, $value)", __FILE__, __LINE__);
		}
		// go back to profile page
		redirectexit('action=profile;u='.$mem.';sa=tparticles;settings');
	}
	// edit or deleting a comment?
	elseif((substr($tpsub,0,11)=='killcomment' || substr($tpsub,0,11)=='editcomment') && $context['user']['is_logged'])
	{
		// check that you indeed can edit or delete
		$comment=substr($tpsub,11);
		if(!is_numeric($comment))
			fatal_error('Not allowed.');

		$request =  tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE id=$comment LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$row=tpdb_fetch_assoc($request);
			tpdb_free_result($request);
			if(allowedTo('tp_articles') || $row['value3'] == $ID_MEMBER)
			{
				// deleting the comment
				if(substr($tpsub,0,11)=='killcomment')
				{
					tp_query("UPDATE " . $tp_prefix . "variables SET value5=-value5 WHERE id=$comment", __FILE__, __LINE__);
					redirectexit('page='.$row['value5']);
				}
				elseif(substr($tpsub,0,11)=='editcomment')
				{
					$context['TPortal']['comment_edit'] = array(
									'id' => $row['id'],
									'title' => $row['value1'],
									'body' => $row['value2'],
										);
					$context['TPortal']['subaction']='editcomment';
					loadtemplate('TPmodules');
				}
			}
			fatal_error($txt['tp-notallowed']);
		}
	}
	// rating is underway
	elseif($tpsub=='rate_article' && isset($_POST['tp_article_rating_submit']) && $_POST['tp_article_type']=='article_rating')
	{
		// check the session
		checkSession('post');

		$commenter = $context['user']['id'];
		$article = $_POST['tp_article_id'];
		// check if the article indeed exists
		$request =  tp_query("SELECT rating,voters FROM " . $tp_prefix . "articles WHERE id=$article", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$row=tpdb_fetch_row($request);
			tpdb_free_result($request);

			$voters=array(); $ratings=array();
			$voters=explode(",",$row[1]);
			$ratings=explode(",",$row[0]);
			// check if we haven't rated anyway
			if(!in_array($ID_MEMBER,$voters))
			{
				if($row[0]!='')
				{
					$new_voters=$row[1].','.$ID_MEMBER;
					$new_ratings=$row[0].','.$_POST['tp_article_rating'];
				}
				else
				{
					$new_voters=$ID_MEMBER;
					$new_ratings=$_POST['tp_article_rating'];
				}
				// update ratings and raters
				tp_query("UPDATE " . $tp_prefix . "articles SET rating='$new_ratings' WHERE id=$article", __FILE__, __LINE__);
				tp_query("UPDATE " . $tp_prefix . "articles SET voters='$new_voters' WHERE id=$article", __FILE__, __LINE__);
			}
			// go back to the article
			redirectexit('page='.$article);
		}
	}
	// rating from download manager
	elseif($tpsub=='rate_dlitem' && isset($_POST['tp_dlitem_rating_submit']) && $_POST['tp_dlitem_type']=='dlitem_rating')
	{
		// check the session
		checkSession('post');

		$commenter = $context['user']['id'];
		$dl = $_POST['tp_dlitem_id'];
		// check if the download indeed exists
		$request =  tp_query("SELECT rating,voters FROM " . $tp_prefix . "dlmanager WHERE id=$dl", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$row=tpdb_fetch_row($request);
			tpdb_free_result($request);

			$voters=array(); $ratings=array();
			$voters=explode(",",$row[1]);
			$ratings=explode(",",$row[0]);
			// check if we haven't rated anyway
			if(!in_array($ID_MEMBER,$voters))
			{
				if($row[0]!='')
				{
					$new_voters=$row[1].','.$ID_MEMBER;
					$new_ratings=$row[0].','.$_POST['tp_dlitem_rating'];
				}
				else
				{
					$new_voters=$ID_MEMBER;
					$new_ratings=$_POST['tp_dlitem_rating'];
				}
				// update ratings and raters
				 tp_query("UPDATE " . $tp_prefix . "dlmanager SET rating='$new_ratings' WHERE id=$dl", __FILE__, __LINE__);
				 tp_query("UPDATE " . $tp_prefix . "dlmanager SET voters='$new_voters' WHERE id=$dl", __FILE__, __LINE__);
			}
			// go back to the download
			redirectexit('action=tpmod;dl=item'.$dl);
		}
	}
	elseif($tpsub=='help')
	{
		$context['current_action']='help';
		require_once( $sourcedir .'/TPhelp.php');
		TPhelp_init();
	}
	// search from articles?
	elseif($tpsub=='searcharticle')
	{
		TPadd_linktree($scripturl.'?action=tpmod;sa=searcharticle' , $txt['tp-searcharticles2']);
		loadtemplate('TPmodules');
	}
	// search from articles?
	elseif($tpsub=='tpattach')
	{
		tpattach();
	}
	// search from articles?
	elseif($tpsub=='searcharticle2')
	{
		$start=0;
		checkSession('post');
		// any parameters then?
		// nothing to search for?
		if(empty($_POST['tpsearch_what']))
			fatal_error($txt['tp-nosearchentered']);
		// clean the search
		$what=strip_tags($_POST['tpsearch_what']);

		if(!empty($_POST['tpsearch_title']))
			$usetitle=true;
		else
			$usetitle=false;
		if(!empty($_POST['tpsearch_body']))
			$usebody=true;
		else
			$usebody=false;

		if($usetitle && !$usebody)
			$query = 'a.subject LIKE \'%' . $what . '%\'';
		elseif(!$usetitle && $usebody)
			$query = 'a.body LIKE \'%' . $what . '%\'';
		elseif($usetitle && $usebody)
			$query = 'a.subject LIKE \'%' . $what . '%\' OR a.body LIKE \'%' . $what . '%\'';
		else
			$query = 'a.subject LIKE \'%' . $what . '%\'';

		$context['TPortal']['searchresults']=array();
		$context['TPortal']['searchterm']=$what;
		$request= tp_query("SELECT a.id, a.date, a.views, a.subject, LEFT(a.body, 100) as body, a.authorID, a.type , m.realName
			FROM " . $tp_prefix . "articles AS a
			LEFT JOIN " . $db_prefix . "members as m ON a.authorID=m.ID_MEMBER
			WHERE $query
			AND a.off=0 
			ORDER BY a.date DESC LIMIT 20", __FILE__, __LINE__);
		
		if(tpdb_num_rows($request)>0)
		{
			while($row=tpdb_fetch_assoc($request))
			{
				if($row['type']=='bbc')
					$row['body']=doUBBC(html_entity_decode($row['body']));
				elseif($row['type']=='php')
					$row['body']='[PHP]';
				else
					$row['body']=strip_tags(html_entity_decode($row['body']));

				$row['subject'] = preg_replace('/'.$what.'/', '<span class="highlight">'.$what.'</span>', $row['subject']);
				$row['body'] = preg_replace('/'.$what.'/', '<span class="highlight">'.$what.'</span>', $row['body']);
				$context['TPortal']['searchresults'][]=array(
					'id' => $row['id'],
					'date' => $row['date'],
					'views' => $row['views'],
					'subject' => $row['subject'],
					'body' => $row['body'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
					);
			}
			tpdb_free_result($request);
		}
		TPadd_linktree($scripturl.'?action=tpmod;sa=searcharticle' , $txt['tp-searcharticles2']);
		loadtemplate('TPmodules');
	}
	// edit your own articles?
	elseif(substr($tpsub,0,11)=='editarticle')
	{
		$what=substr($tpsub,11);
		if(!is_numeric($what))
			fatal_error($txt['tp-notanarticle']);
	   
		// get one article
		$context['TPortal']['subaction']='editarticle';
		$context['TPortal']['editarticle']=array();
		$request =  tp_query("SELECT * FROM " . $tp_prefix . "articles WHERE id=$what LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request))
		{
			$row = tpdb_fetch_assoc($request);
			// check permission
			if(!allowedTo('tp_articles') && $ID_MEMBER!=$row['authorID'])
				fatal_error($txt['tp-articlenotallowed']);
			// can you edit your own then..?
			isAllowedTo('tp_editownarticle');
			$context['TPortal']['editarticle'] = array(
				'id' => $row['id'],
				'date' => array(
					'timestamp' => $row['date'],
					'day' => date("j",$row['date']),
					'month' => date("m",$row['date']),
					'year' => date("Y",$row['date']),
					'hour' => date("G",$row['date']),
					'minute' => date("i",$row['date']),
					),
				'body' => html_entity_decode($row['body']),
				'intro' => html_entity_decode($row['intro']),
				'useintro' => $row['useintro'],
				'category' => $row['category'],
				'frontpage' => $row['frontpage'],
				'subject' => html_entity_decode($row['subject']),
				'authorID' => $row['authorID'],
				'author' => $row['author'],
				'frame' => !empty($row['frame']) ? $row['frame'] : 'theme',
				'approved' => $row['approved'],
				'off' => $row['off'],
				'options' => $row['options'],
				'ID_THEME' => $row['ID_THEME'],
				'shortname' => $row['shortname'],
				'sticky' => $row['sticky'],
				'locked' => $row['locked'],
				'fileimport' => $row['fileimport'],
				'topic' => $row['topic'],
				'illustration' => $row['illustration'],
				'headers' => $row['headers'],
				'articletype' => $row['type'],
			);
			tpdb_free_result($request);
		}
		else
			fatal_error($txt['tp-notanarticlefound']);
		
		if(loadlanguage('TPortalAdmin')==false)
			loadlanguage('TPortalAdmin', 'english');
		loadtemplate('TPmodules');
	}
	// edit your own articles?
	elseif($tpsub=='myarticles')
	{
		// not for guests
		if($context['user']['is_guest'])
			fatal_error($txt['tp-noarticlesfound']);

		// get all articles
		$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "articles 
		WHERE authorID= '". $context['user']['id'] . "'", __FILE__, __LINE__);
		$row = tpdb_fetch_row($request);
		$allmy = $row[0];

		$mystart = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 0;
		// sorting?
		$sort = $context['TPortal']['sort'] = (!empty($_GET['sort']) && in_array($_GET['sort'],array('date','id','subject'))) ? $_GET['sort'] : 'date';
		$context['TPortal']['pageindex'] = TPageIndex($scripturl . '?action=tpmod;sa=myarticles;sort=' . $sort , $mystart, $allmy, 15);
		
		$context['TPortal']['subaction']='myarticles';
		$context['TPortal']['myarticles']=array();
		$request2 =  tp_query("SELECT id,subject,date,locked,approved,off FROM " . $tp_prefix . "articles 
		WHERE authorID= '". $context['user']['id'] ."' ORDER BY " . $sort . " DESC LIMIT ".$mystart.",15", __FILE__, __LINE__);

		if(tpdb_num_rows($request2)>0)
		{
			while($row = tpdb_fetch_assoc($request2))
				$context['TPortal']['myarticles'][] = $row;

			tpdb_free_result($request2);
		}
		
		if(loadlanguage('TPortalAdmin')==false)
			loadlanguage('TPortalAdmin', 'english');
		loadtemplate('TPmodules');
	}
	elseif(in_array($tpsub, array('submitarticle','addarticle_html','addarticle_bbc')))
	{
		global $sourcedir, $settings;

		require_once($sourcedir. '/TPcommon.php');

		// a BBC article?
		if(isset($_GET['bbc']) || $tpsub == 'addarticle_bbc')
		{
			isAllowedTo('tp_submitbbc');
			$context['TPortal']['submitbbc']=1;

		}
		else
			isAllowedTo('tp_submithtml');

		$context['TPortal']['subaction'] = 'submitarticle';
		loadtemplate('TPmodules');
		$context['sub_template'] = 'submitarticle';
	}
	elseif($tpsub == 'submitsuccess')
	{
		$context['TPortal']['subaction'] = 'submitsuccess';
		loadtemplate('TPmodules');
		$context['sub_template'] = 'submitsuccess';
	}
	elseif($tpsub == 'dlsubmitsuccess')
	{
		$context['TPortal']['subaction'] = 'dlsubmitsuccess';
		loadtemplate('TPmodules');
		$context['sub_template'] = 'dlsubmitsuccess';
	}
	// article
	elseif($tpsub=='submitarticle2')
	{
		require_once($sourcedir. '/TPcommon.php');
		
		if(isset($_POST['tp_article_approved']) || allowedTo('tp_alwaysapproved'))
			$artpp='0';
		else
			$artpp='1';

		$arttype = isset($_POST['submittedarticle']) ? $_POST['submittedarticle'] : '';
		$arts=strip_tags($_POST['tp_article_title']);
		$artd=$_POST['tp_article_date'];
		$artimp=isset($_POST['tp_article_fileimport']) ? $_POST['tp_article_fileimport'] : '';
		$artbb=$_POST['tp_article_body'];
		$artu=isset($_POST['tp_article_useintro']) ? $_POST['tp_article_useintro'] : 0;
		$arti=isset($_POST['tp_article_intro']) ? $_POST['tp_article_intro'] : '';
		$artc=$_POST['tp_article_category'];
		$artf=$_POST['tp_article_frontpage'];
		$artframe='theme';
		$artoptions = 'date,title,author,linktree,top,cblock,rblock,lblock,tblock,lbblock,views,rating,ratingallow,avatar';
		$name=$user_info['name'];
		$nameb=$ID_MEMBER;
		if($arts=='')
			$arts=$txt['tp-no_title'];
		// escape any php code
		if($artu==-1 && !get_magic_quotes_gpc())
			$artbb = addslashes($artbb);

        $request = tp_query("INSERT INTO " . $tp_prefix . "articles (date,body,intro,useintro,category,frontpage,subject,authorID,author,frame,approved,off,options,parse,comments,comments_var,views,rating,voters,ID_THEME,shortname,fileimport,type)
			VALUES('$artd','$artbb','$arti','$artu','$artc','$artf','$arts','$nameb','$name','$artframe','$artpp','0','$artoptions',0,0,'',0,'','',0,'','$artimp', '$arttype')", __FILE__, __LINE__);

		$newitem = tpdb_insert_id($request);
		// put this into submissions - id and type
		$title=$arts;
		$now=$artd;
		if($artpp=='0')
			 tp_query("INSERT INTO " . $tp_prefix . "variables (value1,value2,value3,type,value4,value5 ) VALUES ('$title', '$now', '','art_not_approved', '' , $newitem)", __FILE__, __LINE__);

		if(isset($_POST['pre_approved']))
			redirectexit('action=tpmod;sa=addsuccess');

		if(allowedTo('tp_editownarticle') && !allowedTo('tp_articles'))
		{
			// did we get a picture as well?
			if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
			{
				$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
			}
			redirectexit('action=tpmod;sa=editarticle'.$newitem);
		}
		elseif(allowedTo('tp_articles'))
		{
			// did we get a picture as well?
			if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
			{
				$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
				tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
			}
			redirectexit('action=tpadmin;sa=editarticle'.$newitem);
		}
		else
			redirectexit('action=tpmod;sa=submitsuccess');
	}
	// edit a block?
	elseif(substr($tpsub,0,9)=='editblock')
	{
		$what=substr($tpsub,9);
		if(!is_numeric($what))
			fatal_error($txt['tp-notablock']);
		// get one block
		$context['TPortal']['subaction']='editblock';
		$context['TPortal']['blockedit']=array();
		$request =  tp_query("SELECT * FROM " . $tp_prefix . "blocks WHERE id=". $what. " LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$row = tpdb_fetch_assoc($request);

			$can_edit = !empty($row['editgroups']) ? get_perm($row['editgroups'],'') : false;
			// check permission
			if(allowedTo('tp_blocks') || $can_edit)
				$ok=true;
			else
				fatal_error($txt['tp-blocknotallowed']);

			$context['TPortal']['editblock'] = array();
			$context['TPortal']['blockedit']['id']=$row['id'];
			$context['TPortal']['blockedit']['title']=$row['title'];
			$context['TPortal']['blockedit']['body']=$row['body'];
			$context['TPortal']['blockedit']['frame']=$row['frame'];
			$context['TPortal']['blockedit']['type']=$row['type'];
			$context['TPortal']['blockedit']['var1']=$row['var1'];
			$context['TPortal']['blockedit']['var2']=$row['var2'];
			$context['TPortal']['blockedit']['visible']=$row['visible'];
			$context['TPortal']['blockedit']['editgroups']=$row['editgroups'];
			tpdb_free_result($request);
		}
		else
			fatal_error($txt['tp-notablock']);

		if(loadlanguage('TPortalAdmin')==false)
			loadlanguage('TPortalAdmin', 'english');
		loadtemplate('TPmodules');
	}
	// promoting topics
	elseif($tpsub=='publish')
	{
		if(!isset($_GET['t']))
			redirectexit('action=forum');
		
		$t = is_numeric($_GET['t']) ? $_GET['t'] : 0;

		if(empty($t))
			redirectexit('action=forum');

		isAllowedTo('tp_settings');		
		$existing = explode(",",$context['TPortal']['frontpage_topics']);
		if(in_array($t,$existing))
			unset($existing[array_search($t, $existing)]);
		else
			$existing[] = $t;

		$newstring = implode(",",$existing);
		if(substr($newstring,0,1)==',')
			$newstring = substr($newstring,1);

		tp_query("UPDATE " . $tp_prefix . "settings SET value = '" . $newstring . "' WHERE name='frontpage_topics'", __FILE__, __LINE__);
		redirectexit('topic='. $t . '.0');
	}
	// save a block?
	elseif(substr($tpsub,0,9)=='saveblock')
	{
		$whatID=substr($tpsub,9);
		if(!is_numeric($whatID))
			fatal_error($txt['tp-notablock']);
		$request =  tp_query("SELECT editgroups FROM " . $tp_prefix . "blocks WHERE id=$whatID LIMIT 1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			$row = tpdb_fetch_assoc($request);
			// check permission
			if(allowedTo('tp_blocks') || get_perm($row['editgroups']))
				$ok=true;
			else
				fatal_error($txt['tp-blocknotallowed']);
			tpdb_free_result($request);
			
			// loop through the values and save them
			foreach ($_POST as $what => $value) 
			{
				if(substr($what,0,10)=='blocktitle')
				{
					// make sure special charachters can't be done
					$value=strip_tags($value);
					$value = preg_replace('~&#\d+$~', '', $value);
					$val=substr($what,10);
					tp_query("UPDATE " . $tp_prefix . "blocks SET title='$value' WHERE id=$val", __FILE__, __LINE__);
				}
				elseif(substr($what,0,9)=='blockbody')
				{
					$val=substr($what,9);
					tp_query("UPDATE " . $tp_prefix . "blocks SET body='$value' WHERE id=$val", __FILE__, __LINE__);
				}
				elseif(substr($what,0,10)=='blockframe')
				{
					$val=substr($what,10);
					tp_query("UPDATE " . $tp_prefix . "blocks SET frame='$value' WHERE id=$val", __FILE__, __LINE__);
				}
				elseif(substr($what,0,12)=='blockvisible')
				{
					$val=substr($what,12);
					tp_query("UPDATE " . $tp_prefix . "blocks SET visible='$value' WHERE id=$val", __FILE__, __LINE__);
				}
				elseif(substr($what,0,9)=='blockvar1')
				{
					$val=substr($what,9);
					tp_query("UPDATE " . $tp_prefix . "blocks SET var1='$value' WHERE id=$val", __FILE__, __LINE__);
				}
				elseif(substr($what,0,9)=='blockvar2')
				{
					$val=substr($what,9);
					tp_query("UPDATE " . $tp_prefix . "blocks SET var2='$value' WHERE id=$val", __FILE__, __LINE__);
				}
			}
			redirectexit('action=tpmod;sa=editblock'.$whatID);
		}
		else
			fatal_error($txt['tp-notablock']);
	}
	// save an article
	elseif($tpsub=='savearticle')
	{
		if(isset($_REQUEST['send']))
		{
			foreach ($_POST as $what => $value) 
			{
				if(substr($what,0,16)=='tp_article_title')
				{
					$val=substr($what,16);
					if(is_numeric($val) && $val>0)
						tp_query("UPDATE " . $tp_prefix . "articles SET subject='$value' WHERE id=$val", __FILE__, __LINE__);
				}
				elseif(substr($what,0,15)=='tp_article_body')
				{
					$val=substr($what,15);
					if(is_numeric($val) && $val>0)
					    tp_query("UPDATE " . $tp_prefix . "articles SET body='$value' WHERE id=$val", __FILE__, __LINE__);
				}
				elseif(substr($what,0,19)=='tp_article_useintro')
				{
					$val=substr($what,19);
					if(is_numeric($val) && $val>0)
					    tp_query("UPDATE " . $tp_prefix . "articles SET useintro='$value' WHERE id=$val", __FILE__, __LINE__);
				}
				elseif(substr($what,0,16)=='tp_article_intro')
				{
				   $val=substr($what,16);
				    tp_query("UPDATE " . $tp_prefix . "articles SET intro='$value' WHERE id=$val", __FILE__, __LINE__);
				}
				elseif($what=='tp_wysiwyg')
				{
					$result =  tp_query("SELECT id FROM " . $tp_prefix . "data WHERE type=2 AND ID_MEMBER=$ID_MEMBER", __FILE__, __LINE__);
					if(tpdb_num_rows($result)>0)
					{
						$row=tpdb_fetch_assoc($result);
						$wysid=$row['id'];
						tpdb_free_result($result);
					}
					if(isset($wysid))
						tp_query("UPDATE " . $tp_prefix . "data SET value=$value WHERE id=$wysid", __FILE__, __LINE__);
					else
						tp_query("INSERT INTO " . $tp_prefix . "data (type,ID_MEMBER,value,item) VALUES(2, $ID_MEMBER, $value, 0)", __FILE__, __LINE__);
				}
			}
			if(allowedTo('tp_editownarticle') && !allowedTo('tp_articles'))
			{
				// did we get a picture as well?
				if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
				{
					$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
					tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
				}
				redirectexit('action=tpmod;sa=editarticle'.$val);
			}
			elseif(allowedTo('tp_articles'))
			{
				// did we get a picture as well?
				if(isset($_FILES['qup_tp_article_body']) && file_exists($_FILES['qup_tp_article_body']['tmp_name']))
				{
					$name = TPuploadpicture('qup_tp_article_body', $context['user']['id'].'uid');
					tp_createthumb('tp-images/'. $name, 50, 50, 'tp-images/thumbs/thumb_'. $name);
				}
				redirectexit('action=tpadmin;sa=editarticle'.$val);
			}
			else
				fatal_error($txt['tp-notallowed']);
		}
	}
	else
			redirectexit('action=forum');
}

// profile summary
function tp_profile_summary($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix , $settings, $scripturl , $ID_MEMBER;

	$context['page_title'] = $txt['tpsummary'];
	$tp_prefix=$settings['tp_prefix'];
	$max_art=0;
	// get all articles written by member
	$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "articles
									WHERE authorID=$memID", __FILE__, __LINE__);
	$result=tpdb_fetch_row($request);
	$max_art=$result[0];
	tpdb_free_result($request);

	$max_upload=0;
	if($context['TPortal']['show_download'])
	{
		// get all uploads
		$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "dlmanager WHERE authorID=$memID AND type='dlitem'", __FILE__, __LINE__);
		$result=tpdb_fetch_row($request);
		$max_upload=$result[0];
		tpdb_free_result($request);
	}
	$context['TPortal']['tpsummary']=array(
		'articles' => $max_art,
		'uploads' => $max_upload,
	);
 }
// articles and comments made by the member
function tp_profile_articles($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix , $settings, $scripturl;

	$context['page_title'] = $txt['articlesprofile'];
	$tp_prefix=$settings['tp_prefix'];

	if(isset($context['TPortal']['mystart']))
		$start = is_numeric($context['TPortal']['mystart']) ? $context['TPortal']['mystart'] : 0;
	else
		$start=0;

	$context['TPortal']['memID']=$memID;

	if($context['TPortal']['tpsort']!='')
		$sorting= $context['TPortal']['tpsort'];
	else
		$sorting= 'date';

	$max=0;
	// get all articles written by member
	$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "articles WHERE authorID=$memID", __FILE__, __LINE__);
	$result=tpdb_fetch_row($request);
	$max=$result[0];
	tpdb_free_result($request);

	// get all not approved articles
	$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "articles WHERE authorID=$memID AND approved=0", __FILE__, __LINE__);
	$result=tpdb_fetch_row($request);
	$max_approve=$result[0];
	tpdb_free_result($request);

	// get all articles currently being off
	$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "articles WHERE authorID=$memID AND off=1", __FILE__, __LINE__);
	$result=tpdb_fetch_row($request);
	$max_off=$result[0];
	tpdb_free_result($request);

	$context['TPortal']['all_articles']=$max;
	$context['TPortal']['approved_articles']=$max_approve;
	$context['TPortal']['off_articles']=$max_off;

	if(!in_array($sorting, array('date','subject','views','category','comments')))
		$sorting='date';

	$request =  tp_query("SELECT art.id, art.date, art.subject, art.approved, art.off, art.comments, art.views, art.rating, art.voters, art.authorID, art.category
	,art.locked	FROM " . $tp_prefix . "articles AS art
		WHERE art.authorID=$memID
		ORDER BY art." . $sorting . " DESC LIMIT $start,10", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0){
		while($row=tpdb_fetch_assoc($request))
		{
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
			
			$can_see = true;

			if(($row['approved']!=1 || $row['off']==1) && !isAllowedTo('tp_articles'))
				$can_see=false;

			if($can_see)
				$context['TPortal']['profile_articles'][] = array(
					'id' => $row['id'],
					'subject' => $row['subject'],
					'date' => timeformat($row['date']),
					'timestamp' => $row['date'],
					'href' => '' . $scripturl . '?page='.$row['id'],
					'comments' => $row['comments'],
					'views' => $row['views'],
					'rating_votes' => $rating_votes,
					'rating_average' => $rating_average,
					'approved' => $row['approved'],
					'off' => $row['off'],
					'locked' => $row['locked'],
					'catID' => $row['category'],
					'category' => '<a href="'.$scripturl.'?mycat='.$row['category'].'">' . (isset($context['TPortal']['catnames'][$row['category']]) ? $context['TPortal']['catnames'][$row['category']] : '') .'</a>',
					'editlink' => allowedTo('tp_articles') ? $scripturl.'?action=tpadmin;sa=editarticle'.$row['id'] : $scripturl.'?action=tpmod;sa=editarticle'.$row['id'],
				);
		}
		tpdb_free_result($request);
	}
	// construct pageindexes
	if($max>0)
		$context['TPortal']['pageindex']=TPageIndex($scripturl.'?action=profile;sa=tparticles;u='.$memID.';tpsort='.$sorting, $start, $max, '10');
	else
		$context['TPortal']['pageindex']='';

	// setup subaction
	$context['TPortal']['profile_action'] = '';
	if(isset($_GET['sa']) && $_GET['sa']=='settings')
		$context['TPortal']['profile_action'] = 'settings';

	// setup values for personal settings - for now only editor choice
	// type = 1 - 
	// type = 2 - editor choice

	$result =  tp_query("SELECT id, value FROM " . $tp_prefix . "data WHERE type=2 AND ID_MEMBER=$memID LIMIT 1", __FILE__, __LINE__);
	if(tpdb_num_rows($result)>0)
	{
		$row=tpdb_fetch_assoc($result);
		$context['TPortal']['selected_member_choice'] = $row['value'];
		$context['TPortal']['selected_member_choice_id'] = $row['id'];
		tpdb_free_result($result);
	}
	else
	{
		$context['TPortal']['selected_member_choice'] = $context['TPortal']['use_wysiwyg'];
		$context['TPortal']['selected_member_choice_id'] = 0;
	}
	$context['TPortal']['selected_member'] = $memID;
	if(loadlanguage('TPortalAdmin')==false)
		loadlanguage('TPortalAdmin', 'english');
}

function tp_profile_download($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix , $settings, $scripturl;

	$context['page_title'] = $txt['downloadprofile'] ;

	// is dl manager on?
	if($context['TPortal']['show_download']==0)
		fatal_error($txt['tp-dlmanageroff']);

	$tp_prefix=$settings['tp_prefix'];

	if(isset($context['TPortal']['mystart']))
		$start=$context['TPortal']['mystart'];
	else
		$start=0;

	$context['TPortal']['memID']=$memID;

	if($context['TPortal']['tpsort']!='')
		$sorting= $context['TPortal']['tpsort'];
	else
		$sorting= 'date';

	$max=0;
	// get all uploads
	$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "dlmanager WHERE authorID=$memID AND type='dlitem'", __FILE__, __LINE__);
	$result=tpdb_fetch_row($request);
	$max=$result[0];
	tpdb_free_result($request);

	// get all not approved uploads
	$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "dlmanager WHERE authorID=$memID AND type='dlitem' AND category<0", __FILE__, __LINE__);
	$result=tpdb_fetch_row($request);
	$max_approve=$result[0];
	tpdb_free_result($request);

	$context['TPortal']['all_downloads']=$max;
	$context['TPortal']['approved_downloads']=$max_approve;
	$context['TPortal']['profile_uploads']=array();
	if(!in_array($sorting, array('name','created','views','downloads','category')))
		$sorting='created';

	$request =  tp_query("SELECT id, name, category, downloads, views, created, filesize, rating, voters
				FROM " . $tp_prefix . "dlmanager
				WHERE authorID=$memID
				AND type='dlitem'
				ORDER BY " . $sorting . " DESC LIMIT $start,10", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
		{
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

			$editlink = '';
			if(allowedTo('tp_dlmanager'))
				$editlink=$scripturl.'?action=tpmod;dl=adminitem'.$row['id'];
			elseif($memID==$context['user']['id'])
				$editlink=$scripturl.'?action=tpmod;dl=useredit'.$row['id'];

			$context['TPortal']['profile_uploads'][] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'created' => timeformat($row['created']),
				'category' => $row['category'],
				'href' => $scripturl . '?action=tpmod;dl=item'.$row['id'],
				'views' => $row['views'],
				'rating_votes' => $rating_votes,
				'rating_average' => $rating_average,
				'approved' => $row['category']>0 ? '1' : '0',
				'downloads' => $row['downloads'],
				'catID' => abs($row['category']),
				'category' => $row['category'],
				'editlink' => $editlink,
			);
		}
		tpdb_free_result($request);
	}
	// construct pageindexes
	if($max>0)
		$context['TPortal']['pageindex']=TPageIndex($scripturl.'?action=profile;sa=tpdownload;u='.$memID.';tpsort='.$sorting, $start, $max, '10');
	else
		$context['TPortal']['pageindex']='';
}

function tp_profile_gallery($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;
	$context['page_title'] = $txt['galleryprofile'] ;
}
function tp_profile_links($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;
	$context['page_title'] = $txt['linksprofile'] ;
}

function tp_pro_shoutbox()
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;
	$context['page_title'] = $txt['tp-shouts'];

}
// populate the profile
function tp_getprofileareas(&$data)
{
	global $txt;
	
	$data['tp'] = array(
			'title' => 'Tinyportal',
			'areas' => array(),
		);

	$data['tp']['areas']['tpsummary'] = array(
					'label' => $txt['tpsummary'],
					'file' => 'TPmodules.php',
					'function' => 'tp_summary',
					'permission' => array(
						'own' => 'profile_view_own',
						'any' => 'profile_view_any',
					),
				);

	$data['tp']['areas']['tparticles'] = array(
					'label' => $txt['articlesprofile'],
					'file' => 'TPmodules.php',
					'function' => 'tp_articles',
					'permission' => array(
						'own' => 'profile_view_own',
						'any' => 'profile_view_any',
					),
					'subsections' => array(
						'articles' => array($txt['tp-articles'], 'profile_view_any'),
						'settings' => array($txt['tp-settings'], 'profile_view_any'),
					),
				);

	$data['tp']['areas']['tpdownload'] = array(
					'label' => $txt['downloadprofile'],
					'file' => 'TPmodules.php',
					'function' => 'tp_download',
					'permission' => array(
						'own' => 'profile_view_own',
						'any' => 'profile_view_any',
					),
				);

	$data['tp']['areas']['tpshoutbox'] = array(
					'label' => $txt['shoutboxprofile'],
					'file' => 'TPmodules.php',
					'function' => 'tp_shoutb',
					'permission' => array(
						'own' => 'profile_view_own',
						'any' => 'profile_view_any',
					),
				);
}

// Tinyportal
function tp_summary($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['tpsummary'];
	tp_profile_summary($memID);
}
function tp_articles($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['articlesprofile'];
	tp_profile_articles($memID);
}
function tp_download($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['downloadprofile'];
	tp_profile_download($memID);
}
function tp_shoutb($memID)
{
	global $txt, $user_profile, $db_prefix, $context, $db_prefix;

	loadtemplate('TPprofile');
	$context['page_title'] = $txt['shoutboxprofile'];
	tpshout_profile($memID);
}

// fetch all the shouts for output
function tpshout_profile($memID)
{
    global $maintenance, $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $ID_MEMBER, $boarddir, $boardurl, $options, $sourcedir;

	$context['page_title'] = $txt['shoutboxprofile'] ;

	$tp_prefix=$settings['tp_prefix'];

	if(isset($context['TPortal']['mystart']))
		$start=$context['TPortal']['mystart'];
	else
		$start=0;

	$context['TPortal']['memID']=$memID;

	$sorting= 'value2';

	$max=0;
	// get all shouts
	$request =  tp_query("SELECT COUNT(*) FROM " . $tp_prefix . "shoutbox
									WHERE value5=$memID AND type='shoutbox'", __FILE__, __LINE__);
	$result=tpdb_fetch_row($request);
	$max=$result[0];
	tpdb_free_result($request);

	$context['TPortal']['all_shouts']=$max;
	$context['TPortal']['profile_shouts']=array();

	$request =  tp_query("SELECT *
									FROM " . $tp_prefix . "shoutbox
									WHERE value5=$memID
									AND type='shoutbox'
									ORDER BY " . $sorting . " DESC LIMIT $start,10", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0){
		while($row=tpdb_fetch_assoc($request)){

				$context['TPortal']['profile_shouts'][] = array(
						'id' => $row['id'],
						'shout' => parse_bbc(censorText($row['value1'])),
						'created' => timeformat($row['value2']),
						'ip' => $row['value4'],
						'editlink' => allowedTo('tp_shoutbox') ? $scripturl.'?action=tpmod;shout=admin;u='.$memID : '',
			);
		}
		tpdb_free_result($request);
	}
	// construct pageindexes
	if($max>0)
		$context['TPortal']['pageindex']=TPageIndex($scripturl.'?action=profile;sa=tpshoutbox;u='.$memID.';tpsort='.$sorting, $start, $max, '10', true);
	else
		$context['TPortal']['pageindex']='';
	

	loadtemplate('TPShout');
	loadlanguage('TPShout');

	$context['sub_template'] = 'tpshout_profile';
}
?>
