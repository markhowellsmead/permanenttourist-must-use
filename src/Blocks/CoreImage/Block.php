<?php

namespace PT\MustUse\Blocks\CoreImage;

use DOMDocument;
use DOMElement;
use DOMXPath;

class Block
{
	public function run()
	{
		add_filter('render_block_core/image', [$this, 'addWebcamForce'], 10, 2);
	}

	/**
	 * Adds a random URL query parameter to webcam images
	 *
	 * @param string $html
	 * @param array $block
	 * @return string
	 */
	public function addWebcamForce($html, $block)
	{

		if (empty($html)) {
			return $html;
		}

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
			if (!$node instanceof DOMElement) {
				continue;
			}

			$new_src = $node->getAttribute('src') . (parse_url($node->getAttribute('src'), PHP_URL_QUERY) ? '&' : '?') . 'force=' . rand(1, 1000000);
			$node->setAttribute('src', $new_src);
		}

		$body = $dom->saveHtml($dom->getElementsByTagName('body')->item(0));
		return str_replace(['<body>', '</body>'], '', $body);
	}
}
