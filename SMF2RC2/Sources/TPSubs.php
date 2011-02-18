<?php
/******************************************************************************
* TPSubs.php                                                                   *
*******************************************************************************
* TP version: 1.0 RC1                                                                                                      *
* Software Version:           SMF 2.0                                                                                      *
* Software by:                Bloc (http://www.tinyportal.net)                                                      *
* Copyright 2005-2010 by:     Bloc (bloc@tinyportal.net)                                                         *
* Support, News, Updates at:  http://www.tinyportal.net                   *
*******************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

function TPcheckAdminAreas()
{
	global $db_prefix, $context, $scripturl, $txt, $settings;

	$return=false;

	TPcollectPermissions();
	foreach($context['TPortal']['adminlist'] as $adm => $val)
	{
		if(allowedTo($adm))
			$return = true;
	}
	return $return;
}

function TPsetupAdminAreas()
{
	global $db_prefix, $context, $scripturl, $txt, $settings;

	$tp_prefix = $db_prefix . 'tp_';
	// any from modules?
	$request =  tp_query("SELECT modulename,subquery,permissions,languages FROM " . $tp_prefix . "modules WHERE active=1", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		$context['admin_tabs']['custom_modules'] = array();
		if (allowedTo('tp_dlmanager'))
		{
			$context['admin_tabs']['custom_modules']['tpdownloads'] = array(
				'title' => 'TPdownloads',
					'description' => '',
					'href' => $scripturl . '?action=tpmod;dl=admin',
					'is_selected' => isset($_GET['dl']),
					);
			$admin_set=true;
		}
		while($row=tpdb_fetch_assoc($request))
		{
			$perms=explode(",", $row['permissions']);
			$setperm=array();
			$admin_set=false;
			for($a=0 ; $a < sizeof($perms) ; $a++)
			{
				$pr=explode("|",$perms[$a]);
				$setperm[$pr[0]]=$pr[1];
				// admin permission?
				if (isset($pr[1]) && $pr[1]==1)
				{
					if (allowedTo($pr[0]))
					{
						if(!$admin_set)
							$context['admin_tabs']['custom_modules'][$pr[0]] = array(
								'title' => $row['modulename'],
								'description' => '',
								'href' => $scripturl . '?action=tpmod;'.$row['subquery'].'=admin',
								'is_selected' => isset($_GET[$row['subquery']]) ? true : false,
								);
						$admin_set=true;
					}
				}
			}
		}
		tpdb_free_result($request);
	}
}

function TP_addPerms()
{
	global $db_prefix, $tp_prefix, $context, $scripturl, $txt, $settings;
	
	$admperms = array('admin_forum', 'manage_permissions', 'moderate_forum', 'manage_membergroups', 'manage_bans', 'send_mail', 'edit_news', 'manage_boards', 'manage_smileys', 'manage_attachments','tp_articles','tp_blocks','tp_dlmanager','tp_settings');

	$request =  tp_query("SELECT permissions FROM " . $tp_prefix . "modules WHERE active=1", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
		{
			$perms=explode(",", $row['permissions']);
			$setperm=array();
			for($a=0 ; $a < sizeof($perms) ; $a++)
			{
				$pr=explode("|",$perms[$a]);
				$setperm[$pr[0]]=$pr[1];
				// admin permission?
				if($pr[1]==1)
					$admperms[]=$pr[0];
			}
		}
		tpdb_free_result($request);
	}
	return $admperms;
}

function TPcollectPermissions()
{
	global $db_prefix, $context, $scripturl, $txt, $settings;

	// prefix of the TP tables
	$tp_prefix = $db_prefix.'tp_';

	$settings['tp_prefix'] = $tp_prefix;

		$context['TPortal']['permissonlist']=array();
		// first, the built-in permissions
		$context['TPortal']['permissonlist'][]=array(
				'title' => 'tinyportal' ,
				'perms' => array(
					'tp_settings' => 0,
					'tp_blocks' => 0,
					'tp_articles' => 0
					)
				);
		$context['TPortal']['permissonlist'][]=array(
				'title' => 'tinyportal_dl' ,
				'perms' => array(
					'tp_dlmanager' => 0,
					'tp_dlupload' => 0
					)
				);
		$context['TPortal']['permissonlist'][]=array(
				'title' => 'tinyportal_submit' ,
				'perms' => array(
					'tp_alwaysapproved' => 0,
					'tp_submithtml' => 0,
					'tp_submitbbc' => 0,
					'tp_editownarticle' => 0
					)
				);

	// for SMF2
	$context['TPortal']['tppermissonlist']=array(
					'tp_settings' => array(false, 'tinyportal', 'tinyportal'),
					'tp_blocks' => array(false, 'tinyportal', 'tinyportal'),
					'tp_articles' => array(false, 'tinyportal', 'tinyportal'),
					'tp_dlmanager' => array(false, 'tinyportal', 'tinyportal'),
					'tp_dlupload' => array(false, 'tinyportal', 'tinyportal'),
					'tp_alwaysapproved' => array(false, 'tinyportal', 'tinyportal'),
					'tp_submithtml' => array(false, 'tinyportal', 'tinyportal'),
					'tp_submitbbc' => array(false, 'tinyportal', 'tinyportal'),
					'tp_editownarticle' => array(false, 'tinyportal', 'tinyportal')
				);

		$context['TPortal']['adminlist']=array(
			'tp_settings' => 1,
			'tp_blocks' => 1,
			'tp_articles' => 1,
			'tp_dlmanager' => 1,
			'tp_submithtml' => 1,
			'tp_submitbbc' => 1,
		);
		// done, now onto custom modules
		$request =  tp_query("SELECT modulename,permissions,languages FROM " . $tp_prefix . "modules WHERE active=1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			while($row=tpdb_fetch_assoc($request))
			{
				$perms=explode(",", $row['permissions']);
				$setperm=array();
				for($a=0 ; $a < sizeof($perms) ; $a++)
				{
					$pr=explode("|",$perms[$a]);
					$setperm[$pr[0]]=0;
					// admin permission?
					if($pr[1]==1)
						$context['TPortal']['adminlist'][$pr[0]] = 1;				
				}
				$context['TPortal']['permissonlist'][]=array(
					'title' => strtolower($row['modulename']),
					'perms' => $setperm
					);

				$context['TPortal']['tppermissonlist'][$pr[0]] = array(false,strtolower($row['modulename']),strtolower($row['modulename']));
				if(loadLanguage($row['modulename'])==false)
					loadLanguage($row['modulename'], 'english');
			}
			tpdb_free_result($request);
		}
}

function tp_getbuttons()
{
	global $scripturl, $txt, $context;

	loadlanguage('TPortal');

	$buts = array();
	
	if(isset($context['TPortal']['show_download']) && $context['TPortal']['show_download'] == 1)
		$buts['downloads'] = array(
						'title' => $txt['tp-downloads'],
						'href' => $scripturl . '?action=tpmod;dl',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
						),			
					);


	if($context['user']['is_logged'])
		$buts['tpeditwonarticle'] = array(
						'title' => $txt['tp-myarticles'],
						'href' => $scripturl . '?action=tpmod;sa=myarticles',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
						),			
					);
	
	if(allowedTo('tp_submithtml'))
		$buts['tpeditwonarticle']['sub_buttons']['submithtml'] = array(
						'title' => $txt['tp-submitarticle'],
						'href' => $scripturl . '?action=tp' . (allowedTo('tp_articles') ? 'admin' : 'mod') . ';sa=addarticle_html',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
						),			
					);

	if(allowedTo('tp_submitbbc'))
		$buts['tpeditwonarticle']['sub_buttons']['submitbbc'] = array(
						'title' => $txt['tp-submitarticlebbc'],
						'href' => $scripturl . '?action=tp' . (allowedTo('tp_articles') ? 'admin' : 'mod') . ';sa=addarticle_bbc',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
						),			
					);

	

	// the admin fucntions
	if($context['user']['is_logged'])
		$buts['divde1'] = array(
						'title' => '<hr />',
						'href' => '#top',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
					),
			);
	if(allowedTo('tp_blocks'))
	{
		$buts['tpblocks'] = array(
						'title' => $txt['permissionname_tp_blocks'],
						'href' => $scripturl . '?action=tpadmin;sa=blocks',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
							'tppanels' => array(
								'title' => $txt['tp-panels'],
								'href' => $scripturl . '?action=tpadmin;sa=panels',
								'show' => true,
								'active_button' => false,
							),
							'tpmenumanager' => array(
								'title' => $txt['tp-menumanager'],
								'href' => $scripturl . '?action=tpadmin;sa=menubox',
								'show' => true,
								'active_button' => false,
							),
							'tpblockaccess' => array(
								'title' => $txt['tp-blockoverview'],
								'href' => $scripturl . '?action=tpadmin;sa=blocks;overview',
								'show' => true,
								'active_button' => false,
							),
						),			
					);
	}
	if(allowedTo('tp_settings'))
	{
		$buts['tpsettings'] = array(
						'title' => $txt['tp-settings'],
						'href' => $scripturl . '?action=tpadmin;sa=settings',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
							'tpfrontpage' => array(
								'title' => $txt['tp-frontpage'],
								'href' => $scripturl . '?action=tpadmin;sa=frontpage',
								'show' => true,
								'active_button' => false,
							),
							'tpmodules' => array(
								'title' => $txt['tp-modules'],
								'href' => $scripturl . '?action=tpadmin;sa=modules',
								'show' => true,
								'active_button' => false,
							),
							'tppermissions' => array(
								'title' => $txt['tp-permissions'],
								'href' => $scripturl . '?action=tpadmin;sa=permissions',
								'show' => true,
								'active_button' => false,
							),
						),			
					);
	}
	if(allowedTo('tp_articles'))
	{
		$buts['tparticles'] = array(
						'title' => $txt['permissionname_tp_articles'],
						'href' => $scripturl . '?action=tpadmin;sa=articles',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
							'tpcategories' => array(
								'title' => $txt['tp-categories'],
								'href' => $scripturl . '?action=tpadmin;sa=categories',
								'show' => true,
								'active_button' => false,
							),
							'tpstrays' => array(
								'title' => $txt['tp-strays'],
								'href' => $scripturl . '?action=tpadmin;sa=strays',
								'show' => true,
								'active_button' => false,
							),
							'tpsubmissions' => array(
								'title' => $txt['tp-submissions'],
								'href' => $scripturl . '?action=tpadmin;sa=submission',
								'show' => true,
								'active_button' => false,
							),
						),			
					);
	}
	if(allowedTo('tp_dlmanager'))
	{
		$buts['tpdlmanager'] = array(
						'title' => $txt['permissionname_tp_dlmanager'],
						'href' => $scripturl . '?action=tpmod;dl=admin',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
						),			
					);
	}
	if(allowedTo('tp_shoutbox'))
	{
		$buts['tpshoutbox'] = array(
						'title' => $txt['shoutboxprofile'],
						'href' => $scripturl . '?action=tpmod;shout=admin',
						'show' => true,
						'active_button' => false,
						'sub_buttons' => array(
						),			
					);
	}
	return $buts;
}

function TPcollectSnippets()
{
	global $boarddir;

	// fetch any blockcodes in blockcodes folder
	$codefiles = array();
	if ($handle = opendir($boarddir.'/tp-files/tp-blockcodes')) 
	{
		while (false !== ($file = readdir($handle))) 
		{
			if($file!= '.' && $file!='..' && $file!='.htaccess' && substr($file, (strlen($file)-10),10)=='.blockcode')
			{
				$snippet = TPparseModfile(file_get_contents($boarddir . '/tp-files/tp-blockcodes/' . $file) , array('name','author','version','date','description')); 
				$codefiles[] = array(
					'file' => substr($file, 0,strlen($file)-10),
					'name' => $snippet['name'],
					'author' => $snippet['author'],
					'text' => $snippet['description'],
					);
			}
		}
		closedir($handle);
	}
	return $codefiles;
}

function TPparseModfile($file , $returnarray)
{

	
	$file = strtr($file, array("\r" => ''));
	$snippet = array();

	while (preg_match('~<(name|code|parameter|author|version|date|description)>\n(.*?)\n</\\1>~is', $file, $code_match) != 0)
	{
		// get the title of this snippet
		if ($code_match[1] == 'name' && in_array('name',$returnarray))
			$snippet['name'] = $code_match[2];
		elseif ($code_match[1] == 'code' && in_array('code',$returnarray))
			$snippet['code'] = $code_match[2];
		elseif ($code_match[1] == 'parameter' && in_array('name',$returnarray))
			$snippet['parameter'][] = $code_match[2];
		elseif ($code_match[1] == 'author' && in_array('author',$returnarray))
			$snippet['author'] = $code_match[2];
		elseif ($code_match[1] == 'version' && in_array('version',$returnarray))
			$snippet['version'] = $code_match[2];
		elseif ($code_match[1] == 'date' && in_array('date',$returnarray))
			$snippet['date'] = $code_match[2];
		elseif ($code_match[1] == 'description' && in_array('description',$returnarray))
			$snippet['description'] = $code_match[2];

		// Get rid of the old tag.
		$file = substr_replace($file, '', strpos($file, $code_match[0]), strlen($code_match[0]));
	}
	return $snippet;
}


 function TP_article_categories($use_sorted=false)
 {
	global $smcFunc, $scripturl, $db_prefix, $user_info, $context, $settings , $tp_prefix, $txt;

	$tp_prefix=$settings['tp_prefix'];

	$context['TPortal']['caticons']=array();
	$context['TPortal']['catnames']=array();
	$context['TPortal']['categories_shortname']=array();

	//first : fetch all allowed categories
	$sorted = array();
	// for root category

	$sorted[9999] = array(
		'id' => 9999,
		'name' => '&laquo;' . $txt['tp-noname'] . '&raquo;',
		'parent' => '0',
		'access' => '-1,0,1',
		'indent' => 1,
	);
	$total=array();
	$request2 =  tp_query("SELECT category, COUNT(*) as files
	FROM " . $tp_prefix . "articles 
	WHERE category>0 GROUP BY category", __FILE__, __LINE__);
	if(tpdb_num_rows($request2)>0)
	{
		while($row=tpdb_fetch_assoc($request2))
		{
			$total[$row['category']]=$row['files'];
		}
		tpdb_free_result($request2);
	}
	$total2=array();
	$request2 =  tp_query("SELECT value2, COUNT(*) as siblings
	FROM " . $tp_prefix . "variables 
	WHERE type='category' GROUP BY value2", __FILE__, __LINE__);
	if(tpdb_num_rows($request2)>0)
	{
		while($row=tpdb_fetch_assoc($request2))
		{
			$total2[$row['value2']]=$row['siblings'];
		}
		tpdb_free_result($request2);
	}
	
	$request =  tp_query("SELECT cats.*
	FROM " . $tp_prefix . "variables as cats
	WHERE cats.type = 'category' 
	ORDER BY cats.value1 ASC", __FILE__, __LINE__);
	
	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
				// set the options up
				$options=array(
					'layout' => '1', 
					'width' => '100%', 
					'cols' => '1',
					'sort' => 'date',
					'sortorder' => 'desc',
					'showchild' => '1',
					'articlecount' => '5',
					'catlayout' => '1',
					'leftpanel' => '0',
					'rightpanel' => '0',
					'toppanel' => '0' ,
					'bottompanel' => '0' ,
					'upperpanel' => '0' ,
					'lowerpanel' => '0',
				);
				$opts=explode("|" , $row['value7']);
				foreach($opts as $op => $val)
				{
					if(substr($val,0,7) == 'layout=')
						$options['layout']=substr($val,7);
					elseif(substr($val,0,6) == 'width=')
						$options['width']=substr($val,6);
					elseif(substr($val,0,5) == 'cols=')
						$options['cols']=substr($val,5);
					elseif(substr($val,0,5) == 'sort=')
						$options['sort']=substr($val,5);
					elseif(substr($val,0,10) == 'sortorder=')
						$options['sortorder']=substr($val,10);
					elseif(substr($val,0,10) == 'showchild=')
						$options['showchild']=substr($val,10);
					elseif(substr($val,0,13) == 'articlecount=')
						$options['articlecount']=substr($val,13);
					elseif(substr($val,0,10) == 'catlayout=')
						$options['catlayout']=substr($val,10);
					elseif(substr($val,0,10) == 'leftpanel=')
						$options['leftpanel']=substr($val,10);
					elseif(substr($val,0,11) == 'rightpanel=')
						$options['rightpanel']=substr($val,11);
					elseif(substr($val,0,9) == 'toppanel=')
						$options['toppanel']=substr($val,9);
					elseif(substr($val,0,12) == 'bottompanel=')
						$options['bottompanel']=substr($val,12);
					elseif(substr($val,0,11) == 'upperpanel=')
						$options['centerpanel']=substr($val,11);
					elseif(substr($val,0,11) == 'lowerpanel=')
						$options['lowerpanel']=substr($val,11);
				}
				
				// check the parent
				if($row['value2']==$row['id'] || $row['value2']=='' || $row['value2']=='0')
					$row['value2']=9999;
				// check access
				$show=get_perm($row['value3']);
				if($show)
				{
					$sorted[$row['id']] = array(
						'id' => $row['id'],
						'shortname' => !empty($row['value8']) ? $row['value8'] : $row['id'],
						'name' => $row['value1'],
						'parent' => $row['value2'],
						'access' => $row['value3'],
						'icon' => $row['value4'],
						'totalfiles' => !empty($total[$row['id']][0]) ? $total[$row['id']][0] : 0,
						'children' => !empty($total2[$row['id']][0]) ? $total2[$row['id']][0] : 0,
						'options' => array(
							'layout' => $options['layout'], 
							'catlayout' => $options['catlayout'], 
							'width' => $options['width'], 
							'cols' => $options['cols'],
							'sort' => $options['sort'],
							'sortorder' => $options['sortorder'],
							'showchild' => $options['showchild'],
							'articlecount' => $options['articlecount'],
							'leftpanel' => $options['leftpanel'],
							'rightpanel' => $options['rightpanel'],
							'toppanel' => $options['toppanel'],
							'bottompanel' => $options['bottompanel'],
							'upperpanel' => $options['upperpanel'],
							'lowerpanel' => $options['lowerpanel'],
							),
						);
					$context['TPortal']['caticons'][$row['id']]=$row['value4'];
					$context['TPortal']['catnames'][$row['id']]=$row['value1'];
					$context['TPortal']['categories_shortname'][$sorted[$row['id']]['shortname']]=$row['id'];
				}
			}
			tpdb_free_result($request);
	}
	$context['TPortal']['article_categories']=array();
	if($use_sorted)
	 {
		// sort them
		if(count($sorted)>1)
			$context['TPortal']['article_categories'] = chain('id', 'parent', 'name', $sorted);
		else
			$context['TPortal']['article_categories'] = $sorted;
		unset($context['TPortal']['article_categories'][0]);
	}
	else
	{
		$context['TPortal']['article_categories'] = $sorted;
		unset($context['TPortal']['article_categories'][0]);
	}
}

function chain($primary_field, $parent_field, $sort_field, $rows, $root_id=0, $maxlevel=25)
{
   $c = new chain($primary_field, $parent_field, $sort_field, $rows, $root_id, $maxlevel);
   return $c->chain_table;
}

class chain
{
   var $table;
   var $rows;
   var $chain_table;
   var $primary_field;
   var $parent_field;
   var $sort_field;

   function chain($primary_field, $parent_field, $sort_field, $rows, $root_id, $maxlevel)
   {
       $this->rows = $rows;
       $this->primary_field = $primary_field;
       $this->parent_field = $parent_field;
       $this->sort_field = $sort_field;
       $this->buildChain($root_id,$maxlevel);
   }

   function buildChain($rootcatid,$maxlevel)
   {
       foreach($this->rows as $row)
       {
           $this->table[$row[$this->parent_field]][ $row[$this->primary_field]] = $row;
       }
       $this->makeBranch($rootcatid,0,$maxlevel);
   }

   function makeBranch($parent_id,$level,$maxlevel)
   {
       $rows=$this->table[$parent_id];
       foreach($rows as $key=>$value)
       {
           $rows[$key]['key'] = $this->sort_field;
       }

       usort($rows,'chainCMP');
       foreach($rows as $item)
       {
           $item['indent'] = $level;
           $this->chain_table[] = $item;
           if((isset($this->table[$item[$this->primary_field]])) && (($maxlevel>$level+1) || ($maxlevel==0)))
           {
               $this->makeBranch($item[$this->primary_field], $level+1, $maxlevel);
           }
       }
   }
}

function chainCMP($a,$b)
{
   if($a[$a['key']] == $b[$b['key']])
   {
       return 0;
   }
   return($a[$a['key']]<$b[$b['key']])?-1:1;
}

// some general functions making it possible to use in applications within TP
function tp_getArticles($category=0, $current = '-1', $output= 'echo', $display = 'list', $order = 'date', $sort= 'desc')
{
	global $smcFunc, $db_prefix, $settings, $txt, $scripturl;
	
	$tp_prefix = $settings['tp_prefix'];

	// if category is not a number, return
	if(!is_numeric($category))
		return;

	$articles=array();
	$render = '';


	if($output != 'array')
		$render .= '<ul class="tp_articleList">';
	
	$request =  tp_query("SELECT id, subject, shortname FROM " . $tp_prefix . "articles WHERE category=". $category . " ORDER BY ". $order." ". $sort, __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			if(empty($row['shortname']))
				$row['shortname'] = $row['id'];

			$render .= '<li';
			if($current == $row['id'] || strtolower($current) == $row['shortname']) 
				 $render .= ' class="current_art"';
			$render .= '><a href="' . $scripturl . '?page=' . $row['shortname'] . '">' . $row['subject'] . '</a></li>';
			$articles[] = array(
					'id' => $row['id'],
					'subject' => $row['subject'],
					'href' => $scripturl. '?page=' .$row['shortname'],
					'link' => '<a href="' . $scripturl. '?page=' .$row['shortname'] . '">' . $row['subject']. '</a>',
					'selected' => ($current == $row['id'] || strtolower($current) == $row['shortname']) ? true : false,
					);

		}
		tpdb_free_result($request);
	}	
	if($output == 'array')
		return $articles;

	// render it
	if($display == 'list')
		echo $render;
	else
	{
		$art=array(); $i=0; $curr=0;
		foreach($articles as $rt)
		{
			$art[$i]='<a href="' . $rt['href']. '">'.$rt['subject'].'</a>';
			if($rt['selected'])
				$curr=$i;
			$i++;
		}
		if($curr>0)
			$art_previous=$art[$curr-1];
		else
			$art_previous=$art[0];

		if($curr<$i-1)
			$art_next=$art[$curr+1];
		else
			$art_next=$art[$i];
		
		echo '
		<form name="articlejump" id="articlejump" action="#">
			&#171; ' . $art_previous , ' 
			<select name="articlejump_menu" onchange="javascript:location=document.articlejump.articlejump_menu.options[document.articlejump.articlejump_menu.selectedIndex].value;">';
		foreach($articles as $art)
		{
			echo '<option value="' . $art['href']. '"' , $art['selected'] ? ' selected="selected"' : '' , '>'.$art['subject'].'</option>';
		}
		echo '
			</select>  &nbsp;
			' . $art_next . ' &#187;
		</form>';
	}
}
function tp_cleantitle($text)
{
	$tmp = strtr($text, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
	$tmp = strtr($tmp, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
	$cleaned = preg_replace(array('/\s/', '/[^\w_\.\-]/'), array('_', ''), $tmp);
	return $cleaned;
}

function TP_permaTheme($theme)
{
	global $sourcedir, $context, $db_prefix;
	
	$me=$context['user']['id'];
	$request =  tp_query("UPDATE " . $db_prefix . "members SET ID_THEME=".$theme." WHERE id_member=".$me, __FILE__, __LINE__);
	if(isset($context['TPortal']['querystring']))
	{
		$tp_where=str_replace(array(';permanent'),array(''),$context['TPortal']['querystring']);
	}
	else
		$tp_where='action=forum;';
	redirectexit($tp_where);
}

function TP_setThemeLayer($layer,$template, $subtemplate,$admin=false)
{
	global $txt, $context, $settings, $scripturl;

	if($admin)
	{
		loadtemplate($template);
		if(file_exists($settings['theme_dir']. '/'. $template. '.css'))
			$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/'. $template. '.css?fin11" />';
		else
			$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['default_theme_url']. '/'. $template. '.css?fin11" />';

		if( loadlanguage('TPortalAdmin') == false)
			loadlangauge('TPortalAdmin', 'english');
		if(loadlanguage($template) == false)
			loadlanguage($template, 'english');

		adminIndex('tportal');
		$context['template_layers'][] = $layer;
		$context['sub_template'] = $subtemplate;
	}
	else
	{
		loadtemplate($template);
		if(loadlanguage($template)==false)
			loadlanguage($template, 'english');

		if(file_exists($settings['theme_dir']. '/'. $template. '.css'))
			$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/'. $template. '.css?fin11" />';
		else
			$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['default_theme_url']. '/'. $template. '.css?fin11" />';

		$context['template_layers'][] = $layer;
		$context['sub_template'] = $subtemplate;
	}
}

function TP_notify($text)
{
	global $context, $settings, $scripturl;

	$context['TPortal']['tpnotify'] = $text;
	if($context['user']['is_admin'])
	{
		$context['template_layers'][] = 'tpnotify';
		$context['subtemplate'] = '';
	}
}

function TP_error($text)
{
	global $context, $settings, $scripturl;

	$context['TPortal']['tperror'] = $text;
	$context['template_layers'][] = 'tperror';
}

function tp_renderbbc($message)
{
	global $context, $settings, $options, $txt, $modSettings;

	$descriptionEditorOptions = array(
		'id' => 'description',
		'value' => $context['theme']['description'],
		// We do XML preview here.
		'preview_type' => 0,
		// Specify the size
		'rows' => 7,
		'columns' => 120,
		'width' => '99%',
	);
	create_control_richedit($descriptionEditorOptions);

	// We do not yet support spell checking.
	$context['show_spellchecking'] = false;
	$context['can_post_team'] = siteAllowedTo('postAsTeam');
	$context['sub_template'] = 'themepost';
	$context['page_title'] = $context['editing'] ? $txt['ts_editing_theme'] . $context['theme']['name'] : $txt['ts_add_new_theme'];
	loadTemplate('Post');

	echo '
			<tr>
				<td valign="middle" colspan="2" class="windowbg2">';
	
		echo '
				</td>
			</tr>';	
}

function get_snippets_xml()
{

	return;
}

if(!function_exists('htmlspecialchars_decode'))
{
    function htmlspecialchars_decode($string,$style=ENT_COMPAT)
    {
        $translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS,$style));
        if($style === ENT_QUOTES)
		 $translation['&#38;#38;#039;'] = '\''; 
		return strtr($string,$translation);
    }
}

function TP_createtopic($title,$text,$icon,$board, $sticky=0, $submitter)
{
	global $user_info,$board_info, $sourcedir;

	require_once($sourcedir.'/Subs-Post.php');

	$body=str_replace(array("<",">","\n","	"),array("&lt;","&gt;","<br>","&nbsp;"),$text);
	preparsecode($body);

	// Creating a new topic?
	$newTopic = empty($_REQUEST['msg']) && empty($topic);

	// Collect all parameters for the creation or modification of a post.
	$msgOptions = array(
		'id' => empty($_REQUEST['msg']) ? 0 : (int) $_REQUEST['msg'],
		'subject' => $title,
		'body' =>$body,
		'icon' => $icon,
		'smileys_enabled' => '1',
		'attachments' => array(),
	);
	$topicOptions = array(
		'id' => empty($topic) ? 0 : $topic,
		'board' => $board,
		'poll' => null,
		'lock_mode' => null,
		'sticky_mode' => $sticky,
		'mark_as_read' => true,
	);
	$posterOptions = array(
		'id' => $submitter,
		'name' => '',
		'email' => '',
		'update_post_count' => !$user_info['is_guest'] && !isset($_REQUEST['msg']) && $board_info['posts_count'],
	);

		if(createPost($msgOptions, $topicOptions, $posterOptions))
			$topi=$topicOptions['id'];
		else
			$topi=0;

	return $topi;
}

function TPwysiwyg_setup()
{
	global $context, $boardurl, $user_info;

	$context['html_headers'] .= '
 <script language="JavaScript" type="text/javascript" src="'.$boardurl.'/tp-files/tp-plugins/javascript/whizzywig/whizzywig.js"></script>
<script language="JavaScript" type="text/javascript">
		function toggle_tpeditor_on(target)
		{
			document.getElementById(\'CONTROLS\' + target).style.display = \'\';
			document.getElementById(\'whizzy\' + target).style.display = \'\';
			document.getElementById(target + \'_pure\').style.display = \'none\';
		}
		function toggle_tpeditor_off(target)
		{
			document.getElementById(\'CONTROLS\' + target).style.display = \'none\';
			document.getElementById(\'whizzy\' + target).style.display = \'none\';
			document.getElementById(target + \'_pure\').style.display = \'\';
		}
	</script>
 ';
}

function TPwysiwyg($textarea, $body, $upload = true, $uploadname, $use=1, $showchoice = true)
{
	global $user_info,$board_info, $sourcedir , $boardurl, $boarddir, $smcFunc, $context, $txt;

	echo '
	<div style="margin-top: 10px;">';
	if($showchoice)
	{
		echo '
	<b>' . $txt['tp-usewysiwyg'] . '</b>
	<input ' , $use==1 ? 'checked' : '' , ' value="1" type="radio" id="' . $textarea . '_choice" name="' . $textarea . '_choice" onchange="toggle_tpeditor_on(\''.$textarea.'\');"> ' . $txt['tp-yes'] .' 
	<input ' , $use==0 ? 'checked' : '' , ' value="0" type="radio" id="' . $textarea . '_choice" name="' . $textarea . '_choice" onchange="toggle_tpeditor_off(\''.$textarea.'\');"> ' . $txt['tp-no'] .' 
	<br /><br />';
	}

	echo '
	<textarea style="width: 100%; height: ' . $context['TPortal']['editorheight'] . 'px;" name="'.$textarea.'" id="'.$textarea.'">'.$body.'</textarea>
	<script language="JavaScript" type="text/javascript">
		buttonPath = "'.$boardurl.'/tp-files/tp-plugins/javascript/whizzywig/btn/";
		cssFile = "'.$boardurl.'/tp-files/tp-plugins/javascript/whizzywig/simple.css";
		makeWhizzyWig("'.$textarea.'", "all");
		' , $use==0 ? '
		toggle_tpeditor_off(\''.$textarea.'\');' : '' , '
	</script>';
	if($showchoice)
		echo '
	<textarea style="width: 100%; height: ' . $context['TPortal']['editorheight'] . 'px;' , $use==1 ? 'display: none;' : '' , '" name="'.$textarea.'_pure" id="'.$textarea.'_pure">'.html_entity_decode($body).'</textarea>';

	// only if you can edit your own articles
	if($upload && allowedTo('tp_editownarticle'))
	{
		// fetch all images you have uploaded
		$imgfiles = array();
		if ($handle = opendir($boarddir.'/tp-images/thumbs')) 
		{
			while (false !== ($file = readdir($handle))) 
			{
				if($file!= '.' && $file!='..' && $file!='.htaccess' && substr($file,0, strlen($user_info['id'])+9)=='thumb_'.$user_info['id'].'uid')
				{
					$imgfiles[filectime($boarddir.'/tp-images/thumbs/'.$file)] = $file;
				}
			}
			closedir($handle);
			ksort($imgfiles);
			$imgs=array_reverse($imgfiles);
		}
		echo '
		<div style="padding: 6px;">' , $txt['tp-uploadfile'] ,'<input type="file" name="'.$uploadname.'"></div>
		<div class="titlebg" style="padding: 6px;">' , $txt['tp-quicklist'] , '</div>
		<div class="windowbg2 smalltext" style="padding: 1em;">' , $txt['tp-quicklist2'] , '</div>
		<div class="windowbg" style="padding: 4px; margin-top: 4px; max-height: 200px; overflow: auto;">';
		if(isset($imgs))
		{
			foreach($imgs as $im)
				echo '<img src="'.$boardurl.'/tp-images/thumbs/'.$im.'" class="tp-thumb" alt="" onclick=\'insHTML("<img src=\"'.$boardurl.'/tp-images/', substr($im,6) , '\" border=\"none\" alt=\"\" />")\' />';					
		}
		echo '
		</div>
	</div>';
	}
}
function TPsshowgtags($id, $prefix, $itemid, $onlytags=false)
{
	global $user_info,$board_info, $db_prefix, $sourcedir , $boardurl, $boarddir, $ID_MEMBER, $txt, $settings, $context,$smcFunc;

	$tp_prefix = $db_prefix . 'tp_';

	$gtags=array();
	$request =  tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='globaltag'", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
			$gtags[] = array(
					'id' => $row['id'],
					'tag' => $row['value1'],
					'related' => $row['value2'],
				);

		tpdb_free_result($request);
	}

	$found=array();
	$request =  tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='globaltag_item' AND value3='".$id."' AND subtype2='".$itemid . "'", __FILE__, __LINE__);
	
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
			$found[]=$row['subtype']; // the tag

		tpdb_free_result($request);
	}
	
	// just return the tags
	if($onlytags)
		return implode(",",$found);
	elseif(!$onlytags)
	{
		// show all tags
		echo '
		<input type="hidden" name="'.$id.'" value="'.$itemid.'" />
		<div style="padding: 8px; max-height: 200px; overflow: auto;" class="windowbg">
			';
		if(count($gtags)>0)
		{
			echo '<ul class="gtags">';
			foreach($gtags as $tag)
			{
				echo '
					<li><input type="checkbox" value="'.$itemid.'" name="'.$prefix.'_'.$tag['tag'].'" ', in_array($tag['tag'],$found) ? 'checked="checked"' : '' , ' /> ' , $tag['tag'] , '</li>';
			}
			echo '</ul>';
		}
		echo '
			' . $txt['tp-newtag'] .' <input type="input" value="" name="xyzx_'.$prefix.'_'.$itemid.'"  />
		</div>';
		return;
	}
} 

function TPget_globaltags($tags, $itemid)
{
	global $context,$scripturl,$db_prefix, $settings, $boardurl;

	$tp_prefix = $db_prefix . 'tp_';

	$taglinks = array();
	$tagarray=explode(",",$tags);
	// search the variable table for tags matching

	$searchtag = 'AND (subtype = \'' . implode('\' OR subtype = \'', $tagarray) . '\')';
	
	$request =  tp_query("SELECT DISTINCT value1,value2,value3,subtype2 FROM " . $tp_prefix . "variables WHERE type='globaltag_item' ".$searchtag." ORDER BY value1 ASC", __FILE__, __LINE__);

	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
		{
			$taglinks[]=array(
				'href' => $row['value1'],
				'title' => html_entity_decode($row['value2']),
				'icon' => $row['value3'],
				'type' => $row['value3'],
				'itemid' => $row['subtype2'],
				);
		}
		tpdb_free_result($request);
	}
	return $taglinks;
}


function TParticles_showbytag($tag)
{
	global $context,$scripturl,$db_prefix, $settings, $boardurl;

}

function TParticles_showbymember($member)
{
}

function TP_getallmenus()
{
	global $context,$scripturl,$db_prefix, $settings, $boardurl;

	$tp_prefix=$settings['tp_prefix'];

	$request =  tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='menus' ORDER BY value1 ASC", __FILE__, __LINE__);
	$menus=array();
	$menus[0]=array(
		'id' => 0, 
		'name' => 'Internal', 
		'var1' => '', 
		'var2' => ''
	);

	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			$menus[$row['id']]=array(
					'id' => $row['id'], 
					'name' => $row['value1'], 
					'var1' => $row['value2'], 
					'var2' => $row['value3']
				);
		}
		tpdb_free_result($request);
	}
	return $menus;
}

function TP_getmenu($menu_id)
{
	global $context,$scripturl,$db_prefix, $settings, $boardurl;

	$tp_prefix=$settings['tp_prefix'];

	// get menubox items
	$menu=array();
	$request =  tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='menubox' AND subtype2=".$menu_id." ORDER BY value5 ASC ", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			if($row['value5']!=-1 && $row['value2']!='-1')
			{
				$mtype=substr($row['value3'],0,4);
				$idtype=substr($row['value3'],4);
				if($mtype!='cats' && $mtype!='arti' && $mtype!='head' && $mtype!='spac')
				{
					$mtype='link';
					$idtype=$row['value3'];
				}
				if($mtype=='head')
				{
					$mtype='head';
					$idtype=$row['value1'];
				}
				$menupos=$row['value5'];
				$href='';
				switch($mtype)
				{
					case 'cats' :
						$href = '
				<a href="'. $scripturl. '?cat='.$idtype.'" ' .( $row['value2']=='1' ? 'target="_blank"' : ''). '>'.$row['value1'].'</a>';
						break;
					case 'arti' :
						$href =  '
				<a href="'. $scripturl. '?page='.$idtype.'"' .($row['value2']=='1' ? 'target="_blank"' : '') . '>'.$row['value1'].'</a>';
						break;
					case 'link' :
						$href =  '
				<a href="'.$idtype.'"' . ($row['value2']=='1' ? 'target="_blank"' : '') . '>'.$row['value1'].'</a>';
						break;
					default :
						$href =  '
				<a href="'.$idtype.'"' . ($row['value2']=='1' ? 'target="_blank"' : '') . '>'.$row['value1'].'</a>';
						break;
				}
				if(in_array($mtype,array('cats','arti','link')))
					$menu[] = array(
						'id' => $row['id'],
						'name' => $row['value1'],
						'pos' => $menupos,
						'sub' => $row['value4'],
						'link' => $href,
						);
			}
		}
		tpdb_free_result($request);
	}
	return $menu;
}

function tp_fetchpermissions($perms)
{
	global $scripturl, $context, $settings, $txt, $db_prefix;

	if(is_array($perms))
	{
		$tagquery = 'FIND_IN_SET(p.permission, "' . implode(",",$perms) .'")';
		
		$request =  tp_query("SELECT p.permission,m.group_name as groupName,p.id_group as ID_GROUP 
			FROM (" . $db_prefix . "permissions as p, " . $db_prefix . "membergroups as m)
			WHERE p.add_deny=1
			AND p.id_group=m.id_group
			AND ".$tagquery."
			AND m.min_posts = -1
			ORDER BY m.group_name ASC ", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			while ($row = tpdb_fetch_assoc($request))
			{
				$perms[$row['permission']][$row['ID_GROUP']]=$row['ID_GROUP'];
			}
			tpdb_free_result($request);
		}
		// special for members
		$request =  tp_query("SELECT p.permission 
			FROM " . $db_prefix . "permissions as p
			WHERE p.add_deny=1
			AND p.id_group=0
			AND ".$tagquery."
			", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			while ($row = tpdb_fetch_assoc($request))
			{
				$perms[$row['permission']][0]=0;
			}
			tpdb_free_result($request);
		}
		return $perms;
	}
	else
	{
		$names=array();
		$request =  tp_query("SELECT m.group_name as groupName,m.id_group as ID_GROUP 
			FROM " . $db_prefix . "membergroups as m
			WHERE m.min_posts = -1
			ORDER BY m.group_name ASC ", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			// set regaular members
			$names[0]=array(
					'id' => 0,				
					'name' => $txt['members'],				
					);
			while ($row = tpdb_fetch_assoc($request))
			{
				$names[$row['ID_GROUP']]=array(
					'id' => $row['ID_GROUP'],				
					'name' => $row['groupName'],				
					);
			}
			tpdb_free_result($request);
		}
		return $names;
	}
}

function tp_fetchboards()
{
	global $db_prefix;

	// get all boards for board-spesific news
	$request =  tp_query("SELECT id_board as ID_BOARD,name FROM " . $db_prefix . "boards WHERE 1", __FILE__, __LINE__);
	$boards=array();
	if (tpdb_num_rows($request) > 0)
	{
		while($row = tpdb_fetch_assoc($request))
			$boards[]=array('id' => $row['ID_BOARD'], 'name' => $row['name']);

		tpdb_free_result($request);
	}
	return $boards;
}

function tp_hidepanel($id, $inline=false, $string=false, $margin='')
{
	global $txt, $context, $settings;
	
	$what = '
	<a style="' . (!$inline ? 'float: right;' : '') . ' cursor: pointer;" name="toggle_'.$id.'" onclick="togglepanel(\''.$id.'\')">
		<img id="toggle_' . $id . '" src="' . $settings['tp_images_url'] . '/TPupshrink' . (in_array($id, $context['tp_panels']) ? '2' : '') . '.gif" ' . (!empty($margin) ? 'style="margin: '.$margin.';"' : '') . 'alt="*" />
	</a>';
	if($string)
		return $what;
	else
		echo $what;
}
function tp_hidepanel2($id, $id2, $alt)
{
	global $txt, $context, $settings;
	
	$what = '
	<a title="'.$txt[$alt].'" style="cursor: pointer;" name="toggle_'.$id.'" onclick="togglepanel(\''.$id.'\');togglepanel(\''.$id2.'\')">
		<img id="toggle_' . $id . '" src="' . $settings['tp_images_url'] . '/TPupshrink' . (in_array($id, $context['tp_panels']) ? '2' : '') . '.gif" alt="*" />
	</a>';
	
	return $what;
}

function tp_latestblockcodes()
{
	global $txt, $context;

	return;

	echo '
	<div class="catbg" style="padding: 4px;">', $txt['tp-latestcodes'],' </div>
	<div class="windowbg" id="latcodes" style="padding: 1em; ' , in_array('latcodes',$context['tp_panels']) ? ' display: none;' : '' , '">',
	TPparseRSS('http://www.tinyportal.net/tplatestblockcodes.xml');
	echo '
	</div>';
}

function tp_latestmodules()
{
	global $txt, $context;

	return;

	echo '
		<div class="catbg" style="padding: 4px;">',$txt['tp-latestmods'],' ' , tp_hidepanel('latmods2',true) , '</div>
		<div class="windowbg" id="latmods2" style="padding: 1em; ' , in_array('latmods2',$context['tp_panels']) ? ' display: none;' : '' , '">',
	TPparseRSS('http://www.tinyportal.net/tplatestmodules.xml');
	echo '
		</div>';
}

function tp_collectArticleAttached($art)
{
	global $context,$scripturl,$db_prefix, $settings, $boardurl;

	$tp_prefix=$settings['tp_prefix'];

	// get attached images
	$context['TPortal']['illustrations']=array();
	$context['TPortal']['illustrations_align']=array();
	$context['TPortal']['illustrations_text']=array();
	
	if(is_array($art))
	{
		$tagquery = 'FIND_IN_SET(subtype2, "' . implode(",",$art) .'")';
		$request =  tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='articleimage' AND value5=0 AND ".$tagquery." ORDER BY subtype2 ASC ", __FILE__, __LINE__);
	}
	else
		$request =  tp_query("SELECT * FROM " . $tp_prefix . "variables WHERE type='articleimage' AND subtype2=".$art." ORDER BY value5 ASC ", __FILE__, __LINE__);

	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			if(is_array($art))
			{
				$context['TPortal']['illustrations'][$row['subtype2']][$row['value5']]=$row['value1'];
				$context['TPortal']['illustrations_align'][$row['subtype2']][$row['value5']]=$row['value2'];
				$context['TPortal']['illustrations_text'][$row['subtype2']][$row['value5']]=$row['value3'];
			}
			else
			{
				$context['TPortal']['illustrations'][$art][$row['value5']]=$row['value1'];
				$context['TPortal']['illustrations_align'][$art][$row['value5']]=$row['value2'];
				$context['TPortal']['illustrations_text'][$art][$row['value5']]=$row['value3'];
			}
		}
		tpdb_free_result($request);
	}
}


function TP_fetchprofile_areas()
{
	global $db_prefix, $context, $scripturl, $txt, $settings;

	// prefix of the TP tables
	$tp_prefix = $db_prefix.'tp_';

	$settings['tp_prefix'] = $tp_prefix;

	$areas = array(
		'tp_summary' => array('name' => 'tp_summary', 'permission' => 'profile_view_any'),
		'tp_articles' => array('name' => 'tp_articles', 'permission' => 'tp_articles'),
		'tp_download' => array('name' => 'tp_download', 'permission' => 'tp_dlmanager'),
		);

	// done, now onto custom modules
		$request =  tp_query("SELECT modulename,profile FROM " . $tp_prefix . "modules WHERE active=1", __FILE__, __LINE__);
		if(tpdb_num_rows($request)>0)
		{
			while($row=tpdb_fetch_assoc($request))
			{
				$areas[strtolower($row['modulename'])] = array('name' => strtolower($row['modulename']), 'permission' => $row['profile']);
			}
			tpdb_free_result($request);
		}
	return $areas;
}

function TP_fetchprofile_areas2($memID)
{
	global $db_prefix, $context, $scripturl, $txt, $settings, $user_info;

	// prefix of the TP tables
	$tp_prefix = $db_prefix.'tp_';

	$settings['tp_prefix'] = $tp_prefix;

	if (!$user_info['is_guest'] && (($context['user']['is_owner'] && allowedTo('profile_view_own')) || allowedTo(array('profile_view_any', 'moderate_forum', 'manage_permissions','tp_dlmanager','tp_blocks','tp_articles','tp_gallery','tp_linkmanager'))))
	{
		$context['profile_areas']['tinyportal'] = array(
			'title' => $txt['tp-profilesection'],
			'areas' => array()
		);

		$context['profile_areas']['tinyportal']['areas']['tp_summary'] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa=tp_summary">' . $txt['tpsummary'] . '</a>';
		if ($context['user']['is_owner'] || allowedTo('tp_articles'))
			$context['profile_areas']['tinyportal']['areas']['tp_articles'] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa=tp_articles">' . $txt['articlesprofile'] . '</a>';
		if(($context['user']['is_owner'] || allowedTo('tp_dlmanager')) && $context['TPortal']['show_download'])
			$context['profile_areas']['tinyportal']['areas']['tp_download'] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa=tp_download">' . $txt['downloadprofile'] . '</a>';
	}
	// done, now onto custom modules
	$request =  tp_query("SELECT modulename,autoload_run,profile FROM " . $tp_prefix . "modules WHERE active=1 and profile!=''", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while($row=tpdb_fetch_assoc($request))
		{
			if ($context['user']['is_owner'] || allowedTo($row['profile']))
			{
				$context['profile_areas']['tinyportal']['areas'][strtolower($row['profile'])] = '<a href="' . $scripturl . '?action=profile;u=' . $memID . ';sa='.strtolower($row['profile']).';tpmodule">' . $txt['tp-from']. $row['modulename'] . '</a>';
			}
		}
		tpdb_free_result($request);
	}

}
function tp_renderglobaltags($taglinks, $norender=false)
{
	global $txt, $scripturl;

	if(sizeof($taglinks) == 0)
		return;

	$out = '
	<ul class="article_gtags">';
	foreach($taglinks as $tag)
		$out .= '
		<li><a href="' . $scripturl . $tag['href'] . '"' . (isset($tag['selected']) ? ' class="selected"' : '') . '>' . strip_tags($tag['title'],'<a>') . '</a></li>';

	$out .= '
	</ul>';
	if($norender)
		return $out;
	else
		echo $out;
}

function tpdb_query($query,$val1='',$val2='')
{
	global $smcFunc;

	$req =  $smcFunc['db_query']('',$query);
	return $req;
}

function tp_query($query,$val1='',$val2='')
{
	global $smcFunc;

	$req =  $smcFunc['db_query']('',$query);
	return $req;
}

function tpdb_num_rows($request)
{
	global $smcFunc;

	if(is_resource($request))
	{
		$req =  $smcFunc['db_num_rows']($request);
		return $req;
	}
	else
		return 0;
}

function tpdb_fetch_row($request)
{
	global $smcFunc;

	$req =  $smcFunc['db_fetch_row']($request);
	return $req;
}

function tpdb_fetch_assoc($request)
{
	global $smcFunc;

	$req =  $smcFunc['db_fetch_assoc']($request);
	return $req;
}

function tpdb_insert_id($request)
{
	global $smcFunc;

	$req =  $smcFunc['db_insert_id']($request);
	return $req;
}

function tpdb_free_result($request)
{
	global $smcFunc;

	if(is_resource($request))
	{
		$req =  $smcFunc['db_free_result']($request);
		return $req;
	}
	else
		return;
}
function tp_sanitize($value, $strict=false)
{
	return htmlentities(strip_tags($value),ENT_QUOTES);
}

function get_perm($perm, $moderate = '')
{

	global $context, $user_info;

	$acc=array();
	$show=false;
	$acc=explode(',',$perm);
	foreach($acc as $grp => $val)
	{
		if(in_array($val,$user_info['groups']) && $val>-2)
			$show=true;
	}

	// admin sees all
	if($context['user']['is_admin'])
		$show=true;
	
	// permission holds true? allow them as well!
	if($moderate!='' && allowedTo($moderate))
		$show=true;

	return $show;
}

function tpsort($a, $b)
{
	return strnatcasecmp($b["timestamp"], $a["timestamp"]);
}


// add to the linktree
function TPadd_linktree($url,$name)
{
	global $context, $settings;

	$context['linktree'][] = array('url' => $url, 'name' => $name);
}

// strip the linktree
function TPstrip_linktree()
{
	global $context, $settings, $scripturl;

	$context['linktree']= array();
	$context['linktree'][] = array('url' => $scripturl, 'name' => $context['forum_name']);
}

// TinyPortal startpage
function TPortal()
{
	global $txt, $scripturl, $db_prefix, $ID_MEMBER, $user_info, $sourcedir;
	global $modSettings, $context, $settings;

	// For wireless, we use the Wireless template...
	if (WIRELESS){
		loadTemplate('TPwireless');
		$context['sub_template'] = WIRELESS_PROTOCOL . '_tp';
	}
	else
		loadTemplate('TPortal');
}

function normalizeNewline($text)
{
	str_replace("\r\n", "\n", $text);
	str_replace("\r", "\n", $text);
	
	return addslashes($text);
}

function tportal_version()
{
	return;
}
// Constructs a page list.
function TPageIndex($base_url, &$start, $max_value, $num_per_page)
{
	global $modSettings, $txt;

    $flexible_start = false;
	// Save whether $start was less than 0 or not.
	$start_invalid = $start < 0;

	// Make sure $start is a proper variable - not less than 0.
	if ($start_invalid)
		$start = 0;
	// Not greater than the upper bound.
	elseif ($start >= $max_value)
		$start = max(0, (int) $max_value - (((int) $max_value % (int) $num_per_page) == 0 ? $num_per_page : ((int) $max_value % (int) $num_per_page)));
	// And it has to be a multiple of $num_per_page!
	else
		$start = max(0, (int) $start - ((int) $start % (int) $num_per_page));

	// Wireless will need the protocol on the URL somewhere.
	if (WIRELESS)
		$base_url .= ';' . WIRELESS_PROTOCOL;

	$base_link = '<a class="navPages" href="' . ($flexible_start ? $base_url : strtr($base_url, array('%' => '%%')) . ';p=%d') . '">%s</a> ';

	// Compact pages is off or on?
	if (empty($modSettings['compactTopicPagesEnable']))
	{
		// Show the left arrow.
		$pageindex = $start == 0 ? ' ' : sprintf($base_link, $start - $num_per_page, '&#171;');

		// Show all the pages.
		$display_page = 1;
		for ($counter = 0; $counter < $max_value; $counter += $num_per_page)
			$pageindex .= $start == $counter && !$start_invalid ? '<b>' . $display_page++ . '</b> ' : sprintf($base_link, $counter, $display_page++);

		// Show the right arrow.
		$display_page = ($start + $num_per_page) > $max_value ? $max_value : ($start + $num_per_page);
		if ($start != $counter - $max_value && !$start_invalid)
			$pageindex .= $display_page > $counter - $num_per_page ? ' ' : sprintf($base_link, $display_page, '&#187;');
	}
	else
	{
		// If they didn't enter an odd value, pretend they did.
		$PageContiguous = (int) ($modSettings['compactTopicPagesContiguous'] - ($modSettings['compactTopicPagesContiguous'] % 2)) / 2;

		// Show the first page. (>1< ... 6 7 [8] 9 10 ... 15)
		if ($start > $num_per_page * $PageContiguous)
			$pageindex = sprintf($base_link, 0, '1');
		else
			$pageindex = '';

		// Show the ... after the first page.  (1 >...< 6 7 [8] 9 10 ... 15)
		if ($start > $num_per_page * ($PageContiguous + 1))
			$pageindex .= '<b> ... </b>';

		// Show the pages before the current one. (1 ... >6 7< [8] 9 10 ... 15)
		for ($nCont = $PageContiguous; $nCont >= 1; $nCont--)
			if ($start >= $num_per_page * $nCont)
			{
				$tmpStart = $start - $num_per_page * $nCont;
				$pageindex.= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
			}

		// Show the current page. (1 ... 6 7 >[8]< 9 10 ... 15)
		if (!$start_invalid)
			$pageindex .= '[<b>' . ($start / $num_per_page + 1) . '</b>] ';
		else
			$pageindex .= sprintf($base_link, $start, $start / $num_per_page + 1);

		// Show the pages after the current one... (1 ... 6 7 [8] >9 10< ... 15)
		$tmpMaxPages = (int) (($max_value - 1) / $num_per_page) * $num_per_page;
		for ($nCont = 1; $nCont <= $PageContiguous; $nCont++)
			if ($start + $num_per_page * $nCont <= $tmpMaxPages)
			{
				$tmpStart = $start + $num_per_page * $nCont;
				$pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
			}

		// Show the '...' part near the end. (1 ... 6 7 [8] 9 10 >...< 15)
		if ($start + $num_per_page * ($PageContiguous + 1) < $tmpMaxPages)
			$pageindex .= '<b> ... </b>';

		// Show the last number in the list. (1 ... 6 7 [8] 9 10 ... >15<)
		if ($start + $num_per_page * $PageContiguous < $tmpMaxPages)
			$pageindex .= sprintf($base_link, $tmpMaxPages, $tmpMaxPages / $num_per_page + 1);
	}
	$pageindex = $txt['pages']. ': ' . $pageindex;
	return $pageindex;
}


function tp_renderarticle($intro = '')
{

	global $context, $txt, $scripturl, $boarddir;
	
	// just return if data is missing
	if(!isset($context['TPortal']['article']))
		return;

	echo '
	<div class="article_inner">';
	// use intro!
	if(($context['TPortal']['article']['useintro']=='1' && !$context['TPortal']['single_article']) || !empty($intro))
	{
		if($context['TPortal']['article']['rendertype']=='php')
			eval(tp_convertphp($context['TPortal']['article']['introtext'],true));
		elseif($context['TPortal']['article']['rendertype']=='bbc' || $context['TPortal']['article']['rendertype']=='import')
		{
			if(!WIRELESS)
				echo parse_bbc($context['TPortal']['article']['intro']), '<p><b><a href="' .$scripturl . '?page=' , !empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id'] , '' , WIRELESS ? ';' . WIRELESS_PROTOCOL : '' , '">'.$txt['tp-readmore'].'</a></b></p>';
			else
				echo parse_bbc($context['TPortal']['article']['intro']);
		}
		else
		{
			if(!WIRELESS)
				echo $context['TPortal']['article']['intro'], '<p><b><a href="' .$scripturl . '?page=' , !empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id'] , '' , WIRELESS ? ';'.WIRELESS_PROTOCOL : '' , '">'.$txt['tp-readmore'].'</a></b></p>';
			else
				echo $context['TPortal']['article']['intro'];
		}
	}
	else
	{
		if($context['TPortal']['article']['rendertype']=='php')
			eval(tp_convertphp($context['TPortal']['article']['body'],true));
		elseif($context['TPortal']['article']['rendertype']=='import')
		{
			if(!file_exists($boarddir. '/' . $context['TPortal']['article']['fileimport']))
				echo '<em>' , $txt['tp-cannotfetchfile'] , '</em>';
			else
				include($context['TPortal']['article']['fileimport']);
		}
		elseif($context['TPortal']['article']['rendertype']=='bbc')
			echo parse_bbc($context['TPortal']['article']['body']);
		else
			echo $context['TPortal']['article']['body'];
	}
	echo '
	</div>';
	return;
}
function tp_renderblockarticle()
{

	global $context, $txt, $scripturl, $boarddir;
	
	// just return if data is missing
	if(!isset($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]))
		return;

	echo '
	<div class="article_inner">';
	if($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['rendertype']=='php')
		eval($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['body']);
	elseif($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['rendertype']=='import')
	{
		if(!file_exists($boarddir. '/' . $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['fileimport']))
			echo '<em>' , $txt['tp-cannotfetchfile'] , '</em>';
		else
			include($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['fileimport']);
	}
	elseif($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['rendertype']=='bbc')
		echo parse_bbc($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['body']);
	else
		echo $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['body'];
	echo '
	</div>';
	return;
}

function render_template($code, $render=true)
{
	$ncode= 'echo \'' . str_replace(array('{','}'),array("', ","(), '"),$code).'\';';
	if($render)
		eval($ncode);
	else
		return $ncode;
}

function render_template_layout($code, $prefix = '')
{
	$ncode= 'echo \'' . str_replace(array('{','}'),array("', " . $prefix , "(), '"),$code).'\';';
	eval($ncode);
}

function tp_hidebars($what = 'all' )
{
	global $context;

	if($what=='all'){
		$context['TPortal']['leftpanel']=0;
		$context['TPortal']['centerpanel']=0;
		$context['TPortal']['rightpanel']=0;
		$context['TPortal']['bottompanel']=0;
		$context['TPortal']['toppanel']=0;
		$context['TPortal']['lowerpanel']=0;
	}
	elseif($what=='left')
		$context['TPortal']['leftpanel']=0;
	elseif($what=='right')
		$context['TPortal']['rightpanel']=0;
	elseif($what=='center')
		$context['TPortal']['centerpanel']=0;
	elseif($what=='bottom')
		$context['TPortal']['bottompanel']=0;
	elseif($what=='top')
		$context['TPortal']['toppanel']=0;
	elseif($what=='lower')
		$context['TPortal']['lowerpanel']=0;
}

function get_blockaccess($what, $front=false, $whichbar)
{
	global $db_prefix, $context, $scripturl,$txt , $user_info, $settings , $modSettings, $ID_MEMBER, $boardurl, $sourcedir;

	$mylang=$user_info['language'];
	$show=false;
	// prefix of the TP tables
	$tp_prefix = $db_prefix.'tp_';
	$settings['tp_prefix'] = $tp_prefix;

	// if empty return
	if($what=='')
	{
		$show=false;
		return $show;
	}
    // split up the access level
    $levels=explode('|',$what);
    foreach($levels as $level => $code){
		$precode=substr($code,0,6);
		$body=explode(",",substr($code,6));
		if($precode == 'actio=')
		{
			// special case for frontpage
			if(in_array('frontpage',$body) && !isset($_GET['action']) && !isset($_GET['board']) && !isset($_GET['topic']) && !isset($_GET['page']) && !isset($_GET['cat']))
				$show=true;
			// normal
			if(in_array($context['TPortal']['action'],$body) || (isset($_GET['action']) && in_array($_GET['action'],$body)))
				$show=true;
			// special for forum
			if(in_array('forumall', $body) && $context['TPortal']['in_forum'])
				$show=true;
			// if we are on post screen
			if(isset($_GET['action']) && $_GET['action']=='post2' && in_array('post',$body))
				$show=true;

			// special for allpages!
			if(in_array('allpages', $body))
				$show=true;
		}
		elseif($precode == 'board='){
			if(isset($_GET['board']) && in_array($_GET['board'],$body))
				$show=true;
			// show on all boards
			if(isset($_GET['board']) && in_array('-1',$body))
				$show=true;
		}
		elseif($precode == 'dlcat='){
			if(isset($_GET['dl']) && substr($_GET['dl'],0,3)=='cat' && in_array(substr($_GET['dl'],3),$body))
				$show=true;
		}
		elseif($precode == 'tpmod='){
			if($context['TPortal']['action']=='tpmod' && isset($_GET[$body]))
				$show=true;
		}
		elseif($precode == 'custo='){
			if(isset($_GET['action']) && in_array($_GET['action'],$body))
				$show=true;
		}
		elseif($precode == 'tpage=')
		{
			if(isset($context['TPortal']['currentpage']))
			{
				if(in_array($context['TPortal']['currentpage'],$body))
					$show=true;
			}
			if($front && in_array($context['TPortal']['featured_article'],$body))
				$show=true;
		}
		elseif($precode == 'tpcat='){
			if(isset($_GET['cat']) && !isset($_GET['action']) && in_array($_GET['cat'],$body))
				$show=true;
			// also on the actual category
			if(!empty($context['TPortal']['parentcat']) && in_array($context['TPortal']['parentcat'],$body))
				$show=true;
		}
		elseif($precode == 'tlang=')
		{
			// if a language IS selected, use ONLY that, otherwise it will abide to the others
			if(in_array($mylang,$body))
				$show_lang=true;
			else
				$show_lang=false;
		}
		// code for modules
		elseif($precode == 'modul='){
			if($context['TPortal']['action']=='tpmod' && isset($_GET['dl']))
				$show=true;
		}
    }
	// check for language option
	if(isset($show_lang) && $show==true)
	{
		$show=$show_lang;
	}
	return $show;
 }

function TPgetlangOption( $langlist , $set)
{
	$lang = explode("|", $langlist);
	$num=count($lang);

	$setlang='';

	for($i=0; $i<$num ; $i=$i+2){
		if($lang[$i]==$set)
			$setlang=$lang[$i+1];
	}

	return $setlang;
}

// the featured or first article
function category_featured()
{
	global $context;

	unset($context['TPortal']['article']);
	if(empty($context['TPortal']['category']['featured']))
		return;

	$context['TPortal']['article'] = $context['TPortal']['category']['featured'];

	if(!empty($context['TPortal']['article']['template']))
		render_template($context['TPortal']['article']['template']);
	else
	{
		// check if theme has its own
		if(function_exists('ctheme_article_renders'))
			render_template(ctheme_article_renders($context['TPortal']['category']['options']['catlayout'],false,true));
		else
			render_template(article_renders($context['TPortal']['category']['options']['catlayout'],false,true));
	}
}

// the first half
function category_col1()
{
	global $context;

	unset($context['TPortal']['article']);
	if(!isset($context['TPortal']['category']['col1']))
		return;

	foreach($context['TPortal']['category']['col1'] as $article => $context['TPortal']['article'])
	{
		if(!empty($context['TPortal']['article']['template']))
			render_template($context['TPortal']['article']['template']);
		else
		{
			if(function_exists('ctheme_article_renders'))
				render_template(ctheme_article_renders($context['TPortal']['category']['options']['catlayout'],false));
			else
				render_template(article_renders($context['TPortal']['category']['options']['catlayout'],false));
		}
		unset($context['TPortal']['article']);
	}
}

// the second half
function category_col2()
{
	global $context;

	unset($context['TPortal']['article']);
	if(!isset($context['TPortal']['category']['col2']))
		return;

	foreach($context['TPortal']['category']['col2'] as $article => $context['TPortal']['article'])
	{
		if(!empty($context['TPortal']['article']['template']))
			render_template($context['TPortal']['article']['template']);
		else
		{
			if(function_exists('ctheme_article_renders'))
				render_template(ctheme_article_renders($context['TPortal']['category']['options']['catlayout'],false));
			else
				render_template(article_renders($context['TPortal']['category']['options']['catlayout'],false));
		}
		unset($context['TPortal']['article']);
	}
}

function startElement($parser, $tagName, $attrs)
{

	// The function used when an element is encountered
	global $insideitem, $tag;

	if($insideitem)
		$tag = $tagName;
	elseif($tagName == "ITEM")
		$insideitem = true;
	elseif($tagName == "ENTRY")
		$insideitem = true;
	elseif($tagName == "IMAGE")
		$insideitem = true;
}

function characterData($parser, $data)
{
	// The function used to parse all other data than tags
	global $insideitem, $tag, $title, $description, $link , $tpimage , $tpbody, $curl, $content_encoded, $pubdate, $content, $created;

	if ($insideitem)
	{
		switch ($tag)
		{
			case "TITLE":
				$title .= $data;
				break;
			case "DESCRIPTION":
				$description .= $data;
				break;
			case "LINK":
				$link .= $data;
				break;
			case "IMAGE":
				$tpimage .= $data;
				break;
			case "BODY":
				$tpbody .= $data;
				break;
			case "URL":
				$curl .= $data;
				break;
			case "CONTENT:ENCODED":
				$content_encoded .= $data;
				break;
			case "CONTENT":
				$content .= $data;
				break;
			case "PUBDATE":
				$pubdate .= $data;
				break;
			case "CREATED":
				$created .= $data;
				break;
		}
	}
}

function endElement($parser, $tagName)
{

	// This function is used when an end-tag is encountered.
	global $context, $insideitem, $tag, $title, $description, $link, $tpimage, $curl, $content_encoded, $pubdate, $content, $created;

	// RSS/RDF feeds
	if ($tagName == "ITEM")
	{
		echo '
<div class="rss_title' , $context['TPortal']['rss_notitles'] ? '_normal' : '' , '">';
		printf("<a href='%s'>%s</a>", trim($link),htmlspecialchars(trim($title)));
		echo '
</div>';
		if(!$context['TPortal']['rss_notitles'])
		{
			if(!empty($pubdate))
				echo '
<div class="rss_date">' . $pubdate . '</div>';
			echo '
<div class="rss_body">';
		if(!empty($content_encoded))
			echo ($content_encoded); // Print out the live journal entry
		else
			echo ($description); // Print out the live journal entry
		
		echo '
</div>';
		}
		$title = $description = $link = $insideitem = $curl = $content_encoded = $pubdate = false;
	}
	// ATOM feeds
	elseif ($tagName == "ENTRY")
	{
		echo '
<div class="rss_title">' . $link . $title.'</a>';
		echo '
</div>';
		if(!empty($created))
			echo '
<div class="rss_date">' . $created . '</div>';
		if(!$context['TPortal']['rss_notitles']){
			echo '
<div class="rss_body">';
		if(!empty($content))
			echo ($content); // Print out the live journal entry
		else
			echo ($description); // Print out the live journal entry
		
		echo '
</div>';
		}
		$title = $description = $link = $insideitem = $curl = $content_encoded = $pubdate = $created = false;
	}
}

function TPparseRSS($override = '', $encoding = 0)
{

	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	$backend = isset($context['TPortal']['rss']) ? $context['TPortal']['rss'] : '';
	if($override!='')
		$backend=$override;

	$insideitem = false;
	$tag = "";
	$title = "";
	$description = "";
	$link = "";
	$curl = "";
	$content_encoded = "";
	$pubdate = "";

	// Now to the parsing itself. Starts by creating it:
	if($encoding==0)
		$xml_parser = xml_parser_create('ISO-8859-1');
	else
		$xml_parser = xml_parser_create('UTF-8');

	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "characterData");

	// Open the actual datafile:

	$fp = fopen($backend, "r");
    
	$xmlerr='';
	// Run through it line by line and parse:
	while ($data = fread($fp, 4096))
	{
		xml_parse($xml_parser, $data, feof($fp)) or $xmlerr=(sprintf("XML error: %s at line %d",
		xml_error_string(xml_get_error_code($xml_parser)),
		xml_get_current_line_number($xml_parser)));
		if($xmlerr!='')
			break;
	}
	// Close the datafile
	fclose($fp);

	// Free any memmory used
	xml_parser_free($xml_parser);
	if($xmlerr!='')
		echo $xmlerr;
}

// Set up the administration sections.
function TPadminIndex($tpsub='', $module_admin=false)
{
	global $txt, $context, $scripturl, $sc, $modSettings, $user_info, $settings;

   $tp_prefix=$settings['tp_prefix'];

	loadlanguage('TPortalAdmin');
	if($module_admin)
	{
		// make sure tpadmin is still active
		$_GET['action']='tpadmin';
	}
	$context['admin_tabs'] = array();
	$context['admin_header']['tp_news']=$txt['tp-adminnews1'];
	$context['admin_header']['tp_settings'] = $txt['tp-adminheader1'];
	$context['admin_header']['tp_articles'] = $txt['tp-articles'];
	$context['admin_header']['tp_blocks'] = $txt['tp-adminpanels'];
	$context['admin_header']['tp_modules']=$txt['tp-modules'];
	$context['admin_header']['tp_menubox']= $txt['tp-menumanager'];
	$context['admin_header']['custom_modules']= $txt['custom_modules'];

	if (allowedTo('tp_settings'))
	{
		$context['admin_tabs']['tp_news'] = array(
			'news' => array(
				'title' => $txt['tp-adminnews1'],
				'description' => $txt['tp-adminnews2'],
				'href' => $scripturl . '?action=tpadmin;sa=news',
				'is_selected' => $tpsub == 'news',
			),
			'credits' => array(
				'title' => $txt['tp-credits'],
				'description' => $txt['tp-creditsdesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=credits',
				'is_selected' => $tpsub == 'credits',
			),
		);
	}
	if (allowedTo('tp_settings'))
	{
		$context['admin_tabs']['tp_settings'] = array(
			'settings' => array(
				'title' => $txt['tp-settings'],
				'description' => $txt['tp-settingdesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=settings',
				'is_selected' => $tpsub == 'settings',
			),
			'frontpage' => array(
				'title' => $txt['tp-frontpage'],
				'description' => $txt['tp-frontpagedesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=frontpage',
				'is_selected' => $tpsub == 'frontpage',
			),
			'permissions' => array(
				'title' => $txt['tp-permissions'],
				'description' => $txt['tp-permdesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=permissions',
				'is_selected' => $tpsub == 'permissions',
			),
		);
	}
	if (allowedTo('tp_articles'))
	{
		$context['admin_tabs']['tp_articles'] = array(
			'articles' => array(
				'title' => $txt['tp-articles'],
				'description' => $txt['tp-articledesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=articles',
				'is_selected' => (substr($tpsub,0,11)=='editarticle' || in_array($tpsub, array('articles','addarticle','addarticle_php', 'addarticle_bbc', 'addarticle_import','strays'))),
			),
			'categories' => array(
				'title' => $txt['tp-tabs5'],
				'description' => $txt['tp-articledesc2'],
				'href' => $scripturl . '?action=tpadmin;sa=categories',
				'is_selected' => in_array($tpsub, array('categories', 'addcategory','clist')) ,
			),
			'artsettings' => array(
				'title' => $txt['tp-settings'],
				'description' => $txt['tp-articledesc3'],
				'href' => $scripturl . '?action=tpadmin;sa=artsettings',
				'is_selected' => $tpsub == 'artsettings',
			),
			'submission' => array(
				'title' => (isset($context['TPortal']['submissions']) && $context['TPortal']['submissions'])>0 ? $txt['tp-tabs4'].' ['.$context['TPortal']['submissions'].']' : $txt['tp-tabs4'] ,
				'description' => $txt['tp-articledesc4'],
				'href' => $scripturl . '?action=tpadmin;sa=submission',
				'is_selected' => $tpsub == 'submission',
			),	
			'icons' => array(
				'title' => $txt['tp-adminicons'],
				'description' => $txt['tp-articledesc5'],
				'href' => $scripturl . '?action=tpadmin;sa=articons',
				'is_selected' => $tpsub == 'articons',
			),	
		);
	}
	if (allowedTo('tp_blocks'))
	{
		$context['admin_tabs']['tp_blocks'] = array(
			'panelsettings' => array(
				'title' => $txt['tp-allpanels'],
				'description' => $txt['tp-paneldesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=panels',
				'is_selected' => $tpsub=='panels',
			),
			'blocks' => array(
				'title' => $txt['tp-allblocks'],
				'description' => $txt['tp-blocksdesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=blocks',
				'is_selected' => $tpsub == 'blocks' && !isset($_GET['latest']) && !isset($_GET['overview']), 
			),
			'blockoverview' => array(
				'title' => $txt['tp-blockoverview'],
				'description' => '',
				'href' => $scripturl . '?action=tpadmin;sa=blocks;overview',
				'is_selected' => $tpsub == 'blocks' && isset($_GET['overview']), 
			),
		);
	}
	if (allowedTo('tp_settings'))
	{
		$context['admin_tabs']['tp_modules'] = array(
			'modules' => array(
				'title' => $txt['tp-modules'],
				'description' => $txt['tp-moduledesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=modules',
				'is_selected' => $tpsub == 'modules' && !isset($_GET['import']) && !isset($_GET['tags']),
			),
				'tags' => array(
				'title' => $txt['tp-tags'],
				'description' => $txt['tp-tags2'],
				'href' => $scripturl . '?action=tpadmin;sa=modules;tags',
				'is_selected' => $tpsub == 'tags',
			),
		);
	}
	// collect modules and their permissions	
	$result =  tp_query("SELECT * FROM " . $tp_prefix . "modules WHERE 1", __FILE__, __LINE__);
	if(tpdb_num_rows($result)>0)
	{
		while($row = tpdb_fetch_assoc($result))
		{
			$context['TPortal']['admmodules'][] = $row;
		}
		tpdb_free_result($result);
	}
	if (allowedTo('tp_blocks'))
	{
		$context['admin_tabs']['tp_menubox'] = array(
			'menubox' => array(
				'title' => $txt['tp-menumanager'],
				'description' => '',
				'href' => $scripturl . '?action=tpadmin;sa=menubox',
				'is_selected' => in_array($tpsub, array('menubox','linkmanager')),
			),
			'addmenu' => array(
				'title' => isset($_GET['mid']) ? $txt['tp-addmenuitem'] : $txt['tp-addmenu'],
				'description' => '',
				'href' => (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $scripturl . '?action=tpadmin;sa=addmenu;mid='.$_GET['mid'] : $scripturl . '?action=tpadmin;sa=addmenu;fullmenu',
				'is_selected' => in_array($tpsub, array('addmenu')),
				),
			);
	}
	TPsetupAdminAreas();
	validateSession();
}

function tp_collectArticleIcons()
{
	global $scripturl, $context, $settings, $db_prefix, $boarddir, $boardurl;

	// get all themes for selection
	$context['TPthemes']  = array();
	$request =  tp_query("
		SELECT th.value AS name, th.id_theme as ID_THEME, tb.value AS path
		FROM " . $db_prefix . "themes AS th
		LEFT JOIN " . $db_prefix . "themes AS tb ON th.id_theme = tb.id_theme
		WHERE th.variable = 'name'
		AND tb.variable = 'images_url'
		AND th.id_member = 0
		ORDER BY th.value ASC", __FILE__, __LINE__);
	if(is_resource($request) && tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			$context['TPthemes'][] = array(
			'id' => $row['ID_THEME'],
			'path' => $row['path'],
			'name' => $row['name']
			);
		}
		tpdb_free_result($request);
	}

	$count=1;
	$sorted=array();
	$context['TPortal']['articons'] = array();
	$context['TPortal']['articons']['icons'] = array();
	$context['TPortal']['articons']['illustrations'] = array();
	// first, icons
	if($handle = opendir($boarddir.'/tp-files/tp-articles/icons')) 
	{
		while (false !== ($file = readdir($handle))) 
		{
			if($file!= '.' && $file!='..' && $file!='.htaccess' && $file!='TPnoicon.gif' && in_array(strtolower(substr($file,strlen($file)-4,4)),array('.gif','.jpg','.png')))
			{
				$context['TPortal']['articons']['icons'][] = array(
						'id' => $count,
						'file' => $file,
						'image' => '<img src="'.$boardurl.'/tp-files/tp-articles/icons/'.$file.'" alt="'.$file.'" />',
						);
			}
		}
		closedir($handle);
	}
	sort($context['TPortal']['articons']['icons']);

	$count=1;
	$sorted2=array();
	// and illustrations
	if ($handle = opendir($boarddir.'/tp-files/tp-articles/illustrations')) 
	{
		while (false !== ($file = readdir($handle))) 
		{
			if($file!= '.' && $file!='..' && $file!='.htaccess' && $file!='TPno_illustration.gif' && in_array(strtolower(substr($file,strlen($file)-4,4)),array('.gif','.jpg','.png')))
			{
				if(substr($file,0,2)=='s_')
					$context['TPortal']['articons']['illustrations'][] = array(
						'id' => $count,
						'file' => $file,
						'image' => '<img src="'.$boardurl.'/tp-files/tp-articles/illustrations/'.$file.'" alt="'.$file.'" />',
						);
				$count++;
			}
		}
		closedir($handle);
	}
	sort($context['TPortal']['articons']['illustrations']);
}

function tp_recordevent($date, $id_member, $textvariable, $link, $description, $allowed, $eventid)
{
	global $txt, $context, $scripturl, $sc, $modSettings, $user_info, $settings;

	$tp_prefix=$settings['tp_prefix'];
	$request =  tp_query("
		INSERT INTO " . $tp_prefix . "events 
		(id_member, date, textvariable, link, description, allowed, eventid)
		VALUES(" . $id_member . ", " . $date . ", '" . $textvariable . "', '" . $link . "', '" . $description . "', '" . $allowed . "', " . $eventid . ")", __FILE__, __LINE__);
}

function tp_fatal_error($error)
{
	global $context,  $txt;

	$context['sub_template'] = 'tp_fatal_error';	
	$context['TPortal']['errormessage'] = $error;
}

// Recent topic list:   [board] Subject by Poster	Date
function tp_recentTopics($num_recent = 8, $exclude_boards = null, $output_method = 'echo')
{
	global $context, $settings, $scripturl, $txt, $db_prefix, $ID_MEMBER;
	global $user_info, $modSettings, $func;

	if ($exclude_boards === null && !empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0)
		$exclude_boards = array($modSettings['recycle_board']);
	else
		$exclude_boards = empty($exclude_boards) ? array() : $exclude_boards;

	// Find all the posts in distinct topics.  Newer ones will have higher IDs.
	$request = tp_query("
		SELECT
			m.poster_time as posterTime, ms.subject, m.id_topic as ID_TOPIC, m.id_member as ID_MEMBER, m.id_msg as ID_MSG, b.id_board as ID_BOARD, b.name AS bName,
			IFNULL(mem.real_name, m.poster_name) AS posterName, " . ($user_info['is_guest'] ? '1 AS isRead, 0 AS new_from' : '
			IFNULL(lt.id_msg, IFNULL(lmr.id_msg, 0)) >= m.id_msg_modified AS isRead,
			IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1 AS new_from') . ", 
			IFNULL(a.id_attach, 0) AS ID_ATTACH, a.filename, a.attachment_type as attachmentType,  mem.avatar as avy
		FROM (" . $db_prefix . "messages AS m, ". $db_prefix . "topics AS t, " . $db_prefix . "boards AS b, " . $db_prefix . "messages AS ms)
			LEFT JOIN " . $db_prefix . "members AS mem ON (mem.id_member = m.id_member)" . (!$user_info['is_guest'] ? "
			LEFT JOIN " . $db_prefix . "log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = " . $context['user']['id']. ")
			LEFT JOIN " . $db_prefix . "log_mark_read AS lmr ON (lmr.id_board = b.id_board AND lmr.id_member = ". $context['user']['id']. ")" : '') . "
			LEFT JOIN " . $db_prefix . "attachments AS a ON (a.id_member = mem.id_member)
		WHERE t.id_last_msg >= " . ($modSettings['maxMsgID'] - 35 * min($num_recent, 5)) . "
			AND t.id_last_msg = m.id_msg
			AND b.id_board = t.id_board" . (empty($exclude_boards) ? '' : "
			AND b.id_board NOT IN (" . implode(', ', $exclude_boards) . ")") . "
			AND ". $user_info['query_see_board'] . "
			AND ms.id_msg = t.id_first_msg
		ORDER BY t.id_last_msg DESC
		LIMIT " . $num_recent, __FILE__, __LINE__);
	$posts = array();
	while ($row = tpdb_fetch_assoc($request))
	{
		// Censor the subject.
		censorText($row['subject']);

		// Build the array.
		$posts[] = array(
			'board' => array(
				'id' => $row['ID_BOARD'],
				'name' => $row['bName'],
				'href' => $scripturl . '?board=' . $row['ID_BOARD'] . '.0',
				'link' => '<a href="' . $scripturl . '?board=' . $row['ID_BOARD'] . '.0">' . $row['bName'] . '</a>'
			),
			'topic' => $row['ID_TOPIC'],
			'poster' => array(
				'id' => $row['ID_MEMBER'],
				'name' => $row['posterName'],
				'href' => empty($row['ID_MEMBER']) ? '' : $scripturl . '?action=profile;u=' . $row['ID_MEMBER'],
				'link' => empty($row['ID_MEMBER']) ? $row['posterName'] : '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'] . '">' . $row['posterName'] . '</a>',
				'avatar' => $row['avy'] == '' ? ($row['ID_ATTACH'] > 0 ? '<img src="' . (empty($row['attachmentType']) ? $scripturl . '?action=dlattach;attach=' . $row['ID_ATTACH'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $row['filename']) . '" alt="" class="recent_avatar" border="0" />' : '') : (stristr($row['avy'], 'http://') ? '<img src="' . $row['avy'] . '" alt="" class="recent_avatar" border="0" />' : '<img src="' . $modSettings['avatar_url'] . '/' . htmlspecialchars($row['avy']) . '" alt="" class="recent_avatar" border="0" />')
			),
			'subject' => $row['subject'],
			'short_subject' => shorten_subject($row['subject'], 25),
			'time' => timeformat($row['posterTime']),
			'timestamp' => forum_time(true, $row['posterTime']),
			'href' => $scripturl . '?topic=' . $row['ID_TOPIC'] . '.msg' . $row['ID_MSG'] . ';topicseen#new',
			'link' => '<a href="' . $scripturl . '?topic=' . $row['ID_TOPIC'] . '.msg' . $row['ID_MSG'] . '#new">' . $row['subject'] . '</a>',
			'new' => !empty($row['isRead']),
			'new_from' => $row['new_from'],
		);
	}
	tpdb_free_result($request);

	return $posts;

}

// Download an attachment.
function tpattach()
{
	global $txt, $modSettings, $user_info, $scripturl, $context, $sourcedir, $topic, $smcFunc;

	// Some defaults that we need.
	$context['character_set'] = empty($modSettings['global_character_set']) ? (empty($txt['lang_character_set']) ? 'ISO-8859-1' : $txt['lang_character_set']) : $modSettings['global_character_set'];
	$context['utf8'] = $context['character_set'] === 'UTF-8' && (strpos(strtolower(PHP_OS), 'win') === false || @version_compare(PHP_VERSION, '4.2.3') != -1);
	$context['no_last_modified'] = true;

	// Make sure some attachment was requested!
	if (!isset($_REQUEST['attach']) && !isset($_REQUEST['id']))
		fatal_lang_error('no_access', false);

	$_REQUEST['attach'] = isset($_REQUEST['attach']) ? (int) $_REQUEST['attach'] : (int) $_REQUEST['id'];

	if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'avatar')
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_folder, filename, file_hash, fileext, id_attach, attachment_type, mime_type, approved
			FROM {db_prefix}attachments
			WHERE id_attach = {int:id_attach}
				AND id_member > {int:blank_id_member}
			LIMIT 1',
			array(
				'id_attach' => $_REQUEST['attach'],
				'blank_id_member' => 0,
			)
		);
		$_REQUEST['image'] = true;
	}
	// This is just a regular attachment...
	else
	{
		$request = $smcFunc['db_query']('', '
			SELECT a.id_folder, a.filename, a.file_hash, a.fileext, a.id_attach, a.attachment_type, a.mime_type, a.approved
			FROM {db_prefix}attachments AS a
			WHERE a.id_attach = {int:attach}
			LIMIT 1',
			array(
				'attach' => $_REQUEST['attach'],
			)
		);
	}
	if ($smcFunc['db_num_rows']($request) == 0)
		fatal_lang_error('no_access', false);
	list ($id_folder, $real_filename, $file_hash, $file_ext, $id_attach, $attachment_type, $mime_type, $is_approved) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	$filename = getAttachmentFilename($real_filename, $_REQUEST['attach'], $id_folder, false, $file_hash);

	// This is done to clear any output that was made before now. (would use ob_clean(), but that's PHP 4.2.0+...)
	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']) && @version_compare(PHP_VERSION, '4.2.0') >= 0 && @filesize($filename) <= 4194304 && in_array($file_ext, array('txt', 'html', 'htm', 'js', 'doc', 'pdf', 'docx', 'rtf', 'css', 'php', 'log', 'xml', 'sql', 'c', 'java')))
		@ob_start('ob_gzhandler');
	else
	{
		ob_start();
		header('Content-Encoding: none');
	}

	// No point in a nicer message, because this is supposed to be an attachment anyway...
	if (!file_exists($filename))
	{
		loadLanguage('Errors');

		header('HTTP/1.0 404 ' . $txt['attachment_not_found']);
		header('Content-Type: text/plain; charset=' . (empty($context['character_set']) ? 'ISO-8859-1' : $context['character_set']));

		// We need to die like this *before* we send any anti-caching headers as below.
		die('404 - ' . $txt['attachment_not_found']);
	}

	// If it hasn't been modified since the last time this attachement was retrieved, there's no need to display it again.
	if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))
	{
		list($modified_since) = explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
		if (strtotime($modified_since) >= filemtime($filename))
		{
			ob_end_clean();

			// Answer the question - no, it hasn't been modified ;).
			header('HTTP/1.1 304 Not Modified');
			exit;
		}
	}

	// Check whether the ETag was sent back, and cache based on that...
	$eTag = '"' . substr($_REQUEST['attach'] . $real_filename . filemtime($filename), 0, 64) . '"';
	if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) && strpos($_SERVER['HTTP_IF_NONE_MATCH'], $eTag) !== false)
	{
		ob_end_clean();

		header('HTTP/1.1 304 Not Modified');
		exit;
	}

	// Send the attachment headers.
	header('Pragma: ');

	if (!$context['browser']['is_gecko'])
		header('Content-Transfer-Encoding: binary');
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 525600 * 60) . ' GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT');
	header('Accept-Ranges: bytes');
	header('Set-Cookie:');
	header('Connection: close');
	header('ETag: ' . $eTag);

	// IE 6 just doesn't play nice. As dirty as this seems, it works.
	if ($context['browser']['is_ie6'] && isset($_REQUEST['image']))
		unset($_REQUEST['image']);

	elseif (filesize($filename) != 0)
	{
		$size = @getimagesize($filename);
		if (!empty($size))
		{
			// What headers are valid?
			$validTypes = array(
				1 => 'gif',
				2 => 'jpeg',
				3 => 'png',
				5 => 'psd',
				6 => 'x-ms-bmp',
				7 => 'tiff',
				8 => 'tiff',
				9 => 'jpeg',
				14 => 'iff',
			);

			// Do we have a mime type we can simpy use?
			if (!empty($size['mime']) && !in_array($size[2], array(4, 13)))
				header('Content-Type: ' . strtr($size['mime'], array('image/bmp' => 'image/x-ms-bmp')));
			elseif (isset($validTypes[$size[2]]))
				header('Content-Type: image/' . $validTypes[$size[2]]);
			// Otherwise - let's think safety first... it might not be an image...
			elseif (isset($_REQUEST['image']))
				unset($_REQUEST['image']);
		}
		// Once again - safe!
		elseif (isset($_REQUEST['image']))
			unset($_REQUEST['image']);
	}

	header('Content-Disposition: ' . (isset($_REQUEST['image']) ? 'inline' : 'attachment') . '; filename="' . $real_filename . '"');
	if (!isset($_REQUEST['image']))
		header('Content-Type: application/octet-stream');

	// If this has an "image extension" - but isn't actually an image - then ensure it isn't cached cause of silly IE.
	if (!isset($_REQUEST['image']) && in_array($file_ext, array('gif', 'jpg', 'bmp', 'png', 'jpeg', 'tiff')))
		header('Cache-Control: no-cache');
	else
		header('Cache-Control: max-age=' . (525600 * 60) . ', private');

	if (empty($modSettings['enableCompressedOutput']) || filesize($filename) > 4194304)
		header('Content-Length: ' . filesize($filename));

	// Try to buy some time...
	@set_time_limit(0);

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

function art_recentitems($max='5' , $type='date' ){

	global $context, $settings, $db_prefix, $txt, $settings;

	$tp_prefix = $db_prefix.'tp_';
	$now = forum_time();
	$data=array();
    
	if($type=='date')
		$request =  tp_query("SELECT art.id, art.date, art.subject, art.views, art.rating, art.comments FROM " . $tp_prefix . "articles as art WHERE 
	art.off=0 and art.approved=1 
	AND ((art.pub_start=0 AND art.pub_end=0) OR (art.pub_start!=0 AND art.pub_start<".$now." AND art.pub_end=0) OR (art.pub_start=0 AND art.pub_end!=0 AND art.pub_end>".$now.") OR (art.pub_start!=0 AND art.pub_end!=0 AND art.pub_end>".$now." AND art.pub_start<".$now."))
	ORDER BY art.date DESC LIMIT ". $max, __FILE__, __LINE__);
	elseif($type=='views')
		$request =  tp_query("SELECT art.id, art.date, art.subject, art.views, art.rating, art.comments FROM " . $tp_prefix . "articles as art WHERE 
	art.off=0 and art.approved=1 
	AND ((art.pub_start=0 AND art.pub_end=0) OR (art.pub_start!=0 AND art.pub_start<".$now." AND art.pub_end=0) OR (art.pub_start=0 AND art.pub_end!=0 AND art.pub_end>".$now.") OR (art.pub_start!=0 AND art.pub_end!=0 AND art.pub_end>".$now." AND art.pub_start<".$now."))
	ORDER BY art.views DESC LIMIT ". $max, __FILE__, __LINE__);
	elseif($type=='comments')
		$request =  tp_query("SELECT art.id, art.date, art.subject, art.views, art.rating, art.comments FROM " . $tp_prefix . "articles as art WHERE 
	art.off=0 and art.approved=1 
	AND ((art.pub_start=0 AND art.pub_end=0) OR (art.pub_start!=0 AND art.pub_start<".$now." AND art.pub_end=0) OR (art.pub_start=0 AND art.pub_end!=0 AND art.pub_end>".$now.") OR (art.pub_start!=0 AND art.pub_end!=0 AND art.pub_end>".$now." AND art.pub_start<".$now."))
	ORDER BY art.comments DESC LIMIT ". $max, __FILE__, __LINE__);

	if(tpdb_num_rows($request)>0){
		while ($row = tpdb_fetch_assoc($request))
		{
			$rat=explode(",",$row['rating']);
			$rating_votes=count($rat);
			if($row['rating']=='')
				$rating_votes=0;

			$total=0;
			foreach($rat as $mm => $mval)
			{
				$total=$total+$mval;
			}
			if($rating_votes>0 && $total>0)
				$rating_average=floor($total/$rating_votes);
			else
				$rating_average=0;

			$data[] = array(
				'id' => $row['id'],
				'subject' => $row['subject'],
				'views' => $row['views'],
				'date' => timeformat($row['date']),
				'rating' => $rating_average,
				'rating_votes' => $rating_votes,
				'comments' => $row['comments'],
				);
		}
		tpdb_free_result($request);
	}
	return $data;
}

function dl_recentitems($number='8', $sort='date',$type='array', $cat=0)
{
	global $boardurl, $context, $scripturl, $settings, $txt, $db_prefix;

	// collect all categories to search in

	$mycats=array();
	dl_getcats();
	if($cat>0)
		$mycats[]=$cat;
	else
	{
		foreach($context['TPortal']['dl_allowed_cats'] as $ca)
			$mycats[]=$ca['id'];
	}
	
	if($sort=='authorID')
		$sort='author_id';

	// empty?
	if(sizeof($mycats)>0)
	{
		$tp_prefix=$settings['tp_prefix'];
		$context['TPortal']['dlrecenttp'] = array();
		// decide what to sort from
		$sortstring='';
		if($sort=='date')
			$sortstring='ORDER BY dlm.created DESC';
		elseif($sort=='views')
			$sortstring='ORDER BY dlm.views DESC';
		elseif($sort=='downloads')
			$sortstring='ORDER BY dlm.downloads DESC';
		else
			$sortstring='ORDER BY dlm.created DESC';

		if($sort=='weekdownloads')
			$request =  tp_query("SELECT dlm.id, dlm.description, dlm.author_id as authorID, dlm.name, dlm.category, dlm.file, dlm.downloads, dlm.views, dlm.author_id as authorID, dlm.icon, dlm.created, dlm.screenshot, dlm.filesize,
				dlcat.name AS catname, mem.real_name as realName
				FROM (" . $tp_prefix . "dlmanager AS dlm, " . $db_prefix . "members AS mem)
				LEFT JOIN " . $tp_prefix . "dlmanager AS dlcat ON dlcat.id=dlm.category
				WHERE dlm.type = 'dlitem'
				AND dlm.category IN (" . implode(',' , $mycats) . ")
				AND dlm.author_id=mem.id_member
				" . $sortstring . " LIMIT $number ", __FILE__, __LINE__);
		else	
			$request =  tp_query("SELECT dlm.id, dlm.description, dlm.author_id as authorID, dlm.name, dlm.category, dlm.file, dlm.downloads, dlm.views, dlm.author_id, dlm.icon, dlm.created, dlm.screenshot, dlm.filesize,
				dlcat.name AS catname, mem.real_name as realName
				FROM (" . $tp_prefix . "dlmanager AS dlm, " . $db_prefix . "members AS mem)
				LEFT JOIN " . $tp_prefix . "dlmanager AS dlcat ON dlcat.id=dlm.category
				WHERE dlm.type = 'dlitem'
				AND dlm.category IN (" . implode(',' , $mycats) . ")
				AND dlm.author_id=mem.id_member
				" . $sortstring . " LIMIT $number ", __FILE__, __LINE__);
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

				$context['TPortal']['dlrecenttp'][] = array(
					'id' => $row['id'],
					'body' => $row['description'],
					'name' => $row['name'],
					'category' => $row['category'],
					'file' => $row['file'],
					'href' => $scripturl.'?action=tpmod;dl=item'.$row['id'],
					'downloads' => $row['downloads'],
					'views' => $row['views'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['authorID'].'">'.$row['realName'].'</a>',
					'authorID' => $row['author_id'],
					'icon' => $row['icon'],
					'date' => timeformat($row['created']),
					'screenshot' => $ico ,
					'catname' => $row['catname'],
					'cathref' => $scripturl.'?action=tpmod;dl=cat'.$row['category'],
					'filesize' => $fs,
					);
			}
			tpdb_free_result($request);
		}
		if($type=='array')
			return $context['TPortal']['dlrecenttp'];
		else
		{
			echo '<div class="post">
					<ul>';
			foreach($context['TPortal']['dlrecenttp'] as $dl)
			{
				echo '<li><a href="'.$dl['href'].'">'.$dl['name'].'</a>';
				if($sort=='date')
					echo ' <small>[' . $dl['downloads'] . ']</small>';
				elseif($sort=='views')
					echo ' <small>[' . $dl['views'] . ']</small>';
				elseif($sort=='downloads')
					echo ' <small>[' . $dl['downloads'] . ']</small>';

				echo '</li>';
			}
			echo '</ul>
		</div>';
		}
	}
}
function dl_getcats()
{
	global $context, $settings, $db_prefix, $txt;

	if(isset($settings['tp_prefix']))
			$tp_prefix=$settings['tp_prefix'];
	else
	{
		$tp_prefix = $db_prefix.'tp_';
		$settings['tp_prefix'] = $tp_prefix;
	}

	$context['TPortal']['dl_allowed_cats'] = array();
	$request =  tp_query("SELECT id, parent, name, access FROM " . $tp_prefix . "dlmanager WHERE type = 'dlcat'", __FILE__, __LINE__);
	if(tpdb_num_rows($request)>0)
	{
		while ($row = tpdb_fetch_assoc($request))
		{
			$show = get_perm($row['access'], 'tp_dlmanager');
			if($show)
				$context['TPortal']['dl_allowed_cats'][$row['id']] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'parent' => $row['parent'],
					);
		}
		tpdb_free_result($request);
	}
}

function TP_bbcbox($form,$input,$body)
{
	global $context,$sourcedir, $settings, $txt;

   require_once($sourcedir . '/Subs-Editor.php');

   $editorOptions = array(
      'id' => $input,
      'value' => $body,
      'preview_type' => 2,
      'height' => '300px',
      'width' => '92%',
   );
   create_control_richedit($editorOptions);

   // Store the ID.
   $box = $editorOptions['id'];	
	
	
   echo'<div id="tp_messbox"></div>';
   echo'<div id="tp_smilebox"></div>';

   echo template_control_richedit($box, 'tp_messbox', 'tp_smilebox');

}

function tp_getblockstyles()
{
	return array(
				'0' => array(
					'class' => 'catbg+windowbg',
					'code_title_left' => '<h3 class="catbg"><span class="left"></span>',
					'code_title_right' => '</h3>',
					'code_top' => '<div class="windowbg"><div style="padding: 0 8px;">',
					'code_bottom' => '</div><span class="botslice"><span></span></span></div>',
				),
				'1' => array(
					'class' => 'titlebg+windowbg',
					'code_title_left' => '<h3 class="titlebg"><span class="left"></span>',
					'code_title_right' => '</h3>',
					'code_top' => '<div class="windowbg"><div style="padding: 0 8px;">',
					'code_bottom' => '</div><span class="botslice"><span></span></span></div>',
				),
				'2' => array(
					'class' => 'titlebg+windowbg(old)',
					'code_title_left' => '<h3 class="titlebg"><span class="left"></span>',
					'code_title_right' => '</h3>',
					'code_top' => '<div class="windowbg"><div style="padding: 8px;">',
					'code_bottom' => '</div></div>',
				),
				'3' => array(
					'class' => 'catbg+windowbg(old)',
					'code_title_left' => '<h3 class="catbg"><span class="left"></span>',
					'code_title_right' => '</h3>',
					'code_top' => '<div class="windowbg"><div style="padding: 8px;">',
					'code_bottom' => '</div></div>',
				),
				'4' => array(
					'class' => 'titlebg+windowbg',
					'code_title_left' => '<div class="tp_half"><h3 class="titlebg"><span class="l"></span><span class="r"></span>',
					'code_title_right' => '</h3></div>',
					'code_top' => '<div class="windowbg"><div style="padding: 8px 8px 0 8px;">',
					'code_bottom' => '</div><span class="botslice"><span></span></span></div>',
				),
				'5' => array(
					'class' => 'catbg+windowbg',
					'code_title_left' => '<div class="tp_half"><h3 class="catbg"><span class="l"></span><span class="r"></span>',
					'code_title_right' => '</h3></div>',
					'code_top' => '<div class="windowbg"><div style="padding: 8px 8px 0 8px;">',
					'code_bottom' => '</div><span class="botslice"><span></span></span></div>',
				),
				'6' => array(
					'class' => 'titlebg+windowbg2',
					'code_title_left' => '<div class="tp_half"><h3 class="titlebg"><span class="l"></span><span class="r"></span>',
					'code_title_right' => '</h3></div>',
					'code_top' => '<div class="windowbg2"><div style="padding: 8px 8px 0 8px;">',
					'code_bottom' => '</div><span class="botslice"><span></span></span></div>',
				),
				'7' => array(
					'class' => 'catbg+windowbg2',
					'code_title_left' => '<div class="tp_half"><h3 class="catbg"><span class="l"></span><span class="r"></span>',
					'code_title_right' => '</h3></div>',
					'code_top' => '<div class="windowbg2"><div style="padding: 8px 8px 0 8px;">',
					'code_bottom' => '</div><span class="botslice"><span></span></span></div>',
				),
				'8' => array(
					'class' => 'titlebg+roundframe',
					'code_title_left' => '<div class="tp_half"><h3 class="titlebg"><span class="l"></span><span class="r"></span>',
					'code_title_right' => '</h3></div>',
					'code_top' => '<div class="roundframe"><div style="padding: 8px 0 0 0;">',
					'code_bottom' => '</div></div><span class="lowerframe"><span></span></span>',
				),
				'9' => array(
					'class' => 'catbg+roundframe',
					'code_title_left' => '<div class="tp_half"><h3 class="catbg"><span class="l"></span><span class="r"></span>',
					'code_title_right' => '</h3></div>',
					'code_top' => '<div class="roundframe"><div style="padding: 8px 0 0 0;">',
					'code_bottom' => '</div></div><span class="lowerframe"><span></span></span>',
				),
			);

}

function get_grps($save = true, $noposts = true)
{
	global $context, $db_prefix, $txt;

	// get all membergroups for permissions
	$context['TPmembergroups'] = array();
	if($noposts)
	{
		$context['TPmembergroups'][] = array(
			'id' => '-1',
			'name' => $txt['tp-guests'],
			'posts' => '-1'
			);
		$context['TPmembergroups'][] = array(
			'id' => '0',
			'name' => $txt['tp-ungroupedmembers'],
			'posts' => '-1'
			);
	}
	$request = tp_query("
	SELECT id_group as ID_GROUP, group_name as groupName, min_posts as minPosts FROM " . $db_prefix . "membergroups WHERE " . ($noposts ? 'min_posts = \'-1\' AND ID_GROUP>1' : '1')  . " ORDER BY ID_GROUP", __FILE__, __LINE__);
	while ($row = tpdb_fetch_assoc($request))
	{
		$context['TPmembergroups'][] = array(
			'id' => $row['ID_GROUP'],
			'name' => $row['groupName'],
			'posts' => $row['minPosts']
			);
	}
	if($save)
		return $context['TPmembergroups'];
}
function tp_addcopy($buffer)
{
	global $context;

	$string = '<a target="_blank" href="http://custom.simplemachines.org/mods/index.php?mod=97" title="TinyPortal">TinyPortal 1.0 RC1</a> |  <a href="http://www.blocweb.net" target="_blank"> &copy; 2005-2010 BlocWeb</a>';

	if (empty($context['template_layers']) || WIRELESS || strpos($buffer, $string) !== false)
		return $buffer;

	$find = array(
		', Simple Machines LLC</a>',
		'class="copywrite"',
	);
	$replace = array(
		', Simple Machines LLC</a><br />' . $string,
		'class="copywrite" style="line-height: 1;"',
	);

	if (!in_array($context['current_action'], array('post', 'post2')))
	{
		$finds[] = '[cutoff]';
		$replaces[] = '';
	}

	$buffer = str_replace($find, $replace, $buffer);

	if (strpos($buffer, $string) === false)
	{
		$string = '<div style="text-align: center; width: 100%; font-size: x-small; margin-bottom: 5px;">' . $string . '</div></body></html>';
		$buffer = preg_replace('~</body>\s*</html>~', $string, $buffer);
	}

	return $buffer;
}
function tp_convertphp($code, $reverse = false)
{

	if(!$reverse)
	{
		return $code;
	}
	else
	{
		return $code;
	}
}
?>