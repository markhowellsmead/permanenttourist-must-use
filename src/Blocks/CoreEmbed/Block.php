<?php

namespace PT\MustUse\Blocks\CoreEmbed;

use DOMDocument;
use DOMXPath;

class Block
{
	public function run()
	{
		add_filter('render_block_core/embed', [$this, 'render'], 10, 2);
	}

	public function render($html, $block)
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
		</div>
<?php
		$html = ob_get_clean();

		return $html;
	}
}
