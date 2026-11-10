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
		add_filter('query_loop_block_query_vars', [$this, 'queryLoopIgnoreCustomSort'], 10, 2);
	}

	/**
	 * Stops the Post Types Order plugin from replacing the order chosen in a
	 * Query Loop (including Advanced Query Loop) with the manual menu_order.
	 * The editor preview uses the REST API and so already follows the block's
	 * setting; this makes the front end match it.
	 *
	 * @param array $query Query vars for the Query Loop.
	 * @param \WP_Block $block The Query block.
	 * @return array
	 */
	public function queryLoopIgnoreCustomSort($query, $block)
	{
		$orderBy = $block->context['query']['orderBy'] ?? '';

		if ($orderBy !== 'menu_order') {
			$query['ignore_custom_sort'] = true;
		}

		return $query;
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
		$this->enqueueBlockScript('blocks.js');
		$this->enqueueBlockScript('page-controls.js', 'scripts');
		$this->enqueueBlockScript('hikes-controls.js', 'scripts');
		$this->enqueueBlockScript('meow-gallery.js', 'scripts');
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
