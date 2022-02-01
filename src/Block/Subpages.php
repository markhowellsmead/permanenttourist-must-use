<?php

namespace PT\MustUse\Block;

/**
 * Subpages
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Subpages
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('mhm/subpages', [
			'render_callback' => [$this, 'renderBlock']
		]);
	}

	public function renderBlock($attributes)
	{

		$pages = get_posts(array(
			'post_type' => 'page',
			'numberposts' => -1,
			'orderby'     => 'modified',
			'order'       => 'DESC',
			'post_parent' => get_the_ID()
		));

		if (!count($pages)) {
			return '';
		}

		ob_start();

		get_template_part('partials/block/subpages', null, [
			'attributes' => $attributes,
			'pages' => $pages
		]);
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
