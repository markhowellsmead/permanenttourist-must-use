<?php

namespace PT\MustUse\Block;

/**
 * Article publication date
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class PublicationDate
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('mhm/publication-date', [
			'render_callback' => [$this, 'renderBlock'],
			'attributes' => [
				'align' => [
					'type' => 'string'
				]
			]
		]);
	}

	public function renderBlock($attributes)
	{
		return sprintf(
			'<time class="c-article__date%s" datetime="%s">%s</time>',
			!empty($align = $attributes['align'] ?? '') ? ' has-text-align-' . $align : '',
			get_the_date('c'),
			sprintf(
				__('Published on %s', 'sht'),
				get_the_date('')
			)
		);

		return $html;
	}
}
