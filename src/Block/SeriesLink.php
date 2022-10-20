<?php

namespace PT\MustUse\Block;

/**
 * SeriesLink Block
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class SeriesLink
{

	public function run()
	{
		add_action('init', [$this, 'registerBlocks']);
	}

	public function registerBlocks()
	{
		register_block_type('sht/series-link', [
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

		if (!is_singular('post')) {
			return '';
		}

		$tags = get_the_tags();
		$series = [];

		foreach ($tags as $tag) {
			if ((bool) get_field('tag_is_series', "term_{$tag->term_id}")) {
				$series[] = $tag;
			}
		}

		if (empty($series)) {
			return '';
		}

		ob_start();

		$classNameBase = wp_get_block_default_classname($block->name);

		$content = '';

		if (count($series) > 1) {
			$links = [];
			foreach ($series as $series_entry) {
				$links[] = sprintf(
					'<a class="%s__entrylink" href="%s">%s</a>',
					$classNameBase,
					get_term_link($series_entry->term_id),
					esc_html($series_entry->name),
				);
			}

			$content = sprintf(
				'<div class="%s__entries">%s</div>',
				$classNameBase,
				implode(', ', $links)
			);
		} else {
			$content = sprintf(
				'<div class="%1$s__entries">%2$s <a class="%1$s__entrylink" href="%3$s">%4$s</a></div>',
				$classNameBase,
				_x('From the series', '', 'permanenttourst-must-use'),
				get_term_link($series[0]->term_id),
				esc_html($series[0]->name),
			);
		}

		if (empty($content)) {
			return '';
		}

		$align = $attributes['align'] ?? '';

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
