<?php
/*
Plugin Name: Permanent Tourist Must Use
Plugin URI: #
Description: This WordPress plugin contains all of the blocks and portable functionality for permanenttourist.ch.
Author: Mark Howells-Mead
Version: 0.3.2
Author URI: https://www.permanenttourist.ch/
Text Domain: pt-must-use
Domain Path: languages
*/

namespace PT\MustUse;

/*
	 * This lot auto-loads a class or trait just when you need it. You don't need to
	 * use require, include or anything to get the class/trait files, as long
	 * as they are stored in the correct folder and use the correct namespaces.
	 *
	 * See http://www.php-fig.org/psr/psr-4/ for an explanation of the file structure
	 * and https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md for usage examples.
	 */

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

require_once 'src/Plugin.php';

function pt_must_use_get_instance()
{
	return Plugin::getInstance(__FILE__);
}

pt_must_use_get_instance();
