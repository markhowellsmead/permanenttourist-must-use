<?php

namespace PT\MustUse\PostType;

/**
 * Page post type
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Page
{
	public function run()
	{
		add_filter('init', [$this, 'allowExcerpt']);
		add_filter('init', [$this, 'registerMeta']);
		add_action('wp_footer', [$this, 'mastheadColor']);
	}

	public function allowExcerpt()
	{
		add_post_type_support('page', 'excerpt');
	}

	public function registerMeta()
	{
		register_post_meta('page', 'content_behind_masthead', [
			'type'         => 'boolean',
			'single'       => true,
			'show_in_rest' => true,
			'default'      => false,
			'auth_callback' => function () {
				return current_user_can('edit_posts');
			}
		]);

		register_meta('post', 'masthead_color', [
			'show_in_rest' => true,
			'type' => 'string',
			'single' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can('edit_posts');
			}
		]);
	}

	private function calculateBrightness(string $color): float
	{
		// Remove the hash if it exists
		$color = ltrim($color, '#');

		// Convert HEX to RGB
		[$r, $g, $b] = array_map(
			fn($hex) => hexdec(str_pad($hex, 2, $hex)),
			str_split($color, strlen($color) === 3 ? 1 : 2)
		);

		// Calculate relative brightness using the formula
		return ($r * 0.299 + $g * 0.587 + $b * 0.114) / 255;
	}


	public function mastheadColor()
	{
		if (get_post_type() !== 'page') {
			return;
		}

		$content_behind_masthead = get_post_meta(get_the_ID(), 'content_behind_masthead', true);
		$content_behind_masthead_rule = $content_behind_masthead ? 'document.body.classList.add("body--content_behind_masthead");' : '';
		$color = get_post_meta(get_the_ID(), 'masthead_color', true);

		if (empty($color)) {
			return;
		}

		if ($this->calculateBrightness($color)) {
			$hex_brightness_factor = 'light';
		} else {
			$hex_brightness_factor = 'dark';
		}

?>
		<script>
			<?php echo $content_behind_masthead_rule; ?>
			document.querySelector('.c-masthead').classList.add('c-masthead--custom-color');
			document.querySelector('.c-masthead').classList.add('c-masthead--custom-color--<?php echo $hex_brightness_factor; ?>');
			window.addEventListener('scroll', function() {
				const scrollOffset = window.scrollY;

				if (scrollOffset > 50) {
					document.querySelector('.c-masthead').classList.remove('c-masthead--custom-color');
				} else {
					document.querySelector('.c-masthead').classList.add('c-masthead--custom-color');
				}
			});
		</script>
		<style>
			.c-masthead,
			.c-masthead--custom-color {
				transition: background-color 0.3s ease-in-out;
			}

			.c-masthead--custom-color {
				color: <?php echo esc_attr($color); ?> !important;
				background: none transparent !important;
				-webkit-backdrop-filter: none !important;
				backdrop-filter: none !important;
			}

			.c-masthead--custom-color.c-masthead--custom-color--light {
				text-shadow: 0 0 4px rgba(0, 0, 0, 0.5);
			}

			.c-masthead--custom-color * {
				color: inherit !important;
			}

			body.body--content_behind_masthead .c-main {
				margin-top: 0 !important;
				--main--offset: 0;
			}
		</style>
<?php
	}
}
