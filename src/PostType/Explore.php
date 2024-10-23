<?php

namespace PT\MustUse\PostType;

class Explore
{

	public function run()
	{


		add_action('init', [$this, 'registerPostType']);
		add_action('acf/init', [$this, 'registerFields']);
	}

	public function registerPostType()
	{
		register_post_type(
			'mhm-explore',
			[
				'description' => _x('Destinations', 'Post type description', 'permanenttourist-must-use'),
				'menu_icon' => 'dashicons-admin-site-alt',
				'menu_position' => 10,
				'hierarchical' => true,
				'has_archive' => true,
				'public' => true,
				'show_in_rest' => true,
				'rest_base' => 'explore',
				'rewrite' => [
					'slug' => 'explore'
				],
				'supports' => [
					'title',
					'editor',
					'thumbnail',
					'page-attributes'
				],
				'labels' => [
					'name' => _x('Places', 'CPT name', 'permanenttourist-must-use'),
					'singular_name' => _x('Place', 'CPT singular name', 'permanenttourist-must-use'),
					'add_new' => _x('Add new', 'CPT add_new', 'permanenttourist-must-use'),
					'add_new_item' => _x('Add new Place', 'cpt name', 'permanenttourist-must-use'),
					'edit_item' => _x('Edit Place', 'cpt name', 'permanenttourist-must-use'),
					'new_item' => _x('New Place', 'cpt name', 'permanenttourist-must-use'),
					'view_item' => _x('View Place', 'cpt name', 'permanenttourist-must-use'),
					'view_items' => _x('View Places', 'cpt name', 'permanenttourist-must-use'),
					'search_items' => _x('Search Places', 'cpt name', 'permanenttourist-must-use'),
					'not_found' => _x('No Places', 'cpt name', 'permanenttourist-must-use'),
					'not_found_in_trash' => _x('No Places in the trash', 'cpt name', 'permanenttourist-must-use'),
					'all_items' => _x('All Places', 'cpt name', 'permanenttourist-must-use'),
					'archives' => _x('Place Places', 'cpt name', 'permanenttourist-must-use'),
					'attributes' => _x('Attributes', 'cpt name', 'permanenttourist-must-use'),
					'name_admin_bar' => _x('Place', 'Label for name admin bar', 'permanenttourist-must-use'),
					'insert_into_item' => _x('Insert into Place', 'Label for name admin bar', 'permanenttourist-must-use'),
					'uploaded_to_this_item' => _x('Uploaded to this Place', 'Label for name admin bar', 'permanenttourist-must-use'),
					'filter_items_list' => _x('Filter Places', 'Label for name admin bar', 'permanenttourist-must-use'),
					'items_list_navigation' => _x('Place list navigation', 'Label for name admin bar', 'permanenttourist-must-use'),
					'items_list' => _x('List of Places', 'Label for name admin bar', 'permanenttourist-must-use'),
					'item_published' => _x('Place published.', 'Label for name admin bar', 'permanenttourist-must-use'),
					'item_published_privately' => _x('Place published privately.', 'Label for name admin bar', 'permanenttourist-must-use'),
					'item_reverted_to_draft' => _x('Place reverted to draft status.', 'Label for name admin bar', 'permanenttourist-must-use'),
					'item_scheduled' => _x('Place scheduled.', 'Label for name admin bar', 'permanenttourist-must-use'),
					'item_updated' => _x('Place updated.', 'Label for name admin bar', 'permanenttourist-must-use'),
					// 'featured_image' => _x('Featured image', 'Custom post type label', 'permanenttourist-must-use'),
					// 'set_featured_image' => _x('Set featuried image', 'Custom post type label', 'permanenttourist-must-use'),
					// 'remove_featured_image' => _x('Remove Place image', 'Custom post type label', 'permanenttourist-must-use'),
					// 'use_featured_image' => _x('Use as Place image', 'Custom post type label', 'permanenttourist-must-use'),
				]
			]
		);

		add_post_type_support('mhm-explore', 'excerpt');
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
							'value' => 'mhm-explore',
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
						'key' => 'related_places',
						'label' => 'Related places',
						'name' => 'related_places',
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
							0 => 'mhm-explore',
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
							'value' => 'mhm-explore',
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
