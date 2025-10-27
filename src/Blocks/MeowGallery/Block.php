<?php

namespace PT\MustUse\Blocks\MeowGallery;

class Block
{

	/**
	 * The data for the gallery items.
	 *
	 * @var array
	 */
	private array $data = [];

	/*
	 * The sort parameters.
	 *
	 * @var array
	 */
	private array $sortParameters = [];

	/**
	 * Add and run hooks and filters
	 *
	 * @return void
	 */
	public function run(): void
	{
		add_filter('mgl_sort', [$this, 'sort'], 25, 4);

		// This works but is not needed yet.
		// 	add_filter('render_block_meow-gallery/gallery', [$this, 'rememberSortParameter'], 10, 2);
	}

	/**
	 * Custom sort function
	 *
	 * @param array $ids
	 * @param array $data
	 * @param array $atts
	 * @return array
	 */
	public function sort($ids, $data, $layout, $atts): array
	{

		if (empty($ids) || empty($data)) {
			return $ids;
		}

		$wplr_collection = (int) ($atts['wplr-collection'] ?? '');

		if (!$wplr_collection) {
			return $ids;
		}

		$this->data = $data;

		uasort($this->data, [$this, "orderByCaptureDate"]);

		return array_keys($this->data);
	}

	/**
	 * Sort the items by capture date
	 *
	 * @param array $data_a
	 * @param array $data_b
	 * @return bool
	 */
	private function orderByCaptureDate($data_a, $data_b): bool
	{
		$created_timestamp_a = $data_a['meta']['image_meta']['created_timestamp'] ?? 0;
		$created_timestamp_b = $data_b['meta']['image_meta']['created_timestamp'] ?? 0;
		return $created_timestamp_b > $created_timestamp_a;
	}

	/**
	 * Remembers the selected sort order for the specified block in the current stream.
	 *
	 * @param string $html
	 * @param array $block
	 * @return string|null
	 */
	public function rememberSortParameter($html, $block): string|null
	{
		$attrs = $block['attrs'] ?? [];
		$blockId = $attrs['blockId'] ?? '';

		if (empty($blockId)) {
			return $html;
		}

		if (isset($attrs['shpCustomSort'])) {
			$this->sortParameters[$blockId] = $attrs['shpCustomSort'];
		}

		return $html;
	}
}
