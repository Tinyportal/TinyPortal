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

class Article extends Base
{

    private static $_instance   = null;
    private $dBStructure        = array();

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
            'date'          => 'int',
            'body'          => 'string',
            'intro'         => 'string',
            'useintro'      => 'int',
            'category'      => 'int',
            'frontpage'     => 'int',
            'subject'       => 'string',
            'author_id'     => 'int',
            'author'        => 'string',
            'frame'         => 'string',
            'approved'      => 'int',
            'off'           => 'int',
            'options'       => 'string',
            'parse'         => 'int',
            'comments'      => 'int',
            'comments_var'  => 'string',
            'views'         => 'int',
            'rating'        => 'string',
            'voters'        => 'string',
            'id_theme'      => 'int',
            'shortname'     => 'string',
            'sticky'        => 'int',
            'fileimport'    => 'string',
            'topic'         => 'int',
            'locked'        => 'int',
            'illustration'  => 'string',
            'headers'       => 'string',
            'type'          => 'string',
            'featured'      => 'int',
            'pub_start'     => 'int',
            'pub_end'       => 'int',
        );

    }}}

    public function getArticle($article) {{{

        if(empty($article)) {
            return;
        }

        $now        = time();
        if(is_array($article)) {
            $where      = 'art.id IN ({array_string:page})';
        }
        else {
            $where      = is_numeric( $article ) ? 'art.id = {int:page}' : 'art.shortname = {string:page}';
            $article    = is_numeric( $article ) ? (int)$article : $article;
        }

        $request    = $this->dB->db_query('', '
            SELECT
                art.*, art.author_id AS author_id, art.id_theme AS id_theme, var.value1 AS category_name, var.value2,
                var.value3, var.value4, var.value5, var.value7, var.value8 AS category_shortname, art.type AS rendertype, mem.email_address AS email_address,
                COALESCE(mem.real_name,art.author) AS real_name, mem.avatar, mem.posts, mem.date_registered AS date_registered, mem.last_login AS last_login,
                COALESCE(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type AS attachment_type, var.value9, mem.email_address AS email_address
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
            ),
            array (
                'page' => $article
            )
        );

        $articles = array();

        if($this->dB->db_num_rows($request) > 0) {
            while ( $row = $this->dB->db_fetch_assoc($request)) {
                $articles[] = $row;
            }
            $this->dB->db_free_result($request);
        }

        return $articles;

    }}}

    public function getArticleData( $columns, $where ) {{{

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
            FROM {db_prefix}tp_articles
            WHERE {raw:where}',
            array (
                'columns'       => $columns,
                'where'         => $where,
            )
        );

        if($this->dB->db_num_rows($request) > 0) {
            while ( $value = $this->dB->db_fetch_assoc($request) ) {
                $values = $value;
            }
        }

        return $values;

    }}}

    public function getArticleComments($user_id, $item_id) {{{
       return parent::getComments('1', $user_id, $item_id);
    }}}

    public function getArticleComment($comment_id) {{{
       return parent::getComment($comment_id, 'article_comment');
    }}}

    public function insertArticleComment($user_id, $item_id, $comment, $title) {{{

        $comment_id = 0;

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
            $comment_id = parent::insertComment('1', $user_id, $item_id, $comment, $title);

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
        }

        return $comment_id;
    }}}

    public function deleteArticleComment($comment_id) {{{

		// check if the article indeed exists
		$request =  $this->dB->db_query('', '
            SELECT item_id FROM {db_prefix}tp_comments
            WHERE id = {int:artid}',
            array (
                'artid' => $comment_id
            )
        );

		if($this->dB->db_num_rows($request) > 0) {
			$article_id = $this->dB->db_fetch_assoc($request)['item_id'];
			$this->dB->db_free_result($request);
            // check if the article indeed exists
            $request =  $this->dB->db_query('', '
                SELECT comments FROM {db_prefix}tp_articles
                WHERE id = {int:artid}',
                array (
                    'artid' => $article_id
                )
            );

            if($this->dB->db_num_rows($request) > 0) {
			    $num_comments   = $this->dB->db_fetch_assoc($request)['comments'];
			    $this->dB->db_free_result($request);

                $num_comments--;
                // count and decrease the number of comments
                $this->dB->db_query('', '
                    UPDATE {db_prefix}tp_articles
                    SET comments = {int:com}
                    WHERE id = {int:artid}',
                    array (
                        'com'   => $num_comments,
                        'artid' => $article_id
                    )
                );
            }
        }

        return parent::deleteComment($comment_id, 'article_comment');
    }}}

    public function updateArticleViews($article_id) {{{

        // update views
        $this->dB->db_query('', '
            UPDATE {db_prefix}tp_articles
            SET views = views + 1
            WHERE ' . (is_numeric($article_id) ? 'id = {int:page}' : 'shortname = {string:page}'),
            array (
                'page' => $article_id
            )
        );

    }}}

   public function insertArticle($article_data) {{{

        return self::insertSQL($article_data, $this->dBStructure, 'tp_articles');

    }}}

     public function updateArticle($article_id, $article_data) {{{

        return self::updateSQL($article_id, $article_data, $this->dBStructure, 'tp_articles');

    }}}

    public function deleteArticle( $article_id ) {{{

        return self::deleteSQL($article_id, 'tp_articles');

    }}}

    public function toggleColumnArticle($article_id, $column) {{{

        // We can only toggle certain fields so check that the column is in the list
        if(in_array($column, array('off', 'locked', 'sticky', 'frontpage', 'featured'))) {
			if(TP_SMF21 == FALSE) {
				global $modSettings;
				$modSettings['disableQueryCheck'] = true;
			}
			if($article_id > 0) {
				$this->dB->db_query('', '
						UPDATE {db_prefix}tp_articles
						SET {raw:column} =
						(
						 	SELECT CASE WHEN tpa.{raw:column} = 1 THEN 0 ELSE 1 END
						 	FROM ( SELECT * FROM {db_prefix}tp_articles ) AS tpa
							WHERE tpa.id = {int:id}
						 	LIMIT 1
						)
						WHERE id = {int:id}',
					array (
						'id' 		=> $article_id,
						'column' 	=> $column
					)

				);
			}
			if(TP_SMF21 == FALSE) {
				$modSettings['disableQueryCheck'] = true;
			}
        }


    }}}

    public function getTotalAuthorArticles($author_id, $off = false, $approved = true) {{{

        $where          = '';
        $num_articles   = 0;

        if($off == true) {
            $where .= ' AND off = 1 ';
        }

        if($approved == false) {
            $where .= ' AND approved = 0 ';
        }

        $request        = $this->dB->db_query('', '
            SELECT COUNT(id) AS articles FROM {db_prefix}tp_articles
            WHERE author_id = {int:author}
            '.$where,
            array(
                'author' => $author_id
            )
        );
        if($this->dB->db_num_rows($request) > 0) {
            $num_articles = $this->dB->db_fetch_assoc($request)['articles'];
            $this->dB->db_free_result($request);
        }

        return $num_articles;
    }}}

    public function getTotalArticles( $group = '' ) {{{
        $num_articles   = 0;
        $now            = time();

		$request =  $this->dB->db_query('', '
			SELECT COUNT(art.id) AS num_articles
			FROM {db_prefix}tp_articles AS art
            INNER JOIN  {db_prefix}tp_variables AS var
			ON var.id = art.category
			WHERE art.off = 0
			' . $group . '
			AND art.category > 0
			AND ((art.pub_start = 0 AND art.pub_end = 0)
			OR (art.pub_start != 0 AND art.pub_start < '.$now.' AND art.pub_end = 0)
			OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '.$now.')
			OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '.$now.' AND art.pub_start < '.$now.'))
			AND art. approved = 1
			AND art.frontpage = 1'
		);

        if($this->dB->db_num_rows($request) > 0) {
            $num_articles = $this->dB->db_fetch_assoc($request)['num_articles'];
            $this->dB->db_free_result($request);
        }

        return $num_articles;
    }}}

    public function getArticlesInCategory( $category, $active = true, $approved = true ) {{{

        if(is_array($category)) {
            $where = 'category IN ({array_int:cat})';
        }
        else {
            $where = 'category = {int:cat}';
        }

        if( $active == false ) {
            $where .= ' AND off = 0 ';
        }
        else {
            $where .= ' AND off = 1 ';
        }

        if( $approved == false ) {
            $where .= ' AND approved = 0 ';
        }
        else {
            $where .= ' AND approved = 1 ';
        }

        $articles   = array();
        $request    =  $this->dB->db_query('', '
            SELECT id, subject, date, category, author_id, shortname, author
            FROM {db_prefix}tp_articles
            WHERE 1=1 AND '.$where.'
            ORDER BY date DESC',
            array(
                'cat' => $category,
            )
        );

        if($this->dB->db_num_rows($request) > 0) {
            while($row = $this->dB->db_fetch_assoc($request)) {
                if(empty($row['shortname'])) {
                    $row['shortname'] = $row['id'];
                }
                $articles[] = $row;
            }
        }
        $this->dB->db_free_result($request);

        return $articles;

    }}}

	public function getForumPosts( $post_ids ) {{{
		global $context, $memberContext, $txt, $scripturl;

		$forumPosts = $posts = array();
		// ok we got the post ids now, fetch each one, forum first
		if(count($post_ids) > 0) {
			$forumPosts = ssi_fetchPosts($post_ids, true, 'array');
		}

		// insert the forumposts into $posts
		if(is_array($forumPosts) && count($forumPosts) > 0) {
			// Needed for html_to_bbc
			require_once(SOURCEDIR . '/Subs-Editor.php');

			$length = $context['TPortal']['frontpage_limit_len'];
			foreach($forumPosts as $k => $row) {
				$row['date']            = $row['timestamp'];
				$row['real_name']       = $row['poster']['name'];
				$row['author_id']       = $row['poster']['id'];
				$row['category']        = $row['board']['name'];
				$row['date_registered'] = 0;
				$row['id']              = $row['topic'];
				$row['category_name']   = $row['board']['name'];
				$row['category']        = $row['board']['id'];

				$request =  $this->dB->db_query('', '
                    SELECT t.num_views AS views, t.num_replies AS replies, t.locked, COALESCE(thumb.id_attach, 0) AS thumb_id, thumb.filename AS thumb_filename
                    FROM {db_prefix}topics AS t
                    LEFT JOIN {db_prefix}attachments AS thumb
                    ON ( t.id_first_msg = thumb.id_msg AND thumb.attachment_type = 3 )
                    WHERE t.id_topic = ({int:id})',
                    array(
                        'id' => $row['id'],
                    )
                );

				$data                   = $this->dB->db_fetch_assoc($request);
				$row['views']           = isset($data['views']) ? $data['views'] : 0;
				$row['replies']         = isset($data['replies']) ? $data['replies'] : 0;
				$row['locked']          = isset($data['locked']) ? $data['locked'] : 0;
				$row['thumb_id']        = isset($data['thumb_id']) ? $data['thumb_id'] : 0;
				$row['thumb_filename']  = isset($data['thumb_filename']) ? $data['thumb_filename'] : 0;
				$this->dB->db_free_result($request);

                $row['parsed_bbc']      = true;

				// Load their context data.
				loadMemberData($row['author_id']);
				loadMemberContext($row['author_id']);

				// Store this member's information.
				if(!is_null($memberContext) && array_key_exists($row['author_id'], $memberContext)) {
					$avatar         = $memberContext[$row['author_id']];
					$row['avatar']  = $avatar['avatar']['image'];
				}
				else {
					$row['avatar']  = '';
				}

				if(Util::shortenString($row['body'], $context['TPortal']['frontpage_limit_len'])) {
					$row['readmore'] = '... <p class="tp_readmore"><strong><a href="'. $scripturl. '?topic='. $row['id']. '">'. $txt['tp-readmore']. '</a></strong></p>';
				}

				// some needed addons
				$row['rendertype'] = 'bbc';
				$row['frame'] = 'theme';
				$row['boardnews'] = 1;

				if(!isset($context['TPortal']['frontpage_visopts'])) {
					$context['TPortal']['frontpage_visopts'] = 'date,title,author,views' . ($context['TPortal']['forumposts_avatar'] == 1 ? ',avatar' : '');
				}

				$row['visual_options'] = explode(',', $context['TPortal']['frontpage_visopts']);
				$row['useintro'] = '0';

				if(!empty($row['thumb_id'])) {
					$row['illustration'] = $scripturl . '?action=tportal;sa=tpattach;topic=' . $row['id'] . '.0;attach=' . $row['thumb_id'] . ';image';
				}

				$posts[$row['timestamp'].'0' . sprintf("%06s", $row['id'])] = $row;
			}
		}

		return $posts;

	}}}

}

?>
