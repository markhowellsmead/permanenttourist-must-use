<?php

namespace SayHello\Theme\CLI;

use WP_CLI;
use WP_CLI_Command;

class PhotoToPost extends WP_CLI_Command
{

	public function __invoke()
	{
		$mt = microtime(true);

		$posts = get_posts([
			'posts_per_page' => -1,
			'post_type' => 'photo',
			'fields' => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		]);
		wp_reset_postdata();

		$count = count($posts);

		if (!$count) {
			WP_CLI::success('No posts of type “photo” found to convert.');
			exit;
		}

		WP_CLI::confirm("This script will convert {$count} posts of type ”photo” to posts, and sets the post format to “image”. Do you want to proceed?");

		foreach ($posts as $post_id) {
			set_post_format($post_id, 'image');
			set_post_type($post_id, 'post');
			WP_CLI::log("Updated post type and post format for ID {$post_id}");
		}

		WP_CLI::success('Runtime ' . (microtime(true) - $mt) . 's');
	}
}
