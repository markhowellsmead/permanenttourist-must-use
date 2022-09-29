<?php

namespace PT\MustUse\Block;

/**
 * PrimaryMedia Block
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class PrimaryMedia
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('sht/primary-media', [
			'render_callback' => [$this, 'renderBlock'],
			'attributes' => [
				'align' => [
					'type'  => 'string',
				],
			],
		]);
	}

	public function renderBlock($attributes, $content, $block)
	{

		if (is_singular('post') && (bool) get_field('hide_thumbnail')) {
			return '';
		}

		ob_start();

		$post_id = get_the_ID();
		$classNameBase = wp_get_block_default_classname($block->name);
		$align = $attributes['align'] ?? '';

		$media_size = 'medium';
		switch ($align) {
			case 'wide':
				$media_size = 'gutenberg_wide';
				break;
			case 'full':
				$media_size = 'full';
				break;
		}

		$content = '';

		if (!empty($video_url = get_field('video_ref', $post_id))) {

			if (is_singular('post') || is_singular('page')) {
				$video_player = wp_oembed_get($video_url);

				$content = sprintf(
					'<figure class="%1$s__figure %1$s__figure--video">%2$s</figure>',
					$classNameBase,
					$video_player,
				);
			} else {

				$thumbnail = pt_must_use_get_instance()->Package->Media->getVideoThumbnail($video_url);

				if (!empty($thumbnail)) {
					$content = sprintf(
						'<figure class="%1$s__figure %1$s__figure--%2$s"><a href="%5$s"><img class="%1$s__image" src="%3$s" alt="%4$s" /></a></figure>',
						$classNameBase,
						$media_size,
						pt_must_use_get_instance()->Package->Media->getVideoThumbnail($video_url),
						get_the_title($post_id),
						get_the_permalink($post_id)
					);
				}
			}
		} elseif (has_post_thumbnail($post_id)) {
			$image = wp_get_attachment_image(get_post_thumbnail_id($post_id), $media_size, false, ['class' => "{$classNameBase}__image"]);

			if (!empty($image)) {
				if (is_singular('post') || is_singular('page')) {
					$content = sprintf('<figure class="%1$s__figure %1$s__figure--%2$s">%3$s</figure>', $classNameBase, $media_size, $image);
				} else {
					$content = sprintf('<figure class="%1$s__figure %1$s__figure--%2$s"><a href="%4$s">%3$s</a></figure>', $classNameBase, $media_size, $image, get_the_permalink($post_id));
				}
			}
		}

		if (empty($content)) {
			return;
		}

		if (!empty($align)) {
			$align = " align{$align}";
		}

?>

		<div class="<?php echo $classNameBase . $align; ?>">
			<?php echo $content; ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
