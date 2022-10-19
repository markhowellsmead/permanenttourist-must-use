<?php

namespace PT\MustUse\Block;

/**
 * Blog cards
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class ViewpointDescendants
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('mhm/viewpoint-descendants', [
			'render_callback' => [$this, 'renderBlock'],
			'attributes' => [
				'viewpoint_type' => [
					'type'  => 'string',
				],
			],
		]);
	}

	public function renderBlock($attributes)
	{

		if (empty($descendants = get_posts([
			'post_parent' => get_the_ID(),
			'post_type' => get_post_type(),
			'posts_per_page' => -1
		]))) {
			return '';
		}

		foreach ($descendants as $descendant_key => $descendant) {
			$descendants[$descendant_key] = $descendant->ID;
		}

		ob_start();

		get_template_part('partials/block/viewpoint-descendants', null, [
			'attributes' => $attributes,
			'posts' => get_posts([
				'post_type' => 'mhm-viewpoint',
				'post_status' => 'publish',
				'post__in' => (array) $descendants
			])
		]);
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
