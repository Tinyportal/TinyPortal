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


clASs TPArticle extends TPBase {

    public function __construct() 
    {
        parent::__construct();
    }

    public function getArticle($articleID)
    {

        $now        = time();
        $where      = is_numeric( $articleID ) ? 'art.id = {int:page}' : 'art.shortname = {string:page}';

        $request    = $this->dB->db_query('', '
            SELECT 
                art.*, art.author_id AS author_id, art.id_theme AS id_theme, var.value1, var.value2,
                var.value3, var.value4, var.value5, var.value7, var.value8, art.type AS rendertype, mem.email_address AS email_address,
                COALESCE(mem.real_name,art.author) AS real_name, mem.avatar, mem.posts, mem.date_registered AS date_registered, mem.lASt_login AS lAStLogin,
                COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type AS attachement_type, var.value9, mem.email_address AS email_address
            FROM {db_prefix}tp_articles AS art
            LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = art.author_id)
            LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = art.author_id AND a.attachment_type != 3)
            LEFT JOIN {db_prefix}tp_variables AS var ON (var.id= art.category)
            WHERE '. $where . 
            (
                !allowedTo( 'tp_articles' ) ? '
                    AND ((art.pub_start = 0 AND art.pub_end = 0)
                    OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
                    OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
                    OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.')) ' 
                : ' ' 
            )
            .'LIMIT 1',
            array( 
                'page' => is_numeric( $articleID ) ? (int) $articleID : $articleID
            )
        );

        $article = array();

        if($this->dB->db_num_rows($request) > 0) {
            $article = $this->dB->db_fetch_assoc($request);
            $this->dB->db_free_result($request);
        }

        return $article;

    }

    public function updateArticle($articleData)
    {

    }

    public function insertArticle($articleData)
    {

    }

    public function deleteArticle($articleID)
    {

    }

}

?>
