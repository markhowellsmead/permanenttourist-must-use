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
