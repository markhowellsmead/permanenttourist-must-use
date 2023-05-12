<?php

namespace PT\MustUse\Blocks\CoreImage;

use DOMDocument;
use DOMXPath;

class Block
{
	public function run()
	{
		add_filter('render_block_core/image', [$this, 'render'], 10, 2);
	}

	public function render($html, $block)
	{
		$className = $block['attrs']['className'] ?? '';
		if (strpos($className, 'is-style-webcam') === false) {
			return $html;
		}

		libxml_use_internal_errors(true);
		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$xpath = new DOMXPath($dom);
		$nodeList = $xpath->query('//img');

		foreach ($nodeList as $node) {
			$new_src = $node->getAttribute('src') . (parse_url($node->getAttribute('src'), PHP_URL_QUERY) ? '&' : '?') . 'force=' . rand(1, 1000000);
			$node->setAttribute('src', $new_src);
		}

		$body = $dom->saveHtml($dom->getElementsByTagName('body')->item(0));
		return str_replace(['<body>', '</body>'], '', $body);
	}
}
