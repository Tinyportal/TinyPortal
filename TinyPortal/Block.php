<?php
/**
 * @package TinyPortal
 * @version 2.1.0
 * @author tinoest - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2018 - The TinyPortal Team
 *
 */
namespace TinyPortal;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

class Block extends Base {

    private $dBStructure        = array();
    private $blockType          = array();
    private $blockBar           = array();
    private $blockPanel         = array();
    private static $_instance   = null;

    public static function getInstance() {{{
	
    	if(self::$_instance == null) {
			self::$_instance = new self();
		}
	
    	return self::$_instance;
	
    }}}

    // Empty Clone method
    private function __clone() { }

    public function __construct() {{{
        parent::__construct();

        $this->dBStructure = array (
            'id'            => 'int',
            'type'          => 'int',   // smallint
            'frame'         => 'text',  // tinytext
            'title'         => 'text',  // tinytext
            'body'          => 'text',
            'access'        => 'text',
            'bar'           => 'int',   // smallint
            'pos'           => 'int',   
            'off'           => 'int',   // smallint
            'visible'       => 'text',
            'lang'          => 'text',
            'access2'       => 'text',
            'editgroups'    => 'text',
            'settings'      => 'text',
        );

        $this->blockType = array( 
            0   => 'no',
            1   => 'userbox', 
            2   => 'newsbox',
            3   => 'statsbox',
            4   => 'searchbox',
            5   => 'html',
            6   => 'onlinebox',
            7   => 'themebox',
            8   => 'shoutbox',
            9   => 'catmenu',
            10  => 'phpbox',
            11  => 'scriptbox',
            12  => 'recentbox',
            13  => 'ssi',
            14  => 'module',
            15  => 'rss',
            16  => 'sitemap',
            17  => 'admin',
            18  => 'articlebox',
            19  => 'categorybox',
        );

        $this->blockPanel = array(
            'left',
            'right',
            'center',
            'top',
            'bottom',
            'lower',
            'front',
        );
        
        $this->blockBar = array(
            1 => 'left',
            2 => 'right',
            3 => 'center',
            4 => 'front',
            5 => 'bottom',
            6 => 'top',
            7 => 'lower',
        );

        foreach($this->blockType as $k => $v) {
            $name = 'TP_BLOCK_'.strtoupper($v);
            if(!defined($name)) {
               define($name, $k);
            }
        }


    }}}


    public function getBlockPermissions( ) {{{
        global $context, $user_info;

        // construct the spot we are in
        $sqlarray = array();
        // any action?
        if(!empty($_GET['action'])) {
            $sqlarray[] = 'actio=' . preg_replace('/[^A-Za-z0-9]/', '', $_GET['action']);
            if(in_array($_GET['action'], array('forum', 'collapse', 'post', 'calendar', 'search', 'login', 'logout', 'register', 'unread', 'unreadreplies', 'recent', 'stats', 'pm', 'profile', 'post2', 'search2', 'login2'))) {
                $sqlarray[] = 'actio=forumall';
            }
        }

        if(!empty($_GET['board'])) {
            if(!isset($_GET['action'])) {
                $sqlarray[] = 'board=-1';
            }   
            $sqlarray[] = 'board=' . $_GET['board']; 
            $sqlarray[] = 'actio=forumall';
        }       

        if(!empty($_GET['topic'])) {
            if(!isset($_GET['action'])) {
                $sqlarray[] = 'board=-1';
            }       
            $sqlarray[] = 'topic=' . $_GET['topic'];
            $sqlarray[] = 'actio=forumall';
        }       

        if(!empty($_GET['dl']) && substr($_GET['dl'], 0, 3) == 'cat') {
            $sqlarray[] = 'dlcat=' . substr($_GET['dl'], 3);
        }

        // frontpage
        if(!isset($_GET['action']) && !isset($_GET['board']) && !isset($_GET['topic']) && !isset($_GET['page']) && !isset($_GET['cat'])) {
            $sqlarray[] = 'actio=frontpage';
        }       

        $sqlarray[] = 'actio=allpages';
        $sqlarray[] = !empty($_GET['page']) ? !empty($context['shortID']) ? 'tpage=' . $context['shortID'] : 'tpage=' . $_GET['page'] : '';
        $sqlarray[] = !empty($_GET['cat']) ? !empty($context['catshortID']) ? 'tpcat=' . $context['catshortID'] : 'tpcat=' . $_GET['cat'] : '';

        if(!empty($_GET['shout'])) {
            $sqlarray[] = 'tpmod=shout';
        }           

        $access = Util::find_in_set($user_info['groups'], 'access');

        if(allowedTo('tp_blocks') && (!empty($context['TPortal']['admin_showblocks']) || !isset($context['TPortal']['admin_showblocks']))) {
            $access = '1=1';
        }           

        $access2 = Util::find_in_set($sqlarray, 'access2');
        $access3 = '';
        if(!empty($context['TPortal']['uselangoption'])) {
            $access3 = Util::find_in_set(array('tlang='.$user_info['language']), 'access2');
            if(isset($access3)) {
                $access3 = ' AND '. $access3;
            }       
        }           

        // get the blocks
        $request = $this->dB->db_query('', '
            SELECT * FROM {db_prefix}tp_blocks
            WHERE off = 0
            AND bar != {int:bar}
            AND (' . $access2 . ')
            AND ' . $access . ' ' . $access3 . '
            ORDER BY bar, pos, id ASC',
            array(
                'bar' => 4,
            )
        );

        $block = array();

        if ($this->dB->db_num_rows($request) > 0) {
		    while($row = $this->dB->db_fetch_assoc($request)) {
                $blocks[] = $row;
            }
        }

		$this->dB->db_free_result($request);

        return $blocks;

    }}}

