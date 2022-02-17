<?php

namespace PT\MustUse\PostType;

/**
 * Photo post type
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Photo
{

	public function run()
	{
		add_filter('get_the_archive_title', [$this, 'changeTheTitle'], 30);
		add_action('pre_get_posts', [$this, 'postsPerAlbumPage']);
		add_shortcode('photo_post_id', [$this, 'shortcodePostID'], 10, 0);
	}

	public function changeTheTitle($title)
	{

		if (is_post_type_archive('photo')) {
			return ''; //_x('Photographs', 'Archive title', 'picard');
		}

		if (is_tax('album')) {
			return '<span class="c-archive__titleprefix">' . _x('Photo album', 'Archive list header', 'sht') . '</span> ' . single_term_title('', false);
		}

		return $title;
	}

	public function postsPerAlbumPage($query)
	{
		if (!is_admin() && $query->is_main_query() && is_tax('album')) {
			$query->set('posts_per_page', 64);
			return;
		}
	}

	public function shortcodePostID()
	{
		return get_the_ID();
	}
}
