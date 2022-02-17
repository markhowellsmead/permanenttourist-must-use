<?php

namespace PT\MustUse\Package;

use DOMDocument;
use DOMXpath;

/**
 * Footnotes stuff
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Footnotes
{

	public function run()
	{
		add_filter('the_content', [$this, 'addFootnoteAnchors']);
		add_filter('the_content', [$this, 'addFootnoteLinks']);
		add_filter('the_excerpt', [$this, 'stripFootnoteLinks']);
	}

	public function addFootnoteAnchors($content)
	{
		if (has_block('mhm/footnotes') && !empty($content)) {
			libxml_use_internal_errors(true);
			$domDocument = new DOMDocument();
			$domDocument->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

			$xpath = new DOMXpath($domDocument);
			$blocks = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' wp-block-mhm-footnotes ')]");

			$iterator = 0;

			foreach ($blocks as $block) {
				$paragraphs = $xpath->query('p', $block);
				if ($paragraphs) {
					foreach ($paragraphs as $paragraph) {
						$iterator++;
						$paragraph->setAttribute('id', 'footnote' . $iterator);
						$paragraph->setAttribute('class', 'wp-block-mhm-footnote');
					}
				}
			}
			$body = $domDocument->saveHtml($domDocument->getElementsByTagName('body')->item(0));
			$content = str_replace(array('<body>', '</body>'), '', $body);
		}
		return $content;
	}

	/**
	 * Allows the editor to use [[1]] which will automatically link to #fussnote1.
	 * @param string $content The post content
	 */
	public function addFootnoteLinks(string $content)
	{
		if (!is_admin() && !pt_must_use_get_instance()->Package->Gutenberg->isContextEdit()) {
			$content = preg_replace('~\[\[([0-9]+)\]\]~', '<sup><a data-fnq id="footnotesource$1" href="#' . _x('footnote', 'Anchor slug for footnotes', 'sht') . '$1">$1</a></sup>', $content);
		}
		return $content;
	}

	public function stripFootnoteLinks($excerpt)
	{
		return preg_replace('~\[\[[0-9]+\]\]~', '', $excerpt);
	}
}
