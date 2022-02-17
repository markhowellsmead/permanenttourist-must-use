<?php

namespace PT\MustUse\PostType;

class Destination
{

	public function run()
	{
		add_filter('get_the_archive_title', [$this, 'changeTheTitle'], 20);
	}

	public function changeTheTitle($title)
	{

		if (is_post_type_archive('mhm_destination')) {
			return _x('Destinations', 'Archive list header', 'sht');
		}

		if (is_tax('mhm_destination_region')) {
			return '<span class="c-archive__titleprefix">' . _x('Destinations in the region', 'Archive list header', 'sht') . '</span> ' . single_term_title('', false);
		}

		if (is_tax('mhm_destination_tag')) {
			return '<span class="c-archive__titleprefix">' . _x('Destinations featuring the subject', 'Archive list header', 'sht') . '</span> ' . single_term_title('', false);
		}

		return $title;
	}
}
