<?php

namespace PT\MustUse\Package;

/**
 * Adjustments for the Gutenberg Editor
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Gutenberg
{

	private $dir_path = '';
	private $dir_url = '';

	public function __construct()
	{
		$file = pt_must_use_get_instance()->file;
		$this->dir_path = plugin_dir_path($file);
		$this->dir_url = plugin_dir_url($file);
	}
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

		$this->enqueueBlockScript('blocks.js');
		$this->enqueueBlockScript('page-controls.js', 'scripts');
	}

	private function enqueueBlockScript($name, $folder = 'blocks')
	{
		$asset_name = str_replace('.js', '.asset.php', $name);
		$asset_key = str_replace('.js', '', $name);

		$script_path = "{$this->dir_path}assets/dist/{$folder}/{$name}";

		$script_asset_path = "{$this->dir_path}assets/dist/{$folder}/{$asset_name}";
		$script_asset = file_exists($script_asset_path) ? require($script_asset_path) : ['dependencies' => [], 'version' => filemtime($script_path)];

		wp_enqueue_script(
			"ptmu-gb-{$asset_key}",
			"{$this->dir_url}assets/dist/{$folder}/{$name}",
			$script_asset['dependencies'],
			$script_asset['version']
		);
	}
}
