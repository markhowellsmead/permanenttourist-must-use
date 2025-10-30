<?php

namespace PT\MustUse\Blocks\PageTeaserCover;

use WP_Block;

class Block
{
	public function run()
	{
		add_action('init', [$this, 'register']);
	}

	public function register()
	{
		register_block_type(__DIR__);
	}

	// public static function contentStylesCalcString(array $attributes): string
	// {
	// 	$contentStyles = [];

	// 	$style = $attributes['style'] ?? [];

	// 	if (empty($style['spacing'] ?? '')) {
	// 		return '';
	// 	}

	// 	if ($style['spacing']['padding'] ?? false) {
	// 		if (!empty($style['spacing']['padding']['top'] ?? '')) {
	// 			if (strpos($style['spacing']['padding']['top'], 'var:preset') !== false) {
	// 				$parts = explode('|', $style['spacing']['padding']['top']);
	// 				$size = $parts[2] ?? '';
	// 				$contentStyles['padding-top'] = "var(--wp--preset--spacing--{$size})";
	// 			} else {
	// 				$contentStyles['padding-top'] = $style['spacing']['padding']['top'];
	// 			}
	// 		}

	// 		if (!empty($style['spacing']['padding']['right'] ?? '')) {
	// 			if (strpos($style['spacing']['padding']['right'], 'var:preset') !== false) {
	// 				$parts = explode('|', $style['spacing']['padding']['right']);
	// 				$size = $parts[2] ?? '';
	// 				$contentStyles['padding-right'] = "var(--wp--preset--spacing--{$size})";
	// 			} else {
	// 				$contentStyles['padding-right'] = $style['spacing']['padding']['right'];
	// 			}
	// 		}

	// 		if (!empty($style['spacing']['padding']['bottom'] ?? '')) {
	// 			if (strpos($style['spacing']['padding']['bottom'], 'var:preset') !== false) {
	// 				$parts = explode('|', $style['spacing']['padding']['bottom']);
	// 				$size = $parts[2] ?? '';
	// 				$contentStyles['padding-bottom'] = "var(--wp--preset--spacing--{$size})";
	// 			} else {
	// 				$contentStyles['padding-bottom'] = $style['spacing']['padding']['bottom'];
	// 			}
	// 		}

	// 		if (!empty($style['spacing']['padding']['left'] ?? '')) {
	// 			if (strpos($style['spacing']['padding']['left'], 'var:preset') !== false) {
	// 				$parts = explode('|', $style['spacing']['padding']['left']);
	// 				$size = $parts[2] ?? '';
	// 				$contentStyles['padding-left'] = "var(--wp--preset--spacing--{$size})";
	// 			} else {
	// 				$contentStyles['padding-left'] = $style['spacing']['padding']['left'];
	// 			}
	// 		}
	// 	}

	// 	if (empty($contentStyles)) {
	// 		return '';
	// 	}

	// 	return implode('; ', array_map(function ($value, $property) {
	// 		return "{$property}: {$value}";
	// 	}, $contentStyles, array_keys($contentStyles))) . ';' ?? '';
	// }

	public static function innerStylesCalcString(array $style): string
	{

		$innerStyles = [];

		if (empty($style['spacing']['blockGap'] ?? '')) {
			return '';
		}

		if (strpos($style['spacing']['blockGap'], 'var:preset') !== false) {
			$parts = explode('|', $style['spacing']['blockGap']);
			$size = $parts[2] ?? '';
			$innerStyles['--wp--style--block-gap'] = "var(--wp--preset--spacing--{$size})";
		} else {
			$innerStyles['--wp--style--block-gap'] = $style['spacing']['blockGap'];
		}

		if (empty($innerStyles)) {
			return '';
		}

		return implode('; ', array_map(function ($value, $property) {
			return "{$property}: {$value}";
		}, $innerStyles, array_keys($innerStyles))) . ';' ?? '';
	}

	public static function outerStylesCalcString($attributes)
	{
		$outerStyles = [];

		if (!empty($attributes['aspectRatioDesktop'] ?? '')) {
			$outerStyles['--sht-teaser-cover-aspect-ratio--desktop'] = $attributes['aspectRatioDesktop'];
		}

		if (!empty($attributes['aspectRatioTablet'] ?? '')) {
			$outerStyles['--sht-teaser-cover-aspect-ratio--tablet'] = $attributes['aspectRatioTablet'];
		}

		if (!empty($attributes['aspectRatioMobile'] ?? '')) {
			$outerStyles['--sht-teaser-cover-aspect-ratio'] = $attributes['aspectRatioMobile'];
		}

		if (!empty($attributes['aspectRatioLargeDesktop'] ?? '')) {
			$outerStyles['--sht-teaser-cover-aspect-ratio--large-desktop'] = $attributes['aspectRatioLargeDesktop'];
		}

		if (!empty($attributes['aspectRatioXLargeDesktop'] ?? '')) {
			$outerStyles['--sht-teaser-cover-aspect-ratio--xlarge-desktop'] = $attributes['aspectRatioXLargeDesktop'];
		}

		if (empty($outerStyles)) {
			return '';
		}

		return implode('; ', array_map(function ($value, $property) {
			return "{$property}: {$value}";
		}, $outerStyles, array_keys($outerStyles))) . ';' ?? '';
	}
}
