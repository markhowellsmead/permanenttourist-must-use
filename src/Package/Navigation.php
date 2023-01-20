<?php

namespace PT\MustUse\Package;

use WP_REST_Response;

/**
 * Everything to do with menus and site navigation
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Navigation
{
	private $menus;

	public function __construct()
	{
		$this->menus = [
			'primary' => _x('Primary', 'Menu navigation label', 'sha'),
			'mobile' => _x('Mobile', 'Menu navigation label', 'sha'),
		];
	}

	public function run()
	{
		add_action('rest_api_init', [$this, 'endpoints']);
	}

	public function endpoints()
	{
		register_rest_route('sht', '/menus', array(
			'methods' => 'GET',
			'permission_callback' => '__return_true',
			'callback' => function () {
				if (empty($nav_menus = wp_get_nav_menus())) {
					return new WP_REST_Response($nav_menus);
				}

				$response_data = [];

				foreach (array_values($nav_menus) as $values) {
					$response_data[] = [
						'slug' => $values->slug,
						'id' => $values->term_id,
						'title' => $values->name
					];
				}

				return new WP_REST_Response($response_data);
			},
		));

		register_rest_route('sht', '/menu-positions', array(
			'methods' => 'GET',
			'permission_callback' => '__return_true',
			'callback' => function () {
				if (empty($nav_menus = get_registered_nav_menus())) {
					return new WP_REST_Response($nav_menus);
				}

				$response_data = [];

				foreach ($nav_menus as $key => $label) {
					$response_data[] = [
						'id' => $key,
						'title' => $label
					];
				}

				return new WP_REST_Response($response_data);
			},
		));
	}
}
