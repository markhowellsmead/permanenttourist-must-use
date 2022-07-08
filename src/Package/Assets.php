<?php

namespace PT\MustUse\Package;

class Assets
{
	public function run()
	{
		add_action('admin_enqueue_scripts', [$this, 'registerAdminAssets']);
	}

	public function registerAdminAssets()
	{

		$dir_path = plugin_dir_path(pt_must_use_get_instance()->file);
		$dir_url = plugin_dir_url(pt_must_use_get_instance()->file);

		wp_enqueue_style('pt-must-use-admin-style', $dir_url . 'assets/src/styles/admin.css', [], filemtime($dir_path . 'assets/src/styles/admin.css'));
	}
}
