<?php
/**
 * @package TinyPortal
 * @version 3.0.3
 * @author tinoest - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) - The TinyPortal Team
 *
 */

namespace TinyPortal;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

class Block extends Base
{
	private $dBStructure = [];
	private $blockType = [];
	private $blockBar = [];
	private $blockPanel = [];
	private $blockDefault = [];
	private static $_instance = null;

	public static function getInstance()
	{
		if (self::$_instance == null) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	// Empty Clone method
	private function __clone()
	{
	}

	public function __construct()
	{
		parent::__construct();

		$this->dBStructure = [
			'id' => 'int',
			'type' => 'int',   // smallint
			'frame' => 'text',  // tinytext
			'title' => 'text',  // tinytext
			'body' => 'text',
			'access' => 'text',
			'bar' => 'int',   // smallint
			'pos' => 'int',
			'off' => 'int',   // smallint
			'visible' => 'text',
			'lang' => 'text',
			'display' => 'text',
			'settings' => 'text',
		];

		$this->blockType = [
			0 => 'no',
			1 => 'userbox',
			2 => 'newsbox',
			3 => 'statsbox',
			4 => 'searchbox',
			5 => 'html',
			6 => 'onlinebox',
			7 => 'themebox',
			8 => 'shoutbox',
			9 => 'catmenu',
			10 => 'phpbox',
			11 => 'scriptbox',
			12 => 'recentbox',
			13 => 'ssi',
			14 => 'module',
			15 => 'rss',
			16 => 'sitemap',
			17 => 'admin',
			18 => 'articlebox',
			19 => 'categorybox',
			21 => 'promotedbox',
		];

		$this->blockPanel = [
			'left',
			'right',
			'center',
			'top',
			'bottom',
			'lower',
			'front',
		];

		$this->blockBar = [
			1 => 'left',
			2 => 'right',
			3 => 'center',
			4 => 'front',
			5 => 'bottom',
			6 => 'top',
			7 => 'lower',
		];

		$this->blockDefault = [
			'1' => ['panelstyle' => 99],                                                                              // User
			'2' => ['panelstyle' => 99],                                                                              // News
			'3' => ['panelstyle' => 99],                                                                              // Stats
			'4' => ['panelstyle' => 99],                                                                              // Search
			'5' => ['panelstyle' => 99],                                                                              // HTML
			'6' => ['panelstyle' => 99, 'useavatar' => 0],                                                            // Online
			'7' => ['panelstyle' => 99],                                                                              // Theme
			'8' => ['panelstyle' => 99, 'shoutbox_id' => 1, 'shoutbox_layout' => 0, 'shoutbox_height' => 250, 'shoutbox_avatar' => 1, 'shoutbox_barposition' => 1, 'shoutbox_direction' => 0], // Shoutbox
			'9' => ['panelstyle' => 99, 'style' => 0],                                                                // Menu
			'10' => ['panelstyle' => 99],                                                                              // PHP
			'11' => ['panelstyle' => 99],                                                                              // Script
			'12' => ['panelstyle' => 99, 'useavatar' => 1, 'boards' => '', 'include' => 1, 'length' => 100, 'minmessagetopics' => 350], // Recent Topics
			'13' => ['panelstyle' => 99],                                                                              // SSI
			'14' => ['panelstyle' => 99],                                                                              // Module: Downloads/stats
			'15' => ['panelstyle' => 99, 'utf' => 1, 'showtitle' => 1, 'maxwidth' => '100%', 'maxshown' => 20],        // RSS
			'16' => ['panelstyle' => 99],                                                                              // Site Map
			'17' => ['panelstyle' => 99],                                                                              // Admin
			'18' => ['panelstyle' => 99],                                                                              // Article
			'19' => ['panelstyle' => 99, 'block_height' => 15, 'block_author' => 0],                                   // Categories
			'21' => ['panelstyle' => 99, 'useavatar' => 1, 'boards' => '', 'include' => 1, 'length' => 100],           // Promoted Topics
		];

		foreach ($this->blockType as $k => $v) {
			$name = 'TP_BLOCK_' . strtoupper($v);
			if (!defined($name)) {
				define($name, $k);
			}
		}
	}

	public function getBlockPermissions()
	{
		global $context, $user_info;

		$blocks = [];
		$user = $user_info['groups'];
		$activeBlocks = $this->getActiveBlocks();
		foreach ($activeBlocks as $block) {
			// Check group access
			if (allowedTo('tp_blocks') && (!empty($context['TPortal']['admin_showblocks']) || !isset($context['TPortal']['admin_showblocks']))) {
			}
			elseif (!isset($block['access'])) {
				continue;
			}
			elseif (isset($block['access']) && (strpos($block['access'], ',') === false) && (empty(array_intersect([$block['access']], $user)))) {
				continue;
			}
			elseif (empty(array_intersect($user, explode(',', $block['access'])))) {
				continue;
			}

			// check page settings
			$display = explode(',', $block['display']);
			if ($this->checkDisplayBlock($display) !== true) {
				continue;
			}

			$blocks[] = $block;
		}

		return $blocks;
	}

	public function checkDisplayBlock($display)
	{
		global $context, $user_info, $maintenance;

		// if we are in maintance mode, just hide panels
		if (!empty($maintenance) && !allowedTo('admin_forum')) {
			return false;
		}

		// for some very large forum sections, give the option to hide bars
		if ($context['TPortal']['hidebars_profile'] == '1' && $context['TPortal']['action'] == 'profile') {
			return false;
		}
		elseif ($context['TPortal']['hidebars_pm'] == '1' && $context['TPortal']['action'] == 'pm') {
			return false;
		}
		elseif ($context['TPortal']['hidebars_calendar'] == '1' && $context['TPortal']['action'] == 'calendar') {
			return false;
		}
		elseif ($context['TPortal']['hidebars_search'] == '1' && in_array($context['TPortal']['action'], ['search', 'search2'])) {
			return false;
		}
		elseif ($context['TPortal']['hidebars_memberlist'] == '1' && $context['TPortal']['action'] == 'mlist') {
			return false;
		}

		// if custom actions is specified, hide panels there as well
		if (!empty($context['TPortal']['hidebars_custom'])) {
			$cactions = explode(',', $context['TPortal']['hidebars_custom']);
			if (in_array($context['TPortal']['action'], $cactions)) {
				return false;
			}
		}

		// finally..wap modes should not display the bars
		if (isset($_GET['wap']) || isset($_GET['wap2']) || isset($_GET['imode'])) {
			return false;
		}

		// maybe we are at the password pages?
		if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['login2', 'profile2'])) {
			return false;
		}

