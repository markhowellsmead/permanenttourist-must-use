<?php

namespace PT\MustUse\Package;

/**
 * Language stuff
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Language
{

	public function run()
	{
		add_action('init', [$this, 'loadTranslations']);
	}

	public function loadTranslations()
	{
		$path = pt_must_use_get_instance()->path;
		load_plugin_textdomain('pt-must-use', false, "{$path}/languages");
	}
}
