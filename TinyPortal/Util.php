<?php
/**
 * @package TinyPortal
 * @version 3.0.5
 * @author tinoest - https://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * Handles all TinyPortal Util operations
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) - The TinyPortal Team
 *
 * This file contains code covered by:
 * author: tinoest - https://tinoest.co.uk
 * license: BSD-3-Clause
 *
 */

namespace TinyPortal;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

// Static method to call smcFunc calls which are not database related.
class Util
{
	public const PUBLICATION_DATE_FORMAT = 'Y-m-d\TH:i';

	public static function parsePublicationDate($value, ?int $storedTimestamp, bool $optional = true): ?int
	{
		if ($optional && $value === '') {
			return null;
		}

		$format = self::PUBLICATION_DATE_FORMAT;
		if (is_string($value) && preg_match('/\A(?!0000)[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}\z/', $value)) {
			// Preserve the original instant, including seconds, when the displayed minute is unchanged.
			if ($storedTimestamp !== null && date($format, $storedTimestamp) === $value) {
				return $storedTimestamp;
			}

			$date = \DateTimeImmutable::createFromFormat('!' . $format, $value);
			if ($date !== false && $date->format($format) === $value) {
				return $date->getTimestamp();
			}
		}

		throw new \InvalidArgumentException('tp-publication-invalid');
	}

	public static function parsePublicationPeriod(array $input, array $fields, array $stored, ?int $unsetValue = null): array
	{
		$period = [];
		foreach ($fields as $setting => $field) {
			$timestamp = isset($stored[$setting]) ? (int) $stored[$setting] : null;
			if ($timestamp === $unsetValue) {
				$timestamp = null;
			}

			$period[$setting] = array_key_exists($field, $input)
				? self::parsePublicationDate($input[$field], $timestamp)
				: $timestamp;
		}

		if (isset($period['pub_start'], $period['pub_end']) && $period['pub_start'] >= $period['pub_end']) {
			throw new \InvalidArgumentException('tp-publication-order');
		}

		return $period;
	}

	public static function downloadIconUrl($path)
	{
		global $boardurl;

		if ($path === null || $path === '' || preg_match('~^(?:https?:)?//~i', $path)) {
			return $path ?? '';
		}

		return rtrim($boardurl, '/') . '/tp-downloads/icons/' . $path;
	}

	public static function __callStatic($call, $vars)
	{
		global $smcFunc;
		if (array_key_exists($call, $smcFunc)) {
			// It's faster to call directly, failover to call_user_func_array
			switch (count($vars)) {
				case 1:
					return $smcFunc[$call]($vars[0]);
					break;
				case 2:
					return $smcFunc[$call]($vars[0], $vars[1]);
					break;
				case 3:
					return $smcFunc[$call]($vars[0], $vars[1], $vars[2]);
					break;
				case 4:
					return $smcFunc[$call]($vars[0], $vars[1], $vars[2], $vars[3]);
					break;
				case 5:
					return $smcFunc[$call]($vars[0], $vars[1], $vars[2], $vars[3], $vars[4]);
					break;
				default:
					return call_user_func_array($smcFunc[$call], $vars);
					break;
			}
		}
		return false;
	}

	public static function positiveIds($values): array
	{
		if (is_string($values)) {
			$values = explode(',', $values);
		}
		if (!is_array($values)) {
			return [];
		}

		$ids = [];
		foreach ($values as $value) {
			if (!is_int($value) && !is_string($value)) {
				continue;
			}
			$id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
			if ($id !== false) {
				$ids[$id] = $id;
			}
		}

		return array_values($ids);
	}

	public static function find_in_set($data, $field, $arg = 'OR')
	{
		$dB = Database::getInstance();

		if (($arg != 'OR') && ($arg != 'AND')) {
			return;
		}

		if (is_null($data)) {
			return;
		}

		array_walk(
			$data,
			function (&$value, $key) use ($dB) {
				$value = $dB->db_quote('{string:value}', ['value' => $value]);
			}
		);

		$str = '';
		if (TP_PGSQL == false) {
			if ($arg == 'OR') {
				$str = '(FIND_IN_SET(' . implode(', ' . $field . ') ' . $arg . ' FIND_IN_SET(', $data) . ', ' . $field . '))';
			}
			else {
				$str = 'AND (FIND_IN_SET(' . implode(', ' . $field . ') OR FIND_IN_SET(', $data) . ', ' . $field . '))';
			}
		}
		else {
			$comparisons = [];
			foreach ($data as $value) {
				$comparisons[] = $value . " = ANY (string_to_array(" . $field . ", ','))";
			}

			$condition = empty($comparisons) ? 'FALSE' : implode(' OR ', $comparisons);
			$str = ($arg === 'AND' ? 'AND ' : '') . '(' . $condition . ')';
		}

		return $str;
	}

