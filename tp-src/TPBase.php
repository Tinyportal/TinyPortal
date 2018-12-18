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
if (!defined('SMF')) {
	die('Hacking attempt...');
}


class TPBase 
{
	protected $dB = null;

	function __construct()
	{

		if(is_null($this->dB)) {
			$this->dB = new TPortalDB();
		}

	}

    protected function getComments($type, $user_id, $item_id)
    {

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
            SELECT var.* , COALESCE(mem.real_name, \'\') AS real_name, mem.avatar,
            COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type AS attachment_type, mem.email_address AS email_address
            FROM {db_prefix}tp_variables AS var
            LEFT JOIN {db_prefix}members AS mem ON ( var.value3'. ( ( PGSQL == true ) ? '::Integer' : ' ' ) .' = mem.id_member)
            LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = mem.id_member)
            WHERE var.type = {string:type}
            AND var.value5 = {int:val5}
            ORDER BY var.value4 ASC',
            array (
                'type' => 'article_comment',
                'val5' => $item_id
            )
        );

        $comment_count  = 0;
        $new_count      = 0;
        if($this->dB->db_num_rows($request) > 0) {
            while($row = $this->dB->db_fetch_assoc($request)) {
                $comments[] = $row;
                $comment_count++;
                if($row['value4'] > $last) {
                    $new_count++;
                }
            }
        }

        $comments['new_count']      = $new_count;
        $comments['comment_count']  = $comment_count;
        $comments['last']           = $last;

        return $comments;

    }

    protected function insertComment($type, $user_id, $item_id, $title, $comment)
    {

		// check if the article indeed exists
		$request =  $this->dB->db_query('', '
            SELECT comments FROM {db_prefix}tp_articles
            WHERE id = {int:artid}',
            array (
                'artid' => $item_id
            )
        );
		if($this->dB->db_num_rows($request) > 0) {
			$num_comments   = $this->dB->db_fetch_assoc($request)['comments'];
			$this->dB->db_free_result($request);
			$time = time();

			// insert the comment
			$this->dB->db_insert('INSERT',
                '{db_prefix}tp_variables',
                array (
                    'value1' => 'string', 
                    'value2' => 'string', 
                    'value3' => 'string', 
                    'type' => 'string', 
                    'value4' => 'string', 
                    'value5' => 'int'
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

            $num_comments++;
			// count and increase the number of comments
			$this->dB->db_query('', '
                UPDATE {db_prefix}tp_articles
                SET comments = {int:com}
                WHERE id = {int:artid}',
                array (
                    'com'   => $num_comments,
                    'artid' => $item_id
                )
            );

            return true;
		}

        return false;

    }


}

?>
