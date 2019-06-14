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

    private $dBStructure = array();

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

    }}}

    public function getBlock( $block_id ) {{{


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
}

?>
