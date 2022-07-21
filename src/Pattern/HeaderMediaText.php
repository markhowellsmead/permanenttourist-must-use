<?php

namespace PT\MustUse\Pattern;

/**
 * Manage single block pattern
 *
 * @author Say Hello GmbH <hello@sayhello.ch>
 */
class HeaderMediaText
{

	public function run()
	{
		add_filter('init', [$this, 'register'], 1, 1);
	}

	public function register()
	{
		ob_start();
		include(plugin_dir_path(pt_must_use_get_instance()->file) . 'partials/patterns/header-media-text.php');
		$content = ob_get_contents();
		ob_end_clean();

		register_block_pattern(
			'sht/header-media-text',
			[
				'title' => __('Header with media and text', 'sht'),
				'categories' => ['header'],
				'content' => $content,
			]
		);
	}
}
