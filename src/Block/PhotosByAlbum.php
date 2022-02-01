<?php

namespace PT\MustUse\Block;

/**
 * Image gallery with ACF field
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class PhotosByAlbum
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
				'name' => 'photos-by-album',
				'category' => 'common',
				'icon' => 'format-gallery',
				'keywords' => [
					_x('Gallery', 'Gutenberg block keyword', 'sha'),
					_x('Photos', 'Gutenberg block keyword', 'sha'),
					_x('Album', 'Gutenberg block keyword', 'sha')
				],
				'post_types' => ['post', 'page', 'mhm-viewpoint'],
				'supports' => [
					'align' => ['wide', 'full']
				],
				'title' => _x('Photo posts by album', 'Block title', 'sha'),
				'description' => __('An image gallery containing photos from a album.', 'Block description', 'sha'),
				'render_callback' => function ($block, $content = '', $is_preview = false) {
					$block['is_preview'] = $is_preview;
					$block['sht_album'] = get_field('sht_album');
					$block['sht_number_of_posts'] = get_field('sht_number_of_posts');
					get_template_part('partials/block/photos-by-album', null, $block);
				},
			]);
		}
	}

	public function registerFields()
	{
		if (function_exists('acf_add_local_field_group')) :
			acf_add_local_field_group([
				'key' => 'group_posts_by_album',
				'title' => 'Block - Posts By Album',
				'fields' => [
					[
						'key' => 'sht_album',
						'label' => 'Album',
						'name' => 'sht_album',
						'type' => 'taxonomy',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'taxonomy' => 'album',
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
							'value' => 'acf/photos-by-album',
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
