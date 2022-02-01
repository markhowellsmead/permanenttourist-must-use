<?php

namespace PT\MustUse\Block;

/**
 * Post Header block
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class PostHeader
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('sht/post-header', [
			'render_callback' => [$this, 'renderBlock'],
			'attributes'      => [
				'alignment' => [
					'type'    => 'string',
					'default' => 'center'
				],
				'post_title' => [
					'type'    => 'string',
					'default' => ''
				],
				'post_excerpt' => [
					'type'    => 'string',
					'default' => ''
				],
			]
		]);
	}

	public function renderBlock($attributes)
	{
		ob_start();
		sht_theme()->getTemplatePart('partials/block/post-header', [
			'attributes' => $attributes
		]);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
