<?php

namespace PT\MustUse\Block;

/**
 * Blog cards
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class BlogCards
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('mhm/blog-cards', [
			'render_callback' => [$this, 'renderBlock']
		]);
	}

	public function renderBlock($attributes)
	{
		$posts = get_posts([
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => 8,
			'ignore_sticky' => false
		]);

		if (count($posts) > 8) {
			// Sticky post!
			$posts = array_slice($posts, 0, 8);
		}

		if (!count($posts)) {
			return '';
		}

		ob_start();
		get_template_part('partials/block/blog-cards', null, [
			'attributes' => $attributes,
			'posts' => $posts
		]);
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
