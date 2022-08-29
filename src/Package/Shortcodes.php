<?php

namespace PT\MustUse\Package;

class Shortcodes
{

	public function run()
	{
		add_shortcode('pt-flickr', [$this, 'ptFlickr']);
	}

	public function ptFlickr($atts)
	{

		$atts = shortcode_atts(array(
			'id' => '',
			'size' => 'medium',
			'caption' => '',
			'title' => '',
			'tags' => '',
			'number' => null,
		), $atts);

		if ((int) $atts['id']) {
			$posts = get_posts([
				'post_type' => ['post', 'photo'],
				'name'        => (int)$atts['id'],
				'post_status' => 'publish',
				'numberposts' => 1
			]);

			if (count($posts) !== 1) {
				return;
			}


			$thumbnail_id = get_post_thumbnail_id($posts[0]->ID);

			if (!$thumbnail_id) {
				return;
			}

			$image = wp_get_attachment_image($thumbnail_id, $atts['size']);
			$caption = !empty($atts['caption']) ? $atts['caption'] : wp_get_attachment_caption($thumbnail_id);
			if (empty($caption)) {
				$caption = $atts['title'];
			}

			if (!empty($caption)) {
				return sprintf(
					'<figure id="attachment_%1$s" class="wp-caption alignnone">%2$s<figcaption class="wp-caption-text">%3$s</figcaption></figure>',
					$thumbnail_id,
					'<a href="' . get_permalink($posts[0]->ID) . '">' . $image . '</a>',
					$caption
				);
			} else {
				return sprintf(
					'<figure id="attachment_%1$s" class="wp-caption alignnone">%2$s</figure>',
					$thumbnail_id,
					'<a href="' . get_permalink($posts[0]->ID) . '">' . $image . '</a>'
				);
			}
		} elseif (!empty($atts['tags'] ?? '')) {


			$tags = explode(',', $atts['tags']);
			$tax_query = [];

			foreach ($tags as $tag) {
				$tax_query[] = [
					'taxonomy' => 'post_tag',
					'field' => 'slug',
					'terms' => [trim($tag)],
				];
			}

			if (count($tax_query) > 1) {
				$tax_query['relation'] = 'AND';
			}

			if (!($atts['number'] ?? null)) {
				$atts['number'] = -1;
			}

			$post_ids = get_posts([
				'post_type' => ['post', 'photo'],
				'post_status' => 'publish',
				'numberposts' => $atts['number'],
				'orderby' => 'modified',
				'order' => 'DESC',
				'fields' => 'ids',
				'meta_query' => [
					[
						'key' => '_thumbnail_id',
						'compare' => 'IS NOT EMPTY',
					]
				],
				'tax_query' => [
					[
						'taxonomy' => 'post_tag',
						'field' => 'slug',
						'terms' => $tags,
					]
				]
			]);

			if (count($post_ids) < 1) {
				return '';
			}

			ob_start();
			get_template_part('partials/grid500', false, ['post_ids' => $post_ids]);
			$html = ob_get_contents();
			ob_end_clean();

			return $html;
		}
	}
}
