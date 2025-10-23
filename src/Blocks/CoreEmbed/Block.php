<?php

namespace PT\MustUse\Blocks\CoreEmbed;

use DOMDocument;

class Block
{
	public function run()
	{
		add_filter('render_block_core/embed', [$this, 'missingImage'], 10, 2);
		add_filter('render_block_core/embed', [$this, 'addDataAttributes'], 10, 2);
	}

	public function missingImage($html, $block)
	{

		if ($block['attrs']['providerNameSlug'] !== 'permanent-tourist') {
			return $html;
		}

		$html_inner_stripped = trim(strip_tags($html));

		if ($html_inner_stripped !== $block['attrs']['url']) {
			return $html;
		}

		ob_start();
?>
		<div class="wp-block-group has-background is-layout-constrained wp-block-group-is-layout-constrained" style="background-color:#ffcc00">
			<p class="has-text-align-center">A photo or blog post was embedded here, which is currently not available.</p>
			<p style="margin-block-start: var(--wp--preset--spacing--small)" class=" has-text-align-center has-small-font-size"><?php echo $block['attrs']['url']; ?></p>
		</div>
<?php
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Adds data attributes to the block root element
	 *
	 * @param string $html
	 * @param array $block
	 * @return string
	 */
	public function addDataAttributes($html, $block)
	{
		if (empty($html)) {
			return $html;
		}

		$url = $block['attrs']['url'] ?? '';

		if (empty($url)) {
			return $html;
		}

		libxml_use_internal_errors(true);
		$document = new DOMDocument();
		$document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

		$document->documentElement->setAttribute("data-attribute-url", $url);
		libxml_clear_errors();

		return str_replace('<?xml encoding="UTF-8">', '', $document->saveHTML());
	}
}
