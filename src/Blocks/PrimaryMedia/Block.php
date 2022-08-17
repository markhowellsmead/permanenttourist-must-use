<?php

namespace PT\MustUse\Blocks\PrimaryMedia;

use WP_Block;

class Block
{
	public function run()
	{
		add_action('init', [$this, 'register']);
	}

	public function register()
	{
		register_block_type(dirname(__FILE__) . '/block.json', [
			'render_callback' => [$this, 'renderBlock']
		]);
	}

	public function renderBlock(array $attributes, string $content, WP_Block $block)
	{
		$html = '';
		$video_ref = get_field('video_ref', get_the_ID());
		$align = $attributes['align'] ?? '';
		$classNameBase = wp_get_block_default_classname($block->name);

		if (!empty($align)) {
			$align = "align{$align}";
		}

		$video = wp_oembed_get($video_ref);

		if (!empty($video)) {

			ob_start();
?>
			<div <?php echo get_block_wrapper_attributes(['class' => "with--videoplayer {$align}"]); ?>>
				<div class="<?php echo $classNameBase; ?>__videoplayer">
					<?php echo $video; ?>
				</div>
			</div>

		<?php
			$html = ob_get_contents();
			ob_end_clean();
		} elseif (has_post_thumbnail(get_the_ID())) {
			ob_start();
		?>
			<div <?php echo get_block_wrapper_attributes(['class' => "with--post-thumbnail {$align}"]); ?>>
				<figure class="<?php echo $classNameBase; ?>__figure">
					<?php echo wp_get_attachment_image(get_post_thumbnail_id(get_the_ID()), 'full', false, ['class' => "{$classNameBase}__image"]); ?>
				</figure>
			</div>
		<?php
			$html = ob_get_contents();
			ob_end_clean();
		} elseif (pt_must_use_get_instance()->Package->Gutenberg->isContextEdit()) {
			ob_start();
		?>
			<div <?php echo get_block_wrapper_attributes(); ?>>
				<p>BLOCK PLACEHOLDER</p>
			</div>

<?php
			$html = ob_get_contents();
			ob_end_clean();
		}



		return $html;
	}
}
