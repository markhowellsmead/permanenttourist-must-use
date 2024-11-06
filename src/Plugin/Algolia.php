<?php

namespace PT\MustUse\Plugin;

class Algolia
{

	public function run()
	{
		add_filter('algolia_search_params', [$this, 'modifyParams']);
	}

	public function modifyParams($params)
	{
		$params['highlightPreTag'] = '';
		$params['highlightPostTag'] = '';

		return $params;
	}
}
