<?php

namespace PT\MustUse\Block;

class HomeCarousel
{

	public function run()
	{
		add_action('acf/init', [$this, 'registerBlocks']);
		add_action('acf/init', [$this, 'registerFields']);
	}

	public function registerBlocks()
	{
		if (function_exists('acf_register_block_type')) {
			acf_register_block_type([
				'name' => 'mhm/home-carousel',
				'category' => 'layout',
				'icon' => 'images-alt2',
				'keywords' => [
					_x('Slider', 'Gutenberg block keyword', 'sha'),
					_x('Swiper', 'Gutenberg block keyword', 'sha'),
					_x('Carousel', 'Gutenberg block keyword', 'sha')
				],
				'post_types' => ['post', 'page'],
				'supports' => [
					'align' => ['wide', 'full']
				],
				'title' => _x('Home page carousel', 'Block title', 'sha'),
				'description' => __('An image carousel featuring selected, optionally linked images.', 'Block description', 'sha'),
				'render_callback' => function ($block, $content = '', $is_preview = false) {
					$block['entries'] = get_field('entries');
					get_template_part('partials/block/home-carousel', null, $block);
				},
			]);
		}
	}

	public function registerFields()
	{
		if (function_exists('acf_add_local_field_group')) :
			acf_add_local_field_group(array(
				'key' => 'group_5ebc66b5ac0e4',
				'title' => 'Block: Home Carousel',
				'fields' => array(
					array(
						'key' => 'field_5ebc679968ab3',
						'label' => 'Entries',
						'name' => 'entries',
						'type' => 'repeater',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'collapsed' => 'field_5ebc675ce51d2',
						'min' => 1,
						'max' => 0,
						'layout' => 'block',
						'button_label' => 'Add entry',
						'sub_fields' => array(
							array(
								'key' => 'field_5ebc675ce51d2',
								'label' => 'Image',
								'name' => 'image',
								'type' => 'image',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'return_format' => 'array',
								'preview_size' => 'medium',
								'library' => 'all',
								'min_width' => '',
								'min_height' => '',
								'min_size' => '',
								'max_width' => '',
								'max_height' => '',
								'max_size' => '',
								'mime_types' => '',
							),
							array(
								'key' => 'field_5ebc6f74007ad',
								'label' => 'Description',
								'name' => 'legend',
								'type' => 'text',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
							array(
								'key' => 'field_5ebc6763e51d3',
								'label' => 'Link',
								'name' => 'link',
								'type' => 'link_picker',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'block',
							'operator' => '==',
							'value' => 'acf/mhm-home-carousel',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
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
