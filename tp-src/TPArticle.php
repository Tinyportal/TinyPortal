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

    public function getArticle($article_id)
    {

        $now        = time();
        $where      = is_numeric( $article_id ) ? 'art.id = {int:page}' : 'art.shortname = {string:page}';

        $request    = $this->dB->db_query('', '
            SELECT 
                art.*, art.author_id AS author_id, art.id_theme AS id_theme, var.value1, var.value2,
                var.value3, var.value4, var.value5, var.value7, var.value8, art.type AS rendertype, mem.email_address AS email_address,
                COALESCE(mem.real_name,art.author) AS real_name, mem.avatar, mem.posts, mem.date_registered AS date_registered, mem.last_login AS last_login,
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
            array ( 
                'page' => is_numeric( $article_id ) ? (int) $article_id : $article_id
            )
        );

        $article = array();

        if($this->dB->db_num_rows($request) > 0) {
            $article = $this->dB->db_fetch_assoc($request);
            $this->dB->db_free_result($request);
        }

        return $article;

    }

    public function getArticleComments($user_id, $item_id) 
    {
       return parent::getComments('1', $user_id, $item_id); 
    }

    public function insertArticleComment($user_id, $item_id, $comment, $title)
    {
        return parent::insertComment('1', $user_id, $item_id, $comment, $title);
    }

    public function updateArticleViews($article_id)
    {

        // update views
        $this->dB->db_query('', '
            UPDATE {db_prefix}tp_articles
            SET views = views + 1
            WHERE ' . (is_numeric($article_id) ? 'id = {int:page}' : 'shortname = {string:page}'),
            array (
                'page' => $article_id
            )
        );

    }

    public function updateArticle($article_data)
    {

    }

    public function insertArticle($article_data)
    {

    }

    public function deleteArticle($article_id)
    {

    }

    public function getTotalAuthorArticles($author_id)
    {
        $num_articles   = 0;
        $request        = $this->dB->db_query('', '
            SELECT COUNT(id) AS articles FROM {db_prefix}tp_articles
            WHERE author_id = {int:author}
            AND off = 0',
            array(
                'author' => $author_id
            )
        );
        if($this->dB->db_num_rows($request) > 0) {
            $num_articles = $this->dB->db_fetch_assoc($request)['articles'];
            $this->dB->db_free_result($request);
        }

        return $num_articles;
    }

}

?>
