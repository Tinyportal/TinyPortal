<?php
/**
 * @package TinyPortal
 * @version 2.0.0
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

if (!defined('SMF'))
	die('Hacking attempt...');


class TPBlock extends TPBase {

    private $dBStructure    = array();
    private $blockType      = array();
    private $blockBar       = array();
    private $blockPanel     = array();

    public function __construct() {{{
        parent::__construct();

        $this->dbStructure = array (
            'id'            => 'int',
            'type'          => 'smallint',
            'frame'         => 'tinytext',
            'title'         => 'tinytext',
            'body'          => 'text',
            'access'        => 'text',
            'bar'           => 'smallint',
            'pos'           => 'int',
            'off'           => 'smallint',
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
            8   => 'oldshoutbox',
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
            20  => 'tpmodulebox',
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
            0 => 'left',
            1 => 'right',
            2 => 'center',
            3 => 'front',
            4 => 'bottom',
            5 => 'top',
            6 => 'lower',
        );


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

   public function insertBlock($block_data) {{{
        $insert_data = array();
        foreach(array_keys($block_data) as $key) {
            $insert_data[$key] = $this->dBStructure[$key];
        }

        $this->dB->db_insert('INSERT',
            '{db_prefix}tp_blocks',
            $insert_data,
            array_values($block_data),
            array ('id')
        );
			
        return $this->dB->db_insert_id('{db_prefix}tp_blocks', 'id');

    }}}

     public function updateBlock($block_id, $block_data) {{{

        $update_data = $block_data;
        array_walk($update_data, function(&$update_data, $key) {
                $update_data = $key.' = {'.$this->dBStructure[$key].':'.$key.'}';
            }
        );
        $update_query = implode(', ', array_values($update_data));
        $block_data['block_id'] = (int)$block_id;
        $this->dB->db_query('', '
            UPDATE {db_prefix}tp_blocks
            SET '.$update_query.'
            WHERE id = {int:block_id}',
            $block_data
        );

    }}}

    public function deleteBlock( $block_id ) {{{

        $this->dB->db_query('', '
            DELETE FROM {db_prefix}tp_blocks
            WHERE id = {int:block_id}',
            array (
                'block_id' => $block_id
            )
        );

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

        if(!is_null($bar_location) && array_search($bar_location, $this->blockBar)) {
            $bars = array_search($bar_location, $this->blockBar);
        }
        else {
            $bars = $this->blockBar;
        }

        return $bars;

    }}}
}

?>