		// Now we can actually check the block permissions
		$permissions = [];
		$permissions[] = 'allpages';

		if (!empty($_GET['action'])) {
			$permissions[] = preg_replace('/[^A-Za-z0-9]/', '', $_GET['action']);
			if (in_array($_GET['action'], ['forum', 'collapse', 'post', 'calendar', 'search', 'login', 'logout', 'register', 'unread', 'unreadreplies', 'recent', 'stats', 'pm', 'profile', 'post2', 'search2', 'login2'])) {
				$permissions[] = 'forumall';
			}
		}

		if (!empty($_GET['board'])) {
			if (!isset($_GET['action'])) {
				$permissions[] = 'board=-1';
			}
			$permissions[] = 'board=' . $_GET['board'];
			$permissions[] = 'forumall';
		}

		if (!empty($_GET['topic'])) {
			if (!isset($_GET['action'])) {
				$permissions[] = 'board=-1';
			}
			$permissions[] = 'topic=' . $_GET['topic'];
			$permissions[] = 'forumall';
		}

		if (!empty($_GET['dl']) && substr($_GET['dl'], 0, 3) == 'cat') {
			$permissions[] = 'dlcat=' . substr($_GET['dl'], 3);
		}

		// frontpage or boardindex
		if (!isset($_GET['action']) && !isset($_GET['board']) && !isset($_GET['topic']) && !isset($_GET['page']) && !isset($_GET['cat'])) {
			if ($context['TPortal']['front_active'] == '1') {
				$permissions[] = 'frontpage';
			}
			else {
				$permissions[] = 'forum';
				$permissions[] = 'forumall';
			}
		}

