<?php

namespace PT\MustUse\Block;

/**
 * Albums
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Albums
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('mhm/albums', [
			'render_callback' => [$this, 'renderBlock']
		]);
	}

	public function renderBlock($attributes)
	{

		$albums = get_terms('album');

		if (!count($albums)) {
			return '';
		}

		ob_start();

		get_template_part('partials/block/albums', null, [
			'attributes' => $attributes,
			'albums' => $albums
		]);
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
