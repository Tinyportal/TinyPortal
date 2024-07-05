<?php
/**
 * Handles all TinyPortal Upload operations
 *
 * @name        TinyPortal
 * @package     Upload
 * @copyright   TinyPortal
 * @license     MPL 1.1
 *
 * This file contains code covered by:
 * author: tinoest - https://tinoest.co.uk
 * license: BSD-3-Clause
 *
 * @version 3.0.1
 *
 */

namespace TinyPortal;

if (!defined('SMF')) {
	die('Hacking attempt...');
}

class Upload
{
	private static $_instance = null;
	private $allowed_mime_types = [];
	private $errors = [];
	private $max_file_size = 1024;
	private $allowed_chars = 'a-z0-9_.-';
	private $mime_types = [
		'txt' => 'text/plain',
		'htm' => 'text/html',
		'html' => 'text/html',
		'php' => 'text/html',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'xml' => 'application/xml',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',

		// images
		'png' => 'image/png',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'ico' => 'image/vnd.microsoft.icon',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',

		// archives
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		'exe' => 'application/x-msdownload',
		'msi' => 'application/x-msdownload',
		'cab' => 'application/vnd.ms-cab-compressed',

		// audio/video
		'mp3' => 'audio/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',

		// adobe
		'pdf' => 'application/pdf',
		'psd' => 'image/vnd.adobe.photoshop',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',

		// ms office
		'doc' => 'application/msword',
		'rtf' => 'application/rtf',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',

		// open office
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	];

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

	private function set_error(int $err_num)
	{
		$this->errors[] = $err_num;
	}

	public function get_error(bool $last = false)
	{
		if ($last === true) {
			return end($this->errors);
		}
		else {
			return $this->errors;
		}
	}

	public function clear_error()
	{
		$this->errors = [];
	}

	public function set_mime_types(array $mime_types, bool $reset = false)
	{
		if ($reset === true) {
			$this->allowed_mime_types = [];
		}

		foreach ($mime_types as $type) {
			if (array_key_exists($type, $this->mime_types)) {
				$this->allowed_mime_types[] = $this->mime_types[$type];
			}
		}
	}

	public function set_max_file_size(int $file_size)
	{
		$this->max_file_size = $file_size;
	}

	public function set_allowed_chars(string $chars)
	{
		$this->allowed_chars = $chars;
	}

	public function check_mime_type(string $filename)
	{
		$mime_type = '';

		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mime_type = finfo_file($finfo, $filename);
			finfo_close($finfo);
		}
		elseif (function_exists('mime_content_type')) {
			$mime_type = mime_content_type($filename);
		}

		if (!empty($mime_type) && strpos($mime_type, ';')) {
			list($mime_type, ) = explode(';', $mime_type);
		}

		if (in_array($mime_type, $this->allowed_mime_types)) {
			return true;
		}

		return false;
	}

	public function check_file_size(string $filename)
	{
		if (filesize($filename) > (1024 * $this->max_file_size)) {
			return false;
		}

		return true;
	}

	public function check_filename(string $filename)
	{
		return preg_replace('/[^' . $this->allowed_chars . ']/i', '_', $filename);
	}

	public function check_directory_exists(string $directory)
	{
		if (!is_dir($directory)) {
			return false;
		}

		return true;
	}

	public function check_file_exists(string $filename)
	{
		if (!file_exists($filename)) {
			return false;
		}

		return true;
	}

	public function generate_filename(string $directory)
	{
		$conflict = true;

		if (!self::check_directory_exists($directory)) {
			self::set_error(201);
			return false;
		}

		$generated_filename = sha1(mt_rand(1, 9999) . uniqid()) . time();
		if (self::check_file_exists($directory . '/' . $generated_filename) === true) {
			self::set_error(202);
			return false;
		}

		return $generated_filename;
	}

	public function upload_file(string $source, string $destination)
	{
		// Check File Exists
		if (!file_exists($source)) {
			self::set_error(100);
			return false;
		}

		// Check File was uploaded
		if (!is_uploaded_file($source)) {
			self::set_error(101);
			return false;
		}

		if (!self::check_filename($source)) {
			self::set_error(102);
			return false;
		}

		// Check file size is allowed
		if (!self::check_file_size($source)) {
			self::set_error(103);
			return false;
		}

		// Check mime type is allowed
		if (!self::check_mime_type($source)) {
			self::set_error(104);
			return false;
		}

		if (!self::move_file($source, $destination)) {
			// Let's try move instead
			if (move_uploaded_file($source, $destination) != true) {
				self::set_error(105);
				return false;
			}
		}

		return true;
	}

	public function move_file(string $source, string $destination)
	{
		// Check File Exists
		if (!file_exists($source)) {
			self::set_error(300);
			return false;
		}

		// Check destination exists
		if (!is_dir(dirname($destination))) {
			self::set_error(301);
			return false;
		}

		// Check the destination is writable
		if (!is_writable(dirname($destination))) {
			self::set_error(302);
			return false;
		}

		// Make sure a file doesn't already exist
		if (is_file($destination)) {
			self::set_error(303);
			return false;
		}

		$handle = fopen($source, 'r+b');
		$resource = fopen($destination, 'w+b');
		if (stream_copy_to_stream($handle, $resource) === false) {
			self::set_error(304);
			return false;
		}
		fclose($resource);
		fclose($handle);

		return true;
	}

	public function remove_file(string $filename)
	{
		if (!file_exists($filename)) {
			self::set_error(801);
			return false;
		}

		if (!is_writable($filename)) {
			self::set_error(802);
			return false;
		}

		if (unlink($filename) == false) {
			self::set_error(803);
			return false;
		}

		return true;
	}
}
