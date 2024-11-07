<?php

namespace PT\MustUse\Plugin;

class Algolia
{

	public function run()
	{
		add_filter('algolia_search_params', [$this, 'modifyParams']);
		add_filter('algolia_should_index_user', '__return_false');
		add_filter('algolia_search_highlighting_enabled', '__return_false');
		add_filter('algolia_excluded_taxonomies', [$this, 'excludeTaxonomies']);
		add_filter('algolia_excluded_post_types', [$this, 'excludePostTypes']);
	}

	/**
	 * Modify Algolia search parameters
	 *
	 * @param array $params
	 * @return array
	 */
	public function modifyParams($params)
	{
		$params['highlightPreTag'] = '';
		$params['highlightPostTag'] = '';

		return $params;
	}

	/**
	 * Exclude taxonomies from indexing
	 * https://silvanhagen.com/
	 *
	 * @param array $exclude
	 * @return array
	 */
	public function excludeTaxonomies(array $exclude)
	{
		$exclude[] = 'author';
		$exclude[] = 'field';
		$exclude[] = 'wp_block_to_post';
		$exclude[] = 'wp_pattern_category';
		$exclude[] = 'wp_block_category';

		return $exclude;
	}

	/**
	 * Exclude post types from indexing
	 * https://silvanhagen.com/
	 *
	 * @param array $exclude
	 * @return array
	 */
	public function excludePostTypes(array $exclude)
	{
		$exclude[] = 'wp_font_family';
		$exclude[] = 'wp_font_face';
		$exclude[] = 'wp_block';
		$exclude[] = 'wp_template';
		$exclude[] = 'wp_block_pattern';
		$exclude[] = 'acf-taxonomy';
		$exclude[] = 'acf-post-type';
		$exclude[] = 'acf-ui-options-page';
		$exclude[] = 'acf-field-group';
		$exclude[] = 'acf-field';

		return $exclude;
	}
}