	public static function http_parse_query($queryString, $argSeparator = '&', $decType = PHP_QUERY_RFC1738)
	{
		$result = [];
		$parts = explode($argSeparator, $queryString);

		foreach ($parts as $part) {
			list($paramName, $paramValue) = explode('=', $part, 2);

			switch ($decType) {
				case PHP_QUERY_RFC3986:
					$paramName = rawurldecode($paramName);
					$paramValue = rawurldecode($paramValue);
					break;

				case PHP_QUERY_RFC1738:
				default:
					$paramName = urldecode($paramName);
					$paramValue = urldecode($paramValue);
					break;
			}

			if (preg_match_all('/\[([^\]]*)\]/m', $paramName, $matches)) {
				$paramName = self::substr($paramName, 0, strpos($paramName, '['));
				$keys = array_merge([$paramName], $matches[1]);
			}
			else {
				$keys = [$paramName];
			}

			$target = &$result;

			foreach ($keys as $index) {
				if ($index === '') {
					if (isset($target)) {
						if (is_array($target)) {
							$intKeys = array_filter(array_keys($target), 'is_int');
							$index = count($intKeys) ? max($intKeys) + 1 : 0;
						}
						else {
							$target = [$target];
							$index = 1;
						}
					}
					else {
						$target = [];
						$index = 0;
					}
				}
				elseif (isset($target[$index]) && !is_array($target[$index])) {
					$target[$index] = [$target[$index]];
				}

				$target = &$target[$index];
			}

			if (is_array($target)) {
				$target[] = $paramValue;
			}
			else {
				$target = $paramValue;
			}
		}

		return $result;
	}

	public static function checkboxChecked($checkbox)
	{
		return self::filter($checkbox, 'post', 'string');
	}

	public static function shortenString(&$string, $length, $renderedHtml = false)
	{
		if ((int) $length <= 0) {
			return false;
		}

		if ($renderedHtml || self::isHTML($string)) {
			if (class_exists('\DOMDocument')) {
				$dom = new \DOMDocument('1.0', 'UTF-8');
				$loaded = $dom->loadHTML(
					'<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>'
					. $string . '</body></html>',
					LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
				);
				$body = $loaded ? $dom->getElementsByTagName('body')->item(0) : null;

				if ($body !== null) {
					$cutOffPos = self::wordCutoff($body->textContent, $length);
					if ($cutOffPos === false) {
						return false;
					}

					$reachedLimit = false;
					$totalLen = 0;
					$toRemove = [];
					self::walkHTML($body, $cutOffPos, $reachedLimit, $totalLen, $toRemove);
					foreach ($toRemove as $child) {
						$child->parentNode->removeChild($child);
					}

					$string = '';
					foreach ($body->childNodes as $child) {
						$string .= $dom->saveHTML($child);
					}
					return true;
				}
			}

			// Preserve line breaks in a plain-text excerpt if DOM is unavailable.
			$text = html_entity_decode(
				strip_tags(preg_replace('~<br\s*/?>~i', "\n", $string)),
				ENT_QUOTES | ENT_HTML5,
				'UTF-8'
			);
			$cutOffPos = self::wordCutoff($text, $length);
			if ($cutOffPos === false) {
				return false;
			}

			$string = nl2br(htmlspecialchars(
				mb_substr($text, 0, $cutOffPos, 'UTF-8'),
				ENT_QUOTES | ENT_SUBSTITUTE,
				'UTF-8'
			));
			return true;
		}

		// Normalize the same entities as before looking for a space.
		$string = preg_replace('/&nbsp;|&zwnj;|&raquo;|&laquo;|&gt;/', ' ', $string);

		// Spaces inside BBCode tag attributes are part of the markup.
		$boundaryText = preg_replace_callback(
			'~\[/?[a-zA-Z0-9_-]+(?:[= ][^\]]*)?\]~',
			static function ($match) {
				return str_replace(' ', '_', $match[0]);
			},
			$string
		);
		$cutOffPos = self::wordCutoff($boundaryText, $length);
		if ($cutOffPos === false) {
			return false;
		}

		// Keep complete BBCode spans that cross the cutoff.
		$prefix = mb_substr($string, 0, $cutOffPos, 'UTF-8');
		if (preg_match_all('~\[(/?)([a-zA-Z0-9_-]+)(?:[= ][^\]]*)?\]~', $prefix, $tags, PREG_SET_ORDER)) {
			$openTags = [];
			foreach ($tags as $tag) {
				$name = strtolower($tag[2]);
				$openTags[$name] = ($openTags[$name] ?? 0) + ($tag[1] === '/' ? -1 : 1);
			}
			foreach ($openTags as $name => $count) {
				$tagEnd = $cutOffPos;
				while ($count-- > 0) {
					$closingTag = '[/' . $name . ']';
					$closingPos = mb_stripos($string, $closingTag, $tagEnd, 'UTF-8');
					if ($closingPos === false) {
						break;
					}
					$tagEnd = $closingPos + strlen($closingTag);
				}
				$cutOffPos = max($cutOffPos, $tagEnd);
			}

			$cutOffPos = mb_strpos($boundaryText, ' ', $cutOffPos, 'UTF-8');
			if ($cutOffPos === false) {
				return false;
			}
		}

		$string = mb_substr($string, 0, $cutOffPos, 'UTF-8');
		return true;
	}

