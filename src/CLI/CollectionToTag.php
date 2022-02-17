<?php

namespace SayHello\Theme\CLI;

use WP_CLI;
use WP_CLI_Command;

class CollectionToTag extends WP_CLI_Command
{

	public function __invoke()
	{

		$mt = microtime(true);

		$posts = get_posts([
			'posts_per_page' => -1,
			'post_type' => 'post',
			'fields' => 'ids',
			'tax_query' => [
				[
					'taxonomy' => 'collection',
					'operator' => 'EXISTS'
				],
			]
		]);

		$count = count($posts);

		WP_CLI::log("{$count} posts to update");

		foreach ($posts as $post_id) {
			$collections = get_the_terms($post_id, 'collection');
			if (empty($collections)) {
				continue;
			}
			$new_tags = [];
			foreach ($collections as $collection) {
				$new_tags[] = $collection->name;
			}
			wp_set_object_terms($post_id, $new_tags, 'post_tag', true);
			WP_CLI::log("Updated post ID {$post_id}");
		}



		WP_CLI::success('Runtime ' . (microtime(true) - $mt) . 's');
	}
}
