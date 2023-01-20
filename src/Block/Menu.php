<?php

namespace PT\MustUse\Block;

/**
 * Menu Block
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Menu
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('sht/menu', [
			'render_callback' => [$this, 'renderBlock'],
			'attributes' => [
				'align' => [
					'type'  => 'string',
				],
				'menu' => [
					'type'  => 'string',
				],
				'fontSize' => [
					'type'  => 'string',
				]
			],
		]);
	}

	public function renderBlock($attributes)
	{
		ob_start();
		get_template_part('partials/block/menu', null, [
			'attributes' => $attributes
		]);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
