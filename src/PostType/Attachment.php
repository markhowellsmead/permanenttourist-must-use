<?php

namespace PT\MustUse\PostType;

use WP_REST_Attachments_Controller;
use WP_REST_Request;

class Attachment
{
	public function run()
	{
		add_action('init', [$this, 'registerRestFields']);
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
						$fields['photo_posts'][] = $photo_post;
					}
				}

				return $fields;
			}
		]);
	}
}