    public function getBlocks( ) {{{

        $blocks = array();

        $request =  $this->dB->db_query('', '
            SELECT * FROM {db_prefix}tp_blocks
            WHERE 1=1',
            array()
        );

        if($this->dB->db_num_rows($request) > 0) {
            while ( $block = $this->dB->db_fetch_assoc($request) ) {
                $blocks[] = $block;
            }
        }

        $this->dB->db_free_result($request);

        return $blocks;

    }}}

    public function getBlock( $block_id ) {{{

        if(empty($block_id)) {
            return;
        }

        $block = array();

        $request =  $this->dB->db_query('', '
            SELECT * FROM {db_prefix}tp_blocks
            WHERE id = {int:blockid} LIMIT 1',
            array (
                'blockid' => $block_id
            )
        );

        if($this->dB->db_num_rows($request) > 0) {
            $block = $this->dB->db_fetch_assoc($request);
        }

        return $block;

    }}}

    public function getBlockData( $columns, $where ) {{{

        return self::getSQLData($columns, $where, $this->dBStructure, 'tp_blocks');

    }}}

   public function insertBlock($block_data) {{{

        return self::insertSQL($block_data, $this->dBStructure, 'tp_blocks');

    }}}

     public function updateBlock($block_id, $block_data) {{{

        return self::updateSQL($block_id, $block_data, $this->dBStructure, 'tp_blocks');

    }}}

    public function deleteBlock( $block_id ) {{{

        return self::deleteSQL($block_id, 'tp_blocks');

    }}}

    public function getBlockType( $type_id = null ) {{{

        if(!is_null($type_id) && array_key_exists($type_id, $this->blockType)) {
            $types = $this->blockType[$type_id];
        }
        else {
            $types = $this->blockType;
        }

        return $types;

    }}}

    public function getBlockPanel( $panel_id = null ) {{{

        if(!is_null($panel_id) && array_key_exists($panel_id, $this->blockPanel)) {
            $panels = $this->blockPanel[$panel_id];
        }
        else {
            $panels = $this->blockPanel;
        }

        return $panels;

    }}}

    public function getBlockBar( $bar_id = null ) {{{

        if(!is_null($bar_id) && array_key_exists($bar_id, $this->blockBar)) {
            $bars = $this->blockBar[$bar_id];
        }
        else {
            $bars = $this->blockBar;
        }

        return $bars;

    }}}

    public function getBlockBarId( $bar_location = null ) {{{

        if(!is_null($bar_location)) {
            $bars = array_search($bar_location, $this->blockBar);
        }
        else {
            $bars = $this->blockBar;
        }

        return $bars;

    }}}
}

?>
