<?php

namespace PT\MustUse\Plugin;

/**
 * Keeps thin tag archives out of search engines. Tags with only a few
 * posts are near-copies of the posts themselves, so they're set to
 * noindex (links are still followed) and left out of the XML sitemap.
 * The pages themselves stay available to visitors.
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class YoastSEO
{

	/**
	 * Tags with fewer published posts than this are treated as thin.
	 */
	const MIN_TAG_POSTS = 5;

	public function run()
	{
		add_filter('wpseo_robots_array', [$this, 'noindexThinTags']);
		add_filter('terms_clauses', [$this, 'excludeThinTagsFromSitemap'], 10, 2);
	}

	/**
	 * @param array $robots
	 * @return array
	 */
	public function noindexThinTags($robots): array
	{
		if (!is_tag()) {
			return $robots;
		}

		$term = get_queried_object();

		if ($term instanceof \WP_Term && (int) $term->count < self::MIN_TAG_POSTS) {
			$robots['index'] = 'noindex';
		}

		return $robots;
	}

	/**
	 * Filters the tag query in Yoast's sitemap requests, rather than using
	 * wpseo_exclude_from_sitemap_by_term_ids: Yoast counts all terms when
	 * paginating the sitemap index, so excluding by ID would leave a series
	 * of mostly empty tag sitemaps.
	 *
	 * @param array $clauses
	 * @param array $taxonomies
	 * @return array
	 */
	public function excludeThinTagsFromSitemap($clauses, $taxonomies): array
	{
		if (!get_query_var('sitemap') || (array) $taxonomies !== ['post_tag']) {
			return $clauses;
		}

		$clauses['where'] .= sprintf(' AND tt.count >= %d', self::MIN_TAG_POSTS);

		return $clauses;
	}
}
