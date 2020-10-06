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

class Shout extends Base {

    private $dBStructure        = array();
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
            'shoutbox_id'   => 'int',
            'content'       => 'text', 
            'time'          => 'text',
            'member_link'   => 'text',
            'member_ip'     => 'text',
            'member_id'     => 'int',
            'type'          => 'text',  // tinytext
            'sticky'        => 'int',   // smallint
            'sticky_layout' => 'text',
            'edit'          => 'text',
        );

    }}}

    public function getShouts( $shoutbox_id = null ) {{{

        $shout = array();

        if(!is_null($shoutbox_id)) {
            $where          = '{int:shoutbox_id}';
            $where_array    = array( 'shoutbox_id' => $shoutbox_id );
        }
        else {
            $where          = '1=1';
            $where_array    = array( );
        }

        $request =  $this->dB->db_query('', '
            SELECT * FROM {db_prefix}tp_shoutbox
            WHERE '.$where,
            $where_array
        );

        if($this->dB->db_num_rows($request) > 0) {
            while ( $shout = $this->dB->db_fetch_assoc($request) ) {
                $shout[] = $shout;
            }
        }

        $this->dB->db_free_result($request);

        return $shout;

    }}}

    public function getShout( $shout_id ) {{{

        if(empty($shout_id)) {
            return;
        }

        $shout = array();

        $request =  $this->dB->db_query('', '
            SELECT * FROM {db_prefix}tp_shoutbox
            WHERE id = {int:shoutid} LIMIT 1',
            array (
                'shoutid' => $shout_id
            )
        );

        if($this->dB->db_num_rows($request) > 0) {
            $shout = $this->dB->db_fetch_assoc($request);
        }

        return $shout;

    }}}

    public function getShoutData( $columns, $where ) {{{

        $values = null;

        if(empty($columns)) {
            return $values;
        }
        elseif(is_array($columns)) {
            foreach($columns as $column) {
                if(!array_key_exists($column, $this->dBStructure)) {
                    return $values;
                }
            }
            $columns = implode(',', $columns);
        }
        else {
            if(!array_key_exists($columns, $this->dBStructure)) {
                return $values;
            }
        }

        if(empty($where)) {
            $where      = '1=1';
        }
        elseif(is_array($where)) {
            $where_data = array();
            foreach($where as $key => $value) {
                if(array_key_exists($key, $this->dBStructure)) {
                    $where_data[] = $key.' = '.$value;
                }
                elseif(strpos($key, '!') === 0) {
                    $where_data[] = substr($key, strpos($key, '!') + 1).' != '.$value; 
                }
            }
            $where = implode(' AND ', array_values($where_data));
        }
        else {
            return $values;
        }

        $request =  $this->dB->db_query('', '
            SELECT {raw:columns}
            FROM {db_prefix}tp_shoutbox
            WHERE {raw:where}',
            array (
                'columns'       => $columns,
                'where'         => $where,
            )
        );

        if($this->dB->db_num_rows($request) > 0) {
            while ( $value = $this->dB->db_fetch_assoc($request) ) {
                $values[] = $value;
            }
        }

        return $values;

    }}}

   public function insertShout($shout_data) {{{
        $insert_data = array();
        foreach(array_keys($shout_data) as $key) {
            $insert_data[$key] = $this->dBStructure[$key];
        }

        $this->dB->db_insert('INSERT',
            '{db_prefix}tp_shoutbox',
            $insert_data,
            array_values($shout_data),
            array ('id')
        );
			
        return $this->dB->db_insert_id('{db_prefix}tp_shoutbox', 'id');

    }}}

     public function updateShout($shout_id, $shout_data) {{{

        $update_data = $shout_data;
        array_walk($update_data, function(&$update_data, $key) {
                $update_data = $key.' = {'.$this->dBStructure[$key].':'.$key.'}';
            }
        );
        $update_query = implode(', ', array_values($update_data));

        $shout_data['shout_id'] = (int)$shout_id;
        $this->dB->db_query('', '
            UPDATE {db_prefix}tp_shoutbox
            SET '.$update_query.'
            WHERE id = {int:shout_id}',
            $shout_data
        );

    }}}

    public function deleteShout( $shout_id ) {{{

        $this->dB->db_query('', '
            DELETE FROM {db_prefix}tp_shoutbox
            WHERE id = {int:shout_id}',
            array (
                'shout_id' => $shout_id
            )
        );

    }}}

}

?>
