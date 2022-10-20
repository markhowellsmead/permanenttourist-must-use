<?php

namespace PT\MustUse\Package;

/**
 * Post archives
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Archives
{

	public function run()
	{
		add_filter('get_the_archive_title', [$this, 'archiveTitle'], 10, 3);
	}

	public function archiveTitle($title, $original_title, $prefix)
	{
		if (is_category()) {
			$prefix = sprintf(
				'<span class="c-archive__titleprefix">%s</span>',
				_x('Posts from the category', 'Archive title category', 'sht')
			);
			$title = ' ' . $original_title;
		} elseif (is_tag()) {

			$term_id = get_queried_object_id();
			$tag_is_series = (bool) get_field('tag_is_series', "term_{$term_id}");

			if ($tag_is_series) {
				$prefix = sprintf(
					'<span class="c-archive__titleprefix">%s</span>',
					_x('Post series', 'Archive title post tag', 'sht')
				);
			} else {
				$prefix = sprintf(
					'<span class="c-archive__titleprefix">%s</span>',
					_x('Posts about', 'Archive title post tag', 'sht')
				);
			}

			$title = ' ' . $original_title;
		} elseif (is_author()) {
			$prefix = sprintf(
				'<span class="c-archive__titleprefix">%s</span>',
				_x('Posts by', 'Archive title author', 'sht')
			);
			$title = ' ' . $original_title;
		} elseif (is_year()) {
			$prefix = sprintf(
				'<span class="c-archive__titleprefix">%s</span>',
				_x('Posts from', 'Archive title year', 'sht')
			);
			$title = ' ' . $original_title;
		} elseif (is_month()) {
			$prefix = sprintf(
				'<span class="c-archive__titleprefix">%s</span>',
				_x('Posts from', 'Archive title month/year', 'sht')
			);
			$title = ' ' . $original_title;
		} elseif (is_day()) {
			$prefix = sprintf(
				'<span class="c-archive__titleprefix">%s</span>',
				_x('Posts from', 'Archive title day', 'sht')
			);
			$title = ' ' . $original_title;
		} elseif (is_post_type_archive('photo')) {
			$prefix = '';
			$title = '';
		} elseif (is_post_type_archive()) {
			$prefix = sprintf(
				'<span class="c-archive__titleprefix">%s</span>',
				_x('Beitragsarchiv', 'CPT archive title', 'sht')
			);
			$title = ' ' . $original_title;
		} elseif (is_tax('collection')) {
			$prefix = sprintf(
				'<span class="c-archive__titleprefix">%s</span>',
				_x('Photos from the collection', 'Archive title year', 'sht')
			);
			$title = ' ' . $original_title;
		} elseif (is_tax('place')) {
			$prefix = sprintf(
				'<span class="c-archive__titleprefix">%s</span>',
				_x('Entries about', 'Archive place', 'sht')
			);
			$title = ' ' . $original_title;
		} elseif (is_search()) {
			if ($GLOBALS['wp_query']->found_posts > 0) {
				return '<span class="c-archive__titleprefix">' . sprintf(
					_nx('%s search result for', '%s search results for', $GLOBALS['wp_query']->found_posts, 'Archive list header', 'sht'),
					$GLOBALS['wp_query']->found_posts
				) . '</span> ' . get_search_query();
			} else {
				return '<span class="c-archive__titleprefix">' . sprintf(
					_x('No search results for', 'Archive list header', 'sht'),
					$GLOBALS['wp_query']->found_posts
				) . '</span> ' . get_search_query();
			}
		} else {
			$prefix = '';
			$title = '';
		}
		return $prefix . $title;
	}
}
