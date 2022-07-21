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

				Package\Archives::class,
				Package\Assets::class,
				// Package\CLI::class, // See file for comments about errors
				Package\Gutenberg::class,
				Package\Footnotes::class,
				Package\LoginScreen::class,
				Package\Media::class,
				Package\Navigation::class,

				Pattern\HeaderMediaText::class,

				PostType\BlockArea::class,
				PostType\Destination::class,
				PostType\Feature::class,
				PostType\Page::class,
				PostType\Photo::class,
			]
		);

		add_action('init', [$this, 'registerPostMeta']);
		add_action('comment_form_before', [$this, 'enqueueReplyScript']);
		add_action('wp_head', [$this, 'noJsScript']);
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

	/**
	 * Adds a JS script to the head that removes 'no-js' from the html class list
	 */
	public function noJsScript()
	{
		echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>" . chr(10);
	}

	public function enqueueReplyScript()
	{
		if (is_singular() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
	}
}