		$permissions[] = 'allpages';
		$permissions[] = !empty($_GET['page']) ? !empty($context['shortID']) ? 'tpage=' . $context['shortID'] : 'tpage=' . $_GET['page'] : '';
		$permissions[] = !empty($_GET['cat']) ? !empty($context['catshortID']) ? 'tpcat=' . $context['catshortID'] : 'tpcat=' . $_GET['cat'] : '';

		if (!empty($_GET['shout'])) {
			$permissions[] = 'tpmod=shout';
		}

		$check = false;

		foreach ($permissions as $permission) {
			if (in_array($permission, $display)) {
				// Is the language option enabled also?
				if (!empty($context['TPortal']['uselangoption'])) {
					if (in_array('tlang=' . $user_info['language'], $display)) {
						$check = true;
					}
				}
				else {
					$check = true;
				}
			}
		}

		return $check;
	}

	public function getActiveBlocks()
	{
		$blocks = [];

		$request = $this->dB->db_query(
			'',
			'
            SELECT * FROM {db_prefix}tp_blocks
            WHERE off = {int:off}
            ORDER BY bar, pos, id ASC',
			['off' => 0]
		);

		if ($this->dB->db_num_rows($request) > 0) {
			while ($block = $this->dB->db_fetch_assoc($request)) {
				$blocks[] = $block;
			}
		}

		$this->dB->db_free_result($request);

		return $blocks;
	}

	public function getBlocks()
	{
		$blocks = [];

		$request = $this->dB->db_query(
			'',
			'
            SELECT * FROM {db_prefix}tp_blocks
            WHERE 1=1',
			[]
		);

		if ($this->dB->db_num_rows($request) > 0) {
			while ($block = $this->dB->db_fetch_assoc($request)) {
				$blocks[] = $block;
			}
		}

		$this->dB->db_free_result($request);

		return $blocks;
	}

	public function getBlock($block_id)
	{
		if (empty($block_id)) {
			return;
		}

		$block = [];

		$request = $this->dB->db_query(
			'',
			'
            SELECT * FROM {db_prefix}tp_blocks
            WHERE id = {int:blockid} LIMIT 1',
			[
				'blockid' => $block_id
			]
		);

		if ($this->dB->db_num_rows($request) > 0) {
			$block = $this->dB->db_fetch_assoc($request);
		}

		return $block;
	}

	public function getBlockData($columns, $where)
	{
		return self::getSQLData($columns, $where, $this->dBStructure, 'tp_blocks');
	}

	public function insertBlock($block_data)
	{
		return self::insertSQL($block_data, $this->dBStructure, 'tp_blocks');
	}

	public function updateBlock($block_id, $block_data)
	{
		return self::updateSQL($block_id, $block_data, $this->dBStructure, 'tp_blocks');
	}

	public function deleteBlock($block_id)
	{
		return self::deleteSQL($block_id, 'tp_blocks');
	}

	public function getBlockType($type_id = null)
	{
		if (!is_null($type_id) && array_key_exists($type_id, $this->blockType)) {
			$types = $this->blockType[$type_id];
		}
		else {
			$types = $this->blockType;
		}

		return $types;
	}

	public function getBlockPanel($panel_id = null)
	{
		if (!is_null($panel_id) && array_key_exists($panel_id, $this->blockPanel)) {
			$panels = $this->blockPanel[$panel_id];
		}
		else {
			$panels = $this->blockPanel;
		}

		return $panels;
	}

	public function getBlockBar($bar_id = null)
	{
		if (!is_null($bar_id) && array_key_exists($bar_id, $this->blockBar)) {
			$bars = $this->blockBar[$bar_id];
		}
		else {
			$bars = $this->blockBar;
		}

		return $bars;
	}

	public function getBlockDefault($default_id = null)
	{
		if (!is_null($default_id) && array_key_exists($default_id, $this->blockDefault)) {
			$defaults = $this->blockDefault[$default_id];
		}
		else {
			$defaults = $this->blockDefault;
		}

		return $defaults;
	}

	public function getBlockBarId($bar_location = null)
	{
		if (!is_null($bar_location)) {
			$bars = array_search($bar_location, $this->blockBar);
		}
		else {
			$bars = $this->blockBar;
		}

		return $bars;
	}
}
