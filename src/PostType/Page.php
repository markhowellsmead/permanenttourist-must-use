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
	}

	public function allowExcerpt()
	{
		add_post_type_support('page', 'excerpt');
	}
}
