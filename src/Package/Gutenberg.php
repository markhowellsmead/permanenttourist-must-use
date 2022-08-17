<?php

namespace PT\MustUse\Package;

/**
 * Adjustments for the Gutenberg Editor
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Gutenberg
{
	public function run()
	{
		add_action('admin_menu', [$this, 'reusableBlocksAdminMenu']);
		add_action('enqueue_block_editor_assets', [$this, 'blockEditorAssets']);
	}

	public function isContextEdit()
	{
		return array_key_exists('context', $_GET) && $_GET['context'] === 'edit';
	}

	public function reusableBlocksAdminMenu()
	{
		add_submenu_page(
			'themes.php',
			_x('Wiederverwendbare Blöcke', 'Admin page title', 'sht'),
			_x('Wiederverwendbare Blöcke', 'Admin menu label', 'sht'),
			'edit_posts',
			'edit.php?post_type=wp_block'
		);
	}

	public function blockEditorAssets()
	{
		if (!function_exists('get_plugin_data')) {
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		$plugin_data = get_plugin_data(pt_must_use_get_instance()->file, false);

		$dir_path = plugin_dir_path(pt_must_use_get_instance()->file);
		$dir_url = plugin_dir_url(pt_must_use_get_instance()->file);

		$file = defined('WP_DEBUG') && WP_DEBUG ? 'blocks.js' : 'blocks.min.js';

		$script_asset_path = "{$dir_path}assets/dist/blocks/blocks.asset.php";
		$script_asset = file_exists($script_asset_path) ? require($script_asset_path) : ['dependencies' => [], 'version' => $plugin_data['Version'] ?? '0'];

		wp_enqueue_script(
			'pt-must-use-gutenberg-script',
			"{$dir_url}assets/dist/blocks/{$file}",
			$script_asset['dependencies'],
			$script_asset['version']
		);
	}
}
