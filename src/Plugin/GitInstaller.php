<?php

namespace PT\MustUse\Plugin;

/**
 * GitInstaller
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class GitInstaller
{

	public function run()
	{
		add_filter('shgi/Repositories/MustUsePlugins', '__return_true');
	}
}
