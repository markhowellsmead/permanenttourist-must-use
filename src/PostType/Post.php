<?php

namespace PT\MustUse\PostType;

class Post
{

	public function run()
	{
		add_action('init', [$this, 'registerPostMeta']);
	}

	public function registerPostMeta(): void
	{
		register_post_meta('post', 'outdooractive_url', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'description' => 'URL to the hike on outdooractive.com',
			'auth_callback' => function () {
				return current_user_can('edit_posts');
			}
		]);
	}
}
