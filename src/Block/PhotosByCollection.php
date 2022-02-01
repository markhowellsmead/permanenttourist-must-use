<?php

namespace PT\MustUse\Block;

/**
 * Image gallery with ACF field
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class PhotosByCollection
{

	public function run()
	{
		add_action('acf/init', [$this, 'registerBlocks']);
		add_action('acf/init', [$this, 'registerFields']);
	}

	public function registerBlocks()
	{
		if (function_exists('acf_register_block_type')) {
			// Block using ACF fields
			acf_register_block_type([
				'title' => _x('Photo posts by tag', 'Block title', 'sha'),
				'description' => __('An image gallery containing photos associated to a specific tag.', 'Block description', 'sha'),
				'name' => 'photos-by-collection',
				'category' => 'common',
				'icon' => 'format-gallery',
				'keywords' => [
					_x('Gallery', 'Gutenberg block keyword', 'sha'),
					_x('Photos', 'Gutenberg block keyword', 'sha'),
					_x('Collection', 'Gutenberg block keyword', 'sha')
				],
				'post_types' => ['post', 'page', 'mhm-viewpoint'],
				'supports' => [
					'align' => ['wide', 'full']
				],
				'render_callback' => function ($block, $content = '', $is_preview = false) {
					$block['is_preview'] = $is_preview;
					$block['sht_collection'] = get_field('sht_collection');
					$block['sht_number_of_posts'] = get_field('sht_number_of_posts');
					get_template_part('partials/block/photos-by-collection', null, $block);
				},
			]);
		}
	}

	public function registerFields()
	{
		if (function_exists('acf_add_local_field_group')) :
			acf_add_local_field_group([
				'key' => 'group_posts_by_collection',
				'title' => 'Block - Posts By Collection',
				'fields' => [
					[
						'key' => 'sht_collection',
						'label' => 'Post Tag',
						'name' => 'post_tag',
						'type' => 'taxonomy',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'taxonomy' => 'post_tag',
						'field_type' => 'multi_select',
						'allow_null' => 0,
						'add_term' => 0,
						'save_terms' => 0,
						'load_terms' => 0,
						'return_format' => 'id',
						'multiple' => 0,
					],
					[
						'key' => 'sht_number_of_posts',
						'label' => 'Number of photos',
						'name' => 'sht_number_of_posts',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'default_value' => 64,
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => 1,
						'max' => '',
						'step' => '',
					],
					[
						'key' => 'field_609a93dbee0e3',
						'label' => _x('Order by', 'ACF field label', 'sha'),
						'name' => 'sort',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'choices' => [
							'date_za' => 'Post date (latest first)',
							'date_az' => 'Post date (oldest first)',
						],
						'default_value' => 'regular',
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					],
					[
						'key' => 'sht_show_captions',
						'label' => _x('Show captions', 'ACF field label', 'sha'),
						'name' => 'sht_show_captions',
						'type' => 'true_false',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'message' => '',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => _x('Yes', 'ACF field label', 'sha'),
						'ui_off_text' => _x('No', 'ACF field label', 'sha'),
					],
				],
				'location' => [
					[
						[
							'param' => 'block',
							'operator' => '==',
							'value' => 'acf/photos-by-collection',
						],
					],
				],
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			]);
		endif;
	}
}
