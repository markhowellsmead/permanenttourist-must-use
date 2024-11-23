<?php

namespace PT\MustUse\PostType;

/**
 * Page post type
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Page
{
	public function run()
	{
		add_filter('init', [$this, 'allowExcerpt']);
		add_filter('init', [$this, 'registerMeta']);
	}

	public function allowExcerpt()
	{
		add_post_type_support('page', 'excerpt');
	}

	public function registerMeta()
	{
		register_post_meta('page', 'content_behind_masthead', [
			'type'         => 'boolean',
			'single'       => true,
			'show_in_rest' => true,
			'default'      => false,
		]);
	}
}
