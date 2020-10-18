<?php
/**
 * Handles all TPBase operations
 *
 * @name      	TinyPortal
 * @package 	TPBase
 * @copyright 	TinyPortal
 * @license   	MPL 1.1
 *
 * This file contains code covered by:
 * author: tinoest - https://tinoest.co.uk
 * license: BSD-3-Clause 
 *
 * @version 1.0.0
 *
 */
namespace TinyPortal;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

define('ARTICLE_COMMENT', 1);

class Base 
{
	protected $dB = null;
    protected $modSettings = null;

	function __construct() {{{
        global $modSettings;

		if(is_null($this->dB)) {
			$this->dB = Database::getInstance();
		}

        $this->modSettings = $modSettings;

	}}}

    protected function getComments($type, $user_id, $item_id) {{{

        // fetch and update last access by member(to log which comment is new)
        $now        = time();
        $last       = $now;
        $request    = $this->dB->db_query('', '
            SELECT item FROM {db_prefix}tp_data
            WHERE id_member = {int:id_mem}
            AND type = {int:type}
            AND value = {int:val} LIMIT 1',
            array (
                'id_mem'    => $user_id, 
                'type'      => $type, 
                'val'       => $item_id
            )
        );

        if($this->dB->db_num_rows($request) > 0) {
            $last   = $this->dB->db_fetch_assoc($request)['item'];
            $this->dB->db_free_result($request);
            $this->dB->db_query('', '
                UPDATE {db_prefix}tp_data
                SET item = {int:item}
                WHERE id_member = {int:id_member}
                AND type = {int:type}
                AND value = {int:val}',
                array(
                    'item'      => $now,
                    'id_member' => $user_id,
                    'type'      => $type,
                    'val'       => $item_id
                )
            );
        }
        else {
            $this->dB->db_insert('INSERT',
                '{db_prefix}tp_data',
                array (
                    'type'      => 'int',
                    'id_member' => 'int',
                    'value'     => 'int',
                    'item'      => 'int'
                ),
                array ( 
                    $type,
                    $user_id,
                    $item_id,
                    $now
                ),
                array ('id')
            );
        }

        // fetch any comments
        $request =  $this->dB->db_query('', '
            SELECT c.id AS id, c.subject AS subject, c.comment AS comment, c.datetime AS datetime, c.member_id AS member_id,
            COALESCE(mem.real_name, \'\') AS real_name, mem.avatar,
            COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type AS attachment_type, mem.email_address AS email_address
            FROM {db_prefix}tp_comments AS c
            LEFT JOIN {db_prefix}members AS mem ON ( c.member_id'. ( ( TP_PGSQL == true ) ? '::Integer' : ' ' ) .' = mem.id_member)
            LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
            WHERE c.item_type = {string:type}
            AND c.item_id = {int:item_id}
            ORDER BY c.datetime ASC',
            array (
                'type'      => 'article_comment',
                'item_id'   => $item_id
            )
        );

        $comment_count  = 0;
        $new_count      = 0;
        if($this->dB->db_num_rows($request) > 0) {
            while($row = $this->dB->db_fetch_assoc($request)) {
                $comments[] = $row;
                $comment_count++;
                if($row['datetime'] > $last) {
                    $new_count++;
                }
            }
        }

        $comments['new_count']      = $new_count;
        $comments['comment_count']  = $comment_count;
        $comments['last']           = $last;

        return $comments;

    }}}

    protected function getComment($comment_id, $item_type) {{{

        // fetch any comments
        $request =  $this->dB->db_query('', '
            SELECT c.id AS id, c.item_id AS item_id, c.subject AS subject, c.comment AS comment, c.datetime AS datetime, c.member_id AS member_id
            FROM {db_prefix}tp_comments AS c
            WHERE c.item_type = {string:type}
            AND c.id = {int:id}',
            array (
                'type'  => $item_type,
                'id'    => $comment_id
            )
        );

        $comment = false;

        if($this->dB->db_num_rows($request) > 0) {
            $comment = $this->dB->db_fetch_assoc($request);
        }

        return $comment;
    }}}

    protected function insertComment($type, $user_id, $item_id, $comment, $title) {{{

        $time = time();

        // insert the comment
        $this->dB->db_insert('INSERT',
            '{db_prefix}tp_comments',
            array (
                'subject'   => 'string', 
                'comment'   => 'string', 
                'member_id' => 'int', 
                'item_type' => 'string', 
                'datetime'  => 'int',
                'item_id'   => 'int', 
            ),
            array(
                $title,
                $comment,
                $user_id,
                'article_comment',
                $time,
                $item_id
            ),
            array('id')
        );

        return $this->dB->db_insert_id('{db_prefix}tp_comments', 'id');

    }}}

    protected function deleteComment($id, $type) {{{
        
        $this->dB->db_query('', '
			DELETE FROM {db_prefix}tp_comments
			WHERE id = {int:id}
            AND item_type = {string:type}',
			array(
				'id'    => $id,
                'type'  => $type
			)
		);

    }}}
    
    protected function getSQLData( $columns, $where, $dBStructure, $table ) {{{

        $values = null;

        if(empty($columns)) {
            return $values;
        }
        elseif(is_array($columns)) {
            foreach($columns as $column) {
                if(!array_key_exists($column, $dBStructure)) {
                    return $values;
                }
            }
            $columns = implode(',', $columns);
        }
        else {
            if(!array_key_exists($columns, $dBStructure)) {
                return $values;
            }
        }

        if(empty($where)) {
            $where      = '1=1';
        }
        elseif(is_array($where)) {
            $where_data = array();
            foreach($where as $key => $value) {
                if(array_key_exists($key, $dBStructure)) {
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
            FROM {db_prefix}'.$table.'
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

   protected function insertSQL(array $data, array $dBStructure, string $table) {{{
        $insert_data = array();
        
        foreach(array_keys($data) as $key) {
            $insert_data[$key] = $dBStructure[$key];
        }

        $this->dB->db_insert('INSERT',
            '{db_prefix}'.$table,
            $insert_data,
            array_values($data),
            array ('id')
        );
			
        return $this->dB->db_insert_id('{db_prefix}'.$table, 'id');

    }}}

     protected function updateSQL(int $id, array $data, array $dBStructure, string $table) {{{

        $update_data = $data;
        array_walk($update_data, function(&$update_data, $key) use ( $dBStructure ) {
                $update_data = $key.' = {'.$dBStructure[$key].':'.$key.'}';
            }
        );
        $update_query = implode(', ', array_values($update_data));

        $data['id'] = (int)$id;
        return $this->dB->db_query('', '
            UPDATE {db_prefix}'.$table.'
            SET '.$update_query.'
            WHERE id = {int:id}',
            $data
        );

    }}}

    protected function deleteSQL( int $delete_id, string $table ) {{{

        return $this->dB->db_query('', '
            DELETE FROM {db_prefix}'.$table.'
            WHERE id = {int:id}',
            array (
                'id' => $delete_id
            )
        );

    }}}

}

?>
