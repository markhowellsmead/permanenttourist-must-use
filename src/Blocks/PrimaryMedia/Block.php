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
		$video_ref = get_field('video_ref', get_the_ID());
		$align = $attributes['align'] ?? '';
		$classNameBase = wp_get_block_default_classname($block->name);

		if (!empty($align)) {
			$align = "align{$align}";
		}

		if (!empty($video_ref)) {

			$video = wp_oembed_get($video_ref);

			if (!empty($video)) {

				ob_start();
				// dump(get_object_vars($block));
?>
				<div <?php echo get_block_wrapper_attributes(['class' => "with--videoplayer {$align}"]); ?>>
					<div class="<?php echo $classNameBase; ?>__videoplayer">
						<?php echo $video; ?>
					</div>
				</div>

<?php

			}
		}

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
