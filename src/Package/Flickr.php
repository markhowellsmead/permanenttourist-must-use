<?php

namespace PT\MustUse\Package;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Flickr
{

	public function run()
	{
		add_shortcode('pt_flickr', [$this, 'shortcode']);
		add_action('save_post', [$this, 'clearTransients'], 10, 2);
	}

	private function normaliseAtts($atts)
	{
		return shortcode_atts(array(
			'id' => '',
			'size' => 'medium',
			'align' => '',
			'caption' => '',
			'title' => '',
			'tags' => '',
			'number' => null,
		), $atts);
	}

	private function transientKey(array $atts)
	{
		return 'pt_flickr_' . md5(json_encode($atts));
	}

	/**
	 * Delete the cached HTML of every pt_flickr shortcode in the saved post.
	 */
	public function clearTransients($post_id, $post)
	{
		if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id) || empty($post->post_content)) {
			return;
		}

		if (!has_shortcode($post->post_content, 'pt_flickr')) {
			return;
		}

		preg_match_all('/' . get_shortcode_regex(['pt_flickr']) . '/', $post->post_content, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			$atts = $this->normaliseAtts(shortcode_parse_atts($match[3]));

			if ((int) $atts['id']) {
				delete_transient($this->transientKey($atts));
			}
		}
	}

	public function shortcode($atts)
	{

		$atts = $this->normaliseAtts($atts);

		if ((int) $atts['id']) {

			$filename = $atts['id'];
			$transient_key = $this->transientKey($atts);

			$html = get_transient($transient_key);

			if (!empty($html) && !is_user_logged_in()) {
				return $html;
			}

			$upload_dir = wp_upload_dir();
			$directory = $upload_dir['basedir'];

			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
			$filepaths = [];

			foreach ($iterator as $fileinfo) {

				if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $fileinfo->getFilename())) {
					continue;
				}

				$file_name = $fileinfo->getFilename();

				if ($fileinfo->isFile() && strpos($file_name, $filename) === 0 && !preg_match('/[0-9]+x[0-9]+/i', $file_name)) {
					$image_size = getimagesize($fileinfo->getPathname());
					$filepaths["w{$image_size[0]}"] = $fileinfo->getPathname();
				}
			}

			if (empty($filepaths)) {
				return PHP_EOL . "<!-- pt_flickr: No matching file found for the ID {$atts['id']} -->" . PHP_EOL;
			}

			ksort($filepaths);
			$file_url = str_replace($directory, $upload_dir['baseurl'], array_values($filepaths)[0]);

			$align = !empty($atts['align'] ?? '') ? " align{$atts['align']}" : '';

			$html = sprintf(
				'<figure class="pt_flickr__figure%1$s"><img class="pt_flickr__image" src="%2$s" alt="%3$s" />%4$s</figure>',
				$align,
				$file_url,
				str_replace('"', '&quot;', $atts['caption'] ?? ''),
				!empty($atts['caption'] ?? '') ? '<figcaption class="pt_flickr__caption">' . str_replace('"', '&quot;', $atts['caption']) . '</figcaption>' : ''
			);

			// Only reached when a matching file was found. Same key as the lookup above.
			set_transient($transient_key, $html, YEAR_IN_SECONDS);

			return $html;

			// $posts = get_posts([
			// 	'post_type' => ['post', 'photo'],
			// 	'name'        => (int)$atts['id'],
			// 	'post_status' => 'publish',
			// 	'numberposts' => 1
			// ]);

			// if (count($posts) !== 1) {
			// 	return;
			// }


			// $thumbnail_id = get_post_thumbnail_id($posts[0]->ID);

			// if (!$thumbnail_id) {
			// 	return;
			// }

			// $image = wp_get_attachment_image($thumbnail_id, $atts['size']);
			// $caption = !empty($atts['caption']) ? $atts['caption'] : wp_get_attachment_caption($thumbnail_id);
			// if (empty($caption)) {
			// 	$caption = $atts['title'];
			// }

			// if (!empty($caption)) {
			// 	return sprintf(
			// 		'<figure id="attachment_%1$s" class="wp-caption alignnone">%2$s<figcaption class="wp-caption-text">%3$s</figcaption></figure>',
			// 		$thumbnail_id,
			// 		'<a href="' . get_permalink($posts[0]->ID) . '">' . $image . '</a>',
			// 		$caption
			// 	);
			// } else {
			// 	return sprintf(
			// 		'<figure id="attachment_%1$s" class="wp-caption alignnone">%2$s</figure>',
			// 		$thumbnail_id,
			// 		'<a href="' . get_permalink($posts[0]->ID) . '">' . $image . '</a>'
			// 	);
			// }
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
