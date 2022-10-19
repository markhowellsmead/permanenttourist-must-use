<?php

namespace PT\MustUse\Block;

/**
 * Blog cards
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class ViewpointAncestors
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('mhm/viewpoint-ancestors', [
			'render_callback' => [$this, 'renderBlock']
		]);
	}

	public function renderBlock($attributes)
	{
		if (empty($ancestors = get_post_ancestors(get_the_ID()))) {
			return '';
		}

		ob_start();
		get_template_part('partials/block/viewpoint-ancestors', null, [
			'attributes' => $attributes,
			'posts' => get_posts([
				'post_type' => 'mhm-viewpoint',
				'post_status' => 'publish',
				'post__in' => $ancestors
			])
		]);
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
