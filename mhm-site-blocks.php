<?php
/*
Plugin Name: Site blocks
Plugin URI: #
Description: Block registration plugin for a WordPress website.
Author: Mark Howells-Mead
Version: 0.0.1
Author URI: https://www.permanenttourist.ch/
Text Domain: mhm-site-blocks
Domain Path: languages
*/

function mhm_site_blocks_editor_assets()
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
		'mhm-site-blocks-gutenberg-script',
		"{$dir_url}assets/dist/blocks/{$file}",
		$script_asset['dependencies'],
		$script_asset['version']
	);
}

add_action('enqueue_block_editor_assets', 'mhm_site_blocks_editor_assets');
