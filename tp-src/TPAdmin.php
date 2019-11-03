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


class TPAdmin extends TPBase {

    private $dBStructure    = array();
    private $tpSettings     = array();

    public function __construct() {{{
        parent::__construct();

        $this->dbStructure = array (
            'id'        => 'mediumint',
            'name'      => 'text',
            'value'     => 'text',
        );
        
        $this->tpSettings = $this->getSetting();

    }}}

    public function getSetting( $setting_name = null , $refresh = false ) {{{

        if($refresh == false && !is_null($setting_name) && array_key_exists($setting_name, $this->tpSettings)) {
            return $this->tpSettings[$setting_name];
        }

        $settings = array();

        if(empty($setting_name)) {
            $request =  $this->dB->db_query('', '
                SELECT name, value 
                FROM {db_prefix}tp_settings
                WHERE 1=1'
            );

            if($this->dB->db_num_rows($request) > 0) {
                while($row = $this->dB->db_fetch_assoc($request)) {
                    $settings[$row['name']] = $row['value'];
                }
            }
        }
        else {
            $request =  $this->dB->db_query('', '
                SELECT value FROM {db_prefix}tp_settings
                WHERE name = {string:setting_name} LIMIT 1',
                array (
                    'setting_name' => $setting_name
                )
            );

            if($this->dB->db_num_rows($request) > 0) {
                $row                        = $this->dB->db_fetch_assoc($request);
                $settings[$setting_name]    = $row['value'];
                return $row['value'];
            }
        }

        return $settings;

    }}}

   public function insertSetting($setting_data) {{{
        $insert_data = array();
        foreach(array_keys($setting_data) as $key) {
            $insert_data[$key] = $this->dBStructure[$key];
        }

        $this->dB->db_insert('INSERT',
            '{db_prefix}tp_settings',
            $insert_data,
            array_values($settings_data),
            array ('id')
        );
			
        return $this->dB->db_insert_id('{db_prefix}tp_settings', 'id');

    }}}

     public function updateSetting($setting_id, $setting_data) {{{

        $update_data = $setting_data;
        array_walk($update_data, function(&$update_data, $key) {
                $update_data = $key.' = {'.$this->dBStructure[$key].':'.$key.'}';
            }
        );
        $update_query = implode(', ', array_values($update_data));
        $block_data['id'] = (int)$setting_id;
        $this->dB->db_query('', '
            UPDATE {db_prefix}tp_settings
            SET '.$update_query.'
            WHERE id = {int:setting_id}',
            $setting_data
        );

    }}}

    public function deleteSetting( $setting_id ) {{{

        $this->dB->db_query('', '
            DELETE FROM {db_prefix}tp_settings
            WHERE id = {int:setting_id}',
            array (
                'setting_id' => $setting_id
            )
        );

    }}}

}

?>
