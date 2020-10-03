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

class Mentions extends Base {

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
    }}}

    public function addJS() {{{
        // Mentions
        if (!empty($this->modSettings['enable_mentions']) && allowedTo('mention')) {
            loadJavaScriptFile('jquery.atwho.min.js',               array('defer' => true, 'minimize' => false), 'tp_atwho');
            loadJavaScriptFile('jquery.caret.min.js',               array('defer' => true, 'minimize' => false), 'tp_caret');
            loadJavaScriptFile('tinyportal/tinyPortalMentions.js',  array('defer' => true, 'minimize' => false), 'tp_mentions');
        }
    }}}

    public function getMention( $mention_id ) {{{

    }}}

    public function addMention( $mention ) {{{
        if (!empty($this->modSettings['enable_mentions'])) {
            require_once(SOURCEDIR . '/Subs-Post.php');
            require_once(SOURCEDIR . '/Mentions.php');
            $mentions = \Mentions::getMentionedMembers($mention['content']);
            if (is_array($mentions)) {
                \Mentions::insertMentions($mention['type'], $mention['id'], $mentions, $mention['member_id']);
                $mention['content'] = \Mentions::getBody($mention['content'], $mentions);
                foreach($mentions as $id => $member) {
                    $insert_rows[] = array(
                        'alert_time'        => time(),
                        'id_member'         => $member['id'],
                        'id_member_started' => $mention['member_id'],
                        'member_name'       => $mention['username'],
                        'content_type'      => $mention['type'],
                        'content_id'        => $mention['id'],
                        'content_action'    => $mention['action'],
                        'is_read'           => 0,
                        'extra' => Util::json_encode(
                            array (
                                "text"          => $mention['text'],
                                "user_mention"  => $mention['username'],
                                "event_title"   => $mention['event_title'],
                            )
                        ),
                    );

                    $this->dB->db_insert('insert',
                        '{db_prefix}user_alerts',
                        array(  
                            'alert_time'        => 'int', 
                            'id_member'         => 'int',
                            'id_member_started' => 'int',
                            'member_name'       => 'string',
                            'content_type'      => 'string',
                            'content_id'        => 'int',
                            'content_action'    => 'string',
                            'is_read'           => 'int',
                            'extra'             => 'string'
                            ),
                        $insert_rows,
                        array('id_alert')
                    );
                    updateMemberData($member['id'], ['alerts' => '+']);
                }
            }
        }
    }}}

    public function removeMention( $mention_id ) {{{

    }}}
}

?>
