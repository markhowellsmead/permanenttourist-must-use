<?php

namespace PT\MustUse\Package;

/**
 * Dark Mode Controls
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class DarkMode
{

	public function run()
	{
		add_action('wp_enqueue_scripts', [$this, 'scripts']);
	}

	public function scripts()
	{

		$dir_path = plugin_dir_path(pt_must_use_get_instance()->file);
		$dir_url = plugin_dir_url(pt_must_use_get_instance()->file);

		wp_enqueue_script('pt-must-use-dark-mode', $dir_url . 'assets/dist/scripts/dark-mode.js', ['jquery'], filemtime($dir_path . 'assets/dist/scripts/dark-mode.js'), true);

		// Register an empty script handle to attach the inline script.
		wp_register_script('pt-must-use-dark-mode-inline', '');
		wp_enqueue_script('pt-must-use-dark-mode-inline');

		// Inline script to set the theme based on user preference or system preference.
		$inline_script = '
		(function() {
			const body = document.documentElement;
			const isDarkMode = localStorage.getItem("darkMode") === "enabled";
			const prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;

			console.log("prefersDark",prefersDark);

			// Apply the "theme-dark" class based on user or system preference
			body.classList.toggle("sht-theme--prefer-dark", isDarkMode || (!localStorage.getItem("darkMode") && prefersDark));
		})();';

		// Ensure proper escaping
		wp_add_inline_script('pt-must-use-dark-mode-inline', $inline_script);
	}
}
