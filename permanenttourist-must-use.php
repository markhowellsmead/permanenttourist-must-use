<?php
/*
Plugin Name: Permanent Tourist Must Use
Plugin URI: #
Description: This WordPress plugin contains all of the blocks and portable functionality for permanenttourist.ch.
Author: Mark Howells-Mead
Version: 0.27.3
Author URI: https://www.permanenttourist.ch/
Text Domain: pt-must-use
Domain Path: languages
*/

if (!function_exists('dump')) {
	function dump($var, $exit = false)
	{
		echo '<pre>' . print_r($var, true) . '</pre>';
		if ($exit) {
			exit;
		}
	}
}

spl_autoload_register(function ($class) {

	// project-specific namespace prefix
	$prefix = 'PT\\MustUse\\';

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/src/';

	// does the class use the namespace prefix?
	$len = strlen($prefix);

	if (strncmp($prefix, $class, $len) !== 0) {
		// no, move to the next registered autoloader
		return;
	}

	// get the relative class name
	$relative_class = substr($class, $len);

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

	// if the file exists, require it
	if (file_exists($file)) {
		require $file;
	}
});

/**
 * Returns the Theme Instance
 *
 * @return Object Theme Object
 */
if (!function_exists('pt_must_use_get_instance')) {
	function pt_must_use_get_instance()
	{
		return PT\MustUse\Plugin::getInstance(__FILE__);
	}
}

pt_must_use_get_instance();
