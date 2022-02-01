<?php
/*
Plugin Name: Permanent Tourist Must Use
Plugin URI: #
Description: This WordPress plugin contains all of the blocks and portable functionality for permanenttourist.ch.
Author: Mark Howells-Mead
Version: 0.0.1
Author URI: https://www.permanenttourist.ch/
Text Domain: pt-must-use
Domain Path: languages
*/

function pt_must_use_blocks_editor_assets()
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

add_action('enqueue_block_editor_assets', 'pt_must_use_blocks_editor_assets');

function pt_must_use_blocks_register_post_meta()
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

add_action('init', 'pt_must_use_blocks_register_post_meta');
