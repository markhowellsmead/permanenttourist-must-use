<?php

namespace PT\MustUse\PostType;


class Viewpoints
{

	public function run()
	{


		add_action('init', [$this, 'registerPostType']);
		add_action('acf/init', [$this, 'registerFields']);
	}

	public function registerPostType()
	{
		register_post_type(
			'mhm-viewpoint',
			[
				'description' => _x('Viewpoints', 'Post type description', 'mhm-viewpoint'),
				'menu_icon' => 'dashicons-admin-site-alt',
				'menu_position' => 10,
				'hierarchical' => true,
				'has_archive' => true,
				'public' => true,
				'show_in_rest' => true,
				'rest_base' => 'viewpoints',
				'rewrite' => [
					'slug' => 'viewpoints'
				],
				'supports' => [
					'title',
					'editor',
					'thumbnail',
					'page-attributes'
				],
				'labels' => [
					'name' => _x('Viewpoints', 'CPT name', 'mhm-viewpoint'),
					'singular_name' => _x('Viewpoint', 'CPT singular name', 'mhm-viewpoint'),
					'add_new' => _x('Add new', 'CPT add_new', 'mhm-viewpoint'),
					'add_new_item' => _x('Add new viewpoint', 'cpt name', 'mhm-viewpoint'),
					'edit_item' => _x('Edit viewpoint', 'cpt name', 'mhm-viewpoint'),
					'new_item' => _x('New viewpoint', 'cpt name', 'mhm-viewpoint'),
					'view_item' => _x('View viewpoint', 'cpt name', 'mhm-viewpoint'),
					'view_items' => _x('View viewpoints', 'cpt name', 'mhm-viewpoint'),
					'search_items' => _x('Search viewpoints', 'cpt name', 'mhm-viewpoint'),
					'not_found' => _x('No viewpoints', 'cpt name', 'mhm-viewpoint'),
					'not_found_in_trash' => _x('No viewpoints in the trash', 'cpt name', 'mhm-viewpoint'),
					'all_items' => _x('All viewpoints', 'cpt name', 'mhm-viewpoint'),
					'archives' => _x('Viewpoint viewpoints', 'cpt name', 'mhm-viewpoint'),
					'attributes' => _x('Attributes', 'cpt name', 'mhm-viewpoint'),
					'name_admin_bar' => _x('Viewpoint', 'Label for name admin bar', 'mhm-viewpoint'),
					'insert_into_item' => _x('Insert into viewpoint', 'Label for name admin bar', 'mhm-viewpoint'),
					'uploaded_to_this_item' => _x('Uploaded to this viewpoint', 'Label for name admin bar', 'mhm-viewpoint'),
					'filter_items_list' => _x('Filter viewpoints', 'Label for name admin bar', 'mhm-viewpoint'),
					'items_list_navigation' => _x('Viewpoint list navigation', 'Label for name admin bar', 'mhm-viewpoint'),
					'items_list' => _x('List of viewpoints', 'Label for name admin bar', 'mhm-viewpoint'),
					'item_published' => _x('Viewpoint published.', 'Label for name admin bar', 'mhm-viewpoint'),
					'item_published_privately' => _x('Viewpoint published privately.', 'Label for name admin bar', 'mhm-viewpoint'),
					'item_reverted_to_draft' => _x('Viewpoint reverted to draft status.', 'Label for name admin bar', 'mhm-viewpoint'),
					'item_scheduled' => _x('Viewpoint scheduled.', 'Label for name admin bar', 'mhm-viewpoint'),
					'item_updated' => _x('Viewpoint updated.', 'Label for name admin bar', 'mhm-viewpoint'),
					// 'featured_image' => _x('Featured image', 'Custom post type label', 'mhm-viewpoint'),
					// 'set_featured_image' => _x('Set featuried image', 'Custom post type label', 'mhm-viewpoint'),
					// 'remove_featured_image' => _x('Remove viewpoint image', 'Custom post type label', 'mhm-viewpoint'),
					// 'use_featured_image' => _x('Use as viewpoint image', 'Custom post type label', 'mhm-viewpoint'),
				]
			]
		);

		add_post_type_support('mhm-viewpoint', 'excerpt');
	}

	public function registerFields()
	{
		if (function_exists('acf_add_local_field_group')) :
			acf_add_local_field_group(array(
				'key' => 'group_5e3da9e9a4b23',
				'title' => 'Location',
				'fields' => array(
					array(
						'key' => 'location',
						'label' => 'Position on map',
						'name' => 'location',
						'type' => 'google_map',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'center_lng' => '8.22421',
						'center_lat' => '46.8131873',
						'zoom' => 10,
						'height' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'mhm-viewpoint',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'side',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
				'modified' => 1581099561,
			));

			acf_add_local_field_group(array(
				'key' => 'group_related',
				'title' => 'Attributes',
				'fields' => array(
					array(
						'key' => 'related_viewpoints',
						'label' => 'Related viewpoints',
						'name' => 'related_viewpoints',
						'type' => 'relationship',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'mhm-viewpoint',
						),
						'taxonomy' => '',
						'filters' => array(
							0 => 'search',
						),
						'elements' => array(
							0 => 'featured_image',
						),
						'min' => '',
						'max' => '',
						'return_format' => 'object',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'mhm-viewpoint',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'side',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));
		endif;
	}
}
