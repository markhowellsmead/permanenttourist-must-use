<?php

namespace PT\MustUse;

class Plugin
{
	private static $instance;
	public $name = '';
	public $prefix = '';
	public $version = '';
	public $file = '';

	/**
	 * Creates an instance if one isn't already available,
	 * then return the current instance.
	 *
	 * @param  string $file The file from which the class is being instantiated.
	 * @return object       The class instance.
	 */
	public static function getInstance($file)
	{
		if (!isset(self::$instance) && !(self::$instance instanceof Plugin)) {
			self::$instance = new Plugin;

			if (!function_exists('get_plugin_data')) {
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			$data = get_plugin_data($file);

			self::$instance->name = $data['Name'];
			self::$instance->prefix = 'pt_must_use';
			self::$instance->version = $data['Version'];
			self::$instance->file = $file;

			self::$instance->run();
		}
		return self::$instance;
	}

	/**
	 * Loads and initializes the provided classes.
	 *
	 * @param $classes
	 */
	private function loadClasses($classes)
	{
		foreach ($classes as $class) {
			$class_parts = explode('\\', $class);
			$class_short = end($class_parts);
			$class_set   = $class_parts[count($class_parts) - 2];

			if (!isset(pt_must_use_get_instance()->{$class_set}) || !is_object(pt_must_use_get_instance()->{$class_set})) {
				pt_must_use_get_instance()->{$class_set} = new \stdClass();
			}

			if (property_exists(pt_must_use_get_instance()->{$class_set}, $class_short)) {
				wp_die(sprintf(__('A problem has ocurred in the Theme. Only one PHP class named “%1$s” may be assigned to the “%2$s” object in the Theme.', 'sht'), $class_short, $class_set), 500);
			}

			pt_must_use_get_instance()->{$class_set}->{$class_short} = new $class();

			if (method_exists(pt_must_use_get_instance()->{$class_set}->{$class_short}, 'run')) {
				pt_must_use_get_instance()->{$class_set}->{$class_short}->run();
			}
		}
	}

	/**
	 * Execution function which is called after the class has been initialized.
	 * This contains hook and filter assignments, etc.
	 */
	private function run()
	{

		$this->loadClasses(
			[
				Block\Albums::class,
				Block\BlogCards::class,
				Block\CountPosts::class,
				Block\CountPhotos::class,
				Block\HomeCarousel::class,
				Block\ImageGallery::class,
				Block\PostHeader::class,
				Block\PhotosByAlbum::class,
				Block\PhotosByCollection::class,
				Block\PhotosByViewpoint::class,
				Block\PublicationDate::class,
				Block\Subpages::class,
				Block\ViewpointAncestors::class,
				Block\ViewpointCards::class,
				Block\ViewpointDescendants::class,
				Block\YearsOnline::class,
			]
		);

		add_action('enqueue_block_editor_assets', [$this, 'blockEditorAssets']);
		add_action('init', [$this, 'registerPostMeta']);
		add_action('block_categories_all', [$this, 'blockCategories']);
	}

	public function blockEditorAssets()
	{
		if (!function_exists('get_plugin_data')) {
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		$plugin_data = get_plugin_data(__FILE__, false);

		$dir_path = plugin_dir_path(__FILE__);
		$dir_url = plugin_dir_url(__FILE__);

		$file = defined('WP_DEBUG') && WP_DEBUG ? 'blocks.js' : 'blocks.min.js';

		$script_asset_path = "{$dir_path}assets/gutenberg/blocks.asset.php";
		$script_asset = file_exists($script_asset_path) ? require($script_asset_path) : ['dependencies' => [], 'version' => $plugin_data['Version'] ?? '0'];
		wp_enqueue_script(
			'pt-must-use-gutenberg-script',
			"{$dir_url}assets/dist/blocks/{$file}",
			$script_asset['dependencies'],
			$script_asset['version']
		);
	}

	public function registerPostMeta()
	{

		$args = [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'boolean',
			'auth_callback' => function () {
				return current_user_can('edit_posts');
			}
		];

		register_post_meta('post', 'hide_title', $args);
		register_post_meta('page', 'hide_title', $args);
		register_post_meta('photo', 'hide_title', $args);
	}

	public function blockCategories(array $categories)
	{
		return array_merge($categories, [
			[
				'slug'  => 'sht-blocks',
				'title' => __('Blocks by Say Hello', 'sha'),
			],
		]);
	}
}
