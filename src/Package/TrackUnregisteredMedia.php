<?php

namespace PT\MustUse\Package;

class TrackUnregisteredMedia
{
	public $key = '';
	private $directories = array();
	private $directory_base = '';
	private $directories_exclude = ['_publishtarget', '_publish', 'backup', 'uploads/_X_flickr'];
	private $limit = 20;

	public function run()
	{
		$this->key = basename(__DIR__);
		add_action('admin_menu', array($this, 'createAdminMenu'));
	}

	public function createAdminMenu()
	{
		// Secondary-level menu
		add_submenu_page('upload.php', __('Track unregistered media', 'mhm_trackunregisteredmedia'), __('Track unregistered media', 'mhm_trackunregisteredmedia'), 'manage_options', 'mhm_trackunregisteredmedia', array($this, 'settingsPage'));

		//call register settings function
		add_action('admin_init', array($this, 'registerPluginSettings'));
	}

	public function registerPluginSettings()
	{
		//register our settings
		register_setting('mhm_trackunregisteredmedia', 'mhm_trackunregisteredmedia');
	}

	public function settingsPage()
	{

		$this->limit = (int) get_option('posts_per_page', $this->limit);

		echo '<div class="wrap">
            <h1>' . esc_html(get_admin_page_title()) . '</h1>
            <form method="post" action="options.php">
            ';

		settings_fields('mhm_trackunregisteredmedia');

		$this->directories = wp_upload_dir();
		$this->directory_base = $this->directories['basedir'];

		if (!$this->directory_base || !is_dir($this->directory_base)) {

			echo '<p>' . sprintf(
				__('Base directory %1$s does not exist on the server.', 'mhm_trackunregisteredmedia'),
				str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->directory_base)
			) . '</p>';
		} else {

			echo '<p>' . sprintf(
				__('The following files in the uploads directory on the server are not tracked in the WordPress “<a href="%1$s">Media</a>” view. (This means that there is no Post of type “<a href="%2$s">Attachment</a>” for the relevant file.)', 'mhm_trackunregisteredmedia'),
				admin_url('upload.php'),
				'https://codex.wordpress.org/Attachments'
			) . '</p>';

			$this->getFileList();

			//submit_button('Create media entries');
		}

		echo '</form>
        </div>';
	}

	private function getFileList()
	{

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->directory_base, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::SELF_FIRST,
			\RecursiveIteratorIterator::CATCH_GET_CHILD
		);

		$iterator->setMaxDepth(6);

		$n = 0;

		foreach ($iterator as $itref) {

			if (($n < $this->limit) && $itref->isFile() && $this->isImage($itref) && !$this->isGeneratedImage($itref) && !$this->isInvisibleImage($itref) && !$this->isExcludedDirectory($itref)) {
				$file_path = $itref->getPathname();
				$file_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file_path);
				$file_url = str_replace($this->directory_base, $this->directories['baseurl'], $file_path);
				echo '<img style="max-width:400px" src="' . $file_url . '" /><br>' . $file_path . '<br><br>';
				++$n;
			}
		}
	}

	private function isImage($itref)
	{
		return (bool) preg_match("/\.(jpe?g|png|gif)$/i", $itref->getFilename());
	}

	private function isGeneratedImage($itref)
	{
		return (bool) preg_match("/-[0-9]+x[0-9]+/i", $itref->getFilename());
	}

	private function isInvisibleImage($itref)
	{
		return (bool) preg_match("/^\./i", $itref->getFilename());
	}

	private function isExcludedDirectory($itref)
	{
		$return = false;

		foreach ($this->directories_exclude as $dir) {
			if ((bool) preg_match('~' . $dir . '~i', $itref->getPathname())) {
				$return = true;
			}
		}

		return $return;
	}
}
