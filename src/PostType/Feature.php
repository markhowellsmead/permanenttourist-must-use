<?php

namespace PT\MustUse\PostType;

/**
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */

class Feature
{
	public function run()
	{
		add_action('init', [$this, 'registerPostType']);
		add_action('init', [$this, 'registerCustomTaxonomies'], 0);
		add_action('init', [$this, 'addCapabilities']);
		add_action('init', [$this, 'acf']);
		add_action('init', [$this, 'registerPostMeta']);
		add_action('init', [$this, 'registerBlockBindings']);
		add_filter('render_block_core/paragraph', [$this, 'hideEmptyBoundParagraph'], 10, 2);
		add_filter('manage_edit-post_columns', [$this, 'adminColumnHeaders']);
		add_action('manage_post_posts_custom_column', [$this, 'adminColumnContent'], 10, 2);
		add_filter('manage_edit-photo_columns', [$this, 'adminColumnHeaders']);
		add_action('manage_photo_posts_custom_column', [$this, 'adminColumnContent'], 10, 2);
		add_filter('get_the_archive_title', [$this, 'changeTheTitle'], 20);
	}

	/**
	 * Registers the post meta fields (core custom fields, available to block bindings)
	 * @return void
	 */
	public function registerPostMeta(): void
	{
		register_post_meta('mhm_feature', 'featured_image_description', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'description' => 'Description of the featured image (e.g. a lead photo caption)',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can('edit_posts');
			}
		]);
	}

	/**
	 * Registers the block binding source for the featured image description.
	 * The value is prefixed with "Lead photo: " when it is not empty.
	 * The editor-side counterpart is in assets/src/scripts/feature-controls.
	 * @return void
	 */
	public function registerBlockBindings(): void
	{
		if (!function_exists('register_block_bindings_source')) {
			return;
		}

		register_block_bindings_source('pt/featured-image-description', [
			'label' => _x('Featured image description', 'Block binding source label', 'pt-must-use'),
			'uses_context' => ['postId'],
			'get_value_callback' => function (array $source_args, $block_instance) {
				$post_id = (int) ($block_instance->context['postId'] ?? get_the_ID());
				$value = trim((string) get_post_meta($post_id, 'featured_image_description', true));

				if ($value === '') {
					return '';
				}

				return sprintf(
					/* translators: %s: description of the featured image */
					_x('Lead photo: %s', 'Featured image description prefix', 'pt-must-use'),
					$value
				);
			},
		]);
	}

	/**
	 * Removes the empty paragraph that a block binding leaves behind when
	 * the featured image description has no value.
	 *
	 * @param string $content
	 * @param array $block
	 * @return string
	 */
	public function hideEmptyBoundParagraph($content, $block)
	{
		$source = $block['attrs']['metadata']['bindings']['content']['source'] ?? '';

		if ($source === 'pt/featured-image-description' && trim(wp_strip_all_tags($content)) === '') {
			return '';
		}

		return $content;
	}

	/**
	 * Registers the custom post type
	 * @return void
	 */
	public function registerPostType()
	{
		register_post_type(
			'mhm_feature',
			[
				'can_export' => false,
				'capabilities'	=> [
					'read' => 'read_sht_feature',
					'edit_post' => 'edit_sht_feature',
					'read_post' => 'read_sht_feature',
					'delete_post' => 'delete_sht_feature',
					'edit_posts' => 'edit_sht_features',
					'edit_others_posts' => 'edit_others_sht_features',
					'publish_posts' => 'publish_sht_features',
					'read_private_posts' => 'read_private_sht_features',
					'delete_posts' => 'delete_sht_features',
					'delete_private_posts' => 'delete_private_sht_features',
					'delete_published_posts' => 'delete_published_sht_features',
					'delete_others_posts' => 'delete_others_sht_features',
					'edit_private_posts' => 'edit_private_sht_features',
					'edit_published_posts' => 'edit_published_sht_features',
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
					'slug' => _x('features', 'URL slug for custom post type', 'sha')
				],
				'supports' => [
					'title',
					'editor',
					'thumbnail',
					'excerpt',
					'custom-fields', // Required for post meta to be available in the REST API (and so the block editor)
					'page-attributes'
				],
				'labels' => [
					'name' => _x('Features', 'CPT name', 'picard'),
					'singular_name' => _x('Feature', 'CPT singular name', 'picard'),
					'add_new' => _x('Add new', 'CPT add_new', 'picard'),
					'add_new_item' => _x('Add new', 'cpt name', 'picard'),
					'edit_item' => _x('Edit feature', 'cpt name', 'picard'),
					'new_item' => _x('New feature', 'cpt name', 'picard'),
					'view_item' => _x('View feature', 'cpt name', 'picard'),
					'view_items' => _x('View features', 'cpt name', 'picard'),
					'search_items' => _x('Search features', 'cpt name', 'picard'),
					'not_found' => _x('No features', 'cpt name', 'picard'),
					'not_found_in_trash' => _x('No features in the trash', 'cpt name', 'picard'),
					'all_items' => _x('All features', 'cpt name', 'picard'),
					'archives' => _x('Archives', 'cpt name', 'picard'),
					'attributes' => _x('Attribute', 'cpt name', 'picard'),
					'name_admin_bar' => _x('Feature', 'Label for name admin bar', 'picard'),
				]
			]
		);
	}

	public function registerCustomTaxonomies()
	{
		register_taxonomy(
			'mhm_feature_type',
			'mhm_feature',
			[
				'labels'            => [
					'name'              => _x('Feature types', 'taxonomy general name', 'picard'),
					'singular_name'     => _x('Feature type', 'taxonomy singular name', 'picard'),
					'search_items'      => __('Search Feature types', 'picard'),
					'all_items'         => __('All Feature types', 'picard'),
					'parent_item'       => __('Parent Feature type', 'picard'),
					'parent_item_colon' => __('Parent Feature type:', 'picard'),
					'edit_item'         => __('Edit Feature type', 'picard'),
					'update_item'       => __('Update Feature type', 'picard'),
					'add_new_item'      => __('Add New Feature type', 'picard'),
					'new_item_name'     => __('New Feature type Name', 'picard'),
					'menu_name'         => __('Feature types', 'picard'),
				],
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => ['slug' => 'feature-type'],
			]
		);
	}

	/**
	 * Add user capabilities
	 */
	public function addCapabilities()
	{
		$admin = get_role('administrator');
		$admin->remove_cap('edit_others_sht_feature');
		$admin->remove_cap('edit_others_sht_features');
		$admin->add_cap('edit_sht_feature');
		$admin->add_cap('delete_sht_feature');
		$admin->add_cap('read_sht_feature');
		$admin->add_cap('publish_sht_feature');
		$admin->add_cap('edit_sht_features');
		$admin->add_cap('delete_sht_features');
		$admin->add_cap('read_sht_features');
		$admin->add_cap('publish_sht_features');
	}

	public function adminColumnContent($column_name, $post_id)
	{
		if ('mhm_feature' !== $column_name) {
			return;
		}

		$mhm_feature = get_post_meta($post_id, 'mhm_feature', true);
		echo empty($mhm_feature) ? 'None' : 'Yes';
	}

	public function adminColumnHeaders($columns)
	{
		$columns['mhm_feature'] = _x('Feature/s', 'Admin column header', 'picard');
		return $columns;
	}

	public function acf()
	{
		if (function_exists('acf_add_local_field_group')) :
			acf_add_local_field_group(array(
				'key' => 'group_5e500a24973d8',
				'title' => 'Feature',
				'fields' => array(
					array(
						'key' => 'field_5e500a2ef2a8c',
						'label' => 'Features',
						'name' => 'mhm_feature',
						'type' => 'post_object',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'mhm_feature',
						),
						'taxonomy' => '',
						'allow_null' => 0,
						'multiple' => 1,
						'return_format' => 'object',
						'ui' => 1,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'post',
						),
					),
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'photo',
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

	public function changeTheTitle($title)
	{

		if (is_post_type_archive('mhm_feature')) {
			return _x('Features', 'Archive list header', 'picard');
		}

		return $title;
	}
}
