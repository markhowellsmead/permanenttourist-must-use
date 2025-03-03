<?php

namespace PT\MustUse\Package;

class Assets
{
	public function run()
	{
		add_action('admin_enqueue_scripts', [$this, 'registerAdminAssets']);
		add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
	}

	public function registerAssets()
	{

		if (!is_user_logged_in()) {
			wp_deregister_style('dashicons');
		}

		$min = !defined('WP_DEBUG') || !WP_DEBUG;
		$dir_path = plugin_dir_path(pt_must_use_get_instance()->file);
		$dir_url = plugin_dir_url(pt_must_use_get_instance()->file);

		wp_enqueue_style('pt-must-use-style', $dir_url . 'assets/dist/styles/ui' . ($min ? '.min' : '') . '.css', [], filemtime($dir_path . 'assets/dist/styles/ui' . ($min ? '.min' : '') . '.css'));
		wp_enqueue_script('pt-must-use-video', $dir_url . 'assets/dist/scripts/video.js', ['jquery'], filemtime($dir_path . 'assets/dist/scripts/video.js'), true);
	}

	public function registerAdminAssets()
	{

		$dir_path = plugin_dir_path(pt_must_use_get_instance()->file);
		$dir_url = plugin_dir_url(pt_must_use_get_instance()->file);

		wp_enqueue_style('pt-must-use-admin-style', $dir_url . 'assets/src/styles/admin.css', [], filemtime($dir_path . 'assets/src/styles/admin.css'));
	}
}
