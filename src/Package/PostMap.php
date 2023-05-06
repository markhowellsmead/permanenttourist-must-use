<?php

namespace PT\MustUse\Package;

use stdClass;
use WP_Query;

class PostMap
{

	public $version = '1.0';
	public $wpversion = '4.6';
	private $settings = null;
	private $locations = array();
	private $resources_uri = '';

	public function run()
	{

		register_activation_hook(__FILE__, array($this, 'check_version'));

		add_action('admin_init', array($this, 'check_version'));

		// Load translations
		add_action('plugins_loaded', array($this, 'loadTextDomain'));
		add_action('plugins_loaded', array($this, 'initSettings'), 1);

		// Load JavaScript
		add_action('wp_enqueue_scripts', array($this, 'addScripts'));

		// Shortcode handling
		add_shortcode('postmap', array($this, 'render'));

		// Workaround to stop Relevanssi breaking queries if there is no search term passed in
		add_filter('relevanssi_prevent_default_request', array($this, 'fixRelevanssiBlocking'), 10, 2);
	}

	public function initSettings()
	{
		$this->settings = new stdClass();
		$this->settings->nonce = wp_create_nonce('mhm-postmap');
		$this->settings->posts_per_page = 1000;
		$this->settings->post_type = array('photo');

		$this->resources_uri = plugin_dir_url(__FILE__) . 'Resources/';

		apply_filters('mhm-postmap:settings', $this->settings);
	}

	public function addScripts()
	{
		wp_enqueue_script('googlemaps_ofm', plugin_dir_url(__FILE__) . 'Resources/Public/JavaScript/oms.min.js', null, $this->version, true);
	}

	public function render($atts, $content = null)
	{
		$this->getPosts();
		$this->outputMap();
	}

	private function getPosts()
	{

		// The Query
		$query = new WP_Query(array(
			'posts_per_page' => (int) $this->settings->posts_per_page,
			'post_type' => (array) $this->settings->post_type,
			'meta_query' => array(
				array(
					'key' => 'location',
					'value' => '',
					'compare' => '!=',
				),
			)
		));

		if ($query->have_posts()) {

			while ($query->have_posts()) {
				$query->the_post();
				global $post;

				$location = get_post_meta($post->ID, 'location', true);

				if (is_string($location)) {
					$location = unserialize($location);
				}

				if (is_array($location) && isset($location['lat']) && isset($location['lng'])) {
					$this->locations[] = array(
						'title' => $post->post_title,
						'link' => get_permalink($post->ID),
						'longitude' => floatval($location['lat']),
						'latitude' => floatval($location['lng']),
						'icon' => $this->resources_uri . '/Public/Icons/marker.png',
						'markerContent' => '<a href="' . get_permalink($post->ID) . '">' . get_the_post_thumbnail($post->ID, 'thumbnail') . '</a>
<div class="text">
	<h2><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></h2>
	<p class="link"><a href="' . get_permalink($post->ID) . '">' . __('View full-sized image', 'permanenttourist-must-use') . '</a></p>
</div>'
					);
				}
			}
		}
	}

	public function outputMap()
	{
		if (!empty($this->locations)) {
			echo '<script>
	var big_map_locations = ' . json_encode($this->locations) . ';
</script>
<section class="mod map map-large">
	<div class="content">
		<div class="atom googlemap jumbo" id="' . uniqid('googlemap_') . '" data-map="overview"></div>
	</div>
</section>';
		}
	}

	public function check_version()
	{
		// Check that this plugin is compatible with the current version of WordPress
		if (!$this->compatible_version()) {
			if (is_plugin_active(plugin_basename(__FILE__))) {
				deactivate_plugins(plugin_basename(__FILE__));
				add_action('admin_notices', array($this, 'disabled_notice'));
				if (isset($_GET['activate'])) {
					unset($_GET['activate']);
				}
			}
		}
	}

	public function disabled_notice()
	{
		echo '<div class="notice notice-error is-dismissible">
	<p>' . sprintf(
			__('The plugin “%1$s” requires WordPress %2$s or higher!', 'permanenttourist-must-use'),
			_x('Map of posts', 'Plugin name', 'permanenttourist-must-use'),
			$this->wpversion
		) . '</p>
</div>';
	}

	private function compatible_version()
	{
		if (version_compare($GLOBALS['wp_version'], $this->wpversion, '<')) {
			return false;
		}
		return true;
	}

	public function fixRelevanssiBlocking($kill, $query)
	{
		if (empty($query->query_vars['s'])) {
			$kill = false;
		}
		return $kill;
	}
}
