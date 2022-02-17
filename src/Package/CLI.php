<?php

namespace PT\MustUse\Package;

use WP_CLI;

/**
 * CLI stuff
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class CLI
{

	public function run()
	{
		if (class_exists('WP_CLI')) {
			WP_CLI::add_command('mhm photo topost', 'PT\MustUse\CLI\PhotoToPost');
			WP_CLI::add_command('mhm collection totag', 'PT\MustUse\CLI\CollectionToTag');
		}
	}
}
