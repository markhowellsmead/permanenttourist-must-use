<?php

namespace PT\MustUse\PostType;

class Hike
{

	public function run()
	{
		add_filter('get_the_archive_title', [$this, 'changeTheTitle'], 20);
		add_action('init', [$this, 'registerPostType']);
	}

	public function registerPostType(): void
	{
		register_post_type('mhm_hike', [
			'label' => __('Hikes', 'sht'),
			'menu_icon' => 'dashicons-location',
			'public' => true,
			'show_in_rest' => true,
			'rest_base' => 'hikes',
			'supports' => ['title', 'editor', 'thumbnail'],
		]);

		$admin = get_role('administrator');

		$admin->add_cap('read_mhm_hike');
		$admin->add_cap('edit_mhm_hike');
		$admin->add_cap('read_mhm_hikes');
		$admin->add_cap('delete_mhm_hike');
		$admin->add_cap('edit_mhm_hikes');
		$admin->add_cap('edit_others_mhm_hikes');
		$admin->add_cap('publish_mhm_hikes');
		$admin->add_cap('read_private_mhm_hikes');
		$admin->add_cap('delete_mhm_hikes');
		$admin->add_cap('delete_private_mhm_hikes');
		$admin->add_cap('delete_published_mhm_hikes');
		$admin->add_cap('delete_others_mhm_hikes');
		$admin->add_cap('edit_private_mhm_hikes');
		$admin->add_cap('edit_published_mhm_hikes');
	}

	public function changeTheTitle($title)
	{

		if (is_post_type_archive('mhm_hike')) {
			return _x('Hikes and walks', 'Archive list header', 'sht');
		}

		if (is_tax('mhm_hike_region')) {
			return '<span class="c-archive__titleprefix">' . _x('Hiking regions', 'Archive list header', 'sht') . '</span> ' . single_term_title('', false);
		}

		if (is_tax('mhm_hike_tag')) {
			return '<span class="c-archive__titleprefix">' . _x('Hikes featuring the subject', 'Archive list header', 'sht') . '</span> ' . single_term_title('', false);
		}

		return $title;
	}
}