	private static function wordCutoff($text, $length)
	{
		if (mb_strlen($text, 'UTF-8') <= $length) {
			return false;
		}

		$prefix = mb_substr($text, 0, (int) $length + 1, 'UTF-8');
		$space = mb_strrpos($prefix, ' ', 0, 'UTF-8');
		if ($space !== false && trim(mb_substr($prefix, 0, $space, 'UTF-8')) !== '') {
			return $space;
		}

		// Let a first word longer than the limit run to its next space.
		$firstWordEnd = mb_strpos(ltrim($text, ' '), ' ', 0, 'UTF-8');
		return $firstWordEnd === false ? false : strlen($text) - strlen(ltrim($text, ' ')) + $firstWordEnd;
	}

	public static function walkHTML(\DomNode $node, $length, &$reachedLimit, &$totalLen, &$toRemove)
	{
		if ($reachedLimit == true) {
			$toRemove[] = $node;
		}
		else {
			if ($node instanceof \DomText) {
				$text = $node->nodeValue;
				$remaining = $length - $totalLen;
				$nodeLen = mb_strlen($text, 'UTF-8');

				if ($nodeLen > $remaining) {
					$node->nodeValue = mb_substr($text, 0, $remaining, 'UTF-8');
				}

				$totalLen += $nodeLen;
				$reachedLimit = $totalLen >= $length;
			}

			if (isset($node->childNodes)) {
				foreach ($node->childNodes as $child) {
					self::walkHTML($child, $length, $reachedLimit, $totalLen, $toRemove);
				}
			}
		}

		return;
	}

	public static function parseBBC($string)
	{
		if (preg_match_all('/\[([a-zA-Z=0-9_\-]+?)\](.+?)\[\/\1\]/', $string, $matches) > 0) {
			return $matches;
		}

		return false;
	}

	public static function isHTML($string)
	{
		// Remove any HTML which might be in bbc html tags for this check, this means bbc with html will break the shortenString function
		$string = preg_replace('/\[([html]+?)\](.+?)\[\/\1\]/', '', $string);

		return preg_match("~\/[a-z]*>~i", $string) != 0;
	}

	public static function hasLinks($string)
	{
		if (empty($string)) {
			return false;
		}

		$pattern = '%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i';
		if (preg_match_all($pattern, $string, $matches, PREG_PATTERN_ORDER)) {
			return true;
		}

		return false;
	}

	public static function xssClean($string)
	{
		// URL decode
		$string = urldecode($string);
		// Convert Hexadecimals
		$string = preg_replace_callback('!(&#|\\\)[xX]([0-9a-fA-F]+);?!', function ($m) {
			return chr(hexdec($m[2]));
		}, $string);
		// Clean up entities
		$string = preg_replace('!(&#0+[0-9]+)!', '$1;', $string);
		// Decode entities
		$string = html_entity_decode($string, ENT_NOQUOTES, 'UTF-8');
		// Strip whitespace characters
		$string = preg_replace('!\s!', '', $string);
		// Set the patterns we'll test against
		$patterns = [
			// Match any attribute starting with "on" or xmlns
			'#(<[^>]+[\x00-\x20\"\'\/])(on|xmlns)[^>]*>?#iUu',
			// Match javascript:, livescript:, vbscript: and mocha: protocols
			'!((java|live|vb)script|mocha|feed|data):(\w)*!iUu',
			'#-moz-binding[\x00-\x20]*:#u',
			// Match style attributes
			'#(<[^>]+[\x00-\x20\"\'\/])style=[^>]*>?#iUu',
			// Match unneeded tags
			'#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>?#i'
		];

		foreach ($patterns as $pattern) {
			$string = preg_replace($pattern, '', $string);
		}

		if (!empty($string)) {
			return $string;
		}
		else {
			return false;
		}
	}

	public static function filter($key, $type, $filterType = 'string', $options = [])
	{
		switch ($type) {
			case 'get':
				$data = $_GET;
				break;
			case 'post':
				$data = $_POST;
				break;
			case 'request':
				$data = $_REQUEST;
				break;
			default:
				return false;
				break;
		}

		if (!array_key_exists($key, $data)) {
			return false;
		}

		return filter_var($data[$key], self::filterType($filterType), $options);
	}

	private static function filterType($type)
	{
		switch (strtolower($type)) {
			case 'string':
				$filter = FILTER_UNSAFE_RAW;
				break;
			case 'int':
				$filter = FILTER_SANITIZE_NUMBER_INT;
				break;
			case 'float':
			case 'decimal':
				$filter = FILTER_SANITIZE_NUMBER_FLOAT;
				break;
			case 'encoded':
				$filter = FILTER_SANITIZE_ENCODED;
				break;
			case 'url':
				$filter = FILTER_SANITIZE_URL;
				break;
			case 'email':
				$filter = FILTER_SANITIZE_EMAIL;
				break;
			default:
				$filter = FILTER_UNSAFE_RAW;
		}
		return $filter;
	}

	public static function substr($string, $offset, $length = null)
	{
		if (function_exists('mb_substr')) {
			return mb_substr($string, $offset, $length);
		}

		return substr($string, $offset, $length);
	}
}
