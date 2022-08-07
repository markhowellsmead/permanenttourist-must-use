<?php

namespace PT\MustUse\CLI;

use WP_CLI;
use WP_CLI_Command;
use WP_Query;

class PostToPhoto extends WP_CLI_Command
{

	public function __invoke()
	{
		$mt = microtime(true);

		$query = new WP_Query(array(
			'posts_per_page' => -1,
			'post_type' => 'post',
			'fields' => 'ids',
			'tax_query' => array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => array(
						// 'post-format-aside',
						// 'post-format-audio',
						// 'post-format-chat',
						// 'post-format-gallery',
						'post-format-image',
						// 'post-format-link',
						// 'post-format-quote',
						// 'post-format-status',
						// 'post-format-video'
					),
					//'operator' => 'NOT IN'
				)
			)
		));

		// $posts = get_posts([
		// 	'posts_per_page' => -1,
		// 	'post_type' => 'post',
		// 	'fields' => 'ids',
		// 	'update_post_meta_cache' => false,
		// 	'update_post_term_cache' => false,
		// 	'taxonomy' => 'post_format',
		// 	'terms' => array(
		// 		'post-format-image',
		// 	),
		// ]);
		wp_reset_postdata();

		$count = count($query->posts);

		if (!$count) {
			WP_CLI::success('No posts of format “image” found to convert.');
			exit;
		}

		WP_CLI::confirm("This script will convert {$count} posts of format “image” to CPT “photo”. Do you want to proceed?");

		foreach ($query->posts as $post_id) {
			//set_post_format($post_id, 'image');
			set_post_type($post_id, 'photo');
			WP_CLI::log("Updated post type for ID {$post_id}");
		}

		WP_CLI::success('Runtime ' . (microtime(true) - $mt) . 's');
	}
}
