<?php

namespace PT\MustUse\Block;

/**
 * Show how many posts there are
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class CountPosts
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('mhm/count-posts', [
			'render_callback' => [$this, 'renderBlock']
		]);
	}

	public function renderBlock($attributes)
	{
		global $wpdb;
		$post_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'");
		$first_year = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date ASC");
		return sprintf(
			'<p class="wp-block-mhm-count-posts">%s</p>',
			sprintf(
				__('%1$s blog posts published since %2$s', 'pt-must-use'),
				'<span class="wp-block-mhm-count-posts__count">' . number_format($post_count) . '</span>',
				'<span class="wp-block-mhm-count-posts__year">' . date_i18n('Y', strtotime($first_year)) . '</span>'
			)
		);
	}
}
