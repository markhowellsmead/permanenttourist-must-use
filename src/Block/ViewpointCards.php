<?php

namespace PT\MustUse\Block;

/**
 * Blog cards
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class ViewpointCards
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('mhm/viewpoint-cards', [
			'render_callback' => [$this, 'renderBlock']
		]);
	}

	public function renderBlock($attributes)
	{
		$posts = get_posts([
			'post_type' => 'mhm-viewpoint',
			'post_status' => 'publish',
			'posts_per_page' => 4,
			'ignore_sticky' => false
		]);

		if (count($posts) > 4) {
			// Sticky post!
			$posts = array_slice($posts, 0, 4);
		}

		if (!count($posts)) {
			return '';
		}

		ob_start();
		sht_theme()->getTemplatePart('partials/block/viewpoint-cards', [
			'attributes' => $attributes,
			'posts' => $posts
		]);
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
