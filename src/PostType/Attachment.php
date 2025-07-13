<?php

namespace PT\MustUse\PostType;

use WP_Query;
use WP_REST_Attachments_Controller;
use WP_REST_Request;

class Attachment
{
	public function run()
	{
		add_action('init', [$this, 'registerRestFields']);
		add_filter('rest_attachment_query', [$this, 'filterByPhotoPosts'], 10, 2);
	}

	public function registerRestFields()
	{
		register_rest_field('attachment', 'pt', [
			'get_callback' => function (array $attachment) {

				$fields = [];

				$photo_posts = get_posts([
					'post_type' => 'photo',
					'posts_per_page' => -1,
					'meta_query' => [
						[
							'key'     => '_thumbnail_id',
							'compare' => '=',
							'value' => $attachment['id']
						]
					]
				]);

				$fields['photo_posts'] = [];

				if (count($photo_posts)) {
					foreach ($photo_posts as $photo_post) {
						$photo_post->link = get_permalink($photo_post->ID);
						$fields['photo_posts'][] = $photo_post;
					}
				}

				return $fields;
			}
		]);
	}

	public function filterByPhotoPosts(array $args, WP_REST_Request $request)
	{
		if (!$request->get_param('pt_nophoto_posts')) {
			return $args;
		}

		global $wpdb;

		$used_ids = $wpdb->get_col("
        SELECT meta_value
        FROM {$wpdb->postmeta} m
        INNER JOIN {$wpdb->posts} p ON m.post_id = p.ID
        WHERE m.meta_key = '_thumbnail_id'
        AND p.post_type = 'photo'
        AND p.post_status != 'trash'");

		if (empty($used_ids)) {
			return $args;
		}

		$post__not_in = $args['post__not_in'] ?? [];

		$args['post__not_in'] = array_merge($post__not_in, $used_ids);

		// Remove the 'pt_nophoto_posts' parameter to avoid infinite recursion
		unset($args['pt_nophoto_posts']);

		return $args;
	}
}
