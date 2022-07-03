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
		// add_action('init', [$this, 'registerPostType']);
		// add_action('init', [$this, 'addCapabilities']);
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

	/**
	 * Registers the custom post type
	 * @return void
	 */
	public function registerPostType()
	{
		register_post_type(
			'photo',
			[
				'can_export' => false,
				'capabilities'	=> [
					'read' => 'read_photo',
					'edit_post' => 'edit_photo',
					'read_post' => 'read_photo',
					'delete_post' => 'delete_photo',
					'edit_posts' => 'edit_photos',
					'edit_others_posts' => 'edit_others_photos',
					'publish_posts' => 'publish_photos',
					'read_private_posts' => 'read_private_photos',
					'delete_posts' => 'delete_photos',
					'delete_private_posts' => 'delete_private_photos',
					'delete_published_posts' => 'delete_published_photos',
					'delete_others_posts' => 'delete_others_photos',
					'edit_private_posts' => 'edit_private_photos',
					'edit_published_posts' => 'edit_published_photos',
				],
				'has_archive' => true,
				'map_meta_cap' => false,
				'menu_icon' => 'dashicons-welcome-widgets-menus',
				'public' => true,
				'show_in_admin_bar' => true,
				'show_in_nav_menus' => false,
				'show_in_rest' => true,
				'show_ui' => true,
				'menu_position' => 10,
				'hierarchical' => true,
				'rewrite' => [
					'slug' => _x('photo', 'URL slug for custom post type', 'sha')
				],
				'supports' => [
					'title',
					'editor',
					'thumbnail',
					'page-attributes'
				],
				'labels' => [
					'name' => _x('Photo', 'CPT name', 'picard'),
					'singular_name' => _x('Photo', 'CPT singular name', 'picard'),
					'add_new' => _x('Add new', 'CPT add_new', 'picard'),
					'add_new_item' => _x('Add new', 'cpt name', 'picard'),
					'edit_item' => _x('Edit photo', 'cpt name', 'picard'),
					'new_item' => _x('New photo', 'cpt name', 'picard'),
					'view_item' => _x('View photo', 'cpt name', 'picard'),
					'view_items' => _x('View photos', 'cpt name', 'picard'),
					'search_items' => _x('Search photos', 'cpt name', 'picard'),
					'not_found' => _x('No photos', 'cpt name', 'picard'),
					'not_found_in_trash' => _x('No photos in the trash', 'cpt name', 'picard'),
					'all_items' => _x('All photos', 'cpt name', 'picard'),
					'archives' => _x('Archives', 'cpt name', 'picard'),
					'attributes' => _x('Attribute', 'cpt name', 'picard'),
					'name_admin_bar' => _x('Photo', 'Label for name admin bar', 'picard'),
				]
			]
		);
	}

	/**
	 * Add user capabilities
	 */
	public function addCapabilities()
	{
		$admin = get_role('administrator');
		$admin->remove_cap('edit_others_photo');
		$admin->remove_cap('edit_others_photos');
		$admin->add_cap('edit_photo');
		$admin->add_cap('delete_photo');
		$admin->add_cap('read_photo');
		$admin->add_cap('publish_photo');
		$admin->add_cap('edit_photos');
		$admin->add_cap('delete_photos');
		$admin->add_cap('read_photos');
		$admin->add_cap('publish_photos');
	}

	public function shortcodePostID()
	{
		return get_the_ID();
	}
}
